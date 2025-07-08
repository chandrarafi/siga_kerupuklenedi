<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiSettingModel extends Model
{
    protected $table            = 'absensi_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['jam_masuk', 'jam_pulang'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'jam_masuk' => 'required',
        'jam_pulang' => 'required',
    ];
    protected $validationMessages   = [
        'jam_masuk' => [
            'required' => 'Jam masuk harus diisi',
        ],
        'jam_pulang' => [
            'required' => 'Jam pulang harus diisi',
        ],
    ];
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

    // Get current settings
    public function getSettings()
    {
        return $this->first();
    }
}
