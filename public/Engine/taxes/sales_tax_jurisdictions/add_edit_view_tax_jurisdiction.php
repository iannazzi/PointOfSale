<?php 
/*
	Craig Iannazzi 10-28-12	
*/

require_once ('../tax_functions.php');
$complete_location = 'list_tax_jurisdictions.php';
$cancel_location = 'list_tax_jurisdictions.php?message=Canceled';
$type = getPostOrGetValue('type');
if ($type =='edit')
{
	//when editing we are only editing the invoice, not payment info
	$pos_tax_jurisdiction_id = getPostOrGetID('pos_tax_jurisdiction_id');
	$table_type = 'Edit';
	$header = '<p>EDIT Jurisdiction</p>';
	$page_title = 'Edit Jurisdiction';
	$data_table_def_no_data = createTaxJurisdictionTableDef($table_type, $pos_tax_jurisdiction_id);	
	$db_table = 'pos_tax_jurisdictions';
	$key_val_id['pos_tax_jurisdiction_id'] = $pos_tax_jurisdiction_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else if ($type == 'add')
{
	$table_type = 'New';
	$pos_tax_jurisdiction_id = 'TBD';
	$header = '<p>ADD Jurisdiction</p>';
	$page_title = 'Add Jurisdiction';
	$data_table_def = createTaxJurisdictionTableDef($table_type, $pos_tax_jurisdiction_id);
}
else if ($type == 'view')
{
	$pos_tax_jurisdiction_id = getPostOrGetID('pos_tax_jurisdiction_id');
	$edit_location = 'add_edit_view_tax_jurisdiction.php?pos_tax_jurisdiction_id='.$pos_tax_jurisdiction_id.'&type=edit';
	//$delete_location = 'delete_sales_tax_category.form.handler.php?pos_sales_tax_category_id='.$pos_sales_tax_category_id;
	$page_title = 'Tax Jurisdiction ';
	$db_table = 'pos_tax_jurisdictions';
	$key_val_id['pos_tax_jurisdiction_id']  = $pos_tax_jurisdiction_id;
	$data_table_def = createTaxJurisdictionTableDef('View', $pos_tax_jurisdiction_id);
	$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
//create the html
if ($type == 'view')
{
	$html = printGetMessage('message');
	$html .= '<p>View Tax Jurisdiction</p>';
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
	$form_handler = 'add_edit_tax_jurisdiction.form.handler.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("tax_category_name")[0].focus();</script>';
}
//create the page
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>