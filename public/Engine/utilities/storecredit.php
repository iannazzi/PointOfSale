<?


//require_once ('../Config/config.inc.php');
require_once ('../../Config/config.inc.php');
require_once (CHECK_LOGIN_FILE);
require_once(PHP_LIBRARY);
//$dbc = startTransaction();
include(HEADER_FILE);

$invoice_contents = getSQL("


select pos_sales_invoice_id, pos_customer_id, pos_sales_invoice_content_id, barcode, content_type, pos_store_credit_id, retail_price as price from pos_sales_invoice_contents 
LEFT join pos_sales_invoice USING (pos_sales_invoice_id)
WHERE  content_type = 'CREDIT_CARD' AND pos_store_credit_id = 0 AND barcode != ''
ORDER BY pos_sales_invoice_id DESC
LIMIT 0,600

");

echo '<p>Size of $invoice_contents = ' . sizeof($invoice_contents) . '</p?';
for($row=0;$row<sizeof($invoice_contents);$row++){
	$invoice_data = getSalesInvoiceData($invoice_contents[$row]['pos_sales_invoice_id']);
	$barcode = stripWhiteSpace($invoice_contents[$row]['barcode']);
	$existing_card = getSQL("SELECT pos_store_credit_card_number_id FROM pos_store_credit_card_numbers where card_number='$barcode'");
	if (sizeof($existing_card)==0){
		//problem
		echo '<p>skipping not a card in the system' . $invoice_contents[$row]['barcode'] .'</p>';

		//trigger_error("Card was not created using this system.");
	}
	$existing_card_with_value = getSQL("SELECT pos_store_credit_id FROM pos_store_credit where card_number='$barcode'");
	if (sizeof($existing_card_with_value)>0){
		//problem
		echo '<p>skipping existing card with value' . $invoice_contents[$row]['barcode'] .'</p>';
		//trigger_error("trying to insert a gift card already assigned....");
	}
	else{
		$store_credit_insert['original_amount'] = $invoice_contents[$row]['price'];
		$store_credit_insert['card_type'] = 'Gift Card';
		$store_credit_insert['card_number'] = $invoice_contents[$row]['barcode'];
		$store_credit_insert['pos_customer_id'] = $invoice_data[0]['pos_customer_id'];
		$store_credit_insert['date_created'] = $invoice_data[0]['invoice_date'];
		$store_credit_insert['date_issued'] = $invoice_data[0]['invoice_date'];
		$store_credit_insert['pos_user_id'] = $_SESSION['pos_user_id'];
		
		
		$dbc = startTransaction();
		$pos_store_credit_id = simpleTransactionInsertSQLReturnID($dbc,'pos_store_credit',$store_credit_insert);
		//now put this store credit id into the invoice contents...
		$pos_sales_invoice_content_id = $invoice_contents[$row]['pos_sales_invoice_content_id'];
		$content_insert = array();
		$content_insert['pos_store_credit_id'] = $pos_store_credit_id;
		$key_val_id['pos_sales_invoice_content_id'] = $pos_sales_invoice_content_id;
		simpleTransactionUpdateSQL($dbc,'pos_sales_invoice_contents', $key_val_id, $content_insert);
		simpleCommitTransaction($dbc);
	
	
	}
}		
			
			
			

include(FOOTER_FILE);


?>