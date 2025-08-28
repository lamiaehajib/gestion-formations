<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap');

        html, body {
            background-color: #f7f7f7;
            color: #333;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-container {
            text-align: center;
            background: #fff;
            padding: 4rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
            transition: all 0.3s ease;
        }

        .icon {
            font-size: 8rem;
            color: #D32F2F; /* Couleur principale de l'icône */
            margin-bottom: 2rem;
            animation: bounce 1s infinite alternate;
        }
        
        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-10px); }
        }

        .code {
            font-size: 6rem;
            font-weight: 900;
            color: #D32F2F;
            text-shadow: 4px 4px #ef4444; /* Ombre avec une des couleurs demandées */
            margin: 0;
            line-height: 1;
        }
        
        .message {
            font-size: 2.2rem;
            font-weight: 600;
            color: #444;
            margin-top: 1rem;
        }

        .link {
            display: inline-block;
            margin-top: 2rem;
            padding: 12px 25px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
            background-color: #C2185B; /* Couleur du bouton */
            border-radius: 50px;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .link:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(194, 24, 91, 0.4);
        }
    </style>
</head>
<body>
    <div class="error-container">
        @yield('icon')
        <div class="code">@yield('code')</div>
        <div class="message">@yield('message')</div>
        <a href="{{ url('/dashboard') }}" class="link">Retour à l'accueil</a>
    </div>
</body>
</html>