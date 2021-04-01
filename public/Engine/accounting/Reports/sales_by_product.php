<?php

//sales reoprt by product.....

$sql = "SELECT pos_products.style_number, sum( pos_sales_invoice_contents.retail_price ) , sum( pos_sales_invoice_contents.sale_price ) , sum( applied_instore_discount )
FROM pos_sales_invoice_contents
LEFT JOIN pos_products_sub_id ON pos_products_sub_id.pos_product_sub_id = pos_sales_invoice_contents.pos_product_sub_id
LEFT JOIN pos_products ON pos_products.pos_product_id = pos_products_sub_id.pos_product_id
LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
WHERE pos_manufacturer_brands.brand_name = "WOLFORD"
GROUP BY pos_products.style_number
ORDER BY sum( pos_sales_invoice_contents.sale_price ) DESC 




SELECT pos_products.style_number, sum( pos_sales_invoice_contents.retail_price ) , sum( pos_sales_invoice_contents.sale_price ) , sum( applied_instore_discount )
FROM pos_products
LEFT JOIN pos_products_sub_id ON pos_products_sub_id.pos_product_sub_id = pos_sales_invoice_contents.pos_product_sub_id
LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
LEFT JOIN pos_sales_invoice_contents ON pos_products_sub_id.pos_product_sub_id = pos_sales_invoice_contents.pos_product_sub_id
WHERE pos_manufacturer_brands.brand_name = "WOLFORD"
GROUP BY pos_products.style_number
ORDER BY sum( pos_sales_invoice_contents.sale_price ) DESC 



?>