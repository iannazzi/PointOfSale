$(function()
{   
	$( "#print-select-dialog-form" ).dialog(
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
				 	pos_printer_id = $('#pos_printer_id').val();
				 	printCheck(pos_printer_id);
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
			$('#loading_image').hide();
		}

	});
});
function selectPrinter()
{
	$( "#print-select-dialog-form" ).dialog( "open" );
}
function printCheck(pos_printer_id)
{
		console.log(pos_printer_id);
		
		
		//can we send it to a check printer or open it inline?
		//currently we will code it to open inline
		
		if(pos_printer_id == "false")
		{
			console.log('inline');
			open_win('payments_ajax.php?ajax_request=PRINT_CHECK&method=INLINE&pos_payments_journal_id='+pos_payments_journal_id);
		}
		else
		{
			
			//this is the code to send it to a printer
			$('#print_button').attr('disabled','disabled');
			//print_message = $('#print_button').attr("value");
			print_url = 'payments_ajax.php';
			print_post = {};
			print_post['pos_payments_journal_id'] = pos_payments_journal_id;
			print_post['ajax_request'] = 'PRINT_CHECK';
			print_post['method'] = 'PRINT_QUEUE';
			print_post['pos_printer_id'] = pos_printer_id;
			$.post(print_url, print_post,
				function(response2) 
				{
					$('#print_alert').html(response2);
					$("#print_alert").fadeOut(1600, "linear", function (){
						$("#print_alert").html('');
						$("#print_alert").show();
						$('#print_button').removeAttr('disabled');
					
						});
					
				}
			);
		}
}