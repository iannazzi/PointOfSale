//ok for the dynamic table to work we need to know the following things:
//tbody_def => this is the columns and the properties of the cells
//tbody_id => define it in php
//and the table_data_array....
//another thing to cinsider is that the check column for table functions is col 0

//"use strict";
window.onload= init;
var current_row = 0;
var current_column = 0;
//table_data_array keeps a copy of all the data in the html table plus hidden columns. This goes to the post string.. kindof
var table_data_array = [];
//the object is what I would prefer to use as it would re-create the php array...
var table_data_object = {};

//these functions probably need to be on thier own js file

function prepareDynamicTableForPost()
{
	enableAllRows();
	//validateMYSQLInsertForm();
	return validateDynamicTable();
} 



function init()
{
	//build the object?
	for(var col=0;col<tbody_def.length;col++)
	{
		table_data_object[tbody_def[col]['db_field']] = {};
	}
	
	if (json_table_contents.length > 0)
	{
		for(var row=0;row<json_table_contents.length;row++)
		{
			//console.log(json_room_contents[row]);
			addItemDataToTableArray(json_table_contents[row]);
    		addItemDataToHTMLTable(json_table_contents[row]);
    		
		}
	}
	else
	{
		//add a row
		//addRow();
	}
	//finally call an init funtion for extra stuff
	fnName = 'additionalInit';
	ifFnExistsCallIt(fnName);


}
//ajax calls
function updateTableDataForPost()
{
	//this will keep the table data ready to post....
	str_hidden_name = "table_data_array";
	str_hidden_value = JSON.stringify(table_data_array);
	//creating the hidden elements for POST
	element = document.createElement("input");
	element.type = "hidden";
	element.name = str_hidden_name;
	element.value = str_hidden_value;
	document.getElementById(formId).appendChild(element);
	
	
	str_hidden_name = "table_data_object";
	str_hidden_value = JSON.stringify(table_data_object);
	//creating the hidden elements for POST
	element = document.createElement("input");
	element.type = "hidden";
	element.name = str_hidden_name;
	element.value = str_hidden_value;
	document.getElementById(formId).appendChild(element);
}
function addItemDataToTableArray(item_data)
{
	var	row = table_data_array.length;
	table_data_array[row] = [];
	
	var data;
	for(var col=0;col<tbody_def.length;col++)
	{
		//table_data_object[tbody_def[col]['db_field']] = {};
		
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
		
			table_data_object[tbody_def[col]['db_field']][row] = {};
			table_data_object[tbody_def[col]['db_field']][row]['array_values'] = {};
			table_data_object[tbody_def[col]['db_field']][row]['display_value'] = {};
			
			//table_data_array[row][tbody_def[col]['db_field']] = [];	
			var quantity = item_data[tbody_def[col]['price_array_index']];
			for(var qty=0;qty<quantity;qty++)
			{
				table_data_array[row][col]['array_values'][qty] = data;
				table_data_object[tbody_def[col]['db_field']][row]['array_values'][qty] = data;
				//table_data_array[row][tbody_def[column]['db_field']][qty] = data;	
			}
		}
		//what about the special select values?
		else if(typeof tbody_def[col]['individual_select_options'] !== 'undefined')
		{
			table_data_array[row][col] = {};
			table_data_array[row][col]['select_values'] = [];
			table_data_array[row][col]['select_names'] = [];
			table_data_array[row][col]['value'] = data;
		
			table_data_object[tbody_def[col]['db_field']][row] = {};
			table_data_object[tbody_def[col]['db_field']][row]['select_values'] = {};
			table_data_object[tbody_def[col]['db_field']][row]['select_names'] = {};
			table_data_object[tbody_def[col]['db_field']][row]['value'] = data;
			
			var values = item_data[tbody_def[col]['individual_select_options']]['values'];
			for(var opt=0;opt<values.length;opt++)
			{
				table_data_array[row][col]['select_values'][opt] = item_data[tbody_def[col]['individual_select_options']]['values'][opt];
				table_data_array[row][col]['select_names'][opt] = item_data[tbody_def[col]['individual_select_options']]['names'][opt];
				
				table_data_object[tbody_def[col]['db_field']][row]['select_values'][opt] = item_data[tbody_def[col]['individual_select_options']]['values'][opt];
				table_data_object[tbody_def[col]['db_field']][row]['select_names'][opt] = item_data[tbody_def[col]['individual_select_options']]['names'][opt];
				//table_data_array[row][tbody_def[column]['db_field']][qty] = data;	
			}
		}
		else
		{
			table_data_array[row][col] = data;
			table_data_object[tbody_def[col]['db_field']][row] = data;
			//table_data_array[row][tbody_def[column]['db_field']] = data;	

		}
		
		
	}
	
	updateTableDataForPost();
	updateTableArrayLineNumbers();
	return row;
}
function updateItemDataInTableArray(item_data, row)
{
	
	//!Dont forget to 	copyHTMLTableDataToArray(); before updating....
	
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
			else if(typeof tbody_def[col]['individual_select_options'] !== 'undefined')
			{
				table_data_array[row][col] = {};
				table_data_array[row][col]['select_values'] = [];
				table_data_array[row][col]['select_names'] = [];
				table_data_array[row][col]['value'] = data;
			
				table_data_object[tbody_def[col]['db_field']][row] = {};
				table_data_object[tbody_def[col]['db_field']][row]['select_values'] = {};
				table_data_object[tbody_def[col]['db_field']][row]['select_names'] = {};
				table_data_object[tbody_def[col]['db_field']][row]['value'] = data;
				
				var values = item_data[tbody_def[col]['individual_select_options']]['values'];
				//var values = item_data[['values'];
				for(var opt=0;opt<values.length;opt++)
				{
					table_data_array[row][col]['select_values'][opt] = item_data[tbody_def[col]['individual_select_options']]['values'][opt];
					table_data_array[row][col]['select_names'][opt] = item_data[tbody_def[col]['individual_select_options']]['names'][opt];
					
					table_data_object[tbody_def[col]['db_field']][row]['select_values'][opt] = item_data[tbody_def[col]['individual_select_options']]['values'][opt];
					table_data_object[tbody_def[col]['db_field']][row]['select_names'][opt] = item_data[tbody_def[col]['individual_select_options']]['names'][opt];
					//table_data_array[row][tbody_def[column]['db_field']][qty] = data;	
				}
			}
			else
			{
				table_data_array[row][col] = data;	
				table_data_object[tbody_def[col]['db_field']][row] = data;
				//table_data_array[row][tbody_def[column]['db_field']] = data;	
	
			}
		}
		else
		{
			//do nothing
			
		}
		//set it to nothing....
		
	}
	updateTableDataForPost();
	writeArrayToHTMLTable();
}
function addItemDataToHTMLTable(item_data)
{
	row_number = addRowToHTMLTable();
	writeArrayToHTMLTable();
}



