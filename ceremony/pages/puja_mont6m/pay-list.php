<?php
    header("Content-Type: text/html; charset=utf-8");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-download");
    header("Content-Type: application/download");

    ini_set('memory_limit',-1);
    ini_set("error_reporting",0);
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

    $day1title=$_POST["day1title"];
    $day2title=$_POST["day2title"];
    $day3title=$_POST["day3title"];
    $day4title=$_POST["day4title"];
    $day5title=$_POST["day5title"];
    $day6title=$_POST["day6title"];

    $table_name        =$_POST["tbname"];
    $traffic_table_name=$_POST["trafftb"];
    $classname         =$_POST["classname"];
    $classid           =$_POST["classid"];
    $pujatitle         =$_POST["pujatitle"];
    $year              =GetNewYearPUJAYear();	// 跟朝禮法會相當時間
    $curYear           =GetCurrentYear();
    $table_title       =$pujatitle." 已繳費名冊 (".$classname."班)";

    //echo $classid;
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Noman');
    $pdf->SetTitle('法會報名繳費名冊');
    $pdf->SetSubject('');

    // set default header data
    $tablename = "";//"報名名冊";
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,$table_title,$tablename);

    // set header and footer fonts
    $pdf->setHeaderFont(Array('droidsansfallback','center',16));
    $pdf->setFooterFont(Array('droidsansfallback','right',8));

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
    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
    $sql="SELECT * FROM ".$table_name." WHERE `classname`='".$classname."' AND (`day1`>0 OR `day2`>0 OR `day3`>0 OR `day4`>0 OR `day5`>0 OR `day6`>0) ORDER BY `TTL_ID` ASC, `memberseq` ASC";//'".$_POST["username"]."'";
    $result=mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($result);

    $tablehtml_header="<tr align=\"center\"><td width=\"35px\" rowspan=\"2\">序</td><td width=\"80px\" rowspan=\"2\">姓名</td>";
    $tablehtml_header.="<td width=\"480px\" colspan=\"3\">搭車車次</td>";
    $tablehtml_header.="<td width=\"80px\" rowspan=\"2\">車資</td></tr>";
    $tablehtml_header.="<tr align=\"center\"><td width=\"160px\">".$day1title."</td><td width=\"160px\">".$day2title."</td><td width=\"160px\">".$day3title."</td>";
    //$tablehtml_header.="<td width=\"80px\">".$day4title."</td><td width=\"80px\">".$day5title."</td><td width=\"80px\">".$day6title."</td>";
    $tablehtml_header.="</tr>";

    $tablehtml1="";
    $tablehtml2="";
    $tablehtml3="";
    $tablehtml4="";
    $tablehtml5="";
    //------------------------------------------------------------------------------------------------------------------------------
    // Table title
    if ($numrows > 0)
    {
        $tablehtml1.="<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
        $tablehtml1.=$tablehtml_header;

        $idx=0;
        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $idx++;
            if($row["day1"]<=0&&$row["day2"]<=0&&$row["day3"]<=0&&$row["day4"]<=0&&$row["day5"]<=0&&$row["day6"]<=0){continue;}
            $trafflist1=explode(",",$row["traff1"]);
            $trafflist2=explode(",",$row["traff2"]);
            $trafflist3=explode(",",$row["traff3"]);
            $trafflist4=explode(",",$row["traff4"]);
            $trafflist5=explode(",",$row["traff5"]);
            $trafflist6=explode(",",$row["traff6"]);

            //if(count($trafflist1)<3&&count($trafflist2)<3){continue;}
            if (($trafflist1[2]=="Z"||$trafflist1[2]== "ZZ"||$trafflist1[2]=="")&&($trafflist2[2]=="Z"||$trafflist2[2]== "ZZ"||$trafflist2[2]=="")&&
                ($trafflist3[2]=="Z"||$trafflist3[2]== "ZZ"||$trafflist3[2]=="")&&($trafflist4[2]=="Z"||$trafflist4[2]== "ZZ"||$trafflist4[2]=="")&&
                ($trafflist4[2]=="Z"||$trafflist5[2]== "ZZ"||$trafflist5[2]=="")&&($trafflist6[2]=="Z"||$trafflist6[2]== "ZZ"||$trafflist6[2]==""))
                {continue;}

            $day1="";$day2="";$day3="";$day4="";$day5="";$day6="";
            if ($row["day1"]>=1){$day1=$trafflist1[2];if($row["family1"]>=1){$day1.="(眷屬x".$row["family1"].")";}}
            if ($row["day2"]>=1){$day2=$trafflist2[2];if($row["family2"]>=1){$day2.="(眷屬x".$row["family2"].")";}}
            if ($row["day3"]>=1){$day3=$trafflist3[2];if($row["family3"]>=1){$day3.="(眷屬x".$row["family3"].")";}}
            if ($row["day4"]>=1){$day4=$trafflist4[2];if($row["family4"]>=1){$day4.="(眷屬x".$row["family4"].")";}}
            if ($row["day5"]>=1){$day5=$trafflist5[2];if($row["family5"]>=1){$day5.="(眷屬x".$row["family5"].")";}}
            if ($row["day6"]>=1){$day6=$trafflist6[2];if($row["family6"]>=1){$day6.="(眷屬x".$row["family6"].")";}}

            $rowitem="<tr align=\"center\"><td>".$idx."</td><td>".$row["name"]."</td>";
            $rowitem.="<td>".$day1."</td><td>".$day2."</td><td>".$day3."</td>";
            //$rowitem.="<td>".$day4."</td><td>".$day5."</td><td>".$day6."</td>";
            $rowitem.="<td>".($row["pay1"]+$row["pay2"]+$row["pay3"]+$row["pay4"]+$row["pay5"]+$row["pay6"])."</td></tr>";

            if ($idx <= 43)
            {
                $tablehtml1 .= $rowitem;
            }else if ($idx <= 86){
                if ($idx == 44)
                {
                    $tablehtml2.="<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
                    $tablehtml2.=$tablehtml_header;
                    $tablehtml2.=$rowitem;
                }
                else
                {
                    $tablehtml2 .= $rowitem;
                }
            }else if ($idx <= 129){
                if ($idx == 87)
                {
                    $tablehtml3.="<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
                    $tablehtml3.=$tablehtml_header;
                    $tablehtml3.=$rowitem;
                }
                else
                {
                    $tablehtml3.=$rowitem;
                }
            }else if ($idx <= 172){
                if ($idx == 130)
                {
                    $tablehtml4.="<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
                    $tablehtml4.=$tablehtml_header;
                    $tablehtml4.=$rowitem;
                }
                else
                {
                    $tablehtml4 .= $rowitem;
                }
            }else if ($idx <= 215){
                if ($idx == 173)
                {
                    $tablehtml5.="<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
                    $tablehtml5.=$tablehtml_header;
                    $tablehtml5.=$rowitem;
               }
                else
                {
                    $tablehtml5 .= $rowitem;
                }
            }
        }

        $tablehtml1.="</table>";
        if ($tablehtml2!="") {$tablehtml2.="</table>";}
        if ($tablehtml3!="") {$tablehtml3.="</table>";}
        if ($tablehtml4!="") {$tablehtml4.="</table>";}
        if ($tablehtml5!="") {$tablehtml5.="</table>";}
    }
    // ---------------------------------------------------------
    $pdf->AddPage();// add a page
    $pdf->SetFont('droidsansfallback', '', 12);
    $pdf->writeHTML($tablehtml1.$tablehtml2.$tablehtml3.$tablehtml4.$tablehtml5, true, false, false, false, '');
    $filename="paylist.pdf";//$classid.
    $pdf->Output($filename, 'D');

    //============================================================+
    // END OF FILE
    //============================================================+
?>
