<?php 
	header("Content-Type: text/html; charset=utf-8");
	session_start();  

	ini_set("error_reporting", 0);
	ini_set("display_errors","Off"); // On : open, Off : close
	error_reporting(E_ALL & ~E_NOTICE);

	require_once("../_res/_inc/login_check.php");//檢查是否已登入，若未登入則導回首頁
	require_once("../_res/_inc/connMysql.php"); 
	require_once("../_res/_inc/sharelib.php");

	date_default_timezone_set('Asia/Taipei');
	$userlevel=$_SESSION["userlevel"];
	$systemAuth=$_SESSION["systemAuth"];
?>

<!DOCTYPE HTML>
<HTML lang="en"><HEAD><META content="IE=11.0000" http-equiv="X-UA-Compatible">
<TITLE>南區學員點名系統-法會管理</TITLE> 

<META name="keywords" content=""> 
<META name="description" content=""> 
<META http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">     
<META name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon" href="../_res/img/icons.ico">  
<LINK href="../_res/css/site.css" rel="stylesheet" media="screen">   
<LINK href="../_res/css/stdtheme.css" rel="stylesheet" media="screen">   
<LINK href="../_res/css/bwsouth.css" rel="stylesheet" media="screen"> 
<link href="../_res/css/fixtblTheme.css"      rel="stylesheet" media="screen"/>

<SCRIPT src="../_res/js/analytics.js" type="text/javascript"></SCRIPT>
<SCRIPT src="../_res/js/jquery-2.1.1.min.js" type="text/javascript"></SCRIPT> 
<SCRIPT src="../_res/js/jquery.fixedheadertable.js"></SCRIPT>	
<SCRIPT src="../_res/js/datetime.js" type="text/javascript"></SCRIPT> 
<SCRIPT src="./js/account.js?{F017A570-2E23-4359-BF3C-E9E58038C23E}" type="text/javascript"></SCRIPT> 	

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

<TD align="left" class="navigation" id="navigationTree" valign="top"><?php $subMenuItem=300; include("inc\menu.php");?></TD>
				  
<TD class="content" id="demos" valign="top">
<h3 align="center" > <font color="#0000ff">權限管理</font></h2>
<TABLE style="vertical-align: top; border-collapse: collapse;" cellspacing="0" cellpadding="0">
<TR><TD valign="top" align="center">

<div class="demoContainer" id="demoContainer">
<br>

<?php
    if ($systemAuth[0]<=0){exit;}
    if ($systemAuth[1]<=0){exit;}
