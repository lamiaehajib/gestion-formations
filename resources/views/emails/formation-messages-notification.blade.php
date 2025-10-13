<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Message de Formation</title>
    <style>
        /* Styles généraux pour l'email */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { 
            width: 100%; 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #ffffff; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        }
        .header { padding: 30px 20px; text-align: center; }
        .header img { max-width: 150px; height: auto; }
        .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
        .heading { color: #D32F2F; /* Rouge pour l'alerte */ font-size: 24px; margin-top: 0; }
        
        /* Style pour le message texte (équivalent à 'mail::panel') */
        .message-panel { 
            background-color: #f9f9f9; 
            border-left: 5px solid #ef4444; /* Rouge pour l'importance */ 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 4px; 
            white-space: pre-wrap; /* Pour respecter les sauts de ligne (nl2br) */
        }
        .message-panel p { margin: 0; }
        
        /* Style du bouton (équivalent à 'mail::button') */
        .btn-wrapper { text-align: center; margin: 30px 0; }
        .btn { 
            display: inline-block; 
            padding: 10px 20px; 
            background-color: #D32F2F; 
            color: white !important; /* !important pour forcer le blanc */
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
        }

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
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSASmWOzmLDcQQu2Rg3OAIHGZp82Hbh4f-Fig&s" alt="Logo" style="display:block; border:0;">
                        </td>
                    </tr>
                </table>
                
                <table class="content" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            <h1 class="heading" style="color: #D32F2F;">Nouveau Message Important</h1>
                            
                            <p>Bonjour ! Un nouveau message important a été envoyé par l'administration concernant vos formations.</p>

                            <p style="font-size: 16px; margin-top: 25px;"><strong>Sujet :</strong> {{ $message->subject }}</p>

                            @if($message->message)
                            <p style="font-size: 16px; margin-bottom: 10px;"><strong>Message :</strong></p>
                            <div class="message-panel">
                                <p>{!! nl2br(e($message->message)) !!}</p>
                            </div>
                            @endif

                            @if($message->audio_path)
                            <p>Un message audio a été joint à cet avis et est disponible en consultant la plateforme.</p>
                            @endif
                            
                            <div class="btn-wrapper">
                                <a href="{{ route('message.showa', $message->id) }}" class="btn">
                                    Voir le message complet dans votre espace
                                </a>
                            </div>

                            <p style="margin-top: 30px;">
                                Merci,<br>
                                L'équipe {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>
                </table>
                
                <table class="footer" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>