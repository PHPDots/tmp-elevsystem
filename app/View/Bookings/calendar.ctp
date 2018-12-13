<?php 

$this->request->query['area']   = isset($this->request->query['area'])?$this->request->query['area']:'';
$this->request->query['date']   = isset($this->request->query['date'])?$this->request->query['date']:date('Y-m-d',time());

$this->append('script'); ?>

<script type="text/javascript">
    var GlobElement;
    var months              = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var areaWiseTimeSlots   = <?php echo json_encode($areaTimeSlot); ?>;
    var tracksList          = <?php echo json_encode($tracks); ?>;
    var offset              = [];
    var bookingObject       = [];
    var mergedObject        = [];
    var teacherObject       = null;
    var courseList          = <?php echo json_encode($courses); ?>;
    
    function generateTimeSlots(element,object,edit) {
        var temp = object.time;
        var i;
        var html = '';
        var d = new Date();
        var date    = d.dateFormat('d.m.Y');
        var j = 0;
        var m = 0;
        for(i = 0;i < temp.length;i++) {
            if(date == object.date) {
                //ignore the timeSlots that has passed
                if(temp[i].substring(0, 5) >= '<?php echo date('H:i'); ?>') {
                    if(temp[i] == object.selected || (object.selected == '' && j == 0)) {
                        html   += '<option value='+temp[i]+' selected>'+temp[i]+'</option>';
                    } else {
                        html   += '<option value='+temp[i]+'>'+temp[i]+'</option>';
                    }
                    j++;
                }
            } else {
                if(temp[i] == object.selected || (object.selected == '' && m == 0)) {
                    html   += '<option value='+temp[i]+' selected>'+temp[i]+'</option>';
                } else {
                    html   += '<option value='+temp[i]+'>'+temp[i]+'</option>';
                }
                m++;
            }
        }
        element.parent().find('.bookingTrack').show();
        
        if(element.parent().find('.trackCheckbox').attr('edit_track') == 'yes') {
            element.parent().find('.timeSlotText').show();
            element.parent().find('.timeSlot').hide();
        } else {
            element.parent().find('.timeSlot').show();
        }
        
        element.parent().find('.timeSlotSelectCt').html(html).chosen();
        
        element.parent().find('.studentDiv').show();
    }
    
    function generateTrackDetails(area) {
        var tracks  = [];
        eval('tracks = tracksList.'+area);
        var courses = [];
        eval('courses= courseList.'+area);
        var modifiedHtml = '';
        
        jQuery.each(tracks,function(index,value) {
            var role         = '<?php echo $currentUser['User']['role']; ?>';
            var studentValue = '';
            var html = '';

            html += '<div class="span12 no-margin trackDetails" id="trackDetails_'+index+'">';
                html += '<div class="span2 no-margin trackCheckboxCt">';
                    html += '<input type="checkbox" name="data[BookingTrack]['+index+'][track_id]" id="chk_'+index+'" value="'+index+'" class="uniform trackCheckbox"/>';
                    html += '<label for="chk_'+index+'">'+value+'</label>';
                html += '</div>';
            
                html += '<div class="bookingTrack">';
                    html += '<div class="timeSlot span2" style="display:none;">';
                        html += '<select name="data[BookingTrack][%i%][time_slot][]" class="timeSlotSelectCt span12" multiple id="timeSlot_'+index+'">';
                        html += '</select>';
                    html += '</div>';
                    
                    html += '<div class="timeSlotText span2" style="display:none;margin-left:0">';
                        html += '<input type="text" class="span12" disabled />';
                    html += '</div>';

                    html += '<div class="studentDiv span8" id="studentDiv'+index+'" style="display:none;">';
            
                        var studentNameStyle = '<?php ($currentUser['User']['role'] != 'external_teacher') ? '' : 'display:none;'; ?>';
                    
                        html += '<input type="text" class="studentIdAutoSuggest span5 studentDetails pull-left"  placeHolder="<?php echo __('Student Name/ Username'); ?>" style="'+studentNameStyle+'"/>';
            
                        html += '<input type="hidden" name="data[BookingTrack][%i%][student_id]" class="studentId" id="studentId_'+index+'" value="'+studentValue+'"/>';
            
                        html += '<div class="span3">';
                            var checked       = '<?php echo ($currentUser['User']['role'] == 'external_teacher') ? 'checked="checked"' : '' ?>';
                            var style         = 'display:none';
                            var checkboxStyle = '<?php echo ($currentUser['User']['role'] == 'external_teacher') ? 'display:none' : '' ?>';
                
                            html += '<input type="checkbox" name="data[BookingTrack]['+index+'][unknown]" id="chk_unknown_'+index+'" value="1" class="uniform unknownStudent" '+checked+' style="'+checkboxStyle+'"/>';
                            html += '<label for="chk_unknown_'+index+'" style="'+checkboxStyle+'"><?php echo __('Unknown Student'); ?></label>';
                        html += '</div>';
                        /*
                        html += '<div class="span2 no-margin trackCheckboxCt">';
                            html += '<input type="checkbox" name="data[BookingTrack]['+index+'][send_sms]" id="send_sms_chk_'+index+'" value="1" class="uniform"/>';
                            html += '<label for="send_sms_chk_'+index+'">Send SMS</label>';
                        html += '</div>';
                        */
                    html += '</div>';
                html += '</div>';

            html += '</div>';
            //html += '</div>';

            html += '<div class="span12 no-margin" id="chk_unknown_'+index+'_ct" style="'+style+'">';
                html += '<div class="span2 no-margin">';
                html += '</div>';

                html += '<div class="span3 no-margin">';
                    html += '<input type="text" class="span12 unknown_student_name unknown_field" name="data[BookingTrack][%i%][name]" placeHolder="<?php echo __('Enter Student Name'); ?>"/>';
                    html += '<div class="error-message" id="txt_booking_name_%i%"></div>';
                html += '</div>';

                html += '<div class="span3">';
                    html += '<input type="text" class="span12 no-margin unknown_student_phone unknown_field" name="data[BookingTrack][%i%][phone]" placeHolder="<?php echo __('Enter Student Mobile Number'); ?>"/>';
                    html += '<div class="error-message" id="txt_booking_phone_%i%"></div>';
                html += '</div>';
            html += '</div>';

            modifiedHtml += html.replace(/\%i\%/g,index);
        });
        
        return modifiedHtml;
    }
    
    function dayEvent(area,date,week) {
        var teacherId = '<?php echo (isset($this->request->query['teacher_booking_detail']) && !empty($this->request->query['teacher_booking_detail'])) ? $this->request->query['teacher_booking_detail'] : NULL ?>';
        jQuery.ajax({
            url         : '<?php echo $this->Html->url(array('controller'   => 'bookings','action'  => 'getBookings')); ?>',
            data        : (teacherId != '') ? 'area='+area+'&date='+date+'&week='+week+'&teacher_booking_detail='+teacherId : 'area='+area+'&date='+date+'&week='+week+'&time=<?php echo date('YmdHis'); ?>',
            type        : 'get',
            dataType    : 'html',
            beforeSend  : function() {
                jQuery('#detailLoader').show();
                jQuery('#bookingsCt').hide();
            },
            success     : function(data) {
                jQuery('#bookingsCt').html('');
                jQuery('#bookingsCt').html(data);
                jQuery('#booking_info').show();
                jQuery('#bookingsCt').find('.bookingTitle').removeClass().addClass('bookingTitle area-'+area+'-color');
                setTimeout(function() {
                    jQuery.scrollTo('#bookingsCt','slow');
                },200); // scroll down after 0.2 seconds
            },
            complete    : function() {
                jQuery('#detailLoader').hide();
                jQuery('#bookingsCt').show();
            }
        });
    }
    
    jQuery(document).ready(function(){
        
        jQuery('.calendarCt,.detailsLoading,.bookingForm,.hidenField,.longDescription,.studentDiv,.bookingTeacher').hide();
        
        // Area Object For Radio button
        jQuery('.areaObject').click(function() {
            if(jQuery(this).is(':checked')) {
                var area    = jQuery(this).val();
                var date    = (typeof jQuery('.booking_calendar_table').attr('date') != 'undefined') ? jQuery('.booking_calendar_table').attr('date') : '<?php echo $currDate; ?>';
                var week    = (typeof jQuery('.booking_calendar_table').attr('week') != 'undefined') ? jQuery('.booking_calendar_table').attr('week') : '<?php echo $weekNo; ?>';
                var year    = (typeof jQuery('.booking_calendar_table').attr('year') != 'undefined') ? jQuery('.booking_calendar_table').attr('year') : '<?php echo $year; ?>';
                jQuery('.calendarCt').hide();
                jQuery('#bookingForm').hide();
                jQuery('#calendarList').show();
                <?php if(isset($this->request->query['area']) && !empty($this->request->query['area'])) { ?>
                    if(area == '<?php echo $this->request->query['area']; ?>') {
                        area = '<?php echo $this->request->query['area']; ?>';
                    }
                <?php } ?>                
                jQuery.ajax({
                    url         : '<?php echo $this->Html->url(array('controller'=>'bookings','action'=>'calendar')); ?>',
                    data        : 'area='+area+'&date='+date+'&week='+week+'&year='+year,
                    dataType    : 'html',
                    complete    : function() {
                        
                    },
                    beforeSend  : function() {
                        
                    },
                    success     : function(data) {
                        jQuery('#calendarList').html(data);
                        jQuery('td.today').first().trigger('click');
                    },
                    error        : function() {
                    }
                });
            } else {
                jQuery('#calendarList').hide();
            }
        });
        
        jQuery('.areaObject:checked').trigger('click');
        
        jQuery(document).delegate('.date_event','click',function() {
            var currEle = jQuery(this);
            jQuery('#bookingForm').hide();
            jQuery('#bookingError').hide();
            jQuery(this).closest("table").find("tr td").removeClass('shadow');
            jQuery(this).closest("table").find("tr th").removeClass('shadow');
            jQuery('.date_event').removeClass('selected_time_slot');
            jQuery(this).addClass('selected_time_slot');
            var columnNo = jQuery(this).index();
            jQuery('.booking_calendar_table').attr('date',jQuery(this).attr('slot-date'));
            var myDate = new Date(jQuery(this).attr('slot-date'));
            jQuery('.booking_calendar_table').attr('year',myDate.getFullYear());
            jQuery(this).closest('table').find('tr td').removeClass('today');
            jQuery(this).closest("table")
                .find("tr td:nth-child(" + (columnNo+1) + ")").addClass('shadow today');
            jQuery(this).closest("table")
                .find("tr th:nth-child(" + (columnNo+1) + ")").addClass('shadow');
            dayEvent(jQuery('.areaObject:checked').val(), currEle.attr('slot-date'), currEle.parents('table').attr('week'));
            
        });
        
        <?php if($this->request->query['date'] != date('Y-m-d',  time())) { ?>
            jQuery('td[data-date="<?php echo $this->request->query['date']; ?>"]').addClass('date-highlight');
        <?php } ?>
        
        jQuery(document).delegate('.bookingLink','click',function() {

            if(jQuery(this).hasClass('deactiveLink')) {
                return;
            }
           
            jQuery('#bookingError').hide();
            jQuery('#bookingForm').hide();
            
            //Get Attribute of Link
            var area        = jQuery(this).attr('area');
            var date        = jQuery(this).attr('date');
            var track       = jQuery(this).attr('track');
            var timeSlot    = jQuery(this).attr('time');
            var time        = [];
            var selected    = [];
            var reference   = jQuery(this).attr('reference');
            var element     = jQuery('#bookingForm');
            selected.push(jQuery(this).attr('time'));
            
            var newDate     = date.split('.');
            newDate         = new Date(newDate[2],newDate[1]-1,newDate[0]);
            var timestamp   = newDate.getTime();
            timestamp       = timestamp.toString().substring(0, 10);
            timestamp       = parseInt(timestamp);
            
            if(timestamp < <?php echo (strtotime(date('Y-m-d',time())) - 22000); ?>) {
                jQuery('#bookingError').show().html('<h5 class="error-message text-center"><?php echo __('You cannot make a booking for past date.'); ?></h5>');
                jQuery.scrollTo('#bookingError','slow');
                return;
            }
            var prevBookings = new Array();
            
            jQuery.each(jQuery('.bookingLink[track="'+track+'"]'),function() {
                if(typeof jQuery(this).attr('time') != "undefined") {
                    time.push(jQuery(this).attr('time'));
                }
            });
           
            jQuery('#BookingCalendarForm').attr('action','<?php echo $this->Html->url(array('controller' => 'Bookings','action' => 'add','?' => array('iframe' => (isset($this->request->query['iframe']) ? $this->request->query['iframe'] : 0)))); ?>');
            element.html(jQuery('#bookingAddForm').html()).show();
            jQuery.scrollTo('#bookingForm','slow');
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller' => 'bookings','action' => 'getTimeBookings')) ?>',
                type        : 'get',
                dataType    : 'json',
                data        : 'area='+area+'&date='+date+'&time='+timeSlot,
                beforeSend  : function() {
                    jQuery('#hide-on-same-booking-time-slot').show();
                },
                success     : function(data) {
                    if(data.do_edit != 'undefined'){
                        if(data.do_edit == 0){
                            track_lock();
                            return false;
                        }
                    }

                    var time_left = <?php echo $live_edit_time; ?>,
                    display = document.querySelector('#time');
                    startTimer(time_left, display);

                    var users = <?php echo json_encode($students); ?>;
                    jQuery.each(data,function(id,value) {
                        jQuery.each(value,function(index,booking) {
                            jQuery('.tracks').find('#trackDetails_'+index).find('#chk_'+index).attr('edit_track','yes').attr('checked','checked').trigger('click');
                            jQuery('.tracks').find('#trackDetails_'+index).find('.timeSlotText input').val(timeSlot);
                            jQuery('.tracks').find('#trackDetails_'+index).find('.studentDetails').val(users[booking.student_id]).attr('disabled',true);

                            if(booking.name != '' && booking.name != null) {
                                jQuery('.tracks').find('#trackDetails_'+index).find('.unknownStudent').attr('checked','checked').trigger('click');
                                jQuery('.tracks').find('#chk_unknown_'+index+'_ct').find('.unknown_student_name').val(booking.name).attr('disabled',true);
                                jQuery('.tracks').find('#chk_unknown_'+index+'_ct').find('.unknown_student_phone').val(booking.phone).attr('disabled',true);
                            }

                            jQuery('.tracks').find('#trackDetails_'+index).find('.trackCheckbox').attr('disabled',true);
                            jQuery('.tracks').find('#trackDetails_'+index).find('.unknownStudent').attr('disabled',true);
                            jQuery('.tracks').find('#trackDetails_'+index).find('#chk_'+index).parent().addClass('checked');
                            jQuery('.tracks').find('#trackDetails_'+index).find('#chk_'+index).attr('disabled','disabled');
                        });
                    });

                    if(data != ''){
                        jQuery('#hide-on-same-booking-time-slot').hide();
                    }
                }
            });
            element.find('.areaSelection').val(area);
            element.find('.date').val(date);
            
            if((reference != '') && (typeof reference != 'undefined') && (typeof reference != null)) {
                element.find('#reference').val(reference);
            }
            
            var dateFormat  = newDate.getDate()+'/'+months[newDate.getMonth()]+'-'+newDate.getFullYear();
            
            jQuery('#bookingForm').find('.widget-header').html('<h5><?php echo __('Booking Slot Of '); ?>'+area+'<?php echo __(' Area On '); ?>'+dateFormat+'  <span id="edit-time-details" style="padding-left:15px;">Editing will be closed in <span style="color:#c10000;" id="time"><?php echo gmdate("i:s", $live_edit_time); ?></span> minutes!</span></h5><h5 id="txt_booking_details" class="error-message reference"></h5>');
            
            if(time.length == 0) {
                element.parent().find('.bookingTrack').show().html('<label class="bookingError"><?php echo __('Sorry No Slots Are Free For Booking. '); ?></label>');
                return;
            }
            
            element.find('.tracks').show().find('.tracksSelection').html(generateTrackDetails(area));
            element.find('input[type="checkbox"], input[type="radio"]').uniform();
            
            jQuery.each(element.find('.tracksSelection').find('.trackCheckbox'),function() {
                if(jQuery(this).attr('id') == ('chk_'+track)) {
                    
                    jQuery(this).attr('checked','checked');
                    
                    jQuery(this).parent().addClass('checked');
                    var element  = jQuery(this).parents('.trackCheckboxCt');
                    var role     = '<?php echo $currentUser['User']['role']; ?>';
                    if(role == 'external_teacher') {
                        jQuery('#chk_unknown_'+track).attr('checked','checked');
                    }
                    var arguments  = {
                        "area"       : area,
                        "date"       : date,
                        "track"      : track,
                        "time"       : time,
                        "selected"   : selected
                    };
                    
                    generateTimeSlots(element, arguments);
                }
            });
            
            var teacherCourse   = [];
            eval('teacherCourse = courseList.'+area);
            
            var html    = "";
            html    += '<select name="data[Booking][course]" class="span6">';
            html    += '<option value=""><?php echo __('Select Course'); ?></option>';
            if(typeof teacherCourse != 'undefined') {
               jQuery.each(teacherCourse,function(i,val) {
                    var preselected = '';
                    if(val.pre_selected == 1) {
                        preselected = 'selected';
                    }
                    html += '<option value="'+i+'" '+preselected+'>'+val.name+'</option>';
               });
            }
            html    +=  '</select>';
            element.find('#teacherCourseList').html(html);
            <?php if($currentUser['User']['role']   == 'external_user') { ?>
                jQuery.each(jQuery('.unknownStudent'),function() {
                    jQuery(this).trigger('click');
                });
            <?php } ?>
            
            jQuery('.unknownStudent').each(function() {
                if(jQuery(this).is(':checked') && jQuery(this).parents('.trackDetails').find('.trackCheckbox').is(':checked')) {
                    jQuery(this).trigger('click');
                }
            });
        });
        
        jQuery(document).delegate('.trackCheckbox','click',function() {
            if(!jQuery(this).is(':checked')) {
                jQuery(this).addClass('hidden_div');
                jQuery(this).parents('.trackDetails').find('.bookingTrack').hide();
                jQuery('#chk_unknown_' + jQuery(this).val() + '_ct').hide();
                <?php if($currentUser['User']['role'] == 'external_teacher') { ?>
                if(!jQuery(this).parents('.trackDetails').find('.unknownStudent').is(':checked')) {
                    jQuery(this).parents('.trackDetails').find('.unknownStudent').trigger('click');
                }
                <?php } ?>
                if(jQuery('.trackDetails').find('.trackCheckbox').hasClass('external_teacher_selected')) {
                    if(!jQuery(this).parents('.trackDetails').find('.unknownStudent').is(':checked')) {
                        jQuery(this).parents('.trackDetails').find('.unknownStudent').trigger('click');
                    }
                } else {
                    <?php if($currentUser['User']['role'] != 'external_teacher') { ?>
                    jQuery(this).parents('.trackDetails').find('.unknownStudent').attr('checked',false);
                    <?php } ?>
                }
                return;
            }

            var id          = jQuery(this).attr('id');
            var date        = jQuery('#bookingForm').find('.date').val();
            var area        = jQuery('#bookingForm').find('.areaSelection').val();
            var track       = jQuery(this).val();
            var element     = jQuery(this).parents('.trackCheckboxCt');
            var time        = [];
            var selected    = [];
            
            if(!jQuery(this).hasClass('hidden_div')) {
                if((teacherObject != null) && (typeof teacherObject.role != 'undefined') && (teacherObject.role == 'external_teacher')) {
                    element.parent().find('.studentId').val(-1);
                }
                
                jQuery.each(jQuery('.bookingLink[track="'+track+'"]'),function() {
                    if(typeof jQuery(this).attr('time') != "undefined") {
                        time.push(jQuery(this).attr('time'));
                    }
                });
                jQuery.each(jQuery('.trackDetails').find('.trackCheckbox'),function() {
                    if((jQuery(this).is(':checked')) && (id != jQuery(this).attr('id'))) {
                        var element = jQuery(this).parents('.trackDetails').find('.timeSlotSelectCt');
                        var value   = element.val();
                        //18/11/2015
                        var index   = (value != null) ? jQuery.inArray(value[0], time) : '-1';
                        if(index == '-1') {
                            jQuery.each(jQuery('.bookingLink[track="'+track+'"]'),function() {
                                if(typeof jQuery(this).attr('time') != "undefined") {
                                    selected.push(jQuery(this).attr('time'));
                                    return false;
                                }
                            });
                            return false;
                        } else {
                            if(element.val() != 'null') {
                                var str         = element.val()+'';
                                var array       = str.split(',');
                                var selectedVal = (array.length > 1) ? array[0] : element.val();
                                selected.push(selectedVal);
                                return false;
                            }
                        }
                    }
                });
                if(time.length == 0) {
                    element.parent().find('.bookingTrack').show().html('<label class="bookingError"><?php echo __('Sorry No Slots Are Free For Booking. '); ?></label>');
                    return;
                }
                
                var arguments   = {
                    "area"      : area,
                    "date"      : date,
                    "track"     : track,
                    "time"      : time,
                    "selected"  : selected
                };
                generateTimeSlots(element, arguments);
                <?php if($currentUser['User']['role']  == 'external_teacher') { ?>
                    jQuery(this).parents('.trackDetails').find('.unknownStudent').trigger('click');
                <?php } ?>
                if(jQuery('.trackDetails').find('.trackCheckbox').hasClass('external_teacher_selected')) {
                    jQuery(this).parents('.trackDetails').find('.unknownStudent').attr('checked','checked');
                    jQuery(this).parents('.trackDetails').find('.unknownStudent').trigger('click');
                    jQuery(this).parents('.trackDetails').find('.unknownStudent').closest('.span3').hide();
                }
                
            } else {
                jQuery(this).parents('.trackDetails').find('.bookingTrack').show();
                jQuery(this).parents('.trackDetails').find('.unknownStudent').trigger('click');
                jQuery(this).removeClass('hidden_div');
            }
        });
        
        jQuery(document).delegate('#longDescriptionChk','click',function(){
            if(jQuery(this).is(':checked')) {
                jQuery('#longDescription').show();
            } else {
                jQuery('#longDescription').hide();
            }
        });
        
        // Edit Function
        jQuery(document).delegate('.bookingLinkName','click',function() {

            var area        = jQuery(this).attr('area');
            var date        = jQuery(this).attr('date');
            var timeSlot    = jQuery(this).attr('time');
            
            var bookingId   = jQuery(this).attr('item-id');
            jQuery('#bookingError').hide();
            <?php if($currentUser['User']['role'] == 'external_teacher') { ?>
                if(!jQuery(this).hasClass('logged_in_teacher')) {
                    return;
                }
            <?php } ?>
            var url = jQuery(this).attr('url')+'?iframe=<?php echo isset($this->request->query['iframe']) ? $this->request->query['iframe'] : 0; ?>';
            
            jQuery.ajax({
                url         : url,
                dataType    : 'html',
                success     : function(data) {
                    
                    if(data == "StopEditing"){
                        track_lock();
                        return false;
                    }
                    jQuery('#bookingForm').show().html(data);
                    jQuery('#BookingCalendarForm').attr('action',url);
                    jQuery.scrollTo('#bookingForm','slow').delay( 5000 );
                }
            });
            
            setTimeout(function() {
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller' => 'bookings','action' => 'getTimeBookings')) ?>/'+bookingId,
                type        : 'get',
                dataType    : 'json',
                data        : 'area='+area+'&date='+date+'&time='+timeSlot,
                success     : function(data) {
                    var users = <?php echo json_encode($students); ?>;
                    jQuery.each(data,function(id,value) {
                        jQuery.each(value,function(index,booking) {
                            jQuery('#tracksSelection').find('#trackDetails_'+index).find('#chk_'+index).attr('edit_track','yes').attr('checked','checked').trigger('click');
                            jQuery('#tracksSelection').find('#trackDetails_'+index).find('.timeSlotText input').val(timeSlot);
                            jQuery('#tracksSelection').find('#trackDetails_'+index).find('.studentDetails').val(users[booking.student_id]).attr('disabled',true);
                            if(booking.name != '' && booking.name != null) {
                                jQuery('#tracksSelection').find('#trackDetails_'+index).find('.unknownStudent').attr('checked','checked').trigger('click');
                                jQuery('#tracksSelection').find('#chk_unknown_'+index+'_ct').find('.unknown_student_name').val(booking.name).attr('disabled',true);
                                jQuery('#tracksSelection').find('#chk_unknown_'+index+'_ct').find('.unknown_student_phone').val(booking.phone).attr('disabled',true);
                            }
                            jQuery('#tracksSelection').find('#trackDetails_'+index).find('.unknownStudent').attr('disabled',true);
                            jQuery('#tracksSelection').find('#trackDetails_'+index).find('#chk_'+index).parent().addClass('checked');
                            jQuery('#tracksSelection').find('#trackDetails_'+index).find('#chk_'+index).attr('disabled','disabled');
                        });
                    });
                }
            })
            },1000);
        });
        
        // Calendar Pagination Links
        jQuery(document).delegate('.calendarPaginationLink','click',function() {
            var page        = parseInt(jQuery(this).attr('page'));
            var area        = jQuery('.areaObject:checked').val();
            var year        = jQuery(this).attr('year');
            var week        = jQuery(this).attr('week-no');
            
            jQuery('#bookingForm').hide();
            jQuery('#previousCalendar').attr('year',year);
            jQuery('#nextCalendar').attr('year',year);
            jQuery('.booking_calendar_table').attr('year',year);
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller'=>'bookings','action'=>'calendar')); ?>',
                data        : 'area='+area+'&week='+week+'&year='+year+'&date='+jQuery('.booking_calendar_table').attr('start-date'),
                dataType    : "HTML",
                complete    : function() {
                    
                },
                beforeSend  : function() {
                    
                },
                success     : function(data) {
                    jQuery('#calendarList').html(data);
                },
                error        : function() {
                }
            });
        });
        
        jQuery(document).delegate('#onBehalf','click',function(){
            if(jQuery(this).is(':checked')) {
                jQuery('#bookingTeacher').show();
            } else {
                jQuery('#bookingTeacher').hide();
                jQuery.each(jQuery('.studentIdAutoSuggest'),function() {
                    jQuery(this).parents('.trackDetails').find('.unknownStudent').closest('.span3').show();
                    jQuery(this).parents('.trackDetails').find('.trackCheckbox').removeClass('external_teacher_selected');
                    if(jQuery(this).parents('.trackDetails').find('.trackCheckbox').is(':checked')) {
                        jQuery(this).parents('.trackDetails').find('.unknownStudent').trigger('click');
                    }
                });
            }
        }).trigger('click');
        
        // Student Auto Complete
        jQuery(document).delegate('.studentIdAutoSuggest','keyup',function() {
            var studentField    = jQuery(this).parent().find('.studentId').attr('id');
            if(jQuery(this).val() == '') {
                jQuery('#'+studentField).val('');
            }
        });

        jQuery(document).delegate('.studentIdAutoSuggest','focusin',function() {
            var studentField    = jQuery(this).parent().find('.studentId').attr('id');
            var element         = jQuery(this);
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
                                    email     : ' [ Email Id : ' + item.User.email_id +' ] ',
                                    no        : ' [ Student No. : #' + item.User.student_number +' ] '
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
                return jQuery( '<li class="studentLabel"></li>' )
                    .data( "item.autocomplete", item )
                    .append( "<a> <span>" + item.label + "</span> <br> "+item.email+" <br>" + item.no + "</a>" )
                    .appendTo( ul );
            };
        });
        
        jQuery(document).delegate('.teacherAutoSuggest','focusin',function() {
            var field           = jQuery(this).parent().find('.onBehalfTeacher');
            var element         = jQuery(this);
            jQuery(this).autocomplete({
                minLength        : 2,
                select: function( event, ui ) {
                   
                    if(jQuery(this).attr('id') != 'co_teacher_auto') {
                        teacherObject   = {
                            role : ui.item.role,
                            name : ui.item.label
                        }
                    }
                    
                    field.val(ui.item.sysvalue);
                    element.val(ui.item.value);
                    if((jQuery(this).attr('id') != 'co_teacher_auto') && (ui.item.role == 'external_teacher')) {
                        jQuery.each(jQuery('.studentIdAutoSuggest'),function() {
                            if(jQuery(this).parents('.trackDetails').find('.trackCheckbox').is(':checked')) {
                                jQuery(this).parents('.trackDetails').find('.studentId').val('-1');
                            }
                            jQuery(this).parents('.trackDetails').find('.trackCheckbox').addClass('external_teacher_selected');
                            if(jQuery(this).parents('.trackDetails').find('.trackCheckbox').is(':checked')) {
                                if(!jQuery(this).parents('.trackDetails').find('.unknownStudent').is('checked')) {
                                    jQuery(this).parents('.trackDetails').find('.unknownStudent').attr('checked','checked').trigger('click');
                                }
                            }
                            jQuery(this).parents('.trackDetails').find('.unknownStudent').closest('.span3').hide();
                        });
                    } else {
                        jQuery.each(jQuery('.studentIdAutoSuggest'),function() {
                            jQuery(this).parents('.trackDetails').find('.unknownStudent').closest('.span3').show();
                            jQuery(this).parents('.trackDetails').find('.trackCheckbox').removeClass('external_teacher_selected');
                            if(jQuery(this).parents('.trackDetails').find('.trackCheckbox').is(':checked')) {
                                if(jQuery(this).parents('.trackDetails').find('.unknownStudent').is(':checked')) {
                                    jQuery(this).parents('.trackDetails').find('.unknownStudent').trigger('click');
                                }
                            }
                        });
                    }
                },
                source      : function(request, response) {
                    jQuery.ajax({
                        url         : '<?php echo $this->Html->url(array('controller'=>'users','action'=>'autoSuggest')); ?>/' + request.term  + '/teacher',
                        dataType    : "json",
                        complete    : function(){
                        },
                        beforeSend  : function(){
                        },
                        success     : function(data){
                            response(jQuery.map( data , function( item ) {
                                return {
                                  label     : item.User.firstname+' '+item.User.lastname  + ' [ Email Id : ' + item.User.email_id +' ] ',
                                  value     : item.User.firstname+' '+item.User.lastname,
                                  sysvalue  : item.User.id,
                                  role      : item.User.role
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
            });
        });
        
        jQuery(document).delegate('#cancelForm','click',function(){
            jQuery('#bookingForm').hide();
            jQuery.scrollTo('#bookingsCt','slow');
        });
        
        jQuery( '.bookingLinkName' ).tooltip({
            position: {
                my: "center bottom-20",
                at: "center top",
                using: function( position, feedback ) {
                    jQuery( this ).css( position );
                }
            }
        });
        
        jQuery(document).delegate('.releaseTrack','click',function(){
            var area        = jQuery(this).attr('area');
            var date        = jQuery(this).attr('date');
            var time        = jQuery(this).attr('time_slot');
            var ele         = jQuery(this);
            jQuery.fancybox({
                'href'          : '<?php echo $this->Html->url(array('controller'   => 'bookings' , 'action' => 'releaseTrack')); ?>?area='+area+'&date='+date+'&time_slot='+time,
                'width'         : '75%',
                'height'        : '75%',
                'autoScale'     : false,
                'transitionIn'  : 'none',
                'transitionOut' : 'none',
                'type'          : 'iframe',
                'beforeClose'   : function () {
                    if(sessionStorage.getItem('closeFancybox') == 'closed') {
                        sessionStorage.removeItem('closeFancybox');
                        if(ele.hasClass('reopenTracks')) {
                            jQuery.ajax({
                                url         : '<?php echo $this->Html->url(array('controller'   => 'bookings','action'  => 'reopenTracks')); ?>',
                                data        : 'date='+date+'&area='+area+'&time='+time+'&track_status=reopen',
                                dataType    : 'html',
                                complete    : function() {

                                },
                                success     : function(data) {
                                    jQuery('#bookingsCt').show().html(data);
                                    jQuery('#booking_info').show();
                                    jQuery('#bookingsCt').find('.bookingTitle').removeClass().addClass('bookingTitle area-'+area+'-color');
                                    jQuery.scrollTo('#bookingsCt','slow');
                                },
                                error       : function() {
                                }
                            });
                        } else {
                            window.location = "<?php echo $this->Html->url(array('controller' => 'users','action' => 'logout')); ?>";
                        }
                    } else {
                        window.location='<?php echo $this->Html->url(array('controller' => 'bookings','action'=>'calendar')); ?>?date='+date+'&area='+area;
                    }
                }
            });
        });
        
        jQuery(document).delegate('.closeTrack','click',function(){
            var area    = jQuery(this).attr('area');
            var date    = jQuery(this).attr('date');
            var time    = jQuery(this).attr('time_slot');
            var ele     = jQuery(this);
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller'   => 'bookings','action'  => 'closeTracks')); ?>',
                data        : 'date='+date+'&area='+area+'&time='+time,
                dataType    : 'html',
                complete    : function() {
                    
                },
                success     : function(data) {
                    jQuery('#exc').html(data);
                },
                error       : function() {
                }
            });
        });
        
        jQuery(document).delegate('.editTrack','click',function(){
            var area    = jQuery(this).attr('area');
            var date    = jQuery(this).attr('date');
            var time    = jQuery(this).attr('time_slot');
            var ele     = jQuery(this);
            var url     = '<?php echo $this->Html->url(array('controller' => 'bookings','action'  => 'editTracks')); ?>?date='+date+'&area='+area+'&time='+time;
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller'   => 'bookings','action'  => 'editTracks')); ?>',
                data        : 'date='+date+'&area='+area+'&time='+time,
                dataType    : 'html',
                complete    : function() {
                    
                },
                success     : function(data) {
                    jQuery('#bookingForm').show().html(data);
                    jQuery.scrollTo('#bookingForm','slow');
                    jQuery('#BookingCalendarForm').attr('action',url);
                },
                error       : function() {
                }
            });
        });
        
        jQuery(document).delegate('.editTrackDetails','click',function(){
            var area    = jQuery(this).attr('area');
            var date    = jQuery(this).attr('date');
            var time    = jQuery(this).attr('time_slot');
            var ele     = jQuery(this);
            var url     = '<?php echo $this->Html->url(array('controller' => 'bookings','action'  => 'editBookings')); ?>?date='+date+'&area='+area+'&time='+time;
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller'   => 'bookings','action'  => 'editBookings')); ?>',
                data        : 'date='+date+'&area='+area+'&time='+time,
                dataType    : 'html',
                complete    : function() {
                    
                },
                success     : function(data) {
                    jQuery('#bookingForm').show().html(data);
                    jQuery.scrollTo('#bookingForm','slow');
                    jQuery('#BookingCalendarForm').attr('action',url);
                },
                error       : function() {
                }
            });
        });
        
        jQuery(document).delegate('.unknownStudent','click',function(){
            var id = jQuery(this).attr('id');
            if(jQuery(this).is(':checked')) {
                jQuery('#'+id+'_ct').show();
                jQuery(this).parents('.studentDiv').find('.studentDetails').hide();
                var trackCheckBox = jQuery(this).parents('.trackDetails').find('.trackCheckbox').attr('id');
                if(jQuery('#'+trackCheckBox).attr('edit_track') == 'yes') {
                    jQuery(this).parents('.trackDetails').next().find('.unknown_field').attr('disabled','disabled');
                }
            } else {
                jQuery('#'+id+'_ct').hide();
                jQuery(this).parents('.studentDiv').find('.studentDetails').show();
            }
        });
        
        jQuery(document).delegate('.delete_track','click',function() {
            var ele = jQuery(this);
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller'   => 'bookings','action'  => 'deleteTrack')); ?>/'+jQuery(this).attr('booking-id')+'/'+jQuery(this).attr('track-id'),
                dataType    : 'json',
                beforeSend  : function() {
                    ele.parent().find('.delete_track_loader').show();
                },
                complete    : function() {
                    ele.parent().find('.delete_track_loader').hide();
                },
                success     : function(data) {
                    jQuery('#txt_booking_details').html(data.message);
                    if(data.status == 'success') {
                        $track_id = ele.attr('track-id');
                        $chk = jQuery('#chk_' + $track_id);
                        
                        $chk.removeAttr('disabled');
                        $chk.attr('checked', false);
                        jQuery('#uniform-chk_' + $track_id).find('span').removeClass('checked');

                        //var track   = ele.closest('.trackDetails').find('.trackCheckbox').val();

                        jQuery('#timeSlot_'+ $track_id).removeAttr('disabled').val('').trigger('chosen:updated');
                        //jQuery('#timeSlot_'+ $track_id).val('').trigger('chosen:updated');

                        ele.closest('.trackDetails').next('.span12').find('.unknown_field').removeAttr('disabled').val('');
                        ele.closest('.trackDetails').find('.studentDetails').val('');
                        ele.closest('.trackDetails').find('.studentId').val('');

                        jQuery('#chk_unknown_' + $track_id).removeAttr('disabled');
                        jQuery('#chk_unknown_' + $track_id).attr('checked', false);
                        jQuery('#uniform-chk_unknown_' + $track_id).find('span').removeClass('checked');

                        jQuery('#trackDetails_' + $track_id).find('.bookingTrack').hide();
                        jQuery('#chk_unknown_'+ $track_id +'_ct').hide();

                        ele.hide();

                        if(data.show_dialog == 'true'){
                            jQuery( "#delete-last-lane-dialog-confirm" ).dialog({
                                resizable : false,
                                height    : 200,
                                width     : 350,
                                modal     : true,
                                closeOnEscape: false,
                                buttons   : {
                                    "<?php echo __('ok'); ?>": function() {
                                        jQuery(this).dialog( "close" );
                                        window.location.reload();
                                    },
                                }
                            });
                        } else {
                            window.location.reload();
                        }
                    }
                }
            });
        });
        
        jQuery('.month_picker').datepicker({
            format      : "mm-yyyy",
            startView   : "months", 
            minViewMode : "months"
        }).on('changeDate', function(e){
            jQuery(this).datepicker('hide');
            var area = jQuery('.areaObject:checked').val(); 
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller'=>'bookings','action'=>'calendar')); ?>',
                data        : 'select_month=true&area='+area+'&date=' + e.date.getFullYear() + '-' + ( e.date.getMonth() + 1) + '-' + e.date.getDate() ,
                dataType    : 'html',
                complete    : function() {},
                beforeSend  : function() {},
                success     : function(data) {
                    jQuery('#calendarList').html(data);
                    jQuery('td.today').first().trigger('click');
                },
                error       : function() {}
            });
        });
    });

