<?php
    header("Content-Type: text/html; charset=utf-8");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-download");
    header("Content-Type: application/download");

    ini_set('memory_limit', -1 );		
    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close	
    //error_reporting(E_ALL & ~E_NOTICE);	

    //if($_GET["class"]=="") {
    //	header("Location: ..\index.php");
    //}	
	
    require_once('../../../_res/tcpdf/tcpdf.php');		
    require_once("../../../_res/_inc/connMysql.php"); 
    require_once("../../../_res/_inc/sharelib.php");

    set_time_limit(120);
    session_start();	
	
    $table_name =$_POST["tbname"];
    $classname  =$_POST["classname"];
    $classid    =$_POST["classid"];
    $Year       =GetNewYearPUJAYear(); // 跟朝禮法會相當時間
    $curYear    =GetCurrentYear();

	$table_title = $Year." 年 八月份提昇營 (".$classname."班)";	
	//echo $classid;	
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Noman');
	$pdf->SetTitle($table_title);
	$pdf->SetSubject('');

	// set default header data
	$tablename = "";//"報名名冊";	    
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $table_title, $tablename);

	// set header and footer fonts
	$pdf->setHeaderFont(Array('droidsansfallback', 'center', 16));
	$pdf->setFooterFont(Array('droidsansfallback', 'right', 8));

	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	//$pdf->setPrintHeader(false);
	//set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(8, 24, 8);//(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	// set font
	$pdf->SetFont('droidsansfallback','', 8);

	
    //------------------------------------------------------------------------------------------------------------------------------
	// get data from database 
	//$sql = "SELECT * FROM ".$table_name." WHERE `classname`='".$classname."' ORDER BY `idx` ASC";//'".$_POST["username"]."'";
	
	$sql="select sdm_clsmembers.STU_ID,sdm_clsmembers.TTL_ID,sdm_students.Name";
	$sql.=" from `sdm_clsmembers` LEFT JOIN `sdm_students` ON sdm_clsmembers.STU_ID=sdm_students.STU_ID  ";
	$sql.=" WHERE (sdm_clsmembers.CLS_ID='".$classid."' AND sdm_clsmembers.Status='參與' AND (sdm_clsmembers.TTL_ID='TB05' OR sdm_clsmembers.TTL_ID='TB06'))";
	
	
	$result = mysql_query($sql);	
	$numrows = mysql_num_rows($result); 	
    
	// 685 
	$tablehtml_header ="<tr align=\"center\"><td width=\"30px\" v-align=\"center\" rowspan=\"2\">序</td><td width=\"90px\" rowspan=\"2\">姓名</td><td width=\"55px\" rowspan=\"2\">身份</td>";
	$tablehtml_header.="<td width=\"220px\" colspan=\"4\">南海分院場次</td>";
	$tablehtml_header.="<td width=\"110px\" colspan=\"2\">高雄場次</td>";	
	$tablehtml_header.="<td width=\"60px\" rowspan=\"2\">車資</td>";
	$tablehtml_header.="<td width=\"110px\" rowspan=\"2\">備註</td></tr>";
	
	$tablehtml_header.="<tr align=\"center\">";
	$tablehtml_header.="<td width=\"55px\">8/8(六)</td><td width=\"55px\">車次</td>";
	$tablehtml_header.="<td width=\"55px\">8/9(日)</td><td width=\"55px\">車次</td>";
	$tablehtml_header.="<td width=\"55px\">8/8(六)</td>";
	$tablehtml_header.="<td width=\"55px\">8/9(日)</td>";	
	$tablehtml_header.="</tr>";	
	
	//$tablehtml_example ="<tr align=\"center\"><td>例1</td><td>王福智</td><td>班員</td><td>V</td><td>AB</td><td>600</td><td></td><td></td><td></td></tr>";
	//$tablehtml_example .="<tr align=\"center\"><td>例2</td><td>趙善業</td><td>班員</td><td>V</td><td></td><td></td><td>V</td><td></td><td></td></tr>";
	
	$tablehtml1 = "";
	$tablehtml2 = "";
	$tablehtml3 = "";
	$tablehtml4 = "";
	$tablehtml5 = "";
    //------------------------------------------------------------------------------------------------------------------------------
    // Table title	
	if ($numrows > 0)
	{		
        //$tablehtml1 = "<img src=\"title.png\"  width=\"780\" height=\"127\"><br>";		
	    $tablehtml1 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";		
		$tablehtml1 .= $tablehtml_header;
		$tablehtml1 .= $tablehtml_example;		
			
		$idx = 0;
		$offset = 17;
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
		    $idx++;
			$basicitem = "<tr align=\"center\"><td>".$idx."</td><td>".$row["Name"]."</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
			if ($idx <= (27 + $offset))
			{
                $tablehtml1 .=$basicitem;
		    }
			else if ($idx <= (71 + $offset))
			{
			    if ($idx == (28 + $offset))
				{
					$tablehtml2 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
					$tablehtml2 .= $tablehtml_header;
                }
			    $tablehtml2 .=$basicitem;
		    }
			else if ($idx <= (115 + $offset))
			{
			    if ($idx == (72 + $offset))
				{
					$tablehtml3 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
					$tablehtml3 .= $tablehtml_header;
                }
                $tablehtml3 .=$basicitem;				
			}
			else if ($idx <= (159 + $offset))
            {
			    if ($idx == (116 + $offset))
				{
					$tablehtml4 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
					$tablehtml4 .= $tablehtml_header;
                }
				$tablehtml4 .=$basicitem;
			}
			else if ($idx <= (203 + $offset))
            {			    
			    if ($idx == (160 + $offset))
				{
					$tablehtml5 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
					$tablehtml5 .= $tablehtml_header;
                }	
				$tablehtml5 .=$basicitem;				
			}
		}
		
		$tablehtml1 .= "</table>";
		if ($tablehtml2 != "") {$tablehtml2 .= "</table>";}		
		if ($tablehtml3 != "") {$tablehtml3 .= "</table>";}
		if ($tablehtml4 != "") {$tablehtml4 .= "</table>";}	
		if ($tablehtml5 != "") {$tablehtml5 .= "</table>";}		
	}	
	// ---------------------------------------------------------
	$pdf->AddPage();// add a page
	$pdf->SetFont('droidsansfallback', '', 12);
	
	$pdf->writeHTML($tablehtml1.$tablehtml2.$tablehtml3.$tablehtml4.$tablehtml5, true, false, false, false, '');

	$filename = "student-list.pdf";//$classid.
	$pdf->Output($filename, 'D');
	
	//============================================================+
	// END OF FILE
	//============================================================+
?>