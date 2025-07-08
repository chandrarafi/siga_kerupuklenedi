<?php
// File: app/Views/pegawai/izin/_table.php
// Partial view untuk tabel izin
?>

<?php if (empty($izin_list)): ?>
    <div class="p-8 text-center text-gray-500">
        <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-3"></i>
        <p class="text-lg font-medium text-gray-700 mb-1">Belum Ada Pengajuan</p>
        <p class="text-sm text-gray-500 mb-4">Anda belum pernah mengajukan izin, cuti atau sakit</p>
        <a href="<?= site_url('pegawai/izin/create') ?>" class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-all duration-300 gap-2">
            <i class="fas fa-plus-circle"></i>
            <span>Ajukan Izin Baru</span>
        </a>
    </div>
<?php else: ?>
    <div class="w-full overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID Izin</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Jenis</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Durasi</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($izin_list as $izin): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3.5 whitespace-nowrap text-sm font-medium text-gray-900"><?= $izin['idizin'] ?></td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-sm text-gray-700"><?= ucfirst($izin['jenisizin']) ?></td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-sm text-gray-700">
                            <?php if (!empty($izin['selected_dates'])): ?>
                                <?php
                                $dates = explode(',', $izin['selected_dates']);
                                $displayDates = [];

                                // Tampilkan maksimal 2 tanggal
                                for ($i = 0; $i < min(2, count($dates)); $i++) {
                                    $displayDates[] = date('d/m/Y', strtotime(trim($dates[$i])));
                                }

                                echo implode(', ', $displayDates);

                                // Jika ada lebih dari 2 tanggal, tambahkan +n
                                if (count($dates) > 2) {
                                    echo ' <span class="text-xs text-gray-500">+' . (count($dates) - 2) . '</span>';
                                }
                                ?>
                            <?php else: ?>
                                <?= date('d/m/Y', strtotime($izin['tanggalmulaiizin'])) ?>
                                <?php if ($izin['tanggalmulaiizin'] != $izin['tanggalselesaiizin']): ?>
                                    - <?= date('d/m/Y', strtotime($izin['tanggalselesaiizin'])) ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-sm text-gray-700">
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
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <?php
                            // 3 = Menunggu, 2 = Ditolak, 1 = Disetujui
                            if ($izin['statusizin'] == 3 || $izin['statusizin'] === null) {
                            ?>
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                    Menunggu
                                </span>
                            <?php
                            } elseif ($izin['statusizin'] == 1) {
                            ?>
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700">
                                    Disetujui
                                </span>
                            <?php
                            } elseif ($izin['statusizin'] == 2) {
                            ?>
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-700">
                                    Ditolak
                                </span>
                            <?php
                            }
                            ?>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex items-center space-x-2">
                                <button data-id="<?= $izin['idizin'] ?>" class="btn-detail text-blue-500 hover:text-blue-700 hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <?php if ($izin['statusizin'] == 3 || $izin['statusizin'] === null): ?>
                                    <a href="<?= site_url('pegawai/izin/edit/' . $izin['idizin']) ?>" class="text-yellow-500 hover:text-yellow-700 hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= site_url('pegawai/izin/delete/' . $izin['idizin']) ?>" class="text-red-500 hover:text-red-700 hover:scale-110 transition-transform duration-300" onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>