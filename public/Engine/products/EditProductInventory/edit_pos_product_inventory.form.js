// this file will have several functions: 
// Create the table and display the inventory table header
// Create the table data
// Process the data
function updateInventoryTable(control2) 
{
	createCookie('inventory_store_id',control2.options[control2.selectedIndex].value,7);	
	//reload the page to refresh manufacturer info
	window.location.reload()
}
function createInventoryTableHeader()
{
}
function createInventoryTableBody()
{
}

function updateInventory()
{
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}