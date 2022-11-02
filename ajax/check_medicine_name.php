<?php 
	include '../config/connection.php';

  $medicineName = $_GET['medicine_name'];

  $query = "select count(*) as `count` from `medicines` 
	where `medicine_name` = '$medicineName';";
  $stmt = $con->prepare($query);
  $stmt->execute();

$r = $stmt->fetch(PDO::FETCH_ASSOC);
  $count = $r['count'];
  
  echo $count;

?>