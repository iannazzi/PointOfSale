<?php
/*
	* this is the pos product attributes content of the form
	* this will be called by the main product editing page & the attributes editing page
*/
	
echo '<table id = "pos_product_attribute_table" class = "dataTable">
		<thead><tr>';

			echo '<th></th>';
	
		echo '
		<th>Attribute Name (Ex: Size, Color)<BR>This will appear in drop-downs on the web</th>
		<th>Attribute Options(1 per line)<br>This Will appear in drop downs on the web</th>
		<th>Attribute ID<br>Auto Configure</th>
		</thead></tr>
		<tbody id = "pos_product_attribute_table_body">';

// we need to know the list of attributes for the product
$attribute_q = "SELECT pos_product_attribute_id, name, caption, options FROM pos_products_attributes WHERE pos_product_id = $pos_product_id";
$attribute_r = @mysqli_query ($dbc, $attribute_q);
if (mysqli_num_rows($attribute_r) > 0)
{ // We have attributes so diaply them

	while ($attribute_row = mysqli_fetch_array($attribute_r, MYSQLI_ASSOC))
	{
		echo '<tr>';

			echo '<td><input type="checkbox" onclick="unCheckOther(this)" /></td>'; 
	
		echo '<td><input type = "hidden" value = "'. $attribute_row['pos_product_attribute_id']  . '" />'. $attribute_row['name'] . '</td>';
		$strArray = explode("\n", $attribute_row['options']);
		echo '<td>';
		foreach ($strArray as $value) 
		{
			echo $value. "<br>";
		}
		echo '</td>';
		echo '<td>'. $attribute_row['pos_product_attribute_id'] .'</td>';
		echo '</tr>';
	}
}
echo '</tbody></table>';

?>


