<?php

// Load CodeIgniter
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

// Inisialisasi database
$db = \Config\Database::connect();

// Periksa struktur tabel office_settings
echo "Struktur tabel office_settings:\n";
$fields = $db->getFieldData('office_settings');

foreach ($fields as $field) {
    echo $field->name . " - " . $field->type . " (" . $field->max_length . ")\n";
}

// Periksa apakah tabel memiliki data
echo "\nData di tabel office_settings:\n";
$data = $db->table('office_settings')->get()->getResultArray();

if (empty($data)) {
    echo "Tidak ada data\n";
} else {
    foreach ($data as $row) {
        print_r($row);
    }
}
