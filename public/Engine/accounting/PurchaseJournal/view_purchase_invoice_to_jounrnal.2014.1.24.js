function closePurchaseJournal(pos_purchases_journal_id)
{
if(confirm("Confirm Set to Close"))
			{
	//ajax call....
	//send a sql statement to be executed....
	var post_string = {};
	post_string['sql'] = "UPDATE pos_purchases_journal SET invoice_status='CLOSED' WHERE pos_purchases_journal_id=" + pos_purchases_journal_id;
	var url = POS_ENGINE_URL + '/includes/php/ajax_sql_statement.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log(response);
				alert("Closed");
				document.getElementById('invoice_status').value = 'CLOSED';
				
			},
			error: function(xhr, status, error) 
			{
  				//var err = eval("(" + xhr.responseText + ")");
  				console.log('error');
			}
		});
	}
}
function setPOInvoiceToComplete(pos_purchases_journal_id)
{
	
	if(confirm("Confirm Set to Complete"))
	{
		//ajax call....
	//send a sql statement to be executed....
	var post_string = {};
	post_string['sql'] = "UPDATE pos_purchase_orders SET invoice_status='COMPLETE' WHERE pos_purchase_order_id IN (SELECT pos_purchase_order_id FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id=" + pos_purchases_journal_id + ")";
	var url = POS_ENGINE_URL + '/includes/php/ajax_sql_statement.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log(response);
				alert("Complete - Refreshing the page");
				location.reload();
				
			},
			error: function(xhr, status, error) 
			{
  				//var err = eval("(" + xhr.responseText + ")");
  				console.log('error');
			}
		});
	
	}
	
}
function setPOInvoiceToPaid(pos_purchases_journal_id)
{
	

			if(confirm("Confirm Set to Paid"))
			{
				//ajax call....
	//send a sql statement to be executed....
	var post_string = {};
	post_string['sql'] = "UPDATE pos_purchases_journal SET payment_status='PAID', invoice_status='CLOSED' WHERE pos_purchases_journal_id=" + pos_purchases_journal_id;
	var url = POS_ENGINE_URL + '/includes/php/ajax_sql_statement.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log(response);
				alert("Complete - Refreshing the page");
				location.reload();
				
			},
			error: function(xhr, status, error) 
			{
  				//var err = eval("(" + xhr.responseText + ")");
  				console.log('error');
			}
		});
	
			}
		
	
	
	
	
}