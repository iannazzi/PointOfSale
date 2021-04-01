<?php
/*
	*operating_expenses.php
	*craig iannazzi 2-13-12
	
	*This file will sum all sales, cost of goods, and expenses for a given year
	
	there are two methods to sum: cash basis and accrual:
	Cash basis sums when paid
	accrual sums when invoiced.....
	Currently this is a mix of the two, so not really correct.
	
*/
$page_level = 7;
$page_navigation = 'accounting';
$page_title = 'Operating Expenses';
$binder_name = 'Operating Expenses';
require_once ('../accounting_functions.php');
//For the year we will default to the year of today if no get or post provided
//We can adjust that via GET and POST
// Check for a valid ID, through GET or POST:
if ( (isset($_GET['year'])) && (is_numeric($_GET['year'])) ) 
{ 
	$year = $_GET['year'];
} 
elseif ( (isset($_POST['year'])) && (is_numeric($_POST['year'])) ) 
{ // Form submission.
	$year = $_POST['year'];
} 
else 
{ // No valid ID, kill the script.
	$today = getdate();
	$year = $today['year'];
}

// Set up the store ID
if ( (isset($_GET['store_id']))  ) 
{ 
	$store_id = $_GET['store_id'];
	if ($store_id == 'all')
	{
		if( $_SESSION['level'] > 7)
		{
			//OK
		}
		else
		{
			//user ganked the get and switched to all - force the select to thier default store
			$store_id = $_SESSION['store_id'];
			$option_all = 'off';
		}
    }
} 
elseif ( (isset($_POST['store_id'])) ) 
{ // Form submission.
	$store_id = $_POST['store_id'];
	if ($store_id == 'all')
	{
		if( $_SESSION['level'] > 7)
		{
			//OK
		}
		else
		{
			$store_id = $_SESSION['store_id'];
			$option_all = 'off';
		}
    }
} 
else 
{ 
	// set default store
	// depending on the user, this should be either all or thier default store...
	if( $_SESSION['level'] > 7)
	{
		$store_id = 'all';
		$option_all = 'on';
	}
	else
	{
    	$store_id = $_SESSION['store_id'];
    	$option_all = 'off';
    }
}
//Set up the option for select all stores - based on user level
if( $_SESSION['level'] > 7)
{
	$option_all = 'on';
}
else
{
	$option_all = 'off';
}


// crete the form - real simple, just an input box....
$html ='<div class="statement_frame">';
$html .=  '<script src="operating_expenses.js"></script>'.newline();
$html .= '<link type="text/css" href="operating_expense.css" rel="Stylesheet"/>'.newline();
$html .= '<div><span class = "income_logo">' . getSetting('company_logo') . ' </span><span class ="income_logo2">Income Statement</span></div>';
$today = getDate();
$html .= '<p><b>' . getSetting('company_legal_name') . '</b> ' . $today['weekday'] . ', ' . $today['month'] . ' ' . $today['mday'] . ', ' . $today['year'] . '.</p>';
$html .= '
<form id = "operating_epense_year" name = "operating_expense_year" action = "operating_expenses.php" method = "get">
<table><tr>
<td><p>Year:<input class = "year" size = "4" maxlength = "4" name ="year" value = "' . $year . '" id = "year" onchange="validate_year(this)" onkeyup="checkInput(this, \'0123456789\')"/></td>';
$html .= '<td>Store: </td>';

$html .= '<td>' . createStoreSelect('store_id', $store_id, $option_all);
$html .= '</td></tr></table>';
$html .= '<input class = "button" type="submit" value="View Report"/>
</p>
</form>';

