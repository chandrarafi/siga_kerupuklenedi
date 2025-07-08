<?php
// File: app/Views/admin/izin/show.php
// Halaman detail izin untuk admin
?>

<?php if (isset($ajax) && $ajax): ?>
    <!-- Tampilan untuk AJAX modal -->
    <div class="p-3">
        <div class="mb-4">
            <?php if ($izin['statusizin'] == 1): ?>
                <div class="badge bg-success p-2 fs-6">
                    <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                </div>
            <?php elseif ($izin['statusizin'] == 2): ?>
                <div class="badge bg-danger p-2 fs-6">
                    <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                </div>
            <?php elseif ($izin['statusizin'] == 3): ?>
                <div class="badge bg-warning text-dark p-2 fs-6">
                    <i class="bi bi-clock-fill me-1"></i> Menunggu Persetujuan
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Informasi Pengajuan</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%" class="text-muted">ID Izin</td>
                        <td class="fw-bold"><?= $izin['idizin'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Izin</td>
                        <td class="fw-bold"><?= $izin['jenisizin'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Mulai</td>
                        <td class="fw-bold">
                            <?php
                            // Jika ada selected_dates, gunakan tanggal pertama dari array yang diurutkan
                            if (!empty($izin['selected_dates'])) {
                                $dates = explode(',', $izin['selected_dates']);
                                // Bersihkan dan trim setiap tanggal
                                $dates = array_map('trim', $dates);
                                $dates = array_filter($dates, function ($date) {
                                    return !empty($date);
                                });
                                sort($dates); // Urutkan tanggal
                                echo date('d F Y', strtotime(reset($dates))); // Tampilkan tanggal pertama
                            }
                            // Jika tidak ada selected_dates, gunakan cara lama
                            else {
                                $tglMulai = strtotime($izin['tanggalmulaiizin']);
                                $tglSelesai = strtotime($izin['tanggalselesaiizin']);

                                // Pastikan tanggal mulai tidak lebih besar dari tanggal selesai
                                if ($tglMulai <= $tglSelesai) {
                                    echo date('d F Y', $tglMulai);
                                } else {
                                    echo date('d F Y', $tglSelesai); // Tukar jika terbalik
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Selesai</td>
                        <td class="fw-bold">
                            <?php
                            // Jika ada selected_dates, gunakan tanggal terakhir dari array yang diurutkan
                            if (!empty($izin['selected_dates'])) {
                                $dates = explode(',', $izin['selected_dates']);
                                // Bersihkan dan trim setiap tanggal
                                $dates = array_map('trim', $dates);
                                $dates = array_filter($dates, function ($date) {
                                    return !empty($date);
                                });
                                sort($dates); // Urutkan tanggal
                                echo date('d F Y', strtotime(end($dates))); // Tampilkan tanggal terakhir
                            }
                            // Jika tidak ada selected_dates, gunakan cara lama
                            else if ($tglMulai <= $tglSelesai) {
                                echo date('d F Y', $tglSelesai);
                            } else {
                                echo date('d F Y', $tglMulai); // Tukar jika terbalik
                            }
                            ?>
                        </td>
                    </tr>
                    <?php if (!empty($izin['selected_dates'])): ?>
                        <tr>
                            <td class="text-muted">Tanggal Dipilih</td>
                            <td class="fw-bold">
                                <?php
                                $dates = explode(',', $izin['selected_dates']);
                                // Bersihkan dan trim setiap tanggal
                                $dates = array_map('trim', $dates);
                                $dates = array_filter($dates, function ($date) {
                                    return !empty($date);
                                });
                                // Urutkan tanggal
                                sort($dates);
                                foreach ($dates as $index => $date) {
                                    echo date('d F Y', strtotime(trim($date)));
                                    if ($index < count($dates) - 1) {
                                        echo '<br>';
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="text-muted">Durasi</td>
                        <td class="fw-bold">
                            <?php
                            if (!empty($izin['selected_dates'])) {
                                $dates = explode(',', $izin['selected_dates']);
                                // Bersihkan dan trim setiap tanggal
                                $dates = array_map('trim', $dates);
                                $dates = array_filter($dates, function ($date) {
                                    return !empty($date);
                                });
                                echo count($dates) . ' hari';
                            } else {
                                $start = new DateTime($izin['tanggalmulaiizin']);
                                $end = new DateTime($izin['tanggalselesaiizin']);
                                $interval = $start->diff($end);
                                echo $interval->days + 1 . ' hari';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pengajuan</td>
                        <td class="fw-bold"><?= date('d F Y H:i', strtotime($izin['created_at'])) ?></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Informasi Pegawai</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%" class="text-muted">Nama</td>
                        <td class="fw-bold"><?= $izin['namapegawai'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIK</td>
                        <td class="fw-bold"><?= $izin['nik'] ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">ID Pegawai</td>
                        <td class="fw-bold"><?= $izin['pegawai_id'] ?></td>
                    </tr>
                </table>
            </div>

            <div class="col-12">
                <h6 class="fw-bold mb-3">Alasan</h6>
                <p><?= nl2br($izin['alasan']) ?></p>
            </div>

            <?php if ($izin['statusizin'] == 2): ?>
                <div class="col-12 mb-3">
                    <h6 class="fw-bold mb-3">Lampiran</h6>
                    <?php
                    $ext = pathinfo($izin['lampiran'], PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                    ?>

                    <?php if ($isImage): ?>
                        <div class="mb-2">
                            <img src="<?= base_url('uploads/izin/' . $izin['lampiran']) ?>" alt="Lampiran" class="img-fluid img-thumbnail" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>

                    <a href="<?= base_url('uploads/izin/' . $izin['lampiran']) ?>" target="_blank" class="btn btn-sm btn-primary">
                        <i class="bi <?= $isImage ? 'bi-image' : 'bi-file-pdf' ?> me-1"></i> Lihat Lampiran
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!empty($izin['keterangan_admin'])): ?>
                <div class="col-12 mt-3">
                    <h6 class="fw-bold mb-3">Keterangan Admin</h6>
                    <p><?= nl2br($izin['keterangan_admin']) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($izin['statusizin'] == 3): ?>
                <div class="col-12 mt-3">
                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-3">Tindakan</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <form id="form-approve" action="javascript:void(0)">
                                    <div class="mb-3">
                                        <label for="keterangan_approve" class="form-label">Keterangan (Opsional)</label>
                                        <textarea id="keterangan_approve" name="keterangan" rows="3" class="form-control"></textarea>
                                    </div>
                                    <button type="button" class="btn btn-success w-100" onclick="approveIzin('<?= $izin['idizin'] ?>')">
                                        <i class="bi bi-check-circle-fill me-1"></i> Setujui
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form id="form-reject" action="javascript:void(0)">
                                    <div class="mb-3">
                                        <label for="keterangan_reject" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                        <textarea id="keterangan_reject" name="keterangan" rows="3" class="form-control" required></textarea>
                                    </div>
                                    <button type="button" class="btn btn-danger w-100" onclick="rejectIzin('<?= $izin['idizin'] ?>')">
                                        <i class="bi bi-x-circle-fill me-1"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Script untuk AJAX actions -->
        <script>
            function approveIzin(id) {
                const keterangan = document.getElementById('keterangan_approve').value;
                console.log('Approving izin:', id);

                // Create form data
                const formData = new FormData();
                formData.append('keterangan', keterangan);

                // Send AJAX request
                fetch(`<?= site_url('admin/izin/approve/') ?>${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response:', data);
                        if (data.status) {
                            alert(data.message);
                            // Close modal and refresh
                            window.parent.closeDetailModal();
                            window.parent.refreshIzinTable();
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat menyetujui izin');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    });
            }

            function rejectIzin(id) {
                const keterangan = document.getElementById('keterangan_reject').value;
                if (!keterangan) {
                    alert('Alasan penolakan harus diisi');
                    return;
                }

                console.log('Rejecting izin:', id);

                // Create form data
                const formData = new FormData();
                formData.append('keterangan', keterangan);

                // Send AJAX request
                fetch(`<?= site_url('admin/izin/reject/') ?>${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response:', data);
                        if (data.status) {
                            alert(data.message);
                            // Close modal and refresh
                            window.parent.closeDetailModal();
                            window.parent.refreshIzinTable();
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat menolak izin');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    });
            }
        </script>
    </div>
<?php else: ?>
    <!-- Tampilan halaman utuh -->
    <?= $this->extend('admin/layouts/main') ?>

    <?= $this->section('content') ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">Detail Pengajuan Izin</h6>
            <a href="<?= site_url('admin/izin') ?>" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <?php if ($izin['statusizin'] == 1) : ?>
                    <div class="badge bg-success p-2 fs-6">
                        <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                    </div>
                <?php elseif ($izin['statusizin'] == 2) : ?>
                    <div class="badge bg-danger p-2 fs-6">
                        <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                    </div>
                <?php elseif ($izin['statusizin'] == 3) : ?>
                    <div class="badge bg-warning text-dark p-2 fs-6">
                        <i class="bi bi-clock-fill me-1"></i> Menunggu Persetujuan
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header py-3 bg-light">
                            <h6 class="m-0 font-weight-bold">Informasi Pengajuan</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">ID Izin</label>
                                <p class="font-weight-bold"><?= $izin['idizin'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Jenis Izin</label>
                                <p class="font-weight-bold"><?= $izin['jenisizin'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Mulai</label>
                                <p class="font-weight-bold">
                                    <?php
                                    $tglMulai = strtotime($izin['tanggalmulaiizin']);
                                    $tglSelesai = strtotime($izin['tanggalselesaiizin']);

                                    // Pastikan tanggal mulai tidak lebih besar dari tanggal selesai
                                    if ($tglMulai <= $tglSelesai) {
                                        echo date('d F Y', $tglMulai);
                                    } else {
                                        echo date('d F Y', $tglSelesai); // Tukar jika terbalik
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Selesai</label>
                                <p class="font-weight-bold">
                                    <?php
                                    // Pastikan tanggal selesai tidak lebih kecil dari tanggal mulai
                                    if ($tglMulai <= $tglSelesai) {
                                        echo date('d F Y', $tglSelesai);
                                    } else {
                                        echo date('d F Y', $tglMulai); // Tukar jika terbalik
                                    }
                                    ?>
                                </p>
                            </div>
                            <?php if (!empty($izin['selected_dates'])): ?>
                                <div class="mb-3">
                                    <label class="text-muted small">Tanggal Dipilih</label>
                                    <p class="font-weight-bold">
                                        <?php
                                        $dates = explode(',', $izin['selected_dates']);
                                        foreach ($dates as $index => $date) {
                                            echo date('d F Y', strtotime(trim($date)));
                                            if ($index < count($dates) - 1) {
                                                echo '<br>';
                                            }
                                        }
                                        ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="text-muted small">Durasi</label>
                                <p class="font-weight-bold">
                                    <?php
                                    if (!empty($izin['selected_dates'])) {
                                        $dates = explode(',', $izin['selected_dates']);
                                        echo count($dates) . ' hari';
                                    } else {
                                        $start = new DateTime($izin['tanggalmulaiizin']);
                                        $end = new DateTime($izin['tanggalselesaiizin']);
                                        $interval = $start->diff($end);
                                        echo $interval->days + 1 . ' hari';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Pengajuan</label>
                                <p class="font-weight-bold"><?= date('d F Y H:i', strtotime($izin['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card mb-4 h-100">
                        <div class="card-header py-3 bg-light">
                            <h6 class="m-0 font-weight-bold">Informasi Pegawai</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">ID Pegawai</label>
                                <p class="font-weight-bold"><?= $izin['pegawai_id'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Nama Pegawai</label>
                                <p class="font-weight-bold"><?= $izin['namapegawai'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">NIK</label>
                                <p class="font-weight-bold"><?= $izin['nik'] ?? '-' ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header py-3 bg-light">
                            <h6 class="m-0 font-weight-bold">Detail Pengajuan</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">Alasan</label>
                                <p class="font-weight-bold"><?= nl2br($izin['alasan']) ?></p>
                            </div>

                            <?php if (!empty($izin['lampiran'])) : ?>
                                <div class="mb-3">
                                    <label class="text-muted small">Lampiran</label>
                                    <div>
                                        <?php
                                        $ext = pathinfo($izin['lampiran'], PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                                        ?>

                                        <?php if ($isImage) : ?>
                                            <div class="mb-3">
                                                <img src="<?= base_url('uploads/izin/' . $izin['lampiran']) ?>" alt="Lampiran" class="img-fluid img-thumbnail" style="max-height: 300px;">
                                            </div>
                                        <?php endif; ?>

                                        <a href="<?= base_url('uploads/izin/' . $izin['lampiran']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="bi <?= $isImage ? 'bi-image' : 'bi-file-pdf' ?> me-1"></i> Lihat Lampiran
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($izin['statusizin'] == 2) : ?>
                                <div class="mt-4 pt-3 border-top">
                                    <label class="text-muted small">Keterangan Admin</label>
                                    <p class="font-weight-bold"><?= nl2br($izin['keterangan_admin']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($izin['statusizin'] == 3) : ?>
                    <div class="col-md-12 mt-4">
                        <div class="card">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold">Tindakan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <form action="<?= site_url('admin/izin/approve/' . $izin['idizin']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="mb-3">
                                                <label for="keterangan_approve" class="form-label">Keterangan (Opsional)</label>
                                                <textarea id="keterangan_approve" name="keterangan" rows="3" class="form-control"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="bi bi-check-circle-fill me-1"></i> Setujui
                                            </button>
                                        </form>
                                    </div>

                                    <div class="col-md-6">
                                        <form action="<?= site_url('admin/izin/reject/' . $izin['idizin']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="mb-3">
                                                <label for="keterangan_reject" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                <textarea id="keterangan_reject" name="keterangan" rows="3" class="form-control" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="bi bi-x-circle-fill me-1"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?= $this->endSection() ?>
<?php endif; ?>