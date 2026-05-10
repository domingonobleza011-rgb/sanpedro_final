<?php
    error_reporting(E_ALL ^ E_WARNING);
define('BMIS_ROLE_REQUIRED', 'admin');
require_once('secure_header.php'); 
    include('classes/staff.class.php');
    include('classes/resident.class.php');
    require_once('classes/conn.php'); // needed for complaint counts

    $userdetails = $bmis->get_userdata();
    $bmis->validate_admin();

    $rescount = $residentbmis->count_resident();
    $rescountm = $residentbmis->count_male_resident();
    $rescountf = $residentbmis->count_female_resident();
    $rescountfh = $residentbmis->count_head_resident();
    $rescountfm = $residentbmis->count_member_resident();
    $rescountvoter = $residentbmis->count_voters();
    $rescountsenior = $residentbmis->count_resident_senior();

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

<style> 
.card-upper-space {
    margin-top: 35px;
}

.card-row-gap {
    margin-top: 3em;
}
</style>


<?php 
    include('dashboard_sidebar_start.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Page Heading -->


    <div class="row"> 
        <div class="col-md-4">
            <h4> Barangay Resident Data </h4>
            <br>
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Barangay Residents</div>
                                <div class="h5 mb-0 font-weight-bold text-dark"><?= $rescount?></div>
                                <br>
                                <a href="admn_table_totalres.php"> View Records </a>
                        </div>
                        <div class="col-auto">
                            <span style="color: #4e73df;"> 
                                <i class="fas fa-user-friends fa-2x text-dark "></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">  
            <br>
            <div class="card border-left-primary shadow card-upper-space">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Household Count</div>
                                <div class="h5 mb-0 font-weight-bold text-dark"><?= $rescountfh?></div>
                                <br>
                                <a href="admn_table_totalhouse.php"> View Records </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4"> 
            <br>
            <div class="card border-left-primary shadow card-upper-space">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Registered Voters </div>
                                <div class="h5 mb-0 font-weight-bold text-dark"><?= $rescountvoter?></div>
                                <br>
                                <a href="admn_table_voters.php"> View Records </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <div class="row"> 
        <div class="col-md-4">  
            <div class="card border-left-primary shadow card-upper-space">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Male Residents</div>
                                <div class="h5 mb-0 font-weight-bold text-dark"><?= $rescountm?></div>
                                <br>
                                <a href="admn_table_maleres.php"> View Records </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-male fa-2x text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">  
            <div class="card border-left-primary shadow card-upper-space">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Female Residents</div>
                                <div class="h5 mb-0 font-weight-bold text-dark"><?= $rescountf?></div>
                                <br>
                                <a href="admn_table_femaleres.php"> View Records </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-female fa-2x text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-4"> 
            <div class="card border-left-primary shadow card-upper-space">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Senior Residents</div>
                                <div class="h5 mb-0 font-weight-bold text-dark"><?= $rescountsenior?></div>
                                <br>
                                <a href="admn_table_senior.php"> View Records </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-blind fa-2x text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <br>
    <hr>
    <br>

    <div class="row"> 
    <div class="col-md-4">
        <h4> Barangay Staff Data </h4> 
        <br>
        <div class="card border-left-info shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Barangay Staffs</div>
                                <div class="h5 mb-0 font-weight-bold text-dark"><?= $staffcount?></div>
                                <br>
                                <a href="admn_table_totalstaff.php"> View Records </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">  
            <br>
            <div class="card border-left-info shadow card-upper-space">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Barangay Male Staff
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-dark"><?= $staffcountm?></div>
                            <br>
                            <a href="admn_table_malestaff.php"> View Records </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-male fa-2x text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">  
            <br>
            <div class="card border-left-info shadow card-upper-space">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Barangay Female Staffs</div>
                                <div class="h5 mb-0 font-weight-bold text-dark"><?= $staffcountf?></div>
                                <br>
                                <a href="admn_table_femalestaff.php"> View Records </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-female fa-2x text-dark"></i>
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
                            <a href="admn_complaints.php">View All</a>
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
                            <a href="admn_complaints.php?status=pending" class="text-warning fw-semibold">Review Now</a>
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
                            <a href="admn_complaints.php?status=resolved" class="text-success">View Resolved</a>
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

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<?php 
    include('dashboard_sidebar_end.php');
?>