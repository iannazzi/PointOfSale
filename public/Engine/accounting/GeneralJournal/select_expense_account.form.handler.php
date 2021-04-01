<?php
/*
	* add_manufacturer.form.handler.php
	* handels the additon of manufacturer information
	*called from add_manufacturer.php
	*will ne
*/
$binder_name = 'General Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
$pos_account_id['pos_account_id'] = getPostOrGetID('pos_account_id');
if (isset($_POST['submit'])) 
{
	header('Location: '.$_POST['complete_location'] .'?pos_account_id=' . $pos_account_id['pos_account_id']);		
}
?>
