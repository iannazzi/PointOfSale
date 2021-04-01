<?php
/*
	Ahh the terminal... shove a cookie down it to get it an id......
*/
$type = $_GET['type'];
$page_title = 'Terminals';
$binder_name = 'Terminals';
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../system_functions.php');

$complete_location = 'list_terminals.php';
$cancel_location = 'list_terminals.php?message=Canceled';



if(strtoupper($type) == 'ADD')
{
	$pos_terminal_id = 'TBD';
	$header = '<p>Add Terminal</p>';
	$page_title = 'Add Terminal';
	$data_table_def = createTerminalTableDef($type, $pos_terminal_id);
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_terminal_id = getPostOrGetID('pos_terminal_id');
	$header = '<p>EDIT Terminal</p>';
	$page_title = 'Edit Terminal';
	$data_table_def_no_data = createTerminalTableDef($type, $pos_terminal_id);	
	$db_table = 'pos_terminals';
	$key_val_id['pos_terminal_id'] = $pos_terminal_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_terminal_id = getPostOrGetID('pos_terminal_id');
	$edit_location = 'terminals.php?pos_terminal_id='.$pos_terminal_id.'&type=edit';
	//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
	$db_table = 'pos_terminals';
	$key_val_id['pos_terminal_id']  = $pos_terminal_id;
	$data_table_def = createTerminalTableDef($type, $pos_terminal_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
else if (strtoupper($type) == 'REGISTER')
{
	$pos_terminal_id = getPostOrGetID('pos_terminal_id');
	$cookie = getSingleValueSQL("SELECT cookie_name from pos_terminals WHERE pos_terminal_id=$pos_terminal_id");
	setcookie ('pos_terminal_name',$cookie , time()+(10 * 365 * 24 * 60 * 60), '/', '', 0, 0);
	//go to the list with a message
	$message = 'message='.urlencode($cookie . ' Has Been Registered');
	header('Location: ' .addGetToURL('terminals.php?type=view&pos_terminal_id='.$pos_terminal_id, $message));
	exit();

}
else
{
}
//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Terminal</p>';
	if(isset($_COOKIE['pos_terminal_name']))
	{
		$html.= '<p>Your computer/device & browser is registered under the name: ' . $_COOKIE['pos_terminal_name'] .'</p>';
	}
	else
	{
		$html.= '<p>Your computer/device & browser is not a registered POS system</p>';
	}
	//$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	$html .= '<input class = "button" type="button" style="width:300px" name="register" value="Register As A POS Terminal" onclick="open_win(\'terminals.php?type=register&pos_terminal_id='.$pos_terminal_id.'\')"/>';
// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Terminals" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	
	$html = $header;
	$form_handler = 'terminals.fh.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("pos_store_id")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createTerminalTableDef($type, $pos_terminal_id)
{
	if ($pos_terminal_id =='TBD')
	{
		$unique_validate = array('unique_group' => array('pos_store_id', 'terminal_name'), 'min_length' => 1);
		$terminal_name = 'TBD';
	}
	else
	{
		$key_val_id['pos_terminal_id'] = $pos_terminal_id;
		$unique_validate = array('unique_group' => array('pos_store_id', 'terminal_name'), 'min_length' => 1, 'id' => $key_val_id);
		$terminal_name = 'TBD';
	}
	
	return array( 
						array( 'db_field' => 'pos_terminal_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Terminal ID',
								'value' => $pos_terminal_id,
								'validate' => 'none'
								),
						array('db_field' =>  'terminal_name',
								'type' => 'input',
								'caption' => 'Terminal Name',
								'tags' => ' readonly = "readonly" ',
								'db_table' => 'pos_terminals',
								'validate' => $unique_validate,
								'value' => $terminal_name),	
						array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),
						array('db_field' => 'default_cash_account_id',
								'caption' => 'Default Cash Drawer Account',
								'type' => 'select',
								'html' => createCashDepositAccountSelect('default_cash_account_id', 'false'),
								'validate' => 'false'),
						array('db_field' => 'default_check_account_id',
								'caption' => 'Default Check Drawer Account',
								'type' => 'select',
								'html' => createCashDepositAccountSelect('default_check_account_id', 'false'),
								'validate' => 'false'),
						array('db_field' => 'default_gift_card_account_id',
								'caption' => 'Default Gift Card Account',
								'type' => 'select',
								'html' => storeCreditAccountSelect('default_gift_card_account_id', 'false'),
								'validate' => 'false'),
						array('db_field' => 'default_store_credit_account_id',
								'caption' => 'Default Store Credit Account',
								'type' => 'select',
								'html' => storeCreditAccountSelect('default_store_credit_account_id', 'false'),
								'validate' => 'false'),
						array('db_field' => 'default_prepay_account_id',
								'caption' => 'Default Customer Deposit or PrePay Account',
								'type' => 'select',
								'html' => storeCreditAccountSelect('default_prepay_account_id', 'false'),
								'validate' => 'false'),
						array('db_field' => 'default_non_payment_account_id',
								'caption' => 'Default Non Payment Account (used for giving away free gift cards)',
								'type' => 'select',
								'html' => storeCreditAccountSelect('default_non_payment_account_id', 'false'),
								'validate' => 'false'),
						array('db_field' => 'pos_payment_gateway_id',
								'caption' => 'Credit Card Payment Gateway or Device',
								'type' => 'select',
								'html' => createPaymentGatewaySelect('pos_payment_gateway_id', 'false'),
								'validate' => 'false'),
						array('db_field' =>  'terminal_description',
								'type' => 'input',
								'caption' => 'Terminal Description'),
							array('db_field' =>  'location',
								'type' => 'input',
								'caption' => 'location'),
						array('db_field' => 'pos_printer_id',
								'caption' => 'Default Printer',
								'type' => 'select',
								'html' => createPrinterSelect('pos_printer_id', 'false',  'off'),
								'validate' => 'false'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	


}


?>