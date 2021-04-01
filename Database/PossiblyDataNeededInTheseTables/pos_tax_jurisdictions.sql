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
-- Dumping data for table `pos_tax_jurisdictions`
--

LOCK TABLES `pos_tax_jurisdictions` WRITE;
/*!40000 ALTER TABLE `pos_tax_jurisdictions` DISABLE KEYS */;
INSERT INTO `pos_tax_jurisdictions` VALUES (1,32,'New York State','NE 0021 ',4.00000,'State',1),(2,32,'Albany County ','AL 0181 ',4.00000,'Local',1),(3,32,'Allegany County ','AL 0221 ',4.50000,'Local',1),(4,32,'Broome County ','BR 0321 ',4.00000,'Local',1),(5,32,'Cattaraugus County','CA 0481 ',4.00000,'Local',1),(6,32,'Olean (city) ','OL 0441 ',4.00000,'Local',1),(7,32,'Salamanca (city) ','SA 0431 ',4.00000,'Local',1),(8,32,'Cayuga County','CA 0511 ',4.00000,'Local',1),(9,32,'Auburn (city) ','AU 0561 ',4.00000,'Local',1),(10,32,'Chautauqua County ','CH 0651 ',3.00000,'Local',1),(11,32,'Chemung County ','CH 0711 ',4.00000,'Local',1),(12,32,'Chenango County','CH 0861 ',4.00000,'Local',1),(13,32,'Norwich (city) ','NO 0831 ',4.00000,'Local',1),(14,32,'Clinton County ','CL 0921 ',4.00000,'Local',1),(15,32,'Columbia County ','CO 1021 ',4.00000,'Local',1),(16,32,'Cortland County ','CO 1131 ',4.00000,'Local',1),(17,32,'Delaware County ','DE 1221 ',4.00000,'Local',1),(18,32,'Dutchess County ','DU 1311 ',4.12500,'Local',1),(19,32,'Erie County ','ER 1451 ',4.75000,'Local',1),(20,32,'Essex County ','ES 1521 ',3.75000,'Local',1),(21,32,'Franklin County ','FR 1621 ',4.00000,'Local',1),(22,32,'Fulton County','FU 1791 ',4.00000,'Local',1),(23,32,'Gloversville (city) ','GL 1741 ',4.00000,'Local',1),(24,32,'Johnstown (city) ','JO 1751 ',4.00000,'Local',1),(25,32,'Genesee County ','GE 1811 ',4.00000,'Local',1),(26,32,'Greene County ','GR 1911 ',4.00000,'Local',1),(27,32,'Hamilton County ','HA 2011 ',3.00000,'Local',1),(28,32,'Herkimer County ','HE 2121 ',4.00000,'Local',1),(29,32,'Jefferson County ','JE 2221 ',3.75000,'Local',1),(30,32,'Lewis County ','LE 2321 ',3.75000,'Local',1),(31,32,'Livingston County ','LI 2411 ',4.00000,'Local',1),(32,32,'Madison County','MA 2511 ',4.00000,'Local',1),(33,32,'Oneida (city) ','ON 2541 ',4.00000,'Local',1),(34,32,'Monroe County ','MO 2611 ',4.00000,'Local',1),(35,32,'Montgomery County ','MO 2781 ',4.00000,'Local',1),(36,32,'Nassau County ','NA 2811 ',4.62500,'Local',1),(37,32,'Niagara County ','NI 2911 ',4.00000,'Local',1),(38,32,'Oneida County','ON 3010 ',4.75000,'Local',1),(39,32,'Rome (city) ','RO 3015 ',4.75000,'Local',1),(40,32,'Utica (city) ','UT 3018 ',4.75000,'Local',1),(41,32,'Onondaga County ','ON 3121 ',4.00000,'Local',1),(42,32,'Ontario County ','ON 3211 ',3.00000,'Local',1),(43,32,'Orange County ','OR 3321 ',4.12500,'Local',1),(44,32,'Orleans County ','OR 3481 ',4.00000,'Local',1),(45,32,'Oswego County','OS 3501 ',4.00000,'Local',1),(46,32,'Oswego (city) ','OS 3561 ',4.00000,'Local',1),(47,32,'Otsego County ','OT 3621 ',4.00000,'Local',1),(48,32,'Putnam County ','PU 3731 ',4.37500,'Local',1),(49,32,'Rensselaer County ','RE 3881 ',4.00000,'Local',1),(50,32,'Rockland County ','RO 3921 ',4.37500,'Local',1),(51,32,'St. Lawrence County ','ST 4091 ',3.00000,'Local',1),(52,32,'Saratoga County','SA 4111 ',3.00000,'Local',1),(53,32,'Saratoga Springs (city) ','SA 4131 ',3.00000,'Local',1),(54,32,'Schenectady County ','SC 4241 ',4.00000,'Local',1),(55,32,'Schoharie County ','SC 4321 ',4.00000,'Local',1),(56,32,'Schuyler County ','SC 4411 ',4.00000,'Local',1),(57,32,'Seneca County ','SE 4511 ',4.00000,'Local',1),(58,32,'Steuben County','ST 4691 ',4.00000,'Local',1),(59,32,'Corning (city) ','CO 4611 ',4.00000,'Local',1),(60,32,'Hornell (city) ','HO 4641 ',4.00000,'Local',1),(61,32,'Suffolk County ','SU 4711 ',4.62500,'Local',1),(62,32,'Sullivan County ','SU 4821 ',4.00000,'Local',1),(63,32,'Tioga County ','TI 4921 ',4.00000,'Local',1),(64,32,'Tompkins County','TO 5081 ',4.00000,'Local',1),(65,32,'Ithaca (city) ','IT 5021 ',4.00000,'Local',1),(66,32,'Ulster County ','UL 5111 ',4.00000,'Local',1),(67,32,'Warren County','WA 5281 ',3.00000,'Local',1),(68,32,'Glens Falls (city) ','GL 5211 ',3.00000,'Local',1),(69,32,'Washington County ','WA 5311 ',3.00000,'Local',1),(70,32,'Wayne County ','WA 5421 ',4.00000,'Local',1),(71,32,'Westchester County','WE 5581 ',3.37500,'Local',1),(72,32,'Mount Vernon (city) ','MO 5521 ',4.37500,'Local',1),(73,32,'New Rochelle (city) ','NE 6861 ',4.37500,'Local',1),(74,32,'White Plains (city) ','WH 6513 ',4.37500,'Local',1),(75,32,'Yonkers (city) ','YO 6511 ',4.37500,'Local',1),(76,32,'Wyoming County ','WY 5621 ',4.00000,'Local',1),(77,32,'Yates County ','YA 5721 ',4.00000,'Local',1),(78,32,'New York City/State Combined Tax (New York City includes counties of Bronx, Kings (Brooklyn), New York (Manhattan), Queens, and Richmond (Staten Island)','NE 8081 ',4.87500,'Local',1),(79,32,'New York City/MCTD','NE 8061 ',0.37500,'Local',1),(80,32,'New York City - Local Tax Only','NE 8091 ',0.00000,'Local',1);
/*!40000 ALTER TABLE `pos_tax_jurisdictions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-31 10:17:08
