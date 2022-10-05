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
    $orderbydate=$_POST["orderbydate"];
    $joinplace=$_POST["joinplace"];
    $day1title=$_POST["day1title"];
    $day2title=$_POST["day2title"];
    $year=GetCurrentYear();// 跟朝禮法會相當時間
    $curyear=GetCurrentYear();
    $table_title=$pujatitle." 報名名冊";//.$traffic_table_name;//.$statistic_table_name;

    $showtraffconfirmitem=false;//是否匯出確認搭車資料
    $showtraffprepareitem=false;//是否匯出預計搭車資料
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
            $i++;
        }
        $day1trafficcnt = count($traffinfo1);
    }

    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=1 ORDER BY `traffid` ASC";
    $pujatraffic_result = mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($pujatraffic_result);
    $day2trafficcnt=0;

    if ($numrows > 0) {
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
            $i++;
        }
        $day2trafficcnt = count($traffinfo2);
    }

    // 考慮不同區域的報名窗口
    $Car=false;
    if($regioncode==""||$regioncode=="*"||$regioncode==" "){
        $sql  = 'select a.*, b.PhoneNo_H as TEL, b.PhoneNo_C as CP ';
        $sql .= 'from `'.$table_name.'` as a  left join `studentinfo` as b on a.STU_ID=b.stuid ';
        $sql .= 'where (`day`> 0 AND `titleid`<5) ';
        $sql .= 'ORDER BY `CLS_ID` ASC, a.sex DESC,`STU_ID` ASC;';
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
        $sql .= 'where ((`day`> 0 AND `titleid`<5)  '.$areakey.')';
        $sql .= 'ORDER BY `CLS_ID` ASC, a.sex DESC,`STU_ID` ASC;';
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
              "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ",
              "DA","DB","DC","DD","DE","DF","DG","DH","DI","DJ","DK","DL","DM","DN","DO","DP","DQ","DR","DS","DT","DU","DV","DW","DX","DY","DZ");
    //$xlstitle=array("學員代號","序號","組別","姓名","母班班級","護持班級","性別","年齡","區域","職業","服務單位","職稱",
    //                "義工時數","義工組別","","","","","","","","搭車代號","車資","","","","","備註");

    //$xlstitleW=array(12,8,8,12,21,21,8,8,8,10,24,16,
    //                 10,10,10,10,10,10,8,8,8,10,10,10,10,10,10,20);

    $daytitle=array($day1title,$day2title);
    $xlstitleW=array(12,12,16,8,8,8,8,14,14,24);
    //$placetitle=array("左營中心","高雄學苑","鳳仁教室","圓明寺","中正教室","小港教室","旗山教室","南海寺","台東教室","澎湖教室");
    $joinplaces=explode(";",$joinplace);
    for($i=1;$i<count($joinplaces);$i++){
        if ($joinplaces[$i]!="" && $joinplaces[$i]!="-") {
            $placetitle[]=$joinplaces[$i];
        }
    }
    //$xlstitle=array("學員代號","姓名","母班班級","代碼","性別","年齡","區域","電話(宅)","電話(手機)","備註","","","","","","","","","","","","","","","","","搭車代號","車資");
    $xlstitle=array("學員代號","姓名","母班班級","代碼","性別","年齡","區域","電話(宅)","電話(手機)","備註");
    for($w=0;$w<count($placetitle);$w++){
        $xlstitle[]="";$xlstitle[]="";$xlstitle[]="";$xlstitle[]="";
        $xlstitleW[]=10;$xlstitleW[]=10;$xlstitleW[]=10;$xlstitleW[]=10;
    }
    $xlstitle[]="搭車代號";$xlstitle[]="車資";$xlstitleW[]=12;$xlstitleW[]=8;

    $dateCurr=date('Y');
    $jobtype=array('V00'=>'-','V01'=>'教育','V02'=>'醫護','V03'=>'公職','V04'=>'農業','V05'=>'工業','V06'=>'建築業','V07'=>'商業','V08'=>'服務業','V09'=>'軍警','V10'=>'自由業','V11'=>'學生','V12'=>'家管','V13'=>'無','V14'=>'退休','V99'=>'其他');
    $edutype=array('D0'=>'-','D1'=>'國小','D2'=>'國中','D3'=>'高中職','D4'=>'專科','D5'=>'大學','D6'=>'碩士','D7'=>'博士','D8'=>'不識字','D9'=>'識字');

    $mainitem=-1;//21;
    $roundcnt=1; // 2: 考慮去/回
    $top=3;
    // each sub title
    for($w=0;$w<count($xlstitle);$w++){
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
    for($w=0;$w<count($placetitle);$w++){
        $mx=$col[10+$w*2]."3:".$col[10+$w*2+1]."3";
        $objWorkSheet->mergeCells($mx);
        $objWorkSheet->setCellValue($col[10+$w*2]."3",$placetitle[$w]);
        $objWorkSheet->setCellValue($col[10+$w*2]."4",$daytitle[0]);
        $objWorkSheet->setCellValue($col[10+$w*2+1]."4",$daytitle[1]);
        $objWorkSheet->getStyle($col[10+$w*2]."3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($col[10+$w*2+1]."3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    //day1
    $mxz=$col[10+count($placetitle)*2]."3:".$col[10+count($placetitle)*2+count($placetitle)-1]."3";
    $objWorkSheet->mergeCells($mxz);
    $mx=$col[10+count($placetitle)*2]."3";
    $objWorkSheet->setCellValue($mx,$daytitle[0]."中午用餐");
    $objWorkSheet->getStyle($mx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    for($w=0;$w<count($placetitle);$w++){
        $mx=$col[10+count($placetitle)*2+$w]."4";
        $objWorkSheet->setCellValue($mx,$placetitle[$w]);
        $objWorkSheet->getStyle($mx)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    // day2
    $mxz=$col[10+count($placetitle)*2+count($placetitle)]."3:".$col[10+count($placetitle)*2+count($placetitle)+count($placetitle)-1]."3";
    $objWorkSheet->mergeCells($mxz);
    $mx=$col[10+count($placetitle)*2+count($placetitle)]."3";
    $objWorkSheet->setCellValue($mx,$daytitle[1]."中午用餐");
    $objWorkSheet->getStyle($mx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    for($w=0;$w<count($placetitle);$w++){
        $mx=$col[10+count($placetitle)*2+count($placetitle)+$w]."4";
        $objWorkSheet->setCellValue($mx,$placetitle[$w]);
        $objWorkSheet->getStyle($mx)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
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

    $applieditem1=0;//報名場次
    $applieditem2=6;//報名日期,繳費日期,繳費,收費窗口
    if($Car==false){$applieditem2=4;}
    // 報名場次,報名日期,繳費日期,繳費,收費窗口
    if ($applieditem1>1){
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

    if ($applieditem1>0){
        $objWorkSheet->setCellValue($item,"報名場次");
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    $xlstitlex=array("報名日期","繳費","繳費日期","收費窗口");
    $xlstitlexW=array(12,8,12,20);
    for($w=0;$w<count($xlstitlex);$w++){
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

    $idx=0;
    $iRow=$top+$roundcnt;
    // 填寫資料
    $trafficstartcol=$mainitem+$applieditem1+$applieditem2+1;
    $traffCnt1="0";$traffCnt2="0";$traffCntReal1="0";$traffCntReal2="0";
    while($row=mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $idx++;$iRow++;$pay="";$paydate="";$payto="";
        if ($row["pay"]>0){$pay="1";$paydate=$row["paydate"];$payto=$row["paybyname"];}

        // traffic
        for($x=0;$x<$day1trafficcnt;$x++) {$traffinfo1[$x][2]=0;$traffinfo1[$x][3]=0;}//2:預計搭該車次人數,3:確認搭該車次人數(因為family不能參加,故參加則人數應該是1)
        for($x=0;$x<$day2trafficcnt;$x++) {$traffinfo2[$x][2]=0;$traffinfo2[$x][3]=0;}//2:預計搭該車次人數,3:確認搭該車次人數(因為family不能參加,故參加則人數應該是1)

        $traff1="Z";$traff2="Z";$traffReal1="Z";$traffReal2="Z";$traffReal1Cnt=0;$traffReal2Cnt=0;
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
            for($x=0;$x<$day1trafficcnt;$x++){if($traff1==$traffinfo1[$x][0]){$traffinfo1[$x][2]=1+$traffCnt1;$traffinfo1[$x][4]++;break;}}//預計
            for($x=0;$x<$day1trafficcnt;$x++){if($traffReal1==$traffinfo1[$x][0]){$traffinfo1[$x][3]=1+$traffCntReal1;$traffinfo1[$x][5]++;$traffReal1Cnt=$traffinfo1[$x][5];break;}}//確認
        }

	    if ($row["day"]>=10)//有參加第二天
	    {
		  for($x=0;$x<$day2trafficcnt;$x++){if($traff2==$traffinfo2[$x][0]){$traffinfo2[$x][2]=1+$traffCnt2;$traffinfo2[$x][4]++;break;}}//預計
		  for($x=0;$x<$day2trafficcnt;$x++){if($traffReal2==$traffinfo2[$x][0]){$traffinfo2[$x][3]=1+$traffCntReal2;$traffinfo2[$x][5]++;$traffReal2Cnt=$traffinfo2[$x][5];break;}}//確認
	    }

        $meal=$row["meal"];
        $meal1=($meal%100);
        $meal2=($meal-$meal1)/100;

        $fami=$row["family"];
        $fami1=($fami%100);
        $fami2=($fami-$fami1)/100;

        $day=$row["day"];
        $day1=($day%100);
        $day2=($day-$day1)/100;
        //$day11="";$day12="";$day21="";$day22="";$day31="";$day32="";$day41="";$day42="";$day51="";$day52="";$day61="";$day62="";
        //if ($day1==1){$day11=1;}else if ($day1==2){$day21=1;}else if ($day1==3){$day31=1;}else if ($day1==4){$day41=1;}else if ($day1==5){$day51=1;}else if ($day1==6){$day61=1;}
        //if ($day2==1){$day12=1;}else if ($day2==2){$day22=1;}else if ($day2==3){$day32=1;}else if ($day2==4){$day42=1;}else if ($day2==5){$day52=1;}else if ($day2==6){$day62=1;}

        $mainclass=$row["classfullname"];$supportcalss="";
        if ($row["titleid"]>=5){$mainclass=$row["otherinfo"];$supportcalss=$row["classfullname"];}
        $date=$dateCurr-date('Y',strtotime($row["age"]));

        $c=0;
        $objWorkSheet->setCellValue($col[$c].$iRow,$row["STU_ID"])
                     ->setCellValue($col[++$c].$iRow,$row["name"])//$row["ARE_ID"])
                     ->setCellValue($col[++$c].$iRow,$mainclass)
                     ->setCellValue($col[++$c].$iRow,$row["areaid"])
                     ->setCellValue($col[++$c].$iRow,$row["sex"])//$row["CTP_ID"])
                     ->setCellValue($col[++$c].$iRow,$date)//$row["CTP_ID"])
                     ->setCellValue($col[++$c].$iRow,"高區")
                     ->setCellValue($col[++$c].$iRow,$row["TEL"])
                     ->setCellValue($col[++$c].$iRow," ".$row["CP"])
                     ->setCellValue($col[++$c].$iRow,$row["memo"])
                     ->setCellValue($col[++$c].$iRow,$day1==1 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==1 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==2 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==2 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==3 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==3 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==4 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==4 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==5 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==5 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==6 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==6 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==7 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==7 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==8 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==8 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==9 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==9 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==10 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==10 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==11 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==11 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==12 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==12 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==13 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==13 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==14 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==14 ? (1+$fami2):"")
                     ->setCellValue($col[++$c].$iRow,$day1==15 ? (1+$fami1):"")
                     ->setCellValue($col[++$c].$iRow,$day2==15 ? (1+$fami2):"")
                     //->setCellValue($col[++$c].$iRow,$day1==11 ? "1":"")
                     //->setCellValue($col[++$c].$iRow,$day2==11 ? "1":"")

                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==1) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==2) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==3) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==4) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==5) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==6) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==7) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==8) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==9) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==10) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==11) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==12) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==13) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==14) ? ($meal1):"")
                     ->setCellValue($col[++$c].$iRow,($meal1>=1&&$day1==15) ? ($meal1):"")
                     //->setCellValue($col[++$c].$iRow,($meal1==1&&$day1==11) ? "1":"")

                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==1) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==2) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==3) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==4) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==5) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==6) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==7) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==8) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==9) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==10) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==11) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==12) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==13) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==14) ? ($meal2):"")
                     ->setCellValue($col[++$c].$iRow,($meal2>=1&&$day2==15) ? ($meal2):"")
                     //->setCellValue($col[++$c].$iRow,($meal2==1&&$day2==11) ? "1":"")


                     ->setCellValue($col[++$c].$iRow,$traff1)
                     ->setCellValue($col[++$c].$iRow,$row["cost"]>0 ? $row["cost"]:"")

                     ->setCellValue($col[++$c].$iRow,$row["regdate"])
                     ->setCellValue($col[++$c].$iRow,$row["pay"]>0 ? "1":"")
                     ->setCellValue($col[++$c].$iRow,$row["pay"]>0 ? $row["paydate"]:"")
                     ->setCellValue($col[++$c].$iRow,$row["paybyname"]);
    }

    $iRow+=1;

    // 參加人數, 車資, 繳費 總計
    $sumitem=array($col[10],$col[11],$col[12],$col[13],$col[14],$col[15],$col[16],$col[17],$col[18],$col[19],$col[20],$col[21],$col[22],$col[23],$col[24],$col[25],$col[27],$col[29],$col[32],$col[33]);
    for($w=10;$w<70;$w++)
    {
        $item="=SUM(".$col[$w].($top+$roundcnt).":".$col[$w].($iRow-1).")";
        $objWorkSheet->setCellValue($col[$w].$iRow,$item);
        $objWorkSheet->setCellValue($col[$w].($top-1),$item);
    }

    $item="E".($top+$roundcnt+1);
    $objWorkSheet->freezePane($item);

    // 設定欄位寛度
    for($w=0;$w<count($xlstitle);$w++){$objWorkSheet->getColumnDimension($col[$w])->setWidth($xlstitleW[$w]);}//$xlstitleW[$w]
    for($w=0;$w<count($xlstitlex);$w++){$objWorkSheet->getColumnDimension($col[$mainitem+$applieditem1+1+$w])->setWidth($xlstitlexW[$w]);}
    if ($applieditem1>1){for($i=0;$i<$applieditem1;$i++){$objWorkSheet->getColumnDimension($col[$mainitem+$i+1])->setWidth(10);}}

    $traffwidth=3;if($day1trafficcnt<6){$traffwidth=8;}
    $traffstart=$mainitem+$applieditem1+$applieditem2+1;
    $traffend=$traffstart+$offsettraffic-1;
    if($showtraffprepareitem==true){$traffend+=($day1trafficcnt*$roundcnt);$traffend+=($day2trafficcnt*$roundcnt);}
    for($i=$traffstart;$i<=$traffend;$i++){$objWorkSheet->getColumnDimension($col[$i])->setWidth($traffwidth);}

     // set border
    $range="A".$top.":".$col[$traffend].$iRow;
    $objWorkSheet->getStyle($range)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $range="B3:".$col[$traffend].$iRow;
    $objWorkSheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    if ($roundcnt==2){$range="A3:".$col[$traffend]."5";}else{$range="A3:".$col[$traffend]."4";}
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
