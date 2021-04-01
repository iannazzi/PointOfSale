<?
require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);

//https://www.embrasse-moi.com/POS_TEST/Engine/system/printers/printers.php?type=MAKE_SHELL_SCRIPT&print_queue=cefr&ACCOUNT_KEY=1234

//https://www.embrasse-moi.com/POS_TEST/Engine/system/printers/printers.php?type=DELETE&print_queue=CEF5W&ACCOUNT_KEY=1234&FILE_NAME=27356_STORE_COPY.pdf
	
	
	https://www.embrasse-moi.com/POS_TEST/Engine/system/printers/printer_queue.php?type=DELETE&print_queue=CEF5W&ACCOUNT_KEY=1234&FILE_NAME=27356_STORE_COPY.pdf
$type=$_GET['type'];
$print_queue = $_GET['print_queue'];
//account key is the future key that identifies accounts.... 
$account_key = $_GET['ACCOUNT_KEY'];

//while we are here update the ip_address of the store?


if($type == 'LIST')
{
	//the invoice_print_folder gets modified by account_key
	$invoice_print_folder = INVOICE_PRINT_FOLDER .$print_queue;
	$long_files = glob($invoice_print_folder.'/*.pdf');
	$files = array();
	foreach($long_files as $f)
	{
		$files[] = basename($f);
	}
		
	$string = implode(',', $files);
	echo $string;
	exit();
}
elseif($type=='DELETE')
{
	//here we delete the file...
	$file_name = scrubInput($_GET['FILE_NAME']);
	$invoice_print_folder = INVOICE_PRINT_FOLDER .$print_queue;
	
	
	unlink(INVOICE_PRINT_FOLDER .$print_queue.'/' .$file_name);
	echo' Deleted';
}



?>