//window.onload=init_sales_invoice();
 var parsed_autocomplete_data;
 var search_type;
 var selected_autocomplete_index;
 var cust_table;

 $(function() 
 {
	$( "#gift-card-dialog-form" ).dialog(
	{
		autoOpen: false,
		height: 150,
		width: 300,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	assign_gift_card_value();
				}
			},
			Cancel: {
				text: 'Cancel',
				id: 'cancel-form-button',
				click: function() 
					{
						$( this ).dialog( "close" );
			
					}
			}
		},
		close: function() 
		{
			barcodeFocus();
		},
		open: function()
		{
			$('#submit-form-button').button('option', 'label', 'Submit');
			$('#submit-form-button').attr("disabled", false);
			$('#cancel-form-button').attr("disabled", false);
			$('#loading_image').hide();
			$('#gift_card_amount').val('0.00');
			$('#gift_card_amount').focus();
			$('#gift_card_amount').select();
		},
		timeout: 10000, // sets timeout to 3 seconds
		error: function()
		{
        // will fire when timeout is reached
        	alert('Error - Try again');
        	this.close();
    	}

	});
	$('#gift-card-dialog-form').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			  assign_gift_card_value();
		}
    });
    
    
    $( "#customer-select-dialog-form" ).dialog(
	{
		autoOpen: false,
		height: 200,
		width: 700,
		resizable: false,
		modal: true,
		buttons: 
		{
		
			Cancel: {
				text: 'Cancel',
				id: 'customer-select-cancel-form-button',
				click: function() 
					{
						$( this ).dialog( "close" );
			
					}
			}
		},
		close: function() 
		{
			barcodeFocus();

		},
		open: function()
		{
			$('#customer-select-cancel-form-button').attr("disabled", false);
			$('#customer_search_button').attr("disabled", false);

			//$("#customer-select-dialog-form").dialog("option", "height", 200);
			//$("#customer-select-dialog-form").dialog("option", "width", 1000);
			
			$('#customer_search').show();
			$('#customer_search_results').hide();
			$('#customer_loading_image').hide();
			
			if(pos_customer_id ==0)
			{
				$('#edit_customer').hide()
			}
			else
			{
				$('#edit_customer').show()
				$('#edit_customer').prop('value', 'Edit ' + customer_full_name)
				
			}
			$('#first_name_s').focus();
			$('#first_name_s').select();
		},
		timeout: 10000, // sets timeout to 3 seconds
		error: function()
		{
        // will fire when timeout is reached
        	alert('Error - Try again');
        	this.close();
    	}

	});
	$('#customer_search').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER  ) 
		{
			   customerSearch();	 
		}

    });
    
    $( "#cust_add_edit_form" ).dialog(
	{
		autoOpen: false,
		height: 200,
		width: 400,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'customer-select-form-button',
				click: function() 
				{	
				 	
				 	 add_edit_customer();	
				}
			},
			Cancel: {
				text: 'Cancel',
				id: 'customer-select-cancel-form-button',
				click: function() 
					{
						$( this ).dialog( "close" );
			
					}
			}
		},
		close: function() 
		{
			barcodeFocus();

		},
		open: function()
		{
			
		
			$('#customer_ae_loading_image').hide();
			
			$('#first_name_a').focus();
			$('#first_name_a').select();
		},
		timeout: 10000, // sets timeout to 3 seconds
		error: function()
		{
        // will fire when timeout is reached
        	alert('Error - Try again');
        	this.close();
    	}

	});

	$('#cust_add_edit_form').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER ) 
		{
			 e.preventDefault();
			  	 
		}
    });
  	$( "#customer-address-dialog-form" ).dialog(
	{
		autoOpen: false,
		height: 200,
		width: 700,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'address-select-form-button',
				click: function() 
				{	
				 	
				 	 submitAddressChange();	
				}
			},
			Cancel: {
				text: 'Cancel',
				id: 'customer-select-cancel-form-button',
				click: function() 
					{
						$( this ).dialog( "close" );
			
					}
			}
		},
		close: function() 
		{
			barcodeFocus();

		},
		open: function()
		{
			$('#add_edit_address').show();
			$('#address_loading_image').hide();
			
			$('#address1_a').focus();
			$('#address1_a').select();
		},
		timeout: 10000, // sets timeout to 3 seconds
		error: function()
		{
        // will fire when timeout is reached
        	alert('Error - Try again');
        	this.close();
    	}

	});

	$('#customer-address-dialog-form').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			   e.preventDefault();	 
		}
    });


    $( "#customer-deposit-dialog-form" ).dialog(
	{
		autoOpen: false,
		height: 150,
		width: 300,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	
				 	 add_customer_deposit();	
				}
			},
			Cancel: {
				text: 'Cancel',
				id: 'cancel-form-button',
				click: function() 
					{
						$( this ).dialog( "close" );
			
					}
			}
		},
		close: function() 
		{
			barcodeFocus();
	
		},
		open: function()
		{
			$('#submit-form-button').button('option', 'label', 'Submit');
			$('#submit-form-button').attr("disabled", false);
			$('#cancel-form-button').attr("disabled", false);
			$("#customer-deposit-dialog-form").dialog("option", "height", 150);

			
			$('#deposit_loading_image').hide();
			$('#deposit_amount').val('0.00');
			$('#deposit_amount').focus();
			$('#deposit_amount').select();
		},
		timeout: 10000, // sets timeout to 3 seconds
		error: function()
		{
        // will fire when timeout is reached
        	alert('Error - Try again');
        	this.close();
    	}

	});
	$('#customer-deposit-dialog-form').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER && $('#deposit_amount').val() > 0) 
		{
			   add_customer_deposit();	
		}
    });
    
	
	$( "#returnsInvoiceEntryModalForm" ).dialog(
	{
		autoOpen: false,
		height: 300,
		width: 400,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'invoice_submit-form-button',
				click: function() 
				{	
				 	lookupInvoiceData($('#pos_return_sales_invoice_id').val());
				}
			},
			Cancel: {
				text: 'Cancel',
				id: 'invoice_cancel-form-button',
				click: function() 
					{
						$( this ).dialog( "close" );
			
					}
			}
		},
		close: function() 
		{
					barcodeFocus();

		},
		open: function()
		{
			$('#submit-form-button').button('option', 'label', 'Submit');
			$('#submit-form-button').attr("disabled", false);
			$('#cancel-form-button').attr("disabled", false);
			
			$('#return_invoice_id').show();
			$('#pos_return_sales_invoice_id').focus();
			$('#pos_return_sales_invoice_id').select();
			
			$('#return_invoice_lookup').hide();
			$('#returns_invoice_loading_image').hide();
			$('#return_invoice_CC_lookup').hide();

			$('#return_invoice_barcode_lookup').hide();
			$('#pos_return_sales_invoice_id').val('');
			search_type = 'INVOICE';
			
			
		},
		timeout: 10000, // sets timeout to 3 seconds
		error: function()
		{
        // will fire when timeout is reached
        	alert('Error - Try again');
        	this.close();
    	}

	});
	$('#returnsInvoiceEntryModalForm').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			  lookupInvoiceData($('#pos_return_sales_invoice_id').val());
		}
    });
	
	$('#invoice_search').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			  //do nothing
			  e.preventDefault();
		}
    });
	
	$( "#returnsInvoiceSelectModalForm" ).dialog(
	{
		autoOpen: false,
		height: 400,
		width: 600,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	lookUpInvoiceContents();
				}
			},
			Cancel: {
				text: 'Cancel',
				id: 'cancel-form-button',
				click: function() 
					{
						$( this ).dialog( "close" );
			
					}
			}
		},
		close: function() 
		{
			
		},
		open: function()
		{
			$('#submit-form-button').button('option', 'label', 'Submit');
			$('#submit-form-button').attr("disabled", false);
			$('#cancel-form-button').attr("disabled", false);
			
			$('#return_invoice_lookup').show();
			$('#invoice_number').focus();
			$('#invoice_number').select();
			
			$('#returns_loading_image').hide();
			$('#return_content_select').hide();
			
			
		},
		timeout: 10000, // sets timeout to 3 seconds
		error: function()
		{
        // will fire when timeout is reached
        	alert('Error - Try again');
        	this.close();
    	}

	});
	
	$( "#returnsProductSelectModalForm" ).dialog(
	{
		autoOpen: false,
		height: 400,
		width: 600,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	insertReturnContentsToInvoice();
				}
			},
			Cancel: {
				text: 'Cancel',
				id: 'cancel-form-button',
				click: function() 
					{
						$( this ).dialog( "close" );
			
					}
			}
		},
		close: function() 
		{
			
		},
		open: function()
		{

			
			
			
			
		},
		timeout: 10000, // sets timeout to 3 seconds
		error: function()
		{
        // will fire when timeout is reached
        	alert('Error - Try again');
        	this.close();
    	}

	});
	
	
	
	$('#product_search').bind('autocompleteopen', function(event, ui) {
    $(this).data('is_open',true);});
    
	//this is how to find out if the autocomplet is open, important for the next function
	$('#product_search').bind('autocompleteclose', function(event, ui) {
		$(this).data('is_open',false);
	});



	$( "#product_search" )
	// don't navigate away from the field on tab when selecting an item
	.bind( "keydown", function( event ) 
	{
		//what is this for ?
		if ( event.keyCode === $.ui.keyCode.TAB && $(this).data('is_open') ) 
		{
			event.preventDefault();
		}
		if ( event.keyCode === $.ui.keyCode.ENTER  && !$(this).data('is_open')) 
		{
			addSubidFromSearch();
		}
	})
	.autocomplete({
		source: function( request, response ) 
		{
		 $.ajax(
		 {
				url: "retail_sales_invoice.ajax.php",
				type: 'GET',
				async: true,
				data: 
				{
					ajax_request: 'PRODUCT_SEARCH',
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
	

	
	

	init();	
	
	
	

});
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% CUSTOMER STUFF ********************************************
function init()
{
	//console.clear();
	//console.log('customer');
	//console.log(pos_customer_id);
	//	console.log(pos_customer_id);
	if(pos_address_id == 0)
	{
		pos_address_id = 'false';
	}
    if(pos_customer_id == 0)
    {
    	$('#customer_here').hide();
    	$('#customer_not_here').show();
    }
    else
    {
    	$('#customer_here').show();
    	$('#customer_not_here').hide();
    }
   // console.log(type);
    if(type =='edit')
	{
		if(select_customer)
		{
			//$( "#customer-select-dialog-form" ).dialog( "option", "autoOpen", true );
			 open_customer();
		}
		else if(pos_sales_return_id != 0)
		{
			//we want to get the return dialog opened....
			createInvoiceReturnTable(returns_data);
		}
		else
		{
			barcodeFocus();
		}
		calculateTotals();
		
		//disableColumns();
		
	}
	else
	{
		//disable the email, phone and address inputs
		$("#email1").prop('disabled', true).addClass("readonly");
		$("#phone").prop('disabled', true).addClass("readonly");
		$("#pos_address_id").prop('disabled', true).addClass("readonly");
		
	}
   
}
function editAddress(pos_address_id)
{
	if(pos_address_id == 'false')
	{
		alert('Duh-ya.. select an address first');
		$('#pos_address_id').val('false');
	}
	else
	{
		$('#customer-address-dialog-form').dialog('open');	
		var ar = 0;
		for(var i=0;i<addresses.length;i++)
		{
			if(addresses[i]['pos_address_id'] == pos_address_id)
			{
				ar = i;
			}
		}
		$('#address1_a').val(addresses[ar]['address1']);
		$('#address2_a').val(addresses[ar]['address2']);
		$('#city_a').val(addresses[ar]['city']);
		$('#pos_state_id').val(addresses[ar]['pos_state_id']);
		$('#zip_a').val(addresses[ar]['zip']);
	}
	
	
}
function addAddress()
{
	$('#customer-address-dialog-form').dialog('open');	

	
	$('#address1_a').val('');
	$('#address2_a').val('');
	$('#city_a').val('');
	$('#pos_state_id').val('false');
	$('#zip_a').val('');
}
function changeAddress(control)
{
	if(control.value == 'add')
	{
		addAddress();
	}
	else if (control.value == 'edit')
	{
		editAddress(pos_address_id);

	}
	else if (control.value == 'false')
	{
		pos_address_id = 'false';
		//editAddress(pos_address_id);

	}
	else
	{
		pos_address_id = $('#pos_address_id').val();
		//now go through each row and update tax if shipped...
	
		updateAllShippedRowTax();
		
		
		
		
		
	}
	calculateTotals();
	console.log('current address3: ' + pos_address_id);
	barcodeFocus();
}

function submitAddressChange()
{
	$('#add_edit_address').hide();
	$('#address_loading_image').show();
	var post_string = {};
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	post_string['ajax_request'] = 'ADDRESS_ADD_EDIT';
	//if this one has a value then we are looking up the invoice id...
	//these ones will return a search result...
	post_string['pos_customer_id'] = pos_customer_id;
	post_string['pos_address_id'] = pos_address_id;
	post_string['address1'] = $('#address1_a').val();
	post_string['address2'] = $('#address2_a').val();
	post_string['city'] = $('#city_a').val();
	post_string['pos_state_id'] = $('#pos_state_id').val();
	post_string['zip'] = $('#zip_a').val();
	console.log(pos_address_id);
	console.log(post_string);
	var url = 'retail_sales_invoice.ajax.php';
	$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
				
					console.log(response);
					var parsed_data = parseJSONdata(response);
					
					$('#customer-address-dialog-form').dialog('close');	
					
					
					//update the addresses
					addresses = parsed_data['addresses'];
					pos_address_id =  parsed_data['pos_address_id'];
					//select the new one...
					updateAddresses();
					$('#pos_address_id').val(pos_address_id);
					calculateTotals();
					barcodeFocus();

					
					
				
				}
			}
			);
}
function updateAddresses()
{
	var optionsAsString = '<option value="false">Select Address</option>';
	optionsAsString += '<option value="edit">Edit Address</option>';
	for(var i = 0; i < addresses.length; i++) 
	{
		optionsAsString += "<option value='" + addresses[i]['pos_address_id'] + "'>" + addresses[i]['full_address'] + "</option>";
	}
	optionsAsString += '<option value ="add" >Add a New Address</option>';
	$("#pos_address_id").find('option').remove().end().append($(optionsAsString));
}

function open_customer()
{
	$( "#customer-select-dialog-form" ).dialog( "open" );

}
function lookupCustomer()
{
	 $( "#customer-select-dialog-form" ).dialog( "open" );
}
function customerSearch()
{
	//now...
	//pretend we have data here, now create a table and stick the data to it...

	//$('#customer_search_button').attr("disabled", true);
	
	$('#customer_loading_image').show();
	$('#customer_search_results').hide();

	
	var post_string = {};
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	post_string['ajax_request'] = 'CUSTOMER_SEARCH';
	//if this one has a value then we are looking up the invoice id...
	//these ones will return a search result...
	post_string['first_name'] = $('#first_name_s').val();
	post_string['last_name'] = $('#last_name_s').val();
	post_string['email'] = $('#email1_s').val();
	post_string['phone'] = $('#phone_s').val();
	var url = 'retail_sales_invoice.ajax.php';
	$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					
					//here we would update the payment table - currently we will just refresh
			
					post_string = '';
					//console.log(response);
					var parsed_data = parseJSONdata(response);
					if(typeof parsed_data['error'] !== 'undefined')
					{
						//we have an error...
						alert(parsed_data['error']);
						
					}
					$('#customer_loading_image').hide();

					$( "#customer-select-dialog-form" ).dialog("close");
					

					$("#customer-select-dialog-form").dialog("option", "height", 400);
					$("#customer-select-dialog-form").dialog("option", "width", 1000);
					$( "#customer-select-dialog-form" ).dialog("open");
					$('#customer_search_results').show();
					$('#customer_search_button').attr("disabled", false);					
					
					
					
					
					//creating a table....in JAVASCRIPT!!!!
					//CustomerSearchColDef comes from php..	
					//declare the object globally. create the object. create / add the table html. initialize the table	
					//the table needs to go onto the div before we can init it		
					
					var cust_table_id = 'cust_table';
					cust_table = new dynamic_table_object_v3(cust_table_id, CustomerSearchColDef, parsed_data['data']);
					cust_table.addToDiv('customer_search_results');
					$('#customer_search_results').append('<p>Search Limited to 30 Results</p>');

					
					//cust_table.init();
					var rowclick = function(){customerSelect(this.rowIndex);};
					cust_table.setAllRowProps('onclick',rowclick );
					cust_table.returned_data = parsed_data;
					
					$('#' + cust_table_id).addClass("search_table");
					$('#' + cust_table_id).addClass(cust_table_id);

					
					
				}
				});
	post_string = '';
	
	
	
}
function selectNoCustomer()
{
	
	$('#customer_loading_image').show();	
	var post_string = {};
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	post_string['ajax_request'] = 'UPDATE_CUSTOMER';
	post_string['pos_customer_id'] = 0;
	var url = 'retail_sales_invoice.ajax.php';
	$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					
					//alert(response);
					$('#customer_here').hide();
					$('#customer_not_here').show();
					pos_customer_id = 0;
					customer_full_name = '';
					pos_address_id = 'false';
					$('#full_name').html('');
					$('#email1').val('');
					$('#phone').val('');
					$('#customer_loading_image').hide();
					$( "#customer-select-dialog-form" ).dialog( "close" );

					
				}
				});
	
	
	

}
function customerSelect(row)
{
	//console.log(cust_table.returned_data['data']);
	//console.log(cust_table.returned_data['data'][row-1]['addresses']);
	//now we need to set the select values???
	pos_customer_id = cust_table.tdo[row-1]['pos_customer_id']['data'];
	$('#customer_loading_image').show();	
	
	console.log('Customer selected: ' + cust_table.tdo[row-1]['full_name']['data']);

	var post_string = {};
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	post_string['ajax_request'] = 'UPDATE_CUSTOMER';
	post_string['pos_customer_id'] = pos_customer_id;
	var url = 'retail_sales_invoice.ajax.php';
	$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					
					$( "#customer-select-dialog-form" ).dialog( "close" );
					$("#customer-select-dialog-form").dialog("option", "height", 200);
					$("#customer-select-dialog-form").dialog("option", "width", 800);
					//console.log("selected Customer is.... " + cust_table.tdo[row-1]['pos_customer_id']['data']);
	
					customer_full_name = cust_table.tdo[row-1]['full_name']['data'];
					first_name = cust_table.tdo[row-1]['first_name']['data'];
					last_name = cust_table.tdo[row-1]['last_name']['data'];
					phone = cust_table.tdo[row-1]['phone']['data'];
					email1 = cust_table.tdo[row-1]['email1']['data'];
					addresses = cust_table.returned_data['data'][row-1]['addresses'];
	
					$('#full_name').html(customer_full_name);
					$('#email1').val(email1);
					$('#phone').val(phone);
	
					$('#first_name_a').val(first_name);
					$('#last_name_a').val(last_name);
					$('#email1_a').val(email1);
					$('#phone_a').val(phone);
	
	
					updateAddresses();
					
					
				
					$('#customer_loading_image').hide();
					$( "#customer-select-dialog-form" ).dialog( "close" );
					$('#customer_here').show();
					//$('#customer_here').effect('highlight', {}, 1000);
					$('#customer_here').effect('highlight', {}, 1000).animate({'background-color':'rgb(0,255,0)'},500).animate({'background-color':'rgb(225,225,225)'},500);
					//$('#customer_here').toggleClass('cust_green', 1000);
					//$("#customer_here").fadeOut(1600, "linear", function (){$("#customer_here").show()});
					$('#customer_not_here').hide();
					
				}
				});
	
	

	
	
	

	
	
	
	//address? $('#full_name').val(cust_table.tdo[row-1]['full_name']['data']);
	
}
function addCustomer()
{
	$('#first_name_a').val($('#first_name_s').val());
	$('#last_name_a').val($('#last_name_s').val());
	$('#email1_a').val($('#email1_s').val());
	$('#phone_a').val($('#phone_s').val());
	
	$( "#customer-select-dialog-form" ).dialog( "close" );
	$( "#cust_add_edit_form" ).dialog( "open" );
	
}
function editCustomer()
{
	$( "#customer-select-dialog-form" ).dialog( "close" );
	$( "#cust_add_edit_form" ).dialog( "open" );
}
function add_edit_customer()
{

	$('#customer_ae_loading_image').show();
	$('#add_edit_customer').hide();
	
	$('#full_name').html($('#first_name_a').val()+' ' +$('#last_name_a').val());
	$('#email1').val($('#email1_a').val());
	$('#phone').val($('#phone_a').val());
	
	var post_string = {};
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	post_string['ajax_request'] = 'CUSTOMER_ADD_EDIT';
	//if this one has a value then we are looking up the invoice id...
	//these ones will return a search result..
	post_string['pos_customer_id'] = pos_customer_id;
	post_string['first_name'] = $('#first_name_a').val();
	post_string['last_name'] = $('#last_name_a').val();
	post_string['email'] = $('#email1_a').val();
	post_string['phone'] = $('#phone_a').val();
	console.log('post');
	console.log(post_string);
	var url = 'retail_sales_invoice.ajax.php';
	$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					
					console.log(response);			
					post_string = '';
					//var parsed_data = parseJSONdata(response);
					
					$('#customer_ae_loading_image').hide();
					
					$('#add_edit_customer').show();
					$( "#cust_add_edit_form" ).dialog("close");
					
					//now we need to add stuff.....
					pos_customer_id = response;
					$('#customer_here').show();
					//$('#customer_here').effect('highlight', {}, 1000);
					$('#customer_here').effect('highlight', {}, 1000).animate({'background-color':'rgb(0,255,0)'},500).animate({'background-color':'rgb(225,225,225)'},500);
					//$('#customer_here').toggleClass('cust_green', 1000);
					//$("#customer_here").fadeOut(1600, "linear", function (){$("#customer_here").show()});
					$('#customer_not_here').hide();
					
					
					
				}
				});

	
	//just need first name last name email phone?
	
}




