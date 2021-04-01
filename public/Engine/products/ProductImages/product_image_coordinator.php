<?
/*
	need two tables: one for the products, and one for the images....
	
	
*/
		
$binder_name = 'Images';
$access_type = 'Write';
$page_title = 'Product Images';
require_once ('../product_functions.php');		
		
$product_lookup_javascript = 'ajax_product.2013.06.26.js';



$form_handler = 'product_image_coordinator.form.handler.php';
//$complete_location = '../ListProducts/list_products.php';
//$cancel_location = $complete_location . '&message=Canceled';



	$html =  '<script src="product_image_coordinator.js"></script>'.newline();
	//$html .=  '<script src="'.AJAX_PRODUCT_SUB_ID.'"></script>'.newline();
		$html .=  '<script src="'.$product_lookup_javascript.'"></script>'.newline();

	$html .= '<h3>Record the Products and Each Image Name</h3>';
	
	//************************** PRODUCT LOOKUP TABLE  ***********************************************
	$html .= ' <div class = "product_lookup_div">';
	$html .= '<table class = "product_lookup_outline" style="width:100%;">';
	$html .= '<TR><td>';
	$html .= POSproductLookUpTable();
	$html .= '</td>';
	$html.='</TR>';
	$html .= ' </table>';
	$html .= '</div>';

	//$html .= productLookUpTable();
	
	//the form
	
	$form_id = 'image_coordinator';
	$html .=  '<form id = "' . $form_id . '" action="'.$form_handler.'" method="post" onsubmit="return validatePhotoForm()">';
	
	// first the products
	$html .= '<h3>Zap The Products on the Model</h3>';
	$product_contents = array();//getInvoiceContents($pos_sales_invoice_id);
	$product_table_name = 'product_table';
	$product_contents_table_def = createImageCoordinatorProductTableDef($product_table_name);
	$html .= createDynamicTableReuse($product_table_name, $product_contents_table_def, $product_contents, $form_id, ' class="dynamic_contents_table"  ');
	
	//next the images
	$html .= '<h3>Grab the Image Name/Number From the Camera - DO NOT INCLUDE THE .jpg</h3>';
	$image_contents = array();//getInvoiceContents($pos_sales_invoice_id);
	$image_table_name = 'image_table';
	$image_contents_table_def = createImageCoordinatorImageTableDef($image_table_name);
	$html .= createDynamicTableReuse($image_table_name, $image_contents_table_def, $image_contents, $form_id, ' class="dynamic_contents_table"  ');
	
	$html .= '<p><input class ="button" type="submit"  id = "submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="submit" name="cancel" value="Cancel"/>';
	
	$html .= '</form>';
	
	;

	
	//$html .=  '<script src="lookup_product_sub_id.js"></script>'.newline();
	$html .= '<script>document.getElementsByName("barcode")[0].focus();</script>';


function POSproductLookUpTable()
{
	$html =  '<TABLE style="width:100%;">';
	$html .= '<tr>'.newline();
	$html .= ' <TD style="vertical-align:bottom;width:10%;text-align:center;"><INPUT TYPE="TEXT" class="lined_input"  id="barcode" style = "background-color:yellow;width:100%;" NAME="barcode" onclick="this.select()" onKeyPress="return disableEnterKey(event)" onKeyDown="lookUpBarcodeID(this, event)"	/></td>';
	$html .= '<td style="vertical-align:bottom;width:10%;text-align:center;"><input class = "button2" type="button"  name="add_barcode" value="Add" onclick="addBarcodeButton()"/></td>';
	$html .= '<td style="vertical-align:bottom;text-align:center;width:5%;">'.newline();
	$html .= 'OR';
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;width=65%;text-align:center;">'.newline();
	$html .= ' <style>
.ui-autocomplete-loading {
background: white url("'.POS_ENGINE_URL . '/includes/images/ui-anim_basic_16x16.gif") right center no-repeat;
}
</style>';
	$html.= '<div class="ui-widget" >
<input id="product_search"  value="Type to search, leave spaces between search terms..." style = "border: 1px solid black;width:100%;" onclick="productSearchFocus()"/>
</div>';
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;width:10%;">'.newline();
	$html .= '<input class = "button2" type="button"  name="add_prodcut_subid" value="Add" onclick="addSubidFromSearch()"/>';
	$html .= '</td>'.newLine();
	$html .= '</tr>'.newline();
	$html .=  '</table>';
	$html .= addBeepV3().newline();
	return $html;


}



include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);



?>