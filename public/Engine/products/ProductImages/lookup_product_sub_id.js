function UpdateBrandData()
{
	pos_manufacturer_brand_id = document.getElementById('pos_manufacturer_brand_id_lookup').value;
	//need to get a list of style numbers
	var post_string = {};
	post_string['pos_manufacturer_brand_id'] = pos_manufacturer_brand_id;
	var url = 'lookup_product_style_numbers.php';
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

					updateProductSelectOptions('style_number_lookup', parsed_data[0]['style_number'], parsed_data[0]['style_number']);
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
	var url = 'lookup_product_color_codes.php';
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

					updateProductSelectOptions('color_code_lookup', parsed_data[0]['pos_product_option_id'], parsed_data[0]['code_name']);
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
	var url = 'lookup_product_sizes.php';
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
	var url = 'lookup_product_sub_id.php';
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
					console.log(parsed_data);
					
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
						//console.log(parsed_data);
						parsed_data['need_a_label'] = 1;
						var row = addItemDataToTableArray(parsed_data);
						addItemDataToHTMLTable(parsed_data);
						//disableHTMLRow(row);
						//disable some cells
						//disableProductCells(row);
						

    	
    	
					}
				}
		});
	
	
	
}


