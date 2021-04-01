<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
if (isset($_POST['submit'])) 
{
	$pos_purchases_journal_id = getPostOrGetID('pos_purchases_journal_id');
	$array_table_def = deserializeTableDef($_POST['table_def']);
	$table_data = getArrayOfPostDataUsingTableDef($array_table_def);
	
	
	$dbc = startTransaction();
	//set all credit memos back to unused and OPEN
	$credit_memos_effected = getSQL("SELECT pos_purchases_journal_credit_memo_id, applied_amount FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_invoice_id = $pos_purchases_journal_id");
	//probably want to set all of those po's to incomplete
	for($i=0;$i<sizeof($credit_memos_effected);$i++)
	{

		$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', array('pos_purchases_journal_id' => $credit_memos_effected[$i]['pos_purchases_journal_credit_memo_id']), array('payment_status' => 'UNUSED', 'invoice_status' => 'OPEN'));
		 
	}
	$sql = "DELETE FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_invoice_id = $pos_purchases_journal_id";
	$result[] = runTransactionSQL($dbc, $sql);
	
	//now add each one
	for($i=0;$i<sizeof($table_data);$i++)
	{
		if($table_data[$i]['applied_amount'] > 0)
		{
			$insert = array('pos_purchases_journal_invoice_id' => $pos_purchases_journal_id,
							'pos_purchases_journal_credit_memo_id' => $table_data[$i]['pos_purchases_journal_credit_memo_id'],
							'applied_amount' => $table_data[$i]['applied_amount']);
			$results[] = simpleTransactionInsertSQL($dbc, 'pos_invoice_to_credit_memo',$insert);
		}
	}
	
	$close_transaction = simpleCommitTransaction($dbc);
	
	//now can we mark the credit memo as used?
	for($i=0;$i<sizeof($table_data);$i++)
	{
		
		$credit_total = getPurchasesInvoiceTotal($table_data[$i]['pos_purchases_journal_credit_memo_id']);
		$applied_amount = getCreditMemoAppliedTotal($table_data[$i]['pos_purchases_journal_credit_memo_id']);
		if(abs($credit_total - $applied_amount) < 0.00001)
		{
			$credit_update_array = array('invoice_status' => 'CLOSED' ,
		'payment_status' => 'USED');
		}
		else
		{
			$credit_update_array = array('invoice_status' => 'OPEN' ,
		'payment_status' => 'UNUSED');
		}
		$results[] = simpleUpdateSQL('pos_purchases_journal', array('pos_purchases_journal_id' => $table_data[$i]['pos_purchases_journal_credit_memo_id']), $credit_update_array);
	}
	
	//can we mark the invoice as paid?
	$status = checkIfInvoiceIsPaid($pos_purchases_journal_id);
	if ($status == 'PAID')
	{
		$purchases_journal_update_array = array('invoice_status' => 'CLOSED' ,
		'payment_status' => 'PAID');
	}
	else
	{
		$purchases_journal_update_array = array('invoice_status' => 'OPEN' ,
		'payment_status' => 'UNPAID');
	}
	$key_val_id2['pos_purchases_journal_id'] = $pos_purchases_journal_id;
	$results[] = simpleUpdateSQL('pos_purchases_journal', $key_val_id2, $purchases_journal_update_array);
	/*
	
	$credit_memo_id = $_POST['credit_memo_id'];
	$credit_memo_total = getPurchasesInvoiceTotal($credit_memo_id);
	$credit_memo_applied = scrubInput($_POST['credit_memo_applied']);
	$amount_previously_used = getAmountUsedOnCreditMemo($credit_memo_id);
	$credit_memo_remainder = $credit_memo_total - $credit_memo_applied -$amount_previously_used;
	$results= array();
	$date = date('Y:m:d H:i:s');	
	$dbc = startTransaction();	



	if(abs($credit_memo_remainder) < 0.0001)
	{
			$credit_memo_update_array = array('invoice_status' => 'CLOSED','payment_status' => 'USED');
			$pos_invoice_to_credit_memo = 
			array(	'pos_purchases_journal_invoice_id' => $pos_purchases_journal_id,
					'pos_purchases_journal_credit_memo_id' => $credit_memo_id ,
					'applied_amount' => $credit_memo_applied );
	}
	else if($credit_memo_remainder>0)
	{
			$credit_memo_update_array = array('invoice_status' => 'OPEN','payment_status' => 'UNUSED');
			$pos_invoice_to_credit_memo = 
			array(	'pos_purchases_journal_invoice_id' => $pos_purchases_journal_id,
					'pos_purchases_journal_credit_memo_id' => $credit_memo_id ,
					'applied_amount' => $credit_memo_applied );	
	}
	else if($credit_memo_remainder<0)
	{
		//we have an error
		trigger_error('Credit Memo Error');
	}
	$key_val_id['pos_purchases_journal_id'] = $credit_memo_id;
	$results[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', $key_val_id, $credit_memo_update_array);
	$key_val_id2['pos_purchases_journal_id'] = $pos_purchases_journal_id;
	$results[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', $key_val_id2, $purchases_journal_update_array);
	$results[] = simpleTransactionInsertSQL($dbc, 'pos_invoice_to_credit_memo',$pos_invoice_to_credit_memo);*/
	
	
	
	$message = urlencode('Credit Memo Applied');
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);		
}

	
?>
