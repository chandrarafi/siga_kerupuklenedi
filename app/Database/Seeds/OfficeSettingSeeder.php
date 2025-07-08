<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OfficeSettingSeeder extends Seeder
{
    public function run()
    {
        try {
            // Cek apakah sudah ada data
            $existingData = $this->db->table('office_settings')->get()->getRowArray();

            if ($existingData) {
                // Update data yang sudah ada
                $dataUpdate = ['radius' => 20, 'updated_at' => date('Y-m-d H:i:s')];

                // Cek apakah kolom latitude dan longitude ada
                $fields = $this->db->getFieldData('office_settings');
                $columnNames = array_column($fields, 'name');

                if (in_array('latitude', $columnNames) && in_array('longitude', $columnNames)) {
                    $dataUpdate['latitude'] = -0.9467468;
                    $dataUpdate['longitude'] = 100.3534272;
                }

                $this->db->table('office_settings')->update($dataUpdate, ['id' => $existingData['id']]);
                echo "Data pengaturan lokasi kantor berhasil diperbarui dengan radius 20 meter.\n";
            } else {
                // Cek struktur tabel
                $fields = $this->db->getFieldData('office_settings');
                $columnNames = array_column($fields, 'name');

                $dataInsert = [
                    'name' => 'PT Menara Agung',
                    'radius' => 20,
                    'address' => 'Jl. Veteran No.30, Padang Pasir, Kec. Padang Bar., Kota Padang, Sumatera Barat 25115',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                // Tambahkan latitude dan longitude jika kolom ada
                if (in_array('latitude', $columnNames) && in_array('longitude', $columnNames)) {
                    $dataInsert['latitude'] = -0.9467468;
                    $dataInsert['longitude'] = 100.3534272;
                }

                $this->db->table('office_settings')->insert($dataInsert);
                echo "Data pengaturan lokasi kantor berhasil ditambahkan dengan radius 20 meter.\n";
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";

            // Coba buat tabel jika tidak ada
            if (!$this->db->tableExists('office_settings')) {
                echo "Tabel office_settings tidak ditemukan. Membuat tabel...\n";

                $forge = \Config\Database::forge();
                $forge->addField([
                    'id' => [
                        'type'           => 'INT',
                        'constraint'     => 11,
                        'unsigned'       => true,
                        'auto_increment' => true,
                    ],
                    'name' => [
                        'type'       => 'VARCHAR',
                        'constraint' => '255',
                    ],
                    'latitude' => [
                        'type'       => 'DECIMAL',
                        'constraint' => '10,8',
                        'null'       => true,
                    ],
                    'longitude' => [
                        'type'       => 'DECIMAL',
                        'constraint' => '11,8',
                        'null'       => true,
                    ],
                    'radius' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'default'    => 20,
                    ],
                    'address' => [
                        'type' => 'TEXT',
                        'null' => true,
                    ],
                    'created_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                    'updated_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                ]);

                $forge->addKey('id', true);
                $forge->createTable('office_settings');

                // Insert data
                $this->db->table('office_settings')->insert([
                    'name' => 'PT Menara Agung',
                    'latitude' => -0.9467468,
                    'longitude' => 100.3534272,
                    'radius' => 20,
                    'address' => 'Jl. Veteran No.30, Padang Pasir, Kec. Padang Bar., Kota Padang, Sumatera Barat 25115',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                echo "Tabel office_settings berhasil dibuat dan data awal ditambahkan.\n";
            }
        }
    }
}
