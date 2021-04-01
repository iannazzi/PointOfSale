<?php
/* product graveyard are products with no links to invenetory, purchases, or sales
*/

$SQL = "SELECT pos_product_id FROM pos_products_sub_id
LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_product_id.pos_product_id
		WHERE all the subids have to missing from this:  AND pos_products_sub_id NOT IN (SELECT pos_products_sub_id FROM pos_purchase_order_contents) AND pos_product_sub_id NOT IN (SELECT pos_product_sub_id FROM pos_inventory_event_contents)";
		
$binder_name = 'Products';
$access_type = 'READ';
$page_title = 'Product Sub Ids';
require_once ('../product_functions.php');


?>