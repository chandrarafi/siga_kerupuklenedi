<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJabatanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idjabatan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bagianid' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'namajabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'gajipokok' => [
                'type'       => 'DOUBLE',
            ],
            'tunjangan' => [
                'type'       => 'DOUBLE',
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
        $this->forge->addKey('idjabatan', true);
        $this->forge->addForeignKey('bagianid', 'bagian', 'idbagian', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jabatan');
    }

    public function down()
    {
        $this->forge->dropTable('jabatan');
    }
}
