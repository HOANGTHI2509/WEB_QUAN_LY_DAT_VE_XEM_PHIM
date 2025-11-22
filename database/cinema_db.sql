CREATE DATABASE  IF NOT EXISTS `cinema_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `cinema_db`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: cinema_db
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actors`
--

DROP TABLE IF EXISTS `actors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actors` (
  `ActorID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ActorID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actors`
--

LOCK TABLES `actors` WRITE;
/*!40000 ALTER TABLE `actors` DISABLE KEYS */;
INSERT INTO `actors` VALUES (9,'Đỗ Nhật Hoàng'),(5,'Kaity Nguyễn'),(10,'Lê Hạ Anh'),(7,'Ma Ran Đô'),(12,'Nguyễn Phương Nam'),(3,'Sam Worthington'),(11,'Steven Nguyễn'),(4,'Thái Hòa'),(6,'Thanh Sơn'),(8,'Trâm Anh'),(2,'Tự Long'),(1,'Xuân Bắc');
/*!40000 ALTER TABLE `actors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_food`
--

DROP TABLE IF EXISTS `booking_food`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_food` (
  `BookingID` int NOT NULL,
  `FoodID` int NOT NULL,
  `Quantity` int DEFAULT '1',
  `PriceAtBooking` decimal(10,2) NOT NULL,
  PRIMARY KEY (`BookingID`,`FoodID`),
  KEY `FoodID` (`FoodID`),
  CONSTRAINT `booking_food_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `bookings` (`BookingID`) ON DELETE CASCADE,
  CONSTRAINT `booking_food_ibfk_2` FOREIGN KEY (`FoodID`) REFERENCES `foodcombos` (`FoodID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_food`
--

LOCK TABLES `booking_food` WRITE;
/*!40000 ALTER TABLE `booking_food` DISABLE KEYS */;
INSERT INTO `booking_food` VALUES (2,1,1,35000.00),(4,1,1,35000.00),(5,1,1,35000.00),(6,1,1,35000.00),(8,1,1,35000.00),(9,1,1,35000.00),(25,2,1,20000.00);
/*!40000 ALTER TABLE `booking_food` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `BookingID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `PromotionID` int DEFAULT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `PaymentStatus` enum('Pending','Paid','Canceled') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `BookingTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`BookingID`),
  KEY `UserID` (`UserID`),
  KEY `PromotionID` (`PromotionID`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`PromotionID`) REFERENCES `promotions` (`PromotionID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,4,NULL,310000.00,'Paid','2025-11-21 13:47:15'),(2,4,NULL,255000.00,'Paid','2025-11-21 13:51:26'),(3,4,NULL,180000.00,'Paid','2025-11-21 14:21:30'),(4,4,NULL,255000.00,'Paid','2025-11-21 14:25:35'),(5,5,NULL,255000.00,'Paid','2025-11-22 07:12:48'),(6,5,NULL,215000.00,'Paid','2025-11-22 07:36:00'),(8,5,NULL,255000.00,'Paid','2025-11-22 08:06:27'),(9,5,NULL,255000.00,'Paid','2025-11-22 08:14:01'),(10,4,NULL,130000.00,'Paid','2025-11-22 09:51:07'),(13,4,NULL,260000.00,'Paid','2025-11-22 12:24:14'),(14,6,NULL,260000.00,'Paid','2025-11-22 16:34:24'),(16,6,NULL,260000.00,'Pending','2025-11-22 16:42:00'),(18,6,NULL,260000.00,'Pending','2025-11-22 16:49:50'),(19,6,NULL,260000.00,'Pending','2025-11-22 16:50:01'),(21,6,NULL,260000.00,'Pending','2025-11-22 17:00:41'),(24,6,NULL,260000.00,'Pending','2025-11-22 17:08:15'),(25,6,NULL,150000.00,'Paid','2025-11-22 17:17:54');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `foodcombos`
--

DROP TABLE IF EXISTS `foodcombos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `foodcombos` (
  `FoodID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text COLLATE utf8mb4_unicode_ci,
  `Price` decimal(10,2) NOT NULL,
  `ImageURL` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`FoodID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `foodcombos`
--

LOCK TABLES `foodcombos` WRITE;
/*!40000 ALTER TABLE `foodcombos` DISABLE KEYS */;
INSERT INTO `foodcombos` VALUES (1,'2 bỏng + 1 nước ','COMBO1',65000.00,'assets/uploads/food/food_1763813689_9947.webp'),(2,'Nước coca','Coca lon',20000.00,'assets/uploads/food/food_1763813598_8262.webp'),(3,'7 UP','7 UP',20000.00,'assets/uploads/food/food_1763813631_7820.webp');
/*!40000 ALTER TABLE `foodcombos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `genres` (
  `GenreID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`GenreID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genres`
--

LOCK TABLES `genres` WRITE;
/*!40000 ALTER TABLE `genres` DISABLE KEYS */;
INSERT INTO `genres` VALUES (16,'Âm nhạc'),(10,'Bí ẩn'),(14,'Chiến tranh'),(19,'Cổ trang'),(12,'Gia đình'),(9,'Giật gân (Thriller)'),(6,'Hài'),(1,'Hành động'),(4,'Hoạt hình'),(7,'Khoa học viễn tưởng'),(3,'Kinh dị'),(13,'Lịch sử'),(5,'Phiêu lưu'),(15,'Tài liệu'),(8,'Tâm lý (Drama)'),(17,'Thần thoại/Giả tưởng'),(18,'Thể thao'),(2,'Tình cảm'),(11,'Tội phạm');
/*!40000 ALTER TABLE `genres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movie_actors`
--

DROP TABLE IF EXISTS `movie_actors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movie_actors` (
  `MovieID` int NOT NULL,
  `ActorID` int NOT NULL,
  PRIMARY KEY (`MovieID`,`ActorID`),
  KEY `ActorID` (`ActorID`),
  CONSTRAINT `movie_actors_ibfk_1` FOREIGN KEY (`MovieID`) REFERENCES `movies` (`MovieID`) ON DELETE CASCADE,
  CONSTRAINT `movie_actors_ibfk_2` FOREIGN KEY (`ActorID`) REFERENCES `actors` (`ActorID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movie_actors`
--

LOCK TABLES `movie_actors` WRITE;
/*!40000 ALTER TABLE `movie_actors` DISABLE KEYS */;
INSERT INTO `movie_actors` VALUES (5,3),(6,4),(6,5),(8,5),(6,6),(6,7),(6,8),(7,9),(7,10),(7,11),(7,12);
/*!40000 ALTER TABLE `movie_actors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movie_genres`
--

DROP TABLE IF EXISTS `movie_genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movie_genres` (
  `MovieID` int NOT NULL,
  `GenreID` int NOT NULL,
  PRIMARY KEY (`MovieID`,`GenreID`),
  KEY `GenreID` (`GenreID`),
  CONSTRAINT `movie_genres_ibfk_1` FOREIGN KEY (`MovieID`) REFERENCES `movies` (`MovieID`) ON DELETE CASCADE,
  CONSTRAINT `movie_genres_ibfk_2` FOREIGN KEY (`GenreID`) REFERENCES `genres` (`GenreID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movie_genres`
--

LOCK TABLES `movie_genres` WRITE;
/*!40000 ALTER TABLE `movie_genres` DISABLE KEYS */;
INSERT INTO `movie_genres` VALUES (5,1),(6,1),(5,7),(6,9),(8,12),(1,14),(7,14);
/*!40000 ALTER TABLE `movie_genres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movies`
--

DROP TABLE IF EXISTS `movies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movies` (
  `MovieID` int NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text COLLATE utf8mb4_unicode_ci,
  `Duration` int NOT NULL,
  `Director` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PosterURL` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TrailerURL` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Status` enum('Đang chiếu','Sắp chiếu','Ngưng chiếu') COLLATE utf8mb4_unicode_ci DEFAULT 'Sắp chiếu',
  `ReleaseDate` date DEFAULT NULL,
  `Rating` decimal(3,1) DEFAULT '0.0',
  PRIMARY KEY (`MovieID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movies`
--

LOCK TABLES `movies` WRITE;
/*!40000 ALTER TABLE `movies` DISABLE KEYS */;
INSERT INTO `movies` VALUES (1,'An Dương Vương','An Dương Vương (tên thật là Thục Phán) là vị vua sáng lập nhà nước Âu Lạc, sau khi đánh bại vua Hùng cuối cùng và thống nhất hai bộ lạc Âu Việt và Lạc Việt. Ông cho xây dựng thành Cổ Loa (tương truyền xoắn ốc như vỏ ốc) để làm kinh đô vững chắc, bảo vệ đất nước trước sự nhòm ngó từ phương Bắc.\r\nNhờ có thần Kim Quy giúp đỡ, An Dương Vương nhận được chiếc lưỡi nỏ thần (nỏ liên châu) có thể một phát bắn chết hàng nghìn quân địch. Với thành cao, hào sâu và nỏ thần trong tay, Âu Lạc trở thành một quốc gia hùng mạnh, khiến Triệu Đà – vua nước Nam Việt ở phía Bắc – nhiều lần đem quân xâm lược nhưng đều thất bại thảm hại.\r\nĐể tránh chiến tranh lâu dài, Triệu Đà dùng chính sách “hòa thân”: cho con trai mình là Trọng Thủy sang làm con tin ở Âu Lạc và xin cưới công chúa Mị Châu – con gái duy nhất của An Dương Vương. An Dương Vương vì quá tự tin vào sức mạnh thành Cổ Loa và nỏ thần nên chủ quan đồng ý.\r\nMị Châu thật lòng yêu Trọng Thủy, không hề hay biết đó là kế của kẻ thù. Trong một phút ngây thơ và cả tin, nàng đã chỉ cho chồng xem bí mật của nỏ thần. Trọng Thủy lén tháo mất lẫy nỏ thần (ruột nỏ) mang về Bắc, đồng thời đặt điều cho cha mình phát binh đánh Âu Lạc.\r\nKhi quân Triệu Đà kéo đến, An Dương Vương vẫn điềm nhiên ngồi đánh cờ, cười rằng: “Đà không sợ nỏ thần hay sao?”. Chỉ đến khi quân giặc đã vào tận thành, ông mới cầm nỏ ra bắn thì mới biết lẫy nỏ đã bị đánh tráo. Thành Cổ Loa thất thủ, An Dương Vương vội cho Mị Châu lên ngựa cùng mình chạy trốn về phía biển.\r\nTrên đường chạy nạn, ông đau đớn nhận ra vết máu lông ngỗng mà Mị Châu rắc để chỉ đường cho chồng (vì nàng nghĩ Trọng Thủy sẽ theo bảo vệ hai cha con). Quá uất hận vì con gái phản bội tổ quốc, An Dương Vương rút kiếm chém chết Mị Châu ngay trên yên ngựa. Sau đó, ông cưỡi ngựa lao xuống biển. Thần Kim Quy hiện lên nói lớn: “Kẻ ngồi sau lưng chính là giặc!” và dẫn An Dương Vương xuống thủy cung.\r\nCòn Mị Châu chết rồi hóa thành ngọc thạch, Trọng Thủy ôm xác vợ khóc thương rồi gieo mình xuống giếng ở Cổ Loa tự vẫn. Người đời sau lấy máu trai (một loài sò) rửa viên ngọc của Mị Châu thì ngọc trở nên trong suốt lạ thường – đó là nguồn gốc truyền thuyết “ngọc trai - giếng nước”.\r\nBi kịch An Dương Vương - Mị Châu - Trọng Thủy là câu chuyện vừa đau thương vừa day dứt về tình yêu và lòng yêu nước, về sự cảnh giác trước kẻ thù và hậu quả của sự cả tin, chủ quan trong việc giữ gìn giang sơn.',30,'Thi Hoàng','assets/uploads/movies/movie_1763728984_2886.jpg','https://www.youtube.com/watch?v=ukHK1GVyr0I&list=RDAu6LqK1UH8g&index=10','Đang chiếu','2025-10-20',0.0),(5,'Avatar 3','  Avatar: Fire and Ash sẽ đưa khán giả đến với những khía cạnh mới của Pandora, với cốt truyện đen tối và cảm xúc hơn. Dưới đây là những điểm nổi bật trong nội dung Avatar 3.\r\nGiới thiệu tộc Na’vi mới: Người Tro Tàn\r\nPhần 3 sẽ giới thiệu tộc Người Tro Tàn (Ash People),một bộ tộc Na’vi sống ở vùng núi lửa, khác biệt hoàn toàn với các tộc hiền hòa trước đây. Được dẫn dắt bởi Varang (Oona Chaplin), tộc này mang tính cách hung hãn và phức tạp hơn. Nội dung Avatar 3 sẽ phá vỡ lối mòn “người xấu, Na’vi tốt”, mang đến góc nhìn đa chiều. Avatar: Fire and Ash hứa hẹn khám phá sâu sắc văn hóa của tộc này.\r\nNgười Tro Tàn được mô tả là đã trải qua thảm họa tự nhiên, định hình nên tính cách mạnh mẽ và đôi khi “xấu xa”. James Cameron muốn thể hiện sự đối lập giữa các tộc Na’vi. Bom tấn 2025 này sẽ mang đến những cuộc xung đột kịch tính. Nội dung Avatar 3 sẽ làm nổi bật sự đa dạng của Pandora.\r\n  Cốt truyện đen tối và cảm xúc hơn\r\nĐạo diễn James Cameron tiết lộ Avatar: Fire and Ash sẽ khai thác sâu nỗi đau mất mát của gia đình Jake và Neytiri sau cái chết của con trai Neteyam. Phim nhấn mạnh chủ đề đau buồn, với cách tiếp cận chân thực hơn so với các phim Hollywood thông thường. Nội dung Avatar 3 được đánh giá là cảm xúc và có thể là phần hay nhất. Phim James Cameron lần này sẽ chạm đến trái tim khán giả.\r\nCốt truyện sẽ tiếp tục không lâu sau các sự kiện của The Way of Water, với những thử thách mới cho gia đình Sully. Một số phản ứng sớm cho rằng phim có những tình tiết bất ngờ, “đánh mạnh” vào cảm xúc. Avatar: Fire and Ash hứa hẹn là bom tấn 2025 với câu chuyện sâu sắc. Lịch chiếu Avatar 3 là thời điểm để khán giả trải nghiệm hành trình này.\r\n  Tiếp nối câu chuyện\r\nAvatar 3 sẽ tiếp tục câu chuyện từ The Way of Water, tập trung vào xung đột giữa con người và Na’vi, cùng với sự xuất hiện của tộc Người Tro Tàn. Phim sẽ khám phá các vùng đất mới trên Pandora, như khu vực núi lửa và những sinh vật độc đáo. Nội dung Avatar 3 được xây dựng để mở rộng vũ trụ Avatar. Phim James Cameron luôn biết cách giữ chân khán giả với câu chuyện hấp dẫn.\r\nSự xuất hiện của các nhân vật mới như Varang sẽ mang đến những mâu thuẫn kịch tính. Avatar: Fire and Ash không chỉ là câu chuyện về chiến tranh mà còn về sự mất mát và hòa giải. Bom tấn 2025 này sẽ làm hài lòng người hâm mộ với cốt truyện chặt chẽ. Lịch chiếu Avatar 3 là cột mốc không thể bỏ qua với fan của series.',240,'James Cameron ','assets/uploads/movies/movie_1763812825_3072.jpg','https://www.youtube.com/watch?v=rZXmSgjxpdQ','Đang chiếu','2025-11-22',0.0),(6,'Tử Chiến trên không','Sau sự kiện Giải phóng miền Nam vào năm 1975, dưới sự bảo kê của chính phủ Hoa Kỳ, nhiều đối tượng chống đối chính quyền Việt Nam đã tìm mọi cách vượt biên ra nước ngoài, trong đó có cả bằng đường hàng không. Năm 1977, chuyến bay mang số hiệu HVN-137 của chiếc Douglas DC-3 bị một nhóm không tặc bốn người chiếm giữ, chúng sát hại hai thành viên phi hành đoàn và một học viên cảnh vệ trẻ tên Minh, trước khi ép máy bay phải chuyển hướng sang nước ngoài. Sau vụ việc này, chính quyền Việt Nam đã phải tăng cường các biện pháp phòng chống cướp máy bay, bao gồm việc huấn luyện các kỹ năng chiến đấu cho lực lượng cảnh vệ hàng không.\r\n\r\nMột năm sau, Bình – một cảnh sát và chuyên viên đào tạo cảnh vệ hàng không – đã được cấp trên cho nghỉ phép để đi thăm người vợ sắp sinh của mình tên Hạnh ở thành phố Hồ Chí Minh. Anh lên chuyến bay mang số hiệu HVN-602 của chiếc Douglas DC-4, nơi anh tình cờ gặp lại người đồng nghiệp của mình là cảnh vệ Sơn cùng với hai tiếp viên hàng không tên Nhàn – người yêu của Sơn, và Tú Trinh – vợ của cơ phó Khanh, người cũng đã xuất hiện để điều khiển chuyến bay cùng với cơ trưởng Phong thay cho cơ phó Chương. Trên chuyến bay, Bình cũng kết thân với hành khách tên Hải, người cùng với vợ mình tên Phương và cậu con trai tên Đậu lên máy bay để trở về quê chịu tang, trên tay Đậu có cầm hũ cốt của người thân.\r\n\r\nKhi máy bay bắt đầu cất cánh, một nhóm không tặc do Long cầm đầu cùng ba thuộc hạ gồm Sửu – con trai của Long, và hai anh em Tí và Dần bất ngờ xuất hiện, chúng nhanh chóng tấn công phi hành đoàn, đánh Sơn trọng thương và đe dọa các hành khách phải câm nín, sau đó chúng yêu cầu phi công đưa chúng ra khỏi Việt Nam. Hải đánh lạc hướng nhóm không tặc để giúp Bình hạ gục chúng, song nỗ lực của cả hai đều bất thành và bị bọn chúng đánh gây thương tích. Long lần lượt tra tấn Nhàn và Trinh hòng ép Khanh và Phong mở cửa buồng lái, song cả Nhàn và Trinh kiên quyết từ chối. Cùng lúc đó, một chiếc máy bay tiêm kích MiG-21 được cử đến quan sát tình hình của chiếc DC-4. Phong ra hiệu cho chiếc MiG-21 lùi lại để tránh chọc giận nhóm không tặc, trong khi Khanh cho nghiêng máy bay khiến nhóm không tặc ngã xuống, bị Sơn và Bình khống chế rồi nhốt trong khoang hành lý. Chiếc DC-4 sau đó chuyển hướng bay trở lại Đà Nẵng.\r\n\r\nTrong lúc đó, Bình đã báo cáo tình hình với đại tá Quyền dưới mặt đất, biết được Long và Dần từng là những người lính của Quân lực Việt Nam Cộng hòa và đã gặp nhau trong trại cải tạo. Đại tá Quyền suy luận rằng có một kẻ đã giúp nhóm của Long làm giả giấy tờ và người đó cũng đang ở trên máy bay. Khi nghe được điều này, Phong nghi ngờ Khanh có thông đồng với nhóm không tặc nên anh không cho Khanh sử dụng tai nghe liên lạc với tiếp viên, mặc cho Khanh cố hết sức để giải thích. Trên thực tế, Hải là kẻ chủ mưu của vụ việc; hắn chỉ đạo nhóm của Long thực hiện vụ không tặc, đồng thời giả làm nạn nhân để đưa gia đình vượt biên. Lúc này, Phương đưa súng cho Hải và thúc giục chồng phải tự tay hạ sát nhóm không tặc nhằm bịt miệng chúng, song Hải đã thả nhóm không tặc ra. Khi nhóm không tặc được thả, chúng lại tiếp tục tấn công Bình và Sơn, và cả hai bên liên tục đánh trả. Trong lúc hỗn chiến, Phong bị bắn trọng thương, còn Sửu không may bị thương nặng và đã chết do mất quá nhiều máu. Cảm thấy tuyệt vọng vì không thể cứu con trai mình, Long đã kích nổ lựu đạn tự sát, còn Hải lần lượt giết chết cả Tí và Dần để bịt đầu mối.\r\n\r\nChiếc DC-4 sau đó bị hư hỏng nặng và bị mất lái do vụ nổ lựu đạn. Trước tình thế nguy cấp, Khanh và Trinh đã kịp thời điều khiển máy bay, giúp cho máy bay hạ cánh khẩn cấp an toàn và kịp thời đáp xuống sân bay Đà Nẵng, nơi được lực lượng công an bao vây. Đại tá Quyền sau đó cũng thông báo rằng cơ phó Chương bị bắt ở nhà riêng vì tội thông đồng với nhóm không tặc, hắn đã nhận tiền hối lộ của Hải để mở cửa buồng lái nhưng đã hủy bỏ chuyến bay vào phút cuối, nên Khanh là người được thế chỗ cho hắn. Khi tất cả hành khách ra khỏi máy bay, Bình bắt đầu rà soát những vật chứng còn sót lại trên máy bay, và anh tìm thấy mảnh vỡ của hũ cốt chứa một vài ma túy trắng và tàn dư của lựu đạn mà con trai của Hải luôn cầm trên tay. Đến lúc này, Bình mới biết được sự thật tàn nhẫn của Hải và yêu cầu hắn phải ra đầu thú, song hắn kiên quyết từ chối và muốn đòi lại hũ cốt đó. Cả hai sau đó lao vào ẩu đả dữ dội, làm máy bay suýt đi chệch hướng khỏi đường băng, song cuối cùng Bình cũng đã giết chết Hải.\r\n\r\nNhiều ngày sau, Bình đã đến thành phố Hồ Chí Minh để thăm Hạnh và đứa con mới sinh của mình, anh đặt tên cho con mình là Phúc và tạo thành một mái ấm gia đình đầy hạnh phúc. Trinh không may bị sảy thai và phải nhập viện hồi sức sau chuyến bay, dù rất đau lòng nhưng Khanh vẫn hứa sẽ mãi yêu cô trọn đời. Trong khi đó, Sơn – người giờ đây bị chấn thương nặng và được tiết lộ là anh trai của cảnh vệ Minh – cũng đã trao tình cảm sâu đậm với Nhàn. Bình, Phong, Khanh, Nhàn, và Trinh sau này đều được nhà nước trao tặng Huân chương Chiến công để ghi nhận những đóng góp của họ.',180,'Hàm Trần','assets/uploads/movies/movie_1763813126_4492.webp','https://www.youtube.com/watch?v=Q-Zf8KhyS6E','Đang chiếu','2025-11-21',0.0),(7,'MƯA ĐỎ','Cường là sinh viên tại Nhạc viện Hà Nội, nhưng đã từ bỏ cơ hội du học tại Học viện âm nhạc Tchaikovsky để nhập ngũ, tại đây anh gia nhập Tiểu đội 1 thuộc Tiểu đoàn K3 Tam Sơn của Quân Giải phóng. Sau khi được huấn luyện một số kỹ năng chiến đấu cơ bản, Cường cùng các tân binh khác lên đường ra mặt trận. Quang, một trung úy biệt kích dù thuộc Quân lực Việt Nam Cộng hòa, đã phải lòng một cô gái tên Hồng trong một lần giúp cô di tản khỏi vùng nguy hiểm. Tuy nhiên, Hồng chính là một y tá ủng hộ phe cách mạng, buổi tối cô lái đò đưa quân Giải phóng qua sông Thạch Hãn. Cô và Cường đã gặp nhau, cả hai dần nảy sinh tình cảm.\r\n\r\nTiểu đội 1 của Cường gồm có Tạ, Cường, Bình, Sen, Tú, Hải và Tấn. Quân Giải phóng phải bảo vệ Thành cổ Quảng Trị trước các đợt tấn công của quân Việt Nam Cộng hòa. Mọi chuyện càng căng thẳng hơn khi thời gian này Hiệp định Paris đang diễn ra. Quân Cộng hòa định cắm cờ lên nóc Thành cổ nhằm chụp ảnh tuyên truyền cho việc đàm phán tại Hiệp định Paris, Tiểu đội 1 của Cường đã ngăn cản ý định này. Cường và Quang đã đấu tay đôi với nhau, sau đó cả hai phía đều rút lui. Cường và Tú bị thương nên được đưa lên bè qua bên kia sông chữa trị. Tú không may bị trúng đạn pháo và hi sinh giữa dòng sông.\r\n\r\nSự khốc liệt của trận chiến khiến Sen trở nên điên dại. Một lần vì muốn cứu mạng Sen, Tạ bị trúng đạn và hi sinh. Hải dọn dẹp một đường cống ngầm thông ra ngoài Thành cổ, không may bị Thái, cấp dưới của Quang, bắt giữ. Thái tra tấn, bắt buộc Hải kêu gọi những chiến sĩ Giải phóng còn lại đầu hàng, nhưng Hải từ chối. Thái đã thiêu sống Hải một cách tàn nhẫn. Sen sau đó xông ra ném lựu đạn vào nhóm lính Cộng hòa, chính anh cũng bị trúng đạn và hi sinh. Những ngày sau, phía Mỹ và Việt Nam Cộng hòa cho xe tăng M48 Patton và máy bay F-4 Phantom II tấn công quân Giải phóng. Bình đã chết dưới làn đạn máy bay.\r\n\r\nNgày 16 tháng 9 năm 1972, tức ngày thứ 81 quân Giải phóng bảo vệ Thành cổ, họ được phép rút lui khỏi khu vực này. Một số lính Giải phóng, bao gồm Cường, phải ở lại ngăn cản lực lượng biệt kích dù của Quang tấn công nhằm câu giờ cho những người khác rút lui. Chỉ huy trưởng Trần Thành hi sinh trong cuộc giao chiến. Cường và Quang đối đầu nhau một lần nữa. Thái đã xả súng trong điên loạn khiến cả Cường và Quang đều chết, sau đó chính Thái bị một đồng đội là Hoàng giết chết. Trận Thành cổ Quảng Trị kết thúc với thương vong to lớn của cả hai phía. Tấn là người duy nhất sống sót của Tiểu đội 1. Trong cảnh hậu danh đề, khi đất nước Việt Nam hòa bình, Hồng và mẹ của Cường thăm mộ Cường, mẹ của Quang cũng thăm mộ Quang. Nhiều năm sau khi chiến tranh kết thúc, bản giao hưởng Mưa đỏ do Cường sáng tác lúc ở chiến tuyến được dàn nhạc trình diễn tại nhà hát.',124,'Đặng Thái Huyền','assets/uploads/movies/movie_1763813394_8280.jpg','https://www.youtube.com/watch?v=BD6PoZJdt_M','Đang chiếu','2025-09-02',0.0),(8,'Xứ Sở Các Nguyên Tố','Xứ Sở Các Nguyên Tố (Elemental) là một bộ phim hoạt hình của Disney và Pixar, lấy bối cảnh tại thành phố nơi các nguyên tố như lửa, nước, đất và không khí cùng chung sống. Câu chuyện xoay quanh nhân vật Ember, một cô gái cá tính, thông minh và mạnh mẽ. Bộ phim không chỉ mang đến một thế giới tưởng tượng tuyệt vời mà còn khám phá những khía cạnh về tình yêu, tình thân và niềm đam mê.',120,'Hoàng','assets/uploads/movies/movie_1763813916_9155.jpg','https://www.youtube.com/watch?v=maq_YK88Xnw','Sắp chiếu','2026-12-30',0.0);
/*!40000 ALTER TABLE `movies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotions` (
  `PromotionID` int NOT NULL AUTO_INCREMENT,
  `Code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DiscountValue` decimal(10,2) DEFAULT '0.00',
  `DiscountPercent` int DEFAULT '0',
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  PRIMARY KEY (`PromotionID`),
  UNIQUE KEY `Code` (`Code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions`
--

LOCK TABLES `promotions` WRITE;
/*!40000 ALTER TABLE `promotions` DISABLE KEYS */;
INSERT INTO `promotions` VALUES (1,'THANHVIENMOI',20000.00,10,'2025-11-21','2025-12-25');
/*!40000 ALTER TABLE `promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `screens`
--

DROP TABLE IF EXISTS `screens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `screens` (
  `ScreenID` int NOT NULL AUTO_INCREMENT,
  `TheaterID` int NOT NULL,
  `Name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Capacity` int DEFAULT '0',
  PRIMARY KEY (`ScreenID`),
  KEY `TheaterID` (`TheaterID`),
  CONSTRAINT `screens_ibfk_1` FOREIGN KEY (`TheaterID`) REFERENCES `theaters` (`TheaterID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `screens`
--

LOCK TABLES `screens` WRITE;
/*!40000 ALTER TABLE `screens` DISABLE KEYS */;
INSERT INTO `screens` VALUES (1,1,'Phòng 01',50),(2,1,'Phòng 1 - Standard',100),(3,2,'Đan Phượng P1',100),(4,3,'DD V1',90);
/*!40000 ALTER TABLE `screens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seats`
--

DROP TABLE IF EXISTS `seats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seats` (
  `SeatID` int NOT NULL AUTO_INCREMENT,
  `ScreenID` int NOT NULL,
  `SeatTypeID` int NOT NULL,
  `RowName` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SeatNumber` int NOT NULL,
  PRIMARY KEY (`SeatID`),
  KEY `ScreenID` (`ScreenID`),
  KEY `SeatTypeID` (`SeatTypeID`),
  CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`ScreenID`) REFERENCES `screens` (`ScreenID`) ON DELETE CASCADE,
  CONSTRAINT `seats_ibfk_2` FOREIGN KEY (`SeatTypeID`) REFERENCES `seattypes` (`SeatTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=341 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seats`
--

LOCK TABLES `seats` WRITE;
/*!40000 ALTER TABLE `seats` DISABLE KEYS */;
INSERT INTO `seats` VALUES (1,1,1,'A',1),(2,1,1,'A',2),(3,1,1,'A',3),(4,1,1,'A',4),(5,1,1,'A',5),(6,1,1,'A',6),(7,1,1,'A',7),(8,1,1,'A',8),(9,1,1,'A',9),(10,1,1,'A',10),(11,1,1,'B',1),(12,1,1,'B',2),(13,1,1,'B',3),(14,1,1,'B',4),(15,1,1,'B',5),(16,1,1,'B',6),(17,1,1,'B',7),(18,1,1,'B',8),(19,1,1,'B',9),(20,1,1,'B',10),(21,1,1,'C',1),(22,1,1,'C',2),(23,1,1,'C',3),(24,1,1,'C',4),(25,1,1,'C',5),(26,1,1,'C',6),(27,1,1,'C',7),(28,1,1,'C',8),(29,1,1,'C',9),(30,1,1,'C',10),(31,1,2,'D',1),(32,1,2,'D',2),(33,1,2,'D',3),(34,1,2,'D',4),(35,1,2,'D',5),(36,1,2,'D',6),(37,1,2,'D',7),(38,1,2,'D',8),(39,1,2,'D',9),(40,1,2,'D',10),(41,1,2,'E',1),(42,1,2,'E',2),(43,1,2,'E',3),(44,1,2,'E',4),(45,1,2,'E',5),(46,1,2,'E',6),(47,1,2,'E',7),(48,1,2,'E',8),(49,1,2,'E',9),(50,1,2,'E',10),(51,2,1,'A',1),(52,2,1,'A',2),(53,2,1,'A',3),(54,2,1,'A',4),(55,2,1,'A',5),(56,2,1,'A',6),(57,2,1,'A',7),(58,2,1,'A',8),(59,2,1,'A',9),(60,2,1,'A',10),(61,2,1,'B',1),(62,2,1,'B',2),(63,2,1,'B',3),(64,2,1,'B',4),(65,2,1,'B',5),(66,2,1,'B',6),(67,2,1,'B',7),(68,2,1,'B',8),(69,2,1,'B',9),(70,2,1,'B',10),(71,2,1,'C',1),(72,2,1,'C',2),(73,2,1,'C',3),(74,2,1,'C',4),(75,2,1,'C',5),(76,2,1,'C',6),(77,2,1,'C',7),(78,2,1,'C',8),(79,2,1,'C',9),(80,2,1,'C',10),(81,2,2,'D',1),(82,2,2,'D',2),(83,2,2,'D',3),(84,2,2,'D',4),(85,2,2,'D',5),(86,2,2,'D',6),(87,2,2,'D',7),(88,2,2,'D',8),(89,2,2,'D',9),(90,2,2,'D',10),(91,2,2,'E',1),(93,2,2,'E',3),(94,2,2,'E',4),(95,2,2,'E',5),(96,2,2,'E',6),(97,2,2,'E',7),(98,2,2,'E',8),(99,2,2,'E',9),(101,2,2,'F',1),(102,2,2,'F',2),(103,2,2,'F',3),(104,2,2,'F',4),(105,2,2,'F',5),(106,2,2,'F',6),(107,2,2,'F',7),(108,2,2,'F',8),(109,2,2,'F',9),(110,2,2,'F',10),(111,2,2,'G',1),(112,2,2,'G',2),(113,2,2,'G',3),(114,2,2,'G',4),(115,2,2,'G',5),(116,2,2,'G',6),(117,2,2,'G',7),(118,2,2,'G',8),(119,2,2,'G',9),(120,2,2,'G',10),(131,2,2,'I',1),(132,2,2,'I',2),(133,2,2,'I',3),(134,2,2,'I',4),(135,2,2,'I',5),(136,2,2,'I',6),(137,2,2,'I',7),(138,2,2,'I',8),(139,2,2,'I',9),(140,2,2,'I',10),(141,2,2,'J',1),(142,2,2,'J',2),(143,2,2,'J',3),(144,2,2,'J',4),(145,2,2,'J',5),(146,2,2,'J',6),(147,2,2,'J',7),(148,2,2,'J',8),(149,2,2,'J',9),(150,2,2,'J',10),(151,3,1,'A',1),(152,3,1,'A',2),(153,3,1,'A',3),(154,3,1,'A',4),(155,3,1,'A',5),(156,3,1,'A',6),(157,3,1,'A',7),(158,3,1,'A',8),(159,3,1,'A',9),(160,3,1,'A',10),(161,3,1,'B',1),(162,3,1,'B',2),(163,3,1,'B',3),(164,3,1,'B',4),(165,3,1,'B',5),(166,3,1,'B',6),(167,3,1,'B',7),(168,3,1,'B',8),(169,3,1,'B',9),(170,3,1,'B',10),(171,3,1,'C',1),(172,3,1,'C',2),(173,3,1,'C',3),(174,3,1,'C',4),(175,3,1,'C',5),(176,3,1,'C',6),(177,3,1,'C',7),(178,3,1,'C',8),(179,3,1,'C',9),(180,3,1,'C',10),(181,3,2,'D',1),(182,3,2,'D',2),(183,3,2,'D',3),(184,3,2,'D',4),(185,3,2,'D',5),(186,3,2,'D',6),(187,3,2,'D',7),(188,3,2,'D',8),(189,3,2,'D',9),(190,3,2,'D',10),(191,3,2,'E',1),(192,3,2,'E',2),(193,3,2,'E',3),(194,3,2,'E',4),(195,3,2,'E',5),(196,3,2,'E',6),(197,3,2,'E',7),(198,3,2,'E',8),(199,3,2,'E',9),(200,3,2,'E',10),(201,3,2,'F',1),(202,3,2,'F',2),(203,3,2,'F',3),(204,3,2,'F',4),(205,3,2,'F',5),(206,3,2,'F',6),(207,3,2,'F',7),(208,3,2,'F',8),(209,3,2,'F',9),(210,3,2,'F',10),(211,3,2,'G',1),(212,3,2,'G',2),(213,3,2,'G',3),(214,3,2,'G',4),(215,3,2,'G',5),(216,3,2,'G',6),(217,3,2,'G',7),(218,3,2,'G',8),(219,3,2,'G',9),(220,3,2,'G',10),(221,3,2,'H',1),(222,3,2,'H',2),(223,3,2,'H',3),(224,3,2,'H',4),(225,3,2,'H',5),(226,3,2,'H',6),(227,3,2,'H',7),(228,3,2,'H',8),(229,3,2,'H',9),(230,3,2,'H',10),(231,3,2,'I',1),(232,3,2,'I',2),(233,3,2,'I',3),(234,3,2,'I',4),(235,3,2,'I',5),(236,3,2,'I',6),(237,3,2,'I',7),(238,3,2,'I',8),(239,3,2,'I',9),(240,3,2,'I',10),(241,3,2,'J',1),(242,3,2,'J',2),(243,3,2,'J',3),(244,3,2,'J',4),(245,3,2,'J',5),(246,3,2,'J',6),(247,3,2,'J',7),(248,3,2,'J',8),(249,3,2,'J',9),(250,3,2,'J',10),(251,4,1,'A',1),(252,4,1,'A',2),(253,4,1,'A',3),(254,4,1,'A',4),(255,4,1,'A',5),(256,4,1,'A',6),(257,4,1,'A',7),(258,4,1,'A',8),(259,4,1,'A',9),(260,4,1,'A',10),(261,4,1,'B',1),(262,4,1,'B',2),(263,4,1,'B',3),(264,4,1,'B',4),(265,4,1,'B',5),(266,4,1,'B',6),(267,4,1,'B',7),(268,4,1,'B',8),(269,4,1,'B',9),(270,4,1,'B',10),(271,4,1,'C',1),(272,4,1,'C',2),(273,4,1,'C',3),(274,4,1,'C',4),(275,4,1,'C',5),(276,4,1,'C',6),(277,4,1,'C',7),(278,4,1,'C',8),(279,4,1,'C',9),(280,4,1,'C',10),(281,4,2,'D',1),(282,4,2,'D',2),(283,4,2,'D',3),(284,4,2,'D',4),(285,4,2,'D',5),(286,4,2,'D',6),(287,4,2,'D',7),(288,4,2,'D',8),(289,4,2,'D',9),(290,4,2,'D',10),(291,4,2,'E',1),(292,4,2,'E',2),(293,4,2,'E',3),(294,4,2,'E',4),(295,4,2,'E',5),(296,4,2,'E',6),(297,4,2,'E',7),(298,4,2,'E',8),(299,4,2,'E',9),(300,4,2,'E',10),(301,4,2,'F',1),(302,4,2,'F',2),(303,4,2,'F',3),(304,4,2,'F',4),(305,4,2,'F',5),(306,4,2,'F',6),(307,4,2,'F',7),(308,4,2,'F',8),(309,4,2,'F',9),(310,4,2,'F',10),(311,4,2,'G',1),(312,4,2,'G',2),(313,4,2,'G',3),(314,4,2,'G',4),(315,4,2,'G',5),(316,4,2,'G',6),(317,4,2,'G',7),(318,4,2,'G',8),(319,4,2,'G',9),(320,4,2,'G',10),(321,4,2,'H',1),(322,4,2,'H',2),(323,4,2,'H',3),(324,4,2,'H',4),(325,4,2,'H',5),(326,4,2,'H',6),(327,4,2,'H',7),(328,4,2,'H',8),(329,4,2,'H',9),(330,4,2,'H',10),(331,4,2,'I',1),(332,4,2,'I',2),(333,4,2,'I',3),(334,4,2,'I',4),(335,4,2,'I',5),(336,4,2,'I',6),(337,4,2,'I',7),(338,4,2,'I',8),(339,4,2,'I',9),(340,4,2,'I',10);
/*!40000 ALTER TABLE `seats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seattypes`
--

DROP TABLE IF EXISTS `seattypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seattypes` (
  `SeatTypeID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PriceSurcharge` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`SeatTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seattypes`
--

LOCK TABLES `seattypes` WRITE;
/*!40000 ALTER TABLE `seattypes` DISABLE KEYS */;
INSERT INTO `seattypes` VALUES (1,'Ghế Thường',0.00),(2,'Ghế VIP',40000.00);
/*!40000 ALTER TABLE `seattypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `showtimes`
--

DROP TABLE IF EXISTS `showtimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `showtimes` (
  `ShowtimeID` int NOT NULL AUTO_INCREMENT,
  `MovieID` int NOT NULL,
  `ScreenID` int NOT NULL,
  `StartTime` datetime NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`ShowtimeID`),
  UNIQUE KEY `ScreenID` (`ScreenID`,`StartTime`),
  KEY `MovieID` (`MovieID`),
  CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`MovieID`) REFERENCES `movies` (`MovieID`) ON DELETE CASCADE,
  CONSTRAINT `showtimes_ibfk_2` FOREIGN KEY (`ScreenID`) REFERENCES `screens` (`ScreenID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `showtimes`
--

LOCK TABLES `showtimes` WRITE;
/*!40000 ALTER TABLE `showtimes` DISABLE KEYS */;
INSERT INTO `showtimes` VALUES (1,1,2,'2025-11-21 22:00:00',90000.00),(2,1,1,'2025-11-22 15:00:00',90000.00),(4,1,2,'2025-11-22 19:00:00',90000.00),(7,1,2,'2025-11-23 16:00:00',90000.00),(8,5,3,'2025-11-23 13:00:00',90000.00),(9,6,3,'2025-11-23 20:00:00',90000.00),(10,7,3,'2026-11-23 19:00:00',90000.00);
/*!40000 ALTER TABLE `showtimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theaters`
--

DROP TABLE IF EXISTS `theaters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `theaters` (
  `TheaterID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `City` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`TheaterID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theaters`
--

LOCK TABLES `theaters` WRITE;
/*!40000 ALTER TABLE `theaters` DISABLE KEYS */;
INSERT INTO `theaters` VALUES (1,'Cinema Hà Đông','Số 1 Phố Xốm','Hà Nội','',''),(2,'Cimena Đan Phượng','số 1 Đan Phượng','Hà Nội','',''),(3,'Cimena Đống Đa','số 1 Đống Đa','Hà Nội','','');
/*!40000 ALTER TABLE `theaters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `TicketID` int NOT NULL AUTO_INCREMENT,
  `BookingID` int NOT NULL,
  `ShowtimeID` int NOT NULL,
  `SeatID` int NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`TicketID`),
  KEY `BookingID` (`BookingID`),
  KEY `ShowtimeID` (`ShowtimeID`),
  KEY `SeatID` (`SeatID`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `bookings` (`BookingID`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`ShowtimeID`) REFERENCES `showtimes` (`ShowtimeID`),
  CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`SeatID`) REFERENCES `seats` (`SeatID`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,1,1,74,90000.00),(2,1,1,85,110000.00),(3,1,1,96,110000.00),(4,2,1,82,110000.00),(5,2,1,83,110000.00),(6,3,1,77,90000.00),(7,3,1,78,90000.00),(8,4,1,97,110000.00),(9,4,1,98,110000.00),(10,5,2,35,110000.00),(11,5,2,36,110000.00),(12,6,2,27,90000.00),(13,6,2,28,90000.00),(16,8,4,98,110000.00),(17,8,4,97,110000.00),(18,9,4,102,110000.00),(19,9,4,103,110000.00),(20,10,4,85,130000.00),(27,13,10,184,130000.00),(28,13,10,185,130000.00),(29,14,8,184,130000.00),(30,14,8,185,130000.00),(33,16,8,183,130000.00),(34,16,8,182,130000.00),(37,18,8,186,130000.00),(38,18,8,187,130000.00),(39,19,8,188,130000.00),(40,19,8,189,130000.00),(43,21,8,187,130000.00),(44,21,8,188,130000.00),(49,24,10,186,130000.00),(50,24,10,187,130000.00),(51,25,8,187,130000.00);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `FullName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhoneNumber` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PasswordHash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Role` enum('User','Admin') COLLATE utf8mb4_unicode_ci DEFAULT 'User',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin System','admin@gmail.com','0909000000','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Admin','2025-11-21 12:33:11'),(2,'Khách Hàng A','khach@gmail.com','0909111222','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','User','2025-11-21 12:33:11'),(3,'Thi Hoàng','thihoang139hn@icloud.com','099999997','$2y$10$bzNRD.Gu6OQaWkuth7XDbeGf6Gf3mbvDmzT9iLrfcfj5T.6T82MVq','Admin','2025-11-21 12:36:34'),(4,'Minh Sơn','son2005@gmail.com','097382114','$2y$10$Ay4kFNlfUHSyuqnFCmp.quUUBr1sEH5PjEH6oS0/2UuGkb3m89KbS','User','2025-11-21 12:48:01'),(5,'Nguyễn Minh Sơn','son123@gmail.com','0125468520','$2y$10$zkYCnq7QHTpdRve/BTfzguIdx6DcZfElZDVZp0yqASxYWUo9mOtY6','User','2025-11-22 07:12:06'),(6,'Hoàng Thi','thine@gmail.com','09999966','$2y$10$Uth95rtesnse/2/G932fEeQvCz/wUhESx3OmyMPuAzjpM.yxC6pEW','User','2025-11-22 16:31:07');
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

-- Dump completed on 2025-11-23  1:52:53
