/*	This page does all the magic for applying
To use AJAX remeber this:
there is an AJAX file to include.... don't need to worry about it just include it
You need to attach a js function to an event. 
The js function will call a .php file that will have the querey
The query will go back to the js function when it is done.

 */




function unCheckOther(control)
{

		// when a checkbox is checked we are going to uncheck all others
		var tBody = document.getElementById('pos_product_attribute_table_body');
		//get the number of columns
		var rowCount = tBody.rows.length;
		newRowCount = rowCount;
		for(var k=0; k<rowCount; k++) 
		{
		var row = tBody.rows[k];
		var chkbox = row.cells[0].childNodes[0];
		
			if (chkbox == control)
			{
				//do nothing
			}
			else
			{
				// uncheck it
				chkbox.checked = false;
			}
		
		}
		
}

function addAttribute(tBodyId)
{

	// get the tBody name: this is sent by the element.... 
	var tBody = document.getElementById(tBodyId);
	var rowCount = tBody.rows.length;
	var row = tBody.insertRow(rowCount);	
	//row.id = rowCount;
	//alert (rowCount);
	
	var cell;
	var element;
	
	

//Checkbox
    c = 0;
    cell = row.insertCell(c);
    element = document.createElement("input");
    element.type = "checkbox";
    element.onclick=function(){unCheckOther(this);}
    cell.appendChild(element);
	
	
//Attribute Name - input
	c = 1;
	cell = row.insertCell(c);
	element = document.createElement("input");
	element.type = "text";
	element.size = "40";
	element.maxLength = "60";
	//element.style.width = "100%";
	//element.style.height = "100%";
	//element.style.borderWidth = "1px";
    //element.style.margin =  "0";
    //element.style.padding = "0";
    // not working control.verticalAlign =  "center";
	//element.className = "poStyle";
	//styleTextControlNoBorders(element);
	cell.appendChild(element);	
//Options - text area

	c = 2;
	cell = row.insertCell(c);
	element = document.createElement("textarea");
	element.cols= "40";
	//element.rows = "0";
	//element.style.height ="1";
	element.onkeyup=function()
	{
  		this.style.height='';
  		this.rows=this.value.split('\n').length;
  		this.style.height=this.scrollHeight+'px';
	}
	cell.appendChild(element);
	
	//add an empty cell
		c = 3;
	cell = row.insertCell(c);
	
	// hide the add, delete and edit buttons
	document.getElementById('addAttribute').style.visibility='hidden';
	document.getElementById('editAttribute').style.visibility='hidden';
	document.getElementById('deleteAttribute').style.visibility='hidden';
	
	// add an update button
	
	element = document.createElement("button");
	element.id = "updateAttribute";
	// set the pos_product_id in the corresponding .php file. Pass the control so we can get to the data???? although this control is the button and we want the last row of the table
	element.onclick = function(){writeAttributeData(pos_product_id, 'pos_product_attribute_table_body');}
	theText=document.createTextNode("Update");
	element.appendChild(theText);
	document.getElementById('attributeButtons').appendChild(element);
	
	

	
	
	

}


