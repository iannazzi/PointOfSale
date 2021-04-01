<?php # Script 8.5 - register.php #2

$page_title = 'Register Employee';

$page_level = 7;
$page_navigation = 'employees';
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
include (HEADER_FILE);

require_once(MYSQL_POS_CONNECT_FILE);
$dbc = pos_connection();

// Check if the form has been submitted:
if (isset($_POST['submitted'])) {


		
	$errors = array(); // Initialize an error array.
	
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
	// Check for a level:
	if (empty($_POST['level'])) {
		$errors[] = 'You forgot to enter employee level.';
	} else {
		$level = mysqli_real_escape_string($dbc, trim($_POST['level']));
	}
	// Check for a login:
	if (empty($_POST['login'])) {
		$errors[] = 'You forgot to enter your login ID.';
	} else {
		$login = mysqli_real_escape_string($dbc, trim($_POST['login']));
	}
	// Check for an email address:
	if (empty($_POST['email'])) {
		$errors[] = 'You forgot to enter your email address.';
	} else {
		$e = mysqli_real_escape_string($dbc, trim($_POST['email']));
	}
	
	// Check for a password and match against the confirmed password:
	if (!empty($_POST['pass1'])) {
		if ($_POST['pass1'] != $_POST['pass2']) {
			$errors[] = 'Your password did not match the confirmed password.';
		} else {
			$p = mysqli_real_escape_string($dbc, trim($_POST['pass1']));
		}
	} else {
		$errors[] = 'You forgot to enter your password.';
	}
	// Grab the other stuff if it is there.. it is not totally needed:
	$phone = mysqli_real_escape_string($dbc, trim($_POST['phone']));
	$dsid = $_POST['empStoreSelect'];
	
	if (empty($errors)) { // Test for uniqueness
	
		//  Test for unique email address OR Unique Login
		$q = "SELECT pos_user_id FROM pos_usersWHERE email='$e' OR login = '$login'";
		$r = @mysqli_query($dbc, $q);
		if (mysqli_num_rows($r) == 0) {
			// Register the user in the database...
			// Make the query:
			$q = "INSERT INTO pos_users(first_name, last_name, email, password, created_date, login, active, level, phone, default_store_id) VALUES ('$fn', '$ln', '$e', SHA1('$p'), NOW(), '$login', '1', '$level', '$phone', '$dsid')";		
			$r = @mysqli_query ($dbc, $q); // Run the query.
			if ($r) { // If it ran OK.
			
				// Print a message:
				echo '<h1>Thank you!</h1>
			<p>' . $fn . ' ' . $ln . ' is now registered.</p><p><br /></p>';	
			
			} else { // there is already the email or user name
			echo '<h1>System Error</h1>
			<p class="error">You could not be registered due similar email or login. We apologize for any inconvenience.</p>'; 
		} //end of if (mysqli_num_rows($r) == 0)
		} else { // If it did not run OK.
			
			// Public message:
			echo '<h1>System Error</h1>
			<p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>'; 
			
			// Debugging message:
			echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
						
		} // End of if ($r) IF.
		
		mysqli_close($dbc); // Close the database connection.

		// Include the footer and quit the script:
		include (FOOTER_FILE); 
		exit();
		
	} else { // Report the errors.
	
		echo '<h1>Error!</h1>
		<p class="error">The following error(s) occurred:<br />';
		foreach ($errors as $msg) { // Print each error.
			echo " - $msg<br />\n";
		}
		echo '</p><p>Please try again.</p><p><br /></p>';
		
	} // End of if (empty($errors)) IF.
	
	mysqli_close($dbc); // Close the database connection.

} // End of the main Submit conditional.
?>
<h1>Register</h1>
<form action="register_employee.php" method="post">
	<p>First Name: <input type="text" name="first_name" size="15" maxlength="20" value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>" /></p>
	
	<p>Last Name: <input type="text" name="last_name" size="15" maxlength="40" value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>" /></p>
	
	
	<?php 
	// add the store select
	echo '<p> Default Store Where You are Working: <select name="empStoreSelect">';
	echo '<option value="0">Select Store</option>';

	$store_q = "SELECT pos_store_id, company, store_name FROM pos_stores";	
	$store_r = @mysqli_query ($dbc, $store_q);

	while ($store_row = mysqli_fetch_array($store_r, MYSQLI_ASSOC))
	{
		
		echo '<option value="' . $store_row['pos_store_id'] . '"';
		//default
		echo '>' . $store_row['store_name'] . '</option>';
		
	}


	echo '</select></p>'; ?>

	<p>Login ID: <input type="text" name="login" size="15" maxlength="40" value="<?php if (isset($_POST['login'])) echo $_POST['login']; ?>" /></p>
	
	<p>Level: <input type="text" name="level" size="15" maxlength="40" value="<?php if (isset($_POST['level'])) echo $_POST['level']; ?>" /></p>
	
	<p>Email Address: <input type="text" name="email" size="20" maxlength="80" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>"  /> </p>
	
		<p>Phone: <input type="text" name="phone" size="20" maxlength="80" value="<?php if (isset($_POST['phone'])) echo $_POST['phone']; ?>"  /> </p>
		
	<p>Password: <input type="password" name="pass1" size="10" maxlength="20" /></p>

	<p>Confirm Password: <input type="password" name="pass2" size="10" maxlength="20" /></p>
	<p><input type="submit" name="submit" value="Register" /></p>
	<input type="hidden" name="submitted" value="TRUE" />
</form>
<?php
include (FOOTER_FILE);
?>
