<?php
//craig iannazzi 11-23-12
//form to grant access
$binder_name = 'System User Accounts';

require_once ('../user_functions.php');
$pos_user_id = getPostOrGetID('pos_user_id');
$page_title = 'User Binder Access';
$system_enabled_binders = loadSystemBinders();
$binder_access = getUserBinderAccess($pos_user_id);
//this for needs to display the binder with a check box and a read/write option defaulted to read... 

	$form_id = "binder_access";
	$form_action = 'user_binder_access.form.handler.php';
	$html =  '<form id = "' . $form_id . '" action="'.$form_action. '" method="post" >';
	$html .= createHiddenInput('pos_user_id',$pos_user_id);
	$html .= '<TABLE id = "binder_access" class="mysqlTable" name = "binder_access" >';
	$binderdef = array( 'db_field' => 'pos_binder_id',
								'type' => 'checkbox',
								'validate' => 'none'
								);
	$accessdef = array('db_field' =>  'access',
								'type' => 'select',
								'html' => createEnumSelect('access','pos_user_binder_access','access', 'false', 'off', ''),
								'caption' => 'Binder Access',
								'validate' => 'none');
	$html .= '<TR >';
	$html .= '<TH>';
	$html .= 'Binder ID';	
	$html .= '</TH>';		
	$html .= '<TH>';
	$html .= 'Binder Name';	
	$html .= '</TH>';	
	$html .= '<TH>';
	$html .= 'Binder Available?';	
	$html .= '</TH>';	
	$html .= '<TH>';
	$html .= 'Binder Access';	
	$html .= '</TH>';	
	$html .= '</TR >';	
	//only do this once
	$enum_values = array('WRITE','READ');
	for($binder=0;$binder<sizeof($system_enabled_binders);$binder++)
	{
		$html .= '<TR >';
			$html .= '<TD >' . $system_enabled_binders[$binder]['pos_binder_id'] .  '</td>';
		//binder name
		$html .= '<TD >'. $system_enabled_binders[$binder]['navigation_caption'] . '</td>';
		//checkbox
		//is pos_binder_id in $binder_access?
		$access = 0;
		$read_write = 'WRITE';
		for($i=0;$i<sizeof($binder_access);$i++)
		{
			//echo '<p>'.getDateTime().'</p>';
			//echo '<p>'.$counter.'</p>';
			if($binder_access[$i]['pos_binder_id'] == $system_enabled_binders[$binder]['pos_binder_id'])
			{
				$access = 1;
				$read_write = $binder_access[$i]['access'];
			}
		}
		
		$binderdef['db_field'] = $system_enabled_binders[$binder]['pos_binder_id'] . '_check';
		$aaname = $system_enabled_binders[$binder]['pos_binder_id'] . '_access';
		$binderdef['value'] = $access;
		$accessdef['value'] = $read_write;
		$html .= createTDFromTD_def($binderdef);
	
	
	$aaname = $system_enabled_binders[$binder]['pos_binder_id'] . '_access';
	//this line is taking forever
	//$accessdef['html'] = createEnumSelect($aaname,'pos_user_binder_access','access', 'false', 'off', '');
	$accessdef['html'] = createEnumSelectFast($aaname,$enum_values, 'false', 'off', '');

		
		$html .= createTDFromTD_def($accessdef);
		//enum select
		
		$html .= '</tr>';
	}
	
	

	
	$html .= '</table></p>';
	$html .= '<p><input class ="button" type="submit" name="submit" value="Submit" />' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel" />';
	$html .= '</form>';
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);

function getUserBinderAccess($pos_user_id)
{
	//get access
	$sql = "SELECT * from pos_user_binder_access WHERE pos_user_id = $pos_user_id";
	return getSQL($sql);
}
?>
