# Configuration de la base de données

## Installation

Pour configurer la connexion à la base de données :

1. **Copiez le fichier exemple** :
   ```bash
   cp db.example.php db.php
   ```

2. **Modifiez le fichier `db.php`** avec vos paramètres de connexion :
   - `$host` : Adresse du serveur MySQL (généralement `localhost`)
   - `$dbname` : Nom de votre base de données
   - `$username` : Votre nom d'utilisateur MySQL
   - `$password` : Votre mot de passe MySQL

3. **Le fichier `db.php` est ignoré par Git** pour des raisons de sécurité (voir `.gitignore`)

## Structure de la base de données

Importez le fichier SQL situé dans `sql/database.sql` pour créer automatiquement :
- Table `evenements`
- Table `reservations`
- Table `contacts`
- Table `admins`

## Sécurité

- ❌ **NE JAMAIS** committer le fichier `db.php` dans Git
- ✅ Le fichier `db.example.php` peut être partagé (sans credentials)
- ✅ Les credentials sont dans `.gitignore`

## Configuration email

Suivez la même procédure pour `email_config.php` :
1. Créez votre fichier `.env` à la racine du projet
2. Ajoutez vos identifiants SMTP Gmail
3. Le fichier est automatiquement chargé par `email_config.php`
