<?

/*

CRAIG IANAZZI 2014-08-02 on the train to NYC sweet seats

List the expenses that sum up for the operating expense.... e


*/
$page_navigation = 'accounting';
$page_title = 'Operating Expenses';
$binder_name = 'Operating Expenses';
require_once ('../accounting_functions.php');

//first check for some variables
if (
	( isset($_GET['pos_store_id'])) &&  
	( isset($_GET['year'])) && 
	(is_numeric($_GET['year'])) && 
	(isset($_GET['month'])) && 
	(is_numeric($_GET['month'])) &&   
	(isset($_GET['pos_chart_of_accounts_id'])) && 
	(is_numeric($_GET['pos_chart_of_accounts_id']))
	)
{ 
	$pos_store_id = $_GET['pos_store_id'];
	$year = $_GET['year'];
	$month = $_GET['month'];
	$pos_chart_of_accounts_id = $_GET['pos_chart_of_accounts_id'];
	$page_title =   $month . '-' . $year . ' ' . getChartOfAccountName($pos_chart_of_accounts_id) . ' Operating Expenses';
} 
else 
{ 
	include(HEADER_FILE);
	echo '<p class="error">Missing month and or category id\'s</p>';
	include(FOOTER_FILE);
}


//now for the sql statement
//we want to find the expense at the payment date. this changes based on accounting setup.
// in english: find all payment that link to a general journal receipt or invoice at the specified date, store, and category


	$general_journal_sql = "SELECT pos_general_journal_id, invoice_number, supplier, description, pos_invoice_to_payment.applied_amount
							FROM pos_general_journal
							LEFT JOIN pos_invoice_to_payment ON pos_general_journal.pos_general_journal_id=pos_invoice_to_payment.pos_journal_id
							LEFT JOIN pos_payments_journal ON pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
							LEFT JOIN pos_chart_of_accounts on pos_general_journal.pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
							LEFT JOIN pos_chart_of_account_types ON pos_chart_of_accounts.pos_chart_of_account_type_id = pos_chart_of_account_types.pos_chart_of_account_type_id
							WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL'
							AND pos_chart_of_account_types.account_type_name = 'Expense'
							AND pos_chart_of_accounts.active = 1
							AND month(pos_payments_journal.payment_date) = '$month' 
							AND year(pos_payments_journal.payment_date) = '$year'
							AND pos_general_journal.pos_chart_of_accounts_id = " .$pos_chart_of_accounts_id;	

if ($pos_store_id != 'all')
			{
				$general_journal_sql = $general_journal_sql . " AND pos_general_journal.pos_store_id = '" . $pos_store_id ."'";
				
			}
			
			$gj_data = getSQL($general_journal_sql);
	$total = 0.0;
	for($gj=0;$gj<sizeof($gj_data);$gj++)
	{
		$total = $total + $gj_data[$gj]['applied_amount'];
	}
	//preprint($gj_data);
	//preprint($total);
	$html = '';
	$monthName = date("F", mktime(0, 0, 0, $month, 10));
	$html .= '	<P >Listing of Expenses For Category: ' . getChartOfAccountName($pos_chart_of_accounts_id)  .' - ' .getChartOfAccountNumber($pos_chart_of_accounts_id) .' For ' .$monthName . ', ' . $year .'</p>';
	$html .= '<table class ="dataTable">';
	
	
	$html .= '<tr>';
		$html .= '<th>' . 'General Journal ID</td>';
		$html.='<th>' . 'Invoice Number</td>';
		$html.='<th>' . 'Supplier</td>';
		$html.='<th>' . 'Description</td>';
		$html.='<th>' . 'Amount</td>';
		$html .= '</tr>';
	$html .= '	</tr>';
	
	
	
	$html .=   '<tbody>';
	
	for($gj=0;$gj<sizeof($gj_data);$gj++)
	{
		$html .= '<tr>';
		$html .= '<td>' . '<a href= "' . POS_ENGINE_URL . '/accounting/GeneralJournal/view_general_journal_entry.php?pos_general_journal_id='.$gj_data[$gj]['pos_general_journal_id'].'" target = "_blank" >' .$gj_data[$gj]['pos_general_journal_id'] . '</a></td>';
		$html.='<td>' . $gj_data[$gj]['invoice_number'] . '</td>';
		$html.='<td>' . $gj_data[$gj]['supplier'] . '</td>';
		$html.='<td>' . $gj_data[$gj]['description'] . '</td>';
		$html.='<td>$' . number_format($gj_data[$gj]['applied_amount'],2) . '</td>';
		$html .= '</tr>';
	}
	$html .=  '</tbody>';
	$html .=   '<tfoot>';
	$html .= '<tr>';
	$html .= '	<th class = "income_footer" style = "text-align:left;">TOTAL</th>';
	$html .= '	<th class = "income_footer" colspan = "3">'.'</th>';
	$html .= '	<th class = "income_footer" style = "text-align:right;">$'.$total.'</th>';
	$html .= '	</tr>';	
	$html .=   '</tfoot>';

	$html .= '</table>';
	include(HEADER_FILE);
	echo $html;
	include(FOOTER_FILE);
	
?>