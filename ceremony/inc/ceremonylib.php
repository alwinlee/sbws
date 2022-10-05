<?php
    function getClassMembers(){
        $con = mysqli_connect("localhost","root","rinpoche", "sdmdb");
        $sql = "SELECT A.CLS_ID, A.CNT, B.Class, B.OrgName FROM ( ";
        $sql .= "SELECT CLS_ID, COUNT(*) AS CNT FROM `sdmdb`.`sdm_clsmembers` GROUP BY `CLS_ID` ";
        $sql .= ") AS A ";
        $sql .= "INNER JOIN `sdmdb`.`sdm_classes` AS B ON A.CLS_ID=B.CLS_ID WHERE B.ClsStatus='Y' ";
        $sql .= "ORDER BY B.Class ASC ";
        $classinfo = [];
        $sql_result=mysqli_query($con, $sql);
        $numrows=mysqli_num_rows($sql_result);
        if($numrows<=0){return $classinfo;}
        $i=0;
        while($row=mysqli_fetch_array($sql_result, MYSQLI_ASSOC)){//MYSQL_NUM))//MYSQL_ASSOC))
             $classinfo[$i][0]=$row['Class']."-".$row['OrgName'];//class name
             $classinfo[$i][1]=$row['CLS_ID'];
             $classinfo[$i][2]=$row['CNT'];
             $classinfo[$i][3]=0;
             $classinfo[$i][4]=0;
             $i++;
        }
        mysqli_close($con);
        return $classinfo;
    }

    function getClassMembersEx(){
        $sql="select `CLS_ID`,COUNT(*) from `sdm_clsmembers` WHERE (`Status`='參與') GROUP BY `CLS_ID`";// $sql="select `CLS_ID`,COUNT(*) from `sdm_clsmembers` WHERE (`IsMajor`='是' AND `Status`='參與') GROUP BY `CLS_ID`";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        if($numrows<=0){return;}
        $i=0;
        while($row=mysql_fetch_array($sql_result, MYSQL_NUM))//MYSQL_NUM))//MYSQL_ASSOC))
        {
             $classinfo[$i][0]="";//class name
             $classinfo[$i][1]=$row[0];
             $classinfo[$i][2]=$row[1];
             $classinfo[$i][3]=0;
             $classinfo[$i][4]=0;
             $classinfo[$i][5]=0;//remark
             $classinfo[$i][6]=0;//幹部
             $classinfo[$i][7]=0;//學員
             $classinfo[$i][8]=0;//男幹部
             $classinfo[$i][9]=0;//女幹部
             $classinfo[$i][10]=0;// 男學員
             $classinfo[$i][11]=0;//女學員
             $classinfo[$i][12]=0;//65歲以上
             $i++;
        }

        $sql="select `CLS_ID`,COUNT(*) from `sdm_clsmembers` WHERE (`IsMajor`='是' AND `Status`='參與') GROUP BY `CLS_ID`";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        if($numrows<=0){return;}
        $i=0;
        while($row=mysql_fetch_array($sql_result, MYSQL_NUM))//MYSQL_NUM))//MYSQL_ASSOC))
        {
            for($i=0;$i<count($classinfo);$i++)
            {
                if($classinfo[$i][1]==$row[0])
                {
                    $classinfo[$i][7]=$row[1];
                    $classinfo[$i][6]=($classinfo[$i][2]-$row[1]);
                    break;
                }
            }
        }



        $sql="select `CLS_ID`,`Class`,`Remark`,`RegionCode` from `sdm_classes` WHERE `IsCurrent`=1";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);

        //填入班級名
        while($row=mysql_fetch_array($sql_result, MYSQL_NUM))//MYSQL_NUM))//MYSQL_ASSOC))
        {
            for($i=0;$i<count($classinfo);$i++)
            {
                if($classinfo[$i][1]==$row[0])
                {
                    $classinfo[$i][0]=$row[1];
                    $classinfo[$i][3]=$row[3];
                    $classinfo[$i][5]=$row[2];
                    break;
                }
            }
        }
        return $classinfo;
    }

    function canCheckin($userlevel,$mgr,$enddate,$mgrend){
        //echo $userlevel."-".$mgr."-".$enddate."-".$mgrend."-".$curr;
        if($userlevel>=10){return true;}
        $curr=date('Y-m-d');
        $currY=date('Y');
        $currM=date('m');
        $currD=date('d');
        if(($mgr>=1)&&($curr<=$mgrend)){return true;}
        if($curr<=$enddate){return true;}
        return false;
    }

    function canCheckinex($userlevel,$areakey,$mgr,$enddate,$areaend,$mgrend){
        //echo $userlevel."-".$mgr."-".$enddate."-".$mgrend."-".$curr;
        if($userlevel>=10){return true;}
        $curr=date('Y-m-d');
        if($mgr>=1){
            if($areakey==""||$areakey=="*"){
                if($curr<=$mgrend){return true;}//mgr
            }else{
                if($curr<=$areaend){return true;}//area mgr
            }
        }
        if($curr<=$enddate){return true;}
        return false;
    }

    function chklonglifetb($tbname){
        if ($tbname==""){return false;}
        $sql="SHOW TABLES LIKE '".$tbname."'";
        $sql_result=mysql_query($sql);
        $numrows = mysql_num_rows($sql_result);
        if ($numrows>=1){return true;}

        $sql ="CREATE TABLE IF NOT EXISTS `".$tbname."`(";
        $sql.="`idx`            int(8) NOT NULL auto_increment,";
        $sql.="`lock`           int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`name`	        varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學員姓名',";
        $sql.="`classname`	  varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '班級',";
        $sql.="`classid`	  varchar(8)  collate utf8_unicode_ci NOT NULL COMMENT '班級ID',";
        $sql.="`area`	        varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域',";
        $sql.="`areaid`	        varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域ID',";
        $sql.="`title`	        varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`TTL_ID`	        varchar(4) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`titleid`	  int(4) default 0 COMMENT '學員類別 8:班長 7:副班長 6:關懷員 4:班長班員 3:副班長班員 2:關懷員班員 1:一般班員 0:暫停班員',";
        $sql.="`STU_ID`	        varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員代號',";
        $sql.="`staffid`	  varchar(12) collate utf8_unicode_ci COMMENT '職員代號',";
        $sql.="`sex`	        varchar(2) collate utf8_unicode_ci COMMENT '性別',";
        $sql.="`age`	        int(8) default 0 COMMENT '年齡',";
        $sql.="`edu`	        varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學歷',";
        $sql.="`school`	        varchar(60) collate utf8_unicode_ci NOT NULL COMMENT '學校',";
        $sql.="`jobtype`	  varchar(40) collate utf8_unicode_ci NOT NULL COMMENT '職業別',";
        $sql.="`company`	  varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '公司',";
        $sql.="`jobtitle`	  varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '職稱',";
        $sql.="`PhoneNo_H`	  varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(宅)',";
        $sql.="`PhoneNo_C`	  varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(手機)',";
        $sql.="`memberseq`	  int(8) default 0 COMMENT '排序',";
        $sql.="`day`            int(8) default 0 COMMENT '參加第幾天',";
        $sql.="`family`         int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service`        int(8) default 0 COMMENT '是否為義工或取消報名註記',";
        $sql.="`traff`          varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式',";
        $sql.="`traffCnt`       int(4) default 0 COMMENT '來回, 單去,單回',";
        $sql.="`traffReal`      varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式',";
        $sql.="`traffRealCnt`   int(4) default 0 COMMENT '0:無 1:來回, 2:單去,3:單回',";
        $sql.="`cost`           int(4) default 0 COMMENT '車資',";
        $sql.="`pay`            int(4) default 0 COMMENT '繳費',";
        $sql.="`attend`         int(4) default 0 COMMENT '報到',";
        $sql.="`regdate`        date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate`        date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround`       int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`memo`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";
        $sql.="PRIMARY KEY (`idx`)";
        $sql.=")ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32;";
        //echo $sql;
        $sql_result=mysql_query($sql);
        return true;
    }

    function chkNiLecturetb($tbname){
        if ($tbname==""){return false;}
        $sql="SHOW TABLES LIKE '".$tbname."'";
        $sql_result=mysql_query($sql);
        $numrows = mysql_num_rows($sql_result);
        if ($numrows>=1){return true;}

        $sql ="CREATE TABLE IF NOT EXISTS `".$tbname."`(";
        $sql.="`idx`                int(8) NOT NULL auto_increment,";
        $sql.="`lock`               int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`name`	            varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學員姓名',";
        $sql.="`classname`	      varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '班級',";
        $sql.="`classid`	      varchar(8)  collate utf8_unicode_ci NOT NULL COMMENT '班級ID',";
        $sql.="`area`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域',";
        $sql.="`areaid`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域ID',";
        $sql.="`title`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`TTL_ID`	            varchar(4) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`titleid`	      int(4) default 0 COMMENT '學員類別 8:班長 7:副班長 6:關懷員 4:班長班員 3:副班長班員 2:關懷員班員 1:一般班員 0:暫停班員',";
        $sql.="`STU_ID`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員代號',";
        $sql.="`staffid`	      varchar(12) collate utf8_unicode_ci COMMENT '職員代號',";
        $sql.="`sex`	            varchar(2) collate utf8_unicode_ci COMMENT '性別',";
        $sql.="`age`	            int(8) default 0 COMMENT '年齡',";
        $sql.="`edu`	            varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學歷',";
        $sql.="`school`	            varchar(60) collate utf8_unicode_ci NOT NULL COMMENT '學校',";
        $sql.="`jobtype`	      varchar(40) collate utf8_unicode_ci NOT NULL COMMENT '職業別',";
        $sql.="`company`	      varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '公司',";
        $sql.="`jobtitle`	      varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '職稱',";
        $sql.="`PhoneNo_H`	      varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(宅)',";
        $sql.="`PhoneNo_C`	      varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(手機)',";
        $sql.="`memberseq`	      int(8) default 0 COMMENT '排序',";
        $sql.="`day`                int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal`               int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family`             int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service`            int(8) default 0 COMMENT '是否為義工',";
        $sql.="`traff`              varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffCnt`           int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`traffReal`          varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffRealCnt`       int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`cost`               int(4) default 0 COMMENT '車資',";
        $sql.="`pay`                int(4) default 0 COMMENT '繳費',";
        $sql.="`attend`             int(4) default 0 COMMENT '報到',";
        $sql.="`regdate`            date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate`            date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround`           int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`memo`	            varchar(80) collate utf8_unicode_ci COMMENT '備註',";
        $sql.="PRIMARY KEY  (`idx`)";
        $sql.=")ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32;";

        //echo $sql;
        $sql_result=mysql_query($sql);
        return true;
    }

    function chkUpgradeAugtb($tbname){
        if ($tbname==""){return false;}
        $sql="SHOW TABLES LIKE '".$tbname."'";
        $sql_result=mysql_query($sql);
        $numrows = mysql_num_rows($sql_result);
        if ($numrows>=1){return true;}

        $sql ="CREATE TABLE IF NOT EXISTS `".$tbname."`(";
        $sql.="`idx`                int(8) NOT NULL auto_increment,";
        $sql.="`lock`               int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`name`	            varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學員姓名',";
        $sql.="`classname`	      varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '班級',";
        $sql.="`classid`	      varchar(8)  collate utf8_unicode_ci NOT NULL COMMENT '班級ID',";
        $sql.="`area`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域',";
        $sql.="`areaid`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域ID',";
        $sql.="`title`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`TTL_ID`	            varchar(4) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`titleid`	      int(4) default 0 COMMENT '學員類別 8:班長 7:副班長 6:關懷員 4:班長班員 3:副班長班員 2:關懷員班員 1:一般班員 0:暫停班員',";
        $sql.="`STU_ID`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員代號',";
        $sql.="`staffid`	      varchar(12) collate utf8_unicode_ci COMMENT '職員代號',";
        $sql.="`sex`	            varchar(2) collate utf8_unicode_ci COMMENT '性別',";
        $sql.="`age`	            int(8) default 0 COMMENT '年齡',";
        $sql.="`edu`	            varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學歷',";
        $sql.="`school`	            varchar(60) collate utf8_unicode_ci NOT NULL COMMENT '學校',";
        $sql.="`jobtype`	      varchar(40) collate utf8_unicode_ci NOT NULL COMMENT '職業別',";
        $sql.="`company`	      varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '公司',";
        $sql.="`jobtitle`	      varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '職稱',";
        $sql.="`PhoneNo_H`	      varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(宅)',";
        $sql.="`PhoneNo_C`	      varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(手機)',";
        $sql.="`memberseq`	      int(8) default 0 COMMENT '排序',";
        $sql.="`day`                int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal`               int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family`             int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service`            int(8) default 0 COMMENT '是否為義工',";
        $sql.="`traff`              varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffCnt`           int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`traffReal`          varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffRealCnt`       int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`cost`               int(4) default 0 COMMENT '車資',";
        $sql.="`pay`                int(4) default 0 COMMENT '繳費',";
        $sql.="`attend`             int(4) default 0 COMMENT '報到',";
        $sql.="`regdate`            date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate`            date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround`           int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid`	      varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname`	      varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`memo`	            varchar(80) collate utf8_unicode_ci COMMENT '備註',";
        $sql.="PRIMARY KEY  (`idx`)";
        $sql.=")ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32;";

        //echo $sql;
        $sql_result=mysql_query($sql);
        mysql_query("COMMIT");
        return true;
    }

    function chkUpgradetb($tbname){
        if ($tbname==""){return false;}
        $sql="SHOW TABLES LIKE '".$tbname."'";
        $sql_result=mysql_query($sql);
        $numrows = mysql_num_rows($sql_result);
        if ($numrows>=1){return true;}

        $sql ="CREATE TABLE IF NOT EXISTS `".$tbname."`(";
        $sql.="`idx`                int(8) NOT NULL auto_increment,";
        $sql.="`lock`               int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`name`	            varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學員姓名',";
        $sql.="`classname`	      varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '班級',";
        $sql.="`classid`	      varchar(8)  collate utf8_unicode_ci NOT NULL COMMENT '班級ID',";
        $sql.="`area`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域',";
        $sql.="`areaid`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域ID',";
        $sql.="`title`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`TTL_ID`	            varchar(4) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`titleid`	      int(4) default 0 COMMENT '學員類別 8:班長 7:副班長 6:關懷員 4:班長班員 3:副班長班員 2:關懷員班員 1:一般班員 0:暫停班員',";
        $sql.="`STU_ID`	            varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員代號',";
        $sql.="`staffid`	      varchar(12) collate utf8_unicode_ci COMMENT '職員代號',";
        $sql.="`sex`	            varchar(2) collate utf8_unicode_ci COMMENT '性別',";
        $sql.="`age`	            int(8) default 0 COMMENT '年齡',";
        $sql.="`edu`	            varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學歷',";
        $sql.="`school`	            varchar(60) collate utf8_unicode_ci NOT NULL COMMENT '學校',";
        $sql.="`jobtype`	      varchar(40) collate utf8_unicode_ci NOT NULL COMMENT '職業別',";
        $sql.="`company`	      varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '公司',";
        $sql.="`jobtitle`	      varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '職稱',";
        $sql.="`PhoneNo_H`	      varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(宅)',";
        $sql.="`PhoneNo_C`	      varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(手機)',";
        $sql.="`memberseq`	      int(8) default 0 COMMENT '排序',";
        $sql.="`day`                int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal`               int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family`             int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service`            int(8) default 0 COMMENT '是否為義工',";
        $sql.="`traff`              varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffCnt`           int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`traffReal`          varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffRealCnt`       int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`cost`               int(4) default 0 COMMENT '車資',";
        $sql.="`pay`                int(4) default 0 COMMENT '繳費',";
        $sql.="`attend`             int(4) default 0 COMMENT '報到',";
        $sql.="`regdate`            date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate`            date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround`           int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid`	      varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname`	      varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`memo`	            varchar(80) collate utf8_unicode_ci COMMENT '備註',";
        $sql.="PRIMARY KEY  (`idx`)";
        $sql.=")ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32;";

        //echo $sql;
        $sql_result=mysql_query($sql);
        mysql_query("COMMIT");
        return true;
    }

    function chkPujaTraffTB($tbname){
        if ($tbname==""){return false;}
        $sql="SHOW TABLES LIKE '".$tbname."'";
        $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
        $sql_result=mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($sql_result);
        if ($numrows>=1){
            mysqli_close($con);
            return true;
        }

        $sql_traff="CREATE TABLE IF NOT EXISTS `".$tbname."` (";
        $sql_traff.="`idx` int(8) NOT NULL auto_increment,";
        $sql_traff.="`traffid` varchar(8) COLLATE utf8_unicode_ci NOT NULL COMMENT '車次代號',";
        $sql_traff.="`day` int(8) DEFAULT 0 COMMENT '第幾天的車次',";
        $sql_traff.="`traffname` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT '車次位置名稱',";
        $sql_traff.="`traffdesc` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT '車次位置說明',";
        $sql_traff.="`gocost` int(4) DEFAULT '0' COMMENT '去程費用',";
        $sql_traff.="`backcost` int(4) DEFAULT '0' COMMENT '回程費用',";
        $sql_traff.="`roundcost` int(4) DEFAULT '0' COMMENT '去回程費用',";
        $sql_traff.="`mealcost` int(4) DEFAULT '0' COMMENT '餐點費用',";
        $sql_traff.="`morningsession` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '上午場說明',";
        $sql_traff.="`afternoonsession` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '下午場說明',";
        $sql_traff.="UNIQUE KEY `idx` (`idx`)";
        $sql_traff.=") ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        //echo $sql;
        $sql_result=mysqli_query($sql_traff);
        mysqli_commit($con);
        mysqli_close($con);
        // create default table
        /*$sql_insert="";
        for($i=0;$i<3;$i++){
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'Z',".$i.", '自往', '*', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'AB', ".$i.", '大順建工', '*', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'AC', ".$i.", '小港空大', '*', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'AE', ".$i.", '文化中心', '*', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'AF', ".$i.", '鳳山行政', '*', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'BB', ".$i.", '楠梓交流', '*', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'Z',  ".$i.", '自往', '3', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'EA', ".$i.", '東港國小', '3', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'EB', ".$i.", '新庄國小', '3', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'EC', ".$i.", '台糖大門', '3', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'ED', ".$i.", '屏東縣議會', '3', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'EE', ".$i.", '潮州', '3', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'EF', ".$i.", '內埔農會', '3', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'EG', ".$i.", '麟洛鄉公所', '3', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'EH', ".$i.", '九如鄉公所', '3', 0, 0, 0, 0, '06:00', '06:00');";
            $sql_insert.="INSERT INTO `".$tbname."` VALUES (NULL, 'EI', ".$i.", '萬丹西環路', '3', 0, 0, 0, 0, '06:00', '06:00');";
        }
        $sql_result=mysql_query($sql_insert);
        mysql_query("COMMIT");*/
        return true;
    }

    function chkPujaTB($tbname){
        if ($tbname==""){return false;}
        $sql="SHOW TABLES LIKE '".$tbname."'";

        $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
        $result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($result);
        if ($numrows>=1){
            mysqli_close($con);
            return true;
        }

        $sql ="CREATE TABLE IF NOT EXISTS `".$tbname."`(";
        $sql.="`idx`           int(8) NOT NULL auto_increment,";
        $sql.="`lock`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`name`	       varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學員姓名',";
        $sql.="`classname`     varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '班級',";
        $sql.="`CLS_ID`	       varchar(20)  collate utf8_unicode_ci NOT NULL COMMENT '班級ID',";
        $sql.="`area`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域',";
        $sql.="`areaid`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域ID',";
        $sql.="`title`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`TTL_ID`	       varchar(4) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`titleid`	 int(4) default 0 COMMENT '學員類別 8:班長 7:副班長 6:關懷員 4:班長班員 3:副班長班員 2:關懷員班員 1:一般班員 0:暫停班員',";
        $sql.="`STU_ID`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員代號',";
        $sql.="`staffid`	 varchar(12) collate utf8_unicode_ci COMMENT '職員代號',";
        $sql.="`sex`	       varchar(2) collate utf8_unicode_ci COMMENT '性別',";
        $sql.="`age`	       date default '1970-01-01' COMMENT '年齡',";
        $sql.="`edu`	       varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學歷',";
        $sql.="`school`	       varchar(60) collate utf8_unicode_ci NOT NULL COMMENT '學校',";
        $sql.="`jobtype`	 varchar(40) collate utf8_unicode_ci NOT NULL COMMENT '職業別',";
        $sql.="`company`	 varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '公司',";
        $sql.="`jobtitle`	 varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '職稱',";
        $sql.="`PhoneNo_H`     varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(宅)',";
        $sql.="`PhoneNo_C`     varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(手機)',";
        $sql.="`memberseq`     int(8) default 0 COMMENT '排序',";
        $sql.="`day`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`traff`         varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffCnt`      int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`traffReal`     varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffRealCnt`  int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`cost`          int(4) default 0 COMMENT '車資',";
        $sql.="`pay`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid`	 varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`memo`	       varchar(80) collate utf8_unicode_ci COMMENT '備註',";
        $sql.="`leaderinfo`    varchar(255) collate utf8_unicode_ci COMMENT '擔任班幹部資訊',";
        $sql.="`volunteerinfo` varchar(200) collate utf8_unicode_ci COMMENT '擔任義工資訊',";
        $sql.="`otherinfo`     varchar(120) collate utf8_unicode_ci COMMENT '其他額外資訊',";// 幹部母班....
        $sql.="`classfullname` varchar(20) collate utf8_unicode_ci COMMENT  '班級全名',";// 幹部母班....
        $sql.="PRIMARY KEY  (`idx`)";
        $sql.=")ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32;";

        //echo $sql;
        $sql_result=mysqli_query($con, $sql);
        mysqli_commit($con);
        return true;
    }

    function chkPujaTBex($tbname){
        if ($tbname==""){return false;}
        $sql="SHOW TABLES LIKE '".$tbname."'";

        $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
        $result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($result);
        if ($numrows>=1){
            mysqli_close($con);
            return true;
        }

        $sql ="CREATE TABLE IF NOT EXISTS `".$tbname."`(";
        $sql.="`idx`           int(8) NOT NULL auto_increment,";
        $sql.="`lock`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`name`	       varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學員姓名',";
        $sql.="`classname`     varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '班級',";
        $sql.="`CLS_ID`	       varchar(20)  collate utf8_unicode_ci NOT NULL COMMENT '班級ID',";
        $sql.="`area`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域',";
        $sql.="`areaid`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域ID',";
        $sql.="`title`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`TTL_ID`	       varchar(4) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`titleid`	 int(4) default 0 COMMENT '學員類別 8:班長 7:副班長 6:關懷員 4:班長班員 3:副班長班員 2:關懷員班員 1:一般班員 0:暫停班員',";
        $sql.="`STU_ID`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員代號',";
        $sql.="`staffid`	 varchar(12) collate utf8_unicode_ci COMMENT '職員代號',";
        $sql.="`sex`	       varchar(2) collate utf8_unicode_ci COMMENT '性別',";
        $sql.="`age`	       date default '1970-01-01' COMMENT '年齡',";
        $sql.="`edu`	       varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學歷',";
        $sql.="`school`	       varchar(60) collate utf8_unicode_ci NOT NULL COMMENT '學校',";
        $sql.="`jobtype`	 varchar(40) collate utf8_unicode_ci NOT NULL COMMENT '職業別',";
        $sql.="`company`	 varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '公司',";
        $sql.="`jobtitle`	 varchar(80) collate utf8_unicode_ci NOT NULL COMMENT '職稱',";
        $sql.="`PhoneNo_H`     varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(宅)',";
        $sql.="`PhoneNo_C`     varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '電話(手機)',";
        $sql.="`memberseq`     int(8) default 0 COMMENT '排序',";
        $sql.="`day`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`traff`         varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffCnt`      int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`traffReal`     varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '搭車方式-逗點分隔',";
        $sql.="`traffRealCnt`  int(4) default 0 COMMENT '0:來回, 1:單去,2:單回',";
        $sql.="`cost`          int(4) default 0 COMMENT '車資',";
        $sql.="`pay`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid`	 varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`memo`	       varchar(80) collate utf8_unicode_ci COMMENT '備註',";
        $sql.="`leaderinfo`    varchar(255) collate utf8_unicode_ci COMMENT '擔任班幹部資訊',";
        $sql.="`volunteerinfo` varchar(200) collate utf8_unicode_ci COMMENT '擔任義工資訊',";
        $sql.="`otherinfo`     varchar(120) collate utf8_unicode_ci COMMENT '其他額外資訊',";// 幹部母班....
        $sql.="`classfullname` varchar(20) collate utf8_unicode_ci COMMENT  '班級全名',";// 幹部母班....
        $sql.="`joinmode`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="PRIMARY KEY  (`idx`)";
        $sql.=")ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32;";

        //echo $sql;
        $sql_result=mysqli_query($con, $sql);
        mysqli_commit($con);
        return true;
    }

    // 多場次5
    function chkpujadb($tbname){
        if ($tbname==""){return false;}
        $sql="SHOW TABLES LIKE '".$tbname."'";

        $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
        $result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($result);
        if ($numrows>=1){
            mysqli_close($con);
            return true;
        }

        $sql ="CREATE TABLE IF NOT EXISTS `".$tbname."`(";
        $sql.="`idx`           int(8) NOT NULL auto_increment,";
        $sql.="`name`	       varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學員姓名',";
        $sql.="`classname`     varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '班級',";
        $sql.="`CLS_ID`	       varchar(20)  collate utf8_unicode_ci NOT NULL COMMENT '班級ID',";
        $sql.="`area`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域',";
        $sql.="`areaid`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域ID',";
        $sql.="`title`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`TTL_ID`	       varchar(4) collate utf8_unicode_ci  NOT NULL COMMENT '學員職稱',";
        $sql.="`titleid`	 int(4) default 0 COMMENT '學員類別 8:班長 7:副班長 6:關懷員 4:班長班員 3:副班長班員 2:關懷員班員 1:一般班員 0:暫停班員',";
        $sql.="`STU_ID`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員代號',";
        $sql.="`staffid`	 varchar(12) collate utf8_unicode_ci COMMENT '職員代號',";
        $sql.="`sex`	       varchar(2) collate utf8_unicode_ci COMMENT '性別',";
        $sql.="`age`	       date default '1970-01-01' COMMENT '年齡',";
        $sql.="`edu`	       varchar(20) collate utf8_unicode_ci COMMENT '學歷',";
        $sql.="`school`	       varchar(60) collate utf8_unicode_ci COMMENT '學校',";
        $sql.="`jobtype`	 varchar(40) collate utf8_unicode_ci COMMENT '職業別',";
        $sql.="`company`	 varchar(80) collate utf8_unicode_ci COMMENT '公司',";
        $sql.="`jobtitle`	 varchar(80) collate utf8_unicode_ci COMMENT '職稱',";
        $sql.="`PhoneNo_H`     varchar(20) collate utf8_unicode_ci COMMENT '電話(宅)',";
        $sql.="`PhoneNo_C`     varchar(20) collate utf8_unicode_ci COMMENT '電話(手機)',";
        $sql.="`memberseq`     int(8) default 0 COMMENT '排序',"; //以上為學員基本資料
        $sql.="`leaderinfo`    varchar(120) collate utf8_unicode_ci COMMENT '擔任班幹部資訊',";
        $sql.="`volunteerinfo` varchar(80) collate utf8_unicode_ci COMMENT '擔任義工資訊',";
        $sql.="`otherinfo`     varchar(120) collate utf8_unicode_ci COMMENT '其他額外資訊',";// 幹部母班....
        $sql.="`classfullname` varchar(20) collate utf8_unicode_ci COMMENT  '班級全名',";// 幹部母班....

        //0,0,0,0,0,0,0,'',0,0,0,'1970-01-01','1970-01-01',0,'','',''
        $sql.="`lock1`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day1`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal1`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family1`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service1`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode1`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase1`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff1`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost1`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay1`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend1`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate1`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate1`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround1`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid1`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname1`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel1`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo1`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo1`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock2`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day2`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal2`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family2`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service2`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode2`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase2`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff2`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost2`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay2`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend2`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate2`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate2`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround2`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid2`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname2`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel2`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo2`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo2`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock3`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day3`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal3`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family3`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service3`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode3`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase3`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff3`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost3`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay3`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend3`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate3`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate3`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround3`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid3`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname3`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel3`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo3`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo3`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock4`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day4`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal4`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family4`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service4`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode4`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase4`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff4`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost4`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay4`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend4`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate4`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate4`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround4`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid4`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname4`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel4`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo4`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo4`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock5`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day5`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal5`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family5`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service5`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode5`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase5`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff5`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost5`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay5`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend5`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate5`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate5`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround5`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid5`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname5`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel5`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo5`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo5`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="PRIMARY KEY  (`idx`)";
        $sql.=")ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32;";

         //echo $sql;
        $sql_result=mysqli_query($con, $sql);
        mysqli_commit($con);
        mysqli_close($con);
        return true;
    }

    // 多場次 8
    function chkpujadbx($tbname){
        if ($tbname==""){return false;}
        $sql="SHOW TABLES LIKE '".$tbname."'";

        $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
        $result = mysqli_query($con, $sql);
        $numrows = mysqli_num_rows($result);
        if ($numrows>=1){
            mysqli_close($con);
            return true;
        }

        $sql ="CREATE TABLE IF NOT EXISTS `".$tbname."`(";
        $sql.="`idx`           int(8) NOT NULL auto_increment,";
        $sql.="`name`	       varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '學員姓名',";
        $sql.="`classname`     varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '班級',";
        $sql.="`CLS_ID`	       varchar(20)  collate utf8_unicode_ci NOT NULL COMMENT '班級ID',";
        $sql.="`area`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域',";
        $sql.="`areaid`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '班級區域ID',";
        $sql.="`title`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員職稱',";
        $sql.="`TTL_ID`	       varchar(4) collate utf8_unicode_ci  NOT NULL COMMENT '學員職稱',";
        $sql.="`titleid`	 int(4) default 0 COMMENT '學員類別 8:班長 7:副班長 6:關懷員 4:班長班員 3:副班長班員 2:關懷員班員 1:一般班員 0:暫停班員',";
        $sql.="`STU_ID`	       varchar(12) collate utf8_unicode_ci NOT NULL COMMENT '學員代號',";
        $sql.="`staffid`	 varchar(12) collate utf8_unicode_ci COMMENT '職員代號',";
        $sql.="`sex`	       varchar(2) collate utf8_unicode_ci COMMENT '性別',";
        $sql.="`age`	       date default '1970-01-01' COMMENT '年齡',";
        $sql.="`edu`	       varchar(20) collate utf8_unicode_ci COMMENT '學歷',";
        $sql.="`school`	       varchar(60) collate utf8_unicode_ci COMMENT '學校',";
        $sql.="`jobtype`	 varchar(40) collate utf8_unicode_ci COMMENT '職業別',";
        $sql.="`company`	 varchar(80) collate utf8_unicode_ci COMMENT '公司',";
        $sql.="`jobtitle`	 varchar(80) collate utf8_unicode_ci COMMENT '職稱',";
        $sql.="`PhoneNo_H`     varchar(20) collate utf8_unicode_ci COMMENT '電話(宅)',";
        $sql.="`PhoneNo_C`     varchar(20) collate utf8_unicode_ci COMMENT '電話(手機)',";
        $sql.="`memberseq`     int(8) default 0 COMMENT '排序',"; //以上為學員基本資料
        $sql.="`leaderinfo`    varchar(120) collate utf8_unicode_ci COMMENT '擔任班幹部資訊',";
        $sql.="`volunteerinfo` varchar(80) collate utf8_unicode_ci COMMENT '擔任義工資訊',";
        $sql.="`otherinfo`     varchar(120) collate utf8_unicode_ci COMMENT '其他額外資訊',";// 幹部母班....
        $sql.="`classfullname` varchar(20) collate utf8_unicode_ci COMMENT  '班級全名',";// 幹部母班....

        //0,0,0,0,0,0,0,'',0,0,0,'1970-01-01','1970-01-01',0,'','',''
        $sql.="`lock1`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day1`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal1`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family1`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service1`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode1`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase1`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff1`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost1`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay1`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend1`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate1`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate1`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround1`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid1`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname1`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel1`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo1`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo1`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock2`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day2`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal2`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family2`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service2`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode2`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase2`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff2`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost2`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay2`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend2`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate2`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate2`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround2`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid2`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname2`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel2`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo2`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo2`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock3`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day3`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal3`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family3`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service3`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode3`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase3`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff3`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost3`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay3`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend3`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate3`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate3`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround3`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid3`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname3`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel3`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo3`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo3`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock4`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day4`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal4`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family4`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service4`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode4`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase4`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff4`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost4`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay4`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend4`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate4`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate4`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround4`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid4`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname4`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel4`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo4`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo4`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock5`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day5`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal5`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family5`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service5`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode5`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase5`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff5`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost5`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay5`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend5`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate5`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate5`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround5`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid5`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname5`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel5`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo5`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo5`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock6`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day6`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal6`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family6`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service6`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode6`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase6`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff6`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost6`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay6`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend6`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate6`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate6`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround6`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid6`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname6`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel6`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo6`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo6`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock7`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day7`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal7`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family7`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service7`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode7`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase7`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff7`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost7`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay7`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend7`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate7`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate7`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround7`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid7`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname7`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel7`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo7`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo7`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="`lock8`          int(4) default 0 COMMENT '已繳費-鎖住',";
        $sql.="`day8`           int(8) default 0 COMMENT '參加第幾天87654321',";
        $sql.="`meal8`          int(8) default 0 COMMENT '代訂便當87654321',";
        $sql.="`family8`        int(8) default 0 COMMENT '眷屬參加人數',";
        $sql.="`service8`       int(8) default 0 COMMENT '是否為義工',";
        $sql.="`joinmode8`      int(8) default 0 COMMENT '參加(正行重培..)',";
        $sql.="`specialcase8`   int(8) default 0 COMMENT '住宿特殊需求',";
        $sql.="`traff8`         varchar(12) collate utf8_unicode_ci COMMENT '搭車方式-逗點分隔',";//traff,traffCnt,traffReal,traffRealCnt
        $sql.="`cost8`          int(8) default 0 COMMENT '車資',";
        $sql.="`pay8`           int(4) default 0 COMMENT '繳費',";
        $sql.="`attend8`        int(4) default 0 COMMENT '報到',";
        $sql.="`regdate8`       date default '1970-01-01' COMMENT '報名日期',";
        $sql.="`paydate8`       date default '1970-01-01' COMMENT '繳費日期',";
        $sql.="`payround8`      int(8) default 0 COMMENT '第幾次繳費',";
        $sql.="`paybyid8`	  varchar(8) collate utf8_unicode_ci COMMENT 'id',";
        $sql.="`paybyname8`     varchar(20) collate utf8_unicode_ci COMMENT 'name',";
        $sql.="`cancel8`        int(8) collate utf8_unicode_ci COMMENT '退票',";
        $sql.="`cancelinfo8`    varchar(80) collate utf8_unicode_ci COMMENT '退票前的info',";
        $sql.="`memo8`	        varchar(80) collate utf8_unicode_ci COMMENT '備註',";

        $sql.="PRIMARY KEY  (`idx`)";
        $sql.=")ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32;";

        //echo $sql;
        $sql_result=mysqli_query($con, $sql);
        mysqli_commit($con);
        mysqli_close($con);
        return true;
    }

    function getTaffic($trafftbname, $day, $traffdesc){
        $traffday="";
        if ($traffdesc==""||$traffdesc==null){
            $sql="select * from `".$trafftbname."` WHERE `day`=".$day." ORDER BY `traffid` ASC";
        }else{
            $sql="select * from `".$trafftbname."` WHERE (`day`=".$day." AND `traffdesc`='".$traffdesc."')  ORDER BY `traffid` ASC";
        }
        $con = mysqli_connect("localhost","root","rinpoche", "bwsouthdb");
        $sql_traff_result=mysqli_query($con, $sql);
        $numrows=mysqli_num_rows($sql_traff_result);
        if ($numrows<=0){
            $traffday="Z|自往";
            //mysqli_close($con);
            return $traffday;
        }
        while($row = mysqli_fetch_array($sql_traff_result, MYSQLI_ASSOC)) {
            if ($traffday==""){$traffday.=($row["traffid"]."|".$row["traffname"]);}
            else{$traffday.=("|".$row["traffid"]."|".$row["traffname"]);}
        }
        //mysqli_close($con);
        return $traffday;
    }

    function getStaffid($studentid){
        $staffid="";
        if($studentid==""){return $staffid;}
        $sql="select * from `staffinfo` where `STU_ID`='".$studentid."';";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        if($numrows<=0){return $staffid;}
        $row=mysql_fetch_array($sql_result, MYSQL_ASSOC);
        $staffid=$row["STF_ID"];
        return $staffid;
    }

    function getClassInfo(){
        $sql="select CLS_ID,Class,RegionCode from `sdm_classes` where (`IsCurrent`=1)";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        if($numrows<=0){return;}
        $cnt=0;
        while($row=mysql_fetch_array($sql_result, MYSQL_NUM))//MYSQL_NUM))//MYSQL_ASSOC))
        {
            $classinfo[$cnt][0]=$row[0];
            $classinfo[$cnt][1]=$row[1];
            $classinfo[$cnt][2]=$row[2];
            $cnt++;
        }
        return $classinfo;
    }

    function getLeaderInfo($studentid,$findmain){
        if($studentid==""){return ;}
        if ($findmain==true){$sql="select CLS_ID,TTL_ID from sdm_clsmembers where (`STU_ID`='".$studentid."' AND `Status`='參與')";}
        else{$sql="select CLS_ID,TTL_ID from sdm_clsmembers where (`STU_ID`='".$studentid."' AND `Status`='參與' AND `IsMajor`='否')";}
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        if($numrows<=0){return;}
        $cnt=0;if ($findmain==true){$cnt=1;}
        while($row=mysql_fetch_array($sql_result, MYSQL_NUM))//MYSQL_NUM))//MYSQL_ASSOC))
        {
            if($row[1]=="TB05"||$row[1]=="TB06")
            {
                $leaderinfo[0][0]=$row[0];
                $leaderinfo[0][1]="班員";
                $leaderinfo[0][2]="";
                $leaderinfo[0][3]="";
                $leaderinfo[0][4]="";
                continue;
            }
            $leaderinfo[$cnt][0]=$row[0];
            if($row[1]=="TB02"){$leaderinfo[$cnt][1]="班長";}
            else if($row[1]=="TB03"){$leaderinfo[$cnt][1]="副班長";}
            else if($row[1]=="TB04"){$leaderinfo[$cnt][1]="關懷員";}

            $leaderinfo[$cnt][2]="";
            $leaderinfo[$cnt][3]="";
            $leaderinfo[$cnt][4]="";
            $cnt++;
        }
        return $leaderinfo;
    }

    function syncLeaderPujaTB($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname,$mode) // 幹部 TB01, TB02, TB03
    {
        // 從sdm_clsmembers找出該班幹部出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否')";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;}}

        // 從法會報名表中找出該班幹部出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid>=5)";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja))
        {
            for($i=0;$i<count($memberinList);$i++)
            {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){if ($memberinList[$i][0]==$memberinPuja[$j][0]){$matchcnt++;$find=true;break;}}
                if ($find==false){break;}
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            for($j=0;$j<count($memberinPuja);$j++)
            {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }

        //$sql="select A.STU_ID,A.MembersSeq,B.idx,B.seq from `sdm_clsmembers` A LEFT JOIN `".$tbname."` B ON A.STU_ID=B.studentid WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否' AND B.CLS_ID='".$clsid."' AND B.titleid>=5)";
        //$sql_result=mysql_query($sql);
        //$numrows=mysql_num_rows($sql_result);
        //$cnt=0;
        //if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinAll[$cnt][0]=$row[0];$memberinAll[$cnt][1]=$row[1];$memberinAll[$cnt][2]=$row[2];$memberinAll[$cnt][3]=$row[3];$cnt++;}}

        //echo "<br>in all ";	for($i=0;$i<count($memberinAll);$i++){echo "<br>".$memberinAll[$i][0];}
        //echo "<br>in cls member";for($i=0;$i<count($memberinList);$i++){echo "<br>".$memberinList[$i][0];}
        //echo "<br>in rollcall ";for($i=0;$i<count($memberinPuja);$i++){echo "<br>".$memberinPuja[$i][0];}

        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinPuja);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }
        //echo "<br>cls leader none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}
        //echo "<br>puja leader none ";	for($i=0;$i<count($rollcallnone);$i++){echo "<br>".$rollcallnone[$i];}

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);

        $sql_update="";
        // in clsmember but not in class join table -> 1.新幹部(insert)
        if (count($clsmembernone)>0&&$findleaderinfo==true){$classinfo=getClassInfo();}
        for($i=0;$i<count($clsmembernone);$i++)
        {
            $sqlstudent="select * from `sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysql_query($sqlstudent);
            $numrows=mysql_num_rows($sqlstudent_result);
            if ($numrows>0)
            {
                $row_stu=mysql_fetch_array($sqlstudent_result, MYSQL_ASSOC);
                //echo "<br>".$clsmembernone[$i][0]."(加入)"; // Insert into
                $titleid=0;
                $title="";
                $staffid=getStaffid($clsmembernone[$i][0]);
                if($clsmembernone[$i][2]=="TB02"){$titleid=8;$title="班長";}
                else if($clsmembernone[$i][2]=="TB03"){$titleid=7;$title="副班長";}
                else if($clsmembernone[$i][2]=="TB04"){$titleid=6;$title="關懷員";}
                else{continue;}

                $leader="";
                $leadermain="";// 幹部母班
                if($findleaderinfo==true)
                {
                    $leaderinfo=getLeaderInfo($clsmembernone[$i][0],true);
                    for($w=0;$w<count($leaderinfo);$w++)
                    {
                        for($x=0;$x<count($classinfo);$x++)
                        {
                            if($leaderinfo[$w][0]==$classinfo[$x][0])
                            {
                                $leaderinfo[$w][2]=$classinfo[$x][1];
                                $leaderinfo[$w][3]=$classinfo[$x][2];
                                break;
                            }
                        }
                    }
                    for($w=1;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
                    if(count($leaderinfo)>1){$leadermain=$leaderinfo[0][2];}
                }

                if($mode==1)
                {
                    $sql_update.="insert into `".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                    $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
                    $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                    $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','".$leadermain."','".$classfullname."',0,0);";
                }
                else
                {
                    $sql_update.="insert into `".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                    $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
                    $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                    $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','".$leadermain."','".$classfullname."');";
                }
            }
        }
        // in rollcall but not in join table -> 1.幹部離開(insert)
        for($i=0;$i<count($pujanone);$i++)
        {
            $sql_update.="update ".$tbname." set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `CLS_ID`='".$clsid."' AND `titleid`>=5);";
        }
        //echo $sql_update;
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd) > 0)
        {
            mysql_query("SET autocommit=0");
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        $sql_leaderstatus="";
        // 一拼更新幹部在母班的 title id => 一起更新護持的班級
        for($i=0;$i<count($memberinList);$i++)
        {
            if($memberinList[$i][2]=="TB02"){$titleid=4;}else if($memberinList[$i][2]=="TB03"){$titleid=3;}else if($memberinList[$i][2]=="TB04"){$titleid=2;}else{continue;}
            $sqltmp="select idx,titleid from `".$tbname."` where (`STU_ID`='".$memberinList[$i][0]."' AND `titleid`<".$titleid.");";
            $sqltmp_result=mysql_query($sqltmp);
            $numrows=mysql_num_rows($sqltmp_result);
            if ($numrows<=0){continue;}
            $row_tmp=mysql_fetch_array($sqltmp_result, MYSQL_NUM);
            //echo $sqltmp."<br>";
            if ($row_tmp[1]==$titleid){continue;}
            $sql_leaderstatus.="update ".$tbname." set `titleid`=".$titleid." where (`idx`=".$row_tmp[0].");";
            //echo "<br>".$memberinList[$i][0];
        }
        $sqlcmdex=explode(";",$sql_leaderstatus);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        // 一併更新幹部的 memberseq
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++)
        {
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `seq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        return true;
    }

    function syncMemberPujaTB($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname,$mode) // 班員 TB05, TB06
    {
        //從sdm_clsmembers找出該班學員出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='是')";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;}}

        // 從法會報名表中找出該班學員出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid<5)";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja))
        {
            for($i=0;$i<count($memberinList);$i++)
            {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){if ($memberinList[$i][0]==$memberinPuja[$j][0]){$matchcnt++;$find=true;break;}}
                if ($find==false){break;}
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            for($j=0;$j<count($memberinPuja);$j++)
            {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }
        //$sql="select A.STU_ID,A.MembersSeq,B.idx,B.seq from `sdm_clsmembers` A LEFT JOIN `".$tbname."` B ON A.STU_ID=B.STU_ID WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='是' AND B.CLS_ID='".$clsid."' AND B.titleid<5)";
        //$sql_result=mysql_query($sql);
        //$numrows=mysql_num_rows($sql_result);
        //$cnt=0;
        //if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinAll[$cnt][0]=$row[0];$memberinAll[$cnt][1]=$row[1];$memberinAll[$cnt][2]=$row[2];$memberinAll[$cnt][3]=$row[3];$cnt++;}}

        //echo "<br>in all ";	for($i=0;$i<count($memberinAll);$i++){echo "<br>".$memberinAll[$i][0];}
        //echo "<br>in cls member";for($i=0;$i<count($memberinList);$i++){echo "<br>".$memberinList[$i][0];}
        //echo "<br>in rollcall ";for($i=0;$i<count($memberinPuja);$i++){echo "<br>".$memberinPuja[$i][0];}

        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinPuja);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);
        $sql_update="";
        // in clsmember but not in class join table -> 1.在別班?(YES:換班), 2.新學員(insert)
        //echo "<br>cls member none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}

        // 1.在別班?(YES:換班)
        if (count($clsmembernone)>0&&$findleaderinfo==true){$classinfo=getClassInfo();}
        for($i=0;$i<count($clsmembernone);$i++)
        {
            $sqltemp="select A.STU_ID from `".$tbname."` A WHERE (A.STU_ID='".$clsmembernone[$i][0]."' AND A.titleid<5)";//echo $sqltemp;
            $sqltemp_result=mysql_query($sqltemp);
            $numrows=mysql_num_rows($sqltemp_result);
            if ($numrows>0)//在別班=>換到目前的班級
            {
                $sql_update.="update `".$tbname."` set `CLS_ID`='".$clsid."',`classname`='".$clsname."',`memberseq`=".$clsmembernone[$i][1]." where (`STU_ID`='".$clsmembernone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            // 法會報名表內也無此學員 => 新加入的學員,找出學員基本資料再 insert 到法會報名表
            $sqlstudent="select * from `sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysql_query($sqlstudent);
            $numrows=mysql_num_rows($sqlstudent_result);
            if ($numrows<=0){continue;}
            $row_stu=mysql_fetch_array($sqlstudent_result, MYSQL_ASSOC);

            $leader="";
            if($findleaderinfo==true)
            {
                $leaderinfo=getLeaderInfo($clsmembernone[$i][0],false);
                for($w=0;$w<count($leaderinfo);$w++)
                {
                    for($x=0;$x<count($classinfo);$x++)
                    {
                        if($leaderinfo[$w][0]==$classinfo[$x][0])
                        {
                            $leaderinfo[$w][2]=$classinfo[$x][1];
                            $leaderinfo[$w][3]=$classinfo[$x][2];
                            break;
                        }
                    }
                }
                for($w=0;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
            }

            $titleid=1;
            $title="班員";
            $staffid=getStaffid($clsmembernone[$i][0]);
            if($clsmembernone[$i][2]=="TB06"){$titleid=0;}

            if($mode==1)
            {
                $sql_update.="insert into `".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."',0,0);";
            }
            else
            {
                $sql_update.="insert into `".$tbname."` values (NULL,0,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."','".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."','".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="0,0,0,0,'Z',0,'Z',0,0,0,0,'1970-01-01','1970-01-01',0,'','','','".$leader."','','','".$classfullname."');";
            }
        }
        //echo $sql_update;echo "<br>";return;

        // in puja but not in join table -> 1.調到別班?(YES:換班), 2.離開(insert)
        for($i=0;$i<count($pujanone);$i++)
        {
            $sqltemp="select * from `sdm_clsmembers` A WHERE (A.STU_ID='".$pujanone[$i][0]."' AND A.IsMajor='是')";
            $sqltemp_result=mysql_query($sqltemp);
            $numrows=mysql_num_rows($sqltemp_result);
            if ($numrows<=0)//找不到此人....休學或離開
            {
                $sql_update.="update `".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            $row=mysql_fetch_array($sqltemp_result, MYSQL_ASSOC);
            if($row["Status"]=="離開")
            {
                $sql_update.="update `".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                //echo "<br>".$pujanone[$i][0]."(離開)";// update CLS_ID, Name and vailddate ad empty
            }
            else if ($row["CLS_ID"]!=$clsid)
            {
                $sqlclasses="select Class from `sdm_classes` WHERE (`CLS_ID`='".$row["CLS_ID"]."')";
                $sqlclasses_result=mysql_query($sqlclasses);
                $numrows=mysql_num_rows($sqlclasses_result);
                $classname="";
                if ($numrows>0)
                {
                    $row_classes=mysql_fetch_array($sqlclasses_result, MYSQL_ASSOC);
                    $tmp=explode("-",$row_classes["Class"]);
                    $classname=$tmp[0];
                }
                $sql_update.="update `".$tbname."` set `CLS_ID`='".$row["CLS_ID"]."',`classname`='".$classname."' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                // find the class name from sdm_classes
                // echo "<br>".$pujanone[$i][0]."(調出)";// update CLS_ID, Name and vailddate
            }
        }
        //echo $sql_update;//echo "<br>";
        //echo "<br>rollcall none ";	for($i=0;$i<count($pujanone);$i++){echo "<br>".$pujanone[$i][0];}

        // change something
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd)>0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        // 一併更新學員的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++)
        {
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `memberseq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }
        return true;
    }

    // 多場次 5
    function syncLeaderpujadb($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname) // 幹部 TB01, TB02, TB03
    {
        // 從sdm_clsmembers找出該班幹部出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否')";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;}}

        // 從法會報名表中找出該班幹部出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid>=5)";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja))
        {
            for($i=0;$i<count($memberinList);$i++)
            {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){if ($memberinList[$i][0]==$memberinPuja[$j][0]){$matchcnt++;$find=true;break;}}
                if ($find==false){break;}
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            for($j=0;$j<count($memberinPuja);$j++)
            {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }

        //$sql="select A.STU_ID,A.MembersSeq,B.idx,B.seq from `sdm_clsmembers` A LEFT JOIN `".$tbname."` B ON A.STU_ID=B.studentid WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否' AND B.CLS_ID='".$clsid."' AND B.titleid>=5)";
        //$sql_result=mysql_query($sql);
        //$numrows=mysql_num_rows($sql_result);
        //$cnt=0;
        //if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinAll[$cnt][0]=$row[0];$memberinAll[$cnt][1]=$row[1];$memberinAll[$cnt][2]=$row[2];$memberinAll[$cnt][3]=$row[3];$cnt++;}}

        //echo "<br>in all ";	for($i=0;$i<count($memberinAll);$i++){echo "<br>".$memberinAll[$i][0];}
        //echo "<br>in cls member";for($i=0;$i<count($memberinList);$i++){echo "<br>".$memberinList[$i][0];}
        //echo "<br>in rollcall ";for($i=0;$i<count($memberinPuja);$i++){echo "<br>".$memberinPuja[$i][0];}

        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinPuja);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }
        //echo "<br>cls leader none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}
        //echo "<br>puja leader none ";	for($i=0;$i<count($rollcallnone);$i++){echo "<br>".$rollcallnone[$i];}

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);

        $sql_update="";
        $dayitem="0,0,0,0,0,0,0,'Z,0,Z,0',0,0,0,'1970-01-01','1970-01-01',0,'','',0,'',''";
        // in clsmember but not in class join table -> 1.新幹部(insert)
        if (count($clsmembernone)>0&&$findleaderinfo==true){$classinfo=getClassInfo();}
        for($i=0;$i<count($clsmembernone);$i++)
        {
            $sqlstudent="select * from `sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysql_query($sqlstudent);
            $numrows=mysql_num_rows($sqlstudent_result);
            if ($numrows>0)
            {
                $row_stu=mysql_fetch_array($sqlstudent_result, MYSQL_ASSOC);
                //echo "<br>".$clsmembernone[$i][0]."(加入)"; // Insert into
                $titleid=0;
                $title="";
                $staffid=getStaffid($clsmembernone[$i][0]);
                if($clsmembernone[$i][2]=="TB02"){$titleid=8;$title="班長";}
                else if($clsmembernone[$i][2]=="TB03"){$titleid=7;$title="副班長";}
                else if($clsmembernone[$i][2]=="TB04"){$titleid=6;$title="關懷員";}
                else{continue;}

                $leader="";
                $leadermain="";// 幹部母班
                if($findleaderinfo==true)
                {
                    $leaderinfo=getLeaderInfo($clsmembernone[$i][0],true);
                    for($w=0;$w<count($leaderinfo);$w++)
                    {
                        for($x=0;$x<count($classinfo);$x++)
                        {
                            if($leaderinfo[$w][0]==$classinfo[$x][0])
                            {
                                $leaderinfo[$w][2]=$classinfo[$x][1];
                                $leaderinfo[$w][3]=$classinfo[$x][2];
                                break;
                            }
                        }
                    }
                    for($w=1;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
                    if(count($leaderinfo)>1){$leadermain=$leaderinfo[0][2];}
                }

                // INSERT COMMAND
                $sql_update.="insert into `".$tbname."` values (NULL,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."',";
                $sql_update.="'".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."',";
                $sql_update.="'".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="'".$leader."','','".$leadermain."','".$classfullname."',";
                $sql_update.=$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.");";
            }
        }
        // in rollcall but not in join table -> 1.幹部離開(insert)
        for($i=0;$i<count($pujanone);$i++)
        {
            $sql_update.="update ".$tbname." set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `CLS_ID`='".$clsid."' AND `titleid`>=5);";
        }
        //echo $sql_update;
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd) > 0)
        {
            mysql_query("SET autocommit=0");
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        $sql_leaderstatus="";
        // 一拼更新幹部在母班的 title id => 一起更新護持的班級
        for($i=0;$i<count($memberinList);$i++)
        {
            if($memberinList[$i][2]=="TB02"){$titleid=4;}else if($memberinList[$i][2]=="TB03"){$titleid=3;}else if($memberinList[$i][2]=="TB04"){$titleid=2;}else{continue;}
            $sqltmp="select idx,titleid from `".$tbname."` where (`STU_ID`='".$memberinList[$i][0]."' AND `titleid`<".$titleid.");";
            $sqltmp_result=mysql_query($sqltmp);
            $numrows=mysql_num_rows($sqltmp_result);
            if ($numrows<=0){continue;}
            $row_tmp=mysql_fetch_array($sqltmp_result, MYSQL_NUM);
            //echo $sqltmp."<br>";
            if ($row_tmp[1]==$titleid){continue;}
            $sql_leaderstatus.="update ".$tbname." set `titleid`=".$titleid." where (`idx`=".$row_tmp[0].");";
            //echo "<br>".$memberinList[$i][0];
        }
        $sqlcmdex=explode(";",$sql_leaderstatus);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        // 一併更新幹部的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++)
        {
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `seq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        return true;
    }

    function syncMemberpujadb($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname) // 班員 TB05, TB06
    {
        //從sdm_clsmembers找出該班學員出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='是')";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;}}

        // 從法會報名表中找出該班學員出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid<5)";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja))
        {
            for($i=0;$i<count($memberinList);$i++)
            {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){if ($memberinList[$i][0]==$memberinPuja[$j][0]){$matchcnt++;$find=true;break;}}
                if ($find==false){break;}
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            for($j=0;$j<count($memberinPuja);$j++)
            {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }
        //$sql="select A.STU_ID,A.MembersSeq,B.idx,B.seq from `sdm_clsmembers` A LEFT JOIN `".$tbname."` B ON A.STU_ID=B.STU_ID WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='是' AND B.CLS_ID='".$clsid."' AND B.titleid<5)";
        //$sql_result=mysql_query($sql);
        //$numrows=mysql_num_rows($sql_result);
        //$cnt=0;
        //if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinAll[$cnt][0]=$row[0];$memberinAll[$cnt][1]=$row[1];$memberinAll[$cnt][2]=$row[2];$memberinAll[$cnt][3]=$row[3];$cnt++;}}

        //echo "<br>in all ";	for($i=0;$i<count($memberinAll);$i++){echo "<br>".$memberinAll[$i][0];}
        //echo "<br>in cls member";for($i=0;$i<count($memberinList);$i++){echo "<br>".$memberinList[$i][0];}
        //echo "<br>in rollcall ";for($i=0;$i<count($memberinPuja);$i++){echo "<br>".$memberinPuja[$i][0];}

        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinPuja);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);
        $sql_update="";
        // in clsmember but not in class join table -> 1.在別班?(YES:換班), 2.新學員(insert)
        //echo "<br>cls member none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}

        // 1.在別班?(YES:換班) - traff,traffCnt,traffReal,traffRealCnt
        $dayitem="0,0,0,0,0,0,0,'Z,0,Z,0',0,0,0,'1970-01-01','1970-01-01',0,'','',0,'',''";
        if (count($clsmembernone)>0&&$findleaderinfo==true){$classinfo=getClassInfo();}
        for($i=0;$i<count($clsmembernone);$i++)
        {
            $sqltemp="select A.STU_ID from `".$tbname."` A WHERE (A.STU_ID='".$clsmembernone[$i][0]."' AND A.titleid<5)";//echo $sqltemp;
            $sqltemp_result=mysql_query($sqltemp);
            $numrows=mysql_num_rows($sqltemp_result);
            if ($numrows>0)//在別班=>換到目前的班級
            {
                $sql_update.="update `".$tbname."` set `CLS_ID`='".$clsid."',`classname`='".$clsname."',`memberseq`=".$clsmembernone[$i][1]." where (`STU_ID`='".$clsmembernone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            // 法會報名表內也無此學員 => 新加入的學員,找出學員基本資料再 insert 到法會報名表
            $sqlstudent="select * from `sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysql_query($sqlstudent);
            $numrows=mysql_num_rows($sqlstudent_result);
            if ($numrows<=0){continue;}
            $row_stu=mysql_fetch_array($sqlstudent_result, MYSQL_ASSOC);

            $leader="";
            if($findleaderinfo==true)
            {
                $leaderinfo=getLeaderInfo($clsmembernone[$i][0],false);
                for($w=0;$w<count($leaderinfo);$w++)
                {
                    for($x=0;$x<count($classinfo);$x++)
                    {
                        if($leaderinfo[$w][0]==$classinfo[$x][0])
                        {
                            $leaderinfo[$w][2]=$classinfo[$x][1];
                            $leaderinfo[$w][3]=$classinfo[$x][2];
                            break;
                        }
                    }
                }
                for($w=0;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
            }

            $titleid=1;
            $title="班員";
            $staffid=getStaffid($clsmembernone[$i][0]);
            if($clsmembernone[$i][2]=="TB06"){$titleid=0;}

            // INSERT COMMAND
            $sql_update.="insert into `".$tbname."` values (NULL,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
            $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."',";
            $sql_update.="'".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
            $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."',";
            $sql_update.="'".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
            $sql_update.="'".$leader."','','','".$classfullname."',";
            $sql_update.=$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.");";
        }
        //echo $sql_update;echo "<br>";return;

        // in puja but not in join table -> 1.調到別班?(YES:換班), 2.離開(insert)
        for($i=0;$i<count($pujanone);$i++)
        {
            $sqltemp="select * from `sdm_clsmembers` A WHERE (A.STU_ID='".$pujanone[$i][0]."' AND A.IsMajor='是')";
            $sqltemp_result=mysql_query($sqltemp);
            $numrows=mysql_num_rows($sqltemp_result);
            if ($numrows<=0)//找不到此人....休學或離開
            {
                $sql_update.="update `".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            $row=mysql_fetch_array($sqltemp_result, MYSQL_ASSOC);
            if($row["Status"]=="離開")
            {
                $sql_update.="update `".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                //echo "<br>".$pujanone[$i][0]."(離開)";// update CLS_ID, Name and vailddate ad empty
            }
            else if ($row["CLS_ID"]!=$clsid)
            {
                $sqlclasses="select Class from `sdm_classes` WHERE (`CLS_ID`='".$row["CLS_ID"]."')";
                $sqlclasses_result=mysql_query($sqlclasses);
                $numrows=mysql_num_rows($sqlclasses_result);
                $classname="";
                if ($numrows>0)
                {
                    $row_classes=mysql_fetch_array($sqlclasses_result, MYSQL_ASSOC);
                    $tmp=explode("-",$row_classes["Class"]);
                    $classname=$tmp[0];
                }
                $sql_update.="update `".$tbname."` set `CLS_ID`='".$row["CLS_ID"]."',`classname`='".$classname."' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                // find the class name from sdm_classes
                // echo "<br>".$pujanone[$i][0]."(調出)";// update CLS_ID, Name and vailddate
            }
        }
        //echo $sql_update;//echo "<br>";
        //echo "<br>rollcall none ";	for($i=0;$i<count($pujanone);$i++){echo "<br>".$pujanone[$i][0];}

        // change something
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd)>0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        // 一併更新學員的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++)
        {
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `memberseq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }
        return true;
    }

    // 多場次 8
    function syncLeaderpujadbx($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname) // 幹部 TB01, TB02, TB03
    {
        // 從sdm_clsmembers找出該班幹部出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否')";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;}}

        // 從法會報名表中找出該班幹部出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid>=5)";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja))
        {
            for($i=0;$i<count($memberinList);$i++)
            {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){if ($memberinList[$i][0]==$memberinPuja[$j][0]){$matchcnt++;$find=true;break;}}
                if ($find==false){break;}
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            for($j=0;$j<count($memberinPuja);$j++)
            {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }

        //$sql="select A.STU_ID,A.MembersSeq,B.idx,B.seq from `sdm_clsmembers` A LEFT JOIN `".$tbname."` B ON A.STU_ID=B.studentid WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='否' AND B.CLS_ID='".$clsid."' AND B.titleid>=5)";
        //$sql_result=mysql_query($sql);
        //$numrows=mysql_num_rows($sql_result);
        //$cnt=0;
        //if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinAll[$cnt][0]=$row[0];$memberinAll[$cnt][1]=$row[1];$memberinAll[$cnt][2]=$row[2];$memberinAll[$cnt][3]=$row[3];$cnt++;}}

        //echo "<br>in all ";	for($i=0;$i<count($memberinAll);$i++){echo "<br>".$memberinAll[$i][0];}
        //echo "<br>in cls member";for($i=0;$i<count($memberinList);$i++){echo "<br>".$memberinList[$i][0];}
        //echo "<br>in rollcall ";for($i=0;$i<count($memberinPuja);$i++){echo "<br>".$memberinPuja[$i][0];}

        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinPuja);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }
        //echo "<br>cls leader none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}
        //echo "<br>puja leader none ";	for($i=0;$i<count($rollcallnone);$i++){echo "<br>".$rollcallnone[$i];}

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);

        $sql_update="";
        $dayitem="0,0,0,0,0,0,0,'Z,0,Z,0',0,0,0,'1970-01-01','1970-01-01',0,'','',0,'',''";
        // in clsmember but not in class join table -> 1.新幹部(insert)
        if (count($clsmembernone)>0&&$findleaderinfo==true){$classinfo=getClassInfo();}
        for($i=0;$i<count($clsmembernone);$i++)
        {
            $sqlstudent="select * from `sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysql_query($sqlstudent);
            $numrows=mysql_num_rows($sqlstudent_result);
            if ($numrows>0)
            {
                $row_stu=mysql_fetch_array($sqlstudent_result, MYSQL_ASSOC);
                //echo "<br>".$clsmembernone[$i][0]."(加入)"; // Insert into
                $titleid=0;
                $title="";
                $staffid=getStaffid($clsmembernone[$i][0]);
                if($clsmembernone[$i][2]=="TB02"){$titleid=8;$title="班長";}
                else if($clsmembernone[$i][2]=="TB03"){$titleid=7;$title="副班長";}
                else if($clsmembernone[$i][2]=="TB04"){$titleid=6;$title="關懷員";}
                else{continue;}

                $leader="";
                $leadermain="";// 幹部母班
                if($findleaderinfo==true)
                {
                    $leaderinfo=getLeaderInfo($clsmembernone[$i][0],true);
                    for($w=0;$w<count($leaderinfo);$w++)
                    {
                        for($x=0;$x<count($classinfo);$x++)
                        {
                            if($leaderinfo[$w][0]==$classinfo[$x][0])
                            {
                                $leaderinfo[$w][2]=$classinfo[$x][1];
                                $leaderinfo[$w][3]=$classinfo[$x][2];
                                break;
                            }
                        }
                    }
                    for($w=1;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
                    if(count($leaderinfo)>1){$leadermain=$leaderinfo[0][2];}
                }

                // INSERT COMMAND
                $sql_update.="insert into `".$tbname."` values (NULL,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
                $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."',";
                $sql_update.="'".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
                $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."',";
                $sql_update.="'".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
                $sql_update.="'".$leader."','','".$leadermain."','".$classfullname."',";
                $sql_update.=$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.");";
            }
        }
        // in rollcall but not in join table -> 1.幹部離開(insert)
        for($i=0;$i<count($pujanone);$i++)
        {
            $sql_update.="update ".$tbname." set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `CLS_ID`='".$clsid."' AND `titleid`>=5);";
        }
        //echo $sql_update;
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd) > 0)
        {
            mysql_query("SET autocommit=0");
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        $sql_leaderstatus="";
        // 一拼更新幹部在母班的 title id => 一起更新護持的班級
        for($i=0;$i<count($memberinList);$i++)
        {
            if($memberinList[$i][2]=="TB02"){$titleid=4;}else if($memberinList[$i][2]=="TB03"){$titleid=3;}else if($memberinList[$i][2]=="TB04"){$titleid=2;}else{continue;}
            $sqltmp="select idx,titleid from `".$tbname."` where (`STU_ID`='".$memberinList[$i][0]."' AND `titleid`<".$titleid.");";
            $sqltmp_result=mysql_query($sqltmp);
            $numrows=mysql_num_rows($sqltmp_result);
            if ($numrows<=0){continue;}
            $row_tmp=mysql_fetch_array($sqltmp_result, MYSQL_NUM);
            //echo $sqltmp."<br>";
            if ($row_tmp[1]==$titleid){continue;}
            $sql_leaderstatus.="update ".$tbname." set `titleid`=".$titleid." where (`idx`=".$row_tmp[0].");";
            //echo "<br>".$memberinList[$i][0];
        }
        $sqlcmdex=explode(";",$sql_leaderstatus);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        // 一併更新幹部的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++)
        {
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `seq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo ";<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        return true;
    }

    function syncMemberpujadbx($tbname,$clsname,$clsid,$area,$areaid,$findleaderinfo,$findvolunteerinfo,$classfullname) // 班員 TB05, TB06
    {
        //從sdm_clsmembers找出該班學員出來
        $sql="select A.STU_ID,A.MembersSeq,A.TTL_ID from `sdm_clsmembers` A WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='是')";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinList[$cnt][0]=$row[0];$memberinList[$cnt][1]=$row[1];$memberinList[$cnt][2]=$row[2];$cnt++;}}

        // 從法會報名表中找出該班學員出來
        $sql="select A.STU_ID,A.idx,A.memberseq from `".$tbname."` A WHERE (A.CLS_ID='".$clsid."' AND A.titleid<5)";//echo $sql."<br>";
        $sql_result=mysql_query($sql);
        $numrows=mysql_num_rows($sql_result);
        $cnt=0;
        if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinPuja[$cnt][0]=$row[0];$memberinPuja[$cnt][1]=$row[1];$memberinPuja[$cnt][2]=$row[2];$cnt++;}}

        // 先從數量來判定兩邊的清單是否一樣, 若數量不一樣則要繼續找出差異; 若一樣, 就再比對兩清單是否一樣
        $matchcnt=0;
        if (count($memberinList)==count($memberinPuja))
        {
            for($i=0;$i<count($memberinList);$i++)
            {
                $find=false;
                for($j=0;$j<count($memberinPuja);$j++){if ($memberinList[$i][0]==$memberinPuja[$j][0]){$matchcnt++;$find=true;break;}}
                if ($find==false){break;}
            }
            if (count($memberinList)==$matchcnt){return;}
        }

        // 找出同時存在兩個清單中的名單
        $cnt=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            for($j=0;$j<count($memberinPuja);$j++)
            {
                if ($memberinList[$i][0]!=$memberinPuja[$j][0]){continue;}
                $memberinAll[$cnt][0]=$memberinList[$i][0];
                $memberinAll[$cnt][1]=$memberinList[$i][1];
                $memberinAll[$cnt][2]=$memberinPuja[$j][1];
                $memberinAll[$cnt][3]=$memberinPuja[$j][2];
                $cnt++;
                break;
            }
        }
        //$sql="select A.STU_ID,A.MembersSeq,B.idx,B.seq from `sdm_clsmembers` A LEFT JOIN `".$tbname."` B ON A.STU_ID=B.STU_ID WHERE (A.CLS_ID='".$clsid."' AND A.Status='參與' AND A.IsMajor='是' AND B.CLS_ID='".$clsid."' AND B.titleid<5)";
        //$sql_result=mysql_query($sql);
        //$numrows=mysql_num_rows($sql_result);
        //$cnt=0;
        //if ($numrows>0){while($row=mysql_fetch_array($sql_result, MYSQL_NUM)){$memberinAll[$cnt][0]=$row[0];$memberinAll[$cnt][1]=$row[1];$memberinAll[$cnt][2]=$row[2];$memberinAll[$cnt][3]=$row[3];$cnt++;}}

        //echo "<br>in all ";	for($i=0;$i<count($memberinAll);$i++){echo "<br>".$memberinAll[$i][0];}
        //echo "<br>in cls member";for($i=0;$i<count($memberinList);$i++){echo "<br>".$memberinList[$i][0];}
        //echo "<br>in rollcall ";for($i=0;$i<count($memberinPuja);$i++){echo "<br>".$memberinPuja[$i][0];}

        // 找出在clsmembers中,但不在法會清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinList);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinList[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if($find==false){$clsmembernone[$nonidx][0]=$memberinList[$i][0];$clsmembernone[$nonidx][1]=$memberinList[$i][1];$clsmembernone[$nonidx][2]=$memberinList[$i][2];$nonidx++;}
        }

        // 找出在法會清單中,但不在clsmembers清單中的該班學員
        $nonidx=0;
        for($i=0;$i<count($memberinPuja);$i++)
        {
            $find=false;
            for($j=0;$j<count($memberinAll);$j++){if ($memberinPuja[$i][0]==$memberinAll[$j][0]){$find=true;break;}}
            if ($find==true){continue;}
            $pujanone[$nonidx][0]=$memberinPuja[$i][0];
            $pujanone[$nonidx][1]=$memberinPuja[$i][1];
            $nonidx++;
        }

        $currDate=date('Y-m-d');
        $time=strtotime("-1 month", time());
        $prevMonthDate=date("Y-m-d", $time);
        $sql_update="";
        // in clsmember but not in class join table -> 1.在別班?(YES:換班), 2.新學員(insert)
        //echo "<br>cls member none ";	for($i=0;$i<count($clsmembernone);$i++){echo "<br>".$clsmembernone[$i][0];}

        // 1.在別班?(YES:換班) - traff,traffCnt,traffReal,traffRealCnt
        $dayitem="0,0,0,0,0,0,0,'Z,0,Z,0',0,0,0,'1970-01-01','1970-01-01',0,'','',0,'',''";
        if (count($clsmembernone)>0&&$findleaderinfo==true){$classinfo=getClassInfo();}
        for($i=0;$i<count($clsmembernone);$i++)
        {
            $sqltemp="select A.STU_ID from `".$tbname."` A WHERE (A.STU_ID='".$clsmembernone[$i][0]."' AND A.titleid<5)";//echo $sqltemp;
            $sqltemp_result=mysql_query($sqltemp);
            $numrows=mysql_num_rows($sqltemp_result);
            if ($numrows>0)//在別班=>換到目前的班級
            {
                $sql_update.="update `".$tbname."` set `CLS_ID`='".$clsid."',`classname`='".$clsname."',`memberseq`=".$clsmembernone[$i][1]." where (`STU_ID`='".$clsmembernone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            // 法會報名表內也無此學員 => 新加入的學員,找出學員基本資料再 insert 到法會報名表
            $sqlstudent="select * from `sdm_students` where `STU_ID`='".$clsmembernone[$i][0]."';";
            $sqlstudent_result=mysql_query($sqlstudent);
            $numrows=mysql_num_rows($sqlstudent_result);
            if ($numrows<=0){continue;}
            $row_stu=mysql_fetch_array($sqlstudent_result, MYSQL_ASSOC);

            $leader="";
            if($findleaderinfo==true)
            {
                $leaderinfo=getLeaderInfo($clsmembernone[$i][0],false);
                for($w=0;$w<count($leaderinfo);$w++)
                {
                    for($x=0;$x<count($classinfo);$x++)
                    {
                        if($leaderinfo[$w][0]==$classinfo[$x][0])
                        {
                            $leaderinfo[$w][2]=$classinfo[$x][1];
                            $leaderinfo[$w][3]=$classinfo[$x][2];
                            break;
                        }
                    }
                }
                for($w=0;$w<count($leaderinfo);$w++){$leader.=($leaderinfo[$w][2]."(".$leaderinfo[$w][1]."),");}
            }

            $titleid=1;
            $title="班員";
            $staffid=getStaffid($clsmembernone[$i][0]);
            if($clsmembernone[$i][2]=="TB06"){$titleid=0;}

            // INSERT COMMAND
            $sql_update.="insert into `".$tbname."` values (NULL,'".$row_stu["Name"]."','".$clsname."','".$clsid."','".$area."','".$areaid."',";
            $sql_update.="'".$title."','".$clsmembernone[$i][2]."',".$titleid.",'".$row_stu["STU_ID"]."','".$staffid."',";
            $sql_update.="'".$row_stu["Sex"]."','".$row_stu["BirthDate"]."','".$row_stu["DEG_ID"]."',";
            $sql_update.="'".$row_stu["School"]."','".$row_stu["OCC_ID"]."','".$row_stu["Organization"]."','".$row_stu["Title"]."',";
            $sql_update.="'".$row_stu["PhoneNo_H"]."','".$row_stu["PhoneNo_C"]."',".$clsmembernone[$i][1].",";
            $sql_update.="'".$leader."','','','".$classfullname."',";
            $sql_update.=$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.",".$dayitem.");";
        }
        //echo $sql_update;echo "<br>";return;

        // in puja but not in join table -> 1.調到別班?(YES:換班), 2.離開(insert)
        for($i=0;$i<count($pujanone);$i++)
        {
            $sqltemp="select * from `sdm_clsmembers` A WHERE (A.STU_ID='".$pujanone[$i][0]."' AND A.IsMajor='是')";
            $sqltemp_result=mysql_query($sqltemp);
            $numrows=mysql_num_rows($sqltemp_result);
            if ($numrows<=0)//找不到此人....休學或離開
            {
                $sql_update.="update `".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                continue;
            }

            $row=mysql_fetch_array($sqltemp_result, MYSQL_ASSOC);
            if($row["Status"]=="離開")
            {
                $sql_update.="update `".$tbname."` set `CLS_ID`='',`classname`='' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                //echo "<br>".$pujanone[$i][0]."(離開)";// update CLS_ID, Name and vailddate ad empty
            }
            else if ($row["CLS_ID"]!=$clsid)
            {
                $sqlclasses="select Class from `sdm_classes` WHERE (`CLS_ID`='".$row["CLS_ID"]."')";
                $sqlclasses_result=mysql_query($sqlclasses);
                $numrows=mysql_num_rows($sqlclasses_result);
                $classname="";
                if ($numrows>0)
                {
                    $row_classes=mysql_fetch_array($sqlclasses_result, MYSQL_ASSOC);
                    $tmp=explode("-",$row_classes["Class"]);
                    $classname=$tmp[0];
                }
                $sql_update.="update `".$tbname."` set `CLS_ID`='".$row["CLS_ID"]."',`classname`='".$classname."' where (`STU_ID`='".$pujanone[$i][0]."' AND `titleid`<5);";
                // find the class name from sdm_classes
                // echo "<br>".$pujanone[$i][0]."(調出)";// update CLS_ID, Name and vailddate
            }
        }
        //echo $sql_update;//echo "<br>";
        //echo "<br>rollcall none ";	for($i=0;$i<count($pujanone);$i++){echo "<br>".$pujanone[$i][0];}

        // change something
        $sqlcmd=explode(";",$sql_update);
        if (count($sqlcmd)>0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmd); $i++){$sql=$sqlcmd[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }

        // 一併更新學員的 seq number
        $sql_seq="";
        for($i=0;$i<count($memberinAll);$i++)
        {
            if($memberinAll[$i][1]==$memberinAll[$i][3]){continue;}
            //$sql_seq.="update `".$tbname."` set `memberseq`=".$memberinList[$i][1]." where (`CLS_ID`='".$clsid."' AND `studentid`='".$memberinList[$i][0]."');";
            $sql_seq.="update `".$tbname."` set `memberseq`=".$memberinAll[$i][1]." where (`idx`=".$memberinAll[$i][2].");";
        }

        $sqlcmdex=explode(";",$sql_seq);
        if (count($sqlcmdex) > 0)
        {
            mysql_query("SET autocommit=0");
            //for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];echo "<br>".$sql;}
            for($i = 0; $i < count($sqlcmdex); $i++){$sql=$sqlcmdex[$i];mysql_query($sql);}
            mysql_query("COMMIT");
        }
        return true;
    }

    function EngineAsInnoDB()
    {
        // connect your database here first Actual code starts here
        $sql="SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = 'your_database_name' AND ENGINE = 'MyISAM'";
        $rs=mysql_query($sql);
        while($row=mysql_fetch_array($rs))
        {
            $tbl=$row[0];
            $sql="ALTER TABLE `$tbl` ENGINE=INNODB";
            mysql_query($sql);
        }
    }
?>
