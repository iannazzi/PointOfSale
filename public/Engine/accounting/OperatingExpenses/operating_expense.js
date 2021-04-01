window.onload = init;
function init()
{
	document.getElementById('year').focus();
}

function validate_year(control)
{
	minDate = 1900;
	maxDate = 2100;
	if (parseInt(control.value) > minDate && parseInt(control.value) < maxDate)
	{
		//OK
	}
	else
	{
		alert("Date: " + control.value + " is not between the values of " + minDate + " and " + maxDate);
		control.focus();
		control.select();
	}
}