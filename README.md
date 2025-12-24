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
- Frontend : REACT(Vite)
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
`-- vendor/ 
```

## Démarrage du projet (Docker)
Ce projet est entièrement dockerisé. Il est composé de :
- un backend Symfony (API)
- un frontend React (Vite)
- une base de données PostgreSQL

#### Prérequis
- Docker
- Docker Compose

### Étape 1 — Lancer l’application
Depuis la racine du projet (app_main) :
```bash
docker compose up -d --build
```
Cette commande :
- construit les images Docker
- démarre les conteneurs frontend, backend et database
  
#### URLs disponibles
- **Frontend** : http://localhost:5173
- **Backend (API)**: http://localhost:8080
- **PostgreSQL** :
      - host : localhost
      - port : 5432
      - database : app
      - user : app
      - password : !ChangeMe!

## Étape 2 — Initialiser la base de données (migrations)
Une fois les conteneurs démarrés, il est nécessaire de créer les tables de la base de données.
```bash
docker compose exec backend php bin/console doctrine:migrations:migrate -n
```
Cette commande :
- applique les migrations Doctrine
- crée toutes les tables nécessaires au fonctionnement de l’application

## Étape 3 — Création des utilisateurs
L’application gère deux types d’utilisateurs :
- Administrateur
- Client

### Création d’un administrateur (via le terminal)
Pour des raisons de sécurité, un administrateur est créé uniquement via une **commande Symfony**:

```bash
docker compose exec backend php bin/console app:create-admin \
  --email admin@hotel.tn \
  --password "ton_mdp"
```
Cet administrateur peut :
- gérer les hôtels
- visualiser les chambres
- suivre les réservations
- superviser l’occupation en temps réel

Une fois créé, il peut se connecter via http://localhost:5173/login avec son email/mot de passe.


### Création d’un client
Deux possibilités :
➤ **Via l’API (exemple avec curl)**

```bash
curl -X POST "http://localhost:8080/api/register" ^
  -H "Content-Type: application/json" ^
  -d "{\"firstName\":\"Sami\",\"lastName\":\"Ben Ali\",\"phone\":\"123456\",\"email\":\"client@hotel.tn\",\"password\":\"Client1234!\"}"
```

➤ **Via l’interface utilisateur**
Un client peut également créer un compte directement depuis l’interface frontend en remplissant le formulaire d’inscription.

Une fois inscrit, le client peut :
- se connecter via http://localhost:5173/login
- consulter les hôtels
- réserver une chambre sur une période donnée
- annuler une réservation

## Les Tests(PHPUnit)
L'application est testée avec **PHPUnit** sur la base PostgreSQL configurée dans Docker.  
Cette configuration correspond exactement à l’environnement de production.

### ***Étape 1 — Créer la base de test**

```bash
docker-compose exec database sh -lc "psql -U app -d postgres -c 'CREATE DATABASE app_test;' || true"
```
Crée une base app_test indépendante de la base principale.

### **Étape 2 — Appliquer les migrations dans app_test**
```bash
docker-compose exec -e APP_ENV=test -e APP_DEBUG=0 -e DATABASE_URL='postgresql://app:!ChangeMe!@database:5432/app_test?serverVersion=16&charset=utf8' backend php bin/console doctrine:migrations:migrate -n
```
- Prépare la base de test avec toutes les tables nécessaires.
- Les migrations sont identiques à celles de la base principale.

### **Étape 3 — Lancer PHPUnit**
```bash
docker-compose exec backend php bin/phpunit -c phpunit.pgsql.xml.dist --testdox
```
- Tous les tests unitaires et fonctionnels sont exécutés sur la base app_test.
- Les données de la base principale ne sont pas modifiées.


## Arrêter le docker 
```bash
docker-compose down
```
