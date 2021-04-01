<?
/*
	this will create both products and inventory quantities
	
	Select the brand, enter the style number, color code, color description, title, size, cost, retail, sale price, and quantity
	
	if the product already exists we will update the inventory
	
	
*/
		
$binder_name = 'Products';
$access_type = 'Write';
$page_title = 'Bulk Product Creation';
require_once ('../product_functions.php');		
$complete_location = 'list_products.php';
$cancel_location = $complete_location;		
$form_handler = 'bulk_product_generation.form.handler.php';


	$data = array();
	$table_def = createBulkProductLoaderTableDef();
	$html_table = createDynamicTable($table_def, $data);
	$html = '<h3>Bulk Product Creator - UNVALIDATED FORM so Currently SUPER DANGEROUS TO USE</h3>';
	$html .= createFormForDynamicTableMYSQLInsert($table_def, $html_table, $form_handler, $complete_location, $cancel_location);
	$html .=  '<script src="inventory_by_location.js"></script>'.newline();
	$html .=  '<script src="lookup_product_sub_id.js"></script>'.newline();
	$html .= '<script>document.getElementsByName("barcode")[0].focus();</script>';


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);



function createBulkProductLoaderTableDef()
{

	$select_ids = array();
	$select_names = array();		
	//this is the select values
	$selectable_brands = getBrands();
	for($i=0;$i<sizeof($selectable_brands);$i++)
	{
		$select_ids[$i]= $selectable_brands[$i]['pos_manufacturer_brand_id'];
		$select_names[$i] = $selectable_brands[$i]['brand_name'];	
	}
	
	
	//categories
	$categories = getNoParentCategoryArray();
	$category_names = $categories['name'];
	$category_ids = $categories['pos_category_id'];
	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),


					array('db_field' => 'pos_manufacturer_brand_id',
						'caption' => 'Brand',
						'type' => 'select',
						'select_names' => $select_names,
						'select_values' => $select_ids,
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){}',
												/*'onblur' => 'function(){updateSelectOptions();}'*/)
						),
					array('caption' => 'Style Number',
						'db_field' => 'style_number',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												)),
					array('caption' => 'Color Code',
						'db_field' => 'color_code',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												)),
					array('caption' => 'Color Description',
						'db_field' => 'color_description',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												)),
					array('caption' => 'Title',
						'db_field' => 'title',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"30"',
												)),
					array('caption' => 'Size',
						'db_field' => 'size',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												)),
					array('db_field' => 'pos_category_id',
						'caption' => 'Category',
						'type' => 'select',
						'select_names' => $category_names,
						'select_values' => $category_ids,
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){}',
												/*'onblur' => 'function(){updateSelectOptions();}'*/)),
					array('caption' => 'Cost',
						'db_field' => 'cost',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												)),
					array('caption' => 'Retail Price',
						'db_field' => 'retail_price',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'round' => 2,
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												)),						
					
					array('caption' => 'Comments',
					'db_field' => 'comments',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}'))
					
				);			
						
		
		return $columns;
	
	
	
}
?>