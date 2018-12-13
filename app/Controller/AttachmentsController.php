<?php

App::import('Vendor'    , 'qquploader/uploader');

class AttachmentsController extends AppController {
    
    public  $paginate;
    public  $perPage     = 5;
    public  $name       = 'Attachments';
    public  $uses       = array('Attachment');
    
    private $uploader;
    private $sizeLimit;
    private $allowedExtensions;
    private $finfo;
    

    function __construct($request = null, $response = null) {
        
        parent::__construct($request, $response);
        
        $this->allowedExtensions    = array(
            
        );
        $this->sizeLimit            = 2 * 1024 * 1024;
        $this->uploader             = new qqFileUploader($this->allowedExtensions, $this->sizeLimit);
        
    }

    function index($layout = NULL){       
        
        $conditions     = array();
        $joins          = array();
        $categoryAction = array();
        
        $conditions['Attachment.uploader_id']   = $this->currentUser['User']['id'];
        
        if(!is_null($layout)){
            $this->layout = 'fancybox';
            $paginationAction   = array(
                'controller'    => 'Attachments',
                'action'        => 'index',
                 'fancybox'
             );
        }else{
            $paginationAction   = array(
                'controller'    => 'Attachments',
                'action'        => 'index',                
             );
        }
        
        $this->perPage = $this->getPerPage('Attachment');
        
        $this->paginate = array(
            'limit'         => $this->perPage,            
            'order'         => array('Attachment.id' => 'DESC'),
        );
        
        if(count($conditions) > 0){
            $this->paginate['conditions']   = $conditions;
        }
        
        if(count($joins) > 0){
            $this->paginate['joins']        = array_values($joins);
        }
       
        $this->Paginator->settings = $this->paginate;        
        
        
        $attachments            = $this->Paginator->paginate('Attachment');
        if(!is_null($layout)){
            $isFancybox = TRUE;       
        }else{
            $isFancybox = FALSE; 
        }
        $this->set(array(
            'attachments'               => $attachments,
            'isFancybox'                => $isFancybox,
            'perPage'                   => $this->perPage,
            'paginationAction'          => $paginationAction,                       
        ));
        
    }
    
    /**
     * Return the Attachment Details along with 
     * 
     * @param int $attachment_id
     */
    function get($attachment_id,$form = 'download'){
        
        $contentType    = '';
        $fileEntry      = $this->Attachment->findById($attachment_id);
        
        $file           = $this->getUploadPath('attachment') . $this->getFileName($attachment_id);
        $fileData       = file_get_contents($file);
        
        $this->set(array(
            'filename'      => $fileEntry['Attachment']['filename'],
            'fileData'      => $fileData,
            'form'          => $form,
            'contentType'   => $fileEntry['Attachment']['mime_type'],
            'filesize'      => strlen($fileData),
        ));
        
        $this->layout = 'file';
        
        if($form == 'download')
            return ;
        
        switch($fileEntry['Attachment']['mime_type']){
            case 'image/png':
            case 'image/jpeg':
                $this->response->type('png');
                break;
        }
        
    }
    
    function add($layout = NULL){        
       
        $this->SiteInfo->write('pageTitle', __('Add Attachment'));
        $multiple   = 'false';
       
        if(!is_null($layout)){
            $this->layout = 'fancybox';             
        }                
       
        if(isset($this->request->params['named']['multiple']) && ($this->request->params['named']['multiple'] == 'true')){
            $multiple = 'true';           
        }
       
        //$multiple   = $this->request->params['named']['multiple']?'true':'false';
       
        $isFancybox = $this->request->params['pass'];
        
        $args       = array(
            'multiple'      => $multiple,
            'isFancybox'    => $isFancybox,                   
        );
       
        $this->set($args);
    }
    
    /**
     * 
     * @param string $objectType
     */
    function getUploadPath($objectType){
        
        switch ($objectType){
            
            case 'attachment':
                
                return LISABETHDATAATTACHMENT;
                
                break;
        }
    }


    function upload($type = 'attachment'){

        $path   = $this->getUploadPath($type);
        
        $this->Attachment->Save(array(
            'Attachment'=>array(
                'filename'      => $this->uploader->getName(),
                'uploader_id'   => $this->currentUser['User']['id'],        
                'type'          => $type,
            )
        ));
        
        /**
         * @todo replace of $_REQUEST parameter
         */
        
        $result     = $this->uploader->myhandleUpload($this->getFileName($this->Attachment->id),$path);
        $file       = $this->getUploadPath($type) . $this->getFileName($this->Attachment->id);
        $mimeType   = $this->getMimeType($file); 
        
        $this->Attachment->saveField('mime_type',$mimeType);
        $save = FALSE;
        
        $result['id']          = $this->Attachment->id;   
        $result['filename']    = $this->uploader->getName();
        $result['icon_img']    = $this->Attachment->setAttachmentImage($mimeType);
        $result['mime_type']   = $mimeType;
        $result['save']        = $save;
        
        $this->set(array('result'=>$result));
        $this->layout = 'ajax';
    }
    
    private function getFileName($id,$type = ''){
        return "AT_{$id}.lisabethattach";
}

    private function getMimeType($file){
        
        if(!isset($this->finfo))
            $this->finfo = finfo_open(FILEINFO_MIME_TYPE); 
        
        return finfo_file($this->finfo, $file);    
    }
    
    function debug(){
        echo $this->getMimeType($this->getFileName(1));
        die('');
    }
    
    public function view($id){
        
        $attachment     = $this->Attachment->findById($id);
        $user           = Hash::extract($this->User->findById($attachment['Attachment']['uploader_id'],array(
            'User.id','User.firstname','User.lastname'
        )),'User');        
        $dateFromat     = $this->Option->getOption('date_format');
        $this->set(array(
            'isEdit'        => FALSE,            
            'attachment'    => $attachment,
            'user'          => $user,
            'dateFromat'    => $dateFromat
        ));
        
    }
}