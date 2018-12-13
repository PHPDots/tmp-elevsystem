<?php $priceType  = Configure::read('priceType'); ?>
<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <div class="widget">
        <div class="widget-header">
            <?php 
                $title  = '';
                
                if(!empty($student)){
                    $title .= $student['User']['firstname'].' '.$student['User']['lastname'].' ';
                }
                
                $title  .=  __('Products');
            ?>
            <h5><?php echo $title; ?></h5>  
            <?php 
            if(empty($student)){
                echo $this->Html->link(__('Add Product'),array(
                    'controller'    => 'products',
                    'action'        => 'add'
                ));
            }
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>                    
                    <th align="left"><?php echo __('Name');?></th>
                    <th align="left"><?php echo __('Activity Number');?></th>
                    <th align="left"><?php echo __('Price');?></th>
                    <?php if(empty($student)){ ?>
                    <th><?php echo __('Actions');?></th>
                    <?php } ?>
                </tr>
                <?php 
                $i=0;
                if(!empty($products)) {
                    foreach($products as $product) {
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>     
                            <td align="left"> 
                                <?php echo $product['Product']['name']; ?>
                            </td>
                            <td align="left">
                                <?php echo $product['Product']['activity_number']; ?>
                            </td>
                            <td align="left">
                                <?php echo $product['Product']['price']; ?>
                            </td>
                            <?php if(empty($student)){ ?>
                            <td align="center">
                                <?php 
                                echo $this->Html->link(__('Edit'), array(
                                    'controller'    => 'products',
                                    'action'        => 'edit',
                                    $product['Product']['id']
                                ));
                                ?> / <?php 
                                echo $this->Html->link(__('Delete'),array(
                                    'controller'=> 'products',
                                    'action'    => 'delete',
                                    $product['Product']['id']),array(
                                        'class' => 'deleteElement'
                                    )
                                ); 
                                ?>  
                            </td>
                            <?php } ?>
                        </tr>
                    <?php
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No Products are added'); ?></td>
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
            if(!empty($areas)) {
                echo $this->Form->create('Product',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'products',
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