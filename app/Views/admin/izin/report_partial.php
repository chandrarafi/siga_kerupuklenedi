<div class="card shadow" id="reportCard">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Data Izin</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($filters['pegawai_id']) || !empty($filters['status']) || !empty($filters['start_date']) || !empty($filters['end_date'])): ?>
            <div class="mb-3">
                <h6>Filter yang digunakan:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($filters['pegawai_id'])): ?>
                        <?php
                        // Ambil nama pegawai
                        $db = \Config\Database::connect();
                        $pegawai = $db->table('pegawai')
                            ->where('idpegawai', $filters['pegawai_id'])
                            ->get()
                            ->getRowArray();
                        ?>
                        <span class="badge bg-info">Pegawai: <?= $pegawai ? $pegawai['namapegawai'] : 'Unknown' ?></span>
                    <?php endif; ?>

                    <?php if (!empty($filters['status'])): ?>
                        <span class="badge bg-info">
                            Status:
                            <?php
                            if ($filters['status'] == '0') echo 'Menunggu';
                            elseif ($filters['status'] == '1') echo 'Disetujui';
                            elseif ($filters['status'] == '2') echo 'Ditolak';
                            ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($filters['start_date'])): ?>
                        <span class="badge bg-info">Dari: <?= date('d-m-Y', strtotime($filters['start_date'])) ?></span>
                    <?php endif; ?>

                    <?php if (!empty($filters['end_date'])): ?>
                        <span class="badge bg-info">Sampai: <?= date('d-m-Y', strtotime($filters['end_date'])) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Nama Jabatan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Jenis Izin</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($izin)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data izin yang ditemukan dengan filter yang dipilih.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php
                        $totalHariIzin = 0;
                        foreach ($izin as $item):
                            // Hitung lama izin
                            $lamaIzin = 0;
                            if (isset($item['tanggalmulaiizin']) && isset($item['tanggalselesaiizin'])) {
                                try {
                                    $start = new DateTime($item['tanggalmulaiizin']);
                                    $end = new DateTime($item['tanggalselesaiizin']);
                                    $interval = $start->diff($end);
                                    $lamaIzin = $interval->days + 1;
                                    $totalHariIzin += $lamaIzin;
                                } catch (Exception $e) {
                                    $lamaIzin = 0;
                                }
                            }
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['namapegawai'] ?? '-' ?></td>
                                <td><?= $item['namajabatan'] ?? '-' ?></td>
                                <td><?= isset($item['tanggalmulaiizin']) ? date('d/m/Y', strtotime($item['tanggalmulaiizin'])) : '-' ?></td>
                                <td><?= isset($item['tanggalselesaiizin']) ? date('d/m/Y', strtotime($item['tanggalselesaiizin'])) : '-' ?></td>
                                <td><?= $item['jenisizin'] ?? '-' ?></td>
                                <td><?= $item['alasan'] ?? '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($izin)): ?>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total Hari Izin</th>
                            <th colspan="2"><?= $totalHariIzin ?> hari</th>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <div class="mt-3">
            <p>
                <strong>Jumlah Data:</strong> <?= isset($total_izin) ? $total_izin : count($izin) ?> pengajuan
                <?php if (!empty($filters['start_date']) && !empty($filters['end_date'])): ?>
                    | <strong>Periode:</strong> <?= date('d-m-Y', strtotime($filters['start_date'])) ?> s/d <?= date('d-m-Y', strtotime($filters['end_date'])) ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>