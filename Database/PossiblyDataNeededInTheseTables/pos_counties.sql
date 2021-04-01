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
-- Dumping data for table `pos_counties`
--

LOCK TABLES `pos_counties` WRITE;
/*!40000 ALTER TABLE `pos_counties` DISABLE KEYS */;
INSERT INTO `pos_counties` VALUES (1,32,'Albany','',2),(2,32,'Allegany','',3),(3,32,'Bronx','',78),(4,32,'Broome','',4),(5,32,'Cattaraugus','',5),(6,32,'Cayuga','',8),(7,32,'Chautauqua','',10),(8,32,'Chemung','',11),(9,32,'Chenango','',12),(10,32,'Clinton','',14),(11,32,'Columbia','',15),(12,32,'Cortland','',16),(13,32,'Delaware','',17),(14,32,'Dutchess','',18),(15,32,'Erie','',19),(16,32,'Essex','',20),(17,32,'Franklin','',21),(18,32,'Fulton','',22),(19,32,'Genesee','',25),(20,32,'Greene','',26),(21,32,'Hamilton','',27),(22,32,'Herkimer','',28),(23,32,'Jefferson','',29),(24,32,'Kings','Brooklyn',78),(25,32,'Lewis','',30),(26,32,'Livingston','',31),(27,32,'Madison','',32),(28,32,'Monroe','',34),(29,32,'Montgomery','',35),(30,32,'Nassau','',36),(31,32,'New York City','Manhattan',78),(32,32,'Niagara','',37),(33,32,'Oneida','',38),(34,32,'Onondaga','',41),(35,32,'Ontario','',42),(36,32,'Orange','',43),(37,32,'Orleans','',44),(38,32,'Oswego','',45),(39,32,'Otsego','',47),(40,32,'Putnam','',48),(41,32,'Queens','',78),(42,32,'Rensselaer','',49),(43,32,'Richmond','Staten Island',78),(44,32,'Rockland','',50),(45,32,'St. Lawrence','',51),(46,32,'Saratoga','',53),(47,32,'Schenectady','',54),(48,32,'Schoharie','',55),(49,32,'Schuyler','',56),(50,32,'Seneca','',57),(51,32,'Steuben','',58),(52,32,'Suffolk','',61),(53,32,'Sullivan','',62),(54,32,'Tioga','',63),(55,32,'Tompkins','',64),(56,32,'Ulster','',66),(57,32,'Warren','',67),(58,32,'Washington','',69),(59,32,'Wayne','',70),(60,32,'Westchester','',71),(61,32,'Wyoming','',76),(62,32,'Yates','',77);
/*!40000 ALTER TABLE `pos_counties` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-31 10:08:48
