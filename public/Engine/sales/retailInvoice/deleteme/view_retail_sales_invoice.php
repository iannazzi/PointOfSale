<?php 




$binder_name = 'Sales Invoices';
$access_type = 'READ';
require_once('../sales_functions.php');
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
$page_title = 'Sales Invoice '.$pos_sales_invoice_id;
$db_table = 'pos_sales_invoice';
$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
$retail_sales_javascript_version = "view_retail_sales_invoice.2013.03.12.js";
$css_styles_version = 'retail_invoice_styles.2013.06.13.css';

if(checkForValidIDinPOS($pos_sales_invoice_id, $db_table, 'pos_sales_invoice_id'))
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
		$html .= '<div style="float:right;">';
		$html .=  '<INPUT class = "button" type="button"  style="margin: 2px 4px 6px 30px;"value="Edit Contents" onclick="open_win(\'retail_sales_invoice.php?pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
		$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Payments" onclick="open_win(\'retail_sales_invoice_payments.php?pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
		$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Customer View" onclick="open_win(\'retail_sales_invoice_customer_view.php?pos_sales_invoice_id='.$pos_sales_invoice_id.'\')" />'.newline();
		$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Print" onclick="open_win(\'print_sales_invoice.php?pos_sales_invoice_id='.$pos_sales_invoice_id.'\')"
			 />'.newline();
		 $html .='</div>';
		 
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}




?>