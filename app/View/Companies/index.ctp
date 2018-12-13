<?php $priceType  = Configure::read('priceType'); ?>
<div class="inner-content"><div class="row-fluid"><div class="span12">      
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Companies'); ?></h5>  
            <?php 
            echo $this->Html->link(__('Add Company'),array(
                'controller'    => 'companies',
                'action'        => 'add'
            ));
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Company Name');?></th>
                    <th align="left"><?php echo __('Company Nick Name');?></th>
                    <th align="left"><?php echo __('City');?></th>
                    <th align="left"><?php echo __('Status');?></th>
                    <th><?php echo __('Actions');?></th>
                </tr>
                <?php 
                $i=0;
                if(!empty($companies)) {
                    foreach($companies as $company) {
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>
                            <td align="left">
                                <?php echo $company['Company']['name']; ?>
                            </td>
                            <td align="left">
                                <?php echo $company['Company']['nick_name']; ?>
                            </td>
                            <td align="left">
                                <?php echo $company['City']['name']; ?>
                            </td>
                            <td align="left">
                                <?php echo Inflector::humanize($company['Company']['status']); ?>
                            </td>
                            <td align="center">
                                <?php 
                                echo $this->Html->link(__('Edit'), array(
                                    'controller'    => 'companies',
                                    'action'        => 'edit',
                                    $company['Company']['id']
                                ));
                                ?> / <?php 
                                echo $this->Html->link(__('Delete'),array(
                                    'controller'    => 'companies',
                                    'action'        => 'delete',
                                    $company['Company']['id']),array(
                                        'class' => 'deleteElement'
                                    )
                                );
                                ?>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No Companies are added'); ?></td>
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
        if(!empty($companies)) {
            echo $this->Form->create('Company',array(
                'class'         => 'row-fluid',
                'url'           => array(
                'controller'    => 'companies',
                'action'        => 'index'
            )));
            
            echo $this->Form->input('perPage', array(
                'options'   => $perPageDropDown,
                'selected'  => $perPage, 
                'id'        => 'dropdown',
            ));
            
            $this->Form->end();
        }
        ?>
        </div>
    </div>
</div></div></div>