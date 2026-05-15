<?php
    require('classes/main.class.php');
    $bmis->admin_changepass();
    $userdetails = $bmis->get_userdata();

    include('dashboard_sidebar_start.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>BMIS - Change Password</title>
    
    <!-- SB Admin 2 & Bootstrap Core -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../BarangaySystem/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"> 
    
    <!-- Custom & Icons -->
    <link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
</head>

<body id="page-top">

<div class="container-fluid mt-5"> 
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8">
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-lock mr-2"></i>Security Settings
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1 class="h4 text-gray-900">Change Password</h1>
                        <p class="small text-muted">Ensure your new password is at least 8 characters long for better security.</p>
                    </div>

                    <form method="POST" autocomplete="off"> 
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Current Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-key text-gray-400"></i></span>
                                </div>
                                <input type="password" name="oldpassword" class="form-control" placeholder="Enter old password" required>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">New Password</label>
                            <input type="password" name="newpassword" class="form-control" placeholder="Enter new password" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Confirm New Password</label>
                            <input type="password" name="checkpassword" class="form-control" placeholder="Re-type new password" required>
                        </div>

                        <div class="text-center border-top pt-3">
                            <input type="hidden" name="id_admin" value="<?php echo $userdetails['id_admin']; ?>">
                            
                            <button type="submit" name="admin_changepass" class="btn btn-success btn-block shadow-sm">
                                <i class="fas fa-save mr-2"></i> Update Password
                            </button>
                            
                            <div class="mt-3">
                                <a href="admn_settings.php" class="small text-secondary">Cancel and Return</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div> 
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"></script>

</body>
</html>