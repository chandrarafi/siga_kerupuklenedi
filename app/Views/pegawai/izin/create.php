<?php
// File: app/Views/pegawai/izin/create.php
// Halaman form pengajuan izin untuk pegawai
?>

<?= $this->extend('pegawai/layouts/main') ?>

<?= $this->section('style') ?>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Sweetalert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
<style>
    /* Form styling */
    .form-control,
    .form-select {
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        border-color: #d1d5db;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4f46e5;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .form-text {
        color: #6b7280;
        font-size: 0.875rem;
    }

    /* Card styling */
    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .card-header {
        background: #4f46e5;
        color: white;
        padding: 1rem 1.5rem;
        border-bottom: none;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Button styling */
    .btn {
        border-radius: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: #4f46e5;
        border: none;
    }

    .btn-primary:hover {
        background: #4338ca;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #4b5563;
        border: none;
    }

    .btn-secondary:hover {
        background: #d1d5db;
        transform: translateY(-2px);
    }

    /* Date tags styling */
    .date-tag {
        display: inline-flex;
        align-items: center;
        background: #e0e7ff;
        color: #4f46e5;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        margin: 0.25rem;
        font-size: 0.875rem;
    }

    .date-tag .remove-date {
        margin-left: 0.5rem;
        cursor: pointer;
        color: #4f46e5;
        transition: all 0.2s ease;
    }

    .date-tag .remove-date:hover {
        transform: scale(1.2);
    }

    /* Tips styling */
    .tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .tips-list li {
        position: relative;
        padding: 0.875rem 0.875rem 0.875rem 2.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .tips-list li:last-child {
        border-bottom: none;
    }

    .tips-list li:before {
        content: '✓';
        position: absolute;
        left: 0.875rem;
        color: #10b981;
        font-weight: bold;
    }

    /* File upload styling */
    .file-upload-label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background-color: #f3f4f6;
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 0.5rem;
    }

    .file-upload-label:hover {
        background-color: #e5e7eb;
        border-color: #9ca3af;
    }

    .file-upload-icon {
        font-size: 1.5rem;
        margin-right: 0.5rem;
        color: #6b7280;
    }

    /* Form section styling */
    .form-section {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .form-section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .form-section-title i {
        margin-right: 0.5rem;
        color: #4f46e5;
    }

    /* Loader styling */
    .loader {
        border: 3px solid #f3f4f6;
        border-radius: 50%;
        border-top: 3px solid #4f46e5;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 0.5rem;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Invalid feedback */
    .invalid-feedback {
        display: none;
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .is-invalid {
        border-color: #ef4444 !important;
    }

    .is-invalid+.invalid-feedback {
        display: block;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-2">Ajukan Izin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('pegawai/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('pegawai/izin') ?>">Izin</a></li>
        <li class="breadcrumb-item active">Ajukan Izin</li>
    </ol>

    <!-- Alert Container -->
    <div id="alert-container"></div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <i class="fas fa-calendar-plus me-2"></i>
                <h5 class="mb-0">Form Pengajuan Izin</h5>
            </div>
        </div>
        <div class="card-body">
            <form id="izinForm" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Tanggal Izin -->
                <div class="form-section">
                    <h6 class="form-section-title"><i class="fas fa-calendar-day"></i>Tanggal Izin</h6>
                    <div class="mb-3">
                        <label for="tanggal_izin" class="form-label">Pilih Tanggal</label>
                        <input type="text" class="form-control" id="tanggal_izin" name="tanggal_izin" placeholder="Klik untuk memilih tanggal" required>
                        <div class="invalid-feedback" id="tanggal_izin_error"></div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Maksimal 3 hari berturut-turut
                        </div>
                    </div>
                    <div id="selected_dates" class="mt-3">
                        <!-- Selected dates will appear here -->
                    </div>
                </div>

                <!-- Detail Izin -->
                <div class="form-section">
                    <h6 class="form-section-title"><i class="fas fa-info-circle"></i>Detail Izin</h6>

                    <div class="mb-3">
                        <label for="jenis_izin" class="form-label">Jenis Izin</label>
                        <select class="form-select" id="jenis_izin" name="jenis_izin" required>
                            <option value="" selected disabled>Pilih jenis izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Keperluan Keluarga">Keperluan Keluarga</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <div class="invalid-feedback" id="jenis_izin_error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Jelaskan alasan izin Anda secara detail" required></textarea>
                        <div class="invalid-feedback" id="keterangan_error"></div>
                    </div>
                </div>

                <!-- Bukti Pendukung -->
                <div class="form-section">
                    <h6 class="form-section-title"><i class="fas fa-file-upload"></i>Bukti Pendukung</h6>

                    <div class="mb-3">
                        <label for="bukti" class="form-label">Upload Bukti</label>
                        <label for="bukti" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                            <span>Pilih file atau seret ke sini</span>
                        </label>
                        <input type="file" id="bukti" name="bukti" class="form-control d-none" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="invalid-feedback" id="bukti_error"></div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Format file: PDF, JPG, PNG (Maks. 2MB)
                        </div>
                    </div>
                </div>

                <!-- Tips Pengajuan -->
                <!-- <div class="form-section">
                    <h6 class="form-section-title"><i class="fas fa-lightbulb"></i>Tips Pengajuan Izin</h6>

                    <ul class="tips-list">
                        <li>Ajukan izin minimal 3 hari sebelum tanggal mulai (kecuali sakit)</li>
                        <li>Pastikan bukti yang diunggah jelas dan sesuai dengan jenis izin</li>
                        <li>Untuk izin sakit lebih dari 2 hari, wajib melampirkan surat dokter</li>
                        <li>Cek status pengajuan izin Anda secara berkala</li>
                    </ul>
                </div> -->

                <!-- Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('pegawai/izin') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize flatpickr
        const fp = flatpickr("#tanggal_izin", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            locale: "id",
            minDate: "today",
            maxDate: new Date().fp_incr(30),
            disable: [
                function(date) {
                    // Hanya disable hari Minggu (day 0)
                    return date.getDay() === 0;
                }
            ],
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 3) {
                    const limitedDates = selectedDates.slice(0, 3);
                    instance.setDate(limitedDates, true);
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Maksimal 3 hari berturut-turut yang diperbolehkan',
                        icon: 'warning',
                        confirmButtonText: 'Mengerti'
                    });
                    return;
                }

                if (selectedDates.length > 1) {
                    selectedDates.sort((a, b) => a - b);
                    // Anggap selalu berurutan
                    let areConsecutive = true;

                    // Nonaktifkan validasi berurutan
                    /*
                    // Pendekatan yang lebih sederhana untuk validasi tanggal berurutan
                    // Cek selisih antara tanggal pertama dan terakhir
                    const firstDate = new Date(selectedDates[0]);
                    const lastDate = new Date(selectedDates[selectedDates.length - 1]);
                    
                    // Reset jam untuk perhitungan yang lebih akurat
                    const firstDateClean = new Date(firstDate.getFullYear(), firstDate.getMonth(), firstDate.getDate());
                    const lastDateClean = new Date(lastDate.getFullYear(), lastDate.getMonth(), lastDate.getDate());
                    
                    // Hitung selisih hari yang seharusnya
                    const expectedDays = selectedDates.length - 1;
                    
                    // Hitung selisih hari aktual
                    const diffTime = lastDateClean - firstDateClean;
                    const actualDays = diffTime / (1000 * 60 * 60 * 24);
                    
                    console.log(`Validasi berurutan: tanggal pertama=${firstDateClean.toISOString().split('T')[0]}, tanggal terakhir=${lastDateClean.toISOString().split('T')[0]}`);
                    console.log(`Validasi berurutan: seharusnya ${expectedDays} hari, aktual ${actualDays} hari`);
                    
                    // Berikan toleransi kecil untuk floating point
                    areConsecutive = (Math.abs(actualDays - expectedDays) < 0.01);
                    
                    console.log(`Hasil validasi: ${areConsecutive ? 'Berurutan' : 'Tidak berurutan'}`);
                    
                    if (!areConsecutive) {
                        instance.setDate([selectedDates[selectedDates.length - 1]], false);
                        Swal.fire({
                            title: 'Perhatian!',
                            text: 'Tanggal harus berurutan (hari kerja berturut-turut)',
                            icon: 'warning',
                            confirmButtonText: 'Mengerti'
                        });
                    }
                    */
                }

                updateSelectedDatesDisplay(selectedDates);

                // Clear validation error if exists
                document.getElementById('tanggal_izin').classList.remove('is-invalid');
                document.getElementById('tanggal_izin_error').textContent = '';
            }
        });

        // Update selected dates display
        function updateSelectedDatesDisplay(dates) {
            const container = document.getElementById('selected_dates');
            container.innerHTML = '';

            if (dates.length === 0) {
                container.innerHTML = '<p class="text-muted">Belum ada tanggal yang dipilih</p>';
                return;
            }

            dates.sort((a, b) => a - b);
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };

            dates.forEach(date => {
                const dateObj = new Date(date);
                const formattedDate = dateObj.toLocaleDateString('id-ID', options);

                const dateTag = document.createElement('div');
                dateTag.className = 'date-tag';
                dateTag.innerHTML = `
                    <i class="fas fa-calendar-day me-2"></i>
                    ${formattedDate}
                    <span class="remove-date" data-date="${dateObj.toISOString()}">&times;</span>
                `;

                container.appendChild(dateTag);
            });

            // Add event listeners to remove buttons
            document.querySelectorAll('.remove-date').forEach(btn => {
                btn.addEventListener('click', function() {
                    const dateToRemove = new Date(this.getAttribute('data-date'));
                    const currentDates = fp.selectedDates.filter(d =>
                        d.getTime() !== dateToRemove.getTime()
                    );
                    fp.setDate(currentDates, true);
                });
            });
        }

        // File upload preview
        const fileInput = document.getElementById('bukti');
        const fileLabel = document.querySelector('.file-upload-label span');

        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                fileLabel.textContent = fileName;

                // Clear validation error if exists
                this.classList.remove('is-invalid');
                document.getElementById('bukti_error').textContent = '';
            } else {
                fileLabel.textContent = 'Pilih file atau seret ke sini';
            }
        });

        // Remove validation errors on input change
        document.getElementById('jenis_izin').addEventListener('change', function() {
            this.classList.remove('is-invalid');
            document.getElementById('jenis_izin_error').textContent = '';
        });

        document.getElementById('keterangan').addEventListener('input', function() {
            this.classList.remove('is-invalid');
            document.getElementById('keterangan_error').textContent = '';
        });

        // Form submission with AJAX
        const form = document.getElementById('izinForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate form
            let isValid = true;

            const selectedDates = fp.selectedDates;
            if (selectedDates.length === 0) {
                document.getElementById('tanggal_izin').classList.add('is-invalid');
                document.getElementById('tanggal_izin_error').textContent = 'Silakan pilih minimal 1 tanggal izin';
                isValid = false;
            }

            const jenisIzin = document.getElementById('jenis_izin').value;
            if (!jenisIzin) {
                document.getElementById('jenis_izin').classList.add('is-invalid');
                document.getElementById('jenis_izin_error').textContent = 'Silakan pilih jenis izin';
                isValid = false;
            }

            const keterangan = document.getElementById('keterangan').value;
            if (!keterangan.trim()) {
                document.getElementById('keterangan').classList.add('is-invalid');
                document.getElementById('keterangan_error').textContent = 'Silakan isi keterangan izin';
                isValid = false;
            }

            // Validasi bukti dihapus karena upload bukti tidak wajib

            if (!isValid) {
                return;
            }

            // Create FormData object
            const formData = new FormData(form);

            // Update submit button to loading state
            const submitBtn = document.getElementById('submit-btn');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="loader"></div> Menyimpan...';

            // Send AJAX request
            fetch('<?= base_url('pegawai/izin/store') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        // Success
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        // Error
                        if (data.errors) {
                            // Display validation errors
                            Object.keys(data.errors).forEach(field => {
                                const element = document.getElementById(field);
                                if (element) {
                                    element.classList.add('is-invalid');
                                    const errorElement = document.getElementById(`${field}_error`);
                                    if (errorElement) {
                                        errorElement.textContent = data.errors[field];
                                    }
                                }
                            });
                        } else {
                            // Show error message
                            showAlert('danger', data.message);
                        }

                        // Reset submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Terjadi kesalahan saat mengirim data');

                    // Reset submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                });
        });

        // Show alert function
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            alertContainer.appendChild(alertDiv);

            // Auto dismiss after 5 seconds
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alertDiv);
                bsAlert.close();
            }, 5000);
        }
    });
</script>
<?= $this->endSection() ?>