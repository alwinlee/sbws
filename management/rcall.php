<?php 
	header("Content-Type: text/html; charset=utf-8");
	session_start();  

	ini_set("error_reporting", 0);
	ini_set("display_errors","Off"); // On : open, Off : close
	error_reporting(E_ALL & ~E_NOTICE);

	require_once("../_res/_inc/login_check.php");//檢查是否已登入，若未登入則導回首頁
	require_once("../_res/_inc/connMysql.php"); 
	require_once("../_res/_inc/sharelib.php");

	date_default_timezone_set('Asia/Taipei');
	$userlevel=$_SESSION["userlevel"];
	$systemAuth=$_SESSION["systemAuth"];
?>

<!DOCTYPE HTML>
<HTML lang="en"><HEAD><META content="IE=11.0000" http-equiv="X-UA-Compatible">
<TITLE>南區學員點名系統-點名管理</TITLE> 

<META name="keywords" content=""> 
<META name="description" content=""> 
<META http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">     
<META name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon" href="../_res/img/icons.ico">  
<LINK href="../_res/css/site.css" rel="stylesheet" media="screen">   
<LINK href="../_res/css/stdtheme.css" rel="stylesheet" media="screen">   
<LINK href="../_res/css/bwsouth.css" rel="stylesheet" media="screen"> 
<link href="../_res/css/fixtblTheme.css"      rel="stylesheet" media="screen"/>

<SCRIPT src="../_res/js/analytics.js" type="text/javascript"></SCRIPT>
<SCRIPT src="../_res/js/jquery-2.1.1.min.js" type="text/javascript"></SCRIPT> 
<SCRIPT src="../_res/js/jquery.fixedheadertable.js"></SCRIPT>	
<SCRIPT src="../_res/js/datetime.js" type="text/javascript"></SCRIPT> 
<SCRIPT src="./js/rcall.js?{F017A570-2E23-4359-BF3C-E9E58038C23E}" type="text/javascript"></SCRIPT> 	

<META name="GENERATOR" content=""></HEAD> 
<BODY>
<DIV class="top" id="pageTop"></DIV>
<DIV class="rc-all contentdiv">

<TABLE>

<TR valign="top">
<TD align="left">
<TABLE align="left" class="contenttable" cellspacing="0" cellpadding="0">
<TR valign="top">
<TD>
<TABLE style="border-collapse: collapse;" cellspacing="0" cellpadding="0">
<TR>

<TD align="left" class="navigation" id="navigationTree" valign="top"><?php $subMenuItem=200; include("inc\menu.php");?></TD>
				  
<TD class="content" id="demos" valign="top">
<h3 align="center" > <font color="#0000ff">點名管理</font></h2>
<TABLE style="vertical-align: top; border-collapse: collapse;" cellspacing="0" cellpadding="0">
<TR><TD valign="top" align="center">

<div class="demoContainer" id="demoContainer">
<br>
<hr>
<?php
    if ($systemAuth[0]<=0){exit;}
    if ($systemAuth[2]<=0){exit;}
    echo "<br><br>";
    echo "<button id='updateqx' class='updateqx'>更新 qx</button>";

    $currDate = date('Y-m-d');//$startM=GetStartQMonth();$endM=$startM+2;$currDate = date('Y-m-d');	
    echo "<input type=\"hidden\" id=\"classroomtb\" class=\"membertb\" name=\"membertb\" value=\"member\" />";
    echo "<input type=\"hidden\" id=\"currentdate\" name=\"currentdate\" value=\"".$currDate."\" />";		
?>	
</TD>
</TR>
</TABLE></TD>
</TR>

</TABLE>
</TD></TR></TABLE></TD></TR>
</TABLE>

</BODY></HTML>
