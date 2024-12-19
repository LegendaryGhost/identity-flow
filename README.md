# identity-flow
Une API fournisseur d'identité
Ce projet est une application Laravel qui utilise PostgreSQL comme base de données.
Ce guide vous aidera à démarrer et à exécuter l'application sur votre machine.

## Prérequis

Avant de commencer, assurez-vous que vous avez installé les éléments suivants sur votre machine :

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Postman](https://www.postman.com/) (optionnel, pour tester les API)

## Démarrage de l'application

1. **Construisez et démarrez les services Docker**

Utilisez Docker Compose pour démarrer l'application avec PostgreSQL et l'application Laravel.
```bash
docker-compose up --build
```
Cette commande va construire l'image Docker et démarrer les conteneurs pour l'application Laravel et la base de données PostgreSQL.

2. **Accédez à l'application Laravel**

Une fois les services démarrés, l'application sera accessible sur :
**Hôte** : localhost
**Port** : 8000
Vous pouvez y accéder en ouvrant votre navigateur et en allant à l'URL suivante :
```
http://localhost:8000
```

3. **Vérification de l'API avec Postman**
Si vous avez besoin de tester l'API, ajoutez un Bearer Token dans l'onglet "Authorization" de Postman.
**Type** : Bearer Token
**Token** : <votre_token> (assurez-vous d'utiliser un token valide)

Exemple de requête API :
**URL** : http://localhost:8000/api/votre_route
**Méthode** : GET, POST, etc.
**Authorization** : Bearer Token

Commandes utiles
Pour démarrer l'application en arrière-plan :
```bash
docker-compose up -d
```
Pour arrêter les services :
```bash
docker-compose down
```

**Dépendances** :
- PHP 8.2
- Laravel 10
- PostgreSQL 15
- Composer
- Docker
