# README - Projet Événements & Co

> Site web dynamique pour une agence d'organisation d'événements (mariage, anniversaire, soirée)

## 🌟 Présentation du projet

**Nom du projet** : Événements & Co
**Description** : Application web complète permettant de présenter les services d'une agence d'événements et de gérer les réservations avec un système backend dynamique.
**Objectif** : Offrir une plateforme interactive pour découvrir les services, réserver des événements et gérer les demandes via une interface d'administration.
**Public cible** : Particuliers cherchant à organiser un événement privé (familles, couples, jeunes adultes).

---

## 🚀 Démonstration en ligne

Ce projet est hébergé en ligne 👉 [Cliquez ici pour voir le site](https://evenement-co.netlify.app)

---

## 🔹 Structure du projet

### Pages principales

- **index.html** - Page d'accueil avec présentation des services
- **programme.html** - Détails des différents types d'événements
- **contact.html** - Formulaire de contact dynamique avec envoi d'email
- **reservation.php** - Système de réservation avec base de données

### Fonctionnalités

#### Frontend
- Navigation fluide et responsive (mobile, tablette, desktop)
- Ancrages internes vers les sections
- Design moderne avec Bootstrap 5
- Formulaires interactifs avec validation

#### Backend
- **Système de réservation** : Enregistrement des demandes en base de données
- **Formulaire de contact** : Envoi d'emails automatiques via PHPMailer et stockage des messages
- **Panel d'administration** : Gestion complète des réservations (CRUD)
- **Authentification** : Système de login sécurisé pour l'accès admin
- **Notifications email** : Confirmations automatiques pour les clients et notifications admin

---

## 🛠️ Technologies utilisées

### Frontend
- **HTML5** - Structure sémantique
- **CSS3** - Styles personnalisés
- **Bootstrap 5** - Framework responsive
- **JavaScript** - Interactivité

### Backend
- **PHP 8.x** - Logique serveur
- **MySQL/MariaDB** - Base de données relationnelle
- **PHPMailer** - Envoi d'emails SMTP
- **Composer** - Gestion des dépendances

### Outils de développement
- **WAMP/XAMPP** - Serveur local
- **Git & GitHub** - Contrôle de version
- **Figma** - Maquettes haute-fidélité
- **Balsamiq** - Wireframes basse-fidélité

---

## 🌈 Design et maquettes

Le site a été conçu avec une attention particulière portée à l'esthétique et à l'expérience utilisateur.

**🎨 Thème & inspiration** : Couleurs douces et élégantes, ambiance festive et chaleureuse.
**🔠 Police utilisée** : Poppins, intégrée via Google Fonts.

### 🎨 Palette de couleurs

| Utilisation | Couleur (Hex) | Description |
|-------------|---------------|-------------|
| Arrière-plan général | `#FFF5F7` | Rose très clair (doux et lumineux) |
| Titres principaux (h2) | `#C2185B` | Rose fuchsia foncé (pour attirer l'œil) |
| Texte principal | `#333333` | Gris foncé (lecture confortable) |
| Liens actifs / boutons interactifs | `#f06428` | Orange vif (accents dynamiques) |
| Boutons secondaires clairs | `#FFE3EC` | Rose pâle (boutons secondaires) |
| Sections avec fond rose | `#f7cfd0` | Rose pastel (fond de section) |
| Pied de page (copyright) | `#dc868e` | Rose moyen (fond du footer) |
| Focus accessibilité (clavier) | `#FF9900` | Orange contrasté (contour focus) |

### 🧩 Maquettes

**Figma** : Maquettes haute-fidélité représentant la version visuelle finale
**Balsamiq** : Wireframes basse-fidélité pour l'organisation des pages

---

## 📖 Installation et configuration

### Prérequis

- **Serveur local** : WAMP, XAMPP, MAMP ou équivalent
- **PHP** : Version 8.0 ou supérieure
- **MySQL/MariaDB** : Base de données
- **Composer** : Gestionnaire de dépendances PHP

### Installation

#### 1. Cloner le projet

```bash
git clone https://github.com/S-HASSAN-ABDALLAH/ProjetEventem-Co.git
cd ProjetEventem-Co
```

#### 2. Installer les dépendances

```bash
composer install
```

#### 3. Configuration de la base de données

```bash
# Créer la base de données et importer le schéma complet
mysql -u root -p < sql/init_database.sql
```

#### 4. Configuration des fichiers

**Base de données** :
```bash
cp includes/db.example.php includes/db.php
# Éditer includes/db.php avec vos identifiants MySQL
```

**Email SMTP** :
```bash
cp includes/email_config.example.php includes/email_config.php
# Éditer includes/email_config.php avec vos identifiants SMTP
```

Ou créer un fichier `.env` à la racine :
```ini
; Configuration SMTP pour PHPMailer
SMTP_USERNAME=votre-email@gmail.com
SMTP_PASSWORD=votre-mot-de-passe-application

; NE JAMAIS COMMITER CE FICHIER DANS GIT
; Ce fichier contient des informations sensibles
```

#### 5. Créer un compte administrateur

Importer le fichier SQL pour créer le compte admin :

**Note:** Le fichier `init_database.sql` crée déjà le compte admin automatiquement.

**Identifiants admin par défaut** (pour test/démonstration) :
```
Email    : [Défini dans sql/init_database.sql]
Password : [Défini dans sql/init_database.sql]
```

⚠️ **IMPORTANT** :
- Consultez le fichier `sql/init_database.sql` pour les identifiants de test
- Changez IMPÉRATIVEMENT ces identifiants avant toute mise en production
- Ces identifiants ne doivent être utilisés que pour le développement local

#### 6. Lancer l'application

Accédez à `http://localhost/ProjetEventem-Co/` dans votre navigateur.

### Accès admin

- **URL** : `http://localhost/ProjetEventem-Co/admin/login.php`

---

## 🔐 Sécurité

### Fichiers sensibles protégés

Les fichiers suivants sont dans `.gitignore` et **ne doivent JAMAIS** être committés :

- `.env` - Identifiants SMTP
- `includes/db.php` - Identifiants base de données
- `includes/email_config.php` - Configuration email

### Bonnes pratiques appliquées

- ✅ Mots de passe hashés avec `password_hash()`
- ✅ Requêtes préparées (PDO) contre les injections SQL
- ✅ Validation et sanitization des entrées utilisateur
- ✅ Protection CSRF sur les formulaires
- ✅ Fichiers de configuration exclus du versioning

---

## 📊 Performance et accessibilité

### Outils de vérification utilisés

- [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/) - Vérification du contraste
- [Toptal CSS Minifier](https://www.toptal.com/developers/cssminifier) - Minification CSS
- **Lighthouse** - Audit de performance, accessibilité et SEO

### Optimisations appliquées

- ✅ Images au format WebP (compression optimale)
- ✅ CSS minifié pour réduire le temps de chargement
- ✅ Contrastes de couleurs conformes WCAG 2.1
- ✅ Navigation accessible au clavier
- ✅ Labels et attributs ARIA pour les lecteurs d'écran

---

## 🗂️ Structure des dossiers

```
ProjetEventem-Co/
├── admin/              # Panel d'administration
│   ├── dashboard.php
│   ├── login.php
│   └── admin_reservations.php
├── assets/             # Ressources statiques
│   ├── css/
│   ├── js/
│   └── images/
├── docs/               # Documentation et maquettes
├── includes/           # Fichiers de configuration
│   ├── db.example.php
│   └── email_config.example.php
├── sql/                # Scripts de base de données
├── vendor/             # Dépendances Composer
├── index.html          # Page d'accueil
├── programme.html      # Page programme
├── contact.html        # Formulaire de contact
├── contact.php         # Traitement contact
├── reservation.php     # Système de réservation
├── composer.json       # Dépendances PHP
└── .gitignore          # Fichiers exclus de Git
```

---

## 🎯 Fonctionnalités principales

### 1. Système de réservation
- Formulaire de réservation dynamique
- Validation côté client et serveur
- Enregistrement en base de données
- Email de confirmation automatique

### 2. Formulaire de contact
- Envoi d'emails via SMTP (Gmail)
- Stockage des messages en base de données
- Notifications en temps réel

### 3. Panel d'administration
- Authentification sécurisée
- Liste des réservations avec filtres
- Actions CRUD (Create, Read, Update, Delete)
- Envoi d'emails depuis l'interface admin

### 4. Design responsive
- Compatible mobile, tablette et desktop
- Navigation adaptative
- Images optimisées par appareil

---

## 🌟 Auteur et crédits

**Auteur** : Shadah Hassan Abdallah
**Email** : shadah.hassan.abdallah@gmail.com
**GitHub** : [S-HASSAN-ABDALLAH](https://github.com/S-HASSAN-ABDALLAH)

### Crédits externes
- Images optimisées en format WebP
- Icônes via Bootstrap Icons
- Polices via Google Fonts - Poppins
- PHPMailer pour l'envoi d'emails

---

## 📝 Licence

Ce projet est un projet éducatif réalisé dans le cadre d'une formation en développement web.

---

## 📞 Contact

Pour toute question ou suggestion concernant ce projet :

- 📧 Email : shadah.hassan.abdallah@gmail.com
- 💼 GitHub : [@S-HASSAN-ABDALLAH](https://github.com/S-HASSAN-ABDALLAH)

---

**Développé avec ❤️ par Shadah**
