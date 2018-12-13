<script type="text/javascript">
    teacherObject   = {
        role: '<?php echo (isset($booking)) ? $modifiedUsers[$booking['Booking']['user_id']]['role'] : 'admin'; ?>'
    }
    jQuery('.unknownStudent').each(function() {
        if(jQuery(this).is(':checked') && jQuery(this).parents('.trackDetails').find('.trackCheckbox').is(':checked')) {
            jQuery(this).trigger('click');
        }
    });
</script>

<div class="widget" id="editForm">
    <div class="widget-header">
        <h5>
        <?php
        if(isset($booking)) {
            echo __('Update Booking Slot Of ').$area['Area']['name'].__(' Area On ').
                date('d/M-Y',strtotime($booking['Booking']['date'])).' '.__('For Booking Id #').$booking['Booking']['id'];
        } else {
            echo __('Update Booking Slot Of ').$area['Area']['name'].__(' Area On ').
                date('d/M-Y',strtotime($this->request->query['date']));
        }
        ?>
        </h5>
        <h5 id="txt_booking_details" class="error-message reference"></h5>
    </div>

    <div class="widget-content no-padding">
        <?php 
            echo $this->Form->input('Booking.area_slug',array(
                'type'  => 'hidden',
                'id'    => 'areaSelection',
                'value' => (isset($booking)) ? $booking['Booking']['area_slug'] : $this->request->query['area']
            ));
            $date = (isset($booking)) ? strtotime($booking['Booking']['date']) : strtotime($this->request->query['date']);
            echo $this->Form->input('Booking.date',array(
                'type'  => 'hidden',
                'class' => 'date',
                'id'    => 'date',
                'value' => (isset($booking)) ? date('d.m.Y',strtotime($booking['Booking']['date'])) : $this->request->query['date']
            ));
        ?>
        
        <div class="form-row" id="tracks">
            <label class="span1"><?php echo __('Tracks'); ?></label>
            
            <div class="span11" id="tracksSelection">
                <?php foreach($tracks as $id => $name) { ?>
                    <div class="span12 no-margin trackDetails" id="trackDetails_<?php echo $id; ?>">
                        <div class="span1 no-margin trackCheckboxCt">
                            <input type="checkbox" name="data[BookingTrack][<?php echo $id; ?>][track_id]" 
                                <?php echo (isset($modifiedbookedTracks[$id])) ? 'readonly' : '' ?> 
                                    id="chk_<?php echo $id; ?>" value="<?php echo $id; ?>" 
                                    class="uniform trackCheckbox <?php echo (!isset($booking)) ? 'noTimeSlot' : '' ?> 
                                    <?php echo ($modifiedUsers[$booking['Booking']['user_id']]['role'] == 'external_teacher') ? 'external_teacher_selected' : '' ?>"
                                    <?php echo isset($modifiedbookedTracks[$id])?'checked':''; ?>/>
                            <label for="chk_<?php echo $id; ?>"><?php echo $name; ?></label>
                            <?php if(isset($modifiedbookedTracks[$id])) { ?>
                                <input type="hidden" value="<?php echo $id; ?>" name="data[BookingTrack][<?php echo $id; ?>][track_id]" />
                            <?php } ?>
                        </div>

                        <div class="bookingTrack" style="<?php echo isset($modifiedbookedTracks[$id])?'display:block':'display:none'; ?>">
                            <div class="timeSlot span2">
                                <?php if(isset($modifiedbookedTracks[$id]) && !empty($modifiedbookedTracks[$id])) { ?>
                                    <select name="data[BookingTrack][<?php echo $id; ?>][time_slot][]" readonly class="timeSlotSelectCt span12" multiple id="timeSlot_<?php echo $id; ?>">
                                        <?php 
                                        foreach($areaTimeSlots[$id] as $key => $areaTimeSlot) {
                                            $startTimeSlot = explode('-',$areaTimeSlot);
                                        ?>
                                        <option <?php echo ((isset($bookedTimeSlots[$key]) && in_array($id, array_keys($bookedTimeSlots[$key])) && !in_array($areaTimeSlot,$modifiedbookedTracks[$id]['time_slot'])) || ($startTimeSlot[0] < date('H:i') && $date < strtotime(date('Y-m-d')))) ? 'readonly' : ''; ?> 
                                            value='<?php echo $areaTimeSlot; ?>' <?php echo (in_array($areaTimeSlot,$modifiedbookedTracks[$id]['time_slot']))?'selected':''; ?>>
                                            <?php echo $key; ?>
                                        </option>
                                        <?php  } ?>
                                    </select>
                                <?php } else { ?>
                                    <select name="data[BookingTrack][<?php echo $id; ?>][time_slot][]" class="timeSlotSelectCt span12" multiple id="timeSlot_<?php echo $id; ?>">
                                    </select>
                                <?php } ?>
                            </div>

                            <div class="timeSlotText span2" style="display:none;margin-left:0">
                                <input type="text" class="span12" readonly />
                            </div>

                            <div class="studentDiv span6" id="studentDiv<?php echo $id; ?>" >
                                <?php if($currentUser['User']['role'] != 'external_teacher') { ?>
                                    <input type="text" class="studentIdAutoSuggest span5 studentDetails pull-left" value="<?php echo (isset($modifiedbookedTracks[$id]['student_id']) && !empty($modifiedbookedTracks[$id]['student_id']) && isset($modifiedUsers[$modifiedbookedTracks[$id]['student_id']]))?$modifiedUsers[$modifiedbookedTracks[$id]['student_id']]['name']:''; ?>" placeHolder="<?php echo __('Student Name/ Username'); ?>" style="<?php echo (!empty($modifiedbookedTracks[$id]['name']) && !empty($modifiedbookedTracks[$id]['phone']))?'display:none;':''; ?>" />
                                        
                                    <input type="hidden" name="data[BookingTrack][<?php echo $id; ?>][student_id]"  value="<?php echo (isset($modifiedbookedTracks[$id]['student_id']) && !empty($modifiedbookedTracks[$id]['student_id']))?$modifiedbookedTracks[$id]['student_id']:''; ?>" class="studentId" id="studentId_<?php echo $id; ?>" />
                                <?php } ?>
                                
                                <div class="span4 no-margin" style="<?php echo ($modifiedUsers[$booking['Booking']['user_id']]['role'] == 'external_teacher') ? 'display:none' : '' ?>">
                                    <div style="float: left; margin-right: 10px;">
                                        <input type="checkbox" name="data[BookingTrack][<?php echo $id; ?>][unknown]" id="chk_unknown_<?php echo $id; ?>" value="1" class="uniform unknownStudent" <?php echo ((!empty($modifiedbookedTracks[$id]['name']) &&  !empty($modifiedbookedTracks[$id]['phone'])) || ($currentUser['User']['role'] == 'external_teacher') ||  ($modifiedUsers[$booking['Booking']['user_id']]['role'] == 'external_teacher'))?'checked="checked"':''; ?>  style="<?php echo ($currentUser['User']['role'] == 'external_teacher') ? 'display:none' : '' ?>" <?php echo (!empty($modifiedbookedTracks[$id]['name']) && !empty($modifiedbookedTracks[$id]['phone'])) ? 'disabled':''; ?> />
                                        <label for="chk_unknown_<?php echo $id; ?>" style="<?php echo ($currentUser['User']['role'] == 'external_teacher') ? 'display:none' : '' ?>"><?php echo __('Unknown Student'); ?></label>
                                    </div>
                                </div>
                                <!--
                                <div class="span3 no-margin">
                                    <input type="checkbox" name="data[BookingTrack][<?php echo $id; ?>][send_sms]" id="chk_send_sms_<?php echo $id; ?>" value="1" class="uniform" style="<?php echo ($currentUser['User']['role'] == 'external_teacher') ? 'display:none' : '' ?>" <?php echo (!empty($modifiedbookedTracks[$id]['name']) && !empty($modifiedbookedTracks[$id]['phone'])) ? 'disabled':''; ?> />
                                    <label for="chk_send_sms_<?php echo $id; ?>" style="<?php echo ($currentUser['User']['role'] == 'external_teacher') ? 'display:none' : '' ?>"><?php echo __('Send SMS'); ?></label>
                                </div>
                                -->

                                <?php if(!isset($booking)) { ?>
                                    <input type="hidden" name="data[BookingTrack][<?php echo $id; ?>][booking_id]" value="<?php echo (isset($modifiedbookedTracks[$id]['booking_id']) && !empty($modifiedbookedTracks[$id]['booking_id']))?$modifiedbookedTracks[$id]['booking_id']:''; ?>" class="bookingId" id="bookingId_<?php echo $id; ?>"/>
                                    <?php
                                    if(isset($modifiedbookedTracks[$id])) {
                                        foreach($modifiedbookedTracks[$id]['time_slot'] as $key => $timeSlot) {
                                        ?>
                                        <input type="hidden" name="data[BookingTrack][<?php echo $id; ?>][time_slot][]" value="<?php echo $timeSlot; ?>"/>                                        
                                        <?php 
                                        }
                                    }    
                                } ?>
                            </div>

                            <?php if(isset($modifiedbookedTracks[$id])) { ?>
                                <div class="span2 delete_track_div">
                                    <a href="javascript:" booking-id="<?php echo $booking['Booking']['id']; ?>" track-id="<?php echo $id; ?>"
                                       class="button button-red delete_track">
                                           <?php echo '<i class="fa fa-trash-o"></i> '.__('Delete'); ?>
                                    </a>
                                    <span class="pull-right delete_track_loader" style="display:none;"><?php echo $this->Html->image('ajax-loader.gif'); ?></span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if(!empty($modifiedbookedTracks[$id]['name']) && !empty($modifiedbookedTracks[$id]['phone'])){ ?>
                        <div class="span12 no-margin" id="chk_unknown_<?php echo $id; ?>_ct">
                            <div class="span1 no-margin">  
                            </div>
                            <div class="span3 no-margin">  
                                <input type="text" class="span12 unknown_student_name unknown_field" name="data[BookingTrack][<?php echo $id; ?>][name]" value="<?php echo $modifiedbookedTracks[$id]['name']; ?>"  placeHolder="<?php echo __('Enter Student Name'); ?>" readonly="readonly"/>
                                <!--<input type="hidden" name="data[BookingTrack][<?php echo $id; ?>][name]" value="<?php echo $modifiedbookedTracks[$id]['name']; ?>"  placeHolder="<?php echo __('Enter Student Name'); ?>" readonly="readonly"/>-->
                                <div class="error-message" id="txt_booking_name_<?php echo $id; ?>"></div>
                            </div>
                            <div class="span3">  
                                <input type="text" class="span12 no-margin unknown_student_phone unknown_field" name="data[BookingTrack][<?php echo $id; ?>][phone]" value="<?php echo $modifiedbookedTracks[$id]['phone']; ?>" placeHolder="<?php echo __('Enter Student Mobile Number'); ?>" readonly="readonly"/>
                                <!--<input type="hidden" name="data[BookingTrack][<?php echo $id; ?>][phone]" value="<?php echo $modifiedbookedTracks[$id]['phone']; ?>" readonly="readonly"/>-->
                                <div class="error-message" id="txt_booking_phone_<?php echo $id; ?>"></div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="span12 no-margin unknown_student_field" id="chk_unknown_<?php echo $id; ?>_ct" style="display:none;">
                            <div class="span1 no-margin"></div>
                            
                            <div class="span3 no-margin">  
                                <input type="text" class="span12 unknown_student_name unknown_field" name="data[BookingTrack][<?php echo $id; ?>][name]"  placeHolder="<?php echo __('Enter Student Name'); ?>" />
                                <div class="error-message" id="txt_booking_name_<?php echo $id; ?>"></div>
                            </div>

                            <div class="span3">  
                                <input type="text" class="span12 no-margin unknown_student_phone unknown_field" name="data[BookingTrack][<?php echo $id; ?>][phone]" placeHolder="<?php echo __('Enter Student Mobile Number'); ?>" />
                                <div class="error-message" id="txt_booking_phone_<?php echo $id; ?>"></div>
                            </div>
                        </div>
                    <?php }  ?>
                <?php } ?>
            </div>
        
            <div id="txt_tracks_error" class="error-message"></div>
            <div class="clearfix"></div>
        </div>
        
        <div class="form-row">
            <div class="span12">
                <label class="span3"><?php echo __('Would You like to Add a Note?'); ?>:</label>
                <div class="span9">
                    <?php 
                    $args   = array(
                        'id'    => 'longDescriptionChk'
                    );
                    
                    if(!empty($booking['Booking']['full_description'])) {
                        $args['checked']    = 'checked';
                    }
                    echo $this->Form->checkbox('long_description',$args);
                    ?>
                </div>
            </div>
        </div>

        <div class="form-row longDescription" id="longDescription" style="<?php echo (empty($booking['Booking']['full_description']))?'display:none':'display:block';?>">
            <label class="span3"><?php echo __('Long Description'); ?>:</label>
            <div class="span9">
                <?php 
                echo $this->Form->input('Booking.full_description',array(
                    'type'          => 'textarea',
                    'label'         => false,
                    'div'           => null,
                    'class'         => 'span12',
                    'placeHolder'   => __('Enter Long Description'),
                    'value'         => $booking['Booking']['full_description']
                ));
                ?>
            </div>
        </div>

        <?php if(!$isEdit){ ?>
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
                            'value'         => (isset($booking) && !is_null($booking['Booking']['co_teacher']))?$modifiedUsers[$booking['Booking']['co_teacher']]['name']:''
                        ));
                        echo $this->Form->hidden('Booking.co_teacher',array(
                            'class'     => 'onBehalfTeacher',
                            'value'     => (isset($booking) && !is_null($booking['Booking']['co_teacher'])) ? $booking['Booking']['co_teacher'] : ''
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="span12">
                    <label class="span3"><?php echo __('Select Teacher Course'); ?>:</label>
                    <div class="span9">
                        <?php 
                        echo $this->Form->select('Booking.course',$courses,array(
                           'label' => false,
                           'div'   => null,
                           'class' => 'span6',
                           'empty' => __('Select Course'),
                           'value' => (isset($booking) && !is_null($booking['Booking']['course'])) ? $booking['Booking']['course'] : ''
                        ));
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>

            <div class="form-row">
                <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
                <div class="field" id="formControlls">
                    <?php 
                    $btnName = ($isEdit)?__('Update'):__('Add'); 
                    echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                        'class' => 'button button-green button_align',
                        'type'  => 'button',
                        'id'    => 'formSubmit'
                    ),
                        array('escape' => FALSE)
                    );
                    if(((!$isTracksEdit) && ($modifiedUsers[$currentUser['User']['id']]['role'] == 'admin'))
                        || ((!$isTracksEdit) && ($currentUser['User']['id'] == $booking['Booking']['user_id']))) {
                        echo $this->Html->link('<i class="fa fa-trash-o"></i> '.__('Remove Booking'),array(
                            'controller'    => 'bookings',
                            'action'        => 'delete',
                            $booking['Booking']['id'],
                            '?'             => array(
                                'area'     => $booking['Booking']['area_slug'],
                                'date'     => $booking['Booking']['date'],
                                'iframe'   => (isset($this->request->query['iframe'])) ? $this->request->query['iframe'] : 0,
                            )
                        ),array(
                            'class' => 'button button-red',
                            'escape' => FALSE
                        ));
                    }
                    
                    echo $this->Form->button('<i class="icon-remove icon-white"></i> '.__('Cancel'),array(
                        'class' => 'button button-red button_align',
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

<script type="text/javascript">
    jQuery('.fly_loading').hide();
    jQuery('form').find('input[type="checkbox"], input[type="radio"], select.uniform, input[type="file"]').uniform();
    <?php 
    foreach($tracks as $id    =>  $track) {
        if(isset($modifiedbookedTracks[$id]['time_slot'])) {
    ?>
        jQuery('#timeSlot_<?php echo $id; ?>').chosen();
    <?php } } ?>
</script>