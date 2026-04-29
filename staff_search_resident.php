<?php
	// require the database connection
	require 'classes/conn.php';
	if(isset($_POST['search_resident'])){
		$keyword = $_POST['keyword'];
?>
	<table class="table table-hover text-center table-bordered table-responsive" >

		<thead class="alert-info">
			<tr>
                <th> Actions</th>
                <th> Email </th>
                <th> Fullname </th>
                <th> Age </th>
                <th> Sex </th>
                <th> Status </th>
                <th> Address </th>
                <th> Contact </th>
                <th> Birth date </th>
                <th> Birth place </th>
                <th> Nationality </th>
                <th> Registered Voter </th>
                <th> Head of the Family </th>
			</tr>
		</thead>

		<tbody>
		      
			<?php
				
				$stmnt = $conn->prepare("SELECT * FROM `tbl_resident` WHERE `lname` LIKE '%$keyword%' or  `mi` LIKE '%$keyword%' or  `fname` LIKE '%$keyword%' 
				or  `age` LIKE '%$keyword%' or  `sex` LIKE '%$keyword%' or  `status` LIKE '%$keyword%' or  `address` LIKE '%$keyword%' or  `contact` LIKE '%$keyword%'
				or  `bdate` LIKE '%$keyword%' or  `bplace` LIKE '%$keyword%' or  `nationality` LIKE '%$keyword%' or  `family_role` LIKE '%$keyword%' or  `role` LIKE '%$keyword%' or  `email` LIKE '%$keyword%'");
				$stmnt->execute();
				
				while($view = $stmnt->fetch()){
			?>

			<tr>
			<td>  
                  
            <form action="" method="post">
                            <a href="update_resident_form_for_staff.php?id_resident=<?= $view['id_resident'];?>" style="width: 90px; font-size: 17px; border-radius:30px;" class="btn btn-primary">  Update </a>
                            <input type="hidden" name="id_resident" value="<?= $view['id_resident'];?>">
                        </form>
            </td>
			            <td> <?= $view['email'];?> </td>
                        <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?> </td>
                        <td> <?= $view['age'];?> </td>
                        <td> <?= $view['sex'];?> </td>
                        <td> <?= $view['status'];?> </td>
                        <td> <?= $view['houseno'];?>, <?= $view['street'];?>, <?= $view['brgy'];?>, <?= $view['municipal'];?> </td>
                        <td> <?= $view['contact'];?> </td>
                        <td> <?= $view['bdate'];?> </td>
                        <td> <?= $view['bplace'];?> </td>
                        <td> <?= $view['nationality'];?> </td>
                        <td> <?= $view['family_role'];?> </td>
                        <td> <?= $view['voter'];?> </td>
			</tr>
			<?php
			}
			?>
			
			
		</tbody>
	</table>
<?php		
	}else{
?>

<table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
		<thead class="alert-info">
        <tr>
        <th> Actions</th>
                <th> Resident ID </th>
                <th> Email </th>
                <th> Full Name </th>
                <th> Address</th>
        </tr>
    </thead>
    <tbody>

        <?php if(is_array($view)) { ?>
    <?php foreach($view as $row) { ?>
        <tr>                    
            <td>    
                <form action="" method="post">
                    <button type="button" class="btn btn-primary btn-sm" style="width: 90px; font-size: 17px; border-radius:30px;" data-toggle="modal" data-target="#viewModal<?= $row['id_resident'] ?>">
                        <i class="fa fa-eye"></i> View
                    </button>

                    
                    <input type="hidden" name="id_resident" value="<?= $row['id_resident'];?>">
                    
                    <button class="btn btn-danger" type="submit" name="delete_resident" style="width: 90px; font-size: 17px; border-radius:30px;"> Archive </button>
                </form>
            </td>
            <td><?= $row['id_resident'];?></td>
            <td><?= $row['email'];?></td>
            <td><?= $row['lname'];?>, <?= $row['fname'];?> <?= $row['mi'];?></td>
            <td><?= $row['houseno'];?>, <?= $row['street'];?>, <?= $row['brgy'];?></td>

            <div class="modal fade" id="viewModal<?= $row['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Resident Information</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body text-left">
                            <p><strong>Resident ID:</strong> <?= $row['id_resident'];?> </p>
                            <hr style="border: 2px solid green; opacity: 1;">
                            <h5><strong>Personal Information</strong></h5>
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Full Name:</strong><br> <?= $row['lname'] ?>, <?= $row['fname'] ?> <?= $row['mi'] ?>.</p>
                                    <p><strong>Age:</strong> <?= $row['age'] ?></p>
                                    <p><strong>Sex:</strong> <?= $row['sex'] ?></p>
                                    <p><strong>Civil Status:</strong> <?= $row['status'] ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Nationality:</strong> <?= $row['nationality'] ?></p>
                                    <p><strong>Birth Date:</strong> <?= $row['bdate'] ?></p>
                                    <p><strong>Birth Place:</strong> <?= $row['bplace'] ?></p>
                                    <p><strong>Family Role:</strong> <?= $row['family_role'] ?></p>
                                </div>
                            </div>
                            <hr style="border: 1px solid #ccc;">
                            <h5><strong>Contact & Address</strong></h5>
                            <p><strong>Email:</strong> <?= $row['email'] ?></p>
                            <p><strong>Contact Number:</strong> <?= $row['contact'] ?></p>
                            <p><strong>Address:</strong> <?= $row['houseno'] ?>, <?= $row['street'] ?>, <?= $row['brgy'] ?>, <?= $row['municipal'] ?></p>
                            <hr style="border: 2px solid green; opacity: 1;">
                            <a href="update_resident_form.php?id_resident=<?= $row['id_resident'];?>" class="btn btn-success" style="width: 100px; border-radius:30px;"> Update </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="messageModal<?= $row['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title"><i class="fas fa-paper-plane mr-2"></i> Send Message</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <form action="send_resident_msg.php" method="POST">
                            <div class="modal-body text-left">
                                <div class="form-group">
                                    <label><strong>Recipient:</strong></label>
                                    <input type="text" class="form-control-plaintext border-bottom" value="<?= $row['fname'] ?> <?= $row['lname'] ?>" readonly>
                                </div>
                                <div class="form-group mt-3">
                                    <label><strong>Message Content:</strong></label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="Write your message here..." required></textarea>
                                </div>
                                <input type="hidden" name="id_resident" value="<?= $row['id_resident'] ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:30px;">Cancel</button>
                                <button type="submit" name="send_msg" class="btn btn-info text-white" style="border-radius:30px; width: 120px;">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </tr>
    <?php } ?>
<?php } ?>
        
    </tbody>
</table>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modalmanager.min.js" integrity="sha512-/HL24m2nmyI2+ccX+dSHphAHqLw60Oj5sK8jf59VWtFWZi9vx7jzoxbZmcBeeTeCUc7z1mTs3LfyXGuBU32t+w==" crossorigin="anonymous"></script>
<!-- responsive tags for screen compatibility -->
<meta name="viewport" content="width=device-width, initial-scale=1 shrink-to-fit=no">
<!-- custom css --> 
<link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">
<!-- bootstrap css --> 
<link href="../BarangaySystem/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"> 
<!-- fontawesome icons -->
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"> </script>

<?php
	}
$con = null;
?>