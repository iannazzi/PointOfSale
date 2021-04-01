<?

/*
	this will create both products and inventory quantities
	
	Select the brand, enter the style number, color code, color description, title, size, cost, retail, sale price, and quantity
	
	if the product already exists we will update the inventory
	
	
*/
		
$binder_name = 'Locations';
$access_type = 'Write';
$page_title = 'Inventory By Location';
require_once ('../inventory_functions.php');		
		
		
$inventory_javasript = 'inventory.2014.08.26.js';
/*
	i want to know the sum the quantities in and out since the most recent physical count
	
	in is a receive event => that is going to have to transfer the item into a location
	
	when bras come down stairs they need to be 'added' to the location.
	
	
	etc....
	
	or we just put them in and then scan the location....
*/
$type = getPostOrGetValue('type');
//if an event id is not in the get then create one and redirect
if($type == 'edit')
{
	if(ISSET($_GET['pos_inventory_event_id']))
	{
		$pos_inventory_event_id = $_GET['pos_inventory_event_id'];
		
		//check and lock the entry
		$db_table = 'pos_inventory_event';
		$key_val_id['pos_inventory_event_id'] = $pos_inventory_event_id;
		check_lock($db_table, $key_val_id,POS_ENGINE_URL .'/inventory/Locations/inventory.php?type=edit&pos_inventory_event_id='.$pos_inventory_event_id, getBinderURL($binder_name) . '?message=canceled');
		//lock the entry
		lock_entry($db_table, $key_val_id);
	
		$pos_location_id = getInventoryLocation($pos_inventory_event_id);
		$inventory_contents = getLocationInvetory($pos_inventory_event_id);
		//preprint($inventory_contents);
	
		//$form_handler = 'inventory_by_location.form.handler.php';
		$complete_location = 'inventory.php?type=view&pos_location_id='.$pos_location_id;
		$cancel_location = $complete_location . '&message=Canceled';
	
		$go_url = 'inventory.fh.php?pos_inventory_event_id='.$pos_inventory_event_id;//'view_location.php?pos_location_id='.$pos_location_id;
		$form_id = "inventory_form";
		$form_action = 'inventory.fh.php';
		//Inventory contents table
	
		$inventory_table_name = 'inventory_table';
		$inventory_table_def = createBulkProductInventoryLoaderTableDef($inventory_table_name);
	
		$html =  '<form id = "' . $form_id . '" action="'.$form_action.'" method="post" onsubmit="return validateInventoryForm()">';
	
		$html .= '<h3>Physical Count Inventory For Location ID '.$pos_location_id . ' Named '.getLocationName($pos_location_id).'</h3>';
		$html .=  '<script src="'.$inventory_javasript.'"></script>'.newline();
		//$html .= '<script>var pos_location_id = '.$pos_location_id. ';</script>';
		//$html .= '<script>var inventory_date = "'. $inventory_date. '";</script>';
		$html .= '<script>var pos_inventory_event_id = '. $pos_inventory_event_id. ';</script>';

		$html .= InventoryProductLookUpTable();
		$html.= createHiddenInput('pos_location_id', $pos_location_id);
		$buttons = array(
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:60px;",
								'value'=>"Delete Row", 
								'onclick' => $inventory_table_name.'_object.deleteRow();document.getElementById(\'barcode\').focus();'
								),
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:80px;",
								'value'=>"Delete All Rows", 
								'onclick' => $inventory_table_name.'_object.deleteAllRows();;document.getElementById(\'barcode\').focus();'
								)
								);
		$html .= createDynamicTableReuseV2($inventory_table_name, $inventory_table_def, $inventory_contents, $form_id, ' class="dynamic_contents_table" style="width:100%" ', $buttons);
	
		$html .=  '<INPUT class = "button" type="button"  value="Save" onclick="saveDraft()" />'.newline();
		$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Save/Exit" onclick="saveDraftAndGo(\''.$go_url.'\')" />'.newline();
	
	
	
		$html .=  '</form>';
		$html .= '<script>document.getElementsByName("barcode")[0].focus();</script>';
$html .= '<script>document.getElementsByName("barcode")[0].select();</script>';
	
	}
	else
	{
		$pos_location_id = getPostOrGetID('pos_location_id');
		$dbc = startTransaction();
		$pos_inventory_event_id = createNewInventoryEvent($dbc, $pos_location_id);
		simpleCommitTransaction($dbc);
		header('Location: inventory.php?type=edit&pos_inventory_event_id='. $pos_inventory_event_id);
		exit();

	}

}
else if($type == 'view')
{
	
	if(isset($_POST['pos_location_id']) OR isset($_GET['pos_location_id']))
	{
		$pos_location_id = (isset($_POST['pos_location_id'])) ? $_POST['pos_location_id'] : $_GET['pos_location_id'];
	}
	else if(isset($_POST['pos_inventory_event_id']) OR isset($_GET['pos_inventory_event_id']))
	{
		$pos_inventory_event_id = (isset($_POST['pos_inventory_event_id'])) ? $_POST['pos_inventory_event_id'] : $_GET['pos_inventory_event_id'];
		$pos_location_id = getInventoryLocation($pos_inventory_event_id);
	}
	else
	{
		Trigger_error('missing id');
	}

	$location_name = getLocationName($pos_location_id);
	$parent_child_name = getParentChildLocationName($pos_location_id);
	$page_title = $parent_child_name;


	$complete_location = 'list_locations.php';
	$cancel_location = 'list_locations.php?message=Canceled';
	$edit_location = 'add_edit_location.php?pos_location_id='.$pos_location_id.'&type=edit';
	$delete_location = 'delete_location.form.handler.php?pos_location_id='.$pos_location_id;

	$db_table = 'pos_locations';
	$key_val_id['pos_location_id']  = $pos_location_id;
	$data_table_def = createLocationTableDef('View', $pos_location_id);
	$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);


		//now the delete
	
	

	$html = printGetMessage('message');
	$html = '<h3>Inventory Location: ' . $location_name .'</h3>';
	$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($table_def_w_data);
	$html .= '<p><input class = "button"  type="button" style="width:180px" name="edit"  value="Edit Location Details" onclick="open_win(\''.$edit_location.'\')"/>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Add Sub Location" onclick="window.location = \'add_edit_location.php?pos_parent_location_id='.$pos_location_id.'&type=add\'" />';
		// $html .= '<input class = "button" type="button" name="delete" value="Delete Room" onclick="confirmDelete();"/>';



	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Locations" onclick="window.location = \''.$complete_location.'\'" />';

	//$html .= '</p>';


	//now list the inventory

	$html .=getLocationInventoryList($pos_location_id);
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Locations" onclick="window.location = \''.$complete_location.'\'" />';

	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);

}
else if($type == 'print')
{
	$pos_location_id = getPostOrGetID('pos_location_id');
	
	
	
	$last_physical_inventory_date = getMostRecentPhysicalCountInventoryDateForLocation($pos_location_id, getDateTime());
	if($last_physical_inventory_date != '')
	{
	$pos_inventory_event_id = getMostRecentInventoryEventForLocation($pos_location_id,$last_physical_inventory_date);
	$inventory_contents = getLocationInvetory($pos_inventory_event_id);
	}
	else
	{
		$inventory_contents = array();
	}
	$filename = getLocationName($pos_location_id) . '_' .$last_physical_inventory_date . '_inventory.pdf';
	$html = printLocationProductLabelsForm($pos_location_id, $inventory_contents, $filename);
	
}
else if($type == 'sale')
{
	
	

	//this is a static table for now. User organizes items into bins of like priced merchandise
	
	// the table pos_product_sub_sale_price holds the subid, the barcode modifier, and the sale price.
	// the combination of subid and price will be unique. We will need to search for the barcode modifier before each insert.
	//user does not set the subid-mod...
	//the barcode modifier will be P then a number 1 through N
	//user selects a check to update the barcode...
	
	$pos_inventory_event_id = getPostOrGetID('pos_inventory_event_id');
	
	
	$inventory_contents = getLocationInvetory($pos_inventory_event_id);
	for($i=0;$i<sizeof($inventory_contents);$i++)
	{
		//$inventory_contents[$i]['sale_price'] = round($inventory_contents[$i]['retail_price'],2);
	}
	$table_name = 'inventory_table';
	$array_table_def= array(	
					array(
							'th' => 'Row',
							'db_field' => 'row_number',
							'type' => 'innerHTML',
							
					),
					array(
							'th' => 'conten id',
							'db_field' => 'pos_inventory_event_content_id',
							'type' => 'hidden',							
											),
					array(
							'th' => 'Barcode',
							'db_field' => 'barcode',
							'type' => 'innerHTML',							
											),
					array(
							'th' => 'Sub Id',
							'db_field' => 'pos_product_sub_id',
							'type' => 'innerHTML',							
											),
					array(
							'th' => 'Price Level',
							'db_field' => 'price_level',
							'type' => 'innerHTML',							
											),
					array(
							'th' => 'Item',
							'db_field' => 'item',
							
							'type' => 'innerHTML',
							),
					array(
							'th' => 'Retail Price',
							'db_field' => 'retail_price',
							'round' => 2,
							'type' => 'innerHTML',
							
							),
					array(
							'th' => 'Sale Price',
							'db_field' => 'sale_price',
							'type' => 'input',
							'round' => 2,
							'element' => 'input',
							'element_type' => 'none',
							),
					array(
							'th' => 'Clearance<br>Final Sale<br> or As-Is',
							'db_field' => 'clearance',
							'type' => 'checkbox',
							'element' => 'input',
							'element_type' => 'checkbox',
							'td_tags' => array(	'className' => '"test"',
										'style.backgroundColor' => '"#fff";',
										'style.textAlign' => '"center";'
											),
							'properties' => array(	
											'disabled' => 'false',
											//'checked' => true	no default value ;-)					
								)),
					array(
							'th' => 'Create New Price Modifier',
							'db_field' => 'new_modifier',
							'type' => 'checkbox',
							'default_value' => 1,
							'element' => 'input',
							'element_type' => 'checkbox',
							'td_tags' => array(	'className' => '"test"',
										'style.backgroundColor' => '"#fff";',
										'style.textAlign' => '"center";',
    									'style.verticalAlign' => '"middle";'
											)),
					
					);
	$html =  '<script src="'.$inventory_javasript.'"></script>'.newline();
	
	$html .= '<p>Here you set sale pricing. This will allow you to create many new barcodes with sale and clearence pricing per product sub id. To set or update sale pricing set the sale price and clearance flag. To create a new pricing check the last checkbox, create new price modifier. This will create a new barcode. </p>';
	$html .= '<table><tr>';
	$html .= '<td>Set All To Same Price<input type="text" name="sale_price" id="sale_price" value="" onkeyup="checkInput2(this,\'0123456789.\')"/></td>';
	$html .= '<td><input class = "button" type="button" style="width:200px;" name="setDiscount" value="Set All To Sale Price" onclick="setSalePrice();"/></td>';
	$html .= '<td>Set All To Clearance<input class = "button" type="checkbox"  id = "setAll" name="setAll"  onclick="setAllClearence();"/></td>';
	$html .= '<td>Set All To Create New Price Modifier<input class = "button" type="checkbox"  id = "setAllUpdate" name="setAll"  onclick="setAllUpdate();"/></td>';
	$html .= '</tr></table>';
	//$html .= createStaticArrayHTMLTablev2($array_table_def, $inventory_contents);
	
	$form_id = 'inventory_sale';
	$form_handler = 'inventory.php';
	$html .=  '<form id = "' . $form_id . '" action="'.$form_handler.'" method="post" onsubmit="return validateInventorySalePricing()">';
	$html .=  '<script> var form_id="'.$form_id.'"</script>'.newline();
	$html .=  '<script> var pos_location_id="'.getinventorylocation($pos_inventory_event_id).'"</script>'.newline();
	$html .= createHiddenInput('pos_inventory_event_id', $pos_inventory_event_id);
	$html .= createHiddenInput('type', 'sale_submit');
	$html .= createDynamicTableReuseV3('inventory_table', $array_table_def, $inventory_contents);
	$html .= '<input class = "button" name = "submit" type="submit" value="Submit"/>';
	$html .= '<input class = "button" name = "cancel" type="button" onclick = "confirmCancel()" value="Cancel"/>';
	//$html .= '<input class = "button" name = "test button" type="button" value="test" onclick="validateInventorySalePricing()"/>';
	$html .=  '</form>';
	$html .= '<script>$("#sale_price").focus();</script>';
	
	
}
elseif($type == 'sale_submit')
{
	
	$pos_inventory_event_id = $_POST['pos_inventory_event_id'];
	$original_inventory_contents = getLocationInvetory($pos_inventory_event_id);
	$pos_location_id = getInventoryLocation($pos_inventory_event_id);

	$inventory_table_tdo = json_decode(stripslashes($_POST['inventory_table_tdo']) , true);
	$dbc = startTransaction();
		
	
	//now create the new event
		$inventory_date = getDateTime();
		$inventory_event['inventory_date'] = $inventory_date;
		$inventory_event['pos_user_id'] = $_SESSION['pos_user_id'];
		$inventory_event['pos_location_id'] = $pos_location_id;
		$pos_inventory_event_id = simpleTransactionInsertSQLReturnID($dbc,'pos_inventory_event', $inventory_event);
	
	
	for($i = 0; $i<sizeof($inventory_table_tdo);$i++)
	{	
		if($inventory_table_tdo[$i]['sale_price']['data'] > 0)
		{
			// check for existing sale price
			
			$pos_product_sub_id = scrubInput($inventory_table_tdo[$i]['pos_product_sub_id']['data']);
			$sale_data = getTransactionSQL($dbc,"SELECT sale_barcode FROM pos_product_sub_sale_price WHERE pos_product_sub_id = '$pos_product_sub_id'");	
			$price = scrubInput($inventory_table_tdo[$i]['sale_price']['data']);
			$clearance = scrubInput($inventory_table_tdo[$i]['clearance']['data']);
			$sale_barcode = scrubInput($inventory_table_tdo[$i]['barcode']['data']);
			$title = '';
			
			if(sizeof($sale_data)>0)
			{	
				//what if the sale price and the clearence are already excatly the same as another level? do nothing...
				$same_sale_data = getTransactionSQL($dbc,"SELECT sale_barcode FROM pos_product_sub_sale_price WHERE pos_product_sub_id = '$pos_product_sub_id' AND price = '$price' AND clearance='$clearance'");	
				if(sizeof($same_sale_data)>0)
				{	
					//we already have something here...use that instead of creating a new one
					$price_level = getTransactionSingleValueSQL($dbc,"SELECT price_level FROM pos_product_sub_sale_price WHERE pos_product_sub_id = '$pos_product_sub_id' AND price = '$price' AND clearance='$clearance'");
					$sale_barcode = getTransactionSingleValueSQL($dbc,"SELECT sale_barcode FROM pos_product_sub_sale_price WHERE pos_product_sub_id = '$pos_product_sub_id' AND price = '$price' AND clearance='$clearance'");
					
				}
				else
				{
					if($inventory_table_tdo[$i]['new_modifier']['data'] == 1)
					{

						//this is a new insert....
					
					
						$price_level = getTransactionSingleValueSQL($dbc,"SELECT max(price_level) FROM pos_product_sub_sale_price WHERE pos_product_sub_id = $pos_product_sub_id") + 1;
						$sale_barcode = $pos_product_sub_id . 'P' .$price_level;
					
						runTransactionSQL($dbc,"INSERT INTO pos_product_sub_sale_price (
						pos_product_sub_id, 
						price_level, 
						price,
						clearance,
						sale_barcode,
						title
						) 
						VALUES (
						'$pos_product_sub_id',
						'$price_level',
						'$price',
						'$clearance',
						'$sale_barcode',
						'$title'
				
				
						)");
				
					
			
					}
					else
					{
						//we are updating this one...
						//list($sub_id, $price_level) = explode('P', $sale_barcode);
						//or
						//$price_level = getTransactionSingleValueSQL($dbc,"SELECT price_level FROM pos_product_sub_sale_price WHERE price = $sale_barcode");
						$price_level = scrubInput($inventory_table_tdo[$i]['price_level']['data']);

						runTransactionSQL($dbc,"UPDATE pos_product_sub_sale_price SET price = '$price', clearance = '$clearance' WHERE pos_product_sub_id = $pos_product_sub_id AND price_level = $price_level");
					}
				}
			}
			else
			{
				//nothing set so insert
				$sale_barcode = $pos_product_sub_id .'P1';
				$price_level = 1;

					
				
				runTransactionSQL($dbc,"INSERT INTO pos_product_sub_sale_price (
					pos_product_sub_id, 
					price_level, 
					price,
					clearance,
					sale_barcode,
					title
					) 
					VALUES (
					'$pos_product_sub_id',
					'$price_level',
					'$price',
					'$clearance',
					'$sale_barcode',
					'$title'
				
				
					)");
				
				

			
			}
			
			//now that we created the sale product we need to insert new  invetory contents....
			//stick in the new price level and sale_barcode
			//$pos_inventory_event_content_id = $inventory_table_tdo[$i]['pos_inventory_event_content_id']['data'];
			//runTransactionSQL($dbc,"UPDATE pos_inventory_event_contents SET price_level = '$price_level', barcode = '$sale_barcode' WHERE pos_inventory_event_content_id = $pos_inventory_event_content_id ");
			
			//put in a new content...

			$insert_array = 
				array( 	
					'pos_inventory_event_id' => $pos_inventory_event_id,
					'pos_product_sub_id' => $original_inventory_contents[$i]['pos_product_sub_id'],
					'quantity' =>$original_inventory_contents[$i]['quantity'],
					'inventory_type' => $original_inventory_contents[$i]['inventory_type'],
				
					'action' => 'PHYSICAL_COUNT',
					'comments' => $original_inventory_contents[$i]['comments'],
					'value' => $original_inventory_contents[$i]['value'],
					'price_level' => $price_level,
					'barcode' => $sale_barcode
											);
			$id  = simpleTransactionInsertSQLReturnID($dbc,'pos_inventory_event_contents', $insert_array);
		}	
		
		
		
		
			
			
			
			
			
		}
	simpleCommitTransaction($dbc);
	header('Location: inventory.php?type=view&pos_inventory_event_id='.$pos_inventory_event_id);
	exit();
}
else
{
	$html = 'Missing Type';
}

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);	
	
	


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

