<?= $this->extend('pegawai/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Card Utama Absensi -->
<div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg mb-4 overflow-hidden">
    <div class="p-4 md:p-6 text-white">
        <h2 class="text-lg md:text-xl font-bold mb-1">Selamat Datang, <?= explode(' ', $pegawai['namapegawai'])[0] ?? 'Pegawai' ?></h2>
        <p class="text-blue-100 text-sm mb-4"><?= date('l, d F Y') ?></p>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-3 text-center flex-1">
                <div id="digital-clock" class="text-2xl md:text-3xl font-bold mb-1">--:--:--</div>
                <div class="text-xs text-blue-200">Waktu Sekarang</div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-3 text-center flex-1">
                <?php if ($absensiHariIni): ?>
                    <?php if ($absensiHariIni['status'] == 'hadir'): ?>
                        <div class="text-xl font-bold <?= !empty($absensiHariIni['keterangan']) && $absensiHariIni['keterangan'] == 'Terlambat' ? 'text-yellow-300' : 'text-green-300' ?> mb-1">
                            <?= $absensiHariIni['jamkeluar'] ? 'Selesai' : 'Hadir' ?>
                            <?= !empty($absensiHariIni['keterangan']) && $absensiHariIni['keterangan'] == 'Terlambat' ? '(Terlambat)' : '' ?>
                        </div>
                        <div class="text-xs text-blue-200">Status Hari Ini</div>
                    <?php elseif ($absensiHariIni['status'] == 'sakit'): ?>
                        <div class="text-xl font-bold text-yellow-300 mb-1">Sakit</div>
                        <div class="text-xs text-blue-200">Status Hari Ini</div>
                    <?php elseif ($absensiHariIni['status'] == 'izin'): ?>
                        <div class="text-xl font-bold text-blue-300 mb-1">Izin</div>
                        <div class="text-xs text-blue-200">Status Hari Ini</div>
                    <?php else: ?>
                        <div class="text-xl font-bold text-red-300 mb-1">Alpa</div>
                        <div class="text-xs text-blue-200">Status Hari Ini</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-xl font-bold text-blue-200 mb-1">Belum Absen</div>
                    <div class="text-xs text-blue-200">Status Hari Ini</div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$absensiHariIni): ?>
            <button id="btn-absen-masuk" class="w-full py-3 bg-white text-blue-600 font-bold rounded-lg transition-all hover:bg-blue-50 flex items-center justify-center gap-2 shadow-md">
                <i class="fas fa-fingerprint text-lg"></i>
                <span>Absen Masuk</span>
            </button>
        <?php elseif ($absensiHariIni['status'] == 'hadir' && !$absensiHariIni['jamkeluar']): ?>
            <?php
            // Ambil pengaturan jam pulang
            $absensiSetting = (new \App\Models\AbsensiSettingModel())->first();
            $jamPulang = $absensiSetting['jam_pulang'] ?? '17:00:00';

            $now = time();
            $batasJamPulang = strtotime(date('Y-m-d') . ' ' . $jamPulang);
            $canAbsenPulang = $now >= $batasJamPulang;
            ?>
            <?php if ($canAbsenPulang): ?>
                <button id="btn-absen-pulang" class="w-full py-3 bg-orange-500 text-white font-bold rounded-lg transition-all hover:bg-orange-600 flex items-center justify-center gap-2 shadow-md">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                    <span>Absen Pulang</span>
                </button>
            <?php else: ?>
                <div class="w-full py-3 bg-gray-400 text-white font-bold rounded-lg flex items-center justify-center gap-2 shadow-md">
                    <i class="fas fa-clock text-lg"></i>
                    <span>Absen Pulang (Tersedia mulai <?= $jamPulang ?>)</span>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="w-full py-3 bg-green-500 text-white font-bold rounded-lg flex items-center justify-center gap-2 shadow-md">
                <i class="fas fa-check-circle text-lg"></i>
                <span>Absensi Selesai</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Detail Absensi Hari Ini -->
    <?php if ($absensiHariIni): ?>
        <div class="bg-white bg-opacity-10 backdrop-blur-lg p-3 border-t border-blue-300 border-opacity-30">
            <div class="grid grid-cols-2 gap-2">
                <div class="text-center">
                    <div class="text-xs text-blue-200 mb-1">Jam Masuk</div>
                    <div class="text-white font-bold">
                        <?= $absensiHariIni['jammasuk'] ? date('H:i', strtotime($absensiHariIni['jammasuk'])) : '-' ?>
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-xs text-blue-200 mb-1">Jam Keluar</div>
                    <div class="text-white font-bold">
                        <?= $absensiHariIni['jamkeluar'] ? date('H:i', strtotime($absensiHariIni['jamkeluar'])) : 'Belum Absen' ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="location-status" class="mb-4 text-center p-3 rounded-lg bg-blue-50 text-blue-700 text-sm">
    <i class="fas fa-location-dot mr-2"></i>
    Mengambil lokasi Anda...
