<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Réinitialisation de mot de passe</title>
</head>
<body
    style="margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; line-height: 1.6; color: #1a1a1a; background-color: #f4f6f9;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f9;">
    <tr>
        <td align="center" style="padding: 40px 20px;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="500"
                   style="border-radius: 12px; background-color: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e9ecef;">
                <tr>
                    <td style="padding: 40px; text-align: center;">
                        <div style="font-size: 48px; color: #4a90e2; margin-bottom: 20px;">
                            🔒
                        </div>
                        <h1 style="margin: 0 0 15px 0; font-size: 24px; font-weight: 600; color: #1a1a1a;">
                            Réinitialisation de mot de passe</h1>
                        <p style="margin: 0 0 25px 0; font-size: 16px; color: #6c757d;">Pour réinitialiser votre mot de
                            passe, cliquez sur le bouton ci-dessous.</p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 0 40px 40px;">
                        <table role="presentation" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="border-radius: 6px;" bgcolor="#4a90e2">
                                    <a href="{{ $resetLink }}" target="_blank"
                                       style="font-size: 16px; font-family: 'Inter', Arial, sans-serif; color: white; text-decoration: none; border-radius: 6px; padding: 12px 24px; display: inline-block; font-weight: 600; background-color: #4a90e2; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        Réinitialiser le mot de passe
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 40px;">
                        <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0"
                               style="background-color: #f8f9fa; border-radius: 6px; padding: 15px;">
                            <tr>
                                <td style="font-size: 14px; color: #6c757d;">
                                    <p style="margin: 0; line-height: 1.5;">
                                        <strong>⚠️ Important :</strong> Si vous n'avez pas demandé cette
                                        réinitialisation, ignorez simplement cet email.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 40px; text-align: center;">
                        <p style="margin: 0; font-size: 14px; color: #6c757d;">
                            Si le bouton ne fonctionne pas, copiez ce lien :
                            <a href="{{ $resetLink }}"
                               style="color: #4a90e2; text-decoration: none; word-break: break-all;">{{ $resetLink }}</a>
                        </p>
                    </td>
                </tr>
            </table>
            <table role="presentation" width="500" border="0" cellspacing="0" cellpadding="0"
                   style="margin-top: 20px; text-align: center;">
                <tr>
                    <td style="font-size: 12px; color: #6c757d;">
                        © 2024 Tous droits réservés
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
