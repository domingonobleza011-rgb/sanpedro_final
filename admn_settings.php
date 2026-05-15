<?php
    require('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();
    $bmis->create_admin(); 
    
    include('dashboard_sidebar_start.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIS - Add Administrator</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">User Management</h1>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-user-plus mr-2"></i>Register New Admin/Staff
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" autocomplete="off">
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label font-weight-bold">First Name</label>
                                    <input type="text" name="fname" class="form-control" placeholder="Enter first name" required>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label font-weight-bold">M.I.</label>
                                    <input type="text" name="mi" class="form-control" maxlength="1" placeholder="A">
                                </div>

                                <div class="col-md-5 mb-3">
                                    <label class="form-label font-weight-bold">Last Name</label>
                                    <input type="text" name="lname" class="form-control" placeholder="Enter last name" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Account Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="" disabled selected>Select Role</option>
                                        <option value="administrator">Administrator</option> 
                                        <option value="staff">Barangay Staff</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label font-weight-bold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                                <small class="text-muted">Ensure the password is secure and unique.</small>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="admin_changepass.php" class="btn btn-success btn-icon-split">
                                    <span class="icon text-white">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <span class="text">Change Existing Password</span>
                                </a>

                                <div>
                                    <button type="reset" class="btn btn-secondary mr-2">Clear</button>
                                    <button type="submit" name="add_admin" class="btn btn-primary px-4">Create Account</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>