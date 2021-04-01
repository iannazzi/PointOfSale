<?php
/*
 .---------------- minute (0 - 59) 
# |  .------------- hour (0 - 23)
# |  |  .---------- day of month (1 - 31)
# |  |  |  .------- month (1 - 12) OR jan,feb,mar,apr ... 
# |  |  |  |  .---- day of week (0 - 6) (Sunday=0 or 7)  OR sun,mon,tue,wed,thu,fri,sat 
# |  |  |  |  |
# *  *  *  *  *  command to be executed

  30 3  *  *  *  php /home/scripts/do_something.php
  In some cases thou, depending on what kind of script this "do_something.php" is, it would be better to execute it inside the webserver so the option would be to replace "php /home/scripts/do_something.php" with "wget your.domain.com/do_something.php"; – rasjani Jul 29 '09 at 9:05
  
  
  so the command is:  wget http://www.craigiannazzi.com/POS_TEST/Engine/utilities/dailyTasks/scheduled_tasks.php
  do not use https
  
  

*/
$page_title = 'Tasks';
$page_level = 3;
$page_navigation = 'utilities';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
$type = $_GET['type'];

if($type=='daily')
{
	google_feed();
	auth_net_batch();
	//batch
	
}
elseif($type == 'hourly')
{
}
elseif($type == 'weekly')
{
}
elseif($type == 'monthly')
{
}
elseif($type == 'yearly')
{
}

//backup_script();
//email_status();

function backup_script()
{
	//backup
	$file_name = 'embrasse-moi-pos-db-backup-'.time() .'.sql';
	$path = BACKUP_PATH;
	writeDataToFile($file_name, $path, backup_pos_tables());
	
}
function email_status()
{
	$msg = '<p>Executed Scheduled Tasks for '.POS_URL.'</p>';
	if (LIVE)
	{
		$to = "craig.iannazzi@embrasse-moi.com, craig.iannazzi@gmail.com,";
	}
	else
	{
		$to = ADMIN_EMAIL;
		//$to = 'craig.iannazzi@embrasse-moi.com';
	}
	
	$from = ADMIN_EMAIL;
	$subject = 'System Cron ' .date('Y-m-d');
	$headers = "From: " . $from . "\r\n";
	$headers .= "Reply-To: ". $from . "\r\n";
	if (LIVE) $headers .= "CC: " . ADMIN_EMAIL . "\r\n";
	$headers  .= 'MIME-Version: 1.0' . "\r\n";
	
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'System Cron <' . $from . '>' . "\r\n";
	
	mail('craig.iannazzi@embrasse-moi.com', $subject, $msg, $headers);
}
function google_feed()
{
	$filename = BASE_PATH . '/google_feed.txt'; 
	$file_url = BASE_URL . '/google_feed.txt';
	$feed_array = createGoogleBaseFeedArray();
	arrayToTABCSV($filename, $feed_array, $save_keys=false);
}
function transaction_batch()
{
	
	require_once(AUTHORIZE_NET_LIBRARY);
	$request = new AuthorizeNetTD;
	$api_login = getAPILoginID($pos_payment_gateway_id);
	$transaction_key = getTrasactionKey($pos_payment_gateway_id);
			
	$sql = "SELECT pos_customer_payment_id, transaction_id FROM pos_customer_payments WHERE batch_id = '' AND transaction_id != ''";
	$data = getSQL($sql);
	for($i=0;$i<sizeof($data);$i++)
	{
		$transactionId = $data[$i]['transaction_id'];
		$response = $request->getTransactionDetails($transactionId);
		echo $response->xml->transaction->transactionStatus;
		$pos_customer_payment_id = $data[$i]['pos_customer_payment_id'];
		$batch_id = ''
		$insert = "UPDATE pos_customer_payments SET batch_id = '$batch_id' WHERE pos_customer_payment_id = '$pos_cusomter_payment_id'";
		runSQL($insert);
	}
}
//include(HEADER_FILE);
//include(FOOTER_FILE);


?>