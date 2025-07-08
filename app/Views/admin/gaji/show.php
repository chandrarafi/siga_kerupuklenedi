<?php
// File: app/Views/admin/gaji/show.php
// Halaman detail gaji untuk admin
?>

<?php if (isset($ajax) && $ajax) : ?>
    <!-- Tampilan untuk modal -->
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <h6 class="fw-bold">ID Gaji</h6>
                <p><?= $gaji['idgaji'] ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">No. Slip</h6>
                <p><?= $gaji['noslip'] ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Nama Pegawai</h6>
                <p><?= $pegawai['namapegawai'] ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">NIK</h6>
                <p><?= $pegawai['nik'] ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Jabatan</h6>
                <p><?= $pegawai['nama_jabatan'] ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Bagian</h6>
                <p><?= $pegawai['namabagian'] ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <h6 class="fw-bold">Periode</h6>
                <p><?= $gaji['periode'] ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Tanggal</h6>
                <p><?= date('d/m/Y', strtotime($gaji['tanggal'])) ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Status</h6>
                <p>
                    <?php if ($gaji['status'] == 'pending') : ?>
                        <span class="badge bg-warning">Pending</span>
                    <?php elseif ($gaji['status'] == 'paid') : ?>
                        <span class="badge bg-success">Paid</span>
                    <?php elseif ($gaji['status'] == 'cancelled') : ?>
                        <span class="badge bg-danger">Cancelled</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Metode Pembayaran</h6>
                <p><?= $gaji['metodepembayaran'] ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Keterangan</h6>
                <p><?= $gaji['keterangan'] ?: '-' ?></p>
            </div>
        </div>
    </div>

    <hr>

    <h5 class="mb-3">Komponen Gaji</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <h6 class="fw-bold">Gaji Pokok</h6>
                <p>Rp <?= number_format($komponen_gaji['gaji_pokok'], 0, ',', '.') ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Tunjangan</h6>
                <p>Rp <?= number_format($komponen_gaji['tunjangan'], 0, ',', '.') ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Upah Lembur</h6>
                <p>Rp <?= number_format($komponen_gaji['upah_lembur'], 0, ',', '.') ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <h6 class="fw-bold">Gaji Bersih</h6>
                <p class="fw-bold">Rp <?= number_format($komponen_gaji['gaji_bersih'], 0, ',', '.') ?></p>
            </div>
        </div>
    </div>

    <hr>

    <h5 class="mb-3">Detail Perhitungan</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <h6 class="fw-bold">Total Absensi</h6>
                <p><?= $detail['total_absensi'] ?> hari</p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Total Lembur</h6>
                <p><?= $detail['total_lembur'] ?> jam</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <h6 class="fw-bold">Tunjangan Penuh</h6>
                <p>Rp <?= number_format($detail['tunjangan_penuh'], 0, ',', '.') ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Tunjangan per Hari</h6>
                <p>Rp <?= number_format($detail['tunjangan_per_hari'], 0, ',', '.') ?></p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Tarif Lembur</h6>
                <p>Rp <?= number_format($detail['tarif_lembur'], 0, ',', '.') ?> per jam</p>
            </div>
        </div>
    </div>

    <hr>

    <h5 class="mb-3">Rekap Absensi</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <h6 class="fw-bold">Hadir</h6>
                <p><?= $rekap_absensi['hadir'] ?> hari</p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Izin</h6>
                <p><?= $rekap_absensi['izin'] ?> hari</p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Sakit</h6>
                <p><?= $rekap_absensi['sakit'] ?> hari</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <h6 class="fw-bold">Cuti</h6>
                <p><?= $rekap_absensi['cuti'] ?> hari</p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Alpha</h6>
                <p><?= $rekap_absensi['alpha'] ?> hari</p>
            </div>
            <div class="mb-3">
                <h6 class="fw-bold">Total Terlambat</h6>
                <p><?= $rekap_absensi['total_terlambat'] ?> menit</p>
            </div>
        </div>
    </div>

    <hr>

    <h5 class="mb-3">Detail Absensi</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
                    <th>Terlambat</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($detail_absensi)) : ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data absensi</td>
                    </tr>
                <?php else : ?>
                    <?php $no = 1;
                    foreach ($detail_absensi as $absen) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($absen['tanggal'])) ?></td>
                            <td><?= $absen['jammasuk'] ? date('H:i', strtotime($absen['jammasuk'])) : '-' ?></td>
                            <td><?= $absen['jamkeluar'] ? date('H:i', strtotime($absen['jamkeluar'])) : '-' ?></td>
                            <td>
                                <?php if ($absen['status'] == 'hadir') : ?>
                                    <span class="badge bg-success">Hadir</span>
                                <?php elseif ($absen['status'] == 'izin') : ?>
                                    <span class="badge bg-warning">Izin</span>
                                <?php elseif ($absen['status'] == 'sakit') : ?>
                                    <span class="badge bg-info">Sakit</span>
                                <?php elseif ($absen['status'] == 'cuti') : ?>
                                    <span class="badge bg-primary">Cuti</span>
                                <?php elseif ($absen['status'] == 'alpha') : ?>
                                    <span class="badge bg-danger">Alpha</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $absen['terlambat'] ? $absen['terlambat'] . ' menit' : '-' ?></td>
                            <td><?= $absen['keterangan'] ?: '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr>

    <h5 class="mb-3">Detail Lembur</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Durasi (Jam)</th>
                    <th>Upah Lembur</th>
                    <th>Alasan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($detail_lembur)) : ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data lembur</td>
                    </tr>
                <?php else : ?>
                    <?php $no = 1;
                    foreach ($detail_lembur as $lembur) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($lembur['tanggallembur'])) ?></td>
                            <td><?= date('H:i', strtotime($lembur['jammulai'])) ?></td>
                            <td><?= date('H:i', strtotime($lembur['jamselesai'])) ?></td>
                            <td><?= number_format($lembur['durasi_jam'], 2, ',', '.') ?></td>
                            <td>Rp <?= number_format($lembur['upah_lembur'], 0, ',', '.') ?></td>
                            <td><?= $lembur['alasan'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-primary">
                        <td colspan="4" class="text-end fw-bold">Total</td>
                        <td class="fw-bold"><?= number_format($total_jam_lembur, 2, ',', '.') ?></td>
                        <td class="fw-bold">Rp <?= number_format($total_upah_lembur, 0, ',', '.') ?></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3 text-center">
        <a href="<?= site_url('admin/gaji/slip/' . $gaji['idgaji']) ?>" target="_blank" class="btn btn-primary">
            <i class="bi bi-file-earmark-text-fill me-1"></i> Lihat Slip Gaji
        </a>
    </div>
<?php else : ?>
    <!-- Tampilan halaman penuh -->
    <?= $this->extend('admin/layouts/main') ?>

    <?= $this->section('content') ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">Detail Gaji</h6>
            <div class="dropdown no-arrow">
                <a href="<?= site_url('admin/gaji') ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="fw-bold">ID Gaji</h6>
                        <p><?= $gaji['idgaji'] ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">No. Slip</h6>
                        <p><?= $gaji['noslip'] ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Nama Pegawai</h6>
                        <p><?= $pegawai['namapegawai'] ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">NIK</h6>
                        <p><?= $pegawai['nik'] ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Jabatan</h6>
                        <p><?= $pegawai['nama_jabatan'] ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Bagian</h6>
                        <p><?= $pegawai['namabagian'] ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="fw-bold">Periode</h6>
                        <p><?= $gaji['periode'] ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Tanggal</h6>
                        <p><?= date('d/m/Y', strtotime($gaji['tanggal'])) ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Status</h6>
                        <p>
                            <?php if ($gaji['status'] == 'pending') : ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php elseif ($gaji['status'] == 'paid') : ?>
                                <span class="badge bg-success">Paid</span>
                            <?php elseif ($gaji['status'] == 'cancelled') : ?>
                                <span class="badge bg-danger">Cancelled</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Metode Pembayaran</h6>
                        <p><?= $gaji['metodepembayaran'] ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Keterangan</h6>
                        <p><?= $gaji['keterangan'] ?: '-' ?></p>
                    </div>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Komponen Gaji</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="fw-bold">Gaji Pokok</h6>
                        <p>Rp <?= number_format($komponen_gaji['gaji_pokok'], 0, ',', '.') ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Tunjangan</h6>
                        <p>Rp <?= number_format($komponen_gaji['tunjangan'], 0, ',', '.') ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Upah Lembur</h6>
                        <p>Rp <?= number_format($komponen_gaji['upah_lembur'], 0, ',', '.') ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="fw-bold">Gaji Bersih</h6>
                        <p class="fw-bold">Rp <?= number_format($komponen_gaji['gaji_bersih'], 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Detail Perhitungan</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="fw-bold">Total Absensi</h6>
                        <p><?= $detail['total_absensi'] ?> hari</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Total Lembur</h6>
                        <p><?= $detail['total_lembur'] ?> jam</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="fw-bold">Tunjangan Penuh</h6>
                        <p>Rp <?= number_format($detail['tunjangan_penuh'], 0, ',', '.') ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Tunjangan per Hari</h6>
                        <p>Rp <?= number_format($detail['tunjangan_per_hari'], 0, ',', '.') ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Tarif Lembur</h6>
                        <p>Rp <?= number_format($detail['tarif_lembur'], 0, ',', '.') ?> per jam</p>
                    </div>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Rekap Absensi</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="fw-bold">Hadir</h6>
                        <p><?= $rekap_absensi['hadir'] ?> hari</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Izin</h6>
                        <p><?= $rekap_absensi['izin'] ?> hari</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Sakit</h6>
                        <p><?= $rekap_absensi['sakit'] ?> hari</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="fw-bold">Cuti</h6>
                        <p><?= $rekap_absensi['cuti'] ?> hari</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Alpha</h6>
                        <p><?= $rekap_absensi['alpha'] ?> hari</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Total Terlambat</h6>
                        <p><?= $rekap_absensi['total_terlambat'] ?> menit</p>
                    </div>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Detail Absensi</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Terlambat</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($detail_absensi)) : ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data absensi</td>
                            </tr>
                        <?php else : ?>
                            <?php $no = 1;
                            foreach ($detail_absensi as $absen) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($absen['tanggal'])) ?></td>
                                    <td><?= $absen['jammasuk'] ? date('H:i', strtotime($absen['jammasuk'])) : '-' ?></td>
                                    <td><?= $absen['jamkeluar'] ? date('H:i', strtotime($absen['jamkeluar'])) : '-' ?></td>
                                    <td>
                                        <?php if ($absen['status'] == 'hadir') : ?>
                                            <span class="badge bg-success">Hadir</span>
                                        <?php elseif ($absen['status'] == 'izin') : ?>
                                            <span class="badge bg-warning">Izin</span>
                                        <?php elseif ($absen['status'] == 'sakit') : ?>
                                            <span class="badge bg-info">Sakit</span>
                                        <?php elseif ($absen['status'] == 'cuti') : ?>
                                            <span class="badge bg-primary">Cuti</span>
                                        <?php elseif ($absen['status'] == 'alpha') : ?>
                                            <span class="badge bg-danger">Alpha</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $absen['terlambat'] ? $absen['terlambat'] . ' menit' : '-' ?></td>
                                    <td><?= $absen['keterangan'] ?: '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>

            <h5 class="mb-3">Detail Lembur</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Durasi (Jam)</th>
                            <th>Upah Lembur</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($detail_lembur)) : ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data lembur</td>
                            </tr>
                        <?php else : ?>
                            <?php $no = 1;
                            foreach ($detail_lembur as $lembur) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($lembur['tanggallembur'])) ?></td>
                                    <td><?= date('H:i', strtotime($lembur['jammulai'])) ?></td>
                                    <td><?= date('H:i', strtotime($lembur['jamselesai'])) ?></td>
                                    <td><?= number_format($lembur['durasi_jam'], 2, ',', '.') ?></td>
                                    <td>Rp <?= number_format($lembur['upah_lembur'], 0, ',', '.') ?></td>
                                    <td><?= $lembur['alasan'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-primary">
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td class="fw-bold"><?= number_format($total_jam_lembur, 2, ',', '.') ?></td>
                                <td class="fw-bold">Rp <?= number_format($total_upah_lembur, 0, ',', '.') ?></td>
                                <td></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= site_url('admin/gaji') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <a href="<?= site_url('admin/gaji/edit/' . $gaji['idgaji']) ?>" class="btn btn-warning">
                            <i class="bi bi-pencil-fill me-1"></i> Edit
                        </a>
                        <a href="<?= site_url('admin/gaji/slip/' . $gaji['idgaji']) ?>" target="_blank" class="btn btn-primary">
                            <i class="bi bi-file-earmark-text-fill me-1"></i> Slip Gaji
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->endSection() ?>
<?php endif; ?>