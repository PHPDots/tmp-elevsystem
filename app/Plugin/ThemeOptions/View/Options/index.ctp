<div class="inner-content">   
    <?php 
        $this->Html->pageInnerTitle(__('General Settings'));
    ?>          
    <div class="row-fluid"><div class="widget">
        <?php
            echo $this->Form->create('Option',array(
                'class' => 'form-horizontal'
            ));
        ?>
        <div class="widget-header">
            <h5><?PHP echo __('Options'); ?></h5>
        </div>
        <div class="widget-content no-padding">
            <div class="form-row">
                <label class="field-name"><?php echo __('Site Title'); ?>:</label>
                <div class="field">     
                    <?php 
                    $val    = isset($options['site_title'])?$options['site_title']:'';
                    $args   = array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Name of the site'),                                                            
                            'value'         => $val
                    );

                    echo $this->Form->input('site_title',$args);
                    ?>                    
                </div>
                <div class="clearfix"></div>
            </div>
            
            <div class="form-row">
                <label class="field-name"><?php echo __('Home Page'); ?>:</label>
                <div class="field">     
                    <?php 
                    $val    = isset($options['home_page'])?$options['home_page']:'';
                    $args   = array(
                            'label' => false,
                            'div'   => null,
                            'class' => 'span12',
                            'empty' => __('Select Page'),                                                            
                            'value' => $val
                    );

                    echo $this->Form->select('home_page',$pages,$args);
                    ?>                    
                </div>
                <div class="clearfix"></div>
            </div> 
            
            <div class="form-row">
                <div class="span6">
                    <label class="span4"><?php echo __('Teacher Notification Time (Minutes)'); ?>:</label>
                    <div class="span8">     
                        <?php 
                        $val    = isset($options['teacher_notification_time'])?$options['teacher_notification_time']:'';

                        $args   = array(
                            'label'         => false,
                            'div'           => null,
                            'class'         => 'span12',
                            'placeHolder'   => __('Teacher Notification Time in Minutes'),                                                            
                            'value'         => $val
                        );

                        echo $this->Form->input('teacher_notification_time',$args);
                        ?>                    
                    </div>
                </div>
                <div class="span6">
                    <label class="span4"><?php echo __('Student Notification Time  (Minutes)'); ?>:</label>
                    <div class="span8">     
                        <?php 
                        $val    = isset($options['student_notification_time'])?$options['student_notification_time']:'';

                        $args   = array(
                                'label'         => false,
                                'div'           => null,
                                'class'         => 'span12',
                                'placeHolder'   => __('Student Notification Time in Minutes'),                                                            
                                'value'         => $val
                        );
                            
                        echo $this->Form->input('student_notification_time',$args);
                        ?>                    
                    </div>
                </div>
                
                <div class="clearfix"></div>
            </div> 
            <div class="form-row">
                <label class="field-name"><?php echo __('IP Address'); ?>:</label>
                <div class="field">     
                    <?php 
                    $val    = isset($options['ip_adrress']) ? $options['ip_adrress'] : '';
                    $args   = array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span6',
                        'placeHolder'   => __('Enter IP Address'),                                                            
                        'value'         => $val
                    );

                    echo $this->Form->input('ip_adrress',$args);
                    ?>                    
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-row">
                <label class="field-name"><?php echo __('Relesed Track IP Address'); ?>:</label>
                <div class="field">     
                    <?php 
                    $val    = isset($options['release_track_ip_adrress']) ? $options['release_track_ip_adrress'] : '';
                    $args   = array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span6',
                        'placeHolder'   => __('Enter IP Address'),                                                            
                        'value'         => $val
                    );

                    echo $this->Form->input('release_track_ip_adrress',$args);
                    ?>                    
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-row">
                <label class="field-name"><?php echo __('All Tracks Booked Color'); ?>:</label>
                <div class="field">     
                    <?php 
                    $val    = isset($options['all_tracks_booked']) ? $options['all_tracks_booked'] : '';
                    $args   = array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span6 color',
                        'placeHolder'   => __('Select Color'),                                                            
                        'value'         => $val
                    );

                    echo $this->Form->input('all_tracks_booked',$args);
                    ?>                    
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-row">
                <label class="field-name"><?php echo __('Few Tracks Remaining to be Booked Color'); ?>:</label>
                <div class="field">     
                    <?php 
                    $val    = isset($options['some_tracks_booked']) ? $options['some_tracks_booked'] : '';
                    $args   = array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span6 color',
                        'placeHolder'   => __('Select Color'),                                                            
                        'value'         => $val
                    );

                    echo $this->Form->input('some_tracks_booked',$args);
                    ?>                    
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-row">
                <label class="field-name"><?php echo __('No Tracks Booked Color'); ?>:</label>
                <div class="field">     
                    <?php 
                    $val    = isset($options['no_tracks_booked']) ? $options['no_tracks_booked'] : '';
                    $args   = array(
                        'label'         => false,
                        'div'           => null,
                        'class'         => 'span6 color',
                        'placeHolder'   => __('Select Color'),                                                            
                        'value'         => $val
                    );

                    echo $this->Form->input('no_tracks_booked',$args);
                    ?>                    
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-row">                
                <div class="field">
                <?php
                echo $this->Form->button('<i class="icon-ok icon-white"></i> '.__(' Save'),array(
                    'class'         => 'button button-blue',
                    'type'          => 'submit',
                ),array(
                    'escape' => FALSE
                ));
                echo $this->Html->link('<i class="icon-remove icon-white"></i> '.__('Cancel'),array(
                    'controller'    => 'pages',
                    'action'        => 'home',
                    'plugin'        => false
                ),array(
                    'class'         => 'button button-red',
                    'escape'        => FALSE
                ));
                ?>
                </div>
                <div class="clearfix"></div>
            </div>               
        </div> 
    </div></div>
</div>


