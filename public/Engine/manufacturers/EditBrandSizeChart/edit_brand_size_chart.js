/*
				<th>System<br>ID</th>
				<th>Category</th>
				<th>Color or Fragrance</th>
				<th>Cup or Inseam Size</th>
				<th>Size Options (none will show OS)</th>
				<th>Comments</th>
*/


function addSizeRow(tBodyId) 
{
	var tBody = document.getElementById(tBodyId);
	var rowCount = tBody.rows.length;
	var row = tBody.insertRow(rowCount);	
	row.id = rowCount;
	cell=new Array();
	var element;
	
	var cols = tBody.rows[0].cells.length;
	var dynamic_col_modifier = cols - json_table_def[0].length +1;
	//innHTML works like anything... document.getElementById(tBodyId).rows[1].cells[3].childNodes[0].onchange = function(){alert(this.value);}
	
		cellCounter = 0;
		for (column=0;column<json_table_def[0].length;column++)
		{
			//ok one of these columns has an array
			if (typeof json_table_def[0][column]['value'] !== 'undefined'  && is_array(json_table_def[0][column]['value']))
			{
				for(column2 =0;column2<dynamic_col_modifier;column2++)
				{
					cell[cellCounter] = row.insertCell(cellCounter);
					cell[cellCounter].innerHTML = json_table_def[0][column]['html'];
					cellCounter++;
				}	
			}
			else
			{
				cell[cellCounter] = row.insertCell(cellCounter);
				cell[cellCounter].innerHTML = json_table_def[0][column]['html'];
				cellCounter++
			}	
		}
// now name all the cells
	nameTbodyCells(tBodyId);
	// add the number of rows for post - this is to protect against unposted checkboxes
	createHiddenInput(form_id, 'number_of_rows', rowCount+1);
}
function addSizeColumn(tBodyId)
{
	// get the tBody name: this is sent by the element.... 
	var tBody = document.getElementById(tBodyId);
	//get the number of columns
	var rowCount = tBody.rows.length;	
	if (rowCount==0)
	{
		addBrandSizeRow(tBodyId);
		rowCount = 1;
	}
	var cols = tBody.rows[0].cells.length;
	cell=new Array();
	var element;
	//need to know haw many columns before and after the 'dynamic' section of the array...
	
	for (column=0;column<json_table_def[0].length;column++)
	{
		//ok one of these columns has an array
		if (typeof json_table_def[0][column]['value'] !== 'undefined'  && is_array(json_table_def[0][column]['value']))
		{
			dynamic_array_column = column;
			number_of_columns_before_dynamic_column_start = column ;	
			number_of_columns_after_dynamic_column = json_table_def[0].length - number_of_columns_before_dynamic_column_start - 1;
			num_sizes = cols - number_of_columns_before_dynamic_column_start - number_of_columns_after_dynamic_column;
		}
	}

	
	
	insertCol = number_of_columns_before_dynamic_column_start + num_sizes;
	//For each row we need to add a column
	for (row=0;row<rowCount;row++)
	{
		cell[row] = tBody.rows[row].insertCell(insertCol);
		cell[row].innerHTML = json_table_def[0][dynamic_array_column]['html'];
	}
	// now name all the cells
	nameTbodyCells(tBodyId);
	//Fix the header col....
	var size_options_header = document.getElementById('size_header');
	size_options_header.colSpan =num_sizes+1;
	nameTbodyCells(tBodyId);
	// add the number of columns for post - this is to protect against unposted checkboxes
	createHiddenInput(form_id, 'number_of_columns', cols+1);
	num_size_columns = num_size_columns +1;
}
function deleteSizeColumn(tBodyId)
{
	// get the tBody name: this is sent by the element.... 
	var tBody = document.getElementById(tBodyId);
	//get the number of columns
	var rowCount = tBody.rows.length;	
	if (rowCount==0)
	{
		addBrandSizeRow(tBodyId);
		rowCount = 1;
	}
	var cols = tBody.rows[0].cells.length;
	cell=new Array();
	var element;
	//need to know haw many columns before and after the 'dynamic' section of the array...
	for (column=0;column<json_table_def[0].length;column++)
	{
		//ok one of these columns has an array
		if (typeof json_table_def[0][column]['value'] !== 'undefined'  && is_array(json_table_def[0][column]['value']))
		{
			dynamic_array_column = column;
			number_of_columns_before_dynamic_column_start = column ;	
			number_of_columns_after_dynamic_column = json_table_def[0].length - number_of_columns_before_dynamic_column_start - 1;
			num_sizes = cols - number_of_columns_before_dynamic_column_start - number_of_columns_after_dynamic_column;
		}
	}
	deleteCol = number_of_columns_before_dynamic_column_start + num_sizes-1;
	if (deleteCol>number_of_columns_before_dynamic_column_start)
	{
			//For each row we need to add a column
		for (row=0;row<rowCount;row++)
		{
			tBody.rows[row].deleteCell(deleteCol);
		}
		// now name all the cells
		nameTbodyCells(tBodyId);
		//Fix the header col....
		var size_options_header = document.getElementById('size_header');
		size_options_header.colSpan =num_sizes-1;
		nameTbodyCells(tBodyId);
		// add the number of columns for post - this is to protect against unposted checkboxes
		createHiddenInput(form_id, 'number_of_columns', tBody.rows[0].cells.length);
	}
	num_size_columns = num_size_columns -1;
}
function moveRowUp(tBodyId)
{
	//first find the row(s) that are checked
	checked_rows = findCheckedRows(tBodyId);
	//next check that the rows can be moved - they are in bounds
	bln_move_ok = true;
	for(var i=0;i<checked_rows.length;i++)
	{
		if((checked_rows[i] -1) <0) bln_move_ok = false
	}
	if (bln_move_ok)
	{
		//next copy the entire table to an array - need the size rows as well
		tableData = copyTableDataToArray(tBodyId);
		//rearrange the rows into a new array
		for(var i=0;i<checked_rows.length;i++)
		{
			tableData = moveArrayRow(tableData, checked_rows[i], checked_rows[i]-1);
			setChecks(tBodyId, checked_rows[i], checked_rows[i]-1);
		}	
		//put the array back into the table
		writeArrayToTable(tableData, tBodyId);
		nameTbodyCells(tBodyId);
	}
}
function moveRowDown(tBodyId)
{
	var tbody = document.getElementById(tBodyId);
	var rowCount = tbody.rows.length;
	//first find the row(s) that are checked
	checked_rows = findCheckedRows(tBodyId);
	//next check that the rows can be moved - they are in bounds
	bln_move_ok = true;
	for(var i=0;i<checked_rows.length;i++)
	{
		if((checked_rows[i] +1) > rowCount-1) bln_move_ok = false
	}
	if (bln_move_ok)
	{
		//next copy the entire table to an array - need the size rows as well
		tableData = copyTableDataToArray(tBodyId);
		//rearrange the rows into a new array
		for(var i=checked_rows.length-1;i>-1;i--)
		{
			var newRow = parseInt(checked_rows[i])+parseInt(1);
			tableData = moveArrayRow(tableData, checked_rows[i], newRow);
			setChecks(tBodyId, checked_rows[i], newRow);
		}	
		//put the array back into the table
		writeArrayToTable(tableData, tBodyId);
		nameTbodyCells(tBodyId);
	}
}
function setChecks(tBodyId, rowMoving, movingTo)
{
	var tbody = document.getElementById(tBodyId);
	tbody.rows[rowMoving].cells[0].childNodes[0].checked=false;
	tbody.rows[movingTo].cells[0].childNodes[0].checked=true;
	
}

