<?php
$page_level = 5;
$page_navigation = 'purchase_orders';
$page_title = 'entry_lock';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);
$table = getPostorGetValue('table');
$primary_key = getPostorGetValue('primary_key_name');
$primary_key_value = getPostorGetValue('primary_key_value');
$key_val_id[$primary_key] = $primary_key_value;
lock_entry($table, $key_val_id);
echo 'locked mannnnn';

?>