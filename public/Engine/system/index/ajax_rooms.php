<?php



if ( isset($_GET['r']) ) 
{
	$room = $_GET['r'];
}
else
{
	$room = 'home';
}
$_SESSION['room'] = $room;	

runSQL( "UPDATE pos_users SET last_room = '" .scrubInput($_SESSION['room'])."' WHERE pos_user_id = " . $_SESSION['pos_user_id']);


$html = '';

$divide_on = false;	
//now load the rooms.....
if(strtoupper($room) == 'HOME')
{
	//add rooms
	$rooms = getUserRooms($pos_user_id);
	for($r = 0;$r<sizeof($rooms);$r++)
	{
		$html .= '<input class = "roomButton" type="button"  name="POS"  style = "width:150px" value="'.$rooms[$r]['room_name'].'" onclick="open_win(\'index.php?r='.$rooms[$r]['room_name'].'\')"/>';
	}
	if (checkIfUserIsAdmin($pos_user_id))
	{
		//add a link to all system binders
		$html .= '<input class = "roomButton" type="button"  name="ALL_Binders"  style = "width:150px" value="All System Binders" onclick="open_win(\'index.php?r=system_binders\')"/>';
		
	}
	else
	{
		//add a link to all system binders
		$html .= '<input class = "roomButton" type="button"  name="ALL_Binders"  style = "width:150px" value="All User Binders" onclick="open_win(\'index.php?r=system_binders\')"/>';
	}
}
else if(strtoupper($room) == 'SYSTEM_BINDERS')
{
	if (checkIfUserIsAdmin($pos_user_id))
	{
		//in no particular order display the binders...
		$system_binders = getSQL("SELECT * FROM pos_binders WHERE enabled = 1 ORDER BY navigation_caption ASC");
		for($bi = 0;$bi<sizeof($system_binders);$bi++)
		{
			$html .= createSystemBinderNavigationButton($system_binders[$bi]['pos_binder_id']).newline();
		}
	}
	else
	{
		//in no particular order display the binders...
		$pos_user_id = $_SESSION['pos_user_id'];
		$system_binders = getSQL("SELECT * FROM pos_user_binder_access 
								LEFT JOIN pos_binders USING (pos_binder_id)
								WHERE pos_user_id = $pos_user_id ORDER BY navigation_caption ASC");
		for($bi = 0;$bi<sizeof($system_binders);$bi++)
		{
			$html .= createSystemBinderNavigationButton($system_binders[$bi]['pos_binder_id']).newline();
		}
	}
}
else
{
	$html .= '<p><input class = "roomButton" type="button"  name="POS"  style = "width:150px" value="Home" onclick="open_win(\''.POS_URL.'\')"/></p>';
	//now get each binder/divider in the room
	$room_contents = getRoomSetup($room, $pos_user_id);
	for($rc = 0; $rc < sizeof($room_contents); $rc++)
	{
		//ok if it is a divider we add the divider.
		if ($room_contents[$rc]['source'] == 'DIVIDER')
		{
			if ($divide_on)
			{
				$html .= '</div>';
			}
			$html .= '<div class = "no_line_tight_divider">'.newline();
			$divide_on = true;
		}
		else if ($room_contents[$rc]['source'] == 'pos_binders')
		{
			$html .= createSystemBinderNavigationButton($room_contents[$rc]['pos_binder_id']).newline();
		}
		else if ($room_contents[$rc]['source'] == 'pos_custom_binders')
		{
			$html .= 'not implemented';
		}
		else
		{
			//nav error
			$html .= 'navigation error';
		}
		
	}
}
?>