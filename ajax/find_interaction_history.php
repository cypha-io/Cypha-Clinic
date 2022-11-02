<?php 
  include '../config/connection.php';

  $userId = $_GET['user_id'];
  $from = $_GET['from'];
  $to = $_GET['to'];

   $arr = explode("/", $from);
   $from = $arr[2].'-'.$arr[0].'-'.$arr[1];

   $arr = explode("/", $to);
   $to = $arr[2].'-'.$arr[0].'-'.$arr[1];

  $from = $from.' 00:00:00';
  $to = $to.' 23:59:59';

  $query = "select ifnull(`insertion_date_time`, '') as 
`insertion_date_time` ,date_format(`insertion_date_time`,
 '%H:%i:%s') , `description` 
from `interaction_histories` 
where `user_id` = $user_id and 
`insertion_date_time` between '$from' and '$to' 
order by `insertion_date_time` asc;
";

  $stmt = $con->prepare($query);
  $stmt->execute();

  $data = '';

$serial = 0;
  while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $serial++;
      $data =  $data .'<tr>';
      $data =  $data .'<td>'.$serial.'</td>';
      $data =  $data .'<td>'.$r['insertion_date_time'].'</td>';
  
      $data =  $data .'<td>'.$r['description'].'</td>';
     
      $data =  $data .'</tr>';
  }

echo $data;
?>





