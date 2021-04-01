<?
$access_type = 'WRITE';
require_once('retail_sales_invoice_functions.php');

$ajax_request = (ISSET($_GET['ajax_request'])) ? $_GET['ajax_request'] : $_POST['ajax_request'];

// we need this to lock down ajax stuff as anyone can create anything from javascript..... checkForClosedInvoice($pos_sales_invoice_id)?

//use this url to test:
//http://www.craigiannazzi.com/POS_TEST/Engine/products/AjaxProduct/barcode.php

if(strtoupper($ajax_request) == 'BARCODE')
{
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');

	$barcode = scrubInput(getPostOrGetValue('barcode'));
	$barcode =stripWhiteSpace($barcode);

	//first make a determination of what it is
	//****************************** STORE CREDIT CARD *********************************
	if (sizeof(getSQL("SELECT pos_store_credit_card_number_id FROM pos_store_credit_card_numbers WHERE card_number = '$barcode'")) > 0)
	{
	
		// gift card purchase
	
		//here we should check to make sure the card had not been previously assigned...
		if (sizeof(getSQL("SELECT card_number FROM pos_store_credit WHERE card_number = '$barcode'")) > 0)
		{
			//error the card has been used
			$error['error'] = 'This Card Number has been previously Assigned a value';
			echo  json_encode($error) . "\n";
			exit();
		}
		$barcode_data['card_number'] = $barcode;
		$barcode_data['content_type'] = 'CREDIT_CARD';
		$barcode_data['quantity'] = 1;
		$barcode_data['style_number'] = '-';
		$barcode_data['barcode'] = $barcode;
		$barcode_data['size'] = '-';
		$barcode_data['sale_price'] = '-';
		$barcode_data['color_code'] = '-';
		$barcode_data['color_name'] = '-';
		$barcode_data['brand_name'] = '-';
		$barcode_data['title'] = 'Gift Card';
		$barcode_data['checkout_description'] = 'Gift Card';
		//$barcode_data['pos_sales_tax_category_id'] = $pos_sales_tax_category_id; 
		echo json_encode($barcode_data) . "\n";
	
	}
	else if($barcode == 'gift card')
	{
		// gift card purchase
		$barcode_data['card_number'] = $barcode;
		$barcode_data['content_type'] = 'CREDIT_CARD';
		$barcode_data['quantity'] = 1;
		$barcode_data['style_number'] = '-';
		$barcode_data['barcode'] = '';
		$barcode_data['size'] = '-';
		$barcode_data['sale_price'] = '-';
		$barcode_data['color_code'] = '-';
		$barcode_data['color_name'] = '-';
		$barcode_data['brand_name'] = '-';
		$barcode_data['title'] = 'Gift Card';
		$barcode_data['checkout_description'] = 'Gift Card';
		//$barcode_data['pos_sales_tax_category_id'] = $pos_sales_tax_category_id; 
		echo json_encode($barcode_data) . "\n";
	}
	else if (ctype_digit($barcode) && sizeof(getSQL("SELECT pos_product_sub_id FROM pos_products_sub_id WHERE pos_product_sub_id = '$barcode'")) >0)
	{
		//product
		
		$data = getProductSUBIDdata($barcode);
		
		if(sizeof($data)>0)
		{
			$data[0]['clearance'] = 0;
			$data[0]['barcode'] = $barcode;
			echo json_encode(convertProductDataForReturn($data,$pos_sales_invoice_id)) . "\n";
		}
	}
	elseif(sizeof(getSQL("SELECT pos_product_sub_id FROM pos_product_sub_sale_price WHERE sale_barcode = '$barcode'")))
	{
		//barcode is a digit followd by p 1 through N
		//basically get the barcode product, then get the sale price and check if it is celarenced.
		$barcode = strtoupper($barcode);
		list($pos_product_sub_id, $price_modifier) = explode('P', $barcode);
		$sub_id_data = getProductSUBIDdata($pos_product_sub_id);
		$price_data = getSQL("SELECT price, title, clearance FROM pos_product_sub_sale_price WHERE sale_barcode = '$barcode'");

		//put the sale price and clearence data here...
		
		if(sizeof($sub_id_data)>0)
		{
			$sub_id_data[0]['barcode'] = $barcode;
			$sub_id_data[0]['sale_price'] = $price_data[0]['price'];
		$sub_id_data[0]['clearance'] = $price_data[0]['clearance'];
			echo json_encode(convertProductDataForReturn($sub_id_data,$pos_sales_invoice_id)) . "\n";
		}
		
	}
	else if (sizeof(getSQL("SELECT pos_product_sub_id FROM pos_products_sub_id WHERE product_subid_name = '$barcode'")) >0)
	{
		//definately a barcode
		//get the product id, colors, sizes

		$sql = "SELECT pos_product_sub_id, pos_products_sub_id.pos_product_id, product_subid_name, attributes_list,
			 retail_price, sale_price, title, style_number,
		 
			 concat(pos_products.style_number,',',
		
				(SELECT group_concat(concat(attribute_name,':',option_name) SEPARATOR ',') 
				FROM pos_product_sub_id_options 
				LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
				LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
				WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
				)   as product_options
				 
		 
			FROM pos_products_sub_id
			LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
			LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
			WHERE product_subid_name = '$barcode'";
		$data = getSQL($sql);

		if(sizeof($data)>0)
		{
		
	
			echo json_encode(convertProductDataForReturn($data,$pos_sales_invoice_id)) . "\n";
		}
	}
	//PROMOTION
	elseif(sizeof(getSQL("SELECT pos_promotion_id FROM pos_promotions WHERE promotion_code = '$barcode'")) >0)
	{
		
		
		//$promotion_code = scrubInput(getPostOrGetValue('promotion_code'));
		$pos_promotion_id = getPromotionIDFromCode($barcode);
		$data = getPromotionData($pos_promotion_id, 1);
		$data['content_type'] = 'PROMOTION';
		
		echo json_encode($data) . "\n";

		/*if(sizeof($data)==1)
		{	
			$data[0]['content_type'] = 'PROMOTION';
			
	
		}
		elseif(sizeof($data)>1)
		{
			$error['error'] = 'Promotion Error: Multiple Results';
			echo  json_encode($error) . "\n";
		}
		else
		{
			$error['error'] = 'Not Valid';
			echo  json_encode($error) . "\n";
		}*/
	}
	else if($barcode == 'ship')
	{
		$barcode_data['content_type'] = 'SHIPPING';
		$barcode_data['title'] = 'SHIPPING';
		$barcode_data['checkout_description'] = 'SHIPPING';
	
		//we have to get the gd tax category for shipping
		//consider shipping a service....
		//the tax category will pull from that service..
	
		//
	
	}
	elseif(sizeof(getSQL("SELECT pos_service_id FROM pos_services WHERE barcode = '$barcode'")) >0)
	{
		//service - but what kind?
		$barcode_data['content_type'] = 'SERVICE';
		$barcode_data['title'] = 'service';
		$barcode_data['checkout_description'] = 'service';
	}
	else
	{
		$error['error'] = 'No Data Found For Barcode';
		echo  json_encode($error) . "\n";
	}
}
elseif(strtoupper($ajax_request) == 'CUSTOMER_DEPOSIT')
{
	//create a unique card and send it back...
	$card_number = getCardNumber_v2();
	$barcode_data['card_number'] = $card_number;
	$barcode_data['content_type'] = 'CREDIT_CARD';
	$barcode_data['quantity'] = 1;
	$barcode_data['style_number'] = '-';
	$barcode_data['barcode'] = $card_number;
	$barcode_data['size'] = '-';
	$barcode_data['sale_price'] = '-';
	$barcode_data['color_code'] = '-';
	$barcode_data['color_name'] = '-';
	$barcode_data['brand_name'] = '-';
	$barcode_data['title'] = 'Customer Deposit';
	$barcode_data['checkout_description'] = 'Cusomter Deposit ' . $card_number;
	
	
	echo json_encode($barcode_data);
}
elseif(strtoupper($ajax_request) == 'CUSTOMER_SEARCH')
{
	//look up the customer
	
	//send back the customer and the table def
	
	
	$first_name = scrubInput($_POST['first_name']);
	$last_name = scrubInput($_POST['last_name']);
	$email1 = scrubInput($_POST['email']); 
	$phone = scrubInput($_POST['phone']);
	
	$tmp_sql = "

SELECT  
		pos_customers.pos_customer_id,
		pos_customers.default_address_id,
		pos_customers.first_name,
		pos_customers.last_name,
		pos_customers.email1,
		pos_customers.phone,
		concat(first_name, ' ', last_name) as full_name,
		CONCAT_WS(',',pos_customers.address1, pos_customers.address1, pos_customers.city, pos_customers.state, pos_customers.zip) as address,
		
		(SELECT group_concat(if(pos_sales_invoice_contents.pos_product_sub_id = 0, concat('No product id: ',checkout_description), concat(pos_manufacturer_brands.brand_name,':',pos_products.title,':',pos_products.style_number,':',
		
			(SELECT group_concat(concat(attribute_name,':',option_name) SEPARATOR ':') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			)) SEPARATOR '<BR>')
		
		FROM pos_sales_invoice 
		LEFT JOIN pos_sales_invoice_contents USING(pos_sales_invoice_id) 
		LEFT JOIN pos_products_sub_id ON pos_sales_invoice_contents.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE pos_sales_invoice.pos_customer_id = pos_customers.pos_customer_id) as product_description,
		
		pos_customers.comments


		FROM pos_customers WHERE first_name LIKE '%$first_name%' AND last_name LIKE '%$last_name%' AND email1 LIKE '%$email1%' AND phone LIKE '%$phone%'
		
		ORDER BY last_name ASC, first_name ASC
		LIMIT 30


";

	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=10000');
	$data = getTransactionSQL($dbc,$tmp_sql);
	closeDB($dbc);
	
	//now add the addresses
	for($i=0;$i<sizeof($data);$i++)
	{
		$pos_customer_id = $data[$i]['pos_customer_id'];
		$addresses = getPOSv1CustomerAddresses($pos_customer_id);
		$data[$i]['addresses'] = $addresses;
	}
	

	
	$return_array = array();
	//$return_array['sql'] = $tmp_sql;
	$return_array['data'] = $data;
	//$return_array['cdef'] = $col_def;
	echo json_encode($return_array);
	
}
elseif(strtoupper($ajax_request) == 'UPDATE_CUSTOMER')
{
	$pos_customer_id = scrubInput($_POST['pos_customer_id']);
	$pos_sales_invoice_id = scrubInput($_POST['pos_sales_invoice_id']);
	$pos_customer_id = scrubInput($_POST['pos_customer_id']);
	$update['pos_customer_id'] = $pos_customer_id;
	$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $update);
	echo 'customer update complete';
}
elseif(strtoupper($ajax_request) == 'CUSTOMER_ADD_EDIT')
{
	
	$pos_sales_invoice_id = scrubInput($_POST['pos_sales_invoice_id']);
	$pos_customer_id = scrubInput($_POST['pos_customer_id']);
	$customer['first_name'] = scrubInput($_POST['first_name']);
	$customer['last_name'] = scrubInput($_POST['last_name']);
	$customer['email1'] = scrubInput($_POST['email']); 
	$customer['phone'] = scrubInput($_POST['phone']);

	if($pos_customer_id == 0)
	{
		$pos_customer_id = simpleInsertSQLReturnID('pos_customers', $customer);
	}
	else
	{
		$customer_key_val_id['pos_customer_id'] = $pos_customer_id;
		$results[] = simpleUpdateSQL('pos_customers', $customer_key_val_id, $customer);
	}
	runSQL("UPDATE pos_sales_invoice SET pos_customer_id = $pos_customer_id WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	echo $pos_customer_id;
	
}
elseif(strtoupper($ajax_request) == 'ADDRESS_ADD_EDIT')
{
	$pos_customer_id = scrubInput($_POST['pos_customer_id']);
	$pos_address_id = scrubInput($_POST['pos_address_id']);
	$address['address1'] = scrubInput($_POST['address1']);
	$address['address2'] = scrubInput($_POST['address2']);
	$address['city'] = scrubInput($_POST['city']); 
	$address['pos_state_id'] = scrubInput($_POST['pos_state_id']);
	$address['zip'] = scrubInput($_POST['zip']);


	if($pos_address_id == 'false')
	{
		$dbc = startTransaction();
		$pos_address_id = simpleTransactionInsertSQLReturnID($dbc,'pos_addresses', $address);
		$lookup['pos_address_id'] = $pos_address_id;
		$lookup['pos_customer_id'] = $pos_customer_id;
		simpleTransactionInsertSQL($dbc,'pos_customer_addresses', $lookup);
		simpleCommitTransaction($dbc);
	}
	else
	{
		$key_val_id['pos_address_id'] = $pos_address_id;
		$results[] = simpleUpdateSQL('pos_addresses', $key_val_id, $address);
	}
	
	$return['pos_address_id'] = $pos_address_id;
	$return['addresses'] = getPOSv1CustomerAddresses($pos_customer_id);

	echo json_encode($return);
}
elseif(strtoupper($ajax_request) == 'PRODUCT_SEARCH')
{
	/* OK ojk ok we want to search a product to find a sub id
		we need to search the brand name
		the title
		the options list
	
		and link each entry to a sub id
	
		we will pass in a string that should be split via spaces
	*/
	$product_search_terms = urldecode(getPostOrGetValue('product_search_terms'));

	$search_array = explode(' ', $product_search_terms);

	//create a list of sub_ids -> from there turn that to an array
	/*$subids = array();
	for($si=0;$si<sizeof($search_array);$si++)
	{
		$search_term = $search_array[$si];
		//1 search the brand name
	
		$brand_name_sql = "
		SELECT pos_product_sub_id FROM pos_products_sub_id
		LEFT JOIN pos_products USING (pos_product_id)
		LEFT JOIN pos_manufacturer_brands USING (pos_manufacturer_brand_id)
		WHERE brand_name LIKE '%$search_term%'";
		$brand_name_results = getFieldRowSql($brand_name_sql);
		if(sizeof($brand_name_results)>0)
		{
			$subids = array_merge($subids,$brand_name_results['pos_product_sub_id']);
		}
	
		//2 search the title
		$title_sql = "
		SELECT pos_product_sub_id FROM pos_products_sub_id
		LEFT JOIN pos_products USING (pos_product_id)
		WHERE title LIKE '%$search_term%'";
		$title_results = getFieldRowSql($title_sql);
		if(sizeof($title_results)>0)
		{
			$subids = array_merge($subids,$title_results['pos_product_sub_id']);
		}
		//3 search the style number
			$style_sql = "
		SELECT pos_product_sub_id FROM pos_products_sub_id
		LEFT JOIN pos_products USING (pos_product_id)
		WHERE style_number LIKE '%$search_term%'";
		$style_results = getFieldRowSql($style_sql);
		if(sizeof($style_results)>0)
		{
			$subids = array_merge($subids,$style_results['pos_product_sub_id']);
		}
	
		//4 search the options list
	
		$option_sql="	SELECT pos_product_sub_id 
				FROM pos_product_sub_id_options 
				LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
				WHERE option_name LIKE '%$search_term' OR option_code LIKE '%$search_term'";
		$option_results = getFieldRowSql($option_sql);
		if(sizeof($option_results)>0)
		{
			$subids = array_merge($subids,$option_results['pos_product_sub_id']);
		}
	
	
	
	
		//5 search the services list
	
		//6 search the promotions
	
	

	}*/

	//create a list of all the products then use a tmp table to select those...
	$titles_sql = "CREATE TEMPORARY TABLE products
	SELECT pos_products_sub_id.pos_product_sub_id, concat(pos_manufacturer_brands.brand_name,':',pos_products.title,':',pos_products.style_number,':',
		
				(SELECT group_concat(concat(option_code,'-',option_name) SEPARATOR ' ') 
				FROM pos_product_sub_id_options 
				LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
				LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
				WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
				) as long_name
		
			FROM pos_products_sub_id 
			LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
			LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
			;
			";
	
		$search_array_sql = '';
		for($si=0;$si<sizeof($search_array);$si++)
		{
			$search_array_sql .= " AND long_name LIKE '%". scrubInput($search_array[$si]) ."%' ";
		
		}
		$select_sql = "SELECT * FROM products WHERE 1" .$search_array_sql .' LIMIT 10';

		$dbc = openPOSdb();
		$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=1000');
		$result = runTransactionSQL($dbc,$titles_sql);
		$data = getTransactionFieldRowSql($dbc,$select_sql);
		closeDB($dbc);

	/*//now convert the subids into a full blown listing
	//first limit the subids to 12
	$imploded_subids = implode(',', $subids);

	$titles_sql = "SELECT concat(pos_manufacturer_brands.brand_name,':',pos_products.title,':',pos_products.style_number,':',
		
				(SELECT group_concat(concat(option_name) SEPARATOR ' ') 
				FROM pos_product_sub_id_options 
				LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
				LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
				WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
				) as long_name
		
			FROM pos_products_sub_id 
			LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
			LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
			WHERE pos_products_sub_id.pos_product_sub_id IN (" . $imploded_subids .")
	";
	$return_titles = getFieldRowSQL($titles_sql);*/

	echo json_encode($data);
}
elseif(strtoupper($ajax_request) == 'CHECK_LOGIN')
{
	$user = scrubInput(getPostOrGetValue('user'));
	$password = scrubInput(getPostOrGetValue('password'));
	$q = "SELECT pos_user_id FROM pos_users WHERE login='$user' AND password=SHA1('$password') AND active=1";	
	echo getSingleValueSQL($q);
}
elseif(strtoupper($ajax_request) == 'SAVE_INVOICE')
{

	//do this first to prevent deadlock 
		//********************** CUSOMTER ******************************
	$pos_customer_id = scrubInput($_POST['pos_customer_id']);
	if($pos_customer_id != 0)
	{
		$customer['phone'] = scrubInput($_POST['phone']);
		$customer['email1'] = scrubInput($_POST['email1']);
		$customer_key_val_id['pos_customer_id'] = $pos_customer_id;
		$results[] = simpleUpdateSQL('pos_customers', $customer_key_val_id, $customer);

	}
	
	
	//preprint($_POST);
	//exit();
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
	//$invoice_tbody_def = $_POST['invoice_tbody_def'];
	$invoice_table_name = 'invoice_table';
	$invoice_contents_col_def = createRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id, $invoice_table_name);
	$invoicetdo = (isset($_POST['invoice_tdo'])) ? $_POST['invoice_tdo'] : array();
	
	
	
	$promotion_tdo = (isset($_POST['promotion_tdo'])) ? $_POST['promotion_tdo'] : array();

	$dbc = startTransaction();

	//**************** INVOICE  ***********************************
	
	$pos_address_id = $_POST['pos_address_id'];
	$invoice_update['pos_customer_id'] = $pos_customer_id;
	$invoice_update['pos_address_id'] = $pos_address_id;
	$invoice_update['invoice_status'] = scrubInput($_POST['invoice_status']);
	$invoice_update['payment_status'] = 'UNPAID';
	$invoice_update['follow_up'] = $_POST['follow_up'];
	$invoice_update['special_order'] = $_POST['special_order'];
	
	//$invoice_update['shipping_amount'] = scrubInput($_POST['shipping_amount']);
	$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	$results[] = simpleTransactionUpdateSQL($dbc,'pos_sales_invoice', $key_val_id, $invoice_update);
	$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");



	//**************** INVOICE CONTENTS ***********************************
	//next delete all contents for the invoice
	$sic_delet_q = "DELETE FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = '$pos_sales_invoice_id'";
	$results[] = runTransactionSQL($dbc, $sic_delet_q);

	//write contents for the invoice
	// if the tax is mixed then flatten the quantity down...
	if(sizeof($invoicetdo)>0)
	{
		//make a (db_filed1,db_field2,etc) for the insert fields 
		// and (val1,val2,etc),(val1,val2,etc..) for the values....
	
	
		
	
	
	
		//This was the original way, which is really cool, but now that I am doing both gift cards and returns I need more detailed control
	
		$sql = array(); 
		$db_fields[]  = 'pos_sales_invoice_id';
		for($i=0;$i<sizeof($invoice_contents_col_def);$i++)
		{
			if(isset($invoice_contents_col_def[$i]['POST']) && $invoice_contents_col_def[$i]['POST'] =='no')
			{
			
			}
			else
			{
				$db_fields[] = scrubInput($invoice_contents_col_def[$i]['db_field']);
			}
		}
		$str_fields = implode(',', $db_fields);
		for($row=0;$row<sizeof($invoicetdo);$row++) 
		{	
			//strip the $ or percent off discount
			$invoicetdo[$row]['discount'] = str_replace('$' ,'', $invoicetdo[$row]['discount']);
			$invoicetdo[$row]['discount'] = str_replace('%' ,'', $invoicetdo[$row]['discount']);
		
		
			$field_counter= 0;
			$row_array = array();
			$row_array[$field_counter] = $pos_sales_invoice_id;
			$field_counter++;
			for($col=0;$col<sizeof($invoice_contents_col_def);$col++)
			{
				if((isset($invoice_contents_col_def[$col]['POST']) && $invoice_contents_col_def[$col]['POST'] =='no'))
				{}
				else
				{
					if($invoice_contents_col_def[$col]['type'] == 'checkbox')
					{
						if ($invoicetdo[$row][$invoice_contents_col_def[$col]['db_field']] == 'true')
						{
							$invoicetdo[$row][$invoice_contents_col_def[$col]['db_field']] = 1;
						}
						else
						{
							$invoicetdo[$row][$invoice_contents_col_def[$col]['db_field']] = 0;
						}
					}
					$row_array[$field_counter] = "'" . scrubInput($invoicetdo[$row][$invoice_contents_col_def[$col]['db_field']]) . "'";
					$field_counter++;
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
	if(sizeof($promotion_tdo)>0)
	{
		for($row=0;$row<sizeof($promotion_tdo);$row++)
		{
			$pos_promotion_id = $promotion_tdo[$row]['pos_promotion_id'];
			$applied_amount = $promotion_tdo[$row]['applied_amount'];
			$row_number = $promotion_tdo[$row]['row_number'];
			$prom_insert_sql = "INSERT INTO pos_sales_invoice_promotions (pos_sales_invoice_id, pos_promotion_id, applied_amount, row_number) VALUES ($pos_sales_invoice_id,$pos_promotion_id, '$applied_amount', '$row_number')"; 
			$results[] = runTransactionSQL($dbc, $prom_insert_sql);

		}
	}

	simpleCommitTransaction($dbc);
	if(isset($_POST['unlock']))
	{
		if(scrubInput($_POST['unlock']) == 'YES')
		{
			$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
			$result = unlock_entry('pos_sales_invoice', $key_val_id);
		}
	}
	if(isset($_POST['finalize']) && scrubInput($_POST['finalize']) == 'YES')
	{
		finalizePaymentTransaction($pos_sales_invoice_id);
	}
	echo 'STORED ' . count($_POST, COUNT_RECURSIVE) . ' Variables With a Maximum set to ' . ini_get('max_input_vars') . ' Variables. If this maximum is low, like 1000 or 2000, check php.ini for php_max_vars and modify.' . newline();
}
elseif(strtoupper($ajax_request) == 'GET_TAX_RATE')
{
	$pos_sales_tax_category_id = getPostOrGetID('pos_sales_tax_category_id');
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
	$pos_terminal_id = terminalCheck();
	$terminal_info = getTerminalInfo($pos_terminal_id);
	$pos_store_id = $terminal_info[0]['pos_store_id'];

	//the address id is needed to get the tax rate...

	$pos_address_id = getPostOrGetValue('pos_address_id');
	$ship = getPostOrGetValue('ship');
	$invoice_date = getSalesInvoiceDate($pos_sales_invoice_id);
	//need to code the tax jurisdiction ids based on shipping or store...
	//sold in store
	if($ship == 'false')
	{
		//sold in store
		$pos_local_tax_jurisdiction_id = getTaxJurisdictionOfStore($pos_store_id);
		$pos_state_id = getTaxJurisdictionStateID($pos_local_tax_jurisdiction_id);
		$tax['search'] = "In store";
	}
	elseif($pos_address_id == 'false')
	{
		//address is effed so default to local jurisdiction
			$pos_local_tax_jurisdiction_id = getStoreTaxJurisdictionID($pos_store_id);
			$pos_state_id = getStoreStateId($pos_store_id);
			$tax['search'] = 'shipped however no address is selected.... so we cannot actually return tax...';
			
	}
	else
	{		
		//$pos_address_id = getSalesInvoiceAddress($pos_sales_invoice_id);
		$zip_code = getZipCode($pos_address_id);
		$pos_state_id = getAddressStateId($pos_address_id);
		//preprint('state' . $pos_state_id);
		if($zip_code != '')
		{
			$pos_local_tax_jurisdiction_id = getTaxJurisdictionFromZipCode($zip_code);
			//preprint('pos_local_tax_jurisdiction_id: ' . $pos_local_tax_jurisdiction_id);
			$tax['search'] = $zip_code;
		}
	}

	//$tax = getProductTaxArray($pos_sales_tax_category_id, $price, $invoice_date);
	$tax['data'] = getProductTaxArray($pos_local_tax_jurisdiction_id, $pos_state_id, $pos_sales_tax_category_id, $invoice_date);
	$tax['data']['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
	echo json_encode($tax) . "\n";
}
elseif(strtoupper($ajax_request) == 'GATEWAY_CHANGE')
{
	$pos_payment_gateway_id = scrubInput(getPostOrGetValue('pos_payment_gateway_id'));
	$line = getGatewayLine($pos_payment_gateway_id);
	

	echo $line;
}
elseif(strtoupper($ajax_request) == 'CASH_CHECK_PAYMENT')
{
	//coming over is a cash payment. need to insert this payment, 
	
	$pos_sales_invoice_id = $_POST['pos_sales_invoice_id'];
	$dbc = startTransaction();
	$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	
	$amount = $_POST['amount'];
	$customer_payment_inset['pos_customer_payment_method_id'] = getCustomerPaymentMethodID($_POST['payment_type']);
	$customer_payment_inset['deposit_account_id'] = scrubInput($_POST['deposit_account']);
	$customer_payment_inset['payment_amount'] = scrubInput($amount);
	$customer_payment_inset['date'] = scrubInput($invoice_date);
	$customer_payment_inset['summary'] = ''; //this is where you would add a card number, check number, etc...
	$customer_payment_inset['comments'] = scrubInput($_POST['applied_comments']);
	$pos_customer_payment_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payments', $customer_payment_inset);
	
	$lookup_insert['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	$lookup_insert['pos_customer_payment_id'] = $pos_customer_payment_id;
	$lookup_insert['applied_amount'] = scrubInput($amount);
	$lookup_insert['applied_comments'] = scrubInput($_POST['applied_comments']);
	simpleTransactionInsertSQL($dbc, 'pos_sales_invoice_to_payment', $lookup_insert);
	
	simpleCommitTransaction($dbc);
	echo finalizePaymentTransaction($pos_sales_invoice_id);
	
}
elseif(strtoupper($ajax_request) == 'CREDIT_CARD_PAYMENT')
{
	
	// 
	/*
		find out the payment gateway
		for auth.net process the charge
		the response needs to be returned
		
		for all other payments:
		enter the payment into customer payments
	
	
	*/
	
	
	//what is the payment gateway?
	$pos_sales_invoice_id = $_POST['pos_sales_invoice_id'];
	$pos_payment_gateway_id = scrubInput($_POST['pos_payment_gateway_id']);
	$line = getGatewayLine($pos_payment_gateway_id);
	if(strtoupper($line) == 'ONLINE')
	{
		//online cc payment
		//find the provider, then find out the submission method
		$swipe = scrubInput($_POST['swipe']);
		
		$amount = scrubInput($_POST['amount']);
		$gateway_provider = getGatewayProvider($pos_payment_gateway_id);
		
	
		if($gateway_provider == "Authorize.net")
		{
			
			$api_login = getAPILoginID($pos_payment_gateway_id);
			$transaction_key = getTrasactionKey($pos_payment_gateway_id);
			
			
			
	
/*	

if having probelms with auth.net try direct url	https://cardpresent.authorize.net/gateway/transact.dll?x_cpversion=1.0&x_device_type=4&x_market_type=2&x_type=AUTH_ONLY&x_amount=1.99&x_card_num=4111111111111111&x_exp_date=0615&x_delim_data=TRUE&x_relay_response=FALSE&x_login=5udK8MDk3PTA&x_tran_key=8s6FwvgX65F6K2YD
*/
			//require_once(AUTHORIZE_NET_LIBRARY); not using thier library as it wont work...
			//set test to true when testing cards.....
			if(test_cc_proccess('amount'))
			{	
				$min =0;
				$max=0.5;
				$amount = round($min + mt_rand() / mt_getrandmax() * ($max - $min),2);
			}
			else
			{
				$amount = scrubInput($_POST['amount']);
			}
			//manual or keyed
			if ($swipe == 'swipe')
			{
				$card_data = $_POST['card_data'];	
				$card_type = scrubInput($_POST['card_data']['card_type']);
				$secure_card_number = $_POST['card_data']['secure_card_number'];
				if(test_cc_proccess('card_number'))
				{
					$track1_data = setTrack1Data('%B5454545454545454^IPCOMMERCE/TESTCARD^1312101013490000000001000880000?');
				}
				else
				{
					$track1_data = setTrack1Data($card_data['track1']);
				}
				$post_values = array(

		// the API Login ID and Transaction Key must be replaced with valid values
		"x_login"			=> $api_login,
		"x_tran_key"		=> $transaction_key,

		"x_device_type"		=> "4",
		"x_cpversion"		=> "1.0",
		"x_market_type" 	=> "2",

		"x_delim_data"		=> "TRUE",
		"x_delim_char"		=> "|",
		"x_relay_response"	=> "FALSE",


		"x_type"			=> "AUTH_CAPTURE",
		"x_method"			=> "CC",
		"x_track1"		=> $track1_data,

		"x_amount"			=> $amount,
		"x_description"		=> "Embrasse-Moi",

		"x_invoice_num" => $pos_sales_invoice_id
		// Additional fields can be added here as outlined in the AIM integration
		// guide at: http://developer.authorize.net
	);
			}
			elseif ($swipe = 'keyed')
			{
				$exp = scrubInput($_POST['exp']);
				$card_type = scrubInput($_POST['card_type']);
				if(test_cc_proccess('card_number'))
				{
					//http://www.paypalobjects.com/en_US/vhelp/paypalmanager_help/credit_card_numbers.htms
					$card_number = '4007000000027';
				}
				else
				{
					$card_number = scrubInput($_POST['card_number']);
				}	
				$secure_card_number = '****' . substr($card_number, strlen($card_number) - 4, 4);	
				$post_values = array(

		// the API Login ID and Transaction Key must be replaced with valid values
		"x_login"			=> $api_login,
		"x_tran_key"		=> $transaction_key,

		"x_device_type"		=> "4",
		"x_cpversion"		=> "1.0",
		"x_market_type" 	=> "2",

		"x_delim_data"		=> "TRUE",
		"x_delim_char"		=> "|",
		"x_relay_response"	=> "FALSE",


		"x_type"			=> "AUTH_CAPTURE",
		"x_method"			=> "CC",
		"x_card_num" 		=> $card_number,
		"x_exp_date" 		=> $exp,

		"x_amount"			=> $amount,
		"x_description"		=> "Clothing",

		"x_invoice_num" => $pos_sales_invoice_id
		// Additional fields can be added here as outlined in the AIM integration
		// guide at: http://developer.authorize.net
	);
			}
			
			
		$response_array =  process_cc_payment($post_values);
		//preprint($response_array[1]);
		if($response_array[1] == 1)
		{
			/*echo 'Vesion ' . $response_array[0] .newline(); 
			echo 'APPROVED with code' . $response_array[1] .newline(); 
			echo 'Reason Code ' . $response_array[2] . newline();
			echo 'Reason Text ' . $response_array[3] . newline();  
			echo 'Authorization Code ' . $response_array[4] . newline(); 
			echo 'Transaction Code ' . $response_array[7] . newline(); 
			preprint($response_array); */
			
			$dbc = startTransaction();
			$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	
			$customer_payment_inset['pos_customer_payment_method_id'] = getCustomerPaymentMethodID($card_type);
			$customer_payment_inset['pos_payment_gateway_id'] = scrubInput($_POST['pos_payment_gateway_id']);
			
			$customer_payment_inset['transaction_status'] = 'CAPTURED';
			$customer_payment_inset['deposit_account_id'] = getGatewayDepositAccount(scrubInput($_POST['pos_payment_gateway_id']));
			$customer_payment_inset['card_number'] = $secure_card_number;//$response_array[20];
			$customer_payment_inset['payment_amount'] = $amount;
			$customer_payment_inset['date'] = getDateTime();
			$customer_payment_inset['summary'] = implode('|', $response_array); 
			$customer_payment_inset['transaction_id'] = $response_array[7];
			$customer_payment_inset['authorization_code'] = $response_array[4];
			$customer_payment_inset['batch_id'] = '';
			//$customer_payment_inset['comments'] = scrubInput($_POST['applied_comments']);
			$pos_customer_payment_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payments', $customer_payment_inset);
	
			$lookup_insert['pos_sales_invoice_id'] = $pos_sales_invoice_id;
			$lookup_insert['pos_customer_payment_id'] = $pos_customer_payment_id;
			$lookup_insert['applied_amount'] = $amount;
			$lookup_insert['applied_comments'] = '';
			simpleTransactionInsertSQL($dbc, 'pos_sales_invoice_to_payment', $lookup_insert);
	
			simpleCommitTransaction($dbc);
			
	
	
	
	
		}
		else if($response_array[1] == 2)
		{
			
			
			/*echo 'Declined with code ' . $response_array[1] .newline(); 
		
			echo 'Reason Code ' . $response_array[2] . newline(); 
			echo 'Reason Text ' . $response_array[3] . newline(); 
			preprint($response_array); */
			
		}
		else if($response_array[1] == 3)
		{
			
			// expired
			/*echo 'Error with code ' . $response_array[1] .newline(); 
		
			echo 'Reason Code ' . $response_array[2] . newline(); 
			echo 'Reason Text ' . $response_array[3] . newline(); 
			preprint($response_array); */
		}
		else if($response_array[1] == 4)
		{
			/*echo 'Held For Review with code' . $response_array[1] .newline(); 
		
			echo 'Reason Code ' . $response_array[2] . newline(); 
			echo 'Reason Text ' . $response_array[3] . newline(); 
			preprint($response_array); */
		}
		echo json_encode($response_array);
		//echo "test";
		//exit();
	
		
		}
		else
		{
			///not really doing anything.....but this is the code are for new providers.... like orbital
		}
	}
	else if ($line == 'offline')
	{
		//offline cc payment....
		//just enter the data
		$pos_payment_gateway_id = scrubInput($_POST['pos_payment_gateway_id']);
		$amount = scrubInput($_POST['amount']);
		$gateway_provider = getGatewayProvider($pos_payment_gateway_id);
		$dbc = startTransaction();
		$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");

		$customer_payment_inset['pos_customer_payment_method_id'] = getCustomerPaymentMethodID(scrubInput($_POST['card_type']));
		$customer_payment_inset['deposit_account_id'] = getGatewayDepositAccount(scrubInput($_POST['pos_payment_gateway_id']));
		$customer_payment_inset['card_number'] = scrubInput($_POST['card_number']);//$response_array[20];
		$customer_payment_inset['payment_amount'] = $amount;
		$customer_payment_inset['date'] = getDateTime();
		//$customer_payment_inset['summary'] = implode('|', $response_array); 
		//$customer_payment_inset['transaction_id'] = $response_array[7];
		//$customer_payment_inset['authorization_code'] = $response_array[4];
		//$customer_payment_inset['batch_id'] = '';
		//$customer_payment_inset['comments'] = scrubInput($_POST['applied_comments']);
		$pos_customer_payment_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payments', $customer_payment_inset);

		$lookup_insert['pos_sales_invoice_id'] = $pos_sales_invoice_id;
		$lookup_insert['pos_customer_payment_id'] = $pos_customer_payment_id;
		$lookup_insert['applied_amount'] = $amount;
		$lookup_insert['applied_comments'] = scrubInput($_POST['applied_comments']);
		simpleTransactionInsertSQL($dbc, 'pos_sales_invoice_to_payment', $lookup_insert);

		simpleCommitTransaction($dbc);
		
	}
	else 
	{
		//something else
		//error
	}
	
	
	
	
	
	
	
	//now
	finalizePaymentTransaction($pos_sales_invoice_id);
	
}
elseif(strtoupper($ajax_request) == 'CREDIT_CARD_REFUND')
{
	$pos_sales_invoice_id = $_POST['pos_sales_invoice_id'];
	$line = scrubInput($_POST['line']);
	if(strtoupper($line) == 'ONLINE')
	{
		echo 'not coded....';
		
		//only valid for 120 days....
		//x_type=CREDIT
   		//x_trans_id=Transaction ID here
  		//x_card_num=Full credit card number or last four digits only here
		
		//unlinked credit....requires special setup of account....
		//The unique field requirement for an Unlinked Credit is:
        //x_type=CREDIT
		
	}
	else if ($line == 'offline')
	{
		//offline cc payment....
		//just enter the data
		$pos_payment_gateway_id = scrubInput($_POST['pos_payment_gateway_id']);
		$amount = scrubInput($_POST['amount']);
		$gateway_provider = getGatewayProvider($pos_payment_gateway_id);
		$dbc = startTransaction();
		$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");

		$customer_payment_inset['pos_customer_payment_method_id'] = getCustomerPaymentMethodID(scrubInput($_POST['card_type']));
		$customer_payment_inset['deposit_account_id'] = getGatewayDepositAccount(scrubInput($_POST['pos_payment_gateway_id']));
		$customer_payment_inset['card_number'] = scrubInput($_POST['card_number']);//$response_array[20];
		$customer_payment_inset['payment_amount'] = $amount;
		$customer_payment_inset['date'] = getDateTime();

		$pos_customer_payment_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payments', $customer_payment_inset);

		$lookup_insert['pos_sales_invoice_id'] = $pos_sales_invoice_id;
		$lookup_insert['pos_customer_payment_id'] = $pos_customer_payment_id;
		$lookup_insert['applied_amount'] = $amount;
		$lookup_insert['applied_comments'] = scrubInput($_POST['applied_comments']);
		simpleTransactionInsertSQL($dbc, 'pos_sales_invoice_to_payment', $lookup_insert);

		simpleCommitTransaction($dbc);
		
	}
	else 
	{
		//something else
		//error
	}
	//now
	finalizePaymentTransaction($pos_sales_invoice_id);
	
}
elseif(strtoupper($ajax_request) == 'LOOKUP_STORE_CREDIT')
{
	$card_number = scrubInput(getPostOrGetValue('store_card_number'));

		$sql = "SELECT pos_store_credit.pos_store_credit_id

		FROM pos_store_credit 
		WHERE pos_store_credit.card_number = '$card_number' ";
		
		
	$data = getSQL($sql);

	if(sizeof($data)==1)
	{
		//$return_data['card_number'] = $data[0]['card_number']
		
		echo getStoreCreditCardValue($data[0]['pos_store_credit_id']);
	}
	elseif(sizeof($data)>1)
	{
		echo "More than one card found with the same number";
	}
	else
	{
		echo "No Data Found";
	}
}
elseif(strtoupper($ajax_request) == 'STORE_CREDIT_PAYMENT')
{
	//here we insert the payment
	
	$pos_sales_invoice_id = $_POST['pos_sales_invoice_id'];
	$dbc = startTransaction();
	$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	
	$card_number = scrubInput($_POST['store_card_number']);
	$amount = scrubInput($_POST['amount']);
	$pos_terminal_id = terminalCheck();
	if($card_number != '')
	{
		
		
		//card is from the system
		$pos_store_credit_id = 	getStoreCreditCardID($card_number);
		//now that the transaction has started lets double check the card is good to go...
		$value = getStoreCreditCardValue($pos_store_credit_id,$dbc);
		if($value < $amount)
		{
			//problem!!!
			$return_array['payment_status'] = 'error';
			$return_array['message'] = 'The amount exceeds the card value';
			echo json_encode($return_array);
			exit();
		}
		$card_type = getSingleValueSQL("SELECT card_type FROM pos_store_credit where pos_store_credit_id = $pos_store_credit_id");

	}
	else
	{
		//card is not from the system
		$pos_store_credit_id = 0;
		$card_type = 'Gift Card';
	}

	//good to go...
	if($card_type == 'Gift Card')
	{
		$default_account_id = getSingleValueSQL("SELECT default_gift_card_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	}
	else if ($card_type == 'Store Credit')
	{
		$default_account_id = getSingleValueSQL("SELECT default_store_credit_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	}
	else if ($card_type == 'Deposit')
	{
		$default_account_id = getSingleValueSQL("SELECT default_prepay_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	}
	else
	{
	}
			
	$customer_payment_inset['deposit_account_id']=$default_account_id;
	$customer_payment_inset['pos_customer_payment_method_id'] = getCustomerPaymentMethodID('Store Credit');
	$customer_payment_inset['pos_store_credit_id'] = $pos_store_credit_id;
	$customer_payment_inset['payment_amount'] = $amount;
	$customer_payment_inset['date'] = $invoice_date;
	$customer_payment_inset['summary'] = $card_number; //this is where you would add a card number, check number, etc...
	$customer_payment_inset['comments'] = '';
	$pos_customer_payment_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payments', $customer_payment_inset);

	$lookup_insert['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	$lookup_insert['pos_customer_payment_id'] = $pos_customer_payment_id;
	$lookup_insert['applied_amount'] = $amount;
	$lookup_insert['applied_comments'] = scrubInput($_POST['applied_comments']);
	simpleTransactionInsertSQL($dbc, 'pos_sales_invoice_to_payment', $lookup_insert);

	simpleCommitTransaction($dbc);
	echo finalizePaymentTransaction($pos_sales_invoice_id);
	
	$return_array['status'] = 'OK';
	
	
	
	
}
elseif(strtoupper($ajax_request) == 'NON_PAYMENT')
{
	//here we insert the payment
	$pos_sales_invoice_id = $_POST['pos_sales_invoice_id'];
	$dbc = startTransaction();
	$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	$amount = $_POST['amount'];

	$customer_payment_inset['pos_customer_payment_method_id'] = getCustomerPaymentMethodID('Other');
	$customer_payment_inset['payment_amount'] = $amount;
	$customer_payment_inset['date'] = $invoice_date;
	$customer_payment_inset['summary'] = 'Non-Payment'; //this is where you would add a card number, check number, etc...
	$customer_payment_inset['comments'] = scrubInput($_POST['comments']);
	$pos_customer_payment_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payments', $customer_payment_inset);

	$lookup_insert['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	$lookup_insert['pos_customer_payment_id'] = $pos_customer_payment_id;
	$lookup_insert['applied_amount'] = $amount;
	$lookup_insert['applied_comments'] = scrubInput($_POST['applied_comments']);
	simpleTransactionInsertSQL($dbc, 'pos_sales_invoice_to_payment', $lookup_insert);

	simpleCommitTransaction($dbc);
	echo finalizePaymentTransaction($pos_sales_invoice_id);
	
	$return_array['status'] = 'OK';
	
	
	
	
}
// REFUND STUFF
elseif(strtoupper($ajax_request) == 'RETURN_INVOICE_LOOKUP')
{

	//lookup possible invoices by the following
	
	//IMPORTANT - without an original invoice we cannot refund to credit card....
		//Original invoice can be verified by original invoice, credit card statement, card number, license for ID
		//
	
	//$user_data['pos_sales_invoice_id'] = $_POST['pos_sales_invoice_id'];
	$return_data = array();
	
	$card_data = scrubInput($_POST['return_cc']);
	$barcode = scrubInput($_POST['return_barcode']);
	
	$search_type = $_POST['search_type'];
	//how are we searching?
	
	if ($search_type == 'INVOICE')
	{
		$pos_return_sales_invoice_id = scrubInput($_POST['pos_return_sales_invoice_id']);
		$return_data = getReturnInvoiceData($pos_return_sales_invoice_id);
		echo json_encode($return_data);
	}
	//each of these searches need to return the following: pos_return_sales_invoice_id, customer_info, products, payments
	elseif ($search_type == 'CC')
	{
		//curently cannot search by credit card...
		
		//looking for a transaction id
		$return_data['receipt_present'] = 'lala';
		$return_data['invoices'] = 'lala';
		//return false for no invoces?
		
		$return_data['search_type']  = $search_type;
		$return_data['receipt_present']  = 'false';
		
		//we need to go to auth.net to find transaction id...????
		//can we go there?
		//find the default payment gate way, if it is online
		$default_pos_payment_gateway_id = getSingleValueSQL("SELECT pos_payment_gateway_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
		$authnet_check = checkAuthNetOnline($default_pos_payment_gateway_id);
		

		if ($authnet_check)
		{
			$api_login = getAPILoginID($default_pos_payment_gateway_id);
			$transaction_key = getTrasactionKey($default_pos_payment_gateway_id);
		
			if(strpos($card_data, '%'))
			{
			
			}
			else if (is_numeric($card_data))
			{
			}
			else
			{
				$reurn_data['invoices'] = false;
			}
			
		}
		else
		{
			
			$reurn_data['error'] = 'Card Lookup is only possible through Authorize.net Live Payment Gateway';
		}
		echo json_encode($return_data);
	}
	elseif ($search_type == 'PRODUCT')
	{
		
		//looking for a product
		//not currently implementing this.
		$sql = "SELECT  
			date(pos_sales_invoices.invoice_date) as invoice_date,
			pos_customers.first_name,
			pos_customers.last_name,
			pos_customers.email1,
			pos_customers.phone,
			FROM pos_sales_invoices
			LEFT JOIN pos_sales_invoice_contents ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
			LEFT JOIN pos_customers ON pos_customers.pos_customer_id = pos_sales_invoices.pos_customer_id		
			WHERE date(pos_sales_invoice_date ) BETWEEN CURDATE() AND CURDATE() - INTERVAL " . $maxDays . " DAYS AND
			pos_invoice_contents.pos_product_sub_id = '$barcode'";
		
		$data  = getSQL($sql);
		$return_data['receipt_present'] = 'false';
		$return_data['invoices'] = $data;
		
		echo json_encode($return_data);
	}
	elseif ($search_type == 'CUSTOMER')
	{
		//looking up customer data...
		//tangled in a web of lookup crap
	
		//$user_data['pos_sales_invoice_id'] = $_POST['invoice_number'];
		$user_data['first_name'] = $_POST['first_name'];
		$user_data['last_name'] = $_POST['last_name'];
		$user_data['email'] = $_POST['email'];
		$user_data['phone'] = $_POST['phone'];

	
		$maxReturnDays = getSetting('maxReturnDays');
	
		//return an array that looks like this:
		/*
	
		date invoice_number	first name	last name  email phone
	
	
		*/
	
	
	
		$sql = "
	SELECT  
			date(pos_sales_invoice.invoice_date) as invoice_date,
			pos_sales_invoice_id as pos_return_invoice_id,
			pos_customers.first_name,
			pos_customers.last_name,
			pos_customers.email1,
			pos_customers.phone, 
			CONCAT(pos_customers.first_name, ' ', pos_customers.last_name, ' Email: ' , pos_customers.email1, ' PHONE:', pos_customers.phone) as customer_info,
			(SELECT group_concat(concat_ws(',', pos_product_sub_id, checkout_description) SEPARATOR '<br>') FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as products,
			
			(SELECT group_concat(payment_type) FROM pos_customer_payments
			LEFT JOIN pos_customer_payment_methods USING (pos_customer_payment_method_id)
			LEFT JOIN pos_sales_invoice_to_payment USING (pos_customer_payment_id)
			WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id)
			
			 as payments
			FROM pos_sales_invoice
			LEFT JOIN pos_customers ON pos_customers.pos_customer_id = pos_sales_invoice.pos_customer_id		
			WHERE date(invoice_date ) >= DATE_SUB(CURDATE(), INTERVAL " .$maxReturnDays." DAY)"
		

	;
	
	
									
	$sql .= " AND first_name LIKE '%" . scrubInput($_POST['first_name']) . "%' ";
	//$sql .= " AND last_name LIKE '%" . scrubInput($_POST['last_name']) . "%' ";
	//$sql .= " AND email1 LIKE '%" . scrubInput($_POST['email']) . "%' ";
	//$sql .= " AND phone LIKE '%" . scrubInput($_POST['phone']) . "%' ";
$sql .= " LIMIT 30";

	
		$data  = getSQL($sql);
		$return_data['limits'] = 'Results Limited to 30 Records AND are within the past ' . $maxReturnDays . ' Days';
		$return_data['receipt_present'] = 'false';
		$return_data['invoices'] = $data;
		//do we want to convert the invoice data
		echo json_encode($return_data);
	}
	else
	{
		echo 'nada';
	}
	
}
elseif(strtoupper($ajax_request) == 'CASH_REFUND')
{
	$pos_sales_invoice_id = $_POST['pos_sales_invoice_id'];
	$dbc = startTransaction();
	$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	
	$amount = $_POST['amount'];
	$customer_payment_inset['pos_customer_payment_method_id'] = getCustomerPaymentMethodID($_POST['payment_type']);
	$customer_payment_inset['deposit_account_id'] = $_POST['deposit_account'];
	$customer_payment_inset['payment_amount'] = -$amount;
	$customer_payment_inset['date'] = $invoice_date;
	$customer_payment_inset['summary'] = ''; //this is where you would add a card number, check number, etc...
	$customer_payment_inset['comments'] = scrubInput($_POST['applied_comments']);
	$pos_customer_payment_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payments', $customer_payment_inset);
	
	$lookup_insert['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	$lookup_insert['pos_customer_payment_id'] = $pos_customer_payment_id;
	$lookup_insert['applied_amount'] = -$amount;
	$lookup_insert['applied_comments'] = scrubInput($_POST['applied_comments']);
	simpleTransactionInsertSQL($dbc, 'pos_sales_invoice_to_payment', $lookup_insert);
	
	simpleCommitTransaction($dbc);
	echo finalizePaymentTransaction($pos_sales_invoice_id);
}
elseif(strtoupper($ajax_request) == 'CHECK_REFUND')
{
	//look up the customer ID to make sure the address is corrrect....
	$pos_sales_invoice_id = $_POST['pos_sales_invoice_id'];
	$pos_customer_id = getSingleValueSQL("Select pos_customer_id FROM pos_sales_invoices WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	$customer_info = getSQL("SELECT pos_customers.* FROM pos_sales_invoice LEFT JOIN pos_customers ON pos_customerS.pos_customer_id = pos_sales_invoice.pos_customer_id WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	$address = ("SELECT pos_addresses.* FROM pos_sales_invoice LEFT JOIN pos_addresses ON pos_sales_invoice.pos_address_id = pos_addresses.pos_address_id WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	
	if(sizeof($customer_info) > 0 && sizeof($address) > 0)
	{
		//customer info ok, 
		// payments journal entry .... links to the invoice to payment to get the amount, links to the invoice to get the customer address.......
		
		$dbc = startTransaction();
		$invoice_date = getTransactionSingleValueSQL($dbc, "SELECT invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");

		$amount = $_POST['amount'];
		$customer_payment_inset['pos_customer_payment_method_id'] = getCustomerPaymentMethodID($_POST['payment_type']);
		$customer_payment_inset['deposit_account_id'] = $_POST['deposit_account'];
		$customer_payment_inset['payment_amount'] = -$amount;
		$customer_payment_inset['date'] = $invoice_date;
		$customer_payment_inset['summary'] = ''; //this is where you would add a card number, check number, etc...
		$customer_payment_inset['comments'] = scrubInput($_POST['applied_comments']);
		$pos_customer_payment_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payments', $customer_payment_inset);

		$lookup_insert['pos_sales_invoice_id'] = $pos_sales_invoice_id;
		$lookup_insert['pos_customer_payment_id'] = $pos_customer_payment_id;
		$lookup_insert['applied_amount'] = -$amount;
		$lookup_insert['applied_comments'] = scrubInput($_POST['applied_comments']);
		simpleTransactionInsertSQL($dbc, 'pos_sales_invoice_to_payment', $lookup_insert);

		
		//now create the check.....
		/*
		PAYMENT LINKS TO SALES JOURNAL VIA INVOICE TO PAYMENTS TABLE FINDS THE SALES INVOICE OR INVOICES
		FROM THE SALES INVOICE WE GET THE PAYEE
		
		PAYMENT IS positive..... refund is negative?
		
		sales invoice total is negative
		cutomer payment is negative
		payment is positive..... 
		
		roll with it?
		
		
		*/
		
		$payments_journal['source_journal'] = "SALES JOURNAL";
		$payments_journal['pos_user_id'] = $_SESSION['pos_user_id'];
		$payments_journal['pos_account_id'] = $_POST['checking_account'];
		$payments_journal['payment_date'] = $invoice_date;
		$payments_journal['payment_entry_date'] = getDateTime();
		$payments_journal['payment_amount'] = $amount;
		$payments_journal['payment_status'] = 'COMPLETE';
		$payments_journal['applied_status'] = 'APPLIED';
		
		$pos_payments_journal_id = simpleTransactionInsertSQLReturnID($dbc, 'pos_payments_journal', $payments_journal);
		
		$invoice_to_payment['source_journal'] = "SALES JOURNAL";
		$invoice_to_payment['journal_id'] = $pos_sales_invoice_id;
		$invoice_to_payment['applied_amount'] = $amount;
		$invoice_to_payment['pos_payments_journal_id'] = $pos_payments_journal_id;
		
		simpleTransactionInsertSQL($dbc, 'invoice_to_payment', $invoice_to_payment);

		
		simpleCommitTransaction($dbc);
		
		
		
		echo finalizePaymentTransaction($pos_sales_invoice_id);
	}
	else
	{
		$error_array = array('payment_status' => 'UNPAID',
							'Code' => '1',
								'reason' => 'Error, Missing customer or customer address information');
		echo json_encode($error_array);
	}
	
}

elseif(strtoupper($ajax_request) == 'ISSUE_STORE_CREDIT')
{
	//check the printed card numbers first
	//check the issued card numbers
	$pos_sales_invoice_id = scrubInput($_POST['pos_sales_invoice_id']);
	$card_number = scrubInput(getPostOrGetValue('store_card_number'));
	$sql = "SELECT pos_store_credit_id
		FROM pos_store_credit 
		WHERE pos_store_credit.card_number = '$card_number' ";
			
	$data = getSQL($sql);

	if(sizeof($data)==1)
	{
		$return_data['card_data'] = $data[0];
		$return_data['card_type'] = 'active';
		echo json_encode($return_data) . "\n";
	}
	elseif(sizeof($data)>1)
	{
		echo "More than one card found with the same number";
	}
	else
	{
		//check the card listing...
		$sql = "SELECT pos_store_credit_card_number_id FROM pos_store_credit_card_numbers WHERE card_number ='$card_number'";
		$data = getSQL($sql);

		if(sizeof($data)==1)
		{
			$total_due = getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id);
			$total_payments = getTotalPayments($pos_sales_invoice_id);
			$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
			$amount_due = $total_due - $total_payments;
			if($amount_due>0) trigger_error('why is the amount due greater than zero here?');
			$store_credit_insert['original_amount'] = -$amount_due;
			$store_credit_insert['card_type'] = 'Store Credit';
			$store_credit_insert['card_number'] = $card_number;
			
			$store_credit_insert['pos_customer_id'] = $invoice_data[0]['pos_customer_id'];
			$store_credit_insert['date_created'] = $invoice_data[0]['invoice_date'];
			$store_credit_insert['date_issued'] = $invoice_data[0]['invoice_date'];
			$store_credit_insert['pos_user_id'] = $_SESSION['pos_user_id'];
			$dbc = startTransaction();

			$pos_store_credit_id = simpleTransactionInsertSQLReturnID($dbc,'pos_store_credit',$store_credit_insert);
					
					
			//now add as a payment
			$pos_terminal_id = terminalCheck();
			$default_account_id = getSingleValueSQL("SELECT default_store_credit_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");		
			$customer_payment_inset['pos_customer_payment_method_id'] = getCustomerPaymentMethodID('Store Credit');
			$customer_payment_inset['pos_store_credit_id'] = $pos_store_credit_id;
			$customer_payment_inset['payment_amount'] = $amount_due;
			$customer_payment_inset['deposit_account_id']=$default_account_id;
			$customer_payment_inset['date'] = $invoice_data[0]['invoice_date'];
			$customer_payment_inset['summary'] = $card_number; //this is where you would add a card number, check number, etc...
			$customer_payment_inset['comments'] = '';
			$pos_customer_payment_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payments', $customer_payment_inset);

			$lookup_insert['pos_sales_invoice_id'] = $pos_sales_invoice_id;
			$lookup_insert['pos_customer_payment_id'] = $pos_customer_payment_id;
			$lookup_insert['applied_amount'] = $amount_due;
			$lookup_insert['applied_comments'] = '';
			simpleTransactionInsertSQL($dbc, 'pos_sales_invoice_to_payment', $lookup_insert);

			simpleCommitTransaction($dbc);
			$paid_status =  finalizePaymentTransaction($pos_sales_invoice_id);
						
			$return_data['card_type'] = 'new';
			$return_data['amount_added'] = $amount_due;
			$return_data['paid_status'] = $paid_status;
			echo json_encode($return_data) . "\n";
			
	
		}
		else
		{
			$return_data['card_type'] = 'ERROR - card not found';
			echo json_encode($return_data) . "\n";
		}
	}
	
}
elseif(strtoupper($ajax_request) == 'SHIPPING')
{
	/*

		to get shipping we need 
		weight
		destination
		dimensions
		location
		serivice type
	
		all this should come from the sales invoice.....
		however the address might change, so we would update based on that....

	*/
	$pos_shipping_option_id = 7;
	$shipping_description = getShippingName($pos_shipping_option_id);
	$shipping_description = 'Shipping';
	
	//tax shipping at the store level....
	$pos_address_id = scrubInput(getPostOrGetValue('pos_address_id'));
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');


	$shipping_data['content_type'] = 'SHIPPING';
	$shipping_data['quantity'] = 1;
	$shipping_data['title'] = $shipping_description;
	$shipping_data['checkout_description'] = $shipping_description;


	$pos_sales_tax_category_id = getSingleValueSQL( "SELECT pos_sales_tax_category_id FROM pos_shipping_options WHERE pos_shipping_option_id = $pos_shipping_option_id");
	$shipping_data['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;


	$shipping_data['retail_price'] = 0;
	$shipping_data['sale_price'] = '-';
	/*if($pos_address_id != 'false')
	{
		$zip_code = getZipCode($pos_address_id);
		$pos_state_id = getAddressStateId($pos_address_id);
		$pos_local_tax_jurisdiction_id = getTaxJurisdictionFromZipCode($zip_code);
		if($pos_local_tax_jurisdiction_id != false)
		{
			$invoice_date = getSalesInvoiceDate($pos_sales_invoice_id);
			$tax = getProductTaxArray($pos_local_tax_jurisdiction_id, $pos_state_id, $pos_sales_tax_category_id, $invoice_date);
			$shipping_data = array_merge($shipping_data,$tax);
		}
	}*/

	//shipping taxed at point of service which is in store
	$pos_terminal_id = terminalCheck();
	$terminal_info = getTerminalInfo($pos_terminal_id);
	$pos_store_id = $terminal_info[0]['pos_store_id'];
	$pos_local_tax_jurisdiction_id = getTaxJurisdictionOfStore($pos_store_id);
	$pos_state_id = getTaxJurisdictionStateID($pos_local_tax_jurisdiction_id);
	$invoice_date = getSalesInvoiceDate($pos_sales_invoice_id);
			$tax = getProductTaxArray($pos_local_tax_jurisdiction_id, $pos_state_id, $pos_sales_tax_category_id, $invoice_date);
			$shipping_data = array_merge($shipping_data,$tax);

	echo json_encode($shipping_data) . "\n";
}
elseif(strtoupper($ajax_request) == 'PRINT')
{
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
	$type = getPostOrGetValue('type');


	if ($type=='customer')
	{
		printCustomerCopySalesInvoice($pos_sales_invoice_id);
		echo 'Sent to Printer';
		exit();
	
	}
	elseif ($type=='store')
	{

		printStoreCopyMemoSalesInvoice($pos_sales_invoice_id);
		echo 'Sent to Printer';
		exit();
	}
	elseif ($type =='customer_inline')
	{
		openInlineCustomerCopySalesInvoice($pos_sales_invoice_id);
		echo 'Sent Inline';
		exit();
	}
	elseif ($type =='email_pdf')
	{
		$email_status = emailInvoicePDF($pos_sales_invoice_id);
		echo $email_status;
		exit();
	}
	elseif ($type =='email_html')
	{
		//don't use.... not fully coded, need payments, promotions, etc... booooring.
		$email_status = emailInvoiceHtml($pos_sales_invoice_id);
		echo $email_status;
		exit();
	}
	elseif ($type =='gift_receipt')
	{
		printCustomerCopyGiftReceipt($pos_sales_invoice_id);
		echo 'Sent to Printer';
		exit();
	}
	else
	{	
		echo 'No Type';
		exit();
	}
}
elseif(strtoupper($ajax_request) == 'SPECIAL_ORDER_UPDATE')
{
	
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
	$special_order = scrubInput($_POST['special_order']);
	$sql = "UPDATE pos_sales_invoice SET special_order='$special_order' WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	runSQL($sql);
	echo 'updated special order to ' . $special_order;
	
}
elseif(strtoupper($ajax_request) == 'FOLLOW_UP_UPDATE')
{
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
	$follow_up = scrubInput($_POST['follow_up']);
	$sql = "UPDATE pos_sales_invoice SET follow_up='$follow_up' WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	runSQL($sql);
	echo 'updated follow_up  to ' . $follow_up;
}
else
{
}

function convertProductDataForReturn($data,$pos_sales_invoice_id)
{
		$pos_product_sub_id = $data[0]['pos_product_sub_id'];
		$barcode_data['content_type'] = 'PRODUCT';
		$barcode_data['style_number'] = $data[0]['style_number'];
		$barcode_data['barcode'] = $data[0]['barcode'];
		$barcode_data['pos_product_id'] = $data[0]['pos_product_id'];
		$barcode_data['pos_product_sub_id'] = $data[0]['pos_product_sub_id'];
		//for the promotion
		$barcode_data['pos_category_id'] = getProductCategory($data[0]['pos_product_id']);
		$barcode_data['pos_manufacturer_brand_id'] = getProductBrandID($data[0]['pos_product_id']);
		$barcode_data['pos_promotion_id'] = 0;
		
		
		$barcode_data['quantity'] = 1;
		$barcode_data['retail_price'] = $data[0]['retail_price'];
	    $barcode_data['clearance'] = $data[0]['clearance'];
		$barcode_data['sale_price'] = ($data[0]['sale_price'] == 0) ? $data[0]['retail_price'] : $data[0]['sale_price'];
		$barcode_data['title'] = $data[0]['title'];
		$barcode_data['checkout_description'] = getProductCheckoutDescription($pos_product_sub_id);
		$barcode_data['product_options'] = $data[0]['product_options'];
		$barcode_data['brand_name'] = getBrandName(getBrandFromProductId($data[0]['pos_product_id']));
		
		//$barcode_data['applied_instore_discount'] = 0;
		//$barcode_data['tax_total'] = 0;
		//$barcode_data['discount'] = 0;
		//$barcode_data['comments'] = '';
	
	
		$barcode_data['size'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Size'));
		$barcode_data['color_code'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Color'));
		$barcode_data['cup'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Cup'));	
		$barcode_data['color_name'] = getProductOptionName($pos_product_sub_id, getProductAttributeId('Color'));
	   $barcode_data['big_title'] = $barcode_data['brand_name'] . ' ' . $data[0]['title'] . ' in ' . $barcode_data['color_name'];
		$pos_sales_tax_category_id = getProductSalesTaxCategoryId($data[0]['pos_product_id']);
		$barcode_data['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
		//now for the tax
		//is it taxable?
	
		//is it shipped or in-store?
		// cuurently in store
		$shipped = false;
		if($shipped)
		{
			//get the state and tax jurisdiction
			//because this is a zappy it will not be initially shipped.....
			//we will need to click on 'shipped' to get the ship tax rate....
		}
		else
		{
			//sold in store
			$pos_store_id = $_SESSION['store_id'];
			$terminal_info = getTerminalInfo(terminalCheck());
			$pos_local_tax_jurisdiction_id = getTaxJurisdictionOfStore($terminal_info[0]['pos_store_id']);
			$pos_state_id = getTaxJurisdictionStateID($pos_local_tax_jurisdiction_id);
			$pos_state_tax_jurisdiction_id = getStateTaxJurisdictionId($pos_state_id);
		
			//the tax rate id needs to be tagged to the sale item -- however that can be deleted
		
		}
	
		$invoice_date = getSalesInvoiceDate($pos_sales_invoice_id);
		$tax = getProductTaxArray($pos_local_tax_jurisdiction_id, $pos_state_id, $pos_sales_tax_category_id, $invoice_date);

		$return_data = array_merge($barcode_data,$tax);
		return $return_data;
}
function getProductCheckoutDescription($pos_product_sub_id)
{
	//category: Brand name Title 'size' size 'in color' Color name
	$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);
	$return_string ='';
	$return_string .= getProductCategoryName($pos_product_id);
	$return_string .=  ': ' ;
	$return_string .= getProductBrandCode($pos_product_id);
	$return_string .= ' ';
	$return_string .= getProductTitle($pos_product_id);
	//$return_string .= ' Size:';
	//$return_string .= getProductOptionName($pos_product_sub_id, getProductAttributeId('Size'));
	//$return_string .= ' In Color ';
	//$return_string .= getProductOptionName($pos_product_sub_id, getProductAttributeId('Color'));
	return  $return_string;
}
function getPorductOptionsForCheckout($pos_product_sub_id)
{
}
function getStoreCreditCardID($card_number)
{	
	$card_number = getSingleValueSQL("SELECT pos_store_credit_id FROM pos_store_credit WHERE card_number = '$card_number'");
	return $card_number;
}
//################### STORE CREDIT FUNCTIONS ####################################


function getGatewayLine($pos_payment_gateway_id)
{
 return	getSingleValueSQL("SELECT line FROM pos_payment_gateways WHERE pos_payment_gateway_id = $pos_payment_gateway_id");
}
?>