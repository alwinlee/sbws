<?php 
    header("Content-Type: text/html; charset=utf-8");
    session_start();

    require_once("../_res/_inc/login_check.php");
    require_once("../_res/_inc/connMysql.php"); 
    require_once("../_res/_inc/sharelib.php");
?>

<!DOCTYPE HTML>
<HTML lang="en"><HEAD><META content="IE=11.0000" http-equiv="X-UA-Compatible">
<TITLE>南區學員點名系統-系統管理</TITLE>     
<META name="keywords" content=""> 
<META name="description" content=""> 
<META http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">     
<META name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon" href="../_res/img/icons.ico">  
<LINK href="../_res/css/site.css" rel="stylesheet" media="screen">   
<LINK href="../_res/css/stdtheme.css" rel="stylesheet" media="screen">   
<LINK href="../_res/css/bwsouth.css" rel="stylesheet" media="screen"> 

<SCRIPT src="../_res/js/analytics.js" type="text/javascript"></SCRIPT>
<SCRIPT src="../_res/js/format.js" type="text/javascript"></SCRIPT>     
<SCRIPT src="../_res/js/jquery-2.1.1.min.js" type="text/javascript"></SCRIPT> 
<SCRIPT src="../_res/js/js.js" type="text/javascript"></SCRIPT>
<SCRIPT src="../_res/js/toggle.js" type="text/javascript"></SCRIPT>

<SCRIPT src="./js/main.js?{F017A570-2E23-4359-BF3C-E9E58038C23E}" type="text/javascript"></SCRIPT>

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
<TD align="left" class="navigation" id="navigationTree" valign="top"><?php include("./inc/menu.php");?></TD>

<TD class="content" id="demos" valign="top">
<TABLE style="vertical-align: top; border-collapse: collapse;" cellspacing="0" cellpadding="0">
<TR>
<TD valign="top">
<DIV class="demoContainer" id="demoContainer">
<br>
<?php




?>
                                          
</TD>
</TR>
</TABLE></TD>
</TR>

</TABLE>
</TD></TR></TABLE></TD></TR>
</TABLE>
</BODY></HTML>
