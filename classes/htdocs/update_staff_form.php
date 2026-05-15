<?php
    error_reporting(E_ALL ^ E_WARNING);
    require('classes/staff.class.php');
    $userdetails = $bmis->get_userdata();
    //$bmis->validate_admin();
    $view = $staffbmis->view_staff();
    $staffbmis->create_staff();
    $upstaff = $staffbmis->update_staff();
    $staffbmis->delete_staff();
    $staffcount = $staffbmis->count_staff();
    $id_user = $_GET['id_user'];
    $staff = $staffbmis->get_single_staff($id_user);
?>
 
<?php 
    include('dashboard_sidebar_start.php');
?>
 
<!-- Begin Page Content -->
 
<div class="container-fluid">
    <h1 class="mb-4 text-center">Barangay Staff Data</h1>
    <hr><br>
 
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="card shadow"> 
                <div class="card-header bg-primary text-white"> Update Barangay Staff Data </div>
                <div class="card-body"> 
                    <form method="post" enctype="multipart/form-data">
                        
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <label class="d-block mb-2">Current Photo</label>
                                <?php if(!empty($staff['photo'])): ?>
                                    <img src="/BarangaySystem-master/<?= $staff['photo']; ?>" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
                                <?php else: ?>
                                    <i class="fas fa-user-circle fa-7x text-secondary"></i>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <label class="form-group fw-bold">Change Profile Photo:</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                <small class="text-muted">Leave blank if you don't want to change the current photo.</small>
                            </div>
                        </div>
 
                        <div class="row mt-3">
                            <div class="col">
                                <label class="form-group"> Last Name:</label>
                                <input type="text" class="form-control" name="lname" value="<?= htmlspecialchars($staff['lname']);?>">
                            </div>
                            <div class="col">
                                <label class="form-group">First Name: </label>
                                <input type="text" class="form-control" name="fname" value="<?= htmlspecialchars($staff['fname']);?>">
                            </div>
                            <div class="col">
                                <label class="form-group"> Middle Name: </label>
                                <input type="text" class="form-control" name="mi" value="<?= htmlspecialchars($staff['mi']);?>">
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col">
                                <label class="form-group">Login Identity (Email or Phone): </label>
                                <input type="text" class="form-control" name="login_identity" value="<?= htmlspecialchars($staff['login_identity']);?>" placeholder="Enter Email or Phone Number" required>
                            </div>
                            <div class="col">
                                <label class="form-group">Password:</label>
                                <input type="password" class="form-control" name="password" placeholder="Enter new password to change">
                            </div>
                            <div class="col">
                                <label class="form-group">Contact Number:</label>
                                <input type="tel" class="form-control" name="contact" value="<?= htmlspecialchars($staff['contact']);?>">
                            </div>
                        </div>
 
                        <div class="row mt-3">
                            <div class="col"> 
                                <label class="form-group">Position: </label>
                                <select class="form-control" name="position" required>
                                    <option value="<?= $staff['position'];?>" selected><?= $staff['position'];?></option>
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
                        <div class="row mt-3">
                            <div class="col-md-8">
                                <label class="form-group">Address</label>
                                <input class="form-control" type="text" name="address" value="<?= htmlspecialchars($staff['address']);?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-group">Age: </label>
                                <input type="number" class="form-control" name="age" value="<?= $staff['age'];?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-group"> Gender: </label>
                                <select class="form-control" name="sex" required>
                                    <option value="Male" <?= $staff['sex'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?= $staff['sex'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                        </div>
                        
                        <input type="hidden" name="role" value="user">
                        <input type="hidden" name="addedby" value="<?= $userdetails['surname']?>, <?= $userdetails['firstname']?>">
                        
                        <br><hr>
                        <div class="text-center">
                            <a href="admn_staff_crud.php" class="btn btn-danger" style="width: 120px; font-size: 18px; border-radius:30px;"> Back </a>
                            <button class="btn btn-primary" type="submit" name="update_staff" style="width: 120px; font-size: 18px; border-radius:30px;"> 
                                Update 
                            </button>
                        </div>
                    </form>         
                </div>
            </div>
        </div>
        <div class="col-md-2"> </div>
    </div>
    <br>
</div>
<!-- /.container-fluid -->
 
<!-- End of Main Content -->
 
<?php 
    include('dashboard_sidebar_end.php');
?>