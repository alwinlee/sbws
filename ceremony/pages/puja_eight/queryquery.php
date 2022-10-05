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
    syncLeaderPujaTBX($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname,0);
    syncMemberPujaTBX($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname,0);


    
    $traffdesc="*";
    if ($region>="2A" && $region<="2Z"){$traffdesc="2";}
    if ($region>="3A" && $region<="3Z"){$traffdesc="3";}
    $traffdesc="*";
    if ($region>="2A" && $region<="2Z"){$traffdesc="2";}
    if ($region>="3A" && $region<="3Z"){$traffdesc="3";}

    // 交通車次
    //for($i=0;$i<6;$i++){
    //    $traffday=getTaffic($trafftbname,$i,$traffdesc);
    //    if($data==""){$data.=$traffday;}else{$data.=("-".$traffday);}
    //}
    // 固定取三天的車次表
    $data="";$traffday1="";$traffday2="";$traffday3="";
    $con = mysqli_connect("localhost","root","rinpoche");//connect_db("bwsouthdb");
    
    $day = 0;
    $sql="select * from `bwsouthdb`.`".$trafftbname."` WHERE (`day`=".$day." AND `traffdesc`='".$traffdesc."')  ORDER BY `traffid` ASC";
    //$sql="select * from `bwsouthdb`.`".$trafftbname."` WHERE day=0 ORDER BY `traffid` ASC";
    $sql_traff_result = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($sql_traff_result);
    if ($numrows > 0){
        while($row = mysqli_fetch_assoc($sql_traff_result)) {
            if ($traffday1==""){$traffday1.=($row["traffid"]."|".$row["traffname"]);}
            else{$traffday1.=("|".$row["traffid"]."|".$row["traffname"]);}
        }
    }
    

    $day = 1;
    $sql="select * from `bwsouthdb`.`".$trafftbname."` WHERE (`day`=".$day." AND `traffdesc`='".$traffdesc."')  ORDER BY `traffid` ASC";
    $sql_traff_result = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($sql_traff_result);
    if ($numrows > 0){
        while($row = mysqli_fetch_assoc($sql_traff_result)) {
            if ($traffday2==""){$traffday2.=($row["traffid"]."|".$row["traffname"]);}
            else{$traffday2.=("|".$row["traffid"]."|".$row["traffname"]);}
        }
    }

    $day = 2;
    $sql="select * from `bwsouthdb`.`".$trafftbname."` WHERE (`day`=".$day." AND `traffdesc`='".$traffdesc."')  ORDER BY `traffid` ASC";
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
    //if ($Major=="YES"){$sql="select * from `bwsouthdb`.`".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`<5 AND `titleid`>-8) ORDER BY titleid DESC, memberseq ASC";
    //}else{$sql="select * from `".$tbname."` WHERE (`CLS_ID`='".$classid."') ORDER BY titleid DESC, memberseq ASC";}
    if ($Major=="YES"){
        $sql="select * from `bwsouthdb`.`".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 ) ORDER BY titleid DESC, Sex DESC, STU_ID ASC";
    }else{
        $sql="select * from `bwsouthdb`.`".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 AND `titleid`<5) ORDER BY Sex DESC, STU_ID ASC";
    }

    $sql_result = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($sql_result);
    if ($numrows <= 0){echo "0";exit;}

    while($row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)) { //MYSQL_NUM))//MYSQL_ASSOC))
        $data.=($row["idx"]."|".$row["lock"]."|".$row["name"]."|".$row["title"]."|".$row["day"]."|".$row["meal"]."|");
        $data.=($row["traff"]."|".$row["cost"]."|".$row["pay"]."|".$row["regdate"]."|".$row["paydate"]."|");
        $data.=($row["paybyid"]."|".$row["paybyname"]."|".$row["memo"]."|".$row["titleid"])."|".$row["traffCnt"]."|".$row["service"]."|".$row["otherinfo"].";";
    }
    $result=$data;
    echo $result;
?>
