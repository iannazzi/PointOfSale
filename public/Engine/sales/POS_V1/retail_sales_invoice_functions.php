<?php
$css_styles_version = 'retail_invoice_styles.2015.05.04.css';

/*
These functions are used on the initial version of the POS, so we can copy the whole folder and make new systems...
*/
require_once('../sales_functions.php');
function test_cc_proccess($type)
{
	//need to test an amount.... 
	//need to test a card....
	//this does not work very well....
	//also need to turn of user lock here....
	
	
	if(LIVE)
	{
		$set_user_lock = true;
		$use_test_card_number = false;
		$test_amount = false;
	}
	else
	{
		$set_user_lock = false;
		$use_test_card_number = false;
		$test_amount = false;
	}
	$test_gateways = false; 			//thinking I might only want to use sandbox for testing, 
										//however does not seem to work for card present
	$test_url = false; 					//test url will not work
	
	if($type =='card_number')
	{
		return $use_test_card_number;
	}
	elseif ($type == 'test_url')
	{
		return $test_url;
	}
	elseif ($type == 'amount')
	{
		return $test_amount;
	}
	
	elseif ($type == 'test_gateways')
	{
		return $test_gateways;
	}
	elseif ($type = 'set_user_lock')
	{
		return $set_user_lock;
	}
	return $test;
}
function getInvoiceStatus($pos_sales_invoice_id)
{
	$invoice_status = getSingleValueSQL("SELECT invoice_status FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	return $invoice_status;
}
function checkForClosedInvoice($pos_sales_invoice_id)
{	
	$invoice_status = getInvoiceStatus($pos_sales_invoice_id);
	if (checkIfUserIsAdmin($_SESSION['pos_user_id']))
	{
	}
	else if(  $invoice_status == 'CLOSED')
	{
		trigger_error('Attempting to access a closed invoice:' .$pos_sales_invoice_id);
		exit();
	}
}
function process_cc_payment($post_values)
{
		
		if(test_cc_proccess('test_url'))
		{
			$post_url = "https://test.authorize.net/gateway/transact.dll";
			//error 13
			$post_url = "https://cardpresent.authorize.net/gateway/transact.dll";
		}
		else
		{
			$post_url = "https://cardpresent.authorize.net/gateway/transact.dll";
		}
		// This section takes the input fields and converts them to the proper format
		// for an http post.  For example: "x_login=username&x_tran_key=a1B2c3D4"
		$post_string = "";
		foreach( $post_values as $key => $value )
		{ 
			$post_string .= "$key=" . urlencode( $value ) . "&"; 
		}
		$post_string = rtrim( $post_string, "& " );
		$request = curl_init($post_url); // initiate curl object
		curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
		curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
		$post_response = curl_exec($request); // execute curl post and store results in $post_response
		// additional options may be required depending upon your server configuration
		// you can find documentation on curl options at http://www.php.net/curl_setopt
		curl_close ($request); // close curl object

		// This line takes the response and breaks it into an array using the specified delimiting character
		$response_array = explode($post_values["x_delim_char"],$post_response);

		// The results are output to the screen in the form of an html numbered list.
		$response = '';
		$response .= "<OL>\n";
		foreach ($response_array as $value)
		{
			$response .= "<LI>" . $value . "&nbsp;</LI>\n";
		}
		$response .= "</OL>\n";
		return $response_array;
			
}
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
function addCustomerSignitureLine()
{
	$payment_contents = getCustomerPaymentsLinkedSalesInvoice($pos_sales_invoice_id);
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
	
	//$pdf->setLanguageArray($l);
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
	 			if (strtoupper($type)=='STORE' && $payment_contents[$payment_line_counter]['transaction_id'] != '')
	 			{
	 				$line_text = 'Payment using ' . $payment_contents[$payment_line_counter]['payment_type'] . ' ' . $payment_contents[$payment_line_counter]['card_number'] . ' in the amount of ' .number_format($payment_contents[$payment_line_counter]['payment_amount'],2) . ' Signature __________________________ ';
	 			}
	 			else
	 			{
	 				if ($payment_contents[$payment_line_counter]['payment_group'] == 'CREDIT_CARD')
	 				{
	 					$line_text = 'Payment using ' . $payment_contents[$payment_line_counter]['payment_type'] . ' ' . $payment_contents[$payment_line_counter]['card_number'] . ' in the amount of ' .number_format($payment_contents[$payment_line_counter]['payment_amount'],2);
	 				}
	 				else
	 				{
	 					$line_text = 'Payment using ' . $payment_contents[$payment_line_counter]['payment_type'] . ' in the amount of ' .number_format($payment_contents[$payment_line_counter]['payment_amount'],2);
	 				}
	 			}
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
		$mailer = createSwift();
		$filename = getSetting('company_name') . '_Invoice#' . $pos_sales_invoice_id .'.pdf';
		$pdf = printMemoSalesInvoice($pos_sales_invoice_id,$filename,'customer');
		$pdf_as_string = $pdf->Output($filename, 'S');
		$msg =getSetting('sales_invoice_email_text');

		$attachment = Swift_Attachment::newInstance($pdf_as_string, $filename, 'application/pdf');

		$message = Swift_Message::newInstance($subject)
			->setContentType("text/html")
			->setFrom(array($from_email =>$from_name ))
			->setTo(array($to => $to))
			->setBody($msg)
			->attach($attachment)
		;

		$result = $mailer->send($message);
		
//		require_once(PHP_MAILER);
//		$mailer = new PHPMailer();
//
//		$mailer->AddReplyTo($from_email, 'Reply To');
//		$mailer->SetFrom($from_email, 'Sent From');
//		$mailer->FromName = getSetting('company_name');
//		$mailer->AddAddress($to, 'Send To');
//		$mailer->Subject = $subject;
//		$mailer->AltBody = "To view the message, please use an HTML compatible email viewer";
//		$mailer->MsgHTML(getSetting('sales_invoice_email_text'));
//		if ($pdf) {$mailer->AddStringAttachment($attachment, $filename);}
//
//		$mailer->Send();

	
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
//		$headers = "From: " . $from_email . "\r\n";
//		$headers .= "Reply-To: ". $from_email . "\r\n";
//		$headers .= "Return-Path: " . "\r\n";
//		$headers  .= 'MIME-Version: 1.0' . "\r\n";
//
//		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//		//$headers .=  getSetting('company_name') . ' : INVOICE# ' . $from_name . ' <' . $from_email . '>' . "\r\n";
	
//		mail($to, $subject, $html, $headers);
		switfMailIt($from, $to, $subject, $html );
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
function getInventorySearchFields($pos_terminal_id = 'false')
{
	$inventory_search_fields = array(				
								array(	'db_field' => 'pos_store_id2',
											'mysql_search_result' => 'pos_store_id2',
											'caption' => 'Store Name',	
											'type' => 'select',
											'value' => getTerminalStoreId($pos_terminal_id),
											'html' => createStoreSelect('pos_store_id2', getTerminalStoreId($pos_terminal_id), 'on', '')),
								array(	'db_field' => 'item_barcode2',
											'mysql_search_result' => 'item_barcode2',
											'caption' => 'Item Barcode',	
											'type' => 'input',
											'exact_match' => 'true',
											'html' => createSearchInput('item_barcode2')
										),
								array(	'db_field' => 'product_description2',
											'mysql_search_result' => 'product_description2',
											'caption' => 'Product Description',	
											'type' => 'input',
											'html' => createSearchInput('product_description2')
										)
										);
		return $inventory_search_fields;

}
function createRetailSalesInvoiceContentsView($pos_sales_invoice_id)
{
	$html = '';
	
//************************** INVOICE CONTENTS ***********************************************
	$invoice_contents = getInvoiceContents($pos_sales_invoice_id);
	$invoice_table_name = 'invoice_table';
	$invoice_contents_table_def = createRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id, $invoice_table_name);
	$html .= createStaticViewDynamicTableV2($invoice_table_name, $invoice_contents_table_def, $invoice_contents, 'class="static_contents_table" style="width:100%" ');
	return $html;
}
function createRetailSalesInvoiceView($pos_sales_invoice_id)
{
	$html = '';
	//************************** INVOICE OVERVIEW ***********************************************
	$html .= ' <div class = "retail_sales_invoice_div">';
	$html .= createInvoiceHtmlTable($pos_sales_invoice_id);
	$html .= '<script>var invoice_date = ' .getSalesInvoiceDateFromDatetime($pos_sales_invoice_id) .';</script>';
	$html .= '</div>';	
	return $html;
}
function createRetailSalesInvoicePromotionsView($pos_sales_invoice_id)
{
	$promotion_data = getSalesInvoicePromotions($pos_sales_invoice_id);
	$html = '';
//******************** PROMOTIONS ***************************************************//
	
	if(sizeof($promotion_data)>0)
	{
		//promotion table
		$html .= '<h3>Enter Promotions Here. Promotions can only apply to full price items.</h3>';
		$promotion_table_name = 'promotion_table';
		$promotion_table_def = createRetailSalesPromotionsTableDef($promotion_table_name);	
		$html .= createStaticViewDynamicTable( $promotion_table_def, $promotion_data);
	}
	
	return $html;
}
function getSalesInvoiceTotalPayment($pos_sales_invoice_id)
{
	$sql = "SELECT sum(payment_amount) FROM pos_customer_payments
			LEFT JOIN pos_sales_invoice_to_payment USING (pos_customer_payment_id)
			WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql);
}

