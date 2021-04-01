/*

	Table objects.... or single object dealing with multiple tables...
	versioning this file allows me to upgrade the table without breaking old ones, I just copy this file
	
	
	
	Most common tables: search table, search results table, dynamic table, static table, mysql table w/ view edit new. Single select table. 
	two different table types: one with an array of rows, and one with only one row.
	
	Both tables need table cell
	
	table types
	array of rows - search results, static, 
	header - could be a column or a row - but most often it is on the top row
	totals row
	columns could be hidden
	dynamically add rows
	view/edit
	new?
	data - stored in an object - post the object
	moving rows operates on the object
	edit popup would be nice - for example search for a mfg pop up the full edit submit update data
		two different table defs.
		button - on click edit_table.dialog("show") - postSubmit reload the data flash the row
	
	array table needs
	Search table or filter
	Sorting
	

	
	
	Single row 
	header - typically down a column
	data is stored in the html element
	not dynamic
	post - collect the html data
	enable/disable
	edit popup


	different table defs = different objects (can be the same object call though)

*/
function dynamic_table_object_v3(table_id, column_definition, json_table_contents)
{
	
	//version 3 has a few more things....
	/*
 		
 		

 		

		setting standard row events for all rows and new rows
		$html .= 'customer_table.setRDO("onclick", "function(){alert(this.rowIndex);}");';
		
		######################## Table Types ########################################
		
		dynamic writable
		static writable (same as dynamic no add row buttons)
		static read only - disable all the cells above....
		
		
		search results - is this the same? as above? 
		table.postSearchResultsFunction = function(){alert(run this code after the search results load)}
		
		
		######################## Setting Table row clicks... ########################################
		
		create the function
 		var rowclick = function(){customerSelect(this.rowIndex);};
		var rowclick1 = function(){alert(this.rowIndex);};
					
		for the dynamic table:
		cust_table.setAllRowProps('onclick',rowclick );
		cust_table.setRowProp(0,'ondblclick', rowclick1);
		
		however for the default value use (when adding new rows...)
		cust_table.rdo('onclick',rowclick );
 		
 		for the search results table
 		table_name.setAllSearchResultsRowProps('onclick',rowclick );
 		table_name.setSearchResultsRowProp(0,'ondblclick', rowclick1);
 		
 		######################## Setting Table row clicks... ########################################
 		
 		setting styles
 		invoice_table.setRowProp(row,'className', '"return_row"');

 		
 		
 		
 		
 		
 		//row and cell properties:
 		events
 		http://help.dottoro.com/larrqqck.php
 		properties
 		https://developer.mozilla.org/en-US/docs/Web/API/Element/className
 		styles
 		http://www.quirksmode.org/dom/w3c_css.html
 		
 		
 		
 		create the html content for the table which includes the header and footer and return it. Attach it to the div elsewhere
 		
 		this is the column definition
 		it needs to deal with creating the header, the spacing, and the columns. It sets the cells to receive the data

		it is created in php
		javascript needs this, and the data from php
 		
 		a dynamic column lets you adjust the column - brand size chart
 			need a 'column_checkbox' and column number as well
 			when you add a column the tdo needs to add a column
 			so + the column and it will have to add a new column.... that can simply be an "array"
 		then there is static dynamic column - size chart in the order form
 			this probably is created by modifying the table def before loading it.
 			then the data has to match the db_field name
 		
 		var column_def = 
    		[{
    		'db_field' : 'none','row_number' are reserved.  1D arrays should possible
    		'type': 
    			hidden, 
    			row_checkbox, 
    			input, 
    			checkbox, 
    			select, 
    			tree_select, 
    			individual_select, 
    			textContent, 
    			innerHTML,  
    			row_number  
    			none 
    			date (dateSelect)
				
				link (which requires 'get_url_link' => "retail_sales_invoice.php?type=view",
					// no url_caption then use the data 'url_caption' => 'View',
					'get_id_link' => 'pos_customer_id')
					
				button	should be used specifically to execute javascript code
					use with 'button_caption' for caption		
    		'round' :
    		'total' :
    		'search' : LIKE ANY (explode spaces and search each option) BETWEEN EXACT - used for building the search table BETWEEN is required for type date?
    		'html' : for innerHtml?
    		'footer': what to do about the footer?
    		'valid_input' :
    		'th_repeat' : repeat table header every x lines
    		'select_values': array used for selects
    		'select_names': array used for selects
    		'select_array': used for tree select .. nested array
										$cat_array[$pos_category_id] = array(
											'name' => $cat_name,
											'children'=>$children
											);
    		'td_tags' : [] array of tags in case we want to attach something to the td
    		'properties': { //these are all javascript prooperties to attach to each element... again could be an array?
    						'size':
    						
    					
    		
    		
    						}
    		'th_width': x px em %, a 1 d array should be allowed.
    		'caption' :  //this can be a zero one or two dimensional array like size chart
    		'col_span' : // not sure how this would be implemented or why?
    		'row_span : //row span is need when a column has multiple rows, the others need rowspan.
    		'default_value'
    		'POST' : No ... need to limit post values as much as possible. max_input_vars
    		'RETURN_DATA' : NO - do not return the data looked up....why -  data was needed just for search results....
    							or we just do this in the function call to get the data...
    		'variable_get_url_link' : depending on a row result we need different links here
    		
    		
    					array(
			'row_result_lookup' => 'content_type',
			"CREDIT_CARD" => array(
							'url' => POS_ENGINE_URL.'/sales/storeCreditCards/store_credits.php?type=view', 
							'get_data' => array('pos_store_credit_id'=>'pos_store_credit_id')
										),				 
			"PRODUCT" =>  array(
							'url' => POS_ENGINE_URL.'/products/ViewProduct/view_product.php',
							'get_data' => array('pos_product_id' => 'pos_product_id')
										)
													),
													
    		
    		'word_wrap'
    		'array' : 0  //dynamic column generation db_field is modified db_field_1 etc...
    		}],
    		[{}];etc...
    




 		parent/child? rows pretty complicated.... not done
 		
 		cust_table.setRowProp(row-1,'style.visibility', '"hidden"'); just hides it, leaving blank space. 		
 		sooo the tdo should have all the rows.... then there needs to be a parent...
 		.tdo[row]['_row']['_parent'] no this will crash '
 		.tdo[row]['_parent'] = {} 
 		now who is the parent... 
 		for example catagories table.
 		apparal cat 129 pos_parent_id = 0....
 		womens cat 128 parent 129
 		
 		*/	
	//always add to the object in javascript like table_id.returned_data = returned_data
	
	//to limit data passage tbody_id = table_id + '_tbody'
	
	thead_name_id = table_id + '_thead';
	tbody_name_id = table_id + '_tbody';
	tfoot_name_id = table_id + '_tfoot';
	this.thead_id = thead_name_id;
	this.tbody_id = tbody_name_id;
	this.thead_id = tfoot_name_id;
	this.rowCount = 0;
	//global variables need to be the current_row... not
	this.tdo = [];
	this.table_name = table_id;
	this.id = table_id;
	//assign the html table straight to the object

	this.current_row = 0;
	this.current_column = 0;
	this.cdo = column_definition;
	this.rdo = {};
	this.json_table_contents = json_table_contents;

	// this does not work... this.init();

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

		console.log('initing ' + this.table_name);
		//console.log('cdo ');
		//console.log(this.cdo);

		//this seems to need to happen on the load function....
		if (this.json_table_contents.length > 0)
		{
//			console.log(json_table_contents.length);
			for(var row=0;row<json_table_contents.length;row++)
			{

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
		this.tbody = document.getElementById(this.tbody_id);

	}
	this.initializeTableObject = function()
	{
		var new_object = [];
		/*for(var col=0;col<this.cdo.length;col++)
		{
			new_object[this.cdo[col]['db_field']] = {};
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
	//			Column functions
	//			
	//			
	//
	//
	//#####################################################################//
	
	this.AddColumn = function(index,col_def)
	{
		//we need a new column....
		//what is the column name? and where does it go?
		var new_col_def = [];
		var cntr = 0;
		for(var i = 0;i<this.cdo.length;i++)
		{
			if(i==index)
			{
				//put the new def here
				new_col_def[cntr] = col_def;
				//add it to the tdo...
				for( var row= 0;row<this.tdo.length;row++)
				{
					//what data do we add?
					item_data = '';
					this.addItemToColumn(col_def,item_data,row);
				}
			}
			else
			{
				new_col_def[cntr] = column_def[i];
			}
			cntr++;
		}
		this.cdo = new_col_def;	
	}
	this.addDynamicColumn = function(column_name)
	{
		//ok user presses button. I have to tell me that the button pressed was to add to column size_row
		
		col_index = getCDOColumnNumberFromName(column_name);
		//now there has to be an array index.
		
		var col_def = this.cdo[col_index];
		array_index = col_def['array'] + 1;
		this.cdo[col_index]['array'] = array_index;
		col_def['db_field'] = col_def['db_field'] + '_' + array_index;
		this.addColumn(col_index+array_index,col_def);
		//now we need to rewrite the table....user needs to add the table 
		$('#'+this.id).replaceWith(this.createTableHTML(''));
		
		
	}
	this.deleteColumn= function(index)
	{
		var new_col_def = [];
		var cntr = 0;
		for(var i = 0;i<this.cdo.length;i++)
		{
			if(i==index)
			{
				
				 array.splice(index, 1);
				new_col_def[cntr] = col_def;
				//add it to the tdo...
				for( var row= 0;row<this.tdo.length;row++)
				{
					delete this.tdo[row][this.cdo[i]['db_field']];
				}
			}
			else
			{
				new_col_def[cntr] = column_def[i];
			}
			cntr++;
		}
		this.cdo = new_col_def;	
	}
	
	this.deleteCheckedColumn = function()
	{
		indexies = this.findCheckedColumns();
		for(var i=0;i<indexies.length;i++)
		{
			deleteColumn(indexies[i]);
		}
	}
	this.findCheckedColumns = function()
	{
	}
//#####################################################################//
//
//			table functions
//			
//			
//
//
//#####################################################################//	
	this.addToDiv = function(div_id)
 	{
 		$thead_name = this.id + '_thead';
		$tbody_name = this.id + '_tbody';
		$tfoot_name = this.id + '_tfoot';
 		
 		//we need to search for the largest caption array to calculate rowspans
 		
 		html = '';
 		html +=  '<TABLE id="'+this.id+'" name="'+this.id+'"  >';
		html += '<thead id="'+$thead_name+'"  name="'+$thead_name+'">' +newline();
		
		html += '<tr>'+newline();
		for(i=0;i<this.cdo.length;i++)
		{
			th_width = '';	
			if(typeof this.cdo[i]['th_width'] != 'undefined')
			{
				th_width = ' style="width:' +this.cdo[i]['th_width']+'"';
			}
		
			if(typeof this.cdo[i]['type'] != 'undefined' &&  this.cdo[i]['type'] != 'hidden')
			{
				caption = '';
				if(typeof this.cdo[i]['caption'] != 'undefined')
				{
					caption = this.cdo[i]['caption'];
				}
				html += '<th' + th_width +'>' + caption +'</th>' + newline();		
			}
		}
		html += '</tr>';

		html += '</thead>';
		html += '<tbody id="' +this.tbody_id + '"></tbody>';
		html += '</table>';
		$('#' + div_id).html(html);
		this.init();
		return html;
	}	
	this.createTHeadHTML = function()
	{
	}
	this.createTBodyHTML = function()
	{
	}
	this.addSearchTableToDiv = function(div_id,get_post_session_params)
	{
 		//first create the table... then add it to the page. Then operate on it....
		var tbl=document.createElement('table');
		tbl.id = this.id + '_search';
		tbl.className = 'search_table';
		$('#' + tbl.id).addClass('search_table');
		var thead=document.createElement('thead');
		thead.id =  this.id + '_search_thead';
		var tbdy=document.createElement('tbody');
		tbdy.id =  this.id + '_search_tbody';
		
		tbl.appendChild(thead);
		tbl.appendChild(tbdy);
		$('#' + div_id).append(tbl);
		var me = this;
		//add a key event listener to the div...
		$("#" +div_id).keypress(function (event) 
		{
			if (event.which == 13) 
			{
				me.submitSearch();
			}
		});
		$('#' + div_id).append('<div id="' +this.table_name+'_search_buttons">');
		$('#' + this.table_name+'_search_buttons').append('<input type="button" class = "button" value="Search" onclick="'+this.table_name+'.submitSearch()">');
	$('#' + this.table_name+'_search_buttons').append('<input type="button" class = "button" value="Reset" onclick="'+ this.table_name +'.resetSearch()">');
		
		$('#' + div_id).append('<div id="' +this.table_name+'_search_loading_image">');
		$('#' + this.table_name+'_search_loading_image').append('<img src="' + POS_ENGINE_URL + '/includes/images/ajax_loader_gray.gif">');
		$('#' + this.table_name+'_search_loading_image').addClass('loading_image');
		$('#' + this.table_name+'_search_loading_image').hide();
		$('#' + div_id).append('<div id="' + this.table_name+'_search_results">');
		$('#' + this.table_name+'_search_results').html('<p>No search Results</p>');
		

		//now operate...
		
		var tr=thead.insertRow();
		for(var i=0;i<this.cdo.length;i++)
		{
			if(typeof this.cdo[i]['search'] != 'undefined')
			{
				if(typeof this.cdo[i]['th_width'] != 'undefined')
				{
					th_width = this.cdo[i]['th_width'];	
				}
				else
				{
					th_width ='';
				}
					caption = '';
				
				if(this.cdo[i]['type'] == 'date')
				{
					
					if(typeof this.cdo[i]['caption'] != 'undefined')
					{
						caption = this.cdo[i]['caption'];
					}
					var th = document.createElement('th');
					th.innerHTML = caption + ' Start Date';
					th.style.width = th_width;
					tr.appendChild(th);
					

					var th = document.createElement('th');
					th.innerHTML = caption + ' END Date';
					
				}
				else
				{
					if(typeof this.cdo[i]['caption'] != 'undefined')
					{
						caption = this.cdo[i]['caption'];
					}
					//var th=tr.insertCell();
					var th = document.createElement('th');					
					th.innerHTML = caption;	
				}	
				th.style.width = th_width;
				tr.appendChild(th);
			}
		}
		
		var tr=tbdy.insertRow();
		var col_counter = 0;
		for(var c=0;c<this.cdo.length;c++)
		{
			if(typeof this.cdo[c]['search'] != 'undefined')
			{
				th_width = '';	
				cell_html = '';
				cell = tr.insertCell(col_counter);
				//var td=document.createElement('td');
				cell.id = this.id + '_' + "sr0" + "sc" + col_counter;
				col_counter++;
				
				//search is either date, select, or checkbox otherwise input....
				if(this.cdo[c]['type'] == 'date')
				{
					var element = document.createElement('input');
					element.name = this.id + '_' +  this.cdo[c]['db_field'] + '_date_start_search';
					element.id = this.id + '_' +  this.cdo[c]['db_field'] + '_date_start_search';				
					cell.appendChild(element);
						
					$('#' + element.id).datepicker(					
					{
						dateFormat: 'yy-mm-dd',
						onSelect: function () 
						{
                			$(this).focus();
                		},
           				onClose: function (dateText, inst) 
           				{
                    		$(this).select();
                    	}
                    });

					cell = tr.insertCell(col_counter);
					//var td=document.createElement('td');
					cell.id = "sr0" + "sc" + col_counter;
					col_counter++;
					
					var element = document.createElement('input');
					element.name = this.id + '_' + this.cdo[c]['db_field'] + '_date_end_search';
					element.id = this.id + '_' + this.cdo[c]['db_field'] + '_date_end_search';
					cell.appendChild(element);

					//setting it to date happens after we append it to the div....			
				
					$('#' + element.id).datepicker(					
					{
						dateFormat: 'yy-mm-dd',
						onSelect: function () 
						{
                			$(this).focus();
                		},
           				onClose: function (dateText, inst) 
           				{
                    		$(this).select();
                    	}
                    });	
				}
				else if(this.cdo[c]['type'] == 'checkbox')
				{

					
					element = document.createElement('select');
					element.name = this.id + '_' + this.cdo[c]['db_field'] + '_search';
					element.id = this.id + '_' + this.cdo[c]['db_field'] + '_search';
					var option = document.createElement('option');
					option.value = 'NULL';
					option.appendChild(document.createTextNode("Either"));
					element.appendChild(option);
					option = document.createElement('option');
					option.value = '1';
					option.appendChild(document.createTextNode('Checked'));
					element.appendChild(option);
					option = document.createElement('option');
					option.value = '0';
					option.appendChild(document.createTextNode('Not Checked'));
					element.appendChild(option);
					
					cell.appendChild(element);
					
					
				}
				else if (this.cdo[c]['type'] == 'select')
				{
					element = document.createElement('select');
					element.name = this.id + '_' + this.cdo[c]['db_field'] + '_search';
					element.id = this.id + '_' + this.cdo[c]['db_field'] + '_search';
					var option = document.createElement('option');
					option.value = 'NULL';
					option.appendChild(document.createTextNode("Select..."));
					element.appendChild(option);
					for (var i in this.cdo[c]['select_names'])
					{
						option = document.createElement('option');
						option.value = this.cdo[c]['select_values'][i];
						option.appendChild(document.createTextNode(this.cdo[c]['select_names'][i]));
						element.appendChild(option);
					}
					cell.appendChild(element);
				}
				else if (this.cdo[c]['type'] == 'tree_select')
				{
					element = document.createElement('select');
					element.id = this.id + '_' +  this.cdo[c]['db_field'] + '_search';
					element.name = this.id + '_' + this.cdo[c]['db_field'] + '_search';
					
					
					var option = document.createElement('option');
					option.value = 'NULL';
					option.appendChild(document.createTextNode("Select..."));
					element.appendChild(option);
					var level = 0;
					//console.log(this.cdo[c]['select_array']);
					this.addNestedSelectOptions(element, this.cdo[c]['select_array'], level);
					



					if (typeof this.cdo[c]['properties'] !== 'undefined')
					{
						for (var index in this.cdo[c]['properties'])
						{
							eval('element.' + index + '= ' + this.cdo[c]['properties'][index] + ';');
							
						}
					}
					cell.appendChild(element);
				}				
				else
				{
					element = document.createElement('input');
					element.type = 'text';
					element.id = this.id + '_' + this.cdo[c]['db_field'] + '_search';
					element.name = this.id + '_' + this.cdo[c]['db_field'] + '_search';
					cell.appendChild(element);
					
				}				
			}
		}
		
		//finally set focus to the first searchable element....
		var focus_set = false
		for(var c=0;c<this.cdo.length;c++)
		if(typeof this.cdo[c]['search'] != 'undefined')
		{
			if(!focus_set)
			{
				$("#" + this.cdo[c]['db_field'] + "_search").focus();
				$("#" + this.cdo[c]['db_field'] + "_search").select();
				focus_set = true;
			}
		}
		//now load any stored values into the table
		this.loadGetPostSessionParams(get_post_session_params);
		//load any stored data to 
		//now display the results here....
		this.CreateSearchResultsTable(this.table_name+'_search_results', this.json_table_contents);
		
	}
	this.ModifyPageURLWithSearchParameters = function()
	{

		search_parameters = parseQuery_v2(location.search);
		//console.log(this.id + ' search_parameters yooooooo000000000000000000000000000000000');
		//console.log(search_parameters);
		keepKey = {};
		for (var key in search_parameters) 
		{
			var found = false;
			for(var c=0;c<this.cdo.length;c++)
			{
				if (key == this.id + '_' + this.cdo[c]['db_field']+'_date_start_search')
				{
					console.log('found ' + this.id + '_' + this.cdo[c]['db_field']+'_date_start_search');
					found = true;
				}
				else if (key == this.id + '_' + this.cdo[c]['db_field']+'_date_end_search')
				{
					console.log('found ' + this.id + '_' + this.cdo[c]['db_field']+'_date_end_search');
					found = true;
				}
				else if (key == this.id + '_' + this.cdo[c]['db_field']+'_search')
				{
					console.log('found ' + this.id + '_' + this.cdo[c]['db_field']+'_search');
					found = true;
				}
			}
			if(!found)
			{
				keepKey[key] = search_parameters[key];
			}
				
		}
		//console.log('keepKey');
		//console.log(keepKey);
		
		//go through the search table and find what values we need
		var url_params = {};
		for(var c=0;c<this.cdo.length;c++)
		{
			if(typeof this.cdo[c]['search'] != 'undefined')
			{
				if(this.cdo[c]['type'] == 'date')
				{
					if($('#' +this.id + '_' + this.cdo[c]['db_field']+'_date_start_search').val() != '')
					{
						//add it to the url
						url_params[this.id + '_' + this.cdo[c]['db_field']+'_date_start_search'] = $('#' +this.id + '_' + this.cdo[c]['db_field']+'_date_start_search').val();
					}
					if($('#' + this.id + '_' + this.cdo[c]['db_field']+'_date_end_search').val() != '')
					{
						//add it to the url
						url_params[this.id + '_' + this.cdo[c]['db_field']+'_date_end_search'] = $('#' +this.id + '_' + this.cdo[c]['db_field']+'_date_end_search').val();
					}
				
				}
				else if(this.cdo[c]['type'] == 'checkbox')
				{
					if($('#' + this.id + '_' + this.cdo[c]['db_field']+'_search').val() != 'NULL')
					{
						//add it to the url
						url_params[this.id + '_' + this.cdo[c]['db_field']+'_search'] = $('#' +this.id + '_' + this.cdo[c]['db_field']+'_search').val();
					}

				}
				else if (this.cdo[c]['type'] == 'select' || this.cdo[c]['type'] == 'tree_select')
				{
					if($('#' +this.id + '_' +this.cdo[c]['db_field']+'_search').val() != 'NULL')
					{
						//add it to the url
						url_params[this.id + '_' + this.cdo[c]['db_field']+'_search'] = $('#' +this.id + '_' + this.cdo[c]['db_field']+'_search').val();
					}
				}
				else
				{
					if($('#' +this.id + '_' + this.cdo[c]['db_field']+'_search').val() != '')
					{
						//add it to the url
						url_params[this.id + '_' + this.cdo[c]['db_field']+'_search'] = $('#' +this.id + '_' + this.cdo[c]['db_field']+'_search').val();
					}
				}
			}
		}
		//here we would want to add in the sort values		
		//create url_prarms string
		console.log(this.id + ' url_params');
		console.log(url_params);
		url_search_string_array = [];
		counter = 0;
		
		for (var key in url_params) 
		{
    		var value = url_params[key];
    		url_search_string_array[counter] = key +'=' + encodeURIComponent(value);
    		counter++;
		}
		keep_search_string_array = [];
		counter = 0;
		for (var key in keepKey) 
		{
    		var value = keepKey[key];
    		keep_search_string_array[counter] = key +'=' + encodeURIComponent(value);
    		counter++;
		}		
		
		
		
		if(url_search_string_array.length >0)
		{
			url_search_string = url_search_string_array.join('&');
			//url_search_string = '?' + url_search_string;
		}
		else
		{
			url_search_string = '';
		}
		
		
		if(keep_search_string_array.length >0)
		{
			keep_search_string = keep_search_string_array.join('&');
		}
		else
		{
			keep_search_string = '';
		}
		
		//console.log('url_search_string '+ url_search_string);
		//console.log('keep '+ keep_search_string);

		var final_url_search_string = '';
		if(url_search_string_array.length >0)
		{
			if( keep_search_string_array.length >0)
			{
				final_url_search_string = '?' + url_search_string +'&' + keep_search_string;
			}
			else
			{
				final_url_search_string = '?' + url_search_string;
			}
		}
		else
		{
			if( keep_search_string_array.length >0)
			{
				final_url_search_string = '?' + keep_search_string;
			}
			else
			{
				final_url_search_string = '';
			}
		}
		//console.log('final url_search_string '+ final_url_search_string);
	

		
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
		var new_url =  a.hostname + a.pathname + final_url_search_string;
		//console.log('new_url ' + new_url);
		var pageHTML = document.documentElement.innerHTML;
		var title = document.title;
		window.history.pushState('object or string', title, filename + final_url_search_string);


		
		
		
		
	}
	this.loadGetPostSessionParams = function(get_post_session_params)
	{
		
		console.log('get_post_session_params');
		console.log(get_post_session_params);
		
		//this will set the values of the search input boxes
		for(var c=0;c<this.cdo.length;c++)
		{
			
			var db_field = this.cdo[c]['db_field'];
			var id =  this.id +'_' + db_field + '_search';
			var id2 = this.id +'_' + db_field + '_date_start_search';
			var id3 = this.id +'_' + db_field +'_date_end_search';
			
			

			if(typeof get_post_session_params[id] != 'undefined')
			{
				//we have a value....
				var value = get_post_session_params[id];
				if(this.cdo[c]['type'] == 'checkbox')
				{
					//there is no checkbox in the search.... it is a select...
					$('#' + id).val(get_post_session_params[id]);

				}
				else
				{
					$('#' + id).val(get_post_session_params[id]);
				}
			}
			if(typeof get_post_session_params[id2] !== 'undefined')
			{
				$('#' + id2).val(get_post_session_params[id2]);
			}
			if(typeof get_post_session_params[id3] !== 'undefined')
			{
				$('#' + id3).val(get_post_session_params[id3]);
			}
		}

	}
	this.submitSearch = function()
	{
		//check for ajaxHandler
		if(typeof this.ajaxHandler === 'undefined')
		{
			alert("You forgot to set the ajax handler in php: table_name.ajaxHandler=\'ajax_handler.php\'");
			return false;
		}
		if(typeof this.searchFlag === 'undefined')
		{
			alert("You forgot to set the search handler in php: table_name.searchHandler=\'BLAAAA'");
			return false;
		}
		
		var post_data = {};
		post_data['ajax_request'] = this.searchFlag;
		
		for(var c=0;c<this.cdo.length;c++)
		{
			if(typeof this.cdo[c]['search'] != 'undefined')
			{
				//search is either date, select, or checkbox otherwise input....
				if(this.cdo[c]['type'] == 'date')
				{
					var elementid= this.id + '_' + this.cdo[c]['db_field'] + '_date_start_search';
					var val = trim($('#' + elementid).val());
					if ( val != '')
					{
						post_data[elementid] = val;
					}
					var elementid= this.id + '_' + this.cdo[c]['db_field'] + '_date_end_search';
					var val = trim($('#' + elementid).val());
					if ( val != '')
					{
						post_data[elementid] = val;
					}
				}
				else if(this.cdo[c]['type'] == 'checkbox' || this.cdo[c]['type'] == 'row_checkbox' || this.cdo[c]['type'] == 'radio')
				{
					
					elementid = this.id + '_' + this.cdo[c]['db_field'] + '_search';
					if ($('#' + elementid).val() == '1')
					{
						post_data[elementid] = 1;
					}
					else if($('#' + elementid).val() == '0')
					{
						post_data[elementid] = 0;
					}
					else
					{
						//dont bother with the post
					}
				}
				else
				{
					elementid = this.id + '_' + this.cdo[c]['db_field'] + '_search';
					var val = trim($('#' + elementid).val());
					
					if ( val == '' || val == 'NULL')
					{
						//skip posting...
					}
					else
					{
						post_data[elementid] = val;
					}
					
				}				
			}
		}
		//now ajax....
		
		$('#' + this.table_name+'_search_results').html('');
		
		$('#' + this.table_name+'_search_buttons').hide();
		$('#' + this.table_name+'_search_loading_image').show();
		console.log('post data');
		console.log(post_data);
		var me = this;
		$.post(this.ajaxHandler, post_data,
		function(response) 
		{
			
			//clear the tdo....
			
			$('#' + me.table_name+'_search_loading_image').hide();
			$('#' + me.table_name+'_search_buttons').show();
			
			console.log(response);
			var parsed_data = parseJSONdata(response);
			//console.log(parsed_data);
			//console.log(parsed_data);
			//console.log('length');
			//console.log(parsed_data.length);
			//without a reload
			//here we need to create the search table results
			//load the data into the object

			me.CreateSearchResultsTable(me.table_name+'_search_results', parsed_data);
			if(me.postSearchResultsFunction && typeof me.postSearchResultsFunction === 'function')
			{
				me.postSearchResultsFunction();
			}
			//now modify the url 
			me.ModifyPageURLWithSearchParameters();
			
			//me.swipeNwriteHTMLTable();
			
			
		});

	}
	this.resetSearch = function(php_ajax_handler)
	{
		//check for ajaxHandler
		if(typeof this.ajaxHandler === 'undefined')
		{
			alert("You forgot to set the ajax handler in php: table_name.ajaxHandler=\'ajax_handler.php\'");
			return false;
		}
		if(typeof this.resetFlag === 'undefined')
		{
			alert("You forgot to set the reset handler in php: table_name.searchHandler=\'BLAAAA'");
			return false;
		}
		//clear values out of the search boxes
		//modify the url get parameters
		//modify the window history
		//post to remove session values
		this.CreateSearchResultsTable(this.table_name+'_search_results',[]);
		//reset values...
		for(var c=0;c<this.cdo.length;c++)
		{
			if(typeof this.cdo[c]['search'] != 'undefined')
			{
				if(this.cdo[c]['type'] == 'date')
				{
					$('#' +this.id + '_' +  this.cdo[c]['db_field']+'_date_start_search').val(''); 
					$('#' +this.id + '_' +  this.cdo[c]['db_field']+'_date_end_search').val('');
				}
				else if(this.cdo[c]['type'] == 'checkbox')
				{
					$('#' +this.id + '_' +  this.cdo[c]['db_field']+'_search').val('NULL');
				}
				else if (this.cdo[c]['type'] == 'select' || this.cdo[c]['type'] == 'tree_select')
				{
					$('#' +this.id + '_' +  this.cdo[c]['db_field']+'_search').val('NULL');
				}
				else
				{
					$('#' +this.id + '_' +  this.cdo[c]['db_field']+'_search').val('');
				}
			}
		}
		//modify url
		this.ModifyPageURLWithSearchParameters();
		var post_data = {};
		post_data['ajax_request'] = this.resetFlag;
		$.post(this.ajaxHandler, post_data,
		function(response) 
		{
			console.log(response);
			//here we need to create the search table results
			
			
		});
		
	}
	this.CreateSearchResultsTable = function(div_id, parsed_data)
	{
		this.tdo = this.initializeTableObject();
		for(var d=0; d<parsed_data.length; d++)
		{
			var nrow = this.addItemDataToTableObject(parsed_data[d]);
		}
	
		if(this.tdo.length>0)
		{
			//goes to ('#' + this.table_name+'_search_results');
			var totals_row = false;
			for(var i=0;i<this.cdo.length;i++)
			{
				if(typeof this.cdo[i]['total'] != 'undefined')
				{
					totals_row = true;
				}
			}
			var tbl=document.createElement('table');
			tbl.id = this.id + '_search';
			tbl.className = 'generalTable';
			$('#' + tbl.id).addClass('search_results_table');
			var thead=document.createElement('thead');
			thead.id =  this.id + '_search_results_thead';
			var tbdy=document.createElement('tbody');
			tbdy.id =  this.id + '_search_results_tbody';
			tbl.appendChild(thead);
			if(totals_row)
			{
				var total_tbdy=document.createElement('tbody');
				total_tbdy.id =  this.id + '_total_search_results_tbody';
				tbl.appendChild(total_tbdy);
			}
			tbl.appendChild(tbdy);
			$('#' + div_id).html(tbl);
		

			//create the header
			var tr=thead.insertRow();
			for(var i=0;i<this.cdo.length;i++)
			{
				if(typeof this.cdo[i]['type'] != 'hidden')
				{
					if(typeof this.cdo[i]['th_width'] != 'undefined')
					{
						th_width = this.cdo[i]['th_width'];	
					}
					else
					{
						th_width ='';
					}
					caption = '';
				
					if(typeof this.cdo[i]['caption'] != 'undefined')
					{
						caption = this.cdo[i]['caption'];
					}
					//var th=tr.insertCell();
					var th = document.createElement('th');					
					th.innerHTML = caption;	
				
					th.style.width = th_width;
					tr.appendChild(th);
				}
			
			}
		
		
			//totals row
			if(totals_row)
			{
				var tr=total_tbdy.insertRow();
				tr.className='generalTableTotalsRow';
				tr.style.backgroundColor = 'yellow';
				var total_place = false;
				var col_counter = 0;
				for(var c=0;c<this.cdo.length;c++)
				{
					if(typeof this.cdo[c]['type'] != 'hidden')
					{
						cell = tr.insertCell(col_counter);
						cell.id = "tsrr0" + "c" + col_counter;
						col_counter++;
					
						if(!total_place)
						{
							cell.innerHTML = 'TOTALS';
							total_place = true;
						}
				
						if(typeof this.cdo[c]['total'] != 'undefined')
						{
							var total = 0.0;
							for(var r=0;r<this.tdo.length;r++)
							{
								total = total + myParseFloat(this.tdo[r][this.cdo[c]['db_field']]['data']);
							}
							cell.innerHTML = round2(total,this.cdo[c]['total']);
						}
					}
				}

			}
			//create the body
			for(var r=0;r<this.tdo.length;r++)
			{
				var tr=tbdy.insertRow();
				//set the row properties
				for (var index in this.tdo[r]['_row'])
				{
					eval('tr.' + index + '= ' + this.tdo[r]['_row'][index] + ';');
				}
				var col_counter = 0;
				for(var c=0;c<this.cdo.length;c++)
				{
					if(this.cdo[c]['type'] != 'hidden')
					{
						th_width = '';	
						cell_html = '';
						cell = tr.insertCell(col_counter);
						//var td=document.createElement('td');
						cell.id = "srr"+ r+ + "c" + col_counter;
						col_counter++;
				
						//search is either date, select, or checkbox otherwise input....
						if(this.cdo[c]['type'] == 'date')
						{
							cell.innerHTML = this.tdo[r][this.cdo[c]['db_field']]['data'];

						}
						else if(this.cdo[c]['type'] == 'checkbox')
						{
							element = document.createElement('input');
							element.type = 'checkbox';
							element.name = this.cdo[c]['db_field'] + '_search';
							element.id = this.cdo[c]['db_field'] + '_search';
							if(this.tdo[r][this.cdo[c]['db_field']]['data'] == 1)
							{
								element.checked = true;
							}
							element.disabled = true;				
							cell.appendChild(element);		
						}
						//select usually has an id....
						else if (this.cdo[c]['type'] == 'select')
						{
							var index = this.cdo[c]['select_values'].indexOf(this.tdo[r][this.cdo[c]['db_field']]['data']);
							var value =this.cdo[c]['select_names'][index];
							cell.innerHTML = value;
						
						
						}
						else if (this.cdo[c]['type'] == 'tree_select')
						{
							var index = this.cdo[c]['select_values'].indexOf(this.tdo[r][this.cdo[c]['db_field']]['data']);
							var value =this.cdo[c]['select_names'][index];
							cell.innerHTML = value;
						}				
						else if (this.cdo[c]['type'] == 'input')
						{
							var data = this.tdo[r][this.cdo[c]['db_field']]['data'];
							if(typeof this.cdo[c]['round'] != 'undefined')
							{
								data = myParseFloat(data);
								data = round2(data, this.cdo[c]['round']);
							}
							else
							{
							}
							cell.innerHTML = data;
					
						}
						else if (this.cdo[c]['type'] == 'innerHTML')
						{
							cell.innerHTML = this.tdo[r][this.cdo[c]['db_field']]['data'];
					
						}
						else if (this.cdo[c]['type'] == 'button')
						{
							element = document.createElement('button');
							element.id = this.cdo[c]['db_field'] + '_sr' + r;
							element.className="button";
							if(typeof this.cdo[c]['button_caption'] != 'undefined')
							{
								var t = document.createTextNode(this.cdo[c]['button_caption']);     
								element.appendChild(t); 
							} 
						
							if (typeof this.cdo[c]['properties'] !== 'undefined')
							{
								for (var index in this.cdo[c]['properties'])
								{
									eval('element.' + index + '= ' + this.cdo[c]['properties'][index] + ';');
							
								}
							}
							cell.appendChild(element);
						}
						else if (this.cdo[c]['type'] == 'link')
						{
							var data = this.tdo[r][this.cdo[c]['db_field']]['data'];
							if(typeof this.cdo[c]['url_caption'] !== 'undefined')
							{	
								var caption = this.cdo[c]['url_caption'];
							}
							else
							{
								var caption = data;
							}
							delim = '?';
							if(this.cdo[c]['get_url_link'].indexOf('?') != -1)
							{
								delim = '&';
							}
							cell.innerHTML = '<a href="' + this.cdo[c]['get_url_link'] + delim + this.cdo[c]['get_id_link'] + '=' +  data + '">'+caption + '</a>';
										
						}
						else
						{
							cell.innerHTML = 'pendinging code .... ' + this.tdo[r][this.cdo[c]['db_field']]['data'];
						}			
					}
				}
			}
		
		
			
			
		}
		else
		{
			$('#' + this.table_name+'_search_results').html('<p>No search Results</p>');
		}
		
	
	}
	this.setAllSearchResultsRowProps = function(row_prop, row_function)
	{
		//set the row property to the tdo
		//if the property exists we need to overwrite it.
		for(var i=0;i<this.tdo.length;i++)
		{
			this.tdo[i]['_row'][row_prop] = row_function;			
		}
		
		
		
		
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
		var col = this.getCDOColumnNumberFromName(table_def_column_name);
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
		for(var col=0;col<this.cdo.length;col++)
		{
			if (typeof this.cdo[col]['default_value'] !== 'undefined')
			{
				
				//console.log(this.tdo);
				this.tdo[row][this.cdo[col]['db_field']]['data'] = this.cdo[col]['default_value'];
			}
		}
		
		row_number = this.addRowToHTMLTable();
		this.writeObjectToHTMLTable();
		this.updateTable();
		//this.updateTableDataForPost();
		var tbody = document.getElementById(this.tbody_id);
		//set focus to the first element in the new row after the row number
		for(var col=0;col<this.cdo.length;col++)
		{
			if (this.cdo[col]['type'] == 'hidden')
			{
			}
			else if (this.cdo[col]['db_field'] == 'none' || this.cdo[col]['db_field'] == 'row_number')
			{
			}
			else
			{
				tbody.rows[row_number].cells[this.getHTMLColumnNumberFromTableDefColumnName(this.cdo[col]['db_field'])].childNodes[0].focus();
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
		var	row = this.tdo.length; //rowCount;
		this.tdo[row] = {};	
		//'_row' holds the properties for the row.
		this.tdo[row]['_row'] = this.rdo;//{};
		this.tdo[row]['_parent'] = {};
		var data;
		data = '';
		for(var col=0;col<this.cdo.length;col++)
		{
			//set it to nothing....
			//this could be an array???
			
			if(typeof this.cdo[col]['price_array_index'] !== 'undefined')
			{
				this.tdo[row][this.cdo[col]['db_field']] = {};
				this.tdo[row][this.cdo[col]['db_field']]['array_values'] = {};
				this.tdo[row][this.cdo[col]['db_field']]['display_value'] = '';
				
				//this.table_data_array[row][this.cdo[col]['db_field']] = [];	
				var quantity = 0;
				for(var qty=0;qty<quantity;qty++)
				{
					this.tdo[row][this.cdo[col]['db_field']]['array_values'][qty] = data;
				}
			}
			//what about the special select values?
			else if(typeof this.cdo[col]['individual_select_options'] !== 'undefined')
			{	
				this.tdo[row][this.cdo[col]['db_field']] = {};
				this.tdo[row][this.cdo[col]['db_field']]['select_values'] = {};
				this.tdo[row][this.cdo[col]['db_field']]['select_names'] = {};
				this.tdo[row][this.cdo[col]['db_field']]['value'] = data;
		
			}
			else
			{
				this.tdo[row][this.cdo[col]['db_field']] = {};	
				this.tdo[row][this.cdo[col]['db_field']]['data'] = data;
				this.tdo[row][this.cdo[col]['db_field']]['cell'] = {};
	
			}
			
			
		}
		
		
		this.rowCount = this.rowCount+1;
		return row;
	
		
	}
	this.addItemDataToTableObject = function(item_data)
	{
		var	row = this.addRowToTableObject(); //this.tdo.length;
		//console.log(this.tdo);
		var data;
		for(var col=0;col<this.cdo.length;col++)
		{
			//console.log(this.cdo[col]);
			//console.log(item_data);
			this.addItemToColumn(this.cdo[col],item_data,row);
		}
		
		//this.updateTableDataForPost();
		this.updateTableObjectLineNumbers();
		this.rowCount = this.rowCount+1;
		return row;
	}
	this.addItemToColumn = function(col,item_data,row)
	{
		//make sure item data is not an array.. done it before@@
		if(typeof item_data[col['db_field']] !== 'undefined')
		{
			data = item_data[col['db_field']];
			
		}
		else
		{
			if(typeof col['default_value'] !== 'undefined')
			{
				data = col['default_value'];
			}
			else
			{
				data = '';
			}
		
		}
		if(typeof col['row_number'] !== 'undefined')
		{
			data = this.tdo.length;
		}
		if(typeof col['price_array_index'] !== 'undefined')
		{
			this.tdo[row][col['db_field']] = {};
			this.tdo[row][col['db_field']]['array_values'] = {};
			this.tdo[row][col['db_field']]['display_value'] = '';
		
			//this.table_data_array[row][col['db_field']] = [];	
			var quantity = item_data[col['price_array_index']];
			for(var qty=0;qty<quantity;qty++)
			{
				this.tdo[row][col['db_field']]['array_values'][qty] = data;
				//this.table_data_array[row][this.cdo[column]['db_field']][qty] = data;	
			}
		}
		//what about the special select values?
		else if(typeof col['individual_select_options'] !== 'undefined')
		{
			this.tdo[row][col['db_field']] = {};
			this.tdo[row][col['db_field']]['select_values'] = {};
			this.tdo[row][col['db_field']]['select_names'] = {};
			this.tdo[row][col['db_field']]['value'] = data;
		
			var values = item_data[col['individual_select_options']]['values'];
			for(var opt=0;opt<values.length;opt++)
			{
				this.tdo[row][col['db_field']]['select_values'][opt] = item_data[col['individual_select_options']]['values'][opt];
				this.tdo[row][col['db_field']]['select_names'][opt] = item_data[col['individual_select_options']]['names'][opt];
				//this.table_data_array[row][this.cdo[column]['db_field']][qty] = data;	
			}
		}
		else
		{
		
			this.tdo[row][col['db_field']] = {};
			this.tdo[row][col['db_field']]['data'] = data;	
			this.tdo[row][col['db_field']]['cell'] = {};
		}
	}
	this.updateTableObjectLineNumbers = function()
	{
		//seems pointless but needed for post data...
		//db_field has to be 'row_number' for this to work
		//we need to loop through the tbody cells and set the value of the column name bla bla
		var col = this.getCDOColumnNumberFromName('row_number');
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
		for(var col=0;col<this.cdo.length;col++)
		{
			if(typeof item_data[this.cdo[col]['db_field']] !== 'undefined')
			{
				data = item_data[this.cdo[col]['db_field']];
				
				if(typeof this.cdo[col]['price_array_index'] !== 'undefined')
				{	
					this.tdo[row][this.cdo[col]['db_field']] = {};
					this.tdo[row][this.cdo[col]['db_field']]['array_values'] = {};
					this.tdo[row][this.cdo[col]['db_field']]['display_value'] = data;
					
					var quantity = myParseInt(item_data[this.cdo[col]['price_array_index']]);
					for(var qty=0;qty<quantity;qty++)
					{
						this.tdo[this.cdo[col]['db_field'][row]]['array_values'][qty] = data;
					}
				}
				else if(typeof this.cdo[col]['individual_select_options'] !== 'undefined')
				{				
					this.tdo[row][this.cdo[col]['db_field']] = {};
					this.tdo[row][this.cdo[col]['db_field']]['select_values'] = {};
					this.tdo[row][this.cdo[col]['db_field']]['select_names'] = {};
					this.tdo[row][this.cdo[col]['db_field']]['value'] = data;
					
					var values = item_data[this.cdo[col]['individual_select_options']]['values'];
					//var values = item_data[['values'];
					for(var opt=0;opt<values.length;opt++)
					{						
						this.tdo[row][this.cdo[col]['db_field']]['select_values'][opt] = item_data[this.cdo[col]['individual_select_options']]['values'][opt];
						this.tdo[row][this.cdo[col]['db_field']]['select_names'][opt] = item_data[this.cdo[col]['individual_select_options']]['names'][opt];
						//this.table_data_array[row][this.cdo[column]['db_field']][qty] = data;	
					}
				}
				else
				{
					this.tdo[row][this.cdo[col]['db_field']]['data'] = data;
					
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
			for(col= 0;col<this.cdo.length;col++)
			{			
				if(this.cdo[col]['type'] != 'hidden')
				{	
					if (typeof this.cdo[col]['price_array_index'] === 'undefined')
					{
						//alert(tbody.rows[row].cells[column_counter].childNodes[0].value);
						//console.log(this.cdo[col]['type']);
						if (this.cdo[col]['type'] == 'checkbox' || this.cdo[col]['type'] == 'radio')
						{
							this.tdo[row][this.cdo[col]['db_field']]['data'] = tbody.rows[row].cells[column_counter].childNodes[0].checked;
	
						}
						else if(this.cdo[col]['type'] == 'innerHTML')
						{
							//user cant change this....
							//this.tdo[row][this.cdo[col]['db_field']]['data'] = this.tdo[row][this.cdo[col]['db_field']]['data'];
							//but code can....
						}
						else if(this.cdo[col]['type'] == 'row_number')
						{
							//user cant change this....
							//this.tdo[row][this.cdo[col]['db_field']]['data'] = row + 1;
							//but code can....
							//console.log('row number ' + this.tdo[row][this.cdo[col]['db_field']]['data']);
						}
						else if (typeof this.cdo[col]['individual_select_options'] !== 'undefined')
						{
							this.tdo[row][this.cdo[col]['db_field']]['value'] = tbody.rows[row].cells[column_counter].childNodes[0].value;
						}
						else
						{
							this.tdo[row][this.cdo[col]['db_field']]['data'] = tbody.rows[row].cells[column_counter].childNodes[0].value;
								//alert(this.table_data_array[row][col]);
						}
					}
					
					column_counter++;
				}
			}
			//now update the price_index_array
			for(col= 0;col<this.cdo.length;col++)
			{
				if (typeof this.cdo[col]['price_array_index'] !== 'undefined')
				{
					//when quantities are changed the array size needs to change as well...
					
					var quantity = myParseInt(this.tdo[this.cdo[col]['price_array_index']][row]);
					var new_quantity_array = {};
					for(var qty=0;qty<quantity;qty++)
					{
						if(this.tdo[row][this.cdo[col]['db_field']]['array_values'][qty] ==='undefined')
						{
							new_quantity_array[qty] = '';
						}
						else
						{
							new_quantity_array[qty] = 	this.tdo[row][this.cdo[col]['db_field']]['array_values'][qty];
						}
					
					}
					this.tdo[row][this.cdo[col]['db_field']]	['array_values'] = 	new_quantity_array;
					
				}	
			}
		}
		//this.updateTableDataForPost();

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
			//here we add the row properties
			for (var index in this.tdo[row]['_row'])
			{
				//console.log('property: ' + index);
				//console.log(this.tdo[row]['_row'][index]);
				eval('tbody.rows[row].' + index + '= ' + this.tdo[row]['_row'][index] + ';');
			}
			column_counter=0;
			for(col= 0;col<this.cdo.length;col++)
			{
				if(this.cdo[col]['type'] != 'hidden')
				{
					if (this.cdo[col]['type'] == 'checkbox' || this.cdo[col]['type'] == 'radio')
					{
						if(row == control_row && col == control_column)
						{
						}
						else
						{
							//if(this.table_data_array[row][col] == 1 || this.table_data_array[row][col] == '1')
							if(this.tdo[row][this.cdo[col]['db_field']]['data'] == 1 || this.tdo[row][this.cdo[col]['db_field']]['data'] == '1')
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
							
							if(typeof this.cdo[col]['individual_select_options'] !== 'undefined')
							{
								//value =  this.table_data_array[row][col]['value'];
								value =  this.tdo[row][this.cdo[col]['db_field']]['value'];

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
								for(var opt in this.tdo[row][this.cdo[col]['db_field']]['select_values'])
								{
									//load up the options
									option = document.createElement('option');
									option.value = this.tdo[row][this.cdo[col]['db_field']]['select_values'][opt];
									option.appendChild(document.createTextNode(this.tdo[row][this.cdo[col]['db_field']]['select_names'][opt]));
									element.appendChild(option);
								}
							}
							else
							{
								value =  this.tdo[row][this.cdo[col]['db_field']]['data'];
								
							}
								
							if(typeof this.cdo[col]['round'] !== 'undefined')
							{
								if(this.cdo[col]['type'] == 'innerHTML')
								{
										tbody.rows[row].cells[column_counter].innerHTML = round2(value,this.cdo[col]['round']);;
								}
								else
								{
									tbody.rows[row].cells[column_counter].childNodes[0].value = round2(value,this.cdo[col]['round']);
								}
							}
							else
							{
								//here we write the value....if we want text where does it go?
								if(this.cdo[col]['type'] == 'innerHTML' || this.cdo[col]['type'] == 'row_number')
								{
									
									//console.log (this.cdo[col]);
									tbody.rows[row].cells[column_counter].innerHTML = value;
								}
								else if (this.cdo[col]['type'] == 'link')
								{
									tbody.rows[row].cells[column_counter].innerHTML = '<a href="123.conm">'+value +'</a>';
								}
								else
								{
									//console.log (this.cdo[col]);
									tbody.rows[row].cells[column_counter].childNodes[0].value = value;
								}
							}
						}
					}
					column_counter++;
				}
				
			}
		}
		this.updateTable();
	
	
	}	
	this.getTableArrayColumnNumberFromHTMLColumnNumber = function(html_column)
	{
		column = -1;
		var table_data_array_column = 0;
		var html_column_counter = 0;
		for (i=0; i<this.cdo.length;i++)
		{
			if(this.cdo[i]['type'] != 'hidden')
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
	this.getCDOColumnNumberFromName = function(name)
	{
		column = -1;
		for (i=0; i<this.cdo.length;i++)
		{
			if (typeof this.cdo[i]['db_field'] !== 'undefined' && this.cdo[i]['db_field'] == name)
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
		for (i=0; i<this.cdo.length;i++)
		{
			if(this.cdo[i]['type'] != 'hidden')
			{
				if (this.cdo[i]['db_field'] == name)
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
				tbody.rows[row].cells[col].innerHTML = row+1;
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
	this.setRDO = function(row_prop, row_function)
	{
		this.rdo[row_prop] = row_function;	
	}
	this.setAllRowProps = function(row_prop, row_function)
	{
		//set the row property to the tdo
		//if the property exists we need to overwrite it.
		for(var i=0;i<this.tdo.length;i++)
		{
			this.tdo[i]['_row'][row_prop] = row_function;			
		}
		this.write();
	}
	this.setRowProp= function(row,row_prop, row_function)
	{
		this.tdo[row]['_row'][row_prop] = row_function;
		this.write();
	}
	this.addRowToHTMLTable = function()
	{
		var tBody = document.getElementById(this.tbody_id);
		var rowCount = tBody.rows.length;
		var row = tBody.insertRow(rowCount);
		//here we add row properties......
		
		row.id = this.tbody_id + '_row_' +rowCount;
		//console.log('this.rowprop' + this.rowprop);
		
		for (var index in this.tdo[rowCount]['_row'])
		{
			//console.log(index);
			//console.log(this.rowprop[row_prop][index]);
			eval('row.' + index + '= ' + this.tdo[rowCount]['_row'][index] + ';');
		}
		
		
		
		
		
		var col_counter = 0;
		var element;
		for (c=0; c<this.cdo.length;c++)
		{
			if (this.cdo[c]['type'] == 'hidden')
			{
			}
			else
			{
				cell = row.insertCell(col_counter);
				cell.id = "r" + rowCount + "c" + col_counter;
				col_counter++;
				if (typeof this.cdo[c]['td_tags'] !== 'undefined')
				{
					for (var index in this.cdo[c]['td_tags'])
						{
							eval('cell.' + index + '= ' + this.cdo[c]['td_tags'][index] + ';');
							
						}
				}
				
				if (this.cdo[c]['type'] == 'input')
				{
					
					element = document.createElement('input');
					element.type = 'text';
					element.name = this.cdo[c]['db_field'] + '[]';
					if (typeof this.cdo[c]['properties'] !== 'undefined')
					{
						for (var index in this.cdo[c]['properties'])
						{
							eval('element.' + index + '= ' + this.cdo[c]['properties'][index] + ';');
							
						}
					}
					cell.appendChild(element);
				}
				
				else if (this.cdo[c]['type'] == 'row_checkbox' || this.cdo[c]['type'] == 'checkbox')
				{
					element = document.createElement('input');
					element.type = 'checkbox';
					element.name = this.cdo[c]['db_field'] + '[]';
					if (typeof this.cdo[c]['properties'] !== 'undefined')
					{
						for (var index in this.cdo[c]['properties'])
						{
							eval('element.' + index + '= ' + this.cdo[c]['properties'][index] + ';');
							
						}
					}
					cell.appendChild(element);
				}
				else if (this.cdo[c]['type'] == 'radio' )
				{
					element = document.createElement('input');
					element.type = 'radio';
					element.name = this.cdo[c]['db_field'] + '[]';
					if (typeof this.cdo[c]['properties'] !== 'undefined')
					{
						for (var index in this.cdo[c]['properties'])
						{
							eval('element.' + index + '= ' + this.cdo[c]['properties'][index] + ';');
							
						}
					}
					cell.appendChild(element);
				}
				else if (this.cdo[c]['type'] == 'select')
				{
					element = document.createElement('select');
					element.name = this.cdo[c]['db_field'] + '[]';
		
					//we can have individual select options or global options
					if (typeof this.cdo[c]['individual_select_options'] !== 'undefined')
					{
						//there are unique select items in here...
						var option = document.createElement('option');
						option.value = 'NULL';
						option.appendChild(document.createTextNode("Select..."));
						element.appendChild(option);
						//the names and values are in the data object
		
		
						for (var i in this.tdo[this.cdo[c]['db_field']][rowCount]['select_values'])
						{
							option = document.createElement('option');
							option.value = this.tdo[this.cdo[c]['db_field']][rowCount]['select_values'][i];
				option.appendChild(document.createTextNode(this.tdo[this.cdo[c]['db_field']][rowCount]['select_names'][i]));
							element.appendChild(option);
						}
						
					}
					else
					{
						var option = document.createElement('option');
						option.value = 'NULL';
						option.appendChild(document.createTextNode("Select..."));
						element.appendChild(option);
						for (var i in this.cdo[c]['select_names'])
						{
							option = document.createElement('option');
							option.value = this.cdo[c]['select_values'][i];
							option.appendChild(document.createTextNode(this.cdo[c]['select_names'][i]));
							element.appendChild(option);
						}
					}
					if (typeof this.cdo[c]['properties'] !== 'undefined')
					{
						for (var index in this.cdo[c]['properties'])
						{
							eval('element.' + index + '= ' + this.cdo[c]['properties'][index] + ';');
							
						}
					}
					cell.appendChild(element);
				}
				else if (this.cdo[c]['type'] == 'tree_select')
				{
					element = document.createElement('select');
					element.name = this.cdo[c]['db_field'] + '[]';
					
					
					var option = document.createElement('option');
					option.value = 'NULL';
					option.appendChild(document.createTextNode("Select..."));
					element.appendChild(option);
					var level = 0;
					//console.log(this.cdo[c]['select_array']);
					this.addNestedSelectOptions(element, this.cdo[c]['select_array'], level);
					



					if (typeof this.cdo[c]['properties'] !== 'undefined')
					{
						for (var index in this.cdo[c]['properties'])
						{
							eval('element.' + index + '= ' + this.cdo[c]['properties'][index] + ';');
							
						}
					}
					cell.appendChild(element);
				}
				else if (this.cdo[c]['type'] == 'textContent')
				{
					// how?? cell.innerHTML( noo, we need to access childnode[0] later...
					//var cellText = document.createTextNode("cell in row "+i+", column "+j);
      				//not sure if we are using this on?\e?
      				element = document.createElement('textContent');
      				cell.appendChild(element);
				}
				else if (this.cdo[c]['type'] == 'innerHTML')
				{
					// how?? cell.innerHTML( noo, we need to access childnode[0] later...
					//var cellText = document.createTextNode("cell in row "+i+", column "+j);
      				//element = document.createElement(this.cdo[c]['element']);
      				//cell.appendChild(element);
				}
				else if (this.cdo[c]['type'] == 'date')
				{
					// how?? cell.innerHTML( noo, we need to access childnode[0] later...
					//var cellText = document.createTextNode("cell in row "+i+", column "+j);
      				//element = document.createElement(this.cdo[c]['element']);
      				//cell.appendChild(element);
				}
				else if (this.cdo[c]['type'] == 'row_number')
				{
					// how?? cell.innerHTML( noo, we need to access childnode[0] later...
					//var cellText = document.createTextNode("cell in row "+i+", column "+j);
      				//element = document.createElement(this.cdo[c]['element']);
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
		
		for (col=0; col<this.cdo.length;col++)
		{
			if (typeof this.cdo[col]['footer'] !== 'undefined')
			{
				var sum = 0.0;
				for(row=0;row<this.rowCount;row++)
				{
					sum = sum + myParseFloat(this.tdo[row][this.cdo[col]['db_field']]['data']);
				}
				document.getElementById(this.cdo[col]['footer'][0]['db_field']).value = sum;
			}
			if (typeof this.cdo[col]['total'] !== 'undefined')
			{
				
				var sum = 0.0;
				for(var row=0;row<this.tdo.length;row++)
				{
					sum = sum + myParseFloat(this.tdo[row][this.cdo[col]['db_field']]['data']);
				}
				document.getElementById(this.cdo[col]['db_field'] + '_total').value = round2(sum,this.cdo[col]['total']);
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
		if (typeof this.cdo[column]['valid_input'] !== 'undefined')
		{
			
			validInput = this.cdo[column]['valid_input'];
			checkInput2(control,validInput);
		}
	}
	
	
	
	
	this.removeAndReloadAllSelectOptions = function()
	{
		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
		for (col=0; col<this.cdo.length;col++)
		{
		 
			if (typeof this.cdo[col]['unique_select_options'] !== 'undefined')
			{
				//remove all
				for(row=0;row<rowCount;row++)
				{
					length = document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].length;
					var counter = 1;
					for(opt = 1; opt < length;opt++)
					{
						document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].remove(counter);
						//counter++;
					}
				}
				//add all
				for(row=0;row<rowCount;row++)
				{
					for (var i in this.cdo[col]['select_names'])
					{
						element = document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row];
						option = document.createElement('option');
						option.value = this.cdo[col]['select_values'][i];
						option.appendChild(document.createTextNode(this.cdo[col]['select_names'][i]));
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
		for (col=0; col<this.cdo.length;col++)
		{
			if (typeof this.cdo[col]['unique_select_options'] !== 'undefined')
			{
				// re-assign all
				for(row=0;row<rowCount;row++)
				{
					if(this.tdo[row][this.cdo[col]['db_field']]['data'] == '')
					{
						document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].value = 'NULL';
					}
	
					else
					{
						document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].value = this.tdo[row][this.cdo[col]['db_field']]['data'];
	
					}
				}
				
			}
		}
	
	}
	this.reloadSelectOptions = function()
	{
		var tbody = document.getElementById(this.tbody_id);
		var rowCount = tbody.rows.length;
	
		for (col=0; col<this.cdo.length;col++)
		{
			if (typeof this.cdo[col]['unique_select_options'] !== 'undefined')
			{
				//remove all but the selected value
				for(row=0;row<rowCount;row++)
				{
					length = document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].length;
					var counter = 1;
					for(opt = 1; opt < length;opt++)
					{
						if(document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].options[counter].value == this.table_data_array[row][col])
						{
							counter++;
						}
						else
						{
							document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].remove(counter);
						//counter++;
						}
					}
				}
				for(row=0;row<rowCount;row++)
				{
					for (var i in this.cdo[col]['select_values'])
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
								if(this.cdo[col]['select_values'][i] == this.table_data_array[column_def[col]['db_field']][k])
								{
									bln_found = true;
								}
							}
						}
						//add the value if it is not used
						if(!bln_found && this.cdo[col]['select_values'][i] != this.tdo[column_def[col]['db_field']][row])
						{
							//add
							element = document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row];
							option = document.createElement('option');
							option.value = this.cdo[col]['select_values'][i];
							option.appendChild(document.createTextNode(this.cdo[col]['select_names'][i]));
							element.appendChild(option);
						//element.add(option, i+1);
							
						}
						
					}
				}
				// re-assign all
				for(row=0;row<rowCount;row++)
				{
					document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].value = this.table_data_array[column_def[col]['db_field']][row];
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
		for (col=0; col<this.cdo.length;col++)
		{
			if (typeof this.cdo[col]['unique_select_options'] !== 'undefined')
			{
				//ok this column should only have unique values in the list
				for(row=0;row<rowCount;row++)
				{
					for(k=0;k<this.rowCount;k++)
					{
						for (var i in this.cdo[col]['select_values'])
						{
							if (row!=k)
							{
								if(this.cdo[col]['select_values'][i] == this.tdo[this.cdo[col]['db_field']][k])
								{
									//there is a null value to ignore, that is index 0, so add 1 to all indexes to remove..
									//alert('remove index: ' + (parseInt(i)+1) + ' value ' + this.cdo[col]['select_values'][i] + ' from row ' + parseInt(row + 1) + ' value ' + this.table_data_array[k][col]);
									//because each list has destroyed index values check if the values match, if so remove it
									length = document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].length;
									var counter = 1;
									for(opt = 1; opt < length;opt++)
									{
										if (document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].options[counter].value == this.tdo[this.cdo[col]['db_field']][k])
										{
											//alert('remove option index: ' + counter + ' which has the value of ' + document.getElementsByName(this.cdo[col]['db_field']+ '_r' + row)[0].options[counter].value + ' From row ' + parseInt(row+1));
											document.getElementsByName(this.cdo[col]['db_field']+ '[]')[row].remove(counter);
	
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
				element.name = this.id + '_tdo';
				element.value = JSON.stringify(this.tdo);;
				document.getElementById(form_id).appendChild(element);
	}
	this.ajaxPOST = function(ajax_url, other_post_values)
	{
		//basically all i ever want to do is send the table data to the server
		//maybe there are some other things we can send as well
	}
	this.prepareDynamicTableForPost = function()
	{
		this.copyHTMLTableDataToObject();
		this.enableAllRows();
		//validateMYSQLInsertForm();
		return this.validateDynamicTableObject();
	} 
	this.getPostData = function()
	{
		//console.log(this.tdo);
		var postData = [];
		//postData['tdo'] = JSON.stringify(this.tdo);
		for(var r =0;r<this.tdo.length;r++)
		{
			postData[r] = {};
			for (var i=0; i<this.cdo.length;i++)
			{
				if(typeof this.cdo[i]['POST'] !== 'undefined' && this.cdo[i]['POST'] == 'no')
				{
					//post no
				}
				else
				{
	
					//postData[r][this.cdo[i]['db_field']] = {};
					//postData[r][this.cdo[i]['db_field']]['data'] = this.tdo[r][this.cdo[i]['db_field']]['data'];
					//console.log('db ' + this.cdo[i]['db_field'] + ' = ' + this.tdo[r][this.cdo[i]['db_field']]['data']);
					postData[r][this.cdo[i]['db_field']] = this.tdo[r][this.cdo[i]['db_field']]['data'];
				}
			}
		}
		return postData;
	}	
	this.validateDynamicTableObject = function ()
	{
		//this should be the same function as above, but check each row....
			errors = '';
	
		for (i=0; i<this.cdo.length;i++)
		{
			if (typeof this.cdo[i]['db_field'] !== 'undefined')
			{
				if (typeof this.cdo[i]['validate'] !== 'undefined')
				{
					// go through each row
					var elements = document.getElementsByName(this.cdo[i]['db_field']+'[]');
					for(el=0;el<elements.length;el++)
					{
						if (typeof this.cdo[i]['validate']['not_blank_or_zero_or_false_or_null'] !== 'undefined')
						{
							if(elements[el].value == '' ||
							round2(elements[el].value,0) == 0 || elements[el].value == 'false' || elements[el].value == 'NULL')
							{
								errors += 'Bad Value For ' +this.cdo[i]['caption'] + ' Row ' + (el+1) + newline();
							}
						}
						else if  (typeof this.cdo[i]['validate']['acceptable_values'] !== 'undefined')
						{
							acceptable_values = this.cdo[i]['validate']['acceptable_values'][0];
							if(acceptable_value == 'number')
							{
								if (isNaN(elements[el].value))
								{
									errors += this.cdo[i]['db_field'] +' needs to be a value.' + newline();
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
			for (i=0; i<this.cdo.length;i++)
			{
				if(this.cdo[i]['type'] != 'hidden')
				{
					//select the type
					this.enableHTMLCell(this.cdo[i]['type'], row, col_counter);
					col_counter++;
				}
			}
		}
	
		
	}
	this.disableHTMLRow = function(row)
	{
		//go through each column and disable it
		var col_counter = 0;
		for (i=0; i<this.cdo.length;i++)
		{
			if(this.cdo[i]['type'] != 'hidden')
			{
				//select the type
				this.disableHTMLCell(this.cdo[i]['type'], row, col_counter);
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
		this.disableHTMLCell(this.cdo[this.getCDOColumnNumberFromName(name)]['type'],row,col);
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
	for(var col=0;col<this.cdo.length;col++)
		{
			this.tdo[this.cdo[col]['db_field']] = {};
		}
	*/

	
}
function mySQLTable_V1(table_id, column_definition, json_table_contents)
{
	/*

	//when adding a new element the following functions need to be modified
	create the table
	disable or enable the table
	get data from table
	write data to table
	validate table

	currently the element is named by db_field.... we could change it to  el_name....


	some table options.....
	mostly we want to show a read only table (disabled), then pop up a writable table via an edit button.
	Other times we want to show just the edit table?




	*/

	this.tdo = [];
	this.view_table_name = '_view';
	this.edit_table_name = '_edit';
	
	this.table_id = table_id;
	this.cdo = column_definition;
	this.json = json_table_contents;

	this.addViewEditTable = function(div_id)
	{	
		//creates the following.....
		// div table_nameview
			//the view table disabled
			//edit button
		//poup dialog: div  table_nameedit
		//loading image table_name_loading_image_div

		
		var view_div=document.createElement('div');
		view_div.id = this.table_id + this.view_table_name;		
		var view_tbl = this.createTable(this.view_table_name);
		var view_table_div=document.createElement('div');
		view_table_div.id = this.table_id + this.view_table_name+'_table_div';
		view_table_div.appendChild(view_tbl);
		view_div.appendChild(view_table_div);		
		$(view_div).append('<input type="button" id = "'+this.table_id+'_edit_button" class = "button" value="Edit" onclick="'+this.table_id+'.editForm()">');
		
		
		var edit_div=document.createElement('div');
		edit_div.id = this.table_id + this.edit_table_name;		
		var edit_tbl = this.createTable(this.edit_table_name);
		var edit_table_div=document.createElement('div');
		edit_table_div.id = this.table_id + this.edit_table_name+'_table_div';
		edit_table_div.appendChild(edit_tbl);
		edit_div.appendChild(edit_table_div);	
		
		//LOADING IMAGE appends to the modal form....
		var loading_image_div=document.createElement('div');
		loading_image_div.id = this.table_id + '_loading_image_div';
		var img = document.createElement("img");
		img.src =  POS_ENGINE_URL + '/includes/images/ajax_loader_gray.gif';
		loading_image_div.className = 'mySQLTable_loading_image';
		//img.id = 'mySQLTable_loading_image';
		loading_image_div.appendChild(img);		
		edit_div.appendChild(loading_image_div);
		
		//this does not work here and needs to go elsewhere
		/*		
		var me = this;
		$(edit_div).dialog(
			{
				autoOpen: false,
				height: 400,
				width: 600,
				resizable: false,
				modal: true,
				buttons: 
				{
					"Submit": function() 
					{
						me.submitForm();
					},
					Cancel: function() 
					{
						$( this ).dialog( "close" );
					}
				},
				close: function() 
				{
				},

			});
		$(edit_div).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) {
			  
				 alert('enter');
			}
		});
		*/
		
		//return main_div.innerHTML;
		$('#' + div_id).append(view_div); 
		$('#' + div_id).append(edit_div); 
		$('#' + this.table_id+'_loading_image_div').hide();
		this.WriteDataToTable(this.json, this.view_table_name);
		this.disableTable(true, this.view_table_name);

		
	


		
		
		
	}
	this.addEditTableToDiv = function(div_id)
	{
		//creates the following.....
		// div table_nameedit
		//loading image table_name_loading_image_div
		
		var edit_div=document.createElement('div');
		edit_div.id = this.table_id + this.edit_table_name;		
		var edit_tbl = this.createTable(this.edit_table_name);
		var edit_table_div=document.createElement('div');
		edit_table_div.id = this.table_id + this.edit_table_name+'_table_div';
		edit_table_div.appendChild(edit_tbl);
		edit_div.appendChild(edit_table_div);
		
		//LOADING IMAGE appends to the modal form....
		var loading_image_div=document.createElement('div');
		loading_image_div.id = this.table_id + '_loading_image_div';
		var img = document.createElement("img");
		img.src =  POS_ENGINE_URL + '/includes/images/ajax_loader_gray.gif';
		loading_image_div.className = 'mySQLTable_loading_image';
		//img.id = 'mySQLTable_loading_image';
		loading_image_div.appendChild(img);		
		edit_div.appendChild(loading_image_div);
			
		$('#' + div_id).append(edit_div); 
		$('#' + this.table_id+'_loading_image_div').hide();
		this.WriteDataToTable(this.json, this.edit_table_name);

	}
	this.init = function()
	{
		//alert('initing');
		var me = this;
		$('#' +this.table_id + this.edit_table_name).dialog(
			{
				autoOpen: false,
				height: 400,
				width: 600,
				resizable: false,
				modal: true,
				buttons: 
				{
					"Submit": function() 
					{
						me.submitForm();
					},
					Cancel: function() 
					{
						$( this ).dialog( "close" );
					}
				},
				close: function() 
				{
				},

			});
		$('#' +this.table_id + this.edit_table_name).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) {
			  
				 alert('enter');
			}
		});
	}
	this.createTable = function(div_id)
	{
		//table
		var tbl=document.createElement('table');
		tbl.id = this.table_id + '_table' + div_id;
		tbl.className = 'mysql_js_table';
		//$('#' + tbl.id).addClass('search_table');
		
		//MAKE THE TABLE HERE *************************************************
		for(var i=0;i<this.cdo.length;i++)
		{
			var tr=tbl.insertRow();
			caption = '';
			if(typeof this.cdo[i]['caption'] != 'undefined')
			{
				caption = this.cdo[i]['caption'];
			}
			else
			{	
				caption = this.cdo[i]['db_field'];
			}
			//var th=tr.insertCell();
			var th = document.createElement('th');					
			th.innerHTML = caption;	
			//th.style.width = th_width;
			tr.appendChild(th);
			
			//cell = tr.insertCell(col_counter);
			
			//name is table_div_db_field
			var name = this.table_id + div_id + '_' + this.cdo[i]['db_field'];
			var cell = createTableCell(name, this.cdo[i])	
			tr.appendChild(cell);
		
		}
		return tbl;
	}
	this.WriteDataToTable = function(data, table)
	{
		for(var i=0;i<this.cdo.length;i++)
		{	
			
			if(typeof data[this.cdo[i]['db_field']] === 'undefined')
			{
				if(typeof this.cdo[i]['default_value'] !== 'undefined')
				{
					value = this.cdo[i]['default_value'];
				}
				else
				{
					value = '';
				}
			}
			else
			{
				value = data[this.cdo[i]['db_field']];
			}
			//console.log('value ' + value + ' ' + this.cdo[i]['db_field']);
			if(typeof value !== 'undefined')
			{
				var element = document.getElementById(this.table_id + table +  '_' + this.cdo[i]['db_field']);
				if(this.cdo[i]['type'] == 'date')
				{
					element.value = value;

				}
				else if(this.cdo[i]['type'] == 'checkbox')
				{
					if(value == 1)
					{
						element.checked = true;
					}
					else
					{
						element.checked = false;
					}
				}
				else if (this.cdo[i]['type'] == 'select')
				{
					element.value = value;
				}
				else if (this.cdo[i]['type'] == 'tree_select')
				{
					element.value = value;
				}				
				else if (this.cdo[i]['type'] == 'input')
				{
					element.value = value;
				
				}
				else if (this.cdo[i]['type'] == 'textarea')
				{
					element.value = value;
				
				}
				else if (this.cdo[i]['type'] == 'td')
				{
				
					element.innerHTML = value;
				
				}
				else
				{
					alert(this.cdo[i]['type'] + ' have not coded that.....');
				}
			}
		}
	}
	this.EraseTableData = function(table)
	{
		for(var i=0;i<this.cdo.length;i++)
		{	
			
			value = '';
			if(typeof value !== 'undefined')
			{
				var element = document.getElementById(this.table_id + table +  '_' + this.cdo[i]['db_field']);
				if(this.cdo[i]['type'] == 'date')
				{
					element.value = value;

				}
				else if(this.cdo[i]['type'] == 'checkbox')
				{
					if(value == 1)
					{
						element.checked = true;
					}
					else
					{
						element.checked = false;
					}
				}
				else if (this.cdo[i]['type'] == 'select')
				{
					element.value = value;
				}
				else if (this.cdo[i]['type'] == 'tree_select')
				{
					element.value = value;
				}				
				else if (this.cdo[i]['type'] == 'input')
				{
					element.value = value;
				
				}
				else if (this.cdo[i]['type'] == 'textarea')
				{
					element.value = value;
				
				}
				else if (this.cdo[i]['type'] == 'td')
				{
				
					element.innerHTML = value;
				
				}
				else
				{
					alert(this.cdo[i]['type'] + ' have not coded that.....');
				}
			}
		}
	}
	this.disableTable = function(disabled, table)
	{
		
		for(var i=0;i<this.cdo.length;i++)
		{
			//note the [0] gets the dom element....
			var element = document.getElementById(this.table_id + table + '_' + this.cdo[i]['db_field']);
			if(this.cdo[i]['type'] == 'date')
			{
				$('#' + this.table_id + '_' +  this.cdo[i]['db_field']).datepicker( "option", "disabled", disabled );
			}
			else if(this.cdo[i]['type'] == 'checkbox')
			{
				element.disabled = disabled;
			}
			else if (this.cdo[i]['type'] == 'select')
			{
				element.disabled = disabled;			}
			else if (this.cdo[i]['type'] == 'tree_select')
			{
				element.disabled = disabled;
			}				
			else if (this.cdo[i]['type'] == 'input')
			{
				element.disabled = disabled;
			}
			else if (this.cdo[i]['type'] == 'textarea')
			{
				element.disabled = disabled;
			}
			else
			{
			}
		}
		
		
		
	}
	this.editForm = function()
	{
		$('#' + this.table_id+this.edit_table_name+'_table_div').show();
		$( "#" +this.table_id+this.edit_table_name).dialog( "open" );
		this.WriteDataToTable(this.json, this.edit_table_name);
		//$('#' + this.table_id+'_edit_button').hide();
		//$('#' + this.table_id+'_submit_button').show();
		//$('#' + this.table_id+'_cancel_button').show();
		
		//set focus to the first element
		for(var i=0;i<this.cdo.length;i++)
		{
			if(this.cdo[i]['type'] == 'td')
			{
			}
			else
			{
				$('#' + this.table_id+ this.edit_table_name + '_'  + this.cdo[i]['db_field']).focus().select();
				return;
			}

		}
		
		//postRenderTableFunctions(this.table_id + this.edit_table_name, this.cdo);
		
	}
	this.cancelForm = function()
	{
		this.EraseTableData(this.edit_table_name);
		
	}
	this.getPostData = function()
	{
		//post data comes from edit
		var post_data = {};
		post_data['ajax_request'] = this.searchFlag;
		post_data['table_name'] = this.table_id + this.edit_table_name;
		
		//GET THE DATA
		for(var i=0;i<this.cdo.length;i++)
		{	
			
			if(typeof this.cdo[i]['db_field'] !== 'undefined')
			{
				element_id = this.table_id + this.edit_table_name +'_' +  this.cdo[i]['db_field'];
				var element = document.getElementById(element_id);
				if(this.cdo[i]['type'] == 'date')
				{
					post_data[element_id] = element.value;

				}
				else if(this.cdo[i]['type'] == 'checkbox')
				{
					if(element.checked == true)
					{
						post_data[element_id] =  1;
					}
					else
					{
						post_data[element_id] =  0;
					}
				}
				else if (this.cdo[i]['type'] == 'select')
				{
					post_data[element_id] = trim(element.value);
				}
				else if (this.cdo[i]['type'] == 'tree_select')
				{
					post_data[element_id] = element.value;
				}				
				else if (this.cdo[i]['type'] == 'input')
				{
					post_data[element_id] = trim(element.value);
				
				}
				else if (this.cdo[i]['type'] == 'textarea')
				{
					post_data[element_id] = trim(element.value);
				
				}
				else if (this.cdo[i]['type'] == 'td')
				{
				
					// nothing to post? 
					post_data[element_id] = trim(element.innerHTML);
				
				}
				else
				{
					alert(this.cdo[i]['type'] + ' have not coded that.....');
				}
			}
		
		}
		return post_data;
	}
	this.submitForm = function()
	{
		//check for ajaxHandler
		if(typeof this.ajaxHandler === 'undefined')
		{
			alert("You forgot to set the ajax handler in php: table_name.ajaxHandler=\'ajax_handler.php\'");
			return false;
		}
		if(typeof this.searchFlag === 'undefined')
		{
			alert("You forgot to set the search handler for ajax_request in php: table_name.searchHandler=\'BLAAAA'");
			return false;
		}
		
		var post_data = this.getPostData();
		//now ajax....
		
		//$('#' + this.table_id).html('');
		//$('#' + this.table_id+'_submit_div').hide();
		
		$('#' + this.table_id+ this.edit_table_name +'_table_div').hide();
		$('#' + this.table_id+'_loading_image_div').show();
		
		
		this.EraseTableData(this.edit_table_name);
		//console.log('post data');
		//console.log(post_data);
		var me = this;
		$.post(this.ajaxHandler, post_data,
		function(response) 
		{
			var parsed_data = tryParseJSON(response);
			if(parsed_data)
			{
				if(parsed_data['success'])
				{
					me.WriteDataToTable(parsed_data['mysql_return_data'],me.view_table_name);
					me.json = parsed_data['mysql_return_data'];
					$('#' + me.table_id+'_loading_image_div').hide();
					//$('#' + me.table_id+'_table_div').show();
					//$('#' + me.table_id+'_submit_div').show();
					//$('#' + me.table_id+'_edit_button').show();
					//$('#' + me.table_id+'_submit_button').hide();
					//$('#' + me.table_id+'_cancel_button').hide();
					
					$( '#' + me.table_id+ me.edit_table_name ).dialog( "close" );
					
					if(me.postSubmitFunction && typeof me.postSubmitFunction === 'function')
					{
						me.postSubmitFunction();
					}
					
					
				}
			}
			else
			{
				//there was a system error
				
				console.log('error response from the server');
				console.log(response);
				$('#' + me.table_id+'_loading_image_div').hide();
				//$('#' + me.table_id+'edit_table_div').hide();
				
				
				alert('Looks like there was an uncaught data error on the server, possibly an inocorrect value, duplicate name, etc. An email has been sent to the admin. In the mean time the data has not been saved and the forms have been disabled. Try the operation again.');
				$( '#' + me.table_id+ me.edit_table_name ).dialog( "close" );
			}
		});
		
	}	
}






