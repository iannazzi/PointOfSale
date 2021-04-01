<?php
$binder_name = 'General Journal';
$access_type = 'WRITE';
$page_title = 'General Journal';
require_once ('../accounting_functions.php');

$complete_location = 'list_general_journal.php';
$cancel_location = 'list_general_journal.php?message=Canceled';
$pos_general_journal_id = getPostOrGetID('pos_general_journal_id');
$key_val_id['pos_general_journal_id'] = $pos_general_journal_id;
$form_handler = 'edit_general_journal_entry.form.handler.php';
$entry_type = getGeneralJournalEntryType($pos_general_journal_id);

$gj_table_def = createGeneralJournalExpenseEntryTableDef('Edit', $key_val_id);
$gj_table_def_with_data = selectSingleTableDataFromID('pos_general_journal', $key_val_id, $gj_table_def);
$table_def = $gj_table_def;
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
		$payments_html .= createHTMLTableForMYSQLInsert($pj_table_def_with_data);
		$table_def = array_merge($table_def, $pj_table_def);
	}
}
else
{
	$payments_html = '<p>No Payments Found For This Invoice</p>';
}
*/
$big_html_table = createHTMLTableForMYSQLInsert($gj_table_def_with_data);
$big_html_table .= createHiddenInput('general_journal_entry_type',$entry_type);
//$big_html_table .= '<p>Payment</p>';
//$big_html_table .= $payments_html;			


$html = '<p>Edit General Journal Entry</p>';
$html .= createMultiPartFormForMultiMYSQLInsert(array($table_def), $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("pos_employee_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

