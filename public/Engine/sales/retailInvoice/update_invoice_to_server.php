<?php
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales Form Handler';
require_once('../sales_functions.php');
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
$invoice_tbody_def = $_POST['invoice_tbody_def'];
$invoice_table_data_object = (isset($_POST['invoice_table_data_object'])) ? $_POST['invoice_table_data_object'] : array();
$promtion_tbody_def = $_POST['promotion_tbody_def'];
$promotion_table_data_object = (isset($_POST['promotion_table_data_object'])) ? $_POST['promotion_table_data_object'] : array();
//$table_data_object = json_decode(stripslashes($_POST['table_data_object']) , true);


$dbc = startTransaction();

//**************** INVOICE  ***********************************
$pos_customer_id = $_POST['pos_customer_id'];
$pos_address_id = $_POST['pos_address_id'];
$invoice_update['pos_customer_id'] = $pos_customer_id;
$invoice_update['pos_address_id'] = $pos_address_id;
$invoice_update['invoice_status'] = 'DRAFT';
//$invoice_update['shipping_amount'] = scrubInput($_POST['shipping_amount']);
$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
$results[] = simpleTransactionUpdateSQL($dbc,'pos_sales_invoice', $key_val_id, $invoice_update);
$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");

//********************** CUSOMTER ******************************
if($pos_customer_id != 0)
{
	$customer['phone'] = scrubInput($_POST['phone']);
	$customer['email1'] = scrubInput($_POST['email1']);
	$customer_key_val_id['pos_customer_id'] = $pos_customer_id;
	$results[] = simpleTransactionUpdateSQL($dbc,'pos_customers', $customer_key_val_id, $customer);

}

//**************** INVOICE CONTENTS ***********************************
//next delete all contents for the invoice
$sic_delet_q = "DELETE FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = '$pos_sales_invoice_id'";
$results[] = runTransactionSQL($dbc, $sic_delet_q);

