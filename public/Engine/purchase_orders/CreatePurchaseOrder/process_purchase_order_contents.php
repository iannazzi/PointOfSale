<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
/* this function will update the product price, cost, title for each POC in a PO */
$page_title = 'Process Purchase Order';
require_once('../po_functions.php');
require_once(PHP_LIBRARY);

$type = getPostOrGetDataIfAvailable('type');
$type = '';
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
	$cancel_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;

//check if we can process
$errors = checkIfPOCanCreateProducts($pos_purchase_order_id);
//$errors = array();
if (sizeof($errors)==0)
{
	//$continue_location = 'generate_product_and_sub_ids.php?pos_purchase_order_id='.$pos_purchase_order_id;
	$prepare_po_location = 'prepare_po.php?pos_purchase_order_id='.$pos_purchase_order_id;
	
	//first need to view the contents, and the mfg barcodes. Then we need a edit poc button and a continue button....	
	//not saving here because the select would not post...ahhh the disabled select box will not post!!!
	//$html = saveDraftOrder($pos_purchase_order_id);
	$future_products = getPurchaseOrderProductLinks($pos_purchase_order_id);
	$html = '<p>The Following Product Sub ID\'s were generated from the Purchase Order. If UPC data is missing it means one of two things: the UPC file is not loaded or out of date, or a product is ordered incorrectly. For example a size may have been ordered that the manufacturer does not make. Use the Edit button to fix the purchase order contents before creating the prepared order.</p><p></p>'.newline(); 
	//$html .= createHTMLTableFromMYSQLReturnArray($future_products, 'linedTable');
	
	
		$array_table_def= array(	
				
						array(
								'th' => 'Row',
								'db_field' => 'row_number',
								'type' => 'row_number'),
						array(
								'th' => 'Content ID',
								'db_field' => 'pos_purchase_order_content_id',
								'type' => 'td_hidden_input'),
						array(
								'th' => 'Style Number',
								'db_field' => 'style_number',
								'type' => 'td'),
						array(
								'th' => 'Color Code',
								'db_field' => 'color_code',
								'type' => 'td'),
						array(	'th' => 'Color Description',
								'db_field' => 'color_description',
								'type' => 'input',
								'tags' => ' style="background-color:yellow" '),
	
						array(	'th' => 'Title',
								'db_field' => 'title',
								'type' => 'input',
								'tags' => ' style="background-color:yellow" '),
						array(	'th' => 'Size',
								'db_field' => 'size',
								'type' => 'input',
								'tags' => ' style="background-color:yellow" '),
						array(	'th' => 'Size Array',
								'db_field' => 'size_array',
								'type' => 'input',
								'tags' => ' style="background-color:yellow" '),
						
						array(
								'th' => 'Existing<br>Product ID',
								'db_field' => 'pos_product_id',
								'type' => 'link',
								'get_url_link' => POS_ENGINE_URL . '/products/ViewProduct/view_product.php',
								'get_id_link' => 'pos_product_id'
								),
						array(
								'th' => 'Existing<br>Product Sub ID',
								'db_field' => 'pos_product_sub_id',
								'type' => 'link',
								'get_url_link' => POS_ENGINE_URL . '/products/ViewProduct/view_product.php',
								'get_id_link' => 'pos_product_sub_id'
								),
						array(
								'th' => 'Existing <br> Product Sub ID Name<br>(barcode)',
								'db_field' => 'existing_product_subid_name',
								'type' => 'td'),
						array(
								'th' => 'New <br>Product Sub ID Name',
								'db_field' => 'new_product_subid_name',
								'type' => 'td_hidden_input'),
						
						array(
								'th' => 'UPC',
								'db_field' => 'mfg_upc',
								'type' => 'td')
						
					
						);
		
		$html .= createStaticViewDynamicTable($array_table_def,$future_products, ' style = "width:100%;" ');
	
	
	
	
	
	
	
	
	$html .= '<form id = "process" name="print_label_form" action="process_purchase_order_contents.form.handler.php" method="post" >';
	
	//if the price is different than the product price then lets ask for update, default to update
	$update_pricing = checkifProductPricingShouldBeUpdatedFromPO($pos_purchase_order_id);
	if($update_pricing['update_retail'] == true)
	{
		$html .= '<p><input type="checkbox" checked = "checked" name="update_retail" value="Update_retail">Update Retail Pricing</p>';
	}
	if($update_pricing['update_cost'] == true)
	{
		$html .= '<p><input type="checkbox" checked = "checked" name="update_cost" value="update_cost">Update Product Cost</p>';
	}
	if ($type == 'reprocess')
	{
		$html .= createHiddenInput('type', 'reprocess');
		$html .= '<input class = "button" type="submit" name="submit" value="Re-Process"/>';
		$html .= '<INPUT class = "button" type="button" style = "width:180px" value="Do Not Process (Exit)" onclick="window.location =\''.$cancel_location . '\'" />';
	}
	else
	{
		$html .= '<p><INPUT class = "button" type="button" style = "width:180px" value="Edit Contents" onclick="window.location = \'purchase_order_contents.php?pos_purchase_order_id=' .$pos_purchase_order_id. '\'" />';
		$html .= '<input class = "button" type="submit" name="submit" value="Continue"/>';
	$html .= '<INPUT class = "button" type="button" style = "width:180px" value="Do Not Process (Exit)" onclick="window.location =\''.$cancel_location . '\'" />';
	}
	//$html .= '<INPUT class = "button" type="button" style = "width:100px" value="Continue" onclick="window.location =\''.$continue_location . '\'" />';
	
	//$html .= '<INPUT class = "button" type="button" style = "width:200px" value="Continue Without Creating Products" onclick="window.location =\''.$prepare_po_location . '\'" />';
	//$html .= '</p>';
	$html.= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	

	
	$html .='</form>';
}
else
{
	$html = printErrors($errors);
	$html .= '<p><INPUT class = "button" type="button" style = "width:180px" value="Edit Contents" onclick="window.location = \'purchase_order_contents.php?pos_purchase_order_id=' .$pos_purchase_order_id. '\'" />';
		$html .= '<INPUT class = "button" type="button" style = "width:180px" value="Do Not Process (Exit)" onclick="window.location =\''.$cancel_location . '\'" />';
}

include (HEADER_FILE);	
echo $html;
include (FOOTER_FILE);
?>