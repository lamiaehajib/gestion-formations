<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Inscription</title>
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
        .btn { display: inline-block; padding: 10px 20px; background-color: #D32F2F; color: white; text-decoration: none; border-radius: 5px; }
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
                            <h1 class="heading">Nouvelle demande d'inscription</h1>
                            <p>Une nouvelle demande d'inscription a été soumise par un étudiant.</p>
                            
                            <div class="details-box">
                                <p><strong>Nom de l'étudiant :</strong> {{ $student->name }}</p>
                                <p><strong>Email de l'étudiant :</strong> {{ $student->email }}</p>
                                <p><strong>Formation choisie :</strong> {{ $inscription->formation->title ?? 'N/A' }}</p>
                                <p><strong>Statut de la demande :</strong> <span style="color: #D32F2F;">{{ ucfirst($inscription->status) }}</span></p>
                            </div>

                            <p>Veuillez vous connecter au panneau d'administration pour vérifier et valider l'inscription.</p>
                            
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