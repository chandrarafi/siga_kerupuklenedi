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
                            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                            <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
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
                                    <a href="javascript:void(0)" class="btn btn-sm btn-info btn-detail" data-id="<?= $izin['idizin'] ?>" title="Detail">
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

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Pengajuan Izin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
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

        // Detail Button Click
        $('.btn-detail').on('click', function() {
            const id = $(this).data('id');
            console.log('Showing detail for:', id);

            // Show modal
            $('#detailModal').modal('show');

            // Reset modal content dengan cara yang lebih aman
            $('#detailModalBody').empty();
            $('#detailModalBody').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);

            // Menggunakan jQuery AJAX sebagai pengganti fetch
            $.ajax({
                url: '<?= site_url('admin/izin/show/') ?>' + id + '?ajax=1',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    // Pastikan respons tidak kosong
                    if (response && response.trim() !== '') {
                        $('#detailModalBody').html(response);
                    } else {
                        $('#detailModalBody').html(`
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Respons kosong dari server
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching details:', error);
                    let errorMessage = 'Terjadi kesalahan saat memuat data.';

                    // Coba parse error dari respons JSON jika ada
                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    } catch (e) {
                        console.error('Error parsing JSON response:', e);
                    }

                    $('#detailModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Terjadi kesalahan saat memuat data.
                            <p class="small mt-2">${errorMessage}</p>
                        </div>
                    `);
                }
            });
        });

        // Function to close modal - exposed to iframe
        window.closeDetailModal = function() {
            $('#detailModal').modal('hide');
        };

        // Function to refresh table - exposed to iframe
        window.refreshIzinTable = function() {
            window.location.reload();
        };

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