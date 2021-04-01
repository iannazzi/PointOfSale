<?
/*  this will push an image and the products to the web... creating web products if needed.....
	of course this is specific to pinnacle cart, however it should be modifiable....
*/
$binder_name = 'Images';
$access_type = 'Write';
$page_title = 'Product Images';
require_once ('../product_functions.php');	

$pos_product_image_id = getPostOrGetID('pos_product_image_id');

$html = '';
//show the image

$html .= product_image_html($pos_product_image_id);



$type = getFormType();

$product_table_name = 'product_table';
if($type == '')
{	
	//create a drop down to select which image then re-submit the form....
}
else if(strtoupper($type) == 'PRIMARY')
{


	$html .= '<h3>If you want to link products in this images to OLDER web store products you will want to update the older webstore product to the NEWER product ID. For Example, The Pour La Victoire Jaclyn Nude web product ID is (was) plvjaclynnude and the POS id is plv-jaclyn-nude. Update the web product to plv-jaclyn-nude and then the products can be linked on the web. When changing this ID on the web the cart will autmatically fix the image names.';

	$products_table_def = createPinnacleCartCSVProductTableDef($pos_product_image_id, $product_table_name);
	
	
	
	

	
	
}
else if(strtoupper($type) == 'SECONDARY')
{
	
		$html .= '<h3>Should be good to go here....as long as that product id is on the web </h3>';
	$products_table_def = createPinnacleCartSecondaryImageTableDef($pos_product_image_id, $product_table_name);
	
	
}
else if(strtoupper($type) == 'REPLACE')
{
	$html .= '<h3>Should be good to go here....as long as that product id is on the web </h3>';
	$products_table_def = createPinnacleCartSecondaryImageTableDef($pos_product_image_id, $product_table_name);
}
else
{
}


	//create the form
	$form_id = "load_web_image";
	$form_action = 'load_web_image.fh.php';
	$html .=  '<form id = "' . $form_id . '" action="'.$form_action.'" method="post" onsubmit="return alert(\'validate me \')">';
	
	//ok we need to create the web product from here....
	//the image is tagged to many products.
	//create a dynamic table of all these products
	$products = getImageProducts($pos_product_image_id);
	//preprint($products);
	
	$html .= createDynamicTableReuse($product_table_name, $products_table_def, $products, $form_id, ' class="dynamic_contents_table" style="width:100%" ');
	
	
	$html .= '<p><input class ="button" type="submit"  id = "submit" name="submit" value="Push To Web" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="submit" name="cancel" value="Cancel"/>';
	$html .= createHiddenInput('type', $type);
	$html .= createHiddenInput('pos_product_image_id', $pos_product_image_id);
	$html .= '</form>';


	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);



//if the image is a primary image we need to create the product

//if it is a secondary image we just need to .... select which product?




?>