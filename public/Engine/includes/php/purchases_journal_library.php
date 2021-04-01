<?php

function getCreditMemoAppliedTotal($pos_credit_memo_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_credit_memo_id = $pos_credit_memo_id";
	return getSingleValueSQL($sql);
}
function setPurchaseOrderInvoiceStatus($dbc, $pos_purchase_order_id, $status)
{
	$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id), array('invoice_status' => $status));
}
function setPOCreditMemoRequired($dbc, $pos_purchase_order_id, $zeroOrOne)
{
	$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id), array('credit_memo_required' => $zeroOrOne));
}
function setPurchaseJournalInvoicePaymnetStatus($dbc, $pos_purchases_journal_id, $status)
{
	$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', array('pos_purchases_journal_id' => $pos_purchases_journal_id), array('payment_status' => $status));
}
function setPurchaseJournalInvoiceStatus($dbc, $pos_purchases_journal_id, $status)
{
	$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', array('pos_purchases_journal_id' => $pos_purchases_journal_id), array('invoice_status' => $status));
}



function applyInvoiceToPO($dbc, $pos_purchases_journal_id, $pos_purchase_order_ids, $invoice_amount)
{
	//first thing we need to do is clear out all amounts applied from this invoice to purchase orders.
	
	$purchase_orders_effected = getSQL("SELECT pos_purchase_order_id, applied_amount FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id");
	//probably want to set all of those po's to incomplete
	for($i=0;$i<sizeof($purchase_orders_effected);$i++)
	{
		if($purchase_orders_effected[$i]['applied_amount'] != 0)
		{
		     setPurchaseOrderInvoiceStatus($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], 'INCOMPLETE');
		 }
	}
	$sql = "DELETE FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	$result[] = runtransactionsql($dbc, $sql);
	
	
	//first apply the amount to the oldest po it touches, and continue until the amount is exausted..
	$invoice_remainder = $invoice_amount;
	for($i=0;$i<sizeof($pos_purchase_order_ids);$i++)
	{
		if ($pos_purchase_order_ids[$i] != 'false')
		{
			//close off lowest number purchase orders first
			$po_total_due = getTotalReceivedOnPurchaseOrder($pos_purchase_order_ids[$i]);
			$invoice_amount_applied = getPurchaseOrderInvoicesAppliedDBC($dbc,$pos_purchase_order_ids[$i]);
			$po_remainder = $po_total_due - $invoice_amount_applied;
			if(abs($invoice_remainder - $po_remainder )< 0.00001)
			{
				//apply this amount.....
				$po_invoice_insert_array = array('applied_amount' => $po_remainder);
				$po_insert_array = array('invoice_status' => 'COMPLETE');
				$invoice_remainder = 0;
				
			}
			elseif ($invoice_remainder < $po_remainder)
			{
				//not fully paid
				$po_invoice_insert_array = array('applied_amount' => $invoice_remainder);
				$po_insert_array = array('invoice_status' => 'INCOMPLETE');
				$invoice_remainder = 0;
			}
			else
			{
				//paid with leftover
				
				$po_insert_array = array('invoice_status' => 'COMPLETE');
				$po_invoice_insert_array = array('applied_amount' => $po_remainder);
				$invoice_remainder = $invoice_remainder - $po_remainder;
				
			}
			$purchases_to_invoice_update = array('pos_purchases_journal_id' => $pos_purchases_journal_id,
								'pos_purchase_order_id' => $pos_purchase_order_ids[$i]);
			$purchases_to_invoice_update =array_merge($purchases_to_invoice_update,$po_invoice_insert_array);
			$result[] = simpleTransactionInsertSQL($dbc,'pos_purchases_invoice_to_po', $purchases_to_invoice_update);
			$key_val_id2['pos_purchase_order_id'] = $pos_purchase_order_ids[$i];
			$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchase_orders', $key_val_id2, $po_insert_array);						
		}
	}

}
function applyCreditMemoToPO($dbc, $pos_purchases_journal_id, $pos_purchase_order_ids, $credit_amount)
{
	//first thing we need to do is clear out all amounts applied from this invoice to purchase orders.
	$purchase_orders_effected = getSQL("SELECT pos_purchase_order_id, applied_amount FROM pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id");
	//probably want to set all of those po's to incomplete
	for($i=0;$i<sizeof($purchase_orders_effected);$i++)
	{
		if($purchase_orders_effected[$i]['applied_amount'] != 0)
		{
		     setPurchaseOrderInvoiceStatus($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], 'INCOMPLETE');
		     setPOCreditMemoRequired($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], '1');
		 }
	}
	$sql = "DELETE FROM pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	$result[] = runtransactionsql($dbc, $sql);
	//first apply the amount to the oldest po it touches, and continue until the amount is exausted..
	$credit_remainder = $credit_amount;
	for($i=0;$i<sizeof($pos_purchase_order_ids);$i++)
	{
		if ($pos_purchase_order_ids[$i] != 'false')
		{
			//close off lowest number purchase orders first
			$po_total_credit_due = getTotalCreditDueOnPurchaseOrder($pos_purchase_order_ids[$i]);
			$credit_amount_applied = getPurchaseOrderCreditMemosApplied($dbc,$pos_purchase_order_ids[$i]);
			$po_credit_remainder = $po_total_credit_due - $credit_amount_applied;
			if(abs($credit_remainder - $po_credit_remainder )< 0.00001)
			{
				//perfectly paid...
				$po_credit_memo_insert_array = array('applied_amount' => $po_credit_remainder);
				$po_insert_array = array('credit_memo_required' => '0');
				$credit_remainder = 0;
			}
			elseif ($credit_remainder < $po_credit_remainder)
			{
				//not fully paid
				$po_credit_memo_insert_array = array('applied_amount' => $credit_remainder);
				$po_insert_array = array('credit_memo_required' => '1');
				$credit_remainder = 0;
			}
			else
			{
				//paid with leftover
				$po_credit_memo_insert_array = array('applied_amount' => $po_credit_remainder);
				$po_insert_array = array('credit_memo_required' => '0');
				$credit_remainder = $credit_remainder - $po_credit_remainder;			
			}
			$purchases_to_credit_memo_update = array('pos_purchases_journal_id' => $pos_purchases_journal_id,
								'pos_purchase_order_id' => $pos_purchase_order_ids[$i]);
			$purchases_to_credit_memo_update =array_merge($purchases_to_credit_memo_update,$po_credit_memo_insert_array);
			$result[] = simpleTransactionInsertSQL($dbc,'pos_purchases_credit_memo_to_po', $purchases_to_credit_memo_update);
			$key_val_id2['pos_purchase_order_id'] = $pos_purchase_order_ids[$i];
			$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchase_orders', $key_val_id2, $po_insert_array);						
		}
	}
}
function applyPaymentToPurchaseInvoice($dbc, $pos_payment_journal_id, $pos_purchases_journal_ids,$payment_amount)
{
	//first thing we need to do is clear out all amounts applied from this payment to invoice.
	
	$invoices_effected = getSQL("SELECT pos_journal_id, applied_amount FROM pos_invoice_to_payment WHERE pos_payments_journal_id = $pos_payment_journal_id AND source_journal = 'PURCHASES JOURNAL'");
	//probably want to set all of those po's to incomplete
	for($i=0;$i<sizeof($invoices_effected);$i++)
	{
		if($invoices_effected[$i]['applied_amount'] != 0)
		{
		     setPurchaseJournalInvoicePaymnetStatus($dbc, $invoices_effected[$i]['pos_journal_id'], 'UNPAID');
		     setPurchaseJournalInvoiceStatus($dbc, $invoices_effected[$i]['pos_journal_id'], 'OPEN');
		 }
	}
	$sql = "DELETE FROM pos_invoice_to_payment WHERE pos_payments_journal_id = $pos_payment_journal_id AND source_journal = 'PURCHASES JOURNAL'";
	$result[] = runtransactionsql($dbc, $sql);
	
	//first apply the amount to the oldest po it touches, and continue until the amount is exausted..
	$payment_remainder = $payment_amount;
	for($i=0;$i<sizeof($pos_purchases_journal_ids);$i++)
	{
		if ($pos_purchases_journal_ids[$i] != 'false')
		{
			//close off lowest number purchase orders first
			$invoice_total_due = getPurchasesInvoiceTotal($pos_purchases_journal_ids[$i]);
			$discount_applied = getDiscountsAppliedToPurchasesInvoice($dbc,$pos_purchases_journal_ids[$i]);
			$payment_amount_applied = getPaymentsAppliedToPurchasesInvoice($dbc, $pos_purchases_journal_ids[$i]);
			$credit_amount_applied = getCreditMemosAppliedToPurchasesInvoice($pos_purchases_journal_ids[$i]);
			$invoice_remainder = $invoice_total_due - $payment_amount_applied - $credit_amount_applied -$discount_applied;
			if(abs($payment_remainder - $invoice_remainder )< 0.00001)
			{
				//apply this amount.....
				$payment_invoice_insert_array = array('applied_amount' => $invoice_remainder);
				$invoice_update_array = array('invoice_status' => 'CLOSED', 'payment_status' => 'PAID');
				$payment_remainder = 0;
				
			}
			elseif ($payment_remainder < $invoice_remainder)
			{
				//not fully paid
				$payment_invoice_insert_array = array('applied_amount' => $payment_remainder);
				$invoice_update_array = array('invoice_status' => 'OPEN','payment_status' => 'UNPAID');
				$payment_remainder = 0;
			}
			else
			{
				//paid with leftover
				
				$invoice_update_array = array('invoice_status' => 'CLOSED', 'payment_status' => 'PAID');
				$payment_invoice_insert_array = array('applied_amount' => $invoice_remainder);
				$payment_remainder = $payment_remainder - $invoice_remainder;
				
			}
			$invoice_to_payment_insert = array('pos_payments_journal_id' => $pos_payment_journal_id,
								'pos_journal_id' => $pos_purchases_journal_ids[$i],
								'source_journal' => 'PURCHASES JOURNAL');
			$invoice_to_payment_insert =array_merge($invoice_to_payment_insert,$payment_invoice_insert_array);
			$result[] = simpleTransactionInsertSQL($dbc,'pos_invoice_to_payment', $invoice_to_payment_insert);
			$key_val_id2['pos_purchases_journal_id'] = $pos_purchases_journal_ids[$i];
			$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', $key_val_id2, $invoice_update_array);						
		}
	}
}
function calculatePurchaseOrderInvoiceStatus($pos_purchase_order_id)
{
	//is the invoiceing complete?
	$total_invoices_applied = getSingleValueSQL("SELECT sum(applied_amount) from pos_purchases_invoice_to_po where pos_purchase_order_id = $pos_purchase_order_id");
	$total_received = getTotalReceivedOnPurchaseOrder($pos_purchase_order_id);
	//any credit memo's needed?
	$total_credit_memos_applied = getSingleValueSQL("SELECT sum(applied_amount) from pos_purchases_credit_memo_to_po where pos_purchase_order_id = $pos_purchase_order_id");
	$total_returned = getTotalCreditDueOnPurchaseOrder($pos_purchase_order_id);
	
	//regular
	if(abs($total_invoices_applied - $total_received) < 0.00001 && abs($total_credit_memos_applied - $total_returned) < 0.00001 && $total_invoices_applied != 0 && $total_received !=0)
	{
		$status = 'COMPLETE';
	}
	else
	{
		$status = 'INCOMPLETE';
	}
	
	//returns
	
	return simpleUpdateSQL('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id), array('invoice_status' => $status)); 
}
function tryToCompletePurchaseOrderInvoiceStatus($pos_purchase_order_id)
{
		$po_total_due = getTotalInvoicableOnPurchaseOrder($pos_purchase_order_id);
		$invoice_amount_applied = getPurchaseOrderInvoicesApplied($pos_purchase_order_id);
		
		//any credit memo's needed?
	$total_credit_memos_applied = getSingleValueSQL("SELECT sum(applied_amount) from pos_purchases_credit_memo_to_po where pos_purchase_order_id = $pos_purchase_order_id");
	$total_returned = getTotalCreditDueOnPurchaseOrder($pos_purchase_order_id);
		if (abs($invoice_amount_applied - $po_total_due)< 0.00001)
		{
			if(abs($total_credit_memos_applied - $total_returned) < 0.00001)
			{
				$status = 'COMPLETE';
			}
			else if ($total_credit_memos_applied < $total_returned)
			{
				$status = 'NEED CREDIT MEMO';
			}	
			else
			{
				//Goods returned incomplete
				$status = 'NEED TO RETURN GOODS';
				$status = 'COMPLETE';
			}
			
		}
		else if ($invoice_amount_applied > $po_total_due)
		{
			$status = 'OVER APPLIED';
		}else
		{
			$status = 'INCOMPLETE';
		}
		

		
	
		
	$po_insert_array = array('invoice_status' => $status);
	$key_val_id2['pos_purchase_order_id'] = $pos_purchase_order_id;
	$result[] = simpleUpdateSQL('pos_purchase_orders', $key_val_id2, $po_insert_array);	
		
		
}
function getPurchaseInvoicePaymentStatus($pos_purchases_journal_id)
{
	$payments_applied = "SELECT sum(applied_amount) FROM pos_invoice_to_payment WHERE pos_journal_id = $pos_purchases_journal_id AND source_journal = 'PURCHASES JOURNAL'";
	$credit_memos_applied = "SELECT sum(applied_amount) FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_invoice_id = $pos_purchases_journal_id";
	$total_payments = getSingleValueSQL($payments_applied) + getSingleValueSQL($credit_memos_applied);
	
	$invoice_total_sql = "SELECT (invoice_amount - discount_applied) as total from pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	$invoice_total = getSingleValueSQL($invoice_total_sql);
	if(abs($invoice_total - $total_payments)< 0.00001)
	{
		$status =  'PAID';
	}
	elseif ($invoice_total > $total_payments)
	{
		$status = 'UNPAID';
	}
	else
	{
		$status = 'OVERPAID';
  	}
  	return $status;
}
function getExpenseInvoicePaymentStatus($pos_general_journal_id)
{
	$payments_applied = "SELECT sum(applied_amount) FROM pos_invoice_to_payment WHERE pos_journal_id = $pos_general_journal_id AND source_journal = 'GENERAL JOURNAL'";
	//$credit_memos_applied = "SELECT sum(applied_amount) FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_invoice_id = $pos_purchases_journal_id";
	$total_payments = getSingleValueSQL($payments_applied) ;//+ getSingleValueSQL($credit_memos_applied);
	
	$invoice_total_sql = "SELECT (entry_amount - discount_applied) as total from pos_general_journal WHERE pos_general_journal_id = $pos_general_journal_id";
	$invoice_total = getSingleValueSQL($invoice_total_sql);
	if(abs($invoice_total - $total_payments)< 0.00001)
	{
		$status =  'PAID';
	}
	elseif ($invoice_total > $total_payments)
	{
		$status = 'UNPAID';
	}
	else
	{
		$status = 'OVERPAID';
  	}
  	return $status;
}
function updateInvoicePaymentStatus($pos_purchases_journal_id)
{
	$status['payment_status'] = getPurchaseInvoicePaymentStatus($pos_purchases_journal_id);
	$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
	$results[] = simpleUpdateSQL('pos_purchases_journal', $key_val_id, $status);
	//return  $status['payment_status'];
	
}
function updateExpenseInvoicePaymentStatus($pos_general_journal_id)
{
	$status['payment_status'] = getExpenseInvoicePaymentStatus($pos_general_journal_id);
	$key_val_id['pos_general_journal_id'] = $pos_general_journal_id;
	$results[] = simpleUpdateSQL('pos_general_journal', $key_val_id, $status);
	//return  $status['payment_status'];getExpenseInvoicePaymentStatus
	
}
function tryToClosePurchaseInvoice($pos_purchases_journal_id)
{
	//basically close it if it is paid and fully applied
	//what about ra's?
	$payment_status = getPurchaseInvoicePaymentStatus($pos_purchases_journal_id);
	$po_applied_status = getInvoiceAmountAppliedToPOStatus($pos_purchases_journal_id);
	if ( $payment_status == 'PAID' && $po_applied_status == 'COMPLETE')
	{
		$status = array('invoice_status' => 'CLOSED');
	}
	else
	{
		$status = array('invoice_status' => 'OPEN');
	}
	$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
	$results[] = simpleUpdateSQL('pos_purchases_journal', $key_val_id, $status);
}
function tryToCloseGeneralInvoice($pos_general_journal_id)
{
	//basically close it if it is paid and fully applied
	//what about ra's?
	$payment_status = getExpenseInvoicePaymentStatus($pos_general_journal_id);
	//$po_applied_status = getInvoiceAmountAppliedToPOStatus($pos_purchases_journal_id);
	if ( $payment_status == 'PAID' )//&& $po_applied_status == 'COMPLETE')
	{
		$status = array('invoice_status' => 'CLOSED');
	}
	else
	{
		$status = array('invoice_status' => 'OPEN');
	}
	$key_val_id['pos_general_journal_id'] = $pos_general_journal_id;
	$results[] = simpleUpdateSQL('pos_general_journal', $key_val_id, $status);
}
function getInvoiceTotal($pos_purchases_journal_id)
{
	$sql = "select invoice_amount FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}
function getShippingOnInvoice($pos_purchases_journal_id)
{
	$sql = "select shipping_amount FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}
function getFeesOnInvoice($pos_purchases_journal_id)
{
	$sql = "select fee_amount FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}
function getInvoiceAmountAppliedToPOStatus($pos_purchases_journal_id)
{
	$applied_amount = getsingleValueSQL("SELECT sum(applied_amount) FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id");
	$invoice = getPurchasesJournal($pos_purchases_journal_id);
	$invoice_total = $invoice[0]['invoice_amount'] - $invoice[0]['shipping_amount'] - $invoice[0]['fee_amount'];
	
	if(abs($invoice_total - $applied_amount)< 0.00001)
	{
		$status = 'COMPLETE';
	}
	elseif ($invoice_total > $applied_amount)
	{
		$status =  'INCOMPLETE';
	}
	else
	{
		$status = 'OVERPAID';
	}
	return $status;
}
function getOpenCreditMemos($pos_manufacturer_id, $pos_purchases_journal_id)
{
	$sql = "SELECT pos_purchases_journal_id as pos_purchases_journal_credit_memo_id, invoice_number as credit_memo_number, invoice_amount as credit_amount, (SELECT COALESCE(sum(applied_amount),0) FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_credit_memo_id = pos_purchases_journal.pos_purchases_journal_id AND pos_purchases_journal_invoice_id != $pos_purchases_journal_id) as total_applied_amount 
	FROM pos_purchases_journal
	 WHERE pos_manufacturer_id = $pos_manufacturer_id AND invoice_type ='Credit Memo' AND payment_status ='UNUSED'  ORDER BY invoice_amount ASC";
		return getSQL($sql);
}
function getCurrentlyAppliedCreditMemos($pos_purchases_journal_id)
{
	$sql = "SELECT invoice_number as credit_memo_number, pos_purchases_journal_invoice_id, pos_purchases_journal_credit_memo_id, applied_amount , invoice_amount as credit_amount,
	(SELECT COALESCE(sum(applied_amount),0) FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_credit_memo_id = pos_purchases_journal.pos_purchases_journal_id AND pos_purchases_journal_invoice_id != $pos_purchases_journal_id ) as total_applied_amount
			FROM pos_invoice_to_credit_memo 
			LEFT JOIN pos_purchases_journal ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_credit_memo.pos_purchases_journal_credit_memo_id
			WHERE pos_purchases_journal_invoice_id = $pos_purchases_journal_id";
	return getSQL($sql);
	
}
function createOpenInvoiceSelect($name, $pos_manufacturer_id, $pos_purchases_journal_array, $option_all ='off', $select_events = ' onchange="needToConfirm=true" ')
{

	$open_invoices = getOpenInvoices($pos_manufacturer_id);
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	
	$html .= '<option value ="false"';
		if (sizeof($pos_purchases_journal_array) == 0)
		{
			$html .= ' selected="selected"';
		}
	$html .= '>None Selected</option>';
	for($i = 0;$i < sizeof($open_invoices); $i++)
	{
		$html .= '<option value="' . $open_invoices[$i]['pos_purchases_journal_id'] . '"';
		for($k=0;$k<sizeof($pos_purchases_journal_array);$k++)
		{
			if ( ($open_invoices[$i]['pos_purchases_journal_id'] == $pos_purchases_journal_array[$k]['pos_purchases_journal_id']) ) 
			{
				$html .= ' selected="selected"';
			}
		}
		$due = $open_invoices[$i]['invoice_amount'] - $open_invoices[$i]['discount_applied'] -$open_invoices[$i]['payments_applied'] - $open_invoices[$i]['credit_amount'];
		$html .= '>Invoice #: ' . $open_invoices[$i]['invoice_number'] . ' Amount: ' .round($open_invoices[$i]['invoice_amount'],2) . ' Due: ' .$due. '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createUnpaidPurchasesInvoicesSelect($name, $pos_manufacturer_id, $pos_purchases_journal_array, $option_all ='off', $select_events = ' onchange="needToConfirm=true" ')
{

	$open_invoices = getOpenInvoices($pos_manufacturer_id);
	//need to add the invoices
	$full_list = array();
	$ids = array();
	$counter=0;
	for($i = 0;$i < sizeof($pos_purchases_journal_array); $i++)
	{
		$ids[] = $pos_purchases_journal_array[$i]['pos_purchases_journal_id'];
		$full_list[$counter]['pos_purchases_journal_id'] =$pos_purchases_journal_array[$i]['pos_purchases_journal_id'];
		$full_list[$counter]['invoice_amount'] =$pos_purchases_journal_array[$i]['invoice_amount'];
		$full_list[$counter]['discount_applied'] =$pos_purchases_journal_array[$i]['discount_applied'];
		$full_list[$counter]['payments_applied'] =$pos_purchases_journal_array[$i]['payments_applied'];
		$full_list[$counter]['invoice_number'] =$pos_purchases_journal_array[$i]['invoice_number'];
		$full_list[$counter]['credit_amount'] =$pos_purchases_journal_array[$i]['credit_amount'];
		$counter++;
	}
	for($i = 0;$i < sizeof($open_invoices); $i++)
	{
		if(!in_array($open_invoices[$i]['pos_purchases_journal_id'], $ids))
		{
			$full_list[$counter]['pos_purchases_journal_id'] =$open_invoices[$i]['pos_purchases_journal_id'];
			$full_list[$counter]['invoice_amount'] =$open_invoices[$i]['invoice_amount'];
			$full_list[$counter]['discount_applied'] =$open_invoices[$i]['discount_applied'];
			$full_list[$counter]['payments_applied'] =$open_invoices[$i]['payments_applied'];
			$full_list[$counter]['invoice_number'] =$open_invoices[$i]['invoice_number'];
			$full_list[$counter]['credit_amount'] =$open_invoices[$i]['credit_amount'];
			$counter++;
		}
	}

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	
	$html .= '<option value ="false"';
		if (sizeof($pos_purchases_journal_array) == 0)
		{
			$html .= ' selected="selected"';
		}
	$html .= '>None Selected</option>';
	for($i = 0;$i < sizeof($full_list); $i++)
	{
		$html .= '<option value="' . $full_list[$i]['pos_purchases_journal_id'] . '"';
		for($k=0;$k<sizeof($pos_purchases_journal_array);$k++)
		{
			if ( ($full_list[$i]['pos_purchases_journal_id'] == $pos_purchases_journal_array[$k]['pos_purchases_journal_id']) ) 
			{
				$html .= ' selected="selected"';
			}
		}
		$due = $full_list[$i]['invoice_amount'] - $full_list[$i]['discount_applied'] -$full_list[$i]['payments_applied'] - $full_list[$i]['credit_amount'];
		$html .= '>Invoice #: ' . $full_list[$i]['invoice_number'] . ' Amount: ' .round($full_list[$i]['invoice_amount'],2) . ' Due: ' .$due. '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getPOtoOPENPJLookup($pos_manufacturer_id)
{
	return getSQL(
"SELECT pos_purchases_invoice_to_po.pos_purchases_journal_id, pos_purchases_invoice_to_po.pos_purchase_order_id FROM pos_purchases_invoice_to_po 
		LEFT JOIN pos_purchases_journal
		ON pos_purchases_journal.pos_purchases_journal_id = pos_purchases_invoice_to_po.pos_purchases_journal_id
		WHERE pos_purchases_journal.invoice_status='OPEN' AND pos_purchases_journal.invoice_type ='Regular' AND pos_purchases_journal.payment_status != 'PAID' AND pos_purchases_journal.pos_manufacturer_id =$pos_manufacturer_id");
}

function getOpenInvoices($pos_manufacturer_id)
{
	$sql = "SELECT pos_purchases_journal_id, invoice_due_date, invoice_amount, invoice_number, discount_applied, 
		(SELECT COALESCE(sum(applied_amount),0) FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_invoice_id = pos_purchases_journal.pos_purchases_journal_id) as credit_amount,
		(SELECT COALESCE(sum(applied_amount),0) FROM pos_invoice_to_payment WHERE pos_journal_id = pos_purchases_journal.pos_purchases_journal_id AND source_journal = 'PURCHASES JOURNAL') as payments_applied
		 FROM pos_purchases_journal WHERE invoice_type ='Regular' AND (invoice_status='OPEN' OR payment_status != 'PAID') AND pos_manufacturer_id =$pos_manufacturer_id";
	return getSQL($sql);
}
function createCreditMemoSelect($name, $pos_manufacturer_id, $pos_purchases_journal_id, $option_all ='off', $select_events = ' onchange="needToConfirm=true" ')
{

	$credit_memos = getOpenCreditMemos($pos_manufacturer_id);
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_purchases_journal_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Credit Memos</option>';
	}
	$html .= '<option value ="false"';
		if ($pos_purchases_journal_id == 'false')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>None</option>';
	for($i = 0;$i < sizeof($credit_memos); $i++)
	{
		$html .= '<option value="' . $credit_memos[$i]['pos_purchases_journal_id'] . '"';
		if ( ($credit_memos[$i]['pos_purchases_journal_id'] == $pos_purchases_journal_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '> Invoice Number: ' . $credit_memos[$i]['invoice_number'] . ': Total $' . round($credit_memos[$i]['invoice_amount'],2) . ', Used $' . round($credit_memos[$i]['applied_amount'],2) . ', Remaining $' . round($credit_memos[$i]['invoice_amount'] - $credit_memos[$i]['applied_amount'],2) .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function getInvoiceType($pos_purchases_journal_id)
{
	$sql = "SELECT invoice_type FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}
function getInvoiceTypes()
{
	return array('Regular', 'Credit Memo');
}
function getInvoiceStatusOptions()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_purchases_journal'
AND COLUMN_NAME = 'invoice_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}

function getInvoicePaymentStatusOptions()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_purchases_journal'
AND COLUMN_NAME = 'payment_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function createInvoiceStatusSelect($name, $invoice_status, $option_all = 'off', $select_events = '')
{
	$status_options = getInvoiceStatusOptions();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';

	for($i = 0;$i < sizeof($status_options); $i++)
	{
		$html .= '<option value="' . $status_options[$i] . '"';
		
		if ( ($status_options[$i] == $invoice_status) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $status_options[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createInvoicePaymentStatusSelect($name, $payment_status, $option_all = 'off', $select_events = '')
{
	$status_options = getInvoicePaymentStatusOptions();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';

	for($i = 0;$i < sizeof($status_options); $i++)
	{
		$html .= '<option value="' . $status_options[$i] . '"';
		
		if ( ($status_options[$i] == $payment_status) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $status_options[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createAJAXPurchaseOrderSelect($name, $pos_purchase_order_id, $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{
	
	//$sql = "SELECT pos_purchase_order_id, purchase_order_number, purchase_order_status FROM pos_purchase_orders WHERE pos_manufacturer_id = $pos_manufacturer_id AND purchase_order_status = 'OPEN'";
	//$sql = "SELECT pos_purchase_order_id, purchase_order_number, purchase_order_status FROM pos_purchase_orders WHERE pos_manufacturer_id = $pos_manufacturer_id";
	$purchase_orders = array();

	$html = '<select id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select a Purchase Order</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_purchase_order_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Purchase Orders</option>';
	}
	for($i = 0;$i < sizeof($purchase_orders); $i++)
	{
		$html .= '<option value="' . $purchase_orders[$i]['pos_purchase_order_id'] . '"';
		
		if ( ($purchase_orders[$i]['pos_purchase_order_id'] == $pos_purchase_order_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $purchase_orders[$i]['purchase_order_number'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createAJAXInvoicePaymentSelect($name, $pos_account_id, $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{
	
	$invoice_payment_methods = array();

	$html = '<select id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select a Purchase Order</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Purchase Orders</option>';
	}
	for($i = 0;$i < sizeof($purchase_orders); $i++)
	{
		$html .= '<option value="' . $purchase_orders[$i]['pos_purchase_order_id'] . '"';
		
		if ( ($purchase_orders[$i]['pos_purchase_order_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $purchase_orders[$i]['purchase_order_number'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createMfgInvoicePaymentSelect($name, $pos_manufacturer_id, $pos_account_id,  $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	$payment_methods = getInvoicePaymentMethods($pos_manufacturer_id);
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Payment</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payments</option>';
	}
	for($i = 0;$i < sizeof($payment_methods); $i++)
	{
		$html .= '<option value="' . $payment_methods[$i]['pos_account_id'] . '"';
		
		if ( ($payment_methods[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $payment_methods[$i]['company'] . ' ' . craigsDecryption($payment_methods[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
	
}
function getManufacucturerIDFromPurchasesJournal($pos_purchases_journal_id)
{
	$sql = "SELECT pos_manufacturer_id FROM pos_purchases_journal WHERE pos_purchases_journal_id = '$pos_purchases_journal_id'";
	return getSingleValueSQL($sql);
}
function getPurchaseOrderIdsFromPurchaseJournalID($pos_purchase_journal_id)
{
	$sql = "SELECT pos_purchase_order_id FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchase_journal_id ORDER BY pos_purchase_order_id ASC";
	return getSQL($sql);
}






function getPurchaseOrdersWhereCreditMemoRequired($pos_manufacturer_id)
{
	//
	$sql = "SELECT purchase_order_number, pos_purchase_order_id FROM pos_purchase_orders
	LEFT JOIN pos_manufacturer_brands USING (pos_manufacturer_brand_id)
	WHERE credit_memo_required != 0  AND credit_memo_invoice_number = '' AND pos_manufacturer_brands.pos_manufacturer_id = $pos_manufacturer_id ORDER by pos_purchase_order_id ASC";
	return getSQL($sql);
}
function getTotalInvoicableOnPurchaseOrder($pos_purchase_order_id)
{
	$sql = "SELECT ROUND(sum((cost-discount)*(quantity_ordered-quantity_canceled)),2) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function getTotalReceivedOnPurchaseOrder($pos_purchase_order_id)
{
	$sql = "SELECT round(sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)),2) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = $pos_purchase_order_id";
	

			 
	return getSingleValueSQL($sql);
}
function getTotalCreditDueOnPurchaseOrder($pos_purchase_order_id)
{
	$sql = "SELECT ROUND(sum(cost*quantity_returning),2) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function getTotalOrderedFromPurchaseOrder($pos_purchase_order_id)
{
	$sql = "SELECT ROUND(sum(cost*(quantity_ordered-quantity_canceled)) - sum(discount*discount_quantity),2) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function getTotalReturnedFromPurchaseOrder($pos_purchase_order_id)
{
	$sql = "SELECT ROUND(sum(cost*(quantity_returning)),2) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function getTotalOrderedFromPurchaseOrderDBC($dbc, $pos_purchase_order_id)
{
	$sql = "SELECT ROUND(sum(cost*(quantity_ordered-quantity_canceled)) - sum(discount*discount_quantity),2) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = $pos_purchase_order_id";
	return getTransactionSingleValueSQL($dbc, $sql);
}
function getPurchaseOrderInvoiceAmountApplied($pos_purchase_order_id)
{
			
		$sql = "SELECT sum(applied_amount) FROM pos_purchases_invoice_to_po WHERE pos_purchase_order_id=$pos_purchase_order_id";
		return getSingleValueSQL($sql);
}
function getPurchaseOrder($pos_purchase_order_id)
{
	$sql = "SELECT * FROM pos_purchase_orders WHERE pos_purchase_order_id=$pos_purchase_order_id";
	return getSQL($sql);
}
function getTransactionPurchaseOrderIdsFromPurchaseJournalID($dbc,$pos_purchase_journal_id)
{
	$sql = "SELECT pos_purchase_order_id FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchase_journal_id";
	return getTransactionSQL($dbc,$sql);
}
function getTransactionPurchaseReturnIdsFromPurchaseJournalID($dbc,$pos_purchase_journal_id)
{
	$sql = "SELECT pos_purchase_return_id FROM pos_purchases_invoice_to_pr WHERE pos_purchases_journal_id = $pos_purchase_journal_id";
	return getTransactionSQL($dbc,$sql);
}
function getPurchaseJournalIDFromPurchaseOrderID($pos_purchase_id)
{
	$sql = "SELECT pos_purchases_journal_id FROM pos_purchases_invoice_to_po WHERE pos_purchase_order_id = $pos_purchase_order_id";
}
function getPurchaseJournalData($pos_purchase_journal_id)
{
	$sql = "SELECT * FROM pos_purchases_journal WHERE pos_purchases_journal_id=$pos_purchase_journal_id";
	return getSQL($sql);
}
function getPurchasesJournalAccount($pos_purchase_journal_id)
{
	$data = getPurchaseJournalData($pos_purchase_journal_id);
	return $data[0]['pos_account_id'];
}
function getTransactionPurchaseJournalData($dbc, $pos_purchase_journal_id)
{
	$sql = "SELECT * FROM pos_purchases_journal WHERE pos_purchases_journal_id=$pos_purchase_journal_id";
	return getTransactionSQL($dbc, $sql);
}
function getInvoicesFromPurchaseOrder($pos_purchase_order_id)
{
	$sql = "
			SELECT pos_purchases_journal.*
			FROM pos_purchases_journal
			LEFT JOIN pos_purchases_invoice_to_po
			ON pos_purchases_invoice_to_po.pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id
			WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = $pos_purchase_order_id
			";
	return getSQL($sql);

}
function getInvoicesTotalFromPurchaseOrder($dbc, $pos_purchase_order_id)
{
	$sql = "
			SELECT Sum(pos_purchases_journal.invoice_amount - shipping_amount) as invoice_total
			FROM pos_purchases_journal
			LEFT JOIN pos_purchases_invoice_to_po
			ON pos_purchases_invoice_to_po.pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id
			WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = $pos_purchase_order_id
			";
	return getTransactionSQL($dbc,$sql);
}
function getPurchasesInvoicesSum($pos_purchases_journal_id_array)
{
	
	$sql = "SELECT SUM(invoice_amount) as invoice_total FROM pos_purchases_journal WHERE pos_purchases_journal_id IN (". implode( $pos_purchases_journal_id_array ) . ")";
	return getSingleValueSQL($sql);
}
function getPurchasesInvoicesDiscounts($pos_purchases_journal_id_array)
{
	$sql = "SELECT SUM(discount_applied) as invoice_total FROM pos_purchases_journal WHERE pos_purchases_journal_id IN (". implode( $pos_purchases_journal_id_array ) . ")";
	$discounts =  getSingleValueSQL($sql);
	return $discounts;
}
function getCreditMemosAppliedToPurchasesInvoices($pos_purchases_journal_id_array)
{
	$sql = "SELECT sum(applied_amount) FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_invoice_id IN (". implode( $pos_purchases_journal_id_array ) . ")";
	return getSingleValueSQL($sql);
}
function getPurchasesInvoicesTotalAppliedCreditsDiscountsAndPayments($pos_purchases_journal_id_array)
{
	$discounts =  getPurchasesInvoicesDiscounts($pos_purchases_journal_id_array);
	$credit_memos = getCreditMemosAppliedToPurchasesInvoices($pos_purchases_journal_id_array);
	$payments = getPayementsToPurcasesInvoices($pos_purchases_journal_id_array, 'PURCHASES JOURNAL');
	return $payments+$credit_memos+$discounts;
}
function getPurchaseOrderInvoicesApplied($pos_purchase_order_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_purchases_invoice_to_po WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function getPurchaseOrderInvoicesAppliedDBC($dbc,$pos_purchase_order_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_purchases_invoice_to_po WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getTransactionSingleValueSQL($dbc,$sql);
}
function getPurchaseOrderCreditMemosApplied($dbc, $pos_purchase_order_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_purchases_credit_memo_to_po WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getTransactionSingleValueSQL($dbc,$sql);
}
function getCreditMemosAppliedToPurchasesInvoice($pos_purchases_journal_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_invoice_id = $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}


?>