<?php
/*
	Craig Iannazzi 2-8-2013 on a snowy day at midtown
	
	//eventually I would like to link the discount to products, or categories, or manufacturers
	// so there would be a discount_id to category, manufacturer, product id lookup table... a dynamic table
*/


require_once ('../services_functions.php');
$type = getFormType();
$page_title = 'Services';
$binder_name = 'Services';
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';


$complete_location = 'list_shipping_options.php';
$cancel_location = 'list_shipping_options.php?message=Canceled';



if(strtoupper($type) == 'ADD')
{
	$pos_shipping_option_id = 'TBD';
	$header = '<p>Add Shipping Option</p>';
	$page_title = 'Add Shipping Option';
	$data_table_def = createShippingOptionTableDef($type, $pos_shipping_option_id);
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_shipping_option_id = getPostOrGetID('pos_shipping_option_id');
	$header = '<p>EDIT Shipping Option</p>';
	$page_title = 'Edit Shipping Option';
	$data_table_def_no_data = createShippingOptionTableDef($type, $pos_shipping_option_id);	
	$db_table = 'pos_shipping_options';
	$key_val_id['pos_shipping_option_id'] = $pos_shipping_option_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_shipping_option_id = getPostOrGetID('pos_shipping_option_id');
	$edit_location = 'shipping_options.php?pos_shipping_option_id='.$pos_shipping_option_id.'&type=edit';
	//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
	$db_table = 'pos_shipping_options';
	$key_val_id['pos_shipping_option_id']  = $pos_shipping_option_id;
	$data_table_def = createShippingOptionTableDef($type, $pos_shipping_option_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
else
{
}

//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Shipping Option</p>';
	//$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Shippin Options" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	
	$html = $header;
	$form_handler = 'shipping_options.fh.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("carrier_name")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createShippingOptionTableDef($type, $pos_shipping_option_id)
{
	if ($pos_shipping_option_id =='TBD')
	{
		//$unique_validate = array('unique' => 'service_code', 'min_length' => 1);
	}
	else
	{
		//$key_val_id['pos_shipping_option_id'] = $pos_shipping_option_id;
		//$unique_validate = array('unique' => 'service_code', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_shipping_option_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Service ID',
								'value' => $pos_shipping_option_id,
								'validate' => 'none'
								
								),
								
						/*array( 'db_field' => 'carrier_name',
								'type' => 'select',
								'caption' => 'Carrier',
								'html' => createCarrierSelect('pos_shipping_method_id', 'false')),*/
						array('db_field' =>  'carrier_name',
								'type' => 'input',
								'caption' => 'Carrier Name',
								),	
						array('db_field' =>  'method_name',
								'type' => 'input',
								'caption' => 'Shipping Method',
								'validate' => 'none'),
					
						
						array('db_field' =>  'fee',
								'type' => 'input',
								'caption' => 'Fee',
								'validate' => 'number'),
								
						array('db_field' =>  'fee_type',
								'type' => 'select',
								'caption' => 'Fee Type',
								'html' => createEnumSelect('fee_type','pos_shipping_options', 'fee_type', 'false',  'off')),
						array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false')),
						array('db_field' =>  'priority',
								'type' => 'input',
								'caption' => 'Priority'),
						array('db_field' =>  'weight_min',
						'type' => 'input',
						'caption' => 'weight_min'),
						array('db_field' =>  'weight_max',
						'type' => 'input',
						'caption' => 'weight_max'),
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	

}
?>