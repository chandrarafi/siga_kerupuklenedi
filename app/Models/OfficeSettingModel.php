<?php

namespace App\Models;

use CodeIgniter\Model;

class OfficeSettingModel extends Model
{
    protected $table = 'office_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['name', 'latitude', 'longitude', 'radius', 'address'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getOfficeSetting()
    {
        return $this->first();
    }
}
