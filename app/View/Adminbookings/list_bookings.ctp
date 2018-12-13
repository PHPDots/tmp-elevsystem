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
            // '4'     => array( 'title'=>'04:00', 'st' => '04:00', 'et' => '05:00' ),
            // '5'     => array( 'title'=>'05:00', 'st' => '05:00', 'et' => '06:00' ),
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
            '23'    => array( 'title'=>'23:00', 'st' => '23:00', 'et' => '24:00' ),
            '0'     => array( 'title'=>'00:00', 'st' => '00:00', 'et' => '01:00' ),
            '1'     => array( 'title'=>'01:00', 'st' => '01:00', 'et' => '02:00' ),
            '2'     => array( 'title'=>'02:00', 'st' => '02:00', 'et' => '03:00' ),
            '3'     => array( 'title'=>'03:00', 'st' => '03:00', 'et' => '04:00' ),
        );

$minutes = array(0, 15, 30, 45);

$colors = array('Køretime' => '25c525', 'Teori' => '4B77BE', 'Køreprøve' => '000000', 'Privat' => 'f3c200', 'Track' => 'D91E18');

$right_now_date = date('Y-m-d');
$right_now_time = date('H:i');
echo $this->Html->css(array(       
                            'admin/jquery.timepicker.min',
                            'admin/list-booking',
                        )
                     );
echo $this->Html->script(array(
                                'admin/jquery.timepicker.min',
                            )
                        );

