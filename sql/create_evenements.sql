-- ========================================
-- FICHIER : create_evenements.sql
-- RÔLE : Créer la table evenements
-- ========================================

-- 1. Supprimer la table si elle existe déjà (pour repartir de zéro)
DROP TABLE IF EXISTS evenements;

-- 2. Créer la table evenements
CREATE TABLE evenements (
    id INT NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    prix_base DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Insérer des événements exemples (optionnel)
-- Décommenter les lignes ci-dessous si vous voulez des données de test
/*
INSERT INTO evenements (nom, description, prix_base, image) VALUES
('Mariage Premium', 'Organisation complète de votre mariage de rêve', 5000.00, 'mariage.jpg'),
('Anniversaire', 'Fête d\'anniversaire personnalisée', 1500.00, 'anniversaire.jpg'),
('Séminaire Entreprise', 'Organisation de séminaires professionnels', 3000.00, 'seminaire.jpg');
*/
