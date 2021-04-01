<?php
function createUnpaidStatmentSelect($name, $pos_account_id, $selected, $option_all ='off', $select_events = ' onchange="needToConfirm=true" ')
{

	$open_invoices = getUnpaidStatements($pos_account_id);
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	
	$html .= '<option value ="false"';
		if (sizeof($selected) == 0)
		{
			$html .= ' selected="selected"';
		}
	$html .= '>None Selected</option>';
	for($i = 0;$i < sizeof($open_invoices); $i++)
	{
		$html .= '<option value="' . $open_invoices[$i]['pos_general_journal_id'] . '"';
		for($k=0;$k<sizeof($selected);$k++)
		{
			if ( ($open_invoices[$i]['pos_general_journal_id'] == $selected[$k]['pos_general_journal_id']) ) 
			{
				$html .= ' selected="selected"';
			}
		}
		$html .= '>Invoice Date: ' . $open_invoices[$i]['invoice_date'] . ' Amount: ' .round($open_invoices[$i]['entry_amount'],2) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createUnpaidExpenseInvoiceSelect($name, $pos_account_id, $selected_invoices, $option_all ='off', $select_events = ' onchange="needToConfirm=true" ')
{

	$open_invoices = getUnpaidExpenseInvoices($pos_account_id);
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	
	$html .= '<option value ="false"';
		if (sizeof($selected_invoices) == 0)
		{
			$html .= ' selected="selected"';
		}
	$html .= '>None Selected</option>';
	for($i = 0;$i < sizeof($open_invoices); $i++)
	{
		$html .= '<option value="' . $open_invoices[$i]['pos_general_journal_id'] . '"';
		for($k=0;$k<sizeof($selected_invoices);$k++)
		{
			if ( ($open_invoices[$i]['pos_general_journal_id'] == $selected_invoices[$k]['pos_general_journal_id']) ) 
			{
				$html .= ' selected="selected"';
			}
		}
		$html .= '>Invoice Date: ' . $open_invoices[$i]['invoice_date'] . ' Amount: ' .round($open_invoices[$i]['entry_amount'],2) . 
		' Paid: ' .round($open_invoices[$i]['payments_applied'],2) .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createGeneralJournalEntryTypeSelect($name, $entry_type, $option_all = 'off', $select_events = '')
{
	$options = getGeneralJournalEntryTypes();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';

	for($i = 0;$i < sizeof($options); $i++)
	{
		$html .= '<option value="' . $options[$i] . '"';
		
		if ( ($options[$i] == $entry_type) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $options[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function getGeneralJournalEntryTypes()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_general_journal'
AND COLUMN_NAME = 'entry_type'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function getGeneralJournalEntryType($pos_general_journal_id)
{
	$sql = "SELECT entry_type FROM pos_general_journal WHERE pos_general_journal_id = $pos_general_journal_id";
	return getSingleValueSql($sql);
}
function getGeneralJournalInvoiceStatusSelect($name, $invoice_status, $option_all = 'off', $select_events = '')
{
	$options = getGeneralJournalInvoiceStatus();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';

	for($i = 0;$i < sizeof($options); $i++)
	{
		$html .= '<option value="' . $options[$i] . '"';
		
		if ( ($options[$i] == $invoice_status) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $options[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function getGeneralJournalInvoiceStatus()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_general_journal'
AND COLUMN_NAME = 'invoice_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function getGeneralJournalEntry($pos_general_journal_id)
{
	$sql = "SELECT * FROM pos_general_journal WHERE pos_general_journal_id = $pos_general_journal_id";
	return getSQL($sql);
}
function getExpenseInvoicesSum($pos_general_journal_id_array)
{
	
	$sql = "SELECT SUM(entry_amount) as invoice_total FROM pos_general_journal WHERE pos_general_journal_id IN (". implode( $pos_general_journal_id_array ) . ")";
	return getSingleValueSQL($sql);
}
function getExpenseInvoiceAmount($pos_general_journal_id)
{
	
	$sql = "SELECT entry_amount FROM pos_general_journal WHERE pos_general_journal_id = $pos_general_journal_id";
	return getSingleValueSQL($sql);
}
function getExpenseInvoiceDiscountApplied($pos_general_journal_id)
{
	$sql = "SELECT discount_applied FROM pos_general_journal WHERE pos_general_journal_id = $pos_general_journal_id";
	return getSingleValueSQL($sql);
}
?>