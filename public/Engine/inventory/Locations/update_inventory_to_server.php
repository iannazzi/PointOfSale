<?php
$binder_name = 'Locations';
$access_type = 'WRITE';
$page_title = 'update location inventory';
require_once('../inventory_functions.php');
$pos_inventory_event_id = getPostOrGetID('pos_inventory_event_id');
$invoice_tbody_def = $_POST['inventory_tbody_def'];
$invoice_table_data_object = (isset($_POST['inventory_table_data_object'])) ? $_POST['inventory_table_data_object'] : array();

$dbc = startTransaction();

//the inventory_event_id must be passed in...


$delete_sql = "DELETE FROM pos_inventory_event_contents WHERE pos_inventory_event_id = $pos_inventory_event_id";
runTransactionSQL($dbc, $delete_sql);


if(sizeof($invoice_table_data_object)>0)
{
	for($row=0;$row<sizeof($invoice_table_data_object['row_number']);$row++) 
	{	
	
		$insert_array = 
			array( 	
		'pos_inventory_event_id' => $pos_inventory_event_id,
		'pos_product_sub_id' => $invoice_table_data_object['pos_product_sub_id'][$row],
		'price_level' => $invoice_table_data_object['price_level'][$row],
				'quantity' =>$invoice_table_data_object['quantity'][$row],
				//'inventory_type' => $invoice_table_data_object['inventory_type'][$row],
				'barcode' => strtoupper($invoice_table_data_object['barcode'][$row]),
				'action' => 'PHYSICAL_COUNT',
				'comments' => $invoice_table_data_object['comments'][$row],
				//'value' => $invoice_table_data_object['value'][$row]
										);
		$id  = simpleTransactionInsertSQLReturnID($dbc,'pos_inventory_event_contents', $insert_array);
	}
}
simpleCommitTransaction($dbc);
echo 'STORED' .newline();

?>