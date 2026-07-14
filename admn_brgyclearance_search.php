<?php
	// require the database connection
	require 'classes/conn.php';
	if(isset($_POST['search_clearance'])){
		$keyword = $_POST['keyword'];

		$stmnt = $conn->prepare("SELECT * FROM `tbl_clearance` WHERE `lname` LIKE ? or `mi` LIKE ? or `fname` LIKE ?
		or `age` LIKE ? or `id_resident` LIKE ? or `houseno` LIKE ? or `street` LIKE ?
		or `brgy` LIKE ? or `municipal` LIKE ? or `industry` LIKE ? or `aoe` LIKE ?");
		$like = '%' . $keyword . '%';
		$stmnt->execute([$like, $like, $like, $like, $like, $like, $like, $like, $like, $like, $like]);
		$rows = $stmnt->fetchAll();
?>

<div class="table-responsive" style="width: 100%; overflow-x: auto;">
    <table class="table table-hover text-center table-bordered" style="min-width: 1000px;">
        <thead class="alert-info">
        <tr>
            <th> Actions</th>
            <th> Resident ID </th>
            <th> Fullname </th>
            <th> Purpose </th>
            <th> Address </th>
            <th> Street </th>
            <th> Barangay </th>
            <th> Municipality </th>
            <th> Status </th>
            <th> Age </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $view): ?>
            <tr>
                <td>
                    <form action="" method="post">
                        <a class="btn btn-success" target="blank" style="width: 20px; font-size: 17px; border-radius:30px; margin-bottom: 2px;" href="brgyclearance_form.php?id_resident=<?= $view['id_resident'];?>">Generate</a>
                        <input type="hidden" name="id_clearance" value="<?= $view['id_clearance']; ?>">
                        <button class="btn btn-danger"  style="width: 90px; font-size: 17px; border-radius:30px;" type="submit" name="delete_clearance"> Archive </button>
                        <button type="button" class="btn btn-info btn-sm text-white" style="width:110px;font-size:17px;border-radius:30px;" data-toggle="modal" data-target="#messageModal<?= $view['id_resident'] ?>_<?= $view['id_clearance'] ?>" data-bs-toggle="modal" data-bs-target="#messageModal<?= $view['id_resident'] ?>_<?= $view['id_clearance'] ?>">
                            <i class="fas fa-comment-alt"></i> Message
                        </button>
                    </form>
                </td>
                <td> <?= $view['id_resident'];?> </td>
                <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?>. </td>
                <td> <?= $view['purpose'];?> </td>
                <td> <?= $view['houseno'];?>, <?= $view['street'];?>, <?= $view['brgy'];?>, <?= $view['municipal'];?> </td>
                <td> <?= $view['status'];?> </td>
                <td> <?= $view['age'];?> </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php foreach ($rows as $view): ?>
    <div class="modal fade" id="messageModal<?= $view['id_resident'] ?>_<?= $view['id_clearance'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <input type="hidden" name="id_resident" value="<?= $view['id_resident'] ?>">
                        <input type="hidden" name="redirect_to" value="admn_brgyclearance.php">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius:30px;">Cancel</button>
                        <button type="submit" name="send_msg" class="btn btn-info text-white" style="border-radius:30px;width:120px;">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php
	}else{
	    // Preserve the original list — don't let the loops below overwrite $view
	    $clearance_list = is_array($view) ? $view : [];
?>

<div class="table-responsive" style="width: 100%; overflow-x: auto;">
    <table class="table table-hover text-center table-bordered" style="min-width: 1000px;">
        <thead class="alert-info">
        <tr>
            <th> Actions</th>
            <th> Resident ID </th>
            <th> Full Name </th>
            <th> Purpose </th>
            <th> Address </th>
            <th> Status </th>
            <th> Age </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clearance_list as $view): ?>
                <tr>
                    <td>
                        <form action="" method="post">
                            <a class="btn btn-success" target="blank" style="width: 90px; font-size: 17px; border-radius:30px; margin-bottom: 2px;" href="brgyclearance_form.php?id_resident=<?= $view['id_resident'];?>">Generate</a>
                            <input type="hidden" name="id_clearance" value="<?= $view['id_clearance']; ?>">
                            <button class="btn btn-danger"  style="width: 90px; font-size: 17px; border-radius:30px;" type="submit" name="delete_clearance"> Archive </button>
                            <button type="button" class="btn btn-info btn-sm text-white" style="width:110px;font-size:17px;border-radius:30px;" data-toggle="modal" data-target="#messageModal<?= $view['id_resident'] ?>_<?= $view['id_clearance'] ?>" data-bs-toggle="modal" data-bs-target="#messageModal<?= $view['id_resident'] ?>_<?= $view['id_clearance'] ?>">
                                <i class="fas fa-comment-alt"></i> Message
                            </button>
                        </form>
                    </td>
                    <td> <?= $view['id_resident'];?> </td>
                    <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?>. </td>
                    <td> <?= $view['purpose'];?> </td>
                    <td> <?= $view['houseno'];?>, <?= $view['street'];?>, <?= $view['brgy'];?>, <?= $view['municipal'];?> </td>
                    <td> <?= $view['status'];?> </td>
                    <td> <?= $view['age'];?> </td>
                </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php foreach ($clearance_list as $view): ?>
        <div class="modal fade" id="messageModal<?= $view['id_resident'] ?>_<?= $view['id_clearance'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
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
                            <input type="hidden" name="id_resident" value="<?= $view['id_resident'] ?>">
                        <input type="hidden" name="redirect_to" value="admn_brgyclearance.php">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius:30px;">Cancel</button>
                            <button type="submit" name="send_msg" class="btn btn-info text-white" style="border-radius:30px;width:120px;">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php endforeach; ?>

<?php
	}
$con = null;
?>