//this is the same as document ready...
//set up some globals...
var invoice_type;

window.onload = init();
function init()
{
	
}
$(document).ready(function()
{
	$( "#login-dialog-form" ).dialog(
	{
		autoOpen: false,
		height: 150,
		width: 350,
		resizable: false,
		modal: true,
		buttons: 
		{
			"Login": function() 
			{
				 run_login_submit();
				
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
	$('#login-dialog-form').keypress(function(e) {
    if (e.keyCode == $.ui.keyCode.ENTER) {
          run_login_submit();
    }

    
});
	
	
	
});
function run_login_submit()
{
		post_data = {};
		post_data['ajax_request'] = 'CHECK_LOGIN';
		post_data['user'] = $( "#user" ).val();
		post_data['password'] = $( "#password" ).val();
		$.post('POS_V1.ajax.php', post_data,
		function(response) 
		{
			console.log(response);
			if(response != 0 && response != false)
			{
				//$( this ).dialog( "close" );
				//next window....
				if(invoice_type.type =='new')
				{
					open_win('retail_sales_invoice.php?type=SimpleInit&pos_user_id='+response);
				}
				else if (invoice_type.type == 'customer')
				{
					open_win('retail_sales_invoice.php?type=SimpleInit&pos_user_id='+response+'&pos_customer_id='+invoice_type.pos_customer_id);
				}
				else if(invoice_type.type == 'return')
				{
					open_win('retail_sales_invoice.php?type=SimpleInit&pos_user_id='+response+'&pos_sales_return_id='+invoice_type.pos_sales_return_id);
				}
			}
			else
			{
				alert('Invalid Login');
			}
		});
}
function NewInvoiceFromCustomer(control)
{
	//var tr = control.parentNode.parentNode; or
	var tr = $(control).closest('tr');
	var row = $(tr).index();
	//var row2 = tr.rowIndex;
	//console.log(row);
	//here is the sales invoice id
	pos_customer_id=customer_table.tdo[row]['pos_customer_id']['data'];
	invoice_type = {'type':'customer','pos_customer_id':pos_customer_id};
	if(login_enabled)
	{
		$('#login-dialog-form').dialog('open');
	}
	else
	{
		
		open_win('retail_sales_invoice.php?type=SimpleInit&pos_customer_id='+invoice_type.pos_customer_id);
	}
	
	
}
function newCustomerInvoice()
{
	//get the search results...
	var first_name = $('#customer_table_first_name_search').val();
	var last_name = $('#customer_table_last_name_search').val();
	var phone = $('#customer_table_phone_search').val();
	var email = $('#customer_table_email1_search').val();
	
	open_win('retail_sales_invoice.php?type=SimpleInit&first_name='+encodeURI(first_name) + '&last_name='+encodeURI(last_name) + '&phone='+encodeURI(phone) + '&email='+encodeURI(email));

}
function newReturnInvoice(control)
{
	//var tr = control.parentNode.parentNode; or
	var tr = $(control).closest('tr');
	var row = $(tr).index();
	//var row2 = tr.rowIndex;
	//console.log(row);
	//here is the sales invoice id
	pos_sales_invoice_id = customer_table.tdo[row]['pos_sales_invoice_id']['data'];
	invoice_type = {'type':'return','pos_sales_return_id':pos_sales_invoice_id};
	
	if(login_enabled)
	{
		$('#login-dialog-form').dialog('open');
	}
	else
	{
		open_win('retail_sales_invoice.php?type=SimpleInit&pos_sales_return_id='+invoice_type.pos_sales_return_id);	
	}
}
function newInvoice()
{
	if(login_enabled)
	{
		invoice_type = {'type':'new'};
		$('#login-dialog-form').dialog('open');
	}
	else
	{
		open_win('retail_sales_invoice.php?type=SimpleInit');
	}
	
}
function openTerminal()
{
	//btnOpenTerminal
}
function closeTermainal()
{
	//btnCloseTerminal
}
function newInvoiceFromCustomer(pos_customer_id)
{
	
}
function newInvoiceFromReturn(pos_invoice_id)
{
}
function addGetToUrl($name, $value)
{
	//say we did a search, we want to add the search value to the url....
}
function removeGetFromURL($name)
{
}
function removeAllGetFromUrl()
{
}

function processAjaxData(response, urlPath)
{
}


/* this is for setting window history.... window.onpopstate = function(e){
    if(e.state)
    {
        document.getElementById("content").innerHTML = e.state.html;
        document.title = e.state.pageTitle;
    }
};*/
function merge()
{
	//merge selected customers.... bad idea....deactivate instead
}


function removeURLParameter(url, parameter) 
{
    //prefer to use l.search if you have a location/link object
    var urlparts= url.split('?');   
    if (urlparts.length>=2) {

        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {    
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                pars.splice(i, 1);
            }
        }

        url= urlparts[0]+'?'+pars.join('&');
        return url;
    } else {
        return url;
    }
}
function addURLParameter(key, value)
{
    key = encodeURI(key); value = encodeURI(value);

    var kvp = document.location.search.substr(1).split('&');

    var i=kvp.length; var x; while(i--) 
    {
        x = kvp[i].split('=');

        if (x[0]==key)
        {
            x[1] = value;
            kvp[i] = x.join('=');
            break;
        }
    }

    if(i<0) {kvp[kvp.length] = [key,value].join('=');}

}


(function () {
	'use strict';
	var queryString = {};

	queryString.parse = function (str) {
		if (typeof str !== 'string') {
			return {};
		}

		str = str.trim().replace(/^(\?|#)/, '');

		if (!str) {
			return {};
		}

		return str.trim().split('&').reduce(function (ret, param) {
			var parts = param.replace(/\+/g, ' ').split('=');
			var key = parts[0];
			var val = parts[1];

			key = decodeURIComponent(key);
			// missing `=` should be `null`:
			// http://w3.org/TR/2012/WD-url-20120524/#collect-url-parameters
			val = val === undefined ? null : decodeURIComponent(val);

			if (!ret.hasOwnProperty(key)) {
				ret[key] = val;
			} else if (Array.isArray(ret[key])) {
				ret[key].push(val);
			} else {
				ret[key] = [ret[key], val];
			}

			return ret;
		}, {});
	};

	queryString.stringify = function (obj) {
		return obj ? Object.keys(obj).map(function (key) {
			var val = obj[key];

			if (Array.isArray(val)) {
				return val.map(function (val2) {
					return encodeURIComponent(key) + '=' + encodeURIComponent(val2);
				}).join('&');
			}

			return encodeURIComponent(key) + '=' + encodeURIComponent(val);
		}).join('&') : '';
	};

	if (typeof define === 'function' && define.amd) {
		define(function() { return queryString; });
	} else if (typeof module !== 'undefined' && module.exports) {
		module.exports = queryString;
	} else {
		window.queryString = queryString;
	}
})();