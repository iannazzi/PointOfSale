<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Terminals';
$binder_name = 'Terminals';
$access_type = 'READ';
require_once ('../system_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_terminal_id',
											'mysql_search_result' => 'pos_terminal_id',
											'caption' => 'Terminal ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_terminal_id')
										),
										array(	'db_field' => 'pos_store_id',
											'mysql_search_result' => 'pos_store_id',
											'caption' => 'Store',	
											'type' => 'select',
											'html' => createStoreSelect('pos_store_id', 'false', 'on', '')
										),
										array(	'db_field' => 'terminal_name',
											'mysql_search_result' => 'terminal_name',
											'caption' => 'Terminal Name',	
											'type' => 'input',
											'html' => createSearchInput('terminal_name')
										)
							
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_terminal_id',
			'get_url_link' => "terminals.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_terminal_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_terminal_id',
			'sort' => 'pos_terminal_id'),
		array(
			'th' => 'Store Name',
			'mysql_field' => 'store_name',
			'sort' => 'store_name'),	
		array(
			'th' => 'Terminal Name',
			'mysql_field' => 'terminal_name',
			'sort' => 'terminal_name'),
		/*array(
			'th' => 'Cookie Name',
			'mysql_field' => 'cookie_name',
			'sort' => 'cookie_name'),*/
		array(
			'th' => 'Terminal Description',
			'mysql_field' => 'terminal_description',
			'sort' => 'terminal_description'),
			array(
			'th' => 'Location',
			'mysql_field' => 'location',
			'sort' => 'location'),
		array(
			'th' => 'Printer Name',
			'mysql_field' => 'printer_name',
			'sort' => 'printer_name'),
		array(
			'th' => 'Printer Name',
			'mysql_field' => 'printer_name',
			'sort' => 'printer_name'),
			
		array(
			'th' => 'CC Processor',
			'mysql_field' => 'cc_processor',
			'sort' => 'cc_processor'),
		array(
			'th' => 'Cash Drawer',
			'mysql_field' => 'cash_account',
			'sort' => 'cash_account'),
		array(
			'th' => 'Check Drawer',
			'mysql_field' => 'check_account',
			'sort' => 'check_account'),
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_terminals');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE terminals

SELECT  
		pos_terminals.*, printer_name, store1.store_name, CONCAT(store2.store_name, ' - ', gateway_provider, ' - ', model_name) as cc_processor, CONCAT(store3.store_name, ' - ', cash_accounts.legal_name) as cash_account, CONCAT(store4.store_name, ' - ', check_accounts.legal_name) as check_account,pos_accounts.company, pos_accounts.account_number
		FROM pos_terminals
		LEFT JOIN pos_stores as store1 ON  pos_terminals.pos_store_id = store1.pos_store_id
		LEFT JOIN pos_printers USING (pos_printer_id)
		LEFT JOIN pos_payment_gateways USING(pos_payment_gateway_id)
		LEFT JOIN pos_accounts ON pos_payment_gateways.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_stores as store2 ON  pos_payment_gateways.pos_store_id = store2.pos_store_id
		LEFT JOIN pos_accounts as cash_accounts ON  pos_terminals.default_cash_account_id = cash_accounts.pos_account_id
		LEFT JOIN pos_stores as store3 ON  cash_accounts.pos_store_id = store3.pos_store_id
		LEFT JOIN pos_accounts as check_accounts ON  pos_terminals.default_check_account_id = check_accounts.pos_account_id
		LEFT JOIN pos_stores as store4 ON  check_accounts.pos_store_id = store4.pos_store_id
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM terminals WHERE 1";

//create the search form
$action = 'list_terminals.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;


//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//create some buttons
//Add a button to add an expense
if(checkTerminal())
{
	$html.= '<p>Your computer/device & browser is registered under the name: ' . $_COOKIE['pos_terminal_name'] .'</p>';
	$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Edit This Terminal" onclick="open_win(\'terminals.php?type=view&pos_terminal_id='.getTerminalID($_COOKIE['pos_terminal_name']).'\')"/>';
}
else
{
	$html.= '<p>Your computer/device & browser is not a registered POS system</p>';
}

$html .= '<h3>To identify computers in the system a "Cookie" with the unique computer name is installed in the browser. Identifying the computer will allow setting preferences, like default printers, cash drawers, etc. Clearing the browser cache will remove the cookie and therefore require re-registering the computer. Each browser will need to be "registered" for the POS system to work. Add a computer here, then register it in the browser by choosing register on the terminal page. Terminal names are automatically made. In Firefox be carful to set up the preferences to keep cookies, yet remove all other history.</h3>';
$html .= '<p>';


$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Add A Terminal" onclick="open_win(\'terminals.php?type=Add\')"/>';
//$html .= '<input class = "button" type="button" style="width:300px" name="register" value="Register A POS Terminal" onclick="open_win(\'register_terminal.php\')"/>';
$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("pos_terminal_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
