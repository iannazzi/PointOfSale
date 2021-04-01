<?php
//BindEvents Method @1-830736C0
function BindEvents()
{
    global $users;
    $users->CCSEvents["OnValidate"] = "users_OnValidate";
}
//End BindEvents Method

//users_OnValidate @3-2CD43F71
function users_OnValidate(& $sender)
{
    $users_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_OnValidate

//Custom Code @34-2A29BDB7
// -------------------------
	$parameters["{user_login}"] = $Component->user_login_->GetValue();
	$parameters["{site_url}"] = ServerURL;

	$sent = SendEmailMessage("approval_message", $Component->user_email_->GetValue(), $parameters);
	if (strlen($sent))
		$Component->Errors->addError($sent);

// -------------------------
//End Custom Code

//Close users_OnValidate @3-6FF40A5B
    return $users_OnValidate;
}
//End Close users_OnValidate
?>
