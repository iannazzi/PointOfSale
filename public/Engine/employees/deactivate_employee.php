<?php # Script 9.2 - delete_user.php

// This page is for deleting an emplyee record.
// This page is accessed through view_employees.php.

$page_title = 'De-activivate a User';

$page_level = 6;
$page_navigation = 'employees';
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
include (HEADER_FILE);

echo '<h1>Delete an Employee</h1>';

// Check for a valid user ID, through GET or POST:
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From view_employees.php
	$id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Form submission.
	$id = $_POST['id'];
} else { // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	include ('../includes/footer.html'); 
	exit();
}

require_once(MYSQL_POS_CONNECT_FILE);
$dbc = pos_connection();


// Check if the form has been submitted:
if (isset($_POST['submitted'])) {

	if ($_POST['sure'] == 'Yes') { // Delete the record.

		// Make the query:
		$q = "UPDATE pos_usersSET active = '0' WHERE pos_user_id=$id LIMIT 1";		
		$r = @mysqli_query ($dbc, $q);
		if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
		
			// Print a message:
			echo '<p>The employee has been deleted.</p>';	
		
		} else { // If the query did not run OK.
			echo '<p class="error">The user could not be deleted due to a system error.</p>'; // Public message.
			echo '<p>' . mysqli_error($dbc) . '<br />Query: ' . $q . '</p>'; // Debugging message.
		}
	
	} else { // No confirmation of deletion.
		echo '<p>The employee has NOT been deleted.</p>';	
	}

} else { // Show the form.

	// Retrieve the user's information:
	$q = "SELECT CONCAT(last_name, ', ', first_name) FROM pos_usersWHERE pos_user_id=$id";
	$r = @mysqli_query ($dbc, $q);
	
	if (mysqli_num_rows($r) == 1) { // Valid user ID, show the form.

		// Get the user's information:
		$row = mysqli_fetch_array ($r, MYSQLI_NUM);
		
		// Create the form:
		echo '<form action="delete_employee.php" method="post">
	<h3>Name: ' . $row[0] . '</h3>
	<p>Are you sure you want to delete this employee?<br />
	<input type="radio" name="sure" value="Yes" /> Yes 
	<input type="radio" name="sure" value="No" checked="checked" /> No</p>
	<p><input type="submit" name="submit" value="Submit" /></p>
	<input type="hidden" name="submitted" value="TRUE" />
	<input type="hidden" name="id" value="' . $id . '" />
	</form>';
	
	} else { // Not a valid user ID.
		echo '<p class="error">This page has been accessed in error.</p>';
	}

} // End of the main submission conditional.

mysqli_close($dbc);
		
include (FOOTER_FILE);
?>
