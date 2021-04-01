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

	
	//The pos_chart_of_accounts_id is the 'Expense account'
	//the journal entry would essentially be this:
	// DATE DESCRIPTION / CHART OF ACCOUNTS DEBIT CREDIT
	//	1-1	office exxpense - coffee		5
	// Then there is the 'payment' which goes to an account:
	//	1-1 payment for office expense coffee 1000 cash	5
	
	//if it is a credit charge we need to move it to  accounts payable - sub account id (the account id automatically tells me it is accounts payable?
	// get the visa charges in a month:
	// SELECT receipt_amount FROM pos_general_journal WHERE pos_account_id = 'visa';
	//get the office expenses charges in a month
	// SELECT receipt_amount FROM pos_general_journal WHERE pos_chart_of_account_id = 'office expenses'
	//get total accounts payable (cc) in a year
	// SELECT receipt_amount FROM pos_general_journal WHERE pos_account_type = 'CC'
	// get total cash payments to wegmans in a month:
	// SELECT * FROM pos_general_journal WHERE supplier = 'wegmans' AND pos_account_type is cash
	// get total payments to wegmans in a month:
	// SELECT receipt_amount FROM pos_general_journal WHERE supplier = 'wegmans' and date range
	// DATE    SUPPLIER      DESCRIPTION     CATEGORY	PAYMENT		DEBIT	CREDIT
	//	1-1		weg				bulbs			repair	c-cash		42.5
	//	2-3		WEG				SNACKS		MEAL		united		101.25
	//	2-4		weg				bulbs returned	repair	c-cash				42.5
	
	//get the repair account for the year
	// SELECT	*  FROM pos_general_journal WHERE pos_chart_of_accounts_id = '7000' and daterange
	// DATE    SUPPLIER      DESCRIPTION     CATEGORY	PAYMENT		DEBIT	CREDIT
	//  1-1		weg				bulbs			repair		c-cash	42.5
	//	2-4		weg				bulbs			repair		c-cash				42.5
	
	//get the cash disbursed from craig's wallet - this is where things get reversed!
	//SELECT * FROM pos_general_journal WHERE pos_account_id = craig's wallet
	// DATE    SUPPLIER      DESCRIPTION     CATEGORY	PAYMENT		DEBIT	CREDIT
	//  1-1		weg				bulbs			repair		c-cash			42.5
	//	2-4		weg				bulbs returned	repair	c-cash		42.5
	
	//when looking at the 'Expense Accounts' we need to see the + amount Debited
	//when looking at the 'Payment Accounts' we need to see the + amount Credited
	
	// if receipt_amount > 0 display in CREDIT else display in DEBIT column	
	/*$table_def_array = deserializeTableDef($_POST['table_def_array']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array[0]);
	$other_info_array = array(	'entry_date' => getCurrentTime(),
								'pos_user_id' => $_SESSION['pos_user_id'],
								'payment_date' => $_POST['invoice_date'],
								'invoice_due_date' => $_POST['invoice_date'],
								
	$insert = array_merge($insert, $other_info_array);*/
		//check the lock date of the account...
	checkAccountLockDate($_POST['pos_account_id'], $_POST['invoice_date']);
	$date = date('Y:m:d H:i:s');	
	$dbc = startTransaction();
	$g_journal_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'invoice_date' => $_POST['invoice_date'],
									'invoice_number' => scrubInput($_POST['invoice_number']),
									'invoice_status' => 'CLOSED',
									'entry_amount' => scrubInput($_POST['entry_amount']),
									'pos_chart_of_accounts_id' => $_POST['pos_chart_of_accounts_id'],
									'supplier' => scrubInput($_POST['supplier']),
									'description' => scrubInput($_POST['description']),
									'comments' => scrubInput($_POST['comments']),
									'entry_date' => getCurrentTime(),
									'pos_user_id' => $_SESSION['pos_user_id'],
									'invoice_due_date' => $_POST['invoice_date'],
									'entry_type' => 'Receipt',
									'use_tax' => scrubInput($_POST['use_tax'])
									);
	
	$payment_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'pos_account_id' => $_POST['pos_account_id'],
									'payment_date' => $_POST['invoice_date'],
									'payment_entry_date' =>getCurrentTime(),
									'payment_status' => 'COMPLETE',
									'pos_user_id' => $_SESSION['pos_user_id'],
									'comments' => $_POST['comments'],
									'payment_amount' => $_POST['entry_amount'],
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
									'applied_amount' => $_POST['entry_amount']);								
									
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
	$payments = getTransactionPaymentsJournalDataFromGeneralJournal($dbc, $gj_data, $payments);
	$gl_post_array = createExpensePostToGeneralLedgerArray($dbc, $pos_general_journal_id);
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
		$message = urlencode('Journal Entry ' .$pos_general_journal_id . ' - '  . $g_journal_insert_array['description'] . " has been added");
	}
	else
	{
		$message = urlencode("Transaction Error - Receipt has not been entered!");
	}
	tryToCloseExpenseInvoices(array($pos_general_journal_id));
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);		
}

	
?>
