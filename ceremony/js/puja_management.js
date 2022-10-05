$(document).ready(function () {
	$.ajax({
		async: false,url: "top.php",
		success: function (data) {
			$("#pageTop").append(data);
		}
	});

	$('#send').hide();$('#new').hide();$('#del').hide();
	// 叫出法會參數表
	$('#query').click(function (){getpuja();});

	// 叫出法會車次表
	$('#traffic').click(function (){gettraffic();});

	//-----------------------------------------------------------------------------------------------------------------------------------
	// 儲存法會設定
	$('#send').click(function () {
		var tbname = $("#tb").val();
		var trafftb=$('.trafftb').val();
		var sub=$('.sub').val();
		var pujaname = $('#pujaid :selected').text();
		if (pujaname=='-'){alert("尚未指定法會!");return;}
		var C1 = $("#C1").val();//name
		if (C1== null||typeof C1=== 'undefined'||C1 ==""){alert("資料錯誤!");return ;}
		var C2=$("#C2").val();var C3=$("#C3").val();var C4=$("#C4").val();var C5=$("#C5").val();var C6=$("#C6").val();var C7=$("#C7").val();var C8=$("#C8").val();
		var C9=$("#C9").val();var C10 = $("#C10").val();var C11=$("#C11").val();var C12=$("#C12").val();var C13=$("#C13").val();var C14=$("#C14").val();
		var C15=$("#C15").val();var C16=$("#C16").val();var C17=$("#C17").val();var C18=$("#C18").val();var C19=$("#C19").val();var C20=$("#C20").val();
		var C21=$("#C21").val();var C22=$("#C22").val();var C23=$("#C23").val();var C24=$("#C24").val();var C25=$("#C25").val();var C26=$("#C26").val();
            var C27=$("#C27").val();
            var C0= $("#C1").attr('idx');

		var mode="UPDATE";
		//$('#msg').html(allsqlcmd);return;	 // debug show info
		$.ajax({
			async: false,
			url: "./pages/"+sub+"/register.php",
			cache: false,
			dataType: 'html',
			type:'POST',
			data:{tbname:tbname,mode:mode,C0:C0,C1:C1,C2:C2,C3:C3,C4:C4,C5:C5,C6:C6,C7:C7,C8:C8,C9:C9,C10:C10,C11:C11,C12:C12,C13:C13,C14:C14,C15:C15,C16:C16,C17:C17,C18:C18,C19:C19,C20:C20,C21:C21,C22:C22,C23:C23,C24:C24,C25:C25,C26:C26,C27:C27},
			error: function (data) {
			    alert("失敗!!!-"+data);
			},
			success: function (data) {
			    //alert(data);return;//$('#msg').html(data);	 // debug show info
                $('#msg').text(data);
                if (data < 0){alert("設定失敗(錯誤代碼:"+data+")!");}
				else{alert("設定儲存成功!");getpuja();}
			}
		});
	});

	$('#new').click(function () {
		var tbname = $("#tb").val();
		var trafftb=$('.trafftb').val();
		var sub=$('.sub').val();
		var pujaname = $('#pujaid :selected').text();
		if (pujaname=='-'){alert("尚未指定法會!");return;}
		var C1 = $("#C1").val();//name
	      var mode="NEW";
		var C2=$("#C2").val();var C3=$("#C3").val();var C4=$("#C4").val();var C5=$("#C5").val();var C6=$("#C6").val();var C7=$("#C7").val();var C8=$("#C8").val();
		var C9=$("#C9").val();var C10 = $("#C10").val();var C11=$("#C11").val();var C12=$("#C12").val();var C13=$("#C13").val();var C14=$("#C14").val();
		var C15=$("#C15").val();var C16=$("#C16").val();var C17=$("#C17").val();var C18=$("#C18").val();var C19=$("#C19").val();var C20=$("#C20").val();
		var C21=$("#C21").val();var C22=$("#C22").val();var C23=$("#C23").val();var C24=$("#C24").val();var C25=$("#C25").val();var C26=$("#C26").val();
            var C27=$("#C27").val();
            var C0=0;//$("#C1").attr('idx');

		//$('#msg').html(allsqlcmd);return;	 // debug show info
		$.ajax({
			async: false,
			url: "./pages/"+sub+"/register.php",
			cache: false,
			dataType: 'html',
			type:'POST',
			data:{tbname:tbname,mode:mode,C0:C0,C1:C1,C2:C2,C3:C3,C4:C4,C5:C5,C6:C6,C7:C7,C8:C8,C9:C9,C10:C10,C11:C11,C12:C12,C13:C13,C14:C14,C15:C15,C16:C16,C17:C17,C18:C18,C19:C19,C20:C20,C21:C21,C22:C22,C23:C23,C24:C24,C25:C25,C26:C26,C27:C27},
			error: function (data) {
			    alert("失敗!!!");
			},
			success: function (data) {
			    //alert(data);return;
				//$('#msg').html(data);return;	 // debug show info
                if (data < 0){alert("新增失敗(錯誤代碼:"+data+")!");}
				else{alert("新增成功!");getclass();}
			}
		});
	});

	$('#del').click(function () {
		var tbname = $("#tb").val();
		var trafftb=$('.trafftb').val();
		var sub=$('.sub').val();
		var pujaname = $('#pujaid :selected').text();
		if (pujaname=='-'){alert("尚未指定法會!");return;}
		var mode="DELETE";
		var C1 = $("#C1").val();//name
		var C2=$("#C2").val();var C3=$("#C3").val();var C4=$("#C4").val();var C5=$("#C5").val();var C6=$("#C6").val();var C7=$("#C7").val();var C8=$("#C8").val();
		var C9=$("#C9").val();var C10 = $("#C10").val();var C11=$("#C11").val();var C12=$("#C12").val();var C13=$("#C13").val();var C14=$("#C14").val();
		var C15=$("#C15").val();var C16=$("#C16").val();var C17=$("#C17").val();var C18=$("#C18").val();var C19=$("#C19").val();var C20=$("#C20").val();
		var C21=$("#C21").val();var C22=$("#C22").val();var C23=$("#C23").val();var C24=$("#C24").val();var C25=$("#C25").val();
            var C26=$("#C26").val();var C27=$("#C27").val();
            var C0=$("#C1").attr('idx');
		//$('#msg').html(allsqlcmd);return;	 // debug show info
		$.ajax({
			async: false,
			url: "./pages/"+sub+"/register.php",
			cache: false,
			dataType: 'html',
			type:'POST',
			data:{tbname:tbname,mode:mode,C0:C0,C1:C1,C2:C2,C3:C3,C4:C4,C5:C5,C6:C6,C7:C7,C8:C8,C9:C9,C10:C10,C11:C11,C12:C12,C13:C13,C14:C14,C15:C15,C16:C16,C17:C17,C18:C18,C19:C19,C20:C20,C21:C21,C22:C22,C23:C23,C24:C24,C25:C25,C26:C26,C27:C27},
			error: function (data) {
			    alert("失敗!!!");
			},
			success: function (data) {
			    //alert(data);return;//$('#msg').html(data);	 // debug show info
                if (data < 0){alert("刪除失敗(錯誤代碼:"+data+")!");}
				else{alert("刪除成功!");getclass();}
			}
		});
	});

    $('#studentdetailinfo').click(function () {
        var sub = $('.sub').val();
        $.ajax({
            async: false,
            url: "./pages/" + sub + "/studentdetailinfo.php",
            cache: false,
            dataType: 'json', contentType: 'application/json; charset=utf-8',
            type: 'POST',
            data: {},
            error: function (data) {
                alert("失敗!!!-" + data);
            },
            success: function (data) {
                savestudentdetailinfo(data.result);
            }
        });
    });

    $('#studentinfo').click(function () {

    });
});

