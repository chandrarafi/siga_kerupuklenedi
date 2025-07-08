<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <h1 class="content-title">Pengaturan Lokasi Kantor</h1>
</div>

<div class="content-body">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group mb-3">
                        <label for="office-name">Nama Lokasi</label>
                        <input type="text" id="office-name" class="form-control" value="<?= $setting['name'] ?? 'PT Menara Agung' ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label for="office-address">Alamat Lengkap</label>
                        <textarea id="office-address" class="form-control" rows="3"><?= $setting['address'] ?? 'Jl. Veteran No.30, Padang Pasir, Kec. Padang Bar., Kota Padang' ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="office-radius">Radius Maksimum (meter)</label>
                        <input type="number" id="office-radius" class="form-control" value="<?= $setting['radius'] ?? 100 ?>" min="10" max="1000">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="office-latitude">Latitude</label>
                                <input type="text" id="office-latitude" class="form-control" value="<?= $setting['latitude'] ?? '-0.9467468' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="office-longitude">Longitude</label>
                                <input type="text" id="office-longitude" class="form-control" value="<?= $setting['longitude'] ?? '100.3534272' ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <button id="btn-save" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                    </button>
                </div>

                <div class="col-md-7">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Klik pada peta untuk menentukan lokasi kantor. Lingkaran biru menunjukkan radius maksimum untuk absensi.
                    </div>

                    <div id="map" style="height: 400px; border: 1px solid #ddd; border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Leaflet JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let map;
        let marker;
        let circle;

        // Inisialisasi peta
        function initMap() {
            // Ambil koordinat dari input
            const lat = parseFloat(document.getElementById('office-latitude').value) || -0.9467468;
            const lng = parseFloat(document.getElementById('office-longitude').value) || 100.3534272;
            const radius = parseInt(document.getElementById('office-radius').value) || 100;

            // Inisialisasi peta
            map = L.map('map').setView([lat, lng], 16);

            // Tambahkan layer peta
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Tambahkan marker
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            // Tambahkan circle untuk radius
            circle = L.circle([lat, lng], {
                color: '#3b82f6',
                fillColor: '#93c5fd',
                fillOpacity: 0.2,
                radius: radius
            }).addTo(map);

            // Event ketika marker di-drag
            marker.on('drag', function(e) {
                const position = e.target.getLatLng();
                circle.setLatLng(position);
                updateCoordinates(position.lat, position.lng);
            });

            // Event ketika map diklik
            map.on('click', function(e) {
                const position = e.latlng;
                marker.setLatLng(position);
                circle.setLatLng(position);
                updateCoordinates(position.lat, position.lng);
            });

            // Event untuk update circle ketika radius berubah
            document.getElementById('office-radius').addEventListener('input', function(e) {
                const radius = parseInt(e.target.value) || 100;
                circle.setRadius(radius);
            });
        }

        function updateCoordinates(lat, lng) {
            document.getElementById('office-latitude').value = lat.toFixed(8);
            document.getElementById('office-longitude').value = lng.toFixed(8);
        }

        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container');

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

            toastContainer.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast, {
                delay: 5000
            });

            bsToast.show();
        }

        // Save settings
        document.getElementById('btn-save').addEventListener('click', function() {
            const data = {
                name: document.getElementById('office-name').value,
                address: document.getElementById('office-address').value,
                latitude: document.getElementById('office-latitude').value,
                longitude: document.getElementById('office-longitude').value,
                radius: document.getElementById('office-radius').value
            };

            // Validasi input
            if (!data.name.trim()) {
                showToast('Nama lokasi tidak boleh kosong', 'error');
                return;
            }

            if (!data.address.trim()) {
                showToast('Alamat tidak boleh kosong', 'error');
                return;
            }

            if (isNaN(data.radius) || data.radius < 10 || data.radius > 1000) {
                showToast('Radius harus antara 10-1000 meter', 'error');
                return;
            }

            fetch('<?= site_url('admin/settings/save-office-location') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.status) {
                        showToast(result.message, 'success');
                    } else {
                        showToast(result.message || 'Terjadi kesalahan saat menyimpan pengaturan', 'error');
                        console.error('Error details:', result);
                    }
                })
                .catch(error => {
                    showToast('Terjadi kesalahan saat menyimpan pengaturan', 'error');
                    console.error('Error:', error);
                });
        });

        // Initialize map
        initMap();
    });
</script>
<?= $this->endSection() ?>