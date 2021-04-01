<?php
function getRoomFromBinder($binder_name)
{
	//because browsers can have multiple tabs we might loose the room name to another tab. To get around this we are going to look at the binder room names. If the session room name is in the list keep the session room name, otherwise send over the highest priority room name
	$pos_user_id = $_SESSION['pos_user_id'];
	$pos_binder_id = getBinderIDFRomBinderName($binder_name);
	$sql= "SELECT DISTINCT room_name FROM pos_room_arrangements WHERE pos_user_id = $pos_user_id AND pos_binder_id = $pos_binder_id ORDER BY room_priority DESC";
	$rooms = getSQL($sql);
	for($i=0;$i<sizeof($rooms);$i++)
	{
		if($_SESSION['room']== $rooms[$i]['room_name'])
		{
			return $_SESSION['room'];
		}
	}
	if(sizeof($rooms)>0)
	{
		return $rooms[0]['room_name'];
	}
	else
	{
		return false;
	}

}
function getBinderIDFRomBinderName($binder_name)
{
	$binder_name = scrubInput($binder_name);
	$sql = "SELECT pos_binder_id FROM pos_binders WHERE binder_name = '$binder_name'";
	return getSingleValueSQL($sql);
}
function getUserRooms($pos_user_id)
{
	$sql="SELECT DISTINCT room_name FROM pos_room_arrangements WHERE pos_user_id = $pos_user_id ORDER BY room_priority DESC";
	return getSQL($sql);
}
function getRoomContents($room_name, $pos_user_id)
{
	$sql = "SELECT IF(type = 'BINDER', concat(source ,'::', pos_binder_id), 'DIVIDER') as pos_binder_id, type, source, priority FROM pos_room_arrangements WHERE room_name = '$room_name' AND pos_user_id = $pos_user_id ORDER BY priority DESC";
	return getSQL($sql);
	
}
function getRoomSetup($room_name, $pos_user_id)
{
	$sql = "SELECT * FROM pos_room_arrangements WHERE room_name = '$room_name' AND pos_user_id = $pos_user_id ORDER BY priority DESC";
	return getSQL($sql);
	
}
function getBinderURL($binder_name)
{
	$binder_name = scrubInput($binder_name);
	$sql = "SELECT binder_path FROM pos_binders WHERE binder_name = '$binder_name'";
	$binder = getSQL($sql);
	return POS_URL . '/' .$binder[0]['binder_path'];
}
function checkValidBinderName($binder_name)
{
	$binder_name = scrubInput($binder_name);
	$sql = "SELECT pos_binder_id FROM pos_binders WHERE binder_name ='$binder_name'";
	$binder = getSQL($sql);
	if(sizeof($binder) == 0)
	{
		trigger_error( "Bad Binder Name");
		exit();
	}
}
function checkIfBinderEnabled($pos_binder_id)
{
	$sql = "SELECT enabled FROM pos_binders WHERE pos_binder_id ='$pos_binder_id'";
	$binder = getSingleValueSQL($sql);
	if($binder == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}
function getBinder($pos_binder_id)
{
	$sql = "SELECT * FROM pos_binders WHERE pos_binder_id = $pos_binder_id";
	$binder = getSQL($sql);
	return $binder;
}
function createSystemBinderNavigationButton($pos_binder_id)
{
	if(checkIfBinderEnabled($pos_binder_id))
	{
		$binder =  getBinder($pos_binder_id);
		return createIndexButton($binder[0]['navigation_caption'], POS_URL . '/'.$binder[0]['binder_path'], $binder[0]['button_size']);
	}
}

function createUserButton($binder_name)
{
	$pos_user_id = $_SESSION['pos_user_id'];
	$pos_binder_id = getBinderIDFRomBinderName($binder_name);
	if($pos_binder_id == false)
	{
		trigger_error('yo thats no binder: ' . $binder_name);
	}
	else
	{
		$binder =  getBinder($pos_binder_id);
		if(checkIfBinderEnabled($pos_binder_id))
		{
			if (checkUserBinderAccess($pos_user_id, $pos_binder_id) != false)
			{
				return createButton($binder[0]['navigation_caption'], POS_URL . '/'.$binder[0]['binder_path'], $binder[0]['button_size']);
			}
			else
			{
				return createDisabledButton($binder[0]['navigation_caption'], POS_URL . '/'.$binder[0]['binder_path'], $binder[0]['button_size']);
			}
		}
	}
}



function createIndexButton($name, $url, $size = '100')
{

		return '<input class = "indexButton" type="button"  name="'.$name.'" style = "width:'.$size.'px" value="'.$name.'" onclick="open_win(\''.$url .'\')"/>';
}
function createButton($name, $url, $size = '100')
{
	return '<input class = "button" type="button"  name="'.$name.'" style = "width:'.$size.'px;" value="'.$name.'" onclick="open_win(\''.$url .'\')" />';
}
function createDisabledButton($name, $url, $size = '100')
{
	return '<input disabled="disabled" class = "button" type="button"  name="'.$name.'" style = "width:'.$size.'px;" value="'.$name.'" onclick="open_win(\''.$url .'\')" />';
}




?>