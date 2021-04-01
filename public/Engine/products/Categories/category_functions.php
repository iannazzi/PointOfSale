<?php
function createCategoryTableDef($type, $key_val_id)
{
if ($type == 'New')
{
	$pos_category_id = 'TBD';
}
else
{
	$pos_category_id = $key_val_id['pos_category_id'];
}
$table_def = array(
					   array( 'db_field' => 'pos_category_id',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" ',
								'type' => 'input',
								'value' => $pos_category_id,
								'validate' => 'none'),
						array( 'db_field' => 'parent',
								'type' => 'select',
								'caption' => 'Parent Category',
								'html' => createCategorySelect('parent', 'false')),
						array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false')),
						array( 'db_field' => 'name',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'priority',
								'type' => 'input',
								'caption' => 'priority',
								'validate' => 'none'),
						array( 'db_field' => 'default_product_priority',
								'type' => 'input',
								'caption' => 'Default Product Priority',
								'validate' => 'none'),
						array( 'db_field' => 'description',
								'type' => 'textarea',
								'validate' => 'none'),
						array( 'db_field' => 'description_bottom',
								'type' => 'textarea',
								'validate' => 'none'),
						array( 'db_field' => 'meta_keywords',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'meta_title',
								'type' => 'textarea',
								'validate' => 'none'),
						array( 'db_field' => 'meta_description',
								'type' => 'textarea',
								'validate' => 'none'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'tags' => 'checked="checked" ',
								'validate' => 'none')
								
								);	

	return $table_def;
}
?>