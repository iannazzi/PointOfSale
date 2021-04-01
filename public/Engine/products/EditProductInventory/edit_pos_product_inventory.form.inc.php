<?PHP
$binder_name = 'Products';
$access_type = 'WRITE';
/*
	*This is the contents of the product inventory form
	*called from edit_pos_products or edit_pos_products_inventory.form

	*We will need the store selection to load inventory values
	*we will need to be able to edit inventory values - might be a separate form...
	*Ordered values come from purchase orders (might want to include the PO#)

		*sub IDs cannot be edited or deleted
	
	
	*Table will look like this:
	| Product Sub ID  |  Inventory | Ordered | Expected Delivery Date | Invenotry ID | BARCODE IMAGE | Chkbox for print?	
*/
//code for generating barcode


echo '<script src="' . POS_ENGINE_URL . '/includes/javascript_library.js"></script>';

require(POS_ENGINE_URL . '/includes/3rdParty/Image_Barcode2/Barcode2.php');
//require_once('Image/Barcode2.php');
//Script for the store locator refresh
echo '<script src="edit_pos_product_inventory.form.js"></script>';

// Need a store selector
// use the default_store_id set on login to load a store. Igonore company name
//get the company info for the default store id - if inventory_store_id cookie is set the use that first, otherwise use the session
$store_id = $_SESSION['store_id'];
if ( isset($_COOKIE['inventory_store_id']) )
{
	$store_id = $_COOKIE['inventory_store_id'];
}

$store_sql = "SELECT * FROM pos_stores";
$store_r = @mysqli_query ($dbc, $store_sql);
echo '<td><select name="store_id" onChange="updateInventoryTable(this)">';
echo '<option value="false">Select Store</option>';
while($store_addresses = mysqli_fetch_array ($store_r, MYSQLI_ASSOC))
{
	echo '<option value="' . $store_addresses['pos_store_id'] . '"';
	//set the store to the default value or the selected value
	if ($store_addresses['pos_store_id'] == $store_id) 
	{
		echo ' selected="selected"';
		$bln_defaultStore = 'true';
	}
		
	echo '>' . $store_addresses['store_name'] . '</option>';
}
echo '</select>';


//Get all the product SUBID's.... 
//$subid_sql = "SELECT * from pos_products_sub_id WHERE pos_product_id = $pos_product_id";
//$subid_r = @mysqli_query ($dbc, $subid_sql);
//$pos_product_sub_id_array = array();
//while ($subid_row = mysqli_fetch_array($subid_r, MYSQLI_ASSOC))
//{
	//need to loop through and get all subID's for the product
//	$pos_product_sub_id_array[] = $subid_row['pos_product_sub_id'];
//}

//Send the pointer back to the beginning of the array


//Get all the inventory for each subID and store id.. if it is not there assume 0

// to do this using int's
//$inventory_q = "SELECT * FROM pos_products_inventory  WHERE pos_store_id = $store_id AND pos_product_inventory_id IN (".implode(',',$pos_product_sub_id_array).')';
//$inventory_r = @mysqli_query ($dbc, $inventory_q);
//$inventory_row = mysqli_fetch_array($inventory_r, MYSQLI_ASSOC);

// to do this using strings
//$query = "SELECT * FROM `$table` WHERE `$column` IN('".implode("','",$array).'\')';


//Get all the inventory on order from the open PO's and the delivery date

//Get all the product SUBID's.... again
$subid_sql = "SELECT * from pos_products_sub_id WHERE pos_product_id = $pos_product_id";
$subid_r = @mysqli_query ($dbc, $subid_sql);

//This table will be created dynamically - need to specify a spot for the table
	// Table header.
	echo '<table id = "view_pos_product_inventory" class = "generalTable">
	<tr>
		<th>sysID</th>
		<th >Product Sub ID Name</th>
		<th>Qty In Stock</th>
		<th >Qty On Order</th>
		<th>Expected Date</th>
		<th>BARCODE IMAGE</th>
		<th>Print Label Checkbox</th>
	</tr>
';
	$bg = '#eeeeee'; // Set the initial background color.
	// Fetch and print all the records:
	while ($subid_row = mysqli_fetch_array($subid_r, MYSQLI_ASSOC)) {
		$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee'); // Switch the background color.
		$barcode =  $subid_row['product_subid_name'];
		$encoding = 'code128';
		$scale ='1';
		$imgtype='png';
		
		echo '<tr bgcolor="' . $bg . '">
			<td >' . $subid_row['pos_product_sub_id'] . '</td>
			<td >' . $subid_row['product_subid_name']. '</td>';
			echo '<td >';
			//Find out if the form is for display or edit
			if ($form_display_type == "Display")
			{
			}
			else
			{
				echo '<input type="text" size = "5" name ="' . $subid_row['pos_product_sub_id'] . '" value ="';
			}
			// now get the quantity in stock for the selected store
			$inventory_sql = "SELECT in_stock_qty FROM pos_products_inventory WHERE pos_product_sub_id = " . $subid_row['pos_product_sub_id'] . " AND pos_store_id = $store_id";
			$inventory_r = @mysqli_query($dbc, $inventory_sql);
			if (mysqli_num_rows($inventory_r) == 1)
			{
				$inventory_row = mysqli_fetch_array($inventory_r, MYSQLI_ASSOC);
				echo $inventory_row['in_stock_qty'];
			}
			else
			{
				echo '0';
			}
			if ($form_display_type == "Display")
			{
			}
			else
			{
				echo '" onkeyup  = \'checkInput(this,"0123456789")\' />';
			}
			echo '</td>
			<td >on order tbd</td>
			<td >date tbd</td>
			<td >  
			<img src="' .POS_ENGINE_URL . '/includes/3rdParty/Image_Barcode2/barcode_img.php?num=' . $barcode .  '&type=code128&imgtype=png"
 alt="PNG: ' . $barcode . '" title="PNG: ' . $barcode . '">
			 </td>
			<td><input type="checkbox" /></td>
		</tr>';
	}

	echo '</table>'; // Close the table.


?>
