<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Daily Sales Tax';
$binder_name = 'Sales Invoices';
$access_type = 'READ';
require_once ('../sales_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_sales_invoice_id',
											'mysql_search_result' => 'pos_sales_invoice_id',
											'caption' => 'Invoice ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_sales_invoice_id')
										),
										array(	'db_field' => 'invoice_number',
											'mysql_search_result' => 'invoice_number',
											'caption' => 'Invoice Number',	
											'type' => 'input',
											'html' => createSearchInput('invoice_number')
										),
										array(	'db_field' => 'last_name',
											'mysql_search_result' => 'last_name',
											'caption' => 'Last Name',	
											'type' => 'input',
											'html' => createSearchInput('last_name')
										),
										array(	'db_field' => 'invoice_date',
											'mysql_search_result' => 'invoice_date',
											'caption' => 'Invoice Date Start',
											'type' => 'start_date',
											'html' => dateSelect('invoice_date_start_date',valueFromGetOrDefault('invoice_date_start_date'))
										),
								array(	'db_field' => 'invoice_date',
											'mysql_search_result' => 'invoice_date',
											'caption' => 'Invoice Date End',	
											'type' => 'end_date',
											'html' => dateSelect('invoice_date_end_date',valueFromGetOrDefault('invoice_date_end_date'))
											)
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_sales_invoice_id',
			'get_url_link' => "retail_sales_invoice.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_sales_invoice_id'),
		array(
			'th' => 'Edit<br>Details',
			'mysql_field' => 'pos_sales_invoice_id',
			'get_url_link' => "sales_invoice_overview.php?type=edit",
			'url_caption' => 'Edit',
			'get_id_link' => 'pos_sales_invoice_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_sales_invoice_id',
			'sort' => 'pos_sales_invoice_id'),	
		array(
			'th' => 'Invoice<br>Number',
			'mysql_field' => 'invoice_number',
			'sort' => 'invoice_number'),
		array(
			'th' => 'Last Name',
			'mysql_field' => 'last_name',
			'sort' => 'last_name'),
		array(
			'th' => 'Invoice<br> Date',
			'mysql_field' => 'invoice_date',
			'sort' => 'invoice_date'),
		
	
		
		array(
			'th' => 'No <br>Tax <br>Sales',
			'mysql_field' => 'no_tax_sales',
			'sort' => 'no_tax_sales',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Full<br> Tax<br> Sales',
			'mysql_field' => 'regular_sales',
			'sort' => 'regular_sales',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Full<br> Tax<br> Sales<br>From Contents',
			'mysql_field' => 'regular_sales_from_contents',
			'sort' => 'regular_sales_from_contents',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Exemption<br> Sales',
			'mysql_field' => 'exempt_sales',
			'sort' => 'exempt_sales',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Exemption<br> Sales<br>from Contents',
			'mysql_field' => 'exempt_sales',
			'sort' => 'exempt_sales_from_contents',
			'round' => 2,
			'total' => 2),
			array(
			'th' => 'Gift<br> Cards',
			'mysql_field' => 'credit_cards_sold',
			'sort' => 'credit_cards_sold',
			'round' => 0,
			'total' => 0),
		array(
			'th' => 'Total <br>Goods & <br>Services',
			'mysql_field' => 'total_goods_and_service',
			'sort' => 'total_goods_and_service',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Returns',
			'mysql_field' => 'returns',
			'sort' => 'returns',
			'round' => 2,
			'total' => 2),
			array(
			'th' => 'Gross <br> Sales',
			'mysql_field' => 'gross',
			'sort' => 'gross',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Tax <br>Collected',
			'mysql_field' => 'tax_collected',
			'sort' => 'tax_collected',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Tax <br>Collected<br>From Contents',
			'mysql_field' => 'tax_collected_from_contents',
			'sort' => 'tax_collected_from_contents',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Local <br>Regular <br>Tax',
			'mysql_field' => 'local_regular_tax',
			'sort' => 'local_regular_tax',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'State <br>Regular <br>Tax',
			'mysql_field' => 'state_regular_tax',
			'sort' => 'state_regular_tax',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Total',
			'mysql_field' => 'grande_total',
			'sort' => 'grande_total',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Total Payment',
			'mysql_field' => 'total_payment',
			'sort' => 'total_payment',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Payment ERROR<BR>to fix',
			'mysql_field' => 'error',
			'sort' => 'error',
			'round' => 2,
			'total' => 2)

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'list_daily_sales_tax');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.








$tmp_sql = "
CREATE TEMPORARY TABLE sales_invoices

SELECT  
		pos_sales_invoice.pos_sales_invoice_id,
		pos_sales_invoice.invoice_number,
		date(pos_sales_invoice.invoice_date) as invoice_date,
		pos_customers.pos_customer_id,
		pos_customers.first_name,
		pos_customers.last_name,
		pos_customers.email1,
		pos_customers.phone,
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as total_payment,
		
";
$tmp_sql .= getInvoiceSumsSQL();
$tmp_sql .="
		
		FROM pos_sales_invoice
		
		LEFT JOIN pos_customers ON pos_sales_invoice.pos_customer_id = pos_customers.pos_customer_id



		


;


";
$tmp_select_sql = "SELECT *, total_payment - grande_total as error
	FROM sales_invoices WHERE 1";

//create the search form
$action = 'list_daily_sales_tax.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";

//create some buttons
//Add a button to add an expense
$html .= '<p>';
//$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Simple Sales Invoice" onclick="open_win(\'sales_invoice_overview.php?type=Simple\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Sales Invoices" onclick="open_win(\'list_retail_sales_invoices.php\')"/>';

$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Quaterly Sales Tax Collected" onclick="open_win(\'list_sales_tax_collected.php?\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);

if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html .= createRecordsTableWithTotals($data, $table_columns);

}


$html .= '<script>document.getElementsByName("pos_sales_invoice_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
