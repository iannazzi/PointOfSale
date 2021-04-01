<?php
$binder_name = 'User Account Settings';
$page_title = 'Arrange Binders In A Room';
require_once('../user_functions.php');
$pos_user_id = getPostOrGetID('pos_user_id');
checkSystemUserAccess($pos_user_id);
$key_val_id['pos_user_id'] = $pos_user_id;
$type = getPostOrGetValue('type');
$complete_location = '../UserAccountSettings/user_settings.php?type=view&pos_user_id='.$pos_user_id;
$cancel_location = $complete_location;


if(checkForValidIDinPOS($pos_user_id, 'pos_users', 'pos_user_id'))
{
	if (strtoupper($type) =='EDIT')
	{
		$room_name = getPostOrGetValue('room_name');
	}
	elseif (strtoupper($type) =='ADD')
	{
		$room_name = 'TBD';

	}
	elseif (strtoupper($type) =='VIEW')
	{
		$room_name = getPostOrGetValue('room_name');
	}


	$html =  '<script src="room_arrangement.js"></script>'.newline();
		$html .= '<link type="text/css" href="room_arrangement.css" rel="Stylesheet"/>'.newline();

	$html .= '<script>var pos_user_id = '.$pos_user_id. ';</script>';

	$form_id = "room_arrangement_form";
	$form_action = 'room_arrangement.form.handler.php';
	$html .=  '<form id = "' . $form_id . '" action="'.$form_action.'.php" method="post" onsubmit="return validateInvoiceForm()">';

	$html .= createRoomNameTable($room_name, $pos_user_id);
	$table_def = createRoomArrangementTableDef();
	$html .= createRoomArrangementHtmlTable($table_def,$room_name,$pos_user_id);
	$html .= createButtons($complete_location);
	$html .= createHiddenInput('original_room_name', $room_name);
	$html .=  '	<script>var formID = "'.$form_id.'";</script>';
	$html .= '<script>document.getElementById(\'room_name\').focus();</script>';
	$html .= '</form>';
	
	//now the delete
	
	$delete_location = 'delete_room_arrangement.php?pos_user_id='.$pos_user_id.'&room_name='.$room_name;
	$html .= confirmDelete($delete_location);
	




	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
else
{
	include (HEADER_FILE);
	echo 'Not A Valid Id';
	include (FOOTER_FILE);
}

function createButtons($complete_location)
{

	$html =  '<INPUT class = "button" type="button" style="width:60px;" value="Add Row" onclick="addRow()" />';
	$html .=  '<INPUT class = "button" type="button" style="width:80px;" value="Copy Row(s)" onclick="copyRow()" />';
	$html .=  '<INPUT class = "button" type="button" value="Move Row(s) Up" onclick="moveRowUp()" />';
	$html .=  '<INPUT class = "button" type="button" style="width:120px;" value="Move Row(s) Down" onclick="moveRowDown()" />';
	$html .=  '<INPUT class = "button" type="button" style="width:80px;" value="Delete Row(s)" onclick="deleteRow()" />';

	$html .=  '<p>';
	$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Save" onclick="saveDraftAndGo(\''.$complete_location.'\')"
	 />'.newline();
	 $html.= '<INPUT class = "button" type="button" style="width:120px" value="Cancel" onclick="exit()" />'.newline();
	 $html .= '<input class = "button" type="button" name="delete" value="Delete Room" onclick="confirmDelete();"/>';
	$html .= '</p>';
	return $html;
	
}
function createRoomNameTable($room_name, $pos_user_id)
{
	$sql = "SELECT DISTINCT room_priority FROM pos_room_arrangements WHERE pos_user_id = $pos_user_id AND room_name='$room_name'";
	$room_priority = getSingleValueSQL($sql);
	$value = ($room_name=='TBD') ? '' : stripslashes($room_name);
	$html = '<p>Room Name';
	$html .= ' <INPUT TYPE="TEXT" class="lined_input"  id="room_name" maxlength="30", size = "35" NAME="room_name" onKeyPress="return disableEnterKey(event)" 	' . checkInputAlphaNumeric() . ' value = "'.$value.'"/>';
	$html .= '</p>';
	$html .= '<p>Room Priority (integer value)';
	$html .= ' <INPUT TYPE="TEXT" class="lined_input"  id="room_priority" style = "width:20px;" NAME="room_priority" onKeyPress="return disableEnterKey(event)" 	onkeyup="checkInput2(this,\'0123456789\')" value = "'.$room_priority.'"/>';
	$html .= '</p>';
	return $html;
}
function createRoomArrangementHtmlTable($column_defintion, $room_name, $pos_user_id)
{
	$html =  '<div class = "room_div">';
	$html .=  '<TABLE id="contents_table" name="contents_table" class="contents_table">';
	$html .= '<thead id="contents_thead" class="contents_thead" name="contents_thead">' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($column_defintion);$i++)
	{
		$html .= createTHFromTD_def($column_defintion[$i]);
	}
	$html .= '</tr>'.newline();

	$html .= '</thead>'.newline();
	//this is the body which is created by javascript	
	$html .=  '	<tbody id = "contents_tbody" name = "contents_tbody" class = "contents_tbody" ></tbody>';
	//And the footer
	$html .=  '<tfoot id = "contents_tfoot" name = "contents_tfoot" class = "contents_tfoot">';
	//now we need to figure out which columns need a footer, and what are they?
	$html .= createTFootFromTD_def($column_defintion);

	$html .= '</tfoot>';
	$html .=  '</table>';
	

	$html .= '<script>var tbody_def = ' . prepareTableDefForJavascriptTableGeneration($column_defintion) . ';</script>';
	
	$html .=  '	<script>var contents_table = "contents_table";</script>';
	//I harde coded tbody_id into the javascript - so use it!
	$html .=  '	<script>var tbody_id = "contents_tbody";</script>';
	$html .=  '	<script>var contents_thead = "contents_thead";</script>';
	$html .=  '	<script>var contents_tfoot = "contents_tfoot";</script>';
	$html .= '<script> var json_room_contents = ' . json_encode(getRoomContents($room_name, $pos_user_id)) . ';</script>';
	$binder_names_ids = getBinderNamesAndIds($pos_user_id);
	$html .= '<script> var binder_names = ["'.   join("\", \"", $binder_names_ids['name']) .'"];</script>'.newline();
	$html .= '<script> var binder_ids = ["'.   join("\", \"", $binder_names_ids['id']). '"]</script>'.newline();
	
	
	
	
	return $html;
}

function createRoomArrangementTableDef()
{


	$columns = array(
		
				

				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'input',
				'POST' => 'no',
				'element' => 'input',
				'element_type' => 'none',
				'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
										'readOnly' => '"true"',
										'size' => '"3"')
					),
				array('db_field' => 'pos_binder_id',
					'caption' => 'Binder',
					'type' => 'select',
					'select_names' => 'binder_names',
					'select_values' => 'binder_ids',
					'properties' => array(	'style.width' => '"30em"',
											)		
					)
	
				
			);			
					
	
	return $columns;
	
	
	
}
?>