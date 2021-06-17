-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 09 Cze 2021, 20:14
-- Wersja serwera: 10.4.13-MariaDB
-- Wersja PHP: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `store_template`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `category_id` int(5) NOT NULL,
  `parent_id` int(5) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `categories`
--

INSERT INTO `categories` (`category_id`, `parent_id`, `category_name`) VALUES
(3, 0, 'Category 1'),
(4, 0, 'Category 2'),
(5, 0, 'Category 3'),
(6, 0, 'Category 4'),
(7, 3, 'Category 1.1'),
(8, 3, 'Category 1.2'),
(9, 0, 'Category with multiple words');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `images`
--

CREATE TABLE `images` (
  `id` int(10) NOT NULL,
  `unique_name` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `images`
--

INSERT INTO `images` (`id`, `unique_name`, `title`, `alt`, `upload_date`, `path`) VALUES
(1, '60b604eb8bccc9.73585043.png', 'title', 'alt', '2021-06-01 09:59:07', 'uploaded_images/2021/06/60b604eb8bccc9.73585043.png'),
(2, '60b604f238d382.16683952.jpg', 'asddas', 'asdd', '2021-06-01 09:59:14', 'uploaded_images/2021/06/60b604f238d382.16683952.jpg'),
(3, '60b6051f316734.25844722.jpg', 'bvb,mvnmv', 'czxzxcbn', '2021-06-01 09:59:59', 'uploaded_images/2021/06/60b6051f316734.25844722.jpg'),
(4, '60b605272d3712.35801228.png', 'sdfsgd', 'mbnouig', '2021-06-01 10:00:07', 'uploaded_images/2021/06/60b605272d3712.35801228.png');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `order_id` int(16) NOT NULL,
  `user_id` int(8) NOT NULL,
  `order_cost` decimal(65,2) NOT NULL,
  `shipping_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `city` varchar(100) NOT NULL,
  `street` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `apartment` varchar(25) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `additional_info` text NOT NULL,
  `order_status` varchar(50) NOT NULL DEFAULT 'Pending payment',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_cost`, `shipping_id`, `first_name`, `last_name`, `city`, `street`, `postal_code`, `apartment`, `telephone`, `additional_info`, `order_status`, `order_date`) VALUES
