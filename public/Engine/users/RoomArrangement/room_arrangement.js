//ok for the dynamic table to work we need to know the following things:
//tbody_def => this is the columns and the properties of the cells
//tbody_id => define it in php
//and the table_data_array....
//another thing to cinsider is that the check column for table functions is col 0

//"use strict";
window.onload= init;
var save_rul = "update_invoice_to_server.php";
var current_row = 0;
var current_column = 0;
//table_data_array keeps a copy of all the data in the html table plus hidden columns. This goes to the post string
var table_data_array = [];


function init()
{
	if (json_room_contents.length > 0)
	{
		for(var row=0;row<json_room_contents.length;row++)
		{
			//console.log(json_room_contents[row]);
			addItemDataToTableArray(json_room_contents[row]);
    		addItemDataToHTMLTable(json_room_contents[row]);
    		
		}
	}
	else
	{
		//add a row
		addRow();
	}
}
//ajax calls



function preparePostData()
{
	var post_string = {};
	//these are the extras
	post_string['original_room_name'] = document.getElementById('original_room_name').value;
	post_string['room_name'] = document.getElementById('room_name').value;
	post_string['room_priority'] = document.getElementById('room_priority').value;
	post_string['pos_user_id'] = pos_user_id;


	post_string['table_data_array'] = table_data_array;
	post_string['tbody_def'] = tbody_def;
	return post_string;
}

function addItemDataToTableArray(item_data)
{
	var	row = table_data_array.length;
	//table_data_array[row] = {};
	table_data_array[row] = [];
	var data;
	for(var col=0;col<tbody_def.length;col++)
	{
		if(typeof item_data[tbody_def[col]['db_field']] !== 'undefined')
		{
			data = item_data[tbody_def[col]['db_field']];
		}
		else
		{
			data = '';
		}
		//set it to nothing....
		if(typeof tbody_def[col]['price_array_index'] !== 'undefined')
		{
			table_data_array[row][col] = {};
			table_data_array[row][col]['array_values'] = [];
			table_data_array[row][col]['display_value'] = data;
			//table_data_array[row][tbody_def[col]['db_field']] = [];	
			var quantity = item_data[tbody_def[col]['price_array_index']];
			for(var qty=0;qty<quantity;qty++)
			{
				table_data_array[row][col]['array_values'][qty] = data;
				//table_data_array[row][tbody_def[column]['db_field']][qty] = data;	
			}
		}
		else
		{
			table_data_array[row][col] = data;	
			//table_data_array[row][tbody_def[column]['db_field']] = data;	

		}
	}
	

	updateTableArrayLineNumbers();
}
function updateItemDataInTableArray(item_data, row)
{
	
	//table_data_array[row] = {};
	//table_data_array[row] = [];
	var data;
	for(var col=0;col<tbody_def.length;col++)
	{
		if(typeof item_data[tbody_def[col]['db_field']] !== 'undefined')
		{
			data = item_data[tbody_def[col]['db_field']];
			if(typeof tbody_def[col]['price_array_index'] !== 'undefined')
			{
				table_data_array[row][col] = {};
				table_data_array[row][col]['array_values'] = [];
				table_data_array[row][col]['display_value'] = data;
				var quantity = item_data[tbody_def[col]['price_array_index']];
				for(var qty=0;qty<quantity;qty++)
				{
					table_data_array[row][col]['array_values'][qty] = data;
				}
			}
			else
			{
				table_data_array[row][col] = data;	
				//table_data_array[row][tbody_def[column]['db_field']] = data;	
	
			}
		}
		else
		{
			//do nothing
		}
		//set it to nothing....
		
	}
	writeArrayToHTMLTable();
	calculateTotals();
}
function addItemDataToHTMLTable(item_data)
{
	row_number = addRowToHTMLTable();
	writeArrayToHTMLTable();
	//calculateTotals();
}



