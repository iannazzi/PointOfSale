<?php
/*
	we need to be able to reduce quantities and add items
	we cannot delete items however
*/

$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$page_title = 'Adjust PO#'.$pos_purchase_order_id;

$unlock_location = POS_ENGINE_URL . '/purchase_orders/CreatePurchaseOrder/adjust_items.php?pos_purchase_order_id='.$pos_purchase_order_id;
$complete_location = POS_ENGINE_URL . '/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = $complete_location;
$form_handler = 'adjust_items.form.handler.php';
check_lock('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id),$unlock_location, $cancel_location);


if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	
	//lock_entry('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id));
	//for updating the price we just want to display the unique product style numbers and color codes....
	
	
	//we should be able to have a table that we can dynamically edit and not loose track. 
	// the color codes, sizes should be drop down only.. no dynamic product creation....
	
	
	$data = getPurchaseOrderContentsForContentEdit($pos_purchase_order_id);
	$table_def = createAdjustPOCTableDef($pos_purchase_order_id);
	$html_table = createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	$html_table .= createDynamicTable($table_def, $data);
	$html = '<h3>Update Purchase Order Contents For Order #'.$pos_purchase_order_id.'</h3>';
	$html .= createFormForDynamicTableMYSQLInsert($table_def, $html_table, $form_handler, $complete_location, $cancel_location);
	
	
	$html .=  '<script src="adjust_items.js"></script>'.newline();
	//$html .=  '<script src="lookup_product_sub_id.js"></script>'.newline();
	//$html .= '<script>document.getElementsByName("barcode")[0].focus();</script>';
	
	


	


	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
else
{
	include (HEADER_FILE);
	echo 'error - not a valid ID';
	include (FOOTER_FILE);
}


function createAdjustPOCTableDef($pos_purchase_order_id)
{
	
	//get the style numbers
	
	$products_and_styles = getProductIdsAndStyleNumbersFromBrandId( getBrandIdFromPOId($pos_purchase_order_id));
	
	//the color code and sizes will have to update dynamically
	
	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
					array('db_field' => 'pos_product_id',
						'caption' => 'Style Number',
						'type' => 'select',
						'select_names' => $products_and_styles['style_number'],
						'select_values' => $products_and_styles['pos_product_id'],
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onmouseup' => 'function(){updateTableData(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',)),

					array('caption' => 'title',
						'db_field' => 'title',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',
												'onmouseup' => 'function(){updateTableData(this);}'
												)),
					
					array('db_field' => 'color_code',
						'caption' => 'Color Code',
						'type' => 'select',
						'individual_select_options' => 'color_options',
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',
												'onmouseup' => 'function(){updateTableData(this);}')
						),
					array('caption' => 'Size',
						'db_field' => 'size2',
						'type' => 'select',
						'individual_select_options' => 'size_options',
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',
												'onmouseup' => 'function(){updateTableData(this);}')
						),
					array('caption' => 'Quantity',
						'db_field' => 'quantity_ordered',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onmouseup' => 'function(){updateTableData(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',
												)),
					array('caption' => 'Comments',
						'db_field' => 'comments',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onmouseup' => 'function(){updateTableData(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',
												))
					
				);			
						
		
		return $columns;
	
	
	
}


function getPurchaseOrderContentsForContentEdit($pos_purchase_order_id)
{
	$tmp_sql = "
	
	CREATE TEMPORARY TABLE purchase_orders
	
	SELECT  
			pos_purchase_order_contents.*, pos_products_sub_id.pos_product_id,  (quantity_ordered*cost) as extension, concat(size,cup,inseam) as size2 FROM pos_purchase_order_contents
			LEFT JOIN pos_products_sub_id ON pos_products_sub_id.pos_product_sub_id = pos_purchase_order_contents.pos_product_sub_id WHERE pos_purchase_order_id = $pos_purchase_order_id
	
	;
	
	
	";	
	
	$tmp_select_sql = "SELECT *
		FROM purchase_orders WHERE 1";
	$dbc = openPOSdb();
	
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
	
	//now we need options => size and color for the selected product
	//individual_select_options
	for($row=0;$row<sizeof($data);$row++)
	{
		$pos_product_id = getProductIdFromProductSubId($data[$row]['pos_product_sub_id']);
		$colors = getProductColorOptions($pos_product_id);
		$sizes = getProductOptions($pos_product_id, getProductAttributeId('Size'));
		
		$data[$row]['color_options']['names'] = $colors['option_code_name'];
		$data[$row]['color_options']['values'] = $colors['option_code'];
		//$data[$row]['color_options']['value'] = getProductOptionIdFromProductSubId($pos_product_sub_id, 'Color');
		$data[$row]['size_options']['names'] = $sizes['option_name'];
		$data[$row]['size_options']['values'] = $sizes['option_code'];
		//$data[$row]['size_options']['value'] = getProductOptionIdFromProductSubId($pos_product_sub_id, 'Size');

		
		//also need the option to select:
		
	}
	
	
	
	
	
	return $data;
}