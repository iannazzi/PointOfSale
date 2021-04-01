<?php
/*
	invoice.php
	This is rhe sales invoice
	
	// 	chk	barcode		mfg		style		color		size		title	qty		retail
		chk big line for comments
*/
//this is the javascript versions.....
$tax_javascript_version = 'tax_calculations.2013.03.12.js';
$retail_sales_javascript_version = 'retail_sales_invoice.2013.03.12.js';
$css_styles_version = 'retail_invoice_styles.2013.03.12.css';

$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales Invoice';
require_once('../sales_functions.php');
$pos_sales_invoice_id = 77;//getPostOrGetID('pos_sales_invoice_id');
$db_table = 'pos_sales_invoice';
$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
if(checkForValidIDinPOS($pos_sales_invoice_id, $db_table, 'pos_sales_invoice_id'))
{
	$page_title = 'Sales Invoice ' .$pos_sales_invoice_id;
	//if a customer id is passed in, then update the sales invoice with the customer ID.
	if(isset($_GET['pos_customer_id']))
	{
		$pos_customer_id = scrubInput($_GET['pos_customer_id']);
		$update['pos_customer_id'] = $pos_customer_id;
		$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $update);
	}

	$complete_location = 'list_retail_sales_invoices.php';
	$cancel_location = 'list_retail_sales_invoices.php';

	$payment_location = 'add_customer_payment.php?pos_sales_invoice_id='.$pos_sales_invoice_id;
	
	//this is needed with no header
	$html = '<link rel="stylesheet" href="' . STYLE_SHEET . '" type="text/css" media="all" />'; 
	$disable_check_login =  (isset($disable_check_login)) ? $disable_check_login : false;
	$html.= includeJavascriptLibrary($disable_check_login);
	
	

	$html .= '<link type="text/css" href="'.$css_styles_version.'" rel="Stylesheet"/>'.newline();
	$html .=  '<script src="'.$retail_sales_javascript_version.'"></script>'.newline();
	$html .=  '<script src="'.$tax_javascript_version.'"></script>'.newline();
	$html .= '<script src="iphone.js"></script>';
	//$html .= '<meta name="viewport" content="user-scalable=no, width=device-width" />';

//$html .= '<link rel="stylesheet" type="text/css" href="iphone.css" media="only screen and (max-width: 961)" />';
//$html .= '<link rel="stylesheet" type="text/css" href="desktop.css" media="screen and (min-width: 481px)" />';


	$html .=  '<script src="'.AJAX_PRODUCT_SUB_ID.'"></script>'.newline();
	$html .= '<script>var pos_sales_invoice_id = '.$pos_sales_invoice_id. ';</script>';

	$form_id = "sales_invoice_form";
	$form_action = 'sales_invoice.form.handler.php';
	$html .=  '<form id = "' . $form_id . '" action="'.$form_action.'.php" method="post" onsubmit="return validateInvoiceForm()">';
	
	$html .= '<div class = "invoice">';
//************************** INVOICE OVERVIEW ***********************************************
	$html .= ' <div class = "retail_sales_invoice_div">';
	$html .= createIphoneInvoiceHtmlTable($pos_sales_invoice_id);
	$html .= '<script>var invoice_date = "' .getSalesInvoiceDateFromDatetime($pos_sales_invoice_id) .'";</script>';
	//$html .= '<script>var tax_method = "' . getSalesInvoiceTaxCalculationMethod($pos_sales_invoice_id) . '";</script>';
	$html .= '<script>var tax_method = "average";</script>';
	$html .= '</div>';
//************************** CUSTOMER  ***********************************************
	$html .= ' <div class = "customer_invoice_div">';
	$html .= createIphoneCustomerHtmlTable($pos_sales_invoice_id);
	$html .= '</div>';
//************************** PRODUCT LOOKUP TABLE  ***********************************************
	$html .= ' <div class = "product_lookup_div">';
	$html .= '<table class = "product_lookup_outline">';
	$html .= '<TR><td>';
	$html .= productLookUpTable();
	$html .= '</td></TR>';
	$html .= ' </table>';
	$html .= '</div>';



//************************** INVOICE CONTENTS ***********************************************

	//invoice contents table
	$invoice_contents = getInvoiceContents($pos_sales_invoice_id);
	$invoice_table_name = 'invoice_table';
	$invoice_contents_table_def = createIphoneRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id, $invoice_table_name);
	
	$html .= createDynamicTableReuse($invoice_table_name, $invoice_contents_table_def, $invoice_contents, $form_id, ' class="dynamic_contents_table" style="width:100%" ');
	
//************ TOTALS ***************************************************
	$footer_table_name = 'invoice_footer';
	$footer_table_def = createRetailSalesInvoiceContentsFooterTableDef($pos_sales_invoice_id);
	$footer_table_tags = ' class="invoice_summary_table" ';
	$footer_data = array();
	$html .= createHorizontalInputHTMLTable($footer_table_name, $footer_table_def, $footer_data, $footer_table_tags);

//******************** PROMOTIONS ***************************************************//
	$html .= '<div class="discount_table">';
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
		$html .= '<div >';
		$go_url = POS_ENGINE_URL . '/sales/retailInvoice/view_retail_sales_invoice.php?pos_sales_invoice_id='.$pos_sales_invoice_id;
			$html .=  '<INPUT class = "button" type="button"  style="margin: 2px 4px 6px 30px;"value="Save Invoice" onclick="saveDraft()" />'.newline();
			$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Save/Exit" onclick="saveDraftAndGo(\''.$go_url.'\')" />'.newline();
			$go_url = 'retail_sales_invoice_payments.php?pos_sales_invoice_id='.$pos_sales_invoice_id;
			$html .=  '<INPUT class = "button" type="button" style="width:200px" value="Continue To Payments" onclick="saveDraftAndGo(\''.$go_url.'\')"
			 />'.newline();
			//$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Delete Invoice" onclick="deleteInvoice()" />'.newline();
		 $html .='</div>';	

		 

	$html .=  '	<script>var formID = "'.$form_id.'";</script>';
	$html .= '<script>document.getElementById(\'barcode\').focus();</script>';
	$html .= '</form>';
	//$html .= '<div style="clear:both;"/>';
//finally init the form:
	$html .= '<script>init_sales_invoice()</script>';
	//include (HEADER_FILE);
	echo $html;
	//echo '<script>init_sales_invoice()</script>';
	//include (FOOTER_FILE);
}
else
{
	include (HEADER_FILE);
	echo 'Not A Valid Id';
	include (FOOTER_FILE);
}



?>



