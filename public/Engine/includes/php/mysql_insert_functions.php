<?php
require_once(PHP_LIBRARY);
require_once(MYSQL_TRANSACTION_FUNCTIONS);
function checkResult($result, $sql, $dbc)
{
	if(!$result)
	{ 
		// If it did not run OK.
		@mysqli_query($dbc, "ROLLBACK");
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
		mysqli_close($dbc);
	}
}
function updateSQL($sql)
{
	$dbc = openPOSDatabase();
	$result = @mysqli_query($dbc, $sql);
	if (!$result)
	{
		trigger_error( "<p>Update error: " . $sql .'</p>');
	}
	mysqli_close($dbc);
	return $result;
}

function updateDBCSQL($sql, $dbc)
{
	$result = @mysqli_query($dbc, $sql);
	if (!$result)
	{
		trigger_error( "<p>Update error: " . $sql .'</p>');
	}
	return $result;
}
function insertSQL($sql)
{
	$dbc = openPOSDatabase();
	$result = @mysqli_query($dbc, $sql);
	if (!$result)
	{
		$result = "<p>Insert error: " . $sql .'</p>';
	}
	mysqli_close($dbc);
	return $result;
}
function simpleInsertSQLString($table, $mysql_data)
{
	// Make the query out of the keys and values - they should match mysql fields
	//get the keys
	$db_fields = array_keys($mysql_data);
	$str_fields = implode(', ', $db_fields);
	$row_array = array();
	foreach($db_fields as $field)
	{
		$row_array[] = "'" . $mysql_data[$field] . "'";	
    }
    $db_values = '(' .  implode(', ',$row_array) .')';
	
	$insert_q = "INSERT INTO ".$table." (" . $str_fields . ") VALUES  " .  $db_values ;
	return $insert_q;
}
function simpleInsertSQL($table, $mysql_data)
{
	//send in mysql_data that matches the mysql table.... easy breazy
	$dbc = openPOSDatabase();
	$insert_q = simpleInsertSQLString($table, $mysql_data);
	$result = @mysqli_query ($dbc, $insert_q); 
	if ($result) 
	{ // If it ran OK.
		return $result;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $insert_q . '</p>', E_USER_WARNING);
		return false;
	}	
	mysqli_close($dbc);
}
function simpleInsertSQLReturnID($table, $mysql_data)
{
	//send in mysql_data that matches the mysql table.... easy breazy
	$dbc = openPOSDatabase();
	$insert_q = simpleInsertSQLString($table, $mysql_data);
	$result = @mysqli_query ($dbc, $insert_q); 
	if ($result) 
	{ // If it ran OK. return the id
		$lastID = mysqli_insert_id($dbc);
		return $lastID;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $insert_q . '</p>', E_USER_WARNING);
		return false;
	}	
	mysqli_close($dbc);
}
function simpleUpdateSQLString($table, $key_val_id, $mysql_data)
{
	//use like: $mysql_data[$db_field] = $value;
	//send in mysql_data that matches the mysql table.... easy breazy
	// the $id should have a key like this: $id['pos_expense_id'] = $_POST['pos_expense_id'];
	//UPDATE table SET field = 'value', fied2 = 'value2' WHERE bla

	$db_fields = array_keys($mysql_data);
	$key_value_array = array();
	foreach($db_fields as $field)
	{
		$key_value_array[] = $field . " = '" .$mysql_data[$field] ."'";
    }
    $sql_update_string  =  implode(', ',$key_value_array);
	$update_q = "UPDATE ".$table." SET " . $sql_update_string . " WHERE " . key($key_val_id) . "='" .$key_val_id[key($key_val_id)]."'";
	return $update_q;
	
}
function simpleUpdateSQL($table, $key_val_id, $mysql_data)
{
	//use like: $update_data = array('verify' => 1,'blbla'=>'hello');
	//send in mysql_data that matches the mysql table.... easy breazy
	// the $id should have a key like this: $id['pos_expense_id'] = $_POST['pos_expense_id'];
	$update_q = simpleUpdateSQLString($table, $key_val_id, $mysql_data);
	$dbc = openPOSDatabase();
	$result = @mysqli_query ($dbc, $update_q); 
	if ($result) 
	{ // If it ran OK.
		return $result;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $update_q . '</p>', E_USER_WARNING);
		return false;
	}	
	mysqli_close($dbc);
}
function arrayInsertSQLString($table, $mysql_data)
{
	$db_fields = array_keys($mysql_data[0]);
	$str_fields = implode(', ', $db_fields);
	for($i=0;$i<sizeof($mysql_data);$i++)
	{
		$row_array = array();
		foreach($db_fields as $field)
		{
			$row_array[] = "'" . $mysql_data[$i][$field] . "'";	
    	}
    	$db_values[] = '(' .  implode(', ',$row_array) .')';
    }
	$all_insert_values = implode(', ',$db_values);
	$insert_q = "INSERT INTO ".$table." (" . $str_fields . ") VALUES  " .  $all_insert_values;
	return $insert_q;
}
function arrayInsertSQL($table, $mysql_data)
{
	$insert_q = arrayInsertSQLString($table, $mysql_data);
	
	return runSQL($insert_q);
}
function arrayUpdateSQLString($table, $mysql_data_array)
{
	/* the data array should look like this:
	$mysql_data_array[0]['db_field'] = 
	$mysql_data_array[0]['id'] = 
	$mysql_data_array[0]['data_array'][id value 1] = data 1
	$mysql_data_array[0]['data_array'][id value 1] = data 2
	
	ex:
	$mysql_data_array[0]['db_field'] = 'cost';
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['data_array']['3789'] = 30.75;
	$mysql_data_array[0]['data_array']['3790'] = 40.75;
	$mysql_data_array[1]['db_field'] = 'retail';
	$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
	$mysql_data_array[1]['data_array']['3789'] = 60.75;
	$mysql_data_array[1]['data_array']['3790'] = 80.75;
	*/
	
	//first check if anything is empty....
	$empty_set = false;
	for($field_index=0;$field_index<sizeof($mysql_data_array);$field_index++)
	{	
		$sql_array[$field_index] = $mysql_data_array[$field_index]['db_field'] . " = CASE ";
		if (!isset($mysql_data_array[$field_index]['data_array']))
		{
			$empty_set = true;
		}
	}
	
	if ($empty_set == false)
	{
		$sql = "UPDATE ".$table. " SET " ;
		$sql_array = array();
		$where_array = array();
		for($field_index=0;$field_index<sizeof($mysql_data_array);$field_index++)
		{	
			$sql_array[$field_index] = $mysql_data_array[$field_index]['db_field'] . " = CASE ";
			foreach($mysql_data_array[$field_index]['data_array'] as $id_key => $value)
			{
				 $sql_array[$field_index] .= " WHEN " .$mysql_data_array[0]['id']  . " = '" .$id_key . "' THEN '" .$value ."'";
			}
			$sql_array[$field_index] .= " ELSE " . $mysql_data_array[$field_index]['db_field'] . " END";
		}
		foreach($mysql_data_array[0]['data_array'] as $id_key => $value)
		{
			$where_array[] =$mysql_data_array[0]['id'] . " = '" . $id_key ."'";
		}
		
		$where = " WHERE " .implode(" OR ", $where_array);
		$sql .= implode(', ',$sql_array) . $where;
		//echo $sql;
	
			return $sql;
	}
	else
	{
		return false;
	}
}
function arrayUpdateSQL($table, $mysql_data_array)
{
	$sql = arrayUpdateSQLString($table, $mysql_data_array);
	if ($sql != false)
	{
		return runSQL($sql);
	}
	else
	{
		return false;
	}
}

