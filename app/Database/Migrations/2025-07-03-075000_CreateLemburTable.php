<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLemburTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'idlembur' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
            ],
            'pegawai_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'tanggallembur' => [
                'type' => 'DATE',
            ],
            'jammulai' => [
                'type' => 'TIME',
            ],
            'jamselesai' => [
                'type' => 'TIME',
            ],
            'alasan' => [
                'type'       => 'TEXT',
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

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pegawai_id', 'pegawai', 'idpegawai', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lembur');
    }

    public function down()
    {
        $this->forge->dropTable('lembur');
    }
}
