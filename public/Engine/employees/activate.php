<?php # Script 16.7 - activate.php
// This page activates the user's account.

$page_title = 'Activate Your Account';

$page_level = 7;
$page_navigation = 'employees';
$page_title = 'Business Reports';
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
include (HEADER_FILE);


// Validate $_GET['x'] and $_GET['y']:
$x = $y = FALSE;
if (isset($_GET['x']) && preg_match ('/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/', $_GET['x']) ) {
	$x = $_GET['x'];
}
if (isset($_GET['y']) && (strlen($_GET['y']) == 32 ) ) {
	$y = $_GET['y'];
}

// If $x and $y aren't correct, redirect the user.
if ($x && $y) {

	// Update the database...
	require_once(MYSQL_POS_CONNECT_FILE);
	$dbc = pos_connection();

	$q = "UPDATE pos_usersSET activation_code=NULL WHERE (email='" . mysqli_real_escape_string($dbc, $x) . "' AND activation_code='" . mysqli_real_escape_string($dbc, $y) . "') LIMIT 1";
	$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
	
	// Print a customized message:
	if (mysqli_affected_rows($dbc) == 1) {
		echo "<h3>Your account is now active. You may now log in.</h3>";
	} else {
		echo '<p class="error">Your account could not be activated. Please re-check the link or contact the system administrator.</p>'; 
	}

	mysqli_close($dbc);

} else { // Redirect.

	$url = POS_ENGINE_URL .'/index.php'; // Define the URL:
	ob_end_clean(); // Delete the buffer.
	header("Location: $url");
	exit(); // Quit the script.

} // End of main IF-ELSE.

include (FOOTER_FILE);
?>
