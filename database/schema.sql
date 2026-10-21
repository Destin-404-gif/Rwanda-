-- =====================================================================
--  RWANDA EXPLORER - MySQL database schema (PHP + InfinityFree ready)
--  Engine: InnoDB, charset: utf8mb4, MySQL 5.7 / 8.0 compatible
--
--  Import through phpMyAdmin (no CREATE DATABASE / USE statements):
--      1. create the database (utf8mb4_unicode_ci)
--      2. import this file, then database/seed.sql
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ---------------------------------------------------------------------
-- users  (single accounts table, role = customer | admin)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(120) NOT NULL,
  `email`          VARCHAR(180) NOT NULL,
  `phone`          VARCHAR(40)  DEFAULT NULL,
  `password_hash`  VARCHAR(255) NOT NULL,
  `role`           ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  `avatar`         VARCHAR(255) DEFAULT NULL,
  `nationality`    VARCHAR(80)  DEFAULT NULL,
  `loyalty_points` INT UNSIGNED NOT NULL DEFAULT 0,
  `status`         ENUM('active','pending','rejected','suspended') NOT NULL DEFAULT 'active',
  `email_verified` TINYINT(1)   NOT NULL DEFAULT 0,
  `last_login_at`  DATETIME     DEFAULT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- password_resets
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used_at`    DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pr_user` (`user_id`),
  KEY `idx_pr_token` (`token`),
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- login_attempts  (rate limiting)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip`          VARCHAR(45) NOT NULL,
  `email`       VARCHAR(180) NOT NULL,
  `successful`  TINYINT(1) NOT NULL DEFAULT 0,
  `attempted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_attempts_ip_email_time` (`ip`,`email`,`attempted_at`),
  KEY `idx_attempts_time` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- destinations
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `destinations`;
CREATE TABLE `destinations` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`              VARCHAR(180) NOT NULL,
  `name`              VARCHAR(180) NOT NULL,
  `region`            VARCHAR(120) NOT NULL,
  `description`       TEXT,
  `short_description` VARCHAR(255) DEFAULT NULL,
  `hero_image`        VARCHAR(255) DEFAULT NULL,
  `gallery`           JSON DEFAULT NULL,
  `activities`        JSON DEFAULT NULL,
  `best_season`       VARCHAR(80)  DEFAULT NULL,
  `entry_fee`         DECIMAL(10,2) DEFAULT 0,
  `lat`               DECIMAL(10,7) NOT NULL DEFAULT 0,
  `lng`               DECIMAL(10,7) NOT NULL DEFAULT 0,
  `how_to_get_there`  TEXT,
  `meta_title`        VARCHAR(180) DEFAULT NULL,
  `meta_description`  VARCHAR(300) DEFAULT NULL,
  `is_featured`       TINYINT(1) NOT NULL DEFAULT 0,
  `status`            ENUM('published','draft') NOT NULL DEFAULT 'published',
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dest_slug` (`slug`),
  KEY `idx_dest_region` (`region`),
  KEY `idx_dest_lat_lng` (`lat`,`lng`),
  KEY `idx_dest_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- hotels
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hotels`;
CREATE TABLE `hotels` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_id`       BIGINT UNSIGNED DEFAULT NULL,
  `destination_id` BIGINT UNSIGNED DEFAULT NULL,
  `slug`           VARCHAR(180) NOT NULL,
  `name`           VARCHAR(180) NOT NULL,
  `type`           ENUM('hotel','lodge','guesthouse','resort','homestay') NOT NULL DEFAULT 'hotel',
  `stars`          TINYINT UNSIGNED NOT NULL DEFAULT 3,
  `city`           VARCHAR(120) NOT NULL,
  `address`        VARCHAR(255) DEFAULT NULL,
  `description`    TEXT,
  `amenities`      JSON DEFAULT NULL,
  `base_price`     DECIMAL(12,2) NOT NULL DEFAULT 0,
  `currency`       CHAR(3) NOT NULL DEFAULT 'RWF',
  `cover_image`    VARCHAR(255) DEFAULT NULL,
  `gallery`        JSON DEFAULT NULL,
  `lat`            DECIMAL(10,7) NOT NULL DEFAULT 0,
  `lng`            DECIMAL(10,7) NOT NULL DEFAULT 0,
  `phone`          VARCHAR(40) DEFAULT NULL,
  `email`          VARCHAR(180) DEFAULT NULL,
  `check_in_time`  VARCHAR(20) DEFAULT '14:00',
  `check_out_time` VARCHAR(20) DEFAULT '11:00',
  `policies`       TEXT,
  `commission_pct` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
  `rating`         DECIMAL(3,2) NOT NULL DEFAULT 0,
  `review_count`   INT UNSIGNED NOT NULL DEFAULT 0,
  `status`         ENUM('pending','approved','rejected','suspended') NOT NULL DEFAULT 'approved',
  `is_featured`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_hotel_slug` (`slug`),
  KEY `idx_hotel_owner` (`owner_id`),
  KEY `idx_hotel_dest` (`destination_id`),
  KEY `idx_hotel_lat_lng` (`lat`,`lng`),
  KEY `idx_hotel_status` (`status`),
  KEY `idx_hotel_price` (`base_price`),
  CONSTRAINT `fk_hotel_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_hotel_dest` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- rooms
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hotel_id`    BIGINT UNSIGNED NOT NULL,
  `name`        VARCHAR(120) NOT NULL,
  `room_type`   VARCHAR(80) DEFAULT NULL,
  `description` TEXT,
  `size_sqm`    INT UNSIGNED DEFAULT NULL,
  `beds`        VARCHAR(120) DEFAULT NULL,
  `capacity`    TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `price`       DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total_units` INT UNSIGNED NOT NULL DEFAULT 1,
  `features`    JSON DEFAULT NULL,
  `images`      JSON DEFAULT NULL,
  `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_room_hotel` (`hotel_id`),
  CONSTRAINT `fk_room_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- room_availability
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `room_availability`;
CREATE TABLE `room_availability` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id`         BIGINT UNSIGNED NOT NULL,
  `date`            DATE NOT NULL,
  `units_available` INT UNSIGNED NOT NULL DEFAULT 0,
  `price`           DECIMAL(12,2) DEFAULT NULL,
  `is_blocked`      TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_avail_room_date` (`room_id`,`date`),
  KEY `idx_avail_date` (`date`),
  CONSTRAINT `fk_avail_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- tours
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `tours`;
CREATE TABLE `tours` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `operator_id`    BIGINT UNSIGNED DEFAULT NULL,
  `destination_id` BIGINT UNSIGNED DEFAULT NULL,
  `slug`           VARCHAR(180) NOT NULL,
  `title`          VARCHAR(200) NOT NULL,
  `category`       VARCHAR(80) NOT NULL,
  `duration`       VARCHAR(50) NOT NULL,
  `duration_days`  TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `difficulty`     ENUM('easy','moderate','hard') NOT NULL DEFAULT 'easy',
  `group_size`     VARCHAR(50) DEFAULT NULL,
  `max_capacity`   INT UNSIGNED NOT NULL DEFAULT 12,
  `price`          DECIMAL(12,2) NOT NULL DEFAULT 0,
  `discount_pct`   DECIMAL(5,2) NOT NULL DEFAULT 0,
  `description`    TEXT,
  `itinerary`      JSON DEFAULT NULL,
  `included`       JSON DEFAULT NULL,
  `excluded`       JSON DEFAULT NULL,
  `meeting_point`  VARCHAR(255) DEFAULT NULL,
  `guide_id`       BIGINT UNSIGNED DEFAULT NULL,
  `cover_image`    VARCHAR(255) DEFAULT NULL,
  `gallery`        JSON DEFAULT NULL,
  `rating`         DECIMAL(3,2) NOT NULL DEFAULT 0,
  `review_count`   INT UNSIGNED NOT NULL DEFAULT 0,
  `status`         ENUM('draft','published','archived') NOT NULL DEFAULT 'published',
  `is_featured`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tour_slug` (`slug`),
  KEY `idx_tour_operator` (`operator_id`),
  KEY `idx_tour_dest` (`destination_id`),
  KEY `idx_tour_category` (`category`),
  CONSTRAINT `fk_tour_operator` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_tour_dest` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- tour_dates
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `tour_dates`;
CREATE TABLE `tour_dates` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tour_id`        BIGINT UNSIGNED NOT NULL,
  `start_date`     DATE NOT NULL,
  `end_date`       DATE DEFAULT NULL,
  `capacity`       INT UNSIGNED NOT NULL DEFAULT 12,
  `booked`         INT UNSIGNED NOT NULL DEFAULT 0,
  `seasonal_price` DECIMAL(12,2) DEFAULT NULL,
  `status`         ENUM('open','closed','cancelled') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tourdate` (`tour_id`,`start_date`),
  KEY `idx_tourdate_start` (`start_date`),
  CONSTRAINT `fk_tourdate_tour` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- guides
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `guides`;
CREATE TABLE `guides` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         BIGINT UNSIGNED DEFAULT NULL,
  `name`            VARCHAR(120) NOT NULL,
  `type`            ENUM('guide','driver') NOT NULL DEFAULT 'guide',
  `languages`       JSON DEFAULT NULL,
  `specialties`     JSON DEFAULT NULL,
  `experience_years` TINYINT UNSIGNED DEFAULT 0,
  `daily_rate`      DECIMAL(12,2) DEFAULT 0,
  `photo`           VARCHAR(255) DEFAULT NULL,
  `bio`             TEXT,
  `rating`          DECIMAL(3,2) NOT NULL DEFAULT 0,
  `status`          ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_guide_user` (`user_id`),
  CONSTRAINT `fk_guide_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- coupons
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`        VARCHAR(50) NOT NULL,
  `type`        ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  `value`       DECIMAL(12,2) NOT NULL DEFAULT 0,
  `min_spend`   DECIMAL(12,2) NOT NULL DEFAULT 0,
  `usage_limit` INT UNSIGNED DEFAULT NULL,
  `used_count`  INT UNSIGNED NOT NULL DEFAULT 0,
  `starts_at`   DATE DEFAULT NULL,
  `expires_at`  DATE DEFAULT NULL,
  `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_coupon_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- bookings
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference`      VARCHAR(30) NOT NULL,
  `user_id`        BIGINT UNSIGNED DEFAULT NULL,
  `booking_type`   ENUM('hotel','tour','permit','transfer','car','guide') NOT NULL,
  `hotel_id`       BIGINT UNSIGNED DEFAULT NULL,
  `tour_id`        BIGINT UNSIGNED DEFAULT NULL,
  `tour_date_id`   BIGINT UNSIGNED DEFAULT NULL,
  `coupon_id`      BIGINT UNSIGNED DEFAULT NULL,
  `check_in`       DATE DEFAULT NULL,
  `check_out`      DATE DEFAULT NULL,
  `travel_date`    DATE DEFAULT NULL,
  `guests`         INT UNSIGNED NOT NULL DEFAULT 1,
  `rooms`          INT UNSIGNED NOT NULL DEFAULT 1,
  `subtotal`       DECIMAL(12,2) NOT NULL DEFAULT 0,
  `extras_total`   DECIMAL(12,2) NOT NULL DEFAULT 0,
  `discount_total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `tax_total`      DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total`          DECIMAL(12,2) NOT NULL DEFAULT 0,
  `paid_total`     DECIMAL(12,2) NOT NULL DEFAULT 0,
  `currency`       CHAR(3) NOT NULL DEFAULT 'RWF',
  `status`         ENUM('pending','confirmed','cancelled','completed','refunded') NOT NULL DEFAULT 'pending',
  `payment_status` ENUM('unpaid','deposit_paid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `extras`         JSON DEFAULT NULL,
  `customer_name`  VARCHAR(120) DEFAULT NULL,
  `customer_email` VARCHAR(180) DEFAULT NULL,
  `customer_phone` VARCHAR(40) DEFAULT NULL,
  `nationality`    VARCHAR(80) DEFAULT NULL,
  `notes`          TEXT,
  `source`         ENUM('website','dashboard','phone') NOT NULL DEFAULT 'website',
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_booking_ref` (`reference`),
  KEY `idx_booking_user` (`user_id`),
  KEY `idx_booking_hotel` (`hotel_id`),
  KEY `idx_booking_tour` (`tour_id`),
  KEY `idx_booking_status` (`status`),
  KEY `idx_booking_created` (`created_at`),
  CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_booking_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_booking_tour` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_booking_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- booking_items
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `booking_items`;
CREATE TABLE `booking_items` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` BIGINT UNSIGNED NOT NULL,
  `item_type`  VARCHAR(50) NOT NULL,
  `item_id`    BIGINT UNSIGNED DEFAULT NULL,
  `name`       VARCHAR(200) NOT NULL,
  `qty`        INT UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `line_total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_bi_booking` (`booking_id`),
  CONSTRAINT `fk_bi_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- payments
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id`      BIGINT UNSIGNED NOT NULL,
  `provider`        ENUM('mtn','airtel','stripe','flutterwave','pesapal','cash','bank') NOT NULL,
  `method`          VARCHAR(50) DEFAULT NULL,
  `amount`          DECIMAL(12,2) NOT NULL DEFAULT 0,
  `currency`        CHAR(3) NOT NULL DEFAULT 'RWF',
  `status`          ENUM('pending','successful','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_ref` VARCHAR(120) DEFAULT NULL,
  `provider_payload` JSON DEFAULT NULL,
  `paid_at`         DATETIME DEFAULT NULL,
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pay_booking` (`booking_id`),
  KEY `idx_pay_status` (`status`),
  CONSTRAINT `fk_pay_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- permits
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `permits`;
CREATE TABLE `permits` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id`      BIGINT UNSIGNED DEFAULT NULL,
  `user_id`         BIGINT UNSIGNED DEFAULT NULL,
  `permit_type`     ENUM('gorilla','golden_monkey','chimp','other') NOT NULL DEFAULT 'gorilla',
  `full_name`       VARCHAR(120) NOT NULL,
  `email`           VARCHAR(180) NOT NULL,
  `phone`           VARCHAR(40) DEFAULT NULL,
  `nationality`     VARCHAR(80) DEFAULT NULL,
  `passport_no`     VARCHAR(60) DEFAULT NULL,
  `passport_expiry` DATE DEFAULT NULL,
  `permit_date`     DATE NOT NULL,
  `num_permits`     INT UNSIGNED NOT NULL DEFAULT 1,
  `price_each`      DECIMAL(12,2) NOT NULL DEFAULT 1500,
  `status`          ENUM('requested','approved','paid','rejected','issued') NOT NULL DEFAULT 'requested',
  `notes`           TEXT,
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_permit_booking` (`booking_id`),
  KEY `idx_permit_user` (`user_id`),
  KEY `idx_permit_status` (`status`),
  CONSTRAINT `fk_permit_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_permit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- reviews
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED DEFAULT NULL,
  `target_type` ENUM('hotel','tour','destination') NOT NULL,
  `target_id`   BIGINT UNSIGNED NOT NULL,
  `booking_id`  BIGINT UNSIGNED DEFAULT NULL,
  `rating`      TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `title`       VARCHAR(180) DEFAULT NULL,
  `comment`     TEXT,
  `photos`      JSON DEFAULT NULL,
  `author_name` VARCHAR(120) DEFAULT NULL,
  `status`      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reply`       TEXT DEFAULT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_review_target` (`target_type`,`target_id`),
  KEY `idx_review_status` (`status`),
  CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_review_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- wishlists (favorites)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE `wishlists` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `target_type` ENUM('hotel','tour','destination') NOT NULL,
  `target_id`   BIGINT UNSIGNED NOT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wishlist` (`user_id`,`target_type`,`target_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- itineraries (trip planner)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `itineraries`;
CREATE TABLE `itineraries` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED DEFAULT NULL,
  `title`      VARCHAR(180) NOT NULL,
  `share_slug` VARCHAR(60) NOT NULL,
  `days`       INT UNSIGNED NOT NULL DEFAULT 1,
  `budget`     DECIMAL(12,2) DEFAULT 0,
  `interests`  JSON DEFAULT NULL,
  `items`      JSON DEFAULT NULL,
  `notes`      TEXT,
  `is_public`  TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_itinerary_slug` (`share_slug`),
  KEY `idx_itinerary_user` (`user_id`),
  CONSTRAINT `fk_itinerary_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- blog_posts
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id`        BIGINT UNSIGNED DEFAULT NULL,
  `slug`             VARCHAR(200) NOT NULL,
  `title`            VARCHAR(220) NOT NULL,
  `category`         VARCHAR(80) DEFAULT NULL,
  `excerpt`          VARCHAR(400) DEFAULT NULL,
  `content`          LONGTEXT,
  `cover_image`      VARCHAR(255) DEFAULT NULL,
  `read_minutes`     TINYINT UNSIGNED DEFAULT 5,
  `tags`             JSON DEFAULT NULL,
  `meta_title`       VARCHAR(200) DEFAULT NULL,
  `meta_description` VARCHAR(300) DEFAULT NULL,
  `status`           ENUM('draft','published') NOT NULL DEFAULT 'published',
  `published_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_post_slug` (`slug`),
  KEY `idx_post_status` (`status`),
  CONSTRAINT `fk_post_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- gallery
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(180) DEFAULT NULL,
  `category`   VARCHAR(80) DEFAULT NULL,
  `media_type` ENUM('image','video') NOT NULL DEFAULT 'image',
  `url`        VARCHAR(255) NOT NULL,
  `thumb_url`  VARCHAR(255) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gallery_cat` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- hero_slides
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hero_slides`;
CREATE TABLE `hero_slides` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title_line1` VARCHAR(80) NOT NULL,
  `title_line2` VARCHAR(80) NOT NULL,
  `subtitle`    VARCHAR(160) DEFAULT NULL,
  `image`       VARCHAR(255) NOT NULL,
  `card_title`  VARCHAR(120) DEFAULT NULL,
  `card_desc`   VARCHAR(160) DEFAULT NULL,
  `card_thumb`  VARCHAR(255) DEFAULT NULL,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `idx_hero_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- testimonials
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id`      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(120) NOT NULL,
  `country` VARCHAR(80) DEFAULT NULL,
  `rating`  TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `text`    TEXT,
  `avatar`  VARCHAR(255) DEFAULT NULL,
  `status`  ENUM('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- notifications
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED DEFAULT NULL,
  `type`       VARCHAR(60) DEFAULT NULL,
  `title`      VARCHAR(180) NOT NULL,
  `message`    TEXT,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `channel`    ENUM('app','email','sms') NOT NULL DEFAULT 'app',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- settings
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` VARCHAR(60) NOT NULL DEFAULT 'general',
  `key_name`   VARCHAR(120) NOT NULL,
  `value`      TEXT,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_setting_key` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
-- =====================================================================
--  End of schema
-- =====================================================================
