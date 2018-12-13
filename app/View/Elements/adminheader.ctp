<header>
    <span id="mobileNav"><?PHP echo $this->Html->image('mobile-icon.png', array('alt' => 'mobile-icon')); ?></span>
    <span id="phoneNav"><?PHP echo $this->Html->image('mobile-icon.png', array('alt' => 'CakePHP')); ?></span>
    

    <?php if(!empty($currentUser)) { ?>
        <ul class="header-actions">
            <li>
                <a href="#">
                    <?php echo $this->Html->image('icon/14x14/header/settings.png'); ?>
                </a>
                <div class="dropdown"><div class="dropdown-inner">
                    <div class="summary">
                        <strong><?php echo __('Settings'); ?></strong> <?php echo __('for your account'); ?>
                    </div>
                    <?php 
                    if(!$iframe) {
                        echo $this->Html->link('<span class="head">'.$this->Html->image('icon/14x14/light/grid.png').__('Edit Profile').'</span>',
                        array(
                            'controller'    => 'adminusers',
                            'action'        => 'edit',
                            CakeSession::read("Auth.User.id"),
                        ),
                        array(
                            'class'     => 'dropdown-block clearfix',
                            'escape'    => FALSE
                        ));
                    }
                    $cal_icon =  $this->Html->image('icon/14x14/light/calendar.png');
                    if($currentUser['User']['google_token'] == ''){
                        $URL  = $this->Html->url(array('controller' => 'adminbookings','action'  => 'oauth2callback'));
                        $URL_name = "Google Calender Sign In";
                    }else{
                        $URL_name = "Google Calender Sign Out";
                        $URL  = $this->Html->url(array('controller' => 'adminbookings','action'  => 'desync'));
                    }
                    echo "<a class='dropdown-block clearfix'  href='".$URL."'><span class='head'>".$cal_icon.$URL_name."</span> </a>";

                    echo $this->Html->link('<span class="head">'.$this->Html->image('icon/14x14/light/cog2.png').' '.__('Logout').'</span>',
                    array(
                        'controller'    => 'adminusers',
                        'action'        => 'logout',
                        'plugin'        => FALSE,
                        '?'             => array(
                            'iframe'    => ($iframe)?true:false
                        )
                    ),
                    array(
                        'class'     => 'dropdown-block clearfix',
                        'escape'    => FALSE
                    ));
                    ?>
                </div></div>
            </li>
        </ul>
        
        <div class="logUser">
            <?php 
            echo $this->Html->link($this->Html->image($this->Html->profileImg(CakeSession::read("Auth.User.email_id"))),
                array('action'  => '#'),
                array(
                  'escape'   =>  false,
                  'class'    => 'logUserImg'
            ));
            ?>
            <div class="logUserInfo"><?php echo __('Welcome').' '; ?>
                <?PHP echo CakeSession::read("Auth.User.firstname"); ?>
                <div class="clear"></div>
            </div>
        </div>

        <div class="logUser" style="margin-top: 20px; color: white; font-weight: bold; font-size: 15px;">
            <?php echo date('d-m-Y h:i:s A'); ?>
        </div>
    <?php } ?>
    <div id="exc"></div>
</header>