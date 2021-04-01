<?php
require_once(MYSQL_POS_CONNECT_FILE);
require_once(DEBUG_FUNCTIONS);
function openPOSdb()
{
	return openPOSDatabase();
}
function closeDB($dbc)
{
	mysqli_close($dbc);
}
function openPOSDatabase()
{	
	$pos_dbc = pos_connection();
	return $pos_dbc;
}
function openWebStoreDatabase()
{
	require_once(WEBSTORE_MYSQL_CONNECT_FILE);
	$pcart_dbc = webStoreConnection();
	return $pcart_dbc;
}
function switchArrayFromRowFieldToFieldRows($array)
{
	$output = array();
	for($i=0;$i<sizeof($array);$i++)
	{
		foreach($array[$i] as $key => $value)
		{
			$output[$key][$i] = $array[$i][$key];
		}
	}
	return $output;
}
function convert_mysql_result_to_field_row_array($result)
{
	/*
	* This function will take a mysql result and convert it to a php array
	* access variables like this: array[0]['column_name'] if it is multdimensional
	*/
	$cntr = 0;
	$php_array_from_mysql = array();
	//if (mysql_num_rows($result) == 0) ? need to test for bunk result?
	while($row = mysqli_fetch_array($result, MYSQL_ASSOC))
	{
		//this little sweet trick will dump the row to an array
		foreach($row as $key => $value)
		{
			$php_array_from_mysql[$key][$cntr] = $value;
			//echo '<p>' . $key . ': ' . $php_array_from_mysql[$cntr][$key]. '</p>';
		}
		$cntr++;
	}	
	return $php_array_from_mysql;
}
function convert_mysql_result_to_array($result)
{
	/*
	* This function will take a mysql result and convert it to a php array
	* access variables like this: array[0]['column_name'] if it is multdimensional
	*/
	$cntr = 0;
	$php_array_from_mysql = array();
	//if (mysql_num_rows($result) == 0) ? need to test for bunk result?
	while($row = mysqli_fetch_array($result, MYSQL_ASSOC))
	{
		//this little sweet trick will dump the row to an array
		foreach($row as $key => $value)
		{
			$php_array_from_mysql[$cntr][$key] = $value;
			//echo '<p>' . $key . ': ' . $php_array_from_mysql[$cntr][$key]. '</p>';
		}
		$cntr++;
	}	
	return $php_array_from_mysql;
}
function convert_mysql_data_to_indexed_array($data)
{
	$php_array_from_mysql = array();
	for($row = 0;$row<sizeof($data);$row++)
	{
		foreach($data[$row] as $key => $value)
		{
			$php_array_from_mysql[$key][$row] = $value;
		}
	}	
	return $php_array_from_mysql;
}
function my_sql_query($database, $sql_statement)
{
	$result= @mysqli_query($database, $sql_statement);
	return $result;
}
function checkSQLIfExists($sql)
{
		$dbc = openPOSDatabase();
		$result = @mysqli_query($dbc, $sql);
		if (mysqli_num_rows($result) == 0) 
		{
			return false;
		}
		else
		{
			return true;
		}
		mysqli_close($dbc);
}
function getSingleValueSQL($sql)
{
	$result = getSQL($sql);
	if (sizeof($result)>0)
	{
		foreach($result[0] as $key => $value)
		{
			$return_val =  $value;
		}
		if (isset($return_val))
		{
			return $return_val;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}	
}
function getSQL($sql)
{
	$dbc = openPOSDatabase();
	$result = @mysqli_query($dbc, $sql);
	if ($result) 
	{ // If it ran OK.
		$result_array = convert_mysql_result_to_array($result);
		mysqli_close($dbc);
		return $result_array;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
		mysqli_close($dbc);
		return false;
	}	
	
	

}
function getFieldRowSql($sql)
{
	$dbc = openPOSDatabase();
	$result = @mysqli_query($dbc, $sql);
	if ($result) 
	{ // If it ran OK.
		$result_array = convert_mysql_result_to_array($result);
		mysqli_close($dbc);
		return switchArrayFromRowFieldToFieldRows($result_array);
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
		mysqli_close($dbc);
		return false;
	}	
}
function runSQL($sql)
{
	$dbc = openPOSDatabase();
	$result = @mysqli_query($dbc, $sql);
	if ($result) 
	{ // If it ran OK.
		mysqli_close($dbc);
		return $result;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
		mysqli_close($dbc);
		return false;
	}	
	
}
function scrubFloat($float)
{
	//this is just removing the commas
	return floatval(str_replace(',', '', $float));

}
function scrubInput($input)
{
	$dbc = openPOSDatabase();
	//echo '<p>' . $input . '</p>';
	if (get_magic_quotes_gpc()) 
	{
		$input = stripslashes($input);
	}
	// Quote if not integer
	if (!is_numeric($input)) 
	{
		$input =  mysqli_real_escape_string($dbc, trim($input));
	}
	mysqli_close($dbc);
	//this is added in case there are a shit ton of backslashes added - doesn't seem correct?
	//$input = str_replace("\\","", $input);
	//echo '<p>' . $input . '</p>';
	return $input;
	
}

function lock_entry($table, $key_val_id)
{
	$sql_update_string = " user_id_for_entry_lock = " .$_SESSION['pos_user_id'];
	$update_q = "UPDATE ".$table." SET " . $sql_update_string . " WHERE " . key($key_val_id) . "='" .$key_val_id[key($key_val_id)]."'";
	return runSQL($update_q);
}
function unlock_entry($table, $key_val_id)
{
	$sql_update_string = " user_id_for_entry_lock = 0 ";
	$update_q = "UPDATE ".$table." SET " . $sql_update_string . " WHERE " . key($key_val_id) . "='" .$key_val_id[key($key_val_id)]."'";
	return runSQL($update_q);
}
function get_entry_lock($table, $key_val_id)
{
	$sql = "SELECT user_id_for_entry_lock FROM ".$table." WHERE " . key($key_val_id) . "='" .$key_val_id[key($key_val_id)]."'";
	return getSingleValueSQL($sql);
}
function check_lock($table, $key_val_id, $complete_location, $cancel_location)
{
	$page_title = 'Locked';
	$entry_lock = get_entry_lock($table, $key_val_id);
	if( $entry_lock != 0)
	{
		
		//problem.... the entry is coded as locked.... this could be because someone is editing it, or a connection was lost and it needs to be unlocked.
		//ask for advice
		$form_handler = POS_ENGINE_URL .'/includes/php/entry_lock.php';
		$html = '<form id = "entry_lock" name="entry_lock" action="'. $form_handler. '" method="post" >';
		$html .= '<p>Problem! This Entry is Coded as LOCKED by ' . getUserFullName($entry_lock) .', meaning one of two things: 1) Another user is busy monkeying with the data, and if you go into it then you might destroy that fresh data, or 2) Somewhere along the way the entry was locked and then the user\'s session expired or a power failure or something else catastrophic happened, meaning you should use the unlock entry option. <br> Either way, make a choice to unlock the entry or leave it locked.</p>';
		//$html .= '<input type="checkbox" name="unlock" value="unlock">Unlock The Table';
		//$html .= '<br>';
		$primary_key_name = key($key_val_id);
		$primary_key_value = $key_val_id[$primary_key_name];
		$html.= createHiddenInput('table', $table);
		$html.= createHiddenInput('primary_key_name', $primary_key_name);
		$html.= createHiddenInput('primary_key_value', $primary_key_value);
		$html.= createHiddenInput('complete_location', $complete_location);
		$html.= createHiddenInput('cancel_location', $cancel_location);
		$html .= '<p><input class = "button" type="submit" name="submit" style="width:200px" value="Unlock Entry And Edit"/>';
		$html .= '<input class = "button" type="submit" name="cancel" style="width:200px"  value="Cancel and Return"/></p>';
		$html .='</form>';
		include (HEADER_FILE);
		echo $html;
		include (FOOTER_FILE);
		exit();
		//exit the code
	}
	else
	{
		//do nothing
	}
}
function check_lockV2($table, $key_val_id)
{
	$page_title = 'Locked';
	$entry_lock = get_entry_lock($table, $key_val_id);
	if( $entry_lock != 0)
	{
		
		//problem.... the entry is coded as locked.... this could be because someone is editing it, or a connection was lost and it needs to be unlocked.
		//ask for advice
		$form_handler = POS_ENGINE_URL .'/includes/php/entry_lock.php';
		$html = '<form id = "entry_lock" name="entry_lock" action="'. $form_handler. '" method="post" >';
		$html .= '<p>Problem! This Entry is Coded as LOCKED by ' . getUserFullName($entry_lock) .', meaning one of two things: 1) Another user is busy monkeying with the data, and if you go into it then you might destroy that fresh data, or 2) Somewhere along the way the entry was locked and then the user\'s session expired or a power failure or something else catastrophic happened, meaning you should use the unlock entry option. <br> Either way, make a choice to unlock the entry or leave it locked.</p>';
		//$html .= '<input type="checkbox" name="unlock" value="unlock">Unlock The Table';
		//$html .= '<br>';
		$primary_key_name = key($key_val_id);
		$primary_key_value = $key_val_id[$primary_key_name];
		$html.= createHiddenInput('table', $table);
		$html.= createHiddenInput('primary_key_name', $primary_key_name);
		$html.= createHiddenInput('primary_key_value', $primary_key_value);
		$html.= createHiddenInput('complete_location', $complete_location);
		$html.= createHiddenInput('cancel_location', $cancel_location);
		$html .= '<p><input class = "button" type="submit" name="submit" style="width:200px" value="Unlock Entry And Edit"/>';
		$html .= '<input class = "button" type="submit" name="cancel" style="width:200px"  value="Cancel and Return"/></p>';
		$html .='</form>';
		return $html;

		//exit the code
	}
	else
	{
		return false;
	}
}
?>