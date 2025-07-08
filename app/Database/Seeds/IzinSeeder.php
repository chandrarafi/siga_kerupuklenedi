<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class IzinSeeder extends Seeder
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

        // Data izin untuk pegawai pertama
        if (isset($pegawaiIds[0])) {
            $data[] = [
                'idizin' => 'IZN-2025-07-001',
                'idpegawai' => $pegawaiIds[0],
                'tanggal_mulai' => '2025-07-05',
                'tanggal_selesai' => '2025-07-07',
                'jenis_izin' => 'sakit',
                'keterangan' => 'Sakit demam dan flu',
                'status' => 'disetujui',
                'bukti' => 'surat_dokter_001.jpg',
                'approved_by' => 1,
                'approved_at' => '2025-07-04 10:30:00',
                'created_at' => '2025-07-03 08:15:00',
                'updated_at' => '2025-07-04 10:30:00'
            ];

            $data[] = [
                'idizin' => 'IZN-2025-07-006',
                'idpegawai' => $pegawaiIds[0],
                'tanggal_mulai' => '2025-07-28',
                'tanggal_selesai' => '2025-07-29',
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti untuk acara pernikahan saudara',
                'status' => 'pending',
                'bukti' => 'undangan_nikah_006.jpg',
                'approved_by' => null,
                'approved_at' => null,
                'created_at' => '2025-07-24 08:30:00',
                'updated_at' => '2025-07-24 08:30:00'
            ];
        }

        // Data izin untuk pegawai kedua
        if (isset($pegawaiIds[1])) {
            $data[] = [
                'idizin' => 'IZN-2025-07-002',
                'idpegawai' => $pegawaiIds[1],
                'tanggal_mulai' => '2025-07-10',
                'tanggal_selesai' => '2025-07-10',
                'jenis_izin' => 'keperluan_pribadi',
                'keterangan' => 'Mengurus dokumen penting di kantor pemerintah',
                'status' => 'disetujui',
                'bukti' => 'surat_keterangan_002.jpg',
                'approved_by' => 1,
                'approved_at' => '2025-07-08 14:20:00',
                'created_at' => '2025-07-07 09:45:00',
                'updated_at' => '2025-07-08 14:20:00'
            ];

            $data[] = [
                'idizin' => 'IZN-2025-07-007',
                'idpegawai' => $pegawaiIds[1],
                'tanggal_mulai' => '2025-07-18',
                'tanggal_selesai' => '2025-07-18',
                'jenis_izin' => 'keperluan_pribadi',
                'keterangan' => 'Mengurus SIM yang habis masa berlaku',
                'status' => 'ditolak',
                'bukti' => 'sim_lama_007.jpg',
                'approved_by' => 1,
                'approved_at' => '2025-07-15 14:10:00',
                'created_at' => '2025-07-15 09:20:00',
                'updated_at' => '2025-07-15 14:10:00'
            ];
        }

        // Data izin untuk pegawai ketiga
        if (isset($pegawaiIds[2])) {
            $data[] = [
                'idizin' => 'IZN-2025-07-003',
                'idpegawai' => $pegawaiIds[2],
                'tanggal_mulai' => '2025-07-15',
                'tanggal_selesai' => '2025-07-17',
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti tahunan untuk acara keluarga',
                'status' => 'disetujui',
                'bukti' => 'permohonan_cuti_003.pdf',
                'approved_by' => 1,
                'approved_at' => '2025-07-12 11:05:00',
                'created_at' => '2025-07-10 13:30:00',
                'updated_at' => '2025-07-12 11:05:00'
            ];
        }

        try {
            foreach ($data as $row) {
                $this->db->table('izin')->insert($row);
            }
            echo "Data izin berhasil ditambahkan.\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
