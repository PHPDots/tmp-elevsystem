<?php $this->append('script'); ?>
<script type="text/javascript">
    var response;
    var reportTypes = '';
    jQuery(document).ready(function() {
        jQuery('.datetimepicker').datetimepicker({
            format:'d.m.Y H:i:s'      
        });
        
        jQuery('.datepicker').datepicker({
            format      : 'dd.mm.yyyy',
            startDate   : '-50y',
            autoclose   : true,
            orientation : 'auto bottom'
        });
    
        reportTypes = <?php echo json_encode($reportTypes); ?>;
        jQuery('#reportType').change(function() {
            var value = jQuery(this).val();
            jQuery('.formField').parents('.form-row').hide();
            eval('reportFields = reportTypes.'+jQuery(this).val()+'.fields');
            jQuery.each(reportFields,function(key,id){   
                jQuery('#'+id).parents('.form-row').show();
                jQuery('#'+id).show();                
            });
            
            if(value == 'ongoing_list') {
                jQuery('#driving_school').hide();
            }else if(value == 'unapproved_driving_lessons' || value == 'future_bookings') {
                jQuery('.datetimepicker').val('');
                jQuery('.datepicker').val('');
            } else {
                jQuery('#datetime_from .datetimepicker').val('<?php echo date('d.m.Y H:i', strtotime('-1 month')); ?>');
                jQuery('#datetime_to .datetimepicker').val('<?php echo date('d.m.Y H:i'); ?>');
                jQuery('#date_from .datepicker').val('<?php echo date('d.m.Y', strtotime('-1 month')); ?>');
                jQuery('#date_to .datepicker').val('<?php echo date('d.m.Y'); ?>');
            }
        });
        
        jQuery('#reportType').trigger('click');
        
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
                        url         : '<?php echo $this->Html->url(array('controller'=>'users','action'=>'autoSuggest')); ?>/' + request.term  + '/student',
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
        
        jQuery(document).delegate('.teacherAutoSuggest','focusin',function(){
            var field           = jQuery(this).parent().find('.onBehalfTeacher');
            var element         = jQuery(this);
            jQuery(this).autocomplete({        
                minLength        : 2,      
                select: function( event, ui ) {
                    
                    teacherObject   = {
                        role : ui.item.role,
                        name : ui.item.label
                    }
                    
                    field.val(ui.item.sysvalue);
                    element.val(ui.item.value);
                    
                    if(ui.item.role == 'external_teacher'){
                        jQuery.each(jQuery('.tracksSelection').find('.trackDetails'),function(index,value){
                            if(jQuery(this).find('.trackCheckbox').is(':checked')){                               
                                jQuery(this).find('.studentId').val(-1);
                            }
                            jQuery.each(jQuery('.studentIdAutoSuggest'),function(){
                                var value   = jQuery(this).parents('.trackDetails').find('.trackCheckbox').val();
                                var html = '<input type="text" class="span12" name="data[BookingTrack][%i%][number]" placeHolder="<?php echo __('Enter Student Mobile Number'); ?>"/>';
                                jQuery(this).parent().append(html.replace(/\%i\%/g,value));
                                jQuery(this).remove();
                            });
                        });
                    }
                    
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
    <?php $this->Html->pageInnerTitle(__('Generate Report'),array(
        'icon'  => '<i class="fa fa-file-text-o"></i>'
    )); ?>
    <div class="row-fluid">
    <div class="widget reportCt">
        <?php
        echo $this->Form->create('Report',array(
            'class' => 'form-horizontal',
            'type'  => 'get'
        ));
        ?>
        <div class="form-row no-border">
            <div class="span6">
                <label class="span4"><?php echo __('Select Report Type'); ?>:</label>                
                <div class="span8">
                    <?php
                    echo $this->Form->select('report_type.',$reports,array(
                        'label'             => false,
                        'div'               => null,
                        'class'             => 'chosen-select1 span12',
                        'multiple'          => FALSE,
                        'data-placeholder'  => __('Select Report Type'),
                        'empty'             => __('Select Report Type'),
                        'id'                => 'reportType',
                    ));
                    ?>                                       
                </div>
                <div class="clearfix"></div>
            </div>        
            <div class="clearfix"></div>
        </div>
        
        <div class="form-row no-margin no-border" style="display:none;">
            <div class="span6 formField" id="student_name">
                <label class="span4"><?php echo __('Student'); ?>:</label>
                <div class="span8">
                    <?php 
                    echo $this->Form->input('student_autosuggest',array(
                        'label'         => false,
                        'type'          => 'text',
                        'placeholder'   => __('Select Student'),
                        'class'         => 'span12 studentIdAutoSuggest'
                    ));
                    
                    echo $this->Form->hidden('student_id',array(
                        'id'    => 'student_id',
                    ));
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="form-row no-margin no-border" style="display:none;">
            <div class="span6 formField" id="booking_type">
                <label class="span4"><?php echo __('Booking Type'); ?>:</label>
                <div class="span8">
                    <?php
                        echo $this->Form->select('booking_type',$booking_type,array(
                            'label'             => false,
                            'div'               => null,
                            'class'             => 'span12 chosen-select1',
                            'multiple'          => FALSE, 
                            'data-placeholder'  => __('Select Booking Type'),
                            'empty'             => __('Select Booking Type'),                          
                        ));                        
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="span6 formField" id="status">
                <label class="span4"><?php echo __('Booking Status'); ?>:</label>
                <div class="span8">
                    <?php
                        echo $this->Form->select('status',$status,array(
                            'label'             => false,
                            'div'               => null,
                            'class'             => 'span12 chosen-select1',
                            'multiple'          => FALSE,     
                            'data-placeholder'  => __('Select Booking Status'),
                            'empty'             => __('Select Booking Status'),                      
                        ));                        
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        
        <div class="form-row no-margin no-border" style="display:none;">
            <div class="span6 formField" id="area">
                <label class="span4"><?php echo __('Area'); ?>:</label>
                <div class="span8">
                    <?php
                    echo $this->Form->select('area_id',$areas,array(
                        'label'             => false,
                        'div'               => null,
                        'class'             => 'span12 chosen-select1',
                        'multiple'          => FALSE,                           
                        'data-placeholder'  => __('Select Area'),
                        'empty'             => __('Select Area'),
                    ));                        
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="form-row no-margin no-border" style="display:none;">
            <div class="span6 formField" id="city">
                <label class="span4"><?php echo __('City'); ?>:</label>
                <div class="span8">
                    <?php
                    echo $this->Form->input('city',array(
                        'type'          => 'text',
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeholder'   => __('Enter City'),
                    ));                        
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="form-row no-margin no-border" style="display:none;">
            <div class="span6 formField" id="teacher">
                <label class="span4"><?php echo __('Teacher'); ?>:</label>
                <div class="span8">
                    <?php
                        echo $this->Form->input(
                           'teacher',array(
                               'label'         => false,
                               'div'           => null,
                               'class'         => 'span12 teacherAutoSuggest',
                               'placeHolder'   => __('Search Teacher'),                                        
                       ));
                   ?>
                   <?php 
                       echo $this->Form->hidden('teacher_id',array(
                           'class'    => 'onBehalfTeacher'
                       )); 
                   ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="span6 formField" id="driving_school">
                <label class="span4"><?php echo __('Driving School'); ?>:</label>
                <div class="span8">
                    <?php
                        echo $this->Form->select('driving_school',$drivingSchools,array(
                            'label'             => false,
                            'div'               => null,
                            'class'             => 'span12 chosen-select1',
                            'multiple'          => FALSE,                           
                            'data-placeholder'  => __('Select Driving School'),
                            'empty'             => __('Select Driving School'),
                        ));                        
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="form-row no-margin no-border" style="display:none;">
            <div class="span6 formField" id="date_from">
                <label class="span4" id="labelDateFrom"><?php echo __('Date From'); ?>:</label>
                <div class="span8">
                    <?php
                        echo $this->Form->input('date_from',array(
                            'type'      => 'text',
                            'label'     => false, 
                            'required'  => FALSE,
                            'id'        => 'startDate',
                            'class'     => 'span12 datepicker',
                            'div'       => FALSE,
                            'minYear'   => 50,
                            'maxYear'   => 0,   
                            'value'     => date('d.m.Y', strtotime('-1 month')),
                        ));
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="span6 formField" id="date_to">
                <label class="span4" id="labelDateTo"><?php echo __('Date To'); ?>:</label>
                <div class="span8">
                    <?php
                        echo $this->Form->input('date_to',array(
                            'type'      => 'text',
                            'label'     => false, 
                            'required'  => FALSE,
                            'id'        => 'endDate',
                            'class'     => 'span12 datepicker',
                            'div'       => FALSE,
                            'minYear'   => 50,
                            'maxYear'   => 0,
                            'value'     => date('d.m.Y'),
                        ));
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="form-row no-margin no-border" style="display:none;">
            <div class="span6 formField" id="datetime_from">
                <label class="span4" id="labelDateFrom"><?php echo __('Date From'); ?>:</label>
                <div class="span8">
                    <?php
                    echo $this->Form->input('datetime_from',array(
                        'type'      => 'text',
                        'label'     => false, 
                        'required'  => FALSE,
                        'id'        => 'startDate',
                        'class'     => 'span12 datetimepicker',
                        'div'       => FALSE,
                        'minYear'   => 50,
                        'maxYear'   => 1,   
                        'value'     => date('d.m.Y H:i', strtotime('-1 month')),
                    ));
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="span6 formField" id="datetime_to">
                <label class="span4" id="labelDateTo"><?php echo __('Date To'); ?>:</label>
                <div class="span8">
                    <?php
                    echo $this->Form->input('datetime_to',array(
                        'type'      => 'text',
                        'label'     => false, 
                        'required'  => FALSE,
                        'id'        => 'endDate',
                        'class'     => 'span12 datetimepicker',
                        'div'       => FALSE,
                        'minYear'   => 50,
                        'maxYear'   => 1,
                        'value'     => date('d.m.Y H:i'),
                    ));
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="form-row buttonGroup">                
            <div class="field">
            <?php
            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.__(' Generate Report'),array(
                'class'         => 'button button-blue',
                'type'          => 'submit',
            ),array(
                'escape' => FALSE
            ));
            echo $this->Html->link('<i class="icon-remove icon-white"></i>'.__(' Cancel'),
                array('action' => 'index'),
                array(
                    'class'     => 'button button-red',
                    'escape'    => FALSE,
            ));
            ?>
            </div>
        </div>
    </div>
    </div>
</div>