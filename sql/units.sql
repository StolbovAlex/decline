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
-- Table structure for table `units`
--
USE decline;

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units` (
  `id` int AUTO_INCREMENT UNIQUE,
  `castle_id` int DEFAULT NULL,
  `castle_name` varchar(256) DEFAULT NULL,
  `type` tinyint DEFAULT NULL,
  `experience` int DEFAULT NULL,
  `cost` decimal(4,2) DEFAULT NULL,
  `rent` decimal(4,2) DEFAULT NULL,
  `health` tinyint DEFAULT NULL,
  `turns` tinyint DEFAULT NULL,
  `attack` decimal(4,2) DEFAULT NULL,
  `defense` decimal(4,2) DEFAULT NULL,
  `x` int DEFAULT NULL,
  `y` int DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES (1,5,'Beda',4,5,30.00,2.00,45,0,0.90,0.45,11,8),(3,13,'Tundra',1,0,5.00,0.50,100,3,1.00,1.00,11,22),(4,13,'Tundra',2,0,10.00,0.80,100,3,1.00,2.00,11,22),(5,14,'Best',1,0,5.00,0.50,100,3,1.00,1.00,18,30),(6,14,'Best',2,0,10.00,0.80,100,3,1.00,2.00,18,30),(7,15,'Cola',1,0,5.00,0.50,100,3,1.00,1.00,17,11),(8,15,'Cola',2,0,10.00,0.80,100,3,1.00,2.00,17,11),(9,15,'Cola',4,0,30.00,2.00,100,0,2.00,1.00,13,10),(11,5,'Beda',3,30,10.00,0.80,40,0,0.80,0.40,6,11),(12,5,'Beda',2,0,10.00,0.80,100,3,1.00,2.00,6,11);
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
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
