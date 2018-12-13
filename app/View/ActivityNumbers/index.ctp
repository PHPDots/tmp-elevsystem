<?php $priceType  = Configure::read('priceType'); ?>
<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Activity Numbers'); ?></h5>  
            <?php 
            echo $this->Html->link(__('Add Activity Number'),array(
                'controller'    => 'activityNumbers',
                'action'        => 'add'
            ));
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>                    
                    <th align="left"><?php echo __('Name/Area');?></th>
                    <th align="left"><?php echo __('Activity Number');?></th>                    
                    <th><?php echo __('Actions');?></th>
                </tr>
                <?php 
                $i=0;
                if(!empty($activityNumbers)) {
                    foreach($activityNumbers as $activityNumber) {
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>     
                            <td align="left"> 
                                <?php echo ($activityNumber['ActivityNumber']['type'] == 'area') ? $areaListArr[$activityNumber['ActivityNumber']['area']].' ( '.Inflector::humanize($activityNumber['ActivityNumber']['status']).' )' : $priceType[$activityNumber['ActivityNumber']['type']]; ?>
                            </td>
                            <td align="left">
                                <?php echo $activityNumber['ActivityNumber']['activity_number']; ?>
                            </td>                            
                            <td align="center">
                                <?php 
                                echo $this->Html->link(__('Edit'), array(
                                    'controller'    => 'activityNumbers',
                                    'action'        => 'edit',
                                    $activityNumber['ActivityNumber']['id']
                                ));
                                ?> / <?php 
                                echo $this->Html->link(__('Delete'),array(
                                    'controller'    => 'activityNumbers',
                                    'action'  => 'delete',
                                    $activityNumber['ActivityNumber']['id']),array(
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
                        <td colspan="5" class="index_msg"><?php  echo __('No Activity Numbers are added'); ?></td>
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
        if(!empty($activityNumbers)) {
            echo $this->Form->create('ActivityNumber',array(
                'class'         => 'row-fluid',
                'url'           => array(
                'controller'    => 'activityNumbers',
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