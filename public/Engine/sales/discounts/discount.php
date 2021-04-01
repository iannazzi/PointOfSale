<?php
/*
	Craig Iannazzi 2-8-2013 on a snowy day at midtown
	
	//eventually I would like to link the discount to products, or categories, or manufacturers
	// so there would be a discount_id to category, manufacturer, product id lookup table... a dynamic table
*/
$type = $_GET['type'];
$page_title = 'Discounts';
$binder_name = 'Discounts';
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../sales_functions.php');

$complete_location = 'list_discounts.php';
$cancel_location = 'list_discounts.php?message=Canceled';



if(strtoupper($type) == 'ADD')
{
	$pos_discount_id = 'TBD';
	$table_type = 'New';
	$pos_location_group_id = 'TBD';
	$header = '<p>Add Discount</p>';
	$page_title = 'Add Discount';
	$data_table_def = createDiscountTableDef($type, $pos_discount_id);
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_discount_id = getPostOrGetID('pos_discount_id');
	$header = '<p>EDIT Location Group</p>';
	$page_title = 'Edit Discount';
	$data_table_def_no_data = createDiscountTableDef($type, $pos_discount_id);	
	$db_table = 'pos_discounts';
	$key_val_id['pos_discount_id'] = $pos_discount_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_discount_id = getPostOrGetID('pos_discount_id');
	$edit_location = 'discount.php?pos_discount_id='.$pos_discount_id.'&type=edit';
	$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
	$db_table = 'pos_discounts';
	$key_val_id['pos_discount_id']  = $pos_discount_id;
	$data_table_def = createDiscountTableDef($type, $pos_discount_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
else
{
}

//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Discount</p>';
	$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Discounts" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	
	$html = $header;
	$form_handler = 'discount.form.handler.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("location__group_name")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createDiscountTableDef($type, $pos_discount_id)
{
	if ($pos_discount_id =='TBD')
	{
		$unique_validate = array('unique' => 'discount_code', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_discount_id'] = $pos_discount_id;
		$unique_validate = array('unique' => 'discount_code', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_discount_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Discount ID',
								'value' => $pos_discount_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'discount_code',
								'type' => 'input',
								'db_table' => 'pos_discounts',
								'caption' => 'Discount Code',
								'validate' => $unique_validate),	
						array('db_field' =>  'discount_name',
								'type' => 'input',
								'caption' => 'Discount Name',
								'validate' => 'none'),
					
						
						array('db_field' =>  'discount_amount',
								'type' => 'input',
								'caption' => 'Discount Amount',
								'validate' => 'number'),
						array('db_field' =>  'percent_or_dollars',
								'type' => 'select',
								'caption' => '$ or %',
								'html' => createEnumSelect('percent_or_dollars','pos_discounts', 'percent_or_dollars', 'false',  'off')),		
								
						
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