function getMysqlError()
{
	$dbc = openPOSDatabase();
}

function deleteThenInsertArray($table, $id, $mysql_data_array)
{
	$delete_q = "DELETE FROM ".$table." WHERE ".key($id) . "='" .$id[key($id)]."'";
	$result1 = runSQL($delete_q);
	$result2 = arrayInsertSQL($table, $mysql_data_array);
	return array($result1,$result2);
	
}
function simpleInsertOnDuplicateUpdateSQLString($table, $mysql_data)
{
	/* mysql_data should look like this:
		$mysql_data['mysql_column'] = value
	*/
	$db_fields = array_keys($mysql_data);
	$str_fields = implode(', ', $db_fields);
	foreach($db_fields as $field)
	{
		$row_array[] = "'" . $mysql_data[$field] . "'";	
    }
    $db_values[] = '(' .  implode(', ',$row_array) .')';
    
	$all_insert_values = implode(', ',$db_values);
	foreach($db_fields as $field)
	{
		$key_value_array[] = $field . " = '" .$mysql_data[$field] ."'";
    }
    $all_update_values  =  implode(', ',$key_value_array);
	$insert_update_q = "INSERT INTO ".$table." (" . $str_fields . ") VALUES  " .  $all_insert_values . " ON DUPLICATE KEY UPDATE " . $all_update_values;
	return $insert_update_q;
}
function simpleInsertOnDuplicateUpdateSQL($table, $mysql_data)
{

	$insert_update_q = simpleInsertOnDuplicateUpdateSQLString($table, $mysql_data);
	$dbc = openPOSDatabase();
	$result = @mysqli_query ($dbc, $insert_update_q); 
	if ($result) 
	{ // If it ran OK. return the id
		$lastID = mysqli_insert_id($dbc);
		return $lastID;
	} else 
	{ // If it did not run OK.
		// Debugging message:
		trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $insert_update_q . '</p>', E_USER_WARNING);
		return false;
	}	
	mysqli_close($dbc);

}


