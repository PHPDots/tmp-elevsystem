<?php $priceType  = Configure::read('priceType'); ?>
<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Courses'); ?></h5>  
            <?php 
            echo $this->Html->link(__('Add Course'),array(
                'controller'    => 'courses',
                'action'        => 'add'
            ));
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>                    
                    <th align="left"><?php echo __('Name');?></th>                    
                    <th align="left"><?php echo __('Price');?></th>
                    <th align="left"><?php echo __('Teacher Time');?></th>                    
                    <th align="left"><?php echo __('Activity Number');?></th>                    
                    <th align="left"><?php echo __('Area');?></th>
                    <th><?php echo __('Actions');?></th>
                </tr>
                <?php 
                $i=0;
                if(!empty($courses)) {
                    foreach($courses as $course) {
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>     
                            <td align="left"> 
                                <?php echo $course['Course']['name']; ?>
                            </td>
                            <td align="left">
                                <?php echo $course['Course']['price'].' DKK'; ?>
                            </td>     
                            <td align="left">
                                <?php 
                                    $hours   = $course['Course']['teacher_time'];
                                    $hours  .= ($course['Course']['teacher_time'] > 1)?__(' Hours'):__(' Hour'); 
                                    echo $hours;
                                ?>
                            </td>     
                            <td align="left">
                                <?php echo $course['Course']['activity_number']; ?>
                            </td>     
                            <td align="left">
                                <?php echo $areaListArr[$course['Course']['area']]; ?>
                            </td>     
                            <td align="center">
                                <?php 
                                echo $this->Html->link(__('Edit'), array(
                                    'controller'    => 'courses',
                                    'action'        => 'edit',
                                    $course['Course']['id']
                                ));
                                ?> / <?php 
                                echo $this->Html->link(__('Delete'),array(
                                    'controller'    => 'courses',
                                    'action'        => 'delete',
                                    $course['Course']['id']),array(
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
                        <td colspan="7" class="index_msg"><?php  echo __('No Courses are added'); ?></td>
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
        if(!empty($courses)) {
            echo $this->Form->create('Course',array(
                'class'         => 'row-fluid',
                'url'           => array(
                'controller'    => 'courses',
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