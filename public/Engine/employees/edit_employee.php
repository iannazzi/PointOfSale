<?php # Script 9.3 - edit_user.php

// This page is for editing a user record.
// This page is accessed through view_users.php.

$page_title = 'Edit a User';
$page_level = 7;
$page_navigation = 'employees';
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
include (HEADER_FILE);

echo '<h1>Edit an Employee</h1>';

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

	$errors = array();
	
	// Check for a first name:
	if (empty($_POST['first_name'])) {
		$errors[] = 'You forgot to enter your first name.';
	} else {
		$fn = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
	}
	
	// Check for a last name:
	if (empty($_POST['last_name'])) {
		$errors[] = 'You forgot to enter your last name.';
	} else {
		$ln = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
	}
	
	// Check for an email address:
	if (empty($_POST['email'])) {
		$errors[] = 'You forgot to enter your email address.';
	} else {
		$e = mysqli_real_escape_string($dbc, trim($_POST['email']));
	}
	
	$default_sid = $_POST['empStoreSelect'];
	if (empty($errors)) { // If everything's OK.
	
		//  Test for unique email address:
		$q = "SELECT pos_user_id FROM pos_usersWHERE email='$e' AND pos_user_id != $id";
		$r = @mysqli_query($dbc, $q);
		if (mysqli_num_rows($r) == 0) {

			// Make the query:
			$q = "UPDATE pos_usersSET first_name='$fn', last_name='$ln', email='$e', default_store_id='$default_sid' WHERE pos_user_id=$id LIMIT 1";
			$r = @mysqli_query ($dbc, $q);
			if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
			
				// Print a message:
				echo '<p>The employee has been edited. Please log out then back in for changes to take effect.</p>';	
				// log the user out
							
			} else { // If it did not run OK.
				echo '<p class="error">The employee could not be edited due to a system error. We apologize for any inconvenience.</p>'; // Public message.
				echo '<p>' . mysqli_error($dbc) . '<br />Query: ' . $q . '</p>'; // Debugging message.
			}
				
		} else { // Already registered.
			echo '<p class="error">The email address has already been registered.</p>';
		}
		
	} else { // Report the errors.
	
		echo '<p class="error">The following error(s) occurred:<br />';
		foreach ($errors as $msg) { // Print each error.
			echo " - $msg<br />\n";
		}
		echo '</p><p>Please try again.</p>';
		
	} // End of if (empty($errors)) IF.

} // End of submit conditional.

// Always show the form...

// Retrieve the user's information:
$q = "SELECT first_name, last_name, email, default_store_id FROM pos_usersWHERE pos_user_id=$id";	

$r = @mysqli_query ($dbc, $q);

if (mysqli_num_rows($r) == 1) { // Valid user ID, show the form.

	// Get the user's information:
	$row = mysqli_fetch_array ($r, MYSQLI_NUM);
	
	// Create the form:
	echo '<form action="edit_employee.php" method="post">
<p>First Name: <input type="text" name="first_name" size="15" maxlength="15" value="' . $row[0] . '" /></p>
<p>Last Name: <input type="text" name="last_name" size="15" maxlength="30" value="' . $row[1] . '" /></p>
<p>Email Address: <input type="text" name="email" size="20" maxlength="40" value="' . $row[2] . '"  /> </p>';
// add the store select
echo '<p> Default Store Where You are Working: <select name="empStoreSelect">';
echo '<option value="0">Select Store</option>';

$store_q = "SELECT pos_store_id, company, store_name FROM pos_stores";	
$store_r = @mysqli_query ($dbc, $store_q);

while ($store_row = mysqli_fetch_array($store_r, MYSQLI_ASSOC))
	{
		
		echo '<option value="' . $store_row['pos_store_id'] . '"';
		//default
		if ($row[3] == $store_row['pos_store_id']) echo ' selected="selected"';
		echo '>' . $store_row['store_name'] . '</option>';
		
	}


echo '</select></p>';

echo '<p><input type="submit" name="submit" value="Submit" /></p>
<input type="hidden" name="submitted" value="TRUE" />
<input type="hidden" name="id" value="' . $id . '" />
</form>';

} else { // Not a valid user ID.
	echo '<p class="error">This page has been accessed in error.</p>';
}

mysqli_close($dbc);
		
include (FOOTER_FILE);
?>
