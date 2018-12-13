<div class="logo-img">
    <a href="#">
    <?php
    echo $this->Html->image('logo.png',array(
        'alt'   => 'Lisabeth'
    ));
    ?>
    </a>
</div>
<div class="login-txt">
    <label><?php echo __('Elev system'); ?></label>                
</div>
<!--Form_ct Starts Here -->
<div class="form_ct detail">
    <?php
    echo $this->Form->create('User', array(
        'url' => array(
            'controller' => 'users',
            'action' => 'login'
    )));
    ?>
        <div class="lbl"><?php echo __('Login information finder du I din velkomstmail'); ?></div>
        <div class="form-group ">
            <?php
            echo $this->Form->input('username',array(
                'label'         => FALSE,
                'placeholder'   => __('Brugernavn'),
                'class'         => 'form-control',
            ));
            ?>
        </div>
        <div class="form-group ">
            <?php
            echo $this->Form->input('password',
            array
            (
                'label'         => FALSE,
                'placeholder'   => __('Kodeord'),
                'class'         => 'form-control',
            ));
            ?>
        </div>
        <div class="form-group">
            <button name="login" id="login" value="" class="form-control btn "><?php echo __('Login'); ?></button>
        </div>
        <div class="lbl">
            <?php 
                echo $this->Html->link(__('Glemt kodeord?'),array(
                    'controller'    => 'users',
                    'action'        => 'forgotPassword'
                ),array(
                    'class'         => 'forgotPasswordLink'
                )); 
            ?>
        </div>
        <?php
        if(!empty($message)){
        ?>
        <div class="alert alert-danger" role="alert">
            <strong><?php echo $message; ?></strong>
        </div>
        <?php
        }
            
    echo $this->Form->end();
    ?>
</div>