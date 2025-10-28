<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation Prête</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #007bff;">Attestation de Scolarité Disponible</h2>
        
        <p>Bonjour **{{ $studentName }}**, </p>
        
        <p>Nous vous informons que votre attestation de scolarité pour la formation **{{ $formationTitle }}** est désormais signée et prête à être téléchargée.</p>
        
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $attestationLink }}" 
               style="background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Télécharger Mon Attestation
            </a>
        </p>

        <p>Vous pouvez également la retrouver dans votre espace étudiant, section **Mes Attestations**.</p>
        
        <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>

        <p>Cordialement,<br>
        L'Administration</p>
    </div>
    <table class="footer" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            &copy; {{ date('Y') }} UITS. Tous droits réservés.
                        </td>
                    </tr>
                </table>

</body>
</html>