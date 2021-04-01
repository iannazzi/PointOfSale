<?php
/*
	account type is a more understandable version of what an account is and how it links to the chart of accounts
	The only thing to modify is what chart of accounts the account type links to
*/

$binder_name = 'Accounting Setup';
$access_type = 'WRITE';
$page_title = 'Account Setup';
require_once ('../accounting_functions.php');
$type = $_GET['type'];
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
$complete_location = 'accounting_setup.php';
$cancel_location = $complete_location . '?message=Canceled';


//no add here...
if (strtoupper($type) == 'VIEW')
{
	//this should be the view page, which will include additional functions like account activity and an edit button that
	//to avoid re-loading the page the edit button could simply replace the html...
	$pos_account_type_id = getPostOrGetID('pos_account_type_id');
	$edit_location = 'account_type.php?pos_account_type_id='.$pos_account_type_id.'&type=edit';
	//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
	$db_table = 'pos_account_type';
	$key_val_id['pos_account_type_id']  = $pos_account_type_id;
	$data_table_def = createAccountTypeTableDef('New', $pos_account_type_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
	
	$html = printGetMessage('message');
	$html .= '<p>View Account Type</p>';
	//$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back Account Setup" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_account_type_id = getPostOrGetID('pos_account_type_id');
	$header = '<p>EDIT Account Type</p>';
	$page_title = 'Edit Account Type';
	$data_table_def_no_data = createAccountTypeTableDef($type, $pos_account_type_id);	
	$db_table = 'pos_account_type';
	$key_val_id['pos_account_type_id'] = $pos_account_type_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
	
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	
	$html = $header;
	$form_handler = 'account_type.fh.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	//$html .= '<script>document.getElementsByName("printer_name")[0].focus();</script>';
	
}




include(HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createAccountTypeTableDef($type, $pos_account_type_id)
{
if ($type == 'New')
{
	$pos_account_type_id = 'TBD';
	$unique_validate = array('unique_group' => array('account_number', 'company'), 'min_length' => 1);
}
else
{
	//$pos_account_type_id = $key_val_id['pos_account_type_id'];
	//$unique_validate = array('unique_group' => array('account_number', 'company'), 'min_length' => 1, 'id' => $key_val_id);
}

$db_table = 'pos_accounts';
$account_data_table_def = array(
						array( 'db_field' => 'pos_account_type_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_type_id,
								'validate' => 'none'),
						array( 'db_field' => 'account_type_name',
								'caption' => 'Account Type Name',
								'type' => 'input',
								'tags' => ' readonly="readonly" ',
								'validate' => 'none'),
						array( 'db_field' => 'Caption',
								'type' => 'input',
								'tags' => ' readonly="readonly" '),
	
						array('db_field' =>  'default_chart_of_account_id',
								'type' => 'select',
								'caption' => 'Default Chart Of Account',
								'html' => createChartOfAccountSelect('default_chart_of_account_id', 'false'),
								'validate' => 'none'						
								)
						);
				
	return $account_data_table_def;
	
}

?>