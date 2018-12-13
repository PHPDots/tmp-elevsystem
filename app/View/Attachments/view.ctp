<div class="inner-content">
    <div class="row-fluid">
    <div class="widget">
        <?php
            echo $this->Form->create('Attachment',array(
                'class' => 'form-horizontal'
            ));
        ?>
        <div>
            <div class="widget-header">
                <h5><?PHP echo __('Attachment Details'); ?></h5>
            </div>
            <div class="widget-content">
               <?php 
                    $class = 'span12';
                    
                    if(in_array($attachment['Attachment']['mime_type'],array('image/png','image/jpeg','image/jpg'))){
                        $class  = 'span6';
               ?>
                <div class="span6">
                    <div class="avatar">
                        <div class="ajax-img">                            
                           <img src="<?php echo $this->Html->imagePreviewUrl($attachment['Attachment']['id']); ?>" />
                        </div>                  
                    </div> 
                </div>
               <?php
                    }
               ?>
                <div class="<?php echo $class; ?>">
                   <div class="tableLicense">
                        <table cellpading="0" cellspacing="0" border="0" class="default-table">                  
                            <tbody>
                                <tr>
                                    <td class="field_title">
                                        <h5><?PHP echo __('File Name'); ?></h5>
                                    </td>
                                    <td class="">
                                        <?php
                                               echo $this->Html->link(
                                                    $attachment['Attachment']['filename'],
                                                    array(
                                                        'controller'    => 'Attachments', 
                                                        'action'        => 'get',
                                                        $attachment ['Attachment']['id']
                                                    ),array(
                                                        'target'        => 'blank'
                                                    ));      
                                        ?>
                                    </td>                                    
                                </tr>    
                                <tr>
                                    <td class="field_title">
                                        <h5><?PHP echo __('Mime Type'); ?></h5>
                                    </td>
                                    <td class="">
                                        <?php echo $attachment['Attachment']['mime_type']; ?>
                                    </td>                                    
                                </tr>  
                                 <tr>
                                    <td class="field_title">
                                        <h5><?PHP echo __('Upload On'); ?></h5>
                                    </td>
                                    <td class="">
                                        <?php echo date($dateFromat,  strtotime($attachment['Attachment']['timestamp'])); ?>
                                    </td>                                    
                                </tr>
                                <tr>
                                    <td class="field_title">
                                        <h5><?PHP echo __('Uploader'); ?></h5>
                                    </td>
                                    <td class="">
                                        <?php 
                                            echo $this->Html->link($user['firstname'].' '.$user['lastname'],
                                            array(
                                                'controller'    => 'users',
                                                'action'        => 'view',
                                                $user['id']
                                            ),array(
                                                 'target'        => 'blank'
                                             ));                                               
                                         ?>
                                    </td>                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    </div>
</div>