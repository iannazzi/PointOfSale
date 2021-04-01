<?php 
/*
	Craig Iannazzi 10-28-12	
*/

require_once ('store_functions.php');
$complete_location = 'list_stores.php';
$cancel_location = 'list_stores.php?message=Canceled';
$type = getPostOrGetValue('type');
$db_table = 'pos_stores';
if ($type =='edit')
{
	//when editing we are only editing the invoice, not payment info
	$pos_store_id = getPostOrGetID('pos_store_id');
	$header = '<p>EDIT</p>';
	$page_title = 'Edit Store';
	$data_table_def_no_data = createStoreTableDef('Edit', $pos_store_id);	
	$key_val_id['pos_store_id'] = $pos_store_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else if ($type == 'add')
{
	$pos_store_id = 'TBD';
	$header = '<p>ADD Store</p>';
	$page_title = 'Add Store';
	$data_table_def = createStoreTableDef('New', $pos_store_id);
}
else if ($type == 'view')
{
	$pos_store_id = getPostOrGetID('pos_store_id');
	$edit_location = 'add_edit_view_store.php?pos_store_id='.$pos_store_id.'&type=edit';
	$page_title = 'Store ';
	$key_val_id['pos_store_id']  = $pos_store_id;
	$data_table_def = createStoreTableDef('View', $pos_store_id);
	$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
//create the html
if ($type == 'view')
{
	$html = printGetMessage('message');
	$html .= '<p>View Store</p>';
	$html .= createHTMLTableForMYSQLData($table_def_w_data);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Return" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	$html = $header;
	$form_handler = 'add_edit_store.form.handler.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .=  '<script src="stores.js"></script>'.newline();
	$html .= '<script>document.getElementsByName("store_name")[0].focus();</script>';
}
//create the page
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>