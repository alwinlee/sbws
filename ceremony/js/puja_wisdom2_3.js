$(document).ready(function (){
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
    $('#send').click(function() {
        var tbname=$("#tb").val();
        var tbstatistic=$("#tbstatistic").val();
        var nMemberCnt=$("#memberCnt").val();
        var classid=$("#classid").val();
        var tb=$('.tb').val();
        var trafftb=$('.trafftb').val();
        var sub=$('.sub').val();
        var payitem=$('#payitem').val();
        var payercurid=$('#payerid').val();
        var payercurname=$('#payername').val();
        var curdate=$('.currentdate').val();//alert(curdate);
        var dbg=$('.dbg').val();
        var tagnotjoinitem1=$('.tagnotjoinitem1').val();
        var tagnotjoinitem2=$('.tagnotjoinitem2').val();

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
        var bCancelReg=false; //取消報名
        var allsqlcmd=""; var sqlcmd=""; var joinday=0; var regdate=""; var paydate=""; var regnewdate=""; var paynewdate=""; var payernewid=""; var payernewname="";

        var ojoinday=0;
        var clssqlcmd=",`classname`=&#&#"+clsname+"&#&#,`CLS_ID`=&#&#"+clsid+"&#&#,`area`=&#&#"+clsarea+"&#&#,`areaid`=&#&#"+clsregion+"&#&#,`classfullname`=&#&#"+clsfullname+"&#&# ";//更新班級資料
        for(i=1;i<=nMemberCnt;i++)
        {
            bPay=false;
            lock=$('#idx_'+i).attr('lock');
            ojoinday=$('#idx_'+i).attr('ojoinday');
            otraffic=$('#idx_'+i).attr('traffic');
            ofee=$('#idx_'+i).attr('fee');
            oservice=$('#idx_'+i).attr('oservice');
            idx=$('#notjoin_'+i).attr('idx');//取得idx

            if (lock==1&&bPaid==false)//已經 lock且不是管理者無繳費確認功能=>不用處理此 record
            {
                txtMemo=$('#memo_'+i).val();//備註
                sqlcmd="UPDATE `"+tbname+"` "+"SET `memo`=&#&#"+txtMemo+"&#&#";
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
                allsqlcmd+=sqlcmd;allsqlcmd+=";;;;"; //allsqlcmd+="<br>";//debug
                continue;
            }

            regdate=$('#idx_'+i).attr('regdate');paydate=$('#idx_'+i).attr('paydate');
            payerid=$('#idx_'+i).attr('payerid');payername=$('#idx_'+i).attr('payername');
            if (bPaid==true){if ($('#pay_'+i).is(':checked')){bPay=true;}}

            traff="Z";traffReal="Z";paid=0;joinday=0;type=0;speccare=0;
            if ($('#join1_'+i).is(':checked')){joinday=1;traff=$("#traffic1_"+i).val(); if(bPay){traffReal=traff;}}
            else if ($('#join2_'+i).is(':checked')){joinday=2;traff=$("#traffic1_"+i).val(); if(bPay){traffReal=traff;}}
            else if ($('#join3_'+i).is(':checked')){joinday=3;traff=$("#traffic1_"+i).val(); if(bPay){traffReal=traff;}}
            else if ($('#join4_'+i).is(':checked')){joinday=4;traff=$("#traffic1_"+i).val(); if(bPay){traffReal=traff;}}
            else{traff="Z";traffReal="Z";}

            traffCnt="0";traffRealCnt="0";
            traffCnt=$("#traffic1round_"+i).val(); if(bPay){traffRealCnt=traffCnt;}

            // 正行,重培,關懷
            if ($('#type1_'+i).is(':checked')){type=1;}else if ($('#type2_'+i).is(':checked')){type=2;}else if ($('#type3_'+i).is(':checked')){type=3;}
		speccare=$('#care_'+i).val();// 特殊住宿需求

            // 判斷是否取消報名
            bCancelReg=false;
            if (joinday==0){
                if (paydate=="1970-01-01"||paydate==""){bCancelReg=false;}
                else{bCancelReg=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }

            // 考慮 reg date & pay date
            if (joinday==0){regnewdate="1970-01-01";paynewdate="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate=regdate;paynewdate=paydate;if (regdate=="1970-01-01"||regdate==""){regnewdate=curdate;}}

            fee=$('#fee_'+i).text();if (fee==""){fee=0;}// 車資
            txtMemo=$('#memo_'+i).val();//備註

            //考慮 lock, paid, pay, cost if(bPay){paid=fee;}
            if (bPaid==true){//管理窗口
                lock=0;
                srvice=0; //註記取消報名
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
                otherinfo="";
                if(bCancelReg==true&&tagnotjoinitem1=="YES"){srvice=ojoinday;otherinfo=ojoinday+","+otraffic+","+ofee+","+payername+","+paydate+","+curdate+",";}
                if(bCancelReg==true&&tagnotjoinitem2=="YES"){srvice=ojoinday;otherinfo=ojoinday+","+otraffic+","+ofee+","+payername+","+paydate+","+curdate+",";}

                sqlcmd="UPDATE `"+tbname+"` "+"SET `day`="+joinday+",`traff`=&#&#"+traff+"&#&#,`traffReal`=&#&#"+traffReal+"&#&#,`traffCnt`="+traffCnt+",`traffRealCnt`="+traffRealCnt+",";
                sqlcmd+="`cost`="+fee+",`joinmode`="+type+",`specialcase`="+speccare+",`lock`="+lock+",`pay`="+paid+",`regdate`=&#&#"+regnewdate+"&#&#,`paydate`=&#&#"+paynewdate+"&#&#,";
                sqlcmd+="`paybyid`=&#&#"+payernewid+"&#&#,`paybyname`=&#&#"+payernewname+"&#&#,`memo`=&#&#"+txtMemo+"&#&#";
                if(srvice>0){sqlcmd+=(",`service`="+srvice+",`volunteerinfo`=&#&#"+otherinfo+"&#&#");}//暫時記在volunteerinfo
                else if(oservice>0&&paid>0){sqlcmd+=(",`service`=0");}
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
            }else{//幹部一般報名
                sqlcmd="UPDATE `"+tbname+"` "+"SET `day`="+joinday+",`traff`=&#&#"+traff+"&#&#,`traffReal`=&#&#"+traffReal+"&#&#,`traffCnt`="+traffCnt+",`traffRealCnt`="+traffRealCnt+",`cost`="+fee+",`joinmode`="+type+",`specialcase`="+speccare+",`regdate`=&#&#"+regnewdate+"&#&#,`memo`=&#&#"+txtMemo+"&#&#";
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
            }

            allsqlcmd+=sqlcmd;
            allsqlcmd+=";;;;"; //allsqlcmd+="<br>";//debug
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
    $('#prttable').click(function(){
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

        var item1=$('#item1').val(),item2=$('#item2').val(),item3=$('#item3').val(),item4=$('#item4').val();

        var parameter='<input type="hidden" name="classid" value="'+classid+'" /><input type="hidden" name="classname" value="'+classname+'" />';
        parameter+='<input type="hidden" name="region" value="'+region+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+area+'" /><input type="hidden" name="pujatitle" value="'+pujatitle+'" />';

        parameter+='<input type="hidden" name="item1" value="'+item1+'" /><input type="hidden" name="item2" value="'+item2+'" />';
        parameter+='<input type="hidden" name="item3" value="'+item3+'" /><input type="hidden" name="item4" value="'+item4+'" />';

        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="major" value="'+hasleader+'" />';
        parameter+='"';

	  $('<form action="./pages/'+sub+'/print-list.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
     });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 列印繳費單
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#prtinvoice').click(function(){
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
        var item1=$('#item1').val(),item2=$('#item2').val(),item3=$('#item3').val(),item4=$('#item4').val();
        //alert(classname);
        var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';

        parameter+='<input type="hidden" name="item1" value="'+item1+'" /><input type="hidden" name="item2" value="'+item2+'" />';
        parameter+='<input type="hidden" name="item3" value="'+item3+'" /><input type="hidden" name="item4" value="'+item4+'" />';

        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />"';
        $('<form action="./pages/'+sub+'/register-list-invoice.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 已繳費名冊
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#paylist').click(function(){
        var classid=$('#classid').val(); //classid = $(this).val();
        var classname=$('#classid :selected').text();
        var region=$('#classid :selected').attr('regioncode');
        var area=$('#classid :selected').attr('AREAID');//alert(area);return;
        var tbname=$("#tb").val();
        var tbtraffic=$("#traffictb").val();
        var sub=$('.sub').val();
        if (classname==""||tbname==""||tbtraffic==""){alert("未取得班級報名及搭車資料!");return;}
        var item1=$('#item1').val(),item2=$('#item2').val(),item3=$('#item3').val(),item4=$('#item4').val();

        var parameter='<input type="hidden" name="classid" value="'+classid+'" /><input type="hidden" name="classname" value="'+classname+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="tbtraffic" value="'+tbtraffic+'" />';
        parameter+='<input type="hidden" name="item1" value="'+item1+'" /><input type="hidden" name="item2" value="'+item2+'" />';
        parameter+='<input type="hidden" name="item3" value="'+item3+'" /><input type="hidden" name="item4" value="'+item4+'" />';
        parameter+='"';
	    $('<form action="./pages/'+sub+'/pay-list.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
        //$('<form action="./pages/'+sub+'/pay-list.php" method="post"><input type="hidden" name="classname" value='+classname+' /><input type="hidden" name="tbname" value='+tbname+' /><input type="hidden" name="tbtraffic" value='+tbtraffic+' /></form>').appendTo('body').submit().remove();
    });
    //-----------------------------------------------------------------------------------------------------------------------------------
    // 批次列印報名表 - 依區域
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportlistarea').click(function (){
        var classarea=$('#classarea').val();//$('#classname').val();
        var tbname=$("#tb").val();
        var sub=$('.sub').val();
        var pujatitle=$('.pujatitle').val();
        var hasleader=$('#Major').val();
        if (classarea == "" || tbname == ""){alert("未指定區域!");return;}
        var item1=$('#item1').val(),item2=$('#item2').val(),item3=$('#item3').val(),item4=$('#item4').val();
        var parameter='<input type="hidden" name="classarea" value="'+classarea+'" /><input type="hidden" name="tbname" value="'+tbname+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="major" value="'+hasleader+'" />';
        parameter+='<input type="hidden" name="item1" value="'+item1+'" /><input type="hidden" name="item2" value="'+item2+'" />';
        parameter+='<input type="hidden" name="item3" value="'+item3+'" /><input type="hidden" name="item4" value="'+item4+'" />';

        parameter+='"';
	  $('<form action="./pages/'+sub+'/print-list-area.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
     });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出班級報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportclasslist').click(function(){
        var tbname=$("#tb").val();
        var tbtraffic=$("#traffictb").val();
        var tbstatistic=$("#tbstatistic").val();
        var sub=$('.sub').val();
        if (tbname==""||tbtraffic==""){alert("未取得報名統計資料!");return;}
        var item1=$('#item1').val(),item2=$('#item2').val(),item3=$('#item3').val(),item4=$('#item4').val();

        var parameter='<input type="hidden" name="tbstatistic" value="'+tbstatistic+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="tbtraffic" value="'+tbtraffic+'" />';
        parameter+='<input type="hidden" name="item1" value="'+item1+'" /><input type="hidden" name="item2" value="'+item2+'" />';
        parameter+='<input type="hidden" name="item3" value="'+item3+'" /><input type="hidden" name="item4" value="'+item4+'" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
        //$('<form action="./pages/'+sub+'/export-all.php" method="post"><input type="hidden" name="tbname" value='+tbname+' /><input type="hidden" name="tbstatistic" value='+tbstatistic+' /><input type="hidden" name="tbtraffic" value='+tbtraffic+' /></form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出學員報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#export').click(function(){
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}
        var item1=$('#item1').val(),item2=$('#item2').val(),item3=$('#item3').val(),item4=$('#item4').val();

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
        parameter+='<input type="hidden" name="cancelitem" value="NO" />';
        parameter+='<input type="hidden" name="item1" value="'+item1+'" /><input type="hidden" name="item2" value="'+item2+'" />';
        parameter+='<input type="hidden" name="item3" value="'+item3+'" /><input type="hidden" name="item4" value="'+item4+'" />';

        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出學員報名統計表-依報名時間排序
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportbydate').click(function (){
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}
        var item1=$('#item1').val(),item2=$('#item2').val(),item3=$('#item3').val(),item4=$('#item4').val();

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
        parameter+='<input type="hidden" name="cancelitem" value="NO" />';
        parameter+='<input type="hidden" name="item1" value="'+item1+'" /><input type="hidden" name="item2" value="'+item2+'" />';
        parameter+='<input type="hidden" name="item3" value="'+item3+'" /><input type="hidden" name="item4" value="'+item4+'" />';

        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出學員取消報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportcancel').click(function(){
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}
        var item1=$('#item1').val(),item2=$('#item2').val(),item3=$('#item3').val(),item4=$('#item4').val();

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
        parameter+='<input type="hidden" name="cancelitem" value="YES" />';
        parameter+='<input type="hidden" name="item1" value="'+item1+'" /><input type="hidden" name="item2" value="'+item2+'" />';
        parameter+='<input type="hidden" name="item3" value="'+item3+'" /><input type="hidden" name="item4" value="'+item4+'" />';

        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });
    //$('#fixheadertbl').fixedHeaderTable('show', 500);
    $('#fixheadertbl').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true });
});