function createInvoiceHtmlTable($pos_sales_invoice_id)
{
	$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
	
	
	$html = '<TABLE id = "retail_sales_invoice_main" name = "retail_sales_invoice_main" class ="retail_sales_invoice_main">';
	$html .= '<TR >';								
	
	//$html .= '<th>SALES ASSOCIATE</th><td>' .getUserFullName($invoice_data[0]['pos_user_id']) . '</td>';
	//$html .= '<th>INVOICE DATE</th>' . '<td>'. dateSelect('invoice_date', getdatefromdatetime($invoice_data[0]['invoice_date']), ' style = "width:100%" ') .'</td>'.newline();//createTDFromTD_def($date_array);
	$html .= '<th width="100" style=text-align:left;">INVOICE DATE</th>' . '<td id="invoice_date" width="150" align="left">'. getdatefromdatetime($invoice_data[0]['invoice_date']).'</td>'.newline();
	$html .= '<th width="100" >SA</th>' . '<td  >'. getUserFullName($invoice_data[0]['pos_user_id']).'</td>'.newline();
	$html .= '<th width="100" >TERMINAL</th>' . '<td width="200" align="left">'. getTerminalName($invoice_data[0]['pos_terminal_id']).'</td>'.newline();
	$html .= '<th width="100" >STORE</th>' . '<td width="70" align="left">'. getStoreName($invoice_data[0]['pos_store_id']).'</td>'.newline();
	//$html .= '<th width="100" >Reg#</th>' . '<td width="70" align="left">'. ''.'</td>'.newline();
	$html .= '<th style="text-align:right;">INVOICE NUMBER</th><td id="invoice_number" width="70" align="right"><font color="#F00"> ' .str_pad($pos_sales_invoice_id, 6, "0", STR_PAD_LEFT).'</font></td>';
	
	$html .= '</tr>';
	$html .= '</table></p>';

	//$html .= '<script>var invoice_table_def = ' . prepareTableDefArrayForJavascriptVerification(array($table_def)) . ';</script>';
	$html .= '<script>var invoice_main_table_id = "invoice_main";</script>';
	return $html;
}
function POSproductLookUpTable()
{
	$html =  '<TABLE style="width:100%;">';
	$html .= '<tr>'.newline();
	$html .= ' <TD style="vertical-align:bottom;width:10%;text-align:center;"><INPUT TYPE="TEXT" class="lined_input"  id="barcode" style = "background-color:yellow;width:100%;" NAME="barcode" onclick="this.select()" onKeyPress="return disableEnterKey(event)" onKeyDown="lookUpBarcodeID(this, event)"	/></td>';
	$html .= '<td style="vertical-align:bottom;width:10%;text-align:center;"><input class = "button2" type="button"  name="add_barcode" value="Add" onclick="addBarcodeButton()"/></td>';
	$html .= '<td style="vertical-align:bottom;text-align:center;width:5%;">'.newline();
	$html .= 'OR';
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;width=65%;text-align:center;">'.newline();
	$html .= ' <style>
.ui-autocomplete-loading {
background: white url("'.POS_ENGINE_URL . '/includes/images/ui-anim_basic_16x16.gif") right center no-repeat;
}
</style>';
	$html.= '<div class="ui-widget" >
<input id="product_search"  value="Type to search, leave spaces between search terms..." style = "border: 1px solid black;width:100%;" onclick="productSearchFocus()"/>
</div>';
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;width:10%;">'.newline();
	$html .= '<input class = "button2" type="button"  name="add_prodcut_subid" value="Add" onclick="addSubidFromSearch()"/>';
	$html .= '</td>'.newLine();
	$html .= '</tr>'.newline();
	$html .=  '</table>';
	$html .= addBeepV3().newline();
	return $html;


}

