<?php
$binder_name = 'Products';
$access_type = 'Write';
$page_title = 'Print Labels';
require_once ('../product_functions.php');
//pass in the $_POST['pos_product_sub_id'][] array, $_POST['quantity'], row_offest, column_offset, template, $filename

		

		
		$counter = 0;
		$product_sub_ids = array();
		$quantities = array();
		for($row=0;$row<sizeof($_POST['row_number']);$row++)
		{
			//if(isset($_POST['row_checkbox_'.$row]) )
			//{
				$product_sub_ids[$counter] = $_POST['pos_product_sub_id'][$row];
				$quantities[$counter] = $_POST['quantity'][$row];
				$counter++;
			//}
		}
		$row_offset = scrubInput($_POST['row_offset']);
		$column_offset = scrubInput($_POST['column_offset']);
		$filename = scrubInput($_POST['filename']);
		printProductLabelsAvery5167V2($product_sub_ids, $quantities, $row_offset, $column_offset,  $filename);
?>