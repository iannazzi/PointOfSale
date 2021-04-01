<?php
/*
	Craig Iannazzi 2-11-2013
	Missed the trip stitch by one day bad gift
	
	here is how the store credit cards will work. 
	we will make 'batches' of cards.
	we will create N unique ids, insert them into the DB. this will essentially create and reserve the 'card numbers'. 
	we will then need to 'print' the card numbers onto labels with the b-code. this means we need a batch creation id, probably the datetime...
	
	
*/
if(isset($_GET['type']))
{
	$type = $_GET['type'];
}
else
{
	trigger_error('missing type');
}
$page_title = 'Store Credits';
$binder_name = 'Store Credits';
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../sales_functions.php');

$complete_location = 'list_store_credits.php';
$cancel_location = 'list_store_credits.php?message=Canceled';
$form_handler = 'store_credits.form.handler.php';

//**********************************   DATA PREP    **************************//
if(strtoupper($type) == 'ADD')
{
	//essentially there is no such thing as 'add'
	
}
elseif (strtoupper($type) == 'EDIT')
{
	//here we should be able to adjust information
	$pos_store_credit_id = getPostOrGetID('pos_store_credit_id');
	$header = '<p>EDIT Credit</p>';
	$page_title = 'Edit Credit';
	$data_table_def_no_data = createStoreCreditTableDef($type, $pos_store_credit_id);	
	$db_table = 'pos_store_credit';
	$key_val_id['pos_store_credit_id'] = $pos_store_credit_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_store_credit_id = getPostOrGetID('pos_store_credit_id');
	$edit_location = 'store_credits.php?pos_store_credit_id='.$pos_store_credit_id.'&type=edit';
	$db_table = 'pos_store_credit';
	$key_val_id['pos_store_credit_id']  = $pos_store_credit_id;
	$data_table_def = createStoreCreditTableDef($type, $pos_store_credit_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
else
{
}

//**********************************   HTML PREP    **************************//
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Store Credit</p>';
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Store Credits" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
	
	//now tell me about the card.... what invoices were it used on?
	//to reiterate the process....
	//customer purchases gift card.... once the payment goes through the gift card number registers in the pos_store_credit table. no link to the original sales id? why not? a gift card can always have value added... so the barcode can show up on mulitple invoices.....
	
	//which invoices were used to add value to a card?
	$card_number = getStoreCreditCardNumber($pos_store_credit_id);
	$add_sql = "

SELECT pos_sales_invoice.pos_sales_invoice_id, pos_sales_invoice_content_id, barcode, retail_price, extension, quantity FROM 
	pos_sales_invoice
	LEFT JOIN pos_sales_invoice_contents ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	WHERE pos_sales_invoice_contents.barcode = '$card_number' 
	AND pos_sales_invoice.payment_status = 'PAID' AND pos_sales_invoice.invoice_status='CLOSED' AND pos_sales_invoice_contents.content_type ='CREDIT_CARD'

";
	//which invoices were paid using a gift card or store credit && which invoices were assigned a refund to store credit
	
	$used_sql ="SELECT pos_sales_invoice_id, pos_customer_payment_id, payment_amount FROM pos_customer_payments 
				LEFT JOIN pos_sales_invoice_to_payment USING (pos_customer_payment_id) WHERE pos_store_credit_id = $pos_store_credit_id";

	// what cards were "created" and donated... and how does this work?
	//basically create an invoice.. and pay to "other" account - like a non posting account or a "fake cash drawer"
	
	/*	
		$50 donation gift card (get receipt) to breast cancer awarness
		
		howeverrrrr I have only given one out.... no idea when or if it will ever return...
		and no idea if it is a true charity or if i will get a receipt....
		So we make a fake account to exchange fake money, however we are now liable for that card, which is real.
												debit		credit
		liability credit gift cards payable					$50
		pending charity contributions			$50				
		^ This account does not get "expensed"
		
		When redeemed this is what it should look like:
																debit		credit
		charitable contributions expense 						$29 or whatever the cogs ends up being.		
				or promotion expense....
		cost of goods sold (cogs - product and labor)						$29 

		liability - gift cards payable							$50				
		asset - Revenue														$0
		pending charitable contributions									$50
		
		
		maybe we do an invoice, then pay using pending account
		give the gift card out
		record the "sale" COGS as an expense???		
	*/
	
	
	// how about "promotion" gift cards
	
	/*	
		$100  gift card for $75
												should have		should give
		Credit card A/R or cash						$75
		Marketing Expense							$25
		credit gift cards payable									$100
		
		invoice - $100 gift card.... Promotion $25
		
	*/
		
	$html.='<h3>Current card value is $' . number_format(getStoreCreditCardValue($pos_store_credit_id),2) . '</h3>';

	$add_table_columns = array(
		array(
			'th' => 'Invoice',
			'mysql_field' => 'pos_sales_invoice_id',
			'get_url_link' => "../POS_V1/retail_sales_invoice.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_sales_invoice_id'),
		array(
			'th' => 'Amount',
			'mysql_field' => 'extension',
			'total' => 2,
			'round' => 2),
		);
	$add_data = getSQL($add_sql);
	$html.='<p>Invoices Used to add value to the card</p>';
	$html.= createRecordsTableWithTotals($add_data, $add_table_columns);
	
	
	$used_table_columns = array(
		array(
			'th' => 'Invoice',
			'mysql_field' => 'pos_sales_invoice_id',
			'get_url_link' => "../POS_V1/retail_sales_invoice.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_sales_invoice_id'),
		array(
			'th' => 'Payment ID',
			'mysql_field' => 'pos_customer_payment_id',
			'get_url_link' => "../POS_V1/payments.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_customer_payment_id'),
		array(
			'th' => 'Payment Amount',
			'mysql_field' => 'payment_amount',
			'total' => 2,
			'round' => 2),
		
		);
	$used_data = getSQL($used_sql);
	$html.='<p>Invoices Card was used to pay</p>';
	$html.= createRecordsTableWithTotals($used_data, $used_table_columns);
		
}
	
elseif (strtoupper($type) == 'EDIT')
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	$html = createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("card_number")[0].focus();</script>';
}
elseif (strtoupper($type) == 'ASSIGN')
{	
	//add something here for customer selection...
	//customer ajax table....
	$pos_store_credit_id = 'TBD';
	$table_def = createStoreCreditTableDef($type, $pos_store_credit_id);
	$big_html_table = createHTMLTableForMYSQLInsert($table_def);
	//now we need to expense it....noper...
	//$big_html_table .= createChartOfAccountsExpenseCategorySelect('pos_chart_of_accounts_id', 'false');
	$big_html_table .= createHiddenInput('type', $type);
	$html = createFormForMYSQLInsert($table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("card_number")[0].focus();</script>';
}
elseif (strtoupper($type) == 'PRINT')
{
	//here is where we 'create' the cards. These ids will be added to the system.
	//basically need a box that says 'how many to create'
	//this should be modified for different print options. 
	//right now I am going to print labels. because that is what I know...
	
	
		
		$html = '<p>Create Unique CC numbers for store credit cards</p>';
		$html .= '<form action="' . $form_handler.'" method="post">';
		$html .= 'Quantity (80 per page on Avery 5167): <INPUT TYPE="TEXT" value="80" class="lined_input"  '. numbersOnly() . ' id="qty" style = "width:20px;" NAME="qty"/>'.newline();
		$html .= 'Starting column: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="column_offset" style = "width:20px;" NAME="column_offset"/>'.newline();
		$html .= 'Starting row: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="row_offset" style = "width:20px;" NAME="row_offset"/>'.newline();
		$html .= createHiddenInput('type', $type);
		$html .= '<input class = "button" style="width:150px" type="submit" name="select" value="Open Label File"/>';
		$html .= '</form>';
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Store Credits" onclick="window.location = \''.$complete_location.'\'" />';
	

	
}
elseif (strtoupper($type) == 'SINGLE')
{
	$html = '';
	$card_number = getCardNumber_v2();
	$html = '<p>Your unique card number is: ' . formatCardNumber($card_number). '</p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Store Credits" onclick="window.location = \''.$complete_location.'\'" />';
}
elseif (strtoupper($type) == 'BATCH_TEST')
{
	$card_numbers = createBatchOfCardNumbers(1);
	$html = '<p>Generated the following unique card numbers<p>';
	for($cn=0;$cn<sizeof($card_numbers);$cn++)
	{
		$html .= '<p>' . $cn. ' Your unique card number is: ' . formatCardNumber($card_numbers[$cn]). '</p>';
	}
}
else
{
	$html = 'type error';
}
//**********************************   SHOW HTML    **************************//

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

//**********************************   FUNCTIONS    **************************//

function createStoreCreditTableDef($type, $pos_store_credit_id)
{
	if ($pos_store_credit_id =='TBD')
	{
		$unique_validate = array('unique' => 'card_number', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_store_credit_id'] = $pos_store_credit_id;
		$unique_validate = array('unique' => 'card_number', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_store_credit_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Credit ID',
								'value' => $pos_store_credit_id,
								'validate' => 'none'
								
								),
							
						array('db_field' =>  'card_number',
								'type' => 'input',
								'caption' => 'Card Number',
								'db_table' =>'pos_store_credit',
								'validate' =>   $unique_validate),
						array('db_field' =>  'card_type',
								'type' => 'select',
								'caption' => 'Card Type',
								'html' => createEnumSelect('card_type','pos_store_credit', 'card_type', 'false',  'off')),
						array('db_field' => 'date_issued',
								'caption' => 'Date Issued',
								'type' => 'date',
								'separate_date' => 'date',
								'tags' => ' ',
								'html' => dateSelect('date_issued',''),
								'validate' => 'date'),
								

						/*array('db_field' => 'inventory_date',
								'post_name' => 'inventory_time',
								'caption' => 'Time (00:00:00 format)',
								'type' => 'time',
								'tags' => '',
								//'html' => timeSelect('inventory_time','',''),
								'validate' => 'time'),*/
								
								
						/*array('db_field' =>  'original_amount',
								'type' => 'input',
								'caption' => 'Original Amount',
								'validate' => 'number'),*/
						array('db_field' =>  'locked',
								'type' => 'checkbox',
								'caption' => 'Locked?',
								'value' => '0'),
						
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments'),
						
						);	


}
function getStoreCreditCardNumber($pos_store_credit_id)
{	
	$card_number = getSingleValueSQL("SELECT card_number FROM pos_store_credit WHERE pos_store_credit_id = $pos_store_credit_id");
	return $card_number;
}
?>