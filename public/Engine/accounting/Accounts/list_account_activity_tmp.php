<?php 

/*

	Craig Iannazzi 4-23-12
	
	account starting balance....
	at some point there will be an openeing balance.
	the default would be zero.
	
	From the balance point we would work backwards and forwards to calculate a new balance..
	
	In order to get a balance we need to get all the information... then calculate.
	Based on the search results date range we would then choose to diplay or not display....
	
	the account list
	list debit credit balance....
	
	account types:
	
	AP
	Inventory:
	invoices go onto the account.
	discounts taken when the invoice is paid.
	
	
*/
$binder_name = 'Accounts';
$access_type = 'READ';
require_once ('../accounting_functions.php');

$db_table = 'pos_accounts';
$complete_location = 'list_accounts.php';
$cancel_location = 'list_accounts.php';
$key_val_id['pos_account_id'] = getPostOrGetID('pos_account_id');
$pos_account_id = $key_val_id['pos_account_id'];
$page_title = getAccountName($pos_account_id) . ' Account Activity';
$action = 'list_account_activity.php';

//need to link to the source document...
/*			array( 'th' => 'View',
			'mysql_field' => 'pos_journal_id',
			'variable_get_url_link' => array(
				'row_result_lookup' => 'source_journal',
				"PURCHASES JOURNAL" => array(
							'url' => POS_ENGINE_URL.'/accounting/PurchaseJournal/view_purchase_invoice_to_journal.php', 
							'get_data' => array('pos_purchases_journal_id'=>'pos_journal_id')),				 
				"GENERAL JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/GeneralJournal/view_general_journal_entry.php',
							'get_data' => array('pos_general_journal_id' => 'pos_journal_id'))),
			'url_caption' => 'View',),
*/

$search_fields = array(	array(	'db_field' => 'date',
											'mysql_search_result' => 'date',
											'caption' => 'Start Date',
											'type' => 'date',
											'html' => dateSelect('date_start_date',valueFromGetOrDefault('date_start_date'))
											),
						array(	'db_field' => 'date',
											'mysql_search_result' => 'date',
											'caption' => 'End Date',	
											'type' => 'date',
											'html' => dateSelect('date_end_date',valueFromGetOrDefault('date_end_date'))
											),
						/*array(	'db_field' => 'journal_id',
											'mysql_search_result' => 'journal_id',
											'caption' => 'Journal Id',	
											'type' => 'input',
											'html' => createSearchInput('journal_id')
											),
						array(	'db_field' => 'journal',
											'mysql_search_result' => 'journal',
											'caption' => 'Journal',	
											'type' => 'input',
											'html' => createSearchInput('journal')
											),
						array(	'db_field' => 'account_name',
											'mysql_search_result' => 'account_name',
											'caption' => 'Account Name',	
											'type' => 'input',
											'html' => createSearchInput('account_name')
											),
						array(	'db_field' => 'chart_of_account_name',
											'mysql_search_result' => 'chart_of_account_name',
											'caption' => 'Chart of <br>Account Name',	
											'type' => 'input',
											'html' => createSearchInput('chart_of_account_name')
											),*/
						/*array(	'db_field' => 'description',
											'mysql_search_result' => 'description',
											'caption' => 'Description',	
											'type' => 'input',
											'html' => createSearchInput('description')
											),*/
						/*array(	'db_field' => 'debit',
											'mysql_search_result' => 'debit',
											'caption' => 'Debit',	
											'type' => 'input',
											'html' => createSearchInput('debit')
											),
						array(	'db_field' => 'credit',
											'mysql_search_result' => 'credit',
											'caption' => 'Credit',	
											'type' => 'input',
											'html' => createSearchInput('credit')
											)*/
										
										);
