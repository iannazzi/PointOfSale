<?php
require_once ('../tax_functions.php');

if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);
	
	if ($insert['pos_sales_tax_category_id'] == 'all')
	{
		$insert['pos_sales_tax_category_id'] == 0;
	}
		
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_sales_tax_rate_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_sales_tax_rate_id'] == 'TBD')
	{
		$jurisdiction = getPostOrGetValue('jurisdiction');
		//$insert['date_added'] = getCurrentTime();
		if ($jurisdiction == 'Local')
		{
			$pos_state_id = getPostOrGetId('pos_state_id');
			//need to check if the county is 'all'
			if ($insert['pos_tax_jurisdiction_id'] == 'all')
			{
				$counties = getSQL("SELECT * FROM pos_tax_jurisdictions WHERE pos_state_id =$pos_state_id AND local_or_state = 'Local'");
				if(sizeof($counties)>0)
				{
					for($i=0;$i<sizeof($counties);$i++)
					{
						$insert['pos_tax_jurisdiction_id'] = $counties[$i]['pos_tax_jurisdiction_id'];
						//$insert['local_tax'] = $counties[$i]['default_tax_rate'];
						$insert['pos_sales_tax_category_id'] = $insert['pos_sales_tax_category_id'];
						$pos_sales_tax_rate_id[] = simpleInsertSQLReturnID('pos_sales_tax_rates', $insert);
					}
					$message = urlencode('Rate ids' . implode(',',$pos_sales_tax_rate_id) . " have been added");
				}
				else
				{
					$message = urlencode('No Tax Jurisdictions Were found');
				}
			}
			else
			{
				$pos_sales_tax_rate_id = simpleTransactionInsertSQLReturnID($dbc,'pos_sales_tax_rates', $insert);
				$message = urlencode('Rate' . $pos_sales_tax_rate_id . " has been added");
			}
		}
		else 
		{
			//is the state 'all'
			$pos_sales_tax_rate_id = simpleTransactionInsertSQLReturnID($dbc,'pos_sales_tax_rates', $insert);
			$message = urlencode('Rate' . $pos_sales_tax_rate_id . " has been added");
		}
		
	}
	else
	{
		//this is an update
		$pos_sales_tax_rate_id = getPostOrGetID('pos_sales_tax_rate_id');
		$key_val_id['pos_sales_tax_rate_id'] = $pos_sales_tax_rate_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_sales_tax_rates', $key_val_id, $insert);
		$message = urlencode('Sale Tax Rate ID ' . $pos_sales_tax_rate_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);
}
else
{
	//cancel comes from javascript
	header('Location: '.$_POST['cancel_location']); 
}						
								
?>