<?= $this->extend('pegawai/layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-lg shadow">
    <div class="p-4 sm:p-6 border-b">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold text-gray-700 mb-2 sm:mb-0">Riwayat Absensi</h2>

            <!-- Filter -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select id="bulan" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= $bulan == $i ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select id="tahun" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                        <?php for ($i = date('Y') - 2; $i <= date('Y'); $i++): ?>
                            <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="self-end">
                    <button id="btn-filter" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Kehadiran -->
    <div class="p-4 sm:p-6 border-b">
        <h3 class="text-md font-medium text-gray-700 mb-4">Statistik Kehadiran <?= date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) ?></h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-800">Hadir</p>
                        <p class="text-2xl font-bold text-green-700"><?= $stats['hadir'] ?></p>
                    </div>
                    <div class="bg-green-200 p-2 rounded-full">
                        <i class="fas fa-check text-green-700"></i>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-yellow-800">Sakit</p>
                        <p class="text-2xl font-bold text-yellow-700"><?= $stats['sakit'] ?></p>
                    </div>
                    <div class="bg-yellow-200 p-2 rounded-full">
                        <i class="fas fa-thermometer-half text-yellow-700"></i>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-800">Izin</p>
                        <p class="text-2xl font-bold text-blue-700"><?= $stats['izin'] ?></p>
                    </div>
                    <div class="bg-blue-200 p-2 rounded-full">
                        <i class="fas fa-envelope text-blue-700"></i>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-red-800">Alpa</p>
                        <p class="text-2xl font-bold text-red-700"><?= $stats['alpa'] ?></p>
                    </div>
                    <div class="bg-red-200 p-2 rounded-full">
                        <i class="fas fa-times text-red-700"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Absensi -->
    <div class="p-4 sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Keluar</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($absensi)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data absensi</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($absensi as $absen): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= date('d M Y', strtotime($absen['tanggal'])) ?>
                                    <div class="text-xs text-gray-500"><?= date('l', strtotime($absen['tanggal'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($absen['status'] == 'hadir'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hadir</span>
                                    <?php elseif ($absen['status'] == 'sakit'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Sakit</span>
                                    <?php elseif ($absen['status'] == 'izin'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Izin</span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Alpa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($absen['jammasuk']): ?>
                                        <span class="font-medium"><?= date('H:i', strtotime($absen['jammasuk'])) ?></span>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $absen['jamkeluar'] ? date('H:i', strtotime($absen['jamkeluar'])) : '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php
                                    if ($absen['jammasuk'] && $absen['jamkeluar']) {
                                        $masuk = strtotime($absen['jammasuk']);
                                        $keluar = strtotime($absen['jamkeluar']);
                                        $durasi = $keluar - $masuk;

                                        $jam = floor($durasi / 3600);
                                        $menit = floor(($durasi % 3600) / 60);

                                        echo $jam . ' jam ' . $menit . ' menit';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $absen['keterangan'] ?: '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnFilter = document.getElementById('btn-filter');
        const bulanSelect = document.getElementById('bulan');
        const tahunSelect = document.getElementById('tahun');

        btnFilter.addEventListener('click', function() {
            const bulan = bulanSelect.value;
            const tahun = tahunSelect.value;

            window.location.href = `<?= site_url('pegawai/dashboard/riwayat') ?>/${bulan}/${tahun}`;
        });
    });
</script>
<?= $this->endSection() ?>