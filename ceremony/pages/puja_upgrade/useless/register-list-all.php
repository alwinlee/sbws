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
	
	set_time_limit(300); // 5分鐘
	session_start();	
	
	$table_name = $_POST["tbname"];
    $classname = $_POST["classarea"];
	
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Noman');
	$pdf->SetTitle('2015年第二期淨智營(第46、47、48梯)報名表');
	$pdf->SetSubject('');

    //------------------------------------------------------------------------------------------------------------------------------
	// 685 px
	$tablehtml_header ="<tr align=\"center\"><td width=\"30px\" v-align=\"center\" rowspan=\"2\">序</td><td width=\"70px\" rowspan=\"2\">姓名</td><td width=\"55px\" rowspan=\"2\">身份</td>";
	$tablehtml_header.="<td width=\"210px\" colspan=\"6\">參加梯次</td>";
	$tablehtml_header.="<td width=\"40px\" rowspan=\"2\">車次</td><td width=\"40px\" rowspan=\"2\">車資</td>";
	$tablehtml_header.="<td width=\"170px\" colspan=\"4\">特殊住宿需求安排</td><td width=\"80px\" rowspan=\"2\">備註</td></tr>";
	
	$tablehtml_header.="<tr align=\"center\">";
	$tablehtml_header.="<td width=\"35px\">46梯</td><td width=\"35px\">47梯</td><td width=\"35px\">48梯</td>";
	$tablehtml_header.="<td width=\"35px\">正行</td><td width=\"35px\">幹部</td><td width=\"35px\">重培</td>";	
	$tablehtml_header.="<td width=\"40px\">行動不便</td><td width=\"50px\">氣喘/心臟</td><td width=\"40px\">嚴重打鼾</td><td width=\"40px\">其他症狀</td>></tr>";

	$classtable_name = "classroom2015";
	$areaid = $_POST["classarea"];
	$sql_class = "SELECT * FROM ".$classtable_name." WHERE `areaid`='".$areaid."'";//'".$_POST["username"]."'";
	$result_class  = mysql_query($sql_class);	
	$numrows_class = mysql_num_rows($result_class); 	
	
	while($row_class = mysql_fetch_array($result_class, MYSQL_ASSOC))//for($i = 0; $i < 2; $i++)
	{
		$classname = $row_class["name"];		
		$table_title = "2015年第二期淨智營(第46、47、48梯)報名表  (".$classname."班)";
		// set default header data
		//$tablename = "";//"報名名冊";	    
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $table_title, $tablename);
		// set header and footer fonts
		$pdf->setHeaderFont(Array('droidsansfallback', 'center', 16));
		$pdf->setFooterFont(Array('droidsansfallback', 'right', 8));

		//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		//$pdf->setPrintHeader(false);
		//set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(8, 8, 8);//$pdf->SetMargins(8, 24, 8);//(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(2);//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set font
		$pdf->SetFont('droidsansfallback','', 12);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		//------------------------------------------------------------------------------------------------------------------------------
		// get data from database 
		$sql = "SELECT * FROM ".$table_name." WHERE `classname`='".$classname."' ORDER BY `idx` ASC";//'".$_POST["username"]."'";
		$result = mysql_query($sql);	
		$numrows = mysql_num_rows($result); 	
		if ($numrows<=0){continue;}
		$tablehtml1 = "";
		$tablehtml2 = "";
		$tablehtml3 = "";
		$tablehtml4 = "";
		$tablehtml5 = "";
		
		//------------------------------------------------------------------------------------------------------------------------------
		// Table title
		$tablehtml1  = "<table border=\"0\"><tr><td align=\"center\" style=\"font-size:18px\">".$table_title."</td></tr></table><br><br>";		
		//$tablehtml1 .= "<img src=\"title.png\"  width=\"780\" height=\"320\"><br>";		
		$tablehtml1 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";		
		$tablehtml1 .= $tablehtml_header;
		$tablehtml1 .= $tablehtml_example;		
			
		$idx=0;
		$offset=18;
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
		$idx++;
		$basicitem = "<tr align=\"center\"><td>".$idx."</td><td>".$row["name"]."</td><td>".$row["title"]."</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
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
		// ---------------------------------------------------------
		$pdf->AddPage();// add a page
		$pdf->SetFont('droidsansfallback', '', 12);		
		$pdf->writeHTML($tablehtml1.$tablehtml2.$tablehtml3.$tablehtml4.$tablehtml5, true, false, false, false, '');
	}
	
	$filename = $areaid.".pdf";//$classid.
	$pdf->Output($filename, 'D');	
	//============================================================+
	// END OF FILE
	//============================================================+
?>