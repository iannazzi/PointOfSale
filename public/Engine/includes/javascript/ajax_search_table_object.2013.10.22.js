//This object will create a search form and a table that will update through ajax.....

//the object.....
function search_table_object(table_name, search_fields, php_sql_processing_file)
{
	
	//initialize the object below all functions
	console.log(table_name);
	console.log(search_fields);
	console.log(php_sql_processing_file);
	this.search_fields = search_fields;
	this.table_name = table_name;
	console.log(this.table_name);
	this.php_sql_processing_file = php_sql_processing_file;
	this.init = function()
	{
	}

	this.ajax_search_form = function()
	{
		//here we want to collect the search_table def, the user entered data, the sql and the tmp_select_sql statement, send that to the server, and crunch. 
		//aleternatively send the name and the data to the server, and crunch in a switch.... which centralizes many commands... 
		//or crunch in a separate file which should be pretty easy
		//build post data
		user_data = {};
		for(var i = 0; i < this.search_fields.length; i++)
		{
			console.log(this.search_fields[i]['db_field']);
			try
			{
			user_data[this.search_fields[i]['db_field']] = document.getElementById(this.search_fields[i]['db_field']).value;
			}
			catch(err)
			{
				alert('missing element: ' +  this.search_fields[i]['db_field']);
			}
		}
		
		post_data = {};
		post_data['user_data'] = user_data;
		console.log(this.php_sql_processing_file);
		//ajax will loose the table name, needs to be a local variable...
		var tmp_table_name = this.table_name;
		$.post(this.php_sql_processing_file, post_data,
		function(response) {
			//alert(response);
			document.getElementById(tmp_table_name + '_search_results_div').innerHTML = response;
			//the following is the url update..... add it to the url
			//alert(jQuery.param(post_data));
			//updatye the url
			//window.history.pushState("object or string", "Title", "/new-url");
			//jquery?
			//console.log($(tmp_table_name + '_search_results_div').eq(0).html);
			//$(tmp_table_name + "_search_results_div").eq(0).html(response);
			/*
			
			$data = array(
   			 1,
  			  4,
  			  'a' => 'b',
   			 'c' => 'd'
				);
			$query = http_build_query(array('aParam' => $data));


var array = { "foo":"bar", "baz":"boom", "php":"hypertext processor" };
var str = jQuery.param(array);
alert(str);
			*/
			
	   });
	
	
	}
	this.reset_ajax_search_form = function()
	{
		//For each form element set the value to default
		for(var i = 0; i < this.search_fields.length; i++)
		{
			if (document.getElementById(this.search_fields[i]['type']) == 'input')
			{
				document.getElementById(this.search_fields[i]['db_field']).value == '';
			}
			else if (document.getElementById(this.search_fields[i]['type']) == 'select')
			{
				document.getElementById(this.search_fields[i]['db_field']).value == this.search_fields[i]['value'];
			}
			else
			{
				document.getElementById(this.search_fields[i]['db_field']).value == '';
			}
		
		}
		//now kill the table
		document.getElementById(this.table_name + '_search_results_div').innerHTML = "";
		//update the url 
		//window.history.pushState("object or string", "Title", "/new-url");
				
	}
}