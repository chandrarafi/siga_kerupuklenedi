<?php
// File: app/Views/admin/gaji/create.php
// Halaman tambah gaji untuk admin
?>

<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    /* Fix untuk masalah modal backdrop - menggunakan custom overlay */
    .modal-backdrop {
        display: none !important;
    }

    /* Custom overlay untuk modal */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1030;
        display: none;
    }

    .modal {
        z-index: 1040 !important;
    }

    /* Pastikan modal muncul di atas overlay */
    #pegawaiModal {
        z-index: 1050 !important;
    }

    /* Fix untuk body saat modal terbuka */
    body.modal-open {
        overflow: auto !important;
        padding-right: 0 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Tambah Data Gaji</h6>
        <div class="dropdown no-arrow">
            <a href="<?= site_url('admin/gaji') ?>" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form id="gajiForm" action="<?= site_url('admin/gaji/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Data Pegawai & Periode</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="pegawai_id" class="form-label">Pegawai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select id="pegawai_id" name="pegawai_id" class="form-select select2-pegawai <?= (session()->has('errors') && isset(session('errors')['pegawai_id'])) ? 'is-invalid' : '' ?>" required>
                                        <?php if (old('pegawai_id')) : ?>
                                            <option value="<?= old('pegawai_id') ?>" selected>Memuat data pegawai...</option>
                                        <?php else : ?>
                                            <option value="">Pilih Pegawai</option>
                                        <?php endif; ?>
                                    </select>
                                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#pegawaiModal">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <?php if (session()->has('errors') && isset(session('errors')['pegawai_id'])) : ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors')['pegawai_id'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bulan" class="form-label">Bulan <span class="text-danger">*</span></label>
                                    <select id="bulan" name="bulan" class="form-select <?= (session()->has('errors') && isset(session('errors')['bulan'])) ? 'is-invalid' : '' ?>" required>
                                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                                            <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?= (old('bulan') ? old('bulan') : $bulan_sekarang) == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                                <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <?php if (session()->has('errors') && isset(session('errors')['bulan'])) : ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors')['bulan'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <select id="tahun" name="tahun" class="form-select <?= (session()->has('errors') && isset(session('errors')['tahun'])) ? 'is-invalid' : '' ?>" required>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) :
                                        ?>
                                            <option value="<?= $i ?>" <?= (old('tahun') ? old('tahun') : $tahun_sekarang) == $i ? 'selected' : '' ?>>
                                                <?= $i ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <?php if (session()->has('errors') && isset(session('errors')['tahun'])) : ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors')['tahun'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal Gaji <span class="text-danger">*</span></label>
                                <input type="date" id="tanggal" name="tanggal" class="form-control <?= (session()->has('errors') && isset(session('errors')['tanggal'])) ? 'is-invalid' : '' ?>" value="<?= old('tanggal') ? old('tanggal') : date('Y-m-d') ?>" required>
                                <?php if (session()->has('errors') && isset(session('errors')['tanggal'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['tanggal'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" id="hitungGajiBtn" class="btn btn-primary">
                                    <i class="bi bi-calculator me-1"></i> Hitung Gaji
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Informasi Pegawai</h6>
                        </div>
                        <div class="card-body" id="infoPegawai">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-person-badge fs-1"></i>
                                <p class="mt-2">Silakan pilih pegawai dan periode, lalu klik tombol "Hitung Gaji"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4" id="komponenGajiCard" style="display: none;">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Komponen Gaji</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="totalabsen" class="form-label">Total Absensi (Hari) <span class="text-danger">*</span></label>
                                <input type="number" id="totalabsen" name="totalabsen" class="form-control <?= (session()->has('errors') && isset(session('errors')['totalabsen'])) ? 'is-invalid' : '' ?>" value="<?= old('totalabsen') ?>" step="1" min="0" required readonly>
                                <?php if (session()->has('errors') && isset(session('errors')['totalabsen'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['totalabsen'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="totallembur" class="form-label">Total Lembur (Jam) <span class="text-danger">*</span></label>
                                <input type="number" id="totallembur" name="totallembur" class="form-control <?= (session()->has('errors') && isset(session('errors')['totallembur'])) ? 'is-invalid' : '' ?>" value="<?= old('totallembur') ?>" step="0.01" min="0" required readonly>
                                <?php if (session()->has('errors') && isset(session('errors')['totallembur'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['totallembur'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gajibersih_display" class="form-label">Gaji Bersih (Rp) <span class="text-danger">*</span></label>
                                <input type="text" id="gajibersih_display" class="form-control" readonly>
                                <input type="hidden" id="gajibersih" name="gajibersih" value="<?= old('gajibersih') ?>">
                                <?php if (session()->has('errors') && isset(session('errors')['gajibersih'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['gajibersih'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="metodepembayaran" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select id="metodepembayaran" name="metodepembayaran" class="form-select <?= (session()->has('errors') && isset(session('errors')['metodepembayaran'])) ? 'is-invalid' : '' ?>" required>
                                    <option value="Transfer Bank" <?= old('metodepembayaran') == 'Transfer Bank' ? 'selected' : '' ?>>Transfer Bank</option>
                                    <option value="Tunai" <?= old('metodepembayaran') == 'Tunai' ? 'selected' : '' ?>>Tunai</option>
                                    <option value="Cek" <?= old('metodepembayaran') == 'Cek' ? 'selected' : '' ?>>Cek</option>
                                </select>
                                <?php if (session()->has('errors') && isset(session('errors')['metodepembayaran'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['metodepembayaran'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select <?= (session()->has('errors') && isset(session('errors')['status'])) ? 'is-invalid' : '' ?>" required>
                                    <option value="pending" <?= old('status') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="paid" <?= old('status') == 'paid' ? 'selected' : '' ?>>Paid</option>
                                </select>
                                <?php if (session()->has('errors') && isset(session('errors')['status'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['status'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea id="keterangan" name="keterangan" class="form-control <?= (session()->has('errors') && isset(session('errors')['keterangan'])) ? 'is-invalid' : '' ?>" rows="3"><?= old('keterangan') ?></textarea>
                                <?php if (session()->has('errors') && isset(session('errors')['keterangan'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors')['keterangan'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" id="submitBtn" class="btn btn-primary" style="display: none;">
                    <i class="bi bi-save me-1"></i> Simpan Data Gaji
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pastikan tidak ada backdrop yang tersisa saat halaman dimuat
        cleanupModalEffects();

        // Fungsi untuk membersihkan efek modal
        function cleanupModalEffects() {
            $('.modal-backdrop').remove();
            $('.custom-modal-overlay').hide();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            $('body').css('overflow', '');
            $('html').css('overflow', '');
        }

        // Inisialisasi Select2 untuk pegawai
        $('.select2-pegawai').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih pegawai',
            allowClear: true,
            ajax: {
                url: '<?= site_url('admin/gaji/get-pegawai') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        // Fungsi untuk format rupiah
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // Load pegawai ke tabel modal
        function loadPegawaiData(search = '') {
            $.ajax({
                url: '<?= site_url('admin/gaji/get-pegawai') ?>',
                type: 'GET',
                data: {
                    search: search,
                    page: 1,
                    limit: 100
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#pegawaiTable tbody').html('<tr><td colspan="5" class="text-center">Memuat data pegawai...</td></tr>');
                },
                success: function(response) {
                    if (response.results && response.results.length > 0) {
                        let html = '';
                        $.each(response.results, function(index, pegawai) {
                            html += `
                                <tr>
                                    <td>${pegawai.nik}</td>
                                    <td>${pegawai.text.split(' - ')[0]}</td>
                                    <td>${pegawai.jabatan}</td>
                                    <td>${pegawai.bagian}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary pilih-pegawai" 
                                            data-id="${pegawai.id}" 
                                            data-text="${pegawai.text}">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#pegawaiTable tbody').html(html);
                    } else {
                        $('#pegawaiTable tbody').html('<tr><td colspan="5" class="text-center">Tidak ada data pegawai</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $('#pegawaiTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Terjadi kesalahan saat memuat data</td></tr>');
                }
            });
        }

        // Sebelum membuka modal, pastikan efek modal sebelumnya sudah dibersihkan
        $(document).on('click', '[data-bs-toggle="modal"]', function() {
            cleanupModalEffects();
            // Tampilkan custom overlay
            $('.custom-modal-overlay').show();
        });

        // Load data pegawai saat modal dibuka
        $('#pegawaiModal').on('shown.bs.modal', function() {
            loadPegawaiData();
            // Pastikan custom overlay ditampilkan
            $('.custom-modal-overlay').show();
        });

        // Filter pencarian pegawai
        $('#searchPegawai').on('keyup', function() {
            const searchTerm = $(this).val();
            loadPegawaiData(searchTerm);
        });

        // Handle pilih pegawai dari tabel
        $(document).on('click', '.pilih-pegawai', function() {
            const id = $(this).data('id');
            const text = $(this).data('text');

            // Buat option baru di select2 dan pilih
            const newOption = new Option(text, id, true, true);
            $('.select2-pegawai').append(newOption).trigger('change');

            // Tutup modal dengan benar
            closeModalProperly();
        });

        // Tambahkan event handler untuk tombol tutup modal
        $('#closeModal, .btn-close, #tutupBtn').on('click', function() {
            closeModalProperly();
        });

        // Tambahkan event handler saat modal ditutup
        $('#pegawaiModal').on('hidden.bs.modal', function() {
            cleanupModalEffects();
        });

        // Tambahkan event handler untuk klik pada custom overlay (menutup modal saat klik di luar)
        $(document).on('click', '.custom-modal-overlay', function() {
            closeModalProperly();
        });

        // Fungsi untuk menutup modal dengan benar
        function closeModalProperly() {
            // Sembunyikan modal secara manual
            $('#pegawaiModal').removeClass('show');
            $('#pegawaiModal').css('display', 'none');
            $('#pegawaiModal').attr('aria-hidden', 'true');

            // Bersihkan efek modal
            cleanupModalEffects();
        }

        // Hitung Gaji
        $('#hitungGajiBtn').on('click', function() {
            const pegawaiId = $('#pegawai_id').val();
            const bulan = $('#bulan').val();
            const tahun = $('#tahun').val();

            if (!pegawaiId) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Silakan pilih pegawai terlebih dahulu',
                    icon: 'warning'
                });
                return;
            }

            // Tampilkan SweetAlert loading - hanya satu spinner
            let isRequestCancelled = false;
            let ajaxRequest = null;

            // Hapus loading yang mungkin masih ada
            Swal.close();

            // Tampilkan loading dengan satu spinner saja
            Swal.fire({
                title: 'Menghitung Gaji',
                html: `
                    <div class="text-center">
                        <div class="spinner-grow text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Sedang menghitung gaji...</p>
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: false
            });

            // Set timeout untuk tombol batal
            const timeoutId = setTimeout(() => {
                if (!isRequestCancelled) {
                    Swal.update({
                        html: `
                            <div class="text-center">
                                <div class="spinner-grow text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Sedang menghitung gaji...</p>
                                <div class="alert alert-warning mt-3">
                                    <p class="mb-0">Proses memakan waktu lebih lama dari biasanya.</p>
                                </div>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'Batalkan Proses',
                        confirmButtonColor: '#dc3545'
                    });
                }
            }, 5000);

            // Kirim request AJAX untuk hitung gaji
            ajaxRequest = $.ajax({
                url: '<?= site_url('admin/gaji/hitung-gaji') ?>',
                type: 'POST',
                data: {
                    pegawai_id: pegawaiId,
                    bulan: bulan,
                    tahun: tahun,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    // Bersihkan timeout
                    clearTimeout(timeoutId);

                    // Tutup SweetAlert loading jika request belum dibatalkan
                    if (!isRequestCancelled) {
                        Swal.close();

                        if (response.status) {
                            const data = response.data;
                            const pegawai = data.pegawai;
                            const komponenGaji = data.komponen_gaji;
                            const detailGaji = data.detail;

                            // Tampilkan informasi pegawai
                            let pegawaiInfo = `
                                <div class="mb-3">
                                    <h6 class="fw-bold">Nama Pegawai</h6>
                                    <p>${pegawai.namapegawai}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">NIK</h6>
                                    <p>${pegawai.nik}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Jabatan</h6>
                                    <p>${pegawai.nama_jabatan}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Bagian</h6>
                                    <p>${pegawai.namabagian}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Gaji Pokok</h6>
                                    <p>Rp ${formatRupiah(komponenGaji.gaji_pokok)}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Tunjangan per Hari</h6>
                                    <p>Rp ${formatRupiah(detailGaji.tunjangan_per_hari)}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Tarif Lembur</h6>
                                    <p>Rp ${formatRupiah(detailGaji.tarif_lembur)} per jam</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Total Jam Lembur</h6>
                                    <p>${detailGaji.total_lembur} jam</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Total Upah Lembur</h6>
                                    <p>Rp ${formatRupiah(detailGaji.total_lembur * detailGaji.tarif_lembur)}</p>
                                </div>
                            `;

                            $('#infoPegawai').html(pegawaiInfo);

                            // Isi nilai komponen gaji
                            $('#totalabsen').val(detailGaji.total_absensi);
                            $('#totallembur').val(detailGaji.total_lembur);
                            $('#gajibersih').val(komponenGaji.gaji_bersih);
                            $('#gajibersih_display').val('Rp ' + formatRupiah(komponenGaji.gaji_bersih));

                            // Tampilkan form komponen gaji dan tombol submit
                            $('#komponenGajiCard').show();
                            $('#submitBtn').show();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menghitung Gaji',
                                text: response.message,
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // Bersihkan timeout
                    clearTimeout(timeoutId);

                    // Jika error bukan karena pembatalan
                    if (status !== 'abort' && !isRequestCancelled) {
                        // Tutup SweetAlert loading
                        Swal.close();

                        console.error('Error:', xhr.responseText);

                        // Cek apakah error karena konflik (gaji sudah ada)
                        if (xhr.status === 409) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                Swal.fire({
                                    title: 'Peringatan',
                                    text: response.message || 'Pegawai ini sudah menerima gaji untuk periode yang dipilih',
                                    icon: 'warning'
                                });
                            } catch (e) {
                                Swal.fire({
                                    title: 'Peringatan',
                                    text: 'Pegawai ini sudah menerima gaji untuk periode yang dipilih',
                                    icon: 'warning'
                                });
                            }
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghitung gaji',
                                icon: 'error'
                            });
                        }
                    }
                }
            });

            // Handler untuk konfirmasi pembatalan
            $(document).on('click', '.swal2-confirm', function() {
                if (Swal.isVisible()) {
                    isRequestCancelled = true;
                    if (ajaxRequest && ajaxRequest.readyState !== 4) {
                        ajaxRequest.abort();
                    }
                    Swal.fire({
                        title: 'Dibatalkan!',
                        text: 'Proses perhitungan gaji telah dibatalkan',
                        icon: 'info'
                    });
                }
            });
        });

        // Submit form dengan AJAX
        $('#gajiForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menyimpan Data',
                        html: `
                            <div class="text-center">
                                <div class="spinner-grow text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Sedang menyimpan data gaji...</p>
                            </div>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            window.location.href = '<?= site_url('admin/gaji') ?>';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message,
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan data',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>

<!-- Custom Modal Overlay -->
<div class="custom-modal-overlay"></div>

<!-- Modal Pilih Pegawai -->
<div class="modal fade" id="pegawaiModal" tabindex="-1" aria-labelledby="pegawaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pegawaiModalLabel">Pilih Pegawai</h5>
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="searchPegawai" class="form-control" placeholder="Cari pegawai...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="pegawaiTable">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Bagian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">Memuat data pegawai...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>