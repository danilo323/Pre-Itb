-- Base de datos para el panel de administración ITB
CREATE DATABASE IF NOT EXISTS `itb_admin`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `itb_admin`;

-- Tabla de usuarios administradores
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email`         VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para persistencia de contenidos (textos, imágenes, etc.)
CREATE TABLE IF NOT EXISTS `site_content` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `section`     VARCHAR(64) NOT NULL,
    `field_key`   VARCHAR(64) NOT NULL,
    `field_value` LONGTEXT NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_section_field` (`section`, `field_key`),
    INDEX `idx_section` (`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
