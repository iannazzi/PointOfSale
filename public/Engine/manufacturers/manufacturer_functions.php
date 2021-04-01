<?PHP

$page_title = 'View Manufacturers';
$page_level = 5;

require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);



function listBrands($pos_manufacturer_id)
{
	//here is the query that the search and table arrays are built off of.
	$sql = "
	
	SELECT pos_manufacturer_brand_id, active, brand_name, brand_code, 
	(SELECT REPLACE(GROUP_CONCAT( REPLACE( sizes,'\\r\\n', ' ' ) ) , ',', '\\r\\n'	 )
	FROM `pos_manufacturer_brand_sizes`
	WHERE pos_manufacturer_brand_sizes.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id) 
	as concatsizes, comments FROM pos_manufacturer_brands WHERE pos_manufacturer_id = " . $pos_manufacturer_id['pos_manufacturer_id'];
	
	$table_columns = array(
			array(
				'th' => 'Edit',
				'mysql_field' => 'pos_manufacturer_brand_id',
				'get_url_link' => "../EditBrand/edit_brand.php",
				'url_caption' => 'Edit',
				'get_id_link' => 'pos_manufacturer_brand_id'),
			array(
				'th' => 'System Id <br> Auto Generated',
				'mysql_field' => 'pos_manufacturer_brand_id'),
			array(
				'th' => 'Active',
				'mysql_field' => 'active'),
			array(
				'th' => 'Brand Name',
				'mysql_field' => 'brand_name'),
			array(
				'th' => 'Brand Code',
				'mysql_field' => 'brand_code'),
			array(
				'th' => 'Size Chart',
				'mysql_field' => 'concatsizes'),
			array(
				'th' => 'Edit Sizes',
				'mysql_field' => 'pos_manufacturer_brand_id',
				'get_url_link' => "../EditBrandSizeChart/edit_brand_size_chart.php",
				'url_caption' => 'Edit Sizes',
				'get_id_link' => 'pos_manufacturer_brand_id'),
			array(
				'th' => 'Comments',
				'mysql_field' => 'comments'));
	//now make the table
	$html = createRecordsTable(getSQL($sql), $table_columns, 'linedTable');
	return $html;
}
function createBrandSizeChartForm($pos_manufacturer_brand_id, $table_def, $form_handler, $complete_location, $cancel_location)
{
	$html = confirmNavigation();
	//$html .= includeJavascriptLibrary();
	$form_id = 'form_id';
	$html .= '<form id ="'.$form_id.'" action="' . $form_handler.'" method="post" onsubmit="return validateBrandSizeChartForm()">';
	$html .= '<table class = "mysqlTable">' .newline();
	$html .= '<thead><tr>';
	for($i=0;$i<sizeof($table_def[0]);$i++)
	{

		if (isset($table_def[0][$i]['th']))
		{
			$html .=  $table_def[0][$i]['th'];
		}
		elseif(isset($table_def[0][$i]['caption']))
		{
			$html .= '<th>' . $table_def[0][$i]['caption'] .'</th>';
		}
		elseif (isset($table_def[0][$i]['db_field']))
		{
			$html .='<th>' . $table_def[0][$i]['db_field'] .'</th>';
		}
		else
		{
			$html .='<th></th>';
		}
	}
	$html .= '</tr></thead>' .newline();
	$tbody_name = 'tbody';
	$column_counter=0;
	$html .= '<tbody name="'.$tbody_name.'" id="'.$tbody_name.'">';
	for($row =0;$row<sizeof($table_def);$row++)
	{
		$html .= '<tr>';
		$column_counter=0;
		for($column=0;$column<sizeof($table_def[0]);$column++)
		{
			//ok one of these columns has an array
			if (isset($table_def[$row][$column]['value']) && is_array($table_def[$row][$column]['value']))
			{
				if (sizeof($table_def[$row][$column]['value']) ==0)
				{
					$size_rows = 1;
				}
				else
				{
					$size_rows = sizeof($table_def[$row][$column]['value']);
				}
				for($row2 =0;$row2<$size_rows;$row2++)
				{
					if (!isset($table_def[$row][$column]['value'][$row2]))
					{
						$table_def[$row][$column]['value'][$row2] = '';
					}
					$cell_name = $tbody_name.'r'.$row.'c'.$column_counter;
					$column_counter++;
					$table_def[$row][$column]['html'] = changeElementName($table_def[$row][$column]['html'],$cell_name);
					$html .= '<td>' . addValueToHTMLElement($table_def[$row][$column],$table_def[$row][$column]['value'][$row2]) . '</td>';
				}	
			}
			else
			{
				if (!isset($table_def[$row][$column]['value']))
				{
					if (isset($table_def[$row][$column]['default']))
					{
						$table_def[$row][$column]['value'] = $table_def[$row][$column]['default'];
					}
					else
					{
						$table_def[$row][$column]['value'] = '';
					}
				}
						
						
				//$html .= addColumnData($table_def[$row][$column], $table_def[$row][$column]['value']);
				$cell_name = $tbody_name.'r'.$row.'c'.$column_counter;
				$column_counter++;
				$table_def[$row][$column]['html'] = changeElementName($table_def[$row][$column]['html'],$cell_name);
				$html .= '<td>' . addValueToHTMLElement($table_def[$row][$column],$table_def[$row][$column]['value']) . '</td>';
				//name the cells to tbody_data_r0_c1 etc....
			}	
		}
		$html .= '</tr>'.newline();
	} 

	$html .= '</tbody></table>'.newline(); // Close the table.
	
	$html .= '<INPUT class = "button" type="button" style="width:90px;" value="Add Size Row" onclick="addSizeRow(\''.$tbody_name.'\')" />';
	$html .= '<INPUT class = "button" type="button" style="width:110px;" value="Add Size Column" onclick="addSizeColumn(\''.$tbody_name.'\')" />';
	$html .= '<INPUT class = "button" type="button" value="Move Row(s) Up" onclick="moveRowUp(\''.$tbody_name.'\')" />';
	$html .= '<INPUT class = "button" type="button" style="width:120px;" value="Move Row(s) Down" onclick="moveRowDown(\''.$tbody_name.'\')" />';
	$html .= '<INPUT class = "button" type="button" style="width:80px;" value="Delete Row(s)" onclick="deleteRow(\''.$tbody_name.'\')" />';
	$html .= '<INPUT class = "button" type="button" style="width:100px;" value="Delete Column" onclick="deleteSizeColumn(\''.$tbody_name.'\')" />';
	
	//Add the submit/canel buttons
	$html .= '<p><input class ="button" type="submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel" onclick="cancelForm()"/>';
	$html .= createHiddenInput('pos_manufacturer_brand_id', $pos_manufacturer_brand_id);
	$html .= createHiddenSerializedInput('table_def', $table_def);	
	$html .= createHiddenInput('complete_location', $complete_location);
	$html .= createHiddenInput('cancel_location', $cancel_location);
	$html .= createHiddenInput('tbody_name', $tbody_name);
	$html .= createHiddenInput('form_id', $form_id);
	$html .= createHiddenInput('number_of_rows', sizeof($table_def));
	$html .= createHiddenInput('number_of_columns', $column_counter);	
	//close the form
	$html .= '</form>' .newline();
	//drop the array out for form validation
	$json_table_def = json_encode($table_def);
	$html .= '<script>var json_table_def = ' . $json_table_def . ';</script>';
	$html .= '<script>var num_size_columns = ' . $size_rows . ';</script>';
	//$html .= '<script>document.getElementsByName("' . $table_def[0][1]['db_field'] .'")[0].focus();</script>';
	$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
	$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
	$html .= '<script>var tbody_name = "' . $tbody_name . '";</script>';
	$html .= '<script>var tbody_id = "' . $tbody_name . '";</script>';
	$html .= '<script>var form_id = "' . $form_id . '";</script>';
	return $html;
}
function addValueToHTMLElement($table_def, $value)
{
		if ($table_def['type'] == 'text')
		{
			return addValueToInput($table_def['html'], $value);
		}
		elseif ($table_def['type'] == 'select')
		{
			return addValueToSelect($table_def['html'], $value);
		}
		elseif($table_def['type'] =='checkbox')
		{
			return addValueToCheckBox($table_def['html'], $value);
		}
		elseif ($table_def['type'] =='textarea')
		{
			return addValueToTextArea($table_def['html'], $value);
		}
}
function getBrandSizeChartArray($pos_manufacturer_brand_id)
{
	$size_chart_q = "SELECT pos_manufacturer_brand_size_id, pos_category_id, pos_product_attribute_id, case_qty, cup, cup_required, inseam, width, sizes, active, 									  					            comments FROM pos_manufacturer_brand_sizes WHERE  pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'";

	$size_chart = getSQL($size_chart_q);
	//convert sizes into array
	if (sizeof($size_chart)==0)
	{
		//$size_chart[0]['sizes'] = array();
	}
	else
	{
		for($i=0;$i<sizeof($size_chart);$i++)
		{
			$size_chart[$i]['sizes'] = explode("\r\n", $size_chart[$i]['sizes']);
		}
	}
	return $size_chart;
}
function loadBrandSizeDataIntoTableDef($existing_sizes, $dynamic_table_col_def)
{
	//if 0 nothing to load....
	for ($row=0;$row<sizeof($existing_sizes);$row++)
	{
		$dynamic_table_col_def[$row] = $dynamic_table_col_def[0];
		for ($column=0;$column<sizeof($dynamic_table_col_def[0]);$column++)
		{
			if (isset($dynamic_table_col_def[0][$column]['db_field']))
			{

					$dynamic_table_col_def[$row][$column]['value'] = $existing_sizes[$row][$dynamic_table_col_def[0][$column]['db_field']];

				
			}		
		}
	}
	return $dynamic_table_col_def;
	
}
function updateBrandSizeChart($pos_manufacturer_brand_id, $brandSizeRow)
{
	//create the id
	$id['pos_manufacturer_brand_size_id'] = $brandSizeRow['pos_manufacturer_brand_size_id'];
	//strip off the size_id
	unset($brandSizeRow['pos_manufacturer_brand_size_id']);
	//add the brand_id
	//fix cup, inseam, width, etc..... 
	$brandSizeRow['pos_manufacturer_brand_id'] = $pos_manufacturer_brand_id;
	simpleUpdateSQL('pos_manufacturer_brand_sizes', $id, $brandSizeRow);
}
function insertBrandSizeRow($pos_manufacturer_brand_id, $brandSizeRow)
{
	//strip off the id
	unset($brandSizeRow['pos_manufacturer_brand_size_id']);
	//add the brand_id
	$brandSizeRow['pos_manufacturer_brand_id'] = $pos_manufacturer_brand_id;
	simpleInsertSQL('pos_manufacturer_brand_sizes', $brandSizeRow);
}
function createBrandChartSQLStatement($pos_manufacturer_brand_id, $tbody_data, $posted_serialized_table_def)
{
	$table_def = unserialize(stripslashes(htmlspecialchars_decode($posted_serialized_table_def)));
	//need to turn the sizes into a comma separated array
	// to do this we look at the table definition, find where the 'dynamic section' is, then convert that part to commas.
	$rows = sizeof($tbody_data);
	$columns = sizeof($tbody_data[0]);
	
	$sql_insert_array = array();
	
	for ($row=0;$row<$rows;$row++)
	{
		$column_counter = 0;
		for ($column=0;$column<sizeof($table_def[0]);$column++)
		{
			//some data does not have a db filed
			if (isset($table_def[0][$column]['db_field']))
			{
				if (isset($table_def[0][$column]['value']) && is_array($table_def[0][$column]['value']))
				{
					$bln_size = false;
					for($column2=0;$column2<$columns-sizeof($table_def[0])+1;$column2++)
					{
						// this is where the 'dynamic' portion comes into play - for this case I need to turn the dynamic part into single string separated by \r\n
						//For the purchase orde contents, each section is to be converted to a size..
						if ($tbody_data[$row][$column_counter] !='')
						{
							$bln_size = true;
						}
						$size_array[$column2] = $tbody_data[$row][$column_counter];
						$column_counter++;
					}
					//if (bln_size)
					//{
						$sql_insert_array[$row][$table_def[0][$column]['db_field']] = implode($size_array, "\r\n");
					//}
					/*else
					{
						$sql_insert_array[$row][$table_def[0][$column]['db_field']] = 'NA';
					}*/
				}
				else
				{
					$sql_insert_array[$row][$table_def[0][$column]['db_field']] = $tbody_data[$row][$column_counter];
					$column_counter++;
				}
			}
			else
			{
				$column_counter++;
			}
		}
	}	
	for($row=0;$row<sizeof($sql_insert_array);$row++)
	{
		/*if($sql_insert_array[$row]['pos_manufacturer_brand_size_id'] != '')
		{
			updateBrandSizeChart($pos_manufacturer_brand_id, $sql_insert_array[$row]);
		}
		else
		{
			insertBrandSizeRow($pos_manufacturer_brand_id, $sql_insert_array[$row]);
		}*/
		insertBrandSizeRow($pos_manufacturer_brand_id, $sql_insert_array[$row]);
	}
}
function getTbodyData($tbody_name, $posted_serialized_table_def, $rows, $columns)
{
	$table_def = unserialize(stripslashes(htmlspecialchars_decode($posted_serialized_table_def)));
	// most of this BS is because the checkbox's don't post - so we need to define how big the table is, post that data, then go figure out which cell is a checkbox, then add a 0 or convert on to 1 for the checkboxes.... other than that there would be no complications, just find the tbodyrc data and shove it in an array
	// there are $rows x $columns posted 
	// need to know the 'type' of each column
	$type_array=array();
	$column_counter = 0;
	for ($column=0;$column<sizeof($table_def[0]);$column++)
	{
		if (isset($table_def[0][$column]['value']) && is_array($table_def[0][$column]['value']))
		{
			for($column2=0;$column2<$columns-sizeof($table_def[0])+1;$column2++)
			{
				$type_array[$column_counter] = $table_def[0][$column]['type'];
				$column_counter++;
			}
		}
		else
		{
			$type_array[$column_counter] = $table_def[0][$column]['type'];
			$column_counter++;
		}
	}	
	//checkboxes will not post if they are not checked. If they are checked the value will be 'on' 
	// To be sure we will need the table definitiion - so we will check this when comparing to the table definition
	$tbody_data = array();
	$row_counter = 0;
	foreach($_POST as $post_key => $post_value)
	{
		if ($post_key != 'tbody_name')
		{
			if (strpos($post_key, $tbody_name)!==false)
			{
			
				//strip out the body name
				$rc_name = substr($post_key, strpos($post_key, $tbody_name)+strlen($tbody_name), strlen($post_key));
				//strip out the number betwee r and c
				$r = substr($rc_name, strpos($rc_name, 'r')+strlen('r'), strpos($rc_name, 'c')-1);
				//strip out the number after c
				$c = substr($rc_name, strpos($rc_name, 'c')+strlen('c'), strlen($rc_name));
				$tbody_data[$r][$c] = $post_value;
			}
		}
	}
	for($row=0;$row<$rows;$row++)
	{
		for($column=0;$column<$columns;$column++)
		{
			if($type_array[$column] == 'checkbox')
			{
				if(isset($tbody_data[$row][$column]))
				{
					$tbody_data[$row][$column] = 1;
				}
				else
				{
					$tbody_data[$row][$column] = 0;
				}
			}
		}
		ksort($tbody_data[$row]);
	}
	return $tbody_data;
}
	
function createManufacturerAccountsRecordTable($pos_manufacturer_id)
{
		$table_columns = array(

		array(
			'th' => 'View<br>Account<br>Details',
			'mysql_field' => 'pos_account_id',
			'get_url_link' => POS_ENGINE_URL . "/accounting/Accounts/view_account.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_account_id'),
		
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_account_id',
			'sort' => 'pos_account_id'),
		array(
			'th' => 'Account Name',
			'mysql_field' => 'company',
			'sort' => 'company'),
		array(
			'th' => 'Account Number',
			'mysql_field' => 'account_number',
			'encrypted' => 1,
			'sort' => 'account_number')
		);
	$sql = "SELECT pos_account_id, company, account_number FROM pos_accounts
		LEFT JOIN pos_manufacturer_accounts USING (pos_account_id)
		WHERE pos_manufacturer_accounts.pos_manufacturer_id = $pos_manufacturer_id";
		
		
	$dbc = openPOSdb();
	//$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$sql);
	closeDB($dbc);
	$html = createRecordsTable($data, $table_columns);
	return $html;
		
}



?>
