<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'General Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');

if (isset($_POST['submit'])) 
{
	// adding the invoice means we are recording it but not paying it.
	// this is not an accounting event until it is paid
	$results= array();
	checkAccountLockDate($_POST['pos_account_id'], $_POST['invoice_date']);
	checkAccountLockDate($_POST['pos_payment_account_id'], $_POST['payment_date']);
	
	$date = date('Y:m:d H:i:s');	
	$dbc = startTransaction();							
	
	$g_journal_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'invoice_date' => $_POST['invoice_date'],
									'invoice_due_date' => $_POST['invoice_due_date'],
																		'invoice_number' => scrubInput($_POST['invoice_number']),

									'entry_amount' => $_POST['entry_amount'],
									'pos_account_id' => $_POST['pos_account_id'],
									'pos_chart_of_accounts_id' => $_POST['pos_chart_of_accounts_id'],
									'supplier' => getAccountName($_POST['pos_account_id']),
									'description' => $_POST['description'],
									'comments' => $_POST['comments'],
									'entry_date' => getCurrentTime(),
									'pos_user_id' => $_SESSION['pos_user_id'],
									'invoice_status' => 'CLOSED',
									'payment_status' => 'PAID',
									'entry_type' => 'Invoice',
									'use_tax' => scrubInput($_POST['use_tax'])
									);
	$pos_general_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_general_journal', $g_journal_insert_array);
	$key_val_id['pos_general_journal'] = $pos_general_journal_id;

	$payment_amount = $_POST['entry_amount'];
	

	$payment_satus = (strtotime($_POST['payment_date']) <= strtotime(date('Y-m-d'))) ? 'COMPLETE' : 'SCHEDULED';
	$payment_insert_array = array('pos_store_id' => $_SESSION['store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'pos_account_id' => $_POST['pos_payment_account_id'],
									'pos_payee_account_id' => $_POST['pos_account_id'],
									'payment_date' => $_POST['payment_date'],
									'payment_entry_date' =>getCurrentTime(),
									'payment_status' => $payment_satus,
									'pos_user_id' => $_SESSION['pos_user_id'],
									'comments' => $_POST['comments'],
									'payment_amount' => $_POST['entry_amount'],
									'source_journal' => 'GENERAL JOURNAL',
										'pos_manufacturer_id' => 0
									);
	$pos_payments_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_payments_journal', $payment_insert_array);
	$pos_invoice_to_payment = array('pos_journal_id' => $pos_general_journal_id,
									'pos_payments_journal_id' => $pos_payments_journal_id,
									'source_journal' => 'GENERAL JOURNAL',
									'applied_amount' => $_POST['entry_amount']);		
	$results[] = simpleTransactionInsertSQL($dbc, 'pos_invoice_to_payment',$pos_invoice_to_payment);
	

	$close_transaction = commitTransaction($dbc, $results);
	if($close_transaction)
	{	
		$message = urlencode('Journal Entry ' . $pos_general_journal_id . ' ' . $g_journal_insert_array['description'] . " has been added");
	}
	else
	{
		$message = urlencode("Transaction Error - Receipt has not been entered!");
	}
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);		
}

	
?>
