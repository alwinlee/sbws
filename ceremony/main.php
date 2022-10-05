<?php 
	header("Content-Type: text/html; charset=utf-8");
	session_start();
	require_once("../_res/_inc/login_check.php");
	require_once("../_res/_inc/connMysql.php"); 
	require_once("../_res/_inc/sharelib.php");
?>

<!DOCTYPE HTML>
<HTML lang="en"><HEAD><META content="IE=11.0000" http-equiv="X-UA-Compatible">
<TITLE>南區學員點名系統-法會報名</TITLE>     

<META name="keywords" content=""> 
<META name="description" content=""> 
<META http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">     
<META name="viewport" content="width=device-width, initial-scale=1">      

<link rel="shortcut icon" href="../_res/img/icons.ico">
<LINK href="../_res/css/site.css" rel="stylesheet" media="screen">   
<LINK href="../_res/css/stdtheme.css" rel="stylesheet" media="screen">   
<LINK href="../_res/css/bwsouth.css" rel="stylesheet" media="screen"> 

<SCRIPT src="../_res/js/analytics.js" type="text/javascript"></SCRIPT>
<SCRIPT src="../_res/js/format.js" type="text/javascript"></SCRIPT>     
<SCRIPT src="../_res/js/jquery-2.1.1.min.js" type="text/javascript"></SCRIPT> 
<SCRIPT src="../_res/js/js.js" type="text/javascript"></SCRIPT>
<SCRIPT src="../_res/js/toggle.js" type="text/javascript"></SCRIPT>

<SCRIPT src="./js/main.js?{F017A570-2E23-4359-BF3C-E9E58038C23E}" type="text/javascript"></SCRIPT>

<SCRIPT type="text/javascript">		
</SCRIPT>  

<META name="GENERATOR" content=""></HEAD> 
<BODY>
<DIV class="top" id="pageTop"></DIV>
<DIV class="rc-all contentdiv">
<TABLE>

<TR valign="top">
<TD align="left">
<TABLE align="left" class="contenttable" cellspacing="0" cellpadding="0">
<TR valign="top">
<TD>
		  
<TABLE style="border-collapse: collapse;" cellspacing="0" cellpadding="0">
<TR>
<TD align="left" class="navigation" id="navigationTree" valign="top"><?php $subMenuItem=0; include("./inc/menu.php");?></TD>

<TD class="content" id="demos" valign="top">
<TABLE style="vertical-align: top; border-collapse: collapse;" cellspacing="0" cellpadding="0">
<TR>
<TD valign="top">
<DIV class="demoContainer" id="demoContainer">
<br>
<?php
    $curr=date("Y-m-d");
    $i=0;
    $user_level = $_SESSION["userlevel"];
    $con = connect_db($dbname);
    if ($con) {
        $sql="select * from `pujaconfig` ";
        $puja_result=mysqli_query($con, $sql);
        echo "<table style=\"padding-bottom: 1em\">";
        while($row = mysqli_fetch_array($puja_result,MYSQLI_ASSOC)) {
            $i++;		
            $startdate=$row["boardstartdate"];
            $enddate=$row["boardenddate"];	    
            $begin=date("Y-m-d", strtotime($startdate));
            $end=date("Y-m-d", strtotime($enddate));	
            if ($curr>$end||$curr<$begin)
                continue; 		

            $wd=$row["startdate"];
            $startY=date('Y', strtotime($wd));					
            $startM=date('m', strtotime($wd));
            $startD=date('d', strtotime($wd));
                
            $wd=$row["enddate"];
            $endY=date('Y',strtotime($wd));					
            $endM=date('m',strtotime($wd));
            $endD=date('d',strtotime($wd));
            
            $pujabegin=$row["pujadate"];
            $pujaend=$row["pujadateend"];		
            $pujastartY=date('Y',strtotime($pujabegin));					
            $pujastartM=date('m',strtotime($pujabegin));
            $pujastartD=date('d',strtotime($pujabegin));
            $pujaendY=date('Y',strtotime($pujaend));					
            $pujaendM=date('m',strtotime($pujaend));
            $pujaendD=date('d',strtotime($pujaend));	
            
            $oneday = FALSE;
            if ($pujabegin==$pujaend)
                $oneday = TRUE;		
            //echo "<tr><td></td><td ><IMG class=\"topicimage\" style=\"float: left;\" alt=\"\" src=\"img/".$row["img"]."\"></td><td style=\"vertical-align:text-top\"><br>";
            echo "<tr><td rowspan=\"8\"><img class=\"topicimage\" style=\"float: left;\" alt=\"\" src=\"../_res/img/".$row["img"]."\"></td><td></td><td></td></tr>";
            echo "<tr><td>&nbsp;</td><td></td></tr>";		
            echo "<tr><td colspan=\"2\"><font color=\"#0000ff\">".$row["year"]." ".$row["pujaname"]."</font></td></tr>";
            echo "<tr><td style=\"width:70px\">報名日期 ：</td><td>".$startY."年".$startM."月".$startD."日 ~ ".$endY."年".$endM."月".$endD."日</td></tr>";
            
            if ($oneday==FALSE)
                echo "<tr><td>正行日期 ：</td><td>".$pujastartY."年".$pujastartM."月".$pujastartD."日 ~ ".$pujaendY."年".$pujaendM."月".$pujaendD."日</td></tr>";		
            else
                echo "<tr><td>正行日期 ：</td><td>".$pujastartY."年".$pujastartM."月".$pujastartD."日</td></tr>";
            
            echo "<tr><td style=\"vertical-align:text-top\">注意事項 ：</td><td colspan=\"3\">".$row["boarddesc"]."</td></tr>";			
            echo "<tr><td>&nbsp;</td></tr>";
            echo "<tr><td>&nbsp;</td></tr>";	
        }			
        echo "</table>";

        mysqli_free_result($puja_result);
        mysqli_close($con);
    }
?>
</TD>
</TR>
</TABLE></TD>
</TR>
</TABLE>
</TD></TR></TABLE></TD></TR>
</TABLE>