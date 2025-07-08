<?php
// File: app/Views/admin/lembur/show.php
// Halaman detail lembur untuk admin
?>

<?php if (isset($ajax) && $ajax): ?>
    <!-- Tampilan untuk AJAX modal -->
    <div class="p-3">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Informasi Lembur</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%" class="text-muted">ID Lembur</td>
                        <td class="fw-bold"><?= $lembur['idlembur'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td class="fw-bold"><?= date('d F Y', strtotime($lembur['tanggallembur'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jam Mulai</td>
                        <td class="fw-bold"><?= date('H:i', strtotime($lembur['jammulai'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jam Selesai</td>
                        <td class="fw-bold"><?= date('H:i', strtotime($lembur['jamselesai'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Durasi</td>
                        <td class="fw-bold"><?= $lembur['durasi_format'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Input</td>
                        <td class="fw-bold"><?= date('d F Y H:i', strtotime($lembur['created_at'])) ?></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Informasi Pegawai</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%" class="text-muted">Nama</td>
                        <td class="fw-bold"><?= $lembur['namapegawai'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIK</td>
                        <td class="fw-bold"><?= $lembur['nik'] ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">ID Pegawai</td>
                        <td class="fw-bold"><?= $lembur['pegawai_id'] ?></td>
                    </tr>
                </table>
            </div>

            <div class="col-12">
                <h6 class="fw-bold mb-3">Alasan</h6>
                <p><?= nl2br($lembur['alasan']) ?></p>
            </div>

            <div class="col-12 mt-3">
                <div class="d-flex justify-content-between">
                    <a href="<?= site_url('admin/lembur/edit/' . $lembur['idlembur']) ?>" target="_blank" class="btn btn-warning">
                        <i class="bi bi-pencil-fill me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete('<?= $lembur['idlembur'] ?>')">
                        <i class="bi bi-trash-fill me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>

        <script>
            function confirmDelete(id) {
                if (confirm('Apakah Anda yakin ingin menghapus data lembur ini?')) {
                    window.parent.location.href = '<?= site_url('admin/lembur/delete/') ?>' + id;
                }
            }
        </script>
    </div>
<?php else: ?>
    <!-- Tampilan halaman utuh -->
    <?= $this->extend('admin/layouts/main') ?>

    <?= $this->section('content') ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">Detail Lembur</h6>
            <a href="<?= site_url('admin/lembur') ?>" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
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

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header py-3 bg-light">
                            <h6 class="m-0 font-weight-bold">Informasi Lembur</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">ID Lembur</label>
                                <p class="font-weight-bold"><?= $lembur['idlembur'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal</label>
                                <p class="font-weight-bold"><?= date('d F Y', strtotime($lembur['tanggallembur'])) ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Jam Mulai</label>
                                <p class="font-weight-bold"><?= date('H:i', strtotime($lembur['jammulai'])) ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Jam Selesai</label>
                                <p class="font-weight-bold"><?= date('H:i', strtotime($lembur['jamselesai'])) ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Durasi</label>
                                <p class="font-weight-bold"><?= $lembur['durasi_format'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Input</label>
                                <p class="font-weight-bold"><?= date('d F Y H:i', strtotime($lembur['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card mb-4 h-100">
                        <div class="card-header py-3 bg-light">
                            <h6 class="m-0 font-weight-bold">Informasi Pegawai</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">ID Pegawai</label>
                                <p class="font-weight-bold"><?= $lembur['pegawai_id'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Nama Pegawai</label>
                                <p class="font-weight-bold"><?= $lembur['namapegawai'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">NIK</label>
                                <p class="font-weight-bold"><?= $lembur['nik'] ?? '-' ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header py-3 bg-light">
                            <h6 class="m-0 font-weight-bold">Alasan Lembur</h6>
                        </div>
                        <div class="card-body">
                            <p class="font-weight-bold"><?= nl2br($lembur['alasan']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('admin/lembur/edit/' . $lembur['idlembur']) ?>" class="btn btn-warning">
                            <i class="bi bi-pencil-fill me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash-fill me-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
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
                    <p>Apakah Anda yakin ingin menghapus data lembur ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="<?= site_url('admin/lembur/delete/' . $lembur['idlembur']) ?>" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>
    <?= $this->endSection() ?>
<?php endif; ?>