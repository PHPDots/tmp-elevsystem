<div class="inner-content">
    <?php 
    $title = ($isEdit)?__('Edit Company'):__('Add Company');
    $this->Html->pageInnerTitle($title,array(
        'icon'  => '<i class="fa fa-file-text"></i>'
    ));
    ?>
    <div class="row-fluid"><div class="widget">
        <?php 
        echo $this->Form->create('Company',array(
            'type'  => 'post',
            'class' => 'form-horizontal'
        ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('Company Details'); ?></h5>
        </div>
        <div class="widget-content no-padding">
            <div class="form-row">
                <div class="span6">
                    <label class="span3"><?php echo __('Company'); ?>:</label>
                    <div class="span9">
                        <?php 
                        echo $this->Form->input('name',array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Enter Company Name'),
                        ));
                        echo $this->Form->hidden('id');
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_name_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6">
                    <label class="span3"><?php echo __('Company Nick Name'); ?>:</label>
                    <div class="span9">
                        <?php 
                        echo $this->Form->input('nick_name',array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Enter Company Nick Name'),
                        ));
                        ?>
                        <div class="clearfix"></div>
                        <div id="txt_nick_name_error" class="error-message"></div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="span6">
                    <label class="span3"><?php echo __('City'); ?>:</label>
                    <div class="span9">
                       <?php 
                            echo $this->Form->select('city_id',$city,array(
                                'label'     => false,
                                'div'       => null,
                                'class'     => 'span12',
                                'empty'     => __('Select City'),
                            ));
                            
                            ?>
                        <div class="clearfix"></div>
                    </div>
                </div> 
            </div> 
            <?php if($isEdit && $currentUser['User']['role'] == 'admin') { ?>
                <div class="form-row">
                    <div class="span6">
                    <label class="span3"><?php echo __('User Status'); ?>:</label>
                    <div class="span9">
                        <label>
                            <?php
                            $argsactive   = array(
                                'hiddenField'   => FALSE,
                                'checked'       => ($company['Company']['status'] == 'active') ? TRUE : TRUE,
                            );
                            $argsDeactive   = array(
                                'hiddenField'    => FALSE,
                                'checked'       => ($company['Company']['status'] == 'inactive') ? TRUE : '',
                            );

                            echo $this->Form->radio('status',array('active' => __('Active')),$argsactive);
                            ?>
                        </label>
                        <label>
                            <?php 
                            echo $this->Form->radio('status',array('in_active' => __('In Active')),$argsDeactive);
                            ?>
                        </label>                        
                    </div>
                    </div>
                </div>
            <?php } ?>
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