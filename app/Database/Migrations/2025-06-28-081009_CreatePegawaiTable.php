<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePegawaiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idpegawai' => [
                'type'           => 'CHAR',
                'constraint'     => 25,
            ],
            'userid' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jabatanid' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nik' => [
                'type'       => 'CHAR',
                'constraint' => 16,
                'null'       => true,
            ],
            'namapegawai' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jenkel' => [
                'type'       => 'CHAR',
                'constraint' => 15,
            ],
            'alamat' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nohp' => [
                'type'       => 'CHAR',
                'constraint' => 15,
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
        ]);
        $this->forge->addKey('idpegawai', true);
        $this->forge->addForeignKey('userid', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jabatanid', 'jabatan', 'idjabatan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pegawai');
    }

    public function down()
    {
        $this->forge->dropTable('pegawai');
    }
}
