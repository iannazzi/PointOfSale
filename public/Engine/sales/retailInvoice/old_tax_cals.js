///old shizzzz
function calculateSalesTax(in_store_discount,tax_calculation_method)
{
	
	//three ways to apply the tax calculation: minimum, average, maximum
	//we might charge a customer based on the maximum or average, but we will always process to minimize tax.
	//beacuse of difficulties with taxes we are 
	
	in_store_discount_remaining = in_store_discount;
	var rowCount = invoice_table_object.rowCount;
	//zero out the discounts:
	for(row=0; row<rowCount; row++)
	{	
		quantity = parseInt(invoice_table_object.table_data_object['quantity'][row]);		
		for(qty=0;qty<quantity;qty++)
		{
			invoice_table_object.table_data_object['applied_instore_discount'][row]['array_values'][qty] = 0;
			
		}
	}
	if(tax_calculation_method == 'minimum')
	{
		in_store_discount_remaining = applyInstoreDiscountToExemptItems(in_store_discount_remaining,tax_calculation_method);
		recalculateTaxRates();
		in_store_discount_remaining =  applyInstoreDiscountToTaxBracket(in_store_discount_remaining,tax_calculation_method);
		in_store_discount_remaining = applyInstoreDiscountTopDown(in_store_discount_remaining);
		//want to reduce the exemptable items first to lower the tax bracket
		//next reduce the higher tax braket items
		//finally got top down and reduce whatever is left
	}
	else if(tax_calculation_method == 'average')
	{
		//easy - recursively distribute the instore discount across the items
		applyAverageInsotreDiscount(in_store_discount_remaining);
		recalculateTaxRates();
		
	}
	//removing maximum tax option
	/*
	else if(tax_calculation_method == 'maximum')
	{
		//first reduce the tax exempt items
		//second reduce the lower tax bracket items
		//finally reduce the higher tax bracket items
		in_store_discount_remaining = applyInstoreDiscountToExemptItems(in_store_discount_remaining,tax_calculation_method);
		recalculateTaxRates();
		in_store_discount_remaining =  applyInstoreDiscountToTaxBracket(in_store_discount_remaining,tax_calculation_method);
		in_store_discount_remaining = applyInstoreDiscountTopDown(in_store_discount_remaining);
	}*/


	//calculate the total applied instore discount
	var total_instore_discount_applied = 0;
	for(row=0; row<rowCount; row++)
	{
		total_instore_discount_applied = 0;
		quantity = parseInt(invoice_table_object.table_data_object['quantity'][row]);
		for(qty=0;qty<quantity;qty++)
		{	
			total_instore_discount_applied = total_instore_discount_applied + invoice_table_object.table_data_object['applied_instore_discount'][row]['array_values'][qty];
		}
invoice_table_object.table_data_object['applied_instore_discount'][row]['display_value'] = total_instore_discount_applied;		
		
	}
	//calculate the taxable_total
	var total_taxable_total = 0;
	for(row=0; row<rowCount; row++)
	{
		if(!isGiftCard(row))
		{
			total_taxable_total = 0;
			quantity = myParseInt(invoice_table_object.table_data_object['quantity'][row]);
			for(qty=0;qty<quantity;qty++)
			{
				currently_applied_discount = invoice_table_object.table_data_object['applied_instore_discount'][row]['array_values'][qty];
				sale_price  = getFinalPrice(row) - currently_applied_discount;
				invoice_table_object.table_data_object['taxable_total'][row]['array_values'][qty] = sale_price;
				
				total_taxable_total = total_taxable_total + sale_price;
			}
			invoice_table_object.table_data_object['taxable_total'][row]['display_value'] = total_taxable_total;
		}
		else
		{
			invoice_table_object.table_data_object['taxable_total'][row]['array_values'][0] = 0;
			invoice_table_object.table_data_object['taxable_total'][row]['display_value'] = 0;
		}
	}
	
	//caluclate the total tax per line
	var tax_total = 0;
	var line_tax;
	for(row=0; row<rowCount; row++)
	{
		tax_total = 0;
		quantity = myParseInt(invoice_table_object.table_data_object['quantity'][row]);

		for(qty=0;qty<quantity;qty++)
		{
			line_tax = invoice_table_object.table_data_object['taxable_total'][row]['array_values'][qty]*
			invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty]/100;
			invoice_table_object.table_data_object['tax_total'][row]['array_values'][qty] = line_tax;
			tax_total = tax_total + line_tax;
		}
		invoice_table_object.table_data_object['tax_total'][row]['display_value'] = tax_total;
	
	}
	
	
	//finally finally create the tax rates
	for(row=0; row<rowCount; row++)
	{
		quantity = myParseInt(invoice_table_object.table_data_object['quantity'][row]);
		if(quantity == 0)
		{
			invoice_table_object.table_data_object['tax_rate'][row]['display_value'] = "0";
		}
		else
		{
			var average_tax_rate = 0;
			var different_tax_rates = false;
			var tmp_tax_rate = invoice_table_object.table_data_object['tax_rate'][row]['array_values'][0];
			for(qty=0;qty<quantity;qty++)
			{
				average_tax_rate = average_tax_rate + invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty];
				if(tmp_tax_rate != invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty])
				{
					different_tax_rates = true;
				}
				tmp_tax_rate = invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty];
			}
	
			if (different_tax_rates)
			{
				invoice_table_object.table_data_object['tax_rate'][row]['display_value'] = "MIX";
			}
			else
			{
				invoice_table_object.table_data_object['tax_rate'][row]['display_value'] = round2(average_tax_rate/quantity,2);
			}
		}
	}
	
}

