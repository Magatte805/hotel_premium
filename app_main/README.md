# hotel_premium

Stack **Symfony (backend)** + **React/Vite (frontend)** + **PostgreSQL** via Docker.

## Arborescence 

```text
.
|-- docker-compose.yml
|-- README.md
|-- backend/
|-- frontend/
`-- vendor
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

### Créer un **admin** 

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

### Option A SQLite 

```bash
docker compose exec backend php bin/phpunit --testdox
```

### Option B  PostgreSQL

1) **Créer la DB de test** :

```bash
docker compose exec database sh -lc "psql -U app -d postgres -c 'CREATE DATABASE app_test;' || true"
```



2) **Lancer PHPUnit (config Postgres)** :

```bash
docker compose exec backend php bin/phpunit -c phpunit.pgsql.xml.dist --testdox
```

## Stop

```bash
docker compose down
```