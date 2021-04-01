<?php
/* some basic rules... exact change for payments... however it should work like the invoice/payments code... naaa....

basically re-show the view page with an added table....

just show the total due and the dynamic table for payment options....
*/
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales Invoice Payments';
require_once('../sales_functions.php');
require_once('retail_sales_invoice_functions.php');
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
$db_table = 'pos_sales_invoice';
$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
$payments_javascript_version = 'payments_javascript_version.2013.03.12.js';
if(checkForValidIDinPOS($pos_sales_invoice_id, $db_table, 'pos_sales_invoice_id'))
{
		//first thing we need to do is spit out an invoice with STORE COPY, NOT A VALID INVOICE
		$html ='';
		//however if we are an admin we might not want to spit this out....
		 if (checkIfUserIsAdmin($_SESSION['pos_user_id']))
		{
			$html .= '<p class="error">ADMIN ACCOUNT NO STORE INVOICE PRINTED</p>';
		}
		else
		{
			printStoreCopyMemoSalesInvoice($pos_sales_invoice_id);
		}
		
		
		
		$page_title = 'Sales Invoice ' . $pos_sales_invoice_id . ' Payments';
		$form_id = "sales_invoice_form";
		$form_action = 'retail_sales_invoice_payments.fh.php';
		
		$html .=  '<script src="'.$payments_javascript_version.'"></script>'.newline();

		$html .=  '<form id = "' . $form_id . '" action="'.$form_action.'" method="post" onsubmit="return preparePost()">';
	
	$html .= '<div class = "invoice">';
		$amount_due = getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id);
		$html .= '<h2>Sales Invoice ' .$pos_sales_invoice_id . ' Amount Due: ' . $amount_due . '</h2>';
		$html.= '<script>var amount_due = ' . $amount_due . ';</script>';
		//finish this later....
		/*
		//Giftcards/storeCredit table
		$html .= '<h3>Gift Cards/Store Credit Redemption</h3>';
		$html .= '<div class="payments_table" style="display:inline-block;">';
		$credit_table_name = 'store_credit_table';
		$credits_table_def = createStoreCreditPaymentsTableDef($credit_table_name);
		//need the card #, the customer_name, the original_amount, the amount_remaining
		//adding a row then a card number will ajax that shizzz...
		$credit_data = getStoreCreditsLinkedToSalesInvoice($pos_sales_invoice_id);
		$html .= createDynamicTableReuse($credit_table_name, $credits_table_def, $credit_data, $form_id);
		$html .= '</div>';
		*/
		//payments table
		$html .= '<h3>Payments</h3>';
		$html .= '<div class="payments_table" style="display:inline-block;">';
		$payments_table_name = 'payments_table';
		$payments_table_def = createRetailSalesPaymentsTableDef($payments_table_name);
		//$payment_data = getCustomerPaymentsLinkedSalesInvoice($pos_sales_invoice_id);
		$payment_data = getCustomerPayments($pos_sales_invoice_id);
		$html .= createDynamicTableReuse($payments_table_name, $payments_table_def, $payment_data, $form_id);
		$html .= '<div style="clear:right;"></div>';
		$html .= '<div style="float:right;">';
		$go_url = POS_ENGINE_URL . '/sales/retailInvoice/view_retail_sales_invoice.php?pos_sales_invoice_id='.$pos_sales_invoice_id;
		
		$html.= createHiddenInput('pos_sales_invoice_id', $pos_sales_invoice_id);
		$html .= '<p><input class ="button" type="submit" id = "submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
		$html .= '<input class = "button" type="button" name="cancel" value="Cancel" onclick="cancelForm()"/>';
	$html .= '</form>';
	
		 $html .='</div>';	
		 $html .= '<div style="clear:right;"></div>';
		
		
		$html .= '</form>';
	$html .= '</div>';
	$html .='<script>if (payments_table_object.rowCount == 0){ payments_table_object.addRow();
	document.getElementsByName(\'payment_amount[]\')[0].value = ' . $amount_due.';}</script>';
	
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
	   
	
}
function createStoreCreditPaymentsTableDef($table_name)
{
	/* this table should have a card ID followed by the name the amount issued, the amount used, the amount remaining, and the amount to use 
	*/
	$table_object_name = $table_name . '_object';

	$payments = getCustomerPaymentMethods();

	$columns = array(
		
				array(
					'db_field' => 'pos_customer_payment_id',
					'type' => 'hidden',
					'POST' => 'no'
					),
				

				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'checkbox',
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
				array('caption' => 'Card Number<br>(barcode zap it here)',
						'db_field' => 'card_number',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
											
											'onkeydown' => 'function(){loadStoreCreditInfo(this);}',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Issued To',
						'db_field' => 'customer_name',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
											'readOnly' => '"true"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Original Amount',
						'db_field' => 'original_amount',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
											'readonly' => '"true"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Amount Remaining',
						'db_field' => 'amount_remaining',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
											'readonly' => '"true"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Amount to Use',
						'db_field' => 'payment_amount',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
			
				
				
			);			
					
	
	return $columns;
	
	
	
}


?>