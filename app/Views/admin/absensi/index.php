<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Absensi</h5>

            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="<?= site_url('admin/absensi') ?>" method="get" class="d-flex">
                            <div class="input-group">
                                <span class="input-group-text">Tanggal</span>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $tanggal ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="<?= site_url('admin/absensi') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php if (!empty($_GET['tanggal'])): ?>
                            <div class="alert alert-info py-2 mb-0">
                                <small>Menampilkan data absensi untuk tanggal: <strong><?= date('d-m-Y', strtotime($tanggal)) ?></strong></small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info py-2 mb-0">
                                <small>Menampilkan semua data absensi</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="absensi-table" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Pegawai</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($absensi)) : ?>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <?php if (!empty($_GET['tanggal'])): ?>
                                            Tidak ada data absensi pada tanggal <?= date('d-m-Y', strtotime($tanggal)) ?>
                                        <?php else: ?>
                                            Tidak ada data absensi
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php $no = 1;
                                foreach ($absensi as $row) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['namapegawai'] ?></td>
                                        <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= $row['jammasuk'] ? date('H:i', strtotime($row['jammasuk'])) : '-' ?></td>
                                        <td><?= $row['jamkeluar'] ? date('H:i', strtotime($row['jamkeluar'])) : '-' ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'hadir') : ?>
                                                <span class="badge bg-success">Hadir</span>
                                            <?php elseif ($row['status'] == 'sakit') : ?>
                                                <span class="badge bg-warning">Sakit</span>
                                            <?php elseif ($row['status'] == 'izin') : ?>
                                                <span class="badge bg-info">Izin</span>
                                            <?php else : ?>
                                                <span class="badge bg-danger">Alpa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $row['keterangan'] ?: '-' ?></td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#absensi-table').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            "columnDefs": [{
                    "orderable": false,
                    "targets": [6]
                } // Kolom aksi tidak bisa diurutkan
            ],
            "order": [
                [1, 'desc'],
                [2, 'desc']
            ] // Urutkan berdasarkan tanggal (kolom 2) dan jam masuk (kolom 3)
        });

        // Konfirmasi hapus
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data absensi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>