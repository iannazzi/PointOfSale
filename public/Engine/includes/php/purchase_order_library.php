<?php
function updatePOCreditMemoNumbers($pos_purchase_order_id, $credit_memo_number)
{
	$key_val_id['pos_purchase_order_id'] = $pos_purchase_order_id;
	if(getCreditMemoNumber($pos_purchase_order_id) == '')
	{
		$update['credit_memo_invoice_number'] = scrubInput($credit_memo_number);
	}
	else
	{
		$update['credit_memo_invoice_number'] = scrubInput($current_credit_numbers .';' . $credit_memo_number);
	}
	return simpleUpdateSQL('pos_purchase_orders', $key_val_id, $update);
}
function tryToClosePO($pos_purchase_order_id)
{
	//to close the po
	// Receive must be complete
	// Need an invoice amount applied that matches the amount ordered
	// RA request must have a credit memo
	
	//if received status = COMPLETE && invoice_total = goods_received
	$sql = "SELECT received_status, ra_required, credit_memo_required FROM pos_purchase_orders WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	$result = getSQL($sql);
	
	if ($result[0]['received_status'] == 'COMPLETE' && $result[0]['ra_required'] == 0 && $result[0]['credit_memo_required'] == 0)
	{
		setPOStatus($pos_purchase_order_id, 'CLOSED');
		return 'CLOSED';
	}
	else
	{
		setPOStatus($pos_purchase_order_id, 'OPEN');
		return 'OPEN';
	}
}
function setPOStatus($pos_purchase_order_id, $purchase_order_status)
{
	/* PO status can be INIT, OPEN, CLOSED or DRAFT or DELETED*/
	$po_sql = "UPDATE pos_purchase_orders SET purchase_order_status = '" . $purchase_order_status ."'  WHERE pos_purchase_order_id=$pos_purchase_order_id LIMIT 1";
	$result = updateSQL($po_sql);
	return $result;
}
function setOrderStatus($pos_purchase_order_id, $order_status)
{
	/* PO status can be INIT, OPEN, CLOSED or DRAFT */
	$po_sql = "UPDATE pos_purchase_orders SET ordered_status = '" . $order_status ."'  WHERE pos_purchase_order_id=$pos_purchase_order_id LIMIT 1";
	$result = updateSQL($po_sql);
	return $result;
}
function setPurchaseOrderPlacedDate($pos_purchase_order_id, $date)
{
	$po_sql = "UPDATE pos_purchase_orders SET placed_date = '" . $date ."'  WHERE pos_purchase_order_id=$pos_purchase_order_id LIMIT 1";
	$result = updateSQL($po_sql);
	return $result;
}
function setPurchaseOrderStatusToDELETED($pos_purchase_order_id)
{
		//deletePurchaseOrderContents($pos_purchase_order_id);
		$sql = "UPDATE pos_purchase_orders SET purchase_order_status = 'DELETED'  WHERE pos_purchase_order_id=$pos_purchase_order_id LIMIT 1";
		$result = updateSQL($sql);
		return $result;
}
function createPRStatusSelect($name, $purchase_return_status, $option_all = 'off', $select_events = '')
{
	$status_options = getPRStatusOptions();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Status</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($purchase_return_status  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Status</option>';
	}
	for($i = 0;$i < sizeof($status_options); $i++)
	{
		$html .= '<option value="' . $status_options[$i] . '"';
		
		if ( ($status_options[$i] == $purchase_return_status) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $status_options[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}

function createPOStatusSelect($name, $purchase_order_status, $option_all = 'off', $select_events = '')
{
	$status_options = getPOStatusOptions();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Status</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($purchase_order_status  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Status</option>';
	}
	for($i = 0;$i < sizeof($status_options); $i++)
	{
		$html .= '<option value="' . $status_options[$i] . '"';
		
		if ( ($status_options[$i] == $purchase_order_status) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $status_options[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createPRShipStatusSelect($name, $ship_status, $option_all = 'off', $select_events = '')
{
	$status_options = getPOShipStatusOptions();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Status</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($ship_status  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Status</option>';
	}
	for($i = 0;$i < sizeof($status_options); $i++)
	{
		$html .= '<option value="' . $status_options[$i] . '"';
		
		if ( ($status_options[$i] == $ship_status) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $status_options[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}

function createPOOrderedStatusSelect($name, $ordered_status, $option_all = 'off', $select_events = '')
{
	$status_options = getPOOrderedStatusOptions();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Status</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($purchase_order_status  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Status</option>';
	}
	for($i = 0;$i < sizeof($status_options); $i++)
	{
		$html .= '<option value="' . $status_options[$i] . '"';
		
		if ( ($status_options[$i] == $ordered_status) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $status_options[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function loadPurchaseOrderContents($pos_purchase_order_id, $size_chart)
{
	$tbody_data = array();
	// this function will send back an array that matches the table
	$purchase_order_dbase_table = getPurchaseOrderData($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $purchase_order_dbase_table['pos_manufacturer_brand_id'];
	$purchase_order_contents = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	for ($row = 0;$row<sizeof($purchase_order_contents); $row++)
	{
		$tbody_row = $purchase_order_contents[$row]['poc_row_number']; 
		$c=0;
		$tbody_data[$tbody_row][$c] = 'off';$c++;
		$tbody_data[$tbody_row][$c] = $purchase_order_contents[$row]['style_number'];$c++;
		$tbody_data[$tbody_row][$c] = $purchase_order_contents[$row]['color_code'];$c++;
		$tbody_data[$tbody_row][$c] = $purchase_order_contents[$row]['color_description'];$c++;
		$tbody_data[$tbody_row][$c] = $purchase_order_contents[$row]['title'];$c++;		
		$tbody_data[$tbody_row][$c] = $purchase_order_contents[$row]['pos_category_id'];$c++;
		//now we need to find out if there is a cup
		if ($size_chart['bln_cup'] == 1) 
		{
			$tbody_data[$tbody_row][$c] = $purchase_order_contents[$row]['cup'];$c++;
		}
		//inseam
		if ($size_chart['bln_inseam'] == 1)
		{
			 $tbody_data[$tbody_row][$c] = $purchase_order_contents[$row]['inseam'];$c++;
		}
		if(isset($size_chart['attributes']) && sizeof($size_chart['attributes'])>0)
		{
			$attributes = explode('::',$purchase_order_contents[$row]['attributes']);
			//preprint($attributes);
			for($atr=0;$atr<sizeof($attributes);$atr++)
			{
				/*if($attributes[$atr] != '')
				{
					$tbody_data[$tbody_row][$c] = $attributes[$atr];$c++;
				}*/
				$tbody_data[$tbody_row][$c] = $attributes[$atr];$c++;
			}
		}
			
		//now the size - only one in this row data, we need to find the correct column...
		//keep in mind the size chart may have changed - how should we should check for that???
		if ($purchase_order_contents[$row]['quantity_ordered']== '0' && $purchase_order_contents[$row]['size_column']=='')
		{
			$tbody_data[$tbody_row][$purchase_order_contents[$row]['size_column']+$c] = '';
		}
		else
		{
			$tbody_data[$tbody_row][$purchase_order_contents[$row]['size_column']+$c] = $purchase_order_contents[$row]['quantity_ordered'];
		}			
		$c = $c+$size_chart['num_sizes'];
		$tbody_data[$tbody_row][$c] = 0;$c++;
		$tbody_data[$tbody_row][$c] = round($purchase_order_contents[$row]['cost'],2);$c++;
		$tbody_data[$tbody_row][$c] = round($purchase_order_contents[$row]['retail'],2);$c++;
		$tbody_data[$tbody_row][$c] = 0;$c++;
		
		
		$tbody_data[$tbody_row][$c] = parse_json_newlines($purchase_order_contents[$row]['comments']);$c++;
		$tbody_data[$tbody_row][$c] = $purchase_order_contents[$row]['size_row'];
		$number_of_columns = $c;
	}
	//
	//now add the quantity
	for($i=0;$i<sizeof($tbody_data);$i++)
	{
		for($j=0;$j<$number_of_columns;$j++)
		{
			if(!isset($tbody_data[$i][$j]))
			{
				$tbody_data[$i][$j]='';
			}
			
		}
		ksort($tbody_data[$i]);
	}
	return $tbody_data;
}
function loadPurchaseReturnContents($pos_purchase_return_id, $size_chart)
{
	$tbody_data = array();
	// this function will send back an array that matches the table
	$purchase_return_dbase_table = getPurchaseReturnData($pos_purchase_return_id);
	$pos_manufacturer_brand_id = $purchase_order_dbase_table['pos_manufacturer_brand_id'];
	$purchase_return_contents = getPurchaseContents($pos_purchase_return_id);
	for ($row = 0;$row<sizeof($purchase_return_contents); $row++)
	{
		$tbody_row = $purchase_return_contents[$row]['poc_row_number']; 
		$c=0;
		$tbody_data[$tbody_row][$c] = 'off';$c++;
		$tbody_data[$tbody_row][$c] = $purchase_return_contents[$row]['style_number'];$c++;
		$tbody_data[$tbody_row][$c] = $purchase_return_contents[$row]['color_code'];$c++;
		$tbody_data[$tbody_row][$c] = $purchase_return_contents[$row]['color_description'];$c++;
		$tbody_data[$tbody_row][$c] = $purchase_return_contents[$row]['title'];$c++;		
		$tbody_data[$tbody_row][$c] = $purchase_return_contents[$row]['pos_category_id'];$c++;
		//now we need to find out if there is a cup
		if ($size_chart['bln_cup'] == 1) 
		{
			$tbody_data[$tbody_row][$c] = $purchase_return_contents[$row]['cup'];$c++;
		}
		//inseam
		if ($size_chart['bln_inseam'] == 1)
		{
			 $tbody_data[$tbody_row][$c] = $purchase_return_contents[$row]['inseam'];$c++;
		}
		//now the size - only one in this row data, we need to find the correct column...
		//keep in mind the size chart may have changed - how should we should check for that???
		if ($purchase_return_contents[$row]['quantity_ordered']== '0' && $purchase_return_contents[$row]['size_column']=='')
		{
			$tbody_data[$tbody_row][$purchase_return_contents[$row]['size_column']+$c] = '';
		}
		else
		{
			$tbody_data[$tbody_row][$purchase_return_contents[$row]['size_column']+$c] = $purchase_return_contents[$row]['quantity_ordered'];
		}			
		$c = $c+$size_chart['num_sizes'];
		$tbody_data[$tbody_row][$c] = 0;$c++;
		$tbody_data[$tbody_row][$c] = round($purchase_return_contents[$row]['cost'],2);$c++;
		$tbody_data[$tbody_row][$c] = round($purchase_return_contents[$row]['retail'],2);$c++;
		$tbody_data[$tbody_row][$c] = 0;$c++;
		$tbody_data[$tbody_row][$c] = parse_json_newlines($purchase_return_contents[$row]['comments']);$c++;
		$tbody_data[$tbody_row][$c] = $purchase_return_contents[$row]['size_row'];
		$number_of_columns = $c;
	}
	//
	//now add the quantity
	for($i=0;$i<sizeof($tbody_data);$i++)
	{
		for($j=0;$j<$number_of_columns;$j++)
		{
			if(!isset($tbody_data[$i][$j]))
			{
				$tbody_data[$i][$j]='';
			}
			
		}
		ksort($tbody_data[$i]);
	}
	return $tbody_data;
}
function loadStoredSizeChart($pos_purchase_order_id)
{
/*
*	returns this
$size_chart_array = array(
			'num_sizes' => $num_sizes,
			'num_size_rows' => $num_size_rows,
			'bln_cup' => $bln_cup,
			'bln_inseam' => $bln_inseam,
			'size_categories' => $size_category_ids,
			'pos_manufacturer_brand_size_id' => $brand_size_ids,
			'sizes' => getBrandSizes($pos_manufacturer_brand_id));
			
*/
	$dbc = openPOSDatabase();
	$stored_size_chart_sql = "SELECT stored_size_chart FROM pos_purchase_orders WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	$stored_size_chart_r = @mysqli_query ($dbc, $stored_size_chart_sql);
	$stored_size_chart =  convert_mysql_result_to_array($stored_size_chart_r);
	//need to convert this data to something useful
	$size_chart_array=json_decode($stored_size_chart[0]['stored_size_chart'], true);

	/* the loaded size chart is in this format.....
	$size_chart_array = array(
			'num_sizes' => $num_sizes,
			'num_size_rows' => $num_size_rows,
			'bln_cup' => $bln_cup,
			'bln_inseam' => $bln_inseam,
			'size_categories' => $size_category_ids,
			'pos_manufacturer_brand_size_id' => $brand_size_ids,
			'sizes' => getBrandSizes($pos_manufacturer_brand_id));*/
			
	mysqli_close($dbc);
	return $size_chart_array;
}
function loadStoredSizeChartfromPR($pos_purchase_return_id)
{
/*
*	returns this
$size_chart_array = array(
			'num_sizes' => $num_sizes,
			'num_size_rows' => $num_size_rows,
			'bln_cup' => $bln_cup,
			'bln_inseam' => $bln_inseam,
			'size_categories' => $size_category_ids,
			'pos_manufacturer_brand_size_id' => $brand_size_ids,
			'sizes' => getBrandSizes($pos_manufacturer_brand_id));
			
*/
	$dbc = openPOSDatabase();
	$stored_size_chart_sql = "SELECT stored_size_chart FROM pos_purchase_returns WHERE pos_purchase_return_id = '$pos_purchase_return_id'";
	$stored_size_chart_r = @mysqli_query ($dbc, $stored_size_chart_sql);
	$stored_size_chart =  convert_mysql_result_to_array($stored_size_chart_r);
	//need to convert this data to something useful
	$size_chart_array=json_decode($stored_size_chart[0]['stored_size_chart'], true);

	/* the loaded size chart is in this format.....
	$size_chart_array = array(
			'num_sizes' => $num_sizes,
			'num_size_rows' => $num_size_rows,
			'bln_cup' => $bln_cup,
			'bln_inseam' => $bln_inseam,
			'size_categories' => $size_category_ids,
			'pos_manufacturer_brand_size_id' => $brand_size_ids,
			'sizes' => getBrandSizes($pos_manufacturer_brand_id));*/
			
	mysqli_close($dbc);
	return $size_chart_array;
}
function checkForSystemStyles($style_numbers, $pos_manufacturer_brand_id)
{
	$rows_with_system_styles = array();
	for($i=0;$i<sizeof($style_numbers);$i++)
	{
		$style_number = $style_numbers[$i][1];
		
		$style_sql = "SELECT * FROM pos_products WHERE pos_manufacturer_brand_id = '$pos_manufacturer_brand_id' AND style_number = '$style_number'";
		$array = getSQL($style_sql);
		//var_dump($array);
		if (sizeof($array) == 1)
		{
			$rows_with_system_styles[$i] = 'pos';	
		}
		else
		{
			$rows_with_system_styles[$i] = 'custom';
		}
		
	}
	
	return $rows_with_system_styles;
	
}
function sumPurchaseOrder($pos_purchase_order_id)
{
	$dbc = openPOSDatabase();
	$total_sql = "SELECT sum(cost*quantity_ordered) FROM pos_purchase_order_contents WHERE
				pos_purchase_order_id ='$pos_purchase_order_id'";
	$total_result = @mysqli_query ($dbc, $total_sql);
	$total_row = convert_mysql_result_to_array($total_result);
	mysqli_close($dbc);
	return round($total_row[0]['sum(cost*quantity_ordered)'],2);
}
function sumPurchaseOrderDiscounts($pos_purchase_order_id)
{
	$sql = "SELECT sum(discount*quantity_ordered) FROM pos_purchase_order_contents WHERE
				pos_purchase_order_id ='$pos_purchase_order_id'";
	return getSingleValueSQL($sql);
}
function sumPurchaseOrderGrandTotal($pos_purchase_order_id)
{
	$sql = "SELECT sum(cost*quantity_ordered) - sum(discount*discount_quantity)  FROM pos_purchase_order_contents WHERE
				pos_purchase_order_id ='$pos_purchase_order_id'";
	return getSingleValueSQL($sql);
}
function sumPurchaseOrderRow($pos_purchase_order_id, $row)
{
	$total_row_sql = "SELECT sum(cost*quantity_ordered) FROM pos_purchase_order_contents WHERE
				pos_purchase_order_id ='$pos_purchase_order_id' AND poc_row_number = '$row'";
	$total_array = getSQL($total_row_sql);
	return round($total_array[0]['sum(cost*quantity_ordered)'],2);
}
function sumPurchaseOrderQuantityRow($pos_purchase_order_id, $row)
{
	$qty_row_sql = "SELECT sum(quantity_ordered) FROM pos_purchase_order_contents WHERE
				pos_purchase_order_id ='$pos_purchase_order_id' AND poc_row_number = '$row'";
	$qty_array = getSQL($qty_row_sql);
	return $qty_array[0]['sum(quantity_ordered)'];
}
function sumPurchaseOrderQuantity($pos_purchase_order_id)
{
	$qty_sql = "SELECT sum(quantity_ordered) FROM pos_purchase_order_contents WHERE
				pos_purchase_order_id ='$pos_purchase_order_id'";
	$qty_array = getSQL($qty_sql);
	return $qty_array[0]['sum(quantity_ordered)'];
}
function sumPurchaseOrderDiscountsQuantity($pos_purchase_order_id)
{
	$sql = "SELECT sum(discount_quantity) FROM pos_purchase_order_contents WHERE
				pos_purchase_order_id ='$pos_purchase_order_id'";
	$sql = "SELECT sum(quantity_ordered) FROM pos_purchase_order_contents WHERE
				pos_purchase_order_id ='$pos_purchase_order_id'";
	return getSingleValueSQL($sql);
}
function checkForValidPO_ID($pos_purchase_order_id)
{
	//Check to see that it is valid
	$purchase_order_q = "SELECT * FROM pos_purchase_orders WHERE pos_purchase_order_id=$pos_purchase_order_id";	
	return checkSQLIfExists($purchase_order_q);
}
function getAllPOids()
{
	$sql = "SELECT pos_purchase_order_id FROM pos_purchase_orders";
	return getSQL($sql);
}
function getPONUmber($pos_purchase_order_id)
{
	$po = getPurchaseOrderData($pos_purchase_order_id);
	return $po['purchase_order_number'];
}

function getUserFromPO($pos_purchase_order_id)
{
	$po = getPurchaseOrderData($pos_purchase_order_id);
	$employee['email'] = getUserEmail($po['pos_user_id']);
	$employee['full_name'] = getUserFullName($po['pos_user_id']);
	return $employee;
}
function getStoreIDFromPO($pos_purchase_order_id)
{
	$po = getPurchaseOrderDataInArray($pos_purchase_order_id);
	return $po[0]['pos_store_id'];
}
function getTransactionStoreIDFromPO($dbc, $pos_purchase_order_id)
{
	$po = getTransactionPurchaseOrderDataInArray($dbc,$pos_purchase_order_id);
	return $po[0]['pos_store_id'];
}
function getBrandIdFromPOId($pos_purchase_order_id)
{
	$po = getPurchaseOrderData($pos_purchase_order_id);
	return $po['pos_manufacturer_brand_id'];
}
function getManufacturerIdFromPOId($pos_purchase_order_id)
{
	$po = getPurchaseOrderData($pos_purchase_order_id);
	return getManufacturerIdFromBrandId($po['pos_manufacturer_brand_id']);
}
function getPurchaseOrderData($pos_purchase_order_id)
{
	$dbc = openPOSDatabase();
	//Retrieve the purchase order information
	$pos_purchase_order_q = "SELECT pos_manufacturer_id, pos_manufacturer_brand_id, pos_user_id, pos_store_id, po_title,purchase_order_number,manufacturer_purchase_order_number,delivery_date,cancel_date FROM pos_purchase_orders WHERE pos_purchase_order_id='$pos_purchase_order_id'";		
	$pos_purchase_order_r = @mysqli_query ($dbc, $pos_purchase_order_q);
	$pos_purchase_order_row = mysqli_fetch_array ($pos_purchase_order_r, MYSQLI_ASSOC);
	mysqli_close($dbc);
	return $pos_purchase_order_row;
}
function getPurchaseOrderDataInArray($pos_purchase_order_id)
{
	$pos_purchase_order_q = "SELECT pos_manufacturer_id, pos_manufacturer_brand_id, pos_user_id, pos_store_id, po_title,purchase_order_number,manufacturer_purchase_order_number,delivery_date,cancel_date,comments FROM pos_purchase_orders WHERE pos_purchase_order_id='$pos_purchase_order_id'";	
	return getSQL($pos_purchase_order_q);
}
function getAllPurchaseOrderData($pos_purchase_order_id)
{
	$pos_purchase_order_q = "SELECT * FROM pos_purchase_orders WHERE pos_purchase_order_id='$pos_purchase_order_id'";	
	return getSQL($pos_purchase_order_q);
}
function getTransactionPurchaseOrderDataInArray($dbc, $pos_purchase_order_id)
{
	$pos_purchase_order_q = "SELECT pos_manufacturer_id, pos_manufacturer_brand_id, pos_user_id, pos_store_id, po_title,purchase_order_number,manufacturer_purchase_order_number,delivery_date,cancel_date FROM pos_purchase_orders WHERE pos_purchase_order_id='$pos_purchase_order_id'";	
	return getTransactionSQL($dbc, $pos_purchase_order_q);
}
function getPOCSize($pos_purchase_order_content_id)
{
	$sql = "SELECT cup, inseam, size FROM pos_purchase_order_contents WHERE pos_purchase_order_content_id = '$pos_purchase_order_content_id'";
	$size = getSQL($sql);
	$size_string = $size[0]['size'].$size[0]['cup'].$size[0]['inseam'];
	return $size_string;
}
function getPOCCost($pos_purchase_order_content_id)
{
	$sql = "SELECT cost FROM pos_purchase_order_contents WHERE pos_purchase_order_content_id = '$pos_purchase_order_content_id'";
	return getSingleValueSQL($sql);
}

function getPurchaseOrderContents($pos_purchase_order_id)
{
	$purchase_order_contents_sql = "
	SELECT *
	FROM pos_purchase_order_contents 
	WHERE pos_purchase_order_id = '$pos_purchase_order_id'
	ORDER BY pos_purchase_order_content_id ASC";
	return getSQL($purchase_order_contents_sql);
}
function getPurchaseOrderIdFromPOCId($pos_purchase_order_content_id)
{
	$sql = "SELECT pos_purchase_order_id FROM pos_purchase_order_contents WHERE pos_purchase_order_content_id = $pos_purchase_order_content_id";
	return getSingleValueSQL($dbc, $sql);
}	
function getTransactionPurchaseOrderIdFromPOCId($dbc, $pos_purchase_order_content_id)
{
	$sql = "SELECT pos_purchase_order_id FROM pos_purchase_order_contents WHERE pos_purchase_order_content_id = $pos_purchase_order_content_id";
	return getTransactionSingleValueSQL($dbc, $sql);
}	
function getPurchaseOrderContentsInArray($pos_purchase_order_id)
{
	$purchase_order_contents_sql = "
	SELECT pos_purchase_order_contents.*, pos_products_sub_id.pos_product_id
	FROM pos_purchase_order_contents 
	LEFT JOIN pos_products_sub_id ON pos_purchase_order_contents.pos_product_sub_id=pos_products_sub_id.pos_product_sub_id
	WHERE pos_purchase_order_id = '$pos_purchase_order_id'
	ORDER BY pos_purchase_order_content_id ASC";
	return getSQL($purchase_order_contents_sql);	
}
function getPurchaseReturnContents($pos_purchase_return_id)
{
	$sql = "
	SELECT *
	FROM pos_purchase_return_contents 
	WHERE pos_purchase_return_id = '$pos_purchase_return_id'
	ORDER BY pos_purchase_return_content_id ASC";
	return getSQL($sql);	
}
function getPRStatusOptions()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_purchase_returns'
AND COLUMN_NAME = 'purchase_return_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function getPOStatusOptions()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_purchase_orders'
AND COLUMN_NAME = 'purchase_order_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function getPOShipStatusOptions()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_purchase_returns'
AND COLUMN_NAME = 'ship_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function getPOOrderedStatusOptions()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_purchase_orders'
AND COLUMN_NAME = 'ordered_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function  getAccountID($pos_purchase_order_id)
{
	$pos_manufacturer_id = getManufacturerIdFromPOId($pos_purchase_order_id);
	
	//ok - we are trying to get the 'account id' for the goods received
	// this can be a cc/csh/ on account etc....
	//there might be an invoice, there might not be.
	//This will tell me the account if we already have the invoice entered. 
	$sql = "SELECT pos_account_id FROM pos_purchases_journal WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	//this will tell me an account if we have one set up - so not often
	$sql = "SELECT pos_account_id FROM pos_manufacturers WHERE pos_manufacturer_id = '$pos_manufacturer_id'";
	
	//So the answer is I don't really know what the account is when receiving goods.
	
}
function getPurchaseReturnOrderReturnedItems($dbc, $pos_purchase_order_id, $pos_product_sub_id)
{
	$sql = "SELECT pos_purchase_returns.pos_purchase_return_id, (SELECT sum(pos_purchase_return_contents.quantity_returned) FROM pos_purchase_return_contents WHERE pos_purchase_return_contents.pos_purchase_return_id = pos_purchase_returns.pos_purchase_return_id AND pos_purchase_return_contents.pos_product_sub_id =$pos_product_sub_id) AS quantity_returned FROM pos_purchase_returns  WHERE pos_purchase_returns.pos_purchase_order_id = $pos_purchase_order_id AND pos_purchase_returns.submit_status !='NOT SUBMITTED'";
	$result = getTransactionSQL($dbc, $sql);
	$sum = 0;
	for($i=0;$i<sizeof($result);$i++)
	{
		$sum = $sum + $result[$i]['quantity_returned'];
	}
	
	return $sum;
	
}
?>