<?php
  session_start();
  require "db_connection.php";
  
  if(isset($_SESSION['user_id']))
    {
      header("Location: dashboard.php");
      exit();
    }

    else{
        header("Location: login.php");
        exit();
    }

?>