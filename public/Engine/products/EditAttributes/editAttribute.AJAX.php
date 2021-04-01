<?php  

//edit  attributes

/*This script will need to update the table for attributes. We will need to write new attributes and modify existing attributes
 */
 
 /* to test type in the url:
 	http://www.embrasse-moi.com/POS/products/editAttribute.AJAX.php?product_id=856&pos_product_attribute_id=8019&name=test%20size%204&options=%5B%2232%22%2C%2243%22%2C%2249%22%2C%2245%2067%22%5D
 */
 // recieve post data like this
 //// recieve it like this: $data = file_get_contents("php://input");

if ( (isset($_GET['product_id'])) && (is_numeric($_GET['product_id'])) &&(isset($_GET['pos_product_attribute_id'])) && (is_numeric($_GET['pos_product_attribute_id'])) && (isset($_GET['options'])) && (isset($_GET['name'])) )
	 
	
{
	// valid product ID has been sent...
	//echo "options: " . $_GET['options'] . "\n<br />";
	//echo "name: " . $_GET['name'] . "\n<br />";
	//echo "url decoded options: " . urldecode($_GET['options']) . "\n<br />";
	//echo "url decoded name: " . urldecode($_GET['name']) . "\n<br />";
	
	// need to decode everything
	//$name = $_GET['name'];
	//$options = json_decode($_GET['options'], true);
	
	//echo "JSON deocde options: " . $options . "\n<br />";
	//echo "JSON decode name: " . $name . "\n<br />";
	$options = '';
	$options_array = explode('\"', $_GET['options']);
	//echo "exploded options: " . $options_array . "\n<br />";
	foreach($options_array as $str)
	{
		//echo "exploded option: " . $str . " length: " . STRLEN($str) . "\n<br /> ";
		if ( ($str == '[') || ($str == ']') || ($str == '') || ($str == ',')  )
		{
			// do nothing
			//echo "found nothing \n<br />";
		}
		else
		{
			// add the string to the array .. but do not add the \r\n to the last element
			$options = $options . $str . "\r\n";
		}
	}
	//remove the last 4 characters of the options string
	if ( strlen($options > 4) )
	{
		$options = substr($options, 0, -2);
	}
	//$options = implode("\r\n", $options_array);
	//echo "Imploded options: " . $options . "\n<br />";

	// options needs to look like: '32D\r\n32E\r\n32F\r\n32G\r\n32H\r\n34C\r\n34D\r\n34F\r\n34H\r\n36E\r\n36G\r\n36H\r\n38F\r\n38G\r\n38H\r\n40C\r\n40D\r\n40G\r\n40H\r\n42E\r\n42F\r\n42G\r\n42H\r\n44D\r\n44E\r\n44F\r\n44G\r\n44H'
	
	$binder_name = 'Products';
$access_type = 'WRITE';

	require_once ('../includes/config.inc.php');
	require_once (PHP_LIBRARY);

	require_once (CHECK_LOGIN_FILE);
	require_once(MYSQL_POS_CONNECT_FILE);
	$dbc = pos_connection();
	$product_id = mysqli_real_escape_string($dbc, trim($_GET['product_id']));
	$name = mysqli_real_escape_string($dbc, trim($_GET['name']));
	$pos_product_attribute_id = mysqli_real_escape_string($dbc, trim($_GET['pos_product_attribute_id']));
	
	// Define the query:
	// what test do we need to do on the attributes? none right now
	
	$product_attribute_sql = "UPDATE pos_products_attributes SET name ='$name', caption='$name', options='$options' WHERE pos_product_attribute_id =$pos_product_attribute_id LIMIT 1";
	$product_attribute_result = mysqli_query($dbc, $product_attribute_sql);
	
	// If there is a style number then we can get the product ID. Then We can get the color codes.
			if ($product_attribute_result) 
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
				echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $product_attribute_result . '</p>';
			}

	mysqli_close($dbc);	
		
} // closing of isset
else { // No data supplied!

	echo 'Error, check that product id, options, and name are correctly supplied';

}
?>
