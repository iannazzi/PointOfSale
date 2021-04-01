function setCancelDate()
{
	alert(document.getElementById('delivery_date').value);
}
function setPurchaseOrderNumber()
{
	//alert(document.getElementById('pos_manufacturer_brand_id').value);
	//alert(document.getElementById('delivery_date').value);
	if (typeof document.getElementById('pos_manufacturer_brand_id').value !== 'undefined')
	{
	brand_code = brand_code_id_lookup[document.getElementById('pos_manufacturer_brand_id').value];
	date = document.getElementById('delivery_date').value
	if (date != '' && brand_code !='')
	{
		d=date.split("-");

		//po_number = brand_code  +getMonth(date)+ getDays(date)  +getYYYear(date);
		po_number = brand_code  +d[1]+ d[2]  +d[0].substring(2,4);
		document.getElementById('purchase_order_number').value = po_number;
	}
	}
}
function validate_purchase_order()
{

}