function getpuja() {
	$('.editmode').val("none");
	$('#queryresult').html("");
	var pujaid = $('#pujaid').val(); //classid = $(this).val();
	var pujaname = $('#pujaid :selected').text();
	var tb = $('.tb').val();
	var trafftb = $('.trafftb').val();
    var sub=$('.sub').val();
	var advpuja=$('.advpuja').val();

	if (pujaname=='-'){alert("尚未指定法會!");return;}

	// 送出查詢關鍵字至後端
	$('#send').show();
	if (advpuja=="YES"){$('#new').show();$('#del').show();}
	$.ajax({
		async: false,
		url: "./pages/"+sub+"/queryquery.php",
		cache: false,
		dataType: 'html',
		type:'POST',
		data:{pujaname:pujaname, pujaid:pujaid, tbname:tb, trafftb:trafftb},
		error: function (data) {
		    $('#send').prop('disabled',true);
			alert("查詢法會參數失敗!!!");//$('#queryresult').html(data);
		},success: function (data)
		{
			if(data==0){$('#queryresult').html("查無資料!"); $('#send').prop('disabled',true);}
			else{showpuja(data);$('#send').prop('disabled',false);}//$('#queryresult').html(data);
		}
	});
}

function showpuja(data) {
	//$('#queryresult').html(data);return; //debug
	var partsArray = data.split('|');
	var showdata="";
	if (partsArray.length<=0){return;}
	$('#send').prop('disabled',false);
	$('.editmode').val("parameter");
	var table1="<table class=\"reference\" id=\"fixheadertbl1\" style=\"width:850px\" align=\"center\">";
	table1+="<thead><tr>";

	// title row
	table1+="<th style=\"width:150px;\">項目</th><th style=\"width:650px;\">內容</th></tr>";
	table1+="</thead><tbody><tr></tr>";

	table1+="<tr><td style='text-align:center;'>法會名稱</td><td style='text-align:left;'><input style='width:630px' id='C1' class='C1' idx='"+partsArray[0]+"' type='text' value='"+partsArray[1]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>法會年度</td><td style='text-align:left;'><input style='width:630px' id='C2' class='C2' idx='"+partsArray[0]+"' type='text' value='"+partsArray[2]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>是否啟用</td><td style='text-align:left;'><input style='width:630px' id='C3' class='C3' idx='"+partsArray[0]+"' type='text' value='"+partsArray[3]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>報名開始日期</td><td style='text-align:left;'><input style='width:630px' id='C4' class='C4' idx='"+partsArray[0]+"' type='text' value='"+partsArray[4]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>報名結束日期</td><td style='text-align:left;'><input style='width:630px' id='C5' class='C5' idx='"+partsArray[0]+"' type='text' value='"+partsArray[5]+"'></td></tr>";

	table1+="<tr><td style='text-align:center;'>管理者起始日期</td><td style='text-align:left;'><input style='width:630px' id='C6' class='C6' idx='"+partsArray[0]+"' type='text' value='"+partsArray[6]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>管理者結束日期</td><td style='text-align:left;'><input style='width:630px' id='C7' class='C7' idx='"+partsArray[0]+"' type='text' value='"+partsArray[7]+"'></td></tr>";

      table1+="<tr><td style='text-align:center;'>窗口起始日期</td><td style='text-align:left;'><input style='width:630px' id='C24' class='C24' idx='"+partsArray[0]+"' type='text' value='"+partsArray[24]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>窗口結束日期</td><td style='text-align:left;'><input style='width:630px' id='C25' class='C25' idx='"+partsArray[0]+"' type='text' value='"+partsArray[25]+"'></td></tr>";

      table1+="<tr><td style='text-align:center;'>開放窗口</td><td style='text-align:left;'><input style='width:630px' id='C26' class='C26' idx='"+partsArray[0]+"' type='text' value='"+partsArray[26]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>關閉窗口</td><td style='text-align:left;'><input style='width:630px' id='C27' class='C27' idx='"+partsArray[0]+"' type='text' value='"+partsArray[27]+"'></td></tr>";

	table1+="<tr><td style='text-align:center;'>法會訊息公告開始日期</td><td style='text-align:left;'><input style='width:630px' id='C8' class='C8' idx='"+partsArray[0]+"' type='text' value='"+partsArray[8]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>法會訊息公告結束日期</td><td style='text-align:left;'><input style='width:630px' id='C9' class='C9' idx='"+partsArray[0]+"' type='text' value='"+partsArray[9]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>法會正行開始日期</td><td style='text-align:left;'><input style='width:630px' id='C10' class='C10' idx='"+partsArray[0]+"' type='text' value='"+partsArray[10]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>法會正行結束日期</td><td style='text-align:left;'><input style='width:630px' id='C11' class='C11' idx='"+partsArray[0]+"' type='text' value='"+partsArray[11]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>資料表</td><td style='text-align:left;'><input style='width:630px' id='C12' class='C12' idx='"+partsArray[0]+"' type='text' value='"+partsArray[12]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>統計資料表</td><td style='text-align:left;'><input style='width:630px' id='C13' class='C13' idx='"+partsArray[0]+"' type='text' value='"+partsArray[13]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>交通車次資料表</td><td style='text-align:left;'><input style='width:630px' id='C14' class='C14' idx='"+partsArray[0]+"' type='text' value='"+partsArray[14]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>去程費用</td><td style='text-align:left;'><input style='width:630px' id='C15' class='C15' idx='"+partsArray[0]+"' type='text' value='"+partsArray[15]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>回程費用</td><td style='text-align:left;'><input style='width:630px' id='C16' class='C16' idx='"+partsArray[0]+"' type='text' value='"+partsArray[16]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>去回程費用</td><td style='text-align:left;'><input style='width:630px' id='C17' class='C17' idx='"+partsArray[0]+"' type='text' value='"+partsArray[17]+"'></td></tr>";
	table1+="<tr><td style='text-align:center;'>幹部報名</td><td style='text-align:left;'><input style='width:630px' id='C18' class='C18' idx='"+partsArray[0]+"' type='text' value='"+partsArray[18]+"' /></td></tr>";
	table1+="<tr><td style='text-align:center;'>程式檔</td><td style='text-align:left;'><input style='width:630px' id='C19' class='C19' idx='"+partsArray[0]+"' type='text' value='"+partsArray[19]+"' /></td></tr>";
	table1+="<tr><td style='text-align:center;'>其他訊息(梯次或日期~)</td><td style='text-align:left;'><input style='width:630px' id='C20' class='C20' idx='"+partsArray[0]+"' type='text' value='"+partsArray[20]+"' /></td></tr>";
	table1+="<tr><td style='text-align:center;'>選單代碼</td><td style='text-align:left;'><input style='width:630px' id='C21' class='C21' idx='"+partsArray[0]+"' type='text' value='"+partsArray[21]+"' /></td></tr>";
	table1+="<tr><td style='text-align:center;'>法會說明訊息</td><td style='text-align:left;'><textarea style='width:630px' id='C22' class='C22' idx='"+partsArray[0]+"' row='5'>"+partsArray[22]+"</textarea></td></tr>";
	table1+="<tr><td style='text-align:center;'>標題圖檔</td><td style='text-align:left;'><input style='width:630px' id='C23' class='C23' idx='"+partsArray[0]+"' type='text' value='"+partsArray[23]+"' /></td></tr>";
	table1+="</table>";
	showdata+="<div id=\"tabs-1\" class=\"grid_x height450\" >"+table1+"</div>";
      showdata+="<input type='hidden' id='pujaidx' class='pujaidx' name='pujaidx' value='"+partsArray[0]+"' />";
	$('#queryresult').html(showdata);

	if (partsArray.length > 10){
	    $('#fixheadertbl1').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true});
	}

      $("#C4").datepicker();$("#C4").datepicker("option","dateFormat","yy-mm-dd");$("#C4").val(partsArray[4]);
      $("#C5").datepicker();$("#C5").datepicker("option","dateFormat","yy-mm-dd");$("#C5").val(partsArray[5]);
      $("#C6").datepicker();$("#C6").datepicker("option","dateFormat","yy-mm-dd");$("#C6").val(partsArray[6]);
      $("#C7").datepicker();$("#C7").datepicker("option","dateFormat","yy-mm-dd");$("#C7").val(partsArray[7]);
      $("#C8").datepicker();$("#C8").datepicker("option","dateFormat","yy-mm-dd");$("#C8").val(partsArray[8]);
      $("#C9").datepicker();$("#C9").datepicker("option","dateFormat","yy-mm-dd");$("#C9").val(partsArray[9]);
      $("#C10").datepicker();$("#C10").datepicker("option","dateFormat","yy-mm-dd");$("#C10").val(partsArray[10]);
      $("#C11").datepicker();$("#C11").datepicker("option","dateFormat","yy-mm-dd");$("#C11").val(partsArray[11]);
      $("#C24").datepicker();$("#C24").datepicker("option","dateFormat","yy-mm-dd");$("#C24").val(partsArray[24]);
      $("#C25").datepicker();$("#C25").datepicker("option","dateFormat","yy-mm-dd");$("#C25").val(partsArray[25]);
}

