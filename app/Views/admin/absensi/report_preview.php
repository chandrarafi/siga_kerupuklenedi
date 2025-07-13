<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filter Laporan Data Absensi</h5>
            </div>
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-md-3" hidden>
                            <label class="form-label">Pegawai</label>
                            <select class="form-select" name="pegawai" id="pegawai">
                                <option value="">Semua Pegawai</option>
                                <?php foreach ($pegawaiList as $pegawai): ?>
                                    <option value="<?= $pegawai['idpegawai'] ?>" <?= ($filters['pegawai'] == $pegawai['idpegawai']) ? 'selected' : '' ?>>
                                        <?= $pegawai['namapegawai'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3" hidden>
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="Hadir" <?= ($filters['status'] == 'Hadir') ? 'selected' : '' ?>>Hadir</option>
                                <option value="Terlambat" <?= ($filters['status'] == 'Terlambat') ? 'selected' : '' ?>>Terlambat</option>
                                <option value="Alpha" <?= ($filters['status'] == 'Alpha') ? 'selected' : '' ?>>Alpha</option>
                                <option value="Izin" <?= ($filters['status'] == 'Izin') ? 'selected' : '' ?>>Izin</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="text" class="form-control datepicker" name="tanggal_awal" id="tanggal_awal" placeholder="Pilih Tanggal Awal" value="<?= $filters['tanggal_awal'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="text" class="form-control datepicker" name="tanggal_akhir" id="tanggal_akhir" placeholder="Pilih Tanggal Akhir" value="<?= $filters['tanggal_akhir'] ?? '' ?>">
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
                <h5 class="mb-0">Data Absensi</h5>
            </div>
            <div class="card-body" id="reportContent">
                <!-- Konten laporan akan diisi melalui AJAX -->
                <div class="text-center p-5">
                    <i class="bi bi-file-earmark-text fs-1"></i>
                    <p class="mt-2">Silakan gunakan filter di atas untuk menampilkan data absensi</p>
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
        // Inisialisasi date picker
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: true,
            locale: "id"
        });

        // Fungsi untuk memuat laporan
        function loadReport() {
            $.ajax({
                url: '<?= site_url('admin/absensi/report') ?>',
                type: 'GET',
                data: {
                    pegawai: $('#pegawai').val(),
                    status: $('#status').val(),
                    tanggal_awal: $('#tanggal_awal').val(),
                    tanggal_akhir: $('#tanggal_akhir').val(),
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
            $('#pegawai').val('');
            $('#status').val('');
            $('#tanggal_awal').val('');
            $('#tanggal_akhir').val('');
            loadReport();
        });

        // Handler untuk tombol cetak PDF
        $('#btn-cetak').click(function() {
            // Buat URL untuk cetak PDF dengan parameter filter
            let url = '<?= site_url('admin/absensi/report') ?>';
            let params = [];

            const pegawai = $('#pegawai').val();
            const status = $('#status').val();
            const tanggalAwal = $('#tanggal_awal').val();
            const tanggalAkhir = $('#tanggal_akhir').val();

            if (pegawai) params.push(`pegawai=${pegawai}`);
            if (status) params.push(`status=${status}`);
            if (tanggalAwal) params.push(`tanggal_awal=${tanggalAwal}`);
            if (tanggalAkhir) params.push(`tanggal_akhir=${tanggalAkhir}`);

            // Tambahkan parameter print
            params.push('print=true');

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            // Buka di tab baru
            window.open(url, '_blank');
        });

        // Load data awal jika ada filter yang aktif
        <?php if (!empty($filters['pegawai']) || !empty($filters['status']) || !empty($filters['tanggal_awal']) || !empty($filters['tanggal_akhir'])): ?>
            loadReport();
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>