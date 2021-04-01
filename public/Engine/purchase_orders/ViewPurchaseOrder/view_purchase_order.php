<?php
/*
	view_purchase_order.php
	Craig Iannazzi 3-10-12
	this script will create an un-editable table of the stored purchase order
	
*/
$page_level = 5;
$page_navigation = 'purchase_orders';
$binder_name = 'Purchase Orders';
$access_type = 'READ';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once ('../po_functions.php');
$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
$page_title = 'PO# ' . $pos_purchase_order_id;
if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	
	$html = includesForPOC();
	$html .= '<script src="view_purchase_order.js"></script>';
	$html .= printGetMessage();
	$html .= createPOView($pos_purchase_order_id);
	$html .= createPOCView($pos_purchase_order_id);
	$html .= createPOCByContentTable($pos_purchase_order_id);
	

	
	$html .= createPOReceiveButtons($pos_purchase_order_id);
	$html.= createPORAhtml($pos_purchase_order_id);
	$html.= createPOLog($pos_purchase_order_id);
	$html .= createPOInvoiceButtons($pos_purchase_order_id);
	$html .= createPONavigationButtons($pos_purchase_order_id);
	//want to add a log down here...

	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
else
{
	//no valid  id
	include (HEADER_FILE);
	echo '<p>error - not a valid ID</p>';
	include (FOOTER_FILE);
}

function createPOView($pos_purchase_order_id)
{
	$key_val_id['pos_purchase_order_id'] = $pos_purchase_order_id;
	$db_table = 'pos_purchase_orders';
	$data_table_def = createPOTableDef();
	$data_table_def_with_data = loadDataToTableDef($data_table_def, $db_table, $key_val_id);
	$html = '<h2>Purchase Order Overview</h2>';
	$html .= convertTableDefToHTMLForView($data_table_def_with_data);
	$tags = generatePOCopyEnableTags($pos_purchase_order_id);
	$html .= '<p><INPUT class = "button" type="button"  value="Edit" onclick="window.location = \'../EditPurchaseOrder/edit_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';	
	$html .= '<INPUT class = "button" type="button"  value="Copy" '.$tags. 'onclick="window.location = \'../CopyPurchaseOrder/copy_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	
	$delete_location = '../DeletePurchaseOrder/delete_po.php?pos_purchase_order_id='.$pos_purchase_order_id;
	$html .= confirmDelete($delete_location);
	$html .= '<INPUT class = "button" type="button"  value="Delete" onclick="confirmDelete();" />';
	$html .= 'Can only copy once PO is at least prepared';
	$html .= '</p>';	
	return $html;
}

