<?php
$binder_name = 'Products';
$access_type = 'READ';
$page_title = 'Product labels';
require_once('../product_functions.php');

$pos_product_id = getPostOrGetID('pos_product_id');
$sql = " SELECT pos_product_sub_id, product_subid_name FROM pos_products_sub_id WHERE pos_product_id = $pos_product_id";
$data = getSQL($sql);
for($i=0;$i<sizeof($data);$i++)
{
	$data[$i]['quantity'] = 0;
	//$data[$i]['row_number'] = $i+1;
}


$file_name = $pos_product_id .'_labels.pdf';
$html = printProductLabelsForm($data, $file_name);
$html .= createOpenWinButton('Return' , POS_ENGINE_URL .'/products/ViewProduct/view_product.php?pos_product_id='.$pos_product_id, $width = '200') .'</p>';
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
		
		
?>