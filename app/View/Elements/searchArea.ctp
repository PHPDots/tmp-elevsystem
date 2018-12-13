<?php echo $this->Form->create('Confession',array('type' => 'get','class' => 'search-form')); ?>
    <div class="search-box">
        <div class="span12">
            <div class="span12">
            <ul class="headerLinks user-headerLink">    
                <li>
                    <?php 
                         echo $this->Html->link(__('All'),array(
                                'action'     => 'index',                                      
                            ),array(
                            'class'      => ($gender == '') ? 'active-status' : '' ,
                         ));
                    ?>
                </li>
                <li>
                    <?php 
                         echo $this->Html->link(__('Male'),array(
                                'action'     => 'index',
                                'gender'    => 'male',
                            ),array(
                            'class'      => ($gender == 'male') ? 'active-status' : '',
                         ));
                    ?>
                </li>
                <li>
                    <?php 
                         echo $this->Html->link(__('Female'),array(
                                'action'    => 'index',
                                'gender'    => 'female',
                            ),array(
                            'class'      => ($gender == 'female') ? 'active-status' : '',
                         ));
                    ?>
                </li>
            </ul>
            </div>
        </div>
        <div class="span12 no-margin">
            <div class="span2 pull-left ">
                <?php
                    echo $this->Form->select('location',$locations,array(
                        'label' => FALSE,
                        'div'   => FALSE,
                        'class' => 'span12 pull-left chosen-select1',
                        'empty' => 'All Wards',
                        'value' => $location,
                    ));
                ?>
            </div>
            <div class="span6">
                <div class="center">
                <label class=""><b><?php echo __('Date From : '); ?></b></label>
                <?php
                echo $this->Form->input('date_from',array(
                    'type'      => 'text',
                    'label'     => false, 
                    'required'  => FALSE,
                    'id'        => 'confession_date_from',
                    'class'     => 'fields datepick report-form',
                    'div'       => FALSE,
                    'minYear'   => 100,
                    'maxYear'   => 0,
                    'value'     => $dateFrom,
                ));
                ?>
                <label class=""><b><?php echo __('Date To : '); ?></b></label>
                <?php
                echo $this->Form->input('date_to',array(
                    'type'      => 'text',
                    'label'     => false, 
                    'required'  => FALSE,
                    'id'        => 'confession_date_to',
                    'class'     => 'fields datepick report-form',
                    'div'       => FALSE,
                    'minYear'   => 100,
                    'maxYear'   => 0,
                    'value'     => $dateTo,
                ));
                ?>
                </div>
            </div>
            <div class="span1">
                <?php
                echo $this->Form->button(__('Search'),array(
                    'type'  => 'submit',
                    'class' => 'button button-green search-btn span12 pull-left',
                )); 
                ?>
            </div>
        </div>
        <div class="span12 search">
            <div class="span6 pull-right search-result">
                <label class="pull-right"> <?php echo ($search == TRUE) ? $this->Paginator->param('count').__(' Results Found') : ''; ?></label>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php echo $this->Form->end(); ?>