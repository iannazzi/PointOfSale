<?php

/*
	*this page will allow us to edit attributes and add attributes
	*made to be dynamic however didn't really need to be
	
*/
$binder_name = 'Products';
$access_type = 'WRITE';
$page_title = "Edit POS Product Attributes";
$page_level = 3;	
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
echo '<script src="' . POS_ENGINE_URL . '/includes/ajax.js"></script>';
echo '<script src="' .POS_ENGINE_URL . '/includes/confirm_navigation.js"></script>';
echo '<script src="edit_pos_product_attributes.form.js"></script>';
//Check that a the product ID sent over is valid

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
					
echo '<h2> Edit a POS Product Attribute </h2>';
// Need the database connection:
require_once(MYSQL_POS_CONNECT_FILE);
$dbc = pos_connection();


//this for will edit a products attributes - needs to be dynamic.....

include ('edit_pos_product_attributes.form.inc.php');
echo '<div id="attributeButtons" class = "attributeButtons">';
echo '<INPUT class = "button" type="button" id = "addAttribute" value="Add Attribute" onclick="addAttribute(\'pos_product_attribute_table_body\')" />';
echo '<INPUT class = "button" type="button" id = "editAttribute" value="Edit Attribute" onclick="editAttribute(\'pos_product_attribute_table_body\', ' . $pos_product_id . ')" />';
echo '<INPUT class = "button" type="button" id = "deleteAttribute" value="Delete Attribute" onclick="deleteAttribute(\'pos_product_attribute_table_body\', ' . $pos_product_id . ')" />';
echo '</div>';

// drop out some variables:
?>
<script>
var pos_product_id = "<?php echo $pos_product_id; ?>";
</script>
<?php

		
//we need to know the attribute name, and we need to have an add button.....


?>
