<?php 
  include './config/connection.php';
  
  session_destroy();
  
  header("Location:index.php");
?>