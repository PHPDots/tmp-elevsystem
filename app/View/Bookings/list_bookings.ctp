<?php
/* Get the name of the week days */
            $timestamp = strtotime('next Monday');
            $weekDays = array();
            for ($i = 0; $i < 7; $i++) {
                $weekDays[] = strftime('%a', $timestamp);
                $timestamp = strtotime('+1 day', $timestamp);
            }
    setlocale(LC_ALL,'da_DK');
      $danishMonths = Configure::read('danishMonths');
    $week_days = array("Sun"=>"Søndag",
                        "Mon"=>"Mandag",
                        "Tue"=>"Tirsdag",
                        "Wed"=>"Onsdag",
                        "Thu"=>"Torsdag",
                        "Fri"=>"Fredag",
                        "Sat"=>"lørdag",);
    $time = array(
                '6'     => array( 'title'=>'06:00', 'st' => '06:00', 'et' => '07:00' ),
                '7'     => array( 'title'=>'07:00', 'st' => '07:00', 'et' => '08:00' ),
                '8'     => array( 'title'=>'08:00', 'st' => '08:00', 'et' => '09:00' ),
                '9'     => array( 'title'=>'09:00', 'st' => '09:00', 'et' => '10:00' ),
                '10'    => array( 'title'=>'10:00', 'st' => '10:00', 'et' => '11:00' ),
                '11'    => array( 'title'=>'11:00', 'st' => '11:00', 'et' => '12:00' ),
                '12'    => array( 'title'=>'12:00', 'st' => '12:00', 'et' => '13:00' ),
                '13'    => array( 'title'=>'13:00', 'st' => '13:00', 'et' => '14:00' ),
                '14'    => array( 'title'=>'14:00', 'st' => '14:00', 'et' => '15:00' ),
                '15'    => array( 'title'=>'15:00', 'st' => '15:00', 'et' => '16:00' ),
                '16'    => array( 'title'=>'16:00', 'st' => '16:00', 'et' => '17:00' ),
                '17'    => array( 'title'=>'17:00', 'st' => '17:00', 'et' => '18:00' ),
                '18'    => array( 'title'=>'18:00', 'st' => '18:00', 'et' => '19:00' ),
                '19'    => array( 'title'=>'19:00', 'st' => '19:00', 'et' => '20:00' ),
                '20'    => array( 'title'=>'20:00', 'st' => '20:00', 'et' => '21:00' ),
                '21'    => array( 'title'=>'21:00', 'st' => '21:00', 'et' => '22:00' ),
                '22'    => array( 'title'=>'22:00', 'st' => '22:00', 'et' => '23:00' ),
                '23'    => array( 'title'=>'23:00', 'st' => '23:00', 'et' => '00:00' ),
                '24'    => array( 'title'=>'00:00', 'st' => '00:00', 'et' => '01:00' )
            );

    $minutes = array(0, 15, 30, 45);

    $colors = array('Køretime' => '25c525', 'Teori' => '4B77BE', 'Køreprøve' => '000000', 'Privat' => 'f3c200', 'Track' => 'D91E18');

    $right_now_date = date('Y-m-d');
