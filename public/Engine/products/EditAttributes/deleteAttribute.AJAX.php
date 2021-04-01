<?php  

//get attributes

/*This script will need to update the table for attributes. We will need to write new attributes and modify existing attributes
 */
 
 /* to test type in the url:
 	http://www.embrasse-moi.com/POS/products/deleteAttribute.AJAX.php?pos_product_attribute_id=8035&product_id=856
 */
 // recieve post data like this
 //// recieve it like this: $data = file_get_contents("php://input");

if ( (isset($_GET['pos_product_attribute_id'])) && (is_numeric($_GET['pos_product_attribute_id'])) && (isset($_GET['product_id'])) && (is_numeric($_GET['product_id'])) )
	 
	
{

	$binder_name = 'Products';
$access_type = 'WRITE';
	
$page_level = 2;
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
	require_once(MYSQL_POS_CONNECT_FILE);
	$dbc = pos_connection();
	$pos_product_attribute_id = mysqli_real_escape_string($dbc, trim($_GET['pos_product_attribute_id']));
	$product_id = mysqli_real_escape_string($dbc, trim($_GET['product_id']));
	// Define the query:
	// what test do we need to do on the attributes? none right now
	
	$pos_product_attribute_id_sql = "DELETE FROM pos_products_attributes WHERE pos_product_attribute_id=$pos_product_attribute_id LIMIT 1";	
	$pos_product_attribute_id_result = mysqli_query($dbc, $pos_product_attribute_id_sql);
	
	// If there is a style number then we can get the product ID. Then We can get the color codes.
			if ($pos_product_attribute_id_result) 
			{ // If it ran OK.
				//echo 'success entering the overview into the DB';
				// now get the full product attributes so we can re-display them....
				$return_attributes_sql = "SELECT pos_product_attribute_id, name,options FROM pos_products_attributes WHERE pos_product_id='$product_id'";
				$return_attributes_sql_result = mysqli_query($dbc, $return_attributes_sql);
				if (mysqli_num_rows($return_attributes_sql_result) > 0) 
				{
			
					// Initialize an array:
					$json = array();
					// Put each store into the array:
					while (list($return_pos_product_attribute_id, $return_name, $return_options) = mysqli_fetch_array($return_attributes_sql_result, MYSQLI_NUM)) 
					{
					
						$json[] = array('pos_product_attribute_id' => $return_pos_product_attribute_id,
						'name' => $return_name,
						'options' => $return_options);
							
					}
				
				// Send the JSON data:
				echo json_encode($json) . "\n";
				
				} else
				{ // No records returned.
					echo 'null';
				}
				
			} 
			else 
			{
				// Public message:
				echo '<h1>System Error</h1>
				<p class="error">SQL  error. We apologize for any inconvenience.</p>'; 
				// Debugging message:
				echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $pos_product_attribute_id_result . '</p>';
			}

	mysqli_close($dbc);	
		
} // closing of isset
else { // No data supplied!

	echo 'Error, check that pos_product_attribute_id correctly supplied';

}
?>