/*	$areakey="";
	if (isset($_SESSION["keyofareaid"])&&$_SESSION["keyofareaid"]!=""){$areakey=" AND `RegionCode`='".$_SESSION["keyofareaid"]."'";}
	$sql="SELECT * FROM `sdm_classes` WHERE `IsCurrent`=1"." AND `ARE_ID`='AC'".$areakey." ORDER BY `Class` ASC"; 
	
	//echo $sql;//debug	
	$result_class=mysql_query($sql); 
	$numrows=mysql_num_rows($result_class);	
*/	
	
    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    // command panel
    echo "<table class=\"reference\" style=\"width:820px;\" align=\"center\"><tr><td>";
	echo "<table class=\"refgroup\" style=\"width:760px\" align=\"center\" valign=\"center\"><tr></tr>";//<td colspan=\"9\"></td></tr>";
	echo "<tr style=\"background:rgb(241,241,241);\">";
	echo "<td style=\"width:85px;\"></td>";
	echo "<td style=\"width:85px;\"></td>";
	echo "<td style=\"width:85px;\"></td>";
	echo "<td style=\"width:85px;text-align:right;\">使用者:</td>";
	
	echo "<td style=\"width:85px\"><select style=\"width:109px;\" id=\"rollcallclass\" class=\"rollcallclass\" name=\"rollcallclass\">";
	echo "<option value=\"-\">-</option>";
	//while($row = mysql_fetch_array($result_class, MYSQL_ASSOC))
	//{
	//    $classname=explode("-",$row["Class"]);
	//	//echo "<option value=\"".$row["CLS_ID"]."\">".$classname[0]."</option>";
    //    echo "<option value=\"".$row["CLS_ID"]."\">".$classname[0]."</option>";		
	//}		
	echo "</select></td>";
	
	echo "<td style=\"width:85px;\"></td>";//<input type=\"button\" id=\"gettable\" class=\"gettable\" name=\"gettable\" value=\"點名簿\" />	</td>";	
	echo "<td style=\"width:85px;\"></td>";
	echo "<td style=\"width:85px;\"></td>";
	echo "<td style=\"width:85px;\"></td>";	
	echo "</tr>";	
	echo "<tr>";
	//echo "<tr><td colspan=\"9\"><div id=\"msg\" class=\"msg\"></div></td>";//debug info
	echo "</tr></table></td></tr></table>";
	echo "<hr>";
    
	//-----------------------------------------------------------------------------------------------------------------------------------------------------	
	// class table
	echo "<div class=\"fix_container\"><div id=\"queryresult\" class=\"grid_x height450\"  style=\"width:800px;align:center;\">";
	
	//echo "<table><tr align=\"center\"><td>";	
	//echo "<table class=\"reference\" id=\"fixtbl\" style=\"width:1020px;\"><thead><tr><th align=\"center\" style=\"width:35px\">序</th><th align=\"center\" style=\"width:85px\">學員姓名</th><th align=\"center\" style=\"width:80px;\">身份</th><th style=\"width:63px;\">12/02</th><th style=\"width:63px\">12/09</th><th style=\"width:63px\">12/16</th><th style=\"width:63px\">12/23</th><th style=\"width:63px\">12/30</th><th style=\"width:63px\">01/06</th><th style=\"width:63px\">01/13</th><th style=\"width:63px\">01/20</th><th style=\"width:63px\">01/27</th><th style=\"width:63px\">02/03</th><th style=\"width:63px\">02/10</th><th style=\"width:63px\">02/17</th><th style=\"width:63px\">02/24</th></tr></thead><tbody><tr></tr><tr><td>1</td><td>鄭昭儒</td><td>班長</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>2</td><td>王志榮</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>3</td><td>李春生</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>4</td><td>李達英</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>5</td><td>林坐</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>6</td><td>林育靖</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>7</td><td>林海大</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>8</td><td>林常如</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>9</td><td>邱秀雄</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>10</td><td>胡毓仁</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>11</td><td>張仕隆</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>12</td><td>張俊傑</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>13</td><td>張振源</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>14</td><td>張聖傑</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>15</td><td>郭文言</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>16</td><td>曾和源</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>17</td><td>馮垂中</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>18</td><td>劉增寰</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>19</td><td>盧欽明</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>20</td><td>顏明典</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>21</td><td>魏文道</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>22</td><td>蘇智祥</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>23</td><td>刁錦胭</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>24</td><td>王熹淑</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>25</td><td>王穗茹</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>26</td><td>吳月春</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>27</td><td>吳秀梅</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>28</td><td>吳珠芳</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>29</td><td>吳寶珠</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>30</td><td>李伊</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>31</td><td>李秀麗</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>32</td><td>李素菁</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>33</td><td>杜琇琴</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>34</td><td>步家珍</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>35</td><td>林素珍</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>36</td><td>林淑娟</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>37</td><td>林琇豐</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>38</td><td>林糖酸</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>39</td><td>徐金花</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>40</td><td>徐秋敏</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>41</td><td>翁梨珠</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>42</td><td>翁梨滿</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>43</td><td>曹安玉</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>44</td><td>許寶月</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>45</td><td>郭娟媚</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>46</td><td>陳玉香</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>47</td><td>陳美麗</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>48</td><td>陳筱玉</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>49</td><td>陳寶珠 </td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>50</td><td>陶美蓉</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>51</td><td>黃碧珍</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>52</td><td>黃慧雯</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>53</td><td>黃麗雲</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>54</td><td>黃寶桂</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>55</td><td>楊月綿</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>56</td><td>楊舜閔</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>57</td><td>楊寶美</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>58</td><td>葉淑萍</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>59</td><td>葉華英</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>60</td><td>廖顏鳳</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>61</td><td>劉惠方</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>62</td><td>蔡秀蓮</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>63</td><td>鍾秀美</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>64</td><td>鍾英芳</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>65</td><td>藍培玉</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>66</td><td>魏順珠</td><td>班員</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tbody></table>";
	//echo "<table class=\"reference\" id=\"fixheadertbl\" style=\"width:1000px;\">";
	//echo "<div id=\"queryresult\" class=\"queryresult\"></div>";
	
	//echo "</table>";
	//echo "</td></tr>";		
	//echo "</table>";// }}}}}}}}}}}}}}}}}}}}}	
	echo "</div></div>";
	
	echo "<br>";
	echo "<table align=\"center\" style=\"width:800px;\">";//"<table align=\"center\" style:\"width=1018px;\">";
	echo "<tr style=\"align:center;\"><td></td><td></td><td style=\"text-align:center;\"></td><td></td><td></td></tr>";	
	echo "</table>";// }}}}}}}}}}}}}}}}}}}}}	
	
	$currDate = date('Y-m-d');//$startM=GetStartQMonth();$endM=$startM+2;$currDate = date('Y-m-d');	
	echo "<input type=\"hidden\" id=\"classroomtb\" class=\"membertb\" name=\"membertb\" value=\"member\" />";
	echo "<input type=\"hidden\" id=\"currentdate\" name=\"currentdate\" value=\"".$currDate."\" />";		
?>	
</TD>
</TR>
</TABLE></TD>
</TR>

</TABLE>
</TD></TR></TABLE></TD></TR>
</TABLE>

</BODY></HTML>
