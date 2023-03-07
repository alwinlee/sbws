$(document).ready(function ()
{
  $.ajax({
    async: false,
    url: "top.php",
    success: function (data) {
      $("#pageTop").append(data);
    }
  });

  // 叫出班級報名表
  $('#query').click(function (){
    getclassMember("","","","","","");
  });

  //-----------------------------------------------------------------------------------------------------------------------------------
  // 傳送報名
  $('#send').click(function (){
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
      ocancel=$('#idx_'+i).attr('cancel');
      ofee=$('#idx_'+i).attr('fee');
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

      traff="Z";traffReal="Z";paid=0;joinday=0;type=0;speccare=0;
      if ($('#join1_'+i).is(':checked')){joinday=1;traff=$("#traffic1_"+i).val(); if(bPay){traffReal=traff;}}
      else if ($('#join2_'+i).is(':checked')){joinday=10;traff=$("#traffic1_"+i).val(); if(bPay){traffReal=traff;}}
      else if ($('#join3_'+i).is(':checked')){joinday=100;traff=$("#traffic1_"+i).val(); if(bPay){traffReal=traff;}}
      else{traff="Z";traffReal="Z";}

      traffCnt="0";traffRealCnt="0";
      traffCnt=$("#traffic1round_"+i).val(); if(typeof traffCnt==='undefined'||traffCnt==""){traffCnt=0;}
      if(bPay){traffRealCnt=traffCnt;}

      traffitem=traff+","+traffCnt+","+traffReal+","+traffRealCnt;

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
        cancel=0; //註記取消報名
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
        cancelinfo="";
        if(bCancelReg==true&&endjoin=="YES"){cancel=ojoinday;cancelinfo=ojoinday+"#"+otraffic+"#"+ofee+"#"+payername+"#"+paydate+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}

        sqlcmd="UPDATE `"+tbname+"` "+"SET `day1`="+joinday+",`traff1`=&#&#"+traffitem+"&#&#,";
        sqlcmd+="`cost1`="+fee+",`lock1`="+lock+",`pay1`="+paid+",`regdate1`=&#&#"+regnewdate+"&#&#,`paydate1`=&#&#"+paynewdate+"&#&#,";
        sqlcmd+="`paybyid1`=&#&#"+payernewid+"&#&#,`paybyname1`=&#&#"+payernewname+"&#&#,`memo1`=&#&#"+txtMemo+"&#&#";
        if(cancel>0){sqlcmd+=(",`cancel1`="+cancel+",`cancelinfo1`=&#&#"+cancelinfo+"&#&#");}
        else if(ocancel>0&&paid>0){sqlcmd+=(",`cancel1`=0");}//取消了又繳費
        sqlcmd+=clssqlcmd;
        sqlcmd+=" WHERE `idx`="+idx;
      }else{//幹部一般報名
        sqlcmd="UPDATE `"+tbname+"` "+"SET `day1`="+joinday+",`traff1`=&#&#"+traffitem+"&#&#,`cost1`="+fee+",`regdate1`=&#&#"+regnewdate+"&#&#,`memo1`=&#&#"+txtMemo+"&#&#";
        sqlcmd+=clssqlcmd;
        sqlcmd+=" WHERE `idx`="+idx;
      }

      allsqlcmd+=sqlcmd;
      allsqlcmd += ";;;;"; //allsqlcmd+="<br>";//debug
    }

    if(dbg=="YES"){$('#msg').html(allsqlcmd);return;}   // debug show info
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
        //alert(data);//$('#msg').html(data);   // debug show info
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
    if (classname == "" || tbname == ""){alert("未取得班級資料!");return;}
    //alert(classname);
    $('<form action="./pages/'+sub+'/register-list.php" method="post"><input type="hidden" name="classname" value='+classname+' /><input type="hidden" name="classid" value='+classid+' /><input type="hidden" name="tbname" value='+tbname+' /></form>').appendTo('body').submit().remove();
  });

  //-----------------------------------------------------------------------------------------------------------------------------------
  // 匯出車資繳費單
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
  $('#paylist').click(function ()
  {
    var classid=$('#classid').val(); //classid = $(this).val();
    var classname=$('#classid :selected').text();
    var region=$('#classid :selected').attr('regioncode');
    var area=$('#classid :selected').attr('AREAID');//alert(area);return;
    var tbname   = $("#tb").val();
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
  // 匯出班級報名統計表
  //-----------------------------------------------------------------------------------------------------------------------------------
  $('#exportclasslist').click(function () {
    var tbname = $("#tb").val();
    var tbtraffic = $("#traffictb").val();
    var tbstatistic = $("#tbstatistic").val();
    var sub = $('.sub').val();
    var day1title = $('#day1title').val();
    var day2title = $('#day2title').val();
    if (tbname == "" || tbtraffic == "") { alert("未取得報名統計資料!"); return; }
    //alert(tbtraffic+"   ,  "+tbname+"   ,  "+tbstatistic);
    //$('<form action="./pages/'+sub+'/export-all.php" method="post"><input type="hidden" name="tbname" value='+tbname+' /><input type="hidden" name="tbstatistic" value='+tbstatistic+' /><input type="hidden" name="tbtraffic" value='+tbtraffic+' /></form>').appendTo('body').submit().remove();

    var parameter = '<input type="hidden" name="tbname" value="' + tbname + '" />';
    parameter += '<input type="hidden" name="tbstatistic" value="' + tbstatistic + '" /><input type="hidden" name="tbtraffic" value="' + tbtraffic + '" />';
    parameter += '<input type="hidden" name="day1title" value="' + day1title + '" /><input type="hidden" name="day2title" value="' + day2title + '" />';
    parameter += '"';
    //$('<form action="./pages/'+sub+'/register-list-export-class.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    $('<form action="./pages/' + sub + '/export-class.php" method="post">' + parameter + '</form>').appendTo('body').submit().remove();
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
    var item1=$('#item1').val();
    var item2=$('#item2').val();
    var item3=$('#item3').val();
    var item4=$('#item4').val();
    var item5=$('#item5').val();
    var item6=$('#item6').val();

    if (tbname==""||tbtraffic == ""){alert("未取得報名統計資料!");return;}

    var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
    parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
    parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
    parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
    parameter+='<input type="hidden" name="item1" value="'+item1+'" /><input type="hidden" name="item2" value="'+item2+'" />';
    parameter+='<input type="hidden" name="item3" value="'+item3+'" /><input type="hidden" name="item4" value="'+item4+'" />';
    parameter+='<input type="hidden" name="item5" value="'+item5+'" /><input type="hidden" name="item6" value="'+item6+'" />';
    parameter+='<input type="hidden" name="cancelitem" value="NO" />';
    parameter+='"';
    $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
  });

  //-----------------------------------------------------------------------------------------------------------------------------------
  // 匯出班級報名統計表
  //-----------------------------------------------------------------------------------------------------------------------------------
  $('#classexport').click(function ()
  {
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
  $('#exportcancel').click(function ()
  {
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

function showtable(data,classid,classname,area,region,classfullname)
{
  //$('#queryresult').html(data);return; //debug
  var partsArray = data.split(';');
  var showdata="";
  if (partsArray.length<=0){return;}

  var payitem=$('#payitem').val(); //classid = $(this).val();
  var trafftable=partsArray[0].split('-');
  var trafftable1=trafftable[0].split('|');
  var trafftable2=trafftable[1].split('|');

  // Head ROW1
  var table1="<table class=\"reference\" id=\"fixheadertbl1\" style=\"width:800px\" align=\"center\">";
  table1+="<thead><tr>";
  table1+="<th></th><th></th><th></th><th></th><th colspan=\"3\">參加場次</th>";
  table1+="<th></th>";
  table1+="<th></th><th></th>";
  if(payitem=="YES"){table1+="<th></th>";}

  var day1name=$('#item1').val();
  var day2name=$('#item2').val();
  var day3name=$('#item3').val();
  var day1subname=$('#item4').val();
  var day2subname=$('#item5').val();
  var day3subname=$('#item6').val();
  // HEAD ROW2
  table1+="</tr><tr>";
  table1+="<th></th>";
  table1+="<th></th>";
  table1+="<th></th>";
  table1+="<th></th>";
  table1+="<th>"+day1name+"</th><th>"+day2name+"</th><th>"+day3name+"</th>";//<th style=\"width:30px;\">訂餐</th>";
  table1+="<th></th>";
  table1+="<th></th><th></th>";
  if(payitem=="YES"){table1+="<th style=\"width:30px;\"></th>";}
  // HEAD ROW3
  table1+="</tr><tr>";
  table1+="<th style=\"width:30px;\">序</th>";
  table1+="<th style=\"width:70px;\">姓名</th>";
  table1+="<th style=\"width:45px;\">身份</th>";
  table1+="<th style=\"width:30px;\">X</th>";
  table1+="<th style=\"width:100px;\">"+day1subname+"</th><th style=\"width:100px;\">"+day2subname+"</th><th style=\"width:100px;\">"+day3subname+"</th>";//<th style=\"width:30px;\">訂餐</th>";
  table1+="<th style=\"width:120px;\">交通車次</th>";
  table1+="<th style=\"width:50px;\">車資</th><th style=\"width:70px;\">備註</th>";
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

    if(payitem=="YES"){lock=0;}else{lock=row[11];}//管理者可勾選繳費
    disabledlock="  ";if (lock>=1){disabledlock=" disabled ";}

    // item1
    //$data.=($row["idx"]."|".$row["name"]."|".$row["title"]."|".$row["titleid"]."|0|0|0|0|0|0|0|");//預留7個位置0~10
    //$data.=($row["lock1"]."|".$row["day1"]."|".$row["meal1"]."|".$row["family1"]."|".$row["service1"]."|");//11~15
    //$data.=($row["joinmode1"]."|".$row["specialcase1"]."|".$row["traff1"]."|".$row["cost1"]."|".$row["pay1"]."|");//16~20
    //$data.=($row["attend1"]."|".$row["regdate1"]."|".$row["paydate1"]."|".$row["payround1"]."|".$row["paybyid1"]."|");//21~25
    //$data.=($row["paybyname1"]."|".$row["cancel1"]."|".$row["cancelinfo1"]."|".$row["memo1"]."|0|");//26~30

    day=parseInt(row[12]);
    idx=parseInt(row[0]);

    var daytraff=row[18].split(',');
    traff=daytraff[0];if(traff==""||typeof(traff)=="undefined"){traff="Z";}
    // keep the old data
    table1+="<tr><td id='idx_"+(w)+"'' class='idx' idx='"+idx+"' serial='"+(w)+"' lock='"+lock+"' regdate='"+row[22]+"'";
    table1+=" paydate='"+row[23]+"' payerid='"+row[25]+"' payername='"+row[26]+"' ojoinday='"+row[12]+"'";
    table1+=" traffic='"+traff+"' fee='"+row[19]+"' cancel='"+row[27]+"' cancelinfo='"+row[28]+"'";
    table1+="  style='text-align:center;'>"+(w)+"</td>";//序-記錄相關informaion

    table1+="<td style='text-align:center;' id='student_"+(w)+"'' class='student' idx='"+row[1]+"' serial='"+(w)+"'>"+row[1]+"</td>";//姓名
    table1+="<td style='text-align:center;'>"+row[2]+"</td>";//身份

    disableditem="  ";if (day>0){disableditem=" disabled ";}

    // 不參加
    chkNotJoin=" checked ";chkJoin="  ";disableTraff=" disabled ";
    if(day>0){chkNotJoin="  ";chkJoin=" checked ";disableTraff=" ";}
    table1+="<td style='text-align:center;'><input id='notjoin_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='notjoin' type='radio'"+chkNotJoin+disabledlock+"></td>";

    //參加 1,2,3場
    chkJoin="  ";disableditem=" disabled ";

    day1=(day==1?1:0);
    day2=(day==10?1:0);
    day3=(day==100?1:0);

    if (day1>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
    if (day1subname === '-') {
      table1 += "<td style='text-align:center;'><input id='join1_" + (w) + "' serial='" + (w) + "' Item=1 idx='" + idx + "' class='join' type='radio'" + chkJoin + disabledlock + " disabled style='display: none;'></td>";
    } else {
      table1 += "<td style='text-align:center;'><input id='join1_" + (w) + "' serial='" + (w) + "' Item=1 idx='" + idx + "' class='join' type='radio'" + chkJoin + disabledlock + "></td>";
    }

    if (day2>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
    if (day2subname === '-') {
      table1 += "<td style='text-align:center;'><input id='join2_" + (w) + "' serial='" + (w) + "' Item=2 idx='" + idx + "' class='join' type='radio'" + chkJoin + disabledlock + " disabled style='display: none;'></td>";
    } else {
      table1 += "<td style='text-align:center;'><input id='join2_" + (w) + "' serial='" + (w) + "' Item=2 idx='" + idx + "' class='join' type='radio'" + chkJoin + disabledlock + "></td>";
    }

    if (day3>0){chkNotJoin="  ";chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
    if (day3subname === '-') {
      table1 += "<td style='text-align:center;'><input id='join3_" + (w) + "' serial='" + (w) + "' Item=3 idx='" + idx + "' class='join' type='radio'" + chkJoin + disabledlock + " disabled style='display: none;'></td>";
    } else {
      table1 += "<td style='text-align:center;'><input id='join3_" + (w) + "' serial='" + (w) + "' Item=3 idx='" + idx + "' class='join' type='radio'" + chkJoin + disabledlock + "></td>";
    }

    // 交通
    if (day>0){disableditem=" ";}
    table1+="<td style='text-align:center;'><select style='width:110px' id='traffic1_"+(w)+"' class='traffic' serial='"+(w)+"' idx='"+idx+"' name='traffic"+w+"' "+disableditem+disabledlock+">";
    for(kk=0;kk<trafftable1.length;kk+=2){selected=(traff==trafftable1[kk] ? "selected":" ");table1+="<option value='"+trafftable1[kk]+"' "+selected+">"+trafftable1[kk]+" - "+trafftable1[kk+1]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
    //for(kk=0;kk<trafftable1.length;kk+=2){selected=(traff==trafftable1[kk] ? "selected":" ");table1+="<option value='"+trafftable1[kk]+"' "+selected+">"+trafftable1[kk]+"</option>";}
    table1 +="</td>";

    //車資
    table1+="<td style='text-align:center;'><div style='width:35px;' id='fee_"+(w)+"' class='fee' serial='"+(w)+"' idx='"+idx+"' name='fee"+w+"'>"+row[19]+"<div></td>";

    //備註
    //var memo=row[29].replace("\'", "&#039;");
    table1+="<td style='text-align:center;'><input style='width:60px' id='memo_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='memo' type='text'"+" value='"+row[29]+"'></td>";

    //繳費
    if(payitem=="YES")
    {
      chkPay="  ";if (row[20]>0){chkPay=" checked ";}
      disableditem="  ";if (day<=0||row[19]<=0){disableditem=" disabled ";}//不參加或無須繳費 - 無須繳費
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
    $('#traffic1_'+serial+' option[value="Z"]').attr('selected', 'selected'); //$("#traffic1_"+serial+" option")[0].selected=true;
    $('#traffic1_'+serial).prop('disabled',true);
    $('#fee_'+serial).text("0");
    $('#pay_'+serial).prop('checked',false);
    $('#pay_'+serial).prop('disabled',true);
  });

  $('.join').click(function(event)
  {
    var serial=$(this).attr('serial');
    var item=$(this).attr('Item');

    $('#notjoin_'+serial).prop('checked',false);
    if(item==1){$('#join2_'+serial).prop('checked',false);$('#join3_'+serial).prop('checked',false);}
    if(item==2){$('#join1_'+serial).prop('checked',false);$('#join3_'+serial).prop('checked',false);}
    if(item==3){$('#join1_'+serial).prop('checked',false);$('#join2_'+serial).prop('checked',false);}
    $('#traffic1_'+serial).prop('disabled',false);
  });

  $('.traffic').on('change', function ()
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
  unit=parseInt($('#traffgofee').val());
  if ($('#join1_'+index).is(':checked')){if ($('#traffic1_'+index).val()!="Z"){fee+=unit;}}
  if ($('#join2_'+index).is(':checked')){if ($('#traffic1_'+index).val()!="Z"){fee+=unit;}}
  if ($('#join3_'+index).is(':checked')){if ($('#traffic1_'+index).val()!="Z"){fee+=unit;}}

  if (fee<=0){$('#pay_'+index).prop('checked',false);$('#pay_'+index).prop('disabled',true);}
  else{$('#pay_'+index).prop('disabled',false);}
  return fee;
}
