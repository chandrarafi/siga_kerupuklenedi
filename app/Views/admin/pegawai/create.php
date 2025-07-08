<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tambah Pegawai</h5>
                <a href="<?= site_url('admin/pegawai') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->has('errors')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('admin/pegawai/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="idpegawai" class="form-label">ID Pegawai</label>
                            <input type="text" class="form-control" id="idpegawai" name="idpegawai" value="<?= $idpegawai ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="jabatanid" class="form-label">Jabatan</label>
                            <select class="form-select" id="jabatanid" name="jabatanid" required>
                                <option value="" selected disabled>Pilih Jabatan</option>
                                <?php foreach ($jabatan as $row) : ?>
                                    <option value="<?= $row['idjabatan'] ?>"><?= $row['namajabatan'] ?> (<?= $row['namabagian'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="namapegawai" class="form-label">Nama Pegawai</label>
                            <input type="text" class="form-control" id="namapegawai" name="namapegawai" value="<?= old('namapegawai') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik" name="nik" value="<?= old('nik') ?>" maxlength="16">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jenkel" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="jenkel" name="jenkel" required>
                                <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" <?= old('jenkel') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= old('jenkel') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nohp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="nohp" name="nohp" value="<?= old('nohp') ?>" maxlength="15">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= old('alamat') ?></textarea>
                    </div>

                    <hr class="my-4">
                    <h5>Data Akun User</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Password minimal 6 karakter.</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
        // Auto-generate name in lowercase for username suggestion
        $('#namapegawai').on('input', function() {
            if ($('#username').val() === '') {
                let name = $(this).val().toLowerCase();
                // Replace spaces with dots and remove special characters
                name = name.replace(/\s+/g, '.').replace(/[^a-z0-9.]/g, '');
                $('#username').val(name);
            }
        });
    });
</script>
<?= $this->endSection() ?>