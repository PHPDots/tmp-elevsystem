<div class="inner-content">
    <?php $this->Html->pageInnerTitle(__('Add Email Settings template')); ?>
    <div class="row-fluid">
    <div class="widget">
        <?php
            echo $this->Form->create('EmailTemplateSetting',array(
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="form-row">
            <label class="field-name"><?php echo __('Setting Name'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->input('name',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Name of the Email Settings Template'),
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Setting Slug'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->input('template_type',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('System Friendly Name of the Settings Template'),
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Body'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->textarea('body',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12 cleditor',
                        'id'            => 'cleditor',
                        'rows'          => 10,
                        'placeHolder'   => __('Settings E-mail Boby')
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('From'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->input('from',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('From E-mail Address')
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Username'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->input('username',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('User name for the E-mail Address')
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Password'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->input('password',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('password for the E-mail Address')
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Mail Types'); ?>:</label>
            <div class="field">
            <?php
                echo $this->Form->select('mailtype',$EmailTypes,array(
                    'id'        => 'mailtype',
                    'empty'     => __('Select E-mail Type to be used')
                ));
                ?>
            </div>
        </div>
        <div class="form-row">
            <label class="field-name"><?php echo __('Header'); ?>:</label>
            <div class="field">
                <?php 
                    echo $this->Form->textarea('headers',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'rows'          => 3,
                        'placeHolder'   => __('Header to be passed for the E-mail')
                    ));
                ?>
            </div>
        </div>
        <div class="form-row">                
            <div class="field">
            <?PHP
                echo $this->Form->button('<i class="icon-ok icon-white"></i> '.__(' Save'),array(
                                'class'         => 'button button-blue',
                                'type'          => 'submit',
                            ),array(
                                'escape' => FALSE
                            ));
            ?>
                
            <?PHP
            echo $this->Html->link(
                        '<i class="icon-remove icon-white"></i>'.__(' Cancel'),
                        array('action' => 'index'),
                        array(
                            'class'     => 'button button-red',
                            'escape'    => FALSE,
                        ));
            ?>
            </div>
        </div>
        <div class="clearfix">&nbsp;</div>
    </div>
    </div>
</div>

