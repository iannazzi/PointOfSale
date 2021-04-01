<?php
$page_level = 3;
require_once ('../../../Config/config.inc.php');
//echo PHP_LIBRARY;
require_once(PHP_LIBRARY);



require_once (CHECK_LOGIN_FILE);
function createPDFCheck($pos_account_id, $pos_payments_journal_id, $filename, $blank)
{
	

	require_once(TCPDF_LANG);
	require_once(TCPDF);
		
	$grid = false;
	$grid2 = false;
	$title = $filename;
	$subject = 'Check';
	$keywords = 'Check';
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
	$pdf->SetMargins(0.5, 0.5, 0.5);
	//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetAutoPageBreak(TRUE, 0);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->setLanguageArray($l);
	$preferences = array('PrintScaling' => 'None');
	$pdf->setViewerPreferences($preferences);
	$pdf->SetFont('helvetica', 'R', 5);
	// create new PDF document

	// set font
	$pdf->SetFont('Times', '', 10);


	 $pdf->AddPage();
	//grid for debugging alignmet	
	if ($grid)
	{	
		// draw some reference lines
		$linestyle = array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => 	array(240, 240, 240));
		$grid_lines=0.5;
		for($line=0;$line<11;$line=$line+$grid_lines)
		{
			$pdf->Line(0, $line, 8.5, $line, $linestyle);
		}
		for($line=0;$line<8.5;$line=$line+$grid_lines)
		{
			$pdf->Line($line, 0, $line, 5.5, $linestyle);
		}
	}


	if ($grid2)
	{	
		// draw some reference lines
		$linestyle = array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => 	array(240, 240, 240));
		for($line=0;$line<3;$line++)
		{
			$pdf->Line(0, (($line+1)*3.6667), 8.5, (($line+1)*3.6667), $linestyle);
		}
		
	}

	$line_spacing = 0.15;

	//company name
	//is there a logo?
	$logo_name = CUSTOM_IMAGES_FOLDER . '/Invoice/' .getSetting('invoice_print_logo_name');
	$use_logo = true;
	$envelope_return_x_margin = 0.375;
	$envelope_return_Y= 0.625;
	$pdf->SetXY($envelope_return_x_margin, $envelope_return_Y);
	if(getSetting('invoice_print_logo_active') == '1' AND file_exists($logo_name) AND $use_logo)
	{	
		//filename:
				// HEADER
		//dimensions: THe logo image should be 6 in x 0.5 in at 300 dpi
		$logo_width = 2;
		$logo_height = 0.5;
		$pdf->Image($logo_name, $envelope_return_x_margin+0.05, 0.625, $logo_width, '', 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);

	}
	else
	{
		//need to write text
		
		//$logo_font = getSetting('invoice_logo_font'); 
		$logo_font = 'times';
		$logo_text = getSetting('company_logo');
		$pdf->SetFont($logo_font, '', 12);
	
		$pdf->SetXY($envelope_return_x_margin, 0.625);
		$pdf->Cell(0, 0,  $logo_text, 0, 0, 'L', 0, '', 0, 0, 'T', 'T');

	}
	$company_address = getSetting('company_address');
	$company_address = str_replace("\n\r","\n",$company_address );
	//need to split each line up...
	$pdf->SetFont('times', '', 12);
		$pdf->SetColor('text', 0,0,0);
	$comapny_address_lines = explode( "\n", $company_address );
	for($ca=0;$ca<sizeof($comapny_address_lines);$ca++)
	{
		
		$pdf->SetXY($envelope_return_x_margin, 0.75 + $line_spacing*$ca);
		//$pdf->Write(0,$company_address,'',false,'L',0,false,false,0,0,'');
		$pdf->Cell(0, 0,  trim($comapny_address_lines[$ca]), 0, 0, 'L', 0, '', 0, 0, 'T', 'T');
	}
	
	
	
	//bank name
	$bank_line_spacing = 0.1;

	$bank_name = getSingleValueSQL("SELECT legal_name FROM pos_accounts WHERE pos_account_id = $pos_account_id");
	$bank_website = getSingleValueSQL("SELECT website_url FROM pos_accounts WHERE pos_account_id = $pos_account_id");
	$bank_address1 = getSingleValueSQL("SELECT address1 FROM pos_accounts WHERE pos_account_id = $pos_account_id");
	$bank_address2 = getSingleValueSQL("SELECT address2 FROM pos_accounts WHERE pos_account_id = $pos_account_id");
	$bank_city = getSingleValueSQL("SELECT city FROM pos_accounts WHERE pos_account_id = $pos_account_id");
	$bank_state = getSingleValueSQL("SELECT state FROM pos_accounts WHERE pos_account_id = $pos_account_id");
	$bank_zip = getSingleValueSQL("SELECT zip FROM pos_accounts WHERE pos_account_id = $pos_account_id");
	$bank_country = getSingleValueSQL("SELECT country FROM pos_accounts WHERE pos_account_id = $pos_account_id");
	$bank_x_margin = 4.25;
	$bank_y_margin = 2.6;
	$align = 'R';
	//$bank_y_margin = $envelope_return_Y;
	$pdf->SetFont('times', '', 8);
	$pdf->SetColor('text', 0,0,0);
	$pdf->SetXY(0, $bank_y_margin);
	//$pdf->Cell(8.5, 1.0, $bank_name, 1, 0, 'C', 0, '', 0, false, 'T', 'T');
	$pdf->Write(0, $bank_name, '', 0, $align, true, 0, false, false, 0);
	$pdf->SetXY(0, $bank_y_margin +$bank_line_spacing);
	$pdf->Write(0, $bank_website, '', 0, $align, true, 0, false, false, 0);
	
	
		$signature_y = 2.4;
		$signature_x = 6;
		$singature_width = 2;
		$linestyle = array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => 	array(0, 0, 0));

		$pdf->Line( $signature_x, $signature_y, $signature_x +$singature_width,$signature_y, $linestyle);
		$pdf->SetXY($signature_x+.05, $signature_y);
		$pdf->SetFont('times', '', 6);
		$pdf->Write(0,'Authorized Signature' ,'',false,'C',0,false,false,0,0,'');
	
	//check number
	
	//payto
	$pay_to_x =0.375;
	$pay_to_y = 1.5;
	$date_y= 1.0;
	$pdf->SetXY($pay_to_x, $pay_to_y);
	$pdf->SetFont('times', '', 12);
	$pdf->SetColor('text', 0,0,0);
