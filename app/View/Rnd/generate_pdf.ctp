<?php 
$pdf = new XTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 


$pdf->xheadercolor      = array(265,265,265); 
$pdf->xheadertexteng    = 'M G O S CHURCH - BARODA';
$pdf->xfootertext       = ''; 
$this->footertemplate   = '';

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 12));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(20, 80 , 20);
// set margins


// set some language-dependent strings (optional)
$lg = Array();
$lg['a_meta_charset'] = 'UTF-8';
$lg['a_meta_dir'] = 'rtl';
$lg['a_meta_language'] = 'fa';
$lg['w_page'] = 'page';
$pdf->setLanguageArray($lg);


// ---------------------------------------------------------

// ---------------------------------------------------------
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->SetFont('courier');
$pdf->setRTL(false);
$pdf->SetFontSize(10);

// Introduction

$pdf->headertemplate = 'reciept';
$pdf->AddPage('L','A4');

$content    = $this->Element('pdf/reciept',array(
    'receiptDetails'    => $receiptDetails,
    'membersDetails'    => $membersDetails,
    'location'          => $location,
    'donationTypes'     => $donationTypes
));

$pdf->Ln();
$pdf->writeHTML($content, true, false, true, false, '');

echo $pdf->Output('application.pdf', 'I');

