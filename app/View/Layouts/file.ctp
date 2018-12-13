<?php
switch($form){
    case 'preview':
        header("Content-Type:{$contentType}");
        header("Content-Length:{$filesize}");
        break;          
    default :
        header('Content-Type: application/x-download');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        break;
}
echo $this->fetch('content'); 

