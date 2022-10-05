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

    $data="";
    $area="AC";
    $region=$_POST['region'];//region-code
    $findleaderinfo=($leaderinfo=="YES" ? true:false);
    $findvolunteerinfo=($volunteerinfo=="YES" ? true:false);

    chkpujadb($tbname);//多場次  //chkPujaTBex($tbname);//
    $traffdesc="*";
    if ($region>="2A" && $region<="2Z"){$traffdesc="2";}
    if ($region>="3A" && $region<="3Z"){$traffdesc="3";}

    // 交通車次
    for($i=0;$i<6;$i++){
        $traffday=getTaffic($trafftbname,$i,$traffdesc);
        if($data==""){$data.=$traffday;}else{$data.=("-".$traffday);}
    }
    $data.=";";
    //syncLeaderPujaTB($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname,1);
    //syncMemberPujaTB($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname,1);

    //syncLeaderpujadbNEW($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname);
    syncMemberpujadbNEW($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname);

    // 檢查建立學員報名record //$Major=="YES" => 母班報名
    if ($Major=="YES"){$sql="select * from `".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 AND `titleid`<5) ORDER BY Sex DESC, STU_ID ASC";
    }else{$sql="select * from `".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 ) ORDER BY Sex DESC, STU_ID ASC";}

    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");//connect_db("bwsouthdb");
    $sql_result=mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($sql_result);
    if ($numrows <= 0){
        mysqli_close($con);
        echo "0";
        exit;
    }

    while($row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)){//MYSQL_NUM))//MYSQL_ASSOC))
        $data.=($row["idx"]."|".$row["name"]."|".$row["title"]."|".$row["titleid"]."|0|0|0|0|0|0|0|");//預留7個位置0~10

        // item1
        $data.=($row["lock1"]."|".$row["day1"]."|".$row["meal1"]."|".$row["family1"]."|".$row["service1"]."|");//11~15
        $data.=($row["joinmode1"]."|".$row["specialcase1"]."|".$row["traff1"]."|".$row["cost1"]."|".$row["pay1"]."|");//16~20
        $data.=($row["attend1"]."|".$row["regdate1"]."|".$row["paydate1"]."|".$row["payround1"]."|".$row["paybyid1"]."|");//21~25
        $data.=($row["paybyname1"]."|".$row["cancel1"]."|".$row["cancelinfo1"]."|".$row["memo1"]."|0|");//26~30

        // item2
        $data.=($row["lock2"]."|".$row["day2"]."|".$row["meal2"]."|".$row["family2"]."|".$row["service2"]."|");//31~35
        $data.=($row["joinmode2"]."|".$row["specialcase2"]."|".$row["traff2"]."|".$row["cost2"]."|".$row["pay2"]."|");//36~40
        $data.=($row["attend2"]."|".$row["regdate2"]."|".$row["paydate2"]."|".$row["payround2"]."|".$row["paybyid2"]."|");//41~45
        $data.=($row["paybyname2"]."|".$row["cancel2"]."|".$row["cancelinfo2"]."|".$row["memo2"]."|0|");//46~50

        // item3
        $data.=($row["lock3"]."|".$row["day3"]."|".$row["meal3"]."|".$row["family3"]."|".$row["service3"]."|");//51~55
        $data.=($row["joinmode3"]."|".$row["specialcase3"]."|".$row["traff3"]."|".$row["cost3"]."|".$row["pay3"]."|");//56~60
        $data.=($row["attend3"]."|".$row["regdate3"]."|".$row["paydate3"]."|".$row["payround3"]."|".$row["paybyid3"]."|");//61~65
        $data.=($row["paybyname3"]."|".$row["cancel3"]."|".$row["cancelinfo3"]."|".$row["memo3"]."|0|");//66~70

        $data.="0;";
        $nCnt++;
    }

    $result=$data;
    echo $result;

?>
