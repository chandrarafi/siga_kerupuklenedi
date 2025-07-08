<div class="card shadow" id="reportCard">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Data Jabatan</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($filters['bagian'])): ?>
            <div class="mb-3">
                <h6>Filter yang digunakan:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-info">Bagian: <?= $bagian_name ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Kode Jabatan</th>
                        <th class="text-center">Nama Jabatan</th>
                        <th class="text-center">Gaji Pokok</th>
                        <th class="text-center">Tunjangan</th>
                        <th class="text-center">Bagian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jabatan)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1;
                        foreach ($jabatan as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= $row['idjabatan'] ?></td>
                                <td><?= $row['namajabatan'] ?></td>
                                <td class="text-end">Rp <?= number_format($row['gajipokok'], 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($row['tunjangan'], 0, ',', '.') ?></td>
                                <td><?= $row['namabagian'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>