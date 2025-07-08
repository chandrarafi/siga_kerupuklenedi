<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filter Laporan Data Izin</h5>
            </div>
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="text" class="form-control datepicker" name="tanggal_awal" id="tanggal_awal" value="<?= $filter['start_date'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="text" class="form-control datepicker" name="tanggal_akhir" id="tanggal_akhir" value="<?= $filter['end_date'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="0" <?= $filter['status'] === '0' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="1" <?= $filter['status'] === '1' ? 'selected' : '' ?>>Disetujui</option>
                                <option value="2" <?= $filter['status'] === '2' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pegawai</label>
                            <select class="form-select" name="pegawai_id" id="pegawai_id">
                                <option value="">Semua Pegawai</option>
                                <?php foreach ($pegawai_list as $pegawai) : ?>
                                    <option value="<?= $pegawai['idpegawai'] ?>" <?= $filter['pegawai_id'] == $pegawai['idpegawai'] ? 'selected' : '' ?>>
                                        <?= $pegawai['namapegawai'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" id="btn-filter">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button type="button" class="btn btn-success" id="btn-pdf">
                            <i class="fas fa-file-pdf"></i> Cetak PDF
                        </button>
                        <button type="reset" class="btn btn-secondary" id="btn-reset">
                            <i class="fas fa-sync"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="mt-4" id="report-container">
    <!-- Report content will be loaded here via AJAX -->
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        // Initialize date pickers
        $(".datepicker").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: "today"
        });

        // Load initial report
        loadReport();

        // Filter button click
        $("#btn-filter").click(function() {
            loadReport();
        });

        // Reset button click
        $("#btn-reset").click(function() {
            $("#tanggal_awal").val('<?= date('Y-m-01') ?>');
            $("#tanggal_akhir").val('<?= date('Y-m-d') ?>');
            $("#status").val('');
            $("#pegawai_id").val('');
            loadReport();
        });

        // PDF button click
        $("#btn-pdf").click(function() {
            const tanggalAwal = $("#tanggal_awal").val();
            const tanggalAkhir = $("#tanggal_akhir").val();
            const status = $("#status").val();
            const pegawaiId = $("#pegawai_id").val();

            const url = `<?= base_url('admin/izin/generatePdf') ?>?start_date=${tanggalAwal}&end_date=${tanggalAkhir}&status=${status}&pegawai_id=${pegawaiId}`;
            window.open(url, '_blank');
        });

        // Function to load report via AJAX
        function loadReport() {
            const tanggalAwal = $("#tanggal_awal").val();
            const tanggalAkhir = $("#tanggal_akhir").val();
            const status = $("#status").val();
            const pegawaiId = $("#pegawai_id").val();

            $.ajax({
                url: '<?= base_url('admin/izin/report_partial') ?>',
                type: 'GET',
                data: {
                    start_date: tanggalAwal,
                    end_date: tanggalAkhir,
                    status: status,
                    pegawai_id: pegawaiId
                },
                beforeSend: function() {
                    $("#report-container").html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading...</p></div>');
                },
                success: function(response) {
                    $("#report-container").html(response);
                },
                error: function(xhr, status, error) {
                    $("#report-container").html(`<div class="alert alert-danger">Terjadi kesalahan: ${error}</div>`);
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>