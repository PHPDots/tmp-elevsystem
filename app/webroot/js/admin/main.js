$(function(){

    /// Flexslider function ///
//    $('.flexslider-nav').flexslider({
//        animation: "slide",
//        animationLoop: false,
//        itemWidth: 116,
//        itemMargin: 0,
//        directionNav: false,
//    });

    /// Fancy box function ///
    $('a[rel=fancybox]').fancybox({
        padding: 4
    });

    /// Animate progress bars in mainnavigation ///
    $('.diskspace .bar').animate({
        'width': '47%'
    }, 4000);

    $('.bandwidth .bar').animate({
        'width': '87%'
    }, 6000);

    $('.sql .bar').animate({
        'width': '27%'
    }, 2000);

    /// Dashboard actions hover animation using CSS3 ///
    $('ul.dashboard-actions li a').hover(function() {
        $(this).find('img').attr('class', '')
        $(this).find('img').addClass('animated bounceIn');
    }, function() {
        $(this).find('img').removeClass('animated bounceIn');
    });

    /// Popovers ///
    $('a[rel="popover"]').popover();
    $('a[rel="popover"]').click(function() {
        return false;
    });

    /// jQuery powertips ///
//    $('a[data-toggle=n-tooltip]').powerTip({
//        placement: 'n' // north-east tooltip position
//    });

//    $('a[data-toggle=e-tooltip]').powerTip({
//        placement: 'e' // north-east tooltip position
//    });
//
//    $('a[data-toggle=s-tooltip]').powerTip({
//        placement: 's' // north-east tooltip position
//    });
//
//    $('a[data-toggle=w-tooltip]').powerTip({
//        placement: 'w' // north-east tooltip position
//    });

    /// Bootstrap components loading button ///
    $('#fat-btn').click(function () {
        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
          btn.button('reset')
        }, 3000)
    });

    /// Bootstrap modals ///
    $(".myModal").click(function(){
        $("#myModal").modal();
        return false;
    });

    $(".myModal2").click(function(){
        $("#myModal2").modal({backdrop:false});
        return false;
    });

    $(".myModal3").click(function(){
        $("#myModal3").modal('toggle');
        return false;
    });

    $("#datePicker").datepicker({
        showOtherMonths:true,
        autoSize: true,
        appendText: '(dd-mm-yyyy)',
        dateFormat: 'dd-mm-yy'
    });

    $("#from, #to").datepicker();
     
    /// Changing window div containers sizes function ///
//    $(window).smartresize(function(){
//        windowHeight = $(window).height() - 171
//        $('.read-messages').css('height', windowHeight + 'px');
//        $('.list-messages .list').css('height', windowHeight + 'px');
//        $('.messages, messages-template').css('height', windowHeight + 1 + 'px');
//        $('.chat').css('height', windowHeight - 95 + 'px');
//        $('.message-navigation').css('height', windowHeight + 70 + 'px');
//        $('.chat-users > ul').css('height', windowHeight + 11 + 'px');
//        var fixedBottom = $('.messages_template .read-messages').width() - 20
//        $('.messages_template .attachments').css('width', fixedBottom + 'px');
//        chatInput = $(window).width() - 391
//        $('.type-message').css('width', chatInput + 'px')
//    });

    /// Document load div containers sizes function ///
    windowHeight = $(window).height() - 171
    $('.read-messages').css('height', windowHeight + 'px');
    $('.list-messages .list').css('height', windowHeight + 'px');
    $('.messages, messages-template').css('height', windowHeight + 1 + 'px');
    $('.chat').css('height', windowHeight - 95 + 'px');
    $('.message-navigation').css('height', windowHeight + 70 + 'px');
    $('.chat-users > ul').css('height', windowHeight  + 11 + 'px');
    var fixedBottom = $('.messages_template .read-messages').width() - 3
    $('.messages_template .attachments').css('width', fixedBottom + 'px');
    chatInput = $(window).width() - 391
    $('.type-message').css('width', chatInput + 'px')

    /// Vertical splitter ///
//    $(".messages").splitter({
//        minLeft: 170, 
//        sizeLeft: 750, 
//        minRight: 500
//    });

    /// Custom scroll bar plugin ///
//    $('ul.tasks').mCustomScrollbar();
//    $('.task-container a[data-toggle="tab"]').on('shown', function(e) {
//        var element = $(e.target).attr('href')
//        $(element).find('ul.tasks').mCustomScrollbar("update");
//    });

    // Custom scrollbar plugin
