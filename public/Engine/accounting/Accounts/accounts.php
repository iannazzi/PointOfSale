<?php


/* This is the account wizard page

	//maybe this helps?
	http://support.quickbooks.intuit.com/support/pages/inproducthelp/Core/QB2K12/ContentPackage/Core/Chart_of_Accounts/..%5C..%5C%5CCore%5CChart_of_Accounts%5Ctask_account_create.html
	
	http://support.quickbooks.intuit.com/support/pages/inproducthelp/core/qb2k12/contentpackage/core/chart_of_accounts/info_chart.html?family=pro
	
	http://www.brightpearl.com/support/topic/accounting-for-inventory

	the accounts are the "heart" of the system
	everything should touch an account
	anything with an account number should be an account.
	Shutterfly is an account
	mailchimp is an account
	gmail is an account
	CNB business checking is an account
	PPC is two accounts - one for expense and on for taking credit cards.... but it is one account
	
	accounts touch the chart of accounts...
	cnb is an equity account
	
	eveden is an accounts payable...
	
	eveden invoice is a finished goods inventory account...
	
	purchase orders resolve to invoices wchich link to a chart of account paid on an account which link to a chart of account which are paid by another account which link to a chart of account which finally resolve to withdrawl from a bank account which links to a chart of account.
	
	cash taken into the system goes into a cash drawer which is an account
	checks taken in go into a check safe which is an account
	cash in the cash drawer deposits into a safe account which is an account
	checks from multiple cash drawers condense into a check safe account
	cash safe account gets deposited into the bank, which is available for use (posts?) the very next business day and available for use the next business day
	check safe account gets deposited into the bank account which posts the next business day but is available in 10 business days...
	
	credit card processing accounts are both expense and account receivable. credit card deposits within x business days. they charge x percent of the transactions and wihdrwal that money on the 1st of each month.
	
	
	accounts need to know
	
*/
$accounts_javascript_version = 'accounts.2013.11.25.js';
$binder_name = 'Accounts';
$access_type = 'WRITE';
$page_title = 'Accounts';
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../accounting_functions.php');
$complete_location = 'list_accounts.php';
$cancel_location = 'list_accounts.php?message=Canceled';

//skipping ajax on this one... using full php

//submit means we have finished editing or adding
//continue will be set for adding steps
//adding - work through asking what type of account, then present the simplified information
//edit and view - depending on the account type get a specific table definition and only show certain values

