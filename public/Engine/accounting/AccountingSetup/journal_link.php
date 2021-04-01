<?php
/*
	account type is a more understandable version of what an account is and how it links to the chart of accounts
	The only thing to modify is what chart of accounts the account type links to
*/

$binder_name = 'Accounting Setup';
$access_type = 'WRITE';
$page_title = 'Journal Link';
require_once ('../accounting_functions.php');
$complete_location = 'accounting_setup.php';
$cancel_location = $complete_location . '?message=Canceled';


if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_journal_to_coa_link_id']);
	//if it is new then insert, otherwise update.
	
	if($_POST['pos_journal_to_coa_link_id'] == 'TBD')
	{		
		$pos_journal_to_coa_link_id = simpleTransactionInsertSQLReturnID($dbc,'pos_journal_to_coa_link', $insert);
		$message = urlencode('Gateway ID '.$pos_payment_gateway_id.' has been added');
	}
	else
	{
		//this is an update
		$pos_journal_to_coa_link_id = getPostOrGetID('pos_journal_to_coa_link_id');
		$key_val_id['pos_journal_to_coa_link_id'] = $pos_journal_to_coa_link_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_journal_to_coa_link', $key_val_id, $insert);
		$message = urlencode('ID ' . $pos_journal_to_coa_link_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);
}
elseif(isset($_POST['cancel'])) 
{
}
else
{
	$type = $_GET['type'];

	if(strtoupper($type) == 'ADD')
	{
	}
	elseif (strtoupper($type) == 'EDIT')
	{
		$pos_journal_to_coa_link_id = getPostOrGetID('pos_journal_to_coa_link_id');
		$header = '<p>EDIT Journal Link</p>';
		$page_title = 'Edit Journal Link';
		$data_table_def_no_data = createJournalLinkTableDef($type, $pos_journal_to_coa_link_id);	
		$db_table = 'pos_journal_to_coa_link';
		$key_val_id['pos_journal_to_coa_link_id'] = $pos_journal_to_coa_link_id;
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
	}
	elseif (strtoupper($type) == 'VIEW')
	{
		$pos_journal_to_coa_link_id = getPostOrGetID('pos_journal_to_coa_link_id');
		$edit_location = 'journal_link.php?pos_journal_to_coa_link_id='.$pos_journal_to_coa_link_id.'&type=edit';
		//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
		$db_table = 'pos_journal_to_coa_link';
		$key_val_id['pos_journal_to_coa_link_id']  = $pos_journal_to_coa_link_id;
		$data_table_def = createJournalLinkTableDef($type, $pos_journal_to_coa_link_id);
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
	}
	else
	{
	}

	//build the html page
	if (strtoupper($type) == 'VIEW')
	{
		$html = printGetMessage('message');
		$html .= '<p>View Journal Link</p>';
		//$html .= confirmDelete($delete_location);
		$html .= createHTMLTableForMYSQLData($data_table_def);
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
		$html .= '<p>';
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Accounting Setup" onclick="window.location = \''.$complete_location.'\'" />';
		$html .= '</p>';
	}
	else
	{
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
	
		$html = $header;
		$form_handler = 'journal_link.php';
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
		//$html .= '<script>document.getElementsByName("printer_name")[0].focus();</script>';
	}


	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}

function createJournalLinkTableDef($type, $pos_journal_to_coa_link_id)
{
if ($type == 'New')
{
	$pos_journal_to_coa_link_id = 'TBD';
	//$unique_validate = array('unique_group' => array('account_number', 'company'), 'min_length' => 1);
}
else
{
	//$pos_account_type_id = $key_val_id['pos_account_type_id'];
	//$unique_validate = array('unique_group' => array('account_number', 'company'), 'min_length' => 1, 'id' => $key_val_id);
}

//$db_table = 'pos_accounts';
$account_data_table_def = array(
						array( 'db_field' => 'pos_journal_to_coa_link_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_journal_to_coa_link_id,
								'validate' => 'none'),
						array( 'db_field' => 'link_name',
								'caption' => 'Journal Link',
								'type' => 'input',
								'tags' => ' readonly="readonly" ',
								'validate' => 'none'),
						array( 'db_field' => 'comments',
								'type' => 'input',
								'tags' => ' readonly="readonly" '),
	
						array('db_field' =>  'pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Default Chart Of Account',
								'html' => createChartOfAccountSelect('pos_chart_of_accounts_id', 'false'),
								'validate' => 'none'						
								)
						);
				
	return $account_data_table_def;
	
}

?>