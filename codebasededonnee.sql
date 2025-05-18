-- Supprimer la base de données si elle existe
DROP DATABASE IF EXISTS po;
CREATE DATABASE po;
USE po;

-- Créer la table filmsnetflix et charger les données
CREATE TABLE filmsnetflix (
    show_id VARCHAR(50) PRIMARY KEY,
    type VARCHAR(50),
    title VARCHAR(300),
    director VARCHAR(5000),
    cast TEXT,
    country VARCHAR(200),
    date_added VARCHAR(50),
    release_year YEAR,
    rating VARCHAR(50),
    duration VARCHAR(50),
    listed_in VARCHAR(255),
    description TEXT
);

LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/netflix_titles.csv'
INTO TABLE filmsnetflix
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Créer la table filmsdisney et charger les données
CREATE TABLE filmsdisney (
    show_id VARCHAR(50) PRIMARY KEY,
    type VARCHAR(50),
    title VARCHAR(300),
    director VARCHAR(5000),
    cast TEXT,
    country VARCHAR(200),
    date_added VARCHAR(50),
    release_year YEAR,
    rating VARCHAR(50),
    duration VARCHAR(50),
    listed_in VARCHAR(255),
    description TEXT
);

LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/disney_plus_titles.csv'
INTO TABLE filmsdisney
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Créer la table filmsprime et charger les données
CREATE TABLE filmsprime (
    show_id VARCHAR(50) PRIMARY KEY,
    type VARCHAR(50),
    title VARCHAR(300),
    director VARCHAR(5000),
    cast TEXT,
    country VARCHAR(200),
    date_added VARCHAR(50),
    release_year YEAR,
    rating VARCHAR(50),
    duration VARCHAR(50),
    listed_in VARCHAR(255),
    description TEXT
);

LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/amazon_prime_titles.csv'
INTO TABLE filmsprime
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Créer la table filmsa pour rassembler les données
CREATE TABLE filmsa (
    show_id VARCHAR(50),
    type VARCHAR(50),
    title VARCHAR(300),
    director VARCHAR(5000),
    cast TEXT,
    country VARCHAR(200),
    date_added VARCHAR(50),
    release_year YEAR,
    rating VARCHAR(50),
    duration VARCHAR(50),
    listed_in VARCHAR(255),
    description TEXT,
    platform ENUM('netflix', 'disney', 'prime') NOT NULL,
    PRIMARY KEY (show_id, platform)
);

-- Insérer les données de filmsnetflix dans filmsa
INSERT INTO filmsa (show_id, type, title, director, cast, country, date_added, release_year, rating, duration, listed_in, description, platform)
SELECT show_id, type, title, director, cast, country, date_added, release_year, rating, duration, listed_in, description, 'netflix'
FROM filmsnetflix;

-- Insérer les données de filmsdisney dans filmsa
INSERT INTO filmsa (show_id, type, title, director, cast, country, date_added, release_year, rating, duration, listed_in, description, platform)
SELECT show_id, type, title, director, cast, country, date_added, release_year, rating, duration, listed_in, description, 'disney'
FROM filmsdisney;

-- Insérer les données de filmsprime dans filmsa
INSERT INTO filmsa (show_id, type, title, director, cast, country, date_added, release_year, rating, duration, listed_in, description, platform)
SELECT show_id, type, title, director, cast, country, date_added, release_year, rating, duration, listed_in, description, 'prime'
FROM filmsprime;

-- Optionnel : Supprimer les anciennes tables pour garder une base de données propre
DROP TABLE filmsnetflix, filmsdisney, filmsprime;



-- Table des utilisateurs
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user'
);

-- Table des avis
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    show_id VARCHAR(500),
    rating INT,
    review TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (show_id) REFERENCES filmsa(show_id)
);

-- Table des réponses aux avis
CREATE TABLE responses (
    response_id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT,
    user_id INT,
    response TEXT,
    response_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES reviews(review_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Table des likes sur les avis
CREATE TABLE like_reviews (
    like_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    review_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (review_id) REFERENCES reviews(review_id)
);

-- Table des films favoris
CREATE TABLE favori_films (
    fav_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    show_id VARCHAR(500),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (show_id) REFERENCES filmsa(show_id)
);

-- Table des films vus
CREATE TABLE films_vus (
    seen_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    show_id VARCHAR(500),
    watched_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (show_id) REFERENCES filmsa(show_id)
);

-- Table du top 3 des utilisateurs
CREATE TABLE top_3 (
    top_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    show_id VARCHAR(500),
    position ENUM('1', '2', '3') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (show_id) REFERENCES filmsa(show_id)
);

-- 🔧 Ajouter la colonne "platform" dans les tables qui contiennent un show_id

-- Table reviews
ALTER TABLE reviews
ADD platform ENUM('netflix', 'disney', 'prime') NOT NULL AFTER show_id;

ALTER TABLE reviews
DROP FOREIGN KEY reviews_ibfk_2;

ALTER TABLE reviews
ADD CONSTRAINT fk_reviews_filmsa
FOREIGN KEY (show_id, platform) REFERENCES filmsa(show_id, platform);

-- Table favori_films
ALTER TABLE favori_films
ADD platform ENUM('netflix', 'disney', 'prime') NOT NULL AFTER show_id;

ALTER TABLE favori_films
DROP FOREIGN KEY favori_films_ibfk_2;

ALTER TABLE favori_films
ADD CONSTRAINT fk_favori_filmsa
FOREIGN KEY (show_id, platform) REFERENCES filmsa(show_id, platform);

-- Table films_vus
ALTER TABLE films_vus
ADD platform ENUM('netflix', 'disney', 'prime') NOT NULL AFTER show_id;

ALTER TABLE films_vus
DROP FOREIGN KEY films_vus_ibfk_2;

ALTER TABLE films_vus
ADD CONSTRAINT fk_vus_filmsa
FOREIGN KEY (show_id, platform) REFERENCES filmsa(show_id, platform);

-- Table top_3
ALTER TABLE top_3
ADD platform ENUM('netflix', 'disney', 'prime') NOT NULL AFTER show_id;

ALTER TABLE top_3
DROP FOREIGN KEY top_3_ibfk_2;

ALTER TABLE top_3
ADD CONSTRAINT fk_top3_filmsa
FOREIGN KEY (show_id, platform) REFERENCES filmsa(show_id, platform);

SELECT show_id, platform, COUNT(*) AS nb
FROM filmsa
GROUP BY show_id, platform
HAVING nb > 1;

DESCRIBE filmsa;

DROP TABLE like_replies;
