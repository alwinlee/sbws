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

    $table_name         = $_POST["tbname"];
    $traffic_table_name = $_POST["trafftb"];
    $classname          = $_POST["classname"];
    $classid            = $_POST["classid"];
    $pujatitle          = $_POST["pujatitle"];
    $item1              = $_POST["item1"];
    $item2              = $_POST["item2"];
    $item3              = $_POST["item3"];
    $year               = GetNewYearPUJAYear();	// 跟朝禮法會相當時間
    $curYear            = GetCurrentYear();
    $table_title        = $pujatitle." 已繳費名冊 (".$classname."班)";

    //echo $classid;
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Noman');
    $pdf->SetTitle('2015年朝禮法會報名表');
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
    $pdf->SetFont('droidsansfallback','', 12);


    //------------------------------------------------------------------------------------------------------------------------------
    // get data from database
    $con = mysqli_connect("localhost", "root", "rinpoche", "bwsouthdb");
    $sql = "SELECT * FROM ".$table_name." WHERE `classname`='".$classname."' AND `pay`>0 ORDER BY `idx` ASC";//'".$_POST["username"]."'";
    $result = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($result);

    $tablehtml_header ="<tr align=\"center\"><td width=\"35px\">序</td><td width=\"200px\">姓名</td>";
    $tablehtml_header.="<td width=\"140px\">參加梯次</td>";
    $tablehtml_header.="<td width=\"140px\">搭車車次</td>";
    $tablehtml_header.="<td width=\"160px\">車資</td></tr>";
    //2 days
    //$tablehtml_header ="<tr align=\"center\"><td width=\"35px\" rowspan=\"2\">序</td><td width=\"200px\" rowspan=\"2\">姓名</td>";
    //$tablehtml_header.="<td width=\"280px\" colspan=\"2\">搭車車次</td>";
    //$tablehtml_header.="<td width=\"160px\" rowspan=\"2\">車資</td></tr>";
    //$tablehtml_header.="<tr align=\"center\"><td width=\"140px\">8/7(五)</td><td width=\"140px\">8/8(六)</td></tr>";

    $tablehtml1 = "";
    $tablehtml2 = "";
    $tablehtml3 = "";
    $tablehtml4 = "";
    $tablehtml5 = "";
    //------------------------------------------------------------------------------------------------------------------------------
    // Table title
    if ($numrows > 0)
    {
        $tablehtml1 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
        $tablehtml1 .= $tablehtml_header;
        //$tablehtml1 .= $tablehtml_example;

        $idx = 0;
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $idx++;
            $trafflist=explode(",",$row["traffReal"]);
            $day1="";$day2="";
            $joinday="";
            if($row["day"]<= 0){continue;}
            if($row["pay"]<= 0){continue;}
            if ($trafflist[0]!="Z"&&$trafflist[0]!= "ZZ"&&$trafflist[0]!=""){$day1=$trafflist[0];}
            if ($trafflist[1]!="Z"&&$trafflist[1]!= "ZZ"&&$trafflist[1]!=""){$day2=$trafflist[1];}
            //if (($row["day"]%10)==1&&count($trafflist)>=1)//第一天
            //if (($row["day"]%100)>=10&&count($trafflist)>=2)//第二天

			$joinday=$item3;
			if($row["day"]==1){$joinday=$item1;}
			else if($row["day"]==2){$joinday=$item2;}

            $rowitem="<tr align=\"center\"><td>".$idx."</td><td>".$row["name"]."</td>";
			$rowitem.="<td>".$joinday."</td>";
            $rowitem.="<td>".$day1."</td><td>".$row["pay"]."</td></tr>";//$rowitem.="<td>".$day1."</td><td>".$day2."</td><td>".$row["pay"]."</td></tr>";

            if ($idx <= 43)
            {
                $tablehtml1 .= $rowitem;
            }else if ($idx <= 86){
                if ($idx == 44)
                {
                    $tablehtml2 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
                    $tablehtml2 .= $tablehtml_header;
                    $tablehtml2 .= $rowitem;
                }else{
                    $tablehtml2 .= $rowitem;
                }
            }else if ($idx <= 129){
                if ($idx == 87)
                {
                    $tablehtml3 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
                    $tablehtml3 .= $tablehtml_header;
                    $tablehtml3 .= $rowitem;
                }else{
                    $tablehtml3 .= $rowitem;
                }
            }else if ($idx <= 172){
                if ($idx == 130)
                {
                    $tablehtml4 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
                    $tablehtml4 .= $tablehtml_header;
                    $tablehtml4 .= $rowitem;
                }
                else
                {
                    $tablehtml4 .= $rowitem;
                }
            }else if ($idx <= 215){
                if ($idx == 173)
                {
                    $tablehtml5 .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
                    $tablehtml5 .= $tablehtml_header;
                    $tablehtml5 .= $rowitem;
                }else{
                    $tablehtml5 .= $rowitem;
                }
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

    $filename = "paylist.pdf";//$classid.
    $pdf->Output($filename, 'D');

    //============================================================+
    // END OF FILE
    //============================================================+
?>
