function loadRoomHtml(room_name)
{
	//user has clicked a room button.... 
	//first we need to update the log
	//second we need to get the room html
		post_data = {};
		post_data['user_data'] = user_data;
		console.log(this.php_sql_processing_file);
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