function applyInstoreDiscountToExemptItems(in_store_discount_remaining,tax_calculation_method )
{
	//this loop should tell me the best order to apply the exemption to minimize tax
	exemption_array = [];
	tmp_in_store_discount_remaining = in_store_discount_remaining;
	rowCount = invoice_table_object.rowCount;
	var exemption_value;
	//apply the exemption to each row
	// the row with the max exemption is the row to start with when applying....
	
	for(row=0; row<rowCount; row++)
	{
		exemption_array[row] = 0;
		tmp_in_store_discount_remaining = in_store_discount_remaining;
		quantity = parseInt(invoice_table_object.table_data_object['quantity'][row]);
		
		
		//who is exempt - this is wrong.....
		sale_price =  getFinalPrice(row);
		
		exemption_value = getExemptionValue(row);
		//all items are essentially comparing to the exemption value, in some cases that value will be 0
		for(qty=0;qty<quantity;qty++)
		{
			if(tax_calculation_method =='minimum')
			{
				if(sale_price > exemption_value && tmp_in_store_discount_remaining > 0)
				{
					if(sale_price - tmp_in_store_discount_remaining > exemption_value)
					{
						//can't get the tax lower by applying the discount - do nothing
					}
					else
					{
						//reduce the price....to the exemption value
						
						discount_to_apply = sale_price - exemption_value;
						tmp_in_store_discount_remaining = tmp_in_store_discount_remaining - discount_to_apply;
						exemption_array[row] = exemption_array[row] +1;
					}
				}
			}
			/*else if (tax_calculation_method =='maximum')
			{
				//for maximum tax we would first reduce the exempt items to zero
				// then we would go back and reduce the no exempt items to just above the exempt price.
				//seeing that we are never going to use this code I am not going to correctly implement it...
				
				if(sale_price < exemption_value && tmp_in_store_discount_remaining > 0)
				{
					if(sale_price - tmp_in_store_discount_remaining > 0)
					{
						discount_to_apply = tmp_in_store_discount_remaining;
						tmp_in_store_discount_remaining = tmp_in_store_discount_remaining - discount_to_apply;
					}
					else
					{
						//reduce the price to zero
						
						discount_to_apply = sale_price;
						tmp_in_store_discount_remaining = tmp_in_store_discount_remaining - discount_to_apply;
					}
					exemption_array[row] = exemption_array[row] +1;
				}
			}*/
		//}
	}
	}
	starting_row = 0;
	max_exemption_value = 0;
	for(row=0;row<exemption_array.length;row++)
	{
		if(exemption_array[row] > max_exemption_value)
		{
			max_exemption_value = exemption_array[row];
			starting_row = row;
		}
	}
	special_counter = starting_row;
	
	//go through item by item, try to reduce exempt items to the exempt value first to the exempt value.
	for(row=0; row<rowCount; row++)
	{
		quantity = parseInt(invoice_table_object.table_data_object['quantity'][special_counter]);
		//who is exempt
		
		//who is exempt - this is wrong.....
		sale_price =  getFinalPrice(special_counter);
		exemption_value = getExemptionValue(special_counter);
		
		//if(invoice_table_object.table_data_object['tax_type'][special_counter] == 'Exempt')
		//if(sale_price <= exemption_value)
		//{
			for(qty=0;qty<quantity;qty++)
			{
				if(tax_calculation_method =='minimum')
				{
					if(sale_price > exemption_value && in_store_discount_remaining > 0)
					{
						//console.log(sale_price + ' ' + exemption_value + ' ' + in_store_discount_remaining);
						if(sale_price - in_store_discount_remaining > exemption_value)
						{
							//can't get the tax lower by applying the discount - do nothing
						}
						else
						{
							//reduce the price....to the exemption value
							
							discount_to_apply = sale_price - exemption_value;
							in_store_discount_remaining = in_store_discount_remaining - discount_to_apply;
							curently_applied_discount = invoice_table_object.table_data_object['applied_instore_discount'][special_counter]['array_values'][qty];

invoice_table_object.table_data_object['applied_instore_discount'][special_counter]['array_values'][qty] = curently_applied_discount +discount_to_apply; 
						}
					}
				}
				else if (tax_calculation_method =='maximum')
				{
					if(sale_price < exemption_value && in_store_discount_remaining > 0)
					{
						if(sale_price - in_store_discount_remaining > 0)
						{
							discount_to_apply = in_store_discount_remaining;
							in_store_discount_remaining = in_store_discount_remaining - discount_to_apply;
						}
						else
						{
							//reduce the price to zero
							
							discount_to_apply = sale_price;
							in_store_discount_remaining = in_store_discount_remaining - discount_to_apply;
						}
						curently_applied_discount = invoice_table_object.table_data_object['applied_instore_discount'][special_counter]['array_values'][qty];
						
						invoice_table_object.table_data_object['applied_instore_discount'][special_counter]['array_values'][qty] = curently_applied_discount +discount_to_apply; 
					}
				} 
			//}
		}
		special_counter++;
		if(special_counter==rowCount)
		{
			special_counter = 0;
		}
	}
	return in_store_discount_remaining;
}
function applyInstoreDiscountToTaxBracket(in_store_discount_remaining,tax_calculation_method)
{
	rowCount = invoice_table_object.rowCount;
	//next reduce the higher tax bracket items
	var max_tax_rate = 0;
	var min_tax_rate = invoice_table_object.table_data_object['tax_rate'][0]['array_values'][0];
	for(row=0; row<rowCount; row++)
	{
		quantity = invoice_table_object.table_data_object['quantity'][row];
		for(qty=0;qty<quantity;qty++)
		{
			//find the maximum tax rate
			if(max_tax_rate< invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty])
			{
				max_tax_rate = invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty];
			}
			if(min_tax_rate > invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty])
			{
				min_tax_rate = invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty];
			}
		}
	}
	if(tax_calculation_method == 'minimum')
	{
		test_tax_rate = max_tax_rate;
	}
	else if(tax_calculation_method == 'maximum')
	{
		test_tax_rate = min_tax_rate;
	}
	for(row=0; row<rowCount; row++)
	{
		quantity = parseInt(invoice_table_object.table_data_object['quantity'][row]);
		for(qty=0;qty<quantity;qty++)
		{
			tax_rate = invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty];
			if(tax_rate == test_tax_rate && in_store_discount_remaining > 0)
			{
				//reduce the price....
				current_applied_discount = invoice_table_object.table_data_object['applied_instore_discount'][row]['array_values'][qty];
				price_to_reduce  = getFinalPrice(row) - current_applied_discount;
				if (price_to_reduce - in_store_discount_remaining  < 0)
				{
					discount_to_apply = price_to_reduce;
				}
				else
				{
					discount_to_apply = in_store_discount_remaining;
				}
				in_store_discount_remaining = in_store_discount_remaining - discount_to_apply;

invoice_table_object.table_data_object['applied_instore_discount'][row]['array_values'][qty] = current_applied_discount +discount_to_apply;	
	
				} 
			}
	}
	return in_store_discount_remaining;
}
function applyInstoreDiscountTopDown(instore_discount_remaining)
{
		rowCount = invoice_table_object.rowCount;

	//finally take the remaining reductions top down
	for(row=0; row<rowCount; row++)
	{
		quantity = invoice_table_object.table_data_object['quantity'][row];
		for(qty=0;qty<quantity;qty++)
		{
			if(in_store_discount_remaining > 0)
			{
				currently_applied_discount = invoice_table_object.table_data_object['applied_instore_discount'][row]['array_values'][qty];
				
				sale_price  = getFinalPrice(row) - currently_applied_discount;
				if (sale_price - in_store_discount_remaining  < 0)
				{
					discount_to_apply = sale_price;
				}
				else
				{
					discount_to_apply = in_store_discount_remaining;
				}
				in_store_discount_remaining = in_store_discount_remaining - discount_to_apply;
//invoice_table_object.table_data_array[row][invoice_table_object.getTableDataColumnNumberFromTableDefColumnName('applied_instore_discount')]['array_values'][qty] = currently_applied_discount +discount_to_apply;
invoice_table_object.table_data_object['applied_instore_discount'][row]['array_values'][qty] = currently_applied_discount +discount_to_apply;
			}
			else
			{
			}
		}
	}
	return in_store_discount_remaining;
}
function applyAverageInsotreDiscount(instore_discount_remaining)
{
	rowCount = invoice_table_object.rowCount;
	//get the total quantity
	var total_quantity = 0;
	var total_sale_price = 0;
	for(row=0; row<rowCount; row++)
	{
		if(!isGiftCard(row))
		{
			total_quantity = total_quantity +parseInt(invoice_table_object.table_data_object['quantity'][row]); 
		}
	}
	var instore_average_discount = instore_discount_remaining/total_quantity;
	//try to apply the average. do not go below zero. Whatever is left will be applied top down or recursively
	instore_apply_array = [];
	instore_counter = 0;
	qty_remaining_can_ba_applied = 0;
	tmp_discount_remaining = instore_discount_remaining;
	//initialize the application array
	for(row=0; row<rowCount; row++)
	{
		if(!isGiftCard(row))
		{
			quantity = parseInt(invoice_table_object.table_data_object['quantity'][row]);
		for(qty=0;qty<quantity;qty++)
		{
			sale_price  = getFinalPrice(row);
			if(instore_average_discount > sale_price)
			{
				//this is the problem....
				instore_apply_array[instore_counter] = sale_price;
				tmp_discount_remaining = tmp_discount_remaining - sale_price;
			}
			else
			{
				instore_apply_array[instore_counter] = instore_average_discount;
				tmp_discount_remaining = tmp_discount_remaining - instore_average_discount;
				qty_remaining_can_ba_applied++;
			}
			instore_counter++;
		}
		}
	}
	if(qty_remaining_can_ba_applied >0)
	{
		remaining_discount_average = tmp_discount_remaining/qty_remaining_can_ba_applied;
	}
	//now recursively apply the average discount
	//tmp_discount_remaining = instore_discount_remaining;
	for(test_row = 0;test_row<rowCount; test_row++)
	{
		qty_remaining_can_ba_applied = 0;
		instore_counter = 0;
		for(row=0; row<rowCount; row++)
		{
			if(!isGiftCard(row))
			{
			quantity = parseInt(invoice_table_object.table_data_object['quantity'][row]);
			for(qty=0;qty<quantity;qty++)
			{
				currently_applied_discount = instore_apply_array[instore_counter];
				sale_price  = getFinalPrice(row) - currently_applied_discount;
				if(tmp_discount_remaining >0)
				{
					if(sale_price>0)
					{
						if(remaining_discount_average > sale_price)
						{
							//this is the problem....
							instore_apply_array[instore_counter] = getFinalPrice(row);
							tmp_discount_remaining = tmp_discount_remaining - sale_price;
						}
						else
						{
							instore_apply_array[instore_counter] = instore_apply_array[instore_counter]+remaining_discount_average;
							tmp_discount_remaining = tmp_discount_remaining - remaining_discount_average;
							qty_remaining_can_ba_applied++;
						}
					}
				}
				instore_counter++;

			}
			}
		}
		if(qty_remaining_can_ba_applied >0)
		{
			remaining_discount_average = tmp_discount_remaining/qty_remaining_can_ba_applied;
		}
	}
	
	
	//now apply the discount
	instore_counter = 0;
	for(row=0; row<rowCount; row++)
	{
		if(!isGiftCard(row))
		{
			quantity = parseInt(invoice_table_object.table_data_object['quantity'][row]);
			for(qty=0;qty<quantity;qty++)
			{

invoice_table_object.table_data_object['applied_instore_discount'][row]['array_values'][qty] = instore_apply_array[instore_counter];
			instore_counter++;
		}
		}
	}
	return tmp_discount_remaining;
}
function recalculateTaxRates()
{
	//we are going to re-cacluate the tax rates in the table_array
	var rowCount = invoice_table_object.rowCount;

	for(var row=0; row<rowCount; row++)
	{
		if(!isGiftCard(row))
		{
			quantity = myParseInt(invoice_table_object.table_data_object['quantity'][row]);
			for(var qty=0;qty<quantity;qty++)
			{
				//the applied discount is coming in wrong here.... 
				sale_price = getFinalPrice(row) - 
				invoice_table_object.table_data_object['applied_instore_discount'][row]['array_values'][qty];
				//first local
			
				local_exemption_value = myParseFloat(invoice_table_object.table_data_object['local_exemption_value'][row]);
				if(sale_price > local_exemption_value)
				{
					local_tax_rate = invoice_table_object.table_data_object['local_regular_tax_rate'][row];
					item_tax_type = 'Regular';
				}
				else
				{
					local_tax_rate = invoice_table_object.table_data_object['local_exemption_tax_rate'][row];			
					item_tax_type = 'Exempt';					
				}
				state_exemption_value = invoice_table_object.table_data_object['state_exemption_value'][row];
				if(sale_price > state_exemption_value)
				{
					state_tax_rate = invoice_table_object.table_data_object['state_regular_tax_rate'][row];					
				}
				else
				{
					state_tax_rate = invoice_table_object.table_data_object['state_exemption_tax_rate'][row];	
					item_tax_type = 'Exempt';					
				}
				
				invoice_table_object.table_data_object['tax_rate'][row]['array_values'][qty] = parseFloat(state_tax_rate) + parseFloat(local_tax_rate);
				//invoice_table_object.table_data_object['item_tax_type'][row]['array_values'][qty] = item_tax_type;
			
			}
		}	
		else
		{
		}	
	}
}