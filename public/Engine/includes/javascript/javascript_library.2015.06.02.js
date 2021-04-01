//Needed for page navigation, site functionality
function modifyURL(url_string)
{
	//now the bullshit begins...
	//create this element to access the url 
	var url = window.location.href;
	var protocal = window.location.protocol;
	var pathname = window.location.pathname;
	var pathArray = window.location.pathname.split( '/' );
	var filename = pathArray[pathArray.length-1];
	
	var a = $('<a>', { href:url } )[0];
	//console.log('host ' + a.hostname);
	//console.log('pathname ' + a.pathname);
	//console.log('search ' + a.search);
	//console.log('hash ' + a.hash);
	
	//recreate the url
	var new_url =  a.hostname + a.pathname + url_string;
	//console.log('new_url ' + new_url);
	var pageHTML = document.documentElement.innerHTML;
	var title = document.title;
	window.history.pushState('object or string', title, filename + url_string);
}	

//Need for Mobile.....




/*****************************************DEBUG HELPERS *******************************/
function ifFnExistsCallIt(fnName)
{
   //pass in a function name as a string, like caclulateTotals()
   fn = window[fnName];
   fnExists = typeof fn === 'function';
   if(fnExists)
      fn();
}
function ifCalculateTotalsExists()
{
	if(typeof calculateTotals == 'function')
	{
		calculateTotals();
	}
}
function round(num, dec) 
{
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
function round2(num, dec)
{
	if(trim(num) == '')
	{
		num = 0;
	}
	
	if (!isNaN(num))
	{
		
		number=parseFloat(num);
		//number = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
		return number.toFixed(dec);
	}
	else
	{
		return num;
	}
}
function myParseFloat(value)
{
	if(value =='' || value == '0' || value =='null' || value == null)
	{
		return 0;
	}
	else
	{
		return parseFloat(value);
	}
}
function myParseInt(value)
{
	if(value =='' || value == '0' || value =='null' || value == null)
	{
		return 0;
	}
	else
	{
		return parseInt(value);
	}
}
function show2DArray(array)
{
	debug = '';
	for (i=0;i<array.length;i++)
	{
		for(j=0;j<array[0].length;j++)
		{
			debug = debug + ' ' + array[i][j];
		}
		debug = debug + "\r\n";
	}
	alert(debug);
}
function dumpProps(obj, parent) {
	//This function goes through an object and states the index and the property recusively
	// usage dumpProps(object);
	
   // Go through all the properties of the passed-in object
   for (var i in obj) 
   {
      // if a parent (2nd parameter) was passed in, then use that to
      // build the message. Message includes i (the object's property name)
      // then the object's property value on a new line
      if (parent) { var msg = parent + "." + i + "\n" + obj[i]; } else { var msg = i + "\n" + obj[i]; }
      // Display the message. If the user clicks "OK", then continue. If they
      // click "CANCEL" then quit this level of recursion
      if (!confirm(msg)) { return; }
      // If this property (i) is an object, then recursively process the object
      if (typeof obj[i] == "object") 
      {
         if (parent) { dumpProps(obj[i], parent + "." + i); } else { dumpProps(obj[i], i); }
      }
   }
}
function checkInput(objName,validInput)
{
	
	//Function will check input against the valid input.... use to check for numeric, currency, miles, letters
	
	// originally created to watch for cup sizes.....
	
	// First conver to uppercase
	objName.value=objName.value.toUpperCase();
	//get the last charachter entered and evaulate it....
	ch = objName.value.slice(objName.value.length -1, objName.value.length);
	//alert(ch);
	charOK = "false";
	//if the characther matches the cupSizes, then allow it. Otherwise ignore it all
	for (j = 0;  j < validInput.length;  j++)
	{

			if (ch == validInput.charAt(j))
			{
				//charachter is ok, do nothing
				charOK = "true";
			} 
			
	}
	// check if we found an OK match, otherwise erase it
	if (charOK != "true")
	{
		//erase the incoming value
		objName.value = objName.value.slice(0, objName.value.length-1);
	} 


}
function checkInput2(objName,validInput)
{
	
	//Function will check input against the valid input.... use to check for numeric, currency, miles, letters

	//get the last charachter entered and evaulate it....
	ch = objName.value.slice(objName.value.length -1, objName.value.length);
	//alert(ch);
	charOK = "false";
	//if the characther matches the cupSizes, then allow it. Otherwise ignore it all
	for (j = 0;  j < validInput.length;  j++)
	{

			if (ch == validInput.charAt(j))
			{
				//charachter is ok, do nothing
				charOK = "true";
			} 
			
	}
	// check if we found an OK match, otherwise erase it
	if (charOK != "true")
	{
		//erase the incoming value
		objName.value = objName.value.slice(0, objName.value.length-1);
	} 


}
function open_win(url)
{
	window.location = url;
}

//*******************COOKIES**********************************************
function readAllCookiesFromForm(formId)
{
	var cookie = [];

	var elem = document.getElementById(formId).elements;
	for(var i = 0; i < elem.length; i++)
	{
		cookie[i] = readCookie(elem[i].name);
	} 
	return cookie;
}
function trim(strText) 
{
    strText += '';
    strText=strText.replace('\t','');
    //alert(strText.length);
    // this will get rid of leading spaces - not tab however
    while (strText.substring(0,1) == ' ')
    {
        strText = strText.substring(1, strText.length);
    }

    // this will get rid of trailing spaces
    while (strText.substring(strText.length-1,strText.length) == ' ')
    {
        strText = strText.substring(0, strText.length-1);
    }
	//alert(strText.length);
   return strText;
}

function getTbodyCookies(tbodyName)
{
  var pairs = document.cookie.split(";");
  var cookies = [];
  for (var i=0; i<pairs.length; i++){
    var pair = pairs[i].split("=");
    //alert(pair[0].search(tbodyName) );
    if (pair[0].search(tbodyName) != "-1")
    {
    	//found the id
    	cookies[trim(pair[0])] = unescape(pair[1]);
    	//alert("Cookie: " + pair[0] + " Value: " + unescape(pair[1]) );
    }
  }
  return cookies;
}
function readCookie(name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
function createCookiesAllFormValues(formId)
{
	//Doesn't work well in safari - use createCookie and be specific
	var elem = document.getElementById(formId).elements;
	for(var i = 0; i < elem.length; i++)
	{
		//str += "Type: " + elem[i].type + " Name: " + elem[i].name + " ID: " + elem[i].id + " Value: " + elem[i].value + "\r\n";
		createCookie(elem[i].name,elem[i].value,.1);
	} 
	//alert(str);
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
function createCookie(name,value,days) 
{
//note only something like 30 cookies can be stored.. so basically don't use them, they might kill the session

	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
function eraseCookie(name) {
	createCookie(name,"",-1);
}
function tellmeyourname(control)
{
		
	alert("you are: " + control.id + "\n" +
	"Your cell id is: " + control.parentNode.id + "\n" + 
	"Your column number is: " + control.parentNode.cellIndex + "\n" + 
	"Your Row id is: " + control.parentNode.parentNode.id + "\n" +  
	"Your actual row is : " + control.parentNode.parentNode.rowIndex + "\n" +
	"Your thead,tbody, or tfoot id: " + control.parentNode.parentNode.parentNode.id + "\n" +

	"The table name is : " + control.parentNode.parentNode.parentNode.parentNode.id); // this is the table
	//	"Your tbody row number: " + control.parentNode.parentNode.parentNode.rows.rowIndex + "\n" +
	

}
function wait(msecs)
{
	var start = new Date().getTime();
	var cur = start
	while(cur - start < msecs)
	{
		cur = new Date().getTime();
	}
} 
function noEnter(e) 
{
	//this gets rid of the enter key if it is inadvetently submitting the form
	//Add the following attribute into each input type="text" tag(s) in your form:
	//onkeypress="return noEnter()"
	//onkeypress=”return event.keyCode!=13″
	//element.onkeypress = function(e){return noEnter(e);}
  	var e=window.event || e;
	return e.keyCode!=13
        
}

function disableEnterKey(e)
{
     var key;     
     if(window.event)
          key = window.event.keyCode; //IE
     else
          key = e.which; //firefox     

     return (key != 13);
}
function isDate(txtDate)
{
    //checking format 2012-03-01
  //alert(txtDate.substring(4, 5) + ' ' + txtDate.substring(7, 8) + ' ' + txtDate.substring(0, 4) + ' ' +   txtDate.substring(5, 7) + ' ' + txtDate.substring(8, 10));
   txtDate = trim(txtDate);
   var objDate,  // date object initialized from the txtDate string
        mSeconds, // txtDate in milliseconds
        day,      // day
        month,    // month
        year;     // year
    // date length should be 10 characters (no more no less)
    if (txtDate.length !== 10) {
        //alert('length');
        return false;
    }
    // fift and seventh character should be '/'
    if (txtDate.substring(4, 5) !== '-' || txtDate.substring(7, 8) !== '-') {
        //alert('dash');
        return false;
    }
    // extract month, day and year from the txtDate (expected format is yyyy-dd-mm)
    // subtraction will cast variables to integer implicitly (needed
    // for !== comparing)
    month = txtDate.substring(5, 7) - 1; // because months in JS start from 0
    day = txtDate.substring(8, 10) - 0;
    year = txtDate.substring(0, 4) - 0;
    // test year range
    if (year < 1000 || year > 3000) {
        //alert('year');
        return false;
    }
    // convert txtDate to milliseconds
    mSeconds = (new Date(year, month, day)).getTime();
    // initialize Date() object from calculated milliseconds
    objDate = new Date();
    objDate.setTime(mSeconds);
    // compare input date and parts from Date() object
    // if difference exists then date isn't valid
    if (objDate.getFullYear() !== year ||
        objDate.getMonth() !== month ||
        objDate.getDate() !== day) {
       //alert('compare');
       return false;
    }
    // otherwise return true
    return true;
}
function newline()
{
	return "\r\n";
}
function is_array(input)
{
    return typeof(input)=='object'&&(input instanceof Array);
}
function deleteMessage(pos_message_id)
{
		var get_string = {'pos_message_id':pos_message_id}; 
		$.get(POS_ENGINE_URL + "/includes/php/ajax_delete_message.php", get_string,
						function(response) 
						{
							if(response == 'deleted')
							{
								//this is where we would go to the next message...
								//alert($('#display_message').text());	
								//$('#display_message').text('refresh for new mesage');

							}
							else
							{
								alert(response);
							}
						});	
}



function cancelForm()
{
	window.location = cancel_location;
}
function deleteMysqlEntry(table, id_name, id_value)
{
	var r=confirm("Are You Positive?");
	if (r==true)
 	{
  				var get_string = {'table':table,'id_name' : id_name,'id_value': id_value}; 
				$.ajaxSetup({async: false});
				$.get("../../includes/php/ajax_delete_mysql_entry.php", get_string,
						function(response) {
						if(response == 'deleted')
						{
							alert("Deleted");
						}
						else
						{
							alert(response);
						}
					});
		window.location = complete_location;
  	}
	else
  	{
  		//alert("You pressed Cancel!");
  	}
}
/**********************************TABLE CREATION / MANIPULATION FUNCTIONS *********************/

function simpleAJAXSelect(sql)
{
				//can't return the response?
				var get_string = {'sql':encodeURI(sql)}; 
				//alert (encodeURI(sql));
				//$.ajaxSetup({async: false});
				$.get("../../includes/php/ajax_simple_select.php", get_string,
						function(response) {
						alert (response);
						return response;
					});
}
function loadPO()
{
	pos_manufacturer_id=document.getElementById('pos_manufacturer_id').value;
	sql = "SELECT pos_purchase_order_id, purchase_order_number FROM pos_purchase_orders WHERE purchase_order_status='OPEN' AND pos_manufacturer_id=" +pos_manufacturer_id;
	var get_string = {'sql':encodeURI(sql)}; 
				//alert (encodeURI(sql));
	//$.ajaxSetup({async: false});
	$.getJSON("../../includes/php/ajax_simple_select.php", get_string,
	function(response) {
						
						if (response== null)
						{
						}
						else
						{
						
						values = [];
						captions = [];
						for(i=0;i<response.length;i++)
						{
							values[i] = response[i].pos_purchase_order_id;
							captions[i] = response[i].purchase_order_number;
						}
						addValuesToSelect('pos_purchase_order_id', values, captions);
						}
					});
	
	
}
function addValuesToSelect(element_id, values, captions)
{
	element = document.getElementById(element_id);
	removeSelectOptions(element_id);
	option = document.createElement('option');
		option.value = 'false';
		option.appendChild(document.createTextNode('Select a Value'));
		element.appendChild(option);
	for(i=0;i<values.length;i++)
	{
		option = document.createElement('option');
		option.value = values[i];
		option.appendChild(document.createTextNode(captions[i]));
		element.appendChild(option);
	}	
	
}
function changeDate(reference_id, change_id, days)
{
	var reference_element =  document.getElementById(reference_id);
	var change_element =  document.getElementById(change_id);
	change_element.value = dateAddDays(reference_element.value, parseInt(days));
}
function parseDate(input, format) 
{
	//errors arrive in using my favorite format, so we need to parse it
  format = format || 'yyyy-mm-dd'; // default format
  var parts = input.match(/(\d+)/g), 
      i = 0, fmt = {};
  // extract date-part indexes from the format
  format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; });

  return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']]);
}
function convertDate(yyyy_mm_dd)
{
	d=yyyy_mm_dd.split("-");
	return d[1]+'/'+ d[2] +'/' +d[0];
}
function dateAddDays( /*string yyyy-mm-dd*/ dateString, /*int*/ ndays)
{
	//dateString= dateString.replace('-','/');
	dateString = convertDate(dateString);
	//alert(dateString);
	var actualDate = new Date(dateString); // convert to actual date
	var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+ndays); // create new increased 

	return (newDate.toYMD());
}
function compareTwoDates(date1, date2)
{
	//return -1 date one is before date 2, 0 same, 1 date one is after date 2
	dateString1 = convertDate(date1);
	//alert(date1 + ' ' + dateString1);
	dateString2 = convertDate(date2);
	var date1_new = new Date(dateString1);
	var date2_new = new Date(dateString2);
	if(date1_new.getTime() == date2_new.getTime())
	{
		return 0;
	}
	else if (date1_new.getTime() < date2_new.getTime())
	{
		return -1;
	}
	else
	{
		return 1;
	}
}
function getDays(/*string yyyy-mm-dd*/ dateString)
{
	var actualDate = new Date(dateString); // convert to actual date
	var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+1);
 	days = newDate.getDate();
 	day_string =  addZeroToDateString(days.toString());
 	return day_string;

}
function addZeroToDateString(string)
{
	if (string.length == 1)
	{
		string = '0' + string;
	}
	return string;
}
function getMonth(/*string yyyy-mm-dd*/ dateString)
{
	var actualDate = new Date(dateString); // convert to actual date
	var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+1);
	var month = newDate.getMonth() + 1;
	month_string = addZeroToDateString(month.toString());
 	return month_string; 
}
function getYear(/*string yyyy-mm-dd*/ dateString)
{
	var actualDate = new Date(dateString); // convert to actual date
	var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+1);
	year = newDate.getFullYear(); 
	return year.toString();
}
function getYYYear(/*string yyyy-mm-dd*/ dateString)
{
	var actualDate = new Date(dateString); // convert to actual date
	var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+1);
 	year = newDate.getFullYear();
 	year_string = year.toString()
 	return year_string.substring(2,4);
}

