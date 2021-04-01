<?php
/*
	invoice.php
	
	//the flow....
	start an invoice:
	Create an invoice status INIT
	Login
	Select sales associate
	Select Customer
	Update the invoice set it to draft
	Edit contents
	
	Options: Edit/Payments
	
	Done with the content editing and Pay: Invoice Closed, payment_status PAID
	Options: Print invoice
	
	Done with content editing customer puts it onto thier account....
	Options: Print Invoice, Payments
	
	On this there are two options:
	Edit contents
	OR 
	View
	
	The edit contents will use one javascript file for all the table functions
	View will use a separate javascript file for the payments and invoice printing.... 
	
	
*/
//this is the javascript versions.....
$retail_sales_javascript_version = 'retail_sales_invoice.edit_contents.2015.06.11.js';
$payments_javascript = 'retail_sales_invoice.payments.2015.10.24.js';
//$product_lookup_javascript = 'retail_sales_invoice.ajax_product.2013.06.26.js';
$binder_name = 'POS';
$access_type = 'WRITE';
$page_title = 'Sales Invoice';
require_once('retail_sales_invoice_functions.php');
//need a chack on max_input_vars
$max_input_vars = ini_get('max_input_vars');
if (ini_get('max_input_vars') < 10001)
{
	trigger_error('max_input_vars is set to low. Check php.ini. It should be around 50000 or more but it is set at ' .$max_input_vars);
	
}

if (isset($_GET['type']))
{
	$type = $_GET['type'];
}
else if (isset($_POST['type']))
{
	$type = $_POST['type'];
}
else
{
	//error no type
	trigger_error('missing type');
}

//need a terminal check here...
$pos_terminal_id = terminalCheck();

if ($type == 'submit')
{
	//we are actually doing this in save draft and go in the javascript....  but I keep it here in case.
	$db_table = 'pos_sales_invoice';
	$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	unlock_entry($db_table, $key_val_id);
	$go_url = 'retail_sales_invoice.php?type=view&pos_sales_invoice_id='.$pos_sales_invoice_id;
	header('LOCATION: '.$go_url);
	exit();
}
else if ($type =='SimpleInit')
{
	//here we init the invoice
	
	
	if(isset($_GET['pos_customer_id']))
	{
		$pos_customer_id = scrubInput($_GET['pos_customer_id']);
	}
	else if (isset($_GET['pos_sales_return_id']))
	{
		$pos_customer_id = getCustomerFromSalesInvoice(scrubInput($_GET['pos_sales_return_id']));
	}
	else if (isset($_GET['first_name']))
	{
		$customer_insert['first_name'] = scrubInput($_GET['first_name']);
		$customer_insert['last_name'] = scrubInput($_GET['last_name']);
		$customer_insert['phone'] = scrubInput($_GET['phone']);
		$customer_insert['email1'] = scrubInput($_GET['email']);
		$pos_customer_id = simpleInsertSQLReturnID('pos_customers', $customer_insert);
		
	}
	else
	{
		$pos_customer_id = 0;
	}

	if(isset($_GET['pos_user_id']))
	{	
		$pos_user_id = scrubInput($_GET['pos_user_id']);
	}
	else
	{
		$pos_user_id = $_SESSION['pos_user_id'];
	}
	
	$dbc = startTransaction();
	//this is a "secure" user id....
	$insert['pos_user_id'] = $pos_user_id;
	//the store ID has to come from the terminal...
	$insert['pos_store_id'] = getTerminalStoreId($pos_terminal_id);
	$insert['invoice_date'] = getCurrentTime();
	$insert['invoice_status'] = 'INIT';
	$insert['payment_status'] = 'UNPAID';
	$insert['pos_customer_id'] = $pos_customer_id;
	$insert['pos_terminal_id'] = $pos_terminal_id;
	$pos_sales_invoice_id = simpleTransactionInsertSQLReturnID($dbc,'pos_sales_invoice', $insert);
	//now get the next maximum value....right?
	//$max = getTransactionSingleValueSQL($dbc, "SELECT MAX(invoice_number) FROM pos_sales_invoice");
	//runTransactionSQL($dbc, "UPDATE pos_sales_invoice SET invoice_number = $max +1 WHERE pos_sales_invoice_id =  $pos_sales_invoice_id");
	simpleCommitTransaction($dbc);
	
	
	
	if (isset($_GET['pos_sales_return_id']))
	{
				
		$complete_location = 'retail_sales_invoice.php?type=edit&pos_sales_return_id='.scrubInput($_GET['pos_sales_return_id']).'&pos_sales_invoice_id='. $pos_sales_invoice_id;
	}
	else if($pos_customer_id != 0)
	{
		$complete_location = 'retail_sales_invoice.php?type=edit&pos_sales_invoice_id='. $pos_sales_invoice_id;
	}
	else
	{
		$complete_location = 'retail_sales_invoice.php?type=edit&select_customer=true&pos_sales_invoice_id='. $pos_sales_invoice_id;
	}
	
	
	header('Location: '.$complete_location );
	exit();
}
else if ($type =='FullInit')
{
	//here we init the invoice
}
else if($type == 'edit')
{
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
	$select_customer =  (isset($_GET['select_customer']))? true : 0;

	
	
	$db_table = 'pos_sales_invoice';
	$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	$invoice_status = getInvoiceStatus($pos_sales_invoice_id);

	$payment_status = getSingleValueSQL("SELECT payment_status FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");


	$html = '<link type="text/css" href="'.$css_styles_version.'" rel="Stylesheet"/>'.newline();
	$html .=  '<script src="'.$payments_javascript.'"></script>'.newline();
	$html .=  '<script src="'.$retail_sales_javascript_version.'"></script>'.newline();
	$html .=  '<script src="'.DYNAMIC_TABLE_OBJECT_V3.'"></script>'.newline();
	$html .= '<script>var type = "'.$type. '";</script>';

	//$html .=  '<script src="'.$product_lookup_javascript.'"></script>'.newline();
	$html .= '<script>var pos_sales_invoice_id = '.$pos_sales_invoice_id. ';</script>';
	$html .= '<script>var select_customer = '.$select_customer. ';</script>';
	//check that this entry is not 'closed'
	checkForClosedInvoice($pos_sales_invoice_id);

	if(test_cc_proccess('set_user_lock')) 
	{
		check_lock($db_table, $key_val_id,POS_ENGINE_URL .'/sales/POS_V1/retail_sales_invoice.php?type=edit&pos_sales_invoice_id='.$pos_sales_invoice_id, getBinderURL($binder_name) . '?message=canceled');
	}
	//lock the entry
	lock_entry($db_table, $key_val_id);
	
	$page_title = 'Sales Invoice ' .$pos_sales_invoice_id;
	//if a customer id is passed in, then update the sales invoice with the customer ID.
	if(isset($_GET['pos_customer_id']))
	{
		$pos_customer_id = scrubInput($_GET['pos_customer_id']);
		$cust_update['pos_customer_id'] = $pos_customer_id;
		$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $cust_update);
	}
	else
	{
		$pos_customer_id = getCustomerFromSalesInvoice($pos_sales_invoice_id);
	}
	//come in through a return????
	if (isset($_GET['pos_sales_return_id']))
	{
		$pos_sales_return_id = scrubInput($_GET['pos_sales_return_id']);
		$returns_data = getReturnInvoiceData($pos_sales_return_id);
		$html .= '<script>var returns_data='.json_encode($returns_data).';</script>';

	}
	else
	{
		$pos_sales_return_id = 0;
	}
	$html .= '<script>var pos_sales_return_id='.$pos_sales_return_id.';</script>';
	if(isset($_GET['pos_address_id']))
	{
		$pos_address_id = scrubInput($_GET['pos_address_id']);
		$add_update['pos_address_id'] = $pos_address_id;
		$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $add_update);
	}
	$complete_location = 'list_retail_sales_invoices.php';
	$cancel_location = 'list_retail_sales_invoices.php';

	$payment_location = 'add_customer_payment.php?pos_sales_invoice_id='.$pos_sales_invoice_id;
	
	



	
	$html .= '<div class = "invoice_class">';
//************************** INVOICE OVERVIEW ***********************************************
	$html .= ' <div class = "retail_sales_invoice_div">';
	$html .= createInvoiceHtmlTable($pos_sales_invoice_id);
	$html .= '<script>var invoice_date = "' .getSalesInvoiceDateFromDatetime($pos_sales_invoice_id) .'";</script>';
	//$html .= '<script>var tax_method = "' . getSalesInvoiceTaxCalculationMethod($pos_sales_invoice_id) . '";</script>';
	$html .= '<script>var tax_method = "average";</script>';
	$html .= '</div>';
