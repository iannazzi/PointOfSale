<?
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
require_once('../sales_functions.php');
$promotion_code = scrubInput(getPostOrGetValue('promotion_code'));
$invoice_date= scrubInput(getPostOrGetValue('invoice_date'));
//$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');

//if the date is past the expiration date then return the expiration value.
//if the value is zero return 


		$sql = "SELECT promotion_code, promotion_name, pos_promotion_id, promotion_type, if('$invoice_date' < DATE(expiration_date), promotion_amount, expired_value) as promotion_amount, date(expiration_date) as expiration_date, percent_or_dollars, qualifying_amount

	FROM pos_promotions
	WHERE promotion_code = '$promotion_code' AND active = 1";
	
	$sql = "SELECT promotion_code, promotion_name, pos_promotion_id, promotion_type, promotion_amount, expired_value, date(expiration_date) as expiration_date, percent_or_dollars, qualifying_amount

	FROM pos_promotions
	WHERE promotion_code = '$promotion_code' AND active = 1";	
		
$data = getSQL($sql);

if(sizeof($data)==1)
{	
	echo json_encode($data[0]) . "\n";
	//echo $invoice_date;
	//echo $sql;
}
elseif(sizeof($data)>1)
{
	echo "Error";
}
else
{
	//provide a reason why 
	//expired, no value
	//before start date
	echo json_encode(array('error' => "Not Valid"));
}


?>