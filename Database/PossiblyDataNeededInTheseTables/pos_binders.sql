-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: pos
-- ------------------------------------------------------
-- Server version	5.5.46-0ubuntu0.14.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pos_binders`
--

DROP TABLE IF EXISTS `pos_binders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_binders` (
  `pos_binder_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `binder_name` varchar(40) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `binder_path` varchar(255) NOT NULL,
  `binder_folder_path` varchar(255) NOT NULL,
  `binder_file_name` varchar(255) NOT NULL,
  `navigation_caption` varchar(40) NOT NULL,
  `button_size` int(4) NOT NULL DEFAULT '200',
  `default_rooms` varchar(255) NOT NULL COMMENT '''TheOffice'',''TheBackRoom'',''TheStore'',''TheSystem''',
  `priority` int(5) NOT NULL,
  `Description` text NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_binder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_binders`
--

LOCK TABLES `pos_binders` WRITE;
/*!40000 ALTER TABLE `pos_binders` DISABLE KEYS */;
INSERT INTO `pos_binders` VALUES (1,'Purchases Journal',1,'Engine/accounting/PurchaseJournal/list_purchase_journal.php','','','Purchases Journal',150,'TheOffice, TheBackRoom',10000,'',''),(2,'General Journal',1,'Engine/accounting/GeneralJournal/list_general_journal.php','','','General Journal',150,'TheOffice',11000,'',''),(3,'Accounts',1,'Engine/accounting/Accounts/list_accounts.php','','','Accounts',150,'TheOffice',12000,'',''),(4,'Chart Of Accounts',1,'Engine/accounting/ChartOfAccounts/list_chart_of_accounts.php','','','Chart Of Accounts',150,'TheOffice',5000,'',''),(7,'Payments Journal',1,'Engine/accounting/PaymentsJournal/list_payments_journal.php','','','Payments Journal',150,'TheOffice',9000,'',''),(8,'Customers',1,'Engine/customers/list_customers.php','','','Customers',150,'TheStore',5000,'',''),(9,'Employees',0,'Engine/employees/list_employees.php','','','Employees',150,'TheOffice',3000,'',''),(10,'Manufacturers',1,'Engine/manufacturers/ListManufacturers/list_manufacturers.php','','','Manufacturers',150,'TheBackRoom',3000,'',''),(11,'Manufacturer UPC\'s',1,'Engine/manufacturers/ManufacturerUPC/list_upcs.php','','','Manufacturer UPC\'s',150,'TheBackRoom',2000,'',''),(12,'Products',1,'Engine/products/ListProducts/list_products.php','','','Products',150,'TheBackRoom',4000,'',''),(13,'Merchandise Inventory',0,'Engine/inventory/MerchandiseInventory/list_inventory.php','','','Inventory Log',150,'TheBackRoom',4000,'',''),(14,'Product Categories',1,'Engine/products/Categories/list_categories.php','','','Product Categories',150,'TheBackRoom',0,'',''),(15,'Purchase Orders',1,'Engine/purchase_orders/ListPurchaseOrders/list_purchase_orders.php','','','Purchase Orders',150,'TheBackRoom, TheOffice',0,'',''),(16,'Sales Invoices',1,'Engine/sales/retailInvoice/list_retail_sales_invoices.php','','','Sales Invoices',150,'TheStore, TheBackRoom, TheOffice',0,'',''),(17,'Stores',1,'Engine/stores/list_stores.php','','','Stores',150,'TheSystem',0,'',''),(18,'Sales Tax Categories',1,'Engine/taxes/sales_tax_categories/list_sales_tax_categories.php','','','Sales Tax Categories',150,'TheOffice',0,'',''),(19,'Sales Tax Jurisdictions',1,'Engine/taxes/sales_tax_jurisdictions/list_tax_jurisdictions.php','','','Sales Tax Jurisdictions',150,'TheOffice',0,'',''),(20,'Sales Tax Rates',1,'Engine/taxes/sales_tax_rates/list_sales_tax_rates.php','','','Sales Tax Rates',150,'TheOffice',0,'',''),(21,'System User Accounts',1,'Engine/users/ManageUserAccounts/list_users.php','','','System User Accounts',150,'TheSystem',0,'',''),(22,'User Account Settings',1,'Engine/users/UserAccountSettings/user_settings.php','','','User Account Settings',150,'TheSystem',0,'',''),(23,'Custom Binders',0,'Engine/system/CustomBinders/list_custom_binders.php','','','Custom Binders',150,'TheSystem',0,'',''),(24,'System Binders',1,'Engine/system/SystemBinders/list_system_binders.php','','','System Binders',150,'TheSystem',0,'',''),(25,'Google Feed',0,'Engine/utilities/googleFeed/google_feed.php','','','Google Feed',150,'TheSystem',0,'',''),(26,'Locations',1,'Engine/inventory/Locations/list_locations.php','','','Inventory',150,'TheBackRoom,TheOffice',10,'',''),(27,'Product Attributes',1,'Engine/products/Attributes/list_attributes.php','','','Product Attributes',150,'TheBackRoom',0,'',''),(28,'Cash Receipts Journal',0,'Engine/accounting/CashReceiptsJournal/list_cash_receipts_journal.php','','','Deposits Journal',150,'TheOffice',0,'',''),(29,'Discounts',1,'Engine/sales/discounts/list_discounts.php','','','Discounts',150,'TheOffice, TheBackRoom',0,'',''),(30,'Documents',1,'Engine/office/documents/list_documents.php','','','Documents',150,'TheOffice, TheBackRoom',0,'',''),(31,'Calandar',0,'Engine/office/calandar/calandar.php','','','Calandar',150,'TheStore, TheOffice, TheBackRoom',0,'',''),(32,'Notes',0,'Engine/office/notes/list_notes.php','','','Notes',150,'TheStore, TheOffice, TheBackRoom',0,'',''),(33,'Store Credits',1,'Engine/sales/storeCreditCards/list_store_credits.php','','','Store Credit Cards',150,'TheStore, TheBackRoom, TheOffice',0,'',''),(34,'Employee Handbook',1,'Engine/employees/handbook/handbook.php','','','Employee Handbook',150,'TheBackRoom',0,'',''),(35,'Promotions',1,'Engine/sales/promotions/list_promotions.php','','','Promotions',150,'TheOffice, TheBackRoom',0,'',''),(36,'Images',1,'Engine/products/ProductImages/list_product_images.php','','','Images',150,'TheBackRoom',0,'',''),(37,'Services',1,'Engine/services/services/list_services.php','','','Services',150,'TheBackRoom',0,'',''),(38,'Shipping Methods',1,'Engine/services/shipping_options/list_shipping_options.php','','','Shipping Methods',150,'TheOffice, TheBackRoom',0,'',''),(39,'Terminals',1,'Engine/system/terminals/list_terminals.php','','','Terminals',150,'TheSystem',0,'',''),(40,'Printers',1,'Engine/system/printers/list_printers.php','','','Printers',150,'TheSystem',0,'',''),(41,'Cash Drawers',1,'Engine/system/cashDrawers/list_cash_drawers.php','','','Cash Drawers',150,'TheSystem',0,'',''),(42,'Settings',1,'Engine/system/settings/list_settings.php','','','System Settings',150,'TheSystem',0,'',''),(43,'Product Sub Ids',1,'Engine/products/ProductSubId/list_product_sub_ids.php','','','Product Sub Ids',150,'TheStore,TheBackRoom',0,'',''),(44,'User Groups',1,'Engine/users/UserGroups/list_user_groups.php','','','User Groups',150,'TheSystem',0,'',''),(45,'POS',1,'Engine/sales/POS_V1/POS_V1.php','','','Point Of Sale',150,'TheStore, TheBackRoom',0,'This is the current version of the Point of Sale access point for creating invoices','This would be considered the \'active\' version of the POS. Changing this value will prevent having to change any user values and upgrade all systems...'),(46,'POS Store Management',1,'Engine/sales/POS_V1/pos_store_management.php','','','POS Store Management',150,'TheStore, TheBackRoom',0,'',''),(47,'POS Invoices',1,'Engine/sales/POS_V1/list_retail_sales_invoices.php','','','POS All Invoices',150,'TheStore, TheBackRoom, The Office',0,'',''),(48,'POS',1,'Engine/sales/POS_V2/POS_V1.php','','','DEVELOPMENT Point Of Sale',150,'TheStore, TheBackRoom',0,'',''),(50,'POS Store Management',1,'Engine/sales/POS_V2/list_terminal_store_invoices.php','','','POS Store Management DEVELOPMENT',250,'TheStore, TheBackRoom',0,'',''),(51,'POS Invoices',1,'Engine/sales/POS_V2/list_retail_sales_invoices.php','','','DEVELOPMENT POS All Invoices',150,'TheStore, TheBackRoom, The Office',0,'',''),(52,'Active Users',1,'Engine/users/ListActiveUsers/list_active_users.php','','','User Activity',150,'TheSystem',0,'',''),(53,'Sales Journal',0,'Engine/accounting/SalesJournal/sales_journal.php','','','Sales Journal',150,'TheOffice',0,'Customer Sales on Account',''),(54,'Accounting Setup',1,'Engine/accounting/AccountingSetup/accounting_setup.php','','','Accounting Setup',150,'TheOffice, TheSystem',0,'',''),(55,'Operating Expenses',1,'Engine/accounting/OperatingExpenses/operating_expenses.php','','','Operating Expenses',150,'TheOffice',0,'',''),(56,'Balance Sheet',1,'Engine/accounting/BalanceSheet/balance_sheet.php','','','Balance Sheet',150,'TheOffice',0,'',''),(57,'Inventory Events',1,'Engine/inventory/InventoryEvents/list_inventory_events.php','','','Inventory Events',150,'TheBackRoom,TheOffice',10,'',''),(58,'Payment Gateways',1,'Engine/system/payment_gateways/list_payment_gateways.php','','','Payment Gateways',150,'TheSystem',0,'',''),(59,'Server Configuration',1,'Engine/system/Info/server_info.php','','','Server Configuration',150,'TheSystem',100,'',''),(60,'Calendar',1,'Engine/system/office/calendar.php','','','Calendar',150,'TheOffice,TheStore,ThBackRoom,TheSystem',100,'','');
/*!40000 ALTER TABLE `pos_binders` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-31 10:04:32
