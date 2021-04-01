<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 10-23-12
	
*/
$binder_name = 'Customers';
$access_type = 'READ';
require_once ('customer_functions.php');

$complete_location = 'list_customers.php';
$cancel_location = 'list_customers.php?message=Canceled';
$pos_customer_id = getPostOrGetID('pos_customer_id');
$ref = POS_ENGINE_URL.'/customers/view_customer.php?pos_customer_id='.$pos_customer_id;
$customer_data = getCustomerData($pos_customer_id);
$edit_location = 'add_edit_customer.php?pos_customer_id='.$pos_customer_id.'&type=edit';
$delete_location = 'delete_customer.form.handler.php?pos_customer_id='.$pos_customer_id;
$page_title = 'Customer ' . $pos_customer_id . ': ' . $customer_data[0]['first_name'] . ' ' . $customer_data[0]['last_name'];

$db_table = 'pos_customers';
$key_val_id['pos_customer_id']  = $pos_customer_id;
$data_table_def = createCustomerTableDef('View', $pos_customer_id);
$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);




$html = printGetMessage('message');
$html .= '<p>View Customer</p>';

$html .= createHTMLTableForMYSQLData($table_def_w_data);
if (checkWriteAccess($binder_name))
{
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
}
$html .= '</p>';

$html .= '<div class = "mysql_table_divider">';
$html .= '<h3>Invoices Linked To This Customer</h3>';	
$html .= createCustomerInvoiceRecordTable($pos_customer_id);
$html .= '</div>';
	

