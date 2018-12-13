function addRemove(objectelement){
    jQuery(objectelement).parents('.multiplerows').first().find('.removebtn').show();
    jQuery(objectelement).parents('.multiplerows').first().find('.removebtn').last().hide();
    jQuery(objectelement).parents('.multiplerows').first().find('.addbtn').hide();
    jQuery(objectelement).parents('.multiplerows').first().find('.addbtn').last().show();
}

var da  = [];

da = {
    dayNamesShort:["Søn","Man","Tir","Ons","Tor","Fre","Lør"],
    dayNamesMin:["Sø","Ma","Ti","On","To","Fr","Lø"],
    monthNames:["Januar","Februar","Marts","April","Maj","Juni","Juli","August","September","Oktober","November","December"],
    monthNamesShort:["Jan","Feb","Mar","Apr","Maj","Jun","Jul","Aug","Sep","Okt","Nov","Dec"]        
};

jQuery(document).ready(function(){
    
    jQuery('.datepicker').datepicker({
        dateFormat  : 'dd.mm.yy',
        changeMonth: true,
        changeYear: true,
        beforeShow  : function() {
            if(jQuery(this).attr('maxDate') != '') {            
                jQuery(this).datepicker( "option", "maxDate", jQuery(this).attr('maxDate'));            
            }
            var minYear = jQuery(this).attr('minYear');
            var maxYear = jQuery(this).attr('maxYear');
            jQuery(this).datepicker( "option", "yearRange", "-"+minYear+":+"+maxYear);
        }
    });
    
    jQuery("#dropdown").change(function(){    
        jQuery(this).parents('form').submit();    
    });
    
    jQuery('.chosen-select').chosen();
    
    jQuery('.css-radio').parent().find('label').addClass('css-radio-label');
    jQuery('.detailsLoading').hide();
    jQuery(document).delegate('#formSubmit','click',function(){
        
        form    = jQuery(this).parents('form').attr('id');
        href    = jQuery(this).parents('form').attr('action');
        
        jQuery.ajax({
            url     : href,
            data    : jQuery('#'+form).serialize(),
            type    : 'POST',
            dataType: 'html',
            beforeSend: function(data){
                jQuery('#formControlls').hide();
                jQuery('#submitForm').show();
            },
            success  : function(data){               
                jQuery('#exc').html(data);
            },
            complete: function(data){
                jQuery('#submitForm').hide();
                jQuery('#formControlls').show();
            },
            error   : function(){
                alert('error');
            }
        });
        
    });
    
    jQuery(document).delegate('.multiplerows .addbtn','click',function(){ 
        index = jQuery(this).parents('.multiplerows').find('.multipleRow').length;
        jQuery(this).parents('.multiplerows').append(jQuery('#'+jQuery(this).attr('container')).html().replace(/\%i\%/g,index));
        addRemove(this);
    });

    jQuery(document).delegate('.multiplerows .removebtn','click',function(){      
        jQuery(this).parents('.multipleRow').remove();            
        addRemove(this);
    });  

    jQuery.each(jQuery('.multiplerows'),function(index, value){
        addRemove(jQuery(this).find('.multipleRow').last());
    });
});


