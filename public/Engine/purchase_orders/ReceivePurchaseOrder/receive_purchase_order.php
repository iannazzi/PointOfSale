<?php
/*
	*recieve_purchase_order.php
	*Craig Iannazzi 2-14-12
	*This form is used to recieve the purchase order
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';	
$page_title = 'Receive a Purchase Order';
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');


if(isset($_GET['pos_purchase_order_receive_event_id']))
{
	$pos_purchase_order_receive_event_id =  getPostOrGetID('pos_purchase_order_receive_event_id');
	//just want to be able to view the event...
	$receive_date = getSingleValueSQL("SELECT receive_date FROM pos_purchase_order_receive_event WHERE pos_purchase_order_receive_event_id = $pos_purchase_order_receive_event_id");
	$html = '<h2>Recevie Event ID ' . $pos_purchase_order_receive_event_id . ' On Date ' .$receive_date. '</h2>';
	
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
							'th' => 'Product Sub ID',
							'db_field' => 'pos_product_sub_id',
							'type' => 'td_hidden_input'),
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
				

					array(	'th' => 'Quantity<br>Received',
							'db_field' => 'received_quantity',
							'type' => 'input',
							'round' => 0,
							'total' => 0,
							'tags' => ' style="background-color:yellow" '),
					array(	'th' => 'Comments',
							'db_field' => 'receive_comments',
							'type' => 'input',
							
							'tags' => ' style="background-color:yellow" '),
					
					);
	$receive_sql = "SELECT   
	pos_purchase_order_contents.pos_purchase_order_content_id,
	pos_purchase_order_contents.quantity_ordered, 
	pos_purchase_order_contents.quantity_canceled,  
	pos_products_sub_id.pos_product_id,
	pos_products_sub_id.pos_product_sub_id, 
	pos_products_sub_id.product_upc, 
	pos_products.title, 
	pos_products.style_number,
	
	(SELECT concat(
			(SELECT group_concat(concat(attribute_name,':Code:',option_code,' Desc:',option_name) 
				SEPARATOR '<br>') 
				FROM pos_product_sub_id_options 
				LEFT JOIN pos_product_options 
				ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
				LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
				WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
			)
		FROM pos_products_sub_id 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE pos_products_sub_id.pos_product_sub_id = pos_purchase_order_contents.pos_product_sub_id) 
	as item,
		
		
	pos_purchase_order_receive_contents.received_quantity,
	pos_purchase_order_receive_contents.receive_comments
	
		FROM pos_purchase_order_receive_event
		LEFT JOIN pos_purchase_order_receive_contents USING (pos_purchase_order_receive_event_id)
		LEFT JOIN pos_purchase_order_contents 
		ON pos_purchase_order_receive_contents.pos_purchase_order_content_id = pos_purchase_order_contents.pos_purchase_order_content_id
		LEFT JOIN pos_products_sub_id USING(pos_product_sub_id) 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id 
		WHERE pos_purchase_order_receive_event.pos_purchase_order_receive_event_id = $pos_purchase_order_receive_event_id";
		
	$html .= createStaticViewDynamicTable($array_table_def, getSQL($receive_sql), ' style = "width:100%;" ');
	
	$html .= '<p>Wrong Items Comments: <BR>'.getSingleValueSQL("SELECT wrong_items_comments FROM pos_purchase_order_receive_event WHERE pos_purchase_order_receive_event_id = $pos_purchase_order_receive_event_id") . '</p>';
	$html .= backButton();
	$html .= createOpenWinButton('View PO# ' .$pos_purchase_order_id, '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id);
	
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
	exit();
}
else
{
	$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
	$cancel_location = $complete_location;
	$form_handler = 'receive_purchase_order.form.handler.php';
	if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
	{
		include (HEADER_FILE);
		$html = createMiniPOOverview($pos_purchase_order_id, 'true');
		$table_def_array = createRecieveTableARrayDef($pos_purchase_order_id);
$purchase_order_products = getPurchaseOrderContentsAndProductSubids($pos_purchase_order_id);
	
		if(checkForProductSubIds($pos_purchase_order_id))
		{
			$purchase_order_products = getPurchaseOrderContentsAndProductSubids($pos_purchase_order_id);
			$table_def_array_with_data = loadMYSQLArrayIntoTableArray($table_def_array, $purchase_order_products);
			$class = "receive_purchase_order_table";
			$html_table = createMYSQLArrayHTMLTable($table_def_array_with_data, $class, 'receive_table');
			$html_table .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	
			$check_in_column = 9;
			$ordered_column = 6;
			$previously_received_column = 8;
			$html .= createRecievePOForm($table_def_array_with_data, $html_table, $form_handler, $complete_location, $cancel_location, $pos_purchase_order_id, $check_in_column);
			$html .= '<script> var ordered_column = '.$ordered_column.';</script>';
			$html .= '<script> var ordered_column = '.$ordered_column.';</script>';
			$html .= '<script> var previously_received_column = '.$previously_received_column.';</script>';
		}
		else
		{
			$html .= 'There are missing product-sub id\'s for this purchase order. This means you missed selecting a size row';
			$html .= '<p><INPUT class = "button" type="button" style = "width:150px" value="Re-Process PO Contents" onclick="window.location =\'../CreatePurchaseOrder/reprocess_purchase_order_contents.php?pos_purchase_order_id='. $pos_purchase_order_id . '\'" />';
			$html .= '<INPUT class = "button" type="button" style = "width:180px" value="Do Not Process (Exit)" onclick="window.location =\''. '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" /></p>';
		}
		$html .= '<script> document.getElementById(\'mfg_barcode\').focus();
    	document.getElementById(\'mfg_barcode\').select();</script>';
		echo $html;
		include (FOOTER_FILE);
	
	}
	else
	{
		include (HEADER_FILE);
		echo 'error - not a valid ID';
		include (FOOTER_FILE);
	}
}
function createRecieveTableARrayDef($pos_purchase_order_id)
{
	$array_table_def= array(	
					array(	'th' => 'POC ID',
			 				'type' => 'hidden_input',
							'mysql_result_field' => 'pos_purchase_order_content_id',
							'mysql_post_field' => 'pos_purchase_order_content_id'),
					array(
							'th' => 'Manufacturer UPC',
							'mysql_result_field' => 'product_upc',
							'type' => 'if_blank_then_input',
							'mysql_post_field' => 'product_upc'),
					array(
							'th' => 'Product Sub Id <br> (Our Barcode)',
							'mysql_result_field' => 'pos_product_sub_id',
							'type' => 'td',
							'mysql_post_field' => 'pos_product_sub_id'),
					array(
							'th' => 'Style Number',
							'mysql_result_field' => 'style_number',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'title',
							'mysql_result_field' => 'title',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Item',
							'mysql_result_field' => 'item',
							'type' => 'td',
							'mysql_post_field' => ''),
					
					array(	'th' => 'Quantity <br>Ordered',
							'mysql_result_field' => 'quantity_ordered',
							'type' => 'td',
							'total' => 0,
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity <br>Canceled',
							'mysql_result_field' => 'quantity_canceled',
							'type' => 'td',
							'total' => 0,
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity Received',
							'mysql_result_field' => 'received_quantity',
							'type' => 'td',
							'total' => 0,
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity<br> Checking In <br> In New Condition',
							'mysql_result_field' => '',
							'type' => 'input',
							'tags' => ' class="highlight" '.numbersOnly() . ' onchange="needToConfirm=true;updateFooter();"',
							'value' => 0,
							'total' => 0,
							'mysql_post_field' => 'new_received_quantity'),
					
					array(	'th' => 'Receive Comments',
							'mysql_result_field' => '',
							'type' => 'input',
							'mysql_post_field' => 'receive_comments'),
					array(	'th' => 'Order Comments',
							'mysql_result_field' => 'comments',
							'type' => 'td',
							'mysql_post_field' => '')
					);
	return $array_table_def;
}
function createRecievePOForm($table_def, $table_html ,$form_handler, $complete_location ,$cancel_location,$pos_purchase_order_id, $check_in_column)
{
	
	$mfgIdColumn = 1;
	$barcode_column = 2;
	$qty_ordered_column = 8;
	$qty_canceled_column = 7;
	//Set the script up
	$html = confirmNavigation();
	$html .= '<script src="receive_purchase_order.form.2014.01.22.js"></script>';
	$html .= '<link type="text/css" href="../poStyles.css" rel="Stylesheet"/>'.newline();
	//Product sub ID QTY ORDERED QTY RECEIVED QTY DAMAGED
	$html .= '<form id = "purchase_order_receive_form" name="purchase_order_receive_form" action="'.$form_handler.'" method="post" >';

	$html .= '<p>Barcode <INPUT TYPE="TEXT" class="lined_input"  id="mfg_barcode" style = "background-color:yellow;width:150px;" NAME="mfg_barcode" onKeyPress="return disableEnterKey(event)" onKeyDown="lookUpBarcodeID(this, event)"	/>';
	$html .= '<INPUT TYPE="button" class="button"  id="barcode_push" style = "width:50px;" NAME="barcode_push" VALUE="Add" onclick="lookUpBarcode();"	/></p>';
		$html .= ' <p><input type="checkbox" style="width:10px;margin-right:10px;" name="remove" id="remove"  />';

	$html .= 'Check to Subtract Quantities (Scan the barcodes left on the sheet for missing items!)</p>';
	$html .= '<p><input type="button" class="button" name="manual" id="manual" value="Receive Complete" style="width:120px;" onclick="ManualCountReceiveComplete()" /></p>';
	

	$html .= '<p>Pick Ticket Number <INPUT TYPE="TEXT" class="lined_input"  id="pick_ticket" style = "background-color:yellow;width:100px;" NAME="pick_ticket" onKeyPress="return disableEnterKey(event)" 	/>';
	$html .= 'Receive Comments <INPUT TYPE="TEXT" class="lined_input"  id="receive_event_comments" style = "background-color:yellow;width:300px;" NAME="receive_event_comments" onKeyPress="return disableEnterKey(event)" 	/></p>';

	$html .= $table_html;
	
	$html .= '<p>Extra Items: Enter the quantity of items received which are not on this list.<INPUT TYPE="TEXT" class="lined_input"  value ="'.getWrongItems($pos_purchase_order_id).'" name="wrong_items_qty" id="wrong_items_qty" style = "text-align:center;background-color:yellow;width:20px;" onKeyPress="return disableEnterKey(event)" 	/></p><p></p>';
	$html .= '<p>Record Errors to the Order Below.</p>';
	$html .= '<p><textarea class = "textarea_comments" name = "wrong_items_comments" id="wrong_items_comments" >';
	$html .= getWrongItemsComments($pos_purchase_order_id) . '</textarea></p>';
	$html .= '<table ><tr><td width="14px"><input id ="ra_required" type="checkbox" class = "checkbox_class" name="ra_required" value="ra" /></td><td>RA Required</td></tr></table>';

	$html .= '<p><input class = "button" type="submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>';
	$html .= '<input class = "button" type="submit" name="submit" value="Cancel" /></p>'.newline();
	$html .= createHiddenSerializedInput('table_def', prepareArrayTableForPost($table_def)).newline();	
	
	$html .= createHiddenInput('complete_location', $complete_location);
	$html .= createHiddenInput('cancel_location', $cancel_location);
	//close the form
	$html .= '</form>' .newline();
	//drop the array out for form validation
	//$json_table_def = json_encode($table_def);
	$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
	$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
	$html .= '<p>If the manufacturer barcode does not show up on this page then Check that the Manufacturer UPC file is  loaded and there is a style number color code and size match. Try editing the original PO to make sure sizes and color codes are correct. If No product sub-id\'s are shown it means there was a missing size row selection.</p>';
	//variables for javascript
	$pos_manufacturer_brand_id = getBrandIdFromPOId($pos_purchase_order_id);
	$pos_manufacturer_id = getManufacturerIdFromBrandId($pos_manufacturer_brand_id);
	$html .= '<script>var pos_manufacturer_id = "' . $pos_manufacturer_id . '";</script>';
	$html .= '<script>var check_in_column = "' . $check_in_column . '";</script>';
	$html .= '<script>var qty_ordered_column = "' . $qty_ordered_column . '";</script>';
	$html .= '<script>var qty_canceled_column = "' . $qty_canceled_column . '";</script>';
	$html .= '<script>var mfgIdColumn = "' . $mfgIdColumn . '";</script>';
	$html .= '<script>var barcode_column = "' . $barcode_column . '";</script>';
	$html .= '<script>needToConfirm = true;</script>';
	$html .= addBeepV3().newline();
	
	
	return $html;
}
?>
