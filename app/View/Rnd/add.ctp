<?php $this->append('script'); ?>
<script type="text/javascript">    
jQuery(document).ready(function(){
    jQuery('.form,.fly_loading').hide();     
  
    jQuery('#reset_password_link').click(function(){
        if(jQuery(this).is(':checked')){           
           jQuery('#reset_password_form').slideDown();               
        }else{
           jQuery('#reset_password_form').slideUp();
        }

    });
});
</script>
<?php $this->end(); ?>
<div class="inner-content">
    <div class="row-fluid addUserInfo-container">
        <div class="span6">        
            <?php  $title = ($isEdit)?__('Edit Profile'):__('Add User'); ?>
            <h5 class="addUserTitle"><?php echo $title; ?></h5>
        </div>
        <div class="clear"></div>
    </div> 
    <div class="row-fluid"><div class="widget">
        <?php
//            echo $this->Form->create('User',array('controller' => 'services', 
//                'action' => 'users/add'
//                ),array('class' => 'form-horizontal'
//            ));
        ?>
        <form action="<?php echo (!$isEdit) ? '/the_care/services/users/add' : '/the_care/services/users/edit/'.$user['id'] ?>" method="post" class="form-horizontal">
        <div class="widget-header">
            <h5><?php echo __('User account'); ?></h5>
        </div>        
        <div class="widget-content no-padding">     
            
            <div class="form-row">
                <label class="field-name" for="firstname"><?php echo __('Name'); ?>:</label>
                <div class="field">
                    <span class="span6">
                    <?php 
                        $val = isset($user['firstname'])?$user['firstname']:'';
                        echo $this->Form->input(
                            'firstname',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                                'placeHolder'   => __('First Name'), 
                                'value'         => $val
                            ));
                    ?>
                        <div id="txt_firstname_error" class="error-message"></div>
                    </span>  
                    <span class="span6">
                    <?php 
                        $val = isset($user['lastname'])?$user['lastname']:'';
                        echo $this->Form->input(
                            'lastname',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                                'placeHolder'   => __('Last Name'),     
                                'value'         => $val
                        ));
                    ?>
                        <div id="txt_lastname_error" class="error-message"></div>
                    </span>    
                    <div class="clearfix"></div>
                </div>
            </div>            
            <div class="form-row">
                <label class="field-name" for="lastnmae"><?php echo __('Username'); ?>:</label>
                <div class="field">
                    <span class="span6">
                    <?php 
                        $val = isset($user['username'])?$user['username']:'';
                        echo $this->Form->input(
                            'username',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                                'placeHolder'   => __('Username'),     
                                'value'         => $val
                        ));
                    ?>
                        <div id="txt_username_error" class="error-message"></div>
                    </span>  
                </div>
            </div>
            <?php if(!$isEdit){ ?>
                <div class="form-row">
                    <label class="field-name"><?php echo __('Password'); ?>:</label>
                    <div class="field">
                    <?php echo $this->Form->input('password',array(
                                        'type'          => 'password',
                                        'label'         => false,
                                        'div'           => null,
                                        'class'         => 'span12',
                                        'placeHolder'   => __('Enter password')
                                ));
                    ?>
                        <div id="txt_password_error" class="error-message"></div>
                    </div>
                </div>    
            <?php } ?>
            <div class="form-row">
                <label class="field-name" for="email"><?php echo __('Email Address'); ?>:</label>
                <div class="field">
                    <?php 
                       $val    = isset($user['email_id'])?$user['email_id']:'';
                       echo $this->Form->input('email_id',array(
                           'type'          => 'text',
                           'label'         => false,
                           'div'           => null,
                           'class'         => 'span12',
                           'placeHolder'   => __('Email Address'),
                           'value'         => $val
                           ));
                   ?>
                    <div id="txt_email_id_error" class="error-message"></div>
                </div>
            </div>          
            <div class="form-row">
                <label class="field-name" for="email"><?php echo __('Phone Number'); ?>:</label>
                <div class="field">
                    <?php 
                       $val    = isset($user['phone_no'])?$user['phone_no']:'';
                       echo $this->Form->input('phone_no',array(
                           'type'          => 'text',
                           'label'         => false,
                           'div'           => null,
                           'class'         => 'span12',
                           'placeHolder'   => __('Phone Number'),
                           'value'         => $val
                           ));
                   ?>
                    <div id="txt_phone_no_error" class="error-message"></div>
                </div>
            </div>    
            <div class="form-row">
                <label class="field-name" for="email"><?php echo __('Role'); ?>:</label>
                <div class="field">
                    <?php 
                    $val    = isset($user['role'])?$user['role']:'';
                    echo $this->Form->select('role',$roles,
                        array(                            
                            'class' => 'uniform',                                
                            'empty' => __('Select Role'),
                            'value' => $val
                        ));
                    ?>
                    <div id="txt_role_error" class="error-message"></div>
                </div>
            </div>
            <?php if($isEdit){ ?>
            <div class="form-row">
                <label class="field-name" for="standard"><?php echo __('Do you want to reset your password?');?></label>
                <div class="field noSearch">
                    <label>
                        <?PHP
                            echo $this->Form->checkbox('reset_password', 
                                array(
                                    'class' => 'uniform',
                                    'id'    => 'reset_password_link'
                                )
                            );                    
                            echo __('Reset Password');
                        ?>
                    </label>
                </div>
            </div>
           
            <?php 
                $class  = ($isEdit)?'form':'';
                $id     = ($isEdit)?'reset_password_form':'';
            ?>
            <div class="<?php echo $class; ?>" id="<?php echo $id; ?>">
                <div class="form-row">
                    <label class="field-name"><?php echo __('Password'); ?>:</label>
                    <div class="field">
                    <?php echo $this->Form->input('password',array(
                                        'type'          => 'password',
                                        'label'         => false,
                                        'div'           => null,
                                        'class'         => 'span12',
                                        'placeHolder'   => __('Enter password')
                                ));
                    ?>
                        <div id="txt_password_error" class="error-message"></div>
                    </div>                    
                </div>    
                <div class="form-row">
                    <label class="field-name"><?php echo __('Confirm Password'); ?>:</label>
                    <div class="field">
                    <?php echo $this->Form->input('confirm_password',array(
                                        'type'          => 'password',
                                        'label'         => false,
                                        'div'           => null,
                                        'class'         => 'span12',
                                        'placeHolder'   => __('Enter password')
                                ));
                    ?>
                        <div id="txt_confirm_password_error" class="error-message"></div>
                    </div>
                </div>
            </div>
             <?php if($user['status'] != 'deactive'){ ?>
                <div class="form-row">
                    <label class="field-name"><?php echo __('User Status'); ?>:</label>
                    <div class="field">
                        <label>
                           <?php
                           
                                $argssuspend    = array(
                                    'hiddenField'    => FALSE
                                );                        
                                $argsDeactive   = array(
                                    'hiddenField'    => FALSE
                                );   
                           
                                switch ($user['status']) {
                                    case 'active':
                                        $status1['suspend'] = __('Suspend');
                                        $status2['deactive']= __('Deactive');
                                        break;
                                    case 'suspend':
                                        $status1['active']  = __('Active');
                                        $status2['deactive']= __('Deactive');
                                        break;            
                                    default :
                                        $status1['suspend'] = __('Suspend');
                                        $status2['deactive']= __('Deactive');                                        
                                }
                               
                                echo $this->Form->radio('status',$status1,$argssuspend);
                            ?>
                        </label>
                        <label>
                            <?php                    
                                echo $this->Form->radio('status',$status2,$argsDeactive);
                            ?>
                        </label>                        
                    </div>
                </div>
            <?php } ?>
            
             <?php } ?>
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
        <?php //echo $this->Form->end(); ?>
        </form>
    </div>
    </div>
</div>

<?php $title = ($isEdit)?'User Edit':'User Add'; ?>

<div id="userAdd" style="display: none;" title="<?php echo $title; ?>">
    <p> </p>
</div>