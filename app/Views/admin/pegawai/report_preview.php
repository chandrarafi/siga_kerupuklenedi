<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filter Laporan Data Pegawai</h5>
            </div>
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label class="form-label">Jabatan</label>
                            <select class="form-select" name="jabatan" id="jabatan">
                                <option value="">Semua Jabatan</option>
                                <?php foreach ($jabatanList as $jabatan): ?>
                                    <?php if (empty($filters['bagian']) || $jabatan['bagianid'] == $filters['bagian']): ?>
                                        <option value="<?= $jabatan['idjabatan'] ?>" <?= ($filters['jabatan'] == $jabatan['idjabatan']) ? 'selected' : '' ?>>
                                            <?= $jabatan['namajabatan'] ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-select" name="jenkel" id="jenkel">
                                <option value="">Semua</option>
                                <option value="Laki-laki" <?= ($filters['jenkel'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($filters['jenkel'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
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
                <h5 class="mb-0">Data Pegawai</h5>
            </div>
            <div class="card-body" id="reportContent">
                <!-- Konten laporan akan diisi melalui AJAX -->
                <div class="text-center p-5">
                    <i class="bi bi-file-earmark-text fs-1"></i>
                    <p class="mt-2">Silakan gunakan filter di atas untuk menampilkan data pegawai</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Handler untuk perubahan bagian
        $('#bagian').change(function() {
            loadJabatan();
        });

        // Fungsi untuk memuat jabatan berdasarkan bagian
        function loadJabatan() {
            const bagianId = $('#bagian').val();
            const jabatanSelect = $('#jabatan');

            // Reset jabatan dropdown
            jabatanSelect.empty().append('<option value="">Semua Jabatan</option>');

            if (bagianId) {
                // Fetch jabatan based on selected bagian
                $.ajax({
                    url: '<?= site_url('admin/pegawai/getJabatanByBagian') ?>',
                    type: 'GET',
                    data: {
                        bagian_id: bagianId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data.length > 0) {
                            $.each(response.data, function(index, jabatan) {
                                jabatanSelect.append(
                                    $('<option></option>')
                                    .attr('value', jabatan.idjabatan)
                                    .text(jabatan.namajabatan)
                                );
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading jabatan:', error);
                    }
                });
            } else {
                // If no bagian selected, load all jabatan
                <?php foreach ($jabatanList as $jabatan): ?>
                    jabatanSelect.append(
                        $('<option></option>')
                        .attr('value', '<?= $jabatan['idjabatan'] ?>')
                        .text('<?= $jabatan['namajabatan'] ?>')
                    );
                <?php endforeach; ?>
            }
        }

        // Fungsi untuk memuat laporan
        function loadReport() {
            $.ajax({
                url: '<?= site_url('admin/pegawai/report') ?>',
                type: 'GET',
                data: {
                    bagian: $('#bagian').val(),
                    jabatan: $('#jabatan').val(),
                    jenkel: $('#jenkel').val(),
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
            $('#jabatan').val('');
            $('#jenkel').val('');
            loadReport();
        });

        // Handler untuk tombol cetak PDF
        $('#btn-cetak').click(function() {
            // Buat URL untuk cetak PDF dengan parameter filter
            let url = '<?= site_url('admin/pegawai/report') ?>';
            let params = [];

            const bagian = $('#bagian').val();
            const jabatan = $('#jabatan').val();
            const jenkel = $('#jenkel').val();

            if (bagian) params.push(`bagian=${bagian}`);
            if (jabatan) params.push(`jabatan=${jabatan}`);
            if (jenkel) params.push(`jenkel=${jenkel}`);

            // Tambahkan parameter print
            params.push('print=true');

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            // Buka di tab baru
            window.open(url, '_blank');
        });

        // Load data awal jika ada filter yang aktif
        <?php if (!empty($filters['bagian']) || !empty($filters['jabatan']) || !empty($filters['jenkel'])): ?>
            loadReport();
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>