function createBulkProductInventoryLoaderTableDef($table_object_name)
{
	$table_object_name = $table_object_name . '_object';

	
	$enum_select_values_ids = getEnumValues('pos_inventory_event_contents', 'inventory_type');
	

	
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
					

				

					array(
					'db_field' => 'barcode',
					'caption' => 'Barcode',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"',
											
											'size' => '"30"')
						),
					/*when the user selects a po we need to load data to the other cells...
					when a user selects an invoice we need to load that data*/

				array(
					'db_field' => 'pos_product_sub_id',
					'caption' => 'Sub Id',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"',
											
											'size' => '"30"')
						),
				array(
					'db_field' => 'price_level',
					'caption' => 'Sub Id <br>Price Modifier',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"',
											
											'size' => '"30"')),
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
				
			
					/*array('caption' => 'Cost',
						'db_field' => 'cost',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'round' => 2,
						'element_type' => 'text',
						'validate' => array('acceptable_values' => array('number')),

						'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"','size' => '"10"','onclick' => 'function(){setCurrentRow(this);}',
												
'onkeypress' => 'function(e){return noEnter(e);}'								)),*/
					array('caption' => 'Retail Price',
						'db_field' => 'retail_price',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'round' => 2,
						'element_type' => 'text',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('number')),

						'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"','size' => '"10"','onclick' => 'function(){setCurrentRow(this);}',
												
