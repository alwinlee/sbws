<?php
	//header("Content-Type: text/html; charset=utf-8");
	//session_start();
    //header("Content-type: application/json; charset=utf-8");
	require_once("../../../_res/_inc/connMysql.php");
	require_once("../../../_res/_inc/sharelib.php");

	ini_set("error_reporting", 0);
    ini_set("display_errors","Off"); // On : open, Off : close
	date_default_timezone_set('Asia/Taipei');

    $sql = "";
    $sql .= "    SELECT";
    $sql .= "        D.Class AS '班級', D.OrgShortName AS '教室', C.CLS_ID AS '班級代號', C.TTL_ID AS 身份, C.Name AS '姓名',";
    $sql .= "        C.STU_ID AS '學員代號', C.Sex AS '性別', C.Age AS '年齡' FROM (";
    $sql .= "        SELECT";
    $sql .= "            A.CLS_ID, A.TTL_ID, B.Name , B.STU_ID, B.Sex, B.Age";
    $sql .= "        FROM";
    $sql .= "            sdm_clsmembers AS A JOIN sdm_students AS B ON A.STU_ID=B.STU_ID";
    $sql .= "        WHERE";
    $sql .= "            B.IsCurrent=1";
    $sql .= "    ) AS C JOIN sdm_classes AS D ON C.CLS_ID = D.CLS_ID WHERE D.ClsStatus = 'Y'";
    $sql .= "    ORDER BY C.CLS_ID ASC, ( CASE  C.TTL_ID";
    $sql .= "        WHEN '班長' THEN '01'";
    $sql .= "        WHEN '副班長' THEN '02'";
    $sql .= "        WHEN '關懷員' THEN '03'";
    $sql .= "        WHEN '學員' THEN '04'";
    $sql .= "        ELSE '99' END";
    $sql .= "    ) , C.Sex DESC, C.STU_ID ASC;";

    $con = mysqli_connect("localhost","root","rinpoche","sdmdb");
    $sql_result=mysqli_query($con, $sql);
    $numrows=mysqli_num_rows($sql_result);

    $retArray = array();
    while($row = $sql_result->fetch_array(MYSQL_ASSOC)) {
        $retArray[] = $row;
    }

    $json_ret=array("result"=>$retArray,"num"=>$numrows, "sql"=>$sql);//,"traffic"=>$traffic);
    echo json_encode($json_ret);//echo $sql;
?>