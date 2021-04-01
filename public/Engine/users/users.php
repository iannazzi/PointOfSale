<?PHP
/*
*/
$page_level = 0;
$page_navigation = 'accounting';
$page_title = 'Accounting';
require_once ('../../Config/config.inc.php');
require_once (PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);

//If the user is an admin then list the users, otherwise direct the user to the user specific page

	$msg = (isset($_GET['message'])) ? '&message='.$_GET['message'] : '';
	header('Location: UserAccountSettings/user_settings.php?type=View&pos_user_id='.$_SESSION['pos_user_id'].$msg);	



?>