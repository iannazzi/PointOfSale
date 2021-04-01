<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 10-23-12
	
*/

require_once ('../tax_functions.php');

$complete_location = 'list_sales_tax_rates.php';
$cancel_location = 'list_sales_tax_rates.php?message=Canceled';
$pos_sales_tax_rate_id = getPostOrGetID('pos_sales_tax_rate_id');
$tax_data = getSalesTaxData($pos_sales_tax_rate_id);
$edit_location = 'add_edit_sales_tax_rate.php?pos_sales_tax_rate_id='.$pos_sales_tax_rate_id.'&type=edit';
$primary_id_name = 'pos_sales_tax_rate_id';
$primary_id_value = $pos_sales_tax_rate_id;
$delete_message = urlencode('Confirm Delete this tax rate');
$db_table = 'pos_sales_tax_rates';
$delete_location =  POS_ENGINE_URL . '/includes/php/delete_mysql_entry.php?db_table='.$db_table.'&primary_id_name='.$primary_id_name.'&primary_id_value='.$primary_id_value.'&delete_message='.$delete_message.'&complete_location='.$complete_location.'&cancel_location='.$cancel_location;
$page_title = 'Sales Tax Rate ' . $pos_sales_tax_rate_id . ': ' . $tax_data[0]['sales_tax_name'];
$db_table = 'pos_sales_tax_rates';
$key_val_id['pos_sales_tax_rate_id']  = $pos_sales_tax_rate_id;
$data_table_def = createSalesTaxRateTableDef('View', $pos_sales_tax_rate_id);
$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);


$html = printGetMessage('message');
$html .= '<p>View Sales Tax Rate</p>';

$html .= createHTMLTableForMYSQLData($table_def_w_data);
$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
$html .= '<p><input class = "button"  type="button" name="edit"  value="Delete" onclick="open_win(\''.$delete_location.'\')"/>';
$html .= '<p>';
$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Rates" onclick="window.location = \''.$complete_location.'\'" />';
$html .= '</p>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);





?>