function editAttribute(tBodyID, product_id)
{
	//find the row number to edit
	var tBody = document.getElementById('pos_product_attribute_table_body');
	//get the number of rows
	var rowCount = tBody.rows.length;
	newRowCount = rowCount;
	rowToEdit = findCheckedRow()
if (rowToEdit != false)
{
			
		
		// We need to change the buttons to update
		// hide the add, delete and edit buttons
		document.getElementById('addAttribute').style.visibility='hidden';
		document.getElementById('editAttribute').style.visibility='hidden';
		document.getElementById('deleteAttribute').style.visibility='hidden';
		
		// add an update button
		
		element = document.createElement("button");
		element.id = "updateAttribute";
		// set the pos_product_id in the corresponding .php file. Pass the control so we can get to the data???? although this control is the button and we want the last row of the table
		element.onclick = function(){writeEditedAttributeData(pos_product_id, 'pos_product_attribute_table_body');}
		theText=document.createTextNode("Update");
		element.appendChild(theText);
		document.getElementById('attributeButtons').appendChild(element);
	
		//get the info
		var pos_product_attribute_id = tBody.rows[rowToEdit].cells[1].childNodes[0].value;
		tBody.rows[rowToEdit].cells[1].removeChild(tBody.rows[rowToEdit].cells[1].childNodes[0]);
		var attributeName = tBody.rows[rowToEdit].cells[1].innerHTML;
		var attributeOptions = tBody.rows[rowToEdit].cells[2].innerHTML;
		var pos_product_attribute_id2 = tBody.rows[rowToEdit].cells[3].childNodes[0].value;	
		attributeOptions = attributeOptions.replace(/<br>/gi, "");
		
		//alert(pos_product_attribute_id)
		//alert(attributeName);
		//alert(attributeOptions);
		
		//remove the text
		tBody.rows[rowToEdit].cells[1].innerHTML = ''
		tBody.rows[rowToEdit].cells[2].innerHTML = ''
		
		//tBody.rows[rowToEdit].cells[1].removeChild(tBody.rows[rowToEdit].cells[1].childNodes[1]);
		//tBody.rows[rowToEdit].cells[2].removeChild(tBody.rows[rowToEdit].cells[2].childNodes[0]);
		
		//now create the input boxes with the text and hidden pos_product_attribute_id
			element = document.createElement("input");
		element.type = "hidden";
		element.value = pos_product_attribute_id;
		tBody.rows[rowToEdit].cells[1].appendChild(element);	
		
		element = document.createElement("input");
		element.type = "text";
		element.size = "40";
		element.maxLength = "60";
		element.value = attributeName;
		tBody.rows[rowToEdit].cells[1].appendChild(element);	
		//Options - text area
	
		element = document.createElement("textarea");
		element.cols= "40";
		element.value = attributeOptions;
		element.onkeyup=function()
		{
			this.style.height='';
			this.rows=this.value.split('\n').length;
			this.style.height=this.scrollHeight+'px';
		}
		tBody.rows[rowToEdit].cells[2].appendChild(element);
	} // end of if there is no checked row....
	
}
function findCheckedRow()
{
	//find the row number to edit
	var tBody = document.getElementById('pos_product_attribute_table_body');
	//get the number of rows
	var rowCount = tBody.rows.length;
	var rowToEdit = false;
	for(var k=0; k<rowCount; k++) 
	{
		var row = tBody.rows[k];
		var chkbox = row.cells[0].childNodes[0];
		
			if(null != chkbox && true == chkbox.checked) 
			{
				//This is the row
				rowToEdit = k;
			}
			else
			{
			}
		
		}
		return rowToEdit;
} // end of find checked row function

function listChildren(parent)
{
	// debugging function to list children... pass in something like: tBody.rows[rowToEdit].cells[1]
	var children = parent.childNodes;
   for (var i = 0; i < children.length; i++) 
   {
	alert(children[i].value);
   }
}


function writeEditedAttributeData(product_id,tBodyId)
{
	//alert (style_number);
	// Confirm that the object is usable:
	if (ajax) { 
		//alert("product_id= " + product_id);
		// Now we need to get the new attribute data and encode it to JSON
		// get the table body and find the last row.
		// last row is rowCount -1
		// want cells 1 and 2
		var rowToEdit = findCheckedRow();
		//alert(rowToEdit);
		var tBody = document.getElementById(tBodyId);
		var rowCount = tBody.rows.length;
		var pos_product_attribute_id = tBody.rows[rowToEdit].cells[1].childNodes[0].value;
		var attributeName = tBody.rows[rowToEdit].cells[1].childNodes[1].value;
		var attributeOptions = tBody.rows[rowToEdit].cells[2].childNodes[0].value;
		
		// break the attributes up to remove the returns...
		attributeOptions = attributeOptions.split("\n");
		// now we need to encode the data and send it to ajax....
		var JSONattributName = JSON.stringify(attributeName);
		var JSONattributeOptions = JSON.stringify(attributeOptions);
		//Set the string to send
		params = "product_id=" + product_id + "&" + "pos_product_attribute_id=" + pos_product_attribute_id + "&" + "name=" + encodeURIComponent(attributeName) + "&" + "options=" + encodeURIComponent(JSONattributeOptions);		
		
		
		// Call the PHP script.
		// Use the GET method.
		// Pass the username in the URL.
		//alert('editAttribute.AJAX.php?' + params);
		ajax.open('get', 'editAttribute.AJAX.php?' + params);

		ajax.onreadystatechange = function(){updateTable(); }
		


		// Send the request:
		ajax.send(null);
		
		
		//change the buttons back
		

	} else { // Can't use Ajax!
		alert("no ajax!");
	}










}


