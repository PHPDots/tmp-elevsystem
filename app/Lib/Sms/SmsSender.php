<?php 

class SmsSender {
    
    public $username;
    public $password;
    public $msisdn;
    public $url;
    public $domain1;
    public $domain2;
    public $to;
    public $from;
    
    function __construct($args = array()) {
        $this->username = $args['username'];
        $this->password = $args['password'];
        $this->domain1  = $args['url1'];
        $this->domain2  = $args['url1'];
        $this->to       = $args['to'];
        $this->from     = $args['from'];
        
    }
    
    private function character_resolve($body) {
        $special_chrs = array(
            'Δ'=>'0xD0', 'Φ'=>'0xDE', 'Γ'=>'0xAC', 'Λ'=>'0xC2', 'Ω'=>'0xDB',
            'Π'=>'0xBA', 'Ψ'=>'0xDD', 'Σ'=>'0xCA', 'Θ'=>'0xD4', 'Ξ'=>'0xB1',
            '¡'=>'0xA1', '£'=>'0xA3', '¤'=>'0xA4', '¥'=>'0xA5', '§'=>'0xA7',
            '¿'=>'0xBF', 'Ä'=>'0xC4', 'Å'=>'0xC5', 'Æ'=>'0xC6', 'Ç'=>'0xC7',
            'É'=>'0xC9', 'Ñ'=>'0xD1', 'Ö'=>'0xD6', 'Ø'=>'0xD8', 'Ü'=>'0xDC',
            'ß'=>'0xDF', 'à'=>'0xE0', 'ä'=>'0xE4', 'å'=>'0xE5', 'æ'=>'0xE6',
            'è'=>'0xE8', 'é'=>'0xE9', 'ì'=>'0xEC', 'ñ'=>'0xF1', 'ò'=>'0xF2',
            'ö'=>'0xF6', 'ø'=>'0xF8', 'ù'=>'0xF9', 'ü'=>'0xFC',
        );

        $ret_msg = '';
        
        if( mb_detect_encoding($body, 'UTF-8') != 'UTF-8' ) {
            $body = utf8_encode($body);
        }
        
        for ( $i = 0; $i < mb_strlen( $body, 'UTF-8' ); $i++ ) {
            $c = mb_substr( $body, $i, 1, 'UTF-8' );
            if( isset( $special_chrs[ $c ] ) ) {
                    $ret_msg .= chr( $special_chrs[ $c ] );
            }
            else {
                    $ret_msg .= $c;
            }
        }
        
        return $ret_msg;
    }
    
    function sms_body ( $no , $message ,  $domain = NULL ) {
        
        $post_fields = array (
            'domain'    => (is_null($domain))?$this->domain1:$domain,         
            'message'   => $this->character_resolve($message),
            'to'        => $no,     
            'from'      => $this->from
        );

        /*$str  ="{$post_fields['domain']}";
        $str .= "?username={$post_fields['username']}"; // Username
        $str .= "&password={$post_fields['password']}"; // Pass
        $str .= "&to={$post_fields['to']}"; // Reciever
        $str .= "&from={$post_fields['from']}"; // Sender
        $str .= "&message=".urlencode("{$post_fields['message']}");
        $this->send_message($str , $message , $no);*/

        $mobile_no = '';
        if(str_replace("+45", "", $post_fields['to']) == '919033509199'){
            $mobile_no = '+' . str_replace("+45", "", $post_fields['to']);
        }else if(substr($post_fields['to'], 0, 3) == '+45'){
            $mobile_no = $post_fields['to'];
        } else if(substr($post_fields['to'], 0, 2) == '45'){
            $mobile_no = '+' . $post_fields['to'];
        } else {
            $mobile_no = '+45' . $post_fields['to'];
        }

        if(!empty($mobile_no)){
            $array = array(
                'message' => array(
                    'recipients'    =>  $mobile_no,
                    'sender'        =>  $post_fields['from'],
                    'message'       =>  utf8_encode($post_fields['message']),
                    'format'        =>  'UNICODE',
                    'charset'       =>  'UTF-8'
            ));

            $str =  json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            if($mobile_no == "+919033509199"){
                $temp_data = array();
            }else{
                $temp_data = array();
            }
            $temp_data = $this->sendMessage($post_fields['domain'], $str);    
            return $temp_data;
        }
    }

    private function send_message ($url,$message,$no) {
        
        $this->url  = $url;    
        $ch         = curl_init( );

        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $response_string    = curl_exec( $ch );
        $curl_info          = curl_getinfo( $ch );
        
        if ( $response_string == FALSE ) {
            $this->sms_body($no , $message , $this->domain2);            
        }
        
        curl_close( $ch );

        return TRUE;
    }

    private function sendMessage($url, $message_string) {
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $tmp_return = json_decode(curl_exec($ch));
        curl_close ($ch);
        return $tmp_return;
    }
    
}