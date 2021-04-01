function updateOptionCodeData(control)
{
	
	copyHTMLTableDataToArray();
	row = getCurrentRow(control);
	if(control.value == 'NULL')
	{
		//remove the select options....
		//element = document.getElementById(id);	
		//element.options.length = 0;
		//re-create the array with no options...
		item = {};
		item['options'] = {};
		item['pos_product_option_id']='Does not matter, but I need this db_filed';
		item['options']['names'] = [];
		item['options']['values'] = [];
		updateItemDataInTableArray(item, row);
	}
	else
	{
		//we need to get the list of codes avaialable
		//to get the list available we need the product sub id
		//send back the attributes
		
		pos_product_attribute_id = control.value;
		//need to get a list of style numbers
		var post_string = {};
		post_string['pos_product_sub_id'] = pos_product_sub_id;
		post_string['pos_product_attribute_id'] = pos_product_attribute_id;
		var url = 'lookup_product_sub_id_options.php';
		$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					
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
	
						//now we need to update the table array and object
						//then we need to write the data....
						updateItemDataInTableArray(parsed_data, row);
						
						
						//updateIndividualSelectOptions(row, 'pos_product_option_id', parsed_data['pos_product_option_id'], parsed_data['option_code_name']);
	
						
						//document.getElementById('color_code_lookup').options.length = 0;
						//document.getElementById('size_lookup').options.length = 0;
						
					}
				}
				});
	}


	//this will load the style numbers via ajaxery
}

function updateIndividualSelectOptions(row, id, values, names)
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