?>
<style>
/*
.view-booking-approval:active
{
    color: blue;
}*/
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
            if($(".Firstaid_input").is(':visible') && ($('.handed_firstaid_papirs_ja').attr("checked") != "checked" && $('.handed_firstaid_papirs_nej').attr("checked") != "checked")){
                valid = false;
                $('.handed_firstaid_papirs').show();

            }
            if($(".theory_test_input").is(':visible') && ($('.theory_test_passed_nej').attr("checked") != "checked" && $('.theory_test_passed_ja').attr("checked") != "checked")){
                valid = false;
                $('.theory_test_passed').show();

            }
            if($(".no_teacher").is(':visible') && ($('.no_teacher_nej').attr("checked") != "checked" && $('.no_teacher_ja').attr("checked") != "checked")){
                valid = false;
                $('.no_teacher').show();

            }
            if($(".other_teacher").is(':visible') && ($('.other_teacher_nej').attr("checked") != "checked" && $('.other_teacher_ja').attr("checked") != "checked")){
                valid = false;
                $('.other_teacher').show();

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

            var totalBkng = parseInt($('#set_type').val()) + parseInt($('#no_of_booking').val());
            if(totalBkng >= 8)
            {
                $('.Firstaid_input').show();
            }else{
                $('.Firstaid_input').hide();
            }
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
                        // interval: 60,
                        show2400:true,
                        'step': 60,
                        // minTime: '0',
                        // maxTime: '23:00pm',
                        // startTime: '0:00am',
                        // setTime: book_start,
                        // dynamic: false,
                        // dropdown: true,
                        // scrollbar: true,
                        disableTimeRanges: [
                                            ['3:00','6:00'],
                                          ]
                    });
                   $('.timepicker').timepicker('setTime', book_start);
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
                        url         : '<?php echo $this->Html->url(array('controller'=>'adminusers','action'=>'autoSuggest')); ?>/' + request.term  + '/student',
                        dataType    : "json",
                        complete    : function() {
                        },
                        beforeSend  : function() {
                        },
                        success     : function(data) {
                            // console.log(data);
                            response(jQuery.map( data , function( item ) {
								
								var returnBalance = item.User.available_balance;
								if(returnBalance == '' || returnBalance == null)
								{
									returnBalance = 0;
								}
								
                                return {
                                    label     : item.User.firstname+' '+item.User.lastname,
                                    value     : item.User.firstname+' '+item.User.lastname,
                                    sysvalue  : item.User.id,
                                    teacher_id  : item.User.teacher_id,
                                    email     : ' [ Email : ' + item.User.email_id +' ] ',
                                    no        : ' [ Elevnummer : #' + item.User.student_number +' ] ',
                                    student_number        : item.User.student_number ,
                                    phone_no     : ' [ Mobil : ' + item.User.phone_no +' ] ',
                                    address     : ' [ Adresse : ' + item.User.address +' ] ',
                                    balance     : ' [ Saldo : ' + returnBalance +' ] ',
                                    no_of_booking     : item[0].no_of_booking ,
                                    no_of_driving_lessons     : item[0].no_of_driving_lessons ,
                                    theory_test_passed     : item.User.theory_test_passed ,
                                    handed_firstaid_papirs     : item.User.handed_firstaid_papirs ,
                                    teacher_id     : item.User.teacher_id ,
                                    teacher_name     : item[0].teacher_name ,
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
                var booking_type = jQuery('#add_type').val();

                if(cur_user_id == item.teacher_id){

                    var is_other_teachers_students = '';
                }
                category_id = item.student_number;
                category_id = category_id.substr(10);
                category_id = category_id.slice(0, -3);

                if(category_id == '04' && item.no_of_booking == 0){
                    is_other_teachers_students += ' old_stud';
                }
                if(item.handed_firstaid_papirs == 0 && item.no_of_driving_lessons >= 6){
                    if(item.no_of_driving_lessons >= 8){
                        is_other_teachers_students += ' Firstaid';
                    }
                    else{
                        var set_type = $('#set_type').val();
                        if(set_type > 0){
                            var totalBooking = item.no_of_driving_lessons + parseInt(set_type);
                            if(totalBooking >= 8)
                            {
                                $('#no_of_booking').val(item.no_of_driving_lessons);
                                is_other_teachers_students += ' Firstaid';
                            }
                        }
                    }
                }

                if(item.theory_test_passed < 1 && item.no_of_driving_lessons > 13 || booking_type == 'Køreprøve'){
                    is_other_teachers_students += ' theory_test';
                }

                current_teacher_id = "<?php echo $currentUser['User']['id']; ?>";

                if(item.teacher_id == null){
                    is_other_teachers_students += ' no_teacher_show';
                }else if(item.teacher_id != current_teacher_id){
                    is_other_teachers_students += ' other_teacher_show';
                    $('.other_teacher_name').html(item.teacher_name);
                }
               
                return jQuery( '<li class="studentLabel '+is_other_teachers_students+'" ></li>' )
                    .data( "item.autocomplete", item )
                    .append( '<a> <span>' + item.label + '</span> <br> '+item.email+' <br>' + item.no +' <br>' + item.phone_no +' <br>' + item.address+' <br>' + item.balance+'</a>' )
                    .appendTo( ul );
            };
        });

        jQuery(document).on('click', '.old_stud', function(e){
            jQuery('.recovery_type').show();
        });
        jQuery(document).on('click', '.Firstaid', function(e){
            jQuery('.Firstaid_input').show();
        });
        jQuery(document).on('click', '.theory_test', function(e){
            jQuery('.theory_test_input').show();
        });
        jQuery(document).on('click', '.no_teacher_show', function(e){
            jQuery('.no_teacher').show();
        });
        jQuery(document).on('click', '.other_teacher_show', function(e){
            jQuery('.other_teacher').show();
        });

        jQuery(document).on('click', '.error_handed', function(e){
            jQuery('.display_error_handed').show();
        });

        jQuery(document).on('click', '.error_handed_hide', function(e){
            jQuery('.display_error_handed').hide();
        });

        jQuery( "#frm_add_Booking" ).submit(function(  ) {
            var validation_data = validate();
            if(validation_data){
                jQuery.ajax({
                    url:'<?php echo $this->Html->url(array('controller'=>'adminbookings','action'=>'create')); ?>',
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
                            if(typeof(response.balance) != 'undefined'){
								
                                if(response.balance > 0){    
                                jQuery("#success-add").addClass('error-add');
                                jQuery("#success-add").find('.txt_sucess').append('<span>Elevens saldo er herefter '+response.balance+'</span>');
                                }
                                else{
                                jQuery("#success-add").find('.txt_sucess').append('<span>Elevens saldo er herefter '+response.balance+'</span>');
                                }                                
                            }
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
                            jQuery('.success-error-msg').html( response.message ).show();
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
            }
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
		
		jQuery(document).on('click', '.tmp-rana', function(e){
			// alert("OKAY 2");	
		});

        jQuery(document).on('click touchstart', '.view-booking-approval', function(e){
			
			// alert("OK");
			
            var $id     = jQuery(this).attr('data-id');
            var $student_id     = jQuery(this).attr('data-student_id');
            var $booking_type  = jQuery(this).attr('data-booking_type');
            var $color  = jQuery(this).attr('data-color');
            var $status  = jQuery(this).attr('data-status');

            jQuery( "#booking-approval" ).dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true
            }).prev(".ui-widget-header").attr('style', 'background : #' + $color + ' !important; border: 1px solid #' + $color + ' !important').find('.ui-dialog-title').html($booking_type);

            jQuery( "#booking-approval" ).find('.footer button').css('border', '1px solid #' + $color);
            jQuery( "#booking-approval" ).find('input[name="booking_id"]').val($id);
            jQuery( "#booking-approval" ).find('input[name="student_id"]').val($student_id);

            jQuery( "#booking-approval" ).find('.hide-me').hide();

            jQuery( "#booking-approval" ).find('.date').html( jQuery(this).attr('data-date') );
            jQuery( "#booking-approval" ).find('.time').html( jQuery(this).find('.book-time').text() );
            
            if( $booking_type == 'Køretime' ||  $booking_type == 'Køreprøve' ){
                jQuery( "#booking-approval" ).find('.student-name').html( jQuery(this).attr('data-student_name') ).show();
                jQuery( "#booking-approval" ).find('.student-number').html( jQuery(this).attr('data-student_number') ).show();
            } else if( $booking_type == 'Teori') {
                jQuery( "#booking-approval" ).find('.city-name').html( jQuery(this).attr('data-city_name') ).show();
            }

			jQuery('.noteEditable').hide();
            var $note = jQuery(this).find('.view-note').text();
            if( $note != '' && $note != undefined ) {
                jQuery( "#booking-approval" ).find('.note').html( $note ).show();
                var NoteText = $note.replace("Note:", "");
                NoteText = jQuery.trim(NoteText);
                jQuery('.noteEditable .textNotes').val(NoteText);
            }

            if( $booking_type == 'Teori' ){
                jQuery('#frm-booking-approval').find('.app-2').hide();
                jQuery('#frm-booking-approval').find('.app-4').hide();
                if($status == 'approved'){
                    jQuery('#frm-booking-approval').find('.app-1').hide();
                }
            } else if( $booking_type == 'Privat' ){
                jQuery('#frm-booking-approval').find('.app-2').hide();
                jQuery('#frm-booking-approval').find('.app-1').hide();
                jQuery('#frm-booking-approval').find('.app-4').hide();
                jQuery('.noteEditable').show();
                jQuery("#booking-approval").find('.note').html('');
            } else {
                jQuery('#frm-booking-approval').find('.app-4').hide();
                jQuery('#frm-booking-approval').find('.app-1').show();
                jQuery('#frm-booking-approval').find('.app-2').show();
                jQuery('#frm-booking-approval').find('.app-3').show();
                if($booking_type == 'Køretime'){
                    jQuery('#frm-booking-approval').find('.app-4').show();
                }
            }


            if($status != 'pending'){
                jQuery('#frm-booking-approval').find('.app-1').hide();
                jQuery('#frm-booking-approval').find('.app-2').hide();
                jQuery('#frm-booking-approval').find('.app-3').hide();
                jQuery('#frm-booking-approval').find('.app-4').hide();
            }
            
        });

        jQuery(document).on('click', '.btn_booking_approval', function(e){
            var $value = parseInt( jQuery(this).val());
            if(jQuery(this).val() == "update-note")
            {
                jQuery.ajax({
                        url:'<?php echo $this->Html->url(array('controller'=>'adminbookings','action'=>'updateBookingStatus')); ?>',
                        data: {booking_id: jQuery("#frm-booking-approval input[name='booking_id']").val(),note: jQuery(".textNotes").val(),action: 'update_appointment'},
                        type:'post',
                        dataType:'json',
                        beforeSend:function(){
                            jQuery('#frm-booking-approval').find('.loading').show();
                        },
                        success: function(response){
                            if(response.status == 1)
                            {
                                jQuery("#booking-approval").dialog('close');
                                $("#success-update-note" ).dialog({
                                    resizable: false,
                                    height: "auto",
                                    width: 350,
                                    modal: true,
                                }).prev(".ui-dialog-titlebar").remove();                                    
                            }
                        },
                        complete:function(){
                            jQuery('#frm-booking-approval').find('.loading').hide();
                        }
                    });
                
                    return false;
            }

            var $title = jQuery(this).closest('.ui-dialog').find('.ui-widget-header').find('.ui-dialog-title').text();

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
            } else if( $value == 1 ) {
                if($title == 'Køretime' || $title == 'Teori'){
                    ChangeStatus($title);
                }else{
                    $title = 'Køreprøven';
                    jQuery( "#booking-approval" ).dialog('close');
                    var buttons = [{
                                text: 'Bestået',
                                click: function() {
                                    
                                    jQuery('#drivingtestaproving').dialog('close');
                                    jQuery('#frm_action').val('5');
                                    ChangeStatus($title);
                                }
                            },
                            {
                                text: 'Dumpet',
                                click: function() {
                                    jQuery('#drivingtestaproving').dialog('close');
                                    jQuery('#frm_action').val('6');
                                    ChangeStatus($title);
                                }
                            }
                        ];
               
                    jQuery('#drivingtestaproving').dialog('option','buttons',buttons);
                    jQuery('#drivingtestaproving').dialog('open');
                    
                }

               
            } else {
                ChangeStatus($title);
            }
        });

        function ChangeStatus($title){
            jQuery.ajax({
                    url:'<?php echo $this->Html->url(array('controller'=>'adminbookings','action'=>'updateBookingStatus')); ?>',
                    data:jQuery('#frm-booking-approval').serialize(),
                    type:'post',
                    dataType:'json',
                    beforeSend:function(){
                        jQuery('.btn_booking_approval').attr("disabled", true);
                        jQuery('.btn_booking_approval').fadeTo('slow','0.5');
                        jQuery('#frm-booking-approval').find('.loading').show();
                    },
                    success: function(response){
                        if(response.status == 1){
                            var $value = jQuery('#frm-booking-approval').find('input[name="action"]').val();

                            if( $value == 3 ) {
                                jQuery( "#booking-delete" ).dialog('close');
                                $( "#success-delete" ).dialog({
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
                            } else if( $value == 4 ) {
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

                                if($title == 'Teori'){
                                    $title = 'TEORI';
                                    jQuery(".TEORI").prop('value','LUK');
                                    jQuery(".TEORI").attr('value','LUK');
                                    jQuery(".TEORI").val('LUK');
                                }
                                jQuery( "#booking-approved" ).find('.title').html( $title + ' ER');
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

        jQuery('#drivingtestaproving').dialog({
            autoOpen        : false,
            modal           : true,
            width           : 400,
            close           : function(ev, ui) { 
                jQuery('#drivingtestaproving').dialog('close');
            }
        });

        jQuery("#show_teacher_cal").change(function(){
            var id = jQuery("#show_teacher_cal :selected").val();
            if(id == ''){
                window.location.href = "<?php echo $this->request->webroot . 'Adminbookings/listBookings' ; ?>";
            }else{
                window.location.href = "<?php echo $this->request->webroot . 'Adminbookings/listBookings?id=' ; ?>"+id;
            }
        });
        jQuery(".close_dialog").click(function(){
            var dialog_name = jQuery(this).data(dialog_name);
            $("."+dialog_name.dialog_name).dialog("close");
            $("#"+dialog_name.dialog_name).dialog("close");
        });
        jQuery(".book_ny_tid").click(function(){
            var book_ny_tid = jQuery(this).val();
            if(book_ny_tid == 'BOOK NY TID TIL DENNE ELEV'){
                jQuery(this).attr('id',"test");
                jQuery( ".Køretime" ).show();

                var student_id = $("#frm-booking-approval").find('.student_id').val();
                $("#frm_add_Booking").find('#student_id').val(student_id);
                var name_of_student = $("#frm-booking-approval").find('.student-name').text();
                var book_start = $("#frm-booking-approval").find('.time').text();
                var book_start = book_start.split(" - ");
                var book_start = book_start[0].split(":");
                var book_start = book_start[0];
                var end_time = (Number(book_start)+1);
                var book_date   = $("#frm-booking-approval").find('.date').text();
                var book_date = book_date.split("/").reverse().join("-");
                var todate = new Date(book_date);
                var dd1 = todate.getDate()+1;
                var mm1 = todate.getMonth()+1; //January is 0!
                var yyyy1 = todate.getFullYear();
                if(dd1 < 10){
                    dd1 = '0'+ dd1;
                }
                if(mm1 < 10){
                    mm1 = '0' + mm1;
                }
                var book_date =  dd1 + '-' + mm1 + '-' +yyyy1 ;
                jQuery( ".booking-approval" ).remove();

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
                $( ".Køretime_click" ).trigger( "click" );
                jQuery("#name_of_student").val(name_of_student);
                jQuery("#name_of_student").focus();
            }
        });      
        function formatDate(date) {
             var d = new Date(date),
                 month = '' + (d.getMonth() + 1),
                 day = '' + d.getDate(),
                 year = d.getFullYear();

             if (month.length < 2) month = '0' + month;
             if (day.length < 2) day = '0' + day;

             return [day, month, year].join('-');
        }

        var dateToday = formatDate(new Date());

        jQuery('.datepicker_for_booking').datepicker({
            minDate     : dateToday,
            startDate     : dateToday,
            todayHighlight: true,
            format      : 'dd-mm-yyyy',
            // startDate   : '-50y',
            autoclose   : true,
            orientation : 'auto bottom',
        });
    });      
</script>

<div id="drivingtestaproving" style="display: none;" title="bestået">
    har eleven bestået
</div>

<div class="booking-approval" id="booking-approval">
    <form id="frm-booking-approval" action="" method="post">
        <input type="hidden" name="booking_id" value="0">
        <input type="hidden" name="student_id" class="student_id" value="0">
        <input type="hidden" name="action" id="frm_action" value="0">
        <div class="modal-body" style="padding: 0 0 15px 0;">
            <div class="loading" ><?php  echo $this->Html->image("loader.gif");?></div>
            <h3 class="hide-me student-name"></h3>
            <h5 class="hide-me student-number"></h5>
            <h3 class="hide-me city-name"></h3>
            <p>Mandag. <span class="date"></span> <span class="time"></span></p>
            <hr />
            <p class="hide-me note"></p>
            <div class="noteEditable">
                <input type="text" class="textNotes form-control" placeholder="<?php echo __('Notes'); ?>" />
                <button type="button" value="update-note" id="updatePrivateNote" class="btn_booking_approval">
                    <?php echo __('Update'); ?>                        
                </button>    
            </div>
        </div>

        <div class="footer text-center">
            <button type="button" value="3" class="app-3 btn_booking_approval">SLET</button>
            <button type="button" value="2" class="app-2 btn_booking_approval">UDEBLEVET</button>			
            <button type="button" value="1" class="app-1 btn_booking_approval">GODKEND</button>		
            <!-- <button type="button" value="4" class="app-4 btn_booking_approval">BESTÅET</button> -->
        </div>
    </form>
</div>

<div class="booking-approval  text-center" id="booking-approved" style="background: #25c525; padding: 25px;">
    <a class="ui-dialog-titlebar-close ui-corner-all ui-state-hover" onclick="location.reload();" style="top: 10px;right: 0;" role="button"><span data-dialog_name="booking-approved"  class="ui-icon close_dialog ui-icon-closethick"></span></a>
    <div class="">
        <div class="txt_sucess">
            <span class="title">Køreprøven er</span><br /><span>godkendt</span>
        </div>
        <!-- <div>
            <input type="button" value="BOOK NY TID TIL DENNE ELEV" id="btn_success" class="btn_sucess TEORI book_ny_tid" style="width: auto !important;">
        </div> -->

    </div>
</div>

<div class="booking-approval  text-center" id="booking-unapproved" style="background: #cc0000; padding: 25px;">
    <a class="ui-dialog-titlebar-close ui-corner-all ui-state-hover" style="top: 10px;right: 0;" role="button"><span data-dialog_name="booking-unapproved"  class="ui-icon close_dialog ui-icon-closethick"></span></a>
    <div class="">
        <div class="txt_sucess">
            BEKRÆFT AT ELEVEN ER<span>UDEBLEVET</span>
        </div>
        <div>
            <button type="button" value="BEKRAEFT" class="btn_booking_approval" style="background-color: #fff">BEKRÆFT</button>
        </div>
    </div>
</div>

<div class="booking-approval text-center" id="booking-delete" style="background: #cc0000; padding: 25px;">
    <a class="ui-dialog-titlebar-close ui-corner-all ui-state-hover" style="top: 10px;right: 0;" role="button"><span data-dialog_name="booking-delete"  class="ui-icon close_dialog ui-icon-closethick"></span></a>
    <div class="">
        <div class="txt_sucess">
            BEKRÆFT AT BOOKINGEN SKAL<span>SLETTES</span>
        </div>
        <div>
            <button type="button" value="SLET" class="btn_booking_approval" style="background-color: #fff">BEKRÆFT</button>
        </div>
    </div>
</div>

<div class="booking-approval text-center" id="booking-negative-confirm" style="background: #cc0000; padding: 25px;">
    <a class="ui-dialog-titlebar-close ui-corner-all ui-state-hover" style="top: 10px;right: 0;" role="button"><span data-dialog_name="booking-negative-confirm"  class="ui-icon close_dialog ui-icon-closethick"></span></a>
    <div class="">
        <div class="txt_sucess" style="margin-bottom: 20px;line-height: 30px;">
            MÅ KUN BENYTTES I NØDSTILFÆLDE YDELSER SKAL ALTID BETALES FORUD
        </div>
        <div>
            <button type="button" value="1" class="btn-booking-negative-approval" style="background-color: #fff">GENNEMFØR BOOKING</button>
        </div>
    </div>
</div>

<div class="success-add" id="success-update-note">
    <div class="">
        <div class="txt_sucess">
            <span>Opdateret</span>
        </div>
        <div>
            <input type="button" value="LUK" id="btn_success" class="btn_sucess">
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
<div class="success-add" id="success-delete">
    <div class="">
        <div class="txt_sucess">
            <span>Tiden er slettet</span>
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
        <input type="button" class="btn_add Køretime_click" value="Køretime" type_booking="Køretime" btn_caption = "TILFØJ Køretime"> 
        <input type="button" class="btn_add" value="Teori" type_booking="Teori" btn_caption = "TILFØJ Teori "> 
        <input type="button" class="btn_add" value="Køreprøven" type_booking="Køreprøve" btn_caption = "TILFØJ Køreprøve"> 
        <input type="button" class="btn_add" value="Privat" type_booking="Privat" btn_caption = "TILFØJ Privat"> 
    </p>

    <div id="add_data_frm" style="position:relative;">
        <div class="loader" ><?php  echo $this->Html->image("loader.gif");?></div> 
        <form id="frm_add_Booking">
            <input type="hidden" id="student_id" name="student_id" value="Køretime">
            <input type="hidden" id="add_type" name="add_type" value="Køretime">
            <input type="hidden" id="set_type" name="set_type" value="2">
            <input type="hidden" id="no_of_booking" name="no_of_booking" value="0">            
            <!-- <input type="hidden" id="book_date" name="book_date"> -->
            <input type="hidden" id="neg_overwrite" name="neg_overwrite" value="0">

            <div class="row-fluid"> 
                <div class="margin_bottom5 Køretime Køreprøve booking_fields">
                    <input type="text" name="name_of_student" id="name_of_student" value="" class="studentIdAutoSuggest padder span12 ">
                    <div class="error_msg name_of_student"><?php echo __("This field is required.");?></div>
                </div>
                <div class="margin_bottom5 recovery_type booking_fields">
                    <select name="recovery_type" id="recovery_type" class="span12 ">
                        <option value="">Vælg</option>
                        <option value="1200">Generhervelse 2200 kr.</option>
                        <option value="0">Rutinetimer - Løs timesalg</option>
                        <option value="500">Særlig køreundervisning</option>
                    </select>
                    <div class="error_msg"><?php echo __("This field is required.");?></div>
                </div>
                <div class="margin_bottom5 Firstaid_input booking_fields" style="display: none;">
                    <label>Har eleven afleveret førstehjælp og lægeerklæring?</label>
                    <br>
                    <input type="radio" name="handed_firstaid_papirs" class="error_handed_hide handed_firstaid_papirs_ja" value="1" > Ja 
                    <input type="radio" name="handed_firstaid_papirs" class="error_handed handed_firstaid_papirs_nej" value="0"> Nej
                    <div class="error_msg handed_firstaid_papirs"><?php echo __("This field is required.");?></div>
                </div>
                <div class="margin_bottom5 booking_fields error_msg display_error_handed">
                    Overvej at stoppe kørsel med elev, indtil papirer er afleveret.
                </div>
                <div class="margin_bottom5 theory_test_input booking_fields" style="display: none;">
                    <label>Har eleven været til teoriprøve?</label>
                    <br>
                    <input type="radio" name="theory_test_passed" class="theory_test_passed_ja" value="1" > Ja 
                    <input type="radio" name="theory_test_passed" class="theory_test_passed_nej" value="0"> Nej
                    <div class="error_msg theory_test_passed"><?php echo __("This field is required.");?></div>
                </div>
                <div class="margin_bottom5 no_teacher booking_fields">
                    <label>Vil du tildele den studerende til din konto?</label>
                    <br>
                    <input type="radio" name="teacher_id" class="no_teacher_ja" value="1" > Ja 
                    <input type="radio" name="teacher_id" class="no_teacher_nej" value="0"> Nej
                    <div class="error_msg no_teacher"><?php echo __("This field is required.");?></div>
                </div>
                <div class="margin_bottom5 other_teacher booking_fields">
                    <label>Den studerende har <span class="other_teacher_name"></span> som lærer. Vil du tildele den studerende til din konto?</label>
                    <br>
                    <input type="radio" name="teacher_id" class="other_teacher_ja" value="1" > Ja 
                    <input type="radio" name="teacher_id" class="other_teacher_nej" value="0"> Nej
                    <div class="error_msg other_teacher"><?php echo __("This field is required.");?></div>
                </div>
                <div class="margin_bottom5 Køretime Køreprøve Teori Privat booking_fields">
                    <input type="text" name="book_date" id="book_date" value="" class="book_date datepicker_for_booking padder span12 ">
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
                    
                    <a style="margin-top:15px"  href="<?php echo $this->request->webroot . 'Adminbookings/listBookings?calview=yes&m=' .$previous_month; ?>&y=<?php echo $previous_year?>" class="box_ic fa fa-angle-left " ></a>
                    <a href="<?php echo $this->request->webroot . 'Adminbookings/listBookings?calview=yes&m=' .$next_month; ?>&y=<?php echo $next_year?>"  class="  box_ic fa fa-angle-right "></a>
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
                            <?php 
                                $teacher_id = '';
                                if($this->params['url']['id'] != ''){
                                    $teacher_id = '&id='.$this->params['url']['id'];
                                }
                                for($i = 1; $i <= $daysInMonth; $i++): ?>
                               
                                    <td <?php if($day == $i){ echo "class='active_day'";} ?>>
                                        <a href="<?php echo $this->request->webroot . 'Adminbookings/listBookings?date=' .$i; ?>-<?php echo $month?>-<?php echo $year.$teacher_id;?>"><?php echo $i ?></a>
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
                $teacher_id = '';
                if($currentUser['User']['role'] == 'admin'){ 
                    ?>
                        <select id="show_teacher_cal" >
                            <option value="">Vælg Lærer</option>
                            <?php 
                                $teacher_id = '';
                                foreach ($teachers_list as $key => $teacher) { 
                                $selected = '';
                                if($this->params['url']['id'] == $teacher['id']){
                                    $teacher_id = '&id='.$this->params['url']['id'];
                                    $selected = 'selected';
                                }
                                echo "<option value='$teacher[id]'  $selected >$teacher[firstname] $teacher[lastname]</option>";
                                ?>
                            <?php } ?>
                        </select>
                <?php } ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        if(screen.width <= 1024){
                            var next_url = '<?php echo $this->request->webroot . 'Adminbookings/listBookings?date=' . date('d-m-Y', strtotime($next_date . ' +0 days')).$teacher_id; ?>';
                            var prev_url = '<?php echo $this->request->webroot . 'Adminbookings/listBookings?date=' . date('d-m-Y', strtotime($prev_date. ' +1 days')).$teacher_id; ?>';
                        }else{
                            var next_url = '<?php echo $this->request->webroot . 'Adminbookings/listBookings?date=' . date('d-m-Y', strtotime($next_date . ' +0 days')).$teacher_id; ?>';
                            var prev_url = '<?php echo $this->request->webroot . 'Adminbookings/listBookings?date=' . date('d-m-Y', strtotime($prev_date )).$teacher_id; ?>';
                        }
                        $('.next_url_set').attr('href',next_url);
                        $('.prev_url_set').attr('href',prev_url);
                    });
                </script>
                <a href="<?php echo $this->request->webroot . 'Adminbookings/listBookings?calview=yes'.$teacher_id ; ?>" class="fa  fa-3x cal_link " >Hop til dato</a>
                <a href="<?php echo $this->request->webroot . 'Adminbookings/listBookings?date=' . date('d-m-Y', strtotime($prev_date)).$teacher_id; ?>" class="box_ic fa fa-angle-left prev_url_set" ></a>
                <a href=""  class="  box_ic fa fa-angle-right next_url_set"></a>
            </div>

            <div class="booking-date-wrap">
                <div class="span6">
                    <div class="widget" style="overflow: hidden;">
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
                                        <?php
                                        $is_show = false; 
                                        if( strtotime($date) == strtotime($right_now_date) && strtotime($t['title']) >= strtotime($right_now_time) ) { 
                                            $is_show = true; 
                                        }else
                                         if(strtotime($date) > strtotime($right_now_date)){
                                            $is_show = true; 
                                        }

                                        if($t['title'] == ('00:00') || $t['title'] == ('01:00') || $t['title'] == ('02:00') || $t['title'] == ('03:00')){
                                            $is_show = false; 
                                            if($t['title'] == ('00:00')){
                                                $title = $t['title']." <span class='next_date'>".date('d M, Y',strtotime($next_date))."</span>";
                                            }else{
                                                $title = $t['title'];
                                            }
                                        }else{
                                            $title = $t['title'];
                                        }

                                        if( $is_show == true ) { ?>
                                            <a href="javascript://" class="book_now" val="<?php echo $k;?>" book_date = "<?php echo date('d-m-Y', strtotime($date)); ?>"><?php echo $title;?></a>
                                        <?php } else if($t['title'] == ('00:00') || $t['title'] == ('01:00') || $t['title'] == ('02:00') ) { ?>
                                            <a href="javascript://" class="book_now" val="<?php echo $k;?>" book_date = "<?php echo date('d-m-Y', strtotime($date.'+1 day')); ?>"><?php echo $title;?></a>
                                        <?php } else { ?>
                                            <?php echo $title;?>
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
                                                    $cst = strtotime( $t['st']  );
                                                    $cet = strtotime( $t['et']  );
                                                    $bst = strtotime( $current['start_time']  );
                                                    
                                                    $bet = strtotime($current['end_time'] );    

                                                    if( $bst >= $cst  && $bst < $cet ) {
                                                    // print_r($current);
                                                        $has = true;

                                                        if( $current['booking_type'] == 'Track' ) {
                                                            $class = 'booking-slot booking-'. $current['booking_type'];
                                                        } else {
                                                            $class = 'view-booking-approval booking-slot booking-'. $current['booking_type'];
                                                        }

                                                        foreach ($minutes as $min) {
                                                            $s_tmp_time = strtotime(  $k . ':' . $min );
                                                            $end_min = $min + 15;
                                                            $end_hr = $k;
                                                            if($end_min == '60'){
                                                                $end_min = '00';
                                                                $end_hr = $k+1;
                                                            }
                                                            $e_tmp_time = strtotime(  $end_hr . ':' . $end_min );
                                                            if( $bst >= $s_tmp_time && $bst < $e_tmp_time ) {
                                                                $height = ceil( ( $bet - $bst ) / 60 );
                                                                $height = ($height == 1230 || $height == -1230) ? '230' : $height;
                                                                $booking_type = $current['booking_type'];
                                                                $booking_note = $current['note'];
                                                                $style_track = '';
                                                                if( $height > '0' ) {
                                                                    $style_track = 'float:left';
                                                                }
                                                                if( $height >= '90' ) {
                                                                    $mobile_hide = '';
                                                                }else{
                                                                    $mobile_hide = 'hide-mobile';
                                                                }
                                                                echo '<div class="tmp-rana '. $k . '-' . $min .'">';
                                                                    echo '<div
                                                                        data-id="'. $current['id'] .'"
                                                                        data-student_id="'. $current['student_id'] .'"
                                                                        data-date="'. date( 'd/m/Y', strtotime($current['date']) ) .'"
                                                                        data-booking_type="'. $current['booking_type'] .'"
                                                                        data-status="'. @$current['status'] .'"
                                                                        data-color="'. @$colors[$current['booking_type']] .'" 
                                                                        data-student_name="'. @$current['student_name'] .'" 
                                                                        data-student_number="'. @$current['student_number'] .'" 
                                                                        data-city_name="'. @$cities[$current['city_id']] .'"
                                                                        class="'. $class .'"
                                                                        style="min-height:'. $height .'px;'.$style_track.' ">';
                                                                        $icon = '';

                                                                        if(isset($current['status']) && ($current['status'] == 'approved') || $current['status'] == 'passed' || $current['status'] == 'unapproved' || $next['status'] == 'dumped'){
                                                                            $icon =  '  <i class="fa fa-check-circle"></i>';
                                                                        }

                                                                    echo '<span class="booking-type">' . $current['booking_type'] .$icon . '</span>';

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
                                                                            echo '<span class="book-student-name">'.$current['student_name'].' - '.$current['student_number'].'</span>'; 
                                                                            echo '<span class="book-student-addr '.$mobile_hide.'">Adresse: '.$current['student_address'].'</span>'; 
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
                    <div class="widget" style="overflow: hidden;">
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
                                        <?php 
                                         $is_show = false; 
                                        if( strtotime($next_date) == strtotime($right_now_date) && strtotime($t['title']) >= strtotime($right_now_time) ) { 
                                            $is_show = true; 
                                        }else if(strtotime($next_date) > strtotime($right_now_date)){
                                            $is_show = true; 
                                        }

                                        
                                        if($t['title'] == ('00:00') || $t['title'] == ('01:00') || $t['title'] == ('02:00') || $t['title'] == ('03:00')){
                                            $is_show = false; 
                                            if($t['title'] == ('00:00')){
                                                $title = $t['title']." <span class='next_date'>".date('d M, Y',strtotime($next_date.'+1 day'))."</span>";
                                            }else{
                                                $title = $t['title'];
                                            }
                                        }else{
                                            $title = $t['title'];
                                        }


                                        if( $is_show == true ) { ?>
                                            <a href="javascript://" class="book_now" val="<?php echo $k;?>" book_date = "<?php echo date('d-m-Y', strtotime($next_date)); ?>"><?php echo $title;?></a>
                                        <?php } else if($t['title'] == ('00:00') || $t['title'] == ('01:00') || $t['title'] == ('02:00') ) { ?>
                                            <a href="javascript://" class="book_now" val="<?php echo $k;?>" book_date = "<?php echo date('d-m-Y', strtotime($next_date.'+1 day')); ?>"><?php echo $title;?></a>
                                        <?php } else { ?>
                                            <?php echo $title;?>
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
                                                    // print_r($next);
                                                    $cst = strtotime($t['st'] );
                                                    $cet = strtotime($t['et'] );
                                                    $bst = strtotime($next['start_time'] );

                                                    $bet = strtotime($next['end_time'] );    
                                                    
                                                    if( $bst >= $cst && $bst < $cet ) {
                                                        $has = true;

                                                        if( $next['booking_type'] == 'Track' ) {
                                                            $class = 'booking-slot booking-'. $next['booking_type'];
                                                        } else {
                                                            $class = 'view-booking-approval booking-slot booking-'. $next['booking_type'];
                                                        }

                                                        foreach ($minutes as $min) {
                                                            $s_tmp_time = strtotime($k . ':' . $min );
                                                            $end_min = $min + 15;
                                                            $end_hr = $k;
                                                            if($end_min == '60'){
                                                                $end_min = '00';
                                                                $end_hr = $k+1;
                                                            }
                                                            $e_tmp_time = strtotime($end_hr . ':' . $end_min );
                                                            if( $bst >= $s_tmp_time && $bst < $e_tmp_time ) {
                                                                $height = ceil( ( $bet - $bst ) / 60 );
                                                                $height = ($height == 1230 || $height == -1230) ? '230' : $height;
                                                                $booking_type = $next['booking_type'];
                                                                $booking_note = $next['note'];
                                                                $style_track = '';
                                                                if( $height > '0' ) {
                                                                    $style_track = 'float:left';
                                                                }
                                                                if( $height >= '90' ) {
                                                                    $mobile_hide = '';
                                                                }else{
                                                                    $mobile_hide = 'hide-mobile';
                                                                }
                                                                echo '<div class="tmp-rana '. $k . '-' . $min .'">';
                                                                    echo '<div
                                                                        data-id="'. $next['id'] .'"
                                                                        data-student_id="'. $next['student_id'] .'"
                                                                        data-date="'. date( 'd/m/Y', strtotime($next['date']) ) .'"
                                                                        data-booking_type="'. $next['booking_type'] .'"
                                                                        data-status="'. @$next['status'] .'"
                                                                        data-color="'. @$colors[$next['booking_type']] .'" 
                                                                        data-student_name="'. @$next['student_name'] .'" 
                                                                        data-student_number="'. @$next['student_number'] .'" 
                                                                        data-city_name="'. @$cities[$next['city_id']] .'"
                                                                        class="'. $class .'"
                                                                        style="min-height:'. $height .'px;'.$style_track.'">';
                                                                        $icon = '';
                                                                        if(isset($next['status']) &&( $next['status'] == 'approved') || $next['status'] == 'passed' || $next['status'] == 'unapproved' || $next['status'] == 'dumped'){
                                                                            $icon =  '  <i class="fa fa-check-circle"></i>';
                                                                        }
                                                                    echo '<span class="booking-type">' . $next['booking_type'] . $icon .  '</span>';

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
                                                                            echo '<span class="book-student-name">'.$next['student_name'].' - '.$next['student_number'].'</span>'; 
                                                                            echo '<span class="book-student-addr '.$mobile_hide.'">Adresse: '.$next['student_address'].'</span>'; 
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