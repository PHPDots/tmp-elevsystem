 <div class="col-xs-12 info">
    <div class="col-xs-12 info-header no-padding">
        <h3 class="col-xs-8 no-padding"><?php echo __('Your Register Time'); ?></h3>
        <div class="col-xs-4 no-padding">
            <?php
                echo $this->Html->link(__('Register Your Time'),array(
                    'controller'    => 'users',
                    'action'        => 'registerYourTime'
                ),array(
                    'class'         => 'btn btn-warning pull-right'
                ));
            ?>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
    <table width="100%" style="font-size:14px;">
        <tr class="table_heading">
            <th class="bill" width="10%"><?php echo __('No.'); ?></th>
            <th class="bill" width="10%"><?php echo __('Type'); ?></th>            
            <th class="bill" width="25%"><?php echo __('Time'); ?></th>            
            <th class="bill" width="35%"><?php echo __('Purpose / City / Driving Type'); ?></th>    
            <th class="bill" width="20%" align="center"><?php echo __('Action'); ?></th>
        </tr>
        <?php
        $i = 1;
        if(!empty($registerdTimes)) {
            foreach($registerdTimes as $registerdTime) {                                
            ?>
                <tr>
                    <td class="tbl_detail" width="10%"><?php echo $i; ?></td>
                    <td class="tbl_detail" width="10%"><?php echo Inflector::humanize($registerdTime['TeacherRegisterTime']['type']); ?></td>                    
                    <td class="tbl_detail" width="25%"><?php echo $registerdTime['TeacherRegisterTime']['from']; ?></td>
                    <?php if($registerdTime['TeacherRegisterTime']['type'] == 'other'){ ?>
                    <td class="tbl_detail" width="35%"><?php echo String::truncate(strip_tags(__($registerdTime['TeacherRegisterTime']['purpose'])), 60, array('html' => true)); ?></td> 
                    <?php } else if($registerdTime['TeacherRegisterTime']['type'] == 'driving') { ?>
                    <td class="tbl_detail" width="35%"><?php echo $drivingTypes[$registerdTime['TeacherRegisterTime']['driving_type']]; ?></td>    
                    <?php } else { ?>
                    <td class="tbl_detail" width="35%"><?php echo $cities[$registerdTime['TeacherRegisterTime']['city']]; ?></td>
                    <?php } ?>
                    <td class="tbl_detail" width="20%" align="center">
                        <?php 
                            echo $this->Html->link('View',array(
                                'controller'    => 'users',
                                'action'        => 'registerTime',
                                $registerdTime['TeacherRegisterTime']['id']
                            ));
                        ?> / <?php 
                            echo $this->Html->link('Edit',array(
                                'controller'    => 'users',
                                'action'        => 'editRegisterTime',
                                $registerdTime['TeacherRegisterTime']['id']
                            ));
                        ?> 
                    </td>
                </tr>
            <?php
            $i++;
            }
        } else {
            ?>
            <tr>
                <td class="tbl_detail" colspan="5" align="center"><?php echo __('No Time Found'); ?></td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
<div class="clearfix"></div>
<div class="paginationCt">
    <div class="col-xs-12 col-md-5 pagination_no">
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
                                    array('class' => 'last paginate_button'),
                                    null,
                                    array('class' => 'paginate_button_disabled')
                                        );
        ?>
    </div>
    <div class="col-xs-12 col-md-3 pagination pull-right">
        <?php
            if(!empty($registerdTimes)) {
                echo $this->Form->create('TeacherRegisterTime',array(
                            'class'         => 'row-fluid',
                            'url'           => array(
                            'controller'    => 'users',
                            'action'        => 'registerTimeList'
                         ),    
                     )
                );

                echo $this->Form->input('perPage', array(
                    'options'   => $perPageDropDown,
                    'selected'  => $perPage, 
                    'id'        => 'dropdown',
                    'class'     => 'form-control pull-right'
                ));

                $this->Form->end();
            }
        ?>
    </div>
    <div class="clearfix"></div>
</div>
