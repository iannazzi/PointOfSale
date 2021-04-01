<?php
/* the purpose of this file is to allow ajax to call it, and the output will be dumped out in html format

 I am leaving this unfinished because I will loose URL information ajaxing everything, which is bad....
*/

require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);

//we should probably only post to this......
$html = '';
$switch = $_POST['switch'];

switch ($switch) 
{
    case 'RECORDS_TABLE':
        $tmp_sql = $_POST['tmp_sql'];
        $tmp_select_sql = $_POST['tmp_select_sql'];
        $table_columns = $_POST['table_columns'];
        $search_fields = $_POST['search_fields'];
        
        $search_values = $_POST['search_values'];
        
        $tmp_select_sql  .= createSearchSQLStringMultipleDates($search_fields);
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";
$tmp_select_sql  .=  " LIMIT 20";


        
        $dbc = openPOSdb();
		$result = runTransactionSQL($dbc,$tmp_sql);
		$data = getTransactionSQL($dbc,$tmp_select_sql);
		closeDB($dbc);
		$html .= createRecordsTable($data, $table_columns);
        
        break;
    case 'RECORDS_TABLE_WITH_TOTALS':
         $sql = $_POST['sql'];
         break;
    case else
    	break
}

echo $html;

?>