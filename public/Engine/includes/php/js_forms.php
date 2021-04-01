<?php
//************************ Get data from mysql via jcscipt search table and the coreesponding table def
//works in combination with dynamic_table_objectv3

function grabType()
{
	if(isset($_POST['type']))
	{
		$type = $_POST['type'];
	}
	elseif (isset($_GET['type']))
	{
		$type =  scrubInput($_GET['type']);
	}
	else
	{
		$type = 'view';
	}
	return $type;
}

function checkIfSearchIsSet($table_name, $table_def, $saved_session_search_name)
{
	$search_set = false;
	for($i=0;$i<sizeof($table_def);$i++)
	{	
		if(isset($table_def[$i]['search']))
		{
			$type = $table_def[$i]['type'];
			$db_field = $table_def[$i]['db_field'];
			if($type =='date')
			{
				if(isset($_GET[$table_name . '_' . $db_field. '_date_start_search']))
				{
					$search_set = true;
				}
				else if(isset($_POST[$table_name . '_' . $db_field. '_date_start_search']))
				{
					$search_set = true;	
				}
				elseif(isset($_SESSION[$saved_session_search_name][$table_name . '_' . $db_field .'_date_start_search']))

				{
					$search_set = true;	
				}
				if(isset($_GET[$table_name . '_' . $db_field. '_date_end_search']))
				{
					$search_set = true;	
				}
				if(isset($_POST[$table_name . '_' . $db_field. '_date_end_search']))
				{
					$search_set = true;	
				}
				elseif(isset($_SESSION[$saved_session_search_name][$table_name . '_' . $db_field . '_date_end_search']))
				{
					$search_set = true;	
				}
			}
			else
			{	
				if(isset($_GET[$table_name . '_' . $db_field . '_search'] ))
				{
					$search_set = true;	
				}
				if(isset($_POST[$table_name . '_' . $db_field. '_search']))
				{
					$search_set = true;	
				}
				elseif(isset($_SESSION[$saved_session_search_name][$table_name . '_' . $db_field. '_search']))
				{
					$search_set = true;	

				}
				else
				{	
				}
			}
		}
	}
	return 	$search_set;
}
function getSearchCriteriaFromGetPostSessionData($table_name, $table_def, $saved_search)
{	
	/*
	
	This is going to look first at the get, then post, then session to find values matching the db_field in the table def
	
	It will then create the search limit string for the sql statement.
	
	We then need to save the get and post values to the session....
	
	
	*/
	
	//if there is a get or post set then erase the session
	$get_set = false;
	$post_set=false;
	$session_set = false;
	$return_id_value = array();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if(isset($table_def[$i]['search']))
		{
			$type = $table_def[$i]['type'];
			$db_field = $table_def[$i]['db_field'];
			$search_type = $table_def[$i]['search'];
			if($type =='date')
			{				
				if(isset($_GET[$table_name . '_' . $db_field. '_date_start_search']))
				{
					$get_set = true;					
				}
				else if(isset($_POST[$table_name . '_' .$db_field. '_date_start_search']))
				{
					$post_set = true;					
				}
				elseif(isset($_SESSION[$saved_search][$table_name . '_' . $db_field .'_date_start_search']))
				{
					$session_set=true;
				}
				if(isset($_GET[$table_name . '_' . $db_field. '_date_end_search']))
				{
					$get_set = true;
				}
				else if(isset( $_POST[$table_name . '_' .$db_field. '_date_end_search']))
				{
					$post_set = true;
				}
				elseif(isset($_SESSION[$saved_search][$table_name . '_' . $db_field . '_date_end_search']))
				{
					$session_set=true;
				}		
			}
			else
			{	
				
				if(isset($_GET[$table_name . '_' . $db_field . '_search']))
				{
					$get_set = true;
				}
				elseif(isset($_POST[$table_name . '_' .$db_field. '_search']))
				{
					$post_set = true;
				}
				elseif(isset($_SESSION[$saved_search][$table_name . '_' .$db_field. '_search']))
				{
					$session_set=true;
				}
				
			}
		}
	}
	
	if($get_set || $post_set)
	{
		unset($_SESSION[$saved_search]);
	}
	
	$search_add = [];
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if(isset($table_def[$i]['search']))
		{
			$type = $table_def[$i]['type'];
			$db_field = $table_def[$i]['db_field'];
			$search_type = $table_def[$i]['search'];
			
			if($type =='date')
			{				
				$data = [];
				if(isset($_GET[$table_name . '_' .$db_field. '_date_start_search']))
				{
					$id = $table_name . '_' .$db_field. '_date_start_search';
					$value = scrubInput($_GET[$id]);
					$data['start_date'] = $value;
					$return_id_value[$id] = $value;
					$_SESSION[$saved_search][$id]=$value;
					
				}
				else if(isset($_POST[$table_name . '_' .$db_field. '_date_start_search']))
				{
					$id = $table_name . '_' .$db_field. '_date_start_search';
					$value = scrubInput($_POST[$id]);
					$data['start_date'] = $value;
					$return_id_value[$id] = $value;
					$_SESSION[$saved_search][$id]=$value;
				
					
				}
				elseif(isset($_SESSION[$saved_search][$table_name . '_' .$db_field .'_date_start_search']))
				{
					$id = $table_name . '_' .$db_field. '_date_start_search';
					$value = scrubInput($_SESSION[$saved_search][$id]);
					$data['start_date'] = $value;
					$return_id_value[$id] = $value;
				}
				if(isset($_GET[$table_name . '_' .$db_field. '_date_end_search']))
				{
					$id = $table_name . '_' .$db_field. '_date_end_search';
					$value = scrubInput($_GET[$id]);
					$data['end_date'] = $value;
					$return_id_value[$id] = $value;
					$_SESSION[$saved_search][$id]=$value;
				}
				else if(isset($_POST[$table_name . '_' .$db_field. '_date_end_search']))
				{
					$id = $table_name . '_' .$db_field. '_date_end_search';
					$value = scrubInput($_POST[$id]);
					$data['end_date'] = $value;
					$return_id_value[$id] = $value;
					$_SESSION[$saved_search][$id]=$value;
				}
				elseif(isset($_SESSION[$saved_search][$table_name . '_' .$db_field . '_date_end_search']))
				{
					$id = $table_name . '_' .$db_field. '_date_end_search';
					$value = scrubInput($_SESSION[$saved_search][$id]);
					$data['end_date'] = $value;
					$return_id_value[$id] = $value;
				}

				//for between i assum it is only a date.... so the db_fields will be db_field_start_date and db_field_end_date
		
				if (isset($data['start_date']) && isset($data['end_date']) && $data['end_date'] != '')
				{
					$search_add[] =  $db_field ." BETWEEN '" . $data['start_date'] ."' AND '" .$data['end_date']. "' ";
				}
				else if (isset($data['start_date']) && isset($data['end_date']) && $data['end_date'] == '')
				{
					$search_add[] = $db_field." >= '" . $data['start_date'] ."' ";
				}
				else if(isset($data['start_date']) && !isset($data['end_date']))
				{
					$search_add[] = $db_field." >= '" . $data['start_date'] ."' ";
				}
				else
				{

				}				
			}
			else
			{	
				
				if(isset($_GET[$table_name . '_' . $db_field . '_search']))
				{
					//data has come in through the url... use that first
					$id = $table_name . '_' . $db_field. '_search';
					$value = scrubInput($_GET[$id]);
					$data = $value;
					$return_id_value[$id] = $value;
					$_SESSION[$saved_search][$id]=$value;
				}
				elseif(isset($_POST[$table_name . '_' . $db_field. '_search']))
				{
					$id = $table_name . '_' . $db_field. '_search';
					$value = scrubInput($_POST[$id]);
					$data = $value;
					$return_id_value[$id] = $value;	
					$_SESSION[$saved_search][$id]=$value;
				}
				elseif(isset($_SESSION[$saved_search][$table_name . '_' . $db_field. '_search']))
				{
					//data has been stored in the session, use that next
					$id = $table_name . '_' . $db_field. '_search';
					$value = scrubInput($_SESSION[$saved_search][$id]);
					$data = $value;
					$return_id_value[$id] = $value;
				}
				else
				{	
					//there is no data
					$data = '';
				}
				if($data != '')
				{
					$search_type = $table_def[$i]['search'];
					if($search_type =='LIKE')
					{
						$search_add[] = $db_field . " LIKE '%" . $data . "%' ";
					}
					elseif($search_type =='EXACT')
					{
						$search_add[] = $db_field . " = '" . $data . "' ";
					}
					elseif($search_type =='BETWEEN')
					{
						//$search_add = '';
						trigger_error('not using between....');
					}
					elseif($search_type =='ANY')
					{
						$search_array = explode(' ', $data);
						for($si=0;$si<sizeof($search_array);$si++)
						{
							$search_add[] = $db_field. " LIKE '%". scrubInput($search_array[$si]) ."%' ";
		
						}

					}
					else
					{
						//$search_add = '';
						trigger_error('missing search type in function addMysqlSearchCondition');
					}
					
				}
			}
			
		}
	}
	$search_add_string = '';
	if(sizeof($search_add)>0)
	{
		$search_add_string = 'WHERE ' . implode(' AND ' ,$search_add);
	}

	
	//need to return both the sql string and the paramenters javascript needs
	// javascript needs id and value...
	$return_array = array();
	$return_array['search_add_string'] = $search_add_string;
	$return_array['get_post_session_params'] = $return_id_value;
	return $return_array;
}
function eraseSessionSavedSearch($table_def, $saved_search)
{
	unset($_SESSION[$saved_search]);
	/*
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if(isset($table_def[$i]['search']))
		{
			$type = $table_def[$i]['type'];
			$db_field = $table_def[$i]['db_field'];
			$search_type = $table_def[$i]['search'];
			
			if($type =='date')
			{

				unset( $_SESSION[$saved_search][$db_field .'_start_date']);
				unset($_SESSION[$saved_search][$db_field . '_end_date']);
				
			}
			else
			{	
				unset($_SESSION[$saved_search][$db_field]);
			}
		}
	}
	*/
}
function SCRUBmySQLTablePOST($table_def,$table_name)
{
	//make an insert array like this:
	//$update_data[$db_field] = $value;
	$insert_array=array();
	foreach($table_def as $mysql_field)
	{
		$insert_array[$mysql_field['db_field']] = scrubInput($_POST[$table_name  .'_' . $mysql_field['db_field']]);
	}
	return $insert_array;
}


?>