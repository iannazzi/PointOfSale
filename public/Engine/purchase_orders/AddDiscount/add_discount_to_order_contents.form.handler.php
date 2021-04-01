<?php
/*
	*Craig Iannazzi 8-02-2012
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once ('../po_functions.php');
require_once(PHP_LIBRARY);
$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
$key_val_id['pos_purchase_order_id'] = $pos_purchase_order_id;
$array_table_def = deserializeTableDef($_POST['table_def']);
$table_data = getArrayOfPostDataUsingTableDef($array_table_def);
if ($_POST['submit']=='Submit')
{
	if(sizeof($table_data)>0)
	{
		for($row=0;$row<sizeof($table_data);$row++)
		{
			$pos_purchase_order_content_id = $table_data[$row]['pos_purchase_order_content_id'];
			$update_array[$row]['pos_purchase_order_content_id'] = $pos_purchase_order_content_id;
			$update_array[$row]['discount'] = $table_data[$row]['discount'];
			$update_array[$row]['discount_quantity'] = 0;//$table_data[$row]['discount_quantity'];
		}
		$result[] = updateDiscountAmountAndQuantity($update_array);
		$message = urlencode("Discounts Added");
			$po_status = tryToClosePO($pos_purchase_order_id);

		$status = tryToCompletePurchaseOrderInvoiceStatus($pos_purchase_order_id);
		header('Location: '.$_POST['complete_location'] .'&message=' . $message);
	}
	else
	{
		$message = 'No results';
		header('Location: '.$_POST['cancel_location'] .'&message=' . $message);
	}
}
else
{
	$message = 'Canceled';
	header('Location: '.$_POST['cancel_location'] .'&message=' . $message);
}
function updateDiscountAmountAndQuantity($data_array)
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
	$mysql_data_array[0]['db_field'] = 'discount';
	for($i=0;$i<sizeof($data_array);$i++)
	{
		$mysql_data_array[0]['data_array'][$data_array[$i]['pos_purchase_order_content_id']] = $data_array[$i]['discount'];
	}
	$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[1]['db_field'] = 'discount_quantity';
	for($i=0;$i<sizeof($data_array);$i++)
	{
		$mysql_data_array[1]['data_array'][$data_array[$i]['pos_purchase_order_content_id']] = $data_array[$i]['discount_quantity'];
	}
	return runSQL(arrayUpdateSQLString('pos_purchase_order_contents', $mysql_data_array));
}












?>
