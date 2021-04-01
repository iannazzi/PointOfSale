<?php
/*
	*View_manufacturers.php
	*shows a list of all registered manufacturers
*/


//exit();


$page_title = 'View Purchase Orders';
$binder_name = 'Purchase Orders';
$access_type = 'READ';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);


$html = '';//'<h1>Broken - fixing....please do not use</h1>';
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
//$html .= '<p><input class = "button" type="button" style="width:200px;" name="create_po" value="List Broken Purchase Orders" onclick="open_win(\'list_broken_purchase_orders.php\')"/>';

$html .= '<p><input class = "button" type="button" style="width:350px;background:red;" name="create_po" value="CALL THE POLICE: Open Purchase Orders Past Cancel Date" onclick="open_win(\'list_purchase_orders.php?purchase_order_status=OPEN&cancel_date_start_date=1900-01-01&cancel_date_end_date='.date('Y-m-d').'&search=Search\')"/>';
$html .= '<input class = "button" type="button" style="width:350px;background:rgb(125,255,125);" name="create_po" value="WHERE IS MY INOIVE!" onclick="open_win(\'list_purchase_orders.php?pos_purchase_order_id=&purchase_order_number=&purchase_order_status=CLOSED&ordered_status=&received_status=&invoice_status=INCOMPLETE&pos_category_id=false&delivery_date_start_date=&delivery_date_end_date=&received_date_start_date=&received_date_end_date=&cancel_date_start_date=&cancel_date_end_date=&po_title=&company=&brand_name=&poc_comments=&style_numbers=&search=Search&sort=delivery_date&order=ASC\')"/></p>';



//$html .= '<input class = "button" type="button" style="width:200px;" name="create_return" value="Create Purchase Return" onclick="open_win(\'../CreatePurchaseReturn/select_manufacturer_brand_for_return.php\')"/>';
$html .= createUserButton('Purchases Journal');

$html .= '</p>';

