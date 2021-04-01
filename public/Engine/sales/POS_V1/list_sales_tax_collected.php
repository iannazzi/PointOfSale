<?php


/*
	*This should list the sales and tax collected per tax jurisdiction
	
	SO....
	we need the total sales
	Total non-taxable sales
	Credit card deposits
	
	then the sum of the "regular" sales by jurisdiction
	then the sum of the "exempt" sales by jurisdiction
	
	however we need to do it the opposit way: we need the sum of the taxes and back calulate the sales....
	
	calculate it from the invoice and from the sales tax rates
	
	
*/
$page_title = 'Quarterly Sales And Use Tax';
$access_type = 'READ';
require_once('retail_sales_invoice_functions.php');

//include(HEADER_FILE);
$start_date = "2013-06-01";
$end_date = "2013-08-31";
$sale_price = getAppliedDiscountItemPriceSQL();

pprint("Start Date " . $start_date);
pprint("End Date " . $end_date);


//select the total tax collected per jurisdiction


//get the name, and the sum of the sales tax

pprint("************************************************************************");
pprint("Start By verifying the sums to the main queries. I know there is a problem with promotions...");



//separate by state including all localities: gross sales (taxable and exempt sales and non-taxable sales not including sales tax) SALES MADE IN NY => by store then, total non-taxable sales,total cc deposits
/*
$tax_rate_from_contents = TaxRateFromContentsSQL();
$sales_by_state_sql = "
	SELECT 
	round(sum($sale_price*quantity),2)  as gross_sales,
	round(sum(if($tax_rate_from_contents = 0,$sale_price*quantity,0)),2) as non_tax_sales
	

	
	FROM pos_sales_invoice
	LEFT JOIN pos_sales_invoice_contents ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id

	
	WHERE pos_sales_invoice.invoice_date >= '$start_date' AND pos_sales_invoice.invoice_date <= '$end_date' 
	AND content_type != 'CREDIT_CARD'
	";
	
	//for the quarter I want to know:
	//Total all sales + tax
	//Total tax collected
	//total gift cards sold
	//total all sales - tax- gift cards
	//total promotions
	
	$total_sales_sql = "
	CREATE TEMPORARY TABLE sales_invoices 
	
	(SELECT sum(extension-tax_total) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD') as gross_sales,
	
	(SELECT sum(tax_total) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as total_tax,
	
	(SELECT sum(extension) - sum(tax_total) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type = 'CREDIT_CARD') as gift_cards
	
	FROM pos_sales_invoice
	WHERE pos_sales_invoice.invoice_date >= '$start_date' AND pos_sales_invoice.invoice_date <= '$end_date' 
	AND invoice_status = 'CLOSED'
	;
	";
	
	*/
	
		$total_sales_sql = "
	CREATE TEMPORARY TABLE sales_invoices
	
	SELECT 
	sum(extension) as total_sales,
	sum(if(content_type != 'CREDIT_CARD', extension-tax_total,0)) as gross_sales,
	
	sum(tax_total) as tax_collected,
	
	sum(if((content_type != 'CREDIT_CARD' AND tax_total = 0), extension,0)) as non_tax_sales,
	
	sum(pos_sales_invoice_promotions.applied_amount) as promotion_total,
	
	
	
	
	
	 sum(if(content_type = 'CREDIT_CARD',extension,0)) as gift_cards

	FROM pos_sales_invoice
	LEFT JOIN pos_sales_invoice_contents ON pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
	LEFT JOIN pos_sales_invoice_promotions ON pos_sales_invoice_promotions.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
	LEFT JOIN pos_promotions ON pos_sales_invoice_promotions.pos_promotion_id = pos_promotions.pos_promotion_id
	WHERE DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' 
	AND invoice_status = 'CLOSED'
	;
	";
	preprint('PRMOTION IS NOT TOTALING< MOVING ON>>>>>>');
	$tmp_select_sql = "SELECT * FROM sales_invoices ";
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$total_sales_sql);
	$main_data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
	
	pprint("totals");
	preprint($main_data);

pprint("************************************************************************");
pprint("Account totals");

//credit card totals

