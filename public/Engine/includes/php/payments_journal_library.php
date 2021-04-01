<?php

function getManufacturerIdFromPaymentId($pos_payments_journal_id)
{
	$sql = "SELECT pos_manufacturer_id FROM pos_payments_journal
			WHERE pos_payments_journal_id = $pos_payments_journal_id ";
		return getSingleValueSQL($sql);
}
function getPayeeAccountIdFromPaymentId($pos_payments_journal_id)
{
	$sql = "SELECT pos_payee_account_id FROM pos_payments_journal
			WHERE pos_payments_journal_id = $pos_payments_journal_id ";
		return getSingleValueSQL($sql);
}
function paymentStatusSelect($name, $type, $option_all ='off')
{
	$types = getPaymentStatusOptions();

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';

	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($type == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Status</option>';
	}
	for($i = 0;$i < sizeof($types); $i++)
	{
		$html .= '<option value="' . $types[$i] . '"';
		if ( ($types[$i] == $type) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $types[$i]. '</option>';
	}
	$html .= '</select>';
	return $html;
}
function appliedStatusSelect($name, $type, $option_all ='off')
{
	$types = getAppliedStatusOptions();

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';

	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($type == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Status</option>';
	}
	for($i = 0;$i < sizeof($types); $i++)
	{
		$html .= '<option value="' . $types[$i] . '"';
		if ( ($types[$i] == $type) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $types[$i]. '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getAppliedStatusOptions()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_payments_journal'
AND COLUMN_NAME = 'applied_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function getPaymentStatusOptions()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_payments_journal'
AND COLUMN_NAME = 'payment_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function getPaymentAmount($pos_payments_journal_id)
{
	$sql = "SELECT payment_amount FROM pos_payments_journal WHERE pos_payments_journal_id = $pos_payments_journal_id";
	return getSingleValueSQL($sql);
}
function getUnpaidPurchaseInvoicesNotIncludedInPayment($pos_payments_journal_id, $pos_account_id)
{


	//need to get the account or manufacturer id?
	
	//correct answer is the account id....
	//$pos_account_id = getManufacturerAccount($pos_manufacturer_id);

//have to remove pos_invoice_to_payment.comments as comments_for_applied, as it is causing problems with multiple results....

	$tmp_sql = "CREATE TEMPORARY TABLE 
 invoices 
 
 SELECT invoice_amount, pos_purchases_journal.pos_purchases_journal_id,pos_purchases_journal.invoice_number,
   
    (SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_payments_journal_id != '$pos_payments_journal_id' AND pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL') as applied_amount_from_other_payments, 
    

    
    	(SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_payments_journal_id = '$pos_payments_journal_id' AND pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL') as applied_amount_from_this_payment,
 
 (SELECT COALESCE(sum(pos_invoice_to_credit_memo.applied_amount),0) FROM  pos_invoice_to_credit_memo WHERE  pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_credit_memo.pos_purchases_journal_invoice_id ) as credit_memos_applied,
 
  discount_applied, discount_lost
 
FROM pos_purchases_journal 
LEFT JOIN pos_invoice_to_payment 
ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id

WHERE 

 pos_purchases_journal.pos_purchases_journal_id NOT IN (SELECT pos_journal_id FROM pos_invoice_to_payment WHERE pos_payments_journal_id = '$pos_payments_journal_id')
AND
pos_purchases_journal.payment_status = 'UNPAID'
AND
pos_account_id = $pos_account_id
ORDER BY pos_purchases_journal.pos_purchases_journal_id ASC
			
			;";
			
	
$tmp_select_sql = "SELECT distinct *, invoice_amount - discount_applied - applied_amount_from_other_payments - applied_amount_from_this_payment - credit_memos_applied as applied_amount_remaining FROM invoices";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}
function getUnpaidGeneralInvoicesNotIncludedInPayment($pos_payments_journal_id, $pos_account_id)
{


	//need to get the account or manufacturer id?
	
	//correct answer is the account id....
	//$pos_account_id = getManufacturerAccount($pos_manufacturer_id);

	$tmp_sql = "CREATE TEMPORARY TABLE 
 invoices 
 
 SELECT entry_amount, pos_general_journal.pos_general_journal_id,pos_general_journal.invoice_number,pos_invoice_to_payment.comments as comments_for_applied,
   
    (SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_payments_journal_id != '$pos_payments_journal_id' AND pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL') as applied_amount_from_other_payments, 
    

    
    	(SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_payments_journal_id = '$pos_payments_journal_id' AND pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL') as applied_amount_from_this_payment,
 
0 as credit_memos_applied,
 
  discount_applied, discount_lost
 
FROM pos_general_journal 
LEFT JOIN pos_invoice_to_payment 
ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id

WHERE 

 pos_general_journal.pos_general_journal_id NOT IN (SELECT pos_journal_id FROM pos_invoice_to_payment WHERE pos_payments_journal_id = '$pos_payments_journal_id')
AND
pos_general_journal.payment_status = 'UNPAID'
AND
pos_account_id = $pos_account_id
ORDER BY pos_general_journal.pos_general_journal_id ASC
			
			;";
			
$tmp_select_sql = "SELECT distinct *, entry_amount - discount_applied - applied_amount_from_other_payments - applied_amount_from_this_payment - credit_memos_applied as applied_amount_remaining FROM invoices";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}
function getPurchasesInvoicesLinkedToPaymentSQL($pos_payments_journal_id)
{
		$tmp_sql = "CREATE TEMPORARY TABLE 
 invoices 
 
 SELECT invoice_amount, pos_purchases_journal.pos_purchases_journal_id,pos_purchases_journal.invoice_number,pos_invoice_to_payment.comments as comments_for_applied,
   
    (SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_payments_journal_id != '$pos_payments_journal_id' AND pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL') as applied_amount_from_other_payments, 

      	(SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_payments_journal_id = '$pos_payments_journal_id' AND pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL') as applied_amount_from_this_payment,
 
 (SELECT COALESCE(sum(pos_invoice_to_credit_memo.applied_amount),0) FROM  pos_invoice_to_credit_memo WHERE  pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_credit_memo.pos_purchases_journal_invoice_id ) as credit_memos_applied,
 
 discount_applied, discount_lost
  
FROM pos_purchases_journal 
LEFT JOIN pos_invoice_to_payment 
ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
WHERE pos_invoice_to_payment.pos_payments_journal_id = $pos_payments_journal_id ORDER BY pos_purchases_journal.pos_purchases_journal_id ASC
			
			;";
		return $tmp_sql;
}
function getPurchasesInvoicesLinkedToPayment($pos_payments_journal_id)
{
	//we need to know the total amount applied to invoices, the total ordered, the total received, the remaining amount to be applied the total canceled
	//ordered_amoount
	//received_amount
	//applied_amount
	//applied_amount_remaining
	//the invoice is going to be for the sum of the ordered - cancled 
	// received amount is for info only
	

	$tmp_sql =  getPurchasesInvoicesLinkedToPaymentSQL($pos_payments_journal_id);		
	
$tmp_select_sql = "SELECT *, invoice_amount - discount_applied - applied_amount_from_other_payments - applied_amount_from_this_payment - credit_memos_applied as applied_amount_remaining FROM invoices";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}
function getGeneralInvoicesLinkedToPaymentSQL($pos_payments_journal_id)
{
		$tmp_sql = "CREATE TEMPORARY TABLE 
 invoices 
 
 SELECT entry_amount, pos_general_journal.pos_general_journal_id,pos_general_journal.invoice_number,pos_invoice_to_payment.comments as comments_for_applied,
   
    (SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_payments_journal_id != '$pos_payments_journal_id' AND pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL') as applied_amount_from_other_payments, 

      	(SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_payments_journal_id = '$pos_payments_journal_id' AND pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL') as applied_amount_from_this_payment,
 
0 as credit_memos_applied,
 
 discount_applied, discount_lost
  
FROM pos_general_journal 
LEFT JOIN pos_invoice_to_payment 
ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
WHERE pos_invoice_to_payment.pos_payments_journal_id = $pos_payments_journal_id ORDER BY pos_general_journal.pos_general_journal_id ASC
			
			;";
		return $tmp_sql;
}
function getGeneralInvoicesLinkedToPayment($pos_payments_journal_id)
{
	//we need to know the total amount applied to invoices, the total ordered, the total received, the remaining amount to be applied the total canceled
	//ordered_amoount
	//received_amount
	//applied_amount
	//applied_amount_remaining
	//the invoice is going to be for the sum of the ordered - cancled 
	// received amount is for info only
	

	$tmp_sql =  getGeneralInvoicesLinkedToPaymentSQL($pos_payments_journal_id);		
	
$tmp_select_sql = "SELECT *, entry_amount - discount_applied - applied_amount_from_other_payments - applied_amount_from_this_payment - credit_memos_applied as applied_amount_remaining FROM invoices";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}
function getPurchasesInvoiceForNewPayment($pos_purchases_journal_id)
{
	//we need to know the total amount applied to invoices, the total ordered, the total received, the remaining amount to be applied the total canceled
	//ordered_amoount
	//received_amount
	//applied_amount
	//applied_amount_remaining
	//the invoice is going to be for the sum of the ordered - cancled 
	// received amount is for info only
	
	$tmp_sql = "CREATE TEMPORARY TABLE 
 invoices 
 
 SELECT invoice_amount, pos_purchases_journal.pos_purchases_journal_id,pos_purchases_journal.invoice_number,pos_invoice_to_payment.comments as comments_for_applied,
   
    (SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL') as applied_amount_from_other_payments, 

      	0 as applied_amount_from_this_payment,
 
 (SELECT COALESCE(sum(pos_invoice_to_credit_memo.applied_amount),0) FROM  pos_invoice_to_credit_memo WHERE  pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_credit_memo.pos_purchases_journal_invoice_id ) as credit_memos_applied,
 
  discount_applied, discount_lost
  
FROM pos_purchases_journal 
LEFT JOIN pos_invoice_to_payment 
ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
WHERE pos_purchases_journal_id = $pos_purchases_journal_id ORDER BY pos_purchases_journal.pos_purchases_journal_id ASC
			
			;";
			
	
$tmp_select_sql = "SELECT DISTINCT *, invoice_amount - discount_applied - applied_amount_from_other_payments - applied_amount_from_this_payment - credit_memos_applied as applied_amount_remaining FROM invoices";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}
function getGeneralInvoiceForNewPayment($pos_general_journal_id)
{
	//we need to know the total amount applied to invoices, the total ordered, the total received, the remaining amount to be applied the total canceled
	//ordered_amoount
	//received_amount
	//applied_amount
	//applied_amount_remaining
	//the invoice is going to be for the sum of the ordered - cancled 
	// received amount is for info only
	
	$tmp_sql = "CREATE TEMPORARY TABLE 
 invoices 
 
 SELECT entry_amount, pos_general_journal.pos_general_journal_id,pos_general_journal.invoice_number,pos_invoice_to_payment.comments as comments_for_applied,
   
    (SELECT COALESCE(sum(pos_invoice_to_payment.applied_amount),0) FROM  pos_invoice_to_payment WHERE pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL') as applied_amount_from_other_payments, 

      	0 as applied_amount_from_this_payment,
 
 0 as credit_memos_applied,
 
  discount_applied, discount_lost
  
FROM pos_general_journal 
LEFT JOIN pos_invoice_to_payment 
ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
WHERE pos_general_journal_id = $pos_general_journal_id ORDER BY pos_general_journal.pos_general_journal_id ASC
			
			;";
			
	
$tmp_select_sql = "SELECT DISTINCT *, entry_amount - discount_applied - applied_amount_from_other_payments - applied_amount_from_this_payment - credit_memos_applied as applied_amount_remaining FROM invoices";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}
function setPaymentAppliedStatus($pos_payments_journal_id)
{
	//payment amount
	$payment_amount = getPaymentAmount($pos_payments_journal_id);
	$payment_applied_amount = getSingleValueSQL("SELECT sum(applied_amount) from pos_invoice_to_payment WHERE pos_payments_journal_id = $pos_payments_journal_id");
	
	if(abs($payment_amount - $payment_applied_amount)< 0.00001)
	{
		$status = 'APPLIED';
	}
	elseif ($payment_applied_amount < $payment_amount)
	{
			$status = 'UNAPPLIED';
			
	}
	else
	{
		$status = 'OVER APPLIED';
	}
	$sql = "UPDATE pos_payments_journal SET applied_status = '$status' WHERE pos_payments_journal_id = $pos_payments_journal_id";
	runSQL($sql);		
}
function getInvoicesLinkedToPayment($pos_payments_journal_id)
{
	$sql = "SELECT pos_journal_id, source_journal, applied_amount,
			IF(source_journal='GENERAL JOURNAL', pos_general_journal.entry_amount, pos_purchases_journal.invoice_amount) as invoice_amount,
			IF(source_journal='GENERAL JOURNAL', pos_general_journal.description, pos_purchases_journal.invoice_number) as invoice_number
			 FROM pos_invoice_to_payment 
			 LEFT JOIN pos_general_journal
			 ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
			 LEFT JOIN pos_purchases_journal
			 ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
			 WHERE pos_payments_journal_id = $pos_payments_journal_id";
	return getSQL($sql);
}
function getPaymentsJournalData($pos_payments_journal_id)
{
	$sql = "SELECT * FROM pos_payments_journal WHERE pos_payments_journal_id=$pos_payments_journal_id";
	return getSQL($sql);
}
function getPayementsToPurcasesInvoices($pos_journal_id_array, $source_journal)
{
	$sql = "SELECT sum(applied_amount) FROM pos_invoice_to_payment WHERE pos_journal_id IN (". implode( $pos_journal_id_array ) . ") AND source_journal = '$source_journal'";
	return getSingleValueSQL($sql);
	
}
function getDiscountsAppliedToPurchasesInvoice($dbc, $pos_purchases_journal_id)
{
	$sql = "SELECT discount_applied FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id ";
	return getTransactionSingleValueSQL($dbc,$sql);
}
function getPaymentsAppliedToPurchasesInvoice($dbc, $pos_purchases_journal_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_invoice_to_payment WHERE pos_journal_id = $pos_purchases_journal_id AND source_journal = 'PURCHASES JOURNAL'";
	return getTransactionSingleValueSQL($dbc,$sql);
}
function getPreviousPaymentsAppliedToJournalEntry($pos_journal_id, $source_journal, $pos_payments_journal_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_invoice_to_payment WHERE pos_journal_id = $pos_journal_id AND source_journal = '$source_journal' AND pos_payments_journal_id != $pos_payments_journal_id";
	return getSingleValueSQL($sql);
	
}
function getInvoicePaymentApplied($pos_journal_id, $source_journal)
{
	$sql = "SELECT sum(applied_amount) FROM pos_invoice_to_payment WHERE pos_journal_id = $pos_journal_id AND source_journal = '$source_journal'";
	return getSingleValueSQL($sql);
}
function getExpenseInvoicePaymentApplied($pos_general_journal_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_invoice_to_payment WHERE pos_journal_id = $pos_general_journal_id AND source_journal = 'GENERAL JOURNAL'";
	return getSingleValueSQL($sql);
}
function whatJournalIsThePaymentFor($pos_payments_journal_id)
{
	//the answer is GENERAL JOURNAL, PURCHASES JOURNAL, or NONE
	$sql = "
	
			SELECT source_journal
			FROM pos_invoice_to_payment
			WHERE pos_payments_journal_id = $pos_payments_journal_id
			
			";
				
	$data = getSQL($sql);
	if (sizeof($data)>0)
	{
		return $data[0]['source_journal'];
	}
	else
	{
		return 'NONE';
	}
}
function getPaymentSourceJournal($pos_payments_journal_id)
{
	$sql = "SELECT source_journal FROM pos_payments_journal WHERE pos_payments_journal_id = $pos_payments_journal_id";
	return getSingleValueSQL($sql);
}
function findOutWhatJournalThePaymentIsLinkedTo($pos_payments_journal_id)
{
	//we can first check the applied payment list and go with that.
	//next we will check the account the payment went to...
	$source_journal = whatJournalIsThePaymentFor($pos_payments_journal_id);


	if ($source_journal == 'NONE')
	{
		$source_journal = whatJournalisTheAccountLinkedTo($pos_payments_journal_id);
	}
	return $source_journal;
}
function whatJournalisTheAccountLinkedTo($pos_payments_journal_id)
{
	//get the account id
	$pos_payee_account_id = getPayeeAccountIdFromPaymentId($pos_payments_journal_id);
	//what is the account type?
	$account_type_name = getAccountTypeName($pos_payee_account_id);
	//really there should only be inventory and expense here
	if ($account_type_name == 'Inventory Account')
	{
		return 'PURCHASES JOURNAL';
	}
	else if ($account_type_name == 'Expense Account')
	{
		return 'GENERAL JOURNAL';
	}
	else
	{
		return 'NONE';
	}
}
function deletePayment($dbc, $pos_payments_journal_id)
{
	

	$gj_data = getTransactionGeneralJournalDataFromPaymentsJournal($dbc, $pos_payments_journal_id);

	for($i=0;$i<sizeof($gj_data);$i++)
	{
		$g_journal_insert_array = array('invoice_status' => 'OPEN', 'payment_status'=> 'UNPAID');
		$key_val_id['pos_general_journal_id'] = $gj_data[$i]['pos_general_journal_id'];
		$result[] = simpleTransactionUpdateSQL($dbc,'pos_general_journal', $key_val_id, $g_journal_insert_array);
	}

	$purchases_journal_data = getTransactionPurchasesJournalDataFromPaymentsJournalID($dbc, $pos_payments_journal_id);
	for($i=0;$i<sizeof($purchases_journal_data);$i++)
	{
		$purchases_journal_insert_array = array('invoice_status' => 'OPEN',
											'payment_status' => 'UNPAID');
		$key_val_id2['pos_purchases_journal_id'] = $purchases_journal_data[$i]['pos_purchases_journal_id'];
		$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', $key_val_id2, $purchases_journal_insert_array);
	}

	$sql2 = "DELETE FROM pos_invoice_to_payment WHERE pos_payments_journal_id=$pos_payments_journal_id";
	$results[] = runTransactionSQL($dbc,$sql2);
	$sql = "DELETE FROM pos_payments_journal WHERE pos_payments_journal_id = $pos_payments_journal_id";
	$results[] = runTransactionSQL($dbc,$sql);
	return $results;

}
function deletePurchasePayment($dbc, $pos_payments_journal_id)
{
	

	$purchases_journal_data = getTransactionPurchasesJournalDataFromPaymentsJournalID($dbc, $pos_payments_journal_id);
	for($i=0;$i<sizeof($purchases_journal_data);$i++)
	{
		$purchases_journal_insert_array = array('invoice_status' => 'OPEN',
											'payment_status' => 'UNPAID');
		$key_val_id2['pos_purchases_journal_id'] = $purchases_journal_data[$i]['pos_purchases_journal_id'];
		$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', $key_val_id2, $purchases_journal_insert_array);
	}

	$sql2 = "DELETE FROM pos_invoice_to_payment WHERE pos_payments_journal_id=$pos_payments_journal_id";
	$results[] = runTransactionSQL($dbc,$sql2);
	$sql = "DELETE FROM pos_payments_journal WHERE pos_payments_journal_id = $pos_payments_journal_id";
	$results[] = runTransactionSQL($dbc,$sql);
	
	return $results;

}
function tryToCloseInvoices($invoiceArray)
{
	for($i=0;$i<sizeof($invoiceArray);$i++)
		{
			$pos_purchases_journal_id = $invoiceArray[$i];
			if(!in_array($pos_purchases_journal_id, array('NULL', '', 'false')))
			{
				updateInvoicePaymentStatus($pos_purchases_journal_id);
				tryToClosePurchaseInvoice($pos_purchases_journal_id);
			}
		}
}
function tryToCloseExpenseInvoices($invoiceArray)
{
	for($i=0;$i<sizeof($invoiceArray);$i++)
		{
			$pos_general_journal_id = $invoiceArray[$i];
			if(!in_array($pos_general_journal_id, array('NULL', '', 'false')))
			{
				updateExpenseInvoicePaymentStatus($pos_general_journal_id);
				tryToCloseGeneralInvoice($pos_general_journal_id);

			}
		}
}
?>