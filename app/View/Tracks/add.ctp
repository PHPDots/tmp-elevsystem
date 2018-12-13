<div class="inner-content">
    <?php 
        $title = ($isEdit)?__('Edit Track'):__('Add Track');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-road"></i>'
        )); 
    ?>
    <div class="row-fluid"><div class="widget">
        <?php
            echo $this->Form->create('Track',array(
                'type'  => 'post',
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Track Details'); ?></h5>
        </div>        
        <div class="widget-content no-padding">     
            
            <div class="form-row">
                <label class="field-name"><?php echo __('Track Name'); ?>:</label>
                <div class="field">
                    <?php 
                    echo $this->Form->input('name',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Track'), 
                    ));
                    ?>
                    <div class="clearfix"></div>
                </div>
            </div>            
            <div class="form-row">
                <label class="field-name"><?php echo __('Area'); ?>:</label>
                <div class="field">
                    <?php 
                    echo $this->Form->select('area_id',$areas,array(
                        'label' => FALSE,
                        'div'   => NULL,
                        'class' => 'span12',
                        'empty' => FALSE,
                    ));
                    ?>
                </div>
            </div>
            <div class="form-row">
                <label class="field-name"><?php echo __('Status'); ?>:</label>
                <div class="field">
                    <?php 
                    $statuses = array('active' => 'Active','inactive' => 'Inactive'); 
                    echo $this->Form->radio('status',$statuses,array(
                        'type'          => 'text',
                        'label'         => TRUE,
                        'div'           => NULL,
                        'class'         => 'span12',
                        'legend'        => FALSE,
                        'value'         => ($isEdit == FALSE) ? 'active' : $track['Track']['status'],
                     ));
                   ?>
                    <div id="txt_email_id_error" class="error-message"></div>
                </div>
            </div>                        
            
            <div class="form-row">                
            <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
            <div class="field" id="formControlls">
            <?PHP
                $btnName = ($isEdit)?'Update':'Add';               
                echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                    'class' => 'button button-green',
                    'type'  => 'submit',
                ),
                    array('escape' => FALSE)                            
                );
            ?>           
            <?PHP
                echo $this->Html->link(
                            '<i class="icon-remove icon-white"></i> Cancel',
                            array('action' => 'index'),
                            array('class' => 'button button-red',
                                'escape' => FALSE,)
                );
            ?>
            </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div></div>
</div>
