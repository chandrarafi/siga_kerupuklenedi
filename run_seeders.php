<?php

// Jalankan spark untuk menjalankan seeder
echo "Menjalankan seeder...\n";

// Daftar seeder yang akan dijalankan
$seeders = [
    'UserSeeder',
    'BagianSeeder',
    'JabatanSeeder',
    'PegawaiSeeder',
    'AbsensiSeeder',
    'IzinSeeder',
    'LemburSeeder',
    'GajiSeeder'
];

foreach ($seeders as $seeder) {
    echo "Menjalankan $seeder...\n";
    $output = [];
    $return_var = 0;
    exec("php spark db:seed $seeder 2>&1", $output, $return_var);

    echo implode("\n", $output) . "\n";

    if ($return_var !== 0) {
        echo "Error menjalankan $seeder!\n";
    } else {
        echo "Selesai menjalankan $seeder\n";
    }

    echo "-------------------------------------\n";
}

echo "Semua seeder selesai dijalankan!\n";
