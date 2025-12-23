# hotel_premium

### But du projet
La société « Hôtels Premium », grande chaîne d’hôtels dans le monde entier, souhaite revoir son système informatique pour la gestion de ses hôtels.
Le projet permet de :
- Suivre les chambres et services de chaque hôtel.
- Gérer les travaux et remplacements de mobilier.
- Permettre la réservation des chambres par les clients et suivre l’occupation en temps réel.

### Objectifs pédagogiques :
- Développer un projet avec versionning (Git)
- Travailler de manière collaborative
- Concevoir et réaliser une base de données relationnelle
- Réaliser une application architecture n-tiers
- Utiliser le framework PHP Symfony et ses dépendances
- Tester l’application (PHPUnit)

### Technologies utilisées
- Backend : Symfony (PHP)
- Frontend : Vite
- Base de données : PostgreSQL
- Conteneurisation : Docker

### Réalisateurs:
- Magatte LO
- Khadim Diop
- Qatre En Nada Abdellah

### Historique du développement
- Le **backend** a été développé en premier et poussé sur la branche `backend`.  
- Le **frontend** a été développé ensuite et poussé sur la branche `frontend`.  
- Les deux parties ont ensuite été **fusionnées sur la branche `develop`** pour effectuer les tests et vérifier la cohérence globale.  
- Une fois validé, le projet a été **intégré dans `main`**


## Arborescence 

```text
.
|-- docker-compose.yml
|-- README.md
|-- backend/
|-- frontend/
`-- vendor/ (généré par composer, selon ton setup)
```

## Démarrage (Docker)

```bash
docker compose up -d --build
```

### URLs
- **Frontend**: `http://localhost:5173`
- **Backend API**: `http://localhost:8080`
- **PostgreSQL**: `localhost:5432` (db: `app`, user: `app`, pass: `!ChangeMe!`)

## Migrations (PostgreSQL)

```bash
docker compose exec backend php bin/console doctrine:migrations:migrate -n
```

## Créer un utilisateur (admin / client)

### Créer un **admin** (commande Symfony)

```bash
docker compose exec backend php bin/console app:create-admin --email admin@hotel.tn --password "Admin1234!"
```

Ensuite connecte-toi sur `http://localhost:5173/login` avec cet email/mot de passe.

### Créer un **client** (API register)

```bash
curl -X POST "http://localhost:8080/api/register" ^
  -H "Content-Type: application/json" ^
  -d "{\"firstName\":\"Sami\",\"lastName\":\"Ben Ali\",\"phone\":\"123456\",\"email\":\"client@hotel.tn\",\"password\":\"Client1234!\"}"
```

Puis login sur `http://localhost:5173/login`.

## Tests (PHPUnit)

### Option A — SQLite (simple, par défaut)

```bash
docker compose exec backend php bin/phpunit --testdox
```

### Option B — PostgreSQL (recommandé si tu veux coller à la prod)

1) **Créer la DB de test** (une fois) :

```bash
docker compose exec database sh -lc "psql -U app -d postgres -c 'CREATE DATABASE app_test;' || true"
```

2) **Appliquer les migrations dans `app_test`** :

```bash
docker compose exec -e APP_ENV=test -e APP_DEBUG=0 -e DATABASE_URL="postgresql://app:!ChangeMe!@database:5432/app_test?serverVersion=16&charset=utf8" backend php bin/console doctrine:migrations:migrate -n
```

3) **Lancer PHPUnit (config Postgres)** :

```bash
docker compose exec backend php bin/phpunit -c phpunit.pgsql.xml.dist --testdox
```

## Stop

```bash
docker compose down
```
