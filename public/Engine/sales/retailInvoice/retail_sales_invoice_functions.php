<?php

/*
These functions are used on the initial version of the POS, so we can copy the whole folder and make new systems...
*/
require_once('../sales_functions.php');

function openInlineCustomerCopySalesInvoice($pos_sales_invoice_id)
{
	$filename = 'Sales_Invoice_' .$pos_sales_invoice_id .'.pdf';
	
	$pdf = printMemoSalesInvoice($pos_sales_invoice_id,$filename,'customer');
	$pdf->Output($filename, 'I');
}
function printCustomerCopySalesInvoice($pos_sales_invoice_id)
{
	$pos_terminal_id = terminalCheck();
	$printer_name = getPrinterName(getDefaultTerminalPrinter($pos_terminal_id));
	$invoice_print_folder = INVOICE_PRINT_FOLDER .$printer_name;
	//check and create directory
	makeDir($invoice_print_folder);
	//$filename = $invoice_print_folder .'/' .$pos_sales_invoice_id .'_'.date('Y-m-d') .'_'.date('H-i-s') .'.pdf';
	$filename = $invoice_print_folder .'/' .$pos_sales_invoice_id .'.pdf';
	
	$pdf = printMemoSalesInvoice($pos_sales_invoice_id,$filename,'customer');
	$pdf->Output($filename, 'F');
}
function printCustomerCopyGiftReceipt($pos_sales_invoice_id)
{
	$pos_terminal_id = terminalCheck();
	$printer_name = getPrinterName(getDefaultTerminalPrinter($pos_terminal_id));
	$invoice_print_folder = INVOICE_PRINT_FOLDER .$printer_name;
	//check and create directory
	makeDir($invoice_print_folder);
	//$filename = $invoice_print_folder .'/' .$pos_sales_invoice_id .'_'.date('Y-m-d') .'_'.date('H-i-s') .'.pdf';
	$filename = $invoice_print_folder .'/' .$pos_sales_invoice_id .'.pdf';
	
	$pdf = printMemoGiftReciept($pos_sales_invoice_id,$filename,'customer');
	$pdf->Output($filename, 'F');


}
function printStoreCopyMemoSalesInvoice($pos_sales_invoice_id)
{
	$pos_terminal_id = terminalCheck();
	$printer_name = getPrinterName(getDefaultTerminalPrinter($pos_terminal_id));
	$invoice_print_folder = INVOICE_PRINT_FOLDER .$printer_name;
	//check and create directory
	makeDir($invoice_print_folder);
	//$filename = $invoice_print_folder .'/' .$pos_sales_invoice_id .'_'.date('Y-m-d') .'_'.date('H-i-s') .'_STORE_COPY.pdf';
	$filename = $invoice_print_folder .'/' .$pos_sales_invoice_id .'_STORE_COPY.pdf';

	$pdf = printMemoSalesInvoice($pos_sales_invoice_id,$filename,'store');
	//this would need to be modified to add per page...
	//$pdf = addStoreCopyGraffiti($pdf);
	$pdf->Output($filename, 'F');
		
}
function addStoreCopyGraffiti($pdf)
{
	//we need to add 'STORE COPY NOT A VALID INVOICE' DIAGONALLY ACROSS IN LARGE LETTERS
	$pdf->SetColor('text', 200,200,200);
			$pdf->SetFont('times', '', 30);
			$pdf->SetXY(0.7, 3.75);
			$pdf->StartTransform();
			// Rotate 20 degrees counter-clockwise centered by (70,110) which is the lower left corner of the rectangle
			$pdf->Rotate(20, '', '');
			$text = 'STORE COPY - NOT A VALID INVOICE';
			//$pdf->Cell(2, 0, $text, 0, 0, 'L', 0, '', 0, false, 'T', 'T');
			$pdf->Write(0,'STORE COPY - UNPAID INVOICE','',false,'L',0,false,false,0,0,'');
			// Stop Transformation
			$pdf->StopTransform();
	return $pdf;

}
function printMemoSalesInvoice($pos_sales_invoice_id, $filename,$type)
{
	

	require_once(TCPDF_LANG);
	require_once(TCPDF);
	
	
	//invoice content
	$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
	$pos_store_id = $invoice_data[0]['pos_store_id']; 
	$store_info = getStore($pos_store_id);
	$pos_customer_id = $invoice_data[0]['pos_customer_id']; 
	$pos_address_id = $invoice_data[0]['pos_address_id']; 
	$invoice_contents = getInvoiceContents($pos_sales_invoice_id);
	$promotion_contents = getSalesInvoicePromotions($pos_sales_invoice_id);
	$pre_tax_promotions = getSalesInvoicePreTaxPromotions($pos_sales_invoice_id);
	$post_tax_promotions = getSalesInvoicePostTaxPromotions($pos_sales_invoice_id);
	$payment_contents = getCustomerPaymentsLinkedSalesInvoice($pos_sales_invoice_id);
	
	$page_format = array('Rotate' =>90);
	$discount_lines = 0;
	/*if(sizeof($pre_tax_promotions) >0)
	{
		$discount_lines=1;
	}*/
	$post_tax_lines = 0;
	if(sizeof($post_tax_promotions) >0)
	{
		$post_tax_lines = 1;
	}
	//determine the number of pages
	//$number_of_lines = sizeof($invoice_contents) + 1 + $discount_lines + 1 + $post_tax_lines +sizeof($payment_contents) + 1;
	$max_lines_per_page = 17;
	//$num_pages = ceil(sizeof($number_of_lines)/$max_content_per_page);
	$total_lines = sizeof($invoice_contents) + sizeof($payment_contents) + sizeof($promotion_contents) +2+$discount_lines + $post_tax_lines;
	$grid = false;
	
	$margin_left = 0;
	$margin_right = 0;
	$margin_top = 0;
	$margin_bottom = 0;
	
	$x_err = -0.05;
	$y_err = -0.05;

	$address_x = 0;
	$address_y = 0.75;
	
	
	$title = 'Sales Invoice';
	$subject = 'Sales Invoice';
	$keywords = '';
	$page_orientation = 'L';
	$page_format = 'MEMO'; //http://www.tcpdf.org/doc/code/classTCPDF__STATIC.html#a3a1488c8eebb35ad1322424c0e68e686
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
	// create new PDF document

	//description and price
		
		$code_x = 0.25;
		$code_width = 1;
		$description_x = 1.25;
		$description_width = 6;
		$contents_y = 1.35;
		$promotions_x = 4.25;
		$promotions_width = 4;
		
		$price_x = 7.25;
		$price_width = 1;
		$cell_height = 0.2;
	// set font
	$pdf->SetFont('Times', '', 10);

	$content_line_counter = 0;
	 $promotion_line_counter = 0;
	 $payment_line_counter = 0;
	 $total_line_counter = 0;
	 $footer_discounts_added=false;
	 $footer_tax_added=false;
	 $footer_post_tax_discount_added=false;
	 $footer_total_added=false;
	 $page_counter = 0;
	 WHILE ($total_line_counter < $total_lines)
	 {
	 	$page_line_counter = 0;
	 	//add the page
	 	$pdf->AddPage();
	 	 $page_counter++;
	 	//add the header
	 	$pdf = invoiceHeader($pdf,$pos_sales_invoice_id,$store_info,$invoice_data,$grid);
	 	
	 	//now we might need a content header;
	 	if($content_line_counter < sizeof($invoice_contents) OR !$footer_total_added)
	 	{
	 		//add the table header
	 		$pdf->SetColor('text', 0,0,0);
			$pdf->SetFont('times', 'B', 10);
			$pdf->SetXY($code_x, $contents_y+($page_line_counter)*$cell_height);
			$pdf->Cell($code_width, $cell_height, 'Code', 1, 0, 'C', 0, '', 0, false, 'T', 'T');
			$pdf->SetXY($description_x, $contents_y+($page_line_counter)*$cell_height);
			$pdf->Cell($description_width, $cell_height, 'Description', 1, 0, 'C', 0, '', 0, false, 'T', 'T');
			$pdf->SetXY($price_x, $contents_y+($page_line_counter)*$cell_height);
			$pdf->Cell($price_width, $cell_height, 'Price', 1, 0, 'C', 0, '', 0, false, 'T', 'T');
	 		$page_line_counter++;
	 	}
	 	elseif($promotion_line_counter < sizeof($promotion_contents))
	 	{
	 		//not doing a header on this table....
	 		//or say 'promotions continued.... $page_line_counter++
	 	}
	 	elseif($payment_line_counter < sizeof($payment_contents))
	 	{
	 		//not doing a header on this table....
	 	}
	 	WHILE ($page_line_counter < $max_lines_per_page && $total_line_counter < $total_lines)
	 	{
	 		if($content_line_counter < sizeof($invoice_contents))
	 		{			
	 			//we are adding the contents
	 			//when it equals the contents we need to add the footer...
	 			$retail_price = $invoice_contents[$content_line_counter]['retail_price'];
	 			$discount = $invoice_contents[$content_line_counter]['discount'];
	 			$quantity = $invoice_contents[$content_line_counter]['quantity'];
	 			$final_price = $invoice_contents[$content_line_counter]['extension']-$invoice_contents[$content_line_counter]['tax_total'];
	 			
	 			$price = number_format($invoice_contents[$content_line_counter]['extension']-$invoice_contents[$content_line_counter]['tax_total'],2);
	 			
	 			if($retail_price*$quantity> $final_price)
	 			{
	 				//item is dicounted
	 				$description = $invoice_contents[$content_line_counter]['checkout_description'] . ' Originally $' . number_format($retail_price*$quantity,2);
	 			}
	 			else
	 			{
	 				//item is regular
	 				$description = $invoice_contents[$content_line_counter]['checkout_description'];
	 			}
	 			
	 			$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', '', 10);
				$pdf->SetXY($code_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($code_width, $cell_height, $invoice_contents[$content_line_counter]['pos_product_sub_id'], 1, 0, 'R', 0, '', 0, false, 'T', 'T');
	 			$pdf->SetXY($description_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($description_width, $cell_height, $description, 1, 0, 'C', 0, '', 0, false, 'T', 'T');
				$pdf->SetXY($price_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($price_width, $cell_height, $price, 1, 0, 'R', 0, '', 0, false, 'T', 'T');
	 			
	 			
	 			$content_line_counter++;
	 			$page_line_counter++;
	 			$total_line_counter++;
	 		}
	 		
	 		elseif(!$footer_discounts_added && sizeof($pre_tax_promotions) >0)
	 		{
	 			/*$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', '', 10);
	 			$pdf->SetXY($description_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($description_width, $cell_height, 'Pre-Tax Discounts', 0, 0, 'R', 0, '', 0, false, 'T', 'T');
				$pdf->SetXY($price_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($price_width, $cell_height, number_format(getSalesInvoicePreTaxPromotionsTotal($pos_sales_invoice_id),2), 1, 0, 'R', 0, '', 0, false, 'T', 'T');*/
	 			$footer_discounts_added=true;
	 			//$page_line_counter++;
	 			//$total_line_counter++;
	 		}
	 		elseif(!$footer_tax_added)
	 		{
	 			$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', '', 10);
	 			$pdf->SetXY($description_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($description_width, $cell_height, 'Tax', 0, 0, 'R', 0, '', 0, false, 'T', 'T');
				$pdf->SetXY($price_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($price_width, $cell_height, number_format(getSalesInvoiceTaxTotalFromContents($pos_sales_invoice_id),2), 1, 0, 'R', 0, '', 0, false, 'T', 'T');	 			
	 			$footer_tax_added=true;
	 			$page_line_counter++;
	 			$total_line_counter++;
	 		}
	 		elseif(!$footer_post_tax_discount_added && sizeof($post_tax_promotions) >0)
	 		{
	 			$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', '', 10);
	 			$pdf->SetXY($description_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($description_width, $cell_height, 'Post Tax Coupons', 0, 0, 'R', 0, '', 0, false, 'T', 'T');
				$pdf->SetXY($price_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($price_width, $cell_height, number_format(getSalesInvoicePostTaxPromotionsTotal($pos_sales_invoice_id),2), 1, 0, 'R', 0, '', 0, false, 'T', 'T');
	 			$footer_post_tax_discount_added=true;
	 			$page_line_counter++;
	 			$total_line_counter++;
	 		}
	 		elseif(!$footer_total_added)
	 		{
	 			$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', 'B', 10);
	 			$pdf->SetXY($description_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($description_width, $cell_height, 'Total', 0, 0, 'R', 0, '', 0, false, 'T', 'T');
				$pdf->SetXY($price_x, $contents_y+($page_line_counter)*$cell_height);
				
				$pdf->Cell($price_width, $cell_height, number_format(getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id),2), 1, 0, 'R', 0, '', 0, false, 'T', 'T');
	 			$footer_total_added=true;
	 			$page_line_counter++;
	 			$total_line_counter++;
	 		}
	 		
	 		elseif($promotion_line_counter < sizeof($promotion_contents))
	 		{			
	 			//we are adding the promotions
	 			$line_text = 'Promotion ' . $promotion_contents[$promotion_line_counter]['promotion_name'] . ' applied ' .number_format($promotion_contents[$promotion_line_counter]['applied_amount'],2);
	 			$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', '', 10);
	 			
				$pdf->SetXY($code_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($promotions_width, $cell_height, $line_text, 0, 0, 'L', 0, '', 0, false, 'T', 'T');
	 			$promotion_line_counter++;
	 			$page_line_counter++;
	 			$total_line_counter++;
	 		}
	 		elseif($payment_line_counter < sizeof($payment_contents))
	 		{			
	 			//we are adding the payments
	 			$line_text = 'Payment using ' . $payment_contents[$payment_line_counter]['payment_type'] . ' in the amount of ' .number_format($payment_contents[$payment_line_counter]['payment_amount'],2);
	 			$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', '', 10);
	 			
				$pdf->SetXY($code_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($promotions_width, $cell_height, $line_text, 0, 0, 'L', 0, '', 0, false, 'T', 'T');
	 			
	 			$payment_line_counter++;
	 			$page_line_counter++;
	 			$total_line_counter++;
	 		}
	 	}
	 	//add the footer
	 	 $pdf = invoiceFooter($pdf,$page_counter,$type);
	 }
	return $pdf;
	


}
function printMemoGiftReciept($pos_sales_invoice_id, $filename,$type)
{
	

	require_once(TCPDF_LANG);
	require_once(TCPDF);
	
	
	//invoice content
	$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
	$pos_store_id = $invoice_data[0]['pos_store_id']; 
	$store_info = getStore($pos_store_id);
	$pos_customer_id = $invoice_data[0]['pos_customer_id']; 
	$pos_address_id = $invoice_data[0]['pos_address_id']; 
	$invoice_contents = getInvoiceContents($pos_sales_invoice_id);
	$promotion_contents = getSalesInvoicePromotions($pos_sales_invoice_id);
	$pre_tax_promotions = getSalesInvoicePreTaxPromotions($pos_sales_invoice_id);
	$post_tax_promotions = getSalesInvoicePostTaxPromotions($pos_sales_invoice_id);
	$payment_contents = getCustomerPaymentsLinkedSalesInvoice($pos_sales_invoice_id);
	
	$page_format = array('Rotate' =>90);
	$discount_lines = 0;
	if(sizeof($pre_tax_promotions) >0)
	{
		$discount_lines=1;
	}
	$post_tax_lines = 0;
	if(sizeof($post_tax_promotions) >0)
	{
		$post_tax_lines = 1;
	}
	//determine the number of pages
	//$number_of_lines = sizeof($invoice_contents) + 1 + $discount_lines + 1 + $post_tax_lines +sizeof($payment_contents) + 1;
	$max_lines_per_page = 17;
	//$num_pages = ceil(sizeof($number_of_lines)/$max_content_per_page);
	$total_lines = sizeof($invoice_contents);
	$grid = false;
	
	$margin_left = 0;
	$margin_right = 0;
	$margin_top = 0;
	$margin_bottom = 0;
	
	$x_err = -0.05;
	$y_err = -0.05;

	$address_x = 0;
	$address_y = 0.75;
	
	
	$title = 'Sales Invoice';
	$subject = 'Sales Invoice';
	$keywords = '';
	$page_orientation = 'L';
	$page_format = 'MEMO'; //http://www.tcpdf.org/doc/code/classTCPDF__STATIC.html#a3a1488c8eebb35ad1322424c0e68e686
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
	// create new PDF document

	//description and price
		
	$code_x = 0.25;
	$code_width = 1;
	$description_x = 1.25;
	$description_width = 6;
	$contents_y = 1.35;
	$promotions_x = 4.25;
	$promotions_width = 4;
	
	$price_x = 7.25;
	$price_width = 1;
	$cell_height = 0.2;
	// set font
	$pdf->SetFont('Times', '', 10);

	$content_line_counter = 0;
	 $promotion_line_counter = 0;
	 $payment_line_counter = 0;
	 $total_line_counter = 0;
	 $footer_discounts_added=false;
	 $footer_tax_added=false;
	 $footer_post_tax_discount_added=false;
	 $footer_total_added=false;
	 $page_counter = 0;
	 WHILE ($total_line_counter < $total_lines)
	 {
	 	$page_line_counter = 0;
	 	//add the page
	 	$pdf->AddPage();
	 	 $page_counter++;
	 	//add the header
	 	$pdf = invoiceHeader($pdf,$pos_sales_invoice_id,$store_info,$invoice_data,$grid);
		if($content_line_counter < sizeof($invoice_contents) OR !$footer_total_added)
	 	{
	 		//add the table header
	 		$pdf->SetColor('text', 0,0,0);
			$pdf->SetFont('times', 'B', 10);
			$pdf->SetXY($code_x, $contents_y+($page_line_counter)*$cell_height);
			$pdf->Cell($code_width, $cell_height, 'Code', 1, 0, 'C', 0, '', 0, false, 'T', 'T');
			$pdf->SetXY($description_x, $contents_y+($page_line_counter)*$cell_height);
			$pdf->Cell($description_width, $cell_height, 'Description', 1, 0, 'C', 0, '', 0, false, 'T', 'T');
			$pdf->SetXY($price_x, $contents_y+($page_line_counter)*$cell_height);
			$pdf->Cell($price_width, $cell_height, 'Price', 1, 0, 'C', 0, '', 0, false, 'T', 'T');
	 		$page_line_counter++;
	 	}
	 	WHILE ($page_line_counter < $max_lines_per_page && $total_line_counter < $total_lines)
	 	{
	 		
	 		if($content_line_counter < sizeof($invoice_contents))
	 		{			
	 			//we are adding the contents
	 			//when it equals the contents we need to add the footer...
	 			//$price = number_format($invoice_contents[$content_line_counter]['extension']-$invoice_contents[$content_line_counter]['tax_total']+$invoice_contents[$content_line_counter]['applied_instore_discount'],2);
	 			$price = 'Secret';
	 			$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', '', 10);
				$pdf->SetXY($code_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($code_width, $cell_height, $invoice_contents[$content_line_counter]['pos_product_sub_id'], 1, 0, 'R', 0, '', 0, false, 'T', 'T');
	 			$pdf->SetXY($description_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($description_width, $cell_height, $invoice_contents[$content_line_counter]['checkout_description'], 1, 0, 'C', 0, '', 0, false, 'T', 'T');
				$pdf->SetXY($price_x, $contents_y+($page_line_counter)*$cell_height);
				$pdf->Cell($price_width, $cell_height, $price, 1, 0, 'C', 0, '', 0, false, 'T', 'T');
	 			
	 			
	 			$content_line_counter++;
	 			$page_line_counter++;
	 			$total_line_counter++;
	 		}
	 	}
	 	//add the footer
	 	 $pdf = invoiceFooter($pdf,$page_counter,$type);
	 }
	return $pdf;
	


}
function invoiceHeader($pdf,$pos_sales_invoice_id,$store_info,$invoice_data,$grid)
{
	// add a page
		
		
		//grid for debugging alignmet
		
		if ($grid)
		{	
			// draw some reference lines
			$linestyle = array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => 	array(240, 240, 240));
			for($line=0;$line<5.5;$line=$line+0.1)
			{
				$pdf->Line(0, $line, 8.5, $line, $linestyle);
			}
			for($line=0;$line<8.5;$line=$line+0.1)
			{
				$pdf->Line($line, 0, $line, 5.5, $linestyle);
			}
		}

	
		
		// HEADER
		//dimensions: THe logo image should be 6 in x 0.5 in at 300 dpi
		$logo_x = 0.25;
		$logo_y = 0.25;
		$logo_width = 6;
		$logo_height = 0.5;
		//is there a logo?
		$logo_name = CUSTOM_IMAGES_FOLDER . '/Invoice/' .getSetting('invoice_print_logo_name');
		if(getSetting('invoice_print_logo_active') == '1' AND file_exists($logo_name))
		{	
			//filename:
			
			$pdf->Image($logo_name, $logo_x, $logo_y, $logo_width, $logo_height, 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);

		}
		else
		{
			//need to write text
			
			//$logo_font = getSetting('invoice_logo_font'); 
			$logo_font = 'times';
			$logo_text = getSetting('company_logo');
			$pdf->SetFont($logo_font, '', 41);
		
			$pdf->SetXY($logo_x-0.05, $logo_y-0.11);
			$pdf->Cell(0, 0,  $logo_text, 0, 0, 'L', 0, '', 0, 0, 'T', 'T');

		}
		
	
		$linestyle = array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => 	array(0, 0, 0));
		$pdf->SetLineStyle($linestyle);
	
		//INVOIVE NUMBER AND DATE
		$invoice_text_x = 6.75;
		$invoice_text_y = 0;
		$invoice_number_x = 6.75;
		$invoice_text_y = 0.33;
		$pdf->SetFont('times', '', 18);
		$pdf->SetColor('text', 0,0,0);
		$pdf->SetXY(6.75, 0.25);
		$pdf->Write(0,'INVOICE','',false,'L',0,false,false,0,0,'');
		//$pdf->Cell(1.75, 0.33,  'INVOICE', 0, 0, 'R', 0, '', 0, 0, 'T', 'T');
		$pdf->SetXY(6.75, 0.475);
		$pdf->SetFont('times', '', 14);
		$pdf->Write(0,'No.','',false,'L',0,false,false,0,0,'');
		$pdf->SetXY(7.25, 0.475);
		$pdf->SetFont('times', '', 14);
		$pdf->SetColor('text', 0,0,0);
		$pdf->Write(0,$pos_sales_invoice_id,'',false,'L',0,false,false,0,0,'');	
		
		/*$pdf->SetXY(7.25, 0.5);
		$pdf->SetFont('times', '', 14);
		$pdf->SetColor('text', 255,0,0);
		$pdf->Write(0,$pos_sales_invoice_id,'',false,'L',0,false,false,0,0,'');*/
	
		//Date
		$pdf->SetXY(6.75, 0.65);
		$pdf->SetColor('text', 0,0,0);
		$pdf->SetFont('times', '', 14);
		$pdf->Write(0,'Date:','',false,'L',true,0,false,false,0,0,'');
		$pdf->SetXY(7.25, 0.65);
		$pdf->SetColor('text', 0,0,0);
		$pdf->SetFont('times', '', 14);
		$invoice_date = date('n/j/Y',strtotime($invoice_data[0]['invoice_date']));
		$pdf->Write(0,$invoice_date,'',false,'L',true,0,false,false,0,0,'');

		//Address
		$pdf->SetXY(0.25-.05, 0.75);
		$pdf->SetColor('text', 0,0,0);
		$pdf->SetFont('times', '', 10);
	
		$address = $store_info[0]['shipping_address1'] . ' - ' . $store_info[0]['shipping_city'] . ', ' . getStateShortName($store_info[0]['pos_state_id']) . ' ' . $store_info[0]['shipping_zip'];
		$phone = $store_info[0]['phone'] ;
		$website = $store_info[0]['website'];
		$pdf->Write(0,$address,'',false,'L',true,0,false,false,0,0,'');
		$pdf->SetXY(0.25-.05, 0.9);
		$pdf->Write(0,$phone,'',false,'L',true,0,false,false,0,0,'');
		$pdf->SetXY(0.25-.05, 1.05);
		$pdf->Write(0,$website,'',false,'L',true,0,false,false,0,0,'');
		
		return $pdf;
}
function invoiceFooter($pdf,$page, $type)
{
//FOOTER
		$pdf->SetColor('text', 0,0,0);
		$pdf->SetFont('times', '', 8);
		$return_policy = getSetting('invoice_return_policy');
		$pdf->setCellHeightRatio(0.85);
		$pdf->SetXY(0.25, 4.8);
		//$pdf->Cell(8, 0, $return_policy, 0, 0, 'C', 0, '', 0, false, 'T', 'T');
		$pdf->MultiCell(8, 0.7, $return_policy, 0, 'C', 0, 0, '', '', true);
		
		$pdf->setCellHeightRatio(1);
		$pdf->SetColor('text', 0,0,0);
		$pdf->SetFont('times', '', 10);
		$pdf->SetXY(3.75, 5.1);
		$pdf->Cell(1, 0, 'Thank You!', 0, 0, 'C', 0, '', 0, false, 'T', 'T');
		
		$pdf->SetColor('text', 0,0,0);
		$pdf->SetFont('times', '', 6);
		$pdf->SetXY(6.25, 5.15);
		$page_marker = 'Page ' . ($page);//. ' of ' . $num_pages;
		$pdf->Cell(2, 0, $page_marker, 0, 0, 'R', 0, '', 0, false, 'T', 'T');
		if (strtoupper($type)=='STORE')
		{
			$pdf->SetColor('text', 200,200,200);
			$pdf->SetFont('times', '', 30);
			$pdf->SetXY(0.7, 3.75);
			$pdf->StartTransform();
			// Rotate 20 degrees counter-clockwise centered by (70,110) which is the lower left corner of the rectangle
			$pdf->Rotate(20, '', '');
			$text = 'STORE COPY - NOT A VALID INVOICE';
			//$pdf->Cell(2, 0, $text, 0, 0, 'L', 0, '', 0, false, 'T', 'T');
			$pdf->Write(0,'STORE COPY - NOT A VALID INVOICE','',false,'L',0,false,false,0,0,'');
			// Stop Transformation
			$pdf->StopTransform();
		}
		
		return $pdf;
}
function emailInvoicePDF($pos_sales_invoice_id)
{
	if (LIVE)
	{
		$to = getCustomerEmailFromInvoice($pos_sales_invoice_id);
		$from_email = getSetting('sales_email');
		$from_name = getSetting('company_name');
		$subject = getSetting('company_name') . ' : Invoice # ' . $pos_sales_invoice_id ;
	}
	else
	{
		$from_email = ADMIN_EMAIL;
		$from_name = 'ADMIN TEST';
		$to = ADMIN_EMAIL;
		//$to = getCustomerEmailFromInvoice($pos_sales_invoice_id);
		$subject = 'TEST ' . getSetting('company_name') . ' : Invoice # ' . $pos_sales_invoice_id;
	}
	if ($to)
	{
		$filename = getSetting('company_name') . '_Invoice#' . $pos_sales_invoice_id .'.pdf';
		$pdf = printMemoSalesInvoice($pos_sales_invoice_id,$filename,'customer');
		$attachment = $pdf->Output($filename, 'S');
		
		
		require_once(PHP_MAILER);
		$mailer = new PHPMailer();

		$mailer->AddReplyTo($from_email, 'Reply To');
		$mailer->SetFrom($from_email, 'Sent From');
		$mailer->FromName = getSetting('company_name');
		$mailer->AddAddress($to, 'Send To');
		$mailer->Subject = $subject;
		$mailer->AltBody = "To view the message, please use an HTML compatible email viewer";
		$mailer->MsgHTML(getSetting('sales_invoice_email_text'));
		if ($pdf) {$mailer->AddStringAttachment($attachment, $filename);}

		$mailer->Send();

	
		return 'Email Sent to ' .$to;
		
	}
	else
	{
		return 'ERROR';
	}
}
function emailInvoiceHtml($pos_sales_invoice_id)
{
	///this is basically a bessatch cause I would have to recode for every pdf change.... currently we are attaching the pdf instead
	if (LIVE)
	{
		$to = getCustomerEmailFromInvoice($pos_sales_invoice_id);
		$from_email = getSetting('sales_email');
		$from_name = getSetting('company_name');

	}
	else
	{
		$from_email = ADMIN_EMAIL;
		$from_name = 'ADMIN TEST';
		$to = ADMIN_EMAIL;
	}
	if ($to)
	{
		$html = generateInvoiceEmailHtml($pos_sales_invoice_id);
		$subject = getSetting('company_name') . ' : Invoice # ' . $pos_sales_invoice_id;
		// Make sure to escape quotes
		$headers = "From: " . $from_email . "\r\n";
		$headers .= "Reply-To: ". $from_email . "\r\n";
		$headers .= "Return-Path: " . "\r\n";
		$headers  .= 'MIME-Version: 1.0' . "\r\n";
	
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		//$headers .=  getSetting('company_name') . ' : INVOICE# ' . $from_name . ' <' . $from_email . '>' . "\r\n";
	
		mail($to, $subject, $html, $headers);
		return "Email Sent";
		
	}
	else
	{
		return 'ERROR';
	}
}
function generateInvoiceEmailHtml($pos_sales_invoice_id)
{
	
	$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
	$pos_store_id = $invoice_data[0]['pos_store_id']; 
	$store_info = getStore($pos_store_id);
	$pos_customer_id = $invoice_data[0]['pos_customer_id']; 
	$pos_address_id = $invoice_data[0]['pos_address_id']; 
	$invoice_contents = getInvoiceContents($pos_sales_invoice_id);
	$promotion_data = getSalesInvoicePromotions($pos_sales_invoice_id);
	$payment_contents = getCustomerPaymentsLinkedSalesInvoice($pos_sales_invoice_id);
	//this format is different than other formats, so we will just create it here.....
	//printing should use serif - times new roman
	// digital should use helveitca...etc
	

	$html = '';
	$html .= '<div >';
		$html .= '<table width="100%"  style="border-bottom:1px solid rgb(50,50,50);">';
		$html .= '<tr valign="top">';
			$html .= '<td width="70%" >';
			//put the company image here - although we really do not want to be emailing images
			$html .= '<b>'.getSetting('company_logo').'</b>';
			$html .= '</td>';
		$html .= '<td width="30%" align="right">INVOICE NUMBER: <font color="#F00"> ' .str_pad($pos_sales_invoice_id, 6, "0", STR_PAD_LEFT).'</font></td>';
		$html .= '</tr>';
		//Store Address and phone number and web site
		$html .= '<tr>';
		$html .= '<td width="70%" style="">';
		$html .= $store_info[0]['shipping_address1'] . ' - ' . $store_info[0]['shipping_city'] . ', ' . getStateShortName($store_info[0]['pos_state_id']) . ' ' . $store_info[0]['shipping_zip'];
		$html .= '<br>';
		$html .= $store_info[0]['phone'] ;
		$html .= '<br>';
		//if($type == 'email')
		//{
			$html .= '<a href="'.getSetting('web_store_url').'">'.$store_info[0]['website'].'</a>';
		//}
		//else
		//{
		//	$html .= $store_info[0]['website'];
		//}
		
		$html .= '</td>';
		$html .= '<td  style="text-align:right;vertical-align:bottom;">DATE: '. date('n/j/Y', strtotime(getdatefromdatetime($invoice_data[0]['invoice_date']))).'</td>';
			$html .= '</tr>';
		$html .= '</table>';
//	$html .= '</td>';
	//$html .= '</tr>';
	//$html .= '<tr>';
	
/*	//CUSOTMER
	$html .= '<table width="100%"  >';
	$html .= '<tr>';
	$html.= '<td>';
	$html.= 'Sold to:';
	$html.= '<br>';
	if($pos_customer_id != 0)
	{
		$html.= getCustomerFullName($pos_customer_id);
		$html.= '<br>';
		$html.= 'Email: ' .getCustomerEmail($pos_customer_id);
		$html.= '<br>';
		$html.= 'Phone: ' .getCustomerPhone($pos_customer_id);
		$html.= '<br>';
		$html.= 'Address: ' .getFullAddress($pos_address_id);
		
	}
	$html .='</td>';
	$html .='</tr>';
	$html .='</table>';
	
	*/
	//CONTENTS
	$html .= '<div align="center">';
	
	$html .= '<table border ="0" width="100%"   align="center">';
	//header
	$th_style = 'border: 0px solid black;';
	$td_style = 'border: 1px solid black;';
	
	$table_def = array(
	
	array('db_field' => 'checkout_description',
			'type' => 'td',
			'td_style' => 'text-align:left;',
			'caption' => 'Description'),
	
	array('db_field' => 'extension',
			'type' => 'td',
			'td_style' => 'text-align:right;',
			'caption' => 'Price'),
	);
	$html .= '<tr>';

		
		$html.= '<th width="90%" style="' . 'text-align:left;' . '">';
		$html.= 'Description';
		$html.='</th>';
		
		$html.= '<th width="10%" style="' . 'text-align:right;' . '">';
		$html.= 'Price';
		$html.='</th>';
	$html .= '</tr>';
	
	for($row=0;$row<sizeof($invoice_contents);$row++)
	{
		$html .= '<tr>';
		$html.= '<td  style="' . $td_style . 'text-align:left;' .'">';
		$html.= $invoice_contents[$row]['checkout_description'];
		$html.='</td>';
		$html.= '<td style="' . $td_style . 'text-align:right;' .'">';
		$html.= $invoice_contents[$row]['extension'];
		$html.='</td>';
		
		$html .= '</tr>';
		
	}
	
	$html .= '<tr>';
	//need to put the footer under the price....
	$html.= '<td colspan ="'.(sizeof($table_def)-1) .'" style="text-align:right;">';
	$html .= 'Tax';
	$html.= '</td>';
	$html .= '<td style="text-align:right;">'. getTaxTotal($pos_sales_invoice_id) .'</td>';
	$html.= '</tr>';
	$html.= '<tr>';
	$html.= '<td colspan ="'.(sizeof($table_def)-1) .'" style="text-align:right;">Total</td>';
	$html .= '<td style="text-align:right;">' .getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id) .'</td>';
	$html .= '</tr>';
	$html .='</table>';
	$html.= '</div>';
	
	
	/*
	//Promotions/discounts
	
	if(sizeof($promotion_data)>0)
	{
		$html .= '<div style="padding-top:5px;margin-top:5px;">';
		$html .= '<table style="width:100%;border-collapse: collapse;border: 2px solid black"  >';
		$html.= '<tr>';
		$html .= '<th style="'.$th_style.'">Promotion Name</th>';
		$html .= '<th style="'.$th_style.'">Amount Applied</th>';
		$html .= '</tr>';
		for($row=0;$row<sizeof($promotion_data);$row++)
		{
			$html.= '<tr>';
			$html .= '<td style="'.$td_style.'">'. $promotion_data[$row]['promotion_name'].'</td>';
			$html .= '<td style="'.$td_style.'text-align:right;">'. round($promotion_data[$row]['applied_amount'],2).'</td>';
			$html .= '</tr>';
		}
		$html .= '</table>';
		$html.= '</div>';
	}
	*/
	/*
	//Payments
	if(sizeof($payment_contents)>0)
	{
		$html .= '<div style="padding-top:5px;margin-top:5px;">';
		$html .= '<table style="border-collapse: collapse;border: 2px solid black"  >';
		$html.= '<tr>';
		$html .= '<th style="'.$th_style.'">Payment Type</th>';
		$html .= '<th style="'.$th_style.'">Payment Amount</th>';
		$html .= '</tr>';
		for($row=0;$row<sizeof($payment_contents);$row++)
		{
			$html.= '<tr>';
			$html .= '<td style="'.$td_style.'">'. $payment_contents[$row]['payment_type'].'</td>';
			$html .= '<td style="'.$td_style.'text-align:right;">'. round($payment_contents[$row]['payment_amount'],2).'</td>';
			$html .= '</tr>';
		}
		$html .= '</table>';
		$html.='</div>';
	}
	*/
	
	
	//$html .= createRetailSalesInvoiceView($pos_sales_invoice_id);
	
	//$html .= createRetailSalesInvoiceContentsView($pos_sales_invoice_id);
	//$html .= createRetailSalesInvoiceFooterView($pos_sales_invoice_id);

	//$html .= createRetailSalesInvoicePromotionsView($pos_sales_invoice_id);
	//$html .= createRetailSalesInvoicePaymentsView($pos_sales_invoice_id);

	$html .= '</div>';
	return $html;
	
	
}
function createRetailSalesPaymentsTableDef($table_name)
{
	$table_object_name = $table_name . '_object';

	$payments = getCustomerPaymentMethods();

	$columns = array(
		
				array(
					'db_field' => 'pos_payment_id',
					'type' => 'hidden',
					'POST' => 'no'
					),
				

				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'input',
				'element' => 'input',
				'element_type' => 'none',
				'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										'readOnly' => '"true"',
										'size' => '"3"')
					),
				array('db_field' => 'pos_customer_payment_method_id',
					'caption' => 'Payment Type',
					'type' => 'select',
					'select_names' => $payments['payment_type'],
					'select_values' => $payments['pos_customer_payment_method_id'],
					'properties' => array(	
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
				array('caption' => 'Amount',
						'db_field' => 'payment_amount',
					'type' => 'input',
					'total' => 2,
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'onkeyup' => 'function(){calculateTotals();}',
											'onblur' => 'function(){calculateTotals();}',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Comments',
				'db_field' => 'comments',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}'))
				
				
			);			
					
	
	return $columns;
	
	
	
}

function getCustomerEmailFromInvoice($pos_sales_invoice_id)
{
	$email = getSingleValueSQL("SELECT email1 FROM pos_customers
								LEFT JOIN pos_sales_invoice ON pos_sales_invoice.pos_customer_id = pos_customers.pos_customer_id
								WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	
	if ($email)
	{
		//check for valid email
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
    		return $email;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
	
}
function createRetailSalesPromotionsTableDef($table_name)
{
	$table_object_name = $table_name . '';
	
	$available_promotions = getAvailablePromotions();

	$columns = array(
		
				array(
					'db_field' => 'pos_promotion_id',
					'type' => 'hidden',
					'POST' => 'no'
					),
				

				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'input',
				'element' => 'input',
				'element_type' => 'none',
				'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										'readOnly' => '"true"',
										'size' => '"3"')
					),
				array('db_field' => 'promotion_code',
					'caption' => 'Promotion<br>Code',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"17"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',)
					),
					/*array('caption' => 'promotion<br>Name',
					'db_field' => 'pos_promotion_id',
					'type' => 'select',
					'select_names' => $available_promotions['promotion_name'],
					'select_values' => $available_promotions['pos_promotion_id'],
					'properties' => array(	'style.width' => '"7em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){}',
											'onkeyup' => 'function(){}',
											'onmouseup' => 'function(){}')),*/
				array('db_field' => 'promotion_name',
					'caption' => 'Promotion<br>Name',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"30"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
				array('caption' => 'Promotion<br>Type',
						'db_field' => 'promotion_type',
					'type' => 'hidden'),
					
				array('caption' => 'Promotion<br>Amount',
				'db_field' => 'promotion_amount',
					'type' => 'input',
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Qualifying<br>Amount',
				'db_field' => 'qualifying_amount',
					'type' => 'input',
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Expired<br>Value',
				'db_field' => 'expired_value',
					'type' => 'input',
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => '$<br>OR<br>%',
				'db_field' => 'percent_or_dollars',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"3"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Expiration Date',
				'db_field' => 'expiration_date',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
			/*	array('caption' => 'Applicable<br>To Sale Items',
				'db_field' => 'check_if_can_be_applied_to_sale_items',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'size' => '"3"',
											'className' => '"readonly"',
											'disabled' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),*/
			array('caption' => 'Applied<br>Amount',
				'db_field' => 'applied_amount',
					'type' => 'input',
					'round' => 2,
					'total' => 2,
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),	
				
				
			);			
					
	
	return $columns;
	
	
	
}
function createRetailSalesInvoiceContentsFooterTableDef($pos_sales_invoice_id)
{
	
	return array(array('caption' => 'Full Price <br>Product Subtotal',
					'db_field' => 'full_price_subtotal',
					'type' => 'input',
					'tags' =>  '  size="10" class="footerCell" readonly = "readonly" onblur="calculateTotals(this)" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				array('caption' => 'Discounted<br>Product Subtotal',
					'db_field' => 'discounted_subtotal',
					'type' => 'input',
					'tags' =>  '  size="10" class="footerCell" readonly = "readonly" onblur="calculateTotals(this)" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				array('caption' => 'In Store Promotions',
					'db_field' => 'pre_tax_promotion_amount',
					'type' => 'input',
					'tags' =>  '  size="10" class="footerCell" readonly = "readonly" onblur ="calculateTotals(this)"  onkeyup="checkInput2(this,\'0123456789.\');calculateTotals(this)" ',
					'value' => getInStorePromotionsApplied($pos_sales_invoice_id),
					'round' => 2,

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				array('caption' => 'Pre Tax Subtotal',
					'db_field' => 'pre_tax_subtotal',
					'type' => 'input',
					'tags' =>  '  size="10" class="footerCell" readonly = "readonly" onblur="calculateTotals(this)" ',

					'element' => 'input',
					'element_type' => 'text',
					),
				/*array('caption' => 'Shipping',
					'db_field' => 'shipping_amount',
					'type' => 'input',
					'tags' =>  '  size="10" class="footerCell" onkeyup="checkInput2(this,\'0123456789.\');calculateTotals(this);" onblur="calculateTotals(this);" ',

					'element' => 'input',
					'element_type' => 'text',
					),*/
				array('caption' => 'Tax',
					'db_field' => 'invoice_tax_total',
					'type' => 'input',
					'tags' =>  '  size="20" class="footerCell" readonly = "readonly" onblur="calculateTotals(this)" ',

					'element' => 'input',
					'element_type' => 'text',
					),
				array('caption' => 'Manufacturer Promotions',
					'db_field' => 'post_tax_promotion_amount',
					'type' => 'input',
					'tags' =>  ' size="10" class="footerCell" readonly = "readonly" onkeyup="checkInput2(this,\'0123456789.\');calculateTotals(this)" onblur="calculateTotals(this)" ',
					'value' => getManufacturerPromotionsApplied($pos_sales_invoice_id),
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					),
				array('caption' => 'Number Of Items',
					'db_field' => 'total_quantity',
					'type' => 'input',
					'tags' =>  ' size="5" class="footerCell" readonly="readonly" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				array('caption' => 'Returns',
					'db_field' => 'total_returns',
					'type' => 'input',
					'tags' =>  ' size="5" class="footerCell" readonly="readonly" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				array('caption' => 'Le Grande Total',
					'db_field' => 'le_grande_total',
					'type' => 'input',
					'tags' =>  '  size="10" style="border-left:1px solid rgb(50,50,50);border-right:1px solid rgb(50,50,50);color:#000;font-size:1.8em;font-weight:bold" class="leGrandTotal" readonly="readonly" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
			/*	array('caption' => 'You Save',
					'db_field' => 'you_save',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly="readonly" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array())*/
				);
}
function getInvoiceContents($pos_sales_invoice_id)
{
	//this data is a bit trick because we want to condense the 'rows'
	
	$sql = "SELECT pos_products.pos_product_id, pos_category_id, pos_manufacturer_brand_id, pos_sales_invoice_contents.* , 
	if(discount_type='PERCENT',concat(round(pos_sales_invoice_contents.discount,2),'%'), concat('$', round(pos_sales_invoice_contents.discount,2))) as discount,
	
		round(if(content_type= 'CREDIT_CARD' OR content_type = 'SHIPPING', pos_sales_invoice_contents.retail_price, pos_sales_invoice_contents.sale_price),2) as price,
		concat_ws(' ', brand_name, pos_sales_invoice_contents.style_number, color_code, pos_sales_invoice_contents.title, size) as description,
		
		(SELECT group_concat(if(pos_sales_invoice_contents.pos_product_sub_id = 0, concat('No product id: ',checkout_description), concat(pos_manufacturer_brands.brand_name,':',pos_products.style_number,'<br>',
		
			(SELECT group_concat(concat(attribute_name,':',option_name) SEPARATOR '<br>') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			)) SEPARATOR '<BR>')
		
		FROM pos_products_sub_id 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE pos_products_sub_id.pos_product_sub_id = pos_sales_invoice_contents.pos_product_sub_id) as product_options
		
		
		
		
		
		FROM pos_sales_invoice_contents LEFT JOIN pos_products_sub_id ON pos_products_sub_id.pos_product_sub_id = pos_sales_invoice_contents.pos_product_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id WHERE pos_sales_invoice_id = $pos_sales_invoice_id ORDER BY row_number ASC";
		

	$data = getSQL($sql);
	
	//we have to get the tax of each itemmm.....
	//nope...
	return $data;
	//go through each row
	//take the line number as the 'output'
	//if we see the line number more than once 
	//sum the quantity across the 'rows'
	
	/*$condensed_rows = array();
	$row_found = false;
	$last_row_number = 0;
	for($row =0;$row<sizeof($data);$row++)
	{
		$row_number = $data[$row]['row_number'];
		//if the line number is the same as the last line number then update the quantity, otherwise copy the data
		if($last_row_number == $row_number)
		{
			//update the quantity
			$condensed_rows[$row_number-1]['quantity'] = $condensed_rows[$row_number-1]['quantity'] + 1;
		}
		else
		{
			//copy
			$condensed_rows[$row_number-1] = $data[$row];
			//calculate
			//$condensed_rows[$row_number-1]['extension'] = $data[$row][
		}
		$last_row_number = $row_number;

	}
	return $condensed_rows;*/
	
}
?>