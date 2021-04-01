<?
/*
	Config File Vs Settings database
	
	The config file needs access to the database
	It also tells us if we are live or test
	
	the pos_settings table allows us to place editable system settings
	
	problem is that when i copy databases over it will kill my 'test' system.....
	
	So we want values like....
	facebook
	company name
	
	But not
	is test
	
	each setting "name" can have a value, and each name can have different code to select the value...
	generally we will just want a name and text
	however in the case of selecting a checking account, we need an index also we need to decrypt account numbers... so the switch in the table def allows us to do that...
	
	
	
*/
$page_title = 'System Settings';
$binder_name = 'Settings';


$type = (isset($_GET['type']) )? $_GET['type'] : $_POST['type'];
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../system_functions.php');

$complete_location = 'list_settings.php';
$cancel_location = 'list_settings.php?message=Canceled';


if (isset($_POST['submit'])) 
{
	//preprint
	$dbc = startTransaction();
	//$table_def_array = deserializeTableDef($_POST['table_def']);
	//$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	//unset($insert['name']);
	//if it is new then insert, otherwise update.
	
	
	// need the same switch as below to set the value text for user readability...
	
	
	
	
	$name = $_POST['name'];
	$value = $_POST['value'];
	
	switch( $name)
	{
		case 'default_pos_return_checking_account';
			$value_text = getAccountName($value) . ': ' .  xxxxAccountNumber(getAccountNumber($value));
			break;
		default:
			$value_text = $value;
			break;
	}
	
	
	$sql = "UPDATE pos_settings SET value = '$value', value_text = '$value_text' WHERE name = '$name'";
	runTransactionSQL($dbc, $sql);
	$message = urlencode('Setting' . $name . " has been updated");
		
	simpleCommitTransaction($dbc);
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);
}
elseif (isset($_POST['cancel']))
{
	//handled through javascript
}
else
{
	if(strtoupper($type) == 'ADD')
	{
		//there should be no add
	}
	elseif (strtoupper($type) == 'EDIT')
	{
		$name = getPostOrGetValue('name');
		$header = '<p>EDIT Setting</p>';
		$page_title = 'Edit Setting';
		$data_table_def_no_data = createSettingTableDef($type, $name);	
		$db_table = 'pos_settings';
		$key_val_id['name'] = $name;
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
	}
	elseif (strtoupper($type) == 'VIEW')
	{
		$name = getPostOrGetValue('name');
		$edit_location = 'settings.php?name='.$name.'&type=edit';
		//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
		$db_table = 'pos_settings';
		$key_val_id['name']  = $name;
		$data_table_def = createSettingTableDef($type, $name);
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
	}
	else
	{
	}

	//build the html page
	if (strtoupper($type) == 'VIEW')
	{
		$html = printGetMessage('message');
		$html .= '<p>View Setting</p>';
		//$html .= confirmDelete($delete_location);
		$html .= createHTMLTableForMYSQLData($data_table_def);
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
		$html .= '<p>';
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Settings" onclick="window.location = \''.$complete_location.'\'" />';
		$html .= '</p>';
	}
	else
	{
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
	
		$html = $header;
		$form_handler = 'settings.php';
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
		$html .= '<script>document.getElementsByName("setting_name")[0].focus();</script>';
	}


	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}

function createSettingTableDef($type, $name)
{
	/*if ($pos_setting_id =='TBD')
	{
		//$unique_validate = array('unique_group' => array('pos_store_id', 'printer_name'), 'min_length' => 1);
	}
	else
	{
		$key_val_id['name'] = $name;
		//$unique_validate = array('unique_group' => array('pos_store_id', 'printer_name'), 'min_length' => 1, 'id' => $key_val_id);
	}*/
	switch( $name)
	{
		case 'default_pos_return_checking_account';
		$table_def= array( 
						
						array('db_field' =>  'name',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'value' => $name,
								'caption' => 'Setting Name',
								),	

								
						array('db_field' => 'value',
								'type' => 'select',
								'caption' => 'POS return Checking Account',
								'html' => createCheckingAccountSelect('value', 'false'),
								'validate' => 'none'),
						array('db_field' => 'description',
								'caption' => 'Description',
								'type' => 'input',
								'validate' => 'false')
						);	
		break;
		default:
			$table_def= array( 
						
						array('db_field' =>  'name',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'value' => $name,
								'caption' => 'Setting Name',
								),	
						array('db_field' => 'value',
								'caption' => 'Value',
								'type' => 'input',
								'validate' => 'false'),
						array('db_field' => 'description',
								'caption' => 'Description',
								'type' => 'input',
								'validate' => 'false')
						);	
			break;
	}
	return $table_def;

}
?>





?>