//************************** CUSTOMER  ***********************************************
	$html .= ' <div class = "customer_invoice_div">';
	$html .= createCustomerHtmlTable($pos_customer_id, $pos_sales_invoice_id);
	$html .= '</div>';
//************************** PRODUCT LOOKUP TABLE  ***********************************************
	$html .= ' <div class = "product_lookup_div">';
	$html .= '<table class = "product_lookup_outline" style="width:100%;">';
	$html .= '<TR><td>';
	$html .= POSproductLookUpTable();
	$html .= '</td>';
	$html.='</TR>';
	$html .= ' </table>';
	$html .= '</div>';



//************************** INVOICE CONTENTS ***********************************************

	//invoice contents table
	$html .= ' <div class = "invoice_contents">';
	$invoice_contents = siwtchBRToComma(getInvoiceContents($pos_sales_invoice_id));
	$invoice_table_name = 'invoice_table';
	$invoice_contents_col_def = createRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id, $invoice_table_name);	
	$buttons = array(
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:60px;",
								'value'=>"Add Row", 
								'onclick' => $invoice_table_name.'.addRow();document.getElementById(\'barcode\').focus();'
								),
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:60px;",
								'value'=>"Delete Row", 
								'onclick' => $invoice_table_name.'.deleteRow();document.getElementById(\'barcode\').focus();'
								),

								);
	$invoice_contents_row_def = array(
								'class' =>"thin_button",
								'style' =>"button",
								'onclick' => 'this.alert("clickedrow");document.getElementById(\'barcode\').focus();'
								
								);
	
	
	$html .= createDynamicTableReuseV3($invoice_table_name, $invoice_contents_col_def, $invoice_contents, ' class="dynamic_contents_table" style="width:100%" ', $buttons);
	$html .= '</div>';

	
	
//************ TOTALS ***************************************************
	$html .= '<div class="invoice_footer">';
	$footer_table_name = 'invoice_footer';
	$footer_table_def = createRetailSalesInvoiceContentsFooterTableDef($pos_sales_invoice_id);
	$footer_table_tags = ' class="invoice_summary_table" ';
	$footer_data = array();
	$html .= createHorizontalInputHTMLTable($footer_table_name, $footer_table_def, $footer_data, $footer_table_tags);
	$html.='</div>';
//******************** PROMOTIONS ***************************************************//
	$html .= '<div class="promotion_table" >';
		//promotion table
		$html .= '<h3>Promotions</h3>';
		$promotion_table_name = 'promotion_table';
		$promotion_table_def = createRetailSalesPromotionsTableDef($promotion_table_name);
		$promotion_data = getSQL("SELECT pos_promotion_id
								 FROM pos_sales_invoice_promotions
								WHERE pos_sales_invoice_id = $pos_sales_invoice_id
								ORDER BY row_number ASC");
								
		//each element needs to have more data..... i put it in 'returned data'... that was probably a bad idea....
		
		$promotion_data_array = array();
		for($i=0;$i<sizeof($promotion_data);$i++)
		{
			$promotion_data_array[$i] = getPromotionData($promotion_data[$i]['pos_promotion_id'], 0);
		}	
		//preprint($promotion_data_array);
		$buttons = array(
							/*array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:60px;",
								'value'=>"Copy Rows", 
								'onclick' => $promotion_table_name.'.copyRow(\'bottom\');document.getElementById(\'barcode\').focus();'
								),
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:100px;",
								'value'=>"Move Row(s) Up", 
								'onclick' => $promotion_table_name.'.moveRowUp();document.getElementById(\'barcode\').focus();'
								),
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:100px;",
								'value'=>"Move Row(s) Down", 
								'onclick' => $promotion_table_name.'.moveRowDown();document.getElementById(\'barcode\').focus();'
								),*/
							array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:100px;",
								'value'=>"Delete Row", 
								'onclick' => $promotion_table_name.'.deleteRow();document.getElementById(\'barcode\').focus();'
								),
						/*	array('class' =>"thin_button",
								'type' =>"button",
								'style' =>"width:100px;",
								'value'=>"Delete All Rows", 
								'onclick' => $promotion_table_name.'.deleteAllRows();document.getElementById(\'barcode\').focus();'
								),*/
								);
		
		$html .= createDynamicTableReuseV3($promotion_table_name, $promotion_table_def, $promotion_data_array, ' class="dynamic_contents_table"  ', $buttons);
		//for the promotions we need to drop in the category array... each category should list all sub categories...
	
	
	//preprint(createCategorySubArray());
	$html .= '<script> var cat_array = ' .json_encode(createCategorySubArray()) . ';</script>';
	

		// looks like this 
	$html .= '</div>';

	$html .= '<div class = "invoice_options">';
	$special_order = getSingleValueSQL("SELECT special_order FROM pos_sales_invoice WHERE pos_sales_invoice_id=$pos_sales_invoice_id");
	$follow_up = getSingleValueSQL("SELECT follow_up FROM pos_sales_invoice WHERE pos_sales_invoice_id=$pos_sales_invoice_id");
	$html .= '<p><input type="checkbox"  id="special_order"  onclick="barcodeFocus();" ';
	if($special_order) $html .= ' checked ';
	$html .= '/>Create Customer Order Card</p>';
	$html .= '<p><input type="checkbox"  id="follow_up"  onclick="barcodeFocus();" ';
	if($follow_up) $html .= ' checked ';
	$html .= '/>Follow Up Requested</p>';
	$html .= '</div>';


	$html .= '</div>'; //end of invoice div

	//*************************** BUTTONS **************************************
		$html .= '<div class="invoice_buttons" >';
		
		//WE HAVE TO UNLOCK THE INVOICE AT THIS POINT... BEST WAY: AJAX OR BOUNCE OFF THE SERVER...
		//WE HAVE TO RELOAD ANYWAY SO SEND IT TO A FORM HANDLER
		
	//add the return button....
	//$return_url = 'returns.php?pos_original_sales_invoice_id='.$pos_sales_invoice_id;
	$html .=  '<INPUT class = "button" type="button" style="width:150px" value="Customer Deposit" onclick="cusomter_deposit()"/>'.newline();
	$html .=  '<INPUT class = "button" type="button" style="width:100px" value="Returns" onclick="returns()"/>'.newline();
	$html .=  '<INPUT class = "button" type="button" style="width:100px" value="Service" onclick="addService()"/>'.newline();
	$html .=  '<INPUT class = "button" type="button" style="width:100px" value="Shipping" onclick="addShipping()"/>'.newline();
	$html .=  '<INPUT class = "button" type="button" style="width:100px" value="Promotion" onclick="addPromotion()"/>'.newline();
		$view_url = 'retail_sales_invoice.php?type=view&pos_sales_invoice_id='.$pos_sales_invoice_id;
		$html.='</div>';
		
		$html .= '<div class="save_invoice_buttons" >';
		
		$html .=  '<INPUT class = "button" type="button"  value="Save Invoice" onclick="saveDraft()" />'.newline();
		$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Exit" onclick="exitInvoice(\''.$view_url.'\')" />'.newline();
		$html .=  '<INPUT class = "button" type="button" style="width:200px" value="Continue To Payments" onclick="continueToPayments(\''.$view_url.'\')" />';
		
		$html.='</div>';


	//#####################################INSTRUCTIONS ########################################
	
	 $html .='<div class="instructions">';
	 $html.='<h2>Point of Sale Instructions and Hints</h2>';
	 $html.='<p>The barcode field works for everything.... Products, Promotions, and Store Credit Cards. It should always be in focus.</p>';
	 $html.='<p>To take a deposit for an item simply assign the deposit amount to a store credit card. Keep track of the card and later redeem the card.</p>';
	 $html.='<p>For custom orders, where a customer is ordering an item that is not in stock, do the following:
	 <ul><li>Enter the items in to get the total. </li>
	 <li>Scan in a store credit card number, enter the total amount to the card. </li>
	 <li> remove or zero out the items needed to order</li>
	 </p>';
	  $html.='<p>Customer Deposit can be opened using barcode DEP or dep</p>';
	 
	 

	 
	$html .='</div>';
	//$html.='<script>barcodeFocus();</script>';
	
	
		//*****************************Modal forms ***********************
	$html .= customerAddEditModal($pos_customer_id);
	$html.= customerSelectModalForm($pos_customer_id);
	$html.= customerAddressSelectModalForm($pos_customer_id);
	$html.= customerDepositModalForm($pos_customer_id);	
	$html.= giftCardModalForm();	
	$html.=  returnsInvoiceEntryModalForm();
	$html.= returnsInvoiceSelectModalForm();
	$html.= returnsProductSelectModalForm();

	

	
}
else if($type == 'view')
{
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
	$db_table = 'pos_sales_invoice';
	$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	$invoice_status = getSingleValueSQL("SELECT invoice_status FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	$payment_status = getSingleValueSQL("SELECT payment_status FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");


	$html = '<link type="text/css" href="'.$css_styles_version.'" rel="Stylesheet"/>'.newline();
	$html .=  '<script src="'.$payments_javascript.'"></script>'.newline();
	$html .=  '<script src="'.$retail_sales_javascript_version.'"></script>'.newline();
	//$html .=  '<script src="'.$product_lookup_javascript.'"></script>'.newline();
	$html .= '<script>var pos_sales_invoice_id = '.$pos_sales_invoice_id. ';</script>';
		$html .=  '<script src="'.DYNAMIC_TABLE_OBJECT_V3.'"></script>'.newline();

	$html .= '<script>var type = "'.$type. '";</script>';
	
	if(isset($_GET['pos_customer_id']))
	{
		$pos_customer_id = scrubInput($_GET['pos_customer_id']);
		$update['pos_customer_id'] = $pos_customer_id;
		$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $update);
	}
	else
	{
		$pos_customer_id = getCustomerFromSalesInvoice($pos_sales_invoice_id);
	}
	

	$total_due = getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id);
	$total_payments = getTotalPayments($pos_sales_invoice_id);
	$amount_due = $total_due - $total_payments;
	$html.= '<script>var amount_due = ' . $amount_due . ';</script>';
	$html .=  '<script>var pos_sales_invoice_id = '.$pos_sales_invoice_id.'</script>'.newline();
	$html .= '<div class = "invoice">';
	
	//looks good.
	$html .= createRetailSalesInvoiceView($pos_sales_invoice_id);
	//need to be able to change or add customer here.... update email address... phone etc. 
	//but not change address....
	//this would be nice to load my code....
	//************************** CUSTOMER  ***********************************************
	$html .= '<div class = "invoice_class">';
		$html .= ' <div class = "customer_invoice_div">';
		$html .= createCustomerHtmlTable($pos_customer_id, $pos_sales_invoice_id);
		$html .= customerAddEditModal($pos_customer_id);
		$html.= customerSelectModalForm($pos_customer_id);
		$html .= '</div>';

		$html .= ' <div class = "invoice_contents">';
		$html .= createRetailSalesInvoiceContentsView($pos_sales_invoice_id);
		$html.= '</div>';
		$html .= '<div class="invoice_footer">';
		$html .= createRetailSalesInvoiceFooterView($pos_sales_invoice_id);
		$html.= '</div>';
		$html .= '<div class="promotion_table" >';
		$html .= createRetailSalesInvoicePromotionsView($pos_sales_invoice_id);
		$html.= '</div>';

		$html .= '<div class = "invoice_options">';
		$special_order = getSingleValueSQL("SELECT special_order FROM pos_sales_invoice WHERE pos_sales_invoice_id=$pos_sales_invoice_id");
		$follow_up = getSingleValueSQL("SELECT follow_up FROM pos_sales_invoice WHERE pos_sales_invoice_id=$pos_sales_invoice_id");
		$html .= '<p><input type="checkbox"  id="special_order"  onclick="updateSpecialOrder();" ';
		if($special_order) $html .= ' checked ';
		$html .= '/>Create Customer Order Card</p>';
		$html .= '<p><input type="checkbox"  id="follow_up"  onclick="updateFollowUp();" ';
		if($follow_up) $html .= ' checked ';
		$html .= '/>Follow Up Requested</p>';
		$html .= '</div>';
	
	$html .='</div>'; //invocie_class
	

	
