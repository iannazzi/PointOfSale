var collect_track_data = false;
var card_data = '';
var log = true;
$(function()
{
	
	//general keyboard handler looking for cc track data .. if found then pop up the cc data and put the card number in...
	/*$(window).keypress(function(e) {
       //console.log("which: " + e.which);
       //console.log("keyCode: " + e.keyCode);
   });*/
  

   //payment dialogs $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
	$( "#cash-dialog-form" ).dialog(
	{
		autoOpen: false,
		height: 300,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	process_cash_payment();
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
			$("#count_change_instructions").show();
			$('#loading_image').hide();
			$('#cash_input').val('0.00');
			$('#cash_input').focus();
			$('#cash_input').select();
		}

	});
	$('#cash-dialog-form').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			  process_cash_payment();
		}
    });
    
 	$( "#check-dialog-form" ).dialog(
	{
		autoOpen: false,
		height: 200,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	process_check_payment();
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
			$("#count_change_instructions").show();
			$('#check_loading_image').hide();
			$('#check_input').focus();
			$('#check_input').select();
		}

	});
	$('#check-dialog-form').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			  process_check_payment();
		}
    });
    $( "#cc-dialog-form" ).dialog(
	{
		//if the payment gateway is not selected then everyone fucks off
		
		autoOpen: false,
		height: 400,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	//assuming this card was swiped
				 	process_keyed_or_offline_CC_payment();
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
			if($('#pos_payment_gateway_id').val() == 'false' )
			{
				$('#offline_cc_entry').hide();
				$('#Manual_CC_DATA').hide();
				$('#cc_online').hide();
				$('#cc_offline').hide();
				//online=false;
				alert('Must select a cc processor - Set up the terminal for a default processor to avoid this message.');

			}
			else
			{
				if (online==true)
				{
				
						$('#offline_cc_entry').hide();
						$('#Manual_CC_DATA').hide();
						$('#Offline_CC_DATA').hide();
						$('#cc_online').show();
				
				}
				else
				{
						$('#cc_online').hide();
						$('#Manual_CC_DATA').hide();
						$('#offline_cc_entry').show();
						
						$('#Offline_CC_DATA').show();

				}
			}
			$('#cc_loading_image').hide();

			$('#submit-form-button').button('option', 'label', 'Submit');
			$('#submit-form-button').attr("disabled", false);
			$('#cancel-form-button').attr("disabled", false);
			$('#CC_RESPONSE').hide();
			
			$('#cc_input').focus();
			$('#cc_input').select();
			$('#card_type').val('false');
			$('#credit_card_number').val('');
			$('#expiration').val('');
			$('#card_holder').val('');
			$('#ccv').val('');
		
		}

	});
	$('#cc-dialog-form').keypress(function(e) 
	{
		
		//var charCode = e.which;
		var charCode = e.keyCode ? e.keyCode : e.which;
   
		//ie? evt = e || window.event;
		track_start = '%';
		finished = false;
		timeout = 100;
		track_start_code = track_start.charCodeAt(0);
		
		//if (log) console.log('keycode ' + e.keycode);
		
		
		//if (log) console.log('charcode ' + charCode);
		if (charCode == track_start_code)
		{
			collect_track_data = true;
			if (log)  console.log('collection of track data start....');

			$('#offline_cc_entry').hide();
			$('#cc_online').hide();
			$('#Manual_CC_DATA').hide();
			$('#cc_loading_image').show();		
			
		}
		if (collect_track_data)
		{	
			if(charCode == 13) 
			{
				//all done
				if (log) console.log('Track data complete:');
				if (log) console.log( card_data);
				collect_track_data = false;
				//check a few things...
				//http://stackoverflow.com/questions/2121881/parse-credit-card-input-from-magnetic-stripe/27630620#27630620
				
				if(card_data.indexOf('=') === -1 || card_data.indexOf('^')===-1)
				{
					alert("Error Reading Card, Try Again");
					$('#cc_loading_image').hide();
					card_data = '';
					if (online==true)
					{
				
							$('#offline_cc_entry').hide();
							$('#Manual_CC_DATA').hide();
							$('#cc_online').show();
				
					}
					else
					{
							$('#cc_online').hide();
							$('#Manual_CC_DATA').hide();
							$('#offline_cc_entry').show();

					}
				}
				else
				{
					$('#cc_loading_image').hide();
					$('#Manual_CC_DATA').show();
					if (log) console.log("Track Data: " + card_data);
			
					/*
					Track1 and Track2 Data
		The transaction-specific information can include track 1 and track 2 data from the
		magnetic strip on the credit card. Although the entire unaltered track must be provided in
		the authorization request message, any framing characters (Start and End Sentinel) must
		be removed first:
		 For track 1 data, the Start Sentinel character is a percent sign (%); the End Sentinel
		character is a question mark (?).
		 For track 2 data, the Start Sentinel is ASCII $0B, while the End Sentinel character is
		ASCII $0F. If the bytes in track 2 data are converted to ASCII, this turns the Start
		Sentinel character to a semicolon (;), and the End Sentinel to a question mark (?).
		The Start Sentinel and End Sentinel

		*/
					process_swipe_cc_payment(card_data);
					card_data = '';
				}
				
			}
			else
			{
				var tmpchar = String.fromCharCode(charCode);
				card_data = card_data.concat(tmpchar);
				if (log) console.log('Charcode: ' + charCode + ' Character: ' + tmpchar);
				//if (log) console.log(card_data);
				
				if (e.preventDefault) e.preventDefault();
 				e.returnValue=false;
				return false;
				
			}
		}
		else
		{
			//i am guessing this will be regular input?
			if (charCode == $.ui.keyCode.ENTER) 
			{
			 	 process_keyed_or_offline_CC_payment();
			}
		}
		//console.log("which: " + e.which);
        //console.log("keyCode: " + e.keyCode);
		//track and collect data here?

    });
    
    // $( "#cc_input" ).on( "keypress", inputDigitsOnly);

    $( "#storeCredit-dialog-form" ).dialog(
	{
		autoOpen: false,
		height: 300,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'gc-submit-form-button',
				click: function() 
				{	
				 	process_store_credit_payment();
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
			$('#gc-submit-form-button').attr("disabled", false);
			$('#cancel-form-button').attr("disabled", false);
			$('#store_credit_loading_image').hide();
			
			
			
			
		}

	});
	$('#storeCredit-dialog-form').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			  //alert('Looking up card number');
			  //lookUpCardNumber();
			  
			  process_store_credit_payment();
		}
    });
 $( "#storeCreditLookupModal" ).dialog(
	{
		autoOpen: false,
		height: 300,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Lookup Card number",
				id: 'submit-form-button',
				click: function() 
				{	
				 	lookUpCardNumber();
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
			$('#store_card_number').val('');
			
		},
		open: function()
		{
			//$('#submit-form-button').button('option', 'label', 'Submit');
			//$('#gc-submit-form-button').attr("disabled", false);
			//$('#cancel-form-button').attr("disabled", false);
			$('#store_card_number').focus();
			$('#store_card_number').select();
			$('#store_credit_lookup_image').hide();

			
			
			
		}

	});
	$('#storeCreditLookupModal').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			  lookUpCardNumber();
		}
    });    

    $( "#other-payment-dialog-form" ).dialog(
	{
		autoOpen: false,
		height: 300,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	process_other_payment();
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
			$('#non_payment_loading_image').hide();
			$('#non_payment_amount_input').focus();
			$('#non_payment_amount_input').select();
		}

	});
	$('#other-payment-dialog-form').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			  process_other_payment();
		}
    });
   


    
    
});

