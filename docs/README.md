# 📚 Documentation - Événements & Co

Ce dossier contient toute la documentation du projet **Événements & Co**.

---

## 📂 Structure du dossier

```
docs/
├── README.md                          # Ce fichier
├── personas/                          # Personas des utilisateurs
│   ├── persona-client-sarah.pdf       # Persona du client type
│   └── persona-administrateur-karim.md # Persona de l'administrateur
├── parcours/                          # Parcours utilisateurs (User Journey)
│   ├── Parcours utilisateur structuré.docx  # Parcours client (réservation)
│   └── parcours-admin-gestion-reservations.md # Parcours admin
└── maquettes/                         # Maquettes et wireframes
    └── (fichiers de conception graphique)
```

---

## 👥 Personas

Les personas représentent les profils types des utilisateurs du site.

### 1. Persona Client - Sarah Lemaire
**Fichier** : [`personas/👩_💻 Persona Client Sarah Lemaire.pdf`](personas/👩_💻%20Persona%20Client%20%20Sarah%20Lemaire.pdf)

- **Profil** : Mère de famille cherchant à organiser un anniversaire
- **Âge** : 35 ans
- **Objectif** : Trouver une solution clé en main pour l'anniversaire de sa fille
- **Format** : PDF

### 2. Persona Administrateur - Karim Benjelloun
**Fichier** : [`personas/persona-administrateur-karim.md`](personas/persona-administrateur-karim.md)

- **Profil** : Assistant événementiel chez Événements & Co
- **Âge** : 28 ans
- **Objectif** : Gérer efficacement les réservations via le panel admin
- **Format** : Markdown

---

## 🗺️ Parcours utilisateurs (User Journey)

Les parcours décrivent les étapes que suivent les utilisateurs pour accomplir leurs objectifs.

### 1. Parcours Client - Réservation
**Fichier** : [`parcours/Parcours utilisateur structuré.docx`](parcours/Parcours%20utilisateur%20structuré.docx)

**Étapes principales** :
1. Découverte du site
2. Exploration des services
3. Prise de contact
4. Organisation de l'événement
5. Suivi et préparation
6. Jour J
7. Après l'événement

**Format** : Word (.docx)

### 2. Parcours Admin - Gestion des réservations
**Fichier** : [`parcours/parcours-admin-gestion-reservations.md`](parcours/parcours-admin-gestion-reservations.md)

**Étapes principales** :
1. Connexion à la plateforme
2. Vue d'ensemble du dashboard
3. Consultation des réservations
4. Traitement d'une nouvelle demande
5. Envoi d'emails de confirmation
6. Suppression d'une réservation annulée
7. Déconnexion

**Format** : Markdown (peut être converti en PDF)

---

## 🎨 Maquettes

Le dossier `maquettes/` contient les wireframes et maquettes graphiques du site :
- Wireframes Balsamiq (basse fidélité)
- Maquettes Figma (haute fidélité)
- Captures d'écran des différentes pages

---

## 📝 Comment utiliser cette documentation

### Pour les développeurs
- Consultez les **personas** pour comprendre les besoins des utilisateurs
- Suivez les **parcours utilisateurs** pour identifier les fonctionnalités critiques
- Référez-vous aux **maquettes** pour l'implémentation visuelle

### Pour les formateurs/évaluateurs (DWWM)
- Les personas démontrent la **réflexion UX/UI**
- Les parcours montrent la **compréhension des besoins utilisateurs**
- Les maquettes illustrent la **conception ergonomique**

### Pour convertir les fichiers Markdown en PDF

```bash
# Option 1 : Avec pandoc (recommandé)
pandoc persona-administrateur-karim.md -o persona-administrateur-karim.pdf

# Option 2 : Avec un éditeur en ligne
# Copiez le contenu dans https://www.markdowntopdf.com/
```

---

## 🎓 Compétences démontrées

Cette documentation démontre les compétences suivantes du référentiel **DWWM (Bac+2)** :

### 🎯 Conception UX/UI
- ✅ Analyse des besoins utilisateurs
- ✅ Création de personas
- ✅ Cartographie de parcours utilisateurs
- ✅ Conception d'interfaces ergonomiques

### 🛠️ Méthodologie
- ✅ Approche centrée utilisateur (User-Centered Design)
- ✅ Identification des points de douleur (Pain Points)
- ✅ Définition d'opportunités d'amélioration
- ✅ Mesure de la réussite (KPI)

### 📋 Documentation projet
- ✅ Structuration claire de la documentation
- ✅ Utilisation de formats standards (Markdown, PDF, DOCX)
- ✅ Documentation technique ET fonctionnelle

---

## 📧 Contact

Pour toute question sur cette documentation :

- **Auteur** : Shadah Hassan Abdallah
- **Email** : shadah.hassan.abdallah@gmail.com
- **GitHub** : [@S-HASSAN-ABDALLAH](https://github.com/S-HASSAN-ABDALLAH)
- **Diplôme** : DWWM (Développeur Web et Web Mobile) - Bac+2

---

## 🚀 Roadmap - Évolutions futures

Cette section présente les fonctionnalités prévues pour les prochaines versions du projet.

### Version 2.0 - Fonctionnalités avancées (Prévue)

#### 🎯 Côté Client
- **Simulateur de devis interactif** : Estimation instantanée du budget selon les options choisies
- **Espace client sécurisé** :
  - Suivi en temps réel de l'organisation
  - Planning détaillé de l'événement
  - Documents téléchargeables (devis, factures, plan d'accès)
  - Messagerie directe avec l'organisateur
- **Galerie photos automatique** : Album photos envoyé après l'événement
- **Avis clients intégrés** : Widget Google Reviews sur le site
- **Chat en direct** : Support instantané pour questions rapides

#### 🛠️ Côté Admin
- **Dashboard amélioré** : Graphiques de statistiques (réservations par mois, type d'événement, CA)
- **Notifications push** : Alertes en temps réel pour nouvelles réservations
- **Export de rapports** : PDF/Excel des réservations et statistiques
- **Calendrier interactif** : Vue calendrier des événements planifiés
- **Gestion des prestataires** : Base de données des partenaires (salles, traiteurs, etc.)

#### 💳 Paiement et Signature
- **Paiement en ligne sécurisé** : Stripe ou PayPal intégré
- **Signature électronique** : Validation des devis en ligne
- **Factures automatiques** : Génération et envoi automatique

#### 📱 Mobile et Performance
- **Application mobile** (iOS/Android) : Gestion en déplacement
- **PWA (Progressive Web App)** : Version installable du site
- **Optimisations SEO** : Amélioration du référencement naturel

### Version 3.0 - Intelligence et Automatisation (Futur)

- **Recommandations IA** : Suggestions personnalisées selon le profil client
- **Chatbot intelligent** : Réponses automatiques aux questions fréquentes
- **Intégration calendrier** : Sync Google Calendar / Outlook
- **Programme de parrainage** : Réductions pour recommandations
- **Système de reviews vidéo** : Témoignages clients en vidéo

---

## 📅 Historique

| Date | Version | Changements |
|------|---------|-------------|
| Juillet 2025 | 1.0 | Création persona client et parcours client |
| Février 2026 | 2.0 | Ajout persona admin, parcours admin, restructuration, ajout Roadmap |

---

**Projet réalisé dans le cadre du diplôme DWWM**
