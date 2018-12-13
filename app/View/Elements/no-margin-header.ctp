<header class="no-margin">
    <span id="mobileNav"><?PHP echo $this->Html->image('mobile-icon.png', array('alt' => 'mobile-icon')); ?></span>
    <span id="phoneNav"><?PHP echo $this->Html->image('mobile-icon.png', array('alt' => 'CakePHP')); ?></span>
    <ul class="header-actions">
        <li>
            <a href="#">
                <?PHP echo $this->Html->image('icon/14x14/header/settings.png'); ?>
            </a>
            <div class="dropdown"><div class="dropdown-inner">
                <div class="summary">
                    <strong><?PHP echo __('Settings'); ?></strong> <?PHP echo __('for your account'); ?></span>
                </div>                                 
                <?PHP 
                    echo $this->Html->link('<span class="head">'.$this->Html->image('icon/14x14/light/cog2.png').__(' Logout').'</span>',
                                array(
                                    'controller'    => 'users',
                                    'action'        => 'logout',
                                    'plugin'        => FALSE,
                                    '?'             => array(
                                        'iframe'    => ($iframe)?true:false
                                    )
                                ),
                                array(
                                    'class'     => 'dropdown-block clearfix',
                                    'escape'    => FALSE
                                )
                    );
                ?>
            </div></div>
        </li>
    </ul> 
    
    <div class="logUser">
        <?php      
            echo $this->Html->link(
                  $this->Html->image($this->Html->profileImg(CakeSession::read("Auth.User.email_id"))),
                      array('action'  => '#'), 
                      array(
                        'escape'   =>  false,
                        'class'    => 'logUserImg'
            ));
        ?>        
        <div class="logUserInfo"><?PHP echo __('Welcome '); ?>
            <?PHP echo CakeSession::read("Auth.User.firstname"); ?>
            <div class="clear"></div>
        </div>
    </div>
    <div id="exc"></div>
</header>