$html .= '<table class ="operating_expense_table">';
$html .= createOperatingExpenseHeader();
$html .= createOperatingIncomeTable($year, $store_id);
//$html .= createOperatingIncomeFooter($year, $store_id);
//$html .= createOperatingExpenseTitle();
//$html .= createOperatingExpenseTable($year, $store_id);
//$html .= createOperatingExpenseFooter($year, $store_id);
//$html .= createOperatingTaxFooter($year, $store_id);
//$html .= createOperatingExpenseBottomLine($year, $store_id);
$html .= '</table>';
$html .= '</div>';
include(HEADER_FILE);
echo $html;
include(FOOTER_FILE);
function createOperatingExpenseHeader()
{
	return '
	<tbody id = "income_body" class = "title_body">
	<tr>
		<th class = "title" >INCOME</th>
		<th class = "noborder"></th>
		<th class = "month">January</th>
		<th class = "month">February</th>
		<th class = "month">March</th>
		<th class = "month">April</th>
		<th class = "month">May</th>
		<th class = "month">June</th>
		<th class = "month">July</th>
		<th class = "month">August</th>
		<th class = "month">September</th>
		<th class = "month">October</th>
		<th class = "month">November</th>
		<th class = "month">December</th>
		<th class = "month">TOTAL</th>
	</tr>
	</tbody>';
}
function createOperatingIncomeTable($year, $store_id)
{
	$html = '
	<tbody class = "data">
	<tr>
		<th>Gross Sales (No Sales Tax)</th>
		<td class = "currency">$</td>
		
		';
	$gross_sales_total = 0;
	for($month = 1; $month<13; $month++)
		{
			$gross_sales = "SELECT sum((SELECT sum(extension - tax_total)
			
		
		FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD' AND quantity > 0
		) - 
		(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
			AND promotion_type = 'Post Tax')
		
		
		) as gross_sales
		
		
		FROM pos_sales_invoice WHERE month(invoice_date) = '$month' 
									AND year(invoice_date) = '$year'
									
						
									";
			
			if ($store_id != 'all')
			{
				$gross_sales = $gross_sales . " AND pos_store_id = '" . $store_id ."'";
			}
			$gross_sales_month[$month] = getSingleValueSQL($gross_sales);
			$html .= '<td>' . number_format($gross_sales_month[$month],0) . '</td>';
			$gross_sales_total = $gross_sales_total + $gross_sales_month[$month];
			
		}
	$html .= '<td>' . number_format($gross_sales_total,0) . '</td>';

	$html .= '
	</tr>
	<tr>
		<th>Returns and Allowances</th>
		<td class = "currency">$</td>';
		$returns_total = 0;
		for($month = 1; $month<13; $month++)
		{
			$returns = "SELECT sum((SELECT sum(extension - tax_total)
			
		
		FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD' AND quantity < 0
		)) as returns
		
		
		FROM pos_sales_invoice WHERE month(invoice_date) = '$month' 
									AND year(invoice_date) = '$year'
									
						
									";
			
			if ($store_id != 'all')
			{
				$returns = $returns . " AND pos_store_id = '" . $store_id ."'";
			}
			$returns_month[$month] = getSingleValueSQL($returns); 
			$returns_total = $returns_total + $returns_month[$month];
			$html .= '<td>' . number_format($returns_month[$month],0) . '</td>';
			
		}
		$html .= '<td>' . number_format($returns_total,0) . '</td>';

		
		
	$html .= '
	</tr>';
	/*$html .= '
	<tr>
		<th>Sales Tax</th>
		<td class = "currency">$</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
		<td>tbd</td>
	</tr>';*/
	
	$html .= '
	<tr>
		<th>Net Sales</th>
		<td class = "currency">$</td>
	';
		$net_sales_total = 0;
		for($month = 1; $month<13; $month++)
		{
			
			$net_sales_month[$month] = $gross_sales_month[$month] + $returns_month[$month];
			$html .= '<td>' . number_format($net_sales_month[$month],0) . '</td>';
			$net_sales_total = $net_sales_total + $net_sales_month[$month];
			
		}
		$html .= '<td>' . number_format($net_sales_total,0) . '</td>';
	$html .='
	</tr>
	<tr>
		<th>Cost Of Goods Sold</th>
		<td class = "currency">$</td>';
		
		//loop through each month
		//COGS technically should track all the way back to the purchase order...
		//LIFO FIFO....
		//currently this is just going to grab the cost from the product...
		
		$cogs_total = 0;
		for($month = 1; $month<13; $month++)
		{
			$cogs = "SELECT sum(pos_products.cost) FROM pos_products
						LEFT JOIN pos_products_sub_id ON pos_products.pos_product_id = pos_products_sub_id.pos_product_id
						LEFT JOIN pos_sales_invoice_contents ON pos_sales_invoice_contents.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id LEFT JOIN pos_sales_invoice USING (pos_sales_invoice_id)
						
									WHERE month(invoice_date) = '$month' 
									AND year(invoice_date) = '$year'
									AND invoice_status = 'CLOSED'
						
									";
			if ($store_id != 'all')
			{
				$cogs = $cogs . " AND pos_store_id = '" . $store_id ."'";
			}
			$cogs_month[$month] = getSingleValueSQL($cogs);
			$cogs_total = $cogs_total + $cogs_month[$month];
			$html .= '<td>' . number_format($cogs_month[$month],0) . '</td>';
			
		}
		$html .= '<td>'  . number_format($cogs_total,0). '</td>';
		
		
		$html.='
	</tr>
	</tbody>';

