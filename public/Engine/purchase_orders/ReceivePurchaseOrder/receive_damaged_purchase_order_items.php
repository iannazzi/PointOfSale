<?php
/*
	*recieve_purchase_order.php
	*Craig Iannazzi 2-14-12
	*This form is used to recieve the purchase order
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Receive Damaged Goods';
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = $complete_location;
$form_handler = 'receive_damaged_purchase_order_items.form.handler.php';
if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	include (HEADER_FILE);
	$html = '<h2>Receive Damaged Goods</h2>'.newline();
	$html .= createMiniPOOverview($pos_purchase_order_id, 'true');
	$table_def_array = createRecieveDamageTableARrayDef($pos_purchase_order_id);
	
	if(checkForProductSubIds($pos_purchase_order_id))
	{
		$purchase_order_products = getPurchaseOrderContentsAndProductSubids($pos_purchase_order_id);
		$table_def_array_with_data = loadMYSQLArrayIntoTableArray($table_def_array, $purchase_order_products);
		$class = "receive_purchase_order_table";
		$html_table = createMYSQLArrayHTMLTable($table_def_array_with_data, $class, 'receive_table');
		$html_table .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
		$html .= createRecieveDamagePOForm($table_def_array_with_data, $html_table, $form_handler, $complete_location, $cancel_location);
	}
	else
	{
		$html .= 'There are missing product-sub id\'s for this purchase order. This means you missed selecting a size row';
	}
	echo $html;
	include (FOOTER_FILE);
	
}
else
{
	include (HEADER_FILE);
	echo 'error - not a valid ID';
	include (FOOTER_FILE);
}

function createRecieveDamageTableARrayDef($pos_purchase_order_id)
{
	$array_table_def= array(	
					array(	'th' => 'POC ID',
			 				'type' => 'hidden_input',
							'mysql_result_field' => 'pos_purchase_order_content_id',
							'mysql_post_field' => 'pos_purchase_order_content_id'),
					array(
							'th' => 'Manufacturer_id',
							'mysql_result_field' => 'product_upc',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Style Number',
							'mysql_result_field' => 'style_number',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Code',
							'mysql_result_field' => 'color_code',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Description',
							'mysql_result_field' => 'color_description',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Size',
							'mysql_result_field' => 'size',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Product SubId',
							'mysql_result_field' => 'product_subid_name',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity Damaged<br>Already<br> Checked In',
							'mysql_result_field' => 'quantity_damaged',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity Damaged<br> Checking In',
							'mysql_result_field' => '',
							'type' => 'input',
							'tags' => ' class="highlight" ',
							'value' => 0,
							'mysql_post_field' => 'quantity_damaged'),
					array(	'th' => 'Comments',
							'mysql_result_field' => 'comments',
							'type' => 'input',
							'mysql_post_field' => 'comments')
					);
	return $array_table_def;
}
function createRecieveDamagePOForm($table_def, $table_html ,$form_handler, $complete_location ,$cancel_location )
{
	$check_in_column = 8;
	$mfgIdColumn = 1;
	//Set the script up
	$html = confirmNavigation();
	$html .= '<script src="receive_purchase_order.form.js"></script>';
	$html .= '<link type="text/css" href="../poStyles.css" rel="Stylesheet"/>'.newline();
	//Product sub ID QTY ORDERED QTY RECEIVED QTY DAMAGED
	
	$html .= '<form id = "purchase_order_receive_form" name="purchase_order_receive_form" action="'.$form_handler.'" method="post" >';
	$html .= '<p>Manufacturer Barcode <INPUT TYPE="TEXT" class="lined_input"  id="mfg_barcode" style = "background-color:yellow;width:300px;" NAME="mfg_barcode" onKeyPress="return disableEnterKey(event)" onKeyDown="lookUpBarcodeID(this, event)"	/></p><p></p>';

	$html .= $table_html;

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
	$html .= '<script>var check_in_column = "' . $check_in_column . '";</script>';
	$html .= '<script>var mfgIdColumn = "' . $mfgIdColumn . '";</script>';
	$html .= '<script>needToConfirm = true;</script>';
	$html .= addBeepV3().newline();
	
	
	return $html;
}

?>