//    $(".list, .read-messages, .chatlist, .message-navigation, .chat, .chat-users > ul").mCustomScrollbar({
//        scrollButtons:{
//          enable:true
//        }
//    });

    /// Hide show sidebar with remember cookie ///
    $('.sidebar').click(function() {
        if(!$('body').hasClass('nosidebar')) {
            $.cookie('sidebars', 'hide', { path: '/', expires: 365 });
            $('body').addClass('nosidebar');
            $('.mainNavigation').removeAttr('style')
            $('.content').removeAttr('style')
        } else {
            $.removeCookie('sidebars', { path: '/', expires: 365 });
            $('body').removeClass('nosidebar');
            $('.mainNavigation').removeAttr('style')
            $('.content').removeAttr('style')
        }
        return false;
    });
 
//    if($.cookie('sidebars')) {
//        $('.sidebar').parent().addClass('active');
//        $('body').addClass('nosidebar');
//    } else {
//        $('.sidebar').parent().removeClass('active');
//        $('body').removeClass('nosidebar');
//    }

    $('ul.quick-actions > li > a').click(function() {
        var element = $(this).parents('li')
        if(element.hasClass('active')) {
            element.removeClass('active');
        } else {
            element.addClass('active');
        }       
    });

    /// Range datepicker ///
//    $('#reportrange').daterangepicker({
//        ranges: {
//            'Today': ['today', 'today'],
//            'Yesterday': ['yesterday', 'yesterday'],
//            'Last 7 Days': [Date.today().add({
//                days: -6
//            }), 'today'],
//            'Last 15 Days': [Date.today().add({
//                days: -14
//            }), 'today'],
//            'Last 30 Days': [Date.today().add({
//                days: -29
//            }), 'today'],
//            'This Month': [Date.today().moveToFirstDayOfMonth(), Date.today().moveToLastDayOfMonth()],
//            'Last Month': [Date.today().moveToFirstDayOfMonth().add({
//                months: -1
//            }), Date.today().moveToFirstDayOfMonth().add({
//                days: -1
//            })]
//        },
//        opens: 'left',
//        format: 'MM/dd/yyyy',
//        separator: ' to ',
//        startDate: Date.today().add({
//            days: -29
//        }),
//        endDate: Date.today(),
//        minDate: '01/01/2012',
//        maxDate: '12/31/2014',
//        locale: {
//            applyLabel: 'Submit',
//            fromLabel: 'From',
//            toLabel: 'To',
//            customRangeLabel: 'Custom Range',
//            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
//            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
//            firstDay: 1
//        },
//        showWeekNumbers: true,
//        buttonClasses: ['button-blue']
//    },

//    function (start, end) {
//        $('#reportrange span').html(start.toString('MMMM d, yyyy') + ' - ' + end.toString('MMMM d, yyyy'));
//    });

//    $('#reportrange span').html(Date.today().add({
//        days: -29
//    }).toString('MMMM d, yyyy') + ' - ' + Date.today().toString('MMMM d, yyyy'));

//    $('#reportrangechart').daterangepicker({
//        ranges: {
//            'Today': ['today', 'today'],
//            'Yesterday': ['yesterday', 'yesterday'],
//            'Last 7 Days': [Date.today().add({
//                days: -6
//            }), 'today'],
//            'Last 15 Days': [Date.today().add({
//                days: -14
//            }), 'today'],
//            'Last 30 Days': [Date.today().add({
//                days: -29
//            }), 'today'],
//            'This Month': [Date.today().moveToFirstDayOfMonth(), Date.today().moveToLastDayOfMonth()],
//            'Last Month': [Date.today().moveToFirstDayOfMonth().add({
//                months: -1
//            }), Date.today().moveToFirstDayOfMonth().add({
//                days: -1
//            })]
//        },
//        opens: 'left',
//        format: 'MM/dd/yyyy',
//        separator: ' to ',
//        startDate: Date.today().add({
//            days: -29
//        }),
//        endDate: Date.today(),
//        minDate: '01/01/2012',
//        maxDate: '12/31/2014',
//        locale: {
//            applyLabel: 'Submit',
//            fromLabel: 'From',
//            toLabel: 'To',
//            customRangeLabel: 'Custom Range',
//            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
//            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
//            firstDay: 1
//        },
//        showWeekNumbers: true,
//        buttonClasses: ['button-blue']
//    },

