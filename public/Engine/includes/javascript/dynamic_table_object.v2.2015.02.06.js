
//version 2 has a few more things....
/*

	use $html .= createDynamicTableReuseV3($table_id, $table_def, $contents, ' class="dynamic_contents_table" style="width:100%" ', $buttons);
	
	this version changes the table object to contain data and cell properties. 
	we might need to set some cells to read only, then if the table row moves the cell properties will stay with the object.
	
	there is no "form" needed. The table data object gets posted through ajax... if we need to post it through a form then we can do that with php....
	
	this allows us to create a table dynmaically through javascript. you would just need to add table, tbody, etc through javascript.
	
	While this is neat it seems easier to still do this through php.
	
	the table object is arranged differently...
	table = [];
	table[row] = {};
	table[row]['row_properties'] = {}  => row colors, hover colors, etc
	table[row]['row_properties']['color'] = red;
	table[row]['row_properties']['onhover'] = function ();
	etc...
	
	or
	table = {};
	table['row'] = {}
	
	table[row][
	
	old way:
	table['db_fieled'][row]['data']  db_field is actually column data....
	table['db_fieled'][row]['data']
	
*/


/*###########################################################
						Tabledef

	'th' => 'Barcode',
	'db_field' => 'pos_product_sub_id',
	'default_value' => 1,
	'type' => 'input', 
	'element' => 'input',
	'element_type' => 'text',
	
	
*/

//the table has a series of options passed in by an array. Document that here.
//the rows have options passed in by an array. Document that here
//the tbody_def is the meat, document that here... that has thead and tfoot in it.


//next update is the need for complete control of columns
/*
dynamic table column object
currently access via tbody_def[c][value]

what we want is invoice_table.column.column_name.property = blah....

for (var column in this.cd)
{
	this.cd[column][property] 	
}

basically we want complete control....
no php table creation.
all javascript.

ex: promotion remove a column, update the table
	//table.column.visible = false
	//table.update();

maybe on the div copy the user data.

*/


