# 🛡️ DOCUMENTATION PROTECTION CSRF - Niveau DWWM Bac+2

## 📚 TABLE DES MATIÈRES
1. [Qu'est-ce qu'une attaque CSRF ?](#quest-ce-quune-attaque-csrf)
2. [Comment fonctionne la protection CSRF ?](#comment-fonctionne-la-protection-csrf)
3. [Fichiers modifiés dans le projet](#fichiers-modifiés-dans-le-projet)
4. [Guide d'utilisation étape par étape](#guide-dutilisation-étape-par-étape)
5. [Exemples de code commentés](#exemples-de-code-commentés)
6. [Comment tester la protection ?](#comment-tester-la-protection)
7. [Questions fréquentes (FAQ)](#questions-fréquentes-faq)

---

## 🚨 QU'EST-CE QU'UNE ATTAQUE CSRF ?

**CSRF** signifie **Cross-Site Request Forgery** (Falsification de requête inter-sites).

### Scénario d'attaque SANS protection :

```
┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 1 : L'administrateur se connecte sur notre site      │
│           → Session admin active ✅                          │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 2 : L'admin visite un site PIRATE (dans un nouvel    │
│           onglet) tout en restant connecté à notre site     │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 3 : Le site pirate contient un formulaire caché :    │
│                                                              │
│   <form action="https://evenements-co.fr/admin/            │
│          update_reservation.php" method="POST">             │
│     <input name="reservation_id" value="123">               │
│     <input name="nouveau_statut" value="annule">            │
│   </form>                                                    │
│   <script>document.forms[0].submit();</script>              │
│                                                              │
│   Ce formulaire s'envoie automatiquement !                  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ ÉTAPE 4 : Notre serveur reçoit la requête POST             │
│           → La session admin est valide ✅                   │
│           → Le serveur ACCEPTE et annule la réservation !   │
│           → L'admin ne sait même pas ce qui s'est passé 😱  │
└─────────────────────────────────────────────────────────────┘
```

### **CONSÉQUENCES** :
- ❌ Modification de données à l'insu de l'admin
- ❌ Suppression de réservations
- ❌ Changement de statuts
- ❌ Actions malveillantes au nom de l'admin

---

## 🔒 COMMENT FONCTIONNE LA PROTECTION CSRF ?

### Solution : Le **TOKEN CSRF**

Un **token CSRF** est un **code secret unique** généré pour chaque session utilisateur.

```
┌──────────────────────────────────────────────────────────────┐
│ ÉTAPE 1 : Génération du token à la connexion                │
│                                                               │
│   Token généré : a7f4c8e9d2b1f6a3e5c9d7b4f8e2a6c1d9f7e3a8... │
│   Stocké en session : $_SESSION['csrf_token'] = "a7f4c..."   │
└──────────────────────────────────────────────────────────────┘
                          ↓
┌──────────────────────────────────────────────────────────────┐
│ ÉTAPE 2 : Ajout du token dans TOUS les formulaires          │
│                                                               │
│   <form method="POST" action="update_reservation.php">       │
│     <input type="hidden" name="csrf_token"                   │
│            value="a7f4c8e9d2b1f6a3e5c9d7b4f8e2a6c1d9...">    │
│     <input name="reservation_id" value="123">                │
│     <button type="submit">Confirmer</button>                 │
│   </form>                                                     │
└──────────────────────────────────────────────────────────────┘
                          ↓
┌──────────────────────────────────────────────────────────────┐
│ ÉTAPE 3 : Vérification lors de la soumission                │
│                                                               │
│   Token reçu (POST) : "a7f4c8e9d2b1f6a3e5c9d7b4f8e2..."      │
│   Token en session  : "a7f4c8e9d2b1f6a3e5c9d7b4f8e2..."      │
│                                                               │
│   Comparaison : ✅ IDENTIQUES → Requête AUTORISÉE            │
│                 ❌ DIFFÉRENTS → Requête BLOQUÉE (403)         │
└──────────────────────────────────────────────────────────────┘
```

### **Pourquoi ça fonctionne ?**

Le site **pirate** ne connaît **PAS** le token CSRF :
- Le token est généré côté serveur
- Le token est stocké dans la session PHP (invisible pour le pirate)
- Le pirate ne peut pas lire le contenu de notre site à cause de la **Same-Origin Policy** du navigateur

**Résultat** : Sans le bon token, la requête est **REJETÉE** ! 🛡️

---

## 📁 FICHIERS MODIFIÉS DANS LE PROJET

### 1️⃣ **Nouveau fichier créé** : `admin/includes/csrf_helper.php`
**Rôle** : Contient toutes les fonctions pour gérer les tokens CSRF

**Fonctions disponibles** :
- `generer_csrf_token()` : Génère et retourne un token CSRF
- `verifier_csrf_token($token)` : Vérifie si un token est valide
- `afficher_csrf_input()` : Génère un champ HTML caché avec le token
- `proteger_csrf($token)` : Bloque la requête si le token est invalide

---

### 2️⃣ **Fichier modifié** : `admin/admin_reservations.php`
**Modifications** :
```php
// ✅ AJOUTÉ : Charger les fonctions CSRF
require_once "includes/csrf_helper.php";
$csrf_token = generer_csrf_token();

// ✅ AJOUTÉ : Dans chaque formulaire
<?= afficher_csrf_input() ?>
```

---

### 3️⃣ **Fichier modifié** : `admin/update_reservation.php`
**Modifications** :
```php
// ✅ AJOUTÉ : Vérification CSRF au début du traitement POST
require_once "includes/csrf_helper.php";
proteger_csrf($_POST['csrf_token'] ?? '');
// ⚠️ Si le token est invalide, le script s'arrête ici !
```

---

### 4️⃣ **Fichier modifié** : `admin/delete_reservation.php`
**Modifications** :
```php
// ✅ AJOUTÉ : Vérification CSRF au début du traitement POST
require_once "includes/csrf_helper.php";
proteger_csrf($_POST['csrf_token'] ?? '');
// ⚠️ Si le token est invalide, le script s'arrête ici !
```

---

## 🛠️ GUIDE D'UTILISATION ÉTAPE PAR ÉTAPE

### 🔹 ÉTAPE 1 : Charger les fonctions CSRF dans les pages avec formulaires

```php
<?php
// Au début du fichier PHP (après session_start())
require_once "includes/csrf_helper.php";

// Générer le token pour cette session
$csrf_token = generer_csrf_token();
?>
```

---

### 🔹 ÉTAPE 2 : Ajouter le token dans les formulaires HTML

**Méthode automatique (recommandée)** :
```html
<form method="POST" action="update_reservation.php">
    <?= afficher_csrf_input() ?>
    <!-- ↑ Génère automatiquement : <input type="hidden" name="csrf_token" value="..."> -->

    <input type="text" name="nom_client">
    <button type="submit">Envoyer</button>
</form>
```

**Méthode manuelle** :
```html
<form method="POST" action="update_reservation.php">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

    <input type="text" name="nom_client">
    <button type="submit">Envoyer</button>
</form>
```

---

### 🔹 ÉTAPE 3 : Vérifier le token dans les fichiers de traitement

```php
<?php
// Au début du fichier de traitement POST
require_once "includes/csrf_helper.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // ⚠️ VÉRIFICATION CSRF (DOIT ÊTRE FAIT EN PREMIER)
    proteger_csrf($_POST['csrf_token'] ?? '');
    // Si le token est invalide → erreur 403 + arrêt du script
    // Si le token est valide → le script continue normalement

    // Traiter les données du formulaire...
    $nom = $_POST['nom_client'];
    // etc.
}
?>
```

---

## 💡 EXEMPLES DE CODE COMMENTÉS

### Exemple complet : Formulaire de modification de réservation

**Fichier : admin_reservations.php** (Page avec formulaire)
```php
<?php
session_start();

// Vérifier que l'admin est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ✅ Charger les fonctions CSRF
require_once "includes/csrf_helper.php";

// ✅ Générer le token CSRF
$csrf_token = generer_csrf_token();
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Modifier une réservation</h1>

    <form method="POST" action="update_reservation.php">
        <!-- ✅ Ajouter le token CSRF (champ caché) -->
        <?= afficher_csrf_input() ?>

        <!-- Champs du formulaire -->
        <input type="hidden" name="reservation_id" value="123">

        <label>Nouveau statut :</label>
        <select name="nouveau_statut">
            <option value="confirme">Confirmée</option>
            <option value="annule">Annulée</option>
        </select>

        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>
```

**Fichier : update_reservation.php** (Traitement du formulaire)
```php
<?php
session_start();

// Vérifier que l'admin est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Charger la connexion BDD
require_once "../includes/db.php";

// ✅ Charger les fonctions CSRF
require_once "includes/csrf_helper.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // ✅ ÉTAPE 0 : VÉRIFICATION CSRF (OBLIGATOIRE EN PREMIER)
    proteger_csrf($_POST['csrf_token'] ?? '');
    // Si le token est invalide :
    //   → Affiche une page d'erreur 403
    //   → Arrête le script (die)
    // Si le token est valide :
    //   → Continue l'exécution normalement

    // ✅ ÉTAPE 1 : Récupérer les données
    $reservation_id = (int) $_POST['reservation_id'];
    $nouveau_statut = htmlspecialchars($_POST['nouveau_statut']);

    // ✅ ÉTAPE 2 : Valider les données
    if (!in_array($nouveau_statut, ['confirme', 'annule'])) {
        header('Location: admin_reservations.php?error=invalid_status');
        exit();
    }

    // ✅ ÉTAPE 3 : Mettre à jour en base de données
    try {
        $sql = "UPDATE reservations SET statut = :statut WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':statut' => $nouveau_statut,
            ':id' => $reservation_id
        ]);

        // Succès
        header('Location: admin_reservations.php?success=updated');
        exit();

    } catch (PDOException $e) {
        header('Location: admin_reservations.php?error=db');
        exit();
    }
}
?>
```

---

## 🧪 COMMENT TESTER LA PROTECTION ?

### ✅ Test 1 : Vérifier que le formulaire fonctionne normalement

1. Connectez-vous en tant qu'admin
2. Allez sur la page de gestion des réservations
3. Cliquez sur "Confirmer" ou "Annuler" une réservation
4. **Résultat attendu** : La modification fonctionne normalement ✅

---

### ✅ Test 2 : Tester le blocage CSRF

1. Connectez-vous en tant qu'admin
2. Ouvrez les **DevTools** du navigateur (F12)
3. Allez dans l'onglet **Console**
4. Collez ce code JavaScript malveillant :

```javascript
// Simuler une attaque CSRF (sans le bon token)
fetch('update_reservation.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'reservation_id=1&nouveau_statut=annule&csrf_token=TOKEN_INVALIDE'
})
.then(response => response.text())
.then(data => console.log(data));
```

5. **Résultat attendu** :
   - Erreur 403 Forbidden
   - Page d'erreur "Token CSRF invalide" ✅
   - La réservation n'est PAS modifiée

---

### ✅ Test 3 : Tester avec un token vide

1. Inspectez le formulaire dans les DevTools
2. Supprimez la valeur du champ `csrf_token` :
   ```html
   <input type="hidden" name="csrf_token" value="">
   ```
3. Soumettez le formulaire
4. **Résultat attendu** : Erreur 403 + blocage ✅

---

## ❓ QUESTIONS FRÉQUENTES (FAQ)

### 🔹 Q1 : Dois-je ajouter le token dans TOUS les formulaires ?

**Réponse** : Seulement dans les formulaires qui effectuent des **actions sensibles** :
- ✅ Modifier une réservation (update)
- ✅ Supprimer une réservation (delete)
- ✅ Créer un nouvel utilisateur (create)
- ❌ Formulaire de recherche/filtres (pas de modification de données)
- ❌ Formulaire de connexion (pas encore de session)

---

### 🔹 Q2 : Le token change-t-il à chaque page ?

**Réponse** : **NON**. Le token est généré **une seule fois par session**.
- Il reste le même tant que l'utilisateur est connecté
- Il change seulement à la déconnexion ou expiration de session

---

### 🔹 Q3 : Que se passe-t-il si l'utilisateur ouvre 2 onglets ?

**Réponse** : **Aucun problème**. Les deux onglets partagent la même session PHP, donc le même token CSRF.

---

### 🔹 Q4 : La protection CSRF suffit-elle pour sécuriser mon site ?

**Réponse** : **NON**. CSRF est UNE couche de sécurité parmi d'autres :
- ✅ CSRF Tokens (protection contre CSRF)
- ✅ Requêtes préparées PDO (protection contre injection SQL)
- ✅ `htmlspecialchars()` (protection contre XSS)
- ✅ `password_verify()` (vérification sécurisée des mots de passe)
- ✅ Validation des données utilisateur
- ✅ Vérification des sessions

**⚠️ La sécurité web nécessite une approche multicouche !**

---

### 🔹 Q5 : Pourquoi utiliser `hash_equals()` au lieu de `==` ?

**Réponse** : `hash_equals()` protège contre les **attaques par timing**.

**Explication** :
```php
// ❌ MAUVAIS (vulnérable aux attaques par timing)
if ($token1 == $token2) { ... }

// ✅ BON (protection timing-attack)
if (hash_equals($token1, $token2)) { ... }
```

`hash_equals()` compare les chaînes caractère par caractère en **temps constant**, empêchant un attaquant de deviner le token par analyse du temps de réponse.

---

## 📊 TABLEAU RÉCAPITULATIF

| **Fichier** | **Rôle** | **Fonctions CSRF utilisées** |
|-------------|----------|------------------------------|
| `csrf_helper.php` | Bibliothèque de fonctions | Toutes les fonctions |
| `admin_reservations.php` | Page avec formulaires | `generer_csrf_token()` + `afficher_csrf_input()` |
| `update_reservation.php` | Traitement POST | `proteger_csrf()` |
| `delete_reservation.php` | Traitement POST | `proteger_csrf()` |

---

## 🎓 RESSOURCES COMPLÉMENTAIRES

- [OWASP - CSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
- [PHP Documentation - Sessions](https://www.php.net/manual/fr/book.session.php)
- [MDN - Same-Origin Policy](https://developer.mozilla.org/fr/docs/Web/Security/Same-origin_policy)

---

## ✅ CHECKLIST POUR LE PROJET DWWM

Avant de présenter votre projet, vérifiez :

- [ ] Le fichier `csrf_helper.php` est créé et documenté
- [ ] Tous les formulaires sensibles ont le champ `csrf_token`
- [ ] Tous les fichiers de traitement POST vérifient le token avec `proteger_csrf()`
- [ ] Les tests de blocage CSRF fonctionnent
- [ ] Vous savez expliquer comment fonctionne une attaque CSRF
- [ ] Vous savez expliquer comment votre protection fonctionne
- [ ] Votre code est commenté en français pour le jury

---

**🎉 FÉLICITATIONS ! Votre application est maintenant protégée contre les attaques CSRF !**

---

*Documentation créée pour le projet "Événement & Co" - Formation DWWM Bac+2*
*Auteur : Claude (Assistant IA) - Date : 2026*
