{{-- attestation.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attestation de Scolarité</title>

    <style>
        @page {
            margin: 2cm;
            size: A4;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            line-height: 1.6;
            color: #000;
            padding: 25px 50px;
            max-width: 21cm;
            margin: 0 auto;
            background: #fff;
        }
        .header {
            margin-bottom: 20px;
        }
        .header-top {
            border-top: 3px solid #000;
            border-bottom: 3px solid #000;
            padding: 12px 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .header-img {
            width: 90px;
            margin-right: 20px;
        }
        .header-text p {
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 1px;
            margin-top: 10px;
        }
        .content {
            margin-top: 20px;
            text-align: justify;
        }
        .info-line {
            margin: 6px 0;
        }
        .signature-section {
            margin-top: 30px;
            text-align: right;
        }
        .signature-section .name {
            font-weight: bold;
            margin-top: 5px;
        }
        .signature-section .title {
            font-size: 12px;
        }
        .university-section {
            margin-top: 35px;
            border: 2px solid #000;
        }
        .university-title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            padding: 10px;
            background: #e8e8e8;
            letter-spacing: 1px;
        }
        .university-content {
            padding: 15px;
            font-size: 12px;
            text-align: justify;
        }
        strong {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-top">
            <img class="header-img" src="{{ public_path('edmate/assets/images/thumbs/igate.png') }}" alt="IGATE Logo">
            <div class="header-text">
                <p>INSTITUT GATE D'INFORMATIQUE ET DE GESTION 4 - PRIVÉ</p>
            </div>
        </div>
        <h1>ATTESTATION DE SCOLARITÉ</h1>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <p>
            Je soussigné <strong>Mr Houcine ESRAIDI</strong>, Directeur Pédagogique de l’établissement
            <strong>IGATE</strong>, certifie que :
        </p>

        <p><strong>L’étudiant(e) :</strong> {{ $student_name }}</p>

        <div class="info-line">Né(e) le : <strong>{{ $birth_date }}</strong></div>
        <div class="info-line">Nationalité : <strong>{{ $nationality }}</strong></div>
        <div class="info-line">CIN / Passeport : <strong>{{ $cin }}</strong></div>

        <div class="info-line">
            Est régulièrement inscrit(e) et poursuit ses études au sein de l’établissement
            <strong>IGATE</strong> au titre de l’année universitaire
            <strong>{{ $academic_year }}</strong>.
        </div>

        <div class="info-line">
            Filière : <strong>{{ $formation_title }}</strong>
        </div>

        <div class="info-line">
            Niveau de formation : <strong>{{ $level }}</strong>
        </div>

        <p style="margin-top:15px;">
            La présente attestation de scolarité est délivrée à l’intéressé(e)
            pour servir et valoir ce que de droit.
        </p>
    </div>

    <!-- SIGNATURE -->
    <div class="signature-section">
        <p>Fait à Casablanca, le {{ $current_date }}</p>
        <p style="margin-top:60px;">Signature</p>
        <p class="name">Mr Houcine ESRAIDI</p>
        <p class="title">Directeur Pédagogique</p>
    </div>

    <!-- UNIVERSITY PART -->
    <div class="university-section">
        <div class="university-title">
            AMERICAN UNIVERSITY OF PROFESSIONAL STUDIES
        </div>

        <div class="university-content">
            <p>
                Le Directeur de l’<strong>AMERICAN UNIVERSITY OF PROFESSIONAL STUDIES</strong> au Maroc
                atteste que l’établissement de Formation Professionnelle Privée
                <strong>IGATE</strong>, autorisé et accrédité par l’État,
                est un centre de préparation d’examens agréé AUPS.
            </p>

            <p>
                Nous confirmons que l’établissement <strong>IGATE</strong> nous a communiqué
                la liste des étudiants régulièrement inscrits en
                <strong>{{ $level }}</strong> pour l’année universitaire
                <strong>{{ $academic_year }}</strong>.
            </p>

            <p>
                Le nom de l’étudiant(e) <strong>{{ $student_name }}</strong>
                figure sur cette liste.
            </p>

            <div class="signature-section">
                <p>Fait à Casablanca, le {{ $current_date }}</p>
                <p style="margin-top:40px;">Signature</p>
                <p class="name">Mr Zakaria FAKHREDDINE</p>
                <p class="title">Directeur AUPS au Maroc</p>
            </div>
        </div>
    </div>

</body>
</html>
