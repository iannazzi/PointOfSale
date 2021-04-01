<?php


function checkForValidIDinPOS($id, $table, $mysql_id_name)
{

	$dbc = openPOSDatabase();
	//Check to see that it is valid
	$validate_q = "SELECT * FROM " . $table . " WHERE ".  $mysql_id_name. " = '" .$id ."'";		
	$validate_r= @mysqli_query ($dbc, $validate_q);
	if (mysqli_num_rows($validate_r) == 1) 
	{
		return true;
	}
	else
	{

		return false;
	}
	//Close DB
	mysqli_close($dbc);
}


/*********************EMPLOYEEEEEEEEEEEEEEE****************************************/
function getEmployee($pos_employees)
{
	$dbc = openPOSDatabase();
	$employee_sql = "SELECT * FROM pos_employees WHERE pos_employees = '$pos_employees'";
	$employee_r = @mysqli_query ($dbc, $employee_sql);
	$employee =  convert_mysql_result_to_array($employee_r);
	mysqli_close($dbc);
	return $employee;
}
function getActiveEmployees($add_this_for_possibly_unactive_employee_id = 'off')
{
	$dbc = openPOSDatabase();
	if ($add_this_for_possibly_unactive_employee_id != 'off')
	{
		$employee_sql = "SELECT pos_employee_id, first_name, last_name FROM pos_employees WHERE active='1' OR
					pos_employee_id = '$add_this_for_possibly_unactive_employee_id'";
	}
	else
	{
		$employee_sql = "SELECT pos_employee_id, first_name, last_name FROM pos_employees WHERE active='1'";
	}
	$employee_r = @mysqli_query ($dbc, $employee_sql);
	$employees =  convert_mysql_result_to_array($employee_r);
	mysqli_close($dbc);
	return $employees;
}
function getActiveUsersV2()
{

		$sql = "SELECT pos_user_id, first_name, last_name FROM pos_users WHERE active='1'";
	
	return getSQL($sql);
}
function getActiveUsers($add_this_for_possibly_unactive_user_id = 'off')
{
	if ($add_this_for_possibly_unactive_user_id != 'off')
	{
		$sql = "SELECT pos_user_id, first_name, last_name FROM pos_users WHERE active='1' OR
					pos_user_id = '$add_this_for_possibly_unactive_user_id'";
	}
	else
	{
		$sql = "SELECT pos_user_id, first_name, last_name FROM pos_users WHERE active='1'";
	}
	return getSQL($sql);
}
function getAllEmployees()
{
	$dbc = openPOSDatabase();
	$employee_sql = "SELECT pos_employee_id, first_name, last_name FROM pos_employees";
	$employee_r = @mysqli_query ($dbc, $employee_sql);
	$employees =  convert_mysql_result_to_array($employee_r);
	mysqli_close($dbc);
	return $employees;
}
function getEmployeeEmail($pos_employee_id)
{
	$emp = getEmployee($pos_employee_id);
	$emp_email = $emp[0]['email'];
	return $emp_email;
}

function getEmplyeeFullName($pos_employee_id)
{
	$emp = getEmployee($pos_employee_id);
	$emp_name = $emp[0]['first_name'] . ' ' . $emp[0]['last_name'];
	return $emp_name;
}

/****************************PAYMENT METHODS***************************************/