//refund Dialogs
$(function()
{
   // REFUNDS #######################################__---------------------------
   
    $( "#cashRefund" ).dialog(
	{
		autoOpen: false,
		height: 300,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	process_cash_refund();
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
			$('#cash_refund_loading_image').hide();
			$('#cash_refund_input').focus();
			$('#cash_refund_input').select();
		}

	});
    $( "#checkRefund" ).dialog(
	{
		autoOpen: false,
		height: 300,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	process_check_refund();
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
			$('#check_refund_loading_image').hide();
			$('#check_refund_input').focus();
			$('#check_refund_input').select();
		}

	});
    $( "#ccRefund" ).dialog(
	{
		autoOpen: false,
		height: 300,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	offline_cc_refund();
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
			if($('#refund_pos_payment_gateway_id').val() == 'false' )
			{
				$('#offlineCCRefund').hide();
				$('#onlineCCRefund').hide();
				$('#REFUND_CC_RESPONSE').hide();
				alert('Must select a cc processor - Set up the terminal for a default processor to avoid this message.');

			}
			else
			{
				if (online==true)
				{
				
					$('#offlineCCRefund').hide();
					$('#onlineCCRefund').show();
					$('#REFUND_CC_RESPONSE').hide();
				
				}
				else
				{
					$('#offlineCCRefund').show();
					$('#onlineCCRefund').hide();
					$('#REFUND_CC_RESPONSE').hide();

				}
			}
			$('#refundcc_loading_image').hide();

			$('#submit-form-button').button('option', 'label', 'Submit');
			$('#submit-form-button').attr("disabled", false);
			$('#cancel-form-button').attr("disabled", false);
			
			$('#refund_cc_input').focus();
			$('#refund_cc_input').select();
			
			
			
			
			

			

		}

	});   
	$( "#storeCreditRefundForm1" ).dialog(
	{
		autoOpen: false,
		height: 150,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'storeCreditRefundSubmit',
				click: function() 
				{	
				 	ISSUE_STORE_CREDIT();
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
			$('#storeCreditRefundSubmit').attr("disabled", false);
			$('#cancel-form-button').attr("disabled", false);
			$('#store_credit_refund_loading_image1').hide();
			
			//$('#enterStoreCreditCardNumber').show();
			

		}

	});  
	$('#store_credit_number').keypress(function(e) 
	{
		if (e.keyCode == $.ui.keyCode.ENTER) 
		{
			  ISSUE_STORE_CREDIT();
		}
    });

    $( "#otherRefund" ).dialog(
	{
		autoOpen: false,
		height: 300,
		width: 500,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Submit": {
				text: "Submit",
				id: 'submit-form-button',
				click: function() 
				{	
				 	process_cash_payment();
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
			$("#count_change_instructions").show();
			$('#loading_image').hide();
			$('#cash_input').val('0.00');
			$('#cash_input').focus();
			$('#cash_input').select();
		}

	});
});
//################################### PAGE FUNCTIONALITY #################################

