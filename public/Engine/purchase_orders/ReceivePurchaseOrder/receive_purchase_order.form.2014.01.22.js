//recieve_purchase_order.form.js
//This file will help us get the upc codes into the system

//Need to set focus to

window.onload= init;
function init()
{
	document.getElementById('mfg_barcode').focus();
}

function lookUpBarcodeID(control, control_event)
{
	//alert(control_event.keyCode);
	if (control_event.keyCode == 13)
	{
		//return is pressed - do our thing..
		//control.value is our value
		//we need to compare to barcode values
		lookUpBarcode();
	}
	return !(window.event && window.event.keyCode == 13);
	
}
function lookUpBarcode()
{
	value = document.getElementById("mfg_barcode").value;
	var tbody = document.getElementById("receive_table");
		var tfoot = document.getElementById("receive_table_tfoot");
		var rowCount = tbody.rows.length;
		var found = 'false';
		//there might be more than one row with the same mfg id...
		var rows_found = 0;
		if (document.getElementById('remove').checked == true)
		{
			increment = -1;
		}
		else
		{
			increment = 1;
		}
		//check that the value of our barcode does not match thier barcode

		for(var i = 0;i<rowCount;i++)
		{
			if (value == tbody.rows[i].cells[mfgIdColumn].innerHTML || value == tbody.rows[i].cells[barcode_column].innerHTML)
			{
				rows_found = rows_found +1;
				if (tbody.rows[i].cells[check_in_column].childNodes[0].value >= parseInt(tbody.rows[i].cells[qty_ordered_column].innerHTML))
				{
					//do not add to this column row
				}
				else
				{
					
				}
			}
		}
		for(var i = 0;i<rowCount;i++)
		{
			if (value == tbody.rows[i].cells[mfgIdColumn].innerHTML || value == tbody.rows[i].cells[barcode_column].innerHTML)
			{

					
					tbody.rows[i].cells[check_in_column].childNodes[0].value = parseInt(tbody.rows[i].cells[check_in_column].childNodes[0].value) + increment;
					tbody.rows[i].cells[check_in_column].childNodes[0].style.backgroundColor="#00FF33";
					tfoot.rows[0].cells[check_in_column].innerHTML = calculateColumnTotal("receive_table", check_in_column);
				
				found = 'true';
				
				PlaySoundV3(SUCCESS_BEEP_FILENAME);
				break;
			}
			else
			{
				
				//tbody.rows[i].cells[check_in_column].childNodes[0].classList.remove('highlight_green');
				//tbody.rows[i].cells[check_in_column].childNodes[0].classList.add('highlight');
				tbody.rows[i].cells[check_in_column].childNodes[0].style.backgroundColor="yellow";
				
			}
		}
		if (found=='false')
		{
			PlaySoundV3(ERROR_BEEP_FILENAME);
			alert('Error - Item not found: ' + value);
			//document.getElementById("wrong_items_qty").value = parseInt(document.getElementById("wrong_items_qty").value) +1;	
			document.getElementById("wrong_items_comments").value = document.getElementById("wrong_items_comments").value + 
			'Wrong Item Received: UPC: ' + control.value + lookupUPC(pos_manufacturer_id, control.value) + '\n';
			//document.getElementById("ra_required").checked = true;
		}
		document.getElementById('mfg_barcode').focus();
    	document.getElementById('mfg_barcode').select();
}
function lookupUPC(pos_manufacturer_id, upc)
{
	var return_value;
		$.ajax({
	  type: 'GET',
	  url: "lookup_upc_code.php",
	  data: { pos_manufacturer_id:pos_manufacturer_id , upc:upc }
,
	  async: false,
	  success: 	function(response) {return_value = response}
	});
	return return_value;
}
function updateFooter()
{
	var tfoot = document.getElementById("receive_table_tfoot");
	tfoot.rows[0].cells[check_in_column].innerHTML = calculateColumnTotal("receive_table", check_in_column);
}
function ManualCountReceiveComplete()
{
	
	var tbody = document.getElementById("receive_table");
	var tfoot = document.getElementById("receive_table_tfoot");
	var rowCount = tbody.rows.length;
	for(var i = 0;i<rowCount;i++)
	{
		
	tbody.rows[i].cells[check_in_column].childNodes[0].value =
	 myParseInt(tbody.rows[i].cells[ordered_column].innerHTML)  - myParseInt(tbody.rows[i].cells[qty_canceled_column].innerHTML) - myParseInt(tbody.rows[i].cells[previously_received_column].innerHTML);
	 
	 tbody.rows[i].cells[check_in_column].childNodes[0].style.backgroundColor="#00FF33";
			
			
	}
	updateFooter();		
			
	
}
function CancelAllItems()
{
	var tbody = document.getElementById("receive_table");
	var tfoot = document.getElementById("receive_table_tfoot");
	var rowCount = tbody.rows.length;

	for(var i = 0;i<rowCount;i++)
	{
		
	tbody.rows[i].cells[check_in_column].childNodes[0].value = myParseInt(tbody.rows[i].cells[ordered_column].innerHTML) - myParseInt(tbody.rows[i].cells[received_column].innerHTML)-myParseInt(tbody.rows[i].cells[previously_canceled_column].innerHTML);		
			
			
	}
			
	
}
