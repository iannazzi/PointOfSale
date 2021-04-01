<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'Payments Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);
$complete_location = 'list_payments_journal.php';
$cancel_location = 'list_payments_journal.php?message=Canceled';
$date = date('Y:m:d H:i:s');	

$pos_payments_journal_id = getPostOrGetID('pos_payments_journal_id');
$check_data = getSQL("SELECT pos_account_id, pos_payee_account_id, payment_date FROM pos_payments_journal WHERE pos_payments_journal_id = $pos_payments_journal_id");

checkAccountLockDate($check_data[0]['pos_account_id'], $check_data[0]['payment_date']);
checkAccountLockDate($check_data[0]['pos_payee_account_id'], $check_data[0]['payment_date']);

$dbc = startTransaction();

$original_pj_entry = getTransactionPaymentsJournalData($dbc, $pos_payments_journal_id);
$source_journal  = getPaymentSourceJournal($pos_payments_journal_id);



if($source_journal == 'PURCHASES JOURNAL')
{
	//original invoices
	$invoice_array = array();
	$invoices_effected = getSQL("SELECT pos_journal_id, applied_amount FROM pos_invoice_to_payment WHERE pos_payments_journal_id = $pos_payments_journal_id AND source_journal = 'PURCHASES JOURNAL'");
	//probably want to set all of those po's to incomplete
	for($i=0;$i<sizeof($invoices_effected);$i++)
	{
		$invoice_array[$i] = $invoices_effected[$i]['pos_journal_id'];
	}
	$results = deletePurchasePayment($dbc, $pos_payments_journal_id);
	//now try to close out the invoices again...
	
	$close_transaction = simpleCommitTransaction($dbc);
	//try to fix up invoices..
	tryToCloseInvoices($invoice_array);
	
}
else if ($source_journal == 'GENERAL JOURNAL')
{
	$results = deletePayment($dbc, $pos_payments_journal_id);
	$close_transaction = simpleCommitTransaction($dbc);
}
else
{
}


header('Location: '.$complete_location .'?message=Deleted');
	

		


	
?>
