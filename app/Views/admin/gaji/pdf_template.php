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

        .summary {
            margin-top: 20px;
            width: 50%;
            float: right;
        }

        .summary table {
            border: 1px solid #ddd;
        }

        .summary th {
            text-align: left;
            width: 60%;
        }

        .summary td {
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
        <h2><?= $title ?></h2>
    </div>

    <div class="info">
        <?php if (!empty($filters['periode'])): ?>
            <div class="info-item">
                <?php
                $bulan_list = [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember'
                ];
                $bulan = substr($filters['periode'], 0, 2);
                $tahun = substr($filters['periode'], 3);
                $bulan_nama = $bulan_list[$bulan] ?? $bulan;
                ?>
                <strong>Periode:</strong> <?= $bulan_nama . ' ' . $tahun ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($filters['status'])): ?>
            <div class="info-item">
                <strong>Status:</strong> <?= ucfirst($filters['status']) ?>
            </div>
        <?php endif; ?>

        <div class="info-item">
            <strong>Tanggal Cetak:</strong> <?= date('d-m-Y') ?>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Pegawai</th>
                    <th>No. Slip</th>
                    <th>Tanggal</th>
                    <th>Total Absen</th>
                    <th>Total Lembur</th>
                    <th>Potongan</th>
                    <th>Gaji Bersih</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($gaji_list as $gaji) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $gaji['nik'] ?></td>
                        <td><?= $gaji['namapegawai'] ?></td>
                        <td><?= $gaji['noslip'] ?></td>
                        <td><?= date('d/m/Y', strtotime($gaji['tanggal'])) ?></td>
                        <td><?= $gaji['totalabsen'] ?> hari</td>
                        <td><?= $gaji['totallembur'] ?> jam</td>
                        <td>Rp <?= number_format($gaji['potongan'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($gaji['gajibersih'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($gaji['status'] == 'pending') : ?>
                                Pending
                            <?php elseif ($gaji['status'] == 'paid') : ?>
                                Dibayar
                            <?php elseif ($gaji['status'] == 'cancelled') : ?>
                                Cancelled
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Padang, <?= date('d F Y') ?></p>
        <p style="margin-top: 50px;"><strong>Pimpinan</strong></p>
    </div>
</body>

</html>