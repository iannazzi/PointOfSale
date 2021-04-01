<?php

/* 
	Journals and Discriptions
	
	The Sales Journal is a special journal where sales of services and merchandise made on account (business's customer is allowed to charge purchases) are recorded.
	
	The Cash Receipts Journal is a special journal that is used to record all receipts of cash. Columns are set up that indicate the sources of the cash. Two of the major sources of cash for a business are Cash Sales and Collections of Customer Charge Sales. These and other categories that have a lot of activity (transactions) have their own column.

		I have these two journals combined into the customer payment journal. Anything "on account" is not cash received!
		Cash received: Sales Invoice => invoice_to_payment => customer_payment. 

		On account: Sales Invoice => invoice_to_payment => sales_journal

		Customer payment: customer_payment => (sales_journal) => invoice_to_payment => sales_invoice.....  Close the sales invoice... what about the sales journal... 

		Dr CASH
		Cr AR
	
	
	Now we need to receive cash from interest.... for example
	in the customer_payments journal	
	
	
	Cash Receipts Journal - All Payments not on 'account'.... CUSTOMER PAYMENTS JOURNAL - LINK to COA 4000
	
	Purchases Journal - Payments onto account. This journal should include all "payments onto account". We require all inventory accounts to have an account set up... basically because things eventually fall onto an account.. like a credit memo, or a messed up invoice.
		
		This whole journal currently links to one coa - 1215 finished merchandise. Similarly the discount will link to one coa via code. To open this up we set the account default, pull that into the journal entry as a default, with options to change it.
		Link account to COA... Link account discount to COA.... default those to the entry
		
	General Journal ... should be for 'rare' use.... however we use it for all expense paid via cash as well as all expenses on account. The reason for not using a "cash diusbursements" journal is that we want to record the expense as a source document, then split the payment to a payments journal. 
	
	Cash Payments Journal - This journal is for all cash paid out EXCEPT PAYROLL (?). We call it the payments journal and it works as described. 

	The Payroll Journal is a special journal that is used to record and summarize salaries and wages paid to employees and the deductions for taxes and other authorized employee withholding amounts. This introductory tutorial does not cover the payroll accounting process and records.	Sales return Journal
	
	Purchase Return Journal
	
	The Sales Return & Allowances Journal is a special journal that is used to record the returns and allowances of merchandise sold on account.

	The Purchase Returns & Allowances Journal is a special journal that is used to record the returns and allowances of merchandise purchased on account.
	
	
	
	Some good tutorials on accounts:
	http://www.dwmbeancounter.com/BCTutorSite/Courses/ChartAccounts/lesson02-6.html
	
	
	accounting setup
	there needs to be certain links between journals and the chart of accounts
	the purchases journal should be able to go to multiple accounts - 5000, 5050, etc
	the account could specify the default coa to 
	
	DEbit to have Credit to give
	
	examples
	sold $12150 for CASH of finished goods that (cost $9100)
	
	first purchase the goods on account chantelle
	
	Dr Merchandise Inventory 1215 $9100	This is done through the purchase journal... link the whole journal or add coa Link
	Cr AP 2000 chantelle		This is done through the purchase journal no link needed
			
	
	
	Sell the goods
	
	Dr CASH 1010 cash account ID#3 $12150			This is done as a customer payment => the payment links to an account
	Cr SALES 4000 $10000							The whole customer payment journal links to 4000 (?) could add coa link
	Cr Sales Tax 2310 $2150
	take goods out of inventory - this is now FIFO or LIFO ?? date inventory + date received + date sold
	the cogs sold needs to be looked up from the product_id on the invoice to get the product cost, or all the way back to the purchase order..... PROBLEMMMMMMMMMMMMM
	
	receive has the inventory. it is the inventory log. it is then corrected by inventory counts. Receiving then receives to a chart of accounts - 1215 merchandise inventory
	selling has to put the sale to the right cost of goods sold.... the sales invoice links to chart of accounts
	
	FIFO - take the product id. take the inventory count. working backwards, count receive events to the first one reveied. take the cost from the PO. Transfers?
	LIFO - take the product id, find the last one received, take the cost from the PO.
	
	Dr COGS 5000 $9100
	Cr Merchandise Inventory 1215 $9100
	
	Transfer the money to the safe then checking ... all done through the payments journal
	Deposit the cash in the safe
	DR CASH 1010 cash account ID#3  $12150
	Cr CASH 1010 cash account ID#7 safe $12150
	
	Deposit the cash in the Bank
	DR CASH 1010 cash account ID#7 Safe  $12150
	Cr CASH 1020 checking account ID#33 CNB $12150



*/

