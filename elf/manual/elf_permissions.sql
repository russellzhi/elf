-- MySQL dump 10.13  Distrib 5.6.12, for Win32 (x86)
--
-- Host: localhost    Database: zhicms
-- ------------------------------------------------------
-- Server version	5.6.12-log

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
-- Table structure for table `elf_auth_group`
--

DROP TABLE IF EXISTS `elf_auth_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `elf_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` char(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elf_auth_group`
--

LOCK TABLES `elf_auth_group` WRITE;
/*!40000 ALTER TABLE `elf_auth_group` DISABLE KEYS */;
INSERT INTO `elf_auth_group` VALUES (1,'super admin',1,'1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17');
/*!40000 ALTER TABLE `elf_auth_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elf_auth_group_access`
--

DROP TABLE IF EXISTS `elf_auth_group_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `elf_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elf_auth_group_access`
--

LOCK TABLES `elf_auth_group_access` WRITE;
/*!40000 ALTER TABLE `elf_auth_group_access` DISABLE KEYS */;
INSERT INTO `elf_auth_group_access` VALUES (1,1);
/*!40000 ALTER TABLE `elf_auth_group_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elf_auth_rule`
--

DROP TABLE IF EXISTS `elf_auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `elf_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `app` varchar(100) NOT NULL DEFAULT 'Admin',
  `name` char(80) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elf_auth_rule`
--

LOCK TABLES `elf_auth_rule` WRITE;
/*!40000 ALTER TABLE `elf_auth_rule` DISABLE KEYS */;
INSERT INTO `elf_auth_rule` VALUES (1,'Admin','index/index','index/index',1,1,''),(2,'Admin','index/aaa','index/aaa',1,1,'');
/*!40000 ALTER TABLE `elf_auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elf_user`
--

DROP TABLE IF EXISTS `elf_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `elf_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT 'this is username',
  `pwd` varchar(33) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `age` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elf_user`
--

LOCK TABLES `elf_user` WRITE;
/*!40000 ALTER TABLE `elf_user` DISABLE KEYS */;
INSERT INTO `elf_user` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','ding@de.zhi',22);
/*!40000 ALTER TABLE `elf_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-26 17:18:20
