<div class="inner-content">
<div class="row-fluid">
<div class="span12">
    <div class="widget">
        <div class="widget-header">
            <h5><?PHP echo __('SMS Templates'); ?></h5>
            <?PHP 
                echo $this->Html->link(
                            __('Add SMS Template'),
                            array(
                                'controller'    => 'smsTemplates',
                                'action'        => 'add'
                            )
                );
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>
                    <th><?php echo __('Template');?></th>                   
                    <th><?php echo __('Actions');?></th>
                </tr>
                <?php
                $val=array();
                $pageNo = isset($this->Paginator->params['named']['page'])?$this->Paginator->params['named']['page']:1;
                $i      = ($pageNo - 1)*$perPage;   
                if(!empty($smsTemplates)) {
                    foreach($smsTemplates as $val){
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td>
                            <?PHP 
                                echo $this->Html->link(
                                    $val['SmsTemplate']['id'],
                                    array(
                                        'controller'    => 'smsTemplates', 
                                        'action'        => 'edit',
                                        $val['SmsTemplate']['id']
                                    )
                                );
                            ?>
                            </td>
                            <td>
                            <?PHP 
                                echo $val['SmsTemplate']['template'];
                            ?>
                            </td>                            
                            <td>
                               <?php 
                                    echo $this->Html->link(
                                        'Edit', 
                                        array(
                                            'action'    => 'edit',
                                            $val['SmsTemplate']['id']
                                        )
                                    );
                            ?> / <?php 
                                echo $this->Html->link(
                                    'Delete',
                                    array('action'  => 'delete',$val['SmsTemplate']['id']),
                                    array('class'   => 'deleteElement')
                                );
                            ?>
                            </td>
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
            if(!empty($data)) {
                echo $this->Form->create('SmsTemplate',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'smsTemplates',
                            'action'        => 'index'
                         ),    
                     )
                );
                
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
</div>
</div>
</div>