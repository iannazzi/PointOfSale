<?php
/*
	*select customer comes from the sales invoice.
	there will be a return url when completed with the process.
	if a cusomter id comes over we want to populate the data with that user...
*/
$binder_name = 'Sales Invoices';
$access_type = 'READ';
$page_title = 'Select a Customer';
require_once ('../sales_functions.php');
$complete_location = urldecode(getPostOrGetValue('complete_location'));
 




$search_fields = array(				array(	'db_field' => 'pos_customer_id',
											'mysql_search_result' => 'pos_customer_id',
											'caption' => 'Customer ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_customer_id')
										),
										
										array(	'db_field' => 'first_name',
											'mysql_search_result' => 'first_name',
											'caption' => 'First Name',	
											'type' => 'input',
											'html' => createSearchInput('first_name')
										),
										array(	'db_field' => 'last_name',
											'mysql_search_result' => 'last_name',
											'caption' => 'Last Name',	
											'type' => 'input',
											'html' => createSearchInput('last_name')
										),
										array(	'db_field' => 'email1',
											'mysql_search_result' => 'email1',
											'caption' => 'Email',	
											'type' => 'input',
											'html' => createSearchInput('email1')
										),
										array(	'db_field' => 'phone',
											'mysql_search_result' => 'phone',
											'caption' => 'Phone',	
											'type' => 'input',
											'html' => createSearchInput('phone')
										),
										array(	'db_field' => 'comments',
											'mysql_search_result' => 'comments',
											'caption' => 'Comments',	
											'type' => 'input',
											'html' => createSearchInput('comments')
										)
										);
										
$table_columns = array(
		array(
			'th' => 'Select',
			'select' => 'url?',
			'mysql_field' => 'pos_customer_id'
			),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_customer_id',
			'sort' => 'pos_customer_id'),	
		array(
			'th' => 'First Name',
			'mysql_field' => 'first_name',
			'sort' => 'first_name'),
		array(
			'th' => 'Last Name',
			'mysql_field' => 'last_name',
			'sort' => 'last_name'),
		array(
			'th' => 'Email Address',
			'mysql_field' => 'email1',
			'sort' => 'email1'
			),
		array(
			'th' => 'Phone',
			'mysql_field' => 'phone',
			'sort' => 'phone'),
		array(
			'th' => 'Product Description',
			'mysql_field' => 'product_description',
			'sort' => 'product_description'),
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'sort' => 'comments')
		);
$html = printGetMessage('message');	
//saved search functionality
//$search_set = saveAndRedirectSearchFormUrl($search_fields, 'select_customer_url');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE customers

SELECT  
		pos_customers.pos_customer_id,
		pos_customers.first_name,
		pos_customers.last_name,
		pos_customers.email1,
		pos_customers.phone,
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


		FROM pos_customers


;


";
$tmp_select_sql = "SELECT *
	FROM customers WHERE 1";

//create the search form
$action = 'select_customer.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


$tmp_select_sql  .=  " LIMIT 100";


if (isset($_POST['select']))
{
	$pos_customer_id = 0;
	if(isset($_POST['radio']))
	{
		//this is the one....
		$pos_customer_id = $_POST['radio'];
		//$update['pos_customer_id'] = $pos_customer_id;
		//$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $update);
		header('Location: '. addGetToURL($complete_location,'pos_customer_id='.$pos_customer_id));
		exit();
	}

	
}
else if (isset($_POST['select_edit']))
{
	$pos_customer_id = $_POST['radio'];
	header('Location: add_edit_customer.php?type=EDIT_SELECT&complete_location='.urlencode($complete_location).'&pos_customer_id='.$pos_customer_id);
	exit();
}
else if (isset($_POST['select_address']))
{
	$pos_customer_id = $_POST['radio'];	
	header('Location: address.php?ref='.urlencode($complete_location) .'&type=add&pos_customer_id='.$pos_customer_id);
	
	exit();
}
else if (isset($_POST['cancel']))
{
	header('Location: '. $complete_location);
		exit();
}
else if (isset($_POST['deselect']))
{
		$pos_customer_id = 0;
		//$update['pos_customer_id'] = $pos_customer_id;
		//$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $update);
		header('Location: '. addGetToURL($complete_location,'pos_customer_id='.$pos_customer_id));
		exit();
}
else
{
	//show the default state
	$buttons = '<input class = "button" style="width:150px" type="submit" name="select" value="Select Customer"/>';
	$buttons .= '<input class = "button" style="width:150px" type="submit" name="select_edit" value="Edit Customer"/>';
	$buttons .= '<input class = "button" style="width:150px" type="submit" name="select_address" value="Add Address"/>';
	$buttons .= '<input class = "button" style="width:150px" type="submit" name="cancel" value="Cancel"/>';
}


//create some buttons
//Add a button to add an expense




//now make the table
$html .= createSearchFormWithID($search_fields,$action, array('complete_location' => urlencode($complete_location)));
$html .= '<form action="add_edit_customer.php" method="post">';
$html .= '<p>';
$html .= '<input class = "button"  style="width:300px" type = "submit" name="add_customer" value="Add Customer" />';
$html .= createHiddenInput('complete_location', urlencode($complete_location));
$html .= createHiddenInput('type', 'ADD_SELECT');
$html .= createHiddenInput('first_name', (isset($_GET['first_name']))?$_GET['first_name']:'');
$html .= createHiddenInput('last_name', (isset($_GET['last_name']))?$_GET['last_name']:'');
$html .= createHiddenInput('email1', (isset($_GET['email1']))?$_GET['email1']:'');
$html .= createHiddenInput('phone', (isset($_GET['phone']))?$_GET['phone']:'');
$html .= '</p>';
$html .= '</form>';

$form_handler = 'select_customer.php';
$html .= '<form action="' . $form_handler.'" method="post">';

if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=10000');
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html .= $buttons;
	$html .= createSelectableRecordsTable($data, $table_columns);
	$html .= $buttons;
}
$html .= createHiddenInput('complete_location', urlencode($complete_location));
$html .= '<input class = "button" style="width:150px" type="submit" name="deselect" value="Select No Customer"/>';
$html .= '</form>';

$html .= '<script>document.getElementsByName("first_name")[0].focus();</script>';
include (HEADER_FILE);
echo $html;

include (FOOTER_FILE);
?>
