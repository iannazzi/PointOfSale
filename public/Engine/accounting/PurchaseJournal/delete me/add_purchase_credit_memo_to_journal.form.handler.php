<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);

if (isset($_POST['submit'])) 
{

	
	$table_def_array = deserializeTableDef($_POST['table_def_array']);
	//array[0] is the first table - need to add a hidden field to the table
	//need to add the manufacturer id
	$pos_mfg_id = array(array('db_field' => 'pos_manufacturer_id', 'type' => 'input'));

	//$pos_account_id =  getManufacturerAccount($_POST['pos_manufacturer_id']);		
	$table_def_array[0] = array_merge($table_def_array[0], $pos_mfg_id);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array[0]);	
	$other_info_array = array('invoice_entry_date' => getCurrentTime(),
										'pos_user_id' => $_SESSION['pos_user_id'],
										'invoice_type' => 'Credit Memo',
										'payment_status' => 'UNUSED',
										'pos_account_id' =>$_POST['pos_account_id']);
		
	$insert = array_merge($insert, $other_info_array);
	var_dump($insert);
	$date = date('Y:m:d H:i:s');
	$dbc = startTransaction();
	
	$pos_purchases_journal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_purchases_journal', $insert);
	$key_val_id['pos_purchases_journal_id'] = $pos_purchases_journal_id;
	list($results[], $results[]) = transactionGetAndInsertMultiSelect($dbc,'pos_purchase_order_id', 'pos_purchases_invoice_to_po', $key_val_id);
	$post_name_for_file = 'file_name';
	if (isset($_FILES[$post_name_for_file]['size']) && $_FILES[$post_name_for_file]['size'] > 0)
	{
		$file_array = fileUploadHandler($post_name_for_file, UPLOAD_FILE_PATH .'/invoice_uploads');
		
		//$fileName = $_FILES[$post_name_for_file]['name'];
		//$tmpName  = $_FILES[$post_name_for_file]['tmp_name'];
		//$fileSize = $_FILES[$post_name_for_file]['size'];
		//$fileType = $_FILES[$post_name_for_file]['type'];
		
		$fp = fopen($file_array['path'], 'r');
		$content = fread($fp, filesize($file_array['path']));
		$content = addslashes($content);
		fclose($fp);
		if(!get_magic_quotes_gpc())
		{
			$$file_array['name'] = addslashes($file_array['name']);
		}
		$file_data['file_name'] = $file_array['name'];
		$file_data['file_type'] = $file_array['type'];
		$file_data['file_size'] = $file_array['size'];
		$file_data['binary_content'] = $content;
		$results[] = simpleTransactionUpdateSQL($dbc, 'pos_purchases_journal', $key_val_id, $file_data);
	}
	//now try to close each purchase order
	for($i=0;$i<sizeof($_POST['pos_purchase_order_id']);$i++)
	{
		
		$pos_purchase_order_id = $_POST['pos_purchase_order_id'][$i];
		if($pos_purchase_order_id != 'false')
		{
			$po_status = tryToClosePOTransaction($dbc,$pos_purchase_order_id);
		}
	}
	$close_transaction = simpleCommitTransaction($dbc);
	$message = urlencode(getManufacturerName($_POST['pos_manufacturer_id']) . ' Credit Memo # ' . $insert['invoice_number'] . " has been added");
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);		
}

	
?>
