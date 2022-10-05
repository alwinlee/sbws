<?php
    require_once("../_res/_inc/connMysql.php");
    require_once("../_res/_inc/sharelib.php");

    date_default_timezone_set('Asia/Taipei');
    $user_level=$_SESSION["userlevel"];
    $areakey=$_SESSION["keyofareaid"];
    $user_name=$_SESSION["username"];

    $ceremonyAuth=$_SESSION["ceremonyAuth"];
    $systemAuth=$_SESSION["systemAuth"];
    $dbname = "bwsouthdb";
    $con = connect_db($dbname);
    if ( $con == NULL) {
        return;
    }
    $sql = "SELECT * FROM pujaconfig order by `startdate` ASC, `menuid` ASC";
    $result = mysqli_query($con, $sql);
    $currY=date('Y');
    $currM=date('m');
    $currD=date('d');
    $ceremonyCnt=0;
    echo "<table>";

    echo "<tr><td colspan='2'>&nbsp;</td></tr>";
    //------------------------------------------------------------------------------------
    // 法會報名選單 3 : sepcial user
    $mgr=false;
    $area=false;//窗口
    if ($user_level==3||$user_level>=9){
        if($areakey==""||$areakey=="*"){$mgr=true;}
        else if($user_name=="mgr3A"){$mgr=true;}
        else{$area=true;}//窗口
    }

   // while($row = mysqli_fetch_array($puja_result, MYSQLI_ASSOC)){
    while($row=mysqli_fetch_assoc($result)){
        //檢查是否開啟
        if($row["usemode"] <= 0){continue;} // 不啟用
        if($row["usemode"] == 2 && $user_level <= 10){continue;}// 開發中模式, 開發者身份才會顯示
        // special case
        $special=false;
        if($row["usemode"]==1){ // 啟用受時間限制 且看身份為何
            if($row["php"]=="puja_zen3.php"&&$user_name=="mgr1"){$special=true;}
            if($area==true||$special==true){
                $startdate=$row["areastartdate"];
                $enddate=$row["areaenddate"];
                // x v or x - or - v=>窗口全關
                // - - or
                // 目前登入之區域窗口是否有支援
                if($row["areasupport"]=="x"||$row["areanotsupport"]=="v"){continue;} // 開放窗口:無, 關閉窗口:全部 => continue;
                if($row["areasupport"]!="-"&&$row["areasupport"]!="v"){ // 是否不在開放名單內
                    $supporarea=explode(";",$row["areasupport"]);$insupportlist=false;
                    if (count($supporarea)>0){for($z=0;$z<count($supporarea);$z++){if($user_name==$supporarea[$z]){$insupportlist=true;break;}}}
                    if($insupportlist==false){continue;}
                }

                if($row["areanotsupport"]!="-"&&$row["areanotsupport"]!="x"){ //是否在關閉名單內
                    $notsupporarea=explode(";",$row["areanotsupport"]);$innotsupportlist=false;
                    if (count($notsupporarea)>0){for($z=0;$z<count($notsupporarea);$z++){if($user_name==$notsupporarea[$z]){$innotsupportlist=true;break;}}}
                    if($innotsupportlist==true){continue;}
                }
            }else if($mgr==true){
                $startdate=$row["mgrstartdate"];
                $enddate=$row["mgrenddate"];
            }else{
                $startdate=$row["startdate"];
                $enddate=$row["enddate"];
            }

            $begin=date("Y-m-d",strtotime($startdate));
            $end=date("Y-m-d",strtotime($enddate));
            $curr=date("Y-m-d");
            if ($curr>$end||$curr<$begin){continue;}
        }

        $ceremonyCnt++;
        echo "<tr>";

        if ($row["menuid"]==$subMenuItem)
        {
            echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"法會報名\" src=\"../_res/img/ceremonyen.png\"></td>";
            echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"".$row["php"]."\">".$row["pujaname"]."</A></td>";
        }else{
            echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"法會報名\" src=\"../_res/img/ceremony.png\"></td>";
            echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"".$row["php"]."\">".$row["pujaname"]."</A></td>";
        }
        echo "</tr>";
        echo "<tr><td colspan='2'><hr></td></tr>";
    }

    mysqli_free_result($result);
    mysqli_close($con);
    if ($ceremonyCnt<=0)
    {
        echo "<tr>";
        echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"法會報名\" src=\"../_res/img/ceremonyen.png\"></td>";
        echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" >暫無法會報名</A></td>";
        echo "</tr>";
        echo "<tr><td colspan='2'><hr></td></tr>";
    }

    echo "<tr><td>&nbsp;</td><td></td></tr>";
    echo "<tr><td>&nbsp;</td><td></td></tr>";
    echo "<tr><td colspan='2'><hr></td></tr>";

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------
    if ($systemAuth[1]>=1&&$mgr==true&&($user_name!="mgr3A"))
    {
        echo "<tr>";
        echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"法會管理設定\" src=\"../_res/img/link.png\"></td>";

        if (10000001==$subMenuItem)
        {
            echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"puja_management.php\">法會管理設定</A></td>";
        }else{
            echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"puja_management.php\">法會管理設定</A></td>";
        }

        echo "</tr>";
        echo "<tr><td colspan='2'><hr></td></tr>";
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------
    /*echo "<tr>";
    echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"前往學員點名\" src=\"../_res/img/link.png\"></td>";

    if (isset($subMenuItem)&&$subMenuItem==4000)
        echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"../rollcall/main.php\">前往學員點名</A></td>";
    else
        echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"../rollcall/main.php\">前往學員點名</A></td>";

    echo "</tr>";
    echo "<tr><td colspan='2'><hr></td></tr>";
    */
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------
    /*echo "<tr>";
    echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"前往學員異動\" src=\"../_res/img/link.png\"></td>";

    if (isset($subMenuItem)&&$subMenuItem==5000)
        echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"../student/main.php\">前往學員異動</A></td>";
    else
        echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"../student/main.php\">前往學員異動</A></td>";

    echo "</tr>";
    echo "<tr><td colspan='2'><hr></td></tr>";
    */
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------
    /*echo "<tr>";
    echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"前往法本請購\" src=\"../_res/img/link.png\"></td>";

    if (isset($subMenuItem)&&$subMenuItem==6000)
        echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"../books/main.php\">前往法本請購</A></td>";
    else
        echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"../books/main.php\">前往法本請購</A></td>";

    echo "</tr>";
    echo "<tr><td colspan='2'><hr></td></tr>";
    */
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------
    /*echo "<tr>";
    echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"前往淨智課程\" src=\"../_res/img/link.png\"></td>";

    if (isset($subMenuItem)&&$subMenuItem==7000)
        echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"../classes/main.php\">前往淨智課程</A></td>";
    else
        echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"../classes/main.php\">前往淨智課程</A></td>";

    echo "</tr>";
    echo "<tr><td colspan='2'><hr></td></tr>";
    */
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------
    if ($systemAuth[0]>=1)
    {
        echo "<tr>";
        echo "<td style=\"width:42px;text-align:center;color: rgb(63, 63, 63);\">&nbsp;&nbsp;<img alt=\"前往系統管理\" src=\"../_res/img/link.png\"></td>";

        if (isset($subMenuItem) && $subMenuItem==8000)
            echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(0, 0, 255);\" title=\"\" href=\"../management/main.php\">前往系統管理</A></td>";
        else
            echo "<td style=\"width:140px;text-align:left;color: rgb(63, 63, 63);\"><A style=\"text-decoration:none;color: rgb(64, 64, 64);\" title=\"\" href=\"../management/main.php\">前往系統管理</A></td>";

        echo "</tr>";
        echo "<tr><td colspan='2'><hr></td></tr>";
    }

    echo "</table>";

?>