?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<style type="text/css">
    .row_box{
        border-bottom: 1px solid #ddd;
        padding: 10px;
    }
    .time_slot{
        float: left;
    }
    .time_slot_description{
        margin-left: 15px;
        float: left;
        width: 90%;
        position: relative;
    }
    .padding_bottom{
        margin-bottom: 19px;
    }
    .btn_add{
        text-align: center;

        padding: 10px ;
        background: #fff;

        border: 1px solid #ddd;
        margin-left: 5px;
        width: 110px !important;
    }
    .btn_holder{
        margin: 0px auto;
        padding: 10px 0px;

    }
    .stop-scrolling {
     /* height: 150%;
      overflow: hidden;
    */
    }
    .margin_bottom5{
        margin-bottom: 5px;
    }
    hr {
        margin: 7px 0px 14px !important;
        border: 0;
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ffffff;
    }
    .padder{
        padding: 20px !important;
    }
    .btn_add_time{
        background: #30B30F;
        width: 100%;
        text-align: center;
        display: inline-block;
        color: #fff;
        text-transform: uppercase;
    }
    .btn_active{
        background: #30B30F !important;
        color: #fff  !important;
        border: 1px solid #30B30F;  
    }
    #add_data_frm{
        display: none;
    }
    .btn_set_booking{
        background: #fff;
       
        text-align: center;
        display: inline-block;
        color: #30B30F;
        border: 1px solid #ddd;
        text-transform: uppercase;
        padding: 12px !important;
    }
    .btn_active_set{
        background: #30B30F !important;
        color: #fff  !important;
        border: 1px solid #30B30F;
        cursor: pointer;
    }
    .number{
        height: 42px !important;
        font-size: 18px !important;
    }
    .right_0{

    }
    .right_10{

        margin-right: 7px !important;
    }
    .ui-timepicker-container{
        z-index: 99999999999 !important;
    }
    .lbl_padding{
        padding-top: 3px;
        margin:0px; 
    }
    .loader{
        position: absolute;
        top: 70px;
        text-align: center;
        z-index: 9999;
        width: 470px;
       margin-right: 0px auto;
       display: none;
       
    }
    .error_msg{
        color: #c10000;
        font-size: 11px;
        padding: 3px 0px;
        display: none;
    }
    .success-add{
        background: #30B30F !important;
        height: 300px;
        width: 250px;
        text-align:center; 
        padding: 50px !important;
        margin: 0px auto !important;
        display: none;
    }
    .btn_sucess{
        width: 200px !important;
        padding: 7px;
    }
    .txt_sucess{
        color: #fff;
        font-size: 20px;
        text-transform: uppercase;
    }
    .txt_sucess span{
        display: inline-block;
        padding:10px 0px 20px; 
        font-size: 30px;
        text-transform: uppercase;
    }
    .error-add{
        background: #c10000 !important;
        height: 300px;
        text-align:center; 
        padding: 50px 20px 30px !important;
        margin: 0px auto !important;
        display: none;
    }
    .txt_error{
         color: #fff;
        font-size: 12px;
        text-transform: uppercase;
    }

    .booking-approval{
        height: 300px;
        margin: 0px auto !important;
        display: none;
    }
        

    .txt_error span{
        display: inline-block;
        padding:10px 0px 30px; 
        font-size: 35px;
        text-transform: uppercase;
    }
    #dialog-add{
        display: none;
    }

    .booking-type {
        position: absolute;
        right: 0px;
        padding: 0 10px;
    }

    .booking-slot {
        position: absolute;
        width: 100%;
    }

    .booking-Køretime {
        border: 1px solid #25c525;
    }

    .booking-Køretime .booking-type {
        background-color: #25c525;
        color: #ffffff;
    }

    .booking-Teori {
        border: 1px solid #4B77BE;
    }

    .booking-Teori .booking-type {
        background-color: #4B77BE;
        color: #ffffff;
    }

    .booking-Køreprøve {
        border: 1px solid black;
    }

    .booking-Køreprøve .booking-type {
        background-color: black;
        color: #ffffff;
    }

    .booking-Privat {
        border: 1px solid #f3c200;
    }

    .booking-Privat .booking-type {
        background-color: #f3c200;
        color: #ffffff;
    }

    .booking-Track {
        border: 1px solid #D91E18;
    }

    .booking-Track .booking-type {
        background-color: #D91E18;
        color: #ffffff;
    }

    .tmp-rana {
        height: 10px;
    }
    .cal_link{
            font-size:  18px;
            padding: 8px 15px;
           display: inline-block;
           border:1px solid #ddd;
           margin-right: 10px !important;
            color:#000;  
    }
    .box_ic{

        border:1px solid #ddd;
        padding: 8px 15px;
        font-size: 18px; 
        color:#000;
        font-weight: bold;
    }
    .heaing_tbl{
        background-color: #349c1a;
        color: #ffffff;
        font-size: 20px;
        padding:10px 5px; 

    }
    .cal_box{
        box-shadow: 1px 2px 2px 5px #ddd;
        
    }
    .tbl_view{

        padding:5px;
    }
    .table-cal { 
        width: 100%;
        border-collapse: separate;
        border-spacing: 5px 5px;

     }
     .days_td{
         height: 40px;
         padding: 0px !important;

     }
    .days_text{
        margin:  3px;
        font-size: 16px !important;
        padding: 5px  !important;
    }
     .table-cal td{
        border:1px solid #ddd;
        padding: 35px 10px;
        font-size: 35px;
        text-align: center;

     }
     .table-cal td a{

        color:#000;

     }
     .heading_cal_1{
        font-weight: bold;
        font-size: 22px;

     }
     .current_day{
        color:  #30B30F;
     }

     .tbl_view table td:hover {
        background-color: #ccc;
     }

     .active_day{
        background-color: #ccc;  
     }
    .ui-dialog{
        padding: 0px !important;
        z-index: 9999;
    }

    .ui-dialog-titlebar {
        border-radius: 0px !important;
    }

    .btn_booking_approval {
        background-color: transparent;
        border: 1px solid #25c525;
        padding: 5px 15px;
        margin: 0 5px;
    }

    .ui-dialog .ui-dialog-titlebar-close {
        display: block !important;
    }

    .booking-approval .modal-body h3 {
        margin: 0 0 0 0;
        color: #A8A8A8;
    }

    .booking-approval .modal-body h5 {
        font-size: 15px;
        margin: 0 0 10px 0;
        color: #999;
    }

    .booking-approval hr {
        margin: 0 0 5px 0 !important;
    }

    .hide-me {
        display: none;
    }

    .btn_booking_approval:hover {
        background-color: #25c525 !important;
        border: 1px solid #25c525 !important;
        color: #fff !important;
    }

    select.padder {
        padding: 10px !important;
        height: 45px;
    }

    .success-error-msg {
        display: none;
        text-align: center;
        margin: 10px 0;
        font-size: 15px;
        background-color: #ca3333;
        padding: 10px;
        color: #fff;
        font-weight: bold;
    }

    .btn-booking-anyway {
        display: none;
        border: 1px solid #ddd;
        background-color: transparent;
        padding: 10px;
        width: 100%;
        color: #4c4747;
        font-weight: bold;
        font-size: 23px;
    }

    .time-input {
        text-align: center;
        font-size: 20px !important;
        font-weight: bold;
    }
    .book-time {
    float: left;
    padding-right: 5%;
    width: auto;
	}
	.book-wrap{
		width: auto;
		float: left;
	}
	.book-wrap span{
		display: block;
	}
    /* 1024 */
    @media only screen and (max-width: 1199px) {
        .booking-date-wrap .span6:nth-child(2) {
            display: none;
        }
        .booking-date-wrap .span6:nth-child(1) {
            width: 100%;
        }
    }

    #show_teacher_cal{
        margin-top: -4px;
        height: 36px;
    }
    .other_teachers_students {
        opacity: 0.7;
    }

</style>

