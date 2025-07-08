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
                            <input type="text" class="form-control datepicker" name="start_date" id="start_date" value="<?= $filter['start_date'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="text" class="form-control datepicker" name="end_date" id="end_date" value="<?= $filter['end_date'] ?>">
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
                    <div class="mt-3 text-end">
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

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow" id="reportCard">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Data Izin</h5>
            </div>
            <div class="card-body" id="reportContent">
                <!-- Konten laporan akan diisi melalui AJAX -->
                <div class="text-center p-5">
                    <i class="fas fa-file-alt fs-1"></i>
                    <p class="mt-2">Silakan gunakan filter di atas untuk menampilkan data izin</p>
                </div>
            </div>
        </div>
    </div>
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

        // Function to load report via AJAX
        function loadReport() {
            const startDate = $("#start_date").val();
            const endDate = $("#end_date").val();
            const status = $("#status").val();
            const pegawaiId = $("#pegawai_id").val();

            $.ajax({
                url: '<?= base_url('admin/izin/report_partial') ?>',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    status: status,
                    pegawai_id: pegawaiId
                },
                beforeSend: function() {
                    $("#reportContent").html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat data...</p></div>');
                },
                success: function(response) {
                    $("#reportCard").replaceWith(response);
                },
                error: function(xhr, status, error) {
                    $("#reportContent").html(`<div class="alert alert-danger">Terjadi kesalahan: ${error}</div>`);
                }
            });
        }

        // Filter button click
        $("#btn-filter").click(function() {
            loadReport();
        });

        // Reset button click
        $("#btn-reset").click(function() {
            $("#start_date").val('<?= date('Y-m-01') ?>');
            $("#end_date").val('<?= date('Y-m-d') ?>');
            $("#status").val('');
            $("#pegawai_id").val('');
            loadReport();
        });

        // PDF button click
        $("#btn-pdf").click(function() {
            const startDate = $("#start_date").val();
            const endDate = $("#end_date").val();
            const status = $("#status").val();
            const pegawaiId = $("#pegawai_id").val();

            let url = '<?= site_url('admin/izin/generatePdf') ?>';
            let params = [];

            if (startDate) params.push(`start_date=${startDate}`);
            if (endDate) params.push(`end_date=${endDate}`);
            if (status) params.push(`status=${status}`);
            if (pegawaiId) params.push(`pegawai_id=${pegawaiId}`);

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            window.open(url, '_blank');
        });

        // Load initial report if filters are set
        <?php if (!empty($filter['start_date']) || !empty($filter['end_date']) || !empty($filter['status']) || !empty($filter['pegawai_id'])): ?>
            loadReport();
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>