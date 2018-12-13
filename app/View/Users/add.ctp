<?php $this->append('script'); ?>
<script type="text/javascript">    

var uploadProfilePic = {
    'targetHref'    : '<?php echo $this->Html->url(array('controller'=>'attachments','action'=>'add','fancybox','profile_picture','multiple' => 'false')) ?>',            
    'afterSelect'   : function(data){                
        if(data.length>0){
            data = data[0];
            imgpath  = '<?php echo $this->Html->imagePreviewUrl('img_id'); ?>';                    
            imgpath  = imgpath.replace("img_id",data.id);  

            jQuery(this).parent().find('.ajax-img').html('<img src="'+ imgpath +'" />');
            jQuery(this).parent().find('.ajax-avatar-id').val(data.id);
        }
    }
};

jQuery(document).ready(function(){
    
    jQuery('.profileImage').ossuploadergallery(uploadProfilePic);
    
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
<?php $Role       = CakeSession::read("Auth.User.role"); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="widget">
            <?php
                echo $this->Form->create('User',array(
                    'class' => 'form-horizontal'
                ));
            ?>
            <div class="widget-header col-xs-12">
                <h5><?php echo __('User account'); ?></h5>
            </div>   
            <div class="widget-content col-xs-12 no-padding">   
                <?php if($Role != 'student') { ?>   
                <div class="form-row form-group">
                    <label class="col-xs-3 field-name"><?php echo __('Profile Picture'); ?>:</label>
                    <div class="col-xs-9">
                        <div class="col-xs-8">
                            <div class="avatar">
                                <div class="ajax-img pull-left">
                                    <?php if($isEdit && ($user['avatar_id'] != 0)){ ?>
                                    <img src="<?php echo $this->Html->imagePreviewUrl($user['avatar_id']); ?>" />    
                                    <?php
                                       }else{
                                        echo $this->Html->image('user-image.png'); 
                                       }
                                    ?>
                                </div> 
                                <?php 
                                    $val = isset($user['avatar_id'])?$user['avatar_id']:'';
                                    echo $this->Form->input('avatar_id',array(
                                        'type'   => 'hidden',
                                        'class'  => 'ajax-avatar-id',
                                        'value'  => $val
                                    ));
                                ?>
                                <a href="javascript:" class="profileImage btn btn-primary"><?php echo __('Upload image');?></a>                                 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <label class="col-xs-3 field-name" for="firstname"><?php echo __('Name'); ?>:</label>
                    <div class="col-xs-9">
                        <span class="col-xs-6">
                        <?php 
                            $val = isset($user['firstname'])?$user['firstname']:'';
                            echo $this->Form->input(
                                'firstname',array(
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'col-xs-12',
                                    'placeHolder'   => __('First Name'), 
                                    'value'         => $val
                                ));
                        ?>
                            <div id="txt_firstname_error" class="error-message"></div>
                        </span>  
                        <span class="col-xs-6">
                        <?php 
                            $val = isset($user['lastname'])?$user['lastname']:'';
                            echo $this->Form->input(
                                'lastname',array(
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'col-xs-12',
                                    'placeHolder'   => __('Last Name'),     
                                    'value'         => $val
                            ));
                        ?>
                            <div id="txt_lastname_error" class="error-message"></div>
                        </span>    
                        <div class="clearfix"></div>
                    </div>
                </div>      
                <div class="form-row form-group">
                    <label class="col-xs-3 field-name" for="lastnmae"><?php echo __('Username'); ?>:</label>
                    <div class="col-xs-9">
                        <span class="col-xs-6">
                        <?php 
                            $val = isset($user['username'])?$user['username']:'';
                            echo $this->Form->input(
                                'username',array(
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'col-xs-12',
                                    'placeHolder'   => __('Username'),     
                                    'value'         => $val
                            ));
                        ?>
                            <div id="txt_username_error" class="error-message"></div>
                        </span>  
                    </div>
                </div>
                <?php } ?>
                <div class="form-row form-group">
                    <label class="col-xs-3 field-name" for="email"><?php echo __('Email Address'); ?>:</label>
                    <div class="col-xs-9">
                        <span class="col-xs-6">
                            <?php 
                               $val    = isset($user['email_id'])?$user['email_id']:'';
                               echo $this->Form->input('email_id',array(
                                   'type'          => 'text',
                                   'label'         => false,
                                   'div'           => null,
                                   'class'         => 'col-xs-12',
                                   'placeHolder'   => __('Email Address'),
                                   'value'         => $val
                                   ));
                            ?>
                            <div id="txt_email_id_error" class="error-message"></div>
                        </span>  
                    </div>
                </div>          
                <div class="form-row form-group">
                    <label class="col-xs-3 field-name" for="email"><?php echo __('Phone Number'); ?>:</label>
                    <div class="col-xs-9">
                        <div class="col-xs-6">
                            <?php 
                                $val    = isset($user['phone_no'])?$user['phone_no']:'';
                                echo $this->Form->input('phone_no',array(
                                    'type'          => 'text',
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'col-xs-12',
                                    'placeHolder'   => __('Phone Number'),
                                    'value'         => $val
                                    ));
                            ?>
                             <div id="txt_phone_no_error" class="error-message"></div>
                        </div>                        
                    </div>
                </div> 
                <div class="form-row form-group">
                    <label class="col-xs-3 field-name" for="email"><?php echo __('Other Phone Number'); ?>:</label>
                    <div class="col-xs-9">
                        <div class="col-xs-6">
                            <?php 
                                $val    = isset($user['other_phone_no'])?$user['other_phone_no']:'';
                                echo $this->Form->input('other_phone_no',array(
                                    'type'          => 'text',
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'col-xs-12',
                                    'placeHolder'   => __('Other Phone Number'),
                                    'value'         => $val
                                    ));
                            ?>
                             <div id="txt_phone_no_error" class="error-message"></div>
                        </div>                        
                    </div>
                </div>  
                <?php if($Role != 'student') { ?> 
                <div class="form-row form-group">                    
                    <label class="col-xs-3"><?php echo __('Standard City'); ?>:</label>
                    <div class="col-xs-9">
                        <div class="col-xs-6">
                            <?php 
                                $val = isset($user['city'])?$user['city']:'';
                                echo $this->Form->input('city',array(
                                    'type'          => 'text',
                                    'label'         => false,
                                    'div'           => null,
                                    'class'         => 'col-xs-12',
                                    'placeHolder'   => __('Standard City'),
                                    'value'         => $val
                                ));
                            ?> 
                            <div id="txt_city_error" class="error-message"></div>
                        </div>
                    </div>
                   <div class="clearfix"></div>
                </div>

                 <?php if($isEdit){ ?>
                    <div class="form-row form-group">
                        <label class="col-xs-3 field-name" for="standard"><?php echo __('Do you want to reset your password?');?></label>
                        <div class="col-xs-9">
                            <div class="col-xs-6">
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
                    </div>

                    <?php 
                        $class  = ($isEdit)?'form':'';
                        $id     = ($isEdit)?'reset_password_form':'';
                    ?>
                    <div class="<?php echo $class; ?>" id="<?php echo $id; ?>">
                        <div class="form-row form-group">
                            <label class="col-xs-3"><?php echo __('Password'); ?>:</label>
                            <div class="col-xs-9">
                                <div class="col-xs-6">
                                     <?php echo $this->Form->input('password',array(
                                                    'type'          => 'password',
                                                    'label'         => false,
                                                    'div'           => null,
                                                    'class'         => 'col-xs-12',
                                                    'placeHolder'   => __('Enter password')
                                            ));
                                    ?>
                                    <div id="txt_password_error" class="error-message"></div>
                                </div>                               
                            </div>                    
                        </div>    
                        <div class="form-row form-group">
                            <label class="col-xs-3"><?php echo __('Confirm Password'); ?>:</label>
                            <div class="col-xs-9">
                                <div class="col-xs-6">
                                    <?php echo $this->Form->input('confirm_password',array(
                                                    'type'          => 'password',
                                                    'label'         => false,
                                                    'div'           => null,
                                                    'class'         => 'col-xs-12',
                                                    'placeHolder'   => __('Enter password')
                                            ));
                                    ?>
                                    <div id="txt_confirm_password_error" class="error-message"></div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php } ?>
                <div class="form-row form-group">                
                    <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
                    <div class="col-xs-12" id="formControlls">
                        <?PHP
                            $btnName = ($isEdit)?'Update':'Add';               
                            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                                'class' => 'btn btn-success',
                                'type'  => 'button',
                                'id'    => 'formSubmit'
                            ),
                                array('escape' => FALSE)                            
                            );
                        ?>           
                        <?PHP
                            echo $this->Html->link(
                                        '<i class="icon-remove icon-white"></i> Cancel',
                                        array('action' => 'view'),
                                        array('class' => 'btn btn-danger',
                                            'escape' => FALSE,)
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
