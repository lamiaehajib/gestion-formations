<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription activée</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .header { padding: 30px 20px; text-align: center; }
        .header img { max-width: 150px; height: auto; }
        .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
        .heading { color: #ef4444; font-size: 24px; margin-top: 0; }
        .details-box { background-color: #f9f9f9; border-left: 5px solid #C2185B; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .details-box p { margin: 0; }
        .footer { text-align: center; padding: 20px; background-color: #f0f0f0; font-size: 12px; color: #666666; border-top: 1px solid #e0e0e0; }
    </style>
</head>
<body>
    <table class="container" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                <table class="header" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td align="center">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSASmWOzmLDcQQu2Rg3OAIHGZp82Hbh4f-Fig&s" alt="Logo UITS" style="display:block; border:0;">
                        </td>
                    </tr>
                </table>
                <table class="content" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            <h1 class="heading">Félicitations, votre inscription est active !</h1>
                            <p>
                                Bonjour {{ $inscription->user->name ?? 'N/A' }},
                            </p>
                            <p>
                                Nous avons le plaisir de vous informer que votre inscription pour la formation <strong>{{ $inscription->formation->title ?? 'N/A' }}</strong> a été acceptée et est maintenant active.
                            </p>
                            
                            <div class="details-box">
                                <p><strong>Statut de l'inscription :</strong> <span style="color: #4CAF50; font-weight: bold;">Active</span></p>
                                <p><strong>Formation :</strong> {{ $inscription->formation->title ?? 'N/A' }}</p>
                                <p><strong>Date d'inscription :</strong> {{ \Carbon\Carbon::parse($inscription->inscription_date)->format('d/m/Y') }}</p>
                            </div>

                            <p style="margin-top: 30px;">
                                Vous pouvez maintenant accéder à tous les contenus de la formation sur notre plateforme.
                            </p>
                             <p style="text-align: center; margin-top: 30px; color:#fff;">
                                <a href="{{ route('login') }}" class="btn">Se connecter maintenant</a>
                            </p>
                            
                            <p style="margin-top: 30px;">
                                Cordialement,<br>
                                L'équipe UITS
                            </p>
                        </td>
                    </tr>
                </table>
                <table class="footer" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            &copy; {{ date('Y') }} UITS. Tous droits réservés.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>