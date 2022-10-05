<?php
    require_once("../../../_res/_inc/connMysql.php");
    require_once("../../../_res/_inc/sharelib.php");
    require_once("../../inc/ceremonylib.php");
    require_once("../../inc/ceremonylibx.php");
    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close

    // 取得送過來的考生報名資料
    $classname=$_POST['classname'];
    $classid=$_POST['classid'];
    $tbname=$_POST['tbname'];
    $trafftbname=$_POST['trafftbname'];
    $Major=$_POST['Major'];
    $detailinfo=$_POST['detailinfo'];
    $leaderinfo=$_POST['leaderinfo'];
    $volunteerinfo=$_POST['volunteerinfo'];
    $classfullname=$_POST['classfullname'];

    $area="AC";
    $region=$_POST['region'];;//region-code
    $findleaderinfo=($leaderinfo=="YES" ? true:false);
    $findvolunteerinfo=($volunteerinfo=="YES" ? true:false);

    chkPujaTB($tbname);
    //syncLeaderPujaTBX($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname,0);
    syncMemberPujaTBX($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname,0);
    
    $data="";$traffday1="";$traffday2="";$traffday3="";
    // 固定取三天的車次表
    $con = mysqli_connect("localhost","root","rinpoche");//connect_db("bwsouthdb");
    $sql="select * from `bwsouthdb`.`".$trafftbname."` WHERE day=0 ORDER BY `traffid` ASC";
    $sql_traff_result = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($sql_traff_result);
    if ($numrows > 0){
        while($row = mysqli_fetch_assoc($sql_traff_result)) {
            if ($traffday1==""){$traffday1.=($row["traffid"]."|".$row["traffname"]);}
            else{$traffday1.=("|".$row["traffid"]."|".$row["traffname"]);}
        }
    }
    //$sql = "SELECT A.STU_ID, A.TTL_ID, B.Name, B.Sex, B.age, B.day FROM `sdmdb`.`sdm_clsmembers` AS A ";
    //$sql .= "INNER JOIN `bwsouthdb`.`puja_yishienx_2018` AS B ON A.STU_ID = B.STU_ID WHERE A.CLS_ID='".$classid."';";
    //$result = mysqli_query($con, $sql);
    //$numrows = mysqli_num_rows($result);
    //if ($numrows > 0){
    //    while($row = mysqli_fetch_assoc($result)) {
    //        $row['Name'];
    //    }
    //}

    $sql="select * from `bwsouthdb`.`".$trafftbname."` WHERE day=1 ORDER BY `traffid` ASC";
    $sql_traff_result = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($sql_traff_result);
    if ($numrows > 0){
        while($row = mysqli_fetch_assoc($sql_traff_result)) {
            if ($traffday2==""){$traffday2.=($row["traffid"]."|".$row["traffname"]);}
            else{$traffday2.=("|".$row["traffid"]."|".$row["traffname"]);}
        }
    }

    $sql="select * from `bwsouthdb`.`".$trafftbname."` WHERE day=2 ORDER BY `traffid` ASC";
    $sql_traff_result = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($sql_traff_result);
    if ($numrows > 0){
        while($row = mysqli_fetch_assoc($sql_traff_result)) {
            if ($traffday3==""){$traffday3.=($row["traffid"]."|".$row["traffname"]);}
            else{$traffday3.=("|".$row["traffid"]."|".$row["traffname"]);}
        }
    }
    $data.=$traffday1;$data.="-";$data.=$traffday2;$data.="-";$data.=$traffday3;$data.=";";

    // 檢查建立學員報名record //$Major=="YES" => 母班報名
    if ($Major=="YES"){$sql="select * from `bwsouthdb`.`".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`<5 AND `titleid`>-8) ORDER BY titleid DESC, memberseq ASC";
    }else{$sql="select * from `".$tbname."` WHERE (`CLS_ID`='".$classid."') ORDER BY titleid DESC, memberseq ASC";}

    $sql_result = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($sql_result);
    if ($numrows <= 0){echo "0";exit;}

    while($row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)) { //MYSQL_NUM))//MYSQL_ASSOC))
        $data.=($row["idx"]."|".$row["lock"]."|".$row["name"]."|".$row["title"]."|".$row["day"]."|".$row["meal"]."|");
        $data.=($row["traff"]."|".$row["cost"]."|".$row["pay"]."|".$row["regdate"]."|".$row["paydate"]."|");
        $data.=($row["paybyid"]."|".$row["paybyname"]."|".$row["memo"]."|".$row["titleid"])."|".$row["traffCnt"].";";
    }
    $result=$data;
    echo $result;
?>
