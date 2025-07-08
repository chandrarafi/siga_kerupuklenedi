<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class IzinSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah ada data di tabel izin
        $existingCount = $this->db->table('izin')->countAllResults();

        if ($existingCount > 0) {
            echo "Data izin sudah ada ({$existingCount} records), tidak perlu menambahkan data baru.\n";
            return;
        }

        // Ambil ID pegawai yang sudah ada
        $pegawaiIds = $this->db->table('pegawai')->select('idpegawai')->get()->getResultArray();

        // Jika tidak ada pegawai, buat catatan log
        if (empty($pegawaiIds)) {
            echo "Tidak ada data pegawai yang tersedia. Silahkan tambahkan data pegawai terlebih dahulu.\n";
            return;
        }

        $data = [
            [
                'idizin' => 'IZN' . date('Ymd') . '001',
                'pegawai_id' => $pegawaiIds[0]['idpegawai'],
                'tanggalmulaiizin' => date('Y-m-d', strtotime('+1 days')),
                'tanggalselesaiizin' => date('Y-m-d', strtotime('+2 days')),
                'selected_dates' => json_encode([
                    date('Y-m-d', strtotime('+1 days')),
                    date('Y-m-d', strtotime('+2 days'))
                ]),
                'jenisizin' => 'Cuti',
                'alasan' => 'Keperluan keluarga',
                'lampiran' => null,
                'statusizin' => 0, // 0 = menunggu
                'keterangan_admin' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idizin' => 'IZN' . date('Ymd') . '002',
                'pegawai_id' => $pegawaiIds[0]['idpegawai'],
                'tanggalmulaiizin' => date('Y-m-d', strtotime('+3 days')),
                'tanggalselesaiizin' => date('Y-m-d', strtotime('+5 days')),
                'selected_dates' => json_encode([
                    date('Y-m-d', strtotime('+3 days')),
                    date('Y-m-d', strtotime('+4 days')),
                    date('Y-m-d', strtotime('+5 days'))
                ]),
                'jenisizin' => 'Sakit',
                'alasan' => 'Sakit demam',
                'lampiran' => null,
                'statusizin' => 1, // 1 = disetujui
                'keterangan_admin' => 'Disetujui oleh admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idizin' => 'IZN' . date('Ymd') . '003',
                'pegawai_id' => isset($pegawaiIds[1]) ? $pegawaiIds[1]['idpegawai'] : $pegawaiIds[0]['idpegawai'],
                'tanggalmulaiizin' => date('Y-m-d'),
                'tanggalselesaiizin' => date('Y-m-d'),
                'selected_dates' => json_encode([date('Y-m-d')]),
                'jenisizin' => 'Izin',
                'alasan' => 'Acara keluarga',
                'lampiran' => null,
                'statusizin' => 2, // 2 = ditolak
                'keterangan_admin' => 'Ditolak karena alasan tidak lengkap',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert data
        $this->db->table('izin')->insertBatch($data);

        echo "Data izin berhasil ditambahkan.\n";
    }
}