</div>

<!-- Grid Statistik dan Info -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <!-- Statistik Kehadiran Bulan Ini -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-3 md:p-4 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-md md:text-lg font-bold text-gray-800">Statistik Bulan Ini</h2>
                <div class="text-blue-500">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
        </div>
        <div class="p-3 md:p-4 space-y-3">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                        <span class="text-sm font-medium text-gray-700">Hadir</span>
                    </div>
                    <span class="text-sm font-bold"><?= $stats['hadir'] ?> hari</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="bg-green-500 h-full rounded-full" style="width: <?= $stats['total'] > 0 ? ($stats['hadir'] / $stats['total'] * 100) : 0 ?>%"></div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                        <span class="text-sm font-medium text-gray-700">Sakit</span>
                    </div>
                    <span class="text-sm font-bold"><?= $stats['sakit'] ?> hari</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="bg-yellow-500 h-full rounded-full" style="width: <?= $stats['total'] > 0 ? ($stats['sakit'] / $stats['total'] * 100) : 0 ?>%"></div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                        <span class="text-sm font-medium text-gray-700">Izin</span>
                    </div>
                    <span class="text-sm font-bold"><?= $stats['izin'] ?> hari</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full" style="width: <?= $stats['total'] > 0 ? ($stats['izin'] / $stats['total'] * 100) : 0 ?>%"></div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                        <span class="text-sm font-medium text-gray-700">Alpa</span>
                    </div>
                    <span class="text-sm font-bold"><?= $stats['alpa'] ?> hari</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="bg-red-500 h-full rounded-full" style="width: <?= $stats['total'] > 0 ? ($stats['alpa'] / $stats['total'] * 100) : 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Peta Lokasi -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-3 md:p-4 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-md md:text-lg font-bold text-gray-800">Lokasi Anda</h2>
                <div class="text-blue-500">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Radius maksimum: <?= $maxDistance ?> meter dari kantor</p>
        </div>
        <div id="map" class="w-full h-48 md:h-60"></div>
    </div>
</div>