$search_fields = array(		
							array(	'db_field' => 'pos_purchase_order_id',
											'mysql_search_result' => 'pos_purchase_order_id',
											'caption' => 'Purchase<BR>Order<BR>ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_purchase_order_id')
										),
								array(	'db_field' => 'purchase_order_number',
											'mysql_search_result' => 'purchase_order_number',
											'caption' => 'Purchase<BR>Order<BR>Number',	
											'type' => 'input',
											'html' => createSearchInput('purchase_order_number')
										),
								array(	'db_field' => 'purchase_order_status',
											'mysql_search_result' => 'purchase_order_status',
											'caption' => 'Purchase Order Status',
											'type' => 'input',
											'html' => createSearchInput('purchase_order_status')
										),

								array(	'db_field' => 'ordered_status',
											'mysql_search_result' => 'ordered_status',
											'caption' => 'Ordered Status',
											'type' => 'input',
											'html' => createSearchInput('ordered_status')
										),
								array(	'db_field' => 'received_status',
											'mysql_search_result' => 'received_status',
											'caption' => 'Received Status',
											'type' => 'input',
											'html' => createSearchInput('received_status')
										),	
								array(	'db_field' => 'invoice_status',
											'mysql_search_result' => 'invoice_status',
											'caption' => 'Invoice <br> Status',
											'type' => 'input',
											'html' => createSearchInput('invoice_status'),
								
										),
								array(	'db_field' => 'category_name',
											'mysql_search_result' => 'category_name',
											'caption' => 'PO Category',	
											'type' => 'input',
											'html' => createSearchInput('category_name'),
								),
									
								array(	'db_field' => 'delivery_date',
											'mysql_search_result' => 'delivery_date',
											'caption' => 'Delivery Date Start',
											'type' => 'start_date',
											'html' => dateSelect('delivery_date_start_date',valueFromGetOrDefault('delivery_date_start_date'))
										),
								array(	'db_field' => 'delivery_date',
											'mysql_search_result' => 'delivery_date',
											'caption' => 'Delivery Date End',	
											'type' => 'end_date',
											'html' => dateSelect('delivery_date_end_date',valueFromGetOrDefault('delivery_date_end_date'))
											),
								array(	'db_field' => 'received_date',
											'mysql_search_result' => 'received_date',
											'caption' => 'Received Date Start',
											'type' => 'start_date',
											'html' => dateSelect('received_date_start_date',valueFromGetOrDefault('received_date_start_date'))
										),
								array(	'db_field' => 'received_date',
											'mysql_search_result' => 'received_date',
											'caption' => 'Received Date End',	
											'type' => 'end_date',
											'html' => dateSelect('received_date_end_date',valueFromGetOrDefault('received_date_end_date'))
										),
								array(	'db_field' => 'cancel_date',
											'mysql_search_result' => 'cancel_date',
											'caption' => 'Cancel Date Start',
											'type' => 'start_date',
											'html' => dateSelect('cancel_date_start_date',valueFromGetOrDefault('cancel_date_start_date'))
										),
								array(	'db_field' => 'cancel_date',
											'mysql_search_result' => 'cancel_date',
											'caption' => 'Cancel Date',	
											'type' => 'end_date',
											'html' => dateSelect('cancel_date_end_date',valueFromGetOrDefault('cancel_date_end_date'))
										),
								array(	'db_field' => 'po_title',
											'mysql_search_result' => 'po_title',
											'caption' => 'Title',
											'type' => 'input',
											'html' => createSearchInput('po_title')
										),
								array(	'db_field' => 'company',
											'mysql_search_result' => 'company',
											'caption' => 'Manufacturer',
											'type' => 'input',
											'html' => createSearchInput('company')
										),
								array(	'db_field' => 'brand_name',
											'mysql_search_result' => 'brand_name',
											'caption' => 'Brand',
											'type' => 'input',
											'html' => createSearchInput('brand_name')
										),
								/*array(	'db_field' => 'calculated_po_total',
											'mysql_search_result' => ' (SELECT ROUND(sum(cost*quantity_ordered),2) FROM pos_purchase_order_contents WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id)',
											'caption' => 'Calculated Total',
											'type' => 'input',
											'html' => createSearchInput('calculated_po_total')
										),*/
								
								
								array(	'db_field' => 'poc_comments',
											'mysql_search_result' => 'poc_comments',
											'caption' => 'Purchase Order <br> Contents Comments',
											'type' => 'input',
											'html' => createSearchInput('poc_comments'),
								
										),
								array(	'db_field' => 'style_numbers',
											'mysql_search_result' => 'style_numbers',
											'caption' => 'Purchase Order <br> Style Numbers',
											'type' => 'input',
											'html' => createSearchInput('style_numbers'),
								
										),
								
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
			'sort' => 'pos_purchase_order_id'),
		array(
			'th' => 'PO<br>Number',
			'mysql_field' => 'purchase_order_number',
			'mysql_search_result' => 'pos_purchase_orders',
			'sort' => 'purchase_order_number'),
		array(
			'th' => 'PO<br>Category',
			'mysql_field' => 'category_name',
			'mysql_search_result' => 'category_name',
			'sort' => 'category_name'),
		array(
			'th' => 'Ordered Status',
			'mysql_field' => 'ordered_status',
			'mysql_search_result' => 'pos_purchase_orders',
			'sort' => 'ordered_status'),
		array(
			'th' => 'Purchase<br>Order<br>Status',
			'mysql_field' => 'purchase_order_status',
			'mysql_search_result' => 'pos_purchase_orders',
			'sort' => 'purchase_order_status'),
		array(
			'th' => 'Received Status',
			'mysql_field' => 'received_status',
			'mysql_search_result' => 'received_status',
			'sort' => 'received_status'
			),
			
		array(
			'th' => 'Invoice<br>Status',
			'mysql_field' => 'invoice_status',
			'mysql_search_result' => 'invoice_status',
			'sort' => 'invoice_status'
			),
		array(
			'th' => 'Delivery<br>Date',
			'mysql_field' => 'delivery_date',
			'mysql_search_result' => 'pos_purchase_orders',
			'sort' => 'delivery_date'),
		array(
			'th' => 'Cancel<br>Date',
			'mysql_field' => 'cancel_date',
			//'month_day' => 1,
			'mysql_search_result' => 'pos_purchase_orders',
			'sort' => 'cancel_date'),
		array(
			'th' => 'Received<br>Date',
			'mysql_field' => 'received_date',
			//'month_day' => 1,
			'mysql_search_result' => 'pos_purchase_orders',
			'sort' => 'received_date'),
		array(
			'th' => 'Title',
			'mysql_field' => 'po_title',
			'mysql_search_result' => 'pos-purchase_orders',
			'sort' => 'po_title'),
				
		array(
			'th' => 'Manufacturer',
			'mysql_field' => 'company',
			'mysql_search_result' => 'company',
			'sort' => 'company'),			
		array(
			'th' => 'Brand',
			'mysql_field' => 'brand_name',
			'mysql_search_result' => 'pos_manufacturer_brands',
			'sort' => 'brand_name'
			),
		array(
			'th' => 'Calculated<br>Cost',
			'mysql_field' => 'calculated_po_total',
			'mysql_search_result' => 'calculated_po_total',
			'total' => 0,
			'sort' => 'calculated_po_total'
			),
		
		
			array(
			'th' => 'Total<br>Received',
			'mysql_field' => 'received_total',
			'mysql_search_result' => 'received_total',
			'total' => 0,
			'sort' => 'received_total'
			),
		/*array(
			'th' => 'Invoice<br>Number(s)',
			'mysql_field' => 'invoice_number',
			'mysql_search_result' => 'invoice_number',
			'sort' => 'invoice_number'
			),
		array(
			'th' => 'Goods On <br>Invoice',
			'mysql_field' => 'invoice_amount',
			'mysql_search_result' => 'invoice_amount',
			'round' => 2,
			'total' => 0,
			'sort' => 'invoice_amount'
			),
		array(
			'th' => 'Shipping<br>Amount',
			'mysql_field' => 'shipping_amount',
			'mysql_search_result' => 'shipping_amount',
			'round' => 2,
			'total' => 0,
			'sort' => 'shipping_amount'
			),*/
		
		);



