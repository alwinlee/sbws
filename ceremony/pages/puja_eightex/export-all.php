<?php
    header("Content-Type: text/html; charset=utf-8");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-download");
    header("Content-Type: application/download");

    session_start();
    set_time_limit(1200); // page execution time = 1200 seconds

    ini_set("error_reporting", 0); //error_reporting(E_ALL & ~E_NOTICE);
    ini_set("display_errors","Off"); // On : open, Off : close
    ini_set( 'memory_limit', -1 );

    date_default_timezone_set('Asia/Taipei');//	date_default_timezone_set('Europe/London');

    if (PHP_SAPI == 'cli')
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
    $table_name=$_POST["tbname"];
    $traffic_table_name=$_POST["tbtraffic"];
    $regioncode=$_POST["regioncode"];
    $pujatitle=$_POST["pujatitle"];
    $pujadate1=$_POST["pujadate1"];
    $pujadate2=$_POST["pujadate2"];
    $year=GetCurrentYear();// 跟朝禮法會相當時間
    $curyear=GetCurrentYear();
    $table_title=$pujatitle." 報名名冊";//.$traffic_table_name;//.$statistic_table_name;

    $showtraffconfirmitem=true;//是否匯出確認搭車資料
    $showtraffprepareitem=true;//是否匯出預計搭車資料
    // 取得車次表 day 1 & day 2
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=0 ORDER BY `traffid` ASC";
    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
    $pujatraffic_result=mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($pujatraffic_result);
    $day1trafficcnt = 0;
    if ($numrows > 0)
    {
        $i=0;//2;
        //$traffinfo1[0][0] = "Z";	$traffinfo1[0][1] = "自往";		$traffinfo1[0][2] = 0;	$traffinfo1[0][3] = 0; $traffinfo1[0][4] = 0;  $traffinfo1[0][5] = 0;
        //$traffinfo1[1][0] = "ZZ";	$traffinfo1[1][1] = "共乘";		$traffinfo1[1][2] = 0;	$traffinfo1[1][3] = 0; $traffinfo1[1][4] = 0;  $traffinfo1[1][5] = 0;
        while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
        {
            $traffinfo1[$i][0] = $traffic_row["traffid"];
            $traffinfo1[$i][1] = $traffic_row["traffname"];
            $traffinfo1[$i][2] = 0;	 // 預計
            $traffinfo1[$i][3] = 0;	 // 確認
            $traffinfo1[$i][4] = 0;	 // 預計總和
            $traffinfo1[$i][5] = 0;	 // 確認總和
            $traffinfo1[$i][6]=0;	// 確認總和(去)
            $traffinfo1[$i][7]=0;	// 確認總和(回)
            $i++;
        }
        $day1trafficcnt = count($traffinfo1);
    }

    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=1 ORDER BY `traffid` ASC";
    $pujatraffic_result = mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($pujatraffic_result);
    $day2trafficcnt=0;

    if ($numrows > 0)
    {
        $i=0;//2;
        //$traffinfo2[0][0] = "Z";	$traffinfo2[0][1] = "自往";		$traffinfo2[0][2] = 0;	$traffinfo2[0][3] = 0;  $traffinfo2[0][4] = 0;  $traffinfo2[0][5] = 0;
        //$traffinfo2[1][0] = "ZZ";	$traffinfo2[1][1] = "共乘";		$traffinfo2[1][2] = 0;	$traffinfo2[1][3] = 0;  $traffinfo2[1][4] = 0;  $traffinfo2[1][5] = 0;
        while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
        {
            $traffinfo2[$i][0] = $traffic_row["traffid"];
            $traffinfo2[$i][1] = $traffic_row["traffname"];
            $traffinfo2[$i][2] = 0;	// 預計
            $traffinfo2[$i][3] = 0;	// 確認
            $traffinfo2[$i][4] = 0;	// 預計總和
            $traffinfo2[$i][5] = 0;	// 確認總和
            $traffinfo2[$i][6]=0;	// 確認總和(去)
            $traffinfo2[$i][7]=0;	// 確認總和(回)
            $i++;
        }
        $day2trafficcnt = count($traffinfo2);
    }

    // 考慮不同區域的報名窗口
    $Car=true;
    $CarVolume=40;

    if($regioncode==""||$regioncode=="*"||$regioncode==" "){
        $sql  = 'select a.*, b.PhoneNo_H as TEL, b.PhoneNo_C as CP ';
        $sql .= 'from `'.$table_name.'` as a  left join `studentinfo` as b on a.STU_ID=b.stuid ';
        $sql .= 'where ((`memo` <> "" OR `day` > 0) AND `titleid` >= 0) ';
        $sql .= 'ORDER BY `titleid` DESC, `CLS_ID` ASC, a.sex DESC,`STU_ID` ASC;';
    }else{
        $Car=false;
        $areakey="";
        $keyofareaid=$regioncode;
        $areaid=explode(";",$keyofareaid);
        $sqlstring="";
        for($x=0;$x<count($areaid);$x++)
        {
            if ($sqlstring!=""){$sqlstring.=" OR";}
            $sqlstring.="`areaid`='".$areaid[$x]."'";
        }
        $areakey=" AND (".$sqlstring.")";
        $sql  = 'select a.*, b.PhoneNo_H as TEL, b.PhoneNo_C as CP ';
        $sql .= 'from `'.$table_name.'` as a  left join `studentinfo` as b on a.STU_ID=b.stuid ';
        $sql .= 'where (((`memo` <> "" OR `day` > 0) AND `titleid` >= 0)  '.$areakey.')';
        $sql .= 'ORDER BY `titleid` DESC, `CLS_ID` ASC, a.sex DESC,`STU_ID` ASC;';
    }

    $result=mysqli_query($con, $sql);
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
    $xlstitle=array("序號","班級","班級職稱","學員代號","姓名","性別","年齡","學歷","電話(宅)","電話(公)","參加梯次","預計搭車","確認搭車","車資","特殊需求","緊急連絡電話","備註");
    $xlstitleW=array(8,21,10,12,10,6,6,8,16,16,12,10,10,10,16,16,16);
    $dateCurr=date('Y');
    $jobtype=array('V00'=>'-','V01'=>'教育','V02'=>'醫護','V03'=>'公職','V04'=>'農業','V05'=>'工業','V06'=>'建築業','V07'=>'商業','V08'=>'服務業','V09'=>'軍警','V10'=>'自由業','V11'=>'學生','V12'=>'家管','V13'=>'無','V14'=>'退休','V99'=>'其他');
    $edutype=array('D0'=>'-','D1'=>'國小','D2'=>'國中','D3'=>'高中職','D4'=>'專科','D5'=>'大學','D6'=>'碩士','D7'=>'博士','D8'=>'不識字','D9'=>'識字');
    $specialcase=array('0'=>'-', '1'=>'行動不便', '2'=>'懷孕', '3'=>'氣喘心臟', '4'=>'打鼾', '5'=>'其他症狀');

    $mainitem=-1;//21;
    $roundcnt=1; // 2: 考慮去/回
    $top=3;
    // each sub title
    for($w=0;$w<count($xlstitle);$w++)
    {
        $mainitem++;$item=$col[$mainitem].$top.":".$col[$mainitem].($top+$roundcnt);
        $objWorkSheet->mergeCells($item);$item=$col[$mainitem].$top;
        $objWorkSheet->setCellValue($item,$xlstitle[$mainitem]);
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $item=$col[$mainitem].($top-1);
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
     }

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

    $applieditem1=0;//報名場次
    $applieditem2=4;//報名日期,繳費日期,繳費,收費窗口
    // 報名場次,報名日期,繳費日期,繳費,收費窗口
    if ($applieditem1>1)
    {
        $item=$col[$mainitem+1].$top.":".$col[$mainitem+$applieditem1].$top;
        $objWorkSheet->mergeCells($item);
        // 填各場次item title
        //....
        $item=$col[$mainitem+1].$top;
    }else if ($applieditem1>0) {
        $item=$col[$mainitem+1].$top.":".$col[$mainitem+1].($top+$roundcnt);
        $objWorkSheet->mergeCells($item);
        $item=$col[$mainitem+1].$top;
    }

    if ($applieditem1>0)
    {
        $objWorkSheet->setCellValue($item,"報名場次");
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    $xlstitlex=array("報名日期","繳費","繳費日期","收費窗口");
    $xlstitlexW=array(12,6,12,16);
    for($w=0;$w<count($xlstitlex);$w++)
    {
        $item=$col[$mainitem+$applieditem1+1+$w].$top.":".$col[$mainitem+$applieditem1+1+$w].($top+$roundcnt);
        $objWorkSheet->mergeCells($item);
        $item=$col[$mainitem+$applieditem1+1+$w].$top;
        $objWorkSheet->setCellValue($item,$xlstitlex[$w]);
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $item=$col[$mainitem+$applieditem1+1+$w].($top-1);
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    $xlscartitlex=array($pujadate1."-去",$pujadate2."-回");
    $xlscartitlexW=array(18,18);
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
    while($row=mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $idx++;$iRow++;$pay="";$paydate="";$payto="";
        if ($row["pay"]>0){$pay="1";$paydate=$row["paydate"];$payto=$row["paybyname"];}

        // traffic
        for($x=0;$x<$day1trafficcnt;$x++) {$traffinfo1[$x][2]=0;$traffinfo1[$x][3]=0;}//2:預計搭該車次人數,3:確認搭該車次人數(因為family不能參加,故參加則人數應該是1)
        for($x=0;$x<$day2trafficcnt;$x++) {$traffinfo2[$x][2]=0;$traffinfo2[$x][3]=0;}//2:預計搭該車次人數,3:確認搭該車次人數(因為family不能參加,故參加則人數應該是1)

        $traff1="Z";$traff2="Z";$traffReal1="Z";$traffReal2="Z";
        $traffCnt1="0";$traffCnt2="0";$traffCntReal1="0";$traffCntReal2="0";
        $traff=explode(",",$row["traff"]);
        $traffReal=explode(",",$row["traffReal"]);
        $traffCnt=explode(",",$row["traffCnt"]);
        $traffCntReal=explode(",",$row["traffRealCnt"]);
        if (count($traff)>0){$traff1=$traff[0];}if (count($traff)>1){$traff2=$traff[1];}
        if (count($traffCnt)>0){$traffCnt1=$traffCnt[0];}if (count($traffCnt)>1){$traffCnt2=$traffCnt[1];}
        if (count($traffReal)>0){$traffReal1=$traffReal[0];}if (count($traffReal)>1){$traffReal2=$traffReal[1];}
        if (count($traffCntReal)>0){$traffCntReal1=$traffCntReal[0];}if (count($traffCntReal)>1){$traffCntReal2=$traffCntReal[1];}
        if (($row["day"]%10)>=1)//有參加第一天
        {
            for($x=0;$x<$day1trafficcnt;$x++){if ($traff1==$traffinfo1[$x][0]){$traffinfo1[$x][2]=1+$traffCnt1;break;}}//預計
            for($x=0;$x<$day1trafficcnt;$x++){if($traffReal1==$traffinfo1[$x][0]){$traffinfo1[$x][3]=1+$traffCntReal1;break;}}//確認
        }

	  if ($row["day"]>=10)//有參加第二天
	  {
		for($x=0;$x<$day2trafficcnt;$x++){if($traff2==$traffinfo2[$x][0]){$traffinfo2[$x][2]=1+$traffCnt2;break;}}//預計
		for($x=0;$x<$day2trafficcnt;$x++){if($traffReal2==$traffinfo2[$x][0]){$traffinfo2[$x][3]=1+$traffCntReal2;break;}}//確認
	  }

        $day=$row["day"];
        $day1=($day%10);
        $day2=($day-$day1)/10;
        $day11="";$day12="";$day21="";$day22="";$day31="";$day32="";$day41="";$day42="";$day51="";$day52="";
        if ($day1==1){$day11=1;}else if ($day1==2){$day21=1;}else if ($day1==3){$day31=1;}else if ($day1==4){$day41=1;}else if ($day1==5){$day51=1;}
        if ($day2==1){$day12=1;}else if ($day2==2){$day22=1;}else if ($day2==3){$day32=1;}else if ($day2==4){$day42=1;}else if ($day2==5){$day52=1;}

        $car1="";$car2="";$car3="";$car4="";$car5="";$car6="";
        if ($day1>0&&$traffReal1!=""&&$traffReal1!="Z"&&$traffCntReal1==0){//確認 - 先排去回在同一車
           for($x=0;$x<$day1trafficcnt;$x++){if($traffReal1==$traffinfo1[$x][0]){$traffinfo1[$x][3]=1;$traffinfo1[$x][5]++;$car1.=$traffReal1."-".ceil($traffinfo1[$x][5]/$CarVolume)."車";break;}}
        }
        if ($day2>0&&$traffReal2!=""&&$traffReal2!="Z"&&$traffCntReal2==0){
           for($x=0;$x<$day2trafficcnt;$x++){if($traffReal2==$traffinfo2[$x][0]){$traffinfo2[$x][3]=(1+$fami2);$traffinfo2[$x][5]+=1;$traffinfo2[$x][5]+=$fami2;$car2=$traffReal2."-".ceil($traffinfo2[$x][5]/$CarVolume)."車";break;}}
        }
        $attend = '-';
        if ($row["day"] == "1") {
            $attend = '04.08-09';
        } else if ($row["day"] == "2") {
            $attend = '04.15-16';
        } else if ($row["day"] == "3") {
            $attend = '04.22-23';
        }

        $traffitem1="";$traffitem2="";$traffitem3="";$traffitem4="";$traffitem5="";$traffitem6="";
        $traffitem1R="";$traffitem2R="";$traffitem3R="";$traffitem4R="";$traffitem5R="";$traffitem6R="";
        //$traffitem=$traff1;if($traffitem!=""){if($traff2!=""){$traffitem.=",";$traffitem.=$traff2;}}else{$traffitem=$traff2;}
        //$traffRealitem=$traffReal1;if($traffRealitem!=""){if($traffReal2!=""){$traffRealitem.=",";$traffRealitem.=$traffReal2;}}else{$traffRealitem=$traffReal2;}
        if($day1>0){$traffitem1=$traff1;$traffitem1R.=$traffReal1;if($traffitem1!="Z"&&$traffCnt1!=0){$traffitem1.=($traffCnt1==1?"-去":"-回");}if($traffitem1R!="Z"&&$traffCntReal1!=0){$traffitem1R.=($traffCntReal1==1?"-去":"-回");}}//報名第1場
        if($day2>0){$traffitem2=$traff2;$traffitem2R.=$traffReal2;if($traffitem2!="Z"&&$traffCnt2!=0){$traffitem2.=($traffCnt2==1?"-去":"-回");}if($traffitem2R!="Z"&&$traffCntReal2!=0){$traffitem2R.=($traffCntReal2==1?"-去":"-回");}}//報名第2場

        $mainclass=$row["classfullname"];$supportcalss="";
        if ($row["titleid"]>=5){$mainclass=$row["otherinfo"];$supportcalss=$row["classfullname"];}
        $date=$dateCurr-date('Y',strtotime($row["age"]));
        $c=0;
        $objWorkSheet->setCellValue($col[$c].$iRow,$idx)
                     ->setCellValue($col[++$c].$iRow,$row["classname"])
                     ->setCellValue($col[++$c].$iRow,$row["title"])
                     ->setCellValue($col[++$c].$iRow,$row["STU_ID"])//$row["Class"])
                     ->setCellValue($col[++$c].$iRow,$row["name"])//$row["ARE_ID"])
                     ->setCellValue($col[++$c].$iRow,$row["sex"])//$row["CTP_ID"])
                     ->setCellValue($col[++$c].$iRow,$date)//$row["CTP_ID"])
                     ->setCellValue($col[++$c].$iRow, "") // $edutype[$row["edu"]]
                     ->setCellValue($col[++$c].$iRow,$row["TEL"])
                     ->setCellValue($col[++$c].$iRow," ".$row["CP"])
                     ->setCellValue($col[++$c].$iRow,$attend)

                     ->setCellValue($col[++$c].$iRow,$traffitem1)
                     ->setCellValue($col[++$c].$iRow,$traffitem1R)

                     ->setCellValue($col[++$c].$iRow,$row["cost"]>0 ? $row["cost"]:"")

                     ->setCellValue($col[++$c].$iRow,$specialcase[$row["specialcase"]])
                     ->setCellValue($col[++$c].$iRow,' '.$row["tel"])
                     ->setCellValue($col[++$c].$iRow,$row["memo"])
                     ->setCellValue($col[++$c].$iRow,$row["regdate"])
                     ->setCellValue($col[++$c].$iRow,$row["pay"]>0 ? "1":"")
                     ->setCellValue($col[++$c].$iRow,$row["pay"]>0 ? $row["paydate"]:"")
                     ->setCellValue($col[++$c].$iRow,$row["paybyname"]) ;
                    if($Car==true)//預排交通車
                    {
                        $objWorkSheet->setCellValue($col[++$c].$iRow,$car1)
                                     ->setCellValue($col[++$c].$iRow,$car1);
                    }
    }

    // 排車次-單程去&單程回
    ////*
    $idx=0;
    $iRow=$top+$roundcnt;
    $tempc=$mainitem+count($xlstitle)+count($xlstitlex);
    mysqli_data_seek($result,0);

    while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $idx++;$iRow++;
        $traff1="Z";$traff2="Z";$traff3="Z";$traff4="Z";$traff5="Z";$traff6="Z";
        $traffReal1="Z";$traffReal2="Z";$traffReal3="Z";$traffReal4="Z";$traffReal5="Z";$traffReal6="Z";
        $traffCnt1="0";$traffCnt2="0";$traffCnt3="0";$traffCnt4="0";$traffCnt5="0";$traffCnt6="0";
        $traffCntReal1="0";$traffCntReal2="0";$traffCntReal3="0";$traffCntReal4="0";$traffCntReal5="0";$traffCntReal6="0";

        $traff=explode(",",$row["traff"]);
        $traffReal=explode(",",$row["traffReal"]);
        $traffCnt=explode(",",$row["traffCnt"]);
        $traffCntReal=explode(",",$row["traffRealCnt"]);
        if (count($traff)>0){$traff1=$traff[0];}if (count($traff)>1){$traff2=$traff[1];}
        if (count($traffCnt)>0){$traffCnt1=$traffCnt[0];}if (count($traffCnt)>1){$traffCnt2=$traffCnt[1];}
        if (count($traffReal)>0){$traffReal1=$traffReal[0];}if (count($traffReal)>1){$traffReal2=$traffReal[1];}
        if (count($traffCntReal)>0){$traffCntReal1=$traffCntReal[0];}if (count($traffCntReal)>1){$traffCntReal2=$traffCntReal[1];}
        if (($row["day"]%10)>=1)//有參加第一天
        {
            for($x=0;$x<$day1trafficcnt;$x++){if ($traff1==$traffinfo1[$x][0]){$traffinfo1[$x][2]=1+$traffCnt1;break;}}//預計
            for($x=0;$x<$day1trafficcnt;$x++){if($traffReal1==$traffinfo1[$x][0]){$traffinfo1[$x][3]=1+$traffCntReal1;break;}}//確認
        }

	  if ($row["day"]>=10)//有參加第二天
	  {
		for($x=0;$x<$day2trafficcnt;$x++){if($traff2==$traffinfo2[$x][0]){$traffinfo2[$x][2]=1+$traffCnt2;break;}}//預計
		for($x=0;$x<$day2trafficcnt;$x++){if($traffReal2==$traffinfo2[$x][0]){$traffinfo2[$x][3]=1+$traffCntReal2;break;}}//確認
	  }

        $day1=0;$day2=0;$day3=0;$day4=0;$day5=0;$day6=0;
        $day=$row["day"];
        $day1=($day%10);
        $day2=($day-$day1)/10;
        if($day1<=0){$traff1="";}
        if($day2<=0){$traff2="";}

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

        if($carcnt<=0){continue;}
        $c=$tempc;
        if($car1go!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car1go);}$c++;
        if($car1bk!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car1bk);}$c++;
        if($car2go!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car2go);}$c++;
        if($car2bk!=""){$objWorkSheet->setCellValue($col[$c].$iRow,$car2bk);}$c++;

    }
    ///*/

    $iRow+=1;

    // 參加人數, 車資, 繳費 總計
    $sumitem=array($col[9],$col[11],$col[14]);
    for($w=0;$w<count($sumitem);$w++)
    {
        $item="=SUM(".$sumitem[$w].($top+$roundcnt).":".$sumitem[$w].($iRow-1).")";
        $objWorkSheet->setCellValue($sumitem[$w].$iRow,$item);
        $objWorkSheet->setCellValue($sumitem[$w].($top-1),$item);
    }

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