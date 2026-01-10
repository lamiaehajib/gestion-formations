{{-- attestaion.blade.php --}}
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
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            line-height: 1.5;
            color: #000;
            padding: 25px 50px;
            max-width: 21cm;
            margin: 0 auto;
            background: white;
        }
        .header {
            margin-bottom: 20px;
        }
        .header-top {
            border-top: 3px solid #000;
            border-bottom: 3px solid #000;
            padding: 12px 10px;
            margin-bottom: 15px;
            text-align: left;
        }
        .header-img {
            width: 90px;
            height: auto;
            display: inline-block;
            vertical-align: middle;
            margin-right: 20px;
        }
        .header-text {
            display: inline-block;
            vertical-align: middle;
            max-width: calc(100% - 120px);
        }
        .header-text p {
            margin: 0;
            padding: 0;
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.3px;
            line-height: 1.3;
        }
        .header h1 {
            text-decoration: underline;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            text-align: center;
            letter-spacing: 1px;
        }
        .content {
            text-align: justify;
            margin: 15px 0;
            font-size: 13px;
        }
        .content p {
            margin: 8px 0;
            line-height: 1.6;
        }
        .info-line {
            margin: 6px 0;
            line-height: 1.6;
        }
        .signature-section {
            margin-top: 25px;
            text-align: right;
        }
        .signature-section p {
            margin: 5px 0;
        }
        .university-section {
            margin-top: 30px;
            padding: 0;
            border: 2px solid #000;
        }
        .university-section .university-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 12px 15px;
            margin: 0;
            background-color: #e8e8e8;
        }
        .university-section .university-content {
            padding: 15px;
        }
        .university-section p {
            margin: 8px 0;
            line-height: 1.6;
            text-align: justify;
            font-size: 12px;
        }
        .university-signature {
            text-align: right;
            margin-top: 25px;
        }
        .university-signature p {
            margin: 5px 0;
        }
        .directors {
            margin-top: 20px;
            text-align: center;
        }
        .directors p {
            margin: 3px 0;
            font-size: 11px;
        }
        .directors .name {
            font-weight: bold;
            margin-top: 8px;
        }
        .directors .title {
            font-weight: normal;
            font-size: 11px;
        }
        strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <img class="header-img" src="{{ public_path('edmate/assets/images/thumbs/igate.png') }}" alt="Logo IGATE">
            <div class="header-text">
                <p>INSTITUT GATE D'INFORMATIQUE ET DE GESTION 4 -PRIVE</p>
            </div>
        </div>
        <h1>ATTESTATION PRE-INSCRIPTION</h1>
    </div>

    <div class="content">
        <p>Je soussigné <strong>Mr Houcine ESRAIDI</strong> Directeur Pédagogique de l'établissement <strong>IGATE</strong>.</p>

        <p>Certifié que l'étudiant (e) : <strong>{{ $student_name }}</strong></p>

        <div class="info-line">Né (e) le : <strong>{{ $birth_date }}</strong></div>
        <div class="info-line">Nationalité : <strong>{{ $nationality }}</strong></div>
        <div class="info-line">Passeport/CIN : <strong>{{ $cin }}</strong></div>
        <div class="info-line">Est inscrit (e) à l'établissement à la date du <strong>{{ $formation_date }}</strong></div>
        <div class="info-line">Suit ses cours : <strong>{{ $formation_title }}</strong></div>
        <div class="info-line">Niveau de formation : <strong>{{ $level }}</strong></div>
        <div class="info-line">Pour une durée de Formation : d'<strong>une année -{{ $academic_year }}-</strong></div>

        <p style="margin-top: 15px;">
            <strong>Observation :</strong> La présente attestation est délivrée à l'intéressé (e) pour servir et valoir ce que de droit.
        </p>
    </div>

    <div class="signature-section">
        <p>Fait à Casablanca le ; {{ $current_date }}</p>
        <p style="margin-top: 60px;">Signature</p>
         <p class="name">Mr Houcine ESRAIDI</p>
        <p class="title">Directeur Pédagogique</p>
    </div>

    <div class="university-section">
        <p class="university-title">AMERICAN UNIVERSITY OF PROFESSIONAL STUDIES</p>
        
        <div class="university-content">
            <p>Le Directeur de l'<strong>AMERICAN UNIVERSITY OF PROFESSIONAL STUDIES</strong> au MAROC atteste que l'établissement de la Formation Professionnelle Privé <strong>IGATE</strong> (autorisé et accrédité par l'ETAT sous N°3/10/1/2004 en date du 09/03/2004) est un centre de préparation d'examens agréé <strong>AUPS</strong> depuis 2015.</p>

            <p>Nous confirmons que l'établissement <strong>IGATE</strong> nous a communiqué la liste de tous les étudiants inscrits en <strong>{{ $level }}</strong> pour l'année universitaire <strong>{{ $academic_year }}</strong>.</p>

            <p>Sur cette liste figure le nom de l'étudiant <strong>{{ $student_name }}</strong>.</p>

            <div class="signature-section">
                <p>Fait à Casablanca le, {{ $current_date }}</p>
                <p style="margin-top: 35px;">Signature</p>
                <p class="name">Mr Zakaria FAKHREDDINE</p>
        <p class="title">Directeur AUPS au Maroc</p>
            </div>
        </div>
    </div>

    
</body>
</html>