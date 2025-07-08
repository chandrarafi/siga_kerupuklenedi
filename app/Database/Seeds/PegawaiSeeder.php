<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    public function run()
    {
        // Ambil data jabatan
        $jabatanModel = new \App\Models\JabatanModel();
        $jabatan = $jabatanModel->findAll();

        if (empty($jabatan)) {
            echo "Data jabatan kosong. Silahkan jalankan JabatanSeeder terlebih dahulu.\n";
            return;
        }

        // Buat beberapa user pegawai
        $userData = [
            [
                'username' => 'budi.santoso',
                'email' => 'budi.santoso@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'name' => 'Budi Santoso',
                'role' => 'pegawai',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'siti.rahayu',
                'email' => 'siti.rahayu@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'name' => 'Siti Rahayu',
                'role' => 'pegawai',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'ahmad.fauzi',
                'email' => 'ahmad.fauzi@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'name' => 'Ahmad Fauzi',
                'role' => 'pegawai',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert user data dan dapatkan ID
        $userIds = [];
        foreach ($userData as $user) {
            $this->db->table('users')->insert($user);
            $userIds[] = $this->db->insertID();
        }

        // Buat data pegawai
        $pegawaiData = [
            [
                'idpegawai' => 'PGW20250628001',
                'userid' => $userIds[0],
                'jabatanid' => $jabatan[0]['idjabatan'],
                'nik' => '3201012345678901',
                'namapegawai' => 'Budi Santoso',
                'jenkel' => 'Laki-laki',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'nohp' => '081234567890',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'idpegawai' => 'PGW20250628002',
                'userid' => $userIds[1],
                'jabatanid' => $jabatan[1]['idjabatan'],
                'nik' => '3201012345678902',
                'namapegawai' => 'Siti Rahayu',
                'jenkel' => 'Perempuan',
                'alamat' => 'Jl. Pahlawan No. 45, Jakarta',
                'nohp' => '081234567891',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'idpegawai' => 'PGW20250628003',
                'userid' => $userIds[2],
                'jabatanid' => $jabatan[2]['idjabatan'],
                'nik' => '3201012345678903',
                'namapegawai' => 'Ahmad Fauzi',
                'jenkel' => 'Laki-laki',
                'alamat' => 'Jl. Sudirman No. 78, Jakarta',
                'nohp' => '081234567892',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert pegawai data
        foreach ($pegawaiData as $pegawai) {
            $this->db->table('pegawai')->insert($pegawai);
        }

        echo "Data pegawai berhasil ditambahkan.\n";
    }
}