function deleteAttribute(tbodyID,product_id) 
{
	var answer = confirm("Confirm Delete Attribute")
	
	if (answer)
	{	// delete selected attribute
		try 
		{
			var tBody = document.getElementById(tbodyID);
			var rowCount = tBody.rows.length;
				for(var i=0; i<rowCount; i++) 
				{
					var row = tBody.rows[i];
					var chkbox = row.cells[0].childNodes[0];
					if(null != chkbox && true == chkbox.checked) 
					{
						
						// run the query to delete the pos_product_attribute_id from the database
								var pos_product_attribute_id = tBody.rows[i].cells[1].childNodes[0].value;

								//Set the string to send
								params = "pos_product_attribute_id=" + pos_product_attribute_id + "&product_id=" +  product_id;		
								
								// Call the PHP script.
								// Use the GET method.
								// Pass the username in the URL.
								ajax.open('get', 'deleteAttribute.AJAX.php?' + params);
								// Function that handles the response:
								//ajax.onreadystatechange = sp_handle_check(control);
								ajax.onreadystatechange = function(){updateTable(); }
						
								// Send the request:
								ajax.send(null);
		
						
						//table.deleteRow(i);
						//rowCount--;
						//i--;
						
					}
			
				}
			
		}
		catch(e) 
		{
			alert(e);
		}
	}
	else
	{
		//do not delet rows
	}


}


//************************************************  AJAX STUFF **************************************************************
// Script 13.3 - checkusername.js

/*	This page does all the magic for applying
 *	Ajax principles to a registration form.
 *	The users's chosen username is sent to a PHP 
 *	script which will confirm its availability.
 */

// Function that starts the Ajax process:
function writeAttributeData(product_id, tBodyId) 
{
	
	//alert (style_number);
	// Confirm that the object is usable:
	if (ajax) { 
		//alert("product_id= " + product_id);
		// Now we need to get the new attribute data and encode it to JSON
		// get the table body and find the last row.
		// last row is rowCount -1
		// want cells 1 and 2
		var tBody = document.getElementById(tBodyId);
		var rowCount = tBody.rows.length;
		var attributeName = tBody.rows[rowCount-1].cells[1].childNodes[0].value;
		var attributeOptions = tBody.rows[rowCount-1].cells[2].childNodes[0].value;
		
		// Remove all blank lines (lines with only tabs)
		//var attributeOptions = attributeOptions.replace(/\t\r\n/g, '');
                            
		//Remove all blank lines (lines with nothing on them)
		//attributeOptions = attributeOptions.replace(/(\r\n|\n|\r)/gm,"");

		//attributeOptions = attributeOptions.replace(/\n\n/g, "\n");
		//str = str.replace(/\r\n\r\n/g, "\r\n");   
		
   		var str = attributeOptions;
  		 while(str.indexOf("\r\n\r\n") >= 0) 
  		 {
      			 
   		}
   		attributeOptions = str;


		
		// break the attributes up to remove the returns...
		attributeOptions = attributeOptions.split("\n");
		// now we need to encode the data and send it to ajax....
		var JSONattributName = JSON.stringify(attributeName);
		var JSONattributeOptions = JSON.stringify(attributeOptions);
		//Set the string to send
		params = "product_id=" + product_id + "&" + "name=" + encodeURIComponent(attributeName) + "&" + "options=" + encodeURIComponent(JSONattributeOptions);		
		
		// Call the PHP script.
		// Use the GET method.
		// Pass the username in the URL.
		//alert('SetAttributes.AJAX.php?' + params);
		ajax.open('get', 'SetAttributes.AJAX.php?' + params);
		
		// Function that handles the response:
		//ajax.onreadystatechange = sp_handle_check(control);
		ajax.onreadystatechange = function(){sa_handle_check(); }

		
		
		// Send the request:
		ajax.send(null);

	} else { // Can't use Ajax!
		//document.getElementById('username_label').innerHTML = 'The availability of this username will be confirmed upon form submission.';
		alert("no ajax!");
	}
	
} // End of getColorCodes() function.

