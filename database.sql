-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: fdb2.awardspace.net
-- Generation Time: Jul 17, 2020 at 04:50 PM
-- Server version: 5.7.20-log
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `2085653_oldpocket`
--

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(11) UNSIGNED NOT NULL,
  `created` datetime DEFAULT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `place` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `tokens_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `created`, `label`, `place`, `modified`, `tokens_id`) VALUES
(1, '2019-10-07 19:39:13', 'Arduino Uno', 'home office', '2019-10-07 19:39:17', 2),
(2, '2020-05-20 20:40:37', 'Arduino Uno', 'Home Office', '2020-05-20 20:40:37', 6),
(3, '2020-05-20 20:49:08', 'Arduino Uno', 'Home Office', '2020-05-20 20:49:08', 7),
(4, '2020-05-20 20:54:00', 'Arduino Uno', 'Home Office', '2020-05-20 20:54:00', 8),
(5, '2020-05-20 20:58:05', 'Arduino Uno', 'Home Office', '2020-05-20 20:58:05', 9);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id` int(11) UNSIGNED NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  `calling` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `message` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `log`
--


-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `uid`, `description`) VALUES
(1, 'device', 'Role for the devices'),
(2, 'backoffice', 'Backoffice user - can add other users and devices in the system');

-- --------------------------------------------------------

--
-- Table structure for table `roles_routes`
--

