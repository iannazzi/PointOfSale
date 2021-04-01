<?


$page_title = 'Users';
//need this binder name to check if user has access to this page...
$binder_name = 'System User Accounts';
require_once ('../user_functions.php');

$pos_user_id = $_GET['pos_user_id'];
$date = $_GET['date'];

$sql = "SELECT time(time) as time, url, ip_address, browser FROM pos_user_log WHERE pos_user_id = $pos_user_id and DATE(time) = '".$date."'";
$data = getSQL($sql);

$table_columns = array(
		
		array(
			'th' => 'time',
			'mysql_field' => 'time',
			'sort' => 'time'),
			array(
			'th' => 'url',
			'mysql_field' => 'url',
			'sort' => 'url'),
		array(
			'th' => 'Browser',
			'mysql_field' => 'browser',
			'sort' => 'browser'),
		
		array(
			'th' => 'IP Address',
			'mysql_field' => 'ip_address',
			'sort' => 'ip_address'),
		
		
		);
$html = '<h2>User activity Table for ' . $date . '</h2>';
$html .= createRecordsTable($data, $table_columns);
include(HEADER_FILE);
echo $html;
include(FOOTER_FILE);


?>