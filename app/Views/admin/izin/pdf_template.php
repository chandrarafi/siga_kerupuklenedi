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
            padding: 5px;
            vertical-align: top;
        }

        .logo-cell {
            width: 15%;
            text-align: center;
        }

        .title-cell {
            width: 70%;
            text-align: center;
        }

        .info-cell {
            width: 15%;
            text-align: right;
            font-size: 10px;
        }

        .logo-img {
            max-width: 80px;
            max-height: 80px;
        }

        h1 {
            font-size: 18px;
            margin: 0;
            padding: 0;
        }

        h2 {
            font-size: 16px;
            margin: 5px 0;
            padding: 0;
        }

        p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11px;
        }

        th {
            background-color: #1a3c6e;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-approved {
            color: green;
            font-weight: bold;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .status-rejected {
            color: red;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }

        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 11px;
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-box {
            width: 30%;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .stat-box.total {
            background-color: #cfe2ff;
        }

        .stat-box.approved {
            background-color: #d1e7dd;
        }

        .stat-box.pending {
            background-color: #fff3cd;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
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
                <td class="title-cell">
                    <h1>LAPORAN PENGAJUAN IZIN</h1>
                    <h2>PT. KERUPUK LENEDI</h2>
                    <p>Jl. Raya Kerupuk No. 123, Padang, Sumatera Barat</p>
                </td>
                <td class="info-cell">
                    <p>Tanggal Cetak: <?= date('d-m-Y') ?></p>
                    <p>Waktu: <?= date('H:i:s') ?></p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Filter:</strong>
        <?php
        $filterText = [];

        if (!empty($filters['tanggal_awal']) && !empty($filters['tanggal_akhir'])) {
            $filterText[] = "Periode: " . date('d-m-Y', strtotime($filters['tanggal_awal'])) . " s/d " . date('d-m-Y', strtotime($filters['tanggal_akhir']));
        }

        if (!empty($filters['status'])) {
            $statusText = '';
            if ($filters['status'] == '0') $statusText = 'Menunggu';
            elseif ($filters['status'] == '1') $statusText = 'Disetujui';
            elseif ($filters['status'] == '2') $statusText = 'Ditolak';
            $filterText[] = "Status: " . $statusText;
        }

        if (!empty($filters['pegawai_id'])) {
            // Ambil nama pegawai
            $db = \Config\Database::connect();
            $pegawai = $db->table('pegawai')
                ->where('idpegawai', $filters['pegawai_id'])
                ->get()
                ->getRowArray();
            if ($pegawai) {
                $filterText[] = "Pegawai: " . $pegawai['namapegawai'];
            }
        }

        echo !empty($filterText) ? implode(' | ', $filterText) : "Semua Data";
        ?>
    </div>

    <!-- Statistics -->
    <div class="stats-container">
        <div class="stat-box total">
            <p>Total Pengajuan</p>
            <div class="stat-value"><?= count($izin) ?></div>
        </div>
        <div class="stat-box approved">
            <p>Disetujui</p>
            <?php
            $approved = array_filter($izin, function ($item) {
                return $item['statusizin'] == 1;
            });
            ?>
            <div class="stat-value"><?= count($approved) ?></div>
        </div>
        <div class="stat-box pending">
            <p>Menunggu/Ditolak</p>
            <?php
            $pending = array_filter($izin, function ($item) {
                return $item['statusizin'] != 1;
            });
            ?>
            <div class="stat-value"><?= count($pending) ?></div>
        </div>
    </div>

    <!-- Table -->
    <?php if (empty($izin)): ?>
        <p class="text-center">Tidak ada data izin yang ditemukan dengan filter yang dipilih.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Kode Izin</th>
                    <th>Nama Pegawai</th>
                    <th>NIK</th>
                    <th>Jabatan</th>
                    <th>Jenis Izin</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Lama Izin</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($izin as $item): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $item['idizin'] ?></td>
                        <td><?= $item['namapegawai'] ?></td>
                        <td><?= $item['nik'] ?></td>
                        <td><?= $item['namajabatan'] ?? '-' ?></td>
                        <td><?= $item['jenisizin'] ?></td>
                        <td><?= date('d-m-Y', strtotime($item['tanggalmulaiizin'])) ?></td>
                        <td><?= date('d-m-Y', strtotime($item['tanggalselesaiizin'])) ?></td>
                        <td>
                            <?php
                            $start = new DateTime($item['tanggalmulaiizin']);
                            $end = new DateTime($item['tanggalselesaiizin']);
                            $interval = $start->diff($end);
                            echo $interval->days + 1 . ' hari';
                            ?>
                        </td>
                        <td class="text-center">
                            <?php if ($item['statusizin'] == 0): ?>
                                <span class="status-pending">Menunggu</span>
                            <?php elseif ($item['statusizin'] == 1): ?>
                                <span class="status-approved">Disetujui</span>
                            <?php elseif ($item['statusizin'] == 2): ?>
                                <span class="status-rejected">Ditolak</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        <p>Dicetak pada: <?= date('d-m-Y H:i:s') ?></p>
    </div>
</body>

</html>