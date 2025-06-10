/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.7.2-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: c2c
-- ------------------------------------------------------
-- Server version	11.7.2-MariaDB

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
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`admin_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES
(3,1008);
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cart_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES
(1,'Electronics','Phones, laptops, gadgets, and more'),
(2,'Fashion','Clothing, shoes, accessories'),
(3,'Home & Garden','Furniture, tools, plants'),
(4,'Toys & Games','Games, action figures, puzzles'),
(5,'Books & Media','Books, movies, music'),
(6,'Vehicles','Cars, bikes, parts'),
(7,'Sports & Outdoors','Sporting goods, camping gear'),
(8,'Health & Beauty','Cosmetics, skincare, wellness'),
(9,'Baby & Kids','Strollers, clothes, toys for babies'),
(10,'Services','Freelance, tutoring, home repair'),
(12,'Real Estate','Plots, homes and rental buildings'),
(13,'Gaming','Consoles, PC and Gaming Accessories'),
(14,'Furniture','Tables, chairs, desks, and cabinets,'),
(15,'Appliances','Home appliances');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_path` varchar(255) NOT NULL,
  `listing_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_file_path` (`file_path`),
  KEY `listing_id` (`listing_id`),
  CONSTRAINT `images_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
INSERT INTO `images` VALUES
(7,'1002-s20selfie.jpg',1002),
(8,'1002-blacks20.jpg',1002),
(9,'1002-s20backb.jpg',1002),
(10,'1001-9f00b29e5972f74f0c7b9723a18fb77610d42d07a2f3eae716ad6cc5b42b0549',1001),
(11,'1001-05b96e3ba1f3273f76b19405893fdeb072c841a24b662627788521bb457bfdd6',1001),
(12,'1001-35504b65797a62097ff01d6a6b50d9e26905fef1db55eb55043573e6479373ab',1001),
(13,'ca9a679a232364109569ee12925d7361',1001),
(14,'7d4396009d9c746ce479ff87708af244',1001),
(15,'9a1d1587f8a32929534a1363a78d2bb8',1001),
(16,'831e5b2d4f016d6e',1001),
(17,'aee9fb159520003a',1015),
(18,'ad08156861037a98',1015),
(19,'8a522e53c828507c',1016),
(20,'f87272c3be5c4deb',1017),
(21,'b42f3ff51633c6f6',1018),
(22,'d1a816c4549e3f8f',1019),
(23,'251e297d2d24b309',1020),
(24,'4aa0b2c7532909dd',1021),
(25,'9234a2d8a996d814',1022),
(26,'1a2d3a2a2fd300ae',1011),
(27,'55a185da76d2d886',1010),
(28,'5cba428d5a7a90e5',1005),
(29,'b1dd254bbde17512',1003),
(30,'1e5bbd555f937a64',1023),
(31,'d0889741874610c0',1024),
(32,'af49f4927cc9b6ed',1025),
(33,'e704139f96049e99',1026),
(34,'a10c0fa9dc7a0dcd',1027);
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listing_ad`
--

DROP TABLE IF EXISTS `listing_ad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `listing_ad` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT,
  `listing_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `slug` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ad_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `listing_id` (`listing_id`),
  KEY `cat_id` (`cat_id`),
  KEY `location_id` (`location_id`),
  FULLTEXT KEY `title` (`title`),
  CONSTRAINT `listing_ad_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE,
  CONSTRAINT `listing_ad_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `category` (`cat_id`),
  CONSTRAINT `listing_ad_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listing_ad`
--

