<?php
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);



function createLocationTableDef($type, $pos_location_id, $pos_parent_location_id = 'false')
{
	if ($pos_location_id =='TBD')
	{
		$unique_validate = array('unique_group' => array('pos_store_id', 'location_name', 'pos_parent_location_id'), 'min_length' => 1);

	}
	else
	{
		$key_val_id['pos_location_id'] = $pos_location_id;
		$unique_validate = array('unique_group' => array('pos_store_id', 'location_name', 'pos_parent_location_id'), 'min_length' => 1, 'id' => $key_val_id);


	}
	
	return array( 
						array( 'db_field' => 'pos_location_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Location ID',
								'value' => $pos_location_id,
								'validate' => 'none'
								
								),
							array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => array('select_value' => 'false')),
						array('db_field' =>  'location_name',
								'type' => 'input',
								'caption' => 'Location Name',
								'db_table' => 'pos_locations',
								'validate' => $unique_validate),
						array('db_field' =>  'pos_parent_location_id',
								'type' => 'select',
								'caption' => 'Parent Location',
								'html' => createLocationSelect('pos_parent_location_id', $pos_parent_location_id,  'off')),
						array('db_field' =>  'pos_location_group_id',
								'type' => 'select',
								'caption' => 'Location Group',
								'html' => createLocationGroupSelect('pos_location_group_id', 'false',  'off')),
						array('db_field' =>  'priority',
								'type' => 'input',
								'tags' => numbersOnly(),
								'caption' => 'Inventory Priority (Pull Inventory from higher number locations first)',
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
function createbulkAddLocationTableDef($type = 'dynamic')
{
	//$stores = getFieldRowSql("SELECT 0 as pos_store_id, 'No Store' as store_name UNION SELECT pos_store_id,store_name FROM pos_stores where active = 1");
	$stores = getFieldRowSql("SELECT pos_store_id,store_name FROM pos_stores where active = 1");

	//$parentLocations = getFieldRowSql("SELECT 0 as pos_location_id, 'No Parent' as location_name UNION SELECT pos_location_id,location_name FROM pos_locations");
	$parentLocations = getFieldRowSql("SELECT pos_location_id,location_name FROM pos_locations");
	if(sizeof($parentLocations)==0)
	{
		$parentLocations['pos_location_id'] = array();
		$parentLocations['location_name'] = array();
		
	}
	$locationGroups = getFieldRowSql("SELECT pos_location_group_id,location_group_name FROM pos_location_groups");
	if(sizeof($parentLocations)==0)
	{
		$parentLocations['pos_location_group_id'] = array();
		$parentLocations['location_group_name'] = array();
		
	}
	$id = array(
					array('db_field' => 'pos_location_id',
						'type' => 'td_hidden_input',
						'caption' => 'Location Id'
						));
	$check = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
												)));
	$row = array(array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						));
	
		$columns = array(
				
					
					
					array('db_field' => 'pos_store_id',
						'caption' => 'Store',
						'type' => 'select',
						'html'=> createStoreSelect('pos_store_id[]', 'false',  'off'),
						'select_names' => $stores['store_name'],
						'select_values' => $stores['pos_store_id'],
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'value' => '"'.$_SESSION['store_id'].'"',
												'onchange' => 'function(){}')),
					
					array(
					'db_field' => 'location_name',
					'caption' => 'Location Name',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'size' => '"25"')
						),

					array('db_field' => 'pos_location_group_id',
						'caption' => 'Location Group',
						'type' => 'select',
						'html' => createLocationGroupSelect('pos_location_group_id[]', 'false',  'off'),
						'select_names' => $locationGroups['location_group_name'],
						'select_values' => $locationGroups['pos_location_group_id'],
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){}')),
					array('db_field' => 'pos_parent_location_id',
						'caption' => 'Parent Location',
						'type' => 'select',
						'html' =>  createLocationSelect('pos_parent_location_id[]', 'false',  'off'),
						'select_names' => $parentLocations['location_name'],
						'select_values' => $parentLocations['pos_location_id'],
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){}')),
					array('caption' => 'Comments',
					'db_field' => 'comments',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}'))
					
				);			
						
		if ($type == 'dynamic') 
		{
			$columns = array_merge($check,$row,$columns);
		}
		else
		{
			$columns = array_merge($row,$id,$columns);
		}
		return $columns;
}
function createbulkEditLocationTableDef()
{
	//$stores = getFieldRowSql("SELECT 0 as pos_store_id, 'No Store' as store_name UNION SELECT pos_store_id,store_name FROM pos_stores where active = 1");
	$stores = getFieldRowSql("SELECT pos_store_id,store_name FROM pos_stores where active = 1");

	//$parentLocations = getFieldRowSql("SELECT 0 as pos_location_id, 'No Parent' as location_name UNION SELECT pos_location_id,location_name FROM pos_locations");
	$parentLocations = getFieldRowSql("SELECT pos_location_id,location_name FROM pos_locations");
	if(sizeof($parentLocations)==0)
	{
		$parentLocations['pos_location_id'] = array();
		$parentLocations['location_name'] = array();
		
	}
	$locationGroups = getFieldRowSql("SELECT pos_location_group_id,location_group_name FROM pos_location_groups");
	if(sizeof($parentLocations)==0)
	{
		$parentLocations['pos_location_group_id'] = array();
		$parentLocations['location_group_name'] = array();
		
	}

	

	
		$columns = array(
		array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'row_number',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
				array('db_field' => 'pos_location_id',
						'type' => 'td_hidden_input',
						'caption' => 'Location Id'
						),
					
					
					array('db_field' => 'pos_store_id',
						'caption' => 'Store',
						'type' => 'select',
						'html'=> createStoreSelect('pos_store_id[]', 'false',  'off'),
						'select_names' => $stores['store_name'],
						'select_values' => $stores['pos_store_id'],
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'value' => '"'.$_SESSION['store_id'].'"',
												'onchange' => 'function(){}')),
					
					array(
					'db_field' => 'location_name',
					'caption' => 'Location Name',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'size' => '"25"')
						),

					array('db_field' => 'pos_location_group_id',
						'caption' => 'Location Group',
						'type' => 'select',
						'html' => createLocationGroupSelect('pos_location_group_id[]', 'false',  'off'),
						'select_names' => $locationGroups['location_group_name'],
						'select_values' => $locationGroups['pos_location_group_id'],
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){}')),
					array('db_field' => 'pos_parent_location_id',
						'caption' => 'Parent Location',
						'type' => 'select',
						'html' =>  createLocationSelect('pos_parent_location_id[]', 'false',  'off'),
						'select_names' => $parentLocations['location_name'],
						'select_values' => $parentLocations['pos_location_id'],
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){}')),
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
function createLocationGroupTableDef($type, $pos_location_group_id)
{
	if ($pos_location_group_id =='TBD')
	{
		$unique_validate = array('unique' => 'location_group_name', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_location_group_id'] = $pos_location_group_id;
		$unique_validate = array('unique' => 'location_group_name', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_location_group_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Location ID',
								'value' => $pos_location_group_id,
								'validate' => 'none'
								
								),
							
						array('db_field' =>  'location_group_name',
								'type' => 'input',
								'caption' => 'Location Group Name',
								'db_table' => 'pos_location_groups',
								'validate' => $unique_validate),
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	

}

function createInventoryInformationHTMLTable1($pos_store_id)
{
	$html = '<table>';
	$html .= '<tr>';
	$html .= '<th>Store</th><th>Location</th>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td>' .createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off') .'</td>';
	$html .= '<td><input type = "text" name="inventory_location" /></td>';
	$html .= '</tr>';
	$html .= '</table>';
	$html .= '<p>Updating Inventory for Location: ' . getStoreName($pos_store_id) . '. CLICK HERE to change your working location<p>';
	
	return $html;
}
function createInventoryInformationHTMLTable2()
{
	$html = '<table>';
	$html .= '<tr>';
	//$html .= '<th>Store</th>';
	$html .= '<th>Location</th>';
	//$html.='<th>Inventory Type<br>Only Record One Type?</th>';
	$html .= '</tr>';
	$html .= '<tr>';
	//$html .= '<td>' .createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off', ' onchange="updateLocations()" ') .'</td>';
	$html .= '<td>' .createLocationSelect('pos_location_id', 'false',  'off') .'</td>';
	$html .= '</tr>';
	$html .= '</table>';
	
	return $html;
}
function getLocations()
{
	$sql = "SELECT  
			pos_locations.pos_store_id, pos_locations.pos_location_id, if(pos_locations.pos_parent_location_id = 0, pos_locations.location_name, concat(x.location_name,'-',pos_locations.location_name)) as parent_child, pos_locations.pos_parent_location_id, concat('LOCATION::' ,pos_locations.pos_location_id) as barcode, x.location_name as parent_name, (select max(inventory_date) from pos_inventory_event where pos_location_id = pos_locations.pos_location_id) as last_inventory_date,
			(select sum(value) from pos_inventory_event_contents LEFT JOIN pos_inventory_event USING (pos_inventory_event_id) where pos_location_id = pos_locations.pos_location_id) as last_inventory_value,
			pos_locations.location_name, pos_stores.store_name, pos_locations.comments,
			pos_location_groups.location_group_name
						
			FROM pos_locations
			LEFT JOIN pos_stores ON pos_locations.pos_store_id = pos_stores.pos_store_id
			LEFT JOIN pos_locations as x ON pos_locations.pos_parent_location_id = x.pos_location_id
			LEFT JOIN pos_location_groups ON pos_locations.pos_location_group_id = pos_location_groups.pos_location_group_id
			ORDER by parent_child ASC"
			;
		return getSQL($sql);	
		
}
function createLocationSelect($name, $pos_location_id,  $option_all='off', $tags = ' onchange="needToConfirm=true" ')
{
	// use the default_store_id set on login to load a store. The company name should be selectable, then the address
	//option_all is used to add an 'all' option for the stores... this should be the default....
    //get the company info for the default store id
    $locations = getLocations();
    
	$html = '<select  style="width:100%;" name="' . $name . '" id="' . $name .'" class = "store_select"';
	$html .= $tags;
	$html .= '>';
	$html .= '<option value="false">No Location Selected</option>';
	
	for($i = 0;$i < sizeof($locations); $i++)
	{
		$html .= '<option value="' . $locations[$i]['pos_location_id'] . '"';
		//set the store to the default value or the selected value
		if ($locations[$i]['pos_location_id'] == $pos_location_id) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . strtoupper($locations[$i]['pos_location_id'] . '::' . getStoreName($locations[$i]['pos_store_id']) . '::' . $locations[$i]['parent_child']) . '</option>';
	}
	$html .= '</select>';
	return $html;

}
function createLocationGroupSelect($name, $pos_location_group_id,  $option_all='off', $tags = ' onchange="needToConfirm=true" ')
{
	// use the default_store_id set on login to load a store. The company name should be selectable, then the address
	//option_all is used to add an 'all' option for the stores... this should be the default....
    //get the company info for the default store id
    $location_groups = getSQL("SELECT pos_location_group_id, location_group_name FROM pos_location_groups WHERE active = 1");
    
	$html = '<select  style="width:100%;" name="' . $name . '" id="' . $name .'" class = "store_select"';
	$html .= $tags;
	$html .= '>';
	$html .= '<option value="false">No Location Group Selected</option>';
	
	for($i = 0;$i < sizeof($location_groups); $i++)
	{
		$html .= '<option value="' . $location_groups[$i]['pos_location_group_id'] . '"';
		//set the store to the default value or the selected value
		if ($location_groups[$i]['pos_location_group_id'] == $pos_location_group_id) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $location_groups[$i]['location_group_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;

}
function getLocationName($pos_location_id)
{
	$sql = "SELECT location_name FROM pos_locations WHERE pos_location_id = $pos_location_id";
	return getSingleValueSQL($sql);
}
function getParentLocationName($pos_location_id)
{
	$sql = "SELECT pos_parent_location_id FROM pos_locations WHERE pos_location_id = $pos_location_id";
	$parent_location_id = getSingleValueSQL($sql);
	if( $parent_location_id != 0)
	{
		return getLocationName($parent_location_id);
	}
	else
	{
		return false;
	}
}
function getParentChildLocationName($pos_location_id)
{
	if(getParentLocationName($pos_location_id))
	{
		return getParentLocationName($pos_location_id) . '-'. getLocationName($pos_location_id);
	}
	else
	{
		return getLocationName($pos_location_id);
	}
}
function getLocationStoreId($pos_location_id)
{
	$sql = "SELECT pos_store_id FROM pos_locations WHERE pos_location_id = $pos_location_id";
	return getSingleValueSQL($sql);
}

function printLocationLabelsForm($data, $filename)
{
			
		//add quantity in
		for($row = 0; $row<sizeof($data);$row++)
		{
			$data[$row]['quantity'] = 1;
			$data[$row]['row_checkbox'] = 1;
			
		}
		$array_table_def= array(	
			
					array(
							'th' => createCheckbox('TMP', ' disabled="disabled" '),
							'db_field' => 'row_checkbox',
							'th_width' => "15",
							'type' => 'checkbox',
							),
					array(
							'th' => 'Row',
							'db_field' => 'row_number',
							'type' => 'row_number'),
					array(
							'th' => 'System ID',
							'db_field' => 'pos_location_id',
							'type' => 'td_hidden_input'),
					array(
							'th' => 'Location Name',
							'db_field' => 'location_name',
							'type' => 'td'),

					array(	'th' => 'Quantity',
							'db_field' => 'quantity',
							'type' => 'input',
							'tags' => ' style="background-color:yellow" ')
					);
		$html = '<p>Select the Labels Needed</p>';
		$form_handler = POS_ENGINE_URL . '/inventory/Locations/print_location_labels.form.handler.php';
		$html .= '<form action="' . $form_handler.'" method="post">';
		
		$html .= 'Starting column: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="column_offset" style = "width:20px;" NAME="column_offset"/>'.newline();
		$html .= 'Starting row: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="row_offset" style = "width:20px;" NAME="row_offset"/>'.newline();
		$html .= createStaticArrayHTMLTable($array_table_def, $data);
		$html .= createHiddenInput('filename', $filename);
		$html .= '<input class = "button" style="width:150px" type="submit" name="select" value="Open Label File"/>';
		$html .= '<input class = "button" type="button" style="width:200px" name="add_location" value="Locations" onclick="open_win(\'list_locations.php\')"/>';
		$html .= '</form>';
		
		return $html;

}
function createMoveLocationInventoryTableDef()
{
		return array(	
			
					
					array(
							'th' => 'Row',
							'db_field' => 'row_number',
							'type' => 'row_number'),
					
					array(
							'th' => 'Product sub id System ID',
							'db_field' => 'pos_product_sub_id',
							'type' => 'hidden_input'),
					array(
							'th' => 'Subid Name <br>(Barcode)',
							'db_field' => 'product_subid_name',
							'type' => 'td'),
					
					array(	'th' => 'Quantity',
							'db_field' => 'quantity',
							'type' => 'td',
							'round' => 2),
					array('th' => 'New Location',
					'db_field' => 'new_pos_location_id',
						'type' => 'select',
						'html' => createlocationSelect('new_pos_location_id[]','false')),
						
					
					array(	'th' => 'Quantity to Move',
							'db_field' => 'quantity_adjustment',
							'type' => 'input',
							'round' => 0,
							'tags' => ' style="background-color:yellow" '),
						array(	'th' => 'Comments',
							'db_field' => 'Comments',
							'type' => 'input',
							)	

					
					

					);
}
function createAdjustLocationInventoryTableDef()
{
		return array(	
			
					array(
							'th' => 'Row',
							'db_field' => 'row_number',
							'type' => 'row_number'),
					array(
							'th' => 'Product Sub id System ID',
							'db_field' => 'pos_product_sub_id',
							'type' => 'hidden_input'),
					array(
							'th' => 'Subid Name <br>(Barcode)',
							'db_field' => 'product_subid_name',
							'type' => 'td'),
					array(
							'th' => 'Current<br>Value',
							'db_field' => 'value',
							'type' => 'td_hidden_input',
							'post_name' => 'current_value'),
					array(
							'th' => 'Current<br>Type',
							'db_field' => 'current_inventory_type',
							'type' => 'td_hidden_input',
							'post_name' => 'current_inventory_type'),
					array(	'th' => 'Quantity',
							'db_field' => 'quantity',
							'type' => 'td',
							'round' => 2),
					array(	'th' => 'Quantity Adjustment',
							'db_field' => 'quantity_adjustment',
							'type' => 'input',
							'round' => 0,
							'tags' => ' style="background-color:yellow" '),	
					array(	'th' => 'Comments',
							'db_field' => 'Comments',
							'type' => 'input',
							)	


					);
}
function createClearLocationInventoryTableDef()
{
		return array(	
			
					array(
							'th' => 'Clear',
							'db_field' => 'row_checkbox',
							/*'th_width' => "15",*/
							'type' => 'row_checkbox',
							),
					array(
							'th' => 'Row',
							'db_field' => 'row_number',
							'type' => 'row_number'),
					array(
							'th' => 'pos_product_sub_id',
							'db_field' => 'pos_product_sub_id',
							'type' => 'hidden_input'),
					array(
							'th' => 'Subid Name <br>(Barcode)',
							'db_field' => 'product_subid_name',
							'type' => 'td'),
					array(
							'th' => 'Title',
							'db_field' => 'title',
							'type' => 'td'),
					array('caption' => 'quantity',
						'db_field' => 'quantity',
						'type' => 'td',

						),
						array(	'th' => 'Comments',
							'db_field' => 'Comments',
							'type' => 'input',
							)	
					
					);
}

/********* INVENTORY COUNTING FUNCTIONS *************************/
function getMostRecentPhysicalCountInventoryDateForLocation($pos_location_id,$date)
{
	$sql = "SELECT max(inventory_date) FROM pos_inventory_event WHERE pos_location_id = $pos_location_id AND inventory_date <= '$date'";
	return getSingleValueSQL($sql);
}
function getMostRecentInventoryEventForLocation($pos_location_id,$date)
{
	$sql = "SELECT pos_inventory_event_id FROM pos_inventory_event WHERE pos_location_id = $pos_location_id AND inventory_date = '$date'";
	return getSingleValueSQL($sql);
}
function getLocationInvetory($pos_inventory_event_id)
{
	//create a record table showing the most recent results?
	
	//is it the most recent date or some other date?
/*if ( pos_inventory_event_contents.price_level > 0, (SELECT title FROM pos_product_sub_sale_price WHERE pos_product_sub_sale_price.pos_product_sub_id = pos_inventory_event_contents.pos_product_sub_id AND pos_product_sub_sale_price.price_level = pos_inventory_event_contents.price_level)  , title) as title,
*/
		
	$tmp_sql = "
	
	CREATE TEMPORARY TABLE location_inventory
	
	SELECT  
			 pos_products.pos_product_id,pos_inventory_event_contents.pos_product_sub_id, pos_inventory_event_contents.price_level, product_subid_name,  quantity, value, quantity*value as extension, inventory_type, pos_category_id, pos_manufacturer_brand_id, retail_price, cost, cost*quantity as cost_extension, style_number,pos_inventory_event_contents.comments,title,pos_inventory_event_contents.pos_inventory_event_content_id,
			
			
			
			
			if ( pos_inventory_event_contents.price_level > 0, (SELECT price FROM pos_product_sub_sale_price WHERE pos_product_sub_sale_price.pos_product_sub_id = pos_inventory_event_contents.pos_product_sub_id AND pos_product_sub_sale_price.price_level = pos_inventory_event_contents.price_level)  , sale_price) as sale_price,
			
			if ( pos_inventory_event_contents.price_level > 0, (SELECT clearance FROM pos_product_sub_sale_price WHERE pos_product_sub_sale_price.pos_product_sub_id = pos_inventory_event_contents.pos_product_sub_id AND pos_product_sub_sale_price.price_level = pos_inventory_event_contents.price_level)  , 0) as clearance,
			
			pos_inventory_event_contents.barcode
			
			
			
			FROM pos_inventory_event_contents
			LEFT JOIN pos_products_sub_id ON pos_inventory_event_contents.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id
			
			
			
			LEFT JOIN pos_products ON pos_products.pos_product_id = pos_products_sub_id.pos_product_id

	WHERE pos_inventory_event_id = $pos_inventory_event_id 
	ORDER BY pos_inventory_event_contents.pos_inventory_event_content_id ASC
	
	;
	
	
	";	
	
	$tmp_select_sql = "SELECT *
		FROM location_inventory WHERE 1";
	$dbc = openPOSdb();
	
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	for($i=0;$i<sizeof($data);$i++)
	{
		$pos_product_sub_id = $data[$i]['pos_product_sub_id'];
		/*$data[$i]['size'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Size'));
		$data[$i]['color_code'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Color'));
		$data[$i]['cup'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Cup'));	
		$data[$i]['color_description'] = getProductOptionName($pos_product_sub_id, getProductAttributeId('Color'));*/
		$data[$i]['item'] = getProductSubidBrandTitleStyleOptions($pos_product_sub_id);
		/*if($data[$i]['price_level']>0)
		{
			
			$data[$i]['item'] = getProductSubidBrandTitleStyleOptions($pos_product_sub_id). ' - Sale Price titled ' . $data[$i]['title'];
		}*/
		
	}
	return $data;
}
function getInventoryLocation($pos_inventory_event_id)
{
	return getSingleValueSQL("SELECT pos_location_id FROM pos_inventory_event WHERE pos_inventory_event_id = $pos_inventory_event_id");
}
function getLocationInventoryForASubid($pos_product_sub_id, $pos_location_id, $date)
{
	//I might want to know the count on 9-11-2001 or today.
	//to get the count we have to go back to the last physical count date
	
	$last_physical_inventory_date = getMostRecentPhysicalCountInventoryDateForLocation($pos_location_id, $date);
	
	$tmp_sql = "
	
	CREATE TEMPORARY TABLE location_inventory
	
	SELECT  DISTINCT pos_inventory_event_contents.pos_product_sub_id, product_subid_name, sum(quantity), inventory_type, unique_tag
			
	FROM pos_inventory_event_contents
	LEFT JOIN pos_products_sub_id ON pos_products_sub_id.pos_product_sub_id = pos_inventory_event_contents.pos_product_sub_id
	WHERE pos_location_id=$pos_location_id 
	AND inventory_date >= '$last_physical_inventory_date' 
	AND pos_inventory_event_contents.pos_product_sub_id = $pos_product_sub_id
	
	
	;
	";
	
	$tmp_select_sql = "SELECT *
		FROM location_inventory WHERE 1";
	$dbc = openPOSdb();
	
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	return $data;
	
}

function addLocationLabelToPdfAvery5167($pdf, $pos_location_id, $row, $col)
{
	
	//barcode: 128a?
	// define barcode style
	$barcode_style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => false,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => '0',
    'vpadding' => '0',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => false,
    'font' => 'helvetica',
    'fontsize' => 4,
    'stretchtext' => 0
);
	
				
	$cell_width = 1.75;
	$cell_height = 0.5;
	$cell_spacing = 0.3;
	$line_spacing_adjust = 0.015;
	$barcode_spacing_adjust = 0.1;
	$barcode_height_adjust = 0.05;		
				
	// set border width
	$pdf->SetLineWidth(0.01);
	$pdf->SetDrawColor(0,0,0);			
				
	
	$parent_location = (getParentLocationName($pos_location_id)) ? ' ' . getParentLocationName($pos_location_id) : '';
			
	$barcode = 'L' . $pos_location_id;
	$parent = getStoreName(getLocationStoreId($pos_location_id)) . $parent_location;
	$location_name = getLocationName($pos_location_id);
	
		
	$x_spot = $cell_spacing + $col*$cell_width + $col*$cell_spacing;
	$y_spot = $cell_height + $row*$cell_height;
	$coords = 'X:'.$x_spot . ' Y:' .$y_spot;
	$border = 0;
	$border2 = 0;
	//this is the cell that will allow allignment to sticker checking
	$pdf->SetXY($x_spot, $y_spot);
	$pdf->Cell($cell_width, $cell_height,  '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
	
	// CODE 128 A
	$pdf->SetXY($x_spot+$barcode_spacing_adjust, $y_spot);
	//cell to check the barcode placement
	$pdf->Cell($cell_width-2*$barcode_spacing_adjust, $cell_height/2, '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
	$pdf->write1DBarcode($barcode, 'C128A', $x_spot+$barcode_spacing_adjust, $y_spot+$barcode_height_adjust, $cell_width-2*$barcode_spacing_adjust, $cell_height/2 - $barcode_height_adjust, 0.4, $barcode_style, 'N');
	
	//the remaining 3 lines have to fit in 1/2 the sticker size
	//$y_offset = $cell_height/2;
	$pdf->SetXY($x_spot, $y_spot - 0*$line_spacing_adjust + 3/6*$cell_height);
	$pdf->Cell($cell_width, $cell_height/6,  $barcode, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
	$pdf->SetXY($x_spot, $y_spot - 1*$line_spacing_adjust + 4/6*$cell_height);
	$pdf->Cell($cell_width, $cell_height/6,  $parent, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
	$pdf->SetXY($x_spot, $y_spot -2*$line_spacing_adjust + 5/6*$cell_height);
	$pdf->Cell($cell_width, $cell_height/6,  $location_name, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
				
	

		
	
		return $pdf;
			
}




?>