var clear_timer ;
function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    clear_timer =  setInterval( function () {
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            open_it_msg();
            clearInterval(clear_timer);
            //window.location = "<?php echo $this->webroot;?>Bookings";
            open_it_msg();
        }
    }, 1000);
}

/*
window.onload = function () {
    var fiveMinutes = <?php echo $time_left?>,
    display = document.querySelector('#edit-time-details');
    startTimer(fiveMinutes, display);
};*/

function open_it_msg(){
        jQuery('#session_time_out').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                window.location.href = "<?php echo $this->Html->url(array('controller' => 'Areas','action'=>'index')); ?>";
            }
        }
    }); 
        return false;
} 
function track_lock(){
        jQuery('#track_locked').dialog({
        autoOpen        : true,
        modal           : true,
        buttons         : {
            'Ok'        : function(){
                
                window.location.href = "<?php echo $this->Html->url(array('controller' => 'bookings','action'=>'calendar')); ?>";
                }
        }
    }); 
        return false;
} 
</script>
<?php $this->end(); ?>
<div id="session_time_out" style="display: none;" title="Session er udløbet">
    <p>Din computer har været inaktiv for længe, opdater og prøv igen</p>
</div>
<div id="track_locked" style="display: none;" title="Dagen er låst">
    <p>Der er en anden lærer der i øjeblikket redigere denne dag. Kom tilbage senere.</p>
