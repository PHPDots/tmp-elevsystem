<div class="login-container">    
    <div class="login departmentContainer">        
        <h3><?php echo __('Enter Your Details');?></h3>
        <?php 
            echo $this->Form->create('User', array('url' => array('controller' => 'adminusers', 'action' => 'signup')));

            echo $this->Form->input('firstname',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'login-input',
                'type'          => 'text',
                'placeHolder'   => __('Enter your First Name here...'),
            ));
            
            echo $this->Form->input('lastname',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'login-input',
                'type'          => 'text',
                'placeHolder'   => __('Enter your Last Name here...'),
            ));
            
            echo $this->Form->input('username',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'login-input',
                'type'          => 'text',
                'placeHolder'   => __('Enter your User Name here...'),
            ));

            echo $this->Form->input('password',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'password-input',
                'type'          => 'password',
                'placeHolder'   => __('Enter your Password here...'),
            ));
            
            echo $this->Form->input('confirm_password',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'password-input',
                'type'          => 'password',
                'placeHolder'   => __('Enter your Confirm Password here...'),
            ));
            
            echo $this->Form->input('email_id',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'login-input',
                'type'          => 'text',
                'placeHolder'   => __('Enter your Email Id here...'),
            ));
            
            echo $this->Form->input('date_of_birth',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'login-input datepick',
                'type'          => 'text',
                'placeHolder'   => __('Enter Your Date of Birth...'),
            ));
            
            echo $this->Form->input('phone_no',array(
                'label'         => '',
                'div'           => null,
                'class'         => 'login-input',
                'type'          => 'text',
                'placeHolder'   => __('Enter Your Phone Number...'),
            ));
            
            echo $this->Form->select('role',$roles,array(
                'id'        => 'role',
                'empty'     => __('Select Role')
            ));            
        ?>        
        <button type="submit"  class="button button-blue"><?php echo __('Submit'); ?></button>
        <?php echo $this->Form->end(); ?>
        <div class="clearfix"></div>
        <!--</form>-->
    </div>
    <span>
        <?php echo __("&copy; Lisabeth"); ?>
    </span>
</div>
<div id="exc" style="display: none;"></div>

<div id="applicationFormsError">
    
</div>