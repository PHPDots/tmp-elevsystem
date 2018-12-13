<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="pull-right activity">
        <?php
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __(' Generate CSV'),array(
            'controller'    => 'reports',
            'action'        => 'index',
            '?'             => array(
                'report_type'   => $this->request->query['report_type'],
                'city'          => $this->request->query['city'],
                'date_from'     => $this->request->query['date_from'],
                'date_to'       => $this->request->query['date_to'],
                'csv'           => 'true',
            )),array(
                'class'     => 'button button-green',           
                'escape'    => FALSE,
        ));
        ?>
    </div>
    <div class="clearfix"></div>   
    <div class="widget">
        <div class="widget-header">
            <h5>
            <?php echo (empty($searchString)) ? __('Theory Report') : __('Theory Report').  implode(' ', $searchString); ?>
            </h5>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table">
                <tr>
                    <th><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Instructor Name');?></th>
                    <th align="left"><?php echo __('Registered From');?></th>                    
                    <?php if(isset($this->request->query['city']) && empty($this->request->query['city'])) { ?>
                    <th align="left"><?php echo __('City');?></th>
                    <?php } ?>                 
                </tr>
                <?php 
                $pageNo = $this->params['paging']['TeacherRegisterTime']['page'];
                $i = (($pageNo - 1) * $perPage) + 1;
                if(!empty($registeredTeachers)) {
                    foreach($registeredTeachers as $teacher){
                        ?>
                        <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>
                            <td align="left">
                                <?php echo $users[$teacher['TeacherRegisterTime']['user_id']]['firstname'].' '.$users[$teacher['TeacherRegisterTime']['user_id']]['lastname']; ?>
                            </td>
                            <td align="left">
                                <?php echo $teacher['TeacherRegisterTime']['from']; ?>
                            </td>                            
                            <?php if(isset($this->request->query['city']) && empty($this->request->query['city'])) { ?>
                            <td align="left">
                                <?php echo $teacher['TeacherRegisterTime']['city']; ?>
                            </td>
                            <?php } ?>                            
                        </tr>
                    <?php       
                        $i++;
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="7" class="index_msg"><?php  echo __('No Instructor Registered.'); ?></td>
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
        $this->paginator->options(array('url' => array('?' => $this->request->query)));
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
            echo $this->Form->create('Report',array(
                'class'         => 'row-fluid',
                'url'           => array(
                'controller'    => 'reports',
                'action'        => 'bookingReport',
                '?' => array(
                    'report_type'   => $this->request->query['report_type'],
                    'date_from'     => $this->request->query['date_from'],
                    'date_to'       => $this->request->query['date_to'],
                    'area_id'       => $this->request->query['area_id'],
                ),
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