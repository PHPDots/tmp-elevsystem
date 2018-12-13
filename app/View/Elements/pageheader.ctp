<ol class="breadcrumb">
    <li><?php
    echo $this->Html->link('<i class="fa fa-home"></i>',array(
            'controller'    => 'pages',
            'action'        => 'home'
        ),array(
            'escape'    => FALSE,
    ));
    ?>
    </li>
    <?php
        if(isset($pageTitle)){
            $this->Html->breadcrumb($pageTitle);
        }
    ?>
</ol>