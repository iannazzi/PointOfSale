<?
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
require_once('../sales_functions.php');
$card_number = scrubInput(getPostOrGetValue('card_number'));
//$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');

//get the product id, colors, sizes


		$sql = "SELECT pos_store_credit.card_number, concat(pos_customers.first_name, ' ', pos_customers.last_name) as customer_name,  pos_store_credit.original_amount,
	pos_store_credit.original_amount - (select sum(payment_amount) FROM pos_customer_payments b WHERE b.pos_store_credit_id = pos_store_credit.pos_store_credit_id) as amount_remaining
	

	FROM pos_store_credit 
	LEFT JOIN pos_customers ON pos_store_credit.pos_customer_id = pos_customers.pos_customer_id
	WHERE pos_store_credit.card_number = '$card_number' ";
		
		
$data = getSQL($sql);

if(sizeof($data)==1)
{
	//$return_data['card_number'] = $data[0]['card_number']
	
	echo json_encode($data[0]) . "\n";
}
elseif(sizeof($data)>1)
{
	echo "More than one card found with the same number";
}
else
{
	echo "No Data Found";
}


?>