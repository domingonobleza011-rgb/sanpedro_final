<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL ^ E_WARNING);
define('BMIS_ROLE_REQUIRED', 'admin');
require_once('secure_header.php'); 
    require('classes/staff.class.php');
    $userdetails = $bmis->get_userdata();
    $bmis->validate_admin();
    $view = $staffbmis->view_staff();
    $staffbmis->create_staff();
    $upstaff = $staffbmis->update_staff();
    $staffbmis->delete_staff();
    $staffcount = $staffbmis->count_staff();
    
?>

<?php 
    include('dashboard_sidebar_start.php');
?>
<!-- Begin Page Content -->

<div class="container-fluid">


    <div class="row mb-4">
    <div class="col-md-9">
        <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addStaffModal" style="border-radius:30px; padding: 10px 20px;">
            <i class="fas fa-plus-circle"></i> Add New Barangay Staff
        </button>
    </div>

    <div class="col-md-3">
        <div class="card border-left-primary shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Staff Registered</div>
                        <div class="h5 mb-0 font-weight-bold text-dark"><?= $staffcount?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel">Add New Barangay Staff</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Staff Profile Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name:</label>
                                <input type="text" class="form-control" name="lname" placeholder="Enter Last Name" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name:</label>
                                <input type="text" class="form-control" name="fname" placeholder="Enter First Name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Middle Initial:</label>
                                <input type="text" class="form-control" name="mi" placeholder="Enter Middle Initial" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact Number:</label>
                                <input type="tel" class="form-control" name="contact" maxlength="11" pattern="[0-9]{11}" placeholder="09XXXXXXXXX" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Login Identity (Email or Phone):</label>
                                <input type="text" class="form-control" name="login_identity" placeholder="Enter Email or Phone Number" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" class="form-control" name="password" placeholder="Enter Password" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" name="address" placeholder="Enter Address" required>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Position:</label>
                                <select class="form-control" name="position" required>
                                    <option value="">Choose Position</option>
                                    <option value="Punong Barangay">Punong Barangay</option>
                                    <option value="Secretary">Secretary</option>
                                    <option value="Treasurer">Treasurer</option>
                                    <option value="Clerk">Clerk</option>
                                    <option value="Book Keeper">Book Keeper</option>
                                    <option value="Committee on Appropriation">Committee on Appropriation</option>
                                        <option value="Committee on Health">Committee on Health</option>
                                        <option value="Committee on Women and Children">Committee on Women and Children</option>
                                        <option value="Committee on Education">Committee on Education</option>
                                        <option value="Committee on Peace and Order">Committee on Peace and Order</option>
                                        <option value="Committee on Infrastructure">Committee on Infrastructure</option>
                                        <option value="Committee on Ways and Means">Committee on Ways and Means</option>
                                        <option value="Committee on Agriculture">Committee on Agriculture</option>
                                        <option value="Committee on Tourism">Committee on Tourism</option>
                                        <option value="IPMRR Representative">IPMRR Representative</option>
                                        <option value="Sk Chairperson">Sk Chairperson</option>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Age</label>
                                <input type="number" class="form-control" name="age" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Sex</label>
                                <select class="form-control" name="sex" required>
                                    <option value="">Choose Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="role" value="user">
                    <input type="hidden" name="addedby" value="<?= $userdetails['surname']?>, <?= $userdetails['firstname']?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:30px;">Close</button>
                    <button type="submit" name="add_staff" class="btn btn-primary" style="border-radius:30px; width: 120px;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <h1 class="mb-4 text-center">Barangay Staff Tables</h1>

    <hr>
    <br>
    <br>

    <div class="row"> 
        <div class="col-md-12">
           <table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
        <thead class="alert-info">
                        <tr>
                            <th> Actions </th>

                            <th> Fullname </th>
                            <th> Age </th>
                            <th> Sex </th>
                            <th> Address </th>
                            <th> Contact </th>
                            <th> Position </th>
                            <th> Role </th>
                            <th> AddedBy </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if(is_array($view)) {?>
                            <?php foreach($view as $view) {?>
                                <tr>
                                    <td>    
                                        <form action="" method="post">
                                            <a href="update_staff_form.php?id_user=<?= $view['id_user'];?>" style="width: 90px; font-size: 17px; border-radius:30px; margin-bottom: 2px;" class="btn btn-success"> Update </a>
                                            <input type="hidden" name="id_user" value="<?= $view['id_user'];?>">
                                            <button class="btn btn-danger" type="submit" name="delete_staff"style="width: 90px; font-size: 17px; border-radius:30px;"> Archive </button>
                                        </form>
                                    </td>
                                    <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?> </td>
                                    <td> <?= $view['age'];?> </td>
                                    <td> <?= $view['sex'];?> </td>
                                    <td> <?= $view['address'];?> </td>
                                    <td> <?= $view['contact'];?> </td>
                                    <td> <?= $view['position'];?> </td>
                                    <td> <?= $view['role'];?> </td>
                                    <td> <?= $view['addedby'];?> </td>   
                                </tr>
                            <?php }?>
                        <?php } ?>
                    </tbody>
                </form>
            </table>
        </div>
    </div>
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