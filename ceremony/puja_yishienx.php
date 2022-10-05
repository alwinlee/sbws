<?php
    header("Content-Type: text/html; charset=utf-8");
    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close
    error_reporting(E_ALL & ~E_NOTICE);
    session_start();

    require_once("../_res/_inc/login_check.php");//檢查是否已登入，若未登入則導回首頁
    require_once("../_res/_inc/connMysql.php");
    require_once("../_res/_inc/sharelib.php");
    require_once('../_res/tcpdf/tcpdf.php');
    require_once("../_res/_inc/mobile_detect.php");
    require_once("./inc/ceremonylib.php");
    date_default_timezone_set('Asia/Taipei');

    $detectMobile=new Mobile_Detect();
    $userlevel = $_SESSION["userlevel"];  // mgr => 增加備註欄及繳費欄
    $year = GetPUJAYear();
    $curYear = GetCurrentYear();


    $pujaid="puja_yishienx";
    // default value
    $ceremAuth=$_SESSION["ceremonyAuth"];//echo $ceremAuth;
    $checkLevel=3;
    $showdebug=false;//false;
    $volunteerinfo="NO";
    $leaderinfo="YES";
    $detailinfo="YES";//匯出使用
    $pujatitle=($year."年 法會");
	$day1title="10.22(六)";
    $day2title="10.23(日)";

    $traffRoundCost=600; //當天
    $traffGoCost=300;
    $traffBackCost=300;
    $traff2RoundCost=600;
    $leadersupport=false;//幹部報名
    $tbname=$pujaid;
    $trafftbname=$pujaid;

    $subMenuItem=330;
    $enddate="2015-01-01";
    $mgrenddate="2015-01-01";
    $item=Array('-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-');

    $dbname = "bwsouthdb";
    $con = connect_db($dbname);
    if ($con){ // 取法會參數puja configure
        $sql="select * from pujaconfig where `php`='".$pujaid.".php'"; // 取得法會資料設定值
        $puja_result=mysqli_query($con, $sql);;
        $numrows=mysqli_num_rows($puja_result);
        if ($numrows>0){
            $puja_row = mysqli_fetch_array($puja_result, MYSQLI_ASSOC);
            $subMenuItem=$puja_row["menuid"];
            $traffRoundCost=$puja_row["traffroundcost"]; // 當天
            $traffGoCost=$puja_row["traffgocost"];
            $traffBackCost=$puja_row["traffbackcost"];
            $traff2RoundCost=$traffGoCost + $traffBackCost; // 隔天
            $pujatitle=$puja_row["year"]."年 ".$puja_row["pujaname"];
            if($puja_row["leadersupport"]=="Y"){$leadersupport=true;}
            $tbname=$puja_row["dbname"];
            $trafftbname=$puja_row["traffdbname"];
            $enddate=$puja_row["enddate"];
            $mgrenddate=$puja_row["mgrenddate"];
            $otherinfo=$puja_row["statisticphp"];//其他訊息
            if ($otherinfo!=""){
                $itemx=explode("~",$otherinfo);
                for($i=0;$i<count($itemx);$i++){
                    $item[$i]=$itemx[$i];
                    if($i==0){$day1title=$itemx[$i];}
                    if($i==1){$day2title=$itemx[$i];}
                }
            }
        }

        //mysqli_free_result($puja_result);
        mysqli_close($con);
    }

    if(canCheckin($userlevel,$ceremAuth[4],$enddate,$mgrenddate)!=true){exit;}
?>
<!DOCTYPE HTML>
<HTML lang="en"><head><meta content="IE=11.0000" http-equiv="X-UA-Compatible">
<TITLE>南區學員點名系統-法會報名</title>