<!-- Riwayat Absensi 7 Hari Terakhir -->
<div class="bg-white rounded-xl shadow-md overflow-hidden mb-4">
    <div class="p-3 md:p-4 border-b flex items-center justify-between">
        <h2 class="text-md md:text-lg font-bold text-gray-800">Riwayat Absensi Terakhir</h2>
        <a href="<?= site_url('pegawai/dashboard/riwayat') ?>" class="text-blue-500 hover:text-blue-700 text-sm font-medium flex items-center">
            <span>Lihat Semua</span>
            <i class="fas fa-chevron-right ml-1 text-xs"></i>
        </a>
    </div>
    <div class="divide-y divide-gray-100">
        <?php if (empty($absensi7Hari)): ?>
            <div class="p-4 text-center text-gray-500">
                <i class="fas fa-calendar-xmark text-gray-300 text-3xl mb-2"></i>
                <p>Tidak ada data absensi</p>
            </div>
        <?php else: ?>
            <?php foreach ($absensi7Hari as $absen): ?>
                <div class="p-3 hover:bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <div class="text-sm font-medium text-gray-900"><?= date('l, d M Y', strtotime($absen['tanggal'])) ?></div>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <?php if ($absen['status'] == 'hadir'): ?>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full font-medium">Hadir</span>
                                    <?php if (!empty($absen['keterangan']) && $absen['keterangan'] == 'Terlambat'): ?>
                                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Terlambat</span>
                                    <?php endif; ?>
                                <?php elseif ($absen['status'] == 'sakit'): ?>
                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Sakit</span>
                                <?php elseif ($absen['status'] == 'izin'): ?>
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">Izin</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 text-xs rounded-full font-medium">Alpa</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <div>
                                <span class="text-xs text-gray-500">Masuk</span>
                                <div class="font-medium"><?= $absen['jammasuk'] ? date('H:i', strtotime($absen['jammasuk'])) : '-' ?></div>
                            </div>
                            <div class="text-gray-300">|</div>
                            <div>
                                <span class="text-xs text-gray-500">Keluar</span>
                                <div class="font-medium"><?= $absen['jamkeluar'] ? date('H:i', strtotime($absen['jamkeluar'])) : '-' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Data Lembur Terbaru -->
<div class="bg-white rounded-xl shadow-md overflow-hidden mb-4">
    <div class="p-3 md:p-4 border-b flex items-center justify-between">
        <h2 class="text-md md:text-lg font-bold text-gray-800">Lembur Terbaru</h2>
        <a href="<?= site_url('pegawai/dashboard/lembur') ?>" class="text-blue-500 hover:text-blue-700 text-sm font-medium flex items-center">
            <span>Lihat Semua</span>
            <i class="fas fa-chevron-right ml-1 text-xs"></i>
        </a>
    </div>
    <div class="divide-y divide-gray-100">
        <?php if (empty($lemburTerbaru)): ?>
            <div class="p-4 text-center text-gray-500">
                <i class="fas fa-clock text-gray-300 text-3xl mb-2"></i>
                <p>Tidak ada data lembur</p>
            </div>
        <?php else: ?>
            <?php foreach ($lemburTerbaru as $lembur): ?>
                <div class="p-3 hover:bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <div class="text-sm font-medium text-gray-900"><?= date('l, d M Y', strtotime($lembur['tanggallembur'])) ?></div>
                            <div class="text-xs text-gray-500 mt-1"><?= $lembur['alasan'] ?></div>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <div>
                                <span class="text-xs text-gray-500">Mulai</span>
                                <div class="font-medium"><?= date('H:i', strtotime($lembur['jammulai'])) ?></div>
                            </div>
                            <div class="text-gray-300">|</div>
                            <div>
                                <span class="text-xs text-gray-500">Selesai</span>
                                <div class="font-medium"><?= date('H:i', strtotime($lembur['jamselesai'])) ?></div>
                            </div>
                            <div class="text-gray-300">|</div>
                            <div>
                                <span class="text-xs text-gray-500">Durasi</span>
                                <div class="font-medium">
                                    <?php
                                    $jammulai = strtotime($lembur['jammulai']);
                                    $jamselesai = strtotime($lembur['jamselesai']);

                                    // Jika jamselesai lebih kecil dari jammulai, berarti melewati tengah malam
                                    if ($jamselesai < $jammulai) {
                                        $jamselesai += 86400; // Tambah 24 jam
                                    }

                                    $durasiMenit = round(abs($jamselesai - $jammulai) / 60);
                                    $durasiJam = floor($durasiMenit / 60);
                                    $durasiMenitSisa = $durasiMenit % 60;

                                    echo $durasiJam . 'j ' . $durasiMenitSisa . 'm';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Data Gaji Terbaru -->
