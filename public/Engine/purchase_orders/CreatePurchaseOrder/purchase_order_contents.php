<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
/*
	*purchase_order_contents.php
	*Craig Iannazzi 2-2-2012
	*This file will set up for display of the purchase order
*/

require_once ('../po_functions.php');
$po_javascript_version = 'purchase_order_contents.2015.05.11.js';

$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$page_title = 'PO # ' . $pos_purchase_order_id ;
$table = 'pos_purchase_orders';
$key_val_id['pos_purchase_order_id'] = $pos_purchase_order_id;
$complete_location = POS_ENGINE_URL . '/purchase_orders/CreatePurchaseOrder/purchase_order_contents.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = POS_ENGINE_URL . '/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
check_lock($table, $key_val_id,$complete_location, $cancel_location);

if (checkForValidPO_id($pos_purchase_order_id))
{
	//lock the entry
	lock_entry($table, $key_val_id);

	$complete_location = "../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id=".$pos_purchase_order_id;
	$pos_purchase_order_row = getPurchaseOrderOverview($pos_purchase_order_id);
	$pos_manufacturer_id = $pos_purchase_order_row[0]['pos_manufacturer_id'];
	$pos_manufacturer_brand_id = $pos_purchase_order_row[0]['pos_manufacturer_brand_id'];
	// Get the manufacturer information
	$selected_manufacturer = getManufacturer($pos_manufacturer_id);
	$selected_brand = getBrand($pos_manufacturer_brand_id);
	$brand_code = $selected_brand[0]['brand_code'];
	$brand_size_chart = selectNewOrStoredBrandSizeChart($pos_purchase_order_id);
	$pos_category_id = $pos_purchase_order_row[0]['pos_category_id'];
	//Load up some categories for javascript to use
	//$categories = getCategoryArray();
	//$categories = getNoParentCategoryArray();
	$categories = getRecusiveCategories($pos_category_id, array(), 0);
	
	$category_names = $categories['name']; //getRecusiveCategoriesNames($pos_category_id, array(), 0);
	$category_ids = $categories['pos_category_id']; //getRecusiveCategoriesIDS($pos_category_id, array(), 0);
	
	
	if ($brand_size_chart['num_sizes'] == 0)
	{
		$message=urlencode('This Manufacturer does not have a size chart setup, please create one.');
		$referring_page =POS_ENGINE_URL.'/purchase_orders/CreatePurchaseOrder/purchase_order_contents.php?pos_purchase_order_id='. $pos_purchase_order_id;
		$pos_url ='/manufacturers/EditBrandSizeChart/edit_brand_size_chart.php?pos_manufacturer_brand_id='. $pos_manufacturer_brand_id.'&message='.$message.'&referring_page='.$referring_page;
		//pos_redirect($pos_url);
		header('Location: '.POS_ENGINE_URL . $pos_url);
		//$html .= '<p> manufacturer does Not have a size chart<p>';
	}
	
	//load any previously created contents
	$tbody_data = loadPurchaseOrderContents($pos_purchase_order_id, $brand_size_chart);
	
	//set up the columns - 
	$start_columns = 6;
	$footer_colspan = 6;
	
	if ($brand_size_chart['bln_cup'] == 1) 
	{
		$footer_colspan = $footer_colspan + 1;
		$start_columns = $start_columns + 1;
	}
	if ($brand_size_chart['bln_inseam'] == 1) 
	{
		$footer_colspan = $footer_colspan + 1;
		$start_columns = $start_columns + 1;
	}
	if(isset($brand_size_chart['attributes']) && sizeof($brand_size_chart['attributes'])>0)
	{
		for($atr=0;$atr<sizeof($brand_size_chart['attributes']);$atr++)
		{
			$footer_colspan = $footer_colspan + 1;
			$start_columns = $start_columns + 1;
		}
	}


	
	$html = '<link type="text/css" href="../poStyles.css" rel="Stylesheet"/>'.newline();
	$html .=  '<script src="'.$po_javascript_version.'"></script>'.newline();
	
	$form_id = "poc_form";
	$html .=  '<form id = "' . $form_id . '" action="process_purchase_order_contents.php" method="post" onsubmit="return validatePOCForm()">';
	//this is the overview table
	
	$html .= createMiniPOOverview($pos_purchase_order_id);
	$html .=  '<div class = "poc_table_div">';
	
	$html .=  '<TABLE id="poc_table" summary="Embrasse-Moi Purchase Order Details">';
	$html .=	createPOCThead($brand_size_chart, $pos_manufacturer_brand_id);
	//this is the body which is created by javascript	
	$html .=  '	<tbody id = "poc_tbody" name = "poc_tbody"></tbody>';
	//And the footer
	$html .=  '
			<tfoot id = "poc_tfoot">
			<tr>
				<td colspan = "' . $footer_colspan . '" id = "emptyCell"></td>
				<td colspan = "' . $brand_size_chart['num_sizes'] . '">Totals:</td>
				<td name =  "poc_total_qty" id = "poc_total_qty">0</td>
				<td id = "emptyCell"></td>
				<td id = "emptyCell"></td>
				<td name = "poc_total" id = "poc_total">0</td>
				<td id = "emptyCell"></td>
			</tr>
			</tfoot>';
		$html .=  '</table>';
		$html .=  '</div>';

	$html .=  '<INPUT class = "button" type="button" style="width:60px;" value="Add Row" onclick="addRow(\'poc_tbody\')" />';
	$html .=  '<INPUT class = "button" type="button" style="width:80px;" value="Copy Row(s)" onclick="copyRow(\'poc_tbody\')" />';
	$html .=  '<INPUT class = "button" type="button" value="Move Row(s) Up" onclick="moveRowUp(\'poc_tbody\')" />';
	$html .=  '<INPUT class = "button" type="button" style="width:120px;" value="Move Row(s) Down" onclick="moveRowDown(\'poc_tbody\')" />';
	$html .=  '<INPUT class = "button" type="button" style="width:80px;" value="Delete Row(s)" onclick="deleteRow(\'poc_tbody\')" />';
	
	$html .=  '<INPUT class = "button" type="button"  style="margin: 2px 4px 6px 30px;"value="Save Draft Order" onclick="saveDraft(\'poc_tbody\')" />'.newline();
	$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Exit (Finish Later)" onclick="exit()" />'.newline();
	$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Continue To Order" onclick="saveDraftAndContinue()" />'.newline();
	//$html .=  '<INPUT class = "button" type="button" value="Submit Via Email" onclick="submitViaEmail(\''.$pos_purchase_order_id.'\')" />'.newline();
	
	$html .=  '<input class = "rightButton" type="button" name="cancel" style="width:180px;" value="Cancel Changes Since Last Save" onclick="cancelPO()"/>'.newline();
	$html .=  '<INPUT class = "rightButton" type="button" style="width:80px;" value="Destroy PO" onclick="deletePurchaseOrder(\''.$pos_purchase_order_id.'\')" />'.newline();

	//create some hidden input
	$html .= createHiddenSerializedInput('stored_size_chart', $brand_size_chart).newline();
	
	$html .=   '<input type="hidden" name="bln_cup" value="' . $brand_size_chart['bln_cup'] . '" />'.newline();
	$html .=   '<input type="hidden" name="bln_inseam" value="' . $brand_size_chart['bln_inseam'] . '" />'.newline();
	$html .=   '<input type="hidden" name="start_columns" value="' . $start_columns . '" />'.newline();
	$html .=   '<input type="hidden" name="num_sizes" value="' . $brand_size_chart['num_sizes'] . '" />'.newline();
	$html .=   '<input type="hidden" name="pos_purchase_order_id" value="' . $pos_purchase_order_id . '" />'.newline();
	$html .=   '<input type="hidden" name="brand_code" value="' . $brand_code . '" />'.newline();
	$html .= '<script> var pos_purchase_order_id = "'.  $pos_purchase_order_id .'";</script>'.newline();
	$html .= '<script>var json_tbody_data = '.  json_encode($tbody_data) .';</script>'.newline();
	$rows_with_system_styles =  json_encode(checkForSystemStyles($tbody_data, $pos_manufacturer_brand_id));
	$html .= '<script> var rows_with_system_styles = '. $rows_with_system_styles .';</script>'.newline();
	$html .= '<script> var size_category_ids = ["' .join("\", \"", $brand_size_chart['size_categories']). '"];</script>'.newline();
	$html .= '<script> var pos_manufacturer_brand_size_ids = ["' .join("\", \"", $brand_size_chart['pos_manufacturer_brand_size_id']). '"];</script>'.newline();
	$html .= '<script> var brand_size_chart = '.json_encode($brand_size_chart) .';</script>';
	$html .= '<script> var pos_manufacturer_id = "'.  $pos_manufacturer_id .'";</script>'.newline();
	$html .= '<script> var pos_manufacturer_brand_id = "'.   $pos_manufacturer_brand_id . '";</script>'.newline();
	$html .= '<script> var formID = "'.   $form_id .'";</script>'.newline();
	$html .= '<script> var num_sizes = "'.   $brand_size_chart['num_sizes'] .'";</script>'.newline();
	$html .= '<script> var bln_cup= "'.   $brand_size_chart['bln_cup'] . '";</script>'.newline();
	$html .= '<script> var bln_inseam= "'.   $brand_size_chart['bln_inseam'] . '";</script>'.newline();
	$html .= '<script> var num_size_rows= "'.   $brand_size_chart['num_size_rows'] . '";</script>'.newline();
	$html .= '<script> var start_columns= "'.   $start_columns . '";</script>'.newline();
	$html .= '<script> var complete_location = "'.$complete_location.'";</script>'.newline();
	$html .= '<script> var category_names = ["'.   join("\", \"", $category_names) .'"];</script>'.newline();
	$html .= '<script> var category_ids = ["'.   join("\", \"", $category_ids). '"]</script>'.newline();
	$html .=  '</form>';
	$html .= '<h4> HINTS: Order quantity of 0 to create the product which can then be ordered through the POS system. Generally order a quantity, even if 0, for all avalilable sizes.</h4>';
	//$html .='<p class = "error" style="font-size:0.8em" > To use the auto-style loading function, do not click on the pop-up drop down menu in the style number column. Enter the style number in full then tab off the cell. I can not seem to get this coded correctly, and this note serves as a reminder and a work-around. For the color codes I am not sure if the manufacturer color codes are loading correctly. You can view color codes in the upc viewer under manufacturers</p>';
}
else
{
	$html= 'error - not a valid ID';	
}
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);



?>
