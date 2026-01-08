-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-01-2026 a las 21:26:55
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `thehearth`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_log`
--

CREATE TABLE `admin_log` (
  `log_id` int(11) NOT NULL,
  `admin_user` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `affected_entity` varchar(50) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin_log`
--

INSERT INTO `admin_log` (`log_id`, `admin_user`, `action`, `affected_entity`, `timestamp`) VALUES
(1, 1, 'Updated Product', 'Product: Test Simon  (ID: 40)', '2026-01-08 16:39:17'),
(2, 1, 'Updated Order Status', 'Order #5 changed to delivered', '2026-01-08 16:40:17'),
(3, 1, 'Created Product', 'Product: Prueba Logs', '2026-01-08 16:40:59'),
(4, 1, 'Deleted Product', 'Product ID: 41', '2026-01-08 16:41:22'),
(5, 1, 'Updated Order Status', 'Order #2 changed to cancelled', '2026-01-08 16:57:19'),
(6, 1, 'Updated Order Status', 'Order #3 changed to pending', '2026-01-08 16:57:30'),
(7, 1, 'Updated User Role', 'User ID: 2 changed to admin', '2026-01-08 18:31:22'),
(8, 1, 'Updated User Role', 'User ID: 2 changed to customer', '2026-01-08 18:31:40'),
(9, 1, 'Updated Product', 'Product: Test Simon  (ID: 40)', '2026-01-08 19:57:56'),
(10, 1, 'Updated Order Status', 'Order #11 changed to delivered', '2026-01-08 19:58:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `allergen`
--

CREATE TABLE `allergen` (
  `allergen_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon_svg` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customer_order`
--

CREATE TABLE `customer_order` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `coupon_used_id` int(11) DEFAULT NULL,
  `table_number` varchar(10) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','processing','delivered','paid','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `total_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `customer_order`
--

INSERT INTO `customer_order` (`order_id`, `user_id`, `coupon_used_id`, `table_number`, `order_date`, `status`, `subtotal`, `total_discount`, `total_price`) VALUES
(1, NULL, NULL, NULL, '2026-01-06 19:16:12', 'delivered', 63.00, 0.00, 63.00),
(2, NULL, NULL, NULL, '2026-01-06 19:31:11', 'cancelled', 55.00, 0.00, 55.00),
(3, 1, NULL, NULL, '2026-01-07 01:32:23', 'pending', 55.00, 0.00, 55.00),
(4, 1, NULL, NULL, '2026-01-07 17:56:30', 'delivered', 109.00, 0.00, 109.00),
(5, 1, NULL, NULL, '2026-01-07 17:58:08', 'delivered', 87.20, 0.00, 87.20),
(6, 1, 2, NULL, '2026-01-07 18:04:53', 'cancelled', 109.00, 21.80, 87.20),
(7, 1, 1, NULL, '2026-01-08 01:07:34', 'delivered', 235.00, 23.50, 211.50),
(8, 1, NULL, NULL, '2026-01-08 02:23:58', 'delivered', 235.00, 0.00, 235.00),
(9, 2, 2, NULL, '2026-01-08 02:37:08', 'delivered', 110.00, 22.00, 88.00),
(10, 1, NULL, NULL, '2026-01-08 17:08:48', 'pending', 235.00, 0.00, 235.00),
(11, 1, 2, NULL, '2026-01-08 19:10:07', 'delivered', 235.00, 47.00, 188.00),
(12, 1, NULL, NULL, '2026-01-08 20:36:51', 'pending', 235.00, 0.00, 235.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `discount_code`
--

CREATE TABLE `discount_code` (
  `discount_code_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_value` decimal(5,2) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `discount_code`
--

INSERT INTO `discount_code` (`discount_code_id`, `code`, `discount_value`, `discount_type`, `expiration_date`, `is_active`) VALUES
(1, 'SAVE10', 10.00, 'percentage', '2030-12-31', 1),
(2, 'SAVE20', 20.00, 'percentage', '2030-12-31', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `excluded_ingredient`
--

CREATE TABLE `excluded_ingredient` (
  `order_line_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredient`
--

CREATE TABLE `ingredient` (
  `ingredient_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_line`
--

CREATE TABLE `order_line` (
  `order_line_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `order_line`
--

INSERT INTO `order_line` (`order_line_id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 38, 1, 12.00),
(2, 1, 9, 1, 45.00),
(3, 1, 27, 1, 6.00),
(4, 2, 10, 1, 55.00),
(5, 3, 10, 1, 55.00),
(6, 4, 17, 1, 48.00),
(7, 4, 27, 1, 6.00),
(8, 4, 10, 1, 55.00),
(9, 5, 17, 1, 48.00),
(10, 5, 27, 1, 6.00),
(11, 5, 10, 1, 55.00),
(12, 6, 17, 1, 48.00),
(13, 6, 27, 1, 6.00),
(14, 6, 10, 1, 55.00),
(15, 7, 8, 1, 180.00),
(16, 7, 10, 1, 55.00),
(17, 8, 8, 1, 180.00),
(18, 8, 10, 1, 55.00),
(19, 9, 10, 2, 55.00),
(20, 10, 8, 1, 180.00),
(21, 10, 10, 1, 55.00),
(22, 11, 8, 1, 180.00),
(23, 11, 10, 1, 55.00),
(24, 12, 8, 1, 180.00),
(25, 12, 10, 1, 55.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `product_type` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `product`
--

INSERT INTO `product` (`product_id`, `name`, `description`, `base_price`, `product_type`, `image`, `available`, `is_featured`) VALUES
(7, '45-Day Dry-Aged Tomahawk Steak', 'Rich, buttery texture with deep, concentrated beef flavor.', 125.00, 'Meats', 'img/tomahawk.png', 1, 1),
(8, 'Seared Wagyu A5 Filet Mignon', 'The most tender cut, known for its buttery texture and mild flavor.', 180.00, 'Meats', 'img/wagyu.png', 1, 1),
(9, 'Smoked Duck Breast', 'Served with cherry reduction and roasted root vegetables.', 45.00, 'Meats', 'img/duckbreast.png', 1, 1),
(10, 'Bone-In Veal Chop Milanese', 'Breaded and fried veal chop, crisp and tender.', 55.00, 'Meats', 'img/boneinveal.png', 1, 1),
(11, 'Butcher\'s Cut', 'A rotating selection of our butcher\'s finest daily pick.', 65.00, 'Meats', 'img/butcherscut.png', 1, 1),
(12, 'Single Barrel Rye Whiskey', 'Spicy and complex with notes of vanilla and oak.', 25.00, 'Spirits', 'img/ryewhiskey.png', 1, 1),
(13, 'Estate Reserve Cabernet Sauvignon', 'Full-bodied red with dark fruit flavors.', 18.00, 'Wines', 'img/cabernetsauvignon.png', 1, 1),
(14, 'Small Batch Bourbon', 'Smooth finish with hints of caramel and smoke.', 22.00, 'Spirits', 'img/batchbourbon.png', 1, 1),
(15, 'Barrel-Aged Gin', 'Botanical richness with a warm, woody finish.', 16.00, 'Spirits', 'img/agedgin.png', 1, 1),
(16, 'Macallan 12 Year Old Double Cask', 'The perfect balance of American and European oak.', 35.00, 'Spirits', 'img/macallan.png', 1, 1),
(17, 'Roasted Rack of Lamb', 'Succulent herb-crusted lamb rack served with mint reduction and seasonal vegetables.', 48.00, 'Meats', 'img/lambrack.png', 1, 0),
(18, 'Smoked Heritage Pork Chop', 'Double-cut pork chop, slow-smoked and glazed with an apple-bourbon gastrique.', 38.00, 'Meats', 'img/porkchop.png', 1, 0),
(19, 'Spanish Cachopo', 'Traditional Asturian breaded veal stuffed with serrano ham and melted cheese.', 42.00, 'Meats', 'img/cachopo.png', 1, 0),
(20, 'Pan-Seared U-10 Scallops', 'Jumbo scallops seared to golden perfection, served over cauliflower purée.', 36.00, 'Seafood', 'img/scallops.png', 1, 0),
(21, 'Grilled Scottish Salmon', 'Sustainably sourced salmon fillet, grilled and served with lemon-dill butter.', 34.00, 'Seafood', 'img/salmon.png', 1, 0),
(22, 'Creamy Garlic Mashed Potatoes', 'Yukon Gold potatoes whipped with roasted garlic and butter.', 12.00, 'Sides', 'img/garlicmashedpotatoes.png', 1, 0),
(23, 'Lobster Mac & Cheese', 'Chunks of fresh lobster meat in a rich three-cheese blend.', 24.00, 'Sides', 'img/lobsterm&c.png', 1, 0),
(24, 'Sautéed Wild Mushrooms', 'Seasonal wild mushrooms sautéed with fresh thyme and shallots.', 14.00, 'Sides', 'img/mushrooms.png', 1, 0),
(25, 'Coca-Cola', 'Classic chilled Coca-Cola.', 4.00, 'Beverages', 'img/coke.png', 1, 0),
(26, 'Sprite', 'Crisp and refreshing lemon-lime soda.', 5.00, 'Beverages', 'img/sprite.png', 1, 0),
(27, 'Mineral Water', 'Premium still mineral water.', 6.00, 'Beverages', 'img/mineral-water.png', 1, 0),
(28, 'Sparkling Water', 'Effervescent carbonated mineral water.', 6.00, 'Beverages', 'img/sparkling-water.png', 1, 0),
(29, 'Freshly Squeezed Orange Juice', '100% natural orange juice, pressed daily.', 8.00, 'Beverages', 'img/orange-juice.png', 1, 0),
(30, 'Chardonnay', 'Oak-aged with a creamy texture, notes of vanilla, and a buttery finish.', 18.00, 'Wines', 'img/chardonnay.png', 1, 0),
(31, 'Montepulciano d’Abruzzo', 'A robust Italian red featuring dark cherry notes, soft tannins, and a hint of spice.', 20.00, 'Wines', 'img/abruzzo.png', 1, 0),
(32, 'Sauvignon Blanc, Marlborough', 'Crisp and refreshing with vibrant notes of citrus, passion fruit, and fresh herbs.', 16.00, 'Wines', 'img/sauvignon-blanc.png', 1, 0),
(33, 'Pinot Noir, Burgundy', 'An elegant, light-bodied red with earthy undertones and delicate raspberry aromatics.', 24.00, 'Wines', 'img/pinot-noir.png', 1, 0),
(34, 'Hendrick’s Gin', 'A unique small-batch gin infused with cucumber and rose petals for a refreshingly floral finish.', 15.00, 'Spirits', 'img/hendricks-gin.png', 1, 0),
(35, 'Molten Chocolate Lava Cake', 'Decadent warm chocolate cake with a molten center, served with vanilla bean ice cream.', 14.00, 'Desserts', 'img/chocolate-lava-cake.png', 1, 0),
(36, 'Fruit Crumble', 'Baked seasonal fruit topped with a buttery, cinnamon-spiced oat crumble.', 12.00, 'Desserts', 'img/fruit-crumble.png', 1, 0),
(37, 'New York Style Cheesecake', 'Rich and creamy traditional cheesecake on a buttery graham cracker crust.', 13.00, 'Desserts', 'img/cheescake.png', 1, 0),
(38, 'Homemade Vanilla Bean Panna Cotta', 'Silky Italian cream delicacy infused with real vanilla beans and fresh berry compote.', 12.00, 'Desserts', 'img/panna-cotta.png', 1, 0),
(40, 'Test Simon ', 'Hola simon ', 250.00, 'Sides', 'img/cheescake.png', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_allergen`
--

CREATE TABLE `product_allergen` (
  `product_id` int(11) NOT NULL,
  `allergen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_ingredient`
--

CREATE TABLE `product_ingredient` (
  `product_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `is_optional` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservation`
--

CREATE TABLE `reservation` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `booking_time` datetime NOT NULL,
  `guest_number` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `special_request` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservation_restaurant_table`
--

CREATE TABLE `reservation_restaurant_table` (
  `reservation_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restaurant_table`
--

CREATE TABLE `restaurant_table` (
  `table_id` int(11) NOT NULL,
  `table_number` varchar(10) NOT NULL,
  `capacity` int(11) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin','staff') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`user_id`, `name`, `last_name`, `email`, `password`, `address`, `phone`, `role`) VALUES
(1, 'Manel', 'Admin', 'mmbravo3698@gmail.com', '$2y$10$4xAMkYTAjPZEtTBs.FcxOeLlbyy33811YcfSWnKjlUxpgyUVOk29G', NULL, '123456789', 'admin'),
(2, 'Manel', 'Cliente', 'mmbravo36@gmail.com', '$2y$10$9k/A5lKrPPR49iCUXDosV.Scz/OBVCewDT6RsCGaBn/t5mx8mrAzO', NULL, '123456987', 'customer'),
(3, 'Carlos', 'Inventado', 'carlos@gmail.com', '$2y$10$09z8zHeG7OzSfm6oZUadbOHmf07jBSajQcUXPtzu53OAMvO1sD09u', NULL, '987654321', 'customer');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_log`
--
ALTER TABLE `admin_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_user` (`admin_user`);

--
-- Indices de la tabla `allergen`
--
ALTER TABLE `allergen`
  ADD PRIMARY KEY (`allergen_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `customer_order`
--
ALTER TABLE `customer_order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `coupon_used_id` (`coupon_used_id`);

--
-- Indices de la tabla `discount_code`
--
ALTER TABLE `discount_code`
  ADD PRIMARY KEY (`discount_code_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `excluded_ingredient`
--
ALTER TABLE `excluded_ingredient`
  ADD PRIMARY KEY (`order_line_id`,`ingredient_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indices de la tabla `ingredient`
--
ALTER TABLE `ingredient`
  ADD PRIMARY KEY (`ingredient_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `order_line`
--
ALTER TABLE `order_line`
  ADD PRIMARY KEY (`order_line_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indices de la tabla `product_allergen`
--
ALTER TABLE `product_allergen`
  ADD PRIMARY KEY (`product_id`,`allergen_id`),
  ADD KEY `allergen_id` (`allergen_id`);

--
-- Indices de la tabla `product_ingredient`
--
ALTER TABLE `product_ingredient`
  ADD PRIMARY KEY (`product_id`,`ingredient_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indices de la tabla `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `reservation_restaurant_table`
--
ALTER TABLE `reservation_restaurant_table`
  ADD PRIMARY KEY (`reservation_id`,`table_id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indices de la tabla `restaurant_table`
--
ALTER TABLE `restaurant_table`
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `table_number` (`table_number`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_log`
--
ALTER TABLE `admin_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `allergen`
--
ALTER TABLE `allergen`
  MODIFY `allergen_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `customer_order`
--
ALTER TABLE `customer_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `discount_code`
--
ALTER TABLE `discount_code`
  MODIFY `discount_code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ingredient`
--
ALTER TABLE `ingredient`
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `order_line`
--
ALTER TABLE `order_line`
  MODIFY `order_line_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `restaurant_table`
--
ALTER TABLE `restaurant_table`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `admin_log`
--
ALTER TABLE `admin_log`
  ADD CONSTRAINT `admin_log_ibfk_1` FOREIGN KEY (`admin_user`) REFERENCES `user` (`user_id`);

--
-- Filtros para la tabla `customer_order`
--
ALTER TABLE `customer_order`
  ADD CONSTRAINT `customer_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `customer_order_ibfk_2` FOREIGN KEY (`coupon_used_id`) REFERENCES `discount_code` (`discount_code_id`);

--
-- Filtros para la tabla `excluded_ingredient`
--
ALTER TABLE `excluded_ingredient`
  ADD CONSTRAINT `excluded_ingredient_ibfk_1` FOREIGN KEY (`order_line_id`) REFERENCES `order_line` (`order_line_id`),
  ADD CONSTRAINT `excluded_ingredient_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`ingredient_id`);

--
-- Filtros para la tabla `order_line`
--
ALTER TABLE `order_line`
  ADD CONSTRAINT `order_line_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `customer_order` (`order_id`),
  ADD CONSTRAINT `order_line_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Filtros para la tabla `product_allergen`
--
ALTER TABLE `product_allergen`
  ADD CONSTRAINT `product_allergen_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_allergen_ibfk_2` FOREIGN KEY (`allergen_id`) REFERENCES `allergen` (`allergen_id`);

--
-- Filtros para la tabla `product_ingredient`
--
ALTER TABLE `product_ingredient`
  ADD CONSTRAINT `product_ingredient_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_ingredient_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`ingredient_id`);

--
-- Filtros para la tabla `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Filtros para la tabla `reservation_restaurant_table`
--
ALTER TABLE `reservation_restaurant_table`
  ADD CONSTRAINT `reservation_restaurant_table_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`reservation_id`),
  ADD CONSTRAINT `reservation_restaurant_table_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `restaurant_table` (`table_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