// new traffic data
function InsertNewTraffic() {
	var traffictb=$('#addtraffic').attr('addnewtbname');
	var trafficid=$('#addid').val();
	var trafficName=$('#addname').val();
	var trafficday=$('#addday').val();
	var traffdesc=$('#traffdesc').val();
	var sub=$('.sub').val();
	if (traffictb==""||trafficid == ""){alert("資料異常!"+traffictb+""+trafficid);return ;}

	//alert(rollcalltb);return;
	$.ajax({
		async: false, url: "./pages/"+sub+"/add.php",cache: false, dataType: 'html',type:'POST',
		data:{traffictb:traffictb, trafficid:trafficid, trafficName:trafficName, trafficday:trafficday,traffdesc:traffdesc},
		error: function (data) {alert("資料庫異常!!!");},
		success: function (data) {
			//$('#queryresult').html(data);
			if (data==-1){alert("車次已存在!");}
			else{$('#queryresult').html(""); gettraffic();}
		}
	});
}

function removeTraffic(trafficid,trafficday,traffictb) {
	var sub=$('.sub').val();//alert(trafficid+"-"+trafficday+"-"+traffictb+"-"+sub);
	$.ajax({
		async: false, url: "./pages/"+sub+"/remove.php",cache: false, dataType: 'html',type:'POST',
		data:{traffictb:traffictb, trafficid:trafficid,trafficday:trafficday},
		error: function (data) {alert("資料庫異常!!!");},
		success: function (data) {
		    //alert(data);//debug
			//$('#queryresult').html(data);
			$('#queryresult').html(""); gettraffic();
		}
	});
}

