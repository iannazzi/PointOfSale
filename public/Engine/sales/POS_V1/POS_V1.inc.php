<?
require_once('../sales_functions.php');


function getSetCustomerSearchResults($table_name, $table_def, $saved_session_search_name)
{

	//we want to load this from get, post or session....
	//calling from ajax we have post data with search fields
	//message or url can have get data
	//stored search can have data
	
	//priority get, post, session
	//get sets session
	//post sets session and get
	//session sets get parameters
	
	//if no search is set then dont get any data

	// to make this easy, we want the sql, we want to know the search parameters



$tmp_table_sql = "
CREATE TEMPORARY TABLE sales_invoices

SELECT  
		pos_sales_invoice.pos_sales_invoice_id,
		pos_sales_invoice.pos_sales_invoice_id as pos_sales_invoice_id_for_return,
		pos_sales_invoice.invoice_number,
		pos_sales_invoice.invoice_status,
		date(pos_sales_invoice.invoice_date) as invoice_date,
		time(pos_sales_invoice.invoice_date) as invoice_time,
		pos_customers.pos_customer_id,
		pos_customers.pos_customer_id as pos_customer_id_for_invoice,
		pos_customers.comments,
		pos_customers.first_name,
		pos_customers.last_name,
		pos_customers.phone,
		pos_customers.email1,
		concat(pos_customers.first_name, ' ' , pos_customers.last_name) as full_name,
		
		concat(pos_customers.first_name, ' ' , pos_customers.last_name, ' ', pos_customers.email1, ' ', pos_customers.phone) as customer_info,
		
		(SELECT group_concat(if(pos_sales_invoice_contents.pos_product_sub_id = 0, concat('No product id: ',checkout_description), concat(pos_manufacturer_brands.brand_name,':',pos_products.title,':',pos_products.style_number,':',
		
			(SELECT group_concat(concat(attribute_name,':',option_name) SEPARATOR ':') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			)) SEPARATOR '<BR>')
		
		FROM pos_sales_invoice_contents
		LEFT JOIN pos_products_sub_id ON pos_sales_invoice_contents.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id) as invoice_contents,
		
		
		
		
		
		(SELECT sum(extension) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type = 'CREDIT_CARD') as gift_cards_sold,
		
		(select sum(tax_total) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND invoice_status = 'CLOSED'
		) as total_tax,
		
		(SELECT coalesce(sum(extension),0)  - 
		
			(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
			AND promotion_type = 'Post Tax') FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND invoice_status = 'CLOSED'
		) as sum_extension_less_post_tax_promotion,
		
		(SELECT coalesce(sum(extension),0) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND invoice_status = 'CLOSED') as closed_sum_extension,
			(SELECT sum(extension)  - 
		
			(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
			AND promotion_type = 'Post Tax') FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND invoice_status = 'DRAFT'
		) as draft_extension,
		
		(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
			AND promotion_type = 'Post Tax') as post_tax_promotion_total,
		(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
			AND promotion_type = 'Pre Tax') as pre_tax_promotion_total,
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as total_payment,
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'Cash') as cash,
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'Check') as lcheck,
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'Gift Card') as gift_card,
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'Store Credit') as store_credit,
		
		
				(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_group = 'CREDIT_CARD' AND pos_customer_payment_methods.payment_type != 'American Express') as credit,
		
		(SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND pos_customer_payment_methods.payment_type = 'American Express') as amex,
		
		follow_up
		
		FROM pos_sales_invoice
		
		LEFT JOIN pos_customers ON pos_sales_invoice.pos_customer_id = pos_customers.pos_customer_id
		
";

