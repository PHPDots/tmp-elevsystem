<div class="student_widget">
<div class="widget-header">
    <h5><?php echo __('Student Information'); ?></h5>                
</div>
<?php
echo $this->Form->create('BookingTrack',array(
    'action'    => 'updateTrackUser',
    'type'      => 'post',
    'class'     => 'studentForm',
));

echo $this->Form->hidden('booking_track_id',array(
    'type'          => 'text',
    'label'         => FALSE,
    'div'           => NULL,
    'legend'        => FALSE,
    'hiddenField'   => FALSE,
    'id'            => 'booking_track_id'
));
?>

<div class="form-row">
    <div class="span4">
    <label class="span3"><?php echo __('Name'); ?></label>
    <div class="span9">
        <?php 
        echo $this->Form->input((1).'.name',array(
            'type'          => 'text',
            'label'         => FALSE,
            'div'           => NULL,
            'legend'        => FALSE,
            'hiddenField'   => FALSE,
            'id'            => 'name',
            'class'         => 'span12 input_field'
        ));
        echo $this->Form->input((1).'.id',array(
            'type'          => 'hidden',
            'id'            => 'id',
        ));
        echo $this->Form->input((1).'.status',array(
            'type'          => 'hidden',
            'id'            => 'status',
        ));
        ?>
        <div class="clearfix"></div>
        <div id="txt_error_name1" class="error-message"></div>
    </div>
    </div>

    <div class="span4">
    <label class="span3"><?php echo __('Phone'); ?></label>
    <div class="span9">
        <?php 
        echo $this->Form->input((1).'.phone',array(
            'type'          => 'text',
            'label'         => FALSE,
            'div'           => NULL,
            'legend'        => FALSE,
            'hiddenField'   => FALSE,
            'id'            => 'phone_no',
            'class'         => 'span12 input_field'
        ));
        ?>
        <div class="clearfix"></div>
        <div id="txt_error_phone1" class="error-message"></div>
    </div>
    </div>

    <div class="span4">
    <label class="span3"><?php echo __('Date of Birth'); ?></label>
    <div class="span9">
        <?php 
        echo $this->Form->input((1).'.date_of_birth',array(
            'type'          => 'text',
            'label'         => FALSE,
            'div'           => NULL,
            'legend'        => FALSE,
            'hiddenField'   => FALSE,
            'id'            => 'dob',
            'class'         => 'span12 input_field',
            'maxYear'       => 18,
            'minYear'       => 50,
        ));
        ?>
        <div class="clearfix"></div>
        <div id="txt_error_date_of_birth1" class="error-message"></div>
    </div>
    </div>
</div>
<div class="form-row">
    <div class="span6">
        <label class="span2"><?php echo __('Adresse'); ?></label>
        <div class="span9">
            <?php 
            echo $this->Form->input((1).'.address',array(
                'type'          => 'text',
                'label'         => FALSE,
                'div'           => NULL,
                'legend'        => FALSE,
                'hiddenField'   => FALSE,
                'id'            => 'address',
                'rows'          => 5,
                'cols'          => 19,
                'style'         => 'resize: vertical;',
                'class'         => 'span12 input_field'
            ));
            ?>
            <div class="clearfix"></div>
            <div id="txt_error_address1" class="error-message"></div>
        </div>
    </div>
    <div class="span4">
    <label class="span3"><?php echo __('Postnummer'); ?></label>
    <div class="span9">
        <?php 
        echo $this->Form->input((1).'.zip_code',array(
            'type'          => 'text',
            'label'         => FALSE,
            'div'           => NULL,
            'legend'        => FALSE,
            'hiddenField'   => FALSE,
            'id'            => 'zip_code',
            'class'         => 'span12 input_field',
            'maxYear'       => 18,
            'minYear'       => 50,
        ));
        ?>
        <div class="clearfix"></div>
        <div class="error-message"></div>
         <div id="zip_code1" class="error-message"></div>
    </div>
    </div>
    <div class="span4">
    <label class="span3"><?php echo __('City'); ?></label>
    <div class="span9">
        <?php 
        echo $this->Form->input((1).'.city',array(
            'type'          => 'text',
            'label'         => FALSE,
            'div'           => NULL,
            'legend'        => FALSE,
            'hiddenField'   => FALSE,
            'id'            => 'city',
            'class'         => 'span12 input_field',
            'maxYear'       => 18,
            'minYear'       => 50,
        ));
        ?>
        <div class="clearfix"></div>
        <div class="error-message"></div>
         <div id="city1" class="error-message"></div>
    </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="form-row add_student hide_details">
    <div class="span9">
        <label class="span2"><?php echo __('Select Student'); ?></label>
        <div class="span10">
            <div class="span3">
                <?php 
                echo $this->Form->input((1).'.student_name',array(
                    'label'     => FALSE,
                    'class'     => 'student_name span12',
                ));
                ?>
                <div class="clearfix"></div>
            </div>
            <div class="span1 center "><?php echo __(' OR '); ?></div>
            <div class="span2 center addStudentBtn">
                <?php echo $this->Form->button(__('Add Student'),array(
                    'type'  => 'button',
                    'class' => 'button button-blue',
                    'id'    => 'addStudent'
                )); ?>
            </div>
        </div>
    </div>
</div>
<div class="newStudent" style="display:none;">
</div>
<div class="form-row form-btns">
    <div class="pull-left">
    <?php
    echo $this->Form->button('<i class="icon-ok icon-white"></i> '.__('Update'),array(
        'class' => 'button button-green updateUser',
        'type'  => 'button',
//        'id'    => 'updateUser',
    ),
        array('escape' => FALSE)                            
    );
    ?>
    </div>
    <div id="submitForm" class="fly_loading pull-left field" style="display:none"><?php  echo $this->Html->image("submit-form.gif");?></div>
</div>
<div class="clearfix"></div>

<?php echo $this->Form->end(); ?>
</div>