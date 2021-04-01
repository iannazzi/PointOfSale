<?php
/*
*	pos_database_commands.php
*	In an attempt to reduct the amount of code for interacting with the database I am going to include all mysql queries here
*	These are the functions need to write, update, insert, get, products, manufactureres...etc

*/


$page_level = 5;
$page_navigation = 'sales';

require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);
//make sure we are in https....
checkHTTPS();

function getDiscountName($pos_discount_id)
{
	$sql = "SELECT discount_name FROM pos_discounts WHERE pos_discount_id=$pos_discount_id";
	Return getSingleValueSQL($sql);
}

function getDiscountCodes()
{
	if (checkIfUserIsAdmin($_SESSION['pos_user_id']))
	{
		$sql = "SELECT pos_discount_id, discount_name FROM pos_discounts WHERE active=1";
	}
	else
	{
		$sql = "SELECT pos_discount_id, discount_name FROM pos_discounts WHERE active=1 AND admin_only != 1";
	}
	$discount_codes = getFieldRowSQL($sql);
	if(sizeof($discount_codes) == 0)
	{
		$discount_codes['pos_discount_id'] = array();
		$discount_codes['discount_name'] = array();
	}
	return $discount_codes;
}

function createIphoneRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id, $invoice_table_name)
{

$table_object_name = $invoice_table_name . '_object';

	$tax_category_names_ids = getSalesTaxCategoriesIdsAndNames();
	$discount_codes = getDiscountCodes();

	$columns = array(
		
				array(
					'db_field' => 'pos_product_id',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'pos_product_sub_id',
					'type' => 'hidden',
					),
				/*array(
					'db_field' => 'card_number',
					'type' => 'hidden',
					'POST' => 'no'
					),*/
				array(
					'db_field' => 'content_type',
					'type' => 'hidden'
					),
				array(
					'db_field' => 'pos_state_tax_jurisdiction_id',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'pos_state_regular_sales_tax_rate_id',
					'type' => 'hidden',
					//'POST' => 'no'

					),
				array(
					'db_field' => 'pos_state_exemption_sales_tax_rate_id',
					'type' => 'hidden',
					//'POST' => 'no'

					),
				array(
					'db_field' => 'state_regular_tax_rate',
					'type' => 'hidden',

					),
				array(
					'db_field' => 'state_exemption_tax_rate',
					'type' => 'hidden',

					),
				array(
					'db_field' => 'state_exemption_value',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'pos_local_tax_jurisdiction_id',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'pos_local_regular_sales_tax_rate_id',
					'type' => 'hidden',
					///'POST' => 'no'
					),
				array(
					'db_field' => 'pos_local_exemption_sales_tax_rate_id',
					'type' => 'hidden',
					//'POST' => 'no'
					),
				array(
					'db_field' => 'local_regular_tax_rate',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'local_exemption_tax_rate',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'local_exemption_value',
					'type' => 'hidden',
					),
			/*	array(
					'db_field' => 'tax_type',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'item_tax_type',
					'type' => 'hidden',
					'price_array_index' => 'quantity',
					'POST' => 'no'
					),*/
				/*array(
					'db_field' => 'exemption_value',
					'type' => 'hidden',
					),*/

				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'row_checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'input',
				'element' => 'input',
				'element_type' => 'none',
				'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										'readOnly' => '"true"',
										'size' => '"3"')
					),
				array('db_field' => 'barcode',
					'caption' => 'Code',
					//'td_width' => '50%',
					//'th_width' => '50%',
					'word_wrap' => 10,
					'type' => 'hidden',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"30"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
				array('caption' => 'Description',
						'db_field' => 'description',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"40"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
			
				array('caption' => 'Price',
					'db_field' => 'retail_price',
					'type' => 'input',
					'round' => 2,
					'valid_input' => '0123456789.',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
				array('caption' => 'Sale Price',
					'db_field' => 'sale_price',
					'type' => 'input',
					'round' => 2,
					'valid_input' => '0123456789.',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){calculateTotals(this);}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
				array('caption' => 'QTY',
					'db_field' => 'quantity',
					'type' => 'input',
					'valid_input' => '-01',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"3"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){calculateTotals(this);}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
											array('db_field' => 'special_order',
					'caption' => 'Order',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);enablePaidCheck(this)}'
											),
					'td_tags' => array(	'className' => '"test"',
										//'style.backgroundColor' => '"#fff";',
										//'style.textAlign' => '"center";',
										//'style.verticalAlign' => '"middle";',
										//'align' => '"center"'
											)	),
				array('db_field' => 'paid',
					'caption' => 'Paid',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	
					'disabled' => 'true','onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);calculateTotals(this);}'
											)),
				array('db_field' => 'ship',
					'caption' => 'Ship',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);updateShipping(this);}'
											),
					'td_tags' => array(	'className' => '"test"',
										'style.backgroundColor' => '"#fff";',
										'style.textAlign' => '"center";'
											)),						
				array('caption' => 'Discount<br>Code<br>(Required)',
					'db_field' => 'pos_discount_id',
					'type' => 'select',
					//this part is for the 'view'
					//'html' => createDiscountCodeSelect
					'select_names' => $discount_codes['discount_name'],
					'select_values' => $discount_codes['pos_discount_id'],
					'properties' => array(	'style.width' => '"5em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){updateDiscount(this);}',
											//'onkeyup' => 'function(){updateDiscount(this);}',
											//'onmouseup' => 'function(){updateDiscount(this);}'
											)
											),
				array('caption' => 'Item<BR>Discount<BR>ex:10% or $12.90',
					'db_field' => 'discount',
					'type' => 'input',
					'valid_input' => '$%0123456789.',
					//'round' => 2,

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'readOnly' => 'true',
											//'className' => '"nothing"',
											'className' => '"readonly"',

											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){calculateTotals(this);}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
				
				
				
				
				
				
				array(
					'db_field' => 'discount_type',
					//'price_array_index' => 'quantity',
					'type' => 'hidden'
					),
				array('caption' => 'Item<br>Applied<br>Instore<br>Discount',
					'db_field' => 'applied_instore_discount',
					'type' => 'hidden',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 2,
					//'POST' => 'no',
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){calculateTotals();}',
											'readOnly' => 'true')),	
				array('caption' => 'Line Total',
					'db_field' => 'extension',
					'type' => 'hidden',
					'POST' => 'no',
					//'footer' => createRetailSalesInvoiceContentsFooterTableDef($pos_sales_invoice_id),
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					
					'properties' => array(	'size' => '"10"',
											'className' => '"readonly"',
											'onclick' => 'function(){calculateTotals(this);}',
											'readOnly' => 'true')),		
				array('caption' => 'Tax Category',
					'db_field' => 'pos_sales_tax_category_id',
					'type' => 'select',

							'select_names' => $tax_category_names_ids['tax_category_name'],
						'select_values' => $tax_category_names_ids['pos_sales_tax_category_id'],
					'properties' => array(	'style.width' => '"7em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){updateTax(this);}',
											'onkeyup' => 'function(){updateTax(this);}',
											'onmouseup' => 'function(){updateTax(this);}')),
				array('caption' => 'Taxable<br>Total',
					'db_field' => 'taxable_total',
					'POST' => 'no',
					'type' => 'hidden',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 2,
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => 'true',
											'onblur' => 'function(){calculateTotals(this);}')),
				array('caption' => 'Tax Rate',
					'db_field' => 'tax_rate',
					//'POST' => 'no',
					'type' => 'hidden',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 3,
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => 'true',
											'onblur' => 'function(){calculateTotals(this);}')),	
				
				array('caption' => 'Tax Total',
					'db_field' => 'tax_total',
					//'POST' => 'no',
					'type' => 'hidden',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 2,
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => 'true',
											'onblur' => 'function(){calculateTotals(this);}')),	
				
					
				
				array('caption' => 'Comments',
					'db_field' => 'comments',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	
											'className' => '"comments"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}'))
				
				
			);			
					
	
	return $columns;
	
	
	
}
function getAvailablePromotions()
{
	$sql = "SELECT pos_promotion_id, promotion_name FROM pos_promotions WHERE active = 1";
	return getfieldRowSQL($sql);
}

