<?
require_once ('customer_functions.php');
$page_title = 'Upload customers';

$sql = "SELECT pos_customer_id FROM pos_customers WHERE 1";
$pos_customer_ids = getSQL($sql);
for ($i=0;$i<sizeof($pos_customer_ids);$i++)
//for ($i=0;$i<100;$i++)

{
	$pos_customer_id = $pos_customer_ids[$i]['pos_customer_id'];
	$comments = getSingleValueSQL("SELECT comments FROM pos_customers WHERE pos_customer_id = $pos_customer_id");
	$comment_array = explode(newline(), $comments);
	//preprint($comment_array);
	$new_comment_array = scrubInput(implode(newline(), array_unique($comment_array)));
	//preprint($new_comment_array);
	$update = "UPDATE pos_customers SET comments='$new_comment_array' WHERE pos_customer_id = $pos_customer_id";
	$result = runSQL($update);
}
	
echo 'done';
?>