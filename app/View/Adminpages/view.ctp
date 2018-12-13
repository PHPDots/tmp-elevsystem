<div class="inner-content"><div class="row-fluid">
    <?php
        echo $this->Html->link('<i class="fa fa-pencil"></i>&nbsp;&nbsp;'.__('Edit'),array(
            'controller'    => 'adminpages',
            'action'        => 'edit',
            $page['Page']['id']
        ),array(
            'escape'    => FALSE,
            'class'     => 'button button-green',
        ));
    ?> 
    <div class="clearfix" style="margin: 10px 0px;"></div>
    <div class="widget">
        <div class="widget-header"><h5><?php echo $page['Page']['title']; ?></h5></div>
        <div class="slide">
            <div class="widget-content">
                <?php echo $page['Page']['body']; ?>
            </div>
        </div>        
    </div>
    <?php
        echo $this->Html->link('<i class="fa fa-pencil"></i>&nbsp;&nbsp;'.__('Edit'),array(
            'controller'    => 'adminpages',
            'action'        => 'edit',
            $page['Page']['id']
        ),array(
            'escape'    => FALSE,
            'class'     => 'button button-green',
        ));
    ?>
</div></div>
