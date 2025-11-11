-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 07:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shoespace`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_active` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `user_id`, `created_at`, `last_active`) VALUES
(2, 2, '2025-10-22 03:53:23', NULL),
(4, 3, '2025-10-31 14:56:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_label` varchar(30) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `size_label`, `quantity`, `added_at`) VALUES
(24, 1, 3, '40', 1, '2025-10-23 02:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Running', 'running', 'รองเท้าวิ่งสำหรับออกกำลังกายและแข่งขัน', '2025-10-07 04:13:50'),
(2, 'Basketball', 'basketball', 'รองเท้าบาสเกตบอล เหมาะสำหรับการเล่นในสนาม', '2025-10-07 04:13:50'),
(3, 'Fashion', 'fashion', 'รองเท้าแฟชั่น ดีไซน์ทันสมัย', '2025-10-07 04:13:50'),
(4, 'Casual', 'casual', 'รองเท้าผ้าใบ ใส่เที่ยว ใส่ได้ทุกวัน', '2025-10-07 04:13:50'),
(5, 'Sandals', 'sandals', 'รองเท้าแตะ สวมสบาย', '2025-10-07 04:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `status`, `total_amount`, `shipping_address`, `payment_method`, `tracking_number`) VALUES
(1, 1, '2025-10-07 04:13:50', 'paid', 8500.00, 'ที่อยู่ตัวอย่าง จังหวัด ตัวอย่าง', 'PromptPay', NULL),
(10, 2, '2025-10-23 02:20:21', 'pending', 4280.00, 'a', 'COD', NULL),
(11, 2, '2025-10-23 02:21:27', 'pending', 4280.00, '.', 'Bank Transfer', NULL),
(12, 3, '2025-10-23 02:34:53', 'cancelled', 880.00, '77/77 หมู่ 51 ต.เอวดี อ.เอวเคล็ด จ.นอนน้อย', 'COD', NULL),
(13, 3, '2025-10-23 02:43:46', 'cancelled', 3580.00, '77/77 หมู่ 51 ต.เอวดี อ.เอวเคล็ด จ.นอนน้อย', 'COD', NULL),
(15, 3, '2025-10-23 02:55:11', 'cancelled', 880.00, '77/77 หมู่ 51 ต.เอวดี อ.เอวเคล็ด จ.นอนน้อย', 'COD', NULL),
(17, 4, '2025-10-23 03:48:02', 'pending', 880.00, 'บ้านตันเอง', 'Bank Transfer', NULL),
(19, 3, '2025-10-31 15:41:38', 'pending', 4280.00, '77/77 หมู่ 51 ต.เอวดี อ.เอวเคล็ด จ.นอนน้อย', 'Bank Transfer', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_label` varchar(30) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`unit_price` * `quantity`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`detail_id`, `order_id`, `product_id`, `size_label`, `quantity`, `unit_price`) VALUES
(1, 1, 1, '39', 1, 3500.00),
(2, 1, 2, '41', 1, 4200.00),
(3, 10, 2, '40', 1, 4200.00),
(4, 11, 2, '40', 1, 4200.00),
(5, 12, 5, 'Free', 1, 800.00),
(6, 13, 1, '40', 1, 3500.00),
(7, 15, 5, 'Free', 1, 800.00),
(8, 17, 5, 'Free', 1, 800.00),
(9, 19, 2, '40', 1, 4200.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `order_id`, `amount`, `method`, `status`, `paid_at`, `details`) VALUES
(1, 1, 7700.00, 'PromptPay', 'completed', '2025-10-07 04:13:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `name`, `sku`, `brand`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(1, 'Nike Air Zoom', 'NIK-AZ-001', 'Nike', 'รองเท้าวิ่งน้ำหนักเบา เหมาะสำหรับการซ้อมและแข่งขัน', 3500.00, 15, 'nike_air_zoom.jpg', '2025-10-07 04:13:50'),
(2, 'Adidas Harden', 'ADD-HDN-002', 'Adidas', 'รองเท้าบาส ดีดตัวดี รองรับแรงกระแทก', 4200.00, 10, 'adidas_harden.jpg', '2025-10-07 04:13:50'),
(3, 'Converse All Star', 'CON-AS-003', 'Converse', 'รองเท้าผ้าใบคลาสสิค ใส่สบาย', 2500.00, 30, 'converse_allstar.jpg', '2025-10-07 04:13:50'),
(4, 'Vans Old Skool', 'VNS-OS-004', 'Vans', 'รองเท้าแฟชั่น สไตล์สตรีท', 2800.00, 18, 'vans_old_skool.jpg', '2025-10-07 04:13:50'),
(5, 'Ipanema Flip', 'IPN-FLP-005', 'Ipanema', 'รองเท้าแตะสำหรับทุกวัน น้ำหนักเบา', 800.00, 20, 'ipanema_flip.jpg', '2025-10-07 04:13:50'),
(6, 'Asics Gel-Kayano 30', 'ASC-GK-030', 'Asics', 'รองเท้าวิ่งสำหรับคนเท้าแบน เน้นความมั่นคง', 5200.00, 25, 'asics_gel_kayano.jpg', '2025-10-18 15:27:29'),
(7, 'Brooks Ghost 15', 'BRK-GH-015', 'Brooks', 'รองเท้าวิ่งสายซัพพอร์ต นุ่มสบาย เหมาะกับทุกวัน', 4800.00, 30, 'brooks_ghost.jpg', '2025-10-18 15:27:29'),
(8, 'New Balance Fresh Foam 880', 'NB-FF-880', 'New Balance', 'รองเท้าวิ่งสุดคลาสสิค สมดุลระหว่างความนุ่มและมั่นคง', 4500.00, 22, 'nb_freshfoam_880.jpg', '2025-10-18 15:27:29'),
(9, 'Hoka Clifton 9', 'HOK-CL-009', 'Hoka', 'รองเท้าวิ่งน้ำหนักเบา ซัพพอร์ตดีเยี่ยม ลดแรงกระแทก', 5500.00, 18, 'hoka_clifton.jpg', '2025-10-18 15:27:29'),
(10, 'Saucony Kinvara 14', 'SAU-KV-014', 'Saucony', 'รองเท้าวิ่งสำหรับทำความเร็ว คล่องตัวสูง', 4200.00, 28, 'saucony_kinvara.jpg', '2025-10-18 15:27:29'),
(11, 'Mizuno Wave Rider 27', 'MIZ-WR-027', 'Mizuno', 'รองเท้าวิ่งทนทาน ตอบสนองดี เหมาะกับการซ้อมยาว', 4900.00, 15, 'mizuno_waverider.jpg', '2025-10-18 15:27:29'),
(12, 'On Cloudsurfer', 'ON-CS-001', 'On Running', 'รองเท้าวิ่งเทคโนโลยี CloudTec Phase นุ่มสบายเหมือนวิ่งบนเมฆ', 5800.00, 20, 'on_cloudsurfer.jpg', '2025-10-18 15:27:29'),
(13, 'Nike LeBron XXI', 'NIK-LB-021', 'Nike', 'รองเท้าบาสซิกเนเจอร์ล่าสุดของ LeBron James', 7200.00, 12, 'nike_lebron_xxi.jpg', '2025-10-18 15:27:29'),
(14, 'Jordan Zion 3', 'JOR-ZN-003', 'Jordan', 'รองเท้าบาสที่เน้นการล็อคดาวน์เท้าและความคล่องตัว', 4800.00, 15, 'jordan_zion.jpg', '2025-10-18 15:27:29'),
(15, 'Puma MB.03', 'PUM-MB-003', 'Puma', 'รองเท้าบาสจาก LaMelo Ball ดีไซน์โดดเด่น สีสันจัดจ้าน', 5000.00, 18, 'puma_mb03.jpg', '2025-10-18 15:27:29'),
(16, 'Under Armour Curry 11', 'UA-SC-011', 'Under Armour', 'รองเท้าบาสเกาะพื้นดีเลิศ ตอบสนองดีเยี่ยมตามสไตล์ Stephen Curry', 5500.00, 20, 'ua_curry_11.jpg', '2025-10-18 15:27:29'),
(17, 'Adidas Dame 8', 'ADD-DM-008', 'Adidas', 'รองเท้าบาสที่เน้นความเบาและความเร็วในสนาม', 4600.00, 25, 'adidas_dame_8.jpg', '2025-10-18 15:27:29'),
(18, 'New Balance TWO WXY v4', 'NB-TW-004', 'New Balance', 'รองเท้าบาสอเนกประสงค์ เหมาะสำหรับผู้เล่นทุกตำแหน่ง', 4300.00, 22, 'nb_twowxy_v4.jpg', '2025-10-18 15:27:29'),
(19, 'Nike Kyrie Low 5', 'NIK-KL-005', 'Nike', 'รองเท้าบาสสำหรับสายสปีด เน้นการคอนโทรลและการเปลี่ยนทิศทาง', 4500.00, 19, 'nike_kyrie_low.jpg', '2025-10-18 15:27:29'),
(20, 'Adidas Stan Smith', 'ADD-SS-001', 'Adidas', 'รองเท้าสนีกเกอร์คลาสสิค เรียบง่ายแต่มีสไตล์', 3200.00, 40, 'adidas_stansmith.jpg', '2025-10-18 15:27:29'),
(21, 'Nike Air Force 1 07', 'NIK-AF-001', 'Nike', 'สนีกเกอร์ไอคอนตลอดกาล ขาวล้วนสุดฮิต', 3800.00, 50, 'nike_airforce_1.jpg', '2025-10-18 15:27:29'),
(22, 'Puma Suede Classic', 'PUM-SC-001', 'Puma', 'รองเท้าหนังกลับสุดคลาสสิคจากยุค 60s', 2800.00, 35, 'puma_suede.jpg', '2025-10-18 15:27:29'),
(23, 'Reebok Club C 85', 'RBK-CC-085', 'Reebok', 'รองเท้าสไตล์เทนนิสวินเทจ เรียบง่ายและดูดี', 3000.00, 33, 'reebok_clubc.jpg', '2025-10-18 15:27:29'),
(24, 'Veja V-10', 'VEJ-V-010', 'Veja', 'สนีกเกอร์รักษ์โลก ดีไซน์มินิมอลจากฝรั่งเศส', 4500.00, 15, 'veja_v10.jpg', '2025-10-18 15:27:29'),
(25, 'Balenciaga Triple S', 'BAL-TS-001', 'Balenciaga', 'สนีกเกอร์สไตล์ Chunky สุดหรูหรา', 35000.00, 5, 'balenciaga_triples.jpg', '2025-10-18 15:27:29'),
(26, 'Alexander McQueen Oversized', 'AMQ-OV-001', 'McQueen', 'สนีกเกอร์พื้นหนา ดีไซน์เรียบหรูจากอังกฤษ', 18000.00, 8, 'mcqueen_oversized.jpg', '2025-10-18 15:27:29'),
(27, 'Gucci Ace Bee', 'GUC-AB-001', 'Gucci', 'สนีกเกอร์หนังลายผึ้ง ซิกเนเจอร์ของแบรนด์', 25000.00, 7, 'gucci_ace_bee.jpg', '2025-10-18 15:27:29'),
(28, 'Onitsuka Tiger Mexico 66', 'ONT-MX-066', 'Onitsuka Tiger', 'รองเท้าลำลองสไตล์วินเทจจากญี่ปุ่น', 3900.00, 45, 'onitsuka_mexico66.jpg', '2025-10-18 15:27:29'),
(29, 'Clarks Desert Boot', 'CLK-DB-001', 'Clarks', 'รองเท้าบูทหนังกลับทรงคลาสสิค ใส่ได้หลายโอกาส', 4800.00, 25, 'clarks_desert_boot.jpg', '2025-10-18 15:27:29'),
(30, 'Dr. Martens 1461', 'DM-1461-001', 'Dr. Martens', 'รองเท้าหนัง 3 รู ทนทานและมีเอกลักษณ์', 5200.00, 20, 'drmartens_1461.jpg', '2025-10-18 15:27:29'),
(31, 'Birkenstock Arizona', 'BIR-AZ-001', 'Birkenstock', 'รองเท้าแตะสองสายเพื่อสุขภาพจากเยอรมนี', 3500.00, 30, 'birkenstock_arizona.jpg', '2025-10-18 15:27:29'),
(32, 'Crocs Classic Clog', 'CRC-CC-001', 'Crocs', 'รองเท้าลำลองน้ำหนักเบา ใส่สบายขั้นสุด', 1800.00, 60, 'crocs_classic.jpg', '2025-10-18 15:27:29'),
(33, 'TOMS Alpargata', 'TOM-AL-001', 'TOMS', 'รองเท้าผ้าสวมง่าย สบายๆ เหมาะกับวันพักผ่อน', 2200.00, 40, 'toms_alpargata.jpg', '2025-10-18 15:27:29'),
(34, 'Lacoste L.12.12', 'LAC-L12-001', 'Lacoste', 'รองเท้าผ้าใบสไตล์สปอร์ต เรียบหรูดูดี', 3600.00, 28, 'lacoste_l1212.jpg', '2025-10-18 15:27:29'),
(35, 'Sperry Top-Sider', 'SPY-TS-001', 'Sperry', 'รองเท้าทรง Boat Shoes สุดคลาสสิค', 4100.00, 22, 'sperry_topsider.jpg', '2025-10-18 15:27:29'),
(36, 'Jordan NOLA', 'JD-NL-004', 'NIKE', 'รองเท้าแตะผู้หญิงแบบสวม', 1400.00, 19, 'Jordan-NOLA.jpg', '2025-10-21 01:41:20'),
(37, 'Nike Victori One Slide', 'NIK-VIC-001', 'Nike', 'รองเท้าแตะแบบสวม นุ่มสบาย โลโก้ Nike เด่นชัด', 1200.00, 50, 'nike_victori_slide.jpg', '2025-10-21 01:57:47'),
(38, 'Adidas Adilette Comfort', 'ADD-ADI-001', 'Adidas', 'รองเท้าแตะพื้นนุ่มพิเศษ Cloudfoam รองรับแรงกระแทก', 1500.00, 60, 'adidas_adilette.jpg', '2025-10-21 01:57:47'),
(39, 'Havaianas Top', 'HAV-TOP-001', 'Havaianas', 'รองเท้าแตะหูคีบสุดคลาสสิคจากบราซิล ทนทาน สีสันสดใส', 990.00, 100, 'havaianas_top.jpg', '2025-10-21 01:57:47'),
(40, 'Birkenstock Gizeh', 'BIR-GIZ-001', 'Birkenstock', 'รองเท้าแตะเพื่อสุขภาพรุ่น Gizeh แบบหูคีบ ดีไซน์เรียบหรู', 4200.00, 30, 'birkenstock_gizeh.jpg', '2025-10-21 01:57:47'),
(41, 'Crocs LiteRide 360 Clog', 'CRC-LR-360', 'Crocs', 'รองเท้า Crocs รุ่นใหม่ นวัตกรรม LiteRide นุ่มสบายกว่าเดิม', 2400.00, 40, 'crocs_literide.jpg', '2025-10-21 01:57:47'),
(42, 'Under Armour Ignite Slide', 'UA-IGN-001', 'Under Armour', 'รองเท้าแตะปรับระดับได้ พื้น Memory Foam นุ่มสบายเท้า', 1800.00, 40, 'ua_ignite_slide.jpg', '2025-10-21 01:57:47'),
(43, 'Puma Softride Slide', 'PUM-SR-001', 'Puma', 'รองเท้าแตะพื้น Softride นุ่มเด้ง ดีไซน์สปอร์ต', 1300.00, 50, 'puma_softride_slide.jpg', '2025-10-21 01:57:47'),
(44, 'Skechers GOwalk Arch Fit Sandal', 'SKE-GO-001', 'Skechers', 'รองเท้าแตะรัดส้น เทคโนโลยี Arch Fit รองรับอุ้งเท้า', 2500.00, 30, 'skechers_gowalk_sandal.jpg', '2025-10-21 01:57:47'),
(45, 'Gucci GG Slide Sandal', 'GUC-SL-001', 'Gucci', 'รองเท้าแตะยางลาย GG สไตล์หรูหราจาก Gucci', 14500.00, 10, 'gucci_gg_slide.jpg', '2025-10-21 01:57:47'),
(46, 'Teva Original Universal', 'TEV-OU-001', 'Teva', 'รองเท้าแตะรัดส้นสไตล์สปอร์ต ลุยน้ำได้ แห้งไว', 2100.00, 35, 'teva_original.jpg', '2025-10-21 01:57:47');

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`product_id`, `category_id`) VALUES
(1, 1),
(2, 2),
(3, 4),
(4, 3),
(5, 5),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 3),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(25, 3),
(26, 3),
(27, 3),
(28, 4),
(29, 4),
(30, 4),
(31, 4),
(32, 4),
(33, 4),
(34, 4),
(35, 4),
(36, 5),
(37, 5),
(38, 5),
(39, 5),
(40, 5),
(41, 5),
(42, 5),
(43, 5),
(44, 5),
(45, 5),
(46, 5);

-- --------------------------------------------------------

--
-- Table structure for table `product_size`
--

CREATE TABLE `product_size` (
  `size_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_label` varchar(30) NOT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_size`
--

INSERT INTO `product_size` (`size_id`, `product_id`, `size_label`, `stock`) VALUES
(1, 1, '38', 5),
(2, 1, '39', 6),
(3, 1, '40', 4),
(4, 2, '40', 1),
(5, 2, '41', 6),
(6, 3, '38', 10),
(7, 3, '39', 10),
(8, 4, '41', 5),
(9, 4, '42', 5),
(10, 5, 'Free', 19),
(12, 22, '40', 3),
(13, 22, '42', 35),
(14, 22, '41', 13);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `fullname`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'user1', '$2y$10$examplehashplaceholder', 'สมชาย ใจดี', 'somchai@example.com', '0812345678', 'กรุงเทพมหานคร', '2025-10-07 04:13:50'),
(2, 'a', '123456', 'แอดมิน คนเก่า', 'admin.new@example.com', '7400', '', '2025-10-22 03:53:23'),
(3, 'miniice', '$2y$10$TIO8OF3zotTlu9M0AApf8OdSKk/8G2S/WKlgsRG/lN8Au9oXdQgg6', 'Thanarat San-on', 'miniicegamer@gmail.com', '0610483701', '77/77 หมู่ 51 ต.เอวดี อ.เอวเคล็ด จ.นอนน้อย', '2025-10-22 13:20:00'),
(4, 'prayut', '$2y$10$YarOsas7XGZTsp9Y3On.me.3z95riFWfFA95ze4bNHngb.wZ8pJXC', 'ประยูน แจ่มจุง', 'prayut@gspot.www', '0999999999', 'บ้านตันเอง', '2025-10-23 03:45:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `uk_user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `fk_od_order` (`order_id`),
  ADD KEY `fk_od_product` (`product_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payment_order` (`order_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `fk_pc_category` (`category_id`);

--
-- Indexes for table `product_size`
--
ALTER TABLE `product_size`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `fk_ps_product` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `product_size`
--
ALTER TABLE `product_size`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `fk_od_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_od_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `fk_pc_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pc_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_size`
--
ALTER TABLE `product_size`
  ADD CONSTRAINT `fk_ps_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