function createPOCView($pos_purchase_order_id)
{
	$html = '<div class = "tight_divider">';
	$html .= '<h4>This is the original order. The order should not and can not be modify after opening. This will prevent mismatches between company systems. The options to change the order would be to cancel the order,or cancel items off the order. If extra items or wrong items come and it is decided to keep them, a new purchase order will be need to be created for those items. Costs need to be adjusted to match the final pricing on the invoice. Discounts, including show discounts, should be added at time of order.</h4>';

	$html .= '<h2>Purchase Order Contents</h2>';
	$html .= createPOCtable($pos_purchase_order_id, 'true');
	if (getPurchaseOrderStatus($pos_purchase_order_id) == 'INIT' || getPurchaseOrderStatus($pos_purchase_order_id) == 'DRAFT' || getPurchaseOrderStatus($pos_purchase_order_id) == 'PREPARED')
	{
		$tags = '';
	}
	else
	{
		$tags = ' disabled = "disabled" ';
	}
	if(getPurchaseOrderStatus($pos_purchase_order_id) == 'OPEN' || getPurchaseOrderStatus($pos_purchase_order_id) == 'CLOSED')
	{
		$content_edit_tags = '';
	}
	else
	{
		$content_edit_tags = ' disabled = "disabled" ';
	}

	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" '.$tags.' style = "width:150px" value="Edit Contents" onclick="window.location = \'../CreatePurchaseOrder/purchase_order_contents.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';	
	
	
	$open_tags = generateManualSendEnableTags($pos_purchase_order_id);
	$email_tags = generateEmailSendEnableTags($pos_purchase_order_id);
	$ordered_status = getPurchaseOrderOrderedStatus($pos_purchase_order_id);
	if ($ordered_status == 'NOT SUBMITTED')
	{
	}

	//$html .= '<p>Sending and Opening The PO. This PO ordered status is: ' . $ordered_status . '</p>';
	//$html .= '<INPUT class = "email_button" type="button"  ' .$tags.' value="Email PO To: ' . getSalesRepEmailFromPO($pos_purchase_order_id) . '" onclick="window.location = \'../SendPurchaseOrder/confirm_po_email.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	$html .= '<INPUT class = "button" type="button"  ' .$email_tags.' style="width:150px" value="Send PO" onclick="window.location = \'../SendPurchaseOrder/confirm_po_email.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	$html .= '<INPUT class = "button" type="button" ' .$open_tags.' style="width:250px" value="OPEN PO via Print/Fax/PDF Manual Submit" onclick="window.location = \'../CreatePurchaseOrder/open_po.php?pos_purchase_order_id='.$pos_purchase_order_id.'\'" />';
	
$html .= '<INPUT class = "button" type="button" '.$content_edit_tags.' style = "width:100px" value="Edit Costs" onclick="window.location = \'../CreatePurchaseOrder/adjust_pricing.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	$cancel_tags = '';
	$html .= '<INPUT class = "button" style="width:200px" type="button" '.$cancel_tags.' value="Edit Discounts" onclick="window.location = \'../AddDiscount/add_discount_to_order_contents.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	
	$html .= '<INPUT class = "button" type="button" ' .$cancel_tags.'value="Cancel Items" onclick="window.location = \'../ReceivePurchaseOrder/cancel_purchase_order_items.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	
	$html .= '</p>';
	$html .= '</div>';
	
	
	
	
	
	
	
	$html .= '</p>';	
	
	return $html;
}
function createPOCByContentTable($pos_purchase_order_id)
{
	//this is the static view table with all the shinizzles
	$array_table_def= array(	
			
					array(
							'th' => 'Row',
							'db_field' => 'row_number',
							'type' => 'row_number'),
					array(
							'th' => 'PO <br>Content ID',
							'db_field' => 'pos_purchase_order_content_id',
							'type' => 'td_hidden_input'),
					array(
							'th' => 'Product ID',
							'db_field' => 'pos_product_id',
							'type' => 'link',
							'get_url_link' => POS_ENGINE_URL . '/products/ViewProduct/view_product.php',
							'get_id_link' => 'pos_product_id'
							),
					array(
							'th' => 'Product Sub ID',
							'db_field' => 'pos_product_sub_id',
							'type' => 'link',
							'get_url_link' => POS_ENGINE_URL . '/products/ProductSubId/view_product_sub_id.php',
							'get_id_link' => 'pos_product_sub_id'),
					array(	'th' => 'Style Number',
							'db_field' => 'style_number',
							'type' => 'td',
							),
					array(	'th' => 'Title',
							'db_field' => 'title',
							'type' => 'td',
							),
					array(
							'th' => 'Item',
							'db_field' => 'item',
							'type' => 'td'),
				
					array(
							'th' => 'UPC',
							'db_field' => 'product_upc',
							'type' => 'td'),	
					

					
					
					array(	'th' => 'Order<BR>Quantity',
							'db_field' => 'quantity_ordered',
							'type' => 'input',
							'total' => 0,

							'tags' => ' style="background-color:yellow" '),	
							
							
					array(	'th' => 'Order<Br>Adjustment<BR>Quantity',
							'db_field' => 'adjustment_quantity',
							'type' => 'input',
							'total' => 0,
							'round' => 0,
							'tags' => ' style="background-color:yellow" '),			
							
					array(	'th' => 'Cancel<BR>Quantity',
							'db_field' => 'quantity_canceled',
							'type' => 'input',
							'total' => 0,

							'tags' => ' style="background-color:yellow" '),	
					array(	'th' => 'Quantity<br>Received',
							'db_field' => 'received_quantity',
							'type' => 'input',
							'round' => 0,
							'total' => 0,
							'tags' => ' style="background-color:yellow" '),	
					array(	'th' => 'Cost',
							'db_field' => 'cost',
							'type' => 'input',
							'round' => 2,
							'tags' => ' style="background-color:yellow" '),
					array(	'th' => 'Discount',
							'db_field' => 'discount',
							'type' => 'input',
							'round' => 2,
							'tags' => ' style="background-color:yellow" '),		
					/*array(	'th' => 'Discount<br>Quantity',
							'db_field' => 'discount_quantity',
							'type' => 'input',
							'round' => 0,
							'total' => 0,
							'tags' => ' style="background-color:yellow" '),	*/
					array(	'th' => 'Total<br>Discount',
							'db_field' => 'discount_extension',
							'type' => 'input',
							'round' => 2,
							'total' => 2,

							'tags' => ' style="background-color:yellow" '),	
					array(	'th' => 'Ordered<br>Amount',
							'db_field' => 'ordered_amount',
							'type' => 'input',
							'total' => 2,
							'round' => 2,
							'tags' => ' style="background-color:yellow" '),
					
					
					array(	'th' => 'Received<br>Amount',
							'db_field' => 'received_amount',
							'type' => 'input',
							'round' => 2,
							'total' => 2,
							'tags' => ' style="background-color:yellow" '),
					
					);
	$sql = "CREATE TEMPORARY TABLE temp
		SELECT pos_purchase_order_contents.pos_purchase_order_id,	
			pos_purchase_order_contents.pos_purchase_order_content_id,
			pos_purchase_order_contents.cost, 
			pos_purchase_order_contents.discount, 
			pos_purchase_order_contents.adjustment_quantity,
			pos_purchase_order_contents.quantity_ordered, 
			pos_purchase_order_contents.quantity_canceled, 
			pos_purchase_order_contents.discount_quantity,
			pos_products_sub_id.pos_product_id,
			pos_products_sub_id.pos_product_sub_id,
			pos_products_sub_id.product_subid_name, 

			(pos_purchase_order_contents.quantity_ordered - pos_purchase_order_contents.quantity_canceled) *
			(pos_purchase_order_contents.cost -discount) 
			as ordered_amount, 
			
			(pos_purchase_order_contents.quantity_ordered - pos_purchase_order_contents.quantity_canceled)*pos_purchase_order_contents.discount
			as discount_extension, 
			pos_products_sub_id.product_upc, 
			pos_products.title, 
			pos_products.style_number,
	
			
			
			(SELECT concat(
				(SELECT group_concat(concat(attribute_name,':Code:',option_code,' Desc:',option_name) SEPARATOR '<br>') 
				FROM pos_product_sub_id_options 
				LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
				LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
				WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
				)
			FROM pos_products_sub_id 
			LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
			LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_purchase_order_contents.pos_product_sub_id) 
		as item,
		(SELECT coalesce(sum(pos_purchase_order_receive_contents.received_quantity),0) FROM  pos_purchase_order_receive_contents WHERE pos_purchase_order_receive_contents.pos_purchase_order_content_id = pos_purchase_order_contents.pos_purchase_order_content_id) 
		as received_quantity,
		(SELECT coalesce(sum(pos_purchase_order_receive_contents.received_quantity),0) FROM  pos_purchase_order_receive_contents WHERE pos_purchase_order_receive_contents.pos_purchase_order_content_id = pos_purchase_order_contents.pos_purchase_order_content_id) *(pos_purchase_order_contents.cost-pos_purchase_order_contents.discount)
		as received_amount
		
		
		
		
		FROM pos_purchase_order_contents LEFT JOIN pos_products_sub_id USING(pos_product_sub_id) 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id WHERE pos_purchase_order_id = $pos_purchase_order_id
		;";
		
		
	$select_sql = "SELECT *   FROM temp";
	$html = '<div class = "tight_divider">';
	$html .= '<h2>Status Table</h2>';
	$html .= '<h4>This table shows the products associated with the PO, the ordered and canceled amounts, and the amounts received. For a purchase order to be closed the received amount has to equal the ordered amount (including canceled items)</h4>';
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$sql);
	$data = getTransactionSQL($dbc,$select_sql);
	closeDB($dbc);
	$html .= createStaticViewDynamicTable($array_table_def, $data, ' style = "width:100%;" ');
	$html .= '<INPUT class = "button" type="button" style="width:150px" value="Email Update" onclick="window.location = \'../SendPurchaseOrder/email_po_update.php?pos_purchase_order_id='.$pos_purchase_order_id.'\'" />';
	$html .= '<INPUT class = "button" style="width:300px" type="button" value="Reload Product Information To Contents" onclick="window.location = \'../CreatePurchaseOrder/reload_product_information.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	if(getPurchaseOrderStatus($pos_purchase_order_id) == 'OPEN' || getPurchaseOrderStatus($pos_purchase_order_id) == 'CLOSED')
	{
		$content_edit_tags = '';
	}
	else
	{
		$content_edit_tags = ' disabled = "disabled" ';
	}
	$html .= '<INPUT class = "button" style="width:300px" type="button" '.$content_edit_tags.' value="Reload Manufacturer UPC Data" onclick="window.location = \'../CreatePurchaseOrder/reload_upc_data.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	

	
	
	if(checkIfUserIsAdmin($_SESSION['pos_user_id']))
	{
		$html .= '<p><INPUT class = "admin_button" style="width:200px;" type="button" '.$content_edit_tags.' value="ADMIN: Re-Process Product Links" onclick="window.location = \'../CreatePurchaseOrder/process_purchase_order_contents.php?type=reprocess&pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
		$html .= '<INPUT class = "admin_button" type="button" style = "width:200px" value="ADMIN: Clear Bad Products" onclick="window.location = \'../CreatePurchaseOrder/delete_product_and_sub_ids.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" /></p>';
	}
	
	$html .= '</div>';
	return $html;
	
}

