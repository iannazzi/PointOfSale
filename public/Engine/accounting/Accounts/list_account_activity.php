<?php 

/*

	Craig Iannazzi 4-23-12
	
	IN mexico 2-13-2014....
	

	
	file handles form edit, view and submit. View has a "search range" and sorting. Sorting can kill the balance portion.
	
	//######################################   SUBMIT ###################################################
	// send over an array of checkbox data... do not send over sql variables. Problem is if something is unchecked then "we don't know"
	// need to therefore send hidden input for each journal id.
	
	

	

	error: account listing komar, payment #4248 not showing up... source general journal?
	How do we pay from one account to another?
	
	Link invoice # to payment whenever possible.
	Give payment status and invoice status
	Given an account what first is the balance?
	
*/
$binder_name = 'Accounts';
$access_type = 'READ';
require_once ('../accounting_functions.php');

$db_table = 'pos_accounts';
$complete_location = 'list_accounts.php';
$cancel_location = 'list_accounts.php';

//Any errors check max_input_vars = 50000 in php.ini....

$max_input_vars = ini_get('max_input_vars');
if (ini_get('max_input_vars') < 10001)
{
	trigger_error('max_input_vars is set to low. Check php.ini. It should be around 50000 or more but it is set at ' .$max_input_vars);

}

$key_val_id['pos_account_id'] = getPostOrGetID('pos_account_id');
$pos_account_id = $key_val_id['pos_account_id'];
$page_title = getAccountName($pos_account_id) . ' Account Activity';
$form_handler = basename($_SERVER['PHP_SELF']);
$fold = true; //to collapse folds for readability. Do not change. Ever.
//need to link to the source document...
/*			array( 'th' => 'View',
			'mysql_field' => 'pos_journal_id',
			'variable_get_url_link' => array(
				'row_result_lookup' => 'source_journal',
				"PURCHASES JOURNAL" => array(
							'url' => POS_ENGINE_URL.'/accounting/PurchaseJournal/view_purchase_invoice_to_journal.php', 
							'get_data' => array('pos_purchases_journal_id'=>'pos_journal_id')),				 
				"GENERAL JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/GeneralJournal/view_general_journal_entry.php',
							'get_data' => array('pos_general_journal_id' => 'pos_journal_id'))),
			'url_caption' => 'View',),
*/

$search_fields = array(	array(	'db_field' => 'date',
											'mysql_search_result' => 'date',
											'caption' => 'Start Date',
											'type' => 'date',
											'html' => dateSelect('date_start_date',valueFromGetOrDefault('date_start_date'))
											),
						array(	'db_field' => 'date',
											'mysql_search_result' => 'date',
											'caption' => 'End Date',	
											'type' => 'date',
											'html' => dateSelect('date_end_date',valueFromGetOrDefault('date_end_date'))
											),
						array(	'db_field' => 'journal_id',
											'mysql_search_result' => 'journal_id',
											'caption' => 'Journal Id',	
											'type' => 'input',
											'html' => createSearchInput('journal_id')
											),
						array(	'db_field' => 'journal',
											'mysql_search_result' => 'journal',
											'caption' => 'Journal',	
											'type' => 'input',
											'html' => createSearchInput('journal')
											),
						array(	'db_field' => 'account_name',
											'mysql_search_result' => 'account_name',
											'caption' => 'Account Name',	
											'type' => 'input',
											'html' => createSearchInput('account_name')
											),
						array(	'db_field' => 'chart_of_account_name',
											'mysql_search_result' => 'chart_of_account_name',
											'caption' => 'Chart of <br>Account Name',	
											'type' => 'input',
											'html' => createSearchInput('chart_of_account_name')
											),
						array(	'db_field' => 'description',
											'mysql_search_result' => 'description',
											'caption' => 'Description',	
											'type' => 'input',
											'html' => createSearchInput('description')
											),
						array(	'db_field' => 'debit',
											'mysql_search_result' => 'debit',
											'caption' => 'Debit',	
											'type' => 'input',
											'html' => createSearchInput('debit')
											),
						array(	'db_field' => 'credit',
											'mysql_search_result' => 'credit',
											'caption' => 'Credit',	
											'type' => 'input',
											'html' => createSearchInput('credit')
											)
										
										);
$table_columns = array(
		array(
			'th' => 'Date',
			'mysql_field' => 'date',
			'sort' => 'date'),
		array(
			'th' => 'Journal ID',
			'mysql_field' => 'journal_id',
			'target' => 'blank',
			'variable_get_url_link' => array(
				'row_result_lookup' => 'journal',
				"PURCHASES JOURNAL" => array(
							'url' => POS_ENGINE_URL.'/accounting/PurchaseJournal/view_purchase_invoice_to_journal.php', 
							'get_data' => array('pos_purchases_journal_id'=>'journal_id')),				 
				"GENERAL JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/GeneralJournal/view_general_journal_entry.php',
							'get_data' => array('pos_general_journal_id' => 'journal_id')),
				"PAYMENTS JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/PaymentsJournal/view_payments_journal_entry.php',
							'get_data' => array('pos_payments_journal_id' => 'journal_id')),
				"PAYMENTS JOURNAL PAYEE" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/PaymentsJournal/view_payments_journal_entry.php',
							'get_data' => array('pos_payments_journal_id' => 'journal_id'))),
			
			
			
			
			
			'sort' => 'journal_id'),
		array(
			'th' => 'Journal',
			'mysql_field' => 'journal',
			'sort' => 'journal'),
		array(
			'th' => 'Account Name',
			'mysql_field' => 'account_name',
			'sort' => 'account_name'),
		array(
			'th' => 'Chart Of Account Name',
			'mysql_field' => 'chart_of_account_name',
			'sort' => 'chart_of_account_name'),
		array(
			'th' => 'Description',
			'mysql_field' => 'description',
			'sort' => 'description'),
		array(
			'th' => 'Debit',
			'mysql_field' => 'debit',
			'round' => 2,
			'total' => 0,
			'sort' => 'debit'),
		array(
			'th' => 'Credit',
			'mysql_field' => 'credit',
			'round' => 2,
			'total' => 0,
			'sort' => 'credit'),
		
			
			);
