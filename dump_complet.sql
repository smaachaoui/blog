-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: blog
-- ------------------------------------------------------
-- Server version	8.0.44-0ubuntu0.24.04.2

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
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_category_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Fantasy','fantasy'),(2,'Science-Fiction','science-fiction'),(3,'Thriller','thriller'),(4,'Poésie','poesie');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_name` varchar(120) NOT NULL,
  `author_email` varchar(180) DEFAULT NULL,
  `content` longtext NOT NULL,
  `created_at` datetime NOT NULL,
  `is_approved` tinyint NOT NULL,
  `post_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9474526C4B89032C` (`post_id`),
  CONSTRAINT `FK_9474526C4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES (2,'Luke','luke@blog.test','J\'appréhende un peu la suite, j\'espère que la famille Stark va remporter ces jeux de pouvoir !','2026-01-22 22:43:25',1,3);
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20260121083609','2026-01-21 08:36:29',22),('DoctrineMigrations\\Version20260121100332','2026-01-21 10:03:52',22),('DoctrineMigrations\\Version20260121110123','2026-01-21 11:01:37',81),('DoctrineMigrations\\Version20260121110603','2026-01-21 11:06:19',28),('DoctrineMigrations\\Version20260121153953','2026-01-21 15:40:07',36),('DoctrineMigrations\\Version20260122085135','2026-01-22 08:51:48',44);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `book_author` varchar(180) NOT NULL,
  `content` longtext NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `category_id` int NOT NULL,
  `author_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_post_slug` (`slug`),
  KEY `IDX_5A8A6C8D12469DE2` (`category_id`),
  KEY `IDX_5A8A6C8DF675F31B` (`author_id`),
  CONSTRAINT `FK_5A8A6C8D12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  CONSTRAINT `FK_5A8A6C8DF675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
INSERT INTO `post` VALUES (2,'Bilbo le Hobbit : un voyage initiatique au cœur de la Terre du Milieu','bilbo-le-hobbit-un-voyage-initiatique-au-coeur-de-la-terre-du-milieu','J. R. R. Tolkien','Bilbo le Hobbit est à l\'image de son personnage principal. C\'est un livre simple et facétieux qui sent bon la Comté, la magie des Jours Anciens et le monde merveilleux de l\'imagination.\n\nTolkien n\'est pas vraiment un écrivain abordable. Je suis une fan inconditionnelle de son œuvre mais je comprends très bien l\'épreuve insurmontable qu\'elle peut représenter.\n\nPour autant, Bilbo le Hobbit me rappelle les histoires du soir que j\'appréciais tant, dans ma prime enfance. Le genre d\'histoire qu\'on déguste avec une boisson chaude devant un feu de cheminée. Le genre d\'histoire qui émerveille, transporte et envoûte.\n\nMoi aussi, je veux partir à l\'aventure avec une bande de nains grognons et gourmands à la conquête d\'un trésor gardé par un dragon. Moi aussi, je veux suivre cette petite compagnie dans la Forêt Noire, me battre contre des troll, des gobelins et des Wargs.\n\nJe veux danser avec les elfes, me cacher dans des tonneaux et rire d\'un rien. Je veux partir à l\'aventure parce que ça me plait et vivre dans un monde aussi magique et merveilleux que celui de Tolkien.\n\nParce que c\'est ça, Bilbo le Hobbit. Un petit livre qui ouvre une porte vers un monde merveilleux et magique ou il est encore possible de ne vivre que de rêves.',NULL,'2026-01-21 13:48:00','2026-01-21 13:52:17',1,2),(3,'Le Trône de Fer – Tome 1 : L’hiver vient','le-trone-de-fer-tome-1-l-hiver-vient','George R. R. Martin','L\'hiver vient.\n\nPremier tome d\'une série qui compte à ce jour 15 tomes (5 en version originale ou en version \"intégrale\" française), il a obtenu le prix locus 1997.\nJe fais partie de ces gens qui sont venus au livre après avoir vu la série, et je dois avouer qu\'après avoir particulièrement apprécié cette première saison, j\'ai retrouvé dans le livre, l\'ambiance et l\'histoire assez fidèle. Les personnages ont gagné en corps et sont conformes à ce que j\'attendais.\n\nL\'histoire est à la fois simple et complexe : Sur le continent Westeros, protégé au nord des contrées glacées par le Mur sur lequel veille la garde de nuit et à l\'est du continent Essos des Dothrakis, peuple de cavaliers nomades, par la mer, la maison Baratheon et le roi Robert règne sur les 7 couronnes. Il est marié à Cersei Lannister, une autre puissante maison qui convoite le trône. le roi fait alors appel, pour l\'aider en tant que Main du Roi, à Ned de la maison Stark, fidèle ami et allié depuis toujours, lui-même marié à Catelyn de la maison Tully. Pendant ce temps, La maison Targaryen renversée par Robert et son prince héritier Viserys cherche à reconquérir son trône en s\'alliant avec les Dothrakis en offrant sa soeur Daenerys comme épouse à leur chef Drogo.\nVous parlerais-je alors de Tyrion, le fils Lannister, nain et retors, de John Snow le bâtard Stark qui rejoint la garde de nuit et pléthore d\'autres personnages ? Il faut avouer qu\'avoir vu la série avant aide à se retrouver dans cette multitude de personnages.\n\nJ\'ai vraiment trouvé dans la lecture de ce premier opus ce que j\'étais venu chercher. Après l\'époustouflant Gagner la guerre, un peu déçu des Annales de la Compagnie noire, j\'ai retrouvé ici cette atmosphère sombre et réaliste fort bien restituée. Une fantasy médiévale où le \"merveilleux\" et le surnaturel sont presque réduits à l\'état de légende et en tout cas secondaires par rapport à la puissance de l\'intrigue et des personnages. (Peut-être gagneront-ils en puissance dans les prochains tomes).\nUne histoire très politique, très réalpolitique où l\'on discute de l\'assassinat de bébés à naître pour le \"bien\" de la couronne. Des personnages fouillés, nuancés, authentiques, pas de héros au grand coeur pur, pas de machiavéliques forces du mal (quoique ?), des hommes et des femmes baignant dans un univers extrêmement riche et crédible.\nLe style d\'écriture est soutenu et il réussit à associer la modernité du ton à un langage rétro parfaitement raccord avec le milieu médiéval. Il y a une polémique sur la qualité de la traduction. Je ne peux en juger n\'ayant pas lu la VO (et en n\'en étant bien incapable d\'ailleurs), mais j\'ai en tout cas particulièrement apprécié cette lecture.\nJe ne mets que quatre étoiles car j\'ai trouvé malgré tout, la mise en place un poil trop lente (mais un poil hein), un peu plus de ferraillage n\'aurait pas nuit...\n\nCe premier livre se termine de façon un peu abrupte, mais après tout, ce n\'est que la première partie d\'un découpage à la \"française\" de la version originale qui compte également le donjon rouge que je vais m\'empresser de lire.','902b3b784154b499d225428906c8d9b2.jpg','2026-01-22 09:33:39','2026-01-22 09:33:40',1,1),(4,'Star Wars - The Old Republic, tome 3 : Revan','star-wars-the-old-republic-tome-3-revan','Drew Karpyshyn','Voilà ce livre, marque ma relecture des romans Star Wars que je possède (j\'enchaînerai sur les BD)\n\nRevan, le mythique chevalier Jedi qui a repoussé les Mandaloriens (soyons honnête limite éradiqué) puis a basculé du côté obscur pour revenir à la lumière est de retour.\nCe Roman de Drew Karpyshyn (quel plaisir de retrouver sa plume et sa narration) et la suite des excellents jeux Kotor et sert d\'introduction à l\'une des extensions du jeu en ligne SWTOR.\nLe livre s\'adresse clairement à ceux qui ont joué au premier Kotor (au passage un petit chef d\'oeuvre du jeu vidéo) dont Drew était le scénariste.\nCe n\'est pas le meilleur roman de Drew Karpyshyn, mais il reste très bon, c\'est une oeuvre riche en informations et en aventures.\n\nPour les fans de Kotor : Bastila Shan, Canderous Ordo, Meetra et surtout T3-M4 sont aussi présents\n\nUn roman satisfaisan..','ed74bbd3321d4bf22735ca24f58b5f04.jpg','2026-01-23 08:26:12','2026-01-23 08:31:47',2,5);
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin@blog.test','[\"ROLE_USER\", \"ROLE_ADMIN\"]','$2y$13$IMrVgOl4Kifu/ouRBgvv7uOPPIHgwO8t3SBmXEjy7c41VxU/jfP2m'),(2,'test@blog.test','[\"ROLE_USER\"]','$2y$13$sKFIqf1BOKygpCwUWvbGEOeWr5GuA/o7cU7I1.0U/ISaaZwxkzNEa'),(3,'tolkien@blog.test','[\"ROLE_USER\"]','$2y$13$F5fIQQUrcYGWRanItpztcuCkwppR3vPKrgk7UCQ8B29ACqFQVUSl2'),(4,'henry@blog.test','[]','$2y$13$2eGRU7FRehq0ZReFCxSq5u.aZbIRh0RHvVbH5mCRS1bE/1SeBMToG'),(5,'luke@blog.test','[\"ROLE_USER\"]','$2y$13$rcjJe7quYU9ZVPzUNr.w1ONeZM6uKiTW1Yp8X8u4LK3CPqfD42SEm');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-23 19:16:10
