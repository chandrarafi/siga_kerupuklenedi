<?php
// File: app/Views/pegawai/izin/index.php
// Halaman daftar pengajuan izin untuk pegawai
?>

<?= $this->extend('pegawai/layouts/main') ?>

<?= $this->section('style') ?>
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<style>
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        color: white;
        transition: all 0.2s;
        margin-right: 0.25rem;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        color: white;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Card Utama -->
<div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg mb-6 overflow-hidden animate-fade-in">
    <div class="p-4 md:p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl md:text-2xl font-bold mb-1">Pengajuan Izin</h2>
                <p class="text-blue-100 text-sm">Kelola pengajuan izin, cuti dan sakit</p>
            </div>
            <a href="<?= site_url('pegawai/izin/create') ?>" class="py-2 px-4 bg-white text-blue-600 font-semibold rounded-lg transition-all hover:bg-blue-50 flex items-center justify-center gap-2 shadow-md hover:scale-105 transform duration-300">
                <i class="fas fa-plus-circle"></i>
                <span>Ajukan Izin Baru</span>
            </a>
        </div>
    </div>
</div>

<!-- Daftar Pengajuan -->
<div class="bg-white rounded-xl shadow-md overflow-hidden mb-6 animate-slide-down animation-delay-150">
    <div class="p-4 border-b flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">Daftar Pengajuan Izin</h2>
        <button id="btn-refresh" class="text-blue-500 hover:text-blue-700 text-sm font-medium flex items-center hover:scale-110 transition-transform duration-300">
            <i class="fas fa-sync-alt mr-1"></i>
            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>

    <div id="izin-table-container">
        <?php if (empty($izin_list)): ?>
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-3"></i>
                <p class="text-lg font-medium text-gray-700 mb-1">Belum Ada Pengajuan</p>
                <p class="text-sm text-gray-500 mb-4">Anda belum pernah mengajukan izin, cuti atau sakit</p>
                <a href="<?= site_url('pegawai/izin/create') ?>" class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-all duration-300 gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>Ajukan Izin Baru</span>
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <?= $this->include('pegawai/izin/_table') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Informasi Pengajuan Izin -->
<div class="bg-white rounded-xl shadow-md overflow-hidden mb-4 animate-slide-down animation-delay-300">
    <div class="p-4 border-b">
        <h2 class="text-lg font-bold text-gray-800">Informasi Pengajuan Izin</h2>
    </div>
    <div class="p-4 space-y-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 bg-blue-100 text-blue-500 p-2 rounded-full">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-1">Jenis Pengajuan</h3>
                <p class="text-sm text-gray-600 mb-2">Terdapat 3 jenis pengajuan yang dapat digunakan:</p>
                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-2">
                    <li><span class="font-medium">Izin:</span> Untuk keperluan pribadi dengan alasan yang jelas</li>
                    <li><span class="font-medium">Sakit:</span> Untuk kondisi kesehatan yang memerlukan istirahat (disertai bukti/surat dokter)</li>
                    <li><span class="font-medium">Cuti:</span> Untuk pengambilan hak cuti tahunan</li>
                </ul>
            </div>
        </div>

        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 bg-yellow-100 text-yellow-500 p-2 rounded-full">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-1">Perhatian</h3>
                <p class="text-sm text-gray-600">Pengajuan izin harus dilakukan minimal 1 hari sebelumnya, kecuali untuk sakit yang dapat diajukan pada hari yang sama. Pengajuan akan disetujui atau ditolak oleh admin/atasan.</p>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

<!-- Modal Detail Izin -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-lg">Detail Pengajuan Izin</h3>
            <button id="close-detail-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="detail-content" class="p-4">
            <div class="flex justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toast Notification
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container');

            // Create toast element
            const toast = document.createElement('div');
            toast.className = 'flex items-center p-3 rounded-lg shadow-lg max-w-xs transition-all transform translate-x-full';

            // Set background color based on type
            if (type === 'success') {
                toast.classList.add('bg-green-500', 'text-white');
            } else if (type === 'error') {
                toast.classList.add('bg-red-500', 'text-white');
            } else if (type === 'warning') {
                toast.classList.add('bg-yellow-500', 'text-white');
            } else {
                toast.classList.add('bg-blue-500', 'text-white');
            }

            // Set icon based on type
            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';
            if (type === 'warning') icon = 'exclamation-triangle';

            // Create toast content
            toast.innerHTML = `
                <div class="flex-shrink-0 mr-2">
                    <i class="fas fa-${icon}"></i>
                </div>
                <div class="flex-1">${message}</div>
                <div class="ml-2 flex-shrink-0 cursor-pointer" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </div>
            `;

            // Add toast to container
            toastContainer.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 10);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 5000);
        }

        // Handle Detail Modal
        const detailModal = document.getElementById('detail-modal');
        const closeDetailModal = document.getElementById('close-detail-modal');
        const detailContent = document.getElementById('detail-content');

        // Close modal when clicking the close button
        if (closeDetailModal) {
            closeDetailModal.addEventListener('click', function() {
                detailModal.classList.add('hidden');
            });
        }

        // Close modal when clicking outside
        if (detailModal) {
            detailModal.addEventListener('click', function(e) {
                if (e.target === detailModal) {
                    detailModal.classList.add('hidden');
                }
            });
        }

        // Handle refresh button
        const refreshBtn = document.getElementById('btn-refresh');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                const tableContainer = document.getElementById('izin-table-container');
                tableContainer.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <div class="animate-spin inline-block w-8 h-8 border-b-2 border-gray-300 rounded-full mb-3"></div>
                        <p>Memuat data...</p>
                    </div>
                `;

                // Fetch updated data
                fetch('<?= site_url('pegawai/izin?ajax=1') ?>')
                    .then(response => response.text())
                    .then(html => {
                        tableContainer.innerHTML = html;
                        // Reinitialize detail buttons after refresh
                        initDetailButtons();
                        showToast('Data berhasil diperbarui', 'success');
                    })
                    .catch(error => {
                        tableContainer.innerHTML = `
                            <div class="p-8 text-center text-red-500">
                                <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                                <p>Terjadi kesalahan saat memuat data</p>
                            </div>
                        `;
                        showToast('Gagal memperbarui data', 'error');
                        console.error('Error refreshing data:', error);
                    });
            });
        }

        // Initialize detail buttons function
        function initDetailButtons() {
            document.querySelectorAll('.btn-detail').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    console.log('Fetching detail for ID:', id);

                    detailContent.innerHTML = `
                        <div class="flex justify-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                        </div>
                    `;
                    detailModal.classList.remove('hidden');

                    // Fetch detail data
                    const url = `<?= site_url('pegawai/izin/show/') ?>${id}?ajax=1`;
                    console.log('Fetching URL:', url);

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.text();
                        })
                        .then(html => {
                            detailContent.innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Error fetching details:', error);
                            detailContent.innerHTML = `
                                <div class="text-center text-red-500">
                                    <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                                    <p>Terjadi kesalahan saat memuat data</p>
                                    <p class="text-xs mt-2">${error.message}</p>
                                </div>
                            `;
                        });
                });
            });
        }

        // Initialize detail buttons on page load
        initDetailButtons();
    });
</script>
<?= $this->endSection() ?>