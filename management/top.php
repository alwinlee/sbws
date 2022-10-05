<?php
ini_set("error_reporting", 0);
ini_set("display_errors","Off"); // On : open, Off : close	
session_start();
require_once("../_res/_inc/connMysql.php");
require_once("../_res/_inc/sharelib.php");
?>
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=utf-8"><META name="GENERATOR" content="">
</HEAD>
<BODY>
<table style="background: rgb(255, 255, 255); margin: 0px auto; width: 1046px; height: 32px; table-layout: fixed;"><tbody>
  <tr style="height: 50px;">
	<td align="right" style="width:40px;text-decoration:none;"><a title="" style="text-decoration:none;" href="main.php"><img alt="系統管理" src="../_res/img/home.png"></a></td>	
	<td align="left" style="width:80px;text-decoration:none;"><a title="" style="text-decoration:none;" href="main.php"><font color="#0000ff">&nbsp;系統管理&nbsp;&nbsp;</font></a></td>	
	
	<td align="right" style="width:40px"><img alt="今天日期" src="../_res/img/clock.png"></td>
	
	<td align="left" style="width:120px"><font color="#0000ff">&nbsp<?php echo date(Y)."年".date(m)."月".date(d)."日&nbsp;&nbsp;&nbsp;";?></font></td>
	
	<td align="right" style="width:40px"><img alt="登入者" src="../_res/img/user.png"></td>
	<td align="left" style="width:260px"><font color="#0000ff">&nbsp<?php echo  $_SESSION["username"]." (".$_SESSION["user"].")";?></font></td>
	
    <td style="width:320px" align="left"><A href=""><img style="margin: 0px; border: 0px currentColor; border-image: none; text-align: left;" alt="" src=""></A></td>    
	<td align="right" style="width:100px;text-decoration:none;"><a style="width:20;text-decoration:none;" title="" class="" href="logout.php"><font color="#0000ff">登出</font></a></td>
	<td align="left" >&nbsp;&nbsp;<a title="" href="logout.php"><img alt="登出" src="../_res/img/logout.png"></a></td>
  </tr>  
  <tr><td colspan="9"><hr class="hr5"></td></tr>
  
</tbody></table>
</BODY>
</HTML>