</div>
<div class="inner-content">

    <?php 
    $title = __('Booking Calendar');
    $this->Html->pageInnerTitle($title,array(
        'icon'  => '<i class="fa fa-calendar month_picker"></i>'
    ));
    ?>

    <div class="row-fluid">
        <?php 
        echo $this->Form->create('Booking',array(
            'class' => 'form-horizontal',
            'type'  => 'post',
            'url'   => array(
                'controller'    => 'bookings',
                'action'        => 'add',
                '?'             => array(
                    'iframe'    => $iframe
                )
        )));
        ?>
        <div class="widget">
            <div class="widget-header">
                <h5><?php echo __('Bookings'); ?></h5>
            </div>

            <div class="widget-content no-padding">
                <div class="form-row">
                    <label class="field-name"><?php echo __('Area'); ?>&nbsp;&nbsp;:</label>
                    <div class="field">
                        <?php 
                        $areaSlugs  = array_keys($areaList);
                        echo $this->Form->radio('area',$areaList,array(
                            'type'          => 'text',
                            'label'         => TRUE,
                            'div'           => NULL,
                            'class'         => 'span12 areaObject',
                            'legend'        => FALSE,
                            'hiddenField'   => FALSE,
                            'value'         => (isset($this->request->query['area']) && !empty($this->request->query['area']))?$this->request->query['area']:$areaSlugs[0]
                        ));
                        ?>
                    </div>
                </div>
                <div class="form-row calendarCt center" id="calendarList"></div>
                <div class="detailsLoading" id="detailLoader"><?php  echo $this->Html->image("big-loader.gif");?></div>
                <div class="form-row calendarCt" id="bookingsCt"></div>
                <div id="booking_info" style="display:none;">Brugeroversigt: KTR = Kolding Trafikskole, KTV = Kør rigtigt, KRU = Kruses KS, FRØ = Frederiks KS, KOE = Koefoeds KS, TDI = Tage Dibbon, Kur = Kurtzmanns KS, JKS = Jørn Skrødder, Lisbeths = 3 første bogstaver i byen</div>
                <div class="form-row bookingForm" id="bookingForm"></div>
                <div class="form-row" id="bookingError" style="display:none;"></div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<div id="bookingAddForm" style="display: none;">
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Book Slot'); ?></h5>
        </div>
        <div class="widget-content no-padding">
            <?php 
            echo $this->Form->input('Booking.area_slug',array(
                'type'  => 'hidden',
                'class' => 'areaSelection'
            ));
            echo $this->Form->input('Booking.date',array(
                'type'  => 'hidden',
                'class' => 'date'
            ));
            ?>
            <div class="form-row tracks">
                <label class="span1"><?php echo __('Tracks'); ?></label>
                <div class="span11 tracksSelection"></div>
                <div id="txt_tracks_error" class="error-message"></div>
                <div class="clearfix"></div>
            </div>
            <div id="firstBooking">
                <div class="form-row">
                <div class="span12">
                    <label class="span3"><?php echo __('Would You like to Add a Note?'); ?>:</label>
                    <div class="span9">
                        <?php
                        echo $this->Form->checkbox('Booking.long_description',array(
                            'id'    => 'longDescriptionChk'
                        ));
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                </div>

                <div class="form-row longDescription"  id="longDescription">
                    <label class="span3"><?php echo __('Long Description'); ?>:</label>
                    <div class="span9">
                        <?php 
                        echo $this->Form->input('Booking.full_description',array(
                            'type'          => 'textarea',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Enter Long Description'),
                        ));
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div id="hide-on-same-booking-time-slot">
                    <?php if(in_array($currentUser['User']['role'],array('admin','internal_teacher'))) { ?>
                        <div class="form-row">
                            <div class="span4">
                                <label class="span9" id="onBehalfLabel"><?php echo __('On Behalf of '); ?>:</label>
                                <div class="span2">
                                    <?php
                                    echo $this->Form->checkbox('Booking.on_behalf',array(
                                        'id'    => 'onBehalf',
                                        'value' => $currentUser['User']['id'],
                                        'hiddenField'   => FALSE
                                    ));
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="offset1 span7 bookingTeacher" id="bookingTeacher">
                                <label class="span2"><?php echo __('Select Teacher '); ?>:</label>
                                <div class="span10">
                                    <?php 
                                    echo $this->Form->input('teacher',array(
                                        'label'         => false,
                                        'div'           => null,
                                        'class'         => 'span12 teacherAutoSuggest',
                                        'placeHolder'   => __('Search Teacher'),
                                    ));
                                    echo $this->Form->hidden('Booking.user_id',array(
                                        'class'    => 'onBehalfTeacher'
                                    ));
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="error-message" id="txt_booking_users"></div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="form-row">
                        <div class="span12">
                            <label class="span3"><?php echo __('Select Co Teacher '); ?>:</label>
                            <div class="span9">
                                <?php 
                                echo $this->Form->input('co_teacher_auto',array(
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'span6 teacherAutoSuggest',
                                    'placeHolder'   => __('Search Teacher'),
                                    'value'         => (isset($booking) && !is_null($booking['Booking']['co_teacher'])) ? $modifiedUsers[$booking['Booking']['co_teacher']]['name'] : '',
                                ));
                                echo $this->Form->hidden('Booking.co_teacher',array(
                                    'class'     => 'onBehalfTeacher',
                                    'value'     => (isset($booking) && !is_null($booking['Booking']['co_teacher'])) ? $booking['Booking']['co_teacher'] : '',
                                ));
                                ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="span12" >
                            <label class="span3"><?php echo __('Select Teacher Course'); ?>:</label>
                            <div class="span9" id="teacherCourseList">
                                <?php
                                echo $this->Form->select('Booking.course',$courses,array(
                                   'label' => false,
                                   'div'   => null,
                                   'class' => 'span6',
                                   'empty' => __('Select Course'),
                                ));
                                ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo $this->Form->hidden('Booking.reference',array( 'id'    => 'reference' )); ?>
            
            <div class="form-row">
                <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
                <div class="field" id="formControlls">
                    <?php
                    $btnName = __('Add');
                    echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                        'class' => 'button button-green',
                        'type'  => 'button',
                        'id'    => 'formSubmit'
                    ),array(
                        'escape' => FALSE
                    ));
                    echo $this->Form->button('<i class="icon-remove icon-white"></i> '.__('Cancel'),array(
                        'class' => 'button button-red',
                        'type'  => 'button',
                        'id'    => 'cancelForm'
                    ),array(
                        'escape' => FALSE
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="delete-last-lane-dialog-confirm"  style="display: none">
    <h4 class="text-center">Du har slettet den sidste bane</h4>
</div>