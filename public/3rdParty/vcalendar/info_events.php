<?php
//BindEvents Method @1-397EAC53
function BindEvents()
{
    global $CCSEvents;
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//Page_AfterInitialize @1-2F8DF58A
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $info; //Compatibility
//End Page_AfterInitialize

//Custom Code @9-2A29BDB7
// -------------------------
global $Redirect;

	$content = GetContent(CCGetSession("content_type"));

	if (CCStrLen($content)) {
		$info_param = CCGetSession("content_param");
		if (is_array($info_param) && count($info_param)) {
			while (list($key, $val) = each ($info_param))
				$content = str_replace($key, $val, $content);
		}
		$Component->ContentLabel->SetValue($content);
		CCSetSession("content_param", "");
		CCSetSession("content_type", "");
	}
	else {
		$Redirect = "index.php";
	}
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize

?>