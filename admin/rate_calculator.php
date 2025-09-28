<?php
include('header.php');
include('sidebar.php');
include('navbar.php');
include('../connect.php');

$calculated_rate = '';
$error_message = '';
$quantity_input = '';
$selected_unit = '';
$route = null;
$available_units = [];
$save_message = '';

// Handle route selection / calculation
if(isset($_POST['route_id'])){
    $route_id = intval($_POST['route_id']);
    $quantity_input = isset($_POST['quantity']) ? floatval($_POST['quantity']) : 0;
    $selected_unit = isset($_POST['unit']) ? $_POST['unit'] : '';

    // Fetch route details
    $routeStmt = $conn->prepare("
        SELECT r.route_id, r.provider_id, r.carrier_type, r.distance_km,
               o.point_name AS origin_name, d.point_name AS destination_name,
               p.company_name AS provider_name
        FROM routes r
        JOIN network_points o ON r.origin_id = o.point_id
        JOIN network_points d ON r.destination_id = d.point_id
        JOIN active_service_provider p ON r.provider_id = p.provider_id
        WHERE r.route_id = ? AND r.status != 'completed'
    ");
    $routeStmt->bind_param("i", $route_id);
    $routeStmt->execute();
    $result = $routeStmt->get_result();
    $route = $result->fetch_assoc();
    $routeStmt->close();

    if(!$route){
        $error_message = "Route not found or has been completed.";
    } else {
        $provider_id = $route['provider_id'];
        $provider_name = $route['provider_name'];
        $route_distance = floatval($route['distance_km']);
        $carrier_type = strtolower($route['carrier_type']); // FIX: lowercase for DB match

        // Fetch available units for this provider/mode
        $unitQuery = $conn->prepare("
            SELECT DISTINCT unit 
            FROM freight_rates 
            WHERE provider_id=? AND LOWER(mode)=? AND status='Accepted'
        ");
        $unitQuery->bind_param("is", $provider_id, $carrier_type);
        $unitQuery->execute();
        $unitResult = $unitQuery->get_result();
        while($u = $unitResult->fetch_assoc()){
            $available_units[] = $u['unit'];
        }
        $unitQuery->close();

        // Calculate rate
        if(!empty($selected_unit)){
            $rateStmt = $conn->prepare("
                SELECT rate, unit, distance_range, weight_range
                FROM freight_rates
                WHERE provider_id=? AND LOWER(mode)=? AND unit=? AND status='Accepted'
            ");
            $rateStmt->bind_param("iss", $provider_id, $carrier_type, $selected_unit);
            $rateStmt->execute();
            $rates = $rateStmt->get_result();

            $found_rate = false;
            while($rate = $rates->fetch_assoc()){
                $match_distance = false;
                $match_quantity = true; // default

                // --- Distance check ---
                $dist_range = strtolower($rate['distance_range']);
                $dist_range = str_replace([' ', 'km'], '', $dist_range);
                if(preg_match('/(\d+)-(\d+)/', $dist_range, $dm)){
                    $dmin = floatval($dm[1]);
                    $dmax = floatval($dm[2]);
                    if($route_distance >= $dmin && $route_distance <= $dmax){
                        $match_distance = true;
                    }
                }

                // --- Quantity check ---
                if($rate['unit'] == 'per kg'){
                    $match_quantity = false;
                    if($quantity_input > 0 && !empty($rate['weight_range'])){
                        $weight_range = strtolower($rate['weight_range']);
                        $weight_range = str_replace([' ', 'kg'], '', $weight_range);
                        if(preg_match('/(\d+)-(\d+)/', $weight_range, $wm)){
                            $wmin = floatval($wm[1]);
                            $wmax = floatval($wm[2]);
                            if($quantity_input >= $wmin && $quantity_input <= $wmax){
                                $match_quantity = true;
                            }
                        }
                    }
                } else {
                    $match_quantity = true; // per km, per cbm, per container
                }

                if($match_distance && $match_quantity){
                    switch($rate['unit']){
                        case 'per km':
                            $total = $rate['rate'] * $route_distance;
                            break;
                        case 'per kg':
                        case 'per cbm':
                        case 'per container':
                            $total = $rate['rate'] * $quantity_input;
                            break;
                        default:
                            $total = $rate['rate'];
                    }

                    $calculated_rate = "₱ " . number_format($total,2) . 
                        " (Provider: {$provider_name}, Mode: {$carrier_type}, Unit: {$rate['unit']})";
                    $found_rate = true;
                    break;
                }
            }
            $rateStmt->close();

            if(!$found_rate){
                $error_message = "No rate found for this route and provider for the selected unit/quantity.";
            }
        }
    }
}

// Save calculated rate
if(isset($_POST['save_rate']) && !empty($calculated_rate) && $route){
    $checkStmt = $conn->prepare("SELECT * FROM calculated_rates WHERE route_id=? AND unit=? AND quantity=?");
    $checkStmt->bind_param("isi", $route['route_id'], $selected_unit, $quantity_input);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkStmt->close();

    if($checkResult->num_rows == 0){
        $saveStmt = $conn->prepare("
            INSERT INTO calculated_rates (route_id, provider_id, carrier_type, unit, quantity, total_rate)
            VALUES (?,?,?,?,?,?)
        ");
        $total_amount = $total;
        $carrier_uc = ucfirst($route['carrier_type']);
        $saveStmt->bind_param(
            "iissid",
            $route['route_id'],
            $route['provider_id'],
            $carrier_uc,
            $selected_unit,
            $quantity_input,
            $total_amount
        );
        if($saveStmt->execute()){
            $save_message = "Rate saved successfully!";
        } else {
            $error_message = "Failed to save rate.";
        }
        $saveStmt->close();
    } else {
        $error_message = "This route has already been calculated and saved.";
    }
}
?>



<div class="content p-4">
    <div class="container-fluid">
        <div class="card shadow-lg border-0 rounded-4 p-5 text-white">
            <?php if(!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(!empty($save_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $save_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

           

            <form method="POST" id="rateForm">
                <h3 style="background:#fff; color:#1f2937; padding:12px 16px; border-radius:8px;">RATE CALCULATOR</h3>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Select Route</label>
                    <select name="route_id" class="form-select" required onchange="document.getElementById('rateForm').submit()">
                        <option value="">-- Select Route --</option>
                        <?php
                        $routeQuery = $conn->query("
                            SELECT r.route_id, r.distance_km, 
                                   o.point_name AS origin_name, d.point_name AS destination_name,
                                   p.company_name AS provider_name
                            FROM routes r
                            JOIN network_points o ON r.origin_id = o.point_id
                            JOIN network_points d ON r.destination_id = d.point_id
                            JOIN active_service_provider p ON r.provider_id = p.provider_id
                            WHERE r.status != 'completed'
                            ORDER BY r.route_id ASC
                        ");
                        while($r = $routeQuery->fetch_assoc()){
                            $route_name = "From {$r['origin_name']} → To {$r['destination_name']} (Provider: {$r['provider_name']}, {$r['distance_km']} km)";
                            $selected = (isset($_POST['route_id']) && $_POST['route_id']==$r['route_id']) ? 'selected':'' ;
                            echo "<option value='{$r['route_id']}' $selected>{$route_name}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Select Unit</label>
                    <select name="unit" class="form-select" id="unitSelect" onchange="showQuantityInput()" required>
                        <option value="">-- Select Unit --</option>
                        <?php foreach($available_units as $unit): ?>
                            <option value="<?= $unit ?>" <?= ($unit==$selected_unit)?'selected':'' ?>><?= ucfirst($unit) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" id="quantityLabel">Quantity</label>
                    <input type="number" step="0.01" name="quantity" class="form-control form-control-lg" value="<?= $quantity_input ?>" placeholder="Enter quantity">
                </div>

                <div class="d-flex gap-3 mb-4 justify-content-center flex-wrap">
                    <button type="submit" name="calculate_rate" class="btn btn-primary btn-lg rounded-3">Calculate Rate</button>
                    <?php if(!empty($calculated_rate)): ?>
                        <button type="submit" name="save_rate" class="btn btn-success btn-lg rounded-3">Save Rate</button>
                    <?php endif; ?>
                </div>

                <?php if(!empty($calculated_rate)): ?>
                    <div class="text-center mt-4">
                        <div class="fs-2 fw-bold text-white bg-success rounded-3 p-3 d-inline-block">
                            <?= $calculated_rate ?>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
function showQuantityInput() {
    const unit = document.getElementById('unitSelect').value;
    const div = document.getElementById('quantityLabel').parentElement;

    if(unit === 'per kg') {
        document.getElementById('quantityLabel').innerText = 'Weight (kg)';
        div.style.display = 'block';
    } else if(unit === 'per cbm') {
        document.getElementById('quantityLabel').innerText = 'Volume (cbm)';
        div.style.display = 'block';
    } else if(unit === 'per container') {
        document.getElementById('quantityLabel').innerText = 'Number of containers';
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}
window.addEventListener('load', showQuantityInput);
</script>

<?php include('footer.php'); ?>