if(ISSET($_GET['cancel']))
{
	//Cancel was pressed somewhere along the line. go to accounts.
	$message="Canceled";
	header('Location: '.$complete_location .'?message=' . $message);	

}
else if (isset($_POST['submit'])) //form handler here.....
{
		$complete_location = 'accounts.php?type=view';

		
		if(strtoupper($type) == 'ADD') // new data is coming over... we will need to create some values
		{
			$pos_account_type_id = $_POST['pos_account_type_id'];
			$parent_pos_chart_of_accounts_id = getSingleValueSQL("SELECT default_chart_of_account_id FROM pos_account_type WHERE pos_account_type_id = $pos_account_type_id");
			
			
			//if the account is a cash register then we need to create the name....
			$account_type = getAccountTypeNameFromAccountType($pos_account_type_id);
			//this is a general insert with a table def, nothing special needed....
			$dbc = startTransaction();
			$table_def_array = deserializeTableDef($_POST['table_def']);
			$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
			// add some other stuff to the basic array
			//take out things we don't want to insert to mysql
			unset($insert['pos_account_id']);
			$insert['pos_account_type_id'] = $pos_account_type_id;
			
			if($account_type == "Cash Register Account")
			{
				//basically create it here then go straight to 'edit' where we can edit it if needed.
				//cash register creation
				//create it first.
				unset($insert['account_number']);
				$pos_account_id = simpleTransactionInsertSQLReturnID($dbc,'pos_accounts', $insert);
				$message = urlencode('Account ID ' . $pos_account_id . " has been added");
				simpleCommitTransaction($dbc);
			
				$dbc = startTransaction();
				$insert2['company'] = getSetting('company_name');
				
				//$insert2['parent_pos_chart_of_accounts_id'] = getSingleValueSQL("SELECT default_chart_of_account_id FROM pos_account_type WHERE pos_account_type_id = $pos_account_type_id");
				
				$key_val_id['pos_account_id'] = $pos_account_id;
			 	simpleTransactionUpdateSQL($dbc,'pos_accounts', $key_val_id, $insert2);
			 	simpleCommitTransaction($dbc);
			 	$dbc = startTransaction();
				$account_number = craigsEncryption(generateUniqueName(5,getSafeCharset()));
				//now we try to insert it
				$sql = "UPDATE pos_accounts SET account_number='$account_number' WHERE pos_account_id = $pos_account_id";
				$result = @mysqli_query($dbc, $sql);
				WHILE (!$result) 
				{ 
					$account_number = craigsEncryption(generateUniqueName(5,getSafeCharset()));
					//now we try to insert it
				$sql = "UPDATE pos_accounts SET account_number='$account_number' WHERE pos_account_id = $pos_account_id";
					$result = @mysqli_query($dbc, $sql);
				}	
				simpleCommitTransaction($dbc);
				//now go to edit
				$message = urlencode('Register Number ' . craigsDecryption($account_number) . " has been added. Record This Number On The Register");

			}
			else
			{
				if ($account_type == "Store Credit")
				{
					$insert['company'] = getSetting('company_name');
				}
				$insert['account_number'] = craigsEncryption($_POST['account_number']);
				$pos_account_id = simpleTransactionInsertSQLReturnID($dbc,'pos_accounts', $insert);
				$message = urlencode('Account ID ' . $pos_account_id . " has been added");
				simpleCommitTransaction($dbc);
			}
				
			
		}
		else // this was an edit. the table def is sent over with the data, just update the values....
		{
			//this is an update
			$dbc = startTransaction();
			$table_def_array = deserializeTableDef($_POST['table_def']);
			$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
			// add some other stuff to the basic array
			//take out things we don't want to insert to mysql
			unset($insert['pos_account_id']);
			$insert['account_number'] = craigsEncryption($_POST['account_number']);
			$pos_account_id = getPostOrGetID('pos_account_id');
			$key_val_id['pos_account_id'] = $pos_account_id;
			$results[] = simpleTransactionUpdateSQL($dbc,'pos_accounts', $key_val_id, $insert);
			$message = urlencode('Account ID ' . $pos_account_id . " has been updated");
			simpleCommitTransaction($dbc);

		}
		
		$complete_location = 'accounts.php?type=view&pos_account_id='.$pos_account_id;
		header('Location: ' .$complete_location .'&message=' . $message);
		exit();
		
		
}
else if (isset($_GET['print_check'])) 
{
	$pos_account_id = $_GET['pos_account_id'];

	if (isCheckingAccount($pos_account_id))
	{
		$filename = 'Blank Check For Checking Account ' .getAccountName($pos_account_id) . ': ' .  getAccountNumber($pos_account_id) .'.pdf';

		$pdf = createPDFCheck($pos_account_id, false ,$filename,true);
		$pdf->Output($filename, 'I');
		
	}
	exit();
}

