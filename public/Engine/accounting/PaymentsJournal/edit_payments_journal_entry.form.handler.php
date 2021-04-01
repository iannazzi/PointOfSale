<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'Payments Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);
if (isset($_POST['submit'])) 
{
	$date = date('Y:m:d H:i:s');	
	$dbc = startTransaction();
	$pos_payments_journal_id = getPostOrGetID('pos_payments_journal_id');
	


checkAccountLockDate($_POST['pos_account_id'], scrubInput($_POST['payment_date']));
checkAccountLockDate($_POST['pos_payee_account_id'], scrubInput($_POST['payment_date']));
	
	$original_pj_entry = getTransactionPaymentsJournalData($dbc, $pos_payments_journal_id);
	$key_val_id['pos_payments_journal_id'] = $pos_payments_journal_id;
	$payment_insert_array = array('pos_store_id' => $_POST['pos_store_id'],
									'pos_employee_id' => $_POST['pos_employee_id'],
									'pos_account_id' => $_POST['pos_account_id'],
									'pos_payee_account_id' => $_POST['pos_payee_account_id'],
									'payment_date' => scrubInput($_POST['payment_date']),
									'payment_entry_date' =>getCurrentTime(),
									'payment_status' => 'COMPLETE',
									'pos_user_id' => $_SESSION['pos_user_id'],
									'comments' => $_POST['comments'],
									'applied_status' => $_POST['applied_status'],
									'payment_status' => $_POST['payment_status'],
									'payment_amount' => $_POST['payment_amount']
									);
	$results[] = simpleTransactionUpdateSQL($dbc,'pos_payments_journal', $key_val_id, $payment_insert_array);


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
		$results[] = simpleTransactionUpdateSQL($dbc, 'pos_general_journal', $key_val_id, $file_data);
	}


	unlockTables($dbc);
	
	$close_transaction = commitTransaction($dbc, $results);
	if($close_transaction)
	{	
		$message = urlencode('Payment Entry ' .$pos_payments_journal_id . " has been updated");
	}
	else
	{
		$message = urlencode("Transaction Error - Receipt has not been entered!");
	}
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);		
}

	
?>
