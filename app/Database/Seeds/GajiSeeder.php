<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GajiSeeder extends Seeder
{
    public function run()
    {
        // Ambil data pegawai yang ada
        $pegawai = $this->db->table('pegawai')->select('idpegawai')->get()->getResultArray();

        if (empty($pegawai)) {
            echo "Data pegawai kosong. Silahkan jalankan PegawaiSeeder terlebih dahulu.\n";
            return;
        }

        // Gunakan ID pegawai yang ada di database
        $pegawaiIds = array_column($pegawai, 'idpegawai');

        // Pastikan kita hanya menggunakan pegawai yang tersedia
        $data = [];

        // Data gaji bulan Juni
        if (isset($pegawaiIds[0])) {
            $data[] = [
                'idgaji' => 'GJI-2025-06-001',
                'noslip' => 'SLP-2025-06-001',
                'pegawai_id' => $pegawaiIds[0],
                'periode' => '06-2025',
                'tanggal' => '2025-06-30',
                'totalabsen' => 22,
                'totallembur' => 8,
                'potongan' => 0,
                'gajibersih' => 7500000,
                'metodepembayaran' => 'Transfer Bank',
                'status' => 'paid',
                'keterangan' => 'Gaji bulan Juni 2025',
                'created_at' => '2025-06-28 10:00:00',
                'updated_at' => '2025-06-30 15:30:00'
            ];
        }

        if (isset($pegawaiIds[1])) {
            $data[] = [
                'idgaji' => 'GJI-2025-06-002',
                'noslip' => 'SLP-2025-06-002',
                'pegawai_id' => $pegawaiIds[1],
                'periode' => '06-2025',
                'tanggal' => '2025-06-30',
                'totalabsen' => 20,
                'totallembur' => 5,
                'potongan' => 100000,
                'gajibersih' => 5400000,
                'metodepembayaran' => 'Transfer Bank',
                'status' => 'paid',
                'keterangan' => 'Gaji bulan Juni 2025',
                'created_at' => '2025-06-28 10:15:00',
                'updated_at' => '2025-06-30 15:35:00'
            ];
        }

        if (isset($pegawaiIds[2])) {
            $data[] = [
                'idgaji' => 'GJI-2025-06-003',
                'noslip' => 'SLP-2025-06-003',
                'pegawai_id' => $pegawaiIds[2],
                'periode' => '06-2025',
                'tanggal' => '2025-06-30',
                'totalabsen' => 22,
                'totallembur' => 10,
                'potongan' => 0,
                'gajibersih' => 6200000,
                'metodepembayaran' => 'Transfer Bank',
                'status' => 'paid',
                'keterangan' => 'Gaji bulan Juni 2025',
                'created_at' => '2025-06-28 10:30:00',
                'updated_at' => '2025-06-30 15:40:00'
            ];
        }

        // Data gaji bulan Juli
        if (isset($pegawaiIds[0])) {
            $data[] = [
                'idgaji' => 'GJI-2025-07-001',
                'noslip' => 'SLP-2025-07-001',
                'pegawai_id' => $pegawaiIds[0],
                'periode' => '07-2025',
                'tanggal' => '2025-07-31',
                'totalabsen' => 21,
                'totallembur' => 6,
                'potongan' => 0,
                'gajibersih' => 7300000,
                'metodepembayaran' => 'Transfer Bank',
                'status' => 'pending',
                'keterangan' => 'Gaji bulan Juli 2025',
                'created_at' => '2025-07-30 09:00:00',
                'updated_at' => '2025-07-30 09:00:00'
            ];
        }

        if (isset($pegawaiIds[1])) {
            $data[] = [
                'idgaji' => 'GJI-2025-07-002',
                'noslip' => 'SLP-2025-07-002',
                'pegawai_id' => $pegawaiIds[1],
                'periode' => '07-2025',
                'tanggal' => '2025-07-31',
                'totalabsen' => 20,
                'totallembur' => 4,
                'potongan' => 50000,
                'gajibersih' => 5350000,
                'metodepembayaran' => 'Transfer Bank',
                'status' => 'pending',
                'keterangan' => 'Gaji bulan Juli 2025',
                'created_at' => '2025-07-30 09:15:00',
                'updated_at' => '2025-07-30 09:15:00'
            ];
        }

        if (isset($pegawaiIds[2])) {
            $data[] = [
                'idgaji' => 'GJI-2025-07-003',
                'noslip' => 'SLP-2025-07-003',
                'pegawai_id' => $pegawaiIds[2],
                'periode' => '07-2025',
                'tanggal' => '2025-07-31',
                'totalabsen' => 22,
                'totallembur' => 8,
                'potongan' => 0,
                'gajibersih' => 6100000,
                'metodepembayaran' => 'Transfer Bank',
                'status' => 'pending',
                'keterangan' => 'Gaji bulan Juli 2025',
                'created_at' => '2025-07-30 09:30:00',
                'updated_at' => '2025-07-30 09:30:00'
            ];
        }

        try {
            foreach ($data as $row) {
                $this->db->table('gaji')->insert($row);
            }
            echo "Data gaji berhasil ditambahkan.\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