<meta name="keywords" content="">
<meta name="description" content="">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon" href="../_res/img/icons.ico">
<link href="../_res/css/site.css" rel="stylesheet" media="screen">
<link href="../_res/css/stdtheme.css" rel="stylesheet" media="screen">
<link href="../_res/css/jqueryui/jquery-ui.css" rel="stylesheet">
<link href="../_res/css/fixtblTheme.css"      rel="stylesheet" media="screen"/>
<link href="../_res/css/bwsouth.css" rel="stylesheet" media="screen">

<script src="../_res/js/analytics.js" type="text/javascript"></script>
<script src="../_res/js/jquery-2.1.1.min.js" type="text/javascript"></script>
<script src="../_res/js/jquery.fixedheadertable.js"></script>
<script src="./js/puja_yishienx.js?{45D8DB81-FCF5-415B-8BBA-CDF5C9B73952}" type="text/javascript"></script>

<meta name="GENERATOR" content=""></head>
<div class="top" id="pageTop"></div>
<div class="rc-all contentdiv">

<table style="border-collapse: collapse;" cellspacing="0" cellpadding="0">
<tr><td align="left" class="navigation" id="navigationTree" valign="top"><?php include("inc/menu.php");?></td>
<td class="content" id="demos" valign="top"><h2 align="center" > <font color="#0000ff"><?php echo $pujatitle; ?></font></h2>
<table style="vertical-align: top; border-collapse: collapse;" cellspacing="0" cellpadding="0">
<tr><td valign="top">
<div class="demoContainer" id="demoContainer">
<?php
    if ($userlevel>=$checkLevel) {echo "<body style=\"font-size:11px\">";}
    else {echo "<body>";}
    $numrows = 0;
    $dbname = "sdmdb";
    $conx = connect_db($dbname);
    if ($conx){
        //mysqli_select_db($con,"sdmdb");
        $areakey="";
        if (isset($_SESSION["keyofarea"])&&$_SESSION["keyofarea"]!="") {
            $keyofareaid=$_SESSION["keyofarea"];
            $areaid=explode(";",$keyofareaid);
            $sqlstring="";
            for($x=0;$x<count($areaid);$x++) {
                if ($sqlstring!=""){$sqlstring.=" OR";}
                $sqlstring.="`ParentOrgId`=".$areaid[$x]." OR `OrgId`=".$areaid[$x]." ";
            }
            $areakey=" AND (".$sqlstring.")";
        }
        $sql="SELECT * FROM sdm_classes WHERE (`ARE_ID`='C' AND `ClsStatus`='Y' ".$areakey.") ORDER BY `Class` ASC";
        $result_class = mysqli_query($conx, $sql);
        $numrows = mysqli_num_rows($result_class);
    }
    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    // command panel
    echo "<br>";
    echo "<table class=\"reference\" align=\"center\" style=\"width:98%;\"> <tr><td>";
    echo "<table class=\"refgroup\"  align=\"center\" style=\"width:95%;\" valign=\"center\">";
    echo "<tr><td style=\"width:165px\"></td>";
    echo "<td style=\"width:165px\" align=\"right\">班級：</td>";

    echo "<td style=\"width:85px\"><select style=\"width:109px;\" id=\"classid\" class=\"classid\" name=\"classid\">";
    echo "<option value=\"-\">-</option>";
    if ($numrows>0){
        if ($ceremAuth[4]>=1){
            $substring1="春";$substring2="秋";
            $substring3=(date('Y')-2000)."春";$substring4=(date('Y')-2000)."秋";
            while($row = mysqli_fetch_assoc($result_class)){
                $classname=$row["Class"]."-".$row["OrgName"];
                echo "<option value=\"".$row["CLS_ID"]."\" regioncode=\"".$row["ParentOrgId"]."\"  AREAID=\"".$row["ARE_ID"]."\" classname=\"".$row["Class"]."\" >".$classname."</option>";
            }
        } else {
            $substring1=(date('Y')-2001)."春";$substring2=(date('Y')-2001)."秋";
            $substring3=(date('Y')-2002)."春";$substring4=(date('Y')-2002)."秋";
            while($row = mysqli_fetch_assoc($result_class)) {
                $classname=$row["Class"]."-".$row["OrgName"];
                echo "<option value=\"".$row["CLS_ID"]."\" regioncode=\"".$row["ParentOrgId"]."\"  AREAID=\"".$row["ARE_ID"]."\" classname=\"".$row["Class"]."\" >".$classname."</option>";
            }
        }
        mysqli_free_result($result_class);
    }
    echo "</select></td>";
    if ($conx){
        mysqli_close($conx);
    }
    echo "<td style=\"width:350px\" align=\"left\" colspan=\"2\"><input id=\"query\" type=\"button\" name=\"query\" value=\"報名表\">";
    echo "&nbsp;&nbsp;";
    //echo "&nbsp;<input id=\"prttable\" class=\"prttable\" type=\"button\" name=\"prttable\" value=\"列印報名表\">";
    if ($ceremAuth[4]>=1) {
        echo "<input type=\"button\" id=\"exportclasslist\" name=\"exportclasslist\" value=\"匯出班級總表\" class=\"exportclasslist\" />";
    }
    echo "</td></tr>";
    if ($ceremAuth[4]>=1){
        echo "<tr style=\"background-color:#F0F0F0;\"><td style=\"width:150px;\"></td>";
        echo "<td style=\"width:150px\" align=\"right\"></td>";

        // 區域
        echo "<td style=\"width:85px\">";
        echo "</td>";

        echo "<td style=\"width:330px\" align=\"left\">";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<input id=\"export\" type=\"button\" name=\"export\" value=\"匯出總表\">&nbsp;&nbsp;";
        echo "<input id=\"exportbydate\" type=\"button\" name=\"exportbydate\" value=\"匯出總表(時間排序)\">&nbsp;&nbsp;";
        echo "</td></tr>";
    }
    if ($showdebug==true){echo "<tr><td colspan=\"5\"><div id=\"msg\"></div></td></tr>";}//show debug info
    echo "</table>";
    echo "</td></tr></table>";
    echo "<br>";

    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    // class table
    //echo "<div id=\"tabs\">";
    echo "<div id=\"queryresult\" class=\"queryresult\"></div>";
    //echo "<div class=\"fix_container\"><div id=\"queryresult\" class=\"grid_x height450\"></div></div>";
    //echo "</div>";
    //echo "<hr>";

    echo "<br><table align=\"center\" style:\"width=850px;\">";
    echo "<tr style=\"align:center;\"><td></td><td></td><td style=\"align:center;\"><input id=\"send\" type=\"button\" name=\"register\" value=\"儲存\" /></td><td></td><td></td></tr>";
    echo "</table>";// }}}}}}}}}}}}}}}}}}}}}
    echo "<input type=\"hidden\" id=\"tb\" class=\"tb\" name=\"tb\" value=\"".$tbname."\" />";
    echo "<input type=\"hidden\" id=\"trafftb\" class=\"trafftb\" name=\"trafftb\" value=\"".$trafftbname."\" />";
    echo "<input type=\"hidden\" id=\"sub\" class=\"sub\" name=\"sub\" value=\"".$pujaid."\" />";
    echo "<input type=\"hidden\" id=\"Major\" class=\"Major\" name=\"Major\" value=\"".($leadersupport ? "NO":"YES")."\" />";//Major : YES 母班報名
    echo "<input type=\"hidden\" id=\"day1title\" class=\"day1title\" name=\"day1title\" value=\"".$day1title."\" />";
    echo "<input type=\"hidden\" id=\"day2title\" class=\"day2title\" name=\"day2title\" value=\"".$day2title."\" />";
    if ($ceremAuth[4]>=1)
    {
        echo "<input type=\"hidden\" id=\"payitem\" class=\"payitem\" name=\"payitem\" value=\"NO\" />";
        echo "<input type=\"hidden\" id=\"payerid\" class=\"payerid\" name=\"payerid\" value=\"".$_SESSION["username"]."\" />";
        echo "<input type=\"hidden\" id=\"payername\" class=\"payername\" name=\"payername\" value=\"".$_SESSION["user"]."\" />";
        $regioncode="*";
        if (isset($_SESSION["keyofarea"])&&$_SESSION["keyofarea"]!=""){$regioncode=$_SESSION["keyofarea"];}
        echo "<input type=\"hidden\" id=\"regioncode\" class=\"regioncode\" name=\"regioncode\" value=\"".$regioncode."\" />";
    }else{
        echo "<input type=\"hidden\" id=\"payitem\" class=\"payitem\" name=\"payitem\" value=\"NO\" />";
        echo "<input type=\"hidden\" id=\"payerid\" class=\"payerid\" name=\"payerid\" value=\"\" />";
        echo "<input type=\"hidden\" id=\"payername\" class=\"payername\" name=\"payername\" value=\"\" />";
        echo "<input type=\"hidden\" id=\"regioncode\" class=\"regioncode\" name=\"regioncode\" value=\"\" />";
    }
    $currDate = date('Y-m-d');
    echo "<input type=\"hidden\" id=\"currentdate\" class=\"currentdate\" name=\"currentdate\" value=\"".$currDate."\" />";
    echo "<input type=\"hidden\" id=\"dbg\" class=\"dbg\" name=\"dbg\" value=\"".($showdebug ? "YES":"NO")."\" />";
    echo "<input type=\"hidden\" id=\"pujatitle\" class=\"pujatitle\" name=\"pujatitle\" value=\"".$pujatitle."\" />";

    $useMobile="NO";
    if ($detectMobile->isMobile()&&$detectMobile->isTablet()==false){$useMobile="YES";}
    echo "<input type=\"hidden\" id=\"mbdevice\" class=\"mbdevice\" name=\"mbdevice\" value=\"".$useMobile."\" />";

    echo "<input type=\"hidden\" id=\"traffroundfee\" class=\"traffroundfee\" name=\"traffroundfee\" value=\"".$traffRoundCost."\" />";
    echo "<input type=\"hidden\" id=\"traffgofee\" class=\"traffgofee\" name=\"traffgofee\" value=\"".$traffGoCost."\" />";
    echo "<input type=\"hidden\" id=\"traffbackfee\" class=\"traffbackfee\" name=\"traffbackfee\" value=\"".$traffBackCost."\" />";
    echo "<input type=\"hidden\" id=\"traffoverdayfee\" class=\"traffoverdayfee\" name=\"traffoverdayfee\" value=\"".$traff2RoundCost."\" />";

    echo "<input type=\"hidden\" id=\"detailinfo\" class=\"detailinfo\" name=\"detailinfo\" value=\"".$detailinfo."\" />";
    echo "<input type=\"hidden\" id=\"leaderinfo\" class=\"leaderinfo\" name=\"leaderinfo\" value=\"".$leaderinfo."\" />";
    echo "<input type=\"hidden\" id=\"volunteerinfo\" class=\"volunteerinfo\" name=\"volunteerinfo\" value=\"".$volunteerinfo."\" />";
    echo "<input type=\"hidden\" id=\"item1\" class=\"item\" name=\"item1\" value=\"".$item[0]."\" />";
    echo "<input type=\"hidden\" id=\"item2\" class=\"item\" name=\"item2\" value=\"".$item[1]."\" />";
    echo "<input type=\"hidden\" id=\"item3\" class=\"item\" name=\"item3\" value=\"".$item[2]."\" />";
    echo "<input type=\"hidden\" id=\"item4\" class=\"item\" name=\"item4\" value=\"".$item[3]."\" />";
    echo "<input type=\"hidden\" id=\"item5\" class=\"item\" name=\"item5\" value=\"".$item[4]."\" />";
    echo "<input type=\"hidden\" id=\"item6\" class=\"item\" name=\"item6\" value=\"".$item[5]."\" />";
?>
<div></td></tr></table>
</td></tr></table>
</body></html>
