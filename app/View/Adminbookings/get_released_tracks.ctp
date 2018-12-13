
    <script type="text/javascript">
        var response;
        jQuery(document).ready(function(){
            jQuery('.add_student').hide();
            jQuery('input[type="checkbox"], input[type="radio"]').uniform();
            
            jQuery(document).delegate('.studentIdAutoSuggest','focusin',function(){
                var element = $this;
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
                var $current = jQuery(this);
                var bookingTrackId  = $current.parents('.form-row').find('#bookingTrackId').val();

                jQuery.ajax({
                    url         : '<?php echo $this->Html->url(array('controller' => 'adminbookings','action' => 'updateGetReleasedTrackStatus')) ?>?id=' + bookingTrackId,
                    type        : 'get',
                    dataType    : 'JSON',
                    success     : function(data) {
                        if(data.status == 1){
                            jQuery( "#dialog-confirm" ).dialog({
                                resizable : false,
                                height    : 325,
                                width     : 350,
                                modal     : true,
                                closeOnEscape: false,
                                buttons   : {
                                    "<?php echo __('Nej, tag mig tilbage til oversigten'); ?>": function() {
                                        jQuery(this).dialog( "close" );
                                        window.location.reload();
                                    },
                                    "<?php echo __('Ja, lad mig redigere indtastningen'); ?>": function() {
                                        showForm($current);
                                        jQuery(this).dialog( "close" );
                                    }
                                }
                            });

                            jQuery('#dialog-confirm').closest(".ui-dialog").find(".ui-dialog-buttonset").css('float', 'none');
                            jQuery('#dialog-confirm').closest(".ui-dialog").find(".ui-dialog-buttonset button").css('width', '100%');
                            jQuery('#dialog-confirm').closest(".ui-dialog").find(".ui-dialog-buttonset button:first").addClass("btn btn-danger custom-first-btn");
                            jQuery('#dialog-confirm').closest(".ui-dialog").find(".ui-dialog-buttonset button:last").addClass("btn custom-last-btn");
                        } else {
                            showForm($current);
                        }
                    }
                });

                return false;
            });

            jQuery('.student_name').autocomplete({
                minLength        : 2,
                select: function( event, ui ) {
                    jQuery('#new_student_id').val(ui.item.sysvalue);
                    jQuery.ajax({
                        url         :  '<?php echo $this->Html->url(array('controller' => 'adminusers','action' => 'getStudentInfo')) ?>',
                        data        : 'student_id='+ui.item.sysvalue,
                        type        : 'get',
                        dataType    : 'json',
                        success     : function(data) {
                            jQuery.each(data,function(key,value) {
                                jQuery('#'+key+'2').val(value);
                            });
                        }
                    });
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

            jQuery('body').delegate(".datepicker",'focusin',function(){
                var maxDate;

                jQuery(this).datepicker({
                    changeYear  : true,
                    changeMonth : true,
                    dateFormat  : 'dd-mm-yy',
                    yearRange   : "-"+parseInt(jQuery(this).attr('minYear'))+":+"+parseInt(jQuery(this).attr('maxYear'))+"'"
                });

                if(jQuery(this).attr('maxDate') != '') {
                    jQuery(this).datepicker( "option", "maxDate", jQuery(this).attr('maxDate'));
                }
            });

            jQuery(document).on('keypress', '#phone_no', function (event) {
                var key = window.event ? event.keyCode : event.which;
                if (event.keyCode === 8 || event.keyCode === 46) {
                    return true;
                } else if ( key < 48 || key > 57 ) {
                    return false;
                } else {
                    return true;
                }
            });

            /*jQuery(document).on('keypress', '#name', function (event) {
                var regex = new RegExp("^[a-zA-Z ]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            });

            jQuery(document).on('keypress', '#address', function (event) {
                var regex = new RegExp("^[0-9a-zA-Z ]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            });*/
        });

        function showForm(ele){
            var $this = ele;
            jQuery('.student_info .error-message').html('');
            jQuery('#met,#not_met').parent().removeClass('checked');
            jQuery('.student_widget').find('input[type="checkbox"]').removeAttr('disabled');
            //jQuery('.student_info').slideDown('fast');
            jQuery('.single_student_info').html('');
            jQuery('.single_student_info').hide();
            $this.closest('.form-row').next('.single_student_info').html(jQuery('.student_info').html()).show();

            var bookingTrackId  = $this.parents('.form-row').find('#bookingTrackId').val();
            jQuery('#booking_track_id').val(bookingTrackId);
            
            var studentId       = $this.parents('.form-row').find('#studentId').val();
            
            var data = 'booking_track_id='+bookingTrackId+'&student_id='+studentId;
            if(studentId == '') {
                var data = 'booking_track_id='+bookingTrackId;
            }
            if(studentId != '' || bookingTrackId != '') {
                
                jQuery.ajax({
                    url         : '<?php echo $this->Html->url(array('controller' => 'adminusers','action' => 'getStudentInfo')); ?>',
                    data        : data,
                    type        : 'get',
                    dataType    : 'json',
                    beforeSend  : function() {
                        $this.parents('.form-row').find('.loading_img').show();
                    },
                    complete    : function() {
                        $this.parents('.form-row').find('.loading_img').hide();
                    },
                    success     : function(data) {
                        jQuery.each(data,function(key,value) {
                            if((key == 'status') && value != "") {
                                jQuery('#'+value).attr('checked','checked');
                                jQuery('#'+value).parent().addClass('checked');
                                jQuery('.student_widget').find('input[type="checkbox"]').attr('disabled','disabled');
                            }
                            jQuery('#'+key).val(value);
                        });
                        
                        jQuery.scrollTo('#studentForm','slow');
                    },
                    error       : function() {}
                });
            }
        }
    </script>

<div class="inner-content">
    <?php 
        $title = __('Released Tracks');
        $this->Html->pageInnerTitle($title,array(
            'icon'              => '<i class="fa fa-calendar"></i>',
            'released_track'    => 'true',
        ));
    ?>
    <div class="row-fluid form-horizontal">      
        <div class="widget">
            <div class="widget-header">
                <h5><?php echo __('Track And Student Information'); ?></h5>
            </div>
            <div class="widget-content no-padding  released_track_table">
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
                            <label class="span2"><b><?php echo __('Driving School'); ?></b></label>
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
                                echo $areaList[$areaSlug]; 
                            ?></label>
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
                            <label class="span2"><?php 
                            $teacher_id = ($bookingTrack['booking_user_id'] != '') ? $bookingTrack['booking_user_id'] : $bookings['user_id'];
                            echo (!isset($users[$teacher_id]['id']))?'':$users[$teacher_id]['firstname'].' '.$users[$teacher_id]['lastname']; ?></label>
                            <label class="span2"><?php echo (!isset($users[$teacher_id]) || empty($users[$teacher_id]['company_id']))?'':$drivingSchools[$users[$teacher_id]['company_id']]; ?></label>
                            <label class="center">
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
                <div class="form-row">
                    <div class="widget-content no-padding center">
                        <b><?php echo __('No Tracks Released');  ?></b>
                    </div>
                </div>
                <?php } ?>
                <div class="form-row student_info" title="<?php echo __('Student Information'); ?>" style="display:none">
                    <?php echo $this->Element('releasedStudentDetails',array(
                        'type'  => 'get'
                    )); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dialog-confirm" title="Rediger bane" style="display: none">
    <h1 class="text-center">Der er en anden der har tastet sig ind på denne bane.</h1>
    <h4 class="text-center">Er du sikker på du vil redigere indtastningen?</h4>
</div>

<style type="text/css">
    .ui-dialog-buttonset .custom-first-btn {
        height: 75px;
        font-size: 20px;
    }

    .ui-dialog-buttonset .custom-last-btn {
        background:none!important;
        border:none; 
        padding:0!important;
        font: inherit;
        cursor: pointer;
        color: #000;
    }
</style>