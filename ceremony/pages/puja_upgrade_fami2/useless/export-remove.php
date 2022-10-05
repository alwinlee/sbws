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
    $year=GetCurrentYear();// 跟朝禮法會相當時間
    $curyear=GetCurrentYear();   
    $table_title=$pujatitle." 取消報名名冊";//.$traffic_table_name;//.$statistic_table_name;

    $Car=false;
    $showtraffconfirmitem=false;//是否匯出確認搭車資料
    $showtraffprepareitem=false;//是否匯出預計搭車資料    
    // 取得車次表 day 1 & day 2
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=0 ORDER BY `traffid` ASC";
    $pujatraffic_result=mysql_query($sql);
    $numrows=mysql_num_rows($pujatraffic_result);	
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
	
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=1 ORDER BY `traffid` ASC";
    $pujatraffic_result = mysql_query($sql);
    $numrows=mysql_num_rows($pujatraffic_result);
    $day2trafficcnt=0;

    if ($numrows > 0)
    {
        $i=0;//2;
        //$traffinfo2[0][0] = "Z";	$traffinfo2[0][1] = "自往";		$traffinfo2[0][2] = 0;	$traffinfo2[0][3] = 0;  $traffinfo2[0][4] = 0;  $traffinfo2[0][5] = 0;
        //$traffinfo2[1][0] = "ZZ";	$traffinfo2[1][1] = "共乘";		$traffinfo2[1][2] = 0;	$traffinfo2[1][3] = 0;  $traffinfo2[1][4] = 0;  $traffinfo2[1][5] = 0;
        while($traffic_row = mysql_fetch_array($pujatraffic_result, MYSQL_ASSOC))
        {	
            $traffinfo2[$i][0]=$traffic_row["traffid"];
            $traffinfo2[$i][1]=$traffic_row["traffname"];	
            $traffinfo2[$i][2]=0;	// 預計
            $traffinfo2[$i][3]=0;	// 確認		
            $traffinfo2[$i][4]=0;	// 預計總和
            $traffinfo2[$i][5]=0;	// 確認總和		
            $i++;
        }
        $day2trafficcnt = count($traffinfo2);	
    }	
   
    // 考慮不同區域的報名窗口
    if($regioncode==""||$regioncode=="*"||$regioncode==" ")
    {
        if ($orderbydate=="YES"){$sql="SELECT * FROM ".$table_name." WHERE (`service`> 0) ORDER BY `regdate` ASC,`CLS_ID` ASC,`TTL_ID` ASC,`memberseq` ASC, `idx` ASC";}
        else{$sql="SELECT * FROM ".$table_name." WHERE (`service`> 0) ORDER BY `CLS_ID` ASC,`TTL_ID` ASC,`memberseq` ASC, `idx` ASC";}
    }
    else
    {
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
        if ($orderbydate=="YES"){$sql="SELECT * FROM ".$table_name." WHERE (`service`> 0 ".$areakey.") ORDER BY `regdate` ASC,`CLS_ID` ASC,`TTL_ID` ASC,`memberseq` ASC,`idx` ASC";}
        else{$sql="SELECT * FROM ".$table_name." WHERE (`service`> 0 ".$areakey.") ORDER BY `CLS_ID` ASC,`TTL_ID` ASC,`memberseq` ASC, `idx` ASC";}
    }    
    
    $result=mysql_query($sql);
    $numrows=mysql_num_rows($result); 				
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
                     
    $xlstitle=array("學員代號","姓名","母班班級","代碼","性別","年齡","區域","電話(宅)","電話(手機)","備註","","");
    $xlstitleW=array(12,12,16,8,8,8,8,14,14,24,10,10,10,10,10,10,12,8);
                     
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
    $objWorkSheet->mergeCells("K3:L3");
    $objWorkSheet->setCellValue("K3","取消場次日期");
    $objWorkSheet->setCellValue("K4","11/14(六)");	
    $objWorkSheet->setCellValue("L4","11/15(日)");	
    $objWorkSheet->getStyle("K3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objWorkSheet->getStyle("L3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);     

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
    $applieditem2=0;//報名日期,繳費日期,繳費,收費窗口
    if($Car==false){$applieditem2=0;}
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

    //$xlstitlex=array("報名日期","繳費","繳費日期","收費窗口");
    //$xlstitlexW=array(12,8,12,20);   
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
    
    //預排交通車號次 - title
    if($Car==true)
    {
        $item=$col[$mainitem+$applieditem1+1+$w].$top.":".$col[$mainitem+$applieditem1+1+1+$w].($top);
        $objWorkSheet->mergeCells($item);
        $item=$col[$mainitem+$applieditem1+1+$w].$top;
        $objWorkSheet->setCellValue($item,"預排交通車號次");      
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);  
        
        $item=$col[$mainitem+$applieditem1+1+$w].($top+1);
        $objWorkSheet->setCellValue($item,"11/14(六)");
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);  
        
        $item=$col[$mainitem+$applieditem1+1+1+$w].($top+1);
        $objWorkSheet->setCellValue($item,"11/15(日)");    
        $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);         
    }  
   
    // 確認搭車統計 title
    if($showtraffconfirmitem==true)
    {
        if ($day1trafficcnt>0)
        { 
            $begin=($mainitem+$applieditem1+$applieditem2+1);
            $end=($begin+($day1trafficcnt*$roundcnt)-1);		
            $item=$col[$begin].$top;
            $objWorkSheet->mergeCells($col[$begin].$top.":".$col[$end].$top);
            $objWorkSheet->setCellValue($col[$begin].$top,"確認搭車-11/14(六)");
            $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
            for($i=0,$w=0;$i<($day1trafficcnt*$roundcnt);$i+=$roundcnt,$w++)
            {
                if ($roundcnt>=2)
                {
                    $item=$col[$begin+$i].($top+1).":".$col[$begin+$i+($roundcnt-1)].($top+1);
                    $objWorkSheet->mergeCells($item);
                    $item=$col[$begin+$i].($top+2);$objWorkSheet->setCellValue($item,"去");
                    $item=$col[$begin+$i+1].($top+2);$objWorkSheet->setCellValue($item,"回");

                    $item=$col[$begin+$i+1].($top-1);
                    $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);                     
                }
                $item=$col[$begin+$i].($top+1);
                $objWorkSheet->setCellValue($item,$traffinfo1[$w][0]);
                $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                
                $item=$col[$begin+$i].($top-1);
                $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);                
            }
        }
        
        if ($day2trafficcnt>0)
        {
            $begin=($mainitem+$applieditem1+$applieditem2+($day1trafficcnt*$roundcnt)+1);
            $end=($begin+($day2trafficcnt*$roundcnt)-1);	
            $item=$col[$begin].$top;	
            $objWorkSheet->mergeCells($col[$begin].$top.":".$col[$end].$top);
            $objWorkSheet->setCellValue($col[$begin].$top,"確認搭車-11/15(日)");
            $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
            for($i=0,$w=0;$i<($day2trafficcnt*$roundcnt);$i+=$roundcnt,$w++)
            {
                if ($roundcnt>=2)
                {
                    $item=$col[$begin+$i].($top+1).":".$col[$begin+$i+($roundcnt-1)].($top+1);
                    $objWorkSheet->mergeCells($item);
                    $item=$col[$begin+$i].($top+2);$objWorkSheet->setCellValue($item,"去");
                    $item=$col[$begin+$i+1].($top+2);$objWorkSheet->setCellValue($item,"回");
                    
                    $item=$col[$begin+$i+1].($top-1);
                    $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);                     
                }                
                $item=$col[$begin+$i].($top+1);
                $objWorkSheet->setCellValue($item,$traffinfo2[$w][0]);	
                $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                
                $item=$col[$begin+$i].($top-1);
                $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);                
            }	
        }        
    }        
 
    // 預計搭車統計 title
    $offsettraffic=0;
    if($showtraffprepareitem==true)
    {
        if($showtraffconfirmitem==true){$offsettraffic=(($day1trafficcnt+$day2trafficcnt)*$roundcnt);}
        if ($day1trafficcnt>0)
        { 
            $begin=($mainitem+$applieditem1+$applieditem2+1+$offsettraffic);
            $end=($begin+($day1trafficcnt*$roundcnt)-1);		
            $item=$col[$begin].$top;
            $objWorkSheet->mergeCells($col[$begin].$top.":".$col[$end].$top);
            $objWorkSheet->setCellValue($col[$begin]."3","預計搭車-11/14(六)");
            $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
            for($i=0,$w=0;$i<($day1trafficcnt*$roundcnt);$i+=$roundcnt,$w++)
            {
                if ($roundcnt>=2)
                {
                    $item=$col[$begin+$i].($top+1).":".$col[$begin+$i+($roundcnt-1)].($top+1);
                    $objWorkSheet->mergeCells($item);
                    $item=$col[$begin+$i].($top+2);$objWorkSheet->setCellValue($item,"去");
                    $item=$col[$begin+$i+1].($top+2);$objWorkSheet->setCellValue($item,"回");
                    
                    $item=$col[$begin+$i+1].($top-1);
                    $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);                      
                }                
                $item=$col[$begin+$i].($top+1);
                $objWorkSheet->setCellValue($item,$traffinfo1[$w][0]);	
                $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                
                $item=$col[$begin+$i].($top-1);
                $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);                
            }
        }
 
        if ($day2trafficcnt>0)
        {
            $begin=($mainitem+$applieditem1+$applieditem2+1+$offsettraffic+($day1trafficcnt*$roundcnt));
            $end=($begin+($day2trafficcnt*$roundcnt)-1);	
            $item=$col[$begin].$top;	
            $objWorkSheet->mergeCells($col[$begin].$top.":".$col[$end].$top);
            $objWorkSheet->setCellValue($col[$begin]."3","預計搭車-11/15(日)");
            $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
            for($i=0,$w=0;$i<($day2trafficcnt*$roundcnt);$i+=$roundcnt,$w++)
            {
                if ($roundcnt>=2)
                {
                    $item=$col[$begin+$i].($top+1).":".$col[$begin+$i+($roundcnt-1)].($top+1);
                    $objWorkSheet->mergeCells($item);
                    $item=$col[$begin+$i].($top+2);$objWorkSheet->setCellValue($item,"去");
                    $item=$col[$begin+$i+1].($top+2);$objWorkSheet->setCellValue($item,"回");
                    
                    $item=$col[$begin+$i+1].($top-1);
                    $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);                      
                }        
                
                $item=$col[$begin+$i].($top+1);
                $objWorkSheet->setCellValue($item,$traffinfo2[$w][0]);	
                $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                
                $item=$col[$begin+$i].($top-1);
                $objWorkSheet->getStyle($item)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objWorkSheet->getStyle($item)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);                
            }
        }        
    } 
   
    $idx=0;	
    $iRow=$top+$roundcnt;
    // 填寫資料
    $trafficstartcol=$mainitem+$applieditem1+$applieditem2+1;
    $traffCnt1="0";$traffCnt2="0";$traffCntReal1="0";$traffCntReal2="0"; 
    while($row=mysql_fetch_array($result, MYSQL_ASSOC))
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
		
        $day=$row["service"];
        $day1=($day%10);
        $day2=($day-$day1)/10;
        $day11="";$day12="";$day21="";$day22="";$day31="";$day32="";$day41="";$day42="";$day51="";$day52="";
        if ($day1==1){$day11=1;}else if ($day1==2){$day21=1;}else if ($day1==3){$day31=1;}else if ($day1==4){$day41=1;}else if ($day1==5){$day51=1;}
        if ($day2==1){$day12=1;}else if ($day2==2){$day22=1;}else if ($day2==3){$day32=1;}else if ($day2==4){$day42=1;}else if ($day2==5){$day52=1;}	
            
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
                     ->setCellValue($col[++$c].$iRow,"南區")
                     ->setCellValue($col[++$c].$iRow,$row["PhoneNo_H"])				 
                     ->setCellValue($col[++$c].$iRow," ".$row["PhoneNo_C"])
                     ->setCellValue($col[++$c].$iRow,$row["memo"])                     
                     ->setCellValue($col[++$c].$iRow,$day11==1 ? "1":"")
                     ->setCellValue($col[++$c].$iRow,$day12==1 ? "1":"");
                     
                     //->setCellValue($col[++$c].$iRow,$traff1)
                     //->setCellValue($col[++$c].$iRow,$row["cost"]>0 ? $row["cost"]:"")                    

                     //->setCellValue($col[++$c].$iRow,$row["regdate"])                     
                     //->setCellValue($col[++$c].$iRow,$row["pay"]>0 ? "1":"") 
                     //->setCellValue($col[++$c].$iRow,$row["pay"]>0 ? $row["paydate"]:"")                      
                     //->setCellValue($col[++$c].$iRow,$row["paybyname"]);
       
    }	

    $iRow+=1;
    
    // 參加人數, 車資, 繳費 總計
    $sumitem=array($col[10],$col[11]);
    for($w=0;$w<count($sumitem);$w++)
    {
        $item="=SUM(".$sumitem[$w].($top+$roundcnt).":".$sumitem[$w].($iRow-1).")";
        $objWorkSheet->setCellValue($sumitem[$w].$iRow,$item);
        $objWorkSheet->setCellValue($sumitem[$w].($top-1),$item);
    }
 
    if($showtraffconfirmitem==true)//確認搭車總計
    {
        for($x=0;$x<($day1trafficcnt*$roundcnt);$x++) {//第一天                   
            $item="=SUM(".$col[$trafficstartcol+$x].($top+$roundcnt).":".$col[$trafficstartcol+$x].($iRow-1).")";
            $objWorkSheet->setCellValue($col[$trafficstartcol+$x].$iRow,$item);
            $objWorkSheet->setCellValue($col[$trafficstartcol+$x].($top-1),$item);            
        }            
        for($x = 0;$x<($day2trafficcnt*$roundcnt);$x++){//第二天                   
            $item="=SUM(".$col[$trafficstartcol+($day1trafficcnt*$roundcnt)+$x].($top+$roundcnt).":".$col[$trafficstartcol+$day1trafficcnt+$x].($iRow-1).")";
            $objWorkSheet->setCellValue($col[$trafficstartcol+$day1trafficcnt+$x].$iRow,$item);//確認搭車總計
            $objWorkSheet->setCellValue($col[$trafficstartcol+$day1trafficcnt+$x].($top-1),$item);//確認搭車總計-on top			
        } 
    }  

    if($showtraffprepareitem==true)//預計搭車總計
    {
        for($x=0;$x<($day1trafficcnt*$roundcnt);$x++){//第一天        
            $item="=SUM(".$col[$trafficstartcol+$offsettraffic+$x].($top+$roundcnt).":".$col[$trafficstartcol+$offsettraffic+$x].($iRow-1).")";
            $objWorkSheet->setCellValue($col[$trafficstartcol+$offsettraffic+$x].$iRow,$item);//確認搭車總計
            $objWorkSheet->setCellValue($col[$trafficstartcol+$offsettraffic+$x].($top-1),$item);//確認搭車總計            
        }            
        for($x=0;$x<($day2trafficcnt*$roundcnt);$x++){//第二天        		
            $item="=SUM(".$col[$trafficstartcol+$offsettraffic+($day1trafficcnt*$roundcnt)+$x].($top+$roundcnt).":".$col[$trafficstartcol+$offsettraffic+($day1trafficcnt*$roundcnt)+$x].($iRow-1).")";
            $objWorkSheet->setCellValue($col[$trafficstartcol+$offsettraffic+$day1trafficcnt+$x].$iRow,$item);//確認搭車總計
            $objWorkSheet->setCellValue($col[$trafficstartcol+$offsettraffic+$day1trafficcnt+$x].($top-1),$item);//確認搭車總計			
        } 
    }   

    $item="E".($top+$roundcnt+1);
    $objWorkSheet->freezePane($item);
    
    // 設定欄位寛度
    for($w=0;$w<count($xlstitle);$w++){$objWorkSheet->getColumnDimension($col[$w])->setWidth($xlstitleW[$w]);}//$xlstitleW[$w]
    for($w=0;$w<count($xlstitlex);$w++){$objWorkSheet->getColumnDimension($col[$mainitem+$applieditem1+1+$w])->setWidth($xlstitlexW[$w]);}
    if ($applieditem1>1){for($i=0;$i<$applieditem1;$i++){$objWorkSheet->getColumnDimension($col[$mainitem+$i+1])->setWidth(10);}}
    
    $traffwidth=3;if($day1trafficcnt<6){$traffwidth=6;}
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