$cc_total_sql = "SELECT sum(pos_sales_invoice_to_payment.applied_amount) as credit_card_totals FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		LEFT JOIN pos_sales_invoice
		ON pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id 
		WHERE pos_customer_payment_methods.payment_group = 'CREDIT_CARD'
		AND DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' ";
	pprint("cc_totals");
	preprint(getSQL($cc_total_sql));
	
	$cc_total_sql = "SELECT sum(pos_sales_invoice_to_payment.applied_amount) as credit_card_totals FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		LEFT JOIN pos_sales_invoice
		ON pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id 
		WHERE pos_customer_payment_methods.payment_group = 'CASH'
		AND DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' ";
	pprint("cash_totals");
	preprint(getSQL($cc_total_sql));
	$cc_total_sql = "SELECT sum(pos_sales_invoice_to_payment.applied_amount) as credit_card_totals FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		LEFT JOIN pos_sales_invoice
		ON pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id 
		WHERE pos_customer_payment_methods.payment_group = 'CHECK'
		AND DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' ";
	pprint("check_totals");
	preprint(getSQL($cc_total_sql));
		$cc_total_sql = "SELECT sum(pos_sales_invoice_to_payment.applied_amount) as credit_card_totals FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		LEFT JOIN pos_sales_invoice
		ON pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id 
		WHERE pos_customer_payment_methods.payment_group = 'STORE_CREDIT'
		AND DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' ";
	pprint("store credit_totals");
	preprint(getSQL($cc_total_sql));
	$cc_total_sql = "SELECT sum(pos_sales_invoice_to_payment.applied_amount) as credit_card_totals FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		LEFT JOIN pos_sales_invoice
		ON pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id 
		WHERE pos_customer_payment_methods.payment_group = 'OTHER'
		AND DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' ";
	pprint("other_totals");
/*	preprint(getSQL("SELECT pos_sales_invoice_to_payment.pos_sales_invoice_id, applied_amount
FROM pos_sales_invoice_to_payment
LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id
LEFT JOIN pos_customer_payment_methods ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
LEFT JOIN pos_sales_invoice ON pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
WHERE pos_customer_payment_methods.payment_group = 'OTHER'
AND DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' "));*/

	preprint(getSQL($cc_total_sql));
	$cc_total_sql = "SELECT sum(pos_sales_invoice_to_payment.applied_amount) as credit_card_totals FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		LEFT JOIN pos_customer_payment_methods
		ON pos_customer_payments.pos_customer_payment_method_id = pos_customer_payment_methods.pos_customer_payment_method_id
		LEFT JOIN pos_sales_invoice
		ON pos_sales_invoice_to_payment.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id 
		WHERE DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' ";
	pprint("payment_totals");
	preprint(getSQL($cc_total_sql));
	
	
		
//then by jurisdiction => non-exempt sales
//Purchases subject to tax
//net taxable sales and services

//then by jurisdiction => exempt sales
//Purchases subject to tax
//net taxable sales and services

//preprint($sale_price);
//preprint($tax_rate_from_contents);

/*$tax_juridictions = "
	SELECT 	
	Distinct local.jurisdiction_name, local.jurisdiction_code
		
		
	FROM pos_sales_invoice_contents 
	LEFT JOIN pos_sales_invoice ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	LEFT JOIN  pos_tax_jurisdictions as local
	ON pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = local.pos_tax_jurisdiction_id
	WHERE	DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' 
	AND content_type != 'CREDIT_CARD'
	";
pprint("Jurisdictions");
preprint(getSQL($tax_juridictions));*/

//sales for no tax jurisdiction....
$not_taxable_sales_sql = "
	SELECT 	
	
		
	
	sum(extension) as no_tax_jurisdiction

		
	FROM pos_sales_invoice_contents 
	LEFT JOIN pos_sales_invoice ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	LEFT JOIN  pos_tax_jurisdictions as local
	ON pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = local.pos_tax_jurisdiction_id
	WHERE	DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' 
	AND content_type != 'CREDIT_CARD'
	AND pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = 0
	
";
//pprint("Sales with no tax jurisdiction (probably out of state.......");
//preprint(getSQL($not_taxable_sales_sql));

