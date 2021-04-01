<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Purchases Journal';
$access_type = 'READ';
require_once ('../accounting_functions.php');

$complete_location = 'list_purchase_journal.php';
$cancel_location = 'list_purchase_journal.php?message=Canceled';
$pos_purchases_journal_id = getPostOrGetID('pos_purchases_journal_id');
$purchases_journal_data = getPurchaseJournalData($pos_purchases_journal_id);
$journal_data =getPurchaseJournalData($pos_purchases_journal_id);
$type = $journal_data[0]['invoice_type'];
$edit_location = 'add_edit_purchase_invoice_to_journal.php?pos_purchases_journal_id='.$pos_purchases_journal_id.'&type=edit';
$key_val_id['pos_purchases_journal_id']  = $pos_purchases_journal_id;
$pos_manufacturer_id = getManufacucturerIDFromPurchasesJournal($pos_purchases_journal_id);
$delete_location = 'delete_purchases_journal_entry.form.handler.php?pos_purchases_journal_id='.$key_val_id['pos_purchases_journal_id'];

$db_table = 'pos_purchases_journal';
$page_title = 'PJ ' . $pos_purchases_journal_id;
if ($type=='Regular')
{
	$data_table_def = createViewEditPurchaseJournalTableDef('Edit', $pos_manufacturer_id, $pos_purchases_journal_id);
	
}
else
{
	$data_table_def = createCreditMemoPurchaseJournalTableDef('Edit', $pos_manufacturer_id, $pos_purchases_journal_id);
}


$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);




$html = printGetMessage('message');
$html .= '<p>View Purchase Journal Entry</p>';

$html .= confirmJournalDelete($delete_location);
$html .= createHTMLTableForMYSQLData($table_def_w_data);




//$html .= createHTMLTableForMYSQLData($multi_select_table_def);



//$html .= createMultiSelect('pos_purchase_order_id[]', $open_pos_values, $purchase_order_values, ' disabled="disabled" size="15" onchange="needToConfirm=true" ');
if(checkWriteAccess($binder_name))
{
$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';


//$html .= '<input class = "button" type="button" name="edit" style="width:200px" value="Apply Invoice To Purchase Orders" onclick="open_win(\'apply_invoice_to_purchase_orders.php?pos_purchases_journal_id='.$pos_purchases_journal_id.'\')"/>';


$html .= '<input class = "button" type="button" name="edit" value="Delete" onclick="confirmJournalDelete();"/>';
$html .= '</p>';
}
if($type=='Regular')
{

	
	
	$html .= '<p>Purchase Orders Linked To This Invoice</p>';	
	$html .= createPurchaseOrderRecordTable($pos_purchases_journal_id);
	$html .= '<p>Credit Memo\'s Applied</p>';	
	$html .= createCreditMemoRecordTable($pos_purchases_journal_id);
	//$html .= '</p>';
	$html .= '<p><input class = "button" type="button" name="edit" style="width:200px" value="Apply Credit Memo To Invoice" onclick="open_win(\'apply_credit_memo_to_invoices.php?pos_purchases_journal_id='.$pos_purchases_journal_id.'\')"/></p>';
	
	$html .= '<p>Payments Journal Entry</p>';	
	$html .= createPaymentRecordTable($pos_purchases_journal_id, 'PURCHASES JOURNAL');
	//$html .= '</p>';
	if (getInvoicePaymentStatus($pos_purchases_journal_id) =='UNPAID')
	{
				//$pos_account_id = getManufacturerAccount($pos_manufacturer_id);

		$html .= '<p><INPUT class = "button" type="button" style ="width:300px" value="Add A NEW Payment For Invoice" onclick="window.location = \'../PaymentsJournal/pay_purchases_invoices.php?pos_payee_account_id='.$pos_account_id.'&pos_purchases_journal_id='.$pos_purchases_journal_id.'&invoice_verify=YES\'" /></p>';
	}
	//$html .= '<p>';
}
else
{
	$html .= '<p>Purchase Orders Linked To This Credit Memo</p>';	
	$html .= createPurchaseOrderRecordTableLinkedToCreditMemo($pos_purchases_journal_id);
	$html .= '<p>Credit Memo Applied to the Following Invoices</p>';
	$html .= createCreditMemoUsedTable($pos_purchases_journal_id);
}
$html .= '<p>';
$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Purchases Journal" onclick="window.location = \''.$complete_location.'\'" />';
$html .= '</p>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);





?>