<?php
$page_level = 0;
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);




function loadSystemBinders()
{
	$sql = "SELECT * FROM pos_binders WHERE enabled = 1";
	return getSQL($sql);

}
function createUserSettingTableDef($type, $pos_user_id)
{

	$table_def = array(
						array( 'db_field' => 'pos_user_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_user_id,
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'caption' => 'Email',
								'validate' => 'none'),
						array('db_field' =>  'default_view_date_range_days',
								'type' => 'input',
								'caption' => 'Default Number of Days For Views',
								'validate' => 'number')
								
				);	

				
	return $table_def;
	
}
function createAdminUserTableDef($type, $pos_user_id)
{
	$key_val_id['pos_user_id'] = $pos_user_id;
	if ($type == 'New')
	{
		$pos_user_id = 'TBD';
		$unique_validate =  array('unique' => 'true', 'min_length' => 1);
	}
	else
	{
		$unique_validate = array('unique' => 'true', 'min_length' => 1, 'id' => $key_val_id);
	}
	$db_table = 'pos_users';

	$table_def = array(
						array( 'db_field' => 'pos_user_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_user_id,
								'validate' => 'none'),
						array( 'db_field' => 'pos_user_group_id',
								'caption' => 'Group Id',
								'type' => 'select',
								'html' => createUserGroupSelect('pos_user_group_id', 'false', 'off'),
								'validate' => 'none'),
						array( 'db_field' => 'pos_employee_id',
								'caption' => 'Employee Id',
								'type' => 'select',
								'html' => createEmployeeSelect('pos_employee_id', 'false', 'off'),
								'validate' => 'none'),
						array( 'db_field' => 'default_store_id',
								'caption' => 'default store',
								'type' => 'select',
								'html' => createStoreSelect('default_store_id', 'false', 'off'),
								'validate' => 'none'),
						array('db_field' =>  'first_name',
								'type' => 'input',
								'caption' => 'First Name',
								'validate' => 'none'),
						array('db_field' =>  'last_name',
								'type' => 'input',
								'caption' => 'Last Name',
								'validate' => 'none'),
						array('db_field' =>  'login',
								'type' => 'input',
								'caption' => 'Login ID',
								'db_table' => $db_table,
								'validate' => $unique_validate),
						array('db_field' =>  'email',
								'type' => 'input',
								'caption' => 'Email',
								'validate' => 'none'),
						array('db_field' =>  'default_view_date_range_days',
								'type' => 'input',
								'caption' => 'Default Number of Days For Views',
								'validate' => 'number'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'validate' => 'none'),
						array('db_field' =>  'locked',
								'type' => 'checkbox',
								'validate' => 'none'),
						array('db_field' =>  'admin',
								'type' => 'checkbox',
								'validate' => 'none'),

						array('db_field' =>  'database_access',
								'type' => 'select',
								'html' => createEnumSelect('database_access','pos_users','database_access', 'false', 'off', ''),
								'caption' => 'Database Access',
								'validate' => 'none'),
						array('db_field' =>  'level',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'timeout_minutes',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'max_connections',
								'value' => 1,
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'relogin_on_ip_address_change',
								'value' => 1,
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'relogin_on_browser_change',
								'value' => 1,
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'ip_address_restrictions',
								'type' => 'input',
								'validate' => 'none')
								
				);	

	if ($type == 'View')
	{
		$table_def = array_merge($table_def, array(array('db_field' =>  'rights',
								'type' => 'input',
								'validate' => 'none')));
	}
	
				
	return $table_def;
	
}
function CreatePasswordTableDef()
{
		$pswrd_def = array(
							/*array('db_field' => 'password_original',
							'type' => 'input',
							'caption' => 'Enter Original Password',
								'tags' => ' type = "password" ',
								'validate' => 'none'),*/
							array('db_field' => 'password1',
							'type' => 'input',
							'caption' => 'Enter A New Password',
								'tags' => ' type = "password" ',
								'validate' => 'password'),
							array('db_field' => 'password2',
							'caption' => 'Confirm Password',
							'type' => 'input',
								'tags' => ' type = "password" ',
								'validate' => 'password')
								
								);
				return $pswrd_def;			
								
}
function updatePassword($pos_user_id, $password)
{
		$sql = "UPDATE pos_users SET password=SHA1('$password') WHERE pos_user_id=$pos_user_id LIMIT 1";
		return runSQL($sql);
}
function checkPasswordIsValid($password1, $password2)
{
	// Check for a new password and match against the confirmed password:
	$password = FALSE;
	$pattern1 = '/^(\w){4,20}$/';
	$pattern2 = '/^.*(?=.{7,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/';
	$pattern4 = '/^.*(?=.{7,})(?=.*\d).*$/';
//check this explanation out for regrex: http://stackoverflow.com/questions/11873990/create-preg-match-for-password-validation-allowing

 	 // Must be at least 8 characters
	//Must contain at least one one lower case letter, one upper case letter, one digit and one special character
	//Valid special characters are -   *@#$%^&+=
	$pattern3 = '/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[*@#$%^&+=]).*$/';


	if (preg_match ($pattern4, $password1) ) 
	{
		if ($password1 == $password2) 
		{
			return 'Password Updated';
		}
		else 
		{
			$html =  'Password Update Failed. Your password did not match the confirmed password!';
			return $html;
		}
	} 
	else 
	{
		$html = 'Password Update Failed. Please enter a valid password! You Need minimum 8 characters, 1 special character *@#$%^&+= one upper case, on lower case, and one digit.';
		return $html;
	}
} 
function createRoomTableDef($type, $pos_room_id, $pos_user_id)
{
	if (strtoupper($type) == 'NEW')
	{
		$pos_room_id = 'TBD';
		$unique_validate =  array('unique_array' => array('room_name','pos_user_id'), 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_room_id'] = $pos_room_id;
		$unique_validate = array('unique_array' => array('room_name','pos_user_id'), 'min_length' => 1, 'id' => $key_val_id);
	}
	$db_table = 'pos_rooms';

	$table_def = array(
						array( 'db_field' => 'pos_room_id',
								'caption' => 'Room ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_room_id,
								'validate' => 'none'),
						array( 'db_field' => 'pos_user_id',
								'caption' => 'User ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_user_id,
								'validate' => 'none'),
						array( 'db_field' => 'room_name',
								'caption' => 'Room Name',
								'type' => 'input',
								'db_table' => $db_table,
								'validate' => $unique_validate),
						array( 'db_field' => 'priority',
								'caption' => 'Priority',
								'type' => 'input'));
	return $table_def;
						
}
function createBinderCollectionTableDef($type, $pos_binder_collection_id)
{
	if (strtoupper($type) == 'NEW')
	{
		$pos_binder_collection_id = 'TBD';
		$unique_validate =  array('unique_array' => array('binder_collection_name','pos_user_id'), 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_binder_collection_id'] = $pos_binder_collection_id;
		$unique_validate = array('unique_array' => array('binder_collection_name','pos_user_id'), 'min_length' => 1, 'id' => $key_val_id);
	}
	$db_table = 'pos_binder_collections';

	$table_def = array(
						array( 'db_field' => 'pos_binder_collection_id',
								'caption' => 'Room ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_binder_collection_id,
								'validate' => 'none'),

						array( 'db_field' => 'binder_collection_name',
								'caption' => 'Binder Collection Name',
								'type' => 'input',
								'db_table' => $db_table,
								'validate' => $unique_validate),
						);
	return $table_def;
						
}
function binderTableDef($type, $pos_binder_id)
{
	if ($type == 'New')
	{
		$pos_binder_id = 'TBD';
		$unique_validate =  array('unique' => 'true', 'min_length' => 1);
	}
	else
	{
		$unique_validate = array('unique' => 'true', 'min_length' => 1, 'id' => array('pos_binder_id' => $pos_binder_id));
	}
	$db_table = 'pos_binders';

	$table_def = array(
						array( 'db_field' => 'pos_binder_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_binder_id,
								'validate' => 'none'),
						
						array('db_field' =>  'binder_name',
								'type' => 'input',
								'caption' => 'Binder Name',
								'validate' => 'none'),
						array('db_field' =>  'enabled',
								'type' => 'input',
								'caption' => 'enabled',
								'validate' => 'number'),
						array('db_field' =>  'binder_collection',
								'type' => 'select',
								'html' => createEnumSelect('binder_collection','pos_binders','binder_collection', 'false', 'off', ''),
								'caption' => 'Binder Collection',
								'validate' => 'none'),
						array('db_field' =>  'binder_path',
								'type' => 'input',
								'caption' => 'Binder Path',
								'validate' => 'none'),
						array('db_field' =>  'navigation_caption',
								'type' => 'input',
								'caption' => 'Navigation Caption',
								'validate' => 'none'),
						array('db_field' =>  'default_rooms',
								'type' => 'input',
								'caption' => 'Default Rooms (comma separated)',
								'validate' => 'none'),
						
						
								
				);	

	
				
	return $table_def;
	
}
function customBinderTableDef($type, $pos_custom_binder_id)
{
	if ($type == 'New')
	{
		$pos_custom_binder_id = 'TBD';
		$unique_validate =  array('unique' => 'true', 'min_length' => 1);
	}
	else
	{
		$unique_validate = array('unique' => 'true', 'min_length' => 1, 'id' => array('pos_binder_id' => $pos_custom_binder_id));
	}
	$db_table = 'pos_binders';

	$table_def = array(
						array( 'db_field' => 'pos_custom_binder_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_custom_binder_id,
								'validate' => 'none'),
						
						array('db_field' =>  'binder_name',
								'type' => 'input',
								'caption' => 'Binder Name',
								'validate' => 'none')
						
						
								
				);	

	
				
	return $table_def;
	
}
function createRoomList($pos_user_id)
{
		$html = '<p>Rooms</p>';
		$html .= '<input class = "button" type="button" name="edit" style="width:150px;" value="Add A Room" onclick="open_win(\'../RoomArrangement/room_arrangement.php?type=Add&pos_user_id='.$pos_user_id.' \')"/>';
		$tmp_sql = "
	
			CREATE TEMPORARY TABLE rooms

			SELECT DISTINCT room_name, room_priority 
			FROM pos_room_arrangements
			WHERE pos_user_id = $pos_user_id
			
			;";
				
		$tmp_select_sql = "SELECT * FROM rooms WHERE 1 ORDER BY room_priority DESC";			
		$table_columns = array(
			array( 'th' => 'View',
			'mysql_field' => 'room_name',
			'get_url_link' => "../RoomArrangement/room_arrangement.php?type=View&pos_user_id=".$pos_user_id,
			'url_caption' => 'View',
			'get_id_link' => 'room_name'),
		array(
			'th' => 'Room Name',
			'mysql_field' => 'room_name'),
		array(
			'th' => 'Room Priority',
			'mysql_field' => 'room_priority'),
		/*array(
			'th' => 'Binders',
			'mysql_field' => 'binder')*/
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html .= createRecordsTable($data, $table_columns);
	return $html;
}
function listBindersInCollection($pos_user_id,$pos_binder_collection_id)
{
	

	
	$table_columns = array(
	
			array(
				'th' => 'ID',
				'mysql_field' => 'pos_binder_id',
				'sort' => 'pos_binder_id'),	
			array(
				'th' => 'Binder Name',
				'mysql_field' => 'binder_name',
				'sort' => 'binder_name')
			
			
			);
	
	//here is the query that the search and table arrays are built off of.
	$tmp_sql = "
	CREATE TEMPORARY TABLE binders_in_collection
	
	SELECT  pos_binders.pos_binder_id, pos_binders.binder_name, pos_binder_access.access
			FROM pos_binders
			LEFT JOIN pos_binders_in_collections USING (pos_binder_id)
			WHERE pos_binders_in_collection.pos_user_id = $pos_user_id AND pos_binders_in_collection.pos_binder_collection_id = $pos_binder_collection_id
	;
	
	
	";
	$tmp_select_sql = "SELECT * 
		FROM binders_in_collection WHERE 1";
	
	
	$html = '<p>';
	$html .= '<input class = "button" type="button" style="width:300px" name="add_system" value="Add A Binder" onclick="open_win(\''.POS_ENGINE_URL . 'users/Rooms/binders_in_collection.php?pos_user_id='.$pos_user_id.'&pos_binder_collection_id='.$pos_binder_collection_id.'\')"/>';
	$html .= '</p>';
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
	//now make the table
	
	$html .= createRecordsTable($data, $table_columns);
	return $html;


}
function getBinderNamesAndIds($pos_user_id)
{
	$combined_binders = array();
	$binder_sql = "SELECT pos_binders.pos_binder_id, pos_binders.binder_name, 'pos_binders' as source FROM pos_binders 
			LEFT join pos_user_binder_access USING (pos_binder_id)
			WHERE pos_user_binder_access.pos_user_id = $pos_user_id AND binder_type= 'SYSTEM'";
	$custom_binder_sql = "
			SELECT pos_custom_binders.pos_custom_binder_id, pos_custom_binders.binder_name, 'pos_custom_binders' as source
			FROM pos_custom_binders 
			LEFT join pos_user_binder_access USING (pos_custom_binder_id)
			WHERE pos_user_binder_access.pos_user_id = $pos_user_id AND binder_type= 'CUSTOM'";
	$binder_collection_sql ="
			SELECT pos_binder_collection_id, binder_collection_name, 'pos_binder_collections' as source FROM pos_binder_collections WHERE pos_user_id=$pos_user_id";
			
			
	$binders = getSQL($binder_sql);
	$custom_binders = getSQL($custom_binder_sql);
	//$binder_collections = getSQL($binder_collection_sql);
	$binder_counter = 0;
	$combined_binders['name'] = array();
	$combined_binders['id'] = array();
	$combined_binders['name'][$binder_counter] = 'DIVIDER';
	$combined_binders['id'][$binder_counter] = 'DIVIDER';
	$binder_counter++;
	for($i = 0;$i < sizeof($binders); $i++)
	{
		$combined_binders['name'][$binder_counter] ='System Binder: ' . $binders[$i]['binder_name'];
		$combined_binders['id'][$binder_counter] =  $binders[$i]['source'] . '::'.$binders[$i]['pos_binder_id'];
		$binder_counter++;

	}
	for($i = 0;$i < sizeof($custom_binders); $i++)
	{
		$combined_binders['name'][$binder_counter] = 'Custom Binder: ' . $custom_binders[$i]['binder_name'];
		$combined_binders['id'][$binder_counter] =  $custom_binders[$i]['source'] . '::'.$custom_binders[$i]['pos_custom_binder_id'];
		$binder_counter++;

	}
	/*for($i = 0;$i < sizeof($binder_collections); $i++)
	{
		$combined_binders['name'][$binder_counter] = 'Binder Collection: ' . $binders['binder_name'];
		$combined_binders['id'][$binder_counter] =  $binders['source'] . '::'.$binders['pos_binder_collection_id'];
		$binder_counter++;

	}*/
	return $combined_binders;
}

function createBinderCustomBinderBinderCollectionSelect($pos_user_id, $binder_id, $binder_source)
{
	//this wil create an avaiable list of binders and collections in an html drop down
	
	$binder_sql = "SELECT pos_binders.pos_binder_id, pos_binders.binder_name, 'pos_binders' as source FROM pos_binders 
			LEFT join pos_user_binder_access USING (pos_binder_id)
			WHERE pos_user_binder_access.pos_user_id = $pos_user_id AND binder_type= 'SYSTEM'";
	$custom_binder_sql = "
			SELECT pos_custom_binders.pos_custom_binder_id, pos_custom_binders.binder_name, 'pos_custom_binders' as source
			FROM pos_custom_binders 
			LEFT join pos_user_binder_access USING (pos_custom_binder_id)
			WHERE pos_user_binder_access.pos_user_id = $pos_user_id AND binder_type= 'CUSTOM'";
	$binder_collection_sql ="
			SELECT pos_binder_collection_id, binder_collection_name, 'pos_binder_collections' as source FROM pos_binder_collections WHERE pos_user_id=$pos_user_id";
			
			
	$binders = getSQL($binder_sql);
	$custom_binders = getSQL($custom_binders);
	$binder_collections = getSQL($binder_collection_sql);
	$name = 'binders';
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Binder or Collection</option>';
	$html .= '<option value="divider">Binder Divider</option>';
	
	for($i = 0;$i < sizeof($binders); $i++)
	{
		$html .= '<option value="' . $binders[$i]['source'] .'::'. $binders[$i]['pos_binder_id'] . '"';
		if ( $binders[$i]['pos_binder_id'] == $pos_binder_id AND $binder_source == $binders[$i]['source'] ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>System Binder: ' . $binders[$i]['binder_name'] . '</option>';
	}
	for($i = 0;$i < sizeof($custom_binders); $i++)
	{
		$html .= '<option value="' . $custom_binders[$i]['source'] .'::'. $custom_binders[$i]['pos_custom_binder_id'] . '"';
		if ( $custom_binders[$i]['pos_custom_binder_id'] == $pos_binder_id AND $binder_source == $custom_binders[$i]['source'] ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>Custom Binder: ' . $custom_binders[$i]['binder_name'] . '</option>';
	}
	for($i = 0;$i < sizeof($binder_collections); $i++)
	{
		$html .= '<option value="' . $binder_collections[$i]['source'] .'::'. $binder_collections[$i]['pos_binder_collection_id'] . '"';
		if ( $binder_collections[$i]['pos_binder_collection_id'] == $pos_binder_id AND $binder_source == $binder_collections[$i]['source'] ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>Binder Collection: ' . $binder_collections[$i]['binder_collection_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;
	
}
function listUserBinderAccess($pos_user_id)
{

	$table_columns = array(
	
			array(
				'th' => 'ID',
				'mysql_field' => 'pos_binder_id',
				'sort' => 'pos_binder_id'),	
			array(
				'th' => 'Binder Name',
				'mysql_field' => 'binder_name',
				'sort' => 'binder_name'),
			array(
				'th' => 'Binder Access',
				'mysql_field' => 'access',
				'sort' => 'access')
			
			
			);
	
	//here is the query that the search and table arrays are built off of.
	$tmp_sql = "
	CREATE TEMPORARY TABLE binder_access
	
	SELECT  pos_binders.pos_binder_id, pos_binders.binder_name, pos_user_binder_access.access
			FROM pos_binders
			LEFT JOIN pos_user_binder_access USING (pos_binder_id)
			WHERE pos_user_binder_access.pos_user_id = $pos_user_id
	;
	
	
	";
	$tmp_select_sql = "SELECT * 
		FROM binder_access WHERE 1";
	
	
	$html = '<p>';
	$html .= '<input class = "button" type="button" style="width:300px" name="add_system" value="Edit Binder Access" onclick="open_win(\''.POS_ENGINE_URL . '/users/ManageUserAccounts/user_binder_access.php?pos_user_id='.$pos_user_id.'\')"/>';
	$html .= '</p>';
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
	//now make the table
	
	$html .= createRecordsTable($data, $table_columns);
	return $html;


}




?>