<?php

/*

 this should be a big form handler....
 we have to : add value to gift cards...
 close invoices

*/
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales Invoice Payments';
require_once('../sales_functions.php');
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
$db_table = 'pos_sales_invoice';
$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
$payments_javascript_version = 'payments_javascript_version.2013.02.14.js';
if(checkForValidIDinPOS($pos_sales_invoice_id, $db_table, 'pos_sales_invoice_id'))
{
	$date = getSalesInvoiceDate($pos_sales_invoice_id);


	//need a terminal check here...
	$pos_terminal_id = terminalCheck();
	//$pos_customer_id = getCustomerFromSalesInvoice($pos_sales_invoice_id);
	//the deposit_account_id is where the funds are dopsited to....
	//for example the cash goes into a register account
	//the visa/amex goes to payment processing a/r
	//amex goes to amex a/r
	
	//the account_id is if the payment is on a customer account...
	$total_payment = 0;
	if(isset($_POST['payments_table_data_object']))
	{
		$counter=0;
		$dbc = startTransaction();
		//update the terminal here....
		runTransactionSQL($dbc,"UPDATE pos_sales_invoice SET pos_terminal_id = $pos_terminal_id WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
		$sql[$counter] = "SELECT pos_customer_payment_id FROM pos_sales_invoice_to_payment WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
		$original_paymnets = getTransactionSQL($dbc,$sql[$counter]);
		$counter++;
		for($row=0;$row<sizeof($original_paymnets);$row++)
		{
			$pos_customer_payment_id = $original_paymnets[$row]['pos_customer_payment_id'];
			$sql[$counter] = "DELETE FROM pos_customer_payments WHERE pos_customer_payment_id = $pos_customer_payment_id";
			runTransactionSQL($dbc,$sql[$counter]);
			$counter++;
		}
		$sql[$counter] = "DELETE FROM pos_sales_invoice_to_payment WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
		runTransactionSQL($dbc,$sql[$counter]);
		$counter++;
		
		$table_data_object = json_decode(stripslashes($_POST['payments_table_data_object']) , true);
		for($row=0;$row<sizeof($table_data_object['row_number']);$row++)
		{
			//each row is a payment....
			$comments = scrubInput($table_data_object['comments'][$row]);
			$payment_amount = scrubInput($table_data_object['payment_amount'][$row]);
			$total_payment = $total_payment + $payment_amount;
			$pos_customer_payment_method_id = $table_data_object['pos_customer_payment_method_id'][$row];
			$sql[$counter] = "INSERT INTO pos_customer_payments ( pos_customer_payment_method_id, payment_amount, date, comments) VALUES ( $pos_customer_payment_method_id, '$payment_amount', '$date', '$comments')";
			runTransactionSQL($dbc, $sql[$counter]);
			$counter++;
			$pos_customer_payment_id = mysqli_insert_id($dbc);
			$sql[$counter] = "INSERT INTO pos_sales_invoice_to_payment (pos_sales_invoice_id, pos_customer_payment_id, applied_amount) VALUES ('$pos_sales_invoice_id', $pos_customer_payment_id, '$payment_amount')";
			runTransactionSQL($dbc, $sql[$counter]);
			$counter++;
		}
		
		//if the invoice went onto a customer account then we simply open the invoice
		//we are currently not going onto an account, but that would simply be:
		//1) check the payment method for (on account)
		//2) recored the customer account ID to the payment
		//3) basically check for 'on account'
		$grand_total_from_contents = getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id);
		
		//pprint( $grand_total_from_contents);
		//pprint(getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id));
		//pprint( $total_payment);
		if(abs($grand_total_from_contents - $total_payment)<0.0001)
		{
			
			//fully paid, close the invoice.
			runTransactionSQL($dbc, "UPDATE pos_sales_invoice SET payment_status = 'PAID', invoice_status = 'CLOSED' WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
		}
		
		
		//add cc values here...once I fix up the payments page...
		
		
		
		simpleCommitTransaction($dbc);
	}
	
	$complete_location =  'retail_sales_invoice.php?type=view&pos_sales_invoice_id='.$pos_sales_invoice_id;
	//$complete_location =  POS_ENGINE_URL . '/sales/retailInvoice/list_retail_sales_invoices.php?message=' . urlencode($pos_sales_invoice_id . " has been entered");
	//$complete_location =  POS_ENGINE_URL . '/sales/retailInvoice/list_retail_sales_invoices.php?pos_sales_invoice_id=' . $pos_sales_invoice_id . '&search=Search';
	header('Location: '.$complete_location );

}


		

?>