function cashDialog()
{
	//$('#modal_div').html(html);
	
	$( "#cash-dialog-form" ).dialog( "open" );
	//$('#cash_input').focus();
	//$('#cash_input').select();

	
}
function ccDialog()
{
	//$('#modal_div').html(html);
	
	$( "#cc-dialog-form" ).dialog( "open" );
	//$('#tender_input').focus();
	//$('#tender_input').select();
}
function checkDialog()
{
	//$('#modal_div').html(html);
	
	$( "#check-dialog-form" ).dialog( "open" );
}
function storeCreditDialog()
{
	//$('#modal_div').html(html);
	
	$( "#storeCreditLookupModal" ).dialog( "open" );
}
function otherDialog()
{
	//$('#modal_div').html(html);
	
	$( "#other-payment-dialog-form" ).dialog( "open" );
}
function restoreEditContents()
{
	
	$('#payment_buttons').html('');
	$('#edit_contents_div').show();
}
function loadPaymentButtons()
{

	html = '<button class="button" style ="width:200px" onclick="cashDialog()">CASH</button>';
	html += '<button class="button" style ="width:200px" onclick="ccDialog()">CREDIT/DEBIT</button>';
	html += '<button class="button" style ="width:200px" onclick="checkDialog()">CHECK</BUTTON>';
	html += '<button class="button" style ="width:200px" onclick="storeCreditDialog()">STORE CREDIT/GIFT CARD</button>';
	html += '<button class="button" style ="width:200px" onclick="restoreEditContents()">CANCEL</button>';
	//html += '<div id="modal_div"></div>';
	//$('#modal_div').hide();
	$('#payment_buttons').html(html);
	$('#edit_contents_div').hide();

	
	


}
function ccRefundDialog()
{
	 
    $( "#ccRefund" ).dialog( "open" );
    
    
}
function storeCreditRefundDialog()
{
	 $( "#storeCreditRefundForm1" ).dialog( "open" );
}
function cashRefundDialog()
{
	
	 $( "#cashRefund" ).dialog( "open" );

}
function checkRefundDialog()
{
	
	$( "#checkRefund" ).dialog( "open" );

}
function otherRefundDialog()
{
	
	$( "#otherRefund" ).dialog( "open" );

}


//###################################  CASH /CHECK / STORE CREDIT / OTHERPAYMENT #################################

