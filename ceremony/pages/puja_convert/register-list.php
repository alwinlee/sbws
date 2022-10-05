<?php 
    //header("Content-Type: text/html; charset=utf-8");
    //session_start();
    require_once("../../../_res/_inc/connMysql.php"); 
    require_once("../../../_res/_inc/sharelib.php");
    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close	
    date_default_timezone_set('Asia/Taipei');

    // 取得送過來的報名資料
    // 取得送過來的報名資料
    $cmd=$_POST['sqlcommand'];
    $cmd=str_replace (";;;;",";",$cmd);
    $sqlcmd=explode(";",$cmd);// $_POST['sqlcommand'];
    //$statisticcmd = $_POST['statisticcmd'];
    $result=0;
    if (count($sqlcmd) > 0) {
        $con = mysqli_connect("localhost","root","rinpoche","bwsouthdb");
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