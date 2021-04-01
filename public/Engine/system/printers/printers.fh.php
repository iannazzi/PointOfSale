<?php
	/* discount form handler
	craig iannazzi 2-9-13 my bro is 40!! piece of shizz
	*/
$binder_name = 'Printers';
$access_type = 'WRITE';
require_once ('../system_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_printer_id']);
	//if it is new then insert, otherwise update.
	
	
	if($_POST['pos_printer_id'] == 'TBD')
	{
		$pos_printer_id = simpleTransactionInsertSQLReturnID($dbc,'pos_printers', $insert);
		
		//now automatically create the name and the cookie name.....
		$charset = "ABCDEFGHJKMNPRSTWXY345678";
		$printer_name = generateUniqueName(5,$charset);
		//now we try to insert it
		$sql = "UPDATE pos_printers SET printer_name='$printer_name' WHERE pos_printer_id = $pos_printer_id";
		$result = @mysqli_query($dbc, $sql);
		WHILE (!$result) 
		{ // If it ran OK.
			
			$terminal_name = generateUniqueName(5,$charset);
			//now we try to insert it
			$sql = "UPDATE pos_printers SET printer_name='$printer_name' WHERE pos_printer_id = $pos_printer_id";
			$result = @mysqli_query($dbc, $sql);

		}	
		
		
		
		
		
		
		$message = urlencode('Printer Name ' . $printer_name . ' and ID '.$pos_printer_id.' has been added');
		
		
		
		
		
		
	}
	else
	{
		//this is an update
		$pos_printer_id = getPostOrGetID('pos_printer_id');
		$key_val_id['pos_printer_id'] = $pos_printer_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_printers', $key_val_id, $insert);
		$message = urlencode('Printer ID ' . $pos_printer_id . " has been updated");
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
