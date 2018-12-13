    <div class="submenus">
    <div class="message-navigation">
        <div class="button-container">
          <?PHP 
                echo $this->Html->link(__('Create message'),
                            array(
                                'controller'    => 'messages',
                                'action'        => 'add'
                            ),array(
                                'class'         => 'button small-button button-orange',
                                'data-toggle'   => 'modal',
                                'role'          => 'button'
                            )
                );
            ?>
        </div>
        
        <h5><?PHP echo __('Mailboxes'); ?></h5>
        <ul>            
            <li class="active">
            <?PHP 
                echo $this->Html->link($this->Html->image('icon/14x14/light/download5.png').__(' Discussion').' <span class="messageInbox"> 0 </span>',
                        
                            array(
                                'controller'    => 'messages',
                                'action'        => 'index'
                            ),array(                                
                                'escape'    => FALSE
                            )
                );
            ?>
            </li>
            <li>
                <?php 
                        echo $this->Html->link($this->Html->image('icon/14x14/light/upload4.png').__(' Sent').' <span> 0 </span>',
                                array(
                                    'controller'    => 'messages',
                                    'action'        => 'sent'
                                ),array(
                                    'escape'    => FALSE
                                )
                    );

                ?>               
            </li>
            <li>
                 <?php 
                        echo $this->Html->link($this->Html->image('star-grey.png').__(' Star').' <span> 0 </span>',
                                array(
                                    'controller'    => 'messages',
                                    'action'        => 'star'
                                ),array(
                                    'escape'    => FALSE
                                )
                    );

                ?>                    
            </li>
            <li>
                <?php 
                        echo $this->Html->link($this->Html->image('icon/14x14/light/tag1.png').__(' Drafts').' <span> 0 </span>',
                                array(
                                    'controller'    => 'messages',
                                    'action'        => 'draft'
                                ),array(
                                    'escape'    => FALSE
                                )
                    );

                ?>
            </li>            
        </ul>
    </div>
    </div>