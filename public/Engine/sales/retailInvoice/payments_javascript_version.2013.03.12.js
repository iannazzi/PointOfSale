function calculateTotals()
{
	payments_table_object.copyHTMLTableDataToObject();
	payments_table_object.updateFooter();
}
function preparePost()
{
	return payments_table_object.prepareDynamicTableForPost();
}