function getclassMember(clsid,clsname,clsarea,clsregion,clshasleader,clsfullname){
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
            if(data==0){$('#queryresult').html("查無資料!");}
            else{showtable(data,classid,classname,area,region,classfullname);}//$('#queryresult').html(data);
        }
    });
}

function showtable(data,classid,classname,area,region,classfullname){
    //$('#queryresult').html(data);return; //debug
    var joinitem1=$('#joinitem1').val();
    var joinitem2=$('#joinitem2').val();
    var item1=$('#item1').val();
    var item2=$('#item2').val();
    var item3=$('#item3').val();
    var bJoinitem1=true,bJoinitem2=true;
    if (joinitem1=="NO"){bJoinitem1=false;}
    if (joinitem2=="NO"){bJoinitem2=false;}
    var partsArray=data.split(';');
    var showdata="";
    if (partsArray.length<=0){return;}
    var trafftable=partsArray[0].split('-');

    var trafftable1=trafftable[0].split('|');
    var trafftable2=trafftable[1].split('|');

    var vCare = ["-", "行動不便", "懷孕", "氣喘心臟", "打鼾", "其他症狀"];

    var bPaid=false;
    var payitem=$('#payitem').val();//if ($('.paid').val()=="YES"){bPaid=true;}//是否要顯示繳費項目
    if(payitem=="YES"){bPaid=true;}

    var table1="<table class=\"reference\" id=\"fixheadertbl1\" style=\"width:850px\" align=\"center\">";
    table1+="<thead><tr>";

    // row1
    table1+="<th></th><th></th><th></th><th colspan=\"4\">梯次</th><th colspan=\"3\">參加</th><th>車次</th><th>車資</th><th>特殊住宿需求</th><th>備註</th>";
    if(payitem=="YES"){table1+="<th>繳費</th>";}
    table1+="</tr><tr>";

    // row2
    table1+="<th style=\"width:30px;\">序</th><th style=\"width:85px;\">姓名</th><th style=\"width:65px;\">身份</th>";
    table1+="<th style=\"width:25px;\">X</th>";
    table1+="<th style=\"width:30px;\">"+item1+"</th><th style=\"width:30px;\">"+item2+"</th><th style=\"width:30px;\">"+item3+"</th>";//<th style=\"width:25px;\">52</th>";
    table1+="<th style=\"width:35px;\">正行</th><th style=\"width:35px;\">重培</th><th style=\"width:35px;\">關懷</th>";
    table1+="<th style=\"width:220px;\"></th><th style=\"width:30px;\"></th>";//車次&車資

    table1+="<th style=\"width:85px;\"></th>";//special care
    //table1+="<th style=\"width:30px;\">行動不便</th><th style=\"width:30px;\">氣喘/心臟</th><th style=\"width:30px;\">嚴重打鼾</th><th style=\"width:30px;\">其他症狀</th>";
    var nMomeW=80;
    if(payitem=="YES"){table1+="<th style=\"width:55px;\"></th>";nMomeW=50;}else{table1+="<th style=\"width:85px;\"></th>";}//memo
    if(payitem=="YES"){table1+="<th style=\"width:30px;\"></th>";}
    table1+="</thead><tbody><tr></tr>";

    var lock=0;
    var istep=0;
    var idx=0;
    var Joinhidden1=" ",Joinhidden2="  ";
    var JoinhiddenAll=" ";
    chkJoin=" checked ";chkMeal=" checked ";chkLive=" checked ";traff="Z";tfround=" "; chkPay=" "; selected=" ";
    selected0=" ";selected1=" ";selected2=" ";

    for(w=1;w<partsArray.length;w++)
    {
        if (partsArray[w]==""){break;}
        var row = partsArray[w].split('|');
        if (row.length<5){continue;}

        Joinhidden1=" ";Joinhidden2="  ",JoinhiddenAll=" ";

        if(payitem=="YES"){lock=0;}else{lock=row[1];}//管理者可勾選繳費
        disableditem="  ";if (lock>0){disableditem=" disabled ";}
        if (bJoinitem1==false){Joinhidden1=" disabled ";if(row[4]==1){JoinhiddenAll=" disabled ";}}
        if (bJoinitem2==false){Joinhidden2=" disabled ";if(row[4]>=1&&row[4]<=2){JoinhiddenAll=" disabled ";}}

        title=row[3];
        vTitle=title.toString().replace("暫停班員", "暫停");

        var clr = "#000000"; // 8:班長 7:副班長 6:關懷員  5 : 班員 0:暫停班員
        if (row[14] == 0) { clr ="#000000";}else if (row[14]==-1){clr="#326432";}else if (row[14]>5){clr="#0000FF";}

        var daytraff=row[6].split(',');
        traff=daytraff[0];if(traff==""||typeof(traff)=="undefined"){traff="Z";}

        // keep the old data
        table1+="<tr><td id='idx_"+(w)+"'' class='idx' idx='"+row[0]+"' serial='"+(w)+"' lock='"+lock+"' regdate='"+row[9]+"'";
        table1+=" paydate='"+row[10]+"' payerid='"+row[11]+"' payername='"+row[12]+"' ojoinday='"+row[4]+"' oservice='"+row[18]+"'";
        table1+=" traffic='"+traff+"' fee='"+row[7]+"'";
        table1+=" >"+(w)+"</td>";//序-記錄相關informaion

        table1+="<td style='text-align:center;' id='student_"+(w)+"'' class='student' idx='"+row[0]+"' serial='"+(w)+"'> <font color='"+clr+"'> "+row[2]+"</font></td>";//姓名

        if (vTitle=="暫停"){table1+="<td style='text-align:center;color: rgb(255, 0, 0);'>"+vTitle+"</td>";} // 身份
        else if (vTitle=="班長"||vTitle=="副班長"){table1+="<td style='text-align:center;color: rgb(0, 0, 255);'>"+vTitle+"</td>";} // 身份
        else{table1+="<td style='text-align:center;'>"+vTitle+"</td>";} // 身份

        // 不參加
        chkNotJoin=" checked ";chkJoin="  ";disableTraff=" disabled ";
        if (row[4]>0){chkNotJoin="  ";chkJoin=" checked ";disableTraff=" ";}
        table1+="<td style='text-align:center;'><input id='notjoin_"+(w)+"' item='0' serial='"+(w)+"' idx='"+row[0]+"' class='join' type='radio'"+chkNotJoin+disableditem+JoinhiddenAll+"></td>";

        //梯次
        chkJoin1="  ";chkJoin2="  "; chkJoin3="  ";chkJoin4="  ";disableditem="  ";if (lock>0){disableditem=" disabled ";}
        if (row[4]==1){chkJoin1=" checked ";}else if (row[4]==2){chkJoin2=" checked ";}else if (row[4]==3){chkJoin3=" checked ";}else if (row[4]==4){chkJoin4=" checked ";}

        table1+="<td style='text-align:center;'><input id='join1_"+(w)+"' item='1' serial='"+(w)+"' idx='"+row[0]+"' class='join' type='radio'"+chkJoin1+disableditem+Joinhidden1+JoinhiddenAll+"></td>";
        table1+="<td style='text-align:center;'><input id='join2_"+(w)+"' item='2' serial='"+(w)+"' idx='"+row[0]+"' class='join' type='radio'"+chkJoin2+disableditem+Joinhidden2+JoinhiddenAll+"></td>";
        table1+="<td style='text-align:center;'><input id='join3_"+(w)+"' item='3' serial='"+(w)+"' idx='"+row[0]+"' class='join' type='radio'"+chkJoin3+disableditem+JoinhiddenAll+" disabled></td>";
        //table1+="<td style='text-align:center;'><input id='join4_"+(w)+"' item='4' serial='"+(w)+"' idx='"+row[0]+"' class='join' type='radio'"+chkJoin4+disableditem+JoinhiddenAll+"></td>";

        //參加
        chkJoin1="  ";chkJoin2="  "; chkJoin3="  ";disableditem="  ";if (lock>0){disableditem=" disabled ";}
        if (row[16]==1){chkJoin1=" checked ";}else if (row[16]==2){chkJoin2=" checked ";}else if (row[16]==3){chkJoin3=" checked ";}
        if (row[4]<=0){chkJoin1="  ";chkJoin2="  "; chkJoin3="  ";disableditem=" disabled ";}
        table1+="<td style='text-align:center;'><input id='type1_"+(w)+"' item='0' serial='"+(w)+"' idx='"+row[0]+"' class='type' type='radio'"+chkJoin1+disableditem+JoinhiddenAll+"></td>";
        table1+="<td style='text-align:center;'><input id='type2_"+(w)+"' item='1' serial='"+(w)+"' idx='"+row[0]+"' class='type' type='radio'"+chkJoin2+disableditem+JoinhiddenAll+"></td>";
        table1+="<td style='text-align:center;'><input id='type3_"+(w)+"' item='2' serial='"+(w)+"' idx='"+row[0]+"' class='type' type='radio'"+chkJoin3+disableditem+JoinhiddenAll+"></td>";

        // 車次
        table1 +="<td style='text-align:center;'><select style='width:110px' id='traffic1_"+(w)+"' class='traffic' serial='"+(w)+"' idx='"+row[0]+"' name='traffic"+w+"' "+disableditem+JoinhiddenAll+">";
        for(kk=0;kk<trafftable1.length;kk+=2){selected=(traff==trafftable1[kk] ? "selected":" ");table1+="<option value='"+trafftable1[kk]+"' "+selected+">"+trafftable1[kk]+"-"+trafftable1[kk+1]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
        table1+="</select>&nbsp;";

        // 趟次
        tfround=row[15];
        disableditem="  ";if (lock>0||row[4]<=0||traff=="Z"){disableditem=" disabled ";}
        selected0=(tfround==0 ? "selected":" ");selected1=(tfround==1 ? "selected":" ");selected2=(tfround==2 ? "selected":" ");
 	  table1+="<select style='width:55px' id='traffic1round_"+(w)+"' class='traffic1round' serial='"+(w)+"' idx='"+row[0]+"' name='traffic1round"+w+"' "+disableditem+JoinhiddenAll+">";
        table1+="<option value='0' "+selected0+">去回</option><option value='1' "+selected1+">去</option><option value='2' "+selected2+">回</option>";
 	  table1+="</select>";

        table1+="</td>";

        // 車資
        table1+="<td style='text-align:center;'><div style='width:30px' id='fee_"+(w)+"' class='fee' serial='"+(w)+"' idx='"+row[0]+"' name='fee"+w+"'>"+row[7]+"<div></td>";

        // 住宿特殊需求
        var spec=row[17];//.split(',');
        disableditem="  ";if (row[4]<=0){disableditem=" disabled ";}//不參加
        table1 +="<td style='text-align:center;'><select style='width:80px;' id='care_"+(w)+"' class='care' serial='"+(w)+"' idx='"+row[0]+"' name='care"+w+"' "+disableditem+JoinhiddenAll+">";
        for(kk=0;kk<vCare.length;kk++){selected=(spec==kk ? "selected":" ");table1+="<option value='"+kk+"' "+selected+">"+vCare[kk]+"</option>";}
        table1 +="</select></td>";

        //備註
        table1+="<td style='text-align:center;'><input style='width:"+nMomeW+"px' id='memo_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='memo' type='text'"+" value='"+row[13]+"'></td>";

        //繳費
        if(payitem=="YES")
        {
            chkPay="  ";if(row[8]>0){chkPay=" checked ";}
            disableditem="  ";if (row[4]<=0 || row[7]<=0){disableditem=" disabled ";}//不參加或無須繳費 - 無須繳費
            table1+="<td style='text-align:center;'><input id='pay_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='pay' type='checkbox'"+chkPay+disableditem+"></td>";
        }
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

    if (partsArray.length > 11)	{
        $('#fixheadertbl1').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true});
    }

    $('.join').click(function(event)
    {
        var serial=$(this).attr('serial');
        var item=$(this).attr('item');

        if (item==0){
            $('#join1_'+serial).prop('checked',false);$('#join2_'+serial).prop('checked',false);$('#join3_'+serial).prop('checked',false);$('#join4_'+serial).prop('checked',false);
            $('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected');$('#traffic1_'+serial).prop('disabled',true);
			$('#traffic1_'+serial).val("Z").change();

            $('#care_'+serial+' option')[0].selected=true;//$('#care_'+serial+' option[value="-"]').attr('selected', 'selected');
            $('#care_'+serial).prop('disabled',true);

            $('#traffic1round_'+serial+' option[value="0"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
			$('#traffic1round_'+serial).val("0").change();
            $('#traffic1round_'+serial).prop('disabled',true);

            $('#type1_'+serial).prop('checked',false);$('#type2_'+serial).prop('checked',false);$('#type3_'+serial).prop('checked',false);
            $('#type1_'+serial).prop('disabled',true);$('#type2_'+serial).prop('disabled',true);$('#type3_'+serial).prop('disabled',true);
            $('#fee_'+serial).text("0");
        }else{
            $('#type1_'+serial).prop('disabled',false);$('#type2_'+serial).prop('disabled',false);$('#type3_'+serial).prop('disabled',false);$('#type4_'+serial).prop('disabled',false);
            if ($('#type1_'+serial).is(':checked')==false&&$('#type2_'+serial).is(':checked')==false&&$('#type3_'+serial).is(':checked')==false)
            {$('#type1_'+serial).prop('checked',true);}
        }

        if (item==1){$('#notjoin_'+serial).prop('checked',false);$('#join2_'+serial).prop('checked',false);$('#join3_'+serial).prop('checked',false);$('#join4_'+serial).prop('checked',false);$('#traffic1_'+serial).prop('disabled',false);$('#care_'+serial).prop('disabled',false);}
        if (item==2){$('#notjoin_'+serial).prop('checked',false);$('#join1_'+serial).prop('checked',false);$('#join3_'+serial).prop('checked',false);$('#join4_'+serial).prop('checked',false);$('#traffic1_'+serial).prop('disabled',false);$('#care_'+serial).prop('disabled',false);}
        if (item==3){$('#notjoin_'+serial).prop('checked',false);$('#join1_'+serial).prop('checked',false);$('#join2_'+serial).prop('checked',false);$('#join4_'+serial).prop('checked',false);$('#traffic1_'+serial).prop('disabled',false);$('#care_'+serial).prop('disabled',false);}
        if (item==4){$('#notjoin_'+serial).prop('checked',false);$('#join1_'+serial).prop('checked',false);$('#join2_'+serial).prop('checked',false);$('#join3_'+serial).prop('checked',false);$('#traffic1_'+serial).prop('disabled',false);$('#care_'+serial).prop('disabled',false);}

        var fee=getfee(serial);
        $('#fee_'+serial).text(fee);
        if (bPaid)
        {
            if (fee<=0){$('#pay_'+serial).prop('checked',false);$('#pay_'+serial).prop('disabled',true);}
            else{$('#pay_'+serial).prop('disabled',false);}
        }
    });

    $('.type').click(function(event)
    {
        var serial=$(this).attr('serial');
        var item=$(this).attr('item');
        if (item==0){$('#type2_'+serial).prop('checked',false);$('#type3_'+serial).prop('checked',false);}
        if (item==1){$('#type1_'+serial).prop('checked',false);$('#type3_'+serial).prop('checked',false);}
        if (item==2){$('#type1_'+serial).prop('checked',false);$('#type2_'+serial).prop('checked',false);}
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
    if ($('#join1_'+index).is(':checked') || $('#join2_'+index).is(':checked') || $('#join3_'+index).is(':checked')|| $('#join4_'+index).is(':checked'))
    {
        if ($('#traffic1_'+index).val()!="Z")
        {
            if (round1==0){fee+=roundfee;}//來回
            if (round1==1){fee+=gofee;}//去
            if (round1==2){fee+=backfee;}//回
        }
    }
    if (fee<=0){$('#pay_'+index).prop('checked',false);$('#pay_'+index).prop('disabled',true);$('#notjoin_'+index).prop('disabled',false);}
    else{$('#pay_'+index).prop('disabled',false);}

    return fee;
}
