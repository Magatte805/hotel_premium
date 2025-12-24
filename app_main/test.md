# Campagne de tests Hotel Premium (Admin Dashboard)

## 1) Objectif
Valider que le **dashboard Admin** reflète correctement l’état des chambres :
- **Réservée** (réservation confirmée en cours)
- **Libre**
- **En travaux** (maintenance “en cours” ou “prévu”)

Et que les API Admin sont correctement protégées par authentification et rôles.

## 2) Périmètre
### Inclus
- API Admin (Symfony):
  - `GET /api/admin/reservations`
  - `GET /api/admin/maintenance`
  - `GET /api/admin/maintenance/rooms/{id}`
  - `POST /api/admin/maintenance`
  - `PATCH /api/admin/maintenance/{id}`
- Frontend Admin:
  - `frontend/src/pages/admin/AdminDashboard.jsx` (affichage Réservé/Libre/Travaux)
  - `frontend/src/pages/admin/AdminWorks.jsx` (création & suivi travaux par chambre)

### Exclu
- Paiement, facturation, notifications, performance/charge.

## 3) Environnements
### Tests automatisés (CI / local)
- **PHPUnit** + Symfony `WebTestCase`
- Exécution recommandée via **Docker** (extensions PHP OK)

### Tests manuels (recette)
- Frontend (navigateur)
- Backend API + base de données dev/recette

## 4) Données de test recommandées
Créer au minimum :
- 1 Admin (`ROLE_ADMIN`)
- 1 Client (`ROLE_CLIENT`)
- 1 Hôtel + 2 chambres
- Réservations:
  - Une réservation **active**: `startDate <= today <= endDate`, status contenant “confirm”
  - Une réservation **future** (ne doit pas marquer la chambre comme occupée aujourd’hui)
- Travaux:
  - Un travail **en cours** (startDate dans le passé, endDateReal null)
  - Un travail **prévu** (startDate dans le futur)

## 5) Stratégie & niveaux
- **Automatisé**: tests API (auth + structure JSON + création/MAJ)
- **Manuel**: parcours UI (dashboard admin + page Travaux)

## 6) Cas de tests (manuel) — Dashboard Admin
### TC-UI-01 — Chambre réservée (aujourd’hui)
- **Préconditions**: Réservation confirmée active pour une chambre.
- **Étapes**:
  - Ouvrir Dashboard Admin
  - Repérer la chambre concernée
- **Résultat attendu**:
  - La ligne chambre affiche le client (nom ou email) et n’affiche pas “Libre”.

### TC-UI-02 — Chambre libre
- **Préconditions**: Pas de réservation active et pas de travaux actifs/prévus.
- **Résultat attendu**: “Libre”.

### TC-UI-03 — Chambre en travaux (en cours)
- **Préconditions**: Maintenance “en cours” sur la chambre (startDate passée).
- **Résultat attendu**: Affiche **🛠️ Travaux (en cours)** (+ description si présente).
- **Note de priorité**: “Travaux” doit **primer** sur “Réservé/Libre” si les 2 existent.

### TC-UI-04 — Chambre en travaux (prévu)
- **Préconditions**: Maintenance “prévu” sur la chambre (startDate future).
- **Résultat attendu**: Affiche **🗓️ Travaux (prévus)** (+ description si présente).

### TC-UI-05 — Robustesse données client manquantes
- **Préconditions**: réservation active mais `user.firstName/lastName` vides.
- **Résultat attendu**: Affiche l’email si dispo, sinon “Réservé”.

## 7) Cas de tests (manuel) — Admin Travaux
### TC-UI-06 — Créer un travail et vérifier le dashboard
- **Étapes**:
  - Admin → Travaux
  - Sélectionner une chambre
  - Créer une maintenance (description + startDate + endDatePlanned)
  - Revenir au dashboard
- **Résultat attendu**: la chambre est marquée “Travaux”.

## 8) Cas de tests (automatisés) — API (PHPUnit)
### Couverture
- Auth & rôles:
  - Sans auth: 401
  - ROLE_CLIENT sur `/api/admin/*`: 401/403
- Réservations:
  - Le `GET /api/admin/reservations` retourne une liste JSON contenant la réservation seedée
- Maintenances:
  - Le `GET /api/admin/maintenance` retourne une liste JSON contenant la maintenance seedée
  - `POST /api/admin/maintenance` calcule correctement le statut (`en cours` / `prévu`)
  - `PATCH /api/admin/maintenance/{id}` permet de marquer `terminé`
  - `GET /api/admin/maintenance/rooms/{id}` retourne la liste par chambre

### Localisation des tests
- **Réservations admin**: `backend/tests/Api/Admin/AdminReservationApiTest.php`
- **Maintenances admin**: `backend/tests/Api/Admin/AdminMaintenanceApiTest.php`
- **Auth (register/login)**: `backend/tests/Api/AuthFlowTest.php`
- **Boot Kernel**: `backend/tests/KernelBootTest.php`

## 9) Exécution des tests automatisés
### Dans Docker (recommandé)
Dans la racine du projet:

```bash
docker compose exec backend php bin/phpunit --testdox
```

### En local (si PHP est installé)
Dans `backend/` (nécessite les extensions PHP dont `mbstring`) :

```bash
php bin/phpunit --testdox
```

## 10) Critères d’acceptation
- Les tests PHPUnit passent (zéro erreur).
- Le dashboard admin:
  - Affiche correctement **Réservé/Libre**
  - Affiche et priorise correctement **Travaux**
  - Ne casse pas si des champs utilisateur sont manquants (fallback).


