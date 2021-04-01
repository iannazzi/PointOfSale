<?php
/*
	*recieve_purchase_order.php
	*Craig Iannazzi 2-14-12
	*This form is used to recieve the purchase order
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Add a Discount to Order Contents';
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = $complete_location;
$form_handler = 'add_discount_to_order_contents.form.handler.php';
if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	include (HEADER_FILE);
	$html = createMiniPOOverview($pos_purchase_order_id, 'true');
	$table_def_array = createPurchaseOrderContentsDiscountTableFef($pos_purchase_order_id);
	
	if(checkForProductSubIds($pos_purchase_order_id))
	
	{
		$purchase_order_products = getPurchaseOrderContentsAndProductSubids($pos_purchase_order_id);
		$table_def_array_with_data = loadMYSQLArrayIntoTableArray($table_def_array, $purchase_order_products);
		$class = "receive_purchase_order_table";
		$html_table = createMYSQLArrayHTMLTable($table_def_array_with_data, $class, 'discount_table');
		$html_table .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
		$html .= createPurchaseOrderDiscountForm($table_def_array_with_data, $html_table, $form_handler, $complete_location, $cancel_location, $pos_purchase_order_id);
	}
	else
	{
		$html .= 'There are missing product-sub id\'s for this purchase order. This means you missed selecting a size row';
		$html .= '<p><INPUT class = "button" type="button" style = "width:150px" value="Re-Process PO Contents" onclick="window.location =\'../CreatePurchaseOrder/reprocess_purchase_order_contents.php?pos_purchase_order_id='. $pos_purchase_order_id . '\'" />';
		$html .= '<INPUT class = "button" type="button" style = "width:180px" value="Do Not Process (Exit)" onclick="window.location =\''. '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id . '\'" /></p>';
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

function createPurchaseOrderContentsDiscountTableFef($pos_purchase_order_id)
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
					array(	'th' => 'Cost',
							'mysql_result_field' => 'cost',
							'type' => 'td',
							'round' => 2,
							'mysql_post_field' => ''),
					array(	'th' => 'Discount in Dollars',
							'mysql_result_field' => 'discount',
							'type' => 'input',
							'round' => 2,

							'tags' => ' class="highlight" onchange="needToConfirm=true;updateAdjustedPrice()" ',
							'mysql_post_field' => 'discount'),
					array(	'th' => 'Adjusted Price',
							'mysql_result_field' => 'cost_minus_discount',
							'type' => 'input',
							'tags' => ' readonly="readonly" ',
														'round' =>2,

							'mysql_post_field' => 'cost'),
					array(	'th' => 'Ordered Quantity',
							'mysql_result_field' => 'quantity_ordered',
							'type' => 'td',
							'total' =>0,
							'mysql_post_field' => ''),
					/*array(	'th' => 'Discount Quantity',
							'mysql_result_field' => 'discount_quantity',
							'tags' => ' class="highlight" onchange="needToConfirm=true;updateFooter();"',
							'type' => 'input',
							'total' =>0,
							'mysql_post_field' => 'discount_quantity')*/
					
					);
	return $array_table_def;
}
function createPurchaseOrderDiscountForm($table_def, $table_html ,$form_handler, $complete_location ,$cancel_location,$pos_purchase_order_id )
{
	
	//Set the script up
	$html = confirmNavigation();
	$html .= '<script src="add_discount_to_order_contents.2014.08.03.js"></script>';
	$html .= '<link type="text/css" href="../poStyles.css" rel="Stylesheet"/>'.newline();
	//Product sub ID QTY ORDERED QTY RECEIVED QTY DAMAGED
	$html .= '<form id = "add_discount_to_order_contents" name="add_discount_to_order_contents" action="'.$form_handler.'" method="post" >';
	$html .= '<p>Order Discount (%) <INPUT TYPE="TEXT" class="lined_input"  id="show_discount" style = "background-color:yellow;width:20px;" NAME="show_discount" onKeyPress="return disableEnterKey(event)" onKeyup="updateShowDiscount(this)"	/>';
	//$html .= '<input class = "button" type="button" style="width:250px;margin: 0px 4px 0px 4px;" name="create_po" value="Apply Discount to all Ordered Quantity" onclick="copyQuantity()"/>';
	$html .= '</p><p></p>';
	$html .= '<p>Warning: If the order contents are deleted then the discounts will also be deleted</p>';
	$html .= $table_html;
	$html .= '<p>Original Order Cost <INPUT TYPE="TEXT" class="lined_input"  readonly="readonly" id="order_cost" style = "width:50px;" NAME="order_cost"	/>';
	$html .= 'Total Discount <INPUT TYPE="TEXT" class="lined_input"  readonly="readonly" id="total_discount" style = "width:50px;" NAME="total_discount"	/>';
	$html .= 'Discounted Order Cost <INPUT TYPE="TEXT" class="lined_input"  readonly="readonly" id="discounted_order_cost" style = "width:50px;" NAME="discounted_order_cost"	/></p>';
	$html .= '<p><input class = "button" type="submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>';
	$html .= '<input class = "button" type="submit" name="submit" value="Cancel" /></p>'.newline();
	$html .= createHiddenSerializedInput('table_def', prepareArrayTableForPost($table_def)).newline();	
	$html .= '<script>var json_table_def = ' . prepareArrayTableDefForJavascript($table_def) . ';</script>';
	$html .= createHiddenInput('complete_location', $complete_location);
	$html .= createHiddenInput('cancel_location', $cancel_location);
	//close the form
	$html .= '</form>' .newline();
	//drop the array out for form validation
	//$json_table_def = json_encode($table_def);
	$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
	$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
	$html .= '<script>needToConfirm = true;</script>';
	
	
	return $html;
}

?>
