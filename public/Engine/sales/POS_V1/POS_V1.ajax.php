<?

$access_type = 'WRITE';
require_once('retail_sales_invoice_functions.php');
require_once('POS_V1.inc.php');
//we checked login access however there may need to be other checks....

$ajax_request = (ISSET($_GET['ajax_request'])) ? $_GET['ajax_request'] : $_POST['ajax_request'];

if(strtoupper($ajax_request) == 'CUSTOMER_SEARCH')
{
	//do i need other access checks here? terminal open....
	
	//get and clean the data from the search form..... which is what? from the table def...
	//retreive data...
	//send data
	$customer_table_name = 'customer_table';
	$customer_col_def = createCustomerInvoiceSearchColDef($customer_table_name);
	//this gets and sets the search results
	$cust_contents = getSetCustomerSearchResults($customer_table_name,$customer_col_def, 'posv1_saved_customer_search');
	//preprint($_POST);
	//we dont want any stored search with this result... as javascript is doing that for us
	echo json_encode($cust_contents['data']);
	
}
if(strtoupper($ajax_request) == 'INVOICE_SEARCH')
{
	//do i need other access checks here? terminal open....
	
	//get and clean the data from the search form..... which is what? from the table def...
	//retreive data...
	//send data
	$store_invoice_table_name = 'store_invoice_table';
	$store_invoice_col_def = createStoreInvoiceSearchColDef($store_invoice_table_name);
	$invoice_contents = getSetCustomerSearchResults($store_invoice_table_name, $store_invoice_col_def,'posv1_saved_store_invoice_search');
	//preprint($_POST);
	//we dont want any stored search with this result... as javascript is doing that for us
	echo json_encode($invoice_contents['data']);
	
}
elseif (strtoupper($ajax_request) == 'CUSTOMER_SEARCH_RESET')
{
	//do i need other access checks here? terminal open....
	
	$saved_search = 'posv1_saved_customer_search';
	$customer_table_name = 'customer_table';
	$customer_col_def = createCustomerInvoiceSearchColDef($customer_table_name);
	eraseSessionSavedSearch($customer_col_def, $saved_search);
	echo $saved_search . ' was erased.';
}
elseif (strtoupper($ajax_request) == 'INVOICE_SEARCH_RESET')
{
	//do i need other access checks here? terminal open....
	
	$saved_search = 'posv1_saved_store_invoice_search';
	$store_invoice_table_name = 'store_invoice_table';
	$store_invoice_col_def = createStoreInvoiceSearchColDef($store_invoice_table_name);
	eraseSessionSavedSearch($store_invoice_col_def, $saved_search);
	echo $saved_search . ' was erased.';
}
elseif (strtoupper($ajax_request) == 'CHECK_LOGIN')
{
	$dbc = openPOSdb();
	list ($check, $data) = check_login($dbc, $_POST['user'], $_POST['password']);
	closeDB($dbc);
	if($check)
	{
		echo $data['pos_user_id'];
	}
	else
	{
		echo false;
	}
}


?>