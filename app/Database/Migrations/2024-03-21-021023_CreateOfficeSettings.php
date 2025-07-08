<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOfficeSettings_2024_03_21_021023 extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
            ],
            'radius' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 20, // default radius 20 meter
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('office_settings');

        // Insert default data
        $this->db->table('office_settings')->insert([
            'name' => 'PT Menara Agung',
            'latitude' => -0.9467468,
            'longitude' => 100.3534272,
            'radius' => 5,
            'address' => 'Jl. Veteran No.30, Padang Pasir, Kec. Padang Bar., Kota Padang, Sumatera Barat 25115',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('office_settings');
    }
}
