<?php
	// require the database connection
	require 'classes/conn.php';
	if(isset($_POST['search_resident'])){
		$keyword = $_POST['keyword'];
?>
	<table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 

		<thead class="alert-info">
			<tr>
                <th> Actions</th>
                <th> Resident ID </th>
                <th> Full Name </th>
                <th> Address</th>
			</tr>
		</thead>

		<tbody>       
			<?php
				
				$stmnt = $conn->prepare("SELECT * FROM `tbl_resident` WHERE `lname` LIKE '%$keyword%' or  `mi` LIKE '%$keyword%' or  `fname` LIKE '%$keyword%' 
				or  `age` LIKE '%$keyword%' or  `sex` LIKE '%$keyword%' or  `status` LIKE '%$keyword%' or  `address` LIKE '%$keyword%' or  `contact` LIKE '%$keyword%'
				or  `bdate` LIKE '%$keyword%' or  `bplace` LIKE '%$keyword%' or  `nationality` LIKE '%$keyword%' or  `family_role` LIKE '%$keyword%' or  `role` LIKE '%$keyword%' or  `email` LIKE '%$keyword%'");
				$stmnt->execute();
				
				while($row = $stmnt->fetch()){
			?>
                <tr>
                   <td>    
                <form action="" method="post">
                    <button type="button" class="btn btn-primary btn-sm" style="width: 90px; font-size: 17px; border-radius:30px;" data-toggle="modal" data-target="#viewModal<?= $row['id_resident'] ?>">
                        <i class="fa fa-eye"></i> View
                    </button>

                    <button type="button" class="btn btn-info btn-sm text-white" style="width: 110px; font-size: 17px; border-radius:30px;" data-toggle="modal" data-target="#messageModal<?= $row['id_resident'] ?>">
                        <i class="fas fa-comment-alt"></i> Message
                    </button>
                    
                    <button type="button" class="btn btn-warning btn-sm text-white" style="width: 110px; font-size: 17px; border-radius:30px;" data-toggle="modal" data-target="#promoteModal<?= $row['id_resident'] ?>">
                        <i class="fas fa-user-tie"></i> Promote
                    </button>
                                        <input type="hidden" name="id_resident" value="<?= $row['id_resident'];?>">
                    
                    <button class="btn btn-danger" type="submit" name="delete_resident" style="width: 90px; font-size: 17px; border-radius:30px;"> Archive </button>
                </form>
            </td>
            <td><?= $row['id_resident'];?></td>
            <td><?= $row['lname'];?>, <?= $row['fname'];?> <?= $row['mi'];?></td>
            <td><?= $row['houseno'];?>, <?= $row['street'];?>, <?= $row['brgy'];?></td>

            <div class="modal fade" id="viewModal<?= $row['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Resident Information</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body text-left">
                            <p><strong>Resident ID:</strong> <?= $row['id_resident'];?> </p>
                            <hr style="border: 1px solid #ccc;">
                            <h5><strong>Personal Information</strong></h5>
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Full Name:</strong><br> <?= $row['lname'] ?>, <?= $row['fname'] ?> <?= $row['mi'] ?>.</p>
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
                            <p><strong>Contact Number:</strong> <?= $row['contact'] ?></p>
                            <p><strong>Address:</strong> <?= $row['houseno'] ?>, <?= $row['street'] ?>, <?= $row['brgy'] ?>, <?= $row['municipal'] ?></p>
                            <hr style="border: 1px solid #ccc;">
                            <a href="update_resident_form.php?id_resident=<?= $row['id_resident'];?>" class="btn btn-primary" style="width: 100px; border-radius:30px;"> Update </a>
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
                        </i> View
                    </button>

                    <button type="button" class="btn btn-info btn-sm text-white" style="width: 110px; font-size: 17px; border-radius:30px;" data-toggle="modal" data-target="#messageModal<?= $row['id_resident'] ?>">
                        </i> Message
                    </button>
                    
                    <button type="button" class="btn btn-warning btn-sm text-white" style="width: 110px; font-size: 17px; border-radius:30px;" data-toggle="modal" data-target="#promoteModal<?= $row['id_resident'] ?>">
                        </i> Promote
                    </button>
                                        <input type="hidden" name="id_resident" value="<?= $row['id_resident'];?>">
                    
                    <button class="btn btn-danger" type="submit" name="delete_resident" style="width: 90px; font-size: 17px; border-radius:30px;"> Archive </button>
                </form>
            </td>
            <td><?= $row['id_resident'];?></td>
            <td><?= $row['lname'];?>, <?= $row['fname'];?> <?= $row['mi'];?></td>
            <td><?= $row['houseno'];?>, <?= $row['street'];?>, <?= $row['brgy'];?></td>

            <div class="modal fade" id="viewModal<?= $row['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Resident Information</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body text-left">
                            <p><strong>Resident ID:</strong> <?= $row['id_resident'];?> </p>
                            <hr style="border: 1px solid #ccc;">
                            <h5><strong>Personal Information</strong></h5>
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Full Name:</strong><br> <?= $row['lname'] ?>, <?= $row['fname'] ?> <?= $row['mi'] ?>.</p>
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
                            <p><strong>Contact Number:</strong> <?= $row['contact'] ?></p>
                            <p><strong>Address:</strong> <?= $row['houseno'] ?>, <?= $row['street'] ?>, <?= $row['brgy'] ?>, <?= $row['municipal'] ?></p>
                            <hr style="border: 1px solid #ccc;">
                            <a href="update_resident_form.php?id_resident=<?= $row['id_resident'];?>" class="btn btn-primary" style="width: 100px; border-radius:30px;"> Update </a>
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

            <!-- Promote to Staff Modal -->
            <div class="modal fade" id="promoteModal<?= $row['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #b8860b, #daa520);">
                            <h5 class="modal-title"><i class="fas fa-user-tie mr-2"></i> Promote to Barangay Staff</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <form action="admn_resident_crud.php" method="POST">
                            <div class="modal-body text-left">
                                <div class="alert alert-warning" style="border-radius:10px;">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    You are about to promote <strong><?= $row['fname'] ?> <?= $row['lname'] ?></strong> to a Barangay Staff member.
                                </div>
                                <div class="form-group">
                                    <label><strong>Assign Position:</strong></label>
                                    <select class="form-control" name="position" required style="border-radius:10px;">
                                        <option value="">-- Choose Position --</option>
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
                                <input type="hidden" name="promote_id_resident" value="<?= $row['id_resident'] ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:30px;">Cancel</button>
                                <button type="submit" name="promote_resident" class="btn text-white" style="border-radius:30px; width: 140px; background: linear-gradient(135deg, #b8860b, #daa520);">
                                    <i class="fas fa-level-up-alt mr-1"></i> Promote
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Promote to Staff Modal -->
            <div class="modal fade" id="promoteModal<?= $row['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #b8860b, #daa520);">
                            <h5 class="modal-title"><i class="fas fa-user-tie mr-2"></i> Promote to Barangay Staff</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <form action="admn_resident_crud.php" method="POST">
                            <div class="modal-body text-left">
                                <div class="alert alert-warning" style="border-radius:10px;">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    You are about to promote <strong><?= $row['fname'] ?> <?= $row['lname'] ?></strong> to a Barangay Staff member.
                                </div>
                                <div class="form-group">
                                    <label><strong>Assign Position:</strong></label>
                                    <select class="form-control" name="position" required style="border-radius:10px;">
                                        <option value="">-- Choose Position --</option>
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
                                <input type="hidden" name="promote_id_resident" value="<?= $row['id_resident'] ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:30px;">Cancel</button>
                                <button type="submit" name="promote_resident" class="btn text-white" style="border-radius:30px; width: 140px; background: linear-gradient(135deg, #b8860b, #daa520);">
                                    <i class="fas fa-level-up-alt mr-1"></i> Promote
                                </button>
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
    <?php if (!empty($_SESSION['swal'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire(<?= $_SESSION['swal'] ?>);
    });
</script>
<?php unset($_SESSION['swal']); endif; ?>
<?php
	}
$con = null;
?>