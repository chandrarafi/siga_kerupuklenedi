<?php

// Load CodeIgniter
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

// Inisialisasi database
$db = \Config\Database::connect();
$forge = \Config\Database::forge();

// 1. Periksa dan perbaiki tabel office_settings
echo "Memeriksa tabel office_settings...\n";

try {
    // Cek apakah tabel ada
    if ($db->tableExists('office_settings')) {
        echo "Tabel office_settings ditemukan.\n";

        // Cek struktur kolom
        $fields = $db->getFieldData('office_settings');
        $columnNames = [];
        foreach ($fields as $field) {
            $columnNames[] = $field->name;
        }

        echo "Kolom yang ada: " . implode(', ', $columnNames) . "\n";

        // Tambahkan kolom yang kurang
        if (!in_array('latitude', $columnNames)) {
            echo "Menambahkan kolom latitude...\n";
            $forge->addColumn('office_settings', [
                'latitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => true,
                ]
            ]);
        }

        if (!in_array('longitude', $columnNames)) {
            echo "Menambahkan kolom longitude...\n";
            $forge->addColumn('office_settings', [
                'longitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => true,
                ]
            ]);
        }

        // Update data jika ada
        $data = $db->table('office_settings')->get()->getRowArray();
        if ($data) {
            echo "Memperbarui data yang ada...\n";
            $db->table('office_settings')->update([
                'latitude' => -0.9467468,
                'longitude' => 100.3534272,
            ], ['id' => $data['id']]);
        } else {
            echo "Menambahkan data baru...\n";
            $db->table('office_settings')->insert([
                'name' => 'PT Menara Agung',
                'latitude' => -0.9467468,
                'longitude' => 100.3534272,
                'radius' => 20,
                'address' => 'Jl. Veteran No.30, Padang Pasir, Kec. Padang Bar., Kota Padang, Sumatera Barat 25115',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    } else {
        echo "Tabel office_settings tidak ditemukan. Membuat tabel baru...\n";

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
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
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

        echo "Tabel office_settings berhasil dibuat.\n";

        // Insert data
        $db->table('office_settings')->insert([
            'name' => 'PT Menara Agung',
            'latitude' => -0.9467468,
            'longitude' => 100.3534272,
            'radius' => 20,
            'address' => 'Jl. Veteran No.30, Padang Pasir, Kec. Padang Bar., Kota Padang, Sumatera Barat 25115',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        echo "Data awal berhasil ditambahkan.\n";
    }

    echo "Tabel office_settings berhasil diperbaiki.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Selesai!\n";
