<div class="row">
    <div class="col-xs-12 info">
        <h3><?php echo __('Your Students'); ?></h3>
        <table width="100%" style="font-size:14px;">
            <tr class="table_heading">
                <th class="bill"><?php echo __('No.'); ?></th>
                <th class="bill"><?php echo __('Name'); ?></th>
                <th class="bill"><?php echo __('Email Id'); ?></th>
                <th class="bill"><?php echo __('Contact No.'); ?></th>                
                <th class="bill"><?php echo __('Address'); ?></th>
            </tr>
            <?php
            $i = 1;
            if(!empty($users)) {
                foreach($users as $user) {                    
                ?>
                    <tr>
                        <td class="tbl_detail"><?php echo $i; ?></td>
                        <td class="tbl_detail"><?php echo $user['User']['firstname'].' '.$user['User']['lastname']; ?></td>
                        <td class="tbl_detail"><?php echo $user['User']['email_id']; ?></td>
                        <td class="tbl_detail"><?php echo $user['User']['phone_no']; ?></td>                        
                        <td class="tbl_detail"><?php echo $user['User']['address']; ?></td>
                    </tr>
                <?php
                $i++;
                }
            } else {
                ?>
                <tr>
                    <td class="tbl_detail" colspan="5" align="center"><?php echo __('No Students Found'); ?></td>
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
                if(!empty($users)) {
                    echo $this->Form->create('User',array(
                                'class'         => 'row-fluid',
                                'url'           => array(
                                'controller'    => 'users',
                                'action'        => 'index'
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
</div>