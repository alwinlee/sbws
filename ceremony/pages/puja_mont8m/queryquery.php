<?php
    require_once("../../../_res/_inc/connMysql.php");
    require_once("../../../_res/_inc/sharelib.php");
    require_once("../../inc/ceremonylib.php");
    require_once("../../inc/ceremonylibx.php");
    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close
    $limit = 150;
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

    $username =$_POST['user'];

    $data="";
    $area="AC";
    $region=$_POST['region'];//region-code
    $findleaderinfo=($leaderinfo=="YES" ? true:false);
    $findvolunteerinfo=($volunteerinfo=="YES" ? true:false);

    // check current count
    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");//connect_db("bwsouthdb");
    if ($username != 'root' && $username != 'admin' && $username != 'mgr1') {
    //if (true) {
        //$sqlcount = "SELECT COUNT(*) FROM `".$tbname."` where (`day1`>0 OR `day1`>1);";
        $sqlcount = "SELECT COUNT(day1) AS memb ,SUM(family1) AS fami FROM `".$tbname."` where (`day1`>0)";
        $sql_countresult=mysqli_query($con, $sqlcount);
        $numrows=mysqli_num_rows($sql_countresult);
        if ($numrows <= 0){
            mysqli_close($con);
            echo "0";
            exit;
        }
        $row = mysqli_fetch_array($sql_countresult, MYSQLI_ASSOC);
        $sum = $row['memb'] + $row['fami'];
        if ($sum >= $limit) {
            mysqli_close($con);
            echo "-1";
            exit;
        }
    }


    chkpujadbx($tbname);//多場次 8  //chkPujaTBex($tbname);//
    $traffdesc="*";
    if ($region>="2A" && $region<="2Z"){$traffdesc="2";}
    if ($region>="3A" && $region<="3Z"){$traffdesc="3";}
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
    syncMemberpujadbxNEW($tbname,$classname,$classid,$area,$region,$findleaderinfo,$findvolunteerinfo,$classfullname);

    // 檢查建立學員報名record //$Major=="YES" => 母班報名
    if ($Major=="YES"){$sql="select * from `".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 AND `titleid`<5) ORDER BY Sex DESC, STU_ID ASC";
    }else{$sql="select * from `".$tbname."` WHERE (`CLS_ID`='".$classid."' AND `titleid`>=0 ) ORDER BY Sex DESC, STU_ID ASC";}

    
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

        // item4
        $data.=($row["lock4"]."|".$row["day4"]."|".$row["meal4"]."|".$row["family4"]."|".$row["service4"]."|");//71~75
        $data.=($row["joinmode4"]."|".$row["specialcase4"]."|".$row["traff4"]."|".$row["cost4"]."|".$row["pay4"]."|");//76~80
        $data.=($row["attend4"]."|".$row["regdate4"]."|".$row["paydate4"]."|".$row["payround4"]."|".$row["paybyid4"]."|");//81~85
        $data.=($row["paybyname4"]."|".$row["cancel4"]."|".$row["cancelinfo4"]."|".$row["memo4"]."|0|");//86~90  

        // item5
        $data.=($row["lock5"]."|".$row["day5"]."|".$row["meal5"]."|".$row["family5"]."|".$row["service5"]."|");//91~95
        $data.=($row["joinmode5"]."|".$row["specialcase5"]."|".$row["traff5"]."|".$row["cost5"]."|".$row["pay5"]."|");//96~90
        $data.=($row["attend5"]."|".$row["regdate5"]."|".$row["paydate5"]."|".$row["payround5"]."|".$row["paybyid5"]."|");//101~105
        $data.=($row["paybyname5"]."|".$row["cancel5"]."|".$row["cancelinfo5"]."|".$row["memo5"]."|0|");//106~110 

        // item6
        $data.=($row["lock6"]."|".$row["day6"]."|".$row["meal6"]."|".$row["family6"]."|".$row["service6"]."|");//111~115
        $data.=($row["joinmode6"]."|".$row["specialcase6"]."|".$row["traff6"]."|".$row["cost6"]."|".$row["pay6"]."|");//116~120
        $data.=($row["attend6"]."|".$row["regdate6"]."|".$row["paydate6"]."|".$row["payround6"]."|".$row["paybyid6"]."|");//121~125
        $data.=($row["paybyname6"]."|".$row["cancel6"]."|".$row["cancelinfo6"]."|".$row["memo6"]."|0|");//126~130 
        
        // item7
        $data.=($row["lock7"]."|".$row["day7"]."|".$row["meal7"]."|".$row["family7"]."|".$row["service7"]."|");//131~135
        $data.=($row["joinmode7"]."|".$row["specialcase7"]."|".$row["traff7"]."|".$row["cost7"]."|".$row["pay7"]."|");//136~140
        $data.=($row["attend7"]."|".$row["regdate7"]."|".$row["paydate7"]."|".$row["payround7"]."|".$row["paybyid7"]."|");//141~145
        $data.=($row["paybyname7"]."|".$row["cancel7"]."|".$row["cancelinfo7"]."|".$row["memo7"]."|0|");//146~150         
        
        // item8
        $data.=($row["lock8"]."|".$row["day8"]."|".$row["meal8"]."|".$row["family8"]."|".$row["service8"]."|");//151~155
        $data.=($row["joinmode8"]."|".$row["specialcase8"]."|".$row["traff8"]."|".$row["cost8"]."|".$row["pay8"]."|");//156~160
        $data.=($row["attend8"]."|".$row["regdate8"]."|".$row["paydate8"]."|".$row["payround8"]."|".$row["paybyid8"]."|");//161~165
        $data.=($row["paybyname8"]."|".$row["cancel8"]."|".$row["cancelinfo8"]."|".$row["memo8"]."|0|");//166~170 
        
        $data.="0;";
        $nCnt++;
    }

    $result=$data;
    echo $result;
?>
