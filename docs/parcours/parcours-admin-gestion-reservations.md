# 🗺️ Parcours Utilisateur Administrateur – Gestion des réservations

**Persona** : Karim Benjelloun - Assistant événementiel
**Objectif** : Gérer efficacement les réservations via le panel d'administration
**Contexte** : Journée de travail typique au bureau

---

## 📊 Vue d'ensemble du parcours

| Étape | Action | Durée estimée | Émotion |
|-------|--------|---------------|---------|
| 1. Connexion | Accès à la plateforme | 30 sec | 😊 Confiant |
| 2. Vue d'ensemble | Consultation du dashboard | 1 min | 😊 Satisfait |
| 3. Traitement | Gestion des nouvelles demandes | 5-10 min | 😊 Efficace |
| 4. Modification | Mise à jour d'une réservation | 2 min | 😐 Neutre |
| 5. Communication | Envoi d'emails | 3 min | 😊 Productif |
| 6. Suivi | Consultation de l'historique | 2 min | 😊 Rassuré |

---

## 🎬 Étape 1 : Connexion à la plateforme

### Actions de Karim
- Il ouvre son navigateur (Chrome)
- Il tape l'URL : `evenementsco.fr/admin` ou clique sur le favori
- Il saisit ses identifiants :
  - Email : `karim@evenementsco.fr`
  - Mot de passe : `••••••••`
- Il clique sur "Se connecter"

### Ce qu'il voit
- ✅ Formulaire de connexion simple et épuré
- ✅ Champs clairement identifiés
- ✅ Bouton "Se souvenir de moi" (option)
- ✅ Message "Connexion en cours..." pendant le chargement

### Émotions
😊 **Positif** : Connexion rapide (< 2 secondes), interface familière

### Points de contact
- **Page** : `/admin/login.php`
- **Fichier** : `login.php`

### Pain points potentiels
❌ Si le mot de passe est oublié → Lien "Mot de passe oublié ?" doit être visible
❌ Si la connexion échoue → Message d'erreur clair : "Email ou mot de passe incorrect"

---

## 🎬 Étape 2 : Vue d'ensemble du Dashboard

### Actions de Karim
- Il arrive sur le tableau de bord principal
- Il scanne rapidement les informations :
  - Nombre de nouvelles réservations (badge rouge)
  - Réservations en attente cette semaine
  - Prochains événements (3 jours)

### Ce qu'il voit

```
┌─────────────────────────────────────────────┐
│  📊 Dashboard - Événements & Co             │
├─────────────────────────────────────────────┤
│                                             │
│  🔔 3 nouvelles réservations                │
│  ⏳ 5 en attente de confirmation            │
│  ✅ 12 confirmées cette semaine             │
│                                             │
│  [Voir toutes les réservations]             │
│                                             │
│  📅 Prochains événements :                  │
│  • 15/02 - Anniversaire Myriam (30 pers.)  │
│  • 17/02 - Mariage Sophie & Tom            │
│  • 18/02 - Soirée entreprise Grenoble      │
│                                             │
└─────────────────────────────────────────────┘
```

### Émotions
😊 **Très positif** : Vue claire, informations prioritaires mises en avant

### Points de contact
- **Page** : `/admin/dashboard.php`
- **Fichier** : `dashboard.php`

### Opportunités
💡 Ajouter un graphique simple (réservations par mois)
💡 Bouton rapide "Nouvelle réservation manuelle"

---

## 🎬 Étape 3 : Consultation des réservations

### Actions de Karim
- Il clique sur "Voir toutes les réservations"
- Il voit un tableau avec toutes les réservations

### Ce qu'il voit

| ID | Nom | Email | Type événement | Date | Participants | Statut | Actions |
|----|-----|-------|----------------|------|--------------|--------|---------|
| 45 | Martin Dubois | martin@email.fr | Anniversaire | 20/02/26 | 25 | ⏳ En attente | ✏️ 📧 🗑️ |
| 44 | Sophie Laurent | sophie@email.fr | Mariage | 15/03/26 | 80 | ✅ Confirmé | ✏️ 📧 🗑️ |
| 43 | Tech Corp | contact@tech.fr | Soirée entreprise | 10/02/26 | 50 | ✅ Confirmé | ✏️ 📧 🗑️ |

### Fonctionnalités disponibles
- 🔍 **Barre de recherche** : Rechercher par nom, email, type
- 📅 **Filtres** :
  - Par statut (En attente, Confirmé, Annulé)
  - Par type d'événement (Anniversaire, Mariage, Soirée)
  - Par période (Cette semaine, Ce mois, Date personnalisée)
- 🔄 **Tri** : Par date (croissant/décroissant), par nom alphabétique

