<?php

namespace App\Models;

use CodeIgniter\Model;

class LemburModel extends Model
{
    protected $table            = 'lembur';
    protected $primaryKey       = 'idlembur';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idlembur',
        'pegawai_id',
        'tanggallembur',
        'jammulai',
        'jamselesai',
        'alasan'
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
        'tanggallembur' => 'required|valid_date',
        'jammulai' => 'required',
        'jamselesai' => 'required',
        'alasan' => 'required'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Mengambil data lembur berdasarkan ID pegawai
     */
    public function getLemburByPegawai($pegawaiId)
    {
        return $this->where('pegawai_id', $pegawaiId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Generate ID lembur unik
     */
    public function generateIdLembur()
    {
        $date = date('Ymd');
        $lastId = $this->selectMax('idlembur')->first();

        if ($lastId) {
            $lastIdNumber = (int) substr($lastId['idlembur'], -3);
            $newIdNumber = $lastIdNumber + 1;
        } else {
            $newIdNumber = 1;
        }

        return 'LBR' . $date . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Mengambil laporan lembur berdasarkan bulan dan tahun
     */
    public function getLaporanLembur($bulan, $tahun)
    {
        $startDate = $tahun . '-' . $bulan . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        return $this->select('lembur.*, pegawai.namapegawai as nama_pegawai')
            ->join('pegawai', 'pegawai.idpegawai = lembur.pegawai_id')
            ->where('tanggallembur >=', $startDate)
            ->where('tanggallembur <=', $endDate)
            ->orderBy('tanggallembur', 'ASC')
            ->findAll();
    }

    // Mendapatkan data lembur dengan informasi pegawai
    public function getLemburWithPegawai($id = null)
    {
        $builder = $this->db->table('lembur');
        $builder->select('lembur.*, pegawai.namapegawai, pegawai.nik');
        $builder->join('pegawai', 'pegawai.idpegawai = lembur.pegawai_id');

        if ($id !== null) {
            if (is_numeric($id)) {
                $builder->where('lembur.id', $id);
            } else {
                $builder->where('lembur.idlembur', $id);
            }
            return $builder->get()->getRowArray();
        }

        return $builder->orderBy('lembur.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Menghitung durasi lembur dalam menit
     */
    public function hitungDurasiLembur($jammulai, $jamselesai)
    {
        $time1 = strtotime($jammulai);
        $time2 = strtotime($jamselesai);

        // Jika jamselesai lebih kecil dari jammulai, berarti melewati tengah malam
        if ($time2 < $time1) {
            $time2 += 86400; // Tambah 24 jam
        }

        return round(abs($time2 - $time1) / 60); // Durasi dalam menit
    }
}