if (isset($_POST['submit'])) //handle the verification submit
{
	
	if(isset($_POST['lock_entry_date']))
	{
		
		$lock_date = scrubInput($_POST['lock_date']);
		$sql = "UPDATE pos_accounts SET validation_lock_date = ";
		runSQL($sql);
		header('Location: ' .$form_handler.'?pos_account_id='.$pos_account_id );
		exit();
	}
	else
	{
		$most_recent_validation_date = '';
		//loop through the posted data - marked by journal_id array
		for($i=0;$i<sizeof($_POST['pos_journal_id']);$i++)
		{
			if (ISSET($_POST['check_'.$_POST['pos_journal_id'][$i]]))
			{
				$validated = 1;
				$most_recent_validation_date = $_POST['date'][$i];
			}
			else
			{
				$validated = 0;
			}
			
			//now update...
			$update_data = array();
			$update_id = array();
			if ($_POST['journal'][$i] == 'PURCHASES JOURNAL')
			{
			 	$update_data['validated'] = $validated;
			 	$update_id['pos_purchases_journal_id'] = $_POST['pos_journal_id'][$i];
				$table = 'pos_purchases_journal';
			}
			else if ($_POST['journal'][$i] == 'PAYMENTS JOURNAL')
			{
				$update_data['validated'] = $validated;
			 	$update_id['pos_payments_journal_id'] = $_POST['pos_journal_id'][$i];
				$table = 'pos_payments_journal';
			}
			else if ($_POST['journal'][$i] == 'PAYMENTS JOURNAL PAYEE')
			{
				$update_data['post_validated'] = $validated;
			 	$update_id['pos_payments_journal_id'] = $_POST['pos_journal_id'][$i];
				$table = 'pos_payments_journal';
			}
			else if ($_POST['journal'][$i] == 'GENERAL JOURNAL')
			{
				$update_data['validated'] = $validated;
			 	$update_id['pos_general_journal_id'] = $_POST['pos_journal_id'][$i];
				$table = 'pos_general_journal';
			}
			else
			{
				trigger_error("missing journal");
			}
			//now run the update
			$result = simpleUpdateSQL($table,$update_id, $update_data);

		}
		
		
		header('Location: ' .$form_handler.'?pos_account_id='.$pos_account_id );
		exit();
		
		//pop up a form to ask if the user wants to lock the validation date? Date select was not working, so we will get to this eventually
		
		$html = '<p>Would you like to prevent journal entries onto this account prior to a date so your nicely verified balance will not get messed up?</p>';
		$html .= '<form action="' . $form_handler.'" method="POST">';

		$html .= dateSelect('lock_date2',$most_recent_validation_date,'') ;
		$html .= createHiddenInput('pos_account_id', $pos_account_id);
		$html .= createHiddenInput('submit', 'submit');
		$html .= '<input class = "button" style="width:200px" type="submit" name="lock_entry_date" value="Prevent Transactions"/>';
		$html .= '<input class = "button" style="width:200px" type="submit" name="cancel" value="No Thanks"/>';
		$html .= '</form>';
		include(HEADER_FILE);
		echo $html;
		include(FOOTER_FILE);
		//exit();
		
		
	}
	//the data needs to be posted, not re-grabbed here...
	// on the submit would you like to prevent future entries before the most recent date validated:
	//update pos_accounts SET validation_lock_date = "date"
	
}
else if (isset($_POST['cancel'])) //handle the verification submit
{
	//trigger cancel
	header('Location: ' .$form_handler.'?pos_account_id='.$pos_account_id );

}
else //View and Edit
{
	//######################################   VIEW & EDIT ###################################################
	// Both are the same with the exception view should have a search, and edit will have the checkboxes enabled.
	// check for search set - show nothing but the search otherwise
	
	//Build the page. Depending on view or edit we will have a few differnces
	$html = '<h3>Verify ' . getAccountName($pos_account_id) . ' ' . getAccountNumber($pos_account_id) .' Account Activity</h3>';
	IF (isset($_POST['verify_statement']))
	{
		$enabled = true; //enable the check boxes
		$buttons = confirmNavigation();
		$buttons = '<p><input class ="button" type="submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
		$buttons .= '<input class = "button" type="submit" name="cancel" value="Cancel" />';
		$get_data = true;
		//we need to know the date range.
		$start_date = (ISSET($_POST['date_start_date'])) ? $_POST['date_start_date'] : '';
		$end_date = (ISSET($_POST['date_end_date'])) ? $_POST['date_end_date'] : '';
	}
	else
	{
		$enabled = false; //disable check boxes
		//create the search
		//preprint($_SESSION);
		$search_set = saveAndRedirectSearchFormUrlV2($search_fields, $table_columns, 'saved_account_activity_url_v2_' .$pos_account_id);
			
		$html .= createSearchFormWithID($search_fields,$form_handler, $key_val_id);
	
		$buttons = '<input class = "button" style="width:200px" type="submit" name="verify_statement" value="Verify Transactions"/>';
		$get_data = (isset($_GET['search'])) ? true : false;
		//we need to know the date range.
		$start_date = (ISSET($_GET['date_start_date'])) ? $_GET['date_start_date'] : '';
		$end_date = (ISSET($_GET['date_end_date'])) ? $_GET['date_end_date'] : '';
	
	}		
	if ($get_data)
	{
		// the data is the same, so we need to get the data array.	
//######################################################################################
		if($fold) // major data sql and grabber here - create balance array from the beginning of christ
		{
			if ($fold) //thoughts
			{
				//need the debits and the credits....
			// cash debits are the payments, credits are the transfers (gj)
			// accounts debits are the
			// cc: debits are the transfers, credits are the payments
			// debit card: debits are the payments, there are no credits
			// checking account: debits are the payments, credits are deposits.
	
			//asset liability and equity are in multiple places.... we basically need to know some information about each account in order to determine what to do with it... selecting credit cards for example comes from the account to the account type... so the account type has the overall "type"

			//account type table has fixed information regarding the account - the account wizard will then set this information up for the user. So the user chooses what the account is they they are creating and needs to know nothing about assets, liability and equity
			//$account_type = getAccountType($pos_account_id); // is it asset, liability or equity?
			//$account_type_name = getAccountTypeName($pos_account_id); //what is it, credit card, inventory, etc
	
			//so we should get the invoice and payment date? why not?
	
			//mayer account shows up in the general journal pos_account id and the payments journal as pos_payee_account_id
			//mayer refund to a card would  be a negative GJ entry.
			//simone perele and komar show up in the purchases journal as pos_account id and in the payments journal as pos_payee_account_id
			//a refund, possibly a mischarge goes to the payments journal as a transfer
			//post_validated!
			//asset vs liability and equity: GJ account is never an asset account, same as PJ, so only check on the payment journal?
	
			//pos_account_id
			//get the credits from the general journal (there will be none for an inventory, checking, saving, etc account)
			//credit goes on the account at the date always?
			//therfore this is always a credit
			//get the discounts - I am adding them in
			}
			if ($fold) 	//gj_invoice_credits_sql
			{
			$gj_invoice_credits_sql =  "
	
			SELECT 
					pos_general_journal.invoice_date as date,  
					pos_general_journal.pos_general_journal_id as journal_id,
					'GENERAL JOURNAL' as journal,
					pos_accounts.company as account_name, 
					pos_chart_of_accounts2.account_name as chart_of_account_name, 
					CONCAT('Invoice ', pos_general_journal.invoice_number, ' ' ,  pos_general_journal.description, IF(pos_general_journal.discount_applied >0, CONCAT('Discount applied: ',pos_general_journal.discount_applied) , '')) as description,
					IF(pos_general_journal.invoice_type = 'Regular', 'Invoice', 'Credit Memo') as type,
					IF(pos_general_journal.invoice_type = 'Credit Memo', pos_general_journal.entry_amount, NULL) as debit,
					IF(pos_general_journal.invoice_type = 'Regular', pos_general_journal.entry_amount - pos_general_journal.discount_applied, NULL) as credit,
					pos_general_journal.validated as verify

				FROM pos_general_journal
				LEFT JOIN pos_accounts
				ON pos_general_journal.pos_account_id = pos_accounts.pos_account_id
				LEFT JOIN pos_chart_of_accounts
				ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
				LEFT JOIN pos_account_type
				ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
				LEFT JOIN pos_chart_of_accounts AS pos_chart_of_accounts2
				ON pos_general_journal.pos_chart_of_accounts_id = pos_chart_of_accounts2.pos_chart_of_accounts_id
				WHERE (pos_accounts.pos_account_id =$pos_account_id OR pos_accounts.linked_pos_account_id =$pos_account_id)
				AND pos_general_journal.entry_type = 'Invoice' 
	
			";
	
			//get the expenses from the general journal  - depending on the payment this is where things get wacky between cc and bank (asset and liability) credits and debits need to flip
			//ex: charge to citi credit, charge to CNB debit. - value Refund to CITI debit, refund to CNB credit
			//add in linked account as well. for debit cards that pull from bank account.
			}
			if ($fold) 	//gj_expenses_sql
			{
			$gj_expenses_sql ="
			SELECT 
			pos_payments_journal.payment_date as date,
			pos_payments_journal.pos_payments_journal_id as journal_id,
			'GENERAL JOURNAL' as journal,
			pos_general_journal_account.company as account_name,
	
			(SELECT GROUP_CONCAT(pos_general_journal_chart_of_accounts.account_name) FROM pos_general_journal
			LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
			ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
			LEFT JOIN pos_invoice_to_payment
			ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
			WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id 
			AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as chart_of_account_name,

			(SELECT GROUP_CONCAT(concat(pos_general_journal.supplier,' : ', pos_general_journal.description)) FROM pos_general_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
			WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id 
			AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as description,


			'' as type,
			IF(pos_payments_journal.payment_amount<0, IF(pos_account_type.account_type = 'ASSETS' , NULL, -pos_payments_journal.payment_amount), IF(pos_account_type.account_type = 'ASSETS' , -pos_payments_journal.payment_amount,NULL)) as debit,
	
			IF(pos_payments_journal.payment_amount>=0, IF(pos_account_type.account_type = 'ASSETS' , pos_payments_journal.payment_amount,NULL), IF(pos_account_type.account_type = 'ASSETS' , NULL, pos_payments_journal.payment_amount)) as credit,
	
			pos_payments_journal.validated as verify

			FROM pos_accounts
			LEFT JOIN pos_account_type USING (pos_account_type_id)
			LEFT JOIN pos_payments_journal USING (pos_account_id)
			LEFT JOIN pos_invoice_to_payment  
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_general_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			LEFT JOIN pos_accounts as pos_general_journal_account 
			ON pos_general_journal.pos_account_id = pos_general_journal_account.pos_account_id
			LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
			ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
			WHERE  (pos_accounts.pos_account_id =$pos_account_id OR pos_accounts.linked_pos_account_id =$pos_account_id) AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL'
			AND (pos_general_journal.entry_type = 'Receipt' OR 	pos_general_journal.entry_type = 'Invoice')
			";	
	
			}
			if ($fold) 	//purchase_journal_credits_and_discounts_sql
			{
	
			// get the credits and credit memos (debits) from the purchases journal - tell me about the discount if there is one.
			//Only inventory accounts will be here so only liability, no switcharoo shit
	
			$purchase_journal_credits_and_discounts_sql = "
	
			SELECT 
					pos_purchases_journal.invoice_date as date,
					pos_purchases_journal.pos_purchases_journal_id as journal_id,  
					'PURCHASES JOURNAL' as journal,
					pos_accounts.company as account_name, 
					(SELECT 'Merchandise Inventory TBD') as chart_of_account_name, 
					IF(pos_purchases_journal.invoice_type = 'Regular', CONCAT('Invoice: ', pos_purchases_journal.invoice_number, ' Discount Applied: ', ROUND(pos_purchases_journal.discount_applied,2)),  CONCAT('CREDIT: ', pos_purchases_journal.invoice_number)) as description,
					'purchase_journal_credits_and_discounts_sql' as type,
					IF(pos_purchases_journal.invoice_type = 'Credit Memo', pos_purchases_journal.invoice_amount-pos_purchases_journal.discount_applied, NULL) as debit,
					IF(pos_purchases_journal.invoice_type = 'Regular', pos_purchases_journal.invoice_amount-pos_purchases_journal.discount_applied, NULL) as credit,
					pos_purchases_journal.validated as verify
				FROM pos_purchases_journal
				LEFT JOIN pos_accounts
				ON pos_purchases_journal.pos_account_id = pos_accounts.pos_account_id
				LEFT JOIN pos_chart_of_accounts
				ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
				WHERE (pos_accounts.pos_account_id =$pos_account_id OR pos_accounts.linked_pos_account_id =$pos_account_id)
	
			";
			}
			if ($fold) 	//payment_account_view_of_general_journal_invoices
			{
			$payment_account_view_of_general_journal_invoices ="
			SELECT 
			pos_payments_journal.payment_date as date,
			pos_payments_journal.pos_payments_journal_id as journal_id,
			'PAYMENTS JOURNAL' as journal,
			pos_general_journal_account.company as account_name,
	
			(SELECT GROUP_CONCAT(pos_general_journal_chart_of_accounts.account_name) FROM pos_general_journal
			LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
			ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
			LEFT JOIN pos_invoice_to_payment
			ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
			WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id 
			AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as chart_of_account_name,

			(SELECT GROUP_CONCAT(concat(pos_general_journal.supplier,' : ', pos_general_journal.description)) FROM pos_general_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
			WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id 
			AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as description,


			'payments_of_general_journal_invoices' as type,
	
	
			IF(pos_payments_journal.payment_amount<0, IF(pos_account_type.account_type ='ASSETS', NULL, -pos_payments_journal.payment_amount), IF(pos_account_type.account_type ='ASSETS',pos_payments_journal.payment_amount , NULL)) as debit,
			IF(pos_payments_journal.payment_amount<0, IF(pos_account_type.account_type ='ASSETS', -pos_payments_journal.payment_amount, NULL), IF(pos_account_type.account_type ='ASSETS',NULL, pos_payments_journal.payment_amount)) as credit,

			pos_payments_journal.validated as verify

			FROM pos_accounts
			LEFT JOIN pos_account_type USING (pos_account_type_id)
			LEFT JOIN pos_payments_journal USING (pos_account_id)
			LEFT JOIN pos_invoice_to_payment  
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_general_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			LEFT JOIN pos_accounts as pos_general_journal_account 
			ON pos_general_journal.pos_account_id = pos_general_journal_account.pos_account_id
			LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
			ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
			WHERE  (pos_accounts.pos_account_id =$pos_account_id OR pos_accounts.linked_pos_account_id =$pos_account_id) AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL'
			AND (pos_general_journal.entry_type = 'Receipt' OR 	pos_general_journal.entry_type = 'Invoice')
			";	
			}
			if ($fold) 	//payment_account_view_payments_of_purchases_journal_invoices
			{
			//no asset accounts exist in the purchases journal...
			$payment_account_view_payments_of_purchases_journal_invoices ="
			SELECT DISTINCT
			pos_payments_journal.payment_date as date,
			pos_payments_journal.pos_payments_journal_id as journal_id,
			'PAYMENTS JOURNAL' as journal,
			pos_purchases_journal_account.company as account_name,
			'MI TBD' AS chart_of_account_name,
	
			(SELECT GROUP_CONCAT(CONCAT((SELECT pos_manufacturers.company FROM pos_manufacturers WHERE pos_manufacturer_id = pos_purchases_journal.pos_manufacturer_id),' Invoice Number: ', pos_purchases_journal.invoice_number, ' Date: ' ,pos_purchases_journal.invoice_date, ' Applied Amount: ', pos_invoice_to_payment.applied_amount )) FROM pos_purchases_journal
				LEFT JOIN pos_manufacturers
				ON pos_purchases_journal.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id
				INNER JOIN pos_invoice_to_payment
				ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
				WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id AND 		pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL') as description,
	

			'payment_account_view_payments_of_purchases_journal_invoices' as type,
	
			IF(pos_payments_journal.payment_amount<0, IF(pos_account_type.account_type ='ASSETS', NULL, -pos_payments_journal.payment_amount), IF(pos_account_type.account_type ='ASSETS',pos_payments_journal.payment_amount , NULL)) as debit,
			IF(pos_payments_journal.payment_amount<0, IF(pos_account_type.account_type ='ASSETS', -pos_payments_journal.payment_amount, NULL), IF(pos_account_type.account_type ='ASSETS',NULL, pos_payments_journal.payment_amount)) as credit,
	
			pos_payments_journal.validated as verify

			FROM pos_accounts
			LEFT JOIN pos_account_type USING (pos_account_type_id)
			LEFT JOIN pos_payments_journal USING (pos_account_id)
			LEFT JOIN pos_invoice_to_payment  
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_purchases_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
			LEFT JOIN pos_accounts as pos_purchases_journal_account 
			ON pos_purchases_journal.pos_account_id = pos_purchases_journal_account.pos_account_id
	
	
			WHERE  (pos_accounts.pos_account_id =$pos_account_id OR pos_accounts.linked_pos_account_id =$pos_account_id) AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL'
			";
			}	
			if ($fold) 	//expense_account_view_of_payments_of_general_journal_invoices
			{
				$expense_account_view_of_payments_of_general_journal_invoices ="
			SELECT DISTINCT
			pos_payments_journal.payment_date as date,
			pos_payments_journal.pos_payments_journal_id as journal_id,
			'PAYMENTS JOURNAL PAYEE' as journal,
			pos_general_journal_account.company as account_name,
			pos_general_journal_chart_of_accounts.account_name as chart_of_account_name, 
	
		
				(SELECT CONCAT(pos_payments_journal.applied_status,' Payment From ', pos_general_journal_account.company , ' For ',GROUP_CONCAT(CONCAT( ' Invoice Number: ', pos_general_journal.invoice_number, ' Date: ' ,pos_general_journal.invoice_date, ' Applied Amount: ', ROUND(pos_invoice_to_payment.applied_amount,2) )
			 ),IF(pos_payments_journal.applied_status ='UNAPPLIED',
			 (SELECT CONCAT(' Total Applied: ' , Round(sum(pos_invoice_to_payment.applied_amount),2) )FROM pos_invoice_to_payment WHERE pos_invoice_to_payment.pos_payments_journal_id=pos_payments_journal.pos_payments_journal_id),
			 '')) FROM pos_general_journal
		
				INNER JOIN pos_invoice_to_payment
				ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
				WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id AND 		pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL')as description,
	

			'expense_account_view_of_payments_of_purchases_journal_invoices' as type,
	
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
			IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount, NULL) as credit,
	
	
			pos_general_journal.validated as verify

			FROM pos_accounts
			LEFT JOIN pos_account_type USING (pos_account_type_id)
			LEFT JOIN pos_payments_journal ON pos_accounts.pos_account_id = pos_payments_journal.pos_payee_account_id
			LEFT JOIN pos_invoice_to_payment  
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	
	
	
			LEFT JOIN pos_general_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			LEFT JOIN pos_accounts as pos_general_journal_account 
			ON pos_payments_journal.pos_account_id = pos_general_journal_account.pos_account_id
			LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
			ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
	
			WHERE  (pos_accounts.pos_account_id =$pos_account_id OR pos_accounts.linked_pos_account_id =$pos_account_id) AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL'
			";	
			}
			if ($fold) 	//inventory_account_view_of_payments_of_purchases_journal_invoices
			{
	
			$inventory_account_view_of_payments_of_purchases_journal_invoices ="
			SELECT DISTINCT
			pos_payments_journal.payment_date as date,
			pos_payments_journal.pos_payments_journal_id as journal_id,
			'PAYMENTS JOURNAL PAYEE' as journal,
			pos_purchases_journal_account.company as account_name,
	
			'MI TBD' AS chart_of_account_name,
	
	
	
			(SELECT CONCAT(pos_payments_journal.applied_status,' Payment From ', pos_purchases_journal_account.company , ' For ',GROUP_CONCAT(CONCAT( (SELECT pos_manufacturers.company FROM pos_manufacturers WHERE pos_manufacturer_id = pos_purchases_journal.pos_manufacturer_id),' Invoice Number: ', pos_purchases_journal.invoice_number, ' Date: ' ,pos_purchases_journal.invoice_date, ' Applied Amount: ', ROUND(pos_invoice_to_payment.applied_amount,2) )
			 ),IF(pos_payments_journal.applied_status ='UNAPPLIED',
			 (SELECT CONCAT(' Total Applied: ' , Round(sum(pos_invoice_to_payment.applied_amount),2) )FROM pos_invoice_to_payment WHERE pos_invoice_to_payment.pos_payments_journal_id=pos_payments_journal.pos_payments_journal_id),
			 '')) FROM pos_purchases_journal
				LEFT JOIN pos_manufacturers
				ON pos_purchases_journal.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id
				INNER JOIN pos_invoice_to_payment
				ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
				WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id AND 		pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL'
		
		

		
		
		
		
				) as description,
	

			'inventory_account_view_of_payments_of_purchases_journal_invoices' as type,
	
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
			IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount, NULL) as credit,
	
	
			pos_payments_journal.post_validated as verify

			FROM pos_accounts
			LEFT JOIN pos_account_type USING (pos_account_type_id)
			LEFT JOIN pos_payments_journal ON pos_accounts.pos_account_id = pos_payments_journal.pos_payee_account_id
			LEFT JOIN pos_invoice_to_payment  
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	
	
	
			LEFT JOIN pos_purchases_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
			LEFT JOIN pos_accounts as pos_purchases_journal_account 
			ON pos_payments_journal.pos_account_id = pos_purchases_journal_account.pos_account_id
	
	
			WHERE  (pos_accounts.pos_account_id =$pos_account_id OR pos_accounts.linked_pos_account_id =$pos_account_id) AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL'
			";	
	
	
	
	
	
	
	
	
	
			}
			if ($fold)	//payments_account_view_payment_to_payee_account
			{
			/*//this gets payments from the payee account.... hmmmm shouldn't need this?
			$payments_to_account ="
			SELECT DISTINCT
			pos_payments_journal.payment_date as date,
			pos_payments_journal.pos_payments_journal_id as journal_id,
			'PAYMENTS JOURNAL' as journal,
			pos_payment_account.company as account_name,
			pos_payment_chart_of_accounts.account_name AS chart_of_account_name,
			IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment Using ', pos_payment_account.company), CONCAT('Refund To ', pos_payment_account.company)) as description,
			'PAYMENT To ACCOUNT' as type,
	
	
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
			IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as credit,
	
			pos_payments_journal.validated as verify

			FROM pos_accounts
			LEFT JOIN pos_account_type USING (pos_account_type_id)
			LEFT JOIN pos_payments_journal ON pos_payments_journal.pos_payee_account_id = pos_accounts.pos_account_id

			LEFT JOIN pos_accounts as pos_payment_account 
			ON pos_payments_journal.pos_account_id = pos_payment_account.pos_account_id
			LEFT JOIN pos_chart_of_accounts AS pos_payment_chart_of_accounts
			ON pos_payment_account.parent_pos_chart_of_accounts_id = pos_payment_chart_of_accounts.pos_chart_of_accounts_id
			WHERE  pos_accounts.pos_account_id =$pos_account_id 
			";	
			*/
	
	
			//these are payments that have no 'link' to a journal...
			//it looks like the credit / debit order is important for now...
			$payments_account_view_payment_to_payee_account ="
			SELECT DISTINCT
			pos_payments_journal.payment_date as date,
			pos_payments_journal.pos_payments_journal_id as journal_id,
			'PAYMENTS JOURNAL' as journal,
			pos_payee_account.company as account_name,
			pos_payee_chart_of_accounts.account_name AS chart_of_account_name,
			IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment To ', pos_payee_account.company), CONCAT('Refund From ', pos_payee_account.company)) as description,
			'payments_account_view_payment_to_payee_account' as type,
	
	
			IF(pos_payments_journal.payment_amount<0, IF(pos_account_type.account_type ='ASSETS', NULL, -pos_payments_journal.payment_amount), IF(pos_account_type.account_type ='ASSETS',pos_payments_journal.payment_amount , NULL)) as debit,
			IF(pos_payments_journal.payment_amount<0, IF(pos_account_type.account_type ='ASSETS', -pos_payments_journal.payment_amount, NULL), IF(pos_account_type.account_type ='ASSETS',NULL, pos_payments_journal.payment_amount)) as credit,
	
			pos_payments_journal.validated as verify

			FROM pos_accounts
	
			LEFT JOIN pos_account_type USING (pos_account_type_id)
			LEFT JOIN pos_payments_journal ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_accounts as pos_payee_account 
			ON pos_payments_journal.pos_payee_account_id = pos_payee_account.pos_account_id
			LEFT JOIN pos_chart_of_accounts AS pos_payee_chart_of_accounts
			ON pos_payee_account.parent_pos_chart_of_accounts_id = pos_payee_chart_of_accounts.pos_chart_of_accounts_id
			WHERE  (pos_accounts.pos_account_id =$pos_account_id OR pos_accounts.linked_pos_account_id =$pos_account_id) AND pos_payments_journal.pos_payments_journal_id NOT IN (SELECT pos_payments_journal_id FROM pos_invoice_to_payment WHERE pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id)
			";	
			}
			if($fold)//payee_account_view_payment_to_payment_account
			{
			//we need to get the pos_payee_account_id infomation now....
			// get the account info, link to the payments journal with payee_id
			// from there get the account id and link back for information about where the payment came from
			$payee_account_view_payment_to_payment_account ="
	
			SELECT DISTINCT
			pos_payments_journal.payment_date as date,
			pos_payments_journal.pos_payments_journal_id as journal_id,
			'PAYMENTS JOURNAL PAYEE' as journal,
			pos_payee_account.company as account_name,
			pos_payee_chart_of_accounts.account_name AS chart_of_account_name,
			IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment From ', pos_payee_account.company), CONCAT('Refund To ', pos_payee_account.company)) as description,	
			'payee_account_view_payment_to_payment_account' as type,
			IF(pos_payments_journal.payment_amount<0, IF(pos_account_type.account_type ='ASSETS',  -pos_payments_journal.payment_amount, NULL), IF(pos_account_type.account_type ='ASSETS',NULL, pos_payments_journal.payment_amount)) as debit,
			IF(pos_payments_journal.payment_amount<0, 
			IF(pos_account_type.account_type ='ASSETS', NULL, -pos_payments_journal.payment_amount), IF(pos_account_type.account_type ='ASSETS', pos_payments_journal.payment_amount,NULL)) as credit,
			pos_payments_journal.post_validated as verify
			FROM pos_accounts
			LEFT JOIN pos_account_type USING (pos_account_type_id)
			LEFT JOIN pos_payments_journal ON pos_payments_journal.pos_payee_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_accounts as pos_payee_account 
			ON pos_payments_journal.pos_account_id = pos_payee_account.pos_account_id
			LEFT JOIN pos_chart_of_accounts AS pos_payee_chart_of_accounts
			ON pos_payee_account.parent_pos_chart_of_accounts_id = pos_payee_chart_of_accounts.pos_chart_of_accounts_id
			WHERE  (pos_accounts.pos_account_id =$pos_account_id OR pos_accounts.linked_pos_account_id =$pos_account_id) AND pos_payments_journal.pos_payments_journal_id NOT IN (SELECT pos_payments_journal_id FROM pos_invoice_to_payment WHERE pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id)
			";	
			}
			//missing is the cash receipts journal, possibly the cusomter payments journal?
			//missing is the payroll journal
			if($fold) //unionize all the sql statements into the massive statment
			{
				//sum it all up
				//there are invoices on the general journal
				//there are invoices on the purchases journal
				//there are expenses that get charged
				//then the payments: the payment account AND the payee_account
				//payment account: expenses
				//payment account view: expense invoices linked to payments
				//payment account view: purchase invoices linked to payments
				//payee account: expense invoices invoices linked to payments
				//payee account: purchase invoices invoices linked to payments
				//payment view of payment to payee account payments not linked to invoices
				//payee view of payee account to payment account payments not linked to invoices
		
		
				$tmp_table_sql = '';
				$tmp_table_sql .= $gj_invoice_credits_sql;
				$tmp_table_sql .= " UNION "; 

				$tmp_table_sql .= $purchase_journal_credits_and_discounts_sql;
				$tmp_table_sql .= " UNION "; 

				$tmp_table_sql .= $payee_account_view_payment_to_payment_account;
			$tmp_table_sql .= " UNION "; 
				$tmp_table_sql .= $expense_account_view_of_payments_of_general_journal_invoices;
				$tmp_table_sql .= " UNION "; 
				$tmp_table_sql .= $inventory_account_view_of_payments_of_purchases_journal_invoices;
				$tmp_table_sql .= " UNION "; 
				$tmp_table_sql .= $payment_account_view_of_general_journal_invoices;
				$tmp_table_sql .= " UNION "; 
				$tmp_table_sql .= $payment_account_view_payments_of_purchases_journal_invoices;
				$tmp_table_sql .= " UNION "; 
				$tmp_table_sql .= $payments_account_view_payment_to_payee_account;
				
		
		
			}
			if($fold) //get the data
			{
			$tmp_sql = "CREATE TEMPORARY TABLE tmp " . $tmp_table_sql . ";";
			$dbc = openPOSdb();
			$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=10000');
			$results[] = runTransactionSQL($dbc,$tmp_sql);
			//this is where we can get the balance ... 
			$tmp_select_sql = "SELECT balanceCalculation.*
	
			FROM (
			SELECT 
			   tmp.*,
				 @Balance := @Balance + COALESCE(tmp.credit,0) - COALESCE(tmp.debit,0) AS balance
			FROM tmp, (SELECT @Balance := (SELECT balance_init FROM pos_accounts WHERE pos_account_id = $pos_account_id)) AS variableInit
			ORDER BY  tmp.date ASC, tmp.journal_id ASC
		) AS balanceCalculation
	
			ORDER BY  balanceCalculation.date ASC, balanceCalculation.journal_id ASC";
			// now we have to turn the data into an array
			$data = getTransactionSQL($dbc,$tmp_select_sql);
			closeDB($dbc);
			}
		}
		if (sizeof($data) >0)
		{
		
			
			$html .= '<form action="' . $form_handler.'" method="POST">';
				
			//preprint($data);

			// Table header:
			$order = recordsTableSortOrder();
			$getURL = getPageURLwithGETS();
			$class = 'generalTable';
			$html .= '<table class = "'. $class . '">' .newline();
			$html .= '<thead><tr>' . newline();
			foreach($table_columns as $column)
			{
				if (isset($column['sort']))
				{
					$html .= '<th><a href="'.$getURL.'sort='.$column['mysql_field'].'&order='.$order.'">'.$column['th'].'</a></th>'.newline();
				}
				else
				{
					$html .= '<th>'.$column['th'].'</th>'.newline();
				}
			}
			$html .= '<th>Verify</th>'.newline();
			$html .= '<th>Balance</th>'.newline();
		
			$html .= '</tr></thead>'.newline();
			$html .= '<tbody>';
			$html .= '<tr style="background-color:yellow;border-bottom:1px solid rgb(50,50,50);">';
			$html .= '<td colspan="1"><b>Totals</b></td>';
			
			for($i=1;$i<sizeof($table_columns);$i++)
			{
				//
				if(isset($table_columns[$i]['total']))
				{	
					$html .= '<td><b>' .number_format(calculateSQLTotal($data, $table_columns[$i]['mysql_field']),0).'</b></td>';
				}
				else
				{
					$html .= '<td></td>';
				}
			}
			$html .= '<td></td>';
			$html .= '<td></td>';
			$html .= '</tr>';
			
			
			$initial_balance = getSingleValueSQL("SELECT balance_init FROM pos_accounts WHERE pos_account_id=$pos_account_id");
			$balanced_placed = false;
			
			
		
			// Fetch and print all the records.... only the ones within the date range
			
			//calculate the balance up until start? no just need the previous balance.
			

			
			
			for($i=0;$i<sizeof($data);$i++)
			{
				//only display searched range
				
				if((strtotime($data[$i]['date']) >= strtotime($start_date) OR $start_date == '') AND (strtotime($data[$i]['date']) <= strtotime($end_date) OR $end_date == ''))
				{
					if (!$balanced_placed)
					{
						// add the initialized balance
						$html .= '<tr>';
						if($i==0)
						{
							$html .= '<td colspan="2">Initialized Balance</td>';
						}
						else
						{
							$html .= '<td colspan="2">Balance</td>';
						}
						for($i2=1;$i2<sizeof($table_columns);$i2++)
						{

								$html .= '<td></td>';
						}
						//$html .= '<td></td>';
						$html .= '<td>'.number_format($initial_balance,2).'</td>';
						$html .= '</tr>';
						$balanced_placed = true;
					}
					//bulk of the data goes down here:
					$html .= '<tr>';
					foreach($table_columns as $column_data)
					{
						$html .= recordsTableTD($column_data,$data[$i]);
					}
					// add the verify check
					$html .= '<td>';
					// add the array for the journal id... this is what we loop through to check
					$html .= createHiddenInput('pos_journal_id[]', $data[$i]['journal_id']);
					$html .= createHiddenInput('journal[]', $data[$i]['journal']);
					$html .= createHiddenInput('date[]', $data[$i]['date']);
					$html .='<input  onchange="needToConfirm=true" type = "checkbox" id="check_'.$data[$i]['journal_id'].'" name="check_'.$data[$i]['journal_id'].'" ';
					if (!$enabled)
					{
						$html .= ' disabled = "disabled" ';
					}
					if(isset($data[$i]['verify']))
					{
						if ($data[$i]['verify'] == '1' || strtolower($data[$i]['verify']) == 'true' || strtolower($data[$i]['verify']) == 'checked' || strtolower($data[$i]['verify']) == 'yes')
						{
							$html .=  ' checked = "checked" ';
						}
					}
					$html .= ' />'.newline();
					//add the balance
					$html .= '<td>'.number_format($data[$i]['balance'],2) .'</td>'.newline();
					$html .= '</tr>'.newline();
				}
				else
				{

					//no show, update init blnce
					$initial_balance = $data[$i]['balance'];
				}
			}
			
			$html .= '</tbody></table>'.newline(); // Close the table.
			$html .= $buttons;
		$html .= createHiddenInput('pos_account_id', $pos_account_id);
			$html .= createHiddenInput('date_start_date', $start_date);
			$html .= createHiddenInput('date_end_date', $end_date);
			$html .= '</form>';
			$html .= '<p>';
			$html .= sizeof($data) . ' Records Returned';
		}
		else
		{
			$html .= '<p class="error"> There are no records to display </p>';
		}
	}
	else
	{
		$html.= '<p> Choose search criteria from above to display results. Use search alone for all records </p>';
	}

	include(HEADER_FILE);
	echo $html;
	
	
	
	$tmp_sql = "
CREATE TEMPORARY TABLE accounts

		
		SELECT 'GENERAL JOURNAL' as journal, pos_general_journal_id as id, entry_amount-discount_applied as charge FROM pos_general_journal WHERE pos_account_id = $pos_account_id  AND entry_type = 'Invoice'
		
		UNION
		
		(SELECT COALESCE(sum(invoice_amount-discount_applied),0) FROM pos_purchases_journal WHERE invoice_type = 'Regular' AND pos_account_id = pos_accounts.pos_account_id)
		as pj_invoices,
		
		(SELECT COALESCE(sum(invoice_amount),0) FROM pos_purchases_journal WHERE invoice_type = 'Credit Memo' AND pos_account_id = pos_accounts.pos_account_id)
		
		as pj_credits,
				
		(SELECT COALESCE(sum(payment_amount),0) FROM pos_payments_journal WHERE (pos_account_id = pos_accounts.pos_account_id OR pos_account_id=pos_accounts.linked_pos_account_id))
		
		
		as payments_using,
		
		(SELECT COALESCE(sum(payment_amount),0) FROM pos_payments_journal WHERE pos_payee_account_id = pos_accounts.pos_account_id)
		
		
		 as payment_to
		
		




FROM pos_accounts
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
LEFT JOIN pos_chart_of_accounts
ON pos_chart_of_accounts.pos_chart_of_accounts_id = pos_accounts.parent_pos_chart_of_accounts_id
WHERE pos_account_id = $pos_account_id

";
	
	
	
	include(FOOTER_FILE);
	
	
	

}




?>