function validateBrandSizeChartForm()
{
	var tbody = document.getElementById(tbody_id);
	var rowCount = tbody.rows.length;
	errors = '';
	
	//alert(json_table_def[0].length);
	size_column = 7;
	num_sizes = json_table_def[0][size_column].value.length;
	//alert(num_size_columns);
	
	
	//just validate that the category is not false - pretty weak but definately a quick fix....
	
	/*for (row=0;row<rowCount;row++)
	{
		if (tbody.rows[row].cells[3].childNodes[0].value == 'false')
		{
			errors+="You should select a category on row: " + (row + 1) +newline();
		}
		bln_size_blank = true;
		for (size=0;size<num_size_columns;size++)
		{
		 	if (tbody.rows[row].cells[size+size_column].childNodes[0].value != '')
		 	{
		 		bln_size_blank = false;
		 	}
		 	
		}
		if (bln_size_blank)
		{
			errors+="Assign at least one value to the Size Row: " + (row + 1) +newline();
		}
			
	}*/
 	
 	
 	
 	
 	if (errors == '')
    {
    	needToConfirm=false;
    	return true;
    }
    else
    {
    	alert(errors);	
    	return false;
    }
}

/**************Functions to move to the library *****************/
function nameTbodyCells(tBodyId)
{
	//this will name the elements of the cells to tbodyID + r1c1 needed for post data
	// send in the tbody ID - this will bypass header and footer crap
	
	var tbody = document.getElementById(tBodyId);
	var rowCount = tbody.rows.length;
	var colCount = tbody.rows[0].cells.length
	for(i=0; i<rowCount; i++)
	{
		for(j=0; j<colCount; j++)
		{
			tbody.rows[i].cells[j].childNodes[0].name = tBodyId + "r" + i + "c" + j;
		}
	}
}
function copyTableDataToArray(tBodyId)
{
	var tbody = document.getElementById(tBodyId);
	var rowCount = tbody.rows.length;
	var colCount = tbody.rows[0].cells.length;
	var tableData = [];
	for(i=0; i<rowCount; i++)
	{
		tableData[i] = new Array();
		for(j=0; j<colCount; j++)
		{
			if (tbody.rows[i].cells[j].childNodes[0].type == 'checkbox')
			{
				tableData[i][j] = tbody.rows[i].cells[j].childNodes[0].checked;
			}
			else
			{
				tableData[i][j] = tbody.rows[i].cells[j].childNodes[0].value;
			}
		}
	}
	return tableData;
}
function writeArrayToTable(tableData, tBodyId)
{
	var tbody = document.getElementById(tBodyId);
	var rowCount = tbody.rows.length;
	var colCount = tbody.rows[0].cells.length
	for(i=0; i<rowCount; i++)
	{
		for(j=0; j<colCount; j++)
		{
			if (tbody.rows[i].cells[j].childNodes[0].type == 'checkbox')
			{
				tbody.rows[i].cells[j].childNodes[0].checked=tableData[i][j];
			}
			else
			{
				tbody.rows[i].cells[j].childNodes[0].value	 = tableData[i][j];
			}
		}
	}
}
function copyArrayRows(array, rows)
{
	var newRowCounter = 0;
	var newArray = [];
	for (var i = 0;i<array.length;i++)
	{
		for(var r = 0;r<rows.length;r++)
		{
			if(i == rows[r])
			{
				//copy
				newArray[newRowCounter] = array[i];
				newRowCounter++;
				
			}
		}
		//transfer original
		newArray[newRowCounter] = array[i];
		newRowCounter++;
	}
	return newArray;
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
function copyRow(tBodyId)
{
	var tbody = document.getElementById(tBodyId);
	//first find the row(s) that are checked
	checked_rows = findCheckedRows(tBodyId);
	if (checked_rows.length>0)
	{
		//next copy the entire table to an array - need the size rows as well
		tableData = copyTableDataToArray(tbodyID);
		
		//next add the appropriate number of rows
		for (var i=0;i<checked_rows.length;i++)
		{
			addRow(tbodyID);
		}
		//now re-write each table row sticking the copy row after the checked row
		newData = copyArrayRows(tableData, checked_rows);
		//uncheck the newly copied rows

		writeArrayToTable(newData, tbodyID);
		for (var i=0;i<checked_rows.length;i++)
		{
			tbody.rows[checked_rows[i]+1].cells[0].childNodes[0].checked = false;
		}	
	}
	else
	{
	}
}
function deleteRow(tBodyId) 
{
	var answer = confirm("Confirm Delete Row(s)")
	if (answer)
	{	// delete selected rows
		var table = document.getElementById(tBodyId);
		var rowCount = table.rows.length;
		if (rowCount > 1)
		{
			//first find the row(s) that are checked
			checked_rows = findCheckedRows(tBodyId);
			if (checked_rows.length>0)
			{
				//next copy the entire table to an array - need the size rows as well
				tableData = copyTableDataToArray(tBodyId);
				//next add the appropriate number of rows
				for (var i=0;i<checked_rows.length;i++)
				{
					table.deleteRow(checked_rows[i]);
				}
				//now re-write each table row sticking the copy row after the checked row
				newData = deleteArrayRows(tableData, checked_rows);
				writeArrayToTable(newData, tBodyId);
				// add the number of columns for post - this is to protect against unposted checkboxes
				createHiddenInput(form_id, 'number_of_rows', rowCount-1);
				nameTbodyCells(tBodyId);
			}
			else
			{

			}
			
		} 
		else
		{
			alert("Can't delete when there is only one row");
		}
	}
	else
	{
		//do not delet rows
	}


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
function findCheckedRows(tBodyId)
{
	var table = document.getElementById(tBodyId);
	var rowCount = table.rows.length;
	var checked_rows = new Array();
	var counter = 0;
	for(var k=0; k<rowCount; k++) 
	{
		var chkbox = table.rows[k].cells[0].childNodes[0];

		if((null != chkbox) && (true == chkbox.checked) )
		{
			checked_rows[counter] = k; 
			counter = counter+1;
		}
	}
	return checked_rows;
}
function createHiddenInput(formID, name, value)
{
	//creating the hidden elements for POST
	element = document.createElement("input");
	element.type = "hidden";
	element.name = name;
	element.value = value;
	document.getElementById(formID).appendChild(element);	
}