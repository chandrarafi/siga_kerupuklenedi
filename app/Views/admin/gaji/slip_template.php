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
            font-size: 18px;
            text-align: center;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
            border: none;
        }

        .info-table .label {
            width: 150px;
            font-weight: normal;
            text-align: left;
        }

        .info-table .separator {
            width: 20px;
            text-align: center;
        }

        .info-table .value {
            font-weight: bold;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .data-table th {
            background-color: #f5f8fc;
            color: #1a3c6e;
            font-weight: bold;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
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
            width: 100%;
        }

        .summary th {
            text-align: left;
            width: 60%;
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f5f8fc;
        }

        .summary td {
            text-align: right;
            padding: 8px;
            border: 1px solid #ddd;
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
        <h2>SLIP GAJI</h2>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Periode</td>
            <td class="separator">:</td>
            <td class="value">
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
                list($bulan, $tahun) = explode('-', $gaji['periode']);
                echo $bulan_list[$bulan] . ' ' . $tahun;
                ?>
            </td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="separator">:</td>
            <td class="value"><?= $pegawai['nik'] ?></td>
        </tr>
        <tr>
            <td class="label">Nama Pegawai</td>
            <td class="separator">:</td>
            <td class="value"><?= $pegawai['namapegawai'] ?></td>
        </tr>
        <tr>
            <td class="label">Jabatan / Bagian</td>
            <td class="separator">:</td>
            <td class="value"><?= $pegawai['nama_jabatan'] ?> / <?= $pegawai['namabagian'] ?></td>
        </tr>
    </table>

    <table class="data-table">
        <tr>
            <th colspan="3">Rincian Penghasilan</th>
        </tr>
        <tr>
            <td class="text-left" style="width:70%;">Gaji Pokok</td>
            <td class="text-right">Rp</td>
            <td class="text-right"><?= number_format($komponen_gaji['gaji_pokok'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="text-left">Tunjangan (<?= $detail['total_absensi'] ?> hari x Rp <?= number_format($detail['tunjangan_per_hari'], 0, ',', '.') ?>)</td>
            <td class="text-right">Rp</td>
            <td class="text-right"><?= number_format($komponen_gaji['tunjangan'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="text-left">Upah Lembur (<?= $detail['total_lembur'] ?> jam x Rp <?= number_format($detail['tarif_lembur'], 0, ',', '.') ?>)</td>
            <td class="text-right">Rp</td>
            <td class="text-right"><?= number_format($komponen_gaji['upah_lembur'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th class="text-left">Total Gaji Bersih</th>
            <td class="text-right">Rp</td>
            <td class="text-right"><?= number_format($komponen_gaji['gaji_bersih'], 0, ',', '.') ?></td>
        </tr>
    </table>

    <div class="summary">
        <table>
            <tr>
                <th>TOTAL GAJI BERSIH</th>
                <td>Rp <?= number_format($komponen_gaji['gaji_bersih'], 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <?php if (!empty($detail_lembur)): ?>
        <h4 style="margin-top: 20px; font-size: 14px; font-weight: bold; color: #1a3c6e;">Detail Lembur</h4>
        <table class="data-table" style="margin-top: 10px;">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
                <th>Durasi (Jam)</th>
                <th>Upah Lembur</th>
            </tr>
            <?php $no = 1;
            foreach ($detail_lembur as $lembur): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($lembur['tanggallembur'])) ?></td>
                    <td><?= date('H:i', strtotime($lembur['jammulai'])) ?></td>
                    <td><?= date('H:i', strtotime($lembur['jamselesai'])) ?></td>
                    <td class="text-right"><?= number_format($lembur['durasi_jam'], 2, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($lembur['upah_lembur'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="4" class="text-right">Total</th>
                <td class="text-right"><?= number_format($total_jam_lembur, 2, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($total_upah_lembur, 0, ',', '.') ?></td>
            </tr>
        </table>
    <?php endif; ?>

    <div class="footer">
        <p>Padang, <?= date('d F Y') ?></p>
        <br><br><br>
        <p>Pimpinan</p>
    </div>
</body>

</html>