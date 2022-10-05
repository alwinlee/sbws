<?php 
    //header("Content-Type: text/html; charset=utf-8");
    //session_start();	
    require_once("../../../_res/_inc/connMysql.php"); 
    require_once("../../../_res/_inc/sharelib.php");

    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close	
    date_default_timezone_set('Asia/Taipei');
    $mode=$_POST['mode'];
    $tbname=$_POST['tbname'];
    $C0=$_POST['C0'];
    $C1=$_POST['C1'];$C2=$_POST['C2'];$C3=$_POST['C3'];$C4=$_POST['C4'];$C5=$_POST['C5'];$C6=$_POST['C6'];$C7=$_POST['C7'];$C8=$_POST['C8'];$C9=$_POST['C9'];$C10=$_POST['C10'];	
    $C11=$_POST['C11'];$C12=$_POST['C12'];$C13=$_POST['C13'];$C14=$_POST['C14'];$C15=$_POST['C15'];$C16=$_POST['C16'];$C17=$_POST['C17'];$C18=$_POST['C18'];$C19=$_POST['C19'];$C20=$_POST['C20'];	
    $C21=$_POST['C21'];$C22=$_POST['C22'];$C23=$_POST['C23'];$C24=$_POST['C24'];$C25=$_POST['C25'];$C26=$_POST['C26'];$C27=$_POST['C27'];

    $sql_traff="";
    $sql_pujadb="";
    $C22=str_replace ("'","\'",$C22);
    $C22=str_replace ("\\\'","\'",$C22);    
    if ($C0>0)
    {
        if($mode=="DELETE"){$sql="DELETE FROM `".$tbname."` WHERE `idx`=".$C0.";";$sql_traff="DROP TABLE ".$C14.";";}
        else{	    
            $sql="UPDATE `".$tbname."` SET `pujaname`='".$C1."',`year`=".$C2.",`usemode`=".$C3.",`startdate`='".$C4."',`enddate`='".$C5."',";
            $sql.="`mgrstartdate`='".$C6."',`mgrenddate`='".$C7."',`boardstartdate`='".$C8."',`boardenddate`='".$C9."',`pujadate`='".$C10."',";
            $sql.="`pujadateend`='".$C11."',`dbname`='".$C12."',`statisticdbname`='".$C13."',`traffdbname`='".$C14."',`traffgocost`=".$C15.",";		
            $sql.="`traffbackcost`=".$C16.",`traffroundcost`=".$C17.",`leadersupport`='".$C18."',`php`='".$C19."',`statisticphp`='".$C20."',";
            $sql.="`menuid`=".$C21.",`boarddesc`='".$C22."',`img`='".$C23."',`areastartdate`='".$C24."',`areaenddate`='".$C25."',`areasupport`='".$C26."',`areanotsupport`='".$C27."' WHERE `idx`=".$C0." limit 1;";
        }
    }else{
        $sql="INSERT INTO `".$tbname."` VALUES (NULL,'".$C1."','".$C2."','".$C3."','".$C4."','".$C5."','".$C6."','".$C7."','".$C8."','".$C9."','".$C10."',";
        $sql.="'".$C11."','".$C12."','".$C13."','".$C14."','".$C15."','".$C16."','".$C17."','".$C18."','".$C19."','".$C20."','".$C21."','".$C22."','".$C23."','".$C24."','".$C25."','".$C26."','".$C27."');";
   }
    // traff
    $sql_traff="CREATE TABLE IF NOT EXISTS `".$C14."` (";
    $sql_traff.="`idx` int(8) NOT NULL auto_increment,";
    $sql_traff.="`traffid` varchar(8) COLLATE utf8_unicode_ci NOT NULL COMMENT '車次代號',";
    $sql_traff.="`day` int(8) DEFAULT 0 COMMENT '第幾天的車次',";
    $sql_traff.="`traffname` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT '車次位置名稱',";
    $sql_traff.="`traffdesc` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT '車次位置說明',";
    $sql_traff.="`gocost` int(4) DEFAULT '0' COMMENT '去程費用',";
    $sql_traff.="`backcost` int(4) DEFAULT '0' COMMENT '回程費用',";
    $sql_traff.="`roundcost` int(4) DEFAULT '0' COMMENT '去回程費用',";
    $sql_traff.="`mealcost` int(4) DEFAULT '0' COMMENT '餐點費用',";
    $sql_traff.="`morningsession` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '上午場說明',";
    $sql_traff.="`afternoonsession` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '下午場說明',";
    $sql_traff.="UNIQUE KEY `idx` (`idx`)";
    $sql_traff.=") ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";	
        
        //echo $sql_pujadb;exit;
 
    //echo $sql."<br>".$sql_traff."<br>".$sql_pujadb;exit;
    $result=0;
    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
    $ret=$sql_result=mysqli_query($con, $sql);
    if($ret){;}else{$result+=1;}
    if ($sql_traff!=""){$ret=mysqli_query($con, $sql_traff);if($ret){;}else{$result+=1;}}
    if($result<=0){mysqli_commit($con);}

    echo $result;
?>

