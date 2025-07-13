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
            text-align: left;
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
        <?php if (!empty($filters['bagian']) || !empty($filters['jabatan']) || !empty($filters['jenkel'])): ?>
            <?php if (!empty($filters['bagian'])): ?>
                <div class="info-item">
                    <strong>Bagian:</strong> <?= $bagian_name ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($filters['jabatan'])): ?>
                <div class="info-item">
                    <strong>Jabatan:</strong> <?= $jabatan_name ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($filters['jenkel'])): ?>
                <div class="info-item">
                    <strong>Jenis Kelamin:</strong> <?= $filters['jenkel'] ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- <div class="info-item">
            <strong>Tanggal Cetak:</strong> <?= date('d-m-Y') ?>
        </div> -->
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pegawai</th>
                <th>Nama Pegawai</th>
                <th>NIK</th>
                <th>Jabatan</th>
                <th>Bagian</th>
                <th>NoHP</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pegawai)): ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            <?php else: ?>
                <?php $no = 1;
                foreach ($pegawai as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['idpegawai'] ?></td>
                        <td><?= $row['namapegawai'] ?></td>
                        <td><?= $row['nik'] ?: '-' ?></td>
                        <td><?= $row['namajabatan'] ?></td>
                        <td><?= $row['namabagian'] ?></td>
                        <td><?= $row['nohp'] ?: '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Padang, <?= date('d F Y') ?></p>
        <br><br><br>
        <p>Pimpinan</p>
    </div>
</body>

</html>