/*Verified same as 4th query
$net_taxable_sales_sql = "
	SELECT 	
	local.jurisdiction_name, local.jurisdiction_code,
		
	
	sum(if($sale_price>state_exemption_value AND $tax_rate_from_contents > 0 ,$sale_price*quantity,0)) as net_taxable_sales_and_services,
		sum(if($sale_price>state_exemption_value AND $tax_rate_from_contents > 0 ,$sale_price*quantity*$tax_rate_from_contents/100,0)) as tax_collected
		
	FROM pos_sales_invoice_contents 
	LEFT JOIN pos_sales_invoice ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	LEFT JOIN  pos_tax_jurisdictions as local
	ON pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = local.pos_tax_jurisdiction_id
	WHERE	DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' 
	AND content_type != 'CREDIT_CARD'
	GROUP BY pos_local_tax_jurisdiction_id
	
";
pprint("net taxable main form");
preprint(getSQL($net_taxable_sales_sql));
*/
/* WAY OFF
//these are 'full tax' sales ..... if the value of the product is greater than the exemption value grab it....
$net_taxable_sales_sql2 = "
	SELECT 	
	local.jurisdiction_name, local.jurisdiction_code,
		
	
	sum(if((extension-tax_total)/quantity>state_exemption_value AND tax_total > 0 ,extension-tax_total,0)) as net_taxable_sales_and_services,
		sum(if((extension-tax_total)/quantity>state_exemption_value AND tax_total > 0 ,tax_total,0)) as tax_collected
		
	FROM pos_sales_invoice_contents 
	LEFT JOIN pos_sales_invoice ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	LEFT JOIN  pos_tax_jurisdictions as local
	ON pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = local.pos_tax_jurisdiction_id
	WHERE	DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' 
	AND content_type != 'CREDIT_CARD'
	GROUP BY pos_local_tax_jurisdiction_id
	
";



pprint("net taxable main form2");
preprint(getSQL($net_taxable_sales_sql2));

*/
/* WAY OFFFFFF 
$net_taxable_sales_sql3 = "
	SELECT 	
	local.jurisdiction_name, local.jurisdiction_code,
		
	
	sum(if((extension-tax_total)/quantity>state_exemption_value AND tax_total > 0 ,extension-tax_total,0)) as net_taxable_sales_and_services,
		sum(if((extension-tax_total)/quantity>state_exemption_value AND tax_total > 0 ,tax_total,0)) as tax_collected
		
	FROM pos_sales_invoice_contents 
	LEFT JOIN pos_sales_invoice ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	LEFT JOIN  pos_tax_jurisdictions as local
	ON pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = local.pos_tax_jurisdiction_id
	WHERE	DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' 
	AND content_type != 'CREDIT_CARD'
	GROUP BY pos_local_tax_jurisdiction_id
	
";



pprint("net taxable main form3");
preprint(getSQL($net_taxable_sales_sql3));
*/

$main4 = "
CREATE TEMPORARY TABLE main_tax
	SELECT 	
	local.jurisdiction_name, local.jurisdiction_code, state.jurisdiction_name as state, state_regular_tax_rate, local_regular_tax_rate, state_regular_tax_rate + local_regular_tax_rate as total_tax_rate,
		
	
	round(sum(if(content_type != 'CREDIT_CARD', if((extension-tax_total)/quantity>state_exemption_value AND state_regular_tax_rate+local_regular_tax_rate>0,extension-tax_total,0),0)),2 ) as sales,
	round(sum(if(content_type != 'CREDIT_CARD', if((extension-tax_total)/quantity>state_exemption_value AND state_regular_tax_rate+local_regular_tax_rate>0,tax_total,0),0)),2 ) as tax
		
	FROM pos_sales_invoice_contents 
	LEFT JOIN pos_sales_invoice ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	LEFT JOIN  pos_tax_jurisdictions as local
	ON pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = local.pos_tax_jurisdiction_id
	LEFT JOIN  pos_tax_jurisdictions as state
	ON pos_sales_invoice_contents.pos_state_tax_jurisdiction_id = state.pos_tax_jurisdiction_id
	WHERE	DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' 
	GROUP BY pos_local_tax_jurisdiction_id, pos_state_tax_jurisdiction_id
	;
";

	$tmp_select_sql = "SELECT *, tax/(total_tax_rate/100) as reverse_calculated_sales_from_tax FROM main_tax ";
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$main4);
	$main = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
pprint("net taxable main form4");
preprint($main);
/* Verified same as exemption 2 sql
$exemption_sql = "
	SELECT 	
	local.jurisdiction_name, local.jurisdiction_code, state.jurisdiction_name as state,state_exemption_tax_rate, local_exemption_tax_rate, state_exemption_tax_rate + local_exemption_tax_rate as total_tax_rate,
		
	
	round(sum(if(content_type != 'CREDIT_CARD', if($sale_price<=state_exemption_value,$sale_price*quantity,0),0)),2 ) as exempt_sales,
	round(sum(if(content_type != 'CREDIT_CARD', if($sale_price<=state_exemption_value,$sale_price*quantity*$tax_rate_from_contents/100,0),0)),2 ) as exempt_tax
		
	FROM pos_sales_invoice_contents 
	LEFT JOIN pos_sales_invoice ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	LEFT JOIN  pos_tax_jurisdictions as local
	ON pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = local.pos_tax_jurisdiction_id
	LEFT JOIN  pos_tax_jurisdictions as state
	ON pos_sales_invoice_contents.pos_state_tax_jurisdiction_id = state.pos_tax_jurisdiction_id
	WHERE	DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' 
	GROUP BY pos_local_tax_jurisdiction_id, pos_state_tax_jurisdiction_id
	
";
pprint("exemption tax schedule h");
preprint(getSQL($exemption_sql));
*/

