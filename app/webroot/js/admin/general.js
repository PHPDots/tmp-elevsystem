
function addRemove(objectelement){
    jQuery(objectelement).parents('.custom-rows').first().find('.removebtn').show();
    jQuery(objectelement).parents('.custom-rows').first().find('.removebtn').last().hide();
    jQuery(objectelement).parents('.custom-rows').first().find('.addbtn').hide();
    jQuery(objectelement).parents('.custom-rows').first().find('.addbtn').last().show();
}

var ossselectedids;

var da  = [];

da = {
    dayNamesShort:["Søn","Man","Tir","Ons","Tor","Fre","Lør"],
    dayNamesMin:["Sø","Ma","Ti","On","To","Fr","Lø"],
    monthNames:["Januar","Februar","Marts","April","Maj","Juni","Juli","August","September","Oktober","November","December"],
    monthNamesShort:["Jan","Feb","Mar","Apr","Maj","Jun","Jul","Aug","Sep","Okt","Nov","Dec"]        
};

jQuery(document).ready(function(){
    
    jQuery('.fly_loading').hide();
    
 /* 
  *  This is general javascript file for dropdown menu in paginate pages
  *  
  */
   
   jQuery("#dropdown").change(function(){    
        jQuery(this).parents('form').submit();    
    });
    
/* 
 *  This is general javascript for add and remove contactfields
 *  
 */
    jQuery(document).delegate('.custom-rows .addbtn','click',function(){ 
        index = jQuery(this).parents('.custom-rows').find('.custom-row').length;        
        jQuery(this).parents('.custom-rows').append(jQuery('#'+jQuery(this).attr('container')).html().replace(/\%i\%/g,index));
        addRemove(this);
    });

    jQuery(document).delegate('.custom-rows .removebtn','click',function(){
        jQuery(this).parent().hide().find('.status').val('deactive');            
        addRemove(this);

    });  

    jQuery.each(jQuery('.custom-rows'),function(index, value){
        addRemove(jQuery(this).find('.custom-row').last());
    });
        
        
   jQuery('.province').hide();     
   jQuery('.uniform-province').change(function(){            
            if(jQuery(this).val() == 'province'){
                jQuery('.province').slideDown();               
            }else{
                 jQuery('.province').slideUp();                     
            }
   }).trigger('change'); 

/* 
 *  This is general javascript for slide widget
 *  
 */

    jQuery(document).delegate('.deleteElement','click',function(){
        return confirm("Are you sure?");
    });
        
    
    jQuery(document).delegate('.actionButton','click',function(){
            element = '#'+jQuery(this).attr('element')+'-ActionCt';            
            jQuery(element).slideToggle();
    });
    
   
    jQuery('body').delegate('.widget-header','click',function(){
        jQuery(this).parent().find('.slide').first().slideToggle('slow');
    });

/* 
 *  This is general javascript for date picker
 *  
 */

    jQuery('.datetimepicker').datetimepicker({
        format:'d.m.Y H:i'      
    });
    
    jQuery('body').delegate(".datepick",'focusin',function(){
         var maxDate;
         
         jQuery(this).datepicker({
            changeYear  : true,
            changeMonth : true,
            dateFormat  : 'dd/mm/yy',
            yearRange   : "-"+parseInt(jQuery(this).attr('minYear'))+":+"+parseInt(jQuery(this).attr('maxYear'))+"'"
        });
        
        if(jQuery(this).attr('maxDate') != '') {
            jQuery(this).datepicker( "option", "maxDate", jQuery(this).attr('maxDate'));
        }
    });
    
    jQuery('.datepicker').datepicker({
        format          : 'dd.mm.yyyy',
        autoclose       : true,
        orientation     : 'auto bottom'
    }).on('show',function(e) {
//        var minYear = jQuery(this).attr('minyear');
//        var maxYear = jQuery(this).attr('maxyear');
//        jQuery(this).datepicker('setEndDate', '+20y');
//        if(minYear != 0) {
//            jQuery(this).datepicker('setStartDate', '-'+minYear+'d');
//        }
//        if(maxYear != 0) {
//            jQuery(this).datepicker('setEndDate', '+'+maxYear+'d"');
//        }
    });
    
    jQuery(document).delegate('.custom-rows .removebtn','click',function(){
        jQuery(this).parent().remove();
        return false;
    });  
    
    jQuery(document).delegate('#addSignature','click',function(){       
        index = jQuery('#sectionH-signature').find('.widget').length;       
        jQuery(this).parents('#SectionH').find('#sectionH-signature').append(jQuery('#section-H-signature').html().replace(/\%i\%/g,index));
    })
    
    jQuery(document).delegate('#addNewMember','click',function(){       
        index = jQuery('#newMemberCt').find('.widget').length + 1;       
        jQuery(this).parent().find('#newMemberCt').append(jQuery('#newMemeberForm').html().replace(/\%i\%/g,index));
    })
            
    jQuery('.error-message').hide();
    
    jQuery(document).delegate('#formSubmit','click',function(){
        
        form    = jQuery(this).parents('form').attr('id');
        href    = jQuery(this).parents('form').attr('action');
        
        jQuery.ajax({
            url     : href,
            data    : jQuery('#'+form).serialize(),
            type    : 'POST',
            dataType: 'html',
            beforeSend: function(data) {
                jQuery('#formControlls').hide();
                jQuery('#submitForm').show();
            },
            success  : function(data) {
                jQuery('#exc').html(data);
            },
            complete: function(data) {
                jQuery('#submitForm').hide();
                jQuery('#formControlls').show();
            },
            error   : function() {
                alert('error');
            }
        });
    });
    
    /*
     *  Java script for fancybox
    */
    
    jQuery(".media_uploader").fancybox({
        'width'				: '75%',
        'height'			: '75%',
        'autoScale'                     : false,
        'transitionIn'                  : 'none',
        'transitionOut'                 : 'none',
        'type'				: 'iframe',
        'beforeClose'                   : function () {
            ossselectedids = Array();
            var serial      = jQuery(".fancybox-iframe").contents().find("input[name*='file_uploads']")
            if(serial.length > 0) {
                if(serial.length==1) {
                    ossselectedids.push(serial.val());
                } else {
                    jQuery.each(serial,function(){
                        ossselectedids.push(jQuery(this).val());
                    });
                }
            }
        }
    });
    
    jQuery('.chosen-select1').chosen({
        max_selected_options: 1
    });
    
    jQuery('.tableLink').click(function(){
        window.location = jQuery(this).attr('link');
    });
    
    jQuery('.reportDialogs').hide();
        
    jQuery('.reports').on("click",function() {
        jQuery('.reportDialogs').parents('.ui-dialog').hide();
        var name = jQuery(this).attr('reportType');
        jQuery('#'+name).dialog({
            autoOpen: true,
            modal: true,
            width : '45%'
        });
        jQuery('#'+name).parents('.ui-dialog').show();
        jQuery('#'+name).dialog("open");
    });
    
    jQuery('#submitAnnouncement').click(function(){
        jQuery('#weeklyAnnouncements').parents('.ui-dialog').hide();
    });
    
    jQuery('#phoneNav').click(function() {
        if(jQuery('.mainNavigation').hasClass('close-menu')) {
            jQuery('.mainNavigation').show("slide", { direction: "left" }, 1000).removeClass('close-menu').addClass('open-menu');
        } else {
            jQuery('.mainNavigation').hide("slide", { direction: "left" }, 1000).removeClass('open-menu').addClass('close-menu'); 
        }        
    });
    
    jQuery('#dataTable').DataTable(); 
    
    /** BEGIN CHOSEN JS **/
        //    jQuery(function () {
            //    "use strict";
            //    var configChosen = {
            //    '.chosen-select' : {},
            //    '.chosen-select-deselect' : {allow_single_deselect:true},
            //    '.chosen-select-no-single' : {disable_search_threshold:10},
            //    '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
            //    '.chosen-select-width' : {width:"100%"}
            //    }
            //    for (var selector in configChosen) {
            //    jQuery(selector).chosen(configChosen[selector]);
            //    }
        //    });
    /** END CHOSEN JS **/

    /** SESSION TIMEOUT **/
        SessionTimeout.init();
    /** END **/
});