(2, 1, '242.00', 3, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', 'test', 'Pending payment', '2021-06-01 17:49:39'),
(3, 3, '60.99', 2, 'Tester', 'Zbysiu', 'Mała', 'Krótka', '12-121', '69', '861271232', 'asdadsasddas', 'Cancelled', '2021-06-01 17:49:39'),
(4, 3, '60.99', 2, 'Tester', 'Zbysiu', 'Mała', 'Krótka', '12-121', '69', '861271232', '', 'Pending payment', '2021-06-01 17:49:39'),
(5, 4, '100.00', 3, 'Artur', 'Musielak', 'Świebodzin', 'ul. Łużycka', '66-200', '44AB', '662568877', 'xczzxczxczcx', 'Processing', '2021-06-01 17:49:39'),
(6, 5, '60.00', 1, 'Maciej', 'Musielak', 'Świebodzin', 'ul. Łużycka', '66-200', '44AB', '662568877', '', 'Pending payment', '2021-06-01 17:49:39'),
(7, 6, '60.00', 1, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', 'dsasddasad', 'Pending payment', '2021-06-01 17:49:39'),
(8, 6, '67.00', 1, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', '', 'Pending payment', '2021-06-01 17:49:39'),
(9, 6, '67.00', 1, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', '', 'Pending payment', '2021-06-01 17:49:39'),
(10, 6, '107.00', 3, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', '', 'Completed', '2021-06-01 17:49:39'),
(11, 7, '67.00', 1, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', 'aasd', 'Pending payment', '2021-06-01 17:49:39'),
(12, 7, '107.00', 3, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', '', 'Completed', '2021-06-01 17:49:39'),
(13, 1, '150.99', 2, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', '', 'Cancelled', '2021-06-01 17:49:39'),
(14, 1, '20.99', 2, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', '', 'Pending payment', '2021-06-01 18:17:43'),
(15, 1, '202.00', 3, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', '', 'Pending payment', '2021-06-02 12:24:24'),
(16, 1, '100.00', 3, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', '', 'Cancelled', '2021-06-02 12:24:57'),
(17, 1, '60.00', 1, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059', '', 'Processing', '2021-06-02 12:27:03');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders_products`
--

CREATE TABLE `orders_products` (
  `order_id` int(16) NOT NULL,
  `product_id` int(10) NOT NULL,
  `quantity` int(5) NOT NULL,
  `current_price` decimal(65,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `orders_products`
--

INSERT INTO `orders_products` (`order_id`, `product_id`, `quantity`, `current_price`) VALUES
(2, 2, 1, '0.00'),
(2, 1, 3, '0.00'),
(3, 2, 1, '0.00'),
(4, 2, 1, '0.00'),
(5, 1, 1, '0.00'),
(6, 1, 1, '0.00'),
(7, 1, 1, '0.00'),
(8, 2, 1, '0.00'),
(9, 2, 1, '0.00'),
(10, 2, 1, '0.00'),
(11, 2, 1, '0.00'),
(12, 2, 1, '0.00'),
(13, 2, 1, '52.00'),
(13, 1, 2, '45.00'),
(15, 1, 3, '45.00'),
(15, 4, 1, '12.00'),
(16, 1, 1, '45.00'),
(17, 1, 1, '45.00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tags` text NOT NULL,
  `price` decimal(65,2) NOT NULL,
  `price_sale` decimal(65,2) NOT NULL,
  `stock` int(5) NOT NULL,
  `stock_status` int(1) NOT NULL,
  `stock_manage` int(1) NOT NULL,
  `allow_multiple_purchases` int(1) NOT NULL,
  `published` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `tags`, `price`, `price_sale`, `stock`, `stock_status`, `stock_manage`, `allow_multiple_purchases`, `published`) VALUES
(1, 'Product 1', '<h2>What is Lorem Ipsum?</h2><p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><h2>Why do we use it?</h2><p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p><h2>Where does it come from?</h2><p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p><p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p><h2>Where can I get some?</h2><p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>', 'tags1, vxcuujfsd, bvbc', '55.00', '45.00', 23, -1, 1, 1, 1),
(2, 'Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 Product 2 ', '<h2>What is Lorem Ipsum?</h2><p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><h2>Why do we use it?</h2><p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p><h2>Where does it come from?</h2><p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p><p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p><h2>Where can I get some?</h2><p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>', '', '52.00', '-1.00', -1, 1, 0, 0, 1),
(4, 'Placeholder', '<p>asdasddas</p>', '', '12.00', '-1.00', -1, 1, 0, 0, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `product_category`
--

CREATE TABLE `product_category` (
  `product_id` int(10) NOT NULL,
  `category_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `product_category`
--

INSERT INTO `product_category` (`product_id`, `category_id`) VALUES
(1, 3),
(1, 7),
(1, 6),
(2, 4),
(2, 5),
(4, 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `product_image_order`
--

CREATE TABLE `product_image_order` (
  `product_id` int(10) NOT NULL,
  `image_id` int(10) NOT NULL,
  `image_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `product_image_order`
--

INSERT INTO `product_image_order` (`product_id`, `image_id`, `image_order`) VALUES
(1, 1, 0),
(1, 1, 1),
(1, 3, 2),
(1, 4, 3),
(1, 4, 4),
(1, 2, 5),
(1, 3, 6),
(1, 2, 7),
(1, 4, 8),
(1, 3, 9),
(1, 2, 10),
(1, 1, 11),
(2, 3, 0),
(2, 4, 1),
(2, 2, 2),
(2, 1, 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `settings`
--

CREATE TABLE `settings` (
  `setting_name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `settings`
--

INSERT INTO `settings` (`setting_name`, `value`) VALUES
('site_name', 'Page titans'),
('site_description', 'Nice description'),
('currency', 'zł'),
('account_number', '12341234567423423'),
('bank_name', 'Santander'),
('sort_code', 'sort code'),
('iban', 'iban'),
('bic_swift', 'swift'),
('payment_name', 'Direct bank transfer');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `shipping_options`
--

CREATE TABLE `shipping_options` (
  `id` int(11) NOT NULL,
  `shipping_option` varchar(100) NOT NULL,
  `shipping_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `shipping_options`
--

INSERT INTO `shipping_options` (`id`, `shipping_option`, `shipping_price`) VALUES
(1, 'Kurier', '15.00'),
(2, 'Paczkomat', '8.99'),
(3, 'Poczta', '55.00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `user_id` int(8) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(60) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(10) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`user_id`, `password`, `email`, `creation_date`, `role`) VALUES
(1, '$2y$10$BeegUuO7G82sGJm71E8O8O8Vivvd31tnBNEU6diA1KusQHQ09IYKy', 'admin@admin.pl', '2021-06-01 09:46:30', 'admin'),
(2, '$2y$10$LsglfDXCG4ZAhkFdXRUVJecnATU4/5.u4krX0u30uq1WRqCgRNLpq', 'test@test.pl', '2021-06-01 15:09:48', 'user'),
(3, '$2y$10$HF/WEJ.vdKIfaWTKAP40f.Ue5NqqVUnHy5SOvd5g579Ib6toamawq', 'test2@test.pl', '2021-06-01 15:52:14', 'user'),
(4, '$2y$10$OqQL13hjxbIASZ5AOmyV.eTCdmLixEvnbt.ZkKqgcxOTubpxcX.jq', 'armus@poczta.onet.pl', '2021-06-01 15:55:09', 'user'),
(5, '$2y$10$wnGZid3qHIfzfKt0wgULqOEBmyjJN9PS8aFU9qJEFJYL9E9UiDIGS', 'armusas@poczta.onet.pl', '2021-06-01 15:56:07', 'user'),
(6, '$2y$10$i/DxM.Jr1VtK0pBKLpA83OsK1GsB.zSVvrM2OaY01WeEfmRA47hN6', 'admiasdasdasdn@admin.pl', '2021-06-01 16:00:17', 'user'),
(7, '$2y$10$nhDajQpfSleVdoYLu8HbYOZJuDbP5tVXQZg0b/3uWCKrqPgsVnvam', 'admiasddsaasdvxcvzn@admin.pl', '2021-06-01 16:12:18', 'user');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_informations`
--

CREATE TABLE `user_informations` (
  `user_id` int(8) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `city` varchar(100) NOT NULL,
  `street` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `apartment` varchar(25) NOT NULL,
  `telephone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `user_informations`
--

INSERT INTO `user_informations` (`user_id`, `first_name`, `last_name`, `city`, `street`, `postal_code`, `apartment`, `telephone`) VALUES
(1, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059'),
(2, 'Tester', 'Zbysiu', 'Mała', 'Krótka', '12-121', '', '861271232'),
(3, 'Tester', 'Zbysiu', 'Mała', 'Krótka', '12-121', '69', '861271232'),
(4, 'Artur', 'Musielak', 'Świebodzin', 'ul. Łużycka', '66-200', '44AB', '662568877'),
(5, 'Maciej', 'Musielak', 'Świebodzin', 'ul. Łużycka', '66-200', '44AB', '662568877'),
(6, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059'),
(7, 'Maciej', 'Musielak', 'Świebodzin', '30-go Stycznia', '66-200', '11/3', '507987059');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeksy dla tabeli `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `order_user` (`user_id`),
  ADD KEY `order_shipping` (`shipping_id`);

--
-- Indeksy dla tabeli `orders_products`
--
ALTER TABLE `orders_products`
  ADD KEY `orders_product_order` (`order_id`),
  ADD KEY `orders_product` (`product_id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `product_category`
--
ALTER TABLE `product_category`
  ADD KEY `product_product` (`product_id`),
  ADD KEY `product_category` (`category_id`);

--
-- Indeksy dla tabeli `product_image_order`
--
ALTER TABLE `product_image_order`
  ADD KEY `product_image_product` (`product_id`),
  ADD KEY `product_image` (`image_id`);

--
-- Indeksy dla tabeli `shipping_options`
--
ALTER TABLE `shipping_options`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeksy dla tabeli `user_informations`
--
ALTER TABLE `user_informations`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `images`
--
ALTER TABLE `images`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT dla tabeli `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `shipping_options`
--
ALTER TABLE `shipping_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `order_shipping` FOREIGN KEY (`shipping_id`) REFERENCES `shipping_options` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `orders_products`
--
ALTER TABLE `orders_products`
  ADD CONSTRAINT `orders_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `orders_product_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `product_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `product_image_order`
--
ALTER TABLE `product_image_order`
  ADD CONSTRAINT `product_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `product_image_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `user_informations`
--
ALTER TABLE `user_informations`
  ADD CONSTRAINT `user_informations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
