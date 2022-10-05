<?php
    header("Content-Type: text/html; charset=utf-8");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-download");
    header("Content-Type: application/download");

    ini_set('memory_limit', -1 );
    ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close

    require_once("../../../_res/_inc/connMysql.php");
    require_once("../../../_res/_inc/sharelib.php");
    require_once('../../../_res/tcpdf/tcpdf.php');

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
    $table_title        = $pujatitle." 車資繳費單 (".$classname."班)";

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Noman');
    $pdf->SetTitle($table_title);
    $pdf->SetSubject($classname);

    $tablename = "";//"報名名冊";
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $tablename, $table_title);

    // set header and footer fonts
    $pdf->setHeaderFont(Array('droidsansfallback', 'center', 16));
    $pdf->setFooterFont(Array('droidsansfallback', 'right', 8));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(8, 6, 8);//(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // set font
    $pdf->SetFont('droidsansfallback','', 12);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $invoice_out = "<table><tr><td style=\"width:550px;\">".$table_title."</td><td style=\"width:100px;\" align=\"right\">收執聯</td></tr></table>";
    $invoice_in =  "<table><tr><td style=\"width:550px;\">".$table_title."</td><td style=\"width:100px;\" align=\"right\">存根聯</td></tr></table>";

    $sign = "<table><tr><td align=\"right\">承辦人：</td><td align=\"left\">_________________________</td><td align=\"right\">收費日期：_________________________</td></tr></table>";

    //------------------------------------------------------------------------------------------------------------------------------
    // 法會車次表 第一天與第二天
    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
    $sql = "SELECT * FROM ".$traffic_table_name." WHERE `day`=0 ORDER BY `idx` ASC";
    $pujatraffic_result = mysqli_query($con, $sql);
    $i = 0;
    while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
    {
        $traffinfo1[$i][0] = $traffic_row["traffid"];
        $traffinfo1[$i][1] = $traffic_row["traffname"];
        $traffinfo1[$i][2] = 0;
        $traffinfo1[$i][3] = "";
        $traffinfo1[$i][4] = 0;
        $i++;
    }

    $sql = "SELECT * FROM ".$traffic_table_name." WHERE `day`=1 ORDER BY `idx` ASC";
    $pujatraffic_result = mysqli_query($con, $sql);
    $i = 0;
    while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
    {
        $traffinfo2[$i][0] = $traffic_row["traffid"];
        $traffinfo2[$i][1] = $traffic_row["traffname"];
        $traffinfo2[$i][2] = 0;
        $traffinfo2[$i][3] = "";
        $traffinfo2[$i][4] = 0;
        $i++;
    }

    //------------------------------------------------------------------------------------------------------------------------------
    // get data from database

    // puja configure
    $sql         = "SELECT * FROM pujaconfig WHERE `php`='puja_eight.php'"; // 取得法會資料設定值
    $puja_result = mysqli_query($con, $sql);
    $numrows     = mysqli_num_rows($puja_result);

    $traffRoundCost   = 200; //當天
    $traffGoCost      = 200;
    $traffBackCost    = 200;
    $traff2RoundCost  = 400;

    if ($numrows > 0)
    {
        $puja_row = mysqli_fetch_array($puja_result, MYSQLI_ASSOC);
        $traffRoundCost  = $puja_row["traffroundcost"]; // 當天
        $traffGoCost     = $puja_row["traffgocost"];
        $traffBackCost   = $puja_row["traffbackcost"];
        $traff2RoundCost = $traffGoCost + $traffBackCost; // 隔天
    }

    $trafftablehtml = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
    $trafftablehtml .= "<tr><td style=\"width:155px\"> 地點</td><td style=\"width:55px\"> 代號</td><td style=\"width:375px\">搭車學員</td><td style=\"width:100px\"> 小計</td></tr>";

    $sql     = "SELECT * FROM ".$table_name." WHERE `CLS_ID`='".$classid."' ORDER BY `idx` ASC";//'".$_POST["username"]."'";
    $result  = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($result);
    //------------------------------------------------------------------------------------------------------------------------------
    // Table title, 計算人數
    $data = 0;
    $iSize1 = count($traffinfo1);
    $iSize2 = count($traffinfo2);

    $iPaySum=0;
    if ($numrows > 0)
    {
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            //if ($row["title"] == "班長" || $row["title"] == "副班長" || $row["title"] == "關懷員")
            //	continue;

            if ($row["day"] <= 0){continue;} // 沒報名或不參加

            $trafflist=explode(",",$row["traff"]);
            $traffCntlist=explode(",",$row["traffCnt"]);
            $iPaySum+=$row["cost"];

            if (($row["day"]%10)>=1&&count($trafflist)>=1)//第一天
            {
				$joinday=$item3."梯";
				if($row["day"]==1){$joinday=$item1."梯";}
				else if($row["day"]==2){$joinday=$item2."梯";}

                for($j = 0; $j < $iSize1; $j++)
                {
                    if ($trafflist[0]=="Z"||$trafflist[0]== "ZZ"||$trafflist[0]==""){break;}
                    if ($traffinfo1[$j][0] == $trafflist[0])
                    {
                        $traffinfo1[$j][2] += 1;
                        $traffinfo1[$j][2] += 0;//$row["family"];
                        $traffinfo1[$j][3] .= $row["name"]."(".$joinday.")";//."(NT$".$row["cost"].")";
                        if ($traffCntlist[0]==1){$traffinfo1[$j][3].="(去)";}else if ($traffCntlist[0]==2){$traffinfo1[$j][3].="(回)";}
                        $traffinfo1[$j][3] .= ",";
                        $traffinfo1[$j][4] += $row["cost"];
                        break;
                    }
                }
            }

            if ($row["day"]>=10&&count($trafflist)>=2)//第二天
            {
                for($j = 0; $j < $iSize2; $j++)
                {
                    if ($trafflist[1]=="Z"||$trafflist[1]== "ZZ"||$trafflist[1]==""){break;}
                    if ($traffinfo2[$j][0] == $trafflist[1])
                    {
                        $traffinfo2[$j][2] += 1;
                        $traffinfo2[$j][2] += 0;//$row["family"];
                        $traffinfo2[$j][3] .= $row["name"];//."(NT$".$row["cost"].")";
                        if ($traffCntlist[1]==1){$traffinfo2[$j][3].="(去)";}else if ($traffCntlist[1]==2){$traffinfo2[$j][3].="(回)";}
                        $traffinfo2[$j][3] .= ",";
                        $traffinfo2[$j][4] += $row["cost"];
                        break;
                    }
                }
            }
        }
    }
    $iSumDay1 = 0;
    $day1student = "";
    for($j = 0; $j < $iSize1; $j++)
    {
        $iDay1 = $traffinfo1[$j][2];
        $day1student = $traffinfo1[$j][3];

        $iSum = $iDay1;
        if ($iSum <= 0)
            continue;

        $trafftablehtml .= "<tr><td> ".$traffinfo1[$j][1]."</td><td> ".$traffinfo1[$j][0]."</td>";
        $trafftablehtml .= "<td>".$day1student."</td>";
       // $trafftablehtml .= "<td align=\"right\"> ".($iDay1 > 0 ? $iDay1 : "-")."  </td>";
        $trafftablehtml .= "<td align=\"right\"> ".$iSum." </td></tr>";
        //$trafftablehtml .= "<td align=\"right\"> ".($iDay2 > 0 ? $iDay2 : "-")."  </td>";
        $iSumDay1 += $iDay1;
    }

    $iSumDay2 = 0;
    $day2student = "";
    for($j = 0; $j < $iSize2; $j++)
    {
        $iDay2=$traffinfo2[$j][2];
        $day2student=$traffinfo2[$j][3];
        $iSum=$iDay2;
        if ($iSum<=0){continue;}

        $trafftablehtml .= "<tr><td> ".$traffinfo2[$j][1]."</td><td> ".$traffinfo2[$j][0]."</td>";
        $trafftablehtml .= "<td>".$day2student."</td>";
        // $trafftablehtml .= "<td align=\"right\"> ".($iDay1 > 0 ? $iDay1 : "-")."  </td>";
        $trafftablehtml .= "<td align=\"right\"> ".$iSum." </td></tr>";
        //$trafftablehtml .= "<td align=\"right\"> ".($iDay2 > 0 ? $iDay2 : "-")."  </td>";
        $iSumDay2 += $iDay2;
    }
    $trafftablehtml .= "<tr><td colspan=\"3\" align=\"right\">車資  </td><td align=\"right\"> NT$".number_format($iPaySum)." </td></tr>";
    $trafftablehtml .= "</table>";
    $trafftablehtml .= "</table>";
    // ---------------------------------------------------------
    $pdf->AddPage();// add a page
    $pdf->SetFont('droidsansfallback', '', 12);
    //$pdf->writeHTML($sql, true, false, false, false, '');
    $pdf->writeHTML($invoice_out, true, false, false, false, '');
    $pdf->writeHTML($trafftablehtml, true, false, false, false, '');
    $pdf->writeHTML($sign, true, false, false, false, '');

    $pdf->writeHTML("----------------------------------------------------------------------------------------------------------------------------------------------", true, false, false, false, '');

    $pdf->writeHTML($invoice_in, true, false, false, false, '');
    $pdf->writeHTML($trafftablehtml, true, false, false, false, '');
    $pdf->writeHTML($sign, true, false, false, false, '');

    $filename = "receipt-list.pdf";//$classid.
    $pdf->Output($filename, 'D');

	//============================================================+
	// END OF FILE
	//============================================================+
?>
