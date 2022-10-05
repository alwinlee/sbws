<?php
    function newClassMember(){
        $currY=date('Y');
        $currM=date('m');
        if ($currM>=3&&$currM<=5){return $currY."S";}
        if ($currM>=10&&$currM<=12){return $currY."F";}
        return "";
    }

    function newClassTitle(){
        $currY=date('Y');
        $currM=date('m');
        if ($currM>=3&&$currM<=5){return $currY." 春季";}
        if ($currM>=10&&$currM<=12){return $currY." 秋季";}
        return "";
    }

    function GetStartQMonth(){
        $currM = date('m');
        //$startM = floor(currM/4) * 3 + 1;
        //return $startM;
        switch ($currM)
        {
        case 12: case 1: case 2:
            return 12;

        case 3: case 4: case 5:
            return 3;

        case 6:	case 7:	case 8:
            return 6;

        case 9:case 10:case 11:
            return 9;
        }
    }

    function GetStartPrevQMonth(){
        $currM = date('m');
        //$startM = floor(currM/4) * 3 + 1;
        //return $startM;
        switch ($currM)
        {
        case 12: case 1: case 2:
            return 9;

        case 3: case 4: case 5:
            return 12;

        case 6:	case 7:	case 8:
            return 3;

        case 9:case 10:case 11:
            return 6;
        }
    }

    function getRollcallsYear(){
        $currY=date('Y');
        $currM=date('m');
        if ($currM <= 2){$currY -= 1;}
        return $currY;
    }

    function getRollcallsMonth(){
        $currM=date('m');
        if($currM==1){$currM+=12;}
        if($currM==2){$currM+=12;}
        return $currM;
    }

    function getRollcallsxYear(){
        $currY=date('Y');
        $currM=date('m');
        if ($currM <= 3){$currY -= 1;}
        return $currY;
    }

    function getRollcallsxMonth(){
		$currM=date('m');
		if($currM==1){$currM=12;}
		else if($currM==2){$currM=13;}
		else if($currM==3){$currM=14;}
		else {$currM-=1;}
		return $currM;
	}

	function canRollcallsx($userlevel) //
	{
	    //return true;// for testint or demo
	    $currY = date('Y');
		$currM = date('m');
		$currD = date('d');
        if($currD>=1 && $currD<=20){return true;}
        if($userlevel>=10){return true;}

		return false;
	}

    function GetRollcallYear()
	{
	    $currY = date('Y');
		$currM = date('m');

		if ($currM <= 2)
		    $currY -= 1;

		return $currY;
	}

    function GetRollcallXYear()
	{
	    $currY = date('Y');
		$currM = date('m');

		if ($currM <= 5)
		    $currY -= 1;

		return $currY;
	}

	function GetCurrentYear()
	{
	    $currY = date('Y');
		$currM = date('m');

		if ($currM <= 2)
		    $currY -= 1;

		return $currY;
	}

	function GetPUJAYear()
	{
	    $currY = date('Y');
		$currM = date('m');
		return $currY;
	}

	function GetNewYearPUJAYear()
	{
	    $currY = date('Y');
		$currM = date('m');

		if ($currM >= 11)
		    $currY += 1;

		return $currY;
	}

	function CanRollcallX() //
	{
	    //return true;// for testint or demo
	    $currY = date('Y');
		$currM = date('m');
		$currD = date('d');

		if ($currY <= 2014 && $currM <= 9) // 上線第一次, 不用補點名
		    return false;

		if ($currM != 12 && $currM != 3 && $currM != 6 && $currM != 9)
		    return false;

		if ($currD > 7)
            return false;

		return true;
	}

	function GetClassQueryString()
	{
		$calssroomkey = "";
		if (isset($_SESSION["keyofclassroom"]))
			$calssroomkey = $_SESSION["keyofclassroom"];

		$areakey = "";
		if (isset($_SESSION["keyofarea"]))
		{
		    if ($_SESSION["keyofarea"] != "")
			    $areakey = " `area`='".$_SESSION["keyofarea"]."' ";
		}

		$areaidkey = "";
		if (isset($_SESSION["keyofareaid"]))
		{
		    if ($_SESSION["keyofareaid"] != "")
			    $areaidkey = " `AREAID`='".$_SESSION["keyofareaid"]."' ";
		}

		$classroomdb = "classroom".GetRollcallYear();

		$sql = "";
		if ($calssroomkey == "" && $areakey == "" && $areaidkey == "")
		{
		   $sql = "SELECT * FROM ".$classroomdb."  ORDER BY `name` ASC";//classroom2014";//$sql = "SELECT * FROM classroom2014 where name LIKE '南10%'";
		}
		else
		{
			$strCondition = $calssroomkey;

                 if ($areakey!=""){
                     if ($strCondition == "")
                         $strCondition = $areakey;
                     else
                         $strCondition .= (" OR ".$areakey);
                 }

                 if ($areaidkey!=""){
			    if ($strCondition == "")
			        $strCondition = $areaidkey;
			    else
			        $strCondition .= (" OR ".$areaidkey);
                 }

			$sql = "SELECT * FROM ".$classroomdb." where ".$strCondition."  ORDER BY `name` ASC";
		}

        return $sql;
	}

	function GetClassQueryStringEx()
	{
		$calssroomkey = "";
		if (isset($_SESSION["keyofclassroom"]))
			$calssroomkey = $_SESSION["keyofclassroom"];

		$areakey = "";
		if (isset($_SESSION["keyofarea"]))
			$areakey = $_SESSION["keyofarea"];

		$areaidkey = "";
		if (isset($_SESSION["keyofareaid"]))
			$areaidkey = $_SESSION["keyofareaid"];

		$sql = "";
		$classroomdb = "classroom".GetRollcallXYear();
		if ($calssroomkey == "" && $areakey == "" && $areaidkey == "")
		{
		   $sql = "SELECT * FROM ".$classroomdb." where name LIKE '南14秋%'";//$sql = "SELECT * FROM classroom2014 where name LIKE '南10%'";
		}
		else
		{
			$strCondition = $calssroomkey;

			if ($strCondition == "")
			    $strCondition = $areakey;
			else
			    $strCondition .= (" OR ".$areakey);


			if ($strCondition == "")
			    $strCondition = $areaidkey;
			else
			    $strCondition .= (" OR ".$areaidkey);

			if ($strCondition == "")
			    $strCondition = "name LIKE '南14秋%'";
			else
			    $strCondition .= (" AND name LIKE '南14秋%'");

			$sql = "SELECT * FROM ".$classroomdb." where ".$strCondition;
		}

        return $sql;
	}

    function MobileBrowser()
	{
		$mobile_browser=0;

		if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser++;
		}

		if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
			$mobile_browser++;
		}

		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda','xda-','Googlebot-Mobile');

		if(in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser++;
		}

		if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
			$mobile_browser++;
		}

		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
			$mobile_browser=0;
		}

		//if($mobile_browser>0) {header("Location: mobile.php"); //手機版
		//}else {header("Location: pc.php");}  //電腦版

		return $mobile_browser;
	}


	function user_agent()
	{
	    $user_agent = $_SERVER['HTTP_USER_AGENT'];
		return $user_agent;
	}

	function mobile_check()
	{
		$mobile_browser = 0;
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$accept = $_SERVER['HTTP_ACCEPT'];
		if(preg_match('/(acs|alav|alca|amoi|audi|aste|avan|benq|bird|blac|blaz|brew|cell|cldc|cmd-)/i',$user_agent)){
		    $mobile_browser = 1;
		}elseif(preg_match('/(dang|doco|erics|hipt|inno|ipaq|java|jigs|kddi|keji|leno|lg-c|lg-d|lg-g|lge-)/i',$user_agent)){
		    $mobile_browser = 2;
		}elseif(preg_match('/(maui|maxo|midp|mits|mmef|mobi|mot-|moto|mwbp|nec-|newt|noki|opwv)/i',$user_agent)){
		    $mobile_browser = 3;
		}elseif(preg_match('/(palm|pana|pant|pdxg|phil|play|pluc|port|prox|qtek|qwap|sage|sams|sany)/i',$user_agent)){
		    $mobile_browser = 4;
		}elseif(preg_match('/(sch-|sec-|send|seri|sgh-|shar|sie-|siem|smal|smar|sony|sph-|symb|t-mo)/i',$user_agent)){
		    $mobile_browser = 5;
		}elseif(preg_match('/(teli|tim-|tosh|tsm-|upg1|upsi|vk-v|voda|w3cs|wap-|wapa|wapi)/i',$user_agent)){
		    $mobile_browser = 6;
		}elseif(preg_match('/(wapp|wapr|webc|winw|winw|xda|xda-)/i',$user_agent)){
		    $mobile_browser = 7;
		}elseif(preg_match('/(up.browser|up.link|windowssce|iemobile|mini|mmp)/i',$user_agent)){
		    $mobile_browser = 8;
		}elseif(preg_match('/(symbian|midp|wap|phone|pocket|mobile|pda|psp)/i',$user_agent)){
		    $mobile_browser = 9;
		}
		if((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0)){
		    $mobile_browser = 10;
		}
		if ($mobile_browser > 0) // mobile -> pad ?
		{
			if(preg_match('/pad/i',$user_agent)){
				$mobile_browser = 21;
			}
		}
		return $mobile_browser;
	}
?>
