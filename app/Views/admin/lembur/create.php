<?php
// File: app/Views/admin/lembur/create.php
// Halaman tambah data lembur untuk admin
?>

<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Tambah Data Lembur</h6>
        <a href="<?= site_url('admin/lembur') ?>" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div id="responseMessage"></div>

        <form id="formLembur" action="<?= site_url('admin/lembur/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pegawai_id" class="form-label">Pegawai <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="hidden" id="pegawai_id" name="pegawai_id" value="<?= old('pegawai_id') ?>" required>
                        <input type="text" id="pegawai_nama" name="pegawai_nama" class="form-control <?= (session()->has('errors') && isset(session('errors')['pegawai_id'])) ? 'is-invalid' : '' ?>" placeholder="Pilih pegawai" readonly value="<?= old('pegawai_nama') ?? session()->getFlashdata('pegawai_nama') ?>" required>
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#pegawaiModal">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback error-pegawai_id"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tanggallembur" class="form-label">Tanggal Lembur <span class="text-danger">*</span></label>
                    <input type="date" id="tanggallembur" name="tanggallembur" value="<?= old('tanggallembur') ?? date('Y-m-d') ?>" class="form-control <?= (session()->has('errors') && isset(session('errors')['tanggallembur'])) ? 'is-invalid' : '' ?>" required>
                    <div class="invalid-feedback error-tanggallembur"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="jammulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                    <input type="time" id="jammulai" name="jammulai" value="<?= old('jammulai') ?? '17:00' ?>" class="form-control <?= (session()->has('errors') && isset(session('errors')['jammulai'])) ? 'is-invalid' : '' ?>" required>
                    <div class="invalid-feedback error-jammulai"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="jamselesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                    <input type="time" id="jamselesai" name="jamselesai" value="<?= old('jamselesai') ?? '21:00' ?>" class="form-control <?= (session()->has('errors') && isset(session('errors')['jamselesai'])) ? 'is-invalid' : '' ?>" required>
                    <div class="invalid-feedback error-jamselesai"></div>
                    <div class="form-text" id="durasiInfo"></div>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="alasan" class="form-label">Alasan Lembur <span class="text-danger">*</span></label>
                    <textarea id="alasan" name="alasan" rows="4" class="form-control <?= (session()->has('errors') && isset(session('errors')['alasan'])) ? 'is-invalid' : '' ?>" required><?= old('alasan') ?></textarea>
                    <div class="invalid-feedback error-alasan"></div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary" id="btnSimpan">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jammulaiInput = document.getElementById('jammulai');
        const jamselesaiInput = document.getElementById('jamselesai');
        const durasiInfo = document.getElementById('durasiInfo');

        // Fungsi untuk menghitung durasi lembur
        function hitungDurasi() {
            const jammulai = jammulaiInput.value;
            const jamselesai = jamselesaiInput.value;

            if (jammulai && jamselesai) {
                // Konversi ke timestamp
                let time1 = new Date(`2000-01-01T${jammulai}`);
                let time2 = new Date(`2000-01-01T${jamselesai}`);

                // Jika jamselesai lebih kecil dari jammulai, berarti melewati tengah malam
                if (time2 < time1) {
                    time2.setDate(time2.getDate() + 1);
                }

                // Hitung selisih dalam menit
                const selisihMenit = Math.round(Math.abs(time2 - time1) / 60000);
                const jam = Math.floor(selisihMenit / 60);
                const menit = selisihMenit % 60;

                durasiInfo.innerHTML = `Durasi lembur: <strong>${jam} jam ${menit} menit</strong>`;
            } else {
                durasiInfo.innerHTML = '';
            }
        }

        // Panggil fungsi saat halaman dimuat
        hitungDurasi();

        // Event listener saat input berubah
        jammulaiInput.addEventListener('change', hitungDurasi);
        jamselesaiInput.addEventListener('change', hitungDurasi);
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script untuk modal pegawai
        const pilihPegawaiButtons = document.querySelectorAll('.pilih-pegawai');
        const pegawaiIdInput = document.getElementById('pegawai_id');
        const pegawaiNamaInput = document.getElementById('pegawai_nama');
        const pegawaiModal = document.getElementById('pegawaiModal');
        const searchPegawai = document.getElementById('searchPegawai');
        const tablePegawai = document.getElementById('tablePegawai');

        // Event untuk tombol pilih pegawai
        pilihPegawaiButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                pegawaiIdInput.value = id;
                pegawaiNamaInput.value = nama;

                // Tutup modal
                const modal = bootstrap.Modal.getInstance(pegawaiModal);
                modal.hide();
            });
        });

        // Fungsi pencarian pegawai
        searchPegawai.addEventListener('keyup', function() {
            const keyword = this.value.toLowerCase();
            const rows = tablePegawai.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const nik = row.cells[0].textContent.toLowerCase();
                const nama = row.cells[1].textContent.toLowerCase();
                const bagian = row.cells[2].textContent.toLowerCase();

                if (nik.includes(keyword) || nama.includes(keyword) || bagian.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // AJAX form submission
        $('#formLembur').on('submit', function(e) {
            e.preventDefault();

            // Reset validation errors
            $('.invalid-feedback').text('');
            $('.is-invalid').removeClass('is-invalid');
            $('#responseMessage').html('');

            // Mengubah status tombol submit
            $('#btnSimpan').attr('disabled', true);
            $('#btnSimpan').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

            // Mengambil data form
            const formData = $(this).serialize();

            // Kirim data dengan AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // Handle success
                    if (response.status) {
                        // Tampilkan pesan sukses
                        $('#responseMessage').html(`
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);

                        // Reset form
                        $('#formLembur')[0].reset();

                        // Redirect ke halaman index setelah 2 detik
                        setTimeout(function() {
                            window.location.href = '<?= site_url('admin/lembur') ?>';
                        }, 2000);
                    } else {
                        // Tampilkan pesan error
                        $('#responseMessage').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);

                        // Tampilkan error validasi
                        if (response.errors) {
                            $.each(response.errors, function(field, message) {
                                $(`#${field}`).addClass('is-invalid');
                                $(`.error-${field}`).text(message);
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    $('#responseMessage').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi kesalahan saat menyimpan data
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                    console.error(xhr.responseText);
                },
                complete: function() {
                    // Reset status tombol submit
                    $('#btnSimpan').attr('disabled', false);
                    $('#btnSimpan').html('<i class="bi bi-save me-1"></i> Simpan');
                }
            });
        });
    });
</script>
<!-- Modal Pegawai -->
<div class="modal fade" id="pegawaiModal" tabindex="-1" aria-labelledby="pegawaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pegawaiModalLabel">Pilih Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="searchPegawai" class="form-control" placeholder="Cari pegawai...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="tablePegawai">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama Pegawai</th>
                                <th>Bagian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pegawai_list as $pegawai) : ?>
                                <tr>
                                    <td><?= $pegawai['nik'] ?></td>
                                    <td><?= $pegawai['namapegawai'] ?></td>
                                    <td><?= $pegawai['nama_bagian'] ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary pilih-pegawai"
                                            data-id="<?= $pegawai['idpegawai'] ?>"
                                            data-nama="<?= $pegawai['namapegawai'] ?> (<?= $pegawai['nik'] ?>)">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>