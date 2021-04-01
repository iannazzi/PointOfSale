<?php

// this form will allow user to edit products inventory count
echo '<script src="edit_pos_product.inventory.inc.js"></script>';
//confirm navagation before leaving
echo '<script src="' . POS_ENGINE_URL . '/includes/confirm_navigation.js"></script>';


/*
	* pos.products calls this file
	* pos.products.attributes.inc will also call this file when atributes are added
	
 	* A unique product is created for every combination of attributes.
	* generally the PO will create these combinations for us. However a user should be able to add products that were not created through a PO.
	*For the product check to see if there is inventory ID's.. If so display them... if not offer a crete button which will allow user to set up a product then create the IDS
	
	* Adding and deleting inventory ID's:
	* Once created products cannot be deleted. Once the sub ID's are created they can't be deleted.
	* Once the attributes are added or edited the sub ID's can be genereated automatically.
	* sub ID's are in the format: manufacturerCode-StyleNumber-attrbute1Code-attribute2Code-...-attributeNCode
	* ex ANI-5575-NUD-32A
	
	* Products are added through the PO process. 
	* Products can be added through the POS system - when adding a product check for inventory ID's, if none then add a Generate unique ID button
	* Products can be added through .csv upload


	*The inventory table will show all subID's for the product. A store selector is needed to show inventory per store. Default store is the one the user is registered to.
	


*/
$binder_name = 'Products';
$access_type = 'WRITE';
$page_title = "Edit POS Product Inventory";
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
echo '<script src="' . POS_ENGINE_URL . '/includes/confirm_navigation.js"></script>';	

// Check for a valid product ID, through GET or POST:
//if ( (isset($_GET['pid'])) && (is_numeric($_GET['pid'])) ) { // From view_products.php
if ( (isset($_GET['pos_product_id'])) && (is_numeric($_GET['pos_product_id'])) ) 
{ // From view_products.php
	$pos_product_id = $_GET['pos_product_id'];
} elseif ( (isset($_POST['pos_product_id'])) && (is_numeric($_POST['pos_product_id'])) ) 
{ // Form submission.
	$pos_product_id = $_POST['pos_product_id'];
} else 
{ // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	exit();
}

include (HEADER_FILE);
// Need the database connection:
require_once(MYSQL_POS_CONNECT_FILE);
$dbc = pos_connection();


					
echo '<h2> Edit POS Product Inventory </h2>';
$form_display_type = "Edit";
// 
echo '<form action="edit_pos_product_inventory.form.handler.php" method="post">';	
include ('edit_pos_product_inventory.form.inc.php');
echo '<input type="submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>';
echo '<input type="submit" name="cancel" value="Cancel" onclick="needToConfirm=false;"/>';
echo  '<input type="hidden" name="pos_product_id" value="' . $pos_product_id . '" />';
echo '</form>';

mysqli_close($dbc);

include (FOOTER_FILE);
?>
