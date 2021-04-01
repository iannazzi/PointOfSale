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
-- Dumping data for table `pos_states`
--

LOCK TABLES `pos_states` WRITE;
/*!40000 ALTER TABLE `pos_states` DISABLE KEYS */;
INSERT INTO `pos_states` VALUES (1,0.00000,'Alabama','AL'),(2,0.00000,'Alaska','AK'),(3,0.00000,'Arizona','AZ'),(4,0.00000,'Arkansas','AR'),(5,0.00000,'California','CA'),(6,0.00000,'Colorado','CO'),(7,0.00000,'Connecticut','CT'),(8,0.00000,'Delaware','DE'),(9,0.00000,'Florida','FL'),(10,0.00000,'Georgia','GA'),(11,0.00000,'Hawaii','HI'),(12,0.00000,'Idaho','ID'),(13,0.00000,'Illinois','IL'),(14,0.00000,'Indiana','IN'),(15,0.00000,'Iowa','IA'),(16,0.00000,'Kansas','KS'),(17,0.00000,'Kentucky','KY'),(18,0.00000,'Louisiana','LA'),(19,0.00000,'Maine','ME'),(20,0.00000,'Maryland','MD'),(21,0.00000,'Massachusetts','MA'),(22,0.00000,'Michigan','MI'),(23,0.00000,'Minnesota','MN'),(24,0.00000,'Mississippi','MS'),(25,0.00000,'Missouri','MO'),(26,0.00000,'Montana','MT'),(27,0.00000,'Nebraska','NE'),(28,0.00000,'Nevada','NV'),(29,0.00000,'New Hampshire','NH'),(30,0.00000,'New Jersey','NJ'),(31,0.00000,'New Mexico','NM'),(32,0.00000,'New York','NY'),(33,0.00000,'North Carolina','NC'),(34,0.00000,'North Dakota','ND'),(35,0.00000,'Ohio','OH'),(36,0.00000,'Oklahoma','OK'),(37,0.00000,'Oregon','OR'),(38,0.00000,'Pennsylvania','PA'),(39,0.00000,'Rhode Island','RI'),(40,0.00000,'South Carolina','SC'),(41,0.00000,'South Dakota','SD'),(42,0.00000,'Tennessee','TN'),(43,0.00000,'Texas','TX'),(44,0.00000,'Utah','UT'),(45,0.00000,'Vermont','VT'),(46,0.00000,'Virginia','VA'),(47,0.00000,'Washington','WA'),(48,0.00000,'West Virginia','WV'),(49,0.00000,'Wisconsin','WI'),(50,0.00000,'Wyoming','WY'),(51,0.00000,'District of Columbia','DC'),(52,0.00000,'Alberta','AB'),(53,0.00000,'British Columbia','BC'),(54,0.00000,'Manitoba','MB'),(55,0.00000,'New Brunswick','NB'),(56,0.00000,'Newfoundland and Labrador','NL'),(57,0.00000,'Northwest Territories','NT'),(58,0.00000,'Nova Scotia','NS'),(59,0.00000,'Nunavut','NU'),(60,0.00000,'Ontario','ON'),(61,0.00000,'Prince Edward Island','PE'),(62,0.00000,'Quebec','QC'),(63,0.00000,'Saskatchewan','SK'),(64,0.00000,'Yukon','YT');
/*!40000 ALTER TABLE `pos_states` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-31 10:15:44
