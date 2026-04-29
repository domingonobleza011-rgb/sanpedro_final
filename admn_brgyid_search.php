<?php
	// require the database connection
	require 'classes/conn.php';
	if(isset($_POST['search_brgyid'])){
		$keyword = $_POST['keyword'];
?>
<table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
        <thead class="alert-info">
        
        <tr>
            <th> Actions</th>
            <th> Resident ID </th>
            <th> Full Name </th>
            <th> Address </th>
            <th> Birth Date </th>
            <th> Birth Place </th>
            <th> Contact Number </th>
            <th> Emergency Contact Person </th>
            <th> Emergency Contact Number </th>
            <th>Relation</th>
        </tr>
    </thead>

    <tbody> 
        <?php
            
            $stmnt = $conn->prepare("SELECT * FROM `tbl_brgyid` WHERE `lname` LIKE '%$keyword%' or  `mi` LIKE '%$keyword%' or  `fname` LIKE '%$keyword%' 
            or `brgyid` LIKE '%$keyword%' or  `id_resident` LIKE '%$keyword%' or  `houseno` LIKE '%$keyword%' or  `street` LIKE '%$keyword%'
            or `brgy` LIKE '%$keyword%' or `municipal` LIKE '%$keyword%' or `industry` LIKE '%$keyword%' or `aoe` LIKE '%$keyword%' ");
            $stmnt->execute();
            
            while($view = $stmnt->fetch()){
        ?>
            <tr>
                <td>    
                    <form action="" method="post">
                        <a class="btn btn-success" target="blank" style="width: 90px; font-size: 17px; border-radius:30px; margin-bottom: 2px;" href="barangayid_form.php?id_resident=<?= $view['id_resident'];?>">Generate</a> 
                        <input type="hidden" name="id_brgyid" value="<?= $view['id']; ?>">
                        <button class="btn btn-danger" style="width: 90px; font-size: 17px; border-radius:30px;" type="submit" name="delete_brgyid"> Delete </button>
                    </form>
                </td>
                <td> <?= $view['id_resident'];?> </td> 
                <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?></td>
                <td> <?= $view['houseno'];?>, <?= $view['street'];?>, <?= $view['brgy'];?>, <?= $view['municipal'];?> </td>
                <td> <?= $view['bdate'];?> </td>
                <td> <?= $view['bplace'];?> </td>
                <td> <?= $view['contact'];?> </td>
                <td> <?= $view['inc_lname'];?>, <?= $view['inc_fname'];?> </td>
                <td> <?= $view['inc_contact'];?> </td>
                <td> <?= $view['relation'];?> </td>
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
            <th> Birth Date </th>
            <th> Birth Place </th>
            <th> Contact Number </th>
            <th> Emergency Contact Person </th>
            <th> Emergency Contact Number </th>
            <th>Relation</th>
        </tr>
    </thead>
    
    <tbody>
        <?php if(is_array($view)) {?>
            <?php foreach($view as $view) {?>
                <tr>
                    <td>    
                        <form action="" method="post">
                            <a class="btn btn-success" target="blank" style="width: 90px; font-size: 17px; border-radius:30px; margin-bottom: 2px;" href="barangayid_form.php?id_resident=<?= $view['id_resident'];?>">Generate</a> 
                            <input type="hidden" name="id_brgyid" value="<?= $view['id_brgyid']; ?>">
                            <button class="btn btn-danger" style="width: 90px; font-size: 17px; border-radius:30px;" type="submit" name="delete_brgyid"> Delete </button>
                        </form>
                    </td>
                                    <td> <?= $view['id_resident'];?> </td> 
                <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?></td>
                <td> <?= $view['houseno'];?>, <?= $view['street'];?>, <?= $view['brgy'];?>, <?= $view['municipal'];?> </td>
                <td> <?= $view['bdate'];?> </td>
                <td> <?= $view['bplace'];?> </td>
                <td> <?= $view['contact'];?> </td>
                <td> <?= $view['inc_lname'];?>, <?= $view['inc_fname'];?> </td>
                <td> <?= $view['inc_contact'];?> </td>
                <td> <?= $view['relation'];?> </td>
                </tr>
            <?php
                }
            ?>
        <?php
            }
        ?>
    </tbody>

</table>

<?php
	}
$con = null;
?>