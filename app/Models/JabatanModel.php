<?php

namespace App\Models;

use CodeIgniter\Model;

class JabatanModel extends Model
{
    protected $table            = 'jabatan';
    protected $primaryKey       = 'idjabatan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['bagianid', 'namajabatan', 'gajipokok', 'tunjangan'];

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
        'bagianid' => 'required|integer',
        'namajabatan' => 'required|min_length[3]|max_length[100]',
        'gajipokok' => 'required|numeric',
        'tunjangan' => 'required|numeric',
    ];
    protected $validationMessages   = [
        'bagianid' => [
            'required' => 'Bagian harus dipilih',
            'integer' => 'Bagian tidak valid',
        ],
        'namajabatan' => [
            'required' => 'Nama jabatan harus diisi',
            'min_length' => 'Nama jabatan minimal 3 karakter',
            'max_length' => 'Nama jabatan maksimal 100 karakter',
        ],
        'gajipokok' => [
            'required' => 'Gaji pokok harus diisi',
            'numeric' => 'Gaji pokok harus berupa angka',
        ],
        'tunjangan' => [
            'required' => 'Tunjangan harus diisi',
            'numeric' => 'Tunjangan harus berupa angka',
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


    public function getJabatanWithBagian($id = null)
    {
        $builder = $this->db->table('jabatan');
        $builder->select('jabatan.*, bagian.namabagian');
        $builder->join('bagian', 'bagian.idbagian = jabatan.bagianid');

        if ($id !== null) {
            $builder->where('jabatan.idjabatan', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }
}