function createPOSProductSubIDLookupTableDef()
{
		return array(	
			
					array(
							'th' => 'Brand',
							'db_field' => 'pos_manufacturer_brand_id_lookup',
							'type' => 'select',
							'html' => createManufacturerBrandSelect('pos_manufacturer_brand_id_lookup', 'false',  'off', ' onchange="UpdateBrandData()" onkeypress = "return noEnter(event);"  ')),
					array(
							'th' => 'Style Number',
							'db_field' => 'style_number_lookup',
							'type' => 'select',
							'html' => createBlankSelect('style_number_lookup',' onchange="UpdateStyleData()" onkeypress = "return noEnter(event);"  ')),
					array(	'th' => 'Color Code',
							'db_field' => 'color_code_lookup',
							'type' => 'select',
							'html' => createBlankSelect('color_code_lookup',' onchange="UpdateColorCodeData()" onkeypress = "return noEnter(event);" ')),
					array(	'th' => 'Size',
							'db_field' => 'size_lookup',
							'type' => 'select',
							'html' => createBlankSelect('size_lookup',' onchange="UpdateSizeData()" onkeypress = "return noEnter(event);" ')),
					/*array(	'th' => 'Product Sub Id Name',
							'db_field' => 'product_subid_manual_lookup',
							'type' => 'select',
							'html' => createBlankSelect('product_subid_manual_lookup',' '))*/


					);
}
function createRetailSalesInvoiceCusomterView($pos_sales_invoice_id)
{
	$html = '';
	//************************** CUSTOMER  ***********************************************
	$html .= ' <div class = "customer_invoice_div">';
	$html .= createCustomerHtmlTable($pos_sales_invoice_id, $pos_sales_invoice_id);
	$html .= '</div>';
	return $html;
}

