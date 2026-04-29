<?php
	// require the database connection
	require 'classes/conn.php';
	if(isset($_POST['search_bspermit'])){
		$keyword = $_POST['keyword'];
?>
	<table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
        <thead class="alert-info">
            
			<tr>
                <th> Actions</th>
                <th> Resident ID </th>
                <th> Full Name </th>
                <th> Address </th>
                <th> Contact # </th>
                <th> Narrative Report </th>
                <th> Date & Time Applied</th> 
			</tr>
		</thead>
		<tbody>
		
                    
			<?php
				
				$stmnt = $conn->prepare("SELECT * FROM `tbl_bspermit` WHERE `lname` LIKE '%$keyword%' or  `mi` LIKE '%$keyword%' or  `fname` LIKE '%$keyword%' 
				or `bsname` LIKE '%$keyword%' or  `id_resident` LIKE '%$keyword%' or  `houseno` LIKE '%$keyword%' or  `street` LIKE '%$keyword%'
				or `brgy` LIKE '%$keyword%' or `municipal` LIKE '%$keyword%' or `bsindustry` LIKE '%$keyword%' or `aoe` LIKE '%$keyword%' ");
				$stmnt->execute();
				
				while($view = $stmnt->fetch()){
			?>
			<tr>
            <td>    
                <form action="" method="post">
                    <button type="button" class="btn btn-success btn-sm" style="width: 90px; font-size: 17px; border-radius:30px;" data-toggle="modal" data-target="#viewModal<?= $row['id_resident'] ?>">
                    <i class="fa fa-eye"></i> View
                </button>
                    <input type="hidden" name="id_blotter" value="<?= $view['id_blotter'];?>">
                    <button class="btn btn-danger" style="width: 90px; font-size: 17px; border-radius:30px;" type="submit" name="delete_blotter"> Archive </button>
                </form>
                </td>
                <td> <?= $view['id_resident'];?> </td> 
                <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?>. </td>
                <td> <?= $view['houseno'];?>, <?= $view['street'];?>, <?= $view['brgy'];?>, <?= $view['municipal'];?> </td>
                <td> <?= $view['contact'];?> </td>
                <td> <?= $view['narrative'];?> </td>
                <td> <?= $view['timeapplied'];?> </td>
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
                <th> Address </th>
                <th> Contact # </th>
                <th> Narrative Report </th>
                <th> Date & Time Applied</th> 
			</tr>
		</thead>
		<tbody>
		<?php if(isset($view) && is_array($view)) { ?>
            <?php foreach($view as $row) { ?>
            <tr>
                <td>        
                    <form action="" method="post">
                        <button type="button" class="btn btn-success btn-sm" style="width: 90px; font-size: 17px; border-radius:30px;" data-toggle="modal" data-target="#viewModal<?= $row['id_resident'] ?>">
                            <i class="fa fa-eye"></i> View
                        </button>
                        <input type="hidden" name="id_blotter" value="<?= $row['id_blotter'];?>">
                        <button class="btn btn-danger" style="width: 90px; font-size: 17px; border-radius:30px;" type="submit" name="delete_blotter"> Archive </button>
                    </form>
                </td>
                <td> <?= $row['id_resident'];?> </td> 
                <td> <?= $row['lname'];?>, <?= $row['fname'];?> <?= $row['mi'];?>. </td>
                <td> <?= $row['houseno'];?>,  <?= $row['street'];?>, <?= $row['brgy'];?>, <?= $row['municipal'];?> </td>
                <td> <?= $row['contact'];?> </td>
                <td> <?= $row['narrative'];?> </td> 
                <td> <?= $row['timeapplied'];?> </td>
            </tr>

            <div class="modal fade" id="viewModal<?= $row['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Blotter Information</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body text-left">
                            <p><strong>Resident ID:</strong> <br> <?= $row['id_resident'];?> </p>
                            <hr style="border: 2px solid green; opacity: 1;">
                            <h5><strong>Complainant Details</strong></h5>
                            <p><strong>Full Name:</strong> <?= $row['lname'] ?>, <?= $row['fname'] ?> <?= $row['mi'] ?>.</p>
                            <p><strong>Contact Number:</strong><br> <?= $row['contact'] ?></p>
                            <p><strong>Address:</strong><br> <?= $row['houseno'] ?>, <?= $row['street'] ?>, <?= $row['brgy'] ?>, <?= $row['municipal'] ?></p>
                            <p><strong>Date & Time Applied:</strong><br> <?= $row['timeapplied'];?></p>
                            <p><strong>Narrative:</strong><br> <?= $row['narrative'];?></p>
                            <hr style="border: 2px solid green; opacity: 1;">
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        <?php } ?>
		</tbody>
	</table>
<?php
	}
$con = null;
?>