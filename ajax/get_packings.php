<?php 
	include '../config/connection.php';

  	$medicineId = $_GET['medicine_id'];

  	$query = "SELECT `id`, `packing` from `medicine_details` 
  	where `medicine_id` = $medicineId;";

  	$packings = '<option value="">Select Packing</option>';

  	try {
  		$stmt = $con->prepare($query);
  		$stmt->execute();

  		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  			 $packings = $packings.'<option value="'.$row['id'].'">'.$row['packing'].'</option>';
  		}

  	} catch(PDOException $ex) {
  		echo $ex->getTraceAsString();
  		exit;
  	}

  	echo $packings;
?>