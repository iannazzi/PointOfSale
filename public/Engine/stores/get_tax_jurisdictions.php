<?

require_once('store_functions.php');
$pos_state_id = getPostOrGetId('pos_state_id');
$tax_jurisdictions = getSQL("SELECT pos_tax_jurisdiction_id, jurisdiction_name FROM pos_tax_jurisdictions WHERE pos_state_id = $pos_state_id and local_or_state = 'Local'");

$return_array = array();
for($i=0;$i<sizeof($tax_jurisdictions);$i++)
{
	$return_array[$tax_jurisdictions[$i]['pos_tax_jurisdiction_id']] = $tax_jurisdictions[$i]['jurisdiction_name'];
}
echo json_encode($return_array) . "\n";

?>