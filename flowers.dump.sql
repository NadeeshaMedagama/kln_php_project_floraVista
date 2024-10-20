/*M!999999\- enable the sandbox mode */
-- MariaDB dump 10.19-11.4.3-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: kln_php
-- ------------------------------------------------------
-- Server version	11.4.3-MariaDB-1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
                         `admin_id` int(10) NOT NULL AUTO_INCREMENT,
                         `admin_name` varchar(20) NOT NULL,
                         `email` varchar(30) NOT NULL,
                         `password` varchar(80) NOT NULL,
                         PRIMARY KEY (`admin_id`),
                         UNIQUE KEY `admin_name` (`admin_name`),
                         UNIQUE KEY `email` (`email`),
                         KEY `idx_admin_id` (`admin_id`),
                         KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES
    (1,'thush','thushanmadhusanka3@gmail.com','67215bebe2fe2737d90bb951347c6a0852a1f537');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
                              `category_id` int(5) NOT NULL AUTO_INCREMENT,
                              `category_name` varchar(20) NOT NULL,
                              PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
                             (1,'romance'),
                             (2,'room decaration'),
                             (3,'birthday gift'),
                             (4,'garden'),
                             (5,'decaretions'),
                             (6,'wedding');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
                            `flower_id` varchar(100) DEFAULT NULL,
                            `user_id` int(10) DEFAULT NULL,
                            `comment` text DEFAULT NULL,
                            KEY `user_id` (`user_id`),
                            KEY `idx_flower_id` (`flower_id`),
                            CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`flower_id`) REFERENCES `flowers` (`flower_id`) ON DELETE CASCADE,
                            CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES
                           ('670f8e5d133e3',3,'wow'),
                           ('670f8e5d133e3',3,'tbgvihyv'),
                           ('670f8e5d133e3',3,'ergh'),
                           ('670f8e5d133e3',3,'werthwerh'),
                           ('670f8e5d133e3',3,'rtyutyu'),
                           ('670f525cb03f3',3,'sdfh'),
                           ('670f525cb03f3',3,'sdfgdzfhsdfh'),
                           ('670f525cb03f3',3,'asdfhdafsgasg'),
                           ('670f8e72548f2',3,'dhjthgj'),
                           ('670f8e72548f2',3,'fsghsdgfhdfh');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_items`
--

DROP TABLE IF EXISTS `delivery_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_items` (
                                  `flower_id` varchar(100) DEFAULT NULL,
                                  `user_id` int(10) DEFAULT NULL,
                                  `quantity` int(5) DEFAULT NULL,
                                  `reference_no` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_items`
--

LOCK TABLES `delivery_items` WRITE;
/*!40000 ALTER TABLE `delivery_items` DISABLE KEYS */;
INSERT INTO `delivery_items` VALUES
                                 ('670f8e5d133e3',3,2,'6713a8892db6d'),
                                 ('670f9f830fde0',3,3,'6713a8892db6d'),
                                 ('670f8e5d133e3',3,3,'6713b0a414cca'),
                                 ('670f9f830fde0',3,1,'6713b0a414cca'),
                                 ('670f8e5d133e3',3,3,'6713e1181e579'),
                                 ('670f9f830fde0',3,2,'6713e1181e579'),
                                 ('670f8e5d133e3',3,11,'671488a110dcc'),
                                 ('670f9f830fde0',3,1,'67148b73a96ee');
/*!40000 ALTER TABLE `delivery_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flower_categories`
--

DROP TABLE IF EXISTS `flower_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flower_categories` (
                                     `flower_id` varchar(100) NOT NULL,
                                     `category_id` int(5) NOT NULL,
                                     PRIMARY KEY (`flower_id`,`category_id`),
                                     KEY `category_id` (`category_id`),
                                     CONSTRAINT `flower_categories_ibfk_1` FOREIGN KEY (`flower_id`) REFERENCES `flowers` (`flower_id`) ON DELETE CASCADE,
                                     CONSTRAINT `flower_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flower_categories`
--

LOCK TABLES `flower_categories` WRITE;
/*!40000 ALTER TABLE `flower_categories` DISABLE KEYS */;
INSERT INTO `flower_categories` VALUES
                                    ('670f8e5d133e3',1),
                                    ('670f525cb03f3',6),
                                    ('670f8e5d133e3',6);