function process_cash_payment()
{

		if (isNumber(document.getElementById('cash_input').value))
		{

			amount = $('#cash_input').val() - $('#change').val();
			deposit_account = document.getElementById('cash_pos_account_id').value;
		
			//change submit to a spining wheel...and disable submit and cancel
			$('#submit-form-button').button('option', 'label', 'Please wait...');
			$('#submit-form-button').attr("disabled", true);
			$('#cancel-form-button').attr("disabled", true);
			$("#count_change_instructions").hide();
			$('#loading_image').show();
			
			var post_string = {};
			post_string['payment_type'] = 'Cash';
			post_string['ajax_request'] = 'CASH_CHECK_PAYMENT';
			post_string['amount'] = amount;
			post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
			post_string['deposit_account'] = deposit_account;
			post_string['applied_comments'] = 'cash tendered was ' + $('#cash_input').val() + ' Change given was ' + $('#change').val();
			var url = 'retail_sales_invoice.ajax.php';
			$.ajax({
					type: 'POST',
					url: url,
					data: post_string,
					async: true,
					success: 	function(response) 
					{
						//$('#loading_image').hide();
						//alert(response);
						console.log(response);
						var parsed_data = parseJSONdata(response);
												
						$( "#cash-dialog-form" ).dialog( "close" );
						reloadPage();	
						
				
					}
				});
			
			
			
			
		}
		else
		{
			alert("Please enter valid number");
		}
}
function process_cash_refund()
{

		if (isNumber(document.getElementById('cash_refund_input').value))
		{

			amount = $('#cash_refund_input').val();
			deposit_account = document.getElementById('cash_refund_pos_account_id').value;
		
			//change submit to a spining wheel...and disable submit and cancel
			$('#submit-form-button').button('option', 'label', 'Please wait...');
			$('#submit-form-button').attr("disabled", true);
			$('#cancel-form-button').attr("disabled", true);
			$('#cash_refund_loading_image').show();
			
			var post_string = {};
			post_string['payment_type'] = 'Cash';
			post_string['ajax_request'] = 'CASH_CHECK_PAYMENT';
			post_string['amount'] = amount;
			post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
			post_string['deposit_account'] = deposit_account;
			post_string['applied_comments'] = 'cash refunded was ' + $('#cash_refund_input').val();
			var url = 'retail_sales_invoice.ajax.php';
			$.ajax({
					type: 'POST',
					url: url,
					data: post_string,
					async: true,
					success: 	function(response) 
					{
						//$('#loading_image').hide();
						//alert(response);
						console.log(response);
						var parsed_data = parseJSONdata(response);
												
						if (parsed_data['payment_status'] == 'PAID')
						{
						
							//close the invoice => done already in php
							//$("#cash-dialog-form").dialog("close");
							location.reload();
						}
						else
						{

							location.reload();		
						}
						
				
					}
				});
			
			
			
			
		}
		else
		{
			alert("Please enter valid number");
		}
}
function calculate_change()
{
	checkInput2(document.getElementById('cash_input'),'.0123456789');
	
	
	if(document.getElementById('cash_input').value - document.getElementById('amount_due').value > 0)
	{
		document.getElementById('change').value =  round(document.getElementById('cash_input').value - document.getElementById('amount_due').value,2).toFixed(2);
	}
	else
	{
		$('#change').val('0.00');
	}
}
function validateCash()
{
	
}
//###################################  Check  PAYMENT/REFUND #################################
function process_check_payment()
{
		if ($('#license').val() != '')
		{
			if (isNumber(document.getElementById('check_input').value))
			{
				amount = $('#check_input').val();
				deposit_account = document.getElementById('pos_check_account_id').value;
		
				//change submit to a spining wheel...and disable submit and cancel
				$('#submit-form-button').button('option', 'label', 'Please wait...');
				$('#submit-form-button').attr("disabled", true);
				$('#cancel-form-button').attr("disabled", true);
				$('#loading_image').show();
			
				var post_string = {};
				post_string['payment_type'] = 'Check';
				post_string['ajax_request'] = 'CASH_CHECK_PAYMENT';
				post_string['amount'] = amount;
				post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
				post_string['deposit_account'] = deposit_account;
				post_string['applied_comments'] = 'License Number: ' + $('#license').val();
				var url = 'retail_sales_invoice.ajax.php';
				$.ajax({
						type: 'POST',
						url: url,
						data: post_string,
						async: true,
						success: 	function(response) 
						{
							//$('#loading_image').hide();
							//alert(response);
							console.log(response);
							var parsed_data = parseJSONdata(response);
												
							$( "#check-dialog-form" ).dialog( "close" );
							reloadPage();	
						
				
						}
					});
			
			
			
			
			}
			else
			{
				alert("Please enter valid number");
			}
		}
		else
		{
			alert("Please enter license number");
		}
}
function process_check_refund()
{
	if (isNumber(document.getElementById('check_refund_input').value))
	{
		amount = $('#check_refund_input').val();
		deposit_account = document.getElementById('pos_refund_checking_account_id').value;

		if(deposit_account =='false')
		{
			alert('select an account to write the check from');
		}
		else
		{
		//change submit to a spining wheel...and disable submit and cancel
		$('#submit-form-button').button('option', 'label', 'Please wait...');
		$('#submit-form-button').attr("disabled", true);
		$('#cancel-form-button').attr("disabled", true);
		$('#check_refund_loading_image').show();
	
		var post_string = {};
		post_string['payment_type'] = 'Check';
		post_string['ajax_request'] = 'CASH_CHECK_PAYMENT';
		post_string['amount'] = amount;
		post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
		post_string['deposit_account'] = deposit_account;
		post_string['applied_comments'] = '';
		var url = 'retail_sales_invoice.ajax.php';
		$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					//$('#loading_image').hide();
					//alert(response);
					console.log(response);
					var parsed_data = parseJSONdata(response);
					$( "#checkRefund" ).dialog( "close" );
					reloadPage();					
					
				
		
				}
			});
	
	}
	
	
	}
	else
	{
		alert("Please enter valid number");
	}


}
function reloadPage()
{
	$('#content').html( '<div style = "text-align: center;"  id="check_refund_loading_image"><img src="'+POS_ENGINE_URL +'/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="300" width="300"/></div>');
	location.reload();

}
function calculate_check_remainder()
{
	checkInput(document.getElementById('check_input'),'0123456789.');
	document.getElementById('check_remainder').value = round(  document.getElementById('amount_due').value - document.getElementById('check_input').value,2).toFixed(2);
}
//###################################  STORE CREzdIT  PAYMENT #################################
function calculate_gift_card_remainder()
{
	checkInput2(document.getElementById('gift_card_input'),'.0123456789');
	
	//check that the user did not over charge
	if(document.getElementById('gc_amount_due').value - document.getElementById('gift_card_input').value < 0)
	{
		//problem - set the input to the amount due
		document.getElementById('gift_card_input').value = document.getElementById('gc_amount_due').value;
	}

	if(document.getElementById('gift_card_value').value - document.getElementById('gift_card_input').value < 0)
	{
		//problemn
		$('#gc_remainder').val('0.00');
		$('#gift_card_input').val($('#gift_card_value').val());
		
	}
	else
	{
		
		document.getElementById('gc_remainder').value =  round(document.getElementById('gift_card_value').value - document.getElementById('gift_card_input').value,2).toFixed(2);
	}
}
function lookUpCardNumber()
{
	
	$('#store_credit_lookup_image').show();
	//ajax...
	var post_string = {};
		post_string['ajax_request'] = 'LOOKUP_STORE_CREDIT';

		post_string['store_card_number']  =$('#store_card_number').val();
		post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
		var url = 'retail_sales_invoice.ajax.php';
		$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					//alert(response);
					console.log(response);
					if(response =='No Data Found')
					{
						alert('No Data Found, Contact a Manager');
						$('#storeCreditLookupModal').dialog( "close" );
					}
					else
					{
						

						$('#storeCreditLookupModal').dialog( "close" );
						//$('#store_credit_lookup_image').hide();
						$('#storeCredit-dialog-form').dialog( "open" );
						
						//assign some values here
						$('#storeCreditDetails').html('<p>Card Number <input readonly id="store_card_number_returned" value="' + post_string['store_card_number'] +'"></p><p>Card Value: $<input readonly value="' + round(response,2).toFixed(2) +'" id="gift_card_value"></p>');

						if(document.getElementById('gc_amount_due').value - response < 0)
						{
							$('#gift_card_input').val(document.getElementById('gc_amount_due').value);
						}
						else
						{
							$('#gift_card_input').val(response);
						}
						calculate_gift_card_remainder();
						$('#gift_card_input').focus();
						$('#gift_card_input').select();
						
					}
				}
			});
	
	
}
function process_store_credit_payment()
{
		amount = $('#gift_card_input').val();
		if (isNumber(amount))
		{
			
			//deposit_account = document.getElementById('pos_other_account_id').value;
	
			//change submit to a spining wheel...and disable submit and cancel
			$('#submit-form-button').button('option', 'label', 'Please wait...');
			$('#submit-form-button').attr("disabled", true);
			$('#cancel-form-button').attr("disabled", true);
			$('#loading_image').show();
		
			var post_string = {};
			post_string['payment_type'] = 'Store Credit';
			post_string['ajax_request'] = 'STORE_CREDIT_PAYMENT';
			//this value comes from lookupCardNumber function in js
			
			if(typeof $('#store_card_number_returned').val() == 'undefined')
			{
				//incompatible card
				post_string['store_card_number']  ='';
			}
			else
			{	
				post_string['store_card_number'] = $('#store_card_number_returned').val();
			}
			//alert(post_string['store_card_number']);
			post_string['amount'] = amount;
			post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
			//post_string['deposit_account'] = deposit_account;
			post_string['applied_comments'] = ' ';
			var url = 'retail_sales_invoice.ajax.php';
			$.ajax({
					type: 'POST',
					url: url,
					data: post_string,
					async: true,
					success: 	function(response) 
					{
						//$('#loading_image').hide();
						//alert(response);
						console.log(response);
						var parsed_data = parseJSONdata(response);
										
						if (parsed_data['payment_status'] == "error") 
						{
    						alert("something went wrong in processing payment, like double use of the card.");
    						location.reload();
						}				
						else if (parsed_data['payment_status'] == 'PAID')
						{
					
							location.reload();
						}
						else
						{
							//probably unpaid
							location.reload();
						}
					
			
					}
				});
		
		
		
		
		}
		else
		{
			alert("Please enter valid number over zero");
		}
}
function ISSUE_STORE_CREDIT()
{
	
	$('#store_credit_refund_loading_image1').show();
	//ajax...
	var post_string = {};
		post_string['ajax_request'] = 'ISSUE_STORE_CREDIT';
		post_string['store_card_number']  = $('#store_credit_number_rf').val();
		post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
		var url = 'retail_sales_invoice.ajax.php';
		$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					console.log(response);
					$('#store_credit_refund_loading_image1').hide();
					var parsed_data = parseJSONdata(response);
					$('#store_credit_number').val('');
					if(parsed_data['card_type'] =='active')
					{
						alert('This card already has been issued and has value, please use a new card');
						$( "#storeCreditRefundForm1" ).dialog( "close" );
						//$( "#storeCreditRefundForm2" ).dialog( "open" );
						
					}
					else if (parsed_data['card_type'] =='new')
					{
						$( "#storeCreditRefundForm1" ).dialog( "close" );
						reloadPage();
						
					}
					else
					{
						alert(parsed_data['card_type']);
						$( "#storeCreditRefundForm1" ).dialog( "close" );
					}
					
				}
			});
	
	
}
function incompatible_card()
{
	//here we need to skip the card lookup
	$('#storeCreditLookupModal').dialog( "close" );
	$('#storeCredit-dialog-form').dialog( "open" );
						
	//assign some values here
	$('#store_card_number').val('');
	//$("#store_card_number_ro").attr("readonly", false);
	$("#gift_card_value").attr("readonly", false);
	$("#gift_card_input").select();
	$("#gift_card_input").focus();
}
function process_other_payment()
{
		if (isNumber(document.getElementById('non_payment_amount_input').value))
		{
			amount = $('#non_payment_amount_input').val();
			deposit_account = document.getElementById('pos_non_payment_account_id').value;
	
			//change submit to a spining wheel...and disable submit and cancel
			$('#submit-form-button').button('option', 'label', 'Please wait...');
			$('#submit-form-button').attr("disabled", true);
			$('#cancel-form-button').attr("disabled", true);
			$('#non_payment_loading_image').show();
		
			var post_string = {};
			post_string['payment_type'] = 'Other';
			post_string['ajax_request'] = 'NON_PAYMENT';
			post_string['amount'] = amount;
			post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
			post_string['deposit_account'] = deposit_account;
			post_string['applied_comments'] = ' ';
			post_string['comments'] = ' ';
			var url = 'retail_sales_invoice.ajax.php';
			$.ajax({
					type: 'POST',
					url: url,
					data: post_string,
					async: true,
					success: 	function(response) 
					{
						//$('#loading_image').hide();
						//alert(response);
						console.log(response);
						var parsed_data = parseJSONdata(response);
											
						if (parsed_data['payment_status'] == 'PAID')
						{
					
							location.reload();
						}
						else
						{
							location.reload();		
						}
					
			
					}
				});
		
		
		
		
		}
		else
		{
			alert("Please enter valid number");
		}
}
//###################################  CREDIT CARD PAYMENT #################################
function parseCCTrackData(card_data)
{
	//http://blog.opensecurityresearch.com/2012/02/deconstructing-credit-cards-data.html
	//removed this to prevent variables......
}
function manualCC()
{
	
	if(manual_entry)
	{
		manual_entry = false;
		$('#manual_button').prop('value', 'Keyed Entry (costs more to process cc)');
		$('#Manual_CC_DATA').hide();
	}else
	{
		manual_entry = true;
		$('#manual_button').prop('value', 'Swipe Entry');
		$('#Manual_CC_DATA').show();


	}
	
}
function changeGateway()
{
	//need to figure out if we are online or offline
	
	//if the payment gateway is not selected then everyone fucks off
	//console.log($('#pos_payment_gateway_id').val());
	if($('#pos_payment_gateway_id').val() == 'false' )
	{
		$('#offline_cc_entry').hide();
		$('#Manual_CC_DATA').hide();
		$('#cc_online').hide();
		$('#cc_offline').hide();
		online=false;
		$('#CC_RESPONSE').html('Must select a processor');

	}
	else
	{
		$('#offline_cc_entry').hide();
		$('#cc_online').hide();
		$('#Manual_CC_DATA').hide();
		$('#cc_offline').hide();
		var post_string = {};
		post_string['ajax_request'] = 'GATEWAY_CHANGE';
		post_string['pos_payment_gateway_id'] = $('#pos_payment_gateway_id').val();
	
		var url = 'retail_sales_invoice.ajax.php';
		$('#cc_loading_image').show();
		$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: false,
				success: 	function(response) 
				{
					console.log(response);
					$('#cc_loading_image').hide();
					if(response == 'online')
					{
						$('#offline_cc_entry').hide();
						$('#Manual_CC_DATA').hide();
						$('#Offline_CC_DATA').hide();
						$('#cc_online').show();
						online=true;
			
					}
					else
					{
						$('#offline_cc_entry').show();
						$('#cc_online').hide();
						$('#Manual_CC_DATA').hide();
						$('#Offline_CC_DATA').show();
						

					
						online=false;

					}
				
				
				}
				});
	}
	
	
}
function changeRefundGateway()
{
	//need to figure out if we are online or offline
	
	//if the payment gateway is not selected then everyone fucks off
	//console.log($('#pos_payment_gateway_id').val());
	if($('#refund_pos_payment_gateway_id').val() == 'false' )
	{
		$('#offlineCCRefund').hide();
		$('#onlineCCRefund').hide();
		online=false;
		$('#REFUND_CC_RESPONSE').html('Must select a processor');

	}
	else
	{
		$('#offlineCCRefund').hide();
		$('#onlineCCRefund').hide();
		var post_string = {};
		post_string['ajax_request'] = 'GATEWAY_CHANGE';
		post_string['pos_payment_gateway_id'] = $('#refund_pos_payment_gateway_id').val();
	
		var url = 'retail_sales_invoice.ajax.php';
		$('#refundcc_loading_image').show();
		$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: false,
				success: 	function(response) 
				{
					console.log(response);
					$('#refundcc_loading_image').hide();
					if(response == 'online')
					{
						$('#offlineCCRefund').hide();
						$('#onlineCCRefund').show();
						online=true;
			
					}
					else
					{
						$('#offlineCCRefund').show();
						$('#onlineCCRefund').hide();
						

					
						online=false;

					}
				
				
				}
				});
	}
	
	
}
//3 processes : swipe online, keyed entry online, and offline
function process_swipe_cc_payment(card_data)
{	
	//data coming over from swipe....
	//parsed_card_data = parseCCTrackData(card_data);
	
	if(log) console.log('logging CC Data');
	parse_data = true;
	if (parse_data)
	{
	
	var parsed_card_data = {};
	parsed_card_data['card_data'] = card_data;
	var tracks = card_data.split("?");
	
	if(log) console.log('tracks');
	if(log) console.log(tracks);
	parsed_card_data['track1'] = tracks[0];
	parsed_card_data['track2'] = tracks[1];
	//if there is a third track we might find it under tracks[2]
	
	//splitting the card data OPTION 1

	var track1_parsed = tracks[0].split("^");

	//console.log (track1_parsed);
	if(log) console.log('track1_parsed');
	if(log) console.log(track1_parsed);
	
	
	//track1 data....
	var card_number_track1 = track1_parsed[0].substring(2);
	
	
	parsed_card_data['card_number_track1'] = card_number_track1;
	
	var details2_1 = tracks[1].split(";");
	details2_1 = details2_1[1].split("=");


	var exp_date_track_1 = details2_1[1];
	exp_date_track_1 = exp_date_track_1.substring(0, exp_date_track_1.length - 1);
	exp_date_track_1 = exp_date_track_1.substring(2, 4) + "/" + exp_date_track_1.substring(0,2);
	parsed_card_data['exp_track1'] = exp_date_track_1;

	
	
	//now check if track one matches track 2...
	
	track2_parsed = tracks[1].split("=");

	
	card_number_track_2 = track2_parsed[0].substring(1);

	
	
	parsed_card_data['card_number_track_2'] = card_number_track_2;
	exp_date_track_2 = track2_parsed[1].substring(0,4);
	exp_date_track_2 = exp_date_track_2.substring(2, 4) + "/" + exp_date_track_2.substring(0,2);
	parsed_card_data['exp_date_track_2'] = exp_date_track_2;
	

	

	
	if(card_number_track1 == card_number_track_2 &&  exp_date_track_1 == exp_date_track_2)
	{
			//now make a security feature showing the last 4 digits only....
		
		parsed_card_data['secure_card_number'] = generateSecureCardNumber(card_number_track1);
		

		
		parsed_card_data['card_type'] =  getCreditCardCardType(card_number_track1);
		var names_1 = track1_parsed[1].split("/");
		parsed_card_data['first_name'] = names_1[1].trim();
		parsed_card_data['last_name'] = names_1[0].trim();

		if(log) console.log('parsed_card_data');
		if(log) console.log(parsed_card_data);

		
	}
	else
	{
		parsed_card_data = false;
	}
	
		//zero out the variables...
	
		tracks = '';
		track1_parsed = '';
		card_number_track1 = '';
		details2_1 = '';
		exp_date_track_1 = '';
		track2_parsed = '';
		card_number_track_2 = '';
		exp_date_track_2 = '';
		primary_account_number = '';
	}
	console.log('process_swipe_cc_payment2');
	if(parsed_card_data)
	{
		//console.log(parsed_card_data);
		$('#card_type').val(parsed_card_data['card_type']);
		$('#credit_card_number').val(parsed_card_data['secure_card_number']);
		$('#expiration').val(parsed_card_data['exp']);
		$('#card_holder').val(parsed_card_data['first_name']+ " " + parsed_card_data['last_name']);
	
		//parsed_card_data['track1'] is basically what we want???
	console.log('process_swipe_cc_payment3');
		$('#CC_SWIPE_INSTRUCTIONS').hide();
		$('#CC_DATA').hide();
		$('#cc_loading_image').show();
	

	
		var post_string = {};
		post_string['payment_type'] = 'Cash';
		post_string['ajax_request'] = 'CREDIT_CARD_PAYMENT';
		post_string['amount'] = $('#cc_input').val();
		post_string['card_data'] = parsed_card_data;
		post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
		post_string['pos_payment_gateway_id'] = $('#pos_payment_gateway_id').val();
		post_string['line'] = 'online';
		post_string['swipe'] = 'swipe';
	
		card_data = '';
		parsed_card_data = {};
		if (log) console.log('post_string');
		if (log) console.log(post_string);
		var url = 'retail_sales_invoice.ajax.php';
		$.ajax({
				type: 'POST',
				url: url,
				data: post_string,
				async: true,
				success: 	function(response) 
				{
					
					//here we would update the payment table - currently we will just refresh
					if (log) console.log(response);
					post_string = '';
					cc_response(response);
				
				}
				});
		post_string = '';
	}
	else
	{
		//error
		alert("Read Error");
		$( "#cc-dialog-form" ).dialog( "close" );
	}
    
	
	
	
	
	
}
function process_keyed_or_offline_CC_payment()
{
	if(online)
	{
		//test for missing data....
		OK = true;
		error_msg = 'Error \n';
		if($('#card_type').val() =='false')
		{
			OK = false;
			error_msg += 'Missing Card Type  \n';
		}
		if($('#credit_card_number').val() == '')
		{
			OK = false;
			error_msg += 'Missing CC Number  \n';
		}
		if($('#expiration').val() =='')
		{
			OK = false;
			error_msg += 'Missing Expiration \n';
		}
	
		/*if($('#ccv').val() =='false')
		{
			OK = false;
			error_msg += 'Missing CCV  </br>';
		}*/
		if(OK)
		{
			$('#cc_loading_image').show();
	
			var post_string = {};
			post_string['ajax_request'] = 'CREDIT_CARD_PAYMENT';
			post_string['amount'] = $('#cc_input').val();
			//post_sting['card_data'] = parsed_card_data; card data could not be read

			post_string['card_type'] = getCreditCardCardType($('#credit_card_number').val());
			post_string['card_number'] = $('#credit_card_number').val();
			post_string['exp'] = $('#expiration').val();
			//post_string['card_holder'] = $('#card_holder').val();
			//post_string['ccv'] = $('#ccv').val();

			post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
			post_string['pos_payment_gateway_id'] = $('#pos_payment_gateway_id').val();
	
	
			post_string['line'] = 'online';
			post_string['swipe'] = 'keyed';

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
						cc_response(response);

			
					}
					});
		}
		else
		{
			alert(error_msg);
		}
	}
	else
	{
		console.log('processing offline...');
		$('#cc_loading_image').show();
	
			var post_string = {};
			post_string['ajax_request'] = 'CREDIT_CARD_PAYMENT';
			post_string['amount'] = $('#cc_input').val();

			post_string['card_type'] = $('#offline_card_type').val();
			post_string['card_number'] = $('#offline_credit_card_number').val();
			post_string['applied_comments'] = '';
			post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
			post_string['pos_payment_gateway_id'] = $('#pos_payment_gateway_id').val();
	
			//we are offline so we have no cc to process, just data to enter....
	
			post_string['line'] = 'offline';
			post_string['swipe'] = 'keyed';

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
					
						$( "#cc-dialog-form" ).dialog( "close" );
						location.reload();
					
						var parsed_data = parseJSONdata(response);
											
						if (parsed_data['payment_status'] == 'PAID')
						{
					
							//close the invoice => done already in php
							//$("#cash-dialog-form").dialog("close");
							location.reload();
						}
						else
						{
					
							//add a line to the payments_tabl	
							//$( "#cash-dialog-form" ).dialog( "close" );	
							location.reload();		
						}
					
					
			
					}
					});
	}
	
	
	
}
function cc_response(response)
{
		var response_array = parseJSONdata(response);
		if(response_array[1] == 1)
		{
			/*echo 'Vesion ' . $response_array[0] .newline(); 
			echo 'APPROVED with code' . $response_array[1] .newline(); 
			echo 'Reason Code ' . $response_array[2] . newline();
			echo 'Reason Text ' . $response_array[3] . newline();  
			echo 'Authorization Code ' . $response_array[4] . newline(); 
			echo 'Transaction Code ' . $response_array[7] . newline(); 
			preprint($response_array); */
			
			
			$('#cc_loading_image').hide();
			console.log(response_array);
			$('#CC_RESPONSE').show();
			$('#CC_RESPONSE').html('APPROVED with code' + response_array[7]);

	
	
		}
		else if(response_array[1] == 2 || response_array[1] == 3 || response_array[1] == 4)
		{
			msg = 'Declined with code ' + response_array[1] + '/n';
			msg += 'Reason Code ' + response_array[2] + '/n';
			msg += 'Reason Text ' + response_array[3] + '/n';
			alert(msg);	
		}
		$( "#cc-dialog-form" ).dialog( "close" );
		//return false;
		reloadPage();
}
function offline_cc_refund()
{
	console.log('processing offline refund...');
	$('#refundcc_loading_image').show();

	var post_string = {};
	post_string['ajax_request'] = 'CREDIT_CARD_REFUND';
	post_string['amount'] = $('#refund_cc_input').val();

	post_string['card_type'] = $('#offline_refund_card_type').val();
	post_string['card_number'] = $('#offline_refund_card_num').val();
	post_string['applied_comments'] = '';
	post_string['pos_sales_invoice_id'] = pos_sales_invoice_id;
	post_string['pos_payment_gateway_id'] = $('#refund_pos_payment_gateway_id').val();

	//we are offline so we have no cc to process, just data to enter....

	post_string['line'] = 'offline';

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
			
				location.reload();
			
	
			}
			});
	
}

