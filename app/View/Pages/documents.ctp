<?php $role       = CakeSession::read("Auth.User.role"); ?>

<div class="row">
    <div class="col-xs-12 col-sm-12 info">
        <p>
            Nedenfor kan du gense og downloade det materiale du har fået udleveret ved holdstart. 
        </p>
    </div>
    <div class="clearfix"></div>

    <div class="col-xs-12  info">
        <h3>
            <?php echo __('Dokumenter'); ?> 
            <div class="clearfix"></div>            
        </h3>
        <table width="100%" style="font-size:14px;">
            <tr class="table_heading">
                <th class="bill"><?php echo __('No.'); ?></th>
                <th class="bill"><?php echo __('Title'); ?></th>
                <th class="bill"><?php echo __('Action'); ?></th>
            </tr>
            <?php
            $pageNo         = isset($this->Paginator->params['named']['page'])?$this->Paginator->params['named']['page']:1;
            $i              = ($pageNo - 1)*$perPage;
            if(!empty($documents)) {
                foreach($documents as $document) {
                 ?>
                    <tr>
                        <td class="tbl_detail"><?php echo ++$i; ?></td>
                        <td class="tbl_detail"><?php echo $document['Page']['title']; ?></td>
                        <td class="tbl_detail">
                            <?php if($document['Page']['file'] !='') { ?>
                            <a type="application/octet-stream" href="<?php echo WEB_URL . 'documents' . DS .$document['Page']['file']; ?>" download/>Download </a> 
                            <?php } ?>
                        </td>
                    </tr>
                <?php   
                }             
            } else {
                ?>
                <tr>
                    <td class="tbl_detail" align="center"><?php echo __('No Documents are Avalible'); ?></td>
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
        <div class="col-xs-12 col-md-4 pagination pull-right">
            <?php
                if(!empty($documents)) {
                    echo $this->Form->create('DrivingLesson',array(
                                'class'         => 'row-fluid',
                                'url'           => array(
                                'controller'    => 'drivingLessons',
                                'action'        => 'index'
                             ),    
                         )
                    );

                    echo $this->Form->input('perPage', array(
                        'options'   => $perPageDropDown,
                        'selected'  => $perPage, 
                        'id'        => 'dropdown',
						'label' => 'Antal pr. side',
                        'class'     => 'form-control pull-right'
                    ));

                    $this->Form->end();
                }
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
