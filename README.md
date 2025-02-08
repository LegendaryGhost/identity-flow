# identity-flow

Ce projet est une API fournisseur d'identité créée avec Laravel et utilise PostgreSQL comme base de données.

Ce guide vous aidera à démarrer et à exécuter l'application sur votre machine.

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les éléments suivants :

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Postman](https://www.postman.com/) (optionnel, pour tester les API)

## Démarrage de l'application

1. **Créer un réseau docker partagé**

    ```shell
    docker network create shared_network
    ```

2. **Construire et démarrer les services Docker**

   Utilisez Docker Compose :
    ```bash
    docker-compose up --build
    ```
   Cette commande construira l'image Docker et démarrera les conteneurs pour l'application Laravel et la base de données PostgreSQL.

3. **Accéder à l'application Laravel**

   Une fois les services démarrés, l'application sera accessible à l'adresse suivante : http://localhost:8000.

4. **Tester l'API avec Postman**

   Pour tester l'API, ajoutez un Bearer Token dans l'onglet "Authorization" de Postman.
    - **Type** : Bearer Token
    - **Token** : `<votre_token>` (assurez-vous d'utiliser un token valide)

## Commandes Utiles

- Pour démarrer l'application en arrière-plan :
    ```bash
    docker-compose up -d
    ```
- Pour arrêter les services :
    ```bash
    docker-compose down
    ```

## Bon à savoir 

- Pour configurer la durée de vie des tokens, etc., aller dans le fichier [.env](.env) et naviguer vers la 6è ligne. 
- **Attention** ⚠️ : Pour assurer l'envoi d'email, on vous prie de ``désactiver l'antivirus`` de votre système.

---  

## Livrables

Les livrables sont situés dans le répertoire [livrable](livrable) à la racine du projet. Dedans, vous y verrez les fichiers suivants :
- **mcd.loo** : Le modèle conceptuel de données.
- **SCENARIO.md** : Un example de scénario d'utilisation.
- **collection-postman.json** : La collection postman pour tester l'API.
- **TODO.xlsx** : Notre liste des tâches.

## Dépendances

- PHP 8.2
- Laravel 10
- PostgreSQL 15
- Composer
- Docker
