<div class="card shadow" id="reportCard">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Data Izin</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($filters['pegawai_id']) || !empty($filters['status']) || !empty($filters['tanggal_awal']) || !empty($filters['tanggal_akhir'])): ?>
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

                    <?php if (!empty($filters['tanggal_awal'])): ?>
                        <span class="badge bg-info">Dari: <?= date('d-m-Y', strtotime($filters['tanggal_awal'])) ?></span>
                    <?php endif; ?>

                    <?php if (!empty($filters['tanggal_akhir'])): ?>
                        <span class="badge bg-info">Sampai: <?= date('d-m-Y', strtotime($filters['tanggal_akhir'])) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($izin)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Tidak ada data izin yang ditemukan dengan filter yang dipilih.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
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
                            <th class="text-center">Aksi</th>
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
                                        <span class="badge bg-warning">Menunggu</span>
                                    <?php elseif ($item['statusizin'] == 1): ?>
                                        <span class="badge bg-success">Disetujui</span>
                                    <?php elseif ($item['statusizin'] == 2): ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/izin/show/' . $item['idizin']) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>