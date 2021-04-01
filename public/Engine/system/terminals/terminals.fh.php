<?php
	/* discount form handler
	craig iannazzi 2-9-13 my bro is 40!! piece of shizz
	*/
$binder_name = 'Terminals';
$access_type = 'WRITE';
require_once ('../system_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_terminal_id']);
	//if it is new then insert, otherwise update.
	$charset = "ABCDEFGHJKMNPRSTWXY345678";
	
	if($_POST['pos_terminal_id'] == 'TBD')
	{
		$pos_terminal_id = simpleTransactionInsertSQLReturnID($dbc,'pos_terminals', $insert);
		//now automatically create the name and the cookie name.....
		$terminal_name = generateUniqueName(5,$charset);
		//now we try to insert it
		$sql = "UPDATE pos_terminals SET terminal_name='$terminal_name', cookie_name='$terminal_name' WHERE pos_terminal_id = $pos_terminal_id";
		$result = @mysqli_query($dbc, $sql);
		WHILE (!$result) 
		{ // If it ran OK.
			
			$terminal_name = generateUniqueName(5,$charset);
			//now we try to insert it
			$sql = "UPDATE pos_terminals SET terminal_name='$terminal_name', cookie_name='$terminal_name' WHERE pos_terminal_id = $pos_terminal_id";
			$result = @mysqli_query($dbc, $sql);

		}	

		$message = urlencode('Terminal Name ' . $terminal_name . ' and ID '.$pos_terminal_id.' has been added');
	}
	else
	{
		//this is an update
		$pos_terminal_id = getPostOrGetID('pos_terminal_id');
		$key_val_id['pos_terminal_id'] = $pos_terminal_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_terminals', $key_val_id, $insert);
		$message = urlencode('Terminal ID ' . $pos_terminal_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);
}
else
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}

		
?>
