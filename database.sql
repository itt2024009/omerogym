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

-- 3a. Trainers table (new — lets admin add trainers and mark them available/unavailable,
--     shown as a pick-a-trainer dropdown on the Book a Slot page).
CREATE TABLE IF NOT EXISTS trainers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    specialty  VARCHAR(150)  DEFAULT NULL,
    available  TINYINT(1)    NOT NULL DEFAULT 1,   -- 1 = available to be picked, 0 = not
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3b. Session / workout-plan catalog (new — this is what used to be hardcoded inside
--     app.js. Moving it into the database is what lets the admin control workout
--     plans, categories and pricing from the admin site).
CREATE TABLE IF NOT EXISTS sessions_catalog (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    session_key   VARCHAR(50)   NOT NULL UNIQUE,   -- e.g. 'peak-floor', used as the booking id
    category      VARCHAR(30)   NOT NULL,          -- 'floor' | 'classes' | 'personal'
    title         VARCHAR(150)  NOT NULL,
    description   TEXT,
    price         INT           NOT NULL DEFAULT 0,
    duration      INT           NOT NULL DEFAULT 0,   -- minutes
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3c. Admins table (new — a separate login for the site owner/administrator, kept apart
--     from the regular "users" member table on purpose. Run admin/seed_admin.php once
--     after importing this file to create the one fixed admin account.)
CREATE TABLE IF NOT EXISTS admins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Theme-specific table: OMERO GYM is a booking app, so bookings is the theme table
--    (mirrors the project's own README roadmap: "users, messages, bookings").
--    trainer_id / trainer_name are new — a booking can optionally have a trainer attached.
CREATE TABLE IF NOT EXISTS bookings (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT           NOT NULL,
    session_id    VARCHAR(50)   NOT NULL,   -- e.g. 'hiit-blast' (session_key from sessions_catalog)
    title         VARCHAR(150)  NOT NULL,   -- e.g. 'High Intensity HIIT Blast'
    price         INT           NOT NULL DEFAULT 0,
    duration      INT           NOT NULL DEFAULT 0,   -- minutes
    trainer_id    INT           DEFAULT NULL,
    trainer_name  VARCHAR(100)  DEFAULT NULL,
    booking_date  DATE          NOT NULL,
    booking_time  VARCHAR(20)   NOT NULL,   -- e.g. '06:00 AM'
    status        VARCHAR(20)   NOT NULL DEFAULT 'Confirmed',
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_user    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_trainer FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Seed data so the site has content immediately after import ----------------------

-- Starter workout plans / pricing (same values the site launched with — the admin can
-- now edit price/duration/description or add new ones from the admin site).
INSERT INTO sessions_catalog (session_key, category, title, description, price, duration) VALUES
('peak-floor',    'floor',    'Peak Hours Floor Pass',            'Reservation for dynamic weight training and cardio equipment during premium high-energy intervals.', 1500, 90),
('offpeak-floor', 'floor',    'Off-Peak Floor Pass',              'Perfect for crowd-free workouts with total accessibility to all lifting racks and fitness gear.', 1000, 120),
('hiit-blast',    'classes',  'High Intensity HIIT Blast',        'Metabolic conditioning, functional intervals, and explosive plyometrics orchestrated by elite instructors.', 2500, 60),
('elite-power',   'personal', 'Elite Power & Strength Coaching',  'Custom programming focusing heavily on biomechanics, heavy lifting form, and progressive overloading vectors.', 5000, 60)
ON DUPLICATE KEY UPDATE session_key = session_key;

-- Starter trainers — one marked unavailable on purpose so the "not selectable" state
-- in the Book a Slot dropdown is visible right after import.
INSERT INTO trainers (name, specialty, available) VALUES
('Kasun Perera',      'Strength & Conditioning',     1),
('Nadeesha Fernando',  'HIIT & Metabolic Coaching',    1),
('Ruwan Jayasuriya',   'Personal Training',            0);

-- The one fixed admin account, seeded directly so no separate setup step is needed.
-- Login at /OMERO-GYM/admin/login.php with:
--   Email:    admin@omerogym.com
--   Password: Omero@Gym2026
-- Change the password from the database (or add a "change password" admin page)
-- once you've logged in — this hash is only meant to get you started.
INSERT INTO admins (name, email, password) VALUES
('Gym Owner', 'admin@omerogym.com', '$2b$10$SiJl3IF0kOTZsfCdQ.lTy.hicJT9qNLnFJ96.xRH/H0coBd4xAxw.')
ON DUPLICATE KEY UPDATE email = email;