//footer

	$html .=  '
	<tbody class = "income_footer">
	<tr>
		<th >Gross Margin</th>
		<td class = "currency">$</td>';
	
	for($month = 1; $month<13; $month++)
	{
			$gross_margin[$month] = $net_sales_month[$month] - $cogs_month[$month];
			$html .= '<td>'  . number_format($gross_margin[$month] ,0). '</td>';
	}
	$gross_margin_total = $net_sales_total - $cogs_total;
	$html .= '<td>'  . number_format($gross_margin_total,0). '</td>';
	$html .= '
	</tr>
		<tr>
		<th >Gross Margin (%)</th>
		<td class = "currency">$</td>';
	
	for($month = 1; $month<13; $month++)
	{
			$gross_margin_percent[$month] = (abs($net_sales_month[$month]) < 0.0001) ? 0 : 100*$gross_margin[$month]/$net_sales_month[$month];
			$html .= '<td>'  . number_format($gross_margin_percent[$month] ,1). '%</td>';
	}
	if(abs($net_sales_total) > 0.0001)
	{
		$gross_margin_percent_total = 100*$gross_margin_total/$net_sales_total;
	}
	else
	{	
		$gross_margin_percent_total = 0;
	}
	$html .= '<td>'  . number_format($gross_margin_percent_total,1). '%</td>';
	$html .= '
	</tr>
	</tbody>';

//function createOperatingExpenseTitle()

	$html .= '
	<tbody class = "title_body">
		<tr><th class = "title" colspan ="4">OPERATING EXPENSES</th>
		<th class = "calc" colspan ="11">Calculated by finding the invoice, looking up the applied payment amount, at the payment date</th></tr>
	</tbody>';

