<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'idabsensi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idpegawai',
        'tanggal',
        'jammasuk',
        'jamkeluar',
        'status',
        'keterangan',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'terlambat'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Relasi dengan tabel pegawai
    public function pegawai()
    {
        return $this->belongsTo(PegawaiModel::class, 'idpegawai', 'idpegawai');
    }

    public function getAbsensiWithPegawai($id = false)
    {
        $builder = $this->db->table('absensi a');
        $builder->select('a.*, p.namapegawai, p.nik');
        $builder->join('pegawai p', 'p.idpegawai = a.idpegawai');

        if ($id !== false) {
            $builder->where('a.idabsensi', $id);
            return $builder->get()->getRowArray();
        }

        $builder->orderBy('a.tanggal', 'DESC');
        $builder->orderBy('a.jammasuk', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function getAbsensiByPegawai($idpegawai, $tanggal = false)
    {
        $builder = $this->db->table('absensi');
        $builder->where('idpegawai', $idpegawai);

        if ($tanggal !== false) {
            $builder->where('tanggal', $tanggal);
        }

        $builder->orderBy('tanggal', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function getAbsensiByDate($tanggal)
    {
        $builder = $this->db->table('absensi a');
        $builder->select('a.*, p.namapegawai, p.nik');
        $builder->join('pegawai p', 'p.idpegawai = a.idpegawai');
        $builder->where('a.tanggal', $tanggal);
        $builder->orderBy('p.namapegawai', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function getAbsensiByMonth($bulan, $tahun)
    {
        $builder = $this->db->table('absensi a');
        $builder->select('a.*, p.namapegawai, p.nik');
        $builder->join('pegawai p', 'p.idpegawai = a.idpegawai');
        $builder->where('MONTH(a.tanggal)', $bulan);
        $builder->where('YEAR(a.tanggal)', $tahun);
        $builder->orderBy('a.tanggal', 'DESC');
        $builder->orderBy('p.namapegawai', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function getRekapAbsensi($bulan, $tahun)
    {
        $builder = $this->db->table('absensi');
        $builder->select('idpegawai, status, COUNT(*) as jumlah');
        $builder->where('MONTH(tanggal)', $bulan);
        $builder->where('YEAR(tanggal)', $tahun);
        $builder->groupBy('idpegawai, status');
        return $builder->get()->getResultArray();
    }

    // Cek apakah pegawai sudah absen pada tanggal tertentu
    public function isAbsenExists($idpegawai, $tanggal)
    {
        return $this->where('idpegawai', $idpegawai)
            ->where('tanggal', $tanggal)
            ->countAllResults() > 0;
    }
}