function gettraffic() {
	$('.editmode').val("none");
	$('#queryresult').html("");
	var pujaid = $('#pujaid').val(); //classid = $(this).val();
	var pujaname = $('#pujaid :selected').text();
	var tb = $('.tb').val();
	var trafftb = $('.trafftb').val();

    var sub=$('.sub').val();
	if (pujaname=='-'){alert("尚未指定法會!");return;}

	$('#send').hide();$('#new').hide();$('#del').hide();
	// 送出查詢關鍵字至後端
	$.ajax({
		async: false,
		url: "./pages/"+sub+"/querytraffic.php",
		cache: false,
		dataType: 'html',
		type:'POST',
		data:{pujaname:pujaname, pujaid:pujaid, tbname:tb, trafftb:trafftb},
		error: function (data) {
			alert("查詢車次資料失敗!!!");//$('#queryresult').html(data);
		},success: function (data)
		{
			if(data<=0){$('#queryresult').html("查無資料!");}
			else if(data==1){showtraffic("");}
			else{showtraffic(data);}//$('#queryresult').html(data);
		}
	});
}

function showtraffic(data) {
	//$('#queryresult').html(data);return; //debug
	var showdata="";
	$('.editmode').val("traffic");

	// Parser every student of this class
	var partsAry = data.split(';');
	var traffdata=[];
	$('#send').prop('disabled',true);

    // prepare title & input field
	var width=550;//1140;
	var item1W=50;//天
	var item2W=100;//車次代碼
	var item3W=250;//車次
	var item4W=200;//車次
	var item5W=100;//車次

	// title
	showdata="<table class='reference' id='fixtrafftbl' style='width:"+width+"px;'  align=\"center\">";//text-align:center;vertical-align:middle;
	showdata+="<thead><tr>";
	showdata+="<th align='center' style='width:"+item1W+"px'>天</th>";
	showdata+="<th align='center' style='width:"+item2W+"px'>車次代碼<br>(必填)</th>";
	showdata+="<th align='center' style='width:"+item3W+"px'>車次名稱<br>(必填)</th>";
    showdata+="<th align='center' style='width:"+item3W+"px'>車次區域<br>(必選)</th>";
    showdata+="<th align='center' style='width:"+item4W+"px'></th>";
    showdata+="</tr></thead><tbody><tr></tr>";

	// new record
    showdata+="<tr>";
    showdata+="<td align='center'><select style='width:70px;align:center;' id='addday' class='addday' name='addday'><option value='0'>第1天</option>";
    showdata+="<option value='1'>第2天</option><option value='2'>第3天</option><option value='3'>第4天</option><option value='4'>第5天</option><option value='5'>第6天</option><option value='6'>第7天</option><option value='7'>第8天</option>";
    showdata+="</select></td>";//天
    showdata+="<td align='center' style='padding:2 0 2 0 px;'><input type='text' style='width:70px;' id='addid' class='addid' name='addid'/></td>";//ID
    showdata+="<td align='center' style='padding:2 0 2 0 px;'><input type='text' style='width:200px;' id='addname' class='addname' name='addname'/></td>";//DESC

    showdata+="<td align='center'><select style='width:130px;align:center;' id='traffdesc' class='traffdesc' name='traffdesc'>";
    showdata+="<option value='*'>高雄</option>";
    showdata+="<option value='3'>屏東</option>";
    showdata+="<option value='2'>台南</option>";
    showdata+="</select></td>";//天
	// action
	showdata+="<td align='center'><input type=\"image\" style='border:none;outline:0;' src=\"./img/ok-disable.png\" id=\"addtraffic\" class=\"addtraffic\" addnewtbname='"+partsAry[0]+"' /> ";
	showdata+="</td></tr>";

	var temp=$('.currentdate').val();
	//var curdate = getDateFromFormat(temp,"yyyy-MM-dd");
	var day=1;
	if (partsAry.length>=1) {
		for(i=1;i<partsAry.length;i++) {
                traffdata=partsAry[i].split('|');
                if (traffdata.length<4){continue;}
                //if (traffdata[1]=="Z" || traffdata[1]=="z"){continue;}
                day=parseInt(traffdata[2])+1;
                showdata+=("<tr><td align='center'>第"+day+"天</td>");
                showdata+=("<td align='center'>"+traffdata[1]+"</td>");
                showdata+=("<td align='center'>"+traffdata[3]+"</td>");
                if (traffdata[4]=="*"){
                    showdata+=("<td align='center'>高雄</td>");
                }else if (traffdata[4]=="3"){
                    showdata+=("<td align='center'>屏東</td>");
                }else if (traffdata[4]=="2"){
                    showdata+=("<td align='center'>台南</td>");
                }

                showdata+="<td align='center'>";
                showdata+="<input type=\"image\" style='border:none;outline:0;' src=\"./img/remove.png\"";
                showdata+="id=\"removetraffic\" class=\"removetraffic\" traffid='"+traffdata[1]+"' traffname='"+traffdata[3]+"' traffday='"+traffdata[2]+"' removetbname='"+partsAry[0]+"' /> ";
                showdata+="</td>";

                showdata+="</tr>";
		}
	}

	showdata+="</tbody></table>";
	//showdata+="<input type='hidden' id='trafficdb' class='trafficdb' name='trafficdb' value='"+partsAry[0]+"' />";
	var fixedCnt=100;
      $('#queryresult').html(showdata);//text(showdata);//html(showdata);
	if (partsAry.length>=fixedCnt) // generate scroll bar for fix header
	{
        $('#fixtrafftbl').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true});
	}

	// message response
	$(".addtraffic" ).hover(function(){
			if(dataComplete()==false){$(this).attr("src", "./img/ok-disable.png");}
			else {$(this).attr("src", "./img/ok-deep.png");}
		},
		function(){
			if(dataComplete()==false){$(this).attr("src", "./img/ok-disable.png");}
			else {$(this).attr("src", "./img/ok-enable.png");
		}
	});

	$("#addname").on('input',function() {
		if(dataComplete()==false){$('.addtraffic').attr("src", "./img/ok-disable.png");}
		else{$('.addtraffic').attr("src", "./img/ok-enable.png");}
	});

	$("#addid").on('input',function() {
		if(dataComplete()==false){$('.addtraffic').attr("src", "./img/ok-disable.png");}
		else{$('.addtraffic').attr("src", "./img/ok-enable.png");}
	});

	$('.addtraffic').click(function(event) {
	    if(dataComplete()==false){return;}
		InsertNewTraffic();
	});

	$('.removetraffic').click(function(event) {
     	var trafficid=$(this).attr('traffid');
		var trafficName=$(this).attr('traffname');
		var trafficday=$(this).attr('traffday');
        var traffictb=$(this).attr('removetbname');
		removeTraffic(trafficid,trafficday,traffictb);
	});
}

