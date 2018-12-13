<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="widget">
        <div class="widget-header">
            <h5><?PHP echo __('Email Templates Settings'); ?></h5>
            <?PHP 
                echo $this->Html->link(
                            __('Add Email Template Settings'),
                            array(
                                'controller'    => 'emailTemplateSettings',
                                'action'        => 'add'
                            )
                );
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Setting Template Name');?></th>
                    <th align="left"><?php echo __('Slug');?></th>
                    <th align="left"><?php echo __('Actions');?></th>
                </tr>
                <?php
                $i=0;
                if(!empty($data)) {
                    foreach($data as $val){
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" >
                            <td align="center">
                            <?PHP 
                                echo $this->Html->link(
                                    $i,
                                    array(
                                        'controller'    => 'emailTemplateSettings', 
                                        'action'        => 'edit',
                                        $val['EmailTemplateSetting']['id']
                                    )
                                );
                            ?>
                            </td>
                            <td><?PHP echo $val['EmailTemplateSetting']['name']; ?></td>
                            <td><?PHP echo $val['EmailTemplateSetting']['template_type']; ?></td>                            
                            <td>
                               <?php 
                                    echo $this->Html->link(
                                        'Edit', 
                                        array(
                                            'action'    => 'edit',
                                            $val['EmailTemplateSetting']['id']
                                        )
                                    );
                            ?> / <?php 
                               echo $this->Html->link(
                                    'Delete',
                                    array('action'  => 'delete',$val['EmailTemplateSetting']['id'])
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
<!--        <div class="pagination">
        <?php
            if(!empty($data)) {
                
                echo $this->Form->create('EmailTemplateSetting',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'emailTemplateSettings',
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
        </div>-->
    </div>
</div></div></div>