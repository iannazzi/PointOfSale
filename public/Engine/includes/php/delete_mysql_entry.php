<?php
$page_level = 3;
$page_navigation = 'taxes';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
$page_title = 'Delete';

//delete entry
$db_table = getPostOrGetValue('db_table');
$primary_id_name = getPostOrGetValue('primary_id_name');
$delete_message = getPostOrGetValue('delete_message');
$complete_location = getPostOrGetValue('complete_location');
$cancel_location = getPostOrGetValue('cancel_location');
$primary_id_value = getPostOrGetValue('primary_id_value');

if (isset($_POST['delete']))
{
	$sql = "DELETE FROM " . $db_table . " WHERE " .$primary_id_name . " = " . $primary_id_value;
	runSQL($sql);
	header('Location: '.$_POST['complete_location'] .'?message=Deleted');

}
else if (isset($_POST['cancel']))
{
		header('Location: '.$_POST['cancel_location'] .'?message=Canceled');
}

//show the form
$form_handler = 'delete_mysql_entry.php';
$html = '<p>'.$delete_message.'</p>';
$html .= '<form action="' . $form_handler.'" method="post" onsubmit="">';
$html .= createHiddenInput('complete_location', $complete_location);
$html .= createHiddenInput('cancel_location', $cancel_location);
$html .= createHiddenInput('db_table', $db_table);
$html .= createHiddenInput('primary_id_name', $primary_id_name);
$html .= createHiddenInput('delete_message', $delete_message);
$html .= createHiddenInput('primary_id_value', $primary_id_value);
$html .= '<p><input class ="button" type="submit" name="delete" value="Delete" onclick="needToConfirm=false;"/>' .newline();
$html .= '<input class = "button" type="button" name="cancel" value="Cancel" onclick="cancelForm()"/>';
$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>