//function createOperatingExpenseTable($year, $store_id)

	$expense_categories = getExpenseChartOfAccounts();
	$html .=   '<tbody class = "data">';
	$category_sum = array();
	for($month = 1; $month<13; $month++)
		{
		$category_sum[$month] = 0;
		}
	$year_category_sum = 0;
	for($category = 0;$category < sizeof($expense_categories); $category++)
	{
		$year_sum = 0;
		$html .= '<tr>';
		$html .= '<th>' . $expense_categories[$category]['account_name'] . '</th>';
		$html .= '<td class = "currency">$</td>';
		//loop through each month
		for($month = 1; $month<13; $month++)
		{
			$general_journal_sql = "SELECT sum(pos_invoice_to_payment.applied_amount) 
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
							AND pos_general_journal.pos_chart_of_accounts_id = " .$expense_categories[$category]['pos_chart_of_accounts_id'];	
			
			if ($store_id != 'all')
			{
				$general_journal_sql = $general_journal_sql . " AND pos_general_journal.pos_store_id = '" . $store_id ."'";
				
			}

			
			//$html .= '<td>' . round(getSingleValueSQL($expense_category_month_year_sql),0) . '</td>';
			//$html .= '<td>' . round(getSingleValueSQL($payment_sql),0) . '</td>';
			$month_sum = getSingleValueSQL($general_journal_sql);
			$year_sum = $year_sum + $month_sum;
			$year_category_sum = $year_category_sum +$month_sum;
			$html .= '<td>' . '<a href= "list_operating_expense_by_month_category.php?year='.$year.'&pos_store_id='.$store_id.'&month='.$month.'&pos_chart_of_accounts_id='. $expense_categories[$category]['pos_chart_of_accounts_id'].'" target = "_blank" >' .number_format($month_sum,0) . '</a></td>';
			$category_sum[$month] = $category_sum[$month] + $month_sum;
		}
		//Print the sum for the year
		$html .= '<td>' . number_format($year_sum,0) . '</td>';
		$html .= '</tr>';
		
	}
	$html .= '</tbody>';

//function createOperatingExpenseFooter($year, $store_id)

	$html .=  '
	<tbody class = "income_footer">
		<tr>
		<th >Total Operating Expenses</th>
		<td class = "currency">$</td>';
		$expense_total = 0;
		for($month = 1; $month<13; $month++)
		{
		
	
			$html .= '<td>' . number_format($category_sum[$month],0) . '</td>';
			
		}
		
			$html .= '<td>'  . number_format($year_category_sum,0) . '</td>';
		$html .= '</tr>';
		
	$html.='
	

	</tr>
	</tbody>';

//function createOperatingTaxFooter($year, $store_id)

	$html .= '
	<tbody class = "income_summary">
	<tr>
		<th >Net Income Before Taxes</th>
		<td class = "currency">$</td>';
		$net_income_total = 0;
for($month = 1; $month<13; $month++)
	{
			$net_income[$month] = $gross_sales_month[$month] + $returns_month[$month] - $cogs_month[$month] - $category_sum[$month];
			$html .= '<td>'  . number_format($net_income[$month] ,0). '</td>';
	}
	$net_income_total = $gross_sales_total + $returns_total - $cogs_total - $year_category_sum;
	$html .= '<td>'  . number_format($net_income_total,0). '</td>';
	
$html .='
	</tr>
	<tr >
		<th >Procision For Taxes (20%)</th>
		<td class = "currency">$</td>
';
$net_taxes_total =0;
for($month = 1; $month<13; $month++)
	{
			$net_taxes[$month] = ($net_income[$month]>0) ?  $net_income[$month]*0.2 : 0;
			$html .= '<td>'  . number_format($net_taxes[$month] ,0). '</td>';
			$net_taxes_total = $net_taxes[$month] + $net_taxes_total;
	}
	
	$html .= '<td>'  . number_format($net_taxes_total,0). '</td>';
	$html .='
	</tr>
	</tbody>';

//function createOperatingExpenseBottomLine($year, $store_id)

	$html .= '
	<tbody class = "income_footer">
		<tr>
		<th >NET INCOME AFTER TAXES</th>
		<td class = "currency">$</td>
	';
	
$net_after_taxes_total =0;
for($month = 1; $month<13; $month++)
	{
			$net_after_taxes[$month] = $net_income[$month] - $net_taxes[$month];
			$html .= '<td>'  . number_format($net_after_taxes[$month] ,0). '</td>';
			$net_after_taxes_total = $net_after_taxes_total+ $net_after_taxes[$month];
	}
	
	$html .= '<td>'  . number_format($net_after_taxes_total,0). '</td>';
	$html .='
	</tr>
	</tbody>';
	
	return $html;
}
?>



