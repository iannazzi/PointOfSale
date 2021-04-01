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
-- Dumping data for table `pos_customer_payment_methods`
--

LOCK TABLES `pos_customer_payment_methods` WRITE;
/*!40000 ALTER TABLE `pos_customer_payment_methods` DISABLE KEYS */;
INSERT INTO `pos_customer_payment_methods` VALUES (1,'American Express','CREDIT_CARD',1),(2,'Visa','CREDIT_CARD',1),(3,'Master Card','CREDIT_CARD',1),(4,'Cash','CASH',1),(5,'Check','CHECK',1),(8,'Gift Card','STORE_CREDIT',1),(9,'Store Credit','STORE_CREDIT',1),(10,'Discover','CREDIT_CARD',1),(11,'Other','OTHER',0),(12,'Customer Account','OTHER',0);
/*!40000 ALTER TABLE `pos_customer_payment_methods` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-31 10:10:41
