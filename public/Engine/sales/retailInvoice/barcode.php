<?
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
require_once('../sales_functions.php');
$barcode = scrubInput(getPostOrGetValue('barcode'));
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');

//use this url to test:
//http://www.craigiannazzi.com/POS_TEST/Engine/products/AjaxProduct/barcode.php

//first make a determination of what it is
if (sizeof(getSQL("SELECT pos_store_credit_card_number_id FROM pos_store_credit_card_numbers WHERE card_number = '$barcode'")) > 0)
{
	
	// gift card purchase
	
	//here we should check to make sure the card had not been previously assigned...
	if (sizeof(getSQL("SELECT card_number FROM pos_store_credit WHERE card_number = '$barcode'")) > 0)
	{
		//error the card has been used
		$error['error'] = 'This Card Number has been previously Assigned a value';
		echo  json_encode($error) . "\n";
		exit();
	}
	$barcode_data['card_number'] = $barcode;
	$barcode_data['content_type'] = 'CREDIT_CARD';
	$barcode_data['quantity'] = 1;
	$barcode_data['style_number'] = '-';
	$barcode_data['barcode'] = $barcode;
	$barcode_data['size'] = '-';
	$barcode_data['sale_price'] = '-';
	$barcode_data['color_code'] = '-';
	$barcode_data['color_name'] = '-';
	$barcode_data['brand_name'] = '-';
	$barcode_data['title'] = 'Gift Card';
	$barcode_data['checkout_description'] = 'Gift Card';
	//$barcode_data['pos_sales_tax_category_id'] = $pos_sales_tax_category_id; 
	echo json_encode($barcode_data) . "\n";
	
}
else if($barcode == 'gift card')
{
	// gift card purchase
	$barcode_data['card_number'] = $barcode;
	$barcode_data['content_type'] = 'CREDIT_CARD';
	$barcode_data['quantity'] = 1;
	$barcode_data['style_number'] = '-';
	$barcode_data['barcode'] = '';
	$barcode_data['size'] = '-';
	$barcode_data['sale_price'] = '-';
	$barcode_data['color_code'] = '-';
	$barcode_data['color_name'] = '-';
	$barcode_data['brand_name'] = '-';
	$barcode_data['title'] = 'Gift Card';
	$barcode_data['checkout_description'] = 'Gift Card';
	//$barcode_data['pos_sales_tax_category_id'] = $pos_sales_tax_category_id; 
	echo json_encode($barcode_data) . "\n";
}
else if (sizeof(getSQL("SELECT pos_product_sub_id FROM pos_products_sub_id WHERE pos_product_sub_id = '$barcode'")) >0)
{
	//product
	$product_sql = "SELECT pos_product_sub_id, pos_products_sub_id.pos_product_id, product_subid_name, attributes_list,
		 retail_price, sale_price, title, style_number,
		 
		   concat(pos_products.style_number,',',
		
			(SELECT group_concat(concat(attribute_name,':',option_name) SEPARATOR ',') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			)   as product_options
		 
		FROM pos_products_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE pos_product_sub_id = '$barcode'";
	$data = getSQL($product_sql);

	if(sizeof($data)>0)
	{
		echo json_encode(convertProductDataForReturn($data,$pos_sales_invoice_id)) . "\n";
	}
}
else if (sizeof(getSQL("SELECT pos_product_sub_id FROM pos_products_sub_id WHERE product_subid_name = '$barcode'")) >0)
{
	//definately a barcode
	//get the product id, colors, sizes

	$sql = "SELECT pos_product_sub_id, pos_products_sub_id.pos_product_id, product_subid_name, attributes_list,
		 retail_price, sale_price, title, style_number,
		 
		 concat(pos_products.style_number,',',
		
			(SELECT group_concat(concat(attribute_name,':',option_name) SEPARATOR ',') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			)   as product_options
				 
		 
		FROM pos_products_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE product_subid_name = '$barcode'";
	$data = getSQL($sql);

	if(sizeof($data)>0)
	{
		
	
		echo json_encode(convertProductDataForReturn($data,$pos_sales_invoice_id)) . "\n";
	}
}
elseif(sizeof(getSQL("SELECT pos_promotion_id FROM pos_promotions WHERE promotion_code = '$barcode'")) >0)
{
	$sql = "SELECT promotion_code, promotion_name, pos_promotion_id, promotion_type, promotion_amount, expired_value, date(expiration_date) as expiration_date, percent_or_dollars, qualifying_amount

	FROM pos_promotions
	WHERE promotion_code = '$barcode' AND active = 1";	
		
	$data = getSQL($sql);

	if(sizeof($data)==1)
	{	
		$data[0]['content_type'] = 'PROMOTION';
		echo json_encode($data[0]) . "\n";
	
	}
	elseif(sizeof($data)>1)
	{
		$error['error'] = 'Promotion Error: Multiple Results';
		echo  json_encode($error) . "\n";
	}
	else
	{
		$error['error'] = 'Not Valid';
		echo  json_encode($error) . "\n";
	}
}
else if($barcode == 'ship')
{
	$barcode_data['content_type'] = 'SHIPPING';
	$barcode_data['title'] = 'SHIPPING';
	$barcode_data['checkout_description'] = 'SHIPPING';
	
	//we have to get the gd tax category for shipping
	//consider shipping a service....
	//the tax category will pull from that service..
	
	//
	
}
elseif(sizeof(getSQL("SELECT pos_service_id FROM pos_services WHERE barcode = '$barcode'")) >0)
{
	//service - but what kind?
	$barcode_data['content_type'] = 'SERVICE';
	$barcode_data['title'] = 'service';
	$barcode_data['checkout_description'] = 'service';
}
else
{
	$error['error'] = 'No Data Found For Barcode';
	echo  json_encode($error) . "\n";
}
function convertProductDataForReturn($data,$pos_sales_invoice_id)
{
	$pos_product_sub_id = $data[0]['pos_product_sub_id'];
		$barcode_data['content_type'] = 'PRODUCT';
		$barcode_data['style_number'] = $data[0]['style_number'];
		$barcode_data['barcode'] = $data[0]['pos_product_sub_id'];
		$barcode_data['pos_product_id'] = $data[0]['pos_product_id'];
		$barcode_data['pos_product_sub_id'] = $data[0]['pos_product_sub_id'];
		$barcode_data['quantity'] = 1;
		$barcode_data['retail_price'] = $data[0]['retail_price'];
	
		$barcode_data['sale_price'] = ($data[0]['sale_price'] == 0) ? $data[0]['retail_price'] : $data[0]['sale_price'];
		$barcode_data['title'] = $data[0]['title'];
		$barcode_data['checkout_description'] = getProductCheckoutDescription($pos_product_sub_id);
		$barcode_data['product_options'] = $data[0]['product_options'];
		$barcode_data['brand_name'] = getBrandName(getBrandFromProductId($data[0]['pos_product_id']));
		
		//$barcode_data['applied_instore_discount'] = 0;
		//$barcode_data['tax_total'] = 0;
		//$barcode_data['discount'] = 0;
		//$barcode_data['comments'] = '';
	
	
		$barcode_data['size'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Size'));
		$barcode_data['color_code'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Color'));
		$barcode_data['cup'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Cup'));	
		$barcode_data['color_name'] = getProductOptionName($pos_product_sub_id, getProductAttributeId('Color'));
	   $barcode_data['big_title'] = $barcode_data['brand_name'] . ' ' . $data[0]['title'] . ' in ' . $barcode_data['color_name'];
		$pos_sales_tax_category_id = getProductSalesTaxCategoryId($data[0]['pos_product_id']);
		$barcode_data['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
		//now for the tax
		//is it taxable?
	
		//is it shipped or in-store?
		// cuurently in store
		$shipped = false;
		if($shipped)
		{
			//get the state and tax jurisdiction
			//because this is a zappy it will not be initially shipped.....
			//we will need to click on 'shipped' to get the ship tax rate....
		}
		else
		{
			//sold in store
			$pos_store_id = $_SESSION['store_id'];
			$pos_local_tax_jurisdiction_id = getTaxJurisdictionOfStore($pos_store_id);
			$pos_state_id = getTaxJurisdictionStateID($pos_local_tax_jurisdiction_id);
			$pos_state_tax_jurisdiction_id = getStateTaxJurisdictionId($pos_state_id);
			//the tax rate id needs to be tagged to the sale item -- however that can be deleted
		
		}
	
		$invoice_date = getSalesInvoiceDate($pos_sales_invoice_id);
		$tax = getProductTaxArray($pos_local_tax_jurisdiction_id, $pos_state_id, $pos_sales_tax_category_id, $invoice_date);

		$return_data = array_merge($barcode_data,$tax);
		return $return_data;
}
function getProductCheckoutDescription($pos_product_sub_id)
{
	//category: Brand name Title 'size' size 'in color' Color name
	$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);
	$return_string ='';
	$return_string .= getProductCategoryName($pos_product_id);
	$return_string .=  ': ' ;
	$return_string .= getProductBrandCode($pos_product_id);
	$return_string .= ' ';
	$return_string .= getProductTitle($pos_product_id);
	//$return_string .= ' Size:';
	//$return_string .= getProductOptionName($pos_product_sub_id, getProductAttributeId('Size'));
	//$return_string .= ' In Color ';
	//$return_string .= getProductOptionName($pos_product_sub_id, getProductAttributeId('Color'));
	return  $return_string;
}
function getPorductOptionsForCheckout($pos_product_sub_id)
{
}
?>