$linestyle = array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => 	array(0, 0, 0));
	if($blank)
	{
		$pdf->Write(0,'PAY EXACTLY' ,'',false,'L',0,false,false,0,0,'');
		$pdf->Line( $pay_to_x+1.3, $pay_to_y+$line_spacing+.05, 7.05,$pay_to_y +$line_spacing+.05, $linestyle);

		//payment lines
		
		$pdf->SetXY($pay_to_x, $pay_to_y+$line_spacing);
		$pdf->SetFont('times', '', 10);
		$pdf->Write(0,'TO THE ORDER OF' ,'',false,'L',0,false,false,0,0,'');
		
		$pdf->SetXY($pay_to_x, $pay_to_y);
		$pdf->SetFont('times', '', 12);
		$pdf->SetColor('text', 0,0,0);	/*$test_name = '';
		for($c=0;$c<160;$c++)
		{
			$test_name .= 'G';
		}
		$pdf->Write(0,'PAY TO THE ORDER OF: ' . substr($test_name,0,39) ,'',false,'L',0,false,false,0,0,'');*/
		//$pdf->Line( $pay_to_x, $pay_to_y+2*$line_spacing+.1, 8,$pay_to_y +2*$line_spacing+.1, $linestyle);
		$pdf->SetXY(6.25, $date_y);
		$pdf->Write(0,'Date:','',false,'L',0,false,false,0,0,'');
		
		$pdf->SetXY(6.25, $pay_to_y);
		$pdf->SetFont('times', '', 12);
		$pdf->SetColor('text', 0,0,0);
		$pdf->Write(0,'DOLLARS $' ,'',false,'L',0,false,false,0,0,'');
		
		$amount_box_x = 7.075;
		$amount_box_y = $pay_to_y;
		$amount_box_w = 0.925;
		$amount_box_h = $line_spacing;

		$pdf->SetXY($amount_box_x, $amount_box_y);
		$pdf->Cell($amount_box_w, $amount_box_h, '', $linestyle, 0, 'R', 0, '', 0, false, 'T', 'T');
		
		
		
		//$pdf->SetXY(7, $pay_to_y+$line_spacing+.05);
		//$pdf->Write(0,'DOLLARS' ,'',false,'R',0,false,false,0,0,'');
		
		$pdf->SetXY($pay_to_x, $pay_to_y+$line_spacing+0.1);
		$pdf->SetFont('times', '', 12);
		$pdf->SetFont('times', '', 8);
		//$void_string = getSetting('check_void_string');
		$pdf->Write(0,'Void If Not Cashed Within 90 Days' ,'',false,'R',0,false,false,0,0,'');
		
		
		//address box
		$ad_linestyle = array('width' => 0.01, 'cap' => 'butt', 'join' => 'round', 'dash' => '', 'phase' => 0, 'color' => 	array(0, 0, 0));
		$address_box_x = 0.5;
		$address_box_y = 1.825;
		$address_box_w = 4.5;
		$address_box_h = .925;

		$pdf->SetXY($address_box_x, $address_box_y);
		$pdf->Cell($address_box_w, $address_box_h, '', $ad_linestyle, 0, 'R', 0, '', 0, false, 'T', 'T');
	
	}
	else
	{
		
		$pos_payee_account_id = getSingleValueSQL("Select pos_payee_account_id FROM pos_payments_journal WHERE pos_payments_journal_id = $pos_payments_journal_id");
		$payee_name = getSingleValueSQL("SELECT legal_name FROM pos_accounts WHERE pos_account_id = $pos_payee_account_id");
		$payee_address1 = getSingleValueSQL("SELECT address1 FROM pos_accounts WHERE pos_account_id = $pos_payee_account_id");
		$payee_address2 = getSingleValueSQL("SELECT address2 FROM pos_accounts WHERE pos_account_id = $pos_payee_account_id");
		$payee_city = getSingleValueSQL("SELECT city FROM pos_accounts WHERE pos_account_id = $pos_payee_account_id");
		$payee_state = getSingleValueSQL("SELECT state FROM pos_accounts WHERE pos_account_id = $pos_payee_account_id");
		$payee_zip = getSingleValueSQL("SELECT zip FROM pos_accounts WHERE pos_account_id = $pos_payee_account_id");
		$payee_country = getSingleValueSQL("SELECT country FROM pos_accounts WHERE pos_account_id = $pos_payee_account_id");
	
		$pdf->SetXY($pay_to_x, $pay_to_y);
		$line_adj = 0.01;
		$pdf->Line( 0.75, $pay_to_y+$line_spacing+$line_adj, 7,$pay_to_y +$line_spacing+$line_adj, $linestyle);
		$pdf->SetFont('times', '', 12);
		$pdf->SetColor('text', 0,0,0);
		$pdf->Write(0,'PAY' ,'',false,'L',0,false,false,0,0,'');
		
		
		$pdf->SetXY($pay_to_x, $pay_to_y);
		$pdf->SetFont('times', '', 12);
		$pdf->SetFont('times', 'B', 12);
		$amount = getSingleValueSQL("Select payment_amount FROM pos_payments_journal WHERE pos_payments_journal_id = $pos_payments_journal_id");
		$pdf->Write(0,'$ ' . str_pad(number_format($amount,2),10,"*",STR_PAD_LEFT) ,'',false,'R',0,false,false,0,0,'');
		
		$amount_string = strtoupper(convert_number_to_money_string(round($amount,2)));
		//echo 'Amount: ' . $amount_string;
		//exit();
		$amount_string = '**' . str_pad($amount_string, 90, "*", STR_PAD_RIGHT);
		$pdf->SetXY(0.75, $pay_to_y+0.02);
		$pdf->SetFont('times', '', 8);
		$pdf->Write(0, $amount_string ,'',false,'L',0,false,false,0,0,'');
		
		$pdf->SetXY($pay_to_x, $pay_to_y+$line_spacing+0.05);
		$pdf->SetFont('times', '', 12);
		$pdf->SetFont('times', '', 8);
		//$void_string = getSetting('check_void_string');
		$pdf->Write(0,'Void If Not Cashed Within 90 Days' ,'',false,'R',0,false,false,0,0,'');
		
	
		
		
		
		$date = getSingleValueSQL("Select date(payment_date) FROM pos_payments_journal WHERE pos_payments_journal_id = $pos_payments_journal_id");
		$newDate = date('F jS, Y', strtotime($date));
		$pdf->SetFont('times', '', 12);
		$pdf->SetXY(0, $date_y);
		$pdf->Write(0,'Date: ' . $newDate ,'',false,'R',0,false,false,0,0,'');
		
	$payment_x = 4.25;
	$payment_y = $date_y-$line_spacing;
	$align = 'R';
	//$bank_y_margin = $envelope_return_Y;
	$pdf->SetFont('times', '', 8);
	$pdf->SetXY(0, $payment_y);
	//$pdf->Cell(8.5, 1.0, $bank_name, 1, 0, 'C', 0, '', 0, false, 'T', 'T');
	$pdf->Write(0, 'PJID-' . $pos_payments_journal_id, '', 0, $align, true, 0, false, false, 0);

		
		
		
		$pdf->SetXY($pay_to_x, $pay_to_y+$line_spacing);
		$pdf->SetFont('times', '', 10);
		$pdf->Write(0,'TO THE ORDER OF' ,'',false,'L',0,false,false,0,0,'');
		$envelope_send_x_margin = 1.0;
		$envelope_send_y_margin = 2.0;
		$line_counter=0;
		$pdf->SetFont('times', 'B', 12);
		$pdf->SetColor('text', 0,0,0);
		$pdf->SetXY($envelope_send_x_margin, $envelope_send_y_margin);
		$pdf->Write(0,$payee_name,'',false,'L',0,false,false,0,0,'');
		$line_counter++;
		$pdf->SetXY($envelope_send_x_margin, $envelope_send_y_margin + $line_counter*$line_spacing);
		$pdf->Write(0,$payee_address1,'',false,'L',0,false,false,0,0,'');
		$line_counter++;
		if($payee_address2 != '')
		{
			$pdf->SetXY($envelope_send_x_margin, $envelope_send_y_margin + $line_counter*$line_spacing);
			$pdf->Write(0,$payee_address2,'',false,'L',0,false,false,0,0,'');
			$line_counter++;
		}
		$pdf->SetXY($envelope_send_x_margin, $envelope_send_y_margin + $line_counter*$line_spacing);
		$pdf->Write(0,$payee_city . ', '. $payee_state . ' ' . $payee_zip,'',false,'L',0,false,false,0,0,'');
		$line_counter++;
		

	}

	
	
	
	
	


		$pdf->SetFont('times', '', 12);

	//*******************************************************************
	//invoice tab sent with check
	$invoice_tab_y = 3.667+0.5;
	$applied_total=0;
	$inv_date_x = 0.5;
	$inv_date_width = 2;
	$invoice_num_x = 2.5;
	$inv_num_width = 2;
	$contents_y = $invoice_tab_y + 0.5;
	$amount_x = 4.5;
	$amount_width = 2;
	$cell_height = 0.2;
	$invoice_string = '';
	$max_invoices_for_table =7;
	$max_size_of_index_string = 500;
	$table_linestyle = array('LTRB' => array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	if($blank)
	{
		$pdf->SetXY(0.5, $invoice_tab_y);
		$pdf->Write(0,'Notes:','',false,'L',0,false,false,0,0,'');
	}
	else
	{
		//there may be too many invoices to fit on the tab as a table.... if so just bulid a list, otherwise make a table...
		$invoice_lookup = getSQL("SELECT source_journal, pos_journal_id, applied_amount FROM pos_invoice_to_payment WHERE pos_payments_journal_id = $pos_payments_journal_id");
		

//need to add some columns: (remove) date, invoice number, discount applied, credit memo's applied, invoice amount previously paid not from this payment, amount left to pay, payment amount

// #fix

		
		
		if(sizeof($invoice_lookup)> $max_invoices_for_table)
		{
			$pdf->SetXY(0.5, $invoice_tab_y);
			
			//add the table header
	 		$pdf->SetColor('text', 0,0,0);
			$pdf->SetFont('times', 'B', 10);
			//$pdf->SetXY($inv_date_x, $contents_y+(0)*$cell_height);
			$txt1 = 'Payment # ' . $pos_payments_journal_id . 'has too many referenced invoices for this tab.';
			$txt2 = 'Please reference the attached sheet for information on how to apply payment to invoices.';
			$pdf->Write(0,$txt1,'',false,'L',0,false,false,0,0,'');
			$pdf->SetXY(0.5, $invoice_tab_y+$line_spacing);
						$pdf->Write(0,$txt2,'',false,'L',0,false,false,0,0,'');

			//$pdf->Cell(8, 1, $txt, $table_linestyle, 0, 'L', 0, '', 0, false, 'T', 'T');

		}
		else
		
		
		
		{
			$pdf->SetXY(0.5, $invoice_tab_y);
			$pdf->Write(0,'Please apply this payment to the following invoices by the specified amount. Our Reference is Payment # '.$pos_payments_journal_id,'',false,'L',0,false,false,0,0,'');
			//add the table header
	 		$pdf->SetColor('text', 0,0,0);
			$pdf->SetFont('times', 'B', 10);
			$pdf->SetXY($inv_date_x, $contents_y+(0)*$cell_height);
			$pdf->Cell($inv_date_width, $cell_height, 'Date', $table_linestyle, 0, 'C', 0, '', 0, false, 'T', 'T');
			$pdf->SetXY($invoice_num_x, $contents_y+(0)*$cell_height);
			$pdf->Cell($inv_num_width, $cell_height, 'Invoice Number', $table_linestyle, 0, 'C', 0, '', 0, false, 'T', 'T');
			$pdf->SetXY($amount_x, $contents_y+(0)*$cell_height);
			$pdf->Cell($amount_width, $cell_height, 'Amount To Apply', $table_linestyle, 0, 'C', 0, '', 0, false, 'T', 'T');
			
			for($co=0;$co<sizeof($invoice_lookup);$co++)
			{
				if($invoice_lookup[$co]['source_journal'] == 'GENERAL JOURNAL')
				{
					$invoice = getSQL("SELECT invoice_number, entry_amount as invoice_amount, invoice_date FROM pos_general_journal WHERE pos_general_journal_id =" . $invoice_lookup[$co]['pos_journal_id']);
				}
				elseif($invoice_lookup[$co]['source_journal'] == 'PURCHASES JOURNAL')
				{
					$invoice = getSQL("SELECT invoice_number, invoice_amount, invoice_date FROM pos_purchases_journal WHERE pos_purchases_journal_id = " . $invoice_lookup[$co]['pos_journal_id']);
				}
				//build a string in case there are too many invoices to list
				$invoice_string .= 'Invoice Date: ' .$invoice[0]['invoice_date'] . ' Invoice # ' . $invoice[0]['invoice_number'] .' Apply: ' .number_format($invoice_lookup[$co]['applied_amount'],2) .'; ';
				$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', '', 10);
				$pdf->SetXY($inv_date_x, $contents_y+($co+1)*$cell_height);
				$pdf->Cell($inv_date_width, $cell_height, $invoice[0]['invoice_date'], 1, 0, 'R', 0, '', 0, false, 'T', 'T');
				$pdf->SetXY($invoice_num_x, $contents_y+($co+1)*$cell_height);
				$pdf->Cell($inv_num_width, $cell_height, $invoice[0]['invoice_number'], 1, 0, 'C', 0, '', 0, false, 'T', 'T');
				$pdf->SetXY($amount_x, $contents_y+($co+1)*$cell_height);
				$pdf->Cell($amount_width, $cell_height,  number_format($invoice_lookup[$co]['applied_amount'],2), 1, 0, 'R', 0, '', 0, false, 'T', 'T');
				$applied_total = $applied_total +$invoice_lookup[$co]['applied_amount'];
			}
	
		
				//close the table
				$pdf->SetXY($invoice_num_x+1.5, $contents_y+($co+1)*$cell_height);
				$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', 'B', 10);
				$pdf->Write(0,'Total','',false,'L',0,false,false,0,0,'');

				$pdf->SetXY($amount_x, $contents_y+($co+1)*$cell_height);
				$pdf->Cell($amount_width, $cell_height,  number_format($applied_total,2), 1, 0, 'R', 0, '', 0, false, 'T', 'T');
				if(abs($applied_total - $amount) > 0.0001)
				{
					$pdf->SetXY($inv_date_x, $contents_y+($co+2)*$cell_height);
					$pdf->SetColor('text', 0,0,0);
					$pdf->SetFont('times', 'B', 10);
					$pdf->Write(0,'WARNING - APPLIED TOTAL DOES NOT MATCH PAYMENT TOTAL. PLEASE CONTACT US FOR APPLIED AMOUNTS.','',false,'L',0,false,false,0,0,'');
					
				}
		}
	}
	
	
	$craig_y_margin = 3+3.667;
	$pdf->SetFont('times', '', 8);
	$pdf->SetColor('text', 0,0,0);
	$pdf->SetXY(0, $craig_y_margin	);
	//$pdf->Cell(8.5, 1.0, $bank_name, 1, 0, 'C', 0, '', 0, false, 'T', 'T');
	$pdf->Write(0, 'Business & Accounting Software and Systems Design by Craig Iannazzi For Embrasse-Moi', '', 0, 'C', true, 0, false, false, 0);
	$pdf->SetXY(0, $craig_y_margin +$line_spacing);
	
	//check list and transfer tab
	//bank transfer
	//enter transfer into journal
	//stamp
	//enter check number into comments
	

	$checklist_tab_y = 2*3.667 +0.5;
	$checklist_x = 0.5;
	$description_x = 0.825;
	$ch_counter = 0;
	$checklist = array();
	
	
	$craig_y_margin = 3.25+2*3.667;
	$pdf->SetFont('times', '', 8);
	$pdf->SetColor('text', 0,0,0);
	$pdf->SetXY(0, $craig_y_margin	);
	//$pdf->Cell(8.5, 1.0, $bank_name, 1, 0, 'C', 0, '', 0, false, 'T', 'T');
	$pdf->Write(0, 'Business & Accounting Software and Systems Design by Craig Iannazzi For Embrasse-Moi', '', 0, 'C', true, 0, false, false, 0);
	
	

	$autopay_account_id = getSingleValueSQL("SELECT autopay_account_id FROm pos_accounts WHERE pos_account_id = $pos_account_id");
		$pdf->SetColor('text', 0,0,0);
				$pdf->SetFont('times', 'B', 12);

	if(!$blank)
	{
		$pdf->SetXY($checklist_x, $checklist_tab_y+($ch_counter)*$cell_height);
		$checklist[$ch_counter] = 'Detach And Keep this Copy. Complete and check all steps. Payments Journal # '.$pos_payments_journal_id;
		$pdf->Write(0,$checklist[$ch_counter], '',false,'L',0,false,false,0,0,'');
		$ch_counter++;
	
		if(abs($applied_total - $amount) > 0.0001)
		{
		$pdf->SetColor('text', 0,0,0);
					$pdf->SetFont('times', 'B', 14);
		$pdf->SetXY($checklist_x, $checklist_tab_y+($ch_counter)*$cell_height);

		$checklist[$ch_counter] = 'WARNING - APPLIED TOTAL DOES NOT MATCH PAYMENT TOTAL';
		$pdf->Write(0,$checklist[$ch_counter], '',false,'L',0,false,false,0,0,'');
		$ch_counter++;
		$ch_counter++;
		}
	}
	$pdf->SetColor('text', 0,0,0);
	$pdf->SetFont('times', '', 10);
	$pdf->SetXY($checklist_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$pdf->Cell(0.25, $cell_height, '', $table_linestyle, 0, 'R', 0, '', 0, false, 'T', 'T');
	$pdf->SetXY($description_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$checklist[$ch_counter] = 'Verify Media matches Payment Account: Check should be ' .getAccountName($pos_account_id) . ': ' .  getAccountNumber($pos_account_id);
	$pdf->Write(0,$checklist[$ch_counter], '',false,'L',0,false,false,0,0,'');
	$ch_counter++;
	
	
	$pdf->SetXY($checklist_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$pdf->Cell(0.25, $cell_height, '', $table_linestyle, 0, 'R', 0, '', 0, false, 'T', 'T');
	$pdf->SetXY($description_x, $checklist_tab_y+($ch_counter)*$cell_height);
	if($autopay_account_id == 0)
	{
		if($blank)
		{
			$transfer_text = 'Transfer   ________________ From Account ___________________ To Account ' .getAccountName($pos_account_id) . ': ' .  getAccountNumber($pos_account_id);
		}
		else
		{
			$transfer_text = 'Transfer   ' . number_format($amount,2) . ' From Account ___________________ To Account ' .getAccountName($pos_account_id) . ': ' .  getAccountNumber($pos_account_id);
		}
		
	}
	else
	{	
		if($blank)
		{
			$transfer_text = 'Transfer   ________________  From ' .getAccountName($autopay_account_id) . ': ' .  getAccountNumber($autopay_account_id) . ' To ' .getAccountName($pos_account_id) . ': ' .  getAccountNumber($pos_account_id);
		}
		else
		{
			$transfer_text = 'Transfer: ' . number_format($amount,2) . ' From ' .getAccountName($autopay_account_id) . ': ' .  getAccountNumber($autopay_account_id) . ' To ' .getAccountName($pos_account_id) . ': ' .  getAccountNumber($pos_account_id);
		}
	}
	
	$pdf->Write(0,$transfer_text, '',false,'L',0,false,false,0,0,'');
	$ch_counter++;
	
	$pdf->SetXY($checklist_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$pdf->Cell(0.25, $cell_height, '', $table_linestyle, 0, 'R', 0, '', 0, false, 'T', 'T');
	$pdf->SetXY($description_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$checklist[$ch_counter] = 'Verify the Transfer is correct';
	$pdf->Write(0,$checklist[$ch_counter], '',false,'L',0,false,false,0,0,'');
	$ch_counter++;
	
	
	$pdf->SetXY($checklist_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$pdf->Cell(0.25, $cell_height, '', $table_linestyle, 0, 'R', 0, '', 0, false, 'T', 'T');
	$pdf->SetXY($description_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$checklist[$ch_counter] = 'Enter the transfer if Needed into the system - General Journal Transfer $';
	$pdf->Write(0,$checklist[$ch_counter], '',false,'L',0,false,false,0,0,'');
	$ch_counter++;

	if(!$blank)
	{
	$pdf->SetXY($checklist_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$pdf->Cell(0.25, $cell_height, '', $table_linestyle, 0, 'R', 0, '', 0, false, 'T', 'T');
	$pdf->SetXY($description_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$checklist[$ch_counter] = 'Record The Check Number in Paymnets Journal Payment #' . $pos_payments_journal_id .' Comments';
	$pdf->Write(0,$checklist[$ch_counter], '',false,'L',0,false,false,0,0,'');
	$ch_counter++;
	}
		
	$pdf->SetXY($checklist_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$pdf->Cell(0.25, $cell_height, '', $table_linestyle, 0, 'R', 0, '', 0, false, 'T', 'T');
	$pdf->SetXY($description_x, $checklist_tab_y+($ch_counter)*$cell_height);
	$checklist[$ch_counter] = 'File This Check Stub with bank transfer ticket under year/checkStubs/by check number';
	$pdf->Write(0,$checklist[$ch_counter], '',false,'L',0,false,false,0,0,'');
	$ch_counter++;
	
	if(isset($invoice_lookup) && (sizeof($invoice_lookup)> $max_invoices_for_table))
	{
		$pdf->SetXY($checklist_x, $checklist_tab_y+($ch_counter)*$cell_height);
		$pdf->Cell(0.25, $cell_height, '', $table_linestyle, 0, 'R', 0, '', 0, false, 'T', 'T');
		$pdf->SetXY($description_x, $checklist_tab_y+($ch_counter)*$cell_height);
		$checklist[$ch_counter] = 'Attach Printout of Payment # ' . $pos_payments_journal_id;
		$pdf->Write(0,$checklist[$ch_counter], '',false,'L',0,false,false,0,0,'');
		$ch_counter++;
	}

	return $pdf;
	



}
function convert_number_to_words2($number) {
   
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
   
    if (!is_numeric($number)) {
        return false;
    }
   
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
   
    $string = $fraction = null;
   
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
   
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
   
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
   
    return $string;
}
function convert_number_to_money_string($number) {
   
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ' ';
    $negative    = 'negative ';
    $decimal     = ' DOLLARS AND ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
   
    if (!is_numeric($number)) {
        return false;
    }
   
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_money_string(abs($number));
    }
   
    $string = $fraction = null;
   
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
   
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_money_string($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_money_string($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_money_string($remainder);
            }
            break;
    }
   
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
         $string .= $fraction . '/100 CENTS' ;
        /*$words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);*/
    }
   
    return $string;
}
function createPayInvoiceDynamicTableDef($selectable_invoices)	
{

	$select_ids = array();
	$select_names = array();		
	//this is the select values
	for($i=0;$i<sizeof($selectable_invoices);$i++)
	{
		$select_ids[$i]= $selectable_invoices[$i]['pos_purchases_journal_id'];
		$select_names[$i] = 'Sys id: ' . $selectable_invoices[$i]['pos_purchases_journal_id'] . ', Invoice#: ' . $selectable_invoices[$i]['invoice_number'];	
	}
		
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
					/*when the user selects a po we need to load data to the other cells...
					when a user selects an invoice we need to load that data*/
					array('db_field' => 'pos_purchases_journal_id',
						'caption' => 'System ID, <br> Invoice Number',
						'type' => 'select',
						'unique_select_options' => true,
						'select_names' => $select_names,
						'select_values' => $select_ids,
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){updateInvoiceData(this);}',
												/*'onblur' => 'function(){updateSelectOptions();}'*/)
						),
					array('caption' => 'Invoice Amount',
						'db_field' => 'invoice_amount',
						'type' => 'input',
						'element' => 'input',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"'
												)),
					array('caption' => 'Discount Applied',
						'db_field' => 'discount_applied',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"'
												)),
					array('caption' => 'Discount Lost',
						'db_field' => 'discount_lost',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"'
												)),
					array('caption' => 'Credit Memos Applied',
						'db_field' => 'credit_memos_applied',
						'type' => 'input',
						'element' => 'input',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"'
												)),
					array('caption' => 'Invoice Amount<br> Previously Paid <br> Not From This Payment',
						'db_field' => 'applied_amount_from_other_payments',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"')),
					array('caption' => 'Amount Left To Pay',
						'db_field' => 'applied_amount_remaining',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"')),							
												
					array('caption' => 'Invoice Amount To Pay',
							'db_field' => 'applied_amount_from_this_payment',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'validate' => array('dynamic_table_not_zero' => 1, 'acceptable_values' => array('number')),
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'style.backgroundColor' => '"yellow"',
												
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onmouseup' => 'function(){updateTableData(this);}')),
					/* have to remove this as it is returing mutliple results.. something to do with comment in the applied are not unique.... array('caption' => 'Comments',
					'db_field' => 'comments_for_applied',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onmouseup' => 'function(){updateTableData(this);}'))*/
					
				);			
						
		
		return $columns;
	
	
	
}
function createPayExpenseInvoiceDynamicTableDef($selectable_invoices)	
{

	$select_ids = array();
	$select_names = array();		
	//this is the select values
	for($i=0;$i<sizeof($selectable_invoices);$i++)
	{
		$select_ids[$i]= $selectable_invoices[$i]['pos_general_journal_id'];
		$select_names[$i] = 'Sys id: ' . $selectable_invoices[$i]['pos_general_journal_id'] . ', Invoice#: ' . $selectable_invoices[$i]['invoice_number'];	
	}
		
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
					/*when the user selects a po we need to load data to the other cells...
					when a user selects an invoice we need to load that data*/
					array('db_field' => 'pos_general_journal_id',
						'caption' => 'System ID, <br> Invoice Number',
						'type' => 'select',
						'unique_select_options' => true,
						'select_names' => $select_names,
						'select_values' => $select_ids,
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){updateInvoiceData(this);}',
												/*'onblur' => 'function(){updateSelectOptions();}'*/)
						),
					array('caption' => 'Invoice Amount',
						'db_field' => 'invoice_amount',
						'type' => 'input',
						'element' => 'input',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"'
												)),
					array('caption' => 'Discount Applied',
						'db_field' => 'discount_applied',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"'
												)),
					array('caption' => 'Discount Lost',
						'db_field' => 'discount_lost',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"'
												)),
					array('caption' => 'Credit Memos Applied',
						'db_field' => 'credit_memos_applied',
						'type' => 'input',
						'element' => 'input',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"'
												)),
					array('caption' => 'Invoice Amount<br> Previously Paid <br> Not From This Payment',
						'db_field' => 'applied_amount_from_other_payments',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"')),
					array('caption' => 'Amount Left To Pay',
						'db_field' => 'applied_amount_remaining',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												 'tabIndex' => '"-1"')),							
												
					array('caption' => 'Invoice Amount To Pay',
							'db_field' => 'applied_amount_from_this_payment',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'validate' => array('dynamic_table_not_zero' => 1, 'acceptable_values' => array('number')),
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'style.backgroundColor' => '"yellow"',
												
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onmouseup' => 'function(){updateTableData(this);}')),
					array('caption' => 'Comments',
					'db_field' => 'comments_for_applied',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onmouseup' => 'function(){updateTableData(this);}'))
					
				);			
						
		
		return $columns;
	
	
	
}

