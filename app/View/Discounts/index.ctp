<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Discounts'); ?></h5>  
            <?php 
            echo $this->Html->link(__('Add Discount'),array(
                'controller'    => 'discounts',
                'action'        => 'add'
            ));
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>                    
                    <th align="left"><?php echo __('City');?></th>
                    <th align="left"><?php echo __('Discount');?></th>
                    <th align="left"><?php echo __('From Date');?></th>
                    <th><?php echo __('Actions');?></th>
                </tr>
                <?php 
                $i=0;
                if(!empty($discounts)) {
                    foreach($discounts as $discount) {
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>     
                            <td align="left"> 
                                <?php echo $cities[$discount['Discount']['city']]; ?>
                            </td>
                            <td align="left">
                                <?php echo $discount['Discount']['discount'].' DKK'; ?>
                            </td>
                            <td align="left">
                                <?php echo date('d.m.Y',strtotime($discount['Discount']['from_date'])); ?>
                            </td>
                            <td align="center">
                                <?php 
                                echo $this->Html->link(__('Edit'), array(
                                    'controller'    => 'discounts',
                                    'action'        => 'edit',
                                    $discount['Discount']['id']
                                ));
                                ?> / <?php 
                                echo $this->Html->link(__('Delete'),array(
                                    'controller'    => 'discounts',
                                    'action'  => 'delete',
                                    $discount['Discount']['id']),array(
                                        'class' => 'deleteElement'
                                    )
                                ); 
                                ?>  
                            </td>   
                        </tr>
                    <?php
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No Discounts are added'); ?></td>
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
            echo $this->Form->create('Discount',array(
                'class'         => 'row-fluid',
                'url'           => array(
                'controller'    => 'discounts',
                'action'        => 'index'
            )));

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