<div class="inner-content"><div class="row-fluid"><div class="row-fluid"><div class="widget">
    <div class="widget-header"><h5><?php echo __('Terms And Conditions'); ?></h5></div> 
    <div class="widget-content  termsForm">
        <?php
            $url    = array(
                    'controller'    => 'tncs',
                    'action'        => 'updateTermUser'
            );
            if($iframe){
                $url['?']   = array(
                    'iframe'    => $iframe
                );
            }
            echo $this->Form->create('Tnc',array(
                'class' => 'form-horizontal',
                'id'    => 'acceptTerms',
                'url'   => $url                
            ));
            $i = 1;
          
            foreach($notifications['details'] as $notification){ 
        ?>
            <div class="form-row">
                <label class="span12"><?php echo $notification['Tnc']['terms']; ?></label>               
                <div class="clearfix"></div>
                <div class="centerAlignedArea span12">
                    <div class="radioStyle">
                        <?php
                            echo $this->Form->radio("term.{$notification['Tnc']['id']}",array(
                                'yes'   => __('Agree'),
                                'no'    => __('Do Not Agree')
                            ),array(
                                'hiddenField'   => FALSE,
                                'div'           => FALSE,
                                'legend'        => FALSE,                            
                            ));
                        ?>
                    </div>                     
                </div>    
                <div class="clearfix"></div>
                <label class="span3 pull-right onDate"><b><i>
                    <?php echo ' [ '.__('Date : ').date('d.m.Y',  strtotime($notification['Tnc']['created'])).' ] '; ?>
                </i></b></label>
            </div>
        <?php 
            $i++;
            } 
        ?>
        <div class="form-row centerAlignedArea">
            <div class="radioStyle">                
               <?php 
                   echo $this->Form->button(__('Submit'),array(
                       'type'  => 'submit',
                       'class' => 'button button-green',
                       'id'    => 'acceptedTerms'
                   ));
               ?>  
               <?php 
                   echo $this->Html->link(__('Cancel'),array(
                       'controller'    => 'users',
                       'action'        => 'logout'
                   ),array(
                       'class' => 'button button-red',                    
                   ));
               ?>
            </div>
        </div>
    </div>
</div></div></div></div>
