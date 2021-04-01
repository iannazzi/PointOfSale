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
	var row = getCurrentRow(control);
	//applied_amount_remaining = ordered_amount-  applied_amount_from_other_invoices - applied_amount_from_this_invoice
	document.getElementsByName('applied_amount_remaining[]')[row].value = round2(document.getElementsByName('ordered_amount[]')[row].value - document.getElementsByName('discount_amount[]')[row].value - document.getElementsByName('applied_amount_from_other_invoices[]')[row].value - control.value,2);
	updateTableData(control);
	
}



function updateDiscount()
{
	//show_discount_percent = document.getElementById('show_discount').value;
	shipping_amount = myParseFloat(document.getElementById('shipping_amount').value);
	fee_amount = myParseFloat(document.getElementById('fee_amount').value);
	goods_amount = myParseFloat(document.getElementById('goods_amount').value);
	invoice_amount = round(goods_amount + shipping_amount+fee_amount,2);
	
	//show_discount = round(invoice_amount*(show_discount_percent/100),2);
	invoice_discount = round((invoice_amount - shipping_amount-fee_amount)*(discount/100),2);
	total_discount = round(invoice_discount,2);
	total_to_be_paid = round(invoice_amount - total_discount,2);
	document.getElementById('discount_available').value = total_discount;
	document.getElementById('discount_applied').value = total_discount;
	document.getElementById('total_to_be_paid').value = total_to_be_paid; 
	document.getElementById('invoice_amount').value = invoice_amount;
}
function updateTotal()
{
	invoice_amount = document.getElementById('invoice_amount').value;
	discount_applied = document.getElementById('discount_applied').value;
	document.getElementById('total_to_be_paid').value = invoice_amount - discount_applied; 
}
function updatePOTotal()
{

	needToConfirm=true;
	po_multiSelect = document.getElementById('pos_purchase_order_id[]');
	var po_total = 0.0;
	var invoice_total = 0.0;
	var ordered_total = 0.0;
	for (x=0;x<po_multiSelect.length;x++)
	 {
		if (po_multiSelect[x].selected)
		{
			 if(po_multiSelect[x].value != 'false')
			 {
			 	for(j=0;j<open_pos.length;j++)
			 	{
			 		if (open_pos[j].pos_purchase_order_id == po_multiSelect[x].value)
			 		{
			 			po_total = po_total + parseFloat(open_pos[j].received_total);
			 			invoice_total = invoice_total + parseFloat(open_pos[j].invoice_amount_applied);
			 			ordered_total = ordered_total + parseFloat(open_pos[j].ordered_total)
			 		}
			 	}
			 }
		}
	 }
	document.getElementById('does_not_matter').value = po_total;
	document.getElementById('invoice_applied_total').value = invoice_total;
	document.getElementById('total_ordered').value = ordered_total;
}