function addBarcodeButton()
{
	barcode_value = document.getElementById('barcode').value;
	LookUpBarcode(barcode_value);
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
function UpdateBrandData()
{
	pos_manufacturer_brand_id = document.getElementById('pos_manufacturer_brand_id_lookup').value;
	//need to get a list of style numbers
	var post_string = {};
	post_string['pos_manufacturer_brand_id'] = pos_manufacturer_brand_id;
	var url = POS_ENGINE_URL + '/products/AjaxProduct/lookup_product_style_numbers.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				//alert(response);
				//now we need to send this response out for processing...
				if (response == "No Data Found")
				{
					alert ('No Data Found');
				}
				else
				{
					//style_number_lookup
					//color_code_lookup
					//
					//we want to populate the style number select....
					var parsed_data = parseJSONdata(response);
					//console.log(parsed_data);
					if(typeof parsed_data[0]['style_number'] !== 'undefined')
					{
						updateProductSelectOptions('style_number_lookup', parsed_data[0]['style_number'], parsed_data[0]['style_number']);
					}
					else
					{
						document.getElementById('style_number_lookup').options.length = 0;
					}
					//updateSelectOptions('product_subid_manual_lookup', parsed_data[1]['product_subid_name'], parsed_data[1]['product_subid_name']);
					document.getElementById('color_code_lookup').options.length = 0;
					document.getElementById('size_lookup').options.length = 0;
					
				}
			}
			});



	//this will load the style numbers via ajaxery
}
function UpdateStyleData()
{
	pos_manufacturer_brand_id = document.getElementById('pos_manufacturer_brand_id_lookup').value;
	style_number = document.getElementById('style_number_lookup').value;
	//need to get a list of style numbers
	var post_string = {};
	post_string['pos_manufacturer_brand_id'] = pos_manufacturer_brand_id;
	post_string['style_number'] = style_number;
	var url = POS_ENGINE_URL + '/products/AjaxProduct/lookup_product_color_codes.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				//alert(response);
				//now we need to send this response out for processing...
				if (response == "No Data Found")
				{
					alert ('No Data Found');
				}
				else
				{
					//style_number_lookup
					//
					//
					//we want to populate the style number select....
					var parsed_data = parseJSONdata(response);
					//console.log(parsed_data);
					if(typeof parsed_data[0]['code_name'] !== 'undefined')
					{
						updateProductSelectOptions('color_code_lookup', parsed_data[0]['pos_product_option_id'], parsed_data[0]['code_name']);
					}
					else
					{
						document.getElementById('color_code_lookup').options.length = 0;
					}
					//updateSelectOptions('product_subid_manual_lookup', parsed_data[1]['product_subid_name'], parsed_data[1]['product_subid_name']);
					document.getElementById('size_lookup').options.length = 0;

					
				}
			}
			});
}
function UpdateColorCodeData()
{
	pos_manufacturer_brand_id = document.getElementById('pos_manufacturer_brand_id_lookup').value;
	style_number = document.getElementById('style_number_lookup').value;
	pos_product_option_id = document.getElementById('color_code_lookup').value;
	var post_string = {};
	post_string['pos_manufacturer_brand_id'] = pos_manufacturer_brand_id;
	post_string['style_number'] = style_number;
	post_string['pos_product_option_id'] = pos_product_option_id;
	var url = POS_ENGINE_URL + '/products/AjaxProduct/lookup_product_sizes.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log(response);
				//now we need to send this response out for processing...
				if (response == "No Data Found")
				{
					alert ('No Data Found');
				}
				else
				{
					//style_number_lookup
					//
					//
					//we want to populate the style number select....
					var parsed_data = parseJSONdata(response);
					//console.log(parsed_data);

					updateProductSelectOptions('size_lookup', parsed_data['pos_product_option_id'], parsed_data['option_name']);
					//updateSelectOptions('product_subid_manual_lookup', parsed_data[1]['product_subid_name'], parsed_data[1]['product_subid_name']);
					
				}
			}
			});
}
function UpdateSizeData()
{
	/*pos_manufacturer_brand_id = document.getElementById('pos_manufacturer_brand_id_lookup').value;
	style_number = document.getElementById('style_number_lookup').value;
	color_code = document.getElementById('color_code_lookup').value;
	size = document.getElementById('size_lookup').value;
	var post_string = {};
	post_string['pos_manufacturer_brand_id'] = pos_manufacturer_brand_id;
	post_string['style_number'] = style_number;
	post_string['color_code'] = color_code;
	post_string['size'] = size;
	var url = 'lookup_product_sub_id.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log(response);
				//now we need to send this response out for processing...
				if (response == "No Data Found")
				{
					alert ('No Data Found');
				}
				else
				{
					//style_number_lookup
					//
					//
					//we want to populate the style number select....
					var parsed_data = parseJSONdata(response);
					console.log(parsed_data);

					updateSelectOptions('product_subid_manual_lookup', parsed_data[0]['product_subid_name'], parsed_data[0]['product_subid_name']);
					
				}
			}
			});*/
}
function updateProductSelectOptions(id, values, names)
{
	element = document.getElementById(id);	
	element.options.length = 0;
	//clearSelect(id);
	option = document.createElement('option');
	option.value = 'false..';
	option.appendChild(document.createTextNode('Select..'));
	element.appendChild(option);
	for(row=0;row<values.length;row++)
	{
		option = document.createElement('option');
		option.value = values[row];
		option.appendChild(document.createTextNode(names[row]));
		element.appendChild(option);	
	}
}
function addProductSubId()
{
	//repeat of barcode function
	pos_manufacturer_brand_id = document.getElementById('pos_manufacturer_brand_id_lookup').value;
	style_number = document.getElementById('style_number_lookup').value;
	pos_color_option_id = document.getElementById('color_code_lookup').value;
	pos_size_option_id = document.getElementById('size_lookup').value;
	var post_string = {};
	post_string['pos_manufacturer_brand_id'] = pos_manufacturer_brand_id;
	post_string['style_number'] = style_number;
	post_string['pos_color_option_id'] = pos_color_option_id;
	post_string['pos_size_option_id'] = pos_size_option_id;
	//barcode = pos_manufacturer_brand_id . '::' . style_number . '::' . color_code . '::' . size;
	var url = POS_ENGINE_URL + '/products/AjaxProduct/lookup_product_sub_id.php';
	//var url = POS_ENGINE_URL + '/products/AjaxProduct/barcode.php';
	var ajax_request = $.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true
			});
		
		ajax_request.success(function(response) 
		{
  				console.log(response);
				//now we need to send this response out for processing...
				if (response == "No Data Found")
				{
					alert ('No Data Found');
				}
				else
				{
					//style_number_lookup
					//
					//
					//we want to populate the style number select....
					var parsed_data = parseJSONdata(response);
					//console.log(parsed_data);
					
					//now we need to send this response out for processing...
					if (response == "No Data Found For Barcode")
					{
						PlaySoundV3(ERROR_BEEP_FILENAME);
						alert ('No Data Found for ' + control.value);
					}
					else
					{
						
						
						//PlaySoundV3(SUCCESS_BEEP_FILENAME);

    					//parse it here:
    					var parsed_data = parseJSONdata(response);
    					//console.log(parsed_data);
   						barcode_control = document.getElementById('barcode');
   						barcode_control.value = parsed_data['product_subid_name'];
	  					LookUpBarcode(parsed_data['product_subid_name']);

    					

					}
				}
		});
	
	
	
}