/************* THESE FUNCTIONS I HOPE TO RE-USE *************************/
function getTbodyRowCount()
{
	//this might not work?
	var tbody = document.getElementById(tbody_id);
	return tbody.rows.length;
}
function getTableArrayColumnNumberFromHTMLColumnNumber(html_column)
{
	column = -1;
	var table_data_array_column = 0;
	var html_column_counter = 0;
	for (i=0; i<tbody_def.length;i++)
	{
		if(tbody_def[i]['type'] != 'hidden')
		{
			html_column_counter = html_column_counter + 1;
		}
		table_data_array_column = table_data_array_column+1;
		if (html_column == html_column_counter)
		{
			return table_data_array_column;
		}
	}
}
function getHTMLColumnNumberFromTableDefColumnName(name)
{
	column = -1;
	var col_counter = 0;
	for (i=0; i<tbody_def.length;i++)
	{
		if(tbody_def[i]['type'] != 'hidden')
		{
			if (tbody_def[i]['db_field'] == name)
			{
				column = col_counter;
			}
			else
			{
				col_counter++;
			}
		}
	}
	return column;
}
function getTableDataColumnNumberFromTableDefColumnName(name)
{
	column = -1;
	for (i=0; i<tbody_def.length;i++)
	{
		if (typeof tbody_def[i]['db_field'] !== 'undefined' && tbody_def[i]['db_field'] == name)
		{
			column = i;
		}
		
	}
	return column;
}
function setSingleCheck(control)
{
	setCurrentRow(control);
	//uncheck every check but this one...
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;
	for(var row=0; row<rowCount; row++)
	{
		if (row != getCurrentRow(control))
		{
			tbody.rows[row].cells[0].childNodes[0].checked = false;
		}
	}
}
function addRow()
{
	row = table_data_array.length;
	table_data_array[row] = [];
	for(col= 0;col<tbody_def.length;col++)
	{			
		table_data_array[row][col] = '';
	}	
	row_number = addRowToHTMLTable();
	updateLineNumbers();
}
function addRowToHTMLTable()
{
	var tBody = document.getElementById(tbody_id);
	var rowCount = tBody.rows.length;
	var row = tBody.insertRow(rowCount);
	row.id = rowCount;
	var col_counter = 0;
	var element;
	for (c=0; c<tbody_def.length;c++)
	{
    	if (tbody_def[c]['type'] == 'hidden')
    	{
    	}
    	else if (tbody_def[c]['type'] == 'input' || tbody_def[c]['type'] == 'checkbox')
    	{
			cell = row.insertCell(col_counter);
			col_counter++;
			element = document.createElement(tbody_def[c]['element']);
			element.type = tbody_def[c]['element_type'];
			if (typeof tbody_def[c]['properties'] !== 'undefined')
			{
				for (var index in tbody_def[c]['properties'])
				{
					eval('element.' + index + '= ' + tbody_def[c]['properties'][index] + ';');
					
				}
			}
			cell.appendChild(element);
		}
		else if (tbody_def[c]['type'] == 'select')
		{
			cell = row.insertCell(col_counter);col_counter++;
			element = document.createElement('select');
			if (typeof tbody_def[c]['properties'] !== 'undefined')
			{
				for (var index in tbody_def[c]['properties'])
				{
					eval('element.' + index + '= ' + tbody_def[c]['properties'][index] + ';');
					
				}
			}
		
			//var option = document.createElement('option');
			//option.value = '';
			//option.appendChild(document.createTextNode("Select..."));
			//element.appendChild(option);
			for (var i in eval(tbody_def[c]['select_names']))
			{
				option = document.createElement('option');
				option.value = eval(tbody_def[c]['select_values'] + '[i]');
				option.appendChild(document.createTextNode(eval(tbody_def[c]['select_names'] + '[i]')));
				element.appendChild(option);
			}

			cell.appendChild(element);
		}
		else
		{
		}

	}
	current_row = rowCount;
	return rowCount;
}
function setCurrentRow(control)
{
	current_row = getCurrentRow(control);
	current_column = control.parentNode.cellIndex;
	//alert('row ' + current_row + ' column ' + current_column);
}
function getCurrentColumn(control)
{
	return control.parentNode.cellIndex;
}
function getCurrentRow(control)
{
	//need to back the number of thead rows off in order to get the "correct" tbody row index starting at 0
	var thead = getCellTHead(control);
	var theadRowCount = thead.rows.length;
	return  control.parentNode.parentNode.rowIndex - theadRowCount;	
}
function getCellTHead(control)
{
	var table = getCellTable(control);
	var thead = table.tHead;
	//alert('thead:' + thead.id);
	return thead;
}
function getCellTable(control)
{
	var table = control.parentNode.parentNode.parentNode.parentNode;
	//alert('table:' + table.id);
	return table;
}
function getCellTFoot(control)
{
	var table = getCellTable(control);
	var tfoot = table.tFoot;
	//this is how to check if the footer is there...
	if(typeof tfoot !== 'undefined')
	{
		alert('tfoot:' + tfoot.id);
	}
	else
	{
		alert('no footer');
	}
}
function getCellTBody(control)
{
	var tbody = control.parentNode.parentNode.parentNode;
	//alert("tbody:" + tbody.id);
	return tbody;
}
function getCellTBodies(control)
{
	var table = getCellTable(control);
	var tBodies = table.tBodies;
	var num_tbodies = tBodies.length;
	//alert('tbodies[0]:' + tBodies[0].id);
}
function copyRow()
{
	var tbody = document.getElementById(tbody_id);
	copyHTMLTableDataToArray();
	checked_rows = findCheckedRows();
	if (checked_rows.length>0)
	{
		table_data_array = copyArrayRows(table_data_array, checked_rows);
		for (var i=0;i<checked_rows.length;i++)
		{
			addRowToHTMLTable();
		}
		writeArrayToHTMLTable();
		for (var i=0;i<checked_rows.length;i++)
		{
			tbody.rows[checked_rows[i]+1].cells[0].childNodes[0].checked = false;
		}
	}
	else
	{
	}
	calculateTotals();
}
function copyHTMLTableDataToArray()
{
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;
	//var maxColCount = tbody.rows[0].cells.length;

	var column_counter = 0;
	for(row=0; row<rowCount; row++)
	{
		column_counter=0;
		for(col= 0;col<tbody_def.length;col++)
		{			
			if(tbody_def[col]['type'] != 'hidden')
			{	
				if (typeof tbody_def[col]['price_array_index'] === 'undefined')
				{
					//alert(tbody.rows[row].cells[column_counter].childNodes[0].value);
					if (tbody_def[col]['type'] == 'checkbox')
					{
						table_data_array[row][col] = tbody.rows[row].cells[column_counter].childNodes[0].checked;
					}
					else
					{
						table_data_array[row][col] = tbody.rows[row].cells[column_counter].childNodes[0].value;
					}
				}		
				column_counter++;
			}
		}
	}
	return table_data_array;
}
function copyArrayRows(table_array, rows)
{
	var newRowCounter = 0;
	var newArray = [];
	for (var i = 0;i<table_array.length;i++)
	{
		for(var r = 0;r<rows.length;r++)
		{
			if(i == rows[r])
			{
				//copy
				newArray[newRowCounter] = [];
				for(col= 0;col<tbody_def.length;col++)
				{	
					if(typeof tbody_def[col]['price_array_index'] !== 'undefined')
					{
						quantity = table_data_array[i][getTableDataColumnNumberFromTableDefColumnName('quantity')];
						newArray[newRowCounter][col] = {};
						newArray[newRowCounter][col]['array_values'] = [];
						newArray[newRowCounter][col]['display_value'] = table_array[i][col]['display_value'];
						for(qty=0;qty<quantity;qty++)
						{
							newArray[newRowCounter][col]['array_values'][qty] = table_array[i][col]['array_values'][qty];
						}
					}
					else
					{
						newArray[newRowCounter][col] = table_array[i][col];
					}
					
				}	
				newRowCounter++;			
			}
		}
		//transfer original
		newArray[newRowCounter] = [];
		for(col= 0;col<table_array[i].length;col++)
		{	
			if(typeof tbody_def[col]['price_array_index'] !== 'undefined')
			{
				quantity = table_data_array[i][getTableDataColumnNumberFromTableDefColumnName('quantity')];
				newArray[newRowCounter][col] = {};
				newArray[newRowCounter][col]['array_values'] = [];
				newArray[newRowCounter][col]['display_value'] = table_array[i][col]['display_value'];
				for(qty=0;qty<quantity;qty++)
				{
					newArray[newRowCounter][col]['array_values'][qty] = table_array[i][col]['array_values'][qty];
				}
			}
			else
			{
				newArray[newRowCounter][col] = table_array[i][col];
			}
			
		}	
		newRowCounter++;
	}
	return newArray;
}
function writeArrayToHTMLTable(control)
{
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;
	var column_counter=0;
	if (typeof control !== 'undefined')
	{
		control_column  =  getTableArrayColumnNumberFromHTMLColumnNumber(getCurrentColumn(control));
		control_row = getCurrentRow(control);
	}
	else
	{
		control_column  =  'undefined';
		control_row = 'undefined';
	}
	

	
	//var maxColCount = tbody.rows[0].cells.length; 	
	for(row=0; row<rowCount; row++)
	{
		column_counter=0;
		for(col= 0;col<tbody_def.length;col++)
		{
			if(tbody_def[col]['type'] != 'hidden')
			{
				if (tbody_def[col]['type'] == 'checkbox')
				{
					if(row == control_row && col == control_column)
					{
					}
					{
						tbody.rows[row].cells[column_counter].childNodes[0].checked =table_data_array[row][col];
					}
				}
				else
				{
					if(row == control_row && col == control_column)
					{
					}
					else
					{
						if(typeof tbody_def[col]['price_array_index'] !== 'undefined')
						{
							value =  table_data_array[row][col]['display_value'];
						}
						else
						{
							value =  table_data_array[row][col];
						}
							
						if(typeof tbody_def[col]['round'] !== 'undefined')
						{

							tbody.rows[row].cells[column_counter].childNodes[0].value = round2(value,tbody_def[col]['round']);
						}
						else
						{
							tbody.rows[row].cells[column_counter].childNodes[0].value = value;
						}
					}
				}
				column_counter++;
			}
			
		}
	}
	//return tabledata;
	updateLineNumbers();

}
function deleteRow() 
{
	var answer = confirm("Confirm Delete Row(s)")
	if (answer)
	{	// delete selected rows
		var tbody = document.getElementById(tbody_id);
		var rowCount = tbody.rows.length;
		if (rowCount > 0)
		{
			//first find the row(s) that are checked
			checked_rows = findCheckedRows();
			if (checked_rows.length>0)
			{
				table_data_array = copyHTMLTableDataToArray();
				table_data_array = deleteArrayRows(table_data_array, checked_rows);
				for (var i=0;i<checked_rows.length;i++)
				{
					tbody.deleteRow(checked_rows[i]);
				}
				writeArrayToHTMLTable();

			}
			else
			{
			}
			
		} 
		else
		{
			alert("Can't delete when there is no rows");
		}
	}
	else
	{
		//do not delete rows
	}
	calculateTotals();


}
function deleteArrayRows(array,rows)
{
	var newRowCounter = 0;
	var newArray = [];
	for (var i = 0;i<array.length;i++)
	{
		bln_delete = false;
		for(var r = 0;r<rows.length;r++)
		{
			if(i == rows[r])
			{
				//delete
				bln_delete = true;
			}
		}
		//transfer original if bln_delete is false
		if (!bln_delete)
		{
			newArray[newRowCounter] = array[i];
			newRowCounter++;
		}
	}
	return newArray;
}
function moveRowUp()
{
	//first find the row(s) that are checked
	checked_rows = findCheckedRows();
	//next check that the rows can be moved - they are in bounds
	bln_move_ok = true;
	for(var i=0;i<checked_rows.length;i++)
	{
		if((checked_rows[i] -1) <0) bln_move_ok = false
	}
	if (bln_move_ok)
	{
		//next copy the entire table to an array - need the size rows as well
		table_data_array = copyHTMLTableDataToArray();
		//rearrange the rows into a new array
		for(var i=0;i<checked_rows.length;i++)
		{
			table_data_array = moveArrayRow(table_data_array, checked_rows[i], checked_rows[i]-1);
			setChecks(checked_rows[i], checked_rows[i]-1);
		}	
		//put the array back into the table
		writeArrayToHTMLTable();
			calculateTotals();

	}
}
function moveRowDown()
{
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;
	//first find the row(s) that are checked
	checked_rows = findCheckedRows();
	//next check that the rows can be moved - they are in bounds
	bln_move_ok = true;
	for(var i=0;i<checked_rows.length;i++)
	{
		if((checked_rows[i] +1) > rowCount-1) bln_move_ok = false
	}
	if (bln_move_ok)
	{
		//next copy the entire table to an array - need the size rows as well
		table_data_array = copyHTMLTableDataToArray();
		//rearrange the rows into a new array
		for(var i=checked_rows.length-1;i>-1;i--)
		{
			var newRow = parseInt(checked_rows[i])+parseInt(1);
			table_data_array = moveArrayRow(table_data_array, checked_rows[i], newRow);
			setChecks(checked_rows[i], newRow);
		}	
		//put the array back into the table
		writeArrayToHTMLTable();
			calculateTotals();

	}
}
function setChecks(rowMoving, movingTo)
{
	var tbody = document.getElementById(tbody_id);
	tbody.rows[rowMoving].cells[0].childNodes[0].checked=false;
	tbody.rows[movingTo].cells[0].childNodes[0].checked=true;
	
}
function moveArrayRow(tableData, RowToMove, RowToMoveTo)
{
	//ex row 2 row 3
	newTable = new Array();
	for (i=0;i<tableData.length;i++)
	{
		newTable[i] = new Array();
		for(j=0;j<tableData[0].length;j++)
		{
			if (RowToMove == i)
			{
				newTable[i][j] = tableData[RowToMoveTo][j];
			}
			else if (RowToMoveTo == i)
			{
				newTable[i][j] = tableData[RowToMove][j];
			}
			else
			{
				newTable[i][j] = tableData[i][j];
			}
		}
	}
	return newTable;
}
function findCheckedRows()
{
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;
	var checked_rows = new Array();
	var counter = 0;
	for(var k=0; k<rowCount; k++) 
	{
		var chkbox = tbody.rows[k].cells[0].childNodes[0];

		if((null != chkbox) && (true == chkbox.checked) )
		{
			checked_rows[counter] = k; 
			counter = counter+1;
		}
	}
	return checked_rows;
}
function updateTableArrayLineNumbers()
{
	//db_field has to be 'row_number' for this to work
	//we need to loop through the tbody cells and set the value of the column name bla bla
	var col = getTableDataColumnNumberFromTableDefColumnName('row_number');
	if (col != -1)
	{
		for(var row=0; row<table_data_array.length; row++)
		{
			table_data_array[row][col] = row+1;
		}
	}
}
function updateHTMLTableLineNumbers()
{
		//db_field has to be 'row_number' for this to work
	//we need to loop through the tbody cells and set the value of the column name bla bla
	var col = getHTMLColumnNumberFromTableDefColumnName('row_number');
	if (col != -1)
	{
		var tbody = document.getElementById(tbody_id);
		var rowCount = tbody.rows.length;
		for(var row=0; row<rowCount; row++)
		{
			tbody.rows[row].cells[col].childNodes[0].value = row+1;
		}
	}
}
function updateLineNumbers()
{

	updateTableArrayLineNumbers();
	updateHTMLTableLineNumbers();
	
}

function saveDraft()
{
	//copy the table data into the table data array
	save_url = "update_invoice_to_server.php";
	table_data_array = copyHTMLTableDataToArray();
	post_string = preparePostData();
	$.post(save_url, post_string,
   	function(response) {
     console.log(response);
     needToConfirm=false;
   });
}
 
function saveDraftAndGo(complete_location)
{
	
	save_url = "room_arrangement.form.handler.php";
	table_data_array = copyHTMLTableDataToArray();
	post_string = preparePostData();
	$.post(save_url, post_string,
	function(response) 
	{
		//alert(response);
		window.location = complete_location;
	
		
	});
	
}

