function calculateTotals()
{
}
function validatePhotoForm()
{
	//if any of the tables are empty then generate an error

	var error = '';
	if(product_table_object.rowCount == 0)
	{
		error += 'Error: Product table has no products \r\n';
	}
	if(image_table_object.rowCount == 0)
	{
		error += 'Error: Image table has no images \r\n';
	}
	if (error == '')
    {
    	return true;
    }
    else
    {
    	alert(error);
    	return false;
    }
}

function LookUpBarcode(barcode_value)
{
			console.log("looking up bacode: " + barcode_value);
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
						//now we need to send this response out for processing...
						if (response == "No Data Found For Barcode")
						{
							PlaySoundV3(ERROR_BEEP_FILENAME);
							alert ('No Data Found for ' + barcode_value);
						}
						else
						{
							PlaySoundV3(SUCCESS_BEEP_FILENAME);
							barcode_control = document.getElementById('barcode');
	  						barcode_control.value = '';
	  						barcode_control.focus();
    						//barcode_control.select();
    						
							//parse it here:
							var parsed_data = parseJSONdata(response);
							console.log(parsed_data);
							row = product_table_object.addItemDataToTableObject(parsed_data);
    						product_table_object.addItemDataToHTMLTable(parsed_data);
    						//document.getElementsByName('image_name[]')[row-1].focus();
							//console.log(parsed_data);
							
						}
					}
					});
}
function addBarcodeButton()
{
	barcode_control = document.getElementById('barcode');
	LookUpBarcode(barcode_control.value)
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