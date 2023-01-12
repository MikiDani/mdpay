-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2023. Jan 11. 15:36
-- Kiszolgáló verziója: 10.4.25-MariaDB
-- PHP verzió: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `mdshop`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `typeid` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_hungarian_ci NOT NULL,
  `text` text COLLATE utf8_hungarian_ci NOT NULL,
  `price` int(11) NOT NULL,
  `markdown` int(3) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `instock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `product`
--

INSERT INTO `product` (`id`, `typeid`, `name`, `text`, `price`, `markdown`, `rating`, `instock`) VALUES
(1, 1, 'fekete gépház', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 12000, 0, 0, 5),
(2, 1, 'gamer gépházak', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 23000, 0, 0, 10),
(3, 1, 'color 125 billenyűzet próba', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. 33', 17600, 0, 0, 12),
(4, 1, 'billenytűzet', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 8000, 10, 0, 6),
(5, 3, 'white heverő', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 145000, 0, 0, 5),
(6, 3, 'light heverő', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 230000, 0, 0, 7),
(7, 3, 'kerek asztal', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.3', 12000, 0, 0, 5),
(8, 2, 'női báli ruhák', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.1', 14500, 0, 0, 1),
(9, 2, 'női pulóver', 'Aenean commodo ligula eget dolor. Aenean massa.', 4500, 0, 0, 7),
(10, 2, 'férfi kockás ing', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 3420, 0, 0, 10),
(11, 2, 'férfi csíkos ing', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 2700, 25, 0, 21),
(12, 2, 'kék férfi ing', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.33', 11000, 0, 0, 5),
(13, 1, '17\'\' monitor', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 78000, 0, 0, 8),
(14, 5, 'ceruza HB', 'Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend enim.', 270, 0, 0, 500),
(15, 5, 'notesz', 'Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.', 850, 0, 0, 80),
(16, 5, 'papír tartó', 'Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.', 420, 0, 0, 34),
(17, 5, 'papírvágó', 'Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo.', 7850, 0, 0, 4),
(18, 5, 'nagy hegyező', 'Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero.', 1900, 0, 0, 6),
(19, 5, 'rózsaszín radír', 'Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero.', 45, 0, 0, 76),
(20, 5, 'radír MONO', 'Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero.', 25, 0, 0, 24),
(21, 5, 'radír Elephant', 'Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero.Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero.', 60, 0, 0, 30),
(22, 5, 'ceruza design', 'Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero.', 340, 0, 0, 12),
(23, 5, 'ceruza Fabel Castell', 'Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero.', 190, 20, 0, 45),
(24, 5, 'ceruza készlet', 'Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.', 4650, 0, 0, 10),
(25, 5, 'rotring készlet', 'Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet.', 1850, 0, 0, 8),
(26, 4, 'UNO kártya', 'Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in feugiat a tellus.', 3200, 0, 0, 20),
(27, 4, 'Twister társasjáték', 'Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in feugiat a tellus. Vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in feugiat a tellus.', 5600, 0, 0, 7),
(28, 4, 'Master társasjáték', 'Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in feugiat a tellus.', 6700, 10, 0, 14),
(29, 4, 'gyermekjáték', 'Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in feugiat a tellus. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in feugiat.', 950, 0, 0, 3),
(30, 3, 'hálószoba szekrény', 'On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire.33rr', 186000, 0, 0, 6),
(31, 3, 'gray heverő', 'Denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire.', 95000, 0, 0, 2),
(32, 3, 'light kanapé', 'In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business.', 136000, 10, 0, 11);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_orders`
--

CREATE TABLE `product_orders` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `productlist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`productlist`)),
  `totalprice` int(11) NOT NULL,
  `orderepoch` int(10) NOT NULL,
  `postalcode` int(11) NOT NULL,
  `city` varchar(30) COLLATE utf8_hungarian_ci NOT NULL,
  `designation` varchar(30) COLLATE utf8_hungarian_ci NOT NULL,
  `designationtype` varchar(30) COLLATE utf8_hungarian_ci NOT NULL,
  `designationnumber` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_pic`
--

