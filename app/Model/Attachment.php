<?PHP
class Attachment extends AppModel {
    
    function testMethod(){
        return 'this is from ' .  __FILE__;
    }
    
    function afterFind($results, $primary = false) {
        
        for($i=0;$i<count($results);$i++){
            
            if(!isset($results[$i]['Attachment']))
                continue;
            
            $results[$i]['Attachment']['icon_img'] = $this->setAttachmentImage($results[$i]['Attachment']['mime_type']);
            
        }
        
        return $results;
    }
    
    function setAttachmentImage($mimetype){
        
        switch($mimetype){
                case 'image/png':
                case 'image/jpeg':
                    $results    = 'img/pic.png';                   
                    break;
                case 'application/vnd.ms-office':
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.d':
                    $results    = 'img/doc.ico';                   
                    break;
                case 'application/pdf':
                    $results    = 'img/pdf.png';                    
                    break;
                default:
                    $results    = 'img/doc.ico';                   
                    break;
            }
            
            return $results;
        
    }
    
    function getFile($attachment_id){
         
        $file           = FLYAPPDATAATTACHMENT. "AT_{$attachment_id}.nicattach";
        $fileData       = file_get_contents($file);
        
        return $fileData;
    }
    
    function deleteAttachment($id){
        $qry = "DELETE FROM `attachment_categories` WHERE attachment_id = {$id}";
        $this->query($qry);
    }
    
}