<?php if ($gajiTerbaru): ?>
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-4">
        <div class="p-3 md:p-4 border-b flex items-center justify-between">
            <h2 class="text-md md:text-lg font-bold text-gray-800">Gaji Terakhir</h2>
            <a href="<?= site_url('pegawai/dashboard/gaji') ?>" class="text-blue-500 hover:text-blue-700 text-sm font-medium flex items-center">
                <span>Lihat Semua</span>
                <i class="fas fa-chevron-right ml-1 text-xs"></i>
            </a>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-blue-800 font-medium">Periode</p>
                            <p class="text-lg font-bold text-blue-800">
                                <?php
                                list($bulan, $tahun) = explode('-', $gajiTerbaru['periode']);
                                $bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                echo $bulanNames[intval($bulan) - 1] . ' ' . $tahun;
                                ?>
                            </p>
                        </div>
                        <div class="bg-blue-100 p-2 rounded-full text-blue-600">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-green-800 font-medium">Total Gaji</p>
                            <p class="text-lg font-bold text-green-800">Rp <?= number_format($gajiTerbaru['gajibersih'], 0, ',', '.') ?></p>
                        </div>
                        <div class="bg-green-100 p-2 rounded-full text-green-600">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-yellow-800 font-medium">Status</p>
                            <p class="text-lg font-bold text-yellow-800">
                                <?= $gajiTerbaru['status'] == 'paid' ? 'Dibayar' : 'Pending' ?>
                            </p>
                        </div>
                        <div class="bg-yellow-100 p-2 rounded-full text-yellow-600">
                            <i class="fas fa-<?= $gajiTerbaru['status'] == 'paid' ? 'check-circle' : 'clock' ?>"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="<?= site_url('pegawai/dashboard/slip-gaji/' . $gajiTerbaru['idgaji']) ?>" class="inline-block px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-file-invoice me-1"></i> Lihat Slip Gaji
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

<!-- Modal Loading -->
<div id="loading-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-xl shadow-2xl text-center max-w-xs mx-4 w-full">
        <div class="relative w-16 h-16 mx-auto mb-4">
            <div class="absolute inset-0 border-4 border-t-blue-500 border-r-transparent border-b-transparent border-l-transparent rounded-full animate-spin"></div>
            <div class="absolute inset-2 border-4 border-t-blue-300 border-r-transparent border-b-transparent border-l-transparent rounded-full animate-spin animation-delay-150"></div>
        </div>
        <p class="text-gray-700 font-medium text-lg" id="loading-text">Memproses absensi...</p>
        <p class="text-gray-500 text-sm mt-2">Mohon tunggu sebentar</p>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Leaflet JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    /* Perbaikan z-index untuk peta */
    .leaflet-map-pane {
        z-index: 1 !important;
    }

    .leaflet-google-layer {
        z-index: 1 !important;
    }

    .leaflet-control-container {
        z-index: 2 !important;
    }

    #map {
        position: relative;
        z-index: 1;
    }

    #sidebar,
    #mobile-sidebar {
        z-index: 1000 !important;
    }

    .leaflet-top,
    .leaflet-bottom {
        z-index: 10 !important;
    }
</style>

