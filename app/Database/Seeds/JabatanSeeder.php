<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Jabatan di Bagian Produksi (ID 1)
            [
                'bagianid' => 1,
                'namajabatan' => 'Kepala Produksi',
                'gajipokok' => 5000000,
                'tunjangan' => 1500000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'bagianid' => 1,
                'namajabatan' => 'Operator Produksi',
                'gajipokok' => 3000000,
                'tunjangan' => 800000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Jabatan di Bagian Pengemasan (ID 2)
            [
                'bagianid' => 2,
                'namajabatan' => 'Kepala Pengemasan',
                'gajipokok' => 4500000,
                'tunjangan' => 1200000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'bagianid' => 2,
                'namajabatan' => 'Staff Pengemasan',
                'gajipokok' => 2800000,
                'tunjangan' => 700000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Jabatan di Bagian Pemasaran (ID 3)
            [
                'bagianid' => 3,
                'namajabatan' => 'Manajer Pemasaran',
                'gajipokok' => 6000000,
                'tunjangan' => 2000000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'bagianid' => 3,
                'namajabatan' => 'Sales',
                'gajipokok' => 3500000,
                'tunjangan' => 1500000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Jabatan di Bagian Administrasi (ID 4)
            [
                'bagianid' => 4,
                'namajabatan' => 'Manajer Administrasi',
                'gajipokok' => 5500000,
                'tunjangan' => 1800000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'bagianid' => 4,
                'namajabatan' => 'Staff Administrasi',
                'gajipokok' => 3200000,
                'tunjangan' => 900000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Jabatan di Bagian Gudang (ID 5)
            [
                'bagianid' => 5,
                'namajabatan' => 'Kepala Gudang',
                'gajipokok' => 4200000,
                'tunjangan' => 1100000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'bagianid' => 5,
                'namajabatan' => 'Staff Gudang',
                'gajipokok' => 2900000,
                'tunjangan' => 750000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Using Query Builder
        $this->db->table('jabatan')->insertBatch($data);
    }
}
