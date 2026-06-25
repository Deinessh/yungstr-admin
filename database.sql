-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: s7millet
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (8,'Single Product Packs','single-product-packs','Individual 200g millet breakfast mixes — dosa and idly varieties.','images/category-dosa.png','2026-05-28 09:04:51','2026-05-28 09:04:51'),(9,'Dosa Combo Packs','dosa-combo-packs','Curated dosa mix bundles at special combo prices.','images/category-dosa.png','2026-05-28 09:04:51','2026-05-28 09:04:51'),(10,'Idly Combo Packs','idly-combo-packs','Soft, healthy millet idly combos for the whole family.','images/category-idli.png','2026-05-28 09:04:51','2026-05-28 09:04:51'),(11,'Breakfast Combo Packs','breakfast-combo-packs','Mixed dosa and idly breakfast bundles for variety every morning.','images/category-combo.png','2026-05-28 09:04:51','2026-05-28 09:04:51'),(12,'Pick Any 3 Combo','pick-any-3-combo','Choose any 3 breakfast mixes from our range at one special price.','images/category-combo.png','2026-05-28 09:04:51','2026-05-28 09:04:51'),(13,'Ultimate Family Box','ultimate-family-box','Multi Millet Dosa -200g\r\nFoxtail Millet Dosa - 200g\r\nMoong Dal Pesarattu - 200g\r\nRagi Idly - 200g\r\nMulti Millet Idly - 200g',NULL,'2026-06-06 11:11:46','2026-06-06 11:11:46');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checkout_drafts`
--

DROP TABLE IF EXISTS `checkout_drafts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checkout_drafts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `cart_data` json NOT NULL,
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `shipping_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_pincode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `customer_notes` text COLLATE utf8mb4_unicode_ci,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checkout_drafts_user_id_foreign` (`user_id`),
  CONSTRAINT `checkout_drafts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checkout_drafts`
--

