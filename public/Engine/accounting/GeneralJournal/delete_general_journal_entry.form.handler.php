<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'General Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);
$complete_location = 'list_general_journal.php';
$cancel_location = 'list_general_journal.php?message=Canceled';

	$pos_general_journal_id = getPostOrGetID('pos_general_journal_id');
	$original_gj_entry = getSQL("SELECT entry_amount, supplier, description, pos_chart_of_accounts_id FROM pos_general_journal WHERE pos_general_journal_id=$pos_general_journal_id");
	$payments = getPaymentsFromGJEntry($pos_general_journal_id);
	$original_pj_entry = getPaymentDataFromGJEntry($pos_general_journal_id);
	

for($i=0;$i<sizeof($original_pj_entry);$i++)
{
	
	checkAccountLockDate($original_pj_entry[$i]['pos_account_id'], $original_pj_entry[$i]['payment_date']);
	checkAccountLockDate($original_pj_entry[$i]['pos_payee_account_id'], $original_pj_entry[$i]['payment_date']);

}
	
	
	
	$key_val_id['pos_general_journal_id'] = $pos_general_journal_id;
	$date = date('Y:m:d H:i:s');	
	$dbc = startTransaction();
	$sql = "DELETE FROM pos_general_journal WHERE pos_general_journal_id = $pos_general_journal_id";
	$results[] = runTransactionSQL($dbc,$sql);
	if(sizeof($payments)>0)
	{
		$sql2 = "DELETE FROM pos_invoice_to_payment WHERE pos_journal_id=$pos_general_journal_id AND source_journal = 'GENERAL JOURNAL'";
		$results[] = runTransactionSQL($dbc,$sql2);
	}
	for($i=0;$i<sizeof($payments);$i++)
	{
		//$sql3[$i] = "DELETE FROM pos_payments_journal WHERE pos_payments_journal_id=" .$payments[$i]['pos_payments_journal_id'];
		//$results[] = runTransactionSQL($dbc,$sql3[$i]);
		$pos_payments_journal_id = $payments[$i]['pos_payments_journal_id'];
		$results[] = deletePayment($dbc, $pos_payments_journal_id);
	}

	$close_transaction = commitTransaction($dbc, $results);
	if($close_transaction)
	{	
		$message = urlencode('Journal Entry ' . $original_gj_entry[0]['description'] . " has been Deleted");
	}
	else
	{
		$message = urlencode("Transaction Error - Receipt has not been entered!");
	}
	header('Location: '.$complete_location .'?message=' . $message);		


	
?>
