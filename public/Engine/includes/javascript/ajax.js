// Script 13.2 - ajax.js

/*	This page creates an Ajax request object.
 *	This page is included by other pages that 
 *	need to perform an XMLHttpRequest.
 */
 
/* If you have an Ajax application that performs
multiple Ajax transactions in the
same Web browser, you’ll want to modify
the code in ajax.js so that a JavaScript
function returns an XMLHttpRequest
object. Then you would call this function,
assigning the returned value to a new
variable for each separate Ajax transaction.
This may make more sense to you
once you’ve completed the first Ajax
example.
*/


// Initialize the object:
var ajax = false;
//alert('ajax included');

// Create the object...

// Choose object type based upon what's supported:
if (window.XMLHttpRequest) {

	// IE 7, Mozilla, Safari, Firefox, Opera, most browsers:
	ajax = new XMLHttpRequest();
	
} else if (window.ActiveXObject) { // Older IE browsers

	// Create type Msxml2.XMLHTTP, if possible:
	try {
		ajax = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e1) { // Create the older type instead:
		try {
			ajax = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e2) { }
	}
	
}

// Send an alert if the object wasn't created.
if (!ajax) {
	alert ('Some page functionality is unavailable.');
}

