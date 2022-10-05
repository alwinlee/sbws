<?php 
	//檢查是否已登入，若未登入則導回首頁
	if(!isset($_SESSION["username"]) || ($_SESSION["username"]=="") || ($_SESSION["location"]!="bws")) {
		unset($_SESSION["location"]);
	    header("Location: ../index.php");
		exit;
	}
?>