'onkeypress' => 'function(e){return noEnter(e);}',
												'onchange' => 'needToConfirm=true;'												)),						
					
					array('caption' => 'Sale Price',
						'db_field' => 'sale_price',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'round' => 2,
						'element_type' => 'text',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('number')),

						'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"','size' => '"10"','onclick' => 'function(){setCurrentRow(this);}',
												
'onkeypress' => 'function(e){return noEnter(e);}',
												'onchange' => 'needToConfirm=true;'												)),						
					array('caption' => 'Inventory<br>Quantity',
						'db_field' => 'quantity',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'total' => 0,
						'round' => 0,
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('number')),

						'element_type' => 'text',
						'properties' => array(	'size' => '"10"','onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
												'onblur' => 'function(){}',
												'onkeyup' => 'function(){}',
												'onmouseup' => 'function(){}',
												'onkeypress' => 'function(e){return noEnter(e);}',
												'onchange' => 'needToConfirm=true;'												)),	
					/*array('db_field' => 'inventory_type',
						'caption' => 'Inventory<br>Type',
						'type' => 'select',
						'select_names' => $enum_select_values_ids,
						'select_values' => $enum_select_values_ids,
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1),

						'properties' => array(	'style.width' => '"7em"',
												'className' => '"nothing"',
												
												'value' => '"Available"',
												'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
												'onblur' => 'function(){}',
												'onkeyup' => 'function(){}',
												'onmouseup' => 'function(){}',
												'onchange' => 'needToConfirm=true;',
'onkeypress' => 'function(e){return noEnter(e);}'						)),
					
					array('caption' => 'Inventory<br> Value',
						'db_field' => 'value',
						'type' => 'input',
						'element' => 'input',
						'valid_input' => '-0123456789.',
						'round' => 2,
						'element_type' => 'text',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('number')),

						'properties' => array(	'size' => '"10"','onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
												'onblur' => 'function(){}',
												'onkeyup' => 'function(){}',
												'onmouseup' => 'function(){}',
												'onkeypress' => 'function(e){return noEnter(e);}',
												'onchange' => 'needToConfirm=true;'
												)),		*/						
					array('caption' => 'Comments',
					'db_field' => 'comments',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"',
												'className' => '"nothing"',
												'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
												'onblur' => 'function(){}',
												'onkeyup' => 'function(){}',
												'onmouseup' => 'function(){}',
												'onkeypress' => 'function(e){return noEnter(e);}',
												'onchange' => 'needToConfirm=true;')),
				
					
				);			
						
		
		return $columns;
	
	
	
}
function printLocationProductLabelsForm($pos_location_id, $data, $filename)
{
		//pass in row number, subid, subidname, quantity
		$array_table_def= array(	
			
					/*array(
							'th' =>  'Print',
							'db_field' => 'row_checkbox',
							'th_width' => "15",
							'type' => 'row_checkbox',
							'value' =>1
							),*/
					array(
							'th' => 'Row',
							'db_field' => 'row_number',
							'type' => 'row_number'),
					array(
							'th' => 'Barcode',
							'db_field' => 'barcode',
							'type' => 'td_hidden_input'),
					array(
							'th' => 'Item',
							'db_field' => 'item',
							'type' => 'td'),

					array(	'th' => 'Quantity',
							'db_field' => 'quantity',
							'type' => 'input',
							'round' => 0,
							'tags' => ' style="background-color:yellow" ')
					);
		$html = '<p>Select the Labels Needed For Location ID '.$pos_location_id . ' Named ' . getLocationName($pos_location_id).'</p>';
		$form_handler = 'print_location_product_labels.form.handler.php';
		$html .= '<form action="' . $form_handler.'" method="post">';
		
		$html .= 'Starting column: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="column_offset" style = "width:20px;" NAME="column_offset"/>'.newline();
		$html .= 'Starting row: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="row_offset" style = "width:20px;" NAME="row_offset"/>'.newline();
		$html .= createStaticArrayHTMLTable($array_table_def, $data);
		$html .= createHiddenInput('filename', $filename);
		$html .= createHiddenInput('pos_location_id', $pos_location_id);
		$html .= '<input class = "button" style="width:150px" type="submit" name="select" value="Open Label File"/>';
		$html .= '</form>';
		
		return $html;
}

