<?php

namespace App\Models;

use CodeIgniter\Model;

class PegawaiModel extends Model
{
    protected $table            = 'pegawai';
    protected $primaryKey       = 'idpegawai';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idpegawai', 'userid', 'jabatanid', 'nik', 'namapegawai', 'jenkel', 'alamat', 'nohp'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';


    protected $validationRules      = [
        'idpegawai' => 'required|max_length[25]',
        'userid' => 'permit_empty|integer',
        'jabatanid' => 'required|integer',
        'nik' => 'permit_empty|max_length[16]',
        'namapegawai' => 'required|max_length[255]',
        'jenkel' => 'required|max_length[15]',
        'alamat' => 'permit_empty|max_length[255]',
        'nohp' => 'permit_empty|max_length[15]',
    ];
    protected $validationMessages   = [
        'idpegawai' => [
            'required' => 'ID Pegawai harus diisi',
            'max_length' => 'ID Pegawai maksimal 25 karakter',
        ],
        'userid' => [
            'integer' => 'User tidak valid',
        ],
        'jabatanid' => [
            'required' => 'Jabatan harus dipilih',
            'integer' => 'Jabatan tidak valid',
        ],
        'nik' => [
            'max_length' => 'NIK maksimal 16 karakter',
        ],
        'namapegawai' => [
            'required' => 'Nama pegawai harus diisi',
            'max_length' => 'Nama pegawai maksimal 255 karakter',
        ],
        'jenkel' => [
            'required' => 'Jenis kelamin harus dipilih',
            'max_length' => 'Jenis kelamin maksimal 15 karakter',
        ],
        'alamat' => [
            'max_length' => 'Alamat maksimal 255 karakter',
        ],
        'nohp' => [
            'max_length' => 'No HP maksimal 15 karakter',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;


    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public function getPegawaiWithRelations($id = null)
    {
        $builder = $this->db->table('pegawai');
        $builder->select('pegawai.*, jabatan.namajabatan, bagian.namabagian, users.username, users.email');
        $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid');
        $builder->join('bagian', 'bagian.idbagian = jabatan.bagianid');
        $builder->join('users', 'users.id = pegawai.userid');

        if ($id !== null) {
            $builder->where('pegawai.idpegawai', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }


    public function generateIdPegawai()
    {
        $prefix = 'PGW';
        $date = date('Ymd');

        try {

            $lastPegawai = $this->orderBy('idpegawai', 'DESC')->first();

            if ($lastPegawai && !empty($lastPegawai['idpegawai'])) {
                $lastId = $lastPegawai['idpegawai'];

                $lastNumber = (int) substr($lastId, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }


            $idPegawai = $prefix . $date . sprintf('%04d', $newNumber);


            $exists = $this->find($idPegawai);
            if ($exists) {

                $randomNum = mt_rand(1, 999);
                $idPegawai = $prefix . $date . sprintf('%04d', $newNumber + $randomNum);
            }

            return $idPegawai;
        } catch (\Exception $e) {

            $timestamp = time();
            $randomNum = mt_rand(1000, 9999);
            return $prefix . $date . $randomNum;
        }
    }


    public function getPegawaiByUserId($userId)
    {
        return $this->where('userid', $userId)->first();
    }
}