/* ###################### SETUP ##################################

chart of accounts: you need an inventory type asset account

*/
/* 

	journals, biunders, accounts and the chart of accounts
	
	store credit, gift cards, and deposits all go into one binder....
	link the entire binder to the chart of accounts?
	
	purchases journal - 
	
	receive inventory - link to coa
	
*/
$binder_name = 'Accounting Setup';
$access_type = 'WRITE';
$page_title = 'Account Setup';
require_once ('../accounting_functions.php');
$access_type = 'READ';


//link account types to default chart of accounts:
/*Credit card: AP
Inventory Account: AP - 2000 (could be 2010 finished merchandise account payable)
	default invoice coa - 1215 inventory (pull down all current assets)

*/
//list acount types with a view.... ONLY CAN MODIFY THE DEFAULT CHART OF ACCOUNT
$html = '';


//********************REQUIRED CHART OF ACCOUNTS ***************************//
/*

Stock received not invoiced

Reserved nominal codes
There are a few codes in Brightpearl that are required for tax and other calculations. These cannot be changed or used for anything else:

1001 : Stock
1100 : Accounts Receivable / Debtors Control Account
2100 : Accounts Payable / Creditors Control Account
2200 : Sales Tax
2201 : Purchase Tax
2202 : Reserved code / VAT Liability Account
3200 : Retained Earnings
9997 : A/R Import (created automatically during import of accounts receivable (debtors)
9998 : Suspense Account
9999 : Mispostings Account



*/
/*
$html.= '<h2>Required Accounts and the link to the Chart Of Accounts.</h2>';
$html.= '<p>Required Accounts Are needed, probably for auto-posting to the general ledger, which i am still calculating.......

Inventory is calculated from receive events and the brand.
If we are to auto-post receive data we would need a pending accounts payable to wait for an invoice:
Recevive: DEBIT INVENTORY, CREDIT ACCOUNTS PENDING <br>
HOWEVER receiving is not an accounting event. The invoice represents the accounting event. 
Originally we wanted to know how much stuff we received that we were not invoiced for... as it is coming. However we are plicing PO\'s that are not closed and have no invoice...

</p>';
$required_sql1 = "CREATE TEMPORARY TABLE required_accounts
		SELECT pos_chart_of_accounts_required_id, required_account_name, pos_chart_of_account_type_id FROM pos_chart_of_accounts_required
		
		;
		";
$required_sql2 = "SELECT * FROM required_accounts WHERE 1 ";			
		$required_table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_chart_of_accounts_required_id',
			'get_url_link' => "required_chart_of_accounts.php?type=view",
			'url_caption' => 'view',
			'get_id_link' => 'pos_chart_of_accounts_required_id'),

		array(
			'th' => 'Rquired Chart Of Account Name',
			'mysql_field' => 'required_account_name',
			'sort' => 'required_account_name'),
		
		
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$required_sql1);
	$required_data = getTransactionSQL($dbc,$required_sql2);
	closeDB($dbc);
	$html .= createRecordsTable($required_data, $required_table_columns);

*/


$html = '';
$html.= '<h2>Account Types and the default link to the chart of accounts</h2>';
$html.= '<p>Account Types are used for us to set up an account... It simplifies what an account is for a user to set an account up. For example, a user can set up a manufacturing account without needing to know that it is an accounts apyable account. A credit card account will also be an accounts payable. A cash register will go to a cash account, and we might have a separate cash register account, so this will allow us to add that to the chart of account and change it. 

