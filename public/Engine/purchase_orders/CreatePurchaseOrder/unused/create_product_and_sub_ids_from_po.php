<?php
/* create_product_and_sub_ids_from_po 
	craig iannazzi 4-4-12
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Create Product Ids';
require_once ('../po_functions.php');

$pos_purchase_order_id = getPostorGetID('pos_purchase_order_id');
$pos_manufacturer_brand_id = getBrandIdFromPOId($pos_purchase_order_id);
$stored_size_chart = loadStoredSizeChart($pos_purchase_order_id);
$tbody_data = loadPurchaseOrderContents($pos_purchase_order_id, $stored_size_data);
$pos_products = createProductsFromPOC($pos_manufacturer_brand_id,$tbody_data, $stored_size_data);
$pos_product_sub_ids = createSubIDs($pos_manufacturer_brand_id,$tbody_data, $stored_size_data);

?>

