<?
/*########################################################################################

Balance sheet.... awww the balance sheet Craig Iannazzi 2014-03-03

From http://www.dwmbeancounter.com/moodle/mod/resource/view.php?id=14
Balance Sheet Accounts

Assets
Liabilities
Owner's (Stockholders') Equity
Normally, the order of the listing of the asset and liability accounts is based on liquidity. The most liquid accounts are listed first. Thus, when listing assets, cash is listed before accounts receivable which comes before inventory. Likewise for liabilities, accounts payable comes before notes payable because accounts payable are normally paid before notes payable.

Balance sheet as of - so we need a date select.. that works.

from the date we need to see something like this:

Try to follow the chart of accouts:


Current assets:
		1000
		1010 Cash on Hand (payments journal in/out, sales journal or customer payments journal in, payments of expenses)
			1010 Cash Asset bra bag 1 cash register 130
			1010 Cash Asset bra bag 2 cash register 200
			1010 craigs cash account
		1020 Checking (invoice payments, cash receipts,  payments journal transfers)
			1020 CNB .....
			1020 CNB Payroll ... etc
		1100 receivables (sales - cash receipts journal for each account, payments journal transfers)
			1100 Chase Payment
			1100 AMEX1
			1100 Square
			1100 etc...
		1215 Merhcandise Inventory 
			No sub accounts....
			(how do we sum inventory?) => check LIFO/FIFO setting
			last count (inventory) + received (purchase orders) - sold (sales)
			per sub ID look at the whole history in receive, inventory, and sold. 
			Depending on LIFO/FIFO the subid value will be different.
		
		Fixed Assets: not really dealing with this?
		
		
		
		Current Liabilities
		2000 Accounts Payable (get all the data from the payments, purchases, general journal...)
			2000 MJ
			2000 Anita
			2000 etc....
		2100, 2150, 2200 Store Credit Pyable + deposits, etc. => the sales journal needs to link gift cards and store credit to this 2400 chart of accounts, does it need an actual account? If we want an account listing, yes?
		
		2310 - sales tax payable => sales tax goes onto an account, one per "state". Payments go to the account. The account would be the state in the taxing jurisdictions.  The system would need "tax jurisdiciton" => chart of accounts.
Current assets
	cash
	ar
	notes payable - short term
	inventory
Fixed Assets
	land
	machinery and equipment
	auto
	long term notes payable

Liabilities
	Current
		AP
		payroll due
		taxes payable
		loans
	Long term
		Mortgage
		Loans
		Other



*/
$binder_name = 'Balance Sheet';
$access_type = 'READ';
require_once ('../accounting_functions.php');



$page_title = 'Balance Sheet';
$form_handler = basename($_SERVER['PHP_SELF']);
$fold = true; //to collapse folds for readability. Do not change. Ever.

if(isset($_GET['balance_sheet_date']))
{
	$date = $_GET['balance_sheet_date'];
}
else
{
	//use today
	$date = date('Y-m-d');
}


if($fold) //show the date select 
{
$html = '<p>Select the Date to Calculate the Balance Sheet</p>';
$html .= '<form action="' . $form_handler.'" method="GET">';

$html .= dateSelect('balance_sheet_date',$date,'') ;
$html .= '<input class = "button" style="width:200px" type="submit" name="balance_sheet_date_submit" value="Calculate"/>';
//$html .= '<input class = "button" style="width:200px" type="submit" name="cancel" value="No Thanks"/>';
$html .= '</form>';

$newDate = date('F jS, Y', strtotime($date));


}

//set up the table
$html .= '<table class="linedTable">';
$html .= '<thead>';
$html .= '<tr><td colspan = "4" style="text-align:center"><h2><span class = "income_logo">' . getSetting('company_logo') . ' </span><br>Balance Sheet as of ' . $newDate . '</span></h2></td></tr>';
$html .= '</thead>';
$html .= '<tbody>';
$html .= '<tr><td colspan = "4"><h3>Assets</h3></td></tr>';
$html .= '</tbody>';
//now calculate the balance sheet

