<?php
$page_title = 'Point of Sale';
$access_type = 'READ';
//binder is in the include....why?
$binder_name = 'POS';
require_once('retail_sales_invoice_functions.php');
require_once('POS_V1.inc.php');
$pos_terminal_id = terminalCheck();
$html = '';
$pos_javascript_version = 'POS_V1_2015.06.11.js';
$css_version = 'POS_V1.2015.05.04.css';
$html .=  '<script src="'.$pos_javascript_version.'"></script>'.newline();
$html .= '<link type="text/css" href="'.$css_version.'" rel="Stylesheet"/>'.newline();
$html .= '<script>var pos_terminal_id = '.$pos_terminal_id.';</script>';
$html .=  '<script src="'.DYNAMIC_TABLE_OBJECT_V3.'"></script>'.newline();


include(HEADER_FILE);
echo $html;
include(FOOTER_FILE);

if(getTerminalStatus($pos_terminal_id) == 'OPEN')	
{
	//	#################### INVOICE FUNCTIONS ##############################
	
	?>
	<script>
	var login_enabled = false;
	$("#content").append('<div id="invoice_functions">');
	$("#invoice_functions").append('<input class = "button" type="button" style="width:300px" id="btnNewInvoice" value="New Invoice" onclick="newInvoice();">');
	</script><?

	//what else - edit an older invoice details? yes maybe not here thoughh....
	
	if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_allow_edit_invoice_details'))
	{
		$html .= '$("#invoice_functions").append(\'<input class = "btnEditInvoice" type="button" style="width:300px" id="btnEditInvoice" value="Edit Invoice" onclick="EditIncoive()">)\';';
	}
	
	//	#################### TERMINAL FUNCTIONS ##############################
	if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_open_close_terminal'))
	{
		//closing the terminal involves counting the cash box
		//counting checks
		//batching the CC's
		$html.= '$("#invoice_functions").append(\'<div id="terminals">\');';
		$html .= '$("#terminals").append(\'<input class = "btnCloseTerminal" type="button" style="width:300px" id="btnCloseTerminal" value="Close Terminal" onclick="closeTermainal()">)\';';
		
	}
	
	//	#################### CUSTOMER SEARCH FUNCTIONS ##############################	
	
	$customer_table_name = 'customer_table';
	$customer_col_def = createCustomerInvoiceSearchColDef($customer_table_name);
	$cust_contents = getSetCustomerSearchResults($customer_table_name, $customer_col_def,'posv1_saved_customer_search');

	echo createLoginModalForm();
	
	//we have to turn this into a function......
	//we enter, then we load data, then we need to go through the table and disable buttons after the search
	//after the search results call a function.... 
	$maxReturnDays = getSetting('maxReturnDays');

	?>
	
	<script> // this is the customer search functionality
	
	var customer_col_def = <? echo json_encode($customer_col_def) ?>;
	var cust_contents = <? echo json_encode($cust_contents['data']) ?>;
	$("#content").append('<h2>This table is for customer searching</h2>');
	$("#content").append('<div id="customer_search">');
	$('#content').append('<div id="customer_search_results">');
	//we set up the object with the table name, the column definition, and the beginning table contents..
	var customer_table = new dynamic_table_object_v3('customer_table', customer_col_def, cust_contents);
	//set a function to execute after the search is submitted
	customer_table.postSearchResultsFunction = function()
	{
		var maxReturnDays = <?echo getSetting('maxReturnDays');?>;
		var timestamp = new Date().getTime() - (maxReturnDays * 24 * 60 * 60 * 1000);

		for(var row=0;row<customer_table.tdo.length;row++)
		{
			var myDate=customer_table.tdo[row]['invoice_date']['data'];
			myDate=myDate.split("-");
			var newDate=myDate[1]+"/"+myDate[2]+"/"+myDate[0];
			var invoiceDatetime = new Date(newDate).getTime();
			//console.log('invoiceDatetime' + invoiceDatetime + 'timestamp' + timestamp);
			if( invoiceDatetime < timestamp)
			{
				$('#pos_sales_invoice_id_for_return_sr' + row).prop("disabled",true);
			}
			else
			{
			}
			var customer = customer_table.tdo[row]['pos_customer_id_for_invoice']['data'];
			//console.log('customer: ' + customer);
			if (customer == null)
			{
				$('#customer_table_pos_customer_id_for_invoice_sr' + row).hide();
			}
			else
			{
				$('#customer_table_pos_customer_id_for_invoice_sr' + row).html('New Invoice For ' + customer_table.tdo[row]['full_name']['data']);
			}
		}
		var first_name = $('#customer_table_first_name_search').val();
		var last_name = $('#customer_table_last_name_search').val();
		$('#customer_table_search_results').append('<input class = "button" type="button" style="width:300px" id="newCustomerInvoiceFromSearch" value="Create Invoice and New Customer ' + first_name + ' ' + last_name + '" onclick="newCustomerInvoice()">');
		
	};
	//create a search table and attach it to the page. The search table needs the stored search
	customer_table.addSearchTableToDiv('customer_search', <? echo json_encode($cust_contents['get_post_session_params']); ?>);
	//the search table needs an ajax handler
	customer_table.ajaxHandler = 'POS_V1.ajax.php';
	//search table needs a seach handler
	customer_table.searchFlag = 'CUSTOMER_SEARCH';
	customer_table.resetFlag = 'CUSTOMER_SEARCH_RESET';

	
	customer_table.rdo['onclick'] = function(){//includes thead rows and tbody rows
												//console.log(this.rowIndex);
												//go jquery, returns tbody index....
												//console.log($(this).index())
												};
	$('#customer_table_customer_info_search').focus();
	$('#customer_table_customer_info_search').select();
	//now.... we need to load any session stored search....
	//the saved_session_search should just drop out.... as table_name_saved_session_search
	
	
	
	</script>
	<?
	$store_invoice_table_name = 'store_invoice_table';
	$store_invoice_col_def = createStoreInvoiceSearchColDef($store_invoice_table_name);
	$invoice_contents = getSetCustomerSearchResults($store_invoice_table_name, $store_invoice_col_def,'posv1_saved_store_invoice_search');
	
	?>
	
	<script>
	
	var store_invoice_col_def = <? echo json_encode($store_invoice_col_def) ?>;
	var invoice_contents = <? echo json_encode($invoice_contents['data']) ?>;
	$("#content").append('<h2>This table is for daily total searching... might be limited by store/terminal/user access?</h2>');
	$("#content").append('<div id="store_invoice_search">');
	$('#content').append('<div id="store_invoice_search_results">');
	//we set up the object with the table name, the column definition, and the beginning table contents..
	var store_invoice_table = new dynamic_table_object_v3('store_invoice_table', store_invoice_col_def, invoice_contents);
	//set a function to execute after the search is submitted
	store_invoice_table.postSearchResultsFunction = function()
	{
		
	};
	//create a search table and attach it to the page. The search table needs the stored search
	store_invoice_table.addSearchTableToDiv('store_invoice_search', <? echo json_encode($invoice_contents['get_post_session_params']); ?>);
	//the search table needs an ajax handler
	store_invoice_table.ajaxHandler = 'POS_V1.ajax.php';
	store_invoice_table.searchFlag = 'INVOICE_SEARCH';
	store_invoice_table.resetFlag = 'INVOICE_SEARCH_RESET';
	
	

	
	
		
	
	
	
	
	</script>
	<?
	

	
		
	//	#################### TERMINAL vs store INVOICE search ##############################	
	// 1 col def for search and table? yes
	// ajax the data
	// display the table
	// ## daily invoices - with totals for some people limits for others
	// daily text report - to who?
	// margins per ticket for those interested
	// store search in $_SESSION['POS_cust_search']
	// gross tax date time customer last/first
	// store search in $_SESSION['POS_inv_search']
	// ats margin et
	
	//	#################### STORE vs terminal INVOICE search ##############################	
	// who has access to this?
	// how is it different to above?
	// terminals need access to other invoices regardless.
	// terminal needs no access?
	
	
	
	//	#################### PRODUCT/INVENTORY/ORDER SEARCH FUNCTIONS ##############################
	//	#################### GIFT CARD SEARCH FUNCTIONS ##############################
	
		
	//	#################### TASK LISTS ##############################	
	// cycle through defined tasks?
	// A task list is always bulleted. it assigns a task to an empolyee. manager gives 5 star review on task
	//pos_tasks
	
		//task name
		//task id
	//pos_task_contents
		//task_content_id
		//task_id
		//contents (Array?)
	//pos_task_performance
		//user or employee_id
		//datetime assigned
		//datetime complete
		//task_id
		//complete not complete
		//review
	

	

}
else if (getTerminalStatus($pos_terminal_id) == 'CLOSED')
{
	//open terminal
	//count cash register
		//enter this into register count different database.
		//adding money to cash register...
			//transfer cnbank to cash account
		//loosing money. expense cash short, pay from cash account
	$html.= '<div id="terminals">';
	if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_open_close_terminal'))
	{
		$html .= '<input class = "btnOpenTerminal" type="button" style="width:300px" id="btnOpenTerminal" value="Open Terminal" onclick="openTerminal()"/>';
	}
	else
	{
		$html .= 'Contant Manager to Open This Terminal';
	}
	$html .='</div>';
	
}
else if(getTerminalStatus($pos_terminal_id) == 'LOCKED')
{
	// not sure.....
	$html .= 'Contant Manager to Open This Terminal';
}



function createCashDrawerModalForm()
{
	$html = '
<div id="frmCash" title="Set Up Cash Drawer">
</div>
';
}
function createLoginModalForm()
{
	$html = '
	<div id="login-dialog-form" title="login">
	<p>Enter User/Password</p>
	<form>
	<table>
	<tr>
	<th>USER NAME</th><td><input type="text" name="user" id="user" class="text ui-widget-content ui-corner-all" /></td>
	</tr>
	<tr>
	<th>PASSWORD</th><td><input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" /></td>
	</tr>
	</table>

	</form>
	</div>
	';
	return $html;
}
function getTerminalStatus($pos_terminal_id)
{
	$status = getSingleValueSQL("SELECT status from pos_terminals WHERE pos_terminal_id=$pos_terminal_id");
	return $status;
}
?>
