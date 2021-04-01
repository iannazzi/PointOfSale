<?php
$page_title = 'Payment';
$binder_name = 'Payments Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');

$complete_location = 'list_payments_journal.php';
$cancel_location = 'list_payments_journal.php?message=Canceled';
$pos_payments_journal_id = getPostOrGetID('pos_payments_journal_id');
$key_val_id['pos_payments_journal_id'] = $pos_payments_journal_id;
$form_handler = 'edit_payments_journal_entry.form.handler.php';

$pj_table_def = createPaymentsJournalTableDef('Edit', $pos_payments_journal_id);
$pj_table_def_with_data = selectSingleTableDataFromID('pos_payments_journal', $key_val_id, $pj_table_def);
$big_html_table = createHTMLTableForMYSQLInsert($pj_table_def_with_data);

$html = '<p>Edit a Payment</p>';
$html .= createMultiPartFormForMultiMYSQLInsert(array($pj_table_def), $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("pos_employee_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