function postArraytoMysqlArray($post_array)
{	
	$post_array = unserialize(stripslashes(htmlspecialchars_decode($post_array)));
	$insert=array();
	foreach($post_array as $mysql_field)
	{
		$insert[$mysql_field] = scrubInput($_POST[$mysql_field]);
	}
	return $insert;
}
function deserializeTableDef($table_def)
{
	return  unserialize(stripslashes(htmlspecialchars_decode($table_def)));
}
function deserializeHiddenInput($post_value)
{
	return unserialize(stripslashes(htmlspecialchars_decode($post_value)));
}
function tableDefArraytoMysqlInsertArray($table_def)
{
	//make an insert array like this:
	//$update_data[$db_field] = $value;
	$insert_array=array();
	foreach($table_def as $mysql_field)
	{
		if ($mysql_field['type'] == 'checkbox')
		{
			if(isset($_POST[$mysql_field['db_field']]) && $_POST[$mysql_field['db_field']] =='on')
			{
				$insert_array[$mysql_field['db_field']] = 1;
			}
			else
			{
				$insert_array[$mysql_field['db_field']] = 0;
			}
		
		}
		elseif ($mysql_field['type'] == 'file_input')
		{
			//do nothing
		}
		else
		{
			$insert_array[$mysql_field['db_field']] = scrubInput($_POST[$mysql_field['db_field']]);
		}
	}
	return $insert_array;
}
function tableDefArraytoMysqlUpdateArray($table_def,$primary_key)
{
	//make san update array like this:
	//$update_data[$db_field] = $value;
	$update_array=array();
	foreach($table_def as $mysql_field)
	{
		if ($mysql_field['db_field']!=$primary_key)
		{
			if ($mysql_field['type'] == 'checkbox')
			{
				if(isset($_POST[$mysql_field['db_field']]) && $_POST[$mysql_field['db_field']] =='on')
				{
					$update_array[$mysql_field['db_field']] = 1;
				}
				else
				{
					$update_array[$mysql_field['db_field']] = 0;
				}
			
			}
			elseif ($mysql_field['type'] == 'file_input')
			{
				//do nothing
			}
			else
			{
				$update_array[$mysql_field['db_field']] = scrubInput($_POST[$mysql_field['db_field']]);
			}
		}
	}
	return $update_array;	
		
}
function postedTableDefArraytoMysqlInsertArray($table_def)
{
	$table_def = unserialize(stripslashes(htmlspecialchars_decode($table_def)));
	$insert_array=array();
	foreach($table_def as $mysql_field)
	{
		if ($mysql_field['type'] == 'checkbox')
		{
			if(isset($_POST[$mysql_field['db_field']]) && $_POST[$mysql_field['db_field']] =='on')
			{
				$insert_array[$mysql_field['db_field']] = 1;
			}
			else
			{
				$insert_array[$mysql_field['db_field']] = 0;
			}
		
		}
		else
		{
			$insert_array[$mysql_field['db_field']] = scrubInput($_POST[$mysql_field['db_field']]);
		}
	}
	return $insert_array;
}
function postedTableDefArraytoMysqlUpdateArray($table_def, $primary_key)
{
	//use like: $update_data[$db_field] = $value;
	$table_def = unserialize(stripslashes(htmlspecialchars_decode($table_def)));
	$update_array=array();
	foreach($table_def as $mysql_field)
	{
		if ($mysql_field['db_field']!=$primary_key)
		{
			if ($mysql_field['type'] == 'checkbox')
			{
				
				if(isset($_POST[$mysql_field['db_field']]) && $_POST[$mysql_field['db_field']] =='on')
				{
					$update_array[$mysql_field['db_field']] = 1;
				}
				else
				{
					$update_array[$mysql_field['db_field']] = 0;
				}
			
			}
			else
			{
				if(isset($_POST[$mysql_field['db_field']]))
				{
					$update_array[$mysql_field['db_field']] = scrubInput($_POST[$mysql_field['db_field']]);
				}
			}
		}
	}
	return $update_array;
}
function getArrayOfPostDataUsingTableDef($array_table_def)
{
//convert the posted data to an array that matches the table
//like this: array[$row]['column_post_data'] = $_POST['column_post_data_' .$row];
	for($row=0;$row<sizeof($array_table_def);$row++)
	{
		for($column=0;$column<sizeof($array_table_def[$row]);$column++)
		{
			if (isset($array_table_def[$row][$column]['mysql_post_field']) && $array_table_def[$row][$column]['mysql_post_field'] !='')
			{
				if(isset($_POST[$array_table_def[$row][$column]['mysql_post_field'] .'_' .$row]))
				{
					$table_data[$row][$array_table_def[$row][$column]['mysql_post_field']] = $_POST[$array_table_def[$row][$column]['mysql_post_field'] .'_' .$row];
				}
			}
		}
	}
	return $table_data;
}

