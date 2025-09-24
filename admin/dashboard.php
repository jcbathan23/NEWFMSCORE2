<?php
require '../connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Access control: ensure only logged-in admin users can view this page
require_once __DIR__ . '/auth.php';

// Get current year and last 6 months
$currentYear = date('Y');
$months = [];
$monthLabels = [];
for($i=5; $i>=0; $i--){
    $months[] = date('m', strtotime("-$i month"));
    $monthLabels[] = date('M', strtotime("-$i month"));
}

// Initialize arrays for charts
$spPendingData = $spActiveData = $spInactiveData = [];
$tariffsData = $sopData = [];

// Loop through months to get counts
foreach($months as $month){
    $spPending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pending_service_provider WHERE MONTH(date_submitted)='$month' AND YEAR(date_submitted)='$currentYear'"))['total'];
    $spActive = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM active_service_provider WHERE status='Active' AND MONTH(date_approved)='$month' AND YEAR(date_approved)='$currentYear'"))['total'];
    $spInactive = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM active_service_provider WHERE status='Inactive' AND MONTH(date_approved)='$month' AND YEAR(date_approved)='$currentYear'"))['total'];
    
    $spPendingData[] = $spPending;
    $spActiveData[] = $spActive;
    $spInactiveData[] = $spInactive;

    $tariffsData[] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM freight_rates WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$currentYear'"))['total'];
    $sopData[] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM sop_documents WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$currentYear'"))['total'];
}

// Totals for cards
$totalPending = array_sum($spPendingData);
$totalActiveSP = array_sum($spActiveData);
$totalInactiveSP = array_sum($spInactiveData);
$totalAdmin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(email) AS total FROM admin_list"))['total'];
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(email) AS total FROM newaccounts"))['total'];
$totalRoutes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(route_id) AS total FROM routes"))['total'];
$totalPoints = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(point_id) AS total FROM network_points WHERE status='Active'"))['total'];
$totalSchedules = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(schedule_id) AS total FROM schedules WHERE status='scheduled'"))['total'];
$totalTariffs = array_sum($tariffsData);
$totalSOP = array_sum($sopData);

include('header.php');
include('sidebar.php');
include('navbar.php');
?>

