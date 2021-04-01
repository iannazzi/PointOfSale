<?php 
/*
	Craig Iannazzi 8-9-12
	
*/
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
$page_title = 'ApplyCreditMemo';
require_once ('../accounting_functions.php');

// this is all wrong...
// what I want is a list of available credit memos, including any this one touches
//available credit memos are ones that
	//a are already applied to this
	//b are not fully used
// then I want to be able to apply each amount.

//you need to be looking at an invoice to apply the amount..


$pos_purchases_journal_id = getPostOrGetID('pos_purchases_journal_id');
$complete_location = '../PurchaseJournal/view_purchase_invoice_to_journal.php?pos_purchases_journal_id='.$pos_purchases_journal_id;
$cancel_location = $complete_location .'&message=Canceled';
//$pos_credit_memo_id = getPostOrGetDataIfAvailable('pos_credit_memo_id');
$purchases_journal_data = getPurchaseJournalData($pos_purchases_journal_id);
$pos_manufacturer_id = $purchases_journal_data[0]['pos_manufacturer_id'];
$credit_memos = getOpenCreditMemos($pos_manufacturer_id, $pos_purchases_journal_id);
$current_credit_memos_applied = getCurrentlyAppliedCreditMemos($pos_purchases_journal_id);

//combine them
$combined_memos = array();
$counter = 0;
for($i=0;$i<sizeof($current_credit_memos_applied);$i++)
{
	$combined_memos[$counter] = $current_credit_memos_applied[$i];	
	$counter++;
}
for($i=0;$i<sizeof($credit_memos);$i++)
{
	$bln_found = false;
	for($j=0;$j<sizeof($current_credit_memos_applied);$j++)
	{
		if($credit_memos[$i]['pos_purchases_journal_credit_memo_id'] == $current_credit_memos_applied[$j]['pos_purchases_journal_credit_memo_id'])
		{
			$bln_found = true;
			//$credit_memos[$i]['applied_amount'] = $current_credit_memos_applied[$j]['applied_amount'];
			//$credit_memos[$i]['total_applied_amount'] = $credit_memos[$i]['total_applied_amount'];
		}
	}
	if (!$bln_found)
	{
		$combined_memos[$counter] = $credit_memos[$i];
		$combined_memos[$counter]['applied_amount'] = 0;	
		$counter++;
	}
}

/*
$invoice_amount = $purchases_journal_data[0]['invoice_amount'];
$discount_amount = $purchases_journal_data[0]['discount_applied'];
$invoices[0]['pos_purchases_journal_id'] = $pos_purchases_journal_id;
$payment_applied = getInvoicePaymentApplied($pos_purchases_journal_id, 'PURCHASES JOURNAL');
$credit_memos_applied = getCreditMemosAppliedToPurchasesInvoice($pos_purchases_journal_id);

$data_table_def = array( 
						
						
						array('db_field' =>  'pos_purchases_journal_id',
								'caption' => 'Journal ID',
								'type' => 'input',
								'tags' => ' readonly = "readonly"  ',
								'validate' => 'none',
								'value' => $pos_purchases_journal_id),
						array('db_field' =>  'invoice_number',
								'caption' => 'Invoice Number',
								'type' => 'input',
								'tags' => ' readonly = "readonly"  ',
								'validate' => 'none',
								'value' => $purchases_journal_data[0]['invoice_number']),
						array('db_field' =>  'invoice_total',
								'caption' => 'Invoice Total',
								'type' => 'input',
								'tags' => ' readonly = "readonly"  ',
								'value' => $invoice_amount,
								'validate' => 'none',
								'round' => 2),	
						array('db_field' =>  'discounts_applied',
								'caption' => 'Discounts Total',
								'type' => 'input',
								'tags' => ' readonly = "readonly"  ',
								'value' => $discount_amount,
								'validate' => 'none',
								'round' => 2),
						array('db_field' =>  'credit_memos_applied',
								'caption' => 'Credits Already Applied',
								'type' => 'input',
								'tags' => ' readonly = "readonly"  ',
								'value' => $credit_memos_applied,
								'validate' => 'none',
								'round' => 2),
						array('db_field' =>  'payments_applied',
								'caption' => 'Payments Total',
								'type' => 'input',
								'tags' => ' readonly = "readonly"  ',
								'value' => $payment_applied,
								'validate' => 'none',
								'round' => 2),
						array('db_field' => 'credit_memo_id',
								'type' => 'select',
								'caption' => 'Credit Memo To Apply',
								'html' => createCreditMemoSelect('credit_memo_id', $pos_manufacturer_id, 'false', 'off', ' onchange="updateAmountDue()" '),
								'value' => 'false',
								'validate' => 'none'),	
											
						array('db_field' =>  'credit_memo_applied',
								'caption' => 'Credit Amount To Apply',
								'type' => 'input',
								'tags' => ' onchange="needToConfirm=true;updateRemainder()" ',
								'validate' => 'number'),
						array('db_field' =>  'credit_memo_remainder',
								'caption' => 'Credit Amount Remaining',
								'type' => 'input',
								'tags' => ' readonly = "readonly"  '),
								
						);	
														
$big_html_table = convertTableDefToHTMLForMYSQLInsert(array($data_table_def));
$big_html_table .= createHiddenInput('pos_manufacturer_id', $pos_manufacturer_id);
$html = '<p>Apply Credit Memo\'s to Invoices For '.getManufacturerName($pos_manufacturer_id).'</p>';
$html .='<script src="apply_credit_memo_to_invoices.js"></script>'.newline();
$html .= '<script>var credit_memos = ' . json_encode($credit_memos) . ';</script>';
$html .= '<script>var open_invoices = ' . json_encode($open_invoices) . ';</script>';

$form_handler = 'apply_credit_memo_to_invoices.form.handler.php';
$table_array = array($data_table_def);
$html .= createMultiPartFormForMultiMYSQLInsert($table_array, $big_html_table, $form_handler, $complete_location, $cancel_location);
//$html .= '<script>document.getElementsByName("credit_pos_account_id")[0].focus();</script>';
//$html .= '<script> window.onload= updateAmountDue();</script>';*/

