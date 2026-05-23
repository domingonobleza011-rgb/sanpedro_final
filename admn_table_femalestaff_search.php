<?php
	// require the database connection
	require 'classes/conn.php';
	if(isset($_POST['search_totalstaff'])){
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
			<th> Address </th>
			<th> Contact # </th>
			<th> Position </th>
		</tr>
	</thead>

	<tbody>     
		<?php
			$stmnt = $conn->prepare("SELECT * FROM `tbl_user` WHERE `lname` LIKE ? or  `mi` LIKE ? or  `fname` LIKE ? 
			or `age` LIKE ? or  `sex` LIKE ? or  `address` LIKE ? or  `contact` LIKE ?
			or `email` LIKE ?");
			$keyword_param = "%$keyword%";
			$stmnt->execute([$keyword_param, $keyword_param, $keyword_param, $keyword_param, $keyword_param, $keyword_param, $keyword_param, $keyword_param]);
			
			while($view = $stmnt->fetch()){
		?>
			<tr>
			
				<td> <?= $view['lname'];?> </td>
				<td> <?= $view['fname'];?> </td>
				<td> <?= $view['mi'];?> </td>
				<td> <?= $view['age'];?> </td>
				<td> <?= $view['sex'];?> </td>
                <td> <?= $view['address'];?> </td>
				<td> <?= $view['contact'];?> </td>
				<td> <?= $view['position'];?> </td>
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
			<th> Address </th>
			<th> Contact # </th>
			<th> Position </th>
		</tr>
	</thead>

	<tbody>
		<?php if(is_array($view)) {?>
			<?php foreach($view as $row) {?>
				<tr>
					
					<td> <?= $row['lname'];?> </td>
					<td> <?= $row['fname'];?> </td>
					<td> <?= $row['mi'];?> </td>
					<td> <?= $row['age'];?> </td>
					<td> <?= $row['sex'];?> </td>
					<td> <?= $row['address'];?> </td>
					<td> <?= $row['contact'];?> </td>
					<td> <?= $row['position'];?> </td>
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