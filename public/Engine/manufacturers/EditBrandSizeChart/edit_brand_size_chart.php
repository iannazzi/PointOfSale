<?PHP
/*
	*edit_brand_sizes.php
	*Craig Iannazzi 1-28-2012 t minus 10 months till aliens
	*The edit brand is called from the brand form in edit or add manufacturerer
	
	*This file is needed to edit attributes per brand - most specifically the sizing chart
	* almost all brands have a sizing chart. Most often the sizes are pre-determinied
	*For example 2,4,6,8,10,12 or 32A 32B 32C etc..
	*If the brand sells bras we will need a cup size.
	*If the brand sells PANTS we might NEED and inseam
	
*/
$binder_name = 'Manufacturers';
$access_type = 'WRITE';
require_once('../manufacturer_functions.php');

$db_table = 'pos_manufacturer_brand_sizes';
$id['pos_manufacturer_brand_id'] = getPostOrGetID('pos_manufacturer_brand_id');
$pos_manufacturer_id = getManufacturerIDFromBrandID($id['pos_manufacturer_brand_id']);
$complete_location = getCompleteLocation('../ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$pos_manufacturer_id);
$cancel_location = '../ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$pos_manufacturer_id;


$existing_sizes = getBrandSizeChartArray($id['pos_manufacturer_brand_id']);
$num_sizes = (isset($existing_sizes[0]['sizes'])) ? sizeof($existing_sizes[0]['sizes']): 1;
$row = 0;
$dynamic_table_col_def[$row] = array( 
						array( 'type' => $type = 'checkbox',
								'html' => createFormInput(array('type' => $type))),
						array( 'db_field' => $db_field = 'pos_manufacturer_brand_size_id',
								'caption' => 'Brand System Id <br> (automatically generated)',
								'type' => $type = 'text',
								'tags' => ' readonly = "readonly" ',
								'html' => createFormInput(array('type'=> $type, 'name' => $db_field.'_r'.$row, 'readonly' => 'readonly'))),
						array('db_field' =>  $db_field = 'active',
								'type' => $type = 'checkbox',
								'default' => 1,
								'html' => createFormInput(array('type'=> $type, 'name' => $db_field.'_r'.$row, 'checked' => 'checked')),
								'validate' => 'none'),
						array( 'db_field' => 'pos_category_id',
								'type' => 'select',
								'caption' => 'Category',
								'html' => createCategorySelect('pos_category_id', 'false'),
								'validate' => array('select_value' => 'false')),
						array( 'db_field' => $db_field = 'case_qty',
								'type' => $type = 'checkbox',
								'html' => createFormInput(array('type'=> $type, 'name' => $db_field.'_r'.$row)),
								'validate' => 'none'),
						array( 'db_field' => $db_field = 'cup',
								'type' => $type = 'checkbox',
								'html' => createFormInput(array('type'=> $type, 'name' => $db_field.'_r'.$row)),
								'validate' => 'none'),
						array( 'db_field' => $db_field = 'cup_required',
								'type' => $type = 'checkbox',
								'html' => createFormInput(array('type'=> $type, 'name' => $db_field.'_r'.$row)),
								'validate' => 'none'),
						array( 'db_field' => $db_field = 'inseam',
								'type' => $type = 'checkbox',
								'html' => createFormInput(array('type'=> $type, 'name' => $db_field.'_r'.$row)),
								'validate' => 'none'),
						array( 'db_field' => $db_field = 'width',
								'type' => $type = 'checkbox',
								'html' => createFormInput(array('type'=> $type, 'name' => $db_field.'_r'.$row)),
								'validate' => 'none'),
						array( 'db_field' => 'pos_product_attribute_id',
								'type' => 'select',
								'caption' => 'Attribute',
								'html' => createProductAttributeSelect('pos_product_attribute_id', 'false'),
								),
						array( 'db_field' => $db_field = 'sizes',
								'th' => '<th id = "size_header" colspan ="' . $num_sizes .'">Sizes</th>',
								'type' => $type = 'text',
								'html' => createFormInput(array('type'=> $type, 'name' => $db_field.'_r'.$row, 'size'=>'3', 'onkeyup'=>'this.value=this.value.toUpperCase()')),
								'validate' => array('min_length' => 1),
								'value' => array()),
						array('db_field' => $db_field = 'comments',
								'type' => 'textarea',
								'html' => createFormTextArea(array('name' => $db_field.'_r'.$row)),
								'validate' => 'none')
						);	
$data_table_def_with_data = loadBrandSizeDataIntoTableDef($existing_sizes, $dynamic_table_col_def);
include (HEADER_FILE);
//set up form
//Manufacturer Table
$html = '<h2>Edit Size Chart For ' . getBrandName($id['pos_manufacturer_brand_id']) . '</h2>';
$html .= javascript('edit_brand_size_chart.js');
$form_handler = 'edit_brand_size_chart.form.handler.php';
$html .= createBrandSizeChartForm($id['pos_manufacturer_brand_id'],$data_table_def_with_data, $form_handler, $complete_location, $cancel_location);
$html .= '<p><span style="font-size:0.75em;width=90%">Set this table up so that it matches the brand\'s purchase order form.Selecting the right size is needed to match to the manufacturer\'s UPC and therefore utilize their barcodes. This chart will be seen on the top of the purchase order. 	Sizes that have a category specified will default to those sizes when ordering (category bra will use bra sizing while category pants will use pant sizing)</span></p><p></p>	';
echo $html;
include (FOOTER_FILE);



?>