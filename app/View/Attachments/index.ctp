<div class="inner-content"><div class="row-fluid"><div class="span12">
    <?php if($isFancybox){?>
        <ul class="upload_files">
            <li><a href="<?php echo $this->Html->url(array('controller'=>'attachments','action'=>'index','fancybox')); ?>"><?php echo __('Library');?></a></li>
            <li><a href="<?php echo $this->Html->url(array('controller'=>'attachments','action'=>'add','fancybox','multiple' => 'false')); ?>"><?php echo __('Upload Files');?></a></li>
        </ul>    
    <?php } ?>
    <div class="widget">        
        <div class="widget-header">
            <h5><?PHP echo __('Reports'); ?></h5>            
            <?php if($isFancybox){?>
                <a href="<?php echo $this->Html->url(array('controller'=>'attachments','action'=>'add','fancybox','multiple' => 'false')); ?>"><?php echo __('Add File'); ?></a>                
            <?php }else{ ?>
                <a href="<?php echo $this->Html->url(array('controller'=>'attachments','action'=>'add','multiple' => 'true')); ?>"><?php echo __('Add Report'); ?></a>
            <?php } ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">  
                <tr><?php 
                                    if($isFancybox){   ?>
                    <th> </th>
                    <?php } ?> 
                    <th> </th> 
                    <th align="left"><?php echo __('File Name')?></th>                      
                    <th align="left"><?php echo __('Mime Type')?></th>                      
                    <th align="left"><?php echo __('Created')?></th>  
                    <?php 
                                    if(!$isFancybox){   ?>
                    <th><?php echo __('Action')?></th>         
                    <?php } ?>
                    
                </tr>
                <?php
                $val=array();
                $i = 0;              
                if(!empty($attachments)) {
                    foreach($attachments as $attachment){                                                    
                        ?>
                 <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>">                              
                           <?php 
                                    if($isFancybox){   ?>
                            
                            <td>
                              <?php                                    
                                   echo $this->Form->checkbox('attachment_id',array(                                       
                                                'value'         =>  $attachment['Attachment']['id'],                                                 
                                                'hiddenField'   => false,
                                                'class'         => 'attachment',
                                                'id'            => 'attachment_' . $attachment['Attachment']['id'],
                                            ));
                                       
                                    echo $this->Form->hidden('attachment',array(
                                            'value'         => json_encode($attachment['Attachment']),
                                            'class'         => 'attachment_json',
                                            'id'            => 'attachment_json_' . $attachment['Attachment']['id']
                                    ));
                                ?>
                            </td>
                            <?php } ?>    
                            <td align="center" class="attachment-icon-image">
                               <img src= "<?php echo $this->Html->webrootpath().$attachment['Attachment']['icon_img']?>" class="modified-icons" />
                            </td>
                            <td align="left">
                                <?php
                                     echo $this->Html->link(
                                        $attachment['Attachment']['filename'],
                                        array(
                                            'controller'    => 'Attachments', 
                                            'action'        => 'get',
                                            $attachment ['Attachment']['id']
                                        )
                                    );                                    
                                ?>
                            </td>                          
                            <td align="left">
                                <?php echo $attachment['Attachment']['mime_type']; ?>
                            </td>                                                        
                            <td align="left">
                               <?php  echo $attachment['Attachment']['timestamp']; ?>
                            </td>
                            <?php 
                                    if(!$isFancybox){   ?>
                                <td align="center">
                                    <?php 
                                         echo $this->Html->link(
                                             'View',
                                             array('action'  => 'view',$attachment['Attachment']['id'])                                        
                                         );
                                    ?>
                                 </td>    
                            <?php } ?>
                            
                        </tr>                        
                    <?php                                         
                    }
                    
                }
               
                ?>
            </table>
        </div>
    </div>  
    <div>
        <div class="pagination_no">
            <?php
                if(!$isFancybox){ 
                    echo $this->paginator->first(__('First', TRUE),
                                                array('class' => 'first paginate_button'),
                                                null,
                                                array('class' => 'paginate_button_disabled')
                                                );
                }
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
                if(!$isFancybox){ 
                echo $this->paginator->last(__('Last'),
                                            array('class' => 'first paginate_button'),
                                            null,
                                            array('class' => 'paginate_button_disabled')
                                            );
                }
            ?>
            <?php if($isFancybox){ ?>
                <input type="button" value="<?php echo __('Use Files'); ?>" id="use_files_btn" />
            <?php }?>
        </div>
        <div class="pagination">
        <?php
        if(!empty($attachments)) {
            if($isFancybox){
                echo $this->Form->create('Attachment',array(
                        'class'         => 'row-fluid',
                        'url'           => $paginationAction,    
                 )
            );                
            }else{
                 echo $this->Form->create('Attachment',array(
                        'class'         => 'row-fluid',
                        'url'           => $paginationAction
                                           
                 )
            );
            }
            echo $this->Form->input(__('perPage'), array(
                        'options'   => $perPageDropDown,
                        'selected'  => $perPage, 
                        'id'        => 'dropdown' 
            ));
            
            $this->Form->end();
        }
        ?>
        </div>
    </div>
</div></div></div>

 