function getCustomerPaymentMethods()
{
	$sql = "SELECT pos_customer_payment_method_id, payment_type FROM pos_customer_payment_methods";
	return getFieldRowSQL($sql);
}


function getCardNumber_v2()
{
	//basically insert the card number into the card table
	//if it bounces with an error because it exists, re-insert
	//etc
	// get the id, returned, then get the card number and return that...
			$date_added = getDateTime();
	$dbc = startTransaction();
	$card_length = 16;
	//the table should be locked here.
	//create a unique ID (card_number)
	//try to select it
	//if it does not exist insert it.
	//if the insert result in an error, catch it and repeat...
	//keep track of the id.
	$success = false;
	DO
	{
		$success = true;
		$unique_number_attempt = generatUniqueCardNumber($card_length);
		$insert['card_number'] = $unique_number_attempt;
		$insert['date_created'] = $date_added;
		try
		{
			//$result = runSQL($insert_sql);
			$id = simpleTransactionInsertSQLReturnID($dbc, 'pos_store_credit_card_numbers', $insert);
		}
		catch(Exception $e)
		{
			$success = false;
		}
	}
	WHILE ($success == false);
	//can keep track of $id here to print later??
	simpleCommitTransaction($dbc);
	//$id_array[] = $id;
	//$card_array[] = $unique_number_attempt;
		
	return $unique_number_attempt;
	
}
function getCardNumber()
{
	$date = getdatetime();
	$dbc = startTransaction();
	$sql = "SELECT pos_store_credit_card_number_id, card_number FROM pos_store_credit_card_numbers WHERE date_printed IS NULL ORDER BY pos_store_credit_card_number_id ASC LIMIT 1";
	$card_number = getTransactionSQL($dbc, $sql);
	if(sizeof($card_number)>0)
	{
		$pos_store_credit_card_number_id=$card_number[0]['pos_store_credit_card_number_id'];
		$card_number = $card_number[0]['card_number'];
		$sql = "UPDATE pos_store_credit_card_numbers SET date_printed = '$date' WHERE pos_store_credit_card_number_id = $pos_store_credit_card_number_id";
		runTransactionSQL($dbc, $sql);
		simpleCommitTransaction($dbc);
		return $card_number;
	}
	else
	{
		//we ran out of id's
		//need to create more then select one...
		closeDB($dbc);
		return false;
	}
	
	
		if($card_number == false)
	{
		$html .= '<p>Generating Card Numbers......</p>';
		//create a batch of card numbers
		createBatchOfCardNumbers(1);
		$card_number = getCardNumber();
	}
	
	
}
function createBatchOfCardNumbers($number_to_create)
{
		//$number_to_create = scrubInput($_POST['qty']);
		$date_added = getDateTime();
		$id_array = array();
		$card_array = array();
		for($qty = 0; $qty<$number_to_create;$qty++)
		{
			$dbc = startTransaction();
			$card_length = 16;
			//the table should be locked here.
			//create a unique ID (card_number)
			//try to select it
			//if it does not exist insert it.
			//if the insert result in an error, catch it and repeat...
			//keep track of the id.
			$success = false;
			DO
			{
				$success = true;
				$unique_number_attempt = generatUniqueCardNumber($card_length);
				$insert['card_number'] = $unique_number_attempt;
				$insert['date_created'] = $date_added;
				try
				{
					//$result = runSQL($insert_sql);
					$id = simpleTransactionInsertSQLReturnID($dbc, 'pos_store_credit_card_numbers', $insert);
				}
				catch(Exception $e)
				{
					$success = false;
				}
			}
			WHILE ($success == false);
			//can keep track of $id here to print later??
			simpleCommitTransaction($dbc);
			$id_array[] = $id;
			$card_array[] = $unique_number_attempt;
		}
		return $card_array;
}