function setToActive($table, $id_name, $id_value )
{
	$sql="UPDATE ".$table." SET active = 1 WHERE ".$id_name. " = ".$id_value;
	$id[$id_name] = $id_value;
	$mysql_data = array('active' => 1);
	$result = simpleUpdateSQL($table, $id, $mysql_data);
}

/***************************INVETORY***********************************************/


/****************************PurchaseOrders*************************************/
function updatePOReceivedStatus($dbc, $pos_purchase_order_id, $received_status)
{
	/* PO received status can be COMPLETE, INCOMPLETE, EXTRA ITEMS or DAMAGED ITEMS */
	$po_sql = "UPDATE pos_purchase_orders SET received_status = '" . $received_status ."'  WHERE pos_purchase_order_id=$pos_purchase_order_id LIMIT 1";
	$result = runTransactionSQL($dbc, $po_sql);
	return $result;
}



function getAndInsertMultiSelect($mysql_field, $table, $key_val_id)
{
	if (isset($_POST[$mysql_field]))
	{
		$counter = 0;
		for($i=0;$i<sizeof($_POST[$mysql_field]);$i++)
		{
			if ($_POST[$mysql_field] != 'false')
			{
				$mysql_data[$counter] = array(key($key_val_id) => $key_val_id[key($key_val_id)],
									$mysql_field => $_POST[$mysql_field][$i]);
				$counter++;
			}
		}
		$another_result = deleteThenInsertArray($table, $key_val_id, $mysql_data);
	}
}
function getAndTransactionInsertMultiSelect($dbc,$mysql_field, $table, $key_val_id)
{
	if (isset($_POST[$mysql_field]))
	{
		$counter = 0;
		for($i=0;$i<sizeof($_POST[$mysql_field]);$i++)
		{
			if ($_POST[$mysql_field] != 'false')
			{
				$mysql_data[$counter] = array(key($key_val_id) => $key_val_id[key($key_val_id)],
									$mysql_field => $_POST[$mysql_field][$i]);
				$counter++;
			}
		}
		$result = deleteThenInsertArray($table, $key_val_id, $mysql_data);
	}
	return $result;
}
function transactionGetAndInsertMultiSelect($dbc,$mysql_field, $table, $key_val_id)
{
	if (isset($_POST[$mysql_field]))
	{
		$counter = 0;
		for($i=0;$i<sizeof($_POST[$mysql_field]);$i++)
		{
			if ($_POST[$mysql_field] != 'false')
			{
				$mysql_data[$counter] = array(key($key_val_id) => $key_val_id[key($key_val_id)],
									$mysql_field => $_POST[$mysql_field][$i]);
				$counter++;
			}
		}
		$result = deleteThenTransactionInsertArray($dbc, $table, $key_val_id, $mysql_data);
	}
	return $result;
}
function getAndInsertSecondaryCategories($key_val_id)
{
	if (isset($_POST['pos_product_secondary_categories']))
	{
		$counter = 0;
		for($i=0;$i<sizeof($_POST['pos_product_secondary_categories']);$i++)
		{
			if ($_POST['pos_product_secondary_categories'] != 'false')
			{
				$mysql_data[$counter] = array(key($key_val_id) => $key_val_id[key($key_val_id)],
									'pos_category_id' => $_POST['pos_product_secondary_categories'][$i]);
				$counter++;
			}
		}
		$another_result = deleteThenInsertArray('pos_product_secondary_categories', $key_val_id, $mysql_data);
	}
}

function updateMysqlArray($dbc, $data_array, $db_table, $primary_key)
{
	/* data array is jus
	$data[i]['field'] = value
	*/
	/*	ex:
	$mysql_data_array[0]['db_field'] = 'cost';
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['data_array']['3789'] = 30.75;
	$mysql_data_array[0]['data_array']['3790'] = 40.75;
	$mysql_data_array[1]['db_field'] = 'retail';
	$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
	$mysql_data_array[1]['data_array']['3789'] = 60.75;
	$mysql_data_array[1]['data_array']['3790'] = 80.75;
	*/
	$counter = 0;
	foreach($data_array[0] as $key => $value)
	{
		if($key != $primary_key)
		{
			$mysql_data_array[$counter]['id'] = $primary_key;
			$mysql_data_array[$counter]['db_field'] = $key;
			for($i=0;$i<sizeof($data_array);$i++)
			{
				$mysql_data_array[$counter]['data_array'][$data_array[$i][$primary_key]] = $data_array[$i][$key];
			}
			$counter++;
		}
	}
	
	return runTransactionSQL($dbc, arrayUpdateSQLString($db_table, $mysql_data_array));
}

?>
