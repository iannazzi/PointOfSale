function updateAmountDue()
{
	needToConfirm=true;

	invoice_multiSelect = document.getElementById('pos_general_journal_id[]');
	var invoice_total = 0.0;
	var minimum_total = 0.0;

	for (x=0;x<invoice_multiSelect.length;x++)
	 {
		if (invoice_multiSelect[x].selected)
		{
			 if(invoice_multiSelect[x].value != 'false')
			 {
			 	for(j=0;j<statements.length;j++)
			 	{
			 		if (statements[j].pos_general_journal_id == invoice_multiSelect[x].value)
			 		{
			 			invoice_total = invoice_total + parseFloat(statements[j].entry_amount);
						minimum_total = minimum_total + parseFloat(statements[j].minimum_amount_due);
			 		}
			 	}
			 }
		}
	 }	 
	document.getElementById('amount_due').value = round(invoice_total,2);
	document.getElementById('payment_amount').value = round(invoice_total,2);
	document.getElementById('minimum_amount_due').value = round(minimum_total,2);
}