<script type="text/javascript">
    var teacherObject = null;

    function validate(){
        var valid = true;    
        $('.error_msg').hide();
      
        var $type = $('#add_type').val();
        if($type == 'Køretime'){
            if($('#name_of_student').val() == ""){
                valid = false;
                $('.name_of_student').show();
            }
        }else if($type == 'Teori'){
            if($('#name_of_city').val() == ""){
                valid = false;
                $('.name_of_city').show();
            }
        }else if($type == 'Køreprøve'){
            if($('#name_of_student').val() == ""){
                valid = false;
                $('.name_of_student').show();
            }
        }else if($type == 'Privat'){
        }
        return valid;
    }

    jQuery(document).ready(function(){
        $( ".btn_add" ).click(function(){
            var type        = $(this).attr('type_booking');
            var caption     = $(this).attr('btn_caption');
           
            $('.btn_holder').find('.btn_add').removeClass('btn_active');
            $(this).addClass('btn_active');
            $('#add_type').val(''+type+'');
        
            $('.btn_add_time').val(''+caption+'');
            $('.booking_fields').hide();
            $('.'+type+'').show();
            $('#add_data_frm').show();
            $('.error_msg').hide();
        });

        jQuery(document).on('click', '#btn_success', function(e){
            window.location.reload();
        });

        $( ".btn_set_booking" ).click(function(){
            var val    = $(this).attr('val');
            $('.margin_bottom5').find('.btn_set_booking').removeClass('btn_active_set');
            $(this).addClass('btn_active_set');
            $('#set_type').val(''+val+'');
        });

        $( ".book_now" ).click(function(){
            var book_start = $(this).attr('val');
            var end_time = (Number(book_start)+1);
            var book_date   = $(this).attr('book_date');
            $('#book_date').val(''+book_date+''); 
            $( "#dialog-add" ).dialog({
                resizable: false,
                height: "auto",
                width: 500,
                modal: true,
                create: function(event, ui) {
                    $('.timepicker').timepicker({
                        timeFormat: 'H',
                        interval: 60,
                        minTime: '6',
                        maxTime: '23:00pm',
                        startTime: '6:00am',
                        defaultTime: book_start,
                        dynamic: false,
                        dropdown: true,
                        scrollbar: true
                    });   
                   
                    $('body').addClass('stop-scrolling');
                 },
                 beforeClose: function(event, ui) {
                    $('body').removeClass('stop-scrolling');
                  
                 }
            }).prev(".ui-dialog-titlebar").css("background","#30B30F");
        });   

        jQuery(document).delegate('.studentIdAutoSuggest','focusin',function() {
            $('.error_msg').hide();

            var studentField    = jQuery(this).parent().find('.studentId').attr('id');
            var element         = jQuery(this);
            var cur_user_id = "<?php echo $currentUser['User']['id'] ?>";

            if(jQuery(this).val() == '') {
                jQuery('#'+studentField).val('');
            }

            jQuery(this).autocomplete({
                minLength        : 2,
                select: function( event, ui ) {
                    if((teacherObject != null) && (typeof teacherObject.role != 'undefined') && (teacherObject.role == 'external_teacher')) {
                        jQuery('#'+studentField).val(-1);
                        return;
                    }
                    $('#student_id').val(ui.item.sysvalue);
                    element.parent().find('#'+studentField).val(ui.item.sysvalue);
                    element.val(ui.item.value);
                },
                source      : function(request, response){
                    jQuery.ajax({
                        url         : '<?php echo $this->Html->url(array('controller'=>'users','action'=>'autoSuggest')); ?>/' + request.term  + '/student',
                        dataType    : "json",
                        complete    : function() {
                        },
                        beforeSend  : function() {
                        },
                        success     : function(data) {
                            response(jQuery.map( data , function( item ) {
                                return {
                                    label     : item.User.firstname+' '+item.User.lastname,
                                    value     : item.User.firstname+' '+item.User.lastname,
                                    sysvalue  : item.User.id,
                                    teacher_id  : item.User.teacher_id,
                                    email     : ' [ Email : ' + item.User.email_id +' ] ',
                                    no        : ' [ Elevnummer : #' + item.User.student_number +' ] ',
                                    phone_no     : ' [ Phone no : ' + item.User.phone_no +' ] ',
                                    address     : ' [ Address : ' + item.User.address +' ] '
                                }
                            }));
                        },
                        error        : function() {
                        }
                    });
                },
                open: function() {

                    jQuery( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
                },
                close: function() {
                    jQuery( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
                }
            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                var is_other_teachers_students = 'other_teachers_students';
                if(cur_user_id == item.teacher_id){
                    var is_other_teachers_students = '';
                }
                return jQuery( '<li class="studentLabel '+is_other_teachers_students+'" ></li>' )
                    .data( "item.autocomplete", item )
                    .append( "<a> <span>" + item.label + "</span> <br> "+item.email+" <br>" + item.no +" <br>" + item.phone_no +" <br>" + item.address + "</a>" )
                    .appendTo( ul );
            };
        });

        jQuery( "#frm_add_Booking" ).submit(function(  ) {
            validate();
            jQuery.ajax({
                url:'<?php echo $this->Html->url(array('controller'=>'bookings','action'=>'create')); ?>',
                data:jQuery(this).serialize(),
                type:'post',
                dataType:'json',
                beforeSend:function(){
                    jQuery('.btn_add_time').attr("disabled", true);
                    jQuery('#add_data_frm').fadeTo('slow','0.5');
                    jQuery('.loader').show();

                    jQuery('.success-error-msg').hide();
                    jQuery('.booking-anyway').hide();
                    jQuery('.btn-booking-anyway').hide();
                    jQuery(this).find( '#neg_overwrite' ).val(0);
                },
                success: function(response){
                    if(response.status == 1) {
                        jQuery('#dialog-add').dialog('close');
                        $( "#success-add" ).dialog({
                            resizable: false,
                            height: "auto",
                            width: 350,
                            modal: true,
                            
                             create: function(event, ui) {
                                
                             },
                             beforeClose: function(event, ui) {
                                
                              
                             }
                        }).prev(".ui-dialog-titlebar").remove();
                    } else if(response.status == 99) {
                        jQuery('.success-error-msg').html( response.message ).show();
                    } else if(response.status == 98) {
                        jQuery('.success-error-msg').html( response.message ).show();
                        jQuery('.booking-anyway').show();
                    } else if(response.message != '') {
                        //jQuery('.success-error-msg').html( response.message ).show();
                    }
                },
                error:function(response){
                },
                complete:function(){
                    jQuery('.btn_add_time').attr("disabled", false);
                    jQuery('#add_data_frm').fadeTo('slow','1');
                    jQuery('.loader').hide();
                }
            });
            return false;
        });

        jQuery(document).on('click', '.booking-anyway', function(e){
            jQuery( this ).hide();
            jQuery('.btn-booking-anyway').show();
        });

        jQuery(document).on('click', '.btn-booking-anyway', function(e){
            jQuery( this ).hide();
            jQuery( '#dialog-add' ).dialog( 'close' );

            jQuery( "#booking-negative-confirm" ).dialog({
                resizable: false,
                height: "auto",
                width: 350,
                modal: true,
            }).prev(".ui-dialog-titlebar").remove();
        });

        jQuery(document).on('click', '.btn-booking-negative-approval', function(e){
            jQuery('#frm_add_Booking').find( '#neg_overwrite' ).val(1);
            jQuery('#frm_add_Booking').submit();
        });

        jQuery(document).on('click', '.view-booking-approval', function(e){
            var $id     = jQuery(this).attr('data-id');
            var $booking_type  = jQuery(this).attr('data-booking_type');
            var $color  = jQuery(this).attr('data-color');

            jQuery( "#booking-approval" ).dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true
            }).prev(".ui-widget-header").attr('style', 'background : #' + $color + ' !important; border: 1px solid #' + $color + ' !important').find('.ui-dialog-title').html($booking_type);

            jQuery( "#booking-approval" ).find('.footer button').css('border', '1px solid #' + $color);
            jQuery( "#booking-approval" ).find('input[name="booking_id"]').val($id);

            jQuery( "#booking-approval" ).find('.hide-me').hide();

            jQuery( "#booking-approval" ).find('.date').html( jQuery(this).attr('data-date') );
            jQuery( "#booking-approval" ).find('.time').html( jQuery(this).find('.book-time').text() );
            
            if( $booking_type == 'Køretime' ||  $booking_type == 'Køreprøve' ){
                jQuery( "#booking-approval" ).find('.student-name').html( jQuery(this).attr('data-student_name') ).show();
                jQuery( "#booking-approval" ).find('.student-number').html( jQuery(this).attr('data-student_number') ).show();
            } else if( $booking_type == 'Teori') {
                jQuery( "#booking-approval" ).find('.city-name').html( jQuery(this).attr('data-city_name') ).show();
            }

            var $note = jQuery(this).find('.view-note').text();
            if( $note != '' && $note != undefined ) {
                jQuery( "#booking-approval" ).find('.note').html( $note ).show();
            }

            if( $booking_type == 'Teori' ){
                jQuery('#frm-booking-approval').find('.app-2').hide();
            } else if( $booking_type == 'Privat' ){
                jQuery('#frm-booking-approval').find('.app-2').hide();
                jQuery('#frm-booking-approval').find('.app-1').hide();
            } else {
                jQuery('#frm-booking-approval').find('.app-1').show();
                jQuery('#frm-booking-approval').find('.app-2').show();
                jQuery('#frm-booking-approval').find('.app-3').show();
            }
        });

        jQuery(document).on('click', '.btn_booking_approval', function(e){
            var $value = parseInt( jQuery(this).val() );
            var $title = jQuery(this).closest('.ui-dialog').find('.ui-widget-header').text();

            if( $value != '' && $value != undefined && !isNaN( $value ) ){
                jQuery('#frm-booking-approval').find('input[name="action"]').val( $value );
            }

            if( $value == 3 ) {
                jQuery( "#booking-approval" ).dialog('close');

                jQuery( "#booking-delete" ).dialog({
                    resizable: false,
                    height: "auto",
                    width: 400,
                    modal: true
                }).prev(".ui-dialog-titlebar").remove();
            } else if( $value == 2 ) {
                jQuery( "#booking-approval" ).dialog('close');

                jQuery( "#booking-unapproved" ).dialog({
                    resizable: false,
                    height: "auto",
                    width: 400,
                    modal: true
                }).prev(".ui-dialog-titlebar").remove();
            } else {

                jQuery.ajax({
                    url:'<?php echo $this->Html->url(array('controller'=>'bookings','action'=>'updateBookingStatus')); ?>',
                    data:jQuery('#frm-booking-approval').serialize(),
                    type:'post',
                    dataType:'json',
                    beforeSend:function(){
                        jQuery('.btn_booking_approval').attr("disabled", true);
                        jQuery('.btn_booking_approval').fadeTo('slow','0.5');
                        jQuery('#frm-booking-approval').find('.loading').show();
                    },
                    success: function(response){
                        console.log( response );
                        if(response.status == 1){
                            var $value = jQuery('#frm-booking-approval').find('input[name="action"]').val();

                            if( $value == 3 ) {
                                jQuery( "#booking-delete" ).dialog('close');
                                $( "#success-add" ).dialog({
                                    resizable: false,
                                    height: "auto",
                                    width: 350,
                                    modal: true,
                                }).prev(".ui-dialog-titlebar").remove();
                                // window.location.reload();
                            } else if( $value == 2 ) {
                                jQuery( "#booking-unapproved" ).dialog('close');
                                $( "#success-add" ).dialog({
                                    resizable: false,
                                    height: "auto",
                                    width: 350,
                                    modal: true,
                                }).prev(".ui-dialog-titlebar").remove();
                                // window.location.reload();
                            } else {
                                jQuery( "#booking-approval" ).dialog('close');

                                jQuery( "#booking-approved" ).dialog({
                                    resizable: false,
                                    height: "auto",
                                    width: 400,
                                    modal: true
                                }).prev(".ui-dialog-titlebar").remove();

                                jQuery( "#booking-approved" ).find('.title'). html( $title + ' ER');
                            }
                        }
                    },
                    complete:function(){
                        jQuery('.btn_booking_approval').attr("disabled", false);
                        jQuery('.btn_booking_approval').fadeTo('slow','1');
                        jQuery('#frm-booking-approval').find('.loading').hide();
                    }
                });
            }
        });

        jQuery("#show_teacher_cal").change(function(){
            var id = jQuery("#show_teacher_cal :selected").val();
            if(id == ''){
                window.location.href = "<?php echo $this->request->webroot . 'Bookings/listBookings' ; ?>";
            }else{
                window.location.href = "<?php echo $this->request->webroot . 'Bookings/listBookings?id=' ; ?>"+id;
            }
        });
    });      
</script>

<div class="booking-approval" id="booking-approval">
    <form id="frm-booking-approval" action="" method="post">
        <input type="hidden" name="booking_id" value="0">
        <input type="hidden" name="action" value="0">
        <div class="modal-body" style="padding: 0 0 15px 0;">
            <div class="loading" ><?php  echo $this->Html->image("loader.gif");?></div>
            <h3 class="hide-me student-name"></h3>
            <h5 class="hide-me student-number"></h5>
            <h3 class="hide-me city-name"></h3>
            <p>Mandag. <span class="date"></span> <span class="time"></span></p>
            <hr />
            <p class="hide-me note"></p>
        </div>

        <div class="footer text-center">
            <button type="button" value="3" class="app-3 btn_booking_approval">SLET</button>
            <button type="button" value="2" class="app-2 btn_booking_approval">UDEBLEVET</button>
            <button type="button" value="1" class="app-1 btn_booking_approval">GODKEND</button>
        </div>
    </form>
</div>

<div class="booking-approval  text-center" id="booking-approved" style="background: #25c525; padding: 25px;">
    <div class="">
        <div class="txt_sucess">
            <span class="title">Køretime ER</span><br /><span>GODKEND</span>
        </div>
        <div>
            <input type="button" value="BOOK NY TID TIL DENNE ELEV" id="btn_success" class="btn_sucess" style="width: auto !important;">
        </div>
    </div>
</div>

<div class="booking-approval  text-center" id="booking-unapproved" style="background: #cc0000; padding: 25px;">
    <div class="">
        <div class="txt_sucess">
            BEKRAEFT AT ELEVEN ER<span>UDEBLEVET</span>
        </div>
        <div>
            <button type="button" value="BEKRAEFT" class="btn_booking_approval" style="background-color: #fff">BEKRAEFT</button>
        </div>
    </div>
</div>

<div class="booking-approval text-center" id="booking-delete" style="background: #cc0000; padding: 25px;">
    <div class="">
        <div class="txt_sucess">
            BEKRAEFT AT BOOKINGEN SKAL<span>SLETTES</span>
        </div>
        <div>
            <button type="button" value="SLET" class="btn_booking_approval" style="background-color: #fff">BEKRAEFT</button>
        </div>
    </div>
</div>

<div class="booking-approval text-center" id="booking-negative-confirm" style="background: #cc0000; padding: 25px;">
    <div class="">
        <div class="txt_sucess" style="margin-bottom: 20px;line-height: 30px;">
            MÅ KUN BENYTTES I NØDSTILFÆLDE YDELSER SKAL ALTID BETALES FORUD
        </div>
        <div>
            <button type="button" value="1" class="btn-booking-negative-approval" style="background-color: #fff">GENNEMFØR BOOKING</button>
        </div>
    </div>
</div>

<div class="success-add" id="success-add">
    <div class="">
        <div class="txt_sucess">
            <span>Tilføjet</span>
        </div>
        <div>
            <input type="button" value="LUK" id="btn_success" class="btn_sucess">
        </div>
    </div>
</div>

<div class="error-add" id="error-add">
    <div class="">
        <div class="txt_error">
            BOOKINGGEN KAN IKKE GENNEMFORES
            <SPAN>
            ANDEN BOOKING
            </SPAN>   
        </div>
        <div>
            <input type="button" value="LUK" id="btn_success" class="btn_sucess">
        </div>
    </div>
</div>

<div class="booking-approval text-center" id="booking-overlap" style="background: #cc0000; padding: 25px;">
    <div class="">
        <div class="txt_error">
            BOOKINGEN KAN IKKE GENNEMFØRES
            <SPAN>
            ANDEN BOOKING
            </SPAN>   
        </div>
        <div>
            <input type="button" value="LUK" id="btn_success" class="btn_sucess">
        </div>
    </div>
</div>

<div id="dialog-add" title="TILFØJ TID">
    <p class="btn_holder" >
        <input type="button" class="btn_add" value="Køretime" type_booking="Køretime" btn_caption = "TILFØJ Køretime"> 
        <input type="button" class="btn_add" value="Teori" type_booking="Teori" btn_caption = "TILFØJ Teori "> 
        <input type="button" class="btn_add" value="Køreprøve" type_booking="Køreprøve" btn_caption = "TILFØJ Køreprøve"> 
        <input type="button" class="btn_add" value="Privat" type_booking="Privat" btn_caption = "TILFØJ Privat"> 
    </p>

    <div id="add_data_frm" style="position:relative;">
        <div class="loader" ><?php  echo $this->Html->image("loader.gif");?></div> 
        <form id="frm_add_Booking">
            <input type="hidden" id="student_id" name="student_id" value="Køretime">
            <input type="hidden" id="add_type" name="add_type" value="Køretime">
            <input type="hidden" id="set_type" name="set_type" value="2">
            <input type="hidden" id="book_date" name="book_date">
            <input type="hidden" id="neg_overwrite" name="neg_overwrite" value="0">

            <div class="row-fluid"> 
                <div class="margin_bottom5 Køretime Køreprøve booking_fields">
                    <input type="text" name="name_of_student" id="name_of_student" value="" class="studentIdAutoSuggest padder span12 ">
                    <div class="error_msg name_of_student"><?php echo __("This field is required.");?></div>
                </div>
                <div class="margin_bottom5 Teori booking_fields">
                    <select name="city_id" id="name_of_city" value="" class="padder span12 ">
                        <?php foreach ($cities as $city_id => $city_name) { ?>
                            <option value="<?php echo $city_id; ?>"><?php echo $city_name; ?></option>
                        <?php } ?>
                    </select>
                    <div class="error_msg city_id"><?php echo __("This field is required.");?></div>
                </div>
                <div class="margin_bottom5 booking_fields Køretime Køreprøve">
                    <input type="text" name="time_start" id="time_start"  class="time-input timepicker padder span6 ">  :  
                    <input type="number" min="0" max="60" value="0" step="5" name="time_end" id="time_end"  class="time-input number span6">
                </div>
                <div class="margin_bottom5 booking_fields Privat Teori">
                    <label class="span12 lbl_padding"><?php echo __('From');?></label>
                    <input type="text" name="time_start_from" id="time_start_from"  class="time-input timepicker padder span6 ">  :  
                    <input type="number" min="0" max="60" value="0" step="5" name="time_start_from_min" id="time_end"  class="time-input number span6">
                </div>
                <div class="margin_bottom5 booking_fields Privat Teori">
                    <label class="span12 lbl_padding"><?php echo __('To');?></label>
                    <input type="text" name="time_start_to" id="time_start_to"  class="time-input timepicker padder span6 ">  :  
                    <input type="number" min="0" max="60" value="0" step="5" name="time_start_to_min" id="time_end"  class="time-input number span6">
                </div>
                <div class="margin_bottom5 Køretime booking_fields">
                    <input type="button" class="right_10 btn_set_booking span6" val="1" name="time_single" id="singel_time" value="Enkelt Time" >     
                    <input type="button" class="right_0 btn_set_booking btn_active_set span6 " val="2" name="time_double" id="time_end" value="Dobbelt time" >
                </div>
                <div class="margin_bottom5 booking_fields Køretime Køreprøve Privat">
                    <input type="text" name="extra_note" id="extra_note" placeholder="<?php echo __("Note : ");?>" class=" padder span12">
                </div>
                <input type="submit" value="TILFØJ Køretime" class="btn_add_time span12" >
                <div class="success-error-msg"></div>
                <div class="text-center">
                    <a href="javascript:;" class="hide-me booking-anyway">Book alligevel</a>
                </div>
                <button type="button" class="hide-me btn-booking-anyway" >Booking med negativ saldo</button>
            </div>
        </form>
    </div>
</div>

<div class="inner-content">
    <div class="row-fluid">
    <?php
        if($calview) {
            $current_month = date("n");

            $month = (isset($_GET['m'])) ? $_GET['m'] : date("n");
            $year = (isset($_GET['y'])) ? $_GET['y'] : date("Y");

            $previous_month = ($month - 1);
            $next_month = ($month + 1);

            $previous_year = $year;
            $next_year = $year;

            if($previous_month==0) {
                $previous_month = 12;
                $previous_year = $year-1;
            }

            if($next_month>12) {
                $next_month = 1;
                $next_year = $year+1;
            }

            $date = (!isset($_GET['m']) && !isset($_GET['y'])) ? time() : strtotime($_GET['m'] . '/1/' . $_GET['y']);

            /* Set the date */
            $day = date('d', $date);

            $month = date('m', $date);

            $m = date('F', $date);
            $year = date('Y', $date);
          
            $firstDay = mktime(0,0,0,$month, 1, $year);
            $title = strftime('%B', $firstDay);
            $dayOfWeek = date('D', $firstDay);
          
            $daysInMonth = cal_days_in_month(0, $month, $year);
             
             switch ($dayOfWeek) {
                case "Mon": $blank = 0;
                    break;
                case "Tue": $blank = 1;
                    break;
                case "Wed": $blank = 2;
                    break;
                case "Thu": $blank = 3;
                    break;
                case "Fri": $blank = 4;
                    break;
                case "Sat": $blank = 5;
                    break;
                case "Sun": $blank = 6;
                    break;
            }
            
          
            //$blank = date('w', strtotime("{$year}-{$month}-01"));
            //echo  $blank ;
            ?>
            <div class="span12" style="padding-bottom: 15px;">
                <div style="float:left;width:20%">
                    <div  class="  heading_cal_1 "><?php 
                   
                    echo  $danishMonths[$m];?> <?php echo $year;?></div>

                </div>
            
                <div style="float:right;text-align: right; margin-top: -23px;" >
                    
                    <a style="margin-top:15px"  href="<?php echo $this->request->webroot . 'Bookings/listBookings?calview=yes&m=' .$previous_month; ?>&y=<?php echo $previous_year?>" class="box_ic fa fa-angle-left " ></a>
                    <a href="<?php echo $this->request->webroot . 'Bookings/listBookings?calview=yes&m=' .$next_month; ?>&y=<?php echo $next_year?>"  class="  box_ic fa fa-angle-right "></a>
                </div>
                 <div style="clear:both"></div>
            </div>
            <div style="clear:both"></div>
            <div class="cal_box">
                <div class="heaing_tbl">DATO</div>
                <div   class="tbl_view">
                    <table cellspacing="10" border="0" class=' table-cal' style="table-layout: fixed;">
                        <tr>
                            <?php foreach($weekDays as $key => $weekDay) : ?>
                                <td class="text-center days_td">
                                        <div class="days_text">
                                                <?php echo $week_days[$weekDay]; ?>
                                         </div>                   
                                </td>
                            <?php endforeach ?>
                        </tr>
                        <tr>
                            <?php for($i = 0; $i < $blank; $i++): ?>
                                <td></td>
                            <?php endfor; ?>
                            <?php for($i = 1; $i <= $daysInMonth; $i++): ?>
                               
                                    <td <?php if($day == $i){ echo "class='active_day'";} ?>>
                                        <a href="<?php echo $this->request->webroot . 'Bookings/listBookings?date=' .$i; ?>-<?php echo $month?>-<?php echo $year?>"><?php echo $i ?></a>
                                    </td>
                               
                                <?php if(($i + $blank) % 7 == 0): ?>
                                    </tr><tr>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <?php for($i = 0; ($i + $blank + $daysInMonth) % 7 != 0; $i++): ?>
                                <td></td>
                            <?php endfor; ?>
                        </tr>
                    </table>
                </div>
            </div>
            <?php 
        } else {
            ?>
            <div style="text-align: right"  class=" padding_bottom ">
                <?php
                if($currentUser['User']['role'] == 'admin'){ 
                    ?>
                        <select id="show_teacher_cal" >
                            <option value="">Vælg Lærer</option>
                            <?php foreach ($teachers_list as $key => $teacher) { 
                                $selected = '';
                                if($this->params['url']['id'] == $teacher['id']){
                                    $selected = 'selected';
                                }
                                echo "<option value='$teacher[id]'  $selected >$teacher[firstname] $teacher[lastname]</option>";
                                ?>
                            <?php } ?>
                        </select>
                <?php } ?>
                <a href="<?php echo $this->request->webroot . 'Bookings/listBookings?calview=yes' ; ?>" class="fa  fa-3x cal_link" >Hop til dato</a>
                <a style="margin-top:15px" href="<?php echo $this->request->webroot . 'Bookings/listBookings?date=' . date('d-m-Y', strtotime($prev_date)); ?>" class="box_ic fa fa-angle-left " ></a>
                <a href="<?php echo $this->request->webroot . 'Bookings/listBookings?date=' . date('d-m-Y', strtotime($next_date . ' +1 days')); ?>"  class="  box_ic fa fa-angle-right "></a>
            </div>

            <div class="booking-date-wrap">
                <div class="span6">
                    <div class="widget">
                        <div class="widget-header">
                            <h5>
                                <?php
                                    echo date('d',  strtotime($date)).' '.$danishMonths[date('F',  strtotime($date))].', '.date('Y',  strtotime($date));
                                ?>
                            </h5>
                        </div>
                        
                        <?php foreach($time as $k=>$t) { ?>
                            <div class="row-fluid">
                                <div class="row_box">
                                    <div class="time_slot"> 
                                        <?php if( strtotime($date) >= strtotime($right_now_date) ) { ?>
                                            <a href="javascript://" class="book_now" val="<?php echo $k;?>" book_date = "<?php echo $date;?>"><?php echo $t['title'];?></a>
                                        <?php } else { ?>
                                            <?php echo $t['title'];?>
                                        <?php } ?>
                                    </div>

                                    <div class="time_slot_description">
                                        <?php /*foreach ($minutes as $min) { ?>
                                            <div class="tmp-rana <?php echo $k . '-' . $min ?>"></div>
                                        <?php }*/ ?>
                                        <?php
                                            $has = false;
                                            if( isset($current_bookings) && count( $current_bookings ) > 0) {
                                                foreach ($current_bookings as $current) {
                                                    $cst = strtotime( $date . ' ' . $t['st'] .':00'  );
                                                    $cet = strtotime( $date . ' ' . $t['et'] .':00'  );
                                                    $bst = strtotime( $current['date'] . ' ' . $current['start_time'] .':00' );
                                                    
                                                    if( $current['booking_type'] == 'Track' ) {
                                                        $bet = strtotime( $current['date'] . ' ' . $current['c_end_time'] );
                                                    } else {
                                                        $bet = strtotime( $current['date'] . ' ' . $current['end_time'] );    
                                                    }

                                                    if( $bst >= $cst && $bst < $cet ) {
                                                        $has = true;

                                                        if( $current['booking_type'] == 'Track' ) {
                                                            $class = 'booking-slot booking-'. $current['booking_type'];
                                                        } else {
                                                            $class = 'view-booking-approval booking-slot booking-'. $current['booking_type'];
                                                        }

                                                        foreach ($minutes as $min) {
                                                            $s_tmp_time = strtotime( $date . ' ' . $k . ':' . $min );
                                                            $e_tmp_time = strtotime( $date . ' ' . $k . ':' . ( $min + 15 ) );
                                                            if( $bst >= $s_tmp_time && $bst < $e_tmp_time ) {
                                                                $height = ceil( ( $bet - $bst ) / 60 );
                                                                $booking_type = $current['booking_type'];
                                                                $booking_note = $current['note'];
                                                                $style_track = '';
                                                                if( $height > '0' ) {
                                                                    $style_track = 'float:left';
                                                                }
                                                                echo '<div class="tmp-rana '. $k . '-' . $min .'">';
                                                                    echo '<div
                                                                        data-id="'. $current['id'] .'"
                                                                        data-date="'. date( 'd/m/Y', strtotime($current['date']) ) .'"
                                                                        data-booking_type="'. $current['booking_type'] .'"
                                                                        data-color="'. @$colors[$current['booking_type']] .'" 
                                                                        data-student_name="'. @$current['student_name'] .'" 
                                                                        data-student_number="'. @$current['student_number'] .'" 
                                                                        data-city_name="'. @$cities[$current['city_id']] .'"
                                                                        class="'. $class .'"
                                                                        style="min-height:'. $height .'px;'.$style_track.' ">';
                                                                    echo '<span class="booking-type">' . $current['booking_type'] . '</span>';

                                                                    if( $booking_type == 'Track' ) {
                                                                        echo '<span class="book-time">'. $current['start_time'] . ' - ' . $current['end_time'] . ' :: ' . strtoupper($current['city_id']) . '</span>';
                                                                        if($booking_note != '' && $booking_note != ' '){
                                                                            echo '<div class="book-wrap">';
                                                                            echo '<span class="view-note">Note: '. $booking_note . '</span>';
                                                                            echo '</div>';
                                                                        }
                                                                    } else {
                                                                        echo '<span class="book-time">'. $current['start_time'] . ' - ' . $current['end_time'] . '</span>';
                                                                        echo '<div class="book-wrap">';
                                                                        if(isset($current['student_name']) && $current['student_name'] != '' && $current['student_name'] != ' '){
                                                                            echo '<span class="book-student-name">'.$current['student_name'].'</span>'; 
                                                                            echo '<span class="book-student-no">Student no: '.$current['elev_nummer'].'</span>'; 
                                                                            echo '<span class="book-student-no">Mobile no: '.$current['student_number'].'</span>'; 
                                                                            echo '<span class="book-student-addr">Address: '.$current['student_address'].'</span>'; 
                                                                        }
                                                                        if($booking_note != '' && $booking_note != ' '){
                                                                        echo '<span class="view-note">Note: '. $booking_note . '</span>';
                                                                        }
                                                                        echo '</div>';
                                                                    }

                                                                    
                                                                    echo '</div>';
                                                                echo '</div>';
                                                            } else {
                                                                echo '<div class="tmp-rana '. $k . '-' . $min .'"></div>';
                                                            }
                                                        }
                                                        
                                                    }
                                                }
                                            }

                                            if( $has == false ) {
                                                foreach ($minutes as $min) {
                                                    echo '<div class="tmp-rana '. $k . '-' . $min .'"></div>';
                                                }
                                            }
                                        ?>
                                    </div>

                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="span6">
                    <div class="widget">
                        <div class="widget-header">
                            <h5>
                                <?php
                                    $danishMonths = Configure::read('danishMonths');
                                    echo date('d',  strtotime($next_date)).' '.$danishMonths[date('F',  strtotime($next_date))].', '.date('Y',  strtotime($next_date));
                                ?>
                            </h5>
                        </div>

                        <?php foreach(  $time as $k=>$t) { ?>
                            <div class="row-fluid">
                                <div class="row_box">
                                    <div class="time_slot">    
                                        <?php if( strtotime($next_date) >= strtotime($right_now_date) ) { ?>
                                            <a href="javascript://" class="book_now" val="<?php echo $k;?>" book_date = "<?php echo $next_date;?>"><?php echo $t['title'];?></a>
                                        <?php } else { ?>
                                            <?php echo $t['title'];?>
                                        <?php } ?>
                                    </div>
                                    <div class="time_slot_description">
                                        <?php /*foreach ($minutes as $min) { ?>
                                            <div class="tmp-rana <?php echo $k . '-' . $min ?>"></div>
                                        <?php }*/ ?>
                                        <?php
                                            $height = 0;
                                            $has = false;
                                            if( isset($next_bookings) && count( $next_bookings ) > 0 ) {
                                                foreach ($next_bookings as $next) {
                                                    $cst = strtotime( $next_date . ' ' . $t['st'] );
                                                    $cet = strtotime( $next_date . ' ' . $t['et'] );
                                                    $bst = strtotime( $next['date'] . ' ' . $next['start_time'] );

                                                    if( $next['booking_type'] == 'Track' ) {
                                                        $bet = strtotime( $next['date'] . ' ' . $next['c_end_time'] );
                                                    } else {
                                                        $bet = strtotime( $next['date'] . ' ' . $next['end_time'] );    
                                                    }

                                                    if( $bst >= $cst && $bst < $cet ) {
                                                        $has = true;

                                                        if( $next['booking_type'] == 'Track' ) {
                                                            $class = 'booking-slot booking-'. $next['booking_type'];
                                                        } else {
                                                            $class = 'view-booking-approval booking-slot booking-'. $next['booking_type'];
                                                        }

                                                        foreach ($minutes as $min) {
                                                            $s_tmp_time = strtotime( $next_date . ' ' . $k . ':' . $min );
                                                            $e_tmp_time = strtotime( $next_date . ' ' . $k . ':' . ( $min + 15 ) );
                                                            if( $bst >= $s_tmp_time && $bst < $e_tmp_time ) {
                                                                $height = ceil( ( $bet - $bst ) / 60 );
                                                                $booking_type = $next['booking_type'];
                                                                $booking_note = $next['note'];
                                                                $style_track = '';
                                                                if( $height > '0' ) {
                                                                    $style_track = 'float:left';
                                                                }
                                                                echo '<div class="tmp-rana '. $k . '-' . $min .'">';
                                                                    echo '<div
                                                                        data-id="'. $next['id'] .'"
                                                                        data-date="'. date( 'd/m/Y', strtotime($next['date']) ) .'"
                                                                        data-booking_type="'. $next['booking_type'] .'"
                                                                        data-color="'. @$colors[$next['booking_type']] .'" 
                                                                        data-student_name="'. @$next['student_name'] .'" 
                                                                        data-student_number="'. @$next['student_number'] .'" 
                                                                        data-city_name="'. @$cities[$next['city_id']] .'"
                                                                        class="'. $class .'"
                                                                        style="min-height:'. $height .'px;'.$style_track.'">';
                                                                    echo '<span class="booking-type">' . $next['booking_type'] . '</span>';

                                                                    if( $booking_type == 'Track' ) {
                                                                        echo '<span class="book-time">'. $next['start_time'] . ' - ' . $next['end_time'] . ' :: ' . strtoupper($next['city_id']) . '</span></br>';
                                                                        if($booking_note != '' && $booking_note != ' '){
                                                                            echo '<div class="book-wrap">';
                                                                            echo '<span class="view-note">Note: '. $booking_note . '</span>';
                                                                            echo '</div>';
                                                                        }
                                                                    } else {
                                                                        echo '<span class="book-time">'. $next['start_time'] . ' - ' . $next['end_time'] . '</span>';
                                                                        echo '<div class="book-wrap">';
                                                                        if(isset($next['student_name']) && $next['student_name'] != '' && $next['student_name'] != ' '){
                                                                            echo '<span class="book-student-name">'.$next['student_name'].'</span>'; 
                                                                            echo '<span class="book-student-no">Student no: '.$next['elev_nummer'].'</span>'; 
                                                                            echo '<span class="book-student-no">Mobile no: '.$next['student_number'].'</span>'; 
                                                                            echo '<span class="book-student-addr">Address: '.$next['student_address'].'</span>'; 
                                                                        }
                                                                        if($booking_note != '' && $booking_note != ' '){
                                                                        echo '<span class="view-note">Note: '. $booking_note . '</span>';
                                                                        }
                                                                        echo '</div>';
                                                                    }

                                                                    echo '</div>';
                                                                echo '</div>';
                                                            } else {
                                                                echo '<div class="tmp-rana '. $k . '-' . $min .'"></div>';
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            if( $has == false ) {
                                                foreach ($minutes as $min) {
                                                    echo '<div class="tmp-rana '. $k . '-' . $min .'"></div>';
                                                }
                                            }
                                        ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>
            <?php 
        }
    ?>
    </div>
</div>