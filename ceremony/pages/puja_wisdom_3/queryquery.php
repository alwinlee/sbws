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

    chkPujaTBex($tbname);
    syncLeaderPujaTBX($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname,1);
    syncMemberPujaTBX($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname,1);

    $data="";
    // 交通車次
    $traffdesc="*";
    if ($region>="2A" && $region<="2Z"){$traffdesc="2";}
    if ($region>="3A" && $region<="3Z"){$traffdesc="3";}
    for($i=0;$i<6;$i++){
        $traffday=getTaffic($trafftbname,$i,$traffdesc);
        if($data==""){$data.=$traffday;}else{$data.=("-".$traffday);}
    }
    $data.=";";
    /*
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
*/
    // 檢查建立學員報名record //$Major=="YES" => 母班報名
    //if ($Major=="YES"){$sql="select * from `bwsouthdb`.`".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`<5 AND `titleid`>-8) ORDER BY titleid DESC, memberseq ASC";
    //}else{$sql="select * from `".$tbname."` WHERE (`CLS_ID`='".$classid."') ORDER BY titleid DESC, memberseq ASC";}
    if ($Major=="YES"){
        $sql = " select A.*, B.first, B.second from ( ";
        $sql .= "    select * ";
        $sql .= "    from `bwsouthdb`.`".$tbname."` ";
        $sql .= "    where (`CLS_ID`='".$classid."' AND `titleid`>=0 AND `titleid`<5) ";
        $sql .= ") AS A LEFT JOIN `bwsouthdb`.`wisdominfo` AS B ON A.STU_ID = B.stuid ";
        $sql .= "ORDER BY A.titleid DESC, A.Sex DESC, A.STU_ID ASC;";
        // $sql="select * from `bwsouthdb`.`".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 AND `titleid`<5) ORDER BY titleid DESC, Sex DESC, STU_ID ASC";
    } else {
        $sql = " select A.*, B.first, B.second from ( ";
        $sql .= "    select * ";
        $sql .= "    from `bwsouthdb`.`".$tbname."` ";
        $sql .= "    where (`CLS_ID`='".$classid."' AND `titleid` >= 0) ";
        $sql .= ") AS A LEFT JOIN `bwsouthdb`.`wisdominfo` AS B ON A.STU_ID = B.stuid ";
        $sql .= "ORDER BY A.titleid DESC, A.Sex DESC, A.STU_ID ASC;";
        //$sql="select * from `bwsouthdb`.`".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 ) ORDER BY titleid DESC, Sex DESC, STU_ID ASC";
    }
    /*if ($Major=="YES"){
        $sql="select * from `bwsouthdb`.`".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 ) ORDER BY titleid DESC, Sex DESC, STU_ID ASC";
    }else{
        $sql="select * from `bwsouthdb`.`".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 AND `titleid`<5) ORDER BY Sex DESC, STU_ID ASC";
    }*/
    $con = mysqli_connect("localhost","root","rinpoche");//connect_db("bwsouthdb");
    $sql_result = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($sql_result);
    if ($numrows <= 0){echo "0";exit;}

    while($row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)) { //MYSQL_NUM))//MYSQL_ASSOC))
        if (($row["titleid"] < 5) && ($row["first"] == 1 || $row["second"] == 1) ) {
            continue;
        }

        $data.=($row["idx"]."|".$row["lock"]."|".$row["name"]."|".$row["title"]."|".$row["day"]."|".$row["meal"]."|");
        $data.=($row["traff"]."|".$row["cost"]."|".$row["pay"]."|".$row["regdate"]."|".$row["paydate"]."|");
        $data.=($row["paybyid"]."|".$row["paybyname"]."|".$row["memo"]."|");
        $data.=($row["titleid"]."|".$row["traffCnt"]."|".$row["joinmode"]."|".$row["specialcase"]."|".$row["service"].$row["otherinfo"]."|".$row["first"]."|".$row["second"].";");
    }
    $result=$data;
    echo $result;
?>
