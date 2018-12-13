<div class="top-bar">
<!--    <div class="breadcrumbs fLeft">
        <ul class="breadcrumb">
            <li>
                <a href="#">
                    <?php echo $this->Html->image('icon/14x14/light/home5.png', array('alt' => 'CakePHP'));?>
                    <?PHP 
                            echo $this->Html->link($sitename,array(
                                'controller'    => 'pages',
                                'action'        => 'home'
                            )); 
                     ?>
                </a> 
                <span class="divider">/</span>
                <?php
                    if(isset($pageTitle)){
                        $this->Html->breadcrumb($pageTitle);
                    }
                ?>
            </li>            
        </ul>
    </div> -->
</div>