<?php
/*

delete a product


*/

$binder_name = 'Products';
$access_type = 'READ';
$page_title = 'Products';
require_once ('../product_functions.php');


$pos_product_id = getPostOrGetId('pos_product_id');

//if the product sub ids are linked to anything lets error out:
$sql_po = "SELECT pos_purchase_order_id, pos_purchase_order_contents.pos_product_sub_id FROM pos_purchase_order_contents
		LEFT JOIN pos_products_sub_id ON pos_purchase_order_contents.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id
		WHERE pos_products_sub_id.pos_product_id = $pos_product_id";
$purchase_order_links = getSQL($sql_po);

//here we would check the sales

//and check the inventory log
$sql_inv = "SELECT pos_inventory_event_contents.pos_product_sub_id FROM pos_inventory_event_contents
		LEFT JOIN pos_products_sub_id ON pos_inventory_event_contents.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id
		WHERE pos_products_sub_id.pos_product_id = $pos_product_id";
$inventory_links = getSQL($sql_inv);

if(sizeof($purchase_order_links) == 0 AND sizeof($purchase_order_links) == 0)
{
	//this needs to look for all references to the product and the sub_id
	//if the product is linked anywhere you can't delete it, however it can be de-activated
	
	
	$dbc = startTransaction();
	deleteProduct($dbc, $pos_product_id);

	simpleCommitTransaction($dbc);


}
else
{
	include(HEADER_FILE);
	pprint('Cant delete product because of links in the database');
	pprint('Sub-Ids Linked to POs');
	preprint($purchase_order_links);
	pprint('Sub-Ids Linked to Inventory');
	preprint($inventory_links);
	include(FOOTER_FILE);
}


?>