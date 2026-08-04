<?php
	// require the database connection
	require 'classes/conn.php';
	if(isset($_POST['search_clearance'])){
		$keyword = $_POST['keyword'];
?>
<div class="table-responsive" style="width: 100%; overflow-x: auto;">
    <table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
        <thead class="alert-info">

        <tr>
            <th> Actions</th>
            <th> Resident ID </th>
            <th> Full Name </th>
            <th> Address </th>
        </tr>
    </thead>
</div>
    <tbody>     
        <?php
            		$stmnt = $conn->prepare("SELECT * FROM `tbl_clearance` WHERE `lname` LIKE ? or `mi` LIKE ? or `fname` LIKE ?
		or `age` LIKE ? or `id_resident` LIKE ? or `houseno` LIKE ? or `street` LIKE ?
		or `brgy` LIKE ? or `municipal` LIKE ? or `industry` LIKE ? or `aoe` LIKE ?");
		$stmnt->execute();
            
            while($view = $stmnt->fetch()){
        ?>
            <tr>
                <td>    
                    <form action="" method="post">
                        <a class="btn btn-success" style="width: 90px; font-size: 17px; border-radius:30px; margin-bottom: 2px;" href="indigency_form.php?id_resident=<?= $view['id_resident'];?>">Generate</a> 
                        <input type="hidden" name="id_clearance" value="<?= $view['id_clearance'];?>">
                        <button class="btn btn-danger" style="width: 90px; font-size: 17px; border-radius:30px;" type="submit" name="delete_clearance"> Archive </button>
                        <button type="button" class="btn btn-info btn-sm text-white" style="width:110px;font-size:17px;border-radius:30px;margin-bottom:2px;" data-toggle="modal" data-target="#messageModal<?= $view['id_resident'];?>_<?= $view['id_clearance'];?>" data-bs-toggle="modal" data-bs-target="#messageModal<?= $view['id_resident'];?>_<?= $view['id_clearance'];?>">
                            <i class="fas fa-comment-alt"></i> Message
                        </button>
                    </form>
                </td>
                <td> <?= $view['id_resident'];?> </td> 
                <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?> </td>
                <td> <?= $view['houseno'];?>, <?= $view['street'];?>, <?= $view['brgy'];?>, <?= $view['municipal'];?> </td>

                <!-- Message Modal -->
                <div class="modal fade" id="messageModal<?= $view['id_resident'];?>_<?= $view['id_indigency'];?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="border-radius:20px;overflow:hidden;">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title"><i class="fas fa-paper-plane mr-2"></i> Send Message</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">&times;</button>
                            </div>
                            <form action="send_resident_msg.php" method="POST">
                                <div class="modal-body text-left">
                                    <div class="form-group">
                                        <label><strong>Recipient:</strong></label>
                                        <input type="text" class="form-control-plaintext border-bottom" value="<?= htmlspecialchars($view['fname']) ?> <?= htmlspecialchars($view['lname']) ?>" readonly>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label><strong>Message Content:</strong></label>
                                        <textarea name="message" class="form-control" rows="4" placeholder="Write your message here..." required></textarea>
                                    </div>
                                    <input type="hidden" name="id_resident" value="<?= $view['id_resident'];?>">
                                    <input type="hidden" name="redirect_to" value="admn_clearance.php">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius:30px;">Cancel</button>
                                    <button type="submit" name="send_msg" class="btn btn-info text-white" style="border-radius:30px;width:120px;">Send</button>
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

<div class="table-responsive" style="width: 100%; overflow-x: auto;">
    <table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
        <thead class="alert-info">
        <tr>
            <th> Actions</th>
            <th> Resident ID </th>
            <th> Full Name </th>
            <th> Address </th>
        </tr>
    </thead>
</div>
    <tbody>
        <?php if(is_array($view)) {?>
            <?php foreach($view as $view) {?>
                <tr>
                    <td>    
                        <form action="" method="post">
                            <a class="btn btn-success" style="width: 90px; font-size: 17px; border-radius:30px; margin-bottom: 2px;" href="brgyclearance_form.php?id_resident=<?= $view['id_resident'];?>">Generate</a> 
                            <input type="hidden" name="id_clearance" value="<?= $view['id_clearance'];?>">
                            <button class="btn btn-danger" style="width: 90px; font-size: 17px; border-radius:30px;" type="submit" name="delete_clearance"> Archive </button>
                            <button type="button" class="btn btn-info btn-sm text-white" style="width:110px;font-size:17px;border-radius:30px;margin-bottom:2px;" data-toggle="modal" data-target="#messageModal<?= $view['id_resident'];?>_<?= $view['id_clearance'];?>" data-bs-toggle="modal" data-bs-target="#messageModal<?= $view['id_resident'];?>_<?= $view['id_clearance'];?>">
                                <i class="fas fa-comment-alt"></i> Message
                            </button>
                        </form>
                    </td>
                <td> <?= $view['id_resident'];?> </td> 
                <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?> </td>
                <td> <?= $view['houseno'];?>, <?= $view['street'];?>, <?= $view['brgy'];?>, <?= $view['municipal'];?> </td>

                <!-- Message Modal -->
                <div class="modal fade" id="messageModal<?= $view['id_resident'];?>_<?= $view['id_clearance'];?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="border-radius:20px;overflow:hidden;">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title"><i class="fas fa-paper-plane mr-2"></i> Send Message</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">&times;</button>
                            </div>
                            <form action="send_resident_msg.php" method="POST">
                                <div class="modal-body text-left">
                                    <div class="form-group">
                                        <label><strong>Recipient:</strong></label>
                                        <input type="text" class="form-control-plaintext border-bottom" value="<?= htmlspecialchars($view['fname']) ?> <?= htmlspecialchars($view['lname']) ?>" readonly>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label><strong>Message Content:</strong></label>
                                        <textarea name="message" class="form-control" rows="4" placeholder="Write your message here..." required></textarea>
                                    </div>
                                    <input type="hidden" name="id_resident" value="<?= $view['id_resident'];?>">
                                    <input type="hidden" name="redirect_to" value="admn_clearance.php">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius:30px;">Cancel</button>
                                    <button type="submit" name="send_msg" class="btn btn-info text-white" style="border-radius:30px;width:120px;">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </tr>
            
            <?php
                }
            ?>
        <?php
            }
        ?>
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