//******************** BUTTONS *******************************
		
		
	/*
		we can either edit contents
		take payments
		or print an invoice
		
	*/
	
	/*
		the invoice closes when the payments match the total... so a zero invoice auto closes?
		
	*/
	//echo 'invoice_status ' . $invoice_status;
	//contents
	$html .= '<div class="invoice_buttons" >';

	if($invoice_status == 'DRAFT' OR $invoice_status == 'INIT' OR $invoice_status == 'EXITED')
	{
		
		$html .=  '<INPUT class = "button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="EDIT CONTENTS" onclick="open_win(\'retail_sales_invoice.php?type=edit&pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
		
	}
	else
	{
		 if (checkIfUserIsAdmin($_SESSION['pos_user_id']))
		{
		
		$html .=  '<p><INPUT class = "admin_button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="EDIT CONTENTS" onclick="open_win(\'retail_sales_invoice.php?type=edit&pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
		}
		
	}
	$html .= '</div>';
	
	if($payment_status == 'UNPAID')
	{
	if($invoice_status != 'EXITED' && $invoice_status != 'INIT')
	{
	{
		if($total_due>=0)
		{
		//set up the modal forms
		$html.= cashModalForm($pos_terminal_id,$amount_due);
		$html.= ccModalForm($pos_terminal_id,$amount_due);
		$html.= checkModalForm($pos_terminal_id,$amount_due);
		$html.= storeCreditModalForm($pos_terminal_id,$amount_due);
		// if user has access to this then we can use "other" account....
		// other account would be customer account, "pending charitable contibutions" account....etc...
		// Pending charitable contributions account is "fake cash account" that really has no balance.
		// or we can try a non-posting account?
		$html.= otherModalForm($pos_terminal_id,$amount_due);

		//payments buttons
		
		
			//$html .= '<div style="clear:both"></div>';
		$html .= '<div class = "payment_buttons" id="payment_buttons">';
			//add the editable payment table
			
			//$html .= 'Payment options:';
			$html .=  '<INPUT class = "button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="CASH" onclick="cashDialog()" />';
			$html .=  '<INPUT class = "button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="CREDIT/DEBIT" onclick="ccDialog()" />';
			$html .=  '<INPUT class = "button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="CHECK" onclick="checkDialog()" />';
			$html .=  '<INPUT class = "button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="STORE CREDIT/GIFT CARD" onclick="storeCreditDialog()" />';
		
			if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_allow_other_payment'))
			{
		
		$html .=  '<INPUT class = "admin_button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="INTERNAL EXPENSE PAYMENT" onclick="otherDialog()" />';
			}
			$html .= '</div>';
		}
		
		else //if ($total_due<0)
		{
	
	
	
			//refund area
	
	
			$html.= cashRefundModalForm($pos_terminal_id,$amount_due);
			$html.= ccRefundModalForm($pos_sales_invoice_id, $pos_terminal_id,$amount_due);
			$html.= checkRefundModalForm($pos_terminal_id,$amount_due);
			$html.= storeCreditRefundModalForm($pos_sales_invoice_id,$amount_due);
			// if user has access to this then we can use "other" account....
			// other account would be customer account, "pending charitable contibutions" account....etc...
			// Pending charitable contributions account is "fake cash account" that really has no balance.
			// or we can try a non-posting account?
			// $html.= otherRefundModalForm($pos_terminal_id,$amount_due);
	
			/*
				refund usually is accompianed by a receipt...
				usually one refund per receipt? 
				look up the original payment info
				provide buttons based on the payment....
		
				For each invoice content
					if the qty is < 0
						look for the original content id, get the invoice id, and then get the payment amounts and types
				
				say dress $300 from one invoice
				bra $50 from a second invoice
				purchase new bra $100
				refund is then $250.
				so it might look like payments of invoice 1 cash $250, credit $100 store credit $100.
				invoice 2 credit cash $25 cedit card 100 gift card 50
		
				max cash refund of $275 or whatever  max_cash_refund limit we place on system
				max check refund of $0 or $275 to cover cash... automatically insert payment to pos_refund_checking_account_id:
		
				damn shiela.... 
				debit Sales Revenue
				credit	checking account....
		
		
		
				max credit refund is $400
		
		
				cash - same as payment, except will be negative.... 
				check - ehhhhhhh we would need to write a check, so the account would need to be from a checking account...
				credit - offline the code will be identical
						online - the code is different.....
				store credit - code is different.....
			*/
	
			//refund buttons
			if($payment_status == 'UNPAID')
			{
				//$html .= '<div style="clear:both"></div>';
				$html .= '<div style="float:right;" id="refund_buttons">';
				//add the editable payment table
				//$html .= createRetailSalesInvoicePaymentsView($pos_sales_invoice_id);
		
		
				if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_allow_refunds'))
				{
		
					$html .=  '<INPUT class = "button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="ISSUE STORE CREDIT" onclick="storeCreditRefundDialog()" />';
	
					/*if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_allow_other_payment'))
					{
	
				$html .=  '<INPUT class = "admin_button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="INTERNAL EXPENSE PAYMENT" onclick="otherDialog()" />';
					}
					*/
					if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_allow_cc_return'))
					{
							$html .=  '<INPUT class = "button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="REFUND TO CREDIT/DEBIT" onclick="ccRefundDialog()" />';
					}
					else
					{
						$html .=  'Contact manager to refund to CC. ';
					}
					if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_allow_advanced_return'))
					{
									$html .=  '<INPUT class = "button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="REFUND VIA CASH" onclick="cashRefundDialog()" />';
	
					$html .=  '<INPUT class = "button" type="button"  style="width:200px;margin: 2px 4px 6px 0px;" value="REFUND VIA CHECK" onclick="checkRefundDialog()" />';
					}
					else
					{
						$html .=  'Contact manager to refund via cash/check. ';
					}
				}
				else
				{
					$html .= 'The group ' . getUserFullName($_SESSION['pos_user_id']) . ' belongs to does not allow refunding. Contact Manager for assistance.';
				}
				$html .= '</div>';
		
			}
	
	
	
		}
		
		//to open the invoice the invoice will have to go onto an 'account'
		//to print an invoice the invoice needs to be opend or closed... OPEN meaning it went onto an account... currently not using open
		
	}}
	}
	

	$html .= '<div class="payments">';
	$html .= createRetailSalesInvoicePaymentsView($pos_sales_invoice_id);
	$html .= '</div>';

	//invoice printing....
	if($invoice_status == 'OPEN' OR $invoice_status == 'CLOSED')
	{

		$html.= '<div id="save_alert" ></div>';
		$html .= '<div id = "print_button_div" style="float:right;">';
		$html .=  '<INPUT class = "button" id="store_print_button" type="button" style="width:300px" value="Print Store Copy To '.getPrinterName(getDefaultTerminalPrinter($pos_terminal_id)).' At ' .  getPrinterLocation(getDefaultTerminalPrinter($pos_terminal_id)) . '" onclick="sendInvoiceToPrinter(\'store\','.$pos_sales_invoice_id.')"
		 />'.newline();
		$html .=  '<INPUT class = "button" id="customer_print_button" type="button" style="width:300px" value="Print Invoice To '.getPrinterName(getDefaultTerminalPrinter($pos_terminal_id)).' At ' .  getPrinterLocation(getDefaultTerminalPrinter($pos_terminal_id)) . '" onclick="sendInvoiceToPrinter(\'customer\','.$pos_sales_invoice_id.')"
		 />'.newline();
$html .=  '<INPUT class = "button" id="customer_print_button" type="button" style="width:300px" value="Print Gift Invoice To '.getPrinterName(getDefaultTerminalPrinter($pos_terminal_id)).' At ' .  getPrinterLocation(getDefaultTerminalPrinter($pos_terminal_id)) . '" onclick="sendInvoiceToPrinter(\'gift_receipt\','.$pos_sales_invoice_id.')" >';
		$html .=  '<INPUT class = "button" type="button"  id="email_button" style="width:300px" value="EMAIL INVOICE" onclick="emailInvoice('.$pos_sales_invoice_id.')"
		 />'.newline();
		 
	
		 
		 
		 $html .=  '<INPUT class = "button" id="customer_print_button" type="button" style="width:300px" value="Open Invoice PDF" onclick="openInvoiceInline('.$pos_sales_invoice_id.')" />'.newline();
		 $html.='</div>';
		 
		 

	
	 
	
		  $html .='</div>';
	}

			

}
else
{
	
	$html = 'Not A Valid Type';
	
}

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
function giftCardModalForm()
{
	//Assign a gift card value

	$html = '<div id="gift-card-dialog-form" title="Assign A Gift Card Value">';
	$html .= '<table class="singleTable" style="width:100%;"><tr>';
	$html .='<th style="text-align:left;">Amount</th>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="gift_card_amount"  value="0.00" ></td>';
	$html .= '</table>';
	$html .= '<div style = "text-align: center;"  id="loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}
function returnsInvoiceEntryModalForm()
{
	$html = '<div id="returnsInvoiceEntryModalForm" title="Returns">';
	// go directly to the invoice id or....
	
	//two options, enter the invoie id or search...
	$html .= '<div id="return_invoice_id" >';
	$html .= '<table class="singleTable" style="width:100%;">';
	$html .= '<tr>';
	$html .='<th style="text-align:left;">Invoice Number</th>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="pos_return_sales_invoice_id"  value="" ></td>';
	$html .= '</tr></table>';
	$html .='<p>If the customer is missing the original invoice or invoice number, try to search for it. Without the original invoice the system may only be able to issue store credit.</p>';
	$html .= '<input type="button" id="invoice_search" class="button" style="width: 300px;" onclick="cust_search()" value="Invoice Missing - Search By Customer">';
	//can't search by credit card... $html .= '<input type="button" id="invoice_search" class="button" style="width: 300px;" onclick="cc_search()" value="Invoice Missing - Search By Credit Card">';
	// i don't think this is a good idea... $html .= '<input type="button" id="invoice_search" class="button" style="width: 300px;" onclick="product_search()" value="Invoice Missing - Search By Product Barcode">';
	$html .= '</div>';
	
	//get a list invoice id by looking up the customer, phone number, email, invoice id, product number, card number
	$html .= '<div id="return_invoice_lookup" >';
	$html .= '<table class="singleTable" style="width:100%;">';
	$html .= '<tr>';
	//$html .='<th style="text-align:left;">Invoice Number</th>';
	$html .='<th style="text-align:left;">First Name</th>';
	$html .='<th style="text-align:left;">Last Name</th>';
	$html .='<th style="text-align:left;">Email</th>';
	$html .='<th style="text-align:left;">Phone #</th>';
	//$html .='<th style="text-align:left;">Product ID #</th>';
	//$html .='<th style="text-align:left;">Credit Card #</th>'; //=> get the transaction id, find the invoice ids
	$html .= '</tr>';
	$html .= '<tr>';
	//$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="invoice_number"  value="" ></td>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="return_first_name"  value="" ></td>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="return_last_name"  value="" ></td>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="return_email"  value="" ></td>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="return_phone"  value="" ></td>';
	//$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="return_product"  value="" ></td>';
	//$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="return_cc"  value="" ></td>';
	
	$html .= '</tr></table>';
		$html .='<p>Selecing an invoice by searching by customer name, email, or phone will result in store credit refund only.  </p>';
	
//$html .= '<input type="button" id="invoice_search" class="button" style="width: 300px;" onclick="invoice_search()" value="Back">';
	$html .= '</div>';
	
	$html .= '<div id="return_invoice_CC_lookup" >';
	$html .= '<table class="singleTable" style="width:100%;">';
	$html .= '<tr>';
	$html .='<th style="text-align:left;">CC Number</th>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="return_cc"  value="" ></td>';
	$html .= '</tr></table>';
	$html .='<p>Searching by original card number can allow a credit card refund. Simply swipe the credit card or with the card number input box focused or type the number in manually. </p>';
	$html .= '<input type="button" id="invoice_search" class="button" style="width: 300px;" onclick="invoice_search()" value="Back">';

	$html .= '</div>';
	
	$html .= '<div id="return_invoice_barcode_lookup" >';
	$html .= '<table class="singleTable" style="width:100%;">';
	$html .= '<tr>';
	$html .='<th style="text-align:left;">Product Barcode Number</th>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="return_barcode"  value="" ></td>';
	$html .= '</tr></table>';
	$html .='<p>Scan Product Barcode. Selecing an invoice by searching by product will result in store credit refund only.  </p>';
	$html .= '<input type="button" id="invoice_search" class="button" style="width: 300px;" onclick="invoice_search()" value="Back">';
	$html .= '</div>';

	
	$html .= '<div style = "text-align: center;"  id="returns_invoice_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	
	
	
	
	return $html;
}
function returnsInvoiceSelectModalForm()
{
	$html = '<div id="returnsInvoiceSelectModalForm" title="Returns Invoices">';
	
	$html .= '<div id="returnsInvoicesLimitsDiv">';
	$html .= '</div>';
	$html .= '<div id="returnsInvoicesDiv">';
	
	//depending on the search type we may want different tables.....
	
	$table_id = 'returnsInvoicesTable';
	$table_def = array(	/*array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
											),*/
					array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'row_checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_id.'.setSingleCheck(this);}'
											)),
					/*array('db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
											),
					*/
					
					array(	'db_field' => 'pos_return_invoice_id',
							'caption' => 'Invoice<br>Number',
							'type' => 'input',
							'element' => 'input',
							'element_type' => 'none',
							'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
													'readOnly' => '"true"',
													'size' => '"6"',
													'tabIndex' => '"-1"')
												),
							array(	'db_field' => 'customer_info',
							'caption' => 'Customer Info',
							'type' => 'innerHTML',
							'element' => 'innerHTML',
							'element_type' => 'none',
							'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
													'onkeyup' => 'function(){'.$table_id.'.checkValidInput(this);}',
													'size' => '"4"',
													'readOnly' => 'true',
													'tabIndex' => '"-1"')
												),
					array(	'db_field' => 'products',
							'caption' => 'Products',
							'type' => 'innerHTML',
							'element' => 'innerHTML',
							'element_type' => 'none',
							'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
													'readOnly' => '"true"',
													'size' => '"40"',
													'tabIndex' => '"-1"')
												),
					array(	'db_field' => 'payments',
							'caption' => 'Payments',
							'type' => 'innerHTML',
							'element' => 'innerHTML',
							'element_type' => 'none',
							'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
													'readOnly' => '"true"',
													'size' => '"40"',
													'tabIndex' => '"-1"')
												),
											);
	
	
	$contents = array();
	
	

	$html .= createDynamicTableReuseV3($table_id, $table_def, $contents, ' class="dynamic_contents_table" style="width:100%" ');

	
	
	
	$html .= '</div>';
	$html .= '</div>';
	return $html;
	
	
}
function returnsProductSelectModalForm()
{
	$html = '<div id="returnsProductSelectModalForm" title="Return Invoice Contents">';
	
	$html .= '<div id="returnsContentsInvoiceNumberDiv">';
	$html .= '</div>';
	$html .= '<div id="returnsContentsDiv">';
	$html .= '<input type="button" id="price_adjust" class="button" style="width: 300px;" onclick="priceAdjustInvoice()" value="Price Adjust This Invoice">';
	
	$table_id = 'returnsContentTable';
	$table_def = array(	/*array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
											),*/
					array('db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
											),
					array(	'db_field' => 'return_quantity',
							'caption' => 'Return<br>Quantity',
							'type' => 'input',
							'valid_input' => '0123456789',
							'element' => 'input',
							'element_type' => 'none',
							'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
													'onkeyup' => 'function(){'.$table_id.'.checkValidInput(this);}',
													'size' => '"4"')
												),
					
					array(	'db_field' => 'pos_product_sub_id',
							'caption' => 'Barcode',
							'type' => 'input',
							'element' => 'input',
							'element_type' => 'none',
							'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
													'readOnly' => '"true"',
													'size' => '"6"',
													'tabIndex' => '"-1"')
												),
							array(	'db_field' => 'quantity',
							'caption' => 'Purchase Quantity',
							'type' => 'input',
							'valid_input' => '0123456789',
							'element' => 'input',
							'element_type' => 'none',
							'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
													'onkeyup' => 'function(){'.$table_id.'.checkValidInput(this);}',
													'size' => '"4"',
													'readOnly' => 'true',
													'tabIndex' => '"-1"')
												),
					array(	'db_field' => 'product_options',
							'caption' => 'Product Options',
							'type' => 'input',
							'element' => 'input',
							'element_type' => 'none',
							'properties' => array(	'onclick' => 'function(){'.$table_id.'.setCurrentRow(this);}',
													'readOnly' => '"true"',
													'size' => '"40"',
													'tabIndex' => '"-1"')
												),
											);
	
	
	$contents = array();
	$buttons = array();
	
	

	$html .= createDynamicTableReuseV3($table_id, $table_def, $contents, ' class="dynamic_contents_table" style="width:100%" ', $buttons);

	
	
	$html .= '</div>';
	$html .= '</div>';
	return $html;
}

