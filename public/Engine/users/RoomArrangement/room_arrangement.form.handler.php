<?php
$binder_name = 'User Account Settings';
require_once('../user_functions.php');
$pos_user_id = getPostOrGetID('pos_user_id');
checkSystemUserAccess($pos_user_id);
$tbody_def = $_POST['tbody_def'];
$table_data_array = (isset($_POST['table_data_array'])) ? $_POST['table_data_array'] : array();
$original_room_name = $_POST['original_room_name'];
$room_name = scrubInput($_POST['room_name']);

$room_priority = $_POST['room_priority'];
$dbc = startTransaction();
$delet_q = "DELETE FROM pos_room_arrangements WHERE room_name = '$original_room_name' AND pos_user_id = $pos_user_id";
$results[] = runTransactionSQL($dbc, $delet_q);
// add the priority

if(sizeof($table_data_array)>0)
{
	$sql = array(); 
	//add these in to the front
	$db_fields  = array('pos_user_id','room_name','room_priority', 'type','pos_binder_id','source','priority');
	$str_fields = implode(',', $db_fields);
	$priority = sizeof($table_data_array);
	for($row=0;$row<sizeof($table_data_array);$row++) 
	{	
			//check
			//row
			//binder
			$type = ($table_data_array[$row][2] == 'DIVIDER') ? 'DIVIDER': 'BINDER';
			if($type == 'BINDER')
			{
				$pos = strpos($table_data_array[$row][2], 'pos_binders');
				if($pos !== 'false')
				{
					$pos_binder_id = str_replace('pos_binders::','',$table_data_array[$row][2]);
					$source = 'pos_binders';
				}
				else
				{
					$pos_binder_id = str_replace('pos_custom_binders::','',$table_data_array[$row][2]);
					$source = 'pos_custom_binders';
				}
			}
			else
			{
				$source = 'DIVIDER';
				$pos_binder_id = 0;
			}
			$row_counter = 0;
			$row_array = array();
			$row_array[$row_counter] = $pos_user_id;
			$row_counter++;
			$row_array[$row_counter] = "'". $room_name. "'";
			$row_counter++;
			$row_array[$row_counter] = "'". $room_priority. "'";
			$row_counter++;
			$row_array[$row_counter] = "'". $type . "'";
			$row_counter++;
			$row_array[$row_counter] = "'" . $pos_binder_id . "'";
			$row_counter++;
			$row_array[$row_counter] = "'". $source ."'";
			$row_counter++;
			$row_array[$row_counter] = $priority;
			$row_counter++;
			$priority--;
	
			
			$row_string =  implode(',',$row_array);
			$sql[] = '(' . $row_string .')';
	}
	$insert_sql = "INSERT INTO pos_room_arrangements (" . $str_fields . ") VALUES  " . implode(',', $sql);
	$results[] = runTransactionSQL($dbc, $insert_sql);
}	
simpleCommitTransaction($dbc);
echo 'STORED' .newline();

?>