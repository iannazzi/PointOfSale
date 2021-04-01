<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/

require_once ('../tax_functions.php');

$complete_location = 'list_sales_tax_rates.php';
$cancel_location = 'list_sales_tax_rates.php?message=Canceled';
$type = getPostOrGetValue('type');
if ($type =='edit')
{
	$pos_sales_tax_rate_id = getPostOrGetID('pos_sales_tax_rate_id');
	$tax_rate_data = getSalesTaxData($pos_sales_tax_rate_id);
	$tax_jurisdiction_data = getTaxJurisdictionData($tax_rate_data[0]['pos_tax_jurisdiction_id']);
	$table_type = 'Edit';
	$header = '<p>EDIT Rate</p>';
	$page_title = 'Edit Rate ';

	if ($tax_jurisdiction_data[0]['local_or_state'] =='Local')
	{
		//$pos_state_id = getPostOrGetId('pos_state_id');
		$pos_state_id = getTaxJurisdictionStateID($tax_jurisdiction_data[0]['pos_tax_jurisdiction_id']);
		$data_table_def_no_data = createLocalSalesTaxRateTableDef($table_type, $pos_sales_tax_rate_id, $pos_state_id);
	}
	else
	{
		$data_table_def_no_data = createStateSalesTaxRateTableDef($table_type, $pos_sales_tax_rate_id);
	}	
	$db_table = 'pos_sales_tax_rates';
	$key_val_id['pos_sales_tax_rate_id'] = $pos_sales_tax_rate_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else if($type == 'Add')
{
	$jurisdiction = getPostOrGetValue('jurisdiction');
	$table_type = 'New';
	$pos_sales_tax_rate_id = 'TBD';
	$header = '<p>ADD Category</p>';
	$page_title = 'Add Rate';
	if ($jurisdiction =='Local')
	{
		$pos_state_id = getPostOrGetId('pos_state_id');
		$data_table_def = createLocalSalesTaxRateTableDef($table_type, $pos_sales_tax_rate_id, $pos_state_id);
	}
	else
	{
		$data_table_def = createStateSalesTaxRateTableDef($table_type, $pos_sales_tax_rate_id);
	}
}
else if($type == 'view')
{
	$pos_sales_tax_rate_id = getPostOrGetID('pos_sales_tax_rate_id');
	$tax_data = getSalesTaxData($pos_sales_tax_rate_id);
	$edit_location = 'add_edit_view_sales_tax_rate.php?pos_sales_tax_rate_id='.$pos_sales_tax_rate_id.'&type=edit';
	$primary_id_name = 'pos_sales_tax_rate_id';
	$primary_id_value = $pos_sales_tax_rate_id;
	$delete_message = urlencode('Confirm Delete this tax rate');
	$db_table = 'pos_sales_tax_rates';

	$delete_location =  POS_ENGINE_URL . '/includes/php/delete_mysql_entry.php?db_table='.$db_table.'&primary_id_name='.$primary_id_name.'&primary_id_value='.$primary_id_value.'&delete_message='.$delete_message.'&complete_location='.POS_ENGINE_URL.'/taxes/sales_tax_rates/'.$complete_location.'&cancel_location='.POS_ENGINE_URL.'/taxes/sales_tax_rates/'.$cancel_location;
	$page_title = 'Sales Tax Rate ' . $pos_sales_tax_rate_id . ': ' . $tax_data[0]['sales_tax_name'];
	$db_table = 'pos_sales_tax_rates';
	$key_val_id['pos_sales_tax_rate_id']  = $pos_sales_tax_rate_id;
	$data_table_def = createSalesTaxRateTableDef('View', $pos_sales_tax_rate_id);
	$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
if($type == 'view')
{
	$html = printGetMessage('message');
	$html .= '<p>View Sales Tax Rate</p>';
	
	$html .= createHTMLTableForMYSQLData($table_def_w_data);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	$html .= '<input class = "button"  type="button" name="edit"  value="Delete" onclick="open_win(\''.$delete_location.'\')"/>';
	$html .= '</p>';
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Rates" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	if(isset($jurisdiction)) $big_html_table .= createHiddenInput('jurisdiction', $jurisdiction);
	if(isset($pos_state_id)) $big_html_table .= createHiddenInput('pos_state_id', $pos_state_id);
	$html = $header;
	$form_handler = 'add_edit_sales_tax_rate.form.handler.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("pos_sales_tax_category_id")[0].focus();</script>';
}

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

	