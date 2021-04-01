<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/

$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once ('../inventory_functions.php');

	$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');

$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;

$cancel_location = addGetToURL($complete_location, 'message=Canceled');
$page_title = 'Receive PO#' .$pos_purchase_order_id;

$type = getPostOrGetValue('type');
if (strtoupper($type) =='EDIT')
{
	$table_type = 'Edit';
	$data_table_def_no_data = createReceivePurchaseOrderTableDef($table_type, $pos_purchase_order_id);	
	$db_table = 'pos_purchase_order_receive_event';
	$key_val_id['pos_purchase_order_id'] = $pos_purchase_order_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else
{
	$table_type = 'New';
	$pos_purchase_order_id = 'TBD';
	$data_table_def = createLocationGroupTableDef($table_type, $pos_purchase_order_id);
}

$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
$big_html_table .= createHiddenInput('type', $type);
$big_html_table .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
$form_handler = 'add_edit_receive_event.form.handler.php';
$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("pick_ticket_number")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createReceivePurchaseOrderTableDef()
{
	$table_def = array( 
							
							array( 'db_field' => 'pick_ticket',
									'type' => 'input',
									'caption' => 'Pick Ticket Number',
									'validate' => 'none'
									),
							array('db_field' =>  'receive_date',
									'type' => 'date',
									'tags' => '',
									'value' => '',
									'validate' => 'date'),
							array('db_field' => 'comments',
									'caption' => 'Comments',
									'type' => 'input',
									'validate' => 'none')

							);
	return $table_def;
}

?>	