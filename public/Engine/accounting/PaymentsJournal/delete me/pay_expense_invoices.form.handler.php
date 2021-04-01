<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'Payments Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');

if (isset($_POST['submit'])) 
{
	// adding the invoice means we are recording it but not paying it.
	// this is not an accounting event until it is paid
	$results= array();

	$date = date('Y:m:d H:i:s');	
	$dbc = startTransaction();							
	
	
	$payment_satus = (strtotime($_POST['payment_date']) <= strtotime(date('Y-m-d'))) ? 'COMPLETE' : 'SCHEDULED';
	$payment_insert_array = array('pos_store_id' => $_SESSION['store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'pos_account_id' => $_POST['pos_account_id'],
									'pos_payee_account_id' => $_POST['pos_payee_account_id'],
									'payment_date' => $_POST['payment_date'],
									'payment_entry_date' =>getCurrentTime(),
									'payment_status' => $payment_satus,
									'pos_user_id' => $_SESSION['pos_user_id'],
									'comments' => scrubInput($_POST['comments']),
									'payment_amount' => scrubInput($_POST['payment_amount']),
									'source_journal' => 'GENERAL JOURNAL',
										'pos_manufacturer_id' => 0
									);
	$pos_payments_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_payments_journal', $payment_insert_array);
	//assuming the payment matches the invoices here - probably bad assumption... 
	//need to check that there is money to pay - will do that later, lets just get this part working...
	
	//need to make sure we do not over-pay.... or do we?
	
	$payment_amount = $_POST['payment_amount'];
	$payment_remainder = $payment_amount;
	$invoices_total = getExpenseInvoicesSum($_POST['pos_general_journal_id']);
	
	for($i=0;$i<sizeof($_POST['pos_general_journal_id']);$i++)
	{
		//close off oldest invoices first
		$payment_applied = getExpenseInvoicePaymentApplied($_POST['pos_general_journal_id'][$i]);
		$invoice_amount = getExpenseInvoiceAmount($_POST['pos_general_journal_id'][$i]);
		$discount_applied = getExpenseInvoiceDiscountApplied($_POST['pos_general_journal_id'][$i]);
		if($payment_remainder > $invoice_amount - $payment_applied - $discount_applied 
			|| abs($payment_remainder - ($invoice_amount - $payment_applied - $discount_applied ))< 0.00001)
		{
			//mark it payed - set the payment amount to the invoice minus the discount
			$g_journal_insert_array = array('invoice_status' => 'PAID');
			$pos_invoice_to_payment = array('pos_journal_id' => $_POST['pos_general_journal_id'][$i],
									'pos_payments_journal_id' => $pos_payments_journal_id,
									'source_journal' => 'GENERAL JOURNAL',
									'applied_amount' => $invoice_amount - $discount_applied);	
			$payment_remainder = $payment_remainder - $invoice_amount + $payment_applied + $discount_applied;
			
		}
		elseif ($payment_remainder < $invoice_amount - $payment_applied - $discount_applied)
		{
			//not fully paid
			$g_journal_insert_array = array('invoice_status' => 'UNPAID');
			$pos_invoice_to_payment = array('pos_journal_id' => $_POST['pos_general_journal_id'][$i],
									'pos_payments_journal_id' => $pos_payments_journal_id,
									'source_journal' => 'GENERAL JOURNAL',
									'applied_amount' => $invoice_amount - $discount_applied);
			$payment_remainder = 0;
		}
		$key_val_id['pos_general_journal_id'] = $_POST['pos_general_journal_id'][$i];
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_general_journal', $key_val_id, $g_journal_insert_array);
		$results[] = simpleTransactionInsertSQL($dbc, 'pos_invoice_to_payment',$pos_invoice_to_payment);
	}

	
	$post_name_for_file = 'file_name';
	if (isset($_FILES[$post_name_for_file]['size']) && $_FILES[$post_name_for_file]['size'] > 0)
	{
		$file_array = fileUploadHandler($post_name_for_file, UPLOAD_FILE_PATH .'/invoice_uploads');
		
		//$fileName = $_FILES[$post_name_for_file]['name'];
		//$tmpName  = $_FILES[$post_name_for_file]['tmp_name'];
		//$fileSize = $_FILES[$post_name_for_file]['size'];
		//$fileType = $_FILES[$post_name_for_file]['type'];
		
		$fp = fopen($file_array['path'], 'r');
		$content = fread($fp, filesize($file_array['path']));
		$content = addslashes($content);
		fclose($fp);
		if(!get_magic_quotes_gpc())
		{
			$$file_array['name'] = addslashes($file_array['name']);
		}
		$file_data['file_name'] = $file_array['name'];
		$file_data['file_type'] = $file_array['type'];
		$file_data['file_size'] = $file_array['size'];
		$file_data['binary_content'] = $content;
		$results[] = simpleTransactionUpdateSQL($dbc, 'pos_general_journal', $key_val_id, $file_data);
	}

	$close_transaction = commitTransaction($dbc, $results);
	if($close_transaction)
	{	
		$message = urlencode('Payment Using ' . getAccountName($payment_insert_array['pos_account_id']) . " for " . $payment_insert_array['payment_amount'] . " has been added");
	}
	else
	{
		$message = urlencode("Transaction Error - Receipt has not been entered!");
	}
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);		
}

	
?>
