<?php 
    header("Content-Type: text/html; charset=utf-8");
    require_once("./_res/_inc/connMysql.php");
    $dbname = "bwsouthdb";
    $con = connect_db($dbname);
    session_start();

    // 將SESSION資料清除，並重導回首頁
    unset($_SESSION["username"]);  
    unset($_SESSION["keyofclassroom"]);
    unset($_SESSION["keyofarea"]);
    unset($_SESSION["keyofareaid"]);
    unset($_SESSION["userlevel"]);
    unset($_SESSION["username"]);
    //unset($_SESSION["location"]);

    //檢查是否已登入，若已登入則導向會員中心
    if(isset($_SESSION["username"]) && ($_SESSION["username"]!="") && isset($_SESSION["location"]) && ($_SESSION["location"]=="bws"))
    {
        if (isset($_POST["func"]))
        {
            if($_POST["func"]=="rollcall"){header("Location: ./rollcall/main.php");exit;}
            else if($_POST["func"]=="ceremony"){header("Location: ./ceremony/main.php");exit;}
            else if($_POST["func"]=="student"){header("Location: ./student/main.php");exit;}
            else if($_POST["func"]=="books"){header("Location: ./books/main.php");exit;}
            else if($_POST["func"]=="classes"){header("Location: ./classes/main.php");exit;}     
            else{header("Location: ./rollcall/main.php");}
        }else{header("Location: ./rollcall/main.php");exit;}
    }
    if(isset($_POST["username"])==false||isset($_POST["password"])==false){
        unset($_SESSION["location"]);
        header("Location: index.php?loginFail=true");
        exit;
    }
	
    //查詢登入會員資料
    $sql = "SELECT * FROM member WHERE `account`='".$_POST["username"]."' AND `password`=PASSWORD('".$_POST["password"]."')";
    $result = mysqli_query($con, $sql);
    $numrows = mysqli_field_count($con);
    if($numrows<=0){unset($_SESSION["location"]);header("Location: index.php?loginFail=true");exit;}
	
    //取出帳號密碼的值
    $row=mysqli_fetch_assoc($result);
    $username=$row["account"];
    $user=$row["name"];
    $password=$row["password"];
    $keyofclassroom=$row["keyofclassroom"];
    $keyofarea=$row["keyofarea"];
    $keyofareaid=$row["keyofareaid"];	
    $userlevel=$row["level"];
    $authary=explode("-",$row["auth"]); //$auth=$row["auth"];	//echo $auth;exit;
    mysqli_free_result($result);
    mysqli_close($con);
    //將使用者帳號存入Session
    $_SESSION["username"]=$username;
    $_SESSION["user"]=$user;	
    $_SESSION["keyofclassroom"]=$keyofclassroom;
    $_SESSION["keyofarea"]=$keyofarea;
    $_SESSION["keyofareaid"]=$keyofareaid;	
    $_SESSION["userlevel"]=$userlevel;
    $_SESSION["rollcallAuth"]=$authary[0];
    $_SESSION["ceremonyAuth"]=$authary[1];
    $_SESSION["studentAuth"]=$authary[2];
    $_SESSION["extAuth"]=$authary[3];
    $_SESSION["systemAuth"]=$authary[4];
    $_SESSION["location"]="bws";    
	  
	if (isset($_POST["func"]))
	{
		if($_POST["func"]=="rollcall"){header("Location: ./rollcall/main.php");exit;}
		else if($_POST["func"]=="ceremony"){header("Location: ./ceremony/main.php");exit;}
		else if($_POST["func"]=="student"){header("Location: ./student/main.php");exit;}
		else if($_POST["func"]=="books"){header("Location: ./books/main.php");exit;}
		else if($_POST["func"]=="classes"){header("Location: ./classes/main.php");exit;}        
		else{header("Location: ./rollcall/main.php");}
	}else{header("Location: ./rollcall/main.php");exit;}
?>