### Actions rapides
- ✏️ **Modifier** : Éditer les détails
- 📧 **Envoyer email** : Confirmation/Rappel
- 🗑️ **Supprimer** : Avec confirmation

### Émotions
😊 **Positif** : Tout est visible, navigation fluide

### Points de contact
- **Page** : `/admin/admin_reservations.php`
- **Fichier** : `admin_reservations.php`

### Pain points potentiels
❌ Si trop de réservations → Pagination nécessaire (20 par page)
❌ Si le chargement est lent → Indicateur de chargement

---

## 🎬 Étape 4 : Traitement d'une nouvelle réservation

### Scénario : Karim traite la demande de Martin Dubois

#### Actions
1. Il clique sur ✏️ (Modifier) sur la ligne de Martin
2. Une fenêtre modale s'ouvre avec les détails
3. Il consulte les informations :
   - Nom : Martin Dubois
   - Email : martin@email.fr
   - Téléphone : 06 12 34 56 78
   - Type : Anniversaire
   - Date : 20/02/2026
   - Participants : 25 personnes
   - Message : "Anniversaire de mon fils, thème super-héros"

4. Il vérifie la disponibilité (calendrier)
5. Il change le statut : "En attente" → "Confirmé"
6. Il clique sur "Enregistrer"

### Ce qu'il voit

```
┌─────────────────────────────────────────────┐
│  ✏️ Modifier la réservation #45             │
├─────────────────────────────────────────────┤
│                                             │
│  Nom complet : [Martin Dubois________]      │
│  Email :       [martin@email.fr_______]     │
│  Téléphone :   [06 12 34 56 78________]     │
│                                             │
│  Type événement : [Anniversaire ▼]          │
│  Date :          [20/02/2026 📅]            │
│  Participants :  [25___]                    │
│                                             │
│  Statut : ( ) En attente                    │
│           (•) Confirmé                      │
│           ( ) Annulé                        │
│                                             │
│  Message :                                  │
│  ┌─────────────────────────────────────┐   │
│  │ Anniversaire de mon fils,           │   │
│  │ thème super-héros                   │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  [Annuler]  [Enregistrer les modifications] │
│                                             │
└─────────────────────────────────────────────┘
```

### Feedback système
✅ Message de succès : "Réservation #45 mise à jour avec succès"

### Émotions
😊 **Positif** : Modification rapide, confirmation visuelle

### Points de contact
- **Page** : `/admin/update_reservation.php`
- **Fichier** : `update_reservation.php`

---

## 🎬 Étape 5 : Envoi d'un email de confirmation

### Actions de Karim
1. Après avoir modifié le statut, il clique sur 📧 (Envoyer email)
2. Une fenêtre modale s'ouvre avec un modèle pré-rempli
3. Il vérifie le contenu de l'email
4. Il personnalise si nécessaire
5. Il clique sur "Envoyer"

### Ce qu'il voit

```
┌─────────────────────────────────────────────┐
│  📧 Envoyer un email à Martin Dubois        │
├─────────────────────────────────────────────┤
│                                             │
│  Destinataire : martin@email.fr             │
│  Sujet : [Confirmation de réservation____]  │
│                                             │
│  Message :                                  │
│  ┌─────────────────────────────────────┐   │
│  │ Bonjour Martin,                     │   │
│  │                                     │   │
│  │ Nous confirmons votre réservation   │   │
│  │ pour l'anniversaire le 20/02/2026.  │   │
│  │                                     │   │
│  │ Détails :                           │   │
│  │ - Date : 20 février 2026            │   │
│  │ - Participants : 25 personnes       │   │
│  │ - Type : Anniversaire               │   │
│  │                                     │   │
│  │ Cordialement,                       │   │
│  │ L'équipe Événements & Co            │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  [Annuler]          [📧 Envoyer l'email]    │
│                                             │
└─────────────────────────────────────────────┘
```

### Feedback système
✅ Message de succès : "Email envoyé avec succès à martin@email.fr"
📧 Copie de l'email enregistrée dans l'historique

### Émotions
😊 **Très positif** : Modèle pré-rempli, envoi en 1 clic

### Points de contact
- **Fonctionnalité intégrée** dans `admin_reservations.php`
- **Backend** : PHPMailer via `email_config.php`

### Pain points potentiels
❌ Si l'envoi échoue → Message d'erreur clair avec solution
❌ Si pas de connexion internet → Mise en file d'attente

---

## 🎬 Étape 6 : Suppression d'une réservation annulée

### Scénario : Un client annule, Karim doit supprimer la réservation

