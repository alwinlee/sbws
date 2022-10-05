<?php

require_once("../_res/_inc/connMysql.php");
require_once("../_res/_inc/sharelib.php");

date_default_timezone_set('Asia/Taipei');
$user_level=$_SESSION["userlevel"];
$systemAuth=$_SESSION["systemAuth"];
if ($systemAuth[0]<=0){exit;}

echo "<table>";
echo "<tr><td colspan='2'>&nbsp;</td></tr>";
//------------------------------------------------------------------------------------------------------------------------------------------------------------------
echo "<tr>";

if ($systemAuth[1]>=1)
{
	if (isset($subMenuItem) && $subMenuItem == 100)
	{
		echo "<td style=\"width:42px;text-align:center;\"><A style=\"text-decoration:none;color: rgb(63, 63, 63);\" title=\"\" href=\"account.php\">&nbsp;&nbsp;<img alt=\"權限管理\" src=\"../_res/img/accounten.png\"></a></td>";
		echo "<td style=\"width:140px;text-align:left;\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"account.php\">權限管理</A></td>";
	}else{
		echo "<td style=\"width:42px;text-align:center;\"><A style=\"text-decoration:none;color: rgb(63, 63, 63);\" title=\"\" href=\"account.php\">&nbsp;&nbsp;<img alt=\"權限管理\" src=\"../_res/img/account.png\"></a></td>";
		echo "<td style=\"width:140px;text-align:left;\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"account.php\">權限管理</A></td>";
	}	
	echo "</tr>";	
	echo "<tr><td colspan='2'><hr></td></tr>";
}

if ($systemAuth[2]>=1)
{
	if (isset($subMenuItem) && $subMenuItem == 200)
	{
		echo "<td style=\"width:42px;text-align:center;\"><A style=\"text-decoration:none;color: rgb(63, 63, 63);\" title=\"\" href=\"rcall.php\">&nbsp;&nbsp;<img alt=\"點名管理\" src=\"../_res/img/rollcallsen.png\"></a></td>";
		echo "<td style=\"width:140px;text-align:left;\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"rcall.php\">點名管理</A></td>";
	}else{
		echo "<td style=\"width:42px;text-align:center;\"><A style=\"text-decoration:none;color: rgb(63, 63, 63);\" title=\"\" href=\"rcall.php\">&nbsp;&nbsp;<img alt=\"點名管理\" src=\"../_res/img/rollcalls.png\"></a></td>";
		echo "<td style=\"width:140px;text-align:left;\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"rcall.php\">點名管理</A></td>";
	}	
	echo "</tr>";	
	echo "<tr><td colspan='2'><hr></td></tr>";
}

if ($systemAuth[3]>=1)
{
	if (isset($subMenuItem) && $subMenuItem == 300)
	{
		echo "<td style=\"width:42px;text-align:center;\"><A style=\"text-decoration:none;color: rgb(63, 63, 63);\" title=\"\" href=\"ceremony.php\">&nbsp;&nbsp;<img alt=\"法會管理\" src=\"../_res/img/ceremonyen.png\"></a></td>";
		echo "<td style=\"width:140px;text-align:left;\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"ceremony.php\">法會管理</A></td>";
	}else{
		echo "<td style=\"width:42px;text-align:center;\"><A style=\"text-decoration:none;color: rgb(63, 63, 63);\" title=\"\" href=\"ceremony.php\">&nbsp;&nbsp;<img alt=\"法會管理\" src=\"../_res/img/ceremony.png\"></a></td>";
		echo "<td style=\"width:140px;text-align:left;\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"ceremony.php\">法會管理</A></td>";
	}	
	echo "</tr>";	
	echo "<tr><td colspan='2'><hr></td></tr>";
}

if ($systemAuth[4]>=1)
{
	if (isset($subMenuItem) && $subMenuItem == 400)
	{
		echo "<td style=\"width:42px;text-align:center;\"><A style=\"text-decoration:none;color: rgb(63, 63, 63);\" title=\"\" href=\"student.php\">&nbsp;&nbsp;<img alt=\"異動管理\" src=\"../_res/img/adduseren.png\"></a></td>";
		echo "<td style=\"width:140px;text-align:left;\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"student.php\">異動管理</A></td>";
	}else{
		echo "<td style=\"width:42px;text-align:center;\"><A style=\"text-decoration:none;color: rgb(63, 63, 63);\" title=\"\" href=\"student.php\">&nbsp;&nbsp;<img alt=\"異動管理\" src=\"../_res/img/adduser.png\"></a></td>";
		echo "<td style=\"width:140px;text-align:left;\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"student.php\">異動管理</A></td>";
	}	
	echo "</tr>";	
	echo "<tr><td colspan='2'><hr></td></tr>";
}

echo "<tr><td>&nbsp;</td><td></td></tr>";
echo "<tr><td>&nbsp;</td><td></td></tr>";
echo "<tr><td colspan='2'><hr></td></tr>";

//------------------------------------------------------------------------------------------------------------------------------------------------------------------
echo "<tr>";
echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"前往學員點名\" src=\"../_res/img/link.png\"></td>";

if (isset($subMenuItem) && $subMenuItem == 4000)  
	echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"../rollcall/main.php\">前往學員點名</A></td>";
else
	echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"../rollcall/main.php\">前往學員點名</A></td>";
	
echo "</tr>";
echo "<tr><td colspan='2'><hr></td></tr>";	
//------------------------------------------------------------------------------------------------------------------------------------------------------------------
echo "<tr>";
echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"前往法會報名\" src=\"../_res/img/link.png\"></td>";

if (isset($subMenuItem) && $subMenuItem == 5000)  
	echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"../ceremony/main.php\">前往法會報名</A></td>";
else
	echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"../ceremony/main.php\">前往法會報名</A></td>";
	
echo "</tr>";
echo "<tr><td colspan='2'><hr></td></tr>";	

//------------------------------------------------------------------------------------------------------------------------------------------------------------------
echo "<tr>";
echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"前往學員異動\" src=\"../_res/img/link.png\"></td>";

if (isset($subMenuItem) && $subMenuItem == 6000)  
	echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"../student/main.php\">前往學員異動</A></td>";
else
	echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"../student/main.php\">前往學員異動</A></td>";
	
echo "</tr>";
echo "<tr><td colspan='2'><hr></td></tr>";	

echo "</table>";
?>


