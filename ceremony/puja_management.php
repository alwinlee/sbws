<?php
    header("Content-Type: text/html; charset=utf-8");
    session_start();

    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close
    error_reporting(E_ALL & ~E_NOTICE);

    require_once("../_res/_inc/login_check.php");//檢查是否已登入，若未登入則導回首頁
    require_once("../_res/_inc/connMysql.php");
    require_once("../_res/_inc/sharelib.php");
    require_once("./inc/ceremonylib.php");
    //require_once('../_res/tcpdf/tcpdf.php');
    date_default_timezone_set('Asia/Taipei');

    $userlevel = $_SESSION["userlevel"];  // mgr => 增加備註欄及繳費欄
    $systemAuth=$_SESSION["systemAuth"];
    $ceremAuth=$_SESSION["ceremonyAuth"];//echo $ceremAuth;
    if ($systemAuth[1]!=1||$ceremAuth[4]!=1){exit;}
    $year    = GetPUJAYear();
    $curYear = GetCurrentYear();
?>
<!DOCTYPE HTML>
<HTML lang="en"><HEAD><META content="IE=11.0000" http-equiv="X-UA-Compatible">
<TITLE>南區學員點名系統-法會報名</TITLE>
<META name="keywords" content="">
<META name="description" content="">
<META http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<META name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon" href="../_res/img/icons.ico">
<LINK href="../_res/css/site.css" rel="stylesheet" media="screen">
<LINK href="../_res/css/stdtheme.css" rel="stylesheet" media="screen">
<link href="../_res/css/jqueryui/jquery-ui.css" rel="stylesheet">
<link href="../_res/css/fixtblTheme.css" rel="stylesheet" media="screen"/>
<link href="../_res/css/bwsouth.css" rel="stylesheet" media="screen">

<script src="../_res/js/analytics.js" type="text/javascript"></script>
<script src="../_res/js/jquery-2.1.1.min.js" type="text/javascript"></script>
<script src="../_res/js/jqueryui/jquery-ui.min.js" type="text/javascript"></script>
<script src="../_res/js/jquery.fixedheadertable.js"></script>
<!--xlsx js -->
<script src="../_res/js/js-xlsx/xlsx.full.min.js"></script>
<script src="../_res/js/js-xlsx/Blob.js"></script>
<script src="../_res/js/js-xlsx/FileSaver.js"></script>
<script src="../_res/js/moment.min.js"></script>

<script src="./js/puja_management.js?{45D8DB81-FCF5-415B-8BBA-CDF5C9B73952}" type="text/javascript"></script>

<META name="GENERATOR" content=""></HEAD>
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

<TD align="left" class="navigation" id="navigationTree" valign="top"><?php $subMenuItem=11; include("inc/menu.php");?></TD>
<TD class="content" id="demos" valign="top">
<h2 align="center" > <font color="#0000ff"><?php echo "法會管理"; ?></font></h2>
<TABLE style="vertical-align: top; border-collapse: collapse;" cellspacing="0" cellpadding="0">
<TR><TD valign="top">

<DIV class="demoContainer" id="demoContainer">

<?php
    $checkLevel=3;
    if ($userlevel >= $checkLevel) {echo "<body style='font-size:11px'>";}
	else {echo "<body>";}
    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    // command panel
	echo "<br>";
	echo "<table class='reference' align='center' style='width:98%;'> <tr><td>";
	echo "<table class='refgroup'  align='center' style='width:95%;' valign='center'>";
	echo "<tr><td style='width:165px'></td>";
	echo "<td style='width:165px' align='right'>法會：</td>";
	//echo "<td style='width:165px'><select style='width:160px;' id='classname' class='classname' name='classname'>";
	//echo "<option value='-'>-</option>";
	//while($row = mysql_fetch_array($result_class, MYSQL_ASSOC))
	//{
	//	echo "<option value='".$row["name"]."'>".$row["name"]."</option>";
	//}
	//echo "</select></td>";
    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
    $sql="select * from `pujaconfig` ORDER BY `idx` ASC";
    $result_class=mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($result_class);
	echo "<td style='width:85px'><select style='width:109px;' id='pujaid' class='pujaid' name='pujaid'>";
	echo "<option value='-'>-</option>";
	while($row = mysqli_fetch_array($result_class, MYSQLI_ASSOC)) {
        echo "<option value='".$row["idx"]."'>".$row["pujaname"]."</option>";
	}
	echo "</select></td>";

	echo "<td style='width:300px' align='left'><input id='traffic' type='button' name='traffic' value='車次設定'>";
    echo "&nbsp;&nbsp;<input id='query' type='button' name='query' value='法會參數'>";
    echo "&nbsp;&nbsp;<input id='studentdetailinfo' type='button' value='學員名冊'>";
	echo "</td>";
	echo "<td style='width:30px' align='right'></td></tr>";
	//echo "<tr><td colspan='5'><div id='msg'></div></td></tr>";//show debug info
	echo "</table>";
	echo "</td></tr></table>";
	echo "<br>";

	//-----------------------------------------------------------------------------------------------------------------------------------------------------
	// class table
	//echo "<div id='tabs'>";
	echo "<div id='queryresult' class='queryresult'></div>";
	//echo "<div class='fix_container'><div id='queryresult' class='grid_x height450'></div></div>";
	//echo "</div>";
      //echo "<hr>";

	echo "<br><table align='center' style:'width=850px;'>";
	echo "<tr style='align:center;'><td></td><td></td><td style='align:center;'><input id='send' type='button' name='register' value='儲存' /></td><td></td>";
	if ($userlevel>=11){echo "<td>&nbsp;&nbsp;<input id='new' type='button' name='new' value='新增'>&nbsp;&nbsp;<input id='del' type='button' name='del' value='刪除'></td></tr>";}
	else{echo "<td></td></tr>";}

	echo "</table>";// }}}}}}}}}}}}}}}}}}}}}

	echo "<input type='hidden' id='editmode' class='editmode' name='editmode' value='none' />";
	echo "<input type='hidden' id='tb' class='tb' name='tb' value='pujaconfig' />";
	echo "<input type='hidden' id='trafftb' class='trafftb' name='trafftb' value='pujaconfig_traff' />";
	echo "<input type='hidden' id='sub' class='sub' name='sub' value='puja_management' />";

	if ($userlevel>=11){echo "<input type='hidden' id='advpuja' class='advpuja' name='advpuja' value='YES' />";}
    else{echo "<input type='hidden' id='advpuja' class='advpuja' name='advpuja' value='NO' />";}

	$currDate = date('Y-m-d');
	echo "<input type='hidden' id='currentdate' name='currentdate' value='".$currDate."' />";
?>
<DIV>
</TD></TR>
</TABLE>
</TD>

</TR>
</TABLE>
</TD></TR></TABLE></TD></TR>
</TABLE>
</body></html>
