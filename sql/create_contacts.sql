-- ========================================
-- FICHIER : create_contacts.sql
-- RÔLE : Créer la table contacts
-- ========================================

-- 1. Supprimer la table si elle existe déjà (pour repartir de zéro)
DROP TABLE IF EXISTS contacts;

-- 2. Créer la table contacts
CREATE TABLE contacts (
    id INT NOT NULL AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    tel VARCHAR(20) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
