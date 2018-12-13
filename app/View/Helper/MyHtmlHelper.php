<?php  

App::uses('HtmlHelper', 'View/Helper');

class MyHtmlHelper extends HtmlHelper {
    
    public function url($url = null, $full = false) {
       
        if(!isset($url['language']) && isset($this->params['language'])) {
          $url['language'] = $this->params['language'];
        }
        
        return parent::url($url, $full);
    }
   
    public function breadcrumb($pageTitle){
        $url = str_replace("/elev-admin/", "", $this->here);

        if((strpos($url, 'admin') != '' || $this->Session->read('Auth.User.role') != 'student') && $url != ''){
            if(is_string($pageTitle)){
                echo '<li class="active">' . $pageTitle . '</li>';
            }elseif(is_array($pageTitle)){
                $str = '';
                for($i=0;$i<(count($pageTitle)-1);$i++){
                    $str .= '<li>';
                    $str .= '<a href="' . $pageTitle[$i]['url'] . '">';
                    $str .= $pageTitle[$i]['name'];
                    $str .= '</a>';
                    $str .= '<span class="divider">/</span>';
                    $str .= '</li>';
                }
                $str .= '<li class="active">'. $pageTitle[$i]['name'] . '</li>';
                echo $str;
            }
        }else{
            if(is_string($pageTitle)){
                echo '<li><i class="fa fa-angle-double-right"></i>' . $pageTitle . '</li>';
            }elseif(is_array($pageTitle)){
                $str = '';
                for($i=0;$i<(count($pageTitle)-1);$i++){
                    $str .= '<li>';
                    $str .= '<a href="' . $pageTitle[$i]['url'] . '"><i class="fa fa-angle-double-right"></i>';
                    $str .= $pageTitle[$i]['name'];
                    $str .= '</a>';
                    $str .= '</li>';
                }
                $str .= '<li><a href="#"><i class="fa fa-angle-double-right"></i>'. $pageTitle[$i]['name'] . '</a></li>';
                echo $str;
            }
       }
   }
   
   public function webrootpath($filepath = ''){
       return $this->webroot . $filepath;
   }
   
   public function imagePreviewUrl($fileId){       
       return $this->url(array('controller'=>'Attachments','action'=>'get',$fileId,'preview'));
   }
   
   public function profileImg($emailId,$size = 40) {
        $grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $emailId ) ) ) . "?size=".$size;
        return $grav_url;
    }
   
   public function pageInnerTitle($title,$args = array()){
        ?>
    <div class="row-fluid addUserInfo-container">
        <div class="span10">              	
            <h5 class="addNewLicense">
                <icon><?php echo isset($args['icon'])?$args['icon']:''; ?></icon>
                <span>
                    <?php echo $title; ?>
                </span>
            </h5>
        </div>
        <?php if(isset($args['released_track'])) { ?>
        <div class="span2 pull-right">
            <?php
            echo $this->link(__('Reload'),array(),array(
                'class' => 'button button-green pull-right'
            ));
            ?>
        </div>
        <?php } ?>
        <div class="clear">&nbsp;</div>
    </div>
    <?php
    }
    
    public function timeConversion($from,$to){
        
        $time       = strtotime($to)-strtotime($from);
        $minutes    = $time/60;  
        $hours      = $minutes/60;
        $day        = floor($hours/24);
        $hours      = $hours % 24;
        $minutes    = $minutes % 60; 
        
        return $day.__(' Days ').$hours.__(' Hours ').$minutes.__(' Minutes');
        
    }  
}