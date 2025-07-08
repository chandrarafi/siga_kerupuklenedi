<div class="card shadow" id="reportCard">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Data Pegawai</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($filters['bagian']) || !empty($filters['jabatan']) || !empty($filters['jenkel'])): ?>
            <div class="mb-3">
                <h6>Filter yang digunakan:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($filters['bagian'])): ?>
                        <span class="badge bg-info">Bagian: <?= $bagian_name ?></span>
                    <?php endif; ?>

                    <?php if (!empty($filters['jabatan'])): ?>
                        <span class="badge bg-primary">Jabatan: <?= $jabatan_name ?></span>
                    <?php endif; ?>

                    <?php if (!empty($filters['jenkel'])): ?>
                        <span class="badge bg-secondary">Jenis Kelamin: <?= $filters['jenkel'] ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Pegawai</th>
                        <th>Nama Pegawai</th>
                        <th>NIK</th>
                        <th>Jabatan</th>
                        <th>Bagian</th>
                        <th>NoHP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pegawai)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1;
                        foreach ($pegawai as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['idpegawai'] ?></td>
                                <td><?= $row['namapegawai'] ?></td>
                                <td><?= $row['nik'] ?: '-' ?></td>
                                <td><?= $row['namajabatan'] ?></td>
                                <td><?= $row['namabagian'] ?></td>
                                <td><?= $row['nohp'] ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>