// Function that handles the response from the PHP script:
function sa_handle_check() {
 	//alert("inside handle_check");
	// If everything's OK:
	if ( (ajax.readyState == 4) && (ajax.status == 200) ) {
	
	var strpos_product_attribute_id = '';

	

	var myObject = eval('(' + ajax.responseText + ')');
	// set the table values or just reload the form???
	for(var i=0;i<myObject.length;i++)
	{
		strpos_product_attribute_id = strpos_product_attribute_id + myObject[i].pos_product_attribute_id + "\n" ;
	}
	//alert(myObject[myObject.length-1].name);
	//alert(myObject[myObject.length-1].options);
	
	//hide the update button
	document.getElementById('updateAttribute').style.visibility='hidden';
	//show the other buttons
	document.getElementById('addAttribute').style.visibility='visible';
	document.getElementById('editAttribute').style.visibility='visible';
	document.getElementById('deleteAttribute').style.visibility='visible';
	
	// set the data.... this will be the data on the last row of the table...
	//can I really just do a page reload? - this could kill other data.
	// what about breaking the big form into three forms? - then something could change in any of the individual forms and get lost
	// so the answer is to process the overview, attributes, and inventory independently???
	
	var tBody = document.getElementById('pos_product_attribute_table_body');
	var rowCount = tBody.rows.length;
	var inputBox = tBody.rows[rowCount-1].cells[1].childNodes[0];
	var textArea = tBody.rows[rowCount-1].cells[2].childNodes[0];
	//alert(inputBox.value);
	//alert(textArea.value);	
	tBody.rows[rowCount-1].cells[1].removeChild(inputBox);
	tBody.rows[rowCount-1].cells[2].removeChild(textArea);
	
	//create the innerHTML or textNode
	textNode1 = document.createTextNode(myObject[myObject.length-1].name);
	textNode2 = document.createTextNode(myObject[myObject.length-1].options);
	textNode3 = document.createTextNode(myObject[myObject.length-1].pos_product_attribute_id);
	
	//apend the textNode
	
	// append the pos_product_attribute_id hidden value
	element = document.createElement("input");
	element.type = "hidden";
	element.value = myObject[myObject.length-1].pos_product_attribute_id;
	
	tBody.rows[rowCount-1].cells[1].appendChild(element);
	tBody.rows[rowCount-1].cells[1].appendChild(textNode1);
	tBody.rows[rowCount-1].cells[2].appendChild(textNode2);
	tBody.rows[rowCount-1].cells[3].appendChild(textNode3);
		
	}
	else {
		//alert("Ajax ready state and status failure");
	}
	
} // End of handle_check() function.

function updateTable()
{
if ( (ajax.readyState == 4) && (ajax.status == 200) ) {
	
	
	//alert(myObject[myObject.length-1].name);
	//alert(myObject[myObject.length-1].options);
	

	
	// set the data.... this will be the data on the last row of the table...
	//can I really just do a page reload? - this could kill other data.
	// what about breaking the big form into three forms? - then something could change in any of the individual forms and get lost
	// so the answer is to process the overview, attributes, and inventory independently???
	
	var tBody = document.getElementById('pos_product_attribute_table_body');
	var rowCount = tBody.rows.length;
	//alert(rowCount);
	// delete everything from the table
	for(var k=0;k<rowCount;k++)
	{
			//alert(tBody.rows[k].cells[1].childNodes[0].value);
			tBody.deleteRow(k);
			rowCount--;
			k--;
			
	}
	
	//now re-create the table
	cell=new Array();
	//var myObject = eval('(' + ajax.responseText + ')')
	var myObject = JSON.parse(ajax.responseText);
	for(var i=0;i<myObject.length;i++)
	{
		row = tBody.insertRow(i);
		cell[0] = row.insertCell(0);
		cell[1] = row.insertCell(1);
		cell[2] = row.insertCell(2);
		cell[3] = row.insertCell(3);
    	element = document.createElement("input");
    	element.type = "checkbox";
    	element.onclick=function(){unCheckOther(this);}
    	cell[0].appendChild(element);
		element = document.createElement("input");
		element.type = "hidden";
		element.value = myObject[i].pos_product_attribute_id;
		tBody.rows[i].cells[1].appendChild(element);
		textNode1 = document.createTextNode(myObject[i].name);
		tBody.rows[i].cells[1].appendChild(textNode1);

		textNode2 = document.createTextNode(myObject[i].options);
		tBody.rows[i].cells[2].appendChild(textNode2);
		textNode3 = document.createTextNode(myObject[i].pos_product_attribute_id);
		tBody.rows[i].cells[3].appendChild(textNode3);
	}
	
	//hide the update button if it is there
	if (document.getElementById('updateAttribute') != null)
	{
		document.getElementById('updateAttribute').style.visibility='hidden';
	}
	//show the other buttons
	document.getElementById('addAttribute').style.visibility='visible';
	document.getElementById('editAttribute').style.visibility='visible';
	document.getElementById('deleteAttribute').style.visibility='visible';
	
	//apend the textNode
	
	// append the pos_product_attribute_id hidden value

	
	
	
		
	}
	else {
		//alert("Ajax ready state and status failure");
	}
	
	


}