Linking an entire journal, like the purchases journal, to 1215 merhandies invntory happens elsewhere....

why elsewhere?

Starting to think that BINDERS should Link to the COA..... Store credits link to a COA, cash registers link to a COA... hmm...


</p>';
$account_type_sql = "CREATE TEMPORARY TABLE account_type
		SELECT pos_account_type_id, account_type_name, account_number, account_name, default_chart_of_account_id, caption, description FROM pos_account_type
		LEFT JOIN pos_chart_of_accounts ON pos_account_type.default_chart_of_account_id = pos_chart_of_accounts.pos_chart_of_accounts_id
		;
		";
$account_type_select_sql = "SELECT * FROM account_type WHERE 1 ORDER BY account_number ASC";			
		$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_account_type_id',
			'get_url_link' => "account_type.php?type=view",
			'url_caption' => 'view',
			'get_id_link' => 'pos_account_type_id'),

		array(
			'th' => 'Account Type Name',
			'mysql_field' => 'account_type_name',
			'sort' => 'purchase_order_number'),
		array(
			'th' => 'Chart Of Accounts Number',
			'mysql_field' => 'account_number',
			'sort' => 'account_number'),	
		array(
			'th' => 'Chart Of Accounts Name',
			'mysql_field' => 'account_name',
			'sort' => 'account_name'),	
		
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$account_type_sql);
	$accout_type_data = getTransactionSQL($dbc,$account_type_select_sql);
	closeDB($dbc);
	$html .= createRecordsTable($accout_type_data, $table_columns);




//link the purchase to the asset account - added asset_coa_account_id... this should come up as a default when entering the invoice. or we don't see it at all. It could be set at the account level. or it could be set here.

//$default_purchase_invoice = createCOAAssetAccountSelect();

//link the customer payments journal to the REVENUE chart of accounts
// reveune can be wholsale, retail, rental , etc.... 
// however the payment can be split across revenue.... there for each sales invoice line item goes to a revenue...
// like sales - retail sales - alterations sales - wholesale sales

$html.= '<h2>Journals and setting Default Journal Entries Links to the Chart Of Accounts</h2>';
$html.= '<p>Some journal entries always link to the same chart of accounts, so here we automate that to save time and confusion for entry. <br> 
Receving inventory - we can set this up in brands, however users will get confused. So we can link default brand inventory account here. Then the entry of receiving inventory will look to the brand, however it can be overridden.... receiveing only goes to pending inventory and pending ap....

Sales - sales entries for example post to 4000 sales revenue in the chart of accounts. We may have a wholesale that we can post to a different account, however a super user can edit that on the entry

purchases journal again link to a/p, pending inventory, pending ap, and inventory.

</p>';
$journal_link_sql = "CREATE TEMPORARY TABLE journal_link
		SELECT pos_journal_to_coa_link_id, link_name, pos_journal_to_coa_link.comments, account_number, account_name, pos_journal_to_coa_link.pos_chart_of_accounts_id  FROM pos_journal_to_coa_link
		LEFT JOIN pos_chart_of_accounts ON pos_journal_to_coa_link.pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
		;
		";
$journal_link_select_sql = "SELECT * FROM journal_link WHERE 1 ORDER BY account_number ASC";			
		$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_journal_to_coa_link_id',
			'get_url_link' => "journal_link.php?type=view",
			'url_caption' => 'view',
			'get_id_link' => 'pos_journal_to_coa_link_id'),

		array(
			'th' => 'Link',
			'mysql_field' => 'link_name',
			'sort' => 'link_name'),
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'sort' => 'comments'),
		array(
			'th' => 'Chart Of Accounts Number',
			'mysql_field' => 'account_number',
			'sort' => 'account_number'),	
		array(
			'th' => 'Chart Of Accounts Name',
			'mysql_field' => 'account_name',
			'sort' => 'account_name'),	
		
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$journal_link_sql);
	$journal_link_data = getTransactionSQL($dbc,$journal_link_select_sql);
	closeDB($dbc);
	$html .= createRecordsTable($journal_link_data, $table_columns);





include(HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>