<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Data Absensi</h5>
                <a href="<?= site_url('absensi') ?>" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="<?= site_url('absensi/update/' . $absensi['idabsensi']) ?>" method="post" id="formAbsensi">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="idpegawai" class="form-label">Pegawai</label>
                            <select class="form-select <?= session('errors.idpegawai') ? 'is-invalid' : '' ?>" id="idpegawai" name="idpegawai" required>
                                <option value="">Pilih Pegawai</option>
                                <?php foreach ($pegawai as $p) : ?>
                                    <option value="<?= $p['idpegawai'] ?>" <?= $absensi['idpegawai'] == $p['idpegawai'] ? 'selected' : '' ?>><?= $p['namapegawai'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.idpegawai')) : ?>
                                <div class="invalid-feedback"><?= session('errors.idpegawai') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control <?= session('errors.tanggal') ? 'is-invalid' : '' ?>" id="tanggal" name="tanggal" value="<?= old('tanggal', $absensi['tanggal']) ?>" required>
                            <?php if (session('errors.tanggal')) : ?>
                                <div class="invalid-feedback"><?= session('errors.tanggal') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jammasuk" class="form-label">Jam Masuk</label>
                            <input type="time" class="form-control <?= session('errors.jammasuk') ? 'is-invalid' : '' ?>" id="jammasuk" name="jammasuk" value="<?= old('jammasuk', $absensi['jammasuk']) ?>">
                            <?php if (session('errors.jammasuk')) : ?>
                                <div class="invalid-feedback"><?= session('errors.jammasuk') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="jamkeluar" class="form-label">Jam Keluar</label>
                            <input type="time" class="form-control <?= session('errors.jamkeluar') ? 'is-invalid' : '' ?>" id="jamkeluar" name="jamkeluar" value="<?= old('jamkeluar', $absensi['jamkeluar']) ?>">
                            <?php if (session('errors.jamkeluar')) : ?>
                                <div class="invalid-feedback"><?= session('errors.jamkeluar') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select <?= session('errors.status') ? 'is-invalid' : '' ?>" id="status" name="status" required>
                                <option value="hadir" <?= $absensi['status'] == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="sakit" <?= $absensi['status'] == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                <option value="izin" <?= $absensi['status'] == 'izin' ? 'selected' : '' ?>>Izin</option>
                                <option value="alpa" <?= $absensi['status'] == 'alpa' ? 'selected' : '' ?>>Alpa</option>
                            </select>
                            <?php if (session('errors.status')) : ?>
                                <div class="invalid-feedback"><?= session('errors.status') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control <?= session('errors.keterangan') ? 'is-invalid' : '' ?>" id="keterangan" name="keterangan" rows="3"><?= old('keterangan', $absensi['keterangan']) ?></textarea>
                            <?php if (session('errors.keterangan')) : ?>
                                <div class="invalid-feedback"><?= session('errors.keterangan') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Longitude Masuk</label>
                            <input type="text" class="form-control <?= session('errors.longitude_masuk') ? 'is-invalid' : '' ?>" id="longitude" name="longitude" value="<?= old('longitude', $absensi['longitude_masuk']) ?>">
                            <?php if (session('errors.longitude_masuk')) : ?>
                                <div class="invalid-feedback"><?= session('errors.longitude_masuk') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Latitude Masuk</label>
                            <input type="text" class="form-control <?= session('errors.latitude_masuk') ? 'is-invalid' : '' ?>" id="latitude" name="latitude" value="<?= old('latitude', $absensi['latitude_masuk']) ?>">
                            <?php if (session('errors.latitude_masuk')) : ?>
                                <div class="invalid-feedback"><?= session('errors.latitude_masuk') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                            <a href="<?= site_url('absensi') ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Fungsi untuk mengubah status dan mengatur field yang diperlukan
        $('#status').change(function() {
            const status = $(this).val();

            if (status === 'hadir') {
                $('#jammasuk').prop('required', true);
                $('#jammasuk').closest('.col-md-6').show();
                $('#jamkeluar').closest('.col-md-6').show();
            } else {
                $('#jammasuk').prop('required', false);
                $('#jammasuk').val('');
                $('#jamkeluar').val('');
                $('#jammasuk').closest('.col-md-6').hide();
                $('#jamkeluar').closest('.col-md-6').hide();
            }
        });

        // Trigger change event pada load
        $('#status').trigger('change');
    });
</script>
<?= $this->endSection() ?>