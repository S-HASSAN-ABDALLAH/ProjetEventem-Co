# Admin Panel - Événement & Co

## Accès
Pour accéder au panel admin, visitez:
```
http://localhost/ProjetEventem&Co/admin/login.php
```

## 🔐 Identifiants de test (pour le jury)

**IMPORTANT** : Ces identifiants sont uniquement pour la démonstration du projet académique.

```
Email    : admin@evenements-co.fr
Password : admin123
```

## Fichiers
- `login.php` - Page de connexion admin
- `dashboard.php` - Tableau de bord admin
- `admin_reservations.php` - Gestion des réservations
- `update_reservation.php` - Mise à jour statut réservation
- `delete_reservation.php` - Suppression réservation
- `logout.php` - Déconnexion

## Fonctionnalités

### 📊 Dashboard
- Vue d'ensemble des statistiques
- Nombre total de réservations
- Répartition par statut (en attente, confirmé, annulé)
- Graphiques de visualisation

### 📋 Gestion des réservations
- Liste complète des réservations
- Filtres par statut et date
- Actions : Confirmer, Annuler, Supprimer
- Envoi automatique d'emails au client

## Sécurité
- ✅ Authentification requise pour tous les fichiers
- ✅ Sessions PHP sécurisées
- ✅ Mots de passe hashés avec bcrypt
- ✅ Protection CSRF
- ✅ Requêtes préparées PDO (SQL injection protection)
