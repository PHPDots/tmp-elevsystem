<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('CMS'); ?></h5>  
            <?php 
            echo $this->Html->link(__('Add New Documents'),array(
                'controller'    => 'adminpages',
                'action'        => 'add'
            ));
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>                    
                    <th align="left"><?php echo __('Title');?></th>
                    <th align="left"><?php echo __('Slug');?></th>                      
                    <th align="left"><?php echo __('Category');?></th>                      
                    <th><?php echo __('Actions');?></th>  
                </tr>
                <?php                
                $i=0;
                if(!empty($pages)) {
                    foreach($pages as $page){        
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?PHP echo $i; ?>
                            </td>     
                            <td align="left"> 
                                <?php echo $page['Page']['title']; ?>
                            </td>
                            <td align="left">
                                <?php echo $page['Page']['slug']; ?>
                            </td>
                            <td align="left">
                                <?php echo $category[$page['Page']['category_code']]['name']; ?>
                            </td>                            
                            <td align="center">
                                <?php                                    
                                // echo $this->Html->link(__('View'), array(
                                //         'controller'    => 'adminpages',
                                //         'action'        => 'view',
                                //         $page['Page']['slug']
                                // ));
                                  ?>  <?php 
                                echo $this->Html->link(__('Edit'), array(
                                        'controller'    => 'adminpages',
                                        'action'        => 'edit',
                                        $page['Page']['id']
                                ));
                                 ?> / <?php 
                                echo $this->Html->link(__('Delete'),array(
                                    'controller'    => 'adminpages',
                                    'action'  => 'delete',
                                    $page['Page']['id']),array(
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
                        <td colspan="4" class="index_msg"><?php  echo __('No Pages are added'); ?></td>
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
            if(!empty($pages)) {
                echo $this->Form->create('Page',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'adminpages',
                            'action'        => 'documents'
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