/*!40000 ALTER TABLE `flower_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flower_discounts`
--

DROP TABLE IF EXISTS `flower_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flower_discounts` (
                                    `flower_id` varchar(100) DEFAULT NULL,
                                    `today_dicount` int(3) DEFAULT NULL,
                                    `loyalty_discount` int(3) DEFAULT NULL,
                                    `price_off` int(5) DEFAULT NULL,
                                    `today_discount_end` date DEFAULT NULL,
                                    `loyalty_discount_end` date DEFAULT NULL,
                                    `price_off_end` date DEFAULT NULL,
                                    UNIQUE KEY `flower_id` (`flower_id`),
                                    KEY `idx_flower_id` (`flower_id`),
                                    CONSTRAINT `flower_discounts_ibfk_1` FOREIGN KEY (`flower_id`) REFERENCES `flowers` (`flower_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flower_discounts`
--

LOCK TABLES `flower_discounts` WRITE;
/*!40000 ALTER TABLE `flower_discounts` DISABLE KEYS */;
INSERT INTO `flower_discounts` VALUES
                                   ('670f8e5d133e3',5,6,4,'2024-10-18','2024-10-24','2024-10-30'),
                                   ('670f525cb03f3',3,NULL,NULL,'2024-10-18',NULL,NULL),
                                   ('670f8e72548f2',NULL,NULL,2,NULL,NULL,'2024-10-20'),
                                   ('670f9f830fde0',NULL,NULL,2,NULL,NULL,'2024-10-20');
/*!40000 ALTER TABLE `flower_discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flower_images`
--

DROP TABLE IF EXISTS `flower_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flower_images` (
                                 `flower_id` varchar(100) DEFAULT NULL,
                                 `dir_path` text DEFAULT NULL,
                                 KEY `idx_flower_id` (`flower_id`),
                                 CONSTRAINT `flower_images_ibfk_1` FOREIGN KEY (`flower_id`) REFERENCES `flowers` (`flower_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flower_images`
--

LOCK TABLES `flower_images` WRITE;
/*!40000 ALTER TABLE `flower_images` DISABLE KEYS */;
INSERT INTO `flower_images` VALUES
                                ('670f525cb03f3','uploads/670f525cb040f.png'),
                                ('670f8e5d133e3','uploads/670f8e5d133fd.png'),
                                ('670f8e72548f2','uploads/670f8e725490d.png'),
                                ('670f9f830fde0','uploads/670f9f830fe07.png');
/*!40000 ALTER TABLE `flower_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flowers`
--

DROP TABLE IF EXISTS `flowers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flowers` (
                           `flower_id` varchar(100) NOT NULL,
                           `flower_name` varchar(20) DEFAULT NULL,
                           `quantity` int(5) DEFAULT 0,
                           `sale_price` float DEFAULT NULL,
                           `description` varchar(255) DEFAULT NULL,
                           `other_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`other_info`)),
                           PRIMARY KEY (`flower_id`),
                           KEY `idx_flower_name` (`flower_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flowers`
--

LOCK TABLES `flowers` WRITE;
/*!40000 ALTER TABLE `flowers` DISABLE KEYS */;
INSERT INTO `flowers` VALUES
                          ('670f525cb03f3','aregasrg',0,560,'sergsrg',NULL),
                          ('670f8e5d133e3','rose',51,610,'sdfhsdfh',NULL),
                          ('670f8e72548f2','boganvila',0,300,'sdhsre',NULL),
                          ('670f9f830fde0','tulip',54,550,'svdsdvsdv',NULL);
/*!40000 ALTER TABLE `flowers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_users`
--

DROP TABLE IF EXISTS `loyalty_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loyalty_users` (
                                 `loyalty_id` varchar(36) NOT NULL,
                                 `points_blance` int(5) DEFAULT 0,
                                 `user_id` int(10) NOT NULL,
                                 PRIMARY KEY (`user_id`,`loyalty_id`),
                                 CONSTRAINT `loyalty_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_users`
--

LOCK TABLES `loyalty_users` WRITE;
/*!40000 ALTER TABLE `loyalty_users` DISABLE KEYS */;
INSERT INTO `loyalty_users` VALUES
    ('67147ae8df491',85,3);
