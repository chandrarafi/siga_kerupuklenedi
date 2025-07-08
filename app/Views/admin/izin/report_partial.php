<div class="card shadow" id="reportCard">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Data Izin</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($filters['jenis']) || !empty($filters['status']) || !empty($filters['tanggal_awal']) || !empty($filters['tanggal_akhir'])): ?>
            <div class="mb-3">
                <h6>Filter yang digunakan:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($filters['jenis'])): ?>
                        <span class="badge bg-info">Jenis Izin: <?= $filters['jenis'] ?></span>
                    <?php endif; ?>

                    <?php if (!empty($filters['status'])): ?>
                        <span class="badge bg-primary">Status: <?= ucfirst($filters['status']) ?></span>
                    <?php endif; ?>

                    <?php if (!empty($filters['tanggal_awal']) && !empty($filters['tanggal_akhir'])): ?>
                        <span class="badge bg-secondary">Periode: <?= date('d-m-Y', strtotime($filters['tanggal_awal'])) ?> s/d <?= date('d-m-Y', strtotime($filters['tanggal_akhir'])) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Pegawai</th>
                        <th>Nama Jabatan</th>
                        <th class="text-center">Tanggal Mulai</th>
                        <th class="text-center">Tanggal Selesai</th>
                        <th>Jenis Izin</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($izin)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1;
                        foreach ($izin as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= $row['namapegawai'] ?></td>
                                <td><?= $row['namajabatan'] ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggalmulaiizin'])) ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggalselesaiizin'])) ?></td>
                                <td><?= $row['jenisizin'] ?></td>
                                <td><?= $row['alasan'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>