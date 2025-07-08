<?= $this->extend('pegawai/layouts/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= site_url('pegawai/dashboard/gaji') ?>" class="text-primary-600 hover:text-primary-900 inline-flex items-center space-x-1">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali ke Slip Gaji</span>
    </a>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="p-4 sm:p-6 border-b">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold text-gray-700 mb-2 sm:mb-0">Detail Slip Gaji</h2>
            <div>
                <a href="#" onclick="window.print()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                    <i class="fas fa-print"></i>
                    <span>Cetak Slip</span>
                </a>
            </div>
        </div>
    </div>

    <div class="p-4 sm:p-6 relative">
        <?php if ($gaji['status'] == 'paid') : ?>
            <div class="watermark">LUNAS</div>
        <?php elseif ($gaji['status'] == 'cancelled') : ?>
            <div class="watermark">BATAL</div>
        <?php endif; ?>

        <!-- Header Slip Gaji -->
        <div class="text-center border-b pb-6 mb-6">
            <h2 class="text-xl font-bold mb-0">CV. KERUPUK LENEDI</h2>
            <p class="mb-0"><?= $setting['alamat'] ?? 'Jl. Raya Kerupuk No. 123, Kota Lenedi' ?></p>
            <p class="mb-0">Telp: <?= $setting['telepon'] ?? '(021) 123-4567' ?> | Email: <?= $setting['email'] ?? 'info@kerupuklenedi.com' ?></p>
        </div>

        <div class="text-center mb-6">
            <h2 class="text-lg font-bold uppercase">SLIP GAJI KARYAWAN</h2>
            <p class="text-sm">Periode:
                <?php
                list($bulan, $tahun) = explode('-', $gaji['periode']);
                $bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                echo $bulanNames[intval($bulan) - 1] . ' ' . $tahun;
                ?>
            </p>
        </div>

        <!-- Informasi Pegawai dan Periode -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <div class="mb-2">
                    <span class="inline-block w-36 font-semibold">No. Slip</span>
                    <span>: <?= $gaji['noslip'] ?></span>
                </div>
                <div class="mb-2">
                    <span class="inline-block w-36 font-semibold">Tanggal</span>
                    <span>: <?= date('d/m/Y', strtotime($gaji['tanggal'])) ?></span>
                </div>
                <div class="mb-2">
                    <span class="inline-block w-36 font-semibold">Metode Pembayaran</span>
                    <span>: <?= $gaji['metodepembayaran'] ?? 'Transfer Bank' ?></span>
                </div>
            </div>

            <div>
                <div class="mb-2">
                    <span class="inline-block w-36 font-semibold">Nama Karyawan</span>
                    <span>: <?= $gaji['namapegawai'] ?></span>
                </div>
                <div class="mb-2">
                    <span class="inline-block w-36 font-semibold">NIK</span>
                    <span>: <?= $gaji['nik'] ?></span>
                </div>
                <div class="mb-2">
                    <span class="inline-block w-36 font-semibold">Jabatan</span>
                    <span>: <?= $gaji['nama_jabatan'] ?? '-' ?></span>
                </div>
                <div class="mb-2">
                    <span class="inline-block w-36 font-semibold">Bagian</span>
                    <span>: <?= $gaji['namabagian'] ?? '-' ?></span>
                </div>
            </div>
        </div>

        <!-- Komponen Gaji -->
        <div class="mb-6 overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2 text-left" width="5%">No</th>
                        <th class="border px-4 py-2 text-left" width="45%">Keterangan</th>
                        <th class="border px-4 py-2 text-left" width="25%">Jumlah</th>
                        <th class="border px-4 py-2 text-left" width="25%">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Pendapatan -->
                    <tr>
                        <td class="border px-4 py-2">1</td>
                        <td class="border px-4 py-2">Gaji Pokok</td>
                        <td class="border px-4 py-2">1 bulan</td>
                        <td class="border px-4 py-2"><?= number_format($komponen_gaji['gaji_pokok'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">2</td>
                        <td class="border px-4 py-2">Tunjangan Harian (Transport & Makan)</td>
                        <td class="border px-4 py-2"><?= $detail['total_absensi'] ?> hari</td>
                        <td class="border px-4 py-2"><?= number_format($komponen_gaji['tunjangan_harian'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">3</td>
                        <td class="border px-4 py-2">Upah Lembur</td>
                        <td class="border px-4 py-2"><?= $detail['total_lembur'] ?> jam</td>
                        <td class="border px-4 py-2"><?= number_format($komponen_gaji['upah_lembur'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="bg-gray-100 font-semibold">
                        <td class="border px-4 py-2" colspan="3" style="text-align: right;">Total Pendapatan</td>
                        <td class="border px-4 py-2"><?= number_format($komponen_gaji['gaji_bruto'], 0, ',', '.') ?></td>
                    </tr>

                    <!-- Potongan -->
                    <tr>
                        <td class="border px-4 py-2">4</td>
                        <td class="border px-4 py-2">Potongan Keterlambatan</td>
                        <td class="border px-4 py-2">-</td>
                        <td class="border px-4 py-2"><?= number_format($komponen_gaji['potongan'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="bg-gray-100 font-semibold">
                        <td class="border px-4 py-2" colspan="3" style="text-align: right;">Total Potongan</td>
                        <td class="border px-4 py-2"><?= number_format($komponen_gaji['potongan'], 0, ',', '.') ?></td>
                    </tr>

                    <!-- Gaji Bersih -->
                    <tr class="bg-gray-100 font-semibold">
                        <td class="border px-4 py-2" colspan="3" style="text-align: right;">Gaji Bersih</td>
                        <td class="border px-4 py-2"><?= number_format($komponen_gaji['gaji_bersih'], 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Terbilang -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <p class="mb-0"><span class="font-semibold">Terbilang:</span> <em><?= terbilang($komponen_gaji['gaji_bersih']) ?> rupiah</em></p>
        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div class="text-center">
                <p>Diterima oleh,</p>
                <div class="h-16"></div>
                <p class="font-semibold"><?= $gaji['namapegawai'] ?></p>
            </div>
            <div class="text-center">
                <p><?= $setting['kota'] ?? 'Kota Lenedi' ?>, <?= date('d F Y', strtotime($gaji['tanggal'])) ?></p>
                <p>Dibuat oleh,</p>
                <div class="h-16"></div>
                <p class="font-semibold">Bagian Keuangan</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-500">
            <p>Slip gaji ini dihasilkan secara elektronik dan sah tanpa tanda tangan.</p>
            <p>Dicetak pada <?= date('d F Y H:i:s') ?></p>
        </div>
    </div>
</div>

<!-- Style -->
<style type="text/css">
    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 100px;
        color: rgba(200, 200, 200, 0.2);
        z-index: 0;
        pointer-events: none;
    }

    @media print {
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            background-color: white;
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        nav,
        header,
        footer,
        .no-print,
        button,
        a {
            display: none !important;
        }

        main {
            padding: 0 !important;
            margin: 0 !important;
        }

        .bg-white {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(200, 200, 200, 0.2);
            z-index: -1;
        }
    }
</style>
<?= $this->endSection() ?>