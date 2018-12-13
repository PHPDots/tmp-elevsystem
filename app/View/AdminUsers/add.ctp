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
        jQuery('.addProduct').click(function(){
            var student = jQuery(this).attr('element-id');     
            jQuery('#studentId').val(student);
            jQuery('#addProductForm').dialog({
               autoOpen        : true,
               modal           : true
           });           
        });
        
        jQuery('#addProduct').click(function(){
            var student = jQuery('#studentId').val();
            var product = jQuery('#productId').val();  
            
            jQuery.ajax({
                url         : '<?php echo $this->Html->url(array('controller'  => 'products','action'  => 'studentProduct')); ?>',
                data        : 'student='+student+'&product='+product,
                dataType    : 'json',
                type        : 'GET',
                success     : function(data){
                    jQuery('#txt_product_id_error').hide();
                    
                    if(data.status  == 'error'){                    
                        jQuery('#txt_product_id_error').show().html(data.message);
                    }
                    
                    if(data.status  == 'success'){
                        jQuery('#addProductForm').dialog("close");
                        jQuery('#successMessage').find('p').html(data.message);
                        jQuery('#successMessage').dialog({
                            autoOpen        : true,
                            modal           : true,
                            buttons         : {
                                'Ok'        : function(){
                                    jQuery(this).html('Please Wait While We are redirecting you...');
                                    window.location='<?PHP echo $this->Html->url(array('controller' => 'adminusers','action'=>'students')); ?>';
                                }
                            }
                        });
                    }                    
                }
            });
        });
        
        jQuery('#closeProduct').click(function(){
            jQuery('#addProductForm').dialog("close");
        });
        jQuery('.profileImage').ossuploadergallery(uploadProfilePic);
        
        jQuery('.form,.fly_loading,.company').hide();     

        jQuery('#reset_password_link').click(function(){
            if(jQuery(this).is(':checked')){           
               jQuery('#reset_password_form').slideDown();               
            }else{
               jQuery('#reset_password_form').slideUp();
            }

        });
        
        jQuery('#UserRole').change(function(){
            if(jQuery(this).val() != 'student'){
                jQuery('.company').show();
                jQuery('.teacher').hide();
                jQuery('.teacherCompany').show();
                jQuery('.internalTeacher').hide();
                jQuery('.not_student').show();
                if(jQuery(this).val() == 'admin') {
                    jQuery('.teacherCompany').hide();
                }
                if(jQuery(this).val() == 'internal_teacher') {
                    jQuery('.internalTeacher').show();
                }
                
            } else {
                jQuery('.teacher').show();
                jQuery('.company').hide();
                jQuery('.internalTeacher').hide();
                jQuery('.not_student').hide();
            }
        });
        
        jQuery('.teacherAutoSuggest').keyup(function(){
            if(jQuery(this).val() == '') {
                jQuery('#teacherId').val('');
            }
        });
        
        jQuery('.teacherAutoSuggest').autocomplete({
            minLength   : 1,
            select: function( event, ui ) {
                jQuery('#teacherId').val(ui.item.sysvalue);
                jQuery(this).val(ui.item.label);
            },
            source      : function(request, response){
                jQuery.ajax({
                   url         : '<?php echo $this->Html->url(array('controller'=>'adminusers','action'=>'autoSuggest')); ?>/' + request.term+'/teacher',
                   dataType    : "json",
                   complete    : function(){

                   },
                   beforeSend  : function(){

                   },
                   success     : function(data){
                       response( jQuery.map( data , function( item ) {
                           return {
                             label     : item.User.firstname +' '+item.User.lastname ,
                             value     : item.User.firstname +' '+item.User.lastname ,
                             sysvalue  : item.User.id
                           }
                       }));
                   },
                   error        : function() {
                       alert('Could not connect to server.');
                   }

               });
            },
            open: function() {
            },
            close: function() {
            }
        });
        
        <?php if(!$isEdit && $layout == 'fancybox') { ?>
        jQuery(document).delegate('#formSubmit','click',function(){

            form    = jQuery(this).parents('form').attr('id');
            href    = jQuery(this).parents('form').attr('action');

            jQuery.ajax({
                url     : href,
                data    : jQuery('#'+form).serialize(),
                type    : 'POST',
                dataType: 'html',
                beforeSend: function(data){
                    jQuery('#formControlls').hide();
                    jQuery('#submitForm').show();
                },
                success  : function(data){               
                    jQuery('#exc').html(data);
                },
                complete: function(data){
                    jQuery('#submitForm').hide();
                    jQuery('#formControlls').show();
                },
                error   : function(){
                    alert('error');
                }
            });
        });
        
        jQuery('#UserRole').val('student');
        <?php } ?>
    
        jQuery('#UserRole').trigger('change');
    });
