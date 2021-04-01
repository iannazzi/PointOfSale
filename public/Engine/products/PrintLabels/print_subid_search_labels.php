<?php

$binder_name = 'Products';
$access_type = 'READ';
$page_title = 'Product labels';
require_once('../product_functions.php');





	$tmp_sql = urldecode($_POST['tmp_sql']);
	$tmp_select_sql = urldecode($_POST['tmp_select_sql']);
	$search_sql = urldecode($_POST['search_sql']);
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
	//enable the checkbox
	for($i=0;$i<sizeof($data);$i++)
	{
		$data[$i]['row_checkbox'] = 1;
		$data[$i]['quantity'] = 1;
	}



			$file_name = 'SUBID_labels.pdf';
			$html = printProductLabelsForm($data, $file_name);	
			$html .= createOpenWinButton('Return' , POS_ENGINE_URL .'/products/ListProducts/list_product_sub_ids.php', $width = '200') .'</p>';

			include (HEADER_FILE);
			echo $html;
			include (FOOTER_FILE);
			
			
?>