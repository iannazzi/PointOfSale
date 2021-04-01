<?php
/*****************************STORES*****************************************************/
function getStore($pos_store_id)
{
    $dbc = openPOSDatabase();
	$shipto_store_sql = "SELECT * FROM pos_stores WHERE pos_store_id ='" . $pos_store_id . "'"; 
	$shipto_selected_store_r = @mysqli_query ($dbc, $shipto_store_sql);
	$shipto_selected_store = convert_mysql_result_to_array($shipto_selected_store_r);
	mysqli_close($dbc);
	return $shipto_selected_store;
}
function getStoreName($pos_store_id)
{
	$sql = "SELECT store_name FROM pos_stores WHERE pos_store_id ='" . $pos_store_id . "'"; 
	return getSingleValueSQL($sql);
}
function getTransactionStoreName($dbc, $pos_store_id)
{
	$sql = "SELECT store_name FROM pos_stores WHERE pos_store_id ='" . $pos_store_id . "'"; 
	$store_result = getTransactionSQL($dbc,$sql);
	return $store_result[0]['store_name'];	
}
function getStoresAndCompanies()
{
	 $store_sql = "SELECT * FROM pos_stores WHERE active=1";
	 return getSQL($store_sql);
}
function getStores($company)
{
    $dbc = openPOSDatabase();
    $store_sql = "SELECT * FROM pos_stores WHERE active = 1 AND company='" . $company ."'";
	$store_r = @mysqli_query ($dbc, $store_sql);
	$stores =  convert_mysql_result_to_array($store_r);
	mysqli_close($dbc);
	return $stores;

}
?>