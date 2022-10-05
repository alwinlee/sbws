<?php 
	require_once("../../../_res/_inc/connMysql.php"); 
	require_once("../../../_res/_inc/sharelib.php");
	require_once("../../inc/ceremonylib.php");	
	ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close	
	
	// 取得送過來的考生報名資料
	$classname=$_POST['pujaname'];
	$pujaid=$_POST['pujaid'];	
	$tbname=$_POST['tbname'];

	$data="";
    
	// Find traffic db 
	$sql="select * from `".$tbname."` WHERE `idx`=".$pujaid."";
	$con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
	$sql_result=mysqli_query($con, $sql);
	$numrows=mysqli_num_rows($sql_result);		
	if ($numrows <= 0){echo "0";exit;}

	$row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC);
	$tbtraffic=$row["traffdbname"];
	
    chkPujaTraffTB($tbtraffic);
	$sql="SHOW TABLES LIKE '".$tbtraffic."'";
	$sql_result=mysqli_query($con, $sql);
	$numrows = mysqli_num_rows($sql_result);
	if ($numrows<=0){echo "-1";exit;}	
	
	
	// find traffic item sort by day and id
	$data.=$tbtraffic.";";	
	$sql="select * from `".$tbtraffic."` ORDER BY `day` ASC, `traffid` ASC";
	$sql_result=mysqli_query($con, $sql);
	$numrows=mysqli_num_rows($sql_result);
	if ($numrows <= 0){echo $data;exit;}
	
	while($row = mysqli_fetch_array($sql_result, MYSQLI_NUM))//MYSQL_NUM))//MYSQL_ASSOC))
	{		
		$data.=($row[0]."|".$row[1]."|".$row[2]."|".$row[3]."|".$row[4]."|".$row[5]."|".$row[6]."|".$row[7]);
	    $data.=";";
	}
	
	$result=$data;
	echo $result;		
?>

