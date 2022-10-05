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

function initMember()
{	
	$('#W20ALL').click(function(event)
	{
		//alert('click W20ALL');	
	});
}		

function DoW21AllClick()
{
	//alert('DoW21AllClick OK!!!');
	var x = document.getElementById("W21ALL");
	
	if (x.checked)
	{
		//alert('W21 checked');
		$('.W21').each(function() {
		    this.checked = true;
		});
	}
	else
	{
		//alert('W21 not checked');
		$('.W21').each(function() {
		    this.checked = false;
		});	
	}
}

function DoW20AllClick()
{
	//alert('DoW20AllClick OK!!!');
	
	var x = document.getElementById("W20ALL");
	
	if (x.checked)
	{
		//alert('W20 checked');
	}
	else
	{
		//alert('W20 not checked');
	}	
}	


/*
$(document).ready(function () 
{
	$.ajax({
		async: false,
		url: "bottom.htm",
		success: function (data) {
			$("#pageBottom").append(data);
		}
	});
	$.ajax({
		async: false,
		url: "top.htm",
		success: function (data) {
			$("#pageTop").append(data);
		}
	});

	$(document).bind('contextmenu', function (e) { return false; });
	var content = $('#demos')[0];
	var navigation = $($.find('.navigation'));
	var self = this;
	if (!$.jqx.browser.msie) {
		$('#navigationmenu').find('li').css('opacity', 0.95);
		$('#navigationmenu').find('ul').css('opacity', 0.95);
	}
	$('#navigationmenu').jqxMenu({ theme: 'demo', autoSizeMainItems: true, autoCloseOnClick: true });
	$('#navigationmenu').css('visibility', 'visible');
	$(window).resize(function () {
		if (window.screen.width <= 1024) {
			$('#navigationmenu').jqxMenu('minimize');
		}
		else {
			$('#navigationmenu').jqxMenu('restore');
		}
	});
	if (window.screen.width <= 1024) {
		$('#navigationmenu').jqxMenu('minimize');
	}
	else {
		$('#navigationmenu').jqxMenu('restore');
	}
/*/			
/*          
   if ($.jqx.response) {
		var response = new $.jqx.response();
		if (response.device.type != "Desktop") {
			var content = $(".demoContainer");
			if (response.device.type == "Phone") {
				$("#navigationmenu").css('position', 'absolute');
				$("#navigationmenu").css('top', '25px');
				$("#navigationmenu").css('right', '0px');
				$("#navigationmenu").css('left', '-20px');
				$(window).on('orientationchange', function () {
					$("#navigationmenu").css('position', 'absolute');
					$("#navigationmenu").css('right', '0px');
					$("#navigationmenu").css('left', '-20px');
				});
			}
			$(document.body).addClass('body-mobile');
			$('.contenttable').addClass('contenttable-mobile');
			$('.contentdiv').addClass('contentdiv-mobile');
			$('.content').addClass('content-mobile');
			$('.demoContainer').addClass('demoContainer-mobile');
			$('.navigation').addClass('navigation-mobile');
			$('.jqxDemoContainer').addClass('jqxDemoContainer-mobile');
			$('.top').addClass('top-mobile');
			$('.bottom').addClass('bottom-mobile');
			var collapseButton = $("<div style='position: absolute; z-index:9999; top: 0px; left: 204px; width: 15px; height: 20px; background: #fbfbfb; border: 1px solid #e9e9e9;'><div style='position: relative; top: 3px; left: 1px; width: 15px; height: 15px;' class='jqx-icon-arrow-left'></div></div>");
			$(".navigation").prepend(collapseButton);
			$(".navigation").css('position', 'relative');
			var hidden = false;
			collapseButton.click(function () {
				if (!hidden) {
					$(".navigation").hide(150);
					collapseButton.detach();
					$(document.body).append(collapseButton);
					collapseButton.children()[0].className = 'jqx-icon-arrow-right';
					collapseButton.css('left', '0px');
					collapseButton.css('top', '65px');
				}
				else {
					collapseButton.detach();
					$(".navigation").prepend(collapseButton);
					$(".navigation").show(150);
					collapseButton.children()[0].className = 'jqx-icon-arrow-left';
					collapseButton.css('left', '204px');
					collapseButton.css('top', '0px');
				}
				hidden = !hidden;
			});
		}
	}
	var me = this;
	$(".navigationItem").click(function (event) {
		var currentTarget = event.target;
		if (currentTarget.nodeName.toLowerCase() == "img") currentTarget = $(currentTarget).parent().parent();

		if ($(currentTarget).children().length > 0) {
			var target = $(currentTarget).children()[0];
			if ($(currentTarget).children().length == 3) {
				var target = $($($(currentTarget).children()[1]).children()[0])[0];
			}
			else if ($(currentTarget).children().length == 3) {
				var target = $($($(currentTarget).children()[1]).children()[0])[0];
			}
			if (target.nodeName.toLowerCase() == "a") {
				var anchor = $(target);

				if (anchor.text().indexOf('Introduction') != -1)
					window.open(anchor[0].href, '_self');

				if (anchor.text().indexOf('jQuery Basics') != -1)
					window.open(anchor[0].href, '_self');

				if (anchor.text().indexOf('jqxDataAdapter') != -1)
					window.open(anchor[0].href, '_self');

				if (anchor.text().indexOf('MVVM with Knockout') != -1)
					window.open(anchor[0].href, '_self');

				if (anchor.text().indexOf('Roadmap') != -1)
					window.open(anchor[0].href, '_self');

				if (anchor.text().indexOf('jqxResponse') != -1)
					window.open(anchor[0].href, '_self');

				if (anchor.text().indexOf('Release History') != -1)
					window.open(anchor[0].href, '_self');

				if (anchor.text().indexOf('Accessibility') != -1)
					window.open(anchor[0].href, '_self');

				anchor.trigger('click');
			}
		}
	});
	navigation.find('.navigationItemContent').click(function (event) {
		if ($(event.target).children().length > 0) {
			if ($(event.target).children()[0].nodeName.toLowerCase() == "a") {
				var anchor = $($(event.target).children()[0]);
				window.open(anchor[0].href, '_self');
			}
		}
	});
	navigation.find('.navigationHeader').click(function (event) {
		var $target = $(event.target);
		var $targetParent = $target.parent();
		if ($target.text().indexOf('Introduction') != -1)
			return;

		if ($target.text().indexOf('jQuery Basics') != -1)
			return;

		if ($target.text().indexOf('Roadmap') != -1)
			return;

		if ($target.text().indexOf('Release History') != -1)
			return;

		if ($target.text().indexOf('jqxDataAdapter') != -1)
			return;

		if ($target.text().indexOf('MVVM with Knockout') != -1)
			return;

		if ($target.text().indexOf('jqxResponse') != -1)
			return;

		if ($target.text().indexOf('Accessibility') != -1)
			return;

		if ($targetParent[0].className.length == 0) {
			var $targetParentParent = $($target.parent()).parent();
			var oldChildren = $.data(content, 'expandedElement');
			var oldTarget = $.data(content, 'expandedTarget');

			if (oldTarget != null && oldTarget != event.target) {
				var $oldTarget = $(oldTarget);
				var $oldtargetParentParent = $($oldTarget.parent()).parent();
				if (oldChildren.css('display') == 'block') {
					oldChildren.css('display', 'none');
					$oldtargetParentParent.removeClass('navigationItem-expanded');
					$oldtargetParentParent.find('.navigationContent').css('display', 'none');
					$oldtargetParentParent.find('.topicimage')[0].src = "images/topic.png";
				}
			}

			var children = $targetParentParent.find('.navigationItemContent');
			$.data(content, 'expandedElement', children);
			$.data(content, 'expandedTarget', event.target);

			if (children.css('display') == 'none') {
				children.css({ opacity: 0, display: 'block', visibility: 'visible' }).animate({ opacity: 1.0 }, 0, function () {
					if ($.jqx.browser.msie)
						$(this).get(0).style.removeAttribute('filter');
				});

				if ($targetParentParent[0].className == 'navigationItem') {
					$targetParentParent.addClass('navigationItem-expanded');
					$targetParentParent.find('.navigationContent').css('display', 'block');
					$targetParentParent.find('.topicimage')[0].src = "images/topic_open.png";
				}
			}
			else children.css({ opacity: 1, visibility: 'visible' }).animate({ opacity: 0.0 }, 0, function () {
				children.css('display', 'none');
				$targetParentParent.removeClass('navigationItem-expanded');
				$targetParentParent.find('.navigationContent').css('display', 'none');
				$targetParentParent.find('.topicimage')[0].src = "images/topic.png";
			});

		}
		return false;
	});

	var $element = $($.find('.item-expanded'));
	var children = $element.find('.navigationItemContent');
	$.data(content, 'expandedElement', children);
	$.data(content, 'expandedTarget', $element[0]);

	if (children.css('display') == 'none') {
		children.css({ opacity: 0, display: 'block', visibility: 'visible' }).animate({ opacity: 1.0 }, 0, function () {
			if ($.jqx.browser.msie)
				$(this).get(0).style.removeAttribute('filter');
		});

		$element.addClass('navigationItem-expanded');
		$element.find('.navigationContent').css('display', 'block');
		$element.find('.topicimage')[0].src = "images/topic_open.png";
	}
});			
*/		
