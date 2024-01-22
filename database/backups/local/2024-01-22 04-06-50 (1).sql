-- Tablo Adı: posts
-- Satır Sayısı: 0

CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Tablo Adı: test
-- Satır Sayısı: 0

CREATE TABLE `test` (
  `test_id` int(11) NOT NULL AUTO_INCREMENT,
  `test_ehehe` varchar(255) DEFAULT NULL,
  `test_test` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Tablo Adı: users
-- Satır Sayısı: 2

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(51) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `api_token` varchar(60) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;


INSERT INTO `users` (`id`,`username`,`password`,`email`,`api_token`,`updated_at`,`created_at`,`deleted_at`) VALUES 
('1','Admin','093d3dk=ThisSaltIsSecret','admin@localhost.com','qcrZOIPRnVrDQaCA3HsHkJwggVwlwpXOBYkDkgbCft2iPmw8xDrRb240oVqJ','2024-01-22 06:17:02','2024-01-22 05:57:28',''),
('2','Test','','','VlXdoZFC4oTTEc6gTUTPccZifYVYMNZbsHiYEqintRknjhMKCSKPFejpX7VK','2024-01-22 06:40:14','2024-01-22 06:40:14','');




