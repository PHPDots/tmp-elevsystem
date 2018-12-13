<?php $this->append('script'); ?>
<script type="text/javascript">
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
    });
</script>
<?php $this->end(); ?>
<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="span12">
        <div class="span4">
        <div class="span9">
        <?php
        echo $this->Form->create('Adminuser',array(
            'action'    => 'students',
            'type'      => 'GET'
        ));
        
        echo $this->Form->input('searchTxt',array(
            'placeholder'   => __('Search By Id / Name / Kørelærer / Afdeling / Phone'),
            'label'         => FALSE,
            'class'         => 'span12',
            'div'           => FALSE,
            'value'         => (isset($this->request->query['searchTxt'])) ? $this->request->query['searchTxt'] : ''
        ));
        ?>
        </div>
        <div class="span3">
        <?php
        echo $this->Form->button('Search',array(
            'class' => 'button button-green',
        ));
        echo $this->Form->end();
        ?>
        </div>
        </div>
        <div class="span3">
            <?php
            if(isset($this->request->query['searchTxt']) && !empty($this->request->query['searchTxt'])) {
                echo __('Search result for ').' '."\"{$this->request->query['searchTxt']}\"";
            }
            ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Students'); ?></h5>  
            <?php 
            // echo $this->Html->link(__('Add Student'),array(
            //     'controller'    => 'adminusers',
            //     'action'        => 'add'
            // ));
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Name');?></th>
                    <th align="left">Kørelærer</th>
                    <th align="left">Afdeling</th>
                    <th align="left"><?php echo __('Contact No');?></th>
                    <th><?php echo __('Actions');?></th>
                </tr>
                <?php 
                $pageNo         = isset($this->Paginator->params['named']['page'])?$this->Paginator->params['named']['page']:1;
                $i              = ($pageNo - 1)*$perPage;
                if(!empty($users)) {          
                    foreach($users as $user){        
                        ?>
						<?php 
							$extraClass = "";
							if($currentUser['User']['role'] == 'internal_teacher' && 
							$user['User']['teacher_id'] == $currentUser['User']['id'])
							{
								$extraClass = "bold-text ";
							}
						?>
                        <tr class="<?php echo $extraClass;?><?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
								
                                <?php echo $i; ?>									
								
                            </td>     
                            <td align="left"> 
                                <?php echo $user['User']['firstname'].' '.$user['User']['lastname']; ?>
                            </td>
                            <td align="left">
                                <?php echo $user[0]['teachername']; ?>
                            </td>
                            <td align="left">
                                <?php echo $user['User']['city']; ?>
                            </td>
                            <td align="left">
                                <?php echo $user['User']['phone_no']; ?>
                            </td>
                            <td align="center">
                              <!-- <a href="javascript:" class="addProduct" element-id="<?php echo $user['User']['id']; ?>"><?php echo __('Add Product'); ?></a> /  -->
                              <?php 
                                // echo $this->Html->link(__('Products'),array(
                                //     'controller'    => 'products',
                                //     'action'        => 'index',
                                //     '?'             => array(
                                //        'student' => $user['User']['id'],
                                //     )
                                // )); 
                                // echo " / ";
                                echo $this->Html->link(__('View'), array(
                                        'controller'    => 'adminusers',
                                        'action'        => 'edit',
                                        $user['User']['id']
                                ));
                                ?> 
                            </td>   
                        </tr>
                    <?php
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No Students found'); ?></td>
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
        $this->paginator->options(array('url' => array('?' => $this->request->query)));
        
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
                            'controller'    => 'adminusers',
                            'action'        => 'students'
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