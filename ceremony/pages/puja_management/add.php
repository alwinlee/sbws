<?php 
	//header("Content-Type: text/html; charset=utf-8");
	//session_start();
	
	require_once("../../../_res/_inc/connMysql.php"); 
	require_once("../../../_res/_inc/sharelib.php");
      require_once("../../inc/ceremonylib.php");
      
	ini_set("error_reporting", 0);
      ini_set("display_errors","Off"); // On : open, Off : close	
	date_default_timezone_set('Asia/Taipei');
	
	$traffictb=$_POST['traffictb'];
	$trafficid=$_POST['trafficid'];
	$trafficName=$_POST['trafficName'];
	$trafficday=$_POST['trafficday'];
	$traffdesc=$_POST['traffdesc'];
    
      $trafficDesc="*";
      if(isset($_POST['traffdesc']))
      {
          $trafficDesc=$_POST['traffdesc'];
          if($trafficDesc==""){$trafficDesc="*";}
      }      

      chkPujaTraffTB($traffictb);
	$sql="select * from `".$traffictb."` WHERE (`traffid`='".$trafficid."' AND `day`=".$trafficday." AND `traffdesc`='".$trafficDesc."');";
      $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
	$sql_result=mysqli_query($con, $sql);
	$numrows=mysqli_num_rows($sql_result);
	if ($numrows>=1){
		mysqli_close($con);
		echo "-1";exit;
	}
	
	$sql="INSERT INTO `".$traffictb."` VALUES(NULL,'".$trafficid."',".$trafficday.",'".$trafficName."','".$trafficDesc."',0,0,0,0,'06:00','06:00')";		
	$sql_result = mysqli_query($con,$sql);

      echo $sql_result;
?>

