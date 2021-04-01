function updateTaxJurisdictionList(control, list_id)
{
	//first ajax the state_id
		var post_string = {};
		post_string['pos_state_id'] = trim(control.value);
		url = 'get_tax_jurisdictions.php';
		$.ajax({
	 			type: 'POST',
	  			url: url,
	  			data: post_string,
	 			async: false,
	  			success: 	function(response) 
	  			{
	  				//alert(response);
    				parsed_data = JSON && JSON.parse(response) || $.parseJSON(response);
    				updateSelect(list_id, parsed_data);
	  			}
				});
	//second get the new list values
	
	//third update the list
	
}
function updateSelect(select_id, values)
{
		select = document.getElementById(select_id);
		select.options.length=0;
		for (var key in values)
		{
			option = document.createElement('option');
			option.value = key;
			option.appendChild(document.createTextNode(values[key]));
			select.appendChild(option);
		}
}