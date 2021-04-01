<?php # Script 16.11 - change_password.php
// This page allows a logged-in user to change their password.

$page_title = 'Change Password For ' . ($_SESSION['first_name']) . ' ' . ($_SESSION['last_name']);

$page_level = 7;
$page_navigation = 'employees';
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
include (HEADER_FILE);


// If no first_name session variable exists, redirect the user:
if (!isset($_SESSION['first_name'])) {
	
	$url = POS_ENGINE_URL . '/index.php'; // Define the URL.
	ob_end_clean(); // Delete the buffer.
	header("Location: $url");
	exit(); // Quit the script.
	
}

if (isset($_POST['submitted'])) {
	require_once(MYSQL_POS_CONNECT_FILE);
	$dbc = pos_connection();

			
	// Check for a new password and match against the confirmed password:
	$p = FALSE;
	if (preg_match ('/^(\w){4,20}$/', $_POST['password1']) ) {
		if ($_POST['password1'] == $_POST['password2']) {
			$p = mysqli_real_escape_string ($dbc, $_POST['password1']);
		} else {
			echo '<p class="error">Your password did not match the confirmed password!</p>';
		}
	} else {
		echo '<p class="error">Please enter a valid password!</p>';
	}
	
	if ($p) { // If everything's OK.

		// Make the query.
		$q = "UPDATE pos_usersSET password=SHA1('$p') WHERE pos_user_id={$_SESSION['pos_user_id']} LIMIT 1";	
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
		if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
		
			// Send an email, if desired.
			echo '<h3>Your password has been changed.</h3>';
			mysqli_close($dbc); // Close the database connection.
			include ('../includes/footer.html'); // Include the HTML footer.
			exit();
			
		} else { // If it did not run OK.
		
			echo '<p class="error">Your password was not changed. Make sure your new password is different than the current password. Contact the system administrator if you think an error occurred.</p>'; 

		}

	} else { // Failed the validation test.
		echo '<p class="error">Please try again.</p>';		
	}
	
	mysqli_close($dbc); // Close the database connection.

} // End of the main Submit conditional.

?>

<h1>Change Password for <?php echo  $_SESSION['first_name'] ?></h1>
<form action="change_password.php" method="post">
	<fieldset>
	<p><b>New Password:</b> <input type="password" name="password1" size="20" maxlength="20" /> <small>Use only letters, numbers, and the underscore. Must be between 4 and 20 characters long.</small></p>
	<p><b>Confirm New Password:</b> <input type="password" name="password2" size="20" maxlength="20" /></p>
	</fieldset>
	<div align="center"><input type="submit" name="submit" value="Change My Password" /></div>
	<input type="hidden" name="submitted" value="TRUE" />
</form>

<?php
include (FOOTER_FILE);
?>
