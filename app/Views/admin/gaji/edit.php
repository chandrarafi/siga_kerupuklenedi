<?php
// File: app/Views/admin/gaji/edit.php
// Halaman edit gaji untuk admin
?>

<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Edit Data Gaji</h6>
        <div class="dropdown no-arrow">
            <a href="<?= site_url('admin/gaji') ?>" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form id="gajiForm" action="<?= site_url('admin/gaji/update/' . $gaji['idgaji']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Data Gaji</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">ID Gaji</label>
                                <input type="text" class="form-control" value="<?= $gaji['idgaji'] ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. Slip</label>
                                <input type="text" class="form-control" value="<?= $gaji['noslip'] ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pegawai</label>
                                <input type="text" class="form-control" value="<?= $gaji['namapegawai'] ?> (<?= $gaji['nik'] ?>)" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Periode</label>
                                <input type="text" class="form-control" value="<?= $gaji['periode'] ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($gaji['tanggal'])) ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Komponen Gaji</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Total Absensi</label>
                                <input type="text" class="form-control" value="<?= $gaji['totalabsen'] ?> hari" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Total Lembur</label>
                                <input type="text" class="form-control" value="<?= $gaji['totallembur'] ?> jam" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Potongan</label>
                                <input type="text" class="form-control" value="Rp <?= number_format($gaji['potongan'], 0, ',', '.') ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Gaji Bersih</label>
                                <input type="text" class="form-control" value="Rp <?= number_format($gaji['gajibersih'], 0, ',', '.') ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Informasi Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metodepembayaran" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select id="metodepembayaran" name="metodepembayaran" class="form-select <?= (session()->has('errors') && isset(session('errors')['metodepembayaran'])) ? 'is-invalid' : '' ?>" required>
                                    <option value="Transfer Bank" <?= old('metodepembayaran', $gaji['metodepembayaran']) == 'Transfer Bank' ? 'selected' : '' ?>>Transfer Bank</option>
                                    <option value="Tunai" <?= old('metodepembayaran', $gaji['metodepembayaran']) == 'Tunai' ? 'selected' : '' ?>>Tunai</option>
                                    <option value="Cek" <?= old('metodepembayaran', $gaji['metodepembayaran']) == 'Cek' ? 'selected' : '' ?>>Cek</option>
                                </select>
                                <?php if (session()->has('errors') && isset(session('errors')['metodepembayaran'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['metodepembayaran'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select <?= (session()->has('errors') && isset(session('errors')['status'])) ? 'is-invalid' : '' ?>" required>
                                    <option value="pending" <?= old('status', $gaji['status']) == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="paid" <?= old('status', $gaji['status']) == 'paid' ? 'selected' : '' ?>>Paid</option>
                                    <option value="cancelled" <?= old('status', $gaji['status']) == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <?php if (session()->has('errors') && isset(session('errors')['status'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['status'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea id="keterangan" name="keterangan" class="form-control <?= (session()->has('errors') && isset(session('errors')['keterangan'])) ? 'is-invalid' : '' ?>" rows="3"><?= old('keterangan', $gaji['keterangan']) ?></textarea>
                                <?php if (session()->has('errors') && isset(session('errors')['keterangan'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['keterangan'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= site_url('admin/gaji') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>