else //Form here
{


	//load the page based on type
	if(strtoupper($type) == 'ADD')
	{
		//we need a dynamic page that walks through creation of an account
		//$html =  '<script src="'.$accounts_javascript_version.'"></script>'.newline();
		$html = '';
	
		//1 what type of account (in english: ASSET: cc cash check expense cc processor Long Term Asset:  checking saving )?inventory vendor
	
		//we need this information to ease account creation - so in manufacturers an account will automatically link to 'inventory account'
		//coming in with just an 'add' set we should the account select....can javascript do all this?
	
		if(ISSET($_GET['pos_account_type_id']))
		{
			$pos_account_type_id = $_GET['pos_account_type_id'];
				//depending on the account type we want to pre-populate some data and show a different form for each
			//the view and edit will show everything for a bit, but this should work for edit as well.
		
		$account_type = getSingleValueSQL("SELECT account_type_name from pos_account_type WHERE pos_account_type_id=$pos_account_type_id");

			
		$header = '<p>Create Account: '.$account_type.'</p>';
		$page_title = 'Create Account';
		
		list($data_table_def, $html_instructions) = createAccountTableDef($type, 'TBD', $pos_account_type_id);	
		
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
		$big_html_table .= createHiddenInput('pos_account_type_id', $pos_account_type_id);
	
		$html = $header;
		$html .= $html_instructions;
		$form_handler = 'accounts.php';
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
		$html .= '<script>document.getElementsByName("company")[0].focus();</script>';
			
			

		
		}
		else
		{
			//Select the account type...
			$html.='<div id="account_type_form" title="">';
			$html.='<form action="accounts.php" method="GET">';
			$html.='<p>Select the Type of Account Are You Creating: &nbsp';
			//$html.= createAccountTypeSelect('pos_account_type_id', 'false', 'off', '');
			$html .= '</p>';
			

			$sql = "SELECT * FROM pos_account_type ORDER BY priority DESC";
			$accounts_types = getSQL($sql);
			$html .= '<table class ="linedTable">';
			$html .= '<tr><th>Select Account</th><th>Account Type</th><th>Description</th></tr>';
			for($i=0;$i<sizeof($accounts_types);$i++)
			{
			$html .= '<tr><td><input type="radio" name="pos_account_type_id" value="'.$accounts_types[$i]['pos_account_type_id'].'"></td><td>'.$accounts_types[$i]['caption'] .'</td><td>'.$accounts_types[$i]['description'] .'</td></tr>';

			}
			$html .= '</table>';
			$html .= createHiddenInput('type', 'add');
			$html .= '<input class = "button" name = "submit" type="submit" value="Continue"/>';
			$html .= '<input class = "button" name = "cancel" type="submit" value="Cancel"/>';

			$html.='</form>';
			$html.='</div>';
		}
	
	}
	elseif (strtoupper($type) == 'VIEW')
	{
		//this should be the view page, which will include additional functions like account activity and an edit button that
		//to avoid re-loading the page the edit button could simply replace the html...
		$pos_account_id = getPostOrGetID('pos_account_id');
		$pos_account_type_id = getAccountTypeID($pos_account_id);
		$edit_location = 'accounts.php?pos_account_id='.$pos_account_id.'&type=edit';
		//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
		$db_table = 'pos_accounts';
		$key_val_id['pos_account_id']  = $pos_account_id;
		list($data_table_def, $html_instructions) = createAccountTableDef('View', $pos_account_id, $pos_account_type_id);
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
	
		$html = printGetMessage('message');
		$html .= '<p>View Account</p>';
		//$html .= confirmDelete($delete_location);
		$html .= createHTMLTableForMYSQLData($data_table_def);
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
		$html .= '<p>';
	
	
		//account balance forward
		//$html .= '<input class = "button" type="button" style = "width:300px" name="account_balance" value="Account Opening Balances" onclick="open_win(\'../AccountBalances/list_account_balances.php\')"/>';
	
		//now the account activity.... via ajax? or does this go to a different page - probably....
		//store the search in the session - good....
		$html .='<div id="account_activity">';
		//show some search stuff
		$html .= '</div>';
	
	if (isCheckingAccount($pos_account_id))
	{
		$html .='<input class = "button" style="width:200px" type="button" name="print_button_inline" id="print_button_inline" value="Open Check" onclick="open_win(\'accounts.php?pos_account_id='.$pos_account_id.'&print_check=true\')" />';
	$html.='<div id="print_alert"></div>';
	}
	else
	{
	}
	
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Accounts" onclick="window.location = \''.$complete_location.'\'" />';
		$html .= '</p>';
	}
	elseif (strtoupper($type) == 'EDIT')
	{
		$pos_account_id = getPostOrGetID('pos_account_id');
	
		$header = '<p>EDIT Account</p>';
		$page_title = 'Edit Account';
		list($data_table_def_no_data, $htmll_instructions) = createAccountTableDef($type, $pos_account_id, getAccountTypeID($pos_account_id));	
		$db_table = 'pos_accounts';
		$key_val_id['pos_account_id'] = $pos_account_id;
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
	
		$html = $header;
		$form_handler = 'accounts.php';
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
		$html .= '<script>document.getElementsByName("printer_name")[0].focus();</script>';
	
	}
	else
	{
		//missing stuff
		$html = 'Bad URL';
	}

}