function createPOReceiveButtons($pos_purchase_order_id)
{	
	$tags = generateReceiveEnableTag($pos_purchase_order_id);
	$tags = '';
	$html = '<div class = "tight_divider">';
	$html .= '<h2>Receive Event</h2>';

		
	//create a records table of receive events....
	$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_purchase_order_receive_event_id',
			'get_url_link' => "../ReceivePurchaseOrder/receive_purchase_order.php?pos_purchase_order_id=".$pos_purchase_order_id,
			'url_caption' => 'View',
			'get_id_link' => 'pos_purchase_order_receive_event_id'),
			
		array(
			'th' => 'Date',
			'mysql_field' => 'receive_date',
			'date_format' => "date",
			),
		array(
			'th' => 'Total Quantity Received',
			'mysql_field' => 'total_quantity_received',
			'total' =>0),
		array(
			'th' => 'Total Amount Received',
			'mysql_field' => 'total_amount_received',
			'total' => 2,
			'round' => 2),
		array(
			'th' => 'Pick Ticket Number',
			'mysql_field' => 'pick_ticket',
			),	
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			),	
		);
	
	$tmp_sql = "create Temporary table receive 
	SELECT pos_purchase_order_receive_event_id, pick_ticket, comments, receive_date,
	(SELECT sum(pos_purchase_order_receive_contents.received_quantity)  FROM pos_purchase_order_receive_contents WHERE pos_purchase_order_receive_contents.pos_purchase_order_receive_event_id = pos_purchase_order_receive_event.pos_purchase_order_receive_event_id) as total_quantity_received,
	
		(SELECT sum(pos_purchase_order_receive_contents.received_quantity*(pos_purchase_order_contents.cost - pos_purchase_order_contents.discount)) FROM pos_purchase_order_receive_contents 
		LEFT JOIN pos_purchase_order_contents USING (pos_purchase_order_content_id)
		WHERE pos_purchase_order_receive_contents.pos_purchase_order_receive_event_id = pos_purchase_order_receive_event.pos_purchase_order_receive_event_id) as total_amount_received
	
	FROM pos_purchase_order_receive_event
	
	WHERE pos_purchase_order_id = $pos_purchase_order_id ORDER BY receive_date ASC
	;";
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$receive_data = getTransactionSQL($dbc,"SELECT * FROM receive");
	closeDB($dbc);
	$html.= '<p>The following table summarizes the receive events. Each receive event creates a non-editable record of received products. If an error is made create a new entry with the adjusted amount.</p>';
	$html .= createRecordsTableWithTotals($receive_data, $table_columns);
	
	$html .= '<INPUT class = "button" type="button" ' .$tags.'value="Receive Order" onclick="window.location = \'../ReceivePurchaseOrder/receive_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	//$html .= '<INPUT style="width:250px" class = "button" type="button" ' .$tags.'value="Manual Count Receive Complete Order" onclick="window.location = \'../ReceivePurchaseOrder/manually_receive_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	//$html .= '<INPUT class = "button" type="button" ' .$tags.'value="Report Damaged Items" style ="width:200px" onclick="window.location = \'../ReceivePurchaseOrder/receive_damaged_purchase_order_items.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	
	//$html .= '<INPUT class = "button" type="button" ' .$tags.'value="Return Items" onclick="window.location = \'../ReceivePurchaseOrder/return_purchase_order_items.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" />';
	$html .= '<INPUT class = "button" type="button" style="width:150px" value="Print Labels" onclick="window.location = \'../PrintPurchaseOrder/print_po_labels.php?pos_purchase_order_id='.$pos_purchase_order_id.'\'" />';
	if(checkIfUserIsAdmin($_SESSION['pos_user_id']))
	{
		$html .= '<p><INPUT class = "admin_button" type="button" style = "width:300px;" value="ADMIN: Clear Received Items To Edit Contents" onclick="window.location = \'../ReceivePurchaseOrder/clear_received_items.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" /></p>';
	}
	
	$html .= '</div>';
	
	return $html;
}

