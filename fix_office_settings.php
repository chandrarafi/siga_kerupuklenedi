<?php

// Load CodeIgniter
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

// Inisialisasi database
$db = \Config\Database::connect();
$forge = \Config\Database::forge();

// Periksa apakah tabel office_settings ada
if ($db->tableExists('office_settings')) {
    echo "Tabel office_settings ditemukan.\n";

    // Periksa kolom yang ada
    $fields = $db->getFieldData('office_settings');
    $columnNames = [];

    foreach ($fields as $field) {
        $columnNames[] = $field->name;
    }

    echo "Kolom yang ada: " . implode(', ', $columnNames) . "\n";

    // Periksa apakah kolom latitude ada
    if (!in_array('latitude', $columnNames)) {
        echo "Menambahkan kolom latitude...\n";
        $forge->addColumn('office_settings', [
            'latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => true,
            ]
        ]);
        echo "Kolom latitude berhasil ditambahkan.\n";
    }

    // Periksa apakah kolom longitude ada
    if (!in_array('longitude', $columnNames)) {
        echo "Menambahkan kolom longitude...\n";
        $forge->addColumn('office_settings', [
            'longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => true,
            ]
        ]);
        echo "Kolom longitude berhasil ditambahkan.\n";
    }

    // Update data jika ada
    $data = $db->table('office_settings')->get()->getRowArray();
    if ($data) {
        echo "Memperbarui data yang ada...\n";
        $db->table('office_settings')->update([
            'latitude' => -0.9467468,
            'longitude' => 100.3534272,
        ], ['id' => $data['id']]);
        echo "Data berhasil diperbarui.\n";
    }
} else {
    echo "Tabel office_settings tidak ditemukan.\n";
    echo "Membuat tabel office_settings...\n";

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

echo "Selesai!\n";
