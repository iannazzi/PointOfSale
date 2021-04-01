<?PHP
/*
	*upload_manufacturer_upcs.form.handler.php
	*CRAIG IANNAZZI 2-14-12
	
*/
$binder_name = 'Customers';
$access_type = 'WRITE';
ini_set('upload_max_filesize', '200M');
ini_set('post_max_size', '200M');
ini_set('max_input_time', 3000);
ini_set('max_execution_time', 3000);
ini_set('memory_limit', '512M');
require_once ('customer_functions.php');
$page_title = 'Upload customers';
$form_handler = 'upload_customers.form.handler.php';
	if (isset($_POST['upload_file'])) 
	{
		$post_name_for_file = 'customer_file';
		if (isset($_FILES[$post_name_for_file]['size']) && $_FILES[$post_name_for_file]['size'] > 0)
		{
			$file_array = fileUploadHandler($post_name_for_file, UPLOAD_FILE_PATH .'/customer_file_uploads');
			$file_path_name = $file_array['path'];
			$html = createCustomerCSVProcessForm($form_handler, $file_path_name);
			/*$file_data['file_name'] = $file_array['name'];
			$file_data['file_type'] = $file_array['type'];
			$file_data['file_size'] = $file_array['size'];*/
		}
	}
	//at this point I want to make a table and re-display....then process part two....
	elseif(isset($_POST['reload']))
	{
		$file_path_name = $_POST['file_path_name'];
		$html = createCustomerCSVProcessForm($form_handler, $file_path_name);	
	}		
	elseif (isset($_POST['process']))
	{
		//to process we need to know: Style number, Color Code, etc....
		$required_columns = getRequiredCustomerDataColumns();
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
				$column_info_array[$j] = (isset($_POST['definition_c' .$j])) ? $_POST['definition_c' .$j] : 'Ignore';
			}
			$html = '';
			$html .= importCustomerFile($file_path_name, $delimiter, $header_rows, $column_info_array);
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
		header('Location: ' . POS_ENGINE_URL . '/customers/list_customers.php');	
	}
	else
	{
	}
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);

function createCustomerCSVProcessForm($form_handler, $file_path_name)
{
	$html = '<form  action="'.$form_handler.'" method="post">';
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

	$html .= '<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />';
	$html .= createHiddenInput('file_path_name', $file_path_name);
	$html .= createHiddenInput('number_of_columns', sizeof($data[0]));
	$html .= '<p><input class ="button" type="submit" name="process" value="Process" />' .newline();
	$html .= '<input class ="button" type="submit" name="cancel" value="Cancel" /></p>' .newline();
	$html .= '</form>';
	return $html;
}
function getCustomerDataColumns()
{
	return array('First Name', 'Last Name', 'Comments', 'Invoice');
}
function getRequiredCustomerDataColumns()
{
	return array('First Name', 'Last Name', 'Comments');
}
function createDataDefinitionSelect($column, $selected_value)
{	

	$data = getCustomerDataColumns();
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
function importCustomerFile($file_path_name, $delimeter, $header_rows, $column_info_array)
{		
	$html = '';
	$data = getCsvFileInArray($file_path_name, $delimeter);
	$combined_array = array();
	$ca_counter = 0;
	for($i=$header_rows;$i<sizeof($data);$i++)
	{
		 $comments = array();
		 $first_name = '';
		 $last_name = '';
		 $invoice = '';
		 for($j=0;$j<sizeof($column_info_array);$j++)
		 {
			if ($column_info_array[$j] == 'First Name')
			{
				$first_name = scrubInput($data[$i][$j]);
			}
			else if ($column_info_array[$j] == 'Last Name')
			{
				$last_name = scrubInput($data[$i][$j]);
			}
			else if ($column_info_array[$j] == 'Comments')
			{
				if(isset($data[$i][$j]) && scrubInput($data[$i][$j]) != '')
				{
					$comments[] = scrubInput($data[$i][$j]);
				}
			}
			else if ($column_info_array[$j] == 'Invoice')
			{
				$invoice = scrubInput($data[$i][$j]);
			}
			else
			{
			}
		}
		$sql = "SELECT * FROM pos_customers WHERE first_name = '$first_name' AND last_name = '$last_name'";
		$existing_customer = getSQL($sql);
		if (sizeof($existing_customer) >0)
		{
			for($ec =0;$ec<sizeof($existing_customer);$ec++)
			{
				$pos_customer_id = $existing_customer[$ec]['pos_customer_id'];
				$new_comments = scrubInput($existing_customer[$ec]['comments']) . newline(). 'Invoice: '. $invoice . newline() . implode(',', $comments);
				$update = "UPDATE pos_customers SET  comments = '$new_comments' WHERE pos_customer_id = $pos_customer_id";
				$upc_update_insert_r = runSQL($update);
				$html .= '<p>Customer ' . $first_name . ' ' . $last_name . ' has been updated</p>';
				break;
			}
		}
		else
		{
			//$combined_array[$ca_counter]['first_name'] = $first_name;
			//$combined_array[$ca_counter]['last_name'] = $last_name;
			//$combined_array[$ca_counter]['comments'] = 
			$combined_comments = 'Invoice: '. $invoice . newline() . implode(',', $comments);
			$date_time = getdatetime();
			//or just insert:
			$insert = "INSERT INTO pos_customers (first_name, last_name, comments, date_added)
						VALUES ('$first_name', '$last_name', '$combined_comments', '$date_time')";
			$upc_update_insert_r = runSQL($insert);
			$html .= '<p>Customer ' . $first_name . ' ' . $last_name . ' has been inserted</p>';
			
		}
	}
	return $html;
}

?>
