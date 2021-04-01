<?php
/*
	* add_manufacturer.form.handler.php
	* handels the additon of manufacturer information
	*called from add_manufacturer.php
	*will ne
*/
require_once ('../po_functions.php');
$pos_manufacturer_brand_id['pos_manufacturer_brand_id'] = getPostOrGetID('pos_manufacturer_brand_id');
if (isset($_POST['submit'])) 
{
	header('Location: '.$_POST['complete_location'] .'?pos_manufacturer_brand_id=' . $pos_manufacturer_brand_id['pos_manufacturer_brand_id']);		
}
?>
