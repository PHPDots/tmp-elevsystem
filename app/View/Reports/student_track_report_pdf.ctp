<?php
$pdf = new XTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
$pdf->xheadercolor      = array(265,265,265); 
$pdf->xheadertexteng    = __('Booking Report').implode(' ',$searchString);
$pdf->xfootertext       = ''; 
$this->footertemplate   = '';

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 12));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
// set margins


//// set some language-dependent strings (optional)
//$lg = Array();
//$lg['a_meta_charset'] = 'UTF-8';
//$lg['a_meta_dir'] = 'rtl';
//$lg['a_meta_language'] = 'fa';
//$lg['w_page'] = 'page';
//$pdf->setLanguageArray($lg);

// ---------------------------------------------------------

// ---------------------------------------------------------
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->SetFont('courier');
$pdf->setRTL(false);
$pdf->SetFontSize(9);

// Introduction
$pdf->AddPage('L','A4');

$content    = $this->Element('pdf/table',array(
    'bookingDetails'    => $processedArr['modifiedBookingsArr'],
    'users'             => $users,
    'tracks'            => $tracks,
));

// Print text using writeHTMLCell()
 $pdf->writeHTML($content, true, false, false, false, '');
//$pdf->writeHTMLCell(0, 0, '', '', $content, 0, 1, 0, true, '', true);

$content    = $this->Element('pdf/students_calculation',array(
    'studentDetails'    => $processedArr['studentDetails'],
    'users'             => $users,
    'tracks'            => $tracks,
));
$pdf->SetFontSize(10);
// Print text using writeHTMLCell()*/
$pdf->writeHTMLCell(0, 0, '', '', $content, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.

$pdf->Output('Booking Report'.implode(' ',$searchString).'.pdf', 'I');