<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Débloquer votre compte</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; line-height: 1.6; color: #1a1a1a; background-color: #f4f6f9;">
<div style="width: 100%; background-color: #f4f6f9; padding: 40px 20px; box-sizing: border-box;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e9ecef;">
        <!-- Header Section -->
        <div style="padding: 40px; text-align: center;">
            <div style="font-size: 48px; color: #4a90e2; margin-bottom: 20px;">
                🔓
            </div>
            <h1 style="margin: 0 0 15px 0; font-size: 24px; font-weight: 600; color: #1a1a1a;">
                Débloquer votre compte
            </h1>
            <p style="margin: 0 0 25px 0; font-size: 16px; color: #6c757d;">
                Votre compte a été temporairement bloqué suite à plusieurs tentatives de connexion infructueuses. Cliquez sur le bouton ci-dessous pour le débloquer.
            </p>
        </div>

        <!-- Action Button -->
        <div style="padding: 0 40px 30px; text-align: center;">
            <a href="{{ $resetLink }}" style="display: inline-block; background-color: #4a90e2; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                Débloquer mon compte
            </a>
        </div>

        <!-- Info Section -->
        <div style="padding: 0 40px 40px;">
            <div style="background-color: #f8f9fa; border-radius: 6px; padding: 15px;">
                <p style="margin: 0; line-height: 1.5; font-size: 14px; color: #6c757d;">
                    <strong>ℹ️ Note :</strong> Après le déblocage, vous pourrez vous connecter normalement. Pour plus de sécurité, nous vous recommandons d'utiliser un mot de passe fort.
                </p>
            </div>
        </div>

        <!-- Fallback Link -->
        <div style="padding: 0 40px 40px; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #6c757d;">
                Si le bouton ne fonctionne pas, copiez ce lien :
                <a href="{{ $resetLink }}" style="color: #4a90e2; text-decoration: none; word-break: break-all;">
                    {{ $resetLink }}
                </a>
            </p>
        </div>
    </div>

    <!-- Footer -->
    <div style="max-width: 500px; margin: 20px auto 0; text-align: center;">
        <p style="font-size: 12px; color: #6c757d; margin: 0;">
            © 2024 Tous droits réservés<br>
            Cet email a été envoyé automatiquement, merci de ne pas y répondre.
        </p>
    </div>
</div>
</body>
</html>
