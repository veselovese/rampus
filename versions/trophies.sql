-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Авг 18 2024 г., 12:32
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
-- База данных: `rampus`
--

-- --------------------------------------------------------

--
-- Структура таблицы `trophies`
--

CREATE TABLE `trophies` (
  `id` int(5) NOT NULL,
  `user_id` int(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `get_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `trophies`
--

INSERT INTO `trophies` (`id`, `user_id`, `name`, `description`, `image`, `get_date`) VALUES
(1, 1, 'Первый', 'Первый в рейтинге', 'pics/BlossomFirstIcon.svg', '2024-08-17 20:16:39'),
(2, 2, 'Второй', 'Второй в рейтинге', 'pics/BlossomSecondIcon.svg', '2024-08-17 23:38:04'),
(3, 3, 'Третий', 'Третий в рейтинге', 'pics/BlossomThirdIcon.svg', '2024-08-17 23:38:04');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `trophies`
--
ALTER TABLE `trophies`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `trophies`
--
ALTER TABLE `trophies`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
