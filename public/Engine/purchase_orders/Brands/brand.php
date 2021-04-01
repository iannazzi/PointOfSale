<?

//error handling: we are generating an error, but we cannot get the error because the error_handler is bombing....

$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Create MFG Brand';
require_once('../po_functions.php');
require_once('brand.inc.php');

$javascript_version = 'brand.2015.08.03.js';

$html = '';
$html .=  '<script src="'.$javascript_version.'"></script>'.newline();
$html .=  '<script src="'.JTABLE_V1.'"></script>'.newline();


$type = grabType();
if($type == 'add')
{
	//there is no mfg data....
	$pos_manufacturer_id = 'false';
	$mfg_data = array("pos_manufacturer_id" => 'TBD');
	$brand_list_data = array();
	$account_link = false;
	
}
else
{
	//without a manufacturer go straight to edit with nothing else
	
	//with manufacturer
	//display mfg table
	//display account link
	//display brand table
	
	//is there a manufacturer id
	if(isset($_GET['pos_manufacturer_id']))
	{
		$pos_manufacturer_id = scrubInput($_GET['pos_manufacturer_id']);
		$mfg_data = getmySQLTableData('pos_manufacturers', array('pos_manufacturer_id' => $pos_manufacturer_id), createManufacturerTableDef());
		
		
	}
	else if(isset($_POST['pos_manufacturer_id']))
	{
		$pos_manufacturer_id = scrubInput($_POST['pos_manufacturer_id']);
		$mfg_data = getmySQLTableData('pos_manufacturers', array('pos_manufacturer_id' => $pos_manufacturer_id), createManufacturerTableDef());
	}
	else
	{
		$pos_manufacturer_id = 'false';
		$mfg_data = array("pos_manufacturer_id" => 'TBD');
		$type = 'add';
		$brand_list_data = array();
		$account_link = false;
	}
	
	$account_link = getMfgAccountLink($pos_manufacturer_id);
	$brand_list_data = getBrandList($pos_manufacturer_id);
}

//create all this stuff before displaying header/footer...
$mfg_table_def = createManufacturerTableDef();
$brand_table_def = createBrandTableDEf();
$brand_list_table_def = createBrandListTableDef();
$account_table_def = createACcountTableDef();
/*$accounts = getFieldRowSQL("SELECT pos_accounts.pos_account_id, pos_accounts.account_number, pos_accounts.company FROM pos_accounts 
	LEFT JOIN pos_account_type
	ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
	WHERE pos_accounts.active=1 AND (pos_account_type.account_type_name = 'Inventory Account')
	ORDER BY pos_accounts.company ASC");*/

include(HEADER_FILE);
echo $html;

//trigger_error();

//if a PHP error happens below this line it is going to blow balls.....
// the compile error will show the page but drop the error to the error log
// a run time error not sure where that is going...
?>	
<script>





//DATA - maybe this should be done before rendering the html?
var type = <? echo json_encode($type); ?>;
var pos_manufacturer_id = <? echo $pos_manufacturer_id; ?>;
var mfg_tbl_def = <? echo json_encode($mfg_table_def) ;?>;
var mfg_data = <? echo json_encode($mfg_data); ?>;

var account_link = <? echo json_encode($account_link); ?>;
var act_table_def = <? echo json_encode($account_table_def); ?>;
var account_select_table_def = <? echo json_encode(createAccountSelectTAbleDef()); ?>;

var brand_table_def = <? echo json_encode($brand_table_def); ?>;
var brand_list_data = <? echo json_encode($brand_list_data); ?>;
var brand_list_table_def = <? echo json_encode($brand_list_table_def); ?>;



//PAGE SETUP - Main Divs



$("#content").append('<div id="mfg_instructions">');
$("#content").append('<div id="manufacturer">');
$("#content").append('<div id="brand_instructions">');
$("#content").append('<div id="brand_list">');
$("#content").append('<div id="account_instructions">');
$("#content").append('<div id="account_link">');
$("#brand_list").append('<input type="button" class="button" id="add_brand_button" value = "Add Brand" onclick="AddBrand()">');
function AddBrand()
{
	alert("show add brand");
}
//Page Setup  - MODAL FORMS
$("#content").append('<div id="select_manufacturer" class="widget">');
$("#content").append('<div id="manufacturer_edit" class ="widget">');
$("#content").append('<div id="account_link_edit" class ="widget">');
$("#content").append('<div id="brand_edit" class ="widget">');




