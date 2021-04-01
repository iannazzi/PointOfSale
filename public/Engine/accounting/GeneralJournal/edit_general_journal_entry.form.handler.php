<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'General Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);
if (isset($_POST['submit'])) 
{
	
	$pos_general_journal_id = getPostOrGetID('pos_general_journal_id');
	$original_gj_entry = getSQL("SELECT entry_amount, supplier, description, pos_chart_of_accounts_id FROM pos_general_journal WHERE pos_general_journal_id=$pos_general_journal_id");
	
	$original_pj_entry = getPaymentDataFromGJEntry($pos_general_journal_id);
	
	
	$key_val_id['pos_general_journal_id'] = $pos_general_journal_id;
	
	//what type of entry was this?
	
	//invoice status, payment status and entry type might not be avialable..
	if($_POST['general_journal_entry_type'] == 'Invoice')
	{
	$g_journal_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'invoice_date' => $_POST['invoice_date'],
									'invoice_number' => scrubInput($_POST['invoice_number']),
									'entry_amount' => $_POST['entry_amount'],
									'pos_chart_of_accounts_id' => $_POST['pos_chart_of_accounts_id'],
									'supplier' => scrubInput($_POST['supplier']),
									'description' => scrubInput($_POST['description']),
									'comments' => scrubInput($_POST['comments']),
									'entry_date' => getCurrentTime(),
									'pos_user_id' => $_SESSION['pos_user_id'],
									'invoice_due_date' => $_POST['invoice_date'],
									'invoice_status' => $_POST['invoice_status'],
									'payment_status' => $_POST['payment_status'],
									'entry_type' => $_POST['entry_type'],
									'pos_account_id' => $_POST['pos_account_id'],
									'use_tax' => scrubInput($_POST['use_tax'])
									);
	}
	else
	{
		$g_journal_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'invoice_date' => $_POST['invoice_date'],
									'invoice_number' => scrubInput($_POST['invoice_number']),
									'entry_amount' => $_POST['entry_amount'],
									'pos_chart_of_accounts_id' => $_POST['pos_chart_of_accounts_id'],
									'supplier' => scrubInput($_POST['supplier']),
									'description' => scrubInput($_POST['description']),
									'comments' => scrubInput($_POST['comments']),
									'entry_date' => getCurrentTime(),
									'pos_user_id' => $_SESSION['pos_user_id'],
									'invoice_due_date' => $_POST['invoice_date'],
									'use_tax' => scrubInput($_POST['use_tax'])
									
									
									);
	}
	
	/*if($_POST['general_journal_entry_type'] == 'Invoice')
	{
		$invoice_array = array(
									//'payments_applied' => $_POST['payments_applied'],
									'invoice_status' => $_POST['invoice_status'],
																		'payment_status' => $_POST['payment_status'],

									);
		$g_journal_insert_array	= array_merge($g_journal_insert_array, $invoice_array);				
		
	}
	else if ($_POST['general_journal_entry_type'] == 'Receipt')
	{
	
	}
	else if ($_POST['general_journal_entry_type'] == 'Transfer')
	{
	}
	else if ($_POST['general_journal_entry_type'] == 'Statement')
	{
	}*/
	$date = date('Y:m:d H:i:s');	
	$dbc = startTransaction();
	
	$results[] = simpleTransactionUpdateSQL($dbc,'pos_general_journal', $key_val_id, $g_journal_insert_array);


	/*//this is where there should be multiple payments.....
	$payments = getPaymentsFromGJEntry($pos_general_journal_id);
	for($p=0;$p<sizeof($payments);$p++)
	{
		$pos_payments_journal_id = $payments[$p]['pos_payments_journal_id'];
		$payment_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'pos_account_id' => $_POST['pos_account_id'],
									'payment_date' => $_POST['invoice_date'],
									'payment_entry_date' =>getCurrentTime(),
									'payment_status' => 'COMPLETE',
									'pos_user_id' => $_SESSION['pos_user_id'],
									'comments' => scrubInput($_POST['comments']),
									'payment_amount' => $_POST['entry_amount']
									);
		$key_val_id2['pos_payments_journal_id'] = $pos_payments_journal_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_payments_journal', $key_val_id2, $payment_insert_array);
	}*/
	
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
	/*if (sizeof($payments)>0)
	{
		//general ledger post - first the reverse for the edit, then the new post
		$gl_reverse_post_array = reverseExpensePostToGeneralLedgerArray($dbc, $original_gj_entry, $original_pj_entry);	
		$gj_data = getTransactionGeneralJournalData($dbc, $pos_general_journal_id);
		$payments = getTransactionPaymentsJournalDataFromGeneralJournal($dbc, $pos_general_journal_id);
		$gl_post_array = createExpensePostToGeneralLedgerArray($dbc, $gj_data, $payments);
		lockTable($dbc, 'pos_general_ledger');
		
		$transaction_id = getGeneralLedgerTransactionID($dbc);
		for($i=0;$i<sizeof($gl_reverse_post_array);$i++)
		{
			$gl_reverse_post_array[$i]['pos_general_ledger_post_id'] = getGeneralLedgerPOSTID($dbc);
			$gl_reverse_post_array[$i]['pos_general_ledger_transaction_id'] = $transaction_id;
			$gl_reverse_post_array[$i]['date'] = $date;
			$gl_reverse_post_array[$i]['pos_user_id'] = $_SESSION['pos_user_id'];
			$results[] = simpleTransactionInsertSQL($dbc, 'pos_general_ledger', $gl_reverse_post_array[$i]);
		}
		
		$transaction_id = getGeneralLedgerTransactionID($dbc);
		for($i=0;$i<sizeof($gl_post_array);$i++)
		{
			$gl_post_array[$i]['pos_general_ledger_post_id'] = getGeneralLedgerPOSTID($dbc);
			$gl_post_array[$i]['pos_general_ledger_transaction_id'] = $transaction_id;
			$gl_post_array[$i]['date'] = $date;
			$gl_post_array[$i]['pos_user_id'] = $_SESSION['pos_user_id'];
			$results[] = simpleTransactionInsertSQL($dbc, 'pos_general_ledger', $gl_post_array[$i]);
		}
		unlockTables($dbc);
	}*/
	$close_transaction = commitTransaction($dbc, $results);
	if($close_transaction)
	{	
		$message = urlencode('Journal Entry ' . $g_journal_insert_array['description'] . " has been added");
	}
	else
	{
		$message = urlencode("Transaction Error - Receipt has not been entered!");
	}
		tryToCloseExpenseInvoices(array($pos_general_journal_id));

	header('Location: '.$_POST['complete_location'] .'?message=' . $message);		
}

	
?>
