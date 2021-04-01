<?php
/*
	//a semi warm spring day 4-22-2013
	//addresses are basically for customers as they can have many
	manufactureres can have many addresses but probably not
	same with accounts
	contacts probably do not change addresses, but they might
	
	so this is basically for customers with the option of expanding it for others...
*/

if(isset($_GET['type']))
{
	$type = $_GET['type'];
}
else
{
	trigger_error('missing type');
}


$pos_customer_id = (isset($_GET['pos_customer_id'])) ? $_GET['pos_customer_id'] : 0;

$page_title = 'Addresses';

$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
//pass in the binder name
//$binder_name = getPostOrGetValue('binder');

require_once ('../office_functions.php');
$ref = getPostOrGetValue('ref');
$complete_location = $ref;
$cancel_location =  addGetToURL($complete_location,'message=Canceled');



if(strtoupper($type) == 'ADD')
{
	$pos_address_id = 'TBD';
	$header = '<p>Add Address For ' .getCustomerFullName($pos_customer_id) .'</p>';
	$page_title = 'Add Address';
	$data_table_def = createAddressTableDef($type, $pos_address_id);
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_address_id = getPostOrGetID('pos_address_id');
	$header = '<p>EDIT Address</p>';
	$page_title = 'Edit Address';
	$data_table_def_no_data = createAddressTableDef($type, $pos_address_id);	
	$db_table = 'pos_addresses';
	$key_val_id['pos_address_id'] = $pos_address_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_address_id = getPostOrGetID('pos_address_id');
	$edit_location = 'address.php?pos_address_id='.$pos_address_id.'&type=edit&ref='.$ref;
	//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
	$db_table = 'pos_addresses';
	$key_val_id['pos_address_id']  = $pos_address_id;
	$data_table_def = createAddressTableDef($type, $pos_address_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
else
{
}

//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Address</p>';
	//$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Return" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	$big_html_table .= createHiddenInput('ref', $complete_location);
	$big_html_table .= createHiddenInput('pos_customer_id', $pos_customer_id);
	
	$html = $header;
	$form_handler = 'address.fh.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("address1")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createAddressTableDef($type, $pos_address_id)
{
	if ($pos_address_id =='TBD')
	{
		//$unique_validate = array('unique' => 'discount_code', 'min_length' => 1);
	}
	else
	{
		//$key_val_id['pos_discount_id'] = $pos_discount_id;
		//$unique_validate = array('unique' => 'discount_code', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
					array( 'db_field' => 'pos_address_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Discount ID',
								'value' => $pos_address_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'pos_state_id',
								'type' => 'select',
								'html' => createStateSelect('pos_state_id', 'false', 'off', ' '),
								'validate' => 'none'),
						array('db_field' =>  'zip',
								'type' => 'input',
								'validate' => 'none'),
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