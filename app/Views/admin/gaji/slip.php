<?php
// File: app/Views/admin/gaji/slip.php
// Halaman slip gaji untuk admin
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - <?= $gaji['noslip'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .slip-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .company-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .company-logo {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .slip-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
        }

        .info-value {
            flex: 1;
        }

        .table-komponen {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-komponen th,
        .table-komponen td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .table-komponen th {
            background-color: #f5f5f5;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(200, 200, 200, 0.2);
            z-index: -1;
            pointer-events: none;
        }

        .print-button {
            margin: 20px auto;
            display: block;
        }

        @media print {
            .print-button {
                display: none;
            }

            .slip-container {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
            }

            @page {
                size: A4;
                margin: 1cm;
            }
        }
    </style>
</head>

<body>
    <div class="slip-container position-relative">
        <?php if ($gaji['status'] == 'paid') : ?>
            <div class="watermark">LUNAS</div>
        <?php elseif ($gaji['status'] == 'cancelled') : ?>
            <div class="watermark">BATAL</div>
        <?php endif; ?>

        <div class="company-header">
            <h2 class="mb-0">CV. KERUPUK LENEDI</h2>
            <p class="mb-0"><?= $setting['alamat'] ?? 'Jl. Raya Kerupuk No. 123, Kota Lenedi' ?></p>
            <p class="mb-0">Telp: <?= $setting['telepon'] ?? '(021) 123-4567' ?> | Email: <?= $setting['email'] ?? 'info@kerupuklenedi.com' ?></p>
        </div>

        <div class="slip-title">
            SLIP GAJI KARYAWAN<br>
            <span class="fs-6">Periode: <?= date('F Y', strtotime('01-' . $gaji['periode'])) ?></span>
        </div>

        <div class="row info-section">
            <div class="col-md-6">
                <div class="info-row">
                    <div class="info-label">No. Slip</div>
                    <div class="info-value">: <?= $gaji['noslip'] ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value">: <?= date('d/m/Y', strtotime($gaji['tanggal'])) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Metode Pembayaran</div>
                    <div class="info-value">: <?= $gaji['metodepembayaran'] ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-row">
                    <div class="info-label">Nama Karyawan</div>
                    <div class="info-value">: <?= $pegawai['namapegawai'] ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">NIK</div>
                    <div class="info-value">: <?= $pegawai['nik'] ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jabatan</div>
                    <div class="info-value">: <?= $pegawai['nama_jabatan'] ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Bagian</div>
                    <div class="info-value">: <?= $pegawai['namabagian'] ?></div>
                </div>
            </div>
        </div>

        <table class="table-komponen">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Keterangan</th>
                    <th width="25%">Jumlah</th>
                    <th width="25%">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Pendapatan -->
                <tr>
                    <td>1</td>
                    <td>Gaji Pokok</td>
                    <td>1 bulan</td>
                    <td><?= number_format($komponen_gaji['gaji_pokok'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Tunjangan Harian (Transport & Makan)</td>
                    <td><?= $detail['total_absensi'] ?> hari</td>
                    <td><?= number_format($komponen_gaji['tunjangan_harian'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Upah Lembur</td>
                    <td><?= $detail['total_lembur'] ?> jam</td>
                    <td><?= number_format($komponen_gaji['upah_lembur'], 0, ',', '.') ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="text-end">Total Pendapatan</td>
                    <td><?= number_format($komponen_gaji['gaji_bruto'], 0, ',', '.') ?></td>
                </tr>

                <!-- Potongan -->
                <tr>
                    <td>4</td>
                    <td>Potongan Keterlambatan</td>
                    <td>-</td>
                    <td><?= number_format($komponen_gaji['potongan'], 0, ',', '.') ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="text-end">Total Potongan</td>
                    <td><?= number_format($komponen_gaji['potongan'], 0, ',', '.') ?></td>
                </tr>

                <!-- Gaji Bersih -->
                <tr class="total-row">
                    <td colspan="3" class="text-end">Gaji Bersih</td>
                    <td><?= number_format($komponen_gaji['gaji_bersih'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <div class="row">
            <div class="col-md-12">
                <p><strong>Terbilang:</strong> <em><?= ucwords(terbilang($komponen_gaji['gaji_bersih'])) ?> Rupiah</em></p>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p>Diterima oleh,</p>
                <div class="signature-line">
                    <?= $pegawai['namapegawai'] ?>
                </div>
            </div>
            <div class="signature-box">
                <p>Mengetahui,</p>
                <div class="signature-line">
                    Pimpinan
                </div>
            </div>
        </div>


    </div>

    <button class="btn btn-primary print-button" onclick="window.print()">
        <i class="bi bi-printer"></i> Cetak Slip Gaji
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tambahkan event listener untuk tombol cetak
            document.querySelector('.print-button').addEventListener('click', function() {
                window.print();
            });
        });
    </script>
</body>

</html>