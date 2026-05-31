<?php
    require_once('classes/security.php');
    bmis_session_start();
    // Block SK Chairperson from staff dashboard — redirect to their own dashboard
    $__ud = $_SESSION['userdata'] ?? [];
    if (($__ud['role'] ?? '') === 'user' && ($__ud['position'] ?? '') === 'Sk Chairperson') {
        header('Location: sk_dashboard.php');
        exit;
    }
    require('classes/staff.class.php');
    include('classes/resident.class.php');
    require_once('classes/conn.php');
    $userdetails = $bmis->get_userdata();

    $rescount = $residentbmis->count_resident();
    $rescountm = $residentbmis->count_male_resident();
    $rescountf = $residentbmis->count_female_resident();
    $rescountfh = $residentbmis->count_head_resident();
    $rescountfm = $residentbmis->count_member_resident();
    $rescountvoter = $residentbmis->count_voters();
    $rescountvo = $residentbmis->count_voters();
    $rescountpwd = $residentbmis->count_pwd();

    $staffcount = $staffbmis->count_staff();
    $staffcountm = $staffbmis->count_mstaff();
    $staffcountf = $staffbmis->count_fstaff();

    // ── Complaint counts (uses the shared $conn PDO from conn.php) ──
    $complaint_pending  = 0;
    $complaint_resolved = 0;
    $complaint_total    = 0;
    try {
        $complaint_pending  = (int)$conn->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='pending'")->fetchColumn();
        $complaint_resolved = (int)$conn->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='resolved'")->fetchColumn();
        $complaint_total    = $complaint_pending + $complaint_resolved;
    } catch (Exception $e) { /* table may not exist yet */ }
?>


<?php 
    include('dashboard_sidebar_start_staff.php');
?>
<style> 
.card-upper-space {
    margin-top: 35px;
}

.card-row-gap {
    margin-top: 3em;
}
</style>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->

  <div class="row mt-3">

    <!-- Pie Chart: Gender -->
    <div class="col-md-4 mb-3">
        <div class="card shadow" style="border-top: 3px solid var(--navy-light) !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-3">
                    Gender Distribution
                </div>
                <div style="max-width: 200px; margin: 0 auto;">
                    <canvas id="genderPieChart"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-3" style="font-size:0.78rem; font-weight:600;">
                    <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#1a4480;margin-right:5px;"></span>Male (<?= $rescountm ?>)</span>
                    <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#e8b86d;margin-right:5px;"></span>Female (<?= $rescountf ?>)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart: Resident Overview -->
    <div class="col-md-8 mb-3">
        <div class="card shadow" style="border-top: 3px solid var(--navy-light) !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-3">
                    Resident Overview
                </div>
                <div style="max-width: 200px; margin: 0 auto;">
                    <canvas id="residentDoughnutChart"></canvas>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-3" style="font-size:0.78rem; font-weight:600;">
                    <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#1a4480;margin-right:5px;"></span>PWD (<?= $rescountpwd ?>)</span>
                    <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#0d9488;margin-right:5px;"></span>Households (<?= $rescountfh ?>)</span>
                    <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#c9943a;margin-right:5px;"></span>Voters (<?= $rescountvoter ?>)</span>
                </div>
            </div>
        </div>
    </div>

</div>

    <br>
    <hr>
    <br>

    <div class="row mt-3">
    <div class="col-12">
        <h4>Barangay Data</h4>
    </div>
<br>
    <div class="col-md-4 mb-3">
        <div class="card border-left-info shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Barangay Staff List
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-dark">
                            <?= $staffcount ?>
                        </div>
                        <br>
                        <a href="staff_table_totalstaff.php">View List</a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-friends fa-2x text-dark"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-left-info shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Residents
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-dark">
                            <?= $rescount ?>
                        </div>
                        <br>
                        <a href="staff_table_totalres.php">View List</a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-friends fa-2x text-dark"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
        <!-- ══════════════════════════════════════ -->
    <!--  RESIDENT COMPLAINTS SECTION          -->
    <!-- ══════════════════════════════════════ -->
    <br>
    <hr>
    <br>

    <div class="row">
        <div class="col-12">
            <h4>Resident Complaints</h4>
        </div>
    </div>

    <div class="row mt-3">

        <!-- Total Complaints -->
        <div class="col-md-4 mb-3">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Complaints
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-dark"><?= $complaint_total ?></div>
                            <br>
                            <a href="staff_complaints.php">View All</a>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-megaphone-fill" style="font-size:2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Complaints -->
        <div class="col-md-4 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Complaints
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-dark"><?= $complaint_pending ?></div>
                            <br>
                            <a href="staff_complaints.php?status=pending" class="text-warning fw-semibold">Review Now</a>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history" style="font-size:2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolved Complaints -->
        <div class="col-md-4 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolved Complaints
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-dark"><?= $complaint_resolved ?></div>
                            <br>
                            <a href="staff_complaints.php?status=resolved" class="text-success">View Resolved</a>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle-fill" style="font-size:2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- ══ END COMPLAINTS ══ -->

<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<br>
<br>
</div>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
(function() {
    var ctx = document.getElementById('genderPieChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Male', 'Female'],
            datasets: [{
                data: [<?= (int)$rescountm ?>, <?= (int)$rescountf ?>],
                backgroundColor: ['#1a4480', '#e8b86d'],
                borderColor: ['#fff','#fff'],
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var total = ctx.dataset.data.reduce(function(a,b){return a+b;},0);
                            var pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                            return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    // ── Click a slice to navigate ──
    document.getElementById('genderPieChart').addEventListener('click', function(e) {
        var points = chart.getElementsAtEventForMode(e, 'nearest', { intersect: true }, true);
        if (points.length === 0) return;

        var index = points[0].index;
        var links = [
            'staff_table_maleres.php',    // index 0 = Male
            'staff_table_femaleres.php'   // index 1 = Female
        ];

        if (links[index]) {
            window.location.href = links[index];
        }
    });

    // ── Show pointer cursor on hover ──
    document.getElementById('genderPieChart').style.cursor = 'pointer';
})();
</script>  
<script>
(function() {
    var ctx = document.getElementById('residentDoughnutChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Total Residents', 'Households', 'Registered Voters'],
            datasets: [{
                data: [
                    <?= (int)$rescount ?>,
                    <?= (int)$rescountfh ?>,
                    <?= (int)$rescountvoter ?>
                ],
                backgroundColor: [
                    'rgba(26, 68, 128, 0.85)',
                    'rgba(13, 148, 136, 0.85)',
                    'rgba(201, 148, 58, 0.85)'
                ],
                borderColor: ['#fff', '#fff', '#fff'],
                borderWidth: 3,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var total = ctx.dataset.data.reduce(function(a,b){return a+b;},0);
                            var pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                            return ' ' + ctx.label + ': ' + ctx.parsed.toLocaleString() + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    // Click a slice to navigate
    document.getElementById('residentDoughnutChart').addEventListener('click', function(e) {
        var points = chart.getElementsAtEventForMode(e, 'nearest', { intersect: true }, true);
        if (points.length === 0) return;
        var links = [
            'staff_table_pwd.php',
            'staff_table_totalhouse.php',
            'staff_table_voters.php'
        ];
        var index = points[0].index;
        if (links[index]) window.location.href = links[index];
    });

    document.getElementById('residentDoughnutChart').style.cursor = 'pointer';
})();
</script>            
<?php 
    include('dashboard_sidebar_end.php');
?>