include(HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createAccountTableDef($type, $pos_account_id, $pos_account_type_id)
{

	//depending on the account type we we ship back a different table so it is not OVERWHELMING
	//accounts can have a ton of information, only some of it useful for each account type.
	if ($type == 'New')
	{
		$pos_account_id = 'TBD';
		$unique_validate = array('unique_group' => array('account_number', 'company'), 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_account_id'] = $pos_account_id;
		$unique_validate = array('unique_group' => array('account_number', 'company'), 'min_length' => 1, 'id' => $key_val_id);
	}

		$db_table = 'pos_accounts';

		$account_type = getSingleValueSQL("SELECT account_type_name from pos_account_type WHERE pos_account_type_id=$pos_account_type_id");
		
		
		//where is this done?
		$default_chart_of_account_id = getSingleValueSQL("SELECT default_chart_of_account_id FROM pos_account_type WHERE pos_account_type_id=$pos_account_type_id");
		//set up the basics: id, company, act number, name, account type, coa
		
		
		
		$html_instructions = '';
		
		
			if($account_type == "Credit Card" || $account_type == "Short Term Liability")
			{
				$table_def =array(
						array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
						array( 'db_field' => 'company',
								'caption' => 'Easy to type nick name for quick drop down selection',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'encrypted' => 1,
								'validate' => $unique_validate,
								'db_table' => $db_table),
						/*array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', $pos_account_type_id, 'off', ' onchange="alert(\' Changing the account type will require a bit of manual intervention because it is kinda a big deal... so be sure this is what you want to do. After Changing the Account Type Press Submit. This will reload the correct options for the account. Then you can edit the account again... the options for editing will now be different.\')" '),
								'validate' => 	array('select_value' => 'false'))*/
								
								array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Sub Account To Chart Of Account (This part should have been selected for you)',
								'html' => createChartOfAccountSelect('parent_pos_chart_of_accounts_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
				
					
						array('db_field' =>  'website_url',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'username',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'password',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'primary_contact',
								'caption' => 'Primary Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'legal_name',
								'caption' => 'Name to print on checks',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'zip',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'autopay',
								'type' => 'checkbox',
								'validate' => 'none'),
						array('db_field' =>  'autopay_account_id',
								'caption' => 'Default Payment Account',
								'type' => 'select',
								'html' => createExpensePaymentSelect('autopay_account_id', 'false'),
								'validate' => 'none'),
						array('db_field' =>  'terms',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'days',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array('db_field' =>  'discount',
								'caption' => 'Discount Rate (%)',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						/*array('db_field' =>  'credit_limit',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array( 'db_field' => 'interest_rate',
								'caption' => 'Interest Rate (%)',
								'tags' => numbersOnly(),
								'type' => 'input',
								'validate' => 'none'),*/
						/*array('db_field' =>  'priority',
								'caption' => 'Priority (for ordering account selection)',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),*/
						array('db_field' =>  'balance_init',
								'caption' => 'Initialize Account Balance',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array('db_field' => 'verification_lock_date',
								'caption' => 'Lock Account Data Entry Before Date',
								'type' => 'date',
								'separate_date' => 'date',
								'tags' => ' ',
								'html' => dateSelect('verification_lock_date',''),
								'validate' => 'date'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));
			}
			else if($account_type == "Inventory Account")
			{
				// is this invetory which is an asset or an expense?
				
				$table_def = array(
						array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
						array( 'db_field' => 'company',
								'caption' => 'Easy to type nick name for quick drop down selection',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'encrypted' => 1,
								'validate' => $unique_validate,
								'db_table' => $db_table),
						/*array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', $pos_account_type_id, 'off', ' onchange="alert(\' Changing the account type will require a bit of manual intervention because it is kinda a big deal... so be sure this is what you want to do. After Changing the Account Type Press Submit. This will reload the correct options for the account. Then you can edit the account again... the options for editing will now be different.\')" '),
								'validate' => 	array('select_value' => 'false'))*/
								
								array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Sub Account To Chart Of Account (This part should have been selected for you)',
								'html' => createChartOfAccountSelect('parent_pos_chart_of_accounts_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
				
					array('db_field' =>  'default_payment_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Default type of Inventory (This drop down will list all assets, Pick one of the inventory accounts, the option will be the default when entering invoices)',
								'html' => createInventoryChartOfAccountSelect('default_payment_pos_chart_of_accounts_id', 'false'),
								'validate' => 'none'),
						array('db_field' =>  'website_url',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'username',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'password',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'primary_contact',
								'caption' => 'Primary Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'legal_name',
								'caption' => 'Name to print on checks',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'zip',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'autopay',
								'type' => 'checkbox',
								'validate' => 'none'),
						array('db_field' =>  'autopay_account_id',
								'caption' => 'Default Payment Account',
								'type' => 'select',
								'html' => createExpensePaymentSelect('autopay_account_id', 'false'),
								'validate' => 'none'),
						array('db_field' =>  'terms',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'days',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array('db_field' =>  'discount',
								'caption' => 'Discount Rate (%)',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						/*array('db_field' =>  'credit_limit',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array( 'db_field' => 'interest_rate',
								'caption' => 'Interest Rate (%)',
								'tags' => numbersOnly(),
								'type' => 'input',
								'validate' => 'none'),*/
						/*array('db_field' =>  'priority',
								'caption' => 'Priority (for ordering account selection)',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),*/
						array('db_field' =>  'balance_init',
								'caption' => 'Initialize Account Balance',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array('db_field' => 'verification_lock_date',
								'caption' => 'Lock Account Data Entry Before Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('verification_lock_date',''),
								'validate' => 'date'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));	
							
			}
			else if($account_type == "Expense Account")
			{
				// is this invetory which is an asset or an expense?
				
				$table_def = array(
						array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
						array( 'db_field' => 'company',
								'caption' => 'Easy to type nick name for quick drop down selection',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'encrypted' => 1,
								'validate' => $unique_validate,
								'db_table' => $db_table),
						/*array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', $pos_account_type_id, 'off', ' onchange="alert(\' Changing the account type will require a bit of manual intervention because it is kinda a big deal... so be sure this is what you want to do. After Changing the Account Type Press Submit. This will reload the correct options for the account. Then you can edit the account again... the options for editing will now be different.\')" '),
								'validate' => 	array('select_value' => 'false'))*/
						array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Sub Account To Chart Of Account (This part should have been selected for you)',
								'html' => createChartOfAccountSelect('parent_pos_chart_of_accounts_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
				
					array('db_field' =>  'default_payment_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Default Payment Category (You can override this when entering invoices)',
								'html' => createChartOfAccountsExpenseCOGSSelect('default_payment_pos_chart_of_accounts_id', 'false'),
								'validate' => 'none'),
						array('db_field' =>  'website_url',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'username',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'password',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'primary_contact',
								'caption' => 'Primary Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'legal_name',
								'caption' => 'Name to print on checks',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'zip',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'autopay',
								'type' => 'checkbox',
								'validate' => 'none'),
						array('db_field' =>  'autopay_account_id',
								'caption' => 'Default Payment Account',
								'type' => 'select',
								'html' => createExpensePaymentSelect('autopay_account_id', 'false'),
								'validate' => 'none'),
						array('db_field' =>  'terms',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'days',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array('db_field' =>  'discount',
								'caption' => 'Discount Rate (%)',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						/*array('db_field' =>  'credit_limit',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array( 'db_field' => 'interest_rate',
								'caption' => 'Interest Rate (%)',
								'tags' => numbersOnly(),
								'type' => 'input',
								'validate' => 'none'),*/
						/*array('db_field' =>  'priority',
								'caption' => 'Priority (for ordering account selection)',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),*/
						array('db_field' =>  'balance_init',
								'caption' => 'Initialize Account Balance',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array('db_field' => 'verification_lock_date',
								'caption' => 'Lock Account Data Entry Before Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('verification_lock_date',''),
								'validate' => 'date'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));	
							
			}
			else if($account_type == "Accounts Receivable - Customer")
			{
			}
			else if($account_type == "Accounts Receivable - Credit Card Processor")
			{
				//credit card processors primarily go here...
				
				$table_def = array(
						array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
						array( 'db_field' => 'company',
								'caption' => 'Easy to type nick name for quick drop down selection',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'encrypted' => 1,
								'validate' => $unique_validate,
								'db_table' => $db_table),
						/*array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', $pos_account_type_id, 'off', ' onchange="alert(\' Changing the account type will require a bit of manual intervention because it is kinda a big deal... so be sure this is what you want to do. After Changing the Account Type Press Submit. This will reload the correct options for the account. Then you can edit the account again... the options for editing will now be different.\')" '),
								'validate' => 	array('select_value' => 'false'))*/
						array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Sub Account To Chart Of Account (This part should have been selected for you)',
								'html' => createChartOfAccountSelect('parent_pos_chart_of_accounts_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
					array('db_field' => 'linked_pos_account_id',
								'caption' => 'Linked American Express Receivable Account',
								'type' => 'select',
								'html' => createCCAccountReceivableSelect('linked_pos_account_id', 'false'),
								'validate' => 'false'),
					
						
						array('db_field' =>  'autopay',
								'caption' => 'Auto Depost',
								'type' => 'checkbox',
								'validate' => 'none'),
						//cash can only go to cash, checking, line, saving....
						array('db_field' =>  'autopay_account_id',
								'caption' => 'Default Deposit To Account',
								'type' => 'select',
								'html' => createCCDepositAccountSelect('autopay_account_id', 'false'),
								'validate' => 'none'),
						//this is where we would put how many days for the auto deposit to become available
						array('db_field' =>  'days',
								'type' => 'input',
								'caption' => 'Approximate Number of Business Days to Deposit',
								'tags' => numbersOnly(),
								'validate' => 'none'),
								
								
								
								
						array('db_field' =>  'website_url',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'username',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'password',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'primary_contact',
								'caption' => 'Primary Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'legal_name',
								'caption' => 'Name to print on checks',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'zip',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'validate' => 'none'),
								
								
								
								
								
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));	
						
							
			}			

			else if($account_type == "Non Posting")
			{
				$table_def = array(
						array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
						array( 'db_field' => 'company',
								'caption' => 'Easy to type nick name for quick drop down selection',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'encrypted' => 1,
								'validate' => $unique_validate,
								'db_table' => $db_table),
						/*array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', $pos_account_type_id, 'off', ' onchange="alert(\' Changing the account type will require a bit of manual intervention because it is kinda a big deal... so be sure this is what you want to do. After Changing the Account Type Press Submit. This will reload the correct options for the account. Then you can edit the account again... the options for editing will now be different.\')" '),
								'validate' => 	array('select_value' => 'false'))*/
						array('db_field' =>  'website_url',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'username',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'password',
								'type' => 'input',
								'validate' => 'none'),
								
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));
								
			}
			else if($account_type == "Checking Account" OR $account_type == "Saving Account")
			{
				$table_def = array(
						array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
						array( 'db_field' => 'company',
								'caption' => 'Easy to type nick name for quick drop down selection',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'encrypted' => 1,
								'validate' => $unique_validate,
								'db_table' => $db_table),
						/*array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', $pos_account_type_id, 'off', ' onchange="alert(\' Changing the account type will require a bit of manual intervention because it is kinda a big deal... so be sure this is what you want to do. After Changing the Account Type Press Submit. This will reload the correct options for the account. Then you can edit the account again... the options for editing will now be different.\')" '),
								'validate' => 	array('select_value' => 'false'))*/
							array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Sub Account To Chart Of Account (This part should have been selected for you)',
								'html' => createChartOfAccountSelect('parent_pos_chart_of_accounts_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
				
					
						array('db_field' =>  'website_url',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'username',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'password',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'primary_contact',
								'caption' => 'Primary Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'legal_name',
								'caption' => 'Name to print on checks',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'zip',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'autopay_account_id',
								'caption' => 'Default Account Used To Transfer Money Into',
								'type' => 'select',
								'html' => createExpensePaymentSelect('autopay_account_id', 'false'),
								'validate' => 'none'),
						array('db_field' =>  'balance_init',
								'caption' => 'Initialize Account Balance',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						/*array('db_field' => 'balance_init_date',
								'caption' => 'Balance Initialize Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('balance_init_date',''),
								'validate' => 'date'),*/
						
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));
			}
			else if( $account_type == "Cash Account")
			{
				//A cash account has a physical location
				$table_def = array(
						array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
						array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),
						array( 'db_field' => 'company',
								'caption' => 'Cash Account Name',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'encrypted' => 1,
								'validate' => $unique_validate,
								'db_table' => $db_table),
						/*array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', $pos_account_type_id, 'off', ' onchange="alert(\' Changing the account type will require a bit of manual intervention because it is kinda a big deal... so be sure this is what you want to do. After Changing the Account Type Press Submit. This will reload the correct options for the account. Then you can edit the account again... the options for editing will now be different.\')" '),
								'validate' => 	array('select_value' => 'false'))*/
							array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Sub Account To Chart Of Account (This part should have been selected for you)',
								'html' => createChartOfAccountSelect('parent_pos_chart_of_accounts_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
				
					
						array('db_field' =>  'balance_init',
								'caption' => 'Initialize Account Balance',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						/*array('db_field' => 'balance_init_date',
								'caption' => 'Balance Initialize Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('balance_init_date',''),
								'validate' => 'date'),*/
						
						
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));
			}
			elseif($account_type == "Debit Card")
			{
				$table_def = array(array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
						array( 'db_field' => 'company',
								'caption' => 'Easy to type nick name for quick drop down selection',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'encrypted' => 1,
								'validate' => $unique_validate,
								'db_table' => $db_table),
						/*array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', $pos_account_type_id, 'off', ' onchange="alert(\' Changing the account type will require a bit of manual intervention because it is kinda a big deal... so be sure this is what you want to do. After Changing the Account Type Press Submit. This will reload the correct options for the account. Then you can edit the account again... the options for editing will now be different.\')" '),
								'validate' => 	array('select_value' => 'false'))*/
								array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Sub Account To Chart Of Account (This part should have been selected for you)',
								'html' => createChartOfAccountSelect('parent_pos_chart_of_accounts_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
				
					array('db_field' =>  'linked_pos_account_id',
								'type' => 'select',
								'caption' => 'Debit Card Linked To What Checking Account',
								'html' => createInventoryCheckingSavingAccountSelect('linked_pos_account_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
						array('db_field' =>  'website_url',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'username',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'password',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'primary_contact',
								'caption' => 'Primary Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'legal_name',
								'caption' => 'Name to print on checks',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'zip',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'validate' => 'none'),
						
						array('db_field' =>  'balance_init',
								'caption' => 'Initialize Account Balance',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array('db_field' => 'verification_lock_date',
								'caption' => 'Lock Account Data Entry Before Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('verification_lock_date',''),
								'validate' => 'date'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));
			}
			//is a cash register is a unique beast? - not really, it is just an account...
			//http://retail.about.com/od/finances/a/cash_management.htm	
			else if($account_type == "Cash Register Account")
			{
				//auto name the register upon creation 
				//cash register will have a physical location
				$html_instructions ='<p>Select the location of the register. The Cash Register will auto-name and will be provided after creation</p>';
				$table_def = array(array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
				array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),
				array( 'db_field' => 'account_number',
								'type' => 'input',
								'caption' => 'Account Number (Auto-Generated)',
								'tags' => ' readonly="readonly" ',
								'encrypted' => 1,
								'db_table' => $db_table),
				array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Sub Account To Chart Of Account (This part should have been selected for you)',
								'html' => createCurrentAssetChartOfAccountSelect('parent_pos_chart_of_accounts_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
						
						array( 'db_field' => 'legal_name',
								'caption' => 'Easy to type nick name for quick drop down selection',
								'type' => 'input',
								'validate' => 'none'),
						

						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));
			
				

			}
			else if($account_type == "Equity")
			{

			
			}
			else if($account_type == "Long Term Liability")
			{
	
			}
			else if($account_type == "Short Term Liability")
			{
			}
			else if($account_type == "Store Credit")
			{
				//A cash account has a physical location
				$table_def = array(
						array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_account_id,
								'validate' => 'none'),
						/*array( 'db_field' => 'company',
								
								'type' => 'input',
								'validate' => 'none'),*/
						array( 'db_field' => 'account_number',
								'caption' => 'Store Credit Account Name/Number',
								'type' => 'input',
								'encrypted' => 1,
								'validate' => $unique_validate,
								'db_table' => $db_table),
						/*array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', $pos_account_type_id, 'off', ' onchange="alert(\' Changing the account type will require a bit of manual intervention because it is kinda a big deal... so be sure this is what you want to do. After Changing the Account Type Press Submit. This will reload the correct options for the account. Then you can edit the account again... the options for editing will now be different.\')" '),
								'validate' => 	array('select_value' => 'false'))*/
							array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Sub Account To Chart Of Account (This part should have been selected for you)',
								'html' => createChartOfAccountSelect('parent_pos_chart_of_accounts_id', $default_chart_of_account_id),
								'validate' => array('select_value' => 'false')						
								),
				
					
						array('db_field' =>  'balance_init',
								'caption' => 'Initialize Account Balance',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						/*array('db_field' => 'balance_init_date',
								'caption' => 'Balance Initialize Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('balance_init_date',''),
								'validate' => 'date'),*/
						
						
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'value' => '1',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));
			}			
			else
			{
				echo "Craigy baby you need to make this table def!";
				exit();
			}		
		
		

				
	return array($table_def,  $html_instructions);
	
}
?>


