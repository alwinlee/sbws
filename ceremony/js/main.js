$(document).ready(function () 
{	
	var navigation = $($.find('.navigation'));	

	$.ajax({
		async: false,
		url: "top.php",
		success: function (data) {
			$("#pageTop").append(data);
		}
	});
});
	