(function() {
    Date.prototype.toYMD = Date_toYMD;
    function Date_toYMD() {
        var year, month, day;
        year = String(this.getFullYear());
        month = String(this.getMonth() + 1);
        if (month.length == 1) {
            month = "0" + month;
        }
        day = String(this.getDate());
        if (day.length == 1) {
            day = "0" + day;
        }
        return year + "-" + month + "-" + day;
    }
})();
function secondsToTimeString(seconds)
{
	//var totalSec = new Date().getTime() / 1000;
	hours = parseInt( seconds / 3600 ) % 24;
	minutes = parseInt( seconds / 60 ) % 60;
	seconds = seconds % 60;
	
	result = (hours < 10 ? "0" + hours : hours) + " Hours " + (minutes < 10 ? "0" + minutes : minutes) + " Minutes " + (seconds  < 10 ? "0" + seconds : seconds) + " Seconds";
	return result;
}
function removeSelectOptions(element_id)
{
  var element = document.getElementById(element_id);
  var i;
  for (i = element.length - 1; i>=0; i--)
	{
      element.remove(i);
    
  }
}




function  PlaySoundV3(filename)
{   
document.getElementById("sound_file").innerHTML='<audio autoplay="autoplay"><source src="' + filename + '.mp3" type="audio/mpeg" /><source src="' + filename + '.ogg" type="audio/ogg" /><embed hidden="true" autostart="true" loop="false" src="' + filename +'.mp3" /></audio>';
}
function PlaySoundV2(soundID) 
{
	//<embed src="success.wav" autostart="false" width="0" height="0" id="beep" enablejavascript="true">
	
  var sound = document.getElementById(soundID);
 

  	try
  	{
  		sound.Play();
  		console.log('sound.Play() success');
  		return;
  	}
  	catch(err)
  	{
  		console.log('sound.Play() error');
  	}
  	try
  	{
  		sound.play();
  		console.log('sound.play() success');
  		return;
  	}
  	catch(err)
  	{
  		console.log('sound.play() error');
  	}
  
}
function PlaySound(soundID) 
{
	//<embed src="success.wav" autostart="false" width="0" height="0" id="beep" enablejavascript="true">
	
  var sound = document.getElementById(soundID);
 
 	//curently this is breaking the android browsers....so fuck em...
 	
  if (browser == 'Google Chrome')
  {
  	sound.play();
  }
  else if (browser == 'Android Mobile')
  {
  }
  else
  {
  	try
  	{
  		sound.Play();
  	}
  	catch(err)
  	{
  		console.log('attempting to play sound....failed');
  	}
  }
}
function ShowTimeoutWarning ()
{
    window.alert( "You will be automatically logged out in five minutes unless you do something!" );
}
function redirectOnTimeout()
{
	var t=setTimeout("javascript statement",milliseconds);
}
// ***** Session Timeout Warning and Redirect mReschke 2010-09-29 ***** //
//need to turn this into AJAX function
function InitSessionTimer() 
{
    /* mReschke 2010-09-29 */
    warn_sec = 12* 59 * 60 * 1000;             //Warning time in milliseconds
    timeout_sec = 12*60 * 60 * 1000;          //Actual timeout in milliseconds
    show_warning = true;
    start_time = new Date().getTime();
    CheckSessionStatus();
}
//InitSessionTimer();
function CheckSessionStatus() 
{
    //Check for session warning
    current_time = new Date().getTime();
    if (current_time > start_time + warn_sec && current_time < start_time + timeout_sec && show_warning)
    {
        show_warning = false; //Don't show again
        clicked_warning = false; //Did the user click the warning
        alert_shown = true;
        alert("Your session is about to timeout. Data entered on the current page may be lost.");
        //if the user hits OK and we are not timed out then restart the session_timer
        current_time2 = new Date().getTime();
        if(current_time2 < start_time + timeout_sec)
        {
        	InitSessionTimer();
        }
        else
        {
        	needToConfirm=false;
        	//down = setTimeout("CheckSessionStatus();", 1000);
        	window.location.href = LOGOUT_URL;
        }
    } 
    else if (current_time > start_time + timeout_sec) 
    {
        alert("Your session has timed out.");
        window.location.href = LOGOUT_URL;
    } 
    else 
    {
        down = setTimeout("CheckSessionStatus();", 1000);
    }
}
function isValueInArray(value, array)
{
	found = false;
	for(i=0;i<array.length;i++)
	{
		if (array[i] == value)
		{
			found = true;
		}
	}
	return found;
}
function confirmDelete(delete_location)
{
	if(confirm("Certain about that delete?"))
	{
		needToConfirm = false;
		open_win(delete_location);
	}
}
function calculateColumnTotal(tbodyID, column)
{
		
		var tbody = document.getElementById(tbodyID);
		var rowCount = tbody.rows.length;
		var total =0;
		for(var i = 0;i<rowCount;i++)
		{
			tmp_value = parseFloat(tbody.rows[i].cells[column].childNodes[0].value.replace(/,/g, ''));
			total=total+tmp_value;
		}
		return total;
		
}
function calculateinnerHTMLColumnTotal(tbodyID, column)
{
		
		var tbody = document.getElementById(tbodyID);
		var rowCount = tbody.rows.length;
		var total =0;
		for(var i = 0;i<rowCount;i++)
		{
			tmp_value = parseFloat(tbody.rows[i].cells[column].innerHTML);
			total=total+tmp_value;
		}
		return total;
		
}
function addslashes (str) {
    // Escapes single quote, double quotes and backslash characters in a string with backslashes  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/addslashes    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +   improved by: marrtins
    // +   improved by: Nate
    // +   improved by: Onno Marsman    // +   input by: Denny Wardhana
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
    // *     example 1: addslashes("kevin's birthday");
    // *     returns 1: 'kevin\'s birthday'    
    return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
}		
function getJSONKeys(json_data)
{
	var keys = [];
	json_object = JSON.parse(json_data);
	for(var k in json_object) 
	{
		keys.push(k);
		console.log(k);
	}
	return keys;
}
function tryParseJSON (jsonString){
    try {
        var o = JSON.parse(jsonString);

        // Handle non-exception-throwing cases:
        // Neither JSON.parse(false) or JSON.parse(1234) throw errors, hence the type-checking,
        // but... JSON.parse(null) returns 'null', and typeof null === "object", 
        // so we must check for that, too.
        if (o && typeof o === "object" && o !== null) {
            return o;
        }
    }
    catch (e) { }

    return false;
};
function parseJSONdata(json)
{
	 if(json == '')
	 {
	 	obj = new Array();
	 }
	 else
	 {
	 	obj = JSON && JSON.parse(json) || $.parseJSON(json);
	 }	
	 return obj;
}
function validateMYSQLInsertForm()
{
	//there needs to be an array passed to this function that tells us what values are acceptable
	// ex: can't be empty, has to be a date, has to be a number, has to be selected, can't be false, has to be unique (need to check the dbase)
	errors = '';
	
	for (i=0; i<json_table_def.length;i++)
	{
		if (typeof json_table_def[i]['db_field'] !== 'undefined')
		{
			if (typeof json_table_def[i]['validate'] !== 'undefined')
			{
				if (typeof json_table_def[i]['validate']['unique_group'] !== 'undefined')
				{
					//probably create the sql here
					//array('unique_group' => array('style_number', 'pos_manufacturer_brand_id'),
					sql = "SELECT * ";
					sql += " FROM " + json_table_def[i]['db_table'];
					sql += " WHERE ";
					
					sql_array = [];
					for (vi=0;vi<json_table_def[i]['validate']['unique_group'].length;vi++)
					{
						//now find the value for the unique field
						if (typeof document.getElementsByName(json_table_def[i]['validate']['unique_group'][vi])[0] !== 'undefined')
						{
							sql_array[vi] = json_table_def[i]['validate']['unique_group'][vi] + "='" + addslashes(document.getElementsByName(json_table_def[i]['validate']['unique_group'][vi])[0].value) + "'";
						}
						/*for(vii=0; vii< json_table_def.length;vii++)
						{
							if(json_table_def[vii]['db_field']==json_table_def[i]['validate']['unique_group'][vi])
							{
								//this matches the unique group field
								//need to escape
								
								sql_array[vi] = json_table_def[i]['validate']['unique_group'][vi] + "='" + addslashes(document.getElementsByName(json_table_def[i]['validate']['unique_group'][vi])[0].value) + "'";
							}		
						}*/
					}
					sql += sql_array.join(' AND ');
					//get the name and value of the id
					if (typeof  json_table_def[i]['validate']['id'] !== 'undefined')
					{
						for (var prop in json_table_def[i]['validate']['id'])
						{
							id_name = prop;
						}
						sql += " AND " + id_name +"!='" + json_table_def[i]['validate']['id'][id_name] + "'";
		
					}
					//alert(sql);
					var get_string = {'sql':encodeURI(sql)}; 
					//alert(encodeURI(sql));
					$.ajaxSetup({async: false});
					$.get("../../includes/php/ajax_check_unique_sql.php", get_string,
							function(response) {
							//alert(response);
							if(response == 'exists')
							{
								errors += json_table_def[i]['db_field'] + ' ' + document.getElementsByName(json_table_def[i]['db_field'])[0].value + ' already exists - please create a unique value' + newline();
								//set focus to the offender
								document.getElementsByName(json_table_def[i]['db_field'])[0].focus();
							}
							else if (response == 'does not exist')
							{
							}
							else
							{
								console.log(get_string);
								alert("error in unique_group validation response: " + response);
								
							}
						});
						
	/*				var post_string = {};
	post_string['sql'] = sql;
	var url = POS_ENGINE_URL + '/includes/php/ajax_check_unique_sql.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: false,
			success: 	function(response) 
			{
				alert(response + '2');
				if(response == 'exists')
				{
					errors += json_table_def[i]['db_field'] + ' ' + document.getElementsByName(json_table_def[i]['db_field'])[0].value + ' already exists - please create a unique value' + newline();
					//set focus to the offender
					document.getElementsByName(json_table_def[i]['db_field'])[0].focus();
				}
				else if (response == 'does not exist')
				{
				}
				else
				{
					console.log(get_string);
					alert("error in unique_group validation response: " + response);
					
				}
			}
			});	*/
						
						
						
						
				}
				if (typeof json_table_def[i]['validate']['unique'] !== 'undefined')
				{
					//alert(json_table_def[i]['db_table'] + ' ' +json_table_def[i]['db_field'] + ' ' + document.getElementsByName(json_table_def[i]['db_field'])[0].value);
					
					var get_string = {'table':json_table_def[i]['db_table'],'field' : json_table_def[i]['db_field'],'value': document.getElementsByName(json_table_def[i]['db_field'])[0].value}; 
					
					if (typeof json_table_def[i]['validate']['id'] !== 'undefined')
					{
						for (var prop in json_table_def[i]['validate']['id']){id_name = prop;}
						get_string['id_name'] = id_name;
						get_string['id'] = json_table_def[i]['validate']['id'][id_name];
					}
					$.ajaxSetup({async: false});
					$.get("../../includes/php/ajax_check_unique_table_field_value.php", get_string,
							function(response) {
							//alert(response);
							if(response == 'exists')
							{
								errors += json_table_def[i]['db_field'] + ' ' + document.getElementsByName(json_table_def[i]['db_field'])[0].value + ' already exists - please create a unique value' + newline();
								//set focus to the offender
								document.getElementsByName(json_table_def[i]['db_field'])[0].focus();
							}
							else if (response == 'does not exist')
							{
							}
							else
							{
								console.log(get_string);
								alert("error in unique response: " + response);

							}
						});
				}
				if (typeof json_table_def[i]['validate']['min_length'] !== 'undefined')
				{
					if(document.getElementsByName(json_table_def[i]['db_field'])[0].value.length < json_table_def[i]['validate']['min_length'])
					{
						errors += json_table_def[i]['db_field'] +' needs a minumum length of ' + json_table_def[i]['validate']['min_length'] + ' charaters' + newline();
						document.getElementsByName(json_table_def[i]['db_field'])[0].focus();
					}
				}	
				if (typeof json_table_def[i]['validate']['select_value'] !== 'undefined')
				{
					if(document.getElementsByName(json_table_def[i]['db_field'])[0].value == json_table_def[i]['validate']['select_value'])
					{
						errors += 'You must select a value from the drop down' + newline();
						document.getElementsByName(json_table_def[i]['db_field'])[0].focus();
					}
				}
				if (typeof json_table_def[i]['validate']['dynamic_table_not_zero'] !== 'undefined')
				{
					
					//this is for the dynamic table only
					var elems = document.getElementsByName(json_table_def[i]['db_field']+'[]');
					console.log(elems);
					for(el=0;el<elems.length;el++)
					{
							if(document.getElementsByName(json_table_def[i]['db_field']+'[]')[el].value == '' ||
							round2(document.getElementsByName(json_table_def[i]['db_field']+'[]')[el].value,0) == 0)
							{
								errors += 'Unacceptable Zero or Empty Value in Row ' + (el+1) + newline();
							}
						
					}
					
				}
				if (typeof json_table_def[i]['validate']['multi_select_value'] !== 'undefined')
				{
					  var selectedArray = new Array();
					  var selObj = document.getElementById(json_table_def[i]['db_field']+'[]');
					  var mi;
					  var count = 0;
					  var selected = false;
					  for (mi=0; mi<selObj.options.length; mi++) 
					  {

						if (selObj.options[mi].selected) 
						{
						  selectedArray[count] = selObj.options[mi].value;
						  if (selectedArray[count] == 'false')
						  {
							errors += 'Error in Multi-Select value ' + newline();
							document.getElementById(json_table_def[i]['db_field']+'[]').focus();
						  }
						  else
						  {
						  	selected = true;
						  }
						  count++;
						}
					  }
					  if (selected == false)
					  {
					  	errors += 'Must Select a value from the multi-select ' + newline();
					  	document.getElementById(json_table_def[i]['db_field']+'[]').focus();
					  }
					  
				}
				if ( json_table_def[i]['validate'] == 'number')
				{
					//check if it is a valid number
					if (isNaN(document.getElementsByName(json_table_def[i]['db_field'])[0].value))
					{
						errors += json_table_def[i]['db_field'] +' needs to be a value.' + newline();
						document.getElementsByName(json_table_def[i]['db_field'])[0].focus();
					}
					/*if (document.getElementsByName(json_table_def[i]['db_field'])[0].value == '')
					{
						errors += json_table_def[i]['db_field'] +' can\'t be empty.' + newline();
						document.getElementsByName(json_table_def[i]['db_field'])[0].focus();
					}*/
				}
				if ( json_table_def[i]['validate'] == 'date')
				{
					if (!isDate(trim(document.getElementsByName(json_table_def[i]['db_field'])[0].value)) && document.getElementsByName(json_table_def[i]['db_field'])[0].value !='')
					{
						errors += json_table_def[i]['db_field'] +' needs to be a date in YYYY-MM-DD format.' + newline();
						//document.getElementsByName(json_table_def[i]['db_field'])[0].focus();
					}
				}
			}
		}
	}
 	if (errors == '')
    {
    	needToConfirm=false;
    	//disable the submit button: (id'd as submit)
    	if(document.getElementById('submit'))
    	{
    		document.getElementById('submit').disabled = true;
    		//create hidden post value
    		str_hidden_name = "submit";
			str_hidden_value = "submit";
			//creating the hidden elements for POST
			element = document.createElement("input");
			element.type = "hidden";
			element.name = str_hidden_name;
			element.value = str_hidden_value;
			document.getElementById(formId).appendChild(element);
    	}
    	else
    	{
    	}
    	/*else if(typeof document.getElementsByName('submit')[0] !== 'undefined')
    	{
    		
    		document.getElementsByName('submit')[0].disabled = true;
    		
    	}*/
    	return true;
    }
    else
    {
    	alert(errors);
    	needToConfirm=true;	
    	return false;
    }
}
function validateDynamicTable()
{
	//this should be the same function as above, but check each row....
		errors = '';
	
	for (i=0; i<json_table_def.length;i++)
	{
		if (typeof json_table_def[i]['db_field'] !== 'undefined')
		{
			if (typeof json_table_def[i]['validate'] !== 'undefined')
			{
				// go through each row
				var elements = document.getElementsByName(json_table_def[i]['db_field']+'[]');
				for(el=0;el<elements.length;el++)
				{
					if (typeof json_table_def[i]['validate']['not_blank_or_zero_or_false_or_null'] !== 'undefined')
					{
						if(elements[el].value == '' ||
						round2(elements[el].value,0) == 0 || elements[el].value == 'false' || elements[el].value == 'NULL')
						{
							errors += 'Bad Value For ' +json_table_def[i]['caption'] + ' Row ' + (el+1) + newline();
						}
					}
					else if  (typeof json_table_def[i]['validate']['acceptable_values'] !== 'undefined')
					{
						acceptable_values = json_table_def[i]['validate']['acceptable_values'][0];
						if(acceptable_value == 'number')
						{
							if (isNaN(elements[el].value))
							{
								errors += json_table_def[i]['db_field'] +' needs to be a value.' + newline();
								elements[el].focus();
							}
						}
						else if(acceptable_values == 'text')
						{
						}
						else if(acceptable_values == 'specific')
						{
						}
					}
				}
			}
		}
	}
 	if (errors == '')
    {
    	needToConfirm=false;
    	//disable the submit button: (id'd as submit)
    	if(document.getElementById('submit'))
    	{
    		document.getElementById('submit').disabled = true;
    		//create hidden post value
    		str_hidden_name = "submit";
			str_hidden_value = "submit";
			//creating the hidden elements for POST
			element = document.createElement("input");
			element.type = "hidden";
			element.name = str_hidden_name;
			element.value = str_hidden_value;
			document.getElementById(formId).appendChild(element);
    	}
    	else
    	{
    	}
    	/*else if(typeof document.getElementsByName('submit')[0] !== 'undefined')
    	{
    		
    		document.getElementsByName('submit')[0].disabled = true;
    		
    	}*/
    	return true;
    }
    else
    {
    	alert(errors);
    	needToConfirm=true;	
    	return false;
    }
	
}
function hasWhiteSpace(s) {
  return /\s/g.test(s);
}
function permute(input) 
{
    var permArr = [],
    usedChars = [];
    function main(){
        var i, ch;
        for (i = 0; i < input.length; i++) {
            ch = input.splice(i, 1)[0];
            usedChars.push(ch);
            if (input.length == 0) {
                permArr.push(usedChars.slice());
            }
            main();
            input.splice(i, 0, ch);
            usedChars.pop();
        }
        return permArr;
    }
    return main();
}
function arrayMin(arr) {
  var len = arr.length, min = Infinity;
  while (len--) {
    if (arr[len] < min) {
      min = arr[len];
    }
  }
  return min;
}
function arrayMax(arr) {
  var len = arr.length, max = -Infinity;
  while (len--) {
    if (arr[len] > max) {
      max = arr[len];
    }
  }
  return max;
}
function getCheckbox01(checkbox)
{
	if(checkbox.is(':checked'))
	{
		return 1;
	}
	else
	{
		return 0;
	}
}
function parseQuery(str)
{
	if(typeof str != "string" || str.length == 0) return {};
	var s = str.split("&");
	var s_length = s.length;
	var bit, query = {}, first, second;
	for(var i = 0; i < s_length; i++)
	{
		bit = s[i].split("=");
		first = decodeURIComponent(bit[0]);
		if(first.length == 0) continue;
		second = decodeURIComponent(bit[1]);
		if(typeof query[first] == "undefined") query[first] = second;
		else if(query[first] instanceof Array) query[first].push(second);
		else query[first] = [query[first], second]; 
	}
	return query;
}
function parseQuery_v2(querystring) 
{
  if(querystring.indexOf('?') != -1)
  {
	  // remove any preceding url and split
	  querystring = querystring.substring(querystring.indexOf('?')+1);
	  //console.log('querystring' + querystring);
	
	  querystring = querystring.split('&');
	  //console.log('querystring');
	  //console.log(querystring);
	  //console.log('querystring length');
	  //console.log(querystring.length);
	
	  var params = {}, pair, d = decodeURIComponent;
	  // march and parse
	  for (var i = querystring.length - 1; i >= 0; i--) 
	  {
	 	pair = querystring[i].split('=');
		params[d(pair[0])] = d(pair[1]);
	  }

	  return params;

  }
  else
  {
  	return {};
  }
} //--  fn  deparam