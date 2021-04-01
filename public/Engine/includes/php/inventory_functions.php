<?php





function updatePOCQuantityReturned($dbc,$poc_array)
{
	/*	ex:
	$mysql_data_array[0]['db_field'] = 'cost';
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['data_array']['3789'] = 30.75;
	$mysql_data_array[0]['data_array']['3790'] = 40.75;
	$mysql_data_array[1]['db_field'] = 'retail';
	$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
	$mysql_data_array[1]['data_array']['3789'] = 60.75;
	$mysql_data_array[1]['data_array']['3790'] = 80.75;
	*/
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['db_field'] = 'quantity_returning';
	for($i=0;$i<sizeof($poc_array);$i++)
	{
		$mysql_data_array[0]['data_array'][$poc_array[$i]['pos_purchase_order_content_id']] = $poc_array[$i]['quantity_returning'];
	}
	return runTransactionSQL($dbc,arrayUpdateSQLString('pos_purchase_order_contents', $mysql_data_array));
}
function getReceivedDateQty($dbc, $pos_purchase_order_content_id)
{
	$qty_array = getTransactionSQL($dbc, "SELECT received_date_qty FROM pos_purchase_order_contents WHERE
							pos_purchase_order_content_id = '$pos_purchase_order_content_id'");
	return $qty_array[0]['received_date_qty'];
}

function getQuantityReturn($dbc, $pos_purchase_order_content_id)
{
	$qty_array = getTransactionSQL($dbc, "SELECT quantity_returning FROM pos_purchase_order_contents WHERE
							pos_purchase_order_content_id = '$pos_purchase_order_content_id'");
	return $qty_array[0]['quantity_returning'];
}
function updatePOCQuantityCanceled($dbc, $poc_array)
{
	/*	ex:
	$mysql_data_array[0]['db_field'] = 'cost';
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['data_array']['3789'] = 30.75;
	$mysql_data_array[0]['data_array']['3790'] = 40.75;
	$mysql_data_array[1]['db_field'] = 'retail';
	$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
	$mysql_data_array[1]['data_array']['3789'] = 60.75;
	$mysql_data_array[1]['data_array']['3790'] = 80.75;
	*/
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['db_field'] = 'quantity_canceled';
	for($i=0;$i<sizeof($poc_array);$i++)
	{
		$mysql_data_array[0]['data_array'][$poc_array[$i]['pos_purchase_order_content_id']] = $poc_array[$i]['quantity_canceled'];
	}
	return arrayTransactionUpdateSQL($dbc, 'pos_purchase_order_contents', $mysql_data_array);
}
function getQuantityCanceled($dbc,$pos_purchase_order_content_id)
{
	$qty_array = getTransactionSQL($dbc, "SELECT quantity_canceled FROM pos_purchase_order_contents WHERE
							pos_purchase_order_content_id = '$pos_purchase_order_content_id'");
	return $qty_array[0]['quantity_canceled'];
}
function updatePOCQuantityDamaged($dbc, $poc_array)
{
	/*	ex:
	$mysql_data_array[0]['db_field'] = 'cost';
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['data_array']['3789'] = 30.75;
	$mysql_data_array[0]['data_array']['3790'] = 40.75;
	$mysql_data_array[1]['db_field'] = 'retail';
	$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
	$mysql_data_array[1]['data_array']['3789'] = 60.75;
	$mysql_data_array[1]['data_array']['3790'] = 80.75;
	*/
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['db_field'] = 'quantity_damaged';
	for($i=0;$i<sizeof($poc_array);$i++)
	{
		$mysql_data_array[0]['data_array'][$poc_array[$i]['pos_purchase_order_content_id']] = $poc_array[$i]['quantity_damaged'];
	}
	return runTransactionSQL($dbc,arrayUpdateSQLString('pos_purchase_order_contents', $mysql_data_array));
}

function updateQuantityDamaged($pos_purchase_order_content_id, $qty)
{
	$id['pos_purchase_order_content_id'] = $pos_purchase_order_content_id;
	$mysql_data['quantity_damaged'] = $qty;
	return simpleUpdateSQL('pos_purchase_order_contents', $id, $mysql_data);
}
function getQuantityDamagedReceived($dbc,$pos_purchase_order_content_id)
{
	$qty_array = getTransactionSQL($dbc, "SELECT quantity_damaged FROM pos_purchase_order_contents WHERE
							pos_purchase_order_content_id = '$pos_purchase_order_content_id'");
	return $qty_array[0]['quantity_damaged'];
}
function updateQuantityDamagedReceived($pos_purchase_order_content_id, $qty)
{
	$id['pos_purchase_order_content_id'] = $pos_purchase_order_content_id;
	$mysql_data['quantity_damaged'] = $qty;
	return simpleUpdateSQL('pos_purchase_order_contents', $id, $mysql_data);
}
//unused
function updateSimpleInventoryAvailableQtySQLString($dbc,$inventory_array)
{
	/*
		this one returns an array of sql statemetns
		pass in this:
		$inventory_array[0]['available_qty'] = x
		$inventory_array[0]['pos_purchase_order_content_id'] = y
	*/
	$pos_purchase_order_id = getTransactionPurchaseOrderIdFromPOCId($dbc, $inventory_array[0]['pos_purchase_order_content_id']);
	$pos_store_id = getTransactionStoreIDFromPO($dbc, $pos_purchase_order_id);
	for($i=0;$i<sizeof($inventory_array);$i++)
	{
		$pos_purchase_order_content_id = $inventory_array[$i]['pos_purchase_order_content_id'];
		$simple_inventory[$i]['pos_product_sub_id'] = getTransactionProductSubIDFromName($dbc,getTransactionProductSubIDFROMPOC($dbc,$pos_purchase_order_content_id));
		$current_available_qty = getTransactionSimpleAvailableInventoryQTYInStore($dbc, $simple_inventory[$i]['pos_product_sub_id'], $pos_store_id);
		$simple_inventory[$i]['available_qty'] = $current_available_qty + $inventory_array[$i]['available_qty'];
		$simple_inventory[$i]['pos_store_id'] = $pos_store_id;
		$sql[$i] = simpleInsertOnDuplicateUpdateSQLString('pos_merchandise_inventory_simple', $simple_inventory[$i]);
	}
	return $sql;
}
function updateSimpleInventoryDamagedQtySQLString($dbc,$inventory_array)
{
	/*
		this one returns an array of sql statemetns
		pass in this:
		$inventory_array[0]['available_qty'] = x
		$inventory_array[0]['pos_purchase_order_content_id'] = y
	*/
	$pos_purchase_order_id = getTransactionPurchaseOrderIdFromPOCId($dbc, $inventory_array[0]['pos_purchase_order_content_id']);
	$pos_store_id = getTransactionStoreIDFromPO($dbc, $pos_purchase_order_id);
	for($i=0;$i<sizeof($inventory_array);$i++)
	{
		$pos_purchase_order_content_id = $inventory_array[$i]['pos_purchase_order_content_id'];
		$simple_inventory[$i]['pos_product_sub_id'] = getTransactionProductSubIDFromName($dbc,getTransactionProductSubIDFROMPOC($dbc,$pos_purchase_order_content_id));
		$current_damaged_qty = getTransactionSimpleDamagedInventoryQTYInStore($dbc, $simple_inventory[$i]['pos_product_sub_id'], $pos_store_id);
		$simple_inventory[$i]['damaged_qty'] = $current_damaged_qty + $inventory_array[$i]['damaged_qty'];
		$simple_inventory[$i]['pos_store_id'] = $pos_store_id;
		$sql[$i] = simpleInsertOnDuplicateUpdateSQLString('pos_merchandise_inventory_simple', $simple_inventory[$i]);
	}
	return $sql;
}
function productLookUpTable()
{
	$html =  '<TABLE >';
	$html .= '<tr>'.newline();
	//$html .= '<td style="vertical-align:bottom;">'.newline();
	$html .= createBarcodeHTMLTable();
	//$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;">'.newline();
	$html .= '- OR -';
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;">'.newline();
	$html .= createSimpleHorzontalHTMLTable(createProductSubIDLookupTableDef());
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;">'.newline();
	$html .= '<input class = "button2" type="button" style="width:80px" name="add_prodcut_subid" value="Add" onclick="addProductSubId()"/>';
	$html .= '</td>'.newLine();
	$html .= '</tr>'.newline();
	$html .=  '</table>';
	$html .= addBeepV3().newline();
	return $html;


}
//product lookup stuff
function createBarcodeHTMLTable()
{
	$html = ' <TD style="vertical-align:bottom;"><INPUT TYPE="TEXT" class="lined_input"  id="barcode" style = "background-color:yellow;width:300px;" NAME="barcode" onclick="this.select()" onKeyPress="return disableEnterKey(event)" onKeyDown="lookUpBarcodeID(this, event)"	/></td>';
	$html .= '<td style="vertical-align:bottom;"><input class = "button2" type="button" style="width:80px;" name="add_barcode" value="Add" onclick="addBarcodeButton()"/></td>';
	
	return $html;
}
function createProductSubIDLookupTableDef()
{
		return array(	
			
					array(
							'th' => 'Brand',
							'db_field' => 'pos_manufacturer_brand_id_lookup',
							'type' => 'select',
							'html' => createManufacturerBrandSelect('pos_manufacturer_brand_id_lookup', 'false',  'off', ' onchange="UpdateBrandData()" onkeypress = "return noEnter(event);"  ')),
					array(
							'th' => 'Style Number',
							'db_field' => 'style_number_lookup',
							'type' => 'select',
							'html' => createBlankSelect('style_number_lookup',' onchange="UpdateStyleData()" onkeypress = "return noEnter(event);"  ')),
					array(	'th' => 'Color Code',
							'db_field' => 'color_code_lookup',
							'type' => 'select',
							'html' => createBlankSelect('color_code_lookup',' onchange="UpdateColorCodeData()" onkeypress = "return noEnter(event);" ')),
					array(	'th' => 'Size',
							'db_field' => 'size_lookup',
							'type' => 'select',
							'html' => createBlankSelect('size_lookup',' onchange="UpdateSizeData()" onkeypress = "return noEnter(event);" ')),
					/*array(	'th' => 'Product Sub Id Name',
							'db_field' => 'product_subid_manual_lookup',
							'type' => 'select',
							'html' => createBlankSelect('product_subid_manual_lookup',' '))*/


					);
}
?>