<?php

$page_title = 'Export';
require_once ('../po_functions.php');
$full_product_export_sql = "

SELECT pos_products.pos_product_id, pos_products.pos_manufacturer_brand_id, pos_products.priority, pos_products.added, pos_products.is_taxable, pos_products.tax_class_id, pos_products.tax_rate, pos_products.weight, pos_products.overview, pos_products.description, pos_products.style_number, pos_products.title, ROUND(pos_products.cost,2) as cost, ROUND(pos_products.retail_price,2) AS retail_price, ROUND(pos_products.sale_price,2) AS sale_price, pos_manufacturer_brands.brand_name, pos_categories.name, 
(SELECT GROUP_CONCAT(pos_categories.name) 
		FROM pos_product_secondary_categories
		LEFT JOIN pos_categories
		ON pos_product_secondary_categories.pos_category_id = pos_categories.pos_category_id
		WHERE pos_products.pos_product_id = pos_product_secondary_categories.pos_product_id) AS secondary_categories,
pos_products.active
FROM pos_products
LEFT JOIN pos_manufacturer_brands
ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
LEFT JOIN pos_categories
ON pos_products.pos_category_id = pos_categories.pos_category_id
WHERE 1

";

if (isset($_POST['pos_export']))
{
	$data = getSQL($full_product_export_sql.urldecode($_POST['search_sql']).urldecode($_POST['order_sql']));
	$csv_ready_array = MYSQLArrayToCSVReadyArray($data);
	$delimiter =',';
	$filename = 'pos_product_export.csv';
	arrayToCsv($filename, $csv_ready_array, $delimiter);
}

elseif (isset($_POST['webstore_export']))
{
	$data = getSQL($full_product_export_sql.urldecode($_POST['search_sql']).urldecode($_POST['order_sql']));
	$new_products = exportProductsForWebStore($data);
	$csv_ready_array = MYSQLArrayToCSVReadyArray($new_products);
	$delimiter =',';
	$filename = 'pos_product_export.csv';
	arrayToCsv($filename, $csv_ready_array, $delimiter);
	
}
else
{
	echo 'error';
}
?>