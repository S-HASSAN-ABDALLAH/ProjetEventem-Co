-- Table reservations (clé étrangère vers evenements)

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