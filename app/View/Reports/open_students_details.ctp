<div class="inner-content"><div class="row-fluid"><div class="span12">
    <div class="pull-right activity">
        <?php
        echo $this->Html->link('<i class="fa fa-print"></i>  &nbsp;'. __(' Generate CSV'),array(
            'controller'    => 'reports',
            'action'        => 'index',
            '?'             => array(
                'report_type'           => $this->request->query['report_type'],                
                'teacher_id'            => isset($this->request->query['teacher_id'])?$this->request->query['teacher_id']:'',
                'city'                  => isset($this->request->query['city'])?$this->request->query['city']:'',
                'csv'                   => 'true',
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
            <?php 
            $title  = __('Current Open Student Details'); 
            $title .= (isset($this->request->query['student_id']) && !empty($this->request->query['student_id'])) ? ' '.$this->request->query['student_autosuggest'] : '';
            echo $title;
            ?>
            </h5>
        </div>
        <div class="tableLicense">
            <table cellpading="0" cellspacing="0" border="0" class="default-table" id="example">
                <thead>
                <tr>
                    <th><?php echo __('No.');?></th>
                    <th align="left"><?php echo __('Student Name');?></th> 
                    <th align="left"><?php echo __('Student Number');?></th>   
                    <th align="left"><?php echo __('Phone Number'); ?></th>                                     
                    <th align="left"><?php echo __('Last Module');?></th>
                    <th align="left"><?php echo __('Number of Driving Test'); ?></th>
                    <th align="left"><?php echo __('Hours'); ?></th>
                    <th align="left"><?php echo __('Balance');?></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                $pageNo = $this->params['paging']['User']['page'];
                $i      = (($pageNo - 1) * $perPage) + 1;                
                if(!empty($users)) {
                    foreach($users as $student) {
                        ?>
                        <tr class="<?php echo ($i%2==0)?'even':'odd'; ?>" align="center">
                            <td align="center">
                                <?php echo $i; ?>
                            </td>                            
                            <td align="left">
                                <?php echo $student['User']['firstname'].' '.$student['User']['lastname']; ?>
                            </td>                            
                            <td align="left">
                                <?php echo $student['User']['student_number']; ?>
                            </td>
                            <td align="left">
                                <?php echo $student['User']['phone_no']; ?>
                            </td>
                            <td align="left">
                                <?php echo $student['User']['module']; ?>
                            </td>
                            <td align="left">
                                <?php echo $student['User']['driving_lessons_count']; ?>
                            </td>
                            <td align="left">
                                <?php echo $student['User']['time']; ?>
                            </td>
                            <td align="left">
                                <?php echo (empty($student['User']['balance']))?'N/A':$student['User']['balance']; ?>
                            </td>
                        </tr>
                        <?php   
                        $i++;
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="5" class="index_msg"><?php  echo __('No Pending Student Charges.'); ?></td>
                    </tr>
                <?php 
                }
                ?>
                </tbody>
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
                    'report_type'           => $this->request->query['report_type'],                
                    'teacher_id'            => isset($this->request->query['teacher_id'])?$this->request->query['teacher_id']:'',
                    'city'                  => isset($this->request->query['city'])?$this->request->query['city']:'',                    
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