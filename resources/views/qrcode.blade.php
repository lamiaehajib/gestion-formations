<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - uits-portail.ma</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -200px;
            right: -200px;
            animation: pulse 4s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -150px;
            left: -150px;
            animation: pulse 5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .container {
            background: rgba(255, 255, 255, 0.98);
            padding: 50px 40px;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5),
                        0 0 0 1px rgba(255, 255, 255, 0.1);
            text-align: center;
            max-width: 550px;
            width: 100%;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
        }

        .logo-container {
            margin-bottom: 30px;
            animation: fadeInDown 0.8s ease-out;
        }

        .logo-container img {
            max-width: 200px;
            height: auto;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        h1 {
            color: #1a1a1a;
            margin-bottom: 8px;
            font-size: 32px;
            font-weight: 700;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 25px;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        .url {
            color: #dc2626;
            font-size: 20px;
            margin-bottom: 35px;
            font-weight: 700;
            letter-spacing: 0.5px;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .qr-wrapper {
            background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
            padding: 30px;
            border-radius: 20px;
            display: inline-block;
            margin: 20px 0;
            box-shadow: 
                inset 0 2px 8px rgba(0, 0, 0, 0.05),
                0 10px 30px rgba(220, 38, 38, 0.1);
            border: 3px solid #dc2626;
            animation: fadeInScale 0.8s ease-out 0.5s both;
        }

        #qrcode {
            display: inline-block;
            background: white;
            border-radius: 12px;
            padding: 15px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .download-btn {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
            position: relative;
            overflow: hidden;
        }

        .download-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .download-btn:hover::before {
            left: 100%;
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(220, 38, 38, 0.4);
        }

        .download-btn:active {
            transform: translateY(0);
        }

        .print-btn {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);
        }

        .info {
            color: #666;
            font-size: 15px;
            margin-top: 30px;
            line-height: 1.8;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e5e7eb 100%);
            border-radius: 12px;
            border-left: 4px solid #dc2626;
            animation: fadeInUp 0.8s ease-out 0.7s both;
        }

        .info strong {
            color: #1a1a1a;
            display: block;
            margin-bottom: 8px;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media print {
            body {
                background: white;
            }
            .button-group, .info {
                display: none;
            }
            .container {
                box-shadow: none;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 30px 20px;
                border-radius: 16px;
                max-width: 100%;
            }

            .logo-container img {
                max-width: 150px;
            }
            
            h1 {
                font-size: 24px;
            }

            .subtitle {
                font-size: 14px;
            }

            .url {
                font-size: 18px;
                margin-bottom: 25px;
            }

            .qr-wrapper {
                padding: 20px;
                border-width: 2px;
            }

            #qrcode {
                padding: 10px;
            }
            
            .button-group {
                flex-direction: column;
                gap: 12px;
            }
            
            .download-btn, .print-btn {
                width: 100%;
                padding: 16px 24px;
                font-size: 15px;
            }

            .info {
                font-size: 14px;
                padding: 16px;
                margin-top: 25px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 5px;
            }

            .container {
                padding: 25px 15px;
            }

            .logo-container {
                margin-bottom: 20px;
            }

            .logo-container img {
                max-width: 130px;
            }

            h1 {
                font-size: 20px;
                margin-bottom: 6px;
            }

            .subtitle {
                font-size: 13px;
                margin-bottom: 20px;
            }

            .url {
                font-size: 16px;
                margin-bottom: 20px;
            }

            .qr-wrapper {
                padding: 15px;
                margin: 15px 0;
            }

            #qrcode canvas {
                max-width: 100% !important;
                height: auto !important;
            }

            .button-group {
                margin-top: 20px;
                gap: 10px;
            }

            .download-btn, .print-btn {
                padding: 14px 20px;
                font-size: 14px;
            }

            .info {
                font-size: 13px;
                padding: 14px;
                margin-top: 20px;
            }

            body::before,
            body::after {
                display: none;
            }
        }

        @media (max-width: 360px) {
            .container {
                padding: 20px 12px;
            }

            h1 {
                font-size: 18px;
            }

            .logo-container img {
                max-width: 110px;
            }

            .url {
                font-size: 15px;
            }

            .qr-wrapper {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('edmate/assets/images/thumbs/log.jpg') }}" alt="Union IT Services">
        </div>
        
        <h1>Code QR d'Accès</h1>
        <div class="subtitle">Portail Union IT Services</div>
        <div class="url">uits-portail.ma</div>
        
        <div class="qr-wrapper">
            <div id="qrcode"></div>
        </div>
        
        <div class="button-group">
            <button class="download-btn" onclick="downloadQR()">
                📥 Télécharger QR Code
            </button>
            <button class="print-btn" onclick="window.print()">
                🖨️ Imprimer
            </button>
        </div>
        
        <div class="info">
            <strong>📱 Comment scanner ?</strong>
            Ouvrez l'appareil photo de votre smartphone et pointez-le vers le QR code.<br>
            Vous serez automatiquement redirigé vers le portail Union IT Services.
        </div>
    </div>

    <script>
        // Génération du QR Code avec les couleurs Union IT Services
        // Ajuster la taille selon l'écran
        let qrSize = 280;
        if (window.innerWidth < 480) {
            qrSize = Math.min(window.innerWidth - 100, 220);
        } else if (window.innerWidth < 768) {
            qrSize = 240;
        }

        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "https://uits-portail.ma",
            width: qrSize,
            height: qrSize,
            colorDark: "#1a1a1a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Fonction pour télécharger le QR Code
        function downloadQR() {
            const canvas = document.querySelector('#qrcode canvas');
            if (canvas) {
                const url = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = 'union-it-services-qr-code.png';
                link.href = url;
                link.click();
            }
        }
    </script>
</body>
</html>