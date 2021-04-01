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
-- Dumping data for table `pos_chart_of_accounts_required`
--

LOCK TABLES `pos_chart_of_accounts_required` WRITE;
/*!40000 ALTER TABLE `pos_chart_of_accounts_required` DISABLE KEYS */;
INSERT INTO `pos_chart_of_accounts_required` VALUES (1,1,'Merchandise Inventory','',0,''),(2,4,'Accounts Payable','',0,''),(3,1,'Accounts Receivable','',0,''),(4,1,'Pending Merchandise Inventory','',0,''),(5,4,'Pending Accounts Payable','',0,''),(6,8,'Merchandise Inventory Shipping','',0,'Shipping for Finished Merchandise From a Supplier'),(7,1,'Cash','',0,''),(8,1,'Checking','',0,''),(9,8,'Merchandise Inventory Discounts','',0,''),(10,8,'Purchase Discounts','',0,''),(11,6,'Retained Earnings','',0,'');
/*!40000 ALTER TABLE `pos_chart_of_accounts_required` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-31 10:07:57
