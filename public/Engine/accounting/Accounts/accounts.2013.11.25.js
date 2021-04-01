$(function() 
{
	$( "#account_type_form" ).dialog(
	{
		autoOpen: true,
		height: 300,
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
		
	$('#account_type_form').keypress(function(e) 
	{
    	if (e.keyCode == $.ui.keyCode.ENTER) 
    	{
          run_login_submit();
    	}
    });
});
