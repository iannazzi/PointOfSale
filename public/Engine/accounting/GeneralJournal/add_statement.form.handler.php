<?php
$binder_name = 'General Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');

if (isset($_POST['submit'])) 
{
	// adding the invoice means we are recording it but not paying it.
	// this is not an accounting event until it is paid
	$results= array();

	$date = date('Y:m:d H:i:s');	
	$dbc = startTransaction();							
	
	$g_journal_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'invoice_date' => $_POST['invoice_date'],
									'invoice_due_date' => $_POST['invoice_due_date'],
									'entry_amount' => $_POST['entry_amount'],
									'minimum_amount_due' => $_POST['minimum_amount_due'],
									'pos_account_id' => $_POST['pos_account_id'],
									'supplier' => scrubInput(getAccountName($_POST['pos_account_id']) .' '.getAccountNumber($_POST['pos_account_id'])),
									'description' => 'Statement',
									'comments' => $_POST['comments'],
									'entry_date' => getCurrentTime(),
									'pos_user_id' => $_SESSION['pos_user_id'],
									'invoice_status' => 'UNPAID',
									'entry_type' => 'Statement'
									);
	$pos_general_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_general_journal', $g_journal_insert_array);
	$key_val_id['pos_general_journal'] = $pos_general_journal_id;


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
