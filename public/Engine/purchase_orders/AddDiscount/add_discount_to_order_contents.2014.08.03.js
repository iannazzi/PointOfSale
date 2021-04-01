function copyQuantity()
{
		var tbody = document.getElementById("discount_table");
		var rowCount = tbody.rows.length;
		for(var i = 0;i<rowCount;i++)
		{
			tbody.rows[i].cells[11].childNodes[0].value = tbody.rows[i].cells[10].innerHTML;
		}
		updateAdjustedPrice();
		updateFooter();
		 updateTotals();
		
}
function updateShowDiscount(control)
{
		var tbody = document.getElementById("discount_table");
		var rowCount = tbody.rows.length;
		for(var i = 0;i<rowCount;i++)
		{
			tbody.rows[i].cells[8].childNodes[0].value = round2(myParseFloat(tbody.rows[i].cells[7].innerHTML) * myParseFloat(control.value)/100,2);
		}
		updateAdjustedPrice();
		updateFooter();
		 updateTotals();
}
function updateFooter()
{
	var tfoot = document.getElementById("discount_table_tfoot");
	

	//tfoot.rows[0].cells[7].innerHTML = round2(calculateinnerHTMLColumnTotal("discount_table",7),2);
	//tfoot.rows[0].cells[8].innerHTML = round2(calculateColumnTotal("discount_table",8),2);
	//tfoot.rows[0].cells[9].innerHTML = round2(calculateinnerHTMLColumnTotal("discount_table",9),2);
	//tfoot.rows[0].cells[10].innerHTML = calculateinnerHTMLColumnTotal("discount_table",10);
	tfoot.rows[0].cells[11].innerHTML = calculateColumnTotal("discount_table",11);
	
	
}
function updateAdjustedPrice()
{
	var tbody = document.getElementById("discount_table");
	var rowCount = tbody.rows.length;
	for(var row = 0;row<rowCount;row++)
	{
		tbody.rows[row].cells[9].innerHTML = round2(parseFloat(tbody.rows[row].cells[7].innerHTML) - parseFloat(tbody.rows[row].cells[8].childNodes[0].value),2);
	}
	 updateTotals();
}
function updateTotals()
{
	
	var tbody = document.getElementById("discount_table");
	var rowCount = tbody.rows.length;
	var order_cost = 0;
	var total_discount = 0;
	for(var row = 0;row<rowCount;row++)
	{
		order_cost = order_cost + (parseFloat(tbody.rows[row].cells[7].innerHTML)*parseInt(tbody.rows[row].cells[10].innerHTML));
		total_discount = total_discount + (parseFloat(tbody.rows[row].cells[8].childNodes[0].value)*parseInt(tbody.rows[row].cells[11].childNodes[0].value));
		
	}
	
	document.getElementById("order_cost").value = round2(order_cost,2);
	document.getElementById("total_discount").value = round2(total_discount,2);
	document.getElementById("discounted_order_cost").value = round2(order_cost - total_discount,2);
}