function isVaildName(Name) {
	//中文：/^[\u4E00-\u9FA5]+$/
	//數字：/^d+$/（是非負整數哦）
	//字母：/^[a-zA-Z]{1,30}$/（1到30個以字母串）
    var bvalid=true;
    for(var i = 0; i < Name.length; i++) {
        if(Name.charCodeAt(i) < 0x4E00 || Name.charCodeAt(i) > 0x9FA5) {
            bvalid=false;
            break;
        }
    }
	return bvalid;
}

function dataComplete() {
    // check id, desc
　　var traffid=$('.addid').val();
　　var traffname=$('.addname').val();

	if(addid.length < 1){return false;}

	if(isVaildName(traffname)==false){return false;}
	if(traffname.length < 2){return false;}
	return true;
}

function Workbook() {
    if (!(this instanceof Workbook)) {
        return new Workbook();
    }
    this.SheetNames = [];
    this.Sheets = {};
}

function sheet_from_array_of_arrays(data, opts) {
    let ws = {};
    let range = { s: { c: 10000000, r: 10000000 }, e: { c: 0, r: 0 } };
    for (let R = 0; R != data.length; ++R) {
        for (let C = 0; C != data[R].length; ++C) {
            if (range.s.r > R) {
                range.s.r = R;
            }
            if (range.s.c > C) {
                range.s.c = C;
            }
            if (range.e.r < R) {
                range.e.r = R;
            }
            if (range.e.c < C) {
                range.e.c = C;
            }
            let cell = { v: data[R][C] };
            if (cell.v !== null) {
                let cell_ref = XLSX.utils.encode_cell({ c: C, r: R });
                if (typeof cell.v === 'number') {
                    cell.t = 'n';
                } else if (typeof cell.v === 'boolean') {
                    cell.t = 'b';
                } else if (cell.v instanceof Date) {
                    cell.t = 'n';
                    cell.z = XLSX.SSF._table[14];
                    cell.v = datenum(cell.v);
                } else {
                    cell.t = 's';
                }
                //cell.fill={ fgColor:{rgb:"FFFFAA00"}};
                ws[cell_ref] = cell;
            }
        }
    }
    if (range.s.c < 10000000) {
        ws['!ref'] = XLSX.utils.encode_range(range);
    }
    return ws;
}