//$$$$$$$$$$$$$$$$$$$$$$_____---------------__________RETURNS--------______________________>>>>>>>>>>>>>>>
function returns()
{
	/*
		user hits return
		modal form to select which invoice - look up invoice by name number barcode date etc
		AJAX user input
		User selects invoice or no invoice 
		invoice loads => check which items to return
		no invoice => enter -1 quantity in the invoice, code line red
		
		items load into invoice - lines are coded red
		original tax etc are all brought into the line
		the invoice_content_id is also brought => load into returned_invoice_content_id
		
		payment can then be found. track max cc_refundable?
		credit card return needs a transaction_id
		
		ex: bra returned -69.23
		find original invoice, get the transaction id.
		refund to the same card (we store the last 4).
		
		No original invoice, refund has to go to store credit.
	*/
		
		$( "#returnsInvoiceEntryModalForm" ).dialog( "open" );
		
}
function lookupInvoiceData(invoice)
{
	//get the data from the form...
	//set form to pinwheel
	//Ajax it over...
	//bring back the data, set the contents of the form to the data....
	
	//if the invoice id is present we do one thing, otherwise we are doing a search....
	
	
	$('#invoice_submit-form-button').attr("disabled", true);
	$('#invoice_cancel-form-button').attr("disabled", true);
	
	$('#return_invoice_id').hide();
	$('#return_invoice_lookup').hide();
	$('#return_invoice_CC_lookup').hide();
	$('#return_invoice_barcode_lookup').hide();
	$('#returns_invoice_loading_image').show();
	
	var post_string = {};
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	post_string['ajax_request'] = 'RETURN_INVOICE_LOOKUP';
	//if this one has a value then we are looking up the invoice id...
	//these ones will return a search result...
	post_string['pos_return_sales_invoice_id'] = invoice;
	post_string['first_name'] = $('#return_first_name').val();
	post_string['last_name'] = $('#return_last_name').val();
	post_string['email'] = $('#return_email').val();
	post_string['phone'] = $('#return_phone').val();
	post_string['return_barcode'] = $('#return_barcode').val();
	post_string['return_cc'] = $('#return_cc').val();
	post_string['search_type'] = search_type;
	var url = 'retail_sales_invoice.ajax.php';
	$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					
					//here we would update the payment table - currently we will just refresh
			
					post_string = '';
// 					console.log();
					var parsed_data = parseJSONdata(response);
					createInvoiceReturnTable(parsed_data);
					
					

					
				
				}
				});
		post_string = '';
	
	

	
	
	
}
function createInvoiceReturnTable(parsed_data)
{
	console.log(parsed_data);
	if(typeof parsed_data['error'] !== 'undefined')
	{
		//we have an error...
		alert(parsed_data['error']);
		//close the diaolog...
		//which dialog?
		$( "#returnsInvoiceEntryModalForm" ).dialog( "close" );
	
	}
	if(typeof parsed_data['invoice_contents'] !== 'undefined')
	{
		//console.log(parsed_data['invoice_contents']);
		//We have invoice contents... 
		// we loose this data when we leave this function, 
		// we need to store it somewhere....
		// we also need to store the invoice id somewhere....
		// sounds like we want it in returnsContentTable?
		//although php created the table we basically need to re-create it..
		//the table code will be lingering no matter what...
	
	
		$('#returnsContentsInvoiceNumberDiv').html('<p>Contents for Invoice Number ' + parsed_data['pos_return_sales_invoice_id'] + '</p>');
		returnsContentTable.initilizeHTMLTable();
	
	
		returnsContentTable.return_data = parsed_data;
		$( "#returnsInvoiceEntryModalForm" ).dialog( "close" );
		$( "#returnsProductSelectModalForm" ).dialog( "open" );
		//console.log(parsed_data['invoice_contents'].length);
		for(var row=0;row<parsed_data['invoice_contents'].length; row++)
		{
			//console.log(parsed_data['invoice_contents'][row]);
			returnsContentTable.addItemToTable(parsed_data['invoice_contents'][row]);
		}
		//returnsContentTable.setFocus(0,1); or...
	
		returnsContentTable.setFocus(0,'return_quantity');
	
	}
	else if(typeof parsed_data['invoices'] !== 'undefined')
	{
	
		//show the invoice table
		returnsInvoicesTable.initilizeHTMLTable();
		returnsInvoicesTable.return_data = parsed_data;
	
		$( "#returnsInvoiceEntryModalForm" ).dialog( "close" );
		$( "#returnsInvoiceSelectModalForm" ).dialog( "open" );
		$('#returnsInvoicesLimitsDiv').html(parsed_data['limits']);
		//console.log(parsed_data['invoice_contents'].length);
		for(var row=0;row<parsed_data['invoices'].length; row++)
		{
			//console.log(parsed_data['invoice_contents'][row]);
			returnsInvoicesTable.addItemToTable(parsed_data['invoices'][row]);
		}
		//returnsContentTable.setFocus(0,1); or...		
		//returnsInvoicesTable.setFocus(0,'return_quantity');	
	}
}
function lookUpInvoiceContents()
{
	//find the checked invoice, then go get her...
	var checked_rows = returnsInvoicesTable.findCheckedRows();
	
	//console.log(checked_rows);
	for(var row=0;row<checked_rows.length;row++)
	{
		the_invoice_is = returnsInvoicesTable.tdo[checked_rows[0]]['pos_return_invoice_id']['data'];
	}
	$( "#returnsInvoiceSelectModalForm" ).dialog( "close" );
	search_type = 'INVOICE';
	lookupInvoiceData(the_invoice_is);
}
function insertReturnContentsToInvoice()
{
	returnsContentTable.copyHTMLTableDataToObject();
	//console.log(returnsContentTable.return_data);

	
	//now.... for each item we need to add it....
	//however we need to add original stuff back in, not the current.....
	//console.log(returnsContentTable.rowCount);
	for(var row=0;row<returnsContentTable.tdo.length;row++)
	{
		if(returnsContentTable.tdo[row]['return_quantity']['data']>0)
		{
			if(returnsContentTable.tdo[row]['return_quantity']['data']<=returnsContentTable.return_data['invoice_contents'][row]['quantity'])
			{
				returnsContentTable.return_data['invoice_contents'][row]['quantity'] = -returnsContentTable.tdo[row]['return_quantity']['data'];
		returnsContentTable.return_data['invoice_contents'][row]['pos_return_content_id'] = returnsContentTable.return_data['invoice_contents'][row]['pos_sales_invoice_content_id'];
		
				returnsContentTable.return_data['invoice_contents'][row]['content_type'] = 'PRODUCT';
				var rp = invoice_table.addItemToTable(returnsContentTable.return_data['invoice_contents'][row]);
		
				invoice_table.setRowProp(rp,'className', '"return_row"');
				console.log(invoice_table.tdo);
		
		
			}
		}
		
		
	}

		
	
	$( "#returnsProductSelectModalForm" ).dialog( "close" );
	calculateTotals();
	barcodeFocus();
	
}
function priceAdjustInvoice()
{
	//here we return all then load all back in..
	for(var row=0;row<returnsContentTable.tdo.length;row++)
	{
		returnsContentTable.return_data['invoice_contents'][row]['quantity'] = -returnsContentTable.return_data['invoice_contents'][row]['quantity'];
		returnsContentTable.return_data['invoice_contents'][row]['pos_return_content_id'] = returnsContentTable.return_data['invoice_contents'][row]['pos_sales_invoice_content_id'];
		
		returnsContentTable.return_data['invoice_contents'][row]['content_type'] = 'PRODUCT';
		
		invoice_table.addItemToTable(returnsContentTable.return_data['invoice_contents'][row]);
		
		
		
		returnsContentTable.return_data['invoice_contents'][row]['quantity'] = -returnsContentTable.return_data['invoice_contents'][row]['quantity'];
		returnsContentTable.return_data['invoice_contents'][row]['pos_return_content_id'] = 0;
		
		returnsContentTable.return_data['invoice_contents'][row]['content_type'] = 'PRODUCT';
		
		invoice_table.addItemToTable(returnsContentTable.return_data['invoice_contents'][row]);
		
		
		
		
		
	}
	//now load....
	for(var row=0;row<returnsContentTable.tdo.length;row++)
	{
		
		
	}
	calculateTotals();
	$( "#returnsProductSelectModalForm" ).dialog( "close" );
	barcodeFocus();
	
	
}
function invoice_search()
{
	$('#return_invoice_id').show();
	$("#returnsInvoiceEntryModalForm").dialog("option", "height", 300);
	$('#return_invoice_lookup').hide();
	$('#return_invoice_CC_lookup').hide();
	$('#return_cc').val('');
	$('#return_barcode').val('');
	$('#return_invoice_barcode_lookup').hide();

	$('#pos_return_sales_invoice_id').focus();
	$('#pos_return_sales_invoice_id').select();
	search_type = 'INVOICE';
}
function cust_search()
{
	$('#return_invoice_id').hide();
	$('#return_invoice_lookup').show();
	$("#returnsInvoiceEntryModalForm").dialog("option", "height", 200);
	
	
	//$('#invoice_search').prop('value', 'Enter Invoice ID');
	$('#pos_return_sales_invoice_id').val('');
	search_type = 'CUSTOMER';
	$('#return_first_name').val('');
	$('#return_last_name').val('');
	$('#return_phone').val('');
	$('#return_email').val('');
	$('#return_first_name').focus();
	$('#return_first_name').select();
}
function cc_search()
{
	$('#return_invoice_id').hide();
	$('#return_invoice_CC_lookup').show();
	$('#return_cc').val('');
	$('#return_cc').focus();
	$('#return_cc').select();
	$("#returnsInvoiceEntryModalForm").dialog("option", "height", 200);
	//$('#invoice_search').prop('value', 'Enter Invoice ID');
	$('#pos_return_sales_invoice_id').val('');
	search_type = 'CC';
}
function product_search()
{
	$('#return_invoice_id').hide();
	$('#return_invoice_barcode_lookup').show();
	$('#return_barcode').val('');
	$('#return_barcode').focus();
	$('#return_barcode').select();
	$("#returnsInvoiceEntryModalForm").dialog("option", "height", 200);
	search_type = 'PRODUCT';
	$('#pos_return_sales_invoice_id').val('');	
}
function assign_gift_card_value()
{
    
   	//not sure what the current row is....
	row = invoice_table.tdo.length-1;
   	// alert(myParseFloat($('#gift_card_amount').val()));
    invoice_table.tdo[row]['retail_price']['data'] = myParseFloat($('#gift_card_amount').val());
    //console.log(invoice_table.tdo['retail_price']);
    $( '#gift-card-dialog-form' ).dialog( "close" );
    invoice_table.writeObjectToHTMLTable();
    calculateTotals();
    barcodeFocus();
	
   
    
}
function cusomter_deposit()
{
	
	//customer deposit button has been pressed...
	invoice_table.copy();
	
	//pop up the gift card modal form...

	
	$( '#customer-deposit-dialog-form' ).dialog( "open" );
	
	
	
}
function add_customer_deposit()
{

	$("#customer-deposit-dialog-form").dialog("option", "height", 300);
	$('#deposit_loading_image').show();

	var post_string = {};
	//post_string['barcode'] = trim(barcode_value);
	post_string['ajax_request'] = 'CUSTOMER_DEPOSIT';
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	var barcode_url = 'retail_sales_invoice.ajax.php';
	$.ajax({
			type: 'POST',
			url: barcode_url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log(response);
				//now we need to send this response out for processing...
				var parsed_data = parseJSONdata(response);
				if (typeof parsed_data['error'] !== "undefined")
				{
					PlaySoundV3(ERROR_BEEP_FILENAME);
					alert (barcode_value + ' : ' + parsed_data['error']);
					barcode_control = document.getElementById('barcode');
					barcodeFocus();
				}
				else
				{
					PlaySoundV3(SUCCESS_BEEP_FILENAME);
					
					card_found = false;
					//make sure the card number is not on another row.
					for(var row=0;row<invoice_table.tdo.length;row++)
					{
						if(invoice_table.tdo[row]['barcode']['data'] ==parsed_data['card_number'])
						{
							//problem
							card_found = true;
							alert('Card Can Only be Used Once');
							
						} 
					}
					if(card_found == false)
					{
						
						row = invoice_table.addItemToTable(parsed_data);
						invoice_table.tdo[row]['retail_price']['data'] = myParseFloat($('#deposit_amount').val());

						
						//alert ('gift card');
						//disable some cells
						if(parsed_data['barcode'] != '')
						{
							invoice_table.disableCell(row, 'barcode');
						}
						
						
												
	

						invoice_table.disableCell(row, 'quantity');
						invoice_table.disableCell(row, 'pos_discount_id');
						invoice_table.disableCell(row, 'pos_sales_tax_category_id');
						invoice_table.disableCell(row, 'discount');
						invoice_table.disableCell(row, 'sale_price');
						invoice_table.write();
						calculateTotals();
					
						barcodeFocus();		
				}
				}
				$('#deposit_loading_image').hide();
				$( '#customer-deposit-dialog-form' ).dialog( "close" );	
				if($('#create_order').prop('checked'))
				{
					$('#special_order').prop('checked', true);
					$('#follow_up').prop('checked', true);
				}
				barcodeFocus();
			}
	});
	
	
	
	
   
}
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



