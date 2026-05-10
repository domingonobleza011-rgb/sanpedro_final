<?php
    error_reporting(E_ALL ^ E_WARNING);
    require('classes/staff.class.php');
    $userdetails = $bmis->get_userdata();
    //$bmis->validate_admin();
    $id_user = $_GET['id_user'];
    $view = $staffbmis->get_single_staff($id_user);
    $staffbmis->create_staff();
    $upstaff = $staffbmis->update_staff();
    $staffbmis->delete_staff();
    $staffcount = $staffbmis->count_staff();
?>

<?php 
    include('dashboard_sidebar_start_staff.php');
?>
    <!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->

    <h1 class="mb-4 text-center">Barangay Staff Profile</h1>
    <hr>
    <br>
    <div class="card" >
        <div class="card-header bg-primary text-white"> 
            <h5>
                Barangay Staff Credentials
            </h5>
        </div>                 
        <div class="card-body"> 
            <form method="post">
                <div class="row">
                    <div class="col">
                        <label class="form-group"> Last Name</label>
                        <input type="text" class="form-control" name="lname"  Value="<?= $view['lname'];?>" readonly>
                    </div>
                    <div class="col">
                        <label class="form-group" >First Name </label>
                        <input type="text" class="form-control" name="fname"  Value="<?= $view['fname'];?>" readonly>
                    </div>
                    <div class="col">
                        <label class="form-group"> Middle Initial </label>
                        <input type="text" class="form-control" name="mi" Value="<?= $view['mi'];?>" readonly>
                    </div>
                </div>


                <div class="col">
                        <div class="form-group">
                            <label>Comple Address</label>
                            <input class="form-control" type="text" name="address" Value="<?= $view['address'];?>">
                        </div>
                    </div>
                <div class="row" style="margin-top: 1.1em;">
                    <div class="col">
                        <label class="form-group">Email </label>
                        <input type="email" class="form-control" name="email"  Value="<?= $view['email'];?>">
                    </div>
                    <div class="col">
                        <label class="form-group">Password </label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="col">
                        <label class="form-group">Contact Number</label>
                        <input type="tel" class="form-control" name="contact" Value="<?= $view['contact'];?>">
                    </div>
                    <div class="col">
                        <label class="form-group">Position </label>
                        <input type="text" class="form-control" name="position"  Value="<?= $view['position'];?>">
                    </div>
                    <div class="col">
                        <label class="form-group">Age </label>
                        <input type="number" class="form-control" name="age" Value="<?= $view['age'];?>">
                    </div>
                    <div class="col">
                        <label class="form-group">sex </label>
                        <input type="text" class="form-control" name="sex" Value="<?= $view['sex'];?>">
                    </div>
                </div>
                
                <input type="hidden" class="form-control" name="role" value="user">
                <input type="hidden" class="form-control" name="addedby" value="<?= $userdetails['surname']?>, <?= $userdetails['firstname']?>">
                <br>
                <hr>
                <button class="btn btn-primary" type="submit" name="update_staff" 
                        style="margin-left: 42%;
                               width: 150px;
                               border-radius: 30px;
                               font-size: 18px;"> 
                    Update 
                </button>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<!-- End of Main Content -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

<?php 
    include('dashboard_sidebar_end.php');
?>