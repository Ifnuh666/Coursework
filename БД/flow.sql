-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 14 2026 г., 14:22
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `flow`
--
CREATE DATABASE IF NOT EXISTS `flow` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `flow`;

-- --------------------------------------------------------

--
-- Структура таблицы `order_tovar`
--

DROP TABLE IF EXISTS `order_tovar`;
CREATE TABLE `order_tovar` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_tovar`
--

INSERT INTO `order_tovar` (`id`, `id_user`, `date`, `status`) VALUES
(1, 1, '2026-03-11', 'Доставлен'),
(2, 2, '2026-03-11', 'Доставлен'),
(3, 1, '2026-04-08', 'новый'),
(4, 1, '2026-04-08', 'новый'),
(5, 1, '2026-04-08', 'новый'),
(6, 1, '2026-04-08', 'новый'),
(7, 2, '2026-04-08', 'новый'),
(8, 4, '2026-04-08', 'новый'),
(9, 4, '2026-04-08', 'новый'),
(10, 1, '2026-04-08', 'новый'),
(11, 1, '2026-04-08', 'новый'),
(12, 2, '2026-04-08', 'новый'),
(13, 2, '2026-04-08', 'новый'),
(14, 8, '2026-04-09', 'новый'),
(15, 9, '2026-04-09', 'новый');

-- --------------------------------------------------------

--
-- Структура таблицы `order_tovar_prom`
--

DROP TABLE IF EXISTS `order_tovar_prom`;
CREATE TABLE `order_tovar_prom` (
  `id` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `id_tovar` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_tovar_prom`
--

INSERT INTO `order_tovar_prom` (`id`, `id_order`, `id_tovar`) VALUES
(1, 1, 1),
(2, 1, 14),
(3, 2, 7),
(4, 3, 9),
(5, 4, 10),
(6, 5, 5),
(7, 6, 5),
(8, 7, 10),
(9, 8, 10),
(10, 9, 13),
(11, 10, 10),
(12, 11, 10),
(13, 12, 4),
(14, 13, 7),
(15, 14, 2),
(16, 15, 7);

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_tovar` int(11) NOT NULL,
  `review` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `id_user`, `id_tovar`, `review`) VALUES
(1, 1, 1, 'Красивый букет'),
(2, 2, 10, 'Очень красивый букет, цветы свежие, доставили вовремя. Спасибо!'),
(3, 2, 4, 'Очень красивый букет, цветы свежие, доставили вовремя. Спасибо!'),
(4, 2, 7, 'Очень красивый букет, цветы свежие, доставили вовремя. Спасибо!'),
(5, 2, 13, 'Очень красивый букет, цветы свежие, доставили вовремя. Спасибо!'),
(6, 2, 2, 'ывмивыиывтиывт'),
(7, 1, 2, 'гюшюгшюгшюгшю'),
(8, 1, 14, 'Очень красивый букет, цветы свежие, доставили вовремя. Спасибо!'),
(9, 2, 7, 'Красивый букет, рекомендую!'),
(10, 2, 7, 'Обязательно еще раз куплю этот букет!!'),
(11, 8, 2, 'Очень красивый букет, рекомендую продавца!'),
(12, 9, 7, 'Очень красивые цветочки!!!');

-- --------------------------------------------------------

--
-- Структура таблицы `tovar`
--

DROP TABLE IF EXISTS `tovar`;
CREATE TABLE `tovar` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `rating` decimal(3,2) DEFAULT 5.00,
  `image` varchar(255) DEFAULT 'flow1.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tovar`
--

