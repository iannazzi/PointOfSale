<?php 
/*
	*edit_product.php
	*main page used to edit existing a manufacturer to the pos system
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Create Purchase Order';
require_once('../po_functions.php');
$db_table = 'pos_purchase_orders';

	$complete_location = '../purchase_orders.php';
	$cancel_location = '../purchase_orders.php';


$table_def = createNewPOTableDef($db_table);

$big_html_table = createHTMLTableForMYSQLInsert($table_def);
//echo $big_html_table;
//$table_def_for_post = convertArrayTableDefToPostTableDef($table_def);
//preprint($table_def_for_post);	

$html =  '<script src="create_purchase_order.2014.02.11.js"></script>'.newline();
$html .= '<h2>Create A New Purchase Order</h2>';
//$html .='<input class = "button" style="width:300px" type="button" name="create_brand" value="Create A New Brand" onclick="open_win(\'../Brands/brand.php?type=add\')"/>';
$form_handler = 'create_purchase_order.form.handler.php';
$html .= createFormForMYSQLInsert($table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= createBrandCodeBrandIDLookup() .newline();
$html .= '<script>document.getElementsByName("pos_manufacturer_brand_id")[0].focus();</script>';
$html .= '<p>Note: Select the Parent Category to the products you will be ordering. The order form will display the categories below this category. For example if you are ordering only Bras, select Bras. If you are ordering Bras and Panties and Bodies, select Lingerie. If you Are ordering Bras, Panties, Swim Suits, Clothing, and Accessories select Womens. If you are ordering Mens and Womens Select Apparel.</P>';
$html .= '<p>ORDERING HINTS: Keep orders small and managable. For example, create one order per collection. Ask sales reps to combine orders where individual orders do not meet minimums. </P>';

include (HEADER_FILE);

echo $html;
include (FOOTER_FILE);
function createNewPOTableDef($db_table)
{
	/* each array could be another 'table'? 
	$table_def = 
	array(
			array(            <----- These go vertical and are unique tables
					array()     <---- Thes would go horizontal
					array()     <---- Thes would go horizontal
					array()     <---- Thes would go horizontal
				)
			array()            <----- These go vertical and are unique tables
			array()            <----- These go vertical and are unique tables
		)*/
			
$po_date_change_events = ' onchange="changeDate(\'delivery_date\', \'cancel_date\', \'30\');setPurchaseOrderNumber();needToConfirm=true;"';
	$po_creation_basics = array( 
							array( 'db_field' => 'pos_purchase_order_id',
								'type' => 'input',
								'caption' => 'System PO ID',
								'value' => 'TBD',
								'tags' => ' readonly="readonly" '
									),
							array('db_field' => 'pos_manufacturer_brand_id',
									'caption' => 'Brand Name',
									'type' => 'select',
									'html' => createManufacturerBrandSelect('pos_manufacturer_brand_id', 'false', 'off', 'onchange="setPurchaseOrderNumber();needToConfirm=true;"'),
									'validate' => array('select_value' => 'false')),
							array( 'db_field' => 'po_title',
									'type' => 'input',
									'caption' => 'Purchase Order Title',
									'validate' => array('min_length' => 1)
									),
							
							/*array('db_field' => 'purchase_order_status',
									'type' => 'select',
									'html' => createPOStatusSelect('purchase_order_status', 'INIT', 'off', 'onchange="needToConfirm=true" '),
									'value' => 'INIT'
									),*/
							/*array('db_field' => 'ordered_status',
									'type' => 'select',
									'html' => createPOOrderedStatusSelect('ordered_status', 'NOT SUBMITTED', 'off', 'onchange="needToConfirm=true" '),
									'value' => 'NOT SUBMITTED',
									'tags' => 'readonly="readonly"'),*/
							array( 'db_field' => 'pos_store_id',
									'type' => 'select',
									'caption' => 'Ship To',
									'html' => createShipToStoreSelect('pos_store_id', 'false'),
									'value' => $_SESSION['store_id'],
									'validate' => array('select_value' => 'false')),		
							array( 'db_field' => 'pos_category_id',
									'type' => 'select',
									'caption' => 'Primary Category',
									'html' => createCategoryTreeSelectChildlessness('pos_category_id', 'false'),
									'validate' => array('select_value' => 'false')),
							array('db_field' =>  'delivery_date',
									'type' => 'date',
									'tags' => $po_date_change_events,
									'value' => '',
									'validate' => 'date'),
							array('db_field' =>  'cancel_date',
									'type' => 'date',
									'tags' => 'onchange="needToConfirm=true;"',
									'value' => '',
									'validate' => 'date'),
							array('db_field' => 'purchase_order_number',
									'caption' => 'Custom PO Number',
									'type' => 'input',
									'validate' => 'none'),
							array('db_field' => 'comments',
									'caption' => 'Comments',
									'type' => 'input',
									'validate' => 'none')
							);
	
	$po_receiving = array(array('db_field' =>  'received_date',
									'type' => 'date',
									'tags' => 'onchange="needToConfirm=true;" ',
									'value' => '',
									'validate' => 'date')
									);	
	$po_pricing = array( 							
							array(
									'caption' => 'Calculated Order Amount',
									'type' => 'none',
									'html' => '',
									'validate' => 'none'));


									
	$table_def = $po_creation_basics;
	return $table_def;
								
}	
?>