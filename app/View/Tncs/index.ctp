<?php $this->append('script'); ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#addTnc').click(function(){
            jQuery('#addTncForm').dialog({
                autoOpen        : true,
                modal           : true,
                minWidth        : 600,
                buttons         : {
                    'Submit'        : function(){
                        var url     = jQuery('#TncIndexForm').attr('action');
                        var form    = jQuery('#TncIndexForm').serialize();
                        jQuery.ajax({
                           url      : url,
                           data     : form,
                           dataType : 'html',
                           type     : 'post',
                           success  : function(data){
                               jQuery('#exc').html(data);
                           }
                        });
                    },
                    'Cancel'        : function(){
                        jQuery(this).dialog('close');
                    }
                }
            });       
        });
    });
</script>
<?php $this->end(); ?>
<div class="inner-content"><div class="row-fluid"><div class="span12">        
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Terms And Conditions'); ?></h5>  
            <?php if($currentUser['User']['role'] == 'admin'){ ?>
            <a href="javascript:" id="addTnc"><?php echo __('Add Terms And Conditions'); ?></a>           
            <?php } ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Title');?></th>
                    <?php if($currentUser['User']['role'] == 'admin'){ ?>
                    <th align="center"><?php echo __('User Count');?></th>
                    <th align="center"><?php echo __('User Agreed Count');?></th>
                    <?php } ?>
                    <th align="left"><?php echo __('Created');?></th>
                    <th><?php echo __('Actions');?></th>
                </tr>
                <?php 
                $pageNo         = isset($this->Paginator->params['named']['page'])?$this->Paginator->params['named']['page']:1;
                $i              = ($pageNo - 1)*$perPage;
                if(!empty($tncs)) {          
                    foreach($tncs as $tnc){        
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?PHP echo $i; ?>
                            </td>     
                            <td align="left"> 
                                <?php echo $tnc['Tnc']['title']; ?>
                            </td>
                            <?php if($currentUser['User']['role'] == 'admin'){ ?>
                            <td align="center">
                                <?php echo $tnc[0]['total']; ?>
                            </td>
                            <td align="center">
                                <?php echo isset($tncAgreedUserCount[$tnc['Tnc']['id']])?$tncAgreedUserCount[$tnc['Tnc']['id']]:0; ?>
                            </td>
                            <?php } ?>
                            <td align="left">
                                <?php echo date('d.m.Y',strtotime($tnc['Tnc']['created'])); ?>
                            </td>
                            <td align="center">
                                <?php      
                                    
                                    $url    = array(
                                            'controller'    => 'tncs',
                                            'action'        => 'view',
                                            $tnc['Tnc']['id'],
                                        
                                    );
                                    
                                    if(isset($this->request->query['iframe']) && !empty($this->request->query['iframe']) && ($this->request->query['iframe'])){                                                    
                                         $url['?']    = array(
                                            'iframe'    => $this->request->query['iframe']
                                       );  
                                    }
                                
                                    echo $this->Html->link(__('View'), $url);
                                    if($currentUser['User']['role'] == 'admin'){
                                  ?> / <?php 
                                    echo $this->Html->link(
                                        __('Delete'),
                                        array('controller'    => 'tncs','action'  => 'delete', $tnc['Tnc']['id']),
                                        array('class' => 'deleteElement')
                                    ); 
                                    }
                                ?>  
                            </td>   
                        </tr>
                    <?php
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No Terms And Conditions are added'); ?></td>
                    </tr>
                <?php                   
                }
                ?>
            </table>
        </div>
    </div>
            
    <div>
        <div class="pagination_no">
        <?php
            echo $this->paginator->first(__('First'),
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
        <div class="pagination">
        <?php
            if(!empty($users)) {
                echo $this->Form->create('User',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'users',
                            'action'        => 'index'
                         ),    
                     )
                );

                echo $this->Form->input('perPage', array(
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

<div id="addTncForm" title="<?php echo __('Terms And Conditions Details'); ?>" style="display: none;">
    <?php 
        echo $this->Form->create('Tnc',array(
            'class' => 'form-horizontal',
            'url'   => array(
                'controller'    => 'tncs',
                'action'        => 'add'
            )
        ));
    ?>
    <div class="form-row">
        <label class="field-name"><?php echo __('Title'); ?></label>
        <div class="field">
            <?php 
                echo $this->Form->input('title',array(
                    'label'         => false,
                    'div'           => null,
                    'class'         => 'span5',
                    'placeHolder'   => __('Title'),
                )); 
            ?>
            <div class="error-message" id="txt_error_title"></div>
        </div>        
    </div>
    <div class="form-row">
        <label class="field-name"><?php echo __('Terms'); ?></label>
        <div class="field">
            <?php 
                echo $this->Form->input('terms',array(
                    'type'          => 'textarea',
                    'label'         => false,
                    'div'           => null,
                    'class'         => 'span5',
                    'placeHolder'   => __('Terms And Conditions'),
                )); 
            ?>
            <div class="error-message" id="txt_error_terms"></div>
        </div>
    </div> 
    <?php echo $this->Form->end(); ?>
</div>
