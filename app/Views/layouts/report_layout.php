<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Laporan' ?> - Kerupuk Len Edi</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .report-container {
            width: 100%;
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .report-header {
            background-color: #f3e03a;
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 3px solid #e6d535;
        }

        .logo {
            width: 80px;
            height: auto;
            margin-right: 20px;
        }

        .header-text {
            flex-grow: 1;
        }

        .header-text h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
            font-weight: bold;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.5);
        }

        .header-text p {
            margin: 5px 0 0;
            font-size: 16px;
            color: #333;
        }

        .report-title {
            text-align: center;
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
            background-color: #f9f9f9;
            border-bottom: 1px solid #e0e0e0;
        }

        .report-content {
            padding: 20px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .report-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .report-footer {
            padding: 20px;
            text-align: right;
            background-color: #f9f9f9;
            border-top: 1px solid #e0e0e0;
        }

        .report-signature {
            margin-top: 50px;
            font-weight: bold;
        }

        .print-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .print-button:hover {
            background-color: #45a049;
        }

        @media print {
            body {
                background-color: white;
            }

            .report-container {
                box-shadow: none;
                max-width: 100%;
                margin: 0;
                border-radius: 0;
            }

            .no-print,
            .print-button {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">
        <div class="report-header">
            <img src="<?= base_url('image/logo.png') ?>" alt="Logo Kerupuk Len Edi" class="logo">
            <div class="header-text">
                <h1>Sistem Informasi Kerupuk Len Edi</h1>
                <p>Pauh Kambar, Kabupaten Padang Pariaman</p>
            </div>
        </div>

        <div class="report-title">
            <?= $report_title ?? 'Laporan' ?>
        </div>

        <div class="report-content">
            <?= $this->renderSection('content') ?>
        </div>

        <div class="report-footer">
            <p>Padang, <?= date('d F Y') ?></p>
            <div class="report-signature">
                Pimpinan
            </div>
        </div>
    </div>

    <button onclick="window.print()" class="print-button no-print">
        <i class="bi bi-printer"></i> Cetak Laporan
    </button>
</body>

</html>