function getLocationInventoryList($pos_location_id)
{
//create a table that displays this data....
	$location_name = getLocationName($pos_location_id);
	$html = '<h3>Inventory For ' . $location_name .'</h3>';
	$last_physical_inventory_date = getMostRecentPhysicalCountInventoryDateForLocation($pos_location_id, getDateTime());
	if($last_physical_inventory_date == '')
	{
		$html .= '<p>No Inventory has been recorded for this location</p>';
	}
	else
	{
		$pos_inventory_event_id = getMostRecentInventoryEventForLocation($pos_location_id,$last_physical_inventory_date);
		$data = getLocationInvetory($pos_inventory_event_id);
	
	
		$html .= '<p>Last Inventory Date: ' . $last_physical_inventory_date . '</p>';
		$array_table_def= array(	
			
						array(
								'th' => '',//createCheckbox('TMP', ' disabled="disabled" '),
								'db_field' => 'row_checkbox',
								'th_width' => "15",
								'type' => 'row_checkbox',
								),
						array(
								'th' => 'Row',
								'db_field' => 'row_number',
								'type' => 'row_number'),
							/*array(
								'th' => 'ID',
								'db_field' => 'pos_inventory_event_content_id',
								'type' => 'td',
								
								),*/
						array(
								'th' => 'Barcode',
								'db_field' => 'barcode',
								'type' => 'td',
								
								),
						array(
								'th' => 'Product ID',
								'db_field' => 'pos_product_id',
								'type' => 'link',
								'get_url_link' => POS_ENGINE_URL . '/products/ViewProduct/view_product.php',
								'get_id_link' => 'pos_product_id'
								),
						array(
								'th' => 'Sub ID',
								'db_field' => 'pos_product_sub_id',
								'type' => 'link',
								'get_url_link' => POS_ENGINE_URL . '/products/ProductSubId/view_product_sub_id.php',
								'get_id_link' => 'pos_product_sub_id',
								'get_id_data' => 'pos_product_sub_id'
								),
						array(
								'th' => 'Price Level',
								'db_field' => 'price_level',
								'type' => 'td',
								
								),
						
					
						array(
								'th' => 'Item',
								'db_field' => 'item',
								'type' => 'td'),
						/*array(
								'th' => 'Subid Name <br>(Barcode)',
								'db_field' => 'product_subid_name',
								'type' => 'td'),
						array(
								'th' => 'Title',
								'db_field' => 'title',
								'type' => 'td'),*/
						/*array(	'th' => 'Type',
								'db_field' => 'inventory_type',
								'type' => 'input',
								'tags' => ' style="background-color:yellow" '),*/

						array(	'th' => 'Quantity',
								'db_field' => 'quantity',
								'type' => 'input',
								'round' => 2,
								'total' => 2,
								'tags' => ' style="background-color:yellow" '),
						array(	'th' => 'Retail',
								'db_field' => 'retail_price',
								'type' => 'input',
								'round' => 2,
								'tags' => ' style="background-color:yellow" '),
						array(	'th' => 'Sale price',
								'db_field' => 'sale_price',
								'type' => 'input',
								'round' => 2,
								'tags' => ' style="background-color:yellow" '),
						array(	'th' => 'Clearance',
								'db_field' => 'clearance',
								'type' => 'checkbox',
								),
						/*array(	'th' => 'Cost',
								'db_field' => 'cost',
								'type' => 'input',
								'round' => 2,
								'tags' => ' style="background-color:yellow" '),
						array(	'th' => 'Cost<br>Extension',
								'db_field' => 'cost_extension',
								'type' => 'input',
								'round' => 2,
								'total' => 2,
								'tags' => ' style="background-color:yellow" '),
						array(	'th' => 'Current<br>Value',
								'db_field' => 'value',
								'type' => 'input',
								'round' => 2,
								'tags' => ' style="background-color:yellow" '),
						array(	'th' => 'Inventory<br>Extension',
								'db_field' => 'extension',
								'type' => 'input',
								'round' => 2,
								'total' => 2,
								'tags' => ' style="background-color:yellow" ')*/
						);
		$html .= createStaticViewDynamicTableV2('inventory_table', $array_table_def, $data, 'class="static_contents_table" ');
		$html .= createHiddenInput('pos_location_id', $pos_location_id);
		$html .= '<p>';
	

		$html .= '<input class = "button" style="width:150px" type="submit" name="print_labels" value="Print Labels" onclick="window.location = \'inventory.php?type=print&pos_location_id='.$pos_location_id.'\'"/>';
		$html .= '<input class = "button" style="width:150px" type="submit" name="print_labels" value="Sale/Clerance Pricing" onclick="window.location = \'inventory.php?type=sale&pos_inventory_event_id='.$pos_inventory_event_id.'\'"/>';
	}
	
	$html .= '<input class = "button" style="width:200px" type="submit" name="Edit" value="Edit Inventory" onclick="window.location = \'inventory.php?type=edit&pos_location_id='.$pos_location_id.'\'"/>';

	$html .= '</p>';
	return $html;
}
function createNewInventoryEvent($dbc, $pos_location_id)
{
	
		
		$last_physical_inventory_date = getMostRecentPhysicalCountInventoryDateForLocation($pos_location_id, getDateTime());
		if($last_physical_inventory_date != '')
		{
		$pos_inventory_event_id = getMostRecentInventoryEventForLocation($pos_location_id,$last_physical_inventory_date);
		$inventory_contents = getLocationInvetory($pos_inventory_event_id);
		}
		else
		{
			$inventory_contents = array();
		}
		
		
		//now create the new event
		$inventory_date = getDateTime();
		$inventory_event['inventory_date'] = $inventory_date;
		$inventory_event['pos_user_id'] = $_SESSION['pos_user_id'];
		$inventory_event['pos_location_id'] = $pos_location_id;
		$pos_inventory_event_id = simpleTransactionInsertSQLReturnID($dbc,'pos_inventory_event', $inventory_event);
	
		//now copy over the last set of contents
	
		for($c=0;$c<sizeof($inventory_contents);$c++)
		{
			$insert_array = 
				array( 	
			'pos_inventory_event_id' => $pos_inventory_event_id,
			'pos_product_sub_id' => $inventory_contents[$c]['pos_product_sub_id'],
					'quantity' =>$inventory_contents[$c]['quantity'],
					'inventory_type' => $inventory_contents[$c]['inventory_type'],
					'barcode' => $inventory_contents[$c]['barcode'],
			'price_level' => $inventory_contents[$c]['price_level'],
				
					'action' => 'PHYSICAL_COUNT',
					'comments' => $inventory_contents[$c]['comments'],
					'value' => $inventory_contents[$c]['value']
											);
			$id  = simpleTransactionInsertSQLReturnID($dbc,'pos_inventory_event_contents', $insert_array);
		}	
		return $pos_inventory_event_id;
}
?>