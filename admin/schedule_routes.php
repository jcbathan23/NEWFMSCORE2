<?php
include('header.php');
include('sidebar.php');
include('navbar.php');
include('../connect.php');
?>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<link rel="stylesheet" href="modern-table-styles.css">

<style>
.badge-status {
    font-size: 0.8rem;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: capitalize;
}
.badge-scheduled { background-color: #0d6efd; color: #fff; }
.badge-progress { background-color: #0dcaf0; color: #fff; }
.badge-completed { background-color: #198754; color: #fff; }
.badge-cancelled { background-color: #dc3545; color: #fff; }
.badge-pending { background-color: #ffc107; color: #212529; }
.badge-delayed { background-color: #000; color: #fff; }

#scheduleCalendar {
    max-width: 100%;
    margin: 0 auto;
    min-height: 500px;
}


  .content h3.mb-4, .content h3.mb-5 {
    background: transparent !important;
    color: inherit !important;
  }

</style>

<div class="content p-4">
    <!-- Header Section -->
    <div class="modern-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Schedule Routes</h3>
            <p>Manage calculated rates and schedule shipments</p>
        </div>
    </div>

    <!-- CALCULATED RATES TABLE -->
    <div class="mb-5">
        <h4 class="mb-4">Calculated Rates</h4>
        <!-- Modern Calculated Rates Table -->
        <div class="modern-table-container">
            <div class="table-responsive">
                <table class="table modern-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-2"></i>ID</th>
                            <th><i class="fas fa-building me-2"></i>Provider</th>
                            <th><i class="fas fa-map-marker-alt me-2"></i>Origin</th>
                    <th>Destination</th>
                    <th>Mode</th>
                    <th>Unit</th>
                    <th>Quantity</th>
                    <th>Total Rate</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT cr.id, cr.route_id, cr.provider_id, cr.carrier_type, cr.unit, cr.quantity, cr.total_rate, cr.status,
                               r.origin_id, r.destination_id,
                               o.point_name AS origin_name, d.point_name AS destination_name,
                               sp.company_name AS provider_name
                        FROM calculated_rates cr
                        JOIN routes r ON cr.route_id = r.route_id
                        JOIN network_points o ON r.origin_id = o.point_id
                        JOIN network_points d ON r.destination_id = d.point_id
                        JOIN active_service_provider sp ON cr.provider_id = sp.provider_id
                        ORDER BY cr.id DESC";

                $result = $conn->query($sql);
                if($result && $result->num_rows > 0):
                    while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['provider_name']) ?></td>
                    <td><?= htmlspecialchars($row['origin_name']) ?></td>
                    <td><?= htmlspecialchars($row['destination_name']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['carrier_type'])) ?></td>
                    <td><?= htmlspecialchars($row['unit']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                    <td>₱ <?= number_format($row['total_rate'],2) ?></td>
                    <td>
                        <?php
                        $status = strtolower($row['status']);
                        $badgeClass = match($status) {
                            'scheduled' => 'badge-scheduled',
                            'in progress' => 'badge-progress',
                            'completed' => 'badge-completed',
                            'cancelled' => 'badge-cancelled',
                            'pending' => 'badge-pending',
                            'delayed' => 'badge-delayed',
                            default => 'badge-pending',
                        };
                        ?>
                        <span class="badge-status <?= $badgeClass ?>"><?= htmlspecialchars($row['status']) ?></span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm scheduleRateBtn"
                                data-rate_id="<?= htmlspecialchars($row['id'], ENT_QUOTES) ?>"
                                data-route_id="<?= htmlspecialchars($row['route_id'], ENT_QUOTES) ?>"
                                data-provider_id="<?= htmlspecialchars($row['provider_id'], ENT_QUOTES) ?>"
                                data-total_rate="<?= htmlspecialchars($row['total_rate'], ENT_QUOTES) ?>"
                                data-provider_name="<?= htmlspecialchars($row['provider_name'], ENT_QUOTES) ?>"
                                data-origin="<?= htmlspecialchars($row['origin_name'], ENT_QUOTES) ?>"
                                data-destination="<?= htmlspecialchars($row['destination_name'], ENT_QUOTES) ?>"
                                data-mode="<?= htmlspecialchars($row['carrier_type'], ENT_QUOTES) ?>">
                            <i class="fa fa-calendar"></i> Schedule
                        </button>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="10" class="text-center">No calculated rates found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Calendar Schedules -->
    <div class="mb-5">
        <h3 class="mb-4">CALENDAR SCHEDULES</h3>
        <div id="scheduleCalendar"></div>
    </div>
</div>

<!-- Schedule Modal -->
<div class="modal fade" id="scheduleRateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Schedule Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="scheduleForm" method="POST" action="save_schedule.php">
                <div class="modal-body">
                    <input type="hidden" name="rate_id" id="schedule_rate_id">
                    <input type="hidden" name="route_id" id="schedule_route_id">
                    <input type="hidden" name="provider_id" id="schedule_provider_id">
                    <input type="hidden" name="total_rate" id="schedule_total_rate">

                    <div class="mb-3">
                        <label class="form-label">Provider</label>
                        <input type="text" class="form-control" id="schedule_provider_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Route</label>
                        <input type="text" class="form-control" id="schedule_route" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="sop_id" class="form-label">Select SOP</label>
                        <select class="form-select" name="sop_id" id="sop_id" required>
                            <option value="">-- Select SOP --</option>
                            <?php
                            $sopQuery = $conn->query("SELECT sop_id, title FROM sop_documents ORDER BY title ASC");
                            while($sop = $sopQuery->fetch_assoc()){
                                echo '<option value="'.htmlspecialchars($sop['sop_id'], ENT_QUOTES).'">'.htmlspecialchars($sop['title']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="schedule_date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="schedule_date" id="schedule_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="schedule_time" class="form-label">Time</label>
                        <input type="time" class="form-control" name="schedule_time" id="schedule_time" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Rate</label>
                        <input type="text" class="form-control" id="display_total_rate" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Save Schedule</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="eventTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Route ID:</strong> <span id="modalRoute"></span></p>
                <p><strong>Provider:</strong> <span id="modalProvider"></span></p>
                <p><strong>SOP:</strong> <span id="modalSOP"></span></p>
                <p><strong>Total Rate:</strong> ₱ <span id="modalTotalRate"></span></p>
                <p><strong>Date & Time:</strong> <span id="modalDateTime"></span></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
// Fetch scheduled events with total_rate
$events = [];
$sql = "SELECT s.schedule_id, s.schedule_date, s.schedule_time, cr.total_rate,
               cr.route_id, cr.provider_id, sp.company_name,
               s.sop_id, sop.title AS sop_title
        FROM schedules s
        JOIN calculated_rates cr ON s.rate_id = cr.id
        JOIN active_service_provider sp ON cr.provider_id = sp.provider_id
        JOIN sop_documents sop ON s.sop_id = sop.sop_id
        ORDER BY s.schedule_id DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $time = $row['schedule_time'];
        if (strlen($time) === 5) $time .= ':00';
        $start = $row['schedule_date'] . 'T' . $time;
        $events[] = [
            'id' => $row['schedule_id'],
            'title' => $row['sop_title'] . ' (₱ '.number_format($row['total_rate'],2).')',
            'start' => $start,
            'extendedProps' => [
                'route_id' => $row['route_id'],
                'provider' => $row['company_name'],
                'sop_title' => $row['sop_title'],
                'total_rate' => number_format($row['total_rate'],2)
            ]
        ];
    }
}

$calendarEvents = json_encode($events);
?>

<?php include('footer.php'); ?>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Schedule Rate Button
    document.querySelectorAll('.scheduleRateBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('schedule_rate_id').value = btn.dataset.rate_id;
            document.getElementById('schedule_route_id').value = btn.dataset.route_id;
            document.getElementById('schedule_provider_id').value = btn.dataset.provider_id;
            document.getElementById('schedule_total_rate').value = btn.dataset.total_rate;
            document.getElementById('schedule_provider_name').value = btn.dataset.provider_name;
            document.getElementById('schedule_route').value = btn.dataset.origin + ' → ' + btn.dataset.destination;
            document.getElementById('display_total_rate').value = '₱ ' + parseFloat(btn.dataset.total_rate).toFixed(2);

            const modalEl = document.getElementById('scheduleRateModal');
            new bootstrap.Modal(modalEl).show();
        });
    });

    // FullCalendar
    const calendarEl = document.getElementById('scheduleCalendar');
    const calendarData = <?= $calendarEvents ?>;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: calendarData,
        height: 'auto',
        eventClick: function(info) {
            const props = info.event.extendedProps || {};
            document.getElementById('eventTitle').textContent = info.event.title;
            document.getElementById('modalRoute').textContent = props.route_id;
            document.getElementById('modalProvider').textContent = props.provider;
            document.getElementById('modalSOP').textContent = props.sop_title;
            document.getElementById('modalTotalRate').textContent = props.total_rate;
            document.getElementById('modalDateTime').textContent = info.event.start?.toLocaleString() || '';

            const modalEl = document.getElementById('eventDetailModal');
            new bootstrap.Modal(modalEl).show();
        }
    });

    calendar.render();
});
</script>
