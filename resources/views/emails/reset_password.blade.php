<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f4f4;">
    <tr>
        <td align="center" style="padding: 20px 0;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <tr>
                    <td style="padding: 40px 30px; text-align: center; background-color: #3498db; color: white;">
                        <h1 style="margin: 0; font-size: 24px; color: white;">Réinitialisation de mot de passe</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 30px 30px 20px;">
                        <p style="margin: 0 0 15px 0;">Bonjour {{ $name }},</p>
                        <p style="margin: 0 0 20px 0;">Vous avez récemment demandé à réinitialiser le mot de passe de votre compte. Cliquez sur le bouton ci-dessous pour procéder :</p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 0 30px 30px;">
                        <table role="presentation" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="border-radius: 5px;" bgcolor="#3498db">
                                    <a href="{{ $resetLink }}" target="_blank" style="font-size: 16px; font-family: Arial, sans-serif; color: white; text-decoration: none; border-radius: 5px; padding: 12px 20px; border: 1px solid #3498db; display: inline-block; font-weight: bold;">
                                        Réinitialiser mon mot de passe
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 30px;">
                        <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f9f9f9; border-left: 4px solid #e74c3c;">
                            <tr>
                                <td style="padding: 15px; font-size: 14px; color: #333;">
                                    <p style="margin: 0;">⚠️ Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email ou contacter notre support si vous êtes préoccupé.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 20px 30px; font-size: 12px; color: #777; text-align: center;">
                        <p style="margin: 0;">Si le bouton ne fonctionne pas, copiez et collez le lien suivant dans votre navigateur :</p>
                        <p style="margin: 10px 0 0; word-break: break-all;">{{ $resetLink }}</p>
                        <p style="margin: 20px 0 0;">Cordialement,<br>Votre équipe de support</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
