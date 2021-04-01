<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Sales Invoices';
$access_type = 'READ';
require_once('retail_sales_invoice_functions.php');
//need a terminal check here...
$pos_terminal_id = terminalCheck();
/*payment
cash
check
cc
store credit
gift card*/


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
										array(	'db_field' => 'invoice_status',
											'mysql_search_result' => 'invoice_status',
											'caption' => 'Invoice Status',	
											'type' => 'input',
											'html' => createSearchInput('invoice_status')
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
		/*array(
			'th' => 'Edit<br>Details',
			'mysql_field' => 'pos_sales_invoice_id',
			'get_url_link' => "sales_invoice_overview.php?type=edit",
			'url_caption' => 'Edit',
			'get_id_link' => 'pos_sales_invoice_id'),*/
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
			'th' => 'Status',
			'mysql_field' => 'invoice_status',
			'sort' => 'invoice_status'),
	
	/*	array(
			'th' => 'Total',
			'mysql_field' => 'grande_total',
			'sort' => 'grande_total',
			'round' => 2,
			'total' => 2),*/
	/*	total from contents has a rounding error
	array(
			'th' => 'Total<br>FROM<br>CONTENTS',
			'mysql_field' => 'grand_total_from_contents',
			'sort' => 'grand_total_from_contents',
			'round' => 2,
			'total' => 2),*/
	
	array(
			'th' => 'DRAFT Invoice Total<br>(via SUM of<br>the extension)',
			'mysql_field' => 'draft_extension',
			'sort' => 'draft_extension',
			'round' => 2,
			'total' => 2),
	array(
			'th' => 'CLOSED Invoice Total<br>(via SUM of<br>the extension)',
			'mysql_field' => 'sum_extension',
			'sort' => 'sum_extension',
			'round' => 2,
			'total' => 2),
	array(
			'th' => 'Extension<br>Error',
			'mysql_field' => 'err2',
			'sort' => 'err2',
			'round' => 2,
			'total' => 2),	
	array(
			'th' => 'Tax Collected',
			'mysql_field' => 'total_tax',
			'sort' => 'total_tax',
			'round' => 2,
			'total' => 2),
	array(
			'th' => 'Gift Cards <br> Sold',
			'mysql_field' => 'gift_cards_sold',
			'sort' => 'gift_cards_sold',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Promotions',
			'mysql_field' => 'promotion_total',
			'sort' => 'promotion_total',
			'round' => 2,
			'total' => 2),	
		
		array(
			'th' => 'Visa/MC<BR>Discover',
			'mysql_field' => 'credit',
			'sort' => 'credit',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'American<BR>Express',
			'mysql_field' => 'amex',
			'sort' => 'amex',
			'round' => 2,
			'total' => 2),		
		array(
			'th' => 'Cash',
			'mysql_field' => 'cash',
			'sort' => 'cash',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Check',
			'mysql_field' => 'lcheck',
			'sort' => 'lcheck',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Gift Card <br> Redeemed',
			'mysql_field' => 'gift_card',
			'sort' => 'gift_card',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Store Credit <br> redeemed',
			'mysql_field' => 'store_credit',
			'sort' => 'store_credit',
			'round' => 2,
			'total' => 2),
		
		array(
			'th' => 'Total Payment',
			'mysql_field' => 'total_payment',
			'sort' => 'total_payment',
			'round' => 2,
			'total' => 2),
		/*array(
			'th' => 'Payment ERROR<BR>to fix',
			'mysql_field' => 'error',
			'sort' => 'error',
			'round' => 2,
			'total' => 2)*/

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_sales_invoices_url');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.


//depending on the user level there will be a limit to the results.
//here we limit an associate on the search , and we do not sum. 
// A store manager will need to sum for a store
// an accountant will need full summations..... across stores.....

//so there will be two binders...
//POS_V1 which will be a limited binder
//list_invoices_V1 which will be a full search
//partial_invoices_v1 partial search, etc.... 





$tmp_sql = "
CREATE TEMPORARY TABLE sales_invoices

SELECT  
		pos_sales_invoice.pos_sales_invoice_id,
		pos_sales_invoice.invoice_number,
		pos_sales_invoice.invoice_status,
		date(pos_sales_invoice.invoice_date) as invoice_date,
		pos_customers.pos_customer_id,
		pos_customers.first_name,
		pos_customers.last_name,
		pos_customers.email1,
		pos_customers.phone,
		
		(SELECT sum(
		
			CASE content_type 
		
			WHEN ('PRODUCT') THEN
			quantity*(if(discount_type='DOLLAR',sale_price-discount,sale_price -sale_price*discount/100))-applied_instore_discount
			WHEN ('SERVICE') THEN
			quantity*(if(discount_type='DOLLAR',sale_price-discount,sale_price -sale_price*discount/100))-applied_instore_discount
			WHEN ('CREDIT_CARD') THEN
			quantity*(retail_price)
			
			WHEN ('SHIPPING') THEN
			quantity*(retail_price)
			
			ELSE
			quantity*(if(discount_type='DOLLAR',sale_price-discount,sale_price -sale_price*discount/100))-applied_instore_discount
			END
			+ tax_total)
			
		 - 
		
			(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
			AND promotion_type = 'Post Tax') 
		FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
		) as grand_total_from_contents,
		
		(SELECT sum(extension) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type = 'CREDIT_CARD') as gift_cards_sold,
		
		(select sum(tax_total) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND invoice_status = 'CLOSED'
		) as total_tax,
		
		(SELECT sum(extension)  - 
		
			(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
			AND promotion_type = 'Post Tax') FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND invoice_status = 'CLOSED'
		) as sum_extension,
		
		
			(SELECT sum(extension)  - 
		
			(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
			AND promotion_type = 'Post Tax') FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND invoice_status = 'DRAFT'
		) as draft_extension,
		
		(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
			AND promotion_type = 'Post Tax') as promotion_total,
		
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as total_payment,
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'Cash') as cash,
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'Check') as lcheck,
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'Gift Card') as gift_card,
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'Store Credit') as store_credit,
		
		
			(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_group = 'CREDIT_CARD' AND pos_customer_payment_methods.payment_type != 'American Express') as credit,
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'American Express') as amex
";
//$tmp_sql .= getInvoiceSumsSQL();

//$sale_price = getAppliedDiscountItemPriceSQL();


$tmp_sql .="
		
		FROM pos_sales_invoice
		
		LEFT JOIN pos_customers ON pos_sales_invoice.pos_customer_id = pos_customers.pos_customer_id



		


;


";
//$tmp_select_sql = "SELECT *, grand_total_from_contents - grande_total as err2, total_payment - grande_total as error	FROM sales_invoices WHERE 1";
$tmp_select_sql = "SELECT *, grand_total_from_contents-sum_extension as err2
	FROM sales_invoices WHERE 1";

//create the search form
$action = 'list_retail_sales_invoices.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
if ($order_by) $tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";

//create some buttons
//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Simple Sales Invoice" onclick="open_win(\'sales_invoice_overview.php?type=Simple\')"/>';

/*$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Full Sales Invoice" onclick="open_win(\'sales_invoice_overview.php?type=ADD\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Daily Sales Tax Collected" onclick="open_win(\'list_daily_sales_tax.php?\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Quaterly Sales Tax Collected" onclick="open_win(\'list_sales_tax_collected.php?\')"/>';*/


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