function getSpecificAccounts($account_type)
{
	// types are cc cash checking vendor expense
	$sql = "SELECT * FROM pos_accounts WHERE active ='1' AND account_type='$account_type'";
	return getSQL($sql);
}
function getPaymentMethods()
{
	$dbc = openPOSDatabase();
    $payment_category_sql = "SELECT * FROM pos_expense_payment_method WHERE active = 'Yes' ORDER BY priority ASC";
	$payment_category_r = @mysqli_query($dbc, $payment_category_sql);
	$payment_methods =  convert_mysql_result_to_array($payment_category_r);
	mysqli_close($dbc);
	return $payment_methods;
}
/*****************************EXPENSE METHODS**************************************/
function getExpenseCategories()
{
	$dbc2 = openPOSDatabase();
	$expense_categories = array();
	$operating_expense_category_sql = "SELECT pos_expense_category_id, name, caption, priority FROM pos_expense_categories WHERE active = 'Yes' ORDER BY priority";
	$operating_expense_category_result= my_sql_query($dbc2, $operating_expense_category_sql);
	$expense_categories = convert_mysql_result_to_array($operating_expense_category_result);
	mysqli_close($dbc2);
	return $expense_categories;
}

/****************************************************PRODUCTS**********************************/


/**********************************PURCHASE ORDER **********************************/
function getPurchaseOrderOverview($pos_purchase_order_id)
{
	$dbc = openPOSDatabase();
	$pos_purchase_order_q = "SELECT pos_manufacturer_id, pos_manufacturer_brand_id, pos_user_id, pos_store_id, po_title,purchase_order_number,manufacturer_purchase_order_number,delivery_date,cancel_date, pos_category_id FROM pos_purchase_orders WHERE pos_purchase_order_id='$pos_purchase_order_id'";		
	$pos_purchase_order_r = @mysqli_query ($dbc, $pos_purchase_order_q);
	$purchase_order_overview =  convert_mysql_result_to_array($pos_purchase_order_r);
	mysqli_close($dbc);
	return $purchase_order_overview;
}
function getPurchaseOrderStatus($pos_purchase_order_id)
{
	$sql = "SELECT purchase_order_status FROM pos_purchase_orders WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	$status = getSQL($sql);
	return $status[0]['purchase_order_status'];
}
function getPurchaseOrderOrderedStatus($pos_purchase_order_id)
{
	$sql = "SELECT ordered_status FROM pos_purchase_orders WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	$status = getSQL($sql);
	return $status[0]['ordered_status'];
}
function getPurchaseOrderReceivedStatus($pos_purchase_order_id)
{
	$sql = "SELECT received_status FROM pos_purchase_orders WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	$status = getSQL($sql);
	return $status[0]['received_status'];
}




/************************Third party feeeds**************************************/

