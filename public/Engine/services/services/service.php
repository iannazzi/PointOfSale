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


$complete_location = 'list_services.php';
$cancel_location = 'list_services.php?message=Canceled';



if(strtoupper($type) == 'ADD')
{
	$pos_service_id = 'TBD';
	$header = '<p>Add Service</p>';
	$page_title = 'Add Service';
	$data_table_def = createServiceTableDef($type, $pos_service_id);
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_service_id = getPostOrGetID('pos_service_id');
	$header = '<p>EDIT Service</p>';
	$page_title = 'Edit Service';
	$data_table_def_no_data = createServiceTableDef($type, $pos_service_id);	
	$db_table = 'pos_services';
	$key_val_id['pos_service_id'] = $pos_service_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_service_id = getPostOrGetID('pos_service_id');
	$edit_location = 'service.php?pos_service_id='.$pos_service_id.'&type=edit';
	//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
	$db_table = 'pos_services';
	$key_val_id['pos_service_id']  = $pos_service_id;
	$data_table_def = createServiceTableDef($type, $pos_service_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
else
{
}

//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Service</p>';
	//$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Services" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	
	$html = $header;
	$form_handler = 'service.fh.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("service_code")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createServiceTableDef($type, $pos_service_id)
{
	if ($pos_service_id =='TBD')
	{
		$unique_validate = array('unique' => 'service_code', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_service_id'] = $pos_service_id;
		$unique_validate = array('unique' => 'service_code', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_service_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Service ID',
								'value' => $pos_service_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'service_code',
								'type' => 'input',
								'db_table' => 'pos_services',
								'caption' => 'Service Code',
								'validate' => $unique_validate),	
						array('db_field' =>  'Title',
								'type' => 'input',
								'caption' => 'Title',
								'validate' => 'none'),
					
						
						array('db_field' =>  'retail_price',
								'type' => 'input',
								'caption' => 'Retail Price',
								'validate' => 'number'),
						array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false')),
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