//Table Setup - mfg view and edit, account/account link, brand list and brand view/edit




/*
var brand_list_table = createBrandListTable(brand_list_table_def, brand_list_data);
function createBrnadListTable(brand_table_def, brand_data)
{
	brand_list_table = new dynamic_table_object_v3('brand_list', brand_table_def, brand_data);
	brand_table.CreateSearchResultsTable('div_id', brand_data);
}

var brand_table = createBrandTable(brand_tbl_def, []);
function createBrandTable(brand_tbl_def, brand_data)
{
	brand_table = new mySQLTable_V1('mfg_table', brand_tbl_def, brand_data);
	brand_table.ajaxHandler = 'brand.ajax.php';
	brand_table.searchFlag = 'submit_brand_add_edt';
	
	mfg_table.postSubmitFunction = function()
	{
		//need to update the brand list table
	};
	brand_table.addTableToDiv('brand_edit');
	
}
*/
	
	//Select or check existing manufacturer dialog ***************************************************************************
	$("#select_manufacturer").append('<h3>Type in a Manufacturer - Select Existing or Create a New Manufacturer</h3>');
	$("#select_manufacturer").append('<p>A Manufacturer is a parent company for a brand. A Manufacturer is needed to link to accounts payable and for UPC files. Often the Manufacturer is the same as the brand and the account payable. </p>');
	$("#select_manufacturer").append('<p>Instructions: Type in the manufacturer name. A list of names will appear below the input box. If found select the name from the drop down and choose next. Otherwise enter the name and select next.</p>');
	$("#select_manufacturer").append('<input  id = "enter_mfg_name" >');
	$(document).ready(function()
	{
		$( "#select_manufacturer" ).dialog(
			{
				autoOpen: false,
				height: 400,
				width: 600,
				resizable: false,
				modal: true,
				buttons: 
				{
					"Next": function() 
					{
						select_mfg_next();
					},
					Cancel: function() 
					{
						$( this ).dialog( "close" );
						//now we want to use the other code.....
						//change mfg post submit
						mfg_table.postSubmitFunction = function()
						{
						}
					}
				},
				close: function() 
				{
				},

			});
		$('#select_manufacturer').keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) {
			  
				 select_mfg_next();
			}
		});
	
	
		$( "#enter_mfg_name" ).autocomplete({
						source: function( request, response ) 
						{
						 $.ajax(
						 {
								url: 'brand.ajax.php',
								type: 'GET',
								async: true,
								data: 
								{
									ajax_request: 'auto_complete_company',
									featureClass: "P",
									style: "full",
									maxRows: 8,
									search_terms: request.term
								},
								success: function( data ) 
								{
									parsed_autocomplete_data = parseJSONdata(data);
									response( parsed_autocomplete_data);
								}
							});
						},
						search: function() 
						{
							// custom minLength
							var term = this.value;
							if ( term.length < 2 ) 
							{
								return false;
							}
						},
						focus: function() 
						{
						   //prevent value inserted on focus
							return false;
						},
						select: function( event, ui ) 
						{
							selected_autocomplete_index = $.inArray(ui.item.value, parsed_autocomplete_data);
						}
					});	
	
	
		function select_mfg_next()
		{
			$( "#select_manufacturer" ).dialog( "close" );
		
			post_data = {};
			post_data['company'] = $('#enter_mfg_name').val();
			post_data['ajax_request'] = 'select_company';
			$.post('brand.ajax.php', post_data,
			function(response) 
			{
				process_selected_mfg(response);
			}
			);
	
		}
	

	});

	function process_selected_mfg(response)
	{
		//two options.... it exists or not
		//console.log(response);
		var parsed_data = tryParseJSON(response);
		if(parsed_data)
		{
			//console.log(parsed_data);
			if(parsed_data['success'])
			{
				//an existing manufacturer....
				mfg_table.json = parsed_data['mysql_return_data'];
				mfg_table.WriteDataToTable(mfg_table.json,'_view');
				pos_manufacturer_id = parsed_data['mysql_return_data']['pos_manufacturer_id'];
				modifyURL('?pos_manufacturer_id=' + pos_manufacturer_id);
				//here we also need to set the account link and brands data.....
				alert('Set account link and brand data');
				console.log(parsed_data);
				
			}
			else
			{
				
				//a new manufacturer.... 
				mfg_table.editForm();
				$('#mfg_tableeditcompany').val($('#enter_mfg_name').val()).select().focus();
				mfg_table.postSubmitFunction = function()
				{
					if(this.json['pos_manufacturer_id'])
					{
						pos_manufacturer_id = parsed_data['mysql_return_data']['pos_manufacturer_id'];
						modifyURL('?pos_manufacturer_id=' + this.json['pos_manufacturer_id']);
					}
				};
				

			}
			setViewOptions();
		}
		else
		{
			//system error
			alert('hmmmmm2');
		}
	}
	//Manufacturer New / View / Edit Table ****************************************************************************
	
	
	var mfg_table = new mySQLTable_V1('mfg_table', mfg_tbl_def, mfg_data);
	mfg_table.ajaxHandler = 'brand.ajax.php';
	mfg_table.searchFlag = 'submit_mfg_add_edt';
	mfg_table.postSubmitFunction = function()
	{
		if(this.json['pos_manufacturer_id'])
		{
			modifyURL('?pos_manufacturer_id=' + this.json['pos_manufacturer_id']);
		}
		
	};
	mfg_table.addViewEditTable('manufacturer');
	$("#manufacturer_edit").append('<h3>Create a New Manufacturer</h3>');
	$(document).ready(function()
	{
		$( "#manufacturer_edit" ).dialog(
			{
				autoOpen: false,
				height: 600,
				width: 600,
				resizable: false,
				modal: true,
				buttons: 
				{
					"Next": function() 
					{
						process_manufacturer_wizard()
				
					},
					Cancel: function() 
					{
						mfg_table.postSubmitFunction = function()
						{
							if(this.json['pos_manufacturer_id'])
								{
									modifyURL('?pos_manufacturer_id=' + this.json['pos_manufacturer_id']);
								}
						}
						$( this ).dialog( "close" );
					}
				},
				close: function() 
				{
				},

			});
		$('#manufacturer_edit').keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) {
				 alert('enter');
			  
			}
		});
	

	
	
	
	

	});


	



	//Step 2: Create Brand // need to check for existing brand name
	//$("#brand_edit").append('<h3 id="new_brand_name" >Step 1a - Create a New Brand</h3>');
	$(document).ready(function()
	{
		$( "#brand_wizard" ).dialog(
			{
				autoOpen: false,
				height: 600,
				width: 600,
				resizable: false,
				modal: true,
				buttons: 
				{
					"Next": function() 
					{
						$( "#brand_wizard" ).dialog( "close" );

				
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
		$('#brand_wizard').keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) {
				  $( "#brand_wizard" ).dialog( "close" );
			  
			}
		});
	});



	//*************************  LINK TO OR Create Account Payable ********************************
	$("#account_link").append('<div id="account_data">');
	$("#account_link").append('<input type="button" class="button" id="account_link_button" value = "Link to Account" onclick="accountLink()">');
	function accountLink()
	{
		$( "#account_link_edit" ).dialog( "open" );
	}
	$("#account_link_edit").append('<h3 id="new_account_name" > Link to or Create a New Accounts Payable for Billing </h3>');
	$("#account_link_edit").append('<p> All purchases need an account payable, even if paying by cc. Most manufacturers have an account payable with the same information, however many manufacturers use a factor or a different account for billing. If you are unaware of this information now, skip it and enter it when the invoice shows up.</p>');
	
	//here is the account link table def.....
	$("#account_link_edit").append('<h2> Link To existing Account.</h2>');
	
	//$("#account_link_edit").append('<div id="account_link_table_div">');

	var tbl=document.createElement('table');
	var tr=tbl.insertRow();
	var cell=document.createElement('td');
	var account_link_table = new mySQLTable_V1('account_select_table', account_select_table_def, []);
	cell.appendChild(account_link_table.createTable('_edit'));
	tr.appendChild(cell);
	var cell=document.createElement('td');
	cell.innerHTML ='<input type="button" id="acct_lin_button" class="button" value="Link" onclick="linkAccount()">';

	tr.appendChild(cell);
	
	$("#account_link_edit").append(tbl);
	
	
	//I want to make a mysql table with just one field....
	$("#account_link_edit").append('<h2>OR Create a new account</h2>');
	$("#account_link_edit").append('<button class="button" style="width:300px;" onclick="createAccount(\'copy\')"><span style="font-size:16px;">Create New Account</span><br /><span style="font-size:12px;">Copying Manufacturer Information</span></button>');
	$("#account_link_edit").append('<button class="button" style="width:300px;" onclick="createAccount(\'new\')"><span style="font-size:16px;">Create New Account</span><br /><span style="font-size:12px;">Copying No Information</span></button>');
	
	/*account_table = new mySQLTable_V1('account_table', act_table_def, account_link);
	account_table.ajaxHandler = 'brand.ajax.php';
	account_table.searchFlag = 'submit_act_add_edt';

	account_table.postSubmitFunction = function()
	{
		//update
		$("#account_data").html('update account information');
	
	};
	
	account_table.addEditTableToDiv('account_link_edit');
	*/
		
		
	function linkAccount()
	{
		//get the value...
		var pos_account_id = $('#account_select_table_edit_pos_account_id').val();
		if(pos_account_id == 'NULL')
		{
			alert('Please choose an account');
		}
		else
		{
			alert('do it');
		}
	}
	function createAccount(type)
	{
		if(type=='new')
		{
			alert('Open the page with blank information');
		}
		else
		{
			alert('Open the page with the mfg information');
		}
		/*$('#account_table_edit_company').val($('#mfg_table_view_company').val());
		$('#account_table_edit_primary_contact').val($('#mfg_table_view_sales_rep').val());
		$('#account_table_edit_email').val($('#mfg_table_view_email').val());
		$('#account_table_edit_legal_name').val($('#mfg_table_view_company').val());
		$('#account_table_edit_address1').val($('#mfg_table_view_address1').val());
		$('#account_table_edit_address2').val($('#mfg_table_view_address2').val());
		$('#account_table_edit_city').val($('#mfg_table_view_city').val());
		$('#account_table_edit_state').val($('#mfg_table_view_state').val());
		$('#account_table_edit_zip').val($('#mfg_table_view_zip').val());
		$('#account_table_edit_phone').val($('#mfg_table_view_phone').val());
		$('#account_table_edit_fax').val($('#mfg_table_view_fax').val());*/
	}	
	$(document).ready(function()
	{
		$( "#account_link_edit" ).dialog(
			{
				autoOpen: false,
				height: 350,
				width: 750,
				resizable: false,
				modal: true,
				buttons: 
				{
					
					Cancel: function() 
					{
						$( this ).dialog( "close" );
					}
				},
				close: function() 
				{
				},

			});
		$('#account_link_edit').keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) {
				  alert('Enter');
			  
			}
		});
	});


//finally display the page
$(document).ready(function()
{
	mfg_table.init();
	//account_table.init();
	setViewOptions();
	if(type == 'add')
	{
		$( "#select_manufacturer" ).dialog( "open" );
		$( "#select_manufacturer" ).focus().select();
	}
	
	
});
function setViewOptions()
{
	//here we can say if there is a mfg id show things, otherwise hide things....
	if(pos_manufacturer_id)
	{
		$("#mfg_instructions").html('<h2>Manufacturer Information</h2>');
		//$("#manufacturer").show();
		$("#account_instructions").html('<h2>Account Link to Manufacturer</h2>');
		$("#account_link").show();
		$("#brand_instructions").html('<h2>Brand Information</h2>');
		$("#brand_list").show();
	}
	else
	{
		$("#mfg_instructions").html('<h2>First Create A Manufacturer, Then you can create brand and account information</h2>');
		//$("#manufacturer").show();
		$("#account_instructions").html('<h2>Account link will show once the manufacturer is created</h2>');
		$("#account_link").hide();
		$("#brand_instructions").html('<h2>Brands can be created once the manufacturer is created.</h2>');
		$("#brand_list").hide();
	}
}



</script>
<?include(FOOTER_FILE);?>