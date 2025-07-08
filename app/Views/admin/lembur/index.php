<?php
// File: app/Views/admin/lembur/index.php
// Halaman daftar lembur untuk admin
?>

<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Daftar Lembur</h6>
        <div class="dropdown no-arrow">
            <a href="<?= site_url('admin/lembur/create') ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Lembur
            </a>
            <a href="<?= site_url('admin/lembur/report') ?>" class="btn btn-sm btn-info">
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
            <form id="filterForm" action="<?= site_url('admin/lembur') ?>" method="get">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2 col-6">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select id="bulan" name="bulan" class="form-select">
                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?= $filter['bulan'] == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-6">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select id="tahun" name="tahun" class="form-select">
                            <?php
                            $currentYear = date('Y');
                            for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) :
                            ?>
                                <option value="<?= $i ?>" <?= $filter['tahun'] == $i ? 'selected' : '' ?>>
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label for="pegawai_id" class="form-label">Pegawai</label>
                        <select id="pegawai_id" name="pegawai_id" class="form-select">
                            <option value="">Semua Pegawai</option>
                            <?php foreach ($pegawai_list as $pegawai) : ?>
                                <option value="<?= $pegawai['idpegawai'] ?>" <?= $filter['pegawai_id'] == $pegawai['idpegawai'] ? 'selected' : '' ?>>
                                    <?= $pegawai['namapegawai'] ?> (<?= $pegawai['nik'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label for="search" class="form-label">Cari</label>
                        <input type="text" id="search" name="search" value="<?= $filter['search'] ?>" placeholder="ID / Nama Pegawai" class="form-control">
                    </div>

                    <div class="col-md-2 col-12 mt-3 mt-md-0">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                            <a href="<?= site_url('admin/lembur') ?>" class="btn btn-secondary flex-grow-1" id="resetFilter">
                                <i class="bi bi-x-circle me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($lembur_list)) : ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> Tidak ada data lembur yang ditemukan.
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="lemburTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Lembur</th>
                            <th>Pegawai</th>
                            <th>Tanggal</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Durasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lembur_list as $lembur) : ?>
                            <tr>
                                <td><?= $lembur['idlembur'] ?></td>
                                <td><?= $lembur['namapegawai'] ?></td>
                                <td><?= date('d/m/Y', strtotime($lembur['tanggallembur'])) ?></td>
                                <td><?= date('H:i', strtotime($lembur['jammulai'])) ?></td>
                                <td><?= date('H:i', strtotime($lembur['jamselesai'])) ?></td>
                                <td><?= $lembur['durasi_format'] ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-info btn-detail" data-id="<?= $lembur['idlembur'] ?>" title="Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="<?= site_url('admin/lembur/edit/' . $lembur['idlembur']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="confirmDelete('<?= $lembur['idlembur'] ?>')" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </div>
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


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Initialize DataTable
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        let table = new DataTable('#lemburTable', {
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Detail Button Click
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                console.log('Showing detail for:', id);

                // Show modal
                const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                detailModal.show();

                // Reset modal content
                document.getElementById('detailModalBody').innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;

                // Fetch detail data
                fetch(`<?= site_url('admin/lembur/show/') ?>${id}?ajax=1`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(html => {
                        document.getElementById('detailModalBody').innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching details:', error);
                        document.getElementById('detailModalBody').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Terjadi kesalahan saat memuat data.
                                <p class="small mt-2">${error.message}</p>
                            </div>
                        `;
                    });
            });
        });

        // Function to close modal - exposed to iframe
        window.closeDetailModal = function() {
            const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
            if (detailModal) detailModal.hide();
        };

        // Function to refresh table - exposed to iframe
        window.refreshLemburTable = function() {
            window.location.reload();
        };

        // Filter form submission
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;
            const pegawai_id = document.getElementById('pegawai_id').value;
            const search = document.getElementById('search').value;

            let url = '<?= site_url('admin/lembur') ?>';
            let params = [];

            if (bulan) {
                params.push(`bulan=${bulan}`);
            }

            if (tahun) {
                params.push(`tahun=${tahun}`);
            }

            if (pegawai_id) {
                params.push(`pegawai_id=${pegawai_id}`);
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
        document.getElementById('resetFilter').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '<?= site_url('admin/lembur') ?>';
        });
    });

    // Konfirmasi hapus
    function confirmDelete(id) {
        document.getElementById('deleteLink').href = `<?= site_url('admin/lembur/delete/') ?>${id}`;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Lembur</h5>
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data lembur ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>