<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOfficeSettingsTable extends Migration
{
    public function up()
    {
        // Cek apakah tabel sudah ada
        if ($this->db->tableExists('office_settings')) {
            // Jika sudah ada, periksa struktur kolom
            $fields = $this->db->getFieldData('office_settings');
            $columnNames = [];

            foreach ($fields as $field) {
                $columnNames[] = $field->name;
            }

            // Tambahkan kolom yang kurang
            if (!in_array('latitude', $columnNames)) {
                $this->forge->addColumn('office_settings', [
                    'latitude' => [
                        'type'       => 'DECIMAL',
                        'constraint' => '10,8',
                        'null'       => true,
                        'after'      => 'name'
                    ]
                ]);
            }

            if (!in_array('longitude', $columnNames)) {
                $this->forge->addColumn('office_settings', [
                    'longitude' => [
                        'type'       => 'DECIMAL',
                        'constraint' => '11,8',
                        'null'       => true,
                        'after'      => 'latitude'
                    ]
                ]);
            }

            return;
        }

        // Jika tabel belum ada, buat tabel baru
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
                'null'       => true,
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'radius' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 100, // default radius 100 meter
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
    }

    public function down()
    {
        $this->forge->dropTable('office_settings');
    }
}
