<div class="login-container">    
    <div class="login">  
        <div class="avatar">
            <a href="#">
                <?php echo $this->Html->image("logo-image.png",array(''));?>
            </a>
        </div>
        <?php
            if(!empty($message)){
        ?>
        <label class="message <?php echo $class?>"><?php echo $message;?></label>
        <?php } ?>        
        <?php 
            echo $this->Form->create('User', array('url' => array('controller' => 'services', 'action' => 'users/login')));

            echo $this->Form->input('username',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'login-input',
                'placeHolder'   => __('Enter your username here...'),
                'type'          => 'text'
            ));

            echo $this->Form->input('password',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'password-input',
                'placeHolder'   => __('Enter your password here...'),
            ));
        ?>
        <div class="remember fLeft">
            <label class="form-button">
                <input type="checkbox" name="data['User']['remember_me']" id="UserRememberMe"> <?php echo __('Remember me'); ?>
            </label>
        </div>
        <button type="submit"  class="fRight button button-blue"  dir="<?php // echo $lang_dir;?>"><?php echo __('Login'); ?></button>
        <div class="clearfix"></div>
        <!--</form>-->
    </div>

    <div class="login-footer">        
        <div>
            <?php echo $this->Html->link(__('Forgot Password'), array('controller'=>'users', 'action' => 'forgotPassword'));?>
        </div>
    </div>
</div>
