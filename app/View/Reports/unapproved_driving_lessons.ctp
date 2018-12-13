<?php 
    $types      = Configure::read('bookingType'); 
    $lessonTime = Configure::read('lessonTime');
    $status     = Configure::read('lessonStatus');
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
<div class="inner-content"><div class="row-fluid"><div class="span12">

    <?php if(isset($currentUserRole) && $currentUserRole == "admin"):?>    
    <div class="span12">
        <div class="span4">
        <div class="span9">
        <?php
        echo $this->Form->create('Report',array(
            'action'    => 'unapprovedLessons',
            'type'      => 'GET'
        ));
        
        echo $this->Form->input('searchTxt',array(
            'placeholder'   => __('Search By Teacher Name'),
            'label'         => FALSE,
            'class'         => 'span12',
            'div'           => FALSE,
            'value'         => (isset($this->request->query['searchTxt'])) ? $this->request->query['searchTxt'] : ''
        ));
        ?>
        </div>
        <div class="span3">
        <?php
        echo $this->Form->button('Search',array(
            'class' => 'button button-green',
        ));
        echo $this->Form->end();
        ?>
        </div>
        </div>
        <div class="span3">
            <?php
            if(isset($this->request->query['searchTxt']) && !empty($this->request->query['searchTxt'])) {
                echo __('Search result for ').' '."\"{$this->request->query['searchTxt']}\"";
            }
            ?>
        </div>
    </div>
    <div class="clearfix"></div>

    <?php endif;?>


    <div class="pull-right activity">
        <?php
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __(' Generate CSV'),array(
            'controller'    => 'reports',
            'action'        => 'unapprovedLessons',
            '?'             => array(
                'report_type'   => 'unapproved_driving_lessons',
                'csv'           => 'true',
				'searchTxt'   => isset($this->request->query['searchTxt']) ? $this->request->query['searchTxt'] : ''
            )),array(
                'class'     => 'button button-green',
                'escape'    => FALSE,
        ));
        ?>
    </div>
    <div class="clearfix"></div>
    <div class="widget">
        <div class="widget-header">
            <h5>
            <?php 
            $head = __('Unapproved Driving Lessons');
            echo (empty($searchString)) ? $head : $head.__(' from').' '.  implode(' ', $searchString); 
            ?>
            </h5>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th>No.</th>
                    <th align="left">Student Navn</th>
                    <?php if(isset($currentUserRole) && $currentUserRole == "admin"):?>    
                    <th align="left">Teacher Navn</th>
                    <?php endif;?>    					
                    <th align="left">Type</th>
                    <th align="left">Lektions tid</th>
                    <th align="left">Dato</th>
                    <th align="left">Status</th>                   
                </tr>
                <?php 
                $pageNo = $this->params['paging']['Systembooking']['page'];
                $i = (($pageNo - 1) * $perPage) + 1;
                if(!empty($drivingLessons)) {
                    foreach($drivingLessons as $drivingLesson){
                        $User = $drivingLesson['User'];
						
						
                        $teacher_name = "";

                        if(isset($drivingLesson['Teacher']['firstname']))
                        $teacher_name = $drivingLesson['Teacher']['firstname'].' '.$drivingLesson['Teacher']['lastname'];
						
						
                        $drivingLesson = $drivingLesson['Systembooking'];
                        $id = $drivingLesson['id'];
                        $student_id = $drivingLesson['student_id'];
                        $start_time = $drivingLesson['start_time'];
                        $end_time = $drivingLesson['end_time'];
                        $booking_type = $drivingLesson['booking_type'];
                        $status = $drivingLesson['status'];
                        $student_name = $users[$student_id]['firstname'].' '.$users[$student_id]['lastname'];
                        $status = $drivingLesson['status'];
                        $student_number = $User['phone_no'];
                        $city_id = $drivingLesson['city_id'];
                        $book_time = date('H:i',strtotime($start_time)).' - '.date('H:i', strtotime($end_time));


                        ?>
                        <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>
                            <td align="left">
                                <?php echo $student_name; ?>
                            </td>
                            <?php if(isset($currentUserRole) && $currentUserRole == "admin"):?>
                            <td align="left">
                                <?php echo $teacher_name; ?>
                            </td>    
                            <?php endif;?>    
							
                            <td align="left">
                                <?php echo $drivingLesson['booking_type']; ?>
                            </td>
                            <td align="left">
                                <?php echo $lessonTime[$drivingLesson['lesson_type']]; ?>
                            </td>
                            <td align="left">
                                <?php echo date('d.m.y H:i',strtotime($start_time)); ?>
                            </td>
                            <td align="left">
                                <button class="button button-red view-booking-approval" type="button"  align="left" data-id="<?php echo $id; ?>" data-student_id="<?php echo $student_id; ?>" data-date="<?php echo  date( 'd/m/Y', strtotime($start_time) ); ?>" data-booking_type="<?php echo $booking_type; ?>" data-status="<?php echo $status; ?>" data-student_name="<?php echo $student_name; ?>" data-student_number="<?php echo $student_number; ?>" data-book_time="<?php echo $book_time; ?>" data-city_name="<?php echo $cities[$city_id]; ?>" > <?php echo ($status == 'approved' ) ? "Approved" : "Ej godkendt"; ?></button>
                            </td>

                        </tr>
                    <?php       
                        $i++;
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="7" class="index_msg"><?php  echo __('No Bookings are added.'); ?></td>
                    </tr>
                <?php 
                }
                ?>
            </table>
        </div>
    </div>
            
    <div>
        <div class="pagination_no">
        <?php
        $this->paginator->options(array('url' => array('?' => $this->request->query)));
        echo $this->paginator->first(__('First'),
                                    array('class' => 'first paginate_button'),
                                    null,
                                    array('class' => 'paginate_button_disabled')
                                    );
        echo $this->Paginator->prev(__('Previous'),
                                    array('class' => 'previous paginate_button'),
                                    null,
                                    array('class' => 'paginate_button_disabled')
                                    );
        echo $this->Paginator->numbers(array('class' => 'paginate_button','modulus' => 2,'separator' => FALSE));
        echo $this->Paginator->next(__('Next'),
                                    array('class' => 'next paginate_button'),
                                    null,
                                    array('class' => 'paginate_button_disabled')
                                    );
        echo $this->paginator->last(__('Last'),
                                    array('class' => 'first paginate_button'),
                                    null,
                                    array('class' => 'paginate_button_disabled')
                                    );
        ?>
        </div>
        <div class="pagination">
        <?php
        if(!empty($users)) {
            $datetime_from = isset($this->request->query['datetime_from']) ? $this->request->query['datetime_from'] : '';
            $datetime_to = isset($this->request->query['datetime_to']) ? $this->request->query['datetime_to'] : '';
            echo $this->Form->create('Report',array(
                'class'         => 'row-fluid',
                'url'           => array(
                'controller'    => 'reports',
                'action'        => 'unapprovedLessons',
                '?'             => array(
                    'datetime_from' => $datetime_from,
                    'datetime_to'   => $datetime_to,
					'searchTxt' => isset($this->request->query['searchTxt']) ? $this->request->query['searchTxt'] : ''
                ),
            )));
            echo $this->Form->input('perPage', array(
                'options'   => $perPageDropDown,
                'selected'  => $perPage, 
                'id'        => 'dropdown' 
            ));

            $this->Form->end();
        }
        ?>
        </div>
    </div>
    <div id="drivingtestaproving" style="display: none;" title="bestået">
        har eleven bestået
    </div>	
<script type="text/javascript">
        jQuery(document).on('click', '.view-booking-approval', function(e){
            var $id     = jQuery(this).data('id');
            var $student_id     = jQuery(this).data('student_id');
            var $booking_type  = jQuery(this).data('booking_type');
            var $color  = '25c525';
            var $status  = jQuery(this).data('status');

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

            jQuery( "#booking-approval" ).find('.date').html( jQuery(this).data('date') );
            jQuery( "#booking-approval" ).find('.time').html( jQuery(this).data('book_time') );

            if( $booking_type == 'Køretime' ||  $booking_type == 'Køreprøve' ){
                jQuery( "#booking-approval" ).find('.student-name').html( jQuery(this).data('student_name') ).show();
                jQuery( "#booking-approval" ).find('.student-number').html( jQuery(this).data('student_number') ).show();
            } else if( $booking_type == 'Teori') {
                jQuery( "#booking-approval" ).find('.city-name').html( jQuery(this).data('city_name') ).show();
            }

            var $note = jQuery(this).find('.view-note').text();
            if( $note != '' && $note != undefined ) {
                jQuery( "#booking-approval" ).find('.note').html( $note ).show();
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
		
        jQuery('#drivingtestaproving').dialog({
            autoOpen        : false,
            modal           : true,
            width           : 400,
            close           : function(ev, ui) { 
                jQuery('#drivingtestaproving').dialog('close');
            }
        });
		

        jQuery(document).on('click', '.btn_booking_approval', function(e){
            var $value = parseInt( jQuery(this).val() );
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
		
        jQuery(document).on('click', '#btn_success', function(e){
            window.location.reload();
        });
		
        jQuery(document).on('click', '.delete-default-link', function(e){
            jQuery( "#booking-delete" ).dialog('close');
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
    </script>

    <div class="booking-approval" id="booking-approval">
        <form class="TEST"></form>
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
            </div>

            <div class="footer text-center">
                <button type="button" value="3" class="app-3 btn_booking_approval">SLET</button>
                <button type="button" value="2" class="app-2 btn_booking_approval">UDEBLEVET</button>
                <button type="button" value="1" class="app-1 btn_booking_approval">GODKEND</button>
            </div>
        </form>
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
    <a class="ui-dialog-titlebar-close ui-corner-all ui-state-hover delete-default-link" style="top: 10px;right: 0;" role="button"><span data-dialog_name="booking-delete"  class="ui-icon close_dialog ui-icon-closethick"></span></a>
    <div class="">
        <div class="txt_sucess">
            BEKRÆFT AT BOOKINGEN SKAL<span>SLETTES</span>
        </div>
        <div>
            <button type="button" value="SLET" class="btn_booking_approval" style="background-color: #fff">BEKRÆFT</button>
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

    
</div></div></div>