CREATE DATABASE  IF NOT EXISTS `iot_server` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `iot_server`;
-- MySQL dump 10.13  Distrib 5.7.30, for Linux (x86_64)
--
-- Host: localhost    Database: iot_server
-- ------------------------------------------------------
-- Server version	5.5.5-10.3.16-MariaDB

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
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `place` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `tokens_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_foreignkey_devices_tokens` (`tokens_id`),
  CONSTRAINT `c_fk_devices_tokens_id` FOREIGN KEY (`tokens_id`) REFERENCES `tokens` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT NULL,
  `calling` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `message` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'device','Role for the devices'),(2,'backoffice','Backoffice user - can add other users and devices in the system');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_routes`
--

DROP TABLE IF EXISTS `roles_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles_routes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `routes_id` int(11) unsigned DEFAULT NULL,
  `roles_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_2b83abfc3517026366c2a812dc67e524456d822b` (`roles_id`,`routes_id`),
  KEY `index_foreignkey_roles_routes_routes` (`routes_id`),
  KEY `index_foreignkey_roles_routes_roles` (`roles_id`),
  CONSTRAINT `c_fk_roles_routes_roles_id` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `c_fk_roles_routes_routes_id` FOREIGN KEY (`routes_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_routes`
--

LOCK TABLES `roles_routes` WRITE;
/*!40000 ALTER TABLE `roles_routes` DISABLE KEYS */;
INSERT INTO `roles_routes` VALUES (5,3,1),(6,4,1),(8,6,1),(10,7,1),(11,8,1),(12,9,1),(14,10,1),(15,11,1),(16,12,1),(18,13,1),(2,1,2),(3,2,2),(7,5,2),(9,6,2),(13,9,2),(17,12,2),(19,13,2),(20,14,2),(21,15,2),(22,16,2),(23,17,2),(24,18,2);
/*!40000 ALTER TABLE `roles_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `route` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `verb` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
INSERT INTO `routes` VALUES (1,'deviceGet','Return device details','/devices/','GET'),(2,'deviceDeviceUIDPost','Register a new device','/devices/:deviceUID','POST'),(3,'deviceDeviceUIDPut','Update an existing device','/devices/:deviceUID','PUT'),(4,'deviceDeviceUIDGet','Return device details','/devices/:deviceUID','GET'),(5,'deviceDeviceUIDDelete','Remove a device','/devices/:deviceUID','DELETE'),(6,'deviceDeviceUIDSensorsGet','Get data from a sensor','/devices/:deviceUID/sensors','GET'),(7,'deviceDeviceUIDSensorsSensorUIDPost','Register a new sensor in an existing device','/devices/:deviceUID/sensors/:sensorUID','POST'),(8,'deviceDeviceUIDSensorsSensorUIDPut','Update an existing sensor','/devices/:deviceUID/sensors/:sensorUID','PUT'),(9,'deviceDeviceUIDSensorsSensorUIDGet','Return sensor details','/devices/:deviceUID/sensors/:sensorUID','GET'),(10,'deviceDeviceUIDSensorsSensorUIDDelete','Remove a sensor','/devices/:deviceUID/sensors/:sensorUID','DELETE'),(11,'deviceDeviceUIDSensorsSensorUIDDataPost','Add data to a sensor','/devices/:deviceUID/sensors/:sensorUID/data','POST'),(12,'deviceDeviceUIDSensorsSensorUIDDataGet','Get data from a sensor','/devices/:deviceUID/sensors/:sensorUID/data','GET'),(13,'deviceDeviceUIDSensorsSensorUIDDataDelete','Delete data within the timestamp','/devices/:deviceUID/sensors/:sensorUID/data','DELETE'),(14,'usersUsernamePost','Create a new user in the system','/users/:username','POST'),(15,'authorizationRolesRoleUIDPost','Create a new role in the system','/authorization/roles/:roleUID','POST'),(16,'authorizationRoutesRouteUIDPost','Register a new route in the system','/authorization/routes/:routeUID','POST'),(17,'authorizationPermissionRoleUIDRouteUIDPost','Associate a route with a role','/authorization/permission/:roleUID/:routeUID','POST'),(18,'usersUsernamePasswordPut','Update the users password','/users/:username/password','PUT');
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensors`
--

DROP TABLE IF EXISTS `sensors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `uid` int(11) unsigned DEFAULT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `devices_id` int(11) unsigned DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_foreignkey_sensors_devices` (`devices_id`),
  CONSTRAINT `c_fk_sensors_devices_id` FOREIGN KEY (`devices_id`) REFERENCES `devices` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensors`
--

LOCK TABLES `sensors` WRITE;
/*!40000 ALTER TABLE `sensors` DISABLE KEYS */;
/*!40000 ALTER TABLE `sensors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `secret` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `roles_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_foreignkey_tokens_roles` (`roles_id`),
  CONSTRAINT `c_fk_tokens_roles_id` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
INSERT INTO `tokens` VALUES (1,'admin','92V+Xw9o6tlr7A==','$2y$10$MuJUSdZ2NQchdFaFD5mXJebF3xsQtCvganx9V2kjVTTWBX/Ni93PS',2);
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `tokens_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_foreignkey_users_tokens` (`tokens_id`),
  CONSTRAINT `c_fk_users_tokens_id` FOREIGN KEY (`tokens_id`) REFERENCES `tokens` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Basic Admin','admin@email.com',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-05-27 16:23:18
