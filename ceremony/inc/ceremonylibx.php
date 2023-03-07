<?php
    function syncLeaderPujaTBX_OLD($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname,$mode){ // 幹部 TB01, TB02, TB03
        // 從sdm_clsmembers找出該班幹部出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdmdb`.`sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND (A.TTL_ID <> `學員`))";//echo $sql."<br>";
        $sql_result=mysqli_query($con,$sql);
        $numrows=mysqli_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;}}

        // 從法會報名表中找出該班幹部出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid>=5)";//echo $sql."<br>";
        $sql_result=mysqli_query($con,$sql);
        $numrows=mysqli_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja))
        {
            for($i=0;$i<count($memberinList);$i++)
            {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){if ($memberinList[$i][0]==$memberinPuja[$j][0]){$matchcnt++;$find=true;break;}}
                if ($find==false){break;}
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            for($j=0;$j<count($memberinPuja);$j++)
            {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }

        //$sql="select A.STU_ID,A.MembersSeq,B.idx,B.seq from `sdm_clsmembers` A LEFT JOIN `".$tbname."` B ON A.STU_ID=B.studentid WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否' AND B.CLS_ID='".$clsid."' AND B.titleid>=5)";
        //$sql_result=mysqli_query($con,$sql);
        //$numrows=mysqli_num_rows($sql_result);
        //$cnt=0;
        //if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinAll[$cnt][0]=$row[0];$memberinAll[$cnt][1]=$row[1];$memberinAll[$cnt][2]=$row[2];$memberinAll[$cnt][3]=$row[3];$cnt++;}}

        //echo "<br>in all ";	for($i=0;$i<count($memberinAll);$i++){echo "<br>".$memberinAll[$i][0];}
        //echo "<br>in cls member";for($i=0;$i<count($memberinList);$i++){echo "<br>".$memberinList[$i][0];}
        //echo "<br>in rollcall ";for($i=0;$i<count($memberinPuja);$i++){echo "<br>".$memberinPuja[$i][0];}

        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinPuja);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }
        //echo "<br>cls leader none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}
        //echo "<br>puja leader none ";	for($i=0;$i<count($rollcallnone);$i++){echo "<br>".$rollcallnone[$i];}

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);

        $sql_update="";
        // in clsmember but not in class join table -> 1.新幹部(insert)
        if (count($clsmembernone)>0&&$findleaderinfo==true){$classinfo=getClassInfo();}
        for($i=0;$i<count($clsmembernone);$i++)
        {
            $sqlstudent="select * from `sdmdb`.`sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysqli_query($con,$sqlstudent);
            $numrows=mysqli_num_rows($sqlstudent_result);
            if ($numrows>0)
            {
                $row_stu=mysql_fetch_array($sqlstudent_result, MYSQL_ASSOC);
                //echo "<br>".$clsmembernone[$i][0]."(加入)"; // Insert into
                $titleid=0;
                $title="";
                $staffid=0;//getStaffid($clsmembernone[$i][0]);
                if($clsmembernone[$i][2]=="班長"){$titleid=8;$title="班長";} //TB02
                else if($clsmembernone[$i][2]=="副班長"){$titleid=7;$title="副班長";} //TB03
                else if($clsmembernone[$i][2]=="關懷員"){$titleid=6;$title="關懷員";} //TB04
                else{continue;}

                $leader="";
                $leadermain="";// 幹部母班
                if($findleaderinfo==true)
                {
                    $leaderinfo=getLeaderInfo($clsmembernone[$i][0],true);
                    for($w=0;$w<count($leaderinfo);$w++)
                    {
                        for($x=0;$x<count($classinfo);$x++)
                        {
                            if($leaderinfo[$w][0]==$classinfo[$x][0])
                            {
                                $leaderinfo[$w][2]=$classinfo[$x][1];
                                $leaderinfo[$w][3]=$classinfo[$x][2];
                                break;
                            }
                        }
                    }
                    for($w=1;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
                    if(count($leaderinfo)>1){$leadermain=$leaderinfo[0][2];}
                }
                if ($row_stu["IsCurrent"] != 1){
                    $titleid=-10;
                }
                $birth = date('Y');
                if ($row_stu["Age"]){
                    $birth = (date('Y') - $row_stu["Age"])."-01-01";
                }
                if($mode==1)
                {
                    $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                    $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                    $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                    $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','".$leadermain."','".$classfullname."',0,0);";
                }
                else
                {
                    $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                    $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                    $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                    $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','".$leadermain."','".$classfullname."');";
                }
            }
        }
        // in rollcall but not in join table -> 1.幹部離開(insert)
        for($i=0;$i<count($pujanone);$i++)
        {
            $sql_update.="update `bwsouthdb`.".$tbname." set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `CLS_ID`='".$clsid."' AND `titleid`>=5);";
        }
        //echo $sql_update;
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd) > 0){
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysqli_query($con,$sql);}
            mysqli_commit($con);
        }

        $sql_leaderstatus="";
        // 一拼更新幹部在母班的 title id => 一起更新護持的班級
        for($i=0;$i<count($memberinList);$i++)
        {
            if($memberinList[$i][2]=="班長"){$titleid=4;}else if($memberinList[$i][2]=="副班長"){$titleid=3;}else if($memberinList[$i][2]=="關懷員"){$titleid=2;}else{continue;}
            $sqltmp="select idx,titleid from `".$tbname."` where (`STU_ID`='".$memberinList[$i][0]."' AND `titleid`<".$titleid.");";
            $sqltmp_result=mysqli_query($con,$sqltmp);
            $numrows=mysqli_num_rows($sqltmp_result);
            if ($numrows<=0){continue;}
            $row_tmp=mysql_fetch_array($sqltmp_result, MYSQL_NUM);
            //echo $sqltmp."<br>";
            if ($row_tmp[1]==$titleid){continue;}
            $sql_leaderstatus.="update `bwsouthdb`.".$tbname." set `titleid`=".$titleid." where (`idx`=".$row_tmp[0].");";
            //echo "<br>".$memberinList[$i][0];
        }
        $sqlcmdex=explode(";",$sql_leaderstatus);
        if (count($sqlcmdex) > 0){
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysqli_query($con,$sql);}
            mysqli_commit($con);
        }

        // 一併更新幹部的 memberseq
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++)
        {
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `seq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `bwsouthdb`.`".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0){
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysqli_query($con,$sql);}
            mysqli_commit($con);
        }

        return true;
    }

    function syncLeaderPujaTBX($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname,$mode){ // 班員 TB05, TB06
        $con = mysqli_connect("localhost","root","rinpoche");//connect_db("bwsouthdb");
        //從sdm_clsmembers找出該班學員出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdmdb`.`sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.TTL_ID <> '學員')";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){
            while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){
                $memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;
            }
        }

        // 從法會報名表中找出該班學員出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `bwsouthdb`.`".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid > 5)";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        $memberinPuja = [];
        if ($numrows>0){while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja)) {
            for($i=0;$i<count($memberinList);$i++) {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){
                    if ($memberinList[$i][0]==$memberinPuja[$j][0]){
                        $matchcnt++;
                        $find=true;
                        break;
                    }
                }
                if ($find==false){
                    break;
                }
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        $memberinAll=[];
        for($i=0;$i<count($memberinList);$i++) {
            for($j=0;$j<count($memberinPuja);$j++) {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }
        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        $pujanone = [];
        for($i=0;$i<count($memberinPuja);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);
        $sql_update="";
        // in clsmember but not in class join table -> 1.在別班?(YES:換班), 2.新學員(insert)
        //echo "<br>cls member none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}

        // 1.在別班?(YES:換班)
        /*if (count($clsmembernone)>0&&$findleaderinfo==true){
            //$classinfo=getClassInfo();
            $sql="select CLS_ID,Class,RegionCode from `sdmdb`.`sdm_classes` where (`ClsStatus`='Y')";
            $sql_result_classinfo=mysqli_query($con,$sql);
            $numrows=mysqli_num_rows($sql_result_classinfo);
            if($numrows<=0){
                $classinfo[0][0]='';
                $classinfo[0][1]='';
                $classinfo[0][2]='';
            } else {
                $cnt=0;
                while($row=mysqli_fetch_array($sql_result_classinfo, MYSQLI_NUM)){//MYSQL_NUM))//MYSQL_ASSOC))
                    $classinfo[$cnt][0]=$row[0];
                    $classinfo[$cnt][1]=$row[1];
                    $classinfo[$cnt][2]=$row[2];
                    $cnt++;
                }
            }
        }*/
        for($i=0;$i<count($clsmembernone);$i++){
            //$sqltemp="select A.STU_ID from `bwsouthdb`.`".$tbname."` A WHERE (A.STU_ID='".$clsmembernone[$i][0]."' AND A.titleid<5)";//echo $sqltemp;
            //$sqltemp_result=mysqli_query($con,$sqltemp);
            //$numrows=mysqli_num_rows($sqltemp_result);
            //if ($numrows>0){//在別班=>換到目前的班級
            //    $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$clsid."',`classname`='".$clsname."',`memberseq`=".$clsmembernone[$i][1]." where (`STU_ID`='".$clsmembernone[$i][0]."' AND `titleid`<5);";
            //    continue;
            //}

            // 法會報名表內也無此學員 => 新加入的學員,找出學員基本資料再 insert 到法會報名表
            $sqlstudent="select * from `sdmdb`.`sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysqli_query($con,$sqlstudent);
            $numrows=mysqli_num_rows($sqlstudent_result);
            if ($numrows<=0){continue;}
            $row_stu=mysqli_fetch_array($sqlstudent_result, MYSQLI_ASSOC);

            $leader="";
            /*if($findleaderinfo==true) {
                $leaderinfo=getLeaderInfo($clsmembernone[$i][0],false);
                for($w=0;$w<count($leaderinfo);$w++)
                {
                    for($x=0;$x<count($classinfo);$x++)
                    {
                        if($leaderinfo[$w][0]==$classinfo[$x][0])
                        {
                            $leaderinfo[$w][2]=$classinfo[$x][1];
                            $leaderinfo[$w][3]=$classinfo[$x][2];
                            break;
                        }
                    }
                }
                for($w=0;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
            }*/

            $titleid=1;
            $title="學員";
            $staffid=0;//getStaffid($clsmembernone[$i][0]);
            if($clsmembernone[$i][2]=="班長"){$titleid=10; $title="班長";}
            if($clsmembernone[$i][2]=="副班長"){$titleid=8; $title="副班長";}
            if($clsmembernone[$i][2]=="關懷員"){$titleid=6; $title="關懷員";}
            if ($row_stu["IsCurrent"] != "1"){
                $titleid=-10;
            }
            $birth = date('Y');
            if ($row_stu["Age"]){
                $birth = (date('Y') - $row_stu["Age"])."-01-01";
            }

            if($mode==1) {
                $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."',0,0);";
            } else {
                $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."');";
            }
        }
        //echo $sql_update;echo "<br>";return;

        // in puja but not in join table -> 1.調到別班?(YES:換班), 2.離開(insert)
        for($i=0;$i<count($pujanone);$i++) {
            $sqltemp="select * from `sdmdb`.`sdm_clsmembers` A WHERE (A.STU_ID='".$pujanone[$i][0]."' AND A.IsMajor='是')";
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows<=0){//找不到此人....休學或離開
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`> 5);";
                continue;
            }

            $row=mysql_fetch_array($sqltemp_result, MYSQL_ASSOC);
            if($row["Status"]=="離開") {
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                //echo "<br>".$pujanone[$i][0]."(離開)";// update CLS_ID, Name and vailddate ad empty
            }else if ($row["CLS_ID"]!=$clsid){
                $sqlclasses="select Class from `sdmdb`.`sdm_classes` WHERE (`CLS_ID`='".$row["CLS_ID"]."')";
                $sqlclasses_result=mysqli_query($con,$sqlclasses);
                $numrows=mysqli_num_rows($sqlclasses_result);
                $classname="";
                if ($numrows>0)
                {
                    $row_classes=mysql_fetch_array($sqlclasses_result, MYSQL_ASSOC);
                    $tmp=explode("-",$row_classes["Class"]);
                    $classname=$tmp[0];
                }
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$row["CLS_ID"]."',`classname`='".$classname."' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                // find the class name from sdm_classes
                // echo "<br>".$pujanone[$i][0]."(調出)";// update CLS_ID, Name and vailddate
            }
        }
        //echo $sql_update;//echo "<br>";
        //echo "<br>rollcall none ";	for($i=0;$i<count($pujanone);$i++){echo "<br>".$pujanone[$i][0];}

        // change something
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd)>0) {
            //for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmd); $i++){
                $sql=$sqlcmd[$i];
                if ($sql == "") {
                    continue;
                }
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }

        // 一併更新學員的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++){
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `memberseq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `bwsouthdb`.`".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0){
            for($i = 0; $i < count($sqlcmdex); $i++){
                $sql = $sqlcmdex[$i];
                if ($sql == "") {
                    continue;
                }
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }
        mysqli_close($con);
        return true;
    }

    function syncMemberPujaTBX($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname,$mode){ // 班員 TB05, TB06
        $con = mysqli_connect("localhost","root","rinpoche");//connect_db("bwsouthdb");
        //從sdm_clsmembers找出該班學員出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdmdb`.`sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.TTL_ID='學員')";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){
            while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){
                $memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;
            }
        }

        // 從法會報名表中找出該班學員出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `bwsouthdb`.`".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid<5)";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        $memberinPuja = [];
        if ($numrows>0){while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja)) {
            for($i=0;$i<count($memberinList);$i++) {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){
                    if ($memberinList[$i][0]==$memberinPuja[$j][0]){
                        $matchcnt++;
                        $find=true;
                        break;
                    }
                }
                if ($find==false){
                    break;
                }
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        $memberinAll=[];
        for($i=0;$i<count($memberinList);$i++) {
            for($j=0;$j<count($memberinPuja);$j++) {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }
        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        $pujanone = [];
        for($i=0;$i<count($memberinPuja);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);
        $sql_update="";
        // in clsmember but not in class join table -> 1.在別班?(YES:換班), 2.新學員(insert)
        //echo "<br>cls member none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}

        // 1.在別班?(YES:換班)
        /*if (count($clsmembernone)>0&&$findleaderinfo==true){
            //$classinfo=getClassInfo();
            $sql="select CLS_ID,Class,RegionCode from `sdmdb`.`sdm_classes` where (`ClsStatus`='Y')";
            $sql_result_classinfo=mysqli_query($con,$sql);
            $numrows=mysqli_num_rows($sql_result_classinfo);
            if($numrows<=0){
                $classinfo[0][0]='';
                $classinfo[0][1]='';
                $classinfo[0][2]='';
            } else {
                $cnt=0;
                while($row=mysqli_fetch_array($sql_result_classinfo, MYSQLI_NUM)){//MYSQL_NUM))//MYSQL_ASSOC))
                    $classinfo[$cnt][0]=$row[0];
                    $classinfo[$cnt][1]=$row[1];
                    $classinfo[$cnt][2]=$row[2];
                    $cnt++;
                }
            }
        }*/
        for($i=0;$i<count($clsmembernone);$i++){
            $sqltemp="select A.STU_ID from `bwsouthdb`.`".$tbname."` A WHERE (A.STU_ID='".$clsmembernone[$i][0]."' AND A.titleid<5)";//echo $sqltemp;
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows>0){//在別班=>換到目前的班級
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$clsid."',`classname`='".$clsname."',`memberseq`=".$clsmembernone[$i][1]." where (`STU_ID`='".$clsmembernone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            // 法會報名表內也無此學員 => 新加入的學員,找出學員基本資料再 insert 到法會報名表
            $sqlstudent="select * from `sdmdb`.`sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysqli_query($con,$sqlstudent);
            $numrows=mysqli_num_rows($sqlstudent_result);
            if ($numrows<=0){continue;}
            $row_stu=mysqli_fetch_array($sqlstudent_result, MYSQLI_ASSOC);

            $leader="";
            /*if($findleaderinfo==true) {
                $leaderinfo=getLeaderInfo($clsmembernone[$i][0],false);
                for($w=0;$w<count($leaderinfo);$w++)
                {
                    for($x=0;$x<count($classinfo);$x++)
                    {
                        if($leaderinfo[$w][0]==$classinfo[$x][0])
                        {
                            $leaderinfo[$w][2]=$classinfo[$x][1];
                            $leaderinfo[$w][3]=$classinfo[$x][2];
                            break;
                        }
                    }
                }
                for($w=0;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
            }*/

            $titleid=1;
            $title="學員";
            $staffid=0;//getStaffid($clsmembernone[$i][0]);
            if($clsmembernone[$i][2]=="學員"){$titleid=0;}
            if ($row_stu["IsCurrent"] != "1"){
                $titleid=-10;
            }
            $birth = date('Y');
            if ($row_stu["Age"]){
                $birth = (date('Y') - $row_stu["Age"])."-01-01";
            }

            if($mode==1) {
                $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."',0,0);";
            } else {
                $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."');";
            }
        }
        //echo $sql_update;echo "<br>";return;

        // in puja but not in join table -> 1.調到別班?(YES:換班), 2.離開(insert)
        for($i=0;$i<count($pujanone);$i++) {
            $sqltemp="select * from `sdmdb`.`sdm_clsmembers` A WHERE (A.STU_ID='".$pujanone[$i][0]."' AND A.IsMajor='是')";
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows<=0){//找不到此人....休學或離開
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            $row=mysql_fetch_array($sqltemp_result, MYSQL_ASSOC);
            if($row["Status"]=="離開") {
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                //echo "<br>".$pujanone[$i][0]."(離開)";// update CLS_ID, Name and vailddate ad empty
            }else if ($row["CLS_ID"]!=$clsid){
                $sqlclasses="select Class from `sdmdb`.`sdm_classes` WHERE (`CLS_ID`='".$row["CLS_ID"]."')";
                $sqlclasses_result=mysqli_query($con,$sqlclasses);
                $numrows=mysqli_num_rows($sqlclasses_result);
                $classname="";
                if ($numrows>0)
                {
                    $row_classes=mysql_fetch_array($sqlclasses_result, MYSQL_ASSOC);
                    $tmp=explode("-",$row_classes["Class"]);
                    $classname=$tmp[0];
                }
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$row["CLS_ID"]."',`classname`='".$classname."' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                // find the class name from sdm_classes
                // echo "<br>".$pujanone[$i][0]."(調出)";// update CLS_ID, Name and vailddate
            }
        }
        //echo $sql_update;//echo "<br>";
        //echo "<br>rollcall none ";	for($i=0;$i<count($pujanone);$i++){echo "<br>".$pujanone[$i][0];}

        // change something
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd)>0) {
            //for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysqli_query($con,$sql);}
            mysqli_commit($con);
        }

        // 一併更新學員的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++){
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `memberseq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `bwsouthdb`.`".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0){
            for($i = 0; $i < count($sqlcmdex); $i++){
                $sql=$sqlcmdex[$i];
                if ($sql == "") {continue;}
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }
        mysqli_close($con);
        return true;
    }

    // 八關齋戒 2
    function syncLeaderPujaTB2($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname,$mode){ // 班員 TB05, TB06
        $con = mysqli_connect("localhost","root","rinpoche");//connect_db("bwsouthdb");
        //從sdm_clsmembers找出該班學員出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdmdb`.`sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.TTL_ID <> '學員')";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){
            while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){
                $memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;
            }
        }

        // 從法會報名表中找出該班學員出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `bwsouthdb`.`".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid > 5)";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        $memberinPuja = [];
        if ($numrows>0){while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja)) {
            for($i=0;$i<count($memberinList);$i++) {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){
                    if ($memberinList[$i][0]==$memberinPuja[$j][0]){
                        $matchcnt++;
                        $find=true;
                        break;
                    }
                }
                if ($find==false){
                    break;
                }
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        $memberinAll=[];
        for($i=0;$i<count($memberinList);$i++) {
            for($j=0;$j<count($memberinPuja);$j++) {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }
        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        $pujanone = [];
        for($i=0;$i<count($memberinPuja);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);
        $sql_update="";
        // in clsmember but not in class join table -> 1.在別班?(YES:換班), 2.新學員(insert)
        //echo "<br>cls member none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}

        // 1.在別班?(YES:換班)
        /*if (count($clsmembernone)>0&&$findleaderinfo==true){
            //$classinfo=getClassInfo();
            $sql="select CLS_ID,Class,RegionCode from `sdmdb`.`sdm_classes` where (`ClsStatus`='Y')";
            $sql_result_classinfo=mysqli_query($con,$sql);
            $numrows=mysqli_num_rows($sql_result_classinfo);
            if($numrows<=0){
                $classinfo[0][0]='';
                $classinfo[0][1]='';
                $classinfo[0][2]='';
            } else {
                $cnt=0;
                while($row=mysqli_fetch_array($sql_result_classinfo, MYSQLI_NUM)){//MYSQL_NUM))//MYSQL_ASSOC))
                    $classinfo[$cnt][0]=$row[0];
                    $classinfo[$cnt][1]=$row[1];
                    $classinfo[$cnt][2]=$row[2];
                    $cnt++;
                }
            }
        }*/
        for($i=0;$i<count($clsmembernone);$i++){
            //$sqltemp="select A.STU_ID from `bwsouthdb`.`".$tbname."` A WHERE (A.STU_ID='".$clsmembernone[$i][0]."' AND A.titleid<5)";//echo $sqltemp;
            //$sqltemp_result=mysqli_query($con,$sqltemp);
            //$numrows=mysqli_num_rows($sqltemp_result);
            //if ($numrows>0){//在別班=>換到目前的班級
            //    $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$clsid."',`classname`='".$clsname."',`memberseq`=".$clsmembernone[$i][1]." where (`STU_ID`='".$clsmembernone[$i][0]."' AND `titleid`<5);";
            //    continue;
            //}

            // 法會報名表內也無此學員 => 新加入的學員,找出學員基本資料再 insert 到法會報名表
            $sqlstudent="select * from `sdmdb`.`sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysqli_query($con,$sqlstudent);
            $numrows=mysqli_num_rows($sqlstudent_result);
            if ($numrows<=0){continue;}
            $row_stu=mysqli_fetch_array($sqlstudent_result, MYSQLI_ASSOC);

            $leader="";
            /*if($findleaderinfo==true) {
                $leaderinfo=getLeaderInfo($clsmembernone[$i][0],false);
                for($w=0;$w<count($leaderinfo);$w++)
                {
                    for($x=0;$x<count($classinfo);$x++)
                    {
                        if($leaderinfo[$w][0]==$classinfo[$x][0])
                        {
                            $leaderinfo[$w][2]=$classinfo[$x][1];
                            $leaderinfo[$w][3]=$classinfo[$x][2];
                            break;
                        }
                    }
                }
                for($w=0;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
            }*/

            $titleid=1;
            $title="學員";
            $staffid=0;//getStaffid($clsmembernone[$i][0]);
            if($clsmembernone[$i][2]=="班長"){$titleid=10; $title="班長";}
            if($clsmembernone[$i][2]=="副班長"){$titleid=8; $title="副班長";}
            if($clsmembernone[$i][2]=="關懷員"){$titleid=6; $title="關懷員";}
            if ($row_stu["IsCurrent"] != "1"){
                $titleid=-10;
            }
            $birth = date('Y');
            if ($row_stu["Age"]){
                $birth = (date('Y') - $row_stu["Age"])."-01-01";
            }

            if($mode==1) {
                $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."',0,0,0,'');";
            } else {
                $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."',0,'');";
            }
        }
        //echo $sql_update;echo "<br>";return;

        // in puja but not in join table -> 1.調到別班?(YES:換班), 2.離開(insert)
        for($i=0;$i<count($pujanone);$i++) {
            $sqltemp="select * from `sdmdb`.`sdm_clsmembers` A WHERE (A.STU_ID='".$pujanone[$i][0]."' AND A.IsMajor='是')";
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows<=0){//找不到此人....休學或離開
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`> 5);";
                continue;
            }

            $row=mysql_fetch_array($sqltemp_result, MYSQL_ASSOC);
            if($row["Status"]=="離開") {
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                //echo "<br>".$pujanone[$i][0]."(離開)";// update CLS_ID, Name and vailddate ad empty
            }else if ($row["CLS_ID"]!=$clsid){
                $sqlclasses="select Class from `sdmdb`.`sdm_classes` WHERE (`CLS_ID`='".$row["CLS_ID"]."')";
                $sqlclasses_result=mysqli_query($con,$sqlclasses);
                $numrows=mysqli_num_rows($sqlclasses_result);
                $classname="";
                if ($numrows>0)
                {
                    $row_classes=mysql_fetch_array($sqlclasses_result, MYSQL_ASSOC);
                    $tmp=explode("-",$row_classes["Class"]);
                    $classname=$tmp[0];
                }
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$row["CLS_ID"]."',`classname`='".$classname."' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                // find the class name from sdm_classes
                // echo "<br>".$pujanone[$i][0]."(調出)";// update CLS_ID, Name and vailddate
            }
        }
        //echo $sql_update;//echo "<br>";
        //echo "<br>rollcall none ";	for($i=0;$i<count($pujanone);$i++){echo "<br>".$pujanone[$i][0];}

        // change something
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd)>0) {
            //for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmd); $i++){
                $sql=$sqlcmd[$i];
                if ($sql == "") {
                    continue;
                }
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }

        // 一併更新學員的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++){
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `memberseq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `bwsouthdb`.`".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0){
            for($i = 0; $i < count($sqlcmdex); $i++){
                $sql = $sqlcmdex[$i];
                if ($sql == "") {
                    continue;
                }
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }
        mysqli_close($con);
        return true;
    }

    function syncMemberPujaTB2($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname,$mode){ // 班員 TB05, TB06
        $con = mysqli_connect("localhost","root","rinpoche");//connect_db("bwsouthdb");
        //從sdm_clsmembers找出該班學員出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdmdb`.`sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.TTL_ID='學員')";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){
            while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){
                $memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;
            }
        }

        // 從法會報名表中找出該班學員出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `bwsouthdb`.`".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid<5)";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        $memberinPuja = [];
        if ($numrows>0){while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja)) {
            for($i=0;$i<count($memberinList);$i++) {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){
                    if ($memberinList[$i][0]==$memberinPuja[$j][0]){
                        $matchcnt++;
                        $find=true;
                        break;
                    }
                }
                if ($find==false){
                    break;
                }
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        $memberinAll=[];
        for($i=0;$i<count($memberinList);$i++) {
            for($j=0;$j<count($memberinPuja);$j++) {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }
        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        $pujanone = [];
        for($i=0;$i<count($memberinPuja);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);
        $sql_update="";
        // in clsmember but not in class join table -> 1.在別班?(YES:換班), 2.新學員(insert)
        //echo "<br>cls member none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}

        // 1.在別班?(YES:換班)
        /*if (count($clsmembernone)>0&&$findleaderinfo==true){
            //$classinfo=getClassInfo();
            $sql="select CLS_ID,Class,RegionCode from `sdmdb`.`sdm_classes` where (`ClsStatus`='Y')";
            $sql_result_classinfo=mysqli_query($con,$sql);
            $numrows=mysqli_num_rows($sql_result_classinfo);
            if($numrows<=0){
                $classinfo[0][0]='';
                $classinfo[0][1]='';
                $classinfo[0][2]='';
            } else {
                $cnt=0;
                while($row=mysqli_fetch_array($sql_result_classinfo, MYSQLI_NUM)){//MYSQL_NUM))//MYSQL_ASSOC))
                    $classinfo[$cnt][0]=$row[0];
                    $classinfo[$cnt][1]=$row[1];
                    $classinfo[$cnt][2]=$row[2];
                    $cnt++;
                }
            }
        }*/
        for($i=0;$i<count($clsmembernone);$i++){
            $sqltemp="select A.STU_ID from `bwsouthdb`.`".$tbname."` A WHERE (A.STU_ID='".$clsmembernone[$i][0]."' AND A.titleid<5)";//echo $sqltemp;
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows>0){//在別班=>換到目前的班級
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$clsid."',`classname`='".$clsname."',`memberseq`=".$clsmembernone[$i][1]." where (`STU_ID`='".$clsmembernone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            // 法會報名表內也無此學員 => 新加入的學員,找出學員基本資料再 insert 到法會報名表
            $sqlstudent="select * from `sdmdb`.`sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysqli_query($con,$sqlstudent);
            $numrows=mysqli_num_rows($sqlstudent_result);
            if ($numrows<=0){continue;}
            $row_stu=mysqli_fetch_array($sqlstudent_result, MYSQLI_ASSOC);

            $leader="";
            /*if($findleaderinfo==true) {
                $leaderinfo=getLeaderInfo($clsmembernone[$i][0],false);
                for($w=0;$w<count($leaderinfo);$w++)
                {
                    for($x=0;$x<count($classinfo);$x++)
                    {
                        if($leaderinfo[$w][0]==$classinfo[$x][0])
                        {
                            $leaderinfo[$w][2]=$classinfo[$x][1];
                            $leaderinfo[$w][3]=$classinfo[$x][2];
                            break;
                        }
                    }
                }
                for($w=0;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
            }*/

            $titleid=1;
            $title="學員";
            $staffid=0;//getStaffid($clsmembernone[$i][0]);
            if($clsmembernone[$i][2]=="學員"){$titleid=0;}
            if ($row_stu["IsCurrent"] != "1"){
                $titleid=-10;
            }
            $birth = date('Y');
            if ($row_stu["Age"]){
                $birth = (date('Y') - $row_stu["Age"])."-01-01";
            }

            if($mode==1) {
                $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."',0,0,0,'');";
            } else {
                $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."',0,'');";
            }
        }
        //echo $sql_update;echo "<br>";return;

        // in puja but not in join table -> 1.調到別班?(YES:換班), 2.離開(insert)
        for($i=0;$i<count($pujanone);$i++) {
            $sqltemp="select * from `sdmdb`.`sdm_clsmembers` A WHERE (A.STU_ID='".$pujanone[$i][0]."' AND A.IsMajor='是')";
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows<=0){//找不到此人....休學或離開
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            $row=mysql_fetch_array($sqltemp_result, MYSQL_ASSOC);
            if($row["Status"]=="離開") {
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                //echo "<br>".$pujanone[$i][0]."(離開)";// update CLS_ID, Name and vailddate ad empty
            }else if ($row["CLS_ID"]!=$clsid){
                $sqlclasses="select Class from `sdmdb`.`sdm_classes` WHERE (`CLS_ID`='".$row["CLS_ID"]."')";
                $sqlclasses_result=mysqli_query($con,$sqlclasses);
                $numrows=mysqli_num_rows($sqlclasses_result);
                $classname="";
                if ($numrows>0)
                {
                    $row_classes=mysql_fetch_array($sqlclasses_result, MYSQL_ASSOC);
                    $tmp=explode("-",$row_classes["Class"]);
                    $classname=$tmp[0];
                }
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$row["CLS_ID"]."',`classname`='".$classname."' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                // find the class name from sdm_classes
                // echo "<br>".$pujanone[$i][0]."(調出)";// update CLS_ID, Name and vailddate
            }
        }
        //echo $sql_update;//echo "<br>";
        //echo "<br>rollcall none ";	for($i=0;$i<count($pujanone);$i++){echo "<br>".$pujanone[$i][0];}

        // change something
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd)>0) {
            //for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysqli_query($con,$sql);}
            mysqli_commit($con);
        }

        // 一併更新學員的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++){
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `memberseq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `bwsouthdb`.`".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0){
            for($i = 0; $i < count($sqlcmdex); $i++){
                $sql=$sqlcmdex[$i];
                if ($sql == "") {continue;}
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }
        mysqli_close($con);
        return true;
    }

    // 多場次 5
    function syncLeaderpujadbNEW($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname) {
        // 從sdm_clsmembers找出該班幹部出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否')";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;}}

        // 從法會報名表中找出該班幹部出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid>=5)";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja))
        {
            for($i=0;$i<count($memberinList);$i++)
            {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){if ($memberinList[$i][0]==$memberinPuja[$j][0]){$matchcnt++;$find=true;break;}}
                if ($find==false){break;}
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            for($j=0;$j<count($memberinPuja);$j++)
            {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }

        //$sql="select A.STU_ID,A.MembersSeq,B.idx,B.seq from `sdm_clsmembers` A LEFT JOIN `".$tbname."` B ON A.STU_ID=B.studentid WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否' AND B.CLS_ID='".$clsid."' AND B.titleid>=5)";
        //$sql_result=mysql_query($sql);
        //$numrows=mysql_num_rows($sql_result);
        //$cnt=0;
        //if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinAll[$cnt][0]=$row[0];$memberinAll[$cnt][1]=$row[1];$memberinAll[$cnt][2]=$row[2];$memberinAll[$cnt][3]=$row[3];$cnt++;}}

        //echo "<br>in all ";	for($i=0;$i<count($memberinAll);$i++){echo "<br>".$memberinAll[$i][0];}
        //echo "<br>in cls member";for($i=0;$i<count($memberinList);$i++){echo "<br>".$memberinList[$i][0];}
        //echo "<br>in rollcall ";for($i=0;$i<count($memberinPuja);$i++){echo "<br>".$memberinPuja[$i][0];}

        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinPuja);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }
        //echo "<br>cls leader none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}
        //echo "<br>puja leader none ";	for($i=0;$i<count($rollcallnone);$i++){echo "<br>".$rollcallnone[$i];}

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);

        $sql_update="";
        $dayitem="0,0,0,0,0,0,0,'Z,0,Z,0',0,0,0,'1970-01-01','1970-01-01',0,'','',0,'',''";
        // in clsmember but not in class join table -> 1.新幹部(insert)
        if (count($clsmembernone)>0&&$findleaderinfo==true){$classinfo=getClassInfo();}
        for($i=0;$i<count($clsmembernone);$i++)
        {
            $sqlstudent="select * from `sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysql_query($sqlstudent);
            $numrows=mysql_num_rows($sqlstudent_result);
            if ($numrows>0)
            {
                $row_stu=mysql_fetch_array($sqlstudent_result, MYSQL_ASSOC);
                //echo "<br>".$clsmembernone[$i][0]."(加入)"; // Insert into
                $titleid=0;
                $title="";
                $staffid=getStaffid($clsmembernone[$i][0]);
                if($clsmembernone[$i][2]=="TB02"){$titleid=8;$title="班長";}
                else if($clsmembernone[$i][2]=="TB03"){$titleid=7;$title="副班長";}
                else if($clsmembernone[$i][2]=="TB04"){$titleid=6;$title="關懷員";}
                else{continue;}

                $leader="";
                $leadermain="";// 幹部母班
                if($findleaderinfo==true)
                {
                    $leaderinfo=getLeaderInfo($clsmembernone[$i][0],true);
                    for($w=0;$w<count($leaderinfo);$w++)
                    {
                        for($x=0;$x<count($classinfo);$x++)
                        {
                            if($leaderinfo[$w][0]==$classinfo[$x][0])
                            {
                                $leaderinfo[$w][2]=$classinfo[$x][1];
                                $leaderinfo[$w][3]=$classinfo[$x][2];
                                break;
                            }
                        }
                    }
                    for($w=1;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
                    if(count($leaderinfo)>1){$leadermain=$leaderinfo[0][2];}
                }

                // INSERT COMMAND
                $sql_update.="insert into `".$tbname."` values (NULL,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."',";
                $sql_update.="'".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."',";
                $sql_update.="'".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="'".$leader."','','".$leadermain."','".$classfullname."',";
                $sql_update.=$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.");";
            }
        }
        // in rollcall but not in join table -> 1.幹部離開(insert)
        for($i=0;$i<count($pujanone);$i++)
        {
            $sql_update.="update ".$tbname." set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `CLS_ID`='".$clsid."' AND `titleid`>=5);";
        }
        //echo $sql_update;
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd) > 0)
        {
            mysql_query("SET autocommit=0");
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        $sql_leaderstatus="";
        // 一拼更新幹部在母班的 title id => 一起更新護持的班級
        for($i=0;$i<count($memberinList);$i++)
        {
            if($memberinList[$i][2]=="TB02"){$titleid=4;}else if($memberinList[$i][2]=="TB03"){$titleid=3;}else if($memberinList[$i][2]=="TB04"){$titleid=2;}else{continue;}
            $sqltmp="select idx,titleid from `".$tbname."` where (`STU_ID`='".$memberinList[$i][0]."' AND `titleid`<".$titleid.");";
            $sqltmp_result=mysql_query($sqltmp);
            $numrows=mysql_num_rows($sqltmp_result);
            if ($numrows<=0){continue;}
            $row_tmp=mysql_fetch_array($sqltmp_result, MYSQL_NUM);
            //echo $sqltmp."<br>";
            if ($row_tmp[1]==$titleid){continue;}
            $sql_leaderstatus.="update ".$tbname." set `titleid`=".$titleid." where (`idx`=".$row_tmp[0].");";
            //echo "<br>".$memberinList[$i][0];
        }
        $sqlcmdex=explode(";",$sql_leaderstatus);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        // 一併更新幹部的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++)
        {
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `seq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        return true;
    }

    function syncMemberpujadbNEW($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname) {
        $con = mysqli_connect("localhost","root","rinpoche");//connect_db("bwsouthdb");
        //從sdm_clsmembers找出該班學員出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdmdb`.`sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.TTL_ID='學員')";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){
            while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){
                $memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;
            }
        }

        // 從法會報名表中找出該班學員出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `bwsouthdb`.`".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid<5)";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        $memberinPuja = [];
        if ($numrows>0){while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja)) {
            for($i=0;$i<count($memberinList);$i++) {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){
                    if ($memberinList[$i][0]==$memberinPuja[$j][0]){
                        $matchcnt++;
                        $find=true;
                        break;
                    }
                }
                if ($find==false){
                    break;
                }
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        $memberinAll=[];
        for($i=0;$i<count($memberinList);$i++) {
            for($j=0;$j<count($memberinPuja);$j++) {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }
        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        $pujanone = [];
        for($i=0;$i<count($memberinPuja);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);
        $sql_update="";
        // in clsmember but not in class join table -> 1.在別班?(YES:換班), 2.新學員(insert)
        //echo "<br>cls member none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}

        // 1.在別班?(YES:換班)
        $dayitem="0,0,0,0,0,0,0,'Z,0,Z,0',0,0,0,'1970-01-01','1970-01-01',0,'','',0,'',''";
        for($i=0;$i<count($clsmembernone);$i++){
            $sqltemp="select A.STU_ID from `bwsouthdb`.`".$tbname."` A WHERE (A.STU_ID='".$clsmembernone[$i][0]."' AND A.titleid<5)";//echo $sqltemp;
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows>0){//在別班=>換到目前的班級
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$clsid."',`classname`='".$clsname."',`memberseq`=".$clsmembernone[$i][1]." where (`STU_ID`='".$clsmembernone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            // 法會報名表內也無此學員 => 新加入的學員,找出學員基本資料再 insert 到法會報名表
            $sqlstudent="select * from `sdmdb`.`sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysqli_query($con,$sqlstudent);
            $numrows=mysqli_num_rows($sqlstudent_result);
            if ($numrows<=0){continue;}
            $row_stu=mysqli_fetch_array($sqlstudent_result, MYSQLI_ASSOC);

            $leader="";
            $titleid=1;
            $title="學員";
            $staffid=0;//getStaffid($clsmembernone[$i][0]);
            if($clsmembernone[$i][2]=="學員"){$titleid=0;}
            if ($row_stu["IsCurrent"] != "1"){
                $titleid=-10;
            }
            $birth = date('Y');
            if ($row_stu["Age"]){
                $birth = (date('Y') - $row_stu["Age"])."-01-01";
            }

            // INSERT COMMAND
            $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
            $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
            $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
            $sql_update.="'".$leader."','','','".$classfullname."',";
            $sql_update.=$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.");";
        }
        //echo $sql_update;echo "<br>";return;

        // in puja but not in join table -> 1.調到別班?(YES:換班), 2.離開(insert)
        for($i=0;$i<count($pujanone);$i++) {
            $sqltemp="select * from `sdmdb`.`sdm_clsmembers` A WHERE (A.STU_ID='".$pujanone[$i][0]."' AND A.IsMajor='是')";
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows<=0){//找不到此人....休學或離開
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            $row=mysql_fetch_array($sqltemp_result, MYSQL_ASSOC);
            if($row["Status"]=="離開") {
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                //echo "<br>".$pujanone[$i][0]."(離開)";// update CLS_ID, Name and vailddate ad empty
            }else if ($row["CLS_ID"]!=$clsid){
                $sqlclasses="select Class from `sdmdb`.`sdm_classes` WHERE (`CLS_ID`='".$row["CLS_ID"]."')";
                $sqlclasses_result=mysqli_query($con,$sqlclasses);
                $numrows=mysqli_num_rows($sqlclasses_result);
                $classname="";
                if ($numrows>0)
                {
                    $row_classes=mysql_fetch_array($sqlclasses_result, MYSQL_ASSOC);
                    $tmp=explode("-",$row_classes["Class"]);
                    $classname=$tmp[0];
                }
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$row["CLS_ID"]."',`classname`='".$classname."' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                // find the class name from sdm_classes
                // echo "<br>".$pujanone[$i][0]."(調出)";// update CLS_ID, Name and vailddate
            }
        }
        //echo $sql_update;//echo "<br>";
        //echo "<br>rollcall none ";	for($i=0;$i<count($pujanone);$i++){echo "<br>".$pujanone[$i][0];}

        // change something
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd)>0) {
            //for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmd); $i++){
                $sql=$sqlcmd[$i];
                if ($sql==''){continue;}
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }

        // 一併更新學員的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++){
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `memberseq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `bwsouthdb`.`".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0){
            for($i = 0; $i < count($sqlcmdex); $i++){
                $sql=$sqlcmdex[$i];
                if ($sql==''){continue;}
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }
        mysqli_close($con);
        return true;
    }

    // 多場次 8
    function syncLeaderpujadbxNEW($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname) // 幹部 TB01, TB02, TB03
    {
        // 從sdm_clsmembers找出該班幹部出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否')";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;}}

        // 從法會報名表中找出該班幹部出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid>=5)";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja))
        {
            for($i=0;$i<count($memberinList);$i++)
            {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){if ($memberinList[$i][0]==$memberinPuja[$j][0]){$matchcnt++;$find=true;break;}}
                if ($find==false){break;}
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            for($j=0;$j<count($memberinPuja);$j++)
            {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }

        //$sql="select A.STU_ID,A.MembersSeq,B.idx,B.seq from `sdm_clsmembers` A LEFT JOIN `".$tbname."` B ON A.STU_ID=B.studentid WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否' AND B.CLS_ID='".$clsid."' AND B.titleid>=5)";
        //$sql_result=mysql_query($sql);
        //$numrows=mysql_num_rows($sql_result);
        //$cnt=0;
        //if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinAll[$cnt][0]=$row[0];$memberinAll[$cnt][1]=$row[1];$memberinAll[$cnt][2]=$row[2];$memberinAll[$cnt][3]=$row[3];$cnt++;}}

        //echo "<br>in all ";	for($i=0;$i<count($memberinAll);$i++){echo "<br>".$memberinAll[$i][0];}
        //echo "<br>in cls member";for($i=0;$i<count($memberinList);$i++){echo "<br>".$memberinList[$i][0];}
        //echo "<br>in rollcall ";for($i=0;$i<count($memberinPuja);$i++){echo "<br>".$memberinPuja[$i][0];}

        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinPuja);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }
        //echo "<br>cls leader none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}
        //echo "<br>puja leader none ";	for($i=0;$i<count($rollcallnone);$i++){echo "<br>".$rollcallnone[$i];}

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);

        $sql_update="";
        $dayitem="0,0,0,0,0,0,0,'Z,0,Z,0',0,0,0,'1970-01-01','1970-01-01',0,'','',0,'',''";
        // in clsmember but not in class join table -> 1.新幹部(insert)
        if (count($clsmembernone)>0&&$findleaderinfo==true){$classinfo=getClassInfo();}
        for($i=0;$i<count($clsmembernone);$i++)
        {
            $sqlstudent="select * from `sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysql_query($sqlstudent);
            $numrows=mysql_num_rows($sqlstudent_result);
            if ($numrows>0)
            {
                $row_stu=mysql_fetch_array($sqlstudent_result, MYSQL_ASSOC);
                //echo "<br>".$clsmembernone[$i][0]."(加入)"; // Insert into
                $titleid=0;
                $title="";
                $staffid=getStaffid($clsmembernone[$i][0]);
                if($clsmembernone[$i][2]=="TB02"){$titleid=8;$title="班長";}
                else if($clsmembernone[$i][2]=="TB03"){$titleid=7;$title="副班長";}
                else if($clsmembernone[$i][2]=="TB04"){$titleid=6;$title="關懷員";}
                else{continue;}

                $leader="";
                $leadermain="";// 幹部母班
                if($findleaderinfo==true)
                {
                    $leaderinfo=getLeaderInfo($clsmembernone[$i][0],true);
                    for($w=0;$w<count($leaderinfo);$w++)
                    {
                        for($x=0;$x<count($classinfo);$x++)
                        {
                            if($leaderinfo[$w][0]==$classinfo[$x][0])
                            {
                                $leaderinfo[$w][2]=$classinfo[$x][1];
                                $leaderinfo[$w][3]=$classinfo[$x][2];
                                break;
                            }
                        }
                    }
                    for($w=1;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
                    if(count($leaderinfo)>1){$leadermain=$leaderinfo[0][2];}
                }

                // INSERT COMMAND
                $sql_update.="insert into `".$tbname."` values (NULL,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."',";
                $sql_update.="'".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."',";
                $sql_update.="'".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="'".$leader."','','".$leadermain."','".$classfullname."',";
                $sql_update.=$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.");";
            }
        }
        // in rollcall but not in join table -> 1.幹部離開(insert)
        for($i=0;$i<count($pujanone);$i++)
        {
            $sql_update.="update ".$tbname." set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `CLS_ID`='".$clsid."' AND `titleid`>=5);";
        }
        //echo $sql_update;
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd) > 0)
        {
            mysql_query("SET autocommit=0");
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        $sql_leaderstatus="";
        // 一拼更新幹部在母班的 title id => 一起更新護持的班級
        for($i=0;$i<count($memberinList);$i++)
        {
            if($memberinList[$i][2]=="TB02"){$titleid=4;}else if($memberinList[$i][2]=="TB03"){$titleid=3;}else if($memberinList[$i][2]=="TB04"){$titleid=2;}else{continue;}
            $sqltmp="select idx,titleid from `".$tbname."` where (`STU_ID`='".$memberinList[$i][0]."' AND `titleid`<".$titleid.");";
            $sqltmp_result=mysql_query($sqltmp);
            $numrows=mysql_num_rows($sqltmp_result);
            if ($numrows<=0){continue;}
            $row_tmp=mysql_fetch_array($sqltmp_result, MYSQL_NUM);
            //echo $sqltmp."<br>";
            if ($row_tmp[1]==$titleid){continue;}
            $sql_leaderstatus.="update ".$tbname." set `titleid`=".$titleid." where (`idx`=".$row_tmp[0].");";
            //echo "<br>".$memberinList[$i][0];
        }
        $sqlcmdex=explode(";",$sql_leaderstatus);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        // 一併更新幹部的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++)
        {
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `seq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        return true;
    }

    function syncMemberpujadbxNEW($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname) // 班員 TB05, TB06
    {
        $con = mysqli_connect("localhost","root","rinpoche");//connect_db("bwsouthdb");
        //從sdm_clsmembers找出該班學員出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdmdb`.`sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.TTL_ID='學員')";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){
            while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){
                $memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;
            }
        }

        // 從法會報名表中找出該班學員出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `bwsouthdb`.`".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid<5)";//echo $sql."<br>";
        $sql_result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        $cnt=0;
        $memberinPuja = [];
        if ($numrows>0){while($row=mysqli_fetch_array($sql_result, MYSQLI_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja)) {
            for($i=0;$i<count($memberinList);$i++) {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){
                    if ($memberinList[$i][0]==$memberinPuja[$j][0]){
                        $matchcnt++;
                        $find=true;
                        break;
                    }
                }
                if ($find==false){
                    break;
                }
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        $memberinAll=[];
        for($i=0;$i<count($memberinList);$i++) {
            for($j=0;$j<count($memberinPuja);$j++) {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }
        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        $pujanone = [];
        for($i=0;$i<count($memberinPuja);$i++){
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);
        $sql_update="";
        // in clsmember but not in class join table -> 1.在別班?(YES:換班), 2.新學員(insert)
        //echo "<br>cls member none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}

        // 1.在別班?(YES:換班)
        $dayitem="0,0,0,0,0,0,0,'Z,0,Z,0',0,0,0,'1970-01-01','1970-01-01',0,'','',0,'',''";
        for($i=0;$i<count($clsmembernone);$i++){
            $sqltemp="select A.STU_ID from `bwsouthdb`.`".$tbname."` A WHERE (A.STU_ID='".$clsmembernone[$i][0]."' AND A.titleid<5)";//echo $sqltemp;
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows>0){//在別班=>換到目前的班級
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$clsid."',`classname`='".$clsname."',`memberseq`=".$clsmembernone[$i][1]." where (`STU_ID`='".$clsmembernone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            // 法會報名表內也無此學員 => 新加入的學員,找出學員基本資料再 insert 到法會報名表
            $sqlstudent="select * from `sdmdb`.`sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysqli_query($con,$sqlstudent);
            $numrows=mysqli_num_rows($sqlstudent_result);
            if ($numrows<=0){continue;}
            $row_stu=mysqli_fetch_array($sqlstudent_result, MYSQLI_ASSOC);

            $leader="";
            $titleid=1;
            $title="學員";
            $staffid=0;//getStaffid($clsmembernone[$i][0]);
            if($clsmembernone[$i][2]=="學員"){$titleid=0;}
            if ($row_stu["IsCurrent"] != "1"){
                $titleid=-10;
            }
            $birth = date('Y');
            if ($row_stu["Age"]){
                $birth = (date('Y') - $row_stu["Age"])."-01-01";
            }

            // INSERT COMMAND
            $sql_update.="insert into `bwsouthdb`.`".$tbname."` values (NULL,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
            $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$birth."','".$row_stu["DEG_ID"]."',";
            $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
            $sql_update.="'".$leader."','','','".$classfullname."',";
            $sql_update.=$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.");";
        }
        //echo $sql_update;echo "<br>";return;

        // in puja but not in join table -> 1.調到別班?(YES:換班), 2.離開(insert)
        for($i=0;$i<count($pujanone);$i++) {
            $sqltemp="select * from `sdmdb`.`sdm_clsmembers` A WHERE (A.STU_ID='".$pujanone[$i][0]."' AND A.IsMajor='是')";
            $sqltemp_result=mysqli_query($con,$sqltemp);
            $numrows=mysqli_num_rows($sqltemp_result);
            if ($numrows<=0){//找不到此人....休學或離開
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            $row=mysql_fetch_array($sqltemp_result, MYSQL_ASSOC);
            if($row["Status"]=="離開") {
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                //echo "<br>".$pujanone[$i][0]."(離開)";// update CLS_ID, Name and vailddate ad empty
            }else if ($row["CLS_ID"]!=$clsid){
                $sqlclasses="select Class from `sdmdb`.`sdm_classes` WHERE (`CLS_ID`='".$row["CLS_ID"]."')";
                $sqlclasses_result=mysqli_query($con,$sqlclasses);
                $numrows=mysqli_num_rows($sqlclasses_result);
                $classname="";
                if ($numrows>0)
                {
                    $row_classes=mysql_fetch_array($sqlclasses_result, MYSQL_ASSOC);
                    $tmp=explode("-",$row_classes["Class"]);
                    $classname=$tmp[0];
                }
                $sql_update.="update `bwsouthdb`.`".$tbname."` set `CLS_ID`='".$row["CLS_ID"]."',`classname`='".$classname."' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                // find the class name from sdm_classes
                // echo "<br>".$pujanone[$i][0]."(調出)";// update CLS_ID, Name and vailddate
            }
        }
        //echo $sql_update;//echo "<br>";
        //echo "<br>rollcall none ";	for($i=0;$i<count($pujanone);$i++){echo "<br>".$pujanone[$i][0];}

        // change something
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd)>0) {
            //for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmd); $i++){
                $sql=$sqlcmd[$i];
                if ($sql==''){continue;}
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }

        // 一併更新學員的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++){
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `memberseq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `bwsouthdb`.`".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0){
            for($i = 0; $i < count($sqlcmdex); $i++){
                $sql=$sqlcmdex[$i];
                if ($sql==''){continue;}
                mysqli_query($con,$sql);
            }
            mysqli_commit($con);
        }
        mysqli_close($con);
        return true;
    }

?>
