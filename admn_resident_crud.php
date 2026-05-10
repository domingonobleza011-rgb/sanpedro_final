<?php
    
   error_reporting(E_ALL ^ E_WARNING);
   ini_set('display_errors',0);
define('BMIS_ROLE_REQUIRED', 'admin');
require_once('secure_header.php'); 
   require('classes/resident.class.php');
   $userdetails = $bmis->get_userdata();
   $bmis->validate_admin();
   $view = $residentbmis->view_resident();
   $residentbmis->create_resident();
   $residentbmis->update_resident();
   $residentbmis->delete_resident();
   

   $rescount = $residentbmis->count_resident();
   $rescountm = $residentbmis->count_male_resident();
   $rescountf = $residentbmis->count_female_resident();
   $rescountfh = $residentbmis->count_head_resident();
   $rescountfm = $residentbmis->count_member_resident();
   
?>

<?php 
    include('dashboard_sidebar_start.php');
?>
<style>
    .input-icons i {
        position: absolute;
    }
        
    .input-icons {
        width: 30%;
        margin-bottom: 20px;
        margin-left: 34%;
    }
        
    .icon {
        padding: 10px;
        min-width: 40px;
    }

    .search{
        text-align: center;
    }
    .search-container {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .pill-search {
        border-radius: 50px !important;
        padding-left: 45px;
        border: 1px solid #ddd;
    }
    .search-icon-inside {
        position: absolute;
        left: 25px;
        top: 18px;
        z-index: 10;
        color: #4e73df;
    }

    /* Stat Card Borders (Matching your image) */
    .border-left-blue { border-left: 5px solid #4e73df !important; }
    .border-left-green { border-left: 5px solid #1cc88a !important; }
    .border-left-cyan { border-left: 5px solid #36b9cc !important; }
    .border-left-orange { border-left: 5px solid #f6c23e !important; }

    /* Button Styling */
    .btn-pill {
        border-radius: 50px;
        padding: 8px 25px;
    }
</style>

<div class="container-fluid">
    <h1 class="mb-4 text-center text-secondary" style="font-weight: 300;">Barangay Residents Data</h1>

    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="mb-3 text-left">
                <button type="button" class="btn btn-primary btn-pill shadow-sm" data-toggle="modal" data-target="#addResidentModal">
                    <i class="fas fa-plus"></i> Add New Barangay Resident
                </button>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-blue shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="font-size: 0.7rem;">Number of Residents</div>
                            <div class="h4 mb-0 font-weight-bold text-dark"><?= $rescount ?></div>
                            <a href="admn_table_totalres.php"> View Records </a>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-green shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 0.7rem;">Total Household Head</div>
                            <div class="h4 mb-0 font-weight-bold text-dark"><?= $rescountfh ?></div>
                            <a href="admn_table_totalhouse.php"> View Records </a>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-tie fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-cyan shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="font-size: 0.7rem;">Total Male Residents</div>
                            <div class="h4 mb-0 font-weight-bold text-dark"><?= $rescountm ?></div>
                            <a href="admn_table_maleres.php"> View Records </a>
                        </div>
                        <div class="col-auto"><i class="fas fa-male fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-orange shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.7rem;">Total Female Residents</div>
                            <div class="h4 mb-0 font-weight-bold text-dark"><?= $rescountf ?></div>
                            <a href="admn_table_femaleres.php"> View Records </a>
                        </div>
                        <div class="col-auto"><i class="fas fa-female fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="search-container mb-4">
                <form method="POST" action="">
                    <div class="position-relative mb-3">
                        
                        <input type="search" class="form-control pill-search form-control-lg" name="keyword" placeholder="Search resident..." required>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-success btn-pill mr-2" name="search_resident" style="width: 120px;">Search</button>
                        <a href="admn_resident_crud.php" class="btn btn-info btn-pill text-white" style="width: 120px;">Reload</a>
                    </div>
                </form>
            </div>
    <div class="row">
        <div class="col-12">
            <div class="bg-white p-4 shadow-sm" style="border-radius: 15px;">
                <?php include('search_resident.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addResidentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl shadow-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel text-center">Add New Barangay Resident</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" class="was-validated">
                <div class="modal-body bg-light">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Last Name:</label>
                            <input type="text" class="form-control" name="lname" placeholder="Enter Last Name" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>First Name:</label>
                            <input type="text" class="form-control" name="fname" placeholder="Enter First Name" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Middle Initial:</label>
                            <input type="text" class="form-control" name="mi" placeholder="Enter Middle Initial" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Contact Number:</label>
                            <input type="tel" class="form-control" name="contact" maxlength="11" pattern="[0-9]{11}" placeholder="09xxxxxxxxx" required>
                        </div>
                       <div class="col-md-4 form-group">
                            <label>Email or Phone Number:</label>
                            <input type="text" class="form-control" name="login_identity" placeholder="Enter Email or Phone Number" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Password:</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter Password" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 form-group"><label>House No:</label><input type="text" class="form-control" name="houseno" required></div>
                        <div class="col-md-3 form-group"><label>Street:</label><input type="text" class="form-control" name="street" required></div>
                        <div class="col-md-3 form-group"><label>Barangay:</label><input type="text" class="form-control" name="brgy" required></div>
                        <div class="col-md-3 form-group"><label>Municipality:</label><input type="text" class="form-control" name="municipal" required></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group"><label>Birth Date:</label><input type="date" class="form-control" name="bdate" required></div>
                        <div class="col-md-4 form-group"><label>Birth Place:</label><input type="text" class="form-control" name="bplace" required></div>
                        <div class="col-md-4 form-group"><label>Nationality:</label><input type="text" class="form-control" name="nationality" required></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Status:</label>
                            <select class="form-control" name="status" required>
                                <option value="">Choose Status...</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Age:</label>
                            <input type="number" class="form-control" name="age" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Sex:</label>
                            <select class="form-control" name="sex" required>
                                <option value="">Choose Sex...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Registered Voter?</label>
                            <select class="form-control" name="voter" required>
                                <option value="">...</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Head of Family?</label>
                            <select class="form-control" name="family_role" required>
                                <option value="">...</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="role" value="resident">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:30px;">Close</button>
                    <button type="submit" name="add_resident" class="btn btn-primary" style="width: 150px; border-radius:30px;">Submit Data</button>
                </div>
            </form>
        </div>
    </div>
</div>


    
    <!-- /.container-fluid -->
    
</div>
<!-- End of Main Content -->

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