//instead of a re-direct this should pull and store in the session...
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_purchase_orders_url');

$action = 'list_purchase_orders.php';
$html .= createSearchForm($search_fields,$action);
$sql = "
CREATE TEMPORARY TABLE purchase_orders

SELECT  pos_purchase_orders.pos_purchase_order_id, pos_purchase_orders.pos_category_id, pos_purchase_orders.purchase_order_number, pos_purchase_orders.purchase_order_status,  pos_purchase_orders.received_status, pos_purchase_orders.ordered_status, pos_purchase_orders.invoice_status, date(pos_purchase_orders.received_date) as received_date_old, 
(SELECT ROUND(sum((cost-discount)*(quantity_ordered - quantity_canceled)),2) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as calculated_po_total, 


 (SELECT coalesce(round(sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)),2),0) FROM pos_purchase_order_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as received_total,

 (SELECT max(date(receive_date)) FROM pos_purchase_order_receive_event
			
			WHERE pos_purchase_order_receive_event.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) 
			
			 as received_date,

(SELECT group_concat(comments) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as poc_comments, 
(SELECT group_concat(style_number) FROM pos_purchase_order_contents  WHERE pos_purchase_order_contents.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as style_numbers, 
pos_purchase_orders.delivery_date, pos_purchase_orders.cancel_date, pos_purchase_orders.po_title, 
(SELECT pos_manufacturers.company FROM pos_manufacturers WHERE pos_manufacturers.pos_manufacturer_id = pos_manufacturer_brands.pos_manufacturer_id) AS company, 
(SELECT GROUP_CONCAT(pos_purchases_journal.invoice_number) FROM pos_purchases_journal
LEFT JOIN pos_purchases_invoice_to_po
ON pos_purchases_journal.pos_purchases_journal_id = pos_purchases_invoice_to_po.pos_purchases_journal_id
WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as invoice_number,

(SELECT sum(pos_purchases_journal.invoice_amount) FROM pos_purchases_journal
LEFT JOIN pos_purchases_invoice_to_po
ON pos_purchases_journal.pos_purchases_journal_id = pos_purchases_invoice_to_po.pos_purchases_journal_id
WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id AND pos_purchases_journal.invoice_type = 'Regular') - (SELECT sum(pos_purchases_journal.invoice_amount) FROM pos_purchases_journal
LEFT JOIN pos_purchases_invoice_to_po
ON pos_purchases_journal.pos_purchases_journal_id = pos_purchases_invoice_to_po.pos_purchases_journal_id
WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id AND pos_purchases_journal.invoice_type = 'Credit Memo') AS invoice_amount,


(SELECT sum(pos_purchases_journal.shipping_amount) FROM pos_purchases_journal
LEFT JOIN pos_purchases_invoice_to_po
ON pos_purchases_journal.pos_purchases_journal_id = pos_purchases_invoice_to_po.pos_purchases_journal_id
WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id) as shipping_amount,
pos_manufacturer_brands.pos_manufacturer_id,pos_manufacturer_brands.brand_name,
pos_categories.name as category_name
    FROM pos_purchase_orders
    LEFT JOIN pos_manufacturers 
    ON pos_purchase_orders.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id
    LEFT JOIN pos_manufacturer_brands 
    ON pos_purchase_orders.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
    LEFT JOIN pos_categories ON pos_purchase_orders.pos_category_id = pos_categories.pos_category_id
	WHERE purchase_order_status != 'DELETED'

;
	";
	
$tmp_select_sql = "SELECT *
	FROM purchase_orders WHERE 1";
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .=  $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($list_purchase_order_table_columns, $list_purchase_order_table_columns[4]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";
//now make the table
if (isset($_GET['search']))
{
	
	$dbc = openPOSdb();
	//preprint($tmp_select_sql);
	$result = runTransactionSQL($dbc,$sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html .= createRecordsTableWithTotals($data, $list_purchase_order_table_columns);
	
	
}
$html .= '<script>document.getElementsByName("pos_purchase_order_id")[0].focus();</script>';
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>
