<?php
include('header.php');
include('sidebar.php');
include('navbar.php');
include('../connect.php');
?>

<link rel="stylesheet" href="modern-table-styles.css">

<style>
  .content h3.mb-4 {
    background: transparent !important;
    color: inherit !important;
  }
</style>

<div class="content p-4">
    <!-- Header Section -->
    <div class="modern-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Scheduled Shipments</h3>
            <p>View and manage confirmed shipment timetables</p>
        </div>
    </div>

    <!-- Modern Scheduled Shipments Table -->
    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-2"></i>ID</th>
                        <th><i class="fas fa-route me-2"></i>Route</th>
                        <th><i class="fas fa-building me-2"></i>Provider</th>
                        <th><i class="fas fa-file-alt me-2"></i>SOP</th>
                        <th><i class="fas fa-calendar me-2"></i>Date</th>
                        <th><i class="fas fa-clock me-2"></i>Time</th>
                        <th><i class="fas fa-calendar-plus me-2"></i>Created At</th>
                        <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                        <th class="text-center"><i class="fas fa-tools me-2"></i>Actions</th>
                    </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT s.schedule_id, s.route_id, s.provider_id, s.sop_id, s.schedule_date, s.schedule_time, s.created_at, s.status,
                           sp.company_name, sop.title AS sop_title,
                           r.origin_id, r.destination_id, r.carrier_type AS transport_mode, r.distance_km, r.eta_min,
                           o.point_name AS origin_name, o.latitude AS origin_lat, o.longitude AS origin_lng,
                           d.point_name AS destination_name, d.latitude AS dest_lat, d.longitude AS dest_lng
                    FROM schedules s
                    LEFT JOIN active_service_provider sp ON s.provider_id = sp.provider_id
                    JOIN sop_documents sop ON s.sop_id = sop.sop_id
                    JOIN routes r ON s.route_id = r.route_id
                    JOIN network_points o ON r.origin_id = o.point_id
                    JOIN network_points d ON r.destination_id = d.point_id
                    ORDER BY s.schedule_id DESC";

            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0):
                while($row = $result->fetch_assoc()):
                    $status = strtolower($row['status'] ?? 'pending');
                    $badgeClass = match($status) {
                        'pending' => 'bg-warning text-dark',
                        'completed' => 'bg-success text-white',
                        'cancelled' => 'bg-danger text-white',
                        default => 'bg-secondary text-white',
                    };
            ?>
            <tr>
                <td><?= htmlspecialchars($row['schedule_id']) ?></td>
                <td><?= htmlspecialchars($row['route_id']) ?></td>
                <td><?= htmlspecialchars($row['company_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['sop_title']) ?></td>
                <td><?= htmlspecialchars($row['schedule_date']) ?></td>
                <td><?= htmlspecialchars($row['schedule_time']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($row['status'] ?? 'Pending') ?></span></td>
                <td>
                    <!-- View Schedule Button -->
                    <button class="btn btn-info btn-sm viewScheduleBtn"
                        data-schedule_id="<?= $row['schedule_id'] ?>"
                        data-route_id="<?= $row['route_id'] ?>"
                        data-provider="<?= htmlspecialchars($row['company_name'] ?? 'N/A') ?>"
                        data-sop="<?= htmlspecialchars($row['sop_title']) ?>"
                        data-date="<?= $row['schedule_date'] ?>"
                        data-time="<?= $row['schedule_time'] ?>">
                        <i class="fas fa-eye"></i>
                    </button>

                    <!-- View Route Button -->
                    <button class="btn btn-primary btn-sm viewRouteBtn"
                        data-route_id="<?= $row['route_id'] ?>"
                        data-provider="<?= htmlspecialchars($row['company_name'] ?? 'N/A') ?>"
                        data-origin="<?= htmlspecialchars($row['origin_name'], ENT_QUOTES) ?>"
                        data-destination="<?= htmlspecialchars($row['destination_name'], ENT_QUOTES) ?>"
                        data-origin_lat="<?= $row['origin_lat'] ?>"
                        data-origin_lng="<?= $row['origin_lng'] ?>"
                        data-dest_lat="<?= $row['dest_lat'] ?>"
                        data-dest_lng="<?= $row['dest_lng'] ?>"
                        data-mode="<?= htmlspecialchars($row['transport_mode'], ENT_QUOTES) ?>"
                        data-distance="<?= $row['distance_km'] ?>"
                        data-eta="<?= $row['eta_min'] ?>">
                        <i class="fas fa-route"></i>
                    </button>

                  <!-- Cancel Button (opens modal) -->
<button type="button" class="btn btn-danger btn-sm cancelBtn"
    data-id="<?= $row['schedule_id'] ?>"
    data-bs-toggle="modal" data-bs-target="#cancelScheduleModal"
    title="Cancel Schedule">
    <i class="fas fa-times"></i>
</button>

                    
                </td>
            </tr>
            <?php
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="9" class="text-center">No scheduled shipments found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- View Schedule Modal -->
<div class="modal fade" id="viewScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Schedule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Schedule ID:</strong> <span id="modalScheduleId"></span></p>
                <p><strong>Route ID:</strong> <span id="modalRouteId"></span></p>
                <p><strong>Provider:</strong> <span id="modalProvider"></span></p>
                <p><strong>SOP:</strong> <span id="modalSOP"></span></p>
                <p><strong>Date:</strong> <span id="modalDate"></span></p>
                <p><strong>Time:</strong> <span id="modalTime"></span></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View Route Modal -->
<div class="modal fade" id="viewRouteModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Route Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Route ID:</strong> <span id="route_id"></span></p>
                        <p><strong>Provider:</strong> <span id="provider"></span></p>
                        <p><strong>Origin:</strong> <span id="origin"></span></p>
                        <p><strong>Destination:</strong> <span id="destination"></span></p>
                        <p><strong>Mode:</strong> <span id="mode"></span></p>
                        <p><strong>Distance (km):</strong> <span id="distance"></span></p>
                        <p><strong>ETA (min):</strong> <span id="eta"></span></p>
                    </div>
                    <div class="col-md-8">
                        <div id="routeMap" style="height:500px; border:1px solid #ccc;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Cancel Modal -->
<div class="modal fade" id="cancelScheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Cancel Schedule</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Are you sure you want to cancel schedule <strong>#<span id="cancelScheduleId"></span></strong>?</p>
      </div>

      <div class="modal-footer">
        <form method="POST" action="cancel_schedule.php">
          <input type="hidden" name="schedule_id" id="cancelScheduleInput">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">No, Keep It</button>
          <button type="submit" class="btn btn-danger">Yes, Cancel</button>
        </form>
      </div>

    </div>
  </div>
</div>

<?php include('footer.php'); ?>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Routing Machine -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css"/>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.min.js"></script>

<script>
let mapRoute = null;
let routeLayer = null;
let routingControl = null;

document.addEventListener('DOMContentLoaded', function() {

    // View Schedule
    document.querySelectorAll('.viewScheduleBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalScheduleId').textContent = btn.dataset.schedule_id;
            document.getElementById('modalRouteId').textContent = btn.dataset.route_id;
            document.getElementById('modalProvider').textContent = btn.dataset.provider;
            document.getElementById('modalSOP').textContent = btn.dataset.sop;
            document.getElementById('modalDate').textContent = btn.dataset.date;
            document.getElementById('modalTime').textContent = btn.dataset.time;
            new bootstrap.Modal(document.getElementById('viewScheduleModal')).show();
        });
    });

    // View Route
    document.querySelectorAll('.viewRouteBtn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            document.getElementById('route_id').textContent = btn.dataset.route_id;
            document.getElementById('provider').textContent = btn.dataset.provider;
            document.getElementById('origin').textContent = btn.dataset.origin;
            document.getElementById('destination').textContent = btn.dataset.destination;
            document.getElementById('mode').textContent = btn.dataset.mode;
            document.getElementById('distance').textContent = btn.dataset.distance;
            document.getElementById('eta').textContent = btn.dataset.eta;

            const originLat = parseFloat(btn.dataset.origin_lat);
            const originLng = parseFloat(btn.dataset.origin_lng);
            const destLat = parseFloat(btn.dataset.dest_lat);
            const destLng = parseFloat(btn.dataset.dest_lng);
            const mode = btn.dataset.mode.toLowerCase();

            const modalEl = document.getElementById('viewRouteModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            setTimeout(() => {
                if(!mapRoute){
                    mapRoute = L.map('routeMap').setView([originLat, originLng], 6);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(mapRoute);
                }

                // Clear previous route
                if (routingControl) {
                    mapRoute.removeControl(routingControl);
                    routingControl = null;
                }
                if (routeLayer) {
                    mapRoute.removeLayer(routeLayer);
                    routeLayer = null;
                }

                // Draw route like Manage Routes
                if(mode === 'land'){
                    routingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(originLat, originLng),
                            L.latLng(destLat, destLng)
                        ],
                        routeWhileDragging: false,
                        draggableWaypoints: false,
                        addWaypoints: false,
                        createMarker: () => null,
                        showAlternatives: false,
                        lineOptions: { styles: [{ color: 'blue', weight: 4 }] }
                    }).addTo(mapRoute);
                } else {
                    const color = (mode === 'air') ? 'blue' : 'green';
                    routeLayer = L.polyline([[originLat, originLng],[destLat,destLng]], {color: color, dashArray:'10,10', weight:4}).addTo(mapRoute);
                }

                L.marker([originLat, originLng]).addTo(mapRoute).bindPopup("Origin").openPopup();
                L.marker([destLat, destLng]).addTo(mapRoute).bindPopup("Destination");
                mapRoute.fitBounds([[originLat, originLng],[destLat,destLng]], {padding:[50,50]});
            }, 200);
        });
    });

});
// Cancel Modal - pass schedule ID
document.querySelectorAll(".cancelBtn").forEach(btn => {
  btn.addEventListener("click", function () {
    const scheduleId = this.dataset.id;
    document.getElementById("cancelScheduleId").textContent = scheduleId;
    document.getElementById("cancelScheduleInput").value = scheduleId;
  });
});

</script>
