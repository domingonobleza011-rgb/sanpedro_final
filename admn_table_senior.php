<?php
    
   error_reporting(E_ALL ^ E_WARNING);
   ini_set('display_errors',0);
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');
   require('classes/resident.class.php');
   $userdetails = $bmis->get_userdata();
   $bmis->validate_staff_or_admin();
   $view = $residentbmis->view_resident_senior_paginated(2);
   
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
        <div class="col-md-12 text-center"> 
            <h1> Barangay Senior Citizen Table</h1>
        </div>
    </div>

    <hr>
    <br><br>

    <div class="row"> 
        <br> 
        <div class="col-md-12">
            <form method="POST" action="">
                <div class="input-icons" >
                    <i class="fa fa-search icon"></i>
                    <input type="search" class="form-control" name="keyword" value="" style="border-radius:30px;" required=""/>
                </div>
                <button class="btn btn-success" style="width: 90px; font-size: 18px; border-radius:30px; margin-left:41.5%;" name="search_senior">Search</button>
                <a href="admn_table_senior.php" style="width: 90px; font-size: 18px; border-radius:30px;" class="btn btn-info">Reload</a>
            </form>
            <br><br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <?php 
                include('admn_table_senior_search.php');
            ?>
        </div>
        <div class="col-md-1"></div>
    </div>
    
    <!-- /.container-fluid -->
    
</div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

<?php 
    include('dashboard_sidebar_end.php');
?>