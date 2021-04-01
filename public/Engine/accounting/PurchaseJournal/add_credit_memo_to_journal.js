//these work with the dynamic table code
function updatePOData(control)
{
	//alert('running updatePOData');
	copyHTMLTableDataToArray();
	var row = getCurrentRow(control);
	pos_purchase_order_id = control.value;
	
	//this is how we handle the pre-loaded data...
	for(var i=0;i<purchase_orders.length;i++)
	{
		if(purchase_orders[i]['pos_purchase_order_id'] == pos_purchase_order_id)
		{
			updateItemDataInTableArray(purchase_orders[i], row);
		}
	}
	/*//now we can either ajax this value or we already have it pre-loaded....
	
	//lets ajax it...
	var url = 'get_purchase_order_data.php';
	var post_string = {};
	post_string['pos_purchase_order_id'] = pos_purchase_order_id;
	post_string['pos_purchases_journal_id'] = pos_purchases_journal_id;
	$.ajax({
	 			type: 'POST',
	  			url: url,
	  			data: post_string,
	 			async: false,
	  			success: 	function(response) 
	  			{
	  				alert(response);
	  				//now we need to send this response out for processing...
    				var parsed_data = parseJSONdata(response);	
    				console.log(parsed_data);
				 	row = getCurrentRow(control);
				 	updateItemDataInTableArray(parsed_data[0], row);
	  			}
				});
	*/
	/*//user selected a po..
	//need to get the data and update
	for (col=0; col<tbody_def.length;col++)
	{
		if(typeof tbody_def[col]['ajax_update'] !== 'undefined')
		{
			table_data_array[row][col] = poData[pos_purchase_order_id][tbody_def[col]['update_data'];
		}
	}
	writeArrayToHTMLTable();*/
}
function updateAppliedAmountRemaining(control)
{
	/*var row = getCurrentRow(control);
	//applied_amount_remaining = ordered_amount-  applied_amount_from_other_invoices - applied_amount_from_this_invoice
	document.getElementsByName('applied_amount_remaining[]')[row].value = round2(document.getElementsByName('ordered_amount[]')[row].value - document.getElementsByName('applied_amount_from_other_invoices[]')[row].value - control.value,2);*/
	updateTableData(control);
	
}
