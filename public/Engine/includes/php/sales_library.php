<?php
function getSalesInvoiceDate($pos_sales_invoice_id)
{
	$sql = "SELECT invoice_date from pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql);
}

function reloadInvoiceTaxContents($pos_sales_invoice_id)
{
	//when the date of the invoice is changed this needs to be called. Also when the shipping address changes this needs to be called.....
	$contents = getInvoiceContents($pos_sales_invoice_id);
	$invoice_date = getSalesInvoiceDate($pos_sales_invoice_id);
	$pos_store_id = getSalesInvoiceStore($pos_sales_invoice_id);
	for($row=0;$row<sizeof($contents);$row++)
	{
		//get the tax...
		if($contents[$row]['content_type'] == 'CREDIT_CARD')
		{
			//special handeling of credit cards... there is no tax....
		}
		else
		{
			if($contents[$row]['ship'] == 1 OR $contents[$row]['content_type'] == 'SHIPPING')
			{
				$pos_address_id = getSalesInvoiceAddress($pos_sales_invoice_id);
				$zip_code = getZipCode($pos_address_id);
				//preprint('zip: ' . $zip_code);
				$pos_state_id = getAddressStateId($pos_address_id);
				//preprint('state' . $pos_state_id);
				if($zip_code != '')
				{
					$pos_local_tax_jurisdiction_id = getTaxJurisdictionFromZipCode($zip_code);
					//preprint('pos_local_tax_jurisdiction_id: ' . $pos_local_tax_jurisdiction_id);
				}
				else
				{
					//address is effed so default to local jurisdiction
					$pos_local_tax_jurisdiction_id = getStoreTaxJurisdictionID($pos_store_id);
					$pos_state_id = getStoreStateId($pos_store_id);
				}
			
			}
			else
			{
				$pos_local_tax_jurisdiction_id = getStoreTaxJurisdictionID($pos_store_id);
				$pos_state_id = getStoreStateId($pos_store_id);
			}
			$tax = getProductTaxArray($pos_local_tax_jurisdiction_id, $pos_state_id, $contents[$row]['pos_sales_tax_category_id'], $invoice_date);

						
			$tax['tax_rate'] = calculateInvoiceContentTaxRate($contents[$row], $tax);
			$tax['tax_total'] = caclulateInvoiceContentTaxAmount($contents[$row], $tax);
			
			
			$content_key_val_id['pos_sales_invoice_content_id'] = $contents[$row]['pos_sales_invoice_content_id'];
			$results[] = simpleUpdateSQL('pos_sales_invoice_contents', $content_key_val_id, $tax);
		}
		
	}
	
	
}

function getSalesInvoiceData($pos_sales_invoice_id)
{
	$sql = "SELECT * from pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSQL($sql);
}
function getSalesInvoiceTaxCalculationMethod($pos_sales_invoice_id)
{
	$sql = "SELECT tax_calculation_method FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql);
}
function getCustomerFromSalesInvoice($pos_sales_invoice_id)
{
	$sql = "SELECT pos_customer_id FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql);
}
function formatCardNumber($card_number)
{
		$cc_p1=substr($card_number,0,4);
   		$cc_p2=substr($card_number,4,4);
   		$cc_p3=substr($card_number,8,4);
  		$cc_p4=substr($card_number,12,4);
  		return $cc_p1 . ' ' . $cc_p2 . ' ' . $cc_p3 . ' ' . $cc_p4;
}
function getTaxCategoryName($pos_sales_tax_category_id)
{
	$sql = "SELECT tax_category_name FROM pos_sales_tax_categories WHERE pos_sales_tax_category_id = $pos_sales_tax_category_id";
	return getSingleValueSQL($sql);
}
function getSalesInvoiceNumber($pos_sales_invoice_id)
{
	return getSingleValueSQL("SELECT invoice_number FROM pos_sales_invoice WHERE pos_sales_invoice_id=$pos_sales_invoice_id");
	
}

function getSalesInvoiceDateFromDatetime($pos_sales_invoice_id)
{
	return getSingleValueSQL("SELECT DATE(invoice_date) FROM pos_sales_invoice WHERE pos_sales_invoice_id=$pos_sales_invoice_id");
	
}
function getQuarterlyExemptSalesTax($qtr, $pos_tax_jurisdiction_id)
{
}
function getQuarterlyNonExemptSalesTax($qtr, $pos_tax_jurisdiction_id)
{
}
function getRetailSalesInvoiceTotalArray($pos_sales_invoice_id)
{
	$totals['full_price_total'] = getSalesInvoiceFullPriceTotal($pos_sales_invoice_id);
	$totals['discounted_total'] = getSalesInvoiceDiscountedTotal($pos_sales_invoice_id);
	$totals['pre_promotion_subtotal'] = getPrePromotionSubTotal($pos_sales_invoice_id);
	$totals['in_store_promotions_applied'] = getInStorePromotionsApplied($pos_sales_invoice_id);
	$totals['pre_tax_subtotal'] = getPreTaxSubTotal($pos_sales_invoice_id);
	$totals['tax_total'] = getLocalRegularTax($pos_sales_invoice_id);
	$totals['exempt_tax_total'] = 'TBD';
	$totals['manufacturer_discounts_applied'] = getManufacturerPromotionsApplied($pos_sales_invoice_id);
	$totals['pre_tax_subtotal'] = 'TBD';
	$totals['grand_quantity'] =getSalesInvoiceNumberOfItems($pos_sales_invoice_id);
	$totals['grand_total'] = 'TBD';
	return $totals();
}

