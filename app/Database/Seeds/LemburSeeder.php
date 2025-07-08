<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LemburSeeder extends Seeder
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

        // Data lembur untuk pegawai pertama
        if (isset($pegawaiIds[0])) {
            $data[] = [
                'idlembur' => 'LBR-2025-07-001',
                'pegawai_id' => $pegawaiIds[0],
                'tanggallembur' => '2025-07-05',
                'jammulai' => '17:00:00',
                'jamselesai' => '20:00:00',
                'alasan' => 'Menyelesaikan laporan keuangan bulanan',
                'created_at' => '2025-07-04 09:15:00',
                'updated_at' => '2025-07-04 09:15:00'
            ];
        }

        // Data lembur untuk pegawai kedua
        if (isset($pegawaiIds[1])) {
            $data[] = [
                'idlembur' => 'LBR-2025-07-002',
                'pegawai_id' => $pegawaiIds[1],
                'tanggallembur' => '2025-07-06',
                'jammulai' => '17:00:00',
                'jamselesai' => '19:30:00',
                'alasan' => 'Persiapan presentasi untuk klien besok',
                'created_at' => '2025-07-05 14:30:00',
                'updated_at' => '2025-07-05 14:30:00'
            ];
        }

        // Data lembur untuk pegawai ketiga
        if (isset($pegawaiIds[2])) {
            $data[] = [
                'idlembur' => 'LBR-2025-07-003',
                'pegawai_id' => $pegawaiIds[2],
                'tanggallembur' => '2025-07-10',
                'jammulai' => '17:00:00',
                'jamselesai' => '21:00:00',
                'alasan' => 'Menyelesaikan proyek yang deadline-nya besok',
                'created_at' => '2025-07-09 16:45:00',
                'updated_at' => '2025-07-09 16:45:00'
            ];
        }

        try {
            foreach ($data as $row) {
                $this->db->table('lembur')->insert($row);
            }
            echo "Data lembur berhasil ditambahkan.\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