CREATE TABLE `product_pic` (
  `id` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  `serverfilename` varchar(64) COLLATE utf8_hungarian_ci NOT NULL,
  `text` varchar(128) COLLATE utf8_hungarian_ci NOT NULL,
  `primarypic` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `product_pic`
--

INSERT INTO `product_pic` (`id`, `productid`, `serverfilename`, `text`, `primarypic`) VALUES
(1, 1, '1_1669196868.jpg', 'gépház 1.', 1),
(2, 2, '2_1669196970.jpg', '1.', 0),
(3, 2, '2_1669196975.jpg', '2.', 1),
(4, 2, '2_1669196983.jpg', '3.', 0),
(5, 3, '3_1669197032.jpg', 'color 125', 1),
(6, 4, '4_1669197089.jpg', 'billenytűzet', 1),
(7, 5, '5_1669197707.jpg', 'whie heverő 1', 1),
(8, 5, '5_1669197715.jpg', 'white heverő 2', 0),
(9, 5, '5_1669197720.jpg', 'white heverő 3', 0),
(12, 7, '7_1669198145.jpg', 'kerek asztal', 1),
(13, 7, '7_1669198150.jpg', 'nyitható kerekasztal', 0),
(14, 8, '8_1669198261.jpg', 'verzió 1.', 0),
(15, 8, '8_1669198267.jpg', 'verzió 2.', 1),
(16, 9, '9_1669198356.jpg', 'zöld pulóver', 1),
(17, 10, '10_1669198478.jpg', 'kockás ing', 1),
(18, 11, '11_1669198543.jpg', 'férfi csíkos ing', 1),
(19, 12, '12_1669198663.jpg', 'kék ing', 1),
(20, 13, '13_1669198958.jpg', 'elölről', 1),
(21, 13, '13_1669198964.jpg', 'hátulról', 0),
(28, 14, '14_1669202326.png', 'ceruzák', 1),
(29, 15, '15_1669202587.jpg', 'notesz', 1),
(30, 16, '16_1669202623.jpg', 'papírtartó', 1),
(31, 17, '17_1669204465.jpg', 'termék fotó', 1),
(32, 18, '18_1669204519.jpg', 'nagy hegyező dobozban', 1),
(33, 18, '18_1669204532.jpg', 'nagy hegyező', 0),
(34, 19, '19_1669204665.jpg', 'radír 1.', 1),
(35, 20, '20_1669204741.jpg', 'MONO radír', 1),
(36, 21, '21_1669204834.jpg', 'radír Elephant', 1),
(37, 22, '22_1669204954.jpg', 'ceruza Design', 1),
(38, 23, '23_1669205046.jpg', 'Fabel Castell ceruza', 1),
(40, 25, '25_1669205246.jpg', 'rotring készlet', 1),
(41, 26, '26_1669205937.jpg', 'UNO kártya', 1),
(42, 27, '27_1669206003.jpg', 'Twister', 1),
(43, 28, '28_1669206045.jpg', 'Master 1.', 1),
(44, 28, '28_1669206062.jpg', 'Master 2.', 0),
(45, 29, '29_1669206215.jpg', 'gyermekjáték', 1),
(46, 30, '30_1669389705.jpg', 'szekrény 1.', 1),
(47, 30, '30_1669389715.jpg', 'szekrény 2.', 0),
(48, 30, '30_1669389723.jpg', 'szekrény 3.', 0),
(49, 30, '30_1669389731.jpg', 'szekrény méretek', 0),
(50, 31, '31_1669389911.jpg', 'gray 1', 1),
(51, 31, '31_1669389918.jpg', 'gray 2', 0),
(52, 31, '31_1669389925.jpg', 'gray 3', 0),
(53, 32, '32_1669390044.jpg', 'light 1', 1),
(54, 32, '32_1669390054.jpg', 'light 2', 0),
(55, 32, '32_1669390061.jpg', 'light 3', 0),
(66, 6, '6_1671377686.jpg', 'light heverő 1.', 1),
(67, 6, '6_1671377704.jpg', 'light heverő 2.', 0),
(68, 6, '6_1671377734.jpg', 'jobbos-balos', 0),
(69, 24, '24_1672155644.jpg', 'ceruza készlet', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_type`
--

CREATE TABLE `product_type` (
  `id` int(11) NOT NULL,
  `typename` varchar(30) COLLATE utf8_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `product_type`
--

INSERT INTO `product_type` (`id`, `typename`) VALUES
(1, 'elektronika'),
(2, 'ruházat'),
(3, 'bútor'),
(4, 'játék'),
(5, 'iroda');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `token` varchar(128) COLLATE utf8_hungarian_ci NOT NULL,
  `epochstart` int(10) NOT NULL,
  `epochend` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(30) COLLATE utf8_hungarian_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_hungarian_ci NOT NULL,
  `password` varchar(128) COLLATE utf8_hungarian_ci NOT NULL,
  `info` text COLLATE utf8_hungarian_ci NOT NULL,
  `rank` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `info`, `rank`) VALUES
(1, 'admin00', 'admin@mikidani.probaljaki.hu', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 'Admin felhasználó. Az admin felületet ezzel a felhasználóval lehet tesztelni.', 1),
(3, 'user00', 'user@mikidani.probaljaki.hu', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 'User felhasználó. A frontend felületet ezzel a felhasználóval lehet tesztelni.', 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users_favorites`
--

CREATE TABLE `users_favorites` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  `selectepoch` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `product_orders`
--
ALTER TABLE `product_orders`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `product_pic`
--
ALTER TABLE `product_pic`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users_favorites`
--
ALTER TABLE `users_favorites`
  ADD PRIMARY KEY (`id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT a táblához `product_orders`
--
ALTER TABLE `product_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT a táblához `product_pic`
--
ALTER TABLE `product_pic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT a táblához `product_type`
--
ALTER TABLE `product_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `users_favorites`
--
ALTER TABLE `users_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
