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
    var tbname=$("#tb").val(), tbstatistic=$("#tbstatistic").val(), nMemberCnt=$("#memberCnt").val();
    var classid=$("#classid").val(), tb=$('.tb').val(), trafftb=$('.trafftb').val(), sub=$('.sub').val();
    var payitem=$('#payitem').val(), payercurid=$('#payerid').val(), payercurname=$('#payername').val();
    var curdate=$('.currentdate').val(), dbg = $('.dbg').val(), endjoin = $('.endjoin').val();
    var traffprice=$('#traffroundfee').val();
    var day1title = $('#day1title').val(), day2title = $('#day2title').val(), day3title = $('#day3title').val(), day4title = $('#day4title').val();
    var clsid = $("#clsid").val(), clsname = $("#clsname").val(), clsfullname = $("#clsfullname").val(), clsarea = $("#clsarea").val();
    var clsregion = $("#clsregion").val(), clshasleader = $("#Major").val();
    if (clsid == null || typeof clsid === 'undefined' || clsid == "") { alert("未取得班級資訊!"); return; }
    if (clsname == null || typeof clsname === 'undefined' || clsname == "") { alert("未取得班級資訊!"); return; }
    if (clsarea == null || typeof clsarea === 'undefined' || clsarea == "") { alert("未取得班級資訊!"); return; }
    if (clsregion == null || typeof clsregion === 'undefined' || clsregion == "") { alert("未取得班級區域資訊!"); return; }
    if (clshasleader == null || typeof clshasleader === 'undefined' || clshasleader == "") { alert("未取得班級幹部資訊!"); return; }

    if (classid==null||typeof classid==='undefined'||classid==""){alert("未取得班級資訊!");return ;}

    for (i = 1; i <= nMemberCnt; i++) {
      if ($('#join4_' + i).is(':checked')) {
        if ($('#place4_' + i).val() == 0) {
          alert($('#student_' + i).text() + "-未指定 " + day4title + " 參加場次!");
          return;
        }
      }
    }
    var bPaid = false;
    if ($('.payitem').val() == "YES") {
      bPaid = true; //是否要顯示繳費項目
    }

    var allsqlcmd = ""; var sqlcmd = ""; var joinday1 = 0; var joinday2 = 0; var joinday3 = 0; var joinday4 = 0;
    var regdate1 = ""; var paydate1 = ""; var regnewdate1 = ""; var paynewdate1 = ""; var payernewid1 = ""; var payernewname1 = "";
    var regdate2 = ""; var paydate2 = ""; var regnewdate2 = ""; var paynewdate2 = ""; var payernewid2 = ""; var payernewname2 = "";
    var regdate3 = ""; var paydate3 = ""; var regnewdate3 = ""; var paynewdate3 = ""; var payernewid3 = ""; var payernewname3 = "";
    var regdate4 = ""; var paydate4 = ""; var regnewdate4 = ""; var paynewdate4 = ""; var payernewid4 = ""; var payernewname4 = "";
    var fee1 = 0; var fee2 = 0; var fee3 = 0; var fee4 = 0;

    var clssqlcmd = ",`classname`=&#&#" + clsname + "&#&#,`CLS_ID`=&#&#" + clsid + "&#&#,`area`=&#&#" + clsarea + "&#&#,`areaid`=&#&#" + clsregion + "&#&#,`classfullname`=&#&#" + clsfullname + "&#&# ";//更新班級資料
    for (i = 1; i <= nMemberCnt; i++) {
      var bPay = false;
      var lock = $('#idx_' + i).attr('lock');
      var idx = $('#notjoin_' + i).attr('idx');//取得idx

      if (lock == 1 && bPaid == false) { //已經 lock且不是管理者無繳費確認功能=>不用處理此 record
        txtMemo = $('#memo_' + i).val(); //備註
        sqlcmd = "UPDATE `" + tbname + "` " + "SET `memo`=&#&#" + txtMemo + "&#&#";
        sqlcmd += clssqlcmd;
        sqlcmd += " WHERE `idx`=" + idx;
        allsqlcmd += sqlcmd;
        allsqlcmd += ";;;;"; //allsqlcmd+="<br>";//debug
        continue;
      }
      if (bPaid == true) {
        if ($('#pay_' + i).is(':checked')) {
          bPay = true;
        }
      }

      joinday1 = 0; joinday2 = 0; joinday3 = 0; joinday4 = 0;
      fami1 = 0; fami2 = 0; fami3 = 0; fami4 = 0;
      place1 = '0'; place2 = '0'; place3 = '0'; place4 = '0';
      traff1 = "Z"; traffReal1 = "Z"; traff2 = "Z"; traffReal2 = "Z"; traff3 = "Z"; traffReal3 = "Z"; traff4 = "Z"; traffReal4 = "Z";

      if ($('#join1_' + i).is(':checked')) {
        joinday1 = 1;
        fami1 = $("#fami1_" + i).val();
      }
      if ($('#join2_' + i).is(':checked')) {
        joinday2 = 1;
        fami2 = $("#fami2_" + i).val();
      }
      if ($('#join3_' + i).is(':checked')) {
        joinday3 = 1;
        traff3 = $("#traffic3_" + i).val();
        fami3 = $("#fami3_" + i).val();
        if (bPay) {
          traffReal3 = traff3;
        }
      }
      if ($('#join4_' + i).is(':checked')) {
        joinday4 = 1;
        fami4 = $("#fami4_" + i).val();
        place4 = $("#place4_" + i).val();
      }
      if (!place4) {
        place4 = '0';
      }

      traffCnt3 = "0"; traffRealCnt3 = "0";
      traffitem3 = traff3 + "," + traffCnt3 + "," + traffReal3 + "," + traffRealCnt3;

      fee1 = 0;// 車資1
      fee2 = 0;// 車資2
      fee3 = getfee3(i);// 車資 3
      txtMemo = $('#memo_' + i).val();//備註

      if (bPaid == true) {//管理窗口

        regdate1 = $('#idx_' + i).attr('regdate1'); paydate1 = $('#idx_' + i).attr('paydate1');
        payerid1 = $('#idx_' + i).attr('payerid1'); payername1 = $('#idx_' + i).attr('payername1');
        regdate2 = $('#idx_' + i).attr('regdate2'); paydate2 = $('#idx_' + i).attr('paydate2');
        payerid2 = $('#idx_' + i).attr('payerid2'); payername2 = $('#idx_' + i).attr('payername2');
        regdate3 = $('#idx_' + i).attr('regdate3'); paydate3 = $('#idx_' + i).attr('paydate3');
        payerid3 = $('#idx_' + i).attr('payerid3'); payername3 = $('#idx_' + i).attr('payername3');

        // 考慮 reg date & pay date
        if (joinday1 == 0) {
          regnewdate1 = "1970-01-01"; paynewdate1 = "1970-01-01";  //未報名,時間重設為1970-01-01
        } else {
          regnewdate1 = regdate1; paynewdate1 = paydate1;
          if (regdate1 == "1970-01-01" || regdate1 == "" || regdate1 == "0000-00-00") { regnewdate1 = curdate; }
        }

        if (joinday2 == 0) { regnewdate2 = "1970-01-01"; paynewdate2 = "1970-01-01"; }//未報名,時間重設為1970-01-01
        else { regnewdate2 = regdate2; paynewdate2 = paydate2; if (regdate2 == "1970-01-01" || regdate2 == "" || regdate2 == "0000-00-00") { regnewdate2 = curdate; } }

        if (joinday3 == 0) { regnewdate3 = "1970-01-01"; paynewdate3 = "1970-01-01"; }//未報名,時間重設為1970-01-01
        else { regnewdate3 = regdate3; paynewdate3 = paydate3; if (regdate3 == "1970-01-01" || regdate3 == "" || regdate3 == "0000-00-00") { regnewdate3 = curdate; } }

        if (joinday1 == 0 && joinday2 == 0 && joinday3 == 0 && joinday4 == 0) {
          regnewdate1 = regdate1; paynewdate1 = paydate1;
          if (regdate1 == "1970-01-01" || regdate1 == "" || regdate1 == "0000-00-00") { regnewdate1 = curdate; }
        }

        lock = 0;
        paid1 = 0; paid2 = 0; paid3 = 0;
        cancel1 = 0; cancel2 = 0; cancel3 = 0; //註記取消報名
        if (bPay == true) {
          lock = 1; paid1 = fee1; paid2 = fee2; paid3 = fee3;
          if (paydate1 == "1970-01-01" || paydate1 == "") { paynewdate1 = curdate; }
          if (payerid1 == "") { payernewid1 = payercurid; } else { payernewid1 = payerid1; }
          if (payername1 == "") { payernewname1 = payercurname; } else { payernewname1 = payername1; }

          if (paydate2 == "1970-01-01" || paydate2 == "") { paynewdate2 = curdate; }
          if (payerid2 == "") { payernewid2 = payercurid; } else { payernewid2 = payerid2; }
          if (payername2 == "") { payernewname2 = payercurname; } else { payernewname2 = payername1; }

          if (paydate3 == "1970-01-01" || paydate3 == "") { paynewdate3 = curdate; }
          if (payerid3 == "") { payernewid3 = payercurid; } else { payernewid3 = payerid3; }
          if (payername3 == "") { payernewname3 = payercurname; } else { payernewname3 = payername1; }
        } else {
          paynewdate1 = "1970-01-01"; paynewdate2 = "1970-01-01"; paynewdate3 = "1970-01-01";
          payernewid1 = ""; payernewid2 = ""; payernewid3 = "";
          payernewname1 = ""; payernewname2 = ""; payernewname3 = "";
        }

        sqlcmd = "UPDATE `" + tbname + "` " + "SET ";
        sqlcmd += "`day1`=" + joinday1 + ",`family1`=" + fami1 + ",`lock1`=" + lock + ",`pay1`=" + paid1 + ",`regdate1`=&#&#" + regnewdate1 + "&#&#,";
        sqlcmd += "`paydate1`=&#&#" + paynewdate1 + "&#&#,`paybyid1`=&#&#" + payernewid1 + "&#&#,`paybyname1`=&#&#" + payernewname1 + "&#&#,";

        sqlcmd += "`day2`=" + joinday2 + ",`family2`=" + fami2 + ",`lock2`=" + lock + ",`pay2`=" + paid2 + ",`regdate2`=&#&#" + regnewdate2 + "&#&#,";
        sqlcmd += "`paydate2`=&#&#" + paynewdate2 + "&#&#,`paybyid2`=&#&#" + payernewid2 + "&#&#,`paybyname2`=&#&#" + payernewname2 + "&#&#,";

        sqlcmd += "`day3`=" + joinday3 + ",`family3`=" + fami3 + ",`traff3`=&#&#" + traffitem3 + "&#&#,`cost3`=" + fee3 + ",`lock3`=" + lock + ",`pay3`=" + paid3 + ",`regdate3`=&#&#" + regnewdate3 + "&#&#,";
        sqlcmd += "`paydate3`=&#&#" + paynewdate3 + "&#&#,`paybyid3`=&#&#" + payernewid3 + "&#&#,`paybyname3`=&#&#" + payernewname3 + "&#&#,";

        sqlcmd += "`day4`=" + joinday4 + ",`family4`=" + fami4 + ",`service4`=" + place4 + ",`lock4`=" + lock  + ",";

        sqlcmd += "`memo1`=&#&#" + txtMemo + "&#&#";
        sqlcmd += clssqlcmd;
        sqlcmd += " WHERE `idx`=" + idx;
      } else {//幹部一般報名
        sqlcmd = "UPDATE `" + tbname + "` " + "SET ";
        sqlcmd += "`day1`=" + joinday1 + ",`family1`=" + fami1 + ",`regdate1`=&#&#" + regnewdate1 + "&#&#,";
        sqlcmd += "`day2`=" + joinday2 + ",`family2`=" + fami2 + ",`regdate2`=&#&#" + regnewdate2 + "&#&#,";
        sqlcmd += "`day3`=" + joinday3 + ",`family3`=" + fami3 + ",`traff3`=&#&#" + traffitem3 + "&#&#,`cost3`=" + fee3 + ",`regdate3`=&#&#" + regnewdate3 + "&#&#,";
        sqlcmd += "`day4`=" + joinday4 + ",`family4`=" + fami4 + ",`service4`=" + place4 + ",";
        sqlcmd += "`memo1`=&#&#" + txtMemo + "&#&#";
        sqlcmd += clssqlcmd;
        sqlcmd += " WHERE `idx`=" + idx;
      }

      allsqlcmd += sqlcmd;
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
    var day1title = $('#day1title').val();
    var day2title = $('#day2title').val();
    var day3title = $('#day3title').val();
    var day4title = $('#day4title').val();
    //alert(trafftb+","+tbname+","+classname);
    //return;
    if (classname == "" || tbname == ""){alert("未取得班級資料!");return;}
    //alert(classname);
    var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
    parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';
    parameter+='<input type="hidden" name="pujaid" value="'+sub+'" />';
    parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />"';
    parameter += '<input type="hidden" name="day1title" value="' + day1title + '" /><input type="hidden" name="day2title" value="' + day2title + '" />';
    parameter += '<input type="hidden" name="day3title" value="' + day3title + '" /><input type="hidden" name="day4title" value="' + day4title + '" />';

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
    var day1title = $('#day1title').val();
    var day2title = $('#day2title').val();
    var day3title = $('#day3title').val();
    var day4title = $('#day4title').val();
    if (classname == "" || tbname == ""){alert("未取得班級資料!");return;}
    //alert(classname);
    var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
    parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';
    parameter+='<input type="hidden" name="pujaid" value="'+sub+'" />';
    parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />"';
    parameter += '<input type="hidden" name="day1title" value="' + day1title + '" /><input type="hidden" name="day2title" value="' + day2title + '" />';
    parameter += '<input type="hidden" name="day3title" value="' + day3title + '" /><input type="hidden" name="day4title" value="' + day4title + '" />';

    $('<form action="./pages/'+sub+'/pay-list.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
  });


  //-----------------------------------------------------------------------------------------------------------------------------------
  // 匯出班級報名統計表
  //-----------------------------------------------------------------------------------------------------------------------------------
  $('#exportclasslist').click(function () {
    var tbname   = $("#tb").val();
    var tbtraffic  = $("#traffictb").val();
    var tbstatistic  = $("#tbstatistic").val();
    var sub=$('.sub').val();
    if (tbname == "" || tbtraffic == "")
    {
      alert("未取得報名統計資料!");
      return;
    }
    //alert(tbtraffic+"   ,  "+tbname+"   ,  "+tbstatistic);
    $('<form action="./pages/'+sub+'/register-list-export-class.php" method="post"><input type="hidden" name="tbname" value='+tbname+' /><input type="hidden" name="tbstatistic" value='+tbstatistic+' /><input type="hidden" name="tbtraffic" value='+tbtraffic+' /></form>').appendTo('body').submit().remove();
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
    var day1title = $('#day1title').val();
    var day2title = $('#day2title').val();
    var day3title = $('#day3title').val();
    var day4title = $('#day4title').val();
    if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

    var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
    parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
    parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
    parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';

    parameter += '<input type="hidden" name="day1title" value="' + day1title + '" /><input type="hidden" name="day2title" value="' + day2title + '" />';
    parameter += '<input type="hidden" name="day3title" value="' + day3title + '" /><input type="hidden" name="day4title" value="' + day4title + '" />';

    parameter+='<input type="hidden" name="cancelitem" value="NO" />';
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

    if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

    var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
    parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
    parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
    parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
    parameter+='<input type="hidden" name="cancelitem" value="NO" />';
    parameter+='"';
    $('<form action="./pages/'+sub+'/export-class.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
  });
  //-----------------------------------------------------------------------------------------------------------------------------------
  // 匯出學員取消報名統計表
  //-----------------------------------------------------------------------------------------------------------------------------------
  $('#exportcancel').click(function () {
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
    parameter+='<input type="hidden" name="cancelitem" value="YES" />';
    parameter+='"';
    $('<form action="./pages/'+sub+'/export-remove.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
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
  var trafftable2 = [];
  var trafftable3 = [];
  if (trafftable.length >= 2) { trafftable2=trafftable[1].split('|');}
  if (trafftable.length >= 3) { trafftable3 = trafftable[2].split('|'); }
  var day1title=$('#day1title').val();
  var day2title=$('#day2title').val();
  var day3title = $('#day3title').val();
  var day4title = $('#day4title').val();

  var item = $('#joinplace').val().split(';');
  var place_array = [];
  for (var i = 0, len = item.length; i < len; i++) {
    place_array.push(item[i].split(','));
  }

  var fami=[];
  for(x=0;x<20;x++){fami.push(x);}
  // Head ROW1
  var table1="<table style='font-size:11pt;' class='reference' id='fixheadertbl1' style='width:900px;' align='center'>";
  table1+="<thead><tr>";
  table1+="<th></th><th></th><th></th>";//序	姓名	身份
  table1+="<th></th>";// X 義工
  table1+="<th colspan='2'>"+day1title+"</th>";
  table1 +="<th colspan='2'>"+day2title+"</th>";
  table1 += "<th colspan='3'>" + day3title + "</th>";
  table1 += "<th colspan='3'>" + day4title + "</th>";
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
  table1+="<th>12/5(六)</th><th>12/6(日)</th><th>12/6(日)</th>";//<th style='width:30px;'>訂餐</th>";
  table1+="<th></th>";
  table1+="<th></th><th></th>";
  if(payitem=="YES"){table1+="<th style='width:30px;'></th>";}
  */
  // HEAD ROW3
  table1+="</tr><tr>";
  table1+="<th style='width:25px;'>序</th>";
  table1+="<th style='width:82px;'>姓名</th>";
  table1+="<th style='width:45px;'>身份</th>";
  table1+="<th style='width:25px;'>X</th>";

  table1+="<th style='width:35px;'>學員</th>";
  table1+="<th style='width:40px;'>眷屬</th>";
  //table1+="<th style='width:80px;'>交通</th>";

  table1 += "<th style='width:35px;'>學員</th>";
  table1 += "<th style='width:40px;'>眷屬</th>";
  //table1 += "<th style='width:80px;'>交通</th>";

  table1 += "<th style='width:35px;'>學員</th>";
  table1 += "<th style='width:40px;'>眷屬</th>";
  table1 += "<th style='width:80px;'>交通</th>";

  table1 += "<th style='width:35px;'>學員</th>";
  table1 += "<th style='width:40px;'>眷屬</th>";
  table1 += "<th style='width:80px;'>場次</th>";

  table1+="<th style='width:40px;'>車資</th><th style='width:60px;'>備註</th>";
  if(payitem=="YES"){table1+="<th style='width:30px;'>繳費</th>";}
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

    lock=0;
    if(payitem=="YES"){lock=0;}else{lock=row[11];}//管理者可勾選繳費
    disabledlock="  ";if (lock>=1){disabledlock=" disabled ";}

    day1 = parseInt(row[12]);
    day2 = parseInt(row[32]);
    day3 = parseInt(row[52]);
    day4 = parseInt(row[72]);
    day  = day1 + (day2 * 10) + (day3 * 100) + (day4 * 1000);
    idx  = parseInt(row[0]);

    service1 = parseInt(row[15]);//以service1為主
    service2 = parseInt(row[35]);
    service3 = parseInt(row[55]);
    service4 = parseInt(row[75]);

    fami1=parseInt(row[14]);
    fami2=parseInt(row[34]);
    fami3 = parseInt(row[54]);
    fami4 = parseInt(row[74]);

    cost1=parseInt(row[19]);
    cost2=parseInt(row[39]);
    cost3 = parseInt(row[59]);
    cost4 = parseInt(row[79]);
    cost = cost1 + cost2 + cost3 + cost4;

    var daytraff1=row[18].split(',');
    traff1=daytraff1[0];if(traff1==""||typeof(traff1)=="undefined"){traff1="Z";}
    var daytraff2=row[38].split(',');
    traff2=daytraff2[0];if(traff2==""||typeof(traff2)=="undefined"){traff2="Z";}
    var daytraff3=row[58].split(',');
    traff3=daytraff3[0];if(traff3==""||typeof(traff3)=="undefined"){traff3="Z";}
    var daytraff4 = row[78].split(',');
    traff4 = daytraff4[0]; if (traff4 == "" || typeof (traff4) == "undefined") { traff4 = "Z"; }
    // keep the old data
    table1+="<tr><td id='idx_"+(w)+"'' class='idx' idx='"+idx+"' serial='"+(w)+"' lock='"+lock+"'";
    table1+=" regdate1='"+row[22]+"' regdate2='"+row[42]+"' regdate3='"+row[62]+"' regdate4='"+row[82]+"'";
    table1+=" paydate1='"+row[23]+"' payerid1='"+row[25]+"' payername1='"+row[26]+"' ojoinday1='"+row[12]+"'";
    table1+=" paydate2='"+row[43]+"' payerid2='"+row[45]+"' payername2='"+row[46]+"' ojoinday2='"+row[32]+"'";
    table1+=" paydate3='"+row[63]+"' payerid3='"+row[65]+"' payername3='"+row[66]+"' ojoinday3='"+row[52]+"'";
    table1+=" paydate4='"+row[83]+"' payerid4='"+row[85]+"' payername4='"+row[86]+"' ojoinday4='"+row[62]+"'";
    table1+=" traffic1='"+traff1+"' fee1='"+row[19]+"' cancel1='"+row[27]+"' cancelinfo1='"+row[28]+"'";
    table1+=" traffic2='"+traff2+"' fee2='"+row[39]+"' cancel2='"+row[47]+"' cancelinfo2='"+row[48]+"'";
    table1+=" traffic3='"+traff3+"' fee3='"+row[59]+"' cancel3='"+row[67]+"' cancelinfo3='"+row[68]+"'";
    table1+=" traffic4='"+traff4+"' fee4='"+row[79]+"' cancel4='"+row[87]+"' cancelinfo4='"+row[88]+"'";
    table1+=" style='text-align:center;'>"+(w)+"</td>";//序-記錄相關informaion


    table1+="<td style='text-align:center;' id='student_"+(w)+"'' class='student' idx='"+row[1]+"' serial='"+(w)+"'>"+row[1]+"</td>";//姓名
    table1+="<td style='text-align:center;'>"+row[2]+"</td>";//身份

    disableditem="  ";
    if (day>0){disableditem=" disabled ";}

    // 不參加
    chkNotJoin=" checked ";chkJoin="  ";disableTraff=" disabled ";
    if(day>0){chkNotJoin="  ";chkJoin=" checked ";disableTraff=" ";}
    table1+="<td style='text-align:center;'><input id='notjoin_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='notjoin' type='radio'"+chkNotJoin+disabledlock+"></td>";

    // 義工
    //chkJoin="  ";disableditem=" disabled ";
    //if (service1>0){chkJoin=" checked ";}else{chkJoin=" ";}
    //if(day>=0){disableditem=" ";}
    //table1+="<td style='text-align:center;'><input id='service_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='service' type='checkbox'"+chkJoin+disableditem+disabledlock+"></td>";

    //場1學員眷屬&交通
    chkJoin="  ";disableditem=" disabled ";
    if (day1>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
    table1+="<td style='text-align:center;'><input id='join1_"+(w)+"' serial='"+(w)+"' Item=1 idx='"+idx+"' class='join' type='checkbox'"+chkJoin+disabledlock+"></td>";

    table1 +="<td style='text-align:center;'><select style='width:98%;' id='fami1_"+(w)+"' class='fami' serial='"+(w)+"' idx='"+idx+"' name='fami"+w+"' "+disableditem+disabledlock+">";
    for(kk=0;kk<fami.length;kk++){selected=(fami1==fami[kk] ? "selected":" ");table1+="<option value='"+fami[kk]+"' "+selected+">"+fami[kk]+"</option>";}
    table1 +="</td>";

    //if (day1>0){disableditem=" ";}
    //table1+="<td style='text-align:center;'><select style='width:98%;' id='traffic1_"+(w)+"' class='traffic' serial='"+(w)+"' Item=1 idx='"+idx+"' name='traffic"+w+"' "+disableditem+disabledlock+">";
    //for(kk=0;kk<trafftable1.length;kk+=2){selected=(traff1==trafftable1[kk] ? "selected":" ");table1+="<option value='"+trafftable1[kk]+"' "+selected+">"+trafftable1[kk]+" - "+trafftable1[kk+1]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
    //table1 +="</td>";

    //場2學員眷屬&交通
    chkJoin="  ";disableditem=" disabled ";
    if (day2>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
    table1+="<td style='text-align:center;'><input id='join2_"+(w)+"' serial='"+(w)+"' Item=2 idx='"+idx+"' class='join' type='checkbox'"+chkJoin+disabledlock+"></td>";

    table1 +="<td style='text-align:center;'><select style='width:98%;' id='fami2_"+(w)+"' class='fami' serial='"+(w)+"' idx='"+idx+"' name='fami"+w+"' "+disableditem+disabledlock+">";
    for(kk=0;kk<fami.length;kk++){selected=(fami2==fami[kk] ? "selected":" ");table1+="<option value='"+fami[kk]+"' "+selected+">"+fami[kk]+"</option>";}
    table1 +="</td>";

    //if (day2>0){disableditem=" ";}
    //table1 +="<td style='text-align:center;'><select style='width:98%;' id='traffic2_"+(w)+"' class='traffic' serial='"+(w)+"' Item=2 idx='"+idx+"' name='traffic"+w+"' "+disableditem+disabledlock+">";
    //for(kk=0;kk<trafftable2.length;kk+=2){selected=(traff2==trafftable2[kk] ? "selected":" ");table1+="<option value='"+trafftable2[kk]+"' "+selected+">"+trafftable2[kk]+" - "+trafftable2[kk+1]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
    //table1 +="</td>";

    //場3學員眷屬&交通
    chkJoin = "  "; disableditem = " disabled ";
    if (day3 > 0) { chkNotJoin = "  "; chkJoin = " checked "; disableditem = " "; } else { chkJoin = " "; }
    table1 += "<td style='text-align:center;'><input id='join3_" + (w) + "' serial='" + (w) + "' Item=3 idx='" + idx + "' class='join' type='checkbox'" + chkJoin + disabledlock + "></td>";

    table1 += "<td style='text-align:center;'><select style='width:98%;' id='fami3_" + (w) + "' class='fami' serial='" + (w) + "' idx='" + idx + "' name='fami" + w + "' " + disableditem + disabledlock + ">";
    for (kk = 0; kk < fami.length; kk++) { selected = (fami3 == fami[kk] ? "selected" : " "); table1 += "<option value='" + fami[kk] + "' " + selected + ">" + fami[kk] + "</option>"; }
    table1 += "</td>";

    table1 += "<td style='text-align:center;'><select style='width:98%;' id='traffic3_" + (w) + "' class='traffic' serial='" + (w) + "' Item=3 idx='" + idx + "' name='traffic" + w + "' " + disableditem + disabledlock + ">";
    for (kk = 0; kk < trafftable3.length; kk += 2) { selected = (traff3 == trafftable3[kk] ? "selected" : " "); table1 += "<option value='" + trafftable3[kk] + "' " + selected + ">" + trafftable3[kk] + " - " + trafftable3[kk + 1] + "</option>"; }//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
    table1 += "</td>";

    //場4學員眷屬&場次
    chkJoin = "  "; disableditem = " disabled ";
    if (day4 > 0) { chkNotJoin = "  "; chkJoin = " checked "; disableditem = " "; } else { chkJoin = " "; }
    table1 += "<td style='text-align:center;'><input id='join4_" + (w) + "' serial='" + (w) + "' Item=4 idx='" + idx + "' class='join' type='checkbox'" + chkJoin + disabledlock + "></td>";

    table1 += "<td style='text-align:center;'><select style='width:98%;' id='fami4_" + (w) + "' class='fami' serial='" + (w) + "' idx='" + idx + "' name='fami" + w + "' " + disableditem + disabledlock + ">";
    for (kk = 0; kk < fami.length; kk++) { selected = (fami4 == fami[kk] ? "selected" : " "); table1 += "<option value='" + fami[kk] + "' " + selected + ">" + fami[kk] + "</option>"; }
    table1 += "</td>";

    table1 += "<td style='text-align:center;'><select style='width:98%;' id='place4_" + (w) + "' class='place1' serial='" + (w) + "' idx='" + row[0] + "' name='place" + w + "' " + disableditem + disabledlock + ">";
    for (kk = 0; kk < place_array.length; kk++) {
      if (place_array[kk] == "") { continue; }
      sts = (service4 == kk ? "selected" : " ");
      table1 += "<option value='" + place_array[kk][0] + "' " + sts + ">" + place_array[kk][1] + "</option>";
    }
    table1 += "</td>";

    //車資
    table1+="<td style='text-align:center;'><div style='width:40px;' id='fee_"+(w)+"' class='fee' serial='"+(w)+"' idx='"+idx+"' name='fee"+w+"'>"+cost+"<div></td>";

    //備註
    //var memo=row[29].replace("\'", "&#039;");
    table1+="<td style='text-align:center;'><input style='width:55px' id='memo_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='memo' type='text'"+" value='"+row[29]+"'></td>";

    //繳費
    if(payitem=="YES") {
      chkPay = "  "; if (row[20] > 0 || row[40] > 0 || row[60] > 0){chkPay=" checked ";}
      disableditem="  ";if (day<=0||cost<=0){disableditem=" disabled ";}//不參加或無須繳費 - 無須繳費
      table1+="<td style='text-align:center;'><input id='pay_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='pay' type='checkbox'"+chkPay+disableditem+">";
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

  $('.notjoin').click(function(event) {
    var serial=$(this).attr('serial');
    $('#join1_' + serial).prop('checked',false);
    $('#join2_' + serial).prop('checked',false);
    $('#join3_' + serial).prop('checked',false);
    $('#join4_' + serial).prop('checked', false);

    //$('#service_'+serial).prop('checked',false);$('#service_'+serial).prop('disabled',true);

    //$('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
    //$('#traffic1_'+serial).val("Z").change();
    //$('#traffic1_'+serial).prop('disabled',true);
    //$('#traffic2_'+serial+' option[value="Z"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
    //$('#traffic2_'+serial).val("Z").change();
    //$('#traffic2_'+serial).prop('disabled',true);
    $('#traffic3_' + serial + ' option[value="Z"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
    $('#traffic3_' + serial).val("Z").change();
    $('#traffic3_' + serial).prop('disabled', true);

    $('#place4_' + serial + ' option[value=""]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
    $('#place4_' + serial).val("").change();
    $('#place4_' + serial).prop('disabled', true);

    $('#fami1_'+serial+' option[value="0"]').attr('selected', 'selected');
    $('#fami1_'+serial).val("0").change();
    $('#fami1_'+serial).prop('disabled',true);
    $('#fami2_'+serial+' option[value="0"]').attr('selected', 'selected');
    $('#fami2_'+serial).val("0").change();
    $('#fami2_'+serial).prop('disabled',true);
    $('#fami3_' + serial + ' option[value="0"]').attr('selected', 'selected');
    $('#fami3_' + serial).val("0").change();
    $('#fami3_' + serial).prop('disabled', true);
    $('#fami4_' + serial + ' option[value="0"]').attr('selected', 'selected');
    $('#fami4_' + serial).val("0").change();
    $('#fami4_' + serial).prop('disabled', true);
    $('#fee_'+serial).text("0");
    $('#pay_'+serial).prop('checked',false);
    $('#pay_'+serial).prop('disabled',true);
  });

  $('.join').click(function(event) {
    var serial = $(this).attr('serial');
    var item = $(this).attr('Item');

    var day1 = $("#join1_" + serial).is(':checked') ? 1 : 0;
    var day2 = $("#join2_" + serial).is(':checked') ? 1 : 0;
    var day3 = $("#join3_" + serial).is(':checked') ? 1 : 0;
    var day4 = $("#join4_" + serial).is(':checked') ? 1 : 0;

    if ((day1 + day2 + day3 + day4) <=0 ){
      $('#notjoin_'+serial).prop('checked',true);
      //$('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected');
      //$('#traffic1_'+serial).val("Z").change();
      //$('#traffic1_'+serial).prop('disabled',true);
      //$('#traffic2_'+serial+' option[value="Z"]').attr('selected', 'selected');
      //$('#traffic2_'+serial).val("Z").change();
      //$('#traffic2_'+serial).prop('disabled',true);
      $('#traffic3_' + serial + ' option[value="Z"]').attr('selected', 'selected');
      $('#traffic3_' + serial).val("Z").change();
      $('#traffic3_' + serial).prop('disabled', true);

      $('#place4_' + serial + ' option[value=""]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
      $('#place4_' + serial).val("").change();
      $('#place4_' + serial).prop('disabled', true);

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
        //$('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected');
        //$('#traffic1_'+serial).val("Z").change();
        //$('#traffic1_'+serial).prop('disabled',true);
        $('#fami1_'+serial+' option[value="0"]').attr('selected', 'selected');
        $('#fami1_'+serial).val("0").change();
        $('#fami1_'+serial).prop('disabled',true);
      }else{
        //$('#traffic1_'+serial).prop('disabled',false);
        $('#fami1_'+serial).prop('disabled',false);
      }
    }

    if(item==2){
      if(day2<=0){
        //$('#traffic2_'+serial+' option[value="Z"]').attr('selected', 'selected');
        //$('#traffic2_'+serial).val("Z").change();
        //$('#traffic2_'+serial).prop('disabled',true);
        $('#fami2_'+serial+' option[value="0"]').attr('selected', 'selected');
        $('#fami2_'+serial).val("0").change();
        $('#fami2_'+serial).prop('disabled',true);
      }else{
        //$('#traffic2_'+serial).prop('disabled',false);
        $('#fami2_'+serial).prop('disabled',false);
      }
    }
    if (item == 3) {
      if (day3 <= 0) {
        $('#traffic3_' + serial + ' option[value="Z"]').attr('selected', 'selected');
        $('#traffic3_' + serial).val("Z").change();
        $('#traffic3_' + serial).prop('disabled', true);
        $('#fami3_' + serial + ' option[value="0"]').attr('selected', 'selected');
        $('#fami3_' + serial).val("0").change();
        $('#fami3_' + serial).prop('disabled', true);
      } else {
        $('#traffic3_' + serial).prop('disabled', false);
        $('#fami3_' + serial).prop('disabled', false);
      }
    }

    if (item == 4) {
      if (day4 <= 0) {
        $('#place4_' + serial + ' option[value=""]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
        $('#place4_' + serial).val("").change();
        $('#place4_' + serial).prop('disabled', true);
        $('#fami4_' + serial + ' option[value="0"]').attr('selected', 'selected');
        $('#fami4_' + serial).val("0").change();
        $('#fami4_' + serial).prop('disabled', true);
      } else {
        $('#place4_' + serial).prop('disabled', false);
        $('#fami4_' + serial).prop('disabled', false);
      }
    }
    $('#fee_'+serial).text(getfee(serial));
  });

  $('.traffic').on('change', function () {
    //var item=$(this).attr('Item');
    var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
    $('#fee_'+serial).text(getfee(serial));
  });

  $('.fami').on('change', function () {
    var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
    $('#fee_'+serial).text(getfee(serial));
  });

  $('.pay').click(function(event) {
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

  var fami1 = parseInt($('#fami1_'+index).val());
  var fami2 = parseInt($('#fami2_'+index).val());
  var fami3 = parseInt($('#fami3_' + index).val());
  if ($('#join1_'+index).is(':checked')) {
    if ($('#traffic1_' + index).val() && $('#traffic1_'+index).val() != "Z" && $('#traffic1_'+index).val() != "ZZ") {
      fee += (traffroundfee * (fami1 + 1));
    }
  }
  if ($('#join2_' + index).is(':checked')) {
    if ($('#traffic2_' + index).val() && $('#traffic2_' + index).val() != "Z" && $('#traffic2_' + index).val() != "ZZ") {
      fee += (traffroundfee * (fami2 + 1));
    }
  }

  if ($('#join3_' + index).is(':checked')) {
    if ($('#traffic3_' + index).val() && $('#traffic3_' + index).val() != "Z" && $('#traffic3_' + index).val() != "ZZ") {
      fee += (traffroundfee * (fami3 + 1));
    }
  }
  if (fee<=0){
    $('#pay_'+index).prop('checked',false);
    $('#pay_'+index).prop('disabled',true);
  } else {
    $('#pay_'+index).prop('disabled',false);
  }
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

function getfee3(index) {
  var fee = 0;
  var traffroundfee = parseInt($('.traffroundfee').val());
  var traffgofee = parseInt($('.traffgofee').val());
  var traffbackfee = parseInt($('.traffbackfee').val());
  var traffoverdayfee = parseInt($('.traffoverdayfee').val());

  var fami3 = parseInt($('#fami3_' + index).val());
  if ($('#join3_' + index).is(':checked')) { if ($('#traffic3_' + index).val() != "Z" && $('#traffic3_' + index).val() != "ZZ") { fee += (traffroundfee * (fami3 + 1)); } }
  return fee;
}
