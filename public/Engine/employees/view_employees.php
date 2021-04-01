<?php # Script 9.4 - #4

// This script retrieves all the records from the users table.
// This version paginates the query results.
$page_title = 'View the Current Employees';

$page_level = 7;
$page_navigation = 'employees';
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);


include (HEADER_FILE);


echo '<h1>Registered Employees</h1>';
echo '<p><a href="register_employee.php">Add an Employee</a></p>';
require_once(MYSQL_POS_CONNECT_FILE);
$dbc = pos_connection();

// Number of records to show per page:
$display = 10;

// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.

	$pages = $_GET['p'];

} else { // Need to determine.

 	// Count the number of records:
	$q = "SELECT COUNT(pos_user_id) FROM pos_employees";
	$r = @mysqli_query ($dbc, $q);
	$row = @mysqli_fetch_array ($r, MYSQLI_NUM);
	$records = $row[0];

	// Calculate the number of pages...
	if ($records > $display) { // More than 1 page.
		$pages = ceil ($records/$display);
	} else {
		$pages = 1;
	}
	
} // End of p IF.

// Determine where in the database to start returning results...
if (isset($_GET['s']) && is_numeric($_GET['s'])) {
	$start = $_GET['s'];
} else {
	$start = 0;
}
		
// Make the query:
$q = "SELECT last_name, first_name, DATE_FORMAT(created_date, '%M %d, %Y') AS dr, pos_user_id, active, email, level, login, phone, default_store_id FROM pos_usersORDER BY last_name ASC LIMIT $start, $display";		
$r = @mysqli_query ($dbc, $q);

// Table header:
echo '<table align="center" cellspacing="0" cellpadding="5" width="75%">
<tr>
	<td align="left"><b>Edit</b></td>
	<td align="left"><b>Delete</b></td>
	<td align="left"><b>Last Name</b></td>
	<td align="left"><b>First Name</b></td>
	<td align="left"><b>Store</b></td>	
	<td align="left"><b>Login ID</b></td>
	<td align="left"><b>Active</b></td>
	<td align="left"><b>Level</b></td>
	<td align="left"><b>Phone Number</b></td>
	<td align="left"><b>Email</b></td>
	<td align="left"><b>Date Registered</b></td>
</tr>
';

// Fetch and print all the records....

$bg = '#eeeeee'; // Set the initial background color.

while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

	$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee'); // Switch the background color.
	
	echo '<tr bgcolor="' . $bg . '">
		<td align="left"><a href="edit_employee.php?id=' . $row['pos_user_id'] . '">Edit</a></td>
		<td align="left"><a href="delete_employee.php?id=' . $row['pos_user_id'] . '">Delete</a></td>
		<td align="left">' . $row['last_name'] . '</td>
		<td align="left">' . $row['first_name'] . '</td>';
		//get the store name
		$dsid = $row['default_store_id'];
		if ($dsid != 0)
		{
			$store_q = "SELECT store_name FROM pos_stores WHERE pos_store_id = '$dsid'";
			$store_r = @mysqli_query ($dbc, $store_q);
			$store_row = mysqli_fetch_array($store_r, MYSQLI_ASSOC);
			echo '<td align="left">' . $store_row['store_name'] . '</td>';
		} else 
		{
			echo '<td align="left"></td>';
		}
		echo '
		<td align="left">' . $row['login'] . '</td>
		<td align="left">' . $row['active'] . '</td>
		<td align="left">' . $row['level'] . '</td>
		<td align="left">' . $row['phone'] . '</td>
		<td align="left">' . $row['email'] . '</td>
		<td align="left">' . $row['dr'] . '</td>
	</tr>
	';
	
} // End of WHILE loop.

echo '</table>';
mysqli_free_result ($r);
mysqli_close($dbc);

// Make the links to other pages, if necessary.
if ($pages > 1) {
	
	// Add some spacing and start a paragraph:
	echo '<br /><p>';
	
	// Determine what page the script is on:	
	$current_page = ($start/$display) + 1;
	
	// If it's not the first page, make a Previous button:
	if ($current_page != 1) {
		echo '<a href="view_employees.php?s=' . ($start - $display) . '&p=' . $pages . '">Previous</a> ';
	}
	
	// Make all the numbered pages:
	for ($i = 1; $i <= $pages; $i++) {
		if ($i != $current_page) {
			echo '<a href="view_employees.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . '">' . $i . '</a> ';
		} else {
			echo $i . ' ';
		}
	} // End of FOR loop.
	
	// If it's not the last page, make a Next button:
	if ($current_page != $pages) {
		echo '<a href="view_employees.php?s=' . ($start + $display) . '&p=' . $pages . '">Next</a>';
	}
	
	echo '</p>'; // Close the paragraph.
	
} // End of links section.
	
include (FOOTER_FILE);
?>
