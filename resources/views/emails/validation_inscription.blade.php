<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de mot de passe</title>
</head>
<body>
<h1>Bonjour {{ $name }},</h1>
<p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
<a href="{{ $resetLink }}">Réinitialiser mon mot de passe</a>
<p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
</body>
</html>
