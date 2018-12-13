<div class="login-container">    
    <div class="login">  
<!--        <div class="avatar">
            <a href="#">
                <?php echo $this->Html->image("lisabeth/logo.png",array(''));?>
            </a>
        </div>-->
        <?php if(!empty($message)) { ?>
        <label class="message <?php echo $class?>"><?php echo $message;?></label>
        <?php } ?>        
        <?php 
        $url    = array(
            'controller'    => 'adminusers', 
            'action'        => 'login'
        );

        if(isset($this->request->query['iframe']) && !empty($this->request->query['iframe'])){
            $url['?']       = array(
                'iframe'    => $this->request->query['iframe']
            );
        }
        ?>
        <h1 class="login_header">Login</h1>
        <div class="clearfix"></div>
        <?php
        
        echo $this->Form->create('User', array(
            'url' => $url
        ));

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
        <?php echo $this->Html->link(__('Forgot password?'), array('controller'=>'adminusers', 'action' => 'forgotPassword'));?>
    </div>
    <span>
        <?php echo __("&copy; Lisabeth"); ?>
    </span>
</div>
