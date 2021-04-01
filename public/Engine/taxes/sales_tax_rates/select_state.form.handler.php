<?php
/*
	select state.php
*/
require_once ('../tax_functions.php');
$url = $_POST['complete_location'];

$pos_state_id['pos_state_id'] = getPostOrGetID('pos_state_id');
$url = addGetValue($url, 'pos_state_id', $pos_state_id['pos_state_id']);
if (isset($_POST['submit'])) 
{
	header_redirect($url);
}
?>
