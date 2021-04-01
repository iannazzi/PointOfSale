<?php
function startTransaction()
{
	$dbc = openPOSDatabase();
	@mysqli_query($dbc,"START TRANSACTION");
	return $dbc;
}

function commitTransaction($dbc,$results)
{
	$success = true;
	for($i=0;$i<sizeof($results);$i++)
	{
		if(!$results[$i])
		{
			$success = false;
		}
	}
	//	$success = false;
	if ($success) 
	{
		$tmp = @mysqli_query($dbc, "COMMIT");
	} 
	else 
	{        
		$tmp  = @mysqli_query($dbc, "ROLLBACK");
	}
	mysqli_close($dbc);
	return $success;
}
function simpleCommitTransaction($dbc)
{
	$tmp = @mysqli_query($dbc, "COMMIT");
	mysqli_close($dbc);
}
function lockTable($dbc, $db_table)
{
	$sql = "LOCK TABLE ".$db_table." WRITE";
	$result = runTransactionSQL($dbc,$sql);
	return $result;
}
function unlockTables($dbc)
{
	$sql = "UNLOCK TABLES";
	$result = runTransactionSQL($dbc,$sql);
	return $result;
}
function transactionInsert($sql_array)
{
	/*
		$sql_array is an array of sql statements that all need to be committed at once or not at all
	*/
	$dbc = openPOSDatabase();
	
	@mysqli_query($dbc,"START TRANSACTION");
	$transaction_id = getGeneralLedgerTransactionIDSQLString();
	$general_ledger_post_id = getGeneralLedgerPostIDSQLString();
	//It is here where we want to lock tables, get the transaction ID, then insert, then unlock.
	$result = array();
	for($i=0;$i<sizeof($sql_array);$i++)
	{
		//run each query
		$result[$i] = @mysqli_query($dbc,$sql_array[$i]);
		if(!$result[$i])
		{ 
			// If it did not run OK.
			@mysqli_query($dbc, "ROLLBACK");
			// Debugging message:
			trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql_array[$i] . '</p>', E_USER_WARNING);
			mysqli_close($dbc);
			return false;
		}
	}	
	//this is a double check step -seems redundant.
	$success = true;
	for($i=0;$i<sizeof($result);$i++)
	{
		if(!$result[$i])
		{
			$success = false;
		}
	}
	//	$success = false;
	if ($success) 
	{
		$tmp = @mysqli_query($dbc, "COMMIT");
	} 
	else 
	{        
		$tmp  = @mysqli_query($dbc, "ROLLBACK");
	}
	mysqli_close($dbc);
	return $success;
}
function getTransactionFieldRowSql($dbc, $sql)
{
	$result = @mysqli_query($dbc, $sql);
	if ($result) 
	{ // If it ran OK.
		$result_array = convert_mysql_result_to_array($result);
		return switchArrayFromRowFieldToFieldRows($result_array);
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
		return false;
	}	
}
function getTransactionSingleValueSQL($dbc, $sql)
{
	$result = getTransactionSQL($dbc, $sql);
	if (sizeof($result)>0)
	{
		foreach($result[0] as $key => $value)
		{
			$return_val =  $value;
		}
		if (isset($return_val))
		{
			return $return_val;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}	
}
function getTransactionSQL($dbc,$sql)
{
	$result = @mysqli_query($dbc, $sql);
	if ($result) 
	{ // If it ran OK.
		$result_array = convert_mysql_result_to_array($result);
		return $result_array;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
		return false;
	}	
}
function runTransactionSQL($dbc,$sql)
{
	$result = @mysqli_query($dbc, $sql);
	if ($result) 
	{ // If it ran OK.
		return $result;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
		return false;
	}	
}
function transactionUpdateSQL($dbc, $sql)
{
	$result = @mysqli_query($dbc, $sql);
	if (!$result)
	{
		trigger_error( "<p>Update error: " . $sql .'</p>');
	}
	return $result;
}
function simpleTransactionInsertSQL($dbc, $table, $mysql_data)
{
	//send in mysql_data that matches the mysql table.... easy breazy
	$insert_q = simpleInsertSQLString($table, $mysql_data);
	$result = @mysqli_query ($dbc, $insert_q); 
	if ($result) 
	{ // If it ran OK.
		return $result;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $insert_q . '</p>', E_USER_WARNING);
		return false;
	}	
}
function simpleTransactionInsertSQLReturnID($dbc,$table, $mysql_data)
{
	//send in mysql_data that matches the mysql table.... easy breazy
	$insert_q = simpleInsertSQLString($table, $mysql_data);
	$result = @mysqli_query ($dbc, $insert_q); 
	if ($result) 
	{ // If it ran OK. return the id
		$lastID = mysqli_insert_id($dbc);
		return $lastID;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $insert_q . '</p>', E_USER_WARNING);
		return false;
	}	
}
function simpleUpdateTransactionSQL($dbc, $table, $key_val_id, $mysql_data)
{
	$update_q = simpleUpdateSQLString($table, $key_val_id, $mysql_data);
	$result = @mysqli_query ($dbc, $update_q); 
	if ($result) 
	{ // If it ran OK.
		return $result;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $update_q . '</p>', E_USER_WARNING);
		return false;
	}	
}
function simpleTransactionUpdateSQL($dbc, $table, $key_val_id, $mysql_data)
{
	$update_q = simpleUpdateSQLString($table, $key_val_id, $mysql_data);
	$result = @mysqli_query ($dbc, $update_q); 
	if ($result) 
	{ // If it ran OK.
		return $result;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $update_q . '</p>', E_USER_WARNING);
		return false;
	}	
}
function arrayTransactionInsertSQL($dbc,$table, $mysql_data)
{
	$insert_q = arrayInsertSQLString($table, $mysql_data);
	return runTransactionSQL($dbc, $insert_q);
}
function arrayTransactionUpdateSQL($dbc, $table, $mysql_data_array)
{
	$sql = arrayUpdateSQLString($table, $mysql_data_array);
	return runTransactionSQL($dbc, $sql);
}
function deleteThenTransactionInsertArray($dbc, $table, $id, $mysql_data_array)
{
	$delete_q = "DELETE FROM ".$table." WHERE ".key($id) . "='" .$id[key($id)]."'";
	$result1 = runTransactionSQL($dbc, $delete_q);
	$result2 = arrayTransactionInsertSQL($dbc, $table, $mysql_data_array);
	return array($result1,$result2);
	
}
function getTransactionPOCCost($dbc,$pos_purchase_order_content_id)
{
	$sql = "SELECT cost FROM pos_purchase_order_contents WHERE pos_purchase_order_content_id = '$pos_purchase_order_content_id'";
	return getTransactionSingleValueSQL($dbc,$sql);
}