function multiselectDataArrayFromMysqlTable($dbTable,$id, $table_def)
{
	$sql = "SELECT " . $table_def['db_field'] .' FROM ' . $dbTable . " WHERE " .key($id) ." ='" . $id[key($id)] ."'";
	$data = getSQL($sql);
	$table_def['value'] = $data;
	return $table_def; 
}
function selectSingleTableDataFromID($dbTable, $key_val_id, $table_def)
{
	//Version 2 has some added checks in case there is no db Field specified.
	$db_field_array = array();
	$counter = 0;
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if(isset($table_def[$i]['db_field']) && $table_def[$i]['db_field'] !='')
		{
			
				$db_field_array[$counter] = $table_def[$i]['db_field'];
			
			
			$counter++;
		}
	}
	$str_fields = implode(',', $db_field_array);
	$sql = "SELECT " . $str_fields . " FROM " . $dbTable . " WHERE " .key($key_val_id) ." ='" . $key_val_id[key($key_val_id)] . "' LIMIT 1";
	$data_array = getSQL($sql);
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if(isset($table_def[$i]['db_field']) && $table_def[$i]['db_field'] !='')
		{
			if(isset($table_def[$i]['encrypted']))
			{
				$table_def[$i]['value'] = craigsDecryption($data_array[0][$table_def[$i]['db_field']],0);
			}
			else
			{
				$table_def[$i]['value'] = $data_array[0][$table_def[$i]['db_field']];
			}
		}
	}
	return $table_def;
}
function getmySQLTableData($dbTable, $key_val_id, $table_def)
{
	$db_field_array = array();
	$counter = 0;
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if(isset($table_def[$i]['db_field']) && $table_def[$i]['db_field'] !='')
		{
			
				$db_field_array[$counter] = $table_def[$i]['db_field'];
			
			
			$counter++;
		}
	}
	$str_fields = implode(',', $db_field_array);
	$sql = "SELECT " . $str_fields . " FROM " . $dbTable . " WHERE " .key($key_val_id) ." ='" . $key_val_id[key($key_val_id)] . "' LIMIT 1";
	$data_array = getSQL($sql);
	if(sizeof($data_array) == 1)
	{
		return $data_array[0];
	}
	else if(sizeof($data_array) == 0)
	{
		return array();
	}
	else
	{
	
		trigger_error('check it out, your function getmySQLTableData missed the table.....');
	}
}
function selectSingleTableDataFromIDOld($dbTable, $key_val_id, $table_def)
{
	$db_field_array = array();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$db_field_array[$i] = $table_def[$i]['db_field'];
	}
	$str_fields = implode(',', $db_field_array);
	$sql = "SELECT " . $str_fields . " FROM " . $dbTable . " WHERE " .key($key_val_id) ." ='" . $key_val_id[key($key_val_id)] . "' LIMIT 1";
	$data_array = getSQL($sql);
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$table_def[$i]['value'] = $data_array[0][$table_def[$i]['db_field']];
	}
	return $table_def;
}
function loadMYSQLResultsIntoTableDefinition($sql_statement, $table_def)
{
	$data = getSQL($sql_statement);
	for($i=0;$i<sizeof($data);$i++)
	{
		$table_def_new[$i] = $table_def;
		for($j=0;$j<sizeof($table_def);$j++)
		{
			$table_def_new[$i][$j]['value'] = $data[$i][$table_def_new[$i][$j]['mysql_field']];
		}
	}
	if (isset($table_def_new))
	{
		return $table_def_new;
	}
	else
	{
		return false;
	}
	
}
function loadDataToTableDef($table_def, $db_table, $key_val_id)
{
	//this function breaks the table def up to process individual tables
	for ($i=0;$i<sizeof($table_def);$i++)
	{
		//$table_def[$i][0] is either an array of hirzontal table defs  or a table def
		if (isset($table_def[$i][0]['db_field']))
		{
			$table_def[$i] = selectSingleTableDataFromID($db_table, $key_val_id, $table_def[$i]);
		}
		else
		{
			$horizontal_html_table = array();
			for ($j=0;$j<sizeof($table_def[$i]);$j++)
			{
				//this is a horizontal table array
					
					$table_def[$i][$j] = selectSingleTableDataFromID($db_table, $key_val_id, $table_def[$i][$j]);
				
			}
		}
	}
	return $table_def;
}
function loadMYSQLArrayIntoTableArray($array_table_def, $data)
{
	
	/*table def looks like:
	$array_table_def= array(	
					array(	'th' => 'POC ID',
			 				'type' => 'input',
			 				'tags' => ' readonly = "readonly" ',
							'mysql_result_field' => 'pos_purchase_order_content_id',
							'mysql_post_field' => 'pos_purchase_order_content_id'),
							
	$data is an array and comes from: $data =  getSQL($sql_statement);
	
	
	
	*/
	//this function breaks the table def up to process individual tables
	if (sizeof($data)>0)
	{
		for ($i=0;$i<sizeof($data);$i++)
		{
			$array_table_def_with_data[$i] = $array_table_def;
			for($j=0;$j<sizeof($array_table_def_with_data[$i]);$j++)
			{
				if (isset($array_table_def_with_data[$i][$j]['mysql_result_field']) && $array_table_def_with_data[$i][$j]['mysql_result_field'] !='')
				{
					$array_table_def_with_data[$i][$j]['value'] = $data[$i][$array_table_def_with_data[$i][$j]['mysql_result_field']];
				}
	
			}
		}
	}
	else
	{
		$array_table_def_with_data = array();
	}
	return $array_table_def_with_data;
}

?>