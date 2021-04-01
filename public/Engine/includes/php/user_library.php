<?php
function check_user_store_set()
{
	$pos_user_id = $_SESSION['pos_user_id'];
	 $store = getUserStoreId($pos_user_id);
	if($store == 0)
	{
		include(HEADER_FILE);
		echo 'Error - Set user up with a default store id';
		include(FOOTER_FILE);
		exit();
	}
	
}
function check_user_employee_set()
{
	$pos_user_id = $_SESSION['pos_user_id'];
	$sql = "SELECT pos_employee_id from pos_users where pos_user_id=$pos_user_id";
	$employee = getSingleValueSQL($sql);
	if($employee == 0)
	{
		include(HEADER_FILE);
		echo 'Error - Set user up with a employee id';
		include(FOOTER_FILE);
		exit();
	}
	
}
function getUserStoreId($pos_user_id)
{
	$sql = "SELECT default_store_id from pos_users where pos_user_id=$pos_user_id";
	return getSingleValueSQL($sql);
}


function checkSystemUserAccess($pos_user_id)
{
		//if the user_id is not the session user, then we need to check session user access
		//var_dump($_SESSION);

			if($pos_user_id != $_SESSION['pos_user_id'])
			{
				$binder_name = 'System User Accounts';
				$pos_binder_id = getBinderIDFRomBinderName($binder_name);
				if(checkUserBinderAccess($_SESSION['pos_user_id'], $pos_binder_id) != 'WRITE')
				{
					echo 'No Access To This Function - Contact the admin.';
					exit();
				}
				else
				{
				}
			}
		


}
function checkUserAccess($binder_name)
{
	$pos_user_id =$_SESSION['pos_user_id'];
	$pos_binder_id = getBinderIDFRomBinderName($binder_name);
	return checkUserBinderAccess($pos_user_id, $pos_binder_id);
	
}
function checkWriteAccess($binder_name)
{
	$pos_user_id = $_SESSION['pos_user_id'];
	$pos_binder_id = getBinderIDFRomBinderName($binder_name);
	$access= (checkUserBinderAccess($pos_user_id, $pos_binder_id) == 'WRITE') ? true : false;
	return $access;
}
function checkReadAccess($binder_name)
{
	$pos_user_id = $_SESSION['pos_user_id'];
	$pos_binder_id = getBinderIDFRomBinderName($binder_name);
	$access= (checkUserBinderAccess($pos_user_id, $pos_binder_id) == 'READ') ? true : false;
	return $access;
}
function checkUserGroupAccess($pos_user_id, $field)
{
	//we have to find if a user has access to a checked item, typically under groups.
	$user_groups = getSQL(" SELECT pos_user_groups.pos_user_group_id, pos_user_groups." . $field . " FROM pos_user_groups
							LEFT JOIN pos_users_in_groups USING (pos_user_group_id) WHERE pos_user_id = $pos_user_id");
	$checked = false;
	for($i=0; $i < sizeof($user_groups); $i++)
	{
		if($user_groups[$i][$field] > 0 )
		{
			$checked = true;
		}
	}
	return $checked;
		
}
function checkUserBinderAccess($pos_user_id, $pos_binder_id)
{
	if(!checkIfUserIsAdmin($pos_user_id))
	{
		$sql = "SELECT access FROM pos_user_binder_access WHERE binder_type ='SYSTEM' AND pos_user_id = $pos_user_id AND pos_binder_id = $pos_binder_id";
		$binders = getSQL($sql);
		if (sizeof($binders) == 0)
		{
			return false;
		}
		else
		{
			return $binders[0]['access'];
		}
	}
	else
	{
		return 'WRITE';
	}
}
function checkIfUserIsAdmin($pos_user_id)
{
	$sql = "SELECT admin FROM pos_users WHERE pos_user_id = $pos_user_id";
	$admin = getSQL($sql);
	return ($admin[0]['admin'] == 1) ? true : false;
	
	
}
function getDefaultDaysForView($pos_user_id)
{
	$sql = "SELECT default_view_date_range_days FROM pos_users WHERE pos_user_id = $pos_user_id";
	return getSingleValueSQL($sql);
}
function getUserEmail($pos_user_id)
{
	$sql = "SELECT email FROM pos_users WHERE pos_user_id = $pos_user_id";
	return getSingleValueSQL($sql);
}
function getUser($pos_user_id)
{
	$sql = "SELECT * from pos_users where pos_user_id=$pos_user_id";
	return getSQL($sql);
}
function getUserFullName($pos_user_id)
{
	$emp = getUser($pos_user_id);
	if (sizeof($emp) == 0)
	{
		$emp_name = 'NOT A REGISTERED USER';
	}
	else
	{
		$emp_name = $emp[0]['first_name'] . ' ' . $emp[0]['last_name'];
	}
	return $emp_name;
}
function getLoggedInUserId()
{
	return $_SESSION['pos_user_id'];
}

?>