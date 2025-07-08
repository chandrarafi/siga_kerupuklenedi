<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AbsensiSeeder extends Seeder
{
    public function run()
    {
        try {
            $pegawaiModel = new \App\Models\PegawaiModel();
            $pegawai = $pegawaiModel->findAll();

            if (empty($pegawai)) {
                echo "Tidak ada data pegawai. Silahkan jalankan PegawaiSeeder terlebih dahulu.\n";
                return;
            }

            // Tanggal untuk absensi (7 hari terakhir)
            $tanggal = [];
            for ($i = 6; $i >= 0; $i--) {
                $tanggal[] = date('Y-m-d', strtotime("-$i days"));
            }

            // Status absensi
            $status = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'sakit', 'izin'];

            // Jam masuk dan keluar
            $jamMasuk = ['07:30:00', '07:45:00', '08:00:00', '08:15:00', '08:30:00'];
            $jamKeluar = ['16:00:00', '16:15:00', '16:30:00', '16:45:00', '17:00:00'];

            // Keterangan
            $keterangan = [
                'hadir' => ['Tepat waktu', 'Masuk tepat waktu', 'Absen normal', ''],
                'sakit' => ['Sakit flu', 'Demam', 'Sakit kepala', 'Izin sakit'],
                'izin' => ['Urusan keluarga', 'Acara keluarga', 'Izin keperluan pribadi', 'Ada keperluan penting']
            ];

            // Lokasi (contoh koordinat)
            $longitude = ['112.6326', '112.6327', '112.6328', '112.6329', '112.6330'];
            $latitude = ['-7.9666', '-7.9667', '-7.9668', '-7.9669', '-7.9670'];

            $data = [];

            // Generate data absensi untuk setiap pegawai
            foreach ($pegawai as $p) {
                foreach ($tanggal as $t) {
                    // Skip untuk weekend (Sabtu dan Minggu)
                    $dayOfWeek = date('N', strtotime($t));
                    if ($dayOfWeek >= 6) { // 6 = Saturday, 7 = Sunday
                        continue;
                    }

                    // Acak status (90% hadir, 5% sakit, 5% izin)
                    $randStatus = mt_rand(1, 100);
                    if ($randStatus <= 90) {
                        $currentStatus = 'hadir';
                    } elseif ($randStatus <= 95) {
                        $currentStatus = 'sakit';
                    } else {
                        $currentStatus = 'izin';
                    }

                    // Set jam masuk dan keluar berdasarkan status
                    $currentJamMasuk = null;
                    $currentJamKeluar = null;
                    if ($currentStatus == 'hadir') {
                        $currentJamMasuk = $jamMasuk[array_rand($jamMasuk)];
                        $currentJamKeluar = $jamKeluar[array_rand($jamKeluar)];
                    }

                    // Set keterangan berdasarkan status
                    $currentKeterangan = '';
                    if (!empty($keterangan[$currentStatus])) {
                        $currentKeterangan = $keterangan[$currentStatus][array_rand($keterangan[$currentStatus])];
                    }

                    // Set lokasi jika hadir
                    $currentLongitudeMasuk = null;
                    $currentLatitudeMasuk = null;
                    $currentLongitudeKeluar = null;
                    $currentLatitudeKeluar = null;
                    if ($currentStatus == 'hadir') {
                        $currentLongitudeMasuk = $longitude[array_rand($longitude)];
                        $currentLatitudeMasuk = $latitude[array_rand($latitude)];
                        $currentLongitudeKeluar = $longitude[array_rand($longitude)];
                        $currentLatitudeKeluar = $latitude[array_rand($latitude)];
                    }

                    // Hitung keterlambatan (dalam menit)
                    $terlambat = 0;
                    if ($currentStatus == 'hadir' && $currentJamMasuk > '08:00:00') {
                        $masukTime = strtotime($currentJamMasuk);
                        $batasTime = strtotime('08:00:00');
                        $terlambat = floor(($masukTime - $batasTime) / 60);
                    }

                    $absensiData = [
                        'idpegawai' => $p['idpegawai'],
                        'tanggal' => $t,
                        'jammasuk' => $currentJamMasuk,
                        'jamkeluar' => $currentJamKeluar,
                        'status' => $currentStatus,
                        'keterangan' => $currentKeterangan,
                        'latitude_masuk' => $currentLatitudeMasuk,
                        'longitude_masuk' => $currentLongitudeMasuk,
                        'latitude_keluar' => $currentLatitudeKeluar,
                        'longitude_keluar' => $currentLongitudeKeluar,
                        'terlambat' => $terlambat,
                        'created_at' => Time::now(),
                        'updated_at' => Time::now()
                    ];

                    $data[] = $absensiData;
                }
            }

            // Insert batch data
            if (!empty($data)) {
                $this->db->table('absensi')->insertBatch($data);
                echo count($data) . " data absensi berhasil ditambahkan.\n";
            } else {
                echo "Tidak ada data absensi yang ditambahkan.\n";
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
