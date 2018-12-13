$(function(){

    /*========= ibuttons =========*/
//    $(".switch input").iButton({
//        resizeHandle: "auto",
//        resizeContainer: "auto"
//    }); 

//    $(".switch_text input").iButton({
//        resizeHandle: "auto",
//        resizeContainer: "auto",
//        labelOn: "Long text",
//        labelOff: "Long text"
//    }); 

//    $(".chat-status input").iButton({
//        resizeHandle: "auto",
//        resizeContainer: "auto",
//
//    }); 

    $('form').find('input[type="checkbox"], input[type="radio"], select.uniform, input[type="file"]').uniform();

//    $(".star").rating({required: false});

	/*========= chosen plugin =========*/
	$('.chosen').chosen();

	/*========= spinner plugin =========*/
	$("#spinner").spinner();

	$("#spinnerCurrency").spinner({ 
		culture: "en-US", 
		min: 2, 
		max: 2500, step: 1, 
		numberFormat: "C" 
	});

	$("#spinnerDecimal").spinner({
	    step: 0.01,
	    numberFormat: "n"
	});

	$("#spinnerCurrencyDE").spinner({
	    culture: "de-DE", 
		min: 5, 
		max: 2500, step: 1, 
		numberFormat: "C" 
	});

	$("#spinnerCurrencyDE2").spinner({
	    culture: "de-DE", 
		min: 5, 
		max: 2500, step: 1, 
		numberFormat: "C" 
	});

	$("#spinnerin01").spinner();
	$("#spinnerin02").spinner();
	$("#spinnerin03").spinner();
	$("#spinnerin04").spinner();

	/*========= masked inputs plugin =========*/
//	$("#dateinput").mask("99/99/9999");
//	$("#phone").mask("(999)99-999-999");
//	$("#ssn").mask("999-99-9999");
//	$("#tin").mask("99-9999999");

    /*========= Bootstrap wizard =========*/
//    $('#rootwizard').bootstrapWizard({onTabShow: function(tab, navigation, index) {
//        var $total = navigation.find('li').length;
//        var $current = index+1;
//        var $percent = ($current/$total) * 100;
//        $('#rootwizard').find('.bar').css({width:$percent+'%'});
//
//        if($current >= $total) {
//            $('#rootwizard').find('.wizard .next').hide();
//            $('#rootwizard').find('.wizard .finish').show();
//            $('#rootwizard').find('.wizard .finish').removeClass('disabled');
//        } else {
//            $('#rootwizard').find('.wizard .next').show();
//            $('#rootwizard').find('.wizard .finish').hide();
//        }
//    }});

//    $('#rootwizard2').bootstrapWizard({onTabShow: function(tab, navigation, index) {
//        var $total = navigation.find('li').length;
//        var $current = index+1;
//        var $percent = ($current/$total) * 100;
//        $('#rootwizard2').find('.bar').css({width:$percent+'%'});
//
//        if($current >= $total) {
//            $('#rootwizard2').find('.wizard .next').hide();
//            $('#rootwizard2').find('.wizard .finish').show();
//            $('#rootwizard2').find('.wizard .finish').removeClass('disabled');
//        } else {
//            $('#rootwizard2').find('.wizard .next').show();
//            $('#rootwizard2').find('.wizard .finish').hide();
//        }
//    }});

//    $('#rootwizard3').bootstrapWizard({onTabShow: function(tab, navigation, index) {
//        var $total = navigation.find('li').length;
//        var $current = index+1;
//        var $percent = ($current/$total) * 100;
//        $('#rootwizard3').find('.bar').css({width:$percent+'%'});
//
//        if($current >= $total) {
//            $('#rootwizard3').find('.wizard .next').hide();
//            $('#rootwizard3').find('.wizard .finish').show();
//            $('#rootwizard3').find('.wizard .finish').removeClass('disabled');
//        } else {
//            $('#rootwizard3').find('.wizard .next').show();
//            $('#rootwizard3').find('.wizard .finish').hide();
//        }
//    }});

    /*========= check all checkboxes function =========*/
    $('.checkall').click(function(){
        var table = $(this).parents('table');
        var checkbox = table.find('input[type=checkbox]');
            
        // Check is checkall button checked 
        if($(this).is(':checked')) {
            checkbox.each(function(){ // then on all other checks do following
                $(this).attr('checked',true); // check all other check buttons
                $(this).parent().addClass('checked'); // add class checked to them so that uniform can be applied properly
                $(this).parents('tr').addClass('active');
            }); 
        } else { // If checkall button it not checked
            checkbox.each(function(){  // then on all other checks do following
                $(this).attr('checked',false);  // uncheck all other check buttons
                $(this).parent().removeClass('checked'); // remove class checked to them so that uniform can be applied properly
                $(this).parents('tr').removeClass('active');
            }); 
        }
    });

    $('input:checkbox').click(function(){
        if($(this).is(":checked")) {
            $(this).parents('tr').addClass('active');
        } else {
            $(this).parents('tr').removeClass('active');
        }
    });

    /*========= form validate =========*/
    $("#validate").validate({
        rules: {
            name: "required",
            lastname: "required",
            username: "required",
            email: {
                required: true,
                email: true
            },
            url: {
                required: true,
                url: true
            },
            date: {
                required: true,
                date: true
            },
            numbers: {
                required: true,
                digits: true
            },
            password: {
                required: true,
                minlength: 5
            },
            confirm_password: {
                required: true,
                minlength: 5,
                equalTo: "#pass_con"
            },
            message: "required"
        }
    });

    /*========= pass strength =========*/
//    $(".password_test").passStrength({
//        userid: "#username"
//    });

    /*========= ui sliders =========*/
    $(".ui_slider1").slider({
        range: "min",
        value: 120,
        min: 80,
        max: 700,
        slide: function( event, ui ) {
            $( ".ui_slider1_val" ).html( "$" + ui.value );
        }
    });

    $(".ui_slider2").slider({
        range: true,
        min: 0,
        step: 20,
        max: 500,
        values: [ 75, 300 ],
        slide: function( event, ui ) {
            $( ".ui_slider2_val" ).text( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
            $( "#ui_slider_min_val" ).val( "$" + ui.values[ 0 ] );
            $( "#ui_slider_max_val" ).val( "$" + ui.values[ 1 ] );
        }
    });

    $(".ui_slider3").slider({
        range: "min",
        value: 120,
        min: 80,
        max: 700,
        slide: function( event, ui ) {
            $( ".ui_slider1_val" ).html( "$" + ui.value );
        }
    });

    $(".ui_slider4").slider({
        range: "min",
        value: 120,
        min: 80,
        max: 700,
        slide: function( event, ui ) {
            $( ".ui_slider1_val" ).html( "$" + ui.value );
        }
    });

    $(".ui_slider5_ver").slider({
        orientation: "vertical",
        range: "min",
        min: 0,
        max: 100,
        value: 60,
    });

    $(".ui_slider6_ver").slider({
        orientation: "vertical",
        range: "min",
        min: 0,
        max: 100,
        value: 50,
    });

    $(".ui_slider7_ver").slider({
        orientation: "vertical",
        range: "min",
        min: 0,
        max: 100,
        value: 90,
    });

    $(".ui_slider8_ver").slider({
        orientation: "vertical",
        range: "min",
        min: 0,
        max: 100,
        value: 30,
    });

    $(".ui_slider9_ver").slider({
        orientation: "vertical",
        range: "min",
        min: 0,
        max: 100,
        value: 70,
    });

    /*========= ui progress bars =========*/
    $( ".progressred" ).progressbar({
      value: 27
    });

    $( ".progressblue" ).progressbar({
      value: 37
    });

    $( ".progressgreen" ).progressbar({
      value: 47
    });

    $( ".progressorange" ).progressbar({
      value: 57
    });

    $( ".progressturquoise" ).progressbar({
      value: 67
    });

    $( ".progressyellow" ).progressbar({
      value: 87
    });

    $(".progressAnimate").progressbar({
        value: 1,
        create: function() {
            $(".progressAnimate .ui-progressbar-value").animate({"width":"100%"},{
                duration: 10000,
                step: function(now){
                    $(".proValue").html(parseInt(now)+"%");
                },
                easing: "linear"
            })
        }
    });
    
    $(".UprogressAnimate").progressbar({
        value: 1,
        create: function() {
                $(".UprogressAnimate .ui-progressbar-value").animate({"width":"100%"},{
                    duration: 30000,
                    easing: 'linear',
                    step: function(now){
                        $(".UproValue").html("Uploading: <span>"+parseInt(now*10.24)+" Mb</span> / 1024 Mb");
                    },
                    complete: function(){
                        $(".UproValue").html("<span class='must'>Upload Finished</span>");
                    } 
                })
            }
    });
});