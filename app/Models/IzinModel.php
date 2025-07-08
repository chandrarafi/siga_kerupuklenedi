<?php

namespace App\Models;

use CodeIgniter\Model;

class IzinModel extends Model
{
    protected $table            = 'izin';
    protected $primaryKey       = 'idizin';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idizin',
        'pegawai_id',
        'tanggalmulaiizin',
        'tanggalselesaiizin',
        'selected_dates',
        'jenisizin',
        'alasan',
        'lampiran',
        'statusizin',
        'keterangan_admin'
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
        'tanggalmulaiizin' => 'required|valid_date',
        'tanggalselesaiizin' => 'required|valid_date',
        'jenisizin' => 'required',
        'alasan' => 'required'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Mengambil data izin berdasarkan ID pegawai
     */
    public function getIzinByPegawai($pegawaiId)
    {
        return $this->where('pegawai_id', $pegawaiId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Generate ID izin unik
     */
    public function generateIdIzin()
    {
        $date = date('Ymd');
        $lastId = $this->selectMax('idizin')->first();

        if ($lastId) {
            $lastIdNumber = (int) substr($lastId['idizin'], -3);
            $newIdNumber = $lastIdNumber + 1;
        } else {
            $newIdNumber = 1;
        }

        return 'IZN' . $date . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Mengambil laporan izin berdasarkan bulan dan tahun
     */
    public function getLaporanIzin($bulan, $tahun)
    {
        $startDate = $tahun . '-' . $bulan . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        return $this->select('izin.*, pegawai.nama as nama_pegawai')
            ->join('pegawai', 'pegawai.idpegawai = izin.pegawai_id')
            ->where('tanggalmulaiizin >=', $startDate)
            ->where('tanggalmulaiizin <=', $endDate)
            ->orderBy('tanggalmulaiizin', 'ASC')
            ->findAll();
    }

    // Mendapatkan data izin dengan informasi pegawai
    public function getIzinWithPegawai($id = null)
    {
        $builder = $this->db->table('izin');
        $builder->select('izin.*, pegawai.namapegawai, pegawai.nik');
        $builder->join('pegawai', 'pegawai.idpegawai = izin.pegawai_id');

        if ($id !== null) {
            $builder->where('izin.idizin', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->orderBy('izin.created_at', 'DESC')->get()->getResultArray();
    }

    // Menghitung jumlah izin yang belum disetujui
    public function countPendingIzin()
    {
        return $this->where('statusizin', false)
            ->where('keterangan_admin', null)
            ->countAllResults();
    }
}
