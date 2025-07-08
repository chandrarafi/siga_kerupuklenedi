<?php
// File: app/Views/admin/izin/index.php
// Halaman daftar izin untuk admin
?>

<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Daftar Pengajuan Izin</h6>
        <div class="dropdown no-arrow">
            <a href="<?= site_url('admin/izin/report') ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-bar-chart-fill me-1"></i> Laporan
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Filter -->
        <div class="bg-light p-3 rounded mb-4">
            <form action="<?= site_url('admin/izin') ?>" method="get" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="3" <?= $status === '3' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Disetujui</option>
                            <option value="2" <?= $status === '2' ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari</label>
                        <input type="text" id="search" name="search" value="<?= $search ?>" placeholder="ID Izin / Nama Pegawai" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                            <a href="<?= site_url('admin/izin') ?>" class="btn btn-secondary" id="resetFilter">
                                <i class="bi bi-x-circle me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($izin_list)) : ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> Tidak ada data pengajuan izin yang ditemukan.
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
                                    <?php if (!empty($izin['selected_dates'])): ?>
                                        <?php
                                        $dates = explode(',', $izin['selected_dates']);
                                        $displayDates = [];

                                        // Tampilkan maksimal 2 tanggal
                                        for ($i = 0; $i < min(2, count($dates)); $i++) {
                                            $displayDates[] = date('d/m/Y', strtotime(trim($dates[$i])));
                                        }

                                        echo implode(', ', $displayDates);

                                        // Jika ada lebih dari 2 tanggal, tambahkan +n
                                        if (count($dates) > 2) {
                                            echo ' <span class="badge bg-secondary">+' . (count($dates) - 2) . '</span>';
                                        }
                                        ?>
                                    <?php else: ?>
                                        <?= date('d/m/Y', strtotime($izin['tanggalmulaiizin'])) ?> -
                                        <?= date('d/m/Y', strtotime($izin['tanggalselesaiizin'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($izin['selected_dates'])) {
                                        $dates = explode(',', $izin['selected_dates']);
                                        echo count($dates) . ' hari';
                                    } else {
                                        $start = new DateTime($izin['tanggalmulaiizin']);
                                        $end = new DateTime($izin['tanggalselesaiizin']);
                                        $interval = $start->diff($end);
                                        $days = $interval->days + 1; // Termasuk hari pertama dan terakhir
                                        echo $days . ' hari';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($izin['statusizin'] == 1) : ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                        </span>
                                    <?php else : ?>
                                        <?php if ($izin['statusizin'] == 2) : ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                            </span>
                                        <?php else : ?>
                                            <?php if ($izin['statusizin'] == 3) : ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock-fill me-1"></i> Menunggu
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('admin/izin/show/' . $izin['idizin']) ?>" class="btn btn-sm btn-info" title="Detail">
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
        // Initialize DataTable
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

        // Filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            const status = $('#status').val();
            const search = $('#search').val();

            let url = '<?= site_url('admin/izin') ?>';
            let params = [];

            if (status) {
                params.push(`status=${status}`);
            }

            if (search) {
                params.push(`search=${encodeURIComponent(search)}`);
            }

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            window.location.href = url;
        });

        // Reset filter button
        $('#resetFilter').on('click', function() {
            window.location.href = '<?= site_url('admin/izin') ?>';
        });
    });
</script>
<?= $this->endSection() ?>