<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
        }
        td {
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            
            padding: 30px 20px;
            text-align: center;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .content {
            padding: 40px 30px;
            color: #333333;
            line-height: 1.6;
        }
        .heading {
            color: #ef4444;
            font-size: 28px;
            margin-top: 0;
        }
        .paragraph {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .highlight {
            color: #C2185B;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f0f0f0;
            font-size: 12px;
            color: #666666;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>

    <table class="container" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                {{-- Header avec le logo --}}
                <table class="header" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td align="center">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSASmWOzmLDcQQu2Rg3OAIHGZp82Hbh4f-Fig&s" alt="Logo UITS" style="display:block; border:0;">
                        </td>
                    </tr>
                </table>

                {{-- Contenu principal --}}
                <table class="content" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            <h1 class="heading">Bonjour {{ $user->name }},</h1>
                            <p class="paragraph">
                                Merci de vous être inscrit sur notre plateforme. Votre inscription est en cours de validation par notre administration.
                            </p>
                            <p class="paragraph">
                                Vous recevrez un e-mail dès que votre compte sera <span class="highlight">activé</span>.
                            </p>
                            <p class="paragraph">
                                En attendant, si vous avez des questions, n'hésitez pas à nous contacter.
                            </p>
                            <p class="paragraph" style="margin-top: 30px;">
                                Cordialement,<br>
                                L'équipe UITS
                            </p>
                        </td>
                    </tr>
                </table>

                {{-- Footer --}}
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