<?PHP

/*
	*This file will basically update the inventory quantities for each sub ID
	
*/
$binder_name = 'Products';
$access_type = 'WRITE';

$page_title = "Process POS Product Information";
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
//Add javascript library

echo '<script src="' . POS_ENGINE_URL . '/includes/javascriptLibrary.js"></script>';






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


//include (HEADER_FILE);


// Need the database connection:
require_once(MYSQL_POS_CONNECT_FILE);
$dbc = pos_connection();

// Check if the form has been submitted:
if (isset($_POST['submit'])) 
{
	//we need to write the quantity of each product sub id 
	//the name of each quantity box is the sub id
	// no error checking needed here?
	$store_id = $_POST['store_id'];
	$subid_sql = "SELECT * from pos_products_sub_id WHERE pos_product_id = $pos_product_id";
	$subid_r = @mysqli_query ($dbc, $subid_sql);
	while ($subid_row = mysqli_fetch_array($subid_r, MYSQLI_ASSOC))
	{
	
		$nq = $_POST[$subid_row['pos_product_sub_id']];
		$pos_product_sub_id = $subid_row['pos_product_sub_id'];
		
		// does the inventory record exist - if so need to update, if not need to insert
		$sub_id_inventory_check_q = "SELECT pos_product_inventory_id FROM pos_products_inventory WHERE pos_product_sub_id = $pos_product_sub_id AND pos_store_id = $store_id";
		$sub_id_inventory_check_r = @mysqli_query($dbc, $sub_id_inventory_check_q);
		if (mysqli_num_rows($sub_id_inventory_check_r) == 0) 
		{
			//Inventory ID does not exist 
			$inventory_update_q = "INSERT INTO pos_products_inventory (pos_product_sub_id, pos_store_id, in_stock_qty) VALUES ( $pos_product_sub_id,  $store_id, $nq)";
			$inventory_update_r = @mysqli_query($dbc, $inventory_update_q);
		}
		else
		{
			//inventory ID exists
		
			$inventory_update_q = "UPDATE pos_products_inventory SET pos_store_id = $store_id, in_stock_qty = $nq WHERE pos_product_sub_id=$pos_product_sub_id LIMIT 1";
			$inventory_update_r = @mysqli_query($dbc, $inventory_update_q);
		}

		
		
		
	
	}
	
		
			
				include (HEADER_FILE);
				// Print a message:
				echo '<p>' . $pos_product_id . ' has been edited.</p>';	
				//add a link back to the product:
				echo '<a id = "backlink" href="edit_pos_product.php?pos_product_id=' . $pos_product_id. '">Back To Editing the Product</a>';
				
				//set a script to set focus to the link
				//set the focus
				echo '<script type="text/javascript">
				document.getElementById("backlink").focus() ;
				</script>';

				include (FOOTER_FILE);



}
elseif (isset($_POST['cancel']))
{
	include (HEADER_FILE);
				echo '<p> Product has not been edited.</p>';	
				//add a link back to the product:
				echo '<a id = "backlink" href="edit_pos_product.php?pos_product_id=' . $pos_product_id. '">Back To Editing the Product</a>';
				
				//set a script to set focus to the link
				//set the focus
				echo '<script type="text/javascript">
				document.getElementById("backlink").focus() ;
				</script>';
				include (FOOTER_FILE);
}

?>