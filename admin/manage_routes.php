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
            <h3 class="mb-1">Routes Management</h3>
            <p>Manage transportation routes and their details</p>
        </div>
    </div>
  <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
    <div class="alert alert-danger alert-dismissible fade show auto-fade" role="alert">
      Route Deleted Successfully!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
    <!-- Modern Routes Table -->
    <div class="modern-table-container">
        <div class="table-responsive">
            <table class="table modern-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-2"></i>ID</th>
                        <th><i class="fas fa-building me-2"></i>Provider</th>
                        <th><i class="fas fa-map-marker-alt me-2"></i>Origin</th>
                        <th><i class="fas fa-flag-checkered me-2"></i>Destination</th>
                        <th><i class="fas fa-shipping-fast me-2"></i>Mode</th>
                        <th><i class="fas fa-route me-2"></i>Distance (km)</th>
                        <th><i class="fas fa-clock me-2"></i>ETA (min)</th>
                        <th class="text-center"><i class="fas fa-tools me-2"></i>Actions</th>
                    </tr>
                </thead>
       <tbody>
           <?php
           $sql = "SELECT 
                       r.route_id,
                       sp.company_name,
                       o.point_name AS origin,
                       o.latitude AS origin_lat,
                       o.longitude AS origin_lng,
                       d.point_name AS destination,
                       d.latitude AS dest_lat,
                       d.longitude AS dest_lng,
                       r.carrier_type AS transport_mode,
                       r.distance_km,
                       r.eta_min
        FROM routes r
        JOIN active_service_provider sp ON r.provider_id = sp.provider_id
        JOIN network_points o ON r.origin_id = o.point_id
        JOIN network_points d ON r.destination_id = d.point_id
        WHERE r.status != 'completed'
        ORDER BY r.route_id DESC";
           
           $result = $conn->query($sql);
           if ($result && $result->num_rows > 0):
               while($row = $result->fetch_assoc()):
           ?>
               <tr class="modern-table-row">
                   <td><span class="fw-medium"><?= htmlspecialchars($row['route_id']) ?></span></td>
                   <td>
                       <div class="company-name">
                           <i class="fas fa-building me-2 text-muted"></i>
                           <span class="fw-medium"><?= htmlspecialchars($row['company_name']) ?></span>
                       </div>
                   </td>
                   <td>
                       <span class="modern-badge badge-user">
                           <i class="fas fa-map-marker-alt me-1"></i>
                           <?= htmlspecialchars($row['origin']) ?>
                       </span>
                   </td>
                   <td>
                       <span class="modern-badge badge-service-provider">
                           <i class="fas fa-flag-checkered me-1"></i>
                           <?= htmlspecialchars($row['destination']) ?>
                       </span>
                   </td>
                   <td>
                       <span class="modern-badge badge-pending">
                           <i class="fas fa-shipping-fast me-1"></i>
                           <?= htmlspecialchars(ucfirst($row['transport_mode'])) ?>
                       </span>
                   </td>
                   <td><strong><?= htmlspecialchars($row['distance_km']) ?> km</strong></td>
                   <td><strong><?= htmlspecialchars($row['eta_min']) ?> min</strong></td>
                   <td class="text-center">
                       <div class="action-buttons">
                           <!-- View -->
                           <a href="#" 
                              class="btn btn-modern-view viewRouteBtn"
                              data-route_id="<?= $row['route_id'] ?>"
                              data-provider="<?= htmlspecialchars($row['company_name'], ENT_QUOTES) ?>"
                              data-origin="<?= htmlspecialchars($row['origin'], ENT_QUOTES) ?>"
                              data-destination="<?= htmlspecialchars($row['destination'], ENT_QUOTES) ?>"
                              data-origin_lat="<?= $row['origin_lat'] ?>"
                              data-origin_lng="<?= $row['origin_lng'] ?>"
                              data-dest_lat="<?= $row['dest_lat'] ?>"
                              data-dest_lng="<?= $row['dest_lng'] ?>"
                              data-mode="<?= htmlspecialchars($row['transport_mode'], ENT_QUOTES) ?>"
                              data-distance="<?= htmlspecialchars($row['distance_km'], ENT_QUOTES) ?>"
                              data-eta="<?= htmlspecialchars($row['eta_min'], ENT_QUOTES) ?>"
                              title="View Route Details">
                              <i class="fas fa-eye"></i>
                           </a>

                           <!-- Delete -->
                           <a href="#" 
                              class="btn btn-modern-delete deleteRouteBtn"
                              data-id="<?= $row['route_id'] ?>"
                              title="Delete Route">
                              <i class="fas fa-trash-alt"></i>
                           </a>
                       </div>
                   </td>
               </tr>
           <?php
               endwhile;
           else:
           ?>
               <tr>
                   <td colspan="8" class="text-center">
                       <div class="empty-state">
                           <i class="fas fa-route"></i>
                           <h5>No Routes Found</h5>
                           <p>There are no active routes in the system at this time.</p>
                       </div>
                   </td>
               </tr>
           <?php endif; ?>
               </tbody>
            </table>
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

   <!-- Delete Confirmation Modal -->
   <div class="modal fade" id="deleteRouteModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header bg-danger text-white">
               <h5 class="modal-title">Confirm Delete</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
               <p>Are you sure you want to delete this route?</p>
            </div>
            <div class="modal-footer">
               <form method="GET" action="delete_route.php">
                  <input type="hidden" name="table" value="routes">
                  <input type="hidden" name="id" id="deleteRouteId">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger">Delete</button>
               </form>
            </div>
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
   // View Route Button
   document.querySelectorAll('.viewRouteBtn').forEach(btn => {
      btn.addEventListener('click', function(e) {
         e.preventDefault();

         // Set modal info
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

            // Add route
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

               L.marker([originLat, originLng]).addTo(mapRoute).bindPopup("Origin").openPopup();
               L.marker([destLat, destLng]).addTo(mapRoute).bindPopup("Destination");

            } else {
               const color = (mode === 'air') ? 'blue' : 'green';
               routeLayer = L.polyline([[originLat, originLng],[destLat,destLng]], {color: color, dashArray:'10,10', weight:4}).addTo(mapRoute);
               L.marker([originLat, originLng]).addTo(mapRoute).bindPopup("Origin").openPopup();
               L.marker([destLat, destLng]).addTo(mapRoute).bindPopup("Destination");
               mapRoute.fitBounds(routeLayer.getBounds(), {padding:[50,50]});
            }
         }, 200);
      });
   });

   // Delete Button
   document.querySelectorAll('.deleteRouteBtn').forEach(btn => {
      btn.addEventListener('click', function(e) {
         e.preventDefault();
         const id = btn.dataset.id;
         document.getElementById('deleteRouteId').value = id;

         const modalEl = document.getElementById('deleteRouteModal');
         const modal = new bootstrap.Modal(modalEl);
         modal.show();
      });
   });
});
</script>
