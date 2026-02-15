-- ========================================
-- FICHIER : init_database.sql
-- RÔLE : Initialiser TOUTE la base de données
-- USAGE : Exécuter ce fichier pour créer toutes les tables
-- ========================================

-- 1. Créer la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS evenement_db
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

-- 2. Utiliser cette base de données
USE evenement_db;

-- ========================================
-- ORDRE D'EXÉCUTION IMPORTANT :
-- 1. utilisateurs (pas de dépendances)
-- 2. contacts (pas de dépendances)
-- 3. evenements (pas de dépendances)
-- 4. reservations (dépend de evenements)
-- ========================================

-- ====== TABLE: utilisateurs ======
DROP TABLE IF EXISTS utilisateurs;
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Compte administrateur par défaut
-- Mot de passe : admin123 (haché en bcrypt)
INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
VALUES (
    'Administrateur',
    'admin@evenements-co.fr',
    '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFfEm0sLCyKsYJdRWzqUCm6U0QUgMvz2',
    'admin'
);

-- ====== TABLE: contacts ======
DROP TABLE IF EXISTS contacts;
CREATE TABLE contacts (
    id INT NOT NULL AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    tel VARCHAR(20) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ====== TABLE: evenements ======
DROP TABLE IF EXISTS evenements;
CREATE TABLE evenements (
    id INT NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    prix_base DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ====== TABLE: reservations ======
-- IMPORTANT : doit être créée APRÈS evenements (clé étrangère)
DROP TABLE IF EXISTS reservations;
CREATE TABLE reservations (
    id INT NOT NULL AUTO_INCREMENT,
    evenement_id INT NOT NULL,
    nom_client VARCHAR(255) NOT NULL,
    email_client VARCHAR(255) NOT NULL,
    tel_client VARCHAR(20) DEFAULT NULL,
    date_evenement DATE NOT NULL,
    ville ENUM('Grenoble', 'Lyon', 'Annecy') NOT NULL,
    nombre_invites INT DEFAULT 50,
    statut ENUM('en_attente', 'confirme', 'annule') DEFAULT 'en_attente',
    message_client TEXT,
    prix_total DECIMAL(10,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY evenement_id (evenement_id),
    CONSTRAINT reservations_ibfk_1 FOREIGN KEY (evenement_id) REFERENCES evenements (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- FIN DE L'INITIALISATION
-- ========================================
