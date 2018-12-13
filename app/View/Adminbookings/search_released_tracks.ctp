<?php $this->append('script'); ?>
<script type="text/javascript">
    var response;
    jQuery(document).ready(function(){
        jQuery('.add_student').hide();
        jQuery('input[type="checkbox"], input[type="radio"]').uniform();
        jQuery('.teacherAutoSuggest').keyup(function() {
            if(jQuery(this).val() == '') {
                jQuery('#teacher_id').val('');
            }
        });
        jQuery(document).delegate('.studentIdAutoSuggest','focusin',function(){
            var element = jQuery(this);
            jQuery(this).autocomplete({        
                minLength        : 2,      
                select: function( event, ui ) {
                    
                    jQuery('#student_id').val(ui.item.sysvalue);
                    element.val(ui.item.value);
                },
                source      : function(request, response){
                    jQuery.ajax({
                        url         : '<?php echo $this->Html->url(array('controller'=>'adminusers','action'=>'autoSuggest')); ?>/' + request.term  + '/student',
                        dataType    : "json",
                        complete    : function(){

                        },
                        beforeSend  : function(){

                        },
                        success     : function(data){                            
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
                            alert('Could not connect to server.');
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
        
        jQuery('.viewStudent').click(function() {
            jQuery('.student_info .error-message').html('');
            jQuery('#met,#not_met').parent().removeClass('checked');
            jQuery('.student_widget').find('input[type="checkbox"]').removeAttr('disabled');
            jQuery('.single_student_info').html('');
            jQuery(this).closest('.form-row').next('.single_student_info').html(jQuery('.student_info').html()).show();

            var bookingTrackId  = jQuery(this).parents('.form-row').find('#bookingTrackId').val();
            jQuery('#booking_track_id').val(bookingTrackId);
            
            var studentId       = jQuery(this).parents('.form-row').find('#studentId').val();
            
            var data = 'booking_track_id='+bookingTrackId+'&student_id='+studentId;
            if(studentId == '') {
                var data = 'booking_track_id='+bookingTrackId;
            }
            if(studentId != '' || bookingTrackId != '') {
                
                jQuery.ajax({
                    url         : '<?php echo $this->Html->url(array('controller' => 'adminusers','action' => 'getStudentInfo')) ?>',
                    data        : data,
                    type        : 'get',
                    dataType    : 'json',
                    beforeSend  : function() {
                        jQuery(this).parents('.form-row').find('.loading_img').show();
                    },
                    complete    : function() {
                        jQuery(this).parents('.form-row').find('.loading_img').hide();
                    },
                    success     : function(data) {
                        response = data;
                        jQuery.each(data,function(key,value) {
                            response = data;
                            if((key == 'status') && value != "") {
                                jQuery('#'+value).attr('checked','checked');
                                jQuery('#'+value).parent().addClass('checked');
                                jQuery('.student_widget').find('input[type="checkbox"]').attr('disabled','disabled');
                            }
                            jQuery('#'+key).val(value);
                        });
                        
                        jQuery.scrollTo('#studentForm','slow');
                    },
                    error       : function() {
                       
                    }
                });
            }
        });
        
        jQuery(document).delegate('.updateUser','click',function() {
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller' => 'adminbookings','action' => 'updateTrackUser')) ?>/'+false,
                data        : jQuery(this).parents('.studentForm').serialize(),
                type        : 'post',
                dataType    : 'html',
                success     : function(data) {
                    jQuery('#exc').html(data);
                }, 
                error       : function() {
                    alert('Error. Not able to update user');
                }
            });
        });
        
        jQuery(document).delegate('.addStudentBtn','click',function(){
            jQuery.fancybox({
                'href'          : '<?php echo $this->Html->url(array('controller'   => 'adminusers' , 'action' => 'add')); ?>/student/layout',
                'width'         : '90%',
                'height'        : '90%',
                'autoScale'     : false,
                'transitionIn'  : 'none',
                'transitionOut' : 'none',
                'type'          : 'iframe',
                'beforeClose'   : function () {
                }
            });
        });
        
        jQuery(document).delegate('.teacherAutoSuggest','focusin',function(){
            var element         = jQuery(this);
            jQuery(this).autocomplete({        
                minLength        : 2,      
                select: function( event, ui ) {                    
                    jQuery('#teacher_id').val(ui.item.sysvalue);
                    
                },
                source      : function(request, response){
                    jQuery.ajax({
                        url         : '<?php echo $this->Html->url(array('controller'=>'adminusers','action'=>'autoSuggest')); ?>/' + request.term  + '/teacher',
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
                            alert('Could not connect to server.');
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
    });
</script>
<?php $this->end(); ?>
<div class="inner-content">
    <?php 
        $title = __('Search Released Tracks');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-calendar"></i>'
        )); 
    ?>     
    <div class="row-fluid form-horizontal">
        <?php
        echo $this->Form->create('User',array(                    
            'type'      => 'GET'
        ));
        ?>
        <div class="span12" style="margin: 10px 0px;">
            <div class="span3">
                <?php
                echo $this->Form->hidden('search',array(
                    'value'         => 'true'
                ));
                echo $this->Form->input('student',array(
                    'label'         => false,
                    'type'          => 'text',
                    'placeholder'   => __('Select Student'),
                    'class'         => 'span12',
                    'value'         => (isset($this->request->query['student']) && !empty($this->request->query['student'])) ? $this->request->query['student'] : ''
                ));
                ?>
            </div>
            <div class="span3">
                <?php 
                echo $this->Form->select('area_id',$areaList,array(
                    'label'             => false,
                    'div'               => null,
                    'class'             => 'span12 chosen-select1',
                    'multiple'          => FALSE,
                    'data-placeholder'  => __('Select Area'),
                    'empty'             => __('Select Area'),
                    'value'             => (isset($this->request->query['area_id']) && !empty($this->request->query['area_id']))?$this->request->query['area_id']:''
                ));
                ?>
            </div>
            <div class="span3">
                <?php
                echo $this->Form->input('teacher',array(
                    'type'          => 'text',
                    'class'         => 'span12 teacherAutoSuggest',
                    'label'         => FALSE,
                    'required'      => FALSE,
                    'div'           => FALSE,
                    'placeholder'   => __('Select Teacher'),
                    'value'         => (isset($this->request->query['teacher']))?$this->request->query['teacher']:'',
                ));
                
                echo $this->Form->hidden('teacher_id',array(
                    'id'    => 'teacher_id',
                    'value' => (isset($this->request->query['teacher_id']))?$this->request->query['teacher_id']:'',
                ));
                ?>
            </div>
        </div>
        <div class="span12" style="margin: 10px 0px;">
            <div class="span3">
                <?php 
                echo $this->Form->select('driving_school',$drivingSchools,array(
                    'label'     => FALSE,
                    'required'  => FALSE,
                    'class'     => 'span12 chosen-select1',
                    'div'       => FALSE,
                    'empty'     => __('Select Driving School'),
                    'value'     => (isset($this->request->query['driving_school']))?$this->request->query['driving_school']:'',
                ));
                ?>
            </div>
            <div class="span3">
                <?php
                echo $this->Form->input('date_from',array(
                    'type'          => 'text',
                    'label'         => false,
                    'required'      => FALSE,
                    'id'            => 'startDate',
                    'class'         => 'span12 datepicker',
                    'placeholder'   => __('Select Start Date'),
                    'div'           => FALSE,
                    'minYear'       => 50,
                    'maxYear'       => 0,
                    'value'         => (isset($this->request->query['date_from']))?$this->request->query['date_from']:date('d/m/Y', strtotime('-1 month')),
                ));
                ?>
            </div>
            <div class="span3">
                <?php 
                echo $this->Form->input('date_to',array(
                    'type'          => 'text',
                    'label'         => false,
                    'required'      => FALSE,
                    'id'            => 'endDate',
                    'class'         => 'span12 datepicker',
                    'placeholder'   => __('Select End Date'),
                    'div'           => FALSE,
                    'minYear'       => 50,
                    'maxYear'       => 0,
                    'value'         => (isset($this->request->query['date_to']))?$this->request->query['date_to']:date('d/m/Y'),
                ));
                ?>
            </div>
            <div class="span3">
                <?php
                echo $this->Form->button(__('Search'),array(
                    'class' => 'button button-green',
                ));
                ?>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
        <div class="clearfix"></div>
        <div class="widget">
            <div class="widget-header">
                <h5><?php echo __('Track And Student Information'); ?></h5>
            </div>
            <div class="widget-content no-padding released_track_table">
                <?php if(!empty($bookingTracks)) { ?>
                <div class="form-row no-padding" id="studentsCt">
                    <div class="widget-content no-padding">
                        <div class="form-row">
                            <label class="span1"><b><?php echo __('No.'); ?></b></label>
                            <label class="span1"><b><?php echo __('Area'); ?></b></label>
                            <label class="span1"><b><?php echo __('Track'); ?></b></label>
                            <label class="span1"><b><?php echo __('Date'); ?></b></label>
                            <label class="span1"><b><?php echo __('Time Slot'); ?></b></label>
                            <label class="span2"><b><?php echo __('Student'); ?></b></label>
                            <label class="span2"><b><?php echo __('Driving Instructor'); ?></b></label>
                            <label class="span1"><b><?php echo __('Driving School'); ?></b></label>
                            <label class="span1"><b><?php echo __('Status'); ?></b></label>
                            <label class="span1">&nbsp;</label>
                        </div>
                        <?php
                        $i = 0;
                        foreach($bookingTracks as $bookingTrack) {
                        ?>
                        <div class="form-row <?php echo (isset($users[$bookingTrack['student_id']]) && !empty($users[$bookingTrack['student_id']]['date_of_birth'])) ? 'addBackground' : ((!empty($bookingTrack['date_of_birth'])) ? 'addBackground' : '');?>">
                            <label class="span1"><?php echo ++$i; ?></label>
                            <label class="span1"><?php 
                            $areaSlug = $bookings[$bookingTrack['booking_id']]['area_slug']; 
                            echo $areaList[$areaSlug]; ?>
                            </label>
                            <label class="span1"><?php echo $tracks[$areaSlug][$bookingTrack['track_id']]; ?></label>
                            <label class="span1"><?php echo date('d.m.Y',strtotime($bookings[$bookingTrack['booking_id']]['date'])); ?></label>
                            <label class="span1"><?php echo $bookingTrack['time_slot']; ?></label>
                            <label class="span2">
                                <?php 
                                echo (isset($bookingTrack['student_id']) && !empty($bookingTrack['student_id']) && isset($users[$bookingTrack['student_id']])) ? 
                                    $users[$bookingTrack['student_id']]['firstname'].' '.$users[$bookingTrack['student_id']]['lastname'] :
                                    (!empty($bookingTrack['name']) ? $bookingTrack['name'].' ('.__('External Student').')' :__('External Student')); 
                                echo $this->Form->hidden('student_id',array(
                                    'value' => (isset($bookingTrack['student_id']) && !empty($bookingTrack['student_id'])) ? $bookingTrack['student_id']: '',
                                    'id'    => 'studentId'
                                ));
                                echo $this->Form->hidden('booking_track_id',array(
                                    'value' => (isset($bookingTrack['id']) && !empty($bookingTrack['id'])) ? $bookingTrack['id']: '',
                                    'id'    => 'bookingTrackId'
                                ));
                                ?>
                            </label>
                            <label class="span2">
                                <?php 
                                $teacher_id = ($bookingTrack['booking_user_id'] != '') ? $bookingTrack['booking_user_id'] :  $teacher[$bookingTrack['booking_id']]['user_id'] ;
                                echo (!isset($users[$teacher_id])) ? '' : 
                                    $users[$teacher_id]['firstname'].' '.$users[$teacher_id]['lastname']; 
                                ?>
                            </label>
                            <label class="span1">
                                <?php
                                echo (!isset($users[$teacher_id]) || empty($users[$teacher_id]['company_id'])) ? '' : $drivingSchools[$users[$teacher_id]['company_id']];
                                ?>
                            </label>
                            <?php
                            $status = '';
                            if(empty($bookingTrack['status']) || $bookingTrack['status'] == 'not_met' || is_null($bookingTrack['status'])) {
                                $status = __('Not Met');
                            } else {
                                $status = __('Met');
                            }
                            ?>
                            
                            <label class="span1"><?php echo $status; ?></label>
                            <label class="span1 center">
                            <?php
                            echo $this->Form->button(__('Edit'),array('type' => 'button','class' => 'button button-blue viewStudent'));
                            ?>
                            </label>
                        </div>
                        <div class="form-row single_student_info" style="display:none;"></div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <?php } else {
                ?>
                <div class="">
                    <div class="widget-content center">
                        <b><?php echo __('No Tracks Released');  ?></b>
                    </div>
                </div>
                <?php } ?>
                <div class="form-row student_info" title="<?php echo __('Student Information'); ?>" style="display:none">
                    <?php echo $this->Element('releasedStudentDetails',array(
                        'type'  => 'search'
                    )); ?>
                </div>
            </div>
        </div>
    </div>
</div>