function getAllInvoiceGoodsTotalForPurchaseOrder($dbc, $pos_purchase_order_id)
{	$sql = "
			SELECT sum(pos_purchases_journal.invoice_amount-pos_purchases_journal.shipping_amount) as sum FROM pos_purchases_journal 
			LEFT JOIN pos_purchases_invoice_to_po
			ON pos_purchases_invoice_to_po.pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id
			WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = $pos_purchase_order_id
			";
	return getTransactionSingleValueSQL($dbc, $sql);
}
function getTransactionPurchaseOrdersOnInvoice($dbc, $pos_purchase_journal_id)
{
	$sql ="
			SELECT pos_purchase_order_id
			FROM pos_purchases_invoice_to_po 
			WHERE pos_purchases_journal_id = $pos_purchase_journal_id
			";
			return getTransactionSQL($dbc, $sql);
}
function getTransactionInvoicesFromPurchaseOrder($dbc, $pos_purchase_order_id)
{
	$sql = "
			SELECT pos_purchases_journal.*
			FROM pos_purchases_journal
			LEFT JOIN pos_purchases_invoice_to_po
			ON pos_purchases_invoice_to_po.pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id
			WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = $pos_purchase_order_id
			";
	return getTransactionSQL($dbc,$sql);
}

function tryToClosePOTransaction($dbc, $pos_purchase_order_id)
{
	//if received status = COMPLETE && (invoice_total = goods_received) for all PO->invoices

	// get the total of the invoices - don't care how many there are, just want the total
	//$invoice_total_goods = round(getAllInvoiceGoodsTotalForPurchaseOrder($dbc, $pos_purchase_order_id),2);
	
	$sql = "SELECT received_status, ra_required, credit_memo_required FROM pos_purchase_orders WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	$result = getTransactionSQL($dbc, $sql);

	if ($result[0]['received_status'] == 'COMPLETE' && $result[0]['ra_required'] == 0 && $result[0]['credit_memo_required'] == 0)
	{
		$result = setTransactionPOStatus($dbc, $pos_purchase_order_id, 'CLOSED');
	}
	else
	{
		$result = setTransactionPOStatus($dbc,$pos_purchase_order_id, 'OPEN');
	}
	return $result;
}
function closeInvoice($dbc, $pos_purchases_journal_id)
{
	$sql = "UPDATE pos_purchases_journal SET invoice_status = 'CLOSED'  WHERE pos_purchases_journal_id=$pos_purchases_journal_id LIMIT 1";
	$result = transactionUpdateSQL($dbc,$po_sql);
	return $result;
}
function setTransactionPOStatus($dbc,$pos_purchase_order_id, $purchase_order_status)
{
	/* PO status can be INIT, OPEN, CLOSED or DRAFT or DELETED*/
	$po_sql = "UPDATE pos_purchase_orders SET purchase_order_status = '" . $purchase_order_status ."'  WHERE pos_purchase_order_id=$pos_purchase_order_id LIMIT 1";
	$result = transactionUpdateSQL($dbc,$po_sql);
	return $result;
}
function getTransactionAccountBalance($dbc,$pos_account_id)
{
	//the CREDITS come from payments_journal
	//the DEBITS come from the payment_journal looked up to the invoice where the invoice is associated with the account id
	//the balance is from the balance_journal
	
$sql="	

	SELECT COALESCE(
	(SELECT COALESCE(SUM(payment_amount),0) FROM pos_payments_journal WHERE pos_account_id = $pos_account_id)
	
	- (SELECT COALESCE(SUM(payment_amount),0) FROM pos_payments_journal
	LEFT JOIN pos_invoice_to_payment
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_purchases_journal
	ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
	WHERE pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL' AND pos_purchases_journal.pos_account_id = $pos_account_id) 
	
	- (SELECT COALESCE(SUM(payment_amount),0) FROM pos_payments_journal
	LEFT JOIN pos_invoice_to_payment
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_general_journal
	ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
	WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL' AND pos_general_journal.pos_account_id = $pos_account_id)
	
	+ (SELECT COALESCE(balance_amount,0) FROM  pos_account_balances WHERE pos_account_id = $pos_account_id ORDER BY balance_date DESC LIMIT 1) ,0)
	
	AS account_balance
	
	";

	return getTransactionSingleValueSQL($dbc,$sql);
	
}
/**********************************ACCOUNTING************************************/
function getTransactionGeneralJournalData($dbc, $pos_general_journal_id)
{
	$sql = "SELECT * FROM pos_general_journal WHERE pos_general_journal_id = $pos_general_journal_id";
	return getTransactionSQL($dbc, $sql);
}
function getTransactionPaymentsJournalDataFromGeneralJournal($dbc, $pos_general_journal_id)
{
	$sql = "SELECT pos_payments_journal.payment_amount, pos_payments_journal.pos_account_id FROM pos_payments_journal 
	LEFT JOIN pos_invoice_to_payment
	ON pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	LEFT JOIN pos_general_journal
	ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
	WHERE pos_general_journal.pos_general_journal_id = $pos_general_journal_id
	AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL'
	";
	return getTransactionSQL($dbc, $sql);
}
function getTransactionPaymentsJournalData($dbc, $pos_payments_journal_id)
{
	$sql = "SELECT pos_payments_journal.payment_amount, pos_payments_journal.pos_account_id FROM pos_payments_journal
				WHERE pos_payments_journal.pos_payments_journal_id = $pos_payments_journal_id";
	return getTransactionSQL($dbc, $sql);	
}
function getTransactionGeneralJournalDataFromPaymentsJournal($dbc, $pos_payments_journal_id)
{
	$sql = "SELECT pos_general_journal.invoice_due_date, pos_general_journal.pos_general_journal_id, pos_general_journal.entry_amount, pos_general_journal.pos_chart_of_accounts_id, pos_general_journal.supplier, pos_general_journal.description FROM pos_general_journal
	LEFT JOIN pos_invoice_to_payment
	ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
	LEFT JOIN pos_payments_journal
	ON pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	WHERE pos_payments_journal.pos_payments_journal_id = $pos_payments_journal_id AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL' ORDER BY pos_general_journal.invoice_due_date
	";
	return getTransactionSQL($dbc, $sql);
}
function createExpensePostToGeneralLedgerArray($dbc, $gj_data, $payments)
{

	$counter = 0;
	if($gj_data[0]['entry_amount']<0)
	{
		//reverse the debits and credits
		//DEBIT the account used to pay
		for($i=0;$i<sizeof($payments);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($payment[$i]['pos_account_id']);
			$general_ledger_post_array[$counter]['pos_account_id'] = $payments[$i]['pos_account_id'];
			$general_ledger_post_array[$counter]['DEBIT'] = -$payments[$i]['payment_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Refund to account ' . getAccountName($payment[$i]['pos_account_id']). ':' .accountURLLink($payments[$i]['pos_account_id']). ' FROM ' .$gj_data[0]['supplier'] . ' FOR ' . $gj_data[0]['description']);
			$counter++;
		}
		for($i=0;$i<sizeof($gj_data);$i++)
		{
			//CREDIT the expense account
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = $gj_data[$i]['pos_chart_of_accounts_id'];
			$general_ledger_post_array[$counter]['CREDIT'] = -$gj_data[$i]['entry_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Return or credit from ' .$gj_data[$i]['supplier'] . ' for ' . $gj_data[$i]['description']);
			$counter++;
		}
	}
	else
	{
		//DEBIT the expense account
		for($i=0;$i<sizeof($gj_data);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = $gj_data[$i]['pos_chart_of_accounts_id'];
			$general_ledger_post_array[$counter]['DEBIT'] = $gj_data[$i]['entry_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput( "Purchase from " .$gj_data[$i]['supplier'] . " for " . $gj_data[$i]['description']);
			$counter++;
		}
		//CREDIT the payment account
		for($i=0;$i<sizeof($payments);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($payments[$i]['pos_account_id']);
			$general_ledger_post_array[$counter]['pos_account_id'] = $payments[$i]['pos_account_id'];
			$general_ledger_post_array[$counter]['CREDIT'] = $payments[$i]['payment_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Payment from ' . getAccountName($payments[$i]['pos_account_id']). ':' .accountURLLink($payments[$i]['pos_account_id']). ' to ' .$gj_data[0]['supplier'] . ' for ' . $gj_data[0]['description']);
			$counter++;
		}
	}
	return $general_ledger_post_array;
}
function reverseExpensePostToGeneralLedgerArray($dbc,$gj_data, $payments)
{
	$counter = 0;
	if($gj_data[0]['entry_amount']<0)
	{

		//DEBIT the expense account
		$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = $gj_data[0]['pos_chart_of_accounts_id'];
		$general_ledger_post_array[$counter]['DEBIT'] = -$gj_data[0]['entry_amount'];
		$general_ledger_post_array[$counter]['description'] = scrubInput('Edited Entry - REVERSE Return or credit from ' .$gj_data[0]['supplier'] . ' for ' . $gj_data[0]['description']);
		$counter++;
		//CREDIT the account used to pay
		for($i=0;$i<sizeof($payments);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($payment[$i]['pos_account_id']);
			$general_ledger_post_array[$counter]['pos_account_id'] = $payments[$i]['pos_account_id'];
			$general_ledger_post_array[$counter]['CREDIT'] = -$payments[$i]['payment_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Edited Entry - REVERSE Refund to account ' . getAccountName($payment[$i]['pos_account_id']). ':' .accountURLLink($payments[$i]['pos_account_id']). ' FROM ' .$gj_data[0]['supplier'] . ' FOR ' . $gj_data[0]['description']);
			$counter++;
		}
	}
	else
	{
		//DEBIT the payment account
		for($i=0;$i<sizeof($payments);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($payments[$i]['pos_account_id']);
			$general_ledger_post_array[$counter]['pos_account_id'] = $payments[$i]['pos_account_id'];
			$general_ledger_post_array[$counter]['DEBIT'] = $payments[$i]['payment_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Edited Entry - REVERSE Payment from ' . getAccountName($payments[$i]['pos_account_id']). ':' .accountURLLink($payments[$i]['pos_account_id']). ' to ' .$gj_data[0]['supplier'] . ' for ' . $gj_data[0]['description']);
			$counter++;
		}
		//CREDIT the expense account
		$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = $gj_data[0]['pos_chart_of_accounts_id'];
		$general_ledger_post_array[$counter]['CREDIT'] = $gj_data[0]['entry_amount'];
		$general_ledger_post_array[$counter]['description'] = scrubInput( "Edited Entry - REVERSE Purchase from " .$gj_data[0]['supplier'] . " for " . $gj_data[0]['description']);
		$counter++;

	}
	return $general_ledger_post_array;
}
function getInvoiceDataFromPaymentsJournal($dbc, $pos_payments_journal_id)
{
}
function createPaymentsPostToGeneralLedgerArray($dbc, $payments)
{

	$counter = 0;
	if($payments[0]['payment_amount']<0)
	{
		//reverse the debits and credits
		//DEBIT the account used to pay
		for($i=0;$i<sizeof($payments);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($payment[$i]['pos_account_id']);
			$general_ledger_post_array[$counter]['pos_account_id'] = $payments[$i]['pos_account_id'];
			$general_ledger_post_array[$counter]['DEBIT'] = -$payments[$i]['payment_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Refund to account ' . getAccountName($payment[$i]['pos_account_id']). ':' .accountURLLink($payments[$i]['pos_account_id']));
			$counter++;
		}
	}
	else
	{
		//CREDIT the payment account
		for($i=0;$i<sizeof($payments);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($payments[$i]['pos_account_id']);
			$general_ledger_post_array[$counter]['pos_account_id'] = $payments[$i]['pos_account_id'];
			$general_ledger_post_array[$counter]['CREDIT'] = $payments[$i]['payment_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Payment from ' . getAccountName($payments[$i]['pos_account_id']). ':' .accountURLLink($payments[$i]['pos_account_id']));
			$counter++;
		}
	}
	return $general_ledger_post_array;
}
function reversePaymentPostToGeneralLedgerArray($dbc,$payments, $gj_data)
{
	$counter = 0;
	if($payments[0]['payment_amount']<0)
	{
		//DEBIT the expense account
		for($i=0;$i<sizeof($gj_data);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = $gj_data[$i]['pos_chart_of_accounts_id'];
			$general_ledger_post_array[$counter]['DEBIT'] = -$gj_data[$i]['entry_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Edited Entry - REVERSE Return or credit from ' .$gj_data[$i]['supplier'] . ' for ' . $gj_data[$i]['description']);
			$counter++;
		}
		//CREDIT the account used to pay
		for($i=0;$i<sizeof($payments);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($payment[$i]['pos_account_id']);
			$general_ledger_post_array[$counter]['pos_account_id'] = $payments[$i]['pos_account_id'];
			$general_ledger_post_array[$counter]['CREDIT'] = -$payments[$i]['payment_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Edited Entry - REVERSE Refund to account ' . getAccountName($payment[$i]['pos_account_id']). ':' .accountURLLink($payments[$i]['pos_account_id']). ' FROM ' .$gj_data[0]['supplier'] . ' FOR ' . $gj_data[0]['description']);
			$counter++;
		}
	}
	else
	{
		//DEBIT the payment account
		for($i=0;$i<sizeof($payments);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($payments[$i]['pos_account_id']);
			$general_ledger_post_array[$counter]['pos_account_id'] = $payments[$i]['pos_account_id'];
			$general_ledger_post_array[$counter]['DEBIT'] = $payments[$i]['payment_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput('Edited Entry - REVERSE Payment from ' . getAccountName($payments[$i]['pos_account_id']). ':' .accountURLLink($payments[$i]['pos_account_id']). ' to ' .$gj_data[0]['supplier'] . ' for ' . $gj_data[0]['description']);
			$counter++;
		}
		//CREDIT the expense account
		for($i=0;$i<sizeof($gj_data);$i++)
		{
			$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = $gj_data[$i]['pos_chart_of_accounts_id'];
			$general_ledger_post_array[$counter]['CREDIT'] = $gj_data[$i]['entry_amount'];
			$general_ledger_post_array[$counter]['description'] = scrubInput( "Edited Entry - REVERSE Purchase from " .$gj_data[$i]['supplier'] . " for " . $gj_data[$i]['description']);
			$counter++;
		}

	}
	return $general_ledger_post_array;
}


function checkGeneralLedgerARrayForBalance($general_ledger_post_array)
{
	$debit_sum = 0;
	$credit_sum = 0;
	for($i=0;$i<sizeof($general_ledger_post_array);$i++)
	{
		if(isset($general_ledger_post_array[$i]['DEBIT']))
		{
			$debit_sum = $debit_sum + $general_ledger_post_array[$i]['DEBIT'];
		}
		if(isset($general_ledger_post_array[$i]['CREDIT']))
		{
			$credit_sum = $credit_sum + $general_ledger_post_array[$i]['CREDIT'];
		}
	}
	if (($debit_sum - $credit_sum) != 0)
	{
		trigger_error("General Ledger Post Array Does Not balance");
	}
}

function createInvenetoryPostToGeneralLedgerSQLString($dbc, $value_received, $pos_purchase_order_id)
{
	//entering merch
	// accounting events:
	//if invoice received
	//debit inventory credit expecting inventory
	//otherwise
	//debit inventory credit pending accounts payable
	
	// is the value the same of the received and the invoice?
	// we can only debit the amount we received. If there is a discrepancy it stays in the account
		
	//Alternatively an invoice may arrive before the merchandise
	//To find this information we would check the received status of the selected po(s)
	//If not received we would Debit Pending Inventory account and Credit the Accounts Payable
	//If it has been received we need to debit the temporary accounts payable account and creidt the accounts payable with the correct ID
	
	//we need to make sure there is a required account 'Merchadise Inventory' AND default 'Accounts Payable'
	
	if (!checkRequiredAccount('Merchandise Inventory') && !checkRequiredAccount('Accounts Payable') && !checkRequiredAccount('Pending Accounts Payable') && !checkRequiredAccount('Pending Merchandise Inventory'))
	{
		echo "MEGA ERROR - ACCOUNTS NOT SET UP";
		exit();
	}
	
	//try to get the pos_purchase_journal_id -> that will be the invoice -> that will get the invoice amount(s) and the account number(s)
	
	if ($value_received > 0)
	{
		$general_ledger_post_array[0]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromRequiredAccountName('Merchandise Inventory');	
		$general_ledger_post_array[0]['pos_account_id'] = 0;
		$general_ledger_post_array[0]['DEBIT'] = $value_received;
		$general_ledger_post_array[0]['description'] = scrubInput('Goods Received System PO# ' . createPOLink($pos_purchase_order_id));
		
		$invoices = getInvoicesFromPurchaseOrder($pos_purchase_order_id);
		$general_ledger_post_array[1]['pos_account_id'] = 0;
		$general_ledger_post_array[1]['CREDIT'] = $value_received;
	
		if (sizeof($invoices)>0)	
		{
			$general_ledger_post_array[1]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromRequiredAccountName('Pending Merchandise Inventory');	
			$general_ledger_post_array[1]['description'] = scrubInput('Expected Goods Received System PO# ' . createPOLink($pos_purchase_order_id));
		}
		else
		{
			$general_ledger_post_array[1]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromRequiredAccountName('Pending Accounts Payable');
			$general_ledger_post_array[1]['description'] = scrubInput('Pending Accounts Payable For Goods Received System PO# ' . createPOLink($pos_purchase_order_id));
		}
	}
	elseif ($value_received < 0)
	{	
		$general_ledger_post_array[1]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromRequiredAccountName('Merchandise Inventory');	
		$general_ledger_post_array[1]['pos_account_id'] = 0;
		$general_ledger_post_array[1]['CREDIT'] = -$value_received;
		$general_ledger_post_array[1]['description'] = scrubInput('Goods Removed System PO# ' . createPOLink($pos_purchase_order_id));
		
		$invoices = getInvoicesFromPurchaseOrder($pos_purchase_order_id);
		$general_ledger_post_array[0]['pos_account_id'] = 0;
		$general_ledger_post_array[0]['DEBIT'] = -$value_received;
	
		if (sizeof($invoices)>0)	
		{
			$general_ledger_post_array[0]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromRequiredAccountName('Pending Merchandise Inventory');	
			$general_ledger_post_array[0]['description'] = scrubInput('Expected Goods Removed System PO# ' . createPOLink($pos_purchase_order_id));
		}
		else
		{
			$general_ledger_post_array[0]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromRequiredAccountName('Pending Accounts Payable');
			$general_ledger_post_array[0]['description'] = scrubInput('Pending Accounts Payable For Goods Removed System PO# ' . createPOLink($pos_purchase_order_id));
		}
	}
	else
	{
		//0
	}
	return $general_ledger_post_array;
}
function getGeneralLedgerTransactionID($dbc)
{

	//At/before this point we need to lock the tables
 	$id = getTransactionSQL($dbc,"SELECT max(pos_general_ledger_transaction_id)+1 AS pos_general_ledger_transaction_id FROM pos_general_ledger FOR UPDATE");
	return $id[0]['pos_general_ledger_transaction_id'];
}
function getGeneralLedgerPostID($dbc)
{

	//At/before this point we need to lock the tables
 	$id = getTransactionSQL($dbc,"SELECT max(pos_general_ledger_post_id)+1 AS pos_general_ledger_post_id FROM pos_general_ledger FOR UPDATE");
	return $id[0]['pos_general_ledger_post_id'];
}
function postToGeneralLedgerSQLString($general_ledger_post_array)
{
	
	/* 
	This will pass back an array of sql statements
	array should look like this:
	$general_ledger_post_array[0]['date'] = $date;
	$general_ledger_post_array[0]['pos_general_ledger_transaction_id'] 	=
	$general_ledger_post_array[0]['DEBIT'] = $value_received;
	$general_ledger_post_array[0]['description'] = 'Goods Received System PO# ' . $pos_purchase_order_id;
	$general_ledger_post_array[0]['date'] = $date;
	$general_ledger_post_array[0]['pos_general_ledger_transaction_id'] 	=
	$general_ledger_post_array[1]['pos_chart_of_accounts_id'] = getChartOfAccountsID('Accounts Payable');
	$general_ledger_post_array[1]['CREDIT'] = $value_received;
	$general_ledger_post_array[1]['description'] = 'Accounts Payable For Goods Received System PO# ' . $pos_purchase_order_id;$pos_purchase_order_id;
	*/
	$result = array();
	for($i=0;$i<sizeof($general_ledger_post_array);$i++)
	{
		$result[$i] = simpleInsertSQLString('pos_general_ledger', $general_ledger_post_array[$i]);
	}
	return $result;
	
}

function getTransactionPurchasesJournalDataFromPaymentsJournalID($dbc, $pos_payments_journal_id)
{
	$sql = "SELECT pos_purchases_journal.pos_account_id, pos_purchases_journal.invoice_number, pos_purchases_journal.pos_purchases_journal_id, pos_purchases_journal.invoice_amount, pos_purchases_journal.discount_applied
	FROM pos_purchases_journal 
	LEFT JOIN pos_invoice_to_payment
	ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
	LEFT JOIN pos_payments_journal
	ON pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	WHERE pos_payments_journal.pos_payments_journal_id = $pos_payments_journal_id 
	AND pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL'
	AND pos_purchases_journal.invoice_type='Regular'";
	return getTransactionSQL($dbc, $sql);
}
function getTransactionPurchasesJournalCreditMemoDataFromPaymentsJournalID($dbc, $pos_payments_journal_id)
{
	$sql = "SELECT pos_purchases_journal.pos_account_id, pos_purchases_journal.invoice_number, pos_purchases_journal.pos_purchases_journal_id, pos_purchases_journal.invoice_amount, pos_purchases_journal.discount_applied
	FROM pos_purchases_journal 
	LEFT JOIN pos_invoice_to_payment
	ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
	LEFT JOIN pos_payments_journal
	ON pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	WHERE pos_payments_journal.pos_payments_journal_id = $pos_payments_journal_id 
	AND pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL'
	AND pos_purchases_journal.invoice_type='Credit Memo'";
	return getTransactionSQL($dbc, $sql);
}
function createPaymentPostToGeneralLedger($dbc, $pos_payments_journal_id)
{
	$payment_data = getTransactionPaymentsJournalData($dbc, $pos_payments_journal_id);
	$purchases_journal_invoices = getTransactionPurchasesJournalDataFromPaymentsJournalID($dbc, $pos_payments_journal_id);
	$credit_memos = getTransactionPurchasesJournalCreditMemoDataFromPaymentsJournalID($dbc, $pos_payments_journal_id);
	$pos_account_id = $purchases_journal_invoices[0]['pos_account_id'];
	$counter = 0;
	
	//for each invoice, DEBIT  the mfg account_id the total amount
	// CREDIT discounts account 
	
	//for each CREDIT the mfg account the amount of the credit memos
	//CREDIT the payment account id

	for($i=0;$i<sizeof($purchases_journal_invoices);$i++)
	{
		//DEBIT the invoice account
		$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($purchases_journal_invoices[$i]['pos_account_id']);
		$general_ledger_post_array[$counter]['pos_account_id'] = $purchases_journal_invoices[$i]['pos_account_id'];
		$general_ledger_post_array[$counter]['DEBIT'] = $purchases_journal_invoices[$i]['invoice_amount'];
		$general_ledger_post_array[$counter]['description'] = scrubInput( "Payment To " .getAccountName($purchases_journal_invoices[$i]['pos_account_id']) . " for invoice system id # " . 	createPJLink($purchases_journal_invoices[$i]['invoice_number']) . 'Invoice # '. $purchases_journal_invoices[$i]['invoice_number'] . ' Payment ID: ' . createPaymentJournalLink($pos_payments_journal_id));
		$counter++;
		
	}
	for($i=0;$i<sizeof($purchases_journal_invoices);$i++)
	{
		//CREDIT the discounts account
		
		$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromRequiredAccountName('Merchandise Inventory Discounts');
		$general_ledger_post_array[$counter]['pos_account_id'] = 0;
		$general_ledger_post_array[$counter]['CREDIT'] = $purchases_journal_invoices[$i]['discount_applied'];
		$general_ledger_post_array[$counter]['description'] = scrubInput( "Merchandise Inventory Discount From " .getAccountName($purchases_journal_invoices[$i]['pos_account_id']) . " for system id # " . 	createPJLink($purchases_journal_invoices[$i]['invoice_number']) . " Invoice # " . 	$purchases_journal_invoices[$i]['invoice_number']. ' Payment ID: ' . createPaymentJournalLink($pos_payments_journal_id));
		$counter++;
		
	}
	for($i=0;$i<sizeof($credit_memos);$i++)
	{
		//CREDIT the account for the credit memo
		$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($credit_memos[$i]['pos_account_id']);
		$general_ledger_post_array[$counter]['pos_account_id'] = $credit_memos[$i]['pos_account_id'];
		$general_ledger_post_array[$counter]['CREDIT'] = $credit_memos[$i]['invoice_amount'];
		$general_ledger_post_array[$counter]['description'] = scrubInput( "Payment using credit memo from " .getAccountName($credit_memos[$i]['pos_account_id']) . " system invoice id # " . 	createPJLink($purchases_journal_invoices[$i]['invoice_number']) . " Invoice # " . 	$purchases_journal_invoices[$i]['invoice_number']. ' Payment ID: ' . createPaymentJournalLink($pos_payments_journal_id));
		$counter++;
		
	}
	//CREDIT the payment account
	$general_ledger_post_array[$counter]['pos_chart_of_accounts_id'] = getChartOfAccountsIDFromAccountID($payment_data[0]['pos_account_id']);
	$general_ledger_post_array[$counter]['pos_account_id'] = $payment_data[0]['pos_account_id'];
	$general_ledger_post_array[$counter]['CREDIT'] = $payment_data[0]['payment_amount'];
	$general_ledger_post_array[$counter]['description'] = scrubInput('Payment To ' . getAccountName(pos_account_id). ' Payment ID: ' . createPaymentJournalLink($pos_payments_journal_id));
	
	checkGeneralLedgerARrayForBalance($general_ledger_post_array);
	return $general_ledger_post_array;
}



?>