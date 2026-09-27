-- OMERO GYM — database.sql
-- Import this file in phpMyAdmin (creates the database and all tables).
-- Run in phpMyAdmin's SQL tab, or: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS omero_gym CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE omero_gym;

-- 1. Users table (required by Phase 3 guideline)
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    phone      VARCHAR(30)   DEFAULT NULL,
    password   VARCHAR(255)  NOT NULL,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Contact / Messages table (required by Phase 3 guideline)
CREATE TABLE IF NOT EXISTS messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL,
    message    TEXT          NOT NULL,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Theme-specific table: OMERO GYM is a booking app, so bookings is the theme table
--    (mirrors the project's own README roadmap: "users, messages, bookings").
CREATE TABLE IF NOT EXISTS bookings (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT           NOT NULL,
    session_id    VARCHAR(50)   NOT NULL,   -- e.g. 'hiit-blast' (catalog id from app.js)
    title         VARCHAR(150)  NOT NULL,   -- e.g. 'High Intensity HIIT Blast'
    price         INT           NOT NULL DEFAULT 0,
    duration      INT           NOT NULL DEFAULT 0,   -- minutes
    booking_date  DATE          NOT NULL,
    booking_time  VARCHAR(20)   NOT NULL,   -- e.g. '06:00 AM'
    status        VARCHAR(20)   NOT NULL DEFAULT 'Confirmed',
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
