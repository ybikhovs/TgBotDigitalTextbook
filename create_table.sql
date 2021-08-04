-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Авг 04 2021 г., 18:44
-- Версия сервера: 10.2.31-MariaDB-cll-lve
-- Версия PHP: 7.4.18

--
-- Структура таблицы `bot_spisok`
--

CREATE TABLE `bot_spisok` (
  `logintg` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `second_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ok` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `theme` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `math` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bot_spisok`
--
ALTER TABLE `bot_spisok`
  ADD PRIMARY KEY (`logintg`),
  ADD UNIQUE KEY `logintg` (`logintg`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
