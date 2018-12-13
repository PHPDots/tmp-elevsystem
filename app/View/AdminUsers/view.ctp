<div class="inner-content"><div class="row-fluid"><div class="user-profile row-fluid">
    <div class="widget">
        <div class="widget-header"><h5><?PHP echo __('User Details'); ?></h5></div>
        <div class="slide">
            <div class="widget-content">
                <?php if($user['User']['role'] != 'student') { ?>
                <div class="span2">
                    <div class="user-title">User Profile Image</div>
                    <div class="avatar">                       
                        <?php if($user['User']['avatar_id'] != 0){ ?>
                            <img src="<?php echo $this->Html->imagePreviewUrl($user['User']['avatar_id']); ?>" />    
                        <?php
                        }else{
                            echo $this->Html->image('user-image.png');
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>
                <div class="<?php echo ($user['User']['role'] != 'student') ? 'span8 offset1' : 'span12'  ?>">
                    <div class="user-line">
                        <label><?PHP echo __('Name'); ?></label>
                        <label>:&nbsp;&nbsp;<?php echo $user['User']['firstname'] .'&nbsp;'. $user['User']['lastname']; ?></label>              
                    </div>                  
                    <div class="user-line">
                        <label><?php echo __('Username')?></label>
                        <label>:&nbsp;&nbsp;<?php echo $user['User']['username']; ?></label>              
                    </div>                      
                    <div class="user-line">
                        <label><?php echo __('Email id')?></label>
                        <label>:&nbsp;&nbsp;<?php echo $user['User']['email_id']; ?></label>              
                    </div>  
                    <div class="user-line">
                        <label><?php echo __('Phone Number')?></label>
                        <label>:&nbsp;&nbsp;<?php echo $user['User']['phone_no']; ?></label>              
                    </div>       
                    <div class="user-line">
                        <label><?php echo __('Role')?></label>
                        <label>:&nbsp;&nbsp;<?php echo $roles[$user['User']['role']]; ?></label>              
                    </div>   
                    <?php if($user['User']['role'] != 'student') { ?>
                    <div class="user-line">
                        <label><?php echo __('User\'s Nick Name')?></label>
                        <label>:&nbsp;&nbsp;<?php echo (!is_null($user['User']['nick_name_user']))?$user['User']['nick_name_user']:''; ?></label>              
                    </div>
                    <?php } ?>
                    <?php if(in_array($user['User']['role'],array('internal_teacher','external_teacher'))){ ?>
                    <div class="user-line">
                        <label><?php echo __('Company')?></label>
                        <label>:&nbsp;&nbsp;<?php echo (!is_null($user['User']['company_id']) && isset($companies[$user['User']['company_id']]))?$companies[$user['User']['company_id']]:''; ?></label>              
                    </div> 
                    <div class="user-line">
                        <label><?php echo __('Company\'s Nick Name')?></label>
                        <label>:&nbsp;&nbsp;<?php echo (!is_null($user['User']['company_id']))?$user['User']['company_id']:''; ?></label>              
                    </div> 
                    
                    <?php } ?>
                    
                    <?php if($user['User']['role'] == 'student'){ ?>
                    <div class="user-line">
                        <label><?php echo __('Student Number')?></label>
                        <label>:&nbsp;&nbsp;<?php echo $user['User']['student_number']; ?></label>
                    </div>
                    <div class="user-line">
                        <label><?php echo __('Teacher')?></label>
                        <label>:&nbsp;&nbsp;<?php echo (!empty($teacher)) ? $teacher['User']['firstname'].' '.$teacher['User']['lastname'] : ''; ?></label>
                    </div> 
                    <?php } ?>
                    
                    <?php if($user['User']['role'] == 'internal_teacher'){ ?>
                    <div class="user-line">
                        <label><?php echo __('Standard City')?></label>
                        <label>:&nbsp;&nbsp;<?php echo $user['User']['city']; ?></label>
                    </div>
                    <?php } ?>
                    
                    <div class="clearfix"></div>                  
                </div>
                <div class="clearfix"></div> 
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

</div></div></div>