INSERT INTO `tovar` (`id`, `name`, `description`, `price`, `rating`, `image`) VALUES
(1, 'Розы Красные', 'Букет красных роз', 5100, 4.80, 'flow1.jpg'),
(2, 'Розы розовые', 'Красивый букет роз', 3500, 5.00, 'flow2.jpg'),
(3, 'Букет из георгинов, маттиолы и хризантем', 'георгин — 3 шт.; \r\nдиантус — 5 шт.; \r\nматтиола — 3 шт.; \r\nкустовая хризантема — 1 шт..', 5500, 4.00, 'flow3.jpg'),
(4, ' Букет из белых гортензий, роз и гвоздик', 'Состав букета «Это приятно»:\r\nгортензия белая (Голландия) — 1 шт.; \r\nгортензия розовая (Голландия) — 1 шт.; \r\nоксипеталум голубой — 3 шт.; \r\nроза розовая (Россия, 50 см) — 7 шт.; \r\nгвоздика белая (Диантус, Голландия) — 3 шт.;', 7000, 4.90, 'flow4.jpg'),
(5, 'Букет из роз, гвоздик, эустомы и маттиолы', 'Розы — 5 шт.. \r\nМаттиола — 3 шт.. \r\nЭустома — 4 шт.. \r\nГвоздика — 5 шт.. \r\nЗелень. \r\n', 2500, 4.50, 'flow5.jpg'),
(6, 'Букет из пионов, маттиолы и эустомы', 'В состав входят: пион белый — 5 шт., маттиола — 5 шт., эустома — 7 шт., сухоцветы — 8 шт.', 7500, 4.85, 'flow6.jpg'),
(7, 'Букет из пионов, кустовых хризантем и маттиолы', 'пион розовый — 3 шт.; \r\nматтиола — 3 шт.; \r\nхризантема кустовая — 1 шт.; \r\nроза кустовая 50 см — 2 шт.; \r\nстатица;', 6000, 5.00, 'flow6.jpg'),
(8, 'Букет из тюльпанов, гвоздик, гиацинтов и эвкалипта', 'гиацинт — 1 шт., лента — 1 шт., эвкалипт — 2 шт., гвоздика — 1 шт., тишью — 2 шт., тюльпан пионовидный — 1 шт., матовая плёнка — 3 шт.. ', 4200, 4.00, 'flow7.jpg'),
(9, 'Букет с гвоздиками, эустомами, гиперикумами и эвкалиптом.', 'эустома — 3 шт.; \r\nгвоздика — 4 шт.; \r\nгиперикум — 1 шт.; \r\nэвкалипт — 1 шт.; ', 3000, 4.30, 'flow8.jpg'),
(10, 'Букет из гортензии, пионов, роз, маттиолы и гвоздик', 'Гортензия — 7 шт., кустовая гвоздика — 3 шт., пионовидная роза — 13 шт.', 4100, 5.00, 'flow9.jpg'),
(11, 'Букет с тюльпанами, гвоздиками, гиацинтами и эвкалиптом', 'гиацинт — 1 шт.; \r\nлента — 1 шт.; \r\nэвкалипт — 2 шт.; \r\nгвоздика — 1 шт.; \r\nтишью — 2 шт.; \r\nтюльпан пионовидный — 1 шт.; \r\nплёнка матовая — 3 шт..', 3500, 4.00, 'flow10.jpg'),
(12, 'Букет из пионов, тюльпанов, гвоздик, сирени и эвкалипта', 'Пионы \r\nСирень \r\nГвоздики \r\nЭвкалипт ', 2000, 4.00, 'flow11.jpg'),
(13, 'Букет роз с крупными белыми бутонами', 'Крупные белые розы - 5\r\nЗелень', 1200, 4.40, 'flow12.jpg'),
(14, 'Букет «Фрида» от Present Simple', 'Это яркий осенний букет с хризантемами «Бигуди», скимией, герберами, целозией и антириниумом.', 3500, 4.00, 'flow13.jpg'),
(15, 'Букет из эустомы, диантуса и альстромерии', 'Эустома\r\nДиантус \r\nАльстромерия ', 2500, 4.10, 'flow14.jpg'),
(16, 'Пионы', 'Пионы— многолетние травянистые или древовидные растения семейства Пионовые. Произрастают в субтропическом климате и в умеренных районах Евразии и Северной Америки.', 1500, 4.00, 'flow15.jpg'),
(19, 'Пионы', 'Букетик пионов', 3200, 4.80, 'flow15.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `patronymic` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `last_name`, `first_name`, `patronymic`, `login`, `password`) VALUES
(1, 'Волков', 'Максим', 'Владиславович', 'mvvolkov', '123'),
(2, 'Иосипчук', 'Максим', 'Сергеевич', 'msiosipchuk', '1234'),
(3, 's', 's', 's', 's', '1'),
(4, 'Иванов', 'Иван', 'Иванович', 'ivan', '1'),
(6, 'Смирнов', 'Егор', 'Сергеевич', 'ega', '54321'),
(7, 'Смирнов', 'Егор', 'Сергеевич', 'egorka', '197809'),
(8, 'Петров', 'Степан', 'Иванович', 'stepa', '12345678'),
(9, 'Иванов', 'Максим', 'Игоревич', 'makson', '197809');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `order_tovar`
--
ALTER TABLE `order_tovar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_order` (`id_user`);

--
-- Индексы таблицы `order_tovar_prom`
--
ALTER TABLE `order_tovar_prom`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_tovar_order` (`id_order`),
  ADD KEY `fk_order_tovar_tovar` (`id_tovar`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_tovar` (`id_tovar`),
  ADD KEY `fk_reviews_user` (`id_user`);

--
-- Индексы таблицы `tovar`
--
ALTER TABLE `tovar`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `order_tovar`
--
ALTER TABLE `order_tovar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `order_tovar_prom`
--
ALTER TABLE `order_tovar_prom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `tovar`
--
ALTER TABLE `tovar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `order_tovar`
--
ALTER TABLE `order_tovar`
  ADD CONSTRAINT `fk_user_order` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`);

--
-- Ограничения внешнего ключа таблицы `order_tovar_prom`
--
ALTER TABLE `order_tovar_prom`
  ADD CONSTRAINT `fk_order_tovar_order` FOREIGN KEY (`id_order`) REFERENCES `order_tovar` (`id`),
  ADD CONSTRAINT `fk_order_tovar_tovar` FOREIGN KEY (`id_tovar`) REFERENCES `tovar` (`id`);

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_tovar` FOREIGN KEY (`id_tovar`) REFERENCES `tovar` (`id`),
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
