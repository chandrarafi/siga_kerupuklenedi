<?= $this->extend('layouts/report') ?>

<?= $this->section('content') ?>
<table class="report-table">
    <thead>
        <tr>
            <th rowspan="2" style="width: 5%;">No</th>
            <th rowspan="2" style="width: 15%;">Kode Pegawai</th>
            <th rowspan="2" style="width: 20%;">Nama Pegawai</th>
            <th rowspan="2" style="width: 15%;">NIK</th>
            <th rowspan="2" style="width: 15%;">Jabatan</th>
            <th rowspan="2" style="width: 15%;">Bagian</th>
            <th rowspan="2" style="width: 15%;">NoHP</th>
        </tr>
        <tr>
            <!-- Baris kosong untuk rowspan -->
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($pegawai)): ?>
            <?php $no = 1; ?>
            <?php foreach ($pegawai as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['idpegawai'] ?></td>
                    <td><?= $row['namapegawai'] ?></td>
                    <td><?= $row['nik'] ?? '-' ?></td>
                    <td><?= $row['namajabatan'] ?></td>
                    <td><?= $row['namabagian'] ?></td>
                    <td><?= $row['nohp'] ?? '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data pegawai</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>