CREATE TABLE `roles_routes` (
  `id` int(11) UNSIGNED NOT NULL,
  `routes_id` int(11) UNSIGNED DEFAULT NULL,
  `roles_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `roles_routes`
--

INSERT INTO `roles_routes` (`id`, `routes_id`, `roles_id`) VALUES
(5, 6, 1),
(6, 7, 1),
(8, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(14, 13, 1),
(15, 14, 1),
(16, 15, 1),
(18, 16, 1),
(2, 4, 2),
(3, 5, 2),
(7, 8, 2),
(9, 9, 2),
(13, 12, 2),
(17, 15, 2),
(19, 16, 2),
(20, 17, 2),
(21, 18, 2),
(22, 19, 2),
(23, 20, 2);

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `route` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `verb` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `uid`, `description`, `route`, `verb`) VALUES
(4, 'deviceGet', 'Return device details', '/devices/', 'GET'),
(5, 'deviceDeviceUIDPost', 'Register a new device', '/devices/:deviceUID', 'POST'),
(6, 'deviceDeviceUIDPut', 'Update an existing device', '/devices/:deviceUID', 'PUT'),
(7, 'deviceDeviceUIDGet', 'Return device details', '/devices/:deviceUID', 'GET'),
(8, 'deviceDeviceUIDDelete', 'Remove a device', '/devices/:deviceUID', 'DELETE'),
(9, 'deviceDeviceUIDSensorsGet', 'Get data from a sensor', '/devices/:deviceUID/sensors', 'GET'),
(10, 'deviceDeviceUIDSensorsSensorUIDPost', 'Register a new sensor in an existing device', '/devices/:deviceUID/sensors/:sensorUID', 'POST'),
(11, 'deviceDeviceUIDSensorsSensorUIDPut', 'Update an existing sensor', '/devices/:deviceUID/sensors/:sensorUID', 'PUT'),
(12, 'deviceDeviceUIDSensorsSensorUIDGet', 'Return sensor details', '/devices/:deviceUID/sensors/:sensorUID', 'GET'),
(13, 'deviceDeviceUIDSensorsSensorUIDDelete', 'Remove a sensor', '/devices/:deviceUID/sensors/:sensorUID', 'DELETE'),
(14, 'deviceDeviceUIDSensorsSensorUIDDataPost', 'Add data to a sensor', '/devices/:deviceUID/sensors/:sensorUID/data', 'POST'),
(15, 'deviceDeviceUIDSensorsSensorUIDDataGet', 'Get data from a sensor', '/devices/:deviceUID/sensors/:sensorUID/data', 'GET'),
(16, 'deviceDeviceUIDSensorsSensorUIDDataDelete', 'Delete data within the timestamp', '/devices/:deviceUID/sensors/:sensorUID/data', 'DELETE'),
(17, 'usersUsernamePost', 'Create a new user in the system', '/users/:username', 'POST'),
(18, 'authorizationRolesRoleUIDPost', 'Create a new role in the system', '/authorization/roles/:roleUID', 'POST'),
(19, 'authorizationRoutesRouteUIDPost', 'Register a new route in the system', '/authorization/routes/:routeUID', 'POST'),
(20, 'authorizationPermissionRoleUIDRouteUIDPost', 'Associate a route with a role', '/authorization/permission/:roleUID/:routeUID', 'POST');

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE `sensors` (
  `id` int(11) UNSIGNED NOT NULL,
  `created` datetime DEFAULT NULL,
  `uid` int(11) UNSIGNED DEFAULT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `devices_id` int(11) UNSIGNED DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `sensors`
--

INSERT INTO `sensors` (`id`, `created`, `uid`, `label`, `devices_id`, `modified`) VALUES
(1, '2019-10-07 19:39:13', 1001, 'temperatura', 1, '2019-10-07 19:39:15'),
(2, '2019-10-07 19:39:13', 2002, 'humidade', 1, '2019-10-07 19:39:17'),
(3, '2019-10-07 19:39:13', 3002, 'pressao', 1, '2019-10-07 19:39:17'),
(4, '2020-05-20 20:40:37', 1001, 'temperatura', 2, '2020-05-20 20:40:37'),
(5, '2020-05-20 20:40:37', 2002, 'pressao', 2, '2020-05-20 20:40:37'),
(6, '2020-05-20 20:40:37', 3002, 'humidade', 2, '2020-05-20 20:40:37'),
(7, '2020-05-20 20:49:08', 1001, 'temperatura', 3, '2020-05-20 20:49:08'),
(8, '2020-05-20 20:49:08', 2002, 'pressao', 3, '2020-05-20 20:49:08'),
(9, '2020-05-20 20:49:08', 3002, 'humidade', 3, '2020-05-20 20:49:08'),
(10, '2020-05-20 20:54:00', 1001, 'temperatura', 4, '2020-05-20 20:54:00'),
(11, '2020-05-20 20:54:00', 2002, 'pressao', 4, '2020-05-20 20:54:00'),
(12, '2020-05-20 20:54:00', 3002, 'humidade', 4, '2020-05-20 20:54:00'),
(13, '2020-05-20 20:58:05', 1001, 'temperatura', 5, '2020-05-20 20:58:05'),
(14, '2020-05-20 20:58:05', 2002, 'pressao', 5, '2020-05-20 20:58:05'),
(15, '2020-05-20 20:58:05', 3002, 'humidade', 5, '2020-05-20 20:58:05');

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `secret` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `roles_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`id`, `username`, `secret`, `password`, `roles_id`) VALUES
(2, '1000', 'eKNiJDWYsyudQQ==', '$2y$10$4f5Ejxs5AqZWqJA.g1f6d.6OnJ237jQld8vM/Fa..EhAkN5Ux7jDm', NULL),
(5, 'aethiopicus', '92V+Xw9o6tlr7A==', '$2y$10$QHkVrff9iVds5ijT1lk1AeLAt5Kz/7j9rut.leskfeJHWnaVmvhxK', 2),
(6, 'DEVICE_1000', 'Kg+vkmN4HqNnhg==', '$2y$10$lxGp8iux0p1lBTCjca/04eewCd0ewm4BJMGjybGtjBAxsS.blzll2', NULL),
(7, 'DEVICE_1000', 'OoB2HL+p4nIdGw==', '$2y$10$GMP.HCOyy.WCTjxeyGSFVOnELazgZ0CFzBG3.knFX4rBfgFllhzzi', NULL),
(8, 'DEVICE_1000', 'MPozfPBP6nwB2g==', '$2y$10$yqHznWFsDU0awda7kwEq0Orpl8ejGFO16txq9lBubOzWvbvi3o3PW', NULL),
(9, 'DEVICE_1000', 'MBmW+9I7m+gGRA==', '$2y$10$KxNLkTe1l2V55U.PrnQKAeW.1VyaFznWCYT4j.HF24I63T33ynI7G', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `tokens_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `tokens_id`) VALUES
(1, 'Fabio Godoy', 'aethiopicus@gmail.com', NULL),
(2, 'Fabio Godoy', 'aethiopicus@gmail.com', NULL),
(3, 'Fabio Godoy', 'aethiopicus@gmail.com', NULL),
(4, 'Fabio Godoy', 'aethiopicus@gmail.com', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_devices_tokens` (`tokens_id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles_routes`
--
ALTER TABLE `roles_routes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_2b83abfc3517026366c2a812dc67e524456d822b` (`roles_id`,`routes_id`),
  ADD KEY `index_foreignkey_roles_routes_routes` (`routes_id`),
  ADD KEY `index_foreignkey_roles_routes_roles` (`roles_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sensors`
--
ALTER TABLE `sensors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_sensors_devices` (`devices_id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_tokens_roles` (`roles_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_users_tokens` (`tokens_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6418;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `roles_routes`
--
ALTER TABLE `roles_routes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `sensors`
--
ALTER TABLE `sensors`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `c_fk_devices_tokens_id` FOREIGN KEY (`tokens_id`) REFERENCES `tokens` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `roles_routes`
--
ALTER TABLE `roles_routes`
  ADD CONSTRAINT `c_fk_roles_routes_roles_id` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `c_fk_roles_routes_routes_id` FOREIGN KEY (`routes_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sensors`
--
ALTER TABLE `sensors`
  ADD CONSTRAINT `c_fk_sensors_devices_id` FOREIGN KEY (`devices_id`) REFERENCES `devices` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `c_fk_tokens_roles_id` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `c_fk_users_tokens_id` FOREIGN KEY (`tokens_id`) REFERENCES `tokens` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