$exemption_sql2 = "
	CREATE TEMPORARY TABLE schedule_h
	SELECT 	
	local.jurisdiction_name, local.jurisdiction_code, state.jurisdiction_name as state,state_exemption_tax_rate, local_exemption_tax_rate, state_exemption_tax_rate + local_exemption_tax_rate as total_tax_rate,
		
	
	round(sum(if(content_type != 'CREDIT_CARD', if((extension-tax_total)/quantity<=state_exemption_value,extension-tax_total,0),0)),2 ) as exempt_sales,
	round(sum(if(content_type != 'CREDIT_CARD', if((extension-tax_total)/quantity<=state_exemption_value,tax_total,0),0)),2 ) as exempt_tax
		
	FROM pos_sales_invoice_contents 
	LEFT JOIN pos_sales_invoice ON pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
	LEFT JOIN  pos_tax_jurisdictions as local
	ON pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = local.pos_tax_jurisdiction_id
	LEFT JOIN  pos_tax_jurisdictions as state
	ON pos_sales_invoice_contents.pos_state_tax_jurisdiction_id = state.pos_tax_jurisdiction_id
	WHERE	DATE(pos_sales_invoice.invoice_date) >= '$start_date' AND DATE(pos_sales_invoice.invoice_date) <= '$end_date' 
	GROUP BY pos_local_tax_jurisdiction_id, pos_state_tax_jurisdiction_id
	
";
	$tmp_select_sql = "SELECT *, exempt_tax/(total_tax_rate/100) as reverse_calculated_sales_from_tax FROM schedule_h ";
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$exemption_sql2);
	$schedule_h = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);

pprint("exemption tax schedule h2");
preprint($schedule_h);

pprint("************************************************************************");
pprint("Now we need to reverse calculate sales based on the tax collected.....");

$main_total_taxable = 0;

for($m=0;$m<sizeof($main);$m++)
{
	if ($main[$m]['total_tax_rate'] != 0)
	{
		$m_tax = $main[$m]['tax']/	($main[$m]['total_tax_rate']/100);
		$main_total_taxable = $main_total_taxable + $m_tax;
	}
}
//pprint('Main Taxable: ' .$main_total_taxable);

$h_total_taxable = 0;
for($m=0;$m<sizeof($schedule_h);$m++)
{
	if ($schedule_h[$m]['total_tax_rate'] != 0)
	{
		$h_tax = $schedule_h[$m]['exempt_tax']/	($schedule_h[$m]['total_tax_rate']/100);
		$h_total_taxable = $h_total_taxable + $h_tax;
	}
}


//pprint('Exempt taxable ' . $h_total_taxable);
$calculated_gross_taxable_sales = $main_total_taxable+$h_total_taxable;
pprint('Reverse calculated gross sales: ' .$calculated_gross_taxable_sales);
$err = $main_data[0]['gross_sales'] - $calculated_gross_taxable_sales;
pprint('Error to gross sales sum: ' . $err);




$use_tax_sql ="
	SELECT sum(use_tax) as use_tax FROM pos_general_journal WHERE DATE(invoice_date) >= '$start_date' AND DATE(invoice_date) <= '$end_date'
	";
pprint("use tax");
$use_tax = getSQL($use_tax_sql);
$purchases_subject_to_tax = $use_tax[0]['use_tax']/0.08;
//here is where I would need to calculate the use tax per jurisdiction... so maybe by the store id?
preprint('Use tax: ' . $use_tax[0]['use_tax'] . ' /.08% = purchases subject to tax in monroe county = ' . $purchases_subject_to_tax);

pprint("************************************************************************");
pprint("Final numbers");
pprint('Reverse calculated gross sales: ' .round($calculated_gross_taxable_sales,0));
pprint('Non-taxable sales: ' .round($main_data[0]['non_tax_sales'],0));
// have to manually calculate the sales and use tax for now... pprint('Sales And Use Tax: ' . $use_tax[0]['use_tax'] + );

//include(FOOTER_FILE);



?>
