-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.0
-- Время создания: Июл 24 2026 г., 17:48
-- Версия сервера: 8.0.41
-- Версия PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `pozharka`
--

-- --------------------------------------------------------

--
-- Структура таблицы `certificates`
--

CREATE TABLE `certificates` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `issued_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `certificates`
--

INSERT INTO `certificates` (`id`, `title`, `description`, `image`, `issued_date`, `created_at`) VALUES
(1, 'Сертификат соответствия ГОСТ', 'Соответствие всем требованиям государственных стандартов пожарной безопасности', 'certificate5.jpg', '2024-01-15', '2025-11-08 00:39:09'),
(2, 'Лицензия МЧС России', 'Официальная лицензия на осуществление деятельности в области пожарной безопасности', 'certificate2.jpg', '2023-11-20', '2025-11-08 00:39:09'),
(3, 'Сертификат ISO 9001', 'Международный стандарт системы менеджмента качества', 'certificate3.jpg', '2024-03-10', '2025-11-08 00:39:09'),
(4, 'Сертификат профессиональной подготовки', 'Подтверждение квалификации наших специалистов', 'certificate4.jpg', '2024-02-28', '2025-11-08 00:39:09'),
(5, 'Лицензия ', 'Лицензия деятельности по монтажу', 'certificate11.jpg', '2024-01-15', '2025-11-08 00:45:20');

-- --------------------------------------------------------

--
-- Структура таблицы `requests`
--

CREATE TABLE `requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_phone` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `comments` text COLLATE utf8mb4_general_ci,
  `service_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `requests`
--

INSERT INTO `requests` (`id`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `comments`, `service_id`, `created_at`) VALUES
(25, 7, 'Иванов Иван Иванович', 'ivan@gmail.com', '+78005352525', 'Просьба выполнить работу в кратчайшие сроки', 5, '2026-07-24 14:10:10'),
(26, 8, 'Михайлов Михаил Михайлович', 'misha@gmail.com', '+78005353535', '', 5, '2026-07-24 14:46:16');

-- --------------------------------------------------------

--
-- Структура таблицы `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `price`, `image`) VALUES
(1, 'Пожарный аудит объекта', 'Комплексная проверка здания на соответствие нормам пожарной безопасности с выдачей официального заключения', 15000.00, '6944638ba8402.jpg'),
(2, 'Установка пожарной сигнализации', 'Монтаж современной системы пожарной сигнализации с датчиками дыма и тепла', 25000.00, 'signal.jpg'),
(3, 'Обслуживание огнетушителей', 'Проверка, перезарядка и техническое обслуживание огнетушителей всех типов', 5000.00, 'povkval.jpg'),
(4, 'Пожарный инструктаж персонала', 'Обучение сотрудников правилам пожарной безопасности и действиям при возгорании', 8000.00, 'pozharniy.jpg'),
(5, 'Разработка плана эвакуации', 'Создание и оформление планов эвакуации при пожаре согласно ГОСТ', 12000.00, 'plan.jpg'),
(6, 'Монтаж систем автоматического пожаротушения', 'Установка автоматических систем пожаротушения (водяных, порошковых, газовых)', 45000.00, 'pozh.jpg'),
(8, 'Обратная связь', 'Сообщение из формы обратной связи', 0.00, '6944638ba8402.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `slider_images`
--

CREATE TABLE `slider_images` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `button_text` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Подробнее',
  `button_link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '#',
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `slider_images`
--

INSERT INTO `slider_images` (`id`, `title`, `description`, `image_path`, `button_text`, `button_link`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'Пожарная безопасность для бизнеса', 'Профессиональные решения для защиты вашего предприятия от пожара', 'slide1.jpg', 'Наши услуги', 'services.php', 1, 0, '2025-11-08 01:39:51'),
(2, 'Современные системы пожаротушения', 'Установка и обслуживание автоматических систем пожаротушения', 'slide2.jpg', 'Узнать больше', 'about.php', 1, 0, '2025-11-08 01:39:51'),
(3, 'Аудит и консультации', 'Полная проверка объекта на соответствие нормам пожарной безопасности', 'slide3.jpg', 'Заказать аудит', 'services.php', 1, 0, '2025-11-08 01:39:51');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `created_at`, `is_admin`) VALUES
(7, 'ivan@gmail.com', '$2y$10$css/Q3VRkL2GDDWDClWhl.p/9VlZ7iSWBU2EAyFAqM/N4G5viftpm', 'Иванов Иван Иванович', '2026-07-24 11:26:06', 0),
(8, 'misha@gmail.com', '$2y$10$pXc401IMb8nI4wOuHJoeKuUaPWUSLPiqrFk3C09.SVegRLtGWxZo2', 'Михайлов Михаил Михайлович', '2026-07-24 12:02:16', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Индексы таблицы `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `slider_images`
--
ALTER TABLE `slider_images`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT для таблицы `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `slider_images`
--
ALTER TABLE `slider_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
