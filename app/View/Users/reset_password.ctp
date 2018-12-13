<div class="logo-img">
    <a href="#">
    <?php
    echo $this->Html->image('logo.png',array(
        'alt'   => 'Lisabeth'
    ));
    ?>
    </a>
</div>
<div class="login-txt">
    <label><?php echo __('Student Portal'); ?></label>                
</div>
<!--Form_ct Starts Here -->
<div class="form_ct detail">
    <?php echo $this->Form->create('User'); ?>
        <div class="lbl"><?php echo __('Reset Password'); ?></div>
        <div class="form-group ">
            <?php
                echo $this->Form->input('password',array(
                    'label'         => '',
                    'div'           => null,
                    'class'         => 'form-control',
                    'placeHolder'   => __('Enter your password here...'),
                ));
            ?>
        </div>     
        <div class="form-group ">
            <?php
                echo $this->Form->input('confirm_password',array(
                    'label'         => '',
                    'div'           => null,
                    'type'          => 'password',
                    'class'         => 'form-control',
                    'placeHolder'   => __('Enter your Confirm password here...'),
                ));
            ?>
        </div>     
        <div class="form-group">
            <button name="login" id="login" value="Login" class="form-control btn "><?php echo __('Reset Password'); ?></button>
        </div>
        <?php
        if(!empty($message)){
        ?>
        <div class="alert alert-danger" role="alert">
            <strong><?php echo $message; ?></strong>
        </div>
        <?php
        }
            
    echo $this->Form->end();
    ?>
</div>

