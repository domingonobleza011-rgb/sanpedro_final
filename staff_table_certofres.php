<?php
    
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors',0);
    require('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();
    $bmis->validate_staff();
    $bmis->delete_certofres();
    $view = $bmis->view_certofres();
    $id_resident = $_GET['id_resident'];
    $resident = $residentbmis->get_single_certofres($id_resident);
   
?>

<?php 
    include('dashboard_sidebar_start_staff.php');
?>

<style>
    .input-icons i {
        position: absolute;
    }
        
    .input-icons {
        width: 30%;
        margin-bottom: 10px;
        margin-left: 34%;
    }
        
    .icon {
        padding: 10px;
        min-width: 40px;
    }
    .form-control{
        text-align: center;
    }
</style>

    <!-- Begin Page Content -->
    
    <div class="container-fluid">

    <!-- Page Heading -->

    <div class="row"> 
        <div class="col text-center"> 
            <h1> Certificate of Residency Requests</h1>
        </div>
    </div>

    <hr>

    <br><br>

    <div class="row">
        <div class="col">
            <form method="POST">
            <div class="input-icons" >
                <i class="fa fa-search icon"></i>
                <input type="search" class="form-control" name="keyword" value="" required="" style="border-radius: 30px;"/>
            </div>
                <button class="btn btn-success" name="search_certofres" style="width: 90px; font-size: 17px; border-radius:30px; margin-left:41.5%;">
                    Search
                </button>
                <a href="staff_table_certofres.php" class="btn btn-info" style="width: 90px; font-size: 17px; border-radius:30px;">Reload</a>
            </form>
            <br>
        </div>
    </div>

    <div class="row"> 
        <div class="col-md-12"> 
            <?php 
                include('staff_table_certofres_search.php');
            ?>
        </div>
    </div>
    
    <!-- /.container-fluid -->
    
</div>
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
