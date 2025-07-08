<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Laporan' ?> - Kerupuk Len Edi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            background-color: #f8f9fa;
        }

        .report-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .report-header {
            background-color: #f0e68c;
            padding: 15px;
            border-radius: 5px 5px 0 0;
            text-align: center;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .report-header img {
            height: 60px;
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            object-fit: contain;
        }

        .report-header-text {
            margin-left: 30px;
        }

        .report-header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .report-header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        .report-title {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
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
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .report-table th {
            background-color: #f2f2f2;
        }

        .report-footer {
            text-align: right;
            margin-top: 30px;
            padding: 0 20px 20px;
        }

        .report-footer .date {
            margin-bottom: 10px;
        }

        .report-footer .signature {
            font-weight: bold;
        }

        .no-print {
            text-align: center;
            margin-top: 20px;
        }

        .no-print button {
            padding: 8px 16px;
            margin: 0 5px;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .no-print button:hover {
            background-color: #2e59d9;
        }

        @media print {
            body {
                padding: 0;
                background-color: #fff;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .report-container {
                box-shadow: none;
                max-width: none;
                width: 100%;
            }

            .no-print {
                display: none;
            }

            .report-header {
                border-radius: 0;
            }

            .report-table th {
                background-color: #f2f2f2 !important;
            }

            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">
        <div class="report-header">
            <img src="<?= base_url('image/logo.png') ?>" alt="Logo Kerupuk Len Edi">
            <div class="report-header-text">
                <h1>Sistem Informasi Kerupuk Len Edi</h1>
                <p>Pauh Kambar, Kabupaten Padang Pariaman</p>
            </div>
        </div>

        <div class="report-title">
            <?= $title ?? 'Laporan Data' ?>
        </div>

        <div class="report-content">
            <?= $this->renderSection('content') ?>
        </div>

        <div class="report-footer">
            <div class="date">
                Padang, <?= date('d-m-Y') ?>
            </div>
            <div style="height: 80px;"></div>
            <div class="signature">
                Pimpinan
            </div>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()">Cetak Laporan</button>
        <button onclick="window.history.back()">Kembali</button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Automatically print when page loads
            window.print();
        }
    </script>
</body>

</html>