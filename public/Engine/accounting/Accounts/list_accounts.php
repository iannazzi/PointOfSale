<?php
/*
	list_accounts.php
	craig Iannazzi 4-23-12
*/
$binder_name = 'Accounts';
$access_type = 'READ';
$page_title = 'Accounts';
require_once ('../accounting_functions.php');



//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.

/* BALANCE:
	Take the most recent balance point
	Add to it all the payments to the account
	
	Add all the invoices from the general journal 
	
	Add all the invoices - discounts applied from the purchases journal
	
	Subtract all the payments from the account



		

		
		- 
		

		
		
		
		

*/
$tmp_sql = "
CREATE TEMPORARY TABLE accounts

SELECT pos_accounts.*, pos_account_type.account_type_name, pos_chart_of_accounts.account_number  AS chart_account_number, pos_chart_of_accounts.account_name ,
		
		
		
		COALESCE((SELECT sum(entry_amount-discount_applied) FROM pos_general_journal WHERE pos_account_id = pos_accounts.pos_account_id AND entry_type = 'Invoice'),0) 
		
		as gj_invoices,
		
		(SELECT COALESCE(sum(invoice_amount-discount_applied),0) FROM pos_purchases_journal WHERE invoice_type = 'Regular' AND pos_account_id = pos_accounts.pos_account_id)
		as pj_invoices,
		
		(SELECT COALESCE(sum(invoice_amount),0) FROM pos_purchases_journal WHERE invoice_type = 'Credit Memo' AND pos_account_id = pos_accounts.pos_account_id)
		
		as pj_credits,
				
		(SELECT COALESCE(sum(payment_amount),0) FROM pos_payments_journal WHERE  (pos_payments_journal.pos_account_id = pos_accounts.pos_account_id OR pos_payments_journal.pos_account_id IN (SELECT act2.pos_account_id FROM pos_accounts as act2 WHERE act2.linked_pos_account_id = pos_accounts.pos_account_id)))
		
		
		as payments_using,
		
		(SELECT COALESCE(sum(payment_amount),0) FROM pos_payments_journal WHERE pos_payee_account_id = pos_accounts.pos_account_id )
		
		
		 as payment_to
		
		




FROM pos_accounts
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
LEFT JOIN pos_chart_of_accounts
ON pos_chart_of_accounts.pos_chart_of_accounts_id = pos_accounts.parent_pos_chart_of_accounts_id
WHERE 1

;
";
$tmp_select_sql = "SELECT *, IF((SELECT account_type FROM pos_accounts LEFT JOIN pos_account_type USING (pos_account_type_id) WHERE pos_account_id = accounts.pos_account_id) = 'ASSETS', IF((SELECT account_type_name FROM pos_accounts LEFT JOIN pos_account_type USING (pos_account_type_id) WHERE pos_account_id = accounts.pos_account_id) = 'DEBIT CARD' , 0 ,balance_init-gj_invoices-pj_invoices +pj_credits - payments_using + payment_to), balance_init+gj_invoices+pj_invoices -pj_credits + payments_using - payment_to) as balance FROM accounts WHERE 1";


//$tmp_select_sql = "SELECT *,  -payment_to as balance FROM accounts WHERE 1";


//define the search table
$search_fields = array(				array(	'db_field' => 'company',
											'mysql_search_result' => 'company',
											'caption' => 'Company',	
											'type' => 'input',
											'html' => createSearchInput('company')
										),
										array(	'db_field' => 'account_number',
											'mysql_search_result' => 'account_number',
											'caption' => 'Account Number',	
											'type' => 'input',
											'html' => createSearchInput('account_number')),
											array(	'db_field' => 'pos_account_type_id',
											'mysql_search_result' => 'pos_account_type_id',
											'caption' => 'Account Type',	
											'type' => 'select',
											'html' => createAccountTypeSelect('pos_account_type_id', valueFromGetOrDefault('pos_account_type_id'), 'all')),
										array(	'db_field' => 'parent_pos_chart_of_accounts_id',
											'mysql_search_result' => 'parent_pos_chart_of_accounts_id',
											'caption' => 'Parent Chart Of Account',	
											'type' => 'select',
											'html' => createChartOfAccountSelect('parent_pos_chart_of_accounts_id', valueFromGetOrDefault('parent_pos_chart_of_accounts_id'), 'all'))
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_account_id',
			'get_url_link' => "accounts.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_account_id'),
		array(
			'th' => 'List <br> Activity',
			'mysql_field' => 'pos_account_id',
			'get_url_link' => "list_account_activity.php",
			'url_caption' => 'List',
			'get_id_link' => 'pos_account_id'),
		array(
			'th' => 'Company',
			'mysql_field' => 'company',
			'sort' => 'company'),
		array(
			'th' => 'Active',
			'mysql_field' => 'active',
			'type' => 'checkbox',
			'sort' => 'active'),
		array(
			'th' => 'Account Number',
			'mysql_field' => 'account_number',
			'encrypted' => 1,
			'sort' => 'account_number'),
		array(
			'th' => 'Account Type',
			'mysql_field' => 'account_type_name',
			'sort' => 'account_type_name'),
		array(
			'th' => 'Current<br>Balance',
			'mysql_field' => 'balance',
			'round' => 0,
			'total' => 0,
			'sort' => 'balance'),
		array(
			'th' => 'Parent Chart of Account Number',
			'mysql_field' => 'chart_account_number',
			'sort' => 'chart_account_number'),
		array(
			'th' => 'Parent Chart of Account Name',
			'mysql_field' => 'account_name',
			'sort' => 'account_name'),
		array(
			'th' => 'Representative',
			'mysql_field' => 'primary_contact',
			'sort' => 'primary_contact'),
		array(
			'th' => 'Email',
			'mysql_field' => 'email',
			'sort' => 'email'),
		array(
			'th' => 'Phone',
			'mysql_field' => 'phone',
			'sort' => 'phone'),
		/*array(
			'th' => 'Fax',
			'mysql_field' => 'fax',
			'sort' => 'fax'),			
		array(
			'th' => 'Address 1',
			'mysql_field' => 'address1',
			'sort' => 'address1'),			
		array(
			'th' => 'Address 2',
			'mysql_field' => 'address2',
			'sort' => 'address2'),
		array(
			'th' => 'City',
			'mysql_field' => 'city',
			'sort' => 'city'),
		array(
			'th' => 'State',
			'mysql_field' => 'state',
			'sort' => 'state'),
		array(
			'th' => 'Zip',
			'mysql_field' => 'zip',
			'sort' => 'zip'),
		array(
			'th' => 'Terms',
			'mysql_field' => 'terms',
			'sort' => 'terms')*/);

$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_account');


//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" name="add_account" value="Add Account" onclick="open_win(\'accounts.php?type=add\')"/>';


$html .= '</p>';

//create the search form
$action = 'list_accounts.php';
$html .= createSearchForm($search_fields,$action);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[2]['mysql_field'], 'ASC');
$tmp_select_sql  .=  " ORDER BY $order_by";

if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	//preprint($data);
	closeDB($dbc);
	//now make the table
	$html .= createRecordsTableWithTotals($data, $table_columns);
}

$html .= '<script>document.getElementsByName("company")[0].focus();</script>';

include (HEADER_FILE);

echo $html;

include (FOOTER_FILE);
?>
