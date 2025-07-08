<?php
// File: app/Views/admin/izin/report.php
// Halaman laporan izin untuk admin
?>

<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Laporan Pengajuan Izin</h6>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/izin') ?>" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="<?= site_url('admin/izin/export?' . http_build_query($_GET)) ?>" class="btn btn-sm btn-success">
                <i class="bi bi-file-excel-fill me-1"></i> Export Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <div class="bg-light p-3 rounded mb-4">
            <form action="<?= site_url('admin/izin/report') ?>" method="get">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" value="<?= $filter['start_date'] ?>" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" id="end_date" name="end_date" value="<?= $filter['end_date'] ?>" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="1" <?= $filter['status'] === '1' ? 'selected' : '' ?>>Disetujui</option>
                            <option value="0" <?= $filter['status'] === '0' ? 'selected' : '' ?>>Belum Disetujui</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="pegawai_id" class="form-label">Pegawai</label>
                        <select id="pegawai_id" name="pegawai_id" class="form-select">
                            <option value="">Semua Pegawai</option>
                            <?php foreach ($pegawai_list as $pegawai) : ?>
                                <option value="<?= $pegawai['idpegawai'] ?>" <?= $filter['pegawai_id'] === $pegawai['idpegawai'] ? 'selected' : '' ?>>
                                    <?= $pegawai['namapegawai'] ?> (<?= $pegawai['idpegawai'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <a href="<?= site_url('admin/izin/report') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pengajuan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($izin_list) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-calendar-check fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Disetujui</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $approved = array_filter($izin_list, function ($izin) {
                                        return $izin['statusizin'] == true;
                                    });
                                    echo count($approved);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Menunggu/Ditolak</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $pending = array_filter($izin_list, function ($izin) {
                                        return $izin['statusizin'] == false;
                                    });
                                    echo count($pending);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-clock fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <?php if (empty($izin_list)) : ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> Tidak ada data izin yang sesuai dengan filter.
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Izin</th>
                            <th>Pegawai</th>
                            <th>NIK</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($izin_list as $izin) : ?>
                            <tr>
                                <td><?= $izin['idizin'] ?></td>
                                <td><?= $izin['namapegawai'] ?></td>
                                <td><?= $izin['nik'] ?></td>
                                <td><?= $izin['jenisizin'] ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($izin['tanggalmulaiizin'])) ?> -
                                    <?= date('d/m/Y', strtotime($izin['tanggalselesaiizin'])) ?>
                                </td>
                                <td>
                                    <?php
                                    $start = new DateTime($izin['tanggalmulaiizin']);
                                    $end = new DateTime($izin['tanggalselesaiizin']);
                                    $interval = $start->diff($end);
                                    $days = $interval->days + 1; // Termasuk hari pertama dan terakhir
                                    echo $days . ' hari';
                                    ?>
                                </td>
                                <td>
                                    <?php if ($izin['statusizin']) : ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                        </span>
                                    <?php else : ?>
                                        <?php if (!empty($izin['keterangan_admin'])) : ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                            </span>
                                        <?php else : ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock-fill me-1"></i> Menunggu
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('admin/izin/show/' . $izin['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ data keseluruhan)",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                emptyTable: "Tidak ada data yang tersedia",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Terakhir"
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>