//payment modal forms
function cashModalForm($pos_terminal_id,$amount_due)
{
	//CASH
	$default_account_id = getSingleValueSQL("SELECT default_cash_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	$html = '<div id="cash-dialog-form" title="Cash Payment">';
	$html .= '<table class="singleTable" style="width:100%;"><tr>';
	$html .='<th style="text-align:right;">Amount Due</th><th style="text-align:right">Cash Tender</th><th style="text-align:right">Change</th>';
	$html .= '<th>Register/Account</th>';
	$html .='</tr><tr>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="amount_due"  value="'.round($amount_due,2).'" readonly></td>';
	$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="cash_input" name="cash_input"  value ="0.00"  onkeyup="calculate_change()" onblur= "calculate_change()" onclick="this.select();"  ></td>';
	$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="change" name="change"  value="0.00" readonly></td>';
	$html .= '<td>'. createCashDepositAccountSelect( 'cash_pos_account_id', $default_account_id) .'</td>';
	$html .= '</tr></table>';
	$html .= '<div id="count_change_instructions" style="font-size:0.6em">
	<p><b>Counting Change Back</b></p>
	<p><b>Example 1:</b>Total: $7.75. Customer hands you: $20. Begin counting at 7.75. Count: 8 (quarter), 9 (single), 10 (single), and 10 (ten) is $20. They get .25 + 1 + 1 + 10 = $12.25 as change.
	</p>
	<p>
	<b>Example 2 : </b>Total: $12.77. Customer hands you: $20.02. Subtract the coins they give from what they owe in your head. In this case there is: 77 - 02 is 75, so they get a quarter back. Start with the quarter: 13 (quarter), 14, 15 (single x2), and 5 (fiver) is $20.
	</p></div>';
	$html .= '<div style = "text-align: center;"  id="loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}	
function ccModalForm($pos_terminal_id,$amount_due)
{
	//cc
	$default_pos_payment_gateway_id = getSingleValueSQL("SELECT pos_payment_gateway_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	//is the gateway online or offline
	$gateway_provider = getSingleValueSQL("SELECT gateway_provider FROM pos_payment_gateways WHERE pos_payment_gateway_id = $default_pos_payment_gateway_id");
	if($gateway_provider == "Authorize.net" OR $gateway_provider == "Orbital")
	{
		//online
		$online = true;
		$html = '<script> var online=true</script>';
	}
	else
	{
		//offline
		$online = false;
		$html = '<script> var online=false</script>';
	}
	$html .= '<div id="cc-dialog-form" title="Credit Card Payment">';
		$html.='<div id="cc_main">';
		//$html.='div id="cc_main"';
			$html .= '<table class="singleTable" style="width:100%;"><tr>';
			$html .='<th style="text-align:right;">Amount Due</th>';
			$html .= '<th style="text-align:right">CC To Charge</th>';
			$html .= '<th style="text-align:right">Remainder</th>';
			$html .= '<th>Register/Account</th>';
			$html .='</tr><tr>';
			$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="amount_due"  value="'.round($amount_due,2).'" readonly></td>';
			$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="cc_input" name="cc_input" value ="'.round($amount_due,2).'"  onkeyup="calcuate_remainder_due();"  onblur= "calcuate_remainder_due();" ></td>';
			$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="remainder" name="remainder"  value="0.00" readonly></td>';
			$html .= '<td>'. createLimitedPaymentGatewaySelect('pos_payment_gateway_id', $default_pos_payment_gateway_id, ' onchange="changeGateway()" ' ).'</td>';
			//$html .= '<td>'. disableSelect(createLimitedPaymentGatewaySelect('pos_payment_gateway_id', $default_pos_payment_gateway_id, ' onchange="changeGateway()" ' )) .'</td>';
			$html .= '</tr></table>';
	
		$html .= '</div>';


		//CC Instructions
		$html .= '<div id="cc_online">';
		//$html .= 'div id="cc_online"';
			
			$html .= '<p><span style="color:green;">ONLINE</span>: Ready to Swipe Card or Choose Manual Entry For Pesty Cards.</p>';
			$html .= '<input type="button" id="manual_button" class="button" style="width: 250px;" onclick="manualCC()" value="Keyed Entry (costs more to process cc)">';
			$html .= '<script> var manual_entry=false</script>';
			
		$html .= '</div>';
			//cc response
		$html .= '<div id="CC_RESPONSE">';
		$html .= 'div id="CC_RESPONSE"';
		$html .= '</div>';
	
		$html .= '<div id="offline_cc_entry">';
		//$html .= 'div id="offline_cc_entry"';
		
			$html .= '<p><span style="color:red;">OFFLINE</span>: card info can still be swiped for data entry, however process the card off line</p>';						
		
		$html .= '</div>';

		$html .= '<div id="Manual_CC_DATA">';
		//$html .= 'div id="Manual_CC_DATA"';
			$html .= '<table class="singleTable" style="width:100%;margin-top:5px;"><tr>';
			$html .='<th style="text-align:right;width: 20%;">Card Type</th>';
			$html .='<th style="text-align:right;width: 30%;">Card Number</th>';
			$html .='<th style="text-align:right;width: 10%;">Exp(mm/yy)</th>';
			//$html .='<th style="text-align:right;width: 30%;">Card Holder</th>';
			//$html .='<th style="text-align:right;width: 10%;">CCV</th>';
			$html .='</tr><tr>';
			$html .='<td ><select style="width:100%" id = "card_type" name="card_type" >';

			//Add an option for not selected
				$credit_card_types = getSQL("SELECT payment_type FROM pos_customer_payment_methods WHERE payment_group = 'CREDIT_CARD' ORDER BY payment_type ASC");

			$html .= '<option value="false">Select Account</option>';
			for($cc = 0; $cc < sizeof($credit_card_types);$cc++)
			{
				$html .= '<option value="'.$credit_card_types[$cc]['payment_type'] .'">'.$credit_card_types[$cc]['payment_type'] .'</option>';
	
			}
			$html .= '</select></td>';
			$html .='<td ><input style="text-align:right;width: 100%;" size = "10" name = "credit_card_number" id="credit_card_number" type="text" value="" ></td>';
			$html .='<td ><input style="text-align:right;width: 100%;" size = "10" name = "expiration" id="expiration" type="text" value="" ></td>';
			//$html .='<td ><input style="text-align:right;width: 100%;" size = "10" name = "card_holder" id="card_holder" type="text" value="" ></td>';
			//$html .='<td ><input style="text-align:right;width: 100%;" size = "10" name = "ccv" id="ccv" type="text" value="" ></td>';
			$html .='</tr><tr>';
			$html .= '</tr></table>';
			
		$html .= '</div>';
		$html .= '<div id="Offline_CC_DATA">';
		//$html .= 'div id="Manual_CC_DATA"';
			$html .= '<table class="singleTable" style="width:100%;margin-top:5px;"><tr>';
			$html .='<th style="text-align:right;width: 20%;">Card Type</th>';
			$html .='<th style="text-align:right;width: 30%;">Card Number last 4</th>';
			//$html .='<th style="text-align:right;width: 10%;">Exp</th>';
			//$html .='<th style="text-align:right;width: 30%;">Card Holder</th>';
			//$html .='<th style="text-align:right;width: 10%;">CCV</th>';
			$html .='</tr><tr>';
			$html .='<td ><select style="width:100%" id = "offline_card_type" name="offline_card_type" >';

			//Add an option for not selected
				$credit_card_types = getSQL("SELECT payment_type FROM pos_customer_payment_methods WHERE payment_group = 'CREDIT_CARD' ORDER BY payment_type ASC");

			$html .= '<option value="false">Select Account</option>';
			for($cc = 0; $cc < sizeof($credit_card_types);$cc++)
			{
				$html .= '<option value="'.$credit_card_types[$cc]['payment_type'] .'">'.$credit_card_types[$cc]['payment_type'] .'</option>';
	
			}
			$html .= '</select></td>';
			$html .='<td ><input style="text-align:right;width: 100%;" size = "10" name = "offline_credit_card_number" id="offline_credit_card_number" type="text" value="" ></td>';
			//$html .='<td ><input style="text-align:right;width: 100%;" size = "10" name = "expiration" id="expiration" type="text" value="" ></td>';
			//$html .='<td ><input style="text-align:right;width: 100%;" size = "10" name = "card_holder" id="card_holder" type="text" value="" ></td>';
			//$html .='<td ><input style="text-align:right;width: 100%;" size = "10" name = "ccv" id="ccv" type="text" value="" ></td>';
			$html .='</tr><tr>';
			$html .= '</tr></table>';
			
		$html .= '</div>';

	

		$html .= '<div style = "text-align: center;"  id="cc_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}	
function checkModalForm($pos_terminal_id,$amount_due)
{
	//CHECK
	$default_account_id = getSingleValueSQL("SELECT default_check_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	$html = '<div id="check-dialog-form" title="Check Payment">';
	$html .= '<table class="singleTable" style="width:100%;"><tr>';
	$html .='<th style="text-align:right;">Amount Due</th>';
	$html .= '<th style="text-align:right">Check Tender</th>';
	$html .= '<th style="text-align:right">Remainder</th>';
	$html .= '<th style="text-align:right">License</th>';
	$html .= '<th>Register/Account</th>';
	$html .='</tr><tr>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="amount_due"  value="'.round($amount_due,2).'" readonly></td>';
	$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="check_input" name="check_input" value ="'.round($amount_due,2).'"  onkeyup="calculate_check_remainder()"  onblur= "calculate_change()" ></td>';
	$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="check_remainder" name="check_remainder" value ="0.00"    readonly tabindex="-1"></td>';

	$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="license" name="license" type="text" value="" ></td>';
	$html .= '<td>'. createCashDepositAccountSelect('pos_check_account_id',$default_account_id) .'</td>';
	$html .= '</tr></table>';
	$html .= '<div id="check_instructions" style="font-size:0.6em">
	<p><b>Check Instructions</b></p>
	<p><b>Customer First Name, Last Name, and Address must be entered into the system</b>
	</p></div>';
	$html .= '<div style = "text-align: center;"  id="check_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}	
function storeCreditModalForm($pos_terminal_id,$amount_due)
{
	//Store Credit
	//what is the store credit account?
	$default_account_id = getSingleValueSQL("SELECT default_store_credit_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	
	
	$html = '<div id="storeCredit-dialog-form" title="Store Credit Payment">';
		$html .= '<div id="storeCreditDetails">';
		$html .= '</div>';
		$html .= '<div id="storeCreditPayment">';
		$html .='<p>Amount Due: $<input id="gc_amount_due" value="'.round($amount_due,2).'" readonly></p>';
		$html .= '<table class="singleTable" style="width:100%;"><tr>';
		$html .='<th style="text-align:right">Amount To Pay</th>';
		//$html .='<th style="text-align:right">Card Value Remainder</th>';
		/*$html .= '<th>Register/Account</th>';*/
		$html .='</tr><tr>';
		


		$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="gift_card_input" name="gift_card_input" value ="0.00"   ></td>';
		/*$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="gc_remainder" name="gc_remainder" value="0.00" readonly></td>';*/
		/*$html .= '<td>'. storeCreditAccountSelect('pos_store_credit_account_id', $default_account_id) .'</td>';*/
		$html .= '</tr></table>';
		$html .= '</div>';
		$html .= '<div style = "text-align: center;"  id="store_credit_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	
		$html .= '<div id="storeCreditLookupModal" title="Store Credit Card Lookup">';

	$html .= '<div id="storeCreditLookup" title="Store Credit Lookup">';
		$html .= '<table class="singleTable"><tr>';
		$html .='<th style="text-align:right">Store Credit Number <br> (scan barcode)</th>';
		$html .='</tr><tr>';
		$html .='<td style="width:100%;"><input style="text-align:right;" style="width:100%;" id="store_card_number" value=""></td>';
		$html .= '</tr></table>';
		$html .=  '<p>Got a card incompatible with the system?<INPUT class = "button" type="button" style="width:200px" id="gc_inc" value="Incompatible Card" onclick="incompatible_card()"/></p>'.newline();
		$html .= '<div style = "text-align: center;"  id="store_credit_lookup_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	$html .= '</div>';
		

	
	
	return $html;
}	
function otherModalForm($pos_terminal_id,$amount_due)
{
	//other option - currently looking for a non-posting account
$default_account_id = getSingleValueSQL("SELECT default_non_payment_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	$html = '<div id="other-payment-dialog-form" title="Other Payment">';
	$html .= '<table class="singleTable" style="width:100%;"><tr>';
	$html .='<th style="text-align:right;">Amount Due</th><th style="text-align:right">Tender</th>';
	$html .= '<th>Account</th>';
	$html .='</tr><tr>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="non_payment_amount_due"  value="'.round($amount_due,2).'" readonly></td>';
	$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="non_payment_amount_input" name="non_payment_amount_input"  value ="0.00"  onkeyup="" onblur= "" onclick="this.select();"  ></td>';
	$html .= '<td>'. storeCreditAccountSelect('pos_non_payment_account_id', $default_account_id) .'</td>';
	$html .= '</tr></table>';
	
	$html .= '<div style = "text-align: center;"  id="non_payment_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}	

//refund modal forms
function cashRefundModalForm($pos_terminal_id,$amount_due)
{
	//CASH
	$default_account_id = getSingleValueSQL("SELECT default_cash_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	$html = '<div id="cashRefund" title="Cash Refund">';
	$html .= '<table class="singleTable" style="width:100%;"><tr>';
	$html .='<th style="text-align:right;">Refund Amount Due</th>';
	$html .= '<th style="text-align:right">Refund Amount</th>';
	$html .= '<th>Register/Account</th>';
	$html .='</tr><tr>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="refund_amount_due"  value="'.round($amount_due,2).'" readonly></td>';
	$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="cash_refund_input" name="cash_refund_input"  value ="'.round($amount_due,2).'"    ></td>';
	
	$html .= '<td>'. createCashDepositAccountSelect( 'cash_refund_pos_account_id', $default_account_id) .'</td>';
	$html .= '</tr></table>';
	
	$html .= '<div style = "text-align: center;"  id="cash_refund_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}	
function checkRefundModalForm($pos_terminal_id,$amount_due)
{
	//CHECK Refund - only a manager can do this.....select checking account, put it in the payments journal, and have a link to open the check....
	//this refund should happen "automatically....." we 
	$default_account_id = getSetting("default_pos_return_checking_account");
	$html = '<div id="checkRefund" title="Check Refund">';
	$html .= '<table class="singleTable" style="width:100%;"><tr>';
	$html .='<th style="text-align:right;">Refund Amount Due</th>';
	$html .='<th style="text-align:right;">Check Amount</th>';
	$html .= '<th>Register/Account</th>';
	//$html .= '<th>License?</th>';
	$html .='</tr><tr>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="check_refund_amount_due"  value="'.round($amount_due,2).'" readonly></td>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="check_refund_input"  value="'.round($amount_due,2).'" ></td>';

	//$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="license" name="license" type="text" value="" ></td>';
	$html .= '<td>'. createCheckingAccountSelect('pos_refund_checking_account_id',$default_account_id) .'</td>';
	$html .= '</tr></table>';
	
	$html .= '<div style = "text-align: center;"  id="check_refund_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}	
function ccRefundModalForm($pos_sales_invoice_id, $pos_terminal_id, $amount_due)
{
	
	///errr this one...
	//we need to refund via the transaction id or via offline
	//so if there is a transaction id we can refund up to that amount and no more.

	$default_pos_payment_gateway_id = getSingleValueSQL("SELECT pos_payment_gateway_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	//is the gateway online or offline
	$line = getSingleValueSQL("SELECT line FROM pos_payment_gateways WHERE pos_payment_gateway_id = $default_pos_payment_gateway_id");
	if($line == 'online')
	{
		//online
		$online = true;
		$html = '<script> var online=true</script>';
	}
	else
	{
		//offline
		$online = false;
		$html = '<script> var online=false</script>';
	}
	$html .= '<div id="ccRefund" title="Credit Card Refund">';
		$html.='<div id="cc_main">';
		$html .='<p>Invoice Total $<input id="refund_amount_due" value="'.round($amount_due,2).'" readonly></p>';
			$html .= '<table class="singleTable" style="width:100%;"><tr>';
			$html .= '<th style="text-align:right">Refund Amount</th>';
			$html .= '<th>Register/Account</th>';
			$html .='</tr><tr>';
			$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="refund_cc_input" name="refund_cc_input" value ="'.round($amount_due,2).'"   ></td>';
			$html .= '<td>'. createLimitedPaymentGatewaySelect('refund_pos_payment_gateway_id', $default_pos_payment_gateway_id, ' onchange="changeRefundGateway()" ' ).'</td>';

			$html .= '</tr></table>';
	
		$html .= '</div>';


		//CC Instructions
		
		$html .= '<div id="REFUND_CC_RESPONSE">';
		$html .= '<p>coding this.....</p>';
		$html .= '</div>';
		
		//online
		$html .= '<div id="onlineCCRefund">';
		$html .= 'coding this.....';
		$html .= '</div>';
	
		//offline
		$html .= '<div id="offlineCCRefund">';
			$html .= '<table class="singleTable" style="width:100%;margin-top:5px;"><tr>';
			$html .='<th style="text-align:right;width: 20%;">Card Type</th>';
			$html .='<th style="text-align:right;width: 30%;">Last 4 of Card Number</th>';
			$html .='</tr><tr>';
			$html .='<td ><select style="width:100%" id = "offline_refund_card_type" name="offline_refund_card_type" >';

			//Add an option for not selected
			$credit_card_types = getSQL("SELECT payment_type FROM pos_customer_payment_methods WHERE payment_group = 'CREDIT_CARD' ORDER BY payment_type ASC");

			$html .= '<option value="false">Select Account</option>';
			for($cc = 0; $cc < sizeof($credit_card_types);$cc++)
			{
				$html .= '<option value="'.$credit_card_types[$cc]['payment_type'] .'">'.$credit_card_types[$cc]['payment_type'] .'</option>';
	
			}
			$html .= '</select></td>';
			$html .='<td ><input style="text-align:right;width: 100%;" size = "10" name = "offline_refund_card_num" id="offline_refund_card_num" type="text" value="" ></td>';

			$html .='</tr><tr>';
			$html .= '</tr></table>';
			
		$html .= '</div>';
		
		$html .= '<div style = "text-align: center;"  id="refundcc_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}	
function storeCreditRefundModalForm($pos_terminal_id,$amount_due)
{
	
	//store credit 1 - the card lookup....
	
	
	$html = '<div id="storeCreditRefundForm1" title="Enter Store Credit Card Number">';
		$html .= '<div id="enterStoreCreditCardNumber">';
		$html .= '<p>Scan Store Credit Barcode <input class="linedInput" id="store_credit_number_rf"   ></p>';
		$html .= '<p>Amount To Issue: '.round(-$amount_due,2) .'</p>';
		$html .= '</div>';
		$html .= '<div style = "text-align: center;"  id="store_credit_refund_loading_image1"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	

		

	
	
	return $html;
}	
function otherRefundModalForm($pos_terminal_id,$amount_due)
{
	//other option - currently looking for a non-posting account
$default_account_id = getSingleValueSQL("SELECT default_non_payment_account_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	$html = '<div id="otherRefund" title="Other Payment">';
	$html .= '<table class="singleTable" style="width:100%;"><tr>';
	$html .='<th style="text-align:right;">Amount Due</th><th style="text-align:right">Tender</th>';
	$html .= '<th>Account</th>';
	$html .='</tr><tr>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="non_payment_amount_due"  value="'.round($amount_due,2).'" readonly></td>';
	$html .= '<td><input style="text-align:right;width: 100%;" size = "10" id="non_payment_amount_input" name="non_payment_amount_input"  value ="0.00"  onkeyup="" onblur= "" onclick="this.select();"  ></td>';
	$html .= '<td>'. storeCreditAccountSelect('pos_non_payment_account_id', $default_account_id) .'</td>';
	$html .= '</tr></table>';
	
	$html .= '<div style = "text-align: center;"  id="non_payment_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}	

function createRetailSalesInvoicePaymentsView($pos_sales_invoice_id)
{
	$html = '';
//******************** PAYMENTS *******************************

	$invoice_total = getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id);
	$payments_total = getSalesInvoiceTotalPayment($pos_sales_invoice_id);

	$payment_contents = getCustomerPayments($pos_sales_invoice_id);
	if(sizeof($payment_contents)>0)
	{

		$payments_table_name = 'payments_table';
		$payments_contents_table_def = createRetailSalesPaymentsTableDef($pos_sales_invoice_id, $payments_table_name);
		
		
		$html .= createStaticViewDynamicTableV2($payments_table_name, $payments_contents_table_def, $payment_contents, 'class="static_contents_table" ');
		if (abs($invoice_total - $payments_total) > .0001)
		{
			$html .= '<h2 class="error">Payments Do Not Match Invoice Total</h2>';
		}
	}
	return $html;
}
function customerDepositModalForm()
{
		//Assign a gift card value

	$html = '<div id="customer-deposit-dialog-form" title="Add Customer Deposit">';
	$html .= '<table class="singleTable" style="width:100%;"><tr>';
	$html .='<th style="text-align:left;">Amount</th>';
	$html .='<td><input style="text-align:right;width: 100%;" size = "10" id="deposit_amount"  value="0.00" ></td>';
	$html .= '</table>';
	$html .= '<input type="checkbox"  id="create_order"  checked >Create Customer Order Card';
	$html .= '<div style = "text-align: center;"  id="deposit_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}
function customerSelectModalForm($pos_customer_id)
{
	$html = '<div id="customer-select-dialog-form" title="Select a customer: Search or Add Customer">';
	
	$html .= '<div id="customer_search">';
	$html .= '<script>var CustomerSearchColDef = ' . json_encode(createCustomerSearchColDef()) . ';</script>';
	//here we have the standard search table...
	$search_fields = array(				
										
										array(	'db_field' => 'first_name',
											'mysql_search_result' => 'first_name',
											'caption' => 'First Name',	
											'type' => 'input',
											'html' => createSearchInput('first_name_s')
										),
										array(	'db_field' => 'last_name',
											'mysql_search_result' => 'last_name',
											'caption' => 'Last Name',	
											'type' => 'input',
											'html' => createSearchInput('last_name_s')
										),
										array(	'db_field' => 'email1',
											'mysql_search_result' => 'email1',
											'caption' => 'Email',	
											'type' => 'input',
											'html' => createSearchInput('email1_s')
										),
										array(	'db_field' => 'phone',
											'mysql_search_result' => 'phone',
											'caption' => 'Phone',	
											'type' => 'input',
											'html' => createSearchInput('phone_s')
										),
										
										);
	$html .= createHTMLSeachTable($search_fields, 'class="search_table sea2"');

	
	
	
	$html .= '<div id="customer_search_buttons">';
	$html .= '<input class = "button" id="customer_search_button" type="submit" name="search" value="Search" onclick="customerSearch()";/>';
	$html .= '</div>';
	$html .= '<div id="customer_add_edit_buttons">';
	$html .= '<input class = "button" id="edit_customer" style="width:250px" type="submit" name="search" value="Edit '. getCustomerFullName($pos_customer_id) . '"  onclick="editCustomer()";/>';
	$html .= '<input class = "button" id="add_customer" type="submit" name="search" value="Add" onclick="addCustomer()";/>';
	$html .= '<input class = "button" id="select_none" style="width:160px" type="submit" name="search" value="Select No Customer" onclick="selectNoCustomer()";/>';
	
	$html .= '</div>';
	$html .= '</div>';
	
	$html .= '<div id="customer_search_results">';
	//and the standard search results.... in html format already?
	//$html.= '<p>TEST</p>';
	$html .= '</div>';
	
	

	
	$html .= '<div style = "text-align: center;"  id="customer_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	
	$html .= '</div>';
	return $html;
}
function customerAddressSelectModalForm($pos_customer_id)
{
	$html = '<div id="customer-address-dialog-form" title="Add or Edit Customer Address">';
	
	
	
	
	$html .= '<div id="add_edit_address">';
	$html .= '<TABLE id = "add_edit_address_table" name = "add_edit_address_table" class ="cusAddTable">';
		$html .= '<TR >';	
		$html .= '<th>Address 1</th>'; 
		$html .= '<td><input id="address1_a" value=""></td>';
		$html .='</tr>';
		$html .= '<TR >';	
		$html .= '<th>Address 2</th>'; 
		$html .= '<td><input id="address2_a" value = ""></td>';
		$html .='</tr>';
		
		$html .= '<TR >';	

		$html .= '<th>City</th>'; 
		$html .= '<td><input id="city_a" value="" ></td>';
		$html .='</tr>';
		$html .= '<TR >';	

		$html .= '<th>State</th>'; 
		$html .= '<td>'.
		
		
		createStateSelect('pos_state_id', 'false', 'off', ' ') .'
		</td>';
				$html .='</tr>';
		$html .= '<TR >';	
		$html .= '<th>ZIP</th>'; 
		$html .= '<td><input id="zip_a"  "value="" ></td>';
		
		
		$html .= '</tr>';
		$html .= '</table>';
	//$html .= '<input class = "button" type="button" style="width:150px" name="add_customer" id="customer_select_button" value="Edit/Change Customer" onclick="open_customer()"/>';
	
	$html .= '</div>';	

	$html .= '<div style = "text-align: center;"  id="address_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
	return $html;
}

function createPOSCustomerAddressSelect($name, $pos_address_id, $pos_customer_id, $select_events ='')
{
 	$addresses = getPOSv1CustomerAddresses($pos_customer_id);
	//$default_address ?
	
	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Address</option>';
	//Add an option to edit selected address....
	$html .= '<option value="edit">Edit Address</option>';
	
	for($i = 0;$i < sizeof($addresses); $i++)
	{
		$html .= '<option value="' . $addresses[$i]['pos_address_id'] . '"';
		if ( ($addresses[$i]['pos_address_id'] == $pos_address_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $addresses[$i]['full_address'] .'</option>';
	}

	$html .= '<option value ="add" >Add a New Address</option>';
	
	$html .= '</select>';
	$html.='<script> var addresses = '. json_encode($addresses) .';</script>';
	return $html;
}
function createCustomerHtmlTable($pos_customer_id, $pos_sales_invoice_id, $type ='edit')
{
	

		$html = '<div id="customer_here">';
		$html .= '<TABLE id = "customer_invoice_main" name = "customer_invoice_main" class ="customer_invoice_main">';
		$html .= '<TR >';	
		$html .=  '<td><input class = "button" type="button" style="width:150px" name="add_customer" id="customer_select_button" value="Edit/Change Customer" onclick="open_customer()"/></td>';
		$html .= '<th>Customer Name</th>'; 
		$html .= '<td id="full_name">' .getCustomerFullName($pos_customer_id) . '</td>';
		$html .= '<th>Email</th>'; 
		$html .= '<td><input id="email1" style="width:15em" value="' .getCustomerEmail($pos_customer_id) . '" ></td>';
		$html .= '<th>Phone</th>'; 
		$html .= '<td><input id="phone" style="width:8em" value="' .getCustomerPhone($pos_customer_id) . '" ></td>';
		$html .= '<th>Address</th>'; 
		$pos_address_id = getSalesInvoiceAddress($pos_sales_invoice_id);
		$html .= '<td>'. createPOSCustomerAddressSelect('pos_address_id', $pos_address_id, $pos_customer_id, ' onchange="changeAddress(this)" ') . '</td>';
		
		$html .= '</tr>';
		$html .= '</table></div>';

		
		$html .= '<div id="customer_not_here">';

		$html .= '<TABLE id = "customer_invoice_main" name = "customer_invoice_main" class ="customer_invoice_main">';
		$html .=  '<td><input class = "button" type="button" style="width:120px" id="customer_slect_button" name="add_customer" value="Select Customer" onclick="open_customer()"/></td>';
		$html .= '</tr>';
		$html .= '</table></div>';
	
		
	$html .='<script>var pos_address_id = '.$pos_address_id.';</script>';
	$html .='<script>var pos_customer_id = '.$pos_customer_id.';</script>';
	$html .='<script>var customer_full_name = "'.getCustomerFullName($pos_customer_id).'";</script>';
	
	return $html;
}
function customerAddEditModal($pos_customer_id)
{	
	$html = '<div id="cust_add_edit_form" title="Add or Edit Customer">';
	$html .= '<div id="add_edit_customer">';
	$html .= '<TABLE id = "add_edit_customer_table" name = "add_edit_customer_table" class ="cusAddTable">';
		$html .= '<TR >';	
		$html .= '<th>First Name</th>'; 
		$html .= '<td><input id="first_name_a" value="' .getCustomerFirstName($pos_customer_id) . '"></td>';
		$html .='</tr>';
		$html .= '<TR >';	
		$html .= '<th>Last Name</th>'; 
		$html .= '<td><input id="last_name_a" value = "' .getCustomerLastName($pos_customer_id) . '"></td>';
		$html .='</tr>';
		
		$html .= '<TR >';	

		$html .= '<th>Email</th>'; 
		$html .= '<td><input id="email1_a" value="' .getCustomerEmail($pos_customer_id) . '" ></td>';
		$html .='</tr>';
		$html .= '<TR >';	

		$html .= '<th>Phone</th>'; 
		$html .= '<td><input id="phone_a"  "value="' .getCustomerPhone($pos_customer_id) . '" ></td>';
		
		
		$html .= '</tr>';
		$html .= '</table>';
	//$html .= '<input class = "button" type="button" style="width:150px" name="add_customer" id="customer_select_button" value="Edit/Change Customer" onclick="open_customer()"/>';
	//$html .= '<input class = "button" id="select_none" style="width:160px" type="submit" name="search" value="Edit Address" onclick="editAddress()";/>';
	$html .= '</div>';
	
	$html .= '<div style = "text-align: center;"  id="customer_ae_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	
	$html .= '</div>';
	return $html;
}
?>