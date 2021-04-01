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
	$date = date('Y:m:d H:i:s');	
	$dbc = startTransaction();
	$g_journal_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'invoice_date' => scrubInput($_POST['invoice_date']),
									'entry_amount' => scrubInput($_POST['entry_amount']),
									'pos_account_id' => $_POST['debit_pos_account_id'],
									'pos_chart_of_accounts_id' => getChartOfAccountsIDFromAccountId($_POST['debit_pos_account_id']),
									'supplier' => scrubInput(getAccountName($_POST['debit_pos_account_id'])),
									'description' => 'Balance Transfer',
									'comments' => scrubInput($_POST['comments']),
									'entry_date' => getCurrentTime(),
									'pos_user_id' => $_SESSION['pos_user_id'],
									'invoice_due_date' => scrubInput($_POST['invoice_date']),
									'entry_type' => 'Transfer'
									);
	
	$payment_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'pos_account_id' => $_POST['credit_pos_account_id'],
									'payment_date' => $_POST['invoice_date'],
									'payment_entry_date' =>getCurrentTime(),
									'payment_status' => 'COMPLETE',
									'pos_user_id' => $_SESSION['pos_user_id'],
									'comments' => scrubInput($_POST['comments']),
									'payment_amount' => scrubInput($_POST['entry_amount']),
									'source_journal' => 'GENERAL JOURNAL',
										'pos_manufacturer_id' => 0
									);
	$pos_general_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_general_journal', $g_journal_insert_array);
	$key_val_id['pos_general_journal'] = $pos_general_journal_id;
	$pos_payments_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_payments_journal', $payment_insert_array);
	$key_val_id['pos_payments_journal_id'] = $pos_payments_journal_id;
	
	$pos_invoice_to_payment = array('pos_journal_id' => $pos_general_journal_id,
									'pos_payments_journal_id' => $pos_payments_journal_id,
									'source_journal' => 'GENERAL JOURNAL',
									'applied_amount' => scrubInput($_POST['entry_amount']));								
									
	$results[] = simpleTransactionInsertSQL($dbc, 'pos_invoice_to_payment',$pos_invoice_to_payment);
	
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
	//general ledger post
	/*$gj_data = getTransactionGeneralJournalData($dbc, $pos_general_journal_id);
	$payments = getTransactionPaymentsJournalDataFromGeneralJournal($dbc, $pos_general_journal_id);
	$gl_post_array = createExpensePostToGeneralLedgerArray($dbc, $gj_data, $payments);
	lockTable($dbc, 'pos_general_ledger');
	$transaction_id = getGeneralLedgerTransactionID($dbc);
	for($i=0;$i<sizeof($gl_post_array);$i++)
	{
		$gl_post_array[$i]['pos_general_ledger_post_id'] = getGeneralLedgerPOSTID($dbc);
		$gl_post_array[$i]['pos_general_ledger_transaction_id'] = $transaction_id;
		$gl_post_array[$i]['date'] = $date;
		$gl_post_array[$i]['pos_user_id'] = $_SESSION['pos_user_id'];
		$results[] = simpleTransactionInsertSQL($dbc, 'pos_general_ledger', $gl_post_array[$i]);
	}
	unlockTables($dbc);*/
	$close_transaction = commitTransaction($dbc, $results);
	if($close_transaction)
	{	
		$message = urlencode('Journal Entry ' . $g_journal_insert_array['description'] . " has been added");
	}
	else
	{
		$message = urlencode("Transaction Error - Receipt has not been entered!");
	}
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);		
}

	
?>
