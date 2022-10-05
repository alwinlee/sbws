<?php 
    require_once("../../../_res/_inc/connMysql.php"); 
    require_once("../../../_res/_inc/sharelib.php");
    require_once("../../inc/ceremonylib.php");	
    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close	

    // 取得送過來的考生報名資料
    $classname=$_POST['pujaname'];
    $pujaid=$_POST['pujaid'];	
    $tbname=$_POST['tbname'];

    $data="";
    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
    $sql="select * from `".$tbname."` WHERE `idx`=".$pujaid."";
    //echo $sql; exit;

    $sql_result=mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($sql_result);		
    if ($numrows <= 0){echo "0";exit;}

    while($row = mysqli_fetch_array($sql_result, MYSQLI_NUM))//MYSQL_NUM))//MYSQL_ASSOC))
    {	
        //$data.=($row["idx"]."|".$row["lock"]."|".$row["name"]."|".$row["title"]."|".$row["day"]."|".$row["joinmode"]."|".$row["meal"]."|".$row["specialcase"]."|".$row["traff"]."|".$row["cost"]."|".$row["pay"]."|".$row["regdate"]."|".$row["paydate"]."|".$row["memo"]).";";			
        $data.=($row[0]."|".$row[1]."|".$row[2]."|".$row[3]."|".$row[4]."|".$row[5]."|".$row[6]."|".$row[7]."|".$row[8]."|".$row[9]."|".$row[10]."|");//.";<br>";
        $data.=($row[11]."|".$row[12]."|".$row[13]."|".$row[14]."|".$row[15]."|".$row[16]."|".$row[17]."|".$row[18]."|".$row[19]."|".$row[20]."|".$row[21]."|".$row[22]."|".$row[23]."|".$row[24]."|".$row[25]."|".$row[26]."|".$row[27]);
    }

    $result=$data;
    echo $result;		
?>

