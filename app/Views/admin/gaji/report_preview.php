<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filter Laporan Data Gaji</h5>
            </div>
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Bulan</label>
                            <select class="form-select" name="bulan" id="bulan">
                                <?php
                                $bulan_list = [
                                    '01' => 'Januari',
                                    '02' => 'Februari',
                                    '03' => 'Maret',
                                    '04' => 'April',
                                    '05' => 'Mei',
                                    '06' => 'Juni',
                                    '07' => 'Juli',
                                    '08' => 'Agustus',
                                    '09' => 'September',
                                    '10' => 'Oktober',
                                    '11' => 'November',
                                    '12' => 'Desember'
                                ];
                                foreach ($bulan_list as $key => $bulan) {
                                    $selected = ($filter['bulan'] == $key) ? 'selected' : '';
                                    echo "<option value=\"$key\" $selected>$bulan</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tahun</label>
                            <select class="form-select" name="tahun" id="tahun">
                                <?php
                                $tahun_sekarang = date('Y');
                                for ($i = $tahun_sekarang - 5; $i <= $tahun_sekarang + 1; $i++) {
                                    $selected = ($filter['tahun'] == $i) ? 'selected' : '';
                                    echo "<option value=\"$i\" $selected>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="pending" <?= ($filter['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="paid" <?= ($filter['status'] == 'paid') ? 'selected' : '' ?>>Dibayar</option>
                                <option value="cancelled" <?= ($filter['status'] == 'cancelled') ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-primary" id="btn-filter">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <button type="button" class="btn btn-success" id="btn-cetak">
                                <i class="bi bi-printer"></i> Cetak PDF
                            </button>
                            <button type="button" class="btn btn-secondary" id="btn-reset">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow" id="reportCard">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Data Gaji</h5>
            </div>
            <div class="card-body" id="reportContent">
                <!-- Konten laporan akan diisi melalui AJAX -->
                <div class="text-center p-5">
                    <i class="bi bi-file-earmark-text fs-1"></i>
                    <p class="mt-2">Silakan gunakan filter di atas untuk menampilkan data gaji</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    $(document).ready(function() {
        // Fungsi untuk memuat laporan
        function loadReport() {
            $.ajax({
                url: '<?= site_url('admin/gaji/report_partial') ?>',
                type: 'GET',
                data: {
                    bulan: $('#bulan').val(),
                    tahun: $('#tahun').val(),
                    status: $('#status').val(),
                    ajax: true
                },
                beforeSend: function() {
                    // Tampilkan loading
                    $('#reportContent').html('<div class="text-center p-5"><i class="bi bi-hourglass-split fs-1"></i><p class="mt-2">Memuat data...</p></div>');
                },
                success: function(response) {
                    // Update konten laporan
                    $('#reportCard').replaceWith(response);
                },
                error: function(xhr, status, error) {
                    // Tampilkan pesan error
                    $('#reportContent').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div>');
                    console.error('Error loading report:', error);
                }
            });
        }

        // Handler untuk tombol filter
        $('#btn-filter').click(function() {
            loadReport();
        });

        // Handler untuk tombol reset
        $('#btn-reset').click(function() {
            $('#bulan').val('<?= date('m') ?>');
            $('#tahun').val('<?= date('Y') ?>');
            $('#status').val('');
            loadReport();
        });

        // Handler untuk tombol cetak PDF
        $('#btn-cetak').click(function() {
            // Buat URL untuk cetak PDF dengan parameter filter
            let url = '<?= site_url('admin/gaji/generatePdf') ?>';
            let params = [];

            const bulan = $('#bulan').val();
            const tahun = $('#tahun').val();
            const status = $('#status').val();

            if (bulan) params.push(`bulan=${bulan}`);
            if (tahun) params.push(`tahun=${tahun}`);
            if (status) params.push(`status=${status}`);

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            // Buka di tab baru
            window.open(url, '_blank');
        });

        // Load data awal jika ada filter yang aktif
        <?php if (!empty($filter['bulan']) || !empty($filter['tahun']) || !empty($filter['status'])): ?>
            loadReport();
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>