//    function (start, end) {
//        $('#reportrangechart span').html(start.toString('MMMM d, yyyy') + ' - ' + end.toString('MMMM d, yyyy'));
//    });

//    $('#reportrangechart span').html(Date.today().add({
//        days: -29
//    }).toString('MMMM d, yyyy') + ' - ' + Date.today().toString('MMMM d, yyyy'));

    /// Masonary plugin for responsive gallery ///
//    var $container = $('#gallery-container');
//    var gutter_width = 6;
//    var min_width = 100;

//    var imgs = $('.gallery-item img');
//
//    imgs.each(function () {
//        var img = $(this);
//        if(img[0].complete || ($.browser.msie && parseInt($.browser.version) == 6)){
//          img.trigger("load");
//        }
//    }); 
//    imgs.imagesLoaded(function (){
//        $container.masonry({
//          itemSelector : '.gallery-item',
//          gutterWidth: gutter_width,
//          isAnimated: true,
//          columnWidth: function( containerWidth ) {
//            var items = (containerWidth/min_width | 0);
//            var col_width = (((containerWidth - (items-1)*gutter_width)/items) | 0) ;
//            if (containerWidth < min_width) {
//              col_width = containerWidth;
//            }
//            $('.gallery-item').width(col_width);
//            return col_width;
//          }
//        });
//    });

    /// On page load animate login page containers ///
    function fadeloginv2() {
      var delay = 4;
      $('.login-containerv2 .forgot').addClass('animate'+delay+' bounceInRight');
    }

    function fadeavatarv2() {
      $('.login-containerv2 .loginform').addClass('animated fadeInDownBig');
    }
     
    function fadefooterv2() {
      var delay = 4;
      $('.login-containerv2 .nouser').addClass('animate'+delay+' bounceInLeft');
    }
     
    fadeloginv2();
    fadeavatarv2();
    fadefooterv2();

    // v1 login menu
    function fadelogin() {
      $('.login-container .login').addClass('animated fadeInDownBig');
    }

    function fadeavatar() {
    var delay = 4;
      $('.login-container .avatar').addClass('animate'+delay+' bounceInDown');
    }
     
    function fadefooter() {
      $('.login-container .login-footer, .login-container > span').addClass('animated fadeInLeftBig');
    }
     
    fadelogin();
    fadeavatar();
    fadefooter();

    var delay = 5;   
    $('.profile > a > img').addClass('animate'+delay+' fadeInDownBig');

    var delayHeader = 5;   
    $('ul.header-actions > li > a').each(function(){                                        
        $(this).addClass('animate'+delayHeader+' fadeInRightBig');
        delayHeader++;
    });

    $('.loginbutton').click(function() {
        var inputuser = $(this).parents('form').find('input.login-input');
        var inputpass = $(this).parents('form').find('input.password-input');
        if(inputuser.val().length == 0 || inputpass.val().length == 0) {
            if(inputuser.val() == '') inputuser.addClass('error'); else inputuser.removeClass('error');
            if(inputpass.val() == '') inputpass.addClass('error'); else inputpass.removeClass('error');
            var anievent = (jQuery.browser.webkit)? 'webkitAnimationEnd' : 'animationend';
            $('.login').attr('class', 'login');
            $('.login').addClass('animated shake').bind(anievent,function(){
                $(this).removeClass('animated shake');
            });
            $('.error-login').fadeIn();
            return false;
        } else {
            $('.error-login').fadeOut();
            return true;
        }
    });

    $('.loginbuttonv2').click(function() {
        var input = $(this).parents('.loginform').find('input');
        if(input.val().length == 0) {
            if(input.val() == '') input.addClass('error'); else input.removeClass('error');
            var anievent = (jQuery.browser.webkit)? 'webkitAnimationEnd' : 'animationend';
            $('.loginform').attr('class', 'loginform');
            $('.loginform').addClass('animated shake').bind(anievent,function(){
                $(this).removeClass('animated shake');
            });
            return false;
        } else {
            return true;
        }
    });

    // Shaking messages number function
    function hi(){
        $('span.message').addClass('animated wobble');
        setTimeout(
        function() 
        {
            $('span.message').removeClass('animated wobble');
        }, 1000);
    }

    // set an interval to function
    var interval = setInterval(hi, 1200);
    $('ul.header-actions > li > a').click(function() {
        clearInterval(interval);
        $('span.message').addClass('animated fadeOutUp');
    });

    var submenu = $('.innerNavigation > ul > li.active').children('a').attr('href');
    $(submenu).css('display', 'block');

    // Inbox star and pin click function
    $("a.stars").click(function() {
        var element = $(this);
        if(element.hasClass('starred')) {
            $(this).removeClass('starred');
        } else {
            $(this).addClass('starred');
        }
        return false;
    });

    $("a.pin").click(function() {
        var element = $(this);
        if(element.hasClass('unpinned')) {
            $(this).removeClass('unpinned');
        } else {
            $(this).addClass('unpinned');
        }
        return false;
    });

    // Hide show body in inbox_template vs
    $("a.arrow").click(function() {
        var element = $(this);
        var heightInbox = $(this).parents('.recieved-item').prop('scrollHeight') - '45'
        if(element.hasClass('down')) {
            $(this).removeClass('down');
            $(this).addClass('up');
            $(this).parents('.recieved-item').animate({
                height: "-=" + heightInbox + 'px'
            }, function() {
                $(this).parents('.list').mCustomScrollbar("update");
            });
        } else {
            $(this).removeClass('up');
            $(this).addClass('down');
            $(this).parents('.recieved-item').animate({
                height: "+=" + heightInbox + 'px'
            }, function() {
                $(this).parents('.list').mCustomScrollbar("update");
            });
        }
        return false;
    });

    // Menu and submenus function
    var submenu = $('ul.mainNav > li').children('ul');
    if (submenu.length > 0) {
        submenu.parent('li').children('a').append('<span class="submenu-arrow"></span>');
    }

	$('ul.mainNav > li > a').click(function() {
        $('ul.mainNav > li > ul').fadeOut(200);
        submenu = $(this).parent().children('ul');
        if(submenu.length > 0) {
            if (submenu.is(":hidden")) {
                submenu.fadeIn(200);
            } else {
                submenu.fadeOut(200);
            }
            return false;
        }
	});

    $('ul.mainNav > li > a').click(function() {
        if($(this).parent().find('ul').length > 0) {
            return false
        }
    });

    $(document).bind('click', function(e) {
        var $clicked = $(e.target);
        if (! $clicked.parents().hasClass("dropdown"))
        $("ul.mainNav > li > ul").fadeOut(200);
    });

    $('ul.mainNav > li > ul > li > a').click(function() {
        submenu = $(this).parent().children('ul');
        if(submenu.length > 0) {
            if (submenu.is(":hidden")) {
                submenu.slideDown(200);
                $(this).find('.subchild-arrow').addClass('opened');
            } else {
                submenu.slideUp(200);
                $(this).find('.subchild-arrow').removeClass('opened');
            }
            return false;
        }
    });

    // Sparklines
