CREATE DATABASE IF NOT EXISTS books_api
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE books_api;

-- BOOKS
DROP TABLE IF EXISTS books;
CREATE TABLE books (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) NOT NULL,
    author      VARCHAR(150) NOT NULL,
    year        SMALLINT     NOT NULL,
    genre       VARCHAR(80)  NOT NULL DEFAULT 'Uncategorised',
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO books (title, author, year, genre) VALUES
  ('Clean Code',          'Robert C. Martin',  2008, 'Software Engineering'),
  ('Eloquent JavaScript', 'Marijn Haverbeke',   2018, 'Programming'),
  ('Vue.js 3 By Example', 'John Au-Yeung',      2021, 'Web Development');

-- USERS
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    email         VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('member','admin') NOT NULL DEFAULT 'member',
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO users (name, email, password_hash, role) VALUES
  ('Demo Admin',  'admin@books.test',  '$2y$10$IOdsnr4QxDRbaM23oUu5euzfFNXGyADfazm2JCo1YgfpFkYuFeHPG', 'admin'),
  ('Demo Member', 'member@books.test', '$2y$10$IOdsnr4QxDRbaM23oUu5euzfFNXGyADfazm2JCo1YgfpFkYuFeHPG', 'member');