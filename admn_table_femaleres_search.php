<?php
	// require the database connection
	require 'classes/conn.php';

	if(isset($_POST['search_femaleres'])){
	$keyword = $_POST['keyword'];

   
   
?>
<table class="table table-hover text-center table-bordered" style="min-width: 1000px;"> 
        <thead class="alert-info">
        <tr>
            
            <th> Surname </th>
            <th> First name </th>
            <th> Middle name </th>
            <th> Age </th>
            <th> Sex </th>
            <th> Status </th>
            <th> House No. </th>
            <th> Street </th>
            <th> Barangay </th>
            <th> Municipality </th>
            <th> Contact </th>
            <th> Birth date </th>
            <th> Birth place </th>
            <th> Nationality </th>
        </tr>
    </thead>

    <tbody>
        <?php
            $keyword = '%' . $keyword . '%';
            $stmnt = $conn->prepare("SELECT * FROM `tbl_resident` WHERE `lname` LIKE ? or  `mi` LIKE ? or  `fname` LIKE ? 
            or  `age` LIKE ? or  `sex` LIKE ? or  `status` LIKE ? or  `address` LIKE ? or  `contact` LIKE ?
            or  `bdate` LIKE ? or  `bplace` LIKE ? or  `nationality` LIKE ? or  `family_role` LIKE ? or  `role` LIKE ? or  `email` LIKE ?");
            $stmnt->execute([$keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword]);
            
            while($view = $stmnt->fetch()){
        ?>
            <tr>
            
                <td> <?= $view['lname'];?> </td>
                <td> <?= $view['fname'];?> </td>
                <td> <?= $view['mi'];?> </td>
                <td> <?= $view['age'];?> </td>
                <td> <?= $view['sex'];?> </td>
                <td> <?= $view['status'];?> </td>
                <td> <?= $view['houseno'];?> </td>
                <td> <?= $view['street'];?> </td>
                <td> <?= $view['brgy'];?> </td>
                <td> <?= $view['municipal'];?> </td>
                <td> <?= $view['contact'];?> </td>
                <td> <?= $view['bdate'];?> </td>
                <td> <?= $view['bplace'];?> </td>
                <td> <?= $view['nationality'];?> </td>
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
           
            <th> Surname </th>
            <th> First name </th>
            <th> Middle name </th>
            <th> Age </th>
            <th> Sex </th>
            <th> Status </th>
            <th> House No.</th>
            <th> Street </th>
            <th> Barangay </th>
            <th> Contact </th>
            <th> Birth date </th>
            <th> Birth place </th>
            <th> Nationality </th>
        </tr>
    </thead>

    <tbody>
        <?php if(is_array($view)) {?>
            <?php foreach($view as $resident) {?>
                <tr>
                   
                    <td> <?= $resident['lname'];?> </td>
                    <td> <?= $resident['fname'];?> </td>
                    <td> <?= $resident['mi'];?> </td>
                    <td> <?= $resident['age'];?> </td>
                    <td> <?= $resident['sex'];?> </td>
                    <td> <?= $resident['status'];?> </td>
                    <td> <?= $resident['houseno'];?> </td>
                    <td> <?= $resident['street'];?> </td>
                    <td> <?= $resident['brgy'];?> </td>
                    <td> <?= $resident['contact'];?> </td>
                    <td> <?= $resident['bdate'];?> </td>
                    <td> <?= $resident['bplace'];?> </td>
                    <td> <?= $resident['nationality'];?> </td>
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