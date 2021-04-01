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
-- Table structure for table `pos_account_balances`
--

DROP TABLE IF EXISTS `pos_account_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_account_balances` (
  `pos_account_balance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL,
  `pos_chart_of_accounts_id` int(10) unsigned NOT NULL,
  `balance_date` date NOT NULL,
  `balance_entry_date` datetime NOT NULL,
  `balance_amount` decimal(20,5) NOT NULL,
  `comments` text NOT NULL,
  `binary_content` mediumblob NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(40) NOT NULL,
  `file_size` int(10) NOT NULL,
  PRIMARY KEY (`pos_account_balance_id`),
  KEY `pos_user_id` (`pos_user_id`),
  KEY `pos_account_id` (`pos_account_id`),
  KEY `pos_chart_of_accounts_id` (`pos_chart_of_accounts_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_account_category`
--

DROP TABLE IF EXISTS `pos_account_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_account_category` (
  `pos_account_id` int(10) unsigned NOT NULL,
  `pos_chart_of_account_id` int(10) unsigned NOT NULL,
  `priority` int(10) NOT NULL,
  KEY `pos_account_id` (`pos_account_id`,`pos_chart_of_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_account_type`
--

DROP TABLE IF EXISTS `pos_account_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_account_type` (
  `pos_account_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `default_chart_of_account_id` int(10) unsigned NOT NULL,
  `account_type` enum('ASSETS','LIABILITY','EQUITY','NONE') NOT NULL,
  `account_type_name` varchar(48) NOT NULL COMMENT 'credit card, inventory, expense, checking, saving, etc',
  `Priority` int(5) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `account_type_code` varchar(10) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`pos_account_type_id`),
  UNIQUE KEY `account_type` (`account_type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='credit card, inventory, expense, checking, saving, etc';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_accounts`
--

DROP TABLE IF EXISTS `pos_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_accounts` (
  `pos_account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `linked_pos_account_id` int(10) unsigned NOT NULL,
  `pos_account_type_id` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL,
  `parent_pos_chart_of_accounts_id` int(10) unsigned NOT NULL,
  `default_payment_pos_chart_of_accounts_id` int(10) unsigned NOT NULL,
  `legal_name` varchar(255) NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(225) NOT NULL DEFAULT '',
  `city` varchar(48) NOT NULL DEFAULT '',
  `state` varchar(48) NOT NULL DEFAULT '',
  `province` varchar(48) NOT NULL DEFAULT '',
  `zip` varchar(16) NOT NULL DEFAULT '',
  `country` varchar(48) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `fax` varchar(32) NOT NULL DEFAULT '',
  `account_number` varchar(255) NOT NULL,
  `company` varchar(64) NOT NULL DEFAULT '',
  `primary_contact` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `credit_limit` decimal(20,5) NOT NULL,
  `terms` varchar(64) NOT NULL DEFAULT '',
  `days` int(10) DEFAULT NULL,
  `discount` decimal(10,5) DEFAULT NULL,
  `autopay` tinyint(3) NOT NULL,
  `autopay_account_id` int(10) unsigned NOT NULL,
  `interest_rate` decimal(10,5) NOT NULL,
  `balance_init` decimal(20,5) NOT NULL,
  `verification_lock_date` datetime NOT NULL,
  `priority` int(10) NOT NULL,
  `active` tinyint(3) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_account_id`),
  KEY `linked_pos_account_id` (`linked_pos_account_id`),
  KEY `pos_account_type_id` (`pos_account_type_id`),
  KEY `parent_pos_chart_of_accounts_id` (`parent_pos_chart_of_accounts_id`),
  KEY `default_payment_pos_chart_of_accounts_id` (`default_payment_pos_chart_of_accounts_id`),
  KEY `autopay_account_id` (`autopay_account_id`),
  KEY `pos_store_id` (`pos_store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=316 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_accounts_to_chart_of_accounts`
--

DROP TABLE IF EXISTS `pos_accounts_to_chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_accounts_to_chart_of_accounts` (
  `pos_account_id` int(10) unsigned NOT NULL,
  `pos_chart_of_accounts_id` int(10) unsigned NOT NULL,
  KEY `pos_category_id` (`pos_chart_of_accounts_id`),
  KEY `pos_product_id` (`pos_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_addresses`
--

DROP TABLE IF EXISTS `pos_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_addresses` (
  `pos_address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address1` varchar(40) NOT NULL,
  `address2` varchar(40) NOT NULL,
  `city` varchar(40) NOT NULL,
  `state` varchar(40) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `country` varchar(40) NOT NULL,
  `pos_state_id` int(10) unsigned NOT NULL,
  `pos_county_id` int(10) unsigned NOT NULL,
  `comments` text NOT NULL,
  `active` tinyint(3) NOT NULL,
  PRIMARY KEY (`pos_address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_binder_collections`
--

DROP TABLE IF EXISTS `pos_binder_collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_binder_collections` (
  `pos_binder_collection_id` int(10) NOT NULL AUTO_INCREMENT,
  `binder_collection_name` varchar(40) NOT NULL,
  PRIMARY KEY (`pos_binder_collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `pos_calander_entry`
--

DROP TABLE IF EXISTS `pos_calander_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_calander_entry` (
  `pos_calander_entry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_creator_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_store_id` int(10) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `entry_type` text,
  `message` text,
  `satrt_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `reminder1_date` datetime DEFAULT NULL,
  `reminder2_date` datetime DEFAULT NULL,
  `email_reminder_date` datetime DEFAULT NULL,
  PRIMARY KEY (`pos_calander_entry_id`),
  KEY `pos_user_creator_id` (`pos_user_creator_id`),
  KEY `pos_store_id` (`pos_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_cash_drawer_log`
--

DROP TABLE IF EXISTS `pos_cash_drawer_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_cash_drawer_log` (
  `pos_cash_drawer_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_date` datetime NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `action` enum('OPEN','CLOSE') NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL,
  `balance` decimal(10,5) NOT NULL,
  PRIMARY KEY (`pos_cash_drawer_log_id`),
  KEY `pos_user_id` (`pos_user_id`,`pos_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_cash_drawers`
--

DROP TABLE IF EXISTS `pos_cash_drawers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_cash_drawers` (
  `pos_cash_drawer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_store_id` int(10) unsigned NOT NULL,
  `cash_drawer_name` varchar(30) NOT NULL,
  `cash_drawer_description` varchar(30) NOT NULL,
  `location` varchar(30) NOT NULL,
  `comments` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`pos_cash_drawer_id`),
  KEY `pos_store_id` (`pos_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_cash_receipts_journal`
--

DROP TABLE IF EXISTS `pos_cash_receipts_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_cash_receipts_journal` (
  `pos_cash_receipts_journal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `pos_from_account_id` int(10) unsigned NOT NULL,
  `pos_deposit_account_id` int(10) unsigned NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_cash_receipts_journal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_cash_registers`
--

DROP TABLE IF EXISTS `pos_cash_registers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_cash_registers` (
  `pos_cash_register_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_store_id` int(10) unsigned NOT NULL,
  `pos_user_id` int(11) NOT NULL,
  `register_name` varchar(255) NOT NULL,
  `register_type` enum('SALES REGISTER','PETTY CASH','DEPOSIT BAG','WITHDRAWAL BAG') NOT NULL,
  `cash_location` varchar(255) NOT NULL,
  `use_for_inventory_purchases` tinyint(3) NOT NULL DEFAULT '0',
  `pos_chart_of_accounts_id` int(10) unsigned NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL COMMENT 'This is for a cash payment account',
  `register_number` varchar(255) NOT NULL,
  `active` tinyint(3) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_cash_register_id`),
  KEY `pos_user_id` (`pos_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_categories`
--

DROP TABLE IF EXISTS `pos_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_categories` (
  `pos_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `priority` smallint(5) unsigned NOT NULL DEFAULT '5',
  `default_product_priority` int(10) NOT NULL,
  `pos_sales_tax_category_id` int(10) unsigned NOT NULL,
  `is_visible` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `list_subcats` enum('Yes','No') NOT NULL DEFAULT 'No',
  `url_hash` varchar(32) NOT NULL DEFAULT '',
  `url_default` varchar(128) NOT NULL DEFAULT '',
  `url_custom` varchar(128) NOT NULL DEFAULT '',
  `key_name` varchar(255) NOT NULL DEFAULT '',
  `category_header` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` text NOT NULL,
  `meta_title` text NOT NULL,
  `meta_description` text NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `description_bottom` text,
  `category_path` text,
  `active` int(3) NOT NULL,
  PRIMARY KEY (`pos_category_id`),
  KEY `parent` (`parent`),
  KEY `level` (`level`),
  KEY `priority` (`priority`),
  KEY `url_hash` (`url_hash`),
  KEY `pos_tax_category_id` (`pos_sales_tax_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=235 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_chart_of_account_types`
--

DROP TABLE IF EXISTS `pos_chart_of_account_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_chart_of_account_types` (
  `pos_chart_of_account_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_type_name` varchar(255) NOT NULL,
  `Options` text NOT NULL,
  `priority` int(10) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_chart_of_account_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_chart_of_accounts`
--

DROP TABLE IF EXISTS `pos_chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_chart_of_accounts` (
  `pos_chart_of_accounts_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_chart_of_accounts_id` int(10) unsigned NOT NULL,
  `account_number` int(10) unsigned NOT NULL COMMENT '1000 assets 2000 liabilities 3000 equity 4000 revenue 5000 cost of goods sold 6000 expense',
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('CURRENT ASSETS','LONG-TERM ASSETS','OTHER ASSETS','CURRENT LIABILITIES','LONG-TERM LIABILITIES','EQUITY','REVENUE','COST OF GOODS SOLD','EXPENSE') NOT NULL,
  `account_sub_type` enum('Not Specified','Other','Cash','Receivables','Investments','Buildings','Land','Equipment','Vehicles','Inventory','Payables','Notes','Loans','Mortgages') NOT NULL COMMENT 'This a bit more specific way to search through asset, liability, etc accounts',
  `pos_chart_of_account_type_id` int(10) unsigned NOT NULL,
  `pos_chart_of_accounts_required_id` int(10) unsigned NOT NULL,
  `active` tinyint(3) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_chart_of_accounts_id`),
  UNIQUE KEY `account_number` (`account_number`,`account_name`),
  KEY `pos_chart_of_account_type_id` (`pos_chart_of_account_type_id`),
  KEY `pos_chart_of_accounts_required_id` (`pos_chart_of_accounts_required_id`),
  KEY `parent_chart_of_accounts_id` (`parent_chart_of_accounts_id`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_chart_of_accounts_required`
--

DROP TABLE IF EXISTS `pos_chart_of_accounts_required`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_chart_of_accounts_required` (
  `pos_chart_of_accounts_required_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_chart_of_account_type_id` int(10) unsigned NOT NULL,
  `required_account_name` varchar(255) NOT NULL,
  `required_account_code` varchar(10) NOT NULL,
  `priority` int(10) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_chart_of_accounts_required_id`),
  KEY `pos_chart_of_account_type_id` (`pos_chart_of_account_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='These Accounts are required in the chart of accounts for cer';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_contact_addresses`
--

DROP TABLE IF EXISTS `pos_contact_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_contact_addresses` (
  `pos_contact_id` int(10) unsigned NOT NULL,
  `pos_contact_address_id` int(10) unsigned NOT NULL,
  KEY `pos_contact_id` (`pos_contact_id`,`pos_contact_address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_contact_emails`
--

DROP TABLE IF EXISTS `pos_contact_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_contact_emails` (
  `pos_contact_id` int(10) unsigned NOT NULL,
  `pos_email_id` int(10) unsigned NOT NULL,
  KEY `pos_contact_id` (`pos_contact_id`,`pos_email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_contacts`
--

DROP TABLE IF EXISTS `pos_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_contacts` (
  `pos_contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `last_name` int(11) NOT NULL,
  `first_name` int(11) NOT NULL,
  PRIMARY KEY (`pos_contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_counties`
--

DROP TABLE IF EXISTS `pos_counties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_counties` (
  `pos_county_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_state_id` int(10) unsigned NOT NULL,
  `county_name` varchar(40) NOT NULL,
  `nick_name` varchar(30) NOT NULL,
  `pos_tax_jurisdiction_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pos_county_id`),
  KEY `pos_tax_jurisdiction_id` (`pos_tax_jurisdiction_id`),
  KEY `pos_state_id` (`pos_state_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_currencies`
--

DROP TABLE IF EXISTS `pos_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_currencies` (
  `pos_currency_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_default` enum('Yes','No') NOT NULL DEFAULT 'No',
  `title` varchar(100) NOT NULL DEFAULT '',
  `code` varchar(100) NOT NULL DEFAULT '',
  `symbol_left` varchar(100) NOT NULL DEFAULT '',
  `symbol_right` varchar(100) NOT NULL DEFAULT '',
  `decimal_places` tinyint(3) NOT NULL DEFAULT '2',
  `exchange_rate` decimal(20,10) NOT NULL DEFAULT '1.0000000000',
  PRIMARY KEY (`pos_currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_custom_binders`
--

DROP TABLE IF EXISTS `pos_custom_binders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_custom_binders` (
  `pos_custom_binder_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `binder_name` varchar(40) NOT NULL,
  PRIMARY KEY (`pos_custom_binder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_customer_addresses`
--

DROP TABLE IF EXISTS `pos_customer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_customer_addresses` (
  `pos_customer_id` int(10) unsigned NOT NULL,
  `pos_address_id` int(10) unsigned NOT NULL,
  KEY `pos_customer_id` (`pos_customer_id`,`pos_address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_customer_emails`
--

DROP TABLE IF EXISTS `pos_customer_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_customer_emails` (
  `pos_customer_id` int(10) unsigned NOT NULL,
  `pos_email_address_id` int(10) unsigned NOT NULL,
  KEY `pos_customer_id` (`pos_customer_id`,`pos_email_address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_customer_payment_batch`
--

DROP TABLE IF EXISTS `pos_customer_payment_batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_customer_payment_batch` (
  `pos_customer_payment_batch_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `batch_name` varchar(40) NOT NULL,
  PRIMARY KEY (`pos_customer_payment_batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_customer_payment_methods`
--

DROP TABLE IF EXISTS `pos_customer_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_customer_payment_methods` (
  `pos_customer_payment_method_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(255) NOT NULL,
  `payment_group` enum('CREDIT_CARD','CHECK','CASH','STORE_CREDIT','OTHER') NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`pos_customer_payment_method_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_customer_payments`
--

DROP TABLE IF EXISTS `pos_customer_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_customer_payments` (
  `pos_customer_payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_payment_gateway_id` int(10) unsigned NOT NULL,
  `transaction_status` varchar(40) NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL,
  `deposit_account_id` int(10) unsigned NOT NULL,
  `pos_customer_payment_method_id` int(10) unsigned NOT NULL,
  `pos_customer_payment_batch_id` int(10) unsigned NOT NULL,
  `card_number` varchar(25) NOT NULL,
  `pos_store_credit_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `payment_amount` decimal(20,5) NOT NULL,
  `reference_number` varchar(40) NOT NULL,
  `transaction_id` varchar(40) NOT NULL,
  `authorization_code` varchar(40) NOT NULL,
  `batch_id` varchar(40) NOT NULL,
  `payment_status` varchar(20) NOT NULL,
  `summary` varchar(25) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_customer_payment_id`),
  KEY `pos_account_id` (`pos_account_id`),
  KEY `pos_customer_payment_method_id` (`pos_customer_payment_method_id`),
  KEY `pos_store_credit_id` (`pos_store_credit_id`),
  KEY `deposit_account_id` (`deposit_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38750 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_customers`
--

DROP TABLE IF EXISTS `pos_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_customers` (
  `pos_customer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_id` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(40) NOT NULL,
  `default_address_id` int(10) NOT NULL,
  `email1` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `company` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pos_state_id` int(10) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `country` varchar(40) NOT NULL,
  `pos_country_id` int(10) NOT NULL,
  `comments` mediumtext NOT NULL,
  `status` varchar(255) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`pos_customer_id`),
  KEY `first_name` (`first_name`,`last_name`),
  KEY `pos_user_id` (`pos_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24556 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_discount_product_lookup`
--

DROP TABLE IF EXISTS `pos_discount_product_lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_discount_product_lookup` (
  `pos_discount_id` int(10) unsigned NOT NULL,
  `pos_product_id` int(10) unsigned NOT NULL,
  `pos_manufacturer_id` int(10) unsigned NOT NULL,
  `pos_category_id` int(10) unsigned NOT NULL,
  KEY `pos_discount_id` (`pos_discount_id`,`pos_product_id`,`pos_manufacturer_id`,`pos_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_discounts`
--

DROP TABLE IF EXISTS `pos_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_discounts` (
  `pos_discount_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `discount_name` varchar(255) NOT NULL,
  `discount_code` varchar(20) NOT NULL,
  `discount_amount` decimal(20,5) NOT NULL,
  `percent_or_dollars` enum('$','%') NOT NULL,
  `max_discount` decimal(20,5) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `admin_only` tinyint(1) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_discount_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_documents`
--

DROP TABLE IF EXISTS `pos_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_documents` (
  `pos_document_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id_for_entry_lock` int(10) unsigned NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_date` date NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `document_text` longtext NOT NULL,
  `auto_save_document_text` longtext NOT NULL,
  `comments` text NOT NULL,
  `document_overview` text NOT NULL,
  PRIMARY KEY (`pos_document_id`),
  KEY `user_id_for_entry_lock` (`user_id_for_entry_lock`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_documents_backup`
--

DROP TABLE IF EXISTS `pos_documents_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_documents_backup` (
  `pos_document_backup_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_document_id` int(10) unsigned NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_date` date NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `document_text` longtext NOT NULL,
  `comments` text NOT NULL,
  `document_overview` text NOT NULL,
  PRIMARY KEY (`pos_document_backup_id`),
  KEY `pos_document_id` (`pos_document_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_email_addresses`
--

DROP TABLE IF EXISTS `pos_email_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_email_addresses` (
  `pos_email_address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(40) NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`pos_email_address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_employees`
--

DROP TABLE IF EXISTS `pos_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_employees` (
  `pos_employee_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `default_store_id` int(10) NOT NULL COMMENT 'employee default store',
  `first_name` varchar(24) NOT NULL DEFAULT '',
  `last_name` varchar(36) NOT NULL DEFAULT '',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `session_id` varchar(48) NOT NULL DEFAULT '',
  `session_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `block_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `login_erros` int(3) NOT NULL,
  `locked` int(3) NOT NULL,
  `rights` varchar(255) NOT NULL,
  `notifications` varchar(255) NOT NULL,
  `last_access` datetime NOT NULL,
  `last_update` datetime NOT NULL,
  `default_start_page` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `database_access` enum('READ','WRITE') NOT NULL DEFAULT 'WRITE',
  `activation_code` char(32) NOT NULL DEFAULT '',
  `login` varchar(24) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `timeout_minutes` int(10) unsigned NOT NULL,
  `company` varchar(64) NOT NULL DEFAULT '',
  `address1` varchar(225) NOT NULL DEFAULT '',
  `address2` varchar(225) NOT NULL DEFAULT '',
  `city` varchar(48) NOT NULL DEFAULT '',
  `state` varchar(48) NOT NULL DEFAULT '',
  `province` varchar(48) NOT NULL DEFAULT '',
  `zip` varchar(16) NOT NULL DEFAULT '',
  `country` varchar(48) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `pay_rate` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `witholding` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `ss` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos_employee_id`),
  KEY `default_store_id` (`default_store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_expense_categories`
--

DROP TABLE IF EXISTS `pos_expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_expense_categories` (
  `pos_expense_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `caption` varchar(64) NOT NULL DEFAULT '',
  `active` enum('Yes','No','Block') NOT NULL DEFAULT 'Yes',
  `priority` int(3) NOT NULL,
  `comments` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos_expense_category_id`),
  UNIQUE KEY `name` (`name`,`caption`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_expense_payment_method`
--

DROP TABLE IF EXISTS `pos_expense_payment_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_expense_payment_method` (
  `pos_expense_payment_method_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `caption` varchar(64) NOT NULL DEFAULT '',
  `active` enum('Yes','No','Block') NOT NULL DEFAULT 'Yes',
  `priority` smallint(5) unsigned NOT NULL,
  `comments` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos_expense_payment_method_id`),
  UNIQUE KEY `name` (`name`,`caption`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_expenses`
--

DROP TABLE IF EXISTS `pos_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_expenses` (
  `pos_expense_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_expense_category_id` int(10) unsigned NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL,
  `pos_expense_supplier_id` int(10) unsigned NOT NULL,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expense_date` date NOT NULL DEFAULT '0000-00-00',
  `description` varchar(64) NOT NULL DEFAULT '',
  `supplier` varchar(64) NOT NULL DEFAULT '',
  `purchaser` varchar(64) NOT NULL DEFAULT '',
  `pos_expense_payment_method_id` int(10) unsigned NOT NULL,
  `verified_on_statement` enum('Yes','No') NOT NULL DEFAULT 'No',
  `cost` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `deduction_amount` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `comments` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos_expense_id`)
) ENGINE=InnoDB AUTO_INCREMENT=269 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_general_journal`
--

DROP TABLE IF EXISTS `pos_general_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_general_journal` (
  `pos_general_journal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_employee_id` int(10) unsigned NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL,
  `entry_type` enum('Statement','Receipt','Invoice','Transfer') NOT NULL DEFAULT 'Receipt',
  `validated` tinyint(3) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_due_date` date NOT NULL,
  `invoice_status` enum('OPEN','CLOSED') NOT NULL,
  `payment_status` enum('PAID','UNPAID') NOT NULL,
  `invoice_number` varchar(48) NOT NULL,
  `invoice_type` enum('Regular','Credit Memo') NOT NULL DEFAULT 'Regular',
  `payment_date` date NOT NULL,
  `entry_date` datetime NOT NULL,
  `entry_amount` decimal(20,5) NOT NULL,
  `use_tax` decimal(20,5) NOT NULL,
  `minimum_amount_due` decimal(20,5) NOT NULL,
  `discount_applied` decimal(20,5) NOT NULL,
  `discount_lost` decimal(20,5) NOT NULL,
  `pos_chart_of_accounts_id` int(10) unsigned NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL,
  `supplier` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `payments_applied` decimal(20,5) NOT NULL,
  `comments` text NOT NULL,
  `binary_content` mediumblob NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(40) NOT NULL,
  `file_size` int(10) NOT NULL,
  PRIMARY KEY (`pos_general_journal_id`),
  KEY `pos_employee_id` (`pos_employee_id`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_account_id` (`pos_account_id`),
  KEY `pos_chart_of_accounts_id` (`pos_chart_of_accounts_id`),
  KEY `pos_user_id` (`pos_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13117 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_general_ledger`
--

DROP TABLE IF EXISTS `pos_general_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_general_ledger` (
  `pos_general_ledger_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_general_ledger_post_id` int(11) unsigned NOT NULL COMMENT 'Should be gapless',
  `pos_general_ledger_transaction_id` int(10) unsigned NOT NULL,
  `pos_user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `pos_chart_of_accounts_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_account_id` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL,
  `debit` decimal(20,5) NOT NULL,
  `credit` decimal(20,5) NOT NULL,
  `balance` decimal(20,5) NOT NULL,
  PRIMARY KEY (`pos_general_ledger_id`)
) ENGINE=InnoDB AUTO_INCREMENT=587 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_gift_cards`
--

DROP TABLE IF EXISTS `pos_gift_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_gift_cards` (
  `pos_gift_card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_id` int(10) unsigned NOT NULL,
  `gift_card_number` varchar(24) NOT NULL,
  `date_sold` datetime NOT NULL,
  `pos_customer_invoice_id` int(10) unsigned NOT NULL,
  `original_amount` decimal(20,5) NOT NULL,
  `current_value` decimal(20,5) NOT NULL,
  `inactivity_fees` decimal(20,5) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_gift_card_id`),
  UNIQUE KEY `gift_card_number` (`gift_card_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_inventory_complete_dates`
--

DROP TABLE IF EXISTS `pos_inventory_complete_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_inventory_complete_dates` (
  `pos_inventory_complete_date_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_id` int(10) unsigned NOT NULL,
  `user_id_for_entry_lock` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL DEFAULT '0',
  `inventory_start_date` datetime NOT NULL,
  `inventory_end_date` datetime NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_inventory_complete_date_id`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_user_id` (`pos_user_id`),
  KEY `pos_user_id_for_entry_lock` (`user_id_for_entry_lock`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_inventory_event`
--

DROP TABLE IF EXISTS `pos_inventory_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_inventory_event` (
  `pos_inventory_event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_id` int(10) unsigned NOT NULL,
  `user_id_for_entry_lock` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_location_id` int(10) unsigned NOT NULL,
  `inventory_date` datetime NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_inventory_event_id`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_location_id` (`pos_location_id`),
  KEY `pos_user_id` (`pos_user_id`),
  KEY `pos_user_id_for_entry_lock` (`user_id_for_entry_lock`)
) ENGINE=InnoDB AUTO_INCREMENT=2271 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_inventory_event_contents`
--

DROP TABLE IF EXISTS `pos_inventory_event_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_inventory_event_contents` (
  `pos_inventory_event_content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_inventory_event_id` int(10) NOT NULL,
  `barcode` varchar(40) NOT NULL,
  `pos_product_sub_id` int(10) unsigned NOT NULL DEFAULT '0',
  `price_level` int(5) NOT NULL,
  `inventory_type` enum('Available','Committed','Distressed','Discarded') NOT NULL COMMENT 'what is the type being added?',
  `quantity` decimal(20,5) NOT NULL,
  `inventory_tracking_number` varchar(255) NOT NULL,
  `value` decimal(20,5) NOT NULL,
  `storage_cost` decimal(20,5) NOT NULL,
  `purchasing_cost` decimal(20,5) NOT NULL,
  `expiration_date` datetime NOT NULL,
  `lot_number` varchar(255) NOT NULL,
  `action` enum('TRANSFER','PHYSICAL_COUNT','INVENTORY_ADJUSTMENT','CLEAR') NOT NULL,
  `comments` text NOT NULL,
  `unique_tag` varchar(20) NOT NULL,
  PRIMARY KEY (`pos_inventory_event_content_id`),
  KEY `pos_products_sub_id` (`pos_product_sub_id`),
  KEY `pos_inventory_event_id` (`pos_inventory_event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=133713 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_inventory_log`
--

DROP TABLE IF EXISTS `pos_inventory_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_inventory_log` (
  `pos_inventory_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_chart_of_accounts_id` int(10) unsigned NOT NULL,
  `pos_product_sub_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL DEFAULT '0',
  `inventory_type` enum('Available','Committed','Distressed','Discarded') NOT NULL COMMENT 'what is the type being added?',
  `quantity` decimal(20,5) NOT NULL,
  `pos_location_id` int(10) unsigned NOT NULL,
  `inventory_tracking_number` varchar(255) NOT NULL,
  `value` decimal(20,5) NOT NULL,
  `inventory_date` datetime NOT NULL,
  `storage_cost` decimal(20,5) NOT NULL,
  `purchasing_cost` decimal(20,5) NOT NULL,
  `expiration_date` datetime NOT NULL,
  `lot_number` varchar(255) NOT NULL,
  `action` enum('TRANSFER','PHYSICAL_COUNT','INVENTORY_ADJUSTMENT','CLEAR') NOT NULL,
  `comments` text NOT NULL,
  `unique_tag` varchar(20) NOT NULL,
  PRIMARY KEY (`pos_inventory_log_id`),
  KEY `pos_products_sub_id` (`pos_product_sub_id`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_chart_of_accounts_id` (`pos_chart_of_accounts_id`),
  KEY `pos_location_id` (`pos_location_id`),
  KEY `pos_user_id` (`pos_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3854 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_invoice_to_credit_memo`
--

DROP TABLE IF EXISTS `pos_invoice_to_credit_memo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_invoice_to_credit_memo` (
  `pos_purchases_journal_invoice_id` int(10) unsigned NOT NULL,
  `pos_purchases_journal_credit_memo_id` int(10) unsigned NOT NULL,
  `applied_amount` decimal(20,5) NOT NULL,
  KEY `pos_category_id` (`pos_purchases_journal_credit_memo_id`),
  KEY `pos_product_id` (`pos_purchases_journal_invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_invoice_to_payment`
--

DROP TABLE IF EXISTS `pos_invoice_to_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_invoice_to_payment` (
  `pos_journal_id` int(10) unsigned NOT NULL,
  `pos_payments_journal_id` int(10) unsigned NOT NULL,
  `source_journal` enum('PURCHASES JOURNAL','GENERAL JOURNAL','SALES JOURNAL') NOT NULL,
  `applied_amount` decimal(20,5) NOT NULL,
  `comments` text NOT NULL,
  KEY `pos_category_id` (`pos_payments_journal_id`),
  KEY `pos_product_id` (`pos_journal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_journal_to_coa_link`
--

DROP TABLE IF EXISTS `pos_journal_to_coa_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_journal_to_coa_link` (
  `pos_journal_to_coa_link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Journal` enum('Sales','Payroll','Purchases','Purchase Orders','Inventory') NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `comments` varchar(255) NOT NULL,
  `pos_chart_of_accounts_id` int(11) NOT NULL,
  PRIMARY KEY (`pos_journal_to_coa_link_id`),
  KEY `pos_chart_of_accounts_id` (`pos_chart_of_accounts_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_location_groups`
--

DROP TABLE IF EXISTS `pos_location_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_location_groups` (
  `pos_location_group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_group_name` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_location_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_locations`
--

DROP TABLE IF EXISTS `pos_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_locations` (
  `pos_location_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_parent_location_id` int(10) NOT NULL,
  `location_name` varchar(40) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL,
  `pos_location_group_id` int(10) unsigned NOT NULL,
  `priority` int(10) NOT NULL COMMENT 'pull inventory from higher numbers first',
  `active` tinyint(1) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_location_id`),
  UNIQUE KEY `pos_parent_location_id` (`pos_parent_location_id`,`location_name`,`pos_store_id`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `po_location_group_id` (`pos_location_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_manufacturer_accounts`
--

DROP TABLE IF EXISTS `pos_manufacturer_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_manufacturer_accounts` (
  `pos_manufacturer_id` int(10) unsigned NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL,
  `default_account` tinyint(1) NOT NULL,
  KEY `pos_manufacturer_id` (`pos_manufacturer_id`,`pos_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_manufacturer_brand_attributes`
--

DROP TABLE IF EXISTS `pos_manufacturer_brand_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_manufacturer_brand_attributes` (
  `pos_manufacturer_brand_size_id` int(10) unsigned NOT NULL,
  `pos_product_attribute_id` int(10) unsigned NOT NULL,
  KEY `pos_manufacturer_brand_size_id` (`pos_manufacturer_brand_size_id`,`pos_product_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_manufacturer_brand_sizes`
--

DROP TABLE IF EXISTS `pos_manufacturer_brand_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_manufacturer_brand_sizes` (
  `pos_manufacturer_brand_size_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_manufacturer_brand_id` int(10) unsigned NOT NULL,
  `pos_category_id` int(10) unsigned NOT NULL,
  `pos_product_attribute_id` int(10) unsigned NOT NULL,
  `case_qty` tinyint(1) NOT NULL,
  `cup` tinyint(3) NOT NULL,
  `cup_required` tinyint(1) NOT NULL,
  `inseam` tinyint(3) NOT NULL,
  `width` tinyint(3) NOT NULL,
  `size_modifier` varchar(20) NOT NULL COMMENT 'cup,inseam,width - when specified a column, check, etc will be added',
  `sizes` text NOT NULL,
  `active` tinyint(3) NOT NULL,
  `comments` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos_manufacturer_brand_size_id`),
  KEY `pos_manufacturer_brand_id` (`pos_manufacturer_brand_id`),
  KEY `pos_category_id` (`pos_category_id`),
  KEY `pos_product_attribute_id` (`pos_product_attribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=694 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_manufacturer_brands`
--

DROP TABLE IF EXISTS `pos_manufacturer_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_manufacturer_brands` (
  `pos_manufacturer_brand_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_manufacturer_id` int(10) unsigned NOT NULL,
  `brand_name` varchar(64) NOT NULL DEFAULT '',
  `brand_code` varchar(48) NOT NULL DEFAULT '',
  `pos_chart_of_accounts_id` int(10) unsigned NOT NULL COMMENT 'This is the asset account the inventory goes into',
  `sales_rep_email` varchar(40) NOT NULL,
  `sales_rep_name` varchar(40) NOT NULL,
  `sales_rep_phone` varchar(40) NOT NULL,
  `active` int(1) NOT NULL,
  `comments` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos_manufacturer_brand_id`),
  KEY `pos_manufacturer_id` (`pos_manufacturer_id`),
  KEY `pos_chart_of_accounts_id` (`pos_chart_of_accounts_id`)
) ENGINE=InnoDB AUTO_INCREMENT=255 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_manufacturer_upc`
--

DROP TABLE IF EXISTS `pos_manufacturer_upc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_manufacturer_upc` (
  `pos_manufacturer_upc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_manufacturer_id` int(10) unsigned NOT NULL,
  `upc_code` varchar(64) NOT NULL DEFAULT '',
  `date_added` date NOT NULL DEFAULT '0000-00-00',
  `style_number` varchar(64) NOT NULL DEFAULT '',
  `style_description` varchar(255) NOT NULL DEFAULT '',
  `color_code` varchar(64) NOT NULL DEFAULT '',
  `color_description` varchar(64) NOT NULL DEFAULT '',
  `size` varchar(64) NOT NULL DEFAULT '',
  `msrp` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `cost` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `comments` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos_manufacturer_upc_id`),
  UNIQUE KEY `unique_upc` (`pos_manufacturer_id`,`upc_code`),
  KEY `pos_manufacturer_id` (`pos_manufacturer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=373752 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_manufacturers`
--

DROP TABLE IF EXISTS `pos_manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_manufacturers` (
  `pos_manufacturer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_account_id` int(10) unsigned NOT NULL,
  `company` varchar(64) NOT NULL DEFAULT '',
  `sales_rep` varchar(64) NOT NULL DEFAULT '',
  `manufacturer_code` varchar(3) NOT NULL COMMENT 'Manufacturer 3 digit code',
  `address1` varchar(225) NOT NULL DEFAULT '',
  `address2` varchar(225) NOT NULL DEFAULT '',
  `city` varchar(48) NOT NULL DEFAULT '',
  `state` varchar(48) NOT NULL DEFAULT '',
  `province` varchar(48) NOT NULL DEFAULT '',
  `zip` varchar(16) NOT NULL DEFAULT '',
  `country` varchar(48) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `fax` varchar(32) NOT NULL DEFAULT '',
  `terms` varchar(64) NOT NULL DEFAULT '',
  `active` int(1) NOT NULL COMMENT '0 not active, 1 is active',
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_manufacturer_id`),
  UNIQUE KEY `company` (`company`),
  UNIQUE KEY `company_2` (`company`),
  KEY `pos_account_id` (`pos_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=236 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_messages`
--

DROP TABLE IF EXISTS `pos_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_messages` (
  `pos_message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `to_pos_user_id` int(10) unsigned NOT NULL,
  `from_pos_user_id` int(10) unsigned NOT NULL,
  `message` text CHARACTER SET utf8 NOT NULL,
  `action_url` text CHARACTER SET utf8 NOT NULL,
  `response` text CHARACTER SET utf8 NOT NULL,
  `message_creation_date` datetime NOT NULL,
  `message_complete_date` datetime NOT NULL,
  PRIMARY KEY (`pos_message_id`),
  KEY `to_pos_user_id` (`to_pos_user_id`),
  KEY `from_pos_user_id` (`from_pos_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3794 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_payment_gateways`
--

DROP TABLE IF EXISTS `pos_payment_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_payment_gateways` (
  `pos_payment_gateway_id` int(10) NOT NULL AUTO_INCREMENT,
  `pos_store_id` int(10) unsigned NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL,
  `login_id` varchar(255) NOT NULL COMMENT 'encypted',
  `transaction_key` varchar(255) NOT NULL COMMENT 'encypted',
  `gateway_provider` enum('Physical Terminal','Authorize.net','Orbital','Square') NOT NULL,
  `model_name` varchar(255) NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL COMMENT 'encrypted',
  `password` varchar(255) NOT NULL COMMENT 'encrypted',
  `line` enum('online','offline') NOT NULL,
  `active` tinyint(1) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_payment_gateway_id`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_account_id` (`pos_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_payments_journal`
--

DROP TABLE IF EXISTS `pos_payments_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_payments_journal` (
  `pos_payments_journal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_journal` enum('GENERAL JOURNAL','PURCHASES JOURNAL','SALES JOURNAL') NOT NULL,
  `reference_id` varchar(64) NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL,
  `pos_employee_id` int(10) unsigned NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL,
  `pos_payee_account_id` int(10) unsigned NOT NULL,
  `pos_manufacturer_id` int(10) unsigned NOT NULL COMMENT 'used for the purchases journal',
  `payment_date` date NOT NULL,
  `post_date` datetime NOT NULL,
  `payment_entry_date` datetime NOT NULL,
  `payment_amount` decimal(20,5) NOT NULL,
  `payment_status` enum('COMPLETE','PENDING','SCHEDULED') NOT NULL,
  `applied_status` enum('APPLIED','PARTIAL','UNAPPLIED','OVER APPLIED') NOT NULL,
  `validated` tinyint(3) NOT NULL,
  `post_validated` tinyint(3) NOT NULL,
  `comments` text NOT NULL,
  `binary_content` mediumblob NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(40) NOT NULL,
  `file_size` int(10) NOT NULL,
  PRIMARY KEY (`pos_payments_journal_id`),
  KEY `pos_employee_id` (`pos_employee_id`),
  KEY `pos_payee_account_id` (`pos_payee_account_id`),
  KEY `pos_manufacturer_id` (`pos_manufacturer_id`),
  KEY `pos_user_id` (`pos_user_id`),
  KEY `pos_account_id` (`pos_account_id`),
  KEY `pos_store_id` (`pos_store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19394 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_printers`
--

DROP TABLE IF EXISTS `pos_printers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_printers` (
  `pos_printer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_store_id` int(10) unsigned NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL,
  `printer_name` varchar(60) NOT NULL,
  `printer_description` varchar(255) NOT NULL,
  `media` enum('Memo 5x7 Paper','Letter Paper','Avery 5167','Checks - Quicken Format') NOT NULL,
  `location` varchar(60) NOT NULL,
  `comments` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`pos_printer_id`),
  UNIQUE KEY `printer_name` (`printer_name`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_account_id` (`pos_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_product_attributes`
--

DROP TABLE IF EXISTS `pos_product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_product_attributes` (
  `pos_product_attribute_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_name` varchar(40) NOT NULL,
  `priority` int(10) NOT NULL,
  `active` int(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_product_attribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_product_colors`
--

DROP TABLE IF EXISTS `pos_product_colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_product_colors` (
  `pos_product_color_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `color_name` varchar(40) NOT NULL DEFAULT '' COMMENT 'Color Description',
  `color_code` varchar(20) NOT NULL,
  `priority` int(10) NOT NULL COMMENT 'used for sorting things like XS S M L XL 1L 2L etc..',
  `active` tinyint(3) NOT NULL COMMENT '0 or 1',
  `fashion_color` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0 or 1 fashion color',
  `unique_web_product` tinyint(3) NOT NULL COMMENT '0 or 1 - 1 will create a unique web product',
  `web_product_id` varchar(255) NOT NULL,
  `web_product_url` varchar(255) NOT NULL,
  `retail_price` decimal(10,5) NOT NULL COMMENT 'these override the product price',
  `cost` decimal(10,5) NOT NULL COMMENT 'these override the product price',
  `sale_price` decimal(10,5) NOT NULL COMMENT 'these override the product price',
  `employee_price` decimal(10,5) NOT NULL COMMENT 'these override the product price',
  `description` text NOT NULL,
  PRIMARY KEY (`pos_product_color_id`),
  KEY `pid` (`pos_product_id`),
  KEY `color_code` (`color_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11169 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_product_image_lookup`
--

DROP TABLE IF EXISTS `pos_product_image_lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_product_image_lookup` (
  `pos_product_image_id` int(10) unsigned NOT NULL,
  `pos_product_id` int(10) unsigned NOT NULL,
  `pos_product_sub_id` int(10) unsigned NOT NULL,
  `image_order` int(5) NOT NULL COMMENT '1 is main, 2 secondary, etc',
  KEY `pos_product_image_id` (`pos_product_image_id`,`pos_product_id`),
  KEY `pos_product_sub_id` (`pos_product_sub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_product_images`
--

DROP TABLE IF EXISTS `pos_product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_product_images` (
  `pos_product_image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image` blob NOT NULL,
  `image_type` varchar(255) NOT NULL COMMENT 'original, catalog, product, secondary',
  `original_image_name` varchar(255) NOT NULL COMMENT 'Image name',
  `view` enum('FRONT','BACK','SIDE','TOP','BOTTOM') NOT NULL,
  `crop_coordinates` varchar(255) NOT NULL COMMENT 'Coordinates to crop the image in xy pairs (eg ul,ur,ll,lr : 0,0:0,100:100,0:100,100)',
  `web_url` varchar(255) NOT NULL,
  `pos_path` varchar(255) NOT NULL,
  `active` tinyint(3) NOT NULL COMMENT '0 or 1',
  `comments` text NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`pos_product_image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1230 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_product_options`
--

DROP TABLE IF EXISTS `pos_product_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_product_options` (
  `pos_product_option_id` int(10) NOT NULL AUTO_INCREMENT,
  `pos_product_attribute_id` int(10) unsigned NOT NULL,
  `pos_product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `option_name` varchar(40) NOT NULL DEFAULT '' COMMENT 'like Blue, S , USED',
  `option_code` varchar(20) NOT NULL,
  `sort_index` int(10) NOT NULL COMMENT 'used for sorting things like XS S M L XL 1L 2L etc..',
  `price_adjustment` decimal(20,5) NOT NULL,
  `unique_web_product` tinyint(1) NOT NULL,
  `extra_tags` text NOT NULL COMMENT 'separate by a delimeter',
  `active` tinyint(1) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_product_option_id`),
  UNIQUE KEY `pos_product_attribute_id` (`pos_product_attribute_id`,`pos_product_id`,`option_code`)
) ENGINE=InnoDB AUTO_INCREMENT=55449 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_product_recommendations`
--

DROP TABLE IF EXISTS `pos_product_recommendations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_product_recommendations` (
  `pos_product_recommend_id` int(10) unsigned NOT NULL,
  `pos_product_id` int(10) unsigned NOT NULL,
  `pos_product_color_recommend_id` int(10) unsigned NOT NULL,
  `pos_product_color_id` int(10) unsigned NOT NULL,
  KEY `pos_product_id` (`pos_product_id`),
  KEY `pos_product_color_id` (`pos_product_color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_product_secondary_categories`
--

DROP TABLE IF EXISTS `pos_product_secondary_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_product_secondary_categories` (
  `pos_product_color_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_product_option_id` int(10) unsigned NOT NULL,
  `pos_product_id` int(10) unsigned NOT NULL,
  `pos_category_id` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `pos_category_id` (`pos_category_id`,`pos_product_color_id`),
  KEY `pos_product_color_id` (`pos_product_color_id`),
  KEY `pos_product_id` (`pos_product_id`),
  KEY `pos_product_option_id` (`pos_product_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_product_sub_id_options`
--

DROP TABLE IF EXISTS `pos_product_sub_id_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_product_sub_id_options` (
  `pos_product_sub_id` int(10) unsigned NOT NULL,
  `pos_product_option_id` int(10) unsigned NOT NULL,
  KEY `pos_product_sub_id` (`pos_product_sub_id`,`pos_product_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_product_sub_sale_price`
--

DROP TABLE IF EXISTS `pos_product_sub_sale_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_product_sub_sale_price` (
  `pos_product_sub_id` int(10) NOT NULL,
  `sale_barcode` varchar(40) NOT NULL,
  `price_level` int(4) NOT NULL,
  `price` decimal(20,5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `as_is` tinyint(3) NOT NULL,
  `clearance` tinyint(3) NOT NULL,
  `comments` text NOT NULL,
  UNIQUE KEY `pos_product_sub_id` (`pos_product_sub_id`,`price_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_products`
--

DROP TABLE IF EXISTS `pos_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_products` (
  `pos_product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_manufacturer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_manufacturer_brand_id` int(10) unsigned NOT NULL,
  `pos_sales_tax_category_id` int(10) unsigned NOT NULL,
  `style_number` varchar(64) NOT NULL DEFAULT '',
  `product_id` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `overview` text,
  `description` text,
  `active` tinyint(1) NOT NULL,
  `unit_of_measure` enum('EA','LBS','CASE') NOT NULL DEFAULT 'EA',
  `case_quantity` decimal(20,5) NOT NULL,
  `case_price` decimal(20,5) NOT NULL,
  `cost` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `retail_price` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `bulk_retail_quantity` decimal(20,5) NOT NULL,
  `bulk_retail_price` decimal(20,5) NOT NULL,
  `sale_price` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `employee_price` decimal(20,5) NOT NULL,
  `shipping_price` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `is_taxable` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `tax_class_id` int(10) unsigned NOT NULL DEFAULT '0',
  `tax_rate` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `priority` int(11) NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `weight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `pos_manufacturer_brand_size_id` int(10) unsigned NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_product_id`),
  UNIQUE KEY `pos_manufacturer_brand_id_2` (`pos_manufacturer_brand_id`,`style_number`),
  KEY `pos_manufacturer_id` (`pos_manufacturer_id`),
  KEY `pos_category_id` (`pos_category_id`),
  KEY `pos_manufacturer_brand_id` (`pos_manufacturer_brand_id`),
  KEY `pos_sales_tax_category_id` (`pos_sales_tax_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7831 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_products_attributes`
--

DROP TABLE IF EXISTS `pos_products_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_products_attributes` (
  `pos_product_attribute_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attribute_name` varchar(20) NOT NULL DEFAULT '' COMMENT 'like Color, Size',
  `caption` varchar(255) NOT NULL DEFAULT '' COMMENT 'caption to appear on web',
  `attribute_code` varchar(48) NOT NULL,
  `options` text,
  `priority` int(10) NOT NULL COMMENT 'used for sorting things like XS S M L XL 1L 2L etc..',
  PRIMARY KEY (`pos_product_attribute_id`),
  KEY `pid` (`pos_product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7815 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_products_sub_id`
--

DROP TABLE IF EXISTS `pos_products_sub_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_products_sub_id` (
  `pos_product_sub_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_product_color_id` int(10) unsigned NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `inventory_warning` int(10) unsigned NOT NULL DEFAULT '0',
  `product_sku` varchar(64) NOT NULL DEFAULT '',
  `product_upc` varchar(64) NOT NULL DEFAULT '',
  `product_subid_name` varchar(64) NOT NULL DEFAULT '',
  `barcode` varchar(40) NOT NULL,
  `attributes_hash` varchar(255) NOT NULL DEFAULT '',
  `attributes_list` text,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_product_sub_id`),
  UNIQUE KEY `product_subid_name` (`product_subid_name`),
  KEY `pos_product_id` (`pos_product_id`),
  KEY `pos_product_color_id` (`pos_product_color_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57186 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_promotion_buy`
--

DROP TABLE IF EXISTS `pos_promotion_buy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_promotion_buy` (
  `pos_promotion_id` int(10) unsigned NOT NULL,
  `buy` decimal(20,5) NOT NULL,
  `get` decimal(20,5) NOT NULL,
  `discount` decimal(20,5) NOT NULL,
  `d_or_p` enum('$','%') NOT NULL,
  KEY `pos_promotion_id` (`pos_promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_promotion_lookup`
--

DROP TABLE IF EXISTS `pos_promotion_lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_promotion_lookup` (
  `pos_promotion_id` int(10) unsigned NOT NULL,
  `pos_product_id` int(10) unsigned NOT NULL,
  `pos_category_id` int(10) NOT NULL,
  `include_subcategories` tinyint(3) NOT NULL,
  `pos_manufacturer_brand_id` int(10) unsigned NOT NULL,
  `include_product` enum('INCLUDE','EXCLUDE') NOT NULL,
  `include_brand` enum('INCLUDE','EXCLUDE') NOT NULL,
  `include_category` enum('INCLUDE','EXCLUDE') NOT NULL,
  KEY `pos_promotion_id` (`pos_promotion_id`,`pos_product_id`,`pos_category_id`),
  KEY `pos_manufacturer_brand_id` (`pos_manufacturer_brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_promotions`
--

DROP TABLE IF EXISTS `pos_promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_promotions` (
  `pos_promotion_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `promotion_name` varchar(255) NOT NULL,
  `promotion_code` varchar(20) NOT NULL,
  `promotion_type` enum('Pre Tax','Post Tax') NOT NULL,
  `start_date` datetime NOT NULL,
  `expiration_date` datetime NOT NULL,
  `promotion_amount` decimal(20,5) NOT NULL,
  `item_or_total` enum('ITEM','TOTAL') NOT NULL,
  `blanket` tinyint(3) NOT NULL,
  `percent_or_dollars` enum('$','%') NOT NULL,
  `buy_x` decimal(20,5) NOT NULL,
  `get_y` decimal(20,5) NOT NULL,
  `expired_value` decimal(20,5) NOT NULL,
  `qualifying_amount` decimal(20,5) NOT NULL,
  `check_if_can_be_applied_to_sale_items` tinyint(1) NOT NULL COMMENT 'if checked this can be used on sale items',
  `check_if_can_be_applied_to_clearance_items` tinyint(3) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_promotion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchase_order_categories`
--

DROP TABLE IF EXISTS `pos_purchase_order_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchase_order_categories` (
  `pos_purchase_order_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`pos_purchase_order_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='categories for the purchase orders';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchase_order_contents`
--

DROP TABLE IF EXISTS `pos_purchase_order_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchase_order_contents` (
  `pos_purchase_order_content_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pos_purchase_order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `poc_row_number` int(10) unsigned NOT NULL COMMENT 'This is the actual row in the purchase order content form',
  `size_row` varchar(12) NOT NULL COMMENT 'this is the row in the header used for size information',
  `size_column` varchar(3) NOT NULL COMMENT 'size column on the purchase order',
  `style_number` varchar(64) NOT NULL,
  `style_number_source` varchar(10) NOT NULL COMMENT 'mfg, pos, custom',
  `color_code` varchar(64) NOT NULL,
  `color_description` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `pos_category_id` int(10) NOT NULL,
  `cup` varchar(10) NOT NULL,
  `inseam` varchar(10) NOT NULL,
  `attributes` text NOT NULL,
  `size` varchar(255) NOT NULL,
  `cost` decimal(20,5) NOT NULL,
  `retail` decimal(20,5) NOT NULL,
  `discount` decimal(20,5) NOT NULL,
  `discount_quantity` int(4) NOT NULL,
  `pos_product_sub_id` int(10) unsigned NOT NULL,
  `quantity_ordered` int(10) NOT NULL DEFAULT '0' COMMENT 'Order Quantity',
  `adjustment_quantity` decimal(20,5) NOT NULL,
  `quantity_received` int(10) NOT NULL,
  `quantity_missing` int(10) NOT NULL,
  `quantity_canceled` int(10) NOT NULL,
  `quantity_added` int(10) NOT NULL,
  `quantity_damaged` int(10) NOT NULL,
  `quantity_returning` int(10) NOT NULL,
  `returning_comments` text NOT NULL,
  `received_date_qty` text NOT NULL,
  `comments` text,
  `receive_comments` text NOT NULL,
  PRIMARY KEY (`pos_purchase_order_content_id`),
  KEY `pos_purchase_order_id` (`pos_purchase_order_id`),
  KEY `pos_product_sub_id` (`pos_product_sub_id`),
  KEY `pos_category_id` (`pos_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1395925 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchase_order_receive_contents`
--

DROP TABLE IF EXISTS `pos_purchase_order_receive_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchase_order_receive_contents` (
  `pos_purchase_order_receive_content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_purchase_order_receive_event_id` int(10) unsigned NOT NULL,
  `pos_purchase_order_content_id` int(10) unsigned NOT NULL,
  `received_quantity` int(10) NOT NULL,
  `receive_comments` text NOT NULL,
  PRIMARY KEY (`pos_purchase_order_receive_content_id`),
  KEY `pos_purchase_order_receive_id` (`pos_purchase_order_receive_event_id`),
  KEY `pos_product_sub_id` (`pos_purchase_order_content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101822 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchase_order_receive_event`
--

DROP TABLE IF EXISTS `pos_purchase_order_receive_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchase_order_receive_event` (
  `pos_purchase_order_receive_event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_purchase_order_id` int(10) unsigned NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_terminal_id` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL,
  `receive_date` datetime NOT NULL,
  `pick_ticket` varchar(40) NOT NULL,
  `comments` text NOT NULL,
  `wrong_items_comments` longtext NOT NULL,
  PRIMARY KEY (`pos_purchase_order_receive_event_id`),
  KEY `pos_user_id` (`pos_user_id`),
  KEY `pos_terminal_id` (`pos_terminal_id`),
  KEY `pos_store_id` (`pos_store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4880 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchase_orders`
--

DROP TABLE IF EXISTS `pos_purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchase_orders` (
  `pos_purchase_order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id_for_entry_lock` int(10) unsigned NOT NULL,
  `pos_manufacturer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_manufacturer_brand_id` int(10) unsigned NOT NULL,
  `pos_category_id` int(10) unsigned NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_store_id` int(10) unsigned NOT NULL DEFAULT '0',
  `purchase_order_number` varchar(255) NOT NULL DEFAULT '',
  `manufacturer_purchase_order_number` varchar(255) NOT NULL DEFAULT '',
  `purchase_order_type` enum('ORDER','RETURN') NOT NULL,
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `placed_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `delivery_date` date NOT NULL DEFAULT '0000-00-00',
  `cancel_date` date NOT NULL DEFAULT '0000-00-00',
  `received_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pos_receive_store_id` int(10) unsigned NOT NULL,
  `pos_receive_user_id` int(10) unsigned NOT NULL,
  `employee_po_creater_name` varchar(64) NOT NULL DEFAULT '',
  `purchase_order_status` enum('INIT','DRAFT','PREPARED','OPEN','CLOSED','DELETED') NOT NULL DEFAULT 'INIT',
  `ordered_status` enum('NOT SUBMITTED','SUBMITTED','EMAILED','MANUALLY SUBMITTED','CANCELED','REVISED') NOT NULL DEFAULT 'NOT SUBMITTED' COMMENT 'This is how the order has been sent',
  `received_status` varchar(255) NOT NULL COMMENT 'COMPLETE, INCOMPLETE, EXTRA ITEMS, DAMAGED ITEMS',
  `invoice_status` enum('INCOMPLETE','COMPLETE','OVER APPLIED','NEED CREDIT MEMO','NEED TO RETURN GOODS') NOT NULL,
  `comments` text,
  `po_title` varchar(255) NOT NULL COMMENT 'purchase order title',
  `stored_size_chart` text NOT NULL COMMENT 'Stored size chart - in JSON format',
  `wrong_items_qty` int(10) NOT NULL DEFAULT '0',
  `wrong_items_comments` text NOT NULL,
  `log` text NOT NULL,
  `ra_required` int(3) unsigned NOT NULL,
  `ra_number` varchar(255) NOT NULL,
  `credit_memo_required` int(1) NOT NULL,
  `credit_memo_invoice_number` text NOT NULL,
  PRIMARY KEY (`pos_purchase_order_id`),
  KEY `pos_manufacturer_id` (`pos_manufacturer_id`),
  KEY `status` (`purchase_order_status`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_employee_id` (`pos_user_id`),
  KEY `pos_brand_id` (`pos_manufacturer_brand_id`),
  KEY `pos_receive_user_id` (`pos_receive_user_id`),
  KEY `pos_category_id` (`pos_category_id`),
  KEY `pos_receive_store_id` (`pos_receive_store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5072 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchase_return_contents`
--

DROP TABLE IF EXISTS `pos_purchase_return_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchase_return_contents` (
  `pos_purchase_return_content_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pos_purchase_return_id` int(10) unsigned NOT NULL DEFAULT '0',
  `poc_row_number` int(10) unsigned NOT NULL COMMENT 'This is the actual row in the purchase order content form',
  `size_row` varchar(12) NOT NULL COMMENT 'this is the row in the header used for size information',
  `size_column` varchar(3) NOT NULL COMMENT 'size column on the purchase order',
  `style_number` varchar(64) NOT NULL,
  `style_number_source` varchar(10) NOT NULL COMMENT 'mfg, pos, custom',
  `color_code` varchar(64) NOT NULL,
  `color_description` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `pos_category_id` int(10) NOT NULL,
  `cup` varchar(10) NOT NULL,
  `inseam` varchar(10) NOT NULL,
  `size` varchar(255) NOT NULL,
  `cost` decimal(20,5) NOT NULL,
  `retail` decimal(20,5) NOT NULL,
  `pos_product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_product_sub_id` int(10) unsigned NOT NULL,
  `quantity_returned` int(10) NOT NULL DEFAULT '0' COMMENT 'Order Quantity',
  `comments` text,
  PRIMARY KEY (`pos_purchase_return_content_id`),
  KEY `pos_purchase_order_id` (`pos_purchase_return_id`),
  KEY `pos_product_id` (`pos_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchase_returns`
--

DROP TABLE IF EXISTS `pos_purchase_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchase_returns` (
  `pos_purchase_return_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_purchase_order_id` int(10) unsigned NOT NULL COMMENT 'reference to original PO',
  `pos_manufacturer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_manufacturer_brand_id` int(10) unsigned NOT NULL,
  `pos_category_id` int(10) unsigned NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pos_store_id` int(10) unsigned NOT NULL DEFAULT '0',
  `purchase_return_number` varchar(255) NOT NULL DEFAULT '',
  `return_authorization_number` varchar(255) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `placed_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ship_date` date NOT NULL DEFAULT '0000-00-00',
  `cancel_date` date NOT NULL DEFAULT '0000-00-00',
  `pos_ship_store_id` int(10) unsigned NOT NULL,
  `pos_ship_employee_id` int(10) unsigned NOT NULL,
  `employee_pr_creater_name` varchar(64) NOT NULL DEFAULT '',
  `purchase_return_status` enum('INIT','DRAFT','PREPARED','OPEN','CLOSED','DELETED') NOT NULL DEFAULT 'INIT',
  `submit_status` enum('NOT SUBMITTED','SUBMITTED','EMAILED','MANUALLY SUBMITTED','CANCELED') NOT NULL DEFAULT 'NOT SUBMITTED' COMMENT 'This is how the order has been sent',
  `ship_status` enum('NOT SHIPPED','SHIPPED','RECEIVED') NOT NULL,
  `shipping_carrier_id` varchar(255) NOT NULL DEFAULT '',
  `shipping_method_id` varchar(100) NOT NULL DEFAULT '',
  `shipping_tracking_number` varchar(250) NOT NULL DEFAULT '',
  `comments` text,
  `pr_title` varchar(255) NOT NULL COMMENT 'purchase order title',
  `stored_size_chart` text NOT NULL COMMENT 'Stored size chart - in JSON format',
  PRIMARY KEY (`pos_purchase_return_id`),
  KEY `pos_manufacturer_id` (`pos_manufacturer_id`),
  KEY `status` (`purchase_return_status`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_employee_id` (`pos_user_id`),
  KEY `pos_brand_id` (`pos_manufacturer_brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchases_credit_memo_to_po`
--

DROP TABLE IF EXISTS `pos_purchases_credit_memo_to_po`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchases_credit_memo_to_po` (
  `pos_purchases_journal_id` int(10) unsigned NOT NULL,
  `pos_purchase_order_id` int(10) unsigned NOT NULL,
  `applied_amount` decimal(20,5) NOT NULL,
  `comments` text NOT NULL,
  UNIQUE KEY `pos_purchases_journal_id` (`pos_purchases_journal_id`,`pos_purchase_order_id`),
  KEY `pos_category_id` (`pos_purchase_order_id`),
  KEY `pos_product_id` (`pos_purchases_journal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchases_invoice_to_po`
--

DROP TABLE IF EXISTS `pos_purchases_invoice_to_po`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchases_invoice_to_po` (
  `pos_purchases_journal_id` int(10) unsigned NOT NULL,
  `pos_purchase_order_id` int(10) unsigned NOT NULL,
  `applied_amount` decimal(20,5) NOT NULL,
  `comments` text NOT NULL,
  UNIQUE KEY `pos_purchases_journal_id` (`pos_purchases_journal_id`,`pos_purchase_order_id`),
  KEY `pos_category_id` (`pos_purchase_order_id`),
  KEY `pos_product_id` (`pos_purchases_journal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchases_invoice_to_pr`
--

DROP TABLE IF EXISTS `pos_purchases_invoice_to_pr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchases_invoice_to_pr` (
  `pos_purchases_journal_id` int(10) unsigned NOT NULL,
  `pos_purchase_order_id` int(10) unsigned NOT NULL,
  KEY `pos_category_id` (`pos_purchase_order_id`),
  KEY `pos_product_id` (`pos_purchases_journal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_purchases_journal`
--

DROP TABLE IF EXISTS `pos_purchases_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_purchases_journal` (
  `pos_purchases_journal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_manufacturer_id` int(10) unsigned NOT NULL,
  `invoice_number` varchar(48) NOT NULL,
  `invoice_status` enum('OPEN','CLOSED') NOT NULL,
  `invoice_type` varchar(48) NOT NULL COMMENT '''Regular'', ''Credit Memo''',
  `invoice_date` date NOT NULL,
  `invoice_due_date` date NOT NULL,
  `credit_memo_used_date` datetime NOT NULL,
  `invoice_received_date` date NOT NULL,
  `invoice_amount` decimal(10,5) NOT NULL,
  `show_discount` decimal(20,5) NOT NULL,
  `discount_applied` decimal(20,5) NOT NULL,
  `discount_available` decimal(20,5) NOT NULL,
  `discount_lost` decimal(20,5) NOT NULL,
  `discount_coa_account_id` int(10) NOT NULL,
  `shipping_amount` decimal(10,5) NOT NULL,
  `fee_amount` decimal(20,5) NOT NULL,
  `invoice_entry_date` datetime NOT NULL,
  `validated` tinyint(1) NOT NULL,
  `payment_status` enum('OVERPAID','PAID','UNPAID','USED','UNUSED') NOT NULL,
  `payments_applied` decimal(20,5) NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_account_id` int(10) unsigned NOT NULL COMMENT 'links to pos_account_id',
  `pos_asset_coa_account_id` int(11) NOT NULL COMMENT 'this is the asset account number - like 1215 finished merchandise',
  `binary_content` mediumblob NOT NULL COMMENT 'attach a file to this',
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(30) NOT NULL,
  `file_size` int(10) NOT NULL,
  `comments` longtext NOT NULL,
  PRIMARY KEY (`pos_purchases_journal_id`),
  UNIQUE KEY `pos_manufacturer_id` (`pos_manufacturer_id`,`invoice_number`),
  KEY `pos_user_id` (`pos_user_id`),
  KEY `pos_account_id` (`pos_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4272 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_room_arrangements`
--

DROP TABLE IF EXISTS `pos_room_arrangements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_room_arrangements` (
  `pos_user_id` int(10) unsigned NOT NULL,
  `room_name` varchar(40) NOT NULL,
  `room_priority` int(5) NOT NULL,
  `type` enum('BINDER','DIVIDER') NOT NULL,
  `pos_binder_id` int(10) unsigned NOT NULL,
  `source` varchar(20) NOT NULL,
  `priority` int(5) NOT NULL,
  KEY `pos_user_id` (`pos_user_id`,`pos_binder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_rooms`
--

DROP TABLE IF EXISTS `pos_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_rooms` (
  `pos_room_id` int(10) NOT NULL AUTO_INCREMENT,
  `room_name` int(11) NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `priority` int(5) NOT NULL,
  `binders` text NOT NULL,
  PRIMARY KEY (`pos_room_id`),
  KEY `pos_user_id` (`pos_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sales_invoice`
--

DROP TABLE IF EXISTS `pos_sales_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales_invoice` (
  `pos_sales_invoice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_return_invoice_id` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL,
  `pos_terminal_id` int(11) NOT NULL,
  `pos_chart_of_account_id` int(10) NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_sales_associate_id` int(10) NOT NULL COMMENT 'user id for sales associate if different than the entry user',
  `pos_employee_id` int(10) unsigned NOT NULL,
  `user_id_for_entry_lock` int(10) unsigned NOT NULL,
  `pos_customer_id` int(10) unsigned NOT NULL,
  `pos_address_id` int(10) NOT NULL,
  `invoice_number` varchar(11) NOT NULL,
  `invoice_date` datetime NOT NULL,
  `shipping_amount` decimal(20,5) NOT NULL,
  `tax_calculation_method` enum('minimum','average','maximum') NOT NULL,
  `invoice_status` enum('INIT','DRAFT','OPEN','CLOSED','EXITED') NOT NULL,
  `payment_status` enum('PAID','UNPAID') NOT NULL,
  `follow_up` tinyint(3) NOT NULL,
  `special_order` tinyint(3) NOT NULL,
  `comments` mediumtext NOT NULL,
  PRIMARY KEY (`pos_sales_invoice_id`),
  KEY `pos_customer_id` (`pos_customer_id`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_user_id` (`pos_user_id`),
  KEY `pos_employee_id` (`pos_employee_id`),
  KEY `pos_return_invoice_id` (`pos_return_invoice_id`),
  KEY `pos_address_id` (`pos_address_id`),
  KEY `pos_sales_associate_id` (`pos_sales_associate_id`),
  KEY `pos_chart_of_account_id` (`pos_chart_of_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56402 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sales_invoice_contents`
--

DROP TABLE IF EXISTS `pos_sales_invoice_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales_invoice_contents` (
  `pos_sales_invoice_content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_sales_invoice_id` int(10) unsigned NOT NULL,
  `row_number` int(10) NOT NULL,
  `pos_return_content_id` int(10) unsigned NOT NULL,
  `content_type` enum('PRODUCT','SERVICE','CREDIT_CARD','SHIPPING') NOT NULL DEFAULT 'PRODUCT',
  `pos_store_credit_id` int(10) unsigned NOT NULL,
  `pos_product_sub_id` int(10) unsigned NOT NULL,
  `barcode` varchar(64) NOT NULL,
  `checkout_description` varchar(255) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `size` varchar(30) NOT NULL,
  `brand_name` varchar(64) NOT NULL,
  `style_number` varchar(20) NOT NULL,
  `color_code` varchar(20) NOT NULL,
  `retail_price` decimal(20,5) NOT NULL,
  `sale_price` decimal(20,5) NOT NULL,
  `pos_sales_tax_category_id` int(10) unsigned NOT NULL,
  `pos_local_tax_jurisdiction_id` int(10) unsigned NOT NULL,
  `pos_local_regular_sales_tax_rate_id` int(10) NOT NULL,
  `pos_local_exemption_sales_tax_rate_id` int(10) NOT NULL,
  `local_regular_tax_rate` decimal(20,5) NOT NULL,
  `local_exemption_tax_rate` decimal(20,5) NOT NULL,
  `local_exemption_value` decimal(20,5) NOT NULL,
  `pos_state_tax_jurisdiction_id` int(10) unsigned NOT NULL,
  `pos_state_regular_sales_tax_rate_id` int(10) unsigned NOT NULL,
  `pos_state_exemption_sales_tax_rate_id` int(10) unsigned NOT NULL,
  `state_regular_tax_rate` decimal(20,5) NOT NULL,
  `state_exemption_tax_rate` decimal(20,5) NOT NULL,
  `state_exemption_value` decimal(20,5) NOT NULL,
  `tax_rate` decimal(20,5) NOT NULL,
  `tax_total` decimal(20,5) NOT NULL,
  `discount` decimal(20,5) NOT NULL,
  `pos_discount_id` int(10) unsigned NOT NULL,
  `discount_type` enum('PERCENT','DOLLAR') NOT NULL,
  `applied_instore_discount` decimal(20,5) NOT NULL,
  `quantity` int(10) NOT NULL,
  `extension` decimal(20,5) NOT NULL COMMENT 'is the extension even reasonable?',
  `pos_special_order_id` int(10) NOT NULL,
  `pos_alteration_id` int(10) unsigned NOT NULL,
  `special_order` tinyint(1) NOT NULL,
  `paid` tinyint(1) NOT NULL,
  `ship` tinyint(1) NOT NULL,
  `pos_promotion_id` text NOT NULL,
  `wish_list` tinyint(3) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_sales_invoice_content_id`),
  KEY `pos_product_sub_id` (`pos_product_sub_id`),
  KEY `pos_alteration_id` (`pos_alteration_id`),
  KEY `pos_sales_invoice_id` (`pos_sales_invoice_id`),
  KEY `pos_local_tax_jurisdiction_id` (`pos_local_tax_jurisdiction_id`,`pos_state_tax_jurisdiction_id`),
  KEY `pos_sales_tax_category_id` (`pos_sales_tax_category_id`),
  KEY `pos_state_regular_sales_tax_rate_id` (`pos_state_regular_sales_tax_rate_id`,`pos_state_exemption_sales_tax_rate_id`),
  KEY `pos_discount_code` (`pos_discount_id`),
  KEY `pos_store_credit_id` (`pos_store_credit_id`),
  KEY `pos_return_content_id` (`pos_return_content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=108461 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sales_invoice_promotions`
--

DROP TABLE IF EXISTS `pos_sales_invoice_promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales_invoice_promotions` (
  `pos_sales_invoice_id` int(10) unsigned NOT NULL,
  `pos_promotion_id` int(10) unsigned NOT NULL,
  `applied_amount` decimal(20,5) NOT NULL,
  `row_number` int(5) NOT NULL,
  KEY `pos_sales_invoice_id` (`pos_sales_invoice_id`,`pos_promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sales_invoice_to_payment`
--

DROP TABLE IF EXISTS `pos_sales_invoice_to_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales_invoice_to_payment` (
  `pos_sales_invoice_id` int(10) unsigned NOT NULL,
  `pos_customer_payment_id` int(10) unsigned NOT NULL,
  `applied_amount` decimal(20,5) NOT NULL,
  `applied_comments` text NOT NULL,
  KEY `pos_sales_invoice_id` (`pos_sales_invoice_id`,`pos_customer_payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sales_journal`
--

DROP TABLE IF EXISTS `pos_sales_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales_journal` (
  `pos_customer_payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_account_id` int(10) unsigned NOT NULL,
  `deposit_account_id` int(10) unsigned NOT NULL,
  `pos_customer_payment_method_id` int(10) unsigned NOT NULL,
  `pos_customer_payment_batch_id` int(10) unsigned NOT NULL,
  `pos_store_credit_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `payment_amount` decimal(20,5) NOT NULL,
  `reference_number` varchar(40) NOT NULL,
  `payment_status` varchar(20) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_customer_payment_id`),
  KEY `pos_account_id` (`pos_account_id`),
  KEY `pos_customer_payment_method_id` (`pos_customer_payment_method_id`),
  KEY `pos_store_credit_id` (`pos_store_credit_id`),
  KEY `deposit_account_id` (`deposit_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sales_manual_journal`
--

DROP TABLE IF EXISTS `pos_sales_manual_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales_manual_journal` (
  `pos_sales_manual_journal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `pos_customer_payment_id` int(10) unsigned NOT NULL,
  `amount` decimal(20,5) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_sales_manual_journal_id`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `pos_customer_payment_id` (`pos_customer_payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sales_tax_categories`
--

DROP TABLE IF EXISTS `pos_sales_tax_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales_tax_categories` (
  `pos_sales_tax_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax_category_name` varchar(80) NOT NULL,
  `tax_exempt` int(1) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`pos_sales_tax_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sales_tax_exemption_manual_journal`
--

DROP TABLE IF EXISTS `pos_sales_tax_exemption_manual_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales_tax_exemption_manual_journal` (
  `pos_sales_tax_exemption_manual_journal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_id` int(10) unsigned NOT NULL,
  `pos_store_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `amount` decimal(20,5) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_sales_tax_exemption_manual_journal_id`),
  KEY `pos_store_id` (`pos_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sales_tax_rates`
--

DROP TABLE IF EXISTS `pos_sales_tax_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sales_tax_rates` (
  `pos_sales_tax_rate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_sales_tax_category_id` int(10) unsigned NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `pos_tax_jurisdiction_id` int(10) unsigned NOT NULL,
  `sales_tax_name` varchar(255) NOT NULL,
  `tax_rate` decimal(20,5) NOT NULL,
  `tax_type` enum('Regular','Exemption') NOT NULL,
  `exemption_value` decimal(20,5) NOT NULL,
  PRIMARY KEY (`pos_sales_tax_rate_id`),
  KEY `pos_sales_tax_category_id` (`pos_sales_tax_category_id`),
  KEY `pos_county_id` (`pos_tax_jurisdiction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_services`
--

DROP TABLE IF EXISTS `pos_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_services` (
  `pos_service_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_sales_tax_category_id` int(10) unsigned NOT NULL,
  `barcode` varchar(64) NOT NULL DEFAULT '',
  `service_name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `active` tinyint(1) NOT NULL,
  `unit_of_measure` enum('EA','HOUR') NOT NULL,
  `retail_price` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `cost` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_service_id`),
  KEY `pos_sales_tax_category_id` (`pos_sales_tax_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_settings`
--

DROP TABLE IF EXISTS `pos_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_settings` (
  `visible` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `input_type` enum('TEXT','SELECT','CHECKBOX') NOT NULL,
  `group_name` varchar(100) NOT NULL DEFAULT '',
  `priority` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` text,
  `value_text` text NOT NULL,
  `options` text NOT NULL,
  `default_value` varchar(100) NOT NULL DEFAULT '',
  `validation` varchar(100) NOT NULL DEFAULT '',
  `caption` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_shipping_options`
--

DROP TABLE IF EXISTS `pos_shipping_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_shipping_options` (
  `pos_shipping_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_sales_tax_category_id` int(10) unsigned NOT NULL,
  `barcode` varchar(20) NOT NULL,
  `carrier_name` varchar(100) NOT NULL DEFAULT '',
  `method_name` varchar(100) NOT NULL DEFAULT '',
  `priority` tinyint(4) NOT NULL DEFAULT '0',
  `weight_min` decimal(10,2) NOT NULL DEFAULT '0.00',
  `weight_max` decimal(10,2) NOT NULL DEFAULT '1000.00',
  `fee` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `fee_type` enum('amount','percent') NOT NULL DEFAULT 'amount',
  `active` tinyint(3) NOT NULL,
  `comments` text,
  PRIMARY KEY (`pos_shipping_option_id`),
  KEY `pos_sales_tax_category_id` (`pos_sales_tax_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_states`
--

DROP TABLE IF EXISTS `pos_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_states` (
  `pos_state_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `default_state_tax` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `name` varchar(255) NOT NULL DEFAULT '',
  `short_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos_state_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_store_credit`
--

DROP TABLE IF EXISTS `pos_store_credit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_store_credit` (
  `pos_store_credit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_id` int(10) unsigned NOT NULL,
  `card_number` varchar(24) NOT NULL,
  `card_type` enum('Gift Card','Store Credit','Deposit') NOT NULL,
  `date_created` datetime NOT NULL,
  `date_issued` datetime NOT NULL,
  `pos_customer_id` int(10) unsigned NOT NULL,
  `original_amount` decimal(20,5) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_store_credit_id`),
  UNIQUE KEY `gift_card_number` (`card_number`),
  KEY `pos_customer_payment_id` (`pos_customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2081 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_store_credit_card_numbers`
--

DROP TABLE IF EXISTS `pos_store_credit_card_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_store_credit_card_numbers` (
  `pos_store_credit_card_number_id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `card_number` varchar(20) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_printed` datetime DEFAULT NULL,
  PRIMARY KEY (`pos_store_credit_card_number_id`),
  UNIQUE KEY `card_number` (`card_number`)
) ENGINE=InnoDB AUTO_INCREMENT=4481 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_stores`
--

DROP TABLE IF EXISTS `pos_stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_stores` (
  `pos_store_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_state_id` int(10) unsigned NOT NULL COMMENT 'for state tax lookup',
  `pos_tax_jurisdiction_id` int(10) unsigned NOT NULL,
  `active` int(1) NOT NULL,
  `company` varchar(255) NOT NULL DEFAULT '',
  `store_name` varchar(255) NOT NULL DEFAULT '',
  `website` varchar(30) NOT NULL,
  `shipping_address1` varchar(225) NOT NULL DEFAULT '',
  `shipping_address2` varchar(225) NOT NULL DEFAULT '',
  `shipping_city` varchar(255) NOT NULL DEFAULT '',
  `shipping_state` varchar(255) NOT NULL DEFAULT '',
  `shipping_province` varchar(255) NOT NULL DEFAULT '',
  `shipping_zip` varchar(16) NOT NULL DEFAULT '',
  `shipping_country` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `fax` varchar(32) NOT NULL DEFAULT '',
  `billing_address1` varchar(255) NOT NULL,
  `billing_address2` varchar(255) NOT NULL,
  `billing_city` varchar(255) NOT NULL,
  `billing_state` varchar(255) NOT NULL,
  `billing_province` varchar(255) NOT NULL,
  `billing_zip` varchar(255) NOT NULL,
  `billing_country` varchar(255) NOT NULL,
  `comments` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos_store_id`),
  KEY `pos_state_id` (`pos_state_id`),
  KEY `pos_tax_jurisdiction_id` (`pos_tax_jurisdiction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_system`
--

DROP TABLE IF EXISTS `pos_system`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_system` (
  `admin_user_name` varchar(40) NOT NULL,
  `admin_password` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_tax_jurisdictions`
--

DROP TABLE IF EXISTS `pos_tax_jurisdictions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_tax_jurisdictions` (
  `pos_tax_jurisdiction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_state_id` int(10) NOT NULL,
  `jurisdiction_name` varchar(255) NOT NULL,
  `jurisdiction_code` varchar(30) NOT NULL,
  `default_tax_rate` decimal(20,5) NOT NULL,
  `local_or_state` enum('Local','State') NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`pos_tax_jurisdiction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_terminal_log`
--

DROP TABLE IF EXISTS `pos_terminal_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_terminal_log` (
  `pos_terminal_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_terminal_id` int(10) unsigned NOT NULL,
  `log_date` datetime NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  `action` enum('OPEN','CLOSE') NOT NULL,
  `cash_account_id` int(10) unsigned NOT NULL,
  `check_account_id` int(10) unsigned NOT NULL,
  `default_cc_processor_account_id` int(11) NOT NULL,
  PRIMARY KEY (`pos_terminal_log_id`),
  KEY `pos_terminal_id` (`pos_terminal_id`),
  KEY `pos_user_id` (`pos_user_id`,`cash_account_id`,`check_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_terminal_printers`
--

DROP TABLE IF EXISTS `pos_terminal_printers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_terminal_printers` (
  `pos_terminal_id` int(10) unsigned NOT NULL,
  `pos_printer_id` int(10) unsigned NOT NULL,
  KEY `pos_terminal_id` (`pos_terminal_id`,`pos_printer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_terminals`
--

DROP TABLE IF EXISTS `pos_terminals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_terminals` (
  `pos_terminal_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_store_id` int(10) unsigned NOT NULL,
  `status` enum('OPEN','CLOSED','LOCKED') NOT NULL,
  `mac_address` varchar(30) NOT NULL,
  `ip_address` varchar(30) NOT NULL,
  `cash_drawer` varchar(30) NOT NULL,
  `pos_printer_id` int(10) unsigned NOT NULL,
  `default_cash_account_id` int(10) unsigned NOT NULL,
  `default_check_account_id` int(10) unsigned NOT NULL,
  `default_gift_card_account_id` int(10) unsigned NOT NULL,
  `default_store_credit_account_id` int(10) unsigned NOT NULL,
  `default_prepay_account_id` int(10) unsigned NOT NULL,
  `default_non_payment_account_id` int(10) unsigned NOT NULL,
  `pos_payment_gateway_id` int(10) NOT NULL,
  `pos_refund_checking_account_id` int(10) NOT NULL,
  `max_cash_refund` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `location` varchar(30) NOT NULL,
  `comments` text NOT NULL,
  `terminal_name` varchar(16) NOT NULL,
  `terminal_description` text NOT NULL,
  `cookie_name` varchar(30) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`pos_terminal_id`),
  UNIQUE KEY `terminal_name` (`terminal_name`),
  KEY `pos_printer_id` (`pos_printer_id`),
  KEY `pos_store_id` (`pos_store_id`),
  KEY `default_cash_account_id` (`default_cash_account_id`),
  KEY `default_check_account_id` (`default_check_account_id`),
  KEY `default_gift_card_account_id` (`default_gift_card_account_id`,`default_store_credit_account_id`,`default_prepay_account_id`),
  KEY `default_other_account_id` (`default_non_payment_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_user_binder_access`
--

DROP TABLE IF EXISTS `pos_user_binder_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_user_binder_access` (
  `pos_user_id` int(10) NOT NULL,
  `pos_binder_id` int(10) NOT NULL,
  `pos_custom_binder_id` int(10) NOT NULL,
  `binder_type` enum('SYSTEM','CUSTOM') NOT NULL,
  `access` enum('WRITE','READ') NOT NULL,
  KEY `pos_user_id` (`pos_user_id`),
  KEY `pos_binder_id` (`pos_binder_id`),
  KEY `pos_custom_binder_id` (`pos_custom_binder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_user_groups`
--

DROP TABLE IF EXISTS `pos_user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_user_groups` (
  `pos_user_group_id` int(10) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(40) NOT NULL,
  `pos_allow_edit_invoice_details` tinyint(3) NOT NULL,
  `pos_allow_voids` tinyint(1) NOT NULL,
  `pos_allow_refunds` tinyint(1) NOT NULL,
  `pos_max_discount_percent` decimal(20,5) NOT NULL,
  `pos_edit_closed_contents` tinyint(1) NOT NULL,
  `pos_edit_closed_payments` tinyint(1) NOT NULL,
  `pos_edit_closed_customer` tinyint(1) NOT NULL,
  `pos_allow_other_payment` tinyint(3) NOT NULL,
  `pos_allow_cc_return` tinyint(3) NOT NULL,
  `pos_allow_advanced_return` tinyint(3) NOT NULL,
  `pos_open_close_terminal` tinyint(3) NOT NULL,
  `po_max_open_past_cancel` int(10) NOT NULL,
  `po_max_received_not_invoiced` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`pos_user_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf16;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_user_log`
--

DROP TABLE IF EXISTS `pos_user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_user_log` (
  `pos_user_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_id` int(10) unsigned NOT NULL,
  `time` datetime NOT NULL,
  `url` text NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `browser` varchar(40) NOT NULL,
  PRIMARY KEY (`pos_user_log_id`),
  KEY `pos_user_id` (`pos_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1584046 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_users`
--

DROP TABLE IF EXISTS `pos_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_users` (
  `pos_user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_user_group_id` int(10) unsigned NOT NULL,
  `pos_employee_id` int(11) NOT NULL,
  `default_store_id` int(10) NOT NULL COMMENT 'employee default store',
  `first_name` varchar(24) NOT NULL DEFAULT '',
  `last_name` varchar(36) NOT NULL DEFAULT '',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `session_id` varchar(48) NOT NULL DEFAULT '',
  `session_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `block_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `login_errors` int(3) NOT NULL,
  `locked` int(3) NOT NULL,
  `rights` varchar(255) NOT NULL,
  `notifications` varchar(255) NOT NULL,
  `last_access` datetime NOT NULL,
  `last_update` datetime NOT NULL,
  `default_start_page` varchar(255) NOT NULL,
  `default_view_date_range_days` int(10) NOT NULL DEFAULT '90',
  `role` varchar(255) NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `database_access` enum('READ','WRITE') NOT NULL DEFAULT 'WRITE',
  `activation_code` char(32) NOT NULL DEFAULT '',
  `login` varchar(24) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `timeout_minutes` int(10) unsigned NOT NULL,
  `max_connections` tinyint(3) NOT NULL,
  `ip_address_restrictions` varchar(255) NOT NULL COMMENT 'restricted to ip addresses, separated by commas',
  `relogin_on_ip_address_change` tinyint(3) NOT NULL,
  `relogin_on_browser_change` tinyint(3) NOT NULL,
  `email` varchar(64) NOT NULL DEFAULT '',
  `default_room` varchar(20) NOT NULL,
  `last_room` varchar(20) NOT NULL,
  PRIMARY KEY (`pos_user_id`),
  KEY `default_store_id` (`default_store_id`),
  KEY `pos_user_grou-_id` (`pos_user_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_users_in_groups`
--

DROP TABLE IF EXISTS `pos_users_in_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_users_in_groups` (
  `pos_user_group_id` int(10) unsigned NOT NULL,
  `pos_user_id` int(10) unsigned NOT NULL,
  KEY `pos_user_group_id` (`pos_user_group_id`,`pos_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_users_logged_in`
--

DROP TABLE IF EXISTS `pos_users_logged_in`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_users_logged_in` (
  `pos_user_id` int(10) unsigned NOT NULL,
  `http_user_agent` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `browser` varchar(30) NOT NULL,
  `last_accessed` datetime NOT NULL,
  `current_page` varchar(255) NOT NULL,
  `session_time_remaining` int(10) NOT NULL,
  KEY `pos_user_id` (`pos_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_zip_codes`
--

DROP TABLE IF EXISTS `pos_zip_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_zip_codes` (
  `pos_zip_code_id` int(10) NOT NULL AUTO_INCREMENT,
  `zip_code` varchar(12) NOT NULL,
  `type` varchar(20) NOT NULL,
  `primary_city` varchar(40) NOT NULL,
  `acceptable_cities` text NOT NULL,
  `unacceptable_cities` text NOT NULL,
  `state` varchar(10) NOT NULL,
  `pos_state_id` int(10) unsigned NOT NULL,
  `county` varchar(100) NOT NULL,
  `pos_county_id` int(10) unsigned NOT NULL,
  `timezone` varchar(100) NOT NULL,
  `area_codes` varchar(100) NOT NULL,
  `latitude` decimal(20,5) NOT NULL,
  `longitude` decimal(20,5) NOT NULL,
  `world_region` varchar(10) NOT NULL,
  `country` varchar(20) NOT NULL,
  `decommissioned` int(4) NOT NULL,
  `estimated_population` int(10) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`pos_zip_code_id`),
  KEY `pos_state_id` (`pos_state_id`),
  KEY `pos_county_id` (`pos_county_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42523 DEFAULT CHARSET=utf8 COMMENT='Data from http://www.unitedstateszipcodes.org/';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-31  9:57:18
