-- MySQL dump 10.16  Distrib 10.1.26-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: db
-- ------------------------------------------------------
-- Server version	10.1.26-MariaDB-0+deb9u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `castles`
--
USE decline;

DROP TABLE IF EXISTS `castles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `castles` (
  `id` int AUTO_INCREMENT UNIQUE,
  `owner_id` int DEFAULT NULL,
  `castle_name` varchar(256) DEFAULT NULL,
  `race` varchar(16) DEFAULT NULL,
  `x` int DEFAULT NULL,
  `y` int DEFAULT NULL,
  `population` smallint DEFAULT NULL,
  `gold` decimal(12,2) DEFAULT NULL,
  `food` decimal(12,2) DEFAULT NULL,
  `tax` tinyint DEFAULT NULL,
  `protector_id` int DEFAULT NULL,
  `protector_name` varchar(256) DEFAULT NULL,
  `protector_tax` tinyint DEFAULT NULL,
  `destroyed` int DEFAULT NULL,
  `date_creation` varchar(19) DEFAULT NULL,
  `hour_turn` tinyint DEFAULT NULL,
  `hour_change` varchar(19) DEFAULT NULL,
   PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `castles`
--

LOCK TABLES `castles` WRITE;
/*!40000 ALTER TABLE `castles` DISABLE KEYS */;
INSERT INTO `castles` VALUES (5,1,'Beda','Люди',6,11,87,6.95,0.92,10,0,'',0,0,'2015-12-14 03:10:39',12,'2015-12-23 14:33:12'),(6,1,'qwerty','Люди',10,7,88,41.18,1.43,30,0,'',0,0,'2015-12-14 03:13:32',10,'2015-12-18 00:58:40'),(15,2,'Cola','Люди',17,11,105,25.86,0.94,10,0,'',0,0,'2016-01-04 10:43:23',12,'2016-01-04 10:43:23'),(8,1,'aaa','Люди',13,17,117,58.75,0.86,10,0,'',0,0,'2015-12-18 05:46:54',12,'2015-12-18 05:46:54'),(13,1,'Tundra','Люди',11,22,115,56.86,0.94,10,0,'',0,0,'2016-01-04 10:33:49',12,'2016-01-04 10:33:49'),(10,1,'Кощей','Люди',19,23,117,58.75,0.86,10,0,'',0,0,'2015-12-22 13:14:10',12,'2015-12-22 13:14:10'),(11,3,'Hello','Люди',22,8,117,58.75,0.86,10,0,'',0,0,'2016-01-02 09:54:54',12,'2016-01-02 09:54:54'),(14,2,'Best','Люди',18,30,115,56.86,0.94,10,0,'',0,0,'2016-01-04 10:42:37',12,'2016-01-04 10:42:37'),(12,3,'sewer','Люди',14,27,117,58.75,0.86,10,0,'',0,0,'2016-01-02 09:55:48',12,'2016-01-02 09:55:48');
/*!40000 ALTER TABLE `castles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-08-22 15:26:13
