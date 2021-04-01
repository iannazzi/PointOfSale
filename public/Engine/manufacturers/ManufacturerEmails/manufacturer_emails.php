<?php
/*
	this file will list all manufacturer emails
*/
$binder_name = 'Manufacturers';
$access_type = 'READ';
require_once ('../manufacturer_functions.php');
$page_title = 'View a Manufacturer';
include (HEADER_FILE);
$emails = getManufacturerEmails();
$email_array = array();
for($i=0;$i<sizeof($emails);$i++)
{	
	if($emails[$i]['email']!='')
	{
		$email_array[] = $emails[$i]['email'];
	}
}
//preprint($emails)
$html = implode(', ',$email_array);
echo $html;
include (FOOTER_FILE);
?>