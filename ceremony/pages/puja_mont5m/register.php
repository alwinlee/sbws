<?php 
   //header("Content-Type: text/html; charset=utf-8");
    //session_start();
    require_once("../../../_res/_inc/connMysql.php"); 
    require_once("../../../_res/_inc/sharelib.php");
    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close	
    date_default_timezone_set('Asia/Taipei');
    $limit = 150;
    // 取得送過來的報名資料
    // 取得送過來的報名資料
    $cmd = $_POST['sqlcommand'];
    $joins = $_POST['jointotal'];
    $classid = $_POST['classid'];
    $tbname = $_POST['tbname'];
    $con = mysqli_connect("localhost","root","rinpoche","bwsouthdb");
    $sqlcount = "SELECT COUNT(day1) AS memb ,SUM(family1) AS fami FROM `".$tbname."` where (`day1`> 0) AND (`CLS_ID` != '".$classid."');";
    $sql_countresult=mysqli_query($con, $sqlcount);
    $numrows=mysqli_num_rows($sql_countresult);
    if ($numrows <= 0){
        mysqli_close($con);
        echo "0";
        exit;
    }
    $row = mysqli_fetch_array($sql_countresult, MYSQLI_ASSOC);
    $sum = $row['memb'] + $row['fami'];
    if (($sum + $joins)> $limit) {
        mysqli_close($con);
        echo ($sum - $limit - 100000);
        exit;
    }


    $cmd = str_replace (";;;;",";",$cmd);
    $sqlcmd=explode(";",$cmd);// $_POST['sqlcommand'];
    //$statisticcmd = $_POST['statisticcmd'];
    $result=0;
    if (count($sqlcmd) > 0) {
        
        for($i=0;$i<count($sqlcmd);$i++) {
            $sql=$sqlcmd[$i];
            $sql=str_replace ("&#&#","'",$sql);
            if (!$sql){continue;}
            $sql.=" limit 1";
            $ret=mysqli_query($con,$sql);//echo $sql;
            if($ret){;}else{
                $result+=1;
            }
            //echo $sql."<br>";
        }
        if($result<=0){
            mysqli_commit($con);
        }
        mysqli_close($con);
    }	
    echo $result;
?>