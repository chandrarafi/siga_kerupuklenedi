<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Absensi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idabsensi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'idpegawai' => [
                'type'       => 'CHAR',
                'constraint' => 25,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'jammasuk' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'jamkeluar' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['hadir', 'sakit', 'izin', 'alpa'],
                'default'    => 'hadir',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'terlambat' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
                'comment' => 'Keterlambatan dalam menit'
            ],
            'latitude_masuk' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'longitude_masuk' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'latitude_keluar' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'longitude_keluar' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('idabsensi', true);
        $this->forge->addForeignKey('idpegawai', 'pegawai', 'idpegawai', 'CASCADE', 'CASCADE');
        $this->forge->createTable('absensi');
    }

    public function down()
    {
        $this->forge->dropTable('absensi');
    }
}