function getSalesInvoiceFullPriceTotal($pos_sales_invoice_id)
{
	//full price total is the amount of goods sold without a 'discount' or sale
	//does not include promotion.
	//
	//get the invoice contents....
	return getSingleValueSQL("SELECT coalesce(round(sum(quantity*sale_price),2),0) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id AND sale_price>=retail_price AND discount =0 AND content_type != 'CREDIT_CARD' AND quantity > 0");
	
}
function getSalesInvoiceDiscountedTotal($pos_sales_invoice_id)
{
	$sale_price = getItemPriceSQL();
	return getSingleValueSQL("SELECT coalesce(round(sum($sale_price*quantity),2),0) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id AND (sale_price<retail_price OR discount>0) AND content_type != 'CREDIT_CARD' AND  content_type != 'SHIPPING' AND quantity > 0 ");
}
function getItemPriceSQL()
{
	$sale_price = "if (content_type = 'CREDIT_CARD' OR content_type = 'SHIPPING', 
			retail_price, 
			if(discount_type='DOLLAR', 
				(sale_price-discount), 
				( sale_price - (sale_price*(discount/100)))
		)
		)";
	//$sale_price = "sale_price";
	return $sale_price;
}
function getAppliedDiscountItemPriceSQL()
{
	$sale_price = "if (content_type = 'CREDIT_CARD' OR content_type='SHIPPING', 
			retail_price, 
			if(discount_type='DOLLAR', 
				(sale_price-discount -applied_instore_discount), 
				( sale_price - applied_instore_discount - (sale_price*(discount/100)))
		)
		)";
	//$sale_price = "sale_price";
	return $sale_price;
}
function getPrePromotionSubTotal($pos_sales_invoice_id)
{
	$sale_price = getItemPriceSQL();
	return getSingleValueSQL("SELECT round(sum($sale_price*quantity),2) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id AND quantity >0");
}
function getInStorePromotionsApplied($pos_sales_invoice_id)
{
	//two ways... get it from the 'invoice' or the 'promotinos table... promotions is more correct...
	$sql1 =  "SELECT round(sum(applied_instore_discount),2) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id  AND quantity > 0";
	return getSingleValueSQL("SELECT round(coalesce(sum(applied_amount),0),2) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = $pos_sales_invoice_id
			AND promotion_type = 'Pre Tax'");
}
function getPreTaxSubTotal($pos_sales_invoice_id)
{
	$sale_price = getItemPriceSQL();
	//either one works....
	$sql1 =  "SELECT round(sum($sale_price*quantity -applied_instore_discount),2) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id ";
	$sql2 = "SELECT round(sum($sale_price*quantity) - (SELECT round(coalesce(sum(applied_amount),0),2) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = $pos_sales_invoice_id
			AND promotion_type = 'Pre Tax'),2) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql1);
}
function getInvoiceSumsSQL()
{
	//because tax is sooooooo complicated I am breaking out parts of the sql statment....
	$line_item_total_sql = "(SELECT if(discount_type='DOLLAR', sale_price-discount - applied_instore_discount, sale_price - (sale_price*(discount/100)) - applied_instore_discount))";
	$line_total_sql = $line_item_total_sql . '*quantity';
	//if the state regular and local tax rates are zero, this is a non-taxable sale, regardless of exemptions...return zero as the line total, otherwise return the taxable total
	//gift cards should not be included in any tax caluations....
	$taxable_line_total = "if(pos_store_credit_id = 0, if(state_regular_tax_rate AND local_regular_tax_rate = 0, 0, $line_item_total_sql*quantity),0)";
	$sale_price = getAppliedDiscountItemPriceSQL();
	$state_tax = stateTaxRateSQL();
	$local_tax = localTaxRateSQL();
	$tax_rate = getTaxRateSQL();
	$tax_rate_from_contents = TaxRateFromContentsSQL();
	$exempt_value_from_contents = getTaxExemptionValueFromContentsSQL();
	$exempt_value = getTaxExemptionValueSQL();
	$manufacturer_promotions = getManufacturerPromotionsAppliedSQL();
	
	$tmp_sql = "


(SELECT sum(retail_price) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
			AND content_type = 'CREDIT_CARD') as credit_cards_sold,
	(SELECT sum($sale_price*quantity) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND quantity <0) as returns,	
	(SELECT sum( $sale_price*$tax_rate/100*quantity) + sum($sale_price*quantity) - $manufacturer_promotions FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as grande_total,
	(SELECT round(sum($sale_price*quantity) ,2) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD' AND quantity>0) as total_goods_and_service,
	
	(SELECT round(sum($sale_price*quantity)  ,2) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD' ) as gross,
	
	
	
	(SELECT sum( $sale_price*$tax_rate/100*quantity)   FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as tax_collected,

(SELECT sum( $sale_price*($tax_rate_from_contents/100)*quantity)   FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as tax_collected_from_contents,

	(SELECT sum(if($sale_price <= $exempt_value, $sale_price*quantity,0))   FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD') as exempt_sales,
	
	(SELECT sum(if($sale_price <= state_exemption_value, $sale_price*quantity,0))   FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD') as exempt_sales_from_contents,
	
	(SELECT sum(if($sale_price > $exempt_value AND $tax_rate>0, $sale_price*quantity,0) )  FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD') as regular_sales,
	
	(SELECT sum(if($sale_price > state_exemption_value AND $tax_rate_from_contents>0, $sale_price*quantity,0) )  FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD') as regular_sales_from_contents,
	
	(SELECT sum( if($tax_rate=0, $sale_price*quantity,0))  FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id AND content_type != 'CREDIT_CARD') as no_tax_sales,
	(SELECT sum( $sale_price*$local_tax/100*quantity)  FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as local_regular_tax,
	(SELECT sum( $sale_price*$state_tax/100*quantity)   FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id) as state_regular_tax
	
	


	";
	return $tmp_sql;

}
function getTaxExemptionValueSQL()
{
	/*$state= "(
	SELECT state.jurisdiction_name
	FROM pos_sales_invoice_contents 
	INNER JOIN pos_sales_invoice ON pos_sales_invoice_contents.pos_sales_invoice_id = pos_sales_invoice.pos_sales_invoice_id
	INNER join pos_tax_jurisdictions as local
	ON pos_sales_invoice_contents.pos_local_tax_jurisdiction_id = local.pos_tax_jurisdiction_id
	INNER join pos_tax_jurisdictions as state
	ON local.pos_state_id = state.pos_state_id
	WHERE local.pos_tax_jurisdiction_id = pos_sales_invoice_contents.pos_local_tax_jurisdiction_id
	AND state.local_or_state = 'State'
	AND pos_sales_invoice_contents.pos_sales_invoice_id = 25
	
	)";
	
	$state_rates_for_testing = "
	SELECT 
	coalesce(
	(SELECT pos_sales_tax_rates.exemption_value
	FROM  pos_tax_jurisdictions as local_table
	INNER join pos_tax_jurisdictions as state_table
	ON local_table.pos_state_id = state_table.pos_state_id
	INNER JOIN pos_sales_tax_rates
	ON pos_sales_tax_rates.pos_tax_jurisdiction_id = state_table.pos_tax_jurisdiction_id
	WHERE state_table.local_or_state = 'State'
	AND pos_sales_tax_rates.pos_sales_tax_category_id = pos_sales_invoice_contents.pos_sales_tax_category_id
	AND (SELECT pos_sales_invoice.invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id) >= pos_sales_tax_rates.start_date
	
	ORDER BY start_date DESC 
	LIMIT 1
	),0) as exempt_value
	
	FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = 25
	
	";
	*/
	
	//something wrong here...
	$exempt_value = "
	coalesce(
	(SELECT pos_sales_tax_rates.exemption_value
	FROM  pos_tax_jurisdictions as local_table
	INNER join pos_tax_jurisdictions as state_table
	ON local_table.pos_state_id = state_table.pos_state_id
	INNER JOIN pos_sales_tax_rates
	ON pos_sales_tax_rates.pos_tax_jurisdiction_id = state_table.pos_tax_jurisdiction_id
	WHERE state_table.local_or_state = 'State'
	AND pos_sales_tax_rates.pos_sales_tax_category_id = pos_sales_invoice_contents.pos_sales_tax_category_id
	AND (SELECT pos_sales_invoice.invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id) >= pos_sales_tax_rates.start_date
	ORDER BY start_date DESC 
	LIMIT 1
	),0)
	";
	
	$exempt_value = "
	coalesce(
	(SELECT pos_sales_tax_rates.exemption_value
	FROM  pos_tax_jurisdictions 
	INNER JOIN pos_sales_tax_rates
	ON pos_sales_tax_rates.pos_tax_jurisdiction_id = pos_tax_jurisdictions.pos_tax_jurisdiction_id
	WHERE
	pos_tax_jurisdictions.pos_tax_jurisdiction_id = pos_sales_invoice_contents.pos_state_tax_jurisdiction_id
	AND pos_sales_tax_rates.pos_sales_tax_category_id = pos_sales_invoice_contents.pos_sales_tax_category_id
	AND (SELECT pos_sales_invoice.invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id) >= pos_sales_tax_rates.start_date
	ORDER BY start_date DESC 
	LIMIT 1
	),0)
	";
	
	return $exempt_value;
}
function getTaxExemptionValueFromContentsSQL()
{

	
	$exempt_value = "
	state_exemption_value
	";
	
	return $exempt_value;
}
function localTaxRateSQL()
{
	$item_price = getAppliedDiscountItemPriceSQL();
	$local_tax_rate_sql = "
	(SELECT	coalesce(
	(SELECT pos_sales_tax_rates.tax_rate
	FROM  pos_tax_jurisdictions as local_table
	INNER JOIN pos_sales_tax_rates
	ON pos_sales_tax_rates.pos_tax_jurisdiction_id = local_table.pos_tax_jurisdiction_id
	WHERE 
	 pos_sales_tax_rates.pos_sales_tax_category_id = pos_sales_invoice_contents.pos_sales_tax_category_id
	 	AND local_table.pos_tax_jurisdiction_id = pos_sales_invoice_contents.pos_local_tax_jurisdiction_id

	AND (SELECT pos_sales_invoice.invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id) >= pos_sales_tax_rates.start_date
	AND if(pos_sales_tax_rates.exemption_value>0, $item_price <= pos_sales_tax_rates.exemption_value,1)
	ORDER BY start_date DESC 
	LIMIT 1
	),0))
	";
	return $local_tax_rate_sql;
}
function localTaxRateFromContentsSQL()
{
	$item_price = getAppliedDiscountItemPriceSQL();
	$local_tax_rate_sql = "
	
		if($item_price <=  state_exemption_value, local_exemption_tax_rate, local_regular_tax_rate)
	
	";
	return $local_tax_rate_sql;
}
function stateTaxRateFromContentsSQL()
{
	$item_price = getAppliedDiscountItemPriceSQL();
	$local_tax_rate_sql = "
	
		if($item_price <=  state_exemption_value, state_exemption_tax_rate, state_regular_tax_rate)
	
	";
	return $local_tax_rate_sql;
}
function TaxRateFromContentsSQL()
{
	return  '(' . localTaxRateFromContentsSQL() . '+' . stateTaxRateFromContentsSQL() .')';
}
function stateTaxRateSQL()
{
	$item_price = getAppliedDiscountItemPriceSQL();
	$state_tax_rate_sql = 
	"
	(SELECT	coalesce(
	(SELECT pos_sales_tax_rates.tax_rate
	FROM  pos_tax_jurisdictions 
	INNER JOIN pos_sales_tax_rates
	ON pos_sales_tax_rates.pos_tax_jurisdiction_id = pos_tax_jurisdictions.pos_tax_jurisdiction_id
	WHERE 
	 pos_sales_tax_rates.pos_sales_tax_category_id = pos_sales_invoice_contents.pos_sales_tax_category_id
	 	AND pos_tax_jurisdictions.pos_tax_jurisdiction_id = pos_sales_invoice_contents.pos_state_tax_jurisdiction_id
	AND (SELECT pos_sales_invoice.invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id) >= pos_sales_tax_rates.start_date
	AND if(pos_sales_tax_rates.exemption_value>0, $item_price <= pos_sales_tax_rates.exemption_value,1)
	ORDER BY start_date DESC 
	LIMIT 1
	),0))
	";
	return $state_tax_rate_sql;
}
function getTaxRateSQL()
{
	$sale_price = getItemPriceSQL();
	$exempt_value = getTaxExemptionValueSQL();
	
	$item_price = getAppliedDiscountItemPriceSQL();
/*	$state_tax_rate_sql = "
		coalesce(
	(SELECT pos_sales_tax_rates.tax_rate
	FROM  pos_tax_jurisdictions as local_table
	INNER join pos_tax_jurisdictions as state_table
	ON local_table.pos_state_id = state_table.pos_state_id
	INNER JOIN pos_sales_tax_rates
	ON pos_sales_tax_rates.pos_tax_jurisdiction_id = state_table.pos_tax_jurisdiction_id
	WHERE state_table.local_or_state = 'State'
	AND pos_sales_tax_rates.pos_sales_tax_category_id = pos_sales_invoice_contents.pos_sales_tax_category_id
	AND (SELECT pos_sales_invoice.invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id) >= pos_sales_tax_rates.start_date
	AND if(pos_sales_tax_rates.exemption_value>0, $item_price <= pos_sales_tax_rates.exemption_value,1)
	ORDER BY start_date DESC 
	LIMIT 1
	),0)
	";*/
	//bypass the state lookup as this is stored in the contents... makes sense...
	//ids for checking
/*$local_tax_rate_id_sql = "		

	(SELECT pos_sales_tax_rates.pos_sales_tax_rate_id
	FROM  pos_tax_jurisdictions as local_table
	INNER JOIN pos_sales_tax_rates
	ON pos_sales_tax_rates.pos_tax_jurisdiction_id = local_table.pos_tax_jurisdiction_id
	WHERE 
	 pos_sales_tax_rates.pos_sales_tax_category_id = pos_sales_invoice_contents.pos_sales_tax_category_id
	AND local_table.pos_tax_jurisdiction_id = pos_sales_invoice_contents.pos_local_tax_jurisdiction_id
	AND (SELECT pos_sales_invoice.invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id) >= pos_sales_tax_rates.start_date
	AND if(pos_sales_tax_rates.exemption_value>0, $item_price <= pos_sales_tax_rates.exemption_value,1)
	ORDER BY start_date DESC 
	LIMIT 1
	)
	";	
$state_tax_rate_v2_id = "		

	(SELECT pos_sales_tax_rates.pos_sales_tax_rate_id
	FROM  pos_tax_jurisdictions
	INNER JOIN pos_sales_tax_rates
	ON pos_sales_tax_rates.pos_tax_jurisdiction_id = pos_tax_jurisdictions.pos_tax_jurisdiction_id
	WHERE 
	 pos_sales_tax_rates.pos_sales_tax_category_id = pos_sales_invoice_contents.pos_sales_tax_category_id
	AND pos_tax_jurisdictions.pos_tax_jurisdiction_id = pos_sales_invoice_contents.pos_state_tax_jurisdiction_id
	AND (SELECT pos_sales_invoice.invoice_date FROM pos_sales_invoice WHERE pos_sales_invoice.pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id) >= pos_sales_tax_rates.start_date
	AND if(pos_sales_tax_rates.exemption_value>0, $item_price <= pos_sales_tax_rates.exemption_value,1)
	ORDER BY start_date DESC 
	LIMIT 1
	)
	";		
	*/
	//$sql = "SELECT sum($exempt_value) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	//$sql = "SELECT $state_tax_rate_sql + $local_tax_rate_sql FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id";

	return  '(' . localTaxRateSQL() . '+' . stateTaxRateSQL() .')';
}
function getTaxTotal($pos_sales_invoice_id)
{
	//this should not be horrible.....
	//get the local jurisdiction id
	//look up the state
	//look up the regular tax rate and the exempt tax rate and exempt tax values
	
	$sale_price = getAppliedDiscountItemPriceSQL();
	$tax_rate = getTaxRateSQL();
	//$tax_per_line_sql = "SELECT round( $sale_price*$tax_rate/100*quantity,2) as tax  FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id ";
	//preprint(getSQL($tax_per_line_sql));
	$sql1 = "SELECT round(sum( $sale_price*$tax_rate/100*quantity),2)  FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id  ";
	return getSingleValueSQL($sql1);
}
function getSalesInvoiceTaxTotalFromContents($pos_sales_invoice_id)
{
	$sql = "SELECT round(sum(tax_total),2) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql);
	
}
function mysqlProcedure($dbc)
{
	$sql="
	DELIMITER $$
	CREATE PROCEDURE getPreTaxLineTotal(IN content_id INT,OUT total DECIMAL(20,5))
	BEGIN
		SELECT if(discount_type='DOLLAR', quantity*(sale_price-discount - applied_instore_discount), quantity*(sale_price - (sale_price*(discount/100)) - applied_instore_discount))
		INTO total
		FROM pos_sales_invoice_contents
		WHERE pos_sales_invoice_content_id = content_id;
	END$$
	DELIMITER ; 
	";
	
	//CALL CountOrderByStatus('Shipped',@total);
	//SELECT @total AS total_shipped;
	//To get number of in process we do the same as above
	//CALL CountOrderByStatus('in process',@total);
	//SELECT @total AS total_in_process; 

}
function shittySalesInvoiceSQL($pos_sales_invoice_id)
{
	$tmp_sql = "
	CREATE TEMPORARY TABLE sales_invoices_totals
	SELECT if(discount_type='DOLLAR', sale_price-discount - applied_instore_discount, sale_price - (sale_price*(discount/100)) - applied_instore_discount) as line_total, local_exemption_value, local_regular_tax_rate, local_exemption_tax_rate, state_exemption_value, state_regular_tax_rate, state_exemption_tax_rate, quantity FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id
	;";
	return $tmp_sql;
}
function getLocalRegularTax($pos_sales_invoice_id)
{

	//select the total sale amount greater than the exempt amount and multiply it by the non-exempt local tax rate
	$tmp_sql = shittySalesInvoiceSQL($pos_sales_invoice_id);
	$tmp_select_sql = "
	SELECT round(sum(if(line_total >local_exemption_value, line_total*local_regular_tax_rate*quantity/100, 0)),2) as total FROM sales_invoices_totals";
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	return $data[0]['total'];
	
}
function getLocalExemptTax($pos_sales_invoice_id)
{
	$tmp_sql = shittySalesInvoiceSQL($pos_sales_invoice_id);
	$tmp_select_sql = "
	SELECT round(sum(if(line_total <= local_exemption_value, line_total*local_exemption_tax_rate*quantity/100, 0)),2) as total FROM sales_invoices_totals";
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	return $data[0]['total'];
}
function getStateRegularTax($pos_sales_invoice_id)
{

	//select the total sale amount greater than the exempt amount and multiply it by the non-exempt local tax rate
	$tmp_sql = shittySalesInvoiceSQL($pos_sales_invoice_id);
	$tmp_select_sql = "
	SELECT round(sum(if(line_total >state_exemption_value, line_total*state_regular_tax_rate*quantity/100, 0)),2) as total FROM sales_invoices_totals";
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	return $data[0]['total'];
	
}
function getStateExemptTax($pos_sales_invoice_id)
{
	$tmp_sql = shittySalesInvoiceSQL($pos_sales_invoice_id);
	$tmp_select_sql = "
	SELECT round(sum(if(line_total <= state_exemption_value, line_total*state_exemption_tax_rate*quantity/100, 0)),2) as total FROM sales_invoices_totals";
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	return $data[0]['total'];
}
function getManufacturerPromotionsAppliedSQL()
{	
	$sql = "(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
			AND promotion_type = 'Post Tax')";
	return $sql;
}
function getManufacturerPromotionsApplied($pos_sales_invoice_id)
{
	//two ways... get it from the 'invoice' or the 'promotinos table... promotions is more correct...
	return getSingleValueSQL("SELECT round(coalesce(sum(applied_amount),0),2) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = $pos_sales_invoice_id
			AND promotion_type = 'Post Tax'");
}
function getSalesInvoiceNumberOfItems($pos_sales_invoice_id)
{
	return getSingleValueSQL("SELECT sum(quantity) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id AND quantity>0 and content_type != 'SHIPPING'");
}
function getSalesInvoiceReturns($pos_sales_invoice_id)
{
	$sale_price = getItemPriceSQL();
	return getSingleValueSQL("SELECT round(sum($sale_price*quantity),2) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id AND quantity <0");
}

function getCreditCardsSoldSQL()
{
	$sql = "(SELECT sum(retail_price) FROM pos_sales_invoice_contents
			WHERE pos_sales_invoice_contents.pos_sales_invoice_id
			AND content_type = 'CREDIT_CARD')";
	return $sql;
}


function getCustomerPaymentMethodName($pos_customer_payment_method_id)
{
	$sql = "SELECT payment_type FROM pos_customer_payment_methods WHERE pos_customer_payment_method_id = $pos_customer_payment_method_id";
	return getSingleValueSQL($sql);
}
function getCustomerPaymentCreditCards()
{
	$sql = "SELECT payment_type FROM pos_customer_payment_methods WHERE payment_group = 'CREDIT_CARD'";
	return getSingleValueSQL($sql);
}
function getSalesInvoiceShippingAmount($pos_sales_invoice_id)
{
	$sql = "SELECT round(shipping_amount,2) FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql);

}
function getSalesInvoiceAddress($pos_sales_invoice_id)
{
	$sql = "SELECT pos_address_id FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql);

}
function getSalesInvoiceStore($pos_sales_invoice_id)
{
	$sql = "SELECT pos_store_id FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql);

}
function calculateSalePrice($sales_invoice_content_array)
{
	
	if($sales_invoice_content_array['content_type'] == 'CREDIT_CARD')
	{
		$line_total = $sales_invoice_content_array['retail_price'];
	}
	else if($sales_invoice_content_array['content_type'] == 'SHIPPING')
	{
		$line_total = $sales_invoice_content_array['retail_price'];
	}
	else
	{
		$line_total = $sales_invoice_content_array['sale_price'];
	}
	$line_total = ($line_total - $sales_invoice_content_array['applied_instore_discount'])*$sales_invoice_content_array['quantity'];
	if($sales_invoice_content_array['discount_type'] = 'DOLLAR')
	{
		$discount = $sales_invoice_content_array['discount'];
	}
	else
	{
		$discount = $line_total*($sales_invoice_content_array['discount']/100);
	}
	$line_total = $line_total - $discount;
	return $line_total;
}

function createDiscountCodeSelect($name, $pos_discount_id, $option_all = 'off', $select_events ='')
{
	$discounts = getDiscountCodes();

	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select...</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_discount_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All</option>';
	}
	for($i = 0;$i < sizeof($discounts['pos_discount_id']); $i++)
	{
		$html .= '<option value="' . $discounts['pos_discount_id'][$i] . '"';
		if ( ($discounts['pos_discount_id'][$i] == $pos_discount_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $discounts['discount_name'][$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}


function getSalesInvoicePostTaxPromotions($pos_sales_invoice_id)
{
	$promotion_data = getSQL("SELECT pos_sales_invoice_promotions.pos_promotion_id, pos_sales_invoice_promotions.applied_amount, promotion_code, promotion_name, promotion_type, promotion_amount, expired_value, date(expiration_date) as expiration_date, percent_or_dollars, qualifying_amount FROM pos_sales_invoice_promotions
								LEFT JOIN pos_promotions USING(pos_promotion_id)
								WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = $pos_sales_invoice_id AND promotion_type = 'Post Tax'");
	return $promotion_data;
}
function getSalesInvoicePreTaxPromotionsTotal($pos_sales_invoice_id)
{
	$promotion_data = getSingleValueSQL("SELECT sum(applied_amount) FROM pos_sales_invoice_promotions
								LEFT JOIN pos_promotions USING(pos_promotion_id)
								WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = $pos_sales_invoice_id AND promotion_type = 'Pre Tax'");
	return $promotion_data;
}
function getSalesInvoicePostTaxPromotionsTotal($pos_sales_invoice_id)
{
	$promotion_data = getSingleValueSQL("SELECT sum(applied_amount) FROM pos_sales_invoice_promotions
								LEFT JOIN pos_promotions USING(pos_promotion_id)
								WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = $pos_sales_invoice_id AND promotion_type = 'Post Tax'");
	return $promotion_data;
}
function getSalesInvoicePreTaxPromotions($pos_sales_invoice_id)
{
	$promotion_data = getSQL("SELECT pos_sales_invoice_promotions.pos_promotion_id, pos_sales_invoice_promotions.applied_amount, promotion_code, promotion_name, promotion_type, promotion_amount, expired_value, date(expiration_date) as expiration_date, percent_or_dollars, qualifying_amount FROM pos_sales_invoice_promotions
								LEFT JOIN pos_promotions USING(pos_promotion_id)
								WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = $pos_sales_invoice_id AND promotion_type = 'Pre Tax'");
	return $promotion_data;
}
function getSalesInvoicePromotions($pos_sales_invoice_id)
{
	$promotion_data = getSQL("SELECT pos_sales_invoice_promotions.pos_promotion_id, pos_sales_invoice_promotions.applied_amount, promotion_code, promotion_name, promotion_type, promotion_amount, expired_value, date(expiration_date) as expiration_date, percent_or_dollars, qualifying_amount FROM pos_sales_invoice_promotions
								LEFT JOIN pos_promotions USING(pos_promotion_id)
								WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = $pos_sales_invoice_id");
	return $promotion_data;
}


function generatUniqueCardNumber($length, $charset = '0123456789')
{

	$charset = "ABCDEFGHJKMNPQRSTUVWXY3456789";
	$charset = "0123456789";
	//these are pretty unique-looking characthers that should minimize confusion.
	$charset = "ABCDEFGHJKMNPRSTWXY345678";
	$charset = "0123456789";
	$key = '';
	for($i=0; $i<$length; $i++)
	{
	 $key .= $charset[(mt_rand(0,(strlen($charset)-1)))]; 
	}
	return $key;

}
function getSalesInvoiceGrandeTotal($pos_sales_invoice_id)
{
	//the tax is already stored, so simply add up the items, add the tax, remove the two discounts...
	
	/*$sale_price = getItemPriceSQL();
	$grand_sql = "SELECT round(
	sum(if(discount_type='DOLLAR', quantity*(sale_price-discount - applied_instore_discount), quantity*(sale_price - (sale_price*(discount/100)) - applied_instore_discount)))
	-
	(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = $pos_sales_invoice_id
			AND promotion_type = 'Post Tax')
	+
	sum(tax_total)
	
	,2) 
	
	FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	*/
	$sale_price = getAppliedDiscountItemPriceSQL();
	$tax_rate = getTaxRateSQL();
	$manufacturer_promotions = getManufacturerPromotionsAppliedSQL();
	//$sql1 = "SELECT $sale_price*$tax_rate/100*quantity as tax, $sale_price*quantity as price  FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id  ";
	//preprint(getSQL($sql1));
	//preprint($sql1);
	$sql1 = "SELECT round(sum( $sale_price*$tax_rate/100*quantity) + sum($sale_price*quantity) - $manufacturer_promotions ,2) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id  ";
	
	return getSingleValueSql($sql1);
	
}
function getSalesInvoiceGrandeTotalFromContents($pos_sales_invoice_id)
{
	$invoice_total = getSingleValueSQL("
		SELECT sum(
		
			CASE content_type 
		
			WHEN ('PRODUCT') THEN
			quantity*(if(discount_type='DOLLAR',sale_price-discount,sale_price -sale_price*discount/100))-applied_instore_discount
			WHEN ('SERVICE') THEN
			quantity*(if(discount_type='DOLLAR',sale_price-discount,sale_price -sale_price*discount/100))-applied_instore_discount
			WHEN ('CREDIT_CARD') THEN
			quantity*(retail_price)
			
			WHEN ('SHIPPING') THEN
			quantity*(retail_price)
			
			ELSE
			quantity*(if(discount_type='DOLLAR',sale_price-discount,sale_price -sale_price*discount/100))-applied_instore_discount
			END
			+ tax_total)
			
		 - 
		
			(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
			AND promotion_type = 'Post Tax') 
		FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = $pos_sales_invoice_id");
	return $invoice_total;
}
//use this one to get the total...
function getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id)
{
	//take the total of the line extensions and subtract any post tax promotions.. .like groupon
	
	$invoice_total = getSingleValueSQL("
		SELECT sum(extension)
			
		 - 
		
			(SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_promotions 
			LEFT JOIN pos_promotions USING (pos_promotion_id)
			WHERE pos_sales_invoice_id = pos_sales_invoice_contents.pos_sales_invoice_id
			AND promotion_type = 'Post Tax') 
		FROM pos_sales_invoice_contents WHERE pos_sales_invoice_contents.pos_sales_invoice_id = $pos_sales_invoice_id");
	return $invoice_total;
}
function calculateSalesContentLineExtension($pos_sales_invoice_content_id)
{
		$sql = "
		SELECT 
		
			CASE content_type 
		
			WHEN ('PRODUCT') THEN
			quantity*(if(discount_type='DOLLAR',sale_price-discount,sale_price -sale_price*discount/100))-applied_instore_discount
			WHEN ('SERVICE') THEN
			quantity*(if(discount_type='DOLLAR',sale_price-discount,sale_price -sale_price*discount/100))-applied_instore_discount
			WHEN ('CREDIT_CARD') THEN
			quantity*(retail_price)
			
			WHEN ('SHIPPING') THEN
			quantity*(retail_price)
			
			ELSE
			quantity*(if(discount_type='DOLLAR',sale_price-discount,sale_price -sale_price*discount/100))-applied_instore_discount
			END
			+ tax_total
	
		FROM pos_sales_invoice_contents WHERE pos_sales_invoice_content_id = $pos_sales_invoice_content_id";
		return getSingleValueSQL($sql);
}
function getSalesInvoiceTotalPaid($pos_sales_invoice_id)
{
	$total_paid = getSingleValueSQL("
		SELECT sum(pos_sales_invoice_to_payment.applied_amount) FROM pos_sales_invoice_to_payment 
		LEFT JOIN pos_customer_payments ON pos_sales_invoice_to_payment.pos_customer_payment_id = pos_customer_payments.pos_customer_payment_id 
		WHERE pos_sales_invoice_to_payment.pos_sales_invoice_id = $pos_sales_invoice_id");
	return $total_paid;
}
function getAPILoginID($pos_payment_gateway_id)
{
	$api_login = craigsdecryption(getSingleValueSQL("SELECT login_id FROM pos_payment_gateways WHERE pos_payment_gateway_id = $pos_payment_gateway_id"));
	return $api_login;
}
function getTrasactionKey($pos_payment_gateway_id)
{
	$transaction_key = craigsdecryption(getSingleValueSQL("SELECT transaction_key FROM pos_payment_gateways WHERE pos_payment_gateway_id = $pos_payment_gateway_id"));
	return $transaction_key;
}
function auth_net_batch()
{
	require_once(AUTHORIZE_NET_LIBRARY);
	echo 'Starting batch recovery....' .newline();
	//for each auth.net online account
	//find the transactions with no batch id  
	$payment_gateways = getSQL("SELECT pos_payment_gateway_id FROM pos_payment_gateways where line='online' AND active = '1' AND gateway_provider = 'Authorize.net'");
	for($gw=0;$gw<sizeof($payment_gateways);$gw++)
	{
		echo "Gateway: " .$payment_gateways[$gw]['pos_payment_gateway_id'] . newline();
		$pos_payment_gateway_id = $payment_gateways[$gw]['pos_payment_gateway_id'];
		$request = new AuthorizeNetTD;
		$api_login = getAPILoginID($pos_payment_gateway_id);
		$transaction_key = getTrasactionKey($pos_payment_gateway_id);
			
		$batch_sql = "SELECT pos_customer_payment_id, transaction_id FROM pos_customer_payments WHERE batch_id = '' AND transaction_id != '' AND pos_payment_gateway_id = $pos_payment_gateway_id";
		$data = getSQL($batch_sql);
		for($i=0;$i<sizeof($data);$i++)
		{
			$transactionId = $data[$i]['transaction_id'];
			$response = $request->getTransactionDetails($transactionId);
			echo $response->xml->transaction->transactionStatus;
			$pos_customer_payment_id = $data[$i]['pos_customer_payment_id'];
			$batch_id = '';
			$insert = "UPDATE pos_customer_payments SET batch_id = '$batch_id', transaction_status = 'SETTLED' WHERE pos_customer_payment_id = '$pos_cusomter_payment_id'";
			runSQL($insert);
		}

	}
	echo 'Done with Batch....' .newline();
}
?>