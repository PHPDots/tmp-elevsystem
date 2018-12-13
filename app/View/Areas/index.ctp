<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Areas'); ?></h5>  
            <?php 
            echo $this->Html->link(__('Add Area'),array(
                'controller'    => 'areas',
                'action'        => 'add'
            ));
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>                    
                    <th align="left"><?php echo __('Name');?></th>
                    <th align="left"><?php echo __('Slug');?></th>     
                    <th align="center"><?php echo __('Tracks Count');?></th>     
                    <th align="left"><?php echo __('Address');?></th>   
                    <th><?php echo __('Actions');?></th>  
                </tr>
                <?php                
                $i=0;
                if(!empty($areas)) {
                    foreach($areas as $area){        
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?PHP echo $i; ?>
                            </td>     
                            <td align="left"> 
                                <?php 
                                
                                    if($area[0]['lane'] != 0){
                                        echo $this->Html->link($area['Area']['name'],array(
                                            'controller'    => 'adminbookings',
                                            'action'        => 'calendar',
                                            '?'             => array(
                                                'area'      => $area['Area']['slug']
                                            )
                                        ),array(
                                            'target'        => '_blank'
                                        ));
                                    }else{
                                        echo $area['Area']['name'];
                                    }                                   
                                                                           
                                ?>
                            </td>
                            <td align="left">
                                <?php echo $area['Area']['slug']; ?>
                            </td>
                            <td align="center">
                                <?php echo $area[0]['lane']; ?>
                            </td>
                            <td align="left">
                                <?php echo $area['Area']['address']; ?>
                            </td>
                            <td align="center">
                                <?php 
                                echo $this->Html->link(__('Bookings'), array(
                                        'controller'    => 'adminbookings',
                                        'action'        => 'calendar',
                                        '?'             => array(
                                            'area'      => $area['Area']['slug']
                                        )                                   
                                ),array(
                                    'target'    => 'blank'
                                ));
                                ?> / 
                                <?php 
                                echo $this->Html->link(__('Tracks'), array(
                                        'controller'    => 'Tracks',
                                        'action'        => 'index',
                                        '?'             => array(
                                            'area'      => $area['Area']['slug']
                                        )                                        
                                ));
                                ?> / 
                                <?php 
                                echo $this->Html->link(__('Edit'), array(
                                        'controller'    => 'areas',
                                        'action'        => 'edit',
                                        $area['Area']['id']
                                ));
                                ?> / <?php 
                                echo $this->Html->link(__('Delete'),array(
                                    'controller'    => 'areas',
                                    'action'  => 'delete',
                                    $area['Area']['id']),array(
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
                        <td colspan="5" class="index_msg"><?php  echo __('No Areas are added'); ?></td>
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
                echo $this->Form->create('Area',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'areas',
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