function enablePaidCheck(control)
{
	row = getCurrentRow(control);
	column = invoice_table.getHTMLColumnNumberFromTableDefColumnName('paid');
	invoice_table.tbody.rows[row].cells[column].childNodes[0].disabled = false;
}
function updateShipping(control)
{
}
function updateDiscount(control)
{
	//this is where we might want to load some info on the discount...
	//unlock the discount row
	row = getCurrentRow(control);
	column = invoice_table.getHTMLColumnNumberFromTableDefColumnName('discount');
	invoice_table.tbody.rows[row].cells[column].childNodes[0].readOnly = false;
	invoice_table.tbody.rows[row].cells[column].childNodes[0].className = 'nothing';
}


//send this to the payments code
function loadStoreCreditInfo(control)
{
	
	if (window.event.keyCode == 13)
    {
    	var post_string = {};
		post_string['card_number'] = trim(control.value);
		post_string['ajax_request'] = 'STORE_CREDIT';
		//post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
		var url = 'retail_sales_invoice.ajax.php';
		$.ajax({
	 			type: 'POST',
	  			url: url,
	  			data: post_string,
	 			async: true,
	  			success: 	function(response) 
	  			{
	  				alert(response);
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
    					//store_credit_table_object.addItemDataToTableArray(parsed_data);
    					//store_credit_table_object.addItemDataToHTMLTable(parsed_data);
    					
    					store_credit_table_object.updateItemDataInTableArray(parsed_data,getCurrentRow(control));
    					
    					calculateTotals();
	  				}
	  			}
				});
    }
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
function addBarcodeButton()
{
	
	barcode_control = document.getElementById('barcode');
	
	LookUpBarcode(barcode_control.value)
}
function LookUpBarcode(barcode_value)
{
		$('#barcode').val(barcode_value.toUpperCase());
		if($('#barcode').val() == 'DEP')
		{
			$( '#customer-deposit-dialog-form' ).dialog( "open" );
		}
		else
		{
		//return is pressed - do our thing..
		//control.value is our value
		invoice_table.copyHTMLTableDataToObject();
		var post_string = {};
		post_string['barcode'] = trim(barcode_value);
		post_string['ajax_request'] = 'BARCODE';
		post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
		var barcode_url = 'retail_sales_invoice.ajax.php';
		$.ajax({
	 			type: 'POST',
	  			url: barcode_url,
	  			data: post_string,
	 			async: true,
	  			success: 	function(response) 
	  			{
	  				console.log(response);
	  				//now we need to send this response out for processing...
	  				var parsed_data = parseJSONdata(response);
	  				//console.log(parsed_data);
	  				if (typeof parsed_data['error'] !== "undefined")
	  				{
	  					PlaySoundV3(ERROR_BEEP_FILENAME);
	  					alert (barcode_value + ' : ' + parsed_data['error']);
	  					barcode_control = document.getElementById('barcode');
	  					barcodeFocus();
	  				}
	  				else
	  				{
	  					PlaySoundV3(SUCCESS_BEEP_FILENAME);
	  					barcode_control = document.getElementById('barcode');
	  					barcodeFocus();
    					//parse it here:
    					
    					//console.log(parsed_data);
    					//this code should be modifiable per form....
    					
    					
    					//now if it is a gift card we need special processing....
    					if(parsed_data['content_type'] == 'CREDIT_CARD')
    					{
    						
    						barcode_control.value='';
    						barcodeFocus();
    						card_found = false;
    						//make sure the card number is not on another row.
    						for(row=0;row<invoice_table.tdo.length;row++)
    						{
    							if(invoice_table.tdo[row]['barcode']['data'] ==parsed_data['card_number'])
    							{
    								//problem
    								card_found = true;
    								alert('Card Can Only be Used Once');
    								
    							} 
    						}
    						if(card_found == false)
    						{
    							
    							invoice_table.addItemToTable(parsed_data);
    							row = invoice_table.tdo.length-1;
    							
    							//alert ('gift card');
    							//disable some cells
    							if(parsed_data['barcode'] != '')
    							{
    								invoice_table.disableCell(row, 'barcode');
    							}
    							invoice_table.disableCell(row, 'quantity');
    							invoice_table.disableCell(row, 'pos_discount_id');
    							invoice_table.disableCell(row, 'pos_sales_tax_category_id');
    							invoice_table.disableCell(row, 'discount');
    							invoice_table.disableCell(row, 'sale_price');
    							
    							$( '#gift-card-dialog-form' ).dialog( "open" );
    							calculateTotals();
    							

    						}
    					}
    					else if(parsed_data['content_type'] == 'PRODUCT')
    					{
    						invoice_table.addItemToTable(parsed_data);
							calculateTotals();
    					}
    					else if(parsed_data['content_type'] == 'PROMOTION')
    					{
    						processPromotionCode(parsed_data);
    					}
    					else
    					{
    					}
	  				}
	  			}
	});
	}
}
function processPromotionCode(parsed_data)
{
	//check if the code is valid
	if(compareTwoDates(invoice_date, parsed_data['expiration_date']) > 0
		&& parsed_data['expired_value'] == 0)
	{
		//promotion is expired and the expired value is nothing, do not add
		alert('Expired with No Value');
	}
	else
	{
		if(compareTwoDates(invoice_date, parsed_data['expiration_date']) > 0
		&& parsed_data['expired_value'] > 0)
		{
			alert('Promotion value has expired, however an expired value can be applied');
		}
		
		//break the parsed data into an object then add the data to the tdo...
		
		
		var nrow = promotion_table.addItemToTable(parsed_data);
		
		
		//console.log(promotion_table.tdo);
		calculateTotals();
	}
}

