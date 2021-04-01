<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/

require_once ('../tax_functions.php');

$complete_location = 'list_sales_tax_categories.php';
$cancel_location = 'list_sales_tax_categories.php?message=Canceled';
$type = getPostOrGetValue('type');
if ($type =='edit')
{
	//when editing we are only editing the invoice, not payment info
	$pos_sales_tax_category_id = getPostOrGetID('pos_sales_tax_category_id');
	$tax_category_data = getSalesTaxCategoryData($pos_sales_tax_category_id);
	$table_type = 'Edit';
	$header = '<p>EDIT Category</p>';
	$page_title = 'Edit Category ' . $tax_category_data[0]['tax_category_name'];
	$data_table_def_no_data = createTaxCategoryTableDef($table_type, $pos_sales_tax_category_id);	
	$db_table = 'pos_sales_tax_categories';
	$key_val_id['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else
{
	$table_type = 'New';
	$pos_sales_tax_category_id = 'TBD';
	$header = '<p>ADD Category</p>';
	$page_title = 'Add Category';
	$data_table_def = createTaxCategoryTableDef($table_type, $pos_sales_tax_category_id);
}

$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
$big_html_table .= createHiddenInput('type', $type);

$html = $header;
$form_handler = 'add_edit_sales_tax_category.form.handler.php';
$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("tax_category_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

	