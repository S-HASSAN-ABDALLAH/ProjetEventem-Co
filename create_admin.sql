-- ========================================
-- FICHIER : create_admin.sql
-- RÔLE : Créer la table utilisateurs et le compte admin
-- ========================================

-- 1. Supprimer la table si elle existe déjà (pour repartir de zéro)
DROP TABLE IF EXISTS utilisateurs;

-- 2. Créer la table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Insérer le compte administrateur
-- Mot de passe : admin123 (haché en bcrypt)
INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
VALUES (
    'Administrateur',
    'admin@evenements-co.fr',
    '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFfEm0sLCyKsYJdRWzqUCm6U0QUgMvz2',
    'admin'
);
