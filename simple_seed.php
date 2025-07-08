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
    system("php spark db:seed $seeder");
    echo "Selesai menjalankan $seeder\n";
}

echo "Semua seeder selesai dijalankan!\n";
