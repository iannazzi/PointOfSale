<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'Payments Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);
$page_title = 'Check Payment';

checkPostErrors();
if (isset($_POST['submit'])) 
{

	checkAccountLockDate($_POST['pos_account_id'], $_POST['payment_date']);
	//checkAccountLockDate($_POST['pos_payee_account_id'], $_POST['payment_date']);

	$date = date('Y:m:d H:i:s');
	$dbc = startTransaction();
	$payment_amount = $_POST['payment_amount'];
	$payment_schedule = (strtotime($_POST['payment_date']) <= strtotime(date('Y-m-d'))) ? 'COMPLETE' : 'SCHEDULED';
	//$pos_manufacturer_id = $_POST['pos_manufacturer_id'];
	$pos_payee_account_id = $_POST['pos_payee_account_id'];
	//$pos_payee_account_id = getManufacturerAccount($pos_manufacturer_id);
	$payment_insert_info =  array(			'pos_store_id' => $_SESSION['store_id'],
										'pos_employee_id' => 0,
										'pos_account_id' => scrubInput($_POST['pos_account_id']),
										'pos_payee_account_id' => $pos_payee_account_id,
										'payment_date' => scrubInput($_POST['payment_date']),
										'payment_entry_date' => getCurrentTime(),
										'payment_status' => $payment_schedule,
										'pos_user_id' => $_SESSION['pos_user_id'],
										'comments' => scrubInput($_POST['comments']),
										'payment_amount' => scrubInput($_POST['payment_amount']),
										'source_journal' => 'PURCHASES JOURNAL',
										//'pos_manufacturer_id' => $pos_manufacturer_id
										);		
	$type = $_POST['type'];
	if(strtoupper($type)=='EDIT')
	{
		$pos_payments_journal_id = getPostOrGetId('pos_payments_journal_id');
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_payments_journal', array('pos_payments_journal_id' => $pos_payments_journal_id), $payment_insert_info);
		$message = urlencode('Payment # ' . $pos_payments_journal_id . " has been updated");
	}
	else
	{
		$pos_payments_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_payments_journal', $payment_insert_info);
		$message = urlencode('Payment # ' . $pos_payments_journal_id . " has been inserted");
	}	
	// now try to apply and close invoices
	if (isset($_POST['pos_purchases_journal_id']))
	{
		$invoices_effected = getSQL("SELECT pos_journal_id, applied_amount FROM pos_invoice_to_payment WHERE pos_payments_journal_id = $pos_payments_journal_id AND source_journal = 'PURCHASES JOURNAL'");
	//probably want to set all of those po's to incomplete
		for($i=0;$i<sizeof($invoices_effected);$i++)
		{
			if($invoices_effected[$i]['applied_amount'] != 0)
			{
				 setPurchaseJournalInvoicePaymnetStatus($dbc, $invoices_effected[$i]['pos_journal_id'], 'UNPAID');
				 setPurchaseJournalInvoiceStatus($dbc, $invoices_effected[$i]['pos_journal_id'], 'OPEN');
			 }
		}
		$delete_sql = "DELETE FROM pos_invoice_to_payment WHERE pos_payments_journal_id = $pos_payments_journal_id AND source_journal='PURCHASES JOURNAL'";
		$result[] = runtransactionsql($dbc, $delete_sql);
		for($i=0;$i<sizeof($_POST['pos_purchases_journal_id']);$i++)
		{
			$pos_purchases_journal_id = $_POST['pos_purchases_journal_id'][$i];
			if(!in_array($_POST['pos_purchases_journal_id'][$i], array('NULL', '', 'false')))
			{
				$applied_amount = $_POST['applied_amount_from_this_payment'][$i];
				$po_invoice_insert_array = array('pos_journal_id' => $pos_purchases_journal_id,
														'pos_payments_journal_id' => $pos_payments_journal_id,
														'applied_amount' => $applied_amount,
														'source_journal' => 'PURCHASES JOURNAL',
													//	'comments' =>$_POST['comments_for_applied'][$i] 
													);							
				$result[] = simpleTransactionInsertSQL($dbc,'pos_invoice_to_payment', $po_invoice_insert_array);
			}
		}
		//applyPaymentToPurchaseInvoice($dbc, $pos_payments_journal_id, $_POST['pos_purchases_journal_id'] ,$payment_amount);
		
	}
	$close_transaction = simpleCommitTransaction($dbc);

	//now try to close everything...
	if (isset($_POST['pos_purchases_journal_id']))
	{
		tryToCloseInvoices($_POST['pos_purchases_journal_id']);
		
		
	}
	//is the payment applied?

	 setPaymentAppliedStatus($pos_payments_journal_id);

	header('Location: '.POS_ENGINE_URL . '/accounting/PaymentsJournal/view_payments_journal_entry.php?pos_payments_journal_id=' . $pos_payments_journal_id);
		
	
}



?>
