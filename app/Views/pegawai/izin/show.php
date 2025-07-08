<?php
// File: app/Views/pegawai/izin/show.php
// Halaman detail izin untuk pegawai
?>

<?php if (isset($ajax) && $ajax): ?>
    <?php if (!isset($izin) || empty($izin)): ?>
        <div class="text-center text-red-500">
            <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
            <p>Data izin tidak ditemukan atau tidak tersedia</p>
            <pre class="text-xs text-left mt-4 bg-gray-100 p-2 rounded"><?= json_encode(['id' => $izin['id'] ?? 'unknown', 'idizin' => $izin['idizin'] ?? 'unknown'], JSON_PRETTY_PRINT) ?></pre>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <!-- Status -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800"><?= $izin['idizin'] ?></h3>
                <div>
                    <?php if ($izin['statusizin'] == 3): ?>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-700">
                            Menunggu
                        </span>
                    <?php elseif ($izin['statusizin'] == 1): ?>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700">
                            Disetujui
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-700">
                            Ditolak
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Info -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="text-gray-600">Tanggal Pengajuan</div>
                    <div class="font-medium"><?= date('d M Y', strtotime($izin['created_at'])) ?></div>

                    <div class="text-gray-600">Jenis Izin</div>
                    <div class="font-medium"><?= ucfirst($izin['jenisizin']) ?></div>

                    <div class="text-gray-600">Tanggal Mulai</div>
                    <div class="font-medium">
                        <?php
                        $tglMulai = strtotime($izin['tanggalmulaiizin']);
                        $tglSelesai = strtotime($izin['tanggalselesaiizin']);

                        // Pastikan tanggal mulai tidak lebih besar dari tanggal selesai
                        if ($tglMulai <= $tglSelesai) {
                            echo date('d M Y', $tglMulai);
                        } else {
                            echo date('d M Y', $tglSelesai); // Tukar jika terbalik
                        }
                        ?>
                    </div>

                    <div class="text-gray-600">Tanggal Selesai</div>
                    <div class="font-medium">
                        <?php
                        // Pastikan tanggal selesai tidak lebih kecil dari tanggal mulai
                        if ($tglMulai <= $tglSelesai) {
                            echo date('d M Y', $tglSelesai);
                        } else {
                            echo date('d M Y', $tglMulai); // Tukar jika terbalik
                        }
                        ?>
                    </div>

                    <?php if (!empty($izin['selected_dates'])): ?>
                        <div class="text-gray-600">Tanggal Dipilih</div>
                        <div class="font-medium">
                            <?php
                            $dates = explode(',', $izin['selected_dates']);
                            // Bersihkan dan trim setiap tanggal
                            $dates = array_map('trim', $dates);
                            $dates = array_filter($dates, function ($date) {
                                return !empty($date);
                            });
                            // Urutkan tanggal
                            sort($dates);
                            foreach ($dates as $index => $date) {
                                echo date('d M Y', strtotime(trim($date)));
                                if ($index < count($dates) - 1) {
                                    echo ', ';
                                }
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="text-gray-600">Durasi</div>
                    <div class="font-medium">
                        <?php
                        if (!empty($izin['selected_dates'])) {
                            $dates = explode(',', $izin['selected_dates']);
                            // Bersihkan dan trim setiap tanggal
                            $dates = array_map('trim', $dates);
                            $dates = array_filter($dates, function ($date) {
                                return !empty($date);
                            });
                            echo count($dates) . ' hari';
                        } else {
                            $start = new DateTime($izin['tanggalmulaiizin']);
                            $end = new DateTime($izin['tanggalselesaiizin']);
                            $interval = $start->diff($end);
                            echo $interval->days + 1 . ' hari';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Alasan -->
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Alasan</h4>
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700">
                    <?= nl2br(esc($izin['alasan'])) ?>
                </div>
            </div>

            <!-- Bukti Lampiran -->
            <?php if (!empty($izin['lampiran'])): ?>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Bukti Lampiran</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <?php
                        $ext = pathinfo($izin['lampiran'], PATHINFO_EXTENSION);
                        $isPdf = strtolower($ext) === 'pdf';
                        $imgPath = base_url('uploads/izin/' . $izin['lampiran']);
                        ?>

                        <?php if ($isPdf): ?>
                            <div class="flex items-center justify-between bg-white rounded border p-3">
                                <div class="flex items-center">
                                    <i class="fas fa-file-pdf text-red-500 text-xl mr-3"></i>
                                    <span class="text-sm"><?= $izin['lampiran'] ?></span>
                                </div>
                                <a href="<?= $imgPath ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="flex justify-center">
                                <a href="<?= $imgPath ?>" target="_blank">
                                    <img src="<?= $imgPath ?>" alt="Bukti" class="max-h-48 rounded-lg shadow hover:shadow-md transition-shadow duration-300">
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Keterangan Admin -->
            <?php if ($izin['statusizin'] !== null && !empty($izin['keterangan_admin'])): ?>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Keterangan Admin</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700">
                        <?= nl2br(esc($izin['keterangan_admin'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <?php if ($izin['statusizin'] === null): ?>
                <div class="flex justify-end space-x-2 pt-2 border-t">
                    <a href="<?= site_url('pegawai/izin/edit/' . $izin['idizin']) ?>" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-300 flex items-center text-sm">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <a href="<?= site_url('pegawai/izin/delete/' . $izin['idizin']) ?>" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-300 flex items-center text-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <?= $this->extend('pegawai/layouts/main') ?>

    <?= $this->section('content') ?>
    <div class="mb-6">
        <a href="<?= site_url('pegawai/izin') ?>" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-300">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Izin
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Detail Pengajuan Izin</h2>
            <div>
                <?php if ($izin['statusizin'] === null): ?>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-700">
                        Menunggu
                    </span>
                <?php elseif ($izin['statusizin'] == 1): ?>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700">
                        Disetujui
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-700">
                        Ditolak
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="p-4 space-y-6">
            <!-- ID dan Tanggal -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-gray-50 p-4 rounded-lg">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800"><?= $izin['idizin'] ?></h3>
                    <p class="text-sm text-gray-600">Diajukan pada <?= date('d M Y', strtotime($izin['created_at'])) ?></p>
                </div>

                <div class="flex flex-col items-start md:items-end">
                    <div class="text-sm text-gray-600 mb-1">Jenis Izin</div>
                    <div class="font-medium"><?= ucfirst($izin['jenisizin']) ?></div>
                </div>
            </div>

            <!-- Detail Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Informasi Izin</h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="text-gray-600">Tanggal Mulai</div>
                            <div class="font-medium">
                                <?php
                                // Jika ada selected_dates, gunakan tanggal pertama dari array yang diurutkan
                                if (!empty($izin['selected_dates'])) {
                                    $dates = explode(',', $izin['selected_dates']);
                                    // Bersihkan dan trim setiap tanggal
                                    $dates = array_map('trim', $dates);
                                    $dates = array_filter($dates, function ($date) {
                                        return !empty($date);
                                    });
                                    sort($dates); // Urutkan tanggal
                                    echo date('d M Y', strtotime(reset($dates))); // Tampilkan tanggal pertama
                                }
                                // Jika tidak ada selected_dates, gunakan cara lama
                                else {
                                    $tglMulai = strtotime($izin['tanggalmulaiizin']);
                                    $tglSelesai = strtotime($izin['tanggalselesaiizin']);

                                    // Pastikan tanggal mulai tidak lebih besar dari tanggal selesai
                                    if ($tglMulai <= $tglSelesai) {
                                        echo date('d M Y', $tglMulai);
                                    } else {
                                        echo date('d M Y', $tglSelesai); // Tukar jika terbalik
                                    }
                                }
                                ?>
                            </div>

                            <div class="text-gray-600">Tanggal Selesai</div>
                            <div class="font-medium">
                                <?php
                                // Jika ada selected_dates, gunakan tanggal terakhir dari array yang diurutkan
                                if (!empty($izin['selected_dates'])) {
                                    $dates = explode(',', $izin['selected_dates']);
                                    // Bersihkan dan trim setiap tanggal
                                    $dates = array_map('trim', $dates);
                                    $dates = array_filter($dates, function ($date) {
                                        return !empty($date);
                                    });
                                    sort($dates); // Urutkan tanggal
                                    echo date('d M Y', strtotime(end($dates))); // Tampilkan tanggal terakhir
                                }
                                // Jika tidak ada selected_dates, gunakan cara lama
                                else if ($tglMulai <= $tglSelesai) {
                                    echo date('d M Y', $tglSelesai);
                                } else {
                                    echo date('d M Y', $tglMulai); // Tukar jika terbalik
                                }
                                ?>
                            </div>

                            <?php if (!empty($izin['selected_dates'])): ?>
                                <div class="text-gray-600">Tanggal Dipilih</div>
                                <div class="font-medium">
                                    <?php
                                    $dates = explode(',', $izin['selected_dates']);
                                    foreach ($dates as $index => $date) {
                                        echo date('d M Y', strtotime(trim($date)));
                                        if ($index < count($dates) - 1) {
                                            echo ', ';
                                        }
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>

                            <div class="text-gray-600">Durasi</div>
                            <div class="font-medium">
                                <?php
                                if (!empty($izin['selected_dates'])) {
                                    $dates = explode(',', $izin['selected_dates']);
                                    echo count($dates) . ' hari';
                                } else {
                                    $start = new DateTime($izin['tanggalmulaiizin']);
                                    $end = new DateTime($izin['tanggalselesaiizin']);
                                    $interval = $start->diff($end);
                                    echo $interval->days + 1 . ' hari';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Alasan -->
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Alasan</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700">
                        <?= nl2br(esc($izin['alasan'])) ?>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- Bukti Lampiran -->
                    <?php if (!empty($izin['lampiran'])): ?>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Bukti Lampiran</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <?php
                                $ext = pathinfo($izin['lampiran'], PATHINFO_EXTENSION);
                                $isPdf = strtolower($ext) === 'pdf';
                                $imgPath = base_url('uploads/izin/' . $izin['lampiran']);
                                ?>

                                <?php if ($isPdf): ?>
                                    <div class="flex items-center justify-between bg-white rounded border p-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-pdf text-red-500 text-xl mr-3"></i>
                                            <span class="text-sm"><?= $izin['lampiran'] ?></span>
                                        </div>
                                        <a href="<?= $imgPath ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="flex justify-center">
                                        <a href="<?= $imgPath ?>" target="_blank">
                                            <img src="<?= $imgPath ?>" alt="Bukti" class="max-h-48 rounded-lg shadow hover:shadow-md transition-shadow duration-300">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Keterangan Admin -->
                    <?php if ($izin['statusizin'] !== null && !empty($izin['keterangan_admin'])): ?>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Keterangan Admin</h4>
                            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700">
                                <?= nl2br(esc($izin['keterangan_admin'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <?php if ($izin['statusizin'] === null): ?>
                <div class="flex justify-end space-x-2 pt-4 border-t">
                    <a href="<?= site_url('pegawai/izin/edit/' . $izin['idizin']) ?>" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-300 flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit Pengajuan
                    </a>
                    <a href="<?= site_url('pegawai/izin/delete/' . $izin['idizin']) ?>" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-300 flex items-center" onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?= $this->endSection() ?>
<?php endif; ?>