if ($fold) //get the liabilities....
{
	/*now what I think i want is this:
	
		chart_of_account_number, chart_of_account_name, chart_of_account_typ, sub_account_number, sub_account_type, balance
	
		
	
	*/
	

	$tmp_sql = "
CREATE TEMPORARY TABLE chart_of_accounts

SELECT pos_accounts.pos_account_id, pos_accounts.company, pos_chart_of_accounts.account_number as chart_of_account_number, pos_chart_of_accounts.account_name as chart_of_account_name,  pos_chart_of_account_types.account_type_name,
		
		pos_accounts.balance_init +
		
		COALESCE((SELECT sum(entry_amount-discount_applied) FROM pos_general_journal WHERE pos_account_id = pos_accounts.pos_account_id AND entry_type = 'Invoice' AND DATE(invoice_date) <= '$date'),0) 
		
		+
		
		(SELECT COALESCE(sum(invoice_amount-discount_applied),0) FROM pos_purchases_journal WHERE invoice_type = 'Regular' AND pos_account_id = pos_accounts.pos_account_id AND DATE(invoice_date) <= '$date')
		
		-
		
		(SELECT COALESCE(sum(invoice_amount),0) FROM pos_purchases_journal WHERE invoice_type = 'Credit Memo' AND pos_account_id = pos_accounts.pos_account_id AND DATE(invoice_date) <= '$date')
		
		+
				
		(SELECT COALESCE(sum(payment_amount),0) FROM pos_payments_journal WHERE  DATE(payment_date) <= '$date' AND (pos_payments_journal.pos_account_id = pos_accounts.pos_account_id OR pos_payments_journal.pos_account_id IN (SELECT act2.pos_account_id FROM pos_accounts as act2 WHERE act2.linked_pos_account_id = pos_accounts.pos_account_id)))
		
		
		-
		
		(SELECT COALESCE(sum(payment_amount),0) FROM pos_payments_journal WHERE DATE(payment_date) <= '$date' AND pos_payee_account_id = pos_accounts.pos_account_id )
		
		
		 as balance

FROM pos_accounts
LEFT JOIN pos_chart_of_accounts
ON pos_chart_of_accounts.pos_chart_of_accounts_id = pos_accounts.parent_pos_chart_of_accounts_id
LEFT JOIN pos_chart_of_account_types USING (pos_chart_of_account_type_id)
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
WHERE pos_account_type.account_type_name != 'Debit Card' 


;
";
//WHERE pos_chart_of_account_types.account_type_name = 'Current Liabilities'

$tmp_select_sql = "SELECT sum(balance), chart_of_account_number FROM chart_of_accounts GROUP BY chart_of_account_number";
$tmp_select_sql = "SELECT company, balance, chart_of_account_name, chart_of_account_number FROM chart_of_accounts WHERE account_type_name = 'Current Liabilities' ORDER BY  chart_of_account_number ASC, company ASC";





	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	//preprint($data);
	closeDB($dbc);
}
$html .= '<tbody>';
$html .= '<tr ><td colspan = "4"><h3>Liabilities</h3></td></tr>';
$total_liabilities = 0;
for($i=0;$i<sizeof($data);$i++)
{
	$html .= '<tr>';
	$html .= '<td>' . $data[$i]['chart_of_account_number'] .'</td>';
	$html .= '<td>' . $data[$i]['chart_of_account_name'] .'</td>';
	$html .= '<td>' . $data[$i]['company'] . '</td>';
	$html .= '<td>' . number_format($data[$i]['balance'],0) . '</td>';

	$html .= '</tr>';
	$total_liabilities = $total_liabilities + $data[$i]['balance'];
}
$html .= '</tbody>';
$html .= '<tbody>';
$html .= '<tr><td>Equity</td></tr>';
$html .= '</tbody>';
$html .= '</table>';

include(HEADER_FILE);
echo $html;
include(FOOTER_FILE);



?>