//write contents for the invoice
// if the tax is mixed then flatten the quantity down...
if(sizeof($invoice_table_data_object)>0)
{
	//make a (db_filed1,db_field2,etc) for the insert fields 
	// and (val1,val2,etc),(val1,val2,etc..) for the values....
	
	
	//this gift card code should be ran only one time.
	//we should process the 'gift card' at point of payment, only when the invoice is closed.
	/*for($row=0;$row<sizeof($invoice_table_data_object['row_number']);$row++)
	{
		//first lets handle the gift cards.....
		if($invoice_table_data_object['card_number'][row] != '')
		{
			//we have a gift card
			//might be an error with a duplicate card id here
			//if the user scans everything in
			//we need to insert this card number into pos_store_credits
			//get the credit_id back and put that into the invoice...
			$store_credit_insert['original_amount'] = $invoice_table_data_object['retail_price'][row];
			$store_credit_insert['card_number'] = $invoice_table_data_object['barcode'][row];
			$store_credit_insert['pos_customer_id'] = $pos_customer_id;
			$store_credit_insert['date_created'] = $invoice_date;
			$store_credit_insert['date_issued'] = $invoice_date;
			$store_credit_insert['pos_user_id'] = $_SESSION['pos_user_id'];
			$pos_store_credit_id = simpleTransactionInsertSQLReturnID($dbc,'pos_store_credit',$store_credit_insert);
		}
	}*/
	
	
	
	//This was the original way, which is really cool, but now that I am doing both gift cards and returns I need more detailed control
	
	$sql = array(); 
	$db_fields[]  = 'pos_sales_invoice_id';
	for($i=0;$i<sizeof($invoice_tbody_def);$i++)
	{
		if(isset($invoice_tbody_def[$i]['POST']) && $invoice_tbody_def[$i]['POST'] =='no')
		{
			
		}
		else
		{
			$db_fields[] = scrubInput($invoice_tbody_def[$i]['db_field']);
		}
	}

	$str_fields = implode(',', $db_fields);
	for($row=0;$row<sizeof($invoice_table_data_object['row_number']);$row++) 
	{	
		//strip the $ or percent off discount
		$invoice_table_data_object['discount'][$row] = str_replace('$' ,'', $invoice_table_data_object['discount'][$row]);
		$invoice_table_data_object['discount'][$row] = str_replace('%' ,'', $invoice_table_data_object['discount'][$row]);
		
		/*//this flattens the quantity if there are mixed values....
		if($invoice_table_data_object['tax_rate'][$row]['display_value'] == 'MIX')
		{
			$quantity = $invoice_table_data_object['quantity'][$row];
			$invoice_table_data_object['quantity'][$row] = 1;
			for($qty=0;$qty<$quantity;$qty++)
			{
				
				$field_counter= 0;
				$row_array = array();
				$row_array[$field_counter] = $pos_sales_invoice_id;
				$field_counter++;
				for($col=0;$col<sizeof($invoice_tbody_def);$col++)
				{
					if(!(isset($invoice_tbody_def[$col]['POST']) && $invoice_tbody_def[$col]['POST'] =='no'))
					{
						if(is_array($invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row]))
						{
							$row_array[$field_counter] = "'" . $invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row]['array_values'][$qty] . "'";
							$field_counter++;
						}
						else
						{
							if($invoice_tbody_def[$col]['type'] == 'checkbox')
							{
								if ($invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row] == 'true')
								{
									$invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row] = 1;
								}
							}
						
							$row_array[$field_counter] = "'" . $invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row] . "'";
							$field_counter++;
							
						}
					}
				}
				$row_string =  implode(',',$row_array);
				$sql[] = '(' . $row_string .')';
			}
			
		}
		else*/
		
		$field_counter= 0;
		$row_array = array();
		$row_array[$field_counter] = $pos_sales_invoice_id;
		$field_counter++;
		for($col=0;$col<sizeof($invoice_tbody_def);$col++)
		{
			if(!(isset($invoice_tbody_def[$col]['POST']) && $invoice_tbody_def[$col]['POST'] =='no'))
			{
				if(is_array($invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row]))
				{
					$row_array[$field_counter] = "'" . $invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row]['display_value'] . "'";
					$field_counter++;
				}
				else
				{
					if($invoice_tbody_def[$col]['type'] == 'checkbox')
						{
							if ($invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row] == 'true')
							{
								$invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row] = 1;
							}
						}
					$row_array[$field_counter] = "'" . scrubInput($invoice_table_data_object[$invoice_tbody_def[$col]['db_field']][$row]) . "'";
					$field_counter++;
				}
			}
		}
		$row_string =  implode(',',$row_array);
		$sql[] = '(' . $row_string .')';
		
				
	}
	$sic_insert_sql = "INSERT INTO pos_sales_invoice_contents (" . $str_fields . ") VALUES  " . implode(',', $sql);
	$results[] = runTransactionSQL($dbc, $sic_insert_sql);
}	
//**************** PROMOTIONS ***********************************
//just need to store the promtion id and the sales invoice id
//first delete
$prom_delet_q = "DELETE FROM pos_sales_invoice_promotions WHERE pos_sales_invoice_id = '$pos_sales_invoice_id'";
$results[] = runTransactionSQL($dbc, $prom_delet_q);
if(sizeof($promotion_table_data_object)>0)
{
	for($row=0;$row<sizeof($promotion_table_data_object['row_number']);$row++)
	{
		$pos_promotion_id = $promotion_table_data_object['pos_promotion_id'][$row];
		$applied_amount = $promotion_table_data_object['applied_amount'][$row];
		$prom_insert_sql = "INSERT INTO pos_sales_invoice_promotions (pos_sales_invoice_id, pos_promotion_id, applied_amount) VALUES ($pos_sales_invoice_id,$pos_promotion_id, '$applied_amount')"; 
		$results[] = runTransactionSQL($dbc, $prom_insert_sql);

	}
}

simpleCommitTransaction($dbc);
echo 'STORED' .newline();

?>