<?php
//BindEvents Method @1-1E442A8C
function BindEvents()
{
    global $Panel1;
    global $sql_environment;
    global $Panel3;
    global $CCSEvents;
    $Panel1->CCSEvents["BeforeShow"] = "Panel1_BeforeShow";
    $sql_environment->CCSEvents["OnValidate"] = "sql_environment_OnValidate";
    $Panel3->CCSEvents["BeforeShow"] = "Panel3_BeforeShow";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//Panel1_BeforeShow @34-0CB436E9
function Panel1_BeforeShow(& $sender)
{
    $Panel1_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Panel1; //Compatibility
//End Panel1_BeforeShow

//Custom Code @39-2A29BDB7
// -------------------------
global $CCSLocales;
	$ErrorCount = 0;

	//Check the MySQLs 
	if (function_exists( 'mysql_connect' )) {
		$Container->MySQLCheck->SetValue("<font color=\"Green\"><b>OK</b></font>");
	} else {
		$Container->MySQLCheck->SetValue("<font color=\"Red\"><b>Failed</b></font>");
		$ErrorCount = 1;
	} 	

	//Check the rights to the common file 
	if (is_writable("../Common.php")) {
		if ($fp = @fopen("../Common.php", "a")) {
			$Container->WriteCheck->SetValue("<font color=\"Green\"><b>OK</b></font>");
			CCSetSession("isCommonHasPermissions","1");
			fclose($fp);
		} else {
			$Container->WriteCheck->SetValue("<font color=\"Red\"><b>Failed</b></font>");
			$Container->CommonDesc->SetValue("<br><b>Common.php must be modified manually</b>");
			CCSetSession("isCommonHasPermissions","0");
//			$ErrorCount = 1;
		} 	
	} else {
		$Container->WriteCheck->SetValue("<font color=\"Red\"><b>Failed</b></font>");
		$Container->CommonDesc->SetValue("<br><b>Common.php must be modified manually</b>");
		CCSetSession("isCommonHasPermissions","0");
//		$ErrorCount = 1;
	}

	if (!is_dir("../temp")) {
		$Container->FolderCheck->SetValue("<font color=\"Red\"><b>" . $CCSLocales->GetText("inst_not_exist_folder", "temp") . "</b></font>");
		$ErrorCount = 1;
	} elseif (!is_writable("../temp")) {
		$Container->FolderCheck->SetValue("<font color=\"Red\"><b>" . $CCSLocales->GetText("inst_folder_not_writable", "temp") . "</b></font>");
		$ErrorCount = 1;
	} elseif (!is_dir("../images/categories")) {
		$Container->FolderCheck->SetValue("<font color=\"Red\"><b>" . $CCSLocales->GetText("inst_not_exist_folder", "images/categories") . "</b></font>");
		$ErrorCount = 1;
	} elseif (!is_writable("../images/categories")) {
		$Container->FolderCheck->SetValue("<font color=\"Red\"><b>" . $CCSLocales->GetText("inst_folder_not_writable", "images/categories") . "</b></font>");
		$ErrorCount = 1;
	} else
		$Container->FolderCheck->SetValue("<font color=\"Green\"><b>OK</b></font>");

	if ($ErrorCount == 0) {
		$Container->InstallLink->Parameters = CCAddParam("", "step", 2);
		$Container->InstallLink->Value = $CCSLocales->GetText("install_start");
	}
	
	if ($ErrorCount == 1 || CCGetSession("isCommonHasPermissions") == "0") {
		$Container->CheckLink->Value = $CCSLocales->GetText("install_recheck");
	} else {
		$Container->CheckLink->Visible = false;
	}

// -------------------------
//End Custom Code

//Close Panel1_BeforeShow @34-D21EBA68
    return $Panel1_BeforeShow;
}
//End Close Panel1_BeforeShow

//sql_environment_OnValidate @2-B57369EF
function sql_environment_OnValidate(& $sender)
{
    $sql_environment_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $sql_environment; //Compatibility
//End sql_environment_OnValidate

//Custom Code @10-FDAF69F5
// -------------------------
global $CCSLocales;
global $Redirect;

	$sql_par = array("sql_host"     => trim($sql_environment->sql_host->GetValue()),
					 "sql_db_name"  => trim($sql_environment->sql_db_name->GetValue()),
					 "sql_username" => trim($sql_environment->sql_username->GetValue()),
					 "sql_password" => trim($sql_environment->sql_password->GetValue()) );

	$user_info = array("user_login" => trim($sql_environment->user_login->GetValue()),
					   "user_password"     => trim($sql_environment->user_password->GetValue()),
					   "user_password_rep" => trim($sql_environment->user_password_rep->GetValue()) );

	$root_info = array("root_login" => trim($sql_environment->root_username->GetValue()),
					   "root_password" => trim($sql_environment->root_password->GetValue()) );

	$create_db = $sql_environment->create_db->GetValue();
	$change_db = $sql_environment->change_db->GetValue();

	//Check form data
	$ErrorCount = $sql_environment->sql_host->Errors->Count() + 
				$sql_environment->sql_db_name->Errors->Count() + 
				$sql_environment->sql_username->Errors->Count() + 
				$sql_environment->user_login->Errors->Count() + 
				$sql_environment->user_password->Errors->Count(); 

	if ($ErrorCount) {
		return;
	}


	//Check admin login
	if ($create_db == 1 && strlen($root_info["root_login"]) == 0 ) {
		$sql_environment->Errors->addError($CCSLocales->GetText("CCS_RequiredField", $Container->root_username->Caption));
		return;
	}

	//Compare two passwords
	if ($user_info["user_password"] != $user_info["user_password_rep"]) {
		$sql_environment->Errors->addError($CCSLocales->GetText("cal_error_difpass"));
		return;
	}

	//Create the Database 
	if ($create_db == 1) {
		if (!$db = @mysql_connect($sql_par["sql_host"],$root_info["root_login"],$root_info["root_password"])) {
			$sql_environment->Errors->addError($CCSLocales->GetText("sql_connect_error"));
			return;
		}
		if (@mysql_select_db($sql_par["sql_db_name"])) {
			$sql_environment->Errors->addError($CCSLocales->GetText("sql_db_exist_error"));
			mysql_close($db);
			return;
		}

		$SQL = "CREATE DATABASE " . $sql_par["sql_db_name"] . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci"; 
		$result = mysql_query($SQL);

		if (!$result) {
			$sql_environment->Errors->addError($CCSLocales->GetText("sql_create_db_error"));
			mysql_close($db);
			return;
		}
		mysql_close($db);
	}

	//Connect to MySQL
	if (!$db = @mysql_connect($sql_par["sql_host"],$sql_par["sql_username"],$sql_par["sql_password"])) {
		$sql_environment->Errors->addError($CCSLocales->GetText("sql_connect_error"));
		return;
	}

	//Select the Database
	if (!@mysql_select_db($sql_par["sql_db_name"])) {
		$sql_environment->Errors->addError($CCSLocales->GetText("sql_database_error"));
		mysql_close($db);
		return;
	}


	if ($create_db == 1 || $create_db == 0 && $change_db == 1) {
		//Open SQL script 
		if (!$fp = @fopen("VCalendar_MySQL.sql", "r")) {
			$sql_environment->Errors->addError($CCSLocales->GetText("sql_file_open_error"));
			mysql_close($db);
			return;
		}

		//Open SQL script 
		while (!feof($fp)) {
			$str="";
			while (!(strpos($str,";") || feof($fp))) 
				$str .= fgets($fp,4096);
			if (strlen(trim($str))) {
   		        $str = substr(trim($str), 0, -1);
				mysql_query($str);
				if (mysql_errno()) echo "<li><font color=red>".mysql_error()."</font>";
			}
		}
		fclose($fp);
	}

	if ($create_db == 0 && $change_db == 2) {
		//Open SQL script 
		if (!$fp = @fopen("VCalendar_MySQL_update.sql", "r")) {
			$sql_environment->Errors->addError($CCSLocales->GetText("sql_file_open_error"));
			mysql_close($db);
			return;
		}

	 	//Check the existing tables
		$SQL = "SELECT user_id FROM users LIMIT 1";
		$result = @mysql_query($SQL);
		if (!$result || !mysql_num_rows($result)) {
			$sql_environment->Errors->addError($CCSLocales->GetText("sql_unable_to_update"));
			mysql_close($db);
			return;
		}

		//Open SQL script 
		while (!feof($fp)) {
			$str="";
			while (!(strpos($str,";") || feof($fp))) 
				$str .= fgets($fp,4096);
			if (strlen(trim($str))) {
		        $str = substr(trim($str), 0, -1);
				@mysql_query($str);
				// if (mysql_errno()) echo "<li><font color=red>".mysql_error()."</font>";
			}
		}
		fclose($fp);
	}
	
	//Update the Admin login and password
	$SQL = "SELECT user_id FROM users WHERE user_login = '" . str_replace("'", "''", $user_info["user_login"]) . "'";
	$result = mysql_query($SQL);
	if (mysql_num_rows($result)) {
		$SQL = "UPDATE users SET ".
			  " user_password = '" . str_replace("'", "''", $user_info["user_password"]) . "',".
			  " user_level = 100 ," .
			  " user_is_approved = 1 " .
			  " WHERE user_login = '" . str_replace("'", "''", $user_info["user_login"]) . "'";
	} else {
		$SQL = "INSERT INTO users ( ".
			  "user_login, ".
			  "user_password, ".
			  "user_level, ".
			  "user_is_approved) ".
			   "VALUES (" .
			   "'" . str_replace("'", "''", $user_info["user_login"]) . "'," .
			   "'" . str_replace("'", "''", $user_info["user_password"]) . "',".
			   "100 ,".
			   "1 )";
	}
	$result = mysql_query($SQL);
	mysql_close($db);

	//Update Common.php   
	if ($result && CCGetSession("isCommonHasPermissions") == 1) {
		$fcontents = join("", file("../Common.php"));
		if (!$fp = @fopen("../Common.php", "w"))
			$sql_environment->Errors->addError($CCSLocales->GetText("common_file_open_error"));
		else {
			$RegExpMask = "\"Database\"[\t \n]*=>[\t \n]*\"[a-zA-Z0-9_]*\"";
			$NewValue = '"Database" => "' . $sql_par["sql_db_name"] . '"';
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			$RegExpMask = "\"Host\"[\t \n]*=>[\t \n]*\"[a-zA-Z0-9_]*\"";
			$NewValue = '"Host" => "' . $sql_par["sql_host"] . '"';
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			$RegExpMask = "\"User\"[\t \n]*=>[\t \n]*\"[a-zA-Z0-9_]*\"";
			$NewValue = '"User" => "' . $sql_par["sql_username"] . '"';
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			$RegExpMask = "\"Password\"[\t \n]*=>[\t \n]*\"[a-zA-Z0-9_]*\"";
			$NewValue = '"Password" => "' . $sql_par["sql_password"] . '"';
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			$RegExpMask = "[\$]CalendarIsInstalled[\t \n]*=[\t \n]*false";
			$NewValue = "\$CalendarIsInstalled = true";
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			fwrite($fp,$fcontents);
			fclose($fp);
			$Redirect = "install.php?step=3";
		}	
	} else {
	//Make 
		$NewValue  = '"Database" => "' . $sql_par["sql_db_name"] . '",';
		$NewValue .="\n";
		$NewValue .= '"Host" => "' . $sql_par["sql_host"] . '",';
		$NewValue .="\n";
		$NewValue .= '"Port" => "' . '",';
		$NewValue .="\n";
		$NewValue .= '"User" => "' . $sql_par["sql_username"] . '",';
		$NewValue .="\n";
		$NewValue .= '"Password" => "' . $sql_par["sql_password"] . '",';
//		$NewValue .="\n\n";
//		$NewValue .= "\$CalendarIsInstalled = true;";

		CCSetSession("ConfigText",$NewValue);
		$Redirect = "install.php?step=3";
	}

// -------------------------
//End Custom Code

//Close sql_environment_OnValidate @2-A07C6C0B
    return $sql_environment_OnValidate;
}
//End Close sql_environment_OnValidate

//Panel3_BeforeShow @36-35C2954A
function Panel3_BeforeShow(& $sender)
{
    $Panel3_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Panel3; //Compatibility
//End Panel3_BeforeShow

//Custom Code @59-2A29BDB7
// -------------------------

	if (CCGetSession("isCommonHasPermissions") == 0) {
		$Container->ConfigArea->Visible = true;
		$Container->ConfigArea->SetValue(CCGetSession("ConfigText"));
	} else {
		$Container->ConfigArea->Visible = false;
	}

// -------------------------
//End Custom Code

//Close Panel3_BeforeShow @36-33707EC5
    return $Panel3_BeforeShow;
}
//End Close Panel3_BeforeShow

//Page_AfterInitialize @1-27F6875A
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $install; //Compatibility
//End Page_AfterInitialize

//Custom Code @23-D1696264
// -------------------------
global $sql_environment;
global $CalendarIsInstalled;

	$Step = CCGetFromGet("step",1);

	if ($CalendarIsInstalled && $Step != 3) {
		header("Location: ../index.php");
		exit;
	} else {
		//Set the Default variable
		if (!$sql_environment->FormSubmitted) {
			$sql_environment->sql_host->SetValue("localhost");
			$sql_environment->sql_db_name->SetValue("vcalendar");
			$sql_environment->sql_username->SetValue("root");

			$sql_environment->user_login->SetValue("admin");
		}

		$Container->Panel1->Visible = ($Step == 1);
		$Container->Panel2->Visible = ($Step == 2);
		$Container->Panel3->Visible = ($Step == 3);
	}
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize

?>