function updateShippingTax()
{
	//shipping is taxed at the destination
	var rowCount = invoice_table.tdo.length;
	for(var row=0; row<rowCount; row++)
	{
		if(invoice_table.tdo[row]['content_type']['data'] == 'SHIPPING')
		{
			console.log('updating shipping tax....');
			updateTax(row);
		}
	}
}
function checkAndAddShipping(control)
{
	invoice_table.copyHTMLTableDataToObject();
	console.log('shipping...');
	//go through each line.... if there is no shipping add it
	if(control.checked)
	{
		var rowCount = invoice_table.tdo.length;
		var shipping_row = -1;
		for(var row=0; row<rowCount; row++)
		{
			if(invoice_table.tdo[row]['content_type']['data'] == 'SHIPPING')
			{
				shipping_row = row;
			}
		}
		if(shipping_row == -1)
		{
			invoice_table.copyHTMLTableDataToObject();
			var post_string = {};
			post_string['pos_address_id'] = pos_address_id;
			post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
			post_string['ajax_request'] = 'SHIPPING';
			var shipping_url = 'retail_sales_invoice.ajax.php';
			$.ajax({
					type: 'POST',
					url: shipping_url,
					data: post_string,
					async: true,
					success: 	function(response) 
					{
						console.log(response);
						//now we need to send this response out for processing...
						var parsed_data = parseJSONdata(response);
						if (typeof parsed_data['error'] !== "undefined")
						{
							PlaySoundV3(ERROR_BEEP_FILENAME);
							alert (parsed_data['error']);
						}
						else
						{
							//PlaySoundV3(SUCCESS_BEEP_FILENAME);
						
							//parse it here:
						
							//console.log(parsed_data);
							//this code should be modifiable per form....
						
						
							if(parsed_data['content_type'] == 'SHIPPING')
							{
								invoice_table.addItemToTable(parsed_data);
								row = invoice_table.tdo.length-1;
							/*	invoice_table.disableCell(row, 'special_order');
								invoice_table.disableCell(row, 'ship');
								invoice_table.disableCell(row, 'quantity');
								invoice_table.disableCell(row, 'pos_discount_id');
								invoice_table.disableCell(row, 'pos_sales_tax_category_id');
								invoice_table.disableCell(row, 'discount');
								invoice_table.disableCell(row, 'sale_price');*/
								disableColumns();
								calculateTotals();
							}

							else
							{
							}
						}
					}
			});
		
		
		}
		else
		{
			//shipping row is already on there
			calculateTotals();
		}
		//finally we need to get the tax updated....
		if(pos_address_id !='false')
		{
			var row = getCurrentRow(control);
			updateTax(row);
		}
	}
	//get the tax if the control is not checked OR the control is checked and the addrress is not false...
	else
	{
		//unchecked control... now we need to go get the tax..
		var row = getCurrentRow(control);
		updateTax(row);
		
	}

}
function updateTax(row)
{
	/* 
	
		to update the tax we need to know the tax jurisdiction and the tax category
		
	
	*/
	invoice_table.copyHTMLTableDataToObject();
	
	var ship = invoice_table.tdo[row]['ship']['data'];
	if(invoice_table.tdo[row]['content_type']['data'] == 'SHIPPING')
	{
		//ironically shipping is shipped according to tax....
		ship = 'true';
	}
	
	var pos_sales_tax_category_id = invoice_table.tdo[row]['pos_sales_tax_category_id']['data'];
	if (pos_sales_tax_category_id == 'NULL') pos_sales_tax_category_id = 0;
	var post_string = {};
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	post_string['pos_sales_tax_category_id'] = pos_sales_tax_category_id;
	post_string['pos_address_id'] = pos_address_id;
	post_string['ship'] = ship;
	post_string['ajax_request'] = 'GET_TAX_RATE';
	console.log('updating tax: ');
	console.log(post_string);
	
	url = 'retail_sales_invoice.ajax.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log('Tax return data....');
				console.log(response);	
				//now we need to send this response out for processing...
				 tax_data = parseJSONdata(response);
				 console.log(tax_data);	
				 //row = getCurrentRow(control);
				 invoice_table.updateItemDataInTableObject(tax_data['data'], row);
				//console.log(invoice_table.tdo);
				calculateTotals();
			}
			});
	

}
function updateAllShippedRowTax()
{
	if(pos_address_id != 'false')
	{
		for(var row=0; row<invoice_table.tdo.length; row++)
		{
		
			if(invoice_table.tdo[row]['ship']['data'] == true)
			{
				updateTax(row);
			}
		
		}
		updateShippingTax();
	}

}

