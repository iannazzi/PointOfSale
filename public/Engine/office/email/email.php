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

$page_title = 'Email';

$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
//pass in the binder name
//$binder_name = getPostOrGetValue('binder');

require_once ('../office_functions.php');
$ref = getPostOrGetValue('ref');
$complete_location = $ref;
$cancel_location =  addGetToURL($complete_location,'message=Canceled');



if(strtoupper($type) == 'ADD')
{
	$pos_email_address_id = 'TBD';
	$header = '<p>Add Email</p>';
	$page_title = 'Add Email';
	$data_table_def = createEmailAddressTableDef($type, $pos_email_address_id);
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_email_address_id = getPostOrGetID('pos_email_address_id');
	$header = '<p>EDIT Email</p>';
	$page_title = 'Edit Email';
	$data_table_def_no_data = createEmailAddressTableDef($type, $pos_email_address_id);	
	$db_table = 'pos_email_addresses';
	$key_val_id['pos_email_address_id'] = $pos_email_address_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_email_address_id = getPostOrGetID('pos_email_address_id');
	$edit_location = 'email.php?pos_address_id='.$pos_email_address_id.'&type=edit&ref='.$ref;
	//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
	$db_table = 'pos_email_addresses';
	$key_val_id['pos_email_address_id']  = $pos_email_address_id;
	$data_table_def = createEmailAddressTableDef($type, $pos_email_address_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
else
{
}

//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Email</p>';
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
	$form_handler = 'email.fh.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("email")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createEmailAddressTableDef($type, $pos_address_id)
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
					array( 'db_field' => 'pos_email_address_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'System ID',
								'value' => $pos_address_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	

}
?>