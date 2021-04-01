<?php
//craig iannazzi 11-23-12
//this will associate user accounts with groups. Groups will provide higher level privledges....

$binder_name = 'System User Accounts';

require_once ('../user_functions.php');
$pos_user_id = getPostOrGetID('pos_user_id');



//get the type

if (isset($_POST['submit'])) 
{
		$dbc = startTransaction();
	$delete_sql = runTransactionSQL($dbc,"DELETE FROM pos_users_in_groups WHERE pos_user_id = $pos_user_id");
	if (isset($_POST['user_group_data_object'])) 
	{
		$table = json_decode(stripslashes($_POST['user_group_data_object']) , true);
		for($i = 0; $i<sizeof($table['row_number']);$i++)
		{
			$SQL = runTransactionSQL($dbc,"INSERT INTO pos_users_in_groups (pos_user_id, pos_user_group_id) VALUES ($pos_user_id, ".$table['pos_user_group_id'][$i] .")");
		}

	}
	simpleCommitTransaction($dbc);
	header('Location: manage_user.php?type=VIEW&pos_user_id='.$pos_user_id);
}
elseif(isset($_POST['cancel']))
{
	header('Location: manage_user.php?type=VIEW&pos_user_id='.$pos_user_id);
}
else
{
	if(isset($_GET['type']))
{
	$type = $_GET['type'];
}
else
{
	trigger_error('missing type');
}
	if(strtoupper($type) == 'EDIT')
	{
		$javascript = 'user_groups.2014.11.26.js';
	$form_handler = 'user_groups.php';
	$form_id = 'user_groups';
	$html = '';
	
	$html .=  '<script src="'.$javascript.'"></script>'.newline();

	$html .=  '<form id = "' . $form_id . '" action="'.$form_handler.'" method="post" onsubmit="return validateUserGroupForm()">';
		
		$html .= createHiddenInput('pos_user_id', $pos_user_id);
		$user_group_contents = getSQL("SELECT pos_user_group_id, pos_user_id FROM pos_users_in_groups
								LEFT JOIN pos_user_groups USING (pos_user_group_id) WHERE pos_user_id = $pos_user_id");
		$user_group_table_name = 'user_group';
		$user_group_buttons = array(
							
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:60px;",
								'value'=>"Add Row", 
								'onclick' => $user_group_table_name.'_object.addRow();document.getElementById(\'barcode\').focus();'
								),
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:60px;",
								'value'=>"Delete Row", 
								'onclick' => $user_group_table_name.'_object.deleteRow();document.getElementById(\'barcode\').focus();'
								),
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:80px;",
								'value'=>"Delete All Rows", 
								'onclick' => $user_group_table_name.'_object.deleteAllRows();;document.getElementById(\'barcode\').focus();'
								)
								);
		$html .= '<p>Add/Edit Group Access for User ' . getUserFullName($pos_user_id) . '</p>';
		$html .= createDynamicTableReuseV2( $user_group_table_name, createUserGroupTableDef($user_group_table_name), $user_group_contents, $form_id, ' class="dynamic_contents_table"  ', $user_group_buttons);

$html .= '<input class = "button" name = "submit" type="submit" value="Submit"/>';
	$html .= '<input class = "button" name = "cancel" type="submit" value="Cancel"/>';
	
	
	
		$html .=  '</form>';
		
		include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
	}
	else
	{
	}
}
function createUserGroupTableDef($table_object_name)
{
	$table_object_name = $table_object_name . '_object';

$user_groups = getFieldRowSql("SELECT pos_user_group_id, group_name FROM pos_user_groups WHERE active = 1 ");
	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						//'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}')
												),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),

					array('caption' => 'User Group',
					'db_field' => 'pos_user_group_id',
					'type' => 'select',
					'unique_select_options' => true,
					//this part is for the 'view'
					//'html' => createCategorySelect('pos_user_group_id', 'false'),
					'select_names' => $user_groups['group_name'],
					'select_values' => $user_groups['pos_user_group_id'],
					'properties' => array(	'style.width' => '"50em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);'.$table_object_name.'.copyHTMLTableDataToObject();}',
											
											//'onkeyup' => 'function(){updateDiscount(this);}',
											//'onmouseup' => 'function(){updateDiscount(this);}'
											)
											),
						
					

				
					
	

				
					
				);		
						
		return $columns;
	
	
	
}
?>
