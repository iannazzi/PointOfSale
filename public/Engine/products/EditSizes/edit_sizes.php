<?php 

/*

	Craig Iannazzi 1-11-13
	
	well now.... why am i creating all these 'sizes' for each product
	should there just be a set of product_options based on mfg brand? 
	sounds a bit complicated...
	
*/
$binder_name = 'Products';
$access_type = 'WRITE';
$page_title = 'Sizes';
require_once ('../product_functions.php');
$pos_product_id =  getPostOrGetID('pos_product_id');

$complete_location = '../ViewProduct/view_product.php?pos_product_id='.$pos_product_id;
$cancel_location = $complete_location;
$form_handler = 'edit_sizes.form.handler.php';

//this is a dynamic table .... however we cannot delete row?

//sure we can, we just won't delete out of the system?



	$data = getProductSizes($pos_product_id);
	$table_def = createSizeTable();
	$html_table = createHiddenInput('pos_product_id', $pos_product_id);
	$html_table .= createDynamicTable($table_def, $data);
	$html = '<h3>Create And Sort Attributes</h3>';
	$html .= createFormForDynamicTableMYSQLInsert($table_def, $html_table, $form_handler, $complete_location, $cancel_location);
	
	
	//$html .=  '<script src="adjust_items.js"></script>'.newline();
	//$html .=  '<script src="lookup_product_sub_id.js"></script>'.newline();
	//$html .= '<script>document.getElementsByName("barcode")[0].focus();</script>';
	
	
	
	
	





	


	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);





?>