$html .= '<div class = "mysql_table_divider">';
$html .= '<h3>Addresses</h3>';
$html .= createCustomerAddressRecordTable($pos_customer_id, $ref);
if (checkWriteAccess($binder_name))
{
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Add Address" onclick="open_win(\'' . POS_ENGINE_URL .'/office/address/address.php?ref='.$ref .'&type=add&pos_customer_id='.$pos_customer_id. '\')"/>';
}
$html .= '</div>';
/*
$html .= '<div class = "mysql_table_divider">';
$html .= '<h3>Email Addresses</h3>';
$html .= createEmailAddressRecordTable($pos_customer_id, $ref);
if (checkWriteAccess($binder_name))
{
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Add Email" onclick="open_win(\'' . POS_ENGINE_URL .'/office/email/email.php?ref='.$ref .'&type=add&pos_customer_id='.$pos_customer_id. '\')"/>';
}
$html .= '</div>';
*/

$html .= '<div class = "mysql_table_divider">';
$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Customer Database" onclick="window.location = \''.$complete_location.'\'" />';
$html .= '</p>';
$html .= '</div>';


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createCustomerAddressRecordTable($pos_customer_id, $ref)
{
	$tmp_sql = "
	
			CREATE TEMPORARY TABLE addresses

			SELECT pos_addresses.*, pos_states.short_name FROM pos_addresses 
			LEFT JOIN pos_customer_addresses USING (pos_address_id)
			LEFT JOIN pos_states ON pos_addresses.pos_state_id = pos_states.pos_state_id
			LEFT JOIN pos_counties USING (pos_county_id)
			WHERE pos_customer_addresses.pos_customer_id = $pos_customer_id
			;";
				
	$tmp_select_sql = "SELECT * FROM addresses WHERE 1";			
		$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_address_id',
			'get_url_link' => POS_ENGINE_URL . '/office/address/address.php?ref='.$ref .'&type=view&pos_customer_id='.$pos_customer_id,
			'url_caption' => 'view',
			'get_id_link' => 'pos_address_id'),
		array(
			'th' => 'System ID',
			'mysql_field' => 'pos_address_id'),
		array(
			'th' => 'Address1',
			'mysql_field' => 'address1',
			'sort' => 'address1'),
		array(
			'th' => 'Address2',
			'mysql_field' => 'address2',
			'sort' => 'address2'),	
		array(
			'th' => 'City',
			'mysql_field' => 'city',
			'sort' => 'city'),
		array(
			'th' => 'State',
			'mysql_field' => 'short_name',
			'sort' => 'short_name'),	
		array(
			'th' => 'zip',
			'mysql_field' => 'zip',
			'sort' => 'zip'),
	
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html = createRecordsTable($data, $table_columns);
	return $html;

}
function createEmailAddressRecordTable($pos_customer_id, $ref)
{
	$tmp_sql = "
	
			CREATE TEMPORARY TABLE email

			SELECT pos_email_addresses.*
			FROM pos_email_addresses
			LEFT JOIN pos_customer_emails USING (pos_email_address_id)
			WHERE pos_customer_emails.pos_customer_id = $pos_customer_id
			;";
				
	$tmp_select_sql = "SELECT * FROM email WHERE 1";			
		$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_email_address_id',
			'get_url_link' => POS_ENGINE_URL . '/office/email/email.php?ref='.$ref .'&type=view&pos_customer_id='.$pos_customer_id,
			'url_caption' => 'view',
			'get_id_link' => 'pos_email_address_id'),
		array(
			'th' => 'System ID',
			'mysql_field' => 'pos_email_address_id'),
		array(
			'th' => 'email',
			'mysql_field' => 'email',
			'sort' => 'email')
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html = createRecordsTable($data, $table_columns);
	return $html;

}
function createCustomerInvoiceRecordTable($pos_customer_id)
{
$tmp_sql = "
CREATE TEMPORARY TABLE sales_invoices

SELECT  
		pos_sales_invoice.pos_sales_invoice_id,
		pos_sales_invoice.invoice_number,
		date(pos_sales_invoice.invoice_date) as invoice_date,
	
		
";
$tmp_sql .= getInvoiceSumsSQL();
$tmp_sql .="
		
		, (SELECT GROUP_CONCAT(CONCAT_WS(' ', pos_sales_invoice_contents.title, pos_sales_invoice_contents.brand_name, style_number, color_code, size) SEPARATOR '<BR>') FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as items,
		
		
		
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
		WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as product_description
		
		
		FROM pos_sales_invoice
		
		WHERE pos_customer_id = $pos_customer_id



		


;


";
$tmp_select_sql = "SELECT *
	FROM sales_invoices WHERE 1";			
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_sales_invoice_id',
			'get_url_link' => POS_ENGINE_URL . "/sales/POS_V1/retail_sales_invoice.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_sales_invoice_id'),

		array(
			'th' => 'ID',
			'mysql_field' => 'pos_sales_invoice_id',
			'sort' => 'pos_sales_invoice_id'),	
		array(
			'th' => 'Invoice<br>Number',
			'mysql_field' => 'invoice_number',
			'sort' => 'invoice_number'),
		array(
			'th' => 'Invoice<br> Date',
			'mysql_field' => 'invoice_date',
			'sort' => 'invoice_date'),
		array(
			'th' => 'product_description',
			'mysql_field' => 'product_description',
			'sort' => 'product_description'),
	
			array(
			'th' => 'Gift<br> Cards',
			'mysql_field' => 'credit_cards_sold',
			'sort' => 'credit_cards_sold',
			'round' => 0,
			'total' => 0),
		array(
			'th' => 'Total <br>Goods & <br>Services',
			'mysql_field' => 'total_goods_and_service',
			'sort' => 'total_goods_and_service',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Returns',
			'mysql_field' => 'returns',
			'sort' => 'returns',
			'round' => 2,
			'total' => 2),
			array(
			'th' => 'Gross <br> Sales',
			'mysql_field' => 'gross',
			'sort' => 'gross',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Total',
			'mysql_field' => 'grande_total',
			'sort' => 'grande_total',
			'round' => 2,
			'total' => 2)

		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=10000');
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html = createRecordsTableWithTotals($data, $table_columns);
	return $html;

}
?>