//	$("#sales").sparkline([5,6,7,2,1,5,6,5,3,6,2,5,6,4], {
//	    type: 'bar',
//	    height: '18px',
//	    barColor: '#ef5f3c',
//	    barWidth: 3
//	});
//
//    $("#sales2").sparkline([5,6,7,2,6,5,3,6,1,5,2,5,6,4], {
//        type: 'bar',
//        height: '18px',
//        barColor: '#56acab',
//        barWidth: 3
//    });
//
//    $("#sales3").sparkline([5,5,6,5,3,6,6,7,2,1,2,5,6,4], {
//        type: 'bar',
//        height: '18px',
//        barColor: '#df4644',
//        barWidth: 3
//    });
//
//    $("#sales4").sparkline([5,5,6,5,3,6,6,7,2,1,2,5,6,4], {
//        type: 'bar',
//        height: '18px',
//        barColor: '#0099cc',
//        barWidth: 3
//    });
//
//    $("#sales5").sparkline([5,5,6,5,3,6,6,7,2,1,6,6,7,2,1,2,5,6,4], {
//        type: 'bar',
//        height: '28px',
//        barColor: 'rgba(255, 255, 255, 0.75)',
//        barWidth: 3
//    });
//
//    $("#sales-current").sparkline([5,5,6,5,3,6,6,7,2,1,6,6,7,2,1,2,5,6,4], {
//        type: 'bar',
//        height: '28px',
//        barColor: '#ef5f3c',
//        barWidth: 3
//    });
//
//    $("#sales-current2").sparkline([5,5,6,5,3,6,6,7,2,1,6,6,7,2,1,2,5,6,4], {
//        type: 'bar',
//        height: '28px',
//        barColor: '#56acab',
//        barWidth: 3
//    });

    // Header actions drop down function
	$('ul.header-actions > li > a').click(function() {
		$('ul.header-actions > li > .dropdown').fadeOut(150);
		$('ul.header-actions > li').removeClass('active');
		var dropdown = $(this).parent().find('.dropdown');
		if(dropdown.is(":hidden")) {
			dropdown.fadeIn(150);
			dropdown.parent().addClass('active');
		} else {
			dropdown.fadeOut(150);
			dropdown.parent().removeClass('active');
		}
		return false;
	});

	$('ul.header-actions > li').click(function(event){
    	event.stopPropagation();
 	});

	$('html').click(function(event){
    	$('ul.header-actions > li > .dropdown').fadeOut(150);
		$('ul.header-actions > li').removeClass('active');
 	});

    // Filetree plugin