LOCK TABLES `checkout_drafts` WRITE;
/*!40000 ALTER TABLE `checkout_drafts` DISABLE KEYS */;
INSERT INTO `checkout_drafts` VALUES (4,3,'[]','2-38, RAIKODE MANDAL\r\nRAIKODE','Ram','Zaheerabad','Telangana','502257','2026-06-03',NULL,'9533677041',NULL,'S7UJX01','cod','2026-05-30 14:39:47','2026-05-30 14:39:47'),(5,5,'[]','1-62, Gomptalli Street, Boddavaram Village, Kottananduru Mandal, Kakinada District','vegi kumar','Kakinada','Andhra Pradesh','533401',NULL,NULL,'09866139213',NULL,NULL,'cod','2026-06-01 06:33:30','2026-06-01 06:33:30');
/*!40000 ALTER TABLE `checkout_drafts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_submissions`
--

DROP TABLE IF EXISTS `contact_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_submissions`
--

LOCK TABLES `contact_submissions` WRITE;
/*!40000 ALTER TABLE `contact_submissions` DISABLE KEYS */;
INSERT INTO `contact_submissions` VALUES (1,'Joanna Riggs','joriggsvideo3@gmail.com','912448450','Converting more visitors on s7milletco.com','Hi,\r\n\r\nI just visited s7milletco.com and wondered if you\'d ever thought about having an engaging video to explain what you do?\r\n\r\nOur prices start from just $195 (USD).\r\n\r\nLet me know if you\'re interested in seeing samples of our previous work.\r\n\r\nRegards,\r\nJoanna\r\n\r\nUnsubscribe: https://unsubscribe.video/unsubscribe.php?d=s7milletco.com',0,'2026-06-14 13:07:07','2026-06-14 13:07:07'),(2,'Aja Bixby','domains@search-s7milletco.com','317705917','Results for s7milletco.com','Hey\r\n\r\nSubmit s7milletco.com in GoogleSearchIndex and have it appear in google search results!\r\n\r\nAdd s7milletco.com now: https://searchregister.net',0,'2026-06-14 22:03:15','2026-06-14 22:03:15'),(3,'Gemma Marshall','gemma.marshall112@gmail.com','363074759','Question for the team @ s7milletco.com','Hi,\r\n\r\nI was just looking at s7milletco.com and wanted to ask: are you looking to scale your Instagram presence right now?\r\n\r\nWe help brands like yours add 300+ targeted Instagram followers every month using manual outreach and ads. We can grow your existing page or even build a brand-new profile from scratch for you if you\'d prefer a fresh start.\r\n\r\nWould you like me to send over some more info on how it works?\r\n\r\nThanks for your time,\r\nGemma',0,'2026-06-16 17:53:10','2026-06-16 17:53:10');
/*!40000 ALTER TABLE `contact_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_usages`
--

DROP TABLE IF EXISTS `coupon_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_usages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_usages_coupon_id_foreign` (`coupon_id`),
  KEY `coupon_usages_user_id_foreign` (`user_id`),
  KEY `coupon_usages_order_id_foreign` (`order_id`),
  CONSTRAINT `coupon_usages_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coupon_usages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coupon_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_usages`
--

LOCK TABLES `coupon_usages` WRITE;
/*!40000 ALTER TABLE `coupon_usages` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupon_usages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_uses` int unsigned DEFAULT NULL,
  `used_count` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `is_referral_reward` tinyint(1) NOT NULL DEFAULT '0',
  `is_personal_referral` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`),
  KEY `coupons_user_id_foreign` (`user_id`),
  CONSTRAINT `coupons_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'S710','S7 Welcome','percent',10.00,499.00,100,1,1,1,0,0,NULL,NULL,'2026-05-26 08:12:08','2026-06-06 11:44:04'),(2,'HEALTHY10','Healthy Start 10% Off','percent',10.00,299.00,200,0,0,1,0,0,NULL,NULL,'2026-05-26 08:12:08','2026-06-06 11:46:17'),(3,'FLAT100','Flat ₹100 Off','fixed',100.00,499.00,50,0,0,1,0,0,NULL,NULL,'2026-05-26 08:12:08','2026-06-06 11:46:43');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hero_slides`
--

DROP TABLE IF EXISTS `hero_slides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hero_slides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hero_slides`
--

LOCK TABLES `hero_slides` WRITE;
/*!40000 ALTER TABLE `hero_slides` DISABLE KEYS */;
INSERT INTO `hero_slides` VALUES (12,'Shop Healthy Millet Dosa Combos','Save more with curated dosa combo packs','View Dosa Combos','/products?category=dosa-combo-packs','images/hero/1000568175-1781446513.png',NULL,1,1,'2026-05-29 11:42:15','2026-06-14 14:15:13'),(13,'Soft & Healthy Millet Idly Mixes','Fluffy idlis packed with millet goodness','View Idly Combos','/products?category=idly-combo-packs','images/hero/1000568176-1781446523.png',NULL,2,1,'2026-05-29 11:42:15','2026-06-14 14:15:23');
/*!40000 ALTER TABLE `hero_slides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_21_074232_create_categories_table',1),(5,'2026_05_21_074233_create_orders_table',1),(6,'2026_05_21_074233_create_products_table',1),(7,'2026_05_21_074234_create_order_items_table',1),(8,'2026_05_25_120000_add_commerce_features',2),(9,'2026_05_25_130000_add_is_system_to_coupons_table',3),(10,'2026_05_26_100000_add_media_slides_referral_mail',4),(11,'2026_05_26_110000_make_product_stock_nullable',5),(12,'2026_05_26_120000_add_reference_mockup_features',6),(13,'2026_05_27_100000_add_shipping_invoice_store_settings',7),(14,'2026_05_27_120000_create_promo_banners_and_update_shipping_defaults',8),(15,'2026_05_27_130000_add_product_plan_and_order_details',9),(16,'2026_05_25_140000_add_pick_any_selections_to_order_items',10),(17,'2026_05_29_100000_add_shipping_zones_combo_items_and_checkout_fields',11),(18,'2026_05_30_120000_make_user_email_nullable_and_disable_delivery_date',12),(19,'2026_05_30_130000_drop_shipping_zones_priority',13),(20,'2026_05_30_140000_fix_andhra_pradesh_shipping_zone_values',14),(21,'2026_05_30_160000_update_home_how_it_works_icons',15),(22,'2026_06_01_120000_add_cashfree_and_payment_settings',16),(23,'2026_06_01_140000_add_is_coming_soon_to_products',17);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_weight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `combo_includes` text COLLATE utf8mb4_unicode_ci,
  `pick_any_selections` json DEFAULT NULL,
  `mrp` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_zone_id` bigint unsigned DEFAULT NULL,
  `shipping_zone_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `razorpay_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_payment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cashfree_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cashfree_payment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon_id` bigint unsigned DEFAULT NULL,
  `coupon_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_code_used` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cart_snapshot` json DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shipping_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_pincode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `awb_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `velocity_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `velocity_shipment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label_url` text COLLATE utf8mb4_unicode_ci,
  `tracking_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shipping_error` text COLLATE utf8mb4_unicode_ci,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoiced_at` timestamp NULL DEFAULT NULL,
  `confirmation_sent_at` timestamp NULL DEFAULT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `customer_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_coupon_id_foreign` (`coupon_id`),
  KEY `orders_shipping_zone_id_foreign` (`shipping_zone_id`),
  CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_shipping_zone_id_foreign` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_combo_items`
--

DROP TABLE IF EXISTS `product_combo_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_combo_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `combo_product_id` bigint unsigned NOT NULL,
  `included_product_id` bigint unsigned NOT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_combo_items_combo_product_id_included_product_id_unique` (`combo_product_id`,`included_product_id`),
  KEY `product_combo_items_included_product_id_foreign` (`included_product_id`),
  CONSTRAINT `product_combo_items_combo_product_id_foreign` FOREIGN KEY (`combo_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_combo_items_included_product_id_foreign` FOREIGN KEY (`included_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_combo_items`
--

LOCK TABLES `product_combo_items` WRITE;
/*!40000 ALTER TABLE `product_combo_items` DISABLE KEYS */;
INSERT INTO `product_combo_items` VALUES (7,19,29,2,NULL,NULL),(9,20,29,2,NULL,NULL),(12,21,29,3,NULL,NULL),(18,24,29,2,NULL,NULL),(21,25,29,3,NULL,NULL),(24,26,29,3,NULL,NULL),(32,30,29,3,NULL,NULL),(33,30,31,2,NULL,NULL),(34,34,32,1,NULL,NULL);
/*!40000 ALTER TABLE `product_combo_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (1,29,'images/products/1000681615-1780378405.png',1,'2026-06-02 05:33:25','2026-06-02 05:33:25'),(2,29,'images/products/1000681618-1780378405.png',2,'2026-06-02 05:33:25','2026-06-02 05:33:25'),(3,29,'images/products/1000681619-1780378405.png',3,'2026-06-02 05:33:25','2026-06-02 05:33:25'),(4,29,'images/products/1000681620-1780378405.png',4,'2026-06-02 05:33:25','2026-06-02 05:33:25'),(5,29,'images/products/1000682698-1780378405.png',5,'2026-06-02 05:33:25','2026-06-02 05:33:25'),(6,31,'images/products/1000689633-1780971659.jpg',1,'2026-06-09 02:20:59','2026-06-09 02:20:59'),(7,31,'images/products/1000689634-1780971659.jpg',2,'2026-06-09 02:20:59','2026-06-09 02:20:59'),(8,31,'images/products/1000689635-1780971659.jpg',3,'2026-06-09 02:20:59','2026-06-09 02:20:59'),(9,31,'images/products/1000689636-1780971659.jpg',4,'2026-06-09 02:20:59','2026-06-09 02:20:59'),(10,31,'images/products/1000689638-1780971659.png',5,'2026-06-09 02:20:59','2026-06-09 02:20:59'),(11,33,'images/products/1000696538-1781338459.png',1,'2026-06-13 08:14:19','2026-06-13 08:14:19'),(12,33,'images/products/1000696543-1781338459.png',2,'2026-06-13 08:14:19','2026-06-13 08:14:19'),(13,33,'images/products/1000696544-1781338459.png',3,'2026-06-13 08:14:19','2026-06-13 08:14:19'),(14,33,'images/products/1000696552-1781338459.png',4,'2026-06-13 08:14:19','2026-06-13 08:14:19'),(15,33,'images/products/1000696566-1781338459.png',5,'2026-06-13 08:14:19','2026-06-13 08:14:19'),(16,34,'images/products/1000696601-1781339337.png',1,'2026-06-13 08:28:57','2026-06-13 08:28:57'),(17,34,'images/products/1000696622-1781339337.png',2,'2026-06-13 08:28:57','2026-06-13 08:28:57'),(18,34,'images/products/1000696623-1781339337.png',3,'2026-06-13 08:28:57','2026-06-13 08:28:57'),(19,34,'images/products/1000696630-1781339337.png',4,'2026-06-13 08:28:57','2026-06-13 08:28:57'),(20,34,'images/products/1000696631-1781339337.png',5,'2026-06-13 08:28:57','2026-06-13 08:28:57'),(26,32,'images/products/1000577122-1781716749.jpg',2,'2026-06-17 17:19:09','2026-06-17 17:19:09'),(27,32,'images/products/1000577123-1781716749.jpg',3,'2026-06-17 17:19:09','2026-06-17 17:19:09'),(28,32,'images/products/1000577124-1781716749.jpg',4,'2026-06-17 17:19:09','2026-06-17 17:19:09'),(29,32,'images/products/1000577125-1781716749.jpg',5,'2026-06-17 17:19:09','2026-06-17 17:19:09'),(30,32,'images/products/1000577126-1781716749.jpg',6,'2026-06-17 17:19:09','2026-06-17 17:19:09');
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `combo_includes` text COLLATE utf8mb4_unicode_ci,
  `benefit_tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_best_seller` tinyint(1) NOT NULL DEFAULT '0',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0',
  `is_pick_any_combo` tinyint(1) NOT NULL DEFAULT '0',
  `featured_sort` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_coming_soon` tinyint(1) NOT NULL DEFAULT '0',
  `weight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_kg` decimal(8,3) DEFAULT NULL,
  `key_benefits` json DEFAULT NULL,
  `nutrition_info` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (19,9,'Healthy Millet Dosa Combo','healthy-millet-dosa-combo','S7-COMBO-DOSA-01','Two favourite dosa mixes bundled together for everyday healthy breakfasts.','Multi Millet Dosa Mix – 200g + Foxtail Millet Dosa Mix – 200g','Best Value',379.00,498.00,101,'images/products/combo-pack.png',NULL,1,0,0,0,6,1,0,'400g (2 × 200g)',0.400,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-06-06 11:05:42'),(20,9,'Protein Rich Dosa Combo','protein-rich-dosa-combo','S7-COMBO-DOSA-02','Power-packed dosa combo with moong dal and multi millet mixes.','Moong Dal Dosa Mix – 200g + Multi Millet Dosa Mix – 200g','High Protein',379.00,498.00,100,'images/products/combo-pack.png',NULL,1,0,0,0,7,1,0,'400g (2 × 200g)',0.400,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-06-06 11:06:09'),(21,9,'Complete Dosa Breakfast Box','complete-dosa-breakfast-box','S7-COMBO-DOSA-03','All three dosa varieties in one value box for variety every morning.','Multi Millet Dosa Mix – 200g + Moong Dal Dosa Mix – 200g + Foxtail Millet Dosa Mix – 200g','Family Pack',549.00,747.00,100,'images/products/combo-pack.png',NULL,1,1,0,0,8,1,0,'600g (3 × 200g)',0.600,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-06-06 11:07:17'),(22,10,'Soft & Healthy Millet Idly Combo','soft-healthy-millet-idly-combo','S7-COMBO-IDLI-01','Ragi and multi millet idly mixes for soft, nutritious steamed breakfasts.','Ragi Idly Mix – 200g + Multi Millet Idly Mix – 200g','Best Value',359.00,458.00,101,'images/products/combo-pack.png',NULL,1,0,0,0,9,1,0,'400g (2 × 200g)',0.400,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-06-06 11:06:49'),(23,10,'Family Millet Idly Pack','family-millet-idly-pack','S7-COMBO-IDLI-02','Stock up for the whole family with four idly mixes at a bundle price.','2 × Ragi Idly Mix – 200g + 2 × Multi Millet Idly Mix – 200g','Family Pack',529.00,669.00,100,'images/products/combo-pack.png',NULL,0,0,0,0,0,1,0,'800g (4 × 200g)',0.800,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-05-30 14:07:16'),(24,11,'Breakfast Starter Combo','breakfast-starter-combo','S7-COMBO-BF-01','Try both dosa and idly — perfect for new customers exploring S7 MilletCo.','Multi Millet Dosa Mix – 200g + Ragi Idly Mix – 200g','Starter Pack',279.00,349.00,100,'images/products/combo-pack.png',NULL,1,0,0,0,10,1,0,'400g (2 × 200g)',0.400,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-05-30 14:07:38'),(25,11,'Complete Millet Breakfast Combo','complete-millet-breakfast-combo','S7-COMBO-BF-02','Three bestsellers — two dosa mixes and one idly mix for complete variety.','Multi Millet Dosa Mix – 200g + Foxtail Millet Dosa Mix – 200g + Multi Millet Idly Mix – 200g','Best Value',548.97,727.00,100,'images/products/combo-pack.png',NULL,1,1,0,0,11,1,0,'600g (3 × 200g)',0.600,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-06-06 11:07:50'),(26,11,'High Protein Breakfast Combo','high-protein-breakfast-combo','S7-COMBO-BF-03','Protein-focused breakfast bundle with moong dal dosa and millet idly mixes.','Moong Dal Dosa Mix – 200g + Multi Millet Dosa Mix – 200g + Ragi Idly Mix – 200g','High Protein',568.98,747.00,100,'images/products/combo-pack.png',NULL,1,0,0,0,12,1,0,'600g (3 × 200g)',0.600,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-06-06 11:08:10'),(27,11,'100% Gluten Free Breakfast Combo','100-gluten-free-breakfast-combo','S7-COMBO-BF-04','Gluten free • No maida • Ready in minutes. A wholesome trio of millet breakfast mixes.','Foxtail Millet Dosa Mix – 200g + Ragi Idly Mix – 200g + Multi Millet Idly Mix – 200g','Gluten Free',549.00,707.00,100,'images/products/combo-pack.png',NULL,1,0,0,0,13,1,0,'600g (3 × 200g)',0.600,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-06-06 11:08:29'),(28,12,'Pick Any 3 Breakfast Mixes','pick-any-3-breakfast-mixes','S7-COMBO-PICK3','Choose any 3 breakfast mixes from our single product range at one special combo price. Select your 3 products on the product page or at checkout.','Customer selects any 3 single breakfast mixes (200g each)','Your Choice',549.00,747.00,100,'images/products/combo-pack.png',NULL,1,1,1,1,14,1,0,'600g (3 × 200g)',0.600,'[\"Made with real millets\", \"No maida, no preservatives\", \"Smart nutrition every day\"]',NULL,'2026-05-28 09:04:51','2026-06-06 11:09:10'),(29,8,'Multi Millet Dosa Mix | Instant Dry Premix | Just Add Water | Gluten Free | No Fermentation | Healthy Breakfast Mix | Ready in Minutes | 200g','multi-millet-dosa-mix-instant-dry-premix-just-add-water-gluten-free-no-fermentation-healthy-breakfast-mix-ready-in-minutes-200g',NULL,'Enjoy the goodness of traditional millets with S7 MilletCo Multi Millet Dosa Mix, a healthy and convenient way to prepare delicious, crispy dosas at home. Made from a nutritious blend of premium millets, pulses, and grains, this instant dry premix brings together authentic South Indian taste and modern convenience.\r\nSimply add water, mix the batter, and cook—no soaking, grinding, fermentation, or refrigeration required. Rich in dietary fiber and a good source of protein, this dosa mix helps you enjoy a wholesome breakfast or light meal without compromising on taste.\r\nWhether you\'re a busy professional, a health-conscious individual, or looking for a nutritious option for your family, S7 MilletCo Multi Millet Dosa Mix is the perfect choice for quick, tasty, and nourishing meals',NULL,'Rich in Millet Nutrition. Instant & Easy to Prepare',199.00,249.00,NULL,'images/products/1000681614-1780378405.png',NULL,1,1,1,0,0,1,0,NULL,0.500,'[\"Multi Millet Nutrition - Made with the goodness of Foxtail Millet, Jowar & Little Millet.\", \"Just Add Water - Quick and hassle-free preparation\", \"No Fermentation Required - Enjoy fresh dosas without waiting for hours.\", \"Gluten Free - A healthier choice for everyday meals.\"]',NULL,'2026-06-02 05:33:25','2026-06-09 12:30:35'),(30,13,'Ultimate Family Box','ultimate-family-box',NULL,'Complete Family Back(All Packs Included)',NULL,'Family Pack',899.00,1245.00,NULL,NULL,NULL,0,0,0,0,0,1,0,'1000g ( 5 * 200 g)',1.000,NULL,NULL,'2026-06-06 11:15:06','2026-06-06 11:15:06'),(31,8,'Moong Dal Pesarattu Mix | Instant Dry Premix | Just Add Water | Gluten Free | Ready in Minutes | 200g','moong-dal-pesarattu-mix-instant-dry-premix-just-add-water-gluten-free-ready-in-minutes-200g',NULL,'Enjoy the authentic taste of South Indian Pesarattu with S7 MilletCo Moong Dal Pesarattu Mix. Made from carefully selected ingredients, this instant dry premix helps you prepare delicious, crispy, and nutritious pesarattu in minutes. Simply add water, mix, and cook—no soaking, grinding, or fermentation required. Rich in protein and fiber, this gluten-free mix is a healthy choice for breakfast, snacks, or light meals. Convenient, travel-friendly, and easy to store, it brings traditional homemade goodness to your table with minimal effort.\r\nKey Features: ✔ Just Add Water & Cook\r\n✔ No Soaking, Grinding or Fermentation\r\n✔ Gluten Free Formula\r\n✔ High Protein & High Fiber\r\n✔ Ready in Minutes\r\n✔ Easy to Digest\r\n✔ Makes Up to 8 Dosas\r\n✔ No Refrigeration Required\r\n✔ Travel Friendly Packaging\r\n✔ Authentic South Indian Taste\r\nNet Weight: 200g\r\nIdeal For: Breakfast, Evening Snacks & Healthy Meals.',NULL,'Healthy, Protein-Rich & Gluten-Free Instant Pesarattu Mix',199.00,249.00,NULL,NULL,NULL,0,0,0,0,0,1,0,'200g',0.500,'[\"High Protein – Helps support muscle health and keeps you energized.\", \"High Fiber – Supports healthy digestion and gut health.\", \"Gluten Free – Suitable for gluten-conscious diets.\", \"Just Add Water – No soaking, grinding, or complicated preparation\", \"Ready in Minutes – Quick and convenient meal option\", \"Makes Up to 8 Dosas – One pack serves multiple portions.\", \"No Added Preservatives – Made with carefully selected ingredients.\", \"Family-Friendly Food – Suitable for all age groups.\"]','[{\"label\": \"Energy\", \"value\": \"350 kcal\"}, {\"label\": \"Protein\", \"value\": \"22 g\"}, {\"label\": \"Carbohydrates\", \"value\": \"58 g\"}, {\"label\": \"Dietary Fiber\", \"value\": \"10 g\"}, {\"label\": \"Total Fat\", \"value\": \"3 g\"}, {\"label\": \"Sugar\", \"value\": \"2 g\"}, {\"label\": \"Sodium\", \"value\": \"450 mg\"}]','2026-06-09 02:20:59','2026-06-09 12:30:25'),(32,8,'Foxtail Millet Dosa Mix | Instant Dry Premix | Just Add Water | 200g','foxtail-millet-dosa-mix-instant-dry-premix-just-add-water-200g',NULL,'Enjoy the authentic taste of crispy South Indian dosas with the goodness of Foxtail Millet. S7 MilletCo Foxtail Millet Dosa Mix is a healthy and convenient instant premix specially crafted for modern lifestyles. Simply add water, prepare the batter, and make delicious dosas in minutes—no soaking, grinding, fermentation, or refrigeration required.\r\nMade with carefully selected ingredients including Foxtail Millet, Rice Flour, Black Gram Flour, and Fenugreek, this nutritious mix helps you enjoy a wholesome breakfast or snack without the hassle of traditional preparation.',NULL,'Foxtail Millet Goodness | Rich in Fiber | Gluten Free | Just Add Water | Ready in Minutes',189.00,229.00,NULL,'images/products/1-1781698213.png',NULL,0,0,0,0,0,1,0,'200g',0.500,'[\"•  Foxtail Millet Based Nutrition\", \"• Just Add Water & Cook\", \"• No Fermentation Required\", \"• Gluten Free\", \"• Makes Up To 8 Dosas\"]',NULL,'2026-06-13 08:00:35','2026-06-17 12:10:13'),(33,8,'Ragi Millet Idly Mix | Instant Dry Premix | Just Add Water | 200g','ragi-millet-idly-mix-instant-dry-premix-just-add-water-200g',NULL,'Enjoy soft, delicious and nutritious idlis with the goodness of Ragi Millet. This instant idly premix is specially crafted for busy lifestyles, allowing you to prepare fluffy idlis in minutes without soaking, grinding or fermentation.\r\nMade with Ragi Millet and carefully selected ingredients, this mix offers a convenient way to add millet-based nutrition to your daily breakfast routine.',NULL,'Ragi Millet Goodness | Rich in Calcium & Fiber | Gluten Free | Ready in Minutes',189.00,229.00,NULL,'images/products/1000696568-1781338459.png',NULL,0,0,0,0,0,1,0,'200G',0.500,'[\"• Made with Nutritious Ragi Millet\", \"• Rich in Calcium & Dietary Fiber\", \"• Good Source of Protein\", \"• No Fermentation Required\", \"• Smart Nutrition for the Whole Family\"]',NULL,'2026-06-13 08:14:19','2026-06-17 12:06:45'),(34,8,'Multi Millet Idly Mix | Instant Dry Premix | Just Add Water | 200g','multi-millet-idly-mix-instant-dry-premix-just-add-water-200g',NULL,'Enjoy soft, fluffy and nutritious idlis made with the goodness of multiple millets. S7 MilletCo Multi Millet Idly Mix combines carefully selected millet grains to deliver a wholesome breakfast that is both convenient and delicious.\r\nDesigned for modern lifestyles, this instant premix helps you prepare tasty idlis in minutes without soaking, grinding, or fermentation. Simply add water, mix, steam and enjoy.',NULL,'Multi Millet Goodness | Rich in Fiber | Plant Based | Ready in Minutes | Just Add Water',189.00,229.00,NULL,'images/products/1000696597-1781339337.png',NULL,0,0,0,0,0,1,0,'200g',0.500,'[\"✔ Made with Multiple Nutritious Millets\", \"✔ Rich in Dietary Fiber\", \"✔ Good Source of Protein\", \"✔ Supports Healthy Digestion\", \"✔ Plant-Based Nutrition\", \"✔ Easy to Prepare\", \"✔ No Fermentation Required\", \"✔ Light & Filling Breakfast Option\", \"✔ Suitable for Daily Consumption\", \"✔ Smart Nutrition for the Whole Family\"]',NULL,'2026-06-13 08:28:57','2026-06-17 12:06:35');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo_banners`
--

DROP TABLE IF EXISTS `promo_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promo_banners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo_banners`
--

LOCK TABLES `promo_banners` WRITE;
/*!40000 ALTER TABLE `promo_banners` DISABLE KEYS */;
INSERT INTO `promo_banners` VALUES (7,'Pick Any 3 Breakfast Mixes @ ₹549','images/hero/banner-2.png','/products/pick-any-3-breakfast-mixes',0,1,'2026-05-29 11:42:15','2026-06-06 11:15:43'),(8,'High Protein Millet Breakfast Combos','images/hero/banner-1.png','/products?category=breakfast-combo-packs',1,1,'2026-05-29 11:42:15','2026-05-29 11:42:15'),(9,'Gluten Free • No Maida • Ready in Minutes','images/hero/banner-3.png','/products/100-gluten-free-breakfast-combo',2,1,'2026-05-29 11:42:15','2026-05-29 11:42:15');
/*!40000 ALTER TABLE `promo_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referrals`
--

DROP TABLE IF EXISTS `referrals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `referrals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `referrer_id` bigint unsigned NOT NULL,
  `referred_user_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `referrals_referrer_id_foreign` (`referrer_id`),
  KEY `referrals_referred_user_id_foreign` (`referred_user_id`),
  KEY `referrals_order_id_foreign` (`order_id`),
  CONSTRAINT `referrals_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `referrals_referred_user_id_foreign` FOREIGN KEY (`referred_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `referrals_referrer_id_foreign` FOREIGN KEY (`referrer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referrals`
--

LOCK TABLES `referrals` WRITE;
/*!40000 ALTER TABLE `referrals` DISABLE KEYS */;
INSERT INTO `referrals` VALUES (1,5,6,NULL,'completed','2026-06-01 06:35:28','2026-06-01 06:35:28','2026-06-01 06:35:28'),(2,5,7,NULL,'completed','2026-06-01 06:36:06','2026-06-01 06:36:06','2026-06-01 06:36:06'),(3,5,8,NULL,'completed','2026-06-01 06:37:14','2026-06-01 06:37:14','2026-06-01 06:37:14');
/*!40000 ALTER TABLE `referrals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'cod_enabled','1','2026-05-26 07:52:03','2026-05-26 07:52:03'),(2,'free_shipping_threshold','399','2026-05-28 08:57:59','2026-05-28 08:57:59'),(3,'shipping_fee','29','2026-05-28 08:57:59','2026-05-28 08:57:59'),(4,'referral_friend_discount','50','2026-05-26 07:52:03','2026-05-26 07:52:03'),(5,'referral_reward_discount','100','2026-05-26 07:52:03','2026-05-26 07:52:03'),(6,'referrals_required_to_unlock','3','2026-05-26 07:52:03','2026-05-26 07:52:03'),(7,'razorpay_key_id','','2026-05-26 07:52:03','2026-05-26 07:52:03'),(8,'razorpay_key_secret','','2026-05-26 07:52:03','2026-05-26 07:52:03'),(9,'referral_reward_type','fixed','2026-05-26 09:22:35','2026-05-26 09:22:35'),(10,'referral_reward_expiry_days','7','2026-05-26 09:22:35','2026-05-26 09:22:35'),(11,'referral_code_counter','2','2026-05-26 09:22:35','2026-06-01 06:33:30'),(12,'mail_mailer','smtp','2026-05-26 09:22:35','2026-05-26 09:22:35'),(13,'mail_host','imap.hostinger.com','2026-05-26 09:22:35','2026-06-04 06:32:36'),(14,'mail_port','587','2026-05-26 09:22:35','2026-05-26 09:22:35'),(15,'mail_username','hello@s7milletco.com','2026-05-26 09:22:35','2026-06-04 06:32:36'),(16,'mail_password','','2026-05-26 09:22:35','2026-05-26 09:22:35'),(17,'mail_encryption','tls','2026-05-26 09:22:35','2026-05-26 09:22:35'),(18,'mail_from_address','hello@s7milletco.com','2026-05-26 09:22:35','2026-06-04 06:32:36'),(19,'mail_from_name','S7 MilletCo','2026-05-26 09:22:35','2026-05-26 09:22:35'),(20,'delivery_date_enabled','0','2026-06-01 05:49:38','2026-06-01 05:49:38'),(21,'delivery_date_required','0','2026-06-01 05:49:38','2026-06-01 05:49:38'),(22,'delivery_lead_days','1','2026-05-27 07:40:06','2026-05-27 07:40:06'),(23,'store_name','S7 MilletCo','2026-05-28 08:39:07','2026-05-28 08:51:50'),(24,'brand_name','S7 MilletCo','2026-05-28 08:39:07','2026-05-28 08:39:07'),(25,'website_name','www.s7milletco.com','2026-05-28 08:39:07','2026-05-29 15:49:11'),(26,'store_email','hello@s7milletco.com','2026-05-28 08:39:07','2026-05-28 08:39:07'),(27,'store_phone','+91 89786 05003','2026-05-28 08:39:07','2026-05-28 08:39:07'),(28,'currency','INR','2026-05-28 08:39:07','2026-05-28 08:39:07'),(29,'invoice_prefix','S7','2026-05-28 08:39:07','2026-05-28 08:39:07'),(30,'invoice_counter','0','2026-05-28 08:39:07','2026-06-04 13:21:10'),(31,'velocity_enabled','0','2026-05-28 08:39:07','2026-05-28 08:39:07'),(32,'velocity_auto_ship','1','2026-05-28 08:39:07','2026-05-28 08:39:07'),(33,'velocity_package_length','20','2026-05-28 08:39:07','2026-05-28 08:39:07'),(34,'velocity_package_breadth','15','2026-05-28 08:39:07','2026-05-28 08:39:07'),(35,'velocity_package_height','10','2026-05-28 08:39:07','2026-05-28 08:39:07'),(36,'velocity_package_weight','0.5','2026-05-28 08:39:07','2026-05-29 10:39:52'),(37,'announcement_1','FREE Delivery on Combo Packs','2026-05-28 08:39:07','2026-05-29 10:39:52'),(38,'announcement_2','Just Add Water & Make Instant Dosa','2026-05-28 08:39:07','2026-05-29 10:39:52'),(39,'announcement_3','Healthy Millet Breakfasts in Minutes','2026-05-28 08:39:07','2026-05-29 10:39:52'),(40,'newsletter_heading','Stay Updated with Healthy Recipes & Offers!','2026-05-28 08:39:07','2026-05-28 08:39:07'),(41,'seo_default_title','S7 MilletCo — Healthy Millets Online','2026-05-28 08:39:07','2026-05-28 08:39:07'),(42,'seo_default_description','Shop wholesome millet breakfast mixes from S7 MilletCo. Smart nutrition everyday — dosa, idli & multigrain mixes delivered across India.','2026-05-28 08:39:07','2026-05-28 08:46:52'),(43,'page_seo_overrides','[{\"page\":\"home\",\"title\":\"S7 MilletCo \\u2014 Healthy Millets Online\",\"description\":\"Wholesome millet breakfast mixes. Make healthy breakfast in minutes.\",\"canonical\":\"\",\"sitemap\":\"1\"},{\"page\":\"catalogue\",\"title\":\"Shop Millet Mixes | S7 MilletCo\",\"description\":\"Browse dosa mixes, idli mixes, combo packs and trial packs.\",\"canonical\":\"\",\"sitemap\":\"1\"},{\"page\":\"about\",\"title\":\"About Us | S7 MilletCo\",\"description\":\"Our journey bringing ancient millet wisdom to modern Indian kitchens.\",\"canonical\":\"\",\"sitemap\":\"1\"},{\"page\":\"contact\",\"title\":\"Contact Us | S7 MilletCo\",\"description\":\"Reach S7 MilletCo for orders, wholesale and product questions.\",\"canonical\":\"\",\"sitemap\":\"1\"}]','2026-05-28 08:39:07','2026-05-28 08:46:52'),(44,'contact_whatsapp','918978605003','2026-05-28 08:39:07','2026-05-28 08:39:07'),(45,'store_tagline','Smart Nutrition Everyday','2026-05-28 08:46:52','2026-05-28 08:46:52'),(46,'logo_alt','S7 MilletCo - Smart Nutrition Everyday','2026-05-28 08:46:52','2026-05-28 08:46:52'),(47,'invoice_gstin','36TQQPS5568Q1ZY','2026-05-28 08:46:52','2026-05-30 14:38:42'),(48,'invoice_address','Hyderabad, Telangana - 500035\r\nIndia','2026-05-28 08:46:52','2026-05-30 14:48:09'),(49,'home_category_subtitle','Instant millet breakfast mixes for every craving','2026-05-28 08:46:52','2026-05-29 10:39:52'),(50,'home_why_millet_title','Why Choose Millet?','2026-05-28 08:46:52','2026-05-28 08:46:52'),(51,'home_value_props','[{\"icon\":\"fas fa-leaf\",\"title\":\"100% Natural\",\"desc\":\"Wholesome ingredients you can trust\",\"color\":\"text-brand-green-logo\"},{\"icon\":\"fas fa-seedling\",\"title\":\"Ancient Grains\",\"desc\":\"Packed with nutrition & goodness\",\"color\":\"text-brand-orange-logo\"},{\"icon\":\"fas fa-globe-asia\",\"title\":\"Made in India\",\"desc\":\"Supporting local farmers\",\"color\":\"text-brand-green-logo\"},{\"icon\":\"fas fa-heart\",\"title\":\"Loved by Families\",\"desc\":\"Perfect for everyday healthy meals\",\"color\":\"text-brand-green-logo\"}]','2026-05-28 08:46:52','2026-05-28 08:46:52'),(52,'home_why_millet_benefits','[{\"icon\":\"fas fa-seedling\",\"title\":\"Rich in Nutrients\",\"desc\":\"Naturally high in fiber, protein, and essential minerals.\"},{\"icon\":\"fas fa-heartbeat\",\"title\":\"Better for You\",\"desc\":\"Helps maintain energy, supports digestion and overall wellness.\"},{\"icon\":\"fas fa-shield-alt\",\"title\":\"Ancient & Trusted\",\"desc\":\"Millets are traditional grains with proven health benefits.\"},{\"icon\":\"fas fa-globe-americas\",\"title\":\"Good for the Planet\",\"desc\":\"Sustainable, eco-friendly and supports our farmers.\"}]','2026-05-28 08:46:52','2026-05-28 08:46:52'),(53,'founder_badge_title','Founder\n& Mother','2026-05-28 08:46:52','2026-05-28 08:46:52'),(54,'founder_ribbon','Driven by Purpose, Inspired by Tradition','2026-05-28 08:46:52','2026-05-28 08:46:52'),(55,'founder_heading_script','From a Mother’s Vision','2026-05-28 08:46:52','2026-05-28 08:46:52'),(56,'founder_heading_bold','TO EVERY INDIAN KITCHEN','2026-05-28 08:46:52','2026-05-28 08:46:52'),(57,'founder_body','S7 MilletCo was born from a simple belief — healthy traditional breakfasts should be easy for every family. As a mother, I wanted better, quicker and more nutritious options for my family without compromising on taste. That’s why we craft our mixes with the goodness of millets and the wisdom of tradition.','2026-05-28 08:46:52','2026-05-28 08:46:52'),(58,'founder_feature_1','Made with Real Millets','2026-05-28 08:46:52','2026-05-28 08:46:52'),(59,'founder_feature_2','Gluten Free No Preservatives','2026-05-28 08:46:52','2026-05-29 10:39:52'),(60,'founder_feature_3','Smart Nutrition Every day','2026-05-28 08:46:52','2026-05-28 08:46:52'),(61,'founder_cta_text','Read Our Story','2026-05-28 08:46:52','2026-05-28 08:46:52'),(62,'founder_signature_brand','S7 MilletCo','2026-05-28 08:46:52','2026-05-28 08:46:52'),(63,'founder_quote_note','“I started S7 MilletCo with one dream: to help families enjoy healthier breakfasts without compromising time or taste.”','2026-05-28 08:46:52','2026-05-28 08:46:52'),(64,'about_hero_title','About S7 MilletCo','2026-05-28 08:46:52','2026-05-28 08:46:52'),(65,'about_hero_subtitle','We are passionate about bringing the ancient wisdom of millets back to modern dining tables. Our mission is to provide you with the highest quality, naturally grown millet mixes that nourish your body and protect the earth.','2026-05-28 08:46:52','2026-05-28 08:46:52'),(66,'about_journey_title','Our Journey','2026-05-28 08:46:52','2026-05-28 08:46:52'),(67,'about_journey_p1','S7 MilletCo started with a simple idea: food should be medicine. Observing the rise in lifestyle diseases, we looked back to what our ancestors ate and rediscovered the incredible power of millets.','2026-05-28 08:46:52','2026-05-28 08:46:52'),(68,'about_journey_p2','We partner directly with local farmers who employ traditional, organic farming methods. By bypassing the middlemen, we ensure that our farmers get fair compensation while you receive fresh, unadulterated, and nutrient-dense grains.','2026-05-28 08:46:52','2026-05-28 08:46:52'),(69,'about_journey_bullets','[\"100% Organic Sourcing\",\"Direct Farmer Partnerships\",\"Sustainable Packaging\"]','2026-05-28 08:46:52','2026-05-28 08:46:52'),(70,'about_core_values','[{\"emoji\":\"\\ud83c\\udf31\",\"title\":\"Health First\",\"desc\":\"We never compromise on nutritional value. Our processing methods ensure the grain retains its maximum health benefits.\"},{\"emoji\":\"\\ud83e\\udd1d\",\"title\":\"Community\",\"desc\":\"Empowering local farming communities is at the heart of our business model, ensuring sustainable livelihoods.\"},{\"emoji\":\"\\ud83c\\udf0d\",\"title\":\"Sustainability\",\"desc\":\"Millets require significantly less water than rice or wheat. By promoting millets, we advocate for a greener planet.\"}]','2026-05-28 08:46:52','2026-05-28 08:46:52'),(71,'contact_heading','Contact Us','2026-05-28 08:46:52','2026-05-28 08:46:52'),(72,'contact_subtitle','Have questions about our millets, your order, or wholesale inquiries? We\'d love to hear from you.','2026-05-28 08:46:52','2026-05-28 08:46:52'),(73,'contact_address','Telangana, India','2026-05-28 08:46:52','2026-05-29 15:27:05'),(74,'contact_hours','Mon-Sat: 9AM - 6PM','2026-05-28 08:46:52','2026-05-28 08:46:52'),(75,'social_instagram','','2026-05-28 08:46:52','2026-05-29 15:27:05'),(76,'social_facebook','','2026-05-28 08:46:52','2026-05-29 15:27:05'),(77,'social_youtube','','2026-05-28 08:46:52','2026-05-29 15:27:05'),(78,'footer_tagline','Instant millet mixes for a healthy & happy you.','2026-05-28 08:46:52','2026-05-28 08:46:52'),(79,'footer_copyright','S7 MilletCo | A brand of Sukanya Dynamics Exim','2026-05-28 08:46:52','2026-05-28 08:46:52'),(80,'footer_fssai','FSSAI Lic No. 21234567890001','2026-05-28 08:46:52','2026-05-28 08:46:52'),(81,'footer_vegetarian','100% Vegetarian','2026-05-28 08:46:52','2026-05-28 08:46:52'),(82,'seo_default_keywords','millet, dosa mix, idli mix, healthy breakfast, S7 MilletCo','2026-05-28 08:46:52','2026-05-28 08:46:52'),(83,'favicon_path','images/favicon-1779958367.png','2026-05-28 08:52:47','2026-05-28 08:52:47'),(84,'founder_cta_url','/about#founder-story','2026-05-28 08:53:51','2026-05-28 08:53:51'),(85,'founder_signature_label','— Founder','2026-05-28 08:53:51','2026-05-28 08:53:51'),(86,'founder_illustration_path','images/founder-kitchen-illustration.svg','2026-05-28 08:53:51','2026-05-28 08:53:51'),(87,'founder_photo_path','','2026-05-28 08:53:51','2026-05-28 08:53:51'),(88,'velocity_username','+918074051257','2026-05-28 12:17:04','2026-06-06 11:26:45'),(89,'velocity_warehouse_id','WHOJNR','2026-05-28 12:17:04','2026-06-06 10:20:20'),(90,'velocity_pickup_location','Saroornagar','2026-05-28 12:17:04','2026-06-06 10:20:20'),(91,'velocity_warehouse_pincode','500035','2026-05-28 12:17:04','2026-06-06 10:20:20'),(92,'velocity_warehouse_city','Hyderabad','2026-05-28 12:17:04','2026-06-06 10:20:20'),(93,'velocity_warehouse_state','Telangana','2026-05-28 12:17:04','2026-06-06 10:20:20'),(94,'velocity_warehouse_address','S7 MilletCo\r\n20-53/20, Tirumala Nagar Colony, Saoornagar, Hyderabad, Telangana, India, 500035','2026-05-28 12:17:04','2026-06-06 10:20:20'),(95,'velocity_default_carrier_id','','2026-05-28 12:17:04','2026-05-28 12:17:04'),(96,'home_usp_strip','[{\"icon\":\"fas fa-droplet\",\"label\":\"Just Add Water\"},{\"icon\":\"fas fa-clock\",\"label\":\"Ready in Minutes\"},{\"icon\":\"fas fa-snowflake\",\"label\":\"No Refrigeration\"},{\"icon\":\"fas fa-wheat-awn\",\"label\":\"Millet Based\"},{\"icon\":\"fas fa-leaf\",\"label\":\"Gluten Free\"}]','2026-05-29 10:39:52','2026-05-29 10:39:52'),(97,'home_how_it_works','[{\"icon\":\"fas fa-blender\",\"title\":\"Add Water\",\"desc\":\"Add water to the mix.\"},{\"icon\":\"fas fa-clock\",\"title\":\"Mix for 30 Seconds\",\"desc\":\"Mix well to make a smooth batter.\"},{\"icon\":\"fas fa-pan-frying\",\"title\":\"Cook Fresh Dosa & Idly\",\"desc\":\"Cook and enjoy soft & healthy dosa or idly.\"}]','2026-05-29 10:39:52','2026-06-01 11:31:26'),(98,'home_hero_badge','INDIA\'S FIRST','2026-05-29 11:42:15','2026-05-29 11:42:15'),(99,'home_trust_bar','[{\"icon\":\"fas fa-leaf\",\"label\":\"Made with Natural Ingredients\"},{\"icon\":\"fas fa-ban\",\"label\":\"No Artificial Flavours\"},{\"icon\":\"fas fa-certificate\",\"label\":\"FSSAI Approved\"},{\"icon\":\"fas fa-seedling\",\"label\":\"Clean & Healthy Food\"},{\"icon\":\"fas fa-users\",\"label\":\"Trusted by 10,000+ Happy Families\"}]','2026-05-29 11:42:15','2026-05-29 11:42:15'),(100,'home_hero_title','Smart Instant Millet Breakfast','2026-05-29 12:15:14','2026-05-29 12:15:14'),(101,'home_hero_subtitle','Just Add Water • No Fermentation • No Refrigeration','2026-05-29 12:15:14','2026-05-29 12:15:14'),(102,'home_hero_button_text','Shop Instant Breakfasts','2026-05-29 12:15:14','2026-05-29 12:15:14'),(103,'invoice_logo_path','images/invoice-logo-1780151649.png','2026-05-30 14:34:09','2026-05-30 14:34:09'),(104,'invoice_legal_company_name','Sukanya Dynamics Exim','2026-05-30 14:34:09','2026-05-30 14:38:42'),(105,'razorpay_enabled','1','2026-06-04 07:17:29','2026-06-04 07:17:29'),(106,'cashfree_enabled','0','2026-06-04 07:17:29','2026-06-04 07:17:29'),(107,'cashfree_environment','sandbox','2026-06-04 07:17:29','2026-06-04 07:17:29'),(108,'theme_primary','#004D26','2026-06-04 07:17:29','2026-06-10 05:19:33'),(109,'theme_accent','#F26A2E','2026-06-04 07:17:29','2026-06-10 05:19:33'),(110,'theme_background','#FFFFFF','2026-06-04 07:17:29','2026-06-10 05:19:33'),(111,'theme_text','#1A3324','2026-06-04 07:17:29','2026-06-10 05:19:33'),(112,'theme_soft','#E8F5EE','2026-06-04 07:17:29','2026-06-10 05:19:33'),(113,'qr_redirect_url','https://s7milletco.com','2026-06-04 07:17:29','2026-06-04 14:48:36'),(114,'qr_scan_base_url','https://s7milletco.com','2026-06-04 07:17:29','2026-06-04 14:48:36'),(115,'qr_generated_at','1780584808','2026-06-04 07:18:21','2026-06-04 14:53:28'),(116,'show_global_free_shipping_banner','0','2026-06-06 10:20:20','2026-06-06 10:20:20'),(117,'visit_page_title','','2026-06-09 13:12:38','2026-06-09 13:12:38'),(118,'visit_page_subtitle','','2026-06-09 13:12:38','2026-06-09 13:12:38'),(119,'visit_page_links','[{\"title\":\"WhatsApp\",\"subtitle\":\"Chat with us on WhatsApp\",\"url\":\"https:\\/\\/wa.me\\/918978605003\",\"icon\":\"fab fa-instagram\",\"color_from\":\"#25d366\",\"color_to\":\"#128c7e\",\"enabled\":\"1\"},{\"title\":\"Visit Us\",\"subtitle\":\"Visit our website\",\"url\":\"https:\\/\\/s7milletco.com\",\"icon\":\"fab fa-instagram\",\"color_from\":\"#0d9488\",\"color_to\":\"#2563eb\",\"enabled\":\"1\"},{\"title\":\"Instagram\",\"subtitle\":\"Follow Us on Instagram\",\"url\":\"https:\\/\\/www.instagram.com\\/s7milletco\\/\",\"icon\":\"fab fa-instagram\",\"color_from\":\"#cf3c79\",\"color_to\":\"#ec6147\",\"enabled\":\"1\"},{\"title\":\"Facebook\",\"subtitle\":\"Follo Us on Facebook\",\"url\":\"https:\\/\\/www.facebook.com\\/profile.php?id=61590299807738\",\"icon\":\"fab fa-instagram\",\"color_from\":\"#00C6FF\",\"color_to\":\"#0072FF\",\"enabled\":\"1\"}]','2026-06-09 13:12:38','2026-06-10 05:07:47');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_zones`
--

DROP TABLE IF EXISTS `shipping_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_zones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `match_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `match_values` text COLLATE utf8mb4_unicode_ci,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `free_shipping_threshold` decimal(10,2) NOT NULL DEFAULT '399.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_zones`
--

LOCK TABLES `shipping_zones` WRITE;
/*!40000 ALTER TABLE `shipping_zones` DISABLE KEYS */;
INSERT INTO `shipping_zones` VALUES (2,'Hyderabad & Secunderabad','city','Hyderabad\r\nSecunderabad',39.00,499.00,1,0,'2026-05-30 07:20:22','2026-06-13 07:08:31'),(3,'Telangana','state','Telangana',49.00,799.00,1,0,'2026-05-30 07:20:22','2026-06-04 05:13:54'),(4,'Rest of India','default',NULL,89.00,899.00,1,1,'2026-05-30 07:20:22','2026-05-30 13:48:34'),(5,'Andhra Pradesh','state','Andhra Pradesh\r\nAP',49.00,799.00,1,0,'2026-05-30 13:51:17','2026-06-01 06:04:04');
/*!40000 ALTER TABLE `shipping_zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quote` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint unsigned NOT NULL DEFAULT '5',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (1,'Priya Sharma','Verified Buyer · Bangalore','Best instant dosa mix I\'ve tried! Crispy outside, soft inside — tastes just like homemade.',5,1,1,'2026-05-27 09:35:26','2026-05-27 09:35:26'),(2,'Rohit Verma','Verified Buyer · Pune','Healthy, tasty and so convenient. Perfect for busy mornings when I have no time to grind batter.',5,1,2,'2026-05-27 09:35:26','2026-05-27 09:35:26'),(3,'Anjali Mehta','Verified Buyer · Mumbai','My kids love the ragi idli mix, and I love that it\'s packed with nutrition for them.',5,1,3,'2026-05-27 09:35:26','2026-05-27 09:35:26'),(4,'Lakshmi Reddy','Verified Buyer · Hyderabad','The multigrain dosa mix is a game-changer. No preservatives, just wholesome millets — highly recommend!',5,1,4,'2026-05-27 09:35:26','2026-05-27 09:35:26'),(5,'Karthik Iyer','Verified Buyer · Chennai','Finally a brand that keeps millet authentic. The idli mix ferments beautifully and stays fluffy.',5,1,5,'2026-05-27 09:35:26','2026-05-27 09:35:26'),(6,'Deepa Nair','Verified Buyer · Kochi','Our whole family switched to S7 MilletCo for breakfast. Guilt-free, filling, and genuinely delicious.',5,1,6,'2026-05-27 09:35:26','2026-05-27 09:35:26');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_unlocked` tinyint(1) NOT NULL DEFAULT '0',
  `successful_referrals_count` int unsigned NOT NULL DEFAULT '0',
  `referred_by_user_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_referral_code_unique` (`referral_code`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_referred_by_user_id_foreign` (`referred_by_user_id`),
  CONSTRAINT `users_referred_by_user_id_foreign` FOREIGN KEY (`referred_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test User','test@example.com',NULL,NULL,0,0,NULL,'2026-05-21 07:54:32','$2y$12$hgF7dOAYd046ms9FEiWd5OAq.i/zMH6YwP5PydFb.aQk/f2OMYv1q',0,'CFSVoEYIiz','2026-05-21 07:54:32','2026-05-21 07:54:32'),(2,'S7 Admin','admin@s7milletco.com',NULL,NULL,0,0,NULL,NULL,'$2y$12$ZL3.I8etDc2MGMOTqCC.0eGkBCDGWzc6izSYbfwgMfvGyqMgK4jqy',1,'GIRkK1pagNzx6o0fe8o98VRUlOOXH8PB6AdUPWzHJHPxBmSxuXSi9auEW8qK','2026-05-26 07:52:03','2026-05-27 07:40:06'),(3,'Raju Chiklinge','rajuchiklinge03@gmail.com',NULL,'S7AJUX1',0,0,NULL,NULL,'$2y$12$BwLtAymBs/mPQhea2BPNNOBzRPJpObAAvCtJUwEgbjo91U6BWI8eq',0,NULL,'2026-05-28 12:18:50','2026-05-28 12:21:13'),(4,'Ram','raj.chik89@gmail.com','9533677041',NULL,0,0,NULL,NULL,'$2y$12$.gViNBv/Km3wRlOjtOFhZOB3dPovyt7w4AvNIH2ZuV2lIDCghtKEq',0,NULL,'2026-05-30 14:15:14','2026-05-30 14:15:14'),(5,'vegi kumar','vjnankumar@gmail.com',NULL,'S7VEGI2',1,3,NULL,NULL,'$2y$12$j3DsJOPiBjmmzPA7UayTheucqpDbVy3yl.2LYqp.28vBCJf99In5G',0,NULL,'2026-06-01 05:56:58','2026-06-01 06:37:14'),(6,'hirira','hira@gmail.com',NULL,NULL,0,0,5,NULL,'$2y$12$f0sazfK/IP/.hoXZkqOe5uiB7b6NUbd4pmlLw5gHJIhtOGs4uN.LG',0,NULL,'2026-06-01 06:35:28','2026-06-01 06:35:28'),(7,'diva','dive@gmail.com',NULL,NULL,0,0,5,NULL,'$2y$12$PAyQ2hia5TDk3kNy71YTauLGVOBctzBSC3UcZRmzioRos/VBsTLay',0,NULL,'2026-06-01 06:36:06','2026-06-01 06:36:06'),(8,'jive',NULL,'9688139213',NULL,0,0,5,NULL,'$2y$12$CHoPmAmdZvrZkfliJ4L3oe5CXFQ1f5N6O1dL6Y9EFHADcbY0sU2EO',0,NULL,'2026-06-01 06:37:14','2026-06-01 06:37:14');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 's7millet'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-19  7:46:53