$table_def_array = createApplyCreditMemoTableARrayDef($pos_purchases_journal_id);
$table_def_array_with_data = loadMYSQLArrayIntoTableArray($table_def_array, $combined_memos);
$class = "mysql_data_array_table";
$html_table = createMYSQLArrayHTMLTable($table_def_array_with_data, $class, 'po_table');
//add script elements here
$html_table .= '<script src="apply_invoice_to_purchase_orders.js"></script>';
$html_table .= '<script>var applied_column = "' . 5 . '";</script>';
$html_table .='<script>var table_id = "' . 'po_table' . '";</script>';
//$html_table .= '<script>var mfgIdColumn = "' . $mfgIdColumn . '";</script>';
$html_table .= createHiddenInput('pos_purchases_journal_id', $pos_purchases_journal_id);

$form_handler = 'apply_credit_memo_to_invoices.form.handler.php';
if(sizeof($combined_memos)>0)
{
$html = '<p>Invoice Total is $' .number_format($purchases_journal_data[0]['invoice_amount']).'. Choose amounts to apply from the following Credit Memos. Enter 0 to remove the credit memo from the invoice.</p>';
$html .= createFormForMYSQLArrayInsert($table_def_array_with_data, $html_table, $form_handler, $complete_location, $cancel_location);
}
else
{
	$html = '<p class="error">There are no credit memo\'s to apply</p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back" onclick="window.location = \''.$complete_location.'\'" />';
}



include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
function createApplyCreditMemoTableARrayDef()
{
	$array_table_def= array(	
					
					array(	'th' => 'PO System ID',
			 				'type' => 'hidden_input',
							'mysql_result_field' => 'pos_purchases_journal_credit_memo_id',
							'mysql_post_field' => 'pos_purchases_journal_credit_memo_id'),
					array(	'th' => 'Invoice Number',
			 				'type' => 'td',
							'mysql_result_field' => 'credit_memo_number',
							'mysql_post_field' => ''),
					array(
							'th' => 'Credit Amount',
							'mysql_result_field' => 'credit_amount',
							'type' => 'td',
							'total' => 2,
							'round' => 2,
							'mysql_post_field' => ''),
					array(
							'th' => 'Total Amount Applied',
							'mysql_result_field' => 'total_applied_amount',
							'type' => 'td',
							'total' => 2,
							'round' => 2,
							'mysql_post_field' => ''),
					array(
							'th' => 'Amount To Apply',
							'mysql_result_field' => 'applied_amount',
							'type' => 'input',
							'tags' => ' class="highlight" onchange="needToConfirm=true;updateFooter(this);"',
							'total' => 2,
							'round' => 2,
							'mysql_post_field' => 'applied_amount')
					);
	return $array_table_def;
}
?>