// 	$('#file-tree').fileTree({
//        root: '/infinite/filetree/',
//        script: 'php/jqueryFileTree.php',
//        expandSpeed: 200,
//        collapseSpeed: 200,
//        multiFolder: true,
//    }, function(file) {
//            alert(file);
//    });

    // Jquery easypie chart plugin
//    $('.chart1').easyPieChart({
//        size: 70,
//        lineCap: 'square',
//        lineWidth: 5,
//        trackColor: 'rgba(255, 255, 255, 0.2)',
//        barColor: '#f8f8f8',
//        animate: 1000
//    });
//
//    $('.chart2').easyPieChart({
//        size: 70,
//        lineCap: 'square',
//        lineWidth: 5,
//        scaleColor: '#b2b1b1',
//        trackColor: '#d0d0d0',
//        barColor: '#0099cc',
//        animate: 1000
//    });
//
//    $('.chart3').easyPieChart({
//        size: 70,
//        lineCap: 'square',
//        lineWidth: 5,
//        scaleColor: '#b2b1b1',
//        trackColor: '#d0d0d0',
//        barColor: '#ef5f3c',
//        animate: 1000
//    });
//
//    $('.chart4').easyPieChart({
//        size: 70,
//        lineCap: 'square',
//        lineWidth: 5,
//        scaleColor: '#b2b1b1',
//        trackColor: '#d0d0d0',
//        barColor: '#56acab',
//        animate: 1000
//    });
//
//    $('.chart5').easyPieChart({
//        size: 70,
//        lineCap: 'square',
//        lineWidth: 5,
//        scaleColor: '#b2b1b1',
//        trackColor: '#d0d0d0',
//        barColor: '#df4644',
//        animate: 1000
//    });
//
//    $('.chart6').easyPieChart({
//        size: 70,
//        lineCap: 'square',
//        lineWidth: 5,
//        scaleColor: '#b2b1b1',
//        trackColor: '#d0d0d0',
//        barColor: '#ef5f3c',
//        animate: 1000
//    });
//
//    $('.chart7').easyPieChart({
//        size: 60,
//        lineCap: 'square',
//        lineWidth: 2,
//        scaleColor: '#a8a8a8',
//        trackColor: '#202020',
//        barColor: '#a8a8a8',
//        animate: 1000
//    });
//
//    $('.chart8').easyPieChart({
//        size: 60,
//        lineCap: 'square',
//        lineWidth: 2,
//        scaleColor: '#a8a8a8',
//        trackColor: '#202020',
//        barColor: '#a8a8a8',
//        animate: 1000
//    });

    // Dashboard statistic random sparklines
