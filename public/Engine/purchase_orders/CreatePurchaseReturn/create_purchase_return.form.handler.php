<?php
/*
*/
$page_title = 'Create a Purchase Return';
require_once('../po_functions.php');
require_once(PHP_LIBRARY);
if (isset($_POST['submit'])) 
{
	$data = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);	
	
	//remove the first element - it is the id that we do not want - that needs to auto generate...
	unset($data['pos_purchase_return_id']);
	$pos_employee = array('pos_user_id' => $_SESSION['pos_user_id']);
	$data = array_merge($data,$pos_employee);
	$pos_purchase_return_id['pos_purchase_return_id'] = simpleInsertSQLReturnID('pos_purchase_returns', $data);
	$complete_location = 'purchase_return_contents.php?pos_purchase_return_id='.$pos_purchase_return_id['pos_purchase_return_id'];
	header('Location: '.$complete_location);		
}
?>
