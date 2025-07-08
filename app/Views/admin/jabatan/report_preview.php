<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filter Laporan Data Jabatan</h5>
            </div>
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bagian</label>
                            <select class="form-select" name="bagian" id="bagian">
                                <option value="">Semua Bagian</option>
                                <?php foreach ($bagianList as $bagian): ?>
                                    <option value="<?= $bagian['idbagian'] ?>" <?= ($filters['bagian'] == $bagian['idbagian']) ? 'selected' : '' ?>>
                                        <?= $bagian['namabagian'] ?>
                                    </option>
                                <?php endforeach; ?>
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
                <h5 class="mb-0">Data Jabatan</h5>
            </div>
            <div class="card-body" id="reportContent">
                <!-- Konten laporan akan diisi melalui AJAX -->
                <div class="text-center p-5">
                    <i class="bi bi-file-earmark-text fs-1"></i>
                    <p class="mt-2">Silakan gunakan filter di atas untuk menampilkan data jabatan</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Fungsi untuk memuat laporan
        function loadReport() {
            $.ajax({
                url: '<?= site_url('admin/jabatan/report') ?>',
                type: 'GET',
                data: {
                    bagian: $('#bagian').val(),
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
            $('#bagian').val('');
            loadReport();
        });

        // Handler untuk tombol cetak PDF
        $('#btn-cetak').click(function() {
            // Buat URL untuk cetak PDF dengan parameter filter
            let url = '<?= site_url('admin/jabatan/report') ?>';
            let params = [];

            const bagian = $('#bagian').val();

            if (bagian) params.push(`bagian=${bagian}`);

            // Tambahkan parameter print
            params.push('print=true');

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            // Buka di tab baru
            window.open(url, '_blank');
        });

        // Load data awal jika ada filter yang aktif
        <?php if (!empty($filters['bagian'])): ?>
            loadReport();
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>