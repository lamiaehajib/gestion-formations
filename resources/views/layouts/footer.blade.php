<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Footer</title>
    <style>
      

        .dashboard-footer {
            background-color: #ffffff; /* الخلفية بيضاء */
            border-top: 1px solid #e0e0e0; /* خط خفيف فوق الـ footer */
            padding: 24px 32px;
            display: flex;
            justify-content: center;
        }

        

        p {
            margin: 0;
        }

        .text-gray-600 {
            color: #616161; /* لون رمادي غامق */
            font-size: 14px;
            font-weight: 400;
        }

        .flex-align {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .nav-link {
            color: #616161; /* لون رمادي غامق */
            font-size: 14px;
            font-weight: 400;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #ff3860; /* اللون الأحمر عند المرور عليه */
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="dashboard-footer">
        <div class="flex-between">
            <p class="text-gray-600">©2025 UITS. Tous droits réservés.</p>
           {{-- <div class="flex-align">
    <a href="#" class="nav-link" target="_blank">Licence</a>
    <a href="#" class="nav-link" target="_blank">Plus de thèmes</a>
    <a href="#" class="nav-link" target="_blank">Documentation</a>
    <a href="#" class="nav-link" target="_blank">Support</a>
</div> --}}
        </div>
    </div>

</body>
</html>