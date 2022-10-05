$(document).ready(function () 
{	
	$.ajax({
		async: false,
		url: "top.php",
		success: function (data) {
			$("#pageTop").append(data);
		}
	});
    
    
    
    $('#updateqx').click(function () 
    {
        classroomtb="classroom2015";
        classroomnxtb="classroom2016";
        rollcalltb="rollcall2015";
        rollcallnxtb="rollcall2016";        
        $.ajax({
            async: false,
            url: "./pages/rcall/updateqx.php",
            cache: false,
            dataType: 'html',
            type:'POST',
            timeout: 30000,
            data:{classroomtb:classroomtb,rollcalltb:rollcalltb,classroomnxtb:classroomnxtb,rollcallnxtb:rollcallnxtb},
            error: function (data) {
                alert("資料庫異常!!!");
            },
            success: function (data) {
                if (parseInt(data)<=0||data=="")
                    alert("成功!!!");
                else
                    alert("失敗!!! 錯誤代碼:"+data);//alert("學員點名上傳失敗(請勿輸入特殊字元), 錯誤代碼:"+data+" !!!");
                //$('#msg').html(data); //debug
            }	
        });
    });
});		
