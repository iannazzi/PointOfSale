function updateAmountDue()
{
	needToConfirm=true;
	invoice_multiSelect = document.getElementById('pos_general_journal_id[]');
	var invoice_total = 0.0;
	var payments_total = 0.0;
	for (x=0;x<invoice_multiSelect.length;x++)
	 {
		if (invoice_multiSelect[x].selected)
		{
			 if(invoice_multiSelect[x].value != 'false')
			 {
			 	for(j=0;j<open_invoices.length;j++)
			 	{
			 		if (open_invoices[j].pos_general_journal_id == invoice_multiSelect[x].value)
			 		{
			 			invoice_total = invoice_total + parseFloat(open_invoices[j].entry_amount);
			 			payments_total = payments_total + parseFloat(open_invoices[j].payments_applied);
			 		}
			 	}
			 }
		}
	 }
	document.getElementById('amount_due').value = round2(invoice_total-payments_total,2) ;
	document.getElementById('payment_amount').value = round2(invoice_total-payments_total,2);

}