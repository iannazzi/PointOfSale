<?PHP
/*
	*upload_manufacturer_upcs.form.handler.php
	*CRAIG IANNAZZI 2-14-12
	
*/
$binder_name = 'Manufacturer UPC\'s';
$access_type = 'WRITE';
ini_set('upload_max_filesize', '200M');
ini_set('post_max_size', '200M');
ini_set('max_input_time', 3000);
ini_set('max_execution_time', 3000);
ini_set('memory_limit', '512M');
require_once ('../manufacturer_functions.php');
$page_title = 'Upload UPC\'s';
$pos_manufacturer_id = getPostOrGetID('pos_manufacturer_id');

if (checkForValidIdinPOS($pos_manufacturer_id, 'pos_manufacturers', 'pos_manufacturer_id'))
{
	if (isset($_POST['upload_file'])) 
	{
		$post_name_for_file = 'upc_code_file';
		if (isset($_FILES[$post_name_for_file]['size']) && $_FILES[$post_name_for_file]['size'] > 0)
		{
			$file_array = fileUploadHandler($post_name_for_file, UPLOAD_FILE_PATH .'/upc_file_uploads');
			$file_path_name = $file_array['path'];
			$html = createCSVProcessForm($file_path_name,$pos_manufacturer_id);
			/*$file_data['file_name'] = $file_array['name'];
			$file_data['file_type'] = $file_array['type'];
			$file_data['file_size'] = $file_array['size'];*/
		}
	}
	//at this point I want to make a table and re-display....then process part two....
	elseif(isset($_POST['reload']))
	{
		$file_path_name = $_POST['file_path_name'];
		$html = createCSVProcessForm($file_path_name,$pos_manufacturer_id);	
	}		
	elseif (isset($_POST['process']))
	{
		//to process we need to know: Style number, Color Code, etc....
		$required_columns = getRequiredUPCDataColumns();
		$found = array();
		$errors = array();
		for($i=0;$i<sizeof($required_columns);$i++)
		{
			$found[$i] = false;
			for($j=0;$j<$_POST['number_of_columns'];$j++)
			{
				if($_POST['definition_c' .$j] == $required_columns[$i])
				{
					$found[$i] = true;
				}
			}
		}
		for($i=0;$i<sizeof($required_columns);$i++)
		{
			if(!$found[$i])
			{
				$errors[] = '<p>missing required row: ' . $required_columns[$i] .'</p>';
			}
		}
		
		if (sizeof($errors) == 0)
		{
			$delimiter = (isset($_POST['delimiter'])) ? $_POST['delimiter'] : ',';
			$header_rows = (isset($_POST['header_lines'])) ? $_POST['header_lines'] : 0;
			$file_path_name = $_POST['file_path_name'];
			
			$column_info_array = array();
			for($j=0;$j<$_POST['number_of_columns'];$j++)
			{
				$column_info_array[(isset($_POST['definition_c' .$j])) ? $_POST['definition_c' .$j] : 'Ignore'] = $j;
			}
			$html = '<p><a href = "upload_manufacturer_upc_codes.php?pos_manufacturer_id='.$pos_manufacturer_id . '">Upload Another File for '. getManufacturerName($pos_manufacturer_id) . '</a></p>';
			$html .= importManufacturerUPCFile($file_path_name, $delimiter, $header_rows, $column_info_array,$pos_manufacturer_id);
		}
		else
		{
			$html ='';
			for($i=0;$i<sizeof($errors);$i++)
			{
				$html .= $errors[$i];
			}
			$file_path_name = $_POST['file_path_name'];
			$html .= createCSVProcessForm($file_path_name,$pos_manufacturer_id);
		}
		
	}
	elseif (isset($_POST['cancel']))
	{
		header('Location: ' . POS_ENGINE_URL . '/manufacturers/ListManufacturers/list_manufacturers.php');	
	}
	else
	{
	}
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
else //no valid mfg ID
{
	//no valid manufacturer id
	//Header
	include (HEADER_FILE);
	echo 'error - no valid mfg ID';
	include (FOOTER_FILE);
}
function createCSVProcessForm($file_path_name, $pos_manufacturer_id)
{
	$html = '<form  action="upload_manufacturer_upc_codes.form.handler.php" method="post">';
	//$html = '<form enctype="multipart/form-data" action="upload_manufacturer_upc_codes.form.handler.php" method="post">';
	$delimiter = (isset($_POST['delimiter'])) ? $_POST['delimiter'] : ',';
	$header_lines = (isset($_POST['header_lines'])) ? $_POST['header_lines'] : 0;
	$number_lines = (isset($_POST['number_lines'])) ? $_POST['number_lines'] : 10;
	//$html .= '<p>Delimiter: ' . createCSVDelimeterSelect($delimiter) .newline();
	$html .= '<p>Delimiter:<INPUT class = "lined_input" name="delimiter" id ="delimiter" value = "'.$delimiter.'" size="1" maxlength="1" />'.newline();
	$html .= 'Number Of Header Lines:<INPUT class = "lined_input" name="header_lines" id ="header_lines" value = "'.$header_lines.'" size="3" onkeyup="checkInput(this,\'.0123456789\')" />'.newline();
	$html .= 'Number Of Lines:<INPUT class = "lined_input" name="number_lines" id ="number_lines" value = "'.$number_lines.'" size="3" onkeyup="checkInput(this,\'0123456789\')" />'.newline();
	$html .= '<input class ="button" type="submit" name="reload" value="Reload" />' .newline();

	$html .='</p>';
	$html .='<p> 10 Line sample of csv </p>';
	
	$data = getCsvFileInArray($file_path_name, $delimiter,$header_lines);
	$html .= '<table class="linedTable">';
	$html .='<thead>';
	for($i=0;$i<$header_lines;$i++)
	{
		$html .= '<tr>';
		for($j=0;$j<sizeof($data[$i]);$j++)
		{
			$html .= '<th class="highlight">' .fixOddCharacters($data[$i][$j]) .'</th>';
		}
		$html .= '</tr>';
	}
	$html .='</thead>';
	$html .= '<tbody>';
	$html .= '<tr>';
	for($j=0;$j<sizeof($data[0]);$j++)
	{
		$selected_value = (isset($_POST['definition_c' .$j])) ? $_POST['definition_c' .$j] : 'Ignore';
		$html .= '<td>' .createDataDefinitionSelect($j, $selected_value) .'</td>';
	}
	$html .= '</tr>';
	$number_lines = (sizeof($data)<$number_lines) ? sizeof($data) : $number_lines;
	for($i=$header_lines;$i<$number_lines;$i++)
	{
		$html .= '<tr>';
		for($j=0;$j<sizeof($data[$i]);$j++)
		{
			$html .= '<td>' .fixOddCharacters($data[$i][$j]) .'</td>';
		}
		$html .= '</tr>';
	}
	$html .= '</tbody></table>';

	$html .= createHiddenInput('pos_manufacturer_id', $pos_manufacturer_id);
	$html .= '<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />';
	$html .= createHiddenInput('file_path_name', $file_path_name);
	$html .= createHiddenInput('number_of_columns', sizeof($data[0]));
	$html .= '<p><input class ="button" type="submit" name="process" value="Process" />' .newline();
	$html .= '<input class ="button" type="submit" name="cancel" value="Cancel" /></p>' .newline();
	$html .= '</form>';
	$html .='<p> UPC, Style Number, Color Code, and Size are required. If the manufacturer only provides a color description, choose that column as the color code, the color description will automatically duplicate the color code. For special size columns, like cup, the code will combine the size column, then the special column, to arrive at the full size string. </p>';
	$html .='<p> Where multiple Size options are available, choose the size that matches the <a href = "../ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$pos_manufacturer_id .'" target="_blank" >size chart</a> for this manufacturer </p>';
	return $html;
}
function getUPCDataColumns()
{
	return array('UPC', 'Color Code', 'Size', 'Style Number', 'Ignore',  'Color Description',  'Cup', 'Inseam', 'Shoe Width',  'Description', 'Collection', 'MSRP', 'Cost');
}
function getRequiredUPCDataColumns()
{
	return array('UPC', 'Color Code', 'Size', 'Style Number');
}
function createDataDefinitionSelect($column, $selected_value)
{	

	$data = getUPCDataColumns();
	$name = 'definition_c' .$column;
	$html = '<select style = "width:100%" id = "'.$name.'" name="'.$name.'" ';

	$html .= '>';
	//Add an option for not selected
	for($i = 0;$i < sizeof($data); $i++)
	{
		$html .= '<option style="white-space: wrap;" value="' . $data[$i] . '"';
		
		if ( ($data[$i] == $selected_value) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $data[$i] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function importManufacturerUPCFile($file_path_name, $delimeter, $header_rows, $column_info_array,$pos_manufacturer_id)
{		
		$html = '';
		$data = getCsvFileInArray($file_path_name, $delimeter);


		for($i=$header_rows;$i<sizeof($data);$i++)
		{
			
			 $style_number = scrubTextUPCInput($data[$i][$column_info_array['Style Number']]);
			 $color_code = strtoupper(scrubTextUPCInput($data[$i][$column_info_array['Color Code']]));
			 $upc = scrubNumericUPCInput($data[$i][$column_info_array['UPC']]);
			$size = scrubNumericUPCInput($data[$i][$column_info_array['Size']]);
			 
			 if (isset($column_info_array['Cup']))
			 {
			 	$size = $size . scrubNumericUPCInput($data[$i][$column_info_array['Cup']]);
			 }
			 if (isset($column_info_array['Show Width']))
			 {
			 	$size = $size . scrubNumericUPCInput($data[$i][$column_info_array['Show Width']]);
			 }
			 if (isset($column_info_array['Inseam']))
			 {
			 	$size = $size . scrubNumericUPCInput($data[$i][$column_info_array['Inseam']]);
			 }
			 $collection = '';
			 if (isset($column_info_array['Collection']))
			 {
			 	$collection = ucwords(strtolower(scrubTextUPCInput($data[$i][$column_info_array['Collection']])));
			 }
			  $description = '';
			if (isset($column_info_array['Description']))
			 {
			 	$description = ucwords(strtolower(scrubTextUPCInput($data[$i][$column_info_array['Description']])));
			 }
			
			 $style_description = trim($collection . ' ' . $description);
			 $color_description = (isset($column_info_array['Color Description'])) ?  ucwords(strtolower(scrubTextUPCInput(($data[$i][$column_info_array['Color Description']])))) :ucwords(strtolower( $color_code));
			 $msrp =  (isset($column_info_array['MSRP'])) ?  ucwords(strtolower(scrubNumericUPCInput(($data[$i][$column_info_array['MSRP']])))) : '';
			 
			 $cost = (isset($column_info_array['Cost'])) ?  ucwords(strtolower(scrubNumericUPCInput(($data[$i][$column_info_array['Cost']])))) : '';

			 
			 //If the style number + brand combo exists then we need to update, other wise we need to insert
			$upc_update_insert_sql = "INSERT INTO pos_manufacturer_upc (upc_code, pos_manufacturer_id, date_added, style_number, style_description, color_code, color_description, size, msrp, cost, comments) 
			VALUES ('$upc', '$pos_manufacturer_id', NOW(), '$style_number', '$style_description', '$color_code', '$color_description', '$size', '$msrp', '$cost', '') 
			ON DUPLICATE KEY UPDATE
			date_added = NOW(), 
			style_number = '$style_number', 
			style_description = '$style_description', 
			color_code = '$color_code', 
			color_description = '$color_description', 
			size = '$size', msrp = '$msrp', 
			cost = '$cost', 
			comments = ''";
			$upc_update_insert_r = runSQL($upc_update_insert_sql);
			if ($upc_update_insert_r) 	
			{	
					$html .= '<p>UPC # ' . $upc . ' has been inserted/updated</p>';
			}
			else
			{
					$html .= '<p class = "error" >Style # ' . $upc .  ' has been not been updated</p>';
			}
		}
		return $html;
}

?>
