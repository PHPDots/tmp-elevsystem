<?php $this->append('script'); ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        
        jQuery(document).delegate('.studentIdAutoSuggest','focusin',function() {
            var studentField    = jQuery(this).parent().find('.studentId').attr('id');
            var element         = jQuery(this);
            var url             = '';
            
            jQuery(this).autocomplete({
                minLength        : 2,
                select: function( event, ui ) {
                    jQuery('#'+studentField).val(ui.item.sysvalue);
                    element.val(ui.item.value);
                },
                source      : function(request, response){
                    if(jQuery('#searchInAll').is(':checked')) {
                        url = '<?php echo $this->Html->url(array('controller'=>'users','action'=>'autoSuggest')); ?>/' + request.term  + '/student';
                    } else {
                        url = '<?php echo $this->Html->url(array('controller'=>'users','action'=>'autoSuggest')); ?>/' + request.term  + '/student?get_my_student='+true;
                    }
                    jQuery.ajax({
                        url         : url,
                        dataType    : "json",
                        complete    : function(){
                            
                        },
                        beforeSend  : function(){
                            
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
        
        jQuery('.booking_type').click(function() {
            if(jQuery(this).val() == 'test') {
                jQuery('.testing_show').show();                
            } else {
                jQuery('.testing_show').hide();                
            }
        });
        <?php if($isEdit) { ?>
        jQuery('#DrivingLessonType'+'<?php echo Inflector::humanize($drivingLesson['DrivingLesson']['type']); ?>').trigger('click');
        <?php } ?>
        
        jQuery('.teacherAutoSuggest').keyup(function() {
            if(jQuery(this).val() == '') {
                jQuery('#teacher_id').val('');
            }
        });
        
        jQuery(document).delegate('.teacherAutoSuggest','focusin',function() {
            jQuery(this).autocomplete({        
                minLength        : 2,      
                select: function( event, ui ) {
                    jQuery('#teacher_id').val(ui.item.sysvalue);
                },
                source      : function(request, response){
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
                                  sysvalue  : item.User.id
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
        $title = __('Book Lesson');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-user"></i>'
        ));
    ?>
    <div class="row-fluid"><div class="widget">
        <?php
        echo $this->Form->create('DrivingLesson',array(
            'class'         => 'form-horizontal',
        ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Lesson Details'); ?></h5>
        </div>
        <div class="widget-content no-padding">
            <div class="form-row">
                <span class="span6">
                    <label class="span3"><?php echo __('Booking Type'); ?>:</label>
                    <div class="span9">
                        <?php $types = Configure::read('bookingType'); ?>
                        <label>
                            <?php 
                            echo $this->Form->radio('type',$types,array(
                                'label'         => TRUE,
                                'div'           => TRUE,
                                'class'         => 'booking_type',
                                'legend'        => FALSE, 
                                'hiddenField'   => FALSE,
                                'value'         => ($isEdit) ? $drivingLesson['DrivingLesson']['type'] : 'driving',
                            ));
                            ?>
                        </label>
                    </div>
                </span>
                <span class="span6">
                    <label class="span3"><?php echo __('Would you like to search in all students'); ?>:</label>
                    <div class="span9">
                        <input type="checkbox" name="data[DrivingLesson][search]" id="searchInAll" class="css-checkbox" />
                        <label for="searchInAll" class="css-label"></label>
                    </div>
                </span>
                <div class="clearfix"></div>
            </div>
            
            <div class="form-row">
                <div class="span6">
                    <label class="span3" for="email"><?php echo __('Student'); ?>:</label>
                    <div class="span9">
                        <?php 
                        echo $this->Form->input('student',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12 studentIdAutoSuggest',
                            'placeHolder'   => __('Enter Student'),  
                            'value'         => ($isEdit)?$user['User']['firstname'].' '.$user['User']['lastname']:''
                        ));
                        ?>
                        <?php 
                        echo $this->Form->hidden('student_id',array(
                            'class'    => 'studentId',
                            'id'       => 'studentId',
                            'value'    => ($isEdit)?$drivingLesson['DrivingLesson']['student_id']:''
                        )); 
                        ?>
                        <div id="txt_student_id_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6">
                    <label class="span3" for="email"><?php echo __('Start Time'); ?>:</label>
                    <div class="span9">
                        <div class="span6 no-padding-left">
                        <?php 
                        echo $this->Form->input('start_time',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12 datepicker',
                            'placeHolder'   => __('Select Start time'),
                            'value'         => ($isEdit)?date('d.m.Y',strtotime($drivingLesson['DrivingLesson']['start_time'])):'',
                            'maxYear'       => 20,
                            'minYear'       => 50,
                        ));
                        ?>
                        </div>
                        <div class="span3">
                        <?php 
                        $hoursArr = array();
                        for($i = 0; $i < 24; $i++) {
                            $hoursArr[($i+1)] = $i+1;
                        }
                        echo $this->Form->select('start_time_hour',$hoursArr,array(
                            'label' => false,
                            'div'   => null,
                            'class' => 'span12',
                            'empty' => __('Select Hours'),
                            'value' => ($isEdit) ? date('H',strtotime($drivingLesson['DrivingLesson']['start_time'])) : ''
                        ));
                        ?>
                        </div>
                        <div class="span3 no-padding-right">
                        <?php 
                        $minutesArr = array();
                        for($i = 0; $i <= 55; $i+=5) {
                            $minutesArr[$i] = $i;
                        }
                        echo $this->Form->select('start_time_min',$minutesArr,array(
                            'label' => false,
                            'div'   => null,
                            'class' => 'span12',
                            'empty' => __('Select Minutes'),
                            'value' => ($isEdit) ? date('i',strtotime($drivingLesson['DrivingLesson']['start_time'])) : ''
                        ));
                        ?>
                        </div>
                        <div id="txt_start_time_error" class="error-message"></div>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="span6">
                    <label class="span3"><?php echo __('Lesson Time'); ?>:</label>
                    <div class="span9">
                        <?php $lessonTime = Configure::read('lessonTime'); ?>
                        <label>
                            <?php 
                                echo $this->Form->radio('lesson_time',$lessonTime,array(
                                    'label'         => TRUE,
                                    'div'           => TRUE,
                                    'class'         => 'css-radio',
                                    'legend'        => FALSE, 
                                    'hiddenField'   => FALSE,
                                    'value'         => ($isEdit)?$drivingLesson['DrivingLesson']['lesson_time']:'90'
                                 ));
                            ?>
                        </label>
                    </div>
                </div>
                <?php if(!$isEdit) { ?>
                <div class="span6">
                    <label class="span3"><?php echo __('On behalf of'); ?>:</label>
                    <div class="span9">
                        <?php 
                        echo $this->Form->input('user_id',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12 teacherAutoSuggest',
                            'placeHolder'   => __('On behalf of'),
                            'value'         => ($isEdit) ? '' : '',
                        ));
                        
                        echo $this->Form->hidden('teacher_id',array(
                            'id'    => 'teacher_id'
                        ));
                        ?>
                        <div id="txt_module_error" class="error-message"></div>
                    </div>
                </div>
                <?php } ?>
                <div class="clearfix"></div>
            </div>
            
            <div class="form-row testing_show" style="display: none;">
                <div class="span6 approve_driving_lesson">
                    <label class="span3"><?php echo __('Approve Driving Lesson'); ?>:</label>
                    <div class="span9">
                        <input type="checkbox" name="data[DrivingLesson][approved]" value="yes" <?php echo ($isEdit && $drivingLesson['DrivingLesson']['approved'] == 'yes') ? 'checked' : ''; ?> id="approved" class="" />
                        <label for="approved" class=""></label>
                    </div>
                </div>
                <div class="span6" id="booking_status">
                    <label class="span3"><?php echo __('Status'); ?>:</label>
                    <div class="span9">
                        <?php $status = Configure::read('lessonStatus'); ?>
                        <label>
                            <?php 
                            echo $this->Form->radio('status',$status,array(                    
                                'label'         => TRUE,
                                'div'           => TRUE,
                                'class'         => '',
                                'legend'        => FALSE, 
                                'hiddenField'   => FALSE,
                                'value'         => ($isEdit)?$drivingLesson['DrivingLesson']['status']:'confirmed'
                             ));
                            ?>
                        </label>
                    </div>
                </div>                
            </div>
            
            <div class="form-row">
                <div class="span6">
                    <label class="span3"><?php echo __('Module'); ?>:</label>
                    <div class="span9">
                        <?php 
                        echo $this->Form->input('module',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Module'),
                            'value'         => ($isEdit) ? $drivingLesson['DrivingLesson']['module'] : '',
                        ));
                        ?>
                        <div id="txt_module_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6">
                    <label class="span3"><?php echo __('Comments'); ?>:</label>
                    <div class="span9">
                        <?php 
                        echo $this->Form->input('comments',array(
                            'type'          => 'textarea',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Comments'),
                            'value'         => ($isEdit) ? $drivingLesson['DrivingLesson']['comments'] : '',
                        ));
                        ?>
                        <div id="txt_comments_error" class="error-message"></div>
                    </div>
                </div>                
                <div class="clearfix"></div>
            </div>
            
            <div class="form-row">
            <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
            <div class="field" id="formControlls">
            <?php 
            $btnName = ($isEdit)?'Update':'Add';
            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                'class' => 'button button-green',
                'type'  => 'button',
                'id'    => 'formSubmit'
            ),
                array('escape' => FALSE)
            );
            echo $this->Html->link('<i class="icon-remove icon-white"></i> Cancel',array(
                'action' => 'index'
                ),array(
                    'class'     => 'button button-red',
                    'escape'    => FALSE,
            ));
            ?>
            </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div></div>
</div>