/************* THESE FUNCTIONS I HOPE TO RE-USE *************************/
function enableAllRows()
{
	//go through each column and disable it
	
	var rowCount = getTbodyRowCount();
	for (row = 0;row<rowCount;row++)
	{
		var col_counter = 0;
		for (i=0; i<tbody_def.length;i++)
		{
			if(tbody_def[i]['type'] != 'hidden')
			{
				//select the type
				enableHTMLCell(tbody_def[i]['type'], row, col_counter);
				col_counter++;
			}
		}
	}

	
}
function disableHTMLRow(row)
{
	//go through each column and disable it
	var col_counter = 0;
	for (i=0; i<tbody_def.length;i++)
	{
		if(tbody_def[i]['type'] != 'hidden')
		{
			//select the type
			disableHTMLCell(tbody_def[i]['type'], row, col_counter);
			col_counter++;
		}
	}
}
function disableCell(row, name)
{
	col = getHTMLColumnNumberFromTableDefColumnName(name);
	disableHTMLCell(tbody_def[getTableDataColumnNumberFromTableDefColumnName(name)]['type'],row,col);
	
}
function disableHTMLCell(type, row, col_counter)
{
	var tbody = document.getElementById(tbody_id);
	if(type == 'input')
	{
		tbody.rows[row].cells[col_counter].childNodes[0].readOnly = 'true';
	}
	else if(type == 'select')
	{
		tbody.rows[row].cells[col_counter].childNodes[0].disabled = 'true';
	}
	else if(type == 'checkbox')
	{
		tbody.rows[row].cells[col_counter].childNodes[0].disabled = 'true';
	}
	tbody.rows[row].cells[col_counter].childNodes[0].style.color="grey";
}
function enableHTMLCell(type, row, col_counter)
{
	var tbody = document.getElementById(tbody_id);
	if(type == 'input')
	{
		tbody.rows[row].cells[col_counter].childNodes[0].readOnly = false;
	}
	else if(type == 'select')
	{
		tbody.rows[row].cells[col_counter].childNodes[0].disabled = false;
	}
	else if(type == 'checkbox')
	{
		tbody.rows[row].cells[col_counter].childNodes[0].disabled = false;
	}
	tbody.rows[row].cells[col_counter].childNodes[0].style.color="black";
}
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
	row = addRowToTableArray();
	row_number = addRowToHTMLTable();
	updateTable();
	updateTableDataForPost();
}
function addRowToTableArray()
{
	var	row = table_data_array.length;
	table_data_array[row] = [];
	
	var data;
	data = '';
	for(var col=0;col<tbody_def.length;col++)
	{
		//table_data_object[tbody_def[col]['db_field']] = {};
		//set it to nothing....
		if(typeof tbody_def[col]['price_array_index'] !== 'undefined')
		{
			table_data_array[row][col] = {};
			table_data_array[row][col]['array_values'] = [];
			table_data_array[row][col]['display_value'] = data;
		
			table_data_object[tbody_def[col]['db_field']][row] = {};
			table_data_object[tbody_def[col]['db_field']][row]['array_values'] = {};
			table_data_object[tbody_def[col]['db_field']][row]['display_value'] = {};
			
			//table_data_array[row][tbody_def[col]['db_field']] = [];	
			var quantity = item_data[tbody_def[col]['price_array_index']];
			for(var qty=0;qty<quantity;qty++)
			{
				table_data_array[row][col]['array_values'][qty] = data;
				table_data_object[tbody_def[col]['db_field']][row]['array_values'][qty] = data;
				//table_data_array[row][tbody_def[column]['db_field']][qty] = data;	
			}
		}
		//what about the special select values?
		else if(typeof tbody_def[col]['individual_select_options'] !== 'undefined')
		{
			table_data_array[row][col] = {};
			table_data_array[row][col]['select_values'] = [];
			table_data_array[row][col]['select_names'] = [];
			table_data_array[row][col]['value'] = data;
		
			table_data_object[tbody_def[col]['db_field']][row] = {};
			table_data_object[tbody_def[col]['db_field']][row]['select_values'] = {};
			table_data_object[tbody_def[col]['db_field']][row]['select_names'] = {};
			table_data_object[tbody_def[col]['db_field']][row]['value'] = data;
	
		}
		else
		{
			table_data_array[row][col] = data;
			table_data_object[tbody_def[col]['db_field']][row] = data;
			//table_data_array[row][tbody_def[column]['db_field']] = data;	

		}
		
		
	}
	
	
	

	
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
			element.name = tbody_def[c]['db_field'] + '[]';
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
			element.name = tbody_def[c]['db_field'] + '[]';

			//we can have individual select options or global options
			if (typeof tbody_def[c]['individual_select_options'] !== 'undefined')
			{
				//there are unique select items in here...
				var option = document.createElement('option');
				option.value = 'NULL';
				option.appendChild(document.createTextNode("Select..."));
				element.appendChild(option);
				//the names and values are in the data object


				for (var i in table_data_array[rowCount][getTableDataColumnNumberFromTableDefColumnName(tbody_def[c]['db_field'])]['select_values'])
				{
					option = document.createElement('option');
					option.value = table_data_array[rowCount][getTableDataColumnNumberFromTableDefColumnName(tbody_def[c]['db_field'])]['select_values'][i];
			
			
			
			option.appendChild(document.createTextNode(table_data_array[rowCount][getTableDataColumnNumberFromTableDefColumnName(tbody_def[c]['db_field'])]['select_names'][i]));
					element.appendChild(option);
				}
				
			}
			else
			{
				var option = document.createElement('option');
				option.value = 'NULL';
				option.appendChild(document.createTextNode("Select..."));
				element.appendChild(option);
				for (var i in tbody_def[c]['select_names'])
				{
					option = document.createElement('option');
					option.value = tbody_def[c]['select_values'][i];
					option.appendChild(document.createTextNode(tbody_def[c]['select_names'][i]));
					element.appendChild(option);
				}
			}
			if (typeof tbody_def[c]['properties'] !== 'undefined')
			{
				for (var index in tbody_def[c]['properties'])
				{
					eval('element.' + index + '= ' + tbody_def[c]['properties'][index] + ';');
					
				}
			}
			cell.appendChild(element);
		}
		else
		{
			alert('have not coded that');
		}

	}
	//updateSelectOptions();
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
						table_data_object[tbody_def[col]['db_field']][row] = tbody.rows[row].cells[column_counter].childNodes[0].checked;

					}
					else if (typeof tbody_def[col]['individual_select_options'] !== 'undefined')
					{
						table_data_array[row][col]['value'] = tbody.rows[row].cells[column_counter].childNodes[0].value;
						table_data_object[tbody_def[col]['db_field']][row]['value'] = tbody.rows[row].cells[column_counter].childNodes[0].value;
					}
					else
					{
						table_data_array[row][col] = tbody.rows[row].cells[column_counter].childNodes[0].value;
						table_data_object[tbody_def[col]['db_field']][row] = tbody.rows[row].cells[column_counter].childNodes[0].value;
							//alert(table_data_array[row][col]);
					}
				}		
				column_counter++;
			}
		}
	}
	updateTableDataForPost();
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
						else if(typeof tbody_def[col]['individual_select_options'] !== 'undefined')
						{
							value =  table_data_array[row][col]['value'];
							//now we need to put the options in....
							//clear the options...
							element = tbody.rows[row].cells[column_counter].childNodes[0];
							//alert(element.value);	
							if( element.options != 'null')
							{
								element.options.length = 0;
							}
							//reload
							//alert(table_data_array[row][col]['select_values'].length);
							for(opt=0;opt<table_data_array[row][col]['select_values'].length;opt++)
							{
								//load up the options
								option = document.createElement('option');
								option.value = table_data_array[row][col]['select_values'][opt];
								option.appendChild(document.createTextNode(table_data_array[row][col]['select_names'][opt]));
								element.appendChild(option);
							}
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
	updateTable();
	updateTableDataForPost();


}
function nameCells()
{
	//while this code works, it was easier to name the cells using the db_field + [] the get by name... makes more sense...
	
	/*//for posting to work we need names for these cells....
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;

	
	for(row=0; row<rowCount; row++)
	{
		column_counter=0;
		for(col= 0;col<tbody_def.length;col++)
		{
			if(typeof tbody_def[col]['POST'] === 'undefined')
			{
				//this one will post
				if(tbody_def[col]['type'] == 'hidden')
				{
					//hmmm
					str_hidden_name = tbody_def[col]['db_field'] + "_r" + row;
					str_hidden_value = table_data_array[row][col];
					//creating the hidden elements for POST
					element = document.createElement("input");
					element.type = "hidden";
					element.name = str_hidden_name;
					element.value = str_hidden_value;
					document.getElementById(formID).appendChild(element);
				}
				else
				{
					tbody.rows[row].cells[column_counter].childNodes[0].name = tbody_def[col]['db_field'] + "_r" + row;	
					
				}
				
			}
			else
			{
				//do not post
			} 
			
	

				column_counter++;
			}
			
		}*/
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
				table_data_object = deleteTableObjectRows(table_data_object, checked_rows);
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
function deleteTableObjectRows(table_object,rows)
{
	var newRowCounter = 0;
	var newObject = {};
	
	
	for(var col=0;col<tbody_def.length;col++)
	{
		//for (var i = 0;i<table_object[tbody_def[col]['db_field']].length;i++)
		newRowCounter = 0;
		for (var i in table_object[tbody_def[col]['db_field']])
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
				newObject[tbody_def[col]['db_field']] = {};
				newObject[tbody_def[col]['db_field']][newRowCounter] = table_object[tbody_def[col]['db_field']][i];
				newRowCounter++;
			}
		}
	}
	return newObject;
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
		copyHTMLTableDataToArray();
		//rearrange the rows into a new array
		for(var i=0;i<checked_rows.length;i++)
		{
			table_data_array = moveArrayRow(table_data_array, checked_rows[i], checked_rows[i]-1);
			table_data_object = moveTableObjectRow(table_data_object, checked_rows[i], checked_rows[i]-1);
			setChecks(checked_rows[i], checked_rows[i]-1);
		}	
		//put the array back into the table
		writeArrayToHTMLTable();

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
		copyHTMLTableDataToArray();
		//rearrange the rows into a new array
		for(var i=checked_rows.length-1;i>-1;i--)
		{
			var newRow = parseInt(checked_rows[i])+parseInt(1);
			table_data_array = moveArrayRow(table_data_array, checked_rows[i], newRow);
			table_data_object = moveTableObjectRow(table_data_object, checked_rows[i], newRow);
			setChecks(checked_rows[i], newRow);
		}	
		//put the array back into the table
		writeArrayToHTMLTable();

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
	newTableObject = {};
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
function moveTableObjectRow(tableObject, RowToMove, RowToMoveTo)
{	//ex row 2 row 3
	newTableObject = {};
	for(var col=0;col<tbody_def.length;col++)
	{
		console.log("col" + col);
		newTableObject[tbody_def[col]['db_field']] = {};
		//for  (i=0;i<tableObject[tbody_def[col]['db_field']].length;i++)
		for (var i in tableObject[tbody_def[col]['db_field']])
		{
			console.log(i);
			if (RowToMove == i)
			{
				newTableObject[tbody_def[col]['db_field']][i] = tableObject[tbody_def[col]['db_field']][RowToMoveTo];
			}
			else if (RowToMoveTo == i)
			{
				newTableObject[tbody_def[col]['db_field']][i] = tableObject[tbody_def[col]['db_field']][RowToMove];
			}
			else
			{
				newTableObject[tbody_def[col]['db_field']][i] = tableObject[tbody_def[col]['db_field']][i];
			}
		}
	}
	console.log(newTableObject);
	return newTableObject;
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
			table_data_object['row_number'][row] = row+1;
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
function updateTable()
{
	updateTableArrayLineNumbers();
	updateHTMLTableLineNumbers();
	updateSelectOptions();
	updateFooter();

	
}


function checkValidInput(control)
{
	
	column = getTableArrayColumnNumberFromHTMLColumnNumber(getCurrentColumn(control));
	if (typeof tbody_def[column]['valid_input'] !== 'undefined')
	{
		
		validInput = tbody_def[column]['valid_input'];
		checkInput2(control,validInput);
	}
}
function updateTableData(control)
{
	copyHTMLTableDataToArray();
	updateFooter();
	updateTableDataForPost();
	//writeArrayToHTMLTable(control);
}
function updateFooter()
{
	//we can assume a simple sum?
	// find what column has a 'footer'
	//sum that column
	
	for (col=0; col<tbody_def.length;col++)
	{
		if (typeof tbody_def[col]['footer'] !== 'undefined')
		{
			var sum = 0.0;
			for(row=0;row<table_data_array.length;row++)
			{
				sum = sum + myParseFloat(table_data_array[row][col]);
			}
			document.getElementById(tbody_def[col]['footer'][0]['db_field']).value = sum;
		}
		if (typeof tbody_def[col]['total'] !== 'undefined')
		{
			var sum = 0.0;
			for(row=0;row<table_data_array.length;row++)
			{
				sum = sum + myParseFloat(table_data_array[row][col]);
			}
			document.getElementById(tbody_def[col]['db_field'] + '_total').value = round2(sum,tbody_def[col]['total']);
		}
		
	}
	
}
function calculateDynamicTableColumnTotal(table_def_column_name)
{
	//assuming everything is written to the array?
	var sum = 0.0;
	var col = getTableDataColumnNumberFromTableDefColumnName(table_def_column_name);
	for(var row=0;row<table_data_array.length;row++)
	{
		sum = sum + myParseFloat(table_data_array[row][col]);
	}
	return sum;
	
}
function removeAndReloadAllSelectOptions()
{
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;
	for (col=0; col<tbody_def.length;col++)
	{
		if (typeof tbody_def[col]['unique_select_options'] !== 'undefined')
		{
			//remove all
			for(row=0;row<rowCount;row++)
			{
				length = document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].length;
				var counter = 1;
				for(opt = 1; opt < length;opt++)
				{
					document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].remove(counter);
					//counter++;
				}
			}
			//add all
			for(row=0;row<rowCount;row++)
			{
				for (var i in tbody_def[col]['select_names'])
				{
					element = document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row];
					option = document.createElement('option');
					option.value = tbody_def[col]['select_values'][i];
					option.appendChild(document.createTextNode(tbody_def[col]['select_names'][i]));
					element.appendChild(option);
					//element.add(option, i+1);
				}
			}
		}
	}
}
function reSelectSelect()
{
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;
	for (col=0; col<tbody_def.length;col++)
	{
		if (typeof tbody_def[col]['unique_select_options'] !== 'undefined')
		{
			// re-assign all
			for(row=0;row<rowCount;row++)
			{
				if(table_data_array[row][col] == '')
				{
									document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].value = 'NULL';
				}

				else
				{
					document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].value = table_data_array[row][col];

				}
			}
			
		}
	}

}
function reloadSelectOptions()
{
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;

	for (col=0; col<tbody_def.length;col++)
	{
		if (typeof tbody_def[col]['unique_select_options'] !== 'undefined')
		{
			//remove all but the selected value
			for(row=0;row<rowCount;row++)
			{
				length = document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].length;
				var counter = 1;
				for(opt = 1; opt < length;opt++)
				{
					if(document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].options[counter].value == table_data_array[row][col])
					{
						counter++;
					}
					else
					{
						document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].remove(counter);
					//counter++;
					}
				}
			}
			for(row=0;row<rowCount;row++)
			{
				for (var i in tbody_def[col]['select_values'])
				{
					bln_found = false;
					for(k=0;k<table_data_array.length;k++)
					{
						if(k==row)
						{
							//add this value
						}
						else
						{
							if(tbody_def[col]['select_values'][i] == table_data_array[k][col])
							{
								bln_found = true;
							}
						}
					}
					//add the value if it is not used
					if(!bln_found && tbody_def[col]['select_values'][i] != table_data_array[row][col])
					{
						//add
						element = document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row];
						option = document.createElement('option');
						option.value = tbody_def[col]['select_values'][i];
						option.appendChild(document.createTextNode(tbody_def[col]['select_names'][i]));
						element.appendChild(option);
					//element.add(option, i+1);
						
					}
					
				}
			}
			// re-assign all
			for(row=0;row<rowCount;row++)
			{
				document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].value = table_data_array[row][col];
			}
			
		}
	}
	
	
}
function updateSelectOptions()
{
	
	
	
	//this will remove the select value from the control
	//the select options should disclude any items selected
	//first we need to restore all the select options
	removeAndReloadAllSelectOptions();
	//next we need to re-select the option based on tbody data
	reSelectSelect();
	//finally we need to remove whatever options are selected - this prevents double selection!
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;

	// select the column
	// go down each row
		// compare each select list value against each row value - if found in another row then remove it from the list
	for (col=0; col<tbody_def.length;col++)
	{
		if (typeof tbody_def[col]['unique_select_options'] !== 'undefined')
		{
			//ok this column should only have unique values in the list
			for(row=0;row<rowCount;row++)
			{
				for(k=0;k<table_data_array.length;k++)
				{
					for (var i in tbody_def[col]['select_values'])
					{
						if (row!=k)
						{
							if(tbody_def[col]['select_values'][i] == table_data_array[k][col])
							{
								//there is a null value to ignore, that is index 0, so add 1 to all indexes to remove..
								//alert('remove index: ' + (parseInt(i)+1) + ' value ' + tbody_def[col]['select_values'][i] + ' from row ' + parseInt(row + 1) + ' value ' + table_data_array[k][col]);
								//because each list has destroyed index values check if the values match, if so remove it
								length = document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].length;
								var counter = 1;
								for(opt = 1; opt < length;opt++)
								{
									if (document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].options[counter].value == table_data_array[k][col])
									{
										//alert('remove option index: ' + counter + ' which has the value of ' + document.getElementsByName(tbody_def[col]['db_field']+ '_r' + row)[0].options[counter].value + ' From row ' + parseInt(row+1));
										document.getElementsByName(tbody_def[col]['db_field']+ '[]')[row].remove(counter);

									}
									else
									{
										counter++;
									}
								}
								
							}
						}
					}
				}
			}
		}
	}
	
}
function clearSelect(selectID)
{
	element = document.getElementById(selectID);
	alert(element.value);	
	if( element.options != 'null')
	{
		element.options.length = 0;
	}
}

