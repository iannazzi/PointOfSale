<?php
/*
	handbook.php
	
	*/
//$binder_name = 'Employee Handbook';
$access_type = 'READ';
require_once ('../employee_functions.php');	
$page_title = 'Employee Handbook';

include (HEADER_FILE);
echo '<p>Enjoy the following read (click the link to open the .pdf file)</p>';
echo '<a href = "Embrasse-Moi_Handbookv2009-1-15.pdf">Employee Hand Book</a>';
include (FOOTER_FILE);	
	
?>