$(document).ready(function () 
{	
    // TOP PAGE
	$.ajax({
		async: false,
		url: "top.php",
		success: function (data) {
			$("#pageTop").append(data);
		}
	});
	
	// select file 
	$('#selectfile').on('click', function() {
		$('#studentfile').trigger('click');	//alert($('#studentfile').val());
	});	
	
	$("#studentfile").change(function(event) {
        //alert($('#studentfile').val());
		//strPath = $('#studentfile').val();
		//strPath = strPath.replace("C:\\fakepath\\", "");
		
		//alert($('input[type=file]').val());
		//files[0].mozFullPath
		//var tmppath = URL.createObjectURL(event.target.files[0]);
		//var tmppath = this.files[0].mozFullPath;//URL.createObjectURL(event.target.files[0].mozFullPath);
		//$("img").fadeIn("fast").attr('src',tmppath);
		//alert(tmppath);
		
		var filePath = $(this).val(); //console.log(filePath);
		filePath = filePath.replace("C:\\fakepath\\", "");	
		$('#textstudentfile').val(filePath);
    });
	
/*	
	var getPath = function (obj,fileQuery,transImg)
	{
        if (window.navigator.userAgent.indexOf( "MSIE" )>=1)
		{
            obj.select();
            var  path = document.selection.createRange().text;
            obj.removeAttribute( "src" ); 
            obj.setAttribute( "src" ,transImg);
            obj.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" +path+ "',sizingMethod='scale');" ; 
        } else {
            var  file=fileQuery.files[0]; 
            var  reader= new  FileReader(); 
            reader.onload=function (e){
                obj.setAttribute( "src" ,e.target.result)
            }
            reader.readAsDataURL(file); 
        }
    }
*/	

	var progressbox     = $('#progressbox');
	var progressbar     = $('#progressbar');
	var statustxt       = $('#statustxt');
	var completed       = '0%';
	var txtFile         = $('#textstudentfile');
	
	var options = 
	{ 
		target:         '#output',   // target element(s) to be updated with server response 
		beforeSubmit:   beforeSubmit,  // pre-submit callback 
		uploadProgress: OnProgress,
		success:        afterSuccess,  // post-submit callback 
		resetForm:      true        // reset the form after successful submit 
	}; 	
	
	$('#UploadForm').submit(function() 
	{
		alert("upload ajax start!!!");	
		$(this).ajaxSubmit(options); 

		// return false to prevent standard browser submit and page navigation 

		return false; 
	});	
	

	
	//when upload progresses	
	function OnProgress(event, position, total, percentComplete)
	{
		//Progress bar
		progressbar.width(percentComplete + '%') //update progressbar percent complete
		statustxt.html(percentComplete + '%'); //update status text
		if(percentComplete>50)
		{
			//statustxt.css('color','#fff'); //change status text to white after 50%
		}
	}

	//after succesful upload
	function afterSuccess()
	{
		////$('#submit-btn').show(); //hide submit button
		////$('#loading-img').hide(); //hide submit button
		
		txtFile.show();
		progressbox.hide();
	}

	//function to check file size before uploading.
	function beforeSubmit()
	{
	    //alert("beforeSubmit");
		//check whether browser fully supports all File API
	    if (window.File && window.FileReader && window.FileList && window.Blob)
		{

			if( !$('#studentfile').val()) //check empty input filed
			{
				$("#output").html("Are you kidding me?");
				return false
			}
			
			var fsize = $('#studentfile')[0].files[0].size; //get file size
			var ftype = $('#studentfile')[0].files[0].type; // get file type
			
			//alert(ftype);
			//allow only valid image file types 
	/*		switch(ftype)
			{
				case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
					break;
				default:
					$("#output").html("<b>"+ftype+"</b> Unsupported file type!");
					return false
			}
	*/		
			//Allowed file size is less than 1 MB (1048576)
			if(fsize>(1048576*32)) 
			{
				$("#output").html("<b>"+bytesToSize(fsize) +"</b> Too big Image file! <br />Please reduce the size of your photo using an image editor.");
				return false
			}
			
			//Progress bar
			
			txtFile.hide();
			progressbox.show(); //show progressbar
			progressbar.width(completed); //initial value 0% of progressbar
			statustxt.html(completed); //set status text
			statustxt.css('color','#000'); //initial color of status text
		
			////$('#submit-btn').hide(); //hide submit button
			////$('#loading-img').show(); //hide submit button
			////$("#output").html("");  
			//alert("beforeSubmit OK!!!");
		}
		else
		{
			//Output error to older unsupported browsers that doesn't support HTML5 File API
			////$("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
			return false;
		}
	}

	//function to format bites bit.ly/19yoIPO
	function bytesToSize(bytes) {
	   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	   if (bytes == 0) return '0 Bytes';
	   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
	}	
});	