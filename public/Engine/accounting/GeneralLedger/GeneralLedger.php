<? php


/* 	THE GENERAL LEDGER

http://www.dwmbeancounter.com/moodle/mod/resource/view.php?id=14

What's the General Ledger ? 
The General Ledger is just a book (manual system) or computer file (computer system) containing all the account balances and activity (increases and decreases) for all of a business's assets, liabilities, equity, revenue, and expense accounts that are included in the business's chart of accounts. The General Ledger has an account for each account that is listed in the chart of accounts.



	This will summarize all accounting activity by date. It is a calculation of the system
	
	it will look like:
	
	DATE	COA	SUB-ACCOUNT		DEBIT	CREDIT
	
	
	And it will go in pairs
	
	We can search by date range, account.
	
	Should be pretty crazy.....

*/


//GENERAL JOURNAL
// SIMPLE Receipt: DEBIT EXPENSE CATAGORY
//											CREDIT ACCOUNTS PAYABLE . SUB ACCOUNT
//					
// go to the payments journal and get the account paid... back up to the general journal to get the COA

// ON ACCOUNT	DEBIT EXPENSE CATAGORY
//											CREDIT ACCOUNTS PAYABLE . SUB ACCOUNT
// go to the general journal and find accounts paid... 


//PAYMENTS 
//PAY CC					DEBIT ACCOUNTS PAYABLE . SuB account
//											CREDIT CHECKING ACCOUNT . sub account
//This all comes from the payments journal

//PURCHASES JOURNAL
//							DEBIT 1215 Merchandise Inventory
//												Credit AP . sub account
//	go to the purchases journal and find account paid. Link to the COA through settings - no on the journal...

//PAy purchase invoice and take a discount
//				DEBIT AP - sub account
//										credit 5950 purchase discount <= where does this come from? The Journal
//										credit payment account									



//RECEIVE EVENTS

//INVENTORY (SHRINK?)

//CUSTOMER PAYMENTS (CASH RECEIPTS JOURNAL)




?>