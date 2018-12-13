<div class="login-container">
    <?php if($this->Session->check('Message.flash')){ ?>
        <div style="padding-bottom:14%;"><?php echo $this->Session->flash(); ?></div>
    <?php } ?>
    <div class="login">
        <div class="avatar">
            <a href="">
                <?php echo $this->Html->image("lisabeth/logo2.png",array(''));?>
            </a>
        </div>
        <?php echo $this->Session->flash('auth'); ?>        
        <?php 
            echo $this->Form->create('User');

            echo $this->Form->input('password',array(
                            'label'         => '',
                            'div'           => null,
                            'class'         => 'password-input',
                            'placeHolder'   => __('Enter your password here...'),
                        ));
            
            echo $this->Form->input('confirm_password',array(
                            'label'         => '',
                            'div'           => null,
                            'type'          => 'password',
                            'class'         => 'password-input',
                            'placeHolder'   => __('Enter your Confirm password here...'),
                        ));            
           
        ?>
<!--        <div class="remember fLeft">
            <label class="form-button">
                <input type="checkbox" name="data['User']['remember_me']" id="UserRememberMe"> <?php echo __('Remember me'); ?>
            </label>
        </div>-->
        <button type="submit"  class="fRight button button-blue"  dir="<?php // echo $lang_dir;?>"><?php echo __('Reset Password'); ?></button>
        <div class="clearfix"></div>
        <!--</form>-->
    </div>
    <span dir="<?php // echo $lang_dir;?>">
        <?php echo __("&copy; $sitename"); ?>
    </span>
</div>
