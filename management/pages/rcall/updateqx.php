<?php 
    //header("Content-Type: text/html; charset=utf-8");
    //session_start();

    require_once("../../../_res/_inc/connMysql.php"); 
    require_once("../../../_res/_inc/sharelib.php");

    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close	
    date_default_timezone_set('Asia/Taipei');
	
    // 取得送過來的資料	
    $classroomtb=$_POST['classroomtb']; //$_POST['sqlcommand'];	
    $classroomnxtb=$_POST['classroomnxtb']; //$_POST['sqlcommand'];	    
    $rollcalltb=$_POST['rollcalltb']; //$_POST['sqlcommand'];
    $rollcallnxtb=$_POST['rollcallnxtb']; //$_POST['sqlcommand'];    

    //echo $rollcalltb."-".$rollcallnxtb."-".$classroomtb."-".$classroomnxtb; exit;
    //---------------------------------------------------------------------------------
    // 補算班上所有學員的qx季出席率統計值   
    $sql="SELECT * FROM `".$rollcallnxtb."`"; 
    $sql_result=mysql_query($sql);			
    $numrows=mysql_num_rows($sql_result);
    if($numrows > 0)
    {
        while($rownx=mysql_fetch_array($sql_result, MYSQL_ASSOC))
        {
            $qx=0;$qxSum=0;$qxCnt=0;
            $qxSum=$rownx["mx"]+$rownx["m1"]+$rownx["m2"];
            if($rownx["mx"]>0){$qxCnt++;}if($rownx["m1"]>0){$qxCnt++;}if($rownx["m2"]>0){$qxCnt++;}            
            if($qxCnt>0){$qx=$qxSum/$qxCnt;}
            
            $sql="UPDATE `".$rollcallnxtb."` SET `qx`=".$qx." WHERE (`idx`=".$rownx["idx"].") limit 1;";
            $allcmd[]=$sql;
        }             
    }           

    //---------------------------------------------------------------------------------------    
    
    mysql_query("SET autocommit=0");        
    $count=count($allcmd);
    for($i=0;$i<$count;$i++){$ret=mysql_query($allcmd[$i]);}//echo $allcmd[$i]."<br>";

    if ($count>0){mysql_query("COMMIT");}
 
	
    echo $result;
?>

