 var parsed_autocomplete_data;
 var selected_autocomplete_index;
 $(function() 
 {

	$( "#product_search" )
	// don't navigate away from the field on tab when selecting an item
	.bind( "keydown", function( event ) 
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
		var barcode = $('#barcode').val().toUpperCase();
		$('#barcode').val(barcode);
		LookUpBarcode(control.value);
		
	//? return !(window.event && window.event.keyCode == 13);
	}
}
function LookUpBarcode(barcode_value)
{
	var post_string = {};
	post_string['barcode'] = trim(barcode_value);
	var barcode_url = 'barcode_for_inventory.php';
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
	var rowCount = inventory_table_object.rowCount;
	var found = false;
	for(var row=0; row<rowCount; row++)
	{
		//console.log(inventory_table_object.table_data_object['barcode'][row])
		//console.log(barcode);
		
		if(inventory_table_object.table_data_object['barcode'][row] == barcode && found == false)
		{
			
		inventory_table_object.table_data_object['quantity'][row] = parseInt(inventory_table_object.table_data_object['quantity'][row]) + 1;
			found = true;
			inventory_table_object.writeObjectToHTMLTable();
		}
	}
	if (found == false)
	{
		inventory_table_object.addItemToTable(parsed_data);
		var new_row = inventory_table_object.rowCount-1;	
    	//disableHTMLRow(row);
    	//disable some cells
    	disableProductCells(new_row);
    	
    	
    	
    }
}
function disableProductCells(row)
{
	inventory_table_object.disableCell(row, 'pos_product_sub_id');
	inventory_table_object.disableCell(row, 'item');
	inventory_table_object.disableCell(row, 'cost');
	inventory_table_object.disableCell(row, 'retail_price');
}
function additionalInit()
{
	//disable the rows...
	for(var row=0;row<json_table_contents.length;row++)
	{
		disableProductCells(row);
	}
	
}
function preparePostData()
{
	inventory_table_object.copyHTMLTableDataToObject();
	var post_string = {};
	post_string['pos_inventory_event_id'] = pos_inventory_event_id;
	post_string['inventory_table_data_object'] = inventory_table_object.table_data_object;
	//JSON.stringify(invoice_table_object.table_data_object);//JSON.stringify(this.table_data_object);
	post_string['inventory_tbody_def'] = inventory_table_object.tbody_def;
	return post_string;
	
	
	
	
}
function saveDraft()
{
	//copy the table data into the table data array
	save_url = "update_inventory_to_server.php";
	post_string = preparePostData();
	$.post(save_url, post_string,
   	function(response) {
     alert(response);
     needToConfirm=false;
   });
}
function saveDraftAndGo(url)
{
	
	save_url = "update_inventory_to_server.php";
	post_string = preparePostData();
	$.post(save_url, post_string,
	function(response) 
	{
		window.location = url;
	});
	
}
function setSalePrice()
{
	//set all the discounts....
	
	for(var i=0;i<inventory_table.tdo.length;i++)
	{
		inventory_table.tdo[i]['sale_price']['data'] = $('#sale_price').val();
		
	}
	inventory_table.write();
	
}	
function setAllClearence()
{
	var value = $("#setAll").is(':checked') ? 1 : 0;
	for(var i=0;i<inventory_table.tdo.length;i++)
	{
		inventory_table.tdo[i]['clearance']['data'] = value;
		
	}
	inventory_table.write();
}
function setAllUpdate()
{
	var value = $("#setAllUpdate").is(':checked') ? 1 : 0;
	for(var i=0;i<inventory_table.tdo.length;i++)
	{
		inventory_table.tdo[i]['new_modifier']['data'] = value;
		
	}
	inventory_table.write();
	
}
function validateInventorySalePricing()
{
	inventory_table.POST_TDO(form_id);
	var error = '';
	for(var i=0;i<inventory_table.tdo.length;i++)
	{
		if(!isNumber(inventory_table.tdo[i]['sale_price']['data']))
		{
			error += 'Error Row ' + (i+1) + ' Should be a number instead of ' + inventory_table.tdo[i]['sale_price']['data'];
		}
		
		if(inventory_table.tdo[i]['sale_price']['data'] <= 0)
		{
			error += 'Error Row ' + (i+1) + ' Should be greater than zero ';
		}
		
	}
	if(error == '')
	{
		
		return true;
	}
	else
	{
		alert(error);
		return false;
	}
	
	
}
function confirmCancel()
{
	if(confirm("Are you Sure you want to cancel?"))
	{
		open_win("inventory.php?type=view&pos_location_id=" + pos_location_id);
	}
}