$table_columns = array(
		array(
			'th' => 'Date',
			'mysql_field' => 'date',
			'sort' => 'date'),
		array(
			'th' => 'Journal ID',
			'mysql_field' => 'journal_id',
			'target' => 'blank',
			'variable_get_url_link' => array(
				'row_result_lookup' => 'journal',
				"PURCHASES JOURNAL" => array(
							'url' => POS_ENGINE_URL.'/accounting/PurchaseJournal/view_purchase_invoice_to_journal.php', 
							'get_data' => array('pos_purchases_journal_id'=>'journal_id')),				 
				"GENERAL JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/GeneralJournal/view_general_journal_entry.php',
							'get_data' => array('pos_general_journal_id' => 'journal_id')),
				"PAYMENTS JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/PaymentsJournal/view_payments_journal_entry.php',
							'get_data' => array('pos_payments_journal_id' => 'journal_id'))),
			
			
			
			
			
			'sort' => 'journal_id'),
		array(
			'th' => 'Journal',
			'mysql_field' => 'journal',
			'sort' => 'journal'),
		array(
			'th' => 'Account Name',
			'mysql_field' => 'account_name',
			'sort' => 'account_name'),
		array(
			'th' => 'Chart Of Account Name',
			'mysql_field' => 'chart_of_account_name',
			'sort' => 'chart_of_account_name'),
		array(
			'th' => 'Description',
			'mysql_field' => 'description',
			'sort' => 'description'),
		array(
			'th' => 'Debit',
			'mysql_field' => 'debit',
			'round' => 2,
			'total' => 0,
			'sort' => 'debit'),
		array(
			'th' => 'Credit',
			'mysql_field' => 'credit',
			'round' => 2,
			'total' => 0,
			'sort' => 'credit'),
		
			
			);

$balance_array = getAccountOpeningBalanceForActivityArray($pos_account_id);
$tmp_sql = getAccountActivityTableSQL($pos_account_id);
$tmp_sql = "CREATE TEMPORARY TABLE tmp " . $tmp_sql . ";";
$dbc = openPOSdb();
$results[] = runTransactionSQL($dbc,$tmp_sql);
$tmp_select_sql = "SELECT * FROM tmp WHERE 1 ORDER_BY Date ASC";
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//now take this data and calculate the balance...
for($i=0;$i<sizeof($data);$i++)
{
}

//$html .= createAccountrecordsTable($balance_array, $data);
//process form here:

