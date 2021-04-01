function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}
function createCookiesAllFormValues(formId)
{
	//Function to create cookies for all form elements.
	var str = '';
	var elem = document.getElementById(formId).elements;
	for(var i = 0; i < elem.length; i++)
	{
		//str += "Type: " + elem[i].type + " Name: " + elem[i].name + " ID: " + elem[i].id + " Value: " + elem[i].value + "\r\n";
		createCookie(elem[i].name,elem[i].value,1);
		//eraseCookie(elem[i].name);
	} 
}
function eraseCookiesAllFormValues(formId)
{
	//Function to create cookies for all form elements.
	var str = '';
	var elem = document.getElementById(formId).elements;
	for(var i = 0; i < elem.length; i++)
	{
		eraseCookie(elem[i].name);
	} 
}