function createInvoiceToPOApplicationFooter()
{
	
	return array(array('caption' => 'Total',
					'db_field' => 'total',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly = "readonly" onblur="calculateTotals(this)" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				
				);
}





function createAccountBalanceTableDef($type, $key_val_id = 'TBD')
{
if ($type == 'New')
{
	$pos_account_balance_id = 'TBD';
}
else
{
	$pos_account_balance_id = $key_val_id['pos_account_balance_id'];
}

$account_data_table_def = array(
						array( 'db_field' => 'pos_account_balance_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_balance_id,
								'validate' => 'none'),
						array( 'db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Account',
								'html' => createAccountSelect('pos_account_id', 'false')
									),
						array( 'db_field' => 'pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Or Chart of Accounts',
								'html' => createChartOfAccountSelect('pos_chart_of_accounts_id', 'false')
									),
						array( 'db_field' => 'balance_amount',
								'type' => 'input',
								'tags' =>numbersOnly(),
								'validate' => 'number'),
						array('db_field' => 'balance_date',
								'caption' => 'Balance Date',
								'type' => 'date',
								'value' => date('Y-m-d'),
								'tags' => '',
								'html' => dateSelect('balance_date','',''),
								'validate' => 'date'),
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments',
								'validate' => 'none'));	

				
	return $account_data_table_def;
	
}
function createPurchaseOnAccountTableDef($pos_manufacturer_id, $pos_account_id)
{
	$account_def = array(array('db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Purchased Account',
								'html' => createMfgAccountPaymentSelect('pos_account_id', $pos_manufacturer_id, $pos_account_id),
								'validate' => array('select_value' => 'false'))
								);
	return $account_def;
}
function createNewPurchaseJournalTableDef($type, $pos_manufacturer_id, $pos_purchases_journal_id = '')
{
	if ($type == 'New')
	{
		$pos_purchases_journal_id = 'TBD';
		$unique_validate = array('unique_group' => array('invoice_number', 'pos_manufacturer_id'), 'min_length' => 1);
	}


	$select_events = ' onchange="loadPO()" ';
	$date_change = ' onchange="changeDate(\'invoice_date\', \'invoice_due_date\',' . getDays($pos_manufacturer_id) . ')"';
	$data_table_def = array( 
						array( 'db_field' => 'pos_purchases_journal_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Purchases Journal ID',
								'value' => $pos_purchases_journal_id,
								'validate' => 'none'
								),
						array(
								'type' => 'none',
								'caption' => 'Manufacturer',
								'html' =>  getManufacturerName($pos_manufacturer_id) ,
								),
						array('db_field' =>  'invoice_number',
								'type' => 'input',
								'caption' => 'Invoice Number',
								'db_table' => 'pos_purchases_journal', //table is needed!!!
								'validate' => $unique_validate), //key_val is needed for edit

						array('db_field' => 'invoice_status',
								'caption' => 'Invoice Status',
								'type' => 'select',
								'html' => createInvoiceStatusSelect('invoice_status','OPEN')),
						array('db_field' => 'invoice_date',
								'caption' => 'Invoice Date',
								'type' => 'date',
								'value' => '',
								'tags' => $date_change,
								'html' => dateSelect('invoice_date','',$date_change),
								'validate' => 'date'),
								
						array('db_field' => 'invoice_due_date',
								'caption' => 'Invoice Due Date',
								'type' => 'date',
								'value' => '',
								'tags' => '',
								'html' => dateSelect('invoice_due_date', ''),
								'validate' => 'date'),
						array('db_field' =>  'invoice_amount',
								'caption' => 'Invoice Total',
								'type' => 'input',
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),
						array('db_field' =>  'shipping_amount',
								'caption' => 'Shipping Amount',
								'type' => 'input',
								'tags' => positiveNumbersOnly(),
								'validate' => 'number'),
						array('db_field' =>  'show_discount',
								'caption' => 'Show Discount (%)',
								'type' => 'input',
								'value' => 0,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),
						array('db_field' =>  'discount_available',
								'caption' => 'Discount Available',
								'type' => 'input',
								'tags' => positiveNumbersOnly(),
								'validate' => 'number'),
						array('db_field' =>  'discount_applied',
								'caption' => 'Discount to be Applied',
								'type' => 'input',
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateTotal();" ',
								'validate' => 'number'),
						array('db_field' =>  '',
								'caption' => 'Total To Be Paid',
								'type' => 'none',
								'html' => '<input name="total_to_be_paid" id="total_to_be_paid" value="TBD" />',
								),
						array('db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Account Method',
								'html' => createMfgAccountPaymentSelect('pos_account_id', $pos_manufacturer_id, 'false'),
								'validate' => 'none'),
						array('db_field' =>  '',
								'caption' => 'Source File',
								'type' => 'file_input',
								'name' => 'file_name',
								'db_table' => 'pos_purchases_journal',
								'db_id_name' => 'pos_purchase_journal_id',
								'db_id_val' => '',
								'validate' => 'none'),
						array('db_field' =>  'comments',
								'caption' => 'Comments',
								'type' => 'textarea',
								'validate' => 'none')	
						);		
						return $data_table_def;
}
function createPurchaseJournalTableDef($type, $pos_manufacturer_id, $pos_purchases_journal_id)
{
	$pos_account_id = getManufacturerDefaultAccount($pos_manufacturer_id);
	if ($type == 'New')
	{
		$pos_purchases_journal_id = 'TBD';
		$unique_validate = array('unique_group' => array('invoice_number', 'pos_manufacturer_id'), 'min_length' => 1);
		$goods_on_invoice = '';
	}
	else
	{
		$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
		$unique_validate = array('unique_group' => array('invoice_number', 'pos_manufacturer_id'), 'id' => $key_val_id, 'min_length' => 1);
		$invoice_total =  getInvoiceTotal($pos_purchases_journal_id);
		$shipping_total = getShippingOnInvoice($pos_purchases_journal_id);
		$fees_total = getFeesOnInvoice($pos_purchases_journal_id);
		$goods_on_invoice = $invoice_total - $shipping_total - $fees_total;
	}
	


	$select_events = ' onchange="loadPO()" ';
	$date_change = ' onchange="changeDate(\'invoice_date\', \'invoice_due_date\',' . getDays($pos_manufacturer_id) . ')"';
	$data_table_def = array( 
						array( 'db_field' => 'pos_purchases_journal_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Purchases Journal ID',
								'value' => $pos_purchases_journal_id,
								'validate' => 'none'
								),
						array(
								'type' => 'none',
								'caption' => 'Manufacturer',
								'html' =>  getManufacturerName($pos_manufacturer_id) ,
								),
						array('db_field' =>  'invoice_number',
								'type' => 'input',
								'caption' => 'Invoice Number',
								'db_table' => 'pos_purchases_journal', //table is needed!!!
								'validate' => $unique_validate), //key_val is needed for edit

						array('db_field' => 'invoice_status',
								'caption' => 'Invoice Status',
								'type' => 'select',
								'html' => createInvoiceStatusSelect('invoice_status','OPEN')),
						array('db_field' => 'invoice_date',
								'caption' => 'Invoice Date',
								'type' => 'date',
								'value' => '',
								'tags' => $date_change,
								'html' => dateSelect('invoice_date','',$date_change),
								'validate' => 'date'),
								
						array('db_field' => 'invoice_due_date',
								'caption' => 'Invoice Due Date',
								'type' => 'date',
								'value' => '',
								'tags' => '',
								'html' => dateSelect('invoice_due_date', ''),
								'validate' => 'date'),
						array('db_field' =>  '',
								'caption' => 'Total Amount Of Goods On Invoice (Do Not Including Shipping)',
								'type' => 'none',
								'round' => 2,
								'html' => '<input name="goods_amount" value = "'.$goods_on_invoice.'" id="goods_amount"  onkeyup="checkInput(this,\'.0123456789\');updateDiscount();"/>',
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),
						array('db_field' =>  'shipping_amount',
								'caption' => 'Shipping Amount',
								'type' => 'input',
								'round' => 2,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),
						array('db_field' =>  'fee_amount',
								'caption' => 'Additional Fees',
								'type' => 'input',
								'round' => 2,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),
						/*array('db_field' =>  'show_discount',
								'caption' => 'Show Discount (%)',
								'type' => 'input',
								'value' => 0,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),*/
						array('db_field' =>  'invoice_amount',
								'caption' => 'Invoice Total (Total Due Including Shipping)',
								'type' => 'input',
								'round' => 2,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),
						array('db_field' =>  'discount_available',
								'caption' => 'Discount Available',
								'type' => 'input',
								'round' => 2,
								'tags' => positiveNumbersOnly(),
								'validate' => 'number'),
						array('db_field' =>  'discount_applied',
								'caption' => 'Discount to be Applied',
								'type' => 'input',
								'round' => 2,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateTotal();" ',
								'validate' => 'number'),
						
						array('db_field' =>  '',
								'caption' => 'Total To Be Paid',
								'type' => 'none',
								'round' => 2,
								'html' => '<input name="total_to_be_paid" id="total_to_be_paid" value="TBD" />',
								),
						array('db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Account',
								'html' => createMfgAccountPaymentSelect('pos_account_id', $pos_manufacturer_id, $pos_account_id),
								'validate' => 'none'),
						array('db_field' =>  '',
								'caption' => 'Source File',
								'type' => 'file_input',
								'name' => 'file_name',
								'db_table' => 'pos_purchases_journal',
								'db_id_name' => 'pos_purchase_journal_id',
								'db_id_val' => '',
								'validate' => 'none'),
						array('db_field' =>  'comments',
								'caption' => 'Comments',
								'type' => 'textarea',
								'validate' => 'none')	
						);		
						return $data_table_def;
}
function createViewEditPurchaseJournalTableDef($type, $pos_manufacturer_id, $pos_purchases_journal_id)
{
$invoice_total =  getInvoiceTotal($pos_purchases_journal_id);
		$shipping_total = getShippingOnInvoice($pos_purchases_journal_id);
		$fees_total = getfeesOnInvoice($pos_purchases_journal_id);
		$goods_on_invoice = $invoice_total - $shipping_total - $fees_total;


/*if ($pos_account_id != 0)
{
	$account_def = createPurchaseOnAccountTableDef($pos_manufacturer_id, $pos_account_id);
	$data_table_def = array_merge($data_table_def,$account_def);
}*/
	$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
	$unique_validate = array('unique_group' => array('invoice_number', 'pos_manufacturer_id'), 'id' => $key_val_id, 'min_length' => 1);
	$select_events = ' onchange="loadPO()" ';
	$date_change = ' onchange="changeDate(\'invoice_date\', \'invoice_due_date\',' . getDays($pos_manufacturer_id) . ')"';

	$purchases_journal_data = getPurchaseJournalData($pos_purchases_journal_id);
	
	$pos_account_id =  $purchases_journal_data[0]['pos_account_id'];
	//$pos_account_id =  getManufacturerAccount($pos_manufacturer_id);
	$invoice_amount = $purchases_journal_data[0]['invoice_amount'];
	$discount_amount = $purchases_journal_data[0]['discount_applied'];
	$payment_applied = getInvoicePaymentApplied($pos_purchases_journal_id, 'PURCHASES JOURNAL');
	$credit_memos_applied = getCreditMemosAppliedToPurchasesInvoice($pos_purchases_journal_id); 
	$due = $invoice_amount -$discount_amount-$payment_applied-$credit_memos_applied;

	$data_table_def = array( 
						array( 'db_field' => 'pos_purchases_journal_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Purchases Journal ID',
								'value' => $pos_purchases_journal_id,
								'validate' => 'none'
								),
						array(
								'type' => 'none',
								'caption' => 'Manufacturer',
								'html' =>  getManufacturerName($pos_manufacturer_id) ,
								),
						array('db_field' =>  'invoice_number',
								'type' => 'input',
								'caption' => 'Invoice Number',
								'db_table' => 'pos_purchases_journal', //table is needed!!!
								'validate' => $unique_validate), //key_val is needed for edit

						array('db_field' => 'invoice_status',
								'caption' => 'Invoice Status',
								'type' => 'select',
								'html' => createInvoiceStatusSelect('invoice_status','OPEN')),
						array('db_field' => 'invoice_date',
								'caption' => 'Invoice Date',
								'type' => 'date',
								'value' => '',
								'tags' => $date_change,
								'html' => dateSelect('invoice_date','',$date_change),
								'validate' => 'date'),
								
						array('db_field' => 'invoice_due_date',
								'caption' => 'Invoice Due Date',
								'type' => 'date',
								'value' => '',
								'tags' => '',
								'html' => dateSelect('invoice_due_date', ''),
								'validate' => 'date'),
							array('db_field' =>  '',
								'caption' => 'Total Amount Of Goods On Invoice',
								'type' => 'none',
								'round' => 2,
								'html' => $goods_on_invoice,
								'validate' => 'number'),
						
						array('db_field' =>  'shipping_amount',
								'caption' => 'Shipping Amount',
								'type' => 'input',
								'round' =>2,
								'tags' => positiveNumbersOnly(),
								'validate' => 'number'),
						array('db_field' =>  'fee_amount',
								'caption' => 'Additional Fee Amount',
								'type' => 'input',
								'round' =>2,
								'tags' => positiveNumbersOnly(),
								'validate' => 'number'),
						array('db_field' =>  'show_discount',
								'caption' => 'Show Discount (%)',
								'type' => 'input',
								'round' =>2,
								'value' => 0,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),
						array('db_field' =>  'invoice_amount',
								'caption' => 'Invoice Total',
								'type' => 'input',
								'round' =>2,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),
						array('db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Purchased Account',
								'html' => createMfgAccountPaymentSelect('pos_account_id', $pos_manufacturer_id, $pos_account_id),
								'validate' => 'none'),
						array('db_field' =>  'discount_available',
								'caption' => 'Discount Available',
								'type' => 'input',
								'round' =>2,
								'tags' => positiveNumbersOnly(),
								'validate' => 'number'),
						array('db_field' =>  'discount_applied',
								'caption' => 'Discount to be Applied',
								'type' => 'input',
								'round' =>2,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateTotal();" ',
								'validate' => 'number'),
						array('db_field' =>  '',
								'caption' => 'Credit Memos Applied',
								'type' => 'input',
								'round' =>2,
								'value' => $credit_memos_applied,
								'tags' => ' readonly="readonly" ',
								'validate' => 'none'),
						array('db_field' =>  '',
								'caption' => 'Payments Applied',
								'type' => 'input',
								'round' =>2,
								'value' => $payment_applied,
								'tags' => ' readonly="readonly" ',
								'validate' => 'none'),
						array('db_field' =>  '',
								'caption' => 'Total Applied',
								'type' => 'input',
								'round' =>2,
								'value' => $payment_applied+$credit_memos_applied+$discount_amount,
								'tags' => ' readonly="readonly" ',
								'validate' => 'none'),
						array('db_field' =>  '',
								'caption' => 'Payment Due',
								'type' => 'input',
								'value' => $due ,
								'round' =>2,
								'tags' => ' readonly="readonly" ',
								'validate' => 'none',
								),
						array('db_field' => 'payment_status',
								'caption' => 'Payment Status',
								'type' => 'select',
								'html' => createInvoicePaymentStatusSelect('payment_status','UNPAID')),
						array('db_field' =>  '',
								'caption' => 'Source File',
								'type' => 'file_input',
								'name' => 'file_name',
								'db_table' => 'pos_purchases_journal',
								'db_id_name' => 'pos_purchase_journal_id',
								'db_id_val' => '',
								'validate' => 'none'),
						array('db_field' =>  'comments',
								'caption' => 'Comments',
								'type' => 'textarea',
								'validate' => 'none')	
						);
	return $data_table_def;
}
function createCreditMemoPurchaseJournalTableDef($type, $pos_manufacturer_id, $pos_purchases_journal_id)
{
	//$pos_account_id =  getManufacturerAccount($pos_manufacturer_id);
	
	if ($type == 'New')
	{
		$pos_purchases_journal_id = 'TBD';
		$unique_validate = array('unique_group' => array('invoice_number', 'pos_manufacturer_id'), 'min_length' => 1);
	$pos_account_id =  getManufacturerDefaultAccount($pos_manufacturer_id);
	}
	else if ($type == 'View')
	{
		$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
		$unique_validate = array('unique_group' => array('invoice_number', 'pos_manufacturer_id'), 'id' => $key_val_id, 'min_length' => 1);
		$purchases_journal_data = getPurchaseJournalData($pos_purchases_journal_id);
	$pos_account_id =  $purchases_journal_data[0]('pos_account_id');
	}
	else if ($type == 'Edit')
	{
		$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
		$unique_validate = array('unique_group' => array('invoice_number', 'pos_manufacturer_id'), 'id' => $key_val_id, 'min_length' => 1);
		$purchases_journal_data = getPurchaseJournalData($pos_purchases_journal_id);
	$pos_account_id =  $purchases_journal_data[0]['pos_account_id'];
	}
	
	$data_table_def = 	
					array( 
						array( 'db_field' => 'pos_purchases_journal_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Purchases Journal ID',
								'value' => $pos_purchases_journal_id,
								'validate' => 'none'
								),
						array(
								'type' => 'none',
								'caption' => 'Manufacturer',
								'html' =>  getManufacturerName($pos_manufacturer_id) ,
								),
						array('db_field' =>  'invoice_number',
								'type' => 'input',
								'caption' => 'Credit Memo Number',
								'db_table' => 'pos_purchases_journal', //table is needed!!!
								'validate' => $unique_validate), //key_val is needed for edit

						array('db_field' => 'invoice_status',
								'caption' => 'Status',
								'type' => 'select',
								'html' => createInvoiceStatusSelect('invoice_status','OPEN')),
						array('db_field' => 'invoice_date',
								'caption' => 'Credit Memo Date',
								'type' => 'date',
								'value' => '',
								'tags' => '',
								'html' => dateSelect('invoice_date','',''),
								'validate' => 'date'),
								
						array('db_field' => 'credit_memo_used_date',
								'caption' => 'Credit Memo Used Date',
								'type' => 'date',
								'value' => '',
								'separate_date' => 'date',
								'tags' => '',
								'html' => dateSelect('credit_memo_used_date', ''),
								'validate' => 'date'),
						array('db_field' =>  'invoice_amount',
								'caption' => 'Credit Memo Total',
								'type' => 'input',
								'round' => 2,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');updateDiscount();" ',
								'validate' => 'number'),
						array('db_field' => 'payment_status',
								'caption' => 'Credit Memo Status',
								'type' => 'select',
								'html' => createInvoicePaymentStatusSelect('payment_status','UNUSED')),
						array('db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Account',
								'html' => createMfgAccountPaymentSelect('pos_account_id', $pos_manufacturer_id, $pos_account_id),
								'validate' => 'none'),
						array('db_field' =>  'comments',
								'caption' => 'Comments',
								'tags' => ' class="regular_textarea" ',
								'type' => 'textarea'),
						array('db_field' =>  '',
								'caption' => 'Source File',
								'type' => 'file_input',
								'name' => 'file_name',
								'db_table' => 'pos_purchases_journal',
								'db_id_name' => 'pos_purchase_journal_id',
								'db_id_val' => '',
								'validate' => 'none')	
						);

	
					return $data_table_def;
}
function createNewPOToInvoiceTableSelect($pos_manufacturer_id, $pos_purchase_order_id)
{
	$validate = array('multi_select_value' => 'false');
	$validate = 'none';
	
	$multi_select=	array(	array('db_field' => 'pos_purchase_order_id',
								'type' => 'select',
								'caption' => 'Purchase Order Number<br><br>Use Control, Shift, and/or <br>Command To Select Multiple',
								'html' => createPurchaseOrderSelect('pos_purchase_order_id[]', $pos_manufacturer_id, $pos_purchase_order_id, 'off', ' multiple size="15" onclick ="updatePOTotal()" onchange="needToConfirm=true" '),
								'value' => $pos_purchase_order_id,
								'validate' => $validate),
								array('db_field' => 'total_ordered',
									'type' => 'input',
									'caption' => 'Total Ordered On POs'),
								array('db_field' => 'does_not_matter',
									'type' => 'input',
									'caption' => 'Total Received On POs'),
								array('db_field' => 'invoice_applied_total',
									'type' => 'input',
									'caption' => 'Total Invoice Amount Applied To POs'));
	return $multi_select;	
}
function getPurchaseOrderDataFromPurchaseJournalCreditMemo($pos_purchases_journal_id)
{
	$sql = "CREATE TEMPORARY TABLE 
 purchase_orders 
 
  SELECT pos_purchase_orders.pos_purchase_order_id,concat(pos_purchase_orders.pos_purchase_order_id, '  PO#: ' , pos_purchase_orders.purchase_order_number) as po_select_text, pos_purchases_credit_memo_to_po.comments, pos_purchase_orders.purchase_order_number,
 
  pos_purchases_credit_memo_to_po.applied_amount, 
 
   (SELECT COALESCE(sum(pos_purchases_credit_memo_to_po.applied_amount),0) FROM  pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id != $pos_purchases_journal_id AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_credit_memo_to_po.pos_purchase_order_id) as applied_amount_from_other_credit_memos, 


    	(SELECT COALESCE(sum(pos_purchases_credit_memo_to_po.applied_amount),0) FROM  pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_credit_memo_to_po.pos_purchase_order_id) as applied_amount_from_this_credit_memo,
    	
    	

	(SELECT ROUND(sum(cost*(quantity_ordered-quantity_canceled)) - sum(discount*discount_quantity),2) FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as ordered_amount,


	
	
	 (SELECT round(sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)),2) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as received_amount,


	(SELECT ROUND(sum(cost*quantity_returning),2) as returned_amount
	
	 FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as returned_amount	


	  FROM pos_purchase_orders 
			LEFT JOIN pos_purchases_credit_memo_to_po 
			ON pos_purchases_credit_memo_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id
			WHERE pos_purchases_credit_memo_to_po.pos_purchases_journal_id = $pos_purchases_journal_id ORDER BY pos_purchase_orders.pos_purchase_order_id ASC
			;";


	$tmp_select_sql = "SELECT *, ordered_amount - applied_amount_from_other_credit_memos - applied_amount_from_this_credit_memo as applied_amount_remaining FROM purchase_orders";
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	return $data;
	
}
function createCreditMemoPOSelect($pos_manufacturer_id, $pos_purchases_journal_id, $pos_purchase_order_id)
{

	if ($pos_purchase_order_id != '')
	{
		//we sent in a single po value
		$purchase_orders = getAllPurchaseOrderData($pos_purchase_order_id);
	}
	else
	{
		//proabaly an edit or view, so load em up
		$purchase_orders = getPurchaseOrderDataFromPurchaseJournalCreditMemo($pos_purchases_journal_id);
	}

		
	$validate = 'none';
	//po's attached to this invoice
	//other available po's with credit memo requests
	$other_purchase_orders = getPurchaseOrdersWhereCreditMemoRequired($pos_manufacturer_id);	
	$purchase_order_values = array();
	$counter=0;
	$po_total_received=0;
	$po_total_ordered =0;
	$invoice_amount_applied = 0;
	$combined_pos = array();
	for($i=0;$i<sizeof($purchase_orders);$i++)
	{
		$combined_pos[$counter] = $purchase_orders[$i];	
		$counter++;
		$po_total_received = $po_total_received + getTotalReceivedOnPurchaseOrder($purchase_orders[$i]['pos_purchase_order_id']);
		$po_total_ordered = $po_total_ordered  +getTotalOrderedFromPurchaseOrder($purchase_orders[$i]['pos_purchase_order_id']);
		$invoice_amount_applied = $invoice_amount_applied + getPurchaseOrderInvoicesApplied($purchase_orders[$i]['pos_purchase_order_id']);
	}
	for($i=0;$i<sizeof($other_purchase_orders);$i++)
	{
		$bln_found = false;
		for($j=0;$j<sizeof($purchase_orders);$j++)
		{
			if($other_purchase_orders[$i]['pos_purchase_order_id'] == $purchase_orders[$j]['pos_purchase_order_id'])
			{
				$bln_found = true;
			}
		}
		if (!$bln_found)
		{
			$combined_pos[$counter] = $other_purchase_orders[$i];	
			$counter++;
		}
	}



$multi_select =	array(array('db_field' => 'pos_purchase_order_id',
							'type' => 'multi_select',
							'caption' => 'Purchase Order Number<br><br>Use Control, Shift, and/or <br>Command To Select Multiple',
							'html' =>createPurchaseOrderSelectWithPOArray('pos_purchase_order_id[]',  $combined_pos, $purchase_orders, 'off',  ' multiple size="15" onclick ="updatePOTotal()" onchange="needToConfirm=true" ') ,
							'validate' => $validate));
					return $multi_select;
}
function createPurchaseOrderSelect($name,  $pos_manufacturer_id, $pos_purchase_order_id, $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	//$purchase_orders = getOpenMFGPurchaseOrders($pos_manufacturer_id);
	$purchase_orders = getPurchaseOrdersWithIncompleteInvoices($pos_manufacturer_id);
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Purchase Order</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_purchase_order_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Purchase Order\'s</option>';
	}
	for($i = 0;$i < sizeof($purchase_orders); $i++)
	{
		$html .= '<option value="' . $purchase_orders[$i]['pos_purchase_order_id'] . '"';
		
		if ( ($purchase_orders[$i]['pos_purchase_order_id'] == $pos_purchase_order_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>System PO#: ' . $purchase_orders[$i]['pos_purchase_order_id'] . ' Custom po#: ' .$purchase_orders[$i]['purchase_order_number'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getPurchaseOrdersWithIncompleteInvoices($pos_manufacturer_id)
{
	//we need to know what purchase orders are available to receive an invoice.
	//Purchase orders that can be invoiced have a ordered total - canceled total <> invoice total
	//Purchase orders that are closed to invoices have the ordered total - canceled total = invoice total
	// or is it received total?
	
			
			$sql = "SELECT pos_purchase_orders.pos_purchase_order_id, pos_purchase_orders.invoice_status, 
					pos_purchase_orders.invoice_amount_applied, pos_purchase_orders.purchase_order_number,
					
			
					
					
					 (SELECT sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as received_total,
					
					
					
					
					(SELECT SUM(cost*(quantity_ordered-quantity_canceled)) - SUM(discount*discount_quantity) FROM pos_purchase_order_contents WHERE pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as ordered_total
					FROM pos_purchase_orders
					LEFT JOIN pos_manufacturer_brands 
					ON pos_manufacturer_brands.pos_manufacturer_brand_id = pos_purchase_orders.pos_manufacturer_brand_id
					LEFT JOIN pos_manufacturers
					ON pos_manufacturers.pos_manufacturer_id = pos_manufacturer_brands.pos_manufacturer_id
					
					WHERE pos_purchase_orders.invoice_status = 'INCOMPLETE'
					AND (pos_purchase_orders.purchase_order_status = 'OPEN' OR pos_purchase_orders.purchase_order_status = 'CLOSED')
					AND pos_manufacturers.pos_manufacturer_id = $pos_manufacturer_id
				";
				
			return getSQL($sql);
}
function createPurchaseOrderSelectWithPOArray($name,  $purchase_orders, $selected_purchase_orders, $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Purchase Order</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		$html .= '>All Purchase Order\'s</option>';
	}
	for($i = 0;$i < sizeof($purchase_orders); $i++)
	{
		$html .= '<option value="' . $purchase_orders[$i]['pos_purchase_order_id'] . '"';
		for($k=0;$k<sizeof($selected_purchase_orders);$k++)
		{
			if ( ($purchase_orders[$i]['pos_purchase_order_id'] == $selected_purchase_orders[$k]['pos_purchase_order_id']) ) 
			{
				$html .= ' selected="selected"';
			}
		}
		$html .= '>System PO#: ' . $purchase_orders[$i]['pos_purchase_order_id'] . ' Custom po#: ' .$purchase_orders[$i]['purchase_order_number'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createPurchaseOrderCreditMemoSelect($name,  $pos_manufacturer_id, $pos_purchase_order_id, $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	//$purchase_orders = getOpenMFGPurchaseOrders($pos_manufacturer_id);
	
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Purchase Order</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_purchase_order_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Purchase Order\'s</option>';
	}
	for($i = 0;$i < sizeof($purchase_orders); $i++)
	{
		$html .= '<option value="' . $purchase_orders[$i]['pos_purchase_order_id'] . '"';
		
		if ( ($purchase_orders[$i]['pos_purchase_order_id'] == $pos_purchase_order_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>System PO#: ' . $purchase_orders[$i]['pos_purchase_order_id'] . ' Custom po#: ' .$purchase_orders[$i]['purchase_order_number'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getPurchaseOrderNumber($pos_purchase_order_id)
{
	$sql = "SELECT purchase_order_number from pos_purchase_orders WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function createGeneralJournalExpenseEntryTableDef($type, $key_val_id = 'TBD')
{
if ($type == 'New')
{
	$pos_general_journal_id = 'TBD';
	$expense_category_validate = array('select_value' => 'false');
	$entry_type = 'new';
}
else
{
	$pos_general_journal_id = $key_val_id['pos_general_journal_id'];
	$expense_category_validate = 'none';
	$entry_type = getSingleValueSQL("SELECT entry_type FROM pos_general_journal WHERE pos_general_journal_id = $pos_general_journal_id");
}

$table_def = array( 
						array( 'db_field' => 'pos_general_journal_id',
								'type' => 'input',
								'caption' => 'System PO ID',
								'value' => $pos_general_journal_id,
								'tags' => ' readonly="readonly" '
									),
						array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),
						array('db_field' => 'pos_employee_id',
								'caption' => 'Employee',
								'type' => 'select',
								'html' => createEmployeeSelect('pos_employee_id', $_SESSION['pos_employee_id'],  'off'),
								'value' => $_SESSION['pos_employee_id'],
								'validate' => 'false'),
						array('db_field' => 'invoice_number',
								'type' => 'input',
								'caption' => 'invoice_number'),
						array('db_field' => 'invoice_date',
								'caption' => 'Receipt Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('invoice_date',''),
								'validate' => 'date'),
						array('db_field' =>  'entry_amount',
								'caption' => 'Amount',
								'round' =>2,
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						
						array('db_field' => 'pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Expense Category',
								'html' => createChartOfAccountsExpenseCategorySelect('pos_chart_of_accounts_id', 'false'), 
								'validate' => $expense_category_validate),
						array('db_field' => 'supplier',
								'type' => 'input',
								'caption' => 'Supplier',
								'validate' => array('min_length' => 1)),
						array('db_field' => 'description',
								'type' => 'input',
								'caption' => 'Description'),
						array('db_field' => 'use_tax',
								'caption' => 'Use Tax',
								'type' => 'input',
								'validate' => 'number'),
						
						array('db_field' => 'comments',
								'type' => 'textarea',
								'caption' => 'Comments'),
						array('db_field' =>  '',
								'caption' => 'Source File',
								'type' => 'file_input',
								'name' => 'file_name',
								'db_table' => 'pos_general_journal',
								'db_id_name' => 'pos_general_journal_id',
								'db_id_val' => '',
								'validate' => 'none')
								
						);
						
	if ($type != 'New' && $entry_type != 'Receipt')
	//if(true)
	{
		$entry_type = array(
							array('db_field' => 'invoice_status',
								'type' => 'select',
								'caption' => 'Status',
								'html' => getGeneralJournalInvoiceStatusSelect('invoice_status', 'false'), 
								'validate' => 'none'),
							/*array('db_field' => 'payments_applied',
								'round' =>2,
								'Caption' => 'Payments Applied',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'number'),*/
							array('db_field' =>  'payment_status',
								'type' => 'select',
								'caption' => 'Payment Status',
								'html' => createEnumSelect('payment_status','pos_general_journal', 'payment_status', 'false',  'off')),
								array('db_field' =>  'entry_type',
								'type' => 'select',
								'caption' => 'Entry Type',
								'html' => createEnumSelect('entry_type','pos_general_journal', 'entry_type', 'false',  'off')),
							array('db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Account',
								'html' => createAccountSelect('pos_account_id', 'false'),
								'validate' => array('select_value' => 'false')));
		$table_def = array_merge($table_def, $entry_type);
	}
	
return $table_def;
}
function createPaymentRecordTable($pos_journal_id, $journal)
{
	if($journal == 'GENERAL JOURNAL')
	{
		$sql = "

SELECT pos_invoice_to_payment.applied_amount, pos_payments_journal.pos_payments_journal_id,  pos_payments_journal.payment_date, pos_accounts.company, pos_payments_journal.payment_amount
FROM pos_payments_journal
LEFT JOIN pos_accounts USING (pos_account_id)
LEFT JOIN pos_invoice_to_payment USING (pos_payments_journal_id)
LEFT JOIN pos_general_journal ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL' AND pos_general_journal.pos_general_journal_id = $pos_journal_id

";
	}
	else if ($journal == 'PURCHASES JOURNAL')
	{
			$sql = "

SELECT pos_invoice_to_payment.applied_amount,pos_payments_journal.pos_payments_journal_id,  pos_payments_journal.payment_date, pos_accounts.company, pos_payments_journal.payment_amount
FROM pos_payments_journal
LEFT JOIN pos_accounts USING (pos_account_id)
LEFT JOIN pos_invoice_to_payment USING (pos_payments_journal_id)
LEFT JOIN pos_purchases_journal ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
WHERE pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL' AND pos_purchases_journal.pos_purchases_journal_id = $pos_journal_id

";
	}
	else
	{
	}

	$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_payments_journal_id',
			'get_url_link' => "../PaymentsJournal/view_payments_journal_entry.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_payments_journal_id'),
		array(
			'th' => 'Payment ID',
			'mysql_field' => 'pos_payments_journal_id'),
		array(
			'th' => 'Date',
			'mysql_field' => 'payment_date'),
		array(
			'th' => 'Account',
			'mysql_field' => 'company'),
		array(
			'th' => 'Payment Amount',
			'mysql_field' => 'payment_amount',
			'round' => 2),
		array(
			'th' => 'Payment Applied Amount',
			'mysql_field' => 'applied_amount',
			'round' => 2,
			'total' => 2)
		);
	$data = getSQL($sql);
	return createRecordsTableWithTotals($data, $table_columns);
		
}
function createJournalRecordTable($pos_payments_journal_id)
{

		$tmp_sql = "
	
			CREATE TEMPORARY TABLE journals

			SELECT source_journal, applied_amount, pos_journal_id,
			IF(source_journal='PURCHASES JOURNAL', pos_purchases_journal.invoice_number, pos_general_journal.description) as description,
			IF(source_journal='PURCHASES JOURNAL', pos_manufacturers.company, pos_general_journal.supplier) as supplier
			
			FROM pos_invoice_to_payment
			LEFT JOIN pos_purchases_journal 
			ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
			LEFT JOIN pos_manufacturers	ON pos_manufacturers.pos_manufacturer_id = pos_purchases_journal.pos_manufacturer_id
			LEFT JOIN pos_general_journal
			ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
			 WHERE pos_payments_journal_id = $pos_payments_journal_id
			
			;";
				
		$tmp_select_sql = "SELECT * FROM journals WHERE 1";			
		$table_columns = array(
			array( 'th' => 'View',
			'mysql_field' => 'pos_journal_id',
			'variable_get_url_link' => array(
				'row_result_lookup' => 'source_journal',
				"PURCHASES JOURNAL" => array(
							'url' => POS_ENGINE_URL.'/accounting/PurchaseJournal/view_purchase_invoice_to_journal.php', 
							'get_data' => array('pos_purchases_journal_id'=>'pos_journal_id')),				 
				"GENERAL JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/GeneralJournal/view_general_journal_entry.php',
							'get_data' => array('pos_general_journal_id' => 'pos_journal_id'))),
			'url_caption' => 'View',),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_journal_id'),
		array(
			'th' => 'Source Journal',
			'mysql_field' => 'source_journal'),
		array(
			'th' => 'Supplier',
			'mysql_field' => 'supplier'),
		array(
			'th' => 'Description',
			'mysql_field' => 'description'),
		array(
			'th' => 'Applied Amount',
			'mysql_field' => 'applied_amount',
			'sort' => 'applied_amount',
			'round' => 2,
			'total' => 2),
	
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html = createRecordsTableWithTotals($data, $table_columns);
	return $html;



}
function createPaymentsJournalRecordTableForPurchases($pos_payments_journal_id)
{
	//want invoice amount, discount applied, discount lost, credit memos applied, invoice amount not from this invoice, amount left to pay, payment amount, comments, invoice number
	
				

	$table_columns = array(
			/*array( 'th' => 'View',
			'mysql_field' => 'pos_journal_id',
			'variable_get_url_link' => array(
				'row_result_lookup' => 'source_journal',
				"PURCHASES JOURNAL" => array(
							'url' => POS_ENGINE_URL.'/accounting/PurchaseJournal/view_purchase_invoice_to_journal.php', 
							'get_data' => array('pos_purchases_journal_id'=>'pos_journal_id')),				 
				"GENERAL JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/GeneralJournal/view_general_journal_entry.php',
							'get_data' => array('pos_general_journal_id' => 'pos_journal_id'))),
			'url_caption' => 'View',),*/
		array(
			'th' => 'View',
			'mysql_field' => 'pos_purchases_journal_id',
			'get_url_link' => "../PurchaseJournal/view_purchase_invoice_to_journal.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_purchases_journal_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_purchases_journal_id'),
		array(
			'th' => 'Invoice Number',
			'mysql_field' => 'invoice_number',
			'sort' => 'invoice_number'),
		
		array(
			'th' => 'Invoice Amount',
			'mysql_field' => 'invoice_amount',
			'sort' => 'invoice_amount',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Discount Applied',
			'mysql_field' => 'discount_applied',
			'sort' => 'discount_applied',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Discount Lost',
			'mysql_field' => 'discount_lost',
			'sort' => 'discount_lost',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Credit Memos Applied',
			'mysql_field' => 'credit_memos_applied',
			'sort' => 'credit_memos_applied',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Invoice Amount<br>Previously Paid<br>Not From This Payment',
			'mysql_field' => 'applied_amount_from_other_payments',
			'sort' => 'applied_amount_from_other_payments',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Amount<br>Left To<br>Pay',
			'mysql_field' => 'applied_amount_remaining',
			'sort' => 'applied_amount_remaining',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Payment<br>Amount',
			'mysql_field' => 'applied_amount_from_this_payment',
			'sort' => 'applied_amount_from_this_payment',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments_for_applied',
			'sort' => 'comments_for_applied')
		
		);
		
		

	$data = getPurchasesInvoicesLinkedToPayment($pos_payments_journal_id);
	$html = createRecordsTableWithTotals($data, $table_columns);
	return $html;



}
function createCreditMemoTableDef($pos_purchases_journal_id, $pos_manufacturer_id)
{
	$date_change = '';
		$pos_account_id = getManufacturerDefaultAccount($pos_manufacturer_id);

	return array( 
						array( 'db_field' => 'pos_purchases_journal_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Purchases Journal ID',
								'value' => $pos_purchases_journal_id,
								'validate' => 'none'
								),
						array(
								'type' => 'none',
								'caption' => 'Manufacturer',
								'html' =>  getManufacturerName($pos_manufacturer_id) ,
								),
						array('db_field' =>  'invoice_number',
								'type' => 'input',
								'caption' => 'Credit Memo Number',
								'db_table' => 'pos_purchases_journal', //table is needed!!!
								'validate' => array('unique_group' => array('invoice_number', 'pos_manufacturer_id'), 'min_length' => 1)),
						/*array('db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Credit Account',
								'html' => createMfgInvoicePaymentSelect('pos_account_id', $pos_manufacturer_id, 'false'),
								'validate' => 'none'),*/

						array('db_field' => 'invoice_date',
								'caption' => 'Credit Memo Date',
								'type' => 'date',
								'value' => '',
								'tags' => $date_change,
								'html' => dateSelect('invoice_date','',$date_change),
								'validate' => 'date'),
						array('db_field' =>  'invoice_amount',
								'caption' => 'Credit Total',
								'type' => 'input',
								'tags' => positiveNumbersOnly(),
								'validate' => 'number'),
						array('db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Account',
								'html' => createMfgAccountPaymentSelect('pos_account_id', $pos_manufacturer_id, $pos_account_id),
								'validate' => 'none'),
						array('db_field' =>  '',
								'caption' => 'Source File',
								'type' => 'file_input',
								'name' => 'file_name',
								'db_table' => 'pos_purchases_journal',
								'db_id_name' => 'pos_purchase_journal_id',
								'db_id_val' => '',
								'validate' => 'none')
								
						);
}

function createPurchaseOrderRecordTable($pos_purchases_journal_id)
{
	$tmp_sql = "
	
			CREATE TEMPORARY TABLE purchase_orders

			SELECT pos_purchase_orders.pos_purchase_order_id, pos_purchases_invoice_to_po.applied_amount, 
			purchase_order_number, purchase_order_status, received_status, invoice_status, po_title,
			(SELECT ROUND(sum((cost-discount)*(quantity_ordered-quantity_canceled)),2) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as total_ordered,
			
			(SELECT sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as total_received,
			
			
			(SELECT ROUND(sum(applied_amount),2) FROM pos_purchases_invoice_to_po WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as total_applied
			FROM pos_purchase_orders
			LEFT JOIN pos_purchases_invoice_to_po USING (pos_purchase_order_id)
			WHERE pos_purchases_invoice_to_po.pos_purchases_journal_id = $pos_purchases_journal_id
			;";
				
	$tmp_select_sql = "SELECT *, total_ordered - total_applied as remaining_amount FROM purchase_orders WHERE 1";			
		$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_purchase_order_id',
			'get_url_link' => POS_ENGINE_URL . "/purchase_orders/ViewPurchaseOrder/view_purchase_order.php",
			'url_caption' => 'view',
			'get_id_link' => 'pos_purchase_order_id'),
		array(
			'th' => 'System ID',
			'mysql_field' => 'pos_purchase_order_id'),
		array(
			'th' => 'Purchase Order Number',
			'mysql_field' => 'purchase_order_number',
			'sort' => 'purchase_order_number'),
		array(
			'th' => 'Purchase Order Status',
			'mysql_field' => 'purchase_order_status',
			'sort' => 'purchase_order_status'),	
		array(
			'th' => 'Purchase Order <br> Receive Status',
			'mysql_field' => 'received_status',
			'sort' => 'received_status'),
		array(
			'th' => 'Purchase Order <br> Invoice Status',
			'mysql_field' => 'invoice_status',
			'sort' => 'invoice_status'),	
		array(
			'th' => 'Amount Applied',
			'mysql_field' => 'applied_amount',
			'sort' => 'applied_amount',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Total Ordered',
			'mysql_field' => 'total_ordered',
			'sort' => 'total_ordered',
			'round' => 2,
			'total' => 2),
	array(
			'th' => 'Total Applied',
			'mysql_field' => 'total_applied',
			'sort' => 'total_applied',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Total Received',
			'mysql_field' => 'total_received',
			'sort' => 'total_received',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Amount Remaining<br>To Apply',
			'mysql_field' => 'remaining_amount',
			'sort' => 'remaining_amount',
			'round' => 2,
			'total' => 2),
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html = createRecordsTableWithTotals($data, $table_columns);
	return $html;
}
function createPurchaseOrderRecordTableLinkedToCreditMemo($pos_purchases_journal_id)
{
	$tmp_sql = "
	
			CREATE TEMPORARY TABLE purchase_orders

			SELECT pos_purchase_orders.pos_purchase_order_id, pos_purchases_credit_memo_to_po.applied_amount, 
			purchase_order_number, purchase_order_status, received_status, invoice_status, po_title,
			(SELECT ROUND(sum(cost*(quantity_ordered-quantity_canceled)) - sum(discount*discount_quantity),2) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as total_ordered,
			(SELECT sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as total_received
			FROM pos_purchase_orders
			LEFT JOIN pos_purchases_credit_memo_to_po USING (pos_purchase_order_id)
			WHERE pos_purchases_credit_memo_to_po.pos_purchases_journal_id = $pos_purchases_journal_id
			;";
				
	$tmp_select_sql = "SELECT * FROM purchase_orders WHERE 1";			
		$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_purchase_order_id',
			'get_url_link' => POS_ENGINE_URL . "/purchase_orders/ViewPurchaseOrder/view_purchase_order.php",
			'url_caption' => 'view',
			'get_id_link' => 'pos_purchase_order_id'),
		array(
			'th' => 'System ID',
			'mysql_field' => 'pos_purchase_order_id'),
		array(
			'th' => 'Purchase Order Number',
			'mysql_field' => 'purchase_order_number',
			'sort' => 'purchase_order_number'),
		array(
			'th' => 'Purchase Order Status',
			'mysql_field' => 'purchase_order_status',
			'sort' => 'purchase_order_status'),	
		array(
			'th' => 'Purchase Order <br> Receive Status',
			'mysql_field' => 'received_status',
			'sort' => 'received_status'),
		array(
			'th' => 'Purchase Order <br> Invoice Status',
			'mysql_field' => 'invoice_status',
			'sort' => 'invoice_status'),	
		array(
			'th' => 'Amount Applied',
			'mysql_field' => 'applied_amount',
			'sort' => 'applied_amount',
			'round' => 2,
			'total' => 2),

		array(
			'th' => 'Total Ordered',
			'mysql_field' => 'total_ordered',
			'sort' => 'total_ordered',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Total Received',
			'mysql_field' => 'total_received',
			'sort' => 'total_received',
			'round' => 2,
			'total' => 2),
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html = createRecordsTableWithTotals($data, $table_columns);
	return $html;
}
function createCreditMemoRecordTable($pos_purchases_journal_id)
{
	
	$sql = "

SELECT pos_purchases_journal_credit_memo_id as credit_memo_id,  applied_amount 
FROM pos_invoice_to_credit_memo
WHERE pos_purchases_journal_invoice_id = '$pos_purchases_journal_id' 

";

	$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'credit_memo_id',
			'get_url_link' => "view_purchase_invoice_to_journal.php",
			'url_caption' => 'view',
			'get_id_link' => 'pos_purchases_journal_id'),
		array(
			'th' => 'Purchases Journal ID',
			'mysql_field' => 'credit_memo_id'),
		
		array(
			'th' => 'Credit Amount Applied',
			'mysql_field' => 'applied_amount',
			'round' => 2)
		);
		$data = getSQL($sql);
	return createRecordsTable($data, $table_columns,'generalTable');
		
}
function createCreditMemoUsedTable($pos_purchases_journal_credit_memo_id)
{
	
	$sql = "

SELECT pos_purchases_journal_credit_memo_id, pos_purchases_journal_invoice_id as pos_purchases_journal_id,  applied_amount 
FROM pos_invoice_to_credit_memo
WHERE pos_purchases_journal_credit_memo_id = '$pos_purchases_journal_credit_memo_id' 

";

	$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_purchases_journal_id',
			'get_url_link' => "view_purchase_invoice_to_journal.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_purchases_journal_id'),
		array(
			'th' => 'Purchases Journal ID',
			'mysql_field' => 'pos_purchases_journal_id'),
		
		array(
			'th' => 'Credit Amount Applied',
			'mysql_field' => 'applied_amount',
			'round' => 2,
			'total' => 2),

		);
		$data = getSQL($sql);
	return createRecordsTableWithTotals($data, $table_columns);
		
}
function createPaymentEntryTableDef($type = 'none', $key_val_id = 'TBD', $pos_account_id = 'false')
{
	if ($type == 'New')
	{
		$pos_payments_journal_id = 'TBD';
	}
	else
	{
		$pos_payments_journal_id = $key_val_id['pos_payments_journal_id'];
	}
$table_def = array(	
	array( 'db_field' => 'pos_payments_journal_id',
					'type' => 'input',
					'caption' => 'System PO ID',
					'value' => $pos_payments_journal_id,
					'tags' => ' readonly="readonly" '
						),array('db_field' => 'pos_account_id',
					'type' => 'select',
					'caption' => 'Payment Method',
					'html' => createExpensePaymentSelect('pos_account_id', $pos_account_id),
					'validate' => array('select_value' => 'false'))
					);
	return $table_def;
}
function createPaymentEntryTableDefwDate($type = 'none', $key_val_id = 'TBD', $pos_account_id = 'false')
{
	if ($type == 'New')
	{
		$pos_payments_journal_id = 'TBD';
	}
	else
	{
		$pos_payments_journal_id = $key_val_id['pos_payments_journal_id'];
	}
$table_def = array(	
	array( 'db_field' => 'pos_payments_journal_id',
					'type' => 'input',
					'caption' => 'System PO ID',
					'value' => $pos_payments_journal_id,
					'tags' => ' readonly="readonly" '
						),array('db_field' => 'pos_payment_account_id',
					'type' => 'select',
					'caption' => 'Payment Method',
					'html' => createExpensePaymentSelect('pos_payment_account_id', $pos_account_id),
					'validate' => array('select_value' => 'false')),
					array('db_field' => 'payment_date',
								'caption' => 'Payment Date',
								'type' => 'date',
								'value' => date('Y-m-d'),
								'tags' => '',
								'html' => dateSelect('payment_date','',''),
								'validate' => 'date')
					);
	return $table_def;
}							
function createPurchasePaymentEntryTableDef($type = 'none', $key_val_id = 'TBD', $pos_manufacturer_id)
{
	if ($type == 'New')
	{
		$pos_payments_journal_id = 'TBD';
	}
	else
	{
		$pos_payments_journal_id = $key_val_id['pos_payments_journal_id'];
	}
	$table_def = array(	
	array( 'db_field' => 'pos_payments_journal_id',
					'type' => 'input',
					'caption' => 'System PO ID',
					'value' => $pos_payments_journal_id,
					'tags' => ' readonly="readonly" '
						),array('db_field' => 'pos_account_id',
					'type' => 'select',
					'caption' => 'Payment Method',
					'html' => createMfgInvoicePaymentSelect('payment_account_id', $pos_manufacturer_id, 'false'),
					'validate' => array('select_value' => 'false'))
					);
	return $table_def;
}
function createPurchaseOnAccountPaymentEntryTableDef($pos_manufacturer_id)
{

$table_def = array(	
	array('db_field' => 'pos_account_id',
					'type' => 'select',
					'caption' => 'Payment Method',
					'html' => createMfgAccountPaymentSelect('pos_account_id', $pos_manufacturer_id, 'false'),
					'validate' => array('select_value' => 'false'))
					);
	return $table_def;
}
function createPaymentsJournalTableDef($type = 'none', $pos_payments_journal_id = 'TBD')
{
	if ($type == 'New')
	{
		$pos_payments_journal_id = 'TBD';
	}
	else
	{
		//$pos_payments_journal_id = $key_val_id['pos_payments_journal_id'];
	}
$table_def = array(	
	array( 'db_field' => 'pos_payments_journal_id',
					'type' => 'input',
					'caption' => 'System PO ID',
					'value' => $pos_payments_journal_id,
					'tags' => ' readonly="readonly" '
						),
							array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),
					array('db_field' => 'pos_employee_id',
								'caption' => 'Employee',
								'type' => 'select',
								'html' => createEmployeeSelect('pos_employee_id', $_SESSION['pos_employee_id'],  'off'),
								'value' => $_SESSION['pos_employee_id'],
								'validate' => 'false'),
					array('db_field' => 'pos_account_id',
					'type' => 'select',
					'caption' => 'Payment Using',
					'html' => createAccountSelect('pos_account_id', 'false'),
					'validate' => array('select_value' => 'false')),
					array('db_field' => 'pos_payee_account_id',
					'type' => 'select',
					'caption' => 'Payment To',
					'html' => createAccountSelect('pos_payee_account_id', 'false'),
					'validate' => 'none'),
				
						
						array('db_field' => 'payment_date',
								'caption' => 'Payment Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('payment_date',''),
								'validate' => 'date'),
						array('db_field' =>  'payment_amount',
								'caption' => 'Amount',
								'round' =>2,
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						array('db_field' => 'applied_status',
								'caption' => 'Applied Status',
								'type' => 'select',
								'html' => appliedStatusSelect('applied_status','false'),
								'validate' => 'none'),
						array('db_field' => 'payment_status',
								'caption' => 'Payment Status',
								'type' => 'select',
								'html' => paymentStatusSelect('payment_status','false'),
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'caption' => 'Comments'),
						array('db_field' =>  '',
								'caption' => 'Source File',
								'type' => 'file_input',
								'name' => 'file_name',
								'db_table' => 'pos_general_journal',
								'db_id_name' => 'pos_general_journal_id',
								'db_id_val' => '',
								'validate' => 'none')
								
					);
	return $table_def;
}
function getPurchaseOrdersWithIncompleteInvoicesNotIncludingInvoice($pos_purchases_journal_id, $pos_manufacturer_id)
{


//to get a list of po's I need the incomplete invoices AND the invoices linked to the PJ
//this is the incomplete invoices, so they should not include invoices linked to the PJ

//this will give a list of purchase orders that are open or closed with incomplete invoice status
//this will ignore po's associated with the given invoice
				
//we need to know the total amount applied to invoices, the total ordered, the total received, the remaining amount to be applied the total canceled
	//ordered_amoount
	//received_amount
	//applied_amount
	//applied_amount_remaining
	//the invoice is going to be for the sum of the ordered - cancled 
	// received amount is for info only
	
	
	
	$tmp_sql = "CREATE TEMPORARY TABLE 
 purchase_orders 
 
 SELECT pos_purchase_orders.pos_purchase_order_id,pos_purchase_orders.purchase_order_number,pos_purchases_invoice_to_po.comments as comments_for_applied,
    (SELECT COALESCE(sum(pos_purchases_invoice_to_po.applied_amount),0) FROM  pos_purchases_invoice_to_po WHERE pos_purchases_journal_id != '$pos_purchases_journal_id' AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_invoice_to_po.pos_purchase_order_id) as applied_amount_from_other_invoices, 
    
    	(SELECT COALESCE(sum(pos_purchases_invoice_to_po.applied_amount),0) FROM  pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = '$pos_purchases_journal_id' AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_invoice_to_po.pos_purchase_order_id) as applied_amount_from_this_invoice,
 
 (SELECT ROUND(sum(cost*(quantity_ordered-quantity_canceled)) ,2) FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as ordered_amount,
 
 (SELECT ROUND(sum(discount*discount_quantity),2) FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as discount_amount,
 

 
 (SELECT round(sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)),2) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as received_amount
			 
			 
 
FROM pos_purchase_orders 
LEFT JOIN pos_purchases_invoice_to_po 
ON pos_purchases_invoice_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id
LEFT JOIN pos_manufacturer_brands 
ON pos_manufacturer_brands.pos_manufacturer_brand_id = pos_purchase_orders.pos_manufacturer_brand_id
LEFT JOIN pos_manufacturers
ON pos_manufacturers.pos_manufacturer_id = pos_manufacturer_brands.pos_manufacturer_id
WHERE 

 pos_purchase_orders.pos_purchase_order_id NOT IN (SELECT pos_purchase_order_id FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = '$pos_purchases_journal_id')
AND
pos_purchase_orders.invoice_status = 'INCOMPLETE'
AND (pos_purchase_orders.purchase_order_status = 'OPEN' OR pos_purchase_orders.purchase_order_status = 'CLOSED')
AND pos_manufacturers.pos_manufacturer_id = $pos_manufacturer_id
ORDER BY pos_purchase_orders.pos_purchase_order_id ASC
			
			;";
			
	
$tmp_select_sql = "SELECT DISTINCT *, ordered_amount - discount_amount - applied_amount_from_other_invoices - applied_amount_from_this_invoice as applied_amount_remaining FROM purchase_orders";$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}

function createGeneralLedgerEntry($entry_array)
{
	//this is going to put the data into the general ledger:
	// account, sub account, credit, debit, journal, journal id
	/*
		ex 1: enter a purchase invoice:
		array(array('pos_chart_of_account_id' => accounts payable, 'pos_account_id' => chantelle 'credit' => 1000, 'journal' => purchases journal, 'id' => id), array('pos_chart_of_account_id' => asset inventory, 'pos_account_id' => x 'debit' => 1000, 'journal' => purchases journal, 'id' => id))
		
		ex2: pay the invoice with a discount:
		array(array('pos_chart_of_account_id' => accounts payable, 'pos_account_id' => chantelle 'credit' => 1000, 'journal' => purchases journal, 'id' => id), array('pos_chart_of_account_id' => asset inventory, 'pos_account_id' => x 'debit' => 1000, 'journal' => purchases journal, 'id' => id))
	
	*/
	
}

function checkAccountLockDate($pos_account_id, $entry_date)
{
	$sql = "SELECT DATE(verification_lock_date) FROM pos_accounts WHERE pos_account_id = $pos_account_id";
	$lock_date = getSingleValueSQL($sql);
	if(strtotime($entry_date) < strtotime($lock_date))
	{
		//error
		$msg = 'Error - Entry is trying to be added before lock date of ' . $lock_date .' to account ' . getAccountName($pos_account_id) . '. Assume the account has been verified and then a lock date has been set to prevent wrong entry.... check the account listing to see if you are re-entering a receipt... which happens often. Eventually I will add this error to javascript verification.' ;
		//trigger_error($msg);
		include(HEADER_FILE);
		echo $msg;
		include(FOOTER_FILE);
		exit();
		
	}
	else
	{
	}
	
}

?>