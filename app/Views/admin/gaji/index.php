<?php
// File: app/Views/admin/gaji/index.php
// Halaman daftar gaji untuk admin
?>

<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Daftar Gaji</h6>
        <div class="dropdown no-arrow">
            <a href="<?= site_url('admin/gaji/create') ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Gaji
            </a>
            <a href="<?= site_url('admin/gaji/report') ?>" class="btn btn-sm btn-info">
                <i class="bi bi-bar-chart-fill me-1"></i> Laporan
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Filter -->
        <div class="bg-light p-3 rounded mb-4">
            <form id="filterForm" action="<?= site_url('admin/gaji') ?>" method="get">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2 col-6">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select id="bulan" name="bulan" class="form-select">
                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?= $filter['bulan'] == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-6">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select id="tahun" name="tahun" class="form-select">
                            <?php
                            $currentYear = date('Y');
                            for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) :
                            ?>
                                <option value="<?= $i ?>" <?= $filter['tahun'] == $i ? 'selected' : '' ?>>
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label for="pegawai_id" class="form-label">Pegawai</label>
                        <select id="pegawai_id" name="pegawai_id" class="form-select">
                            <option value="">Semua Pegawai</option>
                            <?php foreach ($pegawai_list as $pegawai) : ?>
                                <option value="<?= $pegawai['idpegawai'] ?>" <?= $filter['pegawai_id'] == $pegawai['idpegawai'] ? 'selected' : '' ?>>
                                    <?= $pegawai['namapegawai'] ?> (<?= $pegawai['nik'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" <?= $filter['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="paid" <?= $filter['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="cancelled" <?= $filter['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label for="search" class="form-label">Cari</label>
                        <div class="input-group">
                            <input type="text" id="search" name="search" value="<?= $filter['search'] ?>" placeholder="ID / Nama Pegawai" class="form-control">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 col-12 mt-3">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= site_url('admin/gaji') ?>" class="btn btn-secondary" id="resetFilter">
                                <i class="bi bi-x-circle me-1"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($gaji_list)) : ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> Tidak ada data gaji yang ditemukan.
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="gajiTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Gaji</th>
                            <th>No. Slip</th>
                            <th>Pegawai</th>
                            <th>Periode</th>
                            <th>Tanggal</th>
                            <th>Total Absen</th>
                            <th>Total Lembur</th>
                            <th>Gaji Bersih</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gaji_list as $gaji) : ?>
                            <tr>
                                <td><?= $gaji['idgaji'] ?></td>
                                <td><?= $gaji['noslip'] ?></td>
                                <td><?= $gaji['namapegawai'] ?></td>
                                <td><?= $gaji['periode'] ?></td>
                                <td><?= date('d/m/Y', strtotime($gaji['tanggal'])) ?></td>
                                <td><?= $gaji['totalabsen'] ?> hari</td>
                                <td><?= $gaji['totallembur'] ?> jam</td>
                                <td>Rp <?= number_format($gaji['gajibersih'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($gaji['status'] == 'pending') : ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif ($gaji['status'] == 'paid') : ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php elseif ($gaji['status'] == 'cancelled') : ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/gaji/show/' . $gaji['idgaji']) ?>" class="btn btn-sm btn-info" title="Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="<?= site_url('admin/gaji/slip/' . $gaji['idgaji']) ?>" class="btn btn-sm btn-primary" title="Slip Gaji" target="_blank">
                                            <i class="bi bi-file-earmark-text-fill"></i>
                                        </a>
                                        <a href="<?= site_url('admin/gaji/edit/' . $gaji['idgaji']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <?php if ($gaji['status'] == 'pending') : ?>
                                            <button type="button" class="btn btn-sm btn-success btn-process" data-id="<?= $gaji['idgaji'] ?>" title="Proses Pembayaran">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($gaji['status'] == 'pending') : ?>
                                            <button type="button" class="btn btn-sm btn-danger btn-cancel" data-id="<?= $gaji['idgaji'] ?>" title="Batalkan">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $gaji['idgaji'] ?>" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data gaji ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Proses -->
<div class="modal fade" id="processModal" tabindex="-1" aria-labelledby="processModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processModalLabel">Konfirmasi Proses Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memproses pembayaran gaji ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="processLink" class="btn btn-success">Proses</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pembatalan -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Konfirmasi Pembatalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membatalkan gaji ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="cancelLink" class="btn btn-danger">Batalkan</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= session()->getFlashdata('success') ?>',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= session()->getFlashdata('error') ?>'
            });
        <?php endif; ?>

        // Inisialisasi DataTable
        $('#gajiTable').DataTable({
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

        // Event handler untuk tombol hapus
        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            confirmDelete(id);
        });

        // Event handler untuk tombol proses
        $(document).on('click', '.btn-process', function() {
            const id = $(this).data('id');
            confirmProcess(id);
        });

        // Event handler untuk tombol batal
        $(document).on('click', '.btn-cancel', function() {
            const id = $(this).data('id');
            confirmCancel(id);
        });

        // Fungsi konfirmasi hapus dengan SweetAlert
        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data gaji ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form untuk mengirim permintaan POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `<?= site_url('admin/gaji/delete/') ?>${id}`;
                    // Tambahkan CSRF token
                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '<?= csrf_token() ?>';
                    csrfField.value = '<?= csrf_hash() ?>';
                    form.appendChild(csrfField);
                    // Tambahkan form ke body dan submit
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        };

        // Fungsi konfirmasi proses pembayaran dengan SweetAlert
        window.confirmProcess = function(id) {
            Swal.fire({
                title: 'Konfirmasi Proses',
                text: 'Apakah Anda yakin ingin memproses pembayaran gaji ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form untuk mengirim permintaan POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `<?= site_url('admin/gaji/process-payment/') ?>${id}`;
                    // Tambahkan CSRF token
                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '<?= csrf_token() ?>';
                    csrfField.value = '<?= csrf_hash() ?>';
                    form.appendChild(csrfField);
                    // Tambahkan form ke body dan submit
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        };

        // Fungsi konfirmasi pembatalan dengan SweetAlert
        window.confirmCancel = function(id) {
            Swal.fire({
                title: 'Konfirmasi Pembatalan',
                text: 'Apakah Anda yakin ingin membatalkan gaji ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form untuk mengirim permintaan POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `<?= site_url('admin/gaji/cancel-payment/') ?>${id}`;
                    // Tambahkan CSRF token
                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '<?= csrf_token() ?>';
                    csrfField.value = '<?= csrf_hash() ?>';
                    form.appendChild(csrfField);
                    // Tambahkan form ke body dan submit
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        };

        // Reset filter button
        $('#resetFilter').on('click', function(e) {
            e.preventDefault();
            window.location.href = '<?= site_url('admin/gaji') ?>';
        });
    });
</script>
<?= $this->endSection() ?>