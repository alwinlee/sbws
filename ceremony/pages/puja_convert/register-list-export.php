<?php
    header("Content-Type: text/html; charset=utf-8");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-download");
    header("Content-Type: application/download");
    
	session_start();
	set_time_limit(300); // page execution time = 120 seconds
	
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
	$table_name           =$_POST["tbname"];
	$traffic_table_name   =$_POST["tbtraffic"];
	$regioncode           =$_POST["regioncode"]; 
    $year                 =GetCurrentYear();// 跟朝禮法會相當時間
    $curyear              =GetCurrentYear();	
    $table_title          =$year."年 報名名冊";//.$traffic_table_name;//.$statistic_table_name;
	
	// 取得車次表 day 1 & day 2
	$sql = "SELECT * FROM ".$traffic_table_name." WHERE `day`=0 ORDER BY `traffid` ASC";
	$pujatraffic_result=mysql_query($sql);
	$numrows = mysql_num_rows($pujatraffic_result);	
	$day1trafficcnt = 0;	
	if ($numrows > 0)
	{
		$i=0;//2;
		//$traffinfo1[0][0] = "Z";	$traffinfo1[0][1] = "自往";		$traffinfo1[0][2] = 0;	$traffinfo1[0][3] = 0; $traffinfo1[0][4] = 0;  $traffinfo1[0][5] = 0;
		//$traffinfo1[1][0] = "ZZ";	$traffinfo1[1][1] = "共乘";		$traffinfo1[1][2] = 0;	$traffinfo1[1][3] = 0; $traffinfo1[1][4] = 0;  $traffinfo1[1][5] = 0;	
		while($traffic_row = mysql_fetch_array($pujatraffic_result, MYSQL_ASSOC))
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
	
	$sql = "SELECT * FROM ".$traffic_table_name." WHERE `day`=1 ORDER BY `traffid` ASC";
	$pujatraffic_result = mysql_query($sql);
	$numrows = mysql_num_rows($pujatraffic_result); 
	
	$day2trafficcnt = 0;
	if ($numrows > 0)
	{
		$i=0;//2;
		//$traffinfo2[0][0] = "Z";	$traffinfo2[0][1] = "自往";		$traffinfo2[0][2] = 0;	$traffinfo2[0][3] = 0;  $traffinfo2[0][4] = 0;  $traffinfo2[0][5] = 0;
		//$traffinfo2[1][0] = "ZZ";	$traffinfo2[1][1] = "共乘";		$traffinfo2[1][2] = 0;	$traffinfo2[1][3] = 0;  $traffinfo2[1][4] = 0;  $traffinfo2[1][5] = 0;
		while($traffic_row = mysql_fetch_array($pujatraffic_result, MYSQL_ASSOC))
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

	if($regioncode==""||$regioncode=="*"||$regioncode==" "){$sql = "SELECT * FROM ".$table_name." WHERE (`day`> 0) ORDER BY `idx` ASC";}
	else{$sql = "SELECT * FROM ".$table_name." WHERE (`day`> 0 AND `areaid`='".$regioncode."') ORDER BY `idx` ASC";}
	
	//$sql="select * from `sdm_clsmembers` JOIN `".$table_name."` ON sdm_clsmembers.STU_ID=".$table_name.".STU_ID WHERE (`day`> 0 AND `Status`='參與') ORDER BY `idx` ASC";
	//$sql="select * from `sdm_clsmembers` JOIN `".$table_name."` ON (sdm_clsmembers.STU_ID=".$table_name.".STU_ID AND sdm_clsmembers.TTL_ID=".$table_name.".TTL_ID) WHERE (`day`> 0 AND `Status`='參與') ORDER BY `idx` ASC";	
/*
	$sql="select * from `sdm_clsmembers` JOIN `".$table_name."` ON (sdm_clsmembers.STU_ID=".$table_name.".STU_ID AND sdm_clsmembers.TTL_ID=".$table_name.".TTL_ID) ";
	$sql.=" JOIN `sdm_classes` ON (sdm_clsmembers.CLS_ID=sdm_classes.CLS_ID) ";
	$sql.="WHERE (`day`> 0 AND `Status`='參與') ORDER BY `MembersSeq` ASC";//$sql.="WHERE (`day`> 0 AND `Status`='參與') ORDER BY `idx` ASC";	
*/	
	//$sql="select sdm_clsmembers.STU_ID,sdm_clsmembers.TTL_ID,sdm_clsmembers.MembersSeq,".$tbname.".idx,".$tbname.".name";
	//$sql.=" from `sdm_clsmembers` LEFT JOIN `".$tbname."` ON sdm_clsmembers.STU_ID=".$tbname.".STU_ID  ";
	
	$result  = mysql_query($sql);
	$numrows = mysql_num_rows($result); 				
	if ($numrows < 0)
	    exit;
    //------------------------------------------------------------------------------------------------------------------------------			
	// Create new PHPExcel object
	$nSheet = 0;	
	$col = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
	             "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
				 "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
				 "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ");
				 
	$objPHPExcel = new PHPExcel();
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
								 ->setLastModifiedBy("Maarten Balliauw")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");
							 
	$objWorkSheet = $objPHPExcel->setActiveSheetIndex($nSheet);
	
	$trafficstartcol = 21-2-1;//影響交通車次表的位置
	$item = "A1:".$col[$trafficstartcol+$day1trafficcnt+$day1trafficcnt-1]."1";	
	// set title	
	$objWorkSheet->mergeCells($item);	
	$objWorkSheet->setCellValue("A1",$table_title); //合併後的儲存格		
	$objWorkSheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objWorkSheet->getStyle("A1")->getFont()->setSize(16);
      $objWorkSheet->getStyle("A1")->getFont()->setBold(true);
	$objWorkSheet->getRowDimension("1")->setRowHeight(30);
	
	$objWorkSheet->mergeCells("A2:A3");	
	$objWorkSheet->setCellValue("A2","項次");	
	$objWorkSheet->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("A2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	

	$objWorkSheet->mergeCells("B2:B3");		
	$objWorkSheet->setCellValue("B2","姓名");		
	$objWorkSheet->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("B2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objWorkSheet->mergeCells("C2:C3");	
	$objWorkSheet->setCellValue("C2","班級");	
	$objWorkSheet->getStyle("C2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("C2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objWorkSheet->mergeCells("D2:D3");	
	$objWorkSheet->setCellValue("D2","區域");	
	$objWorkSheet->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("D2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objWorkSheet->mergeCells("E2:E3");	
	$objWorkSheet->setCellValue("E2","代碼");
	$objWorkSheet->getStyle("E2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("E2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	
	$objWorkSheet->mergeCells("F2:F3");	
	$objWorkSheet->setCellValue("F2","身份");
	$objWorkSheet->getStyle("F2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("F2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objWorkSheet->mergeCells("G2:G3");	
	$objWorkSheet->setCellValue("G2","報名日期");
	$objWorkSheet->getStyle("G2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("G2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	
	$objWorkSheet->mergeCells("H2:H3");	
	$objWorkSheet->setCellValue("H2","繳費日期");
	$objWorkSheet->getStyle("H2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("H2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objWorkSheet->mergeCells("I2:I3");	
	$objWorkSheet->setCellValue("I2","繳費");
	$objWorkSheet->getStyle("I2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("I2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objWorkSheet->mergeCells("J2:J3");	
	$objWorkSheet->setCellValue("J2","收費窗口");
	$objWorkSheet->getStyle("J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("J2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objWorkSheet->mergeCells("K2:K3");	
	$objWorkSheet->setCellValue("K2","備註");
	$objWorkSheet->getStyle("K2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("K2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objWorkSheet->mergeCells("L2:M2");	
	$objWorkSheet->setCellValue("L2","鳳山體育場");	
	$objWorkSheet->setCellValue("L3","11/14(六)");
	$objWorkSheet->setCellValue("M3","11/15(日)");	
	$objWorkSheet->getStyle("L2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("L2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("L3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("M3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("M3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("M3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	
	$objWorkSheet->mergeCells("N2:O2");	
	$objWorkSheet->setCellValue("N2","台南高工");	
	$objWorkSheet->setCellValue("N3","11/14(六)");
	$objWorkSheet->setCellValue("O3","11/15(日)");	
	$objWorkSheet->getStyle("N2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("N2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("N3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("O3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("O3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("O3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	
	$objWorkSheet->mergeCells("P2:Q2");	
	$objWorkSheet->setCellValue("P2","屏東大仁科大");	
	$objWorkSheet->setCellValue("P3","11/14(六)");
	$objWorkSheet->setCellValue("Q3","11/15(日)");	
	$objWorkSheet->getStyle("P2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("P2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("P3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("Q3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("Q3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("Q3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	
	$objWorkSheet->mergeCells("R2:S2");	
	$objWorkSheet->setCellValue("R2","-");	
	$objWorkSheet->setCellValue("R3","11/14(六)");
	$objWorkSheet->setCellValue("S3","11/15(日)");	
	$objWorkSheet->getStyle("R2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("R2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("R3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("S3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("S3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("S3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);		
	
	$objWorkSheet->mergeCells("T2:U2");	
	$objWorkSheet->setCellValue("T2","-");	
	$objWorkSheet->setCellValue("T3","11/14(六)");
	$objWorkSheet->setCellValue("U3","11/15(日)");	
	$objWorkSheet->getStyle("T2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("T2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("T3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("U3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	$objWorkSheet->getStyle("U3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objWorkSheet->getStyle("U3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	
    // 確認搭車統計 title	
	if ($day1trafficcnt > 0)
	{ 
	    $begin=($trafficstartcol);
	    $end=($trafficstartcol+$day1trafficcnt-1);		
		$item = $col[$begin]."2";
		$objWorkSheet->mergeCells($col[$begin]."2:".$col[$end]."2");
		$objWorkSheet->setCellValue($col[$begin]."2","11/14 (六) 確認搭車統計");
		$objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
		for($i = 0;$i < $day1trafficcnt;$i++)
		{
			$item = $col[$begin+$i]."3";
			$objWorkSheet->setCellValue($item,$traffinfo1[$i][0]);	
			$objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
		}
	}
	
	if ($day2trafficcnt > 0)
	{
	    $begin=($trafficstartcol+$day1trafficcnt);
	    $end=($trafficstartcol+$day1trafficcnt+$day2trafficcnt-1);	
	    $item = $col[$begin]."2";	
		$objWorkSheet->mergeCells($col[$begin]."2:".$col[$trafficstartcol2+$end]."2");
		$objWorkSheet->setCellValue($col[$begin]."2","11/15(日) 確認搭車統計");
		$objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
		for($i = 0;$i < $day2trafficcnt;$i++)
		{
			$item = $col[$begin+$i]."3";
			$objWorkSheet->setCellValue($item,$traffinfo2[$i][0]);	
			$objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
		}	
	}
	$idx=0;	
	$iRow=3;	
	$notjoinsum=0;
	$servicesum=0; 
	$registersum=0;
	$paysum=0;
	$day11sum=0;
	$day12sum=0;
	$day21sum=0;
	$day22sum=0;
	$day31sum=0;
	$day32sum=0;	
	$day41sum=0;
	$day42sum=0;
	$day51sum=0;
	$day52sum=0;	
	// 填寫資料
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$idx++;	
		$iRow++;
		$pay="";$paydate="";$payto="";
		if ($row["pay"] > 0) {$pay = "1";$paysum++;$paydate=$row["paydate"];$payto=$row["paybyname"];}			

		$servicesum += $row["service"];
		// traffic 
		for($x=0;$x<$day1trafficcnt;$x++) {$traffinfo1[$x][2]=0;$traffinfo1[$x][3]=0;}//2:預計搭該車次人數,3:確認搭該車次人數(因為family不能參加,故參加則人數應該是1)
		for($x=0;$x<$day2trafficcnt;$x++) {$traffinfo2[$x][2]=0;$traffinfo2[$x][3]=0;}//2:預計搭該車次人數,3:確認搭該車次人數(因為family不能參加,故參加則人數應該是1)

		$traff1="Z";$traff2="Z";$traffReal1="Z";$traffReal2="Z";
		$traff=explode(",",$row["traff"]);
		$traffReal=explode(",",$row["traffReal"]);
		if (count($traff)>0){$traff1=$traff[0];}if (count($traff)>1){$traff2=$traff[1];}
		if (count($traffReal)>0){$traffReal1=$traffReal[0];}if (count($traffReal)>1){$traffReal2=$traffReal[1];}

		if (($row["day"]%10)>=1)//有參加第一天
		{ 
			for($x=0;$x<$day1trafficcnt;$x++)//預計
			{
				if ($traff1==$traffinfo1[$x][0])
				{
					$traffinfo1[$x][2]=1;// $row["family"] + 1;
					$traffinfo1[$x][4]+=$traffinfo1[$x][2]; //預計搭此車次之總和
					break;
				}
			}
			for($x=0;$x<$day1trafficcnt;$x++)//確認
			{
				if ($traffReal1==$traffinfo1[$x][0])
				{
					$traffinfo1[$x][3]=1;// $row["family"] + 1;
					$traffinfo1[$x][5]+=$traffinfo1[$x][3]; //預計搭此車次之總和
					break;
				}
			}				
		}
		if ($row["day"]>=10)//有參加第二天
		{ 
			for($x=0;$x<$day2trafficcnt;$x++)//預計
			{
				if ($traff2==$traffinfo2[$x][0])
				{
					$traffinfo2[$x][2]=1;// $row["family"] + 1;
					$traffinfo2[$x][4]+=$traffinfo2[$x][2];//預計搭此車次之總和
					break;
				}
			}
			for($x=0;$x<$day2trafficcnt;$x++)//確認
			{
				if ($traffReal2==$traffinfo2[$x][0])
				{
					$traffinfo2[$x][3]=1;// $row["family"] + 1;
					$traffinfo2[$x][5]+=$traffinfo2[$x][3];//預計搭此車次之總和
					break;
				}
			}				
		}
		
	    $day=$row["day"];
		$day1=($day%10);
		$day2=($day-$day1)/10;
		$day11="";$day12="";$day21="";$day22="";$day31="";$day32="";$day41="";$day42="";$day51="";$day52="";
		if ($day1==1){$day11=1;$day11sum++;}else if ($day1==2){$day21=1;$day21sum++;}else if ($day1==3){$day31=1;$day31sum++;}else if ($day1==4){$day41=1;$day41sum++;}else if ($day1==5){$day51=1;$day51sum++;}
		if ($day2==1){$day12=1;$day12sum++;}else if ($day2==2){$day22=1;$day22sum++;}else if ($day2==3){$day32=1;$day32sum++;}else if ($day2==4){$day42=1;$day42sum++;}else if ($day2==5){$day52=1;$day52sum++;}	
		$objWorkSheet->setCellValue($col[0].$iRow, $idx)
					 ->setCellValue($col[1].$iRow, $row["name"])		
					 ->setCellValue($col[2].$iRow, $row["classname"])//$row["Class"])					 
					 ->setCellValue($col[3].$iRow, $row["area"])//$row["ARE_ID"])						
					 ->setCellValue($col[4].$iRow, $row["areaid"])//$row["CTP_ID"])			 
					 ->setCellValue($col[5].$iRow, $row["title"])
					 ->setCellValue($col[6].$iRow, $row["regdate"])
					 ->setCellValue($col[7].$iRow, $paydate)					 
					 ->setCellValue($col[8].$iRow, $pay)
					 ->setCellValue($col[9].$iRow, $payto)
					 ->setCellValue($col[10].$iRow, $row["memo"])					 
					 ->setCellValue($col[11].$iRow, $day11)
					 ->setCellValue($col[12].$iRow, $day12)
					 ->setCellValue($col[13].$iRow, $day21)
					 ->setCellValue($col[14].$iRow, $day22)
					 ->setCellValue($col[15].$iRow, $day31)
					 ->setCellValue($col[16].$iRow, $day32)					 
					 ->setCellValue($col[17].$iRow, $day41)
					 ->setCellValue($col[18].$iRow, $day42)					 
					 ->setCellValue($col[19].$iRow, $day51)
					 ->setCellValue($col[20].$iRow, $day52);					 
					 //->setCellValue($col[8].$iRow, $row["studentid"])						 
					 //->setCellValue($col[9].$iRow, $row["sex"])					 
					 //->setCellValue($col[10].$iRow, $row["age"])						 
					 //->setCellValue($col[11].$iRow, $row["edu"])					 
					 //->setCellValue($col[12].$iRow, $row["school"])						 
					 //->setCellValue($col[13].$iRow, $row["jobtype"])	
					 //->setCellValue($col[14].$iRow, $row["company"])						 
					 //->setCellValue($col[15].$iRow, $row["jobtitle"]);
					 
		for($x = 0; $x < $day1trafficcnt;$x++)
		{
		    //$objWorkSheet->setCellValue($col[$trafficstartcol+$x].$iRow, $traffinfo1[$x][2]);//預計
			if ($traffinfo1[$x][3]<=0){continue;}
		    $objWorkSheet->setCellValue($col[$trafficstartcol+$x].$iRow, $traffinfo1[$x][3]);//確認		
		}
			
		for($x = 0; $x < $day2trafficcnt;$x++)
        {		
		    //$objWorkSheet->setCellValue($col[$trafficstartcol+$day1trafficcnt+$x].$iRow, $traffinfo2[$x][2]);//預計
			if ($traffinfo2[$x][3]<=0){continue;}
		    $objWorkSheet->setCellValue($col[$trafficstartcol+$day1trafficcnt+$x].$iRow, $traffinfo2[$x][3]);//確認			
		}
	}
	
	$iRow += 1;
	$objWorkSheet->setCellValue($col[8].$iRow, $paysum)			 
				 ->setCellValue($col[11].$iRow, $day11sum)
				 ->setCellValue($col[12].$iRow, $day12sum)
				 ->setCellValue($col[13].$iRow, $day21sum)
				 ->setCellValue($col[14].$iRow, $day22sum)
				 ->setCellValue($col[15].$iRow, $day31sum)
				 ->setCellValue($col[16].$iRow, $day32sum)				 
				 ->setCellValue($col[17].$iRow, $day41sum)
				 ->setCellValue($col[18].$iRow, $day42sum)				 
				 ->setCellValue($col[19].$iRow, $day51sum)
				 ->setCellValue($col[20].$iRow, $day52sum);
				 
	for($x = 0; $x < $day1trafficcnt;$x++)//第一天
	{
		//$objWorkSheet->setCellValue($col[$trafficstartcol+$x].$iRow, $traffinfo1[$x][4]);//預計搭車總計
		$objWorkSheet->setCellValue($col[$trafficstartcol+$x].$iRow, $traffinfo1[$x][5]);//確認搭車總計		
	}
		
	for($x = 0; $x < $day2trafficcnt;$x++)//第二天
	{		
		//$objWorkSheet->setCellValue($col[$trafficstartcol+$day1trafficcnt+$x].$iRow, $traffinfo2[$x][4]);//預計搭車總計
		$objWorkSheet->setCellValue($col[$trafficstartcol+$day1trafficcnt+$x].$iRow, $traffinfo2[$x][5]);//確認搭車總計			
	}	
	
    $iEndCol = $trafficstartcol+$day1trafficcnt+$day2trafficcnt-1;//+$day2trafficcnt+$day2trafficcnt-1;
	// set border
	$range = "A2:".$col[$iEndCol].$iRow;
	$objWorkSheet->getStyle($range)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$range = "B2:".$col[$iEndCol].$iRow;
	$objWorkSheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	//$range = "O2:O".$iRow;$objWorkSheet->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	
	$range = "A2:".$col[$iEndCol]."3";
	//$objWorkSheet->getStyle("A2")->getFill()->getStartColor()->setRGB('B7B7B7');

    $objWorkSheet->getStyle($range)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objWorkSheet->getStyle($range)->getFill()->getStartColor()->setRGB('DDFFDD');
	
	//$objWorkSheet->freezePane('A4');
	$objWorkSheet->freezePane('C4');
	
	$objWorkSheet->getColumnDimension("A")->setWidth(6);
	$objWorkSheet->getColumnDimension("B")->setWidth(13);	
	$objWorkSheet->getColumnDimension("C")->setWidth(24);		
	$objWorkSheet->getColumnDimension("D")->setWidth(8);
	$objWorkSheet->getColumnDimension("E")->setWidth(8);		
	$objWorkSheet->getColumnDimension("F")->setWidth(10);	
  	$objWorkSheet->getColumnDimension("G")->setWidth(12);	
	$objWorkSheet->getColumnDimension("H")->setWidth(12);	
	$objWorkSheet->getColumnDimension("I")->setWidth(12);
	$objWorkSheet->getColumnDimension("J")->setWidth(12);
	$objWorkSheet->getColumnDimension("K")->setWidth(16);
	$objWorkSheet->getColumnDimension("L")->setWidth(10);
	$objWorkSheet->getColumnDimension("M")->setWidth(10);	
	for($i = $trafficstartcol; $i <= $iEndCol; $i++)
		$objWorkSheet->getColumnDimension($col[$i])->setWidth(4);	

	// Rename worksheet
	$objWorkSheet->setTitle($table_title);

	//--------------------------------------------------------------------------------------------------------------------------------------------------	
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	
	//header('Content-Disposition: attachment;filename="simple.xls"');
	$fileheader = "Content-Disposition: attachment;filename=\"".$table_title.".xls\"";
    header($fileheader);

	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
?>