</script> 
<?php $this->end(); 
if($currentUser['User']['role'] == 'admin') {
    $is_read_only = false;
}else if($isEdit && $currentUser['User']['role'] != 'admin') {
    $is_read_only = false;
}else{
    $is_read_only = true;
}

?>
<div id="addProductForm" style="display: none;" title="<?php echo __('Add Product to student.'); ?>">
    <div class="row-fluid">        
        <div class="form-row"><div class="span12">
            <label class="field-name"><?php echo __('Name'); ?>:</label>
            <div class="field">
                <?php                     
                    echo $this->Form->select('product_id',$products,array(
                        'label' => false,
                        'div'   => null,
                        'class' => 'span12',
                        'empty' => __('Select Product'),
                        'id'    => 'productId'
                    ));                            
                ?>
                <div class="clearfix"></div>
                <div id="txt_product_id_error" class="error-message"></div>
            </div>
        </div></div>
        <?php
            echo $this->Form->hidden('student_id',array(
                'id'    => 'studentId'
            ));        
        ?>
        <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
        <div class="field" id="formControlls">
            <?PHP                
                echo $this->Form->button(__('Add'),array(                  
                    'id'    => 'addProduct'
                ));
            ?>           
            <?PHP
                echo $this->Form->button(__('Cancel'), array(                                                
                        'id'        => 'closeProduct'
                ));
            ?>
        </div>
    </div>
</div>

<div id="successMessage" style="display: none;" title="<?php echo __('Product Added'); ?>">
    <p></p>
