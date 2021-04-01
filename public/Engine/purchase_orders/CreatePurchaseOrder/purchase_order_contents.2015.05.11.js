window.onload= init;
var view_product_url = '../../products/view_product.php?pos_product_id=';
var stlyeAjax = [];
var current_row = 0;
var current_column = 0;
var sizes_for_current_row = [];
//these are the column indicies
var checkbox_col;
//var mfg_color_description_col;
var color_code_col;
var style_col;
var color_description_col;
var title_col;
var category_col;
var description_col;
var cost_col;
var color_descritpion_col;
var retail_col;
var linked_row = [];
var style_number_source = []; //this is the source of the style number per row: 'custom' 'mfg' or 'pos' - needed to set the readabolity
var tBodyId = 'poc_tbody';
var needToConfirm = false;

$(window).bind('beforeunload', function(){
  unlock_entry();
   if (needToConfirm) 
    {
   		return "You have made changes on this page that you have not yet saved. If you navigate away from this page you will lose your unsaved changes";
  		//return result;
    }
    	
});
$(function(){  
  $("html").bind("ajaxStart", function(){  
     $(this).addClass('busy');  
   }).bind("ajaxStop", function(){  
     $(this).removeClass('busy');  
   });  
});

//timer
var save_poc_sec;
var start_poc_time;
InitPageTimer();
function InitPageTimer() 
{
    save_poc_sec = 60 * 1000;             //Save every minute
    start_poc_time = new Date().getTime();
    CheckTimerStatusAndSave();
}
function CheckTimerStatusAndSave() 
{
    //Check for session warning
    current_time = new Date().getTime();
    if (current_time > start_poc_time + save_poc_sec)
    {
        //save
        //saveDraft(tBodyId);
        saveDraftNoResponse();
        //alert('saved');
        //needToConfirm=false;
        //restart the timer
        InitPageTimer();
        //re-lock the page
        lock_entry();
    }
    else 
    {
    	//check the status every second
        recheck = setTimeout("CheckTimerStatusAndSave();", 1000);
    }
}
function init()
{
	//need to load tbody_data if it is there....
	//json_tbody_data comes from the form
	if (json_tbody_data.length > 0)
	{
		for(var row=0;row<json_tbody_data.length;row++)
		{
			addRow('poc_tbody');
			//last data column in tbody data is the size row
			for(var col=1;col<json_tbody_data[0].length-1;col++)
			{
				tmpName = tBodyId + 'r' + row + 'c' + col;
				
				//alert("temp Name: " + tmpName + " new value: " + json_tbody_data[row][col] );
				document.getElementsByName(tmpName)[0].value = json_tbody_data[row][col];
			}
			sizes_for_current_row[row] = json_tbody_data[row][json_tbody_data[0].length-1];
			style_number_source[row] = rows_with_system_styles[row];
		}

	}
	else
	{
		addRow('poc_tbody');
	}
	updateRowEditabilityLinksTotalAndNames();
	setRowFocus(0,style_col);
}
function addRow(tBodyId) 
{
	//Break this out into 3 sections - The "Header" the "sizes" and the "calculations"
	// Drop the data from the form
	formID = 'poc_form';
	// get the tBody name: this is sent by the element.... 
	var tBody = document.getElementById(tBodyId);
	//get the number of columns
	var cols = tBody.parentNode.rows[0].cells.length;
	//alert (table.tBodies[0].rows.length);
	//var rowCount = table.rows.length;
	//var row = table.insertRow(rowCount);
	var rowCount = tBody.rows.length;
	var row = tBody.insertRow(rowCount);	
	row.id = rowCount;
	linked_row[rowCount] = rowCount;
	
	//if there is only one row of sizes then lets set those up, otherwise init the size row to undefined
	var tHead = document.getElementById('poc_thead');
	var theadrowCount = tHead.rows.length;
	if (theadrowCount == 1)
	{
		sizes_for_current_row[rowCount] = 0;
	}
	else
	{
		sizes_for_current_row[rowCount] = 'undefined';
	}
	cell=new Array();
	var element;
//*************************      Checkbox ***************************************
    var c = 0;
    checkbox_col = c;

    cell[c] = row.insertCell(c);
    element = document.createElement("input");
    element.type = "checkbox";
    element.onclick = function(){setCurrentRow(this);}
    cell[c].appendChild(element);
//Style# - this should be a smart entry - get a list of all of our products, begin typing and it should be selectable
	c = c + 1;
	style_col = c;
	cell[c] = row.insertCell(c);
	element = document.createElement("input");
	element.type = "text";
	element.size = "6";
	element.maxLength = "60";
	element.className = "poStyle";
	//styleTextControlNoBorders(element);
	
	// Set the change to call code to get the color code for the chosen stle number
	// manufacturer ID comes from the php file that creates this script and function... need to drop it out of php and into javascript like this:
	//var manufacturer_id = "<?php echo $manufacturer_id; ?>";
	element.onchange = function(){needToConfirm = true;loadDataFromStyleNumber(this);}
	//element.onlostfocus = function(){getColorCodes(pos_manufacturer_id,pos_manufacturer_brand_id, this);}
	//element.onkeyup = function(){loadStyleNumbers(this);}
	element.onkeyup  = function(){checkInput(this,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/0123456789-_");}
	element.onclick = function(){setCurrentRow(this);}
	//element.onclick = function(){tellMeYourCell(this);}
	cell[c].appendChild(element);	
	
//Color code - this could be a listing of available colors or a new color. No spaces please

	c = c + 1;
	color_code_col = c;

	cell[c] = row.insertCell(c);
	element = document.createElement("input");
	element.type = "text";
	element.size = "6";
	element.maxLength = "60";
	element.onclick = function(){setCurrentRow(this);autoCompleteColorCodesFromColorCodeCol(this);}
	//element.onclick = function(){setCurrentRow(this);}

	element.onchange = function(){needToConfirm=true;}
	
	element.onkeyup  = function(){checkInput(this,"ABCDEFGHIJKLMNOPQRSTUVWXYZ/0123456789-");}
	element.onkeypress = function(e){return noEnter(e);}
   	//element.onkeypress = function(event){return event.keyCode!=13;}							

	cell[c].appendChild(element);
	
//Color description

	c = c+1;
	color_description_col = c;

	cell[c] = row.insertCell(c);
	element = document.createElement("input");
	element.type = "text";
	element.size = "12";
	element.maxLength = "255";
	element.className = "poStyle";
	element.onclick = function(){setCurrentRow(this);}
	//element.onkeyup  = function(){checkInput(this,"ABCDEFGHIJKLMNOPQRSTUVWXYZ/0123456789()");}
	element.onchange = function(){needToConfirm=true;}
	element.onkeypress = function(e){return noEnter(e);}
	//element.onkeyup = function(){setSlaveRows();}
	//styleTextControlNoBorders(element);
	cell[c].appendChild(element);
	
// Title - this could be available or a new one could pop up - should be multi-line
	c = c+1;
	title_col = c;

	cell[c] = row.insertCell(c);
	element = document.createElement("input");
	element.type = "text";
	element.size = "40";
	element.maxLength = "255";
	element.className = "poStyle";
	element.onclick = function(){setCurrentRow(this);}
	element.onchange = function(){needToConfirm=true;}
	element.onkeyup = function(){setLinkedRowsValue(this);}
	element.onkeypress = function(e){return noEnter(e);}
	//styleTextControlNoBorders(element);
	cell[c].appendChild(element);

//Category column - this needs a drop down of our main categories - if it not there we will need to create a new one?
	c = c+1;
	category_col = c;
	cell[c] = row.insertCell(c);
	element = document.createElement('select');
	element.style.width = "7em";
	element.onclick = function(){setCurrentRow(this);}
	element.onchange = function(){setLinkedRowsValue(this);needToConfirm=true;}
	element.onkeyup = function(){}
	element.onkeypress = function(e){return noEnter(e);}
	
	var option = document.createElement('option');
	option.value = '';
	option.appendChild(document.createTextNode("Select..."));
	element.appendChild(option);
	for (var i in category_names)
	{
		option = document.createElement('option');
		option.value = category_ids[i];
		option.appendChild(document.createTextNode(category_names[i]));
		element.appendChild(option);
	}
	cell[c].appendChild(element);
	//Cup Size - this should be a letter only - error check later - 
	if (bln_cup == 1)
	{
	
		c = c + 1;
		cell[c] = row.insertCell(c);
		element = document.createElement("input");
		element.type = "text";
		element.size = "2";
		element.maxLength = "3";
		element.className = "poSizes";
		element.onclick = function(){setCurrentRow(this);}
		element.onchange = function(){needToConfirm=true;}
		element.onkeyup  = function(){checkInput(this,"ABCDEFGHIJKLMN");}
		element.onkeypress = function(e){changeRowColumnWithArrow(e, this, tBodyId);return noEnter(e);}
		cell[c].appendChild(element);	
	}
	//Inseam Size - this should be a number only -
	if (bln_inseam == 1)
	{
	
		c = c + 1;
		cell[c] = row.insertCell(c);
		element = document.createElement("input");
		element.type = "text";
		element.size = "2";
		element.maxLength = "2";
		element.className = "poSizes";
		element.onclick = function(){setCurrentRow(this);}
		element.onchange = function(){needToConfirm=true;}
		element.onkeypress = function(e){return noEnter(e);}
		element.onkeyup  = function(){checkInput(this,"0123456789");}
		cell[c].appendChild(element);	
	}
	//console.log(brand_size_chart);
	if(typeof brand_size_chart.attributes !== 'undefined')
	{
		for(atr=0;atr<brand_size_chart.attributes.length;atr++)
		{
			c = c + 1;
			cell[c] = row.insertCell(c);
			element = document.createElement("input");
			element.type = "text";
			element.size = "4";
			element.maxLength = "10";
			element.className = "poSizes";
			element.onclick = function(){setCurrentRow(this);}
			element.onchange = function(){needToConfirm=true;}
			element.onkeypress = function(e){return noEnter(e);}
			element.onkeyup  = function(){checkInput(this,"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.-+");}
			cell[c].appendChild(element);	
		}
		
	}
	
	
	//sizing 1 through N - numeric entry only
	
	c = c+1;
	max_c = c + parseInt(num_sizes);	
	for(c=start_columns;c < max_c;c++)
	{
		cell[c] = row.insertCell(c);
		element = document.createElement("input");
		element.type = "text";
		element.size = "2";
		element.maxLength = "3";
		element.className = "poSizes";
		element.onclick = function(){setCurrentRow(this);}
		element.onkeyup  = function(){checkInput(this,"0123456789");}
		//element.onchange = function(){updateTotal();needToConfirm=true;}
		element.onkeypress = function(e){changeRowColumnWithArrow(e, this, tBodyId);return noEnter(e);}
		element.onblur = function(){updateTotal();}
		cell[c].appendChild(element);
	
	}
	c =c -1;
	//qty - calcuated
	c = c + 1;
	cell[c] = row.insertCell(c);
	element = document.createElement("input");
	element.type = "text";
	element.value = "0";
	element.size = "3";
	element.maxLength = "6";
	element.className = "poSizes";
	element.readOnly= "true";
	element.onclick = function(){setCurrentRow(this);}
	element.onkeypress = function(e){return noEnter(e);}
	cell[c].appendChild(element);
	
	//cost - numeric only
	c = c+1;
	cost_col = c;
	cell[c] = row.insertCell(c);
	element = document.createElement("input");
	element.type = "text";
	element.size = "5"; 
	element.maxLength = "9";
	element.className = "poCurrency";
	element.onclick = function(){setCurrentRow(this);}
	element.onkeyup  = function(){checkInput(this,"0123456789.");setLinkedRowsValue(this);}
	element.onkeypress = function(e){return noEnter(e);}
	//element.onchange = function(){updateTotal();}
	element.onblur = function(){updateTotal();updateRetail(this);}
	cell[c].appendChild(element);	
	
	//retail - numeric only
	c = c+1;
	retail_col = c;
	cell[c] = row.insertCell(c);
	element = document.createElement("input");
	element.type = "text";
	element.size = "5";
	element.maxLength = "9";
	element.className = "poCurrency";
	element.onclick = function(){setCurrentRow(this);}
	element.onkeyup  = function(){checkInput(this,"0123456789.");setLinkedRowsValue(this);}
	element.onkeypress = function(e){return noEnter(e);}
	element.onchange = function(){needToConfirm=true;}
	cell[c].appendChild(element);	
	
	//total - caluclated
	c = c+1;
	cell[c] = row.insertCell(c);
	element = document.createElement("input");
	element.type = "text";
	element.size = "6";
	element.maxLength = "10";
	element.className = "poCurrency";
	element.readOnly= "true";
	element.onclick = function(){setCurrentRow(this);}
	element.onkeypress = function(e){return noEnter(e);}
	cell[c].appendChild(element);		
	
//remarks - textarea
	c = c +1;
	cell[c] = row.insertCell(c);
	element = document.createElement("textarea");
	element.onclick = function(){setCurrentRow(this);}
	element.onchange = function(){needToConfirm=true;}
	cell[c].appendChild(element);	
	
// add the size row as undefined
	createPOSTvaluesForSIZES();
// now name all the cells
	nameTbodyCells(tBodyId);
	current_row = rowCount;
	setRowFocus(current_row, style_col);
	//alert(current_row);
}
function copyArrayRows(array, rows)
{
	
	//i reallly want to transfer the rows to the end of the array....
	
	
	//first copy the original array....
	
	
	var newRowCounter = 0;
	var newArray = [];
	for (var i = 0;i<array.length;i++)
	{
		//transfer original
		newArray[newRowCounter] = array[i];
		
		newRowCounter++;
	}
	//console.log(newArray);
	for (var i = 0;i<array.length;i++)
	{
		for(var r = 0;r<rows.length;r++)
		{
			if(i == rows[r])
			{
				//copy
				newArray[newRowCounter] = array[i];
				//take out the color codes!
		//newArray[newRowCounter][color_code_col] = '';
		//newArray[newRowCounter][color_description_col] = '';
				newRowCounter++;
				
			}
		}
		//transfer original
		//newArray[newRowCounter] = array[i];
		//newRowCounter++;
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
function copyRow(tbodyID)
{
	var tbody = document.getElementById(tbodyID);
	//first find the row(s) that are checked
	
	checked_rows = findCheckedRows(tbodyID);
	console.log(checked_rows);
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
		
		checkRows(tbodyID,checked_rows);
		 
		//this should uncheck the new row, if it is positioned right after the row....
		/*for (var i=0;i<checked_rows.length;i++)
		{
			tbody.rows[checked_rows[i]+1].cells[0].childNodes[0].checked = false;
		}*/
		updateRowEditabilityLinksTotalAndNames();
		setRowFocus(checked_rows[0], style_col);	
	}
	else
	{
		setRowFocus(0, style_col);	
	}
}
function deleteRow(tbodyID) 
{
	var answer = confirm("Confirm Delete Row(s)")
	if (answer)
	{	// delete selected rows
		var table = document.getElementById(tbodyID);
		var rowCount = table.rows.length;
		if (rowCount > 1)
		{
			//first find the row(s) that are checked
			checked_rows = findCheckedRows(tbodyID);
			if (checked_rows.length>0)
			{
				//next copy the entire table to an array - need the size rows as well
				tableData = copyTableDataToArray(tbodyID);
				//next add the appropriate number of rows
				for (var i=0;i<checked_rows.length;i++)
				{
					table.deleteRow(checked_rows[i]);
				}
				//now re-write each table row sticking the copy row after the checked row
				newData = deleteArrayRows(tableData, checked_rows);
				writeArrayToTable(newData, tbodyID);
				updateRowEditabilityLinksTotalAndNames();
				setRowFocus(checked_rows[0], style_col);	
				uncheckRows(tbodyID);
			}
			else
			{
				setRowFocus(0, style_col);	
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
function moveRowUp(tbodyID)
{
	//first find the row(s) that are checked
	checked_rows = findCheckedRows(tbodyID);
	//next check that the rows can be moved - they are in bounds
	bln_move_ok = true;
	for(var i=0;i<checked_rows.length;i++)
	{
		if((checked_rows[i] -1) <0) bln_move_ok = false
	}
	if (bln_move_ok)
	{
		//next copy the entire table to an array - need the size rows as well
		tableData = copyTableDataToArray(tbodyID);
		//rearrange the rows into a new array
		for(var i=0;i<checked_rows.length;i++)
		{
			tableData = moveArrayRow(tableData, checked_rows[i], checked_rows[i]-1);
			setChecks(tbodyID, checked_rows[i], checked_rows[i]-1);
		}	
		//put the array back into the table
		writeArrayToTable(tableData, tbodyID);
		updateRowEditabilityLinksTotalAndNames();
		setRowFocus(checked_rows[i]-1, style_col);
	}
}
function moveRowDown(tbodyID)
{
	var tbody = document.getElementById(tbodyID);
	var rowCount = tbody.rows.length;
	//first find the row(s) that are checked
	checked_rows = findCheckedRows(tbodyID);
	//next check that the rows can be moved - they are in bounds
	bln_move_ok = true;
	for(var i=0;i<checked_rows.length;i++)
	{
		if((checked_rows[i] +1) > rowCount-1) bln_move_ok = false
	}
	if (bln_move_ok)
	{
		//next copy the entire table to an array - need the size rows as well
		tableData = copyTableDataToArray(tbodyID);
		//rearrange the rows into a new array
		for(var i=checked_rows.length-1;i>-1;i--)
		{
			var newRow = parseInt(checked_rows[i])+parseInt(1);
			tableData = moveArrayRow(tableData, checked_rows[i], newRow);
			setChecks(tbodyID, checked_rows[i], newRow);
		}	
		//put the array back into the table
		writeArrayToTable(tableData, tbodyID);
		updateRowEditabilityLinksTotalAndNames();
		setRowFocus(newRow, style_col);
	}
}
function setChecks(tbodyID, rowMoving, movingTo)
{
	var tbody = document.getElementById(tbodyID);
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
function copyTableDataToArray(tbodyID)
{
	var tbody = document.getElementById(tbodyID);
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
		//add the size row in as well!
		tableData[i][colCount] = sizes_for_current_row[i];
		//add the source in as well
		tableData[i][colCount+1] = style_number_source[i];
	}
	return tableData;
}

function writeArrayToTable(tableData, tbodyID)
{
	var tbody = document.getElementById(tbodyID);
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
		//add the size row in as well!
		sizes_for_current_row[i] = tableData[i][colCount];
		style_number_source[i] = tableData[i][colCount+1];
	}
}
function uncheckRows(tbodyID)
{
	var tbody = document.getElementById(tbodyID);
	var rowCount = tbody.rows.length;
	for(var k=0; k<rowCount; k++) 
	{
		tbody.rows[k].cells[0].childNodes[0].checked = false;
	}
}
function checkRows(tbodyID, checked_rows)
{
	var tbody = document.getElementById(tbodyID);
	var rowCount = tbody.rows.length;
	for(var k=0; k<rowCount; k++) 
	{
		for(var j=0; j<checked_rows.length; j++) 
		{
			if( k == checked_rows[j])
			{
				tbody.rows[k].cells[0].childNodes[0].checked = true;
			}
			else
			{
				tbody.rows[k].cells[0].childNodes[0].checked = false;
			}
		}
	}
}
function findCheckedRows(tbodyID)
{
	var table = document.getElementById(tbodyID);
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
function copyData(tableID, copy_from_row, copy_to_row)
{
	var table = document.getElementById(tableID);
	var copy_row = table.rows[copy_from_row];
	var paste_row = table.rows[copy_to_row];
	//var chkbox = row.cells[0].childNodes[0];
	
	//alert(copy_row.cells.length); //20
	//for (i = 0; i > copy_row.cells.length; i++)
	//{
	
		//paste_row.cells[1].childNodes[0].options[paste_row.cells[1].childNodes[0].selectedIndex].value = 
		
		//alert(copy_row.cells[1].childNodes[0].options[copy_row.cells[1].childNodes[0].selectedIndex].value);
		paste_row.cells[1].childNodes[0].selectedIndex = copy_row.cells[1].childNodes[0].selectedIndex;
		for(i=1; i< copy_row.cells.length; i++)
		{
			paste_row.cells[i].childNodes[0].value = copy_row.cells[i].childNodes[0].value;
		}
		//want the size row data here as well??
		sizes_for_current_row[copy_to_row] = sizes_for_current_row[copy_from_row];
		
}
function updateTotal()
{
	//alert('update');
	var tBodyId = 'poc_tbody';
	var tBody = document.getElementById(tBodyId);
	var rowCount = tBody.rows.length;
	
	//need to get the values of all size boxes and the cost box
	//rowIndex = control.parentNode.parentNode.rowIndex;
	//row = control.parentNode.parentNode;
	//alert("size 1 value: " + row.cells[7].childNodes[0].value);
	

	//go down each row
	for (var r = 0;r<rowCount;r++)
	{
		var	qty = 0;
		var	total = 0;
		// sum up the sizes
		// What is the starting row of the size column?
		// dropped start_columns out from php
		// end column? is start + num sizes
		var max_c = parseInt(start_columns) + parseInt(num_sizes);
		for(c=start_columns;c<max_c;c++)
		{
			//str_sizeId = "poc_size_c" + c + "r" + row;
			//alert(str_sizeId);
			//if (document.getElementById(str_sizeId).value == '')
			if (tBody.rows[r].cells[c].childNodes[0].value == '')
			{
				// the number is 0 so do nothing or enter a 0
				//row.cells[c].childNodes[0].value = 0;
			} else
			{
				qty = qty + parseInt(tBody.rows[r].cells[c].childNodes[0].value);
			}
		}
		//qty row is now c
		qty_row = c;
		cost_row = c+1;
		retail_row = c+2;
		total_row = c + 3;
		
		
		tBody.rows[r].cells[qty_row].childNodes[0].value = qty;
	
		if (tBody.rows[r].cells[cost_row].childNodes[0].value == '')
		{
			total = 0;
		}
		else
		{
			total = qty * parseFloat(tBody.rows[r].cells[cost_row].childNodes[0].value);
		}
		//str_totalID = "poc_total_r" + row;
		tBody.rows[r].cells[total_row].childNodes[0].value = roundNumber(total,2);	
	}
	
	//var rowCount = control.parentNode.parentNode.parentNode.rows.length;
	var grand_total = 0;
	var grand_qty = 0;
	// now we need to sum all the qty and totals
	//alert(rowCount);
	for (var r = 0;r<rowCount;r++)
	{
		
		grand_qty = grand_qty + parseInt(tBody.rows[r].cells[qty_row].childNodes[0].value);
		
		grand_total = grand_total + parseFloat(tBody.rows[r].cells[total_row].childNodes[0].value);
	}
	
	// now update the total - because of the cell structure when I defined the row the qty coloumn is 2 and the total column is 5
	
	//this is the footer id
	//alert(control.parentNode.parentNode.parentNode.parentNode.tFoot.id);
	
	//this is qty id
	//alert(control.parentNode.parentNode.parentNode.parentNode.tFoot.rows[0].cells[2].id)
	tBody.parentNode.tFoot.rows[0].cells[2].innerHTML = grand_qty;
	//this is grand total id
	//alert(control.parentNode.parentNode.parentNode.parentNode.tFoot.rows[0].cells[5].id)
	tBody.parentNode.tFoot.rows[0].cells[5].innerHTML = roundNumber(grand_total,2);
	
	//creating the hidden elements for POST
	element = document.createElement("input");
	element.type = "hidden";
	element.name = 'po_grand_total';
	element.value = grand_total;
	document.getElementById(formID).appendChild(element);	
	

				
}
function updateRetail(control)
{
	//if retail is empty, simply multiply the cost by 2.5
	
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	var tHead = document.getElementById('poc_thead');
	var theadrowCount = tHead.rows.length;
	test_row = control.parentNode.parentNode.rowIndex - theadrowCount;
	var cost = control.value;
	var retail = tbody.rows[test_row].cells[retail_col].childNodes[0].value;
	if (retail == '' || retail == '0')
	{
		tbody.rows[test_row].cells[retail_col].childNodes[0].value = round(cost*2.5,2);
	}
}

function checkInput(objName,validInput)
{
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


	//if (validInput != "ABCDEFGHIJ") 
	//{
	//	updateTotal();
	//}

}	
function checkCup(objName,cupSizes)
{
	var cupField = objName;
	//we want to capitalize the value first
	objName.value=objName.value.toUpperCase();
	if (chkCup(objName,cupSizes) == false)
	{
		cupField.select();
		cupField.focus();
		return false;
	}
	else
	{
		return true;
	}
}
function chkCup(objName, cupSizes)
{
var checkOK = cupSizes;
var checkStr = objName;
for (i = 0;  i < checkStr.value.length;  i++)
{
	ch = checkStr.value.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
			if (ch == checkOK.charAt(j))
				break;
			if (j == checkOK.length)
			{	
				 allValid = false;
				 break;
			}
			if (ch != ",")
				allNum += ch;
}
if (!allValid)
{	
	alertsay = "Please enter only these values \""
	alertsay = alertsay + checkOK + "\" in the \"" + checkStr.name + "\" field."
	alert(alertsay);
	return (false);
}

}
function clickSetFocus(control)
{
	control.focus();
}
function roundNumber(number, digits) 
{
	number = parseFloat(number);
	var multiple = Math.pow(10, digits);
	var rndedNum = Math.round(number * multiple) / multiple;
	return rndedNum;
}
function listCells(tableID)
{
	var table = document.getElementById(tableID);
	var rowCount = table.rows.length;
	//var row = table.rows[0];
	var colCount = table.rows[0].cells.length
	// this is the first data row in the table: 
	// table.rows[4].cells[2].childNodes[0].value
	
	//alert("Table Columns: " + colCount + " Table Rows: " + rowCount);
	
	// skipping the header lets go through the cells
	// can also reference thead, tbody and tfoot....
	if (rowCount > 4)
	{
		for(i=4; i<rowCount; i++)
		{
			for(j=2; j<colCount; j++)
			{
				alert("r" + i + "c" + j + "value = " + table.rows[i].cells[j].childNodes[0].value + "\n" + table.rows[i].cells[j].childNodes[0].id);
			
			}
		}
	}
	
	
}
function nameTbodyCells(tbodyID)
{
	//this will name the elements of the cells to tbodyID + r1c1 needed for post data
	// send in the tbody ID - this will bypass header and footer crap
	
	var tbody = document.getElementById(tbodyID);
	var rowCount = tbody.rows.length;
	var colCount = tbody.rows[0].cells.length
	for(i=0; i<rowCount; i++)
	{
		for(j=0; j<colCount; j++)
		{
			tbody.rows[i].cells[j].childNodes[0].name = tbodyID + "r" + i + "c" + j;
			//alert (tbody.rows[i].cells[j].childNodes[0].name) ;
		}
	}
}
function setCurrentRow(control)
{
	/*
	* this function will set the size chart row for a purchase order content row
	*
	*
	*/
	//Get the row index starting at 0 (Set it up to remove the header rows....)
	var tHead = document.getElementById('poc_thead');
	var rowCount = tHead.rows.length;
	old_row = current_row;
	current_row = control.parentNode.parentNode.rowIndex - rowCount;	
	current_column = control.parentNode.cellIndex;
	//alert(current_column);
	highlightSizes(sizes_for_current_row[current_row]);
	
	// if the current row does not have a size chart selected and we try to change rows then pop up an alert
	//alert("Old Row: " + old_row + ' Current_row ' + current_row + ' Size chart  ' + sizes_for_current_row[current_row]);
	if ((typeof sizes_for_current_row[old_row] === 'undefined' || sizes_for_current_row[old_row] =='undefined') && old_row != current_row)
	{
		old_row++;
		//alert ("You forgot to pick a size row for row " + old_row );
		//control.focus();
	}
	
}
function getSizeRowFromManufacturerBrandSizeID(id)
{
	return_val = 'undefined';
	for(i=0;i<pos_manufacturer_brand_size_ids.length;i++)
	{
		//alert('id: ' + id + ' pos_data ' + pos_manufacturer_brand_size_ids[i]);
		if(id == pos_manufacturer_brand_size_ids[i])
		{
			return_val = i;
		}
	}
	//alert (return_val);
	return return_val;
}
function setSizeRowForMFGorNoUPCData()
{
	return_val = 'undefined';
	if(pos_manufacturer_brand_size_ids.length==1)
	{
		return_val = 0;
	}
	return return_val;
}
function loadDataFromStyleNumber(control)
{
		
	//First want to check if the style number is in our system
	//If it is load the data from our system, possibly make it read - only?
	//Second check the upc system - if it is there load the data
	
	// if it is nowhere then nothing special needs to be done.
	//alert("disabled auto complete");
	//$(control).autocomplete("disable");
	//$(control).autocomplete("close");
	//$(control).autocomplete("destroy");
	//alert("disabled auto complete");
	
	
	//get the style number and color code:
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	var tHead = document.getElementById('poc_thead');
	var theadrowCount = tHead.rows.length;
	test_row = control.parentNode.parentNode.rowIndex - theadrowCount;
	//color_and_style = tbody.rows[test_row].cells[style_col].childNodes[0].value + tbody.rows[test_row].cells[color_description_col].childNodes[0].value;
	//var blnStyleInOurSystem = 'false';
	//var blnStyleInUPC = 'false';
	var style_number_to_check = tbody.rows[test_row].cells[style_col].childNodes[0].value;
	var loaded_style_numer = control.value;

	//getColorCodes(pos_manufacturer_id,pos_manufacturer_brand_id, this)
	//$(control.parentNode.parentNode.cells[color_code_col].childNodes[0]).autocomplete({ source:  color_codes},{ minLength: 0 }});
	//load the color codes
	var dateHack = new Date().getTime();
	
	//load the data for the style numbers

	stlyeAjax[test_row] = $.getJSON("get_style_data.php", 
		{ style_number: style_number_to_check,  pos_manufacturer_brand_id: pos_manufacturer_brand_id, datehack: dateHack  },
		function(data)
		{
  			console.log('data returning....');
  			console.log(data);
  			if (data != null)
  			 {
  			 	//Alert('data');
  			 	//might need this stuff again
  			 	//get the style number and color code:
				var tbody = document.getElementById('poc_tbody');
				var rowCount = tbody.rows.length;
				var tHead = document.getElementById('poc_thead');
				var theadrowCount = tHead.rows.length;
				test_row = control.parentNode.parentNode.rowIndex - theadrowCount;
				if (data[0].source == 'pos_system')
				{
					
					if (category_ids.indexOf(data[0].pos_category_id) == -1)
					{
						alert('You are trying to add a previously ordered product with a category not included in this purchase order\'s categories. Unfortunately you need start a new purchase order and choose a category that includes the category of this product to include this product. You can also change the product category, however I can see that is going to be another coding nightmare.');
					}
					else
					{
					
						alert('Pos Data');
						style_number_source[test_row] = 'pos';
						//this is data from our system. 
						// load the title, cost, retail and make it read only
						tbody.rows[test_row].cells[title_col].childNodes[0].value = data[0].title;
						tbody.rows[test_row].cells[title_col].childNodes[0].readOnly = 'true';
						tbody.rows[test_row].cells[title_col].childNodes[0].style.color="grey";
						tbody.rows[test_row].cells[title_col].childNodes[0].onclick = //function(){setCurrentRow(this);window.open(view_product_url+data[0].pos_product_id);}
					
						tbody.rows[test_row].cells[cost_col].childNodes[0].value = roundNumber(data[0].cost,2);
						//tbody.rows[test_row].cells[cost_col].childNodes[0].readOnly = 'true';
						//tbody.rows[test_row].cells[cost_col].childNodes[0].style.color="grey";
					
						tbody.rows[test_row].cells[retail_col].childNodes[0].value = roundNumber(data[0].retail_price,2);
						//tbody.rows[test_row].cells[retail_col].childNodes[0].readOnly = 'true';
						//tbody.rows[test_row].cells[retail_col].childNodes[0].style.color="grey";
					
						tbody.rows[test_row].cells[category_col].childNodes[0].value = data[0].pos_category_id;
						tbody.rows[test_row].cells[category_col].childNodes[0].disabled = 'true';
					
					
						//this is where we can pull the size row and set it...
						sizes_for_current_row[test_row] =  getSizeRowFromManufacturerBrandSizeID(data[0].pos_manufacturer_brand_size_id);
					
						//data[0].pos_manufacturer_brand_size_id is the size row id.... can we tag that to the header rows?
					
						//this idea isn't really working well so took it out. setSizeChartBasedOnCategory(control);
						findLinkedRow(control);
						highlightSizes(sizes_for_current_row[test_row]);
						autoCompleteColorCodes(control);
					}
				}
				else
				{
					alert('UPC Data');
					style_number_source[test_row] = 'mfg';
					tbody.rows[test_row].cells[title_col].childNodes[0].value = data[0].style_description;
					tbody.rows[test_row].cells[title_col].childNodes[0].readOnly = false;
					tbody.rows[test_row].cells[title_col].childNodes[0].style.color="black";
					//tbody.rows[test_row].cells[title_col].childNodes[0].onclick = function(){setCurrentRow(this);}
					
					tbody.rows[test_row].cells[cost_col].childNodes[0].value = roundNumber(data[0].cost,2);
					tbody.rows[test_row].cells[cost_col].childNodes[0].readOnly = false;
					tbody.rows[test_row].cells[cost_col].childNodes[0].style.color="black";
					
					tbody.rows[test_row].cells[retail_col].childNodes[0].value = roundNumber(data[0].msrp,2);
					tbody.rows[test_row].cells[retail_col].childNodes[0].readOnly = false;
					tbody.rows[test_row].cells[retail_col].childNodes[0].style.color="black";
					
					tbody.rows[test_row].cells[category_col].childNodes[0].value = '';
					tbody.rows[test_row].cells[category_col].childNodes[0].disabled = false;
					//clearSizeChartForRow(control);
					
					sizes_for_current_row[test_row] = setSizeRowForMFGorNoUPCData();
					highlightSizes(sizes_for_current_row[test_row]);
					findLinkedRow(control);
					autoCompleteColorCodes(control);
				}
  			 }
  			 else
  			 {
  			 		style_number_source[test_row] = 'custom';
  			 		//this is a new style number, is it the "master"?
  			 		//min row is set with the first occurance of a style number
					var tbody = document.getElementById('poc_tbody');
					var rowCount = tbody.rows.length;
					
					var smallest_row = rowCount;
					
					for(var check_row=0;check_row<rowCount;check_row++)
					{
						if (tbody.rows[test_row].cells[style_col].childNodes[0].value == tbody.rows[check_row].cells[style_col].childNodes[0].value)
						{	
							if (smallest_row > check_row ) 
							{
								smallest_row = check_row;
							}
						}
					}
					//alert('Smallest Row: ' + smallest_row);
					if (test_row == smallest_row)
					{
						//master
						//tbody.rows[test_row].cells[title_col].childNodes[0].value = '';
  			 			tbody.rows[test_row].cells[title_col].childNodes[0].readOnly = false;
  			 			tbody.rows[test_row].cells[title_col].childNodes[0].style.color="black";
						//tbody.rows[test_row].cells[title_col].childNodes[0].onclick = function(){setCurrentRow(this);}
					
						//tbody.rows[test_row].cells[cost_col].childNodes[0].value = '';
						tbody.rows[test_row].cells[cost_col].childNodes[0].readOnly = false;
						tbody.rows[test_row].cells[cost_col].childNodes[0].style.color="black";
						
						//tbody.rows[test_row].cells[retail_col].childNodes[0].value = '';
						tbody.rows[test_row].cells[retail_col].childNodes[0].readOnly = false;
						tbody.rows[test_row].cells[retail_col].childNodes[0].style.color="black";
						//tbody.rows[test_row].cells[category_col].childNodes[0].value = '';
						tbody.rows[test_row].cells[category_col].childNodes[0].disabled = false;
						//clearSizeChartForRow(control);
						sizes_for_current_row[test_row] = setSizeRowForMFGorNoUPCData();
						highlightSizes(sizes_for_current_row[test_row]);
					}
					else
					{
						//slave to smallest row
						tbody.rows[test_row].cells[title_col].childNodes[0].value = tbody.rows[smallest_row].cells[title_col].childNodes[0].value;
						tbody.rows[test_row].cells[title_col].childNodes[0].readOnly = 'true';
						tbody.rows[test_row].cells[title_col].childNodes[0].style.color="grey";
						//tbody.rows[test_row].cells[title_col].childNodes[0].onclick = function(){setCurrentRow(this);}
						
						//alert(tbody.rows[smallest_row].cells[category_col].childNodes[0].value);
						tbody.rows[test_row].cells[category_col].childNodes[0].value = tbody.rows[smallest_row].cells[category_col].childNodes[0].value;
					
					
					
						tbody.rows[test_row].cells[cost_col].childNodes[0].value = tbody.rows[smallest_row].cells[cost_col].childNodes[0].value;
						tbody.rows[test_row].cells[cost_col].childNodes[0].readOnly = 'true';
						tbody.rows[test_row].cells[cost_col].childNodes[0].style.color="grey";
						
						tbody.rows[test_row].cells[retail_col].childNodes[0].value = tbody.rows[smallest_row].cells[retail_col].childNodes[0].value;
						tbody.rows[test_row].cells[retail_col].childNodes[0].readOnly = 'true';
						tbody.rows[test_row].cells[retail_col].childNodes[0].style.color="grey";
					
					
						tbody.rows[test_row].cells[category_col].childNodes[0].value = tbody.rows[smallest_row].cells[category_col].childNodes[0].value
						tbody.rows[test_row].cells[category_col].childNodes[0].disabled = 'true';
						
					
					
						
						sizes_for_current_row[test_row] = sizes_for_current_row[smallest_row];
						highlightSizes(sizes_for_current_row[test_row]);
						
					}
  			 		findLinkedRow(control);
   			 }
   			 //tbody.rows[test_row].cells[color_description_col].childNodes[0].value = '';
   			 //tbody.rows[test_row].cells[color_code_col].childNodes[0].value = '';
 		});
	
	//setSizeChartBasedOnCategorycontrol;
	//setSlaveRows();
	
}
function setSlaveRows()
{
	/*
	*This function will loop through the tbody rows and set "slave rows"
	*A slave row is a row that has the same style number as a row above it. The Title, cost, etc, will all be linked to the row that is the slave's master
	*/
	
	var slaveRow = [];
	//The master row is when the slave row is the same as the row i.e. row is 0 slaverow[row] = 0
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	//alert (tbody.rows[0].cells[style_col].childNodes[0].value);
	//var tHead = document.getElementById('poc_thead');
	//var theadrowCount = tHead.rows.length;
	//color_and_style = tbody.rows[test_row].cells[style_col].childNodes[0].value + tbody.rows[test_row].cells[color_description_col].childNodes[0].value;
	
	//go through each row
	// the first occurance of a style number indicates it is a master row
	for(row=0; row<rowCount; row++)
	{
		//min row is set with the first occurance of a style number
		var min_row = rowCount;
		for(check_row=0;check_row<rowCount;check_row++)
		{
			if (tbody.rows[row].cells[style_col].childNodes[0].value == tbody.rows[check_row].cells[style_col].childNodes[0].value)
			{	
				if (min_row > check_row ) 
				{
					min_row = check_row;
				}
				slaveRow[row] = min_row;
				//alert('Min row : ' + min_row + ' Row: ' + row + ' Check row ' + check_row + ' Slave row: ' + slaveRow[row]);
			}
		}
	}
	for(row=0; row<rowCount; row++)
	{
		if  (slaveRow[row] === row)
		{
			//This is the master row
			//we want to keep this as read only if the product is in the system....
			//how do we know the style is in our system?
			
			/*tbody.rows[row].cells[title_col].childNodes[0].readOnly = false;
			tbody.rows[row].cells[category_col].childNodes[0].disabled = false;
			//tbody.rows[row].cells[description_col].childNodes[0].readOnly = false;
			tbody.rows[row].cells[cost_col].childNodes[0].readOnly = false;
			tbody.rows[row].cells[retail_col].childNodes[0].readOnly = false;

			tbody.rows[row].cells[title_col].childNodes[0].style.color="black";
			//tbody.rows[row].cells[description_col].childNodes[0].style.color="black";
			tbody.rows[row].cells[cost_col].childNodes[0].style.color="black";
			tbody.rows[row].cells[retail_col].childNodes[0].style.color="black"*/
		}
		else
		{
			//alert("RowCount: " + rowCount + " Row: " + row + " Slave Row: " + slaveRow[row]);
			// set this to read
			tbody.rows[row].cells[title_col].childNodes[0].readOnly = 'true';
			tbody.rows[row].cells[category_col].childNodes[0].disabled = 'true';
			//tbody.rows[row].cells[description_col].childNodes[0].readOnly = 'true';
			tbody.rows[row].cells[cost_col].childNodes[0].readOnly = 'true';
			tbody.rows[row].cells[retail_col].childNodes[0].readOnly = 'true';
			
			tbody.rows[row].cells[title_col].childNodes[0].style.color="grey";
			//tbody.rows[row].cells[description_col].childNodes[0].style.color="grey";
			tbody.rows[row].cells[cost_col].childNodes[0].style.color="grey";
			tbody.rows[row].cells[retail_col].childNodes[0].style.color="grey";
			

			
			
			//set the size rows the same	
			//sizes_for_current_row[row] = sizes_for_current_row[slaveRow[row]];
		}
	}	
}
function setRowsEditAbility()
{
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	//find out which rows have the same style number, and which row is the first occurrance of the style number
	var slaveRow = [];
	for(var row=0; row<rowCount; row++)
	{
		//min row is set with the first occurance of a style number
		var min_row = rowCount;
		for(var check_row=0;check_row<rowCount;check_row++)
		{
			if (tbody.rows[row].cells[style_col].childNodes[0].value == tbody.rows[check_row].cells[style_col].childNodes[0].value)
			{	
				if (min_row > check_row ) 
				{
					min_row = check_row;
				}
				slaveRow[row] = min_row;
				//alert('Min row : ' + min_row + ' Row: ' + row + ' Check row ' + check_row + ' Slave row: ' + slaveRow[row]);
			}
		}
	}
	for(var row=0; row<rowCount; row++)
	{
		//is the style number from the POS system
		if (style_number_source[row] == 'pos')
		{
			//set values to read only... no need to link a master row to a slave as these values are all read only
			tbody.rows[row].cells[title_col].childNodes[0].readOnly = 'true';
			tbody.rows[row].cells[title_col].childNodes[0].style.color="grey";
			tbody.rows[row].cells[cost_col].childNodes[0].readOnly = false;
			tbody.rows[row].cells[cost_col].childNodes[0].style.color="black";
			tbody.rows[row].cells[retail_col].childNodes[0].readOnly = false;
			tbody.rows[row].cells[retail_col].childNodes[0].style.color="black";
			tbody.rows[row].cells[category_col].childNodes[0].disabled = 'true';
			//alert('color code');
			//autoCompleteColorCodes(tbody.rows[row].cells[color_code_col].childNodes[0]);
			//can't seem to re-load color codes????? after save...
					
		}
		else
		{
			if  (slaveRow[row] === row)
			{
				//This is the master row
				//alert('Row ' + row + " is a master");
				tbody.rows[row].cells[title_col].childNodes[0].readOnly = false;
				tbody.rows[row].cells[category_col].childNodes[0].disabled = false;
				tbody.rows[row].cells[cost_col].childNodes[0].readOnly = false;
				tbody.rows[row].cells[retail_col].childNodes[0].readOnly = false;
				tbody.rows[row].cells[title_col].childNodes[0].style.color="black";
				tbody.rows[row].cells[cost_col].childNodes[0].style.color="black";
				tbody.rows[row].cells[retail_col].childNodes[0].style.color="black"
			}
			else
			{
				// this row slaves to a master and is read only
				tbody.rows[row].cells[title_col].childNodes[0].readOnly = 'true';
				tbody.rows[row].cells[category_col].childNodes[0].disabled = 'true';
				tbody.rows[row].cells[cost_col].childNodes[0].readOnly = 'true';
				tbody.rows[row].cells[retail_col].childNodes[0].readOnly = 'true';
				tbody.rows[row].cells[title_col].childNodes[0].style.color="grey";
				tbody.rows[row].cells[cost_col].childNodes[0].style.color="grey";
				tbody.rows[row].cells[retail_col].childNodes[0].style.color="grey";
			}
		}
	}
}
function updateRowEditabilityLinksTotalAndNames()
{
	setRowsEditAbility();
	findLinkedRow();
	nameTbodyCells('poc_tbody');
	updateTotal();
}
function setRowFocus(row, col)
{
	highlightSizes(sizes_for_current_row[row]);
	console.log("row:" + row);
	console.log("col:" + col);
	document.getElementById('poc_tbody').rows[row].cells[col].childNodes[0].focus();
	current_row = row;
}
function findLinkedRow()
{
	/*
	* this function will find what row is the first occurance of the style number
	*/
	var tbody = document.getElementById('poc_tbody');
	
	var tHead = document.getElementById('poc_thead');
	var theadRowCount = tHead.rows.length;
	//control_row = control.parentNode.parentNode.rowIndex - theadRowCount;	
	var rowCount = tbody.rows.length;
	//intialize linked row
	//linked_row[control_row] = control_row;
	for(var row=0; row<rowCount; row++)
	{
		//min row is set with the first occurance of a style number
		var smallest_row = rowCount;
		for(check_row=0;check_row<rowCount;check_row++)
		{
			if (tbody.rows[row].cells[style_col].childNodes[0].value == tbody.rows[check_row].cells[style_col].childNodes[0].value)
			{	
				if (smallest_row > check_row ) 
				{
					smallest_row = check_row;
				}
				linked_row[row] = smallest_row;
				//alert('Min row : ' + min_row + ' Row: ' + row + ' Check row ' + check_row + ' Slave row: ' + slaveRow[row]);
				sizes_for_current_row[row] = sizes_for_current_row[smallest_row];
				//highlightSizes(sizes_for_current_row[test_row]);
			}
		}
		
	}
	createPOSTvaluesForSIZES();

}
function setLinkedRowsValue(control)
{
	/*
	* This function will set the values for any rows that are linked
	* linked_row[] = row is set up before hand from the change of style number
	* check each row in the body, if the row is linked then set the value
	*/
	
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	var tHead = document.getElementById('poc_thead');
	var theadRowCount = tHead.rows.length;
	control_row = control.parentNode.parentNode.rowIndex - theadRowCount;	
	//get the current column:
	var control_col = control.parentNode.cellIndex;
	
	//when a user enters a value in the "master row" the "linked rows" will update
	
	for(row=0; row<rowCount; row++)
	{
		// check that the linked row is not itself....
		if (row != linked_row[row])
		{
				tbody.rows[row].cells[control_col].childNodes[0].value = tbody.rows[linked_row[row]].cells[control_col].childNodes[0].value;
		}
	}
}
function loadStyleNumbers(control)
{
	//first disable all other autocompletes
	var dateHack = new Date().getTime();
	$(control).autocomplete(
	{source: function( request, response ) 
		{
			$.ajax(
			{
				url: "load_style_numbers.php",
				dataType: "json",
				//async: false,
				data: 
				{
					style_number: control.value,
					pos_manufacturer_brand_id: pos_manufacturer_brand_id,
					
					datehack: dateHack
				},
				type: "GET",
				success: function( data )
				{
					var styles_array = [];
					for(var k=0;k<data.length;k++)
					{
						styles_array[k] = data[k].style_number;
					}
					response(styles_array);
				}
			});
		}
	},
	{ minLength: 2 });

}
function sizeSelect(control)
{
	//This function is called by the header and sets the current working row to the selected size row
	var tHead = document.getElementById('poc_thead');
	selected_row = control.parentNode.rowIndex;
	var rowCount = tHead.rows.length;
		if (linked_row[current_row] == current_row)
		{
			//master can set the size row
			sizes_for_current_row[current_row] = selected_row;
			updateSlaveSizeRows(selected_row);
			highlightSizes(selected_row);
			setRowFocus(current_row, current_column);
		}
		else
		{
			//alert('Current row: ' + current_row + ' linked row ' + linked_row[current_row] + ' sizes: ' + sizes_for_current_row[linked_row[current_row]]);
			if (sizes_for_current_row[linked_row[current_row]] == 'undefined')
			{
				alert('linked row, updating the master row');
				//switch to the master row
				original_row = current_row;
				current_row = linked_row[current_row];
				sizes_for_current_row[current_row] = selected_row;
				updateSlaveSizeRows(selected_row);
				highlightSizes(selected_row);
				setRowFocus(original_row, current_column);
				
			}
			else
			{
				alert("The row you are trying to select a size for is linked to row: " + linked_row[current_row]);
				setRowFocus(current_row, current_column);
			}
			//slave
			//can't do anything
		}
		
}
function updateSlaveSizeRows(selected_row)
{
	// NOW WE NEED TO SET ALL THE SIZE ROWS FOR SLAVE ROWS... 
	for (row=0;row<linked_row.length;row++)
	{
		if (linked_row[row] == current_row && row != current_row)
		{
			sizes_for_current_row[row] = selected_row;
		}
	}
	createPOSTvaluesForSIZES();
	needToConfirm=true;
}
function createPOSTvaluesForSIZES()
{
	for(var row = 0; row<sizes_for_current_row.length; row++)
	{
		str_hidden_name = "style_size_chart_r" + row;
		str_hidden_value = sizes_for_current_row[row];
		//creating the hidden elements for POST
		element = document.createElement("input");
		element.type = "hidden";
		element.name = str_hidden_name;
		element.value = str_hidden_value;
		document.getElementById(formID).appendChild(element);
		
		str_hidden_name2 = "pos_manufacturer_brand_size_id_r" + row;
		str_hidden_value2 = pos_manufacturer_brand_size_ids[sizes_for_current_row[row]];
		//creating the hidden elements for POST
		element = document.createElement("input");
		element.type = "hidden";
		element.name = str_hidden_name2;
		element.value = str_hidden_value2;
		document.getElementById(formID).appendChild(element);	
	}
	
}
/*function setSizeChartBasedOnCategory(control)
{
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	var tHead = document.getElementById('poc_thead');
	var theadrowCount = tHead.rows.length;
	selected_row = control.parentNode.parentNode.rowIndex - theadrowCount;

		for (sz = 0;sz<size_category_ids.length;sz++)
		{
			if (size_category_ids[sz] == tbody.rows[selected_row].cells[category_col].childNodes[0].value)
			{
				//Because we are calling this from the style number, overwrite any other size data
				sizes_for_current_row[selected_row] = sz;
			}
		}
	createPOSTvaluesForSIZES();
}*/
function clearSizeChartForRow(control)
{
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	var tHead = document.getElementById('poc_thead');
	var theadrowCount = tHead.rows.length;
	selected_row = control.parentNode.parentNode.rowIndex - theadrowCount;

	//Because we are calling this from the style number, overwrite any other size data
	sizes_for_current_row[selected_row] = 'false';

	//want to set this as a cookie and as a hidden value
	//The value should be: size_chart_r
	str_hidden_name = "style_size_chart_r" + selected_row;
	str_hidden_value = 'false';

	highlightSizes(sizes_for_current_row[selected_row]);
}
function highlightSizes(sizeRow)
{
/*
* 	This function will highlight the appropriate header row
*	Highlight can be one of three values: dim (unselected) red (selected) or med-grey(selected, can't change it because the row is a slave) .slave_selected_size_row
*/
	
	var tHead = document.getElementById('poc_thead');
	var header_rowCount = tHead.rows.length;
	//first are we a master or slave row?
	if (linked_row[current_row] == current_row)
	{
		//master
		colorClass = "selected_size_row";
	}
	else
	{
		//slave
		colorClass = "slave_selected_size_row";
	}
	for (var header_row = 0; header_row<header_rowCount;header_row++)
	{
		if (header_row==sizeRow)
		//This is the selected row
		{
			if (header_row == 0)
			{
				max_c = parseInt(start_columns) + parseInt(num_sizes);
				sc = parseInt(start_columns);
			}
			else
			{
				max_c = parseInt(num_sizes);
				sc = 0;
			}
			for(c=sc;c < max_c;c++)
			{
				//tHead.rows[header_row].cells[c].style.color="blue";
				//tHead.rows[header_row].cells[c].style.backgroundColor="white";
				tHead.rows[header_row].cells[c].className=colorClass;
			}
		}
		else
		//unselect this row
		{
			if (header_row == 0)
			{
				max_c = parseInt(start_columns) + parseInt(num_sizes);
				sc = parseInt(start_columns);
			}
			else
			{
				max_c = parseInt(num_sizes);
				sc = 0;
			}
			for(c=sc;c < max_c;c++)
			{
				//tHead.rows[header_row].cells[c].style.color="rgb(240,240,240)";
				//tHead.rows[header_row].cells[c].style.backgroundColor="white";
				tHead.rows[header_row].cells[c].className="unselected_size_row";
			}
		}
	}
}
function autoCompleteColorCodes(control)
{
	//get the style number and color code:
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	var tHead = document.getElementById('poc_thead');
	var theadrowCount = tHead.rows.length;
	test_row = control.parentNode.parentNode.rowIndex - theadrowCount;
	//color_and_style = tbody.rows[test_row].cells[style_col].childNodes[0].value + tbody.rows[test_row].cells[color_description_col].childNodes[0].value;
	//var blnStyleInOurSystem = 'false';
	//var blnStyleInUPC = 'false';
	var style_number_to_check = tbody.rows[test_row].cells[style_col].childNodes[0].value;
	var loaded_style_numer = control.value;
	
	//getColorCodes(pos_manufacturer_id,pos_manufacturer_brand_id, this)
	//$(control.parentNode.parentNode.cells[color_code_col].childNodes[0]).autocomplete({ source:  color_codes},{ minLength: 0 }});
	//load the color codes
	var dateHack = new Date().getTime();


/*$(control.parentNode.parentNode.cells[color_code_col].childNodes[0]).autocomplete(
	{ source: function( request, response ) 
		{
			$.ajax(
			{
				url: "getColorCodes.php",
				dataType: "json",
				async: false,
				data: 
				{
					style_number: loaded_style_numer,
					pos_manufacturer_brand_id: pos_manufacturer_brand_id,
					datehack : dateHack
				},
				type: "GET",
				change: function (event, ui) 
				{
        			if (ui.item) {
            		// user selected an item 
            		alert(ui.item);          
        			} 
        			else 
        			{
           			 //user entered a new item 
           			 alert('new item' + ui.item); 
        			}
        		},
				success: function( returned_colors )
				{
					
					var color_codes = [];
					for(var k=0;k<returned_colors.length;k++)
					{
						//alert(returned_colors[k].color_description.charAt(0).toUpperCase() + returned_colors[k].color_description.substring(1).toLowerCase());
						returned_colors[k].color_description = returned_colors[k].color_description.charAt(0).toUpperCase() + returned_colors[k].color_description.substring(1).toLowerCase();
						color_codes.push({label: returned_colors[k].color_code + ':' + returned_colors[k].color_description, value: returned_colors[k].color_code});
					}
					response(color_codes);
				}
			});
		}
	},
	{ minLength: 0 },
	{select: function(event, ui)
	{
				color_description_array = ui.item.label.split(":");
				color_description = color_description_array[1].toLowerCase();
				color_description = color_description.charAt(0).toUpperCase() + color_description.substring(1).toLowerCase();
				control.parentNode.parentNode.cells[color_description_col].childNodes[0].value=color_description;
			}
	});*/
}
function autoCompleteColorCodesFromColorCodeCol(control)
{
	//get the style number and color code:
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	var tHead = document.getElementById('poc_thead');
	var theadrowCount = tHead.rows.length;
	test_row = control.parentNode.parentNode.rowIndex - theadrowCount;
	//color_and_style = tbody.rows[test_row].cells[style_col].childNodes[0].value + tbody.rows[test_row].cells[color_description_col].childNodes[0].value;
	//var blnStyleInOurSystem = 'false';
	//var blnStyleInUPC = 'false';
	var style_number_to_check = tbody.rows[test_row].cells[style_col].childNodes[0].value;
	//var loaded_style_numer = control.value;
	
	//getColorCodes(pos_manufacturer_id,pos_manufacturer_brand_id, this)
	//$(control.parentNode.parentNode.cells[color_code_col].childNodes[0]).autocomplete({ source:  color_codes},{ minLength: 0 }});
	//load the color codes
	var dateHack = new Date().getTime();


	$(control).autocomplete(
	{ source: function( request, response ) 
		{
			$.ajax(
			{
				url: "getColorCodes.php",
				dataType: "json",
				async: true,
				timeout: 1000,
				data: 
				{
					style_number: style_number_to_check,
					pos_manufacturer_brand_id: pos_manufacturer_brand_id,
					datehack : dateHack
				},
				type: "GET",
				change: function (event, ui) 
				{
        			if (ui.item) {
            		/* user selected an item */ 
            		alert(ui.item);          
        			} 
        			else 
        			{
           			 /* user entered a new item */
           			 alert('new item' + ui.item); 
        			}
        		},
				success: function( returned_colors )
				{
					
					var color_codes = [];
					for(var k=0;k<returned_colors.length;k++)
					{
						//alert(returned_colors[k].color_description.charAt(0).toUpperCase() + returned_colors[k].color_description.substring(1).toLowerCase());
						returned_colors[k].color_description = returned_colors[k].color_description.charAt(0).toUpperCase() + returned_colors[k].color_description.substring(1).toLowerCase();
						color_codes.push({label: returned_colors[k].color_code + ':' + returned_colors[k].color_description, value: returned_colors[k].color_code});
					}
					response(color_codes);
				},
				error: function()
				{
				console.log('color code timeout');
				}
			});
		}
	},
	{ minLength: 0 },
	{select: function(event, ui)
	{
				color_description_array = ui.item.label.split(":");
				color_description = color_description_array[1].toLowerCase();
				color_description = color_description.charAt(0).toUpperCase() + color_description.substring(1).toLowerCase();
				control.parentNode.parentNode.cells[color_description_col].childNodes[0].value=color_description;
			}
	});
}
function saveDraft(tBodyId)
{
	
	post_string = getPostData(formID);
	$.post("update_poc_data_to_server.php", post_string,
   	function(response) {
     alert(response);
     console.log(response);
     needToConfirm=false;
   });
	
}
function saveDraftNoResponse()
{
	post_string = getPostData(formID);
	$.post("update_poc_data_to_server.php", post_string,
   	function(response) {
   		console.log(response);
   		if (response.trim() == 'STORED')
   		{
   			
   		}
   		else
   		{
   			alert("ERROR: " + response);
   		}
     needToConfirm=false;
   });
}
function getPostData(formID)
{
	var elem = document.getElementById(formID).elements;
	var post_string = {};
	for(var i = 0; i < elem.length; i++)
	{
		post_string[elem[i].name] = elem[i].value;
	} 
	return post_string;
}
function unlock_entry()
{
	var post_string = {};
	post_string['table'] = 'pos_purchase_orders';
	post_string['primary_key_name'] = 'pos_purchase_order_id';
	post_string['primary_key_value'] = pos_purchase_order_id;
	unlock_url = POS_ENGINE_URL + '/includes/php/unlock_entry.php';
	$.ajax({
	  type: 'POST',
	  url: unlock_url,
	  data: post_string,
	  async: false,
	  success: 	function(response) {
	  //alert(response);
	  }
	});
		
}
function lock_entry()
{
	var post_string = {};
	post_string['table'] = 'pos_purchase_orders';
	post_string['primary_key_name'] = 'pos_purchase_order_id';
	post_string['primary_key_value'] = pos_purchase_order_id;
	unlock_url = POS_ENGINE_URL + '/includes/php/lock_entry.php';
	$.ajax({
	  type: 'POST',
	  url: unlock_url,
	  data: post_string,
	  async: true,
	  success: 	function(response) {
	  //alert(response);
	  }
	});
}
function saveDraftAndClose(tBodyId)
{
	if (validatePOCForm())
	{
		
		//unlock_entry();
		
		post_string = getPostData(formID);
		$.post("update_poc_plus_write_sub_ids.php", post_string,
		function(response) 
		{
			alert(response);
			if (validatePOCForm())
			{
				needToConfirm=false;
				window.location = complete_location;
			}
			
		});
	}
}
function saveDraftAndContinue()
{
	
	post_string = getPostData(formID);
	$.ajax({
	  type: 'POST',
	  url: "update_poc_data_to_server.php",
	  data: post_string,
	  async: false,
	  success: 	function(response) {}
	});


   
	if (validatePOCForm())
	{
		alert('Validated');
		//unlock_entry();
		needToConfirm=false;
		//submit form
		poc_form = document.getElementById(formID);
		poc_form.submit();
		
	}

}

function exit(tBodyId)
{
	//unlock_entry();
	//save it!!
		post_string = getPostData(formID);
	$.post("update_poc_data_to_server.php", post_string,
   	function(response) {
     alert(response);
     needToConfirm=false;
     window.location = complete_location;
   });
	
}

function deletePurchaseOrder(pos_purchase_order_id)
{
	var answer = confirm("Are you certain about deleting this purchase order?")
	if (answer)
	needToConfirm=false;
	window.location = "../DeletePurchaseOrder/delete_po.php?pos_purchase_order_id="+pos_purchase_order_id;
	
}


function validatePOCForm()
{
	//style number, cost, title, and color can't be empty - all size rows must be defined
	var tbody = document.getElementById('poc_tbody');
	var rowCount = tbody.rows.length;
	var error = '';
	var warning = '';
	if (document.getElementById('poc_title').value == '')
	{
    		error = error + 'Missing Title \r\n';
    }
    	
	for(row=0; row<rowCount; row++)
	{
		style_number = tbody.rows[row].cells[style_col].childNodes[0].value;
		title = tbody.rows[row].cells[title_col].childNodes[0].value;
		human_identifier = ' ' + style_number + ' ' + title;
		if (tbody.rows[row].cells[style_col].childNodes[0].value == '')
		{
    		error = error + 'Missing Style Number for Row ' + (row+1) + human_identifier +'\r\n';
    		setRowFocus(row, style_col);
    		//tbody.rows[row].cells[style_col].childNodes[0].focus();
    		current_row =row;
    	}
    	if (tbody.rows[row].cells[color_code_col].childNodes[0].value == '')
		{
    		error = error + 'Missing Color Code for Row ' + (row+1) + human_identifier +'\r\n';
    		//tbody.rows[row].cells[color_code_col].childNodes[0].focus();
    		setRowFocus(row, style_col);
    		current_row =row;
    	}
    	if (tbody.rows[row].cells[color_description_col].childNodes[0].value == '')
		{
    		error = error + 'Missing Color Description for Row ' + (row+1) + human_identifier +'\r\n';
    		//tbody.rows[row].cells[color_description_col].childNodes[0].focus();
    		setRowFocus(row, style_col);
    		current_row =row;
    	}
    	if (tbody.rows[row].cells[category_col].childNodes[0].value == '')
		{
    		error = error + 'Missing Category for Row ' + (row+1) + human_identifier +'\r\n';
    		//tbody.rows[row].cells[color_description_col].childNodes[0].focus();
    		setRowFocus(row, category_col);
    		current_row =row;
    	}
    	if (tbody.rows[row].cells[cost_col].childNodes[0].value == '')
		{
    		error = error + 'Missing Cost for Row ' + (row+1) + human_identifier +'\r\n';
    		setRowFocus(row, style_col);
    		//tbody.rows[row].cells[cost_col].childNodes[0].focus();
    		current_row =row;
    	}
    	if (tbody.rows[row].cells[cost_col].childNodes[0].value == '0')
		{
    		warning = warning + 'Warning Cost for Row ' + (row+1) + human_identifier +' is $0 \r\n';
    	}
    	if (sizes_for_current_row[row] =='undefined')
    	{
    		error = error + 'Missing Size Chart for Row ' + (row+1) + human_identifier +'\r\n';
    		//setRowFocus(row, style_col);
    		tbody.rows[row].cells[style_col].childNodes[0].focus();
    		current_row =row;
    		highlightSizes(row);
    	}
    	//go through the sizes and make sure a 'blank size' is not chosen
    	
    	var max_c = parseInt(start_columns) + parseInt(num_sizes);
    	var counter = 0;
		for(c=start_columns;c<max_c;c++)
		{
    		if (tbody.rows[row].cells[c].childNodes[0].value == '')
			{
				//do nothing
			} 
			else
			{
				//a value is in the qty box
				//console.log(brand_size_chart);
				//console.log(row);
				//console.log(counter);
				//console.log(sizes_for_current_row);
				//console.log(sizes_for_current_row[row]);
				if (sizes_for_current_row[row] !='undefined')
				{
					size = brand_size_chart['sizes'][sizes_for_current_row[row]][counter];
					if(size == '')
					{
						error = error + 'Quantity Ordered is Evaluated To Be A Blank Size for Row - Check the quantity against the size chart' + (row+1) + human_identifier +'\r\n';			
						tbody.rows[row].cells[style_col].childNodes[0].focus();
						current_row =row;
					}
				}
			}
			counter++;
    	}


    	
    }
    
    //now check that each color code, if it exists, has the correct color description
    
    if (warning != '')
    {
    	alert(warning);
    }

    if (error == '')
    {
    	return true;
    }
    else
    {
    	alert(error);
    	return false;
    }
    
}

function checkForSystemStyles()
{
	//alert(rows_with_system_styles);
	var tbody = document.getElementById('poc_tbody');
	//want to go through each style and see if it is in the system
	for(var i = 0; i<rows_with_system_styles.length; i++)
	{
		row = rows_with_system_styles[i];
		//set these rows to read only
			tbody.rows[row].cells[title_col].childNodes[0].readOnly = 'true';
			tbody.rows[row].cells[category_col].childNodes[0].disabled = 'true';
			//tbody.rows[row].cells[description_col].childNodes[0].readOnly = 'true';
			tbody.rows[row].cells[cost_col].childNodes[0].readOnly = 'true';
			tbody.rows[row].cells[retail_col].childNodes[0].readOnly = 'true';
			
			tbody.rows[row].cells[title_col].childNodes[0].style.color="grey";
			//tbody.rows[row].cells[description_col].childNodes[0].style.color="grey";
			tbody.rows[row].cells[cost_col].childNodes[0].style.color="grey";
			tbody.rows[row].cells[retail_col].childNodes[0].style.color="grey";
	}
	
}
function cancelPO()
{
	window.location = "../purchase_orders.php";
}
function submitViaEmail(pos_purchase_order_id)
{
	needToConfirm=false;
	var elem = document.getElementById(formID).elements;
	var post_string = {};
	for(var i = 0; i < elem.length; i++)
	{
		post_string[elem[i].name] = elem[i].value;
	} 
	$.post("update_poc_data_to_server.php", post_string,
   	function(response) {
     window.location = "../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id="+pos_purchase_order_id;
   });
}
function changeRowColumnWithArrow(e, control, tBodyId)
{
	//tBodyId = 'poc_tbody';
	var tBody = document.getElementById(tBodyId);
	var tBodyRowCount = tBody.rows.length;
	//get the number of columns
	var colCount = tBody.parentNode.rows[0].cells.length;
	var tHead = document.getElementById('poc_thead');
	var theadRowCount = tHead.rows.length;
	
	rowCount = tBodyRowCount;

	var col = control.parentNode.cellIndex;
	var tmp_row =  control.parentNode.parentNode.rowIndex;
	var new_row = parseInt(tmp_row) - parseInt(theadRowCount);
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
	  //if (!selectArrowKey) return;
	  //alert("ROW: " + row + " COLUMN: " +col);
	  
	 
	  document.getElementById(tBodyId).rows[final_row].cells[final_col].childNodes[0].focus();
	  //document.getElementById(tBodyId).rows[final_row].cells[final_col].childNodes[0].select();
	  current_row = final_row;
	  setCurrentRow(document.getElementById(tBodyId).rows[final_row].cells[final_col].childNodes[0]);
}