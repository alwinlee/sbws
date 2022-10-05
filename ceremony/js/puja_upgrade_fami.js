$(document).ready(function() {
    $.ajax({
        async: false,
        url: "top.php",
        success: function (data) {
            $("#pageTop").append(data);
        }
    });

    // 叫出班級報名表
    $('#query').click(function(){
        getclassMember("","","","","","");
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 傳送報名
    $('#send').click(function(){
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
        var tagnotjoin=$('.endjoin').val();
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
        if (clsregion==null||typeof clsregion==='undefined'||clsregion==""){alert("未取得班級資訊!");return ;}

        // 檢查是否有未指定場次
        var dayitem = $('#joinday').val();
        var day1title = $('#day1title').val();
        var day2title = $('#day2title').val();
        for(i=1;i<=nMemberCnt;i++) {
            if ($('#join1_'+i).is(':checked')){if ($('#place1_'+i).val()==0){alert($('#student_'+i).text()+"-未指定 "+day1title+" 參加場次!");return;}}
            if ($('#join2_'+i).is(':checked')){if ($('#place2_'+i).val()==0){alert($('#student_'+i).text()+"-未指定 "+day2title+" 參加場次!");return;}}
        }

        var bCancelReg1=false; //取消報名-第一天
        var bCancelReg2=false; //取消報名-第一天
        var bPaid=false; if($('.payitem').val()=="YES"){bPaid=true;}//是否要顯示繳費項目
        var allsqlcmd=""; var sqlcmd=""; var joinday=0; var regdate=""; var paydate=""; var regnewdate=""; var paynewdate=""; var payernewid=""; var payernewname="";

        var clssqlcmd=",`classname`=&#&#"+clsname+"&#&#,`CLS_ID`=&#&#"+clsid+"&#&#,`area`=&#&#"+clsarea+"&#&#,`areaid`=&#&#"+clsregion+"&#&#,`classfullname`=&#&#"+clsfullname+"&#&# ";//更新班級資料
        for(i=1;i<=nMemberCnt;i++) {
            bPay=false;
            lock=$('#idx_'+i).attr('lock');
            ojoinday=$('#idx_'+i).attr('ojoinday');
            otraffic=$('#idx_'+i).attr('traffic');
            ofee=$('#idx_'+i).attr('fee');
            oservice=$('#idx_'+i).attr('service');
            ootherinfo=$('#idx_'+i).attr('otherinfo');
            idx=$('#notjoin_'+i).attr('idx');//取得idx

            if (lock==1&&bPaid==false){//已經 lock且不是管理者無繳費確認功能=>不用處理此 record
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

            traff = "Z,Z"; traffReal ="Z,Z";paid=0;joinday=0;meal=0;family = 0;
            if ($('#join1_'+i).is(':checked')){joinday+=parseInt($('#place1_'+i).val());}
            if ($('#meal1_' + i).val() > 0) { meal += parseInt($('#meal1_' + i).val());}
            if ($('#fami1_' + i).val() > 0) { family += parseInt($('#fami1_' + i).val()); }

            if ($('#join2_'+i).is(':checked')){joinday+=(parseInt($('#place2_'+i).val())*100);}
            if ($('#meal2_' + i).val() > 0) { meal += (parseInt($('#meal2_' + i).val()) * 100); }
            if ($('#fami2_' + i).val() > 0) { family += (parseInt($('#fami2_' + i).val()) * 100); }
            //if ($('#join4_'+i).is(':checked')){joinday+=1000;traff="Z";traffReal="Z";}

            // 判斷是否取消報名-第一天
            bCancelReg1=false;
            if ((joinday%100)==0&&(ojoinday%100)>0&&tagnotjoin=="YES"){
                if (paydate=="1970-01-01"||paydate==""){bCancelReg1=false;}
                else{bCancelReg1=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }

            // 判斷是否取消報名-第二天
            bCancelReg2=false;
            if ((joinday<100)&&(ojoinday>=100)&&tagnotjoin=="YES"){
                if (paydate=="1970-01-01"||paydate==""){bCancelReg2=false;}
                else{bCancelReg2=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }

            // 考慮 reg date & pay date
            if (joinday==0){regnewdate="1970-01-01";paynewdate="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate=regdate;paynewdate=paydate;if (regdate=="1970-01-01"||regdate==""){regnewdate=curdate;}}

            fee=$('#fee_'+i).text();if (fee==""){fee=0;}// 車資
            txtMemo=$('#memo_'+i).val();//備註

            srvice=0;
            service1=0;
            service2=0;
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

                sqlcmd = "UPDATE `" + tbname + "` " + "SET `day`=" + joinday + ",`meal`=" + meal + ",`family`=" + family+",`traff`=&#&#"+traff+"&#&#,`traffReal`=&#&#"+traffReal+"&#&#,";
                sqlcmd+="`cost`="+fee+",`lock`="+lock+",`pay`="+paid+",`regdate`=&#&#"+regnewdate+"&#&#,`paydate`=&#&#"+paynewdate+"&#&#,";
                sqlcmd+="`paybyid`=&#&#"+payernewid+"&#&#,`paybyname`=&#&#"+payernewname+"&#&#,`memo`=&#&#"+txtMemo+"&#&#";

                // 取消報名 or 重新報名
                otherinfo="";
                if(bCancelReg1==true){service1=1}else{service1=(oservice%100);}
                if(bCancelReg2==true){service2=10}else{service2=(oservice>=10?10:0);}
                if((joinday%100)>0 && paid>0){service1=0;} //考慮恢復報名
                if(joinday>10 && paid>0){service2=0;}
                service=parseInt(service1)+parseInt(service2);
                if(tagnotjoin=="YES"){sqlcmd+=(",`service`="+service);}

                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
            }else{//幹部一般報名
                sqlcmd = "UPDATE `" + tbname + "` " + "SET `day`=" + joinday + ",`meal`=" + meal + ",`family`=" + family +",`traff`=&#&#"+traff+"&#&#,`traffReal`=&#&#"+traffReal+"&#&#,`cost`="+fee+",`regdate`=&#&#"+regnewdate+"&#&#,`memo`=&#&#"+txtMemo+"&#&#";
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
            }

            allsqlcmd+=sqlcmd;
            allsqlcmd+=";;;;"; //allsqlcmd+="<br>";//debug
        }

        //$('#msg').html(allsqlcmd);return;	 // debug show info
        $.ajax({
            async: false,
            url: "./pages/"+sub+"/register.php",
            cache: false,
            dataType: 'html',
            type:'POST',
            data:{sqlcommand:allsqlcmd},
            error: function (data) {
                alert("失敗!!!");
            },
            success: function (data) {
                //alert(data);//$('#msg').html(data);	 // debug show info
                if (data < 0){alert("報名失敗(錯誤代碼:"+data+")!");}
                else{alert("報名成功!");getclassMember(clsid,clsname,clsarea,clsregion,clshasleader,clsfullname);}
            }
        });
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出車資繳費單
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
        //alert(classname);
        var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';
        parameter+='<input type="hidden" name="pujaid" value="'+sub+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />"';
        $('<form action="./pages/'+sub+'/register-list-invoice.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

	//-----------------------------------------------------------------------------------------------------------------------------------
	// 匯出已繳費名冊
	//-----------------------------------------------------------------------------------------------------------------------------------
	$('#paylist').click(function(){
		var classid=$('#classid').val(); //classid = $(this).val();
		var classname=$('#classid :selected').text();
		var region=$('#classid :selected').attr('regioncode');
		var area=$('#classid :selected').attr('AREAID');//alert(area);return;
		var tbname     = $("#tb").val();
        var tbtraffic  = $("#traffictb").val();
		var sub=$('.sub').val();
		if (classname == "" || tbname == "" || tbtraffic=="")
		{
		    alert("未取得班級報名及搭車資料!");
		    return;
		}

        //alert(classname);
        $('<form action="./pages/'+sub+'/pay-list.php" method="post"><input type="hidden" name="classname" value='+classname+' /><input type="hidden" name="tbname" value='+tbname+' /><input type="hidden" name="tbtraffic" value='+tbtraffic+' /></form>').appendTo('body').submit().remove();
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
        var joinplace = $('#joinplace').val();
        var day1title = $('#day1title').val();
        var day2title = $('#day2title').val();
        var day3title = $('#day3title').val();
        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}
        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter += '<input type="hidden" name="joinplace" value="' + joinplace + '" /><input type="hidden" name="day1title" value="' + day1title + '" />';
        parameter += '<input type="hidden" name="day2title" value="' + day2title + '" /><input type="hidden" name="day3title" value="' + day3title + '" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
        parameter+='<input type="hidden" name="cancelitem" value="NO" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出班級報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#classexport').click(function(){
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();

        if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

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
    var trafftable2=trafftable[1].split('|');

    var item = $('#joinplace').val();
    var place_array = item.split(';');

    var dayitem = $('#joinday').val();
    var day1title = $('#day1title').val();
    var day2title = $('#day2title').val();

    var table1 ="<table class='reference' id='fixheadertbl1' style='width:800px;font-size:12px;' align='center'>";
    table1 +="<thead><tr>";
    table1+="<th></th><th></th><th></th><th></th><th colspan='4'>"+day1title+"</th>";
    table1+="<th colspan='4'>"+day2title+"</th>";
    table1+="<th>車資</th><th>備註</th>";
    if(payitem=="YES"){table1+="<th>繳費</th>";}
    table1+="</tr><tr>";
    table1+="<th style='width:30px;'>序</th>";
    table1+="<th style='width:70px;'>姓名</th>";
    table1+="<th style='width:50px;'>身份</th>";
    table1+="<th style='width:35px;'>X</th>";
    table1 +="<th style='width:35px;'>參加</th><th style='width:40px;'>眷屬</th><th style='width:40px;'>用餐</th><th style='width:90px;'>場次</th>";//<th style='width:30px;'>訂餐</th>";
    table1 +="<th style='width:35px;'>參加</th><th style='width:40px;'>眷屬</th><th style='width:40px;'>用餐</th><th style='width:90px;'>場次</th>";//<th style='width:30px;'>訂餐</th>";
    table1+="<th style='width:50px;'></th><th style='width:70px;'></th>";
    if(payitem=="YES"){table1+="<th style='width:30px;'></th>";}
    table1+="</thead><tbody><tr></tr>";

    var istep=0;
    var idx=0;
    var lock=0;
    chkJoin=" checked ";chkMeal=" checked ";chkLive=" checked ";traff="Z";tfround=" "; chkPay=" "; selected=" ";
    selected0=" ";selected1=" ";selected2=" ";
    var fami = [], mea = [];
    for (x = 0; x < 8; x++) { fami.push(x);}
    for (x = 0; x <= 8; x++) { mea.push(x); }
    for(w=1;w<partsArray.length;w++)
    {
        if (partsArray[w]==""){break;}
        var row = partsArray[w].split('|');
        if (row.length<5){continue;}

        if(payitem=="YES"){lock=0;}else{lock=row[1];}//管理者可勾選繳費
        disabledlock="  ";if (lock>=1){disabledlock=" disabled ";}

        // keep the old data
        table1+="<tr><td id='idx_"+(w)+"'' class='idx' idx='"+row[0]+"' serial='"+(w)+"' lock='"+lock+"' regdate='"+row[9]+"'";
        table1+=" paydate='"+row[10]+"' payerid='"+row[11]+"' payername='"+row[12]+"' ojoinday='"+row[4]+"'";
        table1+=" traffic='"+traff+"' fee='"+row[7]+"' service='"+row[16]+"' otherinfo='"+row[17]+"'";
        table1+=" >"+(w)+"</td>";//序-記錄相關informaion

        table1+="<td style='text-align:center;' id='student_"+(w)+"'' class='student' idx='"+row[0]+"' serial='"+(w)+"'>"+row[2]+"</td>";//姓名
	    table1+="<td style='text-align:center;'>"+row[3]+"</td>";

	    disableditem="  ";if (row[4]>0){disableditem=" disabled ";}
        // 不參加
        chkNotJoin=" checked ";chkJoin="  ";disableTraff=" disabled ";
        if (row[4]>0){chkNotJoin="  ";chkJoin=" checked ";disableTraff=" ";}
        table1+="<td style='text-align:center;'><input id='notjoin_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='notjoin' type='radio'"+chkNotJoin+disabledlock+"></td>";

        // 參加 day1  & 場次 & 車次 01~09
        chkJoin="  ";disableditem=" disabled ";
        day=parseInt(row[4]);
        day1=day%100;
        day2=(day-day1)/100;
	    meal=parseInt(row[5]);
        meal1=meal%100;
        meal2=(meal-meal1)/100;
        famix = parseInt(row[6]);
        fami1 = famix % 100;
        fami2 = (famix - fami1) / 100;
        //參加
        if (day1>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}
        table1+="<td style='text-align:center;'><input id='join1_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='join1' type='checkbox'"+chkJoin+disabledlock+"></td>";

        // 眷屬
        table1 += "<td style='text-align:center;'><select style='width:35px' id='fami1_" + (w) + "' class='fami' serial='" + (w) + "' idx='" + idx + "' name='fami" + w + "' " + disableditem + disabledlock + ">";
        for (kk = 0; kk < fami.length; kk++) { selected = (fami1 == fami[kk] ? "selected" : " "); table1 += "<option value='" + fami[kk] + "' " + selected + ">" + fami[kk] + "</option>"; }
        table1 += "</td>";

		//用餐
		chkJoin="  ";
		if (meal1>0){chkNotJoin="  ";chkJoin=" checked ";}
        table1 += "<td style='text-align:center;'><select style='width:35px' id='meal1_" + (w) + "' class='meal1' serial='" + (w) + "' idx='" + idx + "' name='meal" + w + "' " + disableditem + disabledlock + ">";
        for (kk = 0; kk < mea.length; kk++) { selected = (meal1 == mea[kk] ? "selected" : " "); table1 += "<option value='" + mea[kk] + "' " + selected + ">" + mea[kk] + "</option>"; }
        table1 += "</td>";

        //場次
        table1 +="<td style='text-align:center;'><select style='width:85px' id='place1_"+(w)+"' class='place1' serial='"+(w)+"' idx='"+row[0]+"' name='place"+w+"' "+disableditem+disabledlock+">";
        for(kk=0;kk<place_array.length;kk++){if(place_array[kk]==""){continue;}sts=(day1==kk ? "selected":" ");table1+="<option value='"+(kk)+"' "+sts+">"+place_array[kk]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
        table1 +="</td>";



        //chkJoin="  ";
        //if ((row[5]%10)==1){chkJoin=" checked ";}
        //table1+="<td style='text-align:center;'><input id='meal1_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='meal1' type='checkbox'"+chkJoin+disableditem+"></td>";

        // 參加 day2 & 場次 & 車次 10~90
        chkJoin="  ";disableditem=" disabled ";
        if (day2>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}
        table1+="<td style='text-align:center;'><input id='join2_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='join2' type='checkbox'"+chkJoin+disabledlock+"></td>";

        // 眷屬
        table1 += "<td style='text-align:center;'><select style='width:35px' id='fami2_" + (w) + "' class='fami' serial='" + (w) + "' idx='" + idx + "' name='fami" + w + "' " + disableditem + disabledlock + ">";
        for (kk = 0; kk < fami.length; kk++) { selected = (fami2 == fami[kk] ? "selected" : " "); table1 += "<option value='" + fami[kk] + "' " + selected + ">" + fami[kk] + "</option>"; }
        table1 += "</td>";

        //用餐
        chkJoin="  ";
        if (meal2>0){chkNotJoin="  ";chkJoin=" checked ";}
        table1 += "<td style='text-align:center;'><select style='width:35px' id='meal2_" + (w) + "' class='meal2' serial='" + (w) + "' idx='" + idx + "' name='meal" + w + "' " + disableditem + disabledlock + ">";
        for (kk = 0; kk < mea.length; kk++) { selected = (meal2 == mea[kk] ? "selected" : " "); table1 += "<option value='" + mea[kk] + "' " + selected + ">" + mea[kk] + "</option>"; }
        table1 += "</td>";
        // 場次
        table1 +="<td style='text-align:center;'><select style='width:85px' id='place2_"+(w)+"' class='place2' serial='"+(w)+"' idx='"+row[0]+"' name='place"+w+"' "+disableditem+disabledlock+">";
        for(kk=0;kk<place_array.length;kk++){if(place_array[kk]==""){continue;}sts=(day2==kk ? "selected":" ");table1+="<option value='"+(kk)+"' "+sts+">"+place_array[kk]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
        table1 +="</td>";

        // 參加 day3 & 4 - 高雄場次
        /*
        chkJoin="  ";disableditem=" disabled ";
        if ((row[4]%1000)>=100){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}
        table1+="<td style='text-align:center;'><input id='join3_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='join3' type='checkbox'"+chkJoin+disabledlock+"></td>";

        chkJoin="  ";disableditem=" disabled ";
        if ((row[4]%10000)>=1000){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}
        table1+="<td style='text-align:center;'><input id='join4_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='join4' type='checkbox'"+chkJoin+disabledlock+"></td>";
        */
        //chkJoin="  ";
        //if ((row[5]%100)>=10){chkJoin=" checked ";}
        //table1+="<td style='text-align:center;'><input id='meal2_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='meal2' type='checkbox'"+chkJoin+disableditem+"></td>";

        //車資
        table1+="<td style='text-align:center;'><div style='width:35px;' id='fee_"+(w)+"' class='fee' serial='"+(w)+"' idx='"+row[0]+"' name='fee"+w+"'>"+row[7]+"<div></td>";

        //備註
        table1+="<td style='text-align:center;'><input style='width:60px' id='memo_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='memo' type='text'"+" value='"+row[13]+"'></td>";

        //繳費
        if(payitem=="YES") {
            chkPay="  ";if (row[8]>0){chkPay=" checked ";}
            disableditem="  ";if (row[4]<=0 || row[7]<=0){disableditem=" disabled ";}//不參加或無須繳費 - 無須繳費
            table1+="<td style='text-align:center;'><input id='pay_"+(w)+"' serial='"+(w)+"' idx='"+row[0]+"' class='pay' type='checkbox'"+chkPay+disableditem+">";
        }

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

	$('.join1').click(function(event){
		var serial=$(this).attr('serial');
		var joincnt=0;
		if ($('#join1_'+serial).is(':checked')){
            joincnt++;
            $('#place1_' + serial).prop('disabled',false);
            $('#fami1_' + serial).prop('disabled',false);
            $('#meal1_' + serial).prop('disabled', false);
        } else {
            $('#fami1_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#fami1_' + serial).val("0").change();
            $('#fami1_' + serial).prop('disabled', true);
            $('#meal1_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#meal1_' + serial).val("0").change();
            $('#meal1_'+serial).prop('disabled', true);

			$('#place1_'+serial+' option[value="0"]').attr('selected', 'selected');
			$('#place1_'+serial).val("0").change();
			$('#place1_'+serial).prop('disabled',true);
		}
		if ($('#join2_'+serial).is(':checked')){joincnt++;}

	    if (joincnt>=1) {
            $('#notjoin_'+serial).prop('checked',false);$('#fee_'+serial).text(getfee(serial));
        } else {
		    $('#notjoin_'+serial).prop('checked',true);
            $('#fami1_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#fami1_' + serial).val("0").change();
            $('#fami1_' + serial).prop('disabled', true);
            $('#meal1_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#meal1_' + serial).val("0").change();
            $('#meal1_' + serial).prop('disabled', true);

			//$('#meal1_'+serial).prop('checked',false);$('#meal1_'+serial).prop('disabled',true);
			$('#join2_'+serial).prop('checked',false);
            $('#fami2_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#fami2_' + serial).val("0").change();
            $('#fami2_' + serial).prop('disabled', true);
            $('#meal2_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#meal2_' + serial).val("0").change();
            $('#meal2_' + serial).prop('disabled', true);
			//$('#meal2_'+serial).prop('checked',false);$('#meal2_'+serial).prop('disabled',true);
            $('#fee_'+serial).text("0");
			$('#pay_'+serial).prop('checked',false);$('#pay_'+serial).prop('disabled',true);
		}
	});
	$('.join2').click(function(event){
		var serial = $(this).attr('serial');
		var joincnt=0;
		if ($('#join1_'+serial).is(':checked')) {joincnt++;}
		if ($('#join2_'+serial).is(':checked')) {
            joincnt++;
            $('#place2_' + serial).prop('disabled', false);
            $('#fami2_' + serial).prop('disabled', false);
            $('#meal2_' + serial).prop('disabled', false);
        } else {
            $('#fami2_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#fami2_' + serial).val("0").change();
            $('#fami2_' + serial).prop('disabled', true);
            $('#meal2_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#meal2_' + serial).val("0").change();
            $('#meal2_' + serial).prop('disabled', true);

            $('#place2_'+serial+' option[value="0"]').attr('selected', 'selected');
			$('#place2_'+serial).val("0").change();
			$('#place2_'+serial).prop('disabled',true);
		}
	    if (joincnt>=1) {
            $('#notjoin_'+serial).prop('checked',false);$('#fee_'+serial).text(getfee(serial));
        } else{
		    $('#notjoin_'+serial).prop('checked',true);
            $('#fami1_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#fami1_' + serial).val("0").change();
            $('#fami1_' + serial).prop('disabled', true);
            $('#meal1_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#meal1_' + serial).val("0").change();
            $('#meal1_' + serial).prop('disabled', true);

			$('#join2_'+serial).prop('checked',false);
            $('#fami2_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#fami2_' + serial).val("0").change();
            $('#fami2_' + serial).prop('disabled', true);
            $('#meal2_' + serial + ' option[value="0"]').attr('selected', 'selected');
            $('#meal2_' + serial).val("0").change();
            $('#meal2_' + serial).prop('disabled', true);

            $('#fee_'+serial).text("0");
			$('#pay_'+serial).prop('checked',false);('#pay_'+serial).prop('disabled',true);
		}
	});

	$('.notjoin').click(function(event){
		var serial=$(this).attr('serial');
		$('#join1_'+serial).prop('checked',false);
        $('#fami1_' + serial + ' option[value="0"]').attr('selected', 'selected');
        $('#fami1_' + serial).val("0").change();
        $('#fami1_' + serial).prop('disabled', true);
        $('#meal1_' + serial + ' option[value="0"]').attr('selected', 'selected');
        $('#meal1_' + serial).val("0").change();
        $('#meal1_' + serial).prop('disabled', true);

		$('#place1_'+serial+' option[value="0"]').attr('selected', 'selected');
		$('#place1_'+serial).val("0").change();
		$('#place1_'+serial).prop('disabled',true);

		$('#join2_'+serial).prop('checked',false);
        $('#fami2_' + serial + ' option[value="0"]').attr('selected', 'selected');
        $('#fami2_' + serial).val("0").change();
        $('#fami2_' + serial).prop('disabled', true);
        $('#meal2_' + serial + ' option[value="0"]').attr('selected', 'selected');
        $('#meal2_' + serial).val("0").change();
        $('#meal2_' + serial).prop('disabled', true);

		$('#place2_'+serial+' option[value="0"]').attr('selected', 'selected');
		$('#place2_'+serial).val("0").change();
		$('#place2_'+serial).prop('disabled',true);

        $('#fee_'+serial).text("0");
		$('#pay_'+serial).prop('checked',false);$('#pay_'+serial).prop('disabled',true);
	});

	$('.traffic').on('change', function(){
		var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
		$('#fee_'+serial).text(getfee(serial));
	});

	$('.place1').on('change', function(){
		var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
		var place=$(this).val();
		//if (place==2){$('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected');$('#traffic1_'+serial).val("Z").change();$('#traffic1_'+serial).prop('disabled',false);}
		//else{$('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected');$('#traffic1_'+serial).val("Z").change();$('#traffic1_'+serial).prop('disabled',true);}
		$('#fee_'+serial).text(getfee(serial));
	});

	$('.place2').on('change', function(){
		var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
		var place=$(this).val();
		//if (place==2){$('#traffic2_'+serial+' option[value="Z"]').attr('selected', 'selected');$('#traffic2_'+serial).val("Z").change();$('#traffic2_'+serial).prop('disabled',false);}
		//else{$('#traffic2_'+serial+' option[value="Z"]').attr('selected', 'selected');$('#traffic2_'+serial).val("Z").change();$('#traffic2_'+serial).prop('disabled',true);}
		$('#fee_'+serial).text(getfee(serial));
	});

	$('.pay').click(function(event){
		var serial = $(this).attr('serial');
		if ($(this).is(':checked')){$('#notjoin_'+serial).prop('disabled',true);$('#join_'+serial).prop('disabled',true);$('#traffic_'+serial).prop('disabled',true);}
		else{$('#notjoin_'+serial).prop('disabled',false);$('#join_'+serial).prop('disabled',false);if ($('#join_'+serial).is(':checked')){$('#traffic_'+serial).prop('disabled',false);}}
	});
}

function getfee(index){
    return 0;
}
