$(document).ready(function () 
{	
	$.ajax({
		async: false,
		url: "top.php",
		success: function (data) {
			$("#pageTop").append(data);
		}
	});
});		
