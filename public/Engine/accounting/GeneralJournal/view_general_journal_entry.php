<?php
$binder_name = 'General Journal';
$access_type = 'READ';
$page_title = 'General Journal Expense Entry';
require_once ('../accounting_functions.php');

$complete_location = 'list_general_journal.php';
$cancel_location = 'list_general_journal.php';
$pos_general_journal_id = getPostOrGetID('pos_general_journal_id');
$key_val_id['pos_general_journal_id'] = $pos_general_journal_id;
$edit_location = 'edit_general_journal_entry.php?pos_general_journal_id='.$key_val_id['pos_general_journal_id'];
$delete_location = 'delete_general_journal_entry.form.handler.php?pos_general_journal_id='.$key_val_id['pos_general_journal_id'];


$data_table_def = createGeneralJournalExpenseEntryTableDef('Edit', $key_val_id);
$data_table_def_with_data = selectSingleTableDataFromID('pos_general_journal', $key_val_id, $data_table_def);	


/*$payments = getPaymentsFromGJEntry($pos_general_journal_id);
if(sizeof($payments)>0)
{
	$payments_html = '';
	for($p=0;$p<sizeof($payments);$p++)
	{
		$pos_payments_journal_id = $payments[$p]['pos_payments_journal_id'];
		$pj_key_val_id['pos_payments_journal_id'] = $pos_payments_journal_id;
		$pj_table_def = createPaymentEntryTableDef('Edit', $pj_key_val_id);
		$pj_table_def_with_data = selectSingleTableDataFromID('pos_payments_journal', $pj_key_val_id, $pj_table_def);
		$payments_html .= createHTMLTableForMYSQLData($pj_table_def_with_data);
	}
}
else
{
	$payments_html = '<p>No Payments Found For This Invoice</p>';
}*/

//one payment ... for now


$html_table = createHTMLTableForMYSQLData($data_table_def_with_data);

//$html_table .= $payments_html;

	
include (HEADER_FILE);
$html = printGetMessage();
$html .= confirmJournalDelete($delete_location);
$html .= '<p>General Journal Entry</p>';
$html .= $html_table;
$html .= '<p>';
$html .= '<input class = "button" type="button" name="edit" value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
$html .= '<input class = "button" type="button" name="edit" value="Delete" onclick="confirmJournalDelete();"/>';
$html .= '</p>';
$html .= '<p>Payments Journal Entry</p>';	
$html .= createPaymentRecordTable($pos_general_journal_id, 'GENERAL JOURNAL');
$html .= '<p>';
//$pos_account_id = getManufacturerAccount($pos_manufacturer_id);
$pos_account_id = getSingleValueSQL("SELECT pos_account_id FROM pos_general_journal WHERE pos_general_journal_id = $pos_general_journal_id");
if($pos_account_id != 0)
{
	$html .= '<p><INPUT class = "button" type="button" style ="width:300px" value="Add A NEW Payment For Invoice" onclick="window.location = \'../PaymentsJournal/pay_account.php?pos_payee_account_id='.$pos_account_id.'&pos_general_journal_id='.$pos_general_journal_id.'&invoice_verify=YES\'" /></p>';
}
//$html .= '<input class = "button" type="button" name="edit" value="Add Payment" onclick="open_win(\'../PaymentsJournal/add_payment.php?pos_general_journal_id='.$pos_general_journal_id.'&journal=general\')"/>';

$html .='<input class = "button" style="width:200px" type="button" name="return" value="Return To Journal" onclick="open_win(\''.$cancel_location.'\')"/>';
$html .= '</p>';
echo $html;
include (FOOTER_FILE);

?>