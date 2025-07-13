<?php

// Load CodeIgniter instance
require_once 'app/Config/Paths.php';
$paths = new \Config\Paths();
require_once $paths->systemDirectory . '/bootstrap.php';

// Buat instance dari Database
$db = \Config\Database::connect();

// Periksa jumlah data di tabel izin
$totalIzin = $db->table('izin')->countAllResults();
echo "Total data di tabel izin: {$totalIzin}\n\n";

// Jika ada data, ambil beberapa untuk ditampilkan
if ($totalIzin > 0) {
    echo "Sampel data izin:\n";

    // Tampilkan semua kolom dari tabel izin dengan informasi pegawai dan jabatan
    $izinData = $db->table('izin')
        ->select('izin.*, pegawai.namapegawai, jabatan.namajabatan')
        ->join('pegawai', 'pegawai.idpegawai = izin.pegawai_id', 'left')
        ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left')
        ->limit(5)
        ->get()
        ->getResultArray();

    // Tampilkan data dalam format yang mudah dibaca
    foreach ($izinData as $i => $data) {
        echo "\nData ke-" . ($i + 1) . ":\n";
        echo "ID: " . ($data['idizin'] ?? 'NULL') . "\n";
        echo "Pegawai ID: " . ($data['pegawai_id'] ?? 'NULL') . "\n";
        echo "Nama Pegawai: " . ($data['namapegawai'] ?? 'NULL') . "\n";
        echo "Jabatan: " . ($data['namajabatan'] ?? 'NULL') . "\n";
        echo "Tanggal Mulai: " . ($data['tanggalmulaiizin'] ?? 'NULL') . "\n";
        echo "Tanggal Selesai: " . ($data['tanggalselesaiizin'] ?? 'NULL') . "\n";
        echo "Jenis Izin: " . ($data['jenisizin'] ?? 'NULL') . "\n";
        echo "Alasan: " . ($data['alasan'] ?? 'NULL') . "\n";
        echo "Status: " . ($data['statusizin'] ?? 'NULL') . "\n";
        echo "-----------------------------------\n";
    }
} else {
    echo "Tidak ada data di tabel izin.\n";

    // Cek data pegawai dan jabatan
    $pegawaiCount = $db->table('pegawai')->countAllResults();
    $jabatanCount = $db->table('jabatan')->countAllResults();

    echo "\nData pendukung:\n";
    echo "- Total pegawai: {$pegawaiCount}\n";
    echo "- Total jabatan: {$jabatanCount}\n";
}
