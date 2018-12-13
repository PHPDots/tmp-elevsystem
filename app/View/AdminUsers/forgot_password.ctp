<div class="login-container">   
    <div class="login departmentContainer">
        <h3><?php echo __('Reset Password');?></h3>        
        <?php 
            echo $this->Form->create('User',array(
                'url'   => array(
                    'controller'        => 'adminusers',
                    'action'            => 'forgotPassword',                    
                )
            ));

            echo $this->Form->input('verification_data',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'login-input',
                'placeHolder'   => __('Enter your username or email id here...'),
            ));
            
       ?>
        <button type="submit"  class="fRight button button-blue"><?php echo __('Reset Password'); ?></button>
        <?php
            
            echo $this->Form->end();
        ?>
        <div class="clearfix"></div>        
    </div>
    <span dir="<?php // echo $lang_dir;?>">
        <?php echo __("&copy; Lisabeth"); ?>
    </span>
</div>