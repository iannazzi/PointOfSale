<?php
/*
	I dont think this is necessary
*/

$binder_name = 'Accounting Setup';
$access_type = 'WRITE';
$page_title = 'Account Setup';
require_once ('../accounting_functions.php');
$type = $_GET['type'];
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
$complete_location = 'accounting_setup.php';
$cancel_location = $complete_location . '?message=Canceled';

include(HEADER_FILE);
	echo 'Reqired Accounts Are Not Editable';
	include (FOOTER_FILE);
exit();

//no add here...
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_account_type_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_account_type_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_account_type_id = simpleTransactionInsertSQLReturnID($dbc,'pos_chart_of_accounts_required', $insert);
		$message = urlencode('Account Type Id ' . $pos_account_type_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_account_type_id = getPostOrGetID('pos_account_type_id');
		$key_val_id['pos_account_type_id'] = $pos_account_type_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_chart_of_accounts_required', $key_val_id, $insert);
		$message = urlencode('Discount ID ' . $pos_account_type_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);

}
else if (isset($_POST['cancel'])) 
{
}
else
{
	if (strtoupper($type) == 'VIEW')
	{
		//this should be the view page, which will include additional functions like account activity and an edit button that
		//to avoid re-loading the page the edit button could simply replace the html...
		$pos_chart_of_accounts_required_id = getPostOrGetID('pos_chart_of_accounts_required_id');
		$edit_location = 'required_chart_of_accounts.php?pos_chart_of_accounts_required_id='.$pos_chart_of_accounts_required_id.'&type=edit';
		//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
		$key_val_id['pos_chart_of_accounts_required_id']  = $pos_chart_of_accounts_required_id;
		$data_table_def = createAccountTypeTableDef('View', $pos_chart_of_accounts_required_id);
		$data_table_def = selectSingleTableDataFromID('pos_chart_of_accounts_required', $key_val_id,  $data_table_def);
	
		$html = printGetMessage('message');
		$html .= '<p>View Required Chart Of Account</p>';
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
}
function createRequiredAccountTableDef($type, $key_val_id)
{
if ($type == 'New')
{
	$pos_account_type_id = 'TBD';
	$unique_validate = array('unique_group' => array('account_number', 'company'), 'min_length' => 1);
}
else
{
	$pos_account_type_id = $key_val_id['pos_account_type_id'];
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