function calcuate_remainder_due()
{
	//checkInput(document.getElementById('tender_input'),'0123456789.');
	document.getElementById('remainder').value = round(  document.getElementById('amount_due').value - document.getElementById('cc_input').value,2).toFixed(2);
}


/*--------------------------------------------------------------------------------
-
-
-
-					REFUND
-
-
-
------------------------------------------------------------------------------------*/

function process_refund_swipe_cc_payment()
{
}
function process_refund_keyed_or_offline_CC_payment()
{
}

function calcuate_refund_remainder_due()
{
	document.getElementById('refund_remainder').value = round(  document.getElementById('refund_amount_due').value - document.getElementById('refund_cc_input').value,2).toFixed(2);
}

//**************PRINTING and emailing... etc.... **************************************//
function emailInvoice(pos_sales_invoice_id)
{
		//here is where we can check and add email....with cool pop up boxes....
		
		
		$('#email_button').attr('disabled','disabled');
		print_url = 'retail_sales_invoice.ajax.php';
		print_post = {};
		print_post['ajax_request'] = 'PRINT';
		print_post['pos_sales_invoice_id'] = pos_sales_invoice_id;
		print_post['type'] = 'email_pdf';
		$.post(print_url, print_post,
			function(response2) 
			{
				console.log(response2);
				$('#save_alert').html(response2);
				$("#save_alert").fadeOut(1600, "linear", function (){
					$("#save_alert").html('');
					$("#save_alert").show();
					$('#email_button').removeAttr('disabled');
					});
					
			}
		);
}
function openInvoiceInline(pos_sales_invoice_id)
{
	
	
	var url = 'retail_sales_invoice.ajax.php?ajax_request=PRINT&type=customer_inline&pos_sales_invoice_id='+pos_sales_invoice_id;
	var win = window.open(url, '_blank');
	

}
function sendInvoiceToPrinter(type, pos_sales_invoice_id)
{
		$('#customer_print_button').attr('disabled','disabled');
		$('#store_print_button').attr('disabled','disabled');
		$('#gift_receipt').attr('disabled','disabled');
		//print_message = $('#print_button').attr("value");
		print_url = 'retail_sales_invoice.ajax.php';
		print_post = {};
		print_post['pos_sales_invoice_id'] = pos_sales_invoice_id;
		print_post['type'] = type;
		print_post['ajax_request'] = 'PRINT';
		$.post(print_url, print_post,
			function(response2) 
			{
				$('#save_alert').html(response2);
				$("#save_alert").fadeOut(1600, "linear", function (){
					$("#save_alert").html('');
					$("#save_alert").show();
					$('#store_print_button').removeAttr('disabled');
					$('#customer_print_button').removeAttr('disabled');
					$('#gift_receipt').removeAttr('disabled');
					});
					
			}
		);

}


