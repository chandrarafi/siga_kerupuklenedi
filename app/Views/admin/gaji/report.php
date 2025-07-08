<?php
// File: app/Views/admin/gaji/report.php
// Halaman laporan gaji untuk admin
?>

<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Laporan Gaji</h6>
        <div class="dropdown no-arrow">
            <a href="<?= site_url('admin/gaji') ?>" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button onclick="printReport()" class="btn btn-sm btn-primary">
                <i class="bi bi-printer me-1"></i> Cetak
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <div class="bg-light p-3 rounded mb-4">
            <form id="filterForm" action="<?= site_url('admin/gaji/report') ?>" method="get">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-6">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select id="bulan" name="bulan" class="form-select">
                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?= $filter['bulan'] == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select id="tahun" name="tahun" class="form-select">
                            <?php
                            $currentYear = date('Y');
                            for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) :
                            ?>
                                <option value="<?= $i ?>" <?= $filter['tahun'] == $i ? 'selected' : '' ?>>
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" <?= $filter['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="paid" <?= $filter['status'] == 'paid' ? 'selected' : '' ?>>Dibayar</option>
                            <option value="cancelled" <?= $filter['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-6 d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div id="printArea">
            <div class="text-center mb-4 d-none" id="reportHeader">
                <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                    <img src="<?= base_url('image/logo.png') ?>" alt="Logo" style="height: 60px; margin-right: 15px;">
                    <div>
                        <h4>SISTEM INFORMASI KERUPUK LEN EDI</h4>
                        <p>Pauh Kambar, Kabupaten Padang Pariaman</p>
                        <p>Telp: (0751) 123456 | Email: info@kerupuklenedi.com</p>
                    </div>
                </div>
                <h5>Laporan Gaji Periode <?= date('F Y', strtotime('01-' . $filter['periode'])) ?></h5>
            </div>

            <?php if (empty($gaji_list)) : ?>
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> Tidak ada data gaji yang ditemukan untuk periode ini.
                </div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="reportTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>NIK</th>
                                <th>Nama Pegawai</th>
                                <th>No. Slip</th>
                                <th>Tanggal</th>
                                <th>Total Absen</th>
                                <th>Total Lembur</th>
                                <th>Potongan</th>
                                <th>Gaji Bersih</th>
                                <th class="no-print">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($gaji_list as $gaji) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $gaji['nik'] ?></td>
                                    <td><?= $gaji['namapegawai'] ?></td>
                                    <td><?= $gaji['noslip'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($gaji['tanggal'])) ?></td>
                                    <td><?= $gaji['totalabsen'] ?> hari</td>
                                    <td><?= $gaji['totallembur'] ?> jam</td>
                                    <td>Rp <?= number_format($gaji['potongan'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($gaji['gajibersih'], 0, ',', '.') ?></td>
                                    <td class="no-print">
                                        <?php if ($gaji['status'] == 'pending') : ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif ($gaji['status'] == 'paid') : ?>
                                            <span class="badge bg-success">Dibayar</span>
                                        <?php elseif ($gaji['status'] == 'cancelled') : ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4 d-none" id="reportFooter">
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-4 text-end">
                        <p>
                            Padang, <?= date('d F Y') ?><br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <strong>HRD / Keuangan</strong>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #printArea,
        #printArea * {
            visibility: visible;
        }

        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-header,
        .card-body {
            padding: 0 !important;
        }

        #reportHeader,
        #reportFooter {
            display: block !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        const table = new DataTable('#reportTable', {
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });

    function printReport() {
        // Tampilkan header dan footer laporan
        document.getElementById('reportHeader').classList.remove('d-none');
        document.getElementById('reportFooter').classList.remove('d-none');

        // Cetak
        window.print();

        // Sembunyikan kembali header dan footer setelah dicetak
        setTimeout(function() {
            document.getElementById('reportHeader').classList.add('d-none');
            document.getElementById('reportFooter').classList.add('d-none');
        }, 100);
    }
</script>
<?= $this->endSection() ?>