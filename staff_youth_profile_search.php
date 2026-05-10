<?php
	// require the database connection
	require 'classes/conn.php';
	if(isset($_POST['search_certofres'])){
		$keyword = $_POST['keyword'];
?>
<div class="table-responsive" style="width: 100%; overflow-x: auto;">
    <table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
        <thead class="alert-info">
            <tr>
                 <th>Actions</th>
                <th>Youth ID</th>
                <th>FullName</th>
                <th>Age</th>
                <th>Sex</th>
                <th>status</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Education</th>
                <th>Employed</th>
                <th>Skills</th>
            </tr>
        </thead>
        <tbody>
            </tbody>
    </table>
</div>

    <tbody>    
        <?php
            $stmnt = $conn->prepare("SELECT * FROM `tbl_youth` WHERE `lname` LIKE '%$keyword%'  or  `fname` LIKE '%$keyword%' 
            or `age` LIKE '%$keyword%' or  `id_youth` LIKE '%$keyword%' or  `sex` LIKE '%$keyword%' or  `civil_status` LIKE '%$keyword%'
            or `contact_number` LIKE '%$keyword%' or `email_address` LIKE '%$keyword%' or `educ_attain` LIKE '%$keyword%' or `emp_status` LIKE '%$keyword%' or `skill_name` LIKE '%$keyword%'");
            $stmnt->execute();
            
            while($view = $stmnt->fetch()){
        ?>
            <tr>
                <td>    
                    <form action="" method="post"> 
                        <input type="hidden" name="id_youth" value="<?= $view['id_youth'];?>">
                        <button class="btn btn-danger" type="submit" style="width: 90px; font-size: 17px; border-radius:30px;" name="delete_youth"> Delete </button>
                    </form>
                </td>
                <td> <?= $view['id_youth'];?> </td> 
                <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?> </td>
                <td> <?= $view['age'];?> </td>
                <td> <?= $view['sex'];?> </td>
                <td> <?= $view['civil_status'];?> </td>
                <td> <?= $view['contact_number'];?> </td>
                <td> <?= $view['email_address'];?> </td>
                <td> <?= $view['educ_attain'];?> </td>
                <td> <?= $view['emp_status'];?> </td>
                <td> <?= $view['skill_name'];?> </td>

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
                <th>Actions</th>
                <th>Youth ID</th>
                <th>FullName</th>
                <th>Age</th>
                <th>Sex</th>
                <th>status</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Education</th>
                <th>Employed</th>
                <th>Skills</th>
            </tr>
        </thead>
        <tbody>
            </tbody>
    
</div>


		<tbody>
		    <?php if(is_array($view)) {?>
                <?php foreach($view as $view) {?>
                    <tr>
                <td>    
                    <form action="" method="post">
                        <input type="hidden" name="id_youth" value="<?= $view['id_youth'];?>">
                        <button class="btn btn-danger" type="submit" style="width: 90px; font-size: 17px; border-radius:30px;" name="delete_youth"> Delete </button>
                    </form>
                </td>
                <td> <?= $view['id_youth'];?> </td> 
                <td> <?= $view['lname'];?>, <?= $view['fname'];?> <?= $view['mi'];?> </td>
                <td> <?= $view['age'];?> </td>
                <td> <?= $view['sex'];?> </td>
                <td> <?= $view['civil_status'];?> </td>
                <td> <?= $view['contact_number'];?> </td>
                <td> <?= $view['email_address'];?> </td>
                <td> <?= $view['educ_attain'];?> </td>
                <td> <?= $view['emp_status'];?> </td>
                <td> <?= $view['skill_name'];?> </td>

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