function s2ab(s) {
    let buf = new ArrayBuffer(s.length);
    let view = new Uint8Array(buf);
    for (let i = 0; i != s.length; ++i) {
        view[i] = s.charCodeAt(i) & 0xFF;
    }
    return buf;
}

function savestudentdetailinfo(da) {

    var wb = new Workbook(), exceldata = [], dataSub = [];
    var item = ['班級', '教室', '班級代號', '姓名', '身份', '學員代號', '性別', '年齡'];
    exceldata.push(item);
    for (var i = 0, len = da.length; i < len; i++ , dataSub = []) {
        for (var w = 0; w < 8; w++) {
            dataSub.push(da[i][item[w]]);
        }
        exceldata.push(dataSub);
    }
    var ws = sheet_from_array_of_arrays(exceldata);
    wb.SheetNames.push('學員名冊(含幹部)');
    wb.Sheets['學員名冊(含幹部)'] = ws;

    exceldata = [];
    exceldata.push(item);
    for (var i = 0, len = da.length; i < len; i++ , dataSub = []) {
        if (da[i]['身份'] != '學員') {continue;}
        for (var w = 0; w < 8; w++) {
            dataSub.push(da[i][item[w]]);
        }
        exceldata.push(dataSub);
    }
    ws = sheet_from_array_of_arrays(exceldata);
    wb.SheetNames.push('學員名冊');
    wb.Sheets['學員名冊'] = ws;
    var daystring = moment().format('YYYYMMDD-HHmmss');
    var wbout = XLSX.write(wb, { bookType: 'xlsx', bookSST: true, type: 'binary' });
    var filename = '高區學員名冊-' + daystring + '.xlsx';
    saveAs(new Blob([s2ab(wbout)], { type: 'application/octet-stream' }), filename);
}