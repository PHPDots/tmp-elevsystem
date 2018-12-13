<?php 

App::import('Vendor','tcpdf/tcpdf');

class XTCPDF extends TCPDF
{
    var $xheadertexteng  = ''; 
    var $xheadercolor    = array(0,0,200);     
    var $headertemplate  = '';    
    var $footertemplate  = '';

    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, $this->xheadertexteng, 0, false, 'C', 0, '', 0, false, 'M', 'M');
    } 
     
    public function Footer() {
    }
}