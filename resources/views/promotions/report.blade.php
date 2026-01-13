<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de la Promotion - {{ $reportData['promotion']->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #111827;
            line-height: 1.2;
            background-color: #ffffff;
            font-size: 8px;
        }

        .container {
            width: 100%;
            padding: 5mm;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Report Header - Très compact */
        .report-header {
            text-align: center;
            margin-bottom: 4mm;
        }

        .report-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #dc2626;
            margin-bottom: 0.5mm;
        }

        .report-date {
            color: #6b7280;
            font-size: 0.7rem;
        }

        /* Stats Cards - Plus compact */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2mm;
            margin-bottom: 4mm;
        }

        .stat-card {
            padding: 3mm;
            border-radius: 4px;
            color: white;
            font-weight: 600;
            text-align: center;
        }

        .stat-card-red { background: #ef4444; }
        .stat-card-green { background: #10b981; }
        .stat-card-purple { background: #a855f7; }
        .stat-card-yellow { background: #f59e0b; }

        .stat-label {
            font-size: 0.5rem;
            margin-bottom: 0.5mm;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 0.9rem;
            font-weight: 800;
        }

        /* Section compacte */
        .section {
            margin-bottom: 3mm;
        }
        
        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 2mm;
        }
        
        /* Détails en 2 colonnes pour économiser l'espace */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2mm;
            margin-bottom: 3mm;
            background-color: #f8f9fa;
            padding: 3mm;
            border-radius: 4px;
        }

        .detail-item {
            display: flex;
            font-size: 0.7rem;
        }

        .detail-label {
            font-weight: 600;
            color: #4b5563;
            min-width: 25mm;
        }

        .detail-value {
            color: #1f2937;
        }

        /* Tableau des étudiants - Ultra compact avec CIN et Téléphone */
        .table-container {
            flex: 1;
            overflow: hidden;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            font-size: 0.6rem;
        }

        .students-table thead {
            background-color: #f1f5f9;
        }

        .students-table th {
            padding: 1.5mm 2mm;
            text-align: left;
            font-size: 0.5rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }

        .students-table td {
            padding: 1.5mm 2mm;
            font-size: 0.55rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .student-info {
            line-height: 1.1;
        }

        .student-name {
            font-weight: 600;
            margin-bottom: 0.3mm;
            font-size: 0.6rem;
        }

        .student-email {
            color: #6b7280;
            font-size: 0.5rem;
        }

        .contact-info {
            line-height: 1.2;
        }

        .contact-item {
            margin-bottom: 0.3mm;
            font-size: 0.55rem;
        }

        .status-badge {
            padding: 0.8mm 1.5mm;
            border-radius: 3px;
            font-size: 0.45rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-green { background-color: #dcfce7; color: #166534; }
        .status-yellow { background-color: #fef3c7; color: #92400e; }
        .status-red { background-color: #fee2e2; color: #991b1b; }

        /* Montants alignés à droite */
        .amount {
            text-align: right;
            font-weight: 600;
        }

        /* Force single page */
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        @media print {
            body { 
                font-size: 7px; 
            }
            .container { 
                height: auto; 
                max-height: 100vh; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Report Header -->
        <div class="report-header">
            <h1 class="report-title">Rapport de la Promotion</h1>
            <p class="report-date">Généré le: {{ $reportData['generation_date']->format('d/m/Y H:i') }}</p>
        </div>
        
        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card stat-card-red">
                <p class="stat-label">Étudiants</p>
                <p class="stat-value">{{ $reportData['summary']['total_students'] }}</p>
            </div>
            <div class="stat-card stat-card-green">
                <p class="stat-label">Revenus</p>
                <p class="stat-value">{{ number_format($reportData['summary']['total_revenue'], 0) }}</p>
            </div>
            <div class="stat-card stat-card-purple">
                <p class="stat-label">Payé</p>
                <p class="stat-value">{{ number_format($reportData['summary']['total_paid'], 0) }}</p>
            </div>
            <div class="stat-card stat-card-yellow">
                <p class="stat-label">Reste</p>
                <p class="stat-value">{{ number_format($reportData['summary']['total_remaining'], 0) }}</p>
            </div>
        </div>

        <!-- Promotion Details en grid -->
        <div class="section">
            <h2 class="section-title">Détails de la Promotion</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Nom:</span>
                    <span class="detail-value">{{ $reportData['promotion']->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Formation:</span>
                    <span class="detail-value">{{ $reportData['promotion']->formation->title }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Année:</span>
                    <span class="detail-value">{{ $reportData['promotion']->year }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Catégorie:</span>
                    <span class="detail-value">{{ $reportData['promotion']->formation->category->name }}</span>
                </div>
            </div>
        </div>

        <!-- Student List Table -->
        <div class="section">
            <h2 class="section-title">Étudiants ({{ count($reportData['students']) }})</h2>
        </div>
        
        <div class="table-container">
            <table class="students-table">
                <thead>
                    <tr>
                        <th style="width: 22%;">Étudiant</th>
                        <th style="width: 13%;">CIN</th>
                        <th style="width: 13%;">Téléphone</th>
                        <th style="width: 12%;">État</th>
                        <th style="width: 13%;">Payé</th>
                        <th style="width: 13%;">Reste</th>
                        <th style="width: 14%;">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['students'] as $student)
                    <tr>
                        <td>
                            <div class="student-info">
                                <div class="student-name">{{ $student['name'] }}</div>
                                <div class="student-email">{{ $student['email'] }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                <div class="contact-item">{{ $student['cin'] ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                <div class="contact-item">{{ $student['phone'] ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClass = [
                                    'Payé' => 'status-green',
                                    'Partiellement payé' => 'status-yellow',
                                    'Non payé' => 'status-red',
                                ][$student['payment_status']] ?? 'status-red';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $student['payment_status'] === 'Partiellement payé' ? 'Partiel' : $student['payment_status'] }}
                            </span>
                        </td>
                        <td class="amount">{{ number_format($student['paid_amount'], 0) }}</td>
                        <td class="amount">{{ number_format($student['remaining_amount'], 0) }}</td>
                        <td>{{ $student['payment_type'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 10px;">Aucun étudiant</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>