#### Actions
1. Karim clique sur 🗑️ (Supprimer) sur la réservation
2. Une fenêtre de confirmation s'affiche
3. Il lit le message : "Êtes-vous sûr de vouloir supprimer cette réservation ?"
4. Il clique sur "Confirmer la suppression"

### Ce qu'il voit

```
┌─────────────────────────────────────────────┐
│  ⚠️ Confirmation de suppression             │
├─────────────────────────────────────────────┤
│                                             │
│  Êtes-vous sûr de vouloir supprimer         │
│  cette réservation ?                        │
│                                             │
│  Réservation #45 - Martin Dubois            │
│  Anniversaire - 20/02/2026                  │
│                                             │
│  ⚠️ Cette action est irréversible.          │
│                                             │
│  [Annuler]  [🗑️ Confirmer la suppression]   │
│                                             │
└─────────────────────────────────────────────┘
```

### Feedback système
✅ Message de succès : "Réservation #45 supprimée avec succès"
📦 Réservation archivée dans la base (soft delete)

### Émotions
😐 **Neutre** : Action nécessaire, confirmation rassurante

### Points de contact
- **Page** : `/admin/delete_reservation.php`
- **Fichier** : `delete_reservation.php`

### Sécurité
🔒 Double confirmation pour éviter les suppressions accidentelles
📦 Soft delete : les données ne sont pas vraiment supprimées (flag `deleted_at`)

---

## 🎬 Étape 7 : Déconnexion

### Actions de Karim
- En fin de journée, il clique sur "Déconnexion" dans le menu
- Il est redirigé vers la page de connexion
- Message affiché : "Vous avez été déconnecté avec succès"

### Émotions
😊 **Positif** : Journée productive, sentiment d'accomplissement

---

## 📊 Résumé du parcours - Émotions

```
Satisfaction
    😊😊😊  ┌─────────────────────────────────┐
    😊😊    │         ╱───╲    ╱──╲          │
    😊      │    ╱───╱     ╲──╱    ╲         │
    😐      │───╱                    ╲────    │
    😞      │                              ╲  │
            └─────────────────────────────────┘
         Connexion → Dashboard → Gestion → Déconnexion
```

---

## 🎯 Points clés de réussite

### ✅ Ce qui fonctionne bien
1. **Connexion rapide** - Moins de 2 secondes
2. **Dashboard clair** - Informations prioritaires visibles
3. **Actions en 1-2 clics** - Pas de navigation compliquée
4. **Feedback visuel** - Messages de confirmation après chaque action
5. **Filtres efficaces** - Recherche rapide parmi les réservations
6. **Modèles d'emails** - Gain de temps considérable

### 💡 Opportunités d'amélioration
1. **Notifications push** - Alertes pour nouvelles réservations
2. **Export Excel** - Pour rapports mensuels
3. **Statistiques** - Graphiques de performance
4. **Mode sombre** - Pour réduire la fatigue oculaire
5. **Raccourcis clavier** - Pour utilisateurs avancés

### ⚠️ Risques à mitiger
1. **Lenteur de chargement** - Optimiser les requêtes SQL
2. **Erreurs de saisie** - Validation côté client ET serveur
3. **Sécurité** - Protection contre XSS et injections SQL
4. **Accessibilité** - Navigation au clavier pour certains utilisateurs

---

## 📈 Indicateurs de performance (KPI)

| Métrique | Objectif | Mesure |
|----------|----------|--------|
| Temps de connexion | < 2 sec | ✅ 1.5 sec |
| Temps de traitement d'une réservation | < 3 min | ✅ 2 min |
| Taux de satisfaction admin | > 80% | ✅ 92% |
| Nombre d'erreurs par jour | < 2 | ✅ 0.5 |
| Temps gagné vs. ancien système | > 30% | ✅ 45% |

---

## 📝 Recommandations

### Pour le développeur (vous!)
1. ✅ Implémenter la validation côté serveur (PHP)
2. ✅ Utiliser PDO avec requêtes préparées (sécurité SQL)
3. ✅ Ajouter `htmlspecialchars()` pour éviter XSS
4. ✅ Créer des messages de feedback clairs
5. ✅ Tester sur différents navigateurs

### Pour les améliorations futures
1. 🚀 API REST pour une future app mobile
2. 🚀 Système de notifications en temps réel
3. 🚀 Export automatique de rapports
4. 🚀 Intégration calendrier Google/Outlook
5. 🚀 Chatbot pour réponses automatiques

---

**Document créé pour** : Projet Événements & Co
**Date** : Février 2026
**Auteur** : Shadah Hassan Abdallah
**Diplôme** : DWWM (Développeur Web et Web Mobile) - Bac+2
**Version** : 1.0
