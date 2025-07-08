<?php

// Pastikan seeder yang akan dijalankan dilewatkan sebagai argumen
if ($argc < 2) {
    echo "Penggunaan: php run_single_seeder.php NamaSeeder\n";
    exit(1);
}

$seeder = $argv[1];

// Load CodeIgniter
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

// Inisialisasi database
$db = \Config\Database::connect();

// Jalankan seeder
echo "Menjalankan $seeder...\n";

try {
    $seederClass = "\\App\\Database\\Seeds\\$seeder";

    if (!class_exists($seederClass)) {
        echo "Error: Seeder $seeder tidak ditemukan!\n";
        exit(1);
    }

    $seederInstance = new $seederClass();
    $seederInstance->run();

    echo "Seeder $seeder berhasil dijalankan.\n";
} catch (\Exception $e) {
    echo "Error menjalankan $seeder: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " baris " . $e->getLine() . "\n";
    exit(1);
}

echo "Selesai!\n";
