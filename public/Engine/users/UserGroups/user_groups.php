<?php
/*
	Craig Iannazzi 2-8-2013 on a snowy day at midtown
	
	//eventually I would like to link the discount to products, or categories, or manufacturers
	// so there would be a discount_id to category, manufacturer, product id lookup table... a dynamic table
*/


require_once ('../user_functions.php');
if(isset($_GET['type']))
{
	$type = $_GET['type'];
}
elseif(isset($_POST['type']))
{
	$type = $_POST['type'];
}
else
{
	trigger_error('missing type');
}
$page_title = 'User Groups';
$binder_name = 'User Groups';
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';


$complete_location = 'list_user_groups.php';
$cancel_location = 'list_user_groups.php?message=Canceled';

if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_user_group_id']);
	//if it is new then insert, otherwise update.
	//create the barcode:
	
	
	if($_POST['pos_user_group_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_user_group_id = simpleTransactionInsertSQLReturnID($dbc,'pos_user_groups', $insert);
		$key_val_id['pos_user_group_id'] = $pos_user_group_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_user_groups', $key_val_id, $insert);
		$message = urlencode('User Group Id ' . $pos_user_group_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_user_group_id = getPostOrGetID('pos_user_group_id');
		$key_val_id['pos_user_group_id'] = $pos_user_group_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_user_groups', $key_val_id, $insert);
		$message = urlencode('User Group Id ' . $pos_user_group_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: user_groups.php?type=view&pos_user_group_id='.$pos_user_group_id .'&message=' . $message);
}
elseif (isset($_POST['cancel']))
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}
else
{
	if(strtoupper($type) == 'ADD')
	{
		$pos_user_group_id = 'TBD';
		$header = '<p>Add User Group</p>';
		$page_title = 'Add User Group';
		$data_table_def = createUserGroupTableDef($type, $pos_user_group_id);
	}
	elseif (strtoupper($type) == 'EDIT')
	{
		$pos_user_group_id = getPostOrGetID('pos_user_group_id');
		$header = '<p>EDIT User Group</p>';
		$page_title = 'Edit User Group';
		$data_table_def_no_data = createUserGroupTableDef($type, $pos_user_group_id);	
		$db_table = 'pos_user_groups';
		$key_val_id['pos_user_group_id'] = $pos_user_group_id;
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
	}
	elseif (strtoupper($type) == 'VIEW')
	{
		$pos_user_group_id = getPostOrGetID('pos_user_group_id');
		$edit_location = 'user_groups.php?pos_user_group_id='.$pos_user_group_id.'&type=edit';
		//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
		$db_table = 'pos_user_groups';
		$key_val_id['pos_user_group_id']  = $pos_user_group_id;
		$data_table_def = createUserGroupTableDef($type, $pos_user_group_id);
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
	}
	else
	{
	}

	//build the html page
	if (strtoupper($type) == 'VIEW')
	{
		$html = printGetMessage('message');
		$html .= '<p>View User Group</p>';
		//$html .= confirmDelete($delete_location);
		$html .= createHTMLTableForMYSQLData($data_table_def);
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
		$html .= '<p>';
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To GROUPS" onclick="window.location = \''.$complete_location.'\'" />';
		$html .= '</p>';
	}
	else
	{
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
	
		$html = $header;
		$form_handler = 'user_groups.php';
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
		$html .= '<script>document.getElementsByName("group_name")[0].focus();</script>';
	}


	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}

function createUserGroupTableDef($type, $pos_user_group_id)
{
	if ($pos_user_group_id =='TBD')
	{
		$unique_validate = array('unique' => 'group_name', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_user_group_id'] = $pos_user_group_id;
		$unique_validate = array('unique' => 'group_name', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_user_group_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Service ID',
								'value' => $pos_user_group_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'group_name',
								'type' => 'input',
								'db_table' => 'pos_user_groups',
								'caption' => 'Group Name',
								'validate' => $unique_validate),	
						
						array('db_field' =>  'pos_max_discount_percent',
								'type' => 'input',
								'caption' => 'Point Of Sale Max Item Discount',
								'value' => '100',
								'verify' => 'number'),	
						array('db_field' =>  'pos_edit_closed_customer',
								'type' => 'checkbox',
								'caption' => 'Point Of Sale Allow Edit Closed Customer',
								'value' => '1'),
						array('db_field' =>  'pos_edit_closed_contents',
								'type' => 'checkbox',
								'caption' => 'Point Of Sale Allow Edit Closed Contents',
								'value' => '0'),
						array('db_field' =>  'pos_edit_closed_payments',
								'type' => 'checkbox',
								'caption' => 'Point Of Sale Allow Edit Closed Payments',
								'value' => '0'),
						array('db_field' =>  'pos_allow_other_payment',
								'type' => 'checkbox',
								'caption' => 'Point Of Sale Allow "Other" Payment - Typically Used for assigning Charitable gift cards value',
								'value' => '0'),
						
						array('db_field' =>  'pos_allow_voids',
								'type' => 'checkbox',
								'caption' => 'Point Of Sale Allow Voids',
								'value' => '1'),
						array('db_field' =>  'pos_allow_refunds',
								'type' => 'checkbox',
								'caption' => 'Point Of Sale Allow Refunds',
								'value' => '1'),
						array('db_field' =>  'pos_allow_cc_return',
								'type' => 'checkbox',
								'caption' => 'Point Of Sale Allow "Credit Card" Refund - Check to allow group to issue credit card refund',
								'value' => '0'),
						array('db_field' =>  'pos_allow_advanced_return',
								'type' => 'checkbox',
								'caption' => 'Point Of Sale Allow "Advanced" Refund - Check to allow cash or check return',
								'value' => '0'),
						array('db_field' =>  'po_max_open_past_cancel',
								'type' => 'input',
								'caption' => 'PO Max Open POs Past Cancel',
								'value' => '20',
								'validate' => 'number'),
						array('db_field' =>  'po_max_received_not_invoiced',
								'type' => 'input',
								'caption' => 'PO Max Pos Received Complete No Invoice',
								'value' => '20',
								'validate' => 'number'),				
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	

}
?>