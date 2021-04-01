<?php

//$type = $_GET['type'];
$binder_name = 'Server Configuration';
$access_type = 'READ';
require_once ('../system_functions.php');

include(HEADER_FILE);
echo'<input class = "button" style="width:200px;" type="button" name="JQUERY" value="JQUERY TEST PAGE" onclick="open_win(\'../testing/jquery.php\')"/>';

phpinfo();
include(FOOTER_FILE);
?>