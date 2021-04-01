<?php
/*
	invoice.php
	
	//the flow....
	start an invoice:
	Create an invoice status INIT
	Select sales associate
	Select Customer
	Update the invoice set it to draft
	Edit contents
	
	Options: Edit/Payments
	
	Done with the content editing and Pay: Invoice Closed, payment_status PAID
	Options: Print invoice
	
	Done with content editing customer puts it onto thier account....
	Options: Print Invoice, Payments
	
	
*/
//this is the javascript versions.....
$retail_sales_javascript_version = 'retail_sales_invoice.2014.02.04.js';
$product_lookup_javascript = 'ajax_product.2013.06.26.js';
$css_styles_version = 'retail_invoice_styles.2013.06.14.css';

$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales Invoice';
require_once('retail_sales_invoice_functions.php');

$type = $_GET['type'];
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
$db_table = 'pos_sales_invoice';
$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;

//need a terminal check here...
$pos_terminal_id = terminalCheck();


$invoice_status = getSingleValueSQL("SELECT invoice_status FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
$payment_status = getSingleValueSQL("SELECT payment_status FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
if($type == 'edit')
{
	//check that this entry is not 'closed'
	
	if (checkIfUserIsAdmin($_SESSION['pos_user_id']))
	{
	}
	else if(  $invoice_status == 'CLOSED')
	{
		trigger_error('Attempting to access a closed invoice:' .$pos_sales_invoice_id);
		exit();
		
	}
	check_lock($db_table, $key_val_id,POS_ENGINE_URL .'/sales/retailInvoice/retail_sales_invoice.php?type=edit&pos_sales_invoice_id='.$pos_sales_invoice_id, getBinderURL($binder_name) . '?message=canceled');
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
	if(isset($_GET['pos_address_id']))
	{
		$pos_address_id = scrubInput($_GET['pos_address_id']);
		$add_update['pos_address_id'] = $pos_address_id;
		$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $add_update);
	}
	$complete_location = 'list_retail_sales_invoices.php';
	$cancel_location = 'list_retail_sales_invoices.php';

	$payment_location = 'add_customer_payment.php?pos_sales_invoice_id='.$pos_sales_invoice_id;
	
	
	$html = '<link type="text/css" href="'.$css_styles_version.'" rel="Stylesheet"/>'.newline();
	$html .=  '<script src="'.$retail_sales_javascript_version.'"></script>'.newline();
	$html .=  '<script src="'.$product_lookup_javascript.'"></script>'.newline();
	$html .= '<script>var pos_sales_invoice_id = '.$pos_sales_invoice_id. ';</script>';

	$form_id = "sales_invoice_form";
	$form_action = 'sales_invoice.form.handler.php';
	$html .=  '<form id = "' . $form_id . '" action="'.$form_action.'.php" method="post" onsubmit="return validateInvoiceForm()">';
	
	$html .= '<div class = "invoice">';
//************************** INVOICE OVERVIEW ***********************************************
	$html .= ' <div class = "retail_sales_invoice_div">';
	$html .= createInvoiceHtmlTable($pos_sales_invoice_id);
	$html .= '<script>var invoice_date = "' .getSalesInvoiceDateFromDatetime($pos_sales_invoice_id) .'";</script>';
	//$html .= '<script>var tax_method = "' . getSalesInvoiceTaxCalculationMethod($pos_sales_invoice_id) . '";</script>';
	$html .= '<script>var tax_method = "average";</script>';
	$html .= '</div>';
//************************** CUSTOMER  ***********************************************
	$html .= ' <div class = "customer_invoice_div">';
	$html .= createCustomerHtmlTable($pos_sales_invoice_id);
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
	$invoice_contents = getInvoiceContents($pos_sales_invoice_id);
	//change the br to a comma?
	for($row=0;$row<sizeof($invoice_contents);$row++)
	{			
	$invoice_contents[$row]['product_options'] = str_replace('<br>',',',$invoice_contents[$row]['product_options']);
	}
	
	$invoice_table_name = 'invoice_table';
	$invoice_contents_table_def = createRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id, $invoice_table_name);
	
	$html .= createDynamicTableReuse($invoice_table_name, $invoice_contents_table_def, $invoice_contents, $form_id, ' class="dynamic_contents_table" style="width:100%" ');
	
//************ TOTALS ***************************************************
	$footer_table_name = 'invoice_footer';
	$footer_table_def = createRetailSalesInvoiceContentsFooterTableDef($pos_sales_invoice_id);
	$footer_table_tags = ' class="invoice_summary_table" ';
	$footer_data = array();
	$html .= createHorizontalInputHTMLTable($footer_table_name, $footer_table_def, $footer_data, $footer_table_tags);

//******************** PROMOTIONS ***************************************************//
	$html .= '<div class="discount_table" style="clear:both">';
		//promotion table
		$html .= '<h3>Enter Promotions Here. Promotions can only apply to full price items.</h3>';
		$promotion_table_name = 'promotion_table';
		$promotion_table_def = createRetailSalesPromotionsTableDef($promotion_table_name);
		$promotion_data = getSQL("SELECT pos_sales_invoice_promotions.pos_promotion_id, promotion_code, promotion_name, promotion_type, promotion_amount, expired_value, date(expiration_date) as expiration_date, percent_or_dollars, qualifying_amount FROM pos_sales_invoice_promotions
								LEFT JOIN pos_promotions USING(pos_promotion_id)
								WHERE pos_sales_invoice_promotions.pos_sales_invoice_id = $pos_sales_invoice_id");
								
		$html .= createDynamicTableReuse($promotion_table_name, $promotion_table_def, $promotion_data, $form_id, ' class="dynamic_contents_table"  ');
	$html .= '</div>';


	$html .= '</div>';

	//*************************** BUTTONS **************************************
		$html .= '<div style="float:right;">';
			//add the return button....
	$return_url = 'returns.php?pos_original_sales_invoice_id='.$pos_sales_invoice_id;
	//$html .=  '<INPUT class = "button" type="button" style="width:100px" value="Returns" onclick="saveDraftAndGo(\''.$return_url.'\')"/>'.newline();
			$go_url = 'retail_sales_invoice.fh.php?next=view&pos_sales_invoice_id='.$pos_sales_invoice_id;
			$html .=  '<INPUT class = "button" type="button"  value="Save Invoice" onclick="saveDraft()" />'.newline();
			$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Save/Exit" onclick="saveDraftAndGo(\''.$go_url.'\')" />'.newline();
			$go_url = 'retail_sales_invoice.fh.php?next=pay&pos_sales_invoice_id='.$pos_sales_invoice_id;
			$html .=  '<INPUT class = "button" type="button" style="width:200px" value="Continue To Payments" onclick="continueToPayments(\''.$go_url.'\')"
			 />'.newline();
			//$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Delete Invoice" onclick="deleteInvoice()" />'.newline();
		 $html .='</div>';	

		 

	$html .=  '	<script>var formID = "'.$form_id.'";</script>';
	$html .= '<script>document.getElementById(\'barcode\').focus();</script>';
	$html .= '</form>';
	//$html .= '<div style="clear:both;"/>';
//finally init the form:
	$html .= '<script>init_sales_invoice()</script>';
}
else if($type == 'view')
{
	if(isset($_GET['pos_customer_id']))
	{
		$pos_customer_id = scrubInput($_GET['pos_customer_id']);
		$update['pos_customer_id'] = $pos_customer_id;
		$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $update);
	}
	
	$html = '<link type="text/css" href="'.$css_styles_version.'" rel="Stylesheet"/>'.newline();
	$html .=  '<script src="'.$retail_sales_javascript_version.'"></script>'.newline();
	$html .=  '<script>var pos_sales_invoice_id = '.$pos_sales_invoice_id.'</script>'.newline();
	$html .= '<div class = "invoice">';
	
	$html .= createRetailSalesInvoiceView($pos_sales_invoice_id);
	$html .= createRetailSalesInvoiceCusomterView($pos_sales_invoice_id);
	$html .= createRetailSalesInvoiceContentsView($pos_sales_invoice_id);
	$html .= createRetailSalesInvoiceFooterView($pos_sales_invoice_id);
	$html .= createRetailSalesInvoicePromotionsView($pos_sales_invoice_id);
	$html .= createRetailSalesInvoicePaymentsView($pos_sales_invoice_id);
//******************** BUTTONS *******************************
		$html .= '<div style="clear:both;">';
		
		if($invoice_status == 'DRAFT' OR $invoice_status == 'INIT')
		{
			$html .=  '<INPUT class = "button" type="button"  style="margin: 2px 4px 6px 30px;"value="Edit Contents" onclick="open_win(\'retail_sales_invoice.php?type=edit&pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
			
		}
		//$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Customer View" onclick="open_win(\'retail_sales_invoice_customer_view.php?pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
		if($payment_status == 'UNPAID' && ($invoice_status == 'OPEN' OR $invoice_status == 'DRAFT'))
		{
			$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Payments" onclick="open_win(\'retail_sales_invoice_payments.php?pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
		}
		
		//to open the invoice the invoice will have to go onto an 'account'
		
		//to print an invoice the invoice needs to be opend or closed
		if($invoice_status == 'OPEN' OR $invoice_status == 'CLOSED')
		{
			$html.= '<div id="save_alert" ></div>';
			$html .=  '<INPUT class = "button" id="store_print_button" type="button" style="width:300px" value="Print Store Copy To '.getPrinterName(getDefaultTerminalPrinter($pos_terminal_id)).' At ' .  getPrinterLocation(getDefaultTerminalPrinter($pos_terminal_id)) . '" onclick="sendInvoiceToPrinter(\'store\','.$pos_sales_invoice_id.')"
			 />'.newline();
			$html .=  '<INPUT class = "button" id="customer_print_button" type="button" style="width:300px" value="Print Invoice To '.getPrinterName(getDefaultTerminalPrinter($pos_terminal_id)).' At ' .  getPrinterLocation(getDefaultTerminalPrinter($pos_terminal_id)) . '" onclick="sendInvoiceToPrinter(\'customer\','.$pos_sales_invoice_id.')"
			 />'.newline();
$html .=  '<INPUT class = "button" id="gift_receipt" type="button" style="width:300px" value="Print Gift Receipt To '.getPrinterName(getDefaultTerminalPrinter($pos_terminal_id)).' At ' .  getPrinterLocation(getDefaultTerminalPrinter($pos_terminal_id)) . '" onclick="sendInvoiceToPrinter(\'gift_receipt\','.$pos_sales_invoice_id.')"
			 />'.newline();
			
$html.='<div>';


			$html .=  '<INPUT class = "button" type="button"  id="email_button" style="width:300px" value="EMAIL INVOICE" onclick="emailInvoice('.$pos_sales_invoice_id.')"
			 />'.newline();
			 
			 
			 $html .=  '<INPUT class = "button" id="customer_print_button" type="button" style="width:300px" value="Open Invoice PDF" onclick="openInvoiceInline('.$pos_sales_invoice_id.')" />'.newline();
			 $html.='</div>';
			 if (checkIfUserIsAdmin($_SESSION['pos_user_id']))
			{
			$html .=  '<p><INPUT class = "admin_button" type="button"  style="width:300px;"value="Open Inline Sales Invoice" onclick="open_win(\'print_sales_invoice.php?type=customer_inline&pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
			$html .=  '<INPUT class = "admin_button" type="button"  style=""value="Edit Contents" onclick="open_win(\'retail_sales_invoice.php?type=edit&pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
			$html .=  '<INPUT class = "admin_button" type="button" style="width:120px" value="Edit Payments" onclick="open_win(\'retail_sales_invoice_payments.php?pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
			$html .=  '<INPUT class = "admin_button" type="button" style="width:120px" value="Edit Invoice Overview" onclick="open_win(\'sales_invoice_overview.php?type=EDIT&pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" /></p>'.newline();
			}
			 
		}
		
		 $html .='</div>';
}
else
{
	
	$html = 'Not A Valid Type';
	
}

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function POSproductLookUpTable()
{
	$html =  '<TABLE style="width:100%;">';
	$html .= '<tr>'.newline();
	$html .= ' <TD style="vertical-align:bottom;width:10%;text-align:center;"><INPUT TYPE="TEXT" class="lined_input"  id="barcode" style = "background-color:yellow;width:100%;" NAME="barcode" onclick="this.select()" onKeyPress="return disableEnterKey(event)" onKeyDown="lookUpBarcodeID(this, event)"	/></td>';
	$html .= '<td style="vertical-align:bottom;width:10%;text-align:center;"><input class = "button2" type="button"  name="add_barcode" value="Add" onclick="addBarcodeButton()"/></td>';
	$html .= '<td style="vertical-align:bottom;text-align:center;width:5%;">'.newline();
	$html .= 'OR';
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;width=65%;text-align:center;">'.newline();
	$html .= ' <style>
.ui-autocomplete-loading {
background: white url("'.POS_ENGINE_URL . '/includes/images/ui-anim_basic_16x16.gif") right center no-repeat;
}
</style>';
	$html.= '<div class="ui-widget" >
<input id="product_search"  value="Type to search, leave spaces between search terms..." style = "border: 1px solid black;width:100%;" onclick="productSearchFocus()"/>
</div>';
	$html .= '</td>'.newLine();
	$html .= '<td style="vertical-align:bottom;width:10%;">'.newline();
	$html .= '<input class = "button2" type="button"  name="add_prodcut_subid" value="Add" onclick="addSubidFromSearch()"/>';
	$html .= '</td>'.newLine();
	$html .= '</tr>'.newline();
	$html .=  '</table>';
	$html .= addBeepV3().newline();
	return $html;


}

function createPOSProductSubIDLookupTableDef()
{
		return array(	
			
					array(
							'th' => 'Brand',
							'db_field' => 'pos_manufacturer_brand_id_lookup',
							'type' => 'select',
							'html' => createManufacturerBrandSelect('pos_manufacturer_brand_id_lookup', 'false',  'off', ' onchange="UpdateBrandData()" onkeypress = "return noEnter(event);"  ')),
					array(
							'th' => 'Style Number',
							'db_field' => 'style_number_lookup',
							'type' => 'select',
							'html' => createBlankSelect('style_number_lookup',' onchange="UpdateStyleData()" onkeypress = "return noEnter(event);"  ')),
					array(	'th' => 'Color Code',
							'db_field' => 'color_code_lookup',
							'type' => 'select',
							'html' => createBlankSelect('color_code_lookup',' onchange="UpdateColorCodeData()" onkeypress = "return noEnter(event);" ')),
					array(	'th' => 'Size',
							'db_field' => 'size_lookup',
							'type' => 'select',
							'html' => createBlankSelect('size_lookup',' onchange="UpdateSizeData()" onkeypress = "return noEnter(event);" ')),
					/*array(	'th' => 'Product Sub Id Name',
							'db_field' => 'product_subid_manual_lookup',
							'type' => 'select',
							'html' => createBlankSelect('product_subid_manual_lookup',' '))*/


					);
}
function createRetailSalesInvoiceCusomterView($pos_sales_invoice_id)
{
	$html = '';
	//************************** CUSTOMER  ***********************************************
	$html .= ' <div class = "customer_invoice_div">';
	$html .= createCustomerHtmlTable($pos_sales_invoice_id, 'view');
	$html .= '</div>';
	return $html;
}
function createCustomerHtmlTable($pos_sales_invoice_id, $type ='edit')
{
	$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
	$pos_customer_id = getCustomerFromSalesInvoice($pos_sales_invoice_id);
	$invoice_url = 'retail_sales_invoice.php?type=edit&pos_sales_invoice_id='.$pos_sales_invoice_id;
	$view_invoice_url = POS_ENGINE_URL . '/sales/retailInvoice/retail_sales_invoice.php?type=view&pos_sales_invoice_id='.$pos_sales_invoice_id;
	$customer = array(
						array( 'db_field' => 'full_name',
								'type' => 'td',
								'tags' => '  size="10"',
								'caption' => 'Name',
								'value' => getCustomerFullName($pos_customer_id),
								'validate' => 'none'
								),
						array( 'db_field' => 'email1',
								'type' => 'input',
								'tags' => ' size="30" ',
								'caption' => 'Email',
								'value' => getCustomerEmail($pos_customer_id),
								'validate' => 'email'
								),
						array( 'db_field' => 'phone',
								'type' => 'input',
								'tags' => '  size="12" ',
								'caption' => 'Phone',
								'value' => getCustomerPhone($pos_customer_id),
								'validate' => 'none'
								),
							array(	'db_field' => 'pos_address_id',
								'type' => 'select',
								'tags' => '  size="20" ',
								'caption' => 'Address',
								'html' => createCustomerAddressSelect('pos_address_id', 'false', $pos_customer_id,'off', ' onchange="changeAddress(this)" ', 'add'),
								'value' => getSalesInvoiceAddress($pos_sales_invoice_id),
								'validate' => 'none')
								
								);
	$html = '<TABLE id = "customer_invoice_main" name = "customer_invoice_main" class ="customer_invoice_main">';
	$html .= '<TR >';								
	
		
	$html .= createHiddenInput('pos_customer_id', $pos_customer_id);
	if($pos_customer_id != 0)
	{
		
		 $url = "select_customer.php?pos_customer_id=\'" .$pos_customer_id."\'&complete_location=" . urlencode($invoice_url).'&search=true';
		$select_address_url = 'retail_sales_invoice.php?pos_sales_invoice_id='.$pos_sales_invoice_id;
		if($type=='view')
		{
		}
		else
		{
			$html .=  '<td><input class = "button" type="button" style="width:130px" name="add_customer" value="Edit/Change Customer" onclick="saveDraftAndGo(\''.$url.'\')"/></td>';
		}
		//$html .= '<th>Customer ID</th>' .createTDFromTD_def($customer);
		for($i=0;$i<sizeof($customer);$i++)
		{
			$html .= createTHFromTD_def($customer[$i]);
			if($type=='view')
			{
				if($customer[$i]['db_field'] == 'pos_address_id')
				{
					$html .= '<td>' . getFullAddress($customer[$i]['value']) . '</td>';
					//$html .=  '<td><input class = "button" type="button" style="width:80px" name="change_address" value="Change" onclick="open_win(\''.$select_address_url.'\')"/></td>';
				}else
				{
					$html .= '<td>' . $customer[$i]['value'] . '</td>';
				}
				
			}
			else
			{
				$html .= createTDFromTD_def($customer[$i]);
			}
		}

	}
	else
	{
		if($type=='view')
		{
			
		}
		else
		{
			$html .=  '<td><input class = "button" type="button" style="width:120px" name="add_customer" value="Select Customer" onclick="lookupCustomer(\''.urlencode($invoice_url).'\')"/></td>';
		}
	}
	$html .= '</tr>';
	$html .= '</table></p>';
	$html .='<script>var pos_customer_id = '.$pos_customer_id.';</script>';
	$html .='<script>var invoice_url = "'.urlencode($invoice_url).'";</script>';
	return $html;
}
function createRetailSalesInvoiceFooterView($pos_sales_invoice_id)
{
	$html = '';
//************* FOOTER *********************************
	$html .= '<div style="clear:right;"></div>';
	$html .='<div style="float:right;">';
	$footer_table_name = 'invoice_footer';
	$footer_table_def = createRetailSalesInvoiceContentsFooterTableDef($pos_sales_invoice_id);
	$footer_data = array();
	//$totals = getRetailSalesInvoiceTotalArray($pos_sales_invoice_id);
	$footer_data['full_price_subtotal'] = getSalesInvoiceFullPriceTotal($pos_sales_invoice_id);
	//echo getSingleValueSQL("SELECT sum(applied_instore_discount) FROM pos_sales_invoice_contents WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
	$footer_data['shipping_amount'] = getSalesInvoiceShippingAmount($pos_sales_invoice_id);
	$footer_data['discounted_subtotal'] = getSalesInvoiceDiscountedTotal($pos_sales_invoice_id);
	$footer_data['pre_tax_promotion_amount'] = getInStorePromotionsApplied($pos_sales_invoice_id);
	$footer_data['pre_tax_subtotal'] = getPreTaxSubTotal($pos_sales_invoice_id);
	//$footer_data['invoice_tax_total'] = getLocalRegularTax($pos_sales_invoice_id) + getLocalExemptTax($pos_sales_invoice_id)+getStateRegularTax($pos_sales_invoice_id);
	//$footer_data['invoice_tax_total'] = getTaxTotal($pos_sales_invoice_id);
	$footer_data['invoice_tax_total'] = number_format(getSalesInvoiceTaxTotalFromContents($pos_sales_invoice_id),2);
	$footer_data['post_tax_promotion_amount'] = getManufacturerPromotionsApplied($pos_sales_invoice_id);
	$footer_data['total_quantity'] = getSalesInvoiceNumberOfItems($pos_sales_invoice_id);
	$footer_data['total_returns'] = -getSalesInvoiceReturns($pos_sales_invoice_id);
	$footer_data['le_grande_total'] = '$' .number_format(getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id),2);
	$footer_table_tags = ' class="invoice_summary_table_view" ';
	$html .= createHorizontalViewHTMLTable($footer_table_name, $footer_table_def, $footer_data, $footer_table_tags);
	return $html;
}
function createRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id, $invoice_table_name)
{

$table_object_name = $invoice_table_name . '_object';

	$tax_category_names_ids = getSalesTaxCategoriesIdsAndNames();
	$discount_codes = getDiscountCodes();

	$columns = array(
		
				array(
					'db_field' => 'pos_product_id',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'pos_product_sub_id',
					'type' => 'hidden',
					),
				/*array(
					'db_field' => 'card_number',
					'type' => 'hidden',
					'POST' => 'no'
					),*/
				array(
					'db_field' => 'content_type',
					'type' => 'hidden'
					),
				array(
					'db_field' => 'pos_state_tax_jurisdiction_id',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'pos_state_regular_sales_tax_rate_id',
					'type' => 'hidden',
					//'POST' => 'no'

					),
				array(
					'db_field' => 'pos_state_exemption_sales_tax_rate_id',
					'type' => 'hidden',
					//'POST' => 'no'

					),
				array(
					'db_field' => 'state_regular_tax_rate',
					'type' => 'hidden',

					),
				array(
					'db_field' => 'state_exemption_tax_rate',
					'type' => 'hidden',

					),
				array(
					'db_field' => 'state_exemption_value',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'pos_local_tax_jurisdiction_id',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'pos_local_regular_sales_tax_rate_id',
					'type' => 'hidden',
					///'POST' => 'no'
					),
				array(
					'db_field' => 'pos_local_exemption_sales_tax_rate_id',
					'type' => 'hidden',
					//'POST' => 'no'
					),
				array(
					'db_field' => 'local_regular_tax_rate',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'local_exemption_tax_rate',
					'type' => 'hidden',
					),
				array(
					'db_field' => 'local_exemption_value',
					'type' => 'hidden',
					),
			/*	array(
					'db_field' => 'tax_type',
					'type' => 'hidden',
					'POST' => 'no'
					),
				array(
					'db_field' => 'item_tax_type',
					'type' => 'hidden',
					'price_array_index' => 'quantity',
					'POST' => 'no'
					),*/
				/*array(
					'db_field' => 'exemption_value',
					'type' => 'hidden',
					),*/

				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'row_checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'input',
				'element' => 'input',
				'element_type' => 'none',
				'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										'readOnly' => '"true"',
										'size' => '"3"')
					),
				array('db_field' => 'barcode',
					'caption' => 'Code',
					//'td_width' => '50%',
					//'th_width' => '50%',
					'word_wrap' => 10,
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
				/*array('caption' => 'Brand',
						'db_field' => 'brand_name',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Style Number',
				'db_field' => 'style_number',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Color<BR>Code',
				'db_field' => 'color_code',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"5"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Color<BR>Description',
				'db_field' => 'color_name',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"15"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Title',
				'db_field' => 'title',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"30"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),		*/
			array('caption' => 'Description',
					'db_field' => 'checkout_description',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"80"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
			array('caption' => 'Options',
					'db_field' => 'product_options',
					'POST' => 'no',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	
					'size' => '"100"',
											'readOnly' => 'true',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
			/*	array('caption' => 'Size',
				'db_field' => 'size',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"4"',
											'className' => '"size"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),	*/	
				array('caption' => 'Price',
					'db_field' => 'retail_price',
					'type' => 'input',
					'round' => 2,
					'valid_input' => '0123456789.',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
				array('caption' => 'Sale Price',
					'db_field' => 'sale_price',
					'type' => 'input',
					'round' => 2,
					'valid_input' => '0123456789.',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){calculateTotals(this);}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
				array('caption' => 'QTY',
					'db_field' => 'quantity',
					'type' => 'input',
					'valid_input' => '-01',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"3"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){calculateTotals(this);}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
					array('db_field' => 'special_order',
					'caption' => 'Order',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){enablePaidCheck(this)}'
											),
					'td_tags' => array(	'className' => '"test"',
										//'style.backgroundColor' => '"#fff";',
										//'style.textAlign' => '"center";',
										//'style.verticalAlign' => '"middle";',
										//'align' => '"center"'
											)	),
				array('db_field' => 'paid',
					'caption' => 'Paid',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	
					'disabled' => 'true','onclick' => 'function(){calculateTotals(this);}'
											)),
				array('db_field' => 'ship',
					'caption' => 'Ship',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){checkAndAddShipping(this);calculateTotals(this);}'
											),
					'td_tags' => array(	'className' => '"test"',
										'style.backgroundColor' => '"#fff";',
										'style.textAlign' => '"center";'
											)),						
				array('caption' => 'Discount<br>Code<br>(Required)',
					'db_field' => 'pos_discount_id',
					'type' => 'select',
					//this part is for the 'view'
					'html' => createDiscountCodeSelect('pos_dicount_id', 'false', 'off'),
					'select_names' => $discount_codes['discount_name'],
					'select_values' => $discount_codes['pos_discount_id'],
					'properties' => array(	'style.width' => '"5em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){updateDiscount(this);}',
											//'onkeyup' => 'function(){updateDiscount(this);}',
											//'onmouseup' => 'function(){updateDiscount(this);}'
											)
											),
				array('caption' => 'Item<BR>Discount<BR>ex:10% or $12.90',
					'db_field' => 'discount',
					'type' => 'input',
					'valid_input' => '$%0123456789.',
					//'round' => 2,

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'readOnly' => 'true',
											//'className' => '"nothing"',
											'className' => '"readonly"',

											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){calculateTotals(this);}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);calculateTotals(this);}')),
				
				
				
				
				
				
				array(
					'db_field' => 'discount_type',
					//'price_array_index' => 'quantity',
					'type' => 'hidden'
					),
				array('caption' => 'Item<br>Applied<br>Instore<br>Discount',
					'db_field' => 'applied_instore_discount',
					'type' => 'input',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 2,
					//'POST' => 'no',
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){calculateTotals();}',
											'readOnly' => 'true')),	
			
				array('caption' => 'Tax Category',
					'db_field' => 'pos_sales_tax_category_id',
					'type' => 'select',

							'select_names' => $tax_category_names_ids['tax_category_name'],
						'select_values' => $tax_category_names_ids['pos_sales_tax_category_id'],
					'properties' => array(	'style.width' => '"7em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){}',
											'onkeyup' => 'function(){}',
											'onmouseup' => 'function(){}',
											//'onchange' => 'function(){alert(this.options[this.selectedIndex].text);}'
											'onchange' => 'function(){updateTax(this);}')),
				array('caption' => 'Taxable<br>Total',
					'db_field' => 'taxable_total',
					'POST' => 'no',
					'type' => 'input',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 2,
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => 'true',
											'onblur' => 'function(){calculateTotals(this);}')),
				array('caption' => 'Tax Rate',
					'db_field' => 'tax_rate',
					//'POST' => 'no',
					'type' => 'input',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 3,
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => 'true',
											'onblur' => 'function(){calculateTotals(this);}')),	
				
				array('caption' => 'Tax Total',
					'db_field' => 'tax_total',
					//'POST' => 'no',
					'type' => 'input',
					//'price_array_index' => 'quantity',
					'element' => 'input',
					'element_type' => 'text',
					'round' => 2,
					'properties' => array(	'size' => '"5"',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => 'true',
											'onblur' => 'function(){calculateTotals(this);}')),	
				
					
					array('caption' => 'Line Total',
					'db_field' => 'extension',
					'type' => 'input',
					//'POST' => 'no',
					//'footer' => createRetailSalesInvoiceContentsFooterTableDef($pos_sales_invoice_id),
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					
					'properties' => array(	'size' => '"10"',
											'className' => '"readonly"',
											'onclick' => 'function(){calculateTotals(this);}',
											'readOnly' => 'true')),		
				array('caption' => 'Comments',
					'db_field' => 'comments',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	
											'className' => '"comments"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}'))
				
				
			);			
					
	
	return $columns;
	
	
	
}
function createRetailSalesInvoiceContentsView($pos_sales_invoice_id)
{
	$html = '';
	
//************************** INVOICE CONTENTS ***********************************************
	$invoice_contents = getInvoiceContents($pos_sales_invoice_id);
	//for the view we need to modify these contents
	for($row=0;$row<sizeof($invoice_contents);$row++)
	{
		//$invoice_contents[$row]['pos_discount_id'] = getDiscountName($invoice_contents[$row]['pos_discount_id']);
		//$invoice_contents[$row]['pos_sales_tax_category_id'] = getTaxCategoryName($invoice_contents[$row]['pos_sales_tax_category_id']);
		//$invoice_contents[$row]['discount'] = ($invoice_contents[$row]['discount_type'] == 'PERCENT') ? number_format($invoice_contents[$row]['discount'],2).'%' : '$' . number_format($invoice_contents[$row]['discount'],2);
	}
	$invoice_table_name = 'invoice_table';
	$invoice_contents_table_def = createRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id, $invoice_table_name);

	$html .= createStaticViewDynamicTableV2($invoice_table_name, $invoice_contents_table_def, $invoice_contents, 'class="static_contents_table" style="width:100%" ');

	return $html;
}
function createRetailSalesInvoiceView($pos_sales_invoice_id)
{
	$html = '';
	//************************** INVOICE OVERVIEW ***********************************************
	$html .= ' <div class = "retail_sales_invoice_div">';
	$html .= createInvoiceHtmlTable($pos_sales_invoice_id);
	$html .= '<script>var invoice_date = ' .getSalesInvoiceDateFromDatetime($pos_sales_invoice_id) .';</script>';
	$html .= '</div>';	
	return $html;
}
function createRetailSalesInvoicePromotionsView($pos_sales_invoice_id)
{
	$promotion_data = getSalesInvoicePromotions($pos_sales_invoice_id);
	$html = '';
//******************** PROMOTIONS ***************************************************//
	
	if(sizeof($promotion_data)>0)
	{
		$html .= '<div class="discount_table" style="display:inline-block;float:left">';
		//promotion table
		$html .= '<h3>Enter Promotions Here. Promotions can only apply to full price items.</h3>';
		$promotion_table_name = 'promotion_table';
		$promotion_table_def = createRetailSalesPromotionsTableDef($promotion_table_name);	
		$html .= createStaticViewDynamicTable( $promotion_table_def, $promotion_data);
		$html .= '</div>';
	}
	
		$html .='</div>';
$html .= '<div style="clear:right;"></div>';
	$html.='</div>';
	return $html;
}
function getSalesInvoiceTotalPayment($pos_sales_invoice_id)
{
	$sql = "SELECT sum(payment_amount) FROM pos_customer_payments
			LEFT JOIN pos_sales_invoice_to_payment USING (pos_customer_payment_id)
			WHERE pos_sales_invoice_id = $pos_sales_invoice_id";
	return getSingleValueSQL($sql);
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
		//for the view we need to modify these contents
		/*for($row=0;$row<sizeof($payment_contents);$row++)
		{
			//$payment_contents[$row]['pos_customer_payment_method_id'] = getCustomerPaymentMethodName($payment_contents[$row]['pos_customer_payment_method_id']);
		
		}*/
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
function createInvoiceHtmlTable($pos_sales_invoice_id)
{
	$invoice_data = getSalesInvoiceData($pos_sales_invoice_id);
	
	
	$html = '<TABLE id = "retail_sales_invoice_main" name = "retail_sales_invoice_main" class ="retail_sales_invoice_main">';
	$html .= '<TR >';								
	
	//$html .= '<th>SALES ASSOCIATE</th><td>' .getUserFullName($invoice_data[0]['pos_user_id']) . '</td>';
	//$html .= '<th>INVOICE DATE</th>' . '<td>'. dateSelect('invoice_date', getdatefromdatetime($invoice_data[0]['invoice_date']), ' style = "width:100%" ') .'</td>'.newline();//createTDFromTD_def($date_array);
	$html .= '<th width="100" style=text-align:left;">INVOICE DATE</th>' . '<td width="70" align="left">'. getdatefromdatetime($invoice_data[0]['invoice_date']).'</td>'.newline();
	$html .= '<th width="100" >SA</th>' . '<td  >'. getUserFullName($invoice_data[0]['pos_user_id']).'</td>'.newline();
	$html .= '<th width="100" >TERMINAL</th>' . '<td width="200" align="left">'. getTerminalName($invoice_data[0]['pos_terminal_id']).'</td>'.newline();
	$html .= '<th width="100" >STORE</th>' . '<td width="70" align="left">'. getStoreName($invoice_data[0]['pos_store_id']).'</td>'.newline();
	//$html .= '<th width="100" >Reg#</th>' . '<td width="70" align="left">'. ''.'</td>'.newline();
	$html .= '<th style="text-align:right;">INVOICE NUMBER</th><td width="70" align="right"><font color="#F00"> ' .str_pad($pos_sales_invoice_id, 6, "0", STR_PAD_LEFT).'</font></td>';
	
	$html .= '</tr>';
	$html .= '</table></p>';

	//$html .= '<script>var invoice_table_def = ' . prepareTableDefArrayForJavascriptVerification(array($table_def)) . ';</script>';
	$html .= '<script>var invoice_main_table_id = "invoice_main";</script>';
	return $html;
}
?>