/*!40000 ALTER TABLE `loyalty_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
                          `order_id` int(10) NOT NULL AUTO_INCREMENT,
                          `order_date` date DEFAULT curdate(),
                          `flower_id` varchar(100) DEFAULT NULL,
                          `quantity` int(5) DEFAULT NULL,
                          `suplier_id` int(10) DEFAULT NULL,
                          `isAccept_suplier` tinyint(1) DEFAULT 0,
                          `accept_suplier_date` date DEFAULT NULL,
                          `delivered_date` date DEFAULT NULL,
                          `isDelivered` tinyint(1) DEFAULT 0,
                          `purchase_price` float DEFAULT NULL,
                          `order_sale_price` float DEFAULT 0,
                          PRIMARY KEY (`order_id`),
                          KEY `flower_id` (`flower_id`),
                          KEY `suplier_id` (`suplier_id`),
                          CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`flower_id`) REFERENCES `flowers` (`flower_id`) ON DELETE CASCADE,
                          CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`suplier_id`) REFERENCES `supliers` (`suplier_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
                         (2,'2024-10-17','670f8e5d133e3',50,1,1,'2024-10-18','2024-10-18',1,100,610),
                         (21,'2024-10-18','670f525cb03f3',46,1,0,NULL,NULL,0,NULL,0),
                         (22,'2024-10-20','670f9f830fde0',200,1,1,'2024-10-20',NULL,0,100,0),
                         (23,'2024-10-20','670f8e5d133e3',100,1,0,NULL,NULL,0,NULL,0);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
                            `reference_no` varchar(20) DEFAULT NULL,
                            `user_id` int(10) NOT NULL,
                            `amount` decimal(10,0) NOT NULL,
                            `date` date DEFAULT NULL,
                            `address` text DEFAULT NULL,
                            KEY `user_id` (`user_id`),
                            CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
                           ('6713a8892db6d',3,2952,'2024-10-19','73/jdfwe/u78mdf'),
                           ('6713b0a414cca',3,2464,'2024-10-19','73/jdfwe/u78mdf'),
                           ('6713e1181e579',3,3025,'2024-10-19','sdfhsdfhsdfhg'),
                           ('671488a110dcc',3,7359,'2024-10-20',''),
                           ('67148b73a96ee',3,530,'2024-10-20','73/jdfwe/');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shopping_cart`
--

DROP TABLE IF EXISTS `shopping_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopping_cart` (
                                 `user_id` int(10) NOT NULL,
                                 `flower_id` varchar(100) NOT NULL,
                                 `quantity` int(5) DEFAULT 0,
                                 PRIMARY KEY (`user_id`,`flower_id`),
                                 KEY `flower_id` (`flower_id`),
                                 CONSTRAINT `shopping_cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
                                 CONSTRAINT `shopping_cart_ibfk_2` FOREIGN KEY (`flower_id`) REFERENCES `flowers` (`flower_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shopping_cart`
--

LOCK TABLES `shopping_cart` WRITE;
/*!40000 ALTER TABLE `shopping_cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `shopping_cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supliers`
--

DROP TABLE IF EXISTS `supliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supliers` (
                            `suplier_id` int(10) NOT NULL AUTO_INCREMENT,
                            `suplier_username` varchar(20) NOT NULL,
                            `email` varchar(40) NOT NULL,
                            `password` varchar(80) NOT NULL,
                            `mobile` int(10) NOT NULL,
                            `verify` tinyint(4) DEFAULT 0,
                            PRIMARY KEY (`suplier_id`),
                            UNIQUE KEY `email` (`email`),
                            UNIQUE KEY `mobile` (`mobile`),
                            KEY `idx_suplier_id` (`suplier_id`),
                            KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supliers`
--

LOCK TABLES `supliers` WRITE;
/*!40000 ALTER TABLE `supliers` DISABLE KEYS */;
INSERT INTO `supliers` VALUES
                           (1,'thushan','thushanmadusanka456@gmail.com','7c4a8d09ca3762af61e59520943dc26494f8941b',785914774,1),
                           (2,'kamal','kamal@mail.com','1241324',784912554,1),
                           (3,'sunil','sunil@mail.com','adgr53y',788914551,0);
/*!40000 ALTER TABLE `supliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
                         `user_id` int(10) NOT NULL AUTO_INCREMENT,
                         `user_name` varchar(20) NOT NULL,
                         `email` varchar(30) NOT NULL,
                         `password` varchar(80) NOT NULL,
                         `mobile` int(10) NOT NULL,
                         `gender` tinyint(4) DEFAULT NULL,
                         `address` text DEFAULT NULL,
                         PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
                        (2,'kamal','kamal123@gmail.com','7c4a8d09ca3762af61e59520943dc26494f8941b',788976554,1,NULL),
                        (3,'thushan','Thushanmadusanka456@gmail.com','67215bebe2fe2737d90bb951347c6a0852a1f537',785914774,1,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2024-10-20 10:21:30