function getStoreCreditsLinkedToSalesInvoice($pos_sales_invoice_id)
{
	$sql = "SELECT pos_store_credit.card_number, concat(pos_customers.first_name, ' ', pos_customers.last_name) as customer_name,  pos_store_credit.original_amount,
	pos_store_credit.original_amount - (select sum(payment_amount) FROM pos_customer_payments b WHERE b.pos_store_credit_id = pos_store_credit.pos_store_credit_id) as amount_remaining
	
	FROM pos_sales_invoice_to_payment
	LEFT JOIN pos_customer_payments ON pos_customer_payments.pos_customer_payment_id = pos_sales_invoice_to_payment.pos_customer_payment_id
	LEFT JOIN pos_store_credit ON pos_customer_payments.pos_store_credit_id = pos_store_credit.pos_store_credit_id
	LEFT JOIN pos_customers ON pos_store_credit.pos_customer_id = pos_customers.pos_customer_id
	WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = $pos_sales_invoice_id ";
	$credit_data = getSQL($sql);
	return $credit_data;
}
function getCustomerPaymentsLinkedSalesInvoice($pos_sales_invoice_id)
{
	$payment_sql = "SELECT pos_sales_invoice_to_payment.pos_customer_payment_id, payment_group, transaction_id, card_number, pos_customer_payments.payment_amount, 
							pos_customer_payment_methods.payment_type
					FROM pos_sales_invoice_to_payment
					LEFT JOIN pos_customer_payments USING(pos_customer_payment_id)
					LEFT JOIN pos_customer_payment_methods USING (pos_customer_payment_method_id)
					WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSQL($payment_sql);
}
function getDiscountsLinkedSalesInvoice($pos_sales_invoice_id)
{
}


