<?php
/*
	*View_manufacturers.php
	*shows a list of all registered manufacturers
*/
$page_title = 'View Purchase Orders';
$binder_name = 'Purchase Orders';
$access_type = 'READ';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);


$html = '';//includeJavascriptLibrary();

//if there is a message print it
if (isset($_GET['added']))
{
	if ($_GET['added'] == 'error')
	{
		$html .= '<h2 class = "error"> ERROR - Duplicate MFG Name or Brand Code or Name Detected</h2>';
	}
	else 
	{
		$html .= '<h2 class = "error">' . $_GET['added'] . ' is now added</h2>';
	}
}
if (isset($_GET['updated']))
{
	$html .= '<h2 class = "error">' . $_GET['updated'] . ' is now updated</h2>';
}
if (isset($_GET['message']))
{
	$html .= '<h2 class = "error">' . urldecode($_GET['message']) . '</h2>';
}

//$html .= '<p><a href="../CreatePurchaseOrder/po_overview.php">Create A Purchase Order</a></p>';
$html .= '<p><input class = "button" type="button" style="width:200px;" name="create_po" value="Create Purchase Order" onclick="open_win(\'../CreatePurchaseOrder/create_purchase_order.php\')"/>';
//$html .= '<input class = "button" type="button" style="width:200px;" name="create_return" value="Create Purchase Return" onclick="open_win(\'../CreatePurchaseReturn/select_manufacturer_brand_for_return.php\')"/>';
$html .= createUserButton('Purchases Journal');

$html .= '</p>';

$search_fields = array(		
							array(	'db_field' => 'pos_purchase_order_id',
											'mysql_search_result' => 'pos_purchase_orders.pos_purchase_order_id',
											'caption' => 'Purchase<BR>Order<BR>ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_purchase_order_id')
										)
								);
$list_purchase_order_table_columns = array(
		 array(
			'th' => 'View',
			'mysql_field' => 'pos_purchase_order_id',
			'get_url_link' => "../ViewPurchaseOrder/view_purchase_order.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_purchase_order_id'),
		/* array(
			'th' => 'Copy',
			'mysql_field' => 'pos_purchase_order_id',
			'get_url_link' => "../CopyPurchaseOrder/copy_purchase_order.php",
			'url_caption' => 'Copy',
			'get_id_link' => 'pos_purchase_order_id'),*/
		array(
			'th' => 'System<br>ID',
			'mysql_field' => 'pos_purchase_order_id',
			'mysql_search_result' => 'pos_purchase_order_id',
			'sort' => 'pos_purchase_orders.pos_purchase_order_id'),
				array(
			'th' => 'Status',
			'mysql_field' => 'purchase_order_status',
			'mysql_search_result' => 'purchase_order_status',
			'sort' => 'purchase_order_status')

		);

$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_broken_purchase_orders_url');

$action = 'list_broken_purchase_orders.php';
$html .= createSearchForm($search_fields,$action);
$sql = "

SELECT  DISTINCT pos_purchase_orders.pos_purchase_order_id, purchase_order_status

    FROM pos_purchase_orders
    LEFT JOIN pos_purchase_order_contents USING (pos_purchase_order_id) 
    
	WHERE pos_purchase_order_contents.pos_product_sub_id = 0 AND (purchase_order_status = 'OPEN' OR purchase_order_status = 'CLOSED' OR purchase_order_status = 'PREPARED')

	";
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$sql  .=  $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($list_purchase_order_table_columns, $list_purchase_order_table_columns[0]['mysql_field']);
$sql  .=  " ORDER BY $order_by";
//now make the table

	$html .= createRecordsTableWithTotals(getSQL($sql), $list_purchase_order_table_columns);

$html .= '<script>document.getElementsByName("pos_purchase_order_id")[0].focus();</script>';
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>