function createRetailSalesInvoiceFooterView($pos_sales_invoice_id)
{
	$html = '';
//************* FOOTER *********************************
	$footer_table_name = 'invoice_footer_table';
	$footer_table_def = createRetailSalesInvoiceContentsFooterTableDef($pos_sales_invoice_id);
	$footer_data = array();
	//$totals = getRetailSalesInvoiceTotalArray($pos_sales_invoice_id);
	$footer_data['full_price_subtotal'] = getSalesInvoiceFullPriceTotal($pos_sales_invoice_id);
	//echo getSingleValueSQL("SELECT sum(applied_instore_discount) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	$footer_data['shipping_amount'] = getSalesInvoiceShippingAmount($pos_sales_invoice_id);
	$footer_data['discounted_subtotal'] = getSalesInvoiceDiscountedTotal($pos_sales_invoice_id);
	$footer_data['pre_tax_promotion_amount'] = getInStorePromotionsApplied($pos_sales_invoice_id);
	$footer_data['pre_tax_subtotal'] = getPreTaxSubTotal($pos_sales_invoice_id);
	//$footer_data['invoice_tax_total'] = getLocalRegularTax($pos_sales_invoice_id) + getLocalExemptTax($pos_sales_invoice_id)+getStateRegularTax($pos_sales_invoice_id);
	//$footer_data['invoice_tax_total'] = getTaxTotal($pos_sales_invoice_id);
	$footer_data['invoice_tax_total'] = number_format(getSalesInvoiceTaxTotalFromContents($pos_sales_invoice_id),2);
	$footer_data['post_tax_promotion_amount'] = getManufacturerPromotionsApplied($pos_sales_invoice_id);
	$footer_data['total_quantity'] = getSalesInvoiceNumberOfItems($pos_sales_invoice_id);
	$footer_data['total_returns'] = -getSalesInvoiceReturns($pos_sales_invoice_id);
	$footer_data['le_grande_total'] = '$' .number_format(getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id),2);
	$footer_table_tags = ' class="invoice_summary_table_view" ';
	$html .= createHorizontalViewHTMLTable($footer_table_name, $footer_table_def, $footer_data, $footer_table_tags);
	return $html;
}
function createRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id, $invoice_table_name)
{

$table_object_name = $invoice_table_name;

	$tax_category_names_ids = getSalesTaxCategoriesIdsAndNames();
	$discount_codes = getDiscountCodes();

	$columns = array(
		
				array(
					'db_field' => 'pos_product_id',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'pos_product_sub_id',
					'type' => 'hidden',
					'default_value' => 0
					),
		
				array(
					'db_field' => 'pos_category_id',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'pos_manufacturer_id',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'pos_manufacturer_brand_id',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'pos_return_content_id',
					'type' => 'hidden',
					),
				/*array(
					'db_field' => 'card_number',
					'type' => 'hidden',
					'POST' => 'no'
					),*/
				array(
					'db_field' => 'content_type',
					'type' => 'hidden'
					),
				array(
					'db_field' => 'pos_state_tax_jurisdiction_id',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'pos_state_regular_sales_tax_rate_id',
					'type' => 'hidden',
					//'POST' => 'no'

					),
				array(
					'db_field' => 'pos_state_exemption_sales_tax_rate_id',
					'type' => 'hidden',
					//'POST' => 'no'

					),
				array(
					'db_field' => 'state_regular_tax_rate',
					'type' => 'hidden',

					),
				array(
					'db_field' => 'state_exemption_tax_rate',
					'type' => 'hidden',

					),
				array(
					'db_field' => 'state_exemption_value',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'pos_local_tax_jurisdiction_id',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'pos_local_regular_sales_tax_rate_id',
					'type' => 'hidden',
					///'POST' => 'no'
					),
				array(
					'db_field' => 'pos_local_exemption_sales_tax_rate_id',
					'type' => 'hidden',
					//'POST' => 'no'
					),
				array(
					'db_field' => 'local_regular_tax_rate',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'local_exemption_tax_rate',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'local_exemption_value',
					'type' => 'hidden',
					),
			/*	array(
					'db_field' => 'tax_type',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'item_tax_type',
					'type' => 'hidden',
					'price_array_index' => 'quantity',
					'POST' => 'no'
					),*/
				/*array(
					'db_field' => 'exemption_value',
					'type' => 'hidden',
					),*/

				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'row_checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'row_number',
				'element' => 'input',
				'element_type' => 'none',
				'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										'readOnly' => '"true"',
										'size' => '"3"')
					),
				array('db_field' => 'barcode',
					'caption' => 'Code',
					//'td_width' => '50%',
					//'th_width' => '50%',
					'variable_get_url_link' => array(
			'row_result_lookup' => 'content_type',
			"CREDIT_CARD" => array(
							'url' => POS_ENGINE_URL.'/sales/storeCreditCards/store_credits.php?type=view', 
							'get_data' => array('pos_store_credit_id'=>'pos_store_credit_id')
										),				 
			"PRODUCT" =>  array(
							'url' => POS_ENGINE_URL.'/products/ViewProduct/view_product.php',
							'get_data' => array('pos_product_id' => 'pos_product_id')
										)
													),
					
					
					'word_wrap' => 10,
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
				/*array('caption' => 'Brand',
						'db_field' => 'brand_name',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Style Number',
				'db_field' => 'style_number',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Color<BR>Code',
				'db_field' => 'color_code',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"5"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Color<BR>Description',
				'db_field' => 'color_name',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Title',
				'db_field' => 'title',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"30"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),		*/
			array('caption' => 'Description',
					'db_field' => 'checkout_description',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"80"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
			array('caption' => 'Options',
					'db_field' => 'product_options',
					'POST' => 'no',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	
					'size' => '"100"',
											'readOnly' => 'true',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
			/*	array('caption' => 'Size',
				'db_field' => 'size',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"4"',
											'className' => '"size"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),	*/	
				array('caption' => 'Price',
					'db_field' => 'retail_price',
					'type' => 'input',
					'round' => 2,
					'valid_input' => '0123456789.',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
				array('caption' => 'Sale Price',
					'db_field' => 'sale_price',
					'type' => 'input',
					'round' => 2,
					'valid_input' => '0123456789.',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){calculateTotals(this);}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
				array('caption' => 'QTY',
					'db_field' => 'quantity',
					'type' => 'input',
					'valid_input' => '-01',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"3"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){calculateTotals(this);}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
					/*array('db_field' => 'special_order',
					'caption' => 'Order',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){enablePaidCheck(this)}'
											),
					'td_tags' => array(	'className' => '"test"',
										//'style.backgroundColor' => '"#fff";',
										//'style.textAlign' => '"center";',
										//'style.verticalAlign' => '"middle";',
										//'align' => '"center"'
											)	),*/
				array('db_field' => 'wish_list',
					'caption' => 'Wish<br>List',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	
					'onclick' => 'function(){calculateTotals(this);}'
											)),
				array('db_field' => 'ship',
					'caption' => 'Ship',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){checkAndAddShipping(this);}'
											),
					'td_tags' => array(	'className' => '"test"',
										'style.backgroundColor' => '"#fff";',
										'style.textAlign' => '"center";'
											)),						
				array('caption' => 'Discount<br>Code<br>(Required)',
					'db_field' => 'pos_discount_id',
					'type' => 'select',
					//this part is for the 'view'
					'html' => createDiscountCodeSelect('pos_dicount_id', 'false', 'off'),
					'select_names' => $discount_codes['discount_name'],
					'select_values' => $discount_codes['pos_discount_id'],
					'properties' => array(	'style.width' => '"5em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){updateDiscount(this);}',
											//'onkeyup' => 'function(){updateDiscount(this);}',
											//'onmouseup' => 'function(){updateDiscount(this);}'
											)
											),
				array('caption' => 'Item<BR>Discount<BR>ex:10% or $12.90',
					'db_field' => 'discount',
					'type' => 'input',
					'valid_input' => '$%0123456789.',
					//'round' => 2,

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'readOnly' => 'true',
											//'className' => '"nothing"',
											'className' => '"readonly"',

											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){calculateTotals(this);}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
				
				
				
				
				
				
				array(
					'db_field' => 'discount_type',
					//'price_array_index' => 'quantity',
					'type' => 'hidden'
					),
				array(
					'db_field' => 'pos_promotion_id',
					'caption' => 'promotion',
					'type' => 'hidden',
					'POST' => 'no'

				
					),
				array(
					'db_field' => 'promo_row',
					'caption' => 'promo_row',
					'type' => 'hidden',
					'POST' => 'no'
				
					),
					array(
					'db_field' => 'promo_lock',
					'caption' => 'promo_lock',
					'type' => 'hidden',
					'POST' => 'no'
				
					),
				array('caption' => 'Item<br>Applied<br>Instore<br>Discount',
					'db_field' => 'applied_instore_discount',
					'type' => 'input',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 2,
					//'POST' => 'no',
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){calculateTotals();}',
											'readOnly' => 'true')),	
			
				array('caption' => 'Tax Category',
					'db_field' => 'pos_sales_tax_category_id',
					'type' => 'select',
					'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false'),

							'select_names' => $tax_category_names_ids['tax_category_name'],
						'select_values' => $tax_category_names_ids['pos_sales_tax_category_id'],
					'properties' => array(	'style.width' => '"7em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){}',
											'onkeyup' => 'function(){}',
											'onmouseup' => 'function(){}',
											//'onchange' => 'function(){alert(this.options[this.selectedIndex].text);}'
											'onchange' => 'function(){updateTax(getCurrentRow(this));}')),
				array('caption' => 'Taxable<br>Total',
					'db_field' => 'taxable_total',
					'POST' => 'no',
					'type' => 'input',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 2,
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => 'true',
											'onblur' => 'function(){calculateTotals(this);}')),
				array('caption' => 'Tax Rate',
					'db_field' => 'tax_rate',
					//'POST' => 'no',
					'type' => 'input',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 3,
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => 'true',
											'onblur' => 'function(){calculateTotals(this);}')),	
				
				array('caption' => 'Tax Total',
					'db_field' => 'tax_total',
					//'POST' => 'no',
					'type' => 'input',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 2,
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => 'true',
											'onblur' => 'function(){calculateTotals(this);}')),	
				
					
					array('caption' => 'Line Total',
					'db_field' => 'extension',
					'type' => 'input',
					//'POST' => 'no',
					//'footer' => createRetailSalesInvoiceContentsFooterTableDef($pos_sales_invoice_id),
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					
					'properties' => array(	'size' => '"10"',
											'className' => '"readonly"',
											'onclick' => 'function(){calculateTotals(this);}',
											'readOnly' => 'true')),		
				array('caption' => 'Comments',
					'db_field' => 'comments',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	
											'className' => '"comments"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}'))
				
				
			);			
					
	
	return $columns;
	
	
	
}
function getTotalPayments($pos_sales_invoice_id)
{
	$total_payments = getSingleValueSQL("SELECT sum(applied_amount) FROM pos_sales_invoice_to_payment WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	return $total_payments;
}
function tryToCloseSalesInvoice($pos_sales_invoice_id)
{
	//now see if the invoice can be closed
	$grand_total_from_contents = getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id);
	$total_payments = getTotalPayments($pos_sales_invoice_id);
	if(abs($grand_total_from_contents - $total_payments)<0.0001)
		{
			
			//fully paid, close the invoice.
			runSQL("UPDATE pos_sales_invoice SET payment_status = 'PAID', invoice_status = 'CLOSED' WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
			return 'PAID';
		}
		else
		{
		return 'UNPAID';
		}
}
function createRetailSalesPaymentsTableDef($table_name)
{
	$table_object_name = $table_name . '_object';

	$payments = getCustomerPaymentMethods();

	$columns = array(
		
			
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
				array(
								'th' => 'Payment ID',
								'db_field' => 'pos_customer_payment_id',
								'type' => 'link',
								'get_url_link' => 'payments.php?type=view',
								'get_id_link' => 'pos_customer_payment_id'
								),
					
					
					
					
				array('db_field' => 'pos_customer_payment_method_id',
					'caption' => 'Payment Type',
					'type' => 'select',
					'select_names' => $payments['payment_type'],
					'select_values' => $payments['pos_customer_payment_method_id'],
					'properties' => array(	
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
					/*array('caption' => 'Payment Method',
						'db_field' => 'payment_type',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'readOnly' => '"true"',
											'onkeyup' => 'function(){calculateTotals();}',
											'onblur' => 'function(){calculateTotals();}',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),*/
				array('caption' => 'Summary',
						'db_field' => 'summary',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
					'readOnly' => '"true"',
											'className' => '"nothing"',
											'onkeyup' => 'function(){calculateTotals();}',
											'onblur' => 'function(){calculateTotals();}',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Amount',
						'db_field' => 'applied_amount',
					'type' => 'input',
					'total' => 2,
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
					'readOnly' => '"true"',
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
					'readOnly' => '"true"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}'))
				
				
			);			
					
	
	return $columns;
	
	
	
}
function getCustomerPaymentsSummary($pos_sales_invoice_id)
{
	$sql = "SELECT *, applied_amount, payment_type FROM pos_customer_payments
			LEFT JOIN pos_customer_payment_methods USING (pos_customer_payment_method_id)
			LEFT JOIN pos_sales_invoice_to_payment USING (pos_customer_payment_id)
			LEFT JOIN pos_store_credit USING (pos_store_credit_id)
			WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSQL($sql);
}
function getCustomerPaymentMethodID($payment_type)
{
	$pos_customer_payment_method_id = getSingleValueSQL("SELECT pos_customer_payment_method_id FROM pos_customer_payment_methods WHERE payment_type = '$payment_type'");
	return $pos_customer_payment_method_id;
}





//*******************************CREDIT CARD CHARGING FUNCTIONS *********************************//
function charge_cc($pos_terminal_id, $track1, $amount, $device_type)
{
	
	require_once(AUTHORIZE_NET_LIBRARY);
	
	//from the terminal setup we need to find the authorize.net account
	
	//Setting up a test cp transaction cc post here....this one works!
	//SANBOX ACCOUNT
	//u: embrasse123
	//p: emb14534CPI	

	$transaction = new AuthorizeNetCP('8fC69KELf56Z', '8K43v7NUfLQ59Erj');
	$transaction->amount = '19.99';
	$transaction->setTrack1Data('%B4111111111111111^CARDUSER/JOHN^1803101000000000020000831000000?');
	$transaction->device_type = '4';
	$response = $transaction->authorizeAndCapture();

	if ($response->approved) {
	  echo "<h1>Success! The test credit card has been charged!</h1>";
	  echo "Transaction ID: " . $response->transaction_id;
	} else {
	  echo $response->error_message;
	}
	exit();
}
function createLimitedPaymentGatewaySelect($name, $pos_payment_gateway_id, $tags = '')
{
	//what gateways do we want the terminal to see - not all of them for sure.....
	//opt1: main (online or offline) 
	//opt2: backup (online or offline)
	//or we grey them out and have to change in terminal setup.... probably...
	
	//test or live
	 if (test_cc_proccess('test_gateways'))
	 {
	 		$gateways = getSQL("SELECT pos_payment_gateway_id, gateway_provider, company, model_name, account_number, line FROM pos_payment_gateways LEFT JOIN pos_accounts USING (pos_account_id) WHERE pos_payment_gateways.active = 1");
	 }
	 else
	 	
	 {
	 	$gateways = getSQL("SELECT pos_payment_gateway_id, gateway_provider, company, model_name, account_number, line FROM pos_payment_gateways LEFT JOIN pos_accounts USING (pos_account_id) WHERE pos_payment_gateways.active = 1 ");
	 }
	
	

	
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .=  $tags;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	
	for($i = 0;$i < sizeof($gateways); $i++)
	{
		$html .= '<option value="' . $gateways[$i]['pos_payment_gateway_id'] . '"';
		
		if ( ($gateways[$i]['pos_payment_gateway_id'] == $pos_payment_gateway_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $gateways[$i]['gateway_provider'] . ' ' . $gateways[$i]['line'] . ' ' . $gateways[$i]['company'] . ' ' . ' ' . $gateways[$i]['model_name'] .  ' ' .craigsDecryption($gateways[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;

}

function setTrack1Data($track1data) {
        if (preg_match('/^%.*\?$/', $track1data)) {
            return substr($track1data, 1, -1);
        } else {
            return $track1data;    
        }
    }
function getGatewayDepositAccount($pos_payment_gateway_id)
{
	return getSingleValueSQL("SELECT pos_account_id FROM pos_payment_gateways WHERE pos_payment_gateway_id = $pos_payment_gateway_id");
}
function getInvoiceLineItemFinalPrice($pos_sales_invoice_content_id)
{
	$invoice_contents = getSQL("SELECT * FROM pos_sales_invoice_contents WHERE pos_sales_invoice_content_id = $pos_sales_invoice_content_id");
	$final_price = $invoice_contents[0]['extension'];
	return $final_price;

}
function getOriginalSalesInvoiceID($pos_sales_invoice_content_id)
{
	return getSingleValueSQL("SELECT pos_sales_invoice_id FROM pos_sales_invoice_contents WHERE pos_sales_invoice_content_id = ". $pos_sales_invoice_content_id);
}
function siwtchBRToComma($invoice_contents)
{
	for($row=0;$row<sizeof($invoice_contents);$row++)
	{
		$invoice_contents[$row]['product_options'] = str_replace('<br>', ',' , $invoice_contents[$row]['product_options']);
	}
	return $invoice_contents;
}
function getGatewayProvider($pos_payment_gateway_id)
{
		return getSingleValueSQL("SELECT gateway_provider FROM pos_payment_gateways WHERE pos_payment_gateway_id = $pos_payment_gateway_id");
}
function checkGatewayProviderOnline($pos_payment_gateway_id)
{
	$online = getSingleValueSQL("SELECT line FROM pos_payment_gateways WHERE pos_payment_gateway_id = $pos_payment_gateway_id");
	if ($online == 'online')
	{
		return true;
	}
	else
	{
		return false;
	}
}
function checkAuthNetOnline($pos_payment_gateway_id)
{
	$online = checkGatewayProviderOnline($pos_payment_gateway_id);
	$provider = getGatewayProvider($pos_payment_gateway_id);
	if($gateway_provider == "Authorize.net" && $online)
	{
		return true;
	}
	else
	{
		return false;
	}
	
}
function getProductSUBIDdata($barcode)
{
	$product_sql = "SELECT pos_product_sub_id, pos_products_sub_id.pos_product_id, product_subid_name, attributes_list,
		 retail_price, sale_price, title, style_number,
	 
		   concat(pos_products.style_number,',',
	
			(SELECT group_concat(concat(attribute_name,':',option_name) SEPARATOR ',') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
	
			)   as product_options
	 
		FROM pos_products_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE pos_product_sub_id = '$barcode'";
	$data = getSQL($product_sql);
	return $data;
}
function createRetailSalesPromotionsTableDef($table_name)
{
	$table_object_name = $table_name . '';
	
	$available_promotions = getAvailablePromotions();

	$columns = array(
		
				array(
					'db_field' => 'pos_promotion_id',
					'type' => 'hidden',
					),
				
				array(
					'db_field' => 'blanket',
					'type' => 'hidden',
					'POST' => 'no'
					),

				array(
					'db_field' => 'item_or_total',
					'type' => 'hidden',
					'POST' => 'no'
					),


				array(
					'db_field' => 'expiration_date',
					'type' => 'hidden',
					'POST' => 'no'
					),

				array(
					'db_field' => 'percent_or_dollars',
					'type' => 'hidden',
					'POST' => 'no'
					),

			array(
					'db_field' => 'buy_y_get_x',
					'type' => 'hidden',
					'POST' => 'no'
					),
					
				array(
					'db_field' => 'categories',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'brands',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'products',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'qualifying_amount',
					'type' => 'hidden',
					'POST' => 'no'
					),
		
				array(
					'db_field' => 'check_if_can_be_applied_to_sale_items',
					'type' => 'hidden',
					'POST' => 'no'
					),
					array(
					'db_field' => 'check_if_can_be_applied_to_clearance_items',
					'type' => 'hidden',
					'POST' => 'no'
					),			
					
				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'row_checkbox',

					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'row_number',
				'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										'readOnly' => '"true"',
										'size' => '"3"')
					),
				array('db_field' => 'promotion_code',
					'caption' => 'Promotion<br>Code',
					'type' => 'input',
					'properties' => array(	'size' => '"17"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',)
					),
					
				array('db_field' => 'promotion_name',
					'caption' => 'Promotion<br>Name',
					'type' => 'input',
					'properties' => array(	'size' => '"30"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
				array('caption' => 'Promotion<br>Type',
						'db_field' => 'promotion_type',
					'type' => 'hidden'),
					
				/*array('caption' => 'Promotion<br>Amount',
				'db_field' => 'promotion_amount',
					'type' => 'input',
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),*/
				/*array('caption' => 'Qualifying<br>Amount',
				'db_field' => 'qualifying_amount',
					'type' => 'input',
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),*/
				/*array('caption' => 'Expired<br>Value',
				'db_field' => 'expired_value',
					'type' => 'input',
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),*/
				/*array('caption' => '$<br>OR<br>%',
				'db_field' => 'percent_or_dollars',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"3"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),*/
				/*array('caption' => 'Expiration Date',
				'db_field' => 'expiration_date',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"readonly"',
											'readOnly' => 'true',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),*/
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


function createCustomerSearchColDef()
{
		$col_def = array(
					array('db_field' => 'pos_customer_id',
					'type' => 'hidden',
					
					),
					array('db_field' => 'full_name',
					'type' => 'hidden',
					
					),
					/*array('db_field' => 'select',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'radio',
					'properties' => array("onclick" => 'function(){customerSelect(this.parentNode.parentNode.rowIndex);}'
											)
					),
					*/
					
					array('db_field' => 'first_name',
					'caption' => 'First Name',
					'type' => 'innerHTML',
					'properties' => array(
											)
					),
					
					array('db_field' => 'last_name',
					'caption' => 'Last Name',
					'type' => 'innerHTML',
					'properties' => array(
											)
					),
					array('db_field' => 'email1',
					'caption' => 'email1',
					'type' => 'innerHTML',
					'properties' => array(
											)
					),
						array('db_field' => 'phone',
					'caption' => 'Phone',
					'type' => 'innerHTML',
					'properties' => array(
											)
					),
						array('db_field' => 'product_description',
					'caption' => 'Product Description',
					'td_tags' => array('className'=>'"cust_table_small"'),
					'type' => 'innerHTML',
					'properties' => array(
											)
					),
					array('db_field' => 'comments',
					'caption' => 'comments',
					'td_tags' => array('className'=>'"cust_table_small"'),
					'type' => 'innerHTML',
					'properties' => array(
											)
					),
		
	);
		return $col_def;
} 
function getPromotionData($pos_promotion_id, $active)
{
		
		$sql = "SELECT promotion_code, promotion_name, pos_promotions.pos_promotion_id, promotion_type, expired_value, date(expiration_date) as expiration_date, date(start_date) as start_date, qualifying_amount, check_if_can_be_applied_to_sale_items, check_if_can_be_applied_to_clearance_items, blanket, item_or_total, percent_or_dollars,
		
		
		(SELECT group_concat(concat_ws(':', buy , get, discount) SEPARATOR ',') FROM pos_promotion_buy WHERE pos_promotion_buy.pos_promotion_id = pos_promotions.pos_promotion_id) as buy_y_get_x,
		
		(SELECT group_concat(if(pos_promotion_lookup.pos_category_id=0,NULL,concat_ws(':', include_category, pos_promotion_lookup.pos_category_id, include_subcategories))) FROM pos_promotion_lookup LEFT JOIN pos_categories ON pos_categories.pos_category_id = pos_promotion_lookup.pos_category_id WHERE pos_promotion_lookup.pos_promotion_id = pos_promotions.pos_promotion_id) as categories,
		
		(SELECT group_concat(if(pos_promotion_lookup.pos_manufacturer_brand_id =0,NULL,concat_ws(':', include_brand, pos_promotion_lookup.pos_manufacturer_brand_id))) FROM pos_promotion_lookup LEFT JOIN pos_manufacturer_brands ON pos_manufacturer_brands.pos_manufacturer_brand_id = pos_promotion_lookup.pos_manufacturer_brand_id WHERE pos_promotion_lookup.pos_promotion_id = pos_promotions.pos_promotion_id) as brands,
		
		(SELECT group_concat(if(pos_promotion_lookup.pos_product_id=0,NULL,concat_ws(':', include_product, pos_promotion_lookup.pos_product_id))) FROM pos_promotion_lookup LEFT JOIN pos_products ON pos_products.pos_product_id = pos_promotion_lookup.pos_product_id WHERE pos_promotion_lookup.pos_promotion_id = pos_promotions.pos_promotion_id) as products

		FROM pos_promotions
		WHERE pos_promotion_id = $pos_promotion_id";
		 
		 if ($active) $sql .= " AND active = 1";
		
	$data = getSQL($sql);
	if(sizeof($data) == 1)
	{
		
	return $data[0];
	}
	else
	{
		return array();
	}
}
function getPromotionIDFromCode($promotion_code)
{
	$sql = getSQL("SELECT pos_promotion_id FROM pos_promotions WHERE promotion_code = '" . $promotion_code . "'");
	if(sizeof($sql)>1)
	{
		trigger_error('Promotion code returns more than one entry');
	}
	else
	{
		return $sql[0]['pos_promotion_id'];
	}
	
	
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
function getReturnContents($pos_sales_invoice_id)
{
	
	
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
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id WHERE pos_sales_invoice_contents.quantity >0 AND content_type != 'CREDIT_CARD' AND pos_sales_invoice_id = $pos_sales_invoice_id ORDER BY row_number ASC";
		

	$data = getSQL($sql);

	return $data;


}
function getReturnInvoiceData($pos_return_sales_invoice_id )
{
	if(getSingleValueSQL("SELECT pos_sales_invoice_id FROM pos_sales_invoice WHERE pos_Sales_invoice_id = '$pos_return_sales_invoice_id'"))
	{
	//got a line on the invoice... send back invoice data...cool?
	$return_data['search_type']  = 'INVOICE';
	$return_data['receipt_present']  = 'true';
	$return_data['pos_return_sales_invoice_id']  = $pos_return_sales_invoice_id;
	$return_data['invoice_contents'] = siwtchBRToComma(getReturnContents($pos_return_sales_invoice_id));
	//from these contents we should be able to barcode the item....
	}
	else
	{
		$return_data['error'] = 'Error - Invoice Number ' . $pos_return_sales_invoice_id . 'Was not Found';
		//$return_data['search_type']  = $search_type;
	}
	return $return_data;
}
function getTerminalInfo($pos_terminal_id)
{
	return getSQL("SELECT * FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
}
function getPOSv1CustomerAddresses($pos_customer_id)
{
	$addresses = getSQL("SELECT pos_addresses.*, concat(address1, ' ', address2, ' ', city, ',', pos_states.name, ' ' ,zip) as full_address FROM pos_addresses LEFT JOIN pos_states USING (pos_state_id) LEFT JOIN pos_customer_addresses ON pos_customer_addresses.pos_address_id = pos_addresses.pos_address_id WHERE pos_customer_addresses.pos_customer_id = $pos_customer_id");
	return $addresses;
}
function finalizePaymentTransaction($pos_sales_invoice_id)
{
	//now try to close....
	$return['payment_sataus'] = tryToCloseSalesInvoice($pos_sales_invoice_id);
	if($return['payment_sataus'] == 'PAID')
	{
		//assign any gift cards value
		assignGiftCardValue($pos_sales_invoice_id);
		//send invoices to the printer
		printStoreCopyMemoSalesInvoice($pos_sales_invoice_id);
		printCustomerCopySalesInvoice($pos_sales_invoice_id);
		
	}
	//$return['payment_info'] = getCustomerPaymentsSummary($pos_sales_invoice_id);
	return json_encode($return);
}
function assignGiftCardValue($pos_sales_invoice_id)
{
	//this gift card code should be ran only one time.
	//we should process the 'gift card' at point of payment, only when the invoice is closed.
	//get the contents of the sales invoice
	$invoice_contents = getInvoiceContents($pos_sales_invoice_id);
	$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
		for($row=0;$row<sizeof($invoice_contents);$row++)
		{
			if($invoice_contents[$row]['content_type'] == 'CREDIT_CARD')
			{
				//we have a gift card
				//might be an error with a duplicate card id here
				//check
				$barcode = stripWhiteSpace($invoice_contents[$row]['barcode']);
				
				
				$existing_card = getSQL("SELECT pos_store_credit_card_number_id FROM pos_store_credit_card_numbers where card_number='$barcode'");
				if (sizeof($existing_card)==0)
				{
					//problem
					trigger_error("Card was not created using this system.");
				}
				$existing_card_with_value = getSQL("SELECT pos_store_credit_id FROM pos_store_credit where card_number='$barcode'");
				if (sizeof($existing_card_with_value)>0)
				{
					//problem
					trigger_error("trying to insert a gift card already assigned....");
				}
				else
				{
					$store_credit_insert['original_amount'] = $invoice_contents[$row]['price'];
					$store_credit_insert['card_type'] = 'Gift Card';
					$store_credit_insert['card_number'] = $invoice_contents[$row]['barcode'];
					$store_credit_insert['pos_customer_id'] = $invoice_data[0]['pos_customer_id'];
					$store_credit_insert['date_created'] = $invoice_data[0]['invoice_date'];
					$store_credit_insert['date_issued'] = $invoice_data[0]['invoice_date'];
					$store_credit_insert['pos_user_id'] = $_SESSION['pos_user_id'];
					$dbc = startTransaction();

					$pos_store_credit_id = simpleTransactionInsertSQLReturnID($dbc,'pos_store_credit',$store_credit_insert);
					
					
					//now put this store credit id into the invoice contents...
					
					$pos_sales_invoice_content_id = $invoice_contents[$row]['pos_sales_invoice_content_id'];
					$content_insert = array();
					$content_insert['pos_store_credit_id'] = $pos_store_credit_id;
					$key_val_id['pos_sales_invoice_content_id'] = $pos_sales_invoice_content_id;
					simpleTransactionUpdateSQL($dbc,'pos_sales_invoice_contents', $key_val_id, $content_insert);
					simpleCommitTransaction($dbc);
					
					
				}
			}
		}
}
?>