<?php
$binder_name = 'Products';
$access_type = 'WRITE';
require_once ('../product_functions.php');
require_once(PHP_LIBRARY);
$pos_product_id = getPostOrGetID('pos_product_id');

	//what is the size to sort??
	
	$unsorted_sizes = getProductSizes($pos_product_id);
	//echo 'unsorted options ' . var_dump($unsorted_options);
	
	$size_chart = getBrandSizeChart(getBrandIdFromProductId($pos_product_id));
	preprint($unsorted_sizes);
	preprint($size_chart['sizes']); //this will give you the two rows...
	
	//$sorted_options = sortProductOptionSizes($unsorted_options, $size_sort);
	//sortProductOptionSizes($unsorted_sizes, $size_chart)



?>
