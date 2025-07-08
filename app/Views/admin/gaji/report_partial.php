<div class="card shadow" id="reportCard">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Data Gaji</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($filter['bulan']) || !empty($filter['tahun']) || !empty($filter['status'])): ?>
            <div class="mb-3">
                <h6>Filter yang digunakan:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($filter['bulan']) && !empty($filter['tahun'])): ?>
                        <?php
                        $bulan_list = [
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember'
                        ];
                        $bulan_nama = $bulan_list[$filter['bulan']] ?? $filter['bulan'];
                        ?>
                        <span class="badge bg-info">Periode: <?= $bulan_nama . ' ' . $filter['tahun'] ?></span>
                    <?php endif; ?>

                    <?php if (!empty($filter['status'])): ?>
                        <span class="badge bg-primary">Status: <?= ucfirst($filter['status']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Lembur</th>
                        <th>Potongan</th>
                        <th>Gaji Bersih</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($gaji_list)): ?>
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1;
                        foreach ($gaji_list as $gaji): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $gaji['nik'] ?></td>
                                <td><?= $gaji['namapegawai'] ?></td>
                                <td><?= $gaji['namajabatan'] ?? '-' ?></td>
                                <td class="text-right">
                                    <?php
                                    // Ambil data gaji pokok dari database
                                    $db = \Config\Database::connect();
                                    $pegawai = $db->table('pegawai')
                                        ->select('jabatan.gajipokok')
                                        ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid')
                                        ->where('pegawai.idpegawai', $gaji['pegawai_id'])
                                        ->get()
                                        ->getRowArray();
                                    $gajiPokok = $pegawai ? $pegawai['gajipokok'] : 0;
                                    ?>
                                    Rp <?= number_format($gajiPokok, 0, ',', '.') ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    // Ambil data tunjangan dari database
                                    $pegawai = $db->table('pegawai')
                                        ->select('jabatan.tunjangan')
                                        ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid')
                                        ->where('pegawai.idpegawai', $gaji['pegawai_id'])
                                        ->get()
                                        ->getRowArray();

                                    $tunjanganPenuh = $pegawai ? $pegawai['tunjangan'] : 0;
                                    $tunjanganPerHari = $tunjanganPenuh / 30;
                                    $tunjangan = $tunjanganPerHari * $gaji['totalabsen'];
                                    ?>
                                    Rp <?= number_format($tunjangan, 0, ',', '.') ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    // Hitung lembur dengan tarif tetap Rp 20.000 per jam
                                    $tarifLembur = 20000;
                                    $upahLembur = $gaji['totallembur'] * $tarifLembur;
                                    ?>
                                    Rp <?= number_format($upahLembur, 0, ',', '.') ?>
                                </td>
                                <td class="text-right">Rp <?= number_format($gaji['potongan'], 0, ',', '.') ?></td>
                                <td class="text-right">Rp <?= number_format($gaji['gajibersih'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <?php if ($gaji['status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif ($gaji['status'] == 'paid'): ?>
                                        <span class="badge bg-success">Dibayar</span>
                                    <?php elseif ($gaji['status'] == 'cancelled'): ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" class="text-right">Total</th>
                        <th class="text-right">Rp <?= number_format($total_gaji, 0, ',', '.') ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3">
            <p>
                <strong>Jumlah Data:</strong> <?= count($gaji_list) ?> data
                <?php if (!empty($filter['periode'])): ?>
                    | <strong>Periode:</strong> <?= date('F Y', strtotime('01-' . $filter['periode'])) ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>