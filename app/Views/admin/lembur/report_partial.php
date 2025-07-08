<div class="card shadow" id="reportCard">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Lembur</h5>
        <div>
            <span class="badge bg-light text-dark">
                Total Data: <?= count($lembur) ?>
            </span>
        </div>
    </div>
    <div class="card-body" id="reportContent">
        <?php if (empty($lembur)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Tidak ada data lembur yang ditemukan dengan filter yang dipilih.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Pegawai</th>
                            <th>Nama Jabatan</th>
                            <th>Tanggal</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($lembur as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= $row['namapegawai'] ?></td>
                                <td><?= $row['namajabatan'] ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggallembur'])) ?></td>
                                <td><?= date('H:i', strtotime($row['jammulai'])) ?></td>
                                <td><?= date('H:i', strtotime($row['jamselesai'])) ?></td>
                                <td><?= $row['alasan'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <div class="alert alert-light border">
                    <h6 class="mb-3">Informasi Filter:</h6>
                    <div class="row">
                        <?php if (!empty($filters['tanggal_awal']) && !empty($filters['tanggal_akhir'])): ?>
                            <div class="col-md-6 mb-2">
                                <strong>Periode:</strong> <?= date('d-m-Y', strtotime($filters['tanggal_awal'])) ?> s/d <?= date('d-m-Y', strtotime($filters['tanggal_akhir'])) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($filters['pegawai'])): ?>
                            <div class="col-md-6 mb-2">
                                <strong>Pegawai:</strong> <?= $pegawai_name ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>