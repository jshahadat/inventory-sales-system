-- =====================================================================
-- Inventory & Sales Management System - Database Schema
-- Engine: InnoDB (required for Foreign Keys) | Charset: utf8mb4
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `inventory_sales`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `inventory_sales`;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. USER  (auth / login table used by Yii2 IdentityInterface)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username`         VARCHAR(64)  NOT NULL,
  `auth_key`         VARCHAR(32)  NOT NULL,
  `password_hash`    VARCHAR(255) NOT NULL,
  `password_reset_token` VARCHAR(255) DEFAULT NULL,
  `email`            VARCHAR(128) NOT NULL,
  `status`           SMALLINT UNSIGNED NOT NULL DEFAULT 10 COMMENT '10=active,0=inactive',
  `created_at`       INT UNSIGNED NOT NULL,
  `updated_at`       INT UNSIGNED NOT NULL,
  UNIQUE KEY `ux_user_username` (`username`),
  UNIQUE KEY `ux_user_email` (`email`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. CATEGORY
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `ux_category_name` (`name`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. SUPPLIER
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`         VARCHAR(150) NOT NULL,
  `phone`        VARCHAR(30)  DEFAULT NULL,
  `email`        VARCHAR(128) DEFAULT NULL,
  `address`      VARCHAR(255) DEFAULT NULL,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_supplier_name` (`name`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. CUSTOMER
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`         VARCHAR(150) NOT NULL,
  `phone`        VARCHAR(30)  DEFAULT NULL,
  `email`        VARCHAR(128) DEFAULT NULL,
  `address`      VARCHAR(255) DEFAULT NULL,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_customer_name` (`name`),
  KEY `idx_customer_phone` (`phone`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. PRODUCT  (FK -> category, supplier)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id`  INT UNSIGNED NOT NULL,
  `supplier_id`  INT UNSIGNED NOT NULL,
  `sku`          VARCHAR(50)  NOT NULL,
  `name`         VARCHAR(150) NOT NULL,
  `unit_price`   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `stock_qty`    INT NOT NULL DEFAULT 0,
  `reorder_level` INT NOT NULL DEFAULT 5,
  `status`       ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `ux_product_sku` (`sku`),
  KEY `idx_product_name` (`name`),
  KEY `idx_product_category` (`category_id`),
  KEY `idx_product_supplier` (`supplier_id`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `category`(`id`)
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_product_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `supplier`(`id`)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. SALES_INVOICE  (FK -> customer, user)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `sales_invoice`;
CREATE TABLE `sales_invoice` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_no`    VARCHAR(30) NOT NULL,
  `customer_id`   INT UNSIGNED NOT NULL,
  `created_by`    INT UNSIGNED NOT NULL COMMENT 'user.id who created invoice',
  `invoice_date`  DATE NOT NULL,
  `sub_total`     DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `discount`      DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `tax`           DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `grand_total`   DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `status`        ENUM('draft','paid','due','cancelled') NOT NULL DEFAULT 'due',
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `ux_invoice_no` (`invoice_no`),
  KEY `idx_invoice_customer` (`customer_id`),
  KEY `idx_invoice_date` (`invoice_date`),
  KEY `idx_invoice_status` (`status`),
  CONSTRAINT `fk_invoice_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer`(`id`)
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_invoice_user` FOREIGN KEY (`created_by`) REFERENCES `user`(`id`)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. SALES_INVOICE_ITEM  (line items, FK -> sales_invoice, product)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `sales_invoice_item`;
CREATE TABLE `sales_invoice_item` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_id`    INT UNSIGNED NOT NULL,
  `product_id`    INT UNSIGNED NOT NULL,
  `qty`           INT NOT NULL DEFAULT 1,
  `unit_price`    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `line_total`    DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  KEY `idx_item_invoice` (`invoice_id`),
  KEY `idx_item_product` (`product_id`),
  CONSTRAINT `fk_item_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice`(`id`)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- RBAC core tables (Yii2 DbManager default schema)
-- Generated normally via: yii migrate --migrationPath=@yii/rbac/migrations
-- Included here for completeness.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name`       VARCHAR(64) NOT NULL PRIMARY KEY,
  `data`       BLOB,
  `created_at` INT,
  `updated_at` INT
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name`        VARCHAR(64) NOT NULL PRIMARY KEY,
  `type`        SMALLINT NOT NULL,
  `description` TEXT,
  `rule_name`   VARCHAR(64) DEFAULT NULL,
  `data`        BLOB,
  `created_at`  INT,
  `updated_at`  INT,
  KEY `idx_auth_item_type` (`type`),
  CONSTRAINT `fk_auth_item_rule` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule`(`name`)
      ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` VARCHAR(64) NOT NULL,
  `child`  VARCHAR(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  CONSTRAINT `fk_aic_parent` FOREIGN KEY (`parent`) REFERENCES `auth_item`(`name`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_aic_child`  FOREIGN KEY (`child`)  REFERENCES `auth_item`(`name`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name`  VARCHAR(64) NOT NULL,
  `user_id`    VARCHAR(64) NOT NULL,
  `created_at` INT,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `fk_assignment_item` FOREIGN KEY (`item_name`) REFERENCES `auth_item`(`name`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- STORED PROCEDURE #1: Create a Sales Invoice + Items atomically,
-- and decrement product stock in the same transaction (optimization:
-- avoids N round-trips from PHP, keeps stock consistent under load).
-- Call example:
--   CALL sp_create_invoice('INV-0001', 3, 1, '2026-07-03', 0, 0,
--        '[{"product_id":1,"qty":2,"unit_price":150.00}]');
-- =====================================================================
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_create_invoice $$
CREATE PROCEDURE sp_create_invoice(
  IN p_invoice_no  VARCHAR(30),
  IN p_customer_id INT UNSIGNED,
  IN p_created_by  INT UNSIGNED,
  IN p_invoice_date DATE,
  IN p_discount    DECIMAL(14,2),
  IN p_tax         DECIMAL(14,2),
  IN p_items_json  JSON
)
BEGIN
  DECLARE v_invoice_id   INT UNSIGNED;
  DECLARE v_sub_total    DECIMAL(14,2) DEFAULT 0;
  DECLARE v_grand_total  DECIMAL(14,2) DEFAULT 0;
  DECLARE v_idx          INT DEFAULT 0;
  DECLARE v_count        INT;
  DECLARE v_product_id   INT UNSIGNED;
  DECLARE v_qty          INT;
  DECLARE v_price        DECIMAL(12,2);
  DECLARE v_line_total   DECIMAL(14,2);

  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;

  START TRANSACTION;

  INSERT INTO sales_invoice
    (invoice_no, customer_id, created_by, invoice_date, sub_total, discount, tax, grand_total, status)
  VALUES
    (p_invoice_no, p_customer_id, p_created_by, p_invoice_date, 0, p_discount, p_tax, 0, 'due');

  SET v_invoice_id = LAST_INSERT_ID();
  SET v_count = JSON_LENGTH(p_items_json);

  WHILE v_idx < v_count DO
    SET v_product_id = JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', v_idx, '].product_id')));
    SET v_qty         = JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', v_idx, '].qty')));
    SET v_price        = JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', v_idx, '].unit_price')));
    SET v_line_total    = v_qty * v_price;
    SET v_sub_total      = v_sub_total + v_line_total;

    INSERT INTO sales_invoice_item (invoice_id, product_id, qty, unit_price, line_total)
    VALUES (v_invoice_id, v_product_id, v_qty, v_price, v_line_total);

    UPDATE product SET stock_qty = stock_qty - v_qty WHERE id = v_product_id;

    SET v_idx = v_idx + 1;
  END WHILE;

  SET v_grand_total = v_sub_total - p_discount + p_tax;

  UPDATE sales_invoice
     SET sub_total = v_sub_total, grand_total = v_grand_total
   WHERE id = v_invoice_id;

  COMMIT;

  SELECT v_invoice_id AS invoice_id, v_sub_total AS sub_total, v_grand_total AS grand_total;
END $$

-- =====================================================================
-- STORED PROCEDURE #2: Low-stock report (used for dashboard widget)
-- =====================================================================
DROP PROCEDURE IF EXISTS sp_low_stock_report $$
CREATE PROCEDURE sp_low_stock_report()
BEGIN
  SELECT p.id, p.sku, p.name, p.stock_qty, p.reorder_level, c.name AS category_name
  FROM product p
  JOIN category c ON c.id = p.category_id
  WHERE p.stock_qty <= p.reorder_level AND p.status = 'active'
  ORDER BY p.stock_qty ASC;
END $$

DELIMITER ;

-- ---------------------------------------------------------------------
-- Seed: default admin user (password = "admin123", change immediately)
-- password_hash generated with Yii2 Security::generatePasswordHash()
-- ---------------------------------------------------------------------
-- INSERT INTO user (username, auth_key, password_hash, email, status, created_at, updated_at)
-- VALUES ('admin', 'REPLACE_WITH_RANDOM_KEY', '$2y$13$REPLACE_WITH_REAL_HASH', 'admin@example.com', 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
