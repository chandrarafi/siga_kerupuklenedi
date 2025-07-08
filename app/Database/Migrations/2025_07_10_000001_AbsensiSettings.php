<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AbsensiSettings extends Migration
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
            'jam_masuk' => [
                'type' => 'TIME',
                'null' => false,
                'default' => '08:00:00',
            ],
            'jam_pulang' => [
                'type' => 'TIME',
                'null' => false,
                'default' => '17:00:00',
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
        $this->forge->createTable('absensi_settings');

        // Insert default settings
        $this->db->table('absensi_settings')->insert([
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('absensi_settings');
    }
}
