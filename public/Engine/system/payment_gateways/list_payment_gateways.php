<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Payment Gateways';
$binder_name = 'Payment Gateways';
$access_type = 'READ';
require_once ('../system_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_gateway_id',
											'mysql_search_result' => 'pos_gateway_id',
											'caption' => 'Gateway ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_ppos_gateway_idrinter_id')
										),
										array(	'db_field' => 'pos_store_id',
											'mysql_search_result' => 'pos_store_id',
											'caption' => 'Store',	
											'type' => 'select',
											'html' => createStoreSelect('pos_store_id', 'false', 'on', '')
										),
										array(	'db_field' => 'gateway_provider',
											'mysql_search_result' => 'gateway_provider',
											'caption' => 'Gateway Provider',	
											'type' => 'input',
											'html' => createSearchInput('gateway_provider')
										),
										array(	'db_field' => 'printer_description',
											'mysql_search_result' => 'printer_description',
											'caption' => 'Printer Description',	
											'type' => 'input',
											'html' => createSearchInput('printer_description')
										)
							
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_payment_gateway_id',
			'get_url_link' => "payment_gateways.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_payment_gateway_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_payment_gateway_id',
			'sort' => 'pos_payment_gateway_id'),
		array(
			'th' => 'Online or Offline',
			'mysql_field' => 'line',
			'sort' => 'line'),	
		array(
			'th' => 'Receivable <br> Account Name',
			'mysql_field' => 'company',
			'sort' => 'company'),	
		array(
			'th' => 'Store',
			'mysql_field' => 'store_name',
			'sort' => 'store_name'),
			
		array(
			'th' => 'Gateway Provider',
			'mysql_field' => 'gateway_provider',
			'sort' => 'gateway_provider'),
		array(
			'th' => 'Model',
			'mysql_field' => 'model_name',
			'sort' => 'model_name'),		
		array(
			'th' => 'Login Id',
			'mysql_field' => 'login_id',
			'encrypted' => 1,
			'sort' => 'login_id'),
			
		array(
			'th' => 'Transaction Key',
			'mysql_field' => 'transaction_key',
			'encrypted' => 1,
			'sort' => 'transaction_key'),
			
			array(
			'th' => 'Website',
			'mysql_field' => 'website_url',
			'html_new_link' => 1,
			'sort' => 'website_url'),
			
			array(
			'th' => 'User Name',
			'mysql_field' => 'user_name',
			'encrypted' => 1,
			'sort' => 'user_name'),
			
			array(
			'th' => 'Password',
			'mysql_field' => 'password',
			'encrypted' => 1,
			'sort' => 'password'),
		
		
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),
	array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'sort' => 'comments'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_payment_gateways');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE payment_gateways

SELECT  
		pos_payment_gateways.*, store_name, pos_accounts.company
		FROM pos_payment_gateways
		LEFT JOIN pos_stores USING (pos_store_id)
		LEFT JOIN pos_accounts USING (pos_account_id)
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM payment_gateways WHERE 1";

//create the search form
$action = 'list_payment_gateways.php';
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

$html .= '<p>';

$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Add A Payment Gateway" onclick="open_win(\'payment_gateways.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("pos_payment_gateway_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
