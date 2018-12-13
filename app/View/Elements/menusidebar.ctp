<?php if(!empty($currentUser)){ ?>
<div class="profile clearfix">
    
    <?php
    // echo $this->Html->link('',array(
    //     'controller'    => 'pages',
    //     'action'        => 'home'
    // ),array(
    //     'escape'    => FALSE,
    //     'class'     => 'sidebar_div'
    // ));
   echo $this->Html->link(
         $this->Html->image('lisabeth/logo.png'),
           array(
               'controller'    => 'adminpages',
               'action'        => 'home'
           ),array(
               'escape' => FALSE
           )
   );
   ?>    
   
    <div class="profile-options">            
        <div class="info clearfix">
            <?php 
                echo $this->Html->link(
                      '<span class="name textUpperCase">'.$sitename.'</span>',
                        array(
                            'controller'    => 'pages',
                            'action'        => 'home'
                        ),array(
                            'escape' => FALSE
                        )
                );
            ?>
        </div>
    </div>
</div>
<?php } ?>
<?php
    if(isset($displayMenuItems) && !empty($displayMenuItems)){
        echo $this->MenuWalker->walk($displayMenuItems,array(
                        'idField'           => 'itemId',
                        'parentIdField'     => 'parentId',                                        
        ));
    }
?>