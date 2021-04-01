<?
$binder_name = 'System User Accounts';
$page_title = 'Default Room Arrangement';
require_once('../user_functions.php');
$pos_user_id = getPostOrGetID('pos_user_id');
$complete_location = '../UserAccountSettings/user_settings.php?type=view&pos_user_id='.$pos_user_id;
$cancel_location = $complete_location;


	//the order:
	//if the binders are in a collection then they are ordered by the collection
	
	//collection 1 - highest priority
	//binder name - highest to lowest priority
	//binder name
	
	//get the collections ordered by priority
	// parent binder?
	//$sql = "SELECT * FROM pos_binder_collections ORDER BY priority DESC";
	//$binder_collections = getSQL($sql);
	$sql = "SELECT * from pos_binders WHERE Enabled=1 ORDER BY binder_name DESC ";
	$binders = getSQL($sql);
	

	
	//the default rooms and default binder collections are part of the binder db_table

	$default_rooms = array('TheStore','TheBackRoom','TheOffice', 'TheSystem');
	$priority = 10;
	$db_fields  = array('pos_user_id','room_name','room_priority', 'type','pos_binder_id','source','priority');
	$str_fields = implode(',', $db_fields);
	$sql_row = array();
	
	for($row=0;$row<sizeof($default_rooms);$row++) 
	{	
		$element_counter = 0;
		$div_check = true;
		for($b=0;$b<sizeof($binders);$b++)
		{
			//is it in the room?
			$tmp_rooms = explode(',',$binders[$b]['default_rooms']);
			$tmp_rooms = array_filter(array_map('trim', $tmp_rooms));
			//doest he user have access to the binder?
			
			
			if(checkUserBinderAccess($pos_user_id, $binders[$b]['pos_binder_id']) != false)
			{
				if(in_array($default_rooms[$row], $tmp_rooms) )
				{
					/*if($div_init)
					{
						$div_check = $binders[$b]['binder_collection_ids'];
						$div_init = false;
					}
					//if the binder collection changed then put in a divider
					if( $binders[$b]['binder_collection_ids'] !=$div_check)
					{
						
						//insert a divider into the room
						$type = 'DIVIDER';
						$source = 'DIVIDER';
						$row_counter = 0;
						$row_array = array();
						$row_array[$row_counter] = $pos_user_id;
						$row_counter++;
						$row_array[$row_counter] = "'". $default_rooms[$row]. "'";
						$row_counter++;
						$row_array[$row_counter] = "'". $row. "'";
						$row_counter++;
						$row_array[$row_counter] = "'". $type . "'";
						$row_counter++;
						$row_array[$row_counter] = "'" . 0 . "'";
						$row_counter++;
						$row_array[$row_counter] = "'". $source ."'";
						$row_counter++;
						$row_array[$row_counter] = $element_counter;
						$row_counter++;
						$element_counter++;
				
						
						$row_string =  implode(',',$row_array);
						$sql[] = '(' . $row_string .')';
						$div_check = $binders[$b]['binder_collection_ids'];
					}*/
					
					//insert the binder into the room
					$type = 'BINDER';
					$source = 'pos_binders';
					$row_counter = 0;
					$row_array = array();
					$row_array[$row_counter] = $pos_user_id;
					$row_counter++;
					$row_array[$row_counter] = "'". $default_rooms[$row]. "'";
					$row_counter++;
					$row_array[$row_counter] = "'". $priority. "'";
					$row_counter++;
					$row_array[$row_counter] = "'". $type . "'";
					$row_counter++;
					$row_array[$row_counter] = "'" . $binders[$b]['pos_binder_id'] . "'";
					$row_counter++;
					$row_array[$row_counter] = "'". $source ."'";
					$row_counter++;
					$row_array[$row_counter] = $element_counter;
					$row_counter++;
					$element_counter++;
					
			
					
					$row_string =  implode(',',$row_array);
					$sql_row[] = '(' . $row_string .')';
	
					
				}
			}

			
		}
		$priority--;
	}
	if(sizeof( $sql_row) == 0)
	{
		$html = 'No binders are available for this user: Please edit user access to binders before creating rooms';
		$html .= listUserBinderAccess($pos_user_id);	
		include (HEADER_FILE);		
		echo $html;
		include (FOOTER_FILE);
	}
	else
	{
		$dbc = startTransaction();
		//clear current contets
		$delte_sql = "DELETE FROM pos_room_arrangements WHERE pos_user_id = $pos_user_id";
		$results[] = runTransactionSQL($dbc, $delte_sql);
		$insert_sql = "INSERT INTO pos_room_arrangements (" . $str_fields . ") VALUES  " . implode(',', $sql_row);
		$results[] = runTransactionSQL($dbc, $insert_sql);
		simpleCommitTransaction($dbc);
		header('Location: '.'../ManageUserAccounts/manage_user.php?type=view&pos_user_id='.$pos_user_id);	
	}

		

	
	


?>