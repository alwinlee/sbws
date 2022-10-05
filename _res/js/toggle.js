function inittoggle() {

    (function ($) { $.jqx.response = function () { this.defineInstance() }; $.jqx.response.prototype = { defineInstance: function () { this._handlers = new Array(); this.refresh(); var that = this; this.addHandler($(document), "scroll.jqxresponse", function () { that.scroll = that.getScroll() }) }, refresh: function () { this.os = this.getOS(); this.browser = this.getBrowser(); this.device = this.getDevice(); this.viewPort = this.getViewPort(); this.document = this.getDocument(); this.scroll = this.getScroll(); this.media = window.matchMedia || window.msMatchMedia || function () { return {} } }, refreshSize: function () { this.viewPort = this.getViewPort(); this.document = this.getDocument() }, addHandler: function (source, event, func, data) { switch (event) { case "mousemove": if (window.addEventListener && !data) { source[0].addEventListener("mousemove", func, false); return false } break } if (source.on) { source.on(event, func) } else { source.bind(event, func) } }, removeHandler: function (source, event, func) { if (event == undefined) { if (source.off) { source.off() } else { source.unbind() } return } if (func == undefined) { if (source.off) { source.off(event) } else { source.unbind(event) } } else { if (source.off) { source.off(event, func) } else { source.unbind(event, func) } } }, destroy: function () { this.removeHandler($(window), "resize.jqxresponse"); this.removeHandler($(document), "scroll.jqxresponse"); for (var i = 0; i < this._handlers.length; i++) { var element = this._handlers[i]; this.removeHandler($(element), "mousedown.response" + element[0].id); this.removeHandler($(element), "touchstart.response" + element[0].id); this.removeHandler($(element), "mousemove.response" + element[0].id); this.removeHandler($(element), "touchmove.response" + element[0].id); this.removeHandler($(element), "mouseup.response" + element[0].id); this.removeHandler($(element), "touchend.response" + element[0].id) } }, resize: function (resizeFuncs) { var that = this; this.removeHandler($(window), "resize.jqxresponse"); this.addHandler($(window), "resize.jqxresponse", function (event) { if (resizeFuncs) { if ($.isArray(resizeFuncs)) { for (var i = 0; i < resizeFuncs.length; i++) { resizeFuncs[i]() } } else { resizeFuncs() } } that.refreshSize() }); if (resizeFuncs == null) { this.removeHandler($(window), "resize.jqxresponse") } }, pointerDown: function (element, func) { if (element && func) { var touchDevice = $.jqx.mobile.isTouchDevice(); var that = this; var canCallFunc = true; if (touchDevice) { var touchstart = $.jqx.mobile.getTouchEventName("touchstart") + ".response" + element[0].id; if (func != null) { this.addHandler($(element), touchstart, function (event) { var position = $.jqx.position(event); var result = func(event, position, "touch"); canCallFunc = false; setTimeout(function () { canCallFunc = true }, 500); return result }) } else { this.removeHandler($(element), touchstart) } } if (func != null) { this.addHandler($(element), "mousedown.response" + element[0].id, function (event) { var position = $.jqx.position(event); if (canCallFunc) { return func(event, position, "mouse") } }) } else { this.removeHandler($(element), "mousedown.response" + element[0].id) } this._handlers.push(element) } }, pointerUp: function (element, func) { if (element) { var touchDevice = $.jqx.mobile.isTouchDevice(); var that = this; var canCallFunc = true; if (touchDevice) { var touchend = $.jqx.mobile.getTouchEventName("touchend") + ".response" + element[0].id; if (func != null) { this.addHandler($(element), touchend, function (event) { var position = $.jqx.position(event); var result = func(event, position, "touch"); canCallFunc = false; setTimeout(function () { canCallFunc = true }, 500); return result }) } else { this.removeHandler($(element), touchend) } } if (func != null) { this.addHandler($(element), "mouseup.response" + element[0].id, function (event) { var position = $.jqx.position(event); if (canCallFunc) { return func(event, position, "mouse") } }) } else { this.removeHandler($(element), "mouseup.response" + element[0].id) } this._handlers.push(element) } }, pointerMove: function (element, func) { if (element) { var touchDevice = $.jqx.mobile.isTouchDevice(); if (touchDevice) { var touchmove = $.jqx.mobile.getTouchEventName("touchmove") + ".response" + element[0].id; if (func != null) { this.addHandler($(element), touchmove, function (event) { var touches = $.jqx.mobile.getTouches(event); if (touches.length == 1) { var position = $.jqx.position(event); return func(event, position, "touch") } }) } else { this.removeHandler($(element), touchmove) } } else { if (func != null) { this.addHandler($(element), "mousemove.response" + element[0].id, function (event) { var position = $.jqx.position(event); return func(event, position, "mouse") }) } else { this.removeHandler($(element), "mousemove.response" + element[0].id) } } this._handlers.push(element) } }, isHidden: function (element) { return $.jqx.isHidden($(element)) }, inViewPort: function (element) { var viewPort = this.viewPort; if (element.getBoundingClientRect) { var r = element.getBoundingClientRect ? element.getBoundingClientRect() : {}; return r && (r.bottom >= 0 && r.top <= viewPort.height && r.right >= 0 && r.left <= viewPort.width) } return false }, getScroll: function () { var obj = { left: window.pageXOffset || document.scrollLeft, top: window.pageYOffset || document.scrollTop }; if (obj.left == undefined) { obj.left = 0 } if (obj.top == undefined) { obj.top = 0 } return obj }, getDocument: function () { return { width: $(document).width(), height: $(document).height() } }, getViewPort: function () { return { width: $(window).width(), height: $(window).height() } }, getTouch: function () { var eventName = "ontouchstart"; var supported = (eventName in window); if (supported) { return true } else { var eventName = "MSPointerDown"; var supported = (eventName in window); if (supported) { return true } } if ($.jqx.mobile.isWindowsPhone()) { return true } return false }, getDevice: function () { var osName = this.os.name; var match = window.location.search.match(/deviceType=(Tablet|Phone)/), nativeDeviceType = window.deviceType; var deviceType = ""; if (match && match[1]) { deviceType = match[1] } else { if (nativeDeviceType === "iPhone") { deviceType = "Phone" } else { if (nativeDeviceType === "iPad") { deviceType = "Tablet" } else { if (osName != "Android" && osName != "iOS" && /Windows|Linux|MacOS|Mac OS|Mac OS X/.test(osName)) { deviceType = "Desktop" } else { if (osName == "iOS" && navigator.userAgent.toLowerCase().indexOf("ipad") >= 0) { deviceType = "Tablet" } else { if (osName == "RIMTablet") { deviceType = "Tablet" } else { if (osName == "Android") { if (this.os.version && this.os.version.substring(0, 1).indexOf("3") >= 0) { deviceType = "Tablet" } else { if (this.os.version && this.os.version.substring(0, 1).indexOf("4") >= 0 && navigator.userAgent.search(/mobile/i) == -1) { deviceType = "Tablet" } else { deviceType = "Phone" } } if (navigator.userAgent.toLowerCase().indexOf("kindle fire") >= 0) { deviceType = "Tablet" } } else { deviceType = "Phone" } } } } } } } if (/Windows/.test(osName)) { if (navigator.userAgent.indexOf("Windows Phone") >= 0 || navigator.userAgent.indexOf("WPDesktop") >= 0 || navigator.userAgent.indexOf("IEMobile") >= 0 || navigator.userAgent.indexOf("ZuneWP7") >= 0) { deviceType = "Phone" } else { if (navigator.userAgent.indexOf("Touch") >= 0) { deviceType = "Tablet"; if (!this.getTouch()) { deviceType = "Desktop" } } } } return { type: deviceType, touch: this.getTouch(), width: window.screen.width, height: window.screen.height, availWidth: window.screen.availWidth, availHeight: window.screen.availHeight } }, canvas: function () { var canvasSupport = false; var canvas = document.createElement("canvas"); if (canvas && canvas.getContext && canvas.getContext("2d")) { canvasSupport = true } return canvasSupport }, vml: function () { if (this._vmlSupport == undefined) { var a = document.body.appendChild(document.createElement("div")); a.innerHTML = '<v:shape id="vml_flag1" adj="1" />'; var b = a.firstChild; b.style.behavior = "url(#default#VML)"; this._vmlSupport = b ? typeof b.adj == "object" : true; a.parentNode.removeChild(a) } return this._vmlSupport }, svg: function () { return document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Image", "1.1") }, getBrowser: function () { var ua = navigator.userAgent.toLowerCase(); var name = ""; var match = null; var that = this; browserNames = { msie: { name: "Internet Explorer", eval: /(msie) ([\w.]+)/.exec(ua) }, webkit: { name: "Webkit", eval: /(webkit)[ \/]([\w.]+)/.exec(ua) }, chrome: { name: "Chrome", eval: /(chrome)[ \/]([\w.]+)/.exec(ua) }, safari: { name: "Safari", eval: /(safari)[ \/]([\w.]+)/.exec(ua) }, opera: { name: "Opera", eval: /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) }, operamobile: { name: "Opera Mobile", eval: /(opera mobi)(?:.*version|)[ \/]([\w.]+)/.exec(ua) || /(opera tablet)(?:.*version|)[ \/]([\w.]+)/.exec(ua) }, dolphin: { name: "Dolphin", eval: /(dolphin)[ \/]([\w.]+)/.exec(ua) }, webosbrowser: { name: "webOSBrowser", eval: /(wosbrowser)(?:.*version|)[ \/]([\w.]+)/.exec(ua) }, chromemobile: { name: "Chrome Mobile", eval: /(crmo)[ \/]([\w.]+)/.exec(ua) }, silk: { name: "Silk", eval: /(silk)[ \/]([\w.]+)/.exec(ua) }, firefox: { name: "Firefox", eval: /(firefox)[ \/]([\w.]+)/.exec(ua) }, msie11: { name: "Internet Explorer 11", eval: ua.indexOf("rv:11.0") >= 0 && ua.indexOf(".net4.0c") >= 0 }, other: { name: "Other", eval: ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) } }; $.each(browserNames, function (index, value) { if (this.eval) { if (this.name == "Other") { if (!match) { match = this.eval; name = this.name } } else { if (this.name == "Internet Explorer 11") { if (!match) { match = ["", "msie", 11]; name = "Internet Explorer" } } else { if (name == "Chrome" && this.name == "Safari") { return true } match = this.eval; name = this.name } } } }); if (match) { var browser = { name: name, accessName: match[1] || "", version: match[2] || "0", canvas: this.canvas(), svg: this.svg(), vml: this.vml() }; browser[match[1]] = match[1] } else { browser = { name: "Other", browser: "other", version: "" } } return browser }, getOS: function () { var match = null; var version = ""; var userAgent = navigator.userAgent; var os = "Other"; var osTypes = { ios: { name: "iOS", regex: new RegExp("(?:i(?:Pad|Phone|Pod)(?:.*)CPU(?: iPhone)? OS )([^\\s;]+)") }, android: { name: "Android", regex: new RegExp("(?:(Android |HTC_|Silk/))([^\\s;]+)") }, webos: { name: "webOS", regex: new RegExp("(?:(?:webOS|hpwOS)/)([^\\s;]+)") }, blackberry: { name: "BlackBerry", regex: new RegExp("(?:BlackBerry(?:.*)Version/)([^\\s;]+)") }, rimTablet: { name: "RIMTablet", regex: new RegExp("(?:RIM Tablet OS )([^\\s;]+)") }, chrome: { name: "Chrome OS", regex: new RegExp("CrOS") }, mac: { name: "MacOS", regex: new RegExp("mac") }, win: { name: "Windows", regex: new RegExp("win") }, linux: { name: "Linux", regex: new RegExp("linux") }, bada: { name: "Bada", regex: new RegExp("(?:Bada/)([^\\s;]+)") }, other: { name: "Other" } }; var osys = ""; var clientStrings = [{ s: "Windows 3.11", r: /Win16/ }, { s: "Windows 95", r: /(Windows 95|Win95|Windows_95)/ }, { s: "Windows ME", r: /(Win 9x 4.90|Windows ME)/ }, { s: "Windows 98", r: /(Windows 98|Win98)/ }, { s: "Windows CE", r: /Windows CE/ }, { s: "Windows 2000", r: /(Windows NT 5.0|Windows 2000)/ }, { s: "Windows XP", r: /(Windows NT 5.1|Windows XP)/ }, { s: "Windows Server 2003", r: /Windows NT 5.2/ }, { s: "Windows Vista", r: /Windows NT 6.0/ }, { s: "Windows 7", r: /(Windows 7|Windows NT 6.1)/ }, { s: "Windows 8.1", r: /(Windows 8.1|Windows NT 6.3)/ }, { s: "Windows 8", r: /(Windows 8|Windows NT 6.2)/ }, { s: "Windows NT 4.0", r: /(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/ }, { s: "Windows ME", r: /Windows ME/ }, { s: "Android", r: /Android/ }, { s: "Open BSD", r: /OpenBSD/ }, { s: "Sun OS", r: /SunOS/ }, { s: "Linux", r: /(Linux|X11)/ }, { s: "BB10", r: /BB10/ }, { s: "MeeGo", r: /MeeGo/ }, { s: "iOS", r: /(iPhone|iPad|iPod)/ }, { s: "Mac OS X", r: /Mac OS X/ }, { s: "Mac OS", r: /(MacPPC|MacIntel|Mac_PowerPC|Macintosh)/ }, { s: "QNX", r: /QNX/ }, { s: "UNIX", r: /UNIX/ }, { s: "BeOS", r: /BeOS/ }, { s: "OS/2", r: /OS\/2/ }, { s: "Search Bot", r: /(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/ }]; for (var id in clientStrings) { var cs = clientStrings[id]; if (cs.r.test(userAgent)) { osys = cs.s; break } } var osVersion = ""; if (/Windows/.test(osys)) { osVersion = /Windows (.*)/.exec(osys)[1]; osys = "Windows" } if (/BB10/.test(osys)) { osVersion = "10"; osys = "BlackBerry" } switch (os) { case "Mac OS X": osVersion = /Mac OS X (10[\.\_\d]+)/.exec(userAgent)[1]; break; case "Android": osVersion = /Android ([\.\_\d]+)/.exec(userAgent)[1]; break; case "iOS": osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer); osVersion = osVersion[1] + "." + osVersion[2] + "." + (osVersion[3] | 0); break } if (osVersion != "") { version = osVersion } $.each(osTypes, function (index, value) { match = userAgent.match(this.regex) || userAgent.toLowerCase().match(this.regex); if (match) { if (!this.name.match(/Windows|Linux|MacOS/)) { if (match[1] && (match[1] == "HTC_" || match[1] == "Silk/")) { version = "2.3" } else { version = match[match.length - 1] } } os = { name: this.name, version: version, platform: navigator.platform }; return false } }); if (os && os.name == "Other") { os.name = osys } if (os && os.name != "" && osys != "") { os.name = osys } if (os && os.version == "" && osVersion != "") { os.version = osVersion } return os } } })(jQuery);
    $.ajax({
        async: false,
        url: "../../bottom.htm",
        success: function (data) {
            $("#pageBottom").append(data);
        }
    });
    $.ajax({
        async: false,
        url: "../../top.htm",
        success: function (data) {
            $("#pageTop").append(data);
        }
    });
    $.ajax({
        async: false,
        url: "../../navigation.htm",
        success: function (data) {
            $("#navigationTree")[0].innerHTML = "";
            $("#navigationTree").append(data);
        }
    });
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
    $(document).bind('contextmenu', function (e) { return false; });
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

        if ($target.text().indexOf('jqxDataAdapter') != -1)
            return;

        if ($target.text().indexOf('MVVM with Knockout') != -1)
            return;

        if ($target.text().indexOf('Roadmap') != -1)
            return;

        if ($target.text().indexOf('jqxResponse') != -1)
            return;

        if ($target.text().indexOf('Release History') != -1)
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
                    $oldtargetParentParent.find('.topicimage')[0].src = "../../images/topic.png";
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
                    $targetParentParent.find('.topicimage')[0].src = "../../images/topic_open.png";
                }
            }
            else children.css({ opacity: 1, visibility: 'visible' }).animate({ opacity: 0.0 }, 0, function () {
                children.css('display', 'none');
                $targetParentParent.removeClass('navigationItem-expanded');
                $targetParentParent.find('.navigationContent').css('display', 'none');
                $targetParentParent.find('.topicimage')[0].src = "../../images/topic.png";
            });
            var height = $(".navigation").height() - 10;
            $("#demoContainer").css('min-height', height + 'px');
        }
        return false;
    });
    var url = window.location;
    var urlItems = window.location.toString().split('/');
    var item = urlItems[urlItems.length - 1];

    var items = $('.navigationItem');
    $.each(items, function () {
        if (this.innerHTML.indexOf(item) >= 0) {
            $(this).addClass('item-expanded');
            return false;
        }
    });

    var $element = $($.find('.item-expanded'));
    var children = $element.find('.navigationItemContent');
    $.data(content, 'expandedElement', children);
    $.data(content, 'expandedTarget', $element.find('a')[0]);
    $element.removeClass('item-expanded');

    if (children.css('display') == 'none') {
        children.css({ opacity: 0, display: 'block', visibility: 'visible' }).animate({ opacity: 1.0 }, 0, function () {
            if ($.jqx.browser.msie)
                $(this).get(0).style.removeAttribute('filter');
        });

        $element.addClass('navigationItem-expanded');
        $element.find('.navigationContent').css('display', 'block');
        $element.find('.topicimage')[0].src = "../../images/topic_open.png";
    }

    var height = $(".navigation").height() - 10;
    $("#demoContainer").css('min-height', height + 'px');
}
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-25803467-1']);
_gaq.push(['_trackPageview']);

/*
(function () {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
*/