<style>
  body{
    background-color:rgb(235, 235, 235);
  }
 /* hooks: most styles live in styles1.css */
 .tile-icon { font-size: 28px; }
 .metric-small { font-size: 12px; color: #6b7280; }
 .section-heading { font-weight: 800; letter-spacing: .5px; }
 .mini-progress { height: 6px; border-radius: 6px; background: #e5e7eb; }
 .mini-progress > div { height: 100%; border-radius: 6px; }
</style>

<div class="content p-4">

  <!-- Info Cards -->
  <div class="row g-3 mb-4">
    <!-- Weather & Environment -->
    <div class="col-xl-4 col-lg-6">
      <div class="info-card shadow-soft rounded-4 p-3">
        <div class="d-flex align-items-center mb-2 fw-semibold"><i class="fa-solid fa-cloud-sun me-2 text-info"></i> Weather & Environment</div>
        <div class="text-center">
          <div class="display-6 fw-bold text-primary">28°C</div>
          <div class="small text-muted">Manila, Philippines • Partly Cloudy</div>
        </div>
        <div class="row mt-3 text-center">
          <div class="col-6">
            <div class="text-info fw-semibold">75%</div>
            <div class="metric-small">Humidity</div>
          </div>
          <div class="col-6">
            <div class="text-info fw-semibold">15 km/h</div>
            <div class="metric-small">Wind Speed</div>
          </div>
        </div>
        <div class="mt-3">
          <div class="metric-small mb-1">Air Quality: Good</div>
          <div class="mini-progress"><div style="width: 70%; background:#22c55e"></div></div>
        </div>
      </div>
    </div>

    <!-- Today's Dashboard -->
    <div class="col-xl-4 col-lg-6">
      <div class="info-card shadow-soft rounded-4 p-3">
        <div class="d-flex align-items-center mb-2 fw-semibold"><i class="fa-regular fa-calendar me-2 text-success"></i> Today’s Dashboard</div>
        <div class="text-center">
          <div class="display-6 fw-bold text-success"><?= date('d') ?></div>
          <div class="small text-muted"><?= date('l, F Y') ?></div>
          <div class="metric-small mt-1">Administrative Overview</div>
        </div>
        <div class="row mt-3 text-center">
          <div class="col-4"><div class="text-success fw-semibold">5</div><div class="metric-small">Active Sessions</div></div>
          <div class="col-4"><div class="text-warning fw-semibold">2</div><div class="metric-small">Pending Tasks</div></div>
          <div class="col-4">
            <div class="mini-progress"><div style="width: 85%; background:#22c55e"></div></div>
            <div class="metric-small mt-1">System Status: Operational</div>
          </div>
        </div>
      </div>
    </div>

    <!-- System Overview -->
    <div class="col-xl-4 col-lg-12">
      <div class="info-card shadow-soft rounded-4 p-3">
        <div class="d-flex align-items-center mb-2 fw-semibold"><i class="fa-solid fa-chart-line me-2 text-primary"></i> System Overview</div>
        <div class="text-center">
          <div class="display-6 fw-bold text-primary">2</div>
          <div class="small text-muted">Total Items Managed across 6 modules</div>
        </div>
        <div class="mt-3">
          <div class="metric-small mb-1">System Health: 75%</div>
          <div class="mini-progress"><div style="width: 75%; background:#3b82f6"></div></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Module Summary Header -->
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="section-heading d-flex align-items-center gap-2"><i class="fa-solid fa-border-all"></i> Module Summary</div>
    <button class="btn btn-sm btn-outline-primary rounded-3"><i class="fa-solid fa-rotate"></i> Refresh Data</button>
  </div>

  <!-- Module Summary Tiles -->
  <div class="row g-3 mb-4">
    <?php
      $summaryTiles = [
        ['icon'=>'fa-building','title'=>'Service Providers','total'=>$totalActiveSP + $totalInactiveSP,'primary'=>$totalActiveSP,'secondary'=>$totalInactiveSP,'pcolor'=>'text-primary','scolor'=>'text-muted'],
        ['icon'=>'fa-route','title'=>'Routes','total'=>$totalRoutes,'primary'=>0,'secondary'=>0,'pcolor'=>'text-success','scolor'=>'text-muted'],
        ['icon'=>'fa-calendar-days','title'=>'Schedules','total'=>$totalSchedules,'primary'=>0,'secondary'=>0,'pcolor'=>'text-warning','scolor'=>'text-muted'],
        ['icon'=>'fa-location-dot','title'=>'Service Points','total'=>$totalPoints,'primary'=>0,'secondary'=>0,'pcolor'=>'text-success','scolor'=>'text-muted'],
        ['icon'=>'fa-file-lines','title'=>'SOPs','total'=>$totalSOP,'primary'=>0,'secondary'=>0,'pcolor'=>'text-primary','scolor'=>'text-muted'],
        ['icon'=>'fa-money-bill-wave','title'=>'Tariffs','total'=>$totalTariffs,'primary'=>0,'secondary'=>0,'pcolor'=>'text-danger','scolor'=>'text-muted'],
      ];
      foreach($summaryTiles as $t):
    ?>
      <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6">
        <div class="summary-tile shadow-soft rounded-4 p-3 h-100">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fa-solid <?= $t['icon'] ?> tile-icon"></i>
            <span class="fw-semibold"><?= $t['title'] ?></span>
          </div>
          <div class="display-6 fw-bold text-dark"><?= $t['total'] ?></div>
          <div class="d-flex justify-content-between mt-2">
            <div><div class="<?= $t['pcolor'] ?> fw-semibold small"><?= $t['primary'] ?></div><div class="metric-small">Active</div></div>
            <div class="text-end"><div class="<?= $t['scolor'] ?> fw-semibold small"><?= $t['secondary'] ?></div><div class="metric-small">Recent</div></div>
          </div>
          <div class="metric-small mt-2 text-muted">0% vs last month</div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Charts Section -->
  <div class="row g-3 mb-4">
      <div class="col-xl-4 col-lg-6 col-md-12">
          <div class="chart-card">
              <h6>Service Providers (Last 6 Months)</h6>
              <div class="canvas-wrapper">
                  <canvas id="providerChart"></canvas>
              </div>
          </div>
      </div>
      <div class="col-xl-4 col-lg-12 col-md-12">
          <div class="chart-card">
              <h6>Routes & Points</h6>
              <div class="canvas-wrapper">
                  <canvas id="routesPointsChart"></canvas>
              </div>
          </div>
      </div>
      <div class="col-xl-4 col-lg-6 col-md-12">
          <div class="chart-card">
              <h6>Tariffs & SOPs (Last 6 Months)</h6>
              <div class="canvas-wrapper">
                  <canvas id="tariffSOPChart"></canvas>
              </div>
          </div>
      </div>
  </div>

  <div class="row g-3 mb-4">
      <div class="col-12">
          <div class="chart-card">
              <h6>Dashboard Overview (Totals)</h6>
              <div class="canvas-wrapper">
                  <canvas id="overviewLineChart"></canvas>
              </div>
          </div>
      </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
const monthLabels = <?= json_encode($monthLabels) ?>;
const spPendingData = <?= json_encode($spPendingData) ?>;
const spActiveData = <?= json_encode($spActiveData) ?>;
const spInactiveData = <?= json_encode($spInactiveData) ?>;
const tariffsData = <?= json_encode($tariffsData) ?>;
const sopData = <?= json_encode($sopData) ?>;
const totalRoutes = <?= $totalRoutes ?>;
const totalPoints = <?= $totalPoints ?>;
const totalPending = <?= $totalPending ?>;
const totalActiveSP = <?= $totalActiveSP ?>;
const totalInactiveSP = <?= $totalInactiveSP ?>;
const totalAdmin = <?= $totalAdmin ?>;
const totalUsers = <?= $totalUsers ?>;
const totalTariffs = <?= $totalTariffs ?>;
const totalSOP = <?= $totalSOP ?>;
const totalSchedules = <?= $totalSchedules ?>;

// Service Providers Stacked Area Chart
new Chart(document.getElementById('providerChart'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [
            { label: 'Pending', data: spPendingData, borderColor: '#ffc107', backgroundColor:'rgba(255,193,7,0.4)', fill:true, tension:0.4 },
            { label: 'Active', data: spActiveData, borderColor: '#28a745', backgroundColor:'rgba(40,167,69,0.4)', fill:true, tension:0.4 },
            { label: 'Inactive', data: spInactiveData, borderColor: '#dc3545', backgroundColor:'rgba(220,53,69,0.4)', fill:true, tension:0.4 }
        ]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
});

// Tariffs & SOPs Line Chart
new Chart(document.getElementById('tariffSOPChart'), {
    type: 'line',
    data: { labels: monthLabels, datasets: [
        { label:'Tariffs', data:tariffsData, borderColor:'#fd7e14', fill:false, tension:0.4 },
        { label:'SOPs', data:sopData, borderColor:'#20c997', fill:false, tension:0.4 }
    ]},
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
});

// Routes & Points & Schedules Bar Chart
new Chart(document.getElementById('routesPointsChart'), {
    type:'bar',
    data:{
        labels:['Routes','Points','Schedules'],
        datasets:[{
            label:'Count',
            data:[totalRoutes,totalPoints,totalSchedules],
            backgroundColor:['#6f42c1','#17a2b8','#ffc107']
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        scales:{y:{beginAtZero:true}},
        plugins:{legend:{display:false}}
    }
});

// Dashboard Overview Line Chart (Totals)
new Chart(document.getElementById('overviewLineChart'), {
    type:'line',
    data:{
        labels:['Pending SP','Active SP','Inactive SP','Admins','Users','Routes','Points','Tariffs','SOPs','Schedules'],
        datasets:[{
            label:'Total Counts',
            data:[totalPending,totalActiveSP,totalInactiveSP,totalAdmin,totalUsers,totalRoutes,totalPoints,totalTariffs,totalSOP,totalSchedules],
            borderColor:'#007bff',
            backgroundColor:'rgba(0,123,255,0.3)',
            fill:true,
            tension:0.4,
            pointRadius:5,
            pointBackgroundColor:'#007bff'
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{ legend:{ position:'bottom' } },
        scales:{
            y:{ beginAtZero:true, title:{display:true,text:'Count'} },
            x:{ title:{display:true,text:'Categories'} }
        }
    }
});
</script>

<?php include('footer.php'); ?>
