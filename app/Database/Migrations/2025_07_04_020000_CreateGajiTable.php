<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGajiTable_2025_07_04_020000 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idgaji' => [
                'type'           => 'VARCHAR',
                'constraint'     => 20,
                'null'           => false,
            ],
            'noslip' => [
                'type'           => 'VARCHAR',
                'constraint'     => 20,
                'null'           => false,
            ],
            'pegawai_id' => [
                'type'           => 'CHAR',
                'constraint'     => 25,
                'null'           => false,
            ],
            'periode' => [
                'type'           => 'VARCHAR',
                'constraint'     => 10,
                'null'           => false,
                'comment'        => 'Format: MM-YYYY'
            ],
            'tanggal' => [
                'type'           => 'DATE',
                'null'           => false,
            ],
            'totalabsen' => [
                'type'           => 'FLOAT',
                'null'           => false,
                'default'        => 0,
            ],
            'totallembur' => [
                'type'           => 'FLOAT',
                'null'           => false,
                'default'        => 0,
            ],
            'potongan' => [
                'type'           => 'FLOAT',
                'null'           => false,
                'default'        => 0,
                'comment'        => 'Potongan dari keterlambatan, dll'
            ],
            'gajibersih' => [
                'type'           => 'FLOAT',
                'null'           => false,
                'default'        => 0,
            ],
            'metodepembayaran' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
                'default'        => 'Transfer Bank',
            ],
            'status' => [
                'type'           => 'VARCHAR',
                'constraint'     => 20,
                'null'           => false,
                'default'        => 'pending',
                'comment'        => 'pending, paid, cancelled'
            ],
            'keterangan' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('idgaji', true);
        $this->forge->addKey('noslip');
        $this->forge->addKey(['pegawai_id', 'periode']);
        $this->forge->addForeignKey('pegawai_id', 'pegawai', 'idpegawai', 'CASCADE', 'CASCADE');

        $this->forge->createTable('gaji');
    }

    public function down()
    {
        $this->forge->dropTable('gaji');
    }
}
