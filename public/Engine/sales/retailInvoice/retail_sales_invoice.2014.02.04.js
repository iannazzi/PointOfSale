//window.onload=init_sales_invoice();
function init_sales_invoice()
{
	//alert('init yeahhhhh');
	
	calculateTotals();
	disableColumns();
}
function enablePaidCheck(control)
{
	row = getCurrentRow(control);
	column = invoice_table_object.getHTMLColumnNumberFromTableDefColumnName('paid');
	invoice_table_object.tbody.rows[row].cells[column].childNodes[0].disabled = false;
}
function updateShipping(control)
{
}
function updateDiscount(control)
{
	//this is where we might want to load some info on the discount...
	//unlock the discount row
	row = getCurrentRow(control);
	column = invoice_table_object.getHTMLColumnNumberFromTableDefColumnName('discount');
	invoice_table_object.tbody.rows[row].cells[column].childNodes[0].readOnly = false;
	invoice_table_object.tbody.rows[row].cells[column].childNodes[0].className = 'nothing';
}
function getPromotionCode(control, control_event)
{
	if (control_event.keyCode == 13)
    {
    	loadPromotionCode(control);
    }
}
function processPromotionCode(parsed_data)
{
	//check if the code is valid
	if(compareTwoDates(invoice_date, parsed_data['expiration_date']) > 0
		&& parsed_data['expired_value'] == 0)
	{
		//promotion is expired and the expired value is nothing, do not add
		alert('Expired with No Value');
	}
	else
	{
		if(compareTwoDates(invoice_date, parsed_data['expiration_date']) > 0
		&& parsed_data['expired_value'] > 0)
		{
			alert('Promotion value has expired, however an expired value can be applied');
		}
		promotion_table_object.addItemToTable(parsed_data);
		calculateTotals();
	}
}
function loadPromotionCode(control)
{
	
	var post_string = {};
	post_string['promotion_code'] = trim(control.value);
	post_string['invoice_date'] = invoice_date;//document.getElementById('invoice_date').value;
	var url = 'ajax_promotion_code.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log(response);
				var parsed_data = parseJSONdata(response);
				//console.log(parsed_data);
				//now we need to send this response out for processing...
				if (typeof parsed_data['error'] !== "undefined")
				{
					PlaySoundV3(ERROR_BEEP_FILENAME);
					alert (parsed_data['error']);
				}
				else
				{
					PlaySoundV3(SUCCESS_BEEP_FILENAME);
					//parse it here:
					
					//console.log(parsed_data);
					//store_credit_table_object.addItemDataToTableArray(parsed_data);
					//store_credit_table_object.addItemDataToHTMLTable(parsed_data);
						//check if the code is valid
					if(compareTwoDates(invoice_date, parsed_data['expiration_date']) > 0
						&& parsed_data['expired_value'] == 0)
					{
						//promotion is expired and the expired value is nothing, do not add
						alert('Expired with No Value');
					}
					else
					{
						if(compareTwoDates(invoice_date, parsed_data['expiration_date']) > 0
						&& parsed_data['expired_value'] > 0)
						{
							alert('Promotion value has expired, however an expired value can be applied');
						}
						promotion_table_object.updateItemDataInTableObject(parsed_data,getCurrentRow(control));
						calculateTotals();
					}
					
					
				}
			}
			});
    
}
//send this to the payments code
function loadStoreCreditInfo(control)
{
	
	if (window.event.keyCode == 13)
    {
    	var post_string = {};
		post_string['card_number'] = trim(control.value);
		//post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
		var url = 'store_credit_ajax.php';
		$.ajax({
	 			type: 'POST',
	  			url: url,
	  			data: post_string,
	 			async: true,
	  			success: 	function(response) 
	  			{
	  				alert(response);
	  				//now we need to send this response out for processing...
	  				if (response == "No Data Found For Barcode")
	  				{
	  					PlaySoundV3(ERROR_BEEP_FILENAME);
	  					alert ('No Data Found for ' + control.value);
	  				}
	  				else
	  				{
	  					PlaySoundV3(SUCCESS_BEEP_FILENAME);
    					//parse it here:
    					var parsed_data = parseJSONdata(response);
    					//console.log(parsed_data);
    					//store_credit_table_object.addItemDataToTableArray(parsed_data);
    					//store_credit_table_object.addItemDataToHTMLTable(parsed_data);
    					
    					store_credit_table_object.updateItemDataInTableArray(parsed_data,getCurrentRow(control));
    					
    					calculateTotals();
	  				}
	  			}
				});
    }
}
function lookUpBarcodeID(control, control_event)
{
	//alert(control_event.keyCode);
	if (control_event.keyCode == 13)
	{
		//this part is pretty idepentand on the form functionality..
		LookUpBarcode(control.value);
		
	//? return !(window.event && window.event.keyCode == 13);
	}
}
function addBarcodeButton()
{
	barcode_control = document.getElementById('barcode');
	LookUpBarcode(barcode_control.value)
}
function LookUpBarcode(barcode_value)
{
		//return is pressed - do our thing..
		//control.value is our value
		invoice_table_object.copyHTMLTableDataToObject();
		var post_string = {};
		post_string['barcode'] = trim(barcode_value);
		post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
		var barcode_url = 'barcode.php';
		$.ajax({
	 			type: 'POST',
	  			url: barcode_url,
	  			data: post_string,
	 			async: true,
	  			success: 	function(response) 
	  			{
	  				console.log(response);
	  				//now we need to send this response out for processing...
	  				var parsed_data = parseJSONdata(response);
	  				if (typeof parsed_data['error'] !== "undefined")
	  				{
	  					PlaySoundV3(ERROR_BEEP_FILENAME);
	  					alert (barcode_value + ' : ' + parsed_data['error']);
	  					barcode_control = document.getElementById('barcode');
	  					barcode_control.focus();
    					barcode_control.select();
	  				}
	  				else
	  				{
	  					PlaySoundV3(SUCCESS_BEEP_FILENAME);
	  					barcode_control = document.getElementById('barcode');
	  					barcode_control.focus();
    					barcode_control.select();
    					//parse it here:
    					
    					//console.log(parsed_data);
    					//this code should be modifiable per form....
    					
    					
    					//now if it is a gift card we need special processing....
    					if(parsed_data['content_type'] == 'CREDIT_CARD')
    					{
    						barcode_control.value='';
    						barcode_control.focus();
    						card_found = false;
    						//make sure the card number is not on another row.
    						for(row=0;row<invoice_table_object.rowCount;row++)
    						{
    							if(invoice_table_object.table_data_object['barcode'][row] ==parsed_data['card_number'])
    							{
    								//problem
    								card_found = true;
    								alert('Card Can Only be Used Once');
    								
    							} 
    						}
    						if(card_found == false)
    						{
    							invoice_table_object.addItemToTable(parsed_data);
    							row = invoice_table_object.rowCount-1;
    							//disable some cells
    							if(parsed_data['barcode'] != '')
    							{
    								invoice_table_object.disableCell(row, 'barcode');
    							}
    							invoice_table_object.disableCell(row, 'quantity');
    							invoice_table_object.disableCell(row, 'pos_discount_id');
    							invoice_table_object.disableCell(row, 'pos_sales_tax_category_id');
    							invoice_table_object.disableCell(row, 'discount');
    							invoice_table_object.disableCell(row, 'sale_price');
								calculateTotals();
    						}
    					}
    					else if(parsed_data['content_type'] == 'PRODUCT')
    					{
    						invoice_table_object.addItemToTable(parsed_data);
							calculateTotals();
    					}
    					else if(parsed_data['content_type'] == 'PROMOTION')
    					{
    						processPromotionCode(parsed_data);
    					}
    					else
    					{
    					}
	  				}
	  			}
	});
}

