 var parsed_autocomplete_data;
 var selected_autocomplete_index;
 $(function() 
 {
	
	
	$(window).keydown(function(event){
    	if(event.keyCode == 13) 
    	{
      		event.preventDefault();
      		//alert("skipped prevented enter submit");
      		//return false;  
      	}
  	});
	$( "#product_search" ).bind( "keydown", function( event ) 
	{
		if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "ui-autocomplete" ).menu.active ) 
		{
			event.preventDefault();
		}
	})
	.autocomplete({
		source: function( request, response ) 
		{
		 $.ajax(
		 {
				url: "ajax_product_search.php",
				type: 'GET',
				async: true,
				data: 
				{
					featureClass: "P",
					style: "full",
					maxRows: 12,
					product_search_terms: request.term
				},
				success: function( data ) 
				{
					console.log(data);
					parsed_autocomplete_data = parseJSONdata(data);
					response( parsed_autocomplete_data['long_name']);
				}
			});
		},
		search: function() 
		{
			// custom minLength
			var term = this.value;
			if ( term.length < 3 ) 
			{
				return false;
			}
		},
		focus: function() 
		{
		// prevent value inserted on focus
			return false;
		},
		select: function( event, ui ) 
		{
			selected_autocomplete_index = $.inArray(ui.item.value, parsed_autocomplete_data['long_name']);
			//console.log (parsed_autocomplete_data['pos_product_sub_id'][selected_autocomplete_index]);
			/*var terms = split( this.value );
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// add placeholder to get the comma-and-space at the end
			terms.push( "" );
			this.value = terms.join( ", " );
			return false;*/
		}
	});
});
function item_or_total_change()
{
	//alert("item_or_total_change = . remove table...");
	
	//what i would like to do is remove a column on the table...
	//table.column.visible = false
	//table.update();
	
	
}
function checkPromoInput()
{
	$('#promotion_code').val($('#promotion_code').val().toUpperCase());
}
function addSubidFromSearch()
{
	//here we would get the value and add it
	var autocomplete_value = document.getElementById('product_search').value;
	index_to_lookup = $.inArray(autocomplete_value, parsed_autocomplete_data['long_name']);
	if(index_to_lookup != -1)
	{
		
		var subid = parsed_autocomplete_data['pos_product_sub_id'][index_to_lookup];
		barcode_control = document.getElementById('barcode');
		barcode_control.value = subid;
		LookUpBarcode(subid);
		
		
	}
}
function addBarcodeButton()
{
	LookUpBarcode(document.getElementById('barcode').value);
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
function LookUpBarcode(barcode_value)
{
	var post_string = {};
	post_string['barcode'] = trim(barcode_value);
	var barcode_url = 'barcode_for_promotions.php';
	$.ajax({
			type: 'POST',
			url: barcode_url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log(response);
				//now we need to send this response out for processing...
				if (response == "No Data Found For Barcode")
				{
					//PlaySoundV2('error_beep');
					PlaySoundV3(ERROR_BEEP_FILENAME);

					alert ('No Data Found for ' + barcode_value);
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
					var parsed_data = parseJSONdata(response);
					console.log(parsed_data);
					parsed_data['include_product'] = 'INCLUDE';
					//console.log(parsed_data);
					updateQuantities(parsed_data, trim(barcode_value));
					
				}
			}
			});

	//? return !(window.event && window.event.keyCode == 13);
}
function updateQuantities(parsed_data, barcode)
{
	//find out if the item is already listed, if so update the quantity, otherwise add it...
	var rowCount = product.tdo.length;
	var found = false;
	for(var row=0; row<rowCount; row++)
	{	
		if(product.tdo[row]['pos_product_id']['data'] == barcode && found == false)
		{
			found = true;
		}
	}
	if (found == false)
	{
		product.addItemToTable(parsed_data);
    	    	
    }
}
/*function parsePromotionDiscount(discount,price,row)
{
	price = myParseFloat(price);
	discount += '';
	//discount could be numeric or could be 10%
	if(discount.indexOf('%') != -1)
	{
		//alert(discount.substr(0,discount.indexOf('%')));
		new_discount = myParseFloat(trim(discount.replace('%', '')));
		invoice_table.tdo[row]['discount_type'] ='PERCENT';
		final_price =  price - (price*(new_discount/100));
		
		
	}
	else if (discount.indexOf('$') != -1)
	{
		new_discount = myParseFloat(trim(discount.replace('$', '')));
		invoice_table.tdo[row]['discount_type']='DOLLAR';
		final_price = price - new_discount;
		
	}
	else
	{
		invoice_table.tdo[row]['discount_type']='DOLLAR';
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
}*/
function validatePromotionForm()
{
	
	//write and add each table to the form for post
	form_id = 'promotion';
	buyXgetY.POST_TDO(form_id);
	product.POST_TDO(form_id);
	brand.POST_TDO(form_id);
	category.POST_TDO(form_id);
	//alert ("validate");
	error = '';
	promotion_code = document.getElementById('promotion_code').value;
	if (isNumber(promotion_code))
	{
		error += 'Problem - Promotion Code should not be a number, otherwise it could get confused as a product when barcode scanning \n';
		$('#promotion_code').focus();
		$('#promotion_code').select();
	}
	//promotion code should have no spaces....
	if( hasWhiteSpace(promotion_code))
	{
		error += 'Problem - Promotion Code should have no white space, why I don\'t know, but that will probably cause code somewhere to bite it.\n';
		$('#promotion_code').focus();
		$('#promotion_code').select();
	}

	
	var rowCount = buyXgetY.tdo.length;
	var dpcheck = 0;
	if (rowCount > 0)	
	{
		for(var row=0; row<rowCount; row++)
		{
			//alert(buyXgetY.tdo[row]['d_or_p']['data']);
			//buy should never be greater than get
			//alert(Number(buyXgetY.tdo[row]['buy']['data']) + ' ' + Number(buyXgetY.tdo[row]['get']['data']));
			if( Number(buyXgetY.tdo[row]['buy']['data']) < Number(buyXgetY.tdo[row]['get']['data']))
			{
				error += 'Problem - Get Should not be greater than buy\n';
			}
			if(!isNumber(buyXgetY.tdo[row]['discount']['data']) || !isNumber(buyXgetY.tdo[row]['buy']['data']) || !isNumber(buyXgetY.tdo[row]['get']['data']))
			{
				error += 'Problem - Buy, Get and Discount should all be numeric.\n';
			}
			
			if(Number(buyXgetY.tdo[row]['get']['data']) !== parseInt(Number(buyXgetY.tdo[row]['get']['data']), 10))
			{
				error += 'Problem - Get Value should be an Integer.\n';
				//console.log(Number(buyXgetY.tdo[row]['get']['data']));
				
			}
			if(buyXgetY.tdo[row]['d_or_p']['data'] == 'NULL')
			{
				error += 'Problem - Must Select $ or %.\n';
				buyXgetY.setFocus(row, 'discount');
			}
			else
			{
				if (dpcheck ==0)
				{
					dpcheck = buyXgetY.tdo[row]['d_or_p']['data'];
				} 
				else if(dpcheck != buyXgetY.tdo[row]['d_or_p']['data'])
				{
					error += 'Problem - Please choose only $ OR % for all rows.\n';
				}
				
			}
			
		}
	}
	else
	{
		error += 'Problem - you need to assign promotion values in the Buy X Get Y Table \n';
	}
	if(error == '')
	{
		console.log('validated');
		return true;
	}
	else
	{
		console.log('falied validation');
		alert(error);
		return false;
	}
	
	
}
function percent_change()
{
	buyXgetY.copyHTMLTableDataToObject();
	percent = $('#percent_or_dollars').val();
	//alert(percent);
	for(var row=0; row<buyXgetY.tdo.length; row++)
	{
		buyXgetY.tdo[row]['d_or_p']['data'] = percent;
	}
	buyXgetY.write();
}
function buyxADdRow()
{
	buyXgetY.addRow();
	percent_change();
	
}
function checkPromoCode()
{
	//need to ajax this shizz...
	var post_string = {};
	post_string['type'] = 'ajax';
	post_string['ajax_request'] = 'promotion_code';
	post_string['promotion_code'] = $('#promotion_code').val();
	post_string['pos_promotion_id'] = $('#pos_promotion_id').val();
	$.ajax({
	 			type: 'POST',
	  			url: 'promotion.php',
	  			data: post_string,
	 			async: true,
	  			success: 	function(response) 
	  			{
	  				console.log(response);
	  				if(response != 'OK')
	  				{
	  					alert(response);
	  					$('#promotion_code').val('');
						$('#promotion_code').focus();
						$('#promotion_code').select();
	  				}
	  
	  			}
	});
}
function cancelPromotion()
{
	if(confirm("Cancel?"))
	{
		window.location = 'list_promotions.php';
	}
}