<?php
$binder_name = 'System User Accounts';
require_once ('../user_functions.php');
$pos_user_id = getPostOrGetID('pos_user_id');
//var_dump($_POST);
if (isset($_POST['submit']))
{
	//delete all binders
		$dbc = startTransaction();

	$sql = "DELETE FROM pos_user_binder_access WHERE pos_user_id = $pos_user_id";
	$res = runTransactionSQL($dbc,$sql);
	//insert all new binders
	$binders = loadSystemBinders();
	for($binder=0;$binder<sizeof($binders);$binder++)
	{
		if(isset($_POST[$binders[$binder]['pos_binder_id'] . '_check']))
		{
			$insert['pos_user_id'] = $pos_user_id;
			$insert['pos_binder_id'] = $binders[$binder]['pos_binder_id'];
			$insert['access'] = $_POST[$binders[$binder]['pos_binder_id'] . '_access'];
			$pos_binder_access_id = simpleTransactionInsertSQLReturnID($dbc,'pos_user_binder_access', $insert);
		}
	}
		simpleCommitTransaction($dbc);

		header('Location: manage_user.php?type=view&pos_user_id='.$pos_user_id);

}
else
{
	header('Location: manage_user.php?type=view&pos_user_id='.$pos_user_id);
}


?>