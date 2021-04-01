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
					//console.log(data);
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
		LookUpBarcode(subid);
	}
}
