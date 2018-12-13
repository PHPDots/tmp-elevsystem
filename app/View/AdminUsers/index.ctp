<div class="inner-content"><div class="row-fluid"><div class="span12">
    <?php 
    $roles = Hash::remove($roles, 'student');
    if(!empty($roles)){ ?>
    <div class="span12">
        <?php $role = (isset($this->request->params['named']['role'])) ? $this->request->params['named']['role'] : '';        ?>
        <ul class="headerLinks user-headerLink span4">
            <li>
                <?php 
                echo $this->Html->link(__('All'),array(
                       'action'     => 'index',
                   ),array(
                       'class'       => ($role == '') ? 'active-status' : '',
                ));
                ?>
            </li>
            <?php foreach($roles as $key => $userRole) { ?>
                <li>
                    <?php 
                    echo $this->Html->link($userRole,array(
                            'action'    => 'index',
                            'role'      => $key,    
                       ),array(
                           'class'       => ($role == $key) ? 'active-status' : '',
                    ));
                    ?>
                </li>
            <?php } ?>
        </ul>        
    </div>
    <?php  } ?>
    <div class="clearfix"></div>
    <div class="widget">
        <div class="widget-header">
            <h5><?php echo __('Users'); ?></h5>  
            <?php 
            echo $this->Html->link(__('Add User'),array(
                'controller'    => 'adminusers',
                'action'        => 'add'
            ));
            ?>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Name');?></th>
                    <th align="left"><?php echo __('Email Id');?></th>
                    <th align="left"><?php echo __('Role');?></th>
                    <th align="left"><?php echo __('Actions');?></th>
                </tr>
                <?php 
                $pageNo         = isset($this->Paginator->params['named']['page'])?$this->Paginator->params['named']['page']:1;
                $i              = ($pageNo - 1)*$perPage;
                if(!empty($users)) {          
                    foreach($users as $user){        
                        ?>
                        <tr class="<?php echo ($i++%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?PHP echo $i; ?>
                            </td>     
                            <td align="left"> 
                                <?php echo $user['User']['firstname'].' '.$user['User']['lastname']; ?>
                            </td>
                            <td align="left">
                                <?php echo $user['User']['email_id']; ?>
                            </td>
                            <td align="left">
                                <?php echo Inflector::humanize($user['User']['role']); ?>
                            </td>
                            <td align="left">
                                <?php                                    
                                echo $this->Html->link(__('View'), array(
                                        'controller'    => 'adminusers',
                                        'action'        => 'view',
                                        $user['User']['id']
                                ));
                                  ?> / <?php 
                                echo $this->Html->link(__('Edit'), array(
                                        'controller'    => 'adminusers',
                                        'action'        => 'edit',
                                        $user['User']['id']
                                ));
                                 ?>  <?php 
//                                    $object = ($user['User']['id'] == 'student')?'student':'user';
//                                    echo $this->Html->link(__('Bookings'), array(
//                                            'controller'    => 'bookings',
//                                            'action'        => 'index',
//                                            '?'             => array(
//                                                'object'    => $object,
//                                                'object_id' => $user['User']['id']
//                                            )
//                                    ),array(
//                                        'target'    => 'blank'
//                                    ));
                                 ?>  / <?php 
                                echo $this->Html->link(
                                    __('Delete'),
                                    array('controller'    => 'adminusers','action'  => 'delete',$user['User']['id']),
                                    array('class' => 'deleteElement')
                                );
                                if($user['User']['role'] == 'internal_teacher') {
                                    echo ' / '.$this->Html->link(__('View Bookings'), array(
                                            'controller'    => 'adminbookings',
                                            'action'        => 'calendar',
                                            '?'             => array(
                                                'teacher_booking_detail'    => $user['User']['id']
                                            )
                                            
                                    ),array(
                                        'target'    => 'blank'
                                    ));
                                 }
                                ?>  
                            </td>   
                        </tr>
                    <?php
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No Users are added'); ?></td>
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
            if(!empty($users)) {
                echo $this->Form->create('User',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'adminusers',
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