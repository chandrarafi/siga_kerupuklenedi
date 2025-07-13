<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a3c6e;
            background-color: #FFFFCC;
            padding: 15px;
            border-radius: 5px;
        }

        .header-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell {
            width: 120px;
            text-align: left;
        }

        .text-cell {
            text-align: center;
        }

        .logo-img {
            width: 100px;
            height: auto;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #1a3c6e;
            text-align: center;
        }

        .header p {
            margin: 5px 0;
            color: #333;
            text-align: center;
        }

        .header h2 {
            margin: 15px 0 5px 0;
            color: #1a3c6e;
            font-size: 16px;
            text-align: center;
        }

        .info {
            margin-bottom: 15px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table,
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f5f8fc;
            color: #1a3c6e;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .footer p {
            margin: 5px 0;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <?php if (!empty($logo)): ?>
                        <img src="<?= $logo ?>" alt="Logo" class="logo-img">
                    <?php endif; ?>
                </td>
                <td class="text-cell">
                    <h1>SISTEM INFORMASI KERUPUK LEN EDI</h1>
                    <p>Pauh Kambar, Kabupaten Padang Pariaman</p>
                    <p>Telp: (0751) 123456 | Email: info@kerupuklenedi.com</p>
                </td>
                <td class="logo-cell">
                    <!-- Sel kosong untuk menyeimbangkan tata letak -->
                </td>
            </tr>
        </table>
        <h2>Laporan Data Izin</h2>
    </div>

    <div class="info">
        <div class="info-item">
            <strong>Periode:</strong>
            <?= !empty($filters['start_date']) && !empty($filters['end_date']) ?
                date('d-m-Y', strtotime($filters['start_date'])) . ' s/d ' . date('d-m-Y', strtotime($filters['end_date'])) :
                'Semua Periode' ?>
        </div>
        <!-- <div class="info-item">
            <strong>Tanggal Cetak:</strong> <?= date('d-m-Y') ?>
        </div> -->
    </div>

    <!-- Table -->
    <?php if (empty($izin)): ?>
        <p class="text-center">Tidak ada data izin yang ditemukan dengan filter yang dipilih.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pegawai</th>
                    <th>Nama Jabatan</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Jenis Izin</th>
                    <th>Alasan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php
                $totalHariIzin = 0;
                foreach ($izin as $item):
                    // Hitung lama izin
                    $lamaIzin = 0;
                    if (isset($item['tanggalmulaiizin']) && isset($item['tanggalselesaiizin'])) {
                        try {
                            $start = new DateTime($item['tanggalmulaiizin']);
                            $end = new DateTime($item['tanggalselesaiizin']);
                            $interval = $start->diff($end);
                            $lamaIzin = $interval->days + 1;
                            $totalHariIzin += $lamaIzin;
                        } catch (Exception $e) {
                            $lamaIzin = 0;
                        }
                    }
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="text-left"><?= $item['namapegawai'] ?? '-' ?></td>
                        <td class="text-left"><?= $item['namajabatan'] ?? '-' ?></td>
                        <td><?= isset($item['tanggalmulaiizin']) ? date('d/m/Y', strtotime($item['tanggalmulaiizin'])) : '-' ?></td>
                        <td><?= isset($item['tanggalselesaiizin']) ? date('d/m/Y', strtotime($item['tanggalselesaiizin'])) : '-' ?></td>
                        <td class="text-left"><?= $item['jenisizin'] ?? '-' ?></td>
                        <td class="text-left"><?= $item['alasan'] ?? '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">Total Hari Izin</th>
                    <th colspan="2"><?= $totalHariIzin ?> hari</th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <div class="footer">
        <p>Padang, <?= date('d') . ' ' . date('F Y') ?></p>
        <br><br><br><br>
        <p><strong>Pimpinan</strong></p>
    </div>
</body>

</html>