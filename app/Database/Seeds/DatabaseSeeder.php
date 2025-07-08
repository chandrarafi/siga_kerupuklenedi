<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Truncate the table first to ensure clean data
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $this->db->table('users')->truncate();
        $this->db->table('bagian')->truncate();
        $this->db->table('jabatan')->truncate();
        $this->db->table('pegawai')->truncate();
        $this->db->table('absensi')->truncate();
        $this->db->table('izin')->truncate();
        $this->db->table('lembur')->truncate();
        $this->db->table('gaji')->truncate();
        // $this->db->table(tableName: 'office_settings')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');

        $this->call('UserSeeder');
        $this->call('BagianSeeder');
        $this->call('JabatanSeeder');
        $this->call('PegawaiSeeder');
        $this->call('AbsensiSeeder');
        $this->call('IzinSeeder');
        $this->call('LemburSeeder');
        $this->call('GajiSeeder');
        // $this->call(class: 'OfficeSettingSeeder');
    }
}
