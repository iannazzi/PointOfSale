<?php


function print_to_network_printer()
{
/*	You could use the LPR Printer class from here:
	
	http://www.phpclasses.org/package/2540-PHP-Abstraction-for-printing-documents.html
	
	Example:
	
	<?php 
	include("PrintSend.php");
	include("PrintSendLPR.php");
	
	$lpr = new PrintSendLPR(); 
	$lpr->setHost("10.0.0.17"); //Put your printer IP here 
	$lpr->setData("C:\\wampp2\\htdocs\\print\\test.txt"); //Path to file, OR string to print. 
	
	$lpr->printJob("someQueue"); //If your printer has a built-in printserver, it might just accept anything as a queue name.*/

}
function imageDoc()
{
	// Create a blank image and add some text
	$im = imagecreatetruecolor(120, 20);
	$text_color = imagecolorallocate($im, 233, 14, 91);
	imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);
	
	// Set the content type header - in this case image/jpeg
	header('Content-Type: image/jpeg');
	
	// Output the image
	imagejpeg($im);
	
	// Free up memory
	imagedestroy($im);
}
function tcpdf_test()
{
	//============================================================+
	// File name   : example_001.php
	// Begin       : 2008-03-04
	// Last Update : 2012-07-25
	//
	// Description : Example 001 for TCPDF class
	//               Default Header and Footer
	//
	// Author: Nicola Asuni
	//
	// (c) Copyright:
	//               Nicola Asuni
	//               Tecnick.com LTD
	//               Manor Coach House, Church Hill
	//               Aldershot, Hants, GU12 4RQ
	//               UK
	//               www.tecnick.com
	//               info@tecnick.com
	//============================================================+
	
	/**
	 * Creates an example PDF TEST document using TCPDF
	 * @package com.tecnick.tcpdf
	 * @abstract TCPDF - Example: Default Header and Footer
	 * @author Nicola Asuni
	 * @since 2008-03-04
	 */
	
	require_once(TCPDF_LANG);
	require_once(TCPDF);
	

	$magin_left = 0;
	$margin_right = 0;
	$margin_top = 0;
	$margin_header = 0;
	$margin_footer = 0;
	
	$title = 'Avery 5075 Sticker';
	$subject = 'PO # 123';
	$keywords = 'purchase order 123';
	$page_orientation = 'P';
	$page_format = 'LETTER';
	$unit = 'in';
	
// create new PDF document
$pdf = new TCPDF($page_orientation, $unit, $page_format, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(getSetting('company_name'));
$pdf->SetAuthor(getUserFullName($_SESSION['pos_user_id']));
$pdf->SetTitle($title);
$pdf->SetSubject($subject );
$pdf->SetKeywords($keywords);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins($magin_left, $margin_top, $margin_right);
$pdf->SetHeaderMargin($margin_header);
$pdf->SetFooterMargin($margin_footer);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('times', '', 10);

// add a page
$pdf->AddPage();

// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);

// set color for background
$pdf->SetFillColor(255, 255, 127);

// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

// set some text for example
$txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

// Multicell test
$pdf->MultiCell(55, 5, '[LEFT] '.$txt, 1, 'L', 1, 0, '', '', true);
$pdf->MultiCell(55, 5, '[RIGHT] '.$txt, 1, 'R', 0, 1, '', '', true);
$pdf->MultiCell(55, 5, '[CENTER] '.$txt, 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(55, 5, '[JUSTIFY] '.$txt."\n", 1, 'J', 1, 2, '' ,'', true);
$pdf->MultiCell(55, 5, '[DEFAULT] '.$txt, 1, '', 0, 1, '', '', true);

$pdf->Ln(4);

// set color for background
$pdf->SetFillColor(220, 255, 220);

// Vertical alignment
$pdf->MultiCell(55, 40, '[VERTICAL ALIGNMENT - TOP] '.$txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
$pdf->MultiCell(55, 40, '[VERTICAL ALIGNMENT - MIDDLE] '.$txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'M');
$pdf->MultiCell(55, 40, '[VERTICAL ALIGNMENT - BOTTOM] '.$txt, 1, 'J', 1, 1, '', '', true, 0, false, true, 40, 'B');

$pdf->Ln(4);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// set color for background
$pdf->SetFillColor(215, 235, 255);

// set some text for example
$txt = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sed imperdiet lectus. Phasellus quis velit velit, non condimentum quam. Sed neque urna, ultrices ac volutpat vel, laoreet vitae augue. Sed vel velit erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Cras eget velit nulla, eu sagittis elit. Nunc ac arcu est, in lobortis tellus. Praesent condimentum rhoncus sodales. In hac habitasse platea dictumst. Proin porta eros pharetra enim tincidunt dignissim nec vel dolor. Cras sapien elit, ornare ac dignissim eu, ultricies ac eros. Maecenas augue magna, ultrices a congue in, mollis eu nulla. Nunc venenatis massa at est eleifend faucibus. Vivamus sed risus lectus, nec interdum nunc.

Fusce et felis vitae diam lobortis sollicitudin. Aenean tincidunt accumsan nisi, id vehicula quam laoreet elementum. Phasellus egestas interdum erat, et viverra ipsum ultricies ac. Praesent sagittis augue at augue volutpat eleifend. Cras nec orci neque. Mauris bibendum posuere blandit. Donec feugiat mollis dui sit amet pellentesque. Sed a enim justo. Donec tincidunt, nisl eget elementum aliquam, odio ipsum ultrices quam, eu porttitor ligula urna at lorem. Donec varius, eros et convallis laoreet, ligula tellus consequat felis, ut ornare metus tellus sodales velit. Duis sed diam ante. Ut rutrum malesuada massa, vitae consectetur ipsum rhoncus sed. Suspendisse potenti. Pellentesque a congue massa.';

// print a blox of text using multicell()
$pdf->MultiCell(80, 5, $txt."\n", 1, 'J', 1, 1, '' ,'', true);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// AUTO-FITTING

// set color for background
$pdf->SetFillColor(255, 235, 235);

// Fit text on cell by reducing font size
$pdf->MultiCell(55, 60, '[FIT CELL] '.$txt."\n", 1, 'J', 1, 1, 125, 145, true, 0, false, true, 60, 'M', true);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// CUSTOM PADDING

// set color for background
$pdf->SetFillColor(255, 255, 215);

// set font
$pdf->SetFont('helvetica', '', 8);

// set cell padding
$pdf->setCellPaddings(2, 4, 6, 8);

$txt = "CUSTOM PADDING:\nLeft=2, Top=4, Right=6, Bottom=8\nLorem ipsum dolor sit amet, consectetur adipiscing elit. In sed imperdiet lectus. Phasellus quis velit velit, non condimentum quam. Sed neque urna, ultrices ac volutpat vel, laoreet vitae augue.\n";

$pdf->MultiCell(55, 5, $txt, 1, 'J', 1, 2, 125, 210, true);

// move pointer to last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_005.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
}
function tcpdf_multicellexample()
{
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2012-07-25
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               Manor Coach House, Church Hill
//               Aldershot, Hants, GU12 4RQ
//               UK
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

	require_once(TCPDF_LANG);
	require_once(TCPDF);


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 057');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 057', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'B', 20);

// add a page
$pdf->AddPage();

$pdf->Write(0, 'Example of alignment options for Cell()', '', 0, 'L', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 11);

// set border width
$pdf->SetLineWidth(0.7);

// set color for cell border
$pdf->SetDrawColor(0,128,255);

$pdf->setCellHeightRatio(3);

$pdf->SetXY(15, 60);

// text on center
$pdf->Cell(30, 0, 'Top-Center', 1, $ln=0, 'C', 0, '', 0, false, 'T', 'C');
$pdf->Cell(30, 0, 'Center-Center', 1, $ln=0, 'C', 0, '', 0, false, 'C', 'C');
$pdf->Cell(30, 0, 'Bottom-Center', 1, $ln=0, 'C', 0, '', 0, false, 'B', 'C');
$pdf->Cell(30, 0, 'Ascent-Center', 1, $ln=0, 'C', 0, '', 0, false, 'A', 'C');
$pdf->Cell(30, 0, 'Baseline-Center', 1, $ln=0, 'C', 0, '', 0, false, 'L', 'C');
$pdf->Cell(30, 0, 'Descent-Center', 1, $ln=0, 'C', 0, '', 0, false, 'D', 'C');


$pdf->SetXY(15, 90);

// text on top
$pdf->Cell(30, 0, 'Top-Top', 1, $ln=0, 'C', 0, '', 0, false, 'T', 'T');
$pdf->Cell(30, 0, 'Center-Top', 1, $ln=0, 'C', 0, '', 0, false, 'C', 'T');
$pdf->Cell(30, 0, 'Bottom-Top', 1, $ln=0, 'C', 0, '', 0, false, 'B', 'T');
$pdf->Cell(30, 0, 'Ascent-Top', 1, $ln=0, 'C', 0, '', 0, false, 'A', 'T');
$pdf->Cell(30, 0, 'Baseline-Top', 1, $ln=0, 'C', 0, '', 0, false, 'L', 'T');
$pdf->Cell(30, 0, 'Descent-Top', 1, $ln=0, 'C', 0, '', 0, false, 'D', 'T');


$pdf->SetXY(15, 120);

// text on bottom
$pdf->Cell(30, 0, 'Top-Bottom', 1, $ln=0, 'C', 0, '', 0, false, 'T', 'B');
$pdf->Cell(30, 0, 'Center-Bottom', 1, $ln=0, 'C', 0, '', 0, false, 'C', 'B');
$pdf->Cell(30, 0, 'Bottom-Bottom', 1, $ln=0, 'C', 0, '', 0, false, 'B', 'B');
$pdf->Cell(30, 0, 'Ascent-Bottom', 1, $ln=0, 'C', 0, '', 0, false, 'A', 'B');
$pdf->Cell(30, 0, 'Baseline-Bottom', 1, $ln=0, 'C', 0, '', 0, false, 'L', 'B');
$pdf->Cell(30, 0, 'Descent-Bottom', 1, $ln=0, 'C', 0, '', 0, false, 'D', 'B');


// draw some reference lines
$linestyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
$pdf->Line(15, 60, 195, 60, $linestyle);
$pdf->Line(15, 90, 195, 90, $linestyle);
$pdf->Line(15, 120, 195, 120, $linestyle);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Print an image to explain cell measures

$pdf->Image('' . POS_URL . '/3rdParty/tcpdf/images/tcpdf_cell.png', 15, 160, 100, 100, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);
$legend = 'LEGEND:

X: cell x top-left origin (top-right for RTL)
Y: cell y top-left origin (top-right for RTL)
CW: cell width
CH: cell height
LW: line width
NRL: normal line position
EXT: external line position
INT: internal line position
ML: margin left
MR: margin right
MT: margin top
MB: margin bottom
PL: padding left
PR: padding right
PT: padding top
PB: padding bottom
TW: text width
FA: font ascent
FB: font baseline
FD: font descent';
$pdf->SetFont('helvetica', '', 10);
$pdf->setCellHeightRatio(1.25);
$pdf->MultiCell(0, 0, $legend, 0, 'L', false, 1, 125, 160, true, 0, false, true, 0, 'T', false);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// CELL BORDERS

// add a page
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 20);

$pdf->Write(0, 'Example of borders for Cell()', '', 0, 'L', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 11);

// set border width
$pdf->SetLineWidth(0.508);

// set color for cell border
$pdf->SetDrawColor(0,128,255);

// set filling color
$pdf->SetFillColor(255,255,128);

// set cell height ratio
$pdf->setCellHeightRatio(3);

$pdf->Cell(30, 0, '1', 1, 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'LTRB', 'LTRB', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'LTR', 'LTR', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'TRB', 'TRB', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'LRB', 'LRB', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'LTB', 'LTB', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'LT', 'LT', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'TR', 'TR', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'RB', 'RB', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'LB', 'LB', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'LR', 'LR', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'TB', 'TB', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'L', 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'T', 'T', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'R', 'R', 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(2);
$pdf->Cell(30, 0, 'B', 'B', 1, 'C', 1, '', 0, false, 'T', 'C');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// ADVANCED SETTINGS FOR CELL BORDERS

// add a page
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 20);

$pdf->Write(0, 'Example of advanced border settings for Cell()', '', 0, 'L', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 11);

// set border width
$pdf->SetLineWidth(1);

// set color for cell border
$pdf->SetDrawColor(0,128,255);

// set filling color
$pdf->SetFillColor(255,255,128);

$border = array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0)));
$pdf->Cell(30, 0, 'LTRB', $border, 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(5);

$border = array(
'L' => array('width' => 2, 'cap' => 'square', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0)),
'R' => array('width' => 2, 'cap' => 'square', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 255)),
'T' => array('width' => 2, 'cap' => 'square', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 255, 0)),
'B' => array('width' => 2, 'cap' => 'square', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 255)));
$pdf->Cell(30, 0, 'LTRB', $border, 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(5);

$border = array('mode' => 'ext', 'LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0)));
$pdf->Cell(30, 0, 'LTRB EXT', $border, 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(5);

$border = array('mode' => 'int', 'LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0)));
$pdf->Cell(30, 0, 'LTRB INT', $border, 1, 'C', 1, '', 0, false, 'T', 'C');
$pdf->Ln(5);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_057.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
}
function tcpdf_write_htmlexample()
{
//============================================================+
// File name   : example_006.php
// Begin       : 2008-03-04
// Last Update : 2010-11-20
//
// Description : Example 006 for TCPDF class
//               WriteHTML and RTL support
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com s.r.l.
//               Via Della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: WriteHTML and RTL support
 * @author Nicola Asuni
 * @since 2008-03-04
 */

	require_once(TCPDF_LANG);
	require_once(TCPDF);

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 006');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// create some HTML content
$html = '<h1>HTML Example</h1>
Some special characters: &lt; € &euro; &#8364; &amp; è &egrave; &copy; &gt; \\slash \\\\double-slash \\\\\\triple-slash
<h2>List</h2>
List example:
<ol>
    <li><img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.png" alt="test alt attribute" width="30" height="30" border="0" /> test image</li>
    <li><b>bold text</b></li>
    <li><i>italic text</i></li>
    <li><u>underlined text</u></li>
    <li><b>b<i>bi<u>biu</u>bi</i>b</b></li>
    <li><a href="http://www.tecnick.com" dir="ltr">link to http://www.tecnick.com</a></li>
    <li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.<br />Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</li>
    <li>SUBLIST
        <ol>
            <li>row one
                <ul>
                    <li>sublist</li>
                </ul>
            </li>
            <li>row two</li>
        </ol>
    </li>
    <li><b>T</b>E<i>S</i><u>T</u> <del>line through</del></li>
    <li><font size="+3">font + 3</font></li>
    <li><small>small text</small> normal <small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal</li>
</ol>
<dl>
    <dt>Coffee</dt>
    <dd>Black hot drink</dd>
    <dt>Milk</dt>
    <dd>White cold drink</dd>
</dl>
<div style="text-align:center">IMAGES<br />
<img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.png" alt="test alt attribute" width="100" height="100" border="0" /><img src="' . POS_URL . '/3rdParty/tcpdf/images/tiger.ai" alt="test alt attribute" width="100" height="100" border="0" /><img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.jpg" alt="test alt attribute" width="100" height="100" border="0" />
</div>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


// output some RTL HTML content
$html = '<div style="text-align:center">The words &#8220;<span dir="rtl">&#1502;&#1494;&#1500; [mazel] &#1496;&#1493;&#1489; [tov]</span>&#8221; mean &#8220;Congratulations!&#8221;</div>';
$pdf->writeHTML($html, true, false, true, false, '');

// test some inline CSS
$html = '<p>This is just an example of html code to demonstrate some supported CSS inline styles.
<span style="font-weight: bold;">bold text</span>
<span style="text-decoration: line-through;">line-trough</span>
<span style="text-decoration: underline line-through;">underline and line-trough</span>
<span style="color: rgb(0, 128, 64);">color</span>
<span style="background-color: rgb(255, 0, 0); color: rgb(255, 255, 255);">background color</span>
<span style="font-weight: bold;">bold</span>
<span style="font-size: xx-small;">xx-small</span>
<span style="font-size: x-small;">x-small</span>
<span style="font-size: small;">small</span>
<span style="font-size: medium;">medium</span>
<span style="font-size: large;">large</span>
<span style="font-size: x-large;">x-large</span>
<span style="font-size: xx-large;">xx-large</span>
</p>';

$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print a table

// add a page
$pdf->AddPage();

// create some HTML content
$subtable = '<table border="1" cellspacing="6" cellpadding="4"><tr><td>a</td><td>b</td></tr><tr><td>c</td><td>d</td></tr></table>';

$html = '<h2>HTML TABLE:</h2>
<table border="1" cellspacing="3" cellpadding="4">
    <tr>
        <th>#</th>
        <th align="right">RIGHT align</th>
        <th align="left">LEFT align</th>
        <th>4A</th>
    </tr>
    <tr>
        <td>1</td>
        <td bgcolor="#cccccc" align="center" colspan="2">A1 ex<i>amp</i>le <a href="http://www.tcpdf.org">link</a> column span. One two tree four five six seven eight nine ten.<br />line after br<br /><small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal  bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla<ol><li>first<ol><li>sublist</li><li>sublist</li></ol></li><li>second</li></ol><small color="#FF0000" bgcolor="#FFFF00">small small small small small small small small small small small small small small small small small small small small</small></td>
        <td>4B</td>
    </tr>
    <tr>
        <td>'.$subtable.'</td>
        <td bgcolor="#0000FF" color="yellow" align="center">A2 € &euro; &#8364; &amp; è &egrave;<br/>A2 € &euro; &#8364; &amp; è &egrave;</td>
        <td bgcolor="#FFFF00" align="left"><font color="#FF0000">Red</font> Yellow BG</td>
        <td>4C</td>
    </tr>
    <tr>
        <td>1A</td>
        <td rowspan="2" colspan="2" bgcolor="#FFFFCC">2AA<br />2AB<br />2AC</td>
        <td bgcolor="#FF0000">4D</td>
    </tr>
    <tr>
        <td>1B</td>
        <td>4E</td>
    </tr>
    <tr>
        <td>1C</td>
        <td>2C</td>
        <td>3C</td>
        <td>4F</td>
    </tr>
</table>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Print some HTML Cells

$html = '<span color="red">red</span> <span color="green">green</span> <span color="blue">blue</span><br /><span color="red">red</span> <span color="green">green</span> <span color="blue">blue</span>';

$pdf->SetFillColor(255,255,0);

$pdf->writeHTMLCell(0, 0, '', '', $html, 'LRTB', 1, 0, true, 'L', true);
$pdf->writeHTMLCell(0, 0, '', '', $html, 'LRTB', 1, 1, true, 'C', true);
$pdf->writeHTMLCell(0, 0, '', '', $html, 'LRTB', 1, 0, true, 'R', true);

// reset pointer to the last page
$pdf->lastPage();

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print a table

// add a page
$pdf->AddPage();

// create some HTML content
$html = '<h1>Image alignments on HTML table</h1>
<table cellpadding="1" cellspacing="1" border="1" style="text-align:center;">
<tr><td><img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" /></td></tr>
<tr style="text-align:left;"><td><img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="top" /></td></tr>
<tr style="text-align:center;"><td><img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="middle" /></td></tr>
<tr style="text-align:right;"><td><img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="bottom" /></td></tr>
<tr><td style="text-align:left;"><img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="top" /></td></tr>
<tr><td style="text-align:center;"><img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="middle" /></td></tr>
<tr><td style="text-align:right;"><img src="' . POS_URL . '/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="bottom" /></td></tr>
</table>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print all HTML colors

// add a page
$pdf->AddPage();

require(POS_PATH .'/3rdParty/tcpdf/htmlcolors.php');

$textcolors = '<h1>HTML Text Colors</h1>';
$bgcolors = '<hr /><h1>HTML Background Colors</h1>';

foreach($webcolor as $k => $v) {
    $textcolors .= '<span color="#'.$v.'">'.$v.'</span> ';
    $bgcolors .= '<span bgcolor="#'.$v.'" color="#333333">'.$v.'</span> ';
}

// output the HTML content
$pdf->writeHTML($textcolors, true, false, true, false, '');
$pdf->writeHTML($bgcolors, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Test word-wrap

// create some HTML content
$html = '<hr />
<h1>Various tests</h1>
<a href="#2">link to page 2</a><br />
<font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font> <font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font> <font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font> <font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font> <font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Test fonts nesting
$html1 = 'Default <font face="courier">Courier <font face="helvetica">Helvetica <font face="times">Times <font face="dejavusans">dejavusans </font>Times </font>Helvetica </font>Courier </font>Default';
$html2 = '<small>small text</small> normal <small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal';
$html3 = '<font size="10" color="#ff7f50">The</font> <font size="10" color="#6495ed">quick</font> <font size="14" color="#dc143c">brown</font> <font size="18" color="#008000">fox</font> <font size="22"><a href="http://www.tcpdf.org">jumps</a></font> <font size="22" color="#a0522d">over</font> <font size="18" color="#da70d6">the</font> <font size="14" color="#9400d3">lazy</font> <font size="10" color="#4169el">dog</font>.';

$html = $html1.'<br />'.$html2.'<br />'.$html3.'<br />'.$html3.'<br />'.$html2;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// test pre tag

// add a page
$pdf->AddPage();

$html = <<<EOF
<div style="background-color:#880000;color:white;">
Hello World!<br />
Hello
</div>
<pre style="background-color:#336699;color:white;">
int main() {
    printf("HelloWorld");
    return 0;
}
</pre>
<tt>Monospace font</tt>, normal font, <tt>monospace font</tt>, normal font.
<br />
<div style="background-color:#880000;color:white;">DIV LEVEL 1<div style="background-color:#008800;color:white;">DIV LEVEL 2</div>DIV LEVEL 1</div>
<br />
<span style="background-color:#880000;color:white;">SPAN LEVEL 1 <span style="background-color:#008800;color:white;">SPAN LEVEL 2</span> SPAN LEVEL 1</span>
EOF;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// test custom bullet points for list

// add a page
$pdf->AddPage();

$html = '
<h1>Test custom bullet image for list items</h1>
<ul >
    <li>test custom bullet image</li>
    <li>test custom bullet image</li>
    <li>test custom bullet image</li>
    <li>test custom bullet image</li>
<ul>
';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_006.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
}
function tcpdf_html_table_example()
{

//============================================================+
// File name   : example_048.php
// Begin       : 2009-03-20
// Last Update : 2010-08-08
//
// Description : Example 048 for TCPDF class
//               HTML tables and table headers
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com s.r.l.
//               Via Della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: HTML tables and table headers
 * @author Nicola Asuni
 * @since 2009-03-20
 */

require_once(TCPDF_LANG);
	require_once(TCPDF);

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 048');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'B', 20);

// add a page
$pdf->AddPage();

$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 8);

// -----------------------------------------------------------------------------

$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3">COL 1 - ROW 1<br />COLSPAN 3</td>
        <td>COL 2 - ROW 1</td>
        <td>COL 3 - ROW 1</td>
    </tr>
    <tr>
        <td rowspan="2">COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text line</td>
        <td>COL 3 - ROW 2</td>
    </tr>
    <tr>
       <td>COL 3 - ROW 3</td>
    </tr>
  
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3">COL 1 - ROW 1<br />COLSPAN 3<br />text line<br />text line<br />text line<br />text line<br />text line<br />text line</td>
        <td>COL 2 - ROW 1</td>
        <td>COL 3 - ROW 1</td>
    </tr>
    <tr>
        <td rowspan="2">COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text line</td>
         <td>COL 3 - ROW 2</td>
    </tr>
    <tr>
       <td>COL 3 - ROW 3</td>
    </tr>
  
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3">COL 1 - ROW 1<br />COLSPAN 3<br />text line<br />text line<br />text line<br />text line<br />text line<br />text line</td>
        <td>COL 2 - ROW 1</td>
        <td>COL 3 - ROW 1</td>
    </tr>
    <tr>
        <td rowspan="2">COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text line</td>
         <td>COL 3 - ROW 2<br />text line<br />text line</td>
    </tr>
    <tr>
       <td>COL 3 - ROW 3</td>
    </tr>
  
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

$tbl = <<<EOD
<table border="1">
<tr>
<th rowspan="3">Left column</th>
<th colspan="5">Heading Column Span 5</th>
<th colspan="9">Heading Column Span 9</th>
</tr>
<tr>
<th rowspan="2">Rowspan 2<br />This is some text that fills the table cell.</th>
<th colspan="2">span 2</th>
<th colspan="2">span 2</th>
<th rowspan="2">2 rows</th>
<th colspan="8">Colspan 8</th>
</tr>
<tr>
<th>1a</th>
<th>2a</th>
<th>1b</th>
<th>2b</th>
<th>1</th>
<th>2</th>
<th>3</th>
<th>4</th>
<th>5</th>
<th>6</th>
<th>7</th>
<th>8</th>
</tr>
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

// Table with rowspans and THEAD
$tbl = <<<EOD
<table border="1" cellpadding="2" cellspacing="2">
<thead>
 <tr style="background-color:#FFFF00;color:#0000FF;">
  <td width="30" align="center"><b>A</b></td>
  <td width="140" align="center"><b>XXXX</b></td>
  <td width="140" align="center"><b>XXXX</b></td>
  <td width="80" align="center"> <b>XXXX</b></td>
  <td width="80" align="center"><b>XXXX</b></td>
  <td width="45" align="center"><b>XXXX</b></td>
 </tr>
 <tr style="background-color:#FF0000;color:#FFFF00;">
  <td width="30" align="center"><b>B</b></td>
  <td width="140" align="center"><b>XXXX</b></td>
  <td width="140" align="center"><b>XXXX</b></td>
  <td width="80" align="center"> <b>XXXX</b></td>
  <td width="80" align="center"><b>XXXX</b></td>
  <td width="45" align="center"><b>XXXX</b></td>
 </tr>
</thead>
 <tr>
  <td width="30" align="center">1.</td>
  <td width="140" rowspan="6">XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX</td>
  <td width="140">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td width="80">XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr>
  <td width="30" align="center" rowspan="3">2.</td>
  <td width="140" rowspan="3">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr>
  <td width="80">XXXX<br />XXXX<br />XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr>
  <td width="80" rowspan="2" >RRRRRR<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr>
  <td width="30" align="center">3.</td>
  <td width="140">XXXX1<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr>
  <td width="30" align="center">4.</td>
  <td width="140">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

// NON-BREAKING TABLE (nobr="true")

$tbl = <<<EOD
<table border="1" cellpadding="2" cellspacing="2" nobr="true">
 <tr>
  <th colspan="3" align="center">NON-BREAKING TABLE</th>
 </tr>
 <tr>
  <td>1-1</td>
  <td>1-2</td>
  <td>1-3</td>
 </tr>
 <tr>
  <td>2-1</td>
  <td>3-2</td>
  <td>3-3</td>
 </tr>
 <tr>
  <td>3-1</td>
  <td>3-2</td>
  <td>3-3</td>
 </tr>
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

// NON-BREAKING ROWS (nobr="true")

$tbl = <<<EOD
<table border="1" cellpadding="2" cellspacing="2" align="center">
 <tr nobr="true">
  <th colspan="3">NON-BREAKING ROWS</th>
 </tr>
 <tr nobr="true">
  <td>ROW 1<br />COLUMN 1</td>
  <td>ROW 1<br />COLUMN 2</td>
  <td>ROW 1<br />COLUMN 3</td>
 </tr>
 <tr nobr="true">
  <td>ROW 2<br />COLUMN 1</td>
  <td>ROW 2<br />COLUMN 2</td>
  <td>ROW 2<br />COLUMN 3</td>
 </tr>
 <tr nobr="true">
  <td>ROW 3<br />COLUMN 1</td>
  <td>ROW 3<br />COLUMN 2</td>
  <td>ROW 3<br />COLUMN 3</td>
 </tr>
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_048.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
}

function printProductLabelsAvery5167($product_sub_ids, $quantities, $row_offset, $column_offset, $filename)
{
	/* This was the original Label Printer and it was difficult to read the price and the codes did not scan well. */
	
	require_once(TCPDF_LANG);
	require_once(TCPDF);
	
	$pdf_file_name = $filename;
	
	$subid = array();
	$poc_title = array();
	$color_price = array();

	for($i=0;$i<sizeof($product_sub_ids);$i++)
	{
		$pos_product_sub_id = $product_sub_ids[$i];
		$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);
		for($qty=0;$qty<$quantities[$i];$qty++)
		{
			$subid[] = getProductSubIDName($pos_product_sub_id);
			$poc_title[] = substr(getProductTitle($pos_product_id),0,48);
			$color_price[] = substr(getProductSubIdColorDescription($pos_product_sub_id),0,38) . '     $' . number_format(getProductRetail($pos_product_id),2);
		}
	}

	$margin_left = 0;
	$margin_right = 0;
	$margin_top = 0;
	$margin_bottom = 0;
	$cell_width = 1.75;
	$cell_height = 0.5;
	$cell_spacing = 0.3;
	$columns = 4;
	$rows = 20;
	$line_spacing_adjust = 0.015;
	$barcode_spacing_adjust = 0.1;
	$barcode_height_adjust = 0.05;
	
	$title = 'Avery 5167 template';
	$subject = 'PO # 123';
	$keywords = 'purchase order 123';
	$page_orientation = 'P';
	$page_format = 'LETTER';
	$unit = 'in';
	
	// create new PDF document
	$pdf = new TCPDF($page_orientation, $unit, $page_format, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(getSetting('company_name'));
	$pdf->SetAuthor(getUserFullName($_SESSION['pos_user_id']));
	$pdf->SetTitle($title);
	$pdf->SetSubject($subject );
	$pdf->SetKeywords($keywords);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	$pdf->SetMargins($margin_left, $margin_top, $margin_right);
	$pdf->SetAutoPageBreak(TRUE, $margin_bottom);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->setLanguageArray($l);
	$preferences = array('PrintScaling' => 'None');
	$pdf->setViewerPreferences($preferences);
	$pdf->SetFont('helvetica', 'R', 5);
	
	//barcode: 128a?
	// define barcode style
	$barcode_style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => false,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => '0',
    'vpadding' => '0',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => false,
    'font' => 'helvetica',
    'fontsize' => 4,
    'stretchtext' => 0
);
	

	// set border width
	$pdf->SetLineWidth(0.01);
	$pdf->SetDrawColor(0,0,0);
	//$pdf->setCellHeightRatio(3);
	$counter = 0;
	
	//calculating the pages.... how many labels are to be printed on the sheet...
	//how many labels are going on the first sheet?
	$first_page_number_of_spots = ($rows)*($columns-($column_offset-1)) -($row_offset-1);
	$number_of_labels = sizeof($subid);
	if($number_of_labels <= $first_page_number_of_spots)
	{
		$pages = 1;
	}
	else
	{
		$lables_on_first_page = $first_page_number_of_spots;
		$labels_remaining = $number_of_labels -$lables_on_first_page;
		$number_of_spots_per_page = $rows*$columns;
		$pages = ceil($labels_remaining/($number_of_spots_per_page)) + 1;
	}
	for($page=0;$page<$pages;$page++)
	{
		$pdf->AddPage();
		for($col=$column_offset-1;$col<$columns;$col++)
		{
			for($row=$row_offset-1;$row<$rows;$row++)
			{
				if($counter< sizeof($subid))
				{
					//barcodes must be cap
					$line1 = strtoupper($subid[$counter]);
					$line2 = $poc_title[$counter];
					$line3 = $color_price[$counter];
				}
				else
				{
					$line1 = '';
					$line2 = '';
					$line3 = '';
				}
				$counter++;
				$x_spot = $cell_spacing + $col*$cell_width + $col*$cell_spacing;
				$y_spot = $cell_height + $row*$cell_height;
				$coords = 'X:'.$x_spot . ' Y:' .$y_spot;
				$border = 0;
				$border2 = 0;
				//this is the cell that will allow allignment to sticker checking
				$pdf->SetXY($x_spot, $y_spot);
				$pdf->Cell($cell_width, $cell_height,  '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
				
				// CODE 128 A
				$pdf->SetXY($x_spot+$barcode_spacing_adjust, $y_spot);
				//cell to check the barcode placement
				$pdf->Cell($cell_width-2*$barcode_spacing_adjust, $cell_height/2, '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
				$pdf->write1DBarcode($line1, 'C128A', $x_spot+$barcode_spacing_adjust, $y_spot+$barcode_height_adjust, $cell_width-2*$barcode_spacing_adjust, $cell_height/2 - $barcode_height_adjust, 0.4, $barcode_style, 'N');
				
				//the remaining 3 lines have to fit in 1/2 the sticker size
				//$y_offset = $cell_height/2;
				$pdf->SetXY($x_spot, $y_spot - 0*$line_spacing_adjust + 3/6*$cell_height);
				$pdf->Cell($cell_width, $cell_height/6,  $line1, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
				$pdf->SetXY($x_spot, $y_spot - 1*$line_spacing_adjust + 4/6*$cell_height);
				$pdf->Cell($cell_width, $cell_height/6,  $line2, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
				$pdf->SetXY($x_spot, $y_spot -2*$line_spacing_adjust + 5/6*$cell_height);
				$pdf->Cell($cell_width, $cell_height/6,  $line3, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
				
				
				//$pdf->writeHTMLCell($cell_width, $cell_height, $x_spot, $y_spot, $text_for_label, 1, 1, false, true, '', false);
				// no work $pdf->MultiCell($cell_width, $cell_height, $text_for_label, 1, 'J', false, '','',true, 0, false, true, $cell_height, 'T', false);
			}
			$row_offset = 1;
		}
		$column_offset = 1;
	}
//Close and output PDF document
$pdf->Output($pdf_file_name, 'D');

//============================================================+
// END OF FILE
//============================================================+
}
function printProductLabelsAvery5167V2($product_sub_ids, $quantities, $row_offset, $column_offset, $filename)
{

	require_once(TCPDF_LANG);
	require_once(TCPDF);
	
	$pdf_file_name = $filename;
	
	$subid = array();
	$poc_title = array();
	$color_price = array();

	for($i=0;$i<sizeof($product_sub_ids);$i++)
	{
		$pos_product_sub_id = $product_sub_ids[$i];
		$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);
		for($qty=0;$qty<$quantities[$i];$qty++)
		{
			$subid[] = $pos_product_sub_id;//getProductSubIDName($pos_product_sub_id);
			$poc_title[] = substr(getProductTitle($pos_product_id),0,48);
			$color_price[] = substr(getProductSubIdColorDescription($pos_product_sub_id),0,38) . '     $' . number_format(getProductRetail($pos_product_id),2);
		}
	}

	$margin_left = 0;
	$margin_right = 0;
	$margin_top = 0;
	$margin_bottom = 0;

	$columns = 4;
	$rows = 20;
	
	$title = 'Avery 5167 template';
	$subject = 'PO # 123';
	$keywords = 'purchase order 123';
	$page_orientation = 'P';
	$page_format = 'LETTER';
	$unit = 'in';
	
	// create new PDF document
	$pdf = new TCPDF($page_orientation, $unit, $page_format, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(getSetting('company_name'));
	$pdf->SetAuthor(getUserFullName($_SESSION['pos_user_id']));
	$pdf->SetTitle($title);
	$pdf->SetSubject($subject );
	$pdf->SetKeywords($keywords);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	$pdf->SetMargins($margin_left, $margin_top, $margin_right);
	$pdf->SetAutoPageBreak(TRUE, $margin_bottom);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->setLanguageArray($l);
	$preferences = array('PrintScaling' => 'None');
	$pdf->setViewerPreferences($preferences);
	$pdf->SetFont('helvetica', 'R', 5);
	
	//$pdf->setCellHeightRatio(3);
	$counter = 0;
	
	//calculating the pages.... how many labels are to be printed on the sheet...
	//how many labels are going on the first sheet?
	$first_page_number_of_spots = ($rows)*($columns-($column_offset-1)) -($row_offset-1);
	$number_of_labels = sizeof($subid);
	if($number_of_labels <= $first_page_number_of_spots)
	{
		$pages = 1;
	}
	else
	{
		$lables_on_first_page = $first_page_number_of_spots;
		$labels_remaining = $number_of_labels -$lables_on_first_page;
		$number_of_spots_per_page = $rows*$columns;
		$pages = ceil($labels_remaining/($number_of_spots_per_page)) + 1;
	}
	for($page=0;$page<$pages;$page++)
	{
		$pdf->AddPage();
		for($col=$column_offset-1;$col<$columns;$col++)
		{
			for($row=$row_offset-1;$row<$rows;$row++)
			{
				if($counter< sizeof($subid))
				{
					$pdf = addProductLabelToPdfAvery5167($pdf, $subid[$counter], $row, $col);
				}
				$counter++;
			}
			$row_offset = 1;
		}
		$column_offset = 1;
	}
//Close and output PDF document
$pdf->Output($pdf_file_name, 'D');

//============================================================+
// END OF FILE
//============================================================+
}
function addProductLabelToPdfAvery5167($pdf, $barcode, $row, $col)
{
	
	//barcode: 128a?
	// define barcode style
	$barcode_style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => false,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => '0',
    'vpadding' => '0',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => false,
    'font' => 'helvetica',
    'fontsize' => 4,
    'stretchtext' => 0
);
	
				
	$cell_width = 1.75;
	$cell_height = 0.5;
	$cell_spacing = 0.3;
	$line_spacing_adjust = 0.015;
	$barcode_spacing_adjust = 0.1;
	$barcode_height_adjust = 0.05;		
				
	// set border width
	$pdf->SetLineWidth(0.01);
	$pdf->SetDrawColor(0,0,0);			
				
		
	//barcodes must be cap
	$barcode = strtoupper($barcode);
	if(strpos($barcode, 'P'))
	{
		//product is sale price....
		$sale = true;
		list($pos_product_sub_id, $price_level) = explode('P', $barcode);
		$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);
		$sale_data = getSQL("SELECT title, price, as_is, clearance FROM pos_product_sub_sale_price WHERE pos_product_sub_id = '$pos_product_sub_id' AND price_level = '$price_level'");
		$price =  $sale_data[0]['price'];
	}
	else
	{
		$sale = false;
		$pos_product_sub_id = $barcode;
		$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);
		$price =  getProductRetail($pos_product_id);
	}
	
	
	
	$whole = floor($price); 
	$cents =  round(($price - $whole)  ,2) *100; 
	$cents = str_pad($cents, 2, "0", STR_PAD_RIGHT);
	
	
	//regular barcode is
	//barcode sn
	//brand name title
	//options price
	
	//sale label is
	//barcode sn options
	//brand name title
	//originally price
	
	//clearance is
	//barcode sn options
	// clearance final sale
	//original price
	
	
	switch (strlen($whole)) 
	{
		case 0:
			$max_chars = 45;
			break;
		case 1:
			$max_chars = 35;
			break;
		case 2:
		   $max_chars = 29;
			break;
		case 3:
		   $max_chars = 26;
			break;
		case 4:
		   $max_chars = 23;
			break;
		default:
			$max_chars = 20;
	}
	
	$title = getProductTitle($pos_product_id);
	$brand_name = getProductBrandName($pos_product_id);
	$original_price =  getProductRetail($pos_product_id);
	//line 1 $barcode_plus_style_number prints
	if($sale)
	{
		$style_number = getProductStyleNumber($pos_product_id);
		$barcode_plus_style_number = $barcode . ' SN:' .$style_number;
		$barcode_plus_style_number .= ' ' . $brand_name;
		if(strlen($barcode_plus_style_number)>$max_chars)
		{
		
		}
	}
	else
	{
		$style_number = getProductStyleNumber($pos_product_id);
		$barcode_plus_style_number = $barcode . ' SN:' .$style_number;
		if(strlen($barcode_plus_style_number)>$max_chars)
		{
		
		}
	}
	//line 2 $poc_title prints
	
	if($sale)
	{

		if($sale_data[0]['clearance'] == '1')
		{
			$poc_title = 'CLEARANCE FINAL SALE WAS $' . number_format($original_price,0);
		}
		else
		{
			$poc_title = 'SALE ORIGINALLY $' . number_format($original_price,0);
		}
		
		
		/*if(strlen($poc_title) > $max_chars - 8)
		{
			$poc_title = getProductBrandCode($pos_product_id). ' ' . $title;
			if(strlen($poc_title) > $max_chars - 8)
			{
			
				$vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");
				$poc_title = getProductBrandCode($pos_product_id) . ' ' . str_replace($vowels, "", $title);
				$poc_title = substr($poc_title,0,$max_chars-8);
			
			}
		}
		*/
	}
	else
	{
		
		$poc_title = $brand_name . ' ' . $title;
		if(strlen($poc_title) > $max_chars)
		{
			$poc_title = getProductBrandCode($pos_product_id). ' ' . $title;
			if(strlen($poc_title) > $max_chars)
			{
			
				$vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");
				$poc_title = getProductBrandCode($pos_product_id) . ' ' . str_replace($vowels, "", $title);
				$poc_title = substr($poc_title,0,$max_chars);
			
			}
		}
	}
	
	
	
	//line 3 $options prints
	if($sale)
	{
		$options = getProductSubIdOptionsList($pos_product_sub_id);
		if(strlen($options)>$max_chars)
		{
			$options = getProductSubIdOptionsListNoDescription($pos_product_sub_id);
			if(strlen($options)>$max_chars)
			{
				$options = getProductSubIdOptionsCodeListNoDescription($pos_product_sub_id);
			}
		
		}
		$options =substr($options,0,$max_chars);
	}
	else
	{
		$options = getProductSubIdOptionsList($pos_product_sub_id);
		if(strlen($options)>$max_chars)
		{
			$options = getProductSubIdOptionsListNoDescription($pos_product_sub_id);
			if(strlen($options)>$max_chars)
			{
				$options = getProductSubIdOptionsCodeListNoDescription($pos_product_sub_id);
			}
		
		}
		$options =substr($options,0,$max_chars);
	}
	
	

		
	$x_spot = $cell_spacing + $col*$cell_width + $col*$cell_spacing;
	$y_spot = $cell_height + $row*$cell_height;
	$coords = 'X:'.$x_spot . ' Y:' .$y_spot;
	$border = 0;
	$border2 = 0;
	//this is the cell that will allow allignment to sticker checking
	$pdf->SetXY($x_spot, $y_spot);
	$pdf->Cell($cell_width, $cell_height,  '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
		
	// CODE 128 A
	$pdf->SetFont('helvetica', 'R', 5);
	$pdf->SetXY($x_spot+$barcode_spacing_adjust, $y_spot);
	//cell to check the barcode placement
	$pdf->Cell($cell_width-2*$barcode_spacing_adjust, $cell_height/2, '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->write1DBarcode($barcode, 'C128A', $x_spot+$barcode_spacing_adjust, $y_spot+$barcode_height_adjust, $cell_width-2*$barcode_spacing_adjust, $cell_height/2 - $barcode_height_adjust, 0.4, $barcode_style, 'N');

	
	if($sale)
	{
		//the remaining 3 lines have to fit in 1/2 the sticker size
		//$y_offset = $cell_height/2;
		$pdf->SetXY($x_spot + 0.05, $y_spot - 0*$line_spacing_adjust + 3/6*$cell_height);
		$pdf->Cell($cell_width, $cell_height/6,  $barcode_plus_style_number, $border2, 0, 'L', 0, '', 0, false, 'T', 'C');
		$pdf->SetXY($x_spot+ 0.05, $y_spot - 1*$line_spacing_adjust + 4/6*$cell_height);
		$pdf->Cell($cell_width-0.75, $cell_height/6,  $options, 0, 0, 'L', 0, '', 0, false, 'T', 'C');
		
		$pdf->SetTextColor(255,0,0);
		$pdf->SetXY($x_spot+ 0.05, $y_spot -2*$line_spacing_adjust + 5/6*$cell_height);
		$pdf->Cell($cell_width, $cell_height/6,  $poc_title, $border2, 0, 'L', 0, '', 0, false, 'T', 'C');
		
		//Finally the price is 3 lines high and 1/3 the sticker width...

		$pdf->SetFont('helvetica', 'B', 14);
		$pdf->SetTextColor(255,0,0);
		$pdf->SetXY($x_spot, $y_spot+$cell_height/2);
		$pdf->Cell($cell_width -0.15, $cell_height/2,  '$' . $whole, $border2, 0, 'R', 0, '', 0, false, 'T', 'C');	
		$pdf->SetFont('helvetica', 'B', 5);
		$pdf->SetXY($x_spot, $y_spot+$cell_height/2+0.05);
		$pdf->Cell($cell_width -0.05, $cell_height/2,  '.' .$cents, $border2, 0, 'R', 0, '', 0, false, 'T', 'C');	
		$pdf->SetTextColor(0,0,0);	
		
		
		$pdf->SetTextColor(0,0,0);

	}
	else
	{
		//the remaining 3 lines have to fit in 1/2 the sticker size
		//$y_offset = $cell_height/2;
		$pdf->SetXY($x_spot + 0.05, $y_spot - 0*$line_spacing_adjust + 3/6*$cell_height);
		$pdf->Cell($cell_width, $cell_height/6,  $barcode_plus_style_number, $border2, 0, 'L', 0, '', 0, false, 'T', 'C');
		$pdf->SetXY($x_spot+ 0.05, $y_spot - 1*$line_spacing_adjust + 4/6*$cell_height);
		$pdf->Cell($cell_width-0.75, $cell_height/6,  $poc_title, 0, 0, 'L', 0, '', 0, false, 'T', 'C');
		$pdf->SetXY($x_spot+ 0.05, $y_spot -2*$line_spacing_adjust + 5/6*$cell_height);
		$pdf->Cell($cell_width, $cell_height/6,  $options, $border2, 0, 'L', 0, '', 0, false, 'T', 'C');
		
		//Finally the price is 3 lines high and 1/3 the sticker width...

		$pdf->SetFont('helvetica', 'B', 14);
		$pdf->SetTextColor(0,0,0);	
		$pdf->SetXY($x_spot, $y_spot+$cell_height/2);
		$pdf->Cell($cell_width -0.15, $cell_height/2,  '$' . $whole, $border2, 0, 'R', 0, '', 0, false, 'T', 'C');	
		$pdf->SetFont('helvetica', 'B', 5);
		$pdf->SetXY($x_spot, $y_spot+$cell_height/2+0.05);
		$pdf->Cell($cell_width -0.05, $cell_height/2,  '.' .$cents, $border2, 0, 'R', 0, '', 0, false, 'T', 'C');	
		$pdf->SetTextColor(0,0,0);	
	}

	

		return $pdf;
			
}

function printCardNumbersAvery5167($card_numbers, $row_offset, $column_offset, $filename)
{

	require_once(TCPDF_LANG);
	require_once(TCPDF);
	
	$pdf_file_name = $filename;
	
	$id = array();
	$titles = array();
	$other_info = array();

	for($i=0;$i<sizeof($card_numbers);$i++)
	{

			
			$id[] = $card_numbers[$i];
			$titles[] = '';
			$other_info[] = '';
	}

	$margin_left = 0;
	$margin_right = 0;
	$margin_top = 0;
	$margin_bottom = 0;
	$cell_width = 1.75;
	$cell_height = 0.5;
	$cell_spacing = 0.3;
	$columns = 4;
	$rows = 20;
	$line_spacing_adjust = 0.015;
	$barcode_spacing_adjust = 0.1;
	$barcode_height_adjust = 0.05;
	
	$title = 'Avery 5167 template';
	$subject = 'location';
	$keywords = 'purchase order 123';
	$page_orientation = 'P';
	$page_format = 'LETTER';
	$unit = 'in';
	
	// create new PDF document
	$pdf = new TCPDF($page_orientation, $unit, $page_format, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(getSetting('company_name'));
	$pdf->SetAuthor(getUserFullName($_SESSION['pos_user_id']));
	$pdf->SetTitle($title);
	$pdf->SetSubject($subject );
	$pdf->SetKeywords($keywords);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	$pdf->SetMargins($margin_left, $margin_top, $margin_right);
	$pdf->SetAutoPageBreak(TRUE, $margin_bottom);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->setLanguageArray($l);
	$preferences = array('PrintScaling' => 'None');
	$pdf->setViewerPreferences($preferences);
	$pdf->SetFont('helvetica', 'R', 5);
	
	//barcode: 128a?
	// define barcode style
	$barcode_style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => false,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => '0',
    'vpadding' => '0',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => false,
    'font' => 'helvetica',
    'fontsize' => 4,
    'stretchtext' => 0
);
	

	// set border width
	$pdf->SetLineWidth(0.01);
	$pdf->SetDrawColor(0,0,0);
	//$pdf->setCellHeightRatio(3);
	$counter = 0;
	
	//calculating the pages.... how many labels are to be printed on the sheet...
	//how many labels are going on the first sheet?
	$first_page_number_of_spots = ($rows)*($columns-($column_offset-1)) -($row_offset-1);
	$number_of_labels = sizeof($id);
	if($number_of_labels <= $first_page_number_of_spots)
	{
		$pages = 1;
	}
	else
	{
		$lables_on_first_page = $first_page_number_of_spots;
		$labels_remaining = $number_of_labels -$lables_on_first_page;
		$number_of_spots_per_page = $rows*$columns;
		$pages = ceil($labels_remaining/($number_of_spots_per_page)) + 1;
	}
	for($page=0;$page<$pages;$page++)
	{
		$pdf->AddPage();
		for($col=$column_offset-1;$col<$columns;$col++)
		{
			for($row=$row_offset-1;$row<$rows;$row++)
			{
				if($counter< sizeof($id))
				{
					//barcodes must be cap
					$line1 = strtoupper($id[$counter]);
					$line2 = $titles[$counter];
					$line3 = $other_info[$counter];
				}
				else
				{
					$line1 = '';
					$line2 = '';
					$line3 = '';
				}
				$counter++;
				$x_spot = $cell_spacing + $col*$cell_width + $col*$cell_spacing;
				$y_spot = $cell_height + $row*$cell_height;
				$coords = 'X:'.$x_spot . ' Y:' .$y_spot;
				$border = 0;
				$border2 = 0;
				//this is the cell that will allow allignment to sticker checking
				$pdf->SetXY($x_spot, $y_spot);
				$pdf->Cell($cell_width, $cell_height,  '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
				
				// CODE 128 A
				$pdf->SetXY($x_spot+$barcode_spacing_adjust, $y_spot);
				//cell to check the barcode placement
				$pdf->Cell($cell_width-2*$barcode_spacing_adjust, $cell_height/2, '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
				$pdf->write1DBarcode($line1, 'C128A', $x_spot+$barcode_spacing_adjust, $y_spot+$barcode_height_adjust, $cell_width-2*$barcode_spacing_adjust, $cell_height/2 - $barcode_height_adjust, 0.4, $barcode_style, 'N');
				
				//the remaining 3 lines have to fit in 1/2 the sticker size
				//$y_offset = $cell_height/2;
				$pdf->SetXY($x_spot, $y_spot - 0*$line_spacing_adjust + 3/6*$cell_height);
				$pdf->Cell($cell_width, $cell_height/6,  formatCardNumber($line1), $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
				$pdf->SetXY($x_spot, $y_spot - 1*$line_spacing_adjust + 4/6*$cell_height);
				$pdf->Cell($cell_width, $cell_height/6,  $line2, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
				$pdf->SetXY($x_spot, $y_spot -2*$line_spacing_adjust + 5/6*$cell_height);
				$pdf->Cell($cell_width, $cell_height/6,  $line3, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
				
				
				//$pdf->writeHTMLCell($cell_width, $cell_height, $x_spot, $y_spot, $text_for_label, 1, 1, false, true, '', false);
				// no work $pdf->MultiCell($cell_width, $cell_height, $text_for_label, 1, 'J', false, '','',true, 0, false, true, $cell_height, 'T', false);
			}
			$row_offset = 1;
		}
		$column_offset = 1;
	}
//Close and output PDF document
$pdf->Output($pdf_file_name, 'D');

//============================================================+
// END OF FILE
//============================================================+
}
function htmlTest()
{
	//============================================================+
	// File name   : example_006.php
	// Begin       : 2008-03-04
	// Last Update : 2013-03-16
	//
	// Description : Example 006 for TCPDF class
	//               WriteHTML and RTL support
	//
	// Author: Nicola Asuni
	//
	// (c) Copyright:
	//               Nicola Asuni
	//               Tecnick.com LTD
	//               Manor Coach House, Church Hill
	//               Aldershot, Hants, GU12 4RQ
	//               UK
	//               www.tecnick.com
	//               info@tecnick.com
	//============================================================+

	/**
	 * Creates an example PDF TEST document using TCPDF
	 * @package com.tecnick.tcpdf
	 * @abstract TCPDF - Example: WriteHTML and RTL support
	 * @author Nicola Asuni
	 * @since 2008-03-04
	 */

	require_once(TCPDF_LANG);
	require_once(TCPDF);

	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Nicola Asuni');
	$pdf->SetTitle('TCPDF Example 006');
	$pdf->SetSubject('TCPDF Tutorial');
	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);

	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('dejavusans', '', 10);

	// add a page
	$pdf->AddPage();

	// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
	// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

	// create some HTML content
	$html = '<h1>HTML Example</h1>
	Some special characters: &lt; € &euro; &#8364; &amp; è &egrave; &copy; &gt; \\slash \\\\double-slash \\\\\\triple-slash
	<h2>List</h2>
	List example:
	<ol>
		<li><img src=POS_URL . "/3rdParty/tcpdf/images/logo_example.png" alt="test alt attribute" width="30" height="30" border="0" /> test image</li>
		<li><b>bold text</b></li>
		<li><i>italic text</i></li>
		<li><u>underlined text</u></li>
		<li><b>b<i>bi<u>biu</u>bi</i>b</b></li>
		<li><a href="http://www.tecnick.com" dir="ltr">link to http://www.tecnick.com</a></li>
		<li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.<br />Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</li>
		<li>SUBLIST
			<ol>
				<li>row one
					<ul>
						<li>sublist</li>
					</ul>
				</li>
				<li>row two</li>
			</ol>
		</li>
		<li><b>T</b>E<i>S</i><u>T</u> <del>line through</del></li>
		<li><font size="+3">font + 3</font></li>
		<li><small>small text</small> normal <small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal</li>
	</ol>
	<dl>
		<dt>Coffee</dt>
		<dd>Black hot drink</dd>
		<dt>Milk</dt>
		<dd>White cold drink</dd>
	</dl>
	<div style="text-align:center">IMAGES<br />
	<img src=POS_URL . "/3rdParty/tcpdf/images/logo_example.png" alt="test alt attribute" width="100" height="100" border="0" /><img src="../../../3rdParty/tcpdf/images/tiger.ai" alt="test alt attribute" width="100" height="100" border="0" /><img src="../../../3rdParty/tcpdf/images/tiger.ai" alt="test alt attribute" width="100" height="100" border="0" />
	</div>';

	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');


	// output some RTL HTML content
	$html = '<div style="text-align:center">The words &#8220;<span dir="rtl">&#1502;&#1494;&#1500; [mazel] &#1496;&#1493;&#1489; [tov]</span>&#8221; mean &#8220;Congratulations!&#8221;</div>';
	$pdf->writeHTML($html, true, false, true, false, '');

	// test some inline CSS
	$html = '<p>This is just an example of html code to demonstrate some supported CSS inline styles.
	<span style="font-weight: bold;">bold text</span>
	<span style="text-decoration: line-through;">line-trough</span>
	<span style="text-decoration: underline line-through;">underline and line-trough</span>
	<span style="color: rgb(0, 128, 64);">color</span>
	<span style="background-color: rgb(255, 0, 0); color: rgb(255, 255, 255);">background color</span>
	<span style="font-weight: bold;">bold</span>
	<span style="font-size: xx-small;">xx-small</span>
	<span style="font-size: x-small;">x-small</span>
	<span style="font-size: small;">small</span>
	<span style="font-size: medium;">medium</span>
	<span style="font-size: large;">large</span>
	<span style="font-size: x-large;">x-large</span>
	<span style="font-size: xx-large;">xx-large</span>
	</p>';

	$pdf->writeHTML($html, true, false, true, false, '');

	// reset pointer to the last page
	$pdf->lastPage();

	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// Print a table

	// add a page
	$pdf->AddPage();

	// create some HTML content
	$subtable = '<table border="1" cellspacing="6" cellpadding="4"><tr><td>a</td><td>b</td></tr><tr><td>c</td><td>d</td></tr></table>';

	$html = '<h2>HTML TABLE:</h2>
	<table border="1" cellspacing="3" cellpadding="4">
		<tr>
			<th>#</th>
			<th align="right">RIGHT align</th>
			<th align="left">LEFT align</th>
			<th>4A</th>
		</tr>
		<tr>
			<td>1</td>
			<td bgcolor="#cccccc" align="center" colspan="2">A1 ex<i>amp</i>le <a href="http://www.tcpdf.org">link</a> column span. One two tree four five six seven eight nine ten.<br />line after br<br /><small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal  bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla<ol><li>first<ol><li>sublist</li><li>sublist</li></ol></li><li>second</li></ol><small color="#FF0000" bgcolor="#FFFF00">small small small small small small small small small small small small small small small small small small small small</small></td>
			<td>4B</td>
		</tr>
		<tr>
			<td>'.$subtable.'</td>
			<td bgcolor="#0000FF" color="yellow" align="center">A2 € &euro; &#8364; &amp; è &egrave;<br/>A2 € &euro; &#8364; &amp; è &egrave;</td>
			<td bgcolor="#FFFF00" align="left"><font color="#FF0000">Red</font> Yellow BG</td>
			<td>4C</td>
		</tr>
		<tr>
			<td>1A</td>
			<td rowspan="2" colspan="2" bgcolor="#FFFFCC">2AA<br />2AB<br />2AC</td>
			<td bgcolor="#FF0000">4D</td>
		</tr>
		<tr>
			<td>1B</td>
			<td>4E</td>
		</tr>
		<tr>
			<td>1C</td>
			<td>2C</td>
			<td>3C</td>
			<td>4F</td>
		</tr>
	</table>';

	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');

	// Print some HTML Cells

	$html = '<span color="red">red</span> <span color="green">green</span> <span color="blue">blue</span><br /><span color="red">red</span> <span color="green">green</span> <span color="blue">blue</span>';

	$pdf->SetFillColor(255,255,0);

	$pdf->writeHTMLCell(0, 0, '', '', $html, 'LRTB', 1, 0, true, 'L', true);
	$pdf->writeHTMLCell(0, 0, '', '', $html, 'LRTB', 1, 1, true, 'C', true);
	$pdf->writeHTMLCell(0, 0, '', '', $html, 'LRTB', 1, 0, true, 'R', true);

	// reset pointer to the last page
	$pdf->lastPage();

	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// Print a table

	// add a page
	$pdf->AddPage();

	// create some HTML content
	$html = '<h1>Image alignments on HTML table</h1>
	<table cellpadding="1" cellspacing="1" border="1" style="text-align:center;">
	<tr><td><img src=POS_URL . "/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" /></td></tr>
	<tr style="text-align:left;"><td><img src=POS_URL . "/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="top" /></td></tr>
	<tr style="text-align:center;"><td><img src=POS_URL . "/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="middle" /></td></tr>
	<tr style="text-align:right;"><td><img src=POS_URL . "/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="bottom" /></td></tr>
	<tr><td style="text-align:left;"><img src=POS_URL . "/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="top" /></td></tr>
	<tr><td style="text-align:center;"><img src=POS_URL . "/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="middle" /></td></tr>
	<tr><td style="text-align:right;"><img src=POS_URL . "/3rdParty/tcpdf/images/logo_example.png" border="0" height="41" width="41" align="bottom" /></td></tr>
	</table>';

	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');

	// reset pointer to the last page
	$pdf->lastPage();

	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// Print all HTML colors

	// add a page
	/*$pdf->AddPage();

	$textcolors = '<h1>HTML Text Colors</h1>';
	$bgcolors = '<hr /><h1>HTML Background Colors</h1>';

	foreach(TCPDF_COLORS::$webcolor as $k => $v) {
		$textcolors .= '<span color="#'.$v.'">'.$v.'</span> ';
		$bgcolors .= '<span bgcolor="#'.$v.'" color="#333333">'.$v.'</span> ';
	}

	// output the HTML content
	$pdf->writeHTML($textcolors, true, false, true, false, '');
	$pdf->writeHTML($bgcolors, true, false, true, false, '');

	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/

	// Test word-wrap

	// create some HTML content
	$html = '<hr />
	<h1>Various tests</h1>
	<a href="#2">link to page 2</a><br />
	<font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font> <font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font> <font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font> <font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font> <font face="courier"><b>thisisaverylongword</b></font> <font face="helvetica"><i>thisisanotherverylongword</i></font> <font face="times"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face="times">thisisaverylongword</font>';

	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');

	// Test fonts nesting
	$html1 = 'Default <font face="courier">Courier <font face="helvetica">Helvetica <font face="times">Times <font face="dejavusans">dejavusans </font>Times </font>Helvetica </font>Courier </font>Default';
	$html2 = '<small>small text</small> normal <small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal';
	$html3 = '<font size="10" color="#ff7f50">The</font> <font size="10" color="#6495ed">quick</font> <font size="14" color="#dc143c">brown</font> <font size="18" color="#008000">fox</font> <font size="22"><a href="http://www.tcpdf.org">jumps</a></font> <font size="22" color="#a0522d">over</font> <font size="18" color="#da70d6">the</font> <font size="14" color="#9400d3">lazy</font> <font size="10" color="#4169el">dog</font>.';

	$html = $html1.'<br />'.$html2.'<br />'.$html3.'<br />'.$html3.'<br />'.$html2;

	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');

	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// test pre tag

	// add a page
	$pdf->AddPage();

	$html = <<<EOF
<div style="background-color:#880000;color:white;">
Hello World!<br />
Hello
</div>
<pre style="background-color:#336699;color:white;">
int main() {
	printf("HelloWorld");
	return 0;
}
</pre>
<tt>Monospace font</tt>, normal font, <tt>monospace font</tt>, normal font.
<br />
<div style="background-color:#880000;color:white;">DIV LEVEL 1<div style="background-color:#008800;color:white;">DIV LEVEL 2</div>DIV LEVEL 1</div>
<br />
<span style="background-color:#880000;color:white;">SPAN LEVEL 1 <span style="background-color:#008800;color:white;">SPAN LEVEL 2</span> SPAN LEVEL 1</span>
EOF;

	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');

	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

	/*// test custom bullet points for list

	// add a page
	$pdf->AddPage();

	$html = '
<h1>Test custom bullet image for list items</h1>
<ul style="font-size:14pt;list-style-type:img|png|4|4|logo_example.png">
	<li>test custom bullet image</li>
	<li>test custom bullet image</li>
	<li>test custom bullet image</li>
	<li>test custom bullet image</li>
<ul>
';

	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');*/

	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

	// reset pointer to the last page
	$pdf->lastPage();

	// ---------------------------------------------------------

	//Close and output PDF document
	$pdf->Output('example_006.pdf', 'I');

	//============================================================+
	// END OF FILE                                                
	//============================================================+
}

?>