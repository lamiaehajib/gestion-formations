<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte paiement en retard</title>
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
                            <h1 class="heading">Alerte: Paiement en retard !</h1>
                            <p>
                                Le paiement de l'étudiant <strong>{{ $inscription->user->name ?? 'N/A' }}</strong> pour la formation <strong>{{ $inscription->formation->title ?? 'N/A' }}</strong> est en retard.
                            </p>
                            
                            <div class="details-box">
                                <p><strong>Nom de l'étudiant :</strong> {{ $inscription->user->name ?? 'N/A' }}</p>
                                <p><strong>Email de l'étudiant :</strong> {{ $inscription->user->email ?? 'N/A' }}</p>
                                <p><strong>Montant total dû :</strong> {{ number_format($inscription->total_amount - $inscription->paid_amount, 2) }} DH</p>
                                <p><strong>Date d'échéance dépassée :</strong> <span style="color: #ef4444; font-weight: bold;">{{ \Carbon\Carbon::parse($inscription->next_installment_due_date)->format('d/m/Y') }}</span></p>
                                <p><strong>Statut de l'inscription :</strong> {{ ucfirst($inscription->status) }}</p>
                            </div>

                            <p style="margin-top: 30px;">
                                Veuillez contacter l'étudiant et/ou mettre à jour le statut de l'inscription dans votre panneau d'administration.
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