function createIphoneInvoiceHtmlTable($pos_sales_invoice_id)
{
	$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
	
	
	$html = '<TABLE id = "retail_sales_invoice_main" name = "retail_sales_invoice_main" class ="retail_sales_invoice_main">';
	$html .= '<TR >';								
	
	//$html .= '<th>SALES ASSOCIATE</th><td>' .getUserFullName($invoice_data[0]['pos_user_id']) . '</td>';
	//$html .= '<th>INVOICE DATE</th>' . '<td>'. dateSelect('invoice_date', getdatefromdatetime($invoice_data[0]['invoice_date']), ' style = "width:100%" ') .'</td>'.newline();//createTDFromTD_def($date_array);
	$html .= '<th width="130" style=text-align:left;">INVOICE DATE</th>' . '<td width="130" align="left">'. getdatefromdatetime($invoice_data[0]['invoice_date']).'</td>'.newline();
	
	$html .= '<th style="text-align:right;">INVOICE NUMBER</th><td width="70" align="right"><font color="#F00"> ' .str_pad(getSalesInvoiceNumber($pos_sales_invoice_id), 6, "0", STR_PAD_LEFT).'</font></td>';
	
	$html .= '</tr>';
	$html .= '</table></p>';

	//$html .= '<script>var invoice_table_def = ' . prepareTableDefArrayForJavascriptVerification(array($table_def)) . ';</script>';
	$html .= '<script>var invoice_main_table_id = "invoice_main";</script>';
	return $html;
}

function createIphoneCustomerHtmlTable($pos_sales_invoice_id)
{
	$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
	$pos_customer_id = getCustomerFromSalesInvoice($pos_sales_invoice_id);
	$invoice_url = POS_ENGINE_URL . '/sales/retailInvoice/iphone_invoice.php?pos_sales_invoice_id='.$pos_sales_invoice_id;

	$customer = array(
						array( 'db_field' => 'first_name',
								'type' => 'input',
								'tags' => '  size="15"',
								'caption' => 'First Name',
								'value' => getCustomerFirstName($pos_customer_id),
								'validate' => 'none'
								),
						array( 'db_field' => 'last_name',
								'type' => 'input',
								'tags' => ' size="20" ',
								'caption' => 'Last Name',
								'value' => getCustomerLastName($pos_customer_id),
								'validate' => 'none')
						);
	$html = '<TABLE id = "customer_invoice_main" name = "customer_invoice_main" class ="customer_invoice_main">';
	$html .= '<TR >';								
	$html .=  '<td><input class = "button" type="button" style="width:120px" name="add_customer" value="Select Customer" onclick="lookupCustomer(\''.$invoice_url.'\')"/></td>';
	$html .= createHiddenInput('pos_customer_id', $pos_customer_id);
	if($pos_customer_id != 0)
	{
		//$html .= '<th>Customer ID</th>' .createTDFromTD_def($customer);
		for($i=0;$i<sizeof($customer);$i++)
		{
			$html .= createTHFromTD_def($customer[$i]);
			$html .= createTDFromTD_def($customer[$i]);
		}

	}
	else
	{
		
	}
	$html .= '</tr>';
	$html .= '</table></p>';
	
	return $html;
}

function getCustomerPayments($pos_sales_invoice_id)
{
	$sql = "SELECT *,
			

			
			
			 CASE payment_group

			WHEN ('STORE_CREDIT') THEN IF(pos_customer_payments.pos_store_credit_id =0, '--', (select card_number FROM pos_store_credit WHERE pos_store_credit.pos_store_credit_id = pos_customer_payments.pos_store_credit_id)) 

			WHEN ('CREDIT_CARD') THEN payment_type 
		
			ELSE
			'--'
			END 

as summary
	
			FROM pos_customer_payments
			LEFT JOIN pos_customer_payment_methods USING (pos_customer_payment_method_id)
			LEFT JOIN pos_sales_invoice_to_payment USING (pos_customer_payment_id)
			WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSQL($sql);
}

function getStoreCreditCardValue( $pos_store_credit_id,$dbc = 'null')
{
	
	
	$add_sql = "

SELECT sum( extension) FROM 
	pos_sales_invoice
	LEFT JOIN pos_sales_invoice_contents ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	WHERE pos_sales_invoice_contents.pos_store_credit_id = '$pos_store_credit_id' 
	AND pos_sales_invoice.payment_status = 'PAID' AND pos_sales_invoice.invoice_status='CLOSED' AND pos_sales_invoice_contents.content_type ='CREDIT_CARD'

";
	//which invoices were paid using a gift card or store credit && which invoices were assigned a refund to store credit
	
	$used_sql ="SELECT sum( payment_amount ) FROM pos_customer_payments 
				LEFT JOIN pos_sales_invoice_to_payment USING (pos_customer_payment_id) WHERE pos_store_credit_id = $pos_store_credit_id";
	if($dbc='null')
	{	
	$add = getSingleValueSQL($add_sql);
	$used = getSingleValueSQL($used_sql);
	}
	else
	{
		$add = getSingleValueTransactionSQL($dbc,$add_sql);
		$used = getSingleValueTransactionSQL($dbc,$used_sql);
	}	
	return $add-$used;
				
}


?>