</div>
<div class="inner-content">
    <?php 
        $title = ($isEdit)?__('Edit Profile'):__('Add User');
        $this->Html->pageInnerTitle($title,array(
            'icon'  => '<i class="fa fa-user"></i>'
        )); 
    ?>        
    <div class="row-fluid">
        <div class="widget">
        <?php
        echo $this->Form->create('User',array(
            'class'         => 'form-horizontal',
            'autocomplete'  => 'false'
        ));
        ?>
        <div class="widget-header">
            <h5><?php echo __('User account'); ?></h5>
            <?php 
                /*echo $this->Html->link(__('Products'),array(
                    'controller'    => 'products',
                    'action'        => 'index',
                    '?'             => array(
                       'student' => isset($user['id']) ? $user['id'] : '',
                    ),
                ),
                    array('class'        => 'teacher') 
            ); 
			*/
            ?>
			<?php /*
            <span class="teacher" style="float: right; padding: 10px 10px 10px 0px;">/</span>
			*/?>
            <a href="javascript:" class="addProduct teacher" element-id="<?php echo isset($user['id']) ? $user['id'] : ''; ?>">
			<?php echo __('Add Product'); ?></a> 
			
        </div>        
        <div class="widget-content no-padding">     
            <div class="form-row company">
                <span class="span6">
                    <label class="span3"><?php echo __('Profile Picture'); ?>:</label>
                    <div class="span8">
                        <div class="avatar">
                            <div class="ajax-img pull-left">
                                <?php if($isEdit && ($user['avatar_id'] != 0)) { ?>
                                <img src="<?php echo $this->Html->imagePreviewUrl($user['avatar_id']); ?>" />    
                                <?php
                                } else {
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
                            <a href="javascript:" class="profileImage button button-blue"><?php echo __('Upload image');?></a>
                        </div>
                        
                    </div>
                </span>
            </div>
            <div class="form-row">
                <span class="span6">
                    <label class="span3" for="firstname"><?php echo __('First Name'); ?>:</label>
                    <div class="span9">
                        <?php 
                        $val = isset($user['firstname'])?$user['firstname']:'';
                        echo $this->Form->input(
                            'firstname',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                                'placeHolder'   => __('First Name'), 
                                'readonly'      => $is_read_only,
                                'value'         => $val
                        ));
                        ?>
                        <div id="txt_firstname_error" class="error-message"></div>
                    </div>
                </span>
                <span class="span6">
                    <label class="span3" for="firstname"><?php echo __('Last Name'); ?>:</label>
                    <div class="span9">
                        <?php 
                        $val = isset($user['lastname'])?$user['lastname']:'';
                        echo $this->Form->input(
                            'lastname',array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                                'placeHolder'   => __('Last Name'),   
                                'readonly'      => $is_read_only,  
                                'value'         => $val
                        ));
                        ?>
                        <div id="txt_lastname_error" class="error-message"></div>
                    </div>
                </span>    
                <div class="clearfix"></div>
            </div>
            
            <div class="form-row">
                <div class="span6">
                <label class="span3" for="email"><?php echo __('Email Address'); ?>:</label>
                <div class="span9">
                    <?php 
                    $val    = isset($user['email_id']) ? $user['email_id'] : '';
                    echo $this->Form->input('email_id',array(
                        'type'          => 'text',
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Email Address'),
                        'readonly'      => $is_read_only,
                        'value'         => $val
                    ));
                    ?>
                    <div id="txt_email_id_error" class="error-message"></div>
                </div>
                </div>
                <div class="span6">
                <label class="span3" for="email"><?php echo __('Phone Number'); ?>:</label>
                <div class="span9">
                    <?php 
                    $val    = isset($user['phone_no']) ? $user['phone_no'] : '';
                    echo $this->Form->input('phone_no',array(
                        'type'          => 'text',
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Phone Number'),
                        'readonly'      => $is_read_only,
                        'value'         => $val
                    ));
                    ?>
                    <div id="txt_phone_no_error" class="error-message"></div>
                </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="span6">
                    <label class="span3"><?php echo __('Role'); ?>:</label>
                    <div class="span9">
                        <?php 
                        $val = isset($user['role']) ? $user['role'] : '';
                        if(!$isEdit) {
                            echo $this->Form->select('role',$roles,array(
                                'label'         => false,
                                'empty'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                            ));
                        } else if($isEdit && $currentUser['User']['role'] == 'admin' && $user['id'] != $currentUser['User']['id']) {
                            echo $this->Form->select('role',$roles,array(
                                'label'         => false,
                                'empty'         => false,
                                'div'           => null,
                                'class'         => 'span12',    
                                'value'         => $val
                            ));
                        } else {
                            echo $this->Form->input('role',array(
                                'disabled'   => TRUE,
                                'value'     => $val,
                            ));
                            echo $this->Form->hidden('role',array(
                                'value' => $val,
                            ));
                        }
                        if(!$isEdit && $layout == 'fancybox') {
                            echo $this->Form->hidden('role',array(
                                'value' => 'student',
                            ));
                        }
                        ?>
                        <div id="txt_role_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6 teacher hide">
                    <label class="span3"><?php echo __('Select Teacher'); ?>:</label>
                    <div class="span9">
                        <?php 
                        $val = (isset($user['teacher_id']) && !empty($user['teacher_id']))?$user['teacher_id']:'';
                        echo $this->Form->input('teacher',array(
                            'type'  => 'text',
                            'label' => FALSE,
                            'class' => 'span12 teacherAutoSuggest',
                            'readonly'      => $is_read_only,
                            'value' => ($val != '' && isset($teacher['User'])) ? $teacher['User']['firstname'].' '.$teacher['User']['lastname'] : '',
                        ));
                        echo $this->Form->hidden('teacher_id',array(
                            'id'    => 'teacherId',
                            'value' => $val
                        ));
                        ?> 
                        <div id="txt_teacher_id_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6 company teacherCompany">
                    <label class="span3"><?php echo __('Select Company'); ?>:</label>
                    <div class="span9">
                        <?php 
                        $val = isset($user['company_id']) ? $user['company_id'] : '';
                        echo $this->Form->select('company_id',$companies,array(
                            'label'     => false,
                            'div'       => null,
                            'class'     => 'span12',
                            'empty'     => __('Select Company'),
                            'value'     => $val,
                            'disabled'  => ($isEdit && $currentUser['User']['role'] != 'admin') ? TRUE : FALSE,
                        ));
                        if($isEdit) {
                            echo $this->Form->hidden('company',array(
                                'value'         => $val,
                            ));
                        }
                        ?>
                        <div id="txt_company_id_error" class="error-message"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-row not_student">
                <div class="span6">
                    <label class="span3"><?php echo __('User\'s Nick Name'); ?>:</label>
                    <div class="span9">
                        <?php 
                        $val = isset($user['nick_name_user']) ? $user['nick_name_user'] : '';
                        echo $this->Form->input('nick_name_user',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Nick Name For User'),
                            'value'         => $val,
                            'disabled'      => ($isEdit && $currentUser['User']['role'] != 'admin') ? TRUE : FALSE
                        ));
                        if($isEdit) {
                            echo $this->Form->hidden('nick_name_user',array(
                                'value' => $val,
                            ));
                        }
                        ?>
                        <div id="txt_nick_name_user_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6 internalTeacher">
                    <label class="span3"><?php echo __('Standard City'); ?>:</label>
                    <div class="span9">
                        <?php 
                        $val = isset($user['city'])?$user['city']:'';

                        echo $this->Form->select('city',$city,array(
                            'type'          => 'text',
                            'options'       => $city,
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Standard City'),
                            'value'         => $val
                        ));
                        ?> 
                        <div id="txt_city_error" class="error-message"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            
            <?php if(!$isEdit) { ?>
            <div class="form-row">
                <div class="span6">
                <label class="span3" for="username"><?php echo __('Username'); ?>:</label>
                <div class="span9">
                    <?php 
                    $val = isset($user['username'])?$user['username']:'';
                    echo $this->Form->input('username',array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Username'),
                        'value'         => $val
                    ));
                    ?>
                    <div id="txt_username_error" class="error-message"></div>
                </div>
                </div>
                
                <div class="span6">
                    <label class="span3"><?php echo __('Password'); ?>:</label>
                    <div class="span9">
                    <?php 
                    echo $this->Form->input('password',array(
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
            </div>
            <?php } ?>
            
            <?php if($isEdit) { ?>
            <div class="form-row">
                <div class="span6">
                    <label class="span3" for="username"><?php echo __('Username'); ?>:</label>
                    <div class="span9">
                        <?php 
                        $val = isset($user['username'])?$user['username']:'';
                        $args   = array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Username'),     
                            'value'         => $val
                        );
                        if($isEdit && $currentUser['User']['role'] != 'admin') {
                            $args['disabled']   = 'disabled';
                        }
                        echo $this->Form->input('username',$args);
                        ?>
                        <div id="txt_username_error" class="error-message"></div>
                    </div>
                </div>
                 <div class="span6">   
                    <label class="span3" for="standard"><?php echo __('Do you want to reset your password?');?></label>
                    <div class="span9 noSearch">
                        <label>
                            <?php
                            echo $this->Form->checkbox('reset_password',array(
                                'class' => 'uniform',
                                'id'    => 'reset_password_link'
                            ));
                            ?>
                            <label for="reset_password_link"><?php echo __('Reset Password'); ?></label>
                        </label>
                    </div>
                 </div>
            </div>
           
            <?php 
            $class  = ($isEdit)?'form':'';
            $id     = ($isEdit)?'reset_password_form':'';
            ?>
            <div class="<?php echo $class; ?> form-row" id="<?php echo $id; ?>">
                <div class="span6">
                    <label class="span3"><?php echo __('Password'); ?>:</label>
                    <div class="span9">
                    <?php 
                    echo $this->Form->input('password',array(
                        'type'          => 'password',
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span12',
                        'placeHolder'   => __('Enter password'),
                    ));
                    ?>
                    <div id="txt_password_error" class="error-message"></div>
                    </div>
                </div>
                <div class="span6">
                    <label class="span3"><?php echo __('Confirm Password'); ?>:</label>
                    <div class="span9">
                    <?php 
                    echo $this->Form->input('confirm_password',array(
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
            
             <?php if($user['status'] != 'deactive' && $currentUser['User']['role'] == 'admin' && $user['id'] != $currentUser['User']['id']) { ?>
                <div class="form-row">
                    <div class="span6">
                    <label class="span3"><?php echo __('User Status'); ?>:</label>
                    <div class="span9">
                        <label>
                            <?php
                            $argsactive   = array(
                                'hiddenField'   => FALSE,
                                'checked'       => ($user['status'] == 'active') ? TRUE : TRUE,
                            );
                            $argsDeactive   = array(
                                'hiddenField'    => FALSE,
                                'checked'       => ($user['status'] == 'inactive') ? TRUE : '',
                            );
                            
                            if($user['role'] != 'student') {
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
                            } else {
                                $status1['active'] = __('Still In Progess');
                                $status2['deactive']= __('Ended');
                            }

                            echo $this->Form->radio('status',$status1,$argsactive);
                            ?>
                        </label>
                        <label>
                            <?php                    
                            echo $this->Form->radio('status',$status2,$argsDeactive);
                            ?>
                        </label>                        
                    </div>
                    </div>
                </div>
            <?php } 
            } ?>
            
            <div class="form-row teacherCompany">
                <div class="span6">
                    <label class="span3"><?php echo __('Assistent ID'); ?>:</label>
                    <div class="span9">
                         <?php 
                        $val = isset($user['assistent_id'])?$user['assistent_id']:'';
                        echo $this->Form->input('assistent_id',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'readonly'      => $is_read_only,
                            'placeHolder'   => __('Assistent ID'),
                            'value'         => $val
                        ));
                        ?>                     
                    </div>
                </div>
                
            </div>  
            
            <div class="form-row teacher">
                <div class="span6">
                    <label class="span3"><?php echo __('Saldo'); ?>:</label>
                    <div class="span9">
                         <?php 
                        $val = isset($user['balance'])?$user['balance']:'';
                        echo $this->Form->input('balance',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12 saldo',
                            'placeHolder'   => __('Saldo'),
                            'readonly'      => 'true',
                            'value'         => $val,
                        ));
                        ?>                     
                    </div>
                </div>
                <?php if($currentUser['User']['role'] == 'admin'){  ?>
                <div class="span6">
                    <label class="span3"><?php echo __('Kreditmax'); ?>:</label>
                    <div class="span9">
                         <?php 
                        $val = isset($user['credit_max'])?$user['credit_max']:'';
                        echo $this->Form->input('credit_max',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Kreditmax'),
                            'readonly'      => $is_read_only,
                            'value'         => $val,
                        ));
                        ?>                     
                    </div>
                </div>
                <?php } ?>
            </div>  
            <div class="form-row teacher">
                <div class="span6">
                    <label class="span3"><?php echo __('Elevnummeret'); ?>:</label>
                    <div class="span9">
                         <?php 
                        $val = isset($user['student_number'])?$user['student_number']:'';
                        echo $this->Form->input('student_number',array(
                            'type'          => 'text',
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Elevnummeret'),
                            'readonly'      => 'true',
                            'value'         => $val
                        ));
                        ?>                     
                    </div>
                </div>
            </div>            
            <div class="form-row">                
            <div id="submitForm" class="fly_loading"><?php  echo $this->Html->image("submit-form.gif");?></div>
            <div class="field" id="formControlls">
            <?php 
            $btnName = ($isEdit)? __('Update'):__('Add');
            echo $this->Form->button('<i class="icon-ok icon-white"></i> '.$btnName,array(
                'class' => 'button button-green',
                'type'  => 'button',
                'id'    => 'formSubmit'
            ),
                array('escape' => FALSE)                            
            );
            echo $this->Html->link('<i class="icon-remove icon-white"></i> '.__('Cancel'),array(
                'action' => 'index'
            ),array(
                'class' => 'button button-red',
                'escape' => FALSE,
            ));
            ?>
            </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <?php 
    $Total_crm_in = 0;
    if(!empty($Payments)){ ?>
    <div class="row-fluid">
        <div class="widget">
            <div class="widget-header">
                <h5>Seneste Betalinger</h5>  
            </div>
            <div class="tableLicense">

            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <!-- <th align="left">Bilags Nummer</th> -->
                    <th align="left">Dato</th>
                    <th align="left">Debet</th>
                    <th align="left">Bilags Nummer</th>
                    <th align="left">Kredit</th>
                </tr>

            <?php
            $i=0;
            foreach ($Payments as $key => $Payment) {
                $Payment = (object)$Payment['LatestPayments'];
                $Total_crm_in = $Total_crm_in + round($Payment->Kredit);
             ?>
                <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?> " align="center" >
                    <!-- <td align="left">
                        <?php echo $Payment->DebitorRegistreringID; ?>
                    </td> -->
                    <td align="left">
                        <?php echo date('d.m.Y',strtotime($Payment->PosteringsDato)); ?>
                    </td>
                    <td align="left">
                       <?php echo round($Payment->Debet); ?>
                    </td>
                    <td align="left">
                       <?php echo $Payment->BilagsNummer; ?>
                    </td>
                    <td align="left">
                        <?php echo round($Payment->Kredit); ?>
                    </td>
                </tr>
                            
            <?php  } ?>
            </table>
            </div>
        </div>
    </div>
    <?php 
    }
    if(isset($user['role']) && $user['role'] == 'student'){ 
    $types      = Configure::read('bookingType'); 
    $lessonTime = Configure::read('lessonTime');
    ?>
    <div class="row-fluid">
        <div class="span12">      
            <div class="widget">
                <div class="widget-header">
                    <h5>
                        <?php 
                        echo __('Future Bookings');
                        echo " ";
                        if (isset($userId) && !empty($userId)) {
                            echo __(' of ')." ".$users[$userId]['firstname'].' '. $users[$userId]['lastname'];
                        }
                        ?>
                    </h5>  
                   
                </div>
                <div class="tableLicense">
                    <table cellpading="0" cellspacing="0" border="0" class="default-table">
                        <tr>
                            <th align="left"><?php echo __('Date');?></th>
                            <th align="left"><?php echo __('Area');?></th>
                            <th align="left"><?php echo __('Price');?></th>
                        </tr>
                        <?php                
                        $i=0;
                        $gtotal = 0;
                        if(!empty($Systembooking)) {
                            foreach($Systembooking as $booking){
                                ?>
                                <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?> " align="center" >
                                     <td align="left">
                                        <?php echo date('d.m.Y',strtotime($booking['Systembooking']['start_time'])); ?>
                                    </td>
                                    <td align="left">
                                        <?php 
                                        $type = ($booking['Systembooking']['lesson_type']  != '') ? $booking['Systembooking']['lesson_type'] : '1' ;
                                        $type1 = ($type != '') ? $type." x " : '';
                                        echo $type1.ucfirst($types[$booking['Systembooking']['booking_type']]); ?>
                                    </td>
                                    <td align="left">
                                        <?php echo $total =  $type*500;
                                        $gtotal = $gtotal + $total;
                                         ?>
                                    </td>
                                   
                                </tr>
                            <?php
                            }
                        }else{
                        ?>
                            <tr>
                                <td colspan="5" class="index_msg"><?php  echo __('No Bookings are added.'); ?></td>
                            </tr>
                        <?php                   
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    

    <div class="row-fluid">
        <div class="span12">      
            <div class="widget">
                <div class="widget-header">
                    <h5>
                        <?php 
                        echo __('Bookings');
                        echo " ";
                        if (isset($userId) && !empty($userId)) {
                            echo __(' of ')." ".$users[$userId]['firstname'].' '. $users[$userId]['lastname'];
                        }
                        ?>
                    </h5>  
                    <?php 
                    echo $this->Html->link(__('Add Booking'),array(
                        'controller'    => 'adminbookings',
                        'action'        => 'calendar'
                    ));
                    ?>
                </div>
                <div class="tableLicense">
                    <table cellpading="0" cellspacing="0" border="0" class="default-table">
                        <tr>
                            <th><?php echo __('No.');?></th>
                            <th align="left"><?php echo __('Name');?></th>
                            <th align="left"><?php echo __('Area');?></th>
                            <th align="left"><?php echo __('Date');?></th>
                            <th><?php echo __('Actions');?></th>
                        </tr>
                        <?php                
                        $i=0;
                        if(!empty($bookings)) {
                            foreach($bookings as $booking){
                                ?>
                                <tr class="<?php echo ($i++%2==0)?'even':'odd'; echo (!empty($nextBooking) && $nextBooking[0] == $booking['Booking']['id']) ? 
                                ' next_booking_row' : ''; ?> " align="center"
                                title = "<?php echo (!empty($nextBooking) && $nextBooking[0] == $booking['Booking']['id']) ? __('Next Run Time') : ''; ?>"
                                >
                                    <td align="center">
                                        <?php echo $i; ?>
                                    </td>
                                    <td align="left">
                                        <?php echo $users[$booking['Booking']['user_id']]['firstname'].' '.$users[$booking['Booking']['user_id']]['lastname']; ?>
                                    </td>
                                    <td align="left">
                                        <?php echo Inflector::humanize($booking['Booking']['area_slug']); ?>
                                    </td>
                                    <td align="left">
                                        <?php echo date('d.m.Y',strtotime($booking['Booking']['date'])); ?>
                                    </td>
                                    <td align="center">
                                        <?php                                    
                                        echo $this->Html->link(__('View'), array(
                                            'controller'    => 'adminbookings',
                                            'action'        => 'view',
                                            $booking['Booking']['id'],
                                            '?'             => array(
                                                'student_booking_detail' => (isset($userId) && !empty($userId)) 
                                                ? $userId
                                                : ''
                                            )
                                        ));
                                        ?> / <?php 
                                        echo $this->Html->link(__('Edit'), array(
                                            'controller'    => 'adminbookings',
                                            'action'        => 'calendar',
                                            '?'             => array(
                                                'area'      => $booking['Booking']['area_slug'],
                                                'date'      => $booking['Booking']['date'],
                                            )
                                        ));
                                        ?> 
                                    </td>
                                </tr>
                            <?php
                            }
                        }else{
                        ?>
                            <tr>
                                <td colspan="5" class="index_msg"><?php  echo __('No Bookings are added.'); ?></td>
                            </tr>
                        <?php                   
                        }
                        ?>
                    </table>
                </div>
            </div>
            
            <div class="pagination_no" style="float: right;">
            <?php
                echo $this->Paginator->first(__('First'),
                                            array('class' => 'first paginate_button'),
                                            null,
                                            array('class' => 'paginate_button_disabled')
                                            );
                echo $this->Paginator->prev(__('Previous'),
                                            array('class' => 'previous paginate_button'),
                                            null,
                                            array('class' => 'paginate_button_disabled')
                                            );
                echo $this->Paginator->numbers(array('class' => 'paginate_button','modulus' => 2,'separator' => FALSE));
                echo $this->Paginator->next(__('Next'),
                                            array('class' => 'next paginate_button'),
                                            null,
                                            array('class' => 'paginate_button_disabled')
                                            );
                echo $this->paginator->last(__('Last'),
                                            array('class' => 'first paginate_button'),
                                            null,
                                            array('class' => 'paginate_button_disabled')
                                            );
            ?>
            </div>
       
        </div>
    </div>

    <div class="row-fluid" style="margin-top: 20px;">
        <div class="span12">      
            <div class="widget">
                <div class="widget-header">
                    <h5>
                        <?php 
                        echo __('Ydelser');
                        echo " ";
                        if (isset($userId) && !empty($userId)) {
                            echo __(' of ')." ".$users[$userId]['firstname'].' '. $users[$userId]['lastname'];
                        }
                        ?>
                    </h5>  
                </div>
                <div class="tableLicense">
                    <table cellpading="0" cellspacing="0" border="0" class="default-table">
                        <tr>
                            <th><?php echo __('No.');?></th>
                            <th align="left"><?php echo __('Tekst');?></th>
                            <th align="left"><?php echo __('PosteringsDato');?></th>
                            <th align="left"><?php echo __('Antal');?></th>
                            <th align="right"><?php echo __('SatsInclMoms');?></th>
                            <th align="right"><?php echo __('BeloebInclMoms');?></th>
                        </tr>
                        <?php                
                        $i=0;
                        $UserServices_total = 0;
                        if(!empty($UserServices)) {
                            // echo "<pre>";
                            foreach($UserServices as $UserService){
                            // print_r($UserService);
                            // die();
                                ?>
                                <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?> " align="center" >
                                    <td align="center">
                                        <?php echo $i; ?>
                                    </td>
                                    <td align="left">
                                        <?php echo $UserService['UserServices']['description']; ?>
                                    </td>
                                    <td align="left">
                                        <?php echo date('d.m.Y',strtotime($UserService['UserServices']['posting_date'])); ?>
                                    </td>
                                    <td align="left">
                                        <?php echo number_format($UserService['UserServices']['qty'], 2, '.', ''); ?>
                                    </td>
                                    <td align="right">
                                        <?php echo number_format($UserService['UserServices']['price'], 2, '.', ''); ?>
                                       
                                    </td>
                                    <td align="right">
                                        <?php 
                                        echo $total_price = number_format($UserService['UserServices']['total_price'], 2, '.', '');
                                        $UserServices_total +=  $total_price; ?>
                                        
                                    </td>
                                </tr>
                            <?php
                            }
                    }else{
                        ?>
                            <tr>
                                <td colspan="6" class="index_msg"><?php  echo __('Ingen Ydelser tilføjet.'); ?></td>
                            </tr>
                        <?php                   
                        }
                        ?>
                    </table>
                    <?php if($UserServices_total > 0){ ?>
                        <div class="widget-header">
                            <h5><?php echo __('Total'); ?></h5>  
                            <h5 style="float: right;padding-right: 10px;"><?php echo number_format($UserServices_total,2 , '.', ''); ?></h5>
                        </div>
                    <?php } ?>
                </div>
            </div>

       
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">      
            <div class="widget">
                     <div class="tableLicense">
                    <table cellpading="0" cellspacing="0" border="0" class="default-table">
                        <tr>
                            <?php
                            $Balance = 0;
                            if($Total_crm_in < 0){
                                $Balance =  $Total_crm_in - $UserServices_total;
                            }else{
                                $Balance =  (-$Total_crm_in) + $UserServices_total;
                            }

                            $available_bal = $Balance + $gtotal;
                            // if($available_bal == 0 || $available_bal < 0){
                            //     $available_bal = $available_bal;
                            // }else {
                            //     $available_bal = "-".$available_bal;
                            // }
                            ?>
                            <th align="left">Balance from system : <?php echo $Balance; ?></th>
                            <th align="center">Future balance : <?php echo $gtotal ; ?></th>
                            <th align="right" >Available balance : <span class="available_bal"><?php echo $available_bal; ?></span></th>
                        </tr>
                    </table>
                </div>
                    

            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            var available_bal = $('.available_bal').html();
            $('.saldo').val(available_bal);
        })
    </script>
    <?php } ?>
</div>
