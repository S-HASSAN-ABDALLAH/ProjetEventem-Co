# 📧 Système de Contact - Événement & Co

## 📌 Description

Système de formulaire de contact complet avec validation double (client-side et server-side), sauvegarde en base de données MySQL, et envoi d'emails automatiques via PHPMailer.

**Projet réalisé dans le cadre du diplôme DWWM (Développeur Web et Web Mobile) Bac+2**

---

## 🚀 Fonctionnalités

✅ **Validation en temps réel** (JavaScript)
- Vérification instantanée des champs (nom, email, message)
- Feedback visuel avec bordures vertes/rouges (Bootstrap)
- Compteur de caractères pour le message (limite 500)

✅ **Validation côté serveur** (PHP)
- Protection contre les injections XSS (`htmlspecialchars()`)
- Protection contre les injections SQL (PDO avec requêtes préparées)
- Validation du format email (`filter_var()`)

✅ **Sauvegarde en base de données**
- Enregistrement sécurisé dans MySQL
- Table `contacts` avec tous les champs nécessaires

✅ **Envoi d'emails automatiques**
- Email de notification pour l'administrateur
- Email de confirmation pour l'utilisateur
- Utilisation de PHPMailer via SMTP Gmail

✅ **Expérience utilisateur optimisée**
- Redirection avec messages Bootstrap
- Prévention du double-clic sur le bouton d'envoi
- Messages d'erreur clairs et informatifs

---

## 🛠️ Technologies utilisées

### **Frontend**
- HTML5
- CSS3 / Bootstrap 5.3
- JavaScript (ES6+)

### **Backend**
- PHP 8.x
- MySQL / MariaDB
- Composer (gestionnaire de dépendances)

### **Bibliothèques**
- PHPMailer 6.x (envoi d'emails SMTP)

---

## 📋 Prérequis

- **WAMP / XAMPP / MAMP** (ou tout serveur local avec PHP 7.4+ et MySQL)
- **Composer** installé ([télécharger ici](https://getcomposer.org/download/))
- **Compte Gmail** avec App Password activé

---

## ⚙️ Installation

### **1. Cloner le projet**

```bash
git clone https://github.com/VOTRE-USERNAME/ProjetEventem-Co.git
cd ProjetEventem-Co
```

### **2. Installer les dépendances PHP**

```bash
composer install
```

### **3. Créer la base de données**

Ouvrez **phpMyAdmin** et créez une base de données :

```sql
CREATE DATABASE evenement_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Puis créez la table `contacts` :

```sql
USE evenement_db;

CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    tel VARCHAR(20),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### **4. Configurer la connexion à la base de données**

Copiez le fichier d'exemple :

```bash
cp db.example.php db.php
```

Modifiez `db.php` avec vos informations :

```php
$host = 'localhost';
$dbname = 'evenement_db';
$username = 'root';
$password = ''; // Votre mot de passe MySQL
```

### **5. Configurer l'envoi d'emails**

#### **a) Créer un App Password Gmail**

1. Allez sur https://myaccount.google.com/apppasswords
2. Connectez-vous avec votre compte Gmail
3. Créez un nouveau mot de passe d'application
4. Copiez le code de 16 caractères

#### **b) Configurer le fichier email**

Copiez le fichier d'exemple :

```bash
cp email_config.example.php email_config.php
```

Modifiez `email_config.php` :

```php
define('SMTP_USERNAME', 'votre.email@gmail.com');
define('SMTP_PASSWORD', 'votre_app_password_16_caracteres');
define('ADMIN_EMAIL', 'votre.email@gmail.com');
```

---

## 🧪 Utilisation

1. Démarrez votre serveur local (WAMP/XAMPP)
2. Ouvrez votre navigateur : `http://localhost/ProjetEventem-Co/contact.html`
3. Remplissez le formulaire
4. Cliquez sur "Envoyer"
5. Vérifiez :
   - Le message de succès s'affiche
   - Les données sont dans phpMyAdmin → `evenement_db` → `contacts`
   - Vous avez reçu 2 emails (notification + confirmation)

---

## 📁 Structure du projet

```
ProjetEventem-Co/
├── contact.html           # Formulaire de contact (Frontend)
├── contact.php            # Traitement du formulaire (Backend)
├── contact.js             # Validation JavaScript côté client
├── db.php                 # Connexion à la base de données (⚠️ Ne pas commit)
├── db.example.php         # Exemple de configuration BDD
├── email_config.php       # Configuration SMTP (⚠️ Ne pas commit)
├── email_config.example.php # Exemple de configuration email
├── composer.json          # Dépendances PHP
├── composer.lock          # Versions exactes des dépendances
├── vendor/                # Dossier des bibliothèques (généré par Composer)
├── .gitignore             # Fichiers à ignorer sur Git
└── README_CONTACT.md      # Ce fichier
```

---

## 🔒 Sécurité

### **Mesures implémentées**

✅ **Protection XSS** : `htmlspecialchars()` sur toutes les entrées utilisateur
✅ **Protection SQL Injection** : PDO avec requêtes préparées et paramètres nommés
✅ **Validation double** : Client-side (JavaScript) + Server-side (PHP)
✅ **Gestion des erreurs** : try/catch avec messages utilisateur clairs
✅ **Fichiers sensibles** : `.gitignore` empêche le commit des mots de passe

### **⚠️ IMPORTANT**

**Ne committez JAMAIS ces fichiers** :
- `db.php` (contient le mot de passe MySQL)
- `email_config.php` (contient le mot de passe Gmail)
- `vendor/` (dossier régénérable avec `composer install`)

---

## 🎓 Compétences démontrées (DWWM Bac+2)

### **Frontend**
- HTML5 sémantique et formulaires
- CSS3 / Framework Bootstrap
- JavaScript ES6+ (DOM, événements, validation)
- Responsive design

### **Backend**
- PHP orienté objet
- Requêtes SQL sécurisées (PDO)
- Gestion de sessions et redirections
- Composer (gestion de dépendances)

### **Sécurité**
- Validation des entrées utilisateur
- Protection contre XSS et SQL Injection
- Gestion des erreurs et exceptions

### **Architecture**
- Séparation des responsabilités (MVC partiel)
- Pattern PRG (Post-Redirect-Get)
- Configuration externalisée

---

## 📧 Contact

**Projet réalisé par** : [Votre Nom]
**Email** : [Votre email]
**GitHub** : [Votre profil GitHub]
**Diplôme** : DWWM (Développeur Web et Web Mobile) - Bac+2

---

## 📄 Licence

Ce projet est un projet pédagogique réalisé dans le cadre du diplôme DWWM.

---

## 🙏 Remerciements

- Bootstrap pour le framework CSS
- PHPMailer pour la bibliothèque d'envoi d'emails
- La communauté PHP pour la documentation

---

**Projet terminé le** : [Date]
**Version** : 1.0.0
