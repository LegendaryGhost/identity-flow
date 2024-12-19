<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Code d'authentification</title>
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
                            🔐
                        </div>
                        <h1 style="margin: 0 0 15px 0; font-size: 24px; font-weight: 600; color: #1a1a1a;">Code
                            d'authentification</h1>
                        <p style="margin: 0 0 25px 0; font-size: 16px; color: #6c757d;">Bonjour {{ $name }}, voici votre
                            code d'authentification :</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 30px;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center">
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            @foreach($code as $digit)
                                                <td style="padding: 0 4px;">
                                                    <div
                                                        style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; width: 45px; height: 60px; display: inline-block; text-align: center; line-height: 60px;">
                                                        <span
                                                            style="font-family: 'Courier New', monospace; font-size: 32px; font-weight: 700; color: #1a1a1a;">{{ $digit }}</span>
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>
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
                                        <strong>⚠️ Important :</strong> Ce code expire dans 90 secondes. Ne le partagez
                                        avec personne.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 40px 40px; text-align: center;">
                        <p style="margin: 0; font-size: 14px; color: #6c757d;">
                            Si vous n'avez pas demandé ce code, ignorez cet email et
                            <a href="#" style="color: #27ae60; text-decoration: none;">contactez immédiatement le
                                support</a>
                        </p>
                    </td>
                </tr>
            </table>
            <table role="presentation" width="500" border="0" cellspacing="0" cellpadding="0"
                   style="margin-top: 20px; text-align: center;">
                <tr>
                    <td style="font-size: 12px; color: #6c757d;">
                        © 2024 Tous droits réservés<br>
                        Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
