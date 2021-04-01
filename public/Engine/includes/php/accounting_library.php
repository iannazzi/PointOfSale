<?php
/****purchases Journal ***/
function getInventoryAccountsFieldRow()
{
	$sql = "
	SELECT pos_accounts.pos_account_id, pos_accounts.account_number, pos_accounts.company FROM pos_accounts 
	LEFT JOIN pos_account_type
	ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
	WHERE pos_accounts.active=1 AND (pos_account_type.account_type_name = 'Inventory Account')
	ORDER BY pos_accounts.company ASC
	";
	$accounts = getFieldRowSQL($sql);
	
	for($i=0;$i<sizeof($accounts['pos_account_id']);$i++)
	{
		$accounts['company_account'][$i] = $accounts['company'][$i] . ' - Act# ' . craigsDecryption($accounts['account_number'][$i]);
	}
	return $accounts;
}
function createCashDepositAccountSelect($name, $pos_account_id)
{
	//we basically want the cash drawer linked to the terminal. 
	//I believe we are linking the drawer upon open and releasing the link on close.
	//Can a terminal access any cash drawer? Probalby not, but lets start there....
	
	$registers = getSQL("SELECT pos_account_id, legal_name, account_number, pos_store_id FROM pos_accounts LEFT JOIN pos_account_type USING (pos_account_type_id) WHERE pos_accounts.active = 1 AND account_type_name = 'Cash Register Account'");

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	
	for($i = 0;$i < sizeof($registers); $i++)
	{
		$html .= '<option value="' . $registers[$i]['pos_account_id'] . '"';
		
		if ( $registers[$i]['pos_account_id'] == $pos_account_id ) 
		{
			
			$html .= ' selected="selected"';
		}
			
		$html .= '>' .$registers[$i]['legal_name'] . ' ' . craigsDecryption($registers[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createOtherDepositAccountSelect($name, $pos_account_id)
{
	//this was made specifically for putting charity gift cards into a pending charity account....
	
	$registers = getSQL("SELECT pos_account_id, legal_name, account_number, pos_store_id FROM pos_accounts LEFT JOIN pos_account_type USING (pos_account_type_id) WHERE pos_accounts.active = 1 AND account_type_name = 'Non Posting'");

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	
	for($i = 0;$i < sizeof($registers); $i++)
	{
		$html .= '<option value="' . $registers[$i]['pos_account_id'] . '"';
		
		if ( $registers[$i]['pos_account_id'] == $pos_account_id ) 
		{
			
			$html .= ' selected="selected"';
		}
			
		$html .= '>' .$registers[$i]['legal_name'] . ' ' . craigsDecryption($registers[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function storeCreditAccountSelect($name, $pos_account_id)
{
	//this was made specifically for putting charity gift cards into a pending charity account....
	
	$registers = getSQL("SELECT pos_account_id, legal_name, account_number, pos_store_id FROM pos_accounts LEFT JOIN pos_account_type USING (pos_account_type_id) WHERE pos_accounts.active = 1 AND account_type_name = 'Store Credit'");

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	
	for($i = 0;$i < sizeof($registers); $i++)
	{
		$html .= '<option value="' . $registers[$i]['pos_account_id'] . '"';
		
		if ( $registers[$i]['pos_account_id'] == $pos_account_id ) 
		{
			
			$html .= ' selected="selected"';
		}
			
		$html .= '>' .$registers[$i]['legal_name'] . ' ' . craigsDecryption($registers[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createCCAccountReceivableSelect($name, $pos_account_id)
{
	//we basically want the cash drawer linked to the terminal. 
	//I believe we are linking the drawer upon open and releasing the link on close.
	//Can a terminal access any cash drawer? Probalby not, but lets start there....
	
	$registers = getSQL("SELECT pos_account_id, company, account_number, pos_store_id FROM pos_accounts LEFT JOIN pos_account_type USING (pos_account_type_id) WHERE pos_accounts.active = 1 AND account_type_name = 'Accounts Receivable - Credit Card Processor'");
	
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	
	for($i = 0;$i < sizeof($registers); $i++)
	{
		$html .= '<option value="' . $registers[$i]['pos_account_id'] . '"';
		
		if ( ($registers[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $registers[$i]['company'] . ' ' . craigsDecryption($registers[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;

}
function createPaymentGatewaySelect($name, $pos_payment_gateway_id, $tags = '')
{
	//we basically want the cash drawer linked to the terminal. 
	//I believe we are linking the drawer upon open and releasing the link on close.
	//Can a terminal access any cash drawer? Probalby not, but lets start there....
	
	$gateways = getSQL("SELECT pos_payment_gateway_id, gateway_provider, company, model_name, account_number, line FROM pos_payment_gateways LEFT JOIN pos_accounts USING (pos_account_id) WHERE pos_payment_gateways.active = 1");
	
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .=  $tags;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	
	for($i = 0;$i < sizeof($gateways); $i++)
	{
		$html .= '<option value="' . $gateways[$i]['pos_payment_gateway_id'] . '"';
		
		if ( ($gateways[$i]['pos_payment_gateway_id'] == $pos_payment_gateway_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $gateways[$i]['gateway_provider'] . ' ' . $gateways[$i]['line'] . ' ' . $gateways[$i]['company'] . ' ' . ' ' . $gateways[$i]['model_name'] .  ' ' .craigsDecryption($gateways[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;

}
function getChartOfAccountsIDFromAccountID($pos_account_id)
{
	
	$sql = "
	SELECT parent_pos_chart_of_accounts_id 
	FROM pos_accounts
	WHERE pos_account_id = $pos_account_id

	";
	return getSingleValueSQL($sql);
} 
function checkManufacturerAccount($pos_manufacturer_id)
{
	$sql ="SELECT pos_account_id FROM pos_manufacturer_accounts WHERE pos_manufacturer_id = $pos_manufacturer_id";
	$data = getSQL($sql);
	
	if(sizeof($data)>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function getManufacturerAccount($pos_manufacturer_id)
{
	//why am I doing this seeing that the result can be one of several accounts....
	$sql = "
		SELECT pos_account_id FROM pos_manufacturers WHERE pos_manufacturer_id = $pos_manufacturer_id";
	//$sql ="SELECT pos_account_id FROM pos_manufacturer_accounts WHERE pos_manufacturer_id = $pos_manufacturer_id AND";
	return getSingleValueSQL($sql);
}
function getAccountTypeNameFromAccountType($pos_account_type_id)
{
	return getSingleValueSQL("SELECT account_type_name from pos_account_type WHERE pos_account_type_id=$pos_account_type_id");
}
function getAccountTypeName($pos_account_id)
{
	$sql = "
			SELECT pos_account_type.account_type_name FROM pos_accounts
			LEFT JOIN pos_account_type
			ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
			WHERE pos_accounts.pos_account_id = $pos_account_id
			";
	return getSingleValueSQL($sql);
}
function getAccountType($pos_account_id)
{
	$sql = "
			SELECT pos_account_type.account_type FROM pos_accounts
			LEFT JOIN pos_account_type
			ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
			WHERE pos_accounts.pos_account_id = $pos_account_id
			";
	return getSingleValueSQL($sql);
}
function getAccountTypeID($pos_account_id)
{
	$sql = "
			SELECT pos_account_type_id FROM pos_accounts
			WHERE pos_accounts.pos_account_id = $pos_account_id
			";
	return getSingleValueSQL($sql);
}
function getAccount($pos_account_id)
{
	$sql = "SELECT * FROM pos_accounts WHERE pos_account_id = $pos_account_id";
	return getSQL($sql);
}
function getAllAccounts()
{
	$sql = "SELECT * FROM pos_accounts WHERE 1";
	return getSQL($sql);
}
function getAccounts()
{
	$sql = "SELECT * FROM pos_accounts WHERE active ='1' ORDER BY company ASC";
	return getSQL($sql);
}
function getAutoPayAccountId($pos_account_id)
{
	$sql = "SELECT autopay_account_id FROM pos_accounts WHERE pos_account_id = '$pos_account_id'";
	$id = getSQL($sql);
	if ($id[0]['autopay_account_id'] == 0)
	{
		return false;
	}
	else
	{
		return $id[0]['autopay_account_id'];
	}
}
function getInventoryAndCCAccounts()
{
	$sql = "
	SELECT pos_account_type.account_type_name, pos_accounts.company, pos_accounts.pos_account_id, pos_accounts.account_number 
FROM pos_accounts 
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
WHERE pos_accounts.active ='1' AND (pos_account_type.account_type_name = 'Credit Card' OR pos_account_type.account_type_name = 'Checking Account' OR pos_account_type.account_type_name = 'Cash Account' OR pos_account_type.account_type_name = 'Inventory Account')
";
	return getSQL($sql);
}
function getChartOfAccountTypes()
{
	$sql = "SELECT * FROM pos_chart_of_account_types ORDER BY priority ASC";
	return getSQL($sql);
}
function getInvoicePaymentStatus($pos_purchases_journal_id)
{
	$sql ="SELECT payment_status FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}
function getChartOfAccountsRequired()
{
	$sql = "SELECT * FROM pos_chart_of_accounts_required ORDER BY priority ASC";
	return getSQL($sql);
}

function getPurchasesJournalInvoiceTotal($pos_purchases_journal_array)
{
	$purchases_journal_ids = implode(',', $pos_purchases_journal_array);
	$sql = "SELECT sum(invoice_amount) FROM pos_purchases_journal WHERE pos_purchases_journal_id IN ( $purchases_journal_ids )";
	return getSingleValueSQL($sql);
	
}
function getPurchasesInvoiceTotal($pos_purchases_journal_id)
{
	$sql = "SELECT invoice_amount FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}
function getPurchasesJournal($pos_purchases_journal_id)
{
	$sql = "SELECT invoice_amount, invoice_due_date, pos_manufacturer_id, invoice_number, invoice_date, fee_amount, shipping_amount, discount_applied, pos_account_id FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSQL($sql);
}
function getPurchasesJournalInvoiceNumber($pos_purchases_journal_id)
{
	$sql = "SELECT invoice_number FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}
function getAmountUsedOnCreditMemo($credit_memo_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_invoice_to_credit_memo WHERE pos_purchases_journal_credit_memo_id = $credit_memo_id";
	return getSingleValueSQL($sql);
}

function getManufacturerIDFromAccountID($pos_account_id)
{
	$sql = "SELECT pos_manufacturer_id FROM pos_manufacturers WHERE pos_account_id = $pos_account_id";
	return getSingleValueSQL($sql);
}


/****general journal *****/


function createInventoryChartOfAccountSelect($name, $pos_chart_of_accounts_id, $option_all ='off', $tags = ' style="width:100%" ')
{
	

	$sql = "SELECT * FROM pos_chart_of_accounts
	LEFT JOIN pos_chart_of_account_types USING (pos_chart_of_account_type_id)
	WHERE account_sub_type = 'Inventory' AND account_type_name = 'Current Assets'
			";
	$accounts_types = getSQL($sql);

	$html = '<select '.$tags.' id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account Type</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_type_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Account Types</option>';
	}
	for($i = 0;$i < sizeof($accounts_types); $i++)
	{
		$html .= '<option value="' . $accounts_types[$i]['pos_chart_of_accounts_id'] . '"';
		if ( ($accounts_types[$i]['pos_chart_of_accounts_id'] == $pos_chart_of_accounts_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts_types[$i]['account_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createAccountTypeSelect($name, $pos_account_type_id, $option_all ='off', $tags = ' style="width:100%" ')
{
	

	$sql = "SELECT * FROM pos_account_type";
	$accounts_types = getSQL($sql);

	$html = '<select '.$tags.' id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account Type</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_type_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Account Types</option>';
	}
	for($i = 0;$i < sizeof($accounts_types); $i++)
	{
		$html .= '<option value="' . $accounts_types[$i]['pos_account_type_id'] . '"';
		if ( ($accounts_types[$i]['pos_account_type_id'] == $pos_account_type_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts_types[$i]['caption'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createInventoryCheckingSavingAccountSelect($name, $pos_account_id, $option_all ='off')
{
	$accounts = getSQL("SELECT pos_account_type.account_type_name, pos_accounts.company, pos_accounts.pos_account_id, pos_accounts.account_number 
FROM pos_accounts 
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
WHERE pos_accounts.active ='1' AND (pos_account_type.account_type_name = 'Checking Account' OR pos_account_type.account_type_name = 'Saving Account') ORDER by pos_accounts.company ASC");

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payment Methods</option>';
	}
	for($i = 0;$i < sizeof($accounts); $i++)
	{
		$html .= '<option value="' . $accounts[$i]['pos_account_id'] . '"';
		if ( ($accounts[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts[$i]['company'] . ' - ' . craigsDecryption($accounts[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createInventoryCCCheckingCashAccountSelect($name, $pos_account_id, $option_all ='off')
{
	$accounts = getInventoryAndCCAccounts();

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payment Methods</option>';
	}
	for($i = 0;$i < sizeof($accounts); $i++)
	{
		$html .= '<option value="' . $accounts[$i]['pos_account_id'] . '"';
		if ( ($accounts[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts[$i]['company'] . ' - ' . craigsDecryption($accounts[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createAccountSelect($name, $pos_account_id, $option_all ='off')
{
	$accounts = getAccounts();

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payment Methods</option>';
	}
	for($i = 0;$i < sizeof($accounts); $i++)
	{
		$html .= '<option value="' . $accounts[$i]['pos_account_id'] . '"';
		if ( ($accounts[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts[$i]['company'] . ' - ' . craigsDecryption($accounts[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getCreditCardAccounts()
{
	$sql = "
	SELECT pos_account_type.account_type_name, pos_accounts.company, pos_accounts.pos_account_id, pos_accounts.account_number 
FROM pos_accounts 
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
WHERE pos_accounts.active ='1' AND (pos_account_type.account_type_name = 'Credit Card') ORDER by pos_accounts.company ASC
";
	return getSQL($sql);
}
function getExpenseAccounts()
{
	$sql = "
	SELECT pos_account_type.account_type_name, pos_accounts.company, pos_accounts.pos_account_id, pos_accounts.account_number 
FROM pos_accounts 
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
WHERE pos_accounts.active ='1' AND (pos_account_type.account_type_name = 'Expense Account' ) ORDER by pos_accounts.company ASC,pos_accounts.priority DESC 
";
	return getSQL($sql);
}
function createCreditCardAccountSelect($name, $pos_account_id, $option_all ='off', $tags = ' onchange="needToConfirm=true" ' )
{	
	$accounts = getCreditCardAccounts();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $tags;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($accounts); $i++)
	{
		$html .= '<option value="' . $accounts[$i]['pos_account_id'] . '"';
		
		if ( ($accounts[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts[$i]['company'] . ' - ' . craigsDecryption($accounts[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createExpenseAccountSelect($name, $pos_account_id, $option_all ='off', $tags = ' onchange="needToConfirm=true" ' )
{	
	$expense_accounts = getExpenseAccounts();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $tags;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($expense_accounts); $i++)
	{
		$html .= '<option value="' . $expense_accounts[$i]['pos_account_id'] . '"';
		
		if ( ($expense_accounts[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $expense_accounts[$i]['company'] . ' - ' . craigsDecryption($expense_accounts[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createCCDepositAccountSelect($name, $pos_account_id, $option_all ='off', $tags = ' onchange="needToConfirm=true" ' )
{	
	$accounts = getsql( "SELECT pos_account_id, company, account_number FROM pos_accounts 
			LEFT JOIN pos_account_type
			ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
			WHERE pos_accounts.active ='1' AND (pos_account_type.account_type_name = 'Checking Account') ORDER by pos_accounts.company ASC");
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $tags;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($accounts); $i++)
	{
		$html .= '<option value="' . $accounts[$i]['pos_account_id'] . '"';
		
		if ( ($accounts[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts[$i]['company'] . ' - ' . craigsDecryption($accounts[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getDepositAccounts()
{
	$sql = "SELECT pos_account_id, company, account_number FROM pos_accounts 
			LEFT JOIN pos_account_type
			ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
			WHERE pos_accounts.active ='1' AND (pos_account_type.account_type_name = 'Checking Account') ORDER by pos_accounts.company ASC";
		return getSQL($sql);
}
function createDepositAccountSelect($name, $pos_account_id, $option_all ='off', $tags = ' onchange="needToConfirm=true" ' )
{	
	$accounts = getDepositAccounts();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $tags;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($accounts); $i++)
	{
		$html .= '<option value="' . $accounts[$i]['pos_account_id'] . '"';
		
		if ( ($accounts[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts[$i]['company'] . ' - ' . craigsDecryption($accounts[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getAccountsReceivable()
{
	//which accounts can be 'receivable'
	//if wolford sends me a check, I would deposit it. That would mean wolford was now a recievable. 
	//ppc I pay money to at the end of the month, but they owe me money daily. essentially is it a dual account, do I need two?
	//can I deposit from a register to the safe
	//deposit from customer account
	$sql = "SELECT pos_account_id, company, account_number FROM pos_accounts 
			LEFT JOIN pos_account_type
			ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
			WHERE pos_accounts.active ='1' AND (pos_account_type.account_type_name = 'Checking Account' OR pos_account_type.account_type_name = 'Cash Account' OR pos_account_type.account_type_name = 'Saving Account') ORDER by pos_accounts.company ASC";
		return getSQL($sql);
}
function createAccountReceivableSelect($name, $pos_account_id, $option_all ='off', $tags = ' onchange="needToConfirm=true" ' )
{	
	$accounts = getAccountsReceivable();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $tags;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($accounts); $i++)
	{
		$html .= '<option value="' . $accounts[$i]['pos_account_id'] . '"';
		
		if ( ($accounts[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts[$i]['company'] . ' - ' . craigsDecryption($accounts[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createInvoiceTypeSelect($name, $type, $option_all ='off')
{
	$types = getInvoiceTypes();

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';

	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($type == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payment Methods</option>';
	}
	for($i = 0;$i < sizeof($types); $i++)
	{
		$html .= '<option value="' . $types[$i] . '"';
		if ( ($types[$i] == $type) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $types[$i]. '</option>';
	}
	$html .= '</select>';
	return $html;
}




function createChartOfAccountTypeSelect($name, $pos_chart_of_account_type_id, $option_all ='off')
{
	
	$accounts_types = getChartOfAccountTypes();

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account Type</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_chart_of_account_type_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Account Types</option>';
	}
	for($i = 0;$i < sizeof($accounts_types); $i++)
	{
		$html .= '<option value="' . $accounts_types[$i]['pos_chart_of_account_type_id'] . '"';
		if ( ($accounts_types[$i]['pos_chart_of_account_type_id'] == $pos_chart_of_account_type_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts_types[$i]['account_type_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createChartOfAccountMultiSelect($name, $pos_chart_of_accounts_id, $option_all = 'off', $select_events ='')
{	
	$chart_of_accounts = getChartOfAccounts();
	$html = '<select style="width:100%" multiple id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_chart_of_account_type_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($chart_of_accounts); $i++)
	{
		$html .= '<option value="' . $chart_of_accounts[$i]['pos_chart_of_accounts_id'] . '"';
		
		if ( ($chart_of_accounts[$i]['pos_chart_of_accounts_id'] == $pos_chart_of_accounts_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $chart_of_accounts[$i]['account_number'] . ' - ' . $chart_of_accounts[$i]['account_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createChartOfAccountsRequiredSelect($name, $pos_chart_of_accounts_required_id, $option_all ='off')
{
	$required_types = getChartOfAccountsRequired();

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Not A Required Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_chart_of_accounts_required_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Required Accounts</option>';
	}
	for($i = 0;$i < sizeof($required_types); $i++)
	{
		$html .= '<option value="' . $required_types[$i]['pos_chart_of_accounts_required_id'] . '"';
		
		if ( ($required_types[$i]['pos_chart_of_accounts_required_id'] == $pos_chart_of_accounts_required_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $required_types[$i]['required_account_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getChartOfAccounts()
{
	$sql = "SELECT pos_chart_of_accounts_id, account_number, account_name FROM pos_chart_of_accounts WHERE active = 1 ORDER BY account_name ASC";
	return getSQL($sql);
}
function createChartOfAccountSelect($name, $pos_chart_of_accounts_id, $option_all ='off', $tags = ' onchange="needToConfirm=true" ' )
{	
	$chart_of_accounts = getChartOfAccounts();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $tags;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_chart_of_accounts_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($chart_of_accounts); $i++)
	{
		$html .= '<option value="' . $chart_of_accounts[$i]['pos_chart_of_accounts_id'] . '"';
		
		if ( ($chart_of_accounts[$i]['pos_chart_of_accounts_id'] == $pos_chart_of_accounts_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $chart_of_accounts[$i]['account_name'] . ' - ' . $chart_of_accounts[$i]['account_number'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getCurrentAssetChartOfAccounts()
{
	$sql = "
	SELECT pos_chart_of_accounts.pos_chart_of_accounts_id, pos_chart_of_accounts.account_number, pos_chart_of_accounts.account_name 
	FROM pos_chart_of_accounts 
	LEFT JOIN pos_chart_of_account_types
	ON pos_chart_of_accounts.pos_chart_of_account_type_id = pos_chart_of_account_types.pos_chart_of_account_type_id
	WHERE pos_chart_of_accounts.active = 1 AND pos_chart_of_account_types.account_type_name = 'CURRENT ASSETS' 
	ORDER BY account_number ASC";
	return getSQL($sql);
}
function createCurrentAssetChartOfAccountSelect($name, $pos_chart_of_accounts_id, $option_all ='off')
{	
	$chart_of_accounts = getCurrentAssetChartOfAccounts();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_chart_of_accounts_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($chart_of_accounts); $i++)
	{
		$html .= '<option value="' . $chart_of_accounts[$i]['pos_chart_of_accounts_id'] . '"';
		
		if ( ($chart_of_accounts[$i]['pos_chart_of_accounts_id'] == $pos_chart_of_accounts_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $chart_of_accounts[$i]['account_number'] . ' - ' . $chart_of_accounts[$i]['account_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getLiablityChartOfAccounts()
{
	$sql = "
	SELECT pos_chart_of_accounts.pos_chart_of_accounts_id, pos_chart_of_accounts.account_number, pos_chart_of_accounts.account_name 
	FROM pos_chart_of_accounts 
	LEFT JOIN pos_chart_of_account_types
	ON pos_chart_of_accounts.pos_chart_of_account_type_id = pos_chart_of_account_types.pos_chart_of_account_type_id
	WHERE pos_chart_of_accounts.active = 1 AND pos_chart_of_account_types.account_type_name = 'CURRENT LIABILITIES' 
	ORDER BY account_number ASC";
	return getSQL($sql);
}
function createLiabilityChartOfAccountSelect($name, $pos_chart_of_accounts_id, $option_all ='off')
{	
	$chart_of_accounts = getLiabilityChartOfAccounts();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_chart_of_accounts_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($chart_of_accounts); $i++)
	{
		$html .= '<option value="' . $chart_of_accounts[$i]['pos_chart_of_accounts_id'] . '"';
		
		if ( ($chart_of_accounts[$i]['pos_chart_of_accounts_id'] == $pos_chart_of_accounts_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $chart_of_accounts[$i]['account_number'] . ' - ' . $chart_of_accounts[$i]['account_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getManufacturerAccounts($pos_manufacturer_id)
{
 	$sql ="SELECT pos_account_id, company, account_number FROM pos_manufacturer_accounts LEFT JOIN pos_accounts USING(pos_account_id) WHERE pos_manufacturer_id = $pos_manufacturer_id ORDER BY default_account DESC";
 	return getSQL($sql);
}
function getManufacturerDefaultAccount($pos_manufacturer_id)
{
 	$sql ="SELECT pos_account_id FROM pos_manufacturer_accounts WHERE pos_manufacturer_id = $pos_manufacturer_id ORDER BY default_account DESC LIMIT 1";
 	return getsingleValueSQL($sql);
}

function createMfgAccountPaymentSelect($name, $pos_manufacturer_id, $pos_account_id,  $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	$payment_methods = getManufacturerAccounts($pos_manufacturer_id);
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="0">Select If Purchased on Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payments</option>';
	}
	for($i = 0;$i < sizeof($payment_methods); $i++)
	{
		$html .= '<option value="' . $payment_methods[$i]['pos_account_id'] . '"';
		
		if ( ($payment_methods[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $payment_methods[$i]['company'] . ' ' . craigsDecryption($payment_methods[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
	
}


function getInvoicePaymentMethods($pos_manufacturer_id)
{
	//this might be 0 or null - if so it does not get added
	$merchandise_inventory_account = getChartOfAccountsIDFromRequiredAccountName('Merchandise Inventory');	
	$sql ="
SELECT  pos_accounts.pos_account_id, pos_accounts.account_number, pos_accounts.company 
FROM pos_accounts 
LEFT JOIN pos_manufacturers
ON pos_manufacturers.pos_account_id = pos_accounts.pos_account_id
WHERE pos_manufacturers.pos_manufacturer_id = '$pos_manufacturer_id'
UNION
SELECT pos_accounts.pos_account_id, pos_accounts.account_number, pos_accounts.company 
FROM pos_accounts 
LEFT JOIN pos_account_type 
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id 
WHERE pos_accounts.active=1 
AND ((pos_account_type.account_type_name = 'Credit Card' AND EXISTS (SELECT pos_accounts_to_chart_of_accounts.pos_account_id FROM pos_accounts_to_chart_of_accounts WHERE pos_accounts_to_chart_of_accounts.pos_chart_of_accounts_id = $merchandise_inventory_account AND pos_accounts_to_chart_of_accounts.pos_account_id = pos_accounts.pos_account_id))
OR (pos_account_type.account_type_name = 'Checking Account' AND EXISTS (SELECT pos_accounts_to_chart_of_accounts.pos_account_id FROM pos_accounts_to_chart_of_accounts WHERE pos_accounts_to_chart_of_accounts.pos_chart_of_accounts_id = $merchandise_inventory_account AND pos_accounts_to_chart_of_accounts.pos_account_id = pos_accounts.pos_account_id))
OR (pos_account_type.account_type_name = 'Cash Account' AND EXISTS (SELECT pos_accounts_to_chart_of_accounts.pos_account_id FROM pos_accounts_to_chart_of_accounts WHERE pos_accounts_to_chart_of_accounts.pos_chart_of_accounts_id = $merchandise_inventory_account AND pos_accounts_to_chart_of_accounts.pos_account_id = pos_accounts.pos_account_id))
OR (pos_account_type.account_type_name = 'Debit Card' AND EXISTS (SELECT pos_accounts_to_chart_of_accounts.pos_account_id FROM pos_accounts_to_chart_of_accounts WHERE pos_accounts_to_chart_of_accounts.pos_chart_of_accounts_id = $merchandise_inventory_account AND pos_accounts_to_chart_of_accounts.pos_account_id = pos_accounts.pos_account_id)))


";

	return getSQL($sql);
}
function createCashAccountSelect($name, $pos_account_id,  $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	$payment_methods =  getSQL("
SELECT pos_accounts.pos_account_id, pos_accounts.account_number, pos_accounts.company 
FROM pos_accounts 
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
WHERE pos_accounts.active=1 AND (pos_account_type.account_type_name = 'Checking Account' OR pos_account_type.account_type_name = 'Cash Account'  OR pos_account_type.account_type_name = 'Saving Account') ORDER BY pos_accounts.company ASC, pos_accounts.priority  DESC
");
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Payment</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payments</option>';
	}
	for($i = 0;$i < sizeof($payment_methods); $i++)
	{
		$html .= '<option value="' . $payment_methods[$i]['pos_account_id'] . '"';
		
		if ( ($payment_methods[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $payment_methods[$i]['company'] . ' ' . craigsDecryption($payment_methods[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
	
}
function isCheckingAccount($pos_account_id)
{
	$checking_account =   getSQL("SELECT pos_accounts.pos_account_id
		FROM pos_accounts 
		LEFT JOIN pos_account_type
		ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
		WHERE pos_accounts.active=1 AND (pos_account_type.account_type_name = 'Checking Account' and pos_accounts.pos_account_id = $pos_account_id) 
		");
	if (sizeof($checking_account)>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function createCheckingAccountSelect($name, $pos_account_id,  $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	$payment_methods =  getSQL("
SELECT pos_accounts.pos_account_id, pos_accounts.account_number, pos_accounts.company 
FROM pos_accounts 
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
WHERE pos_accounts.active=1 AND (pos_account_type.account_type_name = 'Checking Account') ORDER BY pos_accounts.company ASC, pos_accounts.priority  DESC
");
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Payment</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payments</option>';
	}
	for($i = 0;$i < sizeof($payment_methods); $i++)
	{
		$html .= '<option value="' . $payment_methods[$i]['pos_account_id'] . '"';
		
		if ( ($payment_methods[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $payment_methods[$i]['company'] . ' ' . craigsDecryption($payment_methods[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
	
}
function createExpensePaymentSelect($name, $pos_account_id,  $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	$payment_methods = getExpensePaymentMethods();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Payment</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payments</option>';
	}
	for($i = 0;$i < sizeof($payment_methods); $i++)
	{
		$html .= '<option value="' . $payment_methods[$i]['pos_account_id'] . '"';
		
		if ( ($payment_methods[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $payment_methods[$i]['company'] . ' ' . craigsDecryption($payment_methods[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
	
}
function getExpensePaymentMethods()
{

	$sql ="
SELECT pos_accounts.pos_account_id, pos_accounts.account_number, pos_accounts.company 
FROM pos_accounts 
LEFT JOIN pos_account_type
ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
WHERE pos_accounts.active=1 AND (pos_account_type.account_type_name = 'Credit Card' OR pos_account_type.account_type_name = 'Debit Card' OR pos_account_type.account_type_name = 'Checking Account' OR pos_account_type.account_type_name = 'Cash Account' OR pos_account_type.account_type_name = 'Short Term Liability' OR pos_account_type.account_type_name = 'Saving Account') ORDER BY pos_accounts.company ASC, pos_accounts.priority  DESC
";

	return getSQL($sql);
}
function getDebitChartOfAccount($pos_account_id)
{
	$sql = "SELECT pos_chart_of_accounts.pos_chart_of_accounts_id from pos_chart_of_accounts 
			LEFT JOIN pos_accounts_to_chart_of_accounts
			ON pos_chart_of_accounts.pos_chart_of_accounts_id = pos_accounts_to_chart_of_accounts.pos_chart_of_accounts_id
			WHERE pos_accounts_to_chart_of_accounts.pos_account_id = $pos_account_id";
	$results =  getSQL($sql);
	if (sizeof($results)==0)
	{
		$results[0]['pos_chart_of_accounts_id'] ='false';
	}
	return $results;
}
function createChartOfAccountsExpenseCOGSSelect($name, $pos_chart_of_accounts_id,  $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	$expense_accounts = getSQL("
	SELECT pos_chart_of_accounts_id, account_number, account_name FROM pos_chart_of_accounts LEFT JOIN pos_chart_of_account_types USING (pos_chart_of_account_type_id) WHERE active = 1 AND (account_type_name = 'Cost Of Goods Sold' OR account_type_name = 'Expense')  ORDER BY account_type_name, account_name ASC");
	
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Category</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_chart_of_accounts_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payments</option>';
	}
	for($i = 0;$i < sizeof($expense_accounts); $i++)
	{
		$html .= '<option value="' . $expense_accounts[$i]['pos_chart_of_accounts_id'] . '"';
		
		if ( ($expense_accounts[$i]['pos_chart_of_accounts_id'] == $pos_chart_of_accounts_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' /*. $expense_accounts[$i]['account_number'] . ' ' */. $expense_accounts[$i]['account_name'] . ' - # ' .$expense_accounts[$i]['account_number'] . '</option>';
	}
	$html .= '</select>';
	return $html;
	
}
function createChartOfAccountsExpenseCategorySelect($name, $pos_chart_of_accounts_id,  $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	$expense_accounts = getExpenseChartOfAccounts();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Category</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_chart_of_accounts_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payments</option>';
	}
	for($i = 0;$i < sizeof($expense_accounts); $i++)
	{
		$html .= '<option value="' . $expense_accounts[$i]['pos_chart_of_accounts_id'] . '"';
		
		if ( ($expense_accounts[$i]['pos_chart_of_accounts_id'] == $pos_chart_of_accounts_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' /*. $expense_accounts[$i]['account_number'] . ' ' */. $expense_accounts[$i]['account_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;
	
}

function getExpenseChartOfAccounts()
{
	$sql = "
	SELECT pos_chart_of_accounts_id, account_number, account_name FROM pos_chart_of_accounts WHERE active = 1 AND pos_chart_of_account_type_id = 9 ORDER BY account_name ASC";
	return getSQL($sql);
}
function getAccountChartOfAccounts($pos_account_id)
{
	$sql = "SELECT pos_chart_of_accounts_id FROM pos_accounts_to_chart_of_accounts WHERE pos_account_id = $pos_account_id";
	return getSQL($sql);
}
function getCreditLimit($pos_account_id)
{
	$sql = "SELECT credit_limit FROM pos_accounts WHERE pos_account_id = $pos_account_id";
	return getSingleValueSQL($sql);
}
function getAccountMultiSelectTableDef($pos_account_id)
{
	$selected_accounts = getAccountChartOfAccounts($pos_account_id);
	$values = array();
	for($i=0;$i<sizeof($selected_accounts);$i++)
	{
		$values[$i]['value'] = $selected_accounts[$i]['pos_chart_of_accounts_id'];
	}
	$chart_of_accounts = getChartOfAccounts();
	$list = array();
	for($i=0;$i<sizeof($chart_of_accounts);$i++)
	{
		$list[$i]['value'] = $chart_of_accounts[$i]['pos_chart_of_accounts_id'];
		$list[$i]['name'] = $chart_of_accounts[$i]['account_number'] . ' - ' . $chart_of_accounts[$i]['account_name'] ;
	}
	$table_def = array(			array('db_field' => 'pos_chart_of_accounts_id',
								'type' => 'multi_select',
								'caption' => 'Select from the Chart of Accounts that this account pays into<br><br>Use Control, Shift, and/or <br>Command To Select Multiple',
								'html' => createMultiSelect('pos_chart_of_accounts_id[]', $list, $values, ' size="15" onchange="needToConfirm=true" '),
								'validate' => array('multi_select_value' => 'false')));
	return $table_def;
}

function checkRequiredAccountisUnique($pos_chart_of_accounts_required_id)
{
	if ($pos_chart_of_accounts_required_id != 'false')
	{
		$sql = "SELECT account_name, pos_chart_of_accounts_required_id FROM pos_chart_of_accounts WHERE pos_chart_of_accounts_required_id = '$pos_chart_of_accounts_required_id'";
		$account = getSQL($sql);
		if (sizeof($account)>0)
		{
			$account = getSQL($sql);
			return $account[0]['account_name'];
		}
		else 
		{
			return 'unique';
		}
	}
	else
	{
		return 'unique';
	}
}
function getChartOfAccountsRequiredAccounts()
{
	$sql = "
			SELECT pos_chart_of_accounts_required.*, pos_chart_of_account_types.account_type_name FROM pos_chart_of_accounts_required
			LEFT JOIN pos_chart_of_account_types
			ON pos_chart_of_accounts_required.pos_chart_of_account_type_id = pos_chart_of_account_types.pos_chart_of_account_type_id
			";
	return getSQL($sql);
}
function getChartOfAccountsRequiredIdFromName($required_account_name)
{
	$sql = "SELECT pos_chart_of_accounts_required_id FROM pos_chart_of_accounts_required WHERE required_account_name = '$required_account_name'";
	return getSingleValueSQL($sql);
}
function getChartOfAccountsIDFromRequiredAccountName($required_account_name)
{
	$pos_chart_of_accounts_required_id = getChartOfAccountsRequiredIdFromName($required_account_name);
	$sql = "SELECT pos_chart_of_accounts_id FROM pos_chart_of_accounts WHERE pos_chart_of_accounts_required_id = '$pos_chart_of_accounts_required_id'";
	if (sizeof(getSQL($sql))>0)
	{
		return getSingleValueSQL($sql);
	}
	else
	{
		return '';
	}
}
function getChartOfAccountsID($account_name)
{
	$sql = "SELECT pos_chart_of_accounts_id FROM pos_chart_of_accounts WHERE account_name = '$account_name'";
	return getSingleValueSQL($sql);
}
function getTransactionChartOfAccountsID($dbc,$account_name)
{
	$sql = "SELECT pos_chart_of_accounts_id FROM pos_chart_of_accounts WHERE account_name = '$account_name'";
	return getTransactionSingleValueSQL($dbc, $sql);
}
function getAccountNumber($pos_account_id)
{
	$sql = "SELECT account_number FROM pos_accounts where pos_account_id = $pos_account_id";
	return craigsDecryption(getSingleValueSQL($sql));
}
function xxxxAccountNumber($account_number)
{
	//only return the last four digits
	return 'xxxx ' . substr($account_number, strlen($account_number)-4, strlen($account_number));
}
function getAccountName($pos_account_id)
{
	$sql = "SELECT company FROM pos_accounts where pos_account_id = $pos_account_id";
	return getSingleValueSQL($sql);
}
function getChartOfAccount($pos_chart_of_accounts_id)
{
	$sql = "SELECT * FROM pos_chart_of_accounts WHERE pos_chart_of_accounts_id = $pos_chart_of_accounts_id";
	return getSQL($sql);
}
function getChartOfAccountName($pos_chart_of_accounts_id)
{
	$sql = "SELECT account_name FROM pos_chart_of_accounts WHERE pos_chart_of_accounts_id = $pos_chart_of_accounts_id";
	return getSingleValueSQL($sql);
}
function getChartOfAccountNumber($pos_chart_of_accounts_id)
{
	$sql = "SELECT account_number FROM pos_chart_of_accounts WHERE pos_chart_of_accounts_id = $pos_chart_of_accounts_id";
	return getSingleValueSQL($sql);
}
function checkRequiredAccounts()
{
	$required_accounts = getChartOfAccountsRequiredAccounts();
	for ($i=0;$i<sizeof($required_accounts);$i++)
	{
		if(getChartOfAccountsIDFromRequiredAccountName($required_accounts[$i]['required_account_name']) == '')
		{
			// the account is missing, need to send user to the chart of accounts
			$chart_of_accounts_location = '../../business/ChartOfAccounts/list_chart_of_accounts.php';
			$message = urlencode('<span class ="error">Required Account: ' .$required_accounts[$i]['required_account_name'] . ' is not linked to an Account. Create or edit a ' .  $required_accounts[$i]['account_type_name'] .' account that links to the required account. For Example, Create Accounts Payable, #2000, and link the Required Accounts Payable account to that Account. The Code will be using these required accounts to post accounting events to the correct account, while allowing you to customize your chart of accounts.</span>');
			header('Location: '.$chart_of_accounts_location .'?message=' . $message);
		}
	}
}
function checkRequiredAccount($required_account_name)
{
	if(getChartOfAccountsIDFromRequiredAccountName($required_account_name) == '')
	{
		return false;
	}
	else
	{
		return true;
	}
}
function getAccountIdFromPurchaseOrder($pos_purchase_order_id)
{
	$sql = "
			SELECT pos_purchases_journal.pos_account_id
			FROM pos_purchases_journal
			LEFT JOIN pos_purchases_invoice_to_po
			ON pos_purchases_invoice_to_po.pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id
			WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = $pos_purchase_order_id
			";
	return getSQL($sql);
}

function getPurchaseOrderInvoicesPreviouslyApplied($pos_purchase_order_id, $pos_purchases_journal_id)
{
	$sql = "SELECT invoice_amount_applied FROM pos_purchase_orders WHERE pos_purchase_order_id = $pos_purchase_order_id";
	$sql = "SELECT sum(applied_amount) FROM pos_purchases_invoice_to_po WHERE pos_purchase_order_id = $pos_purchase_order_id AND pos_purchases_journal_id != $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}
function getPurchaseOrderCreditMemosPreviouslyApplied($pos_purchase_order_id, $pos_purchases_journal_id)
{
	$sql = "SELECT sum(applied_amount) FROM pos_purchases_credit_memo_to_po WHERE pos_purchase_order_id = $pos_purchase_order_id AND pos_purchases_journal_id != $pos_purchases_journal_id";
	return getSingleValueSQL($sql);
}







function getUnpaidExpenseInvoices($pos_account_id)
{
	$sql = "
			SELECT pos_general_journal.entry_amount, pos_general_journal.pos_general_journal_id, pos_general_journal.invoice_date, pos_general_journal.description FROM pos_general_journal 
			WHERE pos_general_journal.pos_account_id = $pos_account_id AND pos_general_journal.pos_general_journal_id NOT IN ( SELECT pos_journal_id FROM pos_invoice_to_payment WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') ORDER BY pos_general_journal.invoice_date ASC
			
			";
		$sql = "
			SELECT pos_general_journal.entry_amount, pos_general_journal.pos_general_journal_id, pos_general_journal.invoice_date, pos_general_journal.description, pos_general_journal.discount_applied, pos_general_journal.payments_applied FROM pos_general_journal 
			WHERE pos_general_journal.pos_account_id = $pos_account_id AND pos_general_journal.invoice_status = 'UNPAID' ORDER BY pos_general_journal.invoice_date ASC
			
			";
			return getSQL($sql);
}
function getUnpaidStatements($pos_account_id)
{
	$sql = "
			SELECT pos_general_journal.entry_amount, pos_general_journal.minimum_amount_due, pos_general_journal.pos_general_journal_id, pos_general_journal.invoice_date, pos_general_journal.description FROM pos_general_journal 
			WHERE pos_general_journal.pos_account_id = $pos_account_id AND pos_general_journal.pos_general_journal_id NOT IN ( SELECT pos_journal_id FROM pos_invoice_to_payment WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') AND pos_general_journal.entry_type = 'Statement'
			
			";
			echo $sql;
			return getSQL($sql);
}
function getPaymentsFromGJEntry($pos_general_journal_id)
{
	$sql = "SELECT pos_payments_journal_id FROM pos_invoice_to_payment WHERE pos_journal_id = $pos_general_journal_id AND source_journal = 'GENERAL JOURNAL'";
	return getSQL($sql);
}
function getPaymentsFromPurchasesJournalEntry($pos_purchases_journal_id)
{
	$sql = "SELECT pos_payments_journal_id FROM pos_invoice_to_payment WHERE pos_journal_id = $pos_purchases_journal_id AND source_journal = 'PURCHASES JOURNAL'";
	return getSQL($sql);
}

function getPaymentDataFromGJEntry($pos_general_journal_id)
{
	return getSQL(
	
	"SELECT pos_payments_journal.payment_amount, pos_payments_journal.pos_account_id, pos_payments_journal.pos_payee_account_id, pos_payments_journal.payment_date FROM pos_payments_journal 
	LEFT JOIN pos_invoice_to_payment
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_general_journal
	ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
	WHERE pos_general_journal.pos_general_journal_id = $pos_general_journal_id AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL'"
	
	);

}
function getPaymentDataFromPurchasesJournalEntry($pos_purchases_journal_id)
{
	return getSQL(
	
	"SELECT pos_payments_journal.payment_amount, pos_payments_journal.pos_account_id FROM pos_payments_journal 
	LEFT JOIN pos_invoice_to_payment
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_purchases_journal
	ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
	WHERE pos_purchases_journal.pos_purchases_journal_id = $pos_purchases_journal_id AND pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL'"
	
	);

}
function getGeneralLedgerSQL()
{
	$sql ="
	SELECT
	CASE pos_account_type.account_type_name 
	WHEN 'Inventory Account' THEN (SELECT @journal = 'pos_purchases_journal', @amount ='pos_purchases_journal.payment_amount')
	WHEN 'Expense Account' THEN (SELECT @journal = 'pos_general_journal')
	END
	FROM pos_account_type 
	LEFT JOIN pos_accounts
	ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
	LEFT JOIN @journal
	ON @journal.pos_account_id = pos_accounts.pos_account_id
	WHERE pos_accounts.pos_account_id = 1
	
	
	
	";
	
	
	$purchases_journal_sql = "
	
	SELECT 
			pos_purchases_journal.invoice_date as date,  
			'PURCHASES JOURNAL' as journal,
			pos_accounts.company as account_name, 
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = ".getChartOfAccountsIDFromRequiredAccountName('Merchandise Inventory') .") as chart_of_account_name, 
			pos_purchases_journal.invoice_number as description,
			IF(pos_purchases_journal.invoice_type = 'Regular', 'Invoice', 'Credit Memo') as type,
			IF(pos_purchases_journal.invoice_type = 'Regular', pos_purchases_journal.invoice_amount, NULL) as debit, 
			IF(pos_purchases_journal.invoice_type = 'Credit Memo', pos_purchases_journal.invoice_amount, NULL) as credit 
				
		FROM pos_purchases_journal
		LEFT JOIN pos_accounts
		ON pos_purchases_journal.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_chart_of_accounts
		ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
	
	";
	
	$purchase_discount_sql = "
	
	SELECT
			pos_purchases_journal.invoice_date as date,  
			'PURCHASES JOURNAL' as journal,
			pos_accounts.company as account_name, 
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = " .getChartOfAccountsIDFromRequiredAccountName('Purchase Discounts') . ") as chart_of_account_name, 
			pos_purchases_journal.invoice_number as description,
			'Discount' as type,
			pos_purchases_journal.discount_applied as debit, 
			NULL as credit 
				
		FROM pos_purchases_journal
		LEFT JOIN pos_accounts
		ON pos_purchases_journal.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_chart_of_accounts
		ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
		WHERE pos_purchases_journal.invoice_type = 'Regular' AND pos_purchases_journal.payment_status = 'PAID' AND pos_purchases_journal.discount_applied !=0
	
	";
	
	$general_journal_sql = "
	
	SELECT
			pos_general_journal.invoice_date AS date, 
			'GENERAL JOURNAL' as journal,
			pos_accounts.company as account_name, 
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = pos_general_journal.pos_chart_of_accounts_id) as chart_of_account_name, 
			CONCAT_WS(':', pos_general_journal.supplier, pos_general_journal.description) as description,
			IF(pos_general_journal.entry_amount>=0, 'Expense Invoice', 'Refund') as type,
			
			IF(pos_general_journal.entry_amount>=0, pos_general_journal.entry_amount,NULL) as debit,
			IF(pos_general_journal.entry_amount<0, -pos_general_journal.entry_amount,NULL) as credit			
			FROM pos_general_journal
			LEFT JOIN pos_accounts
			ON pos_general_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
	
		";	
	$payments_journal_sql = "
	
			SELECT  
			pos_payments_journal.payment_date AS date, 
			'PAYMENTS JOURNAL' as journal,
			pos_accounts.company as account_name,
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts
			WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) as chart_of_account_name, 
			
			IF(pos_payments_journal.payment_amount>=0, 'Payment', 'Reverse Payment') as description,
			IF(pos_payments_journal.payment_amount>=0, 'Payment', 'Reverse Payment') as type,
			IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount,NULL) as debit,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount,NULL) as credit
			
			FROM pos_payments_journal
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
			
	
	";
	
	
	$sql_array[0] = "CREATE TEMPORARY TABLE tmp " . $purchases_journal_sql . " UNION " . $purchase_discount_sql .'UNION'.  $general_journal_sql . " UNION " . $payments_journal_sql . ";";
	//$sql_array[1] =  "SELECT date, journal, account_name, chart_of_account_name, description, type, coalesce(debit,0) as debit, coalesce(credit,0) as credit FROM tmp WHERE 1";
	//$sql_array[1] =  "SELECT date, journal, account_name, chart_of_account_name, description, type,  CAST(debit AS CHAR) as debit, CAST(credit AS CHAR) as credit FROM tmp WHERE 1";
	$sql_array[1] =  "SELECT date, journal, account_name, chart_of_account_name, description, type, debit, 	credit FROM tmp WHERE 1";
	return $sql_array;
	
}

function getLinkedAccountId($pos_account_id)
{
	return  getSQL("SELECT pos_account_id FROM pos_accounts WHERE linked_pos_account_id = $pos_account_id");
	
}

function getAccountActivityTable($pos_account_id)
{

	
	//going to return an array that looks like this:
	/*$array['opening_balance'] = 10,000
	$array['balance_date'] = 10,000
	$array[0]['credit'] = 1
	$array[0]['date'] = 2012-01-01
	$array[0]['description'] = 'payment'*/
	
	$balance_sql = "
	SELECT pos_account_balances.balance_date as date, pos_account_balances.balance_amount 
	FROM pos_account_balances
		WHERE pos_account_balances.pos_account_id = $pos_account_id 
		AND pos_account_balances.balance_date = (
		SELECT MAX( pos_account_balances.balance_date )
		FROM pos_account_balances
		WHERE pos_account_balances.pos_account_id = $pos_account_id)
		";
	$counter=0;
	$balance_data = getSQL($balance_sql);
	$balance_array=array();
	if (sizeof($balance_data)>0)
	{
		$balance_array[$counter]['balance'] = $balance_data[0]['balance_amount'];
		$balance_array[$counter]['date'] = $balance_data[0]['date'];
	}
	else
	{
		$balance_array[$counter]['balance'] = 0;
		$balance_array[$counter]['date'] = '';
	}
	$balance_array[$counter]['description'] = 'balance';
	$opening_balance = $balance_array[$counter]['balance'];
	$accounting_array = array();
	$account_type = getAccountTypeName($pos_account_id);
	$counter = 0;
	if ($account_type == 'Inventory Account')
	{
		$invoice_sql = "
				SELECT pos_purchases_journal.invoice_date AS date, pos_purchases_journal.pos_purchases_journal_id,
				pos_purchases_journal.invoice_amount,
				pos_purchases_journal.invoice_type,
				pos_purchases_journal.invoice_number  
				FROM pos_purchases_journal
				WHERE pos_purchases_journal.pos_account_id = $pos_account_id
				";
		$invoice_data = getSQL($invoice_sql);
		for($i=0;$i<sizeof($invoice_data);$i++)
		{
			$accounting_array[$counter]['date'] = $invoice_data[$i]['date'];
			if($invoice_data[$i]['invoice_type']=='Regular')
			{
				$accounting_array[$counter]['debit'] = 0;
				$accounting_array[$counter]['credit'] = $invoice_data[$i]['invoice_amount'];
				$accounting_array[$counter]['description'] = 'Invoice Added: ' . createPJLink($invoice_data[$i]['pos_purchases_journal_id']);
			}
			else
			{
				$accounting_array[$counter]['debit'] = $invoice_data[$i]['invoice_amount'];
				$accounting_array[$counter]['credit'] = 0;
				$accounting_array[$counter]['description'] = 'Credit Memo: ' . createPJLink($invoice_data[$i]['pos_purchases_journal_id']);
			}
			$counter++;
		}
		$discount_sql = "
				SELECT pos_purchases_journal.invoice_date AS date, pos_purchases_journal.pos_purchases_journal_id,
				pos_purchases_journal.discount_applied,
				pos_purchases_journal.invoice_number  
				FROM pos_purchases_journal
				WHERE pos_purchases_journal.pos_account_id = $pos_account_id AND pos_purchases_journal.payment_status = 'PAID'
				AND pos_purchases_journal.discount_applied != 0
				";
		$discount_data = getSQL($discount_sql);
		for($i=0;$i<sizeof($discount_data);$i++)
		{
			$accounting_array[$counter]['date'] = $discount_data[$i]['date'];
			$accounting_array[$counter]['description'] = 'Invoice Discount: ' . createPJLink($discount_data[$i]['pos_purchases_journal_id']);
			$accounting_array[$counter]['debit'] = $discount_data[$i]['discount_applied'];
			$accounting_array[$counter]['credit'] = 0;
			$counter++;
		}
		$payments_sql = "
			SELECT DISTINCT pos_payments_journal.payment_date AS date, pos_payments_journal.pos_payments_journal_id, 
			pos_payments_journal.payment_amount
			FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_purchases_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
			WHERE pos_purchases_journal.pos_account_id = $pos_account_id AND pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL'
			";

	}
	elseif ($account_type == 'Expense Account')
	{
		$invoice_sql = "
			SELECT  pos_general_journal.invoice_date AS date,  pos_general_journal.pos_general_journal_id,
			pos_general_journal.entry_amount,
			pos_general_journal.invoice_number 
			FROM pos_general_journal
			WHERE pos_general_journal.pos_account_id = $pos_account_id
			";
		$invoice_data = getSQL($invoice_sql);
		for($i=0;$i<sizeof($invoice_data);$i++)
		{
			$accounting_array[$counter]['date'] = $invoice_data[$i]['date'];
			if($invoice_data[$i]['entry_amount']>0)
			{
				$accounting_array[$counter]['debit'] = 0;
				$accounting_array[$counter]['credit'] = $invoice_data[$i]['entry_amount'];
				$accounting_array[$counter]['description'] = 'Invoice: ' . createGeneralJournalLink($invoice_data[$i]['pos_general_journal_id']);
			}
			else
			{
				$accounting_array[$counter]['debit'] = $invoice_data[$i]['entry_amount'];
				$accounting_array[$counter]['credit'] = 0;
				$accounting_array[$counter]['description'] = 'Refund/Return ' . createGeneralJournalLink($invoice_data[$i]['pos_general_journal_id']);
			}
			$counter++;
		}
		$discount_sql = "
			SELECT  pos_general_journal.invoice_date AS date,  pos_general_journal.pos_general_journal_id,
			pos_general_journal.discount_applied,
			pos_general_journal.invoice_number 
			FROM pos_general_journal
			WHERE pos_general_journal.pos_account_id = $pos_account_id AND pos_general_journal.invoice_status = 'PAID' AND pos_general_journal.discount_applied != 0
			";
		$discount_data = getSQL($discount_sql);
		for($i=0;$i<sizeof($discount_data);$i++)
		{
			$accounting_array[$counter]['date'] = $discount_data[$i]['date'];
			$accounting_array[$counter]['description'] = 'Discount: ' . createGeneralJournalLink($discount_data[$i]['pos_general_journal_id']);
			$accounting_array[$counter]['debit'] = $discount_data[$i]['discount_applied'];
			$accounting_array[$counter]['credit'] = 0;
			$counter++;
		}
		$payments_sql = "
			SELECT DISTINCT  pos_payments_journal.payment_date AS date, pos_payments_journal.pos_payments_journal_id, 
			pos_payments_journal.payment_amount
			FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_general_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			WHERE pos_general_journal.pos_account_id = $pos_account_id AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL'
			";
	}
	
	$payment_data = getSQL($payments_sql);
	for($i=0;$i<sizeof($payment_data);$i++)
	{
		$accounting_array[$counter]['date'] = $payment_data[$i]['date'];
		if($payment_data[$i]['payment_amount']>0)
		{
			$accounting_array[$counter]['debit'] = $payment_data[$i]['payment_amount'];
			$accounting_array[$counter]['credit'] = 0;
			$accounting_array[$counter]['description'] = 'Payment: ' . createPaymentJournalLink($payment_data[$i]['pos_payments_journal_id']);
		}
		else
		{
			$accounting_array[$counter]['debit'] = 
			$accounting_array[$counter]['credit'] = $payment_data[$i]['payment_amount'];
			$accounting_array[$counter]['description'] = 'Reversed Payment' . createPaymentJournalLink($invoice_data[$i]['pos_payments_journal_id']);
		}
		$counter++;
		}
	

	if (sizeof($accounting_array)>0) sksort($accounting_array, "description", false);
	if (sizeof($accounting_array)>0) sksort($accounting_array, "date", true);
	$balance_total = $opening_balance;
	for($i=0;$i<sizeof($accounting_array);$i++)
	{
		$credit = (isset($accounting_array[$i]['credit'])) ? $accounting_array[$i]['credit']:0;
		$debit = (isset($accounting_array[$i]['debit'])) ? $accounting_array[$i]['debit']:0;
		$balance_total = $balance_total + $credit - $debit;
		$accounting_array[$i]['balance'] = $balance_total;
	}
	$html = createAccountrecordsTable(array_merge($balance_array, $accounting_array));
	return $html;
	
	for($i=0;$i<sizeof($accounting_array);$i++)
	{
	}
	
	
	
}
function switchNegativeDebitsAndCredits($data)
{
	for($i=0;$i<sizeof($data);$i++)
	{
		if ($data[$i]['debit']<0)
		{
			$data[$i]['credit'] = -$data[$i]['debit'];
			$data[$i]['debit'] = '';
		}
		if ($data[$i]['credit']<0)
		{
			$data[$i]['debit'] = -$data[$i]['credit'];
			$data[$i]['credit'] = '';
		}
	}
	return $data;
}
function getDiscountAvailable($pos_purchases_journal_id)
{
	$sql = "SELECT discount_available FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSingleValueSql($sql);
}
function getDiscountApplied($pos_purchases_journal_id)
{
	$sql = "SELECT discount_applied FROM pos_purchases_journal WHERE pos_purchases_journal_id = $pos_purchases_journal_id";
	return getSingleValueSql($sql);
}

function checkPurchasesInvoicePayment($pos_purchases_journal_id, $payment_amount)
{
	$purchases_journal_data = getPurchaseJournalData($pos_purchases_journal_id);
	$invoice_amount = $purchases_journal_data[0]['invoice_amount'];
	$discount_amount = $purchases_journal_data[0]['discount_applied'];
	$payment_applied = getInvoicePaymentApplied($pos_purchases_journal_id, 'PURCHASES JOURNAL');
	$credit_memos_applied = getCreditMemosAppliedToPurchasesInvoice($pos_purchases_journal_id); 
	$due = $invoice_amount -$discount_amount-$payment_applied-$credit_memos_applied;
	
	if(abs($invoice_amount -$discount_amount-$payment_applied-$credit_memos_applied-$payment_amount) < 0.0001)
	{
		return 'PAID';
	}
	else if ($invoice_amount -$discount_amount-$payment_applied-$credit_memos_applied-$payment_amount < 0)
	{
		//Overpayment
		return 'OVERPAYMENT';
	}
	else 
	{
		return 'PAID';
	}
}
function checkIfInvoiceIsPaid($pos_purchases_journal_id)
{								
	
	$purchases_journal_data = getPurchaseJournalData($pos_purchases_journal_id);
	$invoice_amount = $purchases_journal_data[0]['invoice_amount'];
	$discount_amount = $purchases_journal_data[0]['discount_applied'];
	$payment_applied = getInvoicePaymentApplied($pos_purchases_journal_id, 'PURCHASES JOURNAL');
	$credit_memos_applied = getCreditMemosAppliedToPurchasesInvoice($pos_purchases_journal_id); 
	$due = $invoice_amount -$discount_amount-$payment_applied-$credit_memos_applied;
	
	if(abs($invoice_amount -$discount_amount-$payment_applied-$credit_memos_applied) < 0.0001)
	{
		return 'PAID';
	}
	else
	{
		return 'UNPAID';
	}
}


function createPurchasePaymentSql()
{
	
	$purchases_journal_sql = "
	
	SELECT 
			pos_purchases_journal.invoice_date as date,  
			'PURCHASES JOURNAL' as journal,
			pos_accounts.company as account_name, 
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = 153) as chart_of_account_name, 
			pos_purchases_journal.invoice_number as description,
			IF(pos_purchases_journal.invoice_type = 'Regular', 'Invoice', 'Credit Memo') as type,
			IF(pos_purchases_journal.invoice_type = 'Regular', pos_purchases_journal.invoice_amount, NULL) as debit, 
			IF(pos_purchases_journal.invoice_type = 'Credit Memo', pos_purchases_journal.invoice_amount, NULL) as credit 
				
		FROM pos_purchases_journal
		LEFT JOIN pos_accounts
		ON pos_purchases_journal.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_chart_of_accounts
		ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
	
	";
	
	
	$general_journal_sql = "
	
	SELECT
			pos_general_journal.invoice_date AS date, 
			'GENERAL JOURNAL' as journal,
			pos_accounts.company as account_name, 
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = pos_general_journal.pos_chart_of_accounts_id) as chart_of_account_name, 
			CONCAT_WS(':', pos_general_journal.supplier, pos_general_journal.description) as description,
			IF(pos_general_journal.entry_amount>=0, 'Expense Invoice', 'Refund') as type,
			
			IF(pos_general_journal.entry_amount>=0, pos_general_journal.entry_amount,NULL) as debit,
			IF(pos_general_journal.entry_amount<0, -pos_general_journal.entry_amount,NULL) as credit			
			FROM pos_general_journal
			LEFT JOIN pos_accounts
			ON pos_general_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
	
		";	
	$payments_journal_sql = "
	
			SELECT  
			pos_payments_journal.payment_date AS date, 
			'PAYMENTS JOURNAL' as journal,
			pos_accounts.company as account_name,
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts
			WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) as chart_of_account_name, 
			
			IF(pos_payments_journal.payment_amount>=0, 'Payment', 'Reverse Payment') as description,
			IF(pos_payments_journal.payment_amount>=0, 'Payment', 'Reverse Payment') as type,
			IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount,NULL) as debit,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount,NULL) as credit
			
			FROM pos_payments_journal
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
			
	
	";
	
	
	$sql_array[0] = "CREATE TEMPORARY TABLE tmp " . $purchases_journal_sql . " UNION " . $general_journal_sql . " UNION " . $payments_journal_sql . ";";
	//$sql_array[1] =  "SELECT date, journal, account_name, chart_of_account_name, description, type, coalesce(debit,0) as debit, coalesce(credit,0) as credit FROM tmp WHERE 1";
	//$sql_array[1] =  "SELECT date, journal, account_name, chart_of_account_name, description, type,  CAST(debit AS CHAR) as debit, CAST(credit AS CHAR) as credit FROM tmp WHERE 1";
	$sql_array[1] =  "SELECT date, journal, account_name, chart_of_account_name, description, type, debit, 	credit FROM tmp WHERE 1";
	return $sql_array;
	
}
function createChartOfAccountsActivitySQL()
{
}

//receipt, invoice, transfer, statement
//purchases journal general journal
//payments journal
//cc cash account debit/bank

//credit card
//cc charges from general journal
//cc charges from purchases journal
//cc charges paying account

//account

//bank/debit/cash
//cc
function charges_to_cc_account_from_general_journal_sql($pos_account_id)
{
	//charges made to credit card from general journal:
	return "
	
	SELECT  
			pos_payments_journal.payment_date AS date, 
			'GENERAL JOURNAL' AS journal,
			pos_accounts.company AS account_name,
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts
			WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) AS chart_of_account_name,
			IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment To: ', pos_general_journal.supplier),CONCAT('Refund From: ', pos_general_journal.supplier)) AS description,
			'Payment' AS type,
			IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount,NULL) as debit,
			IF(pos_payments_journal.payment_amount<0, pos_payments_journal.pos_account_id,NULL) as debit_account_id,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount,NULL) as credit,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.pos_account_id,NULL) as credit_account_id
			
			FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_general_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
			LEFT JOIN pos_account_type
			ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
			WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL' 
			AND pos_account_type.account_type_name ='Credit Card' 
			AND (pos_general_journal.entry_type = 'Receipt' OR pos_general_journal.entry_type = 'Transfer')
			
	
	
	";
}
function charges_to_cc_account_from_purchases_journal_sql($pos_account_id)
{	
	return "
	
	SELECT  
			pos_payments_journal.payment_date AS date, 
			'PAYMENTS JOURNAL' AS journal,
			pos_accounts.company AS account_name,
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts
			WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) AS chart_of_account_name,
			IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment To: ', (SELECT pos_manufacturers.company FROM pos_manufacturers WHERE pos_manufacturers.pos_manufacturer_id = pos_purchases_journal.pos_manufacturer_id)),CONCAT('Refund From: ', (SELECT pos_manufacturers.company FROM pos_manufacturers WHERE pos_manufacturers.pos_manufacturer_id = pos_purchases_journal.pos_manufacturer_id))) AS description,
			'Payment' AS type,
			IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount,NULL) as debit,
			IF(pos_payments_journal.payment_amount<0, pos_payments_journal.pos_account_id,NULL) as debit_account_id,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount,NULL) as credit,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.pos_account_id,NULL) as credit_account_id
			
		
			FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_purchases_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
			LEFT JOIN pos_account_type
			ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
			WHERE pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL'
			AND pos_account_type.account_type_name ='Credit Card'  
	";
}
function charges_to_cc_account_from_account($pos_account_id)
{
		return "
		SELECT  
			pos_payments_journal.payment_date AS date, 
			'GENERAL JOURNAL' AS journal,
			pos_accounts.company AS account_name,
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts
			WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) AS chart_of_account_name,
			IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment To: ', pos_general_journal.supplier),CONCAT('Refund From: ', pos_general_journal.supplier)) AS description,
			'Payment' AS type,
			IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount,NULL) as debit,
			IF(pos_payments_journal.payment_amount<0, pos_payments_journal.pos_account_id,NULL) as debit_account_id,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount,NULL) as credit,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.pos_account_id,NULL) as credit_account_id
			
			FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_general_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
			LEFT JOIN pos_account_type
			ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
			WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL' 
			AND pos_account_type.account_type_name ='Credit Card' 
			AND pos_general_journal.entry_type = 'Transfer'
	";
}
function payments_to_cc_account()
{
	/*(SELECT pos_payments_journal.pos_account_id 
				FROM  pos_payments_journal
				LEFT JOIN pos_invoice_to_payment
				ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
				LEFT JOIN pos_general_journal
				ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
				WHERE pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id 
				AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL')*/
	return "
	
	SELECT 
			
			
			pos_general_journal.invoice_date as date,  
			'GENERAL JOURNAL' as journal,
			pos_accounts.company as account_name, 
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = pos_general_journal.pos_chart_of_accounts_id) as chart_of_account_name, 
			CONCAT('Payment From', 
				' Cant figure this one out') as description,
			IF(pos_general_journal.invoice_type = 'Regular', 'Invoice', 'Credit Memo') as type,
			
			IF(pos_general_journal.invoice_type = 'Regular', pos_general_journal.entry_amount, NULL) as debit, 
			IF(pos_general_journal.invoice_type = 'Regular', pos_general_journal.pos_account_id, NULL) as debit_account_id,
			IF(pos_general_journal.invoice_type = 'Credit Memo', pos_general_journal.entry_amount, NULL) as credit,
			IF(pos_general_journal.invoice_type = 'Credit Memo', pos_general_journal.pos_account_id, NULL) as credit_account_id

		FROM pos_general_journal
		LEFT JOIN pos_accounts
		ON pos_general_journal.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_chart_of_accounts
		ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
		LEFT JOIN pos_account_type
		ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
		WHERE pos_account_type.account_type_name = 'Credit Card'
		AND pos_general_journal.entry_type = 'Transfer'
	
	";
}

//account

function general_journal_payments_to_account_sql()
{
	return "
			SELECT  
			pos_payments_journal.payment_date AS date, 
			'PAYMENTS JOURNAL' AS journal,
			pos_accounts.company AS account_name,
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts
			WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) AS chart_of_account_name,
			CONCAT('Payment Using: ', (SELECT pos_accounts.company FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) AS description,
			'Payment' AS type,
			pos_payments_journal.payment_amount as debit,
			pos_payments_journal.pos_account_id as debit_account_id,
			NULL as credit,
			NULL as credit_account_id
			
			FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_general_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
			LEFT JOIN pos_account_type
			ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
			WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL' 
					
	";
}


//bank - cash - debit accounts are all reveresed from credit and inventory expense accounts
function general_journal_bank_debit_cash_payments_sql()
{
	return "
	
	SELECT  
			pos_payments_journal.payment_date AS date, 
			'GENERAL JOURNAL' AS journal,
			pos_accounts.company AS account_name,
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts
			WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) AS chart_of_account_name,
			IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment To: ', pos_general_journal.supplier),CONCAT('Refund From: ', pos_general_journal.supplier)) AS description,
			'Payment' AS type,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount,NULL) as debit,
			IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.pos_account_id,NULL) as debit_account_id,
			IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount,NULL) as credit,
			IF(pos_payments_journal.payment_amount<0, pos_payments_journal.pos_account_id,NULL) as credit_account_id
			
			
			FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_general_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
			LEFT JOIN pos_account_type
			ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
			WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL' 
			AND pos_account_type.account_type_name ='Cash Account' and pos_general_journal.entry_type != 'Transfer'
			
	";
}
function general_journal_bank_debit_cash_deposits_sql()
{
	//date, journal_id, journal_name, account_name, description, type, debit, debit_account_id, credit, credit_account_id
	return "
	
	SELECT 
			pos_general_journal.invoice_date as date,  
			'GENERAL JOURNAL' as journal,
			pos_accounts.company as account_name, 
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = pos_general_journal.pos_chart_of_accounts_id) as chart_of_account_name, 
			CONCAT('Deposit: ', pos_general_journal.pos_general_journal_id) as description,
			'Deposit' as type,
			IF(pos_general_journal.entry_amount <0, pos_general_journal.entry_amount, NULL) as debit,
			IF(pos_general_journal.entry_amount <0, pos_general_journal.pos_account_id, NULL) as debit_account_id,
			IF(pos_general_journal.entry_amount >=0, pos_general_journal.entry_amount, NULL) as credit, 
			IF(pos_general_journal.entry_amount >=0, pos_general_journal.pos_account_id, NULL) as credit_account_id

		FROM pos_general_journal
		LEFT JOIN pos_accounts
		ON pos_general_journal.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_chart_of_accounts
		ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
		LEFT JOIN pos_account_type
		ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
		WHERE  pos_general_journal.entry_type = 'Transfer' 
	";
}
function general_journal_receipt_payments_sql()
{
		return 
		"
	
	SELECT  
			pos_payments_journal.payment_date AS date, 
			'GENERAL JOURNAL' AS journal,
			pos_accounts.company AS account_name,
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts
			WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) AS chart_of_account_name,
			CONCAT('Payment Using: ', (SELECT pos_accounts.company FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id)) AS description,
			'Payment' AS type,
			pos_payments_journal.payment_amount as debit,
			pos_general_journal.pos_account_id as debit_account_id,
			NULL as credit,
			NULL as credit_account_id
			
			
			FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			LEFT JOIN pos_general_journal
			ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
			LEFT JOIN pos_account_type
			ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
			WHERE pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL'
			AND pos_general_journal.entry_type = 'Receipt'
	";
}


function payments($payment_type = 'Credit Card', $account_id = 'false')
{
	//$payment_type = 'Credit Card';
	if($account_id == 'false')
	{
		$and = '';
	}
	else
	{
		$and = 'AND pos_payments_journal.pos_account_id =' .$account_id;
	}


	//this gets the invoices with the payment
	pprint("Purchases Journal invoices with payments");
	$sql ="
	SELECT 
	pos_purchases_journal.pos_purchases_journal_id as journal_id,
	'PURCHASES JOURNAL' as journal,
	pos_purchases_journal.invoice_amount as amount,
	 pos_purchases_journal.discount_applied as discount,
	pos_purchases_journal.invoice_type as type,
	pj_payment.pos_payments_journal_id as payment_id,
	pjact.company
	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_purchases_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment AS pj_payment 
	ON pos_purchases_journal.pos_purchases_journal_id = pj_payment.pos_journal_id
	LEFT JOIN pos_payments_journal
	ON pj_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	LEFT JOIN pos_accounts as pjact 
	ON pos_payments_journal.pos_account_id = pjact.pos_account_id

	WHERE pos_account_type.account_type_name = '$payment_type' AND pos_accounts.pos_account_id =$account_id AND pj_payment.source_journal ='PURCHASES JOURNAL'	
	";	
	pprint($sql);
	pprint("Purchases Journal invoices without payments");
	$sql ="
		SELECT 
	pos_purchases_journal.pos_purchases_journal_id as journal_id,
	'PURCHASES JOURNAL' as journal,
	pos_purchases_journal.invoice_amount as amount,
	 pos_purchases_journal.discount_applied as discount,
	pos_purchases_journal.invoice_type as type,
	0 as payment_id,
	'' as company
	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_purchases_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment AS pj_payment 
	ON pos_purchases_journal.pos_purchases_journal_id = pj_payment.pos_journal_id
	LEFT JOIN pos_payments_journal
	ON pj_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	LEFT JOIN pos_accounts as pjact 
	ON pos_payments_journal.pos_account_id = pjact.pos_account_id

	WHERE pos_account_type.account_type_name = '$payment_type' AND pos_accounts.pos_account_id =$account_id AND pos_purchases_journal.pos_purchases_journal_id NOT IN (SELECT pos_invoice_to_payment.pos_journal_id FROM pos_invoice_to_payment WHERE pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL')	
	";	
	pprint($sql);
	
		$sql ="
		SELECT 
	pos_general_journal.pos_general_journal_id as journal_id,
	'GENERAL JOURNAL' as journal,
	pos_general_journal.pos_account_id as gj_account,
	pos_chart_of_accounts.account_name,
	pos_general_journal.entry_amount as amount,
	 pos_general_journal.discount_applied as discount,
	pos_general_journal.entry_type as type,
	gj_payment.pos_payments_journal_id as payment_id,
	gjact.company
	FROM pos_accounts
	LEFT JOIN pos_chart_of_accounts ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id 
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_general_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment AS gj_payment 
	ON pos_general_journal.pos_general_journal_id = gj_payment.pos_journal_id
	LEFT JOIN pos_payments_journal
	ON gj_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	LEFT JOIN pos_accounts as gjact 
	ON pos_payments_journal.pos_account_id = gjact.pos_account_id

	WHERE pos_account_type.account_type_name = '$payment_type' AND pos_accounts.pos_account_id =$account_id
	";
	
	pprint($sql);
	pprint("payments");
	$sql = "
	
	SELECT pos_invoice_to_payment.pos_journal_id, 
	pos_invoice_to_payment.source_journal as source_journal, 
	pos_accounts.company, pos_payments_journal.pos_account_id, 
	pos_payments_journal.payment_amount, pos_payments_journal.payment_date 
	FROM pos_payments_journal 
	right JOIN pos_invoice_to_payment USING (pos_payments_journal_id)
	LEFT JOIN pos_accounts
	ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
	LEFT JOIN pos_account_type
	ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
	WHERE pos_account_type.account_type_name = '$payment_type' " .$and;
	
	//and this is the payment query
	$sql = "
	
	SELECT 
	IF(pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL', pos_general_journal.pos_general_journal_id, pos_purchases_journal.pos_purchases_journal_id) as journal_id,
	IF(pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL', pos_general_journal.pos_account_id, pos_purchases_journal.pos_account_id) as journal_account,
	IF(pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL', act2.company, act3.company) as journal_company,
	IF(pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL', pos_general_journal.entry_amount, pos_purchases_journal.invoice_amount) as invoice_amount,
	IF(pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL', pos_general_journal.entry_type, pos_purchases_journal.invoice_type) as type,
	IF(pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL', pos_general_journal.discount_applied, pos_purchases_journal.discount_applied) as discount_applied,
	pos_invoice_to_payment.source_journal as source_journal, 
	
	pos_payments_journal_id,
	pos_payments_journal.pos_account_id as payment_account,
	act1.company as payment_company,  
	pos_payments_journal.payment_amount, 
	pos_payments_journal.payment_date 
	FROM pos_payments_journal 
	LEFT JOIN pos_invoice_to_payment USING (pos_payments_journal_id)
	LEFT JOIN pos_general_journal ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
	LEFT JOIN pos_purchases_journal ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
	LEFT JOIN pos_accounts AS act1
	ON pos_payments_journal.pos_account_id = act1.pos_account_id
	LEFT JOIN pos_accounts AS act2
	ON pos_general_journal.pos_account_id = act2.pos_account_id
	LEFT JOIN pos_accounts AS act3
	ON pos_purchases_journal.pos_account_id = act3.pos_account_id
	LEFT JOIN pos_account_type
	ON act1.pos_account_type_id = pos_account_type.pos_account_type_id
	WHERE pos_account_type.account_type_name = '$payment_type' " .$and;
	pprint($sql);
	return $sql;
}

//new stuff
function create_purchases_payments_sql()
{
	$sql = "
	CREATE TEMPORARY TABLE purchases
	SELECT pos_purchases_journal.pos_purchases_journal_id as pjid, pos_purchases_journal.pos_account_id as debit_account, pos_payments_journal.pos_payments_journal_id as pid, pos_payments_journal.pos_account_id as credit_account
	FROM pos_purchases_journal
	LEFT JOIN pos_invoice_to_payment
	ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
	LEFT JOIN pos_payments_journal
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	WHERE pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL';
	CREATE TEMPORARY TABLE purchases2 LIKE purchases;
	CREATE TEMPORARY TABLE purchases3 LIKE purchases;
	CREATE TEMPORARY TABLE purchases4 LIKE purchases;
	SELECT pos_purchases_journal.pos_purchases_journal_id, pos_purchases_journal.pos_account_id as debit_account, IF(pos_purchases_journal.pos_purchases_journal_id IN (SELECT purchases.pjid FROM purchases), (SELECT purchases2.pid FROM purchases2 WHERE purchases2.pjid = pos_purchases_journal.pos_purchases_journal_id),0) as pos_payments_journal_id, IF(pos_purchases_journal.pos_purchases_journal_id IN (SELECT purchases3.pjid FROM purchases3), (SELECT purchases4.credit_account FROM purchases4 WHERE purchases4.pjid = pos_purchases_journal.pos_purchases_journal_id), 0) as credit_account FROM pos_purchases_journal
	";
	
	
}
function purchases_journal_invoice_on_account_sql($pos_account_id)
{
	//date, journal_id, journal, account_name, chart_of_account_name, description, type, debit, credit
	$sql = "
	
	SELECT 
			pos_purchases_journal.invoice_date as date,
			pos_purchases_journal.pos_purchases_journal_id as journal_id,  
			'PURCHASES JOURNAL' as journal,
			pos_accounts.company as account_name, 
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = ".getChartOfAccountsIDFromRequiredAccountName('Merchandise Inventory') .") as chart_of_account_name, 
			IF(pos_purchases_journal.invoice_type = 'Regular', CONCAT('Invoice: ', pos_purchases_journal.invoice_number),  CONCAT('CREDIT: ', pos_purchases_journal.invoice_number)) as description,
			IF(pos_purchases_journal.invoice_type = 'Regular', 'Invoice', 'Credit Memo') as type,
			IF(pos_purchases_journal.invoice_type = 'Credit Memo', pos_purchases_journal.invoice_amount, NULL) as debit,
			IF(pos_purchases_journal.invoice_type = 'Regular', pos_purchases_journal.invoice_amount, NULL) as credit,
			NULL as verify
		FROM pos_purchases_journal
		LEFT JOIN pos_accounts
		ON pos_purchases_journal.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_chart_of_accounts
		ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
		WHERE pos_accounts.pos_account_id = $pos_account_id
	
	";
	return $sql;
}

function purchases_journal_discounts_applied($pos_account_id)
{
	//date, journal_id, journal_name, account_name, chart_of_account_name, description, type, debit, credit
	$sql =  "
	
	SELECT 
			pos_purchases_journal.invoice_date as date,  
			pos_purchases_journal.pos_purchases_journal_id as journal_id,
			'PURCHASES JOURNAL' as journal,
			pos_accounts.company as account_name, 
			(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = ".getChartOfAccountsIDFromRequiredAccountName('Purchase Discounts') .") as chart_of_account_name, 
			CONCAT('Discount For Invoice: ' , pos_purchases_journal.invoice_number) as description,
			IF(pos_purchases_journal.invoice_type = 'Regular', 'Invoice', 'Credit Memo') as type,
			pos_purchases_journal.discount_applied as debit,
			NULL as credit,
			NULL as verify

		FROM pos_purchases_journal
		LEFT JOIN pos_accounts
		ON pos_purchases_journal.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_chart_of_accounts
		ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
		WHERE pos_purchases_journal.payment_status = 'PAID' AND pos_purchases_journal.discount_applied != 0
		AND pos_accounts.pos_account_id = $pos_account_id
	
	";
		
	return $sql;
}
function purchases_journal_invoice_payments($pos_account_id)
{
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal_name,
	pos_payment_account.company as account_name,
	pos_payment_chart_of_accounts.account_name AS chart_of_account_name,
	IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment Using ', pos_payment_account.company), CONCAT('Refund To ', pos_payment_account.company)) as description,
	'Payment' as type,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as credit,
	NULL as verify

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_purchases_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment AS pj_payment 
	ON pos_purchases_journal.pos_purchases_journal_id = pj_payment.pos_journal_id
	LEFT JOIN pos_payments_journal
	ON pj_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	LEFT JOIN pos_accounts as pos_payment_account 
	ON pos_payments_journal.pos_account_id = pos_payment_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_payment_chart_of_accounts
	ON pos_payment_account.parent_pos_chart_of_accounts_id = pos_payment_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pj_payment.source_journal ='PURCHASES JOURNAL'	
	";	
	return $sql;
}


function transfers_to_account($pos_account_id)
{
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_general_journal.pos_general_journal_id as journal_id,
	'GENERAL JOURNAL' as journal,
	pos_payment_account.company as account_name,
	pos_payment_chart_of_accounts.account_name AS chart_of_account_name,
	IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment Using ', pos_payment_account.company), CONCAT('Refund To ', pos_payment_account.company)) as description,
	pos_general_journal.entry_type as type,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as credit,
	pos_general_journal.validated as verify

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_general_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment AS pj_payment 
	ON pos_general_journal.pos_general_journal_id = pj_payment.pos_journal_id
	LEFT JOIN pos_payments_journal
	ON pj_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	LEFT JOIN pos_accounts as pos_payment_account 
	ON pos_payments_journal.pos_account_id = pos_payment_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_payment_chart_of_accounts
	ON pos_payment_account.parent_pos_chart_of_accounts_id = pos_payment_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id 
	AND pj_payment.source_journal ='GENERAL JOURNAL'
	AND pos_general_journal.entry_type = 'Transfer'	
	";	
	//pos_account_type.account_type_name = '$account_type' AND
	return $sql;
}

//****************************** CC Account Listing ********************//
function cc_charges_from_general_journal($pos_account_id)
{
	$sql ="
	SELECT 
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_general_journal_account.company as account_name,
	
	(SELECT GROUP_CONCAT(pos_general_journal_chart_of_accounts.account_name) FROM pos_general_journal
	LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
	ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
	LEFT JOIN pos_invoice_to_payment
	ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
	WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id 
	AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as chart_of_account_name,

	(SELECT GROUP_CONCAT(concat(pos_general_journal.supplier,' : ', pos_general_journal.description)) FROM pos_general_journal
	LEFT JOIN pos_invoice_to_payment
	ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
	WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id 
	AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as description,


	'' as type,
	IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as credit,
	pos_payments_journal.validated as verify

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment  
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_general_journal
	ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
	LEFT JOIN pos_accounts as pos_general_journal_account 
	ON pos_general_journal.pos_account_id = pos_general_journal_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
	ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL'
	AND (pos_general_journal.entry_type = 'Receipt' OR 	pos_general_journal.entry_type = 'Invoice')
	";	
	return $sql;
}
function cc_charges_from_purchases_journal($pos_account_id)
{

	//getting a double if the 'account' is different than the 'supplier'....
	
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_purchases_journal_account.company as account_name,
	pos_purchases_journal_chart_of_accounts.account_name AS chart_of_account_name,
	
	(SELECT GROUP_CONCAT(CONCAT((SELECT pos_manufacturers.company FROM pos_manufacturers WHERE pos_manufacturer_id = pos_purchases_journal.pos_manufacturer_id),' Invoice Number ', pos_purchases_journal.invoice_number)) FROM pos_purchases_journal
INNER JOIN pos_invoice_to_payment
ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id AND pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL') as description,
	

	'Invoice' as type,
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as credit,
	pos_payments_journal.validated as verify

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment  
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_purchases_journal
	ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
	LEFT JOIN pos_accounts as pos_purchases_journal_account 
	ON pos_purchases_journal.pos_account_id = pos_purchases_journal_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_purchases_journal_chart_of_accounts
	ON ".getChartOfAccountsIDFromRequiredAccountName('Merchandise Inventory') ." = pos_purchases_journal_chart_of_accounts.pos_chart_of_accounts_id
	LEFT JOIN pos_manufacturers
	ON pos_purchases_journal.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL'
	";	
	return $sql;
}
function payments_to_account($pos_account_id)
{
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_payment_account.company as account_name,
	pos_payment_chart_of_accounts.account_name AS chart_of_account_name,
	IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment Using ', pos_payment_account.company), CONCAT('Refund To ', pos_payment_account.company)) as description,
	'PAYMENT To ACCOUNT' as type,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as credit,
	pos_payments_journal.validated as verify

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal ON pos_payments_journal.pos_payee_account_id = pos_accounts.pos_account_id

	LEFT JOIN pos_accounts as pos_payment_account 
	ON pos_payments_journal.pos_account_id = pos_payment_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_payment_chart_of_accounts
	ON pos_payment_account.parent_pos_chart_of_accounts_id = pos_payment_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id 
	";	
	//pos_account_type.account_type_name = '$account_type' AND
	return $sql;
}
function payments_onto_account($pos_account_id)
{

	//these are payments that have no 'link' to a journal...
	//it looks like the credit / debit order is important for now...
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_payee_account.company as account_name,
	pos_payee_chart_of_accounts.account_name AS chart_of_account_name,
	IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment To ', pos_payee_account.company), CONCAT('Refund From ', pos_payee_account.company)) as description,
	'PAYMENT TO ACCOUNT' as type,
	IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as credit,
	
	pos_payments_journal.validated as verify

	FROM pos_accounts
	
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
	LEFT JOIN pos_accounts as pos_payee_account 
	ON pos_payments_journal.pos_payee_account_id = pos_payee_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_payee_chart_of_accounts
	ON pos_payee_account.parent_pos_chart_of_accounts_id = pos_payee_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pos_payments_journal.pos_payments_journal_id NOT IN (SELECT pos_payments_journal_id FROM pos_invoice_to_payment WHERE pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id)
	";	
	//pos_account_type.account_type_name = '$account_type' AND
	//echo $sql;
	return $sql;
}



function general_journal_invoice_on_account_sql($pos_account_id)
{
	$sql =  "
	
	SELECT 
			pos_general_journal.invoice_date as date,  
			pos_general_journal.pos_general_journal_id as journal_id,
			'GENERAL JOURNAL' as journal,
			pos_accounts.company as account_name, 
			pos_chart_of_accounts2.account_name as chart_of_account_name, 
			CONCAT('Invoice ', pos_general_journal.invoice_number, ' ' ,  pos_general_journal.description) as description,
			IF(pos_general_journal.invoice_type = 'Regular', 'Invoice', 'Credit Memo') as type,
			IF(pos_general_journal.invoice_type = 'Credit Memo', pos_general_journal.entry_amount, NULL) as debit,
			IF(pos_general_journal.invoice_type = 'Regular', pos_general_journal.entry_amount, NULL) as credit,
			pos_general_journal.validated as verify

		FROM pos_general_journal
		LEFT JOIN pos_accounts
		ON pos_general_journal.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_chart_of_accounts
		ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
		LEFT JOIN pos_account_type
		ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
		LEFT JOIN pos_chart_of_accounts AS pos_chart_of_accounts2
		ON pos_general_journal.pos_chart_of_accounts_id = pos_chart_of_accounts2.pos_chart_of_accounts_id
		WHERE pos_accounts.pos_account_id =$pos_account_id
		AND pos_general_journal.entry_type = 'Invoice' 
	
	";
	//pos_account_type.account_type_name = '$account_type'
	return $sql;
}
function general_journal_discounts_applied($pos_account_id)
{
$sql =  "
	
	SELECT 
			pos_general_journal.invoice_date as date,  
			pos_general_journal.pos_general_journal_id as journal_id,
			'GENERAL JOURNAL' as journal,
			pos_accounts.company as account_name, 
			pos_chart_of_accounts2.account_name AS chart_of_account_name, 
			CONCAT('Discount For Invoice: ' , pos_general_journal.invoice_number) as description,
			'Discount' as type,
			pos_general_journal.discount_applied as debit,
			NULL as credit,
			NULL as verify

		FROM pos_general_journal
		LEFT JOIN pos_accounts
		ON pos_general_journal.pos_account_id = pos_accounts.pos_account_id
		LEFT JOIN pos_chart_of_accounts
		ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
		LEFT JOIN pos_account_type
		ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
		LEFT JOIN pos_chart_of_accounts AS pos_chart_of_accounts2
		ON pos_general_journal.pos_chart_of_accounts_id = pos_chart_of_accounts2.pos_chart_of_accounts_id
		WHERE pos_general_journal.invoice_status = 'PAID' 
		AND pos_general_journal.discount_applied != 0
		AND pos_general_journal.entry_type = 'Invoice'
		AND pos_chart_of_accounts2.pos_chart_of_accounts_id = " .getChartOfAccountsIDFromRequiredAccountName('Retained Earnings') ."
		AND pos_accounts.pos_account_id =$pos_account_id";
		//AND pos_account_type.account_type_name = '$account_type'";
	return $sql;
	
}
function general_journal_invoice_payments($pos_account_id)
{
	//date, journal_id, journal_name, account_name, chart_of_account_name, description, type, debit, debit_account_id, credit, credit_account_id
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_payment_account.company as account_name,
	pos_payment_chart_of_accounts.account_name AS chart_of_account_name,
	IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment Using ', pos_payment_account.company), CONCAT('Refund To ', pos_payment_account.company)) as description,
	pos_general_journal.entry_type as type,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as credit,
	NULL as verify

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_general_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment AS pj_payment 
	ON pos_general_journal.pos_general_journal_id = pj_payment.pos_journal_id
	LEFT JOIN pos_payments_journal
	ON pj_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	LEFT JOIN pos_accounts as pos_payment_account 
	ON pos_payments_journal.pos_account_id = pos_payment_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_payment_chart_of_accounts
	ON pos_payment_account.parent_pos_chart_of_accounts_id = pos_payment_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pj_payment.source_journal ='GENERAL JOURNAL'	
	";	
	//pos_account_type.account_type_name = '$account_type' AND
	return $sql;
}


//******************* BANK LISTING ***********************//
function bank_payments_from_general_journal($pos_account_id)
{

	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_general_journal.pos_general_journal_id as journal_id,
	'GENERAL JOURNAL' as journal,
	pos_general_journal_account.company as account_name,
	pos_general_journal_chart_of_accounts.account_name AS chart_of_account_name,
	pos_general_journal.description as description,
	pos_general_journal.entry_type as type,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as credit
	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment  
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_general_journal
	ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
	LEFT JOIN pos_accounts as pos_general_journal_account 
	ON pos_general_journal.pos_account_id = pos_general_journal_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
	ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL'
	AND (pos_general_journal.entry_type = 'Receipt' OR 	pos_general_journal.entry_type = 'Invoice')
	";	
	
	
	
	$sql ="
	SELECT 
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_general_journal_account.company as account_name,
	
	(SELECT GROUP_CONCAT(pos_general_journal_chart_of_accounts.account_name) FROM pos_general_journal
	LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
	ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
	LEFT JOIN pos_invoice_to_payment
	ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
	WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id 
	AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as chart_of_account_name,

	(SELECT GROUP_CONCAT(concat(pos_general_journal.supplier,' : ', pos_general_journal.description)) FROM pos_general_journal
	LEFT JOIN pos_invoice_to_payment
	ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
	WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id 
	AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as description,


	'' as type,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount, NULL) as credit,
	
	pos_payments_journal.validated as verify

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment  
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_general_journal
	ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
	LEFT JOIN pos_accounts as pos_general_journal_account 
	ON pos_general_journal.pos_account_id = pos_general_journal_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
	ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL'
	AND (pos_general_journal.entry_type = 'Receipt' OR 	pos_general_journal.entry_type = 'Invoice')
	";	
	
	
	
	return $sql;
}
function bank_payments_from_purchases_journal($pos_account_id)
{

	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_purchases_journal.pos_purchases_journal_id as journal_id,
	'PURCHASES JOURNAL' as journal,
	pos_purchases_journal_account.company as account_name,
	pos_purchases_journal_chart_of_accounts.account_name AS chart_of_account_name,
	CONCAT(pos_manufacturers.company,' Invoice Number ', pos_purchases_journal.invoice_number) as description,
	'Invoice' as type,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as credit
	

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment  
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_purchases_journal
	ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
	LEFT JOIN pos_accounts as pos_purchases_journal_account 
	ON pos_purchases_journal.pos_account_id = pos_purchases_journal_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_purchases_journal_chart_of_accounts
	ON ".getChartOfAccountsIDFromRequiredAccountName('Merchandise Inventory') ." = pos_purchases_journal_chart_of_accounts.pos_chart_of_accounts_id
	LEFT JOIN pos_manufacturers
	ON pos_purchases_journal.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL'
	";	
	
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_purchases_journal_account.company as account_name,
	pos_purchases_journal_chart_of_accounts.account_name AS chart_of_account_name,
	
	(SELECT GROUP_CONCAT(CONCAT((SELECT pos_manufacturers.company FROM pos_manufacturers WHERE pos_manufacturer_id = pos_purchases_journal.pos_manufacturer_id),' Invoice Number ', pos_purchases_journal.invoice_number)) FROM pos_purchases_journal
INNER JOIN pos_invoice_to_payment
ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id AND pos_invoice_to_payment.source_journal = 'PURCHASES JOURNAL') as description,
	

	'Invoice' as type,
		IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as credit,

	pos_payments_journal.validated as verify

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment  
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_purchases_journal
	ON pos_invoice_to_payment.pos_journal_id = pos_purchases_journal.pos_purchases_journal_id
	LEFT JOIN pos_accounts as pos_purchases_journal_account 
	ON pos_purchases_journal.pos_account_id = pos_purchases_journal_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_purchases_journal_chart_of_accounts
	ON ".getChartOfAccountsIDFromRequiredAccountName('Merchandise Inventory') ." = pos_purchases_journal_chart_of_accounts.pos_chart_of_accounts_id
	LEFT JOIN pos_manufacturers
	ON pos_purchases_journal.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pos_invoice_to_payment.source_journal ='PURCHASES JOURNAL'
	";	
	
	
	return $sql;
}
function transfers_from_bank_account($pos_account_id)
{
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_payment_account.company as account_name,
	pos_payment_chart_of_accounts.account_name AS chart_of_account_name,
	IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment Using ', pos_payment_account.company), CONCAT('Refund To ', pos_payment_account.company)) as description,
	pos_general_journal.entry_type as type,
	
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as credit
	

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_general_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment AS pj_payment 
	ON pos_general_journal.pos_general_journal_id = pj_payment.pos_journal_id
	LEFT JOIN pos_payments_journal
	ON pj_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
	LEFT JOIN pos_accounts as pos_payment_account 
	ON pos_payments_journal.pos_account_id = pos_payment_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_payment_chart_of_accounts
	ON pos_payment_account.parent_pos_chart_of_accounts_id = pos_payment_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id 
	AND pj_payment.source_journal ='GENERAL JOURNAL'
	AND pos_general_journal.entry_type = 'Transfer'	
	";	
	//pos_account_type.account_type_name = '$account_type' AND
	return $sql;
}
function payments_to_bank_account($pos_account_id)
{
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_payment_account.company as account_name,
	pos_payment_chart_of_accounts.account_name AS chart_of_account_name,
	IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment Using ', pos_payment_account.company), CONCAT('Refund To ', pos_payment_account.company)) as description,
	'PAYMENT To ACCOUNT' as type,
		IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as debit,

	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as credit,
	pos_payments_journal.validated as verify

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal ON pos_payments_journal.pos_payee_account_id = pos_accounts.pos_account_id

	LEFT JOIN pos_accounts as pos_payment_account 
	ON pos_payments_journal.pos_account_id = pos_payment_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_payment_chart_of_accounts
	ON pos_payment_account.parent_pos_chart_of_accounts_id = pos_payment_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id 
	";	
	//pos_account_type.account_type_name = '$account_type' AND
	return $sql;
}
function payments_from_bank_account($pos_account_id)
{

	//these are payments that have no 'link' to a journal...
	//it looks like the credit / debit order is important for now...
	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_payments_journal.pos_payments_journal_id as journal_id,
	'PAYMENTS JOURNAL' as journal,
	pos_payee_account.company as account_name,
	pos_payee_chart_of_accounts.account_name AS chart_of_account_name,
	IF(pos_payments_journal.payment_amount>=0, CONCAT('Payment To ', pos_payee_account.company), CONCAT('Refund From ', pos_payee_account.company)) as description,
	'PAYMENT TO ACCOUNT' as type,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as debit,
		IF(pos_payments_journal.payment_amount<0, -pos_payments_journal.payment_amount, NULL) as credit,

	pos_payments_journal.validated as verify

	FROM pos_accounts
	
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
	LEFT JOIN pos_accounts as pos_payee_account 
	ON pos_payments_journal.pos_payee_account_id = pos_payee_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_payee_chart_of_accounts
	ON pos_payee_account.parent_pos_chart_of_accounts_id = pos_payee_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pos_payments_journal.pos_payments_journal_id NOT IN (SELECT pos_payments_journal_id FROM pos_invoice_to_payment WHERE pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id)
	";	
	//pos_account_type.account_type_name = '$account_type' AND
	//echo $sql;
	return $sql;
}
function deposits_to_bank_account($pos_account_id)
{
}



/* this returns double payments if one payment is linked to two entries*/
/*function cc_charges_from_general_journal($pos_account_id)
{

	$sql ="
	SELECT DISTINCT
	pos_payments_journal.payment_date as date,
	pos_general_journal.pos_general_journal_id as journal_id,
	'GENERAL JOURNAL' as journal,
	pos_general_journal_account.company as account_name,
	pos_general_journal_chart_of_accounts.account_name AS chart_of_account_name,
	pos_general_journal.description as description,
	pos_general_journal.entry_type as type,
	IF(pos_payments_journal.payment_amount<0, pos_payments_journal.payment_amount, NULL) as debit,
	IF(pos_payments_journal.payment_amount>=0, pos_payments_journal.payment_amount, NULL) as credit

	FROM pos_accounts
	LEFT JOIN pos_account_type USING (pos_account_type_id)
	LEFT JOIN pos_payments_journal USING (pos_account_id)
	LEFT JOIN pos_invoice_to_payment  
	ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
	LEFT JOIN pos_general_journal
	ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
	LEFT JOIN pos_accounts as pos_general_journal_account 
	ON pos_general_journal.pos_account_id = pos_general_journal_account.pos_account_id
	LEFT JOIN pos_chart_of_accounts AS pos_general_journal_chart_of_accounts
	ON pos_general_journal.pos_chart_of_accounts_id = pos_general_journal_chart_of_accounts.pos_chart_of_accounts_id
	WHERE  pos_accounts.pos_account_id =$pos_account_id AND pos_invoice_to_payment.source_journal ='GENERAL JOURNAL'
	AND (pos_general_journal.entry_type = 'Receipt' OR 	pos_general_journal.entry_type = 'Invoice')
	";	
	return $sql;
}*/

function canWePayForIt($dbc, $pos_account_id, $payment_amount)
{
		// can we pay for it? --> is it check or credit?
		$balance = getTransactionAccountBalance($dbc, $pos_account_id);
		if(getAccountTypeName($pos_account_id) == 'Credit Card')
		{
			if($balance + $payment_amount > getCreditLimit($pos_account_id))
			{
				$errors[] = 'PAYMENT EXCEEDS AVAILABLE CREDIT';
			}
		}
		elseif(getAccountTypeName($pos_account_id) == 'Checking Account')
		{
			if($balance < $payment_amount)
			{
				$errors[] = 'PAYMENT EXCEEDS AVAILABLE BALANCE';
			}
		}
}
?>