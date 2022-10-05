<?php 
	//header("Content-Type: text/html; charset=utf-8");
	//session_start();
	
	require_once("../../../_res/_inc/connMysql.php"); 
	require_once("../../../_res/_inc/sharelib.php");
	
	ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close	
	date_default_timezone_set('Asia/Taipei');
	
	$traffictb=$_POST['traffictb'];
	$trafficid=$_POST['trafficid'];
	//$trafficName=$_POST['trafficName'];
	$trafficday=$_POST['trafficday'];
    $con = mysqli_connect("localhost","root","rinpoche","bwsouthdb");
	$sql="DELETE FROM `".$traffictb."` WHERE `traffid`='".$trafficid."' AND `day`=".$trafficday.";";	
	$sql_result=mysqli_query($con, $sql);	
    echo $sql_result;
?>

