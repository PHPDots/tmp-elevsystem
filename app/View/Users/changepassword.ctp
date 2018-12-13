
<?php $Role       = CakeSession::read("Auth.User.role"); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="widget">
            <?php
                echo $this->Form->create('User',array(
                    'class' => 'form-horizontal'
                ));
            ?>
            <?php echo $this->Form->input('is_login_firsttime',array(
                            'type'          => 'hidden',
                            'value'         => '0',
                    ));
            ?>
            <div class="widget-header col-xs-12">
                <h5><?php echo __('User account'); ?></h5>
            </div>   
            <div class="widget-content col-xs-12 no-padding">

                <div class="form-row form-group">
                    <label class="col-xs-3 field-name"><?php echo __('Kodeord'); ?>:</label>
                    <div class="col-xs-9">
                        <div class="col-xs-6">
                             <?php echo $this->Form->input('password',array(
                                            'type'          => 'password',
                                            'label'         => false,
                                            'div'           => null,
                                            'class'         => 'col-xs-12',
                                            'placeHolder'   => __('indtast kodeord')
                                    ));
                            ?>
                            <div id="txt_password_error" class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <label class="col-xs-3"><?php echo __('Bekræft kodeord'); ?>:</label>
                    <div class="col-xs-9">
                        <div class="col-xs-6">
                            <?php echo $this->Form->input('confirm_password',array(
                                            'type'          => 'password',
                                            'label'         => false,
                                            'div'           => null,
                                            'class'         => 'col-xs-12',
                                            'placeHolder'   => __('indtast kodeord')
                                    ));
                            ?>
                            <div id="txt_confirm_password_error" class="error-message"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-row form-group">
                    <div class="col-xs-12" id="formControlls">
                        <?PHP
                            $btnName = ($isEdit) ? 'Opdater':'Add';
                            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                                'class' => 'btn btn-success',
                                // 'type'  => 'submit',
                                'type'  => 'button',
                                'id'    => 'formSubmit'
                            ),
                                array('escape' => FALSE)
                            );
                        ?>           
                        <?PHP
                            echo $this->Html->link
                            (
                                '<i class="icon-remove icon-white"></i> Annuller',
                                array('action' => 'view'),
                                array('class' => 'btn btn-danger','escape' => FALSE)
                            );
                        ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <?php echo $this->Form->end(); ?>
        </div>
        
    </div>
</div>