LOCK TABLES `listing_ad` WRITE;
/*!40000 ALTER TABLE `listing_ad` DISABLE KEYS */;
INSERT INTO `listing_ad` VALUES
(4,1003,1,1,'Samsung S21','Screen broken damaged phone','2025-05-05 13:21:01','1-samsung-s21'),
(6,1005,6,4,'FeroD1 2XD2 max light','Car is brand new','2025-05-20 17:05:49','1-ferod1-2xd2-max-light'),
(11,1010,5,7,'Wizard of Oz and the Deathly Games','My own book that i have been writing.','2025-05-25 19:41:24','1-wizard-of-oz-and-the-deathly-games'),
(12,1011,7,7,'Vintage 1989  Montreal Canadiens Hockey stick','Very old but signed vintage hockey stick','2025-05-25 19:45:11','1-vintage-1989--montreal-canadiens-hockey-stick'),
(16,1015,1,8,'iPhone 12 128GB (Black)','Well-maintained iPhone 12, minor scuffs on edges, battery health at 88%. Includes charger and case.','2025-06-05 13:03:13','3-iphone-12-128gb-(black)'),
(17,1016,7,8,'Men’s Trek Mountain Bike','Hardtail mountain bike, 29-inch wheels, recently serviced. Ideal for trail and commute.','2025-06-05 13:09:12','3-men-s-trek-mountain-bike'),
(18,1017,1,8,'Samsung 55” UHD Smart TV','4K resolution, Netflix/YouTube built-in, includes remote and wall bracket.','2025-06-05 13:12:34','3-samsung-55-uhd-smart-tv'),
(19,1018,9,8,'Baby Cot with Mattress','White wooden baby cot with adjustable base. Hardly used. Clean mattress included.','2025-06-05 13:13:03','3-baby-cot-with-mattress'),
(20,1019,1,8,'Asus Vivobook 15 (i5, 8GB RAM, 256GB SSD)','Perfect for students or remote work. Light, fast, and in excellent condition.','2025-06-05 13:16:20','3-asus-vivobook-15-(i5,-8gb-ram,-256gb-ssd)'),
(21,1020,13,8,'Xbox Series S Console with 2 Controllers','Excellent condition, includes power cable and HDMI. 2 Games Forza Horizon and Lego star wars','2025-06-05 13:23:04','3-xbox-series-s-console-with-2-controllers'),
(22,1021,14,8,'Set of 4 Bar Stools','Industrial-style stools, metal legs with wooden tops. Sturdy and stylish.','2025-06-05 13:23:26','3-set-of-4-bar-stools'),
(23,1022,2,8,'Women’s Winter Clothing Bundle (Size M)','Includes 2 coats, 3 sweaters, jeans, and scarves. Clean and gently worn.','2025-06-05 13:27:17','3-women-s-winter-clothing-bundle-size-m'),
(24,1023,15,1,'Samsung Microwave Oven (30L)','1000W, with grill function and digital controls. Good working condition.','2025-06-05 14:43:45','4-samsung-microwave-oven-30l'),
(25,1024,4,1,'LEGO Classic 900-Piece Set','All pieces accounted for, great for kids or collectors. Includes original box.','2025-06-05 14:45:06','4-lego-classic-900-piece-set'),
(26,1025,1,1,'Samsung Galaxy Watch 4 (44mm)','Includes charger and strap. Great for fitness tracking and notifications.','2025-06-05 14:46:16','4-samsung-galaxy-watch-4-44mm'),
(27,1026,15,1,'KitchenAid Stand Mixer (Red)','Excellent working condition. Powerful motor and attachments included.','2025-06-05 14:47:32','4-kitchenaid-stand-mixer-red'),
(28,1027,3,1,'Set of 3 Succulent Plants in Ceramic Pots','Perfect for indoor spaces. Low-maintenance and beautifully arranged.','2025-06-05 14:48:50','4-set-of-3-succulent-plants-in-ceramic-pots');
/*!40000 ALTER TABLE `listing_ad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listings`
--

DROP TABLE IF EXISTS `listings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `listings` (
  `listing_id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`listing_id`),
  KEY `seller_id` (`seller_id`),
  CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`seller_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1028 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listings`
--

LOCK TABLES `listings` WRITE;
/*!40000 ALTER TABLE `listings` DISABLE KEYS */;
INSERT INTO `listings` VALUES
(1000,1,300.00,'2025-05-04 17:27:55'),
(1001,1,7999.00,'2025-05-05 11:20:47'),
(1002,1,10000.00,'2025-05-05 11:20:57'),
(1003,1,9800.00,'2025-05-05 11:21:01'),
(1004,1,3999.00,'2025-05-05 11:21:08'),
(1005,1,231000.00,'2025-05-20 15:05:49'),
(1009,1,20000.00,'2025-05-23 16:02:57'),
(1010,1,120.00,'2025-05-25 17:41:24'),
(1011,1,3000.00,'2025-05-25 17:45:11'),
(1012,1,78.99,'2025-05-25 18:01:00'),
(1013,1,120000.00,'2025-06-02 09:45:51'),
(1014,1,48545.00,'2025-06-02 10:58:14'),
(1015,3,8500.00,'2025-06-05 11:03:13'),
(1016,3,4500.00,'2025-06-05 11:09:12'),
(1017,3,6700.00,'2025-06-05 11:12:34'),
(1018,3,1200.00,'2025-06-05 11:13:03'),
(1019,3,1200.00,'2025-06-05 11:16:20'),
(1020,3,4500.00,'2025-06-05 11:23:04'),
(1021,3,4500.00,'2025-06-05 11:23:26'),
(1022,3,600.00,'2025-06-05 11:27:17'),
(1023,4,950.00,'2025-06-05 12:43:45'),
(1024,4,850.00,'2025-06-05 12:45:06'),
(1025,4,2200.00,'2025-06-05 12:46:16'),
(1026,4,2200.00,'2025-06-05 12:47:32'),
(1027,4,300.00,'2025-06-05 12:48:50');
/*!40000 ALTER TABLE `listings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `location` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `province` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `province` (`province`,`city`),
  UNIQUE KEY `unique_province_city` (`province`,`city`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES
(8,'Gauteng','Johannesburg'),
(4,'Nothern Cape','Kimberly'),
(7,'Western cape','Bellville'),
(2,'Western Cape','George'),
(5,'Western cape','Mossel bay'),
(3,'Western Cape','Paarl'),
(1,'Western Cape','Stellenbosch');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `time` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `conversation_id` int(11) NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `conversation_id` (`conversation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES
(1,999,1000,'Hello there','2025-05-05 20:55:38',0,3),
(2,999,999,'Hello there','2025-05-05 21:11:48',0,9),
(3,999,999,'John i need that phone','2025-05-05 21:14:44',0,9);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `price`) STORED,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES
(1,1,1002,10,10000.00,100000.00),
(2,2,1002,10,10000.00,100000.00),
(3,3,1002,10,10000.00,100000.00),
(4,4,1002,10,10000.00,100000.00),
(6,6,1002,10,10000.00,100000.00),
(7,7,1002,10,10000.00,100000.00),
(8,8,1002,10,10000.00,100000.00),
(9,9,1002,10,10000.00,100000.00),
(10,10,1002,10,10000.00,100000.00),
(11,11,1003,10,9800.00,98000.00),
(12,11,1009,9,20000.00,180000.00),
(13,12,1001,2,7999.00,15998.00),
(14,12,1002,10,10000.00,100000.00),
(15,12,1005,4,231000.00,924000.00),
(16,13,1002,1,10000.00,10000.00),
(17,13,1003,1,9800.00,9800.00),
(18,14,1002,1,10000.00,10000.00),
(19,14,1005,1,231000.00,231000.00),
(20,14,1009,1,20000.00,20000.00),
(21,15,1002,1,10000.00,10000.00),
(22,15,1011,1,3000.00,3000.00),
(23,16,1012,1,78.99,78.99),
(24,17,1015,1,8500.00,8500.00),
(25,18,1024,1,850.00,850.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(1,999,'paid',100000.00,'2025-05-21 21:06:11'),
(2,999,'paid',100000.00,'2025-05-21 21:06:18'),
(3,999,'pending',100000.00,'2025-05-21 21:06:23'),
(4,999,'pending',100000.00,'2025-05-21 21:06:38'),
(6,999,'pending',100000.00,'2025-05-21 21:08:34'),
(7,999,'pending',100000.00,'2025-05-21 21:09:26'),
(8,999,'pending',100000.00,'2025-05-21 21:09:52'),
(9,999,'pending',100000.00,'2025-05-21 21:11:33'),
(10,999,'pending',100000.00,'2025-05-21 21:12:18'),
(11,999,'pending',278000.00,'2025-05-25 15:42:17'),
(12,999,'paid',1039998.00,'2025-05-25 15:44:24'),
(13,999,'paid',19800.00,'2025-05-25 16:04:16'),
(14,999,'paid',261000.00,'2025-05-25 16:22:52'),
(15,999,'pending',13000.00,'2025-05-25 22:28:50'),
(16,999,'paid',78.99,'2025-05-27 16:16:29'),
(17,1010,'paid',8500.00,'2025-06-05 14:51:44'),
(18,1010,'pending',850.00,'2025-06-05 15:03:18');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_at` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'success',
  PRIMARY KEY (`payment_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(2,1,'card',100000.00,'2025-05-23 16:44:24','success'),
(3,14,'card',261000.00,'2025-05-25 18:32:38','success'),
(4,2,'card',100000.00,'2025-05-25 18:37:18','success'),
(5,13,'card',19800.00,'2025-05-25 18:38:53','success'),
(6,12,'card',1039998.00,'2025-05-25 18:41:35','success'),
(7,16,'card',78.99,'2025-05-27 16:16:44','success'),
(8,17,'card',8500.00,'2025-06-05 14:51:54','success');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `listing_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`report_id`),
  KEY `listing_id` (`listing_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report`
--

LOCK TABLES `report` WRITE;
/*!40000 ALTER TABLE `report` DISABLE KEYS */;
INSERT INTO `report` VALUES
(1,1003,999,'This is a false ad','2025-05-24 09:24:51'),
(2,1009,999,'No images, very bad seller practice','2025-05-24 12:51:10'),
(3,1011,999,'The team is a lie','2025-05-25 17:46:31'),
(4,1003,999,'This is a false ad','2025-05-25 20:21:24');
/*!40000 ALTER TABLE `report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `score` tinyint(4) NOT NULL CHECK (`score` >= 1 and `score` <= 5),
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`review_id`),
  UNIQUE KEY `user_id` (`user_id`,`listing_id`),
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES
(5,999,1002,1,'Bad product boooooo!','2025-05-25 11:46:16'),
(14,999,1009,5,'The car is real','2025-05-27 14:44:22'),
(22,1010,1015,3,'As description says, better than images, however charger was broken.','2025-06-05 12:53:05');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sellers`
--

DROP TABLE IF EXISTS `sellers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sellers` (
  `seller_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `verification_status` enum('unverified','verified') NOT NULL DEFAULT 'unverified',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`seller_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sellers`
--

LOCK TABLES `sellers` WRITE;
/*!40000 ALTER TABLE `sellers` DISABLE KEYS */;
INSERT INTO `sellers` VALUES
(1,999,'verified','2025-05-02 17:22:22'),
(2,1001,'unverified','2025-05-22 18:02:51'),
(3,1009,'unverified','2025-06-05 11:03:13'),
(4,1010,'unverified','2025-06-05 12:43:44');
/*!40000 ALTER TABLE `sellers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tokens` (
  `token` char(16) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`token`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
INSERT INTO `tokens` VALUES
('15073e0416fa5dd2',1006,'2025-05-26 12:26:40','2025-05-26 12:56:40'),
('2dc53f7aa1a20dbb',1009,'2025-06-05 10:59:42','2025-06-05 09:29:42'),
('56744ff411e76c21',1008,'2025-06-05 10:06:36','2025-06-05 08:36:36'),
('6592f02fd0b17ef0',999,'2025-05-03 13:45:53','2025-05-03 14:15:53'),
('842297cced67c865',1001,'2025-05-22 17:58:54','2025-05-22 18:28:54'),
('add3cd41424f680b',1010,'2025-06-05 12:37:44','2025-06-05 11:07:44'),
('cb563a68aa5e0014',1007,'2025-06-05 09:56:40','2025-06-05 08:26:40');
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_auth`
--

DROP TABLE IF EXISTS `user_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_auth` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `salt` char(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`auth_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_auth`
--

LOCK TABLES `user_auth` WRITE;
/*!40000 ALTER TABLE `user_auth` DISABLE KEYS */;
INSERT INTO `user_auth` VALUES
(1,999,'b92757eff365a560c28623a0d0bed281c8b4721b0fdb48de2411f78bee6ae4c6','3de320b0d9369a792930dc5504e2dd19','2025-05-20 19:37:49'),
(2,1001,'3a1af00b81fa8cca4702d3a40e7b2dc01181bd6cf5e06dd9642790eaf73e3247','6e00156071d294b58facc3949fef0566','2025-05-22 17:58:03'),
(3,1002,'a0c7a3f721815225650808be1e2157c99fe9bb25ae497578d4851350853d3a2c','38cd8d9b28a3598e828d26b294370ff8','2025-05-26 11:49:30'),
(4,1003,'3bc4941ba3e4ab2e77ff22be0882d0c0bf8b0dbf54a2b396fa879bc16d793424','1c21bb53542a7918c61198c86c870a95','2025-05-26 12:19:41'),
(5,1004,'bdfe89745957d667a63b3bb95dc4997453b1c01a002e9d902179ac0143db928f','7ee62376b03f66f5216233ea9c1ea491','2025-05-26 12:21:36'),
(6,1005,'542e105e56ac778ef261fe291790a6deb4767c32e084092e4d11bd2f225c1adc','05eb910ad91eb4f87f251262c4316b91','2025-05-26 12:23:10'),
(7,1006,'e309c787c8a3d360da7db83b7fe4aec98ff042078f7beb2f9fb5aa14763e2560','aa9b0058b71c960da4a63572be069375','2025-05-26 12:26:18'),
(8,1007,'bf8d25914524b3b866a05e9a56852a4095330c7749830b51eeee2977585be346','ec753966a822b65325f7412ffd2b7c00','2025-06-05 09:55:53'),
(9,1008,'60d115ce122a6f0b72e5bb93ed7f5069e6380f75ec30876a0aa7be1a15a3e371','04c532abaf6d600b98465163177ca48e','2025-06-05 10:06:28'),
(10,1009,'004f24212277f3f1da197c243b388675d8384cdeb9d0220f26b83e07c94f991a','9d26b80f82624acdee4bac5de81f3465','2025-06-05 10:59:23'),
(11,1010,'cb0556abaea628b8ba79bab08f31ffa2342ad04d016a7e652cb7f9600d59210f','16ed9bd010f19ea021772ae82e60fb48','2025-06-05 12:37:38');
/*!40000 ALTER TABLE `user_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1011 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(999,'testname','test@email.com','1111111111','media/66dc1cac06af99fa'),
(1000,'John Doe','john@example.com','0812345678',NULL),
(1008,'AdminMain','adminmain@email.com','0810000000',NULL),
(1009,'ConorSells','conorsell@email.com','0811312313',NULL),
(1010,'JeffRebuy','bundy@email.com','0611312313',NULL);
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

-- Dump completed on 2025-06-10 19:19:59
