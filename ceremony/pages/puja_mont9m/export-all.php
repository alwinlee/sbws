<?php
    header("Content-Type: text/html; charset=utf-8");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-download");
    header("Content-Type: application/download");

    session_start();
    set_time_limit(1200); // page execution time = 1200 seconds

    ini_set("error_reporting", 0); //error_reporting(E_ALL & ~E_NOTICE);
    ini_set("display_errors","Off"); // On : open, Off : close
    ini_set('memory_limit', -1 );

    date_default_timezone_set('Asia/Taipei');//	date_default_timezone_set('Europe/London');

    if (PHP_SAPI=='cli')
    die('This example should only be run from a Web Browser');

    //檢查是否已登入，若未登入則導回首頁
    if(!isset($_SESSION["username"]) || ($_SESSION["username"]=="") || ($_POST["tbname"]=="")) {
        header("Location: ..\..\..\index.php");
    }

    require_once("../../../_res/_inc/connMysql.php");
    require_once("../../../_res/_inc/sharelib.php");
    require_once ("../../../_res/Classes/PHPExcel.php"); // PHPExcel // require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
    require_once ("../../../_res/Classes/PHPExcel/IOFactory.php"); // PHPExcel_IOFactory

    //------------------------------------------------------------------------------------------------------------------------------
    // 取得參數並準備好資料庫
    $day1title=$_POST["day1title"];
    $day2title=$_POST["day2title"];
    $day3title=$_POST["day3title"];
    $day4title=$_POST["day4title"];
    $day5title=$_POST["day5title"];
    $day6title=$_POST["day6title"];

    $table_name=$_POST["tbname"];
    $traffic_table_name=$_POST["tbtraffic"];
    $regioncode=$_POST["regioncode"];
    $pujatitle=$_POST["pujatitle"];
    $orderbydate=$_POST["orderbydate"];
    $year=GetCurrentYear();// 跟朝禮法會相當時間
    $curyear=GetCurrentYear();
    $table_title=$pujatitle." 報名名冊";//.$traffic_table_name;//.$statistic_table_name;

    $showtraffconfirmitem=false;//是否匯出確認搭車資料
    $showtraffprepareitem=false;//是否匯出預計搭車資料
    // 取得車次表 day 1 & day 2
    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=0 ORDER BY `traffid` ASC";
    $pujatraffic_result=mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($pujatraffic_result);
    $day1trafficcnt=0;
    if ($numrows>0)
    {
        $i=0;//2;
        while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
        {
            $traffinfo1[$i][0]=$traffic_row["traffid"];
            $traffinfo1[$i][1]=$traffic_row["traffname"];
            $traffinfo1[$i][2]=0;// 預計
            $traffinfo1[$i][3]=0;// 確認
            $traffinfo1[$i][4]=0;// 預計總和
            $traffinfo1[$i][5]=0;// 確認總和
            $traffinfo1[$i][6]=0;	// 確認總和(去)
            $traffinfo1[$i][7]=0;	// 確認總和(回)
            $i++;
        }
        $day1trafficcnt=count($traffinfo1);
    }
    //day2
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=1 ORDER BY `traffid` ASC";
    $pujatraffic_result = mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($pujatraffic_result);
    $day2trafficcnt=0;

    if ($numrows>0)
    {
        $i=0;//2;
        while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
        {
            $traffinfo2[$i][0]=$traffic_row["traffid"];
            $traffinfo2[$i][1]=$traffic_row["traffname"];
            $traffinfo2[$i][2]=0;	// 預計
            $traffinfo2[$i][3]=0;	// 確認
            $traffinfo2[$i][4]=0;	// 預計總和
            $traffinfo2[$i][5]=0;	// 確認總和
            $traffinfo2[$i][6]=0;	// 確認總和(去)
            $traffinfo2[$i][7]=0;	// 確認總和(回)
            $i++;
        }
        $day2trafficcnt=count($traffinfo2);
    }

    //day3
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=2 ORDER BY `traffid` ASC";
    $pujatraffic_result = mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($pujatraffic_result);
    $day3trafficcnt=0;
    if ($numrows>0)
    {
        $i=0;//2;
        while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
        {
            $traffinfo3[$i][0]=$traffic_row["traffid"];
            $traffinfo3[$i][1]=$traffic_row["traffname"];
            $traffinfo3[$i][2]=0;	// 預計
            $traffinfo3[$i][3]=0;	// 確認
            $traffinfo3[$i][4]=0;	// 預計總和
            $traffinfo3[$i][5]=0;	// 確認總和
            $traffinfo3[$i][6]=0;	// 確認總和(去)
            $traffinfo3[$i][7]=0;	// 確認總和(回)
            $i++;
        }
        $day3trafficcnt=count($traffinfo3);
    }
    //day4
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=3 ORDER BY `traffid` ASC";
    $pujatraffic_result = mysqli_query($con,$sql);
    $numrows=mysqli_num_rows($pujatraffic_result);
    $day4trafficcnt=0;
    if ($numrows>0)
    {
        $i=0;//2;
        while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
        {
            $traffinfo4[$i][0]=$traffic_row["traffid"];
            $traffinfo4[$i][1]=$traffic_row["traffname"];
            $traffinfo4[$i][2]=0;	// 預計
            $traffinfo4[$i][3]=0;	// 確認
            $traffinfo4[$i][4]=0;	// 預計總和
            $traffinfo4[$i][5]=0;	// 確認總和
            $traffinfo4[$i][6]=0;	// 確認總和(去)
            $traffinfo4[$i][7]=0;	// 確認總和(回)
            $i++;
        }
        $day4trafficcnt=count($traffinfo4);
    }

    //day5
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=4 ORDER BY `traffid` ASC";
    $pujatraffic_result = mysqli_query($con,$sql);
    $numrows=mysqli_num_rows($pujatraffic_result);
    $day5trafficcnt=0;
    if ($numrows>0)
    {
        $i=0;//2;
        while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
        {
            $traffinfo5[$i][0]=$traffic_row["traffid"];
            $traffinfo5[$i][1]=$traffic_row["traffname"];
            $traffinfo5[$i][2]=0;	// 預計
            $traffinfo5[$i][3]=0;	// 確認
            $traffinfo5[$i][4]=0;	// 預計總和
            $traffinfo5[$i][5]=0;	// 確認總和
            $traffinfo5[$i][6]=0;	// 確認總和(去)
            $traffinfo5[$i][7]=0;	// 確認總和(回)
            $i++;
        }
        $day5trafficcnt=count($traffinfo5);
    }

    //day6
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=5 ORDER BY `traffid` ASC";
    $pujatraffic_result = mysqli_query($con,$sql);
    $numrows=mysqli_num_rows($pujatraffic_result);
    $day6trafficcnt=0;
    if ($numrows>0)
    {
        $i=0;//2;
        while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
        {
            $traffinfo6[$i][0]=$traffic_row["traffid"];
            $traffinfo6[$i][1]=$traffic_row["traffname"];
            $traffinfo6[$i][2]=0;	// 預計
            $traffinfo6[$i][3]=0;	// 確認
            $traffinfo6[$i][4]=0;	// 預計總和
            $traffinfo6[$i][5]=0;	// 確認總和(去回)
            $traffinfo6[$i][6]=0;	// 確認總和(去)
            $traffinfo6[$i][7]=0;	// 確認總和(回)
            $i++;
        }
        $day6trafficcnt=count($traffinfo6);
    }

    // 考慮不同區域的報名窗口
    $Car=true;
    $CarVolume=40;
    if($regioncode==""||$regioncode=="*"||$regioncode==" ")
    {
        $sql  = 'select a.*, b.PhoneNo_H as TEL, b.PhoneNo_C as CP ';
        $sql .= 'from `'.$table_name.'` as a  left join `studentinfo` as b on a.STU_ID=b.stuid ';
        $sql .= 'where (`day1`>0 OR `day2`>0) ';
        $sql .= 'ORDER BY `CLS_ID` ASC, a.sex DESC,`STU_ID` ASC;';
    } else {
        $Car=false;
        $areakey="";
        $keyofareaid=$regioncode;
        $areaid=explode(";",$keyofareaid);
        $sqlstring="";
        for($x=0;$x<count($areaid);$x++) {
            if ($sqlstring!=""){$sqlstring.=" OR";}
            $sqlstring.="`areaid`='".$areaid[$x]."'";
        }
        $areakey=" AND (".$sqlstring.")";
        
        $sql  = 'select a.*, b.PhoneNo_H as TEL, b.PhoneNo_C as CP ';
        $sql .= 'from `'.$table_name.'` as a  left join `studentinfo` as b on a.STU_ID=b.stuid ';
        $sql .= 'where ((`day1`>0 OR `day2`>0) '.$areakey.')';
        $sql .= 'ORDER BY `CLS_ID` ASC, a.sex DESC,`STU_ID` ASC;';
    }

    $result=mysqli_query($con,$sql);
    $numrows=mysqli_num_rows($result);
    if ($numrows<0){exit;}
    //------------------------------------------------------------------------------------------------------------------------------
    // Create new PHPExcel object
    $nSheet=0;
    $objPHPExcel=new PHPExcel();
    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                 ->setLastModifiedBy("Maarten Balliauw")
                                 ->setTitle("Office 2007 XLSX Test Document")
                                 ->setSubject("Office 2007 XLSX Test Document")
                                 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                 ->setKeywords("office 2007 openxml php")
                                 ->setCategory("Test result file");
    $objWorkSheet=$objPHPExcel->setActiveSheetIndex($nSheet);

    $col=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
              "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
              "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
              "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ");
    //$xlstitle=array("學員代號","序號","組別","姓名","母班班級","護持班級","性別","年齡","區域","職業","服務單位","職稱",
    //                "義工時數","義工組別","","","","","","","","搭車代號","車資","","","","","備註");

    //$xlstitleW=array(12,8,8,12,21,21,8,8,8,10,24,16,
    //                 10,10,10,10,10,10,8,8,8,10,10,10,10,10,10,20);

    $xlstitle=array("學員代號","姓名","母班班級","代碼","性別","年齡","區域","電話(宅)","電話(手機)","備註","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","車資");
    $xlstitleW=array(12,12,22,8,8,8,8,14,14,24,6,6,6,10,10,6,6,6,10,10,6,6,6,10,10,6,6,6,10,10,6,6,6,10,10,6,6,6,10,10,8);

    $dateCurr=date('Y');
    $jobtype=array('V00'=>'-','V01'=>'教育','V02'=>'醫護','V03'=>'公職','V04'=>'農業','V05'=>'工業','V06'=>'建築業','V07'=>'商業','V08'=>'服務業','V09'=>'軍警','V10'=>'自由業','V11'=>'學生','V12'=>'家管','V13'=>'無','V14'=>'退休','V99'=>'其他');
    $edutype=array('D0'=>'-','D1'=>'國小','D2'=>'國中','D3'=>'高中職','D4'=>'專科','D5'=>'大學','D6'=>'碩士','D7'=>'博士','D8'=>'不識字','D9'=>'識字');

    $mainitem=-1;//21;
    $roundcnt=1; // 2: 考慮去/回
    $top=3;
    // each sub title
    for($w=0;$w<count($xlstitle);$w++)
    {
        $mainitem++;$item=$col[$mainitem].$top.":".$col[$mainitem].($top+$roundcnt);
        if ($xlstitle[$mainitem]!="")
        {
            $objWorkSheet->mergeCells($item);$item=$col[$mainitem].$top;
            $objWorkSheet->setCellValue($item,$xlstitle[$mainitem]);
        }
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $item=$col[$mainitem].($top-1);
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
     }

    // 處理特殊欄位
    $idx=10;//$item=$col[$idx]."3:".$col[$idx]."4";$objWorkSheet->mergeCells($item);

    for($z=0;$z<6;$z++)
    {
        $item=$col[$idx]."3:".$col[$idx+4]."3";
        $objWorkSheet->mergeCells($item);
        $item=$col[$idx]."3";

        if($z==0){$objWorkSheet->setCellValue($item,$day1title);}
        else if($z==1){$objWorkSheet->setCellValue($item,$day2title);}
        else if($z==2){$objWorkSheet->setCellValue($item,$day3title);}
        else if($z==3){$objWorkSheet->setCellValue($item,$day4title);}
        else if($z==4){$objWorkSheet->setCellValue($item,$day5title);}
        else if($z==5){$objWorkSheet->setCellValue($item,$day6title);}

        $offset = 0;
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $item=$col[$idx]."4";
        $objWorkSheet->setCellValue($item,"參加");
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $item=$col[$idx+1]."4";
        $objWorkSheet->setCellValue($item,"眷屬");
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $offset = 1;
        $item=$col[$idx+2]."4";
        $objWorkSheet->setCellValue($item,"午餐");
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $item=$col[$idx+$offset+2]."4";
        $objWorkSheet->setCellValue($item,"預計搭車");
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $item=$col[$idx+$offset+3]."4";
        $objWorkSheet->setCellValue($item,"確認搭車");
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $idx+=5;
    }
    // end ---

    $item="A1:".$col[$mainitem]."1";
    // main title
    $objWorkSheet->mergeCells($item);
    $objWorkSheet->setCellValue("A1",$table_title); //合併後的儲存格
    $objWorkSheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objWorkSheet->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objWorkSheet->getStyle("A1")->getFont()->setSize(16);
    $objWorkSheet->getStyle("A1")->getFont()->setBold(true);
    $objWorkSheet->getRowDimension("1")->setRowHeight(30);
    $objWorkSheet->getRowDimension("3")->setRowHeight(20);
    if($roundcnt>=2){$objWorkSheet->getRowDimension("4")->setRowHeight(20);$objWorkSheet->getRowDimension("5")->setRowHeight(20);}
    else{$objWorkSheet->getRowDimension("4")->setRowHeight(40);}

    $xlstitlex=array("報名日期","繳費","繳費日期","收費窗口");
    $xlstitlexW=array(12,8,12,20);
    for($w=0;$w<count($xlstitlex);$w++)
    {
        $item=$col[$mainitem+1+$w].$top.":".$col[$mainitem+1+$w].($top+$roundcnt);
        if ($xlstitlex[$w]!=""){$objWorkSheet->mergeCells($item);}
        $item=$col[$mainitem+1+$w].$top;
        $objWorkSheet->setCellValue($item,$xlstitlex[$w]);
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $item=$col[$mainitem+1+$w].($top-1);
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    //$xlscartitlex=array("2/18(四)-去","2/18(四)-回","2/19(五)-去","2/19(五)-回","2/20(六)-去","2/20(六)-回","2/21(日)-去","2/21(日)-回","2/22(一)-去","2/22(一)-回","2/23(二)-去","2/23(二)-回");
    //$xlscartitlexW=array(12,12,12,12,12,12,12,12,12,12,12,12);
    $xlscartitlex=array($day1title."-去",$day1title."-回",$day2title."-去",$day2title."-回",$day3title."-去",$day3title."-回",
                        $day4title."-去",$day4title."-回",$day5title."-去",$day5title."-回",$day6title."-去",$day6title."-回");
    $xlscartitlexW=array(12,12,12,12,12,12,12,12,12,12,12,12);
    //預排交通車號次 - title
    if($Car==true)
    {
        $item=$col[$mainitem+count($xlstitlex)+1].$top.":".$col[$mainitem+count($xlstitlex)+1+count($xlscartitlex)-1].($top);
        $objWorkSheet->mergeCells($item);
        $item=$col[$mainitem+count($xlstitlex)+1].$top;
        $objWorkSheet->setCellValue($item,"預排搭車號次");
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $text="";$text1="";
        for($z=0;$z<count($xlscartitlex);$z++)
        {
            $item=$col[$mainitem+count($xlstitlex)+1+$z].($top+1);
            $objWorkSheet->setCellValue($item,$xlscartitlex[$z]);
            $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }
    }

    $idx=0;
    $iRow=$top+$roundcnt;

    // 填寫資料
    $trafficstartcol=$mainitem+$applieditem1+$applieditem2+1;
    $traffCnt1="0";$traffCnt2="0";$traffCnt3="0";$traffCnt4="0";$traffCnt5="0";$traffCnt6="0";
    $traffCntReal1="0";$traffCntReal2="0";$traffCntReal3="0";$traffCntReal4="0";$traffCntReal5="0";$traffCntReal6="0";

    while($row=mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $idx++;$iRow++;$pay="";$paydate="";$payto="";
        if ($row["pay"]>0){$pay="1";$paydate=$row["paydate"];$payto=$row["paybyname"];}

        // traffic
        for($x=0;$x<$day1trafficcnt;$x++) {$traffinfo1[$x][2]=0;$traffinfo1[$x][3]=0;}//2:預計搭該車次人數,3:確認搭該車次人數(因為family不能參加,故參加則人數應該是1)
        for($x=0;$x<$day2trafficcnt;$x++) {$traffinfo2[$x][2]=0;$traffinfo2[$x][3]=0;}//2:預計搭該車次人數,3:確認搭該車次人數(因為family不能參加,故參加則人數應該是1)
        for($x=0;$x<$day3trafficcnt;$x++) {$traffinfo3[$x][2]=0;$traffinfo3[$x][3]=0;}
        for($x=0;$x<$day4trafficcnt;$x++) {$traffinfo4[$x][2]=0;$traffinfo4[$x][3]=0;}
        for($x=0;$x<$day5trafficcnt;$x++) {$traffinfo5[$x][2]=0;$traffinfo5[$x][3]=0;}
        for($x=0;$x<$day6trafficcnt;$x++) {$traffinfo6[$x][2]=0;$traffinfo6[$x][3]=0;}

        $traff1="Z";$traff2="Z";$traff3="Z";$traff4="Z";$traff5="Z";$traff6="Z";
        $traffReal1="Z";$traffReal2="Z";$traffReal3="Z";$traffReal4="Z";$traffReal5="Z";$traffReal6="Z";
        $traffCnt1="0";$traffCnt2="0";$traffCnt3="0";$traffCnt4="0";$traffCnt5="0";$traffCnt6="0";
        $traffCntReal1="0";$traffCntReal2="0";$traffCntReal3="0";$traffCntReal4="0";$traffCntReal5="0";$traffCntReal6="0";

        $traffA=explode(",",$row["traff1"]);$traff1=$traffA[0];$traffReal1=$traffA[2];$traffCnt1=$traffA[1];$traffCntReal1=$traffA[3];
        $traffB=explode(",",$row["traff2"]);$traff2=$traffB[0];$traffReal2=$traffB[2];$traffCnt2=$traffB[1];$traffCntReal2=$traffB[3];
        $traffC=explode(",",$row["traff3"]);$traff3=$traffC[0];$traffReal3=$traffC[2];$traffCnt3=$traffC[1];$traffCntReal3=$traffC[3];
        $traffD=explode(",",$row["traff4"]);$traff4=$traffD[0];$traffReal4=$traffD[2];$traffCnt4=$traffD[1];$traffCntReal4=$traffD[3];
        $traffE=explode(",",$row["traff5"]);$traff5=$traffE[0];$traffReal5=$traffE[2];$traffCnt5=$traffE[1];$traffCntReal5=$traffE[3];
        $traffF=explode(",",$row["traff6"]);$traff6=$traffF[0];$traffReal6=$traffF[2];$traffCnt6=$traffF[1];$traffCntReal6=$traffF[3];

        $day1=0;$day2=0;$day3=0;$day4=0;$day5=0;$day6=0;
        $day1=$row["day1"];if($day1<=0){$traff1="";}
        $day2=$row["day2"];if($day2<=0){$traff2="";}
        $day3=$row["day3"];if($day3<=0){$traff3="";}
        $day4=$row["day4"];if($day4<=0){$traff4="";}
        $day5=$row["day5"];if($day5<=0){$traff5="";}
        $day6=$row["day6"];if($day6<=0){$traff6="";}

        //$fami1=0;$fami2=0;$fami3=0;$fami4=0;$fami5=0;$fami6=0;
        $fami1=$row["family1"];$fami2=$row["family2"];$fami3=$row["family3"];$fami4=$row["family4"];$fami5=$row["family5"];$fami6=$row["family6"];

        //$service1=0;$service2=0;$service3=0;$service4=0;$service5=0;$service6=0;
        //$service1=$row["service1"];$service2=$row["service2"];$service3=$row["service3"];$service4=$row["service4"];$service5=$row["service5"];$service6=$row["service6"];

        $meal1=0;$meal2=0;$meal3=0;$meal4=0;$meal5=0;$meal6=0;
        $meal1=$row["meal1"];$meal2=$row["meal2"];$meal3=$row["meal3"];$meal4=$row["meal4"];$meal5=$row["meal5"];$meal6=$row["meal6"];

        $mealx1=0;$mealx2=0;$mealx3=0;$mealx4=0;$mealx5=0;$mealx6=0;
        $mealx1=$row["specialcase1"];$mealx2=$row["specialcase2"];$mealx3=$row["specialcase3"];$mealx4=$row["specialcase4"];$mealx5=$row["specialcase5"];$mealx6=$row["specialcase6"];

        $live1=0;$live2=0;$live3=0;$live4=0;$live5=0;$live6=0;
        $live1=$row["attend1"];$live2=$row["attend2"];$live3=$row["attend3"];$live4=$row["attend4"];$live5=$row["attend5"];$live6=$row["attend6"];

        $car1="";$car2="";$car3="";$car4="";$car5="";$car6="";

        if ($day1>0&&$traffReal1!=""&&$traffReal1!="Z"&&$traffCntReal1==0){//確認 - 先排去回在同一車
           for($x=0;$x<$day1trafficcnt;$x++){if($traffReal1==$traffinfo1[$x][0]){$traffinfo1[$x][3]=(1+$fami1);$traffinfo1[$x][5]+=1;$traffinfo1[$x][5]+=$fami1;$car1=$traffReal1."-".ceil($traffinfo1[$x][5]/$CarVolume)."車";break;}}
        }
        if ($day2>0&&$traffReal2!=""&&$traffReal2!="Z"&&$traffCntReal2==0){
           for($x=0;$x<$day2trafficcnt;$x++){if($traffReal2==$traffinfo2[$x][0]){$traffinfo2[$x][3]=(1+$fami2);$traffinfo2[$x][5]+=1;$traffinfo2[$x][5]+=$fami2;$car2=$traffReal2."-".ceil($traffinfo2[$x][5]/$CarVolume)."車";break;}}
        }
        if ($day3>0&&$traffReal3!=""&&$traffReal3!="Z"&&$traffCntReal3==0){
           for($x=0;$x<$day3trafficcnt;$x++){if($traffReal3==$traffinfo3[$x][0]){$traffinfo3[$x][3]=(1+$fami3);$traffinfo3[$x][5]+=1;$traffinfo3[$x][5]+=$fami3;$car3=$traffReal3."-".ceil($traffinfo3[$x][5]/$CarVolume)."車";break;}}
        }
        if ($day4>0&&$traffReal4!=""&&$traffReal4!="Z"&&$traffCntReal4==0){
           for($x=0;$x<$day4trafficcnt;$x++){if($traffReal4==$traffinfo4[$x][0]){$traffinfo4[$x][3]=(1+$fami4);$traffinfo4[$x][5]+=1;$traffinfo4[$x][5]+=$fami4;$car4=$traffReal4."-".ceil($traffinfo4[$x][5]/$CarVolume)."車";break;}}
        }
        if ($day5>0&&$traffReal5!=""&&$traffReal5!="Z"&&$traffCntReal5==0){//確認
           for($x=0;$x<$day5trafficcnt;$x++){if($traffReal5==$traffinfo5[$x][0]){$traffinfo5[$x][3]=(1+$fami5);$traffinfo5[$x][5]+=1;$traffinfo5[$x][5]+=$fami5;$car5=$traffReal5."-".ceil($traffinfo5[$x][5]/$CarVolume)."車";break;}}
        }
        if ($day6>0&&$traffReal6!=""&&$traffReal6!="Z"&&$traffCntReal6==0){
           for($x=0;$x<$day6trafficcnt;$x++){if($traffReal6==$traffinfo6[$x][0]){$traffinfo6[$x][3]=(1+$fami6);$traffinfo6[$x][5]+=1;$traffinfo6[$x][5]+=$fami6;$car6=$traffReal6."-".ceil($traffinfo6[$x][5]/$CarVolume)."車";break;}}
        }

        $traffitem1="";$traffitem2="";$traffitem3="";$traffitem4="";$traffitem5="";$traffitem6="";
        $traffitem1R="";$traffitem2R="";$traffitem3R="";$traffitem4R="";$traffitem5R="";$traffitem6R="";
        //$traffitem=$traff1;if($traffitem!=""){if($traff2!=""){$traffitem.=",";$traffitem.=$traff2;}}else{$traffitem=$traff2;}
        //$traffRealitem=$traffReal1;if($traffRealitem!=""){if($traffReal2!=""){$traffRealitem.=",";$traffRealitem.=$traffReal2;}}else{$traffRealitem=$traffReal2;}
        if($row["day1"]>0){$traffitem1=$traff1;$traffitem1R.=$traffReal1;if($traffitem1!="Z"&&$traffCnt1!=0){$traffitem1.=($traffCnt1==1?"-去":"-回");}if($traffitem1R!="Z"&&$traffCntReal1!=0){$traffitem1R.=($traffCntReal1==1?"-去":"-回");}}//報名第1場
        if($row["day2"]>0){$traffitem2=$traff2;$traffitem2R.=$traffReal2;if($traffitem2!="Z"&&$traffCnt2!=0){$traffitem2.=($traffCnt2==1?"-去":"-回");}if($traffitem2R!="Z"&&$traffCntReal2!=0){$traffitem2R.=($traffCntReal2==1?"-去":"-回");}}//報名第2場
        if($row["day3"]>0){$traffitem3=$traff3;$traffitem3R.=$traffReal3;if($traffitem3!="Z"&&$traffCnt3!=0){$traffitem3.=($traffCnt3==1?"-去":"-回");}if($traffitem3R!="Z"&&$traffCntReal3!=0){$traffitem3R.=($traffCntReal3==1?"-去":"-回");}}//報名第3場
        if($row["day4"]>0){$traffitem4=$traff4;$traffitem4R.=$traffReal4;if($traffitem4!="Z"&&$traffCnt4!=0){$traffitem4.=($traffCnt4==1?"-去":"-回");}if($traffitem4R!="Z"&&$traffCntReal4!=0){$traffitem4R.=($traffCntReal4==1?"-去":"-回");}}//報名第4場
        if($row["day5"]>0){$traffitem5=$traff5;$traffitem5R.=$traffReal5;if($traffitem5!="Z"&&$traffCnt5!=0){$traffitem5.=($traffCnt5==1?"-去":"-回");}if($traffitem5R!="Z"&&$traffCntReal5!=0){$traffitem5R.=($traffCntReal5==1?"-去":"-回");}}//報名第5場
        if($row["day6"]>0){$traffitem6=$traff6;$traffitem6R.=$traffReal6;if($traffitem6!="Z"&&$traffCnt6!=0){$traffitem6.=($traffCnt6==1?"-去":"-回");}if($traffitem6R!="Z"&&$traffCntReal6!=0){$traffitem6R.=($traffCntReal6==1?"-去":"-回");}}//報名第6場

        $mainclass=$row["classfullname"];$supportcalss="";
        if ($row["titleid"]>=5){$mainclass=$row["otherinfo"];$supportcalss=$row["classfullname"];}
        $date=$dateCurr-date('Y',strtotime($row["age"]));

        $cost=$row["cost1"]+$row["cost2"]+$row["cost3"]+$row["cost4"]+$row["cost5"]+$row["cost6"];
        $regdate="";
        if($day1>0){$regdate=$row["regdate1"];}else if($day2>0){$regdate=$row["regdate2"];}else if($day3>0){$regdate=$row["regdate3"];}
        else if($day4>0){$regdate=$row["regdate4"];}else if($day5>0){$regdate=$row["regdate5"];}else{$regdate=$row["regdate6"];}

        $paydate="";
        $paydate=($row["paydate1"]=="1970-01-01"?"":$row["paydate1"]);if($paydate==""){$paydate=($row["paydate2"]=="1970-01-01"?"":$row["paydate2"]);}
        if($paydate==""){$paydate=($row["paydate3"]=="1970-01-01"?"":$row["paydate3"]);}if($paydate==""){$paydate=($row["paydate4"]=="1970-01-01"?"":$row["paydate4"]);}
        if($paydate==""){$paydate=($row["paydate5"]=="1970-01-01"?"":$row["paydate5"]);}if($paydate==""){$paydate=($row["paydate6"]=="1970-01-01"?"":$row["paydate6"]);}
        if($paydate=="1970-01-01"){$paydate="";}

        $paybyname="";$paybyname=$row["paybyname1"];if($paybyname==""){$paybyname=$row["paybyname2"];}
        if($paybyname==""){$paybyname=$row["paybyname3"];}if($paybyname==""){$paybyname=$row["paybyname4"];}
        if($paybyname==""){$paybyname=$row["paybyname5"];}if($paybyname==""){$paybyname=$row["paybyname6"];}

        $paytotal=$row["pay1"]+$row["pay2"]+$row["pay3"]+$row["pay4"]+$row["pay5"]+$row["pay6"];
        $c=0;
        $objWorkSheet->setCellValue($col[$c].$iRow, $row["STU_ID"])//$row["STU_ID"])
                     ->setCellValue($col[++$c].$iRow,$row["name"])//$row["ARE_ID"])
                     ->setCellValue($col[++$c].$iRow,$mainclass)
                    // ->setCellValue($col[++$c].$iRow,"")//$row["leaderinfo"])//$supportcalss)
                     ->setCellValue($col[++$c].$iRow,$row["areaid"])
                     ->setCellValue($col[++$c].$iRow,$row["sex"])//$row["CTP_ID"])
                     ->setCellValue($col[++$c].$iRow,$date)//$row["CTP_ID"])
                     ->setCellValue($col[++$c].$iRow,"南區")
                     ->setCellValue($col[++$c].$iRow,$row["TEL"])
                     ->setCellValue($col[++$c].$iRow," ".$row["CP"])
                     ->setCellValue($col[++$c].$iRow,$row["memo1"])

                     ->setCellValue($col[++$c].$iRow,$day1==1 ? "1":"")
                     ->setCellValue($col[++$c].$iRow,$fami1>0 ? $fami1:"")
                     ->setCellValue($col[++$c].$iRow,$meal1>0 ? $meal1:"")
                     ->setCellValue($col[++$c].$iRow,$traffitem1)
                     ->setCellValue($col[++$c].$iRow,$traffitem1R)

                     ->setCellValue($col[++$c].$iRow,$day2==1 ? "1":"")
                     ->setCellValue($col[++$c].$iRow,$fami2>0 ? $fami2:"")
                     ->setCellValue($col[++$c].$iRow,$meal2>0 ? $meal2:"")
                     //->setCellValue($col[++$c].$iRow,$mealx2>0 ? $mealx2:"")
                     ->setCellValue($col[++$c].$iRow,$traffitem2)
                     ->setCellValue($col[++$c].$iRow,$traffitem2R)

                     ->setCellValue($col[++$c].$iRow,$day3==1 ? "1":"")
                     ->setCellValue($col[++$c].$iRow,$fami3>0 ? $fami3:"")
                     ->setCellValue($col[++$c].$iRow,$meal3>0 ? $meal3:"")
                     ->setCellValue($col[++$c].$iRow,$traffitem3)
                     ->setCellValue($col[++$c].$iRow,$traffitem3R)

                     ->setCellValue($col[++$c].$iRow,$day4==1 ? "1":"")
                     ->setCellValue($col[++$c].$iRow,$fami4>0 ? $fami4:"")
                     ->setCellValue($col[++$c].$iRow,$meal4>0 ? $meal4:"")
                     ->setCellValue($col[++$c].$iRow,$traffitem4)
                     ->setCellValue($col[++$c].$iRow,$traffitem4R)

                     ->setCellValue($col[++$c].$iRow,$day5==1 ? "1":"")
                     ->setCellValue($col[++$c].$iRow,$fami5>0 ? $fami5:"")
                     ->setCellValue($col[++$c].$iRow,$meal5>0 ? $meal5:"")
                     ->setCellValue($col[++$c].$iRow,$traffitem5)
                     ->setCellValue($col[++$c].$iRow,$traffitem5R)

                     ->setCellValue($col[++$c].$iRow,$day6==1 ? "1":"")
                     ->setCellValue($col[++$c].$iRow,$fami6>0 ? $fami6:"")
                     ->setCellValue($col[++$c].$iRow,$meal6>0 ? $meal6:"")
                     ->setCellValue($col[++$c].$iRow,$traffitem6)
                     ->setCellValue($col[++$c].$iRow,$traffitem6R)

                     ->setCellValue($col[++$c].$iRow,$cost>0 ? $cost:"")

                     ->setCellValue($col[++$c].$iRow,$regdate)
                     ->setCellValue($col[++$c].$iRow,$paytotal>0 ? $paytotal:"")

                     ->setCellValue($col[++$c].$iRow,$paydate)
                     ->setCellValue($col[++$c].$iRow,$paybyname);
                    if($Car==true)//預排交通車
                    {
                        $objWorkSheet->setCellValue($col[++$c].$iRow,$car1)
                                     ->setCellValue($col[++$c].$iRow,$car1)
                                     ->setCellValue($col[++$c].$iRow,$car2)
                                     ->setCellValue($col[++$c].$iRow,$car2)
                                     ->setCellValue($col[++$c].$iRow,$car3)
                                     ->setCellValue($col[++$c].$iRow,$car3)
                                     ->setCellValue($col[++$c].$iRow,$car4)
                                     ->setCellValue($col[++$c].$iRow,$car4)
                                     ->setCellValue($col[++$c].$iRow,$car5)
                                     ->setCellValue($col[++$c].$iRow,$car5)
                                     ->setCellValue($col[++$c].$iRow,$car6)
                                     ->setCellValue($col[++$c].$iRow,$car6);
                    }
    }

    // 排車次-單程去&單程回
    ////*
    $idx=0;
    $iRow=$top+$roundcnt;
    $tempc=$main+count($xlstitle)+count($xlstitlex);
    mysqli_data_seek($result,0);

    while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $idx++;$iRow++;

        $traff1="Z";$traff2="Z";$traff3="Z";$traff4="Z";$traff5="Z";$traff6="Z";
        $traffReal1="Z";$traffReal2="Z";$traffReal3="Z";$traffReal4="Z";$traffReal5="Z";$traffReal6="Z";
        $traffCnt1="0";$traffCnt2="0";$traffCnt3="0";$traffCnt4="0";$traffCnt5="0";$traffCnt6="0";
        $traffCntReal1="0";$traffCntReal2="0";$traffCntReal3="0";$traffCntReal4="0";$traffCntReal5="0";$traffCntReal6="0";

        $traffA=explode(",",$row["traff1"]);$traff1=$traffA[0];$traffReal1=$traffA[2];$traffCnt1=$traffA[1];$traffCntReal1=$traffA[3];
        $traffB=explode(",",$row["traff2"]);$traff2=$traffB[0];$traffReal2=$traffB[2];$traffCnt2=$traffB[1];$traffCntReal2=$traffB[3];
        $traffC=explode(",",$row["traff3"]);$traff3=$traffC[0];$traffReal3=$traffC[2];$traffCnt3=$traffC[1];$traffCntReal3=$traffC[3];
        $traffD=explode(",",$row["traff4"]);$traff4=$traffD[0];$traffReal4=$traffD[2];$traffCnt4=$traffD[1];$traffCntReal4=$traffD[3];
        $traffE=explode(",",$row["traff5"]);$traff5=$traffE[0];$traffReal5=$traffE[2];$traffCnt5=$traffE[1];$traffCntReal5=$traffE[3];
        $traffF=explode(",",$row["traff6"]);$traff6=$traffF[0];$traffReal6=$traffF[2];$traffCnt6=$traffF[1];$traffCntReal6=$traffF[3];

        $day1=0;$day2=0;$day3=0;$day4=0;$day5=0;$day6=0;
        $day1=$row["day1"];if($day1<=0){$traff1="";}
        $day2=$row["day2"];if($day2<=0){$traff2="";}
        $day3=$row["day3"];if($day3<=0){$traff3="";}
        $day4=$row["day4"];if($day4<=0){$traff4="";}
        $day5=$row["day5"];if($day5<=0){$traff5="";}
        $day6=$row["day6"];if($day6<=0){$traff6="";}

        $carcnt=0;//此人是否有搭單程
        $car1go="";$car1bk="";$car2go="";$car2bk="";$car3go="";$car3bk="";$car4go="";$car4bk="";$car5go="";$car5bk="";$car6go="";$car6bk="";
        if ($day1>0&&$traffReal1!=""&&$traffReal1!="Z"&&$traffCntReal1!=0){//確認 - 先排去回在同一車
            $carcnt++;
            for($x=0;$x<$day1trafficcnt;$x++){
                if($traffReal1==$traffinfo1[$x][0]){
                    if($traffCntReal1==1){$traffinfo1[$x][6]+=1;$traffinfo1[$x][6]+=$fami1;$car1go=$traffReal1."-".ceil(($traffinfo1[$x][5]+$traffinfo1[$x][6])/$CarVolume)."車";}
                    if($traffCntReal1==2){$traffinfo1[$x][7]+=1;$traffinfo1[$x][7]+=$fami1;$car1bk=$traffReal1."-".ceil(($traffinfo1[$x][5]+$traffinfo1[$x][7])/$CarVolume)."車";}
                    break;
                }
            }
        }
        if ($day2>0&&$traffReal2!=""&&$traffReal2!="Z"&&$traffCntReal2!=0){
            $carcnt++;
            for($x=0;$x<$day2trafficcnt;$x++){
                if($traffReal2==$traffinfo2[$x][0]){
                    if($traffCntReal2==1){$traffinfo2[$x][6]+=1;$traffinfo2[$x][6]+=$fami2;$car2go=$traffReal2."-".ceil(($traffinfo2[$x][5]+$traffinfo2[$x][6])/$CarVolume)."車";}
                    if($traffCntReal2==2){$traffinfo2[$x][7]+=1;$traffinfo2[$x][7]+=$fami2;$car2bk=$traffReal2."-".ceil(($traffinfo2[$x][5]+$traffinfo2[$x][7])/$CarVolume)."車";}
                    break;
                }
            }
        }

        if ($day3>0&&$traffReal3!=""&&$traffReal3!="Z"&&$traffCntReal3!=0){
            $carcnt++;
            for($x=0;$x<$day3trafficcnt;$x++){
                if($traffReal3==$traffinfo3[$x][0]){
                    if($traffCntReal3==1){$traffinfo3[$x][6]+=1;$traffinfo3[$x][6]+=$fami3;$car3go=$traffReal3."-".ceil(($traffinfo3[$x][5]+$traffinfo3[$x][6])/$CarVolume)."車";}
                    if($traffCntReal3==2){$traffinfo3[$x][7]+=1;$traffinfo3[$x][7]+=$fami3;$car3bk=$traffReal3."-".ceil(($traffinfo3[$x][5]+$traffinfo3[$x][7])/$CarVolume)."車";}
                    break;
                }
            }
        }

        if($carcnt<=0){continue;}
        $c=$tempc;
        if($car1go!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car1go);}$c++;
        if($car1bk!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car1bk);}$c++;
        if($car2go!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car2go);}$c++;
        if($car2bk!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car2bk);}$c++;
        if($car3go!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car3go);}$c++;
        if($car3bk!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car3bk);}$c++;
    }
    ///*/
    $iRow+=1;

    // SUM - 參加人數, 車資, 繳費 總計
    $sumitem=array($col[10],$col[11],$col[12],$col[15],$col[16],$col[17],$col[20],$col[21],$col[22]
                   ,$col[25],$col[26],$col[27],$col[30],$col[31],$col[32],$col[35],$col[36],$col[37]
                   ,$col[40],$col[42]);
    for($w=0;$w<count($sumitem);$w++)
    {
        $item="=SUM(".$sumitem[$w].($top+$roundcnt).":".$sumitem[$w].($iRow-1).")";
        $objWorkSheet->setCellValue($sumitem[$w].$iRow,$item);
        $objWorkSheet->setCellValue($sumitem[$w].($top-1),$item);
    }

    // FREEZE
    $item="E".($top+$roundcnt+1);
    $objWorkSheet->freezePane($item);

    // 設定欄位寛度
    for($w=0;$w<count($xlstitle);$w++){$objWorkSheet->getColumnDimension($col[$w])->setWidth($xlstitleW[$w]);}//$xlstitleW[$w]
    for($w=0;$w<count($xlstitlex);$w++){$objWorkSheet->getColumnDimension($col[$mainitem+1+$w])->setWidth($xlstitlexW[$w]);}
    for($w=0;$w<count($xlscartitlex);$w++){$objWorkSheet->getColumnDimension($col[$mainitem+count($xlstitlex)+1+$w])->setWidth($xlscartitlexW[$w]);}

    // set border
    $colend=$mainitem+count($xlstitlex)+count($xlscartitlex);
    $range="A".$top.":".$col[$colend].$iRow;
    $objWorkSheet->getStyle($range)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $range="B3:".$col[$colend].$iRow;
    $objWorkSheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    if ($roundcnt==2){$range="A3:".$col[$colend]."5";}else{$range="A3:".$col[$colend]."4";}
    $objWorkSheet->getStyle($range)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objWorkSheet->getStyle($range)->getFill()->getStartColor()->setRGB('DDFFDD');//$objWorkSheet->getStyle("A2")->getFill()->getStartColor()->setRGB('B7B7B7');

    $objWorkSheet->setTitle($table_title);// Rename worksheet
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');// Redirect output to a client’s web browser (Excel5)
    $fileheader="Content-Disposition: attachment;filename=\"".$table_title.".xls\"";//header('Content-Disposition: attachment;filename="simple.xls"');
    header($fileheader);
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');// If you're serving to IE 9, then the following may be needed

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