/* SESSION TIME OUT */
    var get_over_all_data;
    var IDLE_TIMEOUT            = 30; //seconds
    var _idleSecondsTimer       = null;
    var _idleSecondsCounter     = 0;
    var auto_logout_time        = 900000    ;

    document.onclick = function() {
        _idleSecondsCounter = 0;
    };

    document.onmousemove = function() {
        _idleSecondsCounter = 0;
    };

    document.onkeypress = function() {
        _idleSecondsCounter = 0;
    };

    function CheckIdleTime() {
        _idleSecondsCounter++;

        if (_idleSecondsCounter >= IDLE_TIMEOUT) {
            window.clearInterval(get_over_all_data);
        }
    }

    var SessionTimeout = function () {

        var handlesessionTimeout = function () {
            $.sessionTimeout({
                title               : 'Session Timeout Notification',
                message             : 'Your session is about to expire.',
                keepAliveUrl        : '#',
                redirUrl            : http_host_js + 'adminusers/logout',
                logoutUrl           : http_host_js + 'adminusers/logout',
                warnAfter           : (parseInt(auto_logout_time) - 5000),
                redirAfter          : auto_logout_time,
                countdownMessage    : 'Redirecting in {timer} seconds.',
                countdownBar        : true
            });
        }

        return {
            init: function () {
                handlesessionTimeout();
            }
        };
    }();
/* ********************************************************** */