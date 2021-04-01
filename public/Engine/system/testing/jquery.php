<?
/*
		what uses jquery????
		
		jquery ui???
		datepicker

*/
$binder_name = 'Server Configuration';
$access_type = 'READ';
require_once ('../system_functions.php');
include(HEADER_FILE);
echo '<P> JQUERY VERSION: ' . JQUERY_VERSION . '</p>';
echo '<P> JQUERY UI VERSION: ' .JQUERY_UI_VERSION . '</p>';
echo '<p>DATE SELECT: ' . dateSelect('test','','') .'</p>';

include(FOOTER_FILE);

?>