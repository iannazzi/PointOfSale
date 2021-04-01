<?php
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();

	//Entering an invoice:
	//gather the general posted information

	$table_def_array = deserializeTableDef($_POST['table_def_array']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array[0]);	
	// add some other stuff to the basic array
	$insert['pos_manufacturer_id'] = $_POST['pos_manufacturer_id'];
	$insert['invoice_entry_date'] = getCurrentTime();
	$insert['pos_user_id'] = $_SESSION['pos_user_id'];
	$invoice_type = $_POST['invoice_type'];
	$insert['invoice_type'] = $_POST['invoice_type'];
	
	//take out things we don't want to insert to mysql
	unset($insert['total_to_be_paid']);
	unset($insert['pos_purchases_journal_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_purchases_journal_id'] == 'TBD')
	{
		
		if ($insert['invoice_type'] == 'Regular')
		{
			$message = urlencode(getManufacturerName($_POST['pos_manufacturer_id']) . ' Invoive # ' . $insert['invoice_number'] . " has been added");
		}
		else
		{
			$insert['payment_status'] = 'UNUSED';
			$message = urlencode(getManufacturerName($_POST['pos_manufacturer_id']) . ' Credit Memo # ' . $insert['invoice_number'] . " has been added");
		}
		$pos_purchases_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_purchases_journal', $insert);
	}
	else
	{
		//this is an update as we already know the journal id
		$pos_purchases_journal_id = getPostOrGetID('pos_purchases_journal_id');
		$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_purchases_journal', $key_val_id, $insert);
		$message = urlencode(getManufacturerName($_POST['pos_manufacturer_id']) . ' Invoive # ' . $insert['invoice_number'] . " has been updated");
	}
	
	//if a file was attached process that
	$file_data = getFILEPostData('file_name', UPLOAD_FILE_PATH .'/invoice_uploads');
	if($file_data) 
	{
		$results[] = simpleTransactionUpdateSQL($dbc, 'pos_purchases_journal', $key_val_id, $file_data);
	}
	
	if ($invoice_type =='Regular')
	{
		//check if a payment was entered
		if (isset($_POST['payment']))
		{
			$payment_insert_array = array(
										'pos_account_id' => $_POST['payment_account_id'],
										'pos_payee_account_id' => 0,
										'payment_date' => $_POST['invoice_date'],
										'payment_entry_date' =>getCurrentTime(),
										'payment_status' => 'COMPLETE',
										'pos_user_id' => $_SESSION['pos_user_id'],
										'payment_amount' => $_POST['invoice_amount']-$_POST['discount_applied']
										);
			$pos_payments_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_payments_journal', $payment_insert_array);						
			$pos_invoice_to_payment = array('pos_journal_id' => $pos_purchases_journal_id,
										'pos_payments_journal_id' => $pos_payments_journal_id,
										'source_journal' => 'PURCHASES JOURNAL',
										'applied_amount' => $_POST['invoice_amount']-$_POST['discount_applied']);								
			$results[] = simpleTransactionInsertSQL($dbc, 'pos_invoice_to_payment',$pos_invoice_to_payment);
		}
		//apply invoice amount to the po.	
		//probably need to set each PO to invoice status to incomplete

		$purchase_orders_effected = getSQL("SELECT pos_purchase_order_id, applied_amount FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id");
	//probably want to set all of those po's to incomplete
		for($i=0;$i<sizeof($purchase_orders_effected);$i++)
		{
			if($purchase_orders_effected[$i]['applied_amount'] != 0)
			{
				 setPurchaseOrderInvoiceStatus($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], 'INCOMPLETE');
			 }
		}
		//remove all entries so we can add the new ones in...
		$sql = "DELETE FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
		$result[] = runtransactionsql($dbc, $sql);
		if(isset($_POST['pos_purchase_order_id']))
		{
			for($i=0;$i<sizeof($_POST['pos_purchase_order_id']);$i++)
			{	
				$pos_purchase_order_id = $_POST['pos_purchase_order_id'][$i];
				if(!in_array($_POST['pos_purchase_order_id'][$i], array('NULL', '')))
				{
					$applied_amount = $_POST['applied_amount_from_this_invoice'][$i];
					$po_invoice_insert_array = array('pos_purchases_journal_id' => $pos_purchases_journal_id,
														'pos_purchase_order_id' => $pos_purchase_order_id,
														'applied_amount' => $applied_amount,
														'comments' =>$_POST['comments_for_applied'][$i] );							
					$result[] = simpleTransactionInsertSQL($dbc,'pos_purchases_invoice_to_po', $po_invoice_insert_array);
														
					//i do not believe this works - transaction has to be closed!
					//$po_status[] = trytoclosepoTransaction($dbc,$pos_purchase_order_id);
					//$status = tryToCompletePurchaseOrderInvoiceStatus($dbc, $pos_purchase_order_id);

				}
			}
		}
	}
	else
	{
		//update the credit memo stuff here.....
		$purchase_orders_effected = getSQL("SELECT pos_purchase_order_id, applied_amount FROM pos_purchases_credit_memo_to_po 	WHERE pos_purchases_journal_id = $pos_purchases_journal_id");
		//probably want to set all of those po's to incomplete
		for($i=0;$i<sizeof($purchase_orders_effected);$i++)
		{
			if($purchase_orders_effected[$i]['applied_amount'] != 0)
			{
				//setPurchaseOrderInvoiceStatus($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], 'INCOMPLETE');
				//setPOCreditMemoRequired($dbc, $purchase_orders_effected[$i]['pos_purchase_order_id'], '1');
			}
		}
		//remove all entries so we can add the new ones in...
		$sql = "DELETE FROM pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
		$result[] = runtransactionsql($dbc, $sql);
		if(isset($_POST['pos_purchase_order_id']))
		{
			
			for($i=0;$i<sizeof($_POST['pos_purchase_order_id']);$i++)
			{	
				$pos_purchase_order_id = $_POST['pos_purchase_order_id'][$i];
				if(!in_array($_POST['pos_purchase_order_id'][$i], array('NULL', '', 'false')))
				{
					$applied_amount = $_POST['applied_amount_from_this_invoice'][$i];
					$po_invoice_insert_array = array('pos_purchases_journal_id' => $pos_purchases_journal_id,
														'pos_purchase_order_id' => $pos_purchase_order_id,
														'applied_amount' => $applied_amount,
														'comments' =>$_POST['comments_for_applied'][$i] );							
					$result[] = simpleTransactionInsertSQL($dbc,'pos_purchases_credit_memo_to_po', $po_invoice_insert_array);

					// i don't know what to compare the applied amount to... so it is hard to automatically set flags for credit memo required and to close out po's...
				}
			}
		}
	}
	///now commit the source data
	simpleCommitTransaction($dbc);
	
	
	//i believe this stuff was here because the transaction was messing with me!
	if ($invoice_type =='Regular')
	{
		updateInvoicePaymentStatus($pos_purchases_journal_id);
		tryToClosePurchaseInvoice($pos_purchases_journal_id);
	
	}
	
	//finally try to close the po's
	if(isset($_POST['pos_purchase_order_id']))
	{
		for($i=0;$i<sizeof($_POST['pos_purchase_order_id']);$i++)
		{
			if(!in_array($_POST['pos_purchase_order_id'][$i], array('NULL', '', 'false')))
			{
				$pos_purchase_order_id = $_POST['pos_purchase_order_id'][$i];
				if($pos_purchase_order_id != 'false')
				{
					$po_status[] = trytoclosepo($_POST['pos_purchase_order_id'][$i]);
					$status = tryToCompletePurchaseOrderInvoiceStatus($pos_purchase_order_id);
		
				}
			}
		}
	
	}

	header('Location: view_purchase_invoice_to_journal.php?pos_purchases_journal_id='.$pos_purchases_journal_id. '&message=' . $message);
					
}							
else
{
	trigger_error( 'not submitted');
}								
?>