function changeAddress(control)
{
	//when changing an address we need to load all the tax up.
	//probably need to store the invoice and re-load.....
	//need to store the draft... then we need to re-cacluate... then reload...
	if(control.value == 'add')
	{
		//go to the add address page....
		//url = 'address.php?type=add&pos_customer_id=' + pos_customer_id +'&ref=' +	encodeURI('retail_sales_invoice.php?type=edit&pos_sales_invoice_id='+pos_sales_invoice_id);
		url  = 'address.php?type=add&pos_customer_id=' + pos_customer_id +'&ref=' +	invoice_url;
	}
	else
	{
		url = "re_process_invoice.php?pos_sales_invoice_id="+pos_sales_invoice_id;
	}
	saveDraftAndGo(url);
}
function checkAndAddShipping(control)
{
	invoice_table_object.copyHTMLTableDataToObject();
	//go through each line.... if there is no shipping add it
	if(control.checked)
	{
		var rowCount = invoice_table_object.rowCount;
		var shipping_row = -1;
		for(row=0; row<rowCount; row++)
		{
			if(invoice_table_object.table_data_object['content_type'][row] == 'SHIPPING')
			{
				shipping_row = row;
			}
		}
		if(shipping_row == -1)
		{
			//add a row
			//we should probably ajax the shipping here so it is ready to go...
			if(document.getElementById('pos_address_id'))
			{
				pos_address_id = document.getElementById('pos_address_id').value;
			}
			else
			{
				pos_address_id = 'false';
			}
			if(pos_address_id == 'false')
			{
				//can't get the shipping without an address
			}
			else
			{
			}
		
			invoice_table_object.copyHTMLTableDataToObject();
			var post_string = {};
			post_string['pos_address_id'] = pos_address_id;
			post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
			var shipping_url = POS_ENGINE_URL + '/services/shipping/ajax_shipping.php';
			$.ajax({
					type: 'POST',
					url: shipping_url,
					data: post_string,
					async: true,
					success: 	function(response) 
					{
						console.log(response);
						//now we need to send this response out for processing...
						var parsed_data = parseJSONdata(response);
						if (typeof parsed_data['error'] !== "undefined")
						{
							PlaySoundV3(ERROR_BEEP_FILENAME);
							alert (parsed_data['error']);
						}
						else
						{
							//PlaySoundV3(SUCCESS_BEEP_FILENAME);
						
							//parse it here:
						
							//console.log(parsed_data);
							//this code should be modifiable per form....
						
						
							if(parsed_data['content_type'] == 'SHIPPING')
							{
								invoice_table_object.addItemToTable(parsed_data);
								row = invoice_table_object.rowCount-1;
							/*	invoice_table_object.disableCell(row, 'special_order');
								invoice_table_object.disableCell(row, 'ship');
								invoice_table_object.disableCell(row, 'quantity');
								invoice_table_object.disableCell(row, 'pos_discount_id');
								invoice_table_object.disableCell(row, 'pos_sales_tax_category_id');
								invoice_table_object.disableCell(row, 'discount');
								invoice_table_object.disableCell(row, 'sale_price');*/
								disableColumns();
								calculateTotals();
							}

							else
							{
							}
						}
					}
			});
		
		
		}
		else
		{
			//shipping row is already on there
		}
	}
	//now ajax the tax again...
	updateTax(control);

}
function updateTax(control)
{
	/* 
	
		to update the tax we need to know the tax jurisdiction and the tax category
		
	
	*/
	invoice_table_object.copyHTMLTableDataToObject();
	var row = getCurrentRow(control);
	var ship = invoice_table_object.table_data_object['ship'][row];
	if(document.getElementById('pos_address_id'))
	{
		pos_address_id = document.getElementById('pos_address_id').value;
	}
	else
	{
		pos_address_id = 'false';
	}
	
	var pos_sales_tax_category_id = invoice_table_object.table_data_object['pos_sales_tax_category_id'][row];
	if (pos_sales_tax_category_id == 'NULL') pos_sales_tax_category_id = 0;
	var post_string = {};
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	post_string['pos_sales_tax_category_id'] = pos_sales_tax_category_id;
	post_string['pos_address_id'] = pos_address_id;
	post_string['ship'] = ship;
	console.log('pos_sales_tax_category_id: ' + pos_sales_tax_category_id);
	
	url = 'get_tax_rate.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				//console.log(response);	
				//now we need to send this response out for processing...
				 tax_data = parseJSONdata(response);
				 console.log(tax_data);	
				 //row = getCurrentRow(control);
				 invoice_table_object.updateItemDataInTableObject(tax_data, row);
				//console.log(invoice_table_object.table_data_object);
				calculateTotals();
			}
			});
	
	
}
function lookupCustomer(url)
{
	 saveDraftAndGo("select_customer.php?complete_location=" + url);
}
function editCustomer(url, pos_customer_id)
{
	 saveDraftAndGo(POS_ENGINE_URL + "/customers/select_customer.php?pos_customer_id=" +pos_customer_id+"&complete_location=" + encodeURI(url));
}
function getTaxCalculationMethod()
{
	if(document.getElementById('tax_calculation_method_minimum').checked == true)
	{
		tax_mathod = 'minimum';
	}
	else if(document.getElementById('tax_calculation_method_average').checked == true)
	{
		tax_mathod = 'average';
	}
	else if(document.getElementById('tax_calculation_method_maximum').checked == true)
	{
		tax_mathod = 'maximum';
	}
	return tax_mathod;
}
function isGiftCard(row)
{
	if(invoice_table_object.table_data_object['content_type'][row] == 'CREDIT_CARD')
	{
		return true;
	}
	else
	{
		return false;
	}
}
function isShipping(row)
{
	if(invoice_table_object.table_data_object['content_type'][row] == 'SHIPPING')
	{
		return true;
	}
	else
	{
		return false;
	}
}
function isReturn(row)
{	
	quantity = myParseFloat(invoice_table_object.table_data_object['quantity'][row]);
	if(quantity < 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function disableColumns()
{
	//this needs to disable the appropriate columns
	var rowCount = invoice_table_object.rowCount;
	for(row=0; row<rowCount; row++)
	{
		if(invoice_table_object.table_data_object['content_type'][row] == 'SHIPPING')
		{
			invoice_table_object.disableCell(row, 'special_order');
    		invoice_table_object.disableCell(row, 'ship');
    		invoice_table_object.disableCell(row, 'quantity');
    		invoice_table_object.disableCell(row, 'pos_discount_id');
    		invoice_table_object.disableCell(row, 'pos_sales_tax_category_id');
    		invoice_table_object.disableCell(row, 'discount');
    		invoice_table_object.disableCell(row, 'sale_price');
		}
		else if(invoice_table_object.table_data_object['content_type'][row] == 'CREDIT_CARD')
		{
		}
		else if(invoice_table_object.table_data_object['content_type'][row] == 'PRODUCT')
		{
		}
		else if(invoice_table_object.table_data_object['content_type'][row] == 'SERVICE')
		{
		}
	}
	
}
function calculateTotals(control)
{
	//tax method:
	//tax_method = getTaxCalculationMethod();	
	//tax_method = 'minimum';
	/*
		1 - set the tax brackets based on price and discounting
		2 - calculate a sub total to calculate the allowable in-store discount
		3 - apply in store discounts to products
		4 - calculate tax rates
	*/
	invoice_table_object.copyHTMLTableDataToObject();
	promotion_table_object.copyHTMLTableDataToObject();
	//need to update the total on each line.
	var rowCount = invoice_table_object.rowCount;

	var tax = 0;
	var tax_total = 0;
	var line_total;
	var sale_price_total=0;

	var sub_total = 0;
	var line_discount;
	var discount_total = 0;
	var qualifying_promotion_amount = 0;
	var qualifying_amount = 0;
	var total_quantity = 0;
	var return_quantity = 0;
	//var invoice_date = document.getElementById('invoice_date').value;
	if(document.getElementById('pos_address_id'))
	{
		pos_address_id = document.getElementById('pos_address_id').value;
	}
	else
	{
	  pos_address_id = 'false';
	}
	//from this loop 
	//I need the
	//full price total
	//return total
	//gift card total
	//discounted product total
	
	var discounted_total=0;
	var full_price_total = 0;	
	var return_total = 0;
	var gift_card_total = 0;
	var shipping_total = 0;
	if (rowCount>0)
	{
		for(row=0; row<rowCount; row++)
		{
			quantity = myParseFloat(invoice_table_object.table_data_object['quantity'][row]);
			if(quantity >0)
			{
				retail_price = myParseFloat(invoice_table_object.table_data_object['retail_price'][row]);
				//if it is a gift card sale price is the retail price
				if(isGiftCard(row))
				{
					sale_price = retail_price;
					gift_card_total = gift_card_total + retail_price;
				}
				else if(isShipping(row))
				{
					sale_price = retail_price;
					shipping_total = shipping_total + retail_price;
				}
				else
				{
					sale_price = myParseFloat(invoice_table_object.table_data_object['sale_price'][row]);
					
				}
				var discount = getDiscountValue(invoice_table_object.table_data_object['discount'][row],sale_price,row);
				//console.log(discount + ' Discount');
				
				if(!isShipping(row)) total_quantity = total_quantity+quantity;
				//discount is a string!
				new_price = getFinalPrice(row);
				line_discount = quantity*(sale_price - new_price);
				discount_total = line_discount+discount_total;
				line_total = new_price*quantity;
				//count how many 'full price' items there are, for discounting code.
				//neither full price nor sale price applies to gift cards..
	//			console.log('Sale Price: ' + sale_price);
				if(!isGiftCard(row) && !isShipping(row))
				{
					if((retail_price-sale_price) < 0.0001 && (discount == 0))
					{
						if(retail_price>sale_price)
						{
							full_price_total = full_price_total + retail_price*quantity;
						}
						else
						{
							full_price_total = full_price_total + sale_price*quantity;
						}
					}
					else
					{
						discounted_total = discounted_total + (sale_price-discount)*quantity
					}
				}
				//invoice_table_object.table_data_object['extension'][row] = line_total;
				//sub_total = sub_total + line_total;
			}
			else
			{
				
				return_quantity = return_quantity + 1;				
				return_total = return_total - getFinalPrice(row)*quantity;
			
			}
		}
	}
	console.log('Dicounted Total: ' + discounted_total);
	console.log('Returned Total: ' + return_total);
	console.log('Full Price Total: ' + full_price_total);	
	console.log('Gift card Total: ' + gift_card_total);	
	console.log('Shipping Total: ' + shipping_total);	
	
	sub_total = discounted_total - return_total + full_price_total + gift_card_total+shipping_total;
	
// ********************* IN STORE DISCOUNTS ********************************//		
	//the problem with in-store discount is that it can change the tax rate of products...
	//lets get what discount is avaialble
	//what products are able to be discounted?
	//take off the dollar amounts first, followed by the percent amounts...
	var in_store_promotion = 0;
	var discountrowCount = promotion_table_object.rowCount;
	
	full_price_remaining = full_price_total;
	for(dr=0; dr<discountrowCount; dr++)
	{
		var expiration_date = promotion_table_object.table_data_object['expiration_date'][dr];
		if(promotion_table_object.table_data_object['promotion_type'][dr] == 'Pre Tax')
		{
			applied_amount = 0;
			if(promotion_table_object.table_data_object['percent_or_dollars'][dr] == '$')
			{
				if(compareTwoDates(invoice_date, expiration_date) < 0) //return -1 less, 0 same, 1 more
				{
					//the qualifying amount might be zero, meaning it can apply to all full price items
					//however when the qualifying amount is zero and the full price items are zero this will apply it to sale items...
					//so we need to say what
					
					qualifying_promotion_amount = myParseFloat(promotion_table_object.table_data_object['qualifying_amount'][dr]);
					promotion_amount = myParseFloat(promotion_table_object.table_data_object['promotion_amount'][dr]);
					
					if(qualifying_promotion_amount < promotion_amount)
					{
						qualifying_promotion_amount = promotion_amount;
						if(full_price_remaining<qualifying_promotion_amount)
						{
							qualifying_promotion_amount = full_price_remaining;
						}
					}
					//now check if the promotion can be applied
					//console.log('Full Price Total Remaining: ' + full_price_remaining);
					//console.log('Qualifying Promotion Amount: ' + qualifying_promotion_amount);
					
					//full price total is zero.
					//qualifying amount is zero
					//we should not go into the next loop
					
					//full price total is 1
					//we should go into the next loop
					
					if(full_price_remaining >= qualifying_promotion_amount)
					{
						applied_amount = myParseFloat(promotion_table_object.table_data_object['promotion_amount'][dr]);
						if (applied_amount > full_price_remaining)
						{
							applied_amount = full_price_remaining;
						}
						in_store_promotion = in_store_promotion + applied_amount;
						full_price_remaining = full_price_remaining - qualifying_promotion_amount;
						

						//console.log('In Store Promotion: ' + in_store_promotion);
						//console.log('Full Price Total Remaining: ' + full_price_remaining);
					}
				}
				else
				{
					console.log('EXPIRED');
				}
				promotion_table_object.table_data_object['applied_amount'][dr] = applied_amount;
			}
			
		}
	}
	//can only take one percentage off, that will be the first one found...
	var percent_found = false;
	for(dr=0; dr<discountrowCount; dr++)
	{
		expiration_date = promotion_table_object.table_data_object['expiration_date'][dr];
		if(promotion_table_object.table_data_object['promotion_type'][dr] == 'Pre Tax' &&!percent_found)
		{
			applied_amount = 0;
			if(promotion_table_object.table_data_object['percent_or_dollars'][dr] == '%')
			{
				if(compareTwoDates(invoice_date, expiration_date) < 1) 	
				//return -1 date one is before date 2, 0 same, 1 date one is after date 2

				{
					percent_found = true;
					applied_amount = ((full_price_remaining) * (myParseFloat(promotion_table_object.table_data_object['promotion_amount'][dr])/100));
					in_store_promotion = in_store_promotion + applied_amount;
					full_price_remaining = 0;
				}
				else
				{
					console.log('EXPIRED');
					
				}
				promotion_table_object.table_data_object['applied_amount'][dr] = applied_amount;
			}
			
		}
	}
	
	//why would this happen?
	/*if(in_store_promotion > sub_total)
	{
		in_store_promotion = sub_total;
	}*/
	preTax_subtotal = sub_total - in_store_promotion;
	
	//now that we know how much in_store_promotion can be used apply it to the full price items
	applyInStorePromotionToFullPriceItems(in_store_promotion, full_price_total);
	
	
	//shipping - if we check shipping and the address is not selected then we cannot calculate tax...
	ship_ok = true;
	//check that the tax categories have been corretly applied
	tax_ok = true;
	for(row=0; row<rowCount; row++)
	{
		if(invoice_table_object.table_data_object['pos_sales_tax_category_id'][row] == 'NULL' && !isGiftCard(row))
		{
			tax_ok = false;
		}
		else
		{
			//can we calculate the tax here...
			
		}		
		
		if(invoice_table_object.table_data_object['ship'][row] == true && pos_address_id == 'false')
		{
			ship_ok = false;
			tax_ok = false;
		}
		
	}
	
	if( rowCount > 0)
	{
		 calculateSalesTaxV2();
		 if(tax_ok)
		{
			//finally calculate the taxes based on the set up rates
			//calculateLocalAndStateTaxes();
			//now calculate tax
			for(row=0; row<rowCount; row++)
			{

					//tax = myParseFloat(invoice_table_object.table_data_object['tax_total'][row]['display_value']);
				
					tax = myParseFloat(invoice_table_object.table_data_object['tax_total'][row]);
					tax_total = tax+tax_total;
			
			}
	
		//****************** MANUFACTURER COUPON LIKE GROUPON *****************//
			//need to check that the mfg_coupon can be applied to the 'full price' total
			//now we need a re-calculation of full price after the instore discounts have been applied....
		
			mfg_amount_apply_to_anything = 0;
			mfg_amount_apply_to_full_price = 0;
			for(dr=0; dr<discountrowCount; dr++)
			{
				if(promotion_table_object.table_data_object['promotion_type'][dr] == 'Post Tax')
				{
					/*if(promotion_table_object.table_data_object['check_if_can_be_applied_to_sale_items'][dr] == '1')
					{
						mfg_amount_apply_to_anything = mfg_amount_apply_to_anything +
						myParseFloat(promotion_table_object.table_data_object['promotion_amount'][dr]);
					}
					else
					{*/
					//better be a dollar figure
					applied_amount = 0;
					if(promotion_table_object.table_data_object['percent_or_dollars'][dr] == '$')
					{
						var expiration_date = promotion_table_object.table_data_object['expiration_date'][dr];
					
						if(compareTwoDates(invoice_date, expiration_date) < 0) //return -1 less, 0 same, 1 more
						{
							applied_amount = myParseFloat(promotion_table_object.table_data_object['promotion_amount'][dr]);
							if(applied_amount > full_price_total )
							{
								//reduce the amount that can be applied
								applied_amount = full_price_total;
								full_price_total=0;
							}
							else
							{
								 full_price_total =  full_price_total - applied_amount;
							}
						
							mfg_amount_apply_to_full_price = mfg_amount_apply_to_full_price + applied_amount;
						
						}
						else
						{
							applied_amount = myParseFloat(promotion_table_object.table_data_object['expired_value'][dr]);
							if(applied_amount > full_price_remaining )
							{
								//reduce the amount that can be applied
								applied_amount = full_price_remaining;
								full_price_remaining=0;
							}
							else
							{
								 full_price_remaining =  full_price_remaining - applied_amount;
							}
							mfg_amount_apply_to_full_price = mfg_amount_apply_to_full_price + 
							applied_amount;
						}
						promotion_table_object.table_data_object['applied_amount'][dr] = applied_amount;
					}
				
				}
			}
		
			
			/* is this even possible?
			if(mfg_amount_apply_to_full_price > sub_total)
			{
				mfg_amount_apply_to_full_price = preTax_subtotal;
			}*/
			document.getElementById('post_tax_promotion_amount').value = mfg_amount_apply_to_full_price;
			//***************************************************************************//	
			//shipping_amount = document.getElementById('shipping_amount').value;
			//how do we tax shipping? 
			//we need to look up the
			//basically not ready for this!
			invoice_tax_total = round2(tax_total,2);
			grande_total = preTax_subtotal + tax_total - mfg_amount_apply_to_full_price;
			le_grand_total = round2(grande_total,2);
		}
		else
		{
		invoice_tax_total = 'Tax Class Not Assigned';
		le_grand_total = 'TBD';	
		}
		
	}
	else
	{
		 if(ship_ok)
		 {
		 	invoice_tax_total = 'Tax Class Not Assigned';
		 }
		 else
		 {
		 	invoice_tax_total = 'Address Not Assigned';
		 }
		 
		
		le_grand_total = 'TBD';
	}
	
	//assign values:
	//document.getElementById('pre_discount_subtotal').value = round2(sub_total,2);
	//lets caluculate the 'line_total' or extension
	for(row=0; row<rowCount; row++)
	{
		//the extension will either be the sale price - discount - applied_discount + tax
		if(isGiftCard(row))
		{
			invoice_table_object.table_data_object['extension'][row] = getFinalPrice(row);
		}
		else if(isShipping(row))
		{
			invoice_table_object.table_data_object['extension'][row] = myParseFloat(getFinalPrice(row)) +myParseFloat(invoice_table_object.table_data_object['tax_total'][row]);
		}
		else
		{
			invoice_table_object.table_data_object['extension'][row] = myParseFloat(invoice_table_object.table_data_object['quantity'][row]*getItemTotal(row)) + myParseFloat(invoice_table_object.table_data_object['tax_total'][row]);
		}
	}
	document.getElementById('full_price_subtotal').value = round(full_price_total,2);
	document.getElementById('discounted_subtotal').value = round(discounted_total,2);
	document.getElementById('pre_tax_promotion_amount').value = round(in_store_promotion,2);
	document.getElementById('pre_tax_subtotal').value = round2(preTax_subtotal);
	document.getElementById('total_quantity').value = total_quantity;
	document.getElementById('invoice_tax_total').value = invoice_tax_total;
	document.getElementById('total_returns').value = return_total;
	document.getElementById('le_grande_total').value = '$' + le_grand_total;
	//document.getElementById('you_save').value = round2(discount_total,2);
	//console.log(invoice_table_object.table_data_object);
	invoice_table_object.writeObjectToHTMLTable(control);
	promotion_table_object.writeObjectToHTMLTable(control);
	
}
function getItemTotal(row)
{
	if(isShipping(row))
	{
		var line_item_total = myParseFloat(invoice_table_object.table_data_object['retail_price'][row]);
	}
	else
	{
		sale_price = myParseFloat(invoice_table_object.table_data_object['sale_price'][row]);
		discount = getDiscountValue(invoice_table_object.table_data_object['discount'][row],sale_price,row);
		applied_instore_discount = myParseFloat(invoice_table_object.table_data_object['applied_instore_discount'][row]);
		var line_item_total = (sale_price - discount) - applied_instore_discount;
	}
	return line_item_total;
}
function applyInStorePromotionToFullPriceItems(in_store_promotion,full_price_total)
{
	console.log('function applyInStorePromotionToFullPriceItems');
	//we are going to use a weighted average....
	//now is the time we are killing the 'array_values' feature...
	var instore_remainder_counter;
	instore_remainder_counter = 0.0;
	
	for(var row=0; row<invoice_table_object.rowCount; row++)
	{
		invoice_table_object.table_data_object['applied_instore_discount'][row] = 0;
		quantity = myParseFloat(invoice_table_object.table_data_object['quantity'][row]);
		retail_price = myParseFloat(invoice_table_object.table_data_object['retail_price'][row]);
		sale_price = myParseFloat(invoice_table_object.table_data_object['sale_price'][row]);
		discount = getDiscountValue(invoice_table_object.table_data_object['discount'][row],sale_price,row);
		if(!isGiftCard(row) && !isReturn(row))
		{
			if((retail_price-sale_price) < 0.0001 && (discount == 0) && full_price_total>0)
			{
				discount_to_apply = round(((retail_price*quantity)/full_price_total)*in_store_promotion,2);
				if(discount_to_apply>sale_price)
				{
					discount_to_apply = sale_price;
				}
				invoice_table_object.table_data_object['applied_instore_discount'][row] = discount_to_apply;
				instore_remainder_counter = instore_remainder_counter + discount_to_apply;
				//console.log('total applied: ' + instore_remainder_counter);
			}
		
		}
	}
	//console.log('total applied: ' + instore_remainder_counter)
	remainder = round(myParseFloat(in_store_promotion) - myParseFloat(instore_remainder_counter),2);
	
	console.log('in_store_promotion:' + in_store_promotion);
	console.log('instore_remainder_counter:' + instore_remainder_counter);
	console.log('full_price_total:' + full_price_total);
	console.log('discount remainder1:' + remainder);
	//now we need to take the remainder off...
	//remainder can be + or -
	for(var row=0; row<invoice_table_object.rowCount; row++)
	{

			quantity = myParseFloat(invoice_table_object.table_data_object['quantity'][row]);
			retail_price = myParseFloat(invoice_table_object.table_data_object['retail_price'][row]);
			sale_price = myParseFloat(invoice_table_object.table_data_object['sale_price'][row]);
			current_applied_discount = myParseFloat(invoice_table_object.table_data_object['applied_instore_discount'][row]);
			if(!isGiftCard(row) && !isReturn(row))
			{
				if((retail_price-sale_price) < 0.0001 && (discount == 0))
				{
					if(sale_price*quantity+remainder+current_applied_discount>=0 && current_applied_discount + remainder >= 0)
					{
					invoice_table_object.table_data_object['applied_instore_discount'][row] = current_applied_discount + remainder;
						remainder = 0;
						//console.log('remainder:' + remainder);
					}
					else
					{
						
					}
				}
			
		}
	}
	
}
function getFinalPrice(row)
{
	if(isGiftCard(row))
	{
		return invoice_table_object.table_data_object['retail_price'][row];
	}
	else if(isShipping(row))
	{
		return invoice_table_object.table_data_object['retail_price'][row];
	}
	else
	{
		discount = invoice_table_object.table_data_object['discount'][row];
		sale_price = invoice_table_object.table_data_object['sale_price'][row];
		//if(discount>sale_price) discount = sale_price;
		return parseDiscount(discount,sale_price,row);
	}
	
	
}
function  getDiscountValue(discount,price,row)
{
	price = myParseFloat(price);
	discount += '';
	//discount could be numeric or could be 10%
	if(discount.indexOf('%') != -1)
	{
		//alert(discount.substr(0,discount.indexOf('%')));
		new_discount = myParseFloat(trim(discount.replace('%', '')));
		invoice_table_object.table_data_object['discount_type'][row]='PERCENT';
		discount = (price*(new_discount/100));
		
		
	}
	else if (discount.indexOf('$') != -1)
	{
		new_discount = myParseFloat(trim(discount.replace('$', '')));
		invoice_table_object.table_data_object['discount_type'][row]='DOLLAR';
		discount = new_discount;
		
	}
	else
	{
		invoice_table_object.table_data_object['discount_type'][row]='DOLLAR';
		discount = discount;
		
	}
	if(discount>price)
		{
			return price;
		}
		else
		{
			return discount;
		}
}
function parseDiscount(discount,price,row)
{
	price = myParseFloat(price);
	discount += '';
	//discount could be numeric or could be 10%
	if(discount.indexOf('%') != -1)
	{
		//alert(discount.substr(0,discount.indexOf('%')));
		new_discount = myParseFloat(trim(discount.replace('%', '')));
		invoice_table_object.table_data_object['discount_type'][row]='PERCENT';
		final_price =  price - (price*(new_discount/100));
		
		
	}
	else if (discount.indexOf('$') != -1)
	{
		new_discount = myParseFloat(trim(discount.replace('$', '')));
		invoice_table_object.table_data_object['discount_type'][row]='DOLLAR';
		final_price = price - new_discount;
		
	}
	else
	{
		invoice_table_object.table_data_object['discount_type'][row]='DOLLAR';
		final_price = price - discount;
		
	}
	if(final_price<0)
		{
			return 0;
		}
		else
		{
			return final_price;
		}
}
function preparePostData()
{
	var post_string = {};
	
	//customer data:
	//address, email, phone
	if(document.getElementById('pos_address_id'))
	{
		pos_address_id = document.getElementById('pos_address_id').value;
		email  = document.getElementById('email1').value;
	 phone = document.getElementById('phone').value;
	}
	else
	{
		pos_address_id = 'false';
		email = '';
		phone = '';
	}
	post_string['pos_address_id'] = pos_address_id;
	post_string['promotion_tbody_def'] = promotion_table_object.tbody_def;

		post_string['promotion_table_data_object'] = promotion_table_object.table_data_object;

	post_string['email1'] = email;
	post_string['phone'] = phone;
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;//document.getElementById('pos_sales_invoice_id').value;
	//post_string['tax_calculation_method'] = //getTaxCalculationMethod();
	//do we need this one?
	//post_string['shipping_amount'] = document.getElementById('shipping_amount').value;
	//post_string['pre_tax_promotion_amount'] = document.getElementById('pre_tax_promotion_amount').value;
	//post_string['post_tax_promotion_amount'] = document.getElementById('post_tax_promotion_amount').value;
	//post_string['pos_state_tax_jurisdiction_id']
	//post_string['pos_local_tax_jurisdiction_id']
	post_string['pos_customer_id'] = document.getElementById('pos_customer_id').value;
	post_string['invoice_date'] = invoice_date;//document.getElementById('invoice_date').value;
	post_string['invoice_table_data_object'] = invoice_table_object.table_data_object;
	//JSON.stringify(invoice_table_object.table_data_object);//JSON.stringify(this.table_data_object);
	post_string['invoice_tbody_def'] = invoice_table_object.tbody_def;
	return post_string;
	
	
	
	
}
function saveDraftAndReload()
{
	save_url = "update_invoice_to_server.php";
	
	invoice_table_object.table_data_array = invoice_table_object.copyHTMLTableDataToObject();
	post_string = preparePostData();
	$.post(save_url, post_string,
	function(response) 
	{
		document.location.reload(true);
	});
	
}
function saveDraft()
{
	//copy the table data into the table data array
	save_url = "update_invoice_to_server.php";
	invoice_table_object.copyHTMLTableDataToObject();
	promotion_table_object.copyHTMLTableDataToObject();
	post_string = preparePostData();
	console.log(post_string);
	$.post(save_url, post_string,
   	function(response) {
     console.log(response);
     needToConfirm=false;
   });
}
function saveDraftAndGo(url)
{
	
	save_url = "update_invoice_to_server.php";
	
	invoice_table_object.table_data_array = invoice_table_object.copyHTMLTableDataToObject();
	post_string = preparePostData();
	console.log(post_string);
	$.post(save_url, post_string,
	function(response) 
	{
		
		
		//unlock
		unlock_url = POS_ENGINE_URL + '/includes/php/unlock_entry.php';
		unlock_post = {};
		unlock_post['table'] = 'pos_sales_invoice';
    	unlock_post['primary_key_name'] = 'pos_sales_invoice_id';
    	unlock_post['primary_key_value'] = pos_sales_invoice_id;
		$.post(unlock_url, unlock_post,
			function(response2) 
			{
				//alert(response2);
				window.location = url;
			}
		);
	});
	
}
/********Tax calculations *********************/

function calculateSalesTaxV2()
{
	//to cacluate the sales tax we need to first get the item total
	//then we need the tax rate
	//then we can calculate the tax
	for(row=0; row<invoice_table_object.rowCount; row++)
	{
		if(isGiftCard(row))
		{
			// no tax category, 0 tax rate, no taxable total
			invoice_table_object.table_data_object['taxable_total'][row] = 0;
			invoice_table_object.table_data_object['tax_rate'][row] = 0;
			invoice_table_object.table_data_object['tax_total'][row] = 0;
		}

		else
		{
			exemption_value = getExemptionValue(row);
			item_total = getItemTotal(row);
			quantity = myParseFloat(invoice_table_object.table_data_object['quantity'][row]);
			//what is the tax rate for this price and tax category id?
			//console.log(item_total);
			if(item_total <= exemption_value && exemption_value != 0)
			{
				tax_rate = myParseFloat(invoice_table_object.table_data_object['state_exemption_tax_rate'][row]) + myParseFloat(invoice_table_object.table_data_object['local_exemption_tax_rate'][row]);
			}
			else
			{
				tax_rate = myParseFloat(invoice_table_object.table_data_object['state_regular_tax_rate'][row]) + myParseFloat(invoice_table_object.table_data_object['local_regular_tax_rate'][row]);
			}
			if(quantity>=0)
			{
				invoice_table_object.table_data_object['taxable_total'][row] = item_total*quantity;
				invoice_table_object.table_data_object['tax_rate'][row] = tax_rate;
				invoice_table_object.table_data_object['tax_total'][row] = round(item_total*quantity*tax_rate/100,2);
			}
			else
			{
				//return
				invoice_table_object.table_data_object['taxable_total'][row] = -item_total*quantity;
				invoice_table_object.table_data_object['tax_rate'][row] = tax_rate;
				invoice_table_object.table_data_object['tax_total'][row] = round(item_total*quantity*tax_rate/100,2);
			}
		}
	}
}
function getExemptionValue(row)
{
		//basically use the state exeption value as the exemption value....
		/*var exemption_value;
		//take the greater value as the exemption value....
		var state_exemption_value = myParseFloat(invoice_table_object.table_data_object['state_exemption_value'][row]);
		var local_exemption_value = myParseFloat(invoice_table_object.table_data_object['local_exemption_value'][row]);

		if(state_exemption_value>local_exemption_value)
		{
			exemption_value = state_exemption_value;
		}
		else
		{
			exemption_value = local_exemption_value;
		}
		return exemption_value;*/
		return invoice_table_object.table_data_object['state_exemption_value'][row];
}


function sendInvoiceToPrinter(type, pos_sales_invoice_id)
{
		$('#customer_print_button').attr('disabled','disabled');
		$('#store_print_button').attr('disabled','disabled');
		$('#gift_receipt').attr('disabled','disabled');
		//print_message = $('#print_button').attr("value");
		print_url = 'print_sales_invoice.php';
		print_post = {};
		print_post['pos_sales_invoice_id'] = pos_sales_invoice_id;
		print_post['type'] = type;
		$.post(print_url, print_post,
			function(response2) 
			{
				$('#save_alert').html(response2);
				$("#save_alert").fadeOut(1600, "linear", function (){
					$("#save_alert").html('');
					$("#save_alert").show();
					$('#store_print_button').removeAttr('disabled');
					$('#customer_print_button').removeAttr('disabled');
					$('#gift_receipt').removeAttr('disabled');
					});
					
			}
		);

}
function emailInvoice(pos_sales_invoice_id)
{
		//here is where we can check and add email....with cool pop up boxes....
		
		//get the email address
		//check the email address
		
		
		$('#email_button').attr('disabled','disabled');
		print_url = 'print_sales_invoice.php';
		print_post = {};
		print_post['pos_sales_invoice_id'] = pos_sales_invoice_id;
		print_post['type'] = 'email_pdf';
		//print_post['type'] = 'email_html'; html email is simply more and more work....
		$.post(print_url, print_post,
			function(response2) 
			{
				console.log(response2);
				$('#save_alert').html(response2);
				if(response2 == 'ERROR')
				{
					alert("Looks like there is an error in the email address, please check and try to resend.");
				}
				
				$("#save_alert").fadeOut(1600, "linear", function (){
					$("#save_alert").html('');
					$("#save_alert").show();
					$('#email_button').removeAttr('disabled');
					});
					
			}
		);
}
function openInvoiceInline(pos_sales_invoice_id)
{
		open_win('print_sales_invoice.php?type=customer_inline&pos_sales_invoice_id='+pos_sales_invoice_id);

}
function productSearchFocus()
{
	//alert($('#product_search').val()); = 'Type to search, leave spaces between search terms...'
	$('#product_search').select();
	$('#product_search').focus();
	
}
function continueToPayments(url)
{

	if(validateInvoiceForm())
	{
		saveDraftAndGo(url);
	}
}
function validateInvoiceForm()
{

	//alert('validating...');
	var rowCount = invoice_table_object.rowCount;

	//we need to make sure the discount is not greater than the sale price.....
	errors = '';
	if (rowCount>0)
	{
		for(row=0; row<rowCount; row++)
		{
			quantity = myParseFloat(invoice_table_object.table_data_object['quantity'][row]);
			if(quantity >0)
			{
				retail_price = myParseFloat(invoice_table_object.table_data_object['retail_price'][row]);
				//if it is a gift card sale price is the retail price
				if(isGiftCard(row))
				{
					sale_price = retail_price;
					//gift_card_total = gift_card_total + retail_price;
				}
				else if(isShipping(row))
				{
					sale_price = retail_price;
					//shipping_total = shipping_total + retail_price;
				}
				else
				{
					sale_price = myParseFloat(invoice_table_object.table_data_object['sale_price'][row]);
					
				}
				var discount = invoice_table_object.table_data_object['discount'][row];
				if(discount.indexOf('%') != -1)
				{
		//alert(discount.substr(0,discount.indexOf('%')));
					new_discount = myParseFloat(trim(discount.replace('%', '')));
					discount = (sale_price*(new_discount/100));
		
		
				}
				else if (discount.indexOf('$') != -1)
				{
						new_discount = myParseFloat(trim(discount.replace('$', '')));
						discount = new_discount;
		
				}
				else
				{
					invoice_table_object.table_data_object['discount_type'][row]='DOLLAR';
					discount = discount;
		
				}
				//alert(discount + ' Discount, Sale Price: ' + sale_price);
				
				
				if (discount>sale_price)
				{
					errors += 'Please Correct: Discount is greater than sale price, row: ' + parseInt(parseInt(row) + parseInt(1)) + '\r\n';
				}
			}
			
		}
	}
	if (errors != '')
	{
		alert (errors);
		return false;
	}
	else
	{
		return true;
	}


}