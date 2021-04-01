<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');

$complete_location = 'list_purchase_journal.php';
$cancel_location = 'list_purchase_journal.php?message=Canceled';
$type = getPostOrGetValue('type');


//the hardest part seems to be selecting the purchase orders to apply the invoice or credit memo to.
//for the invoice:
//need all purchase orders with incomplete invoice not including purchase orders touching the one you are working with
// need all purchase orders attached to the invoice we are working with
// for editing we need to call the two functions
// for new we just call the one function



if (strtoupper($type) =='EDIT')
{
	//when editing we are only editing the invoice, not payment info
	$pos_purchases_journal_id = getPostOrGetID('pos_purchases_journal_id');
	$pos_manufacturer_id = getManufacucturerIDFromPurchasesJournal($pos_purchases_journal_id);
	$pos_account_id =  getManufacturerAccount($pos_manufacturer_id);
	$journal_data =getPurchaseJournalData($pos_purchases_journal_id);
	$table_type = 'Edit';
	//now for the linked po's
	//is it regular or credit memo?
	$invoice_type = getInvoiceType($pos_purchases_journal_id);
	if($invoice_type == 'Regular')
	{
		$header = '<p>EDIT Invoice</p>';
		$page_title = 'Edit Invoice ' . $journal_data[0]['invoice_number'];
		$data_table_def_no_data = createPurchaseJournalTableDef($table_type, $pos_manufacturer_id,$pos_purchases_journal_id);	
		$pos_manufacturer_id = getManufacucturerIDFromPurchasesJournal($pos_purchases_journal_id);
		$data = getPurchaseOrderDataFromPurchaseJournalID($pos_purchases_journal_id);
		
		//if there is an additional purchase_order_id then lets add that in to the list:
		if (ISSET($_POST['pos_purchase_order_id']))
		{
			$pos_purchase_order_id = $_POST['pos_purchase_order_id'];
			$additional_data = getPurchaseOrderDataFromPurchaseOrderID($pos_purchase_order_id);
			$data = array_merge($data,$additional_data);
		}
		
		$purchase_orders = array_merge($data,getPurchaseOrdersWithIncompleteInvoicesNotIncludingInvoice($pos_purchases_journal_id, $pos_manufacturer_id));
		$po_invoice_table_def = createApplyInvoiceToPODynamicTableDef($pos_purchases_journal_id, $purchase_orders,'Edit');
		$po_table = createDynamicTable($po_invoice_table_def, $data);
		$po_table .= '<script>var purchase_orders = ' . json_encode($purchase_orders) . ';</script>';
		$po_table .='<script src="add_purchase_invoice_to_journal.2015.08.01.js"></script>'.newline();

		$help_text = '<p style="font-size:0.8em" > Discount Available is the calculated discount based on show discounts and terms. Discount to be Applied is the amount to apply to the invoice when paying. Each invoice needs to have the correct value set for Discount to be Applied to calculate the correct payment amount. Sometimes the discount can be applied if the terms are not met, other times there are additional discounts. There will be situations where there is no discount available yet a discount can be applied. The two values are needed to calculate discounts lost.</p>';
	} 
	else // credit memo
	{
		$header = '<p>EDIT Credit Memo</p>';
		$page_title = 'Edit Credit Memo ' . $journal_data[0]['invoice_number'];
		$data_table_def_no_data = createCreditMemoPurchaseJournalTableDef('Edit', $pos_manufacturer_id, $pos_purchases_journal_id);		
		$purchase_orders = getPurchaseOrderDataFromPurchaseJournalCreditMemo($pos_purchases_journal_id);
		$other_purchase_orders = getPurchaseOrdersWhereCreditMemoRequiredNotIncludingCreditMemo($pos_purchases_journal_id);	
		
		
		$data = getPurchaseOrderDataFromCreditMemoID($pos_purchases_journal_id);
		
		$purchase_orders = array_merge($purchase_orders,$other_purchase_orders);
		$po_invoice_table_def = createApplyCreditMemoToPODynamicTableDef($pos_purchases_journal_id, $pos_manufacturer_id,'Edit');
		$po_table = createDynamicTable($po_invoice_table_def, $data);
		$po_table .= '<script>var purchase_orders = ' . json_encode($purchase_orders) . ';</script>';
		$po_table .='<script src="add_credit_memo_to_journal.js"></script>'.newline();

		$help_text = '<p style="font-size:0.8em" > In order for a PO to show up in the PO list, credit_memo Required must be unchecked, and a credit memo number must be entered. Credit memos are a bit of a manual process. An RA is requested for returning goods. Then an RA # is sent. Then the goods are sent, then the credit memo is sent. So there are many opportunbities for failure. The credit memo should be the same amount as those returned goods. While we should be tracking the exactness of all this, there is difficulty knowing what a vendor will actually send compared to what was ordered, causing a disconnect in data. For example, we received a Chantelle shipment with many products that we did not order. These products have pricing that is not in our system. Chantelle will be sending an invoice which includes those incorrect products, and we should then receive an credit memo to compensate the error. So the PO will have an Credit memo check, the invoice will come in high, and the Credit memo is needed to make everything balance.  What we have decided to do is close a PO if the RA required is unchecked or the credit memo is checked and a credit number is entered. </p>';
		
		
	}
	$db_table = 'pos_purchases_journal';
	$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else
{
	//multiple types of creating a new entry
	$table_type = 'New';
	$data =  array();
	$pos_purchases_journal_id = 'TBD';
	$pos_manufacturer_id = getPostOrGetID('pos_manufacturer_id');
	//now for the linked po's - there are none, so just need to make a table...
	$pos_purchase_order_id = getPostOrGetDataIfAvailable('pos_purchase_order_id');	
	if (strtoupper($type) =='CREDIT')
	{
		$page_title = 'Add Credit Memo';
		$invoice_type = 'Credit Memo';
		$header = '<p>Add a Credit Memo to the Purchase Journal For ' . getManufacturerName($pos_manufacturer_id) . '</p>';	

		$data_table_def =  createCreditMemoTableDef('TBD', $pos_manufacturer_id);
		$purchase_orders = getPurchaseOrdersWhereCreditMemoRequired($pos_manufacturer_id);
		$po_invoice_table_def = createApplyCreditMemoToPODynamicTableDef($pos_purchases_journal_id, $pos_manufacturer_id,'New');
		$po_table = createDynamicTable($po_invoice_table_def, $data);
		$po_table .= '<script>var purchase_orders = ' . json_encode($purchase_orders) . ';</script>';
		$po_table .='<script src="add_credit_memo_to_journal.js"></script>'.newline();
	
	$help_text = '<p style="font-size:0.8em" > Credit memos are a bit of a manual process. An RA is requested for returning goods. Then an RA # is sent. Then the goods are sent, then the credit memo is sent. So there are many opportunbities for failure. The credit memo should be the same amount as those returned goods. While we should be tracking the exactness of all this, there is difficulty knowing what a vendor will actually send compared to what was ordered, causing a disconnect in data. For example, we received a Chantelle shipment with many products that we did not order. These products have pricing that is not in our system. Chantelle will be sending an invoice which includes those incorrect products, and we should then receive an credit memo to compensate the error. So the PO will have an Credit memo check, the invoice will come in high, and the Credit memo is needed to make everything balance.  What we have decided to do is close a PO if the RA required is unchecked or the credit memo is checked and a credit number is entered. </p>';
	}
	else if (strtoupper($type) =='INVOICE') 
	{
		$page_title = 'Add Invoice';
		$invoice_type = 'Regular';
		$help_text = '<p style="font-size:0.8em" > Discount Available is the calculated discount based on show discounts and terms. Discount to be Applied is the amount to apply to the invoice when paying. Each invoice needs to have the correct value set for Discount to be Applied to calculate the correct payment amount. Sometimes the discount can be applied if the terms are not met, other times there are additional discounts. There will be situations where there is no discount available yet a discount can be applied. The two values are needed to calculate discounts lost.</p>';
		$pos_purchases_journal_id = 'TBD';
		$data = getPurchaseOrderDataFromPurchaseOrderID($pos_purchase_order_id);
		$purchase_orders = getPurchaseOrdersWithIncompleteInvoicesNotIncludingInvoice($pos_purchases_journal_id, $pos_manufacturer_id);
		$po_invoice_table_def = createApplyInvoiceToPODynamicTableDef($pos_purchases_journal_id, $purchase_orders, 'New');
		$po_table = '<p>Apply The Invoice To The Following Purchase Orders. Be Sure to fill in the applied amount. </p>';
		$po_table .= createDynamicTable($po_invoice_table_def, $data);
		$po_table .= '<script>var purchase_orders = ' . json_encode($purchase_orders) . ';</script>';
		$po_table .='<script src="add_purchase_invoice_to_journal.2015.08.01.js"></script>'.newline();
		
		//$pos_account_id =  getPurchasesJournalAccount($pos_purchase_journal_id);
		//$pos_account_id = getManufacturerAccount($pos_manufacturer_id);
		//if ($pos_account_id == 0)
		if (!checkManufacturerAccount($pos_manufacturer_id))
		{
			//this is an error, 
			include(HEADER_FILE);
			echo 'You need to set up an account for this manufacturer before entering the Invoice. To do that Create an Account. Look for an account number and payable information on the invoice. Once the account is created, link the account to the manufacturer. Go to the manufacturer page and edit the manufacturer, selecting the account. Soon those links will be here.';
			
				echo '<p><input class = "button" type="button" style="width:200px" name="add_purchase_invoice_on_account" value="View  '.getManufacturerName($pos_manufacturer_id).'" onclick="open_win(\'' .POS_ENGINE_URL . '/manufacturers/ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$pos_manufacturer_id.'\')"/></p>';

			include(FOOTER_FILE);
			exit();
		}
		
		$header = '<p>Add an Inventory Invoice to the Purchase Journal For ' . getManufacturerName($pos_manufacturer_id) . '</p>';						
		$data_table_def = createPurchaseJournalTableDef($table_type, $pos_manufacturer_id,$pos_purchases_journal_id);

		
	}
	else
	{
		trigger_error('Invoice type not defined');
		exit();
	}
}

//create the invoice form
$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
$big_html_table .= $po_table;
//now for the purchase orders - if editing there should be linked po's
	





//now if there is a payment it will go here:
$big_html_table .= (isset($pj_html_table)) ? $pj_html_table : '';

// add some hidden stuff for form processing

$big_html_table .= createHiddenInput('pos_manufacturer_id', $pos_manufacturer_id);
$big_html_table .= createHiddenInput('type', $type);
$big_html_table .= createHiddenInput('invoice_type', $invoice_type);

include (HEADER_FILE);
$html = $header;

$html .= '<script>var discount = ' .getDiscount($pos_manufacturer_id) . ';</script>'.newline();

$form_handler = 're_engineered_pj_form_handler.php';
$table_array = array($data_table_def);

$html .= createMultiPartFormForMultiMYSQLInsert($table_array, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("invoice_number")[0].focus();</script>';
$html .= $help_text;
//footer
echo $html;
include (FOOTER_FILE);
function createApplyInvoiceToPODynamicTableDef($pos_purchases_journal_id, $purchase_orders, $type)
{
	// the select values and names need to be shared... I guess they can just go in the first array group...
		$select_ids = array();
		$select_names = array();	
	if(strtoupper($type) == 'VIEW')
	{
		$po = 'po_select_text';

	}
	elseif (strtoupper($type) == 'EDIT')
	{
		$po = 'pos_purchase_order_id';
	}
	else
	{
			$po = 'pos_purchase_order_id';
	}
		
		
		//this is the select values
		for($i=0;$i<sizeof($purchase_orders);$i++)
		{
			$select_ids[$i]= $purchase_orders[$i]['pos_purchase_order_id'];
			$select_names[$i] = $purchase_orders[$i]['pos_purchase_order_id'] . ' PO#: ' . $purchase_orders[$i]['purchase_order_number'];	
		}
		
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"')
						),
					/*when the user selects a po we need to load data to the other cells...
					when a user selects an invoice we need to load that data*/
					array('db_field' => $po,
						'caption' => 'Purchase Order',
						'type' => 'select',
						'unique_select_options' => true,
						'select_names' => $select_names,
						'select_values' => $select_ids,
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){updatePOData(this);}',
												/*'onblur' => 'function(){updateSelectOptions();}'*/)
						),
					array('caption' => 'On Order Amount<br>(Ordered QTY - Canceled Qty)',
						'db_field' => 'ordered_amount',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);updateTableData(this);}',
												'onmouseup' => 'function(){updateTableData(this);}')),
					array('caption' => 'Discount Amount',
						'db_field' => 'discount_amount',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);updateTableData(this);}',
												'onmouseup' => 'function(){updateTableData(this);}')),
					array('caption' => 'Amount Received<br>(FYI only)',
						'db_field' => 'received_amount',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);updateTableData(this);}',
												'onmouseup' => 'function(){updateTableData(this);}')),
					array('caption' => 'Invoice Amount<br> Applied To PO <br> Not From This Invoice',
						'db_field' => 'applied_amount_from_other_invoices',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);updateTableData(this);}',
												'onmouseup' => 'function(){updateTableData(this);}')),
					array('caption' => 'Amount Left To Apply <br>To Set PO Invoice <br>Status to Complete',
						'db_field' => 'applied_amount_remaining',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"readonly"',
												'readOnly' => 'true',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);updateTableData(this);}',
												'onmouseup' => 'function(){updateTableData(this);}')),							
												
					array('caption' => 'Invoice Amount To Apply',
							'db_field' => 'applied_amount_from_this_invoice',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"nothing"',
												'style.backgroundColor' => '"yellow"',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);updateAppliedAmountRemaining(this);}',
												'onmouseup' => 'function(){updateTableData(this);}')),
					array('caption' => 'Comments',
					'db_field' => 'comments_for_applied',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}'))
					
				);			
						
		
		return $columns;
	
	
	
}
function getPurchaseOrderDataFromPurchaseOrderID($pos_purchase_order_id)
{
	//we need to know the total amount applied to invoices, the total ordered, the total received, the remaining amount to be applied the total canceled
	//ordered_amoount
	//received_amount
	//applied_amount
	//applied_amount_remaining
	//the invoice is going to be for the sum of the ordered - cancled 
	// received amount is for info only
	$pos_purchases_journal_id = '0';
	
	$tmp_sql = "CREATE TEMPORARY TABLE 
 purchase_orders 
 
 SELECT pos_purchase_orders.pos_purchase_order_id,concat(pos_purchase_orders.pos_purchase_order_id, '  PO#: ' , pos_purchase_orders.purchase_order_number) as po_select_text,  pos_purchase_orders.purchase_order_number,
 
    (SELECT COALESCE(sum(pos_purchases_invoice_to_po.applied_amount),0) FROM  pos_purchases_invoice_to_po WHERE pos_purchases_journal_id != $pos_purchases_journal_id AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_invoice_to_po.pos_purchase_order_id) as applied_amount_from_other_invoices, 
    
    	(SELECT COALESCE(sum(pos_purchases_invoice_to_po.applied_amount),0) FROM  pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_invoice_to_po.pos_purchase_order_id) as applied_amount_from_this_invoice,
 
 (SELECT ROUND(sum(cost*(quantity_ordered-quantity_canceled)) ,2) FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as ordered_amount,
  
  (SELECT ROUND(sum(discount*(quantity_ordered-quantity_canceled)),2) FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as discount_amount,
 

 
  (SELECT COALESCE(round(sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)),2),0) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as received_amount
 
 
 
FROM pos_purchase_orders 
WHERE pos_purchase_orders.pos_purchase_order_id = $pos_purchase_order_id
			
			;";
			
	
	$tmp_select_sql = "SELECT *, ordered_amount - discount_amount - applied_amount_from_other_invoices - applied_amount_from_this_invoice as applied_amount_remaining FROM purchase_orders";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}
function createApplyCreditMemoToPODynamicTableDef($pos_purchases_journal_id, $pos_manufacturer_id, $type)
{
	// the select values and names need to be shared... I guess they can just go in the first array group...
		$puchase_orders = array();
		$select_ids = array();
		$select_names = array();	
	if(strtoupper($type) == 'VIEW')
	{
		$po = 'po_select_text';

	}
	elseif (strtoupper($type) == 'EDIT')
	{
		$po = 'pos_purchase_order_id';
		$puchase_orders = array_merge(getPurchaseOrderDataFromPurchaseJournalID($pos_purchases_journal_id),getPurchaseOrdersWhereCreditMemoRequired($pos_manufacturer_id));
	}
	else
	{
			$po = 'pos_purchase_order_id';
		$puchase_orders = getPurchaseOrdersWhereCreditMemoRequired($pos_manufacturer_id);
	}
		
		
		//this is the select values
		for($i=0;$i<sizeof($puchase_orders);$i++)
		{
			$select_ids[$i]= $puchase_orders[$i]['pos_purchase_order_id'];
			$select_names[$i] = $puchase_orders[$i]['pos_purchase_order_id'] . ' PO#: ' . $puchase_orders[$i]['purchase_order_number'];	
		}
		
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"')
						),
					/*when the user selects a po we need to load data to the other cells...
					when a user selects an invoice we need to load that data*/
					array('db_field' => $po,
						'caption' => 'Purchase Order',
						'type' => 'select',
						'unique_select_options' => true,
						'select_names' => $select_names,
						'select_values' => $select_ids,
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){updatePOData(this);}',
												/*'onkeyup' => 'function(){updateSelectOptions();}'*/)
						),
						
												
					array('caption' => 'Credit Memo <br> Amount To Apply <br> To Purchase Order',
							'db_field' => 'applied_amount_from_this_invoice',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 2,
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"nothing"',
												
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);updateAppliedAmountRemaining(this);}',
												'onmouseup' => 'function(){updateTableData(this);}')),
					array('caption' => 'Comments',
					'db_field' => 'comments_for_applied',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}'))
					
				);			
						
		
		return $columns;
	
	
	
}
function getPurchaseOrderDataFromPurchaseJournalID($pos_purchases_journal_id)
{
	//we need to know the total amount applied to invoices, the total ordered, the total received, the remaining amount to be applied the total canceled
	//ordered_amoount
	//received_amount
	//applied_amount
	//applied_amount_remaining
	//the invoice is going to be for the sum of the ordered - cancled 
	// received amount is for info only
	
	$tmp_sql = "CREATE TEMPORARY TABLE 
 purchase_orders 
 
 SELECT pos_purchase_orders.pos_purchase_order_id,concat(pos_purchase_orders.pos_purchase_order_id, '  PO#: ' , pos_purchase_orders.purchase_order_number) as po_select_text, pos_purchases_invoice_to_po.comments as comments_for_applied, pos_purchase_orders.purchase_order_number,
 
    (SELECT COALESCE(sum(pos_purchases_invoice_to_po.applied_amount),0) FROM  pos_purchases_invoice_to_po WHERE pos_purchases_journal_id != $pos_purchases_journal_id AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_invoice_to_po.pos_purchase_order_id) as applied_amount_from_other_invoices, 
    
    	(SELECT COALESCE(sum(pos_purchases_invoice_to_po.applied_amount),0) FROM  pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_invoice_to_po.pos_purchase_order_id) as applied_amount_from_this_invoice,
 
 (SELECT ROUND(sum(cost*(quantity_ordered-quantity_canceled)) ,2) FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as ordered_amount,
  
  (SELECT ROUND(sum(discount*(quantity_ordered-quantity_canceled)),2) FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as discount_amount,
 

 
  (SELECT COALESCE(round(sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)),2),0) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as received_amount
 
FROM pos_purchase_orders 
LEFT JOIN pos_purchases_invoice_to_po 
ON pos_purchases_invoice_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id
WHERE pos_purchases_invoice_to_po.pos_purchases_journal_id = $pos_purchases_journal_id ORDER BY pos_purchase_orders.pos_purchase_order_id ASC
			
			;";
			
	
	$tmp_select_sql = "SELECT *, ordered_amount - discount_amount - applied_amount_from_other_invoices - applied_amount_from_this_invoice as applied_amount_remaining FROM purchase_orders";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}
function getPurchaseOrderDataFromCreditMemoID($pos_purchases_journal_id)
{
	//we need to know the total amount applied to invoices, the total ordered, the total received, the remaining amount to be applied the total canceled
	//ordered_amoount
	//received_amount
	//applied_amount
	//applied_amount_remaining
	//the invoice is going to be for the sum of the ordered - cancled 
	// received amount is for info only
	
	$tmp_sql = "CREATE TEMPORARY TABLE 
 purchase_orders 
 
 SELECT pos_purchase_orders.pos_purchase_order_id,concat(pos_purchase_orders.pos_purchase_order_id, '  PO#: ' , pos_purchase_orders.purchase_order_number) as po_select_text, pos_purchases_credit_memo_to_po.comments as comments_for_applied, pos_purchase_orders.purchase_order_number,
 
    (SELECT COALESCE(sum(pos_purchases_credit_memo_to_po.applied_amount),0) FROM  pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id != $pos_purchases_journal_id AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_credit_memo_to_po.pos_purchase_order_id) as applied_amount_from_other_invoices, 
    
    	(SELECT COALESCE(sum(pos_purchases_credit_memo_to_po.applied_amount),0) FROM  pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id AND pos_purchase_orders.pos_purchase_order_id = pos_purchases_credit_memo_to_po.pos_purchase_order_id) as applied_amount_from_this_invoice,
 
 (SELECT ROUND(sum(cost*(quantity_ordered-quantity_canceled)) - sum(discount*discount_quantity),2) FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as ordered_amount,
 
 
 (SELECT round(sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)),2) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as received_amount
 
  
 
FROM pos_purchase_orders 
LEFT JOIN pos_purchases_credit_memo_to_po 
ON pos_purchases_credit_memo_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id
WHERE pos_purchases_credit_memo_to_po.pos_purchases_journal_id = $pos_purchases_journal_id ORDER BY pos_purchase_orders.pos_purchase_order_id ASC
			
			;";
			
	
	$tmp_select_sql = "SELECT *, ordered_amount - applied_amount_from_other_invoices - applied_amount_from_this_invoice as applied_amount_remaining FROM purchase_orders";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);
	return $data;
	
}
function getPurchaseOrdersWhereCreditMemoRequiredNotIncludingCreditMemo($pos_purchases_journal_id)
{
	//this might be wrong.... not returning any PO's === ahh need to check credit memo...
	$pos_manufacturer_id = getManufacucturerIDFromPurchasesJournal($pos_purchases_journal_id);
	$sql = "SELECT pos_purchase_orders.purchase_order_number, pos_purchase_orders.pos_purchase_order_id 
	FROM pos_purchase_orders
	LEFT JOIN pos_manufacturer_brands USING (pos_manufacturer_brand_id)
	LEFT JOIN pos_purchases_credit_memo_to_po 
	ON pos_purchases_credit_memo_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id
	WHERE pos_purchase_orders.pos_purchase_order_id NOT IN (SELECT pos_purchase_order_id FROM pos_purchases_credit_memo_to_po WHERE pos_purchases_journal_id = $pos_purchases_journal_id) 
	AND credit_memo_required != 0  AND credit_memo_invoice_number = '' AND pos_manufacturer_brands.pos_manufacturer_id = $pos_manufacturer_id ORDER BY pos_purchase_orders.pos_purchase_order_id ASC";
	
	
	
	return getSQL($sql);
	
}
?>

	