//    randNum = function(){
//        //return Math.floor(Math.random()*101);
//        return (Math.floor( Math.random()* (1+40-20) ) ) + 20;
//    }
//
//    i=1;
//    for (i=1; i<6; i++) {
//        var data = [[1, 3+randNum()], [2, 5+randNum()], [3, 8+randNum()], [4, 11+randNum()],[5, 14+randNum()],[6, 17+randNum()],[7, 20+randNum()], [8, 15+randNum()], [9, 18+randNum()], [10, 22+randNum()]];
//        placeholder = '.sparkLine' + i;
//        $(placeholder).sparkline(data, { 
//            width: 100,//Width of the chart - Defaults to 'auto' - May be any valid css width - 1.5em, 20px, etc (using a number without a unit specifier won't do what you want) - This option does nothing for bar and tristate chars (see barWidth)
//            height: 30,//Height of the chart - Defaults to 'auto' (line height of the containing tag)
//            lineColor: '#56acab',//Used by line and discrete charts to specify the colour of the line drawn as a CSS values string
//            fillColor: false,//Specify the colour used to fill the area under the graph as a CSS value. Set to false to disable fill
//            spotColor: '#467e8c',//The CSS colour of the final value marker. Set to false or an empty string to hide it
//            maxSpotColor: '#9FC569',//The CSS colour of the marker displayed for the maximum value. Set to false or an empty string to hide it
//            minSpotColor: '#ED7A53',//The CSS colour of the marker displayed for the mimum value. Set to false or an empty string to hide it
//            spotRadius: 3,//Radius of all spot markers, In pixels (default: 1.5) - Integer
//            lineWidth: 2,//In pixels (default: 1) - Integer
//            highlightSpotColor: '#df4644'
//        });
//    }
//
//    $('.sparkline7').sparkline(data, { 
//            width: 100,//Width of the chart - Defaults to 'auto' - May be any valid css width - 1.5em, 20px, etc (using a number without a unit specifier won't do what you want) - This option does nothing for bar and tristate chars (see barWidth)
//            height: 30,//Height of the chart - Defaults to 'auto' (line height of the containing tag)
//            lineColor: '#fff',//Used by line and discrete charts to specify the colour of the line drawn as a CSS values string
//            fillColor: false,//Specify the colour used to fill the area under the graph as a CSS value. Set to false to disable fill
//            spotColor: '#fff',//The CSS colour of the final value marker. Set to false or an empty string to hide it
//            maxSpotColor: '#fff',//The CSS colour of the marker displayed for the maximum value. Set to false or an empty string to hide it
//            minSpotColor: '#fff',//The CSS colour of the marker displayed for the mimum value. Set to false or an empty string to hide it
//            spotRadius: 3,//Radius of all spot markers, In pixels (default: 1.5) - Integer
//            lineWidth: 2,//In pixels (default: 1) - Integer
//            highlightSpotColor: '#fff'
//        });


    /* Mobile devices navigation function */
    $('#mobileNav').click(function() {
        mainNavigation = $('.mainNavigation');
        if($('body').hasClass('nosidebar')) {
            if (mainNavigation.css('left') == '-151px') {
                $('.mainNavigation').animate({
                    left: '0px'
                });
                $('.content').animate({
                    'margin-left': '151px'
                });
            }
            else {
                mainNavigation.animate({
                    left: '-151px'
                });
                $('.content').animate({
                    'margin-left': '0px'
                });
            }
        } else {
            if (mainNavigation.css('left') == '-381px') {
                $('.mainNavigation').animate({
                    left: '0px'
                });
                $('.content').animate({
                    'margin-left': '380px'
                });
            }
            else {
                mainNavigation.animate({
                    left: '-381px'
                });
                $('.content').animate({
                    'margin-left': '0px'
                });
            }
        }
    });

    /* Phone menu and submenu function */
    $('#phoneNav').click(function() {
        phoneNavigation = $('.phone-menu');
        if(phoneNavigation.is(":hidden")) {
            phoneNavigation.slideDown(200);
        } else {
            phoneNavigation.slideUp(200);
        }
    });

    $('.phone-menu > ul > li > a').click(function() {
        $('.phone-menu > ul > li > ul').slideUp(200);
        $('.phone-menu > ul > li > a .subchild-arrow').removeClass('opened');
        submenu = $(this).parent().children('ul');
        if(submenu.length > 0) {
            if(submenu.is(":hidden")) {
                $(this).parent().addClass('active');
                submenu.slideDown(200);
                $(this).find('.subchild-arrow').addClass('opened');
            } else {
                $(this).parent().removeClass('active');
                submenu.slideUp(200);
                $(this).find('.subchild-arrow').removeClass('opened');
            }
            return false;
        } else {
            return true;
        }
    });

    // Carousel from bootstrap
    $('#quick-report').carousel({
        interval: false
    });

    $('#charts-slide').carousel({
        interval: false
    });

    // Cleditor
    $("#cleditor").cleditor({
        width: '100%'
    });

    // Tags input
//    $('#tags').tagsInput({
//        'interactive': true
//    });

    // Change search page list/grid view
    $('.list-view').click(function() {
        $('.results').addClass('list');
        $(this).addClass('active');
        $('.grid-view').removeClass('active');
        return false;
    });

    $('.grid-view').click(function() {
        $('.results').removeClass('list');
        $(this).addClass('active');
        $('.list-view').removeClass('active');
        return false;
    });

});