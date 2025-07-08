<?= $this->extend('pegawai/layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-lg shadow">
    <div class="p-4 sm:p-6 border-b">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold text-gray-700 mb-2 sm:mb-0">Riwayat Lembur</h2>

            <!-- Filter -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select id="bulan" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i < 10 ? '0' . $i : $i ?>" <?= $bulan == ($i < 10 ? '0' . $i : $i) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
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

    <!-- Statistik Lembur -->
    <div class="p-4 sm:p-6 border-b">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-800">Total Jam Lembur</p>
                    <p class="text-2xl font-bold text-blue-700"><?= number_format($totalLembur, 1) ?> jam</p>
                </div>
                <div class="bg-blue-200 p-2 rounded-full">
                    <i class="fas fa-clock text-blue-700"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Lembur -->
    <div class="p-4 sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Mulai</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Selesai</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($lembur)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data lembur</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lembur as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= date('d M Y', strtotime($row['tanggallembur'])) ?>
                                    <div class="text-xs text-gray-500"><?= date('l', strtotime($row['tanggallembur'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('H:i', strtotime($row['jammulai'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('H:i', strtotime($row['jamselesai'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php
                                    $jammulai = strtotime($row['jammulai']);
                                    $jamselesai = strtotime($row['jamselesai']);

                                    // Jika jamselesai lebih kecil dari jammulai, berarti melewati tengah malam
                                    if ($jamselesai < $jammulai) {
                                        $jamselesai += 86400; // Tambah 24 jam
                                    }

                                    $durasiMenit = round(abs($jamselesai - $jammulai) / 60);
                                    $durasiJam = floor($durasiMenit / 60);
                                    $durasiMenitSisa = $durasiMenit % 60;

                                    echo $durasiJam . ' jam ' . $durasiMenitSisa . ' menit';
                                    ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= $row['alasan'] ?>
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

            window.location.href = `<?= site_url('pegawai/dashboard/lembur') ?>/${bulan}/${tahun}`;
        });
    });
</script>
<?= $this->endSection() ?>