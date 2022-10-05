$(document).ready(function ()
{
    $.ajax({
        async:false,url:"top.php",success:function (data) {
            $("#pageTop").append(data);
        }
    });

    // 叫出班級報名表
    $('#query').click(function(){
        getclassMember("","","","","","");
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 傳送報名
    $('#send').click(function ()
    {
        var tbname = $("#tb").val();
        var tbstatistic = $("#tbstatistic").val();
        var nMemberCnt = $("#memberCnt").val();
        var classid=$("#classid").val();
        var tb=$('.tb').val();
        var trafftb=$('.trafftb').val();
        var sub=$('.sub').val();
        var payitem=$('#payitem').val();
        var payercurid=$('#payerid').val();
        var payercurname=$('#payername').val();
        var curdate=$('.currentdate').val();//alert(curdate);
        var dbg=$('.dbg').val();
        if (classid==null||typeof classid==='undefined'||classid==""){alert("未取得班級資訊!");return ;}

        var clsid=$("#clsid").val();
        var clsname=$("#clsname").val();
        var clsfullname=$("#clsfullname").val();
        var clsarea=$("#clsarea").val();
        var clsregion=$("#clsregion").val();
        var clshasleader=$("#Major").val();
        if (clsid==null||typeof clsid==='undefined'||clsid==""){alert("未取得班級資訊!");return ;}
        if (clsname==null||typeof clsname==='undefined'||clsname==""){alert("未取得班級資訊!");return ;}
        if (clsarea==null||typeof clsarea==='undefined'||clsarea==""){alert("未取得班級資訊!");return ;}
        //if (clsregion==null||typeof clsregion==='undefined'||clsregion==""){alert("未取得班級區域資訊!");return ;}
        if (clshasleader==null||typeof clshasleader==='undefined'||clshasleader==""){alert("未取得班級幹部資訊!");return ;}

        var bPaid=false; if ($('.payitem').val()=="YES"){bPaid=true;}//是否要顯示繳費項目
        var allsqlcmd=""; var sqlcmd=""; var joinday=0; var regdate=""; var paydate=""; var regnewdate=""; var paynewdate=""; var payernewid=""; var payernewname="";

        var clssqlcmd=",`classname`=&#&#"+clsname+"&#&#,`CLS_ID`=&#&#"+clsid+"&#&#,`area`=&#&#"+clsarea+"&#&#,`areaid`=&#&#"+clsregion+"&#&#,`classfullname`=&#&#"+clsfullname+"&#&# ";//更新班級資料
        for(i=1;i<=nMemberCnt;i++)
        {
            bPay=false;
            lock=$('#idx_'+i).attr('lock');
            idx=$('#notjoin_'+i).attr('idx');//取得idx

            if (lock==1&&bPaid==false)//已經 lock且不是管理者無繳費確認功能=>不用處理此 record
            {
                txtMemo=$('#memo_'+i).val();//備註
                sqlcmd="UPDATE `"+tbname+"` "+"SET `memo`=&#&#"+txtMemo+"&#&#";
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
                allsqlcmd+=sqlcmd;allsqlcmd += ";;;;"; //allsqlcmd+="<br>";//debug
                continue;
            }

            regdate=$('#idx_'+i).attr('regdate');paydate=$('#idx_'+i).attr('paydate');
            payerid=$('#idx_'+i).attr('payerid');payername=$('#idx_'+i).attr('payername');
            if (bPaid==true){if ($('#pay_'+i).is(':checked')){bPay=true;}}

            traff="Z";traffReal="Z";paid=0;joinday=0;
            if ($('#join1_'+i).is(':checked')){joinday=1;}else if ($('#join2_'+i).is(':checked')){joinday=10;}

            if(joinday>0)
            {
                if ($('#traff1_'+i).is(':checked')){traff="ZA";traffReal="ZA";}
                else if ($('#traff2_'+i).is(':checked')){traff="ZB";traffReal="ZB";}
                else if ($('#traff3_'+i).is(':checked')){traff="ZC";traffReal="ZC";}
                else if ($('#traff4_'+i).is(':checked')){traff="ZD";traffReal="ZD";}
                else if ($('#traff5_'+i).is(':checked')){traff="Z";traffReal="Z";}
            }

            traffCnt="0";traffRealCnt="0";

            // 考慮 reg date & pay date
            if (joinday==0){regnewdate="1970-01-01";paynewdate="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate=regdate;paynewdate=paydate;if (regdate=="1970-01-01"||regdate==""){regnewdate=curdate;}}

            fee=0;// 車資
            txtMemo=$('#memo_'+i).val();//備註

            //考慮 lock, paid, pay, cost if(bPay){paid=fee;}
            if (bPaid==true){//管理窗口
                lock=0;
                if(bPay==true)
                {
                    lock=1;paid=fee;
                    if (paydate=="1970-01-01"||paydate==""){paynewdate=curdate;}
                    if (payerid==""){payernewid=payercurid;}else{payernewid=payerid;}
                    if (payername==""){payernewname=payercurname;}else{payernewname=payername;}
                }else{
                    paid=0;
                    paynewdate="1970-01-01";
                    payernewid="";
                    payernewname="";
                }
                sqlcmd="UPDATE `"+tbname+"` "+"SET `day`="+joinday+",`traff`=&#&#"+traff+"&#&#,`traffReal`=&#&#"+traffReal+"&#&#,`traffCnt`="+traffCnt+",`traffRealCnt`="+traffRealCnt+",";
                sqlcmd+="`cost`="+fee+",`lock`="+lock+",`pay`="+paid+",`regdate`=&#&#"+regnewdate+"&#&#,`paydate`=&#&#"+paynewdate+"&#&#,";
                sqlcmd+="`paybyid`=&#&#"+payernewid+"&#&#,`paybyname`=&#&#"+payernewname+"&#&#,`memo`=&#&#"+txtMemo+"&#&#";
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
            }else{//幹部一般報名
                sqlcmd="UPDATE `"+tbname+"` "+"SET `day`="+joinday+",`traff`=&#&#"+traff+"&#&#,`traffReal`=&#&#"+traffReal+"&#&#,`traffCnt`="+traffCnt+",`traffRealCnt`="+traffRealCnt+",`cost`="+fee+",`regdate`=&#&#"+regnewdate+"&#&#,`memo`=&#&#"+txtMemo+"&#&#";
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
            }

            allsqlcmd+=sqlcmd;
            allsqlcmd += ";;;;"; //allsqlcmd+="<br>";//debug
        }

        if(dbg=="YES"){$('#msg').html(allsqlcmd);return;}	 // debug show info
        $.ajax({
            async: false,
            url: "./pages/"+sub+"/register.php",
            cache: false,
            dataType: 'html',
            type:'POST',
            data:{sqlcommand:allsqlcmd},
            error: function (data) {
                alert("報名失敗!!!");
            },success: function (data) {
                //alert(data);//$('#msg').html(data);	 // debug show info
                if (data < 0){alert("報名失敗(錯誤代碼:"+data+")!");}
                else{alert("報名成功!");getclassMember(clsid,clsname,clsarea,clsregion,clshasleader,clsfullname);}
            }
        });
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出報名表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#prttable').click(function ()
    {
        var classid=$('#classid').val(); //classid = $(this).val();
        var classname=$('#classid :selected').text();
        var region=$('#classid :selected').attr('regioncode');
        var area=$('#classid :selected').attr('AREAID');//alert(area);return;
        var tbname = $("#tb").val();
        var sub=$('.sub').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        var hasleader=$('#Major').val();
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();
        if (classname==""||tbname==""){alert("未取得班級資料!");return;}

        var parameter='<input type="hidden" name="classid" value="'+classid+'" /><input type="hidden" name="classname" value="'+classname+'" />';
        parameter+='<input type="hidden" name="region" value="'+region+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+area+'" /><input type="hidden" name="pujatitle" value="'+pujatitle+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="major" value="'+hasleader+'" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
		parameter+='"';

	  $('<form action="./pages/'+sub+'/print-list.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
     });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 列印繳費單
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#prtinvoice').click(function ()
    {
        var classid=$('#classid').val(); //classid = $(this).val();
        var classname=$('#classid :selected').text();
        var region=$('#classid :selected').attr('regioncode');
        var area=$('#classid :selected').attr('AREAID');//alert(area);return;
        var tbname=$("#tb").val();
        var trafftb=$("#trafftb").val();
        var sub=$('.sub').val();
        var pujatitle=$('.pujatitle').val();
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();
        //alert(trafftb+","+tbname+","+classname);
        //return;
        if (classname == "" || tbname == ""){alert("未取得班級資料!");return;}
        //alert(classname);
        var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />"';
        $('<form action="./pages/'+sub+'/register-list-invoice.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 已繳費名冊
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#paylist').click(function ()
    {
        var classid=$('#classid').val(); //classid = $(this).val();
        var classname=$('#classid :selected').text();
        var region=$('#classid :selected').attr('regioncode');
        var area=$('#classid :selected').attr('AREAID');//alert(area);return;
        var tbname=$("#tb").val();
        var tbtraffic=$("#traffictb").val();
        var sub=$('.sub').val();
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();
        if (classname==""||tbname==""||tbtraffic==""){alert("未取得班級報名及搭車資料!");return;}
        //alert(classname);
        //$('<form action="./pages/'+sub+'/pay-list.php" method="post"><input type="hidden" name="classname" value='+classname+' /><input type="hidden" name="tbname" value='+tbname+' /><input type="hidden" name="tbtraffic" value='+tbtraffic+' /></form>').appendTo('body').submit().remove();

		var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="tbname" value="'+tbname+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
		parameter+='"';
	    $('<form action="./pages/'+sub+'/pay-list.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
	});
    //-----------------------------------------------------------------------------------------------------------------------------------
    // 批次列印報名表 - 依區域
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportlistarea').click(function ()
    {
        var classarea=$('#classarea').val();//$('#classname').val();
        var tbname=$("#tb").val();
        var sub=$('.sub').val();
        var pujatitle=$('.pujatitle').val();
        var hasleader=$('#Major').val();
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();
        if (classarea == "" || tbname == ""){alert("未指定區域!");return;}

        var parameter='<input type="hidden" name="classarea" value="'+classarea+'" /><input type="hidden" name="tbname" value="'+tbname+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="major" value="'+hasleader+'" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
		parameter+='"';
	    $('<form action="./pages/'+sub+'/print-list-area.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
     });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出班級報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportclasslist').click(function (){
        var tbname=$("#tb").val();
        var tbtraffic=$("#traffictb").val();
        var tbstatistic=$("#tbstatistic").val();
        var sub=$('.sub').val();
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();
        if (tbname==""||tbtraffic==""){alert("未取得報名統計資料!");return;}
        //alert(tbtraffic+"   ,    "+tbname+"   ,    "+tbstatistic);
        //$('<form action="./pages/'+sub+'/export-all.php" method="post"><input type="hidden" name="tbname" value='+tbname+' /><input type="hidden" name="tbstatistic" value='+tbstatistic+' /><input type="hidden" name="tbtraffic" value='+tbtraffic+' /></form>').appendTo('body').submit().remove();

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" />';
        parameter+='<input type="hidden" name="tbstatistic" value="'+tbstatistic+'" /><input type="hidden" name="tbtraffic" value="'+tbtraffic+'" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='"';
        //$('<form action="./pages/'+sub+'/register-list-export-class.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
        $('<form action="./pages/'+sub+'/export-class.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出學員報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#export').click(function ()
    {
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();
        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
		parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出學員報名統計表-依報名時間排序
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportbydate').click(function ()
    {
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();
        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all-by-date.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });
    //$('#fixheadertbl').fixedHeaderTable('show', 500);
    $('#fixheadertbl').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true });
});

function getclassMember(clsid,clsname,clsarea,clsregion,clshasleader,clsfullname)
{
    $('#queryresult').html("");
    var classid=clsid; //classid = $(this).val();
    var classname=clsname;
    var region=clsregion;
    var area=clsarea;//alert(area);return;
    var hasleader=clshasleader;
    var classfullname=clsfullname;
    if (classid==""||classname==""||area==""||region==""||classfullname=="")
    {
        classid=$('#classid').val(); //classid = $(this).val();
        classname=$('#classid :selected').text();
        region=$('#classid :selected').attr('regioncode');
        area=$('#classid :selected').attr('AREAID');//alert(area);return;
        classfullname=$('#classid :selected').attr('classname');//alert(area);return;
        hasleader=$('#Major').val();
    }
    var detailinfo=$('#detailinfo').val();
    var leaderinfo=$('#leaderinfo').val();
    var volunteerinfo=$('#volunteerinfo').val();

    var tb=$('.tb').val();
    var trafftb=$('.trafftb').val();
    var sub=$('.sub').val();
    if (classname=='-'){alert("尚未指定班級!");return;}
    // 送出查詢關鍵字至後端
    $.ajax({
        async: false,
        url: "./pages/"+sub+"/queryquery.php",
        cache: false,
        dataType: 'html',
        type:'POST',
        data:{classname:classname, classid:classid, tbname:tb, trafftbname:trafftb,Major:hasleader,detailinfo:detailinfo,leaderinfo:leaderinfo,volunteerinfo:volunteerinfo,area:area,region:region,classfullname:classfullname},
        error: function (data) {
            alert("查詢班級資料失敗!!!");//$('#queryresult').html(data);
        },success: function (data){
            //$('#queryresult').html(data);return;
            if(data==0){$('#queryresult').html("查無資料!");}
            else{showtable(data,classid,classname,area,region,classfullname);}//$('#queryresult').html(data);
        }
    });
}

function showtable(data,classid,classname,area,region,classfullname){
    //$('#queryresult').html(data);return; //debug
    var partsArray = data.split(';');
    var showdata="";
    if (partsArray.length<=0){return;}

    var mbdevice=$('#mbdevice').val();

    var width=800;//1140;
    var item1W=30;//序
    var item2W=80;//姓名
    var item3W=60;//身份
    var item4W=65;//not join
    var item5W=80;//day 1
    var item6W=80;//day2
    var item7W=55;//traff1
    var item8W=55;//traff2
    var item9W=55;//traff3
    var itemAW=55;//traff4
    var itemBW=55;//traff5
    var itemCW=100;//memo
    var itemNW=0;
    if (mbdevice=="YES"){itemNW=70;item3W=50;item7W=45;item8W=45;item9W=45;itemAW=45;}

    var payitem=$('#payitem').val(); //classid = $(this).val();
	var day1title=$('#day1title').val();
	var day2title=$('#day2title').val();
    var trafftable=partsArray[0].split('-');
    var trafftable1=trafftable[0].split('|');
    var trafftable2=trafftable[1].split('|');

    var table1="<table class=\"reference\" id=\"fixheadertbl1\" style=\"width:800px\" align=\"center\">";
    table1+="<thead><tr>";
    table1+="<th></th><th></th><th></th><th></th><th colspan=\"2\">參加場次</th>";
    table1+="<th colspan=\"5\">交通工具</th><th>備註</th>";
    if(payitem=="YES"){table1+="<th>繳費</th>";}
    if(itemNW>0){table1+="<th></th";}
    table1+="</tr><tr>";
    table1+="<th style=\"width:"+item1W+"px;\">序</th><th style=\"width:"+item2W+"px;\">姓名</th><th style=\"width:"+item3W+"px;\">身份</th>";
    table1+="<th style=\"width:"+item4W+"px;\">不參加</th>";
    table1+="<th style=\"width:"+item5W+"px;\">"+day1title+"</th><th style=\"width:"+item6W+"px;\">"+day2title+"</th>";//<th style=\"width:30px;\">訂餐</th>";

    table1+="<th style=\"width:"+item7W+"px;\">捷運</th><th style=\"width:"+item8W+"px;\">機車</th><th style=\"width:"+item9W+"px;\">共乘</th>";
    table1+="<th style=\"width:"+itemAW+"px;\">開車</th><th style=\"width:"+itemBW+"px;\">其他</th>";

    table1+="<th style=\"width:"+itemCW+"px;\"></th>";
    if(payitem=="YES"){table1+="<th style=\"width:30px;\"></th>";}
    if(itemNW>0){table1+="<th style=\"width:"+item2W+"px;\">姓名</th>";}
    table1+="</thead><tbody><tr></tr>";

    var istep=0;
    var idx=0;
    var lock=0;
    chkJoin=" checked ";chkMeal=" checked ";chkLive=" checked ";traff="Z";tfround=" "; chkPay=" "; selected=" ";

    for(w=1;w<partsArray.length;w++)
    {
        if (partsArray[w]==""){break;}
	  var row = partsArray[w].split('|');
	  if (row.length<5){continue;}

        var clr = "#000000"; // 8:班長 7:副班長 6:關懷員  5 : 班員 0:暫停班員
        //if (row[14]==0){clr="#FF0000";}else if (row[14]==-1){clr="#326432";}else if (row[14]>5){clr="#0000FF";}
        if (row[14]==0){clr="#000000";}else if (row[14]==-1){clr="#326432";}else if (row[14]>5){clr="#0000FF";}

        if(payitem=="YES"){lock=0;}else{lock=row[1];}//管理者可勾選繳費
        disabledlock="  ";if (lock>=1){disabledlock=" disabled ";}

        table1+="<tr><td id='idx_"+(w)+"'' class='idx' idx='"+row[0]+"' serial='"+(w)+"' lock='"+lock+"' regdate='"+row[9]+"' paydate='"+row[10]+"' payerid='"+row[11]+"' payername='"+row[12]+"'>"+(w)+"</td>";//序-記錄相關informaion
        table1+="<td style='text-align:center;' id='student_"+(w)+"'' class='student' idx='"+row[0]+"' serial='"+(w)+"'> <font color='"+clr+"'> "+row[2]+"</font></td>";//姓名

        table1+="<td style='text-align:center;'><font color='"+clr+"'>"+row[3]+"</font></td>"; // title

        disableditem="  ";if (row[4]>0){disableditem=" disabled ";}
        // 不參加
        chkNotJoin=" checked ";chkJoin="  ";disableTraff=" disabled ";
        if (row[4]>0){chkNotJoin="  ";chkJoin=" checked ";disableTraff=" ";}
        table1+="<td style='text-align:center;'><input id='notjoin_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='notjoin' type='radio'"+chkNotJoin+disabledlock+"></td>";

        // 參加 & 車次
        chkJoin="  ";disableditem="  ";
        day=parseInt(row[4]);
        day1=day%10;
        day2=(day-day1)/10;
        if (day1>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}
        table1+="<td style='text-align:center;'><input id='join1_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='join' type='checkbox'"+chkJoin+disabledlock+"></td>";

        chkJoin="  ";disableditem="  ";
        if (day2>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}
        table1+="<td style='text-align:center;'><input id='join2_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='join' type='checkbox'"+chkJoin+disabledlock+"></td>";

        // 交通工具
        var daytraff=row[6].split(',');//traff=row[6];
        traff=daytraff[0];if(traff==""||typeof(traff)=="undefined"){traff="Z";}

        chktraff="  ";if (traff=="ZA"){chktraff=" checked ";}
        table1+="<td style='text-align:center;'><input id='traff1_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='traff' type='checkbox'"+chktraff+disableTraff+disabledlock+"></td>";

        chktraff="  ";if (traff=="ZB"){chktraff=" checked ";}
        table1+="<td style='text-align:center;'><input id='traff2_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='traff' type='checkbox'"+chktraff+disableTraff+disabledlock+"></td>";

        chktraff="  ";if (traff=="ZC"){chktraff=" checked ";}
        table1+="<td style='text-align:center;'><input id='traff3_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='traff' type='checkbox'"+chktraff+disableTraff+disabledlock+"></td>";

        chktraff="  ";if (traff=="ZD"){chktraff=" checked ";}
        table1+="<td style='text-align:center;'><input id='traff4_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='traff' type='checkbox'"+chktraff+disableTraff+disabledlock+"></td>";

        chktraff="  ";if (traff=="Z"){chktraff=" checked ";}
        table1+="<td style='text-align:center;'><input id='traff5_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='traff' type='checkbox'"+chktraff+disableTraff+disabledlock+"></td>";

        //備註
        table1+="<td style='text-align:center;'><input style='width:90px' id='memo_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='memo' type='text'"+" value='"+row[13]+"'></td>";

        //繳費
        if(payitem=="YES")
        {
            chkPay="  ";if (row[8]>0){chkPay=" checked ";}
            disableditem="  ";if (row[4]<=0 || row[7]<=0){disableditem=" disabled ";}//不參加或無須繳費 - 無須繳費
            table1+="<td style='text-align:center;'><input id='pay_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='pay' type='checkbox'"+chkPay+disableditem+">";
        }

        if(itemNW>0){table1+="<td align='center'>"+row[2]+"</td>";}//手機模式-右側標註姓名

        table1+="</tr>";
    }

    table1+="</table>";
    showdata+="<div id=\"tabs-1\" class=\"grid_x height450\" >"+table1+"</div>";
    showdata+="<input type='hidden' id='memberCnt' class='memberCnt' name='memberCnt' value='"+(partsArray.length-2)+"' />";

    showdata+="<input type='hidden' id='clsid' class='clsid' name='clsid' value='"+(classid)+"' />";
    showdata+="<input type='hidden' id='clsname' class='clsname' name='clsname' value='"+(classname)+"' />";
    showdata+="<input type='hidden' id='clsarea' class='clsarea' name='clsarea' value='"+(area)+"' />";
    showdata+="<input type='hidden' id='clsregion' class='clsregion' name='clsregion' value='"+(region)+"' />";
    showdata+="<input type='hidden' id='clsfullname' class='clsfullname' name='clsfullname' value='"+(classfullname)+"' />";
    //showtable(showdata);
    $('#queryresult').html(showdata);
    $('#fixheadertbl1').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true});

    $('.join').click(function(event)
    {
        var serial=$(this).attr('serial');
        if ($('#join1_'+serial).is(':checked')||$('#join2_'+serial).is(':checked'))
        {
            $('#join1_'+serial).prop('checked',false);
            $('#join2_'+serial).prop('checked',false);
            $(this).prop('checked',true);
            $('#notjoin_'+serial).prop('checked',false);
            $('#traff1_'+serial).prop('disabled',false);
            $('#traff2_'+serial).prop('disabled',false);
            $('#traff3_'+serial).prop('disabled',false);
            $('#traff4_'+serial).prop('disabled',false);
            $('#traff5_'+serial).prop('disabled',false);
//            if($('#traff1_'+serial).is(':checked')==false&&$('#traff2_'+serial).is(':checked')==false&&
//               $('#traff3_'+serial).is(':checked')==false&&$('#traff4_'+serial).is(':checked')==false&&
//               $('#traff5_'+serial).is(':checked')==false)
//            {
//                $('#traff5_'+serial).prop('checked',true);
//            }
        }
        else
        {
            $('#traff1_'+serial).prop('checked',false);
            $('#traff2_'+serial).prop('checked',false);
            $('#traff3_'+serial).prop('checked',false);
            $('#traff4_'+serial).prop('checked',false);
            $('#traff5_'+serial).prop('checked',false);
            $('#notjoin_'+serial).prop('checked',true);
            $('#traff1_'+serial).prop('disabled',true);
            $('#traff2_'+serial).prop('disabled',true);
            $('#traff3_'+serial).prop('disabled',true);
            $('#traff4_'+serial).prop('disabled',true);
            $('#traff5_'+serial).prop('disabled',true);
        }
    });

    $('.notjoin').click(function(event)
    {
        var serial=$(this).attr('serial');
        $('#join1_'+serial).prop('checked',false);
        $('#join2_'+serial).prop('checked',false);

        $('#traff1_'+serial).prop('checked',false);
        $('#traff2_'+serial).prop('checked',false);
        $('#traff3_'+serial).prop('checked',false);
        $('#traff4_'+serial).prop('checked',false);
        $('#traff5_'+serial).prop('checked',false);

        $('#traff1_'+serial).prop('disabled',true);
        $('#traff2_'+serial).prop('disabled',true);
        $('#traff3_'+serial).prop('disabled',true);
        $('#traff4_'+serial).prop('disabled',true);
        $('#traff5_'+serial).prop('disabled',true);
    });

    $('.traff').click(function(event)
    {
        var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
        $('#traff1_'+serial).prop('checked',false);
        $('#traff2_'+serial).prop('checked',false);
        $('#traff3_'+serial).prop('checked',false);
        $('#traff4_'+serial).prop('checked',false);
        $('#traff5_'+serial).prop('checked',false);
        $(this).prop('checked',true);
    });

    $('.pay').click(function(event)
    {
        var serial = $(this).attr('serial');
        if ($(this).is(':checked')){$('#notjoin_'+serial).prop('disabled',true);$('#join_'+serial).prop('disabled',true);$('#traffic_'+serial).prop('disabled',true);}
        else{$('#notjoin_'+serial).prop('disabled',false);$('#join_'+serial).prop('disabled',false);if ($('#join_'+serial).is(':checked')){$('#traffic_'+serial).prop('disabled',false);}}
    });
}

function getfee(index)
{
    var fee=0;
    var gofee=parseInt($('#traffgofee').val());
    var backfee=parseInt($('#traffbackfee').val());
    var roundfee=parseInt($('#traffroundfee').val());
    var overdayfee=parseInt($('#traffoverdayfee').val());
    var round1=parseInt($('#traffic1round_'+index).val());
    if ($('#join1_'+index).is(':checked'))
    {
        if ($('#traffic1_'+index).val()!="Z")
        {
            if (round1==0){fee+=roundfee;}//來回
            if (round1==1){fee+=gofee;}//去
            if (round1==2){fee+=backfee;}//回
        }
    }
    if (fee<=0){$('#pay_'+index).prop('checked',false);$('#pay_'+index).prop('disabled',true);}
    else{$('#pay_'+index).prop('disabled',false);}

    return fee;
}