<script>
    // Jam Digital
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        document.getElementById('digital-clock').textContent = `${hours}:${minutes}:${seconds}`;
    }

    setInterval(updateClock, 1000);
    updateClock();

    // Toast Notification
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toast-container');

        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'flex items-center p-3 rounded-lg shadow-lg max-w-xs transition-all transform translate-x-full';

        // Set background color based on type
        if (type === 'success') {
            toast.classList.add('bg-green-500', 'text-white');
        } else if (type === 'error') {
            toast.classList.add('bg-red-500', 'text-white');
        } else if (type === 'warning') {
            toast.classList.add('bg-yellow-500', 'text-white');
        } else {
            toast.classList.add('bg-blue-500', 'text-white');
        }

        // Set icon based on type
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'error') icon = 'exclamation-circle';
        if (type === 'warning') icon = 'exclamation-triangle';

        // Create toast content
        toast.innerHTML = `
            <div class="flex-shrink-0 mr-2">
                <i class="fas fa-${icon}"></i>
            </div>
            <div class="flex-1">${message}</div>
            <div class="ml-2 flex-shrink-0 cursor-pointer" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </div>
        `;

        // Add toast to container
        toastContainer.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    }

    // Inisialisasi Peta
    let map;
    let userMarker;
    let officeMarker;
    let circle;
    let userPosition = null;

    // Koordinat kantor dari database
    const officePosition = [<?= $officeLocation['latitude'] ?>, <?= $officeLocation['longitude'] ?>];
    const maxDistance = <?= $maxDistance ?>;

    function initMap() {
        map = L.map('map', {
            zoomControl: false, // Sembunyikan kontrol zoom default
            attributionControl: false // Sembunyikan atribusi
        }).setView(officePosition, 16);

        // Tambahkan kontrol zoom di pojok kanan bawah
        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);

        // Tambahkan atribusi di pojok kanan bawah
        L.control.attribution({
            position: 'bottomright',
            prefix: false
        }).addAttribution('© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>').addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Marker kantor dengan custom icon
        officeMarker = L.marker(officePosition, {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color: #3b82f6; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 1px #3b82f6;"></div>`,
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            })
        }).addTo(map).bindPopup('<b>Lokasi Kantor</b>');

        // Radius jarak maksimum
        circle = L.circle(officePosition, {
            color: '#3b82f6',
            fillColor: '#93c5fd',
            fillOpacity: 0.2,
            radius: maxDistance
        }).addTo(map);

        // Coba dapatkan lokasi user
        getLocation();

        // Resize handler untuk peta
        function handleResize() {
            map.invalidateSize();
        }

        // Jalankan resize handler setelah DOM sepenuhnya dimuat
        window.addEventListener('resize', handleResize);
        setTimeout(handleResize, 500); // Jalankan setelah 500ms untuk memastikan peta sudah dirender
    }

    function getLocation() {
        if (navigator.geolocation) {
            document.getElementById('location-status').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mendapatkan lokasi Anda...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    userPosition = [position.coords.latitude, position.coords.longitude];

                    // Jika marker sudah ada, update posisi
                    if (userMarker) {
                        userMarker.setLatLng(userPosition);
                    } else {
                        // Buat marker baru dengan custom icon
                        userMarker = L.marker(userPosition, {
                            icon: L.divIcon({
                                className: 'custom-div-icon',
                                html: `<div style="background-color: #ef4444; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 1px #ef4444;"></div>`,
                                iconSize: [14, 14],
                                iconAnchor: [7, 7]
                            })
                        }).addTo(map).bindPopup('<b>Lokasi Anda</b>');
                    }

                    // Hitung jarak ke kantor
                    const distance = calculateDistance(
                        userPosition[0], userPosition[1],
                        officePosition[0], officePosition[1]
                    );

                    // Update status lokasi
                    const locationStatus = document.getElementById('location-status');
                    if (distance <= maxDistance) {
                        locationStatus.innerHTML = `<i class="fas fa-check-circle mr-2"></i> Anda berada dalam jangkauan kantor (${distance.toFixed(2)} m)`;
                        locationStatus.className = 'mb-4 text-center p-3 rounded-lg bg-green-50 text-green-700 text-sm';
                    } else {
                        locationStatus.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i> Anda berada diluar jangkauan kantor (${distance.toFixed(2)} m)`;
                        locationStatus.className = 'mb-4 text-center p-3 rounded-lg bg-red-50 text-red-700 text-sm';
                    }

                    // Set view peta untuk menampilkan kedua marker
                    const bounds = L.latLngBounds([userPosition, officePosition]);
                    map.fitBounds(bounds, {
                        padding: [30, 30],
                        maxZoom: 17
                    });
                },
                function(error) {
                    const locationStatus = document.getElementById('location-status');
                    locationStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> ' + getLocationErrorMessage(error);
                    locationStatus.className = 'mb-4 text-center p-3 rounded-lg bg-red-50 text-red-700 text-sm';
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        } else {
            const locationStatus = document.getElementById('location-status');
            locationStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Geolocation tidak didukung oleh browser ini';
            locationStatus.className = 'mb-4 text-center p-3 rounded-lg bg-red-50 text-red-700 text-sm';
        }
    }

    function getLocationErrorMessage(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                return "Akses lokasi ditolak oleh pengguna";
            case error.POSITION_UNAVAILABLE:
                return "Informasi lokasi tidak tersedia";
            case error.TIMEOUT:
                return "Permintaan lokasi timeout";
            case error.UNKNOWN_ERROR:
                return "Terjadi kesalahan yang tidak diketahui";
            default:
                return "Terjadi kesalahan";
        }
    }

    // Fungsi untuk menghitung jarak dalam meter
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Radius bumi dalam meter
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);

        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const d = R * c;

        return d;
    }

    function toRad(value) {
        return value * Math.PI / 180;
    }

    // Fungsi untuk absen masuk
    function absenMasuk() {
        if (!userPosition) {
            showToast('Lokasi Anda belum terdeteksi. Silahkan tunggu atau refresh halaman.', 'warning');
            return;
        }

        const distance = calculateDistance(
            userPosition[0], userPosition[1],
            officePosition[0], officePosition[1]
        );

        if (distance > maxDistance) {
            showToast(`Anda berada diluar jangkauan kantor (${distance.toFixed(2)} m). Maksimal jarak adalah ${maxDistance} m.`, 'error');
            return;
        }

        // Tampilkan loading
        document.getElementById('loading-modal').classList.remove('hidden');
        document.getElementById('loading-text').textContent = 'Memproses absensi masuk...';

        // Kirim data absensi
        fetch('<?= site_url('pegawai/dashboard/absen-masuk') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `longitude=${userPosition[1]}&latitude=${userPosition[0]}`
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading-modal').classList.add('hidden');

                if (data.status) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                document.getElementById('loading-modal').classList.add('hidden');
                showToast('Terjadi kesalahan: ' + error, 'error');
            });
    }

    // Fungsi untuk absen pulang
    function absenPulang() {
        if (!userPosition) {
            showToast('Lokasi Anda belum terdeteksi. Silahkan tunggu atau refresh halaman.', 'warning');
            return;
        }

        const distance = calculateDistance(
            userPosition[0], userPosition[1],
            officePosition[0], officePosition[1]
        );

        if (distance > maxDistance) {
            showToast(`Anda berada diluar jangkauan kantor (${distance.toFixed(2)} m). Maksimal jarak adalah ${maxDistance} m.`, 'error');
            return;
        }

        // Tampilkan loading
        document.getElementById('loading-modal').classList.remove('hidden');
        document.getElementById('loading-text').textContent = 'Memproses absensi pulang...';

        // Kirim data absensi
        fetch('<?= site_url('pegawai/dashboard/absen-pulang') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `longitude=${userPosition[1]}&latitude=${userPosition[0]}`
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading-modal').classList.add('hidden');

                if (data.status) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                document.getElementById('loading-modal').classList.add('hidden');
                showToast('Terjadi kesalahan: ' + error, 'error');
            });
    }

    // Event listener
    document.addEventListener('DOMContentLoaded', function() {
        initMap();

        // Button event listeners
        const btnAbsenMasuk = document.getElementById('btn-absen-masuk');
        if (btnAbsenMasuk) {
            btnAbsenMasuk.addEventListener('click', absenMasuk);
        }

        const btnAbsenPulang = document.getElementById('btn-absen-pulang');
        if (btnAbsenPulang) {
            btnAbsenPulang.addEventListener('click', absenPulang);
        }

        // Refresh location setiap 30 detik
        setInterval(getLocation, 30000);
    });
</script>
<?= $this->endSection() ?>