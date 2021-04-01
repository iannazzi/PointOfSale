<?php
/*
	craig iannazzi 2-11-2013 with a medium cold. some sneezing. I just drank nyquill.
	
	so it works like this:
	a db table contains a big list of random card numbers. the list will tell me the date printed. meaning it was used.
	when I need a number, i go to this list, get a number, stick it on the card. If i loose the sticker, the number will never be avialable again.
	the card then eventually gets a number.
	Hopefully we make a stack of gift cards with numbers on them, all random and unique.
	on the invoice page, we can add a gift card as a product and we will have to put the card number in... or the whole thing spits out of a printer.... or we zap it.. which sounds best.
	then the card number is tied to an amount, invoice number, possible a customer id.
*/
$page_title = 'Store Credits';
$binder_name = 'Store Credits';
$access_type = 'READ';
require_once ('../sales_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_store_credit_id',
											'mysql_search_result' => 'pos_store_credit_id',
											'caption' => 'Store Credit ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_store_credit_id')
										),
										
										array(	'db_field' => 'card_number',
											'mysql_search_result' => 'card_number',
											'caption' => 'Card Number',	
											'type' => 'input',
											'html' => createSearchInput('card_number')
										),
										array(	'db_field' => 'last_name',
											'mysql_search_result' => 'last_name',
											'caption' => 'Last Name',	
											'type' => 'input',
											'html' => createSearchInput('last_name')
										)
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_store_credit_id',
			'get_url_link' => "store_credits.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_store_credit_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_store_credit_id',
			'sort' => 'pos_store_credit_id'),	
		array(
			'th' => 'Card Number',
			'mysql_field' => 'card_number',
			'sort' => 'card_number'),
		array(
			'th' => 'Card Type',
			'mysql_field' => 'card_type',
			'sort' => 'card_type'),
		array(
			'th' => 'Original Amount',
			'mysql_field' => 'original_amount',
			'sort' => 'original_amount'),
		array(
			'th' => 'Amount Remaining',
			'mysql_field' => 'amount_remaining',
			'sort' => 'amount_remaining'),
		array(
			'th' => 'Invoices',
			'mysql_field' => 'invoices',
			'sort' => 'invoices'),
		array(
			'th' => 'Locked',
			'type' =>'checkbox',
			'mysql_field' => 'locked',
			'sort' => 'locked'),	
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'sort' => 'comments'),



		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saced_store_credit');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE store_credit


SELECT pos_store_credit_id, pos_store_credit.card_number, card_type, pos_customers.last_name, pos_customers.first_name,  pos_store_credit.original_amount, locked, pos_store_credit.comments,

	(SELECT GROUp_CONCAT(pos_sales_invoice.pos_sales_invoice_id) 
		FROM pos_sales_invoice
		LEFT JOIN pos_sales_invoice_to_payment ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_to_payment.pos_sales_invoice_id
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id
		WHERE pos_customer_payments.pos_store_credit_id = pos_store_credit.pos_store_credit_id) as invoices
	
FROM pos_store_credit
LEFT JOIN pos_customers ON pos_store_credit.pos_customer_id = pos_customers.pos_customer_id


";
$tmp_select_sql = "SELECT *
	FROM store_credit WHERE 1";

//create the search form
$action = 'list_store_credits.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


$tmp_select_sql  .=  " LIMIT 100";

//create some buttons
//Add a button to add an expense
$html .= '<p>';
//$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Batch Test" onclick="open_win(\'store_credits.php?type=batch_test\')"/>';

$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Print Card Numbers" onclick="open_win(\'store_credits.php?type=Print\')"/>';
/*$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Get A Card Number" onclick="open_win(\'store_credits.php?type=Single\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Assign Value to Store Credit Card" onclick="open_win(\'store_credits.php?type=ASSIGN\')"/>';*/
$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="List Printed Store Credit Card Numbers" onclick="open_win(\'list_printed_store_credits.php\')"/>';


$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);

$html .= '<script>document.getElementsByName("card_number")[0].focus();</script>';


if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc, $tmp_sql);
	$data = getTransactionSQL($dbc, $tmp_select_sql);
	closeDB($dbc);

	for ($i = 0; $i < sizeof($data); $i ++)
	{
		$data[ $i ]['amount_remaining'] = getStoreCreditCardValue($data[ $i ]['pos_store_credit_id']);
	}
	$html .= createRecordsTable($data, $table_columns);
}
//preprint($data);





include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
