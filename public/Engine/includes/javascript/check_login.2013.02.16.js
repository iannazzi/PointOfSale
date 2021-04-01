//whenever a user clicks on the page the timer resets
//whenever the page loads the timer resets

//Every 30 seconds check the time against the timeout. If it is ok then do nothing.
//If it is timed out then pop up a warning. 
//wait five minutes - keep checking every 30 seconds
//If the user clears the message then reset the timer (other pages should clear at this point)
//If it is timed out plus 5 minutes then clear the warning and redirect to the kill session login page with the reference to the current working page. when logged out.

//self.setTimeout("timeoutWarning()", 8*60*1000);

//timer
var check_session_frequency;
var update_session_frequency;
var update_session_time;
var start_session_page_time;
InitSessionPageTimer();
function InitSessionPageTimer() 
{
    check_session_frequency = 2* 60 * 1000;             //check session every 2 minutes
    update_session_frequency = 2* 60 * 1000;             //limit update session once per 2 minutes
    update_session_time = new Date().getTime();
    CheckSessionStatusAndBootOff();
}
function CheckSessionStatusAndBootOff() 
{
    console.log("Checking timeout");
    //If the session is expired then do something, otherwise, check it again soon....
    var current_sesssion_time = new Date().getTime();
    var timeout = checkSessionTimeout();
    
}
function checkSessionTimeout()
{
	//var timeout;
	$url = POS_ENGINE_URL + "/includes/php/ajax_check_login_timeout.php";
	var request = $.ajax({	url: $url,
				async: true,
				
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
					//without the server what should we do?
					console.log( "Warning: Server Is Slow, Down or Not Responding" );
					recheck = setTimeout("CheckSessionStatusAndBootOff();", check_session_frequency);
				},
				success: function(data) 
				{
					console.log('Time Remaining: ' + secondsToTimeStringv2(data));
					//timeout = data;
					//return data;
					if (parseInt(data) < 0) //problem.....
					{
						console.log('Booting off');
						location.href=LOGOUT_URL;
						/*//display a warning, 
						var pre_final_time_check = new Date().getTime();
						//alert("Log out warning due to \n lack of user activity");
						var final_time_check = new Date().getTime();
						if (parseInt(final_time_check) > parseInt(pre_final_time_check) + parseInt(5*60*1000))
						{
							if (checkSessionTimeout() < 0 || checkSessionTimeout() == 'NULL')
							{
								//alert ('logging out');
								location.href=LOGOUT_URL;
							}
							else
							{
								//alert ('voided logging out');
								updateSessionTimeout();
								//InitSessionPageTimer();
								recheck = setTimeout("CheckSessionStatusAndBootOff();", check_session_frequency);
							}
						}
						else
						{
							//alert ('voided logging out 2');
							updateSessionTimeout();
							//InitSessionPageTimer();
							recheck = setTimeout("CheckSessionStatusAndBootOff();", check_session_frequency);
						}  */ 
					}
					else //ok....
					{
						console.log('Login OK');
						recheck = setTimeout("CheckSessionStatusAndBootOff();", check_session_frequency);
					}
				},
				timeout: 3*60*1000
	});	
}
function updateSessionTimeout()
{
		$url = POS_ENGINE_URL + "/includes/php/ajax_set_login_timeout.php";

	$.ajax({	url: $url,
				async: true,
  				success: function(data) 
  				{
    				
  				}
			});
			/*
		This works the same as below...
		$.get($url,
						function(response) 
						{
							if(response == 'OK')
							{
								alert("IT all good");
							}
							else
							{
								alert(response);
							}
						});	
	*/
}
function UserInput()
{
	//only send data up once per minute
	current_input_time = new Date().getTime();
    if (current_input_time > update_session_time + update_session_frequency)
    {
		//console.log('user input: updating session');
		updateSessionTimeout();
		update_session_time = new Date().getTime();
	}
}
function secondsToTimeStringv2(seconds)
{
	//var totalSec = new Date().getTime() / 1000;
	hours = parseInt( seconds / 3600 ) % 24;
	minutes = parseInt( seconds / 60 ) % 60;
	seconds = seconds % 60;
	
	result = (hours < 10 ? "0" + hours : hours) + " Hours " + (minutes < 10 ? "0" + minutes : minutes) + " Minutes " + (seconds  < 10 ? "0" + seconds : seconds) + " Seconds";
	return result;
}
