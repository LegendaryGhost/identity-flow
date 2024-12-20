# Scénario d'utilisation

Voici un scénario simple d'utilisation de l'application.

## Inscription

1. L'utilisateur commence d'abord par s'inscrire en fournissant ses informations d'identité :

   Voici un exemple de la requête :
    ```json
    {
        "email": "votre-email@gmail.com",
        "nom":"Doe",
        "prenom":"John",
        "mot_de_passe":"123456",
        "date_naissance":"2001-03-01"
    }
    ```

   Si les données ne sont pas conformes à ``nos règles de validation`` alors, il y aura erreur. Dans le cas contraire, on envoie un email à l'adresse précisée.

2. Validation de mail :

   Dans le corps de l'email, il suffit juste de cliquer sur le lien et le compte sera correctement créé. ``Attention, le lien ne sera valide que pendant 2 minutes``.

## Connexion

1. L'utilisateur se connecte en fournissant son email et son mot de passe :

   Voici un exemple de la requête :
    ```json
    {
        "email": "your-email@gmail.com",
        "mot_de_passe": "123456"
    }
    ```

   Si les informations d'identification sont correctes, un code PIN sera envoyé à l'adresse e-mail enregistrée pour une vérification supplémentaire.

2. Vérification du code PIN :

   L'utilisateur doit entrer le code PIN reçu par email pour compléter la connexion :
    ```json
    {
        "email": "your-email@gmail.com",
        "code_pin": "606746"
    }
    ```

   Si le code PIN est correct et valide, l'utilisateur sera authentifié et un token d'accès sera renvoyé.

## Gestion du compte utilisateur

1. Modification des informations de l'utilisateur :

   Une fois connecté, l'utilisateur peut mettre à jour ses informations personnelles. La requête doit être autorisée avec un token d'accès valide :
    ```json
    {
        "email": "rihantiana000@gmail.com",
        "nom": "Mbolatsiory",
        "prenom": "Doe",
        "mot_de_passe": "123456",
        "date_naissance": "2001-03-01"
    }
    ```

2. Réinitialisation des tentatives de connexion par email :

   Si l'utilisateur rencontre des problèmes de connexion, il peut demander une réinitialisation des tentatives de connexion en fournissant son email et un token de réinitialisation :
    ```json
    {
        "email": "your-email@gmail.com",
        "token": "resetToken123"
    }
    ```
