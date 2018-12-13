<div class="row">
    <div class="col-xs-12 col-sm-7 info">
        <h3><?php echo __('Din Profil'); ?></h3>
        <div class="profile">
          <p>
            <?php echo "Navn : ".$user['User']['firstname'].' '.$user['User']['lastname']; ?><br/>
          </p>
          <p>
            <?php echo __('Telefon nr. 1 : ').$user['User']['phone_no']; ?><br/>
            <?php echo (!empty($user['User']['other_phone_no'])) ? __('Telefon nr. 2 : ').$user['User']['other_phone_no'].'<br/>' : ''; ?>
            <?php echo __('E-mail : ').$user['User']['email_id']; ?><br/>
            <?php echo __('Adresse : ').$user['User']['address']; ?><br/>
            <?php echo ($user['User']['city']) ? __('By : ').$user['User']['city'].'<br/>' : ""; ?>
            <?php echo ($user['User']['zip'] != '') ? __('Post nr. : ').$user['User']['zip'].'<br/>' : ''; ?>
            <?php if($currentUser['User']['role'] == 'student'){?>    
            <?php 
            echo __('Elev nummer : ').$user['User']['student_number'];?><br/>
            <?php } ?>
          </p>
        </div>
        <?php
                echo $this->Html->link('Rediger',
                                        array('action' => 'edit'),
                                        array('class' => 'btn btn-success',
                                            'escape' => FALSE,)
                            );
                        ?>
    </div>    
    
    <?php if($currentUser['User']['role'] == 'student'){?>    
    <div class="col-xs-12 col-sm-5 ">
        <div class="col-xs-12 grey-block" >
            <div class="col-xs-12 col-md-4 profile_pic">
                <?php if(isset($teacher['User']['avatar_id']) && $teacher['User']['avatar_id'] != 0){ ?>
                    <img src="<?php echo $this->Html->imagePreviewUrl($teacher['User']['avatar_id']); ?>" />    
                <?php
                }else{
                    echo $this->Html->image('default-medium.png');
                }
                ?>
            
            </div>
            <div class="col-xs-12 col-md-8">
                <h3><?php echo __('Din kørelærer'); ?></h3>
                <h4>
                    <?php
                    echo (isset($teacher) && !empty($teacher)) ? $teacher['User']['firstname'].' '.$teacher['User']['lastname'] : 'Kommer senere';
                    ?>
                    <?php if(isset($teacher) && !empty($teacher['User']['phone_no'])) { ?>
                    <span><br/>
                        <?php
                            echo __('Telefon : ');
                            echo $teacher['User']['phone_no'];
                        ?>
                    </span>
                    <?php } ?>
                </h4>
            </div>
        </div>
    </div>    
    <?php } ?>
    <?php if($currentUser['User']['role'] == 'internal_teacher'){?>
    <div class="col-xs-12 col-sm-5 ">
        <div class="col-xs-12 grey-block" >
            <div class="col-xs-12 col-md-4 profile_pic">
                <?php if(isset($user['User']['avatar_id']) && $user['User']['avatar_id'] != 0){ ?>
                    <img src="<?php echo $this->Html->imagePreviewUrl($user['User']['avatar_id']); ?>" />    
                <?php
                }else{
                    echo $this->Html->image('default-medium.png');
                }
                ?>
            
            </div>
            <div class="col-xs-12 col-md-8">
                <h3><?php echo __('Number Of students'); ?></h3>
                <h4>                    
                    <?php echo $this->Paginator->param('count'); ?>                    
                </h4>
            </div>
        </div>
    </div>    

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
                    <td class="tbl_detail" colspan="4" align="center"><?php echo __('No Students Found'); ?></td>
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
                                'action'        => 'view'
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
    <div class="col-xs-12">
        <?php
            echo $this->Html->link(__('Edit'),array(
                'controller'    => 'users',
                'action'        => 'edit'
            ),array(
                'class'         => 'col-xs-2 btn btn-warning'
            ));
        ?>
        
    </div>
    <?php } ?>
</div>