function changeRowAndColumnWithArrow(e, control, tBodyId)
{
	//get rid of the return
	 console.log(e);
	
	//tBodyId = 'poc_tbody';
	var tBody = document.getElementById(tBodyId);
	var tBodyRowCount = tBody.rows.length;
	//get the number of columns
	var colCount = tBody.parentNode.rows[0].cells.length;
	//var tHead = document.getElementById('poc_thead');
	//var theadRowCount = tHead.rows.length;
	
	rowCount = tBodyRowCount;

	var col = control.parentNode.cellIndex;
	var tmp_row =  control.parentNode.parentNode.rowIndex;
	
	//var new_row = parseInt(tmp_row) - parseInt(theadRowCount);
	var new_row = parseInt(tmp_row) -1;
	var final_col = col;
	var final_row = new_row;
	//alert("in arrows" + e.keyCode);
	//alert ("Row count: " + rowCount);
	//alert ("Header Count: " + theadRowCount);
	//alert ("Current row: " + new_row);
	
	//depending on the arrow key  we just need to set focus
	 if (!e) e=window.event;
	 //alert(e.keyCode);
	  switch(e.keyCode)
	  {
	  case 37:
		// Key left.
		if (col-1 >= 0)
		{
			final_col = col - 1;
		}

		break;
	  case 38:
		// Key up.
		if (new_row-1 >= 0)
		{
			final_row = new_row - 1;
		}
		break;
	  case 39:
		// Key right.
		if (col+1 < colCount)
		{
			final_col = col + 1;
		}
		break;
	  case 40:
		// Key down.
		if (new_row+1 < rowCount)
		{
			final_row = new_row + 1;
		}
		break;
	  }
	  document.getElementById(tBodyId).rows[final_row].cells[final_col].childNodes[0].focus();
	  //document.getElementById(tBodyId).rows[final_row].cells[final_col].childNodes[0].select();
	 return noEnter(e);
}