$select_sql = "SELECT *
	FROM sales_invoices ";

	
	
	//ok here is the saved search part....
	
	//$saved_session_search_name = 'posv1_saved_customer_search';
	$search_parameters = getSearchCriteriaFromGetPostSessionData($table_name, $table_def ,$saved_session_search_name);
	$select_sql .= $search_parameters['search_add_string'];
	
	$select_sql .= ' ORDER BY invoice_date DESC, invoice_time DESC';
	//echo $select_sql;
	//$select_sql .= ' LIMIT 30 ';
	
	
	//if a search is set how do we know??? search is set if any column in the table_def has a value....
	// search reset kills the values in the session....also removes all get values from the url...
	//only search if there is a search set....
	
	if(checkIfSearchIsSet($table_name, $table_def, $saved_session_search_name))
	{
		
		$return_array = array();
		$dbc = openPOSdb();
		$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=10000');
		$result = runTransactionSQL($dbc,$tmp_table_sql);
		$return_array['data'] = getTransactionSQL($dbc,$select_sql);
		//this needs to get to javascript
		$return_array['get_post_session_params'] = $search_parameters['get_post_session_params'];
		closeDB($dbc);	
		return($return_array);
	}
	else
	{
		$return_array = array();
		$return_array['data'] =  array();
		$return_array['get_post_session_params'] = array();
		return($return_array);
	}
	
}
function createCustomerInvoiceSearchColDef()
{
		
		//_row onclick start new invoice?
		//disable?
		
		$col_def = array(
					array('db_field' => 'pos_sales_invoice_id',
					'caption' => 'Invoice ID',
					'type' => 'link',
					'get_url_link' => "retail_sales_invoice.php?type=view",
					// no url_caption then use the data 'url_caption' => 'View',
					'get_id_link' => 'pos_sales_invoice_id',
					'search' => 'EXACT'
					),	
					array('db_field' => 'pos_customer_id_for_invoice',
					'type' => 'button',
					'button_caption' => 'New Invoice Using Customer',
					'properties' =>array("onclick" => 'function(){NewInvoiceFromCustomer(this);}')
					
					),	
						array('db_field' => 'pos_sales_invoice_id_for_return',
					'type' => 'button',
					'button_caption' => 'Return Items',
					'properties' =>array("onclick" => 'function(){newReturnInvoice(this);}')
					
					),	
					
					
					/*//last name phone email all in one?
					array('db_field' => 'customer_info',
					'caption' => 'Customer Name Phone Email',
					'type' => 'innerHTML',
					'search' => 'ANY',
					//'th_width' => '100px',
					'properties' => array(
											)
					),*/
					array('db_field' => 'first_name',
					'caption' => 'First Name',
					'type' => 'innerHTML',
					'search' => 'ANY',
					//'th_width' => '100px',
					'properties' => array(
											)
					),
					array('db_field' => 'last_name',
					'caption' => 'Last Name',
					'type' => 'innerHTML',
					'search' => 'ANY',
					//'th_width' => '100px',
					'properties' => array(
											)
					),
					array('db_field' => 'phone',
					'caption' => 'Phone',
					'type' => 'innerHTML',
					'search' => 'ANY',
					//'th_width' => '100px',
					'properties' => array(
											)
					),
					array('db_field' => 'email1',
					'caption' => 'Email',
					'type' => 'innerHTML',
					'search' => 'ANY',
					//'th_width' => '100px',
					'properties' => array(
											)
					),
					array(
						'caption' => 'Follow Up',
						'db_field' => 'follow_up',
						'type' => 'checkbox',
						'search' => 'EXACT',
						'th_width' => '10px',),
					array(
						'caption' => 'Invoice Date',
						'db_field' => 'invoice_date',
						'type' => 'date',
						'search' => 'does not matter what this says',
						//'th_width' => '100px',
						),
					array(
						'caption' => 'Time',
						'db_field' => 'invoice_time',
						'type' => 'input',
						//'th_width' => '100px',
						),
						
						
					array(
						'caption' => 'Status',
						'db_field' => 'invoice_status',
						'type' => 'input',
						//'th_width' => '100px',
						),		
					array(
						'caption' => 'Closed<br>Total',
						'db_field' => 'closed_sum_extension',
						'total' => 2,
						'round' => 2,
						'type' => 'input',
						//'th_width' => '100px',
						),	
					array('db_field' => 'invoice_contents',
					'caption' => 'Invoice Contents',
					'td_tags' => array('className'=>'"cust_table_small"'),
					'type' => 'innerHTML',
					'search' => 'LIKE',
					//'th_width' => '100px',
					'properties' => array(
											)
					),
					array('db_field' => 'pos_customer_id',
					'caption' => 'Customer <br> ID',
					'type' => 'link',
					'get_url_link' => POS_ENGINE_URL . "/customers/view_customer.php",
					'url_caption' => 'View Customer',
					'get_id_link' => 'pos_customer_id',
					),	
					array('db_field' => 'full_name',
					'type' => 'hidden',
					),
		
			);
		return $col_def;
} 
function createStoreInvoiceSearchColDef()
{
		
		//_row onclick start new invoice?
		//disable?
		
		$col_def = array(
					array('db_field' => 'pos_sales_invoice_id',
					'caption' => 'Invoice ID',
					'type' => 'link',
					'get_url_link' => "retail_sales_invoice.php?type=view",
					// no url_caption then use the data 'url_caption' => 'View',
					'get_id_link' => 'pos_sales_invoice_id',
					'search' => 'EXACT'
					),	
					array(
						'caption' => 'Invoice Date',
						'db_field' => 'invoice_date',
						'type' => 'date',
						'search' => 'does not matter what this says',
						//'th_width' => '100px',
						),
					array(
						'caption' => 'Time',
						'db_field' => 'invoice_time',
						'type' => 'input',
						//'th_width' => '100px',
						),
					array(
						'caption' => 'Status',
						'db_field' => 'invoice_status',
						'type' => 'input',
						//'th_width' => '100px',
						),
					array(
						'caption' => 'Draft<br>Total',
						'db_field' => 'draft_sum_extension',
						'total' => 2,
						'round' => 2,
						'type' => 'input',
						//'th_width' => '100px',
						),			
					array(
						'caption' => 'Closed<br>Total',
						'db_field' => 'closed_sum_extension',
						'total' => 2,
						'round' => 2,
						'type' => 'input',
						),	
	array(
			'caption' => 'Tax Collected',
			'db_field' => 'total_tax',
			'type' => 'input',
			'round' => 2,
			'total' => 2),
	array(
			'caption' => 'Gift Cards <br> Sold',
			'db_field' => 'gift_cards_sold',
			'type' => 'input',
			'round' => 2,
			'total' => 2),
		array(
			'caption' => 'Pre Tax Promotions<br>(In-Store Info Only)',
			'db_field' => 'pre_tax_promotion_total',
			'type' => 'input',
			'round' => 2,
			'total' => 2),	
		array(
			'caption' => 'Post Tax Promotions<BR>(payment)',
			'db_field' => 'post_tax_promotion_total',
			'type' => 'input',
			'round' => 2,
			'total' => 2),	
		
		array(
			'caption' => 'Visa/MC<BR>Discover',
			'db_field' => 'credit',
			'type' => 'input',
			'round' => 2,
			'total' => 2),
		array(
			'caption' => 'American<BR>Express',
			'db_field' => 'amex',
			'type' => 'input',
			'round' => 2,
			'total' => 2),	
		
		array(
			'caption' => 'Cash',
			'db_field' => 'cash',
			'type' => 'input',
			'round' => 2,
			'total' => 2),
		array(
			'caption' => 'Check',
			'db_field' => 'lcheck',
			'type' => 'input',
			'round' => 2,
			'total' => 2),
		array(
			'caption' => 'Gift Card <br> Redeemed',
			'db_field' => 'gift_card',
			'type' => 'input',
			'round' => 2,
			'total' => 2),
		array(
			'caption' => 'Store Credit <br> redeemed',
			'db_field' => 'store_credit',
			'type' => 'input',
			'round' => 2,
			'total' => 2),
		
		array(
			'caption' => 'Total Payment',
			'db_field' => 'total_payment',
			'type' => 'input',
			'round' => 2,
			'total' => 2),
					
					
		
			);
		return $col_def;
} 
?>