function createPORAhtml($pos_purchase_order_id)
{
	$html = '<div class = "tight_divider">';
	$html .= '<h2>Purchase Order RA and Credit Memos</h2>';
	
	if (getRANumber($pos_purchase_order_id) != '')
	{
		$html .= '<table><tr><td>Ra Number(s):</td><td> <input style="width:500px" type="text" value="' . getRANumber($pos_purchase_order_id) . '" class = "linedInput" name="ra_number" disabled="disabled" /></td></tr></table>';
		$html .= '<INPUT class = "button" type="button" style = "width:150px" value="Edit RA Number" onclick="window.location =\'../ReceivePurchaseOrder/ra.php?pos_purchase_order_id='. $pos_purchase_order_id . '&edit=edit\'" />';
		$html .='</p>';
	}
	if (getPORARequest($pos_purchase_order_id) == 1)
	{
		$html .= '<p>';
		$html .= '<table><tr><td width="14px"><input type="checkbox" name="ra_requested" checked="checked" disabled="disabled" value="ra_requested"></td><td>RA Required</td></tr></table>';
		$html .= '<INPUT class = "button" type="button" style = "width:150px" value="Enter RA Number" onclick="window.location =\'../ReceivePurchaseOrder/ra.php?pos_purchase_order_id='. $pos_purchase_order_id . '\'" />';
		$html .= '</p>';

	}
	
	if (getCreditMemoNumber($pos_purchase_order_id) != '')
	{
		$html .= '<table><tr><td>Credit Memo Number(s):</td><td> <input style="width:500px" type="text" value="' . getCreditMemoNumber($pos_purchase_order_id) . '" class = "linedInput" name="ra_number" disabled="disabled" /></td></tr></table>';
		$html .= '<INPUT class = "button" type="button" style = "width:150px" value="Edit Credit Memo Number" onclick="window.location =\'../ReceivePurchaseOrder/credit_memo_number.php?pos_purchase_order_id='. $pos_purchase_order_id . '&edit=edit\'" />';
		$html .='</p>';
	}
	if (getPOCreditMemoRequired($pos_purchase_order_id) == 1)
	{
		$html .= '<table><tr><td width="14px"><input type="checkbox" checked="checked" name="credit_memo_required" disabled="disabled" value="credit_memo_required"></td><td>Credit Memo Required</td></tr></table>';
		//$html .= '<p><INPUT class = "button" type="button" style = "width:150px" value="Enter Credit Memo Number" onclick="window.location =\'../ReceivePurchaseOrder/credit_memo_number.php?pos_purchase_order_id='. $pos_purchase_order_id . '\'" />';
		$html .= '<input class = "button" type="button" style="width:300px" name="add_credit_memo" value="Add Credit Memo" onclick="open_win(\'../../accounting/PurchaseJournal/add_edit_purchase_invoice_to_journal.php?type=credit&pos_purchase_order_id='.$pos_purchase_order_id.'&pos_manufacturer_id='.getManufacturerIdFromPOId($pos_purchase_order_id).'\')"/>';
		$html .= '</p>';

	}
	
	$html .= '</div>';

	return $html;
}
function createPOProductButtons($pos_purchase_order_id)
{	
	$tags = generateReceiveEnableTag($pos_purchase_order_id);
	$html = '<div class = "tight_divider">';
	$html .= '<p>Products Generated From PO</p>';
	

	$html .= '</div>';
	return $html;
}