function isGiftCard(row)
{
	if(invoice_table.tdo[row]['content_type']['data'] == 'CREDIT_CARD')
	{
		return true;
	}
	else
	{
		return false;
	}
}
function isShipping(row)
{
	if(invoice_table.tdo[row]['content_type']['data'] == 'SHIPPING')
	{
		return true;
	}
	else
	{
		return false;
	}
}
function isReturn(row)
{	
	quantity = myParseFloat(invoice_table.tdo[row]['quantity']['data']);
	if(quantity < 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function isSaleOrDiscounted(row)
{
	retail_price = myParseFloat(invoice_table.tdo[row]['retail_price']['data']);
	sale_price = myParseFloat(invoice_table.tdo[row]['sale_price']['data']);
	discount =  getDiscountValue(invoice_table.tdo[row]['discount']['data'],sale_price,row);
	sale_price = sale_price - discount;
	if(retail_price - sale_price < 0.00001)
	{
		//console.log("Full Price: " + retail_price + ' ' + sale_price);
		return false;
	}
	else
	{
		//console.log("Sale Price: " + retail_price + ' ' + sale_price);
		return true;
	}
}
function isClearance(row)
{
		if(invoice_table.tdo[row]['clearance'] == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
}
function isDiscounted(row)
{
	if (getDiscountValue(invoice_table.tdo[row]['discount']['data'],sale_price,row)>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function isProductFullPrice(row)
{
	sale_price = myParseFloat(invoice_table.tdo[row]['sale_price']['data']);
	full_price = myParseFloat(invoice_table.tdo[row]['retail_price']['data']);
	discount = getDiscountValue(invoice_table.tdo[row]['discount']['data'],sale_price,row);
	applied_instore_discount = myParseFloat(invoice_table.tdo[row]['applied_instore_discount']['data']);
	var line_item_total = (sale_price - discount) - applied_instore_discount;
	if(invoice_table.tdo[row]['content_type']['data'] == 'PRODUCT')
	{
		
		full_price = myParseFloat(invoice_table.tdo[row]['retail_price']['data']);
		sale_price = myParseFloat(invoice_table.tdo[row]['sale_price']['data']);
		discount = getDiscountValue(invoice_table.tdo[row]['discount']['data'],sale_price,row);
		applied_instore_discount = myParseFloat(invoice_table.tdo[row]['applied_instore_discount']['data']);
		var line_item_total = (sale_price - discount) - applied_instore_discount;
		if(full_price - line_item_total < 0.00001)
		{
		 return true;
		 }
		 else
		 {
		 	return false;
		 }
	}
	else
	{
		return false;
	}
	
}
function isProduct(row)
{
	if(invoice_table.tdo[row]['content_type']['data'] == 'PRODUCT')
	{
		return true;
	}
	else
	{
		return false;
	}
}
function isService(row)
{
	if(invoice_table.tdo[row]['content_type']['data'] == 'SERVICE')
	{
		return true;
	}
	else
	{
		return false;
	}
}
function isProductOrService(row)
{
	if(invoice_table.tdo[row]['content_type']['data'] == 'PRODUCT' || invoice_table.tdo[row]['content_type']['data'] == 'SERVICE')
	{
		return true;
	}
	else
	{
		return false;
	}
}
function getItemPrice(row)
{
	sale_price = invoice_table.tdo[row]['sale_price']['data'];
	price = myParseFloat(sale_price - getDiscountValue(invoice_table.tdo[row]['discount']['data'],sale_price,row));
	//console.log('price');
	//console.log(price);
	return price;
}
function getItemTotal(row)
{
	if(isShipping(row))
	{
		var line_item_total = myParseFloat(invoice_table.tdo[row]['retail_price']['data']);
	}
	else
	{
		sale_price = myParseFloat(invoice_table.tdo[row]['sale_price']['data']);
		discount = getDiscountValue(invoice_table.tdo[row]['discount']['data'],sale_price,row);
		applied_instore_discount = myParseFloat(invoice_table.tdo[row]['applied_instore_discount']['data']);
		var line_item_total = (sale_price - discount) - applied_instore_discount;
	}
	return line_item_total;
}
function getFinalPrice(row)
{
	if(isGiftCard(row))
	{
		return invoice_table.tdo[row]['retail_price']['data'];
	}
	else if(isShipping(row))
	{
		return invoice_table.tdo[row]['retail_price']['data'];
	}
	else
	{
		discount = invoice_table.tdo[row]['discount']['data'];
		sale_price = invoice_table.tdo[row]['sale_price']['data'];
		//if(discount>sale_price) discount = sale_price;
		return parseDiscount(discount,sale_price,row);
	}
	
	
}
function getExtension(row)
{
	//the extension will either be the sale price - discount - applied_discount + tax
	extension = 0;
	if(isGiftCard(row))
	{
		extension = getFinalPrice(row);
	}
	else if(isShipping(row))
	{
		extension = myParseFloat(getFinalPrice(row)) +myParseFloat(invoice_table.tdo[row]['tax_total']['data']);
	}
	else
	{
		extension = myParseFloat(invoice_table.tdo[row]['quantity']['data']*getItemTotal(row)) + myParseFloat(invoice_table.tdo[row]['tax_total']['data']);
	}
	return extension;
}
function  getDiscountValue(discount,price,row)
{
	price = myParseFloat(price);
	discount += '';
	//discount could be numeric or could be 10%
	if(discount.indexOf('%') != -1)
	{
		//alert(discount.substr(0,discount.indexOf('%')));
		new_discount = myParseFloat(trim(discount.replace('%', '')));
		invoice_table.tdo[row]['discount_type']['data']='PERCENT';
		discount = (price*(new_discount/100));
		
		
	}
	else if (discount.indexOf('$') != -1)
	{
		new_discount = myParseFloat(trim(discount.replace('$', '')));
		invoice_table.tdo[row]['discount_type']['data']='DOLLAR';
		discount = new_discount;
		
	}
	else
	{
		invoice_table.tdo[row]['discount_type']['data']='DOLLAR';
		discount = discount;
		
	}
	if(discount>price)
		{
			return price;
		}
		else
		{
			return discount;
		}
}
function parseDiscount(discount,price,row)
{
	price = myParseFloat(price);
	discount += '';
	//discount could be numeric or could be 10%
	if(discount.indexOf('%') != -1)
	{
		//alert(discount.substr(0,discount.indexOf('%')));
		new_discount = myParseFloat(trim(discount.replace('%', '')));
		invoice_table.tdo[row]['discount_type']['data'] ='PERCENT';
		final_price =  price - (price*(new_discount/100));
		
		
	}
	else if (discount.indexOf('$') != -1)
	{
		new_discount = myParseFloat(trim(discount.replace('$', '')));
		invoice_table.tdo[row]['discount_type']['data']='DOLLAR';
		final_price = price - new_discount;
		
	}
	else
	{
		invoice_table.tdo[row]['discount_type']['data']='DOLLAR';
		final_price = price - discount;
		
	}
	if(final_price<0)
		{
			return 0;
		}
		else
		{
			return final_price;
		}
}
function calculateTotals(control)
{
	//return false;
	invoice_table.copyHTMLTableDataToObject();
	promotion_table.copyHTMLTableDataToObject();
	//we can calculate the totals if....
	//shipping has an address...
	//tax is selected....
	
	//pos_address_id should be all set....	
	ship_ok = true;
	//check that the tax categories have been corretly applied
	tax_ok = true;
	for(var row=0; row<invoice_table.tdo.length; row++)
	{
		if(invoice_table.tdo[row]['pos_sales_tax_category_id']['data'] == 'NULL' && !isGiftCard(row))
		{
			tax_ok = false;
		}
		else
		{
			//can we calculate the tax here...
			
		}		
		if(invoice_table.tdo[row]['ship']['data'] == true && pos_address_id == 'false')
		{
			ship_ok = false;
		}
		
	}

	
	var instore_total_promotion = getBestInStorePromotionCombination();
	//next is tax then....
	if( invoice_table.tdo.length > 0)
	{
		if(tax_ok)
		{
			if(ship_ok)
			{
				var total_tax = calculateSalesTaxV2();
				//now the extension can be calculated
				var le_grand_total = 0;
				for(var row=0; row<invoice_table.tdo.length; row++)
				{
					invoice_table.tdo[row]['extension']['data'] = getExtension(row);
					le_grand_total = le_grand_total+myParseFloat(invoice_table.tdo[row]['extension']['data']);
					console.log('le_grand_total' + le_grand_total);
				}
				
				var post_tax_total_promotion = applyPostTaxPromotions();
				le_grand_total = le_grand_total -post_tax_total_promotion;

				
				//document.getElementById('full_price_subtotal').value = round(full_price_total,2);
				//document.getElementById('discounted_subtotal').value = round(discounted_total,2);
				document.getElementById('pre_tax_promotion_amount').value = round2(instore_total_promotion,2);
				//document.getElementById('pre_tax_subtotal').value = round2(preTax_subtotal);
				//document.getElementById('total_quantity').value = total_quantity;
				document.getElementById('invoice_tax_total').value = round2(total_tax,2);
				document.getElementById('post_tax_promotion_amount').value = round2(post_tax_total_promotion,2);

				//document.getElementById('total_returns').value = return_total;
				document.getElementById('le_grande_total').value = '$' + round2(le_grand_total,2);
				
				
				
				
			}
			else
			{
				document.getElementById('invoice_tax_total').value = 'Check Address';
				document.getElementById('le_grande_total').value = 'Need Address';
			}
		}
		else
		{

			document.getElementById('invoice_tax_total').value = 'Check Item Tax';
			document.getElementById('le_grande_total').value = 'TBD';
		}
	
	}
	else
	{
	}
	

	
	
	invoice_table.writeObjectToHTMLTable(control);
	promotion_table.writeObjectToHTMLTable(control);
	
	
	//assign values:
	//document.getElementById('pre_discount_subtotal').value = round2(sub_total,2);
	//lets caluculate the 'line_total' or extension
	

	//document.getElementById('you_save').value = round2(discount_total,2);
	//console.log(invoice_table.tdo);
	invoice_table.writeObjectToHTMLTable(control);
	promotion_table.writeObjectToHTMLTable(control);
	
}
function getBestInStorePromotionCombination()
{
	//this function gets all the possible combinations of promotions and chooses the combination of the best value to the customer
	instore_total_promotion = 0;
	if(promotion_table.tdo.length >0)
	{
		var total_applied_instore_discount = 0; //return this value

		//Find out what promotions we will be using - basically pre tax promotions. 
		var promo_row_array = [];
		var promo_counter = 0;
		for(var i =0; i<promotion_table.tdo.length; i++)
		{
			if(promotion_table.tdo[i]['promotion_type']['data'] == 'Pre Tax')
			{
				promo_row_array[promo_counter] = i;
				promo_counter++;
			}
		}
		if(promo_row_array.length>0)
		{
			//create an array of all the combinations of promotions. For each combination calculate the total discount.
			var combo_array = permute(promo_row_array);
			//console.log('combo_array');
			//console.log(combo_array);
	
			var applied_instore_discount_array = [];
	
			for(var i =0; i<combo_array.length; i++)
			{
				//process combination in this order.....
				applied_instore_discount_array[i] = applyPromotions(combo_array[i]);
				//console.log('COMBINATION ' + combo_array[i].join() + ' Total discount: ' + applied_instore_discount_array[i]);		
			}
			//the largest promotion discount amount is the winner
			var max_promotion_discount = 0;
			var max_promotion_combo_row = 0;
			for(var pi = 0;pi<applied_instore_discount_array.length;pi++)
			{
				if(applied_instore_discount_array[pi] > max_promotion_discount)
				{
					max_promotion_combo_row = pi;
					max_promotion_discount = applied_instore_discount_array[pi];
				}
			}
			//finally apply the combo once and for all....
			console.log('BEST PROMO COMBINATION ' + combo_array[max_promotion_combo_row].join() + ' Total discount: ' + applied_instore_discount_array[max_promotion_combo_row]);		

			var instore_total_promotion = applyPromotions(combo_array[max_promotion_combo_row]);
		}
		
		
  	}
  	

  	return instore_total_promotion;
  	
}
function applyPromotions(promotion_order_array)
{	
	
	var total_all_promo_applied_instore_discount = 0;
	var previous_qualifying_amount = 0;
	//initialize the item promotions to 0. 
	for(var ip=0; ip<invoice_table.tdo.length; ip++)
	{
		invoice_table.tdo[ip]['pos_promotion_id']['data'] = 0;
		invoice_table.tdo[ip]['applied_instore_discount']['data'] = 0;
		invoice_table.tdo[ip]['promo_row']['data'] = 'NA';
		invoice_table.tdo[ip]['promo_lock']['data'] = 0;
	}
	
	//now go through each promotion in the combination
	for(var k=0; k <promotion_order_array.length; k++)
	{
		var total_applied_instore_discount = 0;
		var promo_row = promotion_order_array[k];  					
		var applied_instore_discount = 0;
		//console.log('Promotion ROW ' + k + ' ' + promotion_table.tdo[promo_row]['promotion_name']['data']);
		
		//go through each item and find out if it is included in the promotion
		promotion_included_array = findIncludedItemsForPromotion(promo_row);
		//console.log("promotion_included_array");
		//console.log(promotion_included_array);
		var blanket = promotion_table.tdo[promo_row]['blanket']['data'];
			
		var items_remaining = promotion_included_array.length;
		var return_content_array = promotion_included_array;
		var return_total = 0;
		var loop_counter = 0;
		//blanket discount applies until nothing is left to apply to...
		if(blanket == 1)
		{
			//console.log("blanket");
			while(items_remaining >0)
			//while(items_remaining >0 && loop_counter<100)
			{
				if(promotion_table.tdo[promo_row]['item_or_total']['data'] == 'ITEM')
				{
					//console.log("blanket item applying.....");
					return_content_object = applyPromotionToItems(return_content_array, promo_row);
					return_content_array = return_content_object['return_promotion_include_array'];
					return_total = return_total + return_content_object['return_total'];
				}
				else
				{
					//console.log("blanket total applying.....");
					return_content_object = applyPromotionToTotal(return_content_array, promo_row,previous_qualifying_amount);
					return_content_array = return_content_object['return_promotion_include_array'];
					return_total = return_total + return_content_object['return_total'];
					previous_qualifying_amount =  previous_qualifying_amount + return_content_object['qualifying_amount'];
				}
				items_remaining = return_content_array.length;
				//items_remaining = 0;
				//console.log('return_content_array');console.log(return_content_array);console.log('loop_counter ' + loop_counter);
				loop_counter++;
				
			}
		}
		else
		{
			
			if(promotion_table.tdo[promo_row]['item_or_total']['data'] == 'ITEM')
			{
				//console.log("single item applying.....");
				return_content_object = applyPromotionToItems(promotion_included_array, promo_row);
				return_content_array = return_content_object['return_promotion_include_array'];
				return_total = return_content_object['return_total'];
			}
			else
			{
				//console.log("single total applying.....");
				return_content_object = applyPromotionToTotal(promotion_included_array, promo_row, previous_qualifying_amount)
				return_content_array = return_content_object['return_promotion_include_array'];
				return_total = return_content_object['return_total'];
				previous_qualifying_amount =  previous_qualifying_amount + return_content_object['qualifying_amount'];

			}
			//console.log('return_content_array');console.log(return_content_array);

		}				

		promotion_table.tdo[promo_row]['applied_amount']['data'] = return_total;
		total_all_promo_applied_instore_discount = total_all_promo_applied_instore_discount +return_total;
	}
	
	return total_all_promo_applied_instore_discount;
}
function applyPostTaxPromotions()
{
	//basically fuck post tax promotions.... apply them in order as we can.
	//post tax promotions only work on full price items
	var previous_qualifying_amount = 0;
	var total_post_tax = 0;
	for(var promo_row =0; promo_row<promotion_table.tdo.length; promo_row++)
	{
		if(promotion_table.tdo[promo_row]['promotion_type']['data'] == 'Post Tax')
		{
			var expdate = promotion_table.tdo[promo_row]['expiration_date']['data'];
			if(compareTwoDates(invoice_date,expdate ) > 0)
			{
				value = myParseFloat(promotion_table.tdo[promo_row]['expiration_date']['data']);
			}
			else
			{
				buy_y_get_x = parseBuyXgetYTable(promo_row);
				value = buy_y_get_x['discount'][0];
			}
			
			
			
			var return_total = 0;
			included_items = findIncludedItemsForPromotion(promo_row);
			//calucate the full price total
			var full_price_total = 0;
			for(var ir=0; ir<included_items.length; ir++)
			{
				content_row = included_items[ir]['row'];
				if(isProductFullPrice(content_row))
				{
					full_price_total = full_price_total + getItemPrice(content_row);
				}
				else
				{
				}
			}
			
			//calculate the total...
			promo_total = full_price_total - previous_qualifying_amount;
			if(promo_total < value)
			{
				value = promo_total;
			}
			//console.log('promo_total ' + promo_total + ' full_price_total ' + full_price_total + ' previous_qualifying_amount ' + previous_qualifying_amount + ' value ' + value);

			buy_row = 0;
			buy_flag = true;
			
			//console.log('Buy Flag : ' + buy_flag + ' buy_x ' + buy_y_get_x['buy_x'][buy_row]);
			if(buy_flag)
			{
				qualifying_amount = value;
				previous_qualifying_amount = previous_qualifying_amount + qualifying_amount;
				//discount only up to the buy_row amount...

		
			
				//second loop uses weighted avarage to calulate discount per item
				for(var ii = 0;ii<included_items.length;ii++)
				{
					var content_row = included_items[ii]['row'];
					var discount = value*(getItemPrice(content_row)/full_price_total);
			
					if(invoice_table.tdo[content_row]['promo_row']['data'] == 'NA')
					{
						invoice_table.tdo[content_row]['promo_row']['data'] = promo_row;
						invoice_table.tdo[content_row]['pos_promotion_id']['data'] = promotion_table.tdo[promo_row]['pos_promotion_id']['data'];
					}
					else
					{
						invoice_table.tdo[content_row]['promo_row']['data'] = invoice_table.tdo[content_row]['promo_row']['data'] +','+promo_row;
						invoice_table.tdo[content_row]['pos_promotion_id']['data'] = invoice_table.tdo[content_row]['pos_promotion_id']['data'] + ',' + promotion_table.tdo[promo_row]['pos_promotion_id']['data'];
					}
					included_items[ii]['promo_row'] = promo_row;
					console.log('content_row ' + content_row + ' discount ' + discount);
					return_total = return_total +discount;

				}
			

				
			}
			else
			{}
			promotion_table.tdo[promo_row]['applied_amount']['data'] = return_total;
			total_post_tax = total_post_tax + return_total;
		}
	} //next promotion
	return total_post_tax;
} 
function applyPromotionToItems(included_items, promo_row)
{
	//the include array coming in may be blocked by existing promotions...so limiti it...
	var new_include_array = [];
	var new_include_counter = 0;
	for(var ia=0;ia<included_items.length;ia++)
	{
		var content_row = included_items[ia]['row'];
		if(invoice_table.tdo[content_row]['pos_promotion_id']['data'] == 0)
		{
			//we can apply the promotion to the item
			new_include_array[new_include_counter] = {};
			new_include_array[new_include_counter]['row'] = content_row;
			new_include_array[new_include_counter]['price'] = getItemPrice(content_row);
			new_include_array[new_include_counter]['promo_row'] = false;
			new_include_array[new_include_counter]['promo_lock'] = false;
			new_include_counter ++;
		}
		else
		{
			
		}
	}
	included_items = new_include_array;
	
	var return_promotion_include_array = [];
	var return_counter = 0;
	var return_total = 0;
	var percent_or_dollars = promotion_table.tdo[promo_row]['percent_or_dollars']['data'];
	//break up the buyx data
	buy_x_data = parseBuyXgetYTable(promo_row);
	//Find the maximum purchase level on the buy_y_get_x table
	var max_buy = 0;
	var buy_row = 0;
	var buy_flag = false;
	for(var by = 0 ; by<buy_x_data['buy_x'].length;by++)
	{
		if(included_items.length>=buy_x_data['buy_x'][by] && buy_x_data['buy_x'][by] > max_buy )
		{
			buy_row =by;
			max_buy = buy_x_data['buy_x'][by];
			buy_flag = true;
		}

	}
	/*console.log('Included Items');
	console.log(included_items);
	console.log('included items length: ' + included_items.length);
	console.log('Buy row: ' + buy_row + ' Buy Flag ' + buy_flag);	
	console.log('buy_x_data');
	console.log(buy_x_data);*/		
	//If we have found a match for the number of buy items calculate the discount amount
	var calc_discount = 0;
	if(buy_flag)
	{
		var total_number_get_items = myParseInt(buy_x_data['get_y'][buy_row]);
		//console.log('total_number_get_items ' + total_number_get_items + ' ' + typeof(total_number_get_items));
		var total_promotion_amount = 0
		if (percent_or_dollars == '$')
		{
			//APPLY THE $ PROMOTION AMOUNTS TO THE HIGER PRICED ITEMS FIRST
			//panty party: buy 3 get 3 @ $2 or 1 @ $6
			//easy....
			calc_discount = total_number_get_items * buy_x_data['discount'][buy_row];
			//Create and sort a price array...as we apply this from high to low		
			included_items.sort(function(a,b) { return parseFloat(b.price) - parseFloat(a.price) } );
			//console.log('included_items Sort a');
			//console.log(included_items);
			//get the total of the promotion value
			var buy_x_counter = 0;
			for(var pa=0;pa<included_items.length;pa++)
			{
				var content_row = included_items[pa]['row'];
				if(invoice_table.tdo[content_row]['pos_promotion_id']['data'] == 0 && buy_x_counter < buy_x_data['buy_x'][buy_row])
				{
					total_promotion_amount = total_promotion_amount + getItemPrice(content_row);
					buy_x_counter ++;
				}
				
			}
			//now assign the value.... via weighted distribution
			var buy_x_counter = 0;
			for(var pa=0;pa<included_items.length;pa++)
			{
				var content_row = included_items[pa]['row'];
				//item discount cannot apply if there are any other promotions....
				if(invoice_table.tdo[content_row]['pos_promotion_id']['data'] == 0 && buy_x_counter < buy_x_data['buy_x'][buy_row])
				{
					var applied_instore_discount = calc_discount*(getItemPrice(content_row)/total_promotion_amount);
					invoice_table.tdo[content_row]['pos_promotion_id']['data'] = promotion_table.tdo[promo_row]['percent_or_dollars']['data'];
					invoice_table.tdo[content_row]['promo_row']['data'] = promo_row;
					invoice_table.tdo[content_row]['promo_lock']['data'] = 1;
					included_items[pa]['promo_row'] = promo_row;
					included_items[pa]['promo_lock'] = true;
					
					invoice_table.tdo[content_row]['applied_instore_discount']['data'] = applied_instore_discount;
					buy_x_counter ++;
					return_total = return_total + applied_instore_discount;
				}
			}
	

		}
		else
		{
			//IF THE BUY ROW IS THE SAME AS THE GET ROW THEN WE SHOULD APPLY THE DISCOUNT TO EVERYONE
			//EX BUY 4 GET 4 at 40%... if there are 5 I assume the 5th is also 40%
			//HOWEVER Buy 4 GET 1 at 100% ... if you get a 5th it does not apply.
			//but if it is 1 @ 10% then it will blanket it...so ignore if it is 1?
			if(buy_x_data['get_y'][buy_row] == buy_x_data['buy_x'][buy_row] && myParseInt(buy_x_data['buy_x'][buy_row]) != 1)
			{
				
				//the percent is the same on all items... go through them all and apply the discount
				for(var ii = 0; ii<included_items.length;ii++)
				{
					var content_row = included_items[ii]['row'];

					var applied_instore_discount = getItemPrice(content_row)*(buy_x_data['discount'][buy_row]/100);
						//console.log('Row: ' + content_row + 'Applied Discount ' + applied_instore_discount);
					if(invoice_table.tdo[content_row]['pos_promotion_id']['data'] == 0 )
					{
						
						invoice_table.tdo[content_row]['applied_instore_discount']['data'] = applied_instore_discount;
			
						invoice_table.tdo[content_row]['pos_promotion_id']['data'] = promotion_table.tdo[promo_row]['pos_promotion_id']['data'];
						invoice_table.tdo[content_row]['promo_row']['data'] = promo_row;
						invoice_table.tdo[content_row]['promo_lock']['data'] = 1;
						included_items[ii]['promo_row'] = promo_row;
						included_items[ii]['promo_lock'] = true;
						return_total = return_total + applied_instore_discount;
					}

				}
			}
			else
			{
				//CALCULATE THE % PROMOTION AMOUNTS FROM THE LOWER PRICED ITEMS
				//APPLY THE DISCOUNT FIRST TO THE LOWEST THEN THE HIGEST ITEMS
				
				//go through the get amount of items.
				//add them to the promotion.
				//calculate the promotion amount

				//buy 3 get 1 free (Need 4 qualifying , items, 1 (lowest price) is 100% each)
				//10% off 2 bras (Need 2 qualifying bras, 2 (lowest price) items taken at 10% each)
				//20% off 3 bras (same)
				//here we need to find the "lowest" price item(s) and take a percentage off
				//sor the array
				included_items.sort(function(a,b) { return parseFloat(a.price) - parseFloat(b.price) } );
				//console.log('included_items Sort a');
				//console.log(included_items);
				
				//calculate the amount to apply
				var total_to_apply = 0
				for(var g=0;g<total_number_get_items;g++)
				{
					var content_row = included_items[g]['row'];
					if(invoice_table.tdo[content_row]['pos_promotion_id']['data'] == 0 )
					{
						total_to_apply = total_to_apply + getItemPrice(content_row);
					}
				}
				//console.log('discount: ' + buy_x_data['discount'][buy_row]/100);
				total_to_apply = total_to_apply*(buy_x_data['discount'][buy_row]/100);
				
				//console.log('total_to_apply ' + total_to_apply);
				
				//calculate the total amount to apply to.....
				var total_amount_in_promo = 0;
				for(var g=0;g<total_number_get_items;g++)
				{
					var content_row = included_items[g]['row'];
					if(invoice_table.tdo[content_row]['pos_promotion_id']['data'] == 0 )
					{
						total_amount_in_promo = total_amount_in_promo + getItemPrice(content_row);
					}
				}
				
				//console.log('total_amount_in_promo ' + total_amount_in_promo);
				
				//the rest to the highest items...which is.... buy-get
				var items_ramining = buy_x_data['buy_x'][buy_row] - buy_x_data['get_y'][buy_row];
				for(var g=0;g<items_ramining;g++)
				{
					var last_index = included_items.length - 1 - g;
					var content_row = included_items[last_index]['row'];
					if(invoice_table.tdo[content_row]['pos_promotion_id']['data'] == 0 )
					{
						total_amount_in_promo = total_amount_in_promo + getItemPrice(content_row);
					}
				}
				
				//console.log('total_amount_in_promo ' + total_amount_in_promo);
				
				
				//now apply...FIRST TO THE LOWEST PRICE ITEMS...

				for(var g=0;g<total_number_get_items;g++)
				{
					var content_row = included_items[g]['row'];
					if(invoice_table.tdo[content_row]['pos_promotion_id']['data'] == 0 )
					{
						var applied_instore_discount = total_to_apply*(getItemPrice(content_row)/total_amount_in_promo);
						invoice_table.tdo[content_row]['applied_instore_discount']['data'] = applied_instore_discount;
						invoice_table.tdo[content_row]['pos_promotion_id']['data'] = promotion_table.tdo[promo_row]['pos_promotion_id']['data'];	
						invoice_table.tdo[content_row]['promo_row']['data'] = promo_row;
						invoice_table.tdo[content_row]['promo_lock']['data'] = 1;
						included_items[g]['promo_row'] = promo_row;
						included_items[g]['promo_lock'] = true;
						return_total = return_total + applied_instore_discount;
					}
		
				}
			
				//the rest to the highest items...which is.... buy-get
				var items_ramining = buy_x_data['buy_x'][buy_row] - buy_x_data['get_y'][buy_row];
				//console.log('items_ramining ' + items_ramining);
				for(var g=0;g<items_ramining;g++)
				{
					var last_index = included_items.length - 1 - g;
					var content_row = included_items[last_index]['row'];
					if(invoice_table.tdo[content_row]['pos_promotion_id']['data'] == 0 )
					{
						var applied_instore_discount = total_to_apply*(getItemPrice(content_row)/total_amount_in_promo);
						invoice_table.tdo[content_row]['applied_instore_discount']['data'] = applied_instore_discount;
						invoice_table.tdo[content_row]['pos_promotion_id']['data'] = promotion_table.tdo[promo_row]['pos_promotion_id']['data'];
						invoice_table.tdo[content_row]['promo_lock']['data'] = 1;

						invoice_table.tdo[content_row]['promo_row']['data'] = promo_row;
						included_items[last_index]['promo_row'] = promo_row;
						included_items[last_index]['promo_lock'] = true;
						return_total = return_total + applied_instore_discount;
					}

					
				}
			}
			
		}	
		//now what is remaining?
		//here we need to exclude items where promotions are blocking themm....
		for(var pa=0;pa<included_items.length;pa++)
		{
			var content_row = included_items[pa]['row'];
			
			if(included_items[pa]['promo_lock']===false)
			{
				return_promotion_include_array[return_counter] = {};
				return_promotion_include_array[return_counter]['row'] = content_row;
				return_promotion_include_array[return_counter]['price'] = getItemPrice(content_row);
				return_promotion_include_array[return_counter]['promo_row'] = false;
				return_promotion_include_array[return_counter]['promo_lock'] = false;
				return_counter ++;
				
			}
		}
	}
	else
	{
		//nothing was found....nothing happens, nothing returned...
		//items_remaining = 0;
	}
	var return_object = {};
	return_object['return_total'] = return_total;
	return_object['return_promotion_include_array'] = return_promotion_include_array;
	return return_object;	
}
function applyPromotionToTotal(included_items, promo_row, previous_qualifying_amount)
{
	//applying the promotion to the total amount.....
	//items can take multiple promotions on the total amount.....
	//we are going to sum up the total for all the included items
	
	var return_promotion_include_array = [];
	var return_counter = 0;
	var return_total = 0;
	var qualifying_amount = 0;
	
	//calculate the total...
	var total = 0;
	for(var ii = 0;ii<included_items.length;ii++)
	{
		var content_row = included_items[ii]['row'];
		
		
		//getItemPrice
		//here we use the item price including applied instore discount....
		//what is the total that can apply?
		//the getItemPrice - any previous qualifying amounts is what we need.....
		
		
		total =  total + getItemPrice(content_row);
	}
	

	//how can i look up any previcous qualifying amount?
	promo_total = total - previous_qualifying_amount;
	//console.log('previous_qualifying_amount to apply promotion to: ' + previous_qualifying_amount);
	//console.log('Total to apply promotion to: ' + promo_total);
	
	//break up the buyx data
	buy_y_get_x = parseBuyXgetYTable(promo_row);
	//Find the maximum purchase level on the buy_y_get_x table
	var max_buy = 0;
	var buy_row = 0;
	var buy_flag = false;
	for(var by = 0 ; by<buy_y_get_x['buy_x'].length;by++)
	{
		//console.log('Total: ' + total + ' buy_y_get_x[buy_x][by] ' + buy_y_get_x['buy_x'][by] + ' max_buy ' + max_buy)
		if(promo_total>=buy_y_get_x['buy_x'][by] && buy_y_get_x['buy_x'][by] > max_buy )
		{
			buy_row =by;
			max_buy = buy_y_get_x['buy_x'][by];
			buy_flag = true;
		}

	}
	//console.log('Buy Flag : ' + buy_flag + ' buy_x ' + buy_y_get_x['buy_x'][buy_row]);
	if(buy_flag)
	{
		var percent_or_dollars = promotion_table.tdo[promo_row]['percent_or_dollars']['data'];
		qualifying_amount = buy_y_get_x['buy_x'][buy_row];
		//discount only up to the buy_row amount...
		if(percent_or_dollars == '$')
		{
			//console.log('Checing Item dollars ');
			//$25 off $125
			//$50 off $200
			// $100 off $350
			//APPLY $ OFF TO HIGHET PRICED ITEMS FIRST
		
			
			//second loop uses weighted avarage to calulate discount per item
			for(var ii = 0;ii<included_items.length;ii++)
			{
				var content_row = included_items[ii]['row'];
				var applied_instore_discount = buy_y_get_x['discount'][buy_row]*(getItemPrice(content_row)/total);
				invoice_table.tdo[content_row]['applied_instore_discount']['data'] = myParseFloat(invoice_table.tdo[content_row]['applied_instore_discount']['data'])  + applied_instore_discount;
				
				if(invoice_table.tdo[content_row]['promo_row']['data'] == 'NA')
				{
					invoice_table.tdo[content_row]['promo_row']['data'] = promo_row;
					invoice_table.tdo[content_row]['pos_promotion_id']['data'] = promotion_table.tdo[promo_row]['pos_promotion_id']['data'];
				}
				else
				{
					invoice_table.tdo[content_row]['promo_row']['data'] = invoice_table.tdo[content_row]['promo_row']['data'] +','+promo_row;
					invoice_table.tdo[content_row]['pos_promotion_id']['data'] = invoice_table.tdo[content_row]['pos_promotion_id']['data'] + ',' + promotion_table.tdo[promo_row]['pos_promotion_id']['data'];
				}
				included_items[ii]['promo_row'] = promo_row;
				return_total = return_total +applied_instore_discount;

			}
			
			
			
		}
		else
		{
			//console.log('Checing Item percent ');
			//the percent is the same on all items... go through them all and apply the discount
			for(var ii = 0; ii<included_items.length;ii++)
			{
				var content_row = included_items[ii]['row'];
				var applied_instore_discount = getItemPrice(content_row)*(buy_y_get_x['discount'][buy_row]/100);
				invoice_table.tdo[content_row]['applied_instore_discount']['data'] = myParseFloat(invoice_table.tdo[content_row]['applied_instore_discount']['data']) + applied_instore_discount;
				
				if(invoice_table.tdo[content_row]['promo_row']['data'] == 'NA')
				{
					invoice_table.tdo[content_row]['promo_row']['data'] = promo_row;
					invoice_table.tdo[content_row]['pos_promotion_id']['data'] = promotion_table.tdo[promo_row]['pos_promotion_id']['data'];
				}
				else
				{
					invoice_table.tdo[content_row]['promo_row']['data'] = invoice_table.tdo[content_row]['promo_row']['data'] +','+promo_row;
					invoice_table.tdo[content_row]['pos_promotion_id']['data'] = invoice_table.tdo[content_row]['pos_promotion_id']['data'] + ',' + promotion_table.tdo[promo_row]['pos_promotion_id']['data'];
				}				
				included_items[ii]['promo_row'] = promo_row;
				return_total = return_total +applied_instore_discount;
			}
		}
		//now what is remaining?
		for(var pa=0;pa<included_items.length;pa++)
		{
			var content_row = included_items[pa]['row'];
			if(included_items[pa]['promo_lock']=== false)
			{
				return_promotion_include_array[return_counter] = {};
				return_promotion_include_array[return_counter]['row'] = content_row;
				return_promotion_include_array[return_counter]['price'] = getItemPrice(content_row);
				return_promotion_include_array[return_counter]['promo_row'] = false;
				return_promotion_include_array[return_counter]['promo_lock'] = false;
				return_counter ++;
			}
		}
	}
	else
	{}
	
	var return_object = {};
	return_object['return_total'] = return_total;
	return_object['return_promotion_include_array'] = return_promotion_include_array;
	return_object['qualifying_amount'] = qualifying_amount;
	return return_object;	
		
}
function parseBuyXgetYTable(promo_row)
{
	var buy_y_get_x = promotion_table.tdo[promo_row]['buy_y_get_x']['data'];
	buy_y_get_x = buy_y_get_x.split(",");
	var buy_x_data = {};
	buy_x_data['buy_x']= [];
	buy_x_data['get_y']= [];
	buy_x_data['discount']= [];
	for(var by=0;by<buy_y_get_x.length;by++)
	{
		//concat_ws(':', buy , get, discount, d_or_p)
		buy_get_discount = buy_y_get_x[by].split(':');
		buy_x_data['buy_x'][by] = Number(buy_get_discount[0]);
		buy_x_data['get_y'][by] = myParseInt(buy_get_discount[1]);
		buy_x_data['discount'][by] = Number(buy_get_discount[2]);
	}	
	//console.log('buy_x_data');
	//console.log(buy_x_data);
	return (buy_x_data);
}
function findIncludedItemsForPromotion(promo_row)
{
	
	var item_counter = 0;
	var other_excluded = [];
	var cats_excluded = [];
	var products_excluded = [];
	var brands_excluded = [];
	var promotion_included_array = [];
	
	var cat_limits = promotion_table.tdo[promo_row]['categories']['data'];
	var brand_limits = promotion_table.tdo[promo_row]['brands']['data'];
	var product_limits = promotion_table.tdo[promo_row]['products']['data'];
	var qualifying_amount = promotion_table.tdo[promo_row]['qualifying_amount']['data'];
	for(var ir=0; ir<invoice_table.tdo.length; ir++)
	{
		other_excluded[ir] = false;
	 	cats_excluded[ir] = false;
		products_excluded[ir] = false;
	 	brands_excluded[ir] = false;
	}
	if(cat_limits != null)
	{
		cat_limits = cat_limits.split(",");
		//console.log('####################CHECKING CATAGROY LIMITS###################');
		//console.log(cat_limits);
		for(var ir=0; ir<invoice_table.tdo.length; ir++)
		{
			var bln_found = false;
			var product_category = invoice_table.tdo[ir]['pos_category_id']['data'];
			for(var cat=0; cat<cat_limits.length;cat++)
			{
				var cats_and_include = cat_limits[cat].split(":");
//				console.log(cats_and_include);
				var include_or_exclude = cats_and_include[0];
				var promotion_category_id = cats_and_include[1];
				var sub_categories = cats_and_include[2];
			
				
				//console.log('CHECKING ROW ' + ir + ' product_category '  + include_or_exclude + ' ' +promotion_category_id + ' Verse ' + product_category);
				if(include_or_exclude == 'INCLUDE')
				{
					var tmp_bool = true;
				}
				else
				{
					var tmp_bool = false;
				}
				if(!bln_found)
				{
					cats_excluded[ir] = tmp_bool;
					//console.log('JUST SET cats_excluded[ir] ' + cats_excluded[ir]);
					if(product_category == promotion_category_id)
					{
						cats_excluded[ir] = !tmp_bool;
						//console.log('found cats_excluded[ir] ' + cats_excluded[ir]);
						bln_found = true;
					}
					else if(cat_array[promotion_category_id].indexOf(product_category) != -1 && sub_categories == 1 )
					{
						cats_excluded[ir] = !tmp_bool;
						//console.log('found sub cat cats_excluded[ir] ' + cats_excluded[ir]);
						bln_found = true;
					}
				}
				
				
			}
		}
				
	}
	
	if(product_limits != null)
	{
		product_limits = product_limits.split(",");
		//console.log('####################CHECKING Product LIMITS###################');
		//console.log(product_limits);
		
		
		for(var ir=0; ir<invoice_table.tdo.length; ir++)
		{
			var product_id = invoice_table.tdo[ir]['pos_product_id']['data'];
			var bln_found = false;
			for(var pro=0; pro<product_limits.length;pro++)
			{
				var prods_and_include = product_limits[pro].split(":");
				var include_or_exclude = prods_and_include[0];
				var promotion_product_id = prods_and_include[1];
			
				if(include_or_exclude == 'INCLUDE')
				{
					var tmp_bool = true;
				}
				else
				{
					var tmp_bool = false;
				}
				if(!bln_found)
				{
					products_excluded[ir] = tmp_bool;
	
					if(product_id == promotion_product_id)
					{
						products_excluded[ir] = !tmp_bool;
						bln_found = true;
						//console.log('Prod id ' + product_id + ' INCLUDE ' +tmp_bool );
					}
				}
			}
		}
	}
	
	if(brand_limits != null)
	{
		brand_limits = brand_limits.split(",");
		//console.log('####################CHECKING BRAND LIMITS###################');
		//console.log(brand_limits);
		for(var ir=0; ir<invoice_table.tdo.length; ir++)
		{
			var bln_found = false;
			for(var bra=0; bra<brand_limits.length;bra++)
			{
				var brands_and_include = brand_limits[bra].split(":");
				var include_or_exclude = brands_and_include[0];
				var promotion_brand_id = brands_and_include[1];
			
				
				var brand_id = invoice_table.tdo[ir]['pos_manufacturer_brand_id']['data'];
				//console.log('BRand id ' + brand_id);
				if(include_or_exclude == 'INCLUDE')
				{
					var tmp_bool = true;
				}
				else
				{
					var tmp_bool = false;
				}
				if(!bln_found)
				{
					brands_excluded[ir] = tmp_bool;
	
					if(brand_id == promotion_brand_id)
					{
						//console.log('Brand id ' + brand_id + ' INCLUDE ' +tmp_bool );
						brands_excluded[ir] = !tmp_bool;
						bln_found=true;
					
					}
				}
			}
		}
		

	} 	
	
	
	//second we need to find out if the item is excluded for other reasons , like sale price, clearance
	//and set up the array to return back
	for(var ir=0; ir<invoice_table.tdo.length; ir++)
	{
	

		//is the item a product? what about service? does not use promotion code?
		//items can have multiple promotion codes.....
		other_excluded[ir]=false;
		if(!isProduct(ir))
		{
			other_excluded[ir] = true;
		} 
		if(invoice_table.tdo[ir]['promo_lock']['data'] == 1)
		{
			other_excluded[ir] = true;
		}
		
		if(invoice_table.tdo[ir]['quantity']['data'] < 1)
		{
			other_excluded[ir] = true;
		}
		if(getItemPrice(ir) < qualifying_amount)
		{
			//console.log(ir + ' excluded due to lower than qualifying amount');
			other_excluded[ir] = true;
		}
	
		if(isSaleOrDiscounted(ir) && promotion_table.tdo[promo_row]['check_if_can_be_applied_to_sale_items']['data'] != 1)
		{
			//console.log(ir + ' excluded due to sale item');
			other_excluded[ir] = true;
		}
		if(isClearance(ir) && promotion_table.tdo[promo_row]['check_if_can_be_applied_to_clearance_items']['data'] != 1)
		{
			//console.log(ir + ' excluded due to clearance');
			other_excluded[ir] = true;
		}
	
		if(!other_excluded[ir] && !cats_excluded[ir] && !products_excluded[ir] && !brands_excluded[ir])
		{
			
			promotion_included_array[item_counter] = {};
			promotion_included_array[item_counter]['row'] = ir;
			promotion_included_array[item_counter]['price'] = getItemPrice(ir);
			promotion_included_array[item_counter]['promo_row'] = false;
			promotion_included_array[item_counter]['promo_lock'] = false;
			item_counter++;
		}
		
		
		//console.log('exluded[ir]');
		//console.log(exluded[ir]);
		
	}
	//console.log('other_excluded');console.log(other_excluded);
	//console.log('cats_excluded');console.log(cats_excluded);
	//console.log('products_excluded');console.log(products_excluded);
	//console.log('brands_excluded');console.log(brands_excluded);
	return promotion_included_array;
}






/********Tax calculations *********************/
function calculateSalesTaxV2()
{
	var tax_total = 0;
	//to cacluate the sales tax we need to first get the item total
	//then we need the tax rate
	//then we can calculate the tax
	for(var row=0; row<invoice_table.tdo.length; row++)
	{
		if(isGiftCard(row))
		{
			// no tax category, 0 tax rate, no taxable total
			invoice_table.tdo[row]['taxable_total']['data']= 0;
			invoice_table.tdo[row]['tax_rate']['data'] = 0;
			invoice_table.tdo[row]['tax_total']['data'] = 0;
		}
		else
		{
			exemption_value = getExemptionValue(row);
			item_total = getItemTotal(row);
			quantity = myParseFloat(invoice_table.tdo[row]['quantity']['data']);
			//what is the tax rate for this price and tax category id?
			//console.log(item_total);
			if(item_total <= exemption_value && exemption_value != 0)
			{
				tax_rate = myParseFloat(invoice_table.tdo[row]['state_exemption_tax_rate']['data']) + myParseFloat(invoice_table.tdo[row]['local_exemption_tax_rate']['data']);
			}
			else
			{
				tax_rate = myParseFloat(invoice_table.tdo[row]['state_regular_tax_rate']['data']) + myParseFloat(invoice_table.tdo[row]['local_regular_tax_rate']['data']);
			}
			tax = round(item_total*quantity*tax_rate/100,2);
			if(quantity>=0)
			{
				
				invoice_table.tdo[row]['taxable_total']['data'] = item_total*quantity;

			}
			else
			{
				//return - 
				invoice_table.tdo[row]['taxable_total']['data'] = -item_total*quantity;
			}
			invoice_table.tdo[row]['tax_rate']['data'] = tax_rate;
			invoice_table.tdo[row]['tax_total']['data'] = tax;
			tax_total = tax_total + tax;
		}
	}
	return tax_total;
}
function getExemptionValue(row)
{
		//basically use the state exeption value as the exemption value....
		/*var exemption_value;
		//take the greater value as the exemption value....
		var state_exemption_value = myParseFloat(invoice_table.tdo['state_exemption_value'][row]);
		var local_exemption_value = myParseFloat(invoice_table.tdo['local_exemption_value'][row]);

		if(state_exemption_value>local_exemption_value)
		{
			exemption_value = state_exemption_value;
		}
		else
		{
			exemption_value = local_exemption_value;
		}
		return exemption_value;*/
		return invoice_table.tdo[row]['state_exemption_value']['data'];
}
function productSearchFocus()
{
	//alert($('#product_search').val()); = 'Type to search, leave spaces between search terms...'
	$('#product_search').select();
	$('#product_search').focus();
	
}
function barcodeFocus()
{
	if($( "#barcode" ).length)
	{
		barcode_control = document.getElementById('barcode');
	  	barcode_control.focus();
	  	barcode_control.select();
    }
}
//********************** POSTING ***********************************************

function preparePostData()
{
	invoice_table.copyHTMLTableDataToObject();
	promotion_table.copyHTMLTableDataToObject();
	var post_string = {};
	
	//customer data:
	//address, email, phone
	if(document.getElementById('pos_address_id'))
	{
		email  = document.getElementById('email1').value;
	 	phone = document.getElementById('phone').value;
	}
	else
	{
		email = '';
		phone = '';
	}
	post_string['pos_address_id'] = pos_address_id;
	
	post_string['email1'] = email;
	post_string['phone'] = phone;
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;//document.getElementById('pos_sales_invoice_id').value;
	//post_string['tax_calculation_method'] = //getTaxCalculationMethod();
	//do we need this one?
	//post_string['shipping_amount'] = document.getElementById('shipping_amount').value;
	//post_string['pre_tax_promotion_amount'] = document.getElementById('pre_tax_promotion_amount').value;
	//post_string['post_tax_promotion_amount'] = document.getElementById('post_tax_promotion_amount').value;
	//post_string['pos_state_tax_jurisdiction_id']
	//post_string['pos_local_tax_jurisdiction_id']
	post_string['pos_customer_id'] = pos_customer_id;//document.getElementById('pos_customer_id').value;
	post_string['invoice_date'] = invoice_date;//document.getElementById('invoice_date').value;
	post_string['invoice_tdo'] = invoice_table.getPostData();
	post_string['follow_up'] =  getCheckbox01($('#follow_up'));
	post_string['special_order'] = getCheckbox01($('#special_order'));
	//JSON.stringify(invoice_table.tdo);//JSON.stringify(this.tdo);
	post_string['promotion_tdo'] = promotion_table.getPostData();
	//post_string['invoice_tbody_def'] = invoice_table.tbody_def;
	//post_string['promotion_tbody_def'] = promotion_table.tbody_def;
	post_string['ajax_request'] = 'SAVE_INVOICE';
	
	post_string['invoice_status'] = 'DRAFT';
	console.log('post_string');
	console.log(post_string);
	return post_string;
	
	
	
	
}
function saveDraftAndReload()
{
	save_url = "retail_sales_invoice.ajax.php";
	
	post_string = preparePostData();
	$.post(save_url, post_string,
	function(response) 
	{
		document.location.reload(true);
	});
	
}
function saveDraft()
{
	save_url = "retail_sales_invoice.ajax.php";	
	post_string = preparePostData();
	$.post(save_url, post_string,
   	function(response) {
     console.log(response);
     needToConfirm=false;
     barcodeFocus();
     
   });
   //this code gets ran before the other ajax code...
}
function saveDraftAndGo(url)
{
	
	save_url = "retail_sales_invoice.ajax.php";	
	post_string = preparePostData();
	$.post(save_url, post_string,
	function(response) 
	{
		//unlock
		unlock_url = POS_ENGINE_URL + '/includes/php/unlock_entry.php';
		unlock_post = {};
		unlock_post['table'] = 'pos_sales_invoice';
    	unlock_post['primary_key_name'] = 'pos_sales_invoice_id';
    	unlock_post['primary_key_value'] = pos_sales_invoice_id;
		$.post(unlock_url, unlock_post,
			function(response2) 
			{
				console.log(response2);
				//window.location = url;
			}
		);
	});
	
}
function exitInvoice(url)
{
	//here we clicked on "exit"
	//the form is not validated
	//status should be set to exited
	//exited invoice 
	//only edit contents can therefore show
	
	//save draft with invoice 
	var post_string = preparePostData();
	post_string['invoice_status'] = 'EXITED';
	post_string['unlock'] = 'YES';
	post_string['finalize'] = 'NO';
	$.post('retail_sales_invoice.ajax.php', post_string,
	function(response) 
	{
		//go....
		window.location = url;
	});
	
	
}
function continueToPayments(url)
{
	//we want to run the code 
	//finalizePaymentTransaction($pos_sales_invoice_id);
	if(validateInvoiceForm())
	{
		var post_string = preparePostData();
		post_string.finalize = 'YES';
		post_string['unlock'] = 'YES';
		$.post('retail_sales_invoice.ajax.php', post_string,
		function(response) 
		{
			window.location = url;
			
		
		});
	}
}
function validateInvoiceForm()
{

	//alert('validating...');
	//really there should be nothing to validate... the code should deal with it all


	//we need to make sure the discount is not greater than the sale price.....
	errors = '';
	if (invoice_table.tdo.length>0)
	{
		for(var row=0; row<invoice_table.tdo.length; row++)
		{
			console.log('wtf');
			console.log(invoice_table.tdo[row]);
			console.log(invoice_table.tdo[row]['quantity']['data']);
			quantity = myParseFloat(invoice_table.tdo[row]['quantity']['data']);
			if(quantity >0)
			{
				retail_price = myParseFloat(invoice_table.tdo[row]['retail_price']['data']);
				//if it is a gift card sale price is the retail price
				if(isGiftCard(row))
				{
					sale_price = retail_price;
					//gift_card_total = gift_card_total + retail_price;
				}
				else if(isShipping(row))
				{
					sale_price = retail_price;
					//shipping_total = shipping_total + retail_price;
				}
				else
				{
					sale_price = myParseFloat(invoice_table.tdo[row]['sale_price']['data']);
					
				}
				var discount = invoice_table.tdo[row]['discount']['data'];
				if(discount.indexOf('%') != -1)
				{
		//alert(discount.substr(0,discount.indexOf('%')));
					new_discount = myParseFloat(trim(discount.replace('%', '')));
					discount = (sale_price*(new_discount/100));
		
		
				}
				else if (discount.indexOf('$') != -1)
				{
						new_discount = myParseFloat(trim(discount.replace('$', '')));
						discount = new_discount;
		
				}
				else
				{
					invoice_table.tdo[row]['discount_type']['data']='DOLLAR';
					discount = discount;
		
				}
				//alert(discount + ' Discount, Sale Price: ' + sale_price);
				
				
				if (discount>sale_price)
				{
					errors += 'Please Correct: Discount is greater than sale price, row: ' + parseInt(parseInt(row) + parseInt(1)) + '\r\n';
				}
			}
			
		}
	}
	if (errors != '')
	{
		alert (errors);
		return false;
	}
	else
	{
		return true;
	}


}
function addService()
{
	alert('working on add service');
}
function addShipping()
{
	alert('working on add shipping - try checking the ship box or type in ship to barcode');
}
function addPromotion()
{
	alert('add promotion ... this will eventually give a list of active promotions, until then use the barcode for the promotion');
}

//FIX THESE WITH NEW VERSION
function disableColumns()
{
	//this needs to disable the appropriate columns
	for(var row=0; row<invoice_table.tdo.length; row++)
	{
		if(invoice_table.tdo[row]['content_type']['data'] == 'SHIPPING')
		{
			invoice_table.disableCell(row, 'special_order');
    		invoice_table.disableCell(row, 'ship');
    		invoice_table.disableCell(row, 'quantity');
    		invoice_table.disableCell(row, 'pos_discount_id');
    		invoice_table.disableCell(row, 'pos_sales_tax_category_id');
    		invoice_table.disableCell(row, 'discount');
    		invoice_table.disableCell(row, 'sale_price');
		}
		else if(invoice_table.tdo[row]['content_type']['data'] == 'CREDIT_CARD')
		{
		}
		else if(invoice_table.tdo[row]['content_type']['data'] == 'PRODUCT')
		{
		}
		else if(invoice_table.tdo[row]['content_type']['data'] == 'SERVICE')
		{
		}
	}
	
}