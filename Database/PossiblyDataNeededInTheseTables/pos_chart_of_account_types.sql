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
-- Dumping data for table `pos_chart_of_account_types`
--

LOCK TABLES `pos_chart_of_account_types` WRITE;
/*!40000 ALTER TABLE `pos_chart_of_account_types` DISABLE KEYS */;
INSERT INTO `pos_chart_of_account_types` VALUES (1,'Current Assets','Bank, Cash, Not Specified, Inventory',1000,''),(2,'Long-Term Assets','',1500,''),(3,'Other Assests','',1900,''),(4,'Current Liabilities','',2000,''),(5,'Long-Term Liabilities','',2600,''),(6,'Equity','',3500,''),(7,'Revenue','',4000,''),(8,'Cost Of Goods Sold','',5000,''),(9,'Expense','',6000,'');
/*!40000 ALTER TABLE `pos_chart_of_account_types` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-31 10:06:46