function createPOInvoiceButtons($pos_purchase_order_id)
{
	//does the manufacturer have an account?
	
	
	
	$html = '<div class = "tight_divider">';
	$html .= '<h2>INVOICES</h2>';
	$pos_manufacturer_id = getManufacturerIdFromPOId($pos_purchase_order_id);
	if (!checkManufacturerAccount($pos_manufacturer_id))
	{
		//this is an error, 
		$html.= 'You need to set up an account for this manufacturer before entering the Invoice. To do that Create an Account. Look for an account number and payable information on the invoice. Once the account is created, link the account to the manufacturer. Go to the manufacturer page and edit the manufacturer, selecting the account. Soon those links will be here.';
			if (checkUserAccess('Manufacturers'))
			{
			$html.= '<p><input class = "button" type="button" style="width:300px" name="add_purchase_invoice_on_account" value="View  '.getManufacturerName($pos_manufacturer_id).' Manufacturer Info" onclick="open_win(\'' .POS_ENGINE_URL . '/manufacturers/ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$pos_manufacturer_id.'\')"/>';
			}
			else
			{
				$html.= '<p><input class = "button" type="button" style="width:300px" readonly="readonly" name="add_purchase_invoice_on_account" value="Ask for Manufacturer Binder Access" onclick="open_win(\'' .POS_ENGINE_URL . '/manufacturers/ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$pos_manufacturer_id.'\')"/>';
			}
			
			if (checkUserAccess('Accounts'))
			{
			$html .= '<input class = "button" type="button" name="add_account" style="width:200px" value="Add Account" onclick="open_win(\''.POS_ENGINE_URL.'/accounting/Accounts/accounts.php?type=add\')"/>';
			}
			else
			{
			$html .= '<input class = "button" type="button" readonly="readonly" style="width:300px" name="add_account" value="Ask for Account Binder access" onclick="open_win(\''.POS_ENGINE_URL.'/accounting/Accounts/accounts.php?type=add\')"/>';
			}

	}
	else
	{
	
		$html .= createPurchaseOrderRecordTable($pos_purchase_order_id);
		$html .= '<INPUT class = "button" type="button" style="width:200px;" value="Add New Invoice" onclick="window.location = \'../../accounting/PurchaseJournal/add_edit_purchase_invoice_to_journal.php?pos_purchase_order_id='.$pos_purchase_order_id . '&pos_manufacturer_id=' . getManufacturerIdFromPOId($pos_purchase_order_id) . '&type=invoice' . '\'" />';
			
		
		
			
		//get the Invoices that have not been fully applied to PO's:
	//this would be where the applied amount + shipping != invoice
	
	$html .= '<form action="'.POS_ENGINE_URL .'/accounting/PurchaseJournal/add_edit_purchase_invoice_to_journal.php" method="POST" >';
	$html .= 'Add a this po to an existing invoice';
	$tmp_sql = "SELECT  
	pos_purchases_journal_id,  
		pos_account_id, 
		invoice_number, 
		invoice_date,
		invoice_status,
		payment_status,
		invoice_amount,
		shipping_amount, 
		invoice_amount-shipping_amount as goods_amount,
	(SELECT COALESCE(sum(applied_amount),0) FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id) as applied_amount,
		invoice_amount-shipping_amount as goods_amount,
	invoice_amount-shipping_amount - (SELECT COALESCE(sum(applied_amount),0) FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id) as amount_remaining
	
		
FROM pos_purchases_journal
	
	WHERE pos_account_id=" . getManufacturerAccount(getManufacturerIdFromPOId($pos_purchase_order_id)) . " AND invoice_status = 'OPEN' AND invoice_type = 'Regular' AND (invoice_amount-shipping_amount) > (SELECT COALESCE(sum(applied_amount),0) FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id) ORDER BY invoice_date ASC	
	";

	
	
	$dbc = openPOSdb();
	$purchase_invoices = getTransactionSQL($dbc,$tmp_sql);
	closeDB($dbc);
	//preprint($purchase_invoices);
	
	$html .= '<select id = "pos_purchases_journal_id" name="pos_purchases_journal_id" >';
	//Add an option for not selected
	$html .= '<option value="false">Select Invoice From the Purchases Journal</option>';
	//add an option for all accounts
	for($i = 0;$i < sizeof($purchase_invoices); $i++)
	{
		$html .= '<option value="' . $purchase_invoices[$i]['pos_purchases_journal_id'] . '"';		
		$html .= '>System PJ#: ' . $purchase_invoices[$i]['pos_purchases_journal_id'] . ' Invoice #: ' .$purchase_invoices[$i]['invoice_number'] . ' Dated: ' .$purchase_invoices[$i]['invoice_date'] . ' Amount: ' .number_format($purchase_invoices[$i]['invoice_amount'],2) . ' Amount Remaining to Apply: ' .number_format($purchase_invoices[$i]['amount_remaining'],2) .'</option>';
	}
	$html .= '</select>';
	
	
	
	
	$html .= '<INPUT class = "button" type="submit" style="width:200px;" value="Add To Invoice"  />';
	$html .= createHiddenInput('type', 'edit');
	$html .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	$html .= '</form>';
	}
	

	$html .= '</div>';
	return $html;
}
function createPOLog($pos_purchase_order_id)
{
	$html = '<div class = "tight_divider">';
	$html .= '<h2>Purchase Order Log</h2>';	
	//$form_handler = 'update_po_log.form.handler.php';
	//$html = '<form action="' . $form_handler.'" method="post" >';
	$html .= '<p>Type a Message below and choose the add message to log button to add a message.</p>';
	$html .= '<textarea class="textarea_comments" type ="text" id="po_log" name ="po_log" >';
	$html .= '</textarea>';
	//$html .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	//$html .= '<p><input class ="button" type="submit" style="width:200px" name="add_note" value="Add Comment To Log" />' .newline();
	$html .= '<p>';
	$html .= '<INPUT class = "button" style="width:200px" type="button" value="Add Comment To Log" onclick="updatePOLog()" />';
	
	$html .= '<script>var pos_purchase_order_id = ' .$pos_purchase_order_id.'</script>';
	$html .= '</p>';
	//$html .= '</form>';
	if (getPOLog($pos_purchase_order_id) != '')
	{
		$title = '';
		$html .= '<h4>Messages</h4>';
		//$html .= nl2br(textareaHTMLTable($title, getPOLog($pos_purchase_order_id)));
		$html .= textareaHTMLTable($title, getPOLog($pos_purchase_order_id));
		$html .= '<p></p>';
		//$html .= '<INPUT class = "button" style="width:200px" type="button" value="Edit Log" onclick="window.location = \'../po_log/edit_po_log.php?pos_purchase_order_id='.$pos_purchase_order_id .'\'" />';
	}
	$html .= '</div>';
	return $html;
}

function createPONavigationButtons($pos_purchase_order_id)
{
	$html = '<div class = "tight_divider">';	
	$html .= '<INPUT class = "button" type="button" value="Back To Orders" onclick="window.location = \'../purchase_orders.php\'" />';
	$html .= '</div>';
	return $html;
}
function includesForPOC()
{
return '<link type="text/css" href="' . POS_ENGINE_URL . '/purchase_orders/poStyles.css" rel="Stylesheet"/>';

}
?>
<script>

function cancel()
{
	window.open('../purchase_orders.php');
}
</script>

