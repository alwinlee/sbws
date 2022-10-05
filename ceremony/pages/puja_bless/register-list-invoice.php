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

    $table_name         =$_POST["tbname"];
    $traffic_table_name =$_POST["trafftb"];
    $classname          =$_POST["classname"];
    $classid            =$_POST["classid"];
    $pujatitle          =$_POST["pujatitle"];
    $day1title          =$_POST["day1title"];
    $day2title          =$_POST["day2title"];
    $day3title          =$_POST["day3title"];
    $day4title          =$_POST["day4title"];
    $year               =GetNewYearPUJAYear();	// 跟朝禮法會相當時間
    $curYear            =GetCurrentYear();
    $traffunitcost      =290;
    $table_title        =$pujatitle." 車資繳費單 (".$classname."班)";

    $pdf=new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Noman');
    $pdf->SetTitle($table_title);
    $pdf->SetSubject($classname);

    $tablename="";//"報名名冊";
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

    $invoice_out="<table><tr><td style=\"width:550px;\">".$table_title."</td><td style=\"width:100px;\" align=\"right\">收執聯</td></tr></table>";
    $invoice_in="<table><tr><td style=\"width:550px;\">".$table_title."</td><td style=\"width:100px;\" align=\"right\">存根聯</td></tr></table>";

    $sign = "<table><tr><td align=\"right\">承辦人：</td><td align=\"left\">_________________________</td><td align=\"right\">收費日期：_________________________</td></tr></table>";

    //------------------------------------------------------------------------------------------------------------------------------
    // 法會車次表 第一天與第二天
    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=0 ORDER BY `idx` ASC";
    $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
    $pujatraffic_result = mysqli_query($con, $sql);
    $i=0;
    while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
    {
        $traffinfo1[$i][0]=$traffic_row["traffid"];
        $traffinfo1[$i][1]=$traffic_row["traffname"]."[".$day1title."]";
        $traffinfo1[$i][2]=0;
        $traffinfo1[$i][3]="";
        $traffinfo1[$i][4]=0;
        $i++;
    }

    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=1 ORDER BY `idx` ASC";
    $pujatraffic_result = mysqli_query($con, $sql);
    $i=0;
    while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
    {
        $traffinfo2[$i][0]=$traffic_row["traffid"];
        $traffinfo2[$i][1]=$traffic_row["traffname"]."[".$day2title."]";
        $traffinfo2[$i][2]=0;
        $traffinfo2[$i][3]="";
        $traffinfo2[$i][4]=0;
        $i++;
    }

    $sql="SELECT * FROM ".$traffic_table_name." WHERE `day`=2 ORDER BY `idx` ASC";
    $pujatraffic_result = mysqli_query($con, $sql);
    $i=0;
    while($traffic_row = mysqli_fetch_array($pujatraffic_result, MYSQLI_ASSOC))
    {
        $traffinfo3[$i][0]=$traffic_row["traffid"];
        $traffinfo3[$i][1]=$traffic_row["traffname"]."[".$day3title."]";
        $traffinfo3[$i][2]=0;
        $traffinfo3[$i][3]="";
        $traffinfo3[$i][4]=0;
        $i++;
    }
    //------------------------------------------------------------------------------------------------------------------------------
    // get data from database

    // puja configure
    $sql        ="SELECT * FROM pujaconfig WHERE `php`='puja_invoke.php'"; // 取得法會資料設定值
    $puja_result= mysqli_query($con, $sql);
    $numrows    = mysqli_num_rows($puja_result);

    $traffRoundCost   = 300; //當天
    $traffGoCost      = 300;
    $traffBackCost    = 300;
    $traff2RoundCost  = 300;
    if ($numrows > 0) {
        $puja_row = mysqli_fetch_array($puja_result, MYSQLI_ASSOC);
        $traffRoundCost =$puja_row["traffroundcost"]; // 當天
        $traffGoCost    =$puja_row["traffgocost"];
        $traffBackCost  =$puja_row["traffbackcost"];
        $traff2RoundCost=$traffGoCost + $traffBackCost; // 隔天
    }
    $traffunitcost=$traffRoundCost;

    $trafftablehtml = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" nobr=\"true\">";
    $trafftablehtml .= "<tr><td style=\"width:230px\"> 地點</td><td style=\"width:45px\"> 代號</td><td style=\"width:340px\">搭車學員</td><td style=\"width:70px\"> 小計</td></tr>";

    $sql     = "SELECT * FROM ".$table_name." WHERE `CLS_ID`='".$classid."' AND (`day1`>0 OR `day2`>0 OR `day3`>0) ORDER BY `TTL_ID` ASC, `memberseq` ASC";//'".$_POST["username"]."'";
    $result  = mysqli_query($con, $sql);
    $numrows = mysqli_num_rows($result);
    //------------------------------------------------------------------------------------------------------------------------------
    // Table title, 計算人數
    $data = 0;
    $iSize1=count($traffinfo1);
    $iSize2=count($traffinfo2);
    $iSize3=count($traffinfo3);

    $iPaySum=0;
    if ($numrows>0)
    {
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            //if ($row["title"] == "班長" || $row["title"] == "副班長" || $row["title"] == "關懷員")
            //	continue;

            if ($row["day1"]<=0&&$row["day2"]<=0&&$row["day3"]<=0){continue;} // 沒報名或不參加
            if ($row["cost1"]<=0&&$row["cost2"]<=0&&$row["cost3"]<=0){continue;}

            $trafflist1=explode(",",$row["traff1"]);//if (count($trafflist1)<=0){continue;}
            $trafflist2=explode(",",$row["traff2"]);//if (count($trafflist1)<=0){continue;}
            $trafflist3=explode(",",$row["traff3"]);
            $iPaySum+=$row["cost1"];
            $iPaySum+=$row["cost2"];
            $iPaySum+=$row["cost3"];

            if ($row["day1"]>0)//第一場
            {
                for($j=0;$j<$iSize1;$j++)
                {
                    if ($trafflist1[0]=="Z"||$trafflist1[0]== "ZZ"||$trafflist1[0]==""){break;}
                    if ($traffinfo1[$j][0]==$trafflist1[0])
                    {
                        $traffinfo1[$j][2]+=1;
                        $traffinfo1[$j][2]+=$row["family1"];
                        $traffinfo1[$j][3].=$row["name"];//."(NT$".$row["cost"].")";
                        if($row["family1"]>0){$traffinfo1[$j][3].="(眷屬x".$row["family1"].")";}
                        $traffinfo1[$j][3].=",";
                        $traffinfo1[$j][4]+=$row["cost1"];
                        break;
                    }
                }
            }
            if ($row["day2"]>0)//第2場
            {
                for($j=0;$j<$iSize2;$j++)
                {
                    if ($trafflist2[0]=="Z"||$trafflist2[0]== "ZZ"||$trafflist2[0]==""){break;}
                    if ($traffinfo2[$j][0]==$trafflist2[0])
                    {
                        $traffinfo2[$j][2]+=1;
                        $traffinfo2[$j][2]+=$row["family2"];
                        $traffinfo2[$j][3].=$row["name"];//."(NT$".$row["cost"].")";
                        if($row["family2"]>0){$traffinfo2[$j][3].="(眷屬x".$row["family2"].")";}
                        $traffinfo2[$j][3].=",";
                        $traffinfo2[$j][4]+=$row["cost2"];
                        break;
                    }
                }
            }
            if ($row["day3"]>0)//第3場
            {
                for($j=0;$j<$iSize3;$j++)
                {
                    if ($trafflist3[0]=="Z"||$trafflist3[0]== "ZZ"||$trafflist3[0]==""){break;}
                    if ($traffinfo3[$j][0]==$trafflist3[0])
                    {
                        $traffinfo3[$j][2]+=1;
                        $traffinfo3[$j][2]+=$row["family3"];
                        $traffinfo3[$j][3].=$row["name"];//."(NT$".$row["cost"].")";
                        if($row["family3"]>0){$traffinfo3[$j][3].="(眷屬x".$row["family3"].")";}
                        $traffinfo3[$j][3].=",";
                        $traffinfo3[$j][4]+=$row["cost3"];
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
        $trafftablehtml .= "<td align=\"right\"> ".$iSum." x ".$traffunitcost."</td></tr>";
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
        $trafftablehtml .= "<td align=\"right\"> ".$iSum." x ".$traffunitcost."</td></tr>";
        //$trafftablehtml .= "<td align=\"right\"> ".($iDay2 > 0 ? $iDay2 : "-")."  </td>";
        $iSumDay2 += $iDay2;
    }

    $iSumDay3 = 0;
    $day3student = "";
    for($j = 0; $j < $iSize3; $j++)
    {
        $iDay3=$traffinfo3[$j][2];
        $day3student=$traffinfo3[$j][3];
        $iSum=$iDay3;
        if ($iSum<=0){continue;}

        $trafftablehtml .= "<tr><td> ".$traffinfo3[$j][1]."</td><td> ".$traffinfo3[$j][0]."</td>";
        $trafftablehtml .= "<td>".$day3student."</td>";
        // $trafftablehtml .= "<td align=\"right\"> ".($iDay1 > 0 ? $iDay1 : "-")."  </td>";
        $trafftablehtml .= "<td align=\"right\"> ".$iSum." x ".$traffunitcost."</td></tr>";
        //$trafftablehtml .= "<td align=\"right\"> ".($iDay2 > 0 ? $iDay2 : "-")."  </td>";
        $iSumDay3 += $iDay3;
    }

    $trafftablehtml .= "<tr><td colspan=\"4\" align=\"right\">車資 ： NT$".number_format($iPaySum)." &nbsp;</td></tr>";
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
