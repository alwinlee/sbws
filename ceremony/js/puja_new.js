$(document).ready(function () {
    $.ajax({
        async: false,url: "top.php",
        success: function (data) {
            $("#pageTop").append(data);
        }
    });

    // 叫出班級報名表
    $('#query').click(function () {
        getclassMember("","","","","","");
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 傳送報名
    $('#send').click(function () {
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
        var endjoin=$('.endjoin').val();
        var traffprice=$('#traffroundfee').val();

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
        var bCancelReg1=false; //取消報名
        var bCancelReg2=false; //取消報名
        var bCancelReg3=false; //取消報名

        var allsqlcmd=""; var sqlcmd=""; var joinday1=0; var joinday2=0; var joinday3=0;
        var regdate1=""; var paydate1=""; var regnewdate1=""; var paynewdate1=""; var payernewid1=""; var payernewname1="";
        var regdate2=""; var paydate2=""; var regnewdate2=""; var paynewdate2=""; var payernewid2=""; var payernewname2="";
        var regdate3=""; var paydate3=""; var regnewdate3=""; var paynewdate3=""; var payernewid3=""; var payernewname3="";
        var fee1=0; var fee2=0; var fee3=0;

        var ojoinday1=0;var ojoinday2=0;var ojoinday3=0; var otraffic1="";var otraffic2="";var otraffic3="";
        var ocancel1="";var ocancel2="";var ocancel3=""; var ofee1=0;var ofee2=0;var ofee3=0;
        var clssqlcmd=",`classname`=&#&#"+clsname+"&#&#,`CLS_ID`=&#&#"+clsid+"&#&#,`area`=&#&#"+clsarea+"&#&#,`areaid`=&#&#"+clsregion+"&#&#,`classfullname`=&#&#"+clsfullname+"&#&# ";//更新班級資料
        for(i=1;i<=nMemberCnt;i++)
        {
            bPay=false;
            lock=$('#idx_'+i).attr('lock');
            ojoinday1=$('#idx_'+i).attr('ojoinday1');otraffic1=$('#idx_'+i).attr('traffic1');ocancel1=$('#idx_'+i).attr('cancel1');ofee1=$('#idx_'+i).attr('fee1');
            ojoinday2=$('#idx_'+i).attr('ojoinday2');otraffic2=$('#idx_'+i).attr('traffic2');ocancel2=$('#idx_'+i).attr('cancel2');ofee2=$('#idx_'+i).attr('fee2');
            ojoinday3=$('#idx_'+i).attr('ojoinday3');otraffic3=$('#idx_'+i).attr('traffic3');ocancel3=$('#idx_'+i).attr('cancel3');ofee3=$('#idx_'+i).attr('fee3');
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

            regdate1=$('#idx_'+i).attr('regdate1');paydate1=$('#idx_'+i).attr('paydate1');
            payerid1=$('#idx_'+i).attr('payerid1');payername1=$('#idx_'+i).attr('payername1');
            regdate2=$('#idx_'+i).attr('regdate2');paydate2=$('#idx_'+i).attr('paydate2');
            payerid2=$('#idx_'+i).attr('payerid2');payername2=$('#idx_'+i).attr('payername2');
            //regdate3=$('#idx_'+i).attr('regdate3');paydate3=$('#idx_'+i).attr('paydate3');
            //payerid3=$('#idx_'+i).attr('payerid3');payername3=$('#idx_'+i).attr('payername3');
            if (bPaid==true){if ($('#pay_'+i).is(':checked')){bPay=true;}}

            traff1="Z";traffReal1="Z";traff2="Z";traffReal2="Z";traff3="Z";traffReal3="Z";
            paid1=0;paid2=0;paid3=0;joinday1=0;joinday2=0;joinday3=0;type=0;speccare=0;fami1=0;fami2=0;service1=0;service2=0;
            if ($('#join1_'+i).is(':checked')){joinday1=1;traff1=$("#traffic1_"+i).val();fami1=$("#fami1_"+i).val();if(bPay){traffReal1=traff1;}}
            if ($('#join2_'+i).is(':checked')){joinday2=1;traff2=$("#traffic2_"+i).val();fami2=$("#fami2_"+i).val();if(bPay){traffReal2=traff2;}}

            if ($('#service_'+i).is(':checked')){service1=1;}

            traffCnt1="0";traffRealCnt1="0";traffCnt2="0";traffRealCnt2="0";traffCnt3="0";traffRealCnt3="0";
            traffitem1=traff1+","+traffCnt1+","+traffReal1+","+traffRealCnt1;
            traffitem2=traff2+","+traffCnt2+","+traffReal2+","+traffRealCnt2;
            traffitem3=traff3+","+traffCnt3+","+traffReal3+","+traffRealCnt3;

            // 判斷是否取消報名
            bCancelReg1=false;
            if (joinday1==0){
                if (paydate1=="1970-01-01"||paydate1==""){bCancelReg1=false;}
                else{bCancelReg1=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }
            bCancelReg2=false;
            if (joinday2==0){
                if (paydate2=="1970-01-01"||paydate2==""){bCancelReg2=false;}
                else{bCancelReg2=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }
            bCancelReg3=false;
            if (joinday3==0){
                if (paydate3=="1970-01-01"||paydate3==""){bCancelReg3=false;}
                else{bCancelReg3=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }

            // 考慮 reg date & pay date
            if (joinday1==0){regnewdate1="1970-01-01";paynewdate1="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate1=regdate1;paynewdate1=paydate1;if(regdate1=="1970-01-01"||regdate1==""||regdate1=="0000-00-00"){regnewdate1=curdate;}}

            if (joinday2==0){regnewdate2="1970-01-01";paynewdate2="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate2=regdate2;paynewdate2=paydate2;if (regdate2=="1970-01-01"||regdate2==""||regdate2=="0000-00-00"){regnewdate2=curdate;}}

            if(joinday1==0&&joinday2==0&&service1>0){
                 regnewdate1=regdate1;paynewdate1=paydate1;
                 if(regdate1=="1970-01-01"||regdate1==""||regdate1=="0000-00-00"){regnewdate1=curdate;}
            }
            //if (joinday3==0){regnewdate3="1970-01-01";paynewdate3="1970-01-01";}//未報名,時間重設為1970-01-01
            //else{regnewdate3=regdate3;paynewdate3=paydate3;if (regdate3=="1970-01-01"||regdate3==""||regdate3=="0000-00-00"){regnewdate3=curdate;}}

            //fee=$('#fee_'+i).text();if (fee==""){fee=0;}// 車資
            fee1=getfee1(i);// 車資1
            fee2=getfee2(i);// 車資2
            //fee3=getfee3(i);// 車資3

            txtMemo=$('#memo_'+i).val();//備註

            //考慮 lock, paid, pay, cost if(bPay){paid=fee;}

            if (bPaid==true){//管理窗口
                lock=0;
                paid1=0;paid2=0;paid3=0;
                cancel1=0;cancel2=0;cancel3=0; //註記取消報名
                if(bPay==true)
                {
                    lock=1;paid1=fee1;paid2=fee2; paid3=fee3;
                    if (paydate1=="1970-01-01"||paydate1==""){paynewdate1=curdate;}
                    if (payerid1==""){payernewid1=payercurid;}else{payernewid1=payerid1;}
                    if (payername1==""){payernewname1=payercurname;}else{payernewname1=payername1;}

                    if (paydate2=="1970-01-01"||paydate2==""){paynewdate2=curdate;}
                    if (payerid2==""){payernewid2=payercurid;}else{payernewid2=payerid2;}
                    if (payername2==""){payernewname2=payercurname;}else{payernewname2=payername1;}

                    //if (paydate3=="1970-01-01"||paydate3==""){paynewdate3=curdate;}
                    //if (payerid3==""){payernewid3=payercurid;}else{payernewid3=payerid3;}
                    //if (payername3==""){payernewname3=payercurname;}else{payernewname3=payername1;}
                }else{
                    //paid1=0;paid2=0;paid2=0;
                    paynewdate1="1970-01-01";paynewdate2="1970-01-01";paynewdate3="1970-01-01";
                    payernewid1="";payernewid2="";payernewid3="";
                    payernewname1="";payernewname2="";payernewname3="";
                }
                cancelinfo1="";cancelinfo2="";cancelinfo3="";
                if(bCancelReg1==true&&endjoin=="YES"){cancel1=ojoinday1;cancelinfo1=ojoinday1+"#"+otraffic1+"#"+ofee1+"#"+payername1+"#"+paydate1+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}
                if(bCancelReg2==true&&endjoin=="YES"){cancel2=ojoinday2;cancelinfo2=ojoinday2+"#"+otraffic2+"#"+ofee2+"#"+payername2+"#"+paydate2+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}
                //if(bCancelReg3==true&&endjoin=="YES"){cancel3=ojoinday3;cancelinfo3=ojoinday3+"#"+otraffic3+"#"+ofee3+"#"+payername3+"#"+paydate3+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}

                sqlcmd="UPDATE `"+tbname+"` "+"SET ";
                sqlcmd+="`day1`="+joinday1+",`family1`="+fami1+",`service1`="+service1+",`traff1`=&#&#"+traffitem1+"&#&#,`cost1`="+fee1+",`lock1`="+lock+",`pay1`="+paid1+",`regdate1`=&#&#"+regnewdate1+"&#&#,";
                sqlcmd+="`paydate1`=&#&#"+paynewdate1+"&#&#,`paybyid1`=&#&#"+payernewid1+"&#&#,`paybyname1`=&#&#"+payernewname1+"&#&#,";

                sqlcmd+="`day2`="+joinday2+",`family2`="+fami2+",`service2`="+service2+",`traff2`=&#&#"+traffitem2+"&#&#,`cost2`="+fee2+",`lock2`="+lock+",`pay2`="+paid2+",`regdate2`=&#&#"+regnewdate2+"&#&#,";
                sqlcmd+="`paydate2`=&#&#"+paynewdate2+"&#&#,`paybyid2`=&#&#"+payernewid2+"&#&#,`paybyname2`=&#&#"+payernewname2+"&#&#,";

                sqlcmd+="`memo1`=&#&#"+txtMemo+"&#&#";
                if(cancel1>0){sqlcmd+=(",`cancel1`="+cancel1+",`cancelinfo1`=&#&#"+cancelinfo1+"&#&#");}else if(ocancel1>0&&paid1>0){sqlcmd+=(",`cancel1`=0");}//取消了又繳費
                if(cancel2>0){sqlcmd+=(",`cancel2`="+cancel2+",`cancelinfo2`=&#&#"+cancelinfo2+"&#&#");}else if(ocancel2>0&&paid2>0){sqlcmd+=(",`cancel2`=0");}//取消了又繳費
                if(cancel3>0){sqlcmd+=(",`cancel3`="+cancel3+",`cancelinfo3`=&#&#"+cancelinfo3+"&#&#");}else if(ocancel3>0&&paid3>0){sqlcmd+=(",`cancel3`=0");}//取消了又繳費

                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
            }else{//幹部一般報名
                sqlcmd="UPDATE `"+tbname+"` "+"SET ";
                sqlcmd+="`day1`="+joinday1+",`family1`="+fami1+",`service1`="+service1+",`traff1`=&#&#"+traffitem1+"&#&#,`cost1`="+fee1+",`regdate1`=&#&#"+regnewdate1+"&#&#,";
                sqlcmd+="`day2`="+joinday2+",`family2`="+fami2+",`service2`="+service2+",`traff2`=&#&#"+traffitem2+"&#&#,`cost2`="+fee2+",`regdate2`=&#&#"+regnewdate2+"&#&#,";
                sqlcmd+="`memo1`=&#&#"+txtMemo+"&#&#";
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
    // 匯出車資繳費單
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
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();
        //alert(trafftb+","+tbname+","+classname);
        //return;
        if (classname == "" || tbname == ""){alert("未取得班級資料!");return;}
        //alert(classname);
        var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';
        parameter+='<input type="hidden" name="pujaid" value="'+sub+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" />';
        parameter+='<input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/register-list-invoice.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出已繳費名冊
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#paylist').click(function () {
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
        
        if (classname == "" || tbname == ""){alert("未取得班級資料!");return;}
        //alert(classname);
        var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';
        parameter+='<input type="hidden" name="pujaid" value="'+sub+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" />';
        parameter+='<input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/pay-list.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
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
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();

        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
        parameter+='<input type="hidden" name="cancelitem" value="NO" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" />';
        parameter+='<input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
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
        var day1title=$('#day1title').val();
        var day2title=$('#day2title').val();

        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
        parameter+='<input type="hidden" name="cancelitem" value="NO" />';
        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" />';
        parameter+='<input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-class.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
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

    var sub=$('.sub').val();
    var tb = $('.tb').val();
    var trafftb = $('.trafftb').val();
    var Major = $('.Major').val();
    var detailinfo=$('#detailinfo').val();
    var leaderinfo=$('#leaderinfo').val();
    var volunteerinfo=$('#volunteerinfo').val();

    if (classname=='-'){alert("尚未指定班級!");return;}
    // 送出查詢關鍵字至後端
    $.ajax({
        async: false,
        url: "./pages/"+sub+"/queryquery.php",
        cache: false,
        dataType: 'html',
        type:'POST',
        data:{classname:classname, classid:classid, tbname:tb, trafftbname:trafftb,Major:Major,detailinfo:detailinfo,leaderinfo:leaderinfo,volunteerinfo:volunteerinfo,area:area,region:region,classfullname:classfullname},
        error: function (data) {
            alert("查詢班級資料失敗!!!");//$('#queryresult').html(data);
        },success: function (data)
        {
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

    var payitem=$('#payitem').val(); //classid = $(this).val();
    var trafftable=partsArray[0].split('-');
    var trafftable1=trafftable[0].split('|');
    if (trafftable.length>=2){var trafftable2=trafftable[1].split('|');}
    var day1title=$('#day1title').val();
    var day2title=$('#day2title').val();

    var fami=[];
    for(x=0;x<20;x++){fami.push(x);}
    // Head ROW1
    var table1="<table style='font-size:11pt;' class=\"reference\" id=\"fixheadertbl1\" style=\"width:800px\" align=\"center\">";
    table1+="<thead><tr>";
    table1+="<th></th><th></th><th></th>";//序	姓名	身份
    table1+="<th></th><th></th>";// X 義工
    table1+="<th colspan=\"3\">"+day1title+"</th>";
    //table1 +="<th colspan=\"3\">"+day2title+"</th>";
    table1+="<th></th>";
    table1+="<th></th><th></th>";
    if(payitem=="YES"){table1+="<th></th>";}

    // HEAD ROW2
    /*
    table1+="</tr><tr>";
    table1+="<th></th>";
    table1+="<th></th>";
    table1+="<th></th>";
    table1+="<th></th>";
    table1+="<th>12/5(六)</th><th>12/6(日)</th><th>12/6(日)</th>";//<th style=\"width:30px;\">訂餐</th>";
    table1+="<th></th>";
    table1+="<th></th><th></th>";
    if(payitem=="YES"){table1+="<th style=\"width:30px;\"></th>";}
    */
    // HEAD ROW3
    table1+="</tr><tr>";
    table1+="<th style=\"width:25px;\">序</th>";
    table1+="<th style=\"width:92px;\">姓名</th>";
    table1+="<th style=\"width:45px;\">身份</th>";
    table1+="<th style=\"width:25px;\">X</th>";
    table1+="<th style=\"width:35px;\">義工</th>";

    table1+="<th style=\"width:40px;\">學員</th>";
    table1+="<th style=\"width:50px;\">眷屬</th>";
    table1+="<th style=\"width:180px;\">交通車次</th>";

    //table1+="<th style=\"width:35px;\">學員</th>";
    //table1+="<th style=\"width:50px;\">眷屬</th>";
    //table1+="<th style=\"width:115px;\">交通車次</th>";

    table1+="<th style=\"width:40px;\">車資</th><th style=\"width:210px;\">備註</th>";
    if(payitem=="YES"){table1+="<th style=\"width:30px;\">繳費</th>";}
    table1+="</thead><tbody><tr></tr>";

    var istep=0;
    var idx=0;
    var lock=0;
    chkJoin=" checked ";chkMeal=" checked ";chkLive=" checked ";traff="Z";tfround=" "; chkPay=" "; selected=" ";
    selected0=" ";selected1=" ";selected2=" ";

    for(w=1;w<partsArray.length;w++)
    {
        if (partsArray[w]==""){break;}
        var row = partsArray[w].split('|');
        if (row.length<5){continue;}

        lock=0;
        if(payitem=="YES"){lock=0;}else{lock=row[11];}//管理者可勾選繳費
        disabledlock="  ";if (lock>=1){disabledlock=" disabled ";}

        day1=parseInt(row[12]);
        day2=parseInt(row[32]);
        day=day1+(day2*10);
        idx=parseInt(row[0]);

        service1=parseInt(row[15]);//以service1為主
        service2=parseInt(row[25]);

        fami1=parseInt(row[14]);
        fami2=parseInt(row[34]);

        cost1=parseInt(row[19]);
        cost2=parseInt(row[39]);
        cost=cost1+cost2;

        var daytraff1=row[18].split(',');
        traff1=daytraff1[0];if(traff1==""||typeof(traff1)=="undefined"){traff1="Z";}
        var daytraff2=row[38].split(',');
        traff2=daytraff2[0];if(traff2==""||typeof(traff2)=="undefined"){traff2="Z";}
        var daytraff3=row[58].split(',');
        traff3=daytraff3[0];if(traff3==""||typeof(traff3)=="undefined"){traff3="Z";}
        // keep the old data
        table1+="<tr><td id='idx_"+(w)+"'' class='idx' idx='"+idx+"' serial='"+(w)+"' lock='"+lock+"'";
        table1+=" regdate1='"+row[22]+"' regdate2='"+row[42]+"'  regdate3='"+row[62]+"'";
        table1+=" paydate1='"+row[23]+"' payerid1='"+row[25]+"' payername1='"+row[26]+"' ojoinday1='"+row[12]+"'";
        table1+=" paydate2='"+row[43]+"' payerid2='"+row[45]+"' payername2='"+row[46]+"' ojoinday2='"+row[32]+"'";
        table1+=" paydate3='"+row[63]+"' payerid3='"+row[65]+"' payername3='"+row[66]+"' ojoinday3='"+row[52]+"'";
        table1+=" traffic1='"+traff1+"' fee1='"+row[19]+"' cancel1='"+row[27]+"' cancelinfo1='"+row[28]+"'";
        table1+=" traffic2='"+traff2+"' fee2='"+row[39]+"' cancel2='"+row[47]+"' cancelinfo2='"+row[48]+"'";
        table1+=" traffic3='"+traff3+"' fee3='"+row[59]+"' cancel3='"+row[67]+"' cancelinfo3='"+row[68]+"'";
        table1+=" style='text-align:center;'>"+(w)+"</td>";//序-記錄相關informaion


        table1+="<td style='text-align:center;' id='student_"+(w)+"'' class='student' idx='"+row[1]+"' serial='"+(w)+"'>"+row[1]+"</td>";//姓名
    	table1+="<td style='text-align:center;'>"+row[2]+"</td>";//身份

    	disableditem="  ";if (day>0){disableditem=" disabled ";}

        // 不參加
        chkNotJoin=" checked ";chkJoin="  ";disableTraff=" disabled ";
        if(day>0){chkNotJoin="  ";chkJoin=" checked ";disableTraff=" ";}
        table1+="<td style='text-align:center;'><input id='notjoin_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='notjoin' type='radio'"+chkNotJoin+disabledlock+"></td>";

        // 義工
        chkJoin="  ";disableditem=" disabled ";
        if (service1>0){chkJoin=" checked ";}else{chkJoin=" ";}
        if(day>=0){disableditem=" ";}
        table1+="<td style='text-align:center;'><input id='service_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='service' type='checkbox'"+chkJoin+disableditem+disabledlock+"></td>";

        //場1學員眷屬&交通
        chkJoin="  ";disableditem=" disabled ";
        if (day1>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        table1+="<td style='text-align:center;'><input id='join1_"+(w)+"' serial='"+(w)+"' Item=1 idx='"+idx+"' class='join' type='checkbox'"+chkJoin+disabledlock+"></td>";

        table1+="<td style='text-align:center;'><select style='width:45px' id='fami1_"+(w)+"' class='fami' serial='"+(w)+"' idx='"+idx+"' name='fami"+w+"' "+disableditem+disabledlock+">";
        for(kk=0;kk<fami.length;kk++){selected=(fami1==fami[kk] ? "selected":" ");table1+="<option value='"+fami[kk]+"' "+selected+">"+fami[kk]+"</option>";}
        table1 +="</td>";

        if (day1>0){disableditem=" ";}
        table1+="<td style='text-align:center;'><select style='width:175px' id='traffic1_"+(w)+"' class='traffic' serial='"+(w)+"' Item=1 idx='"+idx+"' name='traffic"+w+"' "+disableditem+disabledlock+">";
        for(kk=0;kk<trafftable1.length;kk+=2){selected=(traff1==trafftable1[kk] ? "selected":" ");table1+="<option value='"+trafftable1[kk]+"' "+selected+">"+trafftable1[kk]+" - "+trafftable1[kk+1]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
        table1 +="</td>";

        //場2學員眷屬&交通
        /*
        chkJoin="  ";disableditem=" disabled ";
        if (day2>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        table1+="<td style='text-align:center;'><input id='join2_"+(w)+"' serial='"+(w)+"' Item=2 idx='"+idx+"' class='join' type='checkbox'"+chkJoin+disabledlock+"></td>";

        table1+="<td style='text-align:center;'><select style='width:45px' id='fami2_"+(w)+"' class='fami' serial='"+(w)+"' idx='"+idx+"' name='fami"+w+"' "+disableditem+disabledlock+">";
        for(kk=0;kk<fami.length;kk++){selected=(fami2==fami[kk] ? "selected":" ");table1+="<option value='"+fami[kk]+"' "+selected+">"+fami[kk]+"</option>";}
        table1 +="</td>";

        if (day2>0){disableditem=" ";}
        table1+="<td style='text-align:center;'><select style='width:110px' id='traffic2_"+(w)+"' class='traffic' serial='"+(w)+"' Item=2 idx='"+idx+"' name='traffic"+w+"' "+disableditem+disabledlock+">";
        for(kk=0;kk<trafftable2.length;kk+=2){selected=(traff2==trafftable2[kk] ? "selected":" ");table1+="<option value='"+trafftable2[kk]+"' "+selected+">"+trafftable2[kk]+" - "+trafftable2[kk+1]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
        table1 +="</td>";
        */

        //車資
        table1+="<td style='text-align:center;'><div style='width:40px;' id='fee_"+(w)+"' class='fee' serial='"+(w)+"' idx='"+idx+"' name='fee"+w+"'>"+cost+"<div></td>";

        //備註
        //var memo=row[29].replace("\'", "&#039;");
        table1+="<td style='text-align:center;'><input style='width:200px' id='memo_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='memo' type='text'"+" value='"+row[29]+"'></td>";

        //繳費
        if(payitem=="YES") {
            chkPay="  ";if (row[20]>0||row[40]>0){chkPay=" checked ";}
            disableditem="  ";if (day<=0||cost<=0){disableditem=" disabled ";}//不參加或無須繳費 - 無須繳費
            table1+="<td style='text-align:center;'><input id='pay_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='pay' type='checkbox'"+chkPay+disableditem+">";
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
    $('#fixheadertbl1').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true});

    $('.notjoin').click(function(event)
    {
        var serial=$(this).attr('serial');
        $('#join1_'+serial).prop('checked',false);
        $('#join2_'+serial).prop('checked',false);
        $('#join3_'+serial).prop('checked',false);

        //$('#service_'+serial).prop('checked',false);$('#service_'+serial).prop('disabled',true);

        $('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
        $('#traffic1_'+serial).val("Z").change();
        $('#traffic1_'+serial).prop('disabled',true);
        $('#traffic2_'+serial+' option[value="Z"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
        $('#traffic2_'+serial).val("Z").change();
        $('#traffic2_'+serial).prop('disabled',true);

        $('#fami1_'+serial+' option[value="0"]').attr('selected', 'selected');
        $('#fami1_'+serial).val("0").change();
        $('#fami1_'+serial).prop('disabled',true);
        $('#fami2_'+serial+' option[value="0"]').attr('selected', 'selected');
        $('#fami2_'+serial).val("0").change();
        $('#fami2_'+serial).prop('disabled',true);

        $('#fee_'+serial).text("0");
        $('#pay_'+serial).prop('checked',false);
        $('#pay_'+serial).prop('disabled',true);
    });

    $('.join').click(function(event)
    {
        var serial=$(this).attr('serial');
        var item=$(this).attr('Item');

        var day1=$("#join1_"+serial).is(':checked') ? 1 : 0;
        var day2=$("#join2_"+serial).is(':checked') ? 1 : 0;

        if((day1+day2)<=0){
            $('#notjoin_'+serial).prop('checked',true);
            $('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected');
            $('#traffic1_'+serial).val("Z").change();
            $('#traffic1_'+serial).prop('disabled',true);
            $('#traffic2_'+serial+' option[value="Z"]').attr('selected', 'selected');
            $('#traffic2_'+serial).val("Z").change();
            $('#traffic2_'+serial).prop('disabled',true);
            $('#fee_'+serial).text("0");
            $('#pay_'+serial).prop('checked',false);
            $('#pay_'+serial).prop('disabled',true);

            $('#service_'+serial).prop('checked',false);
            $('#service_'+serial).prop('disabled',true);
        }else{
            $('#service_'+serial).prop('disabled',false);
            $('#notjoin_'+serial).prop('checked',false);
        }

        if(item==1){
            if(day1<=0){
                $('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected');
                $('#traffic1_'+serial).val("Z").change();
                $('#traffic1_'+serial).prop('disabled',true);
                $('#fami1_'+serial+' option[value="0"]').attr('selected', 'selected');
                $('#fami1_'+serial).val("0").change();
                $('#fami1_'+serial).prop('disabled',true);
            }else{
                $('#traffic1_'+serial).prop('disabled',false);
                $('#fami1_'+serial).prop('disabled',false);
            }
        }

        if(item==2){
            if(day2<=0){
                $('#traffic2_'+serial+' option[value="Z"]').attr('selected', 'selected');
                $('#traffic2_'+serial).val("Z").change();
                $('#traffic2_'+serial).prop('disabled',true);
                $('#fami2_'+serial+' option[value="0"]').attr('selected', 'selected');
                $('#fami2_'+serial).val("0").change();
                $('#fami2_'+serial).prop('disabled',true);
            }else{
                $('#traffic2_'+serial).prop('disabled',false);
                $('#fami2_'+serial).prop('disabled',false);
            }
        }

        $('#fee_'+serial).text(getfee(serial));
    });

    $('.traffic').on('change', function ()
    {
        //var item=$(this).attr('Item');
        var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
        $('#fee_'+serial).text(getfee(serial));
    });

    $('.fami').on('change', function ()
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

function getfee(index){
    var fee=0;
    var traffroundfee=parseInt($('.traffroundfee').val());
    var traffgofee=parseInt($('.traffgofee').val());
    var traffbackfee=parseInt($('.traffbackfee').val());
    var traffoverdayfee=parseInt($('.traffoverdayfee').val());

    var fami1=parseInt($('#fami1_'+index).val());
    var fami2=parseInt($('#fami2_'+index).val());

    if ($('#join1_'+index).is(':checked')){if ($('#traffic1_'+index).val()!="Z"&&$('#traffic1_'+index).val()!="ZZ"){fee+=(traffroundfee*(fami1+1));}}
    if ($('#join2_'+index).is(':checked')){if ($('#traffic2_'+index).val()!="Z"&&$('#traffic2_'+index).val()!="ZZ"){fee+=(traffroundfee*(fami2+1));}}
    //if ($('#join3_'+index).is(':checked')){if ($('#traffic1_'+index).val()!="Z"){fee+=traffroundfee;}}

    if (fee<=0){$('#pay_'+index).prop('checked',false);$('#pay_'+index).prop('disabled',true);}
    else{$('#pay_'+index).prop('disabled',false);}
    return fee;
}

function getfee1(index){
    var fee=0;
    var traffroundfee=parseInt($('.traffroundfee').val());
    var traffgofee=parseInt($('.traffgofee').val());
    var traffbackfee=parseInt($('.traffbackfee').val());
    var traffoverdayfee=parseInt($('.traffoverdayfee').val());

    var fami1=parseInt($('#fami1_'+index).val());
    if ($('#join1_'+index).is(':checked')){if ($('#traffic1_'+index).val()!="Z"&&$('#traffic1_'+index).val()!="ZZ"){fee+=(traffroundfee*(fami1+1));}}

   return fee;
}

function getfee2(index){
    var fee=0;
    var traffroundfee=parseInt($('.traffroundfee').val());
    var traffgofee=parseInt($('.traffgofee').val());
    var traffbackfee=parseInt($('.traffbackfee').val());
    var traffoverdayfee=parseInt($('.traffoverdayfee').val());

    var fami2=parseInt($('#fami2_'+index).val());
    if ($('#join2_'+index).is(':checked')){if ($('#traffic2_'+index).val()!="Z"&&$('#traffic2_'+index).val()!="ZZ"){fee+=(traffroundfee*(fami2+1));}}
   return fee;
}
