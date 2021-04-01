//recieve_purchase_order.form.js
//This file will help us get the upc codes into the system

//Need to set focus to



function updateFooter()
{
	var tfoot = document.getElementById("receive_table_tfoot");
	tfoot.rows[0].cells[check_in_column].innerHTML = calculateColumnTotal("receive_table", check_in_column);
	 document.getElementById("ra_required").checked = true;
}
