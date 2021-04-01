<?php

//to download the file we need to know what and where it is....
$page_level = 5;
$page_navigation = '';
require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
if(isset($_GET['db_table']) && isset($_GET['db_id_name']) && isset($_GET['db_id_val']))
{
	$sql = "SELECT file_name, file_type, file_size, binary_content FROM ".$_GET['db_table']." WHERE ".$_GET['db_id_name']." = '".$_GET['db_id_val']."'";
         
	$file = getSQL($sql);

header("Content-length: ".$file[0]['file_size']);
header("Content-type: ".$file[0]['file_type']);
header("Content-Disposition: attachment; filename=".$file[0]['file_name']);
echo $file[0]['binary_content'];

}

?>