if (isset($_POST['submit']))
{
	//get the sql for the data
	//$search_sql = urldecode($_POST['search_sql']);
	//$tmp_select_sql  .= $search_sql;
	//Create the order sting to append to the sql statement
	//$order_by = urldecode($_POST['order_by']);
	//$tmp_select_sql  .=  " ORDER BY $order_by";
	
	//process the charges, re-show the form
	for($i=0;$i<sizeof($data);$i++)
	{
		$update_id=array();
		$update_data = array();
		if(isset($_POST[$data[$i]['journal_id']]))
		{
			$update_data = array('validated' => 1);
		}
		else
		{
			$update_data = array('validated' => 0);
		}
		
		if($data[$i]['journal'] == 'PAYMENTS JOURNAL')
		{
			$update_id['pos_payments_journal_id'] = $data[$i]['journal_id'];
			$table = 'pos_payments_journal';
		}
		elseif($data[$i]['journal'] == 'GENERAL JOURNAL')
		{
			$update_id['pos_general_journal_id'] = $data[$i]['journal_id'];
			$table = 'pos_general_journal';
		}
		elseif($data[$i]['journal'] == 'PURCHASES JOURNAL')
		{
			$update_id['pos_purchases_journal_id'] = $data[$i]['journal_id'];
			$table = 'pos_purchases_journal';
		}
		//preprint($update_data);
		//pprint($table);
		//preprint($update_id);
		$result = simpleUpdateSQL($table,$update_id, $update_data);
	}
	$enabled = false;
	$buttons = '<input class = "button" style="width:200px" type="submit" name="verify_statement" value="Verify Transactions"/>';
	header('Location: list_account_activity.php?pos_account_id='.$pos_account_id );
	
}
else
{
	if (isset($_POST['verify_statement']))
	{
	//open the form for write
	//get the post data to build the sql...
	//Create Search String (AND's after MYSQL WHERE from $_GET data)
	//$search_sql = urldecode($_POST['search_sql']);
	//$tmp_select_sql  .= $search_sql;
	//Create the order sting to append to the sql statement
	//$order_by = urldecode($_POST['order_by']);
	//$tmp_select_sql  .=  " ORDER BY $order_by";
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html = '<h3>Verify ' . getAccountName($pos_account_id) . ' ' . getAccountNumber($pos_account_id) .' Account Activity</h3>';
	//$html .= createSearchFormWithID($search_fields,$action, $key_val_id);

	$html .= confirmNavigation();
	$buttons = '<p><input class ="button" type="submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$buttons .= '<input class = "button" type="submit" name="cancel" value="Cancel" />';
	$enabled = true;
}
	else
	{
	//function as normal....
	//redirect to saved url?
	$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_account_activity_url_'.$pos_account_id);

	$start_date = $_GET['date_start_date'];

	//get the data
	//Create Search String (AND's after MYSQL WHERE from $_GET data)
	//$search_sql = createSearchSQLStringMultipleDates($search_fields);
	$search_sql = '';


	
	
	//$tmp_select_sql  .= $search_sql;
	//Create the order sting to append to the sql statement
	//$order_by = createSortSQLString($table_columns, 'date', 'ASC');
	//$tmp_select_sql  .=  " ORDER BY $order_by";
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);

	//show the default state
	$html .= '<h3>' . getAccountName($pos_account_id) . ' ' . getAccountNumber($pos_account_id) .' Account Activity</h3>';
	$html = '<form id = "search_form" name="search_form" action="'.$action.'" method="get">';
		$html .= '<div class = "search_div">';
		$html .= '<h2>Search</h2>';
		$html .= createHiddenInput(key($key_val_id), $key_val_id[key($key_val_id)]);
		
		$html .=  '<table class = "search_table">' . newline();
		$html .= '<thead>' . newline();
		$html .= '<tr>' . newline();
		foreach($search_fields as $caption)
		{
			$html .= '<th>' . $caption['caption'] . '</th>' . newline();
		}
		$html .= '</tr>' . newline();
		$html .= '</thead>' . newline();
		$html .= '<tbody>' .newline();
		$html .= '<tr>' .newline();
		foreach($search_fields as $caption)
		{
			$html .= '<td>' . $caption['html'] . '</td>' . newline();
		}
		$html .= '</tr>' .newline();
		$html .= '</tbody>' .newline();
		$html .= '</table>' .newline();
		$html .= '<p><input class = "button" type="submit" name="search" value="Search" />';
		$html .= '<input type="button" class ="button" value="Clear" onclick="reset_search_form(\'search_form\', \''.$action.'?'.key($key_val_id).'='. $key_val_id[key($key_val_id)] .'\')"/></p>';
		$html .= '</div></form>';
		$html .="<script>
		function reset_search_form(formId, location)
		{
			//For each form element set the value to default
			var elem = document.getElementById(formId).elements;
			for(var i = 0; i < elem.length; i++)
			{
				if (elem[i].type == 'text')
				{
					elem[i].value = '';
				}
				if (elem[i].type == 'select-one')
				{
					elem[i].value = 'false';
				}
			}
			window.location = location;
			
		}
		</script>";
	$buttons = '<input class = "button" style="width:200px" type="submit" name="verify_statement" value="Verify Transactions"/>';
	$enabled = false;
}


	//add form here
	$form_handler = 'list_account_activity.php';
	$html .= '<form action="' . $form_handler.'" method="POST">';
	$html .= createAccountingRecordsTableWithTotals($balance_array, $data, $table_columns, $enabled);
	$html .= $buttons;
	$html .= createHiddenInput('pos_account_id', $pos_account_id);
	$html .= createHiddenInput('tmp_sql', urlencode($tmp_sql));
	$html .= createHiddenInput('tmp_select_sql', urlencode($tmp_select_sql));
	$html .= createHiddenInput('order_by', urlencode($order_by));
	$html .= createHiddenInput('search_sql', urlencode($search_sql));
	$html .= '</form>';
	$html .= '<p>';


	$html .= '</p>';
	$html .= '<p>';
	$html .='<input class = "button" style="width:200px" type="button" name="return" value="Return To Accounts" onclick="open_win(\'list_accounts.php\')"/>';
	$html .= '</p>';


	include(HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);

}
function getAccountOpeningBalanceForActivityArray($pos_account_id)
{
		$balance_sql = "
	SELECT pos_account_balances.balance_date as date, pos_account_balances.balance_amount 
	FROM pos_account_balances
		WHERE pos_account_balances.pos_account_id = $pos_account_id 
		AND pos_account_balances.balance_date = (
		SELECT MAX( pos_account_balances.balance_date )
		FROM pos_account_balances
		WHERE pos_account_balances.pos_account_id = $pos_account_id)
		";
	$counter=0;
	$balance_data = getSQL($balance_sql);
	$balance_array=array();
	if (sizeof($balance_data)>0)
	{
		$balance_array[$counter]['balance'] = $balance_data[0]['balance_amount'];
		$balance_array[$counter]['date'] = $balance_data[0]['date'];
	}
	else
	{
		$balance_array[$counter]['balance'] = 0;
		$balance_array[$counter]['date'] = '';
	}
	$balance_array[$counter]['description'] = 'balance';
	return $balance_array;
}
function getAccountActivityTableSQL($pos_account_id)
{

	//need the debits and the credits....
	// cash debits are the payments, credits are the transfers (gj)
	// accounts debits are the
	// cc: debits are the transfers, credits are the payments
	// debit card: debits are the payments, there are no credits
	// checking account: debits are the payments, credits are deposits.
	

	$account_type = getAccountTypeName($pos_account_id);
	switch( $account_type)
	{
		//the account type determines the t-chart....
		//so register could be an account type
		// that would be cash
		// same with a check drop box
		//
		
		
		case 'Inventory Account':
			$sql = createInventoryAccountSummary($pos_account_id);
			break;
		case 'Expense Account':
			$sql = createExpenseAccountSummary($pos_account_id);
			break;
		case 'Credit Card':
			$sql = createCreditCardAccountSummary($pos_account_id);
			break;
		case 'Debit Card':
			$sql = createBankAccountSummary($pos_account_id);
			break;
		case 'Cash Account':
			$sql = createBankAccountSummary($pos_account_id);
			break;
		case 'Checking Account':
			$sql = createBankAccountSummary($pos_account_id);
			break;
		case 'Short Term Liability':

			break;
	}
	return $sql;	
}
function createInventoryAccountSummary($pos_account_id)
{
	//need the purchases on account, the payments to the account, the credit memos applied and the discounts
	$tmp_table_sql = purchases_journal_invoice_on_account_sql($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= purchases_journal_discounts_applied($pos_account_id);
	//$tmp_table_sql .= " UNION "; 
	//$tmp_table_sql .= purchases_journal_invoice_payments($pos_account_id);
	//$tmp_table_sql .= " UNION "; 
	//$tmp_table_sql .= transfers_to_account($pos_account_id);
		$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= payments_onto_account($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= payments_to_account($pos_account_id);
	
	return $tmp_table_sql;
}
function createExpenseAccountSummary($pos_account_id)
{
	$tmp_table_sql = general_journal_invoice_on_account_sql($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= general_journal_discounts_applied($pos_account_id);
	//$tmp_table_sql .= " UNION "; 
	//$tmp_table_sql .= general_journal_invoice_payments($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	//$tmp_table_sql .= transfers_to_account($pos_account_id);
	$tmp_table_sql .= payments_to_account($pos_account_id);
	$linked_pos_account_ids = getLinkedAccountId($pos_account_id);
	for($la=0;$la<sizeof($linked_pos_account_ids);$la++)
	{
		$tmp_table_sql .= " UNION "; 
		$tmp_table_sql = general_journal_invoice_on_account_sql($linked_pos_account_ids[$la]['pos_account_id']);
		$tmp_table_sql .= " UNION "; 
		$tmp_table_sql .= general_journal_discounts_applied($linked_pos_account_ids[$la]['pos_account_id']);
		$tmp_table_sql .= " UNION "; 
		$tmp_table_sql .= general_journal_invoice_payments($linked_pos_account_ids[$la]['pos_account_id']);
		$tmp_table_sql .= " UNION "; 
		$tmp_table_sql .= transfers_to_account($linked_pos_account_ids[$la]['pos_account_id']);
	}
	return $tmp_table_sql;
}
function createCreditCardAccountSummary($pos_account_id)
{

	
	$tmp_table_sql = cc_charges_from_general_journal($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= cc_charges_from_purchases_journal($pos_account_id);
	//$tmp_table_sql .= " UNION "; 
	//$tmp_table_sql .= transfers_to_account($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= payments_onto_account($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= payments_to_account($pos_account_id);
	
	
	return $tmp_table_sql;
}
function createBankAccountSummary($pos_account_id)
{
	//unfortunately the bank is opposite credit cards. Basically withdraws or payments are debits, and deposits are credits.
	//need the purchases on account, the payments to the account, the credit memos applied and the discounts
	$tmp_table_sql = bank_payments_from_general_journal($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= bank_payments_from_purchases_journal($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= payments_to_bank_account($pos_account_id);
	$tmp_table_sql .= " UNION "; 
	$tmp_table_sql .= payments_from_bank_account($pos_account_id);
	//$tmp_table_sql .= transfers_from_bank_account($pos_account_id);
	//$tmp_table_sql .= " UNION "; 
	//$tmp_table_sql .= deposits_to_bank_account($pos_account_id);
	$linked_pos_account_ids = getLinkedAccountId($pos_account_id);
	//preprint($linked_pos_account_ids);
	for($la=0;$la<sizeof($linked_pos_account_ids);$la++)
	{
		$tmp_table_sql .= " UNION "; 
		$tmp_table_sql .= bank_payments_from_general_journal($linked_pos_account_ids[$la]['pos_account_id']);
		$tmp_table_sql .= " UNION "; 
		$tmp_table_sql .= bank_payments_from_purchases_journal($linked_pos_account_ids[$la]['pos_account_id']);
		$tmp_table_sql .= " UNION "; 
		$tmp_table_sql .= payments_to_bank_account($linked_pos_account_ids[$la]['pos_account_id']);
		$tmp_table_sql .= " UNION "; 
		$tmp_table_sql .= payments_from_bank_account($linked_pos_account_ids[$la]['pos_account_id']);
	}
	return $tmp_table_sql;
}
function createAccountingRecordsTableWithTotals($balance_array, $data, $table_columns, $enabled, $class = 'generalTable', $number_of_rows_to_be_used_for_totals_caption = 1)
{
	//preprint($data);
	$order = recordsTableSortOrder();
	
	if (sizeof($data) > 0) 
	{ 
		// Table header:
		$getURL = getPageURLwithGETS();
	
		$html = '<table class = "'. $class . '">' .newline();
		$html .= '<thead><tr>' . newline();
		foreach($table_columns as $column)
		{
			if (isset($column['sort']))
			{
				$html .= '<th><a href="'.$getURL.'sort='.$column['mysql_field'].'&order='.$order.'">'.$column['th'].'</a></th>'.newline();
			}
			else
			{
				$html .= '<th>'.$column['th'].'</th>'.newline();
			}
		}
		$html .= '<th>Verify</th>'.newline();
		$html .= '<th>Balance</th>'.newline();
		
		$html .= '</tr></thead>'.newline();
		$html .= '<tbody>';
		$html .= '<tr style="background-color:yellow;border-bottom:1px solid rgb(50,50,50);">';
		if($number_of_rows_to_be_used_for_totals_caption > 0)
		{
			$html .= '<td colspan="'.$number_of_rows_to_be_used_for_totals_caption.'"><b>Totals</b></td>';
		}
		for($i=$number_of_rows_to_be_used_for_totals_caption;$i<sizeof($table_columns);$i++)
		{
			//
			if(isset($table_columns[$i]['total']))
			{	
				$html .= '<td><b>' .number_format(calculateSQLTotal($data, $table_columns[$i]['mysql_field']),0).'</b></td>';
			}
			else
			{
				$html .= '<td></td>';
			}
		}
		$html .= '</tr>';
		$balance = 0;
		
		

		$balance_avaialbe = ($balance_array[0]['date'] == '') ? false : true;
		$balanced_placed = false;
		// Fetch and print all the records....
		for($i=0;$i<sizeof($data);$i++)
		{
			
			if($balance_avaialbe)
			{
				//pprint($data[$i]['journal_id'] . ' ' . (strtotime($balance_array[0]['date']) -  strtotime($data[$i]['date'])));
				if(strtotime($balance_array[0]['date']) <= strtotime($data[$i]['date']) && !$balanced_placed)
				{
					//put the balance here
					$balanced_placed = true;
					//add the 'opening balance'
					$balance = $balance_array[0]['balance'];
					$html .= '<tr>';
					$html.= '<td>'.$balance_array[0]['date'].'</td>';
					for($i2=0;$i2<4;$i2++)
					{
						$html .= '<td></td>';
					}
					$html .= '<td>Opening Balance</td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>'.number_format($balance_array[0]['balance'],2).'</td>';
					$html .= '</tr>';
				}
			}
			$html .= '<tr>';
			foreach($table_columns as $column_data)
			{
				$html .= recordsTableTD($column_data,$data[$i]);
			}
			// add the verify check
			$html .= '<td><input  onchange="needToConfirm=true" type = "checkbox" id="'.$data[$i]['journal_id'].'" name="'.$data[$i]['journal_id'].'" ';
			if (!$enabled)
			{
				$html .= ' disabled = "disabled" ';
			}
			if(isset($data[$i]['verify']))
			{
				if ($data[$i]['verify'] == '1' || strtolower($data[$i]['verify']) == 'true' || strtolower($data[$i]['verify']) == 'checked' || strtolower($data[$i]['verify']) == 'yes')
				{
					$html .=  ' checked = "checked" ';
				}
			}
			$html .= ' />'.newline();
			//add the balance
			$balance = $balance - $data[$i]['debit'] + $data[$i]['credit'];
			$html .= '<td>'.number_format($balance,2) .'</td>'.newline();
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody></table>'.newline(); // Close the table.
	} 
	else 
	{ // If no records were returned.
		$html = '<p class="error">There are currently no records.</p>';
	}
	return $html;
}



?>

