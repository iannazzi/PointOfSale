<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 10-23-12
	
*/

require_once ('../tax_functions.php');

$complete_location = 'list_sales_tax_categories.php';
$cancel_location = 'list_sales_tax_categories.php?message=Canceled';
$pos_sales_tax_category_id = getPostOrGetID('pos_sales_tax_category_id');
$tax_category_data = getSalesTaxCategoryData($pos_sales_tax_category_id);
$edit_location = 'add_edit_sales_tax_category.php?pos_sales_tax_category_id='.$pos_sales_tax_category_id.'&type=edit';
$delete_location = 'delete_sales_tax_category.form.handler.php?pos_sales_tax_category_id='.$pos_sales_tax_category_id;
$page_title = 'Sales Tax Category ' . $pos_sales_tax_category_id . ': ' . $tax_category_data[0]['tax_category_name'];

$db_table = 'pos_sales_tax_categories';
$key_val_id['pos_sales_tax_category_id']  = $pos_sales_tax_category_id;
$data_table_def = createTaxCategoryTableDef('View', $pos_sales_tax_category_id);
$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);




$html = printGetMessage('message');
$html .= '<p>View Sales Tax Category</p>';

$html .= createHTMLTableForMYSQLData($table_def_w_data);
$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';

$html .= '<p>';
$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Categories" onclick="window.location = \''.$complete_location.'\'" />';
$html .= '</p>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);





?>