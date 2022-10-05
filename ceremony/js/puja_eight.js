$(document).ready(function () {
    $.ajax({
        async:false,url:"top.php",success:function (data) {
            $("#pageTop").append(data);
        }
    });

    // 叫出班級報名表
    $('#query').click(function() {
        getclassMember("","","","","","");
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 傳送報名
    $('#send').click(function () {
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
        if (clsregion==null||typeof clsregion==='undefined'||clsregion==""){alert("未取得班級區域資訊!");return ;}
        if (clshasleader==null||typeof clshasleader==='undefined'||clshasleader==""){alert("未取得班級幹部資訊!");return ;}

        var bPaid=false; if ($('.payitem').val()=="YES"){bPaid=true;}//是否要顯示繳費項目
        var allsqlcmd=""; var sqlcmd=""; var joinday=0; var regdate=""; var paydate=""; var regnewdate=""; var paynewdate=""; var payernewid=""; var payernewname="";

        var clssqlcmd=",`classname`=&#&#"+clsname+"&#&#,`CLS_ID`=&#&#"+clsid+"&#&#,`area`=&#&#"+clsarea+"&#&#,`areaid`=&#&#"+clsregion+"&#&#,`classfullname`=&#&#"+clsfullname+"&#&# ";//更新班級資料
        for(i=1;i<=nMemberCnt;i++) {
            bPay=false;
            lock=$('#idx_'+i).attr('lock');
            idx=$('#notjoin_'+i).attr('idx');//取得idx
            if (lock == 1 && bPaid == false) {//已經 lock且不是管理者無繳費確認功能=>不用處理此 record
                txtMemo=$('#memo_'+i).val();//備註
                sqlcmd="UPDATE `"+tbname+"` "+"SET `memo`=&#&#"+txtMemo+"&#&#";
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
                allsqlcmd+=sqlcmd;allsqlcmd += ";"; //allsqlcmd+="<br>";//debug
                continue;
            }

            regdate=$('#idx_'+i).attr('regdate');paydate=$('#idx_'+i).attr('paydate');
            payerid=$('#idx_'+i).attr('payerid');payername=$('#idx_'+i).attr('payername');
            if (bPaid==true){if ($('#pay_'+i).is(':checked')){bPay=true;}}

            traff="Z";traffReal="Z";paid=0;joinday=0;
            if ($('#join1_'+i).is(':checked')){joinday=1;traff=$("#traffic1_"+i).val(); if(bPay){traffReal=traff;}}
            else{traff="Z";traffReal="Z";}

            traffCnt="0";traffRealCnt="0";
            traffCnt=$("#traffic1round_"+i).val(); if(bPay){traffRealCnt=traffCnt;}

            // 考慮 reg date & pay date
            if (joinday==0){regnewdate="1970-01-01";paynewdate="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate=regdate;paynewdate=paydate;if (regdate=="1970-01-01"||regdate==""){regnewdate=curdate;}}

            fee=$('#fee_'+i).text();if (fee==""){fee=0;}// 車資
            txtMemo=$('#memo_'+i).val();//備註

            //考慮 lock, paid, pay, cost if(bPay){paid=fee;}
            if (bPaid==true){//管理窗口
                lock=0;
                if(bPay==true) {
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
            allsqlcmd += ";"; //allsqlcmd+="<br>";//debug
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
    $('#prttable').click(function () {
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
        if (classname==""||tbname==""){alert("未取得班級資料!");return;}

        var parameter='<input type="hidden" name="classid" value="'+classid+'" /><input type="hidden" name="classname" value="'+classname+'" />';
        parameter+='<input type="hidden" name="region" value="'+region+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+area+'" /><input type="hidden" name="pujatitle" value="'+pujatitle+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="major" value="'+hasleader+'" />';
        parameter+='"';

	  $('<form action="./pages/'+sub+'/print-list.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
     });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 列印繳費單
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#prtinvoice').click(function () {
        var classid=$('#classid').val(); //classid = $(this).val();
        var classname=$('#classid :selected').text();
        var region=$('#classid :selected').attr('regioncode');
        var area=$('#classid :selected').attr('AREAID');//alert(area);return;
        var tbname=$("#tb").val();
        var trafftb=$("#trafftb").val();
        var sub=$('.sub').val();
        var pujatitle=$('.pujatitle').val();
        //alert(trafftb+","+tbname+","+classname);
        //return;
        if (classname == "" || tbname == ""){alert("未取得班級資料!");return;}
        //alert(classname);
        var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';

        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />"';
        $('<form action="./pages/'+sub+'/register-list-invoice.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 已繳費名冊
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#paylist').click(function () {
        var classid=$('#classid').val(); //classid = $(this).val();
        var classname=$('#classid :selected').text();
        var region=$('#classid :selected').attr('regioncode');
        var area=$('#classid :selected').attr('AREAID');//alert(area);return;
        var tbname=$("#tb").val();
        var tbtraffic=$("#traffictb").val();
        var sub=$('.sub').val();
        if (classname==""||tbname==""||tbtraffic==""){alert("未取得班級報名及搭車資料!");return;}
        //alert(classname);
        $('<form action="./pages/'+sub+'/pay-list.php" method="post"><input type="hidden" name="classname" value='+classname+' /><input type="hidden" name="tbname" value='+tbname+' /><input type="hidden" name="tbtraffic" value='+tbtraffic+' /></form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出學員報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#export').click(function () {
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        var pujadate1=$('#pujadate1').val();
        var pujadate2=$('#pujadate2').val();
        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujadate1" value="'+pujadate1+'" /><input type="hidden" name="pujadate2" value="'+pujadate2+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出學員報名統計表-依報名時間排序
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportbydate').click(function () {
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        var pujadate1=$('#pujadate1').val();
        var pujadate2=$('#pujadate2').val();
        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujadate1" value="'+pujadate1+'" /><input type="hidden" name="pujadate2" value="'+pujadate2+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all-by-date.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出班級報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#classexport').click(function () {
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();

        if(tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
        parameter+='<input type="hidden" name="cancelitem" value="NO" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-class.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //$('#fixheadertbl').fixedHeaderTable('show', 500);
    $('#fixheadertbl').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true });
});

function getclassMember(clsid,clsname,clsarea,clsregion,clshasleader,clsfullname) {
    $('#queryresult').html("");
    var classid=clsid; //classid = $(this).val();
    var classname=clsname;
    var region=clsregion;
    var area=clsarea;//alert(area);return;
    var hasleader=clshasleader;
    var classfullname=clsfullname;
    if (classid==""||classname==""||area==""||region==""||classfullname=="") {
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
            if(data==0){$('#queryresult').html("查無資料!");}
            else{showtable(data,classid,classname,area,region,classfullname);}//$('#queryresult').html(data);
        }
    });
}

function showtable(data,classid,classname,area,region,classfullname) {
    //$('#queryresult').html(data);return; //debug
    var partsArray = data.split(';');
    var showdata="";
    if (partsArray.length<=0){return;}

    var mbdevice=$('#mbdevice').val();
    var payitem=$('#payitem').val(); //classid = $(this).val();
    var trafftable=partsArray[0].split('-');
    var trafftable1=trafftable[0].split('|');
    var trafftable2=trafftable[1].split('|');
    var pujadatetitle=$('#pujadatetitle').val();
    var itemNW=160;
    if (mbdevice=="YES"){itemNW=90;}

    var table1 ="<table class='reference' id='fixheadertbl1' style='width:800px;font-size:12px;' align='center'>";
    table1+="<thead><tr>";
    table1+="<th></th><th></th><th></th><th></th><th colspan='2'>"+pujadatetitle+"</th>";
    table1+="<th>車資</th><th>備註</th>";
    if(payitem=="YES"){table1+="<th>繳費</th>";}
    if(mbdevice=="YES"){table1+="<th></th>";}
    table1+="</tr><tr>";
    table1+="<th style='width:30px;'>序</th><th style='width:85px;'>姓名</th><th style='width:60px;'>身份</th>";
    table1+="<th style='width:65px;'>不參加</th>";

    table1+="<th style='width:65px;'>參加</th><th style='width:200px;'>車次</th>";//<th style='width:30px;'>訂餐</th>";
    table1+="<th style='width:50px;'></th>";
    if (mbdevice=="YES"){table1+="<th style='width:100px;'></th>";}else{table1+="<th style='width:180px;'></th>";}
    if(payitem=="YES"){table1+="<th style='width:30px;'></th>";}
    if(mbdevice=="YES"){table1+="<th>姓名</th>";}
    table1+="</thead><tbody><tr></tr>";

    var istep=0;
    var idx=0;
    var lock=0;
    chkJoin=" checked ";chkMeal=" checked ";chkLive=" checked ";traff="Z";tfround=" "; chkPay=" "; selected=" ";
    selected0=" ";selected1=" ";selected2=" ";

    for(w=1;w<partsArray.length;w++) {
        if (partsArray[w]==""){break;}
        var row = partsArray[w].split('|');
        if (row.length<5){continue;}

        var clr = "#000000"; // 8:班長 7:副班長 6:關懷員  5 : 班員 0:暫停班員
        //if (row[14]==0){clr="#FF0000";}else if (row[14]==-1){clr="#326432";}else if (row[14]>5){clr="#0000FF";}

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
        chkJoin="  ";disableditem=" disabled ";
        day=parseInt(row[4]);
        day1=day%10;
        day2=(day-day1)/10;
        if (day1>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}
        table1+="<td style='text-align:center;'><input id='join1_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='join1' type='checkbox'"+chkJoin+disabledlock+"></td>";

        var daytraff=row[6].split(',');//traff=row[6];
        traff=daytraff[0];if(traff==""||typeof(traff)=="undefined"){traff="Z";}

        //車次表 & 趟次
        tfround=row[15];
        disableditem=" disabled ";if (day1==1||day1==2){disableditem=" ";}
        table1+="<td style='text-align:center;'><select style='width:120px' id='traffic1_"+(w)+"' class='traffic' serial='"+(w)+"' idx='"+row[0]+"' name='traffic"+w+"' "+disableditem+disabledlock+">";
        for(kk=0;kk<trafftable1.length;kk+=2){selected=(traff==trafftable1[kk] ? "selected":" ");table1+="<option value='"+trafftable1[kk]+"' "+selected+">"+trafftable1[kk]+" - "+trafftable1[kk+1]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
        //for(kk=0;kk<trafftable1.length;kk+=2){selected=(traff==trafftable1[kk] ? "selected":" ");table1+="<option value='"+trafftable1[kk]+"' "+selected+">"+trafftable1[kk]+"</option>";}
        table1+="</select>&nbsp;";

        disableditem=" disabled ";if (day1>0&&traff!="Z"){disableditem=" ";}
        selected0=(tfround==0 ? "selected":" ");selected1=(tfround==1 ? "selected":" ");selected2=(tfround==2 ? "selected":" ");
        table1+="<select style='width:60px' id='traffic1round_"+(w)+"' class='traffic1round' serial='"+(w)+"' idx='"+idx+"' name='traffic1round"+w+"' "+disableditem+disabledlock+">";
        table1+="<option value='0' "+selected0+">去回</option><option value='1' "+selected1+">去</option><option value='2' "+selected2+">回</option>";
        table1+="</select>";
        table1 +="</td>";
        //車資
        table1+="<td style='text-align:center;'><div style='width:35px;' id='fee_"+(w)+"' class='fee' serial='"+(w)+"' idx='"+row[0]+"' name='fee"+w+"'>"+row[7]+"<div></td>";

        //備註
        table1+="<td style='text-align:center;'><input style='width:"+itemNW+"px' id='memo_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='memo' type='text'"+" value='"+row[13]+"'></td>";

        //繳費
        if(payitem=="YES")
        {
            chkPay="  ";if (row[8]>0){chkPay=" checked ";}
            disableditem="  ";if (row[4]<=0 || row[7]<=0){disableditem=" disabled ";}//不參加或無須繳費 - 無須繳費
            table1+="<td style='text-align:center;'><input id='pay_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='pay' type='checkbox'"+chkPay+disableditem+">";
        }

        if (mbdevice=="YES"){table1+="<td style='text-align:center;'>"+row[2]+"</td>";}
	  table1+="</tr>";
    }

    table1+="</table>";
    showdata+="<div id='tabs-1' class='grid_x height450' >"+table1+"</div>";
    showdata+="<input type='hidden' id='memberCnt' class='memberCnt' name='memberCnt' value='"+(partsArray.length-2)+"' />";

    showdata+="<input type='hidden' id='clsid' class='clsid' name='clsid' value='"+(classid)+"' />";
    showdata+="<input type='hidden' id='clsname' class='clsname' name='clsname' value='"+(classname)+"' />";
    showdata+="<input type='hidden' id='clsarea' class='clsarea' name='clsarea' value='"+(area)+"' />";
    showdata+="<input type='hidden' id='clsregion' class='clsregion' name='clsregion' value='"+(region)+"' />";
    showdata+="<input type='hidden' id='clsfullname' class='clsfullname' name='clsfullname' value='"+(classfullname)+"' />";
    //showtable(showdata);
    $('#queryresult').html(showdata);
    $('#fixheadertbl1').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true});

    $('.join1').click(function(event)
    {
        var serial=$(this).attr('serial');
        var joincnt=0;
        var kaojoincnt=0;

        $('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected');
        $('#traffic1_'+serial).val("Z").change();
        if ($('#join1_'+serial).is(':checked'))
        {
            $('#notjoin_'+serial).prop('checked',false);
            $('#traffic1_'+serial).prop('disabled',false);
            $('#fee_'+serial).text(getfee(serial));
        }
        else
        {
            $('#notjoin_'+serial).prop('checked',true);
            $("#traffic1_"+serial+" option")[0].selected=true;
            $('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
            $('#traffic1_'+serial).val("Z").change();
            $('#traffic1_'+serial).prop('disabled',true);
            $('#fee_'+serial).text("0");
            $('#pay_'+serial).prop('checked',false);$('#pay_'+serial).prop('disabled',true);
        }
    });

    $('.notjoin').click(function(event)
    {
        var serial=$(this).attr('serial');
        $('#join1_'+serial).prop('checked',false);
        $("#traffic1_"+serial+" option")[0].selected=true;
        $('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
        $('#traffic1_'+serial).val("Z").change();
        $('#traffic1_'+serial).prop('disabled',true);

        $('#traffic1round_'+serial+' option[value="0"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
        $('#traffic1round_'+serial).val("0").change();
        $('#traffic1round_'+serial).prop('disabled',true);

        $('#fee_'+serial).text("0");
        $('#pay_'+serial).prop('checked',false);$('#pay_'+serial).prop('disabled',true);
    });

    $('.traffic').on('change', function ()
    {
        var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
        $('#fee_'+serial).text(getfee(serial));

        if ($('#traffic1_'+serial).val()=="Z")
        {
            $('#traffic1round_'+serial+' option[value="0"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
            $('#traffic1round_'+serial).val("0").change();
            $('#traffic1round_'+serial).prop('disabled',true);
        }
        else
        {
            //$('#traffic1round_'+serial+' option[value="0"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
            $('#traffic1round_'+serial).prop('disabled',false);
        }
    });

    $('.traffic1round').on('change', function ()
    {
        var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
        $('#fee_'+serial).text(getfee(serial));
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








