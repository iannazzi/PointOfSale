<?php 

/*
deposits:
INVOICE => PAYMENT(s) => UNDEPOSITIED FUNDS => BATCH => DEPOSIT
INVOICE => PAYMENT(s) => VISA => BATCH => PPC => CNB
INVOICE => PAYMENT => CASH => REGESTER 1 => BATCH => OVER/SHORT => DEPOSIT
Craig Iannazzi 2013-02-01
	
*/
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');

$complete_location = 'list_cash_receipts_journal.php';
$cancel_location = 'list_cash_receipts_journal.php?message=Canceled';
$type = getPostOrGetValue('type');
if (strtoupper($type) =='NEW')
{
	$pos_cash_receipts_journal_id = 'TBD';
}
else
{
	$pos_cash_receipts_journal_id = getPostOrGetID('pos_cash_receipts_journal_id');
}


if (strtoupper($type) =='EDIT')
{
	//when editing we are only editing the invoice, not payment info
	$header = '<p>EDIT Deposit</p>';
	$page_title = 'Edit Deposit ';
	$data_table_def_no_data = createCashReceiptJournalEntryTableDef('Edit', $pos_cash_receipts_journal_id);	

		/*$po_invoice_table_def = createApplyInvoiceToPODynamicTableDef($pos_purchases_journal_id, $purchase_orders,'Edit');
		$po_table = createDynamicTable($po_invoice_table_def, $data);
		$po_table .= '<script>var purchase_orders = ' . json_encode($purchase_orders) . ';</script>';
		$po_table .='<script src="add_purchase_invoice_to_journal.js"></script>'.newline();*/

	$db_table = 'pos_cash_receipts_journal';
	$key_val_id['pos_cash_receipts_journal_id'] = $pos_cash_receipts_journal_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);	


}
else if  (strtoupper($type) =='NEW')
{
	$header = '<p>New Deposit</p>';
	$page_title = 'New Deposit ';
	$data_table_def = createCashReceiptJournalEntryTableDef('New', $pos_cash_receipts_journal_id);	
}
else if  (strtoupper($type) =='VIEW')
{
	$edit_location = 'cash_receipts_journal_entry.php?pos_cash_receipts_journal_id='.$pos_cash_receipts_journal_id.'&type=edit';
	$delete_location = 'delete_cash_receipts_journal_entry.form.handler.php?pos_cash_receipts_journal_id='.$pos_cash_receipts_journal_id;
	$page_title = 'Cash Receipt Journal Entry ' . $pos_purchases_journal_id;
	
	$data_table_def = createCashReceiptJournalEntryTableDef('View', $pos_cash_receipts_journal_id);
	$db_table = 'pos_cash_receipts_journal';
	$key_val_id['pos_cash_receipts_journal_id'] = $pos_cash_receipts_journal_id;
	$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);

	$html = printGetMessage('message');
	$html .= '<p>Cash Receipt Journal Entry</p>';

	$html .= confirmJournalDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($table_def_w_data);


	if(checkWriteAccess($binder_name))
	{
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
		$html .= '<input class = "button" type="button" name="edit" value="Delete" onclick="confirmJournalDelete();"/>';
		$html .= '</p>';	
	}
}
else
{
	//wrong type
	$html = 'error';
}

//create the html
if(strtoupper($type) =='VIEW')
{
}
else
{

	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	// add some hidden stuff for form processing
	$big_html_table .= createHiddenInput('type', $type);
	$html = $header;
	$form_handler = 'cash_receipts_journal_entry.form.handler.php';
	$table_array = array($data_table_def);
	$html .= createMultiPartFormForMultiMYSQLInsert($table_array, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("invoice_number")[0].focus();</script>';
}


include (HEADER_FILE);

echo $html;
include (FOOTER_FILE);

function createCashReceiptJournalEntryTableDef($type, $pos_cash_receipts_journal_id)
{
	if ($type == 'New')
	{

	}
	else
	{

	}
	


	$data_table_def = array( 
						array( 'db_field' => 'pos_cash_receipts_journal_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Cash Receipts Journal ID',
								'value' => $pos_cash_receipts_journal_id,
								'validate' => 'none'
								),
						array('db_field' => 'date',
								'caption' => 'Date',
								'type' => 'date',
								'value' => '',
								'tags' => '',
								'html' => dateSelect('date','',''),
								'validate' => 'date'),
						
						
						array('db_field' => 'pos_from_account_id',
								'type' => 'select',
								'caption' => 'From Account',
								'html' => createAccountReceivableSelect('pos_from_account_id', 'false'),
								'validate' => 'none'),
						array('db_field' => 'pos_deposit_account_id',
								'type' => 'select',
								'caption' => 'To Account',
								'html' => createDepositAccountSelect('pos_deposit_account_id', 'false'),
								'validate' => 'none'),
						array('db_field' =>  'amount',
								'caption' => 'Amount',
								'type' => 'input',
								'round' => 2,
								'tags' => ' onkeyup="checkInput(this,\'.0123456789\');" ',
								'validate' => 'number'),
						array('db_field' =>  'comments',
								'caption' => 'Comments',
								'type' => 'textarea',
								'validate' => 'none')	
						);		
						return $data_table_def;
}
?>

	