<?php


$binder_name = 'Chart Of Accounts';
$page_title = 'Chart Of Accounts';
//type is set in get or post;
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../accounting_functions.php');

$complete_location = 'list_chart_of_accounts.php';
$cancel_location = 'list_chart_of_accounts.php';


//Form Handler can go right here
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_chart_of_accounts_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_chart_of_accounts_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_chart_of_accounts_id = simpleTransactionInsertSQLReturnID($dbc,'pos_chart_of_accounts', $insert);
		$message = urlencode('Chart Of Accounts Id ' . $pos_chart_of_accounts_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_chart_of_accounts_id = getPostOrGetID('pos_chart_of_accounts_id');
		$key_val_id['pos_chart_of_accounts_id'] = $pos_chart_of_accounts_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_chart_of_accounts', $key_val_id, $insert);
		$message = urlencode('Chart Of Accounts Id ' . $pos_chart_of_accounts_id . " has been Updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);
	exit();
}
else if (isset($_POST['cancel'])) 
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}
else //Form Display
{
	if (strtoupper($type) == 'VIEW')
	{

		//this should be the view page, which will include additional functions like account activity and an edit button that
		//to avoid re-loading the page the edit button could simply replace the html...
		$pos_chart_of_accounts_id = getPostOrGetID('pos_chart_of_accounts_id');
		$edit_location = 'chart_of_accounts.php?pos_chart_of_accounts_id='.$pos_chart_of_accounts_id.'&type=edit';
		//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
		$db_table = 'pos_chart_of_accounts';
		$key_val_id['pos_chart_of_accounts_id']  = $pos_chart_of_accounts_id;
		$data_table_def = createChartOfAccountsDataTableDEF('View', $pos_chart_of_accounts_id);
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
	
		$html = printGetMessage('message');
		$html .= '<p>View Chart Of Account</p>';
		//$html .= confirmDelete($delete_location);
		$html .= createHTMLTableForMYSQLData($data_table_def);
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Chart Of Accounts" onclick="window.location = \''.$complete_location.'\'" />';
		$html .= '</p>';
	}
	else 
	{
		if(strtoupper($type) == 'ADD' )
		{
			$pos_chart_of_accounts_id = 'TBD';
			$table_type = 'New';
			$header = '<p>Add Account To Chart Of Accounts</p>';
			$page_title = 'Add Chart Of Account';
			$data_table_def = createChartOfAccountsDataTableDEF($type, $pos_chart_of_accounts_id);
		}
		else if(strtoupper($type) == 'EDIT' )
		{
			$pos_chart_of_accounts_id = getPostOrGetID('pos_chart_of_accounts_id');
			$header = '<p>EDIT Location Group</p>';
			$page_title = 'Edit Discount';
			$data_table_def_no_data = createChartOfAccountsDataTableDEF($type, $pos_chart_of_accounts_id);	
			$db_table = 'pos_chart_of_accounts';
			$key_val_id['pos_chart_of_accounts_id'] = $pos_chart_of_accounts_id;
			$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
		}
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
	
		$html = $header;
		$form_handler = 'chart_of_accounts.php';
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
		$html .= '<script>document.getElementsByName("account_name")[0].focus();</script>';
	}

	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}

function createChartOfAccountsDataTableDEF($type, $pos_chart_of_accounts_id)
{

	if ($type == 'New')
	{
		$pos_chart_of_accounts_id = 'TBD';
		$unique_validate1 = array('unique' => array('account_name'), 'min_length' => 1);
		$unique_validate2 = array('unique' => array('account_number'), 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_chart_of_accounts_id'] = $pos_chart_of_accounts_id;
		$unique_validate1 = array('unique' => array('account_name'), 'min_length' => 1, 'id' => $key_val_id);

		$unique_validate2 = array('unique' => array('account_number'), 'min_length' => 1, 'id' => $key_val_id);
	}
	$db_table = 'pos_chart_of_accounts';
	$data_table_def = array(
						array( 'db_field' => 'pos_chart_of_accounts_id',
								'type' => 'input',
								'tags' => ' readonly="readonly" ',
								'value' => $pos_chart_of_accounts_id,
								'validate' => 'none'),
						array('db_field' => 'account_name',
								'caption' => 'Account Name',
								'type' => 'input',
								'validate' =>  $unique_validate1,
								'db_table' => $db_table),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'validate' => $unique_validate2,
								'db_table' => $db_table),
						array('db_field' =>  'pos_chart_of_account_type_id',
								'type' => 'select',
								'html' => createChartOfAccountTypeSelect('pos_chart_of_account_type_id', 'false'),
								'validate' => array('select_value' => 'false')),
						array('db_field' =>  'account_sub_type',
								'type' => 'select',
								'caption' => 'Account Sub Type (for use in account creation - limits the chart of account listing when selecting an chart of account))',
								'html' => createEnumSelect('account_sub_type','pos_chart_of_accounts', 'account_sub_type', 'false',  'off')),
						/*array('db_field' =>  'pos_chart_of_accounts_required_id',
								'type' => 'select',
								'html' => createChartOfAccountsRequiredSelect('pos_chart_of_accounts_required_id', 'false'),
								'validate' => 'none'),*/
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'tags' => 'checked="checked" ',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'validate' => 'none'));	
		return $data_table_def;
}
?>