//######################################### A New listner? ##################
function inputDigitsOnly(e) 
{
 var chrTyped, chrCode=0, evt=e?e:event;
 if (evt.charCode!=null)     chrCode = evt.charCode;
 else if (evt.which!=null)   chrCode = evt.which;
 else if (evt.keyCode!=null) chrCode = evt.keyCode;

 if (chrCode==0) chrTyped = 'SPECIAL KEY';
 else chrTyped = String.fromCharCode(chrCode);

 //[test only:] display chrTyped on the status bar 
 console.log('inputDigitsOnly: chrTyped = '+chrTyped);

 //Digits, special keys & backspace [\b] work as usual:
 if (chrTyped.match(/\d|[\b]|SPECIAL/)) return true;
 if (evt.altKey || evt.ctrlKey || chrCode<28) return true;

 //Any other input? Prevent the default response:
 if (evt.preventDefault) evt.preventDefault();
 evt.returnValue=false;
 return false;
}
function getCreditCardCardType(card_number)
{
		
		var primary_account_number =  card_number.substring(0,1);
		if(card_number.length == 15)
		{
			return  "American Express";	
		}
		else if(primary_account_number == 4)
		{
			return "Visa";
		}
		else if(primary_account_number == 5)
		{
			return "Master Card";
		}
		else if(primary_account_number == 6)
		{
			return "Discover";
		}
		else
		{
			return false;
		}
}
function generateSecureCardNumber(card_number)
{
	 return "XXX " + card_number.substring(card_number.length-4, card_number.length);
}
function checkbox0or1(control)
{
	if($(control).is(':checked'))
	{
		return 1;
	}
	else
	{
		return 0;
	}
}
function updateSpecialOrder()
{
	$('#invoice_options').hide();
	ajax_url = 'retail_sales_invoice.ajax.php';
	var update_post = {};
	update_post['ajax_request'] = 'SPECIAL_ORDER_UPDATE';
	update_post['pos_sales_invoice_id'] = pos_sales_invoice_id;
	
	
	
	update_post['special_order'] = checkbox0or1($('#special_order'));
	
	$.post(ajax_url, update_post,
		function(response) 
		{
			console.log(response);
			$('#special_order').show();
				
		}
	);
	
}
function updateFollowUp()
{
	$('#invoice_options').hide();
	ajax_url = 'retail_sales_invoice.ajax.php';
	var update_post = {};
	update_post['ajax_request'] = 'FOLLOW_UP_UPDATE';
	update_post['pos_sales_invoice_id'] = pos_sales_invoice_id;
	update_post['follow_up'] = checkbox0or1($('#follow_up'));
	$.post(ajax_url, update_post,
		function(response) 
		{
			console.log(response);
			$('#follow_up').show();
				
		}
	);
}

