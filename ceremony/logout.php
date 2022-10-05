<?php 
  session_start();
  //執行登出動作，將SESSION資料清除，並重導回首頁
  unset($_SESSION["username"]);  
  unset($_SESSION["keyofclassroom"]);
  unset($_SESSION["keyofarea"]);
  unset($_SESSION["keyofareaid"]);
  unset($_SESSION["userlevel"]);
  unset($_SESSION["username"]);
  unset($_SESSION["location"]);
  header("Location: ../index.php");
?>