function dynamic_table_object_v2(table_name, table_body_column_definition, json_table_contents)
{
	
	//always add to the object in javascript like table_name.returned_data = returned_data
	
	//to limit data passage tbody_id = table_name + '_tbody'
	
	thead_name_id = table_name + '_thead';
	tbody_name_id = table_name + '_tbody';
	tfoot_name_id = table_name + '_tfoot';
	this.thead_id = thead_name_id;
	this.tbody_id = tbody_name_id;
	this.thead_id = tfoot_name_id;
	this.rowCount = 0;
	//global variables need to be the current_row... not
	this.tdo = [];
	this.table_name = table_name;
	//assign the html table straight to the object
	this.tbody = document.getElementById(this.tbody_id);

	this.current_row = 0;
	this.current_column = 0;
	this.tbody_def = table_body_column_definition;
	this.json_table_contents = json_table_contents;



	//#####################################################################//
	//
	//			calls made from php, probably when starting the table
	//
	//			
	//			
	//
	//
	//#####################################################################//
	this.init = function()
	{

		//this seems to need to happen on the load function....
		if (this.json_table_contents.length > 0)
		{
//			console.log(json_table_contents.length);
			for(var row=0;row<json_table_contents.length;row++)
			{
				//console.log(row);
				//console.log(json_table_contents[row]);
				this.addItemDataToTableObject(this.json_table_contents[row]);
				this.addItemDataToHTMLTable(this.json_table_contents[row]);
				
			}
		}
		else
		{
			//add a row
			//addRow();
		}
			
		//finally call an init funtion for extra stuff ????
		//ifFnExistsCallIt(init_javascript_function_name);	
	}
	this.initializeTableObject = function()
	{
		var new_object = [];
		/*for(var col=0;col<this.tbody_def.length;col++)
		{
			new_object[this.tbody_def[col]['db_field']] = {};
		}*/
		return new_object;
	}
	this.initilizeHTMLTable = function()
	{
			var tbody = document.getElementById(this.tbody_id);
			var rowCount = tbody.rows.length;
			if (rowCount > 0)
			{
					this.tdo = this.initializeTableObject();
					for (var i=0;i<rowCount;i++)
					{
						tbody.deleteRow(0);
						this.rowCount = this.rowCount-1;
					}
					this.writeObjectToHTMLTable();			
			} 
			else
			{
			
			}

		// maybe I need this? 
		ifCalculateTotalsExists();
	}
	//#####################################################################//
	//
	//			calls made from javascript, probably a result of loading data
	//			from ajax calls, etc
	//			
	//			
	//
	//
	//#####################################################################//
	this.addItemToTable = function(item_data)
	{
		var nrow = this.addItemDataToTableObject(item_data);
		
		//instead of this we change to write htmltable....? or not?
		this.addItemDataToHTMLTable(item_data);
		//return the row
		return nrow;
			
	}
	this.setFocus = function(row, column_name_or_number)
	{
		if (column_name_or_number === parseInt(column_name_or_number, 10))
		{
			this.tbody.rows[row].cells[column_name_or_number].childNodes[0].focus();

		}
		else
		{
			this.tbody.rows[row].cells[this.getHTMLColumnNumberFromTableDefColumnName(column_name_or_number)].childNodes[0].focus();

		}
	}	
	this.calculateDynamicTableColumnTotal = function(table_def_column_name)
	{
		//assuming everything is written to the array?
		var sum = 0.0;
		var col = this.getTableDataColumnNumberFromTableDefColumnName(table_def_column_name);
		for(var row=0;row<this.tdo.length;row++)
		{
			sum = sum + myParseFloat(this.tdo[row][table_def_column_name]);
		}
		return sum;
		
	}
	//#####################################################################//
	//
	//			Typical calls a user makes
	//			things like adding data to the html table
	//			
	//			
	//
	//
	//#####################################################################//


	this.addRow = function()
	{
		
		//here we might want to set the row to some initial data?
		this.copyHTMLTableDataToObject();
		row = this.addRowToTableObject();
		//console.log('row ' + row);
		
		//any default values?
		for(var col=0;col<this.tbody_def.length;col++)
		{
			if (typeof this.tbody_def[col]['default_value'] !== 'undefined')
			{
				
				//console.log(this.tdo);
				this.tdo[row][this.tbody_def[col]['db_field']]['data'] = this.tbody_def[col]['default_value'];
			}
		}
		
		row_number = this.addRowToHTMLTable();
		this.writeObjectToHTMLTable();
		this.updateTable();
		//this.updateTableDataForPost();
		var tbody = document.getElementById(this.tbody_id);
		//set focus to the first element in the new row after the row number
		for(var col=0;col<this.tbody_def.length;col++)
		{
			if (this.tbody_def[col]['type'] == 'hidden')
			{
			}
			else if (this.tbody_def[col]['db_field'] == 'none' || this.tbody_def[col]['db_field'] == 'row_number')
			{
			}
			else
			{
				tbody.rows[row_number].cells[this.getHTMLColumnNumberFromTableDefColumnName(this.tbody_def[col]['db_field'])].childNodes[0].focus();
				break;
			}
			
		}
		//finally there usually is a calculateTotals() function needed to update totals
		ifCalculateTotalsExists();
	}
	this.copyRow = function(place)
	{
		//place == bottom top or below
		if ( place == 'below')
		{
			//default place == bottom	
		}
		else if (place == 'top')
		{
		}
		else if (place == 'bottom')
		{
		}
		else
		{
			//undefined set to one of these...
			place = 'bottom';
			place = 'top';
			place = 'below';
		}
		this.copyHTMLTableDataToObject();
		checked_rows = this.findCheckedRows();
		if (checked_rows.length>0)
		{
			this.copyObjectRows( checked_rows, place);
			this.swipeNwriteHTMLTable();	
		}
		//finally there usually is a calculateTotals() function needed to update totals
		ifCalculateTotalsExists();
	}
	this.moveRowUp = function()
	{
		//first find the row(s) that are checked
		checked_rows = this.findCheckedRows();
		//next check that the rows can be moved - they are in bounds
		bln_move_ok = true;
		for(var i=0;i<checked_rows.length;i++)
		{
			if((checked_rows[i] -1) <0) bln_move_ok = false
		}
		if (bln_move_ok)
		{
			//next copy the entire table to an array - need the size rows as well
			this.copyHTMLTableDataToObject();
			//rearrange the rows into a new array
			for(var i=0;i<checked_rows.length;i++)
			{
				this.moveTableObjectRow(checked_rows[i], checked_rows[i]-1);
				this.setChecks(checked_rows[i], checked_rows[i]-1);
			}	
			//put the array back into the table
			this.writeObjectToHTMLTable();
	
		}
		//finally there usually is a calculateTotals() function needed to update totals
		ifCalculateTotalsExists();
	}
	this.moveRowDown = function()
	{
		//first find the row(s) that are checked
		checked_rows = this.findCheckedRows();
		//next check that the rows can be moved - they are in bounds
		bln_move_ok = true;
		for(var i=0;i<checked_rows.length;i++)
		{
			if((checked_rows[i] +1) > this.tdo.length -1) bln_move_ok = false
		}
		if (bln_move_ok)
		{
			//next copy the entire table to an array - need the size rows as well
			this.copyHTMLTableDataToObject();
			//rearrange the rows into a new array
			for(var i=checked_rows.length-1;i>-1;i--)
			{
				var newRow = parseInt(checked_rows[i])+parseInt(1);
				this.moveTableObjectRow(checked_rows[i], newRow);
				this.setChecks(checked_rows[i], newRow);
			}	
			//put the array back into the table
			this.writeObjectToHTMLTable();
	
		}
		//finally there usually is a calculateTotals() function needed to update totals
		ifCalculateTotalsExists();
	}	
	this.deleteRow = function() 
	{
		var answer = confirm("Confirm Delete Row(s)")
		if (answer)
		{	// delete selected rows
			if (this.tdo.length > 0)
			{
				//first find the row(s) that are checked
				checked_rows = this.findCheckedRows();
				if (checked_rows.length>0)
				{
					this.copyHTMLTableDataToObject();
					this.deleteTableObjectRows(checked_rows);
					this.swipeNwriteHTMLTable();
				}
			} 
			else
			{
				alert("Can't delete when there is no rows");
			}
		}
		//finally there usually is a calculateTotals() function needed to update totals
		ifCalculateTotalsExists();
	
	}
	this.deleteAllRows = function() 
	{
		var answer = confirm("Confirm Delete All Rows")
		if (answer)
		{	
			this.tdo = this.initializeTableObject();
			this.swipeNwriteHTMLTable();		
		}
		else
		{
			//do not delete rows
		}
		//finally there usually is a calculateTotals() function needed to update totals
		ifCalculateTotalsExists();
	
	}	
	
	//#####################################################################//
	//
	//			Table Data Object operations - row deleting moving etc
	//
	//
	//
	//
	//
	//#####################################################################//
	this.addRowToTableObject = function()
	{
		var	row = this.rowCount;
		this.tdo[row] = {};	
		var data;
		data = '';
		for(var col=0;col<this.tbody_def.length;col++)
		{
			//set it to nothing....
			//this could be an array???
			
			if(typeof this.tbody_def[col]['price_array_index'] !== 'undefined')
			{
				this.tdo[row][this.tbody_def[col]['db_field']] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['array_values'] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['display_value'] = '';
				
				//this.table_data_array[row][this.tbody_def[col]['db_field']] = [];	
				var quantity = 0;
				for(var qty=0;qty<quantity;qty++)
				{
					this.tdo[row][this.tbody_def[col]['db_field']]['array_values'][qty] = data;
				}
			}
			//what about the special select values?
			else if(typeof this.tbody_def[col]['individual_select_options'] !== 'undefined')
			{	
				this.tdo[row][this.tbody_def[col]['db_field']] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['select_values'] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['select_names'] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['value'] = data;
		
			}
			else
			{
				this.tdo[row][this.tbody_def[col]['db_field']] = {};	
				this.tdo[row][this.tbody_def[col]['db_field']]['data'] = data;
				this.tdo[row][this.tbody_def[col]['db_field']]['cell'] = {};
	
			}
			
			
		}
		
		
		this.rowCount = this.rowCount+1;
		return row;
	
		
	}
	this.addItemDataToTableObject = function(item_data)
	{
		var	row = this.tdo.length;
		this.tdo[row] = {};
		var data;
		for(var col=0;col<this.tbody_def.length;col++)
		{
			
			if(typeof item_data[this.tbody_def[col]['db_field']] !== 'undefined')
			{
				data = item_data[this.tbody_def[col]['db_field']];
			}
			else
			{
				if(typeof this.tbody_def[col]['default_value'] !== 'undefined')
				{
					data = this.tbody_def[col]['default_value'];
				}
				else
				{
					data = '';
				}
				
			}
			//set it to nothing....
			if(typeof this.tbody_def[col]['price_array_index'] !== 'undefined')
			{
				this.tdo[row][this.tbody_def[col]['db_field']] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['array_values'] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['display_value'] = '';
				
				//this.table_data_array[row][this.tbody_def[col]['db_field']] = [];	
				var quantity = item_data[this.tbody_def[col]['price_array_index']];
				for(var qty=0;qty<quantity;qty++)
				{
					this.tdo[row][this.tbody_def[col]['db_field']]['array_values'][qty] = data;
					//this.table_data_array[row][this.tbody_def[column]['db_field']][qty] = data;	
				}
			}
			//what about the special select values?
			else if(typeof this.tbody_def[col]['individual_select_options'] !== 'undefined')
			{
				this.tdo[row][this.tbody_def[col]['db_field']] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['select_values'] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['select_names'] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['value'] = data;
				
				var values = item_data[this.tbody_def[col]['individual_select_options']]['values'];
				for(var opt=0;opt<values.length;opt++)
				{
					this.tdo[row][this.tbody_def[col]['db_field']]['select_values'][opt] = item_data[this.tbody_def[col]['individual_select_options']]['values'][opt];
					this.tdo[row][this.tbody_def[col]['db_field']]['select_names'][opt] = item_data[this.tbody_def[col]['individual_select_options']]['names'][opt];
					//this.table_data_array[row][this.tbody_def[column]['db_field']][qty] = data;	
				}
			}
			else
			{
				
				this.tdo[row][this.tbody_def[col]['db_field']] = {};
				this.tdo[row][this.tbody_def[col]['db_field']]['data'] = data;	
				this.tdo[row][this.tbody_def[col]['db_field']]['cell'] = {};
			}
			
			
		}
		
		//this.updateTableDataForPost();
		this.updateTableObjectLineNumbers();
		this.rowCount = this.rowCount+1;
		return row;
	}
	this.updateTableObjectLineNumbers = function()
	{
		//seems pointless but needed for post data...
		//db_field has to be 'row_number' for this to work
		//we need to loop through the tbody cells and set the value of the column name bla bla
		var col = this.getTableDataColumnNumberFromTableDefColumnName('row_number');
		if (col != -1)
		{
			for(var row=0; row<this.tdo.length; row++)
			{
				this.tdo[row]['row_number']['data'] = row+1;	
			}
		}
	}
	this.updateItemDataInTableObject = function(item_data, row)
	{
		
		//!Dont forget to 	copyHTMLTableDataToObject(); before updating....
		
		//this.table_data_array[row] = {};
		//this.table_data_array[row] = [];
		var data;
		for(var col=0;col<this.tbody_def.length;col++)
		{
			if(typeof item_data[this.tbody_def[col]['db_field']] !== 'undefined')
			{
				data = item_data[this.tbody_def[col]['db_field']];
				
				if(typeof this.tbody_def[col]['price_array_index'] !== 'undefined')
				{	
					this.tdo[row][this.tbody_def[col]['db_field']] = {};
					this.tdo[row][this.tbody_def[col]['db_field']]['array_values'] = {};
					this.tdo[row][this.tbody_def[col]['db_field']]['display_value'] = data;
					
					var quantity = myParseInt(item_data[this.tbody_def[col]['price_array_index']]);
					for(var qty=0;qty<quantity;qty++)
					{
						this.tdo[this.tbody_def[col]['db_field'][row]]['array_values'][qty] = data;
					}
				}
				else if(typeof this.tbody_def[col]['individual_select_options'] !== 'undefined')
				{				
					this.tdo[row][this.tbody_def[col]['db_field']] = {};
					this.tdo[row][this.tbody_def[col]['db_field']]['select_values'] = {};
					this.tdo[row][this.tbody_def[col]['db_field']]['select_names'] = {};
					this.tdo[row][this.tbody_def[col]['db_field']]['value'] = data;
					
					var values = item_data[this.tbody_def[col]['individual_select_options']]['values'];
					//var values = item_data[['values'];
					for(var opt=0;opt<values.length;opt++)
					{						
						this.tdo[row][this.tbody_def[col]['db_field']]['select_values'][opt] = item_data[this.tbody_def[col]['individual_select_options']]['values'][opt];
						this.tdo[row][this.tbody_def[col]['db_field']]['select_names'][opt] = item_data[this.tbody_def[col]['individual_select_options']]['names'][opt];
						//this.table_data_array[row][this.tbody_def[column]['db_field']][qty] = data;	
					}
				}
				else
				{
					this.tdo[row][this.tbody_def[col]['db_field']]['data'] = data;
					
				}
			}
			else
			{
				//do nothing
				
			}
			//set it to nothing....
			
		}
		//this.updateTableDataForPost();
		this.writeObjectToHTMLTable();
	}
	this.getTableArrayColumnNumberFromHTMLColumnNumber = function(html_column)
	{
		column = -1;
		var table_data_array_column = 0;
		var html_column_counter = 0;
		for (i=0; i<this.tbody_def.length;i++)
		{
			if(this.tbody_def[i]['type'] != 'hidden')
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
	this.getTableDataColumnNumberFromTableDefColumnName = function(name)
	{
		column = -1;
		for (i=0; i<this.tbody_def.length;i++)
		{
			if (typeof this.tbody_def[i]['db_field'] !== 'undefined' && this.tbody_def[i]['db_field'] == name)
			{
				column = i;
			}
			
		}
		return column;
	}
	this.getHTMLColumnNumberFromTableDefColumnName = function(name)
	{
		column = -1;
		var col_counter = 0;
		for (i=0; i<this.tbody_def.length;i++)
		{
			if(this.tbody_def[i]['type'] != 'hidden')
			{
				if (this.tbody_def[i]['db_field'] == name)
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
	
	//this is a biggie..... get the user input from the table... use it often.
	this.copyHTMLTableDataToObject = function()
	{
		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
		//var maxColCount = tbody.rows[0].cells.length;
	
		var column_counter = 0;
		for(var row=0; row<rowCount; row++)
		{
			column_counter=0;
			for(col= 0;col<this.tbody_def.length;col++)
			{			
				if(this.tbody_def[col]['type'] != 'hidden')
				{	
					if (typeof this.tbody_def[col]['price_array_index'] === 'undefined')
					{
						//alert(tbody.rows[row].cells[column_counter].childNodes[0].value);
						if (this.tbody_def[col]['type'] == 'checkbox')
						{
							this.tdo[row][this.tbody_def[col]['db_field']]['data'] = tbody.rows[row].cells[column_counter].childNodes[0].checked;
	
						}
						else if(this.tbody_def[col]['type'] == 'innerHTML')
						{
							//user cant change this....
							//this.tdo[row][this.tbody_def[col]['db_field']]['data'] = this.tdo[row][this.tbody_def[col]['db_field']]['data'];
						}
						else if (typeof this.tbody_def[col]['individual_select_options'] !== 'undefined')
						{
							this.tdo[row][this.tbody_def[col]['db_field']]['value'] = tbody.rows[row].cells[column_counter].childNodes[0].value;
						}
						else
						{
							this.tdo[row][this.tbody_def[col]['db_field']]['data'] = tbody.rows[row].cells[column_counter].childNodes[0].value;
								//alert(this.table_data_array[row][col]);
						}
					}
					
					column_counter++;
				}
			}
			//now update the price_index_array
			for(col= 0;col<this.tbody_def.length;col++)
			{
				if (typeof this.tbody_def[col]['price_array_index'] !== 'undefined')
				{
					//when quantities are changed the array size needs to change as well...
					
					var quantity = myParseInt(this.tdo[this.tbody_def[col]['price_array_index']][row]);
					var new_quantity_array = {};
					for(var qty=0;qty<quantity;qty++)
					{
						if(this.tdo[row][this.tbody_def[col]['db_field']]['array_values'][qty] ==='undefined')
						{
							new_quantity_array[qty] = '';
						}
						else
						{
							new_quantity_array[qty] = 	this.tdo[row][this.tbody_def[col]['db_field']]['array_values'][qty];
						}
					
					}
					this.tdo[row][this.tbody_def[col]['db_field']]	['array_values'] = 	new_quantity_array;
					
				}	
			}
		}
		//this.updateTableDataForPost();
	}


	this.copyObjectRows = function( row_array, place)
	{		
		var newRowCounter = 0;
		var new_tdo = [];
		
		console.log("place: " + place);
		//depending on place we put the copied rows first, last or below checked rows...
		if (place == 'top')
		{
			//take each row in row array and put it in the new array...
			//go through the rest of the rows and add them
			for(var row = 0; row < row_array.length; row++)
			{
				new_tdo[newRowCounter] = this.tdo[row_array[row]];
				newRowCounter++;
			}
			for(var row = 0; row < this.tdo.length; row++)
			{
				new_tdo[newRowCounter] = this.tdo[row];
				newRowCounter++;
			}
		}
		else if (place == 'bottom')
		{
			//go through the rows and add them
			//take each row in row array and put it in the new array...
			
			for(var row = 0; row < this.tdo.length; row++)
			{
				new_tdo[newRowCounter] = this.tdo[row];
				newRowCounter++;
			}
			for(var row = 0; row < row_array.length; row++)
			{
				new_tdo[newRowCounter] = this.tdo[row_array[row]];
				newRowCounter++;
			}
			
		}
		else
		{
			for(var row = 0; row < this.tdo.length; row++)
			{
				// we are keeping this row.
				new_tdo[newRowCounter] = this.tdo[row];
				newRowCounter++;
				// if the row is in the checked row array it is copied
				if(isValueInArray(row, row_array))
				{
					//copy
					new_tdo[newRowCounter] = this.tdo[row];
					newRowCounter++;
				}

					
			}
		}
		console.log('New TDO');
		console.log(new_tdo);
		this.tdo = new_tdo;
		console.log('New TDO');
		console.log(this.tdo);
		this.rowCount = new_tdo.length;
		
		

		
		
		
	}
	this.moveTableObjectRow = function(RowToMove, RowToMoveTo)
	{	//ex row 2 row 3
		var new_tdo = [];
		for  (i=0;i<this.tdo.length;i++)
		{
			if (RowToMove == i)
			{
				new_tdo[i] = this.tdo[RowToMoveTo];
			}
			else if (RowToMoveTo == i)
			{
				new_tdo[i] = this.tdo[RowToMove];
			}
			else
			{
				new_tdo[i] = this.tdo[i];
			}
		}
		this.tdo = new_tdo;
	}
	this.deleteTableObjectRows = function(row_array)
	{
		var newRowCounter = 0;
		var new_tdo = [];
		
		for(var row = 0; row < this.tdo.length; row++)
		{
			// if the row is in the checked row array it is gonzo
			if(isValueInArray(row, row_array))
			{
				//delete
			}
			else
			{
				// we are keeping this row.
				new_tdo[newRowCounter] = this.tdo[row];
				newRowCounter++;
			}
		
		}
		this.tdo = new_tdo;
		this.rowCount = new_tdo.length;
		
		

		
	}	
	
	//#####################################################################//
	//
	//			Writing TDO to HTML
	//
	//
	//
	//
	//
	//#####################################################################//	
	this.swipeNwriteHTMLTable = function()
	{
		//lets see how fast this is... this is saying every time we update the tdo we kill the html table and re-write it...
		//this is really fast, so for all those copy, move just do it in the tdo then wipenwrite.
		
		//delete all rows from the tbody
		//add all the rows to tbody
		//then add the data
		//format all the rows
		//update the footer
		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
		if (rowCount > 0)
		{
			for (var i=0;i<rowCount;i++)
			{
				tbody.deleteRow(0);
			}
		}
		//now add the rows...
		for(var r=0;r<this.tdo.length;r++)
		{
			this.addRowToHTMLTable();
		}
		//now write the data
		this.writeObjectToHTMLTable();
	
		
	}
	this.copy = function()
	{	
		this.copyHTMLTableDataToObject();
	}
	this.write = function()
	{	
		this.writeObjectToHTMLTable();
	}
	this.writeObjectToHTMLTable = function(control)
	{

		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
		var column_counter=0;
		if (typeof control !== 'undefined')
		{
			control_column  =  this.getTableArrayColumnNumberFromHTMLColumnNumber(getCurrentColumn(control));
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
			for(col= 0;col<this.tbody_def.length;col++)
			{
				if(this.tbody_def[col]['type'] != 'hidden')
				{
					if (this.tbody_def[col]['type'] == 'checkbox')
					{
						if(row == control_row && col == control_column)
						{
						}
						else
						{
							//if(this.table_data_array[row][col] == 1 || this.table_data_array[row][col] == '1')
							if(this.tdo[row][this.tbody_def[col]['db_field']]['data'] == 1 || this.tdo[row][this.tbody_def[col]['db_field']]['data'] == '1')
							{
								tbody.rows[row].cells[column_counter].childNodes[0].checked = true;
							}
							else
							{
								tbody.rows[row].cells[column_counter].childNodes[0].checked = false;
							}
						}
					}
					else
					{
						if(row == control_row && col == control_column)
						{
						}
						else
						{
							
							if(typeof this.tbody_def[col]['price_array_index'] !== 'undefined')
							{
								//value =  this.table_data_array[row][col]['display_value'];
								value =  this.tdo[row][this.tbody_def[col]['db_field']]['display_value'];

							}
							else if(typeof this.tbody_def[col]['individual_select_options'] !== 'undefined')
							{
								//value =  this.table_data_array[row][col]['value'];
								value =  this.tdo[row][this.tbody_def[col]['db_field']]['value'];

								//now we need to put the options in....
								//clear the options...
								element = tbody.rows[row].cells[column_counter].childNodes[0];
								//alert(element.value);	
								if( element.options != 'null')
								{
									element.options.length = 0;
								}
								//reload
								//alert(this.table_data_array[row][col]['select_values'].length);
								//for(opt=0;opt<this.table_data_array[row][col]['select_values'].length;opt++)
								for(var opt in this.tdo[row][this.tbody_def[col]['db_field']]['select_values'])
								{
									//load up the options
									option = document.createElement('option');
									option.value = this.tdo[row][this.tbody_def[col]['db_field']]['select_values'][opt];
									option.appendChild(document.createTextNode(this.tdo[row][this.tbody_def[col]['db_field']]['select_names'][opt]));
									element.appendChild(option);
								}
							}
							else
							{
								value =  this.tdo[row][this.tbody_def[col]['db_field']]['data'];
								
							}
								
							if(typeof this.tbody_def[col]['round'] !== 'undefined')
							{
	
								
								if(this.tbody_def[col]['type'] == 'innerHTML')
								{
										tbody.rows[row].cells[column_counter].innerHTML = round2(value,this.tbody_def[col]['round']);;
								}
								else
								{
									tbody.rows[row].cells[column_counter].childNodes[0].value = round2(value,this.tbody_def[col]['round']);
								}
							}
							else
							{
								//here we write the value....if we want text where does it go?
								if(this.tbody_def[col]['type'] == 'innerHTML')
								{
										tbody.rows[row].cells[column_counter].innerHTML = value;
								}
								else
								{
									tbody.rows[row].cells[column_counter].childNodes[0].value = value;
								}
							}
						}
					}
					column_counter++;
				}
				
			}
		}
		//return tabledata;
		this.updateTable();
		//this.updateTableDataForPost();
	
	
	}	
	this.updateTable = function()
	{
		this.updateTableObjectLineNumbers();
		this.updateHTMLTableLineNumbers();
		this.updateSelectOptions();
		this.updateFooter();	
	}
	this.updateTableData = function(control)
	{
		this.copyHTMLTableDataToObject();
		this.updateFooter();
		//this.updateTableDataForPost();
		//writeObjectToHTMLTable(control);
	}
	this.updateHTMLTableLineNumbers = function()
	{
			//db_field has to be 'row_number' for this to work
		//we need to loop through the tbody cells and set the value of the column name bla bla
		var col = this.getHTMLColumnNumberFromTableDefColumnName('row_number');
		if (col != -1)
		{
			var tbody = document.getElementById(this.tbody_id);
			var rowCount = tbody.rows.length;
			for(var row=0; row<rowCount; row++)
			{
				tbody.rows[row].cells[col].childNodes[0].value = row+1;
			}
		}
	}
	//#####################################################################//
	//
	//			HTML Table writing, copying, navigation
	//
	//
	//
	//
	//
	//#####################################################################//
	this.addNestedSelectOptions = function(element, category_array, level)
	{
		for (var key in category_array)
		{
			option = document.createElement('option');
			option.value = key;
			name = category_array[key]['name'];
			
			
			for(i=0;i<level;i++)
			{
				name = "\u00A0" + name;
				name =  "\u00A0" + name;
			}
			option.appendChild(document.createTextNode(name));
			element.appendChild(option);
			if(!$.isEmptyObject(category_array[key]['children']))
			{
				this.addNestedSelectOptions(element, category_array[key]['children'], level+1);
			}
		}
	}
	this.addRowToHTMLTable = function()
	{
		var tBody = document.getElementById(this.tbody_id);
		var rowCount = tBody.rows.length;
		var row = tBody.insertRow(rowCount);
		row.id = rowCount;
		var col_counter = 0;
		var element;
		for (c=0; c<this.tbody_def.length;c++)
		{
			if (this.tbody_def[c]['type'] == 'hidden')
			{
			}
			else
			{
				cell = row.insertCell(col_counter);
				col_counter++;
				if (typeof this.tbody_def[c]['td_tags'] !== 'undefined')
				{
					for (var index in this.tbody_def[c]['td_tags'])
						{
							eval('cell.' + index + '= ' + this.tbody_def[c]['td_tags'][index] + ';');
							
						}
				}
				if (this.tbody_def[c]['type'] == 'input' || this.tbody_def[c]['type'] == 'checkbox' || this.tbody_def[c]['type'] == 'row_checkbox')
				{
					
					
					element = document.createElement(this.tbody_def[c]['element']);
					element.type = this.tbody_def[c]['element_type'];
					element.name = this.tbody_def[c]['db_field'] + '[]';
					if (typeof this.tbody_def[c]['properties'] !== 'undefined')
					{
						for (var index in this.tbody_def[c]['properties'])
						{
							eval('element.' + index + '= ' + this.tbody_def[c]['properties'][index] + ';');
							
						}
					}
					cell.appendChild(element);
				}
				else if (this.tbody_def[c]['type'] == 'select')
				{
					element = document.createElement('select');
					element.name = this.tbody_def[c]['db_field'] + '[]';
		
					//we can have individual select options or global options
					if (typeof this.tbody_def[c]['individual_select_options'] !== 'undefined')
					{
						//there are unique select items in here...
						var option = document.createElement('option');
						option.value = 'NULL';
						option.appendChild(document.createTextNode("Select..."));
						element.appendChild(option);
						//the names and values are in the data object
		
		
						for (var i in this.tdo[this.tbody_def[c]['db_field']][rowCount]['select_values'])
						{
							option = document.createElement('option');
							option.value = this.tdo[this.tbody_def[c]['db_field']][rowCount]['select_values'][i];
				option.appendChild(document.createTextNode(this.tdo[this.tbody_def[c]['db_field']][rowCount]['select_names'][i]));
							element.appendChild(option);
						}
						
					}
					else
					{
						var option = document.createElement('option');
						option.value = 'NULL';
						option.appendChild(document.createTextNode("Select..."));
						element.appendChild(option);
						for (var i in this.tbody_def[c]['select_names'])
						{
							option = document.createElement('option');
							option.value = this.tbody_def[c]['select_values'][i];
							option.appendChild(document.createTextNode(this.tbody_def[c]['select_names'][i]));
							element.appendChild(option);
						}
					}
					if (typeof this.tbody_def[c]['properties'] !== 'undefined')
					{
						for (var index in this.tbody_def[c]['properties'])
						{
							eval('element.' + index + '= ' + this.tbody_def[c]['properties'][index] + ';');
							
						}
					}
					cell.appendChild(element);
				}
				else if (this.tbody_def[c]['type'] == 'tree_select')
				{
					element = document.createElement('select');
					element.name = this.tbody_def[c]['db_field'] + '[]';
					
					
					var option = document.createElement('option');
					option.value = 'NULL';
					option.appendChild(document.createTextNode("Select..."));
					element.appendChild(option);
					var level = 0;
					//console.log(this.tbody_def[c]['select_array']);
					this.addNestedSelectOptions(element, this.tbody_def[c]['select_array'], level);
					



					if (typeof this.tbody_def[c]['properties'] !== 'undefined')
					{
						for (var index in this.tbody_def[c]['properties'])
						{
							eval('element.' + index + '= ' + this.tbody_def[c]['properties'][index] + ';');
							
						}
					}
					cell.appendChild(element);
				}
				else if (this.tbody_def[c]['type'] == 'textContent')
				{
					// how?? cell.innerHTML( noo, we need to access childnode[0] later...
					//var cellText = document.createTextNode("cell in row "+i+", column "+j);
      				element = document.createElement(this.tbody_def[c]['element']);
      				cell.appendChild(element);
				}
				else if (this.tbody_def[c]['type'] == 'innerHTML')
				{
					// how?? cell.innerHTML( noo, we need to access childnode[0] later...
					//var cellText = document.createTextNode("cell in row "+i+", column "+j);
      				//element = document.createElement(this.tbody_def[c]['element']);
      				//cell.appendChild(element);
				}
				else
				{
					alert('have not coded that: options are select, hidden, input, checkbox, row_checkbox');
				}
			}
	
		}
		//updateSelectOptions();
		current_row = rowCount;
		return rowCount;
	}
	this.addItemDataToHTMLTable = function(item_data)
	{
		row_number = this.addRowToHTMLTable();
		this.writeObjectToHTMLTable();
	}
	this.updateFooter = function()
	{
		//we can assume a simple sum?
		// find what column has a 'footer'
		//sum that column
		
		for (col=0; col<this.tbody_def.length;col++)
		{
			if (typeof this.tbody_def[col]['footer'] !== 'undefined')
			{
				var sum = 0.0;
				for(row=0;row<this.rowCount;row++)
				{
					sum = sum + myParseFloat(this.tdo[row][this.tbody_def[col]['db_field']]['data']);
				}
				document.getElementById(this.tbody_def[col]['footer'][0]['db_field']).value = sum;
			}
			if (typeof this.tbody_def[col]['total'] !== 'undefined')
			{
				
				var sum = 0.0;
				for(var row=0;row<this.tdo.length;row++)
				{
					sum = sum + myParseFloat(this.tdo[row][this.tbody_def[col]['db_field']]['data']);
				}
				document.getElementById(this.tbody_def[col]['db_field'] + '_total').value = round2(sum,this.tbody_def[col]['total']);
			}
			
		}
		
	}
	this.setCurrentRow = function(control)
	{
		this.current_row = getCurrentRow(control);
		this.current_column = control.parentNode.cellIndex;
		//alert('row ' + current_row + ' column ' + current_column);
	}
	this.findCheckedRows = function()
	{
		//this only works on the first column
		var tbody = document.getElementById(this.tbody_id);
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
	this.setChecks = function(rowMoving, movingTo)
	{
		var tbody = document.getElementById(this.tbody_id);
		tbody.rows[rowMoving].cells[0].childNodes[0].checked=false;
		tbody.rows[movingTo].cells[0].childNodes[0].checked=true;
		
	}
	
	//#####################################################################//
	//
	//			HTML user input, Cell operations
	//
	//
	//
	//
	//
	//#####################################################################//
	
	this.checkValidInput = function(control)
	{
		
		column = this.getTableArrayColumnNumberFromHTMLColumnNumber(getCurrentColumn(control));
		if (typeof this.tbody_def[column]['valid_input'] !== 'undefined')
		{
			
			validInput = this.tbody_def[column]['valid_input'];
			checkInput2(control,validInput);
		}
	}
	
	
	
	
	this.removeAndReloadAllSelectOptions = function()
	{
		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
		for (col=0; col<this.tbody_def.length;col++)
		{
		 
			if (typeof this.tbody_def[col]['unique_select_options'] !== 'undefined')
			{
				//remove all
				for(row=0;row<rowCount;row++)
				{
					length = document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].length;
					var counter = 1;
					for(opt = 1; opt < length;opt++)
					{
						document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].remove(counter);
						//counter++;
					}
				}
				//add all
				for(row=0;row<rowCount;row++)
				{
					for (var i in this.tbody_def[col]['select_names'])
					{
						element = document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row];
						option = document.createElement('option');
						option.value = this.tbody_def[col]['select_values'][i];
						option.appendChild(document.createTextNode(this.tbody_def[col]['select_names'][i]));
						element.appendChild(option);
						//element.add(option, i+1);
					}
				}
			}
		}
	}
	this.reSelectSelect = function()
	{
		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
		for (col=0; col<this.tbody_def.length;col++)
		{
			if (typeof this.tbody_def[col]['unique_select_options'] !== 'undefined')
			{
				// re-assign all
				for(row=0;row<rowCount;row++)
				{
					if(this.tdo[row][this.tbody_def[col]['db_field']]['data'] == '')
					{
						document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].value = 'NULL';
					}
	
					else
					{
						document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].value = this.tdo[row][this.tbody_def[col]['db_field']]['data'];
	
					}
				}
				
			}
		}
	
	}
	this.reloadSelectOptions = function()
	{
		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
	
		for (col=0; col<this.tbody_def.length;col++)
		{
			if (typeof this.tbody_def[col]['unique_select_options'] !== 'undefined')
			{
				//remove all but the selected value
				for(row=0;row<rowCount;row++)
				{
					length = document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].length;
					var counter = 1;
					for(opt = 1; opt < length;opt++)
					{
						if(document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].options[counter].value == this.table_data_array[row][col])
						{
							counter++;
						}
						else
						{
							document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].remove(counter);
						//counter++;
						}
					}
				}
				for(row=0;row<rowCount;row++)
				{
					for (var i in this.tbody_def[col]['select_values'])
					{
						bln_found = false;
						for(k=0;k<this.rowCount;k++)
						{
							if(k==row)
							{
								//add this value
							}
							else
							{
								if(this.tbody_def[col]['select_values'][i] == this.table_data_array[tbody_def[col]['db_field']][k])
								{
									bln_found = true;
								}
							}
						}
						//add the value if it is not used
						if(!bln_found && this.tbody_def[col]['select_values'][i] != this.tdo[tbody_def[col]['db_field']][row])
						{
							//add
							element = document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row];
							option = document.createElement('option');
							option.value = this.tbody_def[col]['select_values'][i];
							option.appendChild(document.createTextNode(this.tbody_def[col]['select_names'][i]));
							element.appendChild(option);
						//element.add(option, i+1);
							
						}
						
					}
				}
				// re-assign all
				for(row=0;row<rowCount;row++)
				{
					document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].value = this.table_data_array[tbody_def[col]['db_field']][row];
				}
				
			}
		}
		
		
	}
	this.updateSelectOptions = function()
	{
		
		
		
		//this will remove the select value from the control
		//the select options should disclude any items selected
		//first we need to restore all the select options
		this.removeAndReloadAllSelectOptions();
		//next we need to re-select the option based on tbody data
		this.reSelectSelect();
		//finally we need to remove whatever options are selected - this prevents double selection!
		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
	
		// select the column
		// go down each row
			// compare each select list value against each row value - if found in another row then remove it from the list
		for (col=0; col<this.tbody_def.length;col++)
		{
			if (typeof this.tbody_def[col]['unique_select_options'] !== 'undefined')
			{
				//ok this column should only have unique values in the list
				for(row=0;row<rowCount;row++)
				{
					for(k=0;k<this.rowCount;k++)
					{
						for (var i in this.tbody_def[col]['select_values'])
						{
							if (row!=k)
							{
								if(this.tbody_def[col]['select_values'][i] == this.tdo[this.tbody_def[col]['db_field']][k])
								{
									//there is a null value to ignore, that is index 0, so add 1 to all indexes to remove..
									//alert('remove index: ' + (parseInt(i)+1) + ' value ' + this.tbody_def[col]['select_values'][i] + ' from row ' + parseInt(row + 1) + ' value ' + this.table_data_array[k][col]);
									//because each list has destroyed index values check if the values match, if so remove it
									length = document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].length;
									var counter = 1;
									for(opt = 1; opt < length;opt++)
									{
										if (document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].options[counter].value == this.tdo[this.tbody_def[col]['db_field']][k])
										{
											//alert('remove option index: ' + counter + ' which has the value of ' + document.getElementsByName(this.tbody_def[col]['db_field']+ '_r' + row)[0].options[counter].value + ' From row ' + parseInt(row+1));
											document.getElementsByName(this.tbody_def[col]['db_field']+ '[]')[row].remove(counter);
	
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
	
	//#####################################################################//
	//
	//			FINSIHING, VALIDATING, POSTING
	//
	//
	//
	//
	//
	//#####################################################################//
	this.POST_TDO = function(form_id)
	{
				this.copyHTMLTableDataToObject();
				element = document.createElement("input");
				element.type = "hidden";
				element.name = this.table_name + '_tdo';
				element.value = JSON.stringify(this.tdo);;
				document.getElementById(form_id).appendChild(element);
		}
	this.prepareDynamicTableForPost = function()
	{
		this.copyHTMLTableDataToObject();
		this.enableAllRows();
		//validateMYSQLInsertForm();
		return this.validateDynamicTableObject();
	} 
	this.GetPostData = function()
	{
		var postData = {};
		postData['tdo'] = JSON.stringify(this.tdo);
		return postData;
	}	
	this.validateDynamicTableObject = function ()
	{
		//this should be the same function as above, but check each row....
			errors = '';
	
		for (i=0; i<this.tbody_def.length;i++)
		{
			if (typeof this.tbody_def[i]['db_field'] !== 'undefined')
			{
				if (typeof this.tbody_def[i]['validate'] !== 'undefined')
				{
					// go through each row
					var elements = document.getElementsByName(this.tbody_def[i]['db_field']+'[]');
					for(el=0;el<elements.length;el++)
					{
						if (typeof this.tbody_def[i]['validate']['not_blank_or_zero_or_false_or_null'] !== 'undefined')
						{
							if(elements[el].value == '' ||
							round2(elements[el].value,0) == 0 || elements[el].value == 'false' || elements[el].value == 'NULL')
							{
								errors += 'Bad Value For ' +this.tbody_def[i]['caption'] + ' Row ' + (el+1) + newline();
							}
						}
						else if  (typeof this.tbody_def[i]['validate']['acceptable_values'] !== 'undefined')
						{
							acceptable_values = this.tbody_def[i]['validate']['acceptable_values'][0];
							if(acceptable_value == 'number')
							{
								if (isNaN(elements[el].value))
								{
									errors += this.tbody_def[i]['db_field'] +' needs to be a value.' + newline();
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
				//getting rid of form stuff....
				/*//create hidden post value
				str_hidden_name = "submit";
				str_hidden_value = "submit";
				//creating the hidden elements for POST
				element = document.createElement("input");
				element.type = "hidden";
				element.name = str_hidden_name;
				element.value = str_hidden_value;
				document.getElementById(this.formId).appendChild(element);*/
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
	
	
//#####################################################################//
	//
	//			table properties
	//
	//
	//
	//
	//
	//#####################################################################//
	this.setSingleCheck = function(control)
	{
		// this needs to be a table property.... like invoice.table.setSingleCheck = true or false.
		this.setCurrentRow(control);
		var col = getCurrentColumn(control);
		//uncheck every check but this one...
		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
		for(var row=0; row<rowCount; row++)
		{
			if (row != getCurrentRow(control))
			{
				tbody.rows[row].cells[col].childNodes[0].checked = false;
			}
		}
		this.copyHTMLTableDataToObject();
	}
	
	
	//#####################################################################//
	//
	//			Confuxing Stuff Im probably going to remove
	//
	//
	//
	//
	//
	//#####################################################################//

	

	
// none of this used to work with the old object, so it all needs to be recoded and removed....


	this.enableAllRows = function()
	{
		//go through each column and disable it
		
		var rowCount = this.getTbodyRowCount();
		for (row = 0;row<rowCount;row++)
		{
			var col_counter = 0;
			for (i=0; i<this.tbody_def.length;i++)
			{
				if(this.tbody_def[i]['type'] != 'hidden')
				{
					//select the type
					this.enableHTMLCell(this.tbody_def[i]['type'], row, col_counter);
					col_counter++;
				}
			}
		}
	
		
	}
	this.disableHTMLRow = function(row)
	{
		//go through each column and disable it
		var col_counter = 0;
		for (i=0; i<this.tbody_def.length;i++)
		{
			if(this.tbody_def[i]['type'] != 'hidden')
			{
				//select the type
				this.disableHTMLCell(this.tbody_def[i]['type'], row, col_counter);
				col_counter++;
			}
		}
	}
	this.disableCell = function(row, name)
	{
		col = this.getHTMLColumnNumberFromTableDefColumnName(name);
				//console.log('row ' + row + ' col ' + col + ' name ' + name);		

		if(col>=0)
		{
		this.disableHTMLCell(this.tbody_def[this.getTableDataColumnNumberFromTableDefColumnName(name)]['type'],row,col);
		}
		
	}
	this.disableHTMLCell = function(type, row, col_counter)
	{
		var tbody = document.getElementById(this.tbody_id);
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
	this.enableHTMLCell = function(type, row, col_counter)
	{
		var tbody = document.getElementById(this.tbody_id);
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
	this.getTbodyRowCount = function()
	{
		//this might not work?
		var tbody = document.getElementById(this.tbody_id);
		return tbody.rows.length;
	} 	
	
	//build the object?
	//this is really needed?
	/*
	this.tdo = this.initializeTableObject();
	for(var col=0;col<this.tbody_def.length;col++)
		{
			this.tdo[this.tbody_def[col]['db_field']] = {};
		}
	*/

	
}




