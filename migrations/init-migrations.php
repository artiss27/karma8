<?php

require_once('./src/Core/functions.php');

Core::DB()->exec("DROP TABLE IF EXISTS `users`");
Core::DB()->exec("DROP TABLE IF EXISTS `emails`");
Core::DB()->exec("DROP TABLE IF EXISTS `emails_to_send`");

Core::DB()->exec("CREATE TABLE `users` (
            `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `validts` INT(11) NOT NULL,
            `confirmed` TINYINT(1) NOT NULL DEFAULT 0)"
);
Core::DB()->exec("ALTER TABLE `users` ADD INDEX `validts_confirmed` (`validts`, `confirmed`)");
Core::DB()->exec("ALTER TABLE `users` ADD UNIQUE `email` (`email`)");

Core::DB()->exec("CREATE TABLE `emails` (
            `email` VARCHAR(255) PRIMARY KEY,
            `checked` TINYINT(1) NOT NULL DEFAULT 0,
            `valid` TINYINT(1) NOT NULL DEFAULT 0)"
);

Core::DB()->exec("CREATE TABLE `emails_to_send` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `email` VARCHAR(255) NOT NULL,
          `from` VARCHAR(255) NOT NULL,
          `to` VARCHAR(255) NOT NULL,
          `subject` VARCHAR(255) NOT NULL,
          `body` TEXT NOT NULL,
          `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
          `ident` VARCHAR(255) NOT NULL DEFAULT '',
          `start_at` TIMESTAMP NOT NULL,
          `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)"
);
Core::DB()->exec("ALTER TABLE `emails_to_send` ADD INDEX `status_ident_email` (`status`, `ident`, `email`)");
Core::DB()->exec("ALTER TABLE `emails_to_send` ADD INDEX `created_at` (`created_at`)");