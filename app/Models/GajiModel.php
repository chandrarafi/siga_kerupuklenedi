<?php

namespace App\Models;

use CodeIgniter\Model;

class GajiModel extends Model
{
    protected $table            = 'gaji';
    protected $primaryKey       = 'idgaji';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idgaji',
        'noslip',
        'pegawai_id',
        'periode',
        'tanggal',
        'totalabsen',
        'totallembur',
        'potongan',
        'gajibersih',
        'metodepembayaran',
        'status',
        'keterangan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = null;

    // Validation
    protected $validationRules      = [
        'pegawai_id' => 'required',
        'periode' => 'required',
        'tanggal' => 'required|valid_date',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generate ID gaji unik
     */
    public function generateIdGaji()
    {
        $date = date('Ymd');
        $lastId = $this->selectMax('idgaji')->first();

        if ($lastId) {
            $lastIdNumber = (int) substr($lastId['idgaji'], -3);
            $newIdNumber = $lastIdNumber + 1;
        } else {
            $newIdNumber = 1;
        }

        return 'GJI' . $date . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate nomor slip gaji unik
     */
    public function generateNoSlip($periode)
    {
        // Format periode: MM-YYYY
        $periode = str_replace('-', '', $periode);
        $lastSlip = $this->select('noslip')
            ->like('noslip', "SLIP{$periode}")
            ->orderBy('noslip', 'DESC')
            ->first();

        if ($lastSlip) {
            $lastNumber = (int) substr($lastSlip['noslip'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'SLIP' . $periode . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menghitung total absensi pegawai dalam periode tertentu
     * 
     * @param int $pegawaiId ID pegawai
     * @param string $periode Format: MM-YYYY
     * @return float Total hari kerja
     */
    public function hitungTotalAbsensi($pegawaiId, $periode)
    {
        try {
            // Pisahkan bulan dan tahun
            list($bulan, $tahun) = explode('-', $periode);

            $db = \Config\Database::connect();
            $builder = $db->table('absensi');

            // Hitung total hari kerja (status = 'hadir')
            $result = $builder->select('COUNT(*) as total_hari')
                ->where('idpegawai', $pegawaiId)
                ->where('MONTH(tanggal)', $bulan)
                ->where('YEAR(tanggal)', $tahun)
                ->where('status', 'hadir')
                ->get()
                ->getRow();

            return $result ? (float)$result->total_hari : 0;
        } catch (\Exception $e) {
            log_message('error', 'Error pada hitungTotalAbsensi: ' . $e->getMessage());
            return 0; // Return 0 jika terjadi error
        }
    }

    /**
     * Menghitung total jam lembur pegawai dalam periode tertentu
     * 
     * @param int $pegawaiId ID pegawai
     * @param string $periode Format: MM-YYYY
     * @return float Total jam lembur
     */
    public function hitungTotalLembur($pegawaiId, $periode)
    {
        try {
            // Pisahkan bulan dan tahun
            list($bulan, $tahun) = explode('-', $periode);

            $db = \Config\Database::connect();
            $builder = $db->table('lembur');

            // Hitung total durasi lembur dalam menit
            $result = $builder->select('SUM(TIMESTAMPDIFF(MINUTE, jammulai, jamselesai)) as total_menit')
                ->where('pegawai_id', $pegawaiId)
                ->where('MONTH(tanggallembur)', $bulan)
                ->where('YEAR(tanggallembur)', $tahun)
                ->get()
                ->getRow();

            // Konversi menit ke jam
            $totalMenit = $result ? (float)$result->total_menit : 0;
            $totalJam = $totalMenit / 60;

            return $totalJam;
        } catch (\Exception $e) {
            log_message('error', 'Error pada hitungTotalLembur: ' . $e->getMessage());
            return 0; // Return 0 jika terjadi error
        }
    }

    /**
     * Menghitung potongan keterlambatan pegawai dalam periode tertentu
     * 
     * @param int $pegawaiId ID pegawai
     * @param string $periode Format: MM-YYYY
     * @param float $potonganPerMenit Nilai potongan per menit keterlambatan
     * @return float Total potongan
     */
    public function hitungPotonganKeterlambatan($pegawaiId, $periode, $potonganPerMenit = 1000)
    {
        try {
            // Pisahkan bulan dan tahun
            list($bulan, $tahun) = explode('-', $periode);

            $db = \Config\Database::connect();
            $builder = $db->table('absensi');

            // Hitung total keterlambatan dalam menit
            $result = $builder->select('SUM(terlambat) as total_terlambat')
                ->where('idpegawai', $pegawaiId)
                ->where('MONTH(tanggal)', $bulan)
                ->where('YEAR(tanggal)', $tahun)
                ->get()
                ->getRow();

            $totalMenitTerlambat = $result ? (float)$result->total_terlambat : 0;
            $totalPotongan = $totalMenitTerlambat * $potonganPerMenit;

            return $totalPotongan;
        } catch (\Exception $e) {
            log_message('error', 'Error pada hitungPotonganKeterlambatan: ' . $e->getMessage());
            return 0; // Return 0 jika terjadi error
        }
    }

    /**
     * Menghitung gaji bersih pegawai
     * 
     * @param int $pegawaiId ID pegawai
     * @param string $periode Format: MM-YYYY
     * @return array Data perhitungan gaji
     */
    public function hitungGaji($pegawaiId, $periode)
    {
        try {
            $db = \Config\Database::connect();
            $pegawaiModel = new PegawaiModel();

            // Validasi pegawaiId
            if (empty($pegawaiId)) {
                return [
                    'status' => false,
                    'message' => 'ID Pegawai tidak boleh kosong'
                ];
            }

            // Validasi format periode
            if (!preg_match('/^\d{2}-\d{4}$/', $periode)) {
                return [
                    'status' => false,
                    'message' => 'Format periode tidak valid (harus MM-YYYY)'
                ];
            }

            // Ambil data pegawai
            $pegawai = $pegawaiModel->find($pegawaiId);
            if (!$pegawai) {
                return [
                    'status' => false,
                    'message' => 'Pegawai tidak ditemukan'
                ];
            }

            // Ambil data jabatan dan gaji pokok
            $builder = $db->table('pegawai');
            $builder->select('pegawai.*, jabatan.namajabatan as nama_jabatan, jabatan.gajipokok, jabatan.tunjangan, bagian.namabagian');
            $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid');
            $builder->join('bagian', 'bagian.idbagian = jabatan.bagianid');
            $builder->where('pegawai.idpegawai', $pegawaiId);
            $dataPegawai = $builder->get()->getRowArray();

            if (!$dataPegawai) {
                return [
                    'status' => false,
                    'message' => 'Data jabatan pegawai tidak ditemukan'
                ];
            }

            // Hitung komponen gaji
            $totalAbsensi = $this->hitungTotalAbsensi($pegawaiId, $periode);
            $totalLembur = $this->hitungTotalLembur($pegawaiId, $periode);

            // Hitung gaji bersih dengan rumus baru
            $gajiPokok = $dataPegawai['gajipokok'];

            // Tunjangan tergantung kehadiran
            // Asumsi hari kerja dalam sebulan adalah 30 hari
            $hariKerjaNormal = 30;
            $tunjanganPenuh = $dataPegawai['tunjangan'];
            $tunjanganPerHari = $tunjanganPenuh / $hariKerjaNormal;
            $tunjangan = $tunjanganPerHari * $totalAbsensi;

            // Lembur dengan tarif Rp 20.000 per jam
            $tarifLembur = 20000; // Tarif lembur per jam
            $upahLembur = $totalLembur * $tarifLembur;

            // Hitung gaji bersih tanpa potongan
            $gajiBruto = $gajiPokok + $tunjangan + $upahLembur;
            $gajiBersih = $gajiBruto;

            return [
                'status' => true,
                'data' => [
                    'pegawai' => $dataPegawai,
                    'periode' => $periode,
                    'tanggal' => date('Y-m-d'),
                    'komponen_gaji' => [
                        'gaji_pokok' => $gajiPokok,
                        'tunjangan' => $tunjangan,
                        'upah_lembur' => $upahLembur,
                        'gaji_bruto' => $gajiBruto,
                        'potongan' => 0,
                        'gaji_bersih' => $gajiBersih
                    ],
                    'detail' => [
                        'total_absensi' => $totalAbsensi,
                        'total_lembur' => $totalLembur,
                        'tunjangan_penuh' => $tunjanganPenuh,
                        'tunjangan_per_hari' => $tunjanganPerHari,
                        'tarif_lembur' => $tarifLembur,
                    ]
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error pada model hitungGaji: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghitung gaji: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mendapatkan data gaji dengan informasi pegawai
     */
    public function getGajiWithPegawai($id = null)
    {
        $builder = $this->db->table('gaji');
        $builder->select('gaji.*, pegawai.namapegawai, pegawai.nik');
        $builder->join('pegawai', 'pegawai.idpegawai = gaji.pegawai_id');

        if ($id !== null) {
            if (is_numeric($id)) {
                $builder->where('gaji.idgaji', $id);
            } else {
                $builder->where('gaji.idgaji', $id);
            }
            return $builder->get()->getRowArray();
        }

        return $builder->orderBy('gaji.tanggal', 'DESC')->get()->getResultArray();
    }

    /**
     * Mendapatkan laporan gaji berdasarkan periode
     */
    public function getLaporanGaji($periode)
    {
        return $this->select('gaji.*, pegawai.namapegawai, pegawai.nik')
            ->join('pegawai', 'pegawai.idpegawai = gaji.pegawai_id')
            ->where('periode', $periode)
            ->orderBy('tanggal', 'DESC')
            ->findAll();
    }
}
