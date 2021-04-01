<?php
/*
	Craig Iannazzi 2-8-2013 on a snowy day at midtown
	
	//eventually I would like to link the promotion to products, or categories, or manufacturers
	// so there would be a promotion_id to category, manufacturer, product id lookup table... a dynamic table
*/

$page_title = 'promotions';
$binder_name = 'promotions';
	
	
	
	
//$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../sales_functions.php');


if(isset($_GET['type']))
{
	$type = $_GET['type'];
}
elseif(isset($_POST['type']))
{
	$type = $_POST['type'];
}
else
{
	trigger_error('missing type');
}

$complete_location = 'list_promotions.php';
$cancel_location = 'list_promotions.php?message=Canceled';

if (isset($_POST['submit'])) 
{

	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	
	//some error checking....
	$pos_promotion_id = $_POST['pos_promotion_id'];
	$promotion_code = scrubInput($_POST['promotion_code']);
	if(getSingleValueSQL("SELECT pos_promotion_id FROM pos_promotions WHERE promotion_code = '$promotion_code' AND pos_promotion_id != '$pos_promotion_id'"))
		{
			include(HEADER_FILE);
			echo 'Problem - Promotion Code Already Exists - go back and try again';
			include(FOOTER_FILE);
			exit();
		}
	
	unset($insert['pos_promotion_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_promotion_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_promotion_id = simpleTransactionInsertSQLReturnID($dbc,'pos_promotions', $insert);
		$message = urlencode('promotion Id ' . $pos_promotion_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_promotion_id = getPostOrGetID('pos_promotion_id');
		$key_val_id['pos_promotion_id'] = $pos_promotion_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_promotions', $key_val_id, $insert);
		$message = urlencode('promotion ID ' . $pos_promotion_id . " has been updated");
	}
	
	//either case update the lookup table\
	//first delete everything then add new
	$delete_sql = runTransactionSQL($dbc,"DELETE FROM pos_promotion_lookup WHERE pos_promotion_id = $pos_promotion_id");
	$delete_sql = runTransactionSQL($dbc,"DELETE FROM pos_promotion_buy WHERE pos_promotion_id = $pos_promotion_id");
	if (isset($_POST['buyXgetY_tdo'])) 
	{
		$buyXgetY_tdo = json_decode(stripslashes($_POST['buyXgetY_tdo']) , true);
		for($i = 0; $i<sizeof($buyXgetY_tdo);$i++)
		{
			
			//error check here...
			if (!is_int($buyXgetY_tdo[$i]['get']['data'])){
			
			if(!ctype_digit($buyXgetY_tdo[$i]['get']['data']))
			{
				INCLUDE(HEADER_FILE);
				echo 'Error - Get value is not an integer. Go back and try again.';
				preprint($_POST);
				INCLUDE(FOOTER_FILE);
				exit();
			}
			}
			
			if ($buyXgetY_tdo[$i]['get']['data']> $buyXgetY_tdo[$i]['buy']['data'])
			{
				INCLUDE(HEADER_FILE);
				echo 'Error - Get value is larger than buy. Go back and try again.';
				preprint($_POST);
				INCLUDE(FOOTER_FILE);
				exit();

			}
			
			$SQL = runTransactionSQL($dbc,"INSERT INTO pos_promotion_buy (pos_promotion_id, buy,get,discount, d_or_p) VALUES ($pos_promotion_id, '".$buyXgetY_tdo[$i]['buy']['data'] . "', '".$buyXgetY_tdo[$i]['get']['data'] . "', '".$buyXgetY_tdo[$i]['discount']['data'] . "', '".$buyXgetY_tdo[$i]['d_or_p']['data'] . "')");
		}

	}
	if (isset($_POST['product_tdo'])) 
	{
		$product_table = json_decode(stripslashes($_POST['product_tdo']) , true);
		for($i = 0; $i<sizeof($product_table);$i++)
		{
			$SQL = runTransactionSQL($dbc,"INSERT INTO pos_promotion_lookup (pos_promotion_id, pos_product_id, include_product) VALUES (
			$pos_promotion_id, 
			'".$product_table[$i]['pos_product_id']['data'] . "',
			'".$product_table[$i]['include_product']['data'] . "'
			)");
		}

	}
	if (isset($_POST['brand_tdo'])) 
	{
		$brand_table = json_decode(stripslashes($_POST['brand_tdo']) , true);
		for($i = 0; $i<sizeof($brand_table);$i++)
		{
			$SQL = runTransactionSQL($dbc,"INSERT INTO pos_promotion_lookup (pos_promotion_id, pos_manufacturer_brand_id, include_brand) VALUES (
			$pos_promotion_id, 
			'".$brand_table[$i]['pos_manufacturer_brand_id']['data'] . "',
			'".$brand_table[$i]['include_brand']['data'] . "'
			
			)");
		}

	}
	if (isset($_POST['category_tdo'])) 
	{
		$category_table = json_decode(stripslashes($_POST['category_tdo']) , true);
		for($i = 0; $i<sizeof($category_table);$i++)
		{
			$SQL = runTransactionSQL($dbc,"INSERT INTO pos_promotion_lookup (pos_promotion_id, pos_category_id, include_category,include_subcategories) VALUES (
			$pos_promotion_id, 
			'".$category_table[$i]['pos_category_id']['data'] . "',
			'".$category_table[$i]['include_category']['data'] . "',
			'".$category_table[$i]['include_subcategories']['data'] . "'

			)");
		}

	}
	simpleCommitTransaction($dbc);
	header('Location: promotion.php?type=VIEW&pos_promotion_id='.$pos_promotion_id);
}
else if (isset($_POST['cancel']))
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}
else if ($type == 'ajax')
{
	$ajax_request = $_POST['ajax_request'];
	
	if($ajax_request  == 'promotion_code')
	{
		$pos_promotion_id = $_POST['pos_promotion_id'];
		$promotion_code = scrubInput($_POST['promotion_code']);
		if(getSingleValueSQL("SELECT pos_promotion_id FROM pos_promotions WHERE promotion_code = '$promotion_code' AND pos_promotion_id != '$pos_promotion_id'"))
		{
			echo 'Problem - Promotion Code Already Exists';
			exit();
		}
		else
		{
			echo 'OK';
			exit();
		}
		
	}
}
else
{

	if(strtoupper($type) == 'ADD')
	{
		$pos_promotion_id = 'TBD';
		$table_type = 'New';
		$pos_location_group_id = 'TBD';
		$header = '<p>Add promotion</p>';
		$page_title = 'Add promotion';
		$data_table_def = createpromotionTableDef($type, $pos_promotion_id);
	}
	elseif (strtoupper($type) == 'EDIT')
	{
		$pos_promotion_id = getPostOrGetID('pos_promotion_id');
		$header = '<p>EDIT Location Group</p>';
		$page_title = 'Edit promotion';
		$data_table_def_no_data = createpromotionTableDef($type, $pos_promotion_id);	
		$db_table = 'pos_promotions';
		$key_val_id['pos_promotion_id'] = $pos_promotion_id;
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
	}
	elseif (strtoupper($type) == 'VIEW')
	{
		$pos_promotion_id = getPostOrGetID('pos_promotion_id');
		$edit_location = 'promotion.php?pos_promotion_id='.$pos_promotion_id.'&type=edit';
		//$delete_location = 'delete_promotion.for.php?pos_promotion_id='.$pos_promotion_id;
		$db_table = 'pos_promotions';
		$key_val_id['pos_promotion_id']  = $pos_promotion_id;
		$data_table_def = createpromotionTableDef($type, $pos_promotion_id);
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);

	}
	else
	{
	}
	$buyXGetY_table_name = 'buyXgetY';
	$brand_table_name = 'brand';
	$category_table_name = 'category';
	$product_table_name = 'product';
	$buyXGetY_contents = getSQL("SELECT * FROM pos_promotion_buy WHERE pos_promotion_id = '$pos_promotion_id'"); 
$product_contents = getSQL("SELECT pos_product_id, include_product, concat_WS( '-', brand_name,style_number,title ) as item FROM pos_promotion_lookup 
	LEFT JOIN pos_products USING (pos_product_id)
	LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
	WHERE pos_promotion_id = '$pos_promotion_id' AND pos_product_id !=0");
	$brand_contents = getSQL("SELECT  pos_manufacturer_brand_id, include_brand FROM pos_promotion_lookup WHERE pos_promotion_id = '$pos_promotion_id' AND pos_manufacturer_brand_id !=0");
	$category_contents = getSQL("SELECT pos_category_id, include_category, include_subcategories FROM pos_promotion_lookup WHERE pos_promotion_id = '$pos_promotion_id' AND pos_category_id !=0");
	//build the html page
	if (strtoupper($type) == 'VIEW')
	{
		$html = printGetMessage('message');
		$html .= '<p>View promotion</p>';
		//$html .= confirmDelete($delete_location);

		$html .= createHTMLTableForMYSQLData($data_table_def);
		$html .= '<P> Promotion Values (Buy X Get Y at Z ($ or %) Discount</p>';
		$html .= createStaticViewDynamicTableV2( $buyXGetY_table_name, createBUYxGETyTableDef($buyXGetY_table_name), $buyXGetY_contents, ' class="static_contents_table" ');
		$html .= '<P> AND Limit the promotion to products within the following categories...</p>';
		$html .= createStaticViewDynamicTableV2( $category_table_name, createCategoryToPromotionTableDef($category_table_name), $category_contents, ' class="static_contents_table" ');
			$html .= '<P> AND Limit the promotion to products within the following brands...</p>';

		$html .= createStaticViewDynamicTableV2($brand_table_name,createBrandToPromotionTableDef($brand_table_name), $brand_contents, ' class="static_contents_table" ');
			$html .= '<P> AND Limit the promotion to products...</p>';

		$html .= createStaticViewDynamicTableV2($product_table_name, createProductToPromotionTableDef($product_table_name), $product_contents, ' class="static_contents_table" ');
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete promotion" onclick="confirmDelete();"/>';
	
		$html .= '<p>';
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to promotions" onclick="window.location = \''.$complete_location.'\'" />';
		$html .= '</p>';
		$html .= promotion_instructions();
	}
	else
	{
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
	
		$html = $header;
	
		$javascript = 'promotions.2014.08.06.js';
		$form_handler = 'promotion.php';
		$form_id = 'promotion';
		$html .=  '<script src="'.$javascript.'"></script>'.newline();
		$html .=  '<script> var form_id="'.$form_id.'"</script>'.newline();

		$html .=  '<form id = "' . $form_id . '" action="'.$form_handler.'" method="post" onsubmit="return validatePromotionForm()">';
		$html .= createHiddenInput('complete_location', $complete_location);
		$html .= createHiddenInput('type', $type);
		$html .= createHiddenSerializedInput('table_def', prepareTableDefForPost($data_table_def)).newline();	


		$html .= '
		<h2>To Create Promotion First create the promotion type THEN create the promotion value using the Buy x Get Y table. There are many steps, even for a seemingly simple promotion. Refer to the examples below.</h2>';

		$html .= createHTMLTableForMYSQLInsert($data_table_def);
	
	

	
	
		//add the dynamic tables for products and categories and brands to link....
		$buttonsbuyxgety = array(
							
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:60px;",
									'value'=>"Add Row", 
									'onclick' => 'buyxADdRow()'
									),
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:60px;",
									'value'=>"Delete Row", 
									'onclick' => $buyXGetY_table_name.'.deleteRow();'
									),
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:80px;",
									'value'=>"Delete All Rows", 
									'onclick' => $buyXGetY_table_name.'.deleteAllRows();'
									)
									);
		$buttonsproduct = array(
							
								
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:60px;",
									'value'=>"Delete Row", 
									'onclick' => $product_table_name.'.deleteRow();'
									),
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:80px;",
									'value'=>"Delete All Rows", 
									'onclick' => $product_table_name.'.deleteAllRows();'
									)
									);
		$buttonsbrand = array(
							
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:60px;",
									'value'=>"Add Row", 
									'onclick' => $brand_table_name.'.addRow();'
									),
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:60px;",
									'value'=>"Delete Row", 
									'onclick' => $brand_table_name.'.deleteRow();'
									),
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:80px;",
									'value'=>"Delete All Rows", 
									'onclick' => $brand_table_name.'.deleteAllRows();'
									)
									);
		$buttonscategory = array(
							
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:60px;",
									'value'=>"Add Row", 
									'onclick' => $category_table_name.'.addRow();'
									),
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:60px;",
									'value'=>"Delete Row", 
									'onclick' => $category_table_name.'.deleteRow();'
									),
								array('class' =>"thin_button",
									'type' =>"button",
									'style' =>"width:80px;",
									'value'=>"Delete All Rows", 
									'onclick' => $category_table_name.'.deleteAllRows();'
									)
									);
		$html .= '<H1>TO ASSIGN PROMOTION VALUES USE THE FOLLOWING TABLE</h1>';
		$html .= '<H2>This table is required for ALL Promotions. See examples below for use.</h2>';
		//$html .= '<P> Use the Buy X Get Y Table (ignored for the $x off $y or Full item??...</p>';
		$html .= createDynamicTableReuseV3( $buyXGetY_table_name, createBUYxGETyTableDef($buyXGetY_table_name), $buyXGetY_contents, ' class="dynamic_contents_table"  ', $buttonsbuyxgety);
		$html .='<br>';
		$html .= '<H1>The Following Tables are optional and further limit the promotions to Products, Product Category, and Brands.</h1>';
		$html .='<br>';
		$html .= '<P>Limit the promotion to products within the following categories...</p>';
		$html .= createDynamicTableReuseV3($category_table_name, createCategoryToPromotionTableDef($category_table_name), $category_contents, ' class="dynamic_contents_table"  ', $buttonscategory);
		$html .= '<P>Limit the promotion to products within the following brands...</p>';
		$html .= createDynamicTableReuseV3($brand_table_name, createBrandToPromotionTableDef($brand_table_name), $brand_contents, ' class="dynamic_contents_table"  ', $buttonsbrand);
		$html .= '<P>Limit the promotion to the following products...</p>';
		$html .= '<P>NOTE this limitation is for product id\'s, not the sub id. Scan the sub-id barcode to load the product.</p>';
		$html .= '<table id = "hello" style="width:75%"><tr><td>';
		$html .= InventoryProductLookUpTable();
		$html .= createDynamicTableReuseV3($product_table_name, createProductToPromotionTableDef($product_table_name), $product_contents, ' class="dynamic_contents_table"  ', $buttonsproduct);
		$html .= '</td></tr></table>';
	
		//$html .=  '<INPUT class = "button" type="button"  value="Save" onclick="saveDraft()" />'.newline();
		//$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Save/Exit" onclick="saveDraftAndGo(\''.$go_url.'\')" />'.newline();
		
	
		
	
		$html .= promotion_instructions();
		$html .= '<input class = "button" name = "submit" type="submit" value="Submit"/>';
		$html .= '<input class = "button" name = "cancel" type="button" value="Cancel"  onclick="cancelPromotion()"/>';
		//$html .= '<input class = "button" name = "test button" type="button" value="test" onclick="validatePromotionForm()"/>';
		$html .=  '</form>';
		$html .= '<script>document.getElementsByName("promotion_code")[0].focus();</script>';
	
	
		
	}
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
function createpromotionTableDef($type, $pos_promotion_id)
{
	if ($pos_promotion_id =='TBD')
	{
		$unique_validate = array('unique' => 'promotion_code', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_promotion_id'] = $pos_promotion_id;
		$unique_validate = array('unique' => 'promotion_code', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_promotion_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Promotion ID',
								'value' => $pos_promotion_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'promotion_code',
								'type' => 'input',
								'tags' => ' onkeyup = "checkPromoInput()" onblur = "checkPromoCode()" ',
								'db_table' => 'pos_promotions',
								'caption' => 'Promotion Code (no spaces, not an integer)',
								'validate' => $unique_validate),	
						array('db_field' =>  'promotion_name',
								'type' => 'input',
								'caption' => 'Promotion Description',
								'validate' => 'none'),
						array('db_field' => 'start_date',
								'caption' => 'Start Date',
								'type' => 'date',
								'separate_date' => 'date',
								'tags' => ' ',
								'html' => dateSelect('start_date',''),
								'validate' => 'date'),
						array('db_field' => 'expiration_date',
								'caption' => 'Expiration Date',
								'type' => 'date',
								'separate_date' => 'date',
								'tags' => ' ',
								'html' => dateSelect('expiration_date',''),
								'validate' => 'date'),
						
						array('db_field' =>  'promotion_type',
								'type' => 'select',
								'caption' => 'Promotion Tax Type (Mfg Coupons Are post-tax, otherwise in-store promotions are pre-tax)',
								'html' => createEnumSelectFast('promotion_type',array("Pre Tax","Post Tax"), 'false',  'off')),
	
						array('db_field' =>  'item_or_total',
								'type' => 'select',
								'caption' => 'Is the Promotion Taken Per ITEM or based on a TOTAL amount?',
								
								
								'html' => createEnumSelectFast('item_or_total',array("ITEM","TOTAL"), 'false',  'off', "onclick = 'item_or_total_change()'")),
						array('db_field' =>  'percent_or_dollars',
								'type' => 'select',
								'caption' => 'Is the Promotion Calculated % or dollars?',
								'html' => createEnumSelectFast('percent_or_dollars',array("$","%"), 'false',  'off', "onclick = 'percent_change()'")),	
						
						array('db_field' =>  'blanket',
								'type' => 'checkbox',
								'caption' => 'Check for Balnket Discount ( apply the promotion to as many items as possible) <br>Default is promotion only applies once.',
								'value' => '0'),
						array('db_field' =>  'check_if_can_be_applied_to_sale_items',
								'type' => 'checkbox',
								'caption' => 'Check This if promotion Can Be Applied To Sale Items',
								'value' => '0'),
						array('db_field' =>  'check_if_can_be_applied_to_clearance_items',
								'type' => 'checkbox',
								'caption' => 'Check This if promotion Can Be Applied To Clearance Items',
								'value' => '0'),
						array('db_field' =>  'qualifying_amount',
								'type' => 'input',
								'caption' => 'Item Qualifying Amount',
								'validate' => 'number'),
						
						
						
						
						
						
						/*array('db_field' =>  'promotion_amount',
								'type' => 'input',
								'caption' => 'Promotion Amount',
								'validate' => 'number'),*/
						
								
						array('db_field' =>  'expired_value',
								'type' => 'input',
								'caption' => 'Post Expiration Promotion Amount',
								'validate' => 'number'),
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	

}
function createCategoryToPromotionTableDef($table_object_name)
{
	$table_object_name = $table_object_name . '';

	$categories = getCategoryArray();

	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						//'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}')
												),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),

					
					array('caption' => 'Include<br>or<br>Exclude',
						'db_field' => 'include_category',
						'type' => 'select',
						//this part is for the 'view'
						
						'html' => createEnumSelectFast('include_category', array('INCLUDE', 'EXCLUDE'), 'INCLUDE', 'off', ''),

						'default_value' => 'INCLUDE',
						'select_names' => array('INCLUDE','EXCLUDE'),
						'select_values' => array('INCLUDE','EXCLUDE'),
						'properties' => array(
						
						'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										
											)
							),	
					array('caption' => 'Category',
					'db_field' => 'pos_category_id',
					'type' => 'tree_select',
					//this part is for the 'view'
					'html' => 
					 createCategoryTreeSelect('pos_category_id', 'false'),
					'select_array' => getCategoryTree(),
					'select_values' => $categories['pos_category_id'],
					'select_names' => $categories['name'],
					'properties' => array(	'style.width' => '"50em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											
											//'onkeyup' => 'function(){updateDiscount(this);}',
											//'onmouseup' => 'function(){updateDiscount(this);}'
											)
											),
						
					array('db_field' => 'include_subcategories',
					'caption' => 'Include<BR>Sub categories',
					'type' => 'checkbox',
					'element' => 'input',
					'default_value' => '1',
					'element_type' => 'checkbox',
					'properties' => array(	
					'disabled' => 'false',
					//'checked' => true	no default value ;-)					
					)),

				
					
				);			
						
		
		return $columns;
	
	
	
}
function createBrandToPromotionTableDef($table_object_name)
{
	$table_object_name = $table_object_name . '';

	$brands = getFieldRowSql("SELECT pos_manufacturer_brand_id, brand_name FROM pos_manufacturer_brands WHERE active = 1 ORDER BY brand_name asc");

	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						//'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}')
												),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),

				
			array('caption' => 'Include<br>or<br>Exclude',
						'db_field' => 'include_brand',
						'type' => 'select',
						//this part is for the 'view'
						'default_value' => 'INCLUDE',
						'html' => createEnumSelectFast('include_brand', array('INCLUDE', 'EXCLUDE'), 'INCLUDE', 'off', ''),

						'select_names' => array('INCLUDE','EXCLUDE'),
						'select_values' => array('INCLUDE','EXCLUDE'),
						'properties' => array(
						
						'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										
											)
							),

						
				
						
					array('caption' => 'Brand',
					'db_field' => 'pos_manufacturer_brand_id',
					'type' => 'select',
					//this part is for the 'view'
					'html' => createManufacturerBrandSelect('pos_manufacturer_brand_id', 'false'),

					'select_names' => $brands['brand_name'],
					'select_values' => $brands['pos_manufacturer_brand_id'],
					'properties' => array(	'style.width' => '"50em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											
											//'onkeyup' => 'function(){updateDiscount(this);}',
											//'onmouseup' => 'function(){updateDiscount(this);}'
											)
											)
					

				
					
				);			
						
		
		return $columns;
	
	
	
}
function createProductToPromotionTableDef($table_object_name)
{
	$table_object_name = $table_object_name . '';


	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						//'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}')
												),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),

					array('caption' => 'Include<br>or<br>Exclude',
						'db_field' => 'include_product',
						'type' => 'select',
						//this part is for the 'view'
						'html' => createEnumSelectFast('include_product', array('INCLUDE', 'EXCLUDE'), 'INCLUDE', 'off', ''),

						'default_value' => 'INCLUDE',
						'select_names' => array('INCLUDE','EXCLUDE'),
						'select_values' => array('INCLUDE','EXCLUDE'),
						'properties' => array(
						
						'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										
											)
							),
					array(
					'db_field' => 'pos_product_id',
					'caption' => 'Product Id',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"',
											
											'size' => '"30"')
						),
					array('caption' => 'Item',
						'db_field' => 'item',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'valid_input' => '-'.uppercase().lowercase().safesymbols().integers(), //'-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('specific'=>uppercase().lowercase().safesymbols().integers())),
						'properties' => array(	
											'readOnly' => 'true',
											'className' => '"readonly"',
												'size' => '"300"',
												
												'onkeypress' => 'function(e){return noEnter(e);}'
												)),
				
			

						
				
				
					
				);			
						
		
		return $columns;
	
	
	
}
function createBUYxGETyTableDef($table_object_name)
{


	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						//'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}')
												),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),

					array('db_field' => 'buy',
					'caption' => 'Buy QTY<BR>For buy 3 get 1 free <br> Put in Buy 4 Get 1 @ 100% ',
						'type' => 'input',
						'round' => 0,
						'element' => 'input',
						'element_type' => 'text',
						'valid_input' => '0123456789',//.integers(), //'-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('specific'=>uppercase().lowercase().safesymbols().integers())),
						'properties' => array(	
											
												'size' => '"30"',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}'
												)),
				array('db_field' => 'get',
					'caption' => 'GET QTY<br>Ignored for Promotions<br> Based on Total Amount',
						'type' => 'input',
						'valid_input' => '0123456789', //'-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
						'round' => 0,
						'element' => 'input',
						'element_type' => 'text',
						'valid_input' => ''.integers(), //'-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('specific'=>integers())),
						'properties' => array(	
											
												'size' => '"30"',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}'
												)),
												
					
			array('db_field' => 'discount',
					'caption' => 'Discount Amount',
						'type' => 'input',
						'round' => 2,
						'element' => 'input',
						'element_type' => 'text',
						'valid_input' => '0123456789.', //'-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('specific'=>integers())),
						'properties' => array(	
											
												'size' => '"30"',
												'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}'
												)),
array('caption' => '$ or %',
					'db_field' => 'd_or_p',
					'type' => 'select',
					//this part is for the 'view'
					'html' => createEnumSelect('d_or_p','pos_promotion_buy', 'd_or_p', 'false',  'off'),

					'select_names' => array('$','%'),
					'select_values' => array('$','%'),
					'properties' => array(	
					'disabled' => '"true"', 'style.width' => '"5em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											
											
											)
											)
					

				
					
				);		
						
		
		return $columns;
	
	
	
}
function InventoryProductLookUpTable()
{
	
	$html =  '<TABLE style="width:100%">';
	$html .= '<tr>'.newline();
	//$html .= '<td style="vertical-align:bottom;">'.newline();
	$html .= ' <TD style="vertical-align:bottom;width:10%;"><INPUT TYPE="TEXT" class="lined_input"  id="barcode" style = "background-color:yellow;width:120px" value="BARCODE HERE" NAME="barcode" onclick="this.select()" onKeyPress="return disableEnterKey(event)" onKeyDown="lookUpBarcodeID(this, event)"	/>';
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;width:80px;">'.newline();
	$html .= '<input class = "button2" type="button" style="width:80px;" name="add_barcode" value="Add" onclick="addBarcodeButton()"/>';
	$html .= '</td>'.newLine();
	
	$html .= '<td style="vertical-align:bottom;">'.newline();
	$html .= ' <style>
.ui-autocomplete-loading {
background: white url("'.POS_ENGINE_URL . '/includes/images/ui-anim_basic_16x16.gif") right center no-repeat;
}
</style>';
	$html.= '<div class="ui-widget">

<input id="product_search"  value="OR Search..." style = "border: 1px solid black;width:100%;" />
</div>';
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;width:80px;">'.newline();
	$html .= '<input class = "button2" type="button" style="width:80px;" name="add_prodcut_subid" value="Add" onclick="addSubidFromSearch()"/>';
	$html .= '</td>'.newLine();
	$html .= '</tr>'.newline();
	$html .=  '</table>';
	$html .= addBeepV3().newline();
	return $html;


}
function promotion_instructions()
{
	
		$html= '<div id = "promotion_instructions">';
		$html .= '<h1>Here is how to create promotions</h1>';
		$html .= '<h2>The Basics.........................</h2>';
		$html .= '<p>Promotion Code is short and sweet and sweet and will be read by the barcode. Ex VIP25 BRA10 HOLIDAY2014. Do not include whitespaces and do not make it a number</p>';
		$html .= '<p>Promotion Description will show up on the customer receipt.</p>';
		$html .= '<p>Promotion type: Pre Or Post Tax: In store promotions - or promotions you create - apply a discount before tax is calculated. In Store promotions will therfore be pre-tax. Post Tax promotions apply a discount after tax is calculated. Examples of post tax promotions include Groupon and Manufacturer Coupons. Almost always you will choose pre-tax. </p>';
		
		
		$html .= '<h2>Examples of common promotions and how to create them:</h2>';
		
		$html .= '<h3>20% Off Dresses (Over $99)</h3>';
		$html .= '<p>PreTax. Item. Choose %. Choose Blanket Disocunt. Set Item Qualifying  Amount 99. Add Buy 1 Get 1 item at 20 % to the buy X get Y table. Include category Dresses. </p>';
		
		$html .= '<h3>3 panties for $30 (price is $12 each)</h3>';
		$html .= '<p>PreTax. Choose Item. Choose $. Choose Blanket Discount. Item Qualifying Amount 0. On the buy x get y table add Buy 3 Get 3 items at 2 $ Discount, Or Buy 3 get 1 at 6 $. Limit to  Individual products. </p>';
		$html .= '<br>';
		
		$html .= '<h3>Wolford Buy 3 get 1 Free (In other words, Buy 4 items, and we will give you one at 100% off)</h3>';
		$html .= '<p>PreTax. Choose Item. Choose %. Choose Blanket Discount. Item Qualifying Amount 0. On the buy x get Y Buy 4  Get 1 items at 100 (%) Discount. Limit to  Wolford Brand. </p>';
		$html .= '<br>';
	
		$html .= '<h3>Buy 1 Get 1 Free (In other words, Buy 2 items, and we will give you one at 100% off)</h3>';
		$html .= '<p>PreTax. Choose Item. Choose %. Choose Blanket Discount. Item Qualifying Amount 0. On the buy x get Y Buy 2  Get 1 items at 100 (%) Discount.  </p>';
		$html .= '<br>';
		
		$html .= '<h3>Buy 1 Get 1 50% off. (In other words, Buy 2 items, and we will give you one at 50% off)</h3>';
		$html .= '<p>PreTax  Choose Item. Choose %. Blanket Discount. Item Qualifying Amount $0. ON the buy X Get Y Buy 2 items Get on 1 item at 50 (%). </p>';
		$html .= '<br>';
		
		$html .= '<h3>$10 off all bras over $50.</h3>';
		$html .= '<p>PreTax Choose Item.  Choose Blanket Discount. Item Qualifying Amount $50. On the Buy x Get Y Table  Buy 1 Get 1 $10 Discount. Limit to bras. </p>';
		$html .= '<br>';
		
		$html .= '<h3>$25 off $125 Coupon.</h3>';
		$html .= '<p> PreTax. Choose Total. Set Item Qualifying Amount to $0. Uncheck Blanket Discount. On the Buy X Get Y table Buy 125 ignore Get Set Discount to 25.</p>';
		$html .= '<br>';
		
		$html .= '<h3>$10 off $100, $25 off $150, $50 off $200</h3>';
		$html .= '<p> PreTax. Choose Total. Set Item Qualifying Amount to $0. Uncheck Blanket Discount. On the Buy X Get Y table Buy 100 ignore Get Set Discount to 10, add row Buy 150 ignore Get Set Discount to 25, add row Buy 200 ignore Get Set Discount to 50. </p>';
		$html .= '<br>';
		
		$html .= '<h3>10% off $100, 15% off $150, 20% off $200 all items over $20</h3>';
		$html .= '<p>Pretax. Choose %. Set Item Qualifying Amount to $20. Uncheck Blanket Discount. On the Buy X Get Y table Buy 100 ignore Get Set Discount to 10, add row Buy 150 ignore Get Set Discount to 15, add row Buy 200 ignore Get Set Discount to 20. Discount applies to all full priced items.</p>';
		$html .= '<br>';
		
		
		$html .= '<h3>NOTES ON POST TAX PROMOTIONS</h3>';
		$html .= '<p>Post tax promotions are basically manufacturer coupons. These coupons are typically purchased by customers through a third party. Amazon Local, Groupon, kiss my Perks (entercom radio) are all examples of post tax coupons. Post tax coupons are a systematic nightmare, not to mention just a bad idea in my own opinion...    so post tax promotions work a bit differently than the pre tax promotions. They only apply to full price items. They only work with $, not %. They do not apply via blanket discount. You can include/exclude items, brands, and catagories. They apply after all the instore promotions and tax. They are not optimized to find the best value. One final note is that these coupons are basically considered tender, and presents a massive accounting challange. You need to report the sale of these coupons, and guess what.... unused coupon revenue can be seized by the state. Finally, you need to be prepared to track coupon fraud as you are now dealing with an alternate computer & financial systems. Now I\'m not being lazy and avoiding optimizing these coupons, I\'m just drawing the line on what is important. In store promotions are important, third party promotions are a bad idea.</p>';
		$html .= '<br>';
		
		$html .= '<h3>Groupon: $50 for $25</h3>';
		$html .= '<p>Choose Post Tax. Choose Total.  Set Item Qualifying Amount to $0. Post expired amount is up to the offer, but at time of this writing is $25 for 5 years. Uncheck Blanket Discount, Sale and Clearance. On the Buy X Get Y table ignore Buy ignore Get Set Discount to 50.  Discount applies to all full priced items.</p>';
		$html .= '<br>';
		
		
		$html .= '<h1>Here are some promotions that are very difficult to implement, and may have undersired results.</h1>';
		$html .= '<h2>For these promotions it may be best to use a disount on an item</h2>';

		$html .= '<h3>25% off Matching Panty when purchasing a Bra</h3>';
		$html .= '<p>Choose %. Do not use blanket discount. Item Qualifying Amount amount 0. On the buy y get x Buy 1 Get 1 item at 25 (%). Limit to Category Panty. Techincally this promotion does not work as we cannot track "matching" items, so the sales associates needs to apply some common sense when choosing the promotion. Each promotion will deduct 25% from a single panty on the invoice, lowest priced panty first. In this case it is probably better to use a discount code on the panty.</p>';
		$html .= '<br>';
		
		$html .= '<h3>25% off a Coverup when purchasing a Swimsuit</h3>';
		$html .= '<p>Choose %. Do not use blanket discount. Item Qualifying Amount amount 0. On the buy y get x Buy 1 Get 1 item at 25 (%). Limit to Category Coverups. The code will look for anything priced higher than the coverup as there is no link between swim suit and coverup. In this case it is probably better to use a discount code on the cover-up. </p>';
		
		
		
		$html .= '</div>';
	return $html;
}



?>