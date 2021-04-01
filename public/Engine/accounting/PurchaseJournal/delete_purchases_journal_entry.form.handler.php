<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);
$complete_location = 'list_purchase_journal.php';
$cancel_location = 'list_purchase_journal.php?message=Canceled';

	$pos_purchases_journal_id = getPostOrGetID('pos_purchases_journal_id');
	$payments = getPaymentsFromPurchasesJournalEntry($pos_purchases_journal_id);
	$original_pj_entry = getPurchaseJournalData($pos_purchases_journal_id);
	
	for($i=0;$i<sizeof($payments);$i++)
{
	
	
	$check_data = getSQL("SELECT pos_account_id, pos_payee_account_id, payment_date FROM pos_payments_journal WHERE pos_payments_journal_id = " .$payments[$i]['pos_payments_journal_id']);
	checkAccountLockDate($check_data[0]['pos_account_id'], $check_data[0]['payment_date']);
	checkAccountLockDate($check_data[0]['pos_payee_account_id'], $check_data[0]['payment_date']);

}

	
	$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
	$date = date('Y:m:d H:i:s');	
	if($original_pj_entry[0]['invoice_type'] == 'Regular')
	{
		$dbc = startTransaction();
		$sql = "DELETE FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
		$results[] = runTransactionSQL($dbc,$sql);
		if(sizeof($payments)>0)
		{
			$sql2 = "DELETE FROM pos_invoice_to_payment WHERE pos_journal_id=$pos_purchases_journal_id AND source_journal = 'PURCHASES JOURNAL'";
			$results[] = runTransactionSQL($dbc,$sql2);
		}
		for($i=0;$i<sizeof($payments);$i++)
		{
			$sql3[$i] = "DELETE FROM pos_payments_journal WHERE pos_payments_journal_id=" .$payments[$i]['pos_payments_journal_id'];
			$results[] = runTransactionSQL($dbc,$sql3[$i]);
		}
		
		$sql4 = "DELETE FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
		$results[] = runTransactionSQL($dbc,$sql4);
		
		//need to back out the invoice applied to the PO
		$purchase_orders = getPurchaseOrderIdsFromPurchaseJournalID($pos_purchases_journal_id);
		$invoice_amount = $original_pj_entry[0]['invoice_amount'];
		if($invoice_amount>0)
		{
			for($i=sizeof($purchase_orders)-1;$i>-1;$i--)
			{
				$po_insert_array = array('invoice_status' => 'INCOMPLETE');
				$key_val_id3['pos_purchase_order_id'] = $purchase_orders[$i]['pos_purchase_order_id'];
				$results[] = simpleTransactionUpdateSQL($dbc,'pos_purchase_orders', $key_val_id3, $po_insert_array);
			}
		}
		simpleCommitTransaction($dbc);
		$message = urlencode('Journal Entry ' . $original_pj_entry[0]['invoice_number'] . " has been Deleted");
		header('Location: '.$complete_location .'?message=' . $message);
	}
	else
	{
		//credit memo delete
		$dbc = startTransaction();
		$purchase_orders_effected = getSQL("SELECT pos_purchase_order_id, applied_amount FROM pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id");
		$sql4 = "DELETE FROM pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
		$results[] = runTransactionSQL($dbc,$sql4);
		
		//probably want to set all of those po's to incomplete
		for($i=0;$i<sizeof($purchase_orders_effected);$i++)
		{
			if($purchase_orders_effected[$i]['applied_amount'] != 0)
			{
				 //setPurchaseOrderInvoiceStatus($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], 'INCOMPLETE');
				 //setPOCreditMemoRequired($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], '1');
				 $po_update_array = array('credit_memo_required' => 1, 'invoice_status' => 'INCOMPLETE', 'purchase_order_status' => 'OPEN');
				$results[] = simpleTransactionUpdateSQL($dbc,'pos_purchase_orders', array('pos_purchase_order_id' => $purchase_orders_effected[$i]['pos_purchase_order_id']), $po_update_array);
				 
			 }
		}
		
		//now we need to take the credit memo out of the invoice applied lookup table
		$purchase_journals_effected = getSQL("SELECT pos_purchases_journal_invoice_id, applied_amount FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_credit_memo_id = $pos_purchases_journal_id");
		$sql4 = "DELETE FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_credit_memo_id = $pos_purchases_journal_id";
		$results[] = runTransactionSQL($dbc,$sql4);
		//probably want to set all of those pj's to incomplete and back out the credit applied amount....
		for($i=0;$i<sizeof($purchase_journals_effected);$i++)
		{
			if($purchase_journals_effected[$i]['applied_amount'] != 0)
			{
				 //setPurchaseOrderInvoiceStatus($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], 'INCOMPLETE');
				 //setPOCreditMemoRequired($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], '1');
				 $pj_update_array = array('invoice_status' => 'OPEN', 'payment_status' => 'UNPAID');
				$results[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', array('pos_purchases_journal_id' => $purchase_journals_effected[$i]['pos_purchases_journal_invoice_id']), $pj_update_array);
				 
			 }
		}
		
		
		$sql = "DELETE FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
		$results[] = runTransactionSQL($dbc,$sql);
		simpleCommitTransaction($dbc);
		$message = urlencode('Journal Entry ' . $original_pj_entry[0]['invoice_number'] . " has been Deleted");
		header('Location: '.$complete_location .'?message=' . $message);
	}
?>
