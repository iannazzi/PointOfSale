<?php
/*
	Craig Iannazzi 2-8-2013 on a snowy day at midtown
	
	//eventually I would like to link the discount to products, or categories, or manufacturers
	// so there would be a discount_id to category, manufacturer, product id lookup table... a dynamic table
*/
$page_title = 'Documents';
$binder_name = 'Documents';
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../office_functions.php');
$javascript_version = 'documents.2014.02.04.js';
$complete_location = 'list_documents.php';
$cancel_location = 'list_documents.php?message=Canceled';

if (isset($_POST['submit'])) //Form Handler
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_document_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_document_id'] == 'TBD')
	{
		$insert['document_date'] = getCurrentTime();
		$insert['pos_user_id'] = $_SESSION['pos_user_id'];
		$pos_document_id = simpleTransactionInsertSQLReturnID($dbc,'pos_documents', $insert);
		$message = urlencode('Document Id ' . $pos_document_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_document_id = getPostOrGetID('pos_document_id');
		$date_time = getCurrentTime();
		
		//First insert everything existing into the backup
		$sql = "SELECT * FROM pos_documents WHERE pos_document_id = $pos_document_id";
		$backup_data = getSQL($sql);
		$backup['pos_document_id'] = $pos_document_id;
		$backup['pos_user_id'] = $backup_data[0]['pos_user_id'];
		$backup['document_date'] = $backup_data[0]['document_date'];
		$backup['document_name'] = scrubInput($backup_data[0]['document_name']);
		$backup['document_text'] = scrubInput($backup_data[0]['document_text']);
		$backup['comments'] = scrubInput($backup_data[0]['comments']);
		$backup['document_overview'] = scrubInput($backup_data[0]['document_overview']);
		$pos_document_backup_id = simpleTransactionInsertSQLReturnID($dbc,'pos_documents_backup', $backup);
		
		
		
		
		$insert['document_date'] = $date_time;
		$insert['pos_user_id'] = $_SESSION['pos_user_id'];
		$insert['auto_save_document_text'] = '';
		//$insert['document_date'] = '';
		$key_val_id['pos_document_id'] = $pos_document_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_documents', $key_val_id, $insert);
		$message = urlencode('Document ID ' . $pos_document_id . " has been updated");
		
		
		
	}
	simpleCommitTransaction($dbc);
	
	//unlock...
	$db_table = 'pos_documents';
	$key_val_id['pos_document_id'] = $pos_document_id;
	unlock_entry($db_table, $key_val_id);
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);
}
else if (isset($_POST['cancel']))
{
}
else if (isset($_POST['original']))
{
	echo 'restore original text';
	exit();
}
else if (isset($_POST['unlock']))
{

	
	$dbc = startTransaction();
	$pos_document_id = getPostOrGetID('pos_document_id');
	$date_time = getCurrentTime();
	
	//First insert everything existing into the backup
	$sql = "SELECT * FROM pos_documents WHERE pos_document_id = $pos_document_id";
	$backup_data = getSQL($sql);
	$backup['pos_document_id'] = $pos_document_id;
	$backup['pos_user_id'] = $backup_data[0]['pos_user_id'];
	$backup['document_date'] = $backup_data[0]['document_date'];
	$backup['document_name'] = scrubInput($backup_data[0]['document_name']);
	$backup['document_text'] = scrubInput($backup_data[0]['document_text']);
	$backup['comments'] = scrubInput($backup_data[0]['comments']);
	$backup['document_overview'] = scrubInput($backup_data[0]['document_overview']);
	$pos_document_backup_id = simpleTransactionInsertSQLReturnID($dbc,'pos_documents_backup', $backup);
	
	
	//next put the autosave text into the text, clear the autosave text, and clear the lock
	$insert = array();
	$insert['document_text'] = scrubInput($backup_data[0]['auto_save_document_text']);
	$insert['auto_save_document_text'] = '';
	$insert['user_id_for_entry_lock'] = 0;
	$key_val_id['pos_document_id'] = $pos_document_id;
	$results[] = simpleTransactionUpdateSQL($dbc,'pos_documents', $key_val_id, $insert);
	simpleCommitTransaction($dbc);
	header('Location: documents.php?type=edit&pos_document_id=' . $pos_document_id);
	exit();
	
}
else if (isset($_POST['backup']))
{
	//show the backup
	echo 'this is where we will show the backup text - remind me later';
	exit();
}
else if (isset($_POST['auto']))
{
	echo 'backup original text';
	exit();
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
	if(strtoupper($type) == 'ADD')
	{
		$pos_document_id = 'TBD';
		$header = '<p>Add Document</p>';
		$page_title = 'Add Document';
		$data_table_def = createDocumentTableDef($type, $pos_document_id);
	}
	elseif (strtoupper($type) == 'EDIT')
	{
		$pos_document_id = getPostOrGetID('pos_document_id');
		$db_table = 'pos_documents';
		$key_val_id = array('pos_document_id' => $pos_document_id);
		
	
	
		//here we check the lock and the auto save:
		$page_title = 'Locked';
		$entry_lock = getSingleValueSQL("SELECT user_id_for_entry_lock FROM ".$db_table." WHERE " . key($key_val_id) . "='" .$key_val_id[key($key_val_id)]."'");
		if( $entry_lock != 0)
		{
		
			//problem.... the entry is coded as locked.... this could be because someone is editing it, or a connection was lost and it needs to be unlocked. this is also where we need to consider autosave
		
		
			//ask for advice
			//dont ask for advice.... if unlocked then take the original text and back up... restore the autosave text.
			$form_handler =  basename($_SERVER['PHP_SELF']);
			$auto_save_text = getSingleValueSQL("SELECT auto_save_document_text FROM pos_documents WHERE pos_document_id = $pos_document_id");
		
			$original_text = getSingleValueSQL("SELECT document_text FROM pos_documents WHERE pos_document_id = $pos_document_id");
		
			$html = '<form id = "entry_lock" name="entry_lock" action="'. $form_handler. '" method="post" >';
			$html .= '<p>Problem! This Entry is Coded as LOCKED by ' . getUserFullName($entry_lock) .', meaning one of two things: 1) Another user is busy monkeying with the data, and if you go into it then you might destroy that fresh data, or 2) Somewhere along the way the entry was locked and then the user\'s session expired or a power failure or something else catastrophic happened, meaning you need to unlock to edit. If you have a browser open there is a chance of data corruption due to that browser auto-saving text. Be careful.</p>';
			$html .= createHiddenInput('pos_document_id', $pos_document_id);
			//$html .= '<input type="checkbox" name="unlock" value="unlock">Unlock The Table';
			//$html .= '<br>';
		
			//$html.= '<h2>Currently Stored but Unsaved Text</h2>';
			//$html .= $original_text;
			//$html.= '<h2>Auto Saved Text - Restoring this will also backup the Original stored Text</h2>';
			//$html .= $auto_save_text;

			$html .= '<p>';
			//$html .= '<input class = "button" type="submit" name="original" style="width:200px" value="Keep Original Text &  Text Disregard Auto Save text"/>';
			//$html .= '<input class = "button" type="submit" name="auto" style="width:200px" value="Keep Auto Save TExt and Backup Original Text"/>';
			$html .= '<input class = "button" type="submit" name="unlock" style="width:200px" value="Unlock and Edit"/>';
			$html .= '<input class = "button" type="submit" name="cancel" style="width:200px"  value="Cancel and Return"/>';
			$html .= '</p>';
			$html .='</form>';
			include (HEADER_FILE);
			echo $html;
			include (FOOTER_FILE);
			exit();
			//exit the code
	
		}
	
	
		//lock the entry
		lock_entry($db_table, $key_val_id);
	
		$header = '<p>EDIT Document</p>';
		$page_title = 'Edit Document';
		$data_table_def_no_data = createDocumentTableDef($type, $pos_document_id);	
		$db_table = 'pos_documents';
		$key_val_id['pos_document_id'] = $pos_document_id;
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
	}
	elseif (strtoupper($type) == 'VIEW')
	{
		$pos_document_id = getPostOrGetID('pos_document_id');
		$edit_location = 'documents.php?pos_document_id='.$pos_document_id.'&type=edit';
		$delete_location = 'delete_document.form.handler.php?pos_document_id='.$pos_document_id;
		$db_table = 'pos_documents';
		$key_val_id['pos_document_id']  = $pos_document_id;
		$data_table_def = createDocumentTableDef($type, $pos_document_id);
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
	}
	else
	{
	}

	//build the html page
	if (strtoupper($type) == 'VIEW')
	{
		$html = printGetMessage('message');
		$html .= '<p>View Document</p>';
		$html .= confirmDelete($delete_location);
		$html .= createHTMLTableForMYSQLData($data_table_def);
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
		$html .= '<p>';
		
		
		
		//now add the revision history
		$tmp_sql = "
	
			CREATE TEMPORARY TABLE documents

			SELECT pos_documents_backup.*, concat(first_name, ' ', last_name) as user_name FROM pos_documents_backup
			LEFT JOIN pos_users USING (pos_user_id)
			WHERE pos_document_id = $pos_document_id
			
			
			;";
				
	$tmp_select_sql = "SELECT * FROM documents WHERE 1";			
		$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_document_backup_id',
			'get_url_link' => 'documents.php?type=backup',
			'url_caption' => 'view',
			'get_id_link' => 'pos_document_backup_id'),
		array(
			'th' => 'User Name',
			'mysql_field' => 'user_name'),
		array(
			'th' => 'Date',
			'mysql_field' => 'document_date',
			'sort' => 'document_date'),
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html .= '<h2>Revisions</h2>';
	$html .= createRecordsTable($data, $table_columns);
		
		
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Documents" onclick="window.location = \''.$complete_location.'\'" />';
		$html .= '</p>';
		
		
		
		
	}
	else
	{
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
	
		$html = $header;
		$html .=  '<script src="'.$javascript_version.'"></script>'.newline();
		$html .= '<script>var pos_document_id = '.$pos_document_id. ';</script>';
		$html .= tinymce_editor();
		$form_handler = 'documents.php';
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
		$html .= '<script>document.getElementsByName("document_name")[0].focus();</script>';
	}


	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}





function createDocumentTableDef($type, $pos_document_id)
{
	if ($pos_document_id =='TBD')
	{
		//$unique_validate = array('unique' => 'location_group_name', 'min_length' => 1);
	}
	else
	{
		//$key_val_id['pos_location_group_id'] = $pos_location_group_id;
		//$unique_validate = array('unique' => 'location_group_name', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_document_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Document ID',
								'value' => $pos_document_id,
								'validate' => 'none'
								
								),
							
						array('db_field' =>  'document_name',
								'type' => 'input',
								'caption' => 'Document Name',
								'validate' => 'none'),
						array('db_field' =>  'document_overview',
								'type' => 'input',
								'caption' => 'Document Overview',
								'validate' => 'none'),
						array('db_field' => 'document_text',
									'type